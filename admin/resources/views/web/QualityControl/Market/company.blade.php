<!DOCTYPE html>
<html>
	<head>
        <title>检验检测机构信息查询_{{ $key_str ?? '' }}市场监督管理局_陕西{{ $key_str ?? '' }}市场监督管理局第{{ $page ?? '' }}页_检验检测能力</title>
        <meta name="keywords" content="检验检测机构信息查询,{{ $key_str ?? '' }}市场监督管理局,陕西{{ $key_str ?? '' }}市场监督管理局第{{ $page ?? '' }}页,检验检测能力" />
        <meta name="description" content="检验检测机构信息查询,{{ $key_str ?? '' }}市场监督管理局,陕西{{ $key_str ?? '' }}市场监督管理局第{{ $page ?? '' }}页,检验检测能力" />
        @include('web.QualityControl.Market.layout_public.pagehead')
        <link href="{{asset('static/css/bootstrap.css')}}" rel="stylesheet" type="text/css" />
	</head>
	<body style="background-color: #F0F6FC;">
        @include('web.QualityControl.Market.layout_public.header')
        @include('web.QualityControl.Market.layout_public.search')
		<!-- <div class="keyword">
			<div class="wrap">
				<p>关键词：<strong>{{ $key_str ?? '' }}</strong></p>
				<div class="c"></div>
			</div>
		</div> -->
		<div class="list-wrap">
			<div class="wrap">

				<div class="list content">

					<table class=" comlist">
						<thead>
							<tr>
								<th style="width:750px;">机构名称</th>
								<th>资质认定证书编号</th>
								<th>机构信息</th>
								<th>能力附表</th>
								<th>自我声明公告	</th>
								<th>监督检查信息	</th>
								<th>行政处罚信息</th>
							</tr>
						</thead>
                        @foreach ($company_list as $k => $v)
						<tr>
							<td class="com-name">
                                {{ $v['company_name'] ?? '' }}
							</td>
							<td class="content-info">
								{{ $v['company_certificate_no'] ?? '' }}
							</td>
							<td>
 								 <!-- <p>公司地址：<span>{{ $v['addr'] ?? '' }}</span></p>
 								<p>联系人：<span>{{ $v['company_contact_name'] ?? '' }}</span></p>
 								<p>联系电话：<span>{{ $v['company_contact_mobile'] ?? '' }}/{{ $v['company_contact_tel'] ?? '' }}</span></p>  -->
                                <a href="javascript:void(0);" onclick="otheraction.browseInfo('{{ $v["id"] ?? "0" }}','{{ $v["company_name"] ?? "" }}')" alt="机构信息" > <img src="{{asset('quality/Market/images/details12.png')}}" alt="" /> </a>
							</td>
							<td>
                                <a href="javascript:void(0);" onclick="otheraction.schedule('{{ $v["id"] ?? "0" }}','{{ $v["company_name"] ?? "" }}')" alt="能力附表"> <img src="{{asset('quality/Market/images/details12.png')}}" alt="" /> </a>
								<!-- ({{ $v['extend_info']['schedule_num'] ?? '0' }}) -->
							</td>
							<td>
                                <a href="javascript:void(0);" onclick="otheraction.company_statement_num('{{ $v["id"] ?? "0" }}','{{ $v["company_name"] ?? "" }}')" alt="自我声明公告"> <img src="{{asset('quality/Market/images/details12.png')}}" alt="" /> </a>
								<!-- ({{ $v['extend_info']['statement_num'] ?? '0' }}) -->
							</td>
							<td>
                                <a href="javascript:void(0);" onclick="otheraction.company_supervise('{{ $v["id"] ?? "0" }}','{{ $v["company_name"] ?? "" }}')" alt="监督检查信息"> <img src="{{asset('quality/Market/images/details12.png')}}" alt="" /> </a>
								<!-- ({{ $v['extend_info']['supervise_num'] ?? '0' }}) -->
							</td>
							<td>
                                <a href="javascript:void(0);" onclick="otheraction.company_punish_num('{{ $v["id"] ?? "0" }}','{{ $v["company_name"] ?? "" }}')" alt="行政处罚信息"> <img src="{{asset('quality/Market/images/details12.png')}}" alt="" /> </a>
								<!-- ({{ $v['extend_info']['punish_num'] ?? '0' }}) -->
							</td>
						</tr>
                        @endforeach

					</table>
                    <div class="mmfoot"><!--
                        <div class="mmfleft"></div> -->
                        <div class="pagination">
                            {!! $pageInfoLink ?? ''  !!}
                        </div>
                    </div>

				</div>





				<div class="c"></div>

			</div>
		</div>

		<div class="c"></div>
        @include('web.QualityControl.Market.layout_public.footer')
	</body>
</html>

<script type="text/javascript">

    var SHOW_CLOSE_OPERATE = 0 ;// 详情弹窗operate_num关闭时的操作0不做任何操作1刷新当前页面2刷新当前列表页面

    var SEARCH_COMPANY_URL = "{{url('web/market/company/' . ($city_id ?? 0)  . '_' . ($industry_id ?? 0)  . '_0_1')}}";//保存成功后跳转到的地址
    // var SEARCH_COMPANY_URL = "{ {url('jigou/list/' . ($city_id ?? 0)  . '_' . ($industry_id ?? 0)  . '_0_1')}}";//保存成功后跳转到的地址

    // 机构信息-查看
    var COMPANY_INFO_URL = "{{url('web/market/company/info')}}/";//显示页面地址前缀 + id

    var SCHEDULE_SHOW_URL = "{{ url('web/market/company_new_schedule')}}";// "{ { url('admin/company_new_schedule/show')}}/";//查看企业能力附表

    var COMPANY_SUPERVISE_EDIT_URL = "{{ url('web/market/company_supervise/info/0') }}"; // 监督检查信息修改/添加url


    var COMPANY_STATEMENT_URL = "{{ url('web/market/company_statement')}}/";// 查看机构自我声明
    var COMPANY_PUNISH_URL = "{{ url('web/market/company_punish')}}/";// 查看机构处罚

</script>
{{--<script src="{{asset('js/jquery-3.3.1.min.js')}}"></script>--}}
<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.all.js')}}"></script>
{{--<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.js')}}"></script>--}}
@include('public.dynamic_list_foot')

<script src="{{ asset('/js/web/QualityControl/Market/search.js') }}?1"  type="text/javascript"></script>

<script src="{{ asset('/js/web/QualityControl/Market/company.js') }}?2"  type="text/javascript"></script>
