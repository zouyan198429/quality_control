

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>九宫格</title>
  <meta name="renderer" content="webkit">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
  <link rel="stylesheet" href="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/css/layui.css')}}" media="all">
  <link rel="stylesheet" href="{{asset('layui-admin-v1.2.1/src/layuiadmin/style/admin.css')}}" media="all">
</head>
<body>

  <style>
  /* 这段样式只是用于演示 */
  #LAY-component-grid-speed-dial .layui-card-body{display: flex; justify-content: center; flex-direction: column; text-align: center; font-size: 20px;}
  #LAY-component-grid-speed-dial .layui-card-body:hover{background-color: #FAFAFA;}
  </style>

  <div class="layui-fluid" id="LAY-component-grid-speed-dial">
    <div class="layui-row layui-col-space1">
      <div class="layui-col-xs4">
        <!-- 填充内容 -->
        <div class="layui-card">
          <div class="layui-card-body">1</div>
        </div>
      </div>
      <div class="layui-col-xs4">
        <div class="layui-card">
          <div class="layui-card-body">2</div>
        </div>
      </div>
      <div class="layui-col-xs4">
        <div class="layui-card">
          <div class="layui-card-body">3</div>
        </div>
      </div>
      <div class="layui-col-xs4">
        <div class="layui-card">
          <div class="layui-card-body">4</div>
        </div>
      </div>
      <div class="layui-col-xs4">
        <div class="layui-card">
          <div class="layui-card-body">5</div>
        </div>
      </div>
      <div class="layui-col-xs4">
        <div class="layui-card">
          <div class="layui-card-body">6</div>
        </div>
      </div>
      <div class="layui-col-xs4">
        <div class="layui-card">
          <div class="layui-card-body">7</div>
        </div>
      </div>
      <div class="layui-col-xs4">
        <div class="layui-card">
          <div class="layui-card-body">8</div>
        </div>
      </div>
      <div class="layui-col-xs4">
        <div class="layui-card">
          <div class="layui-card-body">9</div>
        </div>
      </div>
    </div>
  </div>

  
  <script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.js')}}"></script>
  <script>
  layui.config({
    base: '/layui-admin-v1.2.1/src/layuiadmin/' //静态资源所在路径
  }).extend({
    index: 'lib/index' //主入口模块
  }).use(['index'], function(){
    var $ = layui.$
    ,admin = layui.admin
    ,element = layui.element;
    
    element.render('breadcrumb', 'breadcrumb');
    
    //监听窗口尺寸改变事件，控制宽度相同
    admin.resize(function(){
      var cardBody = $('#LAY-component-grid-speed-dial .layui-card-body');
      cardBody.height(cardBody.width())
    });
  });
  </script>
</body>
</html>
