

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
                <th>检测项目<span class="must">*</span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="ability_name" value="{{ $info['ability_name'] ?? '' }}" placeholder="请输入检测项目"/>
                </td>
            </tr>
            <tr>
                <th>预估参加实验数<span class="must">*</span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="estimate_add_num" value="{{ $info['estimate_add_num'] ?? '' }}" placeholder="请输入预估参加实验数"  onkeyup="isnum(this) " onafterpaste="isnum(this)"  />
                </td>
            </tr>
            <tr>
                <th>报名起止时间</th>
                <td>
                    <input type="text" class="inp wlong join_begin_date" name="join_begin_date" value="{{ $info['join_begin_date'] ?? '' }}" placeholder="请选择开始时间" style="width: 150px;"  readonly="true"/>
                    -
                    <input type="text" class="inp wlong join_end_date" name="join_end_date" value="{{ $info['join_end_date'] ?? '' }}" placeholder="请选择结束时间"  style="width: 150px;" readonly="true"/>
                </td>
            </tr>
            <tr>
                <th> </th>
                <td>

                    <div class="tags" id="tags">
                        <input type="text" name="" id="inputTags" placeholder="回车生成标签" autocomplete="off">
                    </div>
                    <div class="tags" id="tagsaaa">
                        <input type="text" name="" id="inputTagsaaa" placeholder="回车生成标签" autocomplete="off">
                    </div>
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
<link rel="stylesheet" href="{{asset('layui/extend/inputTags/inputTags.css')}}" media="all">
<script type="text/javascript" src="{{asset('layui/extend/inputTags/inputTags.js')}}"></script>
@include('public.dynamic_list_foot')
<script type="text/javascript">
    var SAVE_URL = "{{ url('api/admin/abilitys/ajax_save') }}";// ajax保存记录地址
    var LIST_URL = "{{url('admin/abilitys')}}";//保存成功后跳转到的地址

    var BEGIN_TIME = "{{ $info['join_begin_date'] ?? '' }}" ;//报名开始时间
    var END_TIME = "{{ $info['join_end_date'] ?? '' }}" ;//报名截止时间

</script>
<script src="{{ asset('/js/admin/QualityControl/Abilitys_edit.js') }}"  type="text/javascript"></script>
</body>
</html>
