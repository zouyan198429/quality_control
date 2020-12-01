
var SUBMIT_FORM = true;//防止多次点击提交

//获取当前窗口索引
var PARENT_LAYER_INDEX = parent.layer.getFrameIndex(window.name);
//让层自适应iframe
////parent.layer.iframeAuto(PARENT_LAYER_INDEX);
// parent.layer.full(PARENT_LAYER_INDEX);// 用这个
//关闭iframe
$(document).on("click",".closeIframe",function(){
    iframeclose(PARENT_LAYER_INDEX);
});
//刷新父窗口列表
// reset_total 是否重新从数据库获取总页数 true:重新获取,false不重新获取
function parent_only_reset_list(reset_total){
    // window.parent.reset_list(true, true, reset_total, 2);//刷新父窗口列表
    let list_fun_name = window.parent.LIST_FUNCTION_NAME || 'reset_list';
    eval( 'window.parent.' + list_fun_name + '(' + true +', ' + true +', ' + reset_total +', 2)');
}
//关闭弹窗,并刷新父窗口列表
// reset_total 是否重新从数据库获取总页数 true:重新获取,false不重新获取
function parent_reset_list_iframe_close(reset_total){
    // window.parent.reset_list(true, true, reset_total, 2);//刷新父窗口列表
    let list_fun_name = window.parent.LIST_FUNCTION_NAME || 'reset_list';
    eval( 'window.parent.' + list_fun_name + '(' + true +', ' + true +', ' + reset_total +', 2)');
    parent.layer.close(PARENT_LAYER_INDEX);
}
//关闭弹窗
function parent_reset_list(){
    parent.layer.close(PARENT_LAYER_INDEX);
}

window.onload = function() {
    var layer_index = layer.load();
    // initPic();
    layer.close(layer_index);//手动关闭
};
function initPic(){
    baguetteBox.run('.baguetteBoxOne');
    // baguetteBox.run('.baguetteBoxTwo');
}

$(function(){
    //提交
    $(document).on("click","#submitBtn",function(){
        //var index_query = layer.confirm('您确定提交保存吗？', {
        //    btn: ['确定','取消'] //按钮
        //}, function(){
        ajax_form();
        //    layer.close(index_query);
        // }, function(){
        //});
        return false;
    });

});

//业务逻辑部分
var otheraction = {

    seledAll:function(obj){
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
        return false;
    },
    seledSingle:function(obj) {// 单选点击
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
        return false;
    }
};
//ajax提交表单
function ajax_form(){
    if (!SUBMIT_FORM) return false;//false，则返回

    // 验证信息
    var id = $('input[name=id]').val();
    if(!judge_validate(4,'记录id',id,true,'digit','','')){
        return false;
    }
    // 收款开通类型
    if(!judge_list_checked('data_list',1)) {//没有选中的
        layer_alert('请选择学员！',3,0);
        return false;
    }

    var contacts = $('input[name=contacts]').val();
    if(!judge_validate(4,'联络人员',contacts,true,'length',1,50)){
        return false;
    }

    var tel = $('input[name=tel]').val();
    if(!judge_validate(4,'联络人电话',tel,true,'length',5,30)){
        return false;
    }

    ajax_save(id);
}


// 验证通过后，ajax保存
function ajax_save(id){

    // 验证通过
    SUBMIT_FORM = false;//标记为已经提交过
    var data = $("#addForm").serialize();
    console.log(SAVE_URL);
    console.log(data);
    var layer_index = layer.load();
    $.ajax({
        'type' : 'POST',
        'url' : SAVE_URL,
        'headers':get_ajax_headers({}, ADMIN_AJAX_TYPE_NUM),
        'data' : data,
        'dataType' : 'json',
        'success' : function(ret){
            console.log(ret);
            if(!ret.apistatus){//失败
                SUBMIT_FORM = true;//标记为未提交过
                //alert('失败');
                err_alert(ret.errorMsg);
            }else{//成功
                // go(LIST_URL);

                // countdown_alert("操作成功!",1,5);
                // parent_only_reset_list(false);
                // wait_close_popus(2,PARENT_LAYER_INDEX);
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
                // var supplier_id = ret.result['supplier_id'];
                //if(SUPPLIER_ID_VAL <= 0 && judge_integerpositive(supplier_id)){
                //    SUPPLIER_ID_VAL = supplier_id;
                //    $('input[name="supplier_id"]').val(supplier_id);
                //}
                // save_success();
            }
            layer.close(layer_index);//手动关闭
        }
    });
    return false;
}
