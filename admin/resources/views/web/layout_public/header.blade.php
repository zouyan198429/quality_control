
<div class="header">
    <div class="logo">
        <img src="{{asset('staticweb/images/logo.png')}}" alt="">
        <a href="{{ url('web') }}" class="sitename">教育云助手</a>
    </div>
    <div class="header-right fr">
        <img src="{{asset('staticweb/images/icon-mess.png')}}"  alt="">
        <img src="{{asset('staticweb/images/icon-user-b.png')}}"  alt="">
        <a href="{{ url('web/teacher_templates') }}" class="member">{{ $baseArr['real_name'] ?? '' }}</a>
        <a href="{{ url('web/classes') }}" class="nav-myclass">我的班级</a>
        <a href="{{ url('web/logout') }}" class="quit">退出</a>
    </div>
</div>
