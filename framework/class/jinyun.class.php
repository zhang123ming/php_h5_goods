<?php
/*
 *jinyunzn Openapi SDK For PHP，Version:1.0
 *Api WebSite:wwww.jinyunzn.com
 *date:2016-09-02
*/
class JinYunZN{

/*
 *发送数据接口
 *$token、$apikey:www.jinyunzn.com中登陆后在第三方开发中获取
 *$dtuid、$imei:打印机背面获取
 *$senddata:打印内容
 *$ReturnValue:返回信息
*/
public function SendPrintData($token,$apikey,$dtuid,$imei,$senddata)
{
	error_reporting(0);  //关闭错误显示
	if ($token=='' || $apikey=='' || $dtuid=='' || $imei=='' || $senddata=='')
	{
		$ReturnValue=13;
	}else{
		$senddata=str_replace('&lt;','<',$senddata);
		$senddata=str_replace('&gt;','>',$senddata);
		$ReturnValue=$this->SendSocketJson($token,$apikey,$dtuid,$imei,$senddata);
	}
	switch ($ReturnValue)
	{
		case 30:
			$ReturnValue="服务器连不通";
			break;
		case 31:
			$ReturnValue="数据发送失败";
			break;
		case 117:
			$ReturnValue="数据发送成功";
			break;
		case 12:
			$ReturnValue="数据包缺少必要成员";
			break;
		case 13:
			$ReturnValue="数据包中的参数错误";
			break;
		case 20:
			$ReturnValue="没有添加设备";
			break;
		case 18:
			$ReturnValue="设备ID或IMEI码不正确";
			break;
		case 21:
			$ReturnValue="token或apikey不正确";
			break;
		default:
			$ReturnValue="其他原因";
			break;
	}
	return $ReturnValue;
}
/*
 *发送数据接口
 *$token、$apikey:www.jinyunzn.com中登陆后在第三方开发中获取
 *$dtuid、$imei:打印机背面获取
 *$senddata:打印内容
*/
function SendSocketJson($token,$apikey,$dtuid,$imei,$senddata)
{
	//购买机器后向客服获取ip、port
	$port = 14999;
	$ip="120.24.219.107";
	$url="www.jinyunzn.com";
	$ReturnValue="";

	//转码，打印机用的是GB2312；但在某些服务器上转gb2312，遇到特殊字符时会返回空值，所以用GBK
	$senddata2 = iconv("utf-8","gb2312//IGNORE",$senddata);  
	if ($senddata2=='')
	{
		$senddata = iconv("utf-8","GBK//IGNORE",$senddata);}
	else{
		$senddata=$senddata2;
	}
	$data='{"request":"17",';
	$data.='"token":"'.$token.'",';
	$data.='"api_key":"'.$apikey.'",';
	$data.='"id":"'.$dtuid.'",';
	$data.='"imei":"'.$imei.'",';
	$data.='"context":"'.$senddata.'"}';
			
	$buf=$this->PackageWebHead();
	$buf=$this->SetPacketLen($buf, 12+strlen($data));
	$buf=implode($buf);  //buf数组转成字符串
	$senddata=$buf.$data;  //合并
	if (function_exists('socket_create')) {
		$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		if ($socket >= 0){
			$result = socket_connect($socket, $ip, $port);
			if (!$result){
				$result = socket_connect($socket, $url, $port);
			}
			if (!$result){
				$ReturnValue=30;
				socket_close($socket);
			}
			else{
				//发送数据
				if(socket_write($socket, $senddata, strlen($senddata))){
					//返回结果
					while($ReturnData = socket_read($socket, 8192,PHP_BINARY_READ)){
						socket_close($socket);
						//返回结果：{"request": "17", "request_code": "117"}
						$ReturnData=substr($ReturnData,8);
						$ReturnData=json_decode($ReturnData);
						$ReturnValue=$ReturnData->{'request_code'};
					}
				}else{
					$ReturnValue=31;
					socket_close($socket);
				}
			}
		}else{
			socket_close($socket);
		}
	}else{
		$ReturnValue=$this->SendSocketJsonB($senddata);
	}
	
	//socket_close($socket);
	return $ReturnValue;
}
function SendSocketJsonB($senddata){
	$url='http://www.jinyunzn.com/receiveStreamFile.php';
    $opts = array(
        'http' => array(
            'method' => 'POST',
            'header' => 'content-type:application/x-www-form-urlencoded',
            'content' => $senddata
        )
    );

    $context = stream_context_create($opts);
    $response = file_get_contents($url, false, $context);
    $ret = json_decode($response, true);
    return $ret['success'];
}

//加0-3位前缀
function PackageWebHead()
{
	$buf[0]=$this->decode(0x19, '&#');
	$buf[1]=$this->decode(0x26, '&#');
	$buf[2]=$this->decode(0x72, '&#');
	$buf[3]=$this->decode(0x01, '&#');
	return $buf;
}
//加4-11位前缀
function SetPacketLen($buf, $length)
{
	$buf[4] = $length%10+'0';
	$length /= 10;
	$buf[5] = $length%10+'0';
	$length /= 10;
	$buf[6] = $length%10+'0';
	$length /= 10;	
	$buf[7] = $length%10+'0';
	$length /= 10;	
	$buf[8] = $length%10+'0';
	$length /= 10;
	$buf[9] = $length%10+'0';
	$length /= 10;
	$buf[10] = $length%10+'0';
	$length /= 10;	
	$buf[11] = $length%10+'0';
	return $buf;
}
/**
* 将ascii码转为字符串
* @param type $str 要解码的字符串
* @param type $prefix 前缀，默认:&#
* @return type
*/
function decode($str, $prefix="&#")
{
	$str = str_replace($prefix, "", $str);
	$a = explode(";", $str);
	$utf='';
	foreach ($a as $dec)
	{
		if ($dec < 128)
		{
			$utf .= chr($dec);
		} 
		else if ($dec < 2048)
		{
			$utf .= chr(192 + (($dec - ($dec % 64)) / 64));
			$utf .= chr(128 + ($dec % 64));
		} 
		else 
		{
			$utf .= chr(224 + (($dec - ($dec % 4096)) / 4096));
			$utf .= chr(128 + ((($dec % 4096) - ($dec % 64)) / 64));
			$utf .= chr(128 + ($dec % 64));
		}
	}
	return $utf;
}


}
?>
