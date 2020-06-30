<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>教育云助手</title>
    @include('web.layout_public.pagehead')
    <link rel="stylesheet" href="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/css/layui.css')}}" media="all">
</head>
<body style="background:#f1f3f3;">

@include('web.layout_public.header')
<div class="k50"></div>
<div class="wrap tc">
    @include('web.layout_public.headClass')
    @include('common.pageParams')

    <form onsubmit="return false;" class="form-horizontal" style="display: none; " role="form" method="post" id="search_frm" action="#">
        <div class="msearch fr" >
            <button class="btn btn-normal search_frm">搜索</button>
        </div>
    </form>
	<div class="main tabcontent">
		<div class="tab-item">

{{--			<div class="card-head">--}}
{{--				<div class="btn-group fl">--}}
{{--					<a href="" class="btn">添加学生</a>--}}
{{--					<a href="" class="btn">导入学生</a>--}}
{{--					<a href="" class="btn">删除</a>--}}
{{--				</div>--}}
{{--			</div>--}}

            @if(isset($canModif) && $canModif == 1)
           <div class="table-header card-head">
               <div class="btn-group fl">
                   <button class="btn btn-danger  btn-xs batch_del"  onclick="action.iframeModify(0)">添加学生</button>
                    <button class="btn btn-danger  btn-xs batch_del"  onclick="action.batchDel(this)">批量删除</button>
{{--                    <button class="btn btn-success  btn-xs export_excel"  onclick="action.batchExportExcel(this)" >导出[按条件]</button>--}}
{{--                    <button class="btn btn-success  btn-xs export_excel"  onclick="action.exportExcel(this)" >导出[勾选]</button>--}}
                    <button class="btn btn-success  btn-xs import_excel"  onclick="action.importExcelTemplate(this)">导入模版[EXCEL]</button>
                    <button class="btn btn-success  btn-xs import_excel"  onclick="action.importExcel(this)">导入学生</button>
                    <div style="display:none;" ><input type="file" class="import_file img_input"></div>{{--导入file对象--}}
               </div>
            </div>
            @endif
            <div class="k20"></div>
            <table class="table table-nob" style="width:100%;"  id="dynamic-table">
                <colgroup>
{{--                    <col width="50">--}}
                    <col width="100">
                    <col width="">
                    <col width="">
                    <col width="150">
{{--                    <col width="">--}}
                    <col width="150">
                </colgroup>
                <thead>
                    <tr>
                        <th>
                            <label class="pos-rel">
                                <input type="checkbox"  class="ace check_all"  value="" onclick="action.seledAll(this)"/>
                                <!-- <span class="lbl">全选</span> -->
                            </label>
                        </th>
{{--                        <th>头像</th>--}}
                        <th>学号</th>
                        <th>姓名</th>
                        <th>性别</th>
{{--                        <th>标签</th>--}}
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody  id="data_list">
{{--                    <tr>--}}
{{--                        <td><input type="checkbox"></td>--}}
{{--                        <td><img src="{{asset('staticweb/images-temp/tx01.jpg')}}" class="tx" alt=""></td>--}}
{{--                        <td>徐静宸</td>--}}
{{--                        <td>女生</td>--}}
{{--                        <td>一组</td>--}}
{{--                        <td>--}}
{{--                            <a href="" class="btn">编辑</a>--}}
{{--                            <a href="" class="btn">删除</a>--}}
{{--                        </td>--}}
{{--                    </tr>--}}
                </tbody>
            </table>
            <div class="mmfoot">
                <div class="mmfleft"></div>
                <div class="pagination">
                </div>
            </div>
{{--			<div class="pages">--}}
{{--				<a href="#1">&lt;</a>--}}
{{--				<a href="#1" class="on">1</a>--}}
{{--				<a href="#1">2</a>--}}
{{--				<a href="#1">3</a>--}}
{{--				<a href="#1">4</a>--}}
{{--				<a href="#1">5</a>--}}
{{--				<a href="#1">6</a>--}}
{{--				<a href="#0">7</a>--}}
{{--				<a href="#1">&gt;</a>--}}
{{--			</div>--}}

		</div>
		<div class="tab-item">

		</div>
	</div>
</div>

