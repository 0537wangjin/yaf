<?php

/**
 * Created by PhpStorm.
 * User: wangjin
 * Date: 2017/9/4
 * Time: 下午1:53
 */
class BaseController extends Yaf_Controller_Abstract
{
    protected $twig = null;
    /**
     * init 初始化函数
     */
    protected function init()
    {
        $this->twig = Yaf_Registry::get('twig');
    }
}