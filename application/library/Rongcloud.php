<?php
/**
 * 融云 Rongcloud 接口
 * Class Rongcloud
 * @author  qfsoft@163.com
 * @version	2016-6-14 09:42:38
 */
class Rongcloud {
    private $appKey;                //appKey
    private $appSecret;             //secret
    const   SERVERAPIURL = 'http://api.cn.ronghub.com';    //请求服务地址
    private $format;                //数据格式 json/xml
    public	$is_record = false;		//是否记录日志
    public	$destination = '';		//日志存放目标
    
    /**
     * 参数初始化
     * @param string $appKey
     * @param string $appSecret
     * @param string $format
     */
    public function __construct($appKey,$appSecret,$format = 'json'){
        $this->appKey = $appKey;
        $this->appSecret = $appSecret;
        $this->format = $format;
        if ($this->is_record) {
        	$this->destination = str_replace("\\", "/", dirname(__FILE__)).'/Rongcloud_'.date('Y_m_d').'.log';		// 默认当前目录
        }
    }
    
    /**
     * 获取 Token
     * @param string $userId   用户 Id，最大长度 64 字节。是用户在 App 中的唯一标识码，必须保证在同一个 App 内不重复，重复的用户 Id 将被当作是同一用户。
     * @param string $name     用户名称，最大长度 128 字节。用来在 Push 推送时显示用户的名称。
     * @param string $portraitUri  用户头像 URI，最大长度 1024 字节。用来在 Push 推送时显示用户的头像。
     * @return json|xml
     */
    public function getToken($userId, $name, $portraitUri) {
    	try{
            if(empty($userId))
                throw new Exception('用户 Id 不能为空');
            if(empty($name))
                throw new Exception('用户名称 不能为空');
            if(empty($portraitUri))
                throw new Exception('用户头像 URI 不能为空');

            $ret = $this->curl('/user/getToken',array('userId'=>$userId,'name'=>$name,'portraitUri'=>$portraitUri));
            if(empty($ret))
                throw new Exception('请求失败');
            return $ret;
        }catch (Exception $e) {
        	$message = date('[ c ]').' '.__METHOD__.' '.$e->getMessage()."\r\n";
        	if ($this->is_record) {
        		error_log($message, 3, $this->destination, '');
        	}else {
        		print_r($e->getMessage());
        	}
        }
    }
    
    /**
     * 刷新用户信息
     * 说明：当您的用户昵称和头像变更时，您的 App Server 应该调用此接口刷新在融云侧保存的用户信息，以便融云发送推送消息的时候，能够正确显示用户信息。
     * @param string $userId	用户 Id，最大长度 64 字节。是用户在 App 中的唯一标识码，必须保证在同一个 App 内不重复，重复的用户 Id 将被当作是同一用户。
     * @param string $name		用户名称，最大长度 128 字节。用来在 Push 推送时，显示用户的名称，刷新用户名称后 5 分钟内生效。（可选，提供即刷新，不提供忽略）
     * @param string $portraitUri	用户头像 URI，最大长度 1024 字节。用来在 Push 推送时显示。（可选，提供即刷新，不提供忽略）
     * @return mixed
     */
    public function userRefresh($userId, $name='', $portraitUri='') {
        try{
            if(empty($userId))
                throw new Exception('用户 Id 不能为空');
            if(empty($name))
                throw new Exception('用户名称不能为空');
            if(empty($portraitUri))
                throw new Exception('用户头像 URI 不能为空');
            $ret = $this->curl('/user/refresh',
                array('userId' => $userId, 'name' => $name, 'portraitUri' => $portraitUri));
            if(empty($ret))
                throw new Exception('请求失败');
            return $ret;
        }catch (Exception $e) {
        	$message = date('[ c ]').' '.__METHOD__.' '.$e->getMessage()."\r\n";
        	if ($this->is_record) {
        		error_log($message, 3, $this->destination, '');
        	}else {
        		print_r($e->getMessage());
        	}
        }
    }
    
    /**
     * 检查用户在线状态，调用频率：每秒钟限 100 次
     * @param string $userId	用户 Id
     * @return mixed	status-在线状态，1为在线，0为不在线
     */
    public function userCheckOnline($userId) {
        try{
            if(empty($userId))
                throw new Exception('用户 Id 不能为空');
            $ret = $this->curl('/user/checkOnline', array('userId' => $userId));
            if(empty($ret))
                throw new Exception('请求失败');
            return $ret;
        }catch (Exception $e) {
        	$message = date('[ c ]').' '.__METHOD__.' '.$e->getMessage()."\r\n";
        	if ($this->is_record) {
        		error_log($message, 3, $this->destination, '');
        	}else {
        		print_r($e->getMessage());
        	}
        }
    }
    
    /**
     * 封禁用户，调用频率：每秒钟限 100 次
     * @param string $userId	用户 Id
     * @param integer $minute	封禁时长,单位为分钟，最大值为43200分钟
     * @return mixed
     */
    public function userBlock($userId, $minute) {
        try{
            if(empty($userId))
                throw new Exception('用户 Id 不能为空');
            if(empty($minute))
                throw new Exception('封禁时长不能为空');
            $ret = $this->curl('/user/block', array('userId' => $userId, 'minute' => $minute));
            if(empty($ret))
                throw new Exception('请求失败');
            return $ret;
        }catch (Exception $e) {
        	$message = date('[ c ]').' '.__METHOD__.' '.$e->getMessage()."\r\n";
        	if ($this->is_record) {
        		error_log($message, 3, $this->destination, '');
        	}else {
        		print_r($e->getMessage());
        	}
        }
    }
    
    /**
     * 解除用户封禁，调用频率：每秒钟限 100 次
     * @param string $userId	用户 Id
     * @return mixed
     */
    public function userUnBlock($userId) {
        try{
            if(empty($userId))
                throw new Exception('用户 Id 不能为空');
            $ret = $this->curl('/user/unblock', array('userId' => $userId));
            if(empty($ret))
                throw new Exception('请求失败');
            return $ret;
        }catch (Exception $e) {
        	$message = date('[ c ]').' '.__METHOD__.' '.$e->getMessage()."\r\n";
        	if ($this->is_record) {
        		error_log($message, 3, $this->destination, '');
        	}else {
        		print_r($e->getMessage());
        	}
        }
    }
    
    /**
     * 获取被封禁用户，调用频率：每秒钟限 100 次
     * @return mixed
     */
    public function userBlockQuery() {
        try{
            $ret = $this->curl('/user/block/query','');
            if(empty($ret))
                throw new Exception('请求失败');
            return $ret;
        }catch (Exception $e) {
        	$message = date('[ c ]').' '.__METHOD__.' '.$e->getMessage()."\r\n";
        	if ($this->is_record) {
        		error_log($message, 3, $this->destination, '');
        	}else {
        		print_r($e->getMessage());
        	}
        }
    }
    
    /**
     * 添加用户到黑名单，调用频率：每秒钟限 100 次
     * @param string $userId				用户 Id
     * @param string|array $blackUserId		被加黑的用户Id
     * @return mixed
     */
    public function userBlacklistAdd($userId, $blackUserId) {
        try{
            if(empty($userId))
                throw new Exception('用户 Id 不能为空');
            if(empty($blackUserId))
                throw new Exception('被加黑的用户 Id 不能为空');

            $params = array(
                'userId' => $userId,
                'blackUserId' => $blackUserId
            );

            $ret = $this->curl('/user/blacklist/add', $params);
            if(empty($ret))
                throw new Exception('请求失败');
            return $ret;
        }catch (Exception $e) {
        	$message = date('[ c ]').' '.__METHOD__.' '.$e->getMessage()."\r\n";
        	if ($this->is_record) {
        		error_log($message, 3, $this->destination, '');
        	}else {
        		print_r($e->getMessage());
        	}
        }
    }
    
    /**
     * 从黑名单中移除用户，调用频率：每秒钟限 100 次
     * @param string $userId			用户 Id
     * @param string|array $blackUserId	被移除的用户Id
     * @return mixed
     */
    public function userBlacklistRemove($userId, $blackUserId) {
        try{
            if(empty($userId))
                throw new Exception('用户 Id 不能为空');
            if(empty($blackUserId))
                throw new Exception('被移除的用户Id 不能为空');

            $params = array(
                'userId' => $userId,
                'blackUserId' => $blackUserId
            );

            $ret = $this->curl('/user/blacklist/remove', $params);
            if(empty($ret))
                throw new Exception('请求失败');
            return $ret;
        }catch (Exception $e) {
        	$message = date('[ c ]').' '.__METHOD__.' '.$e->getMessage()."\r\n";
        	if ($this->is_record) {
        		error_log($message, 3, $this->destination, '');
        	}else {
        		print_r($e->getMessage());
        	}
        }
    }
    
