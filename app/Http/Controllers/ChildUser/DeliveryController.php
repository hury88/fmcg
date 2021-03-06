<?php

namespace App\Http\Controllers\ChildUser;

use App\Models\Order;
use Illuminate\Http\Request;
use DB;
use App\Services\DeliveryService;
use PhpOffice\PhpWord\PhpWord;
use Carbon\Carbon;

class DeliveryController extends Controller
{

    /**
     * DeliveryController constructor.
     */
    public function __construct()
    {
        $this->middleware('deposit');
    }

    /**
     * 配送历史查询．
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function history(Request $request)
    {
        $user = child_auth()->user();
        $search = $request->all();
        $search['start_at'] = array_get($search, 'start_at');;
        $search['end_at'] = array_get($search, 'end_at');
        $search['delivery_man_id'] = array_get($search, 'delivery_man_id');
        $delivery = Order::where('shop_id',
            $user->shop_id)->whereNotNull('delivery_finished_at')->ofDeliverySearch($search)->with('user.shop',
            'shippingAddress.address', 'deliveryMan', 'systemTradeInfo')->orderBy('delivery_finished_at',
            'DESC')->paginate();

        $deliveryMen = $user->shop->deliveryMans;
        return view('child-user.delivery.history',
            ['deliveryMen' => $deliveryMen, 'deliveries' => $delivery, 'search' => $request->all()]);
    }

    /**
     * 配送统计
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function statistical(Request $request)
    {
        $user = child_auth()->user();
        $search = $request->all();
        $data = (new DeliveryService())->deliveryStatistical($search, $user);
        return view('child-user.delivery.statistical',
            ['data' => $data['delivery'], 'search' => $search, 'deliveryNum' => $data['deliveryNum']]);
    }

    /**
     * 统计导出
     *
     */
    public function report(Request $request)
    {
        $search = $request->all();
        $user = child_auth()->user();
        $data = (new DeliveryService())->deliveryStatistical($search, $user)['delivery'];
        $phpWord = new PhpWord();

        $styleTable = array('borderSize' => 1, 'borderColor' => '999999');


        $cellAlignCenter = ['align' => 'center'];
        $cellRowSpan = ['vMerge' => 'restart', 'valign' => 'center'];
        $cellRowContinue = array('vMerge' => 'continue');
        $gridSpan6 = ['gridSpan' => 6, 'valign' => 'center'];
        $gridSpan2 = ['gridSpan' => 2, 'valign' => 'center'];
        $gridSpan1 = ['gridSpan' => 1, 'valign' => 'center'];
        $phpWord->setDefaultFontName('仿宋');
        $phpWord->setDefaultFontSize(10);
        $phpWord->addTableStyle('table', $styleTable);

        $phpWord->addParagraphStyle('Normal', [
            'spaceBefore' => 0,
            'spaceAfter' => 0,
            'lineHeight' => 1.2,  // 行间距
        ]);

        $section = $phpWord->addSection();
        $table = $section->addTable('table');
        foreach ($data['deliveryMan'] as $name => $deliveryMan) {

            $table->addRow();
            $table->addCell(2000, $cellRowSpan)->addText('配送人员', null, $cellAlignCenter);
            $table->addCell(1500)->addText($name, null, $cellAlignCenter);
            $table->addCell(1500)->addText('时间', null, $cellAlignCenter);
            $table->addCell(1500)->addText($search['start_at'] . '至' . $search['end_at'], null, $cellAlignCenter);
            $table->addCell(1500)->addText('配送单数', null, $cellAlignCenter);
            $table->addCell(1500)->addText($deliveryMan['orderNum'], null, $cellAlignCenter);

            $table->addRow();
            $table->addCell(1500)->addText('配送订单总金额', null, $cellAlignCenter);
            $table->addCell(1500)->addText('现金', null, $cellAlignCenter);
            $table->addCell(1500)->addText('pos机', null, $cellAlignCenter);
            $table->addCell(1500)->addText('易宝', null, $cellAlignCenter);
            $table->addCell(1500)->addText('支付宝', null, $cellAlignCenter);
            $table->addCell(1500)->addText('平台余额', null, $cellAlignCenter);

            $table->addRow();
            $table->addCell(1500)->addText($deliveryMan['totalPrice'], null, $cellAlignCenter);
            $table->addCell(1500)->addText(array_key_exists(0,
                $deliveryMan['price']) ? $deliveryMan['price'][0] : '0.00', null, $cellAlignCenter);
            $table->addCell(1500)->addText(array_key_exists(cons('trade.pay_type.pos'),
                $deliveryMan['price']) ? $deliveryMan['price'][cons('trade.pay_type.pos')] : '0.00', null,
                $cellAlignCenter);
            $table->addCell(1500)->addText(@bcadd($deliveryMan['price'][cons('trade.pay_type.yeepay')],
                $deliveryMan['price'][cons('trade.pay_type.yeepay_wap')], 2), null, $cellAlignCenter);
            $table->addCell(1500)->addText(@bcadd($deliveryMan['price'][cons('trade.pay_type.alipay_pc')],
                $deliveryMan['price'][cons('trade.pay_type.alipay')], 2), null, $cellAlignCenter);
            $table->addCell(1500)->addText(array_key_exists(cons('trade.pay_type.balancepay'),
                $deliveryMan['price']) ? $deliveryMan['price'][cons('trade.pay_type.balancepay')] : '0.00', null,
                $cellAlignCenter);
        }
        $table->addRow();
        $table->addCell(9000, $gridSpan6)->addText('商品列表', null, $cellAlignCenter);

        foreach ($data['goods'] as $deliveryNum => $allgoods) {
            $table->addRow();
            $table->addCell(9000, $gridSpan6)->addText($deliveryNum . '人', null, $cellAlignCenter);
            $table->addRow();
            $table->addCell(3000, $gridSpan2)->addText('商品名称', null, $cellAlignCenter);
            $table->addCell(3000, $gridSpan2)->addText('购买角色', null, $cellAlignCenter);
            $table->addCell(1500, $gridSpan1)->addText('商品数量', null, $cellAlignCenter);
            $table->addCell(1500, $gridSpan1)->addText('金额', null, $cellAlignCenter);
            foreach ($allgoods as $goodsName => $goods) {

                foreach ($goods as $userType => $detail) {
                    foreach ($detail as $goodsPieces => $goodsDetail) {
                        $table->addRow();
                        if (array_keys($goods)[0] == $userType && array_keys($detail)[0] == $goodsPieces) {
                            $table->addCell(3000, array_merge($gridSpan2, $cellRowSpan))->addText($goodsName, null,
                                $cellAlignCenter);
                        } else {
                            $table->addCell(null, array_merge($gridSpan2, $cellRowContinue));
                        }
                        if (array_keys($detail)[0] == $goodsPieces) {
                            $table->addCell(3000,
                                array_merge($gridSpan2, $cellRowSpan))->addText(cons()->valueLang('user.type',
                                cons('user.type.' . $userType)), null,
                                $cellAlignCenter);
                        } else {
                            $table->addCell(null, array_merge($gridSpan2, $cellRowContinue));
                        }

                        $table->addCell(3000,
                            $gridSpan1)->addText($goodsDetail['num'] . cons()->valueLang('goods.pieces', $goodsPieces),
                            null,
                            $cellAlignCenter);
                        $table->addCell(3000, $gridSpan1)->addText($goodsDetail['price'], null, $cellAlignCenter);

                    }
                }


            }

        }

        $name = '配送统计报告.docx';
        $phpWord->save(iconv('UTF-8', 'GBK//IGNORE', $name), 'Word2007', true);
    }


}