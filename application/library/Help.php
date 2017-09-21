<?php

/**
 *      [CodeJm!] Author CodeJm[codejm@163.com].
 *
 *
 *      $Id: Tools_help.php 2014-07-29 17:24:01 codejm $
 */
class Help
{

    /**
     * 使用反斜线引用数据 防注入 前端xss过滤 (使用pdo, 省略addslashes过滤)
     */
    public static function filter($string, $force = 1, $allow = '')
    {
        if ($force) {
            if (is_array($string)) {
                foreach ($string as $key => $val) {
                    $string[$key] = self::filter($val, $force, $allow);
                }
            } else {
                $string = self::remove_xss($string, $allow);
                $string = addslashes($string);
            }
        }
        return $string;
    }

    /**
     * 移除HTML中的危险代码，如iframe和script
     * @param $val
     * @return unknown_type
     */
    public static function remove_xss($content, $allow = '')
    {
        $danger = 'javascript,vbscript,expression,applet,meta,xml,blink,link,style,script,embed,object,iframe,frame,frameset,ilayer,layer,bgsound,title,base,eval';
        $event = 'onabort|onactivate|onafterprint|onafterupdate|onbeforeactivate|onbeforecopy|onbeforecut|onbeforedeactivate|onbeforeeditfocus|' .
            'onbeforepaste|onbeforeprint|onbeforeunload|onbeforeupdate|onblur|onbounce|oncellchange|onchange|onclick|oncontextmenu|oncontrolselect|' .
            'oncopy|oncut|ondataavailable|ondatasetchanged|ondatasetcomplete|ondblclick|ondeactivate|ondrag|ondragend|ondragenter|ondragleave|' .
            'ondragover|ondragstart|ondrop|onerror|onerrorupdate|onfilterchange|onfinish|onfocus|onfocusin|onfocusout|onhelp|onkeydown|onkeypress|' .
            'onkeyup|onlayoutcomplete|onload|onlosecapture|onmousedown|onmouseenter|onmouseleave|onmousemove|onmouseout|onmouseover|onmouseup|' .
            'onmousewheel|onmove|onmoveend|onmovestart|onpaste|onpropertychange|onreadystatechange|onreset|onresize|onresizeend|onresizestart|' .
            'onrowenter|onrowexit|onrowsdelete|onrowsinserted|onscroll|onselect|onselectionchange|onselectstart|onstart|onstop|onsubmit|onunload';

        if (!empty($allow)) {
            $allows = explode(',', $allow);
            $danger = str_replace($allow, '', $danger);
        }
        $danger = str_replace(',', '|', $danger);
        //替换所有危险标签
        $content = preg_replace("/<\s*({$danger})[^>]*>[^<]*(<\s*\/\s*\\1\s*>)?/is", '', $content);
        //替换所有危险的JS事件
        $content = preg_replace("/<([^>]*)({$event})\s*\=([^>]*)>/is", "<\\1 \\3>", $content);
        return $content;
    }

    /**
     * 获取GET数据 help::getg("name");
     */
    public static function getg($p, $t = "")
    {
        return isset($_GET[$p]) ? self::filter($_GET[$p], $t) : $t;
    }

    /**
     * 获取POST数据 help::getp("name");
     */
    public static function getp($p, $t = "")
    {
        return isset($_POST[$p]) ? self::filter($_POST[$p], $t) : $t;
    }

    /**
     * 获取完整上传地址
     *
     */
    public static function fbu($url = '')
    {
        $uri = $url;
        $config = \Yaf_Registry::get('configarr');
        $url = rtrim($config['application']['site']['uploadUrl'], '/') . '/' . ltrim($url, '/');

        if (stripos($url, 'http://') === false) {
            $uploadBaseUrl = rtrim('http://' . $_SERVER['HTTP_HOST']) . '/' . ltrim($url, '/');
        } else {
            $uploadBaseUrl = $url;
        }

        if (empty($uri)) {
            return $uploadBaseUrl;
        } else {
            return (stripos($uri, 'http://') === 0) ? $uri : $uploadBaseUrl;
        }
    }

