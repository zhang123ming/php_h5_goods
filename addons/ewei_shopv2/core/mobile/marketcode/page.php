<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Page_EweiShopV2Page extends MobilePage
{
	public function main()
	{
		global $_W;
		global $_GPC;
		$openid = $_W['openid'];
		$uniacd = $_W['uniacid'];
		$member = m('member')->getMember($openid);
		$admin = pdo_fetch('select * from ' . tablename('zmcn_fw_admin') . ' where openid=:openid and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':openid' => $openid));
		if (empty($admin)) {
			$nopurview=1;
		}else{
			$setting=unserialize($admin['setting']);
		}
		if (!$setting['purview']['send']) {
			$nopurview=1;
		}
		if ($admin) {
			$agentList = pdo_fetchall('select * from ' . tablename('zmcn_fw_agent') . ' where uniacid=:uniacid', array(':uniacid' => $_W['uniacid']));
		}
		include $this->template();
	}
	public function searchAgent(){
		global $_W,$_GPC;
		$name = $_GPC['name'];
		$agentList = pdo_fetchall('select * from '.tablename('zmcn_fw_agent')." where name like '%".$name."%' and uniacid=:uniacid", array(':uniacid' => $_W['uniacid']));
		echo json_encode($agentList);
	}
	public function deliverRecord()
	{
		global $_W;
		global $_GPC;
		$quickModel=0;//快速出货模式1为开启
		$openid = $_W['openid'];
		$uniacid = $_W['uniacid'];
		$admin = pdo_fetch('select * from ' . tablename('zmcn_fw_admin') . ' where openid=:openid and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':openid' => $openid));
		if (empty($admin)) {
			$nopurview=1;
		}else{
			$setting=unserialize($admin['setting']);
		}
		if (!$setting['purview']['send']) {
			$nopurview=1;
		}
		if ($nopurview) {
			exit(json_encode(array('code'=>100,'msg' =>'您无权限')));
		}
		$agentInfo = pdo_fetch('select * from ' . tablename('zmcn_fw_agent') . ' where itemid=:itemid and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':itemid' => intval($_GPC['agent'])));
		if (empty($agentInfo)) {
			exit(json_encode(array('code'=>100,'msg' =>'经销商不存在')));
		}
		$code = trim($_REQUEST['code']);
		$agent = trim($_GPC['agent']);
		$code = str_replace("\r\n", "|", $code);
		$code = str_replace("\r", "|", $code);
		$code = str_replace("\n", "|", $code);
		$codeArr=explode("|", $code);
		$i=0;
		foreach($codeArr as $k=>$d){
			$post=array();
			$d=$this->docode($d);
			$condition="uniacid=$uniacid and (largecode='$d' or bigcode='$d' or middlecode='$d' or smallcode='$d' or code='$d')";
			$sql="select * from " . tablename('zmcn_fw_data') . " where $condition";
			if($quickModel==0){
				$checkCode = pdo_fetch($sql);
				if (empty($checkCode)) {
					$checkCode['code']="-";
				}
			}else{
				$checkCode['code']="-";
			}
			if($checkCode){
				$post['uniacid']=$uniacid;
				$post['code']=$d;
				$post['direction']='send';
				$post['adminID']=$admin['itemid'];
				$post['sendID']=$admin['itemid'];
				$post['sendInfo']=serialize($admin);
				$post['receiveID']=$agentInfo['itemid'];
				$post['receiveInfo']=serialize($agentInfo);
				$post['createTime']=TIMESTAMP;
				$post['updateTime']=TIMESTAMP;
				$post['ip']=$_W['clientip'];
				if($checkCode['code']==$d){
					$post['complete']=1;
				}else{
					$post['complete']=0;
				}
				$rid=pdo_insert('zmcn_fw_DeliveryRecord', $post);
				if($quickModel==0||1==2){
					if($rid){
						$itemid = pdo_insertid();
						$sql="update " . tablename('zmcn_fw_data') . " set deliveryID=$itemid where $condition";
						// file_put_contents($d.".check.txt",$sql."\r\n".$sqlR);
						pdo_query($sql);
					}
				}
				$i++;
			}
		}
		$msg=$i."条记录成功出货";
		exit(json_encode(array('code'=>102,'msg' =>$msg)));
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
