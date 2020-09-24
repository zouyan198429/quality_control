<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>后台管理</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    @include('admin.layout_public.pagehead')
    <link rel="stylesheet" href="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/css/layui.css')}}" media="all">
    <link rel="stylesheet" href="{{asset('layui-admin-v1.2.1/src/layuiadmin/style/admin.css')}}" media="all">

</head>
<body>

<div class="layui-fluid">
    <div class="layui-card">

        <div class="layui-row layui-card-body">

            <form class="am-form am-form-horizontal" method="post"  id="addForm">
                <input type="hidden" name="course_id" value="{{ $info['id'] }}"/>

                <fieldset class="layui-elem-field layui-field-title">
                    <legend>培训信息</legend>
                </fieldset>
                <div class="layui-form-item">
                    <label class="layui-form-label">培训名称</label>
                    <div class="layui-input-block">
                        <p style="padding: 7px 15px;">{{ $info['course_name'] ?? '' }}</p>
                    </div>
                </div>
                <div class="layui-form-item" style="display: flex;">
                    <label class="layui-form-label" style="padding-top: 25px;">选择学员</label>
                    <div class="layui-card-body">
                        <table lay-even class="layui-table table2 tableWidthFixed"  lay-size="lg"  id="dynamic-table">
                            <colgroup>
                                <col width="50">
                                <col width="120">
                                <col width="50">
                                <col width="150">
                                <col width="200">
                            </colgroup>
                            <thead>
                            <tr>
                                <th>
                                    <label class="pos-rel">
                                        <input type="checkbox"  class="ace check_all"  value="" onclick="selectAll(this)"/>
                                    </label>
                                </th>
                                <th>姓名</th>
                                <th>性别</th>
                                <th>手机号</th>
                                <th>身份证</th>
                            </tr>
                            </thead>
                            <tbody id="data_list">
                            </tbody>
                        </table>
                        <div class="mmfoot">
                            <div class="mmfleft"></div>
                            <div class="pagination">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">联络人员</label>
                    <div class="layui-input-block">
                        <input type="text" name="contacts" value="{{ $info['contacts'] ?? '' }}"  lay-verify="title" autocomplete="off" placeholder="请输入联络人" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">联络人电话</label>
                    <div class="layui-input-block">
                        <input type="text" name="tel" value="{{ $info['tel'] ?? '' }}"  lay-verify="title" autocomplete="off" placeholder="请输入联络人电话" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <button class="btn btn-l wnormal"  id="submitBtn" >提交</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript" src="{{asset('laydate/laydate.js')}}"></script>
<script src="{{asset('js/jquery-3.3.1.min.js')}}"></script>
<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.all.js')}}"></script>
{{--<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.js')}}"></script>--}}
@include('public.dynamic_list_foot')
<script type="text/javascript">
    var AUTO_READ_FIRST = true;//自动读取第一页 true:自动读取 false:指定地方读取
    // var LIST_FUNCTION_NAME = "reset_list_self";// 列表刷新函数名称, 需要列表刷新同步时，使用自定义方法reset_list_self；异步时没有必要自定义
    var SAVE_URL = "{{ url('api/company/courses/sign-up') }}";// ajax保存记录地址
    var LIST_URL = "{{url('company/courses')}}";//保存成功后跳转到的地址
    var AJAX_URL = "{{ url('api/company/courses/staff_list') }}";//ajax请求的url
    var IFRAME_TAG_KEY = "";// "QualityControl\\CTAPIStaff";// 获得模型表更新时间的关键标签，可为空：不获取
    var IFRAME_TAG_TIMEOUT = 60000;// 获得模型表更新时间运行间隔 1000:1秒 ；可以不要此变量：默认一分钟
</script>
<script src="{{asset('js/common/list.js')}}"></script>
<script src="{{ asset('/js/company/QualityControl/Course_form.js') }}"  type="text/javascript"></script>
</body>
</html>

