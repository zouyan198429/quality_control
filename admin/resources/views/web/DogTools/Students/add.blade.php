<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>教育云助手</title>
    @include('web.layout_public.pagehead')
    <link rel="stylesheet" href="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/css/layui.css')}}" media="all">
</head>
<body style="background:#f1f3f3;">

<div class="wrap tc">
	<div class="main tabcontent">
		<div class="tab-item">
            <form class="am-form am-form-horizontal" method="post"  id="addForm">
                <input type="hidden" name="id" value="{{ $info['id'] ?? 0 }}"/>
			<div class="form-item">
				<label for="">姓名：</label>
				<div class="input-block">
                    <input type="text" class="inp wnormal"  name="real_name" value="{{ $info['real_name'] ?? '' }}" placeholder="请输入姓名"/>
                </div>
			</div>
                <div class="form-item">
                    <label for="">学号：</label>
                    <div class="input-block">
                        <input type="text" class="inp wnormal"  name="student_number" value="{{ $info['student_number'] ?? '' }}" placeholder="请输入学号"/>
                    </div>
                </div>
			<div class="form-item">
				<label for="">性别：</label>
				<div class="input-block">

                    @foreach ($sexArr as $k=>$txt)
                        <label><input type="radio"  name="sex"  value="{{ $k }}"  @if(isset($defaultSex) && $defaultSex == $k) checked="checked"  @endif />{{ $txt }} </label>
                    @endforeach
{{--					<input type="radio" name="sex" value="男" title="男" checked="">男生--}}
{{--					<input type="radio" name="sex" value="女" title="女">女生--}}
				</div>
			</div>
{{--			<div class="form-item">--}}
{{--				<label for="">标签:</label>--}}
{{--				<div class="input-block">--}}
{{--					<span class="tag tagon">帅气</span>--}}
{{--					<span class="tag">聪明伶俐 </span>--}}
{{--					<span class="tag">德才兼备 </span>--}}
{{--					<span class="tag">助人为乐 </span>--}}
{{--					<span class="tag">品学兼优 </span>--}}
{{--			    </div>--}}
{{--	        </div>--}}
{{--	        <div class="form-item">--}}
{{--				<div for="" class="form-label-bok">头像：</div>--}}
{{--				<div class="input-block">--}}
{{--					<img src="{{asset('staticweb/images/icon-student.png')}}" alt="" width="80">--}}
{{--					<button class="btn">上传</button>--}}
{{--					<p>只支持2M以内的jpg或png格式图片d</p>--}}
{{-- 				</div>--}}
{{--			</div>--}}

			<div class="form-item" style="border:0; ">
				<label for=""></label>
				<div class="input-block">
					<button class="btn"   id="submitBtn">保存</button>
				</div>
			</div>
			<div class="k20"></div>
            </form>

		</div>
		<div class="tab-item">

		</div>
	</div>
</div>
<script src="{{asset('js/jquery-3.3.1.min.js')}}"></script>
<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.all.js')}}"></script>
{{--<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.js')}}"></script>--}}
@include('public.dynamic_list_foot')

<script type="text/javascript">
    var SAVE_URL = "{{ url('api/web/students/' . $class_id . '/ajax_save') }}";// ajax保存记录地址
    var LIST_URL = "{{url('web/students/' . $class_id . '')}}";//保存成功后跳转到的地址
</script>
<script src="{{ asset('/js/web/DogTools/Students_edit.js') }}"  type="text/javascript"></script>
</body>
</html>
