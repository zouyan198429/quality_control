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

{{--<div id="crumb"><i class="fa fa-reorder fa-fw" aria-hidden="true"></i> 我的同事</div>--}}
<div class="mm">
  <div class="mmhead" id="mywork">

    @include('common.pageParams')
    <div class="tabbox" >
      <a href="javascript:void(0);" class="on" onclick="action.iframeModify(0)">添加课程</a>
    </div>
    <form onsubmit="return false;" class="form-horizontal" style="display: block;" role="form" method="post" id="search_frm" action="#">
      <div class="msearch fr">

        <select class="wmini" name="status_online">
          <option value="">上架状态</option>
          @foreach ($statusOnline as $k=>$txt)
            <option value="{{ $k }}"  @if(isset($defaultStatusOnline) && $defaultStatusOnline == $k) selected @endif >{{ $txt }}</option>
          @endforeach
        </select>
          <select class="wmini" name="pay_config_id" style="width: 80px;">
              <option value="">收款帐号</option>
              @foreach ($pay_config_kv as $k=>$txt)
                  <option value="{{ $k }}"  @if(isset($defaultPayConfig) && $defaultPayConfig == $k) selected @endif >{{ $txt }}</option>
              @endforeach
          </select>
          <select class="wmini" name="pay_method" style="width: 80px;">
              <option value="">收款方式</option>
              @foreach ($payMethod as $k=>$txt)
                  <option value="{{ $k }}"  @if(isset($defaultPayMethod) && $defaultPayMethod == $k) selected @endif >{{ $txt }}</option>
              @endforeach
          </select>
        <select style="width:80px; height:28px;" name="field">
          <option value="course_name">课程名称</option>
          <option value="explain_remarks">简要概述</option>
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
        <col width="90">
        <col width="15%">
        <col width="120">
        <col width="95">
        <col width="95">
        <col width="100">
        <col width="95">
        <col width="140">
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
      <th>课程名称</th>
        <th>图片</th>
        <th>收款帐号<hr/>收款开通类型</th>
        <th>收费标准(会员)<hr/>收费标准<br/>(非会员)</th>
        <th>报名池人数<hr/>已分班人数</th>
        <th>已作废人数</th>
        <th>浏览量<hr/>上架状态</th>
        <th>创建时间<hr/>更新时间</th>
      <th>操作</th>
    </tr>
    </thead>
    <tbody id="data_list"  class=" baguetteBoxOne gallery" >
    </tbody>
  </table>
  <div class="mmfoot">
    <div class="mmfleft"></div>
    <div class="pagination">
    </div>
  </div>

