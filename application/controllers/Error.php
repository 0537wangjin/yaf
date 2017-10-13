<?php

/**
 * Created by PhpStorm.
 * User: wangjin
 * Date: 2017/10/13
 * Time: 上午9:49
 */
class ErrorController extends BaseController
{
    // 错误信息输出
    public function errorAction($exception)
    {
        switch ($exception->getCode()) {
            case YAF_ERR_NOTFOUND_MODULE:
            case YAF_ERR_NOTFOUND_CONTROLLER:
            case YAF_ERR_NOTFOUND_ACTION:
            case YAF_ERR_NOTFOUND_VIEW:
                header('HTTP/1.1 404 Not Found');
                header("status: 404 Not Found");
                break;
            default:
                header('HTTP/1.0 500 Internal Server Error');
                break;
        }
        if (is_string($exception))
            echo $exception;
        else
            echo $exception->getMessage();
    }
}