    /**
     * 获取某用户的黑名单列表，调用频率：每秒钟限 100 次
     * @param string $userId	用户 Id
     * @return mixed
     */
    public function userBlacklistQuery($userId) {
        try{
            if(empty($userId))
                throw new Exception('用户 Id 不能为空');
            $ret = $this->curl('/user/blacklist/query', array('userId' => $userId));
            if(empty($ret))
                throw new Exception('请求失败');
            return $ret;
        }catch (Exception $e) {
        	$message = date('[ c ]').' '.__METHOD__.' '.$e->getMessage()."\r\n";
        	if ($this->is_record) {
        		error_log($message, 3, $this->destination, '');
        	}else {
        		print_r($e->getMessage());
        	}
        }
    }
    
    /**
     * 发送单聊消息，说明：一个用户向另外一个用户发送消息，单条消息最大 128k。
     * 发送频率：每分钟最多发送 6000 条信息，每次发送用户上限为 1000 人，如：一次发送 1000 人时，示为 1000 条消息。
     * @param string $fromUserId		发送人用户 Id
     * @param string|array $toUserId	接收用户 Id，可以实现向多人发送消息，每次上限为 1000 人
     * @param string $objectName		消息类型，参考融云消息类型表.消息标志；可自定义消息类型
     * @param array $content			发送消息内容，参考融云消息类型表.示例说明；如果 objectName 为自定义消息类型，该参数可自定义格式	
     * @param string $pushContent		(可选)定义显示的 Push 内容，如果 objectName 为融云内置消息类型时，则发送后用户一定会收到 Push 信息。如果为自定义消息，则 pushContent 为自定义消息显示的 Push 内容，如果不传则用户不会收到 Push 通知
     * @param string $pushData			(可选)针对 iOS 平台为 Push 通知时附加到 payload 中，Android 客户端收到推送消息时对应字段名为 pushData
     * @param string $count				(可选)针对 iOS 平台，Push 时用来控制未读消息显示数，只有在 toUserId 为一个用户 Id 的时候有效
     * @param integer $verifyBlacklist 	(可选)是否过滤发送人黑名单列表，0 为不过滤、 1 为过滤，默认为 0 不过滤
     * @return json|xml
     */
    public function messagePrivatePublish($fromUserId, $toUserId, $objectName, $content, $pushContent='', $pushData='', $count=0, $verifyBlacklist=0) {
        try{
            if(empty($fromUserId))
                throw new Exception('发送人用户 Id 不能为空');
            if(empty($toUserId))
                throw new Exception('接收用户 Id 不能为空');
            if(empty($objectName))
                throw new Exception('消息类型 不能为空');
            if(empty($content))
                throw new Exception('发送消息内容 不能为空');
            if(!is_array($content))
                throw new Exception('发送消息内容 必须数组格式');
            
            if (is_array($toUserId)) {
            	$toUserId = array_values($toUserId);
            }
            $content = json_encode($content);
            $params = array(
                'fromUserId' => $fromUserId,
                'toUserId' => $toUserId,
                'objectName' => $objectName,
                'content' => $content,
                'pushContent' => $pushContent,
                'pushData' => $pushData,
                'count' => $count,
                'verifyBlacklist' => $verifyBlacklist,
            );

            $ret = $this->curl('/message/private/publish', $params);
            if(empty($ret))
                throw new Exception('请求失败');
            return $ret;
        }catch (Exception $e) {
        	$message = date('[ c ]').' '.__METHOD__.' '.$e->getMessage()."\r\n";
        	if ($this->is_record) {
        		error_log($message, 3, $this->destination, '');
        	}else {
        		print_r($e->getMessage());
        	}
        }
    }
    
    /**
     * 发送单聊模板消息，说明：一个用户向多个用户发送不同消息内容，单条消息最大 128k。
     * toUserId、values、pushContent、pushData 的数量必须相等。
     * 发送频率：每分钟最多发送 6000 条信息，每次发送用户上限为 1000 人。
     * @param string $fromUserId		发送人用户 Id
     * @param string|array $toUserId	接收用户 Id，提供多个本参数可以实现向多人发送消息，上限为 1000 人
     * @param string $objectName		消息类型，参考融云消息类型表.消息标志；可自定义消息类型
     * @param array $values				消息内容中，标识位对应内容
     * @param array $content			发送消息内容，内容中定义标识通过 values 中设置的标识位内容进行替换，参考融云消息类型表.示例说明；如果 objectName 为自定义消息类型，该参数可自定义格式	
     * @param array $pushContent		如果为自定义消息，定义显示的 Push 内容，内容中定义标识通过 values 中设置的标识位内容进行替换。如消息类型为自定义不需要 Push 通知，则对应数组传空值即可
     * @param array $pushData			针对 iOS 平台为 Push 通知时附加到 payload 中，Android 客户端收到推送消息时对应字段名为 pushData。如不需要 Push 功能对应数组传空值即可
     * @param integer $verifyBlacklist 	(可选)是否过滤发送人黑名单列表，0 为不过滤、 1 为过滤，默认为 0 不过滤
     * @return json|xml
     */
    public function messagePrivatePublishTemplate($fromUserId, $toUserId, $objectName, $values, $content, $pushContent, $pushData, $verifyBlacklist=0) {
        try{
            if(empty($fromUserId))
                throw new Exception('发送人用户 Id 不能为空');
            if(empty($toUserId))
                throw new Exception('接收用户 Id 不能为空');
            if(empty($objectName))
                throw new Exception('消息类型 不能为空');
            if(empty($values))
                throw new Exception('标识位内容 不能为空');
            if(!is_array($values))
                throw new Exception('标识位内容 必须数组格式');
            if(empty($content))
                throw new Exception('发送消息内容 不能为空');
            if(!is_array($content))
                throw new Exception('发送消息内容 必须数组格式');
            if(empty($pushContent))
                throw new Exception('Push内容 不能为空');
            if(!is_array($pushContent))
                throw new Exception('Push内容 必须数组格式');
            if(empty($pushData))
                throw new Exception('Push数据 不能为空');
            if(!is_array($pushData))
                throw new Exception('Push数据 必须数组格式');

        	if (is_array($toUserId)) {
            	$toUserId = array_values($toUserId);
            }
            $content = json_encode($content);
            $params = array(
                'fromUserId' => $fromUserId,
                'toUserId' => $toUserId,
                'objectName' => $objectName,
                'values' => $values,
                'content' => $content,
                'pushContent' => $pushContent,
                'pushData' => $pushData,
                'verifyBlacklist' => $verifyBlacklist,
            );

            $ret = $this->curl('/message/private/publish_template', $params, 'json');
            if(empty($ret))
                throw new Exception('请求失败');
            return $ret;
        }catch (Exception $e) {
        	$message = date('[ c ]').' '.__METHOD__.' '.$e->getMessage()."\r\n";
        	if ($this->is_record) {
        		error_log($message, 3, $this->destination, '');
        	}else {
        		print_r($e->getMessage());
        	}
        }
    }
    
    /**
     * 发送系统消息，说明：一个用户向一个或多个用户发送系统消息，单条消息最大 128k，会话类型为 SYSTEM。
     * 发送频率：每秒钟最多发送 100 条消息，每次最多同时向 100 人发送，如：一次发送 100 人时，示为 100 条消息。
     * @param string $fromUserId		发送人用户 Id
     * @param string|array $toUserId	接收用户Id，提供多个本参数可以实现向多用户发送系统消息，上限为 100 人
     * @param string $objectName		消息类型，参考融云消息类型表.消息标志；可自定义消息类型
     * @param array $content			发送消息内容，参考融云消息类型表.示例说明；如果 objectName 为自定义消息类型，该参数可自定义格式
     * @param string $pushContent		(可选)定义显示的 Push 内容，如果 objectName 为融云内置消息类型时，则发送后用户一定会收到 Push 信息。 如果为自定义消息，则 pushContent 为自定义消息显示的 Push 内容，如果不传则用户不会收到 Push 通知
     * @param string $pushData			(可选)针对 iOS 平台为 Push 通知时附加到 payload 中，Android 客户端收到推送消息时对应字段名为 pushData
     * @return json|xml
     */
    public function messageSystemPublish($fromUserId, $toUserId, $objectName, $content, $pushContent='', $pushData='') {
    	try{
            if(empty($fromUserId))
                throw new Exception('发送人用户 Id 不能为空');
            if(empty($toUserId))
                throw new Exception('接收用户 Id 不能为空');
            if(empty($objectName))
                throw new Exception('消息类型 不能为空');
            if(empty($content))
                throw new Exception('发送消息内容 不能为空');
            if(!is_array($content))
                throw new Exception('发送消息内容 必须数组格式');
            
            if (is_array($toUserId)) {
            	$toUserId = array_values($toUserId);
            }
            $content = json_encode($content);
            $params = array(
                'fromUserId' => $fromUserId,
                'toUserId' => $toUserId,
                'objectName' => $objectName,
                'content' => $content,
                'pushContent' => $pushContent,
                'pushData' => $pushData,
            );

            $ret = $this->curl('/message/system/publish', $params);
            if(empty($ret))
                throw new Exception('请求失败');
            return $ret;
        }catch (Exception $e) {
        	$message = date('[ c ]').' '.__METHOD__.' '.$e->getMessage()."\r\n";
        	if ($this->is_record) {
        		error_log($message, 3, $this->destination, '');
        	}else {
        		print_r($e->getMessage());
        	}
        }
    }
    
