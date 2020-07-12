<!doctype html>
<html lang="en">
<head>
    @include('web.QualityControl.layout_public.pagehead')
</head>
<body style=" background:#eee; ">
    @include('web.QualityControl.layout_public.header')
	<div id="main">
		<div class="reg" style="width:980px; margin:40px auto 20px auto; border:1px solid #eee; min-height:500px;  padding:20px 20px; background:#fff;  ">

			<div class="hd tc" style="padding:30px 0;">
				<h2>完善个人资料</h2>
			</div>
            <form class="am-form am-form-horizontal" method="post"  id="addForm">
                <input type="hidden" name="id" value="{{ $info['id'] ?? 0 }}"/>
			<div class="bd" style="width:800px; margin:0 auto;">
				<p>带 <span class="red">*</span> 为必填项</p> <br>
				<div class="form-item">
				    <label for="username" class="form-label"> 单位名称 <span class="red">*</span> </label>
				    <div class="form-input">
                        <input type="hidden" name="company_id"  value="{{ $info['company_id'] ?? '' }}" />
                        <span class="company_name">{{ $info['user_company_name'] ?? '' }}</span>
{{--                        <button  type="button"  class="btn btn-danger  btn-xs ace-icon fa fa-plus-circle bigger-60"  onclick="otheraction.selectShop(this)">选择所属店铺</button>--}}
{{--						<input type="text" name="username" class="form-control" autocomplete="off" value="">--}}
						<button   type="button" class="btn btn-gray"   onclick="otheraction.selectCompany(this)">选择所属企业</button>
					</div>
				</div>
				<div class="form-item">
                    <label for="username" class="form-label"> 姓名 <span class="red">*</span> </label>
                    <div class="form-input">
                    	<input type="text" name="real_name" autocomplete="off" value="{{ $info['real_name'] ?? '' }}">
                    </div>
                </div>
                <div class="form-item">
                    <label for="username" class="form-label"> 性别 <span class="red">*</span> </label>
                    <div class="form-input">
                        <label><input type="radio" name="sex" value="1" @if (isset($info['sex']) && $info['sex'] == 1 ) checked @endif>男</label>&nbsp;&nbsp;&nbsp;&nbsp;
                        <label><input type="radio" name="sex" value="2" @if (isset($info['sex']) && $info['sex'] == 2 ) checked @endif>女</label>
                    </div>
                </div>

                 <div class="form-item">
                    <label for="text" class="form-label">邮箱<span class="red">*</span> </label>
                    <div class="form-input">
                    <input type="text" name="email" autocomplete="off" value="{{ $info['email'] ?? '' }}">
                    <p class="gray">用于接收通知等。</p>
                    </div>
                </div>
                <div class="form-item">
                    <label for="text" class="form-label">手机号 <span class="red">*</span></label>
                    <div class="form-input">
                    <input type="text" name="mobile" autocomplete="off" value="{{ $info['mobile'] ?? '' }}">
                    </div>
                </div>

                <div class="form-item">
                    <label for="text" class="form-label">微信号<span class="red">*</span></label>
                    <div class="form-input">
                    <input type="text" name="qq_number" autocomplete="off" value="{{ $info['qq_number'] ?? '' }}">
                    </div>
                </div>
                <div class="form-item">
                    <label for="text" class="form-label">身份证号<span class="red">*</span></label>
                    <div class="form-input">
                    <input type="text" name="id_number" autocomplete="off" value="{{ $info['id_number'] ?? '' }}">
                    </div>
                </div>
                <div class="form-item">
                    <label for="text" class="form-label">所在城市 <span class="red">*</span> </label>
                    <div class="form-input">
                    <select class="form-control"  name="city_id">
					  <option value="">请选择</option>
                        @foreach ($citys_kv as $k=>$txt)
                            <option value="{{ $k }}"  @if(isset($defaultCity) && $defaultCity == $k) selected @endif >{{ $txt }}</option>
                        @endforeach
					</select>
					</div>

                </div>
                <div class="form-item">
                    <label for="text" class="form-label">通讯地址</label>
                    <div class="form-input">
	                    <input type="text" name="addr" autocomplete="off" value="{{ $info['addr'] ?? '' }}">
	                </div>
                </div>
                <div class="k20"></div>
                <div class="form-item">
                    <label for="password" class="form-label"></label>
                	<div class="form-input">
	                	<a href="javascript:void(0);" class="btn btn-default btn-block"   id="submitBtn">提交</a>
	                </div>
                </div>
                <div class="k20"></div>

			</div>
            </form>
			<div class="c"></div>
		</div>
	</div>
    @include('web.QualityControl.layout_public.footer')
</body>
</html>
<script src="{{asset('js/jquery-3.3.1.min.js')}}"></script>
<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.all.js')}}"></script>
{{--<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.js')}}"></script>--}}
@include('public.dynamic_list_foot')

<script type="text/javascript">
    var SAVE_URL = "{{ url('api/web/ajax_perfect_user') }}";// ajax保存记录地址
    var LOG_OUT_URL = "{{url('web/logout')}}";//保存成功后跳转到的地址

    var SELECT_COMPANY_URL = "{{url('web/select_company')}}";// 选择所属企业
</script>
<script src="{{ asset('/js/web/QualityControl/perfect_user.js') }}"  type="text/javascript"></script>

