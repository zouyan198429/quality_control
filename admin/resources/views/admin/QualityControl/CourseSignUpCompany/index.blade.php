<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>报名学员</title>
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
        <form onsubmit="return false;" class="form-horizontal" style="display: block;" role="form" method="post" id="search_frm" action="#">
            <div class="msearch fr">

                {{--<select class="wmini" name="province_id">--}}
                {{--<option value="">全部</option>--}}
                {{--@foreach ($province_kv as $k=>$txt)--}}
                {{--<option value="{{ $k }}"  @if(isset($province_id) && $province_id == $k) selected @endif >{{ $txt }}</option>--}}
                {{--@endforeach--}}
                {{--</select>--}}

                <select class="wmini" name="class_id" style="width: 80px;">
                    <option value="">选择班次</option>
                    @foreach ($classes as $k=>$txt)
                        <option value="{{ $k }}">{{ $txt }}</option>
                    @endforeach
                </select>
                <input type="text" value=""    name="keyword"  placeholder="请输入关键字"/>
                <button class="btn btn-normal search_frm">搜索</button>
            </div>
        </form>
    </div>
    <table lay-even class="layui-table table2 tableWidthFixed"  lay-size="lg"  id="dynamic-table">
            <colgroup>
                <col width="50">
                <col width="160">
                <col width="160">
                <col>
                <col>
                <col width="150">
                <col width="150">
                <col width="150">
            </colgroup>
            <thead>
            <tr>
                <th>
                    <label class="pos-rel">
                        <input type="checkbox"  class="ace check_all"  value="" onclick="action.seledAll(this)"/>
                    </label>
                </th>
                <th>姓名</th>
                <th>电话</th>
                <th>单位</th>
                <th>培训班</th>
                <th>是否缴费</th>
                <th>报名时间</th>
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
  @include('public.dynamic_list_foot')

  <script type="text/javascript">
      var OPERATE_TYPE = <?php echo isset($operate_type)?$operate_type:0; ?>;
      var AUTO_READ_FIRST = false;//自动读取第一页 true:自动读取 false:指定地方读取
      var LIST_FUNCTION_NAME = "reset_list_self";// 列表刷新函数名称, 需要列表刷新同步时，使用自定义方法reset_list_self；异步时没有必要自定义
      var AJAX_URL = "{{ url('api/admin/courses/sign_up/company/ajax_list') }}";//ajax请求的url
      var ADD_URL = "{{ url('admin/courses/create/0') }}"; //添加url
      var PAID_URL = "{{url('api/admin/courses/sign_up/company/update')}}";// 完成缴费

      var IFRAME_MODIFY_URL = "{{url('admin/courses/create/')}}/";//添加/修改页面地址前缀 + id
      var IFRAME_MODIFY_URL_TITLE = "课程" ;// 详情弹窗显示提示  [添加/修改] +  栏目/主题
      var IFRAME_MODIFY_CLOSE_OPERATE = 0 ;// 详情弹窗operate_num关闭时的操作0不做任何操作1刷新当前页面2刷新当前列表页面
  </script>
  <script src="{{asset('js/common/list.js')}}"></script>
  <script src="{{ asset('js/admin/QualityControl/CourseSignUpCompany.js') }}?1"  type="text/javascript"></script>
</body>
</html>
