<?php

if (!defined('IN_IA')) {
	exit('Access Denied');
}

require EWEI_SHOPV2_PLUGIN . 'app/core/page_mobile.php';
class Share_EweiShopV2Page extends AppMobilePage
{
	public function send()
	{
		global $_W;
		global $_GPC;

		$params=array(':uniacid'=>$_W['uniacid']);

		$sql="SELECT * FROM ".tablename('ewei_shop_wxapp_sendticket')." WHERE uniacid=:uniacid";

		$item = pdo_fetch($sql,$params);


		if ($item['status']==1) {
			if ($item['expiration'] == 1) {
				if ($item['endtime'] < TIMESTAMP) {
					$status = array('status' => 0);
					pdo_update('ewei_shop_wxapp_sendticket', $status, array('id' => $item['id']));
					return false;
				}
			}


			$couponArr =explode(',',$item['cpid']);

			if ($couponArr) {
			 	$count=0;
			 	$coupon_list=array();

			 	foreach ($couponArr as $value) {

			 		/*统计改用户已分享次数*/
					$sql="SELECT count(*) FROM ".tablename("ewei_shop_coupon")." as l LEFT JOIN ".tablename("ewei_shop_coupon_data")." as d ON l.id = d.couponid WHERE d.openid='{$_W[openid]}' AND d.uniacid='{$_W[uniacid]}' AND d.gettype=14 AND d.couponid='{$value}'";

	
					$currentTotal = pdo_fetchcolumn($sql);

					if ($item['getmax']==0) {

						
					}else{

						if ($currentTotal>=$item['getmax']) {
							return false;
						}
						
					}

			 		
			 		if ($this->userAddCoupon($value)) {

			 			$count++;
			 			$coupon= pdo_fetch("SELECT * FROM".tablename('ewei_shop_coupon')." WHERE uniacid
			 				='{$_W[uniacid]}' AND id = '{$value}'");

			 			$coupon_list[]=$coupon;
			 		}
			 	}

			 	if ($count==0) {
			 		app_error(0,"没有发送优惠券");
			 	}

			 	$result = array(

			 		'coupon_list'=>$coupon_list,
			 	);

			 	app_json($result);

			 } 
		}else{

			app_error(0,"没有活动");
		}
		
	}

	public function userAddCoupon($couponid)
	{
		global $_W;
		global $_GPC;

		$openid = $_W['openid']; 

		$member = m('member')->getMember($openid);

		if (!$member) {
			 return false;
		}


		$sql = "SELECT * FROM ".tablename('ewei_shop_coupon')." WHERE uniacid=:uniacid AND id=:id";
		$params = array(':uniacid'=>$_W['uniacid'],':id'=>$couponid);

		$coupon=pdo_fetch($sql,$params);  

		if (!$coupon) {
			return false;
		}

		$time=time();

		$log = array('uniacid' => $_W['uniacid'], 'merchid' => $coupon['merchid'], 'openid' => $openid, 'logno' => m('common')->createNO('coupon_log', 'logno', 'CC'), 'couponid' => $couponid, 'status' => 1, 'paystatus' => -1, 'creditstatus' => -1, 'createtime' => $time, 'getfrom' => 0);
		pdo_insert('ewei_shop_coupon_log', $log);
		$logid = pdo_insertid();

		if ($logid) {
			$data = array('uniacid' => $_W['uniacid'], 'merchid' => $coupon['merchid'], 'openid' => $openid, 'couponid' => $couponid, 'gettype' => 14, 'gettime' => $time, 'senduid' => $_W['uid']);
			pdo_insert('ewei_shop_coupon_data', $data);
			return true;

		}
		
	}

	public function getStatus(){
		global $_W,$_GPC;

		$money = str_replace(',', '', $_GPC['money']);
		$openid = $_W['openid'];
		$activity = $this->activity($money);

		if (!empty($activity)) {
			$arr['status'] = 'success';
			$arr['did'] = $activity['id'];
			$arr['orderid'] = $_GPC['orderid'];
			echo json_encode($arr);
			exit();
		}
		else {
			app_error(-1, "活动已经结束！");
		}

	}


		public function sendTicket($openid, $couponid, $gettype = 0, $num, $ident = '')
	{
		global $_W;
		global $_GPC;
		$couponlog = array('uniacid' => $_W['uniacid'], 'openid' => $openid, 'logno' => m('common')->createNO('coupon_log', 'logno', 'CC'), 'couponid' => $couponid, 'status' => 1, 'paystatus' => -1, 'creditstatus' => -1, 'createtime' => time(), 'getfrom' => 3);
		$i = 0;

		while ($i < $num) {
			$log = pdo_insert('ewei_shop_coupon_log', $couponlog);
			++$i;
		}

		$tmp = range(0, 20);
		$data = array('uniacid' => $_W['uniacid'], 'openid' => $openid, 'couponid' => $couponid, 'gettype' => $gettype, 'gettime' => time(), 'shareident' => $ident);
		$i = 0;

		while ($i < $num) {
			$data['textkey'] = array_rand($tmp, 1);
			$datas = pdo_insert('ewei_shop_coupon_data', $data);
			++$i;
		}

		if ($log && $datas) {
			return true;
		}

		return false;
	}

	public function shareCoupon()
	{
		global $_W;
		global $_GPC;

		$shareOpenid = $_GPC['shareopenid'];
		$openid = $_W['openid'];

		if ($shareOpenid == $openid) {
			$data = array('isshare' => 1);
			pdo_update('ewei_shop_order', $data, array('id' => intval($_GPC['orderid'])));
			show_json('-1',array('shareident' => $_GPC['ident'],'sid'=>$_GPC['sid'],'shareurl'=>1));
			exit();
		}

		$csql = 'SELECT * FROM ' . tablename('ewei_shop_coupon_data') . ' WHERE uniacid = ' . intval($_W['uniacid']) . ' AND shareident = "' . trim($_GPC['ident']) . '" AND openid = "' . $openid . '"';
		$clist = pdo_fetchall($csql);


		if (!empty($clist)) {
			show_json('-1',array('shareident' => $_GPC['ident']));
			exit();
		}

		$asql = 'SELECT * FROM ' . tablename('ewei_shop_sendticket_share') . ' WHERE uniacid = ' . intval($_W['uniacid']) . ' AND id = ' . intval($_GPC['sid']) . ' AND status = 1 AND (expiration = 0 OR (expiration = 1 AND endtime >= ' . TIMESTAMP . '))';
		$activity = pdo_fetch($asql);


		if (!empty($activity)) {
			$cpid = array();
			$nums = array();

			if (!empty($activity['sharecpid1'])) {
				$cpid[] = $activity['sharecpid1'];
				$nums[] = $activity['sharecpnum1'];
			}

			if (!empty($activity['sharecpid2'])) {
				$cpid[] = $activity['sharecpid2'];
				$nums[] = $activity['sharecpnum2'];
			}

			if (!empty($activity['sharecpid3'])) {
				$cpid[] = $activity['sharecpid3'];
				$nums[] = $activity['sharecpnum3'];
			}

			foreach ($cpid as $cpks => $cpvs) {

				$insertid = $this->sendTicket($openid, $cpvs, 15, $nums[$cpks], trim($_GPC['ident']));
			}

			app_json(array('shareident' => $_GPC['ident'], 'sid' => $_GPC['sid'],'shareurl'=>1));
			exit();
		}
		else {
			show_json('-1',array('shareident' => $_GPC['ident'], 'close' => 1));
			exit();
		}
	}

	public function activity($money)
	{
		global $_W;
		$sql = 'SELECT * FROM ' . tablename('ewei_shop_sendticket_share') . ' WHERE uniacid = ' . intval($_W['uniacid']) . ' AND status = 1 AND (enough = ' . $money . ' OR enough <= ' . $money . ') AND (expiration = 0 OR (expiration = 1 AND endtime >= ' . TIMESTAMP . ')) ORDER BY enough DESC,createtime DESC LIMIT 1';
		$activity = pdo_fetch($sql);
		return $activity;
	}

	public function sharePage()
	{
		global $_W;
		global $_GPC;
		$openid = $_W['openid'];
		//$openid="oFU8i0RQcitWySWq-wRn83gTdXJg";
		if ($_GPC['close'] == 1) {
			$close = 1;
		}
		else {
			$close = 0;

			if (!empty($_GPC['sid'])) {
				$asql = 'SELECT * FROM ' . tablename('ewei_shop_sendticket_share') . ' WHERE uniacid = ' . intval($_W['uniacid']) . ' AND id = ' . intval($_GPC['sid']) . ' AND status = 1 AND (expiration = 0 OR (expiration = 1 AND endtime >= ' . TIMESTAMP . '))';
				$activity = pdo_fetch($asql);
				$cpid = array();
				$nums = array();

				if (!empty($activity['sharecpid1'])) {
					$cpid[] = $activity['sharecpid1'];
					$nums[] = $activity['sharecpnum1'];
				}

				if (!empty($activity['sharecpid2'])) {
					$cpid[] = $activity['sharecpid2'];
					$nums[] = $activity['sharecpnum2'];
				}

				if (!empty($activity['sharecpid3'])) {
					$cpid[] = $activity['sharecpid3'];
					$nums[] = $activity['sharecpnum3'];
				}

				if (!empty($cpid)) {
					$newCoupon = array();

					foreach ($cpid as $ck => $cv) {
						$couponsql = 'SELECT * FROM ' . tablename('ewei_shop_coupon') . ' WHERE uniacid = ' . intval($_W['uniacid']) . ' AND id = ' . $cv;
						$newCoupon[$ck] = pdo_fetch($couponsql);
						$newCoupon[$ck]['cpnum'] = $nums[$ck];
					}
				}

				$num = intval(array_sum($nums));
			}
		}

		$firendsql = 'SELECT c.backtype,c.deduct,c.backmoney,c.backredpack,c.discount,c.backcredit,d.id as did,d.couponid,d.openid,d.gettime,d.textkey,d.shareident FROM ' . tablename('ewei_shop_coupon_data') . ' d,' . tablename('ewei_shop_coupon') . ' c WHERE c.id = d.couponid AND d.uniacid = ' . intval($_W['uniacid']) . ' AND d.gettype = 15 AND d.shareident = "' . trim($_GPC['shareident']) . '" GROUP BY d.openid ORDER BY d.id ASC limit 20';
		$firendlist = pdo_fetchall($firendsql);

		foreach ($firendlist as $fk => $fv) {
			$member = m('member')->getMember($fv['openid']);
			$firendlist[$fk]['headimg'] = $member['avatar'];
			$firendlist[$fk]['nickname'] = $member['nickname'];
			$firendlist[$fk]['gettime']=date('m-d H:i',$fv['gettime']);
			$firendlist[$fk]['text'] = $this->textClause($fv['textkey']);
		}

		$firendlist = array_reverse($firendlist);

		$result=array('close'=>$close,'firendlist'=>$firendlist,'num'=>$num,'newCoupon'=>$newCoupon);
		app_json($result);

	
	}

	public function unclaimed()
	{
		global $_W;
		global $_GPC;
		$openid = $_W['openid'];


		if (!empty($_GPC['sid'])) {
			$asql = 'SELECT * FROM ' . tablename('ewei_shop_sendticket_share') . ' WHERE uniacid = ' . intval($_W['uniacid']) . ' AND id = ' . intval($_GPC['sid']) . ' AND status = 1 AND (expiration = 0 OR (expiration = 1 AND endtime >= ' . TIMESTAMP . '))';
			$active = pdo_fetch($asql);
		}
		else {
			$active = $this->activity($_GPC['money']);

			$_GPC['sid'] = $active['id'];
		}

		if (empty($active)) {
			$result=array('shareident' => $_GPC['ident'], 'close' => 1);
			show_json('-1',$result);

		}

		$cpid = array();
		$nums = array();

		if (!empty($active['paycpid1'])) {
			$cpid[] = $active['paycpid1'];
			$nums[] = $active['paycpnum1'];
		}

		if (!empty($active['paycpid2'])) {
			$cpid[] = $active['paycpid2'];
			$nums[] = $active['paycpnum2'];
		}

		if (!empty($active['paycpid3'])) {
			$cpid[] = $active['paycpid3'];
			$nums[] = $active['paycpnum3'];
		}

		if (!empty($cpid)) {
			$newCoupon = array();

			foreach ($cpid as $ck => $cv) {
				$couponsql = 'SELECT * FROM ' . tablename('ewei_shop_coupon') . ' WHERE uniacid = ' . intval($_W['uniacid']) . ' AND id = ' . $cv;
				$newCoupon[$ck] = pdo_fetch($couponsql);
				$newCoupon[$ck]['cpnum'] = $nums[$ck];
			}
		}

		$num = intval(array_sum($nums));
		$storesql = 'SELECT * FROM ' . tablename('ewei_shop_sysset') . ' WHERE uniacid = ' . intval($_W['uniacid']);
		$store = pdo_fetch($storesql);
		$storeInfo = iunserializer($store['sets']);
		$ident = 'rrsc' . date('Ymd', time()) . intval($_GPC['orderid']);
		$result = array('sid' => intval($_GPC['sid']), 'ident' => $ident,'num'=>$num,'orderid'=>$_GPC['orderid']);
		app_json($result);
	}

	public function shareSuccess()
	{
		global $_W;
		global $_GPC;
		$openid = $_W['openid'];
		$orderSql = 'SELECT isshare FROM ' . tablename('ewei_shop_order') . ' WHERE uniacid = ' . intval($_W['uniacid']) . ' AND id = ' . intval($_GPC['orderid']);
		$orderData = pdo_fetch($orderSql);

		if ($orderData['isshare'] == 1) {
			return false;
		}

		$couponDataSql = 'SELECT * FROM ' . tablename('ewei_shop_coupon_data') . ' WHERE uniacid = ' . intval($_W['uniacid']) . ' AND shareident = "' . $_GPC['ident'] . '"';
		$couponData = pdo_fetchall($couponDataSql);

		if ($couponData) {
			return false;
		}

		$asql = 'SELECT * FROM ' . tablename('ewei_shop_sendticket_share') . ' WHERE uniacid = ' . intval($_W['uniacid']) . ' AND id = ' . intval($_GPC['sid']) . ' AND status = 1 AND (expiration = 0 OR (expiration = 1 AND endtime >= ' . TIMESTAMP . '))';
		$activity = pdo_fetch($asql);
		$cpid = array();
		$nums = array();

		if (!empty($activity['paycpid1'])) {
			$cpid[] = $activity['paycpid1'];
			$nums[] = $activity['paycpnum1'];
		}

		if (!empty($activity['paycpid2'])) {
			$cpid[] = $activity['paycpid2'];
			$nums[] = $activity['paycpnum2'];
		}

		if (!empty($activity['paycpid3'])) {
			$cpid[] = $activity['paycpid3'];
			$nums[] = $activity['paycpnum3'];
		}

		$insertid = array();

		foreach ($cpid as $cpks => $cpvs) {
			$insertid[] = $this->sendTicket($openid, $cpvs, 15, $nums[$cpks], $_GPC['ident']);
		}

		$data = array('isshare' => 1);
		pdo_update('ewei_shop_order', $data, array('id' => intval($_GPC['orderid'])));

		if ($insertid) {
			return true;
		}

		return false;
	}


	public function enjoy()
	{
		global $_W;
		global $_GPC;
		$openid = $_W['openid'];

		if (!empty($_GPC['sid'])) {
			$asql = 'SELECT * FROM ' . tablename('ewei_shop_sendticket_share') . ' WHERE uniacid = ' . intval($_W['uniacid']) . ' AND id = ' . intval($_GPC['sid']) . ' AND status = 1 AND (expiration = 0 OR (expiration = 1 AND endtime >= ' . TIMESTAMP . '))';
			$active = pdo_fetch($asql);
		}
		else {
			$active = $this->activity($_GPC['money']);
			$_GPC['sid'] = $active['id'];	
		}

		if (empty($active)) {

			app_error(1, '活动已经结束！');
		}

		

		$csql = 'SELECT * FROM ' . tablename('ewei_shop_coupon_data') . ' WHERE uniacid = ' . intval($_W['uniacid']) . ' AND shareident = "' . trim($_GPC['ident']) . '" AND openid = "' . $openid . '"';
		$clist = pdo_fetchall($csql);
		if (!empty($clist)) {
			$newCoupon = array();
		}
		else {

			$insertData = $this->shareSuccess();
			$cpid = array();
			$nums = array();

			if (!empty($active['paycpid1'])) {
				$cpid[] = $active['paycpid1'];
				$nums[] = $active['paycpnum1'];
			}

			if (!empty($active['paycpid2'])) {
				$cpid[] = $active['paycpid2'];
				$nums[] = $active['paycpnum2'];
			}

			if (!empty($active['paycpid3'])) {
				$cpid[] = $active['paycpid3'];
				$nums[] = $active['paycpnum3'];
			}

			if (!empty($cpid)) {
				$newCoupon = array();

				foreach ($cpid as $ck => $cv) {
					$couponsql = 'SELECT * FROM ' . tablename('ewei_shop_coupon') . ' WHERE uniacid = ' . intval($_W['uniacid']) . ' AND id = ' . $cv;
					$newCoupon[$ck] = pdo_fetch($couponsql);
					$newCoupon[$ck]['cpnum'] = $nums[$ck];
				}
			}

			$num = intval(array_sum($nums));
		}

		$storesql = 'SELECT * FROM ' . tablename('ewei_shop_sysset') . ' WHERE uniacid = ' . intval($_W['uniacid']);
		$store = pdo_fetch($storesql);
		$storeInfo = iunserializer($store['sets']);
		
		$firendsql = 'SELECT c.backtype,c.deduct,c.backmoney,c.backredpack,c.discount,c.backcredit,d.id as did,d.couponid,d.openid,d.gettime,d.textkey,d.shareident FROM ' . tablename('ewei_shop_coupon_data') . ' d,' . tablename('ewei_shop_coupon') . ' c WHERE c.id = d.couponid AND d.uniacid = ' . intval($_W['uniacid']) . ' AND d.gettype = 15 AND d.shareident = "' . trim($_GPC['ident']) . '" GROUP BY d.openid ORDER BY d.id ASC limit 20';
		$firendlist = pdo_fetchall($firendsql);
		foreach ($firendlist as $fk => $fv) {
			$member = m('member')->getMember($fv['openid']);
			$firendlist[$fk]['headimg'] = $member['avatar'];
			$firendlist[$fk]['nickname'] = $member['nickname'];
			$firendlist[$fk]['gettime']  = date('m-d H:i',$fv['gettime']);
			$firendlist[$fk]['text'] = $this->textClause($fv['textkey']);
		}

		$firendlist = array_reverse($firendlist);

		$result=array('firendlist' =>$firendlist , 'newCoupon'=>$newCoupon,'credittext'=>$_W['shopset']['trade']['credittext']);
		app_json($result);
	}

	public function textClause($key)
	{
		$text = array();
		$text[0] = '我是购物狂，我为自己代言！';
		$text[1] = '花钱如尿裤子一般，痛快!';
		$text[2] = '不肯花大钱就买不来贵重东西!';
		$text[3] = '不花钱是造不成宫殿的!';
		$text[4] = '我是购物狂，我在振兴经济！';
		$text[5] = '现在就买，否则晚点会哭!';
		$text[6] = '没有什么比没买的东西叫人念念不忘的!';
		$text[7] = '消费拉动内需，活跃市场！';
		$text[8] = '购物让人心情愉悦！';
		$text[9] = '懂得花钱和懂得挣钱是幸福的！';
		$text[10] = '爱是个银行，不怕花钱，就怕不存钱！';
		$text[11] = '和朋友在一起消费可以促进感情！';
		$text[12] = '花钱去做广告，不如用实际行动使大家口耳相传。';
		$text[13] = '人生最悲哀的是：人在天堂，钱在银行。';
		$text[14] = '挣钱是本事，花钱是美德。';
		$text[15] = '花钱就是投资，只有懂得如何投资才能得到更好的产出。';
		$text[16] = '会花钱才会更能赚钱。';
		$text[17] = '花钱的速度，决定你赚钱的动力。';
		$text[18] = '花了的钱，才是自己的钱。';
		$text[19] = '挣钱、赚钱只有一个目的，就是花！';
		$text[20] = '一个人，富不富，不在于你存了多少，而在于你使用了多少！';
		return $text[$key];
	}

}

?>
