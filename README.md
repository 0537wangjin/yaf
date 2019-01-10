<h1 align="center">Yaf基本封装</h1>
<h3 align="center">https://github.com/0537wangjin/yaf</h3>
<p align="center">QQ：445899710</p>

#### 包含以下内容的使用方法

- Twig
- SeasLog
- Medoo
- 阿里云短信
- PHPMailer
- 融云API
- Upyun
- 迅搜(xunsearch)
- Swoole
- JWT

## 安装说明




#### 安装步骤
````
1. git clone https://github.com/0537wangjin/yaf.git ./
2. composer install
````

#### nginx配置
````
# 增加伪静态规则
if (!-e $request_filename) {
    rewrite ^/(.*\.(js|ico|gif|jpg|png|css|bmp|html|xls)$) /$1 last;
    rewrite ^/(.*) /index.php?$1 last;
}
````



## 使用说明

#### SeasLog日志
详细使用方法参考 https://github.com/Neeke/SeasLog

如需启用, 请取消注释, 引入位置在

/application/controller/Base.php

````
protected function init()
{
    $this->twig = Yaf_Registry::get('twig');
    // SeasLog 日志设置
    // SeasLog::setBasePath('/data/log');
    // SeasLog::setLogger('kaoqin');
}
````


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
    $db->update('sys_address', ['rec' => 0], ['client_id' => $this->uid]);
    if ($db->error()[0] != '00000') {
        return false;
    }
    // 设置新默认
    $db->update('sys_address', ['rec' => 1], ['id' => $id]);
    if ($db->error()[0] != '00000') {
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
#### 多图上传
````
$avatar = Help::uploads('titlepic', $dir);
if (!empty($avatar)) {
    $subdir = $dir . '/' . date('Ym') . '/' . date('d') . '/';
    for ($i = 0; $i < count($avatar); $i++) {
        $urlname = sha1(mt_rand(1, 9999) . uniqid()) . '.jpg';
        $titlepic = $this->uploadToUpyun(PUBLIC_PATH . $subdir . $avatar[$i], $subdir, $urlname);
        $add['picurl'] = $titlepic;
        $this->db->insert('ju_star_pic', $add);
    }
    //. Success
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
#### 发送邮件
```
$config = $this->db->get('config', ['site_title_small', 'smtp_server', 'smtp_user', 'smtp_pass'], ['id' => 1]);
$mailer = new \PHPMailer();
$mailer->CharSet = "UTF-8";
$mailer->ContentType = 'text/html';
$mailer->IsSMTP();
//0是不输出调试信息
//2是输出详细的调试信息
$mailer->SMTPDebug  = 0;
//需要验证
$mailer->SMTPAuth = true;
$mailer->SMTPSecure = 'ssl';
$mailer->Host = $config['smtp_server'];
$mailer->Port = 465;
$mailer->Username = $config['smtp_user'];
$mailer->Password = $config['smtp_pass'];
$mailer->SetFrom($config['smtp_user'],$config['site_title_small']);
$mailer->AddAddress('445899710@qq.com',"尊敬的客户");
$mailer->Subject = '商户注册!!';
$mailer->MsgHTML('注册成功!!!!!!!');
$res = $mailer->send();
var_dump($res);
```

#### 融云推送

````
$rc = new \Rongcloud($this->ry_appkey, $this->ry_appsec);
$message = '您有新的审批提醒' . PHP_EOL;
$uid = $row['from_uid'];
$rc_content = array('name' => 'update_correct_list', 'data' => '');
$res = $rc->messageSystemPublish($uid, $uid, 'RC:CmdNtf', $rc_content);
$json = json_decode($res, true);
if ($json['code'] == '200') {}
````


#### Upyun

````
/**
 * 上传到UPYUN
 * @param $filename
 * @param $updir
 * @param $urlname
 * @return string
 */
private function uploadToUpyun($filename, $updir, $urlname)
{
    $config = new \Upyun\Config(Yaf_Registry::get('config')->application->upyun->bucketname, Yaf_Registry::get('config')->application->upyun->operatorname, Yaf_Registry::get('config')->application->upyun->operatorpassword);
    $upyun = new \Upyun\Upyun($config);
    // 读文件
    $upyunfile = fopen($filename, 'r');
    //文件上传
    $res = $upyun->write('/tools/' . $updir . '/' . $urlname, $upyunfile);
    //删除本地文件
    @unlink($filename);
    $imgurl = 'http://cdn.huyahaha.com/tools/' . $updir . '/' . $urlname;
    return $imgurl;
}
$avatar = Help::upload('avatar', 'upload');
if (!empty($avatar)) {
    $subdir = $dir . '/' . date('Ym') . '/' . date('d') . '/';
    $urlname = sha1(mt_rand(1, 9999) . uniqid()) . '.jpg';
    $titlepic = $this->uploadToUpyun(PUBLIC_PATH . $avatar, $subdir, $urlname);
    $param['avatar'] = $titlepic;
}
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
