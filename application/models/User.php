<?php

/**
 * @name UserModel
 * @desc 用户模型
 * @author ccjin
 */
class UserModel
{

    public function __construct()
    {
        $this->db = Yaf_Registry::get('db');
    }

    /**
     * 登录验证
     * @param $data
     */
    public function login($data)
    {
        $info = $this->db->get('ju_user', array('id', 'username', 'password', 'leixing', 'title', 'titlepic', 'gender', 'zhiye', 'xuexiao', 'tags', 'lng', 'lat'), array('username' => $data['username']));
        // password
        $pass = md5($data['password']);
        if ($pass != $info['password']) {
            Help::sys_out_fail('密码错误', 102);
        }
        // 更新登录信息
        $this->db->update('ju_user', array('loginnum[+]' => 1, 'lasttime' => time(), 'devicetype' => $data['devicetype'], 'lastversion' => $data['lastloginversion']), array('id' => $info['id']));
        // Session设置
        $token = Help::sys_get_token($info['id']);
        Help::setSession('username', $info['username']);//用户名
        Help::setSession('nickname', $info['title']);//演员名字
        $info['token'] = $token;
        Help::sys_out_success('登录成功', $info);
    }

    /**
     * 登录验证
     * @param $data
     */
    public function client_get($uid)
    {
        $info = $this->db->get('ju_user', array('id', 'username', 'leixing', 'title', 'titlepic', 'gender', 'zhiye', 'xuexiao', 'tags', 'lng', 'lat'), array('id' => $uid));
        return $info;
    }

    /**
     * 获取博客列表 -1 删除 0 草稿 1 正常 2 置顶
     */
    public function getList($where = '', $offset = 10, $limit = 0)
    {
        $sql = "SELECT id, username, leixing, title, titlepic, gender, zhiye, xuexiao, tags, lng, lat FROM `ju_user` WHERE 1 {$where} ORDER BY `id` DESC LIMIT {$limit},{$offset}";
        $data = $this->db->query($sql)->fetchAll();
        return $data;
    }

}
