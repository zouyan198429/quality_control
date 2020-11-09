<!DOCTYPE html>
<html>
	<head>
        <title>{{ $info['company_name'] ?? '' }}_{{ $info['city_name'] ?? '' }}{{ $info['industry_name'] ?? '' }}检验检测能力_{{ $info['city_name'] ?? '' }}检验检测能力</title>
        <meta name="keywords" content="{{ $info['company_name'] ?? '' }},{{ $info['city_name'] ?? '' }}{{ $info['industry_name'] ?? '' }}检验检测能力,{{ $info['city_name'] ?? '' }}检验检测能力" />
        <meta name="description" content="{{ $info['company_name'] ?? '' }},{{ $info['city_name'] ?? '' }}{{ $info['industry_name'] ?? '' }}检验检测能力,{{ $info['city_name'] ?? '' }}检验检测能力" />
        @include('web.QualityControl.CertificateSchedule.layout_public.pagehead')
		<style>
			a.totop {
				position: fixed; right: 20px; bottom: 150px;
				display: block;
				background-color: #fff; border:1px solid #d5d6d8;
				text-align: center; border-radius: 3px; line-height: 50px; height: 50px; width:50px;
			}
			a.totop:hover {
				border:1px solid #d1d1d2;
				background-color: #e4e5e8;
			}
			mark {
				background: orange;
				color: black;
			}
		</style>
	</head>
	<body>
        @include('web.QualityControl.CertificateSchedule.layout_public.header')
		<div class="details-header" id="top">
			<div class="wrap">
				<!-- <div class="com-logo">

				</div> -->
				<div class="com-name">
                    {{ $info['company_name'] ?? '' }}
				</div>
				<div class="content-info">
					<p>CMA证书编号：<span>{{ $info['company_certificate_no'] ?? '' }}</span></p>
					<p>发证日期：<span><!-- {{ $info['certificate_detail']['valid_date'] ?? '' }} --></span></p>
					<p>证书有效期：<span> {{ $info['certificate_detail']['valid_date'] ?? '' }}</span></p>
					<div class="c"></div>
					<p>联系人：<span>{{ $info['company_contact_name'] ?? '' }}</span></p>
					<p>联系电话：<span>{{ $info['company_contact_mobile'] ?? '' }}</span></p>
					<p>联系地址：<span>{{ $info['addr'] ?? '' }}</span></p>

				</div>
				<div class="c"></div>
			</div>
		</div>





{{--        <script src="https://cdn.jsdelivr.net/mark.js/7.0.0/jquery.mark.min.js"></script>--}}
        <script src="{{asset('quality/CertificateSchedule/js/jquery.mark.min.js')}}"></script>

        <script type="text/javascript">

    $(function() {
        $("input").on("input.highlight", function() {
            // Determine specified search term
            var searchTerm = $(this).val();
            // Highlight search term inside a specific context
            $("#context").unmark().mark(searchTerm);
        }).trigger("input.highlight").focus();
    });