    /**
     * 发送系统模板消息，说明：一个用户向一个或多个用户发送系统消息，单条消息最大 128k，会话类型为 SYSTEM
     * 发送频率：每秒钟最多发送 100 条消息，每次最多同时向 100 人发送，如：一次发送 100 人时，示为 100 条消息
     * toUserId、values、pushContent、pushData 的数量必须相等
     * 
     * @param string $fromUserId		发送人用户 Id
     * @param string|array $toUserId	接收用户 Id，接收用户 Id，提供多个本参数可以实现向多人发送消息，上限为 100 人
     * @param string $objectName		消息类型，参考融云消息类型表.消息标志；可自定义消息类型
     * @param array $values				消息内容中，标识位对应内容
     * @param array $content			发送消息内容，内容中定义标识通过 values 中设置的标识位内容进行替换，参考融云消息类型表.示例说明；如果 objectName 为自定义消息类型，该参数可自定义格式	
     * @param array $pushContent		如果为自定义消息，定义显示的 Push 内容，内容中定义标识通过 values 中设置的标识位内容进行替换。如消息类型为自定义不需要 Push 通知，则对应数组传空值即可
     * @param array $pushData			针对 iOS 平台为 Push 通知时附加到 payload 中，Android 客户端收到推送消息时对应字段名为 pushData。如不需要 Push 功能对应数组传空值即可
     * @return json|xml
     */
    public function messageSystemPublishTemplate($fromUserId, $toUserId, $objectName, $values, $content, $pushContent='', $pushData='') {
        try{
            if(empty($fromUserId))
                throw new Exception('发送人用户 Id 不能为空');
            if(empty($toUserId))
                throw new Exception('接收用户 Id 不能为空');
            if(empty($objectName))
                throw new Exception('消息类型 不能为空');
            if(empty($values))
                throw new Exception('标识位内容 不能为空');
            if(!is_array($values))
                throw new Exception('标识位内容 必须数组格式');
            if(empty($content))
                throw new Exception('发送消息内容 不能为空');
            if(!is_array($content))
                throw new Exception('发送消息内容 必须数组格式');
            
            if (is_array($toUserId)) {
            	$toUserId = array_values($toUserId);
            }
            $content = json_encode($content);
            $params = array(
                'fromUserId' => $fromUserId,
                'toUserId' => $toUserId,
                'objectName' => $objectName,
                'values' => $values,
                'content' => $content,
                'pushContent' => $pushContent,
                'pushData' => $pushData,
            );

            $ret = $this->curl('/message/system/publish_template', $params, 'json');
            if(empty($ret))
                throw new Exception('请求失败');
            return $ret;
        }catch (Exception $e) {
        	$message = date('[ c ]').' '.__METHOD__.' '.$e->getMessage()."\r\n";
        	if ($this->is_record) {
        		error_log($message, 3, $this->destination, '');
        	}else {
        		print_r($e->getMessage());
        	}
        }
    }
    
    /**
     * 发送群组消息，说明：以一个用户身份向群组发送消息，单条消息最大 128k。
     * 发送频率：每秒钟最多发送 20 条消息，每次最多向 3 个群组发送，如：一次向 3 个群组发送消息，示为 3 条消息。
     * @param string $fromUserId		发送人用户 Id	
     * @param string|array $toGroupId	接收群Id，提供多个本参数可以实现向多群发送消息，最多不超过 3 个群组
     * @param string $objectName		消息类型，参考融云消息类型表.消息标志；可自定义消息类型
     * @param array $content			发送消息内容，参考融云消息类型表.示例说明；如果 objectName 为自定义消息类型，该参数可自定义格式
     * @param string $pushContent		定义显示的 Push 内容，如果 objectName 为融云内置消息类型时，则发送后用户一定会收到 Push 信息。 如果为自定义消息，则 pushContent 为自定义消息显示的 Push 内容，如果不传则用户不会收到 Push 通知。(可选)
     * @param string $pushData			针对 iOS 平台为 Push 通知时附加到 payload 中，Android 客户端收到推送消息时对应字段名为 pushData。(可选)
     * @return json|xml
     */
    public function messageGroupPublish($fromUserId, $toGroupId, $objectName, $content, $pushContent='', $pushData='') {
    	try{
            if(empty($fromUserId))
                throw new Exception('发送人用户 Id 不能为空');
            if(empty($toGroupId))
                throw new Exception('接收群Id 不能为空');
            if(empty($objectName))
                throw new Exception('消息类型 不能为空');
            if(empty($content))
                throw new Exception('发送消息内容 不能为空');
            if(!is_array($content))
                throw new Exception('发送消息内容 必须数组格式');
                
            if (is_array($toGroupId)) {
            	$toGroupId = array_values($toGroupId);
            }
            $content = json_encode($content);
            $params = array(
                'fromUserId' => $fromUserId,
                'toGroupId' => $toGroupId,
                'objectName' => $objectName,
                'content' => $content,
                'pushContent' => $pushContent,
                'pushData' => $pushData,
            );

            $ret = $this->curl('/message/group/publish', $params);
            if(empty($ret))
                throw new Exception('请求失败');
            return $ret;
        }catch (Exception $e) {
        	$message = date('[ c ]').' '.__METHOD__.' '.$e->getMessage()."\r\n";
        	if ($this->is_record) {
        		error_log($message, 3, $this->destination, '');
        	}else {
        		print_r($e->getMessage());
        	}
        }
    }
    
    /**
     * 发送讨论组消息，说明：以一个用户身份向讨论组发送消息，单条消息最大 128k。
     * @param string $fromUserId		发送人用户 Id
     * @param string $toDiscussionId	接收讨论组 Id
     * @param string $objectName		消息类型，参考融云消息类型表.消息标志；可自定义消息类型
     * @param array $content			发送消息内容，参考融云消息类型表.示例说明；如果 objectName 为自定义消息类型，该参数可自定义格式
     * @param string $pushContent		定义显示的 Push 内容，如果 objectName 为融云内置消息类型时，则发送后用户一定会收到 Push 信息。 如果为自定义消息，则 pushContent 为自定义消息显示的 Push 内容，如果不传则用户不会收到 Push 通知。(可选)
     * @param string $pushData			针对 iOS 平台为 Push 通知时附加到 payload 中，Android 客户端收到推送消息时对应字段名为 pushData。(可选)
     * @return json|xml
     */
    public function messageDiscussionPublish($fromUserId, $toDiscussionId, $objectName, $content, $pushContent='', $pushData='') {
    	try{
            if(empty($fromUserId))
                throw new Exception('发送人用户 Id 不能为空');
            if(empty($toDiscussionId))
                throw new Exception('接收讨论组 Id 不能为空');
            if(empty($objectName))
                throw new Exception('消息类型 不能为空');
            if(empty($content))
                throw new Exception('发送消息内容 不能为空');
            if(!is_array($content))
                throw new Exception('发送消息内容 必须数组格式');

            $content = json_encode($content);
            $params = array(
                'fromUserId' => $fromUserId,
                'toDiscussionId' => $toDiscussionId,
                'objectName' => $objectName,
                'content' => $content,
                'pushContent' => $pushContent,
                'pushData' => $pushData,
            );

            $ret = $this->curl('/message/discussion/publish', $params);
            if(empty($ret))
                throw new Exception('请求失败');
            return $ret;
        }catch (Exception $e) {
        	$message = date('[ c ]').' '.__METHOD__.' '.$e->getMessage()."\r\n";
        	if ($this->is_record) {
        		error_log($message, 3, $this->destination, '');
        	}else {
        		print_r($e->getMessage());
        	}
        }
    }
    
