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
				<h2>完善企业资料</h2>
			</div>

            <form class="am-form am-form-horizontal" method="post"  id="addForm">
                <input type="hidden" name="id" value="{{ $info['id'] ?? 0 }}"/>
			<div class="bd" style="width:800px; margin:0 auto;">
				<p>带 <span class="red">*</span> 为必填项</p> <br>
				<div class="form-item">
                    <label for="username" class="form-label"> 单位名称 <span class="red">*</span> </label>
                    <div class="form-input"><input type="text" name="company_name" class="form-control" autocomplete="off" value="{{ $info['company_name'] ?? '' }}"></div>
                </div>
				<div class="form-item">
				    <label for="username" class="form-label"> 统一社会信用代码 <span class="red">*</span> </label>
				    <div class="form-input"><input type="text" name="company_credit_code" class="form-control" autocomplete="off" value="{{ $info['company_credit_code'] ?? '' }}"></div>
				</div>

				<div class="form-item company_is_legal_persion">
				    <label for="username" class="form-label"> 是否独立法人：  </label>
				    <div class="form-input"><input type="checkbox" name="company_is_legal_persion" class="form-control" autocomplete="off" value="1">非独立法人 <span class="gray">企业类型为非独立法人时请填写主体单位信息</span></div>

				</div>
				<div class="form-item company_is_legal_persion_item">
                    <label for="username" class="form-label"> 主体机构统一社会信用代码   </label>
                    <div class="form-input"><input type="text" name="company_legal_credit_code" class="form-control" autocomplete="off" value="{{ $info['company_legal_credit_code'] ?? '' }}"></div>
                </div>
				<div class="form-item company_is_legal_persion_item">
				    <label for="username" class="form-label"> 主体机构  </label>
				    <div class="form-input"><input type="text" name="company_legal_name" class="form-control" autocomplete="off" value="{{ $info['company_legal_name'] ?? '' }}"></div>
				</div>

				<hr>
				<div class="k20"></div>

				<div class="form-item">
				    <label for="text" class="form-label">所在城市 <span class="red">*</span> </label>
				    <div class="form-input">
				    	<select class="form-control" name="city_id">
						  <option value="">请选择</option>
                            @foreach ($citys_kv as $k=>$txt)
                            <option value="{{ $k }}"  @if(isset($defaultCity) && $defaultCity == $k) selected @endif >{{ $txt }}</option>
                            @endforeach
						</select>

					</div>
				</div>

				<div class="form-item">
				    <label for="text" class="form-label">企业类型 <span class="red">*</span> </label>
				    <div class="form-input">
						 <input type="radio" name="company_type" value="1" title="检测机构" @if (isset($info['company_type']) && $info['company_type'] == 1 ) checked @endif>检测机构
						 <input type="radio" name="company_type" value="2" title="生产企业" @if (isset($info['company_type']) && $info['company_type'] == 2 ) checked @endif>生产企业
					</div>
				</div>
				<div class="form-item">
				    <label for="text" class="form-label">企业性质 <span class="red">*</span> </label>
				    <div class="form-input">
						<select name="company_prop" id="drpNature" class="ipt"  style="width: 360px;">
							<option value="">请选择</option>
                            @foreach ($companyProp as $k=>$txt)
                                <option value="{{ $k }}"  @if(isset($defaultCompanyProp) && $defaultCompanyProp == $k) selected @endif >{{ $txt }}</option>
                            @endforeach

						</select>
					</div>
				</div>


                <div class="form-item">
                    <label for="text" class="form-label">通讯地址 <span class="red">*</span> </label>
                    <div class="form-input"><input type="text" name="addr" class="form-control" autocomplete="off" value="{{ $info['addr'] ?? '' }}"></div>
                </div>
				<div class="form-item">
				    <label for="text" class="form-label">邮编</label>
				    <div class="form-input"><input type="text" name="zip_code" class="form-control" autocomplete="off" value="{{ $info['zip_code'] ?? '' }}"></div>
				</div>
				<div class="form-item">
				    <label for="text" class="form-label">传真</label>
				    <div class="form-input"><input type="text" name="fax" class="form-control" autocomplete="off" value="{{ $info['fax'] ?? '' }}"></div>
				</div>
                 <div class="form-item">
                    <label for="text" class="form-label">企业邮箱<span class="red">*</span> </label>
                    <div class="form-input">
                    	<input type="text" name="email" class="form-control" autocomplete="off" value="{{ $info['email'] ?? '' }}">
                    	<p class="gray">用于接收通知等。</p>
                    </div>
                </div>
                <div class="form-item">
                    <label for="text" class="form-label">法人代表 <span class="red">*</span></label>
                    <div class="form-input"><input type="text" name="company_legal" class="form-control" autocomplete="off" value="{{ $info['company_legal'] ?? '' }}"></div>
                </div>
				<div class="form-item">
				    <label for="text" class="form-label">营业执照 <span class="red">*</span></label>
				    <div class="form-input"><input type="file" name="text" class="form-control" autocomplete="off" value="{{ $info['aaaa'] ?? '' }}"></div>
				</div>
                <div class="form-item">
                    <label for="text" class="form-label">单位人数 <span class="red">*</span> </label>
                    <select class="form-control" style="width: 360px;"  name="company_peoples_num">
					  <option value="">请选择</option>
                        @foreach ($companyPeoples as $k=>$txt)
                            <option value="{{ $k }}"  @if(isset($defaultCompanyPeoples) && $defaultCompanyPeoples == $k) selected @endif >{{ $txt }}</option>
                        @endforeach
					</select>
                </div>
                <div class="form-item">
                    <label for="text" class="form-label">所属行业 <span class="red">*</span> </label>
                    <select class="form-control" style="width: 360px;"   name="company_industry_id">
					  <option value="">请选择</option>
                        @foreach ($industry_kv as $k=>$txt)
                            <option value="{{ $k }}"  @if(isset($defaultIndustry) && $defaultIndustry == $k) selected @endif >{{ $txt }}</option>
                        @endforeach
					</select>
                </div>
                <div class="form-item">
                    <label for="text" class="form-label">证书编号 <span class="red">*</span></label>
                    <div class="form-input"><input type="text" name="company_certificate_no" class="form-control" autocomplete="off" value="{{ $info['company_certificate_no'] ?? '' }}"></div>
                </div>
                <div class="form-item">
                    <label for="text" class="form-label">联系人<span class="red">*</span></label>
                    <div class="form-input"><input type="text" name="company_contact_name" class="form-control" autocomplete="off" value="{{ $info['company_contact_name'] ?? '' }}"></div>
                </div>
				<div class="form-item">
				    <label for="text" class="form-label">联系人手机<span class="red">*</span></label>
				    <div class="form-input"><input type="text" name="company_contact_mobile" class="form-control" autocomplete="off" value="{{ $info['company_contact_mobile'] ?? '' }}"></div>
				</div>
                <div class="form-item">
                    <label for="text" class="form-label">联系电话<span class="red">*</span></label>
                    <div class="form-input"><input type="text" name="company_contact_tel" class="form-control" autocomplete="off" value="{{ $info['company_contact_tel'] ?? '' }}"></div>
                </div>
				<div class="form-item read_and_agree">
				    <label for="text" class="form-label"> </label>
				    <div class="form-input"><input type="checkbox" name="read_and_agree" autocomplete="off" value="1">我已阅读并同意<a href="" class="blue">注册服务协议</a></div>
				</div>
                <div class="k20"></div>
                <a href="javascript:void(0);" class="btn btn-default btn-block"   id="submitBtn">提交</a>
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
    var SAVE_URL = "{{ url('api/web/ajax_perfect_company') }}";// ajax保存记录地址
    var LOG_OUT_URL = "{{url('web/logout')}}";//保存成功后跳转到的地址
</script>
<script src="{{ asset('/js/web/QualityControl/perfect_company.js') }}"  type="text/javascript"></script>
