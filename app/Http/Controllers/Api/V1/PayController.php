<?php
/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/9/28
 * Time: 10:05
 */

namespace App\Http\Controllers\Api\V1;

use App\Models\Order;
use App\Models\Shop;
use App\Models\SystemTradeInfo;
use App\Services\PayService;
use App\Services\RedisService;
use Carbon\Carbon;
use DB;
use Gate;
use Illuminate\Http\Request;
use Pingpp\Charge;
use Pingpp\Pingpp;
use WeiHeng\Yeepay\YeepayClient;

class PayController extends Controller
{
    protected $pingxxConfig;

    public function __construct()
    {
        $this->pingxxConfig = config('pingxx');
        Pingpp::setApiKey($this->pingxxConfig['api_key']);
    }

    /**
     * 支付
     *
     * @param \Illuminate\Http\Request $request
     * @param $orderId
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function charge(Request $request, $orderId)
    {
        $type = $request->input('type');
        $field = $type == 'all' ? 'pid' : 'id';
        $channels = $this->pingxxConfig['channels'];
        $channel = $request->input('channel');
        $channel = in_array($channel, $channels) ? $channel : head($channel);
        $orders = auth()->user()->orders()->useful()->noInvalid()->where($field, $orderId)->with('coupon')->get();
        if ($orders->isEmpty()) {
            return $this->error('获取失败，请重试');
        }

        if (Gate::denies('validate-payment-orders', $orders)) {
            return $this->error('获取失败，请重试');
        }
        $orderIds = $orders->pluck('shop_id')->all();

        $orderNames = Shop::whereIn('id', $orderIds)->get()->implode('name', ',');

        //配置extra
        $charge = Charge::create(
            array(
                'subject' => '成都订百达科技有限公司',
                'body' => $orderNames,
                'amount' => ($orders->sum('after_rebates_price')) * 100,   //单位为分
                'order_no' => $orderId,
                'currency' => 'cny',
                'extra' => $this->_getExtra($channel, $request),
                'channel' => $channel,
                'client_ip' => $request->server('REMOTE_ADDR'),
                'description' => $type,
                'app' => array('id' => $this->pingxxConfig['app_id'])
            )
        )->__toArray(true);
        return $this->success($charge);
    }

    /**
     * 余额支付
     *
     * @param \Illuminate\Http\Request $request
     * @param $orderId
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function balancepay(Request $request, $orderId)
    {
        $type = $request->input('type');
        $field = $type == 'all' ? 'pid' : 'id';
        $payService = new PayService();
        $result = $payService->balancepay($field, $orderId);
        return $result ? $this->success('支付成功') : $this->error($payService->getError());
    }

    /**
     * 退款
     *
     * @param \Illuminate\Http\Request $request
     * @param $orderId
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function refund(Request $request, $orderId)
    {

        $reason = $request->input('reason', 'no reason');

        $refundBySeller = $request->input('is_seller');

        $order = $refundBySeller ? Order::where('shop_id',
            auth()->user()->shop_id)->find($orderId) : Order::where('user_id', auth()->id())->find($orderId);


        if (!$order || !$order->can_refund) {
            return $this->error('订单不存在或不能退款');
        }

        $tradeInfo = SystemTradeInfo::where('order_id', $orderId)->first();

        if (!$tradeInfo) {
            return $this->error('订单不存在或不能退款');
        }

        $result = DB::transaction(function () use ($order, $tradeInfo, $reason, $refundBySeller) {

            if (!$order->fill(['pay_status' => cons('order.pay_status.refund')])->save()) {
                return false;
            }
            $payTypes = cons('trade.pay_type');

            if ($tradeInfo->pay_type == $payTypes['yeepay']) {
                if ($this->_refundByYeepay($tradeInfo, $reason)) {
                    $order->orderReason()->create(['reason' => $reason]);
                    // 更新订单状态
                    $order->fill([
                        'pay_status' => cons('order.pay_status.refund_success'),
                        'refund_at' => Carbon::now()
                    ])->save();
                    //通知 卖家/买 已退款
                    $this->_setRefundNotice($order, $refundBySeller);
                } else {
                    return false;
                }
            } elseif (in_array($tradeInfo->pay_type, [$payTypes['alipay'], $payTypes['alipay_pc']])) {
                $result = $this->_refundByAlipay($tradeInfo, $reason);
                if ($result) {
                    $order->orderReason()->create(['reason' => $reason]);
                    //通知 卖家/买 已退款
                    $this->_setRefundNotice($order, $refundBySeller);
                } else {
                    return false;
                }
            } elseif ($tradeInfo->pay_type == $payTypes['yeepay_wap']) {
                $result = $this->_refundByPingxx($tradeInfo, $reason);

                if ($result) {
                    $order->orderReason()->create(['reason' => $reason]);
                    //通知 卖家/买 已退款
                    $this->_setRefundNotice($order, $refundBySeller);
                } else {
                    return false;
                }
            } elseif ($tradeInfo->pay_type == $payTypes['balancepay']) {
                $result = $this->_refundByBalance($tradeInfo);

                if ($result) {
                    $order->orderReason()->create(['reason' => $reason]);
                    //通知 卖家/买 已退款
                    $this->_setRefundNotice($order, $refundBySeller);
                } else {
                    return false;
                }
            } elseif ($tradeInfo->pay_type == $payTypes['wechat_pay']) {
                $result = $this->_refundByWechatPay($tradeInfo, $reason);

                if ($result) {
                    $order->orderReason()->create(['reason' => $reason]);
                    //通知 卖家/买 已退款
                    $this->_setRefundNotice($order, $refundBySeller);
                } else {
                    return false;
                }

            } else {
                return false;
            }
            return true;
        });
        if ($result === false) {
            $order->fill(['pay_status' => cons('order.pay_status.payment_success')])->save();
            return $this->error('退款时遇到错误，请重试');
        }
        $successMessage = $tradeInfo->pay_type == cons('trade.pay_type.alipay') ? '退款审请成功' : '退款成功';
        return $this->success($successMessage);
    }

    /**
     * 移动端成功页面跳转
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function successUrl()
    {
        return redirect('dbdfmcg://pingppwappay?result=success');
    }

    /**
     * 移动端取消页面跳转
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function cancelUrl()
    {
        return redirect('dbdfmcg://pingppwappay?result=cancel');
    }

    /**
     * Pingxx退款
     *
     * @param $tradeInfo
     * @param $reason
     * @return bool
     */
    private function _refundByPingxx($tradeInfo, $reason)
    {
        $ch = Charge::retrieve($tradeInfo->charge_id);
        $result = $ch->refunds->create(
            array(
                'amount' => $tradeInfo->amount * 100,
                'description' => $reason,
                'metadata' => ['order_no' => $tradeInfo->order_id]
            )
        );
        //info($result);
        return is_null($result->failure_code);
    }


