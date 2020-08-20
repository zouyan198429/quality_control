
$(function(){

    $('.search_frm').trigger("click");// 触发搜索事件
    // reset_list_self(false, false, true, 2);
});

//重载列表
//is_read_page 是否读取当前页,否则为第一页 true:读取,false默认第一页
// ajax_async ajax 同步/导步执行 //false:同步;true:异步  需要列表刷新同步时，使用自定义方法reset_list_self；异步时没有必要自定义
// reset_total 是否重新从数据库获取总页数 true:重新获取,false不重新获取  ---ok
// do_num 调用时: 1 初始化页面时[默认];2 初始化页面后的调用
function reset_list_self(is_read_page, ajax_async, reset_total, do_num){
    console.log('is_read_page', typeof(is_read_page));
    console.log('ajax_async', typeof(ajax_async));
    reset_list(is_read_page, false, reset_total, do_num);
    // initList();
}

//业务逻辑部分
// var otheraction = {
//     selectCompany: function(obj){// 选择商家
//         var recordObj = $(obj);
//         //获得表单各name的值
//         var weburl = SELECT_COMPANY_URL;
//         console.log(weburl);
//         // go(SHOW_URL + id);
//         // location.href='/pms/Supplier/show?supplier_id='+id;
//         // var weburl = SHOW_URL + id;
//         // var weburl = '/pms/Supplier/show?supplier_id='+id+"&operate_type=1";
//         var tishi = '选择所属企业';//"查看供应商";
//         console.log('weburl', weburl);
//         layeriframe(weburl,tishi,700,450,0);
//         return false;
//     }
// };
// 获得选中的企业id 数组
// function getSelectedCompanyIds(){
//     var company_ids = [];
//     var company_id = $('input[name=company_id]').val();
//     company_ids.push(company_id);
//     console.log('company_ids' , company_ids);
//     return company_ids;
// }
//
// // 取消
// // company_id 企业id
// function removeCompany(company_id){
//     var seled_company_id = $('input[name=company_id]').val();
//     if(company_id == seled_company_id){
//         $('input[name=company_id]').val('');
//         $('.company_name').html('');
//     }
// }
//
// // 增加
// // company_id 企业id, 多个用,号分隔
// function addCompany(company_id, company_name){
//     $('input[name=company_id]').val(company_id);
//     $('.company_name').html(company_name);
//     $('.search_frm').trigger("click");// 触发搜索事件
// }

(function() {
    document.write("");
    document.write("    <!-- 前端模板部分 -->");
    document.write("    <!-- 列表模板部分 开始  <! -- 模板中可以用HTML注释 -- >  或  <%* 这是模板自带注释格式 *%>-->");
    document.write("    <script type=\"text\/template\"  id=\"baidu_template_data_list\">");
    document.write("");
    document.write("        <%for(var i = 0; i<data_list.length;i++){");
    document.write("        var item = data_list[i];");
    document.write("        var resource_list = item.resource_list;");
    //document.write("        var can_modify = false;");
   // document.write("        if( item.issuper==0 ){");
    document.write("        can_modify = true;");
    //document.write("        }");
    document.write("        %>");
    document.write("");
    document.write("        <tr>");
    // document.write("            <td>");
    // document.write("                <label class=\"pos-rel\">");
    // document.write("                    <input  onclick=\"action.seledSingle(this)\" type=\"checkbox\" class=\"ace check_item\" <%if( false &&  !can_modify){%> disabled <%}%>  value=\"<%=item.id%>\"\/>");
    // document.write("                  <span class=\"lbl\"><\/span>");
    // document.write("                <\/label>");
    // document.write("            <\/td>");
    document.write("            <td><%=item.id%><\/td>");
    // document.write("            <td><%=item.client_id%><\/td>");
    // document.write("            <td><%=item.user_company_name%><\/td>");
    document.write("           <td>");
    document.write("            <%for(var j = 0; j<resource_list.length;j++){");
    document.write("                var jitem = resource_list[j];");
    document.write("                 %>");
    document.write("               <a href=\"<%=jitem.resource_url_format%>\" target='_blank'>");
        document.write("                    <%=jitem.resource_name%>--查看");
    // document.write("                <img  src=\"<%=jitem.resource_url%>\"  style=\"width:100px;\">");
    document.write("              </a>");
    document.write("            <%}%>");
    document.write("           <\/td>");
    document.write("            <td><%=item.created_at%><\/td>");
    document.write("            <td>");
    document.write("                <%if( false){%>");
    document.write("                <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-success\"  onclick=\"action.show(<%=item.id%>)\">");
    document.write("                    <i class=\"ace-icon  fa fa-eye  bigger-60\"> 查看<\/i>");
    document.write("                <\/a>");
    document.write("                <%}%>");
    document.write("                <%if( can_modify){%>");
    document.write("                <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-info\" onclick=\"action.del(<%=item.id%>)\">");
    document.write("                    <i class=\"ace-icon fa fa-trash-o bigger-60\"> 删除<\/i>");
    document.write("                <\/a>");
    document.write("                <%}%>");
    document.write("");
    document.write("            <\/td>");
    document.write("        <\/tr>");
    document.write("    <%}%>");
    document.write("<\/script>");
    document.write("<!-- 列表模板部分 结束-->");
    document.write("<!-- 前端模板结束 -->");
}).call();