</div>
<a href="javascript:void(0);" class="btn btn-success  btn-xs  on" id="testBTN" style="display: none;">测试</a>

  <script src="{{asset('js/jquery-3.3.1.min.js')}}"></script>
  <script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.all.js')}}"></script>
  {{--<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.js')}}"></script>--}}
  @include('public.dynamic_list_foot')

  <script type="text/javascript">
      var OPERATE_TYPE = <?php echo isset($operate_type)?$operate_type:0; ?>;
      var AUTO_READ_FIRST = false;//自动读取第一页 true:自动读取 false:指定地方读取
      var LIST_FUNCTION_NAME = "reset_list_self";// 列表刷新函数名称, 需要列表刷新同步时，使用自定义方法reset_list_self；异步时没有必要自定义
      var AJAX_URL = "{{ url('api/admin/course/ajax_alist') }}";//ajax请求的url
      var ADD_URL = "{{ url('admin/course/add/0') }}"; //添加url

      var IFRAME_MODIFY_URL = "{{url('admin/course/add/')}}/";//添加/修改页面地址前缀 + id
      var IFRAME_MODIFY_URL_TITLE = "课程" ;// 详情弹窗显示提示  [添加/修改] +  栏目/主题
      var IFRAME_MODIFY_CLOSE_OPERATE = 0 ;// 详情弹窗operate_num关闭时的操作0不做任何操作1刷新当前页面2刷新当前列表页面

      var SHOW_URL = "{{url('admin/course/info/')}}/";//显示页面地址前缀 + id
      var SHOW_URL_TITLE = "" ;// 详情弹窗显示提示
      var SHOW_CLOSE_OPERATE = 0 ;// 详情弹窗operate_num关闭时的操作0不做任何操作1刷新当前页面2刷新当前列表页面
      var EDIT_URL = "{{url('admin/course/add/')}}/";//修改页面地址前缀 + id
      var DEL_URL = "{{ url('api/admin/course/ajax_del') }}";//删除页面地址
      var BATCH_DEL_URL = "{{ url('api/admin/course/ajax_del') }}";//批量删除页面地址
      var EXPORT_EXCEL_URL = "{{ url('admin/course/export') }}";//导出EXCEL地址
      var IMPORT_EXCEL_TEMPLATE_URL = "{{ url('admin/course/import_template') }}";//导入EXCEL模版地址
      var IMPORT_EXCEL_URL = "{{ url('api/admin/course/import') }}";//导入EXCEL地址
      var IMPORT_EXCEL_CLASS = "import_file";// 导入EXCEL的file的class

      var DOWN_FILE_URL = "{{ url('admin/down_file') }}";// 下载
      var DEL_FILE_URL = "{{ url('api/admin/upload/ajax_del') }}";// 删除文件的接口地址

      // 列表数据每隔指定时间就去执行一次刷新【如果表有更新时】--定时执行
      var IFRAME_TAG_KEY = "";// "QualityControl\\CTAPIStaff";// 获得模型表更新时间的关键标签，可为空：不获取
      var IFRAME_TAG_TIMEOUT = 60000;// 获得模型表更新时间运行间隔 1000:1秒 ；可以不要此变量：默认一分钟

  </script>

<link rel="stylesheet" href="{{asset('js/baguetteBox.js/baguetteBox.min.css')}}">
<script src="{{asset('js/baguetteBox.js/baguetteBox.min.js')}}" async></script>
{{--<script src="{{asset('js/baguetteBox.js/highlight.min.js')}}" async></script>--}}
<!-- zui js -->
<script src="{{asset('dist/js/zui.min.js') }}"></script>

<script src="{{asset('js/common/list.js')}}?1"></script>
  <script src="{{ asset('js/admin/QualityControl/Course.js') }}?5"  type="text/javascript"></script>
@component('component.upfileincludejsmany')
@endcomponent
</body>
</html>

<script type="text/ecmascript" src="{{asset('static/js/sha1.js')}}"></script>
<script type="text/ecmascript" src="{{asset('static/js/md5/md5.js')}}"></script>
<script type="text/ecmascript" src="{{asset('static/js/sign.js')}}"></script>
<script type="text/javascript">

    var TEST_URL = "{{ url('api/admin/testAPI') }}";// 测试地址
    $(function(){
        $(document).on("click","#testBTN",function(){
            var obj = $(this);
            // 测试
            let dataParams = {'name' : '小明', 'age' : 25, 'sex': 1};
            // $sign = getSignByObj(dataParams, '123456789', '111222333', 1);
            // console.log('~~~~~~~~$sign~~~~~~~~~~~~~~~~', $sign);
            console.log('~~~~~~~~dataParams~~~~~~~~~~~~~~~~', dataParams);
            signTest(dataParams);
            console.log('~~~~~~~~API后~~~~~~~~~~~~~~~~', dataParams);
            return false;
        });
    });

    // 签名请求测试
    function signTest(data){
        // var data = {'id':id};
        console.log(TEST_URL);
        console.log(data);
        data['sign'] = getSignByObj(data, '123456789', '111222333', 1);
        var layer_index = layer.load();
        $.ajax({
            'type' : 'POST',
            'url' : TEST_URL,
            'headers':get_ajax_headers({}, ADMIN_AJAX_TYPE_NUM),
            'data' : data,
            'dataType' : 'json',
            'success' : function(ret){
                console.log(ret);
                if(!ret.apistatus){//失败
                    //alert('失败');
                    err_alert(ret.errorMsg);
                }else{//成功
                    // reset_list_self(true,false,true, 2);
                    layer_alert('操作成功',1,0)
                }
                layer.close(layer_index)//手动关闭
            }
        })
    }
</script>
