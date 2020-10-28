

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>数据表格</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <!-- zui css -->
    <link rel="stylesheet" href="{{asset('dist/css/zui.min.css') }}">
    @include('admin.layout_public.pagehead')
    <link rel="stylesheet" href="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/css/layui.css?88')}}" media="all">
    <link rel="stylesheet" href="{{asset('layui-admin-v1.2.1/src/layuiadmin/style/admin.css?8')}}" media="all">
</head>
<body>

<div class="mm">
{{--    <p class="red" ><i class="ace-icon fa fa-info-circle  bigger-60"></i> 请上传资质认定的全部附表</p>--}}
    <form class="am-form am-form-horizontal" method="post"  id="addForm">
        <input type="hidden" name="id" value="{{ $info['id'] ?? 0 }}"/>
        <table class="table1">
            <tr  @if (isset($company_hidden) && $company_hidden == 1 ) style="display: none;"  @endif>
                <th>所属企业<span class="must">*</span></th>
                <td>
                    <input type="hidden" class="select_id"  name="company_id"  value="{{ $info['company_id'] ?? '' }}" />
                    <span class="select_name company_name">{{ $info['user_company_name'] ?? '' }}</span>
                    <i class="close select_close company_id_close"  onclick="clearSelect(this)" style="display: none;">×</i>
                    <button  type="button"  class="btn btn-danger  btn-xs ace-icon fa fa-plus-circle bigger-60"  onclick="otheraction.selectCompany(this)">选择所属企业</button>
                </td>
            </tr>
            <tr>
                <th>文件<span class="must">*</span></th>
                <td>
                    <input type="hidden" name="resource_id" value=""/>

                    <div class="alert alert-warning alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <p>请上传excel格式的文件</p>
                    </div>
                    <div class="row  baguetteBoxOne gallery ">
                        <div class="col-xs-6">
                            @component('component.upfileone.piconecode')
                                @slot('fileList')
                                    large
                                @endslot
                                @slot('upload_url')
                                    {{ url('api/admin/upload') }}
                                @endslot
                            @endcomponent
                        </div>
                    </div>
                    <span>请上传excel格式的文档</span>
                </td>
            </tr>
{{--            <tr>--}}
{{--                <th>文件<span class="must">*</span></th>--}}
{{--                <td>--}}
{{--                    <button class="btn btn-success  btn-xs import_excel"  onclick="otheraction.importExcel(this)">上传文件</button>--}}
{{--                    <div style="display:none;" ><input type="file" class="import_file img_input"></div>--}}{{--导入file对象--}}
{{--                    <span>请上传excel格式的文档</span>--}}
{{--                </td>--}}
{{--            </tr>--}}
            <tr>
                <th> </th>
                <td>
                    <div class="k20"></div>
                    <button class="btn btn-l wnormal"  id="submitBtn" >提交</button></td>
            </tr>

        </table>
    </form>
</div>
<script src="{{asset('js/jquery-3.3.1.min.js')}}"></script>
<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.all.js')}}"></script>
@include('public.dynamic_list_foot')

<script type="text/javascript">
    var SAVE_URL = "{{ url('api/admin/company_new_schedule/ajax_excel_save') }}";//"{ { url('api/admin/company_new_schedule/ajax_save') }}";// ajax保存记录地址
    var LIST_URL = "{{url('admin/company_new_schedule')}}";//保存成功后跳转到的地址

    var SELECT_COMPANY_URL = "{{url('admin/company/select')}}";// 选择所属企业

    var IMPORT_EXCEL_URL = "{{ url('api/admin/company_new_schedule/import') }}";//上传文件地址

    var UPLOAD_EXCEL_URL = "{{ url('api/admin/company_new_schedule/up_excel') }}";//上传excel地址
    var UPLOAD_PDF_URL = "{{ url('api/admin/company_new_schedule/up_pdf') }}";//上传pdf地址

    var FLASH_SWF_URL = "{{asset('dist/lib/uploader/Moxie.swf') }}";// flash 上传组件地址  默认为 lib/uploader/Moxie.swf
    var SILVERLIGHT_XAP_URL = "{{asset('dist/lib/uploader/Moxie.xap') }}";// silverlight_xap_url silverlight 上传组件地址  默认为 lib/uploader/Moxie.xap  请确保在文件上传页面能够通过此地址访问到此文件。

</script>
<link rel="stylesheet" href="{{asset('js/baguetteBox.js/baguetteBox.min.css')}}">
<script src="{{asset('js/baguetteBox.js/baguetteBox.min.js')}}" async></script>
{{--<script src="{{asset('js/baguetteBox.js/highlight.min.js')}}" async></script>--}}
<!-- zui js -->
<script src="{{asset('dist/js/zui.min.js') }}"></script>
<script src="{{ asset('/js/admin/QualityControl/CompanyNewSchedule_excel.js') }}?3"  type="text/javascript"></script>

<link href="{{asset('dist/lib/uploader/zui.uploader.min.css') }}" rel="stylesheet">
<script src="{{asset('dist/lib/uploader/zui.uploader.min.js') }}"></script>{{--此文件引用一次就可以了--}}
</body>
</html>