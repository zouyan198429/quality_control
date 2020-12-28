

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>开启头部工具栏 - 数据表格</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <!-- zui css -->
    <link rel="stylesheet" href="{{asset('dist/css/zui.min.css') }}">
    @include('admin.layout_public.pagehead')
    <link rel="stylesheet" href="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/css/layui.css')}}" media="all">
    <link rel="stylesheet" href="{{asset('layui-admin-v1.2.1/src/layuiadmin/style/admin.css')}}" media="all">
</head>
<body>

{{--<div id="crumb"><i class="fa fa-reorder fa-fw" aria-hidden="true"></i> {{ $operate ?? '' }}员工</div>--}}
<div class="mm">
    <form class="am-form am-form-horizontal" method="post"  id="addForm" onsubmit="return false;">
        <input type="hidden" name="hidden_option"  value="{{ $hidden_option ?? 0 }}" />
        <input type="hidden" name="id" value="{{ $info['id'] ?? 0 }}"/>
        <table class="table1" >
            <tr>
                <th>培训名称<span class="must"></span></th>
                <td>
                    {{ $info['course_name'] ?? '' }}
                </td>
            </tr>
            <tr>
                <th>简要概述<span class="must"></span></th>
                <td>
                    {!! $info['explain_remarks'] ?? '' !!}
                </td>
            </tr>
            <tr>
                <th>当前状态<span class="must"></span></th>
                <td>
                    {{ $info['status_online_text'] ?? '' }}
                </td>
            </tr>
            <tr class=" baguetteBoxOne gallery"   id="resource_block">
                <th>图片资源<span class="must"></span></th>
                <td>
                    <span class="resource_list"  style="display: none;">@json($info['resource_list'] ?? [])</span>
                    <span  class="resource_show_course"></span>
                </td>
            </tr>
            <tr>
                <th>课程介绍<span class="must"></span></th>
                <td>
                    {!! $info['course_content'] ?? '' !!}
                </td>
            </tr>
            <tr>
                <th>选择学员</th>
                <td>
                    共 <span class="subject_num">{{ $subject_num ?? '0' }}</span> 人
                    <button class="btn btn-danger  btn-xs ace-icon fa fa-plus-circle bigger-60"  onclick="otheraction.selectUser(this)">选择人员</button>
                </td>
            </tr>
            <tr>
                <td colspan="2"  class="staff_td">
                    <div class="table-header">
                        <button class="btn btn-danger  btn-xs ace-icon fa fa-trash-o bigger-60"  onclick="otheraction.batchDel(this, '.staff_td', 'tr')">批量删除</button>
                    </div>
                    <table class="table2">
                        <thead>
                        <tr>
                            <th style="width: 90px;">
                                <label class="pos-rel">
                                    <input type="checkbox" class="ace check_all" value="" onclick="otheraction.seledAll(this,'.table2')">
                                    <span class="lbl">全选</span>
                                </label>
                                <input type="hidden" name="subject_ids[]" value="1502"/>
                                <input type="hidden" name="subject_history_ids[]" value="17"/>
                            </th>
                            <th>姓名</th>
                            <th>性别</th>
                            <th>证书所属单位</th>
                            <th>证件照</th>
                            <th>手机</th>
                            <th>身份证</th>
                            <th>报名状态</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody class="data_list   baguetteBoxOne gallery" >
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

    var PAY_URL = "{{ url('company/course_order/pay') }}";//操作(缴费)

    var DYNAMIC_TABLE = 'dynamic-table';//动态表格id


    var DOWN_FILE_URL = "{{ url('company/down_file') }}";// 下载
    var DEL_FILE_URL = "{{ url('api/company/upload/ajax_del') }}";// 删除文件的接口地址

    var SELECT_USER_URL = "{{ url('company/user/select') }}";// 选择人员地址
    var AJAX_USER_ADD_URL = "{{ url('api/company/course/ajax_add_user') }}";// ajax添加人员地址
    var DYNAMIC_BAIDU_TEMPLATE = "baidu_template_data_list";//百度模板id
    var DYNAMIC_TABLE_BODY = "data_list";//数据列表class

</script>
<link rel="stylesheet" href="{{asset('js/baguetteBox.js/baguetteBox.min.css')}}">
<script src="{{asset('js/baguetteBox.js/baguetteBox.min.js')}}" async></script>
{{--<script src="{{asset('js/baguetteBox.js/highlight.min.js')}}" async></script>--}}
<!-- zui js -->
<script src="{{asset('dist/js/zui.min.js') }}"></script>
<script src="{{ asset('/js/company/QualityControl/Course_join.js') }}?29"  type="text/javascript"></script>
@component('component.upfileincludejsmany')
@endcomponent
</body>
</html>
