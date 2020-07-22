<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>管理后台</title>
  <meta name="renderer" content="webkit">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    @include('admin.layout_public.pagehead')
    <link rel="stylesheet" href="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/css/layui.css')}}" media="all">
    <link rel="stylesheet" href="{{asset('layui-admin-v1.2.1/src/layuiadmin/style/admin.css')}}" media="all">
{{--  <link rel="stylesheet" href="../../layuiadmin/layui/css/layui.css" media="all">--}}
{{--  <link rel="stylesheet" href="../../layuiadmin/style/admin.css" media="all">--}}
</head>
<body>
<div class="layui-fluid">
  <div class="layui-card">


        <div class="layui-card-header">
            报名单位管理
        </div>
        <div class="layui-card">
            <div class="layui-card-body">


              <table class="layui-table">
                <tbody>
                  <tr>
                    <th>报名企业</th>
                    <td>西安某某有限公司</td>
                    <th>会员等级</th>
                    <td>理事</td>
                  </tr>
				  <tr>
				    <th>CMA证书号</th>
				    <td>234634563456345</td>
				    <th></th>
				    <td></td>
				  </tr>
                  <tr>
                    <th>联系人</th>
                    <td>张小明</td>
                    <th>联系电话</th>
                    <td>18955688564</td>
                  </tr>
                  <tr>
                    <th>报名时间</th>
                    <td>2020-05-22 12:23</td>
                    <th>取样时间</th>
                    <td>2020-05-28 12:23</td>
                  </tr>


                </tbody>
              </table>
<!--
              <fieldset class="layui-elem-field layui-field-title" style="margin-top: 30px;">
                <legend>能力验证代码</legend>
              </fieldset>


              <table class="layui-table">
                <tbody>
                 <tr>
                    <th style="width:200px; background:#f1f1f1;">能力验证代码</th>
                    <td colspan="3"><input type="text" name="title" lay-verify="title" autocomplete="off" placeholder="" class="layui-input"   > </td>
                  </tr>
                </tbody>
              </table> -->

              <fieldset class="layui-elem-field layui-field-title" style="margin-top: 30px;">
                <legend>取样编号</legend>
              </fieldset>

              <table class="layui-table">
                <thead>
                  <tr>
                    <th>项目</th>
                    <th>取样编号1</th>
                    <th>取样编号2</th>
                    <th>取样编号3</th>
                    <th>补测取样编号1</th>
                    <th>补测取样编号2</th>
                    <th>补测取样编号3</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>蔬菜中毒死蜱</td>
                    <td><input type="text" name="title" lay-verify="title" autocomplete="off" class="layui-input"></td>
                    <td><input type="text" name="title" lay-verify="title" autocomplete="off" class="layui-input"></td>
                    <td><input type="text" name="title" lay-verify="title" autocomplete="off" class="layui-input"></td>
                    <td><input type="text" name="title" lay-verify="title" autocomplete="off" class="layui-input"></td>
                    <td><input type="text" name="title" lay-verify="title" autocomplete="off" class="layui-input"></td>
                    <td><input type="text" name="title" lay-verify="title" autocomplete="off" class="layui-input"></td>
                  </tr>
                  <tr>
                    <td>饮料中环己基氨基磺酸钠(甜蜜素)  </td>
                    <td><input type="text" name="title" lay-verify="title" autocomplete="off" class="layui-input"></td>
                    <td><input type="text" name="title" lay-verify="title" autocomplete="off" class="layui-input"></td>
                    <td><input type="text" name="title" lay-verify="title" autocomplete="off" class="layui-input"></td>
                    <td><input type="text" name="title" lay-verify="title" autocomplete="off" class="layui-input"></td>
                    <td><input type="text" name="title" lay-verify="title" autocomplete="off" class="layui-input"></td>
                    <td><input type="text" name="title" lay-verify="title" autocomplete="off" class="layui-input"></td>
                  </tr>
                  <tr>
                    <td>蔬菜中毒死蜱</td>
                    <td><input type="text" name="title" lay-verify="title" autocomplete="off" class="layui-input"></td>
                    <td><input type="text" name="title" lay-verify="title" autocomplete="off" class="layui-input"></td>
                    <td><input type="text" name="title" lay-verify="title" autocomplete="off" class="layui-input"></td>
                    <td><input type="text" name="title" lay-verify="title" autocomplete="off" class="layui-input"></td>
                    <td><input type="text" name="title" lay-verify="title" autocomplete="off" class="layui-input"></td>
                    <td><input type="text" name="title" lay-verify="title" autocomplete="off" class="layui-input"></td>
                  </tr>
                </tbody>
              </table>
              <div class="layui-form-item">
                  <div class="layui-input-block">
                    <button type="submit" class="layui-btn" lay-submit="" lay-filter="demo1">立即提交</button>
                    <button type="reset" class="layui-btn layui-btn-primary">重置</button>
                  </div>
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
    var SAVE_URL = "{{ url('api/admin/user/ajax_save') }}";// ajax保存记录地址
    var LIST_URL = "{{url('admin/user')}}";//保存成功后跳转到的地址

    var SELECT_COMPANY_URL = "{{url('admin/company/select')}}";// 选择所属企业
</script>
<script src="{{ asset('/js/admin/QualityControl/User_edit.js') }}"  type="text/javascript"></script>
</body>
</html>
