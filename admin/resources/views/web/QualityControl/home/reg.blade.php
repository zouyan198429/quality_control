<!doctype html>
<html lang="en">
<head>
    <link rel="stylesheet" href="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/css/layui.css')}}" media="all">

    @include('web.QualityControl.layout_public.pagehead')
</head>
<body  id="body-reg1"   >
    @include('web.QualityControl.layout_public.header')
    <div class="line-blue"></div>
    <form class="am-form" action="#"  method="post"  id="addForm">
	<div id="main">
		<div class="reg"  >

			<div class="hd-reg" >
				<h2>新用户注册</h2>
                <span>  Register</span>
			</div>
			<div class="bd" style="width:800px; margin:0 auto;">

				<div class="form-item">
                    <label for="username" class="form-label">用户名</label>
                    <div class="form-input">
                    	<input type="text" name="admin_username"  autocomplete="off" value="" class="w480">
                   	</div>
                </div>
                <div class="form-item">
                    <label for="password" class="form-label">密码</label>
                    <div class="form-input">
                   		<input type="password" name="admin_password"  autocomplete="off" value="" class="w480">
                    </div>
                </div>
                <div class="form-item">
                    <label for="password" class="form-label">确认密码</label>
                    <div class="form-input">
                    	<input type="password" name="repass"  autocomplete="off" value="" class="w480">
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
                        <input type="text" name="captcha_code" id="LAY-user-login-vercode" lay-verify="required" placeholder="图形验证码" class="layui-input" style="width:100px; display: inline-block;">
                        <input type="hidden" name="captcha_key" />
                        <img src="" class="layadmin-user-login-codeimg" id="LAY-user-get-vercode" >
                    </div>
                </div>
                <div class="form-item">
                    <label for="password" class="form-label"></label>
                	<div class="form-input">
	                	<a href="javascript:void(0);" class="btn btn-default btn-block w150"  id="submitBtn" >注册</a>
 	                </div>
                </div> 
                <div class="fd">    
                    已经有账户了？<a href="{{ url('web/login') }}">登录</a>
                </div>
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
