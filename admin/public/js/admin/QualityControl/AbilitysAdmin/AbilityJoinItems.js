
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
var otheraction = {
    sampleResultInfo:function(id, ability_name, retry_no){// 数据上报 id : 报名项目表的id  ability_name:项目名称
        //获得表单各name的值
        var data = get_frm_values(SURE_FRM_IDS);// {} parent.get_frm_values(SURE_FRM_IDS)
        console.log(IFRAME_SAMPLE_RESULT_INFO_URL);
        console.log(data);
        var url_params = get_url_param(data);// parent.get_url_param(data)
        var weburl = IFRAME_SAMPLE_RESULT_INFO_URL + id + '/' + retry_no + '?' + url_params;
        console.log(weburl);
        // go(SHOW_URL + id);
        // location.href='/pms/Supplier/show?supplier_id='+id;
        // var weburl = SHOW_URL + id;
        // var weburl = '/pms/Supplier/show?supplier_id='+id+"&operate_type=1";
        var tishi = "数据上报--" + ability_name;
        layeriframe(weburl,tishi,950,600,IFRAME_MODIFY_CLOSE_OPERATE);
        return false;
    }

};
(function() {
    document.write("");
    document.write("    <!-- 前端模板部分 -->");
    document.write("    <!-- 列表模板部分 开始  <! -- 模板中可以用HTML注释 -- >  或  <%* 这是模板自带注释格式 *%>-->");
    document.write("    <script type=\"text\/template\"  id=\"baidu_template_data_list\">");
    document.write("");
    document.write("        <%for(var i = 0; i<data_list.length;i++){");
    document.write("        var item = data_list[i];");
    //document.write("        var can_modify = false;");
   // document.write("        if( item.issuper==0 ){");
    document.write("        can_modify = true;");
    //document.write("        }");
    document.write("        %>");
    document.write("");
    document.write("        <tr>");
    document.write("            <td>");
    document.write("                <label class=\"pos-rel\">");
    document.write("                    <input  onclick=\"action.seledSingle(this)\" type=\"checkbox\" class=\"ace check_item\" <%if( false &&  !can_modify){%> disabled <%}%>  value=\"<%=item.id%>\"\/>");
    document.write("                  <span class=\"lbl\"><\/span>");
    document.write("                <\/label>");
    document.write("            <\/td>");
    // document.write("            <td><%=item.id%><\/td>");
    document.write("            <td><%=item.ability_code%><\/td>");
    document.write("            <td><%=item.company_name%><\/td>");
    document.write("            <td><%=item.contacts%><\/td>");
    document.write("            <td><%=item.mobile%><hr/><%=item.tel%><\/td>");
    document.write("            <td><%=item.join_time%><\/td>");
    document.write("            <td><%=item.is_sample_text%><hr/><%=item.sample_time%><\/td>");
    document.write("            <td><%=item.submit_status_text%><hr/><%=item.submit_time%><\/td>");
    document.write("            <td><%=item.judge_status_text%><hr/><%=item.judge_time%><\/td>");
    document.write("            <td><%=item.status_text%>(<%=item.retry_no_text%>)<\/td>");
    document.write("            <td><%=item.result_status_text%><\/td>");
    // document.write("            <td><%=item.created_at%><\/td>");
    // document.write("            <td><%=item.updated_at%><\/td>");
    document.write("            <td>");
    document.write("                <%if( item.retry_no > 0 || (item.retry_no == 0 && item.status >= 4)){%>");
    document.write("                <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-success\"  onclick=\"otheraction.sampleResultInfo(<%=item.id%>,'<%=item.ability_name%>-初测查看', 0)\">");
    document.write("                    <i class=\"ace-icon fa fa-check bigger-60\"> 查看数据[初测]<\/i>");
    document.write("                <\/a>");
    document.write("                <%}%>");
    document.write("                <%if( item.retry_no > 1 || (item.retry_no == 1 && item.status >= 4)){%>");
    document.write("                <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-success\"  onclick=\"otheraction.sampleResultInfo(<%=item.id%>,'<%=item.ability_name%>-补测查看', 1)\">");
    document.write("                    <i class=\"ace-icon fa fa-check bigger-60\"> 查看数据[补测]<\/i>");
    document.write("                <\/a>");
    document.write("                <%}%>");
    // document.write("                <%if( false){%>");
    // document.write("                <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-success\"  onclick=\"action.show(<%=item.id%>)\">");
    // document.write("                    <i class=\"ace-icon fa fa-check bigger-60\"> 查看<\/i>");
    // document.write("                <\/a>");
    // document.write("                <%}%>");
    // document.write("                <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-info\" onclick=\"action.iframeModify(<%=item.id%>)\">");
    // document.write("                    <i class=\"ace-icon fa fa-pencil bigger-60\"> 编辑<\/i>");
    // document.write("                <\/a>");
    // document.write("                <%if( can_modify){%>");
    // document.write("                <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-info\" onclick=\"action.del(<%=item.id%>)\">");
    // document.write("                    <i class=\"ace-icon fa fa-trash-o bigger-60\"> 删除<\/i>");
    // document.write("                <\/a>");
    // document.write("                <%}%>");
    document.write("");
    document.write("            <\/td>");
    document.write("        <\/tr>");
    document.write("    <%}%>");
    document.write("<\/script>");
    document.write("<!-- 列表模板部分 结束-->");
    document.write("<!-- 前端模板结束 -->");
}).call();