<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

require EWEI_SHOPV2_PLUGIN . 'commission/core/page_login_mobile.php';
class Withdrawlist_EweiShopV2Page extends CommissionMobileLoginPage
{
	public function main()
	{
		global $_W;
		global $_GPC;
		$record = pdo_fetchall("select * from" . tablename('ewei_shop_commission_record') . "where uniacid=:uniacid and openid=:openid and amount>0 order by createtime desc", array(':uniacid' => $_W['uniacid'],':openid' => $_W['openid']));
		$level = $_W['shopset']['commission']['level'];
		$l = '';
		if($level==1){
			$l = $_W['shopset']['commission']['texts']['c1'].'分销';
		} else if($level == 2){
			$l = $_W['shopset']['commission']['texts']['c2'].'分销';
		} else if($level == 3){
			$l = $_W['shopset']['commission']['texts']['c3'].'分销';
		}
		$member = pdo_fetch("select level from" .tablename('ewei_shop_member') . "where uniacid=:uniacid and openid=:openid ", array(':uniacid' => $_W['uniacid'],':openid' => $_W['openid']));
		if(!empty($member)){
			$levelname = pdo_fetch("select levelname from " . tablename('ewei_shop_member_level') . "where uniacid=:uniacid and id=:id and level>= 90 ",array(':uniacid' => $_W['uniacid'],':id' => $member['level']));
		}
		foreach ($record as $key => $value) {
			if(!empty($value['orderid'])){
				$record[$key]['yMember'] = pdo_fetch('select m.nickname,m.mobile from '.tablename('ewei_shop_order').' as o,'.tablename('ewei_shop_member').' as m where o.openid=m.openid and o.uniacid=:uniacid and m.uniacid=:uniacid and o.id=:id',array(':uniacid'=>$_W['uniacid'],':id'=>$value['orderid']));
			}
			$order = pdo_fetch("select price from" .tablename('ewei_shop_order') . "where uniacid=:uniacid and id=:id",array(':uniacid' => $_W['uniacid'],':id' => $value['orderid']));
			if(!empty($order)){
				$record[$key]['goodsprice'] = $order['price'];
			}
			$res = substr($value['remark'],0,3);
			if($value['amount']<=0){
				unset($record[$key]);
			}
			if($res == '一'){
				$record[$key]['remark'] = $l.$_W['shopset']['commission']['texts']['c1'].'奖励';
			} else if($res == '二'){
				$record[$key]['remark'] = $l.$_W['shopset']['commission']['texts']['c2'].'奖励';
			} else if($res == '三'){
				$record[$key]['remark'] = $l.$_W['shopset']['commission']['texts']['c3'].'奖励';
			} else if($res == '代'){
				$record[$key]['remark'] = $levelname['levelname'].'代理奖励';
			}
	
		}
		include $this->template();
	}
}

?>