    /**
     * 余额退款
     *
     * @param $tradeInfo
     * @return mixed
     */
    private function _refundByBalance($tradeInfo)
    {
        $order = $tradeInfo->order;
        if (!$order) {
            return false;
        }
        $user = $order->user;
        if (!$user) {
            return false;
        }
        if ($order->fill([
            'pay_status' => cons('order.pay_status.refund_success'),
            'refund_at' => Carbon::now()
        ])->save()
        ) {
            return $user->increment('balance', $tradeInfo->amount);
        }
        return false;
    }

    /**
     * 退款
     *
     * @param $tradeInfo
     * @param $reason
     * @return \WeiHeng\Alipay\提交表单HTML文本
     */
    private function _refundByAlipay($tradeInfo, $reason)
    {
        return true;
    }

    /**
     * @param $tradeInfo
     * @return bool
     */
    private function _refundByYeepay($tradeInfo, $reason)
    {
        $yeepayConf = config('yeepay');

        $params = array(
            'p0_Cmd' => "RefundOrd",
            'p1_MerId' => $yeepayConf['p1_mer_id'],
            'pb_TrxId' => $tradeInfo->trade_no,
            'p3_Amt' => $tradeInfo->amount,
            'p4_Cur' => "CNY",
            'p5_Desc' => $reason,
        );
        $hmac = $this->_getYeepayHamc($params);
        $params['hmac'] = $hmac;

        $yeepayClient = new YeepayClient($yeepayConf['ref_url_online']);
        $pageContents = $yeepayClient->quickPost($yeepayConf['ref_url_online'], $params);

        $result = explode("\n", $pageContents);
        return $pageContents && $this->_validateYeepayRefundResult($result);

    }

    /**
     * @param $tradeInfo
     * @param $reason
     * @return bool
     */
    private function _refundByWechatPay($tradeInfo, $reason)
    {
        $wechatPay = app('wechat.pay');

        $result = $wechatPay->refund($tradeInfo, $reason);

        if (!$wechatPay->verifySign($result)) {
            return false;
        }

        return $result['dealCode'] === 10000;
    }