    /**
     * 获取完整系统地址
     *
     */
    public static function sfbu($url = '')
    {
        $config = \Yaf_Registry::get('configarr');
        $url = $config['application']['site']['uploadUri'] . $url;
        return PUBLIC_PATH . $url;
    }

    /**
     * url 生成
     * @param string $route 路由
     * @param array $param 参数数组
     *
     * @return string $url
     */
    public static function url($route, $params = array())
    {
        //$route = preg_replace('/^m\//is', 'mm/', $route);
        // rewrite start
        $rewrite_route = \Yaf_Registry::get('rewrite_route');
        $route_lower = strtolower($route);
        if (isset($rewrite_route[$route_lower])) {
            $router = Yaf_Dispatcher::getInstance()->getRouter();
            $currRoute = $router->getRoute($route_lower);
            if ($currRoute instanceof Yaf_Route_Regex) {
                $route_temp = explode('/', $route);
                $info = array(':m' => $route_temp[0], ':c' => $route_temp[1], ':a' => $route_temp[2]);
                $url = $currRoute->assemble($info, array());
            } else {
                $url = $currRoute->assemble($params, array());
            }
            if ($url) {
                $config = \Yaf_Registry::get('configarr');
                $url = $config['application']['site']['baseUri'] . $url;
                $params_other = array();
                foreach ($params as $key => $value) {
                    if ($value === 0 || $key == 'page')
                        continue;
                    if (strpos($url, ':' . $key) !== false) {
                        $url = str_replace(':' . $key, $value, $url);
                        unset($params[$key]);
                    } else {
                        $params_other[$key] = $value;
                    }
                }

                $url = rtrim($url, '/');
                if ($params_other) {
                    $query = http_build_query($params_other);
                    if ($query) {
                        $url = $url . '?' . $query;
                    }
                }
                return $url;
            }
        }
        // rewrite end


        // 系统默认
        $moduleName = \Yaf_Dispatcher::getInstance()->getRequest()->getModuleName();
        $controllerName = \Yaf_Dispatcher::getInstance()->getRequest()->getControllerName();
        $actionName = \Yaf_Dispatcher::getInstance()->getRequest()->getActionName();

        // 当前url
        if ($route == 'curr_url') {
            $route = $moduleName . '/' . $controllerName . '/' . $actionName;
            $route = strtolower($route);
            $arr = \Yaf_Dispatcher::getInstance()->getRequest()->getParams();

            // backend sort 处理
            if (isset($arr['sort'])) {
                $sort = explode('.', $arr['sort']);
                if ($params['sort'] == $sort[0])
                    unset($params['sort']);
                else
                    unset($arr['sort']);
            }
            $params = array_merge($arr, $params);
            if (isset($params['sort'])) {
                if (stripos($params['sort'], '.desc') !== false) {
                    $params['sort'] = str_ireplace('.desc', '', $params['sort']);
                } else {
                    $params['sort'] = $params['sort'] . '.desc';
                }
            }

        } elseif ($route[0] == '/') {
            // 合并参数
            $arr = \Yaf_Dispatcher::getInstance()->getRequest()->getParams();
            $params = array_merge($arr, $params);
            if (isset($params['page'])) {
                unset($params['page']);
                $params['page'] = '';
            }
            $route = $moduleName . '/' . $controllerName . '/' . $actionName;
        }
        $config = \Yaf_Registry::get('configarr');
        $url = $config['application']['site']['baseUri'];
        $url = $url . $route;
        $url = rtrim($url, '/');
        foreach ($params as $key => $value) {
            if (empty($value) && $key != 'page')
                continue;
            $url .= '/' . $key . '/' . $value;
        }

        $currModule = \Yaf_Registry::get('currModule');
        if ($currModule == 'm') {
            $url = preg_replace(array('/index\/index$/i', '/\/index$/i'), '', $url);
        } else {
            $url = preg_replace('/index\/index$/i', '', $url);
        }
        return $url;
    }

