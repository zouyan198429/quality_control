

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
      <a href="javascript:void(0);" class="on" onclick="action.iframeModify(0)">添加课程</a>
    </div>
    <form onsubmit="return false;" class="form-horizontal" style="display: block;" role="form" method="post" id="search_frm" action="#">
      <div class="msearch fr">
        <input type="text" value=""    name="keyword"  placeholder="请输入关键字"/>
        <button class="btn btn-normal search_frm">搜索</button>
      </div>
    </form>
  </div>

    <div class="layui-table-box">
        <table cellspacing="0" cellpadding="0" lay-size="sm" lay-skin="line" border="0"   class="layui-table">
            <colgroup>
                <col width="60">
                <col width="">
                <col width="100">
                <col width="120">
                <col width="120">
                <col width="90">
                <col width="90">
                <col width="100">
                <col>
            </colgroup>
            <thead>
            <tr>
                <th>ID</th>
                <th>课程</th>
                <td>创建时间</td>
                <td>最后修改</td>
                <td>浏览量</td>
                <td>报名池人数</td>
                <th>状态</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            <tr data-index="0" class="">
                <td> 1</td>
                <td><a href="trainedit.html">内审员初次取证</a></td>
                <td> 2020-02-22</td>
                <td> 2020-02-22</td>
                <td> 651</td>
                <td> 73 </td>
                <td>正常</td>
                <td >
                    <a class="layui-btn layui-btn-normal layui-btn-xs" href="trainedit.html" lay-event="edit"><i class="layui-icon layui-icon-file"></i>编辑</a>
                </td>
            </tr>
            <tr data-index="0" class="">
                <td> 1</td>
                <td><a href="trainedit.html">内审员初次取证</a></td>
                <td> 2020-02-22</td>
                <td> 2020-02-22</td>
                <td> 651</td>
                <td> 73 </td>
                <td>已下架</td>
                <td >
                    <a class="layui-btn layui-btn-normal layui-btn-xs" href="trainedit.html" lay-event="edit"><i class="layui-icon layui-icon-file"></i>编辑</a>
                </td>
            </tr>
            </tbody>
        </table>
    </div>

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
      var ADD_URL = "{{ url('admin/face_to_face_training/create') }}"; //添加url
      var IFRAME_MODIFY_URL = "{{url('admin/face_to_face_training/create')}}/";//添加/修改页面地址前缀 + id
      var IFRAME_MODIFY_URL_TITLE = "项目" ;// 详情弹窗显示提示  [添加/修改] +  栏目/主题
      var IFRAME_MODIFY_CLOSE_OPERATE = 0 ;// 详情弹窗operate_num关闭时的操作0不做任何操作1刷新当前页面2刷新当前列表页面

      var SHOW_URL = "{{url('admin/abilitys/info/')}}/";//显示页面地址前缀 + id
      var SHOW_URL_TITLE = "详情" ;// 详情弹窗显示提示
      var SHOW_CLOSE_OPERATE = 0 ;// 详情弹窗operate_num关闭时的操作0不做任何操作1刷新当前页面2刷新当前列表页面
      var EDIT_URL = "{{url('admin/abilitys/add/')}}/";//修改页面地址前缀 + id

      // 列表数据每隔指定时间就去执行一次刷新【如果表有更新时】--定时执行
      var IFRAME_TAG_KEY = "";// "QualityControl\\CTAPIStaff";// 获得模型表更新时间的关键标签，可为空：不获取
      var IFRAME_TAG_TIMEOUT = 60000;// 获得模型表更新时间运行间隔 1000:1秒 ；可以不要此变量：默认一分钟

  </script>
  <script src="{{asset('js/common/list.js')}}"></script>
  <script src="{{ asset('js/admin/QualityControl/Abilitys.js') }}?824"  type="text/javascript"></script>
</body>
</html>