<script src="{{asset('js/jquery-3.3.1.min.js')}}"></script>
<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.all.js')}}"></script>
{{--<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.js')}}"></script>--}}
@include('public.dynamic_list_foot')

<script type="text/javascript">
    var OPERATE_TYPE = <?php echo isset($operate_type)?$operate_type:0; ?>;
    var AUTO_READ_FIRST = false;//自动读取第一页 true:自动读取 false:指定地方读取
    var LIST_FUNCTION_NAME = "reset_list_self";// 列表刷新函数名称, 需要列表刷新同步时，使用自定义方法reset_list_self；异步时没有必要自定义
    var AJAX_URL = "{{ url('api/web/students/' . $class_id . '/ajax_alist') }}";//ajax请求的url
    var ADD_URL = "{{ url('web/students/' . $class_id . '/add/0') }}"; //添加url

    var IFRAME_MODIFY_URL = "{{url('web/students/' . $class_id . '/add/')}}/";//添加/修改页面地址前缀 + id
    var IFRAME_MODIFY_URL_TITLE = "班级老师" ;// 详情弹窗显示提示  [添加/修改] +  栏目/主题
    var IFRAME_MODIFY_CLOSE_OPERATE = 0 ;// 详情弹窗operate_num关闭时的操作0不做任何操作1刷新当前页面2刷新当前列表页面

    var SHOW_URL = "{{url('web/students/' . $class_id . '/info/')}}/";//显示页面地址前缀 + id
    var SHOW_URL_TITLE = "" ;// 详情弹窗显示提示
    var SHOW_CLOSE_OPERATE = 0 ;// 详情弹窗operate_num关闭时的操作0不做任何操作1刷新当前页面2刷新当前列表页面
    var EDIT_URL = "{{url('web/students/' . $class_id . '/add/')}}/";//修改页面地址前缀 + id
    var DEL_URL = "{{ url('api/web/students/' . $class_id . '/ajax_del') }}";//删除页面地址
    var BATCH_DEL_URL = "{{ url('api/web/students/' . $class_id . '/ajax_del') }}";//批量删除页面地址
    var EXPORT_EXCEL_URL = "{{ url('web/students/' . $class_id . '/export') }}";//导出EXCEL地址
    var IMPORT_EXCEL_TEMPLATE_URL = "{{ url('web/students/' . $class_id . '/import_template') }}";//导入EXCEL模版地址
    var IMPORT_EXCEL_URL = "{{ url('api/web/students/' . $class_id . '/import') }}";//导入EXCEL地址
    var IMPORT_EXCEL_CLASS = "import_file";// 导入EXCEL的file的class

    var CURRENT_USER_CAN_MODIFY = "{{ $canModif }}";// 是否可以修改记录 2：不可以修改操作 1 ：可以修改操作
</script>
<script src="{{asset('js/common/list.js')}}"></script>
<script src="{{ asset('js/web/DogTools/Students.js') }}"  type="text/javascript"></script>

</body>
</html>

<!-- 前端模板部分 -->
<!-- 列表模板部分 开始  <! -- 模板中可以用HTML注释 -- >  或  <%* 这是模板自带注释格式 *%> -->
<script type="text/template"  id="baidu_template_data_list">
    <%for(var i = 0; i<data_list.length;i++){
    var item = data_list[i];
    var can_modify = false;
    if(CURRENT_USER_CAN_MODIFY == 1 ){
        can_modify = true;
    }
    // var resource_list = item.resource_list;
    %>
    <tr>
        <td>
            <label class="pos-rel">
                <input  onclick="action.seledSingle(this)" type="checkbox" class="ace check_item" value="<%=item.id%>" <%if( false &&  !can_modify){%> disabled <%}%> />
                <span class="lbl"></span>
            </label>
        </td>
{{--        <td><img src="{{asset('staticweb/images-temp/tx01.jpg')}}" class="tx" alt=""></td>--}}
        <td><%=item.student_number%></td>
        <td><%=item.real_name%></td>
        <td><%=item.sex_text%></td>
        <td>
            <%if( false){%>
            <a href="javascript:void(0);" class="btn btn-mini btn-success"  onclick="action.show(<%=item.id%>)">
                <i class="ace-icon fa fa-check bigger-60"> 查看</i>
            </a>
            <%}%>
            <%if( can_modify){%>
            <a href="javascript:void(0);" class="btn btn-mini btn-info" onclick="action.iframeModify(<%=item.id%>)">
                <i class="ace-icon fa fa-pencil bigger-60"> 编辑</i>
            </a>
            <a href="javascript:void(0);" class="btn btn-mini btn-info" onclick="action.del(<%=item.id%>)">
                <i class="ace-icon fa fa-trash-o bigger-60"> 删除</i>
            </a>
            <%}%>
        </td>
    </tr>

    <%}%>
</script>
<!-- 列表模板部分 结束-->
<!-- 前端模板结束 -->

