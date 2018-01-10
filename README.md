<h1 align="center">Yaf基本封装</h1>
<h3 align="center">https://github.com/0537wangjin/yaf</h3>
<p align="center">QQ：445899710</p>

## 安装说明
#### 安装步骤
````
1. git clone https://github.com/0537wangjin/yaf.git ./
2. composer install
````

<code>
PHP7以下版本, composer.json中的twig引用请修改为 "twig/twig": "^1.35.0"
</code>

#### nginx配置
````
# 增加伪静态规则
if (!-e $request_filename) {
    rewrite ^/(.*\.(js|ico|gif|jpg|png|css|bmp|html|xls)$) /$1 last;
    rewrite ^/(.*) /index.php?$1 last;
}
````

## 使用说明



#### 数据库操作 [Medoo]
详细使用方法参考 [https://medoo.in/doc](https://medoo.in/doc)

````
$this->db = Yaf_Registry::get('db');
$this->db->get('ju_user', array('id', 'username'), array('username' => $data['username']));
````
事务使用方法

````
// 开始事务
$res = $this->db->action(function ($db) use ($id) {
    // 取消默认
    $sql1 = $db->update('sys_address', ['rec' => 0], ['client_id' => $this->uid]);
    if ($sql1->rowCount() <  1){
        return false;
    }
    // 设置新默认
    $sql2 = $db->update('sys_address', ['rec' => 1], ['id' => $id]);
    if ($sql2->rowCount() <  1){
        return false;
    }
});
if ($res == true) {
    die('操作成功!');
} else {
    die('系统出错!');
}
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
#### base64图片上传
````
public function evaluateSaveAction()
{
    $file = array();
    $subdir1 = date('Ym');
    $subdir2 = date('d');
    $subdir = PUBLIC_PATH . 'upload' . '/' . $subdir1 . '/' . $subdir2 . '/';
    if (!file_exists($subdir)) {
        mkdir($subdir, 0777, true);
    }
    if($_POST['pic1']){
        $base64 = $_POST['pic1'];
        $img = Help::upBase64($base64, $subdir, $this->uid . '_reply_');
        $file['pic1'] = '/upload/' . $subdir1 . '/' . $subdir2 . '/' . $img;
    }
    print_r($file);
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
#### 阿里大鱼短信使用方法
````
$verify_code = rand(1000, 9999);
$outId = date('YmdHis');
$sms = new \Aliyun\Dysms(AccessKeyId, AccessKeySecret);
$signName = '支付宝';
$templateCode = 'SMS_90880000';
$username = '15566669999';
$templateParam = array('code' => $verify_code);
$response = $sms->send_sms($signName, $templateCode, $username, $templateParam, $outId);
if (isset($response['Code']) && 'OK' == $response['Code']) {
    //发送成功
    $status = '1';
} else {
    //发送失败
}
````
#### Excel导入
```
/**
     * 批量导入保单
     */
    public function userinfoImportAction()
    {
        // 上传文件
        $file = Help::upload('file', 'upload', 'url', 'xlsx');
        if (empty($file)){
            $this->success(1, '文件上传失败!', $_SERVER['HTTP_REFERER']);
        }
        $file = PUBLIC_PATH . $file;
        // 判断读取文件格式
        $reader = \PHPExcel_IOFactory::createReader('Excel2007');
        if (!$reader->canRead($file)) {
            $reader = \PHPExcel_IOFactory::createReader('Excel5');
        }
        $PHPExcel = $reader->load($file);
        $sheet = $PHPExcel->getActiveSheet();
        $highestRow = $sheet->getHighestRow(); // 取得总行数
        // $highestColumn = $sheet->getHighestColumn(); // 取得总列数
        for ($i = 2; $i <= $highestRow; $i++) {
            // 检查保单号是否存在
            $cardno = $sheet->getCell('A' . $i)->getValue();
            $arg1 = array(
                'starttime' => $sheet->getCell('B' . $i)->getValue(),
                'endtime' => $sheet->getCell('C' . $i)->getValue(),
                'baodanhao' => $sheet->getCell('D' . $i)->getValue(),
                'baoan' => $sheet->getCell('E' . $i)->getValue(),
            );
            if (!empty($cardno)) {
                $res = $this->db->update('userinfo', $arg1, ['cardno' => $cardno]);
            }
        }
        $this->success(1, '上传成功!', $_SERVER['HTTP_REFERER']);
    }
```

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