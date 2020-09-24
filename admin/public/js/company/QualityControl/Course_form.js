var SUBMIT_FORM = true;//防止多次点击提交

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

function ajax_form(){
    if (!SUBMIT_FORM) return false;//false，则返回

    // // 验证信息
    var course_id = $('input[name=course_id]').val();
    if(!judge_validate(4,'课程id',course_id,true,'digit')){
        return false;
    }
    var contacts = $('input[name=contacts]').val();
    if(!judge_validate(4,'联络人', contacts, true, 'length', 1,10)){
        return false;
    }
    var tel = $('input[name=tel]').val();
    if(!judge_validate(4,'联络人电话', tel, true, 'mobile')){
        return false;
    }
    var ids = get_list_checked(DYNAMIC_TABLE_BODY,1,1);
    if(!judge_validate(4,'学员', ids, true)){
        return false;
    }

    // 验证通过
    SUBMIT_FORM = false;//标记为已经提交过

    var layer_index = layer.load();
    $.ajax({
        'type' : 'POST',
        'url' : SAVE_URL,
        'headers':get_ajax_headers({}, ADMIN_AJAX_TYPE_NUM),
        'data' : {
            'ids':ids,
            'tel':tel,
            'course_id':course_id,
            'contacts':contacts
        },
        'dataType' : 'json',
        'success' : function(ret){
            console.log(ret);
            if(!ret.apistatus){//失败
                SUBMIT_FORM = true;//标记为未提交过
                //alert('失败');
                err_alert(ret.errorMsg);
            }else{//成功
                // go(LIST_URL);
                layer.msg('操作成功！', {
                    icon: 1,
                    shade: 0.3,
                    time: 3000 //2秒关闭（如果不配置，默认是3秒）
                }, function(){
                    var reset_total = true; // 是否重新从数据库获取总页数 true:重新获取,false不重新获取
                    if(id > 0) reset_total = false;
                    parent_reset_list_iframe_close(reset_total);// 刷新并关闭
                    //do something
                });
            }
            layer.close(layer_index)//手动关闭
        }
    });
    return false;
}

$(function(){
    //提交
    $(document).on("click","#submitBtn",function(){
        ajax_form();
        return false;
    });
});
function selectAll(obj){
    var checkAllObj =  $(obj);
    /*
    checkAllObj.closest('#' + DYNAMIC_TABLE).find('input:checkbox').each(function(){
        if(!$(this).prop('disabled')){
            $(this).prop('checked', checkAllObj.prop('checked'));
        }
    });
    */
    checkAllObj.closest('#' + DYNAMIC_TABLE).find('.check_item').each(function(){
        if(!$(this).prop('disabled')){
            $(this).prop('checked', checkAllObj.prop('checked'));
        }
    });
};
function selectSingle(obj) {// 单选点击
    var checkObj = $(obj);
    var allChecked = true;
    /*
     checkObj.closest('#' + DYNAMIC_TABLE).find('input:checkbox').each(function () {
        if (!$(this).prop('disabled') && $(this).val() != '' &&  !$(this).prop('checked') ) {
            // $(this).prop('checked', checkAllObj.prop('checked'));
            allChecked = false;
            return false;
        }
    });
    */
    checkObj.closest('#' + DYNAMIC_TABLE).find('.check_item').each(function () {
        if (!$(this).prop('disabled') && $(this).val() != '' &&  !$(this).prop('checked') ) {
            // $(this).prop('checked', checkAllObj.prop('checked'));
            allChecked = false;
            return false;
        }
    });
    // 全选复选操选中/取消选中
    /*
    checkObj.closest('#' + DYNAMIC_TABLE).find('input:checkbox').each(function () {
        if (!$(this).prop('disabled') && $(this).val() == ''  ) {
            $(this).prop('checked', allChecked);
            return false;
        }
    });
    */
    checkObj.closest('#' + DYNAMIC_TABLE).find('.check_all').each(function () {
        $(this).prop('checked', allChecked);
    });

};
(function() {
    document.write("");
    document.write("    <!-- 前端模板部分 -->");
    document.write("    <!-- 列表模板部分 开始  <! -- 模板中可以用HTML注释 -- >  或  <%* 这是模板自带注释格式 *%>-->");
    document.write("    <script type=\"text\/template\"  id=\"baidu_template_data_list\">");
    document.write("");
    document.write("        <%for(var i = 0; i<data_list.length;i++){");
    document.write("        var item = data_list[i];");
    document.write("        can_modify = true;");
    //document.write("        }");
    document.write("        %>");
    document.write("");
    document.write("        <tr>");
    document.write("            <td>");
    document.write("                <label class=\"pos-rel\">");
    document.write("                    <input onclick=\"selectSingle(this)\" type=\"checkbox\" class=\"ace check_item\" <%if( false &&  !can_modify){%> disabled <%}%>  value=\"<%=item.id%>\"\/>");
    document.write("                  <span class=\"lbl\"><\/span>");
    document.write("                <\/label>");
    document.write("            <\/td>");
    document.write("            <td><%=item.real_name%><\/td>");
    document.write("            <td><%=item.sex_text%><\/td>");
    document.write("            <td><%=item.mobile%><\/td>");
    document.write("            <td><%=item.id_number%><\/td>");
    document.write("        <\/tr>");
    document.write("    <%}%>");
    document.write("<\/script>");
    document.write("<!-- 列表模板部分 结束-->");
    document.write("<!-- 前端模板结束 -->");
}).call();
