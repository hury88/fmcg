@extends('master')

@section('title')@yield('subtitle') | 订百达 - 订货首选@stop

@include('includes.chat')
@include('includes.notice')

{{--@if(!request()->cookie('province_id'))--}}
{{--@include('includes.first-load-model')--}}
{{--@endif--}}

@section('css')
    <link href="{{ asset('css/index.css?v=1.0.0') }}" rel="stylesheet">
    <link href="{{ asset('css/shop-navigator.css') }}" rel="stylesheet">
    @stop

@section('header')
    <!--[if lt IE 9]>
    <div class="ie-warning alert alert-warning alert-dismissable fade in">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        您的浏览器不是最新的，您正在使用 Internet Explorer 的一个<strong>老版本</strong>。 为了获得最佳的浏览体验，我们建议您选用其它浏览器。
        <a class="btn btn-primary" href="http://browsehappy.com/" target="_blank" rel="nofollow">立即升级</a>
    </div>
    <![endif]-->
    <div class="dealer-top-header">
        <div class="container ">
            <div class="row">
                <div class="col-xs-6 city-wrap">
                    <div class="location-panel">
                        <i class="fa fa-map-marker"></i> 所在地：
                        <a href="#" class="location-text">
                            <span class="city-value" title="{{  $addressData['address_name'] }}">
                                {{  $addressData['address_name'] }}
                            </span>
                            <span class="fa fa-angle-down up-down"></span>
                        </a>
                    </div>
                    <div class="city-list clearfix">
                        <ul id="myTab" class="nav nav-tabs">
                            <li>
                                <a href="#deliveryProvince" data-class="deliveryProvince" data-toggle="tab"
                                   class="delivery-province" data-id="{{ $addressData['province_id'] }}">请选择</a>
                            </li>
                            <li class="active">
                                <a href="#deliveryCity" data-class="deliveryCity" data-toggle="tab"
                                   class="delivery-city" data-id="{{ $addressData['city_id'] }}">请选择</a>
                            </li>
                        </ul>
                        <div class="list-wrap tab-content" id="myTabContent">
                            <div class="tab-pane fade deliveryProvince">

                            </div>

                            <div class="tab-pane fade  in active deliveryCity">

                            </div>
                        </div>
                    </div>

                    <div class="login-info-wrap">
                        @if(isset($user))
                            <a href="{{ url("personal/info") }}" class="name-panel">
                                <span class="user-name">{{ $user->shop_name }}
                                    ({{ cons()->valueLang('user.type' , $user->type) }})</span>
                                <span class="exit"
                                      onclick="window.location.href='{{ url('auth/logout') }}';return false;">退出</span>
                            </a>
                        @else
                            <a href="{{ url('auth/login') }}" class="red login">登录</a>
                        @endif
                        <a href="{{ url('personal/chat') }}">消息( <span class="total-message-count">0</span> )</a>
                    </div>

                </div>
                <div class="col-xs-6">
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle collapsed navbar-button" data-toggle="collapse"
                                data-target="#bs-example-navbar-collapse-9" aria-expanded="false">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                    </div>
                    <div class="navbar-collapse collapse top-nav-list" id="bs-example-navbar-collapse-9">
                        <ul class="nav navbar-nav navbar-right pull-right operating-wrap">
                            {{--@if((isset($user) && $user->type <= cons('user.type.wholesaler')) || is_null($user))--}}
                            <li><a href="{{ url('/') }}">订百达首页</a></li>
                            {{--@endif--}}

                            <li><a href="{{ url('personal/info') }}">管理中心</a></li>
                            <li>
                                <a href="{{ isset($user) && $user->type > cons('user.type.retailer') ? url('order-sell') : url('order-buy') }}">
                                    我的订单</a></li>
                            <li class="notice"><a href="#"> 活动公告</a>
                                <div class="upcoming-events-wrap">
                                    <ul class="upcoming-events">
                                        @foreach((new \App\Services\NoticeService())->getNotice() as $key=>$notice)
                                            <li>
                                                <a class="content-title" href="javascript:" data-target="#noticeModal"
                                                   data-toggle="modal"
                                                   data-content="{{ $notice->content }}"
                                                   title="{{ $notice->title }}">{{ ($key+1). '.' .$notice->title }}
                                                </a>
                                            </li>

                                        @endforeach
                                    </ul>
                                </div>
                            </li>
                            <li><a href="{{ url('help') }}"> 帮助中心</a></li>
                            @if((isset($user) && $user->type < cons('user.type.supplier')) || is_null($user))
                                <li class="collect-select">
                                    <a href="{{ url('like/goods') }}" class="collect-selected">
                                        <span class="selected">收藏夹</span>
                                        <span class="fa fa-angle-down"></span>
                                    </a>
                                    <ul class="select-list">
                                        <li><a href="{{ url('like/goods') }}">商品收藏</a></li>
                                        <li><a href="{{ url('like/shops') }}">店铺收藏</a></li>
                                    </ul>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('body')
    @yield('container')
    @if ( !is_null(auth()->user()))
        @include('includes.navigator')
    @endif
    @if(isset($user))
        <audio id="myaudio" src="{{ asset('images/notice.wav') }}" style="opacity:0;">
        </audio>
        <div class="msg-channel control-center-channel" id="alert-div">
            <div class="title"><span class="pull-left">你有新消息</span><a class="close-btn  pull-right"><i
                            class="fa fa-remove"></i></a>
            </div>
            <a class="check" href="#">点击查看>>>></a>
        </div>
    @endif
