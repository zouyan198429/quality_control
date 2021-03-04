

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
      <a href="javascript:void(0);" class="on fa fa-plus" onclick="action.iframeModify(0)">添加会员等级配置</a>
    </div>
    <form onsubmit="return false;" class="form-horizontal" style="display: none;" role="form" method="post" id="search_frm" action="#">
      <div class="msearch fr">
          <input type="hidden" name="hidden_option"  value="{{ $hidden_option ?? 0 }}" />
          <input type="hidden" name="company_hidden"  value="{{ $company_hidden ?? 0 }}" />
          <span   @if (isset($company_hidden) && $company_hidden == 1 ) style="display: none;"  @endif>
                <input type="hidden" class="select_id" name="company_id"  value="{{ $info['company_id'] ?? '' }}" />
                <span class="select_name company_name">{{ $info['user_company_name'] ?? '' }}</span>
                <i class="close select_close company_id_close"  onclick="clearSelect(this)" style="display: none;">×</i>
                <button  type="button"  class="btn btn-danger  btn-xs ace-icon fa fa-plus-circle bigger-60"  onclick="otheraction.selectCompany(this)">选择企业</button>
          </span>

        <select class="wmini" name="company_grade_add">
          <option value="">会员等级[创建记录时]</option>
          @foreach ($companyGradeAdd as $k=>$txt)
            <option value="{{ $k }}"  @if(isset($defaultCompanyGradeAdd) && $defaultCompanyGradeAdd == $k) selected @endif >{{ $txt }}</option>
          @endforeach
        </select>
          <select class="wmini" name="company_grade_final">
              <option value="">会员等级[变更时]</option>
              @foreach ($companyGradeFinal as $k=>$txt)
                  <option value="{{ $k }}"  @if(isset($defaultCompanyGradeFinal) && $defaultCompanyGradeFinal == $k) selected @endif >{{ $txt }}</option>
              @endforeach
          </select>
          <select class="wmini" name="company_grade">
              <option value="">会员等级[最终]</option>
              @foreach ($companyGrade as $k=>$txt)
                  <option value="{{ $k }}"  @if(isset($defaultCompanyGrade) && $defaultCompanyGrade == $k) selected @endif >{{ $txt }}</option>
              @endforeach
          </select>
          <select class="wmini" name="open_status">
              <option value="">审核状态</option>
              @foreach ($openStatus as $k=>$txt)
                  <option value="{{ $k }}"  @if(isset($defaultOpenStatus) && $defaultOpenStatus == $k) selected @endif >{{ $txt }}</option>
              @endforeach
          </select>
          <select class="wmini" name="valid_status">
              <option value="">有效状态</option>
              @foreach ($validStatus as $k=>$txt)
                  <option value="{{ $k }}"  @if(isset($defaultValidStatus) && $defaultValidStatus == $k) selected @endif >{{ $txt }}</option>
              @endforeach
          </select>

        <select style="width:80px; height:28px;" name="field">
          <option value="remarks">备注说明</option>
        </select>
        <input type="text" value=""    name="keyword"  placeholder="请输入关键字"/>
        <button class="btn btn-normal search_frm">搜索</button>
      </div>
    </form>
  </div>
