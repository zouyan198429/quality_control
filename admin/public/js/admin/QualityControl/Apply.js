
var SUBMIT_FORM = true;//防止多次点击提交

$(function(){

    // $('.search_frm').trigger("click");// 触发搜索事件
    // reset_list_self(false, false, true, 2);
    popSelectInit();// 初始化选择弹窗

    // window.location.href 返回 web 主机的域名，如：http://127.0.0.1:8080/testdemo/test.html?id=1&name=test
    autoRefeshList(window.location.href, IFRAME_TAG_KEY, IFRAME_TAG_TIMEOUT);// 根据设置，自动刷新列表数据【每隔一定时间执行一次】
});

//重载列表
//is_read_page 是否读取当前页,否则为第一页 true:读取,false默认第一页
// ajax_async ajax 同步/导步执行 //false:同步;true:异步  需要列表刷新同步时，使用自定义方法reset_list_self；异步时没有必要自定义
// reset_total 是否重新从数据库获取总页数 true:重新获取,false不重新获取  ---ok
// do_num 调用时: 1 初始化页面时[默认];2 初始化页面后的调用
function reset_list_self(is_read_page, ajax_async, reset_total, do_num){
    console.log('is_read_page', typeof(is_read_page));
    console.log('ajax_async', typeof(ajax_async));
    var layer_index = layer.load();
    reset_list(is_read_page, false, reset_total, do_num);

    // 初始化列表文件显示功能
    var uploadAttrObj = {
        down_url:DOWN_FILE_URL,
        del_url: DEL_FILE_URL,
        del_fun_pre:'',
        files_type: 1,
        icon : 'file-o',
        operate_auth:(1 | 2)
    };
    var resourceListObj = $('#data_list').find('tr');
    initFileShow(uploadAttrObj, resourceListObj, 'resource_show', 'baidu_template_upload_file_show', 'baidu_template_upload_pic', 'resource_id[]');

    // initList();
    initPic();
    layer.close(layer_index);//手动关闭
}

window.onload = function() {
    $('.search_frm').trigger("click");// 触发搜索事件
    // reset_list_self(false, false, true, 2);
//     initPic();
};
function initPic(){
    baguetteBox.run('.baguetteBoxOne');
    // baguetteBox.run('.baguetteBoxTwo');
}

//业务逻辑部分
var otheraction = {
    selectStaff: function(obj){// 选择商家
        var recordObj = $(obj);
        //获得表单各name的值
        var weburl = SELECT_STAFF_URL;
        console.log(weburl);
        // go(SHOW_URL + id);
        // location.href='/pms/Supplier/show?supplier_id='+id;
        // var weburl = SHOW_URL + id;
        // var weburl = '/pms/Supplier/show?supplier_id='+id+"&operate_type=1";
        var tishi = '选择所属企业';//"查看供应商";
        console.log('weburl', weburl);
        layeriframe(weburl,tishi,700,450,0);
        return false;
    },
    down_file:function(resource_url, save_file_name){//下载网页打印机驱动
        // var layer_index = layer.load();//layer.msg('加载中', {icon: 16});
        // //layer_alert("已打印"+print_nums+"打印第"+begin_page+"页-第"+end_page+"页;每次打"+per_page_num+"页",3);
        // var url = DOWN_FILE_URL + '?resource_url=' + resource_url + '&save_file_name=' + save_file_name;
        // console.log('下载文件：', url);
        // // PrintOneURL(url);
        // go(url);
        // layer.close(layer_index);//手动关闭
        commonaction.down_file(DOWN_FILE_URL, resource_url, save_file_name);
    }
};

// 初始化，来决定*是显示还是隐藏
function popSelectInit(){

    $('.select_close').each(function(){
        let closeObj = $(this);
        let idObj = closeObj.siblings(".select_id");
        if(idObj.length > 0 && idObj.val() != '' && idObj.val() != '0'  ){
            closeObj.show();
        }else{
            closeObj.hide();
        }
    });
}

