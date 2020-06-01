<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}
/**
 * 
 */
class Couponcharge_EweiShopV2Page extends MobileLoginPage
{
	
	public function main(){
		global $_GPC,$_W;
		$set = $_W['shopset']['coupon'];
		$member = m('member')->getMember($_W['openid']);
		$merch = pdo_get('ewei_shop_merch_user',array('id'=>$member['merchid']));
		$coupon = pdo_fetch('select t.*,c.backrate from '.tablename('ewei_shop_coupon_goodsendtask').' t left join '.tablename('ewei_shop_coupon').' c on c.id=t.couponid where t.uniacid=:uniacid and t.allgoods=1',array(':uniacid'=>$_W['uniacid']));
		if ($_W['ispost']) {
			$money = $_GPC['money'];
			$agentid = $_GPC['agentid'];
			$agentinfo = m('member')->getMember($agentid);
			if (empty($agentinfo)) {
				show_json(0,'会员不存在');
			}
			if (empty($money)) {
				show_json(0,'请填写消费金额');
			}
			if ($money<0) {
				show_json(0,'消费金额必须大于0');
			}
			if ($coupon['backrate']>0) {
				$amount = $coupon['backrate']*$money;
				$couponrate = $amount*$set['couponrate'];
				if ($couponrate>$member['credit2']) {
					show_json(2,'帐户余额不足');
				}else{
					if ($couponrate>0) {
						$result = m('member')->setCredit($_W['openid'],'credit2',0-$couponrate,array($_W['member']['uid'], $_W['shopset']['shop']['name'] . '线下返优惠券费' . $couponrate));
						if(is_error($result)){
							show_json(0,$result['message']);
						}
						$logno = m('common')->createNO('member_log', 'logno', 'CC');
						$log = array('uniacid' => $_W['uniacid'], 'logno' => $logno, 'title' => $set['shop']['name'] . '线下返优惠券费', 'openid' => $_W['openid'], 'money' => 0-$couponrate, 'type' => 0, 'createtime' => time(), 'status' => 1);
						pdo_insert('ewei_shop_member_log', $log);
					}
				}
				$couponlog = array('uniacid' => $_W['uniacid'], 'openid' => $agentinfo['openid'], 'logno' => m('common')->createNO('coupon_log', 'logno', 'CC'), 'couponid' => $coupon['couponid'], 'status' => 1, 'paystatus' => -1, 'creditstatus' => -1, 'createtime' => time(), 'getfrom' => 6);
				pdo_insert('ewei_shop_coupon_log', $couponlog);
				$data = array('uniacid' => $_W['uniacid'], 'openid' => $agentinfo['openid'], 'couponid' => $coupon['couponid'], 'gettype' => 6, 'gettime' => time(),'amount'=>$amount,'frommerch'=>$member['merchid'],'fromtype'=>empty($merch['merchtype']) ? 2 : 1);
				$res = pdo_insert('ewei_shop_coupon_data', $data);
				$coupondataid = pdo_insertid();
				$data = array('showkey' => $showkey, 'uniacid' => $_W['uniacid'], 'openid' => $agentinfo['openid'], 'coupondataid' => $coupondataid);
				pdo_insert('ewei_shop_coupon_sendshow', $data);
				show_json(1);
			}else{
				show_json(0,'没有开启消费送券');
			}
		}
		if (empty($member['merchid'])) {
			$this->message('非商户没有权限!', mobileUrl('member'));
		}else{
			if (empty($merch)) {
				$this->message('绑定商户号不存在，请联系平台处理!', mobileUrl('member'));
			}
		}
		if (empty($coupon['backrate'])||$coupon['backrate']<=0) {
			$this->message('没有开启消费送券', mobileUrl('member'));
		}
		include $this->template();
	}
}
?>
