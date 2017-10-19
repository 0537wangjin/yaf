<?php

namespace Aliyun;

use Aliyun\Core\Config;
use Aliyun\Core\Profile\DefaultProfile;
use Aliyun\Core\DefaultAcsClient;
use Aliyun\Api\Sms\Request\V20170525\SendSmsRequest;
use Aliyun\Api\Sms\Request\V20170525\QuerySendDetailsRequest;

// 加载区域结点配置
Config::load();

class Dysms
{
    /**
     * 构造函数
     * 
     * @param string $accessKeyId 必填，AccessKeyId
     * @param string $accessKeySecret 必填，AccessKeySecret
     */
    public function __construct($accessKeyId, $accessKeySecret)
    {
        // 短信API产品名
        $product = "Dysmsapi";
        
        // 短信API产品域名
        $domain = "dysmsapi.aliyuncs.com";
        
        // 暂时不支持多Region
        $region = "cn-hangzhou";
        
        // 服务结点
        $endPointName = "cn-hangzhou";
        
        // 初始化用户Profile实例
        $profile = DefaultProfile::getProfile($region, $accessKeyId, $accessKeySecret);
        
        // 增加服务结点
        DefaultProfile::addEndpoint($endPointName, $region, $product, $domain);
        
        // 初始化AcsClient用于发起请求
        $this->acsClient = new DefaultAcsClient($profile);
    }
    
    /**
     * 发送短信
     * 
     * @param string $signName 短信签名[必填](e.g. 盯盯)
     * @param string $templateCode 短信模板ID[必填](e.g. SMS_0001)
     * @param string $phoneNumbers <p>
     * 短信接收号码[必填](e.g. 12345678901)
     * 支持以逗号分隔的形式进行批量调用，批量上限为1000个手机号码,批量调用相对于单条调用及时性稍有延迟,验证码类型的短信推荐使用单条调用的方式
     * </p>
     * @param array|null $templateParam <p>
     * 短信模板变量替换JSON串[选填]，假如模板中存在变量需要替换则为必填项 (e.g. Array("code"=>"123456", "product"=>"云通信"))
     * 如果JSON中需要带换行符，请参照标准的JSON协议对换行符的要求，比如短信内容中包含\r\n的情况在JSON中需要表示成\r\n,否则会导致JSON在服务端解析失败
     * </p>
     * @param string|null $outId 外部流水扩展字段[选填](e.g. 20170813204735000001)
     * @return array
     */
    public function send_sms($signName, $templateCode, $phoneNumbers, $templateParam = null, $outId = null)
    {
        // 初始化SendSmsRequest实例用于设置发送短信的参数
        $request = new SendSmsRequest();
        
        // 必填，设置短信接收号码
        $request->setPhoneNumbers($phoneNumbers);
        
        // 必填，设置签名名称
        $request->setSignName($signName);
        
        // 必填，设置模板CODE
        $request->setTemplateCode($templateCode);
        
        // 可选，设置模板参数
        if ($templateParam) {
            $request->setTemplateParam(json_encode($templateParam));
        }
        
        // 可选，设置流水号
        if ($outId) {
            $request->setOutId($outId);
        }
        
        // 发起访问请求
        $acsResponse = $this->acsClient->getAcsResponse($request);
        
        return $acsResponse;
    }
    
    /**
    * 查询短信发送记录
    *
    * @param string $phone_number 短信接收号码(e.g. 12345678901)
    * @param string $send_date 短信发送日期，格式yyyyMMdd，支持最近30天记录查询(e.g. 20170901)
    * @param integer $page_size 分页大小，Max=50
    * @param integer $current_page 当前页码
    * @param string|null $biz_id 发送流水号，从调用发送接口返回值中获取[选填](e.g. 1234^1234)
    * @return array
    */
    public function query_send_details($phone_number, $send_date, $page_size = 10, $current_page = 1, $biz_id = null)
    {
        // 初始化QuerySendDetailsRequest实例用于设置短信查询的参数
        $request = new QuerySendDetailsRequest();
        $request->setPhoneNumber($phone_number);
        $request->setBizId($biz_id);
        $request->setSendDate($send_date);
        $request->setPageSize($page_size);
        $request->setCurrentPage($current_page);
        // 发起访问请求
        $acsResponse = $this->acsClient->getAcsResponse($request);
        return $acsResponse;
    }
}