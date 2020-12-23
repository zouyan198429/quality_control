

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
                <th>公布类型<span class="must">*</span></th>
                <td  class="layui-input-block">
                    <label><input type="radio" name="publish_type" value="2" @if (isset($info['publish_type']) && $info['publish_type'] == 2 ) checked @endif>立即公布</label>&nbsp;&nbsp;&nbsp;&nbsp;
                    <label><input type="radio" name="publish_type" value="4" @if (isset($info['publish_type']) && $info['publish_type'] == 4 ) checked @endif>指定时间</label>

                    <input type="text" class="inp wlong publish_time" name="publish_time" value="{{ $info['publish_time'] ?? '' }}" placeholder="请选择公布结果时间" style="width: 150px;"  readonly="true"/>

                </td>
            </tr>
{{--            <tr>--}}
{{--                <th>公布结果时间<span class="must">*</span></th>--}}
{{--                <td>--}}
{{--                </td>--}}
{{--            </tr>--}}
            <tr>
                <th> </th>
                <td>
                    <button class="btn btn-l wnormal"  id="submitBtn" >提交</button>
                </td>
            </tr>

        </table>
    </form>
</div>
<script type="text/javascript" src="{{asset('laydate/laydate.js')}}"></script>
<script src="{{asset('js/jquery-3.3.1.min.js')}}"></script>
<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.all.js')}}"></script>
{{--<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.js')}}"></script>--}}
@include('public.dynamic_list_foot')
<script type="text/javascript">
    var SAVE_URL = "{{ url('api/admin/abilitys/ajax_save_publish') }}";// ajax保存记录地址
    var LIST_URL = "{{url('admin/abilitys')}}";//保存成功后跳转到的地址

    var PUBLISH_TIME = "{{ $info['publish_time'] ?? '' }}" ;//报名开始时间

</script>
<script src="{{ asset('/js/admin/QualityControl/Abilitys_publish.js') }}?5"  type="text/javascript"></script>
</body>
</html>
