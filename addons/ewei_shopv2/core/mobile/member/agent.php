<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Agent_EweiShopV2Page extends MobileLoginPage
{
	public function main()
	{
		global $_W;
		global $_GPC;

		$member = m("member")->getMember($_W['openid']);
		$set = m('common')->getPluginset('merch');
		$cansettle = (1 <= $member['credit3']) && (floatval($set['withdraw']) <= $member['credit3']);

		if($member['level']>0){
			$level = m("member")->getLevel($_W['openid']);
			if($level['level']<90){
				$this->message('您暂无权限进入！');
			}
		} else {
			$this->message('您暂无权限进入！');
		}

		if($level['level']=='91'){
			$level1 = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_member') . ' where agentid=:agentid and uniacid=:uniacid and isagent=1 and status=1 ', array(':agentid' => $member['id'], ':uniacid' => $_W['uniacid']));
			$level1_agentids = pdo_fetchall('select id from ' . tablename('ewei_shop_member') . ' where agentid=:agentid and isagent=1 and status=1 and uniacid=:uniacid ', array(':uniacid' => $_W['uniacid'], ':agentid' => $member['id']), 'id');

			$level2 = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_member') . ' where agentid in( ' . implode(',', array_keys($level1_agentids)) . ') and uniacid=:uniacid', array(':uniacid' => $_W['uniacid']));
			$level2_agentids = pdo_fetchall('select id from ' . tablename('ewei_shop_member') . ' where agentid in( ' . implode(',', array_keys($level1_agentids)) . ') and isagent=1 and status=1 and uniacid=:uniacid', array(':uniacid' => $_W['uniacid']), 'id');
		}
		if($level['level']=='92'){
			$le1 = pdo_fetch('select id,level,levelname from ' . tablename('ewei_shop_member_level') . ' where uniacid=:uniacid and level=:level and enabled=1 limit 1',array(':uniacid'=>$_W['uniacid'],':level'=>91));
			$level1 = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_member') . ' where agentid=:agentid and uniacid=:uniacid and isagent=1 and status=1 and level=:level ', array(':agentid' => $member['id'], ':uniacid' => $_W['uniacid'],':level'=>$le1['id']));
			$level1_agentids = pdo_fetchall('select id from ' . tablename('ewei_shop_member') . ' where agentid=:agentid and isagent=1 and status=1 and uniacid=:uniacid and level=:level ', array(':uniacid' => $_W['uniacid'], ':agentid' => $member['id'],':level'=>$le1['id']), 'id');
		}
		if($level['level']=='93'){
			$le1 = pdo_fetch('select id,level,levelname from ' . tablename('ewei_shop_member_level') . ' where uniacid=:uniacid and level=:level and enabled=1 limit 1',array(':uniacid'=>$_W['uniacid'],':level'=>'92'));
			$le2 = pdo_fetch('select id,level,levelname from ' . tablename('ewei_shop_member_level') . ' where uniacid=:uniacid and level=:level and enabled=1 limit 1',array(':uniacid'=>$_W['uniacid'],':level'=>'91'));

			$level1 = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_member') . ' where agentid=:agentid and uniacid=:uniacid and isagent=1 and status=1 and level=:level ', array(':agentid' => $member['id'], ':uniacid' => $_W['uniacid'],':level'=>$le1['id']));
			$level1_agentids = pdo_fetchall('select id from ' . tablename('ewei_shop_member') . ' where agentid=:agentid and isagent=1 and status=1 and uniacid=:uniacid and level=:level ', array(':uniacid' => $_W['uniacid'], ':agentid' => $member['id'],':level'=>$le1['id']), 'id');

			$level2 = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_member') . ' where agentid in( ' . implode(',', array_keys($level1_agentids)) . ') and uniacid=:uniacid and level=:level', array(':uniacid' => $_W['uniacid'],':level'=>$le2['id']));
			$level2_agentids = pdo_fetchall('select id from ' . tablename('ewei_shop_member') . ' where agentid in( ' . implode(',', array_keys($level1_agentids)) . ') and isagent=1 and status=1 and uniacid=:uniacid and level=:level ', array(':uniacid' => $_W['uniacid'],':level'=>$le2['id']), 'id');
		}
		$downcount = $level1 +$level2;

		$orderprice = pdo_fetchcolumn('select sum(price) from ' . tablename('ewei_shop_order') . ' where uniacid=:uniacid and status=1 and find_in_set(:id,fids)>0 ',array(':uniacid'=>$_W['uniacid'],':id'=>$member['id']));

		$goodsnum = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_goods') . ' where uniacid=:uniacid and type=1 and status=1 and total>0 and nocommission=0 ',array(':uniacid'=>$_W['uniacid']));

		$lognum = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_commission_order_log') . ' where uniacid=:uniacid and openid=:openid ',array(':uniacid'=>$_W['uniacid'],':openid'=>$member['openid']));
		$applynum = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_commission_apply') . ' where uniacid=:uniacid and mid=:mid ',array(':uniacid'=>$_W['uniacid'],':mid'=>$member['id']));
		include $this->template();
	}

	public function goodslist()
	{
		global $_W;
		global $_GPC;

		$member = m("member")->getMember($_W['openid']);
		$mid = $member['id'];
		$commission_data = m('common')->getPluginset('commission');
		$goodslist = pdo_fetchall('select * from ' . tablename('ewei_shop_goods') . ' where uniacid=:uniacid and type=1 and status=1 and total>0 and nocommission=0 ',array(':uniacid'=>$_W['uniacid']));
		foreach ($goodslist as $k => &$v) {
			$goodscode = '';
			$parameter = array();

			$v['thumb'] = tomedia($v['thumb']);

			if (com('goodscode')) {
				if ($v['minprice'] == $v['maxprice']) {
					$price = '¥' . $v['minprice'];
				}
				else {
					$price = '¥' . $v['minprice'] . ' ~ ' . $v['maxprice'];
				}

				$url = mobileUrl('goods/detail', array('id' => $v['id'], 'mid' => $mid), true);
				$qrcode = m('qrcode')->createQrcode($url);

				if ($commission_data['codeShare'] == 1) {
					$title[0] = mb_substr($v['title'], 0, 10, 'utf-8');
					$title[1] = mb_substr($v['title'], 11, 10, 'utf-8');
					$title = '    ' . $title[0] . "\r\n    " . $title[1];
					$codedata = array(
						'portrait' => array('thumb' => tomedia($_W['shopset']['shop']['logo']) ? tomedia($_W['shopset']['shop']['logo']) : tomedia($member['avatar']), 'left' => 40, 'top' => 40, 'width' => 100, 'height' => 100),
						'shopname' => array('text' => $_W['shopset']['shop']['name'], 'left' => 160, 'top' => 80, 'size' => 12, 'width' => 360, 'height' => 50, 'color' => '#333'),
						'thumb'    => array('thumb' => tomedia($v['thumb']), 'left' => 40, 'top' => 160, 'width' => 560, 'height' => 560),
						'qrcode'   => array('thumb' => tomedia($qrcode), 'left' => 23, 'top' => 730, 'width' => 220, 'height' => 220),
						'title'    => array('text' => $title, 'left' => 230, 'top' => 770, 'size' => 24, 'width' => 360, 'height' => 50, 'color' => '#333'),
						'price'    => array('text' => $price, 'left' => 270, 'top' => 880, 'size' => 30, 'color' => '#f20'),
						'desc'     => array('text' => '长按二维码扫码购买', 'left' => 210, 'top' => 980, 'size' => 18, 'color' => '#666')
						);
				}
				else if ($commission_data['codeShare'] == 2) {
					$title[0] = mb_substr($v['title'], 0, 14, 'utf-8');
					$title[1] = mb_substr($v['title'], 15, 14, 'utf-8');
					$title = '    ' . $title[0] . "\r\n    " . $title[1];
					$codedata = array(
						'thumb'    => array('thumb' => tomedia($v['thumb']), 'left' => 20, 'top' => 20, 'width' => 150, 'height' => 150),
						'title'    => array('text' => $title, 'left' => 170, 'top' => 30, 'size' => 22, 'width' => 430, 'height' => 90, 'color' => '#333'),
						'price'    => array('text' => $price, 'left' => 210, 'top' => 120, 'size' => 30, 'color' => '#f20'),
						'qrcode'   => array('thumb' => tomedia($qrcode), 'left' => 170, 'top' => 200, 'width' => 300, 'height' => 300),
						'desc'     => array('text' => '长按二维码扫码购买', 'left' => 205, 'top' => 510, 'size' => 18, 'color' => '#666'),
						'shopname' => array('text' => $_W['shopset']['shop']['name'], 'left' => 0, 'top' => 585, 'size' => 28, 'width' => 640, 'height' => 50, 'color' => '#fff')
						);
				}
				else {
					if ($commission_data['codeShare'] == 3) {
						$title[0] = mb_substr($v['title'], 0, 12, 'utf-8');
						$title[1] = mb_substr($v['title'], 13, 12, 'utf-8');
						$title = '                ' . $title[0] . "\r\n                " . $title[1];
						$codedata = array(
							'title'  => array('text' => $title, 'left' => 27, 'top' => 40, 'size' => 22, 'width' => 600, 'height' => 90, 'color' => '#333'),
							'thumb'  => array('thumb' => tomedia($v['thumb']), 'left' => 0, 'top' => 150, 'width' => 640, 'height' => 640),
							'qrcode' => array('thumb' => tomedia($qrcode), 'left' => 20, 'top' => 810, 'width' => 220, 'height' => 220),
							'price'  => array('text' => $price, 'left' => 280, 'top' => 870, 'size' => 30, 'color' => '#000'),
							'desc'   => array('text' => '长按二维码扫码购买', 'left' => 280, 'top' => 950, 'size' => 18, 'color' => '#666')
							);
					}
				}

				$parameter = array('goodsid' => $v['id'], 'qrcode' => $qrcode, 'codedata' => $codedata, 'mid' => $mid, 'codeshare' => $commission_data['codeShare']);
				$goodscode = com('goodscode')->createcode($parameter);
			}
			else {
				if ($v['minprice'] == $v['maxprice']) {
					$price = '¥' . $v['minprice'];
				}
				else {
					$price = '¥' . $v['minprice'] . ' ~ ' . $v['maxprice'];
				}

				$url = mobileUrl('goods/detail', array('id' => $v['id'], 'mid' => $mid), true);
				$qrcode = m('qrcode')->createQrcode($url);

				if ($commission_data['codeShare'] == 1) {
					$shoptitle = m('common')->qrcode_title_substr($_W['shopset']['shop']['name'],34,17);
					$title = m('common')->qrcode_title_substr($v['title'],28,14);
					$codedata = array(
						'portrait' => array('thumb' => tomedia($_W['shopset']['shop']['logo']) ? tomedia($_W['shopset']['shop']['logo']) : tomedia($member['avatar']), 'left' => 40, 'top' => 40, 'width' => 100, 'height' => 100),
						'shopname' => array('text' => $shoptitle, 'left' => 160, 'top' => 60, 'size' => 18, 'width' => 360, 'height' => 50, 'color' => '#333'),
						'thumb'    => array('thumb' => tomedia($v['thumb']), 'left' => 40, 'top' => 160, 'width' => 560, 'height' => 560),
						'qrcode'   => array('thumb' => tomedia($qrcode), 'left' => 23, 'top' => 730, 'width' => 220, 'height' => 220),
						'title'    => array('text' => $title, 'left' => 245, 'top' => 770, 'size' => 18, 'width' => 360, 'height' => 50, 'color' => '#333'),
						'price'    => array('text' => $price, 'left' => 270, 'top' => 880, 'size' => 30, 'color' => '#f20'),
						'desc'     => array('text' => '长按二维码扫码购买', 'left' => 210, 'top' => 980, 'size' => 18, 'color' => '#666')
						);
				}
				else {
					if ($commission_data['codeShare'] == 2) {
						$title[0] = mb_substr($v['title'], 0, 14, 'utf-8');
						$title[1] = mb_substr($v['title'], 15, 14, 'utf-8');
						$title = '    ' . $title[0] . "\r\n    " . $title[1];
						$codedata = array(
							'thumb'    => array('thumb' => tomedia($v['thumb']), 'left' => 20, 'top' => 20, 'width' => 150, 'height' => 150),
							'title'    => array('text' => $title, 'left' => 170, 'top' => 30, 'size' => 22, 'width' => 430, 'height' => 90, 'color' => '#333'),
							'price'    => array('text' => $price, 'left' => 210, 'top' => 120, 'size' => 30, 'color' => '#f20'),
							'qrcode'   => array('thumb' => tomedia($qrcode), 'left' => 170, 'top' => 200, 'width' => 300, 'height' => 300),
							'desc'     => array('text' => '长按二维码扫码购买', 'left' => 205, 'top' => 510, 'size' => 18, 'color' => '#666'),
							'shopname' => array('text' => $_W['shopset']['shop']['name'], 'left' => 0, 'top' => 585, 'size' => 28, 'width' => 640, 'height' => 50, 'color' => '#fff')
							);
					}
				}

				$parameter = array('goodsid' => $v['id'], 'qrcode' => $qrcode, 'codedata' => $codedata, 'mid' => $mid, 'codeshare' => $commission_data['codeShare']);
				$goodscode = m('goods')->createcode($parameter);
			}
			$v['goodscode'] = $goodscode;
		}
		unset($v);

		include $this->template();
	}

	public function down()
	{
		global $_W;
		global $_GPC;

		$_GPC['level'] = 1;
		$set = m('common')->getPluginset('commission');
		$member = m("member")->getMember($_W['openid']);
		$level = m("member")->getLevel($_W['openid']);
		if($level['level']<'91'){
			$this->message('您暂无权限进入！');
		}

		if($level['level']=='91'){
			$l1 = '一级';
			$l2 = '二级';

			$level1 = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_member') . ' where agentid=:agentid and uniacid=:uniacid and isagent=1 and status=1 ', array(':agentid' => $member['id'], ':uniacid' => $_W['uniacid']));
			$level1_agentids = pdo_fetchall('select id from ' . tablename('ewei_shop_member') . ' where agentid=:agentid and isagent=1 and status=1 and uniacid=:uniacid ', array(':uniacid' => $_W['uniacid'], ':agentid' => $member['id']), 'id');

			$level2 = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_member') . ' where agentid in( ' . implode(',', array_keys($level1_agentids)) . ') and uniacid=:uniacid', array(':uniacid' => $_W['uniacid']));
			$level2_agentids = pdo_fetchall('select id from ' . tablename('ewei_shop_member') . ' where agentid in( ' . implode(',', array_keys($level1_agentids)) . ') and isagent=1 and status=1 and uniacid=:uniacid', array(':uniacid' => $_W['uniacid']), 'id');
		}
		if($level['level']=='92'){
			$l1 = '区代';
			$le1 = pdo_fetch('select id,level,levelname from ' . tablename('ewei_shop_member_level') . ' where uniacid=:uniacid and level=:level and enabled=1 limit 1',array(':uniacid'=>$_W['uniacid'],':level'=>91));

			$level1 = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_member') . ' where agentid=:agentid and uniacid=:uniacid and isagent=1 and status=1 and level=:level ', array(':agentid' => $member['id'], ':uniacid' => $_W['uniacid'],':level'=>$le1['id']));
			$level1_agentids = pdo_fetchall('select id from ' . tablename('ewei_shop_member') . ' where agentid=:agentid and isagent=1 and status=1 and uniacid=:uniacid and level=:level ', array(':uniacid' => $_W['uniacid'], ':agentid' => $member['id'],':level'=>$le1['id']), 'id');

			$level2 = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_member') . ' where agentid in( ' . implode(',', array_keys($level1_agentids)) . ') and uniacid=:uniacid', array(':uniacid' => $_W['uniacid']));
			$level2_agentids = pdo_fetchall('select id from ' . tablename('ewei_shop_member') . ' where agentid in( ' . implode(',', array_keys($level1_agentids)) . ') and isagent=1 and status=1 and uniacid=:uniacid', array(':uniacid' => $_W['uniacid']), 'id');
		}
		if($level['level']=='93'){
			$l1 = '市代';
			$le1 = pdo_fetch('select id,level,levelname from ' . tablename('ewei_shop_member_level') . ' where uniacid=:uniacid and level=:level and enabled=1 limit 1',array(':uniacid'=>$_W['uniacid'],':level'=>'92'));
			$l2 = '区代';
			$le2 = pdo_fetch('select id,level,levelname from ' . tablename('ewei_shop_member_level') . ' where uniacid=:uniacid and level=:level and enabled=1 limit 1',array(':uniacid'=>$_W['uniacid'],':level'=>'91'));

			$level1 = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_member') . ' where agentid=:agentid and uniacid=:uniacid and isagent=1 and status=1 and level=:level ', array(':agentid' => $member['id'], ':uniacid' => $_W['uniacid'],':level'=>$le1['id']));
			$level1_agentids = pdo_fetchall('select id from ' . tablename('ewei_shop_member') . ' where agentid=:agentid and isagent=1 and status=1 and uniacid=:uniacid and level=:level ', array(':uniacid' => $_W['uniacid'], ':agentid' => $member['id'],':level'=>$le1['id']), 'id');

			$level2 = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_member') . ' where agentid in( ' . implode(',', array_keys($level1_agentids)) . ') and uniacid=:uniacid and level=:level', array(':uniacid' => $_W['uniacid'],':level'=>$le2['id']));
			$level2_agentids = pdo_fetchall('select id from ' . tablename('ewei_shop_member') . ' where agentid in( ' . implode(',', array_keys($level1_agentids)) . ') and isagent=1 and status=1 and uniacid=:uniacid and level=:level ', array(':uniacid' => $_W['uniacid'],':level'=>$le2['id']), 'id');
		}

		include $this->template();
	}

	public function getdownlist()
	{
		global $_W;
		global $_GPC;
		global $_S;
		$openid = $_W['openid'];
		$member = p('commission')->getInfo($openid);
		$set = m('common')->getPluginset('commission');
		$total_level = 0;
		$level = intval($_GPC['level']);
		((3 < $level) || ($level <= 0)) && ($level = 1);
		$condition = '';
		$levelcount1 = $member['level1'];
		$levelcount2 = $member['level2'];
		$levelcount3 = $member['level3'];
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;

		$mlevel = m("member")->getLevel($openid);
		if($mlevel['level']=='92'){
			$le1 = pdo_fetch('select id,level,levelname from ' . tablename('ewei_shop_member_level') . ' where uniacid=:uniacid and level=:level and enabled=1 limit 1',array(':uniacid'=>$_W['uniacid'],':level'=>91));
		}
		if($mlevel['level']=='93'){
			$le1 = pdo_fetch('select id,level,levelname from ' . tablename('ewei_shop_member_level') . ' where uniacid=:uniacid and level=:level and enabled=1 limit 1',array(':uniacid'=>$_W['uniacid'],':level'=>'92'));
			$le2 = pdo_fetch('select id,level,levelname from ' . tablename('ewei_shop_member_level') . ' where uniacid=:uniacid and level=:level and enabled=1 limit 1',array(':uniacid'=>$_W['uniacid'],':level'=>'91'));
		}

		if ($level == 1) {
			$condition = ' and agentid=' . $member['id'];
			$hasangent = true;
			if($mlevel['level']>91){
				$total_level = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_member') . ' where agentid=:agentid and uniacid=:uniacid and level=:level limit 1', array(':agentid' => $member['id'], ':uniacid' => $_W['uniacid'],':level'=>$le1['id']));
			} else {
				$total_level = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_member') . ' where agentid=:agentid and uniacid=:uniacid limit 1', array(':agentid' => $member['id'], ':uniacid' => $_W['uniacid']));
			}
		}
		else if ($level == 2) {
			if (empty($levelcount1)) {
				show_json(1, array('list'=> array(),'total'=> 0,'pagesize' => $psize));
			}

			$condition = ' and agentid in( ' . implode(',', array_keys($member['level1_agentids'])) . ')';
			$hasangent = true;
			if($mlevel['level']>91){
				$total_level = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_member') . ' where agentid in( ' . implode(',', array_keys($member['level1_agentids'])) . ') and uniacid=:uniacid and level=:level limit 1', array(':uniacid' => $_W['uniacid'],':level'=>$le2['id']));
			} else {
				$total_level = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_member') . ' where agentid in( ' . implode(',', array_keys($member['level1_agentids'])) . ') and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid']));
			}
		}
		else {
			if ($level == 3) {
				if (empty($levelcount2)) {
					show_json(1, array('list'=>array(),'total'=> 0,'pagesize'=>$psize));
				}

				$condition = ' and agentid in( ' . implode(',', array_keys($member['level2_agentids'])) . ')';
				$hasangent = true;
				$total_level = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_member') . ' where agentid in( ' . implode(',', array_keys($member['level2_agentids'])) . ') and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid']));
			}
		}

		if(!empty($_GPC['levelid'])){
			$condition .= ' and level= ' . $_GPC['levelid'];
		}

		$list = pdo_fetchall('select * from ' . tablename('ewei_shop_member') . ' where uniacid = ' . $_W['uniacid'] . ' ' . $condition . '  ORDER BY isagent desc,id desc limit ' . (($pindex - 1) * $psize) . ',' . $psize);

		foreach ($list as &$row) {
			if ($member['isagent'] && $member['status']) {
				$info = p('commission')->getInfo($row['openid'], array('total'));
				$row['agentcount'] = $info['agentcount'];
				$row['agenttime'] = date('Y-m-d H:i', $row['agenttime']);
			}

			$ulevel = m("member")->getLevel($row['openid']);
			$row['ulevelname'] = $ulevel['levelname'];

			if($ulevel['level']>=91){
				$sqls = "select sum(og.realprice) from " . tablename('ewei_shop_order_goods') . " og left join " . tablename('ewei_shop_order') . ' o on o.uniacid=og.uniacid and o.id=og.orderid ' . ' left join ' . tablename('ewei_shop_commission_order_log') . ' l on l.uniacid=og.uniacid and l.goodsid=og.goodsid and l.orderid=o.id ' . ' where og.uniacid=:uniacid and o.status>=1 and l.money>0 and l.issend>=0 and find_in_set(:id,o.fids) and l.openid=:openid ';
				$vparams = array(':uniacid'=>$_W['uniacid'],':id'=>$row['id'],':openid'=>$row['openid']);
				$moneycount = pdo_fetchcolumn($sqls,$vparams);

				$sqls = "select o.id from " . tablename('ewei_shop_order') . ' o left join ' . tablename('ewei_shop_commission_order_log') . ' l on l.uniacid=o.uniacid and l.orderid=o.id ' . ' where o.uniacid=:uniacid and o.status>=1 and l.money>0 and find_in_set(:id,o.fids) and l.openid=:openid group by o.id ';
				$params = array(':uniacid'=>$_W['uniacid'],':id'=>$row['id'],':openid'=>$row['openid']);
				$ordercount = pdo_fetchall($sqls,$params);
				$row['ordercount'] = number_format(intval(count($ordercount)), 0);
			} else {
				$sqls = "select o.id from " . tablename('ewei_shop_order') . ' o left join ' . tablename('ewei_shop_commission_order_log') . ' l on l.uniacid=o.uniacid and l.orderid=o.id ' . ' where o.uniacid=:uniacid and o.openid=:uopenid and o.status>=1 and l.money>0 and find_in_set(:id,o.fids) and l.openid=:openid ';
				$params = array(':uniacid'=>$_W['uniacid'],':uopenid'=>$row['openid'],':id'=>$member['id'],':openid'=>$member['openid']);
				$ordercount = pdo_fetchcolumn($sqls,$params);
				$row['ordercount'] = $ordercount?intval(count($ordercount)):0;
				
				$sqls = "select sum(og.realprice) from " . tablename('ewei_shop_order_goods') . " og left join " . tablename('ewei_shop_order') . ' o on o.uniacid=og.uniacid and o.id=og.orderid ' . ' left join ' . tablename('ewei_shop_commission_order_log') . ' l on l.uniacid=og.uniacid and l.goodsid=og.goodsid and l.orderid=o.id ' . ' where og.uniacid=:uniacid and o.status>=1 and l.money>0 and l.issend>=0 and find_in_set(:id,o.fids) and o.openid=:uopenid and l.openid=:openid ';
				$params = array(':uniacid'=>$_W['uniacid'],':id'=>$member['id'],':uopenid'=>$row['openid'],':openid'=>$member['openid']);
				$moneycount = pdo_fetchcolumn($sqls,$params);
			}

			
			$row['moneycount'] = number_format(floatval($moneycount), 2);
			$row['islevel'] = 1;

			$row['createtime'] = date('Y-m-d H:i', $row['createtime']);
		}
		unset($row);
		
		show_json(1, array('list' => $list, 'total' => $total_level, 'pagesize' => $psize));
	}

	public function order()
	{
		global $_W;
		global $_GPC;

		$userid = intval($_GPC['userid']);
		$agent = m("member")->getMember($_W['openid']);
		$member = pdo_fetch('select id,openid,nickname from ' . tablename('ewei_shop_member') . ' where uniacid=:uniacid and id=:id limit 1',array(':uniacid'=>$_W['uniacid'],':id'=>$userid));
		$level = m("member")->getLevel($member['openid']);
		if($level['level']>=91){
			$sqls = "select sum(og.realprice) from " . tablename('ewei_shop_order_goods') . " og left join " . tablename('ewei_shop_order') . ' o on o.uniacid=og.uniacid and o.id=og.orderid ' . ' left join ' . tablename('ewei_shop_commission_order_log') . ' l on l.uniacid=og.uniacid and l.goodsid=og.goodsid and l.orderid=o.id ' . ' where og.uniacid=:uniacid and o.status>=1 and l.money>0 and l.issend>=0 and find_in_set(:id,o.fids) and l.openid=:openid ';
			$vparams = array(':uniacid'=>$_W['uniacid'],':id'=>$member['id'],':openid'=>$member['openid']);
			$orderprice = pdo_fetchcolumn($sqls,$vparams);
		} else {
			$sqls = "select sum(og.realprice) from " . tablename('ewei_shop_order_goods') . " og left join " . tablename('ewei_shop_order') . ' o on o.uniacid=og.uniacid and o.id=og.orderid ' . ' left join ' . tablename('ewei_shop_commission_order_log') . ' l on l.uniacid=og.uniacid and l.goodsid=og.goodsid and l.orderid=o.id ' . ' where og.uniacid=:uniacid and o.status>=1 and l.money>0 and l.issend>=0 and find_in_set(:id,o.fids) and o.openid=:uopenid and l.openid=:openid ';
			$vparams = array(':uniacid'=>$_W['uniacid'],':id'=>$agent['id'],':uopenid'=>$member['openid'],':openid'=>$agent['openid']);
			$orderprice = pdo_fetchcolumn($sqls,$vparams);
		}

		include $this->template();
	}

	public function getorderlist()
	{
		global $_W;
		global $_GPC;

		$userid = intval($_GPC['userid']);
		$agent = m("member")->getMember($_W['openid']);
		$member = pdo_fetch('select id,openid,nickname from ' . tablename('ewei_shop_member') . ' where uniacid=:uniacid and id=:id limit 1',array(':uniacid'=>$_W['uniacid'],':id'=>$userid));
		$level = m("member")->getLevel($member['openid']);

		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		if($level['level']>=91){
			$sqls = "select o.id,o.price,o.ordersn,o.createtime from " . tablename('ewei_shop_order_goods') . " og left join " . tablename('ewei_shop_order') . ' o on o.uniacid=og.uniacid and o.id=og.orderid ' . ' left join ' . tablename('ewei_shop_commission_order_log') . ' l on l.uniacid=og.uniacid and l.goodsid=og.goodsid and l.orderid=o.id ' . ' where og.uniacid=:uniacid and o.status>=1 and l.money>0 and l.issend>=0 and find_in_set(:id,o.fids) and l.openid=:openid group by o.id order by o.id desc limit ' . (($pindex - 1) * $psize) . ',' . $psize;
			$params = array(':uniacid'=>$_W['uniacid'],':id'=>$member['id'],':openid'=>$member['openid']);
		} else {
			$sqls = "select o.id,o.price,o.ordersn,o.createtime from " . tablename('ewei_shop_order') . ' o left join ' . tablename('ewei_shop_commission_order_log') . ' l on l.uniacid=o.uniacid and l.orderid=o.id ' . ' where o.uniacid=:uniacid and o.status>=1 and l.money>0 and find_in_set(:id,o.fids) and l.openid=:openid order by o.id desc limit ' . (($pindex - 1) * $psize) . ',' . $psize;
			$sqls = "select o.id,o.price,o.ordersn,o.createtime from " . tablename('ewei_shop_order_goods') . " og left join " . tablename('ewei_shop_order') . ' o on o.uniacid=og.uniacid and o.id=og.orderid ' . ' left join ' . tablename('ewei_shop_commission_order_log') . ' l on l.uniacid=og.uniacid and l.goodsid=og.goodsid and l.orderid=o.id ' . ' where og.uniacid=:uniacid and o.status>=1 and l.money>0 and l.issend>=0 and find_in_set(:id,o.fids) and o.openid=:uopenid and l.openid=:openid group by o.id order by o.id desc limit ' . (($pindex - 1) * $psize) . ',' . $psize;
			$params = array(':uniacid'=>$_W['uniacid'],':id'=>$agent['id'],':uopenid'=>$member['openid'],':openid'=>$agent['openid']);
		}
		
		$list = pdo_fetchall($sqls,$params);

		foreach ($list as &$v) {
			$v['createtime'] = date('Y-m-d',$v['createtime']);
			$goods = pdo_fetchall('SELECT og.id,og.goodsid,g.thumb,og.price,og.realprice,og.total,g.title,og.optionname,' . 'og.commission1,og.commission2,og.commission3,og.commissions,' . 'og.status1,og.status2,og.status3,' . 'og.content1,og.content2,og.content3 from ' . tablename('ewei_shop_order_goods') . ' og' . ' left join ' . tablename('ewei_shop_goods') . ' g on g.id=og.goodsid  ' . ' where og.orderid=:orderid and og.nocommission=0 and og.uniacid = :uniacid order by og.createtime  desc ', array(':uniacid' => $_W['uniacid'], ':orderid' => $v['id']));
			$goods = set_medias($goods, 'thumb');
			$v['goods'] = $goods;
		}
		unset($v);

		$sqls = "select o.id,o.ordersn from " . tablename('ewei_shop_order') . ' o left join ' . tablename('ewei_shop_commission_order_log') . ' l on l.uniacid=o.uniacid and l.orderid=o.id ' . ' where o.uniacid=:uniacid and o.status=>3 and l.money>0 and find_in_set(:id,o.fids) order by o.id desc ';
		$params = array(':uniacid'=>$_W['uniacid'],':id'=>$member['id']);
		$alllist = pdo_fetchall($sqls,$params);
		show_json(1, array('list' => $list, 'pagesize' => $psize, 'total' => count($alllist)));
	}

	public function apply()
	{
		global $_W;
		global $_GPC;
		$mid = intval($_GPC['mid']);
		$member = m("member")->getMember($_W['openid']);
		$level = m('member')->getLevel($_W['openid']);
		if($level['level']>=91){
			$url = mobileUrl('member/agent',array(),true);
			header('Location: '.$url);
			return;
			// $this->message('您已成为代理,无需申请！');
		}

		//检测申请内容
		$apply = pdo_fetch('select * from ' . tablename('ewei_shop_member_apply') . ' where uniacid=:uniacid and openid=:openid and status>=0 order by createtime desc limit 1',array(':uniacid'=>$_W['uniacid'],':openid'=>$_W['openid']));
		if(!empty($apply)){
			if($apply['status']==0){
				$this->message('您的申请正在审核，请耐心等待！');
			}
			if($apply['status']>1){
				$url = mobileUrl('member/agent',array(),true);
				header('Location: '.$url);
				return;
			}
		}

		if(empty($mid)){
			$this->message('请扫描推广码进入！');
		}
		$ordernum = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_order') . ' where uniacid=:uniacid and openid=:openid and status>=0 ',array(':uniacid'=>$_W['uniacid'],':openid'=>$_W['openid']));
		$downnum = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_member') . ' where uniacid=:uniacid and agentid=:agentid ',array(':uniacid'=>$_W['uniacid'],':agentid'=>$member['id']));
		if($ordernum || $downnum){
			$this->message('您不是新会员，无法申请！');
		}
		if(!empty($mid) && $mid==$member['id']){
			$this->message('您无法扫描自己的推广码！');
		}
		if($member['agentid']!=$mid && !empty($member['agentid'])){
			$this->message('您的推荐人不一致');
		}
		//新会员无上级更新推荐关系
		if(empty($member['agentid'])){
			pdo_update('ewei_shop_member',array('agentid'=>$mid),array('uniacid'=>$_W['uniacid'],'openid'=>$member['openid']));
			//更新关系
			$teamlist = pdo_fetchall('select * from '.tablename('ewei_shop_member').' where uniacid=:uniacid and find_in_set('.$member['id'].',fids) order by fids asc',array(':uniacid'=>$_W['uniacid']));
			foreach ($teamlist as $k => $v) {
				$agentdata1 = p('commission')->getAgentId($v['agentid'],$v);
				pdo_update('ewei_shop_member',$agentdata1,array('id'=>$v['id']));
			}
			//查询会员数据
			$member = m("member")->getMember($member['openid']);
		}
		$agent = pdo_fetch('select * from ' . tablename('ewei_shop_member') . ' where uniacid=:uniacid and id=:id limit 1',array(':uniacid'=>$_W['uniacid'],':id'=>$mid));

		if(empty($_GPC['level'])){
			$this->message('参数错误，请重新扫码或重新生成推广码！');
		}

		//查询等级
		$l = intval($_GPC['level']);
		$level = pdo_fetch('select * from ' . tablename('ewei_shop_member_level') . ' where uniacid=:uniacid and level=:level and enabled=1 limit 1',array(':uniacid'=>$_W['uniacid'],':level'=>$l));
		if(empty($level)){
			$this->message('暂无推荐的等级，请重新扫码！');
		}

		if ($_W['ispost']) {
			$mobile = trim($_GPC['mobile']);
			$pwd = trim($_GPC['pwd']);
			$memberdata = array('mid'=>$mid,'mobile'=>$mobile,'pwd'=>$pwd);
			$data = array('uniacid'=>$_W['uniacid'],'openid'=>$member['openid'],'createtime'=>time(),'status'=>0,'level'=>$level['id'],'memberdata'=>serialize($memberdata));
			pdo_insert('ewei_shop_member_apply',$data);
			$applyid = pdo_insertid();
			if($applyid){
				show_json();
			} else {
				show_json(0,'数据保存失败，请刷新重试');
			}
		}

		include $this->template();
	}

	public function qrcode()
	{
		global $_W;
		global $_GPC;

		$openid = $_W['openid'];
		$member = m('member')->getMember($openid);
		$level = m('member')->getLevel($openid);
		if($level['level']<91){
			$this->message('您暂无权限生成代理推广码！');
		}
// 		if($level['level']>91){
// 			$poster = pdo_fetch('select * from ' . tablename('ewei_shop_poster') . ' where uniacid=:uniacid and isagent=1 limit 1', array(':uniacid' => $_W['uniacid']));
// 		} else {
			$poster = pdo_fetch('select * from ' . tablename('ewei_shop_poster') . ' where uniacid=:uniacid and isdefault=1 and type=1 limit 1', array(':uniacid' => $_W['uniacid']));
// 		}
		if(empty($poster)){
			$this->message('请设置商城海报！');
		}
		
		if($level['level']==91){
			$url = '';
		} else {
			$l = intval($level['level']-1);
			$url = mobileUrl("member/agent/apply",array('mid' => $member['id'],'level'=>$l), true);
		}

		$qr = p('poster')->getQR($poster,$member,0,$url);

// 		$img = p('poster')->createPoster($poster, $member, $qr, false);

		include $this->template();
	}
	
	public function withdrawalinfo()
	{

		global $_W;
		global $_GPC;
		
		$member = m("member")->getMember($_W['openid']);
		$wapset = m('common')->getSysset('wap');
		$area_set = m('util')->get_area_config_set();
		$new_area = intval($area_set['new_area']);
		$show_data = 1;
		if ((!empty($new_area) && empty($member['datavalue'])) || (empty($new_area) && !empty($member['datavalue']))) {
			$show_data = 0;
		}
		$a = pdo_get('ewei_shop_branch', array('branch_id' => $member['branch']), array('branch_name'));
		$b = pdo_get('ewei_shop_detp', array('detp_id' => $member['detp']), array('detp_name'));
		$c = pdo_get('ewei_shop_group', array('group_id' => $member['groups']), array('group_name'));
		if($a['branch_name'] != "" and $b['detp_name'] != "" and $c['group_name'] != ""){
			$member['branch_name'] =$a['branch_name'];
			$member['detp_name'] =$b['detp_name'];
			$member['group_name'] =$c['group_name'];
		}

		if($_W['ispost']){
			$realname = trim($_GPC['realname']);//获取真实姓名
			$mobile = intval($_GPC['mobile']);
			$bankcard = trim($_GPC['bankcard']);//获取银行卡号
			$opening = trim($_GPC['opening']);//获取开户行
			$number = trim($_GPC['number']);//获取账号
			$branch_name = trim($_GPC['branch_name']);//获取所在公司
			$detp_name = trim($_GPC['detp_name']);//获取所在部门
			$group_name = trim($_GPC['group_name']);//获取所在组
			$id = $member['id'];

			$data = array(
				'mobile'   => $mobile,
				'realname' => $realname,
				'bankcard' => $bankcard,
				'opening'  => $opening,
				'number'   => $number
			);

			//如果分公司已存在
			$branch_yes = pdo_get('ewei_shop_branch', array('branch_name' => $branch_name), array('branch_id'));
			if(!empty($branch_yes['branch_id'])){
				$detp_yes = pdo_get('ewei_shop_detp', array('detp_name' => $detp_name,'branch_id'=>$branch_yes['branch_id']), array('detp_id'));
				if(!empty($detp_yes['detp_id'])){
					$data['detp'] = intval($detp_yes['detp_id']);
					$group_yes = pdo_get('ewei_shop_group', array('group_name' => $group_name,'detp_id'=>$detp_yes['detp_id']), array('group_id'));
					if(!empty($group_yes['group_id'])){
						$data['groups'] = intval($group_yes['group_id']);
					}else{
						$group_id = pdo_insert('ewei_shop_group',array('group_name'=>$group_name,'detp_id'=>$detp_yes['detp_id']));
						if (!empty($group_id)) {
						    $group_id = pdo_insertid();
						}
						$data['groups'] = intval($group_id['group_id']);
					}
				}else{
					$detp_id = pdo_insert('ewei_shop_detp',array('detp_name'=>$detp_name,'branch_id'=>$branch_yes['branch_id']));
					if (!empty($detp_id)) {
					    $detp_id = pdo_insertid();
					}
					$group_id = pdo_insert('ewei_shop_group',array('group_name'=>$group_name,'detp_id'=>$detp_id));
					if (!empty($group_id)) {
					    $group_id = pdo_insertid();
					}
					$data['detp'] = intval($detp_id);
					$data['groups'] = intval($group_id);
				}
				$data['branch'] = intval($branch_yes['branch_id']);

			}else{

				$branch_id = pdo_insert('ewei_shop_branch',array('branch_name'=>$branch_name));
				if (!empty($branch_id)) {
				    $branch_id = pdo_insertid();
				}
				$detp_id = pdo_insert('ewei_shop_detp',array('detp_name'=>$detp_name,'branch_id'=>$branch_id));
				if (!empty($detp_id)) {
				    $detp_id = pdo_insertid();
				}
				$group_id = pdo_insert('ewei_shop_group',array('group_name'=>$group_name,'detp_id'=>$detp_id));
				if (!empty($group_id)) {
				    $group_id = pdo_insertid();
				}

				$data['branch'] = intval($branch_id);
				$data['detp'] = intval($detp_id);
				$data['groups'] = intval($group_id);

			}
			$res = pdo_update("ewei_shop_member",$data,array('id'=>$id));
			if($res!==false){
				show_json();
			} else {
				show_json(0,'保存失败，请刷新重试');
			}
		}
		include $this->template();
	}

	public function performance()
	{
		global $_W;
		global $_GPC;

		$openid = $_W['openid'];
		$member = m('member')->getMember($openid);
		$level = m("member")->getLevel($openid);

		if($level['level']<91 || empty($level)){
			$this->message('您暂无权限进入！');
		}

		$totalperformance = 0;
		// $totalordercount = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_order') . ' where uniacid=:uniacid and status=3 and find_in_set(:id,fids)',array(':uniacid'=>$_W['uniacid'],':id'=>$member['id']));

		$sql = "select og.id,og.goodsid from " . tablename('ewei_shop_order_goods') . " og left join " . tablename('ewei_shop_order') . ' o on o.uniacid=og.uniacid and o.id=og.orderid ' . ' where og.uniacid=:uniacid and o.status>=1 and find_in_set(:id,o.fids) group by og.goodsid';
		$params = array(':uniacid'=>$_W['uniacid'],':id'=>$member['id']);
		$list = pdo_fetchall($sql,$params);
		foreach ($list as $k => &$v) {
			$sqls = "select sum(og.realprice) from " . tablename('ewei_shop_order_goods') . " og left join " . tablename('ewei_shop_order') . ' o on o.uniacid=og.uniacid and o.id=og.orderid ' . ' left join ' . tablename('ewei_shop_commission_order_log') . ' l on l.uniacid=og.uniacid and l.goodsid=og.goodsid and l.orderid=o.id ' . ' where og.uniacid=:uniacid and o.status>=1 and l.money>0 and l.issend>=0 and find_in_set(:id,o.fids) and og.goodsid=:goodsid and l.openid=:openid ';
			$vparams = array(':uniacid'=>$_W['uniacid'],':id'=>$member['id'],':goodsid'=>$v['goodsid'],':openid'=>$member['openid']);
			$goodsprice = pdo_fetchcolumn($sqls,$vparams);
			$v['goodsprice'] = $goodsprice?$goodsprice:0;
			$totalperformance += $v['goodsprice'];

			$goods = pdo_fetch('select thumb,title from ' . tablename('ewei_shop_goods') . ' where uniacid=:uniacid and id=:id limit 1',array(':uniacid'=>$_W['uniacid'],':id'=>$v['goodsid']));
			$v['thumb'] = tomedia($goods['thumb']);
			$v['title'] = $goods['title'];
		}
		unset($v);
		$last_ages = array_column($list,'goodsprice');
		array_multisort($last_ages ,SORT_DESC,$list);
		include $this->template();
	}
}