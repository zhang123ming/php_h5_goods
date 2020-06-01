<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Rent_EweiShopV2Page extends WebPage
{
	public function main()
	{
		global $_W;
		global $_GPC;
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		if (empty($starttime) || empty($endtime)) {
			$starttime = strtotime('-1 month');
			$endtime = time();
		}
		$condition = " r.uniacid = :uniacid ";
		$params[':uniacid'] = $_W['uniacid'];
		$searchtime = trim($_GPC['searchtime']);
		if (!empty($searchtime) && is_array($_GPC['time']) && in_array($searchtime, array('create', 'begin', 'end'))) {
			$starttime = strtotime($_GPC['time']['start']);
			$endtime = strtotime($_GPC['time']['end']);
			$condition .= ' AND r.' . $searchtime . 'time >= :starttime AND r.' . $searchtime . 'time <= :endtime ';
			$params[':starttime'] = $starttime;
			$params[':endtime'] = $endtime;
		}
		$mcondition = '';
		if (!empty($_GPC['keyword'])) {
			$_GPC['keyword'] = trim($_GPC['keyword']);
			$mcondition = ' and (m.realname like :keyword or m.nickname like :keyword or m.mobile like :keyword or m.id like :keyword)';
			$params[':keyword'] = '%'.$_GPC['keyword'].'%';
		}
		$sql = "select r.*,m.nickname,m.mobile,m.avatar,m.realname,m.id as mid,o.ordersn from ".tablename('ewei_shop_machine_rent_record').' r left join '.tablename('ewei_shop_member').' m on m.uniacid=r.uniacid and m.openid=r.openid left join '.tablename('ewei_shop_order').' o on o.id=r.orderid where '.$condition.$mcondition.' order by id desc limit ' . (($pindex - 1) * $psize) . ',' . $psize;

		$list = pdo_fetchall($sql,$params);
		$arr = array('禁用','永久有效','限期租用','设备到期');
		foreach ($list as &$v) {
			$v['merchname'] = pdo_fetchcolumn('select merchname from '.tablename('ewei_shop_merch_user').' where uniacid=:uniacid and id=:id',array(':uniacid'=>$_W['uniacid'],':id'=>$v['merchid']));
			$v['statusstr'] = $v['status']==-1? $arr[3] : $arr[$v['status']];
		}
		unset($v);
		$total = pdo_fetchcolumn('select count(*) from '.tablename('ewei_shop_machine_rent_record').' r where '.$condition,$params);
		$pager = pagination($total, $pindex, $psize);
		
		load()->func('tpl');
		include $this->template('order/rent');
	}
	public function edit(){
		global $_W,$_GPC;
		$id = $_GPC['id'];
		$item = pdo_fetch("select r.*,m.nickname,m.mobile,m.avatar,m.id as mid from ".tablename('ewei_shop_machine_rent_record').' r left join '.tablename('ewei_shop_member').' m on m.uniacid=r.uniacid and m.openid=r.openid where r.uniacid=:uniacid and r.id=:id',array(':uniacid'=>$_W['uniacid'],':id'=>$id));
		if ($_W['ispost']) {
			$data['periodtime'] = intval($_GPC['periodtime']);
			$data['macid'] = trim($_GPC['macid']);
			$data['status'] = $_GPC['status'];
			if (!empty($item['begintime'])) {
				$data['endtime'] = $item['begintime']+$data['periodtime']*86400;
			}
			pdo_update('ewei_shop_machine_rent_record',$data,array('id'=>$id));
			show_json(1);
		}
		include $this->template('order/rent_post');
	}
	public function rentMsg(){
		global $_W,$_GPC;
		$id = $_GPC['id'];
		$item = pdo_fetch("select message,statusa,id from ".tablename('ewei_shop_machine_rent_record').' where id=:id',array(':id'=>$id));
		$message = empty($item['message']) ? array():unserialize($item['message']);
		if ($_W['ispost']) {
			$message['reply'] = trim($_GPC['reply']);
			if (empty($message['reply'])) {
				show_json(0,'回复不能为空');
			}
			$data['message'] = serialize($message);
			$data['statusa'] = $_GPC['statusa'];
			pdo_update('ewei_shop_machine_rent_record',$data,array('id'=>$id));
			show_json(1);
		}
		include $this->template('order/rent_msg');
	}
}

?>
