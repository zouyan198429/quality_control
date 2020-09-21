

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
<div class="mm">
    <form class="am-form am-form-horizontal" method="post"  id="addForm">
        <input type="hidden" name="id" value="{{ $info['id'] ?? 0 }}"/>
        <table class="table1">
            <tr>
                <th>班级名称<span class="must">*</span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="class_name" value="{{ $info['class_name'] ?? '' }}" placeholder="请输入班级名称"/>
                </td>
            </tr>
            <tr>
                <th>城市<span class="must">*</span></th>
                <td>
                    <select class="wnormal" name="city_id" style="width: 100px;">
                        <option value="">请选择城市</option>
                        @foreach ($cities as $k => $txt)
                            <option value="{{ $k }}"  @if(isset($defaultCity) && $defaultCity == $k) selected @endif >{{ $txt }}</option>
                        @endforeach
                    </select>
                </td>
            </tr>
            <tr>
                <th>备注<span class="must">*</span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="remarks" value="{{ $info['remarks'] ?? '' }}"/>
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
@include('public.dynamic_list_foot')

<script type="text/javascript">
    const SAVE_URL = "{{ url('api/admin/courses_class/save') }}";// ajax保存记录地址
    const LIST_URL = "{{url('admin/courses_class')}}";//保存成功后跳转到的地址
</script>
<script src="{{ asset('/js/admin/QualityControl/CourseClass_edit.js') }}"  type="text/javascript"></script>
</body>
</html>
