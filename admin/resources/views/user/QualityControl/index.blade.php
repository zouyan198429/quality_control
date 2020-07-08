<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>前端 -{{ config('public.webName') }}</title>
  <meta name="renderer" content="webkit">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
  <link rel="stylesheet" href="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/css/layui.css?8')}}" media="all">
  <link rel="stylesheet" href="{{asset('layui-admin-v1.2.1/src/layuiadmin/style/admin.css')}}" media="all">

  <script>
  /^http(s*):\/\//.test(location.href) || alert('请先部署到 localhost 下再访问');
  </script>
</head>
<body class="layui-layout-body">

  <div id="LAY_app">
    <div class="layui-layout layui-layout-admin">
      <div class="layui-header">
        <!-- 头部区域 -->
        <ul class="layui-nav layui-layout-left">
          <li class="layui-nav-item layadmin-flexible" lay-unselect>
            <a href="javascript:;" layadmin-event="flexible" title="侧边伸缩">
              <i class="layui-icon layui-icon-shrink-right" id="LAY_app_flexible"></i>
            </a>
          </li>
          {{--<li class="layui-nav-item layui-hide-xs" lay-unselect>--}}
            {{--<a href="http://www.layui.com/admin/" target="_blank" title="前台">--}}
              {{--<i class="layui-icon layui-icon-website"></i>--}}
            {{--</a>--}}
          {{--</li>--}}
          <li class="layui-nav-item" lay-unselect>
            <a href="javascript:;" layadmin-event="refresh" title="刷新">
              <i class="layui-icon layui-icon-refresh-3"></i>
            </a>
          </li>
          {{----}}
          {{--<li class="layui-nav-item layui-hide-xs" lay-unselect>--}}
            {{--<input type="text" placeholder="搜索..." autocomplete="off" class="layui-input layui-input-search" layadmin-event="serach" lay-action="{{ url('layui/template/search') }}?keywords=">--}}
          {{--</li>--}}
          {{----}}
        </ul>
        <ul class="layui-nav layui-layout-right" lay-filter="layadmin-layout-right">

          {{--<li class="layui-nav-item" lay-unselect>--}}
            {{--<a lay-href="{{ url('layui/app/message/index') }}" layadmin-event="message" lay-text="消息中心">--}}
              {{--<i class="layui-icon layui-icon-notice"></i>  --}}
              {{----}}
              {{--<!-- 如果有新消息，则显示小圆点 -->--}}
              {{--<span class="layui-badge-dot"></span>--}}
            {{--</a>--}}
          {{--</li>--}}
          <li class="layui-nav-item layui-hide-xs" lay-unselect>
            <a href="javascript:;" layadmin-event="theme">
              <i class="layui-icon layui-icon-theme"></i>
            </a>
          </li>

          {{--<li class="layui-nav-item layui-hide-xs" lay-unselect>--}}
            {{--<a href="javascript:;" layadmin-event="note">--}}
              {{--<i class="layui-icon layui-icon-note"></i>--}}
            {{--</a>--}}
          {{--</li>--}}

          <li class="layui-nav-item layui-hide-xs" lay-unselect>
            <a href="javascript:;" layadmin-event="fullscreen">
              <i class="layui-icon layui-icon-screen-full"></i>
            </a>
          </li>
          <li class="layui-nav-item" lay-unselect>
            <a href="javascript:;">
              <cite>{{ $baseArr['real_name'] ?? '' }}</cite>
            </a>
            <dl class="layui-nav-child">
              <dd><a lay-href="{{ url('user/info') }}">基本资料</a></dd>
              <dd><a lay-href="{{ url('user/password') }}">修改密码</a></dd>
              <hr>
              <dd  style="text-align: center;"><a href="{{ url('user/logout') }}">退出</a></dd>
            </dl>
          </li>

          {{--<li class="layui-nav-item layui-hide-xs" lay-unselect>--}}
            {{--<a href="javascript:;" layadmin-event="about"><i class="layui-icon layui-icon-more-vertical"></i></a>--}}
          {{--</li>--}}
          <li class="layui-nav-item layui-show-xs-inline-block layui-hide-sm" lay-unselect>
            <a href="javascript:;" layadmin-event="more"><i class="layui-icon layui-icon-more-vertical"></i></a>
          </li>
        </ul>
      </div>

      <!-- 侧边菜单 -->
      <div class="layui-side layui-side-menu">
        <div class="layui-side-scroll">
          {{--<div class="layui-logo" lay-href="{{ url('layui/home/console') }}">--}}
          <div class="layui-logo" lay-href="{{ url('/help/index.html') }}">
            <span>质量认证认可协会-用户中心</span>
          </div>

          <ul class="layui-nav layui-nav-tree" lay-shrink="all" id="LAY-system-side-menu" lay-filter="layadmin-system-side-menu">
            {{--<li data-name="home" class="layui-nav-item layui-nav-itemed">--}}
              {{--<a href="javascript:;" lay-tips="主页" lay-direction="2">--}}
                {{--<i class="layui-icon layui-icon-home"></i>--}}
                {{--<cite>主页</cite>--}}
              {{--</a>--}}
              {{--<dl class="layui-nav-child">--}}
                {{--<dd data-name="console" class="layui-this">--}}
                  {{--<a lay-href="{{ url('layui/home/console') }}">控制台</a>--}}
                  {{--<a lay-href="{{ url('/help/index.html') }}">控制台</a>--}}
                {{--</dd>--}}
                {{--<dd data-name="console">--}}
                  {{--<a lay-href="{{ url('layui/home/homepage1') }}">主页一</a>--}}
                {{--</dd>--}}
                {{--<dd data-name="console">--}}
                  {{--<a lay-href="{{ url('layui/home/homepage2') }}">主页二</a>--}}
                {{--</dd>--}}
              {{--</dl>--}}
            {{--</li>--}}

            <li data-name="user" class="layui-nav-item">
              <a href="javascript:;" lay-tips="会员管理" lay-direction="2">
                <i class="layui-icon layui-icon-component"></i>
                <cite>会员管理</cite>
              </a>
                <dl class="layui-nav-child">
                    <dd>
                        <a lay-href="{{ url('admin/company') }}?company_grade=1">非会员单位</a>
                    </dd>
                </dl>
                <dl class="layui-nav-child">
                    <dd>
                        <a lay-href="{{ url('admin/company') }}?company_grade=2">会员单位</a>
                    </dd>
                </dl>
                <dl class="layui-nav-child">
                    <dd>
                        <a lay-href="{{ url('admin/company') }}?company_grade=4">理事单位</a>
                    </dd>
                </dl>
                <dl class="layui-nav-child">
                    <dd>
                        <a lay-href="{{ url('admin/company') }}?company_grade=8">常务理事单位</a>
                    </dd>
                </dl>
                <dl class="layui-nav-child">
                    <dd>
                        <a lay-href="{{ url('admin/company') }}?company_grade=16">理事长单位</a>
                    </dd>
                </dl>
              <dl class="layui-nav-child">
                <dd>
                  <a lay-href="{{ url('admin/user') }}">人员列表</a>
                </dd>
              </dl>
                <dl class="layui-nav-child">
                    <dd>
                        <a lay-href="{{ url('admin/staff') }}">个人会员</a>
{{--                        没有所属企业的会员--}}
                    </dd>
                </dl>
            </li>
              <li data-name="user" class="layui-nav-item">
                  <a href="javascript:;" lay-tips="能力验证" lay-direction="2">
                      <i class="layui-icon layui-icon-component"></i>
                      <cite>能力验证</cite>
                  </a>
                  <dl class="layui-nav-child">
                      <dd>
                          <a lay-href="javascript:void(0);">项目管理</a>
                      </dd>
                  </dl>
                  <dl class="layui-nav-child">
                      <dd>
                          <a lay-href="javascript:void(0);">用户报名</a>
                      </dd>
                  </dl>
                  <dl class="layui-nav-child">
                      <dd>
                          <a lay-href="{{ url('admin/ability_type') }}?company_grade=4">领域管理</a>
                      </dd>
                  </dl>
              </li>
            <li data-name="user" class="layui-nav-item">
              <a href="javascript:;" lay-tips="基础设置" lay-direction="2">
                <i class="layui-icon layui-icon-user"></i>
                <cite>基础设置</cite>
              </a>
              <dl class="layui-nav-child">
                <dd>
                  <a lay-href="{{ url('admin/staff') }}">管理员管理</a>
                </dd>
                  <dd>
{{--                      <a lay-href="{{ url('admin/company') }}">企业管理</a>--}}
{{--                  </dd>--}}
{{--                  <dd>--}}
{{--                      <a lay-href="{{ url('admin/user') }}">个人会员</a>--}}
{{--                  </dd>--}}
                  <dd>
                      <a lay-href="{{ url('admin/industry') }}">企业行业管理</a>
                  </dd>
                  <dd>
                      <a lay-href="{{ url('admin/citys') }}">城市管理</a>
                  </dd>
                  <dd>
                      <a lay-href="{{ url('admin/company_certificate_type') }}">资质证书类型</a>
                  </dd>
{{--                  <dd>--}}
{{--                      <a lay-href="{{ url('admin/ability_type') }}">能力验证行业分类</a>--}}
{{--                  </dd>--}}
                  <dd>
                      <a lay-href="{{ url('admin/sms_code') }}">短信日志管理</a>
                  </dd>
              </dl>
            </li>
          </ul>
        </div>
      </div>

      <!-- 页面标签 -->
      <div class="layadmin-pagetabs" id="LAY_app_tabs">
        <div class="layui-icon layadmin-tabs-control layui-icon-prev" layadmin-event="leftPage"></div>
        <div class="layui-icon layadmin-tabs-control layui-icon-next" layadmin-event="rightPage"></div>
        <div class="layui-icon layadmin-tabs-control layui-icon-down">
          <ul class="layui-nav layadmin-tabs-select" lay-filter="layadmin-pagetabs-nav">
            <li class="layui-nav-item" lay-unselect>
              <a href="javascript:;"></a>
              <dl class="layui-nav-child layui-anim-fadein">
                <dd layadmin-event="closeThisTabs"><a href="javascript:;">关闭当前标签页</a></dd>
                <dd layadmin-event="closeOtherTabs"><a href="javascript:;">关闭其它标签页</a></dd>
                <dd layadmin-event="closeAllTabs"><a href="javascript:;">关闭全部标签页</a></dd>
              </dl>
            </li>
          </ul>
        </div>
        <div class="layui-tab" lay-unauto lay-allowClose="true" lay-filter="layadmin-layout-tabs">
          <ul class="layui-tab-title" id="LAY_app_tabsheader">
            {{--<li lay-id="{{ url('layui/home/console') }}" lay-attr="{{ url('layui/home/console') }}" class="layui-this"><i class="layui-icon layui-icon-home"></i></li>--}}
            <li lay-id="{{ url('/help/index.html') }}" lay-attr="{{ url('/help/index.html') }}" class="layui-this"><i class="layui-icon layui-icon-home"></i></li>
          </ul>
        </div>
      </div>


      <!-- 主体内容 -->
      <div class="layui-body" id="LAY_app_body">
        <div class="layadmin-tabsbody-item layui-show">
          {{--<iframe src="{{ url('layui/home/console') }}" frameborder="0" class="layadmin-iframe"></iframe>--}}
          <iframe src="{{ url('/help/index.html') }}" frameborder="0" class="layadmin-iframe"></iframe>
        </div>
      </div>

      <!-- 辅助元素，一般用于移动设备下遮罩 -->
      <div class="layadmin-body-shade" layadmin-event="shade"></div>
    </div>
  </div>

  <script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.js')}}"></script>
  {{--<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.all.js')}}"></script>--}}

  <script>
  layui.config({
    base: '/layui-admin-v1.2.1/src/layuiadmin/' //静态资源所在路径
  }).extend({
    index: 'lib/index' //主入口模块
  }).use('index');
  </script>
  {{--
  <!-- 百度统计 -->
  <script>
  var _hmt = _hmt || [];
  (function() {
    var hm = document.createElement("script");
    hm.src = "https://hm.baidu.com/hm.js?d214947968792b839fd669a4decaaffc";
    var s = document.getElementsByTagName("script")[0];
    s.parentNode.insertBefore(hm, s);
  })();
  </script>
  --}}
</body>
</html>


