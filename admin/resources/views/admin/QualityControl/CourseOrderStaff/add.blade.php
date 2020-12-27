

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
            <tr>
                <th>课程<span class="must"></span></th>
                <td>
                    {{ $info['course_name'] ?? '' }}
                </td>
            </tr>
            <tr>
                <th>单位<span class="must"></span></th>
                <td>
                    {{ $info['company_name'] ?? '' }}
                </td>
            </tr>
            <tr>
                <th>证书所属单位<span class="must">*</span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="certificate_company" value="{{ $info['certificate_company'] ?? '' }}" placeholder="请输入证书所属单位"/>
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
    var SAVE_URL = "{{ url('api/admin/course_order_staff/ajax_save') }}";// ajax保存记录地址
    var LIST_URL = "{{url('admin/course_order_staff')}}";//保存成功后跳转到的地址
</script>
<script src="{{ asset('/js/admin/QualityControl/CourseOrderStaff_edit.js') }}?1"  type="text/javascript"></script>
</body>
</html>
