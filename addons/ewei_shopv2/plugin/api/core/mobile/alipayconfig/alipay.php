<?php
require_once EWEI_SHOPV2_PLUGIN."api/core/mobile/alipayconfig/Alipay.class.php";
class alipay
{
	public function ali($data,$sec)
	{
		if(empty($sec)){
			$res['alipay_fund_trans_toaccount_transfer_response']['sub_msg'] = '请配置好支付参数';
			return $res;
		}
		//构建支付请求 可以传递MD5 RSA RSA2三种参数
		$obj = new Alipay_pay($sec);
		//UTF-8格式的json数据
		$res = iconv('gbk','utf-8',$obj->transfer($data,$sec));
		//转换为数组
		$res = json_decode($res,true);
		return $res;
	}
}

?>