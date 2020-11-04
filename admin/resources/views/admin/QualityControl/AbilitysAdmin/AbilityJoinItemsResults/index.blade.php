

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
{{--    <div class="tabbox" >--}}
{{--      <a href="javascript:void(0);" class="on" onclick="action.iframeModify(0)">添加项目</a>--}}
{{--    </div>--}}
    <form onsubmit="return false;" class="form-horizontal" style="display: block;" role="form" method="post" id="search_frm" action="#">
      <div class="msearch fr">
          <input type="hidden" name="operate_num" value="{{ $operate_num ?? 0 }}" />

          <span>
                <input type="hidden" class="select_id" name="company_id"  value="{{ $info['company_id'] ?? '' }}" />
                <span class="select_name company_name">{{ $info['user_company_name'] ?? '' }}</span>
                <i class="close select_close company_id_close"  onclick="clearSelect(this)" style="display: none;">×</i>
                <button  type="button"  class="btn btn-danger  btn-xs ace-icon fa fa-plus-circle bigger-60"  onclick="otheraction.selectCompany(this)">选择企业</button>
          </span>

          <select class="wmini" name="retry_no" style="width: 80px;display:none;">
              <option value="">测试次序</option>
              @foreach ($retryNo as $k=>$txt)
                  <option value="{{ $k }}"  @if(isset($defaultRetryNo) && $defaultRetryNo == $k) selected @endif >{{ $txt }}</option>
              @endforeach
          </select>
          <select class="wmini" name="status" style="width: 80px;">
              <option value="">状态</option>
              @foreach ($status as $k=>$txt)
                  <option value="{{ $k }}"  @if(isset($defaultStatus) && $defaultStatus == $k) selected @endif >{{ $txt }}</option>
              @endforeach
          </select>
          <select class="wmini" name="result_status" style="width: 80px;">
              <option value="">验证结果</option>
              @foreach ($resultStatus as $k=>$txt)
                  <option value="{{ $k }}"  @if(isset($defaultResultStatus) && $defaultResultStatus == $k) selected @endif >{{ $txt }}</option>
              @endforeach
          </select>
          <select class="wmini" name="is_sample" style="width: 80px;">
              <option value="">是否取样</option>
              @foreach ($isSample as $k=>$txt)
                  <option value="{{ $k }}"  @if(isset($defaultIsSample) && $defaultIsSample == $k) selected @endif >{{ $txt }}</option>
              @endforeach
          </select>
          <select class="wmini" name="submit_status" style="width: 80px; @if(isset($operate_num) && in_array($operate_num, [2,4])) display: none;  @endif">
              <option value="">是否上传数据</option>
              @foreach ($submitStatus as $k=>$txt)
                  <option value="{{ $k }}"  @if(isset($defaultSubmitStatus) && $defaultSubmitStatus == $k) selected @endif >{{ $txt }}</option>
              @endforeach
          </select>
          <select class="wmini" name="judge_status" style="width: 80px;">
              <option value="">是否评定</option>
              @foreach ($judgeStatus as $k=>$txt)
                  <option value="{{ $k }}"  @if(isset($defaultJudgeStatus) && $defaultJudgeStatus == $k) selected @endif >{{ $txt }}</option>
              @endforeach
          </select>
        <select style="width:120px; height:28px;" name="field">
          <option value="ability_code">能力验证代码</option>
            <option value="contacts">联系人姓名</option>
            <option value="mobile">联系人手机</option>
            <option value="tel">联系人电话</option>
        </select>
        <input type="text" value=""    name="keyword"  placeholder="请输入关键字"/>
        <button class="btn btn-normal search_frm">搜索</button>
      </div>
    </form>
  </div>

    @if(isset($operate_num) && in_array($operate_num, [2,4]))
    <div class="table-header">
      {{--<button class="btn btn-danger  btn-xs batch_del"  onclick="action.batchDel(this)">批量删除</button>--}}
      <button class="btn btn-success  btn-xs export_excel"  onclick="action.batchExportExcel(this)" >导出[按条件]</button>
      <button class="btn btn-success  btn-xs export_excel"  onclick="action.exportExcel(this)" >导出[勾选]</button>
{{--      <button class="btn btn-success  btn-xs import_excel"  onclick="action.importExcelTemplate(this)">导入模版[EXCEL]</button>--}}
{{--      <button class="btn btn-success  btn-xs import_excel"  onclick="action.importExcel(this)">导入城市</button>--}}
{{--      <div style="display:none;" ><input type="file" class="import_file img_input"></div>{ {--导入file对象--} }--}}
    </div>
    @endif
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
        <th>能力验证代码</th>
        <th>单位</th>
        <th>联系人</th>
        <th>联系人手机</th>
        <th>报名时间</th>
        <th>是否取样<hr/>取样时间</th>
        <th>是否提交<hr/>提交时间</th>
        <th>是否评定<hr/>评定时间</th>
        <th>状态<hr/>验证结论</th>
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
      var AJAX_URL = "{{ url('api/admin/abilitys_admin/' . ($ability_id ?? 0)  . '/ability_join_items_results/ajax_alist') }}";//ajax请求的url
      var ADD_URL = "{{ url('admin/abilitys_admin/' . ($ability_id ?? 0)  . '/ability_join_items_results/add/0') }}"; //添加url

      var IFRAME_MODIFY_URL = "{{url('admin/abilitys_admin/' . ($ability_id ?? 0)  . '/ability_join_items_results/add/')}}/";//添加/修改页面地址前缀 + id
      var IFRAME_MODIFY_URL_TITLE = "项目" ;// 详情弹窗显示提示  [添加/修改] +  栏目/主题
      var IFRAME_MODIFY_CLOSE_OPERATE = 0 ;// 详情弹窗operate_num关闭时的操作0不做任何操作1刷新当前页面2刷新当前列表页面

      var SHOW_URL = "{{url('admin/abilitys_admin/' . ($ability_id ?? 0)  . '/ability_join_items_results/info/')}}/";//显示页面地址前缀 + id
      var SHOW_URL_TITLE = "详情" ;// 详情弹窗显示提示
      var SHOW_CLOSE_OPERATE = 0 ;// 详情弹窗operate_num关闭时的操作0不做任何操作1刷新当前页面2刷新当前列表页面
      var EDIT_URL = "{{url('admin/abilitys_admin/' . ($ability_id ?? 0)  . '/ability_join_items_results/add/')}}/";//修改页面地址前缀 + id
      var DEL_URL = "{{ url('api/admin/abilitys_admin/' . ($ability_id ?? 0)  . '/ability_join_items_results/ajax_del') }}";//删除页面地址
      var BATCH_DEL_URL = "{{ url('api/admin/abilitys_admin/' . ($ability_id ?? 0)  . '/ability_join_items_results/ajax_del') }}";//批量删除页面地址
      var EXPORT_EXCEL_URL = "{{ url('admin/abilitys_admin/' . ($ability_id ?? 0)  . '/ability_join_items_results/export') }}";//导出EXCEL地址
      var IMPORT_EXCEL_TEMPLATE_URL = "{{ url('admin/abilitys_admin/' . ($ability_id ?? 0)  . '/ability_join_items_results/import_template') }}";//导入EXCEL模版地址
      var IMPORT_EXCEL_URL = "{{ url('api/admin/abilitys_admin/' . ($ability_id ?? 0)  . '/ability_join_items_results/import') }}";//导入EXCEL地址
      var IMPORT_EXCEL_CLASS = "import_file";// 导入EXCEL的file的class

      var IFRAME_SAMPLE_URL = "{{url('admin/abilitys_admin/' . ($ability_id ?? 0)  . '/ability_join_items_results/get_sample/')}}/";//添加/修改页面地址前缀 + id

      var IFRAME_SAMPLE_RESULT_INFO_URL = "{{url('admin/abilitys_admin/' . ($ability_id ?? 0)  . '/ability_join_items/sample_result_info/')}}/";//显示页面地址前缀 + id / + retry_no

      var SAVE_DISSATISFIED_URL = "{{ url('api/admin/abilitys_admin/' . ($ability_id ?? 0)  . '/ability_join_items_results/ajax_save_dissatisfied') }}";// 已领样，未上传数据的--可以手动直接判断为不满意

      var SELECT_COMPANY_URL = "{{url('admin/company/select')}}";// 选择所属企业

      // 列表数据每隔指定时间就去执行一次刷新【如果表有更新时】--定时执行
      var IFRAME_TAG_KEY = "";// "QualityControl\\CTAPIStaff";// 获得模型表更新时间的关键标签，可为空：不获取
      var IFRAME_TAG_TIMEOUT = 60000;// 获得模型表更新时间运行间隔 1000:1秒 ；可以不要此变量：默认一分钟

  </script>
  <script src="{{asset('js/common/list.js')}}"></script>
  <script src="{{ asset('js/admin/QualityControl/AbilitysAdmin/AbilityJoinItemsResults.js') }}?22"  type="text/javascript"></script>
</body>
</html>
