<!DOCTYPE html>
<html>
	<head>
        <title>{{ $info['company_name'] ?? '' }}_{{ $info['city_name'] ?? '' }}{{ $info['industry_name'] ?? '' }}检验检测能力_{{ $info['city_name'] ?? '' }}检验检测能力</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
        <meta name="keywords" content="{{ $info['company_name'] ?? '' }},{{ $info['city_name'] ?? '' }}{{ $info['industry_name'] ?? '' }}检验检测能力,{{ $info['city_name'] ?? '' }}检验检测能力" />
        <meta name="description" content="{{ $info['company_name'] ?? '' }},{{ $info['city_name'] ?? '' }}{{ $info['industry_name'] ?? '' }}检验检测能力,{{ $info['city_name'] ?? '' }}检验检测能力" />
        @include('web.QualityControl.Market.layout_public.pagehead')
		<style>
			.cominfo {
				border:1px solid #333;
				width: 90%;
				margin:15px auto;	
				background-color: #fff;
				border-collapse: collapse;
			}
			.cominfo td {
				border:1px solid #333;
				padding:15px 15px;
			}
			.cominfo th {
				border:1px solid #333;
				padding:15px 15px;
			}
		</style>
	</head>
	<body>  
	
	
	<table class="cominfo">
		<tr>
			<th>机构名称:</th>
			<td>{{ $info['company_name'] ?? '' }}</td>
		</tr>
		<tr>
			<th>机构地址:</th>
			<td>{{ $info['addr'] ?? '' }}</td>
		</tr>
		<tr>
			<th>联系人:</th>
			<td>{{ $info['company_contact_name'] ?? '' }}</td>
		</tr>
		<tr>
			<th>联系电话:</th>
			<td>{{ $info['company_contact_mobile'] ?? '' }}</td>
		</tr>
		<tr>
			<th>资质认定编号:</th>
			<td>{{ $info['company_certificate_no'] ?? '' }}</td>
		</tr>
		<tr>
			<th>发证日期：</th>
			<td></td>
		</tr>
		<tr>
			<th>有效日期：</th>
			<td></td>
		</tr>
	</table>
	  
	</body>
</html>
