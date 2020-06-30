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





		<div class="tab-item">
			<div class="card-head">
				<label for="">标签</label>
				 <input type="text"  >
				 <button class="btn btn-small ">添加</button>
			</div>
			<div class="k20"></div>
			<hr>
			<div class="k20"></div>
			<form action="form_action.asp" method="get">

				<span class="tag">帅气 <i>×</i></span>
				<span class="tag">聪明伶俐  <i>×</i></span>
				<span class="tag">德才兼备  <i>×</i></span>
				<span class="tag">助人为乐  <i>×</i></span>
				<span class="tag">品学兼优  <i>×</i></span>


			</form>


		</div>
		<div class="tab-item">

		</div>
	</div>
</div>


</body>
</html>
