@extends('index.manage-master')
@section('subtitle', '入库异常')
@section('container')
    @include('includes.menu')
    <div class="page-content-wrapper">
        <div class="page-content">
            <div class="row">
                <div class="col-sm-12 path-title">
                    <a href="{{ url('inventory') }}">库存管理</a> >
                    <span class="second-level">入库异常</span>
                </div>
            </div>
            <div class="row delivery">
                <div class="col-sm-12 control-search">
                    <a type="button" class="btn btn-default control" href="javascript:history.back()">返回</a>
                </div>
                <div class="col-sm-12 table-responsive wareh-details-table">
                    <table class="table-bordered table table-center public-table">
                        <thead>
                        <tr>
                            <th>商品名称</th>
                            <th>商品条形码</th>
                            <th>进货单号</th>
                            <th>日期</th>
                            <th>
                                <a class="iconfont icon-tixing" title=""
                                   data-container="body" data-toggle="popover" data-placement="bottom"
                                   data-content="我的商品库里没有与此匹配的商品,请新先新增商品后再手动入库">
                                </a>
                                操作
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(isset($errorLists))
                            @foreach($errorLists as $list)
                                <tr>
                                    <td>
                                        <div class="product-name" title="{{$list->goods->name ?? ''}}">
                                            {{$list->type > 0 ? '('.cons()->valueLang('inventory.source',$list->type).')' : ''}}
                                            {{$list->goods->name ?? ''}}
                                        </div>
                                    </td>
                                    <td>{{$list->goods->bar_code ?? ''}}</td>
                                    <td>{{$list->order_id ?? ''}}</td>
                                    <td>{{$list->updated_at}}</td>
                                    <td>
                                        @if($shopGoods->where('bar_code',$list->goods->bar_code)->first())
                                            <a class="gray">已增商品</a>
                                            <a href="{{url('inventory/in-create?orderGoods='.$list->id)}}"
                                               class="green">我要入库</a>
                                        @else
                                            <a href="{{url('my-goods/create/')}}?orderGoods={{$list->id ?? ''}}"
                                               class="edit">新增商品</a>
                                        @endif

                                        <a class="color-blue viewDetail" data-order_goods="{{$list->id}}"
                                           data-target="#myModal"
                                           data-toggle="modal">查看</a>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
                <div class="col-sm-12 text-right">
                    {!! $errorLists->render() !!}
                </div>
            </div>
        </div>
    </div>
    @include('includes.in-error-detail')
@stop
@section('js')
    @parent
    <script type="text/javascript">
        $(function () {
            $("[data-toggle='popover']").popover();
        })
    </script>
@stop
