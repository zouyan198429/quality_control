<!DOCTYPE html>
<html>
	<head>
        <title>检验检测机构信息查询_陕西省市场监督管理局_陕西省检验检测机构信息管理平台_检验检测能力</title>
        <meta name="keywords" content="检验检测机构信息查询,陕西省市场监督管理局,陕西省检验检测机构信息管理平台,检验检测能力" />
        <meta name="description" content="检验检测机构信息查询,陕西省市场监督管理局,陕西省检验检测机构信息管理平台,检验检测能力" />
		
        @include('web.QualityControl.Market.layout_public.pagehead')
		<style type="text/css">
		   a:link{text-decoration: none; color:blue; font-size: 18px; font-family: 微软雅黑;}
		   a:visited{ color:green;}
		   a:hover{text-decoration: underline; color:#09f; font-size: 19px;}
		   a:active{text-decoration: blink; color: yellow;}
		   
		   p.margin
		{
		    margin-top:40px; 
			margin-bottom:100px;
			margin-left:60px; 
		} 
		.content {
			
			background: none;
		}
		.div12
		{
			position: relative; 
		    width: 300px;
		    height: 130px;
		    padding: 15px;
		    background-color: 	#FFFFFF;
		    box-shadow: 10px 10px 5px grey;
			display:inline-block;
			margin-right: 25px;
		}
		 </style>
	</head>
	<body style="background-color: #F0F6FC;">
        @include('web.QualityControl.Market.layout_public.header') 
	<div class="content">
		<div class="wrap">
			<div  style="padding-top:100px;">
				<div class="div12">
					<p class="margin"><b><a href="http://qts.cnca.cn/qts/" target="_blank">检验检测统计直报系统</a></b></p> 
				</div>
				<div class="div12">
					<p class="margin"><b><a href="http://113.140.67.203:1291" target="_blank">行政审批企业上报系统</a></b></p>
				</div>
			</div>
		
		</div>
	</div>



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

