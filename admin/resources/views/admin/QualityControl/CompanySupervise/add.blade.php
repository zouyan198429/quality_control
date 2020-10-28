

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
    {{--  本页单独使用 --}}
    <script src="{{asset('dist/lib/kindeditor/kindeditor.min.js')}}"></script>
</head>
<body>

{{--<div id="crumb"><i class="fa fa-reorder fa-fw" aria-hidden="true"></i> {{ $operate ?? '' }}员工</div>--}}
<div class="mm">
    <form class="am-form am-form-horizontal" method="post"  id="addForm">
        <input type="hidden" name="id" value="{{ $info['id'] ?? 0 }}"/>
        <table class="table1">
            <tr  @if (isset($company_hidden) && $company_hidden == 1 ) style="display: none;"  @endif>
                <th>所属企业<span class="must">*</span></th>
                <td>
                    <input type="hidden" class="select_id"  name="company_id"  value="{{ $info['company_id'] ?? '' }}" />
                    <span class="select_name company_name">{{ $info['user_company_name'] ?? '' }}</span>
                    <i class="close select_close company_id_close"  onclick="clearSelect(this)" style="display: none;">×</i>
                    <button  type="button"  class="btn btn-danger  btn-xs ace-icon fa fa-plus-circle bigger-60"  onclick="otheraction.selectCompany(this)">选择所属企业</button>
                </td>
            </tr>
            <tr>
                <th>监督检查信息<span class="must">*</span></th>
                <td>
                    <textarea class="kindeditor" name="company_content" rows="15" id="doc-ta-1" style=" width:770px;height:400px;">{!!  htmlspecialchars($info['company_content'] ?? '' )   !!}</textarea>
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
    var SAVE_URL = "{{ url('api/admin/company_supervise/ajax_save') }}";// ajax保存记录地址
    var LIST_URL = "{{url('admin/company_supervise')}}";//保存成功后跳转到的地址

    var SELECT_COMPANY_URL = "{{url('admin/company/select')}}";// 选择所属企业
</script>
<script src="{{ asset('/js/admin/QualityControl/CompanySupervise_edit.js') }}"  type="text/javascript"></script>
</body>
</html>