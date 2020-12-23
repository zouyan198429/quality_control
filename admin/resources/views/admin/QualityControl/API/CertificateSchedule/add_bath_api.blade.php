

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>开启头部工具栏 - 数据表格</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    @include('admin.layout_public.pagehead')
    <link rel="stylesheet" href="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/css/layui.css')}}" media="all">
    <link rel="stylesheet" href="{{asset('layui-admin-v1.2.1/src/layuiadmin/style/admin.css')}}" media="all">
</head>
<body>

{{--<div id="crumb"><i class="fa fa-reorder fa-fw" aria-hidden="true"></i> {{ $operate ?? '' }}员工</div>--}}
app_id:<input type="text"   name="app_id" value="" placeholder="请输入app_id" style="width: 80%;">
<br/>
app_secret:<input type="text"   name="app_secret" value="" placeholder="请输入app_secret" style="width: 80%;">
<hr/>
<div class="mm">
    <form class="am-form am-form-horizontal" method="post"  id="addForm">
        <input type="hidden" name="hidden_option"  value="{{ $hidden_option ?? 0 }}" />
        {{--        <input type="hidden" name="id" value="{{ $info['id'] ?? 0 }}"/>--}}
        <table class="table1">
            <tr>
                <th>机构名称<span class="must">*</span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="company_name" value="{{ $info['company_name'] ?? '' }}" placeholder="请输入机构名称"/>
                </td>
            </tr>
            <tr>
                <th>CMA证书号<span class="must">*</span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="certificate_no" value="{{ $info['certificate_no'] ?? '' }}" placeholder="请输入CMA证书号"/>
                </td>
            </tr>
            <tr>
                <th>有效起止时间<span class="must">*</span></th>
                <td>
                    <input type="text" class="inp wlong ratify_date" name="ratify_date" value="{{ $info['ratify_date'] ?? '' }}" placeholder="请选择批准日期" style="width: 150px;"  readonly="true"/>
                    -
                    <input type="text" class="inp wlong valid_date" name="valid_date" value="{{ $info['valid_date'] ?? '' }}" placeholder="请选择有效期至"  style="width: 150px;" readonly="true"/>
                </td>
            </tr>
            <tr>
                <th>实验室地址<span class="must">*</span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="addr" value="{{ $info['addr'] ?? '' }}" placeholder="请输入实验室地址"/>
                </td>
            </tr>
            <tr>
                <th>联系人<span class="must">*</span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="contact_name" value="{{ $info['contact_name'] ?? '' }}" placeholder="请输入联系人"/>
                </td>
            </tr>
            <tr>
                <th>联系人手机或电话<span class="must">*</span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="contact_mobile" value="{{ $info['contact_mobile'] ?? '' }}" placeholder="请输入联系人手机或电话"/>
                </td>
            </tr>
            <tr>
                <th>文件信息json<span class="must">*</span></th>
                <td>
                    <textarea name="file_json" placeholder="请输入文件信息json" class="layui-textarea">{{ replace_enter_char($info['file_json'] ?? '',2) }}</textarea>
                    格式：
                    [{"file_title": "能力附表文件名","file_url": "http://qualitycontrol.admin.cunwo.net/resource/company/45/excel/2020/11/09/202011091819227042d1f0f7cb0f39.xlsx","file_type": "1","schedule_type": "0"}]
                </td>
            </tr>
            <tr>
                <th>能力范围json数据<span class="must">*</span></th>
                <td>
                    <textarea name="schedule_json" placeholder="请输入能力范围json数据" class="layui-textarea">{{ replace_enter_char($info['schedule_json'] ?? '',2) }}</textarea>
                    格式：
                    [{"category_name": "类别1","project_name":"产品1","three_name":"第三级1","four_name":"","param_name":"","method_name":"标准（方法）名称","limit_range":"限制范围","explain_text":"说明"}]
                </td>
            </tr>
            <tr>
                <th> </th>
                <td><button class="btn btn-l wnormal"  id="submitBtn" >提交</button></td>
            </tr>

        </table>
    </form>
</div>
<script type="text/javascript" src="{{asset('laydate/laydate.js')}}"></script>
<script src="{{asset('js/jquery-3.3.1.min.js')}}"></script>
<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.all.js')}}"></script>
{{--<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.js')}}"></script>--}}
@include('public.dynamic_list_foot')

<script type="text/javascript">
    var SAVE_URL = "{{ url('api/market/certificate_schedule/bath_save') }}";// "{ { url('api/admin/API/certificate_schedule/ajax_bath_save') }}";// ajax保存记录地址
    var LIST_URL = "{{url('admin/API/certificate_schedule')}}";//保存成功后跳转到的地址

    var BEGIN_TIME = "{{ $info['ratify_date'] ?? '' }}" ;//批准日期
    var END_TIME = "{{ $info['valid_date'] ?? '' }}" ;//有效期至

</script>
<script type="text/ecmascript" src="{{asset('static/js/sha1.js')}}"></script>
<script type="text/ecmascript" src="{{asset('static/js/md5/md5.js')}}"></script>
<script type="text/ecmascript" src="{{asset('static/js/sign.js')}}"></script>

<script src="{{ asset('/js/admin/QualityControl/API/CertificateSchedule_bath.js') }}?6"  type="text/javascript"></script>

</body>
</html>
