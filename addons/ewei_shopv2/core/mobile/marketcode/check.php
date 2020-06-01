<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Check_EweiShopV2Page extends MobilePage
{
	public function main()
	{
		global $_W;
		global $_GPC;
		$openid = $_W['openid'];
		$uniacid = $_W['uniacid'];
		$admin = pdo_fetch('select * from ' . tablename('zmcn_fw_admin') . ' where openid=:openid and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':openid' => $openid));
		if (empty($admin)) {
			$nopurview=1;
		}else{
			$setting=unserialize($admin['setting']);
		}
		if (!$setting['purview']['check']) {
			$nopurview=1;
		}
		$member = m('member')->getMember($openid);
		$code = $this->docode($_REQUEST['code']);
		if($code){
			$condition="uniacid=$uniacid and (largecode='$code' or bigcode='$code' or middlecode='$code' or smallcode='$code' or code='$code')";
			$sql="select deliveryID from " . tablename('zmcn_fw_data') . " where $condition";
			$codeC = pdo_fetch($sql,array());
			if(!$codeC['deliveryID']&&substr($code,0,2)=="FW"){
				$codeS=substr($code,2,8)."0";
				$conditionD="uniacid=$uniacid and code='$codeS'";
				$sqlD="select itemid from " . tablename('zmcn_fw_deliveryRecord') . " where $conditionD order by itemid desc";
				$codeD = pdo_fetch($sqlD,array());
				if($codeD) $codeC['deliveryID']=$codeD['itemid'];
			}
			if($codeC['deliveryID']&&1==1){
				$condition=" itemid=".$codeC['deliveryID'];
			}else{
				$condition=" code='$code'";
			}
			$sqlR="select * from " . tablename('zmcn_fw_deliveryRecord') . " where $condition and uniacid=$uniacid order by itemid desc limit 1";
			$codeR = pdo_fetch($sqlR,array());
			if($codeR){
				$sendInfo=unserialize($codeR['sendInfo']);
				$receiveInfo=unserialize($codeR['receiveInfo']);
			}
			//file_put_contents(time().".check.txt",$sql."\r\n".$sqlD."\r\n".$sqlR);
		}
		include $this->template();
	}
	public function docode($var){
		if (strpos($var,",")!=false){
			$varArr=explode(",",$var);
			$varTrue=$varArr['1'];	
		}elseif (strpos($var,"?")!=false){
			$varArr=explode("?",$var);
			parse_str($varArr['1'],$parsArr);
			if($parsArr['co']){
				$varTrue=$parsArr['co'];
			}elseif($parsArr['sc']){
				$varTrue=$parsArr['sc'];
			}elseif($parsArr['mc']){
				$varTrue=$parsArr['mc'];
			}elseif($parsArr['bc']){
				$varTrue=$parsArr['bc'];
			}elseif($parsArr['lc']){
				$varTrue=$parsArr['lc'];
			}
		}else{
			$varTrue=$var;
		}
		return $varTrue;
	}
}

?>
