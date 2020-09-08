

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>企业帐号-能力附表</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    @include('admin.layout_public.pagehead')
    <link rel="stylesheet" href="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/css/layui.css')}}" media="all">
    <link rel="stylesheet" href="{{asset('layui-admin-v1.2.1/src/layuiadmin/style/admin.css')}}" media="all">
</head>
<body>
<div class="layui-fluid">
    <div class="layui-row layui-col-space15">
        <div class="layui-col-md12">
            <div class="layui-card">
                @include('common.pageParams')
                <div class="layui-card-header">
                    能力附表
                    <div class="layui-btn-group layuiadmin-btn-group" >
                        <a href="javascript:void(0);" class="layui-btn" style="background: none; color:#1E9FFF;"  onclick="otheraction.down_moban()">能力附表Execl模板文件</a>
                        <a href="javascript:void(0);" class="layui-btn layui-btn-sm layui-btn-normal" onclick="otheraction.upExcelFile(0)">上传excel文件</a>
                        <a href="javascript:void(0);" class="layui-btn layui-btn-sm layui-btn-normal" onclick="action.iframeModify(0)">上传PDF文件</a>
                    </div>

                </div>

                <form onsubmit="return false;" class="form-horizontal" style="display: block;" role="form" method="post" id="search_frm" action="#">
                    <div class="msearch fr">
                        <button class="btn btn-normal search_frm" style="display: none;">搜索</button>
                    </div>
                </form>
                <div class="layui-card-body" pad15>
                    <p style="color: red; padding-bottom:10px;">注：<strong>需要同时上传pdf和Execl两种文档; </strong><br >pdf文件需要是盖省局章子的能力附表扫描件；<br >Execl文件参考右上角的 “<a href="javascript:void(0);"  style="background: none; color:#1E9FFF;"  onclick="otheraction.down_moban()">能力附表Execl模板文件</a>”，下载后对照模板文件的格式检查。</p>

                    <table lay-even class="layui-table table2 tableWidthFixed"  lay-size="lg"  id="dynamic-table">
                        <colgroup>
{{--                            <col width="60">--}}
                            <col width="150">
                            <col width="">
{{--                            <col width="">--}}
                            <col width="180">
                            <col width="90">
                        </colgroup>
                        <thead>
                        <tr>
                            <!-- <th>
                              <label class="pos-rel">
                                <input type="checkbox"  class="ace check_all"  value="" onclick="action.seledAll(this)"/>
                              </label>
                            </th> -->
{{--                            <th>ID</th>--}}
                            <th>文档类型</th>
                            <th>文件</th>
{{--                            <th>pdf文件</th>--}}
{{--                            <th>excel文件</th>--}}
                            <th>创建时间</th>
                            <th>操作</th>
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

            </div>
        </div>
    </div>

</div>

<script src="{{asset('js/jquery-3.3.1.min.js')}}"></script>
<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.all.js')}}"></script>
@include('public.dynamic_list_foot')

<script type="text/javascript">
    var OPERATE_TYPE = <?php echo isset($operate_type)?$operate_type:0; ?>;
    var AUTO_READ_FIRST = false;//自动读取第一页 true:自动读取 false:指定地方读取
    var LIST_FUNCTION_NAME = "reset_list_self";// 列表刷新函数名称, 需要列表刷新同步时，使用自定义方法reset_list_self；异步时没有必要自定义
    var AJAX_URL = "{{ url('api/company/company_new_schedule/ajax_alist') }}";//ajax请求的url
    var ADD_URL = "{{ url('company/company_new_schedule/add/0') }}"; //添加url

    var IFRAME_MODIFY_URL = "{{url('company/company_new_schedule/add/')}}/";//添加/修改页面地址前缀 + id
    var IFRAME_MODIFY_URL_TITLE = "能力附表-上传PDF文件" ;// 详情弹窗显示提示  [添加/修改] +  栏目/主题
    var IFRAME_MODIFY_CLOSE_OPERATE = 0 ;// 详情弹窗operate_num关闭时的操作0不做任何操作1刷新当前页面2刷新当前列表页面

    var SHOW_URL = "{{url('company/company_new_schedule/info/')}}/";//显示页面地址前缀 + id
    var SHOW_URL_TITLE = "" ;// 详情弹窗显示提示
    var SHOW_CLOSE_OPERATE = 0 ;// 详情弹窗operate_num关闭时的操作0不做任何操作1刷新当前页面2刷新当前列表页面
    var EDIT_URL = "{{url('company/company_new_schedule/add/')}}/";//修改页面地址前缀 + id
    var DEL_URL = "{{ url('api/company/company_new_schedule/ajax_del') }}";//删除页面地址
    var BATCH_DEL_URL = "{{ url('api/company/company_new_schedule/ajax_del') }}";//批量删除页面地址
    var EXPORT_EXCEL_URL = "{{ url('company/company_new_schedule/export') }}";//导出EXCEL地址
    var IMPORT_EXCEL_TEMPLATE_URL = "{{ url('company/company_new_schedule/import_template') }}";//导入EXCEL模版地址
    var IMPORT_EXCEL_URL = "{{ url('api/company/company_new_schedule/import') }}";//导入EXCEL地址
    var IMPORT_EXCEL_CLASS = "import_file";// 导入EXCEL的file的class

    var SELECT_COMPANY_URL = "{{url('company/company/select')}}";// 选择所属企业

    var IFRAME_UPEXCEL_URL = "{{url('company/company_new_schedule/add_excel/')}}/";//添加/修改页面地址前缀 + id
    var IFRAME_UPEXCEL_URL_TITLE = "excel文件" ;// 详情弹窗显示提示  [添加/修改] +  栏目/主题


    var DOWN_MOBAN_URL = "{{ url('company/company_new_schedule/down_moban') }}";// 下载excel模板文件
    var DOWN_FILE_URL = "{{ url('company/down_file') }}";// 下载网页打印机驱动

    // 列表数据每隔指定时间就去执行一次刷新【如果表有更新时】--定时执行
    var IFRAME_TAG_KEY = "";// "QualityControl\\CTAPIStaff";// 获得模型表更新时间的关键标签，可为空：不获取
    var IFRAME_TAG_TIMEOUT = 60000;// 获得模型表更新时间运行间隔 1000:1秒 ；可以不要此变量：默认一分钟

</script>
<script src="{{asset('js/common/list.js')}}"></script>
<script src="{{ asset('js/company/QualityControl/CompanyNewSchedule.js?21') }}"  type="text/javascript"></script>
</body>
</html>