<!--
  <div class="table-header">
{{--    { {--<button class="btn btn-danger  btn-xs batch_del"  onclick="action.batchDel(this)">批量删除</button>--} }--}}
{{--    <button class="btn btn-success  btn-xs export_excel"  onclick="action.batchExportExcel(this)" >导出[按条件]</button>--}}
{{--    <button class="btn btn-success  btn-xs export_excel"  onclick="action.exportExcel(this)" >导出[勾选]</button>--}}
{{--    <button class="btn btn-success  btn-xs import_excel"  onclick="action.importExcelTemplate(this)">导入模版[EXCEL]</button>--}}
{{--    <button class="btn btn-success  btn-xs import_excel"  onclick="action.importExcel(this)">导入城市</button>--}}
{{--    <div style="display:none;" ><input type="file" class="import_file img_input"></div>{ {--导入file对象--} }--}}
    <button class="btn btn-success  btn-xs export_excel"  onclick="action.smsByIds(this, 0, 0, 1, 0, 0)" >发送短信[按条件]</button>
        <button class="btn btn-success  btn-xs export_excel"  onclick="action.smsSelected(this, 0, 2, 0, 0)" >发送短信[勾选]</button>
      <button class="btn btn-success fa fa-check-circle btn-xs export_excel"  onclick="otheraction.openSelected(this, 2)" >审核通过[勾选]</button>
      <button class="btn btn-success fa fa-window-close btn-xs export_excel"  onclick="otheraction.openSelected(this, 4)" >审核不通过[勾选]</button>
  </div> -->
  <table lay-even class="layui-table table2 tableWidthFixed"  lay-size="lg"  id="dynamic-table">
    <colgroup>
        <col>
        <col width="90">
        <col width="150">
        <col width="100">
        <col width="80">
        <col width="150">
        <col width="140">
    </colgroup>
    <thead>
    <tr>
     <!-- <th>
        <label class="pos-rel">
          <input type="checkbox"  class="ace check_all"  value="" onclick="action.seledAll(this)"/>
        </label>
      </th> -->
{{--      <th>ID</th>--}}
      <th>所属企业</th>
        <!-- <th>会员等级[添加时]<hr/>会员等级[生效时]</th> -->
      <th>会员等级</th>
        <th>开始时间<hr/>结束时间</th>
        <!-- <th>真实结束时间</th> -->
        <th>备注说明</th>
        <th>审核状态<hr/>有效状态</th>
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
      var AJAX_URL = "{{ url('api/admin/company_grade_config/ajax_alist') }}";//ajax请求的url
      var ADD_URL = "{{ url('admin/company_grade_config/add/0') }}"; //添加url

      var IFRAME_MODIFY_URL = "{{url('admin/company_grade_config/add/')}}/";//添加/修改页面地址前缀 + id
      var IFRAME_MODIFY_URL_TITLE = "会员等级配置" ;// 详情弹窗显示提示  [添加/修改] +  栏目/主题
      var IFRAME_MODIFY_CLOSE_OPERATE = 0 ;// 详情弹窗operate_num关闭时的操作0不做任何操作1刷新当前页面2刷新当前列表页面

      var SHOW_URL = "{{url('admin/company_grade_config/info/')}}/";//显示页面地址前缀 + id
      var SHOW_URL_TITLE = "" ;// 详情弹窗显示提示
      var SHOW_CLOSE_OPERATE = 0 ;// 详情弹窗operate_num关闭时的操作0不做任何操作1刷新当前页面2刷新当前列表页面
      var EDIT_URL = "{{url('admin/company_grade_config/add/')}}/";//修改页面地址前缀 + id
      var DEL_URL = "{{ url('api/admin/company_grade_config/ajax_del') }}";//删除页面地址
      var BATCH_DEL_URL = "{{ url('api/admin/company_grade_config/ajax_del') }}";//批量删除页面地址
      var EXPORT_EXCEL_URL = "{{ url('admin/company_grade_config/export') }}";//导出EXCEL地址
      var IMPORT_EXCEL_TEMPLATE_URL = "{{ url('admin/company_grade_config/import_template') }}";//导入EXCEL模版地址
      var IMPORT_EXCEL_URL = "{{ url('api/admin/company_grade_config/import') }}";//导入EXCEL地址
      var IMPORT_EXCEL_CLASS = "import_file";// 导入EXCEL的file的class
      var SMS_SEND_PAGE_URL = "{{url('admin/rrr_dddd/sms_send')}}";// 选择短信模板页面
      var SMS_SEND_URL = "{{url('api/admin/rrr_dddd/ajax_sms_send')}}";// 短信模板发送短信

      // 列表数据每隔指定时间就去执行一次刷新【如果表有更新时】--定时执行
      var IFRAME_TAG_KEY = "";// "QualityControl\\CTAPIStaff";// 获得模型表更新时间的关键标签，可为空：不获取
      var IFRAME_TAG_TIMEOUT = 60000;// 获得模型表更新时间运行间隔 1000:1秒 ；可以不要此变量：默认一分钟

      // hidden_option 8192:调用父窗品的方法：[public/js目录下的] 项目目录+数据功能目录+当前文件名称 【有_线，则去掉】
      // 其它地方弹出此窗，保存完成时调用的父窗口方法名称 参数(obj:当前表单值对像, result:保存接口返回的结果，operateNum:自己定义的一个编号【页面有多处用到时用--通知父窗口调用位置】)
      var PARENT_BUSINESS_FUN_NAME = "adminQualityControlRrrDdddedit";


      var SELECT_COMPANY_URL = "{{url('admin/company/select')}}";// 选择所属企业

      var OPEN_OPERATE_URL = "{{ url('api/admin/company_grade_config/ajax_open') }}";//审核操作(通过/不通过)

  </script>
  <script src="{{asset('js/common/list.js')}}?1"></script>
  <script src="{{ asset('js/admin/QualityControl/CompanyGradeConfig.js') }}?14"  type="text/javascript"></script>
</body>
</html>
