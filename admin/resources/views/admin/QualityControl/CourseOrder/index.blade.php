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
    <div class="mmhead" id="mywork">

        @include('common.pageParams')
        <div class="tabbox" >
            <a href="javascript:void(0);" class="on" onclick="action.iframeModify(0)">添加培训班</a>
        </div>
        <form onsubmit="return false;" class="form-horizontal" style="display: block;" role="form" method="post" id="search_frm" action="#">
            <div class="msearch fr">
                <input type="text" value=""    name="keyword"  placeholder="请输入关键字"/>
                <button class="btn btn-normal search_frm">搜索</button>
            </div>
        </form>
    </div>
    <table lay-even class="layui-table table2 tableWidthFixed"  lay-size="lg"  id="dynamic-table">
        <colgroup>
            <col width="50">
            <col>
            <col>
            <col width="75">
            <col width="150">
            <col>
            <col>
            <col>
            <col width="90">
            <col width="125">
            <col width="150">
        </colgroup>
        <thead>
        <tr>
            <th>
                <label class="pos-rel">
                    <input type="checkbox"  class="ace check_all"  value="" onclick="action.seledAll(this)"/>
                </label>
            </th>
            <th>单位</th>
            <th>报名人数</th>
            <th>联系人</th>
            <th>联系电话</th>
            <th>培训班</th>
            <th>是否缴费</th>
            <th>报名时间</th>
        </tr>
        </thead>
        <tbody id="data_list">
        </tbody>
    </table>
    <div class="mmfoot">
        <div class="mmfleft"></div>
        <div class="pagination">
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
    // var LIST_FUNCTION_NAME = "reset_list_self";// 列表刷新函数名称, 需要列表刷新同步时，使用自定义方法reset_list_self；异步时没有必要自定义
    var AJAX_URL = "{{ url('api/admin/courses_order/management/' . $info['id']  . '/ajax_list') }}";//ajax请求的url
    var ADD_URL = "{{ url('admin/courses_order/management/' . $info['id']  . '/ability_join_items_results/add/0') }}"; //添加url

    var IFRAME_MODIFY_URL = "{{url('admin/courses_order/management/' . $info['id']  . '/ability_join_items_results/add/')}}/";//添加/修改页面地址前缀 + id
    var IFRAME_MODIFY_URL_TITLE = "项目" ;// 详情弹窗显示提示  [添加/修改] +  栏目/主题
    var IFRAME_MODIFY_CLOSE_OPERATE = 0 ;// 详情弹窗operate_num关闭时的操作0不做任何操作1刷新当前页面2刷新当前列表页面

    var SHOW_URL = "{{url('admin/courses_order/management/' . $info['id']  . '/ability_join_items_results/info/')}}/";//显示页面地址前缀 + id
    var SHOW_URL_TITLE = "详情" ;// 详情弹窗显示提示
    var SHOW_CLOSE_OPERATE = 0 ;// 详情弹窗operate_num关闭时的操作0不做任何操作1刷新当前页面2刷新当前列表页面
    var EDIT_URL = "{{url('admin/courses_order/management/' . $info['id']  . '/ability_join_items_results/add/')}}/";//修改页面地址前缀 + id
    var DEL_URL = "{{ url('api/admin/courses_order/management/' . $info['id']  . '/ability_join_items_results/ajax_del') }}";//删除页面地址
    var BATCH_DEL_URL = "{{ url('api/admin/courses_order/management/' . $info['id']  . '/ability_join_items_results/ajax_del') }}";//批量删除页面地址
    var EXPORT_EXCEL_URL = "{{ url('admin/courses_order/management/' . $info['id']  . '/ability_join_items_results/export') }}";//导出EXCEL地址
    var IMPORT_EXCEL_TEMPLATE_URL = "{{ url('admin/courses_order/management/' . $info['id']  . '/ability_join_items_results/import_template') }}";//导入EXCEL模版地址
    var IMPORT_EXCEL_URL = "{{ url('api/admin/courses_order/management/' . $info['id']  . '/ability_join_items_results/import') }}";//导入EXCEL地址
    var IMPORT_EXCEL_CLASS = "import_file";// 导入EXCEL的file的class

    var IFRAME_SAMPLE_URL = "{{url('admin/courses_order/management/' . $info['id']  . '/ability_join_items_results/get_sample/')}}/";//添加/修改页面地址前缀 + id

    var IFRAME_SAMPLE_RESULT_INFO_URL = "{{url('admin/courses_order/management/' . $info['id']  . '/ability_join_items/sample_result_info/')}}/";//显示页面地址前缀 + id / + retry_no

    // 列表数据每隔指定时间就去执行一次刷新【如果表有更新时】--定时执行
    var IFRAME_TAG_KEY = "";// "QualityControl\\CTAPIStaff";// 获得模型表更新时间的关键标签，可为空：不获取
    var IFRAME_TAG_TIMEOUT = 60000;// 获得模型表更新时间运行间隔 1000:1秒 ；可以不要此变量：默认一分钟

</script>
<script src="{{asset('js/common/list.js')}}"></script>
<script src="{{ asset('js/admin/QualityControl/CourseOrder.js') }}"  type="text/javascript"></script>
</body>
</html>
