<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>面授培训-近期课程</title>
  <meta name="renderer" content="webkit">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
  @include('admin.layout_public.pagehead')
  <link rel="stylesheet" href="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/css/layui.css')}}" media="all">
  <link rel="stylesheet" href="{{asset('layui-admin-v1.2.1/src/layuiadmin/style/admin.css')}}" media="all">
</head>
<body>
<div class="layui-fluid">
  <div class="layui-row">
      <div class="layui-col-md12">

        <div class="layui-card">

          @include('common.pageParams')

          <div class="layui-card-header">
              <h3 style="width:120px; float: left;">面授培训</h3>
          </div>
          <form onsubmit="return false;" class="form-horizontal" style="display: none;" role="form" method="post" id="search_frm" action="#">
            <div class="msearch fr">
              <select style="width:80px; height:28px;" name="field">
                <option value="ability_name">检测项目</option>
              </select>
              <input type="text" value=""    name="keyword"  placeholder="请输入关键字"/>
              <button class="btn btn-normal search_frm">搜索</button>
            </div>
          </form>
          <div class="layui-card-body">
              <table lay-even class="layui-table table2 tableWidthFixed"  lay-size="lg"  id="dynamic-table">
                <colgroup>
                    <col width="100">
                    <col>
                    <col width="150">
                    <col width="150">
                    <col width="150">
                </colgroup>
                <thead>
                <tr>
                  <th>图片</th>
                  <th>课程</th>
                  <th>项目状态</th>
                  <th>我的参与</th>
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
<img src="" alt="">
  <script src="{{asset('js/jquery-3.3.1.min.js')}}"></script>
  <script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.all.js')}}"></script>
  @include('public.dynamic_list_foot')

  <script type="text/javascript">
      var OPERATE_TYPE = <?php echo isset($operate_type)?$operate_type:0; ?>;
      var AUTO_READ_FIRST = false;//自动读取第一页 true:自动读取 false:指定地方读取
      var LIST_FUNCTION_NAME = "reset_list_self";// 列表刷新函数名称, 需要列表刷新同步时，使用自定义方法reset_list_self；异步时没有必要自定义
      var AJAX_URL = "{{ url('api/company/courses/ajax_list') }}";//ajax请求的url

      var IFRAME_MODIFY_URL_TITLE = "项目" ;// 详情弹窗显示提示  [添加/修改] +  栏目/主题
      var IFRAME_MODIFY_CLOSE_OPERATE = 0 ;// 详情弹窗operate_num关闭时的操作0不做任何操作1刷新当前页面2刷新当前列表页面

      var FORM_URL = "{{ url('company/courses/sign-up/form') }}/";//显示页面地址前缀 + id
      var SHOW_URL_TITLE = "详情" ;// 详情弹窗显示提示
      var SHOW_CLOSE_OPERATE = 0 ;// 详情弹窗operate_num关闭时的操作0不做任何操作1刷新当前页面2刷新当前列表页面

      var SIGN_UP_URL = "{{ url('company/courses/sign-up/') }}/";//报名地址
      // 列表数据每隔指定时间就去执行一次刷新【如果表有更新时】--定时执行
      var IFRAME_TAG_KEY = "";// "QualityControl\\CTAPIStaff";// 获得模型表更新时间的关键标签，可为空：不获取
      var IFRAME_TAG_TIMEOUT = 60000;// 获得模型表更新时间运行间隔 1000:1秒 ；可以不要此变量：默认一分钟

  </script>
  <script src="{{asset('js/common/list.js')}}"></script>
  <script src="{{ asset('js/company/QualityControl/Course.js') }}"  type="text/javascript"></script>
</body>
</html>
