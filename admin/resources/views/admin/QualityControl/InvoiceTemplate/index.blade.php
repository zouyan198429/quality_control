

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

{{--<div id="crumb"><i class="fa fa-reorder fa-fw" aria-hidden="true"></i> 我的同事</div>--}}
<div class="mm">
  <div class="mmhead" id="mywork">

    @include('common.pageParams')
    <div class="tabbox" >
      <a href="javascript:void(0);" class="on" onclick="action.iframeModify(0)">添加发票开票模板</a>
    </div>
    <form onsubmit="return false;" class="form-horizontal" style="display: block;" role="form" method="post" id="search_frm" action="#">
      <div class="msearch fr">
          <input type="hidden" name="hidden_option"  value="{{ $hidden_option ?? 0 }}" />

        {{--<select class="wmini" name="province_id">--}}
          {{--<option value="">全部</option>--}}
          {{--@foreach ($province_kv as $k=>$txt)--}}
            {{--<option value="{{ $k }}"  @if(isset($province_id) && $province_id == $k) selected @endif >{{ $txt }}</option>--}}
          {{--@endforeach--}}
        {{--</select>--}}
          <select class="wmini" name="invoiceService">
              <option value="">开票服务商</option>
              @foreach ($invoiceService as $k=>$txt)
                  <option value="{{ $k }}"  @if(isset($defaultInvoiceService) && $defaultInvoiceService == $k) selected @endif >{{ $txt }}</option>
              @endforeach
          </select>
{{--          <select class="wmini" name="itype">--}}
{{--              <option value="">发票类型</option>--}}
{{--              @foreach ($itype as $k=>$txt)--}}
{{--                  <option value="{{ $k }}"  @if(isset($defaultItype) && $defaultItype == $k) selected @endif >{{ $txt }}</option>--}}
{{--              @endforeach--}}
{{--          </select>--}}
          <select class="wmini" name="zsfs">
              <option value="">征税方式</option>
              @foreach ($zsfs as $k=>$txt)
                  <option value="{{ $k }}"  @if(isset($defaultZsfs) && $defaultZsfs == $k) selected @endif >{{ $txt }}</option>
              @endforeach
          </select>
          <select class="wmini" name="tspz">
              <option value="">特殊票种标识</option>
              @foreach ($tspz as $k=>$txt)
                  <option value="{{ $k }}"  @if(isset($defaultTspz) && $defaultTspz == $k) selected @endif >{{ $txt }}</option>
              @endforeach
          </select>
          <select class="wmini" name="open_status">
              <option value="">开启状态</option>
              @foreach ($openStatus as $k=>$txt)
                  <option value="{{ $k }}"  @if(isset($defaultOpenStatus) && $defaultOpenStatus == $k) selected @endif >{{ $txt }}</option>
              @endforeach
          </select>
        <select style="width:80px; height:28px;" name="field">
            <option value="template_name">发票模板名称</option>
          <option value="kpr">开票人</option>
            <option value="skr">收款人</option>
            <option value="fhr">复核人</option>
            <option value="bz">备注</option>
        </select>
        <input type="text" value=""    name="keyword"  placeholder="请输入关键字"/>
        <button class="btn btn-normal search_frm">搜索</button>
      </div>
    </form>
  </div>
  {{--
  <div class="table-header">
    { {--<button class="btn btn-danger  btn-xs batch_del"  onclick="action.batchDel(this)">批量删除</button>--} }
    <button class="btn btn-success  btn-xs export_excel"  onclick="action.batchExportExcel(this)" >导出[按条件]</button>
    <button class="btn btn-success  btn-xs export_excel"  onclick="action.exportExcel(this)" >导出[勾选]</button>
    <button class="btn btn-success  btn-xs import_excel"  onclick="action.importExcelTemplate(this)">导入模版[EXCEL]</button>
    <button class="btn btn-success  btn-xs import_excel"  onclick="action.importExcel(this)">导入城市</button>
    <div style="display:none;" ><input type="file" class="import_file img_input"></div>{ {--导入file对象--} }
  </div>
--}}
  <table lay-even class="layui-table table2 tableWidthFixed"  lay-size="lg"  id="dynamic-table">
    <colgroup>
{{--        <col width="50">--}}
{{--        <col width="60">--}}
        <col>
        <col>
        <col width="110">
        <col width="80">
        <col width="80">
        <col>
        <col width="95">
        <col width="15%">
    </colgroup>
    <thead>
    <tr>
{{--      <th>--}}
{{--        <label class="pos-rel">--}}
{{--          <input type="checkbox"  class="ace check_all"  value="" onclick="action.seledAll(this)"/>--}}
{{--          <!-- <span class="lbl">全选</span> -->--}}
{{--        </label>--}}
{{--      </th>--}}
{{--      <th>ID</th>--}}
        <th>开票服务商</th>
      <th>发票模板名称</th>
      <th>征税方式<hr/>特殊票种标识</th>
        <th>开票人<hr/>收款人</th>
        <th>复核人<hr/>开启状态</th>
        <th>备注</th>
      <th>创建时间<hr/>更新时间</th>
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

  <script src="{{asset('js/jquery-3.3.1.min.js')}}"></script>
  <script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.all.js')}}"></script>
  {{--<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.js')}}"></script>--}}
  @include('public.dynamic_list_foot')

  <script type="text/javascript">
      var OPERATE_TYPE = <?php echo isset($operate_type)?$operate_type:0; ?>;
      var AUTO_READ_FIRST = false;//自动读取第一页 true:自动读取 false:指定地方读取
      var LIST_FUNCTION_NAME = "reset_list_self";// 列表刷新函数名称, 需要列表刷新同步时，使用自定义方法reset_list_self；异步时没有必要自定义
      var AJAX_URL = "{{ url('api/admin/invoice_template/ajax_alist') }}";//ajax请求的url
      var ADD_URL = "{{ url('admin/invoice_template/add/0') }}"; //添加url

      var IFRAME_MODIFY_URL = "{{url('admin/invoice_template/add/')}}/";//添加/修改页面地址前缀 + id
      var IFRAME_MODIFY_URL_TITLE = "发票开票模板" ;// 详情弹窗显示提示  [添加/修改] +  栏目/主题
      var IFRAME_MODIFY_CLOSE_OPERATE = 0 ;// 详情弹窗operate_num关闭时的操作0不做任何操作1刷新当前页面2刷新当前列表页面

      var SHOW_URL = "{{url('admin/invoice_template/info/')}}/";//显示页面地址前缀 + id
      var SHOW_URL_TITLE = "" ;// 详情弹窗显示提示
      var SHOW_CLOSE_OPERATE = 0 ;// 详情弹窗operate_num关闭时的操作0不做任何操作1刷新当前页面2刷新当前列表页面
      var EDIT_URL = "{{url('admin/invoice_template/add/')}}/";//修改页面地址前缀 + id
      var DEL_URL = "{{ url('api/admin/invoice_template/ajax_del') }}";//删除页面地址
      var BATCH_DEL_URL = "{{ url('api/admin/invoice_template/ajax_del') }}";//批量删除页面地址
      var EXPORT_EXCEL_URL = "{{ url('admin/invoice_template/export') }}";//导出EXCEL地址
      var IMPORT_EXCEL_TEMPLATE_URL = "{{ url('admin/invoice_template/import_template') }}";//导入EXCEL模版地址
      var IMPORT_EXCEL_URL = "{{ url('api/admin/invoice_template/import') }}";//导入EXCEL地址
      var IMPORT_EXCEL_CLASS = "import_file";// 导入EXCEL的file的class

      // 列表数据每隔指定时间就去执行一次刷新【如果表有更新时】--定时执行
      var IFRAME_TAG_KEY = "";// "QualityControl\\CTAPIStaff";// 获得模型表更新时间的关键标签，可为空：不获取
      var IFRAME_TAG_TIMEOUT = 60000;// 获得模型表更新时间运行间隔 1000:1秒 ；可以不要此变量：默认一分钟

      // hidden_option 8192:调用父窗品的方法：[public/js目录下的] 项目目录+数据功能目录+当前文件名称 【有_线，则去掉】
      // 其它地方弹出此窗，保存完成时调用的父窗口方法名称 参数(obj:当前表单值对像, result:保存接口返回的结果，operateNum:自己定义的一个编号【页面有多处用到时用--通知父窗口调用位置】)
      var PARENT_BUSINESS_FUN_NAME = "adminQualityControlRrrDdddedit";


  </script>
  <script src="{{asset('js/common/list.js')}}?1"></script>
  <script src="{{ asset('js/admin/QualityControl/InvoiceTemplate.js') }}?3"  type="text/javascript"></script>
</body>
</html>
