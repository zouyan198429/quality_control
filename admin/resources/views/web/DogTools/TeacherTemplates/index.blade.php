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
<div class="nav">
	<a href="index.blade.php" class="on">我的卡片</a>
	<a href="template.html">模板库</a>
</div>
<div class="wrap tc">

	<div class="k50"></div>



	<div class="main tabcontent">
 	 <table class="table table-nob" style="width:100%;">
						<colgroup>
							<col width="50">
							<col width="100">
							<col width="">
							<col width="150">
							<col width="220">
						</colgroup>
						<thead>
							<tr>
								<th>ID</th>
								<th>缩略图</th>
								<th>文件名</th>
								<th>日期</th>
								<th>操作</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>1</td>
								<td><a href="card-edit.html"><img src="{{asset('staticweb/images-temp/pic01.jpg')}}" class="pic-card" alt=""></a></td>
								<td>0522-1</td>
								<td>2020-05-22 12:32</td>
								<td>
									<a href="" class="btn">复制</a>
									<a href="card-edit.html" class="btn">编辑</a>
									<a href="" class="btn">删除</a>
								</td>
							</tr>
							<tr>
								<td>1</td>
								<td><a href="card-edit.html"><img src="{{asset('staticweb/images-temp/pic01.jpg')}}" class="pic-card" alt=""></a></td>
								<td>0522-1</td>
								<td>2020-05-22 12:32</td>
								<td>
									<a href="" class="btn">复制</a>
									<a href="card-edit.html" class="btn">编辑</a>
									<a href="" class="btn">删除</a>
								</td>
							</tr>
							<tr>
								<td>1</td>
								<td><a href="card-edit.html"><img src="{{asset('staticweb/images-temp/pic01.jpg')}}" class="pic-card" alt=""></a></td>
								<td>0522-1</td>
								<td>2020-05-22 12:32</td>
								<td>
									<a href="" class="btn">复制</a>
									<a href="card-edit.html" class="btn">编辑</a>
									<a href="" class="btn">删除</a>
								</td>
							</tr>
							<tr>
								<td>1</td>
								<td><a href="card-edit.html"><img src="{{asset('staticweb/images-temp/pic01.jpg')}}" class="pic-card" alt=""></a></td>
								<td>0522-1</td>
								<td>2020-05-22 12:32</td>
								<td>
									<a href="" class="btn">复制</a>
									<a href="card-edit.html" class="btn">编辑</a>
									<a href="" class="btn">删除</a>
								</td>
							</tr>
							<tr>
								<td>1</td>
								<td><a href="card-edit.html"><img src="{{asset('staticweb/images-temp/pic01.jpg')}}" class="pic-card" alt=""></a></td>
								<td>0522-1</td>
								<td>2020-05-22 12:32</td>
								<td>
									<a href="" class="btn">复制</a>
									<a href="card-edit.html" class="btn">编辑</a>
									<a href="" class="btn">删除</a>
								</td>
							</tr>
							<tr>
								<td>1</td>
								<td><a href="card-edit.html"><img src="{{asset('staticweb/images-temp/pic01.jpg')}}" class="pic-card" alt=""></a></td>
								<td>0522-1</td>
								<td>2020-05-22 12:32</td>
								<td>
									<a href="" class="btn">复制</a>
									<a href="card-edit.html" class="btn">编辑</a>
									<a href="" class="btn">删除</a>
								</td>
							</tr>

						</tbody>
					</table>

</div>
</div>

</body>
</html>
