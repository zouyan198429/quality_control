

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
        <input type="hidden" name="hidden_option"  value="{{ $hidden_option ?? 0 }}" />
        <input type="hidden" name="id" value="{{ $info['id'] ?? 0 }}"/>
        <table class="table1">
            <tr  @if (isset($hidden_option) && (($hidden_option & 2) == 2) ) style="display: none;"  @endif>
                <th>收款帐号<span class="must">*</span></th>
                <td>
                    @foreach ($pay_config_kv as $k=>$txt)
                        <label><input type="radio"  name="pay_config_id"  value="{{ $k }}"  @if(isset($defaultPayConfig) && $defaultPayConfig == $k) checked="checked"  @endif />{{ $txt }} </label>
                    @endforeach
                </td>
            </tr>
            <tr>
                <th>销售方名称<span class="must">*</span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="xsf_mc" value="{{ $info['xsf_mc'] ?? '' }}" placeholder="请输入销售方名称"/>
                </td>
            </tr>
            <tr>
                <th>销售方地址<span class="must"></span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="xsf_dz" value="{{ $info['xsf_dz'] ?? '' }}" placeholder="请输入销售方地址"/>
                </td>
            </tr>
            <tr>
                <th>销售方电话<span class="must"></span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="xsf_dh" value="{{ $info['xsf_dh'] ?? '' }}" placeholder="请输入销售方电话"/>
                </td>
            </tr>
            <tr>
                <th>销售方纳税人识别号<span class="must">*</span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="xsf_nsrsbh" value="{{ $info['xsf_nsrsbh'] ?? '' }}" placeholder="请输入销售方纳税人识别号"/>
                </td>
            </tr>
            <tr>
                <th>销售方银行<span class="must"></span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="xsf_yh" value="{{ $info['xsf_yh'] ?? '' }}" placeholder="请输入销售方银行"/>
                </td>
            </tr>
            <tr>
                <th>销售方银行账号<span class="must"></span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="xsf_yhzh" value="{{ $info['xsf_yhzh'] ?? '' }}" placeholder="请输入销售方银行账号"/>
                </td>
            </tr>
{{--            <tr>--}}
{{--                <th>开启状态<span class="must">*</span></th>--}}
{{--                <td class="sel_pay_method">--}}
{{--                    @foreach ($openStatus as $k=>$txt)--}}
{{--                        <label><input type="radio"  name="open_status"  value="{{ $k }}"  @if(isset($defaultOpenStatus) && $defaultOpenStatus == $k) checked="checked"  @endif />{{ $txt }} </label>--}}
{{--                    @endforeach--}}
{{--                </td>--}}
{{--            </tr>--}}
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

    // hidden_option 8192:调用父窗品的方法：[public/js目录下的] 项目目录+数据功能目录+当前文件名称 【有_线，则去掉】
    // 其它地方弹出此窗，保存完成时调用的父窗口方法名称 参数(obj:当前表单值对像, result:保存接口返回的结果，operateNum:自己定义的一个编号【页面有多处用到时用--通知父窗口调用位置】)
    var PARENT_BUSINESS_FUN_NAME = "adminQualityControlrrrddddedit";

    var SAVE_URL = "{{ url('api/admin/invoice_seller/ajax_save') }}";// ajax保存记录地址
    var LIST_URL = "{{url('admin/invoice_seller')}}";//保存成功后跳转到的地址
</script>
<script src="{{ asset('/js/admin/QualityControl/InvoiceSeller_edit.js') }}?1"  type="text/javascript"></script>
</body>
</html>
