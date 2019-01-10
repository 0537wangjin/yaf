<?php

/**
 * Created by PhpStorm.
 * User: wangjin
 * Date: 2017/9/4
 * Time: 下午1:53
 */
class BaseController extends Yaf_Controller_Abstract
{
    protected $twig = null;//twig
    protected $db = null;//db
    protected $assign = null;//twig模板参数
    /**
     * init 初始化函数
     */
    protected function init()
    {
        $loader = new \Twig_Loader_Filesystem('views', APP_PATH);
        $this->twig = new \Twig_Environment($loader, array(
            /* 'cache' => './compilation_cache', */
        ));
        $this->db = new \Medoo\Medoo([
            'database_type' => 'mysql',
            'database_name' => $this->arrConfig->application->db->database,
            'server' => $this->arrConfig->application->db->hostname,
            'username' => $this->arrConfig->application->db->username,
            'password' => $this->arrConfig->application->db->password,
            'prefix' => $this->arrConfig->application->db->prefix,
            'logging' => $this->arrConfig->application->db->log,
            'charset' => 'utf8mb4'
        ]);
        // SeasLog 日志设置
        // SeasLog::setBasePath('/data/log');
        // SeasLog::setLogger('kaoqin');
    }
}