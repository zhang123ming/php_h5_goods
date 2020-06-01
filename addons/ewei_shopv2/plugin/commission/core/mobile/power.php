<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

require EWEI_SHOPV2_PLUGIN . 'commission/core/page_login_mobile.php';
class Power_EweiShopV2Page extends CommissionMobileLoginPage
{
	public function main(){
		global $_W;
		global $_GPC;
		$id = $_GPC['id']?$_GPC['id']:'';
		$mid = $_W['mid']?$_W['mid']:'';

		$diyform_plugin = p('diyform');
		if ($diyform_plugin) {
			$set_config = $diyform_plugin->getSet();
			$commission_diyform_open = $set_config['commission_diyform_open'];

			if ($commission_diyform_open == 1) {
				$template_flag = 1;
				$diyform_id = $set_config['commission_diyform'];

				if (!empty($diyform_id)) {
					$formInfo = $diyform_plugin->getDiyformInfo($diyform_id);
					$fields = $formInfo['fields'];
					$diyform_data = iunserializer($member['diycommissiondata']);
					$f_data = $diyform_plugin->getDiyformData($diyform_data, $fields, $member);
				}
			}
		}

		if($_W['ispost']){
			$n_data['status'] = 1;
			$n_data['isagent'] = 1;

			// var_dump($id);die;
			$members = m('member')->getMember($id, true);
			$pagent = pdo_fetch("SELECT agentpower FROM " . tablename("ewei_shop_member") . " WHERE uniacid=:uniacid and level=:level and agentlevel=:agentlevel and status = 1 and isagent = 1 " , array(':uniacid' => $_W['uniacid'],':level'=>$members['level'],':agentlevel'=>$members['agentlevel']));
			if (!empty($pagent)) {
				pdo_update('ewei_shop_member', $pagent, array('id' => $id,'uniacid'=>$_W['uniacid']));
			}
			if ($template_flag == 1){
				pdo_update('ewei_shop_member', $n_data, array('id' => $id));
			} else {
				$levels = $_GPC['levels'];
				$agentlevels = $_GPC['agentlevels'];
				if(!empty($levels)){
					$n_data['level'] = $levels;
				}
				if(!empty($agentlevels)){
					$n_data['agentlevel'] = $agentlevels;
				}
				pdo_update('ewei_shop_member', $n_data, array('id' => $id));
			}
		}
			
		//待审核用户信息
		if(!empty($id)){
			$member = m('member')->getMember($id, true);
		}

		//自己的信息
		if(!empty($mid)){
			$userinfo = m('member')->getMember($mid, true);
		}

		

		if(!empty($member['level'])){
			if ($template_flag == 1) {
				if (!empty($member["diycommissiondata"])){
					$diydata = unserialize($member['diycommissiondata']);
					$levelid =  pdo_fetch("SELECT id,levelname FROM " . tablename("ewei_shop_member_level") . " WHERE uniacid=:uniacid and levelname=:levelname" , array(':uniacid' => $_W['uniacid'],':levelname'=>$diydata['diyshenqingdengji']));
				}
			} else {
				$levelid = pdo_fetch("SELECT id,levelname FROM " . tablename("ewei_shop_member_level") . " WHERE uniacid=:uniacid and id=:id" , array(':uniacid' => $_W['uniacid'],':id'=>$member['level']));
			}
		}
		$aaglevels = pdo_fetchall("SELECT id,levelname,level FROM " . tablename("ewei_shop_commission_level") . " WHERE uniacid=:uniacid" , array(':uniacid' => $_W['uniacid']));
		$auserlevels = pdo_fetchall("SELECT id,levelname,level FROM " . tablename("ewei_shop_member_level") . " WHERE uniacid=:uniacid" , array(':uniacid' => $_W['uniacid']));
		if(!empty($userinfo['agentlevel'])){
			$aagent = pdo_fetch("SELECT id,levelname,level FROM " . tablename("ewei_shop_commission_level") . " WHERE id=:id and uniacid=:uniacid" , array(':id'=>$userinfo['agentlevel'], ':uniacid' => $_W['uniacid']));
		}
		if(!empty($userinfo['level'])){
			$alevel = pdo_fetch("SELECT id,levelname,level FROM " . tablename("ewei_shop_member_level") . " WHERE id=:id and uniacid=:uniacid" , array(':id'=>$userinfo['level'], ':uniacid' => $_W['uniacid']));
		}
		// var_dump($aaglevels,$auserlevels,$aagent,$alevel);die;
		//获取用户审核权限
		if(!empty($userinfo['agentpower'])) {
			$userinfo['agentpower'] = unserialize($userinfo['agentpower']);
			$uslevels = $userinfo['agentpower']['userlevelid']?$userinfo['agentpower']['userlevelid']:'';
			$aglevels = $userinfo['agentpower']['agentlevelid']?$userinfo['agentpower']['agentlevelid']:'';

			if(!empty($uslevels)){
				$userpower = explode(",",$uslevels);
				array_pop($userpower);
			} else {
				$userpower = "";
			}

		} else {
			$userinfo['agentpower']['agentpower'] = '0';
		}

		// //获取用户等级信息
		if(!empty($member)){
			$levels = $this->model->getLevel($member['openid']);
			$level = intval($member['level']);
			$user = pdo_fetch("SELECT * FROM " . tablename("ewei_shop_member_level") . " WHERE id = :id" , array(":id" => "$level"));

			$agents = intval($member['agentlevel']);
			$agent = pdo_fetch("SELECT * FROM " . tablename("ewei_shop_commission_level") . " WHERE id = :id" , array(":id" => "$agents"));
			if (!empty($member['diycommissiondata'])) {
				$datalevelname = unserialize($member['diycommissiondata']);
			}
		}

		include $this->template();
	}
	public function changeback()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		
		$data['status'] = 0;
		$data['isagent'] = 0;

		$res = pdo_update('ewei_shop_member', $data, array('id' => $id));
		if(!empty($res)){
			show_json(1,"驳回成功");
		} else {
			show_json(0,"驳回失败");
		}
	}
	
}
?>