@stop

@section('footer')
    <div class="footer">
        @yield('join-us')
        <footer class="panel-footer footer">
            <div class="container  text-muted">
                <div class="row ">
                    <div class="col-xs-6 pd-left-clear">
                        <ul class="list-inline">
                            <li><a href="{{ url('about') }}" class="icon about">关于我们</a></li>
                            <li>
                                <div class="contact-panel">
                                    <a href="javascript:;" class="icon contact-information">联系方式</a>
                                </div>
                                <div class="contact-content content hidden">
                                    <div>{{ cons('system.company_tel') . '&nbsp;&nbsp;&nbsp;&nbsp;' . cons('system.company_mobile') }}</div>
                                    <div>{{ cons('system.company_addr') }}</div>
                                </div>
                            </li>
                            <li>
                                <div class="feedback-panel">
                                    <a class="feedback icon" href="javascript:;">意见反馈</a>
                                </div>
                                <div class="content hidden">
                                    <form class="ajax-form" method="post" action="{{ url('api/v1/feedback') }}"
                                          accept-charset="UTF-8" data-help-class="error-msg text-center"
                                    >
                                        <div>
                                            <textarea placeholder="请填写您的反馈意见" name="content"></textarea>
                                        </div>
                                        <div>
                                            <div class="input-group">
                                            <span class="input-group-addon" id="feedback-contact"><i
                                                        class="fa fa-envelope-o"></i></span>
                                                <input type="text" class="form-control" placeholder="留个邮箱或者别的联系方式呗"
                                                       aria-describedby="feedback-contact" name="contact">
                                                <span class="input-group-btn">
                                                <button class="btn btn-primary btn-submit" type="submit"
                                                        data-done-then="none" data-done-text="反馈提交成功">提交
                                                </button>
                                            </span>
                                            </div>
                                            <!-- /input-group -->
                                        </div>
                                    </form>
                                </div>
                            </li>
                            <li>
                                <div id="qr-content-panel">
                                    <a href="javascript:;" class="app-down icon">APP下载</a>
                                </div>
                                <div class="content hidden">
                                    <div class="qr-panel">
                                        <div class="dbd item">
                                            <div class="qr-code dbd-qr-code"></div>
                                            <div class="text text-center">订百达</div>
                                        </div>
                                        <div class="driver-helper item">
                                            <div class="qr-code helper"></div>
                                            <div class="text text-center">司机助手</div>
                                        </div>
                                        <div class="driver-helper item">
                                            <div class="qr-code field"></div>
                                            <div class="text text-center">外勤</div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="col-xs-6">
                        <p>
                            Copyright &copy; {!! cons('system.company_name') . '&nbsp;&nbsp;&nbsp;&nbsp;' . cons('system.company_record') !!} </p>
                    </div>
                </div>
            </div>
        </footer>
        <div id="allmap"></div>
    </div>
@stop

@section('js-lib')
    @parent
    <script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=mUrGqwp43ceCzW41YeqmwWUG"></script>
    <script type="text/javascript" src="{{ asset('js/index.js?v=1.0.0') }}"></script>
    <script type="text/javascript" src="{{ asset('js/address-for-delivery.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/ajax-polling.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/shop-navigator.js') }}"></script>
@stop

@section('js')
    <script type="text/javascript">
        //没有定位获取用户当前位置
                @if(!request()->cookie('province_id'))
        var map = new BMap.Map("allmap");
        var point = new BMap.Point(116.331398, 39.897445);
        map.centerAndZoom(point, 12);

        function myFun(result) {
            var cityName = result.name;
            map.setCenter(cityName);
            if (document.cookie.indexOf("province_id=") == -1) {
                $.ajax({
                    url: site.api('address/city-detail'),
                    method: 'get',
                    data: {name: cityName}
                }).done(function (data, textStatus, jqXHR) {
                    setCookie('province_id', data.province_id);
                    setCookie('city_id', data.city_id);
                    window.location.reload();
                });

            }
        }

        var myCity = new BMap.LocalCity();
        myCity.get(myFun);
        @endif
    $(function () {

            //意见反馈
            $('.feedback-panel > a').popover({
                container: '.feedback-panel',
                placement: 'top',
                html: true,
                content: function () {
                    return $(this).parent().siblings('.content').html();
                }
            })

            //扫二维码下载app
            tooltipFunc('#qr-content-panel > a', '#qr-content-panel');
            //联系方式
            tooltipFunc('.contact-panel > a', '.contact-panel');

            //调用tooltip插件
            function tooltipFunc(item, container) {
                $(item).tooltip({
                    container: container,
                    placement: 'top',
                    html: true,
                    title: function () {
                        return $(this).parent().siblings('.content').html();
                    }
                })
            }


        });
    </script>
@stop