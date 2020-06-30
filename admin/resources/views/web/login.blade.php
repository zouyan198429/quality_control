<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>登录--教育云助手</title>
	<link rel="stylesheet" href="{{asset('staticweb/css/wnui.css')}}" media="all">
  	<link rel="stylesheet" href="{{asset('staticweb/css/web.css')}}" media="all">
  	<style>
  	body {
  		background: #39a1f3 url({{asset('staticweb/images/bg-login.jpg')}}) no-repeat;
  	}
	.login {
		width: 400px;
		margin:100px auto;
		background: #fff;
		padding:50px 40px ;
		border-radius:8px;
		height: 300px;
	}

	.login-hd {
		color: #fff;
		padding-top: 200px;
	}
	.login-hd h1 {
		font-size: 42px;
		line-height: 1.5em;
	}
	.login .hd  {
		height: 50px;
		margin-bottom: 40px;
		text-align: center;
	}
	.login .hd div {
		text-align: center;
		font-size: 16px;
		font-weight: normal;
		display: inline-block;
		padding: 0 15px;
		color: #888;
	}
	.login .hd div.on {
		font-size: 20px;
		font-weight: bold;
		color: #222;
		border-bottom: 2px solid #333;
	}

	.form-control {
		border:1px solid #ddd;
		border-radius: 20px;
		color: #71737b;
		width: 400px;
	}
	.btn-block {
		width: 100%;
		padding: 12px 0;
		border-radius: 30px;
		margin-top: 20px;
		font-size: 16px;
	}
	a.btn-login {
		border-radius: 30px;
		width: 400px;
		padding:5px 0;
		margin:0 auto;
		line-height: 24px;
	}
  	</style>
</head>
<body>
	<div class="login-main">
		<div class="login-hd">
			<h1>老师小帮手</h1>
 		</div>
        <form class="am-form" action="#"  method="post"  id="addForm">
		<div class="login">
			<div class="hd">
				<div>短信验证登录</div>
 			</div>
			<ul>
				<div class="form-group">
	                <input type="text" name="mobile" class="form-control" placeholder="手机号"   value="">
	            </div>
	            <div class="form-group">
	                <input type="text" name="text" class="form-control"   placeholder="验证码"   value="">
	                <div class="c"></div>
	            </div>
                <div class="layui-form-item">
                    <div class="layui-row">
                        <div class="layui-col-xs7">
                            <label class="layadmin-user-login-icon layui-icon layui-icon-vercode" for="LAY-user-login-vercode"></label>
                            <input type="text" name="mobile_vercode" id="LAY-user-login-vercode" lay-verify="required" placeholder="验证码" class="layui-input">
                        </div>
                        <div class="layui-col-xs5">
                            <div style="margin-left: 10px;">
                                <button type="button" class="layui-btn layui-btn-primary layui-btn-fluid LAY-user-getsmscode" id="LAY-user-getsmscode">获取验证码</button>
                            </div>
                        </div>
                    </div>
                </div>

				<a href="javascript:void(0);" class="btn btn-login"   id="submitBtn">登 录</a>
			</ul>
		</div>
        </form>
	</div>
    <script src="{{asset('js/jquery-3.3.1.min.js')}}"></script>
    <script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.all.js')}}"></script>
    @include('public.dynamic_list_foot')
    <script>
        var LOGIN_URL = "{{ url('api/web/ajax_mobile_reg_login') }}";// 登录
        var GET_CAPTCHA_IMG_URL = "{{ url('api/web/ajax_captcha') }}";// 图形验证码
        var CAPTCHA_IMG_ID = "LAY-user-get-vercode";
        var CAPTCHA_KEY_INPUT_NAME = "captcha_key";

        var INDEX_URL = "{{url('web')}}";// 首页

        var CODE_TIME = 60 * 2;// 手机短信验证码有效期
        var SEND_MOBILE_CODE_URL = "{{ url('api/web/ajax_send_mobile_vercode') }}";// 发送手机验证码
        var SEND_MOBILE_CODE_VERIFY_URL = "{{ url('api/web/ajax_mobile_code_verify') }}";// 发送手机验证码-验证

    </script>
    <script src="{{ asset('/js/web/DogTools/login.js') }}"  type="text/javascript"></script>

</body>
</html>
