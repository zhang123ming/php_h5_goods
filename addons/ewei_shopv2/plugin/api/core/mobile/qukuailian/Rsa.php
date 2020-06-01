<?php
class Rsa
{
	/**
	 * 校验$value是否非空
	 *  if not set ,return true;
	 *    if is null , return true;
	 **/
	public function checkEmpty($value) {
		if (!isset($value))
			return true;
		if ($value === null)
			return true;
		if (trim($value) === "")
			return true;

		return false;
	}

	public function subCNchar($str, $start = 0, $length, $charset = "utf-8") {
		if (strlen($str) <= $length) {
			return $str;
		}
		$re['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
		$re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
		$re['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
		$re['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
		preg_match_all($re[$charset], $str, $match);
		$slice = join("", array_slice($match[0], $start, $length));
		return $slice;
	}

	public function splitCN($cont, $n = 0, $subnum, $charset) {
		$arrr = array();
		for ($i = $n; $i < strlen($cont); $i += $subnum) {
			$res = $this->subCNchar($cont, $i, $subnum, $charset);
			if (!empty ($res)) {
				$arrr[] = $res;
			}
		}
        return $arrr;
	}

	/** 
	 *  在使用本方法前，必须初始化AopClient且传入公私钥参数。
	 *  公钥是否是读取字符串还是读取文件，是根据初始化传入的值判断的。
	 **/
	 public function rsaEncrypt($data, $pu_key, $charset) {
			if(!empty($pu_key)){
				//转换为openssl格式密钥
				$res = openssl_get_publickey($pu_key);
			}
		($res) or die('RSA公钥错误。请检查公钥文件格式是否正确'); 
		$blocks = $this->splitCN($data, 0,30, $charset);
		$chr  = '';
		$encodes  = array();
		foreach ($blocks as $n => $block) {
			if (!openssl_public_encrypt($block,$chr,$res)) {
				echo "<br/>" . openssl_error_string() . "<br/>";
			}
			$encodes[] = $chr;
		}
		$chr = implode(",", $encodes);
        return base64_encode($chr);
	}
}