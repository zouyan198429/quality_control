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


	<div class="main-nob">

		<div>创建班级</div>
		<div class="k50"></div>

        <form class="am-form am-form-horizontal" method="post"  id="addForm">
            <input type="hidden" name="id" value="0"/>

 			<div class="form-item">
 				<label for="">班级名称</label>
	            <input type="text" name="class_name" class="form-control" placeholder="班级名称" value="" style="width:480px;">
 			</div>
 			<div class="form-tiem">
 				<button  class="btn btn-primary"  id="submitBtn" >
                    提交
                </button>
 			</div>
 		</form>


	</div>
</div>

<script src="{{asset('js/jquery-3.3.1.min.js')}}"></script>
<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.all.js')}}"></script>
{{--<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.js')}}"></script>--}}
@include('public.dynamic_list_foot')

<script type="text/javascript">
    var SAVE_URL = "{{ url('api/web/classes/ajax_add') }}";// ajax保存记录地址
    var LIST_URL = "{{url('web/classes')}}";//保存成功后跳转到的地址
</script>
<script src="{{ asset('/js/web/DogTools/Classes_add.js') }}"  type="text/javascript"></script>

</body>
</html>
