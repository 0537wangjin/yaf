<?php

/**
 * @name IndexController
 * @author root
 * @desc 默认控制器
 */
class IndexController extends BaseController
{
    public function indexAction()
    {

    }

    /**
     * 文件上传
     */
    public function upfileAction()
    {
        echo Help::upload('upfile', 'upload');// 表单name, 上传目录
        die;
    }
    /**
     * 模拟登陆, 设置session
     */
    public function loginAction()
    {
        Help::setSession('openid', Help::randStr(32));
        Help::setSession('username', 'ccjin');
        Help::setSession('face', 'https://pic2.zhimg.com/v2-ab75d3075361743ed110165a9dd16679_xs.jpg');

        Header('Location: ./user');
    }
    public function userAction()
    {
        echo Help::getSession('openid');
        die;
    }
    /**
     * Redis使用方法
     */
    public function redisAction()
    {
        $redis = Yaf_Registry::get('redis');
        $redis->set('name', time());
        echo $redis->get('name');
        die;
    }
}