    /**
     * 发送聊天室消息，说明：一个用户向聊天室发送消息，单条消息最大 128k。
     * 调用频率：每秒钟限 100 次
     * @param string $fromUserId			发送人用户 Id
     * @param string|array $toChatroomId	接收聊天室Id，提供多个本参数可以实现向多个聊天室发送消息
     * @param string $objectName			消息类型，参考融云消息类型表.消息标志；可自定义消息类型
     * @param array $content				发送消息内容，参考融云消息类型表.示例说明；如果 objectName 为自定义消息类型，该参数可自定义格式
     * @return json|xml
     */
    public function messageChatroomPublish($fromUserId, $toChatroomId, $objectName, $content) {
        try{
            if(empty($fromUserId))
                throw new Exception('发送人用户 Id 不能为空');
            if(empty($toChatroomId))
                throw new Exception('接收聊天室Id 不能为空');
            if(empty($objectName))
                throw new Exception('消息类型 不能为空');
            if(empty($content))
                throw new Exception('发送消息内容 不能为空');
            if(!is_array($content))
                throw new Exception('发送消息内容 必须数组格式');

            if (is_array($toChatroomId)) {
            	$toChatroomId = array_values($toChatroomId);
            }
            $content = json_encode($content);
            $params = array(
                'fromUserId' => $fromUserId,
                'toChatroomId' => $toChatroomId,
                'objectName' => $objectName,
                'content' => $content
            );

            $ret = $this->curl('/message/chatroom/publish', $params);
            if(empty($ret))
                throw new Exception('请求失败');
            return $ret;
        }catch (Exception $e) {
        	$message = date('[ c ]').' '.__METHOD__.' '.$e->getMessage()."\r\n";
        	if ($this->is_record) {
        		error_log($message, 3, $this->destination, '');
        	}else {
        		print_r($e->getMessage());
        	}
        }
    }
    
    /**
     * 发送广播消息，说明：发送消息给一个应用下的所有注册用户，如用户未在线会对满足条件（绑定手机终端）的用户发送 Push 信息，单条消息最大 128k，会话类型为 SYSTEM。
     * 调用频率：每小时只能发送 1 次，每天最多发送 3 次。
     * @param string $fromUserId	发送人用户 Id
     * @param string $objectName	消息类型，参考融云消息类型表.消息标志；可自定义消息类型
     * @param array $content		发送消息内容，参考融云消息类型表.示例说明；如果 objectName 为自定义消息类型，该参数可自定义格式
     * @param string $pushContent	定义显示的 Push 内容，如果 objectName 为融云内置消息类型时，则发送后用户一定会收到 Push 信息。 如果为自定义消息，则 pushContent 为自定义消息显示的 Push 内容，如果不传则用户不会收到 Push 通知。(可选)
     * @param string $pushData		针对 iOS 平台为 Push 通知时附加到 payload 中，Android 客户端收到推送消息时对应字段名为 pushData。(可选)
     * @param string $os			针对操作系统发送 Push，值为 iOS 表示对 iOS 手机用户发送 Push ,为 Android 时表示对 Android 手机用户发送 Push ，如对所有用户发送 Push 信息，则不需要传 os 参数。(可选)
     * @return json|xml
     */
    public function messageBroadcast($fromUserId, $objectName, $content, $pushContent='', $pushData='', $os='') {
    	try{
            if(empty($fromUserId))
                throw new Exception('发送人用户 Id 不能为空');
            if(empty($objectName))
                throw new Exception('消息类型 不能为空');
            if(empty($content))
                throw new Exception('发送消息内容 不能为空');
            if(!is_array($content))
                throw new Exception('发送消息内容 必须数组格式');

            $content = json_encode($content);
            $params = array(
                'fromUserId' => $fromUserId,
                'objectName' => $objectName,
                'content' => $content,
                'pushContent' => $pushContent,
                'pushData' => $pushData,
                'os' => $os,
            );

            $ret = $this->curl('/message/broadcast', $params);
            if(empty($ret))
                throw new Exception('请求失败');
            return $ret;
        }catch (Exception $e) {
        	$message = date('[ c ]').' '.__METHOD__.' '.$e->getMessage()."\r\n";
        	if ($this->is_record) {
        		error_log($message, 3, $this->destination, '');
        	}else {
        		print_r($e->getMessage());
        	}
        }
    }
    
    /**
     * 添加敏感词
     * @param string $word		敏感词，最长不超过 32 个字符
     * @return json|xml
     */
    public function wordfilterAdd($word) {
    	try{
            if(empty($word))
                throw new Exception('敏感词 不能为空');

            $params = array(
                'word' => $word
            );

            $ret = $this->curl('/wordfilter/add', $params);
            if(empty($ret))
                throw new Exception('请求失败');
            return $ret;
        }catch (Exception $e) {
        	$message = date('[ c ]').' '.__METHOD__.' '.$e->getMessage()."\r\n";
        	if ($this->is_record) {
        		error_log($message, 3, $this->destination, '');
        	}else {
        		print_r($e->getMessage());
        	}
        }
    }
    
    /**
     * 移除敏感词，说明：从敏感词列表中，移除某一敏感词。
     * @param string $word		敏感词，最长不超过 32 个字符
     * @return json|xml
     */
    public function wordfilterDelete($word) {
    	try{
            if(empty($word))
                throw new Exception('敏感词 不能为空');

            $params = array(
                'word' => $word
            );

            $ret = $this->curl('/wordfilter/delete', $params);
            if(empty($ret))
                throw new Exception('请求失败');
            return $ret;
        }catch (Exception $e) {
        	$message = date('[ c ]').' '.__METHOD__.' '.$e->getMessage()."\r\n";
        	if ($this->is_record) {
        		error_log($message, 3, $this->destination, '');
        	}else {
        		print_r($e->getMessage());
        	}
        }
    }
    
    /**
     * 查询敏感词列表
     * @return json|xml
     */
    public function wordfilterList() {
        try{
            $ret = $this->curl('/wordfilter/list', array());
            if(empty($ret))
                throw new Exception('请求失败');
            return $ret;
        }catch (Exception $e) {
        	$message = date('[ c ]').' '.__METHOD__.' '.$e->getMessage()."\r\n";
        	if ($this->is_record) {
        		error_log($message, 3, $this->destination, '');
        	}else {
        		print_r($e->getMessage());
        	}
        }
    }
    
    /**
     * 消息历史记录下载地址获取方法
     * 说明：获取 APP 内指定某天某小时内的所有会话消息记录的下载地址（目前支持二人会话、讨论组、群组、聊天室、客服、系统通知消息历史记录下载）
     * @param string $date		指定北京时间某天某小时，格式为2014010101,表示：2014年1月1日凌晨1点
     * @return json|xml
     */
    public function messageHistory($date) {
    	try {
            if(empty($date))
                throw new Exception('时间 不能为空');

            $params = array(
                'date' => $date
            );

            $ret = $this->curl('/message/history', $params);
            if(empty($ret))
                throw new Exception('请求失败');
            return $ret;
    	}catch (Exception $e) {
        	$message = date('[ c ]').' '.__METHOD__.' '.$e->getMessage()."\r\n";
        	if ($this->is_record) {
        		error_log($message, 3, $this->destination, '');
        	}else {
        		print_r($e->getMessage());
        	}
    	}
    }
    
    /**
     * 消息历史记录删除
     * 说明：删除 APP 内指定某天某小时内的所有会话消息记录
     * 调用该接口返回成功后，date参数指定的某小时的消息记录文件将在随后的5-10分钟内被永久删除。
     * @param string $date		指定北京时间某天某小时，格式为2014010101,表示：2014年1月1日凌晨1点
     * @return json|xml
     */
    public function messageHistoryDelete($date) {
    	try {
            if(empty($date))
                throw new Exception('时间 不能为空');

            $params = array(
                'date' => $date
            );

            $ret = $this->curl('/message/history/delete', $params);
            if(empty($ret))
                throw new Exception('请求失败');
            return $ret;
    	}catch (Exception $e) {
        	$message = date('[ c ]').' '.__METHOD__.' '.$e->getMessage()."\r\n";
        	if ($this->is_record) {
        		error_log($message, 3, $this->destination, '');
        	}else {
        		print_r($e->getMessage());
        	}
    	}
    }
    
