<!DOCTYPE html>
<html>
	<head>
        <title>检验检测机构信息查询_陕西省质量认证认可协会_陕西质量认证咨询中心_检验检测能力</title>
        <meta name="keywords" content="检验检测机构信息查询,陕西省质量认证认可协会,陕西质量认证咨询中心,检验检测能力" />
        <meta name="description" content="检验检测机构信息查询,陕西省质量认证认可协会,陕西质量认证咨询中心,检验检测能力" />
        @include('web.QualityControl.Market.layout_public.pagehead')
	</head>
	<body>
        @include('web.QualityControl.Market.layout_public.header')
        @include('web.QualityControl.Market.layout_public.search')
{{--        这里写新的内容--}}
        <a href="{{ url('web/market/company') }}" >机构信息查询</a>
        <a href="{{ url('web/market/platform_notices') }}" >通知公告</a>
        <a href="{{ url('web/market/platform_down_files') }}" >表格下载</a>
        <a href="{{ url('web/market/link') }}" target="_blank">相关链接</a>

        @include('web.QualityControl.Market.layout_public.footer')
	</body>
</html>

<script type="text/javascript">
    // var SEARCH_COMPANY_URL = "{ {url('web/certificate/company/' . ($city_id ?? 0)  . '_' . ($industry_id ?? 0)  . '_0_1')}}";//保存成功后跳转到的地址
    var SEARCH_COMPANY_URL = "{{url('jigou/list/' . ($city_id ?? 0)  . '_' . ($industry_id ?? 0)  . '_0_1')}}";//保存成功后跳转到的地址
</script>
{{--<script src="{{asset('js/jquery-3.3.1.min.js')}}"></script>--}}
<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.all.js')}}"></script>
{{--<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.js')}}"></script>--}}
@include('public.dynamic_list_foot')

<script src="{{ asset('/js/web/QualityControl/Market/search.js') }}?1"  type="text/javascript"></script>

