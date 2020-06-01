<?php
if (!(defined('IN_IA'))) 
{
	exit('Access Denied');
}
require EWEI_SHOPV2_PLUGIN . 'commission/core/page_login_mobile.php';
class Index_EweiShopV2Page extends CommissionMobileLoginPage 
{
	public function main() 
	{
		global $_W;
		global $_GPC;
		$years = array();
		$current_year = date('Y');
		$i = $current_year - 10;
		while ($i <= $current_year) 
		{
			$years[] = $i;
			++$i;
		}
		$months = array();
		$i = 1;
		while ($i <= 12) 
		{
			$months[] = ((strlen($i) == 1 ? '0' . $i : $i));
			++$i;
		}
		$set = $this->getSet();
		$member = m('member')->getMember($_W['openid']);
		$bonus = $this->model->getBonus($_W['openid'], array('ok', 'lock', 'ok1', 'ok2', 'ok3', 'lock1', 'lock2', 'lock3', 'total', 'total1', 'total2', 'total3'));
		$levelname = ((empty($set['levelname']) ? '默认等级' : $set['levelname']));
		$level = m('member')->getLevel($_W['openid']);
		if (!(empty($level))) 
		{
			$levelname = $level['levelname'];
		}
		if ($member['aagenttype'] == 1) 
		{
			$cols = 4;
		}
		else if ($member['aagenttype'] == 2) 
		{
			$cols = 3;
		}
		else if ($member['aagenttype'] == 3) 
		{
			$cols = 2;
		}
		else 
		{
			$cols = 4;
		}
		$bonus_wait = 0;
		$year = date('Y');
		$month = intval(date('m'));
		$week = 0;
		if ($set['paytype'] == 2) 
		{
			$ds = explode('-', date('Y-m-d'));
			$day = intval($ds[2]);
			$week = ceil($day / 7);
		}
		$set = p('groupaward')->getSet();
		$level = m('member')->getLevel($_W['openid']);
		$year = date('Y',time());
		$month =date('m',time());
		if($month==12){
			$month = 1;
		}else{
			$month +=1;
		}
		$days = get_last_day($year, $month);
		$starttime = strtotime($year . '-' . $month . '-1');
		$endtime = strtotime($year . '-' . $month . '-' . $days);
		$settletimes = intval($set['settledays']) * 86400;
		$orders = pdo_fetch('select sum(price) as totalSale, count(*) as count from ' . tablename('ewei_shop_order') . ' where uniacid=' . $_W['uniacid'] . ' and status>=3 '.$ccondition.' and finishtime + ' . $settletimes . '>= ' . $starttime . ' and  finishtime + ' . $settletimes . '<=' . $endtime);
		$get_leveltotal = pdo_fetchcolumn('select count(*) from '.tablename('ewei_shop_member').' where uniacid=:uniacid and level=:level',array(':uniacid'=>$_W['uniacid'],':level'=>$level['id']));
		$total = round($orders['totalSale']*$set['awardRate'][$level['level']]*0.01/$get_leveltotal,2);
		$count_total = pdo_fetchcolumn('select sum(realProfit) from '.tablename('ewei_shop_groupaward_billp').' where uniacid=:uniacid and openid=:openid',array(':uniacid'=>$_W['uniacid'],':openid'=>$_W['openid']));
		$count_wait = pdo_fetchcolumn('select sum(realProfit) from '.tablename('ewei_shop_groupaward_billp').' where uniacid=:uniacid and openid=:openid and paytime=0',array(':uniacid'=>$_W['uniacid'],':openid'=>$_W['openid']));
		$count_ok = pdo_fetchcolumn('select sum(realProfit) from '.tablename('ewei_shop_groupaward_billp').' where uniacid=:uniacid and openid=:openid and paytime>0',array(':uniacid'=>$_W['uniacid'],':openid'=>$_W['openid']));
		include $this->template();
	}
	public function get_total()
	{
		global $_W,$_GPC;
		$set = p('groupaward')->getSet();
		if(!empty($_GPC['year']) &&!empty($_GPC['moth'])){
			$level = m('member')->getLevel($_W['openid']);
			$year = $_GPC['year'];
			$month = $_GPC['moth'];
			$days = get_last_day($year, $month);
			$starttime = strtotime($year . '-' . $month . '-1');
			$endtime = strtotime($year . '-' . $month . '-' . $days);
			$settletimes = intval($set['settledays']) * 86400;
			$orders = pdo_fetch('select sum(price) as totalSale, count(*) as count from ' . tablename('ewei_shop_order') . ' where uniacid=' . $_W['uniacid'] . ' and status>=3 '.$ccondition.' and finishtime + ' . $settletimes . '>= ' . $starttime . ' and  finishtime + ' . $settletimes . '<=' . $endtime);
			$get_leveltotal = pdo_fetchcolumn('select count(*) from '.tablename('ewei_shop_member').' where uniacid=:uniacid and level=:level',array(':uniacid'=>$_W['uniacid'],':level'=>$level['id']));
			$total = round($orders['totalSale']*$set['awardRate'][$level['level']]*0.01/$get_leveltotal,2);
			$result = array(
				'leveltotal' => $get_leveltotal,
				'total'=>$total
			);
			// var_dump();exit;
			echo json_encode($result);

		}
	}
}
?>