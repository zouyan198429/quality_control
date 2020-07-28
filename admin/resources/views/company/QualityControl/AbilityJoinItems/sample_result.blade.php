<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>管理后台</title>
  <meta name="renderer" content="webkit">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    @include('admin.layout_public.pagehead')
    <link rel="stylesheet" href="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/css/layui.css')}}" media="all">
    <link rel="stylesheet" href="{{asset('layui-admin-v1.2.1/src/layuiadmin/style/admin.css')}}" media="all">
    <link rel="stylesheet" href="{{asset('layuiadmin_quality/layui/css/layui.css')}}" media="all">
    <link rel="stylesheet" href="{{asset('layuiadmin_quality/style/admin.css')}}" media="all">
</head>
<body>

<div class="layui-fluid">
    <div class="layui-card">
{{--        <div class="layui-row layui-card-header">--}}
{{--            <h3>数据上报- 蔬菜中毒死蜱</h3>--}}
{{--        </div>--}}

        <div class="layui-row layui-card-body">

            <form class="am-form am-form-horizontal" method="post"  id="addForm">
                <input type="hidden" name="id" value="{{ $info['id'] ?? 0 }}"/>

                <fieldset class="layui-elem-field layui-field-title">
                    <legend>测试结果</legend>
                </fieldset>
                @foreach ($info['join_item_reslut_info_updata']['items_samples_list'] as $k => $sample_info)
                <div class="layui-form-item">
                    <label class="layui-form-label">样品编号： {{ $sample_info['sample_one'] ?? '' }} </label>
                    <div class="layui-input-block sample_list" data-sample_one="{{ $sample_info['sample_one'] ?? '' }}">
                    {{-- 样品id _需要收集项目的数据项目的数据类型id --}}
                        @foreach ($info['project_submit_items_list'] as $t_k => $submit_info)

                            {{ $submit_info['name'] ?? '' }}：<input type="text" data-name="{{ $submit_info['name'] ?? '' }}" name="sample_result_{{ $sample_info['id'] ?? '' }}_{{ $submit_info['id'] ?? '' }}" lay-verify="title" autocomplete="off" placeholder="请输入{{ $submit_info['name'] ?? '' }}" style="width:120px; display:inline;" class="layui-input">
                        @endforeach
                    </div>
                </div>
                @endforeach

                <fieldset class="layui-elem-field layui-field-title">
                    <legend>检测所用仪器</legend>
                </fieldset>
                @foreach ($info['join_item_reslut_info_updata']['results_instrument_list'] as $k => $instrument_info)
                    <div class="instrument_list">
                        <input type="hidden" name="instrument_id[]" value="{{ $instrument_info['id'] ?? '' }}">
                        <div class="layui-form-item ">
                            <label class="layui-form-label">名称/型号</label>
                            <div class="layui-input-block">
                                <input type="text" name="instrument_model[]" value="{{ $instrument_info['instrument_model'] ?? '' }}"  lay-verify="title" autocomplete="off" placeholder="请输入名称/型号" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">出厂编号</label>
                            <div class="layui-input-block">
                                <input type="text" name="factory_number[]" value="{{ $instrument_info['factory_number'] ?? '' }}" lay-verify="title" autocomplete="off" placeholder="请输入出厂编号" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">检定日期</label>
                            <div class="layui-input-block">
                                <input type="text" name="check_date[]" value="{{ $instrument_info['check_date'] ?? '' }}"  lay-verify="title" autocomplete="off" placeholder="请输入检定日期" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">有效期</label>
                            <div class="layui-input-block">
                                <input type="text" name="valid_date[]" value="{{ $instrument_info['valid_date'] ?? '' }}"  lay-verify="title" autocomplete="off" placeholder="请输入有效期" class="layui-input">
                            </div>
                        </div>
                    </div>
                @endforeach

                <fieldset class="layui-elem-field layui-field-title">
                    <legend>标准物质</legend>
                </fieldset>
                @foreach ($info['join_item_reslut_info_updata']['results_standard_list'] as $k => $standard_info)
                    <div class="standard_list">
                        <input type="hidden" name="standard_id[]" value="{{ $standard_info['id'] ?? '' }}">
                        <div class="layui-form-item">
                            <label class="layui-form-label">名称</label>
                            <div class="layui-input-block">
                                <input type="text" name="standard_name[]" value="{{ $standard_info['standard_name'] ?? '' }}" lay-verify="title" autocomplete="off" placeholder="请输入名称" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">生产单位</label>
                            <div class="layui-input-block">
                                <input type="text" name="produce_unit[]" value="{{ $standard_info['produce_unit'] ?? '' }}" lay-verify="title" autocomplete="off" placeholder="请输入生产单位" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">批号</label>
                            <div class="layui-input-block">
                                <input type="text" name="batch_number[]" value="{{ $standard_info['batch_number'] ?? '' }}" lay-verify="title" autocomplete="off" placeholder="请输入批号" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">有效期</label>
                            <div class="layui-input-block">
                                <input type="text" name="standard_valid_date[]" value="{{ $standard_info['valid_date'] ?? '' }}" lay-verify="title" autocomplete="off" placeholder="请输入有效期" class="layui-input">
                            </div>
                        </div>
                    </div>
                @endforeach

                <fieldset class="layui-elem-field layui-field-title">
                    <legend>方法依据</legend>
                </fieldset>
                @foreach ($info['join_item_reslut_info_updata']['results_method_list'] as $k => $method_info)
                    <div class="method_list">
                        <input type="hidden" name="method_id[]" value="{{ $method_info['id'] ?? '' }}">
                        <div class="layui-form-item">
                            <label class="layui-form-label"></label>
                            <div class="layui-input-block">
                                <textarea name="content[]" placeholder="请输入内容" class="layui-textarea">{{ replace_enter_char($method_info['content'] ?? '',2) }}</textarea>
                            </div>
                        </div>
                    </div>
                @endforeach

                <div class="layui-form-item">
                    <label class="layui-form-label">图片上传</label>
                    <div class="layui-input-block">
                        <input type="text" name="title" lay-verify="title" autocomplete="off" placeholder="请输入标题" class="layui-input">
                    </div>
                </div>

                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <button  type="button"  class="layui-btn" lay-submit="" lay-filter="demo1"  id="submitBtn">立即提交</button>
{{--                        <button type="reset" class="layui-btn layui-btn-primary">保存草稿</button>--}}
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>

<script src="{{asset('js/jquery-3.3.1.min.js')}}"></script>
<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.all.js')}}"></script>
{{--<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.js')}}"></script>--}}
@include('public.dynamic_list_foot')

<script type="text/javascript">
    var SAVE_URL = "{{ url('api/company/ability_join_item/ajax_save_result_sample') }}";// ajax保存提交数据地址
    var LIST_URL = "{{url('company/ability_join_item')}}";//保存成功后跳转到的地址

</script>
<script src="{{ asset('/js/company/QualityControl/AbilityJoinItems_sample_result.js') }}"  type="text/javascript"></script>
</body>
</html>
