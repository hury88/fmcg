@extends('master')


@section('title')@yield('subtitle') | 快销后台管理@stop


@section('css')
    <link href="{{ asset('css/admin.css') }}" rel="stylesheet">
@stop


@section('header')
@stop


@section('body')
    <div class="container-fluid admin-container">
        <div class="row">
            <div class="col-sm-2">
                <div class="panel-group text-center" id="accordion">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#accordion"
                                   href="#collapse-one">
                                    系统管理
                                </a>
                            </h4>
                        </div>
                        <div id="collapse-one" class="panel-collapse collapse in">
                            <div class="panel-body">
                                <ul>
                                    <li><a href="#">角色添加</a> <a href="#" class="manger">管理</a></li>
                                    <li><a href="#">菜单添加</a> <a href="#" class="manger">管理</a></li>
                                    <li><a href="{{url('admin/admin/create')}}">管理员添加</a> <a href="#"
                                                                                             class="manger">管理</a></li>
                                    <li><a href="#">密码修改</a></li>

                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#accordion"
                                   href="#collapse-two">
                                    账号管理
                                </a>
                            </h4>
                        </div>
                        <div id="collapse-two" class="panel-collapse collapse">
                            <div class="panel-body">
                                <ul>
                                    <li><a href="{{ url('/admin/wholesalers/create')  }}">批发商添加</a> <a
                                                href="{{ url('/admin/wholesalers')  }}" class="manger">管理</a></li>
                                    <li><a href="#">终端商添加</a> <a href="#" class="manger">管理</a></li>

                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#accordion"
                                   href="#collapse-three">
                                    商品管理
                                </a>
                            </h4>
                        </div>
                        <div id="collapse-three" class="panel-collapse collapse">
                            <div class="panel-body">
                                <ul>
                                    <li><a href="#">角色添加</a> <a href="#" class="manger">管理</a></li>
                                    <li><a href="#">菜单添加</a> <a href="#" class="manger">管理</a></li>
                                    <li><a href="#">管理员添加</a> <a href="#" class="manger">管理</a></li>
                                    <li><a href="#">密码修改</a></li>

                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#accordion"
                                   href="#collapse-four">
                                    广告投放
                                </a>
                            </h4>
                        </div>
                        <div id="collapse-four" class="panel-collapse collapse">
                            <div class="panel-body">
                                <ul>
                                    <li><a href="#">角色添加</a> <a href="#" class="manger">管理</a></li>
                                    <li><a href="#">菜单添加</a> <a href="#" class="manger">管理</a></li>
                                    <li><a href="#">管理员添加</a> <a href="#" class="manger">管理</a></li>
                                    <li><a href="#">密码修改</a></li>

                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#accordion"
                                   href="#collapse-five">
                                    平台交易
                                </a>
                            </h4>
                        </div>
                        <div id="collapse-five" class="panel-collapse collapse">
                            <div class="panel-body">
                                <ul>
                                    <li><a href="#">角色添加</a> <a href="#" class="manger">管理</a></li>
                                    <li><a href="#">菜单添加</a> <a href="#" class="manger">管理</a></li>
                                    <li><a href="#">管理员添加</a> <a href="#" class="manger">管理</a></li>
                                    <li><a href="#">密码修改</a></li>

                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-10">
                <div class="right-container">
                    @yield('right-container')
                </div>
            </div>
        </div>
    </div><!--/.container-->
@stop


@section('js')
    <script src="{{ asset('js/admin.js') }}"></script>
@stop