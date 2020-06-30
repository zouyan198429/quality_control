<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>教育云助手</title>
    @include('web.layout_public.pagehead')
    <link rel="stylesheet" href="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/css/layui.css')}}" media="all">
</head>
<body style="background:#f1f3f3;">

@include('web.layout_public.header')

<div class="k50"></div>
<div class="wrap tc">

    @include('common.pageParams')

	<div class="main-nob">
		<div class="class-add">
 			<a href="{{url('web/classes/add')}}">
 				<i>+</i>
 				<p>创建班级</p>
 			</a>
 		</div>
 		<div class="class-add">
 			<a href="{{url('web/classes/join')}}">
 				<i>+</i>
 				<p>加入已有班级</p>
 			</a>
 		</div>
 		<div class="c"></div>
        @foreach ($teacherList as $teacher)
 		<div class="class-item">
 			<span class="icon-my">
                @if (isset($user_id) && $user_id == $teacher['staff_id']  && ($teacher['is_create_teacher'] == 1 || $teacher['is_head_master'] == 1))
                    <img src="{{asset('staticweb/images/icon-my.svg')}}" alt="">
                @endif
            </span>
 			<a href="{{url('web/students/' . $teacher['class_id']) }}">
	 			<i><img src="{{asset('staticweb/images/icon-class.png')}}" alt=""></i>
	 			<h3>{{ $teacher['class_info']['class_name'] ?? '' }}</h3>
	 			<p>老师：{{ $teacher['class_info']['teacher_num'] ?? '0' }}人</p>
	 			<p>学生：{{ $teacher['class_info']['student_num'] ?? '0' }}</p>
 			</a>
 		</div>
        @endforeach
	</div>
</div>


</body>
</html>

<script src="{{asset('js/jquery-3.3.1.min.js')}}"></script>
<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.all.js')}}"></script>
{{--<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.js')}}"></script>--}}
@include('public.dynamic_list_foot')

<script type="text/javascript">
    var OPERATE_TYPE = <?php echo isset($operate_type)?$operate_type:0; ?>;
    var AUTO_READ_FIRST = false;//自动读取第一页 true:自动读取 false:指定地方读取
    var LIST_FUNCTION_NAME = "reset_list_self";// 列表刷新函数名称, 需要列表刷新同步时，使用自定义方法reset_list_self；异步时没有必要自定义
    var AJAX_URL = "{{ url('api/admin/dogtools/classes/ajax_alist') }}";//ajax请求的url
    var ADD_URL = "{{ url('admin/dogtools/classes/add/0') }}"; //添加url

    var IFRAME_MODIFY_URL = "{{url('admin/dogtools/classes/add/')}}/";//添加/修改页面地址前缀 + id
    var IFRAME_MODIFY_URL_TITLE = "班级" ;// 详情弹窗显示提示  [添加/修改] +  栏目/主题
    var IFRAME_MODIFY_CLOSE_OPERATE = 0 ;// 详情弹窗operate_num关闭时的操作0不做任何操作1刷新当前页面2刷新当前列表页面

    var SHOW_URL = "{{url('admin/dogtools/classes/info/')}}/";//显示页面地址前缀 + id
    var SHOW_URL_TITLE = "" ;// 详情弹窗显示提示
    var SHOW_CLOSE_OPERATE = 0 ;// 详情弹窗operate_num关闭时的操作0不做任何操作1刷新当前页面2刷新当前列表页面
    var EDIT_URL = "{{url('admin/dogtools/classes/add/')}}/";//修改页面地址前缀 + id
    var DEL_URL = "{{ url('api/admin/dogtools/classes/ajax_del') }}";//删除页面地址
    var BATCH_DEL_URL = "{{ url('api/admin/dogtools/classes/ajax_del') }}";//批量删除页面地址
    var EXPORT_EXCEL_URL = "{{ url('admin/dogtools/classes/export') }}";//导出EXCEL地址
    var IMPORT_EXCEL_TEMPLATE_URL = "{{ url('admin/dogtools/classes/import_template') }}";//导入EXCEL模版地址
    var IMPORT_EXCEL_URL = "{{ url('api/admin/dogtools/classes/import') }}";//导入EXCEL地址
    var IMPORT_EXCEL_CLASS = "import_file";// 导入EXCEL的file的class
</script>
<script src="{{asset('js/common/list.js')}}"></script>
<script src="{{ asset('js/admin/DogTools/Classes.js') }}"  type="text/javascript"></script>
