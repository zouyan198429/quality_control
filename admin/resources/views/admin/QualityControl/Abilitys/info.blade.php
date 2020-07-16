

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
        <table class="table1">
            <tr>
                <th>检测项目<span class="must"></span></th>
                <td>
                    {{ $info['ability_name'] ?? '' }}
                </td>
            </tr>
            <tr>
                <th>预估参加实验数<span class="must"></span></th>
                <td>
                    {{ $info['estimate_add_num'] ?? '' }}
                </td>
            </tr>
            <tr>
                <th>报名企业数<span class="must"></span></th>
                <td>
                    {{ $info['join_num'] ?? '' }}
                </td>
            </tr>
            <tr>
                <th>报名起止时间<span class="must"></span></th>
                <td>
                    {{ $info['join_begin_date'] ?? '' }}
                    -
                    {{ $info['join_end_date'] ?? '' }}
                </td>
            </tr>
            <tr>
                <th>数据提交时限<span class="must"></span></th>
                <td>
                    {{ $info['duration_minute'] ?? '' }}天
                </td>
            </tr>
            <tr>
                <th>方法标准<span class="must"></span></th>
                <td>
                    {!!  $info['project_standards_text'] ?? '' !!}
                </td>
            </tr>
            <tr>
                <th>验证数据项<span class="must"></span></th>
                <td>
                    {!!  $info['submit_items_text'] ?? '' !!}
                </td>
            </tr>

        </table>
</div>
<script type="text/javascript" src="{{asset('laydate/laydate.js')}}"></script>
<script src="{{asset('js/jquery-3.3.1.min.js')}}"></script>
<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.all.js')}}"></script>
{{--<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.js')}}"></script>--}}
@include('public.dynamic_list_foot')
<script type="text/javascript">

</script>
<script src="{{ asset('/js/admin/QualityControl/Abilitys_info.js?0') }}"  type="text/javascript"></script>
</body>
</html>
