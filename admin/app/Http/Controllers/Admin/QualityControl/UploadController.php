<?php

namespace App\Http\Controllers\Admin\QualityControl;

use App\Business\Controller\API\QualityControl\CTAPIResourceBusiness;
use App\Services\Request\CommonRequest;
use App\Services\Tool;
use Illuminate\Http\Request;
use App\Http\Controllers\WorksController;

class UploadController extends BasicController
{
    public $controller_id =0;// 功能小模块[控制器]id - controller_id  历史表 、正在进行表 与原表相同

    public $model_name = 'Resource';
    // 大后台 admin/年/月/日/文件
    // 企业 company/[生产单元/]年/月/日/文件
    protected $source_path = '/resource/company/';

    /**
     * 文件上传--图片
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function index(Request $request)
    {
        $this->InitParams($request);
        return CTAPIResourceBusiness::filePlupload($request, $this, 1);
    }

    /**
     * 根据资源id，删除资源[删除文件和数据记录]
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_del(Request $request)
    {
//        $this->InitParams($request);
//        return CTAPIResourceBusiness::delAjax($request, $this);

        $tem_id = CommonRequest::get($request, 'id');
        Tool::formatOneArrVals($tem_id, [null, ''], ',', 1 | 2 | 4 | 8);
        $pageNum = (is_array($tem_id) && count($tem_id) > 1 ) ? 1024 : 512;
        return $this->exeDoPublicFun($request, $pageNum, 4,'', true, '', [], function (&$reDataArr) use ($request){
            return CTAPIResourceBusiness::delAjax($request, $this);
        });
    }

    /**
     * 文件上传
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
//    public function index(Request $request)
//    {
//        $errArr = [
//            'result' => 'failed',// 文件上传失败
//            'message' => '文件内容包含违规内容',//用于在界面上提示用户的消息
//        ];
//        // return $errArr;
//
//        $requestLog = [
//            'file'       =>$request->file('file'),
//            'files'       => $request->file(),
//            'posts'  => $request->post(),
//            'input'      => $request->input(),
//        ];
//        Log::info('上传文件日志',$requestLog);
//
//        if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
//            $photo = $request->file('photo');
//            $extension = $photo->extension();
//            //$store_result = $photo->store('photo');
//            $store_result = $photo->storeAs('photo', 'testaaaa.jpg');
//            $output = [
//                'extension' => $extension,
//                'store_result' => $store_result
//            ];
//            $sucArr = [
//                'result' => 'ok',// 文件上传成功
//                'id' => 10001, // 文件在服务器上的唯一标识
//                'url'=> 'http://example.com/file-10001.jpg',// 文件的下载地址
//                'output'  => $output,
//            ];
//            return $sucArr;
//            Log::info('上传文件日志',$output);
//            print_r($output);exit();
//        }
//        $errArr = [
//            'result' => 'failed',// 文件上传失败
//            'message' => '文件内容包含违规内容',//用于在界面上提示用户的消息
//        ];
//        return $errArr;
//        $sucArr = [
//            'result' => 'ok',// 文件上传成功
//            'id' => 10001, // 文件在服务器上的唯一标识
//            'url'=> 'http://example.com/file-10001.jpg',// 文件的下载地址
//        ];
//        return $sucArr;
//    }

}
