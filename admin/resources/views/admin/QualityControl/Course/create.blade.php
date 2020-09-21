

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
                    <input type="text" class="inp wnormal"  name="course_content" value="{{ $info['course_content']['course_content'] ?? '' }}"/>
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
                    <label><input type="radio" name="status_online" value="1" title="上架" {{$info['status_online'] == 1 ? 'checked' : null}}>上架</label>
                    <label><input type="radio" name="status_online" value="2" title="下架" {{$info['status_online'] == 2 ? 'checked' : null}}>下架</label>
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
    // 上传图片变量
    var FILE_UPLOAD_URL = "{{ url('api/admin/upload') }}";// 文件上传提交地址 'your/file/upload/url'
    var PIC_DEL_URL = "{{ url('api/admin/upload/ajax_del') }}";// 删除图片url
    var MULTIPART_PARAMS = {pro_unit_id:'0'};// 附加参数	函数或对象，默认 {}
    var LIMIT_FILES_COUNT = 1;//   限制文件上传数目	false（默认）或数字
    var MULTI_SELECTION = false;//  是否可用一次选取多个文件	默认 true false
    var FLASH_SWF_URL = "{{asset('dist/lib/uploader/Moxie.swf') }}";// flash 上传组件地址  默认为 lib/uploader/Moxie.swf
    var SILVERLIGHT_XAP_URL = "{{asset('dist/lib/uploader/Moxie.xap') }}";// silverlight_xap_url silverlight 上传组件地址  默认为 lib/uploader/Moxie.xap  请确保在文件上传页面能够通过此地址访问到此文件。
    var SELF_UPLOAD = true;//  是否自己触发上传 TRUE/1自己触发上传方法 FALSE/0控制上传按钮
    var FILE_UPLOAD_METHOD = 'initPic()';// 单个上传成功后执行方法 格式 aaa();  或  空白-没有
    var FILE_UPLOAD_COMPLETE = '';  // 所有上传成功后执行方法 格式 aaa();  或  空白-没有
    var FILE_RESIZE = {quuality: 40};
    var RESOURCE_LIST = @json($info['resource_list'] ?? []);
    var PIC_LIST_JSON =  {'data_list': RESOURCE_LIST };// piclistJson 数据列表json对象格式  {‘data_list’:[{'id':1,'resource_name':'aaa.jpg','resource_url':'picurl','created_at':'2018-07-05 23:00:06'}]}

</script>
<script src="{{asset('dist/js/zui.min.js') }}"></script>
<script src="{{ asset('/js/admin/QualityControl/Course_edit.js') }}"  type="text/javascript"></script>
@component('component.upfileincludejs')
@endcomponent
</body>
</html>
