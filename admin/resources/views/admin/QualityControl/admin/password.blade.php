

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>设置我的密码</title>
  <meta name="renderer" content="webkit">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
  <link rel="stylesheet" href="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/css/layui.css')}}" media="all">
  <link rel="stylesheet" href="{{asset('layui-admin-v1.2.1/src/layuiadmin/style/admin.css')}}" media="all">
</head>
<body>

  <div class="layui-fluid">
    <div class="layui-row layui-col-space15">
      <div class="layui-col-md12">
        <div class="layui-card">
          <div class="layui-card-header">修改密码</div>
          <div class="layui-card-body" pad15>

            <form  method="post"  id="addForm">
            <div class="layui-form" lay-filter="">
              <div class="layui-form-item">
                <label class="layui-form-label">当前密码</label>
                <div class="layui-input-inline">
                  <input type="password" name="old_password" lay-verify="required" lay-verType="tips" class="layui-input"  placeholder="请输入旧密码" >
                </div>
              </div>
              <div class="layui-form-item">
                <label class="layui-form-label">新密码</label>
                <div class="layui-input-inline">
                  <input type="password" name="admin_password" lay-verify="pass" lay-verType="tips" autocomplete="off" id="LAY_password" class="layui-input"   placeholder="请输入密码" >
                </div>
                <div class="layui-form-mid layui-word-aux">6到16个字符</div>
              </div>
              <div class="layui-form-item">
                <label class="layui-form-label">确认新密码</label>
                <div class="layui-input-inline">
                  <input type="password" name="sure_password" lay-verify="repass" lay-verType="tips" autocomplete="off" class="layui-input"  placeholder="请再次输入密码">
                </div>
              </div>
              <div class="layui-form-item">
                <div class="layui-input-block">
                  <button class="layui-btn"  id="submitBtn" >确认修改</button>
                </div>
              </div>
            </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="{{asset('js/jquery-3.3.1.min.js')}}"></script>
  <script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.all.js')}}"></script>
  {{--<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.js')}}"></script>--}}
  @include('public.dynamic_list_foot')
  {{--
  <script>
  layui.config({
    base: '/layui-admin-v1.2.1/src/layuiadmin/' //静态资源所在路径
  }).extend({
    index: 'lib/index' //主入口模块
  }).use(['index', 'set']);
  </script>
  --}}
  <script>

    // hidden_option 8192:调用父窗品的方法：[public/js目录下的] 项目目录+数据功能目录+当前文件名称 【有_线，则去掉】
    // 其它地方弹出此窗，保存完成时调用的父窗口方法名称 参数(obj:当前表单值对像, result:保存接口返回的结果，operateNum:自己定义的一个编号【页面有多处用到时用--通知父窗口调用位置】)
    var PARENT_BUSINESS_FUN_NAME = "adminQualityControlrrrddddedit";

    var SAVE_URL = "{{ url('api/admin/ajax_password_save') }}";
      var SET_URL = "{{url('admin/logout')}}";//"{{url('admin/password')}}"
  </script>
  <script src="{{ asset('js/common/admin_password.js') }}"  type="text/javascript"></script>
</body>
</html>
