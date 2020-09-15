

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
                <th>课程名称<span class="must">*</span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="real_name" value="{{ $info['real_name'] ?? '' }}" placeholder="请输入课程名称"/>
                </td>
            </tr>
            <tr>
                <th>图片</th>
                <td  class="layui-input-block">
                    <button style="float: left;" type="button" class="layui-btn" id="layuiadmin-upload-list">上传图片</button><input class="layui-upload-file" type="file" accept="image/*" name="file">
                </td>
            </tr>
            <tr>
                <th>简要概述</th>
                <td>
                    <input type="text" class="inp wnormal"  name="mobile" value="{{ $info['mobile'] ?? '' }}" placeholder="请输入简要概述" />
                </td>
            </tr>
            <tr>
                <th>详细内容</th>
                <td>
                    <input type="text" class="inp wnormal"  name="tel" value="{{ $info['tel'] ?? '' }}"/>
                </td>
            </tr>
            <tr>
                <th>收费标准(会员)</th>
                <td>
                    <input type="text" class="inp wnormal"  name="qq_number" value="{{ $info['qq_number'] ?? '' }}"/>
                </td>
            </tr>
            <tr>
                <th>收费标准(非会员)</th>
                <td>
                    <input type="text" class="inp wnormal"  name="admin_username" value="{{ $info['admin_username'] ?? '' }}"/>
                </td>
            </tr>
            <tr>
                <th>显示状态</th>
                <td>
                    <div class="layui-input-block">
                        <input type="radio" name="sex" value="上架" title="上架" checked>
                        <input type="radio" name="sex" value="下架" title="下架">
                    </div>
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
    var SAVE_URL = "{{ url('api/admin/staff/ajax_save') }}";// ajax保存记录地址
    var LIST_URL = "{{url('admin/staff')}}";//保存成功后跳转到的地址
</script>

</body>
</html>
