<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>后台管理</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    @include('admin.layout_public.pagehead')
    <link rel="stylesheet" href="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/css/layui.css')}}" media="all">
    <link rel="stylesheet" href="{{asset('layui-admin-v1.2.1/src/layuiadmin/style/admin.css')}}" media="all">

</head>
<body>

<div class="layui-fluid">
    <div class="layui-card">
{{--        <div class="layui-row layui-card-header">--}}
{{--            <h3>报名</h3>--}}
{{--        </div>--}}

        <div class="layui-row layui-card-body">

            <form class="am-form am-form-horizontal" method="post"  id="addForm">
                <input type="hidden" name="ids" value="{{ $ids ?? '' }}"/>

                @foreach ($data_list as $k => $info)
                <fieldset class="layui-elem-field layui-field-title">
                    <legend>项目</legend>
                </fieldset>
                <div class="layui-form-item">
                    <label class="layui-form-label">检测项目</label>
                    <div class="layui-form-mid layui-word-aux">
                        <span id="ability_name_{{ $info['id'] ?? '' }}">{{ $info['ability_name'] ?? '' }}</span>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label ">方法标准</label>
                    <div class="layui-input-block " id="project_standard_{{ $info['id'] ?? '' }}">
                        @foreach ($info['project_standards'] as $k_p => $p_info)
                          <label><input type="checkbox" name="project_standard_id_{{ $info['id'] ?? '' }}[]" value="{{ $p_info['id'] ?? '' }}">{{ $p_info['tag_name'] ?? '' }}</label>&nbsp;&nbsp;&nbsp;&nbsp;
                        <br>
                        @endforeach
                         <label><input type="checkbox" name="project_standard_id_{{ $info['id'] ?? '' }}[]" value="0">其他</label>&nbsp;&nbsp;&nbsp;&nbsp;
                        <br>
                        <textarea name="project_standard_name_{{ $info['id'] ?? '' }}" id="" cols="50" rows="4"></textarea>
                    </div>
                </div>
                    @if ($data_num > ($k + 1) )<hr>@endif
                @endforeach

                <fieldset class="layui-elem-field layui-field-title">
                    <legend>联系人</legend>
                </fieldset>
                <div class="layui-form-item">
                    <label class="layui-form-label">姓名</label>
                    <div class="layui-input-block">
                        <input type="text" name="contacts" lay-verify="title" autocomplete="off" placeholder="请输入姓名" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">手机</label>
                    <div class="layui-input-block">
                        <input type="text" name="mobile" lay-verify="title" autocomplete="off" placeholder="请输入手机" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">联系电话</label>
                    <div class="layui-input-block">
                        <input type="text" name="tel" lay-verify="title" autocomplete="off" placeholder="请输入电话" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <button class="btn btn-l wnormal"  id="submitBtn" >提交</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript" src="{{asset('laydate/laydate.js')}}"></script>
<script src="{{asset('js/jquery-3.3.1.min.js')}}"></script>
<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.all.js')}}"></script>
{{--<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.js')}}"></script>--}}
@include('public.dynamic_list_foot')
<script type="text/javascript">
    var SAVE_URL = "{{ url('api/company/abilitys/ajax_join_save') }}";// ajax保存记录地址
    var LIST_URL = "{{url('company/abilitys')}}";//保存成功后跳转到的地址

</script>
<script src="{{ asset('/js/company/QualityControl/Abilitys_join.js?18') }}"  type="text/javascript"></script>
</body>
</html>

