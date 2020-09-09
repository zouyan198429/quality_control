<!DOCTYPE html>
<html>
	<head>
        <title>秦检通_陕西省质量认证认可协会_陕西质量认证咨询中心_检验检测能力</title>
        <meta name="keywords" content="秦检通,陕西省质量认证认可协会,陕西质量认证咨询中心,检验检测能力" />
        <meta name="description" content="秦检通,陕西省质量认证认可协会,陕西质量认证咨询中心,检验检测能力" />
        @include('web.QualityControl.CertificateSchedule.layout_public.pagehead')
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

		<div class="dataview">
			<div class="wrap">
				<dl class="dv1 dva1">
					<dt></dt>
					<dd>
						<span>入驻企业</span>
						<strong>{{ $company_count ?? '0' }}</strong>
					</dd>
				</dl>
				<dl class="dv1 dva2">
					<dt></dt>
					<dd><span>可检测产品</span>
						<strong>1652</strong>
					</dd>
				</dl>
				<dl class="dv1 dva3">
					<dt></dt>
					<dd>
						<span>可检测项目</span>
						<strong>1652</strong>
					</dd>
				</dl>
				<div class="c"></div>
			</div>
		</div>




		<div class="floor1">
			<div class="wrap">
				<div class="comtab">
					<div class="hd">
						<ul><li>查询最新注册企业</li><li>最新变更企业</li></ul>
					</div>
					<div class="bd">
						<ul class="comtabul">
                            @foreach ($company_new_list as $k => $v)
							<li>
								<div class="com-logo">

								</div>
								<div class="name">
                                    {{ $v['company_name'] ?? '' }}
								</div>
								<div class="date">
									注册日期： {{ $v['created_at_fmt'] ?? '' }}
								</div>
							</li>
                           @endforeach
							<div class="c"></div>
						</ul>
						<ul class="comtabul">
                            @foreach ($company_update_list as $k => $v)
							<li>
								<div class="com-logo">

								</div>
								<div class="name">
                                    {{ $v['company_name'] ?? '' }}
								</div>
								<div class="date">
									变更日期：{{ $v['updated_at_fmt'] ?? '' }}
								</div>
							</li>
                            @endforeach
							<div class="c"></div>
						</ul>

					</div>
				</div>
				<script type="text/javascript">jQuery(".comtab").slide();</script>

			</div>


		</div>

		<div class="floor2">
			<div class="wrap">
				<div class="adv1 adva1">权威数据</div>
				<div class="adv1 adva2">精确查询</div>
				<div class="adv1 adva3">实时更新</div>
							<div class="c"></div>
			</div>
		</div>


		<div class="floor3">
			<div class="wrap">

				<h1>企业分布</h1>
				<div class="comtypetab">
					<div class="hd">
						<ul><li>行业分布</li><li>地区分布</li></ul>
					</div>
					<div class="bd">
						<ul class="typetab">

                            @foreach ($industry_list as $k => $v)
                                <li>
                                    <div class="type-name">
                                        {{ $v['industry_name'] ?? '' }}
                                    </div>
                                    <div class="com-data">
                                        {{ $v['company_count'] ?? '0' }}
                                    </div>
                                </li>
                            @endforeach
							<div class="c"></div>
						</ul>
						<ul class="typetab">
                            @foreach ($city_list as $k => $v)
							<li>
								<div class="type-name">
                                    {{ $v['city_name'] ?? '' }}
								</div>
								<div class="com-data">
                                    {{ $v['company_count'] ?? '0' }}
								</div>
							</li>
                            @endforeach
							<div class="c"></div>
						</ul>

					</div>
				</div>
				<script type="text/javascript">jQuery(".comtypetab").slide();</script>


			</div>
		</div>
        @include('web.QualityControl.CertificateSchedule.layout_public.footer')
	</body>
</html>
