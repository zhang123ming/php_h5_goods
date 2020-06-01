<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}
require EWEI_SHOPV2_PLUGIN . 'commission/core/page_login_mobile.php';
class AgentTo_EweiShopV2Page extends CommissionMobileLoginPage
{
	public function main()
	{
		global $_W;
		global $_GPC;
		$level = m('member')->getLevel($_W['openid']);
		if($level['level']<92){
			$level['level'] = 92;
		}
		$aglevels = pdo_fetchall("SELECT id,levelname,level FROM " . tablename("ewei_shop_commission_level") . " WHERE uniacid=:uniacid and level>:level" , array(':uniacid' => $_W['uniacid'],':level'=>$level['level']));
		if(empty($aglevels)){
			$aglevels[0]['levelname'] = '已是最高等级';
			$aglevels[0]['level'] = 0;
		}
		$isTO = pdo_fetchcolumn('select count(*) from '.tablename('ewei_shop_member_apply').' where uniacid=:uniacid and openid=:openid and status=0',array(':uniacid'=>$_W['uniacid'],':openid'=>$_W['openid']));
		include $this->template();
	}

	public function submit()
	{
		global $_W,$_GPC;
		$data = array(
			'uniacid'=>$_W['uniacid'],
			'openid'=>$_W['openid'],
			'memberdata'=>iserializer(array('realname'=>$_GPC['realname'],'mobile'=>$_GPC['mobile'],'city'=>$_GPC['province'].$_GPC['city'].$_GPC['district'])),
			'level'=>$_GPC['agentlevel'],
			'status'=>0,
			'createtime'=>time()
		);
		pdo_insert('ewei_shop_member_apply',$data);
		echo 1;exit;
	}

}

?>