    /**
     *  获取hamc
     *
     * @param $params
     * @return string
     */
    private function _getYeepayHamc($params)
    {
        //	加入订单查询请求，固定值"QueryOrdDetail"
        $sbOld = $params['p0_Cmd'];
        //	加入商户编号
        $sbOld = $sbOld . $params['p1_MerId'];
        //	加入易宝支付交易流水号
        $sbOld = $sbOld . $params['pb_TrxId'];
        //	加入退款金额
        $sbOld = $sbOld . $params['p3_Amt'];
        //	加入交易币种
        $sbOld = $sbOld . $params['p4_Cur'];
        //	加入退款说明
        $sbOld = $sbOld . $params['p5_Desc'];

        $hmac = HmacMd5($sbOld, config('yeepay.merchant_key'));

        return $hmac;
    }

    /**
     * 验证易宝
     *
     * @param $result
     * @return bool
     */
    private function _validateYeepayRefundResult($result)
    {
        $params = [];
        $hmac = '';
        foreach ($result as $val) {//数组循环
            $val = trim($val);
            if (strlen($val) == 0) {
                continue;
            }
            $aryReturn = explode("=", $val);
            $sKey = $aryReturn[0];
            //$sValue = urldecode($aryReturn[1]);
            $sValue = $aryReturn[1];

            if ($sKey == "r0_Cmd") {                                            //业务类型
                $params['r0_Cmd'] = $sValue;
            } elseif ($sKey == "r1_Code") {                                //退款申请结果
                if ($sValue != 1) {
                    return false;
                }
                $params['r1_Code'] = $sValue;
            } elseif ($sKey == "r2_TrxId") {                    //易宝支付交易流水号
                $params['r2_TrxId'] = $sValue;
            } elseif ($sKey == "r3_Amt") {                      //退款金额
                $params['r3_Amt'] = $sValue;
            } elseif ($sKey == "r4_Cur") {                      //交易币种
                $params['r4_Cur'] = $sValue;
            } elseif ($sKey == "hmac") {                                    //取得签名数据
                $hmac = $sValue;
            }
        }

        return $this->_validateRefundHamc($params, $hmac);
    }

    /**
     * 验证退款hamc
     *
     * @param $params
     * @param $hmac
     * @return bool
     */
    private function _validateRefundHamc($params, $hmac)
    {
        //进行校验码检查 取得加密前的字符串
        $sbOld = "";
        //加入业务类型
        $sbOld = $sbOld . $params['r0_Cmd'];
        //加入退款申请是否成功
        $sbOld = $sbOld . $params['r1_Code'];
        //加入易宝支付交易流水号
        $sbOld = $sbOld . $params['r2_TrxId'];
        //加入退款金额
        $sbOld = $sbOld . $params['r3_Amt'];
        //加入交易币种
        $sbOld = $sbOld . $params['r4_Cur'];

        $sNewString = HmacMd5($sbOld, config('yeepay.merchant_key'));

        return $sNewString == $hmac;
    }

    /**
     * 设置订单退款通知
     *
     * @param $order
     * @param bool $refundBySeller
     * @return \WeiHeng\Responses\Apiv1Response
     */
    private function _setRefundNotice($order, $refundBySeller = false)
    {

        $redisKey = '';
        if ($refundBySeller) {
            $redisKey = 'push:user:' . $order->user->id;
        } else {
            $redisKey = 'push:seller:' . $order->shop->user_id;
        }

        $redisVal = '您的订单号' . $order->id . ',' . cons()->lang('push_msg.refund');
        (new RedisService)->setRedis($redisKey, $redisVal, cons('push_time.msg_life'));
    }

    /**
     * 获取extra
     *
     * @param $channel
     * @param $request
     * @return array
     */
    private function _getExtra($channel, $request)
    {
        $extra = [];
        $inWeb = $request->input('web');
        switch ($channel) {
            case  'yeepay_wap':
                $extra = array(
                    'product_category' => '7',
                    'identity_id' => auth()->id() . '',
                    'identity_type' => 2,
                    'terminal_type' => 3,
                    'terminal_id' => auth()->id() . '',
                    'user_ua' => $request->server('HTTP_USER_AGENT'),
                    'result_url' => $inWeb ? url('order') : url($this->pingxxConfig['success_url'][$channel])
                );
                break;
            case  'alipay' :

                break;
            case 'alipay_wap' :
                $extra = array(
                    'success_url' => $inWeb ? url('order') : url($this->pingxxConfig['success_url'][$channel]),
                    'cancel_url' => $inWeb ? url('order') : url($this->pingxxConfig['cancel_url'][$channel])
                );
                break;
        }

        return $extra;
    }
}