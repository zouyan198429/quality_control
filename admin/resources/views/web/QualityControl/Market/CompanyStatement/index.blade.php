

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



<div class="mm" style="margin:0">

	<div>
{{--		<div class="com-name">--}}
{{--					 {{ $info['company_name'] ?? '' }}--}}
{{--		</div>--}}
		<div class="content-info">
			<p>机构名称：<span>{{ $info['company_name'] ?? '' }}</span></p>
			<p>CMA证书编号：<span>{{ $info['company_certificate_no'] ?? '' }}</span></p>
			<p>发证日期：<span>{{ $info['ratify_date'] ?? '' }}</span></p>
			<p>证书有效期：<span> {{ $info['valid_date'] ?? '' }}</span></p>
			<p>联系地址：<span> {{ $info['laboratory_addr'] ?? '' }}</span></p>
		</div>
		<div class="c"></div>
	</div>


  <div class="mmhead" id="mywork">
    @include('common.pageParams')
    <form onsubmit="return false;" class="form-horizontal" style="display: none;" role="form" method="post" id="search_frm" action="#">
      <div class="msearch fr">

          <input type="hidden" name="company_hidden"  value="{{ $company_hidden ?? 0 }}" />
          <span   @if (isset($company_hidden) && $company_hidden == 1 ) style="display: none;"  @endif>
                <input type="hidden" class="select_id" name="company_id"  value="{{ $info['company_id'] ?? '' }}" />
                <span class="select_name company_name">{{ $info['user_company_name'] ?? '' }}</span>
                <i class="close select_close company_id_close"  onclick="clearSelect(this)" style="display: none;">×</i>
                <button  type="button"  class="btn btn-danger  btn-xs ace-icon fa fa-plus-circle bigger-60"  onclick="otheraction.selectCompany(this)">选择企业</button>
          </span>
        <select style="width:80px; height:28px;" name="field">
            <option value="resource_name">资源名称</option>
        </select>
        <input type="text" value=""    name="keyword"  placeholder="请输入关键字"/>
        <button class="btn btn-normal search_frm">搜索</button>
      </div>
    </form>
  </div>
   <h3>◆ 自我声明公告</h3>
  <table lay-even class="layui-table table2 tableWidthFixed"  lay-size="lg"  id="dynamic-table">
    <colgroup>
        <col>
        <col width="200">
        <col width="350">
    </colgroup>
    <thead>
    <tr>
		<th>文件名</th>
        <th>上传日期</th>
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

    <h3>◆ 机构能力附表</h3>
	<table lay-even class="layui-table table2 tableWidthFixed"  lay-size="lg"  id="nlfb-table">
	  <colgroup>
	      <col>
	      <col width="200">
	      <col width="350">
	  </colgroup>
	  <thead>
	  <tr>
		<th>文件名</th>
	    <th>上传日期</th>
	    <th>操作</th>
	  </tr>
	  </thead>
	  <tbody id="schedule_data_list">
	  </tbody>
	</table>

</div>

  <script src="{{asset('js/jquery-3.3.1.min.js')}}"></script>
  <script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.all.js')}}"></script>
  {{--<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.js')}}"></script>--}}
  @include('public.dynamic_list_foot')

  <script type="text/javascript">
      var OPERATE_TYPE = <?php echo isset($operate_type)?$operate_type:0; ?>;
      var AUTO_READ_FIRST = false;//自动读取第一页 true:自动读取 false:指定地方读取
      var LIST_FUNCTION_NAME = "reset_list_self";// 列表刷新函数名称, 需要列表刷新同步时，使用自定义方法reset_list_self；异步时没有必要自定义
      var AJAX_URL = "{{ url('api/web/market/company_statement/ajax_alist') }}";//ajax请求的url
      var ADD_URL = "{{ url('web/market/company_statement/add/0') }}"; //添加url

      var IFRAME_MODIFY_URL = "{{url('web/market/company_statement/add/')}}/";//添加/修改页面地址前缀 + id
      var IFRAME_MODIFY_URL_TITLE = "机构自我声明" ;// 详情弹窗显示提示  [添加/修改] +  栏目/主题
      var IFRAME_MODIFY_CLOSE_OPERATE = 0 ;// 详情弹窗operate_num关闭时的操作0不做任何操作1刷新当前页面2刷新当前列表页面

      var SHOW_URL = "{{url('web/market/company_statement/info/')}}/";//显示页面地址前缀 + id
      var SHOW_URL_TITLE = "" ;// 详情弹窗显示提示
      var SHOW_CLOSE_OPERATE = 0 ;// 详情弹窗operate_num关闭时的操作0不做任何操作1刷新当前页面2刷新当前列表页面
      var EDIT_URL = "{{url('web/market/company_statement/add/')}}/";//修改页面地址前缀 + id
      var DEL_URL = "{{ url('api/web/market/company_statement/ajax_del') }}";//删除页面地址
      var BATCH_DEL_URL = "{{ url('api/web/market/company_statement/ajax_del') }}";//批量删除页面地址
      var EXPORT_EXCEL_URL = "{{ url('web/market/company_statement/export') }}";//导出EXCEL地址
      var IMPORT_EXCEL_TEMPLATE_URL = "{{ url('web/market/company_statement/import_template') }}";//导入EXCEL模版地址
      var IMPORT_EXCEL_URL = "{{ url('api/web/market/company_statement/import') }}";//导入EXCEL地址
      var IMPORT_EXCEL_CLASS = "import_file";// 导入EXCEL的file的class

      var SELECT_COMPANY_URL = "{{url('web/market/company/select')}}";// 选择所属企业

      var DOWN_FILE_URL = "{{ url('web/market/down_file') }}";// 下载

      // 列表数据每隔指定时间就去执行一次刷新【如果表有更新时】--定时执行
      // var IFRAME_TAG_KEY = "";// "QualityControl\\CTAPIStaff";// 获得模型表更新时间的关键标签，可为空：不获取
      // var IFRAME_TAG_TIMEOUT = 60000;// 获得模型表更新时间运行间隔 1000:1秒 ；可以不要此变量：默认一分钟

      var  SCHEDULE_LIST_DATA = @json($schedule_list ?? []);
      var SCHEDULE_TABLE_ID = 'schedule_data_list';
      var SCHEDULE_BAIDU_TELPLETE = 'baidu_template_data_list_schedule';

  </script>
  <script src="{{asset('js/common/list.js')}}"></script>
  <script src="{{ asset('js/web/QualityControl/Market/CompanyStatement.js') }}?443"  type="text/javascript"></script>
</body>
</html>
