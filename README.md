## Yaf 使用说明



#### 数据库操作 [Medoo]
详细使用方法参考 [http://medoo.lvtao.net/doc.php](http://medoo.lvtao.net/doc.php)

````
$this->db = Yaf_Registry::get('db');
$this->db->get('ju_user', array('id', 'username'), array('username' => $data['username']));
````

#### Redis操作
````
public function redisAction()
{
    $redis = Yaf_Registry::get('redis');
    $redis->set('name', time());
    echo $redis->get('name');
    die;
}
````

#### 文件上传
````
HTML代码
<form action="<?php echo BASE_URL;?>index/upfile" method="post" enctype="multipart/form-data">
    <input type="file" name="upfile" />
    <input type="submit">
</form>
````

````
控制器
public function upfileAction()
{
	// 详细参数,请参考Help类; 表单name, 上传目录
    echo Help::upload('upfile', 'upload');//上传到public/upload文件夹
    die;
}
````
#### Session操作
````
查看sessionn_id
Yaf_Session::getInstance();// 初始化session
echo session_id();
die;
````

````
设置session
Help::setSession('openid', Help::randStr(32));
````

````
获取session
Help::getSession('openid');
````


#### 小技巧
- 获取当前控制器
<code>
Help::getRoute();
</code>
- 页面跳转
<code>$this->redirect(BASE_URL);</code>