

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>开启头部工具栏 - 数据表格</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link rel="stylesheet" href="{{asset('dist/css/zui.min.css') }}">
    @include('admin.layout_public.pagehead')
    <link rel="stylesheet" href="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/css/layui.css')}}" media="all">
    <link rel="stylesheet" href="{{asset('layui-admin-v1.2.1/src/layuiadmin/style/admin.css')}}" media="all">
</head>
<body>
<div class="mm">
    <form class="am-form am-form-horizontal" method="post"  id="addForm">
        <input type="hidden" name="id" value="{{ $info['id'] ?? 0 }}"/>
        <table class="table1">
            <tr>
                <th>课程名称<span class="must">*</span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="course_name" value="{{ $info['course_name'] ?? '' }}" placeholder="请输入课程名称"/>
                </td>
            </tr>
            <tr>
                <th>图片<span class="must">*</span></th>
                <td>
                    <div class="alert alert-warning alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <p>一次最多上传1张图片。</p>
                    </div>
                    <div class="row  baguetteBoxOne gallery ">
                        <div class="col-xs-6">
                            @component('component.upfileone.piconecode')
                                @slot('fileList')
                                    grid
                                @endslot
                                @slot('upload_url')
                                    {{ url('api/admin/upload') }}
                                @endslot
                            @endcomponent
                            {{--
                            <input type="file" class="form-control" value="">
                            --}}
                        </div>
                    </div>

                </td>
            </tr>
            <tr>
                <th>简要概述</th>
                <td>
                    <input type="text" class="inp wnormal"  name="explain_remarks" value="{{ $info['explain_remarks'] ?? '' }}" placeholder="请输入简要概述" />
                </td>
            </tr>
            <tr>
                <th>详细内容</th>
                <td>
                    <input type="text" class="inp wnormal"  name="course_content" value="{{ $info['course_content'] ?? '' }}"/>
                </td>
            </tr>
            <tr>
                <th>收费标准(会员)</th>
                <td>
                    <input type="text" class="inp wnormal"  name="price_member" value="{{ $info['price_member'] ?? '' }}"/>
                </td>
            </tr>
            <tr>
                <th>收费标准(非会员)</th>
                <td>
                    <input type="text" class="inp wnormal"  name="price_general" value="{{ $info['price_general'] ?? '' }}"/>
                </td>
            </tr>
            <tr>
                <th>显示状态</th>
                <td>
                    <div class="layui-input-block">
                        <input type="radio" name="status_online" value="1" title="上架" checked>
                        <input type="radio" name="status_online" value="2" title="下架">
                    </div>
                </td>
            </tr>
            <tr>
                <th> </th>
                <td><button class="btn btn-l wnormal"  id="submitBtn" >提交</button></td>
            </tr>

        </table>
    </form>
</div>
<script src="{{asset('js/jquery-3.3.1.min.js')}}"></script>
<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.all.js')}}"></script>
{{--<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.js')}}"></script>--}}
@include('public.dynamic_list_foot')

<script type="text/javascript">
    var SAVE_URL = "{{ url('api/admin/courses/save') }}";// ajax保存记录地址
    var LIST_URL = "{{url('admin/courses')}}";//保存成功后跳转到的地址
    var FILE_UPLOAD_URL = "{{ url('api/admin/upload') }}";// 文件上传提交地址 'your/file/upload/url'
</script>
<script src="{{asset('dist/js/zui.min.js') }}"></script>
<script src="{{ asset('/js/admin/QualityControl/Course_edit.js') }}"  type="text/javascript"></script>
@component('component.upfileincludejs')
@endcomponent
</body>
</html>
