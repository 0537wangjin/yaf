<?php

/**
 * Created by PhpStorm.
 * User: wangjin
 * Date: 2017/10/12
 * Time: 下午2:13
 */
class BaseModel
{
    protected $db = null;

    public function __construct()
    {
        $this->db = Yaf_Registry::get('db');
    }
}