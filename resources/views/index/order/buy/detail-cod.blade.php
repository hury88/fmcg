@extends('index.menu-master')

@section('subtitle' , '订单详情')
@section('top-title', '进货管理->订单详情')


@section('top-title')
    <a href="{{ url('order-buy') }}">进货管理</a> &rarr;
    订单详情
@stop

@include('includes.stepBar')
@include('includes.pay')
@include('includes.order-refund')

@section('right')
    <div class="order-detail row">
        <div class="col-sm-12 go-history">
            <a class="go-back" href="javascript:history.back()"><i class="fa fa-reply"></i> 返回</a>
        </div>
        <div class="col-sm-12 order-panel">
            <ul>
                <li><span class="title-name">订单号 : </span> {{ $order['id'] }}</li>
                <li><span class="title-name">订单金额 : </span><span class="red">¥{{ $order['price'] }}</span></li>
                @if(!is_null($order->systemTradeInfo))
                    <li>
                        <span class="title-name">订单手续费 : </span><span
                                class="red">¥{{ $order->systemTradeInfo->target_fee }}</span>
                    </li>
                @endif
                <li><span class="title-name">支付方式 : </span>{{ $order['payment_type'] }}( {{ $order->pay_way_lang  }} )
                </li>
                <li><span class="title-name">订单状态 : </span><span class="red">{{ $order['status_name'] }}</span></li>
                <li><span class="title-name">备注 : </span>

                    <p class="remarks-content">{{ $order['remark'] }}</p>
                </li>
            </ul>
        </div>
        <div class="col-sm-12">
            <div id="stepBar" class="ui-stepBar-wrap">
                <div class="ui-stepBar">
                    <div class="ui-stepProcess"></div>
                </div>
                <div class="ui-stepInfo-wrap">
                    <div class="ui-stepLayout">
                        <ul>
                            <li class="ui-stepInfo">
                                <a class="ui-stepSequence"></a>

                                <p class="ui-stepName">未发货</p>
                            </li>
                            <li class="ui-stepInfo">
                                <a class="ui-stepSequence"></a>

                                <p class="ui-stepName">已发货</p>
                            </li>
                            <li class="ui-stepInfo">
                                <a class="ui-stepSequence"></a>

                                <p class="ui-stepName">已付款</p>
                            </li>
                            <li class="ui-stepInfo">
                                <a class="ui-stepSequence"></a>

                                <p class="ui-stepName">已完成</p>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-12 order-table table-responsive text-center">
            <table class="table table-bordered little-table">
                <thead>
                <tr>
                    <td>订单操作</td>
                    <td>操作时间</td>
                    <td>操作人</td>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>提交订单</td>
                    <td>{{ $order['created_at'] }}</td>
                    <td>{{ $order->user->shop_name }}</td>
                </tr>
                @if((int)$order['send_at'])
                    <tr>
                        <td>
                            订单发货
                        </td>
                        <td>{{ $order['send_at'] }}</td>
                        <td>{{ $order->shop->name }}</td>
                    </tr>
                @endif
                @if((int)$order['paid_at'])
                    <tr>
                        <td>付款</td>
                        <td>{{ $order['paid_at'] }}</td>
                        <td>{{ $order->user->shop_name }}</td>
                    </tr>
                @endif
                @if((int)$order['finished_at'])
                    <tr>
                        <td>已完成</td>
                        <td>{{ $order['finished_at'] }}</td>
                        <td>{{ $order->user->shop_name }}</td>
                    </tr>
                @endif
                @if($order['is_cancel'])
                    <tr>
                        <td>取消订单</td>
                        <td>{{ $order['cancel_at'] }}</td>
                        <td>{{ $order['cancel_by'] == $order->user->id ? $order->user->shop_name : $order->shop->name }}</td>
                    </tr>
                @endif
                </tbody>
            </table>
        </div>

        <div class="col-sm-12 store-msg">
            <div class="clearfix item">
                <label class="pull-left title-name">商家信息</label>
                <ul class="pull-left">
                    <li>
                        <span>商家名称 :</span>
                        <span><a href="{{ url('shop/' . $order['shop']['id']) }}"
                                 target="_blank">{{ $order['shop']['name'] }}</a></span>
                    </li>
                    <li>
                        <span>联系人 :</span>
                        <span>{{ $order['shop']['contact_person'] }}</span>
                    </li>
                    <li>
                        <span>联系电话 :</span>
                        <span>{{ $order['shop']['contact_info'] }}</span>
                    </li>
                </ul>
            </div>
            @if((int)$order['send_at'])
                <div class="clearfix item">
                    <ul>
                        <li class="title">配送人信息</li>
                        @foreach($order->deliveryMan as $deliveryMan)
                            <li>
                                <span class="title-name">联系人 : </span>{{ $deliveryMan->name . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $deliveryMan->phone }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="item">
                <label class="title-name">收货地址</label>
                <span>{{ $order->shippingAddress->address ? $order->shippingAddress->address->address_name : '' }}</span>
            </div>
            <div class="table-responsive order-table clearfix item">
                <label class="pull-left title-name">订单商品</label>
                <table class="table table-bordered table-center">
                    <thead>
                    <tr>
                        <th>商品编号</th>
                        <th>商品图片</th>
                        <th>商品名称</th>
                        <th>商品价格</th>
                        <th>商品数量</th>
                        <th>金额</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($orderGoods as $goods)
                        <tr>
                            <td>{{ $goods['id'] }}</td>
                            <td><img class="store-img" src={{ $goods['image_url'] }} /></td>
                            <td>
                                <div class="product-panel">
                                    <a class="product-name"
                                       href="{{ url('goods/'. $goods['id']) }}">{{ $goods->name }}</a>
                                    {!! $goods->is_promotion ? '<p class="promotions">(<span class="ellipsis"> ' . $goods->promotion_info . '</span>)</p>' : '' !!}
                                </div>
                            </td>
                            <td>{{ $goods['pivot']['price'] }}
                                / {{ cons()->valueLang('goods.pieces', $goods->pivot->pieces)  }}</td>
                            <td>{{ $goods['pivot']['num'] }}</td>
                            <td>{{ $goods['pivot']['total_price'] }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            @if (!$mortgageGoods->isEmpty())
                <div class="table-responsive order-table clearfix item">
                    <label class="pull-left title-name">抵费商品</label>
                    <table class="table table-bordered table-center">
                        <thead>
                        <tr>
                            <th>商品编号</th>
                            <th>商品图片</th>
                            <th>商品名称</th>
                            <th>商品数量</th>
                            @if($order->can_change_price)
                                <th>操作</th>
                            @endif
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($mortgageGoods as $goods)
                            <tr>
                                <td>{{ $goods['id'] }}</td>
                                <td><img class="store-img" src={{ $goods['image_url'] }} /></td>
                                <td>
                                    <div class="product-panel">
                                        <a class="product-name"
                                           href="{{ url('goods/'. $goods['id']) }}">{{ $goods->name }}</a>
                                        {!! $goods->is_promotion ? '<p class="promotions">(<span class="ellipsis"> ' . $goods->promotion_info . '</span>)</p>' : '' !!}
                                    </div>
                                </td>
                                <td>{{ $goods['pivot']['num'] }}</td>
                                @if($order->can_change_price)
                                    <td><a class="change-price" href="javascript:void(0)" data-target="#changePrice"
                                           data-toggle="modal" data-id="{{ $order['id'] }}"
                                           data-price="{{ $goods->pivot->price }}" ,
                                           data-num="{{ $goods->pivot->num }}"
                                           data-pivot="{{  $goods['pivot']['id'] }}">修改</a>
                                        <a class="delete-no-form" data-method="delete"
                                           data-url="{{ url('api/v1/order/goods-delete/' . $goods->pivot->id) }}"
                                           href="javascript:">删除</a>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
        <div class="col-sm-12 text-right bottom-content">
            <p>
                总额：<b class="red">¥{{ $order->price }}</b>
                @if($order->coupon_id)
                    <br/> 优惠：<b class="red">¥{{ bcsub($order->price, $order->after_rebates_price, 2) }}</b>
                    <br><span class="prompt-coupon">(满{{ $order->coupon->full }}减 {{ $order->coupon->discount }}
                        )</span>
                    <br/>  应付金额：<b class="red">¥{{ $order->after_rebates_price }}</b>
                @elseif($order->display_fee > 0)
                    <br/> 陈列费：<b class="red">¥{{ $order->display_fee }}</b>

                    <br/>  应付金额：<b class="red">¥{{ $order->after_rebates_price }}</b>
                @endif
            </p>

            <p>
                @if(!$order['is_cancel'])
                    @if ($order->can_refund)
                        <a class="btn btn-danger refund" data-target="#refund"
                           data-toggle="modal"
                           data-url="{{ url('api/v1/pay/refund/' . $order->id) }}">
                            退款
                        </a>
                    @elseif($order['can_cancel'])
                        <a class="btn btn-cancel ajax"
                           data-url="{{ url('api/v1/order/cancel-sure') }}"
                           data-method="put"
                           data-data='{"order_id":{{ $order['id'] }}}'>取消</a>
                    @endif
                    @if($order['can_payment'])
                        <a href="javascript:" data-target="#payModal" data-toggle="modal"
                           class="btn btn-success" data-id="{{ $order->id }}"
                           data-price="{{ $order->after_rebates_price }}">去付款</a>
                    @elseif($order['can_confirm_arrived'])
                        <a class="btn btn-danger ajax"
                           data-url="{{ url('api/v1/order/batch-finish-of-buy') }}"
                           data-method="put"
                           data-data='{"order_id":{{ $order['id'] }}}'>确认收货</a>
                    @endif
                @endif
            </p>
        </div>
    </div>
@stop