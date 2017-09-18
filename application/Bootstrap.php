<?php

/**
 * @name Bootstrap
 * @author root
 * @desc 所有在Bootstrap类中, 以_init开头的方法, 都会被Yaf调用,
 * 这些方法, 都接受一个参数:Yaf_Dispatcher $dispatcher
 * 调用的次序, 和申明的次序相同
 */
class Bootstrap extends Yaf_Bootstrap_Abstract
{

    public function _initConfig()
    {
        // 把配置保存起来
        $this->arrConfig = Yaf_Application::app()->getConfig();
        Yaf_Registry::set('config', $this->arrConfig);
        // 调用方法 Yaf_Registry::get('config')->application->upyun->bucketname
        // 关闭自动加载模板
        Yaf_Dispatcher::getInstance()->autoRender(FALSE);
    }

    // 是否显示错误提示
    public function _initError(Yaf_Dispatcher $dispatcher)
    {/*{{{*/
        if ($this->arrConfig->application->db->showErrors) {
            error_reporting(-1);
        } else {
            error_reporting(-1);
        }
    }

    // 载入数据库
    public function _initDatabase()
    {
        // 数据库1
        Yaf_Registry::set('db', new \Medoo\Medoo([
            'database_type' => 'mysql',
            'database_name' => $this->arrConfig->application->db->database,
            'server' => $this->arrConfig->application->db->hostname,
            'username' => $this->arrConfig->application->db->username,
            'password' => $this->arrConfig->application->db->password,
            'charset' => 'utf8'
        ]));
    }

    // 载入redis
    public function _initRedis()
    {
        /*$redis = new \Redis();
        $redis->connect($this->arrConfig->application->redis->host, $this->arrConfig->application->redis->port);
        Yaf_Registry::set('redis', $redis);*/
    }

    public function _initPlugin(Yaf_Dispatcher $dispatcher)
    {
        //注册一个插件
    }

    public function _initRoute(Yaf_Dispatcher $dispatcher)
    {

        //在这里注册自己的路由协议,默认使用简单路由
    }

    public function _initView()
    {
        //在这里注册自己的view控制器，例如smarty,twig
        $loader = new \Twig_Loader_Filesystem('views', APP_PATH);
        $twig = new \Twig_Environment($loader, array(
            /* 'cache' => './compilation_cache', */
        ));
        Yaf_Registry::set('twig', $twig);
    }
}
