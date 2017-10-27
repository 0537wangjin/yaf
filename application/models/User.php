<?php

/**
 * @name UserModel
 * @desc 用户模型
 * @author ccjin
 */
class UserModel extends BaseModel
{
    /**
     * 登录验证
     * @param $data
     */
    public function client_get($uid)
    {
        $info = $this->db->get('ju_user', array('id', 'username', 'leixing', 'title', 'titlepic', 'gender', 'zhiye', 'xuexiao', 'tags', 'lng', 'lat'), array('id' => $uid));
        return $info;
    }
}
