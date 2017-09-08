<?php

/**
 * Created by PhpStorm.
 * User: wangjin
 * Date: 2017/9/4
 * Time: 下午5:15
 */
class ServiceController extends BaseController
{
    /**
     * 初始化验证
     */
    public function init()
    {
        parent::init(); // TODO: Change the autogenerated stub
        // 如果没有开启DEBUG模式, 则进行sign验证
        if (!Yaf_Registry::get('config')['application']['debug']) {
            Help::validateSign();
        }
        // 定义不需要检查token的接口数组
        $not_check_token_list = array(
            'service/login',
        );
        // 非指定方法名，统一进行登录令牌有效性检查
        if (!in_array(Help::getRoute($_SERVER["REQUEST_URI"]), $not_check_token_list)) {
            Help::sys_check_token();
        }
    }

    /**
     * APP初始化信息
     */
    public function initAction()
    {
        $data = array();
        $data['startimg'] = array(
            'http://wx1.sinaimg.cn/thumb180/6e00656dgy1fi0r637sowj20ku0rsjuu.jpg',
            'http://wx3.sinaimg.cn/thumb180/5f2f3c0bly1fi0qbrj1wqj215n1jje81.jpg',
        );
        $data['android_last_version'] = '1.0.0';
        $data['android_must_update'] = '0';
        $data['android_update_url'] = 'http://xxx.com/1.2.apk';
        $data['ios_last_version'] = '1.0.0';
        $data['ios_must_update'] = '0';
        $data['ios_update_url'] = 'http://ios.com/';
        $data['phone'] = '15562679693';// 客服电话
        Help::sys_out_success('', $data);
    }

    /**
     * 登录
     */
    public function loginAction()
    {

        $post_array = array('username', 'password', 'devicetype', 'lastloginversion');
        Help::sys_check_post($post_array);
        unset($post_array);
        $data = array();
        $data['username'] = Help::getp('username');
        $data['password'] = Help::getp('password');
        $data['devicetype'] = Help::getp('devicetype');
        $data['lastloginversion'] = Help::getp('lastloginversion');

        $mod = new UserModel();
        $mod->login($data);
    }

    /**
     * 获取用户信息
     */
    public function userInfoAction()
    {
        $uid = Help::getp('id');
        if (empty($uid)) {
            $uid = Help::getSession('userid');
        }
        $mod = new UserModel();
        $res = $mod->client_get($uid);
        Help::sys_out_success('', $res);
    }

    /**
     * 用户注册
     */
    public function userAddAction()
    {

    }
    /**
     * 保存用户信息
     */
    public function userSaveAction()
    {

    }

    /**
     * 获取通告列表
     */
    public function filmListAction()
    {
        $mod = new FilmModel();
        $res = $mod->getList();
        Help::sys_out_success('', $res);
    }
    /**
     * 根据local编号获取通告列表
     */
    public function localFilmListAction()
    {
        $post_array = array('lid');
        Help::sys_check_post($post_array);
        unset($post_array);
        $lid = intval(Help::getp('lid'));
        $mod = new FilmModel();
        $res = $mod->getListByLid($lid);
        Help::sys_out_success('', $res);
    }
}