<!DOCTYPE html>
<html>
	<head>
        <title>秦检通_陕西省质量认证认可协会_陕西质量认证咨询中心_检验检测能力</title>
        <meta name="keywords" content="秦检通,陕西省质量认证认可协会,陕西质量认证咨询中心,检验检测能力" />
        <meta name="description" content="秦检通,陕西省质量认证认可协会,陕西质量认证咨询中心,检验检测能力" />
        @include('web.QualityControl.CertificateSchedule.layout_public.pagehead')
        <link href="{{asset('static/css/bootstrap.css')}}" rel="stylesheet" type="text/css" />
	</head>

	<body>
        @include('web.QualityControl.CertificateSchedule.layout_public.header')

		<div class="search">
			<div class="wrap">

				<h1>资质认定获证机构查询</h1>

				<div class="slideTxtBox">
					<div class="hd">
						<ul><li>按企业信息查</li><li>按检测项目查</li><li>按证书号查询</li></ul>
					</div>
					<div class="bd">
						<ul class="searchbox">

							<input type="radio" name="oncom" value="1" id="killOrder1" checked><label for="killOrder1">检验机构名称</label>
							<input type="radio" name="oncom" value="2" id="killOrder2" ><label for="killOrder2">统一社会信用代码或组织机构代码</label>
							<input type="text" name="admin_username" placeholder="" class="inp" ><button class="searchbtn" >搜索</button>
						</ul>
						<ul class="searchbox">
							<input type="radio" name="onzhengshu" value="1" id="killOrder3" checked><label for="killOrder3">标准名称</label>
							<input type="radio" name="onzhengshu" value="2" id="killOrder4" ><label for="killOrder4">标准编号</label>
							<input type="text" name="admin_username" placeholder="" class="inp" ><button class="searchbtn" >搜索</button>
						</ul>
						<ul class="searchbox">
							<input type="text" name="admin_username" placeholder="证书号" class="inp" ><button class="searchbtn" >搜索</button>
						</ul>
						<div class="c"></div>
					</div>
				</div>
				<script type="text/javascript">jQuery(".slideTxtBox").slide();</script>
			</div>

		</div>

		<div class="keyword">
			<div class="wrap">
				<p>关键词：<strong>某某某某</strong></p>
				<div class="c"></div>
			</div>
		</div>

		<div class="list-wrap">
			<div class="wrap">

				<div class="list">

					<ul class="comlist">
                        @foreach ($company_list as $k => $v)
						<li>
							<div class="com-logo">

							</div>
							<div class="com-name">
                                {{ $v['company_name'] ?? '' }}
							</div>
							<div class="more">
								<a href="{{url('web/certificate/info/' . $v['id'])}}" target="_blank" >查看详情</a>
							</div>
							<div class="content-info">
								<p>CMA证书编号：<span>{{ $v['company_certificate_no'] ?? '' }}</span></p>
								<p>公司地址：<span>{{ $v['addr'] ?? '' }}</span></p>
								<p>联系人：<span>{{ $v['company_contact_name'] ?? '' }}</span></p>
								<p>联系电话：<span>{{ $v['company_contact_mobile'] ?? '' }}/{{ $v['company_contact_tel'] ?? '' }}</span></p>
							</div>
							<div class="c"></div>
						</li>
                        @endforeach

					</ul>
                    <div class="mmfoot">
                        <div class="mmfleft"></div>
                        <div class="pagination">
                            {!! $pageInfoLink ?? ''  !!}
                        </div>
                    </div>

				</div>

				<div class="list-side">


					<div class="tjcom">
						<div class="hd">
							推荐企业
						</div>
						<div class="bd">
							<ul class="txtlist">
								<li><a href="">西安某某企业有限公司</a></li>
								<li><a href="">西安某某企业有限公司</a></li>
								<li><a href="">西安某某企业有限公司</a></li>
								<li><a href="">西安某某企业有限公司</a></li>
								<li><a href="">西安某某企业有限公司</a></li>
								<li><a href="">西安某某企业有限公司</a></li>
								<li><a href="">西安某某企业有限公司</a></li>
								<li><a href="">西安某某企业有限公司</a></li>
							</ul>
						</div>
					</div>




				</div>

				<div class="c"></div>

			</div>
		</div>

		<div class="c"></div>


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