    /**
     * 同步用户所属群组
     * 当第一次连接融云服务器时，需要向融云服务器提交 userId 对应的用户当前所加入的所有群组，此接口主要为防止应用中用户群信息同融云已知的用户所属群信息不同步。
     * 调用频率：每秒钟限 100 次
     * @param string $userId	被同步群信息的用户 Id
     * @param array $data		该用户的群信息，如群 Id 已经存在，则不会刷新对应群组名称，如果想刷新群组名称请调用刷新群组信息方法
     * 当不提交group[id]=name参数时，表示删除userId对应的群信息；此参数可传多个
     * @return json|xml
     */
    public function groupSync($userId, $data=array()) {
    	try{
            if(empty($userId))
                throw new Exception('被同步群信息的用户 Id 不能为空');
            if(empty($data))
                throw new Exception('该用户的群信息 不能为空');

            $params = array(
                'userId' => $userId
            );
            foreach ($data as $key=>$value) {
            	$params['group['.$key.']'] = $value;
            }

            $ret = $this->curl('/group/sync', $params);
            if(empty($ret))
                throw new Exception('请求失败');
            return $ret;
        }catch (Exception $e) {
        	$message = date('[ c ]').' '.__METHOD__.' '.$e->getMessage()."\r\n";
        	if ($this->is_record) {
        		error_log($message, 3, $this->destination, '');
        	}else {
        		print_r($e->getMessage());
        	}
        }
    }
    
    /**
     * 创建群组
     * 创建群组，并将用户加入该群组，用户将可以收到该群的消息，同一用户最多可加入 500 个群，每个群最大至 3000 人，App 内的群组数量没有限制。注：其实本方法是加入群组方法 /group/join 的别名。
     * @param string|array $userId		要加入群的用户 Id，当提交多个userId参数时，表示创建群组，并将多个用户加入该群组，用户将可以收到该群的消息
     * @param string $groupId			创建群组 Id
     * @param string $groupName			群组 Id 对应的名称
     * @return json|xml
     */
    public function groupCreate($userId, $groupId, $groupName) {
    	try{
            if(empty($userId))
                throw new Exception('要加入群的用户 Id 不能为空');
            if(empty($groupId))
                throw new Exception('创建群组 Id 不能为空');
            if(empty($groupName))
                throw new Exception('群组 Id 对应的名称 不能为空');
    	
            if (is_array($userId)) {
            	$userId = array_values($userId);
            }
            $params = array(
                'userId' => $userId,
                'groupId' => $groupId,
                'groupName' => $groupName
            );

            $ret = $this->curl('/group/create', $params);
            if(empty($ret))
                throw new Exception('请求失败');
            return $ret;
        }catch (Exception $e) {
        	$message = date('[ c ]').' '.__METHOD__.' '.$e->getMessage()."\r\n";
        	if ($this->is_record) {
        		error_log($message, 3, $this->destination, '');
        	}else {
        		print_r($e->getMessage());
        	}
        }
    }
    
    /**
     * 加入群组
     * 将用户加入指定群组，用户将可以收到该群的消息，同一用户最多可加入 500 个群，每个群最大至 3000 人。
     * @param string|array $userId		要加入群的用户 Id，可提交多个，最多不超过 1000 个，当提交多个userId参数时，表示将多个用户加入指定群组，用户将可以收到该群的消息，建议每次同时加入最多不超过 3 个群组
     * @param string $groupId			要加入的群 Id
     * @param string $groupName			要加入的群 Id 对应的名称
     * @return json|xml
     */
    public function groupJoin($userId, $groupId, $groupName) {
    	try{
            if(empty($userId))
                throw new Exception('要加入群的用户 Id 不能为空');
            if(empty($groupId))
                throw new Exception('要加入的群 Id 不能为空');
            if(empty($groupName))
                throw new Exception('要加入的群 Id 对应的名称 不能为空');

            if (is_array($userId)) {
            	$userId = array_values($userId);
            }
            $params = array(
                'userId' => $userId,
                'groupId' => $groupId,
                'groupName' => $groupName
            );

            $ret = $this->curl('/group/join', $params);
            if(empty($ret))
                throw new Exception('请求失败');
            return $ret;
        }catch (Exception $e) {
        	$message = date('[ c ]').' '.__METHOD__.' '.$e->getMessage()."\r\n";
        	if ($this->is_record) {
        		error_log($message, 3, $this->destination, '');
        	}else {
        		print_r($e->getMessage());
        	}
        }
    }
    
    /**
     * 退出群组
     * 将用户从群中移除，不再接收该群组的消息。
     * @param string|array $userId		要退出群的用户 Id，当提交多个userId参数时，表示将多个用户从群中移除，不再接收该群组的消息
     * @param string $groupId			要退出的群 Id
     * @return json|xml
     */
    public function groupQuit($userId, $groupId) {
    	try{
            if(empty($userId))
                throw new Exception('要退出群的用户 Id 不能为空');
            if(empty($groupId))
                throw new Exception('要退出的群 Id 不能为空');

            if (is_array($userId)) {
            	$userId = array_values($userId);
            }
            $params = array(
                'userId' => $userId,
                'groupId' => $groupId
            );

            $ret = $this->curl('/group/quit', $params);
            if(empty($ret))
                throw new Exception('请求失败');
            return $ret;
        }catch (Exception $e) {
        	$message = date('[ c ]').' '.__METHOD__.' '.$e->getMessage()."\r\n";
        	if ($this->is_record) {
        		error_log($message, 3, $this->destination, '');
        	}else {
        		print_r($e->getMessage());
        	}
        }
    }
    
    /**
     * 解散群组
     * 将该群解散，所有用户都无法再接收该群的消息。
     * @param string $userId		操作解散群的用户 Id
     * @param string $groupId		要解散的群 Id
     * @return json|xml
     */
    public function groupDismiss($userId, $groupId) {
    	try{
            if(empty($userId))
                throw new Exception('操作解散群的用户 Id 不能为空');
            if(empty($groupId))
                throw new Exception('要解散的群 Id 不能为空');

            $params = array(
                'userId' => $userId,
                'groupId' => $groupId
            );

            $ret = $this->curl('/group/dismiss', $params);
            if(empty($ret))
                throw new Exception('请求失败');
            return $ret;
        }catch (Exception $e) {
        	$message = date('[ c ]').' '.__METHOD__.' '.$e->getMessage()."\r\n";
        	if ($this->is_record) {
        		error_log($message, 3, $this->destination, '');
        	}else {
        		print_r($e->getMessage());
        	}
        }
    }
    
    /**
     * 刷新群组信息
     * @param string $groupId		群 Id
     * @param string $groupName		群名称
     * @return json|xml
     */
    public function groupRefresh($groupId, $groupName) {
    	try{
            if(empty($groupId))
                throw new Exception('群 Id 不能为空');
            if(empty($groupName))
                throw new Exception('群名称 不能为空');

            $params = array(
                'groupId' => $groupId,
                'groupName' => $groupName
            );

            $ret = $this->curl('/group/refresh', $params);
            if(empty($ret))
                throw new Exception('请求失败');
            return $ret;
        }catch (Exception $e) {
        	$message = date('[ c ]').' '.__METHOD__.' '.$e->getMessage()."\r\n";
        	if ($this->is_record) {
        		error_log($message, 3, $this->destination, '');
        	}else {
        		print_r($e->getMessage());
        	}
        }
    }
    
    /**
     * 查询群成员
     * @param string $groupId		群 Id
     * @return json|xml
     */
    public function groupUserQuery($groupId) {
    	try{
            if(empty($groupId))
                throw new Exception('群 Id 不能为空');

            $params = array(
                'groupId' => $groupId
            );

            $ret = $this->curl('/group/user/query', $params);
            if(empty($ret))
                throw new Exception('请求失败');
            return $ret;
        }catch (Exception $e) {
        	$message = date('[ c ]').' '.__METHOD__.' '.$e->getMessage()."\r\n";
        	if ($this->is_record) {
        		error_log($message, 3, $this->destination, '');
        	}else {
        		print_r($e->getMessage());
        	}
        }
    }
    
    /**
     * 添加禁言群成员
     * 在 App 中如果不想让某一用户在群中发言时，可将此用户在群组中禁言，被禁言用户可以接收查看群组中用户聊天信息，但不能发送消息。
     * @param string|array $userId		用户 Id，当提交多个 userId参数时，表示将群组中多个用户禁言
     * @param string $groupId			群组 Id
     * @param integer $minute			禁言时长，以分钟为单位，最大值为43200分钟
     * @return json|xml
     */
    public function groupUserGagAdd($userId, $groupId, $minute) {
    	try{
            if(empty($userId))
                throw new Exception('用户 Id 不能为空');
            if(empty($groupId))
                throw new Exception('群组 Id 不能为空');
            if(empty($minute))
                throw new Exception('禁言时长 不能为空');

            if (is_array($userId)) {
            	$userId = array_values($userId);
            }
            $params = array(
                'userId' => $userId,
                'groupId' => $groupId,
                'minute' => $minute
            );

            $ret = $this->curl('/group/user/gag/add', $params);
            if(empty($ret))
                throw new Exception('请求失败');
            return $ret;
        }catch (Exception $e) {
        	$message = date('[ c ]').' '.__METHOD__.' '.$e->getMessage()."\r\n";
        	if ($this->is_record) {
        		error_log($message, 3, $this->destination, '');
        	}else {
        		print_r($e->getMessage());
        	}
        }
    }
    
