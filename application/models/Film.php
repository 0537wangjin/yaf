<?php

/**
 * Created by PhpStorm.
 * User: wangjin
 * Date: 2017/9/4
 * Time: 下午2:40
 */
class FilmModel
{
    private $db = null;

    public function __construct()
    {
        $this->db = Yaf_Registry::get('db');
    }

    /**
     * 列表
     * @return mixed
     */
    public function getList()
    {
        $list = $this->db->select("ju_film",
            array('id', 'title', 'titlepic', 'newstime', 'onclick', 'leixing', 'didian', 'kaiji', 'zhouqi'),
            array(
                'ORDER' => ['id' => 'DESC']
            ));
        foreach ($list as $key => $val){
            $list[$key]['newstime'] = Help::formatTime($list[$key]['newstime']);
        }
        return $list;
    }
    /**
     * 列表
     * @return mixed
     */
    public function getListByLid($lid)
    {
        $list = $this->db->select("ju_film",
            array('id', 'title', 'titlepic', 'newstime', 'onclick', 'leixing', 'didian', 'kaiji', 'zhouqi'),
            array(
                'lid' => $lid,
                'ORDER' => ['id' => 'DESC']
            ));
        foreach ($list as $key => $val){
            $list[$key]['newstime'] = Help::formatTime($list[$key]['newstime']);
        }
        return $list;
    }
}