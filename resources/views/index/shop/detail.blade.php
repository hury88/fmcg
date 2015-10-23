@extends('index.master')

@section('subtitle', '店铺详情')

@section('container')
    <div class="container wholesalers-index goods-detail">
        <div class="row">
            <div class="col-sm-5 left-store-logo">
                <div id="myCarousel" class="carousel slide banner-slide">
                    <ol class="carousel-indicators">
                        @foreach($shop->images as $key=>$image)
                            <li data-target="#myCarousel" data-slide-to="{{ $key }}"
                                class="{{ $key == 0 ? 'active' : '' }}">
                        @endforeach
                    </ol>
                    <div class="carousel-inner banner">
                        @foreach($shop->images as $key=>$image)
                            <div class="item {{ $key == 0 ? 'active' : '' }}">
                                <img src="{{ $image->url }}" alt="{{ $image->name }}">
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="col-sm-7 store-detail-wrap">
                <ul class="store-detail information">
                    <li><span class="title-name">店家图片 </span><img class="stores-img"
                                                                  src="{{ $shop->logo ? $shop->logo->url : '' }}"></li>
                    <li><span class="title-name">联系人 </span><b>{{ $shop->contact_person }}</b></li>
                    <li><span class="title-name">联系方式 </span><b>{{ $shop->contact_info }}</b></li>
                    <li><span class="title-name">最低配送额 </span><b class="red">￥{{ $shop->min_money }}</b></li>
                    <li><span class="title-name">店家地址 </span><b class="red">{{ $shop->address }}</b></li>
                    <li><span class="title-name">店家介绍 </span><span>{{ $shop->introduction }}</span>
                    </li>
                </ul>
            </div>
        </div>
        <div class="row nav-wrap">
            <div class="col-sm-12 switching">
                <a href="#"  class="active">配送区域</a>
            </div>
            <div class="col-sm-12 address-wrap">
                <div class="item clearfix">
                    <h5 class="title-name">商品配送区域</h5>
                    <ul class="address-list">
                        @foreach($shop->deliveryArea as $area)
                            <li>{{ $area->address_name }}</li>
                        @endforeach
                    </ul>
                </div>
                <div class="item">
                    <h5 class="title-name">商品配送区域大概地图标识</h5>

                    {{--<p class="map pull-left">--}}
                        {{--<img class="img-thumbnail" src="http://placehold.it/470x350">--}}

                    {{--</p>--}}
                    <div id="map"></div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js-lib')
    @parent
    <script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=mUrGqwp43ceCzW41YeqmwWUG"></script>
@stop
@section('js')
    <script type="text/javascript">
        $(document).ready(function () {
            $('.carousel').carousel({
                interval: 2000
            });
            getCoordinateMap({!! $coordinates !!});
        });
    </script>
@stop