// 清空
function clearSelect(Obj){
    let closeObj = $(Obj);
    console.log('closeObj=' , closeObj);

    var index_query = layer.confirm('确定移除？', {
        btn: ['确定','取消'] //按钮
    }, function(){
        // 清空id
        let idObj = closeObj.siblings(".select_id");
        if(idObj.length > 0 ){
            idObj.val('');
        }
        // 清空名称文字
        let nameObj = closeObj.siblings(".select_name");
        if(nameObj.length > 0 ){
            nameObj.html('');
        }
        closeObj.hide();
        layer.close(index_query);
    }, function(){
    });
}

// 获得选中的企业id 数组
function getSelectedStaffIds(){
    var staff_ids = [];
    var staff_id = $('input[name=staff_id]').val();
    staff_ids.push(staff_id);
    console.log('staff_ids' , staff_ids);
    return staff_ids;
}

// 取消
// staff_id 企业id
function removeStaff(staff_id){
    var seled_staff_id = $('input[name=staff_id]').val();
    if(staff_id == seled_staff_id){
        $('input[name=staff_id]').val('');
        $('.staff_name').html('');
        $('.staff_id_close').hide();
    }
}

// 增加
// staff_id 企业id, 多个用,号分隔
function addStaff(staff_id, staff_name){
    $('input[name=staff_id]').val(staff_id);
    $('.staff_name').html(staff_name);
    $('.staff_id_close').show();
    console.log('--show--', $('.staff_id_close').length);
}

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
    // document.write("            <td><%=item.id%><\/td>");
    document.write("            <td><%=item.user_staff_name%><\/td>");
    document.write("            <td><%=item.app_name%><\/td>");
    document.write("            <td>");
    document.write("               <span class=\"resource_list\"  style=\"display: none;\"><%=JSON.stringify(item.resource_list)%></span>");
    document.write("               <span  class=\"resource_show\"></span>");
    document.write("            <\/td>");
    // document.write("           <td>");
    // document.write("            <%for(var j = 0; j<resource_list.length;j++){");
    // document.write("                var jitem = resource_list[j];");
    // document.write("                 %>");
    // document.write("               <p><%=jitem.resource_name%>");
    // document.write("               <a href=\"javascript:void(0);\"  class=\"btn btn-mini btn-success\"    onclick=\"commonaction.browse_file('<%=jitem.resource_url_format%>','<%=jitem.resource_name%>')\">");
    // document.write("                    <i class=\"ace-icon fa fa-eye bigger-60\"> 查看<\/i>");
    // // document.write("                <img  src=\"<%=jitem.resource_url%>\"  style=\"width:100px;\">");
    // document.write("              </a>");
    // document.write("                <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-success\"  onclick=\"otheraction.down_file('<%=jitem.resource_url_old%>','<%=item.company_id%>-<%=item.user_company_name%>-<%=jitem.resource_name%>')\">");
    // document.write("                    <i class=\"ace-icon fa fa-cloud-download bigger-60\"> 下载<\/i>");
    // document.write("                <\/a></p>");
    // document.write("            <%}%>");
    // document.write("           <\/td>");
    document.write("            <td><%=item.app_id%><hr/><%=item.app_secret%><\/td>");
    document.write("            <td><%=item.remarks%><\/td>");
    document.write("            <td><%=item.open_status_text%><\/td>");
    document.write("            <td><%=item.created_at%><\/td>");
    document.write("            <td><%=item.updated_at%><\/td>");
    // document.write("            <td><\/td>");
    // document.write("            <td><%=item.sort_num%><\/td>");
    document.write("            <td>");
    document.write("                <%if( false){%>");
    document.write("                <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-success\"  onclick=\"action.show(<%=item.id%>)\">");
    document.write("                    <i class=\"ace-icon fa fa-check bigger-60\"> 查看<\/i>");
    document.write("                <\/a>");
    document.write("                <%}%>");
    document.write("                <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-info\" onclick=\"action.iframeModify(<%=item.id%>)\">");
    document.write("                    <i class=\"ace-icon fa fa-pencil bigger-60\"> 编辑<\/i>");
    document.write("                <\/a>");
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