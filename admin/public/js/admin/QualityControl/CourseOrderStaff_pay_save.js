
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
    // 修改实收金额
    $(document).on("change",'input[name=payment_amount]',function(){
        var payment_amount = parseFloat($(this).val());// 收款金额
        var pay_method = $('input[name=pay_method]').val();
        var total_price = parseFloat($('input[name=total_price]').val());// 总金额
        if(pay_method != 1){
            err_alert('非现金收款，不可改变实收金额');
            $(this).val(total_price);
            $('input[name=change_amount]').val(0);
            return false;
        }
        if(payment_amount < total_price){
            err_alert('实收金额，不能小于总金额【¥' + total_price + '】');
            $(this).val(total_price);
            $('input[name=change_amount]').val(0);
            return false;
        }
        var change_amount = payment_amount - total_price;
        $('input[name=change_amount]').val(number_format(change_amount, 2));
        $('.change_amount').html(price_format(change_amount));
    });
    $('input[name=payment_amount]').select();// 实收金额自动选中
    $('input[name=payment_amount]').focus();// 实收金额自动获得焦点

});

window.onload = function() {
    // $('.search_frm').trigger("click");// 触发搜索事件
    // reset_list_self(false, false, true, 2);
//     initPic();

    // 按钮改为显示收款码
    var pay_method = $('input[name=pay_method]').val();
    if(pay_method == 2 || pay_method == 4){
        $('#submitBtn').html('显示收款码');
    }
    var layer_index = layer.load();

    // 初始化列表文件显示功能
    var uploadAttrObj = {
        down_url:DOWN_FILE_URL,
        del_url: DEL_FILE_URL,
        del_fun_pre:'',
        files_type: 0,
        icon : 'file-o',
        operate_auth:(1 | 2)
    };
    var resourceListObj = $('.baguetteBoxOne');// $('#data_list').find('tr');
    initFileShow(uploadAttrObj, resourceListObj, 'resource_show', 'baidu_template_upload_file_show', 'baidu_template_upload_pic', 'resource_id[]');

    // initList();
    initPic();
    layer.close(layer_index);//手动关闭
};
function initPic(){
    baguetteBox.run('.baguetteBoxOne');
    // baguetteBox.run('.baguetteBoxTwo');
}
//业务逻辑部分
var otheraction = {

};

//ajax提交表单
function ajax_form(){
    if (!SUBMIT_FORM) return false;//false，则返回

    // 验证信息
    var id = $('input[name=id]').val();
    // if(!judge_validate(4,'记录id',id,true,'digit','','')){
    //     return false;
    // }
    if(!judge_validate(4,'收款学员',id,true,'length',1,3000)){
        return false;
    }
    var total_price = parseFloat($('input[name=total_price]').val());// 总金额
    var payment_amount = parseFloat($('input[name=payment_amount]').val());// 实收金额
    if(payment_amount < total_price){
        err_alert('实收金额，不能小于总金额【¥' + total_price + '】');
        return false;
    }

    var index_query = layer.confirm('您确定操作吗？', {
       btn: ['确定','取消'] //按钮
    }, function(){
        layer.close(index_query);
        ajax_save(id);
    }, function(){
    });
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
                var order_no = ret.result['order_no'];
                var pay_config_id = ret.result['pay_config_id'];
                var pay_method = ret.result['pay_method'];
                var params = ret.result['params'];
                var code_url = params['code_url'] || '';
                var pay_order_no = params['pay_order_no'] || '';
                if(code_url.length <= 0){
                    layer.msg('操作成功！订单号【' + order_no + '】', {
                        icon: 1,
                        shade: 0.3,
                        time: 3000 //2秒关闭（如果不配置，默认是3秒）
                    }, function(){
                        var reset_total = true; // 是否重新从数据库获取总页数 true:重新获取,false不重新获取
                        if(id > 0) reset_total = false;
                        window.parent.parent_reset_list_iframe_close(reset_total);// 刷新并关闭
                        //do something
                    });
                }else{
                    console.log('--code_url--', code_url);
                    showQRCodeTable('qrcode', code_url, 250, 250);// 显示付款二维码
                    $('.qrcode_block').show();// 显示 付款码
                    $('#submitBtn').hide();// 隐藏按钮
                    // 每秒去查询一下付款码付款情况


                    SUBMIT_FORM = true;//标记为未提交过

                }
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

// 生成的付款码，支付定时查询支付情况
// pay_order_no 我方订单号
function loopQueryResult(pay_order_no) {
    loopDoingFun(60, 1000, function (intervalId, close_loop, loopedSec, loop_num) {
        console.log('===每次循环的方法开始==1=');
        console.log('=1==intervalId===', intervalId);
        console.log('=1==close_loop===', close_loop);
        console.log('=1==loopedSec===', loopedSec);
        console.log('=1==loop_num===', loop_num);
        // if(loop_num >= 20) {// 执行次数关闭
            // clearInterval(intervalId);
            // close_loop.is_close = true; // -- 一般用这个控制开关
        // }
    }, function (intervalLoopId, close_loop, do_sec_num, do_num) {
        console.log('===每分钟循环的方法开始==2=');
        console.log('=2==intervalLoopId===', intervalLoopId);
        console.log('=2==close_loop===', close_loop);
        console.log('=2==do_sec_num===', do_sec_num);
        console.log('=2==do_num===', do_num);
        // if(do_num >= 10) {// 执行次数关闭
            // clearInterval(intervalLoopId);
            // close_loop.is_close = true; // -- 一般用这个控制开关
        // }
        // if()
    });
}
