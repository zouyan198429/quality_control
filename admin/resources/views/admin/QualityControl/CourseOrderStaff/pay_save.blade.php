

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>开启头部工具栏 - 数据表格</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <!-- zui css -->
    <link rel="stylesheet" href="{{asset('dist/css/zui.min.css') }}">
    @include('admin.layout_public.pagehead')
    <link rel="stylesheet" href="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/css/layui.css')}}" media="all">
    <link rel="stylesheet" href="{{asset('layui-admin-v1.2.1/src/layuiadmin/style/admin.css')}}" media="all">
</head>
<body>

{{--<div id="crumb"><i class="fa fa-reorder fa-fw" aria-hidden="true"></i> {{ $operate ?? '' }}员工</div>--}}
<div class="mm">

    @foreach ($pay_config_format as $pay_config_id => $pay_config)
        <?php
        $course_order_staff = $config_staff_list[$pay_config_id] ?? [];
        $tem_pay_method = $pay_config['pay_method'] ?? 0;
        $allow_pay_method = $pay_config['allow_pay_method'] ?? 0;
        $pay_company_name = $pay_config['pay_company_name'] ?? '';
        $totalPrice = 0;
        $staff_ids = [];
        foreach($course_order_staff as $k => $staff_info){
            $totalPrice += $staff_info['price'];
            array_push($staff_ids, $staff_info['id']);
        }
        ?>

            <form class="am-form am-form-horizontal" method="post"  id="addForm">
                <div  class=" baguetteBoxOne gallery">

                    <input type="hidden" name="pay_config_id" value="{{ $pay_config_id ?? 0 }}"/>
                    <input type="hidden" name="pay_method" value="{{ $pay_method ?? 0 }}"/>
                    <input type="hidden" name="id" value="{{ implode(',', $staff_ids) }}"/>
                    <input type="hidden" name="total_price" value="{{ $totalPrice ?? '' }}"/>{{--总金额--}}
                    <input type="hidden" name="change_amount" value="0"/>{{--找零金额--}}
                    共{{ count($course_order_staff) }}人；
                    总计：￥{{ $totalPrice ?? '' }}元；<hr/>
                    实收<input type="text" name="payment_amount" value="{{ $totalPrice ?? '' }}" placeholder="请输入实收金额" style="width: 80px;" @if (isset($pay_method) && $pay_method != 1 )  readonly="true"   @endif  onkeyup="numxs(this) " onafterpaste="numxs(this)" >元;
                    应找零<span style="color: red;"><strong class="change_amount">¥0</strong></span>元
                    <button class="layui-btn layui-btn-sm layui-btn-normal layui-btn-radius"  id="submitBtn" >确认收款</button>
                    <hr/>
                    收款帐号：{{ $pay_company_name ?? '' }}<br/>
                    收款方式：{{ $method_info['pay_name'] ?? '' }}<hr/>
                    @if (isset($method_info['resource_list']) && !empty($method_info['resource_list']) )
                        收款图片：
                        @if (false)
                            <span class="resource_list"  style="display: none;">{{ json_encode($method_info['resource_list']) }}</span>
                            <span  class="resource_show"></span>
                        @else
                            @foreach ($method_info['resource_list'] as $resource_item)
                                <a href="{{ $resource_item['resource_url_format'] ?? '' }}" target='_blank'>
                {{--                    {{ $resource_item['resource_name'] ?? '' }}--查看--}}
                                    <img  src="{{ $resource_item['resource_url_format'] ?? '' }}"  style="width:200px;">
                                </a>
                            @endforeach

                        @endif
                        <hr/>
                    @else
                        <div class="qrcode_block" style="display:none;">
                            收款码：<span style="color: red;"><strong class="count_down_num">60</strong></span>秒
                            <div id="qrcode"></div>
                            <hr/>
                        </div>
                    @endif
                    收款说明：<br/>{!!   $method_info['pay_remarks'] ?? '' !!}<hr/>
                </div>
            </form>
    @endforeach
</div>
<script src="{{asset('js/jquery-3.3.1.min.js')}}"></script>
<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.all.js')}}"></script>
{{--<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.js')}}"></script>--}}

<script src="{{asset('dist/lib/jquery-qrcode-master/jquery.qrcode.min.js')}}"></script>

@include('public.dynamic_list_foot')

<script type="text/javascript">
    var SAVE_URL = "{{ url('api/admin/course_order_staff/ajax_create_order') }}";// ajax保存记录地址
    var LIST_URL = "{{url('admin/course_order_staff')}}";//保存成功后跳转到的地址

    var DOWN_FILE_URL = "{{ url('admin/down_file') }}";// 下载
    var DEL_FILE_URL = "{{ url('api/admin/upload/ajax_del') }}";// 删除文件的接口地址

</script>
<link rel="stylesheet" href="{{asset('js/baguetteBox.js/baguetteBox.min.css')}}">
<script src="{{asset('js/baguetteBox.js/baguetteBox.min.js')}}" async></script>
{{--<script src="{{asset('js/baguetteBox.js/highlight.min.js')}}" async></script>--}}
<!-- zui js -->
<script src="{{asset('dist/js/zui.min.js') }}"></script>

<script src="{{ asset('/js/admin/QualityControl/CourseOrderStaff_pay_save.js') }}?38"  type="text/javascript"></script>
@component('component.upfileincludejsmany')
@endcomponent
</body>
</html>
