

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
{{--            <tr>--}}
{{--                <th>帐号管理名称<span class="must">*</span></th>--}}
{{--                <td>--}}
{{--                    <input type="text" class="inp wnormal"  name="type_name" value="{{ $info['type_name'] ?? '' }}" placeholder="请输入帐号管理名称"/>--}}
{{--                </td>--}}
{{--            </tr>--}}
{{--            <tr>--}}
{{--                <th>排序[降序]<span class="must">*</span></th>--}}
{{--                <td>--}}
{{--                    <input type="text" class="inp wnormal"  name="sort_num" value="{{ $info['sort_num'] ?? '' }}" placeholder="请输入排序"  onkeyup="isnum(this) " onafterpaste="isnum(this)"  />--}}
{{--                </td>--}}
{{--            </tr>--}}
            <tr  @if (isset($company_hidden) && $company_hidden == 1 ) style="display: none;"  @endif>
                <th>所属企业<span class="must">*</span></th>
                <td>
                    <input type="hidden" name="company_id"  value="{{ $info['company_id'] ?? '' }}" />
                    <span class="company_name">{{ $info['user_company_name'] ?? '' }}</span>
                    <button  type="button"  class="btn btn-danger  btn-xs ace-icon fa fa-plus-circle bigger-60"  onclick="otheraction.selectCompany(this)">选择所属企业</button>
                </td>
            </tr>
            <tr>
                <th>姓名<span class="must">*</span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="real_name" value="{{ $info['real_name'] ?? '' }}" placeholder="请输入姓名"/>
                </td>
            </tr>
            <tr>
                <th>性别<span class="must">*</span></th>
                <td  class="layui-input-block">
                    <label><input type="radio" name="sex" value="1" @if (isset($info['sex']) && $info['sex'] == 1 ) checked @endif>男</label>&nbsp;&nbsp;&nbsp;&nbsp;
                    <label><input type="radio" name="sex" value="2" @if (isset($info['sex']) && $info['sex'] == 2 ) checked @endif>女</label>
                </td>
            </tr>
            <tr>
                <th>邮箱<span class="must"></span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="email" value="{{ $info['email'] ?? '' }}" placeholder="请输入邮箱"  />
                </td>
            </tr>
{{--            <tr>--}}
{{--                <th>状态<span class="must">*</span></th>--}}
{{--                <td  class="layui-input-block">--}}
{{--                    <label><input type="radio" name="account_status" value="1" @if (isset($info['account_status']) && $info['account_status'] == 1 ) checked @endif>正常</label>&nbsp;&nbsp;&nbsp;&nbsp;--}}
{{--                    <label><input type="radio" name="account_status" value="2" @if (isset($info['account_status']) && $info['account_status'] == 2 ) checked @endif>冻结</label>--}}
{{--                </td>--}}
{{--            </tr>--}}
            <tr>
                <th>手机<span class="must">*</span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="mobile" value="{{ $info['mobile'] ?? '' }}" placeholder="请输入手机"  onkeyup="isnum(this) " onafterpaste="isnum(this)"  />
                </td>
            </tr>
            <tr>
                <th>QQ\微信<span class="must"></span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="qq_number" value="{{ $info['qq_number'] ?? '' }}" placeholder="请输入QQ\email\微信" />
                </td>
            </tr>
            <tr>
                <th>身份证号<span class="must"></span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="id_number" value="{{ $info['id_number'] ?? '' }}" placeholder="请输入身份证号"  />
                </td>
            </tr>
            <tr>
                <th>职位<span class="must"></span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="position_name" value="{{ $info['position_name'] ?? '' }}" placeholder="请输入职位"  />
                </td>
            </tr>
            <tr>
                <th>所在城市<span class="must">*</span></th>
                <td>
                    <select class="wnormal" name="city_id">
                        <option value="">请选择城市</option>
                        @foreach ($citys_kv as $k=>$txt)
                            <option value="{{ $k }}"  @if(isset($defaultCity) && $defaultCity == $k) selected @endif >{{ $txt }}</option>
                        @endforeach
                    </select>
                </td>
            </tr>
            <tr>
                <th>通讯地址<span class="must"></span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="addr" value="{{ $info['addr'] ?? '' }}" placeholder="请输入地址"  />
                </td>
            </tr>
            <tr>
                <th>角色<span class="must"></span></th>
                <td  class="seledRoleNumIds">
                    @foreach ($roleNum as $k=>$txt)
                        <label><input type="checkbox"  name="role_nums[]"  value="{{ $k }}"  @if(isset($defaultRoleNum) && (($defaultRoleNum & $k) == $k)) checked="checked"  @endif />{{ $txt }} </label>
                    @endforeach
                </td>
            </tr>
            <tr id="tr_sign">
                <th>签字范围<span class="must">*</span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="sign_range" value="{{ $info['sign_range'] ?? '' }}" placeholder="请输入签字范围"  />
                    <label><input type="checkbox"  name="sign_is_food"  value="1"  @if(isset($info['sign_is_food']) && ($info['sign_is_food'] == 1)) checked="checked"  @endif /> 食品 </label>
                </td>
            </tr>
{{--            <tr>--}}
{{--                <th>用户名<span class="must">*</span></th>--}}
{{--                <td>--}}
{{--                    <input type="text" class="inp wnormal"  name="admin_username" value="{{ $info['admin_username'] ?? '' }}" placeholder="请输入用户名"/>--}}
{{--                </td>--}}
{{--            </tr>--}}
{{--            <tr>--}}
{{--                <th>登录密码<span class="must">*</span></th>--}}
{{--                <td>--}}
{{--                    <input type="password"  class="inp wnormal"   name="admin_password" placeholder="登录密码" />修改时，可为空，不修改密码。--}}
{{--                </td>--}}
{{--            </tr>--}}
{{--            <tr>--}}
{{--                <th>确认密码<span class="must">*</span></th>--}}
{{--                <td>--}}
{{--                    <input type="password" class="inp wnormal"     name="sure_password"  placeholder="确认密码"/>修改时，可为空，不修改密码。--}}
{{--                </td>--}}
{{--            </tr>--}}
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
    var SAVE_URL = "{{ url('api/admin/user/ajax_save') }}";// ajax保存记录地址
    var LIST_URL = "{{url('admin/user')}}";//保存成功后跳转到的地址

    var SELECT_COMPANY_URL = "{{url('admin/company/select')}}";// 选择所属企业
</script>
<script src="{{ asset('/js/admin/QualityControl/User_edit.js') }}"  type="text/javascript"></script>
</body>
</html>
