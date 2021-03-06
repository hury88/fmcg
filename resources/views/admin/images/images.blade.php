@extends('admin.master')
@include('includes.treetable')
@include('includes.cropper')
@section('subtitle' , '图片管理')

@section('right-container')
    <form class="form-horizontal ajax-form" action="{{ url('admin/images') }}" method="post"
          data-help-class="col-sm-push-2 col-sm-10" data-done-url="{{ url('admin/images') }}" autocomplete="off">
        <div class="form-group">
            <label class="col-sm-1 control-label"></label>

            <div class="col-sm-10">
                <button data-height="400" data-width="400" data-target="#cropperModal" data-toggle="modal"
                        data-loading-text="图片已达到最大数量" class="btn btn-primary btn-sm" type="button"
                        id="pic-upload">
                    请选择图片文件(裁剪)
                </button>

                <div class="progress collapse">
                    <div class="progress-bar progress-bar-striped active"></div>
                </div>
                 <span data-name="agency_contract" class="btn btn-primary btn-sm fileinput-button">
                        请选择图片文件(批量)
                       <input type="file" accept="image/*" data-url="{{ url('api/v1/file/upload-temp') }}" name="file"
                              data-multi="multi"
                              multiple>
                  </span>

                <div class="row pictures">

                </div>
            </div>
        </div>
        <div class="col-sm-8 text-center save">
            <button class="btn btn-bg btn-primary" type="submit"><i class="fa fa-save"></i> 保存</button>
            <button class="btn btn-bg btn-warning" type="button" onclick="javascript:history.go(-1)">
                <i class="fa fa-reply"></i> 取消
            </button>
        </div>
        </div>
    </form>
@stop

@section('js')
    @parent
    <script>
        $(function () {
            picFunc(10000);
        });
    </script>
@stop