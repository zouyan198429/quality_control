<!doctype html>
<html lang="en">
<head>
    <link rel="stylesheet" href="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/css/layui.css')}}" media="all">

    @include('web.QualityControl.layout_public.pagehead')
{{--  	<script type="text/javascript" src="{{asset('staticweb/js/jquery1.42.min.js')}}"></script>--}}
{{--  	<script type="text/javascript" src="{{asset('staticweb/js/jquery.SuperSlide.2.1.1.js')}}"></script>--}}
</head>
<body id="body-login" >
    @include('web.QualityControl.layout_public.header')
	<div id="main">
		<div class="login">
        <style>
            .login {
                width:1180px; margin:40px auto 20px auto;  padding:20px 20px; min-height: 500px;
            }
            .bd-right {
                .min-height: 480px; 
            }
            .layui-tab {
                margin:0;
            }
            .layui-tab-title li {
                height: 60px;
            }
            .layui-tab-title .layui-this {
                border:0;
                border-left:1px solid #eee;
                border-right: 1px solid #eee;
                border-bottom: 2px solid #06a;
                height: 58px;
                border-right:1px solid #eee;
                margin-left: -1px;
            }
            .layui-tab-title .layui-this:after {
                display: none;
            }
            .layui-tab-content {
                min-height: 400px;
            }
            .layui-tab-item {
                padding-top:30px;
            }
            input[type='text'].form-control {
                height: 36px;
            }
            input[type='password'].form-control {
                height: 36px;
            }
        </style>

			<div class="bd-left">
			</div>
			<div class="bd-right" style="background:#fff;">

                <div class="layui-tab login-tag hd">
                    <input type="hidden" name="form_type" value="user_login" />
                    <ul class="layui-tab-title">
                        <li class="layui-this"  data-type="user_login">个人登录</li>
                        <li data-type="compnay_login">企业登录</li>
                    </ul>
                    <div class="layui-tab-content">
                        <div class="layui-tab-item layui-show">
                            <div class="bd login-mm" style="width:360px; margin:0 auto;">

                            <ul>
                                <p>验证码登录</p>

                                <form class="am-form user_login" action="#"  method="post" >
                                    <div class="form-group layui-form-item">
                                        <input type="text" name="mobile" class="form-control" placeholder="输入手机号"   value="">
                                    </div>
                                    <div class="form-group layui-form-item">
                                        <input type="text" name="mobile_vercode" class="form-control fl" style="width:69.1%;" placeholder="验证码"   value="">
<!--                          <input type="text" name="text" class="form-control fr tc" style="width:34%;" placeholder="发送验证码"   value=""> 
 -->                                      <button type="button" class="layui-btn" id="LAY-user-getsmscode">获取验证码</button>
                                        <div class="c"></div>
                                    </div>
<!--                                      <div class="form-group layui-form-item">- -}}
                                         <label for="password" class="form-label">图形验证码</label>- -}}
                                         <div class="form-input">- -}}
                                             <input type="text" name="captcha_code"  lay-verify="required" placeholder="图形验证码" class="layui-input" style="width:100px;">- -}}
                                             <input type="hidden" name="captcha_key" />- -}}
                                             <img src="" class="layadmin-user-login-codeimg" id="LAY-user-get-vercode" >- -}}
                                         </div>- -}}
                                     </div>- -}}
 -->
                                    <a href="javascript:void(0);" class="btn btn-block submitBtn">登录</a>
                                </form>

                            </ul>
                            </div>

                        </div>
                        <div class="layui-tab-item">
                            <div class="bd login-mm" style="width:360px; margin:0 auto;">
                                <ul>
                                    <p>企业帐号登录</p>
                                    <form class="am-form compnay_login" action="#"  method="post" >
                                        <div class="form-group layui-form-item">
                                            <input type="text" name="admin_username" class="form-control" placeholder="输入帐号"   value="">
                                        </div>
                                        <div class="form-group layui-form-item">
                                            <input type="password" name="admin_password" class="form-control fl"   placeholder="输入密码"   value="">
                                            <div class="c"></div>
                                        </div>
                                        <div class="form-group layui-form-item">
<!--                                             <label for="password" class="form-label">图形验证码</label>
 -->                                            <div class="form-input">
                                                <input type="text" name="captcha_code" lay-verify="required" placeholder="图形验证码" class="layui-input" style="width:100px; display: inline-block;">
                                                <input type="hidden" name="captcha_key" />
                                                <img src="" class="layadmin-user-login-codeimg" id="LAY-user-get-vercode" >
                                            </div>
                                        </div>

                                        <a href="javascript:void(0);" class="btn btn-block submitBtn" >登录</a>
                                    </form>

                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
				<div class="fd tc">
					<a href="{{ url('web/reg') }}" >新用户注册</a>
					<div class="k20"></div>
				</div>
                <div class="c"></div>

			</div>
			<div class="c"></div>
		</div>
	</div>

{{--		<script type="text/javascript">jQuery(".bd-right").slide();</script>--}}
    @include('web.QualityControl.layout_public.footer')
</body>
</html>

<script src="{{asset('js/jquery-3.3.1.min.js')}}"></script>
<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.all.js')}}"></script>
@include('public.dynamic_list_foot')
<script>
    var CAPTCHA_IMG_CLASS = "layadmin-user-login-codeimg";
    // var CAPTCHA_IMG_ID = "LAY-user-get-vercode";
    var CAPTCHA_FORM_ITEM = "layui-form-item";
    var CAPTCHA_KEY_INPUT_NAME = "captcha_key";

    // 企业登录
    var COMPANY_LOGIN_URL = "{{ url('api/company/ajax_login') }}";
    var COMPANY_GET_CAPTCHA_IMG_URL = "{{ url('api/company/ajax_captcha') }}";
    var COMPANY_INDEX_URL = "{{url('company')}}";
    // 个人登录
    var USER_LOGIN_URL = "{{ url('api/user/ajax_login_sms') }}";
    var USER_GET_CAPTCHA_IMG_URL = "{{ url('api/user/ajax_captcha') }}";
    var USER_INDEX_URL = "{{url('user')}}";

    var CODE_TIME = 60 * 2;// 手机短信验证码有效期
    var SEND_MOBILE_CODE_URL = "{{ url('api/user/ajax_send_mobile_vercode') }}";// 发送手机验证码
    var SEND_MOBILE_CODE_VERIFY_URL = "{{ url('api/user/ajax_mobile_code_verify') }}";// 发送手机验证码-验证
</script>
<script src="{{ asset('/js/web/QualityControl/login.js') }}"  type="text/javascript"></script>
