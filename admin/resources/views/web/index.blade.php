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

<div class="nav">
	<a href="mycard.html" >我的卡片</a>
	<a href="template.html" class="on">模板库</a>
</div>
@include('common.pageParams')

<form onsubmit="return false;" class="form-horizontal" style="display: none; " role="form" method="post" id="search_frm" action="#">
    <div class="msearch fr" >
        <button class="btn btn-normal search_frm">搜索</button>
    </div>
</form>
<div  id="dynamic-table">

    <div class="wrap tc "  >
{{--    <div class="wrap tc " >--}}

        <div class="k50"></div>
        <div class=" baguetteBoxOne gallery" id="data_list">
            <div class='mb-item'>
                <a href='card-edit.html'><img src='' alt=""></a>
            </div>
        </div>
    </div>
</div>


<div class="mmfoot">
    <div class="mmfleft"></div>
    <div class="pagination">
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
    var AJAX_URL = "{{ url('api/web/templates/ajax_alist') }}";//ajax请求的url
    var ADD_URL = "{{ url('web/templates/add/0') }}"; //添加url

    var IFRAME_MODIFY_URL = "{{url('web/templates/add/')}}/";//添加/修改页面地址前缀 + id
    var IFRAME_MODIFY_URL_TITLE = "模板库" ;// 详情弹窗显示提示  [添加/修改] +  栏目/主题
    var IFRAME_MODIFY_CLOSE_OPERATE = 0 ;// 详情弹窗operate_num关闭时的操作0不做任何操作1刷新当前页面2刷新当前列表页面

    var SHOW_URL = "{{url('web/templates/info/')}}/";//显示页面地址前缀 + id
    var SHOW_URL_TITLE = "" ;// 详情弹窗显示提示
    var SHOW_CLOSE_OPERATE = 0 ;// 详情弹窗operate_num关闭时的操作0不做任何操作1刷新当前页面2刷新当前列表页面
    var EDIT_URL = "{{url('web/templates/add/')}}/";//修改页面地址前缀 + id
    var DEL_URL = "{{ url('api/web/templates/ajax_del') }}";//删除页面地址
    var BATCH_DEL_URL = "{{ url('api/web/templates/ajax_del') }}";//批量删除页面地址
    var EXPORT_EXCEL_URL = "{{ url('web/templates/export') }}";//导出EXCEL地址
    var IMPORT_EXCEL_TEMPLATE_URL = "{{ url('web/templates/import_template') }}";//导入EXCEL模版地址
    var IMPORT_EXCEL_URL = "{{ url('api/web/templates/import') }}";//导入EXCEL地址
    var IMPORT_EXCEL_CLASS = "import_file";// 导入EXCEL的file的class

    var IFRAME_USE_URL = "{{url('web/templates/use/')}}/";//使用页面地址前缀 + id
</script>
<link rel="stylesheet" href="{{asset('js/baguetteBox.js/baguetteBox.min.css')}}">
<script src="{{asset('js/baguetteBox.js/baguetteBox.min.js')}}" async></script>
{{--<script src="{{asset('js/baguetteBox.js/highlight.min.js')}}" async></script>--}}

<script src="{{asset('js/common/list.js')}}"></script>
<script src="{{ asset('js/web/DogTools/Templates.js') }}"  type="text/javascript"></script>

</body>
</html>

<!-- 前端模板部分 -->
<!-- 列表模板部分 开始  <! -- 模板中可以用HTML注释 -- >  或  <%* 这是模板自带注释格式 *%> -->
<script type="text/template"  id="baidu_template_data_list">
    <%for(var i = 0; i<data_list.length;i++){
    var item = data_list[i];
    var resource_list = item.resource_list;
    %>
    <div class="mb-item">
        <a href="javascript:void(0);" onclick="otheraction.use(this, <%=item.id%>)" >
        <%for(var j = 0; j < resource_list.length; j++){
            var jitem = resource_list[j];
            %>
            <img src="<%=jitem.resource_url%>" alt="<%=item.template_name%>" title="<%=item.template_name%>"/>
        <%}%>
        </a>
    </div>
    <%}%>
</script>
<!-- 列表模板部分 结束-->
<!-- 前端模板结束 -->
