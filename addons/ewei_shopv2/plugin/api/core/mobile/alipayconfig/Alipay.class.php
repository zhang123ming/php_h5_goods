<?php
require_once EWEI_SHOPV2_PLUGIN."api/core/mobile/alipayconfig/Base.class.php";
class Alipay_pay extends Base
{
    const TRANSFER = 'https://openapi.alipay.com/gateway.do';
 
    public function __construct($sec) {
        $this->appid = $sec['appid'];
        $this->appprikey = $sec['appprikey'];
        $this->public_key = $sec['public_key'];
    }
 
    //转账
    public function transfer($data){

        //公共请求参数
        $pub_params = [
            'app_id'    => $this->appid,
            'method'    =>  'alipay.fund.trans.toaccount.transfer', //接口名称 应填写固定值alipay.fund.trans.toaccount.transfer
            'format'    =>  'JSON', //目前仅支持JSON
            'charset'    =>  'UTF-8',
            'sign_type'    =>  'RSA2',//签名方式
            'sign'    =>  '', //签名
            'timestamp'    => date('Y-m-d H:i:s'), //发送时间 格式0000-00-00 00:00:00
            'version'    =>  '1.0', //固定为1.0
            'biz_content'    =>  '', //业务请求参数的集合
        ];
        //请求参数
        $api_params = [
            'out_biz_no'  => $data['out_biz_no'],//商户转账订单号
            'payee_type'  => 'ALIPAY_LOGONID', //收款方账户类型
            'payee_account'  => $data['payee_account'], //收款方账户
            'amount'  => $data['amount'], //金额
            'remark' => $data['remark'] //转账说明
        ];

        $pub_params['biz_content'] = json_encode($api_params,JSON_UNESCAPED_UNICODE);
        $pub_params =  $this->setRsa2Sign($pub_params);
        return $this->curlRequest(self::TRANSFER, $pub_params);
    }
}