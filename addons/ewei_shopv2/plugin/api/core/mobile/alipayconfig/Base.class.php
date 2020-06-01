<?php
require_once EWEI_SHOPV2_PLUGIN."api/core/mobile/alipayconfig/Rsa.class.php";
class Base extends RSA
{
/**
* 以下信息需要根据自己实际情况修改
*/
//申请到的APPID
public $appid;
//修改成自己的 应用私钥
public $appprikey;
//修改成自己的 支付宝公钥
public $public_key;
public function getStr($arr,$type = 'RSA'){
    //筛选
    if(isset($arr['sign'])){
    unset($arr['sign']);
    }
    if(isset($arr['sign_type']) && $type == 'RSA'){
    unset($arr['sign_type']);
    }
    //排序
    ksort($arr);
    //拼接
    return  $this->getUrl($arr,false);
}
//将数组转换为url格式的字符串
public function getUrl($arr,$encode = true){
    if($encode){
    return http_build_query($arr);
    }else{
    return urldecode(http_build_query($arr));
    }
}
 
//获取签名RSA2
public function getRsa2Sign($arr){
    return $this->rsaSign($this->getStr($arr,'RSA2'),$this->appprikey,'RSA2') ;
}
//获取含有签名的数组RSA
public function setRsa2Sign($arr){
    $arr['sign'] = $this->getRsa2Sign($arr);
    return $arr;
}
public function checkSign($arr){
    if($this->getRsa2Sign($arr) == $arr['sign']){
      return true;
    }else{
      return false;
    }
}
 
public function curlRequest($url,$data = ''){
    $ch = curl_init();
    $params[CURLOPT_URL] = $url;    //请求url地址
    $params[CURLOPT_HEADER] = false; //是否返回响应头信息
    $params[CURLOPT_RETURNTRANSFER] = true; //是否将结果返回
    $params[CURLOPT_FOLLOWLOCATION] = true; //是否重定向
    $params[CURLOPT_TIMEOUT] = 30; //超时时间
    if(!empty($data)){
    $params[CURLOPT_POST] = true;
    $params[CURLOPT_POSTFIELDS] = $data;
    }
    $params[CURLOPT_SSL_VERIFYPEER] = false;//请求https时设置,还有其他解决方案
    $params[CURLOPT_SSL_VERIFYHOST] = false;//请求https时,其他方案查看其他博文
    curl_setopt_array($ch, $params); //传入curl参数
    $content = curl_exec($ch); //执行
    curl_close($ch); //关闭连接
    return $content;
}
 
}