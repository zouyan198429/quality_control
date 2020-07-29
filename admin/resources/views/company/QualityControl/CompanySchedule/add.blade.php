

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>数据表格</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    @include('admin.layout_public.pagehead')
    <link rel="stylesheet" href="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/css/layui.css?88')}}" media="all">
    <link rel="stylesheet" href="{{asset('layui-admin-v1.2.1/src/layuiadmin/style/admin.css?8')}}" media="all">
</head>
<body>

<div class="mm"> 
        <table class="table1">
  
            <tr>
                <th>文件<span class="must">*</span></th>
                <td>
                    <button class="btn btn-success  btn-xs import_excel"  onclick="otheraction.importExcel(this)">上传文件</button>
                    <div style="display:none;" ><input type="file" class="import_file img_input"></div>{{--导入file对象--}}
                    <span>请上传pdf或doc格式的文档</span>
                </td>
            </tr> 

        </table> 
</div>
<script src="{{asset('js/jquery-3.3.1.min.js')}}"></script>
<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.all.js')}}"></script>
@include('public.dynamic_list_foot')

<script type="text/javascript">
    var SAVE_URL = "{{ url('api/company/company_schedule/ajax_save') }}";// ajax保存记录地址
    var LIST_URL = "{{url('company/company_schedule')}}";//保存成功后跳转到的地址

    var SELECT_COMPANY_URL = "{{url('company/company/select')}}";// 选择所属企业

    var IMPORT_EXCEL_URL = "{{ url('api/company/company_schedule/import') }}";//上传文件地址
</script>
<script src="{{ asset('/js/company/QualityControl/CompanySchedule_edit.js') }}?1"  type="text/javascript"></script>
</body>
</html>
