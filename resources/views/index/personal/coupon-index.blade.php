@extends('index.menu-master')
@section('subtitle', '个人中心-优惠券')

@section('top-title')
    <a href="{{ url('personal/info') }}">个人中心</a> > <span class="second-level"> 优惠券</span>
@stop

@include('includes.coupon')
@section('right')
    <form action="#" method="post">
        <div class="row coupon">
            <div class="col-sm-12 table-responsive">
                <div class="add-coupon">
                    <a class="add btn btn-blue-lighter" href="javascript:" data-toggle="modal"
                           data-target="#couponModal">
                        <label>
                            <span class="fa fa-plus"></span>
                        </label>添加优惠券
                    </a>
                </div>
                <table class="table table-bordered table-center public-table">
                    <thead>
                    <tr>
                        <th>序号</th>
                        <th>满</th>
                        <th>减</th>
                        <th>库存</th>
                        <th>总量</th>
                        <th>结束时间</th>
                        <th>状态</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($coupons as $coupon)
                        <tr>
                            <td>
                                {{ $coupon->id }}
                            </td>
                            <td>
                                {{ $coupon->full }}
                            </td>
                            <td>
                                {{ $coupon->discount  }}
                            </td>
                            <td>
                                {{ $coupon->stock  }}
                            </td>
                            <td>
                                {{ $coupon->total  }}
                            </td>
                            <td>
                                {{ $coupon->end_at  }}
                            </td>
                            <td>
                                {{ $coupon->status_name  }}
                            </td>
                            <td>

                                <div role="group" class="btn-group btn-group-xs">
                                    <a data-url="{{ url('api/v1/personal/coupon/'. $coupon->id) }}" data-method="delete"
                                       class="red ajax" type="button">
                                        <i class="fa fa-trash-o"></i> 删除
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach

                    </tbody>
                </table>
            </div>
        </div>
    </form>
    @parent
@stop