</script>

			<!-- <div class="zhengshu box1">
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
				</div> -->

				<style>
					.ssnavwrap{   text-align:left;  }
					.ssnavwrap .hd{  height: 44px;   position:relative; }
					.ssnavwrap .hd ul{ float:left;  position:absolute; left:0px; top:0px;   }
					.ssnavwrap .hd ul li{ float:left; height: 44px;  line-height: 42px;  padding:0 15px;   cursor:pointer;   }
					.ssnavwrap .hd ul li.on{ height: 42px; line-height: 42px;  background-color: #fff; border-top:2px solid #0060CD;border-bottom:2px solid #fff; }

					.ssnavwrap .bd ul{   zoom:1;  }
					input.searloc {
						position: absolute; top:4px; right: 20px;
						height: 32px; width: 350px;
						border:1px solid #888;
					}
				</style>
		<div class="ssnavwrap" id="context">
					<div class="ssnav">
						<div class="hd inner">
							<ul><li>批准的授权签字人及领域</li><li>批准的检验检测能力能力范围</li></ul>
							<input type="text" value="" class="searloc" placeholder="输入关键词查询">
						</div>
					</div>
					<div class="bd">
						<ul>
							<div class="det-floor1" >
									<div class="qianziren box1">
										<div class="hd">批准的授权签字人及领域</div>
										<div class="bd">
											<table class="table" style="width: 100%;">
												<colgroup>
													<col width="200">
												    <col width="240">
												    <col width="150">
												    <col width="240">
												    <col>
												</colgroup>
												<thead>
													<tr align="center">
														<th>姓名</th>
														<th>职务</th>
														<th>手机</th>
														<th>身份证号</th>
														<th>批准授权签字范围</th>
													</tr>
												</thead>
												<tbody>
							                    <?php
							                    $user_auth_list = $info['user_auth_list'] ?? [];
							                    ?>
							                    @foreach ($user_auth_list as $k => $user_info)
															<tr>
																<td align="center">{{ $user_info['real_name'] ?? '' }}</td>

																<td>{{ $user_info['role_num_text'] ?? '' }}</td>
																<td>{{ $user_info['mobile'] ?? '' }}</td>
																<td>{{ $user_info['id_number'] ?? '' }}</td>
																<td>{{ $user_info['sign_range'] ?? '' }}</td>
															</tr>
							                    @endforeach
												</tbody>
											</table>
										</div>
									</div>

									<div class="c"></div>

							</div>


						</ul>
						<ul>

							<div class="wrap" style="width: 80%; margin-top:20px;">
								<div class="box1" style="min-height: 500px;">
									<div class="hd">
										检验检测能力表
									</div>
									<div class="bd">

										<table border="" cellspacing="" cellpadding="" class="table wb100">
											<colgroup>
												  <col width="120">
												  <col width="120">
												  <col width="120">
												  <col>
												  <col width="150">
												  <col width="150">
												  <col width="180">
												  <col width="120">
												  <col>
											</colgroup>
											<thead>
												<tr align="center">
													<th>产品类别</th>
													<th>检测产品</th>
													<th>检测参数</th>
													<th>依据的标准（方法）</th>
													<th>限制范围</th>
							                        <th>说明</th>
													<th>场所地址</th>
													<th>批准日期</th>
												</tr>
											</thead>
											<tbody id="data_list">
							                <?php
							                $certificate_list = $info['certificate_list'] ?? [];
							                ?>
							                @foreach ($certificate_list as $k => $v)
												<tr>
													<td  align="center" class="category_name">{{ $v['category_name'] ?? '' }}</td>
													<td  align="center" class="project_name">{{ $v['project_name'] ?? '' }}</td>
													<td class="param_name">{{ $v['param_name'] ?? '' }}</td>
													<td class="method_name">{{ $v['method_name'] ?? '' }}</td>
													<td class="limit_range">{{ $v['limit_range'] ?? '' }}</td>
							                        <td class="explain_text">{{ $v['explain_text'] ?? '' }}</td>
													<td class="addr">{{ $v['addr'] ?? '' }}</td>
													<td class="ratify_date">{{ $v['ratify_date'] ?? '' }}</td>
												</tr>
							                @endforeach
											</tbody>
										</table>
									</div>

								</div>
							</div>


						</ul>


					</div>
				</div>
				<script type="text/javascript">jQuery(".ssnavwrap").slide();</script>
		<div class="k20"></div>



		<div class="c"></div>
		<div class="k20"></div>
		<div class="k50"></div>

		<a href="#top" target="_self" class="totop" >顶部</a>
		<!-- <div class="floor2">
			<div class="wrap">
				<div class="adv1 adva1">权威数据</div>
				<div class="adv1 adva2">精确查询</div>
				<div class="adv1 adva3">实时更新</div>
							<div class="c"></div>
			</div>
		</div> -->
        @include('web.QualityControl.CertificateSchedule.layout_public.footer')
	</body>
</html>

<script src="{{asset('static/js/custom/common.js')}}?2"></script>
<script type="text/javascript">

    $(function(){
        //提交
        // $(document).on("keypress",".searloc",function(event){
        //     if(event.keyCode == 13) {
        //         var obj = $(this);
        //         searchKey(obj);
        //         return false;
        //     }
        // })
        // initAttr();
    });
    function initAttr() {
        $('#data_list').find('tr').each(function () {
            var trObj = $(this);
            var category_name_obj = trObj.find('.category_name');
            if(category_name_obj.length > 0){
                category_name_obj.data('old', category_name_obj.html());
            }

            var project_name_obj = trObj.find('.project_name');
            if(project_name_obj.length > 0){
                project_name_obj.data('old', project_name_obj.html());
            }

            var param_name_obj = trObj.find('.param_name');
            if(param_name_obj.length > 0){
                param_name_obj.data('old', param_name_obj.html());
            }

            var method_name_obj = trObj.find('.method_name');
            if(method_name_obj.length > 0){
                method_name_obj.data('old', method_name_obj.html());
            }

            var limit_range_obj = trObj.find('.limit_range');
            if(limit_range_obj.length > 0){
                limit_range_obj.data('old', limit_range_obj.html());
            }

            var explain_text_obj = trObj.find('.explain_text');
            if(explain_text_obj.length > 0){
                explain_text_obj.data('old', explain_text_obj.html());
            }

            var addr_obj = trObj.find('.addr');
            if(addr_obj.length > 0){
                addr_obj.data('old', addr_obj.html());
            }

            var ratify_date_obj = trObj.find('.ratify_date');
            if(ratify_date_obj.length > 0){
                ratify_date_obj.data('old', ratify_date_obj.html());
            }
        });
    }

    function strInCount(str, findStr){
        console.log('==str==', str);
        console.log('==findStr==', findStr);
        return str.split(findStr).length - 1;
    }

    // str 原字符
    // oldStr 要替换的字符
    // newStr 新的字符
    function replaceAllStr(str, oldStr , newStr) {
        // 要替换全部匹配项，可以使用正则表达式：
        // var str = "a<br/>b<br/>c<br/>";
        // re = new RegExp("<br/>","g"); //定义正则表达式
        var re = new RegExp(oldStr,"g");
        //第一个参数是要替换掉的内容，第二个参数"g"表示替换全部（global）。
        // var Newstr = str.replace(re, ""); //第一个参数是正则表达式。
        var Newstr = str.replace(re, newStr);
        //本例会将全部匹配项替换为第二个参数。能将所有的</br>换为空的
        // alert(Newstr); //内容为：abc
        return Newstr;
    }
    function getNewStr(str) {
        return '<span style="color: red;background-color: #1b6eab;font-weight: bold;">' + str + '</span>';
    }
    // obj 输入框对象
    function searchKey(obj) {

        var key = obj.val();
        // alert('你输入的内容为：' + obj.val());
        if(judge_empty(key)){
            $('#data_list').find('tr').each(function () {
                var trObj = $(this);
                trObj.show();
                var category_name_obj = trObj.find('.category_name');
                if(category_name_obj.length > 0){
                    category_name_obj.html(category_name_obj.data('old'));
                }

                var project_name_obj = trObj.find('.project_name');
                if(project_name_obj.length > 0){
                    project_name_obj.html(project_name_obj.data('old'));
                }

                var param_name_obj = trObj.find('.param_name');
                if(param_name_obj.length > 0){
                    param_name_obj.html(param_name_obj.data('old'));
                }

                var method_name_obj = trObj.find('.method_name');
                if(method_name_obj.length > 0){
                    method_name_obj.html(method_name_obj.data('old'));
                }

                var limit_range_obj = trObj.find('.limit_range');
                if(limit_range_obj.length > 0){
                    limit_range_obj.html(limit_range_obj.data('old'));
                }

                var explain_text_obj = trObj.find('.explain_text');
                if(explain_text_obj.length > 0){
                    explain_text_obj.html(explain_text_obj.data('old'));
                }

                var addr_obj = trObj.find('.addr');
                if(addr_obj.length > 0){
                    addr_obj.html(addr_obj.data('old'));
                }

                var ratify_date_obj = trObj.find('.ratify_date');
                if(ratify_date_obj.length > 0){
                    ratify_date_obj.html(ratify_date_obj.data('old'));
                }
            });
            return false;
        }
        var is_show = false;
        $('#data_list').find('tr').each(function () {
            var trObj = $(this);
            is_show = false;
            var category_name_obj = trObj.find('.category_name');
            if(category_name_obj.length > 0){
                var category_name = category_name_obj.data('old');
                if(!judge_empty(category_name) && strInCount(category_name, key) > 0){
                    is_show = true;
                    category_name = replaceAllStr(category_name, key , getNewStr(key))
                }
                category_name_obj.html(category_name);
            }

            var project_name_obj = trObj.find('.project_name');
            if(project_name_obj.length > 0){
                var project_name = project_name_obj.data('old');
                if(!judge_empty(project_name) && strInCount(project_name, key) > 0){
                    is_show = true;
                    project_name = replaceAllStr(project_name, key , getNewStr(key))
                }
                project_name_obj.html(project_name);
            }

            var param_name_obj = trObj.find('.param_name');
            if(param_name_obj.length > 0){
                var param_name = param_name_obj.data('old');
                if(!judge_empty(param_name) && strInCount(param_name, key) > 0){
                    is_show = true;
                    param_name = replaceAllStr(param_name, key , getNewStr(key))
                }
                param_name_obj.html(param_name);
            }

            var method_name_obj = trObj.find('.method_name');
            if(method_name_obj.length > 0){
                var method_name = method_name_obj.data('old');
                if(!judge_empty(method_name) && strInCount(method_name, key) > 0){
                    is_show = true;
                    method_name = replaceAllStr(method_name, key , getNewStr(key))
                }
                method_name_obj.html(method_name);
            }

            var limit_range_obj = trObj.find('.limit_range');
            if(limit_range_obj.length > 0){
                var limit_range = limit_range_obj.data('old');
                if(!judge_empty(limit_range) && strInCount(limit_range, key) > 0){
                    is_show = true;
                    limit_range = replaceAllStr(limit_range, key , getNewStr(key))
                }
                limit_range_obj.html(limit_range);
            }

            var explain_text_obj = trObj.find('.explain_text');
            if(explain_text_obj.length > 0){
                var explain_text = explain_text_obj.data('old');
                if(!judge_empty(explain_text) && strInCount(explain_text, key) > 0){
                    is_show = true;
                    explain_text = replaceAllStr(explain_text, key , getNewStr(key))
                }
                explain_text_obj.html(explain_text);
            }

            var addr_obj = trObj.find('.addr');
            if(addr_obj.length > 0){
                var addr = addr_obj.data('old');
                if(!judge_empty(addr) && strInCount(addr, key) > 0){
                    is_show = true;
                    addr = replaceAllStr(addr, key , getNewStr(key))
                }
                addr_obj.html(addr);
            }

            var ratify_date_obj = trObj.find('.ratify_date');
            if(ratify_date_obj.length > 0){
                var ratify_date = ratify_date_obj.data('old');
                if(!judge_empty(ratify_date) && strInCount(ratify_date, key) > 0){
                    is_show = true;
                    ratify_date = replaceAllStr(ratify_date, key , getNewStr(key))
                }
                ratify_date_obj.html(ratify_date);
            }
            console.log('啊顶顶顶顶顶');
            if(is_show){
                trObj.show();
                console.log('显示行');
            }else{
                trObj.hide();
                console.log('隐藏行');
            }
        });
    }
</script>
