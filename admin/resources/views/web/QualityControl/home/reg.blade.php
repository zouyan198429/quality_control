<!doctype html>
<html lang="en">
<head>
    @include('web.QualityControl.layout_public.pagehead')
    <link rel="stylesheet" href="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/css/layui.css')}}" media="all">
</head>
<body style=" background:#eee; ">
    @include('web.QualityControl.layout_public.header')

    <form class="am-form" action="#"  method="post"  id="addForm">
	<div id="main">
		<div class="reg" style="width:980px; margin:40px auto 20px auto; border:1px solid #eee; min-height:500px;  padding:20px 20px; background:#fff;  ">

			<div class="hd tc" style="padding:30px 0; ">
				<h2>欢迎注册</h2>
			</div>
			<div class="bd" style="width:800px; margin:0 auto;">

				<div class="form-item">
                    <label for="username" class="form-label">用户名</label>
                    <div class="form-input">
                    	<input type="text" name="admin_username"  autocomplete="off" value="">
                   	</div>
                </div>
                <div class="form-item">
                    <label for="password" class="form-label">密码</label>
                    <div class="form-input">
                   		<input type="password" name="admin_password"  autocomplete="off" value="">
                    </div>
                </div>
                <div class="form-item">
                    <label for="password" class="form-label">确认密码</label>
                    <div class="form-input">
                    	<input type="password" name="repass"  autocomplete="off" value="">
                    </div>
                </div>
                <div class="form-item">
                    <label for="password" class="form-label">帐户类型</label>
                    <div class="form-input ">
	                    <input type="radio" name="admin_type" value="2" title="企业帐号">企业帐号
	                    <input type="radio" name="admin_type" value="4" title="个人帐号">个人帐号
                    </div>
                </div>
                <div class="form-item">
                    <label for="password" class="form-label">图形验证码</label>
                    <div class="form-input">
                        <input type="text" name="captcha_code" id="LAY-user-login-vercode" lay-verify="required" placeholder="图形验证码" class="layui-input" style="width:100px;">
                        <input type="hidden" name="captcha_key" />
                        <img src="" class="layadmin-user-login-codeimg" id="LAY-user-get-vercode" >
                    </div>
                </div>
                <div class="form-item">
                    <label for="password" class="form-label"></label>
                	<div class="form-input">
	                	<a href="javascript:void(0);" class="btn btn-default btn-block"  id="submitBtn" >注册</a>
{{--                        <button class="layui-btn layui-btn-fluid"  id="submitBtn" >注册</button>--}}
	                </div>
                </div>
                <div class="line"></div>
                <p class="text-muted text-center">已经有账户了？<a href="{{ url('web/login') }}">登录</a> </p>

			</div>
			<div class="c"></div>
		</div>
	</div>
    </form>
    @include('web.QualityControl.layout_public.footer')
</body>
</html>
<script src="{{asset('js/jquery-3.3.1.min.js')}}"></script>
<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.all.js')}}"></script>
{{--<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.js')}}"></script>--}}
@include('public.dynamic_list_foot')

<script>
    var REG_URL = "{{ url('api/web/ajax_reg') }}";
    var GET_CAPTCHA_IMG_URL = "{{ url('api/web/ajax_captcha') }}";
    var CAPTCHA_IMG_ID = "LAY-user-get-vercode";
    var CAPTCHA_KEY_INPUT_NAME = "captcha_key";
    // var INDEX_URL = "{{url('web')}}";
    var PERFECT_COMPANY_URL = "{{url('web/perfect_company')}}";// 补充企业资料
    var PERFECT_USER_URL = "{{url('web/perfect_user')}}";// 补充用户资料
</script>
<script src="{{ asset('/js/common/reg.js') }}"  type="text/javascript"></script>
