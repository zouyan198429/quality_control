<!DOCTYPE html>
<html>
	<head>
        <title>{{ $info['company_name'] ?? '' }}_{{ $info['city_name'] ?? '' }}{{ $info['industry_name'] ?? '' }}检验检测能力_{{ $info['city_name'] ?? '' }}检验检测能力</title>
        <meta name="keywords" content="{{ $info['company_name'] ?? '' }},{{ $info['city_name'] ?? '' }}{{ $info['industry_name'] ?? '' }}检验检测能力,{{ $info['city_name'] ?? '' }}检验检测能力" />
        <meta name="description" content="{{ $info['company_name'] ?? '' }},{{ $info['city_name'] ?? '' }}{{ $info['industry_name'] ?? '' }}检验检测能力,{{ $info['city_name'] ?? '' }}检验检测能力" />
        @include('web.QualityControl.CertificateSchedule.layout_public.pagehead')
	</head>
	<body>
        @include('web.QualityControl.CertificateSchedule.layout_public.header')
		<div class="details-header">
			<div class="wrap">
				<div class="com-logo">

				</div>
				<div class="com-name">
                    {{ $info['company_name'] ?? '' }}
				</div>
				<div class="content-info">
					<p>CMA证书编号：<span>{{ $info['company_certificate_no'] ?? '' }}</span></p>
					<p>公司地址：<span>{{ $info['addr'] ?? '' }}</span></p>
					<p>联系人：<span>{{ $info['company_contact_name'] ?? '' }}</span></p>
					<p>联系电话：<span>{{ $info['company_contact_mobile'] ?? '' }}</span></p>
				</div>
				<div class="c"></div>
			</div>
		</div>

		<div class="det-floor1">
			<div class="wrap">
				<div class="zhengshu box1">
					<div class="hd">资质证书</div>
					<div class="bd">
						<img src="{{asset('quality/CertificateSchedule/images/icon-zs.jpg')}}" alt="" class="icon-zs">
						<div class="mm">
							<p class="f16">计量认证</p>
							<div class="k10"></div>
							<p class="f14">证书编号：</p>
							<p class="f14">{{ $info['certificate_detail']['certificate_no'] ?? '' }}</p>
							<div class="k10"></div>
							<p>证书有效期：</p>
							<p>{{ $info['certificate_detail']['valid_date'] ?? '' }}</p>
						</div>
					</div>
				</div>
				<div class="qianziren box1">
					<div class="hd">授权签字人</div>
					<div class="bd">
						<ul>
                            <?php
                            $user_auth_list = $info['user_auth_list'] ?? [];
                            ?>
                            @foreach ($user_auth_list as $k => $user_info)
							<li class="qz-man">
								<img src="{{asset('quality/CertificateSchedule/images/icon-man.jpg')}}" alt="" class="icon-man">
								<div class="con">
									<p>姓名：{{ $user_info['real_name'] ?? '' }}</p>
									<p>职务：{{ $user_info['role_num_text'] ?? '' }}</p>
									<div class="k10"></div>
									<p>批准授权签字范围</p>
									<p>{{ $user_info['sign_range'] ?? '' }}</p>
								</div>
							</li>
                            @endforeach
						</ul>
					</div>
				</div>

				<div class="c"></div>

			</div>
		</div>
		<div class="k20"></div>
		<div class="wrap">
			<div class="box1">
				<div class="hd">
					检验检测能力
				</div>
				<div class="bd">

					<table border="" cellspacing="" cellpadding="" class="table wb100">
						<colgroup>
							  <col width="120">
							  <col width="120"> 
							  <col width="120">
							  <col>
							  <col width="180">
							  <col width="180">
							  <col width="120"> 
							  <col>
						</colgroup> 
						<thead>
							<tr>
								<th>产品类别</th>
								<th>检测产品</th>
								<th>检测参数</th>
								<th>依据的标准（方法）</th>
								<th>限制范围或说明</th>
								<th>场所地址</th>
								<th>批准日期</th>
							</tr>
						</thead>
						<tbody>
                        <?php
                        $certificate_list = $info['certificate_list'] ?? [];
                        ?>
                        @foreach ($certificate_list as $k => $v)
							<tr>
								<td>{{ $v['category_name'] ?? '' }}</td>
								<td>{{ $v['project_name'] ?? '' }}</td>
								<td>{{ $v['param_name'] ?? '' }}</td>
								<td>{{ $v['method_name'] ?? '' }}</td>
								<td>{{ $v['limit_range'] ?? '' }}{{ $v['explain_text'] ?? '' }}</td>
								<td>{{ $v['addr'] ?? '' }}</td>
								<td>{{ $v['ratify_date'] ?? '' }}</td>
							</tr>
                        @endforeach
						</tbody>
					</table>
				</div>

			</div>
		</div>


		<div class="c"></div>
		<div class="k20"></div>


		<div class="floor2">
			<div class="wrap">
				<div class="adv1 adva1">权威数据</div>
				<div class="adv1 adva2">精确查询</div>
				<div class="adv1 adva3">实时更新</div>
							<div class="c"></div>
			</div>
		</div>
        @include('web.QualityControl.CertificateSchedule.layout_public.footer')
	</body>
</html>