    /**
     * 移除禁言群成员
     * @param string|array $userId		用户 Id，当提交多个 userId参数时，表示将群组中多个用户解禁
     * @param string $groupId			群组 Id
     * @return json|xml
     */
    public function groupUserGagRollback($userId, $groupId) {
    	try{
            if(empty($userId))
                throw new Exception('用户 Id 不能为空');
            if(empty($groupId))
                throw new Exception('群组 Id 不能为空');

            if (is_array($userId)) {
            	$userId = array_values($userId);
            }
            $params = array(
                'userId' => $userId,
                'groupId' => $groupId
            );

            $ret = $this->curl('/group/user/gag/rollback', $params);
            if(empty($ret))
                throw new Exception('请求失败');
            return $ret;
        }catch (Exception $e) {
        	$message = date('[ c ]').' '.__METHOD__.' '.$e->getMessage()."\r\n";
        	if ($this->is_record) {
        		error_log($message, 3, $this->destination, '');
        	}else {
        		print_r($e->getMessage());
        	}
        }
    }
    
    /**
     * 查询被禁言群成员
     * @param string $groupId		群组 Id
     * @return json|xml
     */
    public function groupUserGagList($groupId) {
    	try{
            if(empty($groupId))
                throw new Exception('群组 Id 不能为空');

            $params = array(
                'groupId' => $groupId
            );

            $ret = $this->curl('/group/user/gag/list', $params);
            if(empty($ret))
                throw new Exception('请求失败');
            return $ret;
        }catch (Exception $e) {
        	$message = date('[ c ]').' '.__METHOD__.' '.$e->getMessage()."\r\n";
        	if ($this->is_record) {
        		error_log($message, 3, $this->destination, '');
        	}else {
        		print_r($e->getMessage());
        	}
        }
    }
    
    /**
     * 创建聊天室
     * 当提交参数chatroom[id]=name多个时表示创建多个聊天室
     * @param array $data		chatroom[id]=name id:要创建的聊天室的id；name:要创建的聊天室的name
     * @return json|xml
     */
    public function chatroomCreate($data=array()) {
    	try{
            if(empty($data))
                throw new Exception('聊天室信息 不能为空');

            $params = array();
            foreach ($data as $key=>$value) {
            	$params['chatroom['.$key.']'] = $value;
            }

            $ret = $this->curl('/chatroom/create', $params);
            if(empty($ret))
                throw new Exception('请求失败');
            return $ret;
        }catch (Exception $e) {
        	$message = date('[ c ]').' '.__METHOD__.' '.$e->getMessage()."\r\n";
        	if ($this->is_record) {
        		error_log($message, 3, $this->destination, '');
        	}else {
        		print_r($e->getMessage());
        	}
        }
    }
    
    /**
     * 加入聊天室
     * 将用户加入指定聊天室，用户将可以收到该聊天室的消息。
     * @param string|array $userId		要加入聊天室的用户 Id，可提交多个，最多不超过 50 个
     * @param string $chatroomId		要加入的聊天室 Id
     * @return json|xml
     */
    public function chatroomJoin($userId, $chatroomId) {
    	try{
            if(empty($userId))
                throw new Exception('要加入聊天室的用户 Id 不能为空');
            if(empty($chatroomId))
                throw new Exception('要加入的聊天室 Id 不能为空');

            if (is_array($userId)) {
            	$userId = array_values($userId);
            }
            $params = array(
                'userId' => $userId,
                'chatroomId' => $chatroomId
            );

            $ret = $this->curl('/chatroom/join', $params);
            if(empty($ret))
                throw new Exception('请求失败');
            return $ret;
        }catch (Exception $e) {
        	$message = date('[ c ]').' '.__METHOD__.' '.$e->getMessage()."\r\n";
        	if ($this->is_record) {
        		error_log($message, 3, $this->destination, '');
        	}else {
        		print_r($e->getMessage());
        	}
        }
    }
    
    /**
     * 销毁聊天室
     * @param string|array $chatroomId		要销毁的聊天室 Id，当提交参数 chatroomId 多个时表示销毁多个聊天室
     * @return json|xml
     */
    public function chatroomDestroy($chatroomId) {
    	try{
            if(empty($chatroomId))
                throw new Exception('要销毁的聊天室 Id 不能为空');

            $params = array(
                'chatroomId' => $chatroomId
            );

            $ret = $this->curl('/chatroom/destroy', $params);
            if(empty($ret))
                throw new Exception('请求失败');
            return $ret;
        }catch (Exception $e) {
        	$message = date('[ c ]').' '.__METHOD__.' '.$e->getMessage()."\r\n";
        	if ($this->is_record) {
        		error_log($message, 3, $this->destination, '');
        	}else {
        		print_r($e->getMessage());
        	}
        }
    }
    
    /**
     * 查询聊天室信息
     * @param string|array $chatroomId		要查询的聊天室Id
     * @return json|xml
     */
    public function chatroomQuery($chatroomId) {
    	try{
            if(empty($chatroomId))
                throw new Exception('要查询的聊天室Id 不能为空');

            $params = array(
                'chatroomId' => $chatroomId
            );

            $ret = $this->curl('/chatroom/query', $params);
            if(empty($ret))
                throw new Exception('请求失败');
            return $ret;
        }catch (Exception $e) {
        	$message = date('[ c ]').' '.__METHOD__.' '.$e->getMessage()."\r\n";
        	if ($this->is_record) {
        		error_log($message, 3, $this->destination, '');
        	}else {
        		print_r($e->getMessage());
        	}
        }
    }
    
    /**
     * 查询聊天室内用户
     * @param string $chatroomId		要查询的聊天室 Id
     * @param integer $count			要获取的聊天室成员数，上限为 500 ，超过 500 时最多返回 500 个成员
     * @param integer $order			加入聊天室的先后顺序， 1 为加入时间正序， 2 为加入时间倒序
     * @return json|xml
     */
    public function chatroomUserQuery($chatroomId, $count=500, $order=1) {
    	try{
            if(empty($chatroomId))
                throw new Exception('要查询的聊天室 Id 不能为空');
            if(empty($count))
                throw new Exception('要获取的聊天室成员数 不能为空');
            if(empty($order))
                throw new Exception('加入聊天室的先后顺序 不能为空');

            $params = array(
                'chatroomId' => $chatroomId,
                'count' => $count,
                'order' => $order
            );

            $ret = $this->curl('/chatroom/user/query', $params);
            if(empty($ret))
                throw new Exception('请求失败');
            return $ret;
        }catch (Exception $e) {
        	$message = date('[ c ]').' '.__METHOD__.' '.$e->getMessage()."\r\n";
        	if ($this->is_record) {
        		error_log($message, 3, $this->destination, '');
        	}else {
        		print_r($e->getMessage());
        	}
        }
    }
    
    /**
     * 添加禁言聊天室成员
     * @param string $userId		用户 Id
     * @param string $chatroomId	聊天室 Id
     * @param integer $minute		禁言时长，以分钟为单位，最大值为43200分钟
     * @return json|xml
     */
    public function chatroomUserGagAdd($userId, $chatroomId, $minute) {
    	try{
            if(empty($userId))
                throw new Exception('用户 Id 不能为空');
            if(empty($chatroomId))
                throw new Exception('聊天室 Id 不能为空');
            if(empty($minute))
                throw new Exception('禁言时长 不能为空');

            $params = array(
                'userId' => $userId,
                'chatroomId' => $chatroomId,
                'minute' => $minute
            );

            $ret = $this->curl('/chatroom/user/gag/add', $params);
            if(empty($ret))
                throw new Exception('请求失败');
            return $ret;
        }catch (Exception $e) {
        	$message = date('[ c ]').' '.__METHOD__.' '.$e->getMessage()."\r\n";
        	if ($this->is_record) {
        		error_log($message, 3, $this->destination, '');
        	}else {
        		print_r($e->getMessage());
        	}
        }
    }
    
