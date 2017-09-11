## Yaf 使用说明



#### 数据库操作 [Medoo]
详细使用方法参考 [https://medoo.in/doc](https://medoo.in/doc)

````
$this->db = Yaf_Registry::get('db');
$this->db->get('ju_user', array('id', 'username'), array('username' => $data['username']));
````
事务使用方法

````
$this->db->action(function($db) use ($id){
    // 取消默认
    $db->update('sys_address', ['rec' => 0], ['client_id' => $this->uid]);
    // 设置新默认
    $db->update('sys_address', ['rec' => 1], ['id' => $id]);
});
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

#### 生成缩略图
````
$file = PUBLIC_PATH . $p['titlepic'];
$thumb_file = $file . '_thumb.jpg';
// 生成缩略图
$res = ImageManager::thumbnail($file, $thumb_file, 400);
if ($res) {
    $p['titlepicthumb'] = $p['titlepic'] . '_thumb.jpg';
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
方法1
echo Help::getSession('openid');
方法2
echo unserialize(base64_decode(Yaf_Session::getInstance()->get('openid')));
````


#### 小技巧
- 获取当前控制器
<code>
Help::getRoute();
</code>

- 页面跳转
<code>$this->redirect(BASE_URL);</code>

- 关闭自动加载模板
<code>Yaf_Dispatcher::getInstance()->autoRender(FALSE);</code>

- 调用指定模板
<code>$this->getView()->display('user/advice.php');</code>