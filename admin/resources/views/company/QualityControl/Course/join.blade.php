

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
                <th>培训名称<span class="must"></span></th>
                <td>
                    {{ $info['course_name'] ?? '' }}
                </td>
            </tr>
            <tr>
                <th>选择学员<span class="must"></span></th>
                <td>
                    <table cellspacing="0" cellpadding="0" lay-size="sm" lay-skin="line" border="0" class="layui-table" style="width:640px;" id="dynamic-table">
                        <colgroup>
                            <col width="60">
                            <col width="">
                            <col width="120">
                            <col width="160">
                            <col width="220">
                            <col>
                        </colgroup>
                        <thead>
                        <tr>
                            <th data-field="id" data-key="2-0-1" class=" layui-unselect">
                                <div class="layui-table-cell laytable-cell-2-0-1">
                                    <label class="pos-rel">
                                        <input type="checkbox" class="ace check_all" value="" onclick="otheraction.seledAll(this)">
                                        <span>全选</span>
                                    </label>
                                </div>
                            </th>
                            <th data-field="id" data-key="2-0-1" class=" layui-unselect">
                                <div class="layui-table-cell laytable-cell-2-0-1">
                                    <span>姓名</span>
                                </div>
                            </th>
                            <th data-field="id" data-key="2-0-1" class=" layui-unselect">
                                <div class="layui-table-cell laytable-cell-2-0-1">
                                    <span>性别</span>
                                </div>
                            </th>
                            <th data-field="id" data-key="2-0-1" class=" layui-unselect">
                                <div class="layui-table-cell laytable-cell-2-0-1">
                                    <span>手机号</span>
                                </div>
                            </th>
                            <th data-field="posttime" data-key="1-0-6" class="">
                                <div class="layui-table-cell">
                                    <span>身份证</span>
                                </div>
                            </th>
                            <th data-field="posttime" data-key="1-0-6" class="">
                                <div class="layui-table-cell">
                                    <span>报名状态</span>
                                </div>
                            </th>
                        </tr>
                        </thead>
                        <tbody  id="data_list" >
                        @foreach ($staff_list as $k => $staff_info)
                        <tr data-index="0" class="">
                            <td data-field="id" data-key="1-0-1" class="">
                                <label class="pos-rel">
                                    <input onclick="otheraction.seledSingle(this)" type="checkbox" class="ace check_item"  name="staff_id[]"   value="{{ $staff_info['id'] ?? '' }}" @if(isset($staff_info['is_joined']) && ($staff_info['is_joined'] & 1) == 1)  disabled @endif>
                                    <span class="lbl"></span>
                                </label>

                            </td>
                            <td data-field="id" data-key="1-0-1" class="">
                                <div class="layui-table-cell laytable-cell-1-0-1">
                                    {{ $staff_info['real_name'] ?? '' }}
                                </div>
                            </td>
                            <td data-field="posttime" data-key="1-0-6" class="">
                                <div class="layui-table-cell">
                                    {{ $staff_info['sex_text'] ?? '' }}
                                </div>
                            </td>
                            <td data-field="id" data-key="1-0-1" class="">
                                <div class="layui-table-cell laytable-cell-1-0-1">
                                    {{ $staff_info['mobile'] ?? '' }}
                                </div>
                            </td>

                            <td data-field="id" data-key="1-0-1" class="">
                                <div class="layui-table-cell laytable-cell-1-0-1">
                                    {{ $staff_info['id_number'] ?? '' }}
                                </div>
                            </td>
                            <td data-field="id" data-key="1-0-1" class="">
                                <div class="layui-table-cell laytable-cell-1-0-1">
                                    {{ $staff_info['is_joined_text'] ?? '' }}
                                </div>
                            </td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr>
                <th>联络人员<span class="must">*</span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="contacts" value="{{ $info['contacts'] ?? '' }}" placeholder="请输入联络人员"/>
                </td>
            </tr>
            <tr>
                <th>联络人电话<span class="must">*</span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="tel" value="{{ $info['tel'] ?? '' }}" placeholder="请输入联络人电话"/>
                </td>
            </tr>
            <tr>
                <th> </th>
                <td><button class="btn btn-l wnormal"  id="submitBtn" >立即提交</button></td>
            </tr>
        </table>
    </form>
</div>
<script src="{{asset('js/jquery-3.3.1.min.js')}}"></script>
<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.all.js')}}"></script>
{{--<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.js')}}"></script>--}}
@include('public.dynamic_list_foot')

<script type="text/javascript">
    var SAVE_URL = "{{ url('api/company/course/ajax_join_save') }}";// ajax保存记录地址
    var LIST_URL = "{{url('company/course')}}";//保存成功后跳转到的地址

    var DYNAMIC_TABLE = 'dynamic-table';//动态表格id
</script>
<script src="{{ asset('/js/company/QualityControl/Course_join.js') }}?2"  type="text/javascript"></script>
</body>
</html>