    /**
     * 移除禁言聊天室成员
     * @param string $userId		用户 Id
     * @param string $chatroomId	聊天室 Id
     * @return json|xml
     */
    public function chatroomUserGagRollback($userId, $chatroomId) {
    	try{
            if(empty($userId))
                throw new Exception('用户 Id 不能为空');
            if(empty($chatroomId))
                throw new Exception('聊天室 Id 不能为空');

            $params = array(
                'userId' => $userId,
                'chatroomId' => $chatroomId
            );

            $ret = $this->curl('/chatroom/user/gag/rollback', $params);
            if(empty($ret))
                throw new Exception('请求失败');
            return $ret;
        }catch (Exception $e) {
        	$message = date('[ c ]').' '.__METHOD__.' '.$e->getMessage()."\r\n";
        	if ($this->is_record) {
        		error_log($message, 3, $this->destination, '');
        	}else {
        		print_r($e->getMessage());
        	}
        }
    }
    
    /**
     * 查询被禁言聊天室成员
     * @param string $chatroomId	聊天室 Id
     * @return json|xml
     */
    public function chatroomUserGagList($chatroomId) {
    	try{
            if(empty($chatroomId))
                throw new Exception('聊天室 Id 不能为空');

            $params = array(
                'chatroomId' => $chatroomId
            );

            $ret = $this->curl('/chatroom/user/gag/list', $params);
            if(empty($ret))
                throw new Exception('请求失败');
            return $ret;
        }catch (Exception $e) {
        	$message = date('[ c ]').' '.__METHOD__.' '.$e->getMessage()."\r\n";
        	if ($this->is_record) {
        		error_log($message, 3, $this->destination, '');
        	}else {
        		print_r($e->getMessage());
        	}
        }
    }
    
    /**
     * 添加封禁聊天室成员
     * @param string $userId		用户 Id
     * @param string $chatroomId	聊天室 Id
     * @param integer $minute		禁言时长，以分钟为单位，最大值为43200分钟
     * @return json|xml
     */
    public function chatroomUserBlockAdd($userId, $chatroomId, $minute) {
    	try{
            if(empty($userId))
                throw new Exception('用户 Id 不能为空');
            if(empty($chatroomId))
                throw new Exception('聊天室 Id 不能为空');
            if(empty($minute))
                throw new Exception('禁言时长 不能为空');

            $params = array(
                'userId' => $userId,
                'chatroomId' => $chatroomId,
                'minute' => $minute
            );

            $ret = $this->curl('/chatroom/user/block/add', $params);
            if(empty($ret))
                throw new Exception('请求失败');
            return $ret;
        }catch (Exception $e) {
        	$message = date('[ c ]').' '.__METHOD__.' '.$e->getMessage()."\r\n";
        	if ($this->is_record) {
        		error_log($message, 3, $this->destination, '');
        	}else {
        		print_r($e->getMessage());
        	}
        }
    }
    
    /**
     * 移除封禁聊天室成员
     * @param string $userId		用户 Id
     * @param string $chatroomId	聊天室 Id
     * @return json|xml
     */
    public function chatroomUserBlockRollback($userId, $chatroomId) {
    	try{
            if(empty($userId))
                throw new Exception('用户 Id 不能为空');
            if(empty($chatroomId))
                throw new Exception('聊天室 Id 不能为空');

            $params = array(
                'userId' => $userId,
                'chatroomId' => $chatroomId
            );

            $ret = $this->curl('/chatroom/user/block/rollback', $params);
            if(empty($ret))
                throw new Exception('请求失败');
            return $ret;
        }catch (Exception $e) {
        	$message = date('[ c ]').' '.__METHOD__.' '.$e->getMessage()."\r\n";
        	if ($this->is_record) {
        		error_log($message, 3, $this->destination, '');
        	}else {
        		print_r($e->getMessage());
        	}
        }
    }
    
    /**
     * 查询被封禁聊天室成员
     * @param string $chatroomId	聊天室 Id
     * @return json|xml
     */
    public function chatroomUserBlockList($chatroomId) {
    	try{
            if(empty($chatroomId))
                throw new Exception('聊天室 Id 不能为空');

            $params = array(
                'chatroomId' => $chatroomId
            );

            $ret = $this->curl('/chatroom/user/block/list', $params);
            if(empty($ret))
                throw new Exception('请求失败');
            return $ret;
        }catch (Exception $e) {
        	$message = date('[ c ]').' '.__METHOD__.' '.$e->getMessage()."\r\n";
        	if ($this->is_record) {
        		error_log($message, 3, $this->destination, '');
        	}else {
        		print_r($e->getMessage());
        	}
        }
    }
    
    /**
     * 聊天室消息停止分发
     * 停止分发后聊天室中用户发送的消息，融云服务端不会再将消息发送给聊天室中其他用户。
     * @param string $chatroomId	聊天室 Id
     * @return json|xml
     */
    public function chatroomMessageStopDistribution($chatroomId) {
    	try{
            if(empty($chatroomId))
                throw new Exception('聊天室 Id 不能为空');

            $params = array(
                'chatroomId' => $chatroomId
            );

            $ret = $this->curl('/chatroom/message/stopDistribution', $params);
            if(empty($ret))
                throw new Exception('请求失败');
            return $ret;
        }catch (Exception $e) {
        	$message = date('[ c ]').' '.__METHOD__.' '.$e->getMessage()."\r\n";
        	if ($this->is_record) {
        		error_log($message, 3, $this->destination, '');
        	}else {
        		print_r($e->getMessage());
        	}
        }
    }
    
    /**
     * 聊天室消息恢复分发
     * @param string $chatroomId	聊天室 Id
     * @return json|xml
     */
    public function chatroomMessageResumeDistribution($chatroomId) {
    	try{
            if(empty($chatroomId))
                throw new Exception('聊天室 Id 不能为空');

            $params = array(
                'chatroomId' => $chatroomId
            );

            $ret = $this->curl('/chatroom/message/resumeDistribution', $params);
            if(empty($ret))
                throw new Exception('请求失败');
            return $ret;
        }catch (Exception $e) {
        	$message = date('[ c ]').' '.__METHOD__.' '.$e->getMessage()."\r\n";
        	if ($this->is_record) {
        		error_log($message, 3, $this->destination, '');
        	}else {
        		print_r($e->getMessage());
        	}
        }
    }
    
    /**
     * 添加标签
     * 为应用中的用户添加标签，如果某用户已经添加了标签，再次对用户添加标签时将覆盖之前设置的标签内容。
     * @param string $userId	用户 Id
     * @param array $tags		用户标签，一个用户最多添加 20 个标签，每个 tags 最大不能超过 40 个字节，标签中不能包含特殊字符。
     * @return json|xml
     */
    public function userTagSet($userId, $tags=array()) {
    	try{
            if(empty($userId))
                throw new Exception('用户 Id 不能为空');
            if(empty($tags))
                throw new Exception('用户标签 不能为空');

            if (is_array($tags)) {
            	$tags = array_values($tags);
            }
            $params = array(
                'userId' => $userId,
                'tags' => $tags
            );

            $ret = $this->curl('/user/tag/set', $params, 'json');
            if(empty($ret))
                throw new Exception('请求失败');
            return $ret;
        }catch (Exception $e) {
        	$message = date('[ c ]').' '.__METHOD__.' '.$e->getMessage()."\r\n";
        	if ($this->is_record) {
        		error_log($message, 3, $this->destination, '');
        	}else {
        		print_r($e->getMessage());
        	}
        }
    }
    
