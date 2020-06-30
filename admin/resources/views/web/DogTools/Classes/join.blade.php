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

		<div>加入已有班级</div>
		<div class="k50"></div>

 		<form action="">

 			<div class="form-item">
 				<label for="">班级邀请码</label>
	            <input type="text" name="text" class="form-control" placeholder="班级邀请码" value="" style="width:480px;">
 			</div>
 			<div class="form-tiem">
 				<button type="submit" id="" name="" class="btn btn-primary">
                    加入
                </button>
 			</div>
 		</form>


	</div>
</div>


</body>
</html>
