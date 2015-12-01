@section('body')
    <div class="modal fade" id="backupModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title text-center forgot-modal-title" id="myModalLabel">
                        找回密码
                    </h4>
                </div>
                <form class="form-horizontal ajax-form" action="{{ url('api/v1/auth/backup') }}" method="post"
                      data-help-class="col-sm-push-3 col-sm-10" autocomplete="off">
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="user_name">
                                <span class="prompt">用户账号:</span></label>

                            <div class="col-sm-8">
                                <input class="form-control" name="user_name" placeholder="请输入用户账号" type="text">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="backup_mobile">
                                <span class="prompt">密保手机:</span></label>

                            <div class="col-sm-8">
                                <input class="form-control" name="backup_mobile" placeholder="请输入密保手机" type="text">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="license_num">
                                <span class="prompt">营业执照注册号:</span></label>

                            <div class="col-sm-8">
                                <input class="form-control" name="license_num" placeholder="请输入营业执照注册号" type="text">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="password">
                                <span class="prompt">新密码:</span></label>

                            <div class="col-sm-8">
                                <input class="form-control" name="password" placeholder="请输入新密码" type="password">
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer text-center">
                        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                        <button type="submit" class="btn btn-primary">确认</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @parent
@stop

@section('js')
    @parent
    <script>
        $(function () {
            var cropper = $('#backupModal'),
                    userName = cropper.find('input[name="user_name"]'),
                    backupMobile = cropper.find('input[name="backup_mobile"]'),
                    licenseNum = cropper.find('input[name="license_num"]'),
                    password = cropper.find('input[name="password"]');

            cropper.on('hidden.bs.modal', function () {
                userName.val('');
                backupMobile.val('');
                licenseNum.val('');
                password.val('')
            });
        });
    </script>
@stop