    /**
     * 获取当前语言获取字符串对应的语言
     * @param string 字符串
     * @return 对应字符串
     */
    public static function lang($str, $args = array())
    {
        $lan_arr = Yaf_Registry::get('lang_arr');
        return vsprintf($lan_arr[$str], $args);
    }

    /**
     * 设置Session值
     * @param mixed $key
     * @param mixed $value
     */
    public static function setSession($key, $value)
    {
        if ($value) {
            $value = base64_encode(serialize($value));
        }
        Yaf_Session::getInstance()->set($key, $value);
    }

    /**
     * 获取Session值
     * @param mixed $key Usually a string, right ?
     * @return mixed
     */
    public static function getSession($key, $value = "")
    {
        $val = Yaf_Session::getInstance()->get($key);
        if (empty($val))
            $val = $value;
        else {
            $val = unserialize(base64_decode($val));
        }
        return $val;
    }
    /**
     * 获取session_id
     * @return string
     */
    public static function getSessionID()
    {
        Yaf_Session::getInstance();// 初始化session
        return session_id();
    }

    /**
     * 获取cookie数据 getcookie($p);
     */
    public static function setCookie($key, $value, $time)
    {
        $config = \Yaf_Registry::get('configarr');
        $pre = $config['application']['cookie']['pre'];

        $key = $pre . $key;
        if ($value != '1') {
            $value = base64_encode(serialize($value));
        }
        setcookie($key, $value, time() + $time, '/', $config['application']['cookie']['domain']);
    }

    /**
     * 获取cookie数据 getcookie($p);
     */
    public static function getCookie($key, $value = "")
    {
        $config = \Yaf_Registry::get('configarr');
        $pre = $config['application']['cookie']['pre'];

        $key = $pre . $key;
        $value = isset($_COOKIE[$key]) ? $_COOKIE[$key] : $value;

        if ($value) {
            $value = unserialize(base64_decode($value));
        }
        return $value;
    }

    /**
     * 加密方式
     *
     */
    public static function hash($password)
    {
        return md5($password);
    }

    /**
     * 字段输出
     *
     */
    public static function arraytofields($array)
    {
        return '`' . implode('`,`', $array) . '`';
    }

    /**
     * 分页函数
     * @param $cur int  当前页码
     * @param $total int  总条数
     * @param $size int 每页条数
     * @param $url string  URL
     * @param $url_suffix string  URL后缀
     *
     */
    public static function pager($cur, $total, $size, $url, $url_suffix = '')
    {
        if ($cur <= 0) {
            $cur = 1;
        }

        if ($total == 0 || $total < $size) {
            $page_num = 0;
        } else {
            $page_num = floor($total / $size);
        }

        if (($total % $size) > 0) {
            $page_num++;
        }

        if ($cur > $page_num) {
            $cur = $page_num;
        }

        $cur > 5 ? $page_start = $cur - 4 : $page_start = 1;

        if ($page_start > ($page_num - 9)) {
            $page_start = $page_num - 8;
        }

        if ($page_start < 1) {
            $page_start = 1;
        }

        $cur < 5 ? $page_end = 10 : $page_end = $cur + 5;
        if ($page_end > $page_num) {
            $page_end = $page_num + 1;
        }

        $cur > 1 ? $pagestr = '<li class="paginate_button previous"><a href="' . $url . '1' . $url_suffix . '">首页</a></li><li class="paginate_button previous"><a href="' . $url . ($cur - 1) . $url_suffix . '">上一页</a></li>' : $pagestr = '<li class="paginate_button previous disabled"><a href="#">首页</a></li><li class="paginate_button previous disabled"><a href="#">上一页</a>';

        for ($i = $page_start; $i < $page_end; $i++) {
            $pagestr .= ($i == $cur) ? '<li class="paginate_button active"><a href="#">' . $cur . '</a></li>' : '<li class="paginate_button"><a href="' . $url . $i . $url_suffix . '">' . $i . '</a></li>';
        }

        if ($total == 0) {
            $pagestr .= '<li class="active"><a href="#">1</a></li>';
        }

        $cur < $page_num ? $pagestr .= '<li class="paginate_button next"><a href="' . $url . ($cur + 1) . $url_suffix . '">下一页</a></li><li class="paginate_button next"><a href="' . $url . $page_num . $url_suffix . '">尾页</a></li>' : $pagestr .= '<li class="paginate_button next disabled"><a href="#">下一页</a></li><li class="paginate_button next disabled"><a href="#">尾页</a></li>';
        return $pagestr;
    }

    /**
     * 图片上传接口
     * @param string $input 表单名称
     * @param string $dir 路径
     * @param string $size 大小
     *
     */
    public static function upload($input, $dir, $return = "url", $type = "image", $size = "10M")
    {
        if (empty($_FILES[$input]['tmp_name'])) {
            return false;
        }
        $subdir1 = date('Ym');
        $subdir2 = date('d');
        $subdir = $dir . '/' . $subdir1 . '/' . $subdir2 . '/';

        $config = \Yaf_Registry::get('configarr');
        $url = $config['application']['site']['uploadUri'];
        $dir = PUBLIC_PATH . $url . $subdir;
        $dir = str_replace('//', '/', $dir);


        $fileUpload = new FileUpload();
        $fileUpload->setInput($input);
        $fileUpload->setDestinationDirectory($dir, true);
        $fileUpload->setAllowMimeType($type);
        $fileUpload->setMaxFileSize($size);
        $fileUpload->setAutoFilename();
        $fileUpload->save();
        $fileInfo = $fileUpload->getInfo();
        if ($fileUpload->getStatus()) {
            if ($return == 'url')
                return $subdir . $fileInfo->filename;
            else
                return $fileInfo;
        }
        return false;
    }

    /**
     *  获取用户的ip
     */
    public static function getIp()
    {
        $keys = array('X_FORWARDED_FOR', 'HTTP_X_FORWARDED_FOR', 'CLIENT_IP', 'REMOTE_ADDR');
        foreach ($keys as $key) {
            if (isset($_SERVER[$key])) {
                return $_SERVER[$key];
            }
        }
        return '';
    }

    /**
     * 获取客户端ip端口
     *
     */
    public static function getIpPort()
    {
        return (int)(getenv('REMOTE_PORT') ? getenv('REMOTE_PORT') : $_SERVER['REMOTE_PORT']);
    }


    /**
     * 通过新浪接口 获取ip地理位置
     */
    public static function iplookup($ip)
    {
        $add = '未知区域';

        $str = file_get_contents('http://ip.taobao.com/service/getIpInfo.php?ip=' . $ip);
        $str = json_decode($str, true);
        if ($str) {
            $add = $str['data']['region'] . ' ' . $str['data']['city'];
        }
        return $add;
    }

    /**
     * 获取用户浏览器型号。新加浏览器，修改代码，增加特征字符串.把IE加到12.0 可以使用5-10年了.
     */
    static function getBrowser()
    {
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'Maxthon')) {
            $browser = 'Maxthon';
        } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 12.0')) {
            $browser = 'IE12.0';
        } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 11.0')) {
            $browser = 'IE11.0';
        } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 10.0')) {
            $browser = 'IE10.0';
        } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 9.0')) {
            $browser = 'IE9.0';
        } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 8.0')) {
            $browser = 'IE8.0';
        } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 7.0')) {
            $browser = 'IE7.0';
        } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 6.0')) {
            $browser = 'IE6.0';
        } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'NetCaptor')) {
            $browser = 'NetCaptor';
        } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Netscape')) {
            $browser = 'Netscape';
        } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Lynx')) {
            $browser = 'Lynx';
        } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Opera')) {
            $browser = 'Opera';
        } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome')) {
            $browser = 'Google';
        } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Firefox')) {
            $browser = 'Firefox';
        } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Safari')) {
            $browser = 'Safari';
        } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'iphone')) {
            $browser = 'iphone';
        } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'ipod')) {
            $browser = 'ipod';
        } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'ipad')) {
            $browser = 'ipad';
        } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'android')) {
            $browser = 'android';
        } else {
            $browser = 'other';
        }
        return $browser;
    }

    /**
     * 检测是否为手机访问
     *
     */
    static function is_mobile()
    {
        $mobilebrowser_list = array('iphone', 'android', 'phone', 'mobile', 'wap', 'netfront', 'java', 'opera mobi', 'opera mini',
            'ucweb', 'windows ce', 'symbian', 'series', 'webos', 'sony', 'blackberry', 'dopod', 'nokia', 'samsung',
            'palmsource', 'xda', 'pieplus', 'meizu', 'midp', 'cldc', 'motorola', 'foma', 'docomo', 'up.browser',
            'up.link', 'blazer', 'helio', 'hosin', 'huawei', 'novarra', 'coolpad', 'webos', 'techfaith', 'palmsource',
            'alcatel', 'amoi', 'ktouch', 'nexian', 'ericsson', 'philips', 'sagem', 'wellcom', 'bunjalloo', 'maui', 'smartphone',
            'iemobile', 'spice', 'bird', 'zte-', 'longcos', 'pantech', 'gionee', 'portalmmm', 'jig browser', 'hiptop',
            'benq', 'haier', '^lct', '320x320', '240x320', '176x220', 'pad', 'gt-p1000');

        $useragent = strtolower($_SERVER['HTTP_USER_AGENT']);

        foreach ($mobilebrowser_list as $v) {
            if (strpos($useragent, $v) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * 获取当前请求的完整URL
     */
    static function getCurrURL()
    {
        $pageURL = 'http';
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
            $pageURL .= 's';
        }
        $pageURL .= '://';
        if ($_SERVER['SERVER_PORT'] != '80') {
            $pageURL .= $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . $_SERVER['REQUEST_URI'];
        } else {
            $pageURL .= $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
        }
        $pageURL = str_replace(array('?lang=en-us', '&lang=en-us', '?lang=zh-cn', '&lang=zh-cn', '?lang=zh-tw', '&lang=zh-tw'), '', $pageURL);
        return $pageURL;
    }

    /**
     * 获取内存限制
     *
     * @return int
     */
    public static function getMemoryLimit()
    {
        $memory_limit = @ini_get('memory_limit');
        return self::sizeInBytes($memory_limit);
    }

    /**
     * 获取系统临时文件路径
     */
    public static function sys_get_temp_dir()
    {
        if (function_exists('sys_get_temp_dir')) {
            return sys_get_temp_dir();
        }
        if ($temp = getenv('TMP')) {
            return $temp;
        }
        if ($temp = getenv('TEMP')) {
            return $temp;
        }
        if ($temp = getenv('TMPDIR')) {
            return $temp;
        }
        $temp = tempnam(__FILE__, '');
        if (file_exists($temp)) {
            unlink($temp);
            return dirname($temp);
        }
        return null;
    }

    /**
     * 下载文件保存到指定位置
     *
     * @param $url
     * @param $filepath
     *
     * @return bool
     */
    public static function saveFile($url, $filepath)
    {
        if ($url && !empty($filepath)) {
            $file = file_get_contents($url);
            $fp = @fopen($filepath, 'w');
            if ($fp) {
                @fwrite($fp, $file);
                @fclose($fp);
                return $filepath;
            }
        }
        return false;
    }

    /**
     *  curl方式post数据  $arr数组用来设置要post的字段和数值 help::getpost("http://www.123.com",$array);
     *  $array = array('name'=>'good','pass'=>'wrong');
     *
     */
    public static function getpost($URL, $arr, $build = 1, $header = false)
    {
        if ($build)
            $arr = http_build_query($arr);

        $ch = curl_init();
        if ($header) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }
        curl_setopt($ch, CURLOPT_URL, $URL);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);      //设置返回信息的内容和方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $arr);       //发送post数据
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);            //设置30秒超时
        $result = curl_exec($ch);                         //进行数据传输
        curl_close($ch);                                  //关闭
        return $result;
    }

    /**
     * curl 方式 get数据 help::getget('http://www.123.com')
     *
     */
    public static function getget($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);            //设置30秒超时
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    /**
     * 获取文件扩展名
     *
     */
    public static function getFileExt($filename)
    {
        return addslashes(strtolower(substr(strrchr($filename, '.'), 1, 10)));
    }

    /**
     * 人性化文件大小单位
     */
    public static function sizeFormat($size, $precision = 2)
    {
        $base = log($size) / log(1024);
        $suffixes = array('B', 'K', 'M', 'G');
        return round(pow(1024, $base - floor($base)), $precision) . $suffixes[floor($base)];
    }

    /**
     * 将人性化的文件大小转换成byte
     */
    public static function sizeInBytes($size)
    {
        $unit = 'B';
        $units = array('B' => 0, 'K' => 1, 'M' => 2, 'G' => 3);
        $matches = array();
        preg_match('/(?<size>[\d\.]+)\s*(?<unit>b|k|m|g)?/i', $size, $matches);
        if (array_key_exists('unit', $matches)) {
            $unit = strtoupper($matches['unit']);
        }
        return (floatval($matches['size']) * pow(1024, $units[$unit]));
    }

    /**
     * 显示错误信息
     *
     * @param string $string
     * @param array $error
     * @param bool $htmlentities
     *
     * @return mixed|string
     */
    public static function displayError($string = 'Fatal error', $error = array(), $htmlentities = true)
    {
        if (DEBUG_MODE) {
            if (!is_array($error) || empty($error))
                return str_replace('"', '&quot;', $string) . ('<pre>' . print_r(debug_backtrace(), true) . '</pre>');
            $key = md5(str_replace('\'', '\\\'', $string));
            $str = (isset($error) AND is_array($error) AND key_exists($key, $error)) ? ($htmlentities ? htmlentities($error[$key], ENT_COMPAT, 'UTF-8') : $error[$key]) : $string;
            return str_replace('"', '&quot;', stripslashes($str));
        } else {
            return str_replace('"', '&quot;', $string);
        }
    }

    /**
     * 根据格式获取当前时间
     *
     */
    public static function hdate($format = 'Y-m-d H:i:s', $time = 0)
    {
        if (empty($time)) {
            return date($format);
        }
        return date($format, $time);
    }

    /**
     * 根据参数获取当前时间戳
     *
     */
    public static function htime($date = '')
    {
        if (empty($date)) {
            return time();
        }
        return strtotime($date);
    }

    /**
     * 将数组中所有的值转换为整形
     * @param array $array
     * @return array
     *
     */
    static function arrayValue2num($array)
    {
        foreach ($array as $key => $value) {
            $array[$key] = intval($value);
        }
        return $array;
    }


    /**
     * 主键为key
     *
     */
    static function formatkey($result, $key = '')
    {
        // key 键值对
        $data = array();
        if ($result && $key) {
            foreach ($result as $value) {
                $data[$value[$key]] = $value;
            }
        } else if ($result) {
            $data = $result;
        }
        return $data;
    }

    /***
     * 清除html
     *
     */
    public static function clearHTML($str)
    {
        $str = preg_replace(array("/<br[^>]*>\s*\r*\n*/is", "/<br \/>/is"), "\r\n\r\n", $str);
        $str = preg_replace("/\n\n/is", "\r\n\r\n", $str);

        $str = str_replace(array('&nbsp;', '&bull;', '&mdash;', '&quot;', '&rdquo;', '&ldquo;', '&#8226;', '&#160;'), ' ', strip_tags(htmlspecialchars_decode($str, ENT_NOQUOTES)));
        return $str;

    }

    /***
     * ajaxReturn
     * @param mixed $data 要返回的数据
     * @param String $type AJAX返回数据格式
     */
    static function ajaxReturn($data, $type = 'JSON')
    {
        switch (strtoupper($type)) {
            case 'JSON':
                // 返回JSON数据格式到客户端 包含状态信息
                header('Content-Type:application/json; charset=utf-8');
                exit(json_encode($data));
            case 'JSONP':
                // 返回JSON数据格式到客户端 包含状态信息
                header('Content-Type:application/json; charset=utf-8');
                $handler = self::getg('callback', 'callfun');
                exit($handler . '(' . json_encode($data) . ');');
        }
    }

    public static function formatTime($time)
    {
        $rtime = date('Y/m/d H:i', $time);
        $htime = date('H:i', $time);
        $time = time() - $time;
        if ($time < 60) {
            $str = '刚刚';
        } elseif ($time < 60 * 60) {
            $min = floor($time / 60);
            $str = $min . '分钟前';
        } elseif ($time < 60 * 60 * 24) {
            $h = floor($time / (60 * 60));
            $str = $h . '小时前 ' . $htime;
        } elseif ($time < 60 * 60 * 24 * 3) {
            $d = floor($time / (60 * 60 * 24));
            if ($d == 1) {
                $str = '昨天 ' . $rtime;
            } else {
                $str = '前天 ' . $rtime;
            }
        } else {
            $str = $rtime;
        }
        return $str;
    }

    public static function emoji($str)
    {
        $str = str_replace('[偷笑]', '<img class="emoji" src="http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/19/heia_org.gif" />', $str);
        $str = str_replace('[二哈]', '<img class="emoji" src="http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/74/moren_hashiqi_org.png" />', $str);
        $str = str_replace('[嘻嘻]', '<img class="emoji" src="http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/0b/tootha_org.gif" />', $str);
        $str = str_replace('[鼓掌]', '<img class="emoji" src="http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/36/gza_org.gif" />', $str);
        $str = str_replace('[悲伤]', '<img class="emoji" src="http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/1a/bs_org.gif" />', $str);
        $str = str_replace('[微笑]', '<img class="emoji" src="http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/5c/huanglianwx_org.gif" />', $str);
        $str = str_replace('[生病]', '<img class="emoji" src="http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/b6/sb_org.gif" />', $str);
        $str = str_replace('[摊手]', '<img class="emoji" src="http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/09/pcmoren_tanshou_org.png" />', $str);
        return $str;
    }

    /**
     * 新浪微博图片大图转小图
     * @param $url
     * @return mixed
     */
    public static function bigToSmall($url)
    {
        $url = str_replace('mw1024', 'thumb180', $url);
        $url = str_replace('mw690', 'thumb180', $url);
        $url = str_replace('bmiddle', 'thumb180', $url);
        return $url;
    }

    /**
     * 获取当前路由
     * @param $url
     * @return string
     */
    public static function getRoute($url = '')
    {
        $url = $url ? $url : $_SERVER["REQUEST_URI"];
        // 删除开始的斜杠
        $url = ltrim($url, '/');
        // 删除?后的内容
        $start = strpos($url, '?');
        if ($start) {
            $url = mb_strcut($url, 0, $start);
        }
        return $url;
    }

    /**
     * 根据client_id来生成并返回token
     * @param $client_id
     * @param $nickname
     * @return string
     */
    public static function sys_get_token($client_id)
    {
        $myToken = "TK_" . rand(1000, 9999) . "_" . $client_id;//命名规则：TK_4位随机数_用户主键ID
        self::setSession('token', $myToken);
        return $myToken;
    }

    /**
     * 验证签名
     */
    public static function validateSign()
    {
        //$datetime = self::getp('timestamp');
        //$datetime = time();
        // 获取加密KEY
        $key = Yaf_Registry::get('config')['application']['app']['appkey'];
        $sign = self::getp('sign');
        /*if ($_SERVER['REQUEST_TIME'] - $datetime > 60) {
            self::sys_out_fail('会话超时', 500);
        }*/
        $path = $key . '|' . self::getRoute($_SERVER["REQUEST_URI"]);
        $signValue = md5($path);
        if ($signValue != $sign) {
            self::sys_out_fail('sign: ' . $signValue . ' old: ' . $path, 101);
        }
    }

    /**
     * app接口返回json
     */
    public static function print_json($parm_array = array())
    {
        //print_r($parm_array);
        //$parm_array['route'] = self::getRoute();
        $json = array();
        $json['code'] = intval($parm_array['error_code']);
        if($parm_array['success']==true){
            $json['message'] = 'success';
        }else{
            $json['message'] = $parm_array['msg'];
        }
        $json['data'] = $parm_array['infor']?$parm_array['infor']:NULL;
        echo json_encode($json);
        die;
    }

    // 向客户端输出错误信息(500表示是服务器端异常错误，需要重试)
    public static function sys_out_fail($parmMsg = NULL, $errorNumber = 500)
    {
        unset($result_array);
        $result_array['success'] = false;//注意：为了和extjs兼容，此处必须不带引号

        if (empty($parmMsg)) {
            $parmMsg = "操作失败！";
        } else {
            $parmMsg = $parmMsg;
        }

        $result_array['msg'] = $parmMsg;
        $result_array['error_code'] = $errorNumber;

        self::print_json($result_array);
    }

    // 向客户端输出成功信息
    public static function sys_out_success($parmMsg = NULL, $infor_array = NULL)
    {
        unset($result_array);
        $result_array['success'] = true;//注意：为了和extjs兼容，此处必须不带引号

        if (empty($parmMsg)) {
            $parmMsg = "操作成功！";
        }

        $result_array['msg'] = $parmMsg;
        $result_array['infor'] = $infor_array;//固定输出infor字段，以适配各种复杂情况
        self::print_json($result_array);
    }

    // 封装一下简单（默认）输出(result一般是数据库操作或函数返回的布尔值)
    public static function sys_out_result($result)
    {
        if ($result !== false)
            self::sys_out_success();
        else
            self::sys_out_fail();
    }

    /**
     * 检测多个post参数是否完整并且不为空值
     * @param $post_array
     * @param int $type [0:json输出, 1:die输出]
     */
    public static function sys_check_post($post_array, $type = 0)
    {
        foreach ($post_array as $parm) {
            if (empty(self::getp($parm))) {
                if ($type) {
                    die($parm . " 参数不能为空");
                } else {
                    self::sys_out_fail($parm . " 参数不能为空", 100);
                }
            }
        }
    }

    /**
     * 检测多个post参数是否完整并且不为空值
     * @param $post_array
     * @param int $type [0:json输出, 1:die输出]
     */
    public static function sys_check_get($post_array, $type = 1)
    {
        foreach ($post_array as $parm) {
            if (empty(self::getg($parm))) {
                if ($type) {
                    die($parm . " 参数不能为空");
                } else {
                    self::sys_out_fail($parm . " 参数不能为空", 100);
                }
            }
        }
    }

    //形如：TK_150261_848
    public static function sys_check_token()
    {
        if (!self::getp('token')) {
            self::sys_out_fail('token 参数不能为空', 100);
        }
        if (self::getSession('token') != self::getp('token')) {
            self::sys_out_fail('登录令牌失效，请重新登录！', 102);
        }
    }

    /**
     * 生成随机字符串
     * @param $length 长度
     * @return string
     */
    public static function randStr($length)
    {
        $str = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';//62个字符
        $strlen = 62;
        while ($length > $strlen) {
            $str .= $str;
            $strlen += 62;
        }
        $str = str_shuffle($str);
        return substr($str, 0, $length);
    }

    /**
     * 根据中心点经纬度，来计算以radius千米为距离半径，所画圆圈的正切正方形的四个点坐标
     *
     *param lng float 经度
     *param lat float 纬度
     *param radius float 该点所在圆的半径，默认值为10 千米
     *return array 正方形的四个点的经纬度坐标
     */
    public static function squarePoint($lng, $lat, $radius = 10)
    {

        $dlng = 2 * asin(sin($radius / 12742) / cos(deg2rad($lat)));//12742为地球直径
        $dlng = rad2deg($dlng);

        $dlat = $radius / 6371; //6371为地球平均半径
        $dlat = rad2deg($dlat);

        return array(
            'left-top' => array('lat' => $lat + $dlat, 'lng' => $lng - $dlng),
            'right-top' => array('lat' => $lat + $dlat, 'lng' => $lng + $dlng),
            'left-bottom' => array('lat' => $lat - $dlat, 'lng' => $lng - $dlng),
            'right-bottom' => array('lat' => $lat - $dlat, 'lng' => $lng + $dlng)
        );
    }
}

?>
