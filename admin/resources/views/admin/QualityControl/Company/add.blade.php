

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>开启头部工具栏 - 数据表格</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    @include('admin.layout_public.pagehead')
    <link rel="stylesheet" href="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/css/layui.css')}}" media="all">
    <link rel="stylesheet" href="{{asset('layui-admin-v1.2.1/src/layuiadmin/style/admin.css')}}" media="all">
</head>
<body>

{{--<div id="crumb"><i class="fa fa-reorder fa-fw" aria-hidden="true"></i> {{ $operate ?? '' }}员工</div>--}}
<div class="mm">
    <form class="am-form am-form-horizontal" method="post"  id="addForm">
        <input type="hidden" name="id" value="{{ $info['id'] ?? 0 }}"/>
        <table class="table1">
            <tr>
                <th>单位名称<span class="must">*</span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="company_name" value="{{ $info['company_name'] ?? '' }}" placeholder="请输入单位名称"/>
                </td>
            </tr>
            <tr>
                <th>统一社会信用代码<span class="must">*</span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="company_credit_code" value="{{ $info['company_credit_code'] ?? '' }}" placeholder="请输入统一社会信用代码"/>
                </td>
            </tr>
            <tr>
                <th>是否独立法人<span class="must"></span></th>
                <td>
                    <label class="company_is_legal_persion"><input type="checkbox"  name="company_is_legal_persion"  value="1"  @if(isset($info['company_is_legal_persion']) && $info['company_is_legal_persion'] == 1) checked="checked"  @endif />是否独立法人</label><span class="gray">企业类型为非独立法人时请填写主体单位信息</span>
                </td>
            </tr>
            <tr class="company_is_legal_persion_item">
                <th>主体机构统一社会信用代码<span class="must"></span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="company_legal_credit_code" value="{{ $info['company_legal_credit_code'] ?? '' }}" placeholder="请输入主体机构统一社会信用代码"/>
                </td>
            </tr>
            <tr class="company_is_legal_persion_item">
                <th>主体机构<span class="must"></span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="company_legal_name" value="{{ $info['company_legal_name'] ?? '' }}" placeholder="请输入主体机构"/>
                </td>
            </tr>
            <tr>
                <th>所在城市<span class="must">*</span></th>
                <td>
                    <select class="wnormal" name="city_id" style="width: 100px;">
                        <option value="">请选择城市</option>
                        @foreach ($citys_kv as $k=>$txt)
                            <option value="{{ $k }}"  @if(isset($defaultCity) && $defaultCity == $k) selected @endif >{{ $txt }}</option>
                        @endforeach
                    </select>
                </td>
            </tr>
            <tr>
                <th>企业类型<span class="must">*</span></th>
                <td>
                    <label><input type="radio" name="company_type" value="1" @if (isset($info['company_type']) && $info['company_type'] == 1 ) checked @endif>检测机构</label>&nbsp;&nbsp;&nbsp;&nbsp;
                    <label><input type="radio" name="company_type" value="2" @if (isset($info['company_type']) && $info['company_type'] == 2 ) checked @endif>生产企业</label>

                </td>
            </tr>
            <tr>
                <th>企业性质<span class="must">*</span></th>
                <td>
                    <select class="wnormal" name="company_prop" style="width: 100px;">
                        <option value="">请选择企业性质</option>
                        @foreach ($companyProp as $k=>$txt)
                            <option value="{{ $k }}"  @if(isset($defaultCompanyProp) && $defaultCompanyProp == $k) selected @endif >{{ $txt }}</option>
                        @endforeach
                    </select>
                </td>
            </tr>
            <tr>
                <th>通讯地址<span class="must">*</span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="addr" value="{{ $info['addr'] ?? '' }}" placeholder="请输入通讯地址"/>
                </td>
            </tr>
            <tr>
                <th>邮编<span class="must"></span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="zip_code" value="{{ $info['zip_code'] ?? '' }}" placeholder="请输入邮编"/>
                </td>
            </tr>
            <tr>
                <th>传真<span class="must"></span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="fax" value="{{ $info['fax'] ?? '' }}" placeholder="请输入传真"/>
                </td>
            </tr>
            <tr>
                <th>企业邮箱<span class="must"></span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="email" value="{{ $info['email'] ?? '' }}" placeholder="请输入企业邮箱"/>
                    <p class="gray">用于接收通知等。</p>
                </td>
            </tr>
            <tr>
                <th>法人代表<span class="must">*</span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="company_legal" value="{{ $info['company_legal'] ?? '' }}" placeholder="请输入法人代表"/>
                </td>
            </tr>
            <tr>
                <th>营业执照<span class="must">*</span></th>
                <td>

                </td>
            </tr>
            <tr>
                <th>单位人数<span class="must">*</span></th>
                <td>

                    <select class="wnormal" name="company_peoples_num" style="width: 100px;">
                        <option value="">请选择单位人数</option>
                        @foreach ($companyProp as $k=>$txt)
                            <option value="{{ $k }}"  @if(isset($defaultCompanyProp) && $defaultCompanyProp == $k) selected @endif >{{ $txt }}</option>
                        @endforeach
                    </select>
                </td>
            </tr>
            <tr>
                <th>所属行业<span class="must">*</span></th>
                <td>
                    <select class="wnormal" name="company_industry_id" style="width: 100px;">
                        <option value="">请选择行业</option>
                        @foreach ($industry_kv as $k=>$txt)
                            <option value="{{ $k }}"  @if(isset($defaultIndustry) && $defaultIndustry == $k) selected @endif >{{ $txt }}</option>
                        @endforeach
                    </select>
                </td>
            </tr>
            <tr>
                <th>证书编号<span class="must">*</span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="company_certificate_no" value="{{ $info['company_certificate_no'] ?? '' }}" placeholder="请输入证书编号"/>
                </td>
            </tr>
            <tr>
                <th>联系人<span class="must">*</span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="company_contact_name" value="{{ $info['company_contact_name'] ?? '' }}" placeholder="请输入联系人"/>
                </td>
            </tr>
            <tr>
                <th>联系人手机<span class="must">*</span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="company_contact_mobile" value="{{ $info['company_contact_mobile'] ?? '' }}" placeholder="请输入联系人手机"/>
                </td>
            </tr>
            <tr>
                <th>联系电话<span class="must">*</span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="company_contact_tel" value="{{ $info['company_contact_tel'] ?? '' }}" placeholder="请输入联系电话"/>
                </td>
            </tr>
            <tr>
                <th>用户名<span class="must">*</span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="admin_username" value="{{ $info['admin_username'] ?? '' }}" placeholder="请输入用户名"/>

                </td>
            </tr>
            <tr>
                <th>登录密码<span class="must">*</span></th>
                <td>
                    <input type="password"  class="inp wnormal"   name="admin_password" placeholder="登录密码" /><p>修改时，可为空：不修改密码。</p>
                </td>
            </tr>
            <tr>
                <th>确认密码<span class="must">*</span></th>
                <td>
                    <input type="password" class="inp wnormal"     name="sure_password"  placeholder="确认密码"/><p>修改时，可为空：不修改密码。</p>
                </td>
            </tr>
            <tr>
                <th>是否完善资料<span class="must">*</span></th>
                <td>
                    @foreach ($isPerfect as $k=>$txt)
                        <label><input type="radio"  name="is_perfect"  value="{{ $k }}"  @if(isset($defaultIsPerfect) && $defaultIsPerfect == $k) checked="checked"  @endif />{{ $txt }} </label>
                    @endforeach
                </td>
            </tr>
            <tr>
                <th> </th>
                <td><button class="btn btn-l wnormal"  id="submitBtn" >提交</button></td>
            </tr>

        </table>
    </form>
</div>
<script src="{{asset('js/jquery-3.3.1.min.js')}}"></script>
<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.all.js')}}"></script>
{{--<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.js')}}"></script>--}}
@include('public.dynamic_list_foot')

<script type="text/javascript">
    var SAVE_URL = "{{ url('api/admin/company/ajax_save') }}";// ajax保存记录地址
    var LIST_URL = "{{url('admin/company')}}";//保存成功后跳转到的地址
</script>
<script src="{{ asset('/js/admin/QualityControl/Company_edit.js') }}"  type="text/javascript"></script>
</body>
</html>
