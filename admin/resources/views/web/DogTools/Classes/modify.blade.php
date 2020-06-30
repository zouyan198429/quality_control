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
    @include('web.layout_public.headClass')
	<div class="main tabcontent">
		<form action="">

			<div class="form-item">
				<div for="" class="form-label-bok">班级头像：</div>
				<div class="input-block">
					<img src="images/icon-class.png" alt="" width="80">
					<button class="btn">上传</button>
					<p>只支持2M以内的jpg或png格式图片d</p>
 				</div>
			</div>
			<div class="form-item">
				<label for="">班级昵称：</label>
				<div class="input-block">
					<input type="text" name="text"  placeholder="" value="" style="width:280px;">
				</div>
			</div>
			<div class="form-item">
				<label for="">班级邀请码：</label>
				<div class="input-block">
					<p>348455</p>
				</div>
			</div>
			<div class="form-item" style="border:0; ">
				<label for=""></label>
				<div class="input-block">
					<button class="btn">保存</button>
				</div>
			</div>

		</form>




	</div>
</div>


</body>
</html>
