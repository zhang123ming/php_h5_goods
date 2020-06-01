<?php
require_once EWEI_SHOPV2_PLUGIN."api/core/mobile/qukuailian/Rsa.php";
require_once EWEI_SHOPV2_PLUGIN."api/core/mobile/qukuailian/Des.php";
class qukuailian 
{
   	public function pay($data){
   			$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
	        $key   = '';
	        for ($i = 0; $i < 8; $i++) {
	            $key .= $chars[mt_rand(0, strlen($chars) - 1)];
	        }
	        $setting = uni_setting($_W['uniacid'], array('payment'));
	        $pu_key = $setting['payment']['qukuailian']['signkey'];
	        $MerchantID = $setting['payment']['qukuailian']['merid'];
   			$rsa = new rsa();
			$des = new CryptDes($key,$key);//（秘钥向量，混淆向量）
			$sign = $rsa->rsaEncrypt($key,$pu_key,'utf-8');
			$ret = $des->encrypt($data);//加密字符串
			$data = array(
				"MerchantID"=>$MerchantID,
				'sign'=>$sign,
				'data'=>$ret
   			);
			$url = "https://flink.gl/userdata/TradePayInfo.a";
			$result = $this->curlRequest($url,$data);
			return $result;
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