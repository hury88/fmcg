@extends('index.help.master')
@section('subtitle', '批发商发布商品')

@section('content')
    <h3 class="content-title"><span>批发商发布商品</span></h3>

    <div class="content-panel">
        <br>

        <p class="title-name">添加商品 :</p>

        <div class="padding-2em">
            <p>1.进入管理中心，点击商品管理中新增商品<img src="{{ asset('images/help-images/22.jpg') }}">，根据相关提示填写对应内容。</p>

            <p>2.促销信息，选择是否促销为是的时候才能输入促销信息，选择否则无需输入。</p>

            <p>3.商品配送区域，默认会与个人信息中商铺配送区域，可根据该商品实际情况对配送区域进行修改。</p>

            <p>4.填写完所有信息后，点击立即上架，系统会将商品信息进行保存并上架。点击保存，则系统只会将商品信息进行保存但不会进行上架，未上架商品只有自己能查看。</p>
        </div>
        <p class="title-name">批量添加商品 :</p>

        <div class="padding-2em">
            <p>1.进入管理中心，点击商品管理中批量导入<img src="{{ asset('images/help-images/23.jpg') }}">进入。</p>

            <p>2.点击模板下载，下载Execl商品上传模块，根据模板提醒信息填写商品信息。建议将要上传商品根据订百达商品分类进行分类，保存为多个Execl文件分次上传。</p>

            <p>3.选择商品分类，选择将上传商品所属分类，商品标签可暂时不进行选择，可等上传商品后再根据具体商品进行对应标签设置。</p>

            <p>4.点击<img src="{{ asset('images/help-images/24.jpg') }}">选择将保存商品信息的Execl文件。</p>

            <p>5.点击立即上架，系统会将商品信息进行保存并上架。点击保存，则系统只会将商品信息进行保存但不会进行上架，未上架商品只有自己能查看。</p>
        </div>
    </div>
@stop