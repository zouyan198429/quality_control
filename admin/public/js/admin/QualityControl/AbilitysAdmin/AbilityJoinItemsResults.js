
var SUBMIT_FORM = true;//防止多次点击提交

$(function(){

    $('.search_frm').trigger("click");// 触发搜索事件
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
    reset_list(is_read_page, false, reset_total, do_num);
    // initList();
}

//业务逻辑部分
var otheraction = {
    getSample : function(id){// 弹窗取样
        //获得表单各name的值
        var data = get_frm_values(SURE_FRM_IDS);// {} parent.get_frm_values(SURE_FRM_IDS)
        console.log(IFRAME_SAMPLE_URL);
        console.log(data);
        var url_params = get_url_param(data);// parent.get_url_param(data)
        var weburl = IFRAME_SAMPLE_URL + id + '?' + url_params;
        console.log(weburl);
        // go(SHOW_URL + id);
        // location.href='/pms/Supplier/show?supplier_id='+id;
        // var weburl = SHOW_URL + id;
        // var weburl = '/pms/Supplier/show?supplier_id='+id+"&operate_type=1";
        var tishi = "取样";
        layeriframe(weburl,tishi,950,510,IFRAME_MODIFY_CLOSE_OPERATE);
        return false;
    },
    sampleResultInfo:function(item_id, ability_name, retry_no){// 数据上报 item_id : 报名项目表的id  ability_name:项目名称
        //获得表单各name的值
        var data = get_frm_values(SURE_FRM_IDS);// {} parent.get_frm_values(SURE_FRM_IDS)
        console.log(IFRAME_SAMPLE_RESULT_INFO_URL);
        console.log(data);
        var url_params = get_url_param(data);// parent.get_url_param(data)
        var weburl = IFRAME_SAMPLE_RESULT_INFO_URL + item_id + '/' + retry_no ;// + '?' + url_params;
        console.log(weburl);
        // go(SHOW_URL + item_id);
        // location.href='/pms/Supplier/show?supplier_id='+item_id;
        // var weburl = SHOW_URL + id;
        // var weburl = '/pms/Supplier/show?supplier_id='+item_id+"&operate_type=1";
        var tishi = "数据上报--" + ability_name;
        layeriframe(weburl,tishi,950,510,IFRAME_MODIFY_CLOSE_OPERATE);
        return false;
    },
    selectCompany: function(obj){// 选择商家
        var recordObj = $(obj);
        //获得表单各name的值
        var weburl = SELECT_COMPANY_URL;
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
    iframeJudge : function(id){// 弹窗--判定
        //获得表单各name的值
        var data = get_frm_values(SURE_FRM_IDS);// {} parent.get_frm_values(SURE_FRM_IDS)
        console.log(IFRAME_MODIFY_URL);
        console.log(data);
        var url_params = get_url_param(data);// parent.get_url_param(data)
        var weburl = IFRAME_MODIFY_URL + id + '?' + url_params;
        console.log(weburl);
        // go(SHOW_URL + id);
        // location.href='/pms/Supplier/show?supplier_id='+id;
        // var weburl = SHOW_URL + id;
        // var weburl = '/pms/Supplier/show?supplier_id='+id+"&operate_type=1";
        var tishi = IFRAME_MODIFY_URL_TITLE;//"添加/修改供应商";
        var operateText = "结果判定";// "添加";
        // if(id > 0){
        //     operateText = "修改";
        // }
        tishi = operateText + tishi;
        layeriframe(weburl,tishi,950,510,IFRAME_MODIFY_CLOSE_OPERATE);
        return false;
    },
    dissatisfied : function(id){// 已领样，未上传数据的--可以手动直接判断为不满意
        dissatisfied(id);
        return false;
    }
};
// 已领样，未上传数据的--可以手动直接判断为不满意
function dissatisfied(id) {
    var index_query = layer.confirm('确定不满意【已传】当前选择记录？', {
        btn: ['确定','取消'] //按钮
    }, function(){
        var operateText = '不满意【已传】';
        other_operate_ajax('dissatisfied', id, operateText, {});
        layer.close(index_query);
    }, function(){
    });
}

//操作
// params 其它参数对象  {}
function other_operate_ajax(operate_type, id, operate_txt, params){
    params = params || {};
    if(operate_type == '' || id == ''){
        err_alert('请选择需要操作的数据');
        return false;
    }
    operate_txt = operate_txt || "";
    var data = params;// {};
    var ajax_url = "";
    var reset_total = true;// 是否重新从数据库获取总页数 true:重新获取,false不重新获取  ---ok
    switch(operate_type)
    {
        case 'dissatisfied':// 已领样，未上传数据的--可以手动直接判断为不满意
            // operate_txt = "开启";
            // data = {'id':id, 'activity_id': CURRENT_ACTIVITY_ID};
            // 合并对象
            objAppendProps(data, {'id':id}, true);
            ajax_url = SAVE_DISSATISFIED_URL;// /pms/Supplier/ajax_del?operate_type=1
            reset_total = true;
            break;
        // case 'batch_open'://批量开启
        //     // operate_txt = "批量开启";
        //     // data = {'id':id, 'activity_id': CURRENT_ACTIVITY_ID};
        //     // 合并对象
        //     objAppendProps(data, {'id':id}, true);
        //     reset_total = false;
        //     ajax_url = OPEN_OPERATE_URL;// "/pms/Supplier/ajax_del?operate_type=2";
        //     break;
        default:
            break;
    }
    console.log('ajax_url:',ajax_url);
    console.log('data:',data);
    var layer_index = layer.load();//layer.msg('加载中', {icon: 16});
    $.ajax({
        'type' : 'POST',
        'url' : ajax_url,//'/pms/Supplier/ajax_del',
        'headers':get_ajax_headers({}, ADMIN_AJAX_TYPE_NUM),
        'data' : data,
        'dataType' : 'json',
        'success' : function(ret){
            console.log('ret:',ret);
            if(!ret.apistatus){//失败
                //alert('失败');
                // countdown_alert(ret.errorMsg,0,5);
                layer_alert(ret.errorMsg,3,0);
            }else{//成功
                var msg = ret.errorMsg;
                if(msg === ""){
                    msg = operate_txt+"成功";
                }
                // countdown_alert(msg,1,5);
                layer_alert(msg,1,0);
                // reset_list(true, true);
                console.log(LIST_FUNCTION_NAME);
                eval( LIST_FUNCTION_NAME + '(' + true +', ' + true +', ' + reset_total + ', 2)');
            }
            layer.close(layer_index);//手动关闭
        }
    });
}

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
function getSelectedCompanyIds(){
    var company_ids = [];
    var company_id = $('input[name=company_id]').val();
    company_ids.push(company_id);
    console.log('company_ids' , company_ids);
    return company_ids;
}

// 取消
// company_id 企业id
function removeCompany(company_id){
    var seled_company_id = $('input[name=company_id]').val();
    if(company_id == seled_company_id){
        $('input[name=company_id]').val('');
        $('.company_name').html('');
        $('.company_id_close').hide();
    }
}

// 增加
// company_id 企业id, 多个用,号分隔
function addCompany(company_id, company_name){
    $('input[name=company_id]').val(company_id);
    $('.company_name').html(company_name);
    $('.company_id_close').show();
}

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
    document.write("            <td><%=item.status_text%>(<%=item.retry_no_text%>)<hr/><%=item.result_status_text%><\/td>");
    // document.write("            <td><%=item.created_at%><\/td>");
    // document.write("            <td><%=item.updated_at%><\/td>");
    document.write("            <td>");
    document.write("                <%if(item.retry_no == 0 && item.submit_status == 2){%>");
    document.write("                <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-success\"  onclick=\"otheraction.sampleResultInfo(<%=item.ability_join_item_id%>,'<%=item.ability_name%>-初测查看', 0)\">");
    document.write("                    <i class=\"ace-icon  fa fa-eye  bigger-60\"> 查看数据[初测]<\/i>");
    document.write("                <\/a>");
    document.write("                <%}%>");
    document.write("                <%if( item.retry_no == 1 && item.submit_status == 2){%>");
    document.write("                <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-success\"  onclick=\"otheraction.sampleResultInfo(<%=item.ability_join_item_id%>,'<%=item.ability_name%>-补测查看', 1)\">");
    document.write("                    <i class=\"ace-icon  fa fa-eye  bigger-60\"> 查看数据[补测]<\/i>");
    document.write("                <\/a>");
    document.write("                <%}%>");
    // document.write("                <%if(item.status == 1  &&  item.is_sample == 1 ){%>");
    document.write("                <%if( (item.status == 1 || item.status == 2) ){%>");
    document.write("                <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-success\"  onclick=\"otheraction.getSample(<%=item.id%>)\">");
    document.write("                    <i class=\"ace-icon fa fa-eyedropper bigger-60\"> 取样<\/i>");
    document.write("                <\/a>");
    document.write("                <%}%>");
    document.write("                <%if( item.status == 2 && item.result_status == 1 && item.submit_status == 1 && item.is_sample == 2){%>");
    document.write("                <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-info\" onclick=\"otheraction.dissatisfied(<%=item.id%>)\">");
    document.write("                    <i class=\"ace-icon fa fa-pencil bigger-60\"> 不满意[未传]<\/i>");
    document.write("                <\/a>");
    document.write("                <%}%>");
    // document.write("                <%if( false){%>");
    // document.write("                <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-success\"  onclick=\"action.show(<%=item.id%>)\">");
    // document.write("                    <i class=\"ace-icon fa fa-check bigger-60\"> 查看<\/i>");
    // document.write("                <\/a>");
    // document.write("                <%}%>");
    document.write("                <%if( item.status == 4 && item.result_status == 1){%>");
    document.write("                <a href=\"javascript:void(0);\" class=\"btn btn-mini btn-info\" onclick=\"otheraction.iframeJudge(<%=item.id%>)\">");
    document.write("                    <i class=\"ace-icon fa fa-pencil bigger-60\"> 判定<\/i>");
    document.write("                <\/a>");
    document.write("                <%}%>");
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
