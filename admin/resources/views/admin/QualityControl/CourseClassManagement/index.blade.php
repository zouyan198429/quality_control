<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>培训班管理</title>
    @include('admin.layout_public.pagehead')
    <link rel="stylesheet" href="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/css/layui.css')}}" media="all">
    <link rel="stylesheet" href="{{asset('layui-admin-v1.2.1/src/layuiadmin/style/admin.css')}}" media="all">
</head>
<body>

<div class="layui-fluid">
    <div class="layui-card">

        <div class="layui-card-body">
            <div class="layui-carousel layadmin-carousel layadmin-backlog" lay-anim="" lay-arrow="none" style="width: 100%;">
                <div carousel-item="">
                    <ul class="layui-row layui-col-space10 layui-this">
                        <li class="layui-col-xs3">
                            <a  class="layadmin-backlog-body">
                                <h3>报名单位</h3>
                                <p><cite>{{ $info['join_num'] ?? '0' }}</cite></p>
                            </a>
                        </li>
                        <li class="layui-col-xs3">
                            <a  class="layadmin-backlog-body">
                                <h3>报名人数</h3>
                                <p><cite>{{ $info['join_num'] ?? '0' }}</cite></p>
                            </a>
                        </li>
                        <li class="layui-col-xs3">
                            <a  class="layadmin-backlog-body">
                                <h3>报名个人</h3>
                                <p><cite>{{ $info['repair_num'] ?? '0' }}</cite></p>
                            </a>
                        </li>

                        <li class="layui-col-xs3">
                            <a class="layadmin-backlog-body">
                                <h3>会员报名</h3>
                                <p><cite>{{ $info['repair_num'] ?? '0' }}</cite></p>
                            </a>
                        </li>
                        <li class="layui-col-xs3">
                            <a class="layadmin-backlog-body">
                                <h3>非会员报名</h3>
                                <p><cite>{{ $info['repair_num'] ?? '0' }}</cite></p>
                            </a>
                        </li>
                        <li class="layui-col-xs3">
                            <a class="layadmin-backlog-body">
                                <h3>已付款单位</h3>
                                <p><cite>{{ $info['repair_num'] ?? '0' }}</cite></p>
                            </a>
                        </li>
                        <li class="layui-col-xs3">
                            <a class="layadmin-backlog-body">
                                <h3>已付款个人会员</h3>
                                <p><cite>{{ $info['repair_num'] ?? '0' }}</cite></p>
                            </a>
                        </li>
                        <li class="layui-col-xs3">
                            <a class="layadmin-backlog-body">
                                <h3>申请开票单位</h3>
                                <p><cite>{{ $info['repair_num'] ?? '0' }}</cite></p>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">班级名称</label>
                <div class="layui-form-mid layui-word-aux">
                    {{ $info['class_name'] }}
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">城市</label>
                <div class="layui-form-mid layui-word-aux">
                    {!! $info['city']['city_name'] !!}
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">备注</label>
                <div class="layui-form-mid layui-word-aux">
                    {!! $info['project_standards_text'] ?? '' !!}
                </div>
            </div>
        </div>
    </div>
</div>


<script src="{{asset('js/jquery-3.3.1.min.js')}}"></script>
<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.all.js')}}"></script>
{{--<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.js')}}"></script>--}}
@include('public.dynamic_list_foot')
<script type="text/javascript">
</script>

</body>
</html>
