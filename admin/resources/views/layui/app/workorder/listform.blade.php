

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>layuiAdmin 工单管理 iframe 框</title>
  <meta name="renderer" content="webkit">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
  <link rel="stylesheet" href="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/css/layui.css')}}" media="all">
</head>
<body>

  <div class="layui-form" lay-filter="layuiadmin-form-order" id="layuiadmin-form-order" style="padding: 20px 30px 0 0;">
    <div class="layui-form-item">
      <label class="layui-form-label">业务性质</label>
      <div class="layui-input-inline">
        <input type="text" name="attr" lay-verify="required" placeholder="请输入业务性质" autocomplete="off" class="layui-input">
      </div>
    </div>
    <div class="layui-form-item">
      <label class="layui-form-label">工单标题</label>
      <div class="layui-input-inline">
        <input type="text" name="title" lay-verify="required" placeholder="工单标题..." autocomplete="off" class="layui-input">
      </div>
    </div>
    <div class="layui-form-item">
      <label class="layui-form-label">工单进度</label>
      <div class="layui-input-inline">
        <input type="text" name="progress" lay-verify="required" placeholder="工单进度..." autocomplete="off" class="layui-input">
      </div>
    </div>
    <div class="layui-form-item">
      <label class="layui-form-label">工单状态</label>
      <div class="layui-input-inline">
        <select name="state">
          <option value="未分配">未分配</option>
          <option value="处理中">处理中</option>
          <option value="已处理">已处理</option>
        </select>
      </div>
    </div>
    <div class="layui-form-item">
      <label class="layui-form-label">受理人员</label>
      <div class="layui-input-inline">
        <select name="accept">
          <option value="">请选择受理人员</option>
          <option value="员工-1">员工-1</option>
          <option value="员工-2">员工-2</option>
          <option value="员工-3">员工-3</option>
          <option value="员工-4">员工-4</option>
          <option value="员工-5">员工-5</option>
        </select>
      </div>
    </div>
    <div class="layui-form-item layui-hide">
      <input type="button" lay-submit lay-filter="LAY-app-workorder-submit" id="LAY-app-workorder-submit" value="确认">
    </div>
  </div>

  <script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.js')}}"></script>
  <script>
  layui.config({
    base: '/layui-admin-v1.2.1/src/layuiadmin/' //静态资源所在路径
  }).extend({
    index: 'lib/index' //主入口模块
  }).use(['index', 'form'], function(){
    var $ = layui.$
    ,form = layui.form;
    
  })
  </script>
</body>
</html>