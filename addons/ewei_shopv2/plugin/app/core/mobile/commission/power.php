<?php 	

if (!(defined('IN_IA'))) {
    exit('Access Denied');
}
require __DIR__ . '/base.php';
class Power_EweiShopV2Page extends Base_EweiShopV2Page
{
	
	function getAgentList()
	{
		global $_W;
		global $_GPC;

		$openid =$_W['openid'];
		$member =m('member')->getMember($openid);

		$pindex =max(1,$_GPC['page']);
		$psize = 20; 

		if (!$member) {
			app_error("-1000", "参数错误");
		}

		$sql = "SELECT * FROM ".tablename('ewei_shop_member')." WHERE uniacid={$_W[uniacid]} AND isagent=1 AND status=0 and agentid=$member[id] limit ".($pindex-1)*$psize.",".$psize;
		
		
		$memberlist = pdo_fetchall($sql);

		foreach ($memberlist as &$value) {
			$value['createtime'] = date('Y-m-d H:i:s',$value['createtime']);
		}

		$total = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename('ewei_shop_member')." WHERE uniacid=$_W[uniacid] AND isagent=1 AND status=0 and agentid=$member[id]");

		$result=array('list'=>$memberlist,'pagesize'=>$psize,'total'=>$total);
		app_json($result);
	}

	public function getAgentDetail(){
		global $_W;
		global $_GPC;

		if (empty($_GPC['mopenid'])) {
			app_error('-1000', "参数错误");
		}

		$agentid =$_GPC['mopenid'];

		$sql="SELECT * FROM ".tablename('ewei_shop_member')." WHERE uniacid=$_W[uniacid] AND id=$agentid";
		$memberinfo = pdo_fetch($sql);
		if ($memberinfo) {
			$diycommissiondata = iunserializer($memberinfo['diycommissiondata']);
			$diycommissionfields = iunserializer($memberinfo['diycommissionfields']);

			$diycommission=array_merge_recursive($diycommissionfields,$diycommissiondata);

			$memberinfo['createtime'] = date('Y-m-d H:i:s',$memberinfo['createtime']);
			$result=array('memberinfo'=>$memberinfo,'membershow'=>1,'diycommission'=>$diycommission);
			app_json($result);
		}else{
			app_error('-10001',"未发现该用户的申请需求");
		}
		

	}

	public function check(){

		global $_W;
		global $_GPC;

		if (empty($_GPC['magentid'])) {
			app_error('-10001', "参数错误");
		}

		$currentMember=m('member')->getMember($_W['openid']);

		$magentid = $_GPC['magentid'];
		$sql="select * from ".tablename('ewei_shop_member')." WHERE uniacid={$_W[uniacid]} and id={$magentid}";
		$member = pdo_fetch($sql);
		$time=time();

		if ($member) {
			pdo_update('ewei_shop_member', array('status' => 1, 'agenttime' => $time), array('id' => $member['id'], 'uniacid' => $_W['uniacid']));

			plog('commission.agent.check','上级审核分销商 <br/>分销商信息:  ID: ' . $member['id'] . ' /  ' . $member['openid'] . '/' . $member['nickname'] . '/' . $member['realname'] . '/' . $member['mobile'].",上级ID".$currentMember['id']);
			app_json('更新成功');
		}else{
			app_error('-10002', '更新失败');
		}
	}
}




?>