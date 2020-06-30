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
        <form class="am-form am-form-horizontal" method="post"  id="addForm">
            <input type="hidden" name="id" value="{{ $info['id'] ?? 0 }}"/>
			<div class="form-item">
				<label for="">姓名：</label>
				<div class="input-block">{{ $info['staff_info']['real_name'] ?? '' }}</div>
			</div>
			<div class="form-item">
				<label for="">手机：</label>
				<div class="input-block">{{ $info['staff_info']['mobile'] ?? '' }}</div>
				</div>
			<div class="form-item">
				<label for="">角色:</label>
				<div class="input-block">
                    <select name="teacher_role_id" lay-verify="required" lay-search="">
			          <option value="">请选择</option>
                        @foreach ($roles_kv as $k=>$txt)
			                <option value="{{ $k }}"  @if ($k === $defaultRole) selected @endif >{{ $txt }}</option>
                        @endforeach
			        </select>
			    </div>
	        </div>
			<div class="form-item">
				<label for="">是否班主任:</label>
				<div class="input-block">
					<input type="checkbox" value="1" name="is_head_master" lay-skin="primary"  @if ($info['is_head_master'] == 1) checked @endif>班主任
				</div>
			</div>
            <div class="form-item">
                <label for="">审核状态:</label>
                <div class="input-block">
                    @foreach ($openStatus as $k=>$txt)
                        <label><input type="radio"  name="open_status"  value="{{ $k }}"  @if(isset($defaultOpenStatus) && $defaultOpenStatus == $k) checked="checked"  @endif />{{ $txt }} </label>
                    @endforeach
                </div>
            </div>
			<div class="form-item" style="border:0; ">
				<label for=""></label>
				<div class="input-block">
					<button class="btn"  id="submitBtn">保存</button>
				</div>
			</div>

			<div class="k20">
				<hr>
			</div>
		</form>
	</div>
</div>
<script src="{{asset('js/jquery-3.3.1.min.js')}}"></script>
<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.all.js')}}"></script>
{{--<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.js')}}"></script>--}}
@include('public.dynamic_list_foot')

<script type="text/javascript">
    var SAVE_URL = "{{ url('api/web/class_teachers/' . $class_id . '/ajax_save') }}";// ajax保存记录地址
    var LIST_URL = "{{url('web/class_teachers/' . $class_id . '')}}";//保存成功后跳转到的地址
</script>
<script src="{{ asset('/js/web/DogTools/ClassTeachers_edit.js') }}"  type="text/javascript"></script>
</body>
</html>
