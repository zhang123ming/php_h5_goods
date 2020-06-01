<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

require EWEI_SHOPV2_PLUGIN . 'commission/core/page_login_mobile.php';
class QueueWithdraw_EweiShopV2Page extends CommissionMobileLoginPage
{
	public function main()
	{
		global $_W;
		global $_GPC;
		$totalall = pdo_fetchcolumn('select sum(amount) from '.tablename('ewei_shop_commission_queuepaylog').' where uniacid=:uniacid and openid=:openid and status=0',array(':uniacid'=>$_W['uniacid'],':openid'=>$_W['openid']));
		$totalall = empty($totalall)?0:$totalall;
		$withdraw = $totalall;
		include $this->template();
	}


	// 免审核提现
	public function pay()
	{
		global $_W;
		global $_GPC;
		if($_GPC['type']){
			if($_GPC['type']==1){
				$credit = 'credit2';
			}else{
				$credit = 'credit1';
			}
			$list = pdo_fetchall('select id,amount from '.tablename('ewei_shop_commission_queuepaylog').' where uniacid=:uniacid and openid=:openid and status=0',array(':uniacid'=>$_W['uniacid'],':openid'=>$_W['openid']));
			$num = 0;
			foreach($list as $v){
				$res = pdo_update('ewei_shop_commission_queuepaylog',array('status'=>1,'paytype'=>$_GPC['type']),array('uniacid'=>$_W['uniacid'],'openid'=>$_W['openid'],'id'=>$v['id']));
				if($res>0){
					if($v['amount']>0){
						if($credit=='credit2'){
							$v['amount'] = $v['amount']*0.5;
						}
						m('member')->setCredit($_W['openid'],$credit,$v['amount'],array('排队奖发放','排队奖发放 时间 '.date('Y-m-d H:y:s',time())));	
								$logno = m('common')->createNO('member_log', 'logno', 'RC');
								$data = array('openid' => $openid,'credittype'=>$credit, 'logno' => $logno, 'uniacid' => $_W['uniacid'], 'type' => '0', 'createtime' => TIMESTAMP, 'status' => '1', 'title' => '排队奖发放', 'money' =>$v['amount'], 'remark' => '排队奖发放', 'rechargetype' => 'system');
						pdo_insert('ewei_shop_member_log', $data);
						$num++;
					}
				}
			}
			if($num>0){
				show_json(1,'提现成功');		
			}
		}
	}
}

?>