    /**
     * 广播消息
     * 此方法与 /message/broadcast 广播消息方法发送机制一样，可选择更多发送条件。
     * 调用频率：推送和广播消息合计每小时只能发送 1 次，每天最多发送 3 次。
     * @param array $platform				目标操作系统，ios、android 最少传递一个。如果需要给两个系统推送消息时，则需要全部填写
     * @param string $fromuserid			发送人用户 Id
     * @param array $audience				推送条件，包括： tag 、 userid 、 is_to_all
     * @param array $audience['tag']			（非必传）用户标签，每次发送时最多发送 20 个标签，标签之间为与的关系，is_to_all 为 true 时可不传
     * @param array $audience['userid']			（非必传）用户 Id，每次发送时最多发送 1000 个用户，如果 tag 和 userid 两个条件同时存在时，则以 userid 为准，如果 userid 有值时，则 platform 参数无效，is_to_all 为 true 时可不传
     * @param boolean $audience['is_to_all']	是否全部推送，false 表示按 tag 或 userid 条件推送，true 表示向所有用户推送，tag 和 userid 两个条件无效
     * @param array $message				发送消息，包括：content、objectName
     * @param string $message['content']		发送消息内容，参考融云 Server API 消息类型表.示例说明；如果 objectName 为自定义消息类型，该参数可自定义格式
     * @param string $message['objectName']		消息类型，参考融云 Server API 消息类型表.消息标志；可自定义消息类型
     * @param array $notification			按操作系统类型推送消息内容，如 platform 中设置了给 ios 和 android 系统推送消息，而在 notification 中只设置了 ios 的推送内容，则 android 的推送内容为最初 alert 设置的内容
     * @param string $notification['alert']		默认推送消息内容，如填写了 ios 或 android 下的 alert 时，则推送内容以对应平台系统的 alert 为准
     * @param string $notification['ios']		设置 iOS 平台下的推送及附加信息
     * @param string $notification['android']	设置 Android 平台下的推送及附加信息
     * @param string $notification['ios']['alert']		ios平台下的推送消息内容，传入后默认的推送消息内容失效，不能为空
     * @param string $notification['ios']['extras']		ios平台下的附加信息，如果开发者自己需要，可以自己在 App 端进行解析
     * @param string $notification['android']['alert']	android 平台下的推送消息内容，传入后默认的推送消息内容失效，不能为空
     * @param string $notification['android']['extras']	android 平台下的附加信息，如果开发者自己需要，可以自己在 App 端进行解析
     */
    public function pushMessage($platform=array(), $fromuserid, $audience=array(), $message=array(), $notification=array()) {
    	try{
            if(empty($platform))
                throw new Exception('目标操作系统 不能为空');
            if(empty($fromuserid))
                throw new Exception('发送人用户 Id 不能为空');
            if(empty($audience))
                throw new Exception('推送条件 不能为空');
            if(!isset($audience['is_to_all']))
                throw new Exception('是否全部推送 不能为空');
            if(empty($message))
                throw new Exception('发送消息 不能为空');
            if(empty($message['content']))
                throw new Exception('发送消息内容 不能为空');
            if(empty($message['objectName']))
                throw new Exception('消息类型 不能为空');
            if(empty($notification))
                throw new Exception('推送消息 不能为空');
            if(empty($notification['alert']))
                throw new Exception('默认推送消息内容 不能为空');

            $params = array(
                'platform' => $platform,
                'fromuserid' => $fromuserid,
                'audience' => $audience,
                'message' => $message,
                'notification' => $notification
            );
            
            $ret = $this->curl('/push', $params, 'json');
            if(empty($ret))
                throw new Exception('请求失败');
            return $ret;
        }catch (Exception $e) {
        	$message = date('[ c ]').' '.__METHOD__.' '.$e->getMessage()."\r\n";
        	if ($this->is_record) {
        		error_log($message, 3, $this->destination, '');
        	}else {
        		print_r($e->getMessage());
        	}
        }
    }
    
    /**
     * 推送
     * 调用频率：推送和广播消息合计每小时只能发送 1 次，每天最多发送 3 次。
     * @param array $platform				目标操作系统，ios、android 最少传递一个。如果需要给两个系统推送消息时，则需要全部填写
     * @param array $audience				推送条件，包括： tag 、 userid 、 is_to_all
     * @param array $audience['tag']			（非必传）用户标签，每次发送时最多发送 20 个标签，标签之间为与的关系，is_to_all 为 true 时可不传
     * @param array $audience['userid']			（非必传）用户 Id，每次发送时最多发送 1000 个用户，如果 tag 和 userid 两个条件同时存在时，则以 userid 为准，如果 userid 有值时，则 platform 参数无效，is_to_all 为 true 时可不传
     * @param boolean $audience['is_to_all']	是否全部推送，false 表示按 tag 或 userid 条件推送，true 表示向所有用户推送，tag 和 userid 两个条件无效
     * @param array $notification			按操作系统类型推送消息内容，如 platform 中设置了给 ios 和 android 系统推送消息，而在 notification 中只设置了 ios 的推送内容，则 android 的推送内容为最初 alert 设置的内容
     * @param string $notification['alert']		notification 下 alert，默认推送消息内容，如填写了 ios 或 android 下的 alert 时，则推送内容以对应平台系统的 alert 为准
     * @param string $notification['ios']		设置 iOS 平台下的推送及附加信息
     * @param string $notification['android']	设置 Android 平台下的推送及附加信息
     * @param string $notification['ios']['alert']		ios平台下的推送消息内容，传入后默认的推送消息内容失效，不能为空
     * @param string $notification['ios']['extras']		ios平台下的附加信息，如果开发者自己需要，可以自己在 App 端进行解析
     * @param integer $notification['ios']['badge']		应用角标，针对 iOS 平台；不填时，表示不改变角标数；为 0 或负数时，表示 App 角标上的数字清零；否则传相应数字表示把角标数改为指定的数字，最大不超过 9999
     * @param string $notification['android']['alert']	android 平台下的推送消息内容，传入后默认的推送消息内容失效，不能为空
     * @param string $notification['android']['extras']	android 平台下的附加信息，如果开发者自己需要，可以自己在 App 端进行解析
     */
    public function push($platform=array(), $audience=array(), $notification=array()) {
    	try{
            if(empty($platform))
                throw new Exception('目标操作系统 不能为空');
            if(empty($audience))
                throw new Exception('推送条件 不能为空');
            if(!isset($audience['is_to_all']))
                throw new Exception('是否全部推送 不能为空');
            if(empty($notification))
                throw new Exception('推送消息 不能为空');
            if(empty($notification['alert']))
                throw new Exception('默认推送消息内容 不能为空');

            $params = array(
                'platform' => $platform,
                'audience' => $audience,
                'notification' => $notification
            );
            
            $ret = $this->curl('/push', $params, 'json');
            if(empty($ret))
                throw new Exception('请求失败');
            return $ret;
        }catch (Exception $e) {
        	$message = date('[ c ]').' '.__METHOD__.' '.$e->getMessage()."\r\n";
        	if ($this->is_record) {
        		error_log($message, 3, $this->destination, '');
        	}else {
        		print_r($e->getMessage());
        	}
        }
    }
    
    /**
     * 创建http header 参数
     * @return array
     */
    private function createHttpHeader() {
        $nonce = mt_rand();
        $timeStamp = time();
        $sign = sha1($this->appSecret.$nonce.$timeStamp);
        return array(
            'RC-App-Key:'.$this->appKey,
            'RC-Nonce:'.$nonce,
            'RC-Timestamp:'.$timeStamp,
            'RC-Signature:'.$sign,
        );
    }

    /**
     * 重写实现 http_build_query 提交实现(同名key)key=val1&key=val2
     * @param array $formData 数据数组
     * @param string $numericPrefix 数字索引时附加的Key前缀
     * @param string $argSeparator 参数分隔符(默认为&)
     * @param string $prefixKey Key 数组参数，实现同名方式调用接口
     * @return string
     */
    private function build_query($formData, $numericPrefix = '', $argSeparator = '&', $prefixKey = '') {
        $str = '';
        foreach ($formData as $key => $val) {
            if (!is_array($val)) {
                $str .= $argSeparator;
                if ($prefixKey === '') {
                    if (is_int($key)) {
                        $str .= $numericPrefix;
                    }
                    $str .= urlencode($key) . '=' . urlencode($val);
                } else {
                    $str .= urlencode($prefixKey) . '=' . urlencode($val);
                }
            } else {
                if ($prefixKey == '') {
                    $prefixKey .= $key;
                }
                if (is_array($val[0])) {
                    $arr = array();
                    $arr[$key] = $val[0];
                    $str .= $argSeparator . http_build_query($arr);
                } else {
                    $str .= $argSeparator . $this->build_query($val, $numericPrefix, $argSeparator, $prefixKey);
                }
                $prefixKey = '';
            }
        }
        return substr($str, strlen($argSeparator));
    }

    /**
     * 发起 server 请求
     * @param string $action 请求地址
     * @param array $params 请求数据参数
     * @param string $contentType 内容类型 urlencoded|json
     * @return mixed
     */
    public function curl($action, $params, $contentType='urlencoded') {
        $action = self::SERVERAPIURL.$action.'.'.$this->format;
        $httpHeader = $this->createHttpHeader();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $action);
        curl_setopt($ch, CURLOPT_POST, 1);
        if ($contentType=='urlencoded') {
            $httpHeader[] = 'Content-Type:application/x-www-form-urlencoded';
            curl_setopt($ch, CURLOPT_POSTFIELDS, $this->build_query($params));
        }
        if ($contentType=='json') {
            $httpHeader[] = 'Content-Type:Application/json';
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params) );
        }
        
        curl_setopt($ch, CURLOPT_HTTPHEADER, $httpHeader);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false); //处理http证书问题
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_DNS_USE_GLOBAL_CACHE, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $ret = curl_exec($ch);
        if (false === $ret) {
            $ret = curl_errno($ch);
        }
        curl_close($ch);
        return $ret;
    }
}