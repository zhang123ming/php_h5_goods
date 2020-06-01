<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Agentcommission_EweiShopV2Page extends WebPage
{

	//2020.5.12 

	//代理佣金
	public function main()
	{

		global $_W;
		global $_GPC;
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		if (!empty($_GPC['mid'])) {
			$params[':mid'] = intval($_GPC['mid']);
		}
		if (!empty($_GPC['realname'])) {
			if(strlen(intval($_GPC['realname'])) == 11 ){
				$condition = " and  a.mobile = ".intval($_GPC['realname']);
			}elseif(strlen(intval($_GPC['realname'])) < 11 && strlen(intval($_GPC['realname'])) > 1){
				$condition = " and  id = ".intval($_GPC['realname']);
			}else{
				$condition = " and  a.realname Like '%" . $_GPC['realname'] . "%'";
				$_GPC['realname'] = trim($_GPC['realname']);
				$params['realname'] = '%' . $_GPC['realname'] . '%';
			}
		}
		if (!empty($_GPC['level'])) {
			$condition .= " and  a.level= ".intval($_GPC['level']);
		}else{
			$condition .= " and  a.level>0";
		}
		if (!empty($_GPC['orderstaus']) && $_GPC['orderstaus']== '1') {
			$sql = " select a.*,sum(b.money) as kk from " . tablename('ewei_shop_member') ." as a left JOIN ".tablename('ewei_shop_commission_order_log')." as b on a.uniacid = b.uniacid and a.openid = b.openid where a.uniacid = " . $_W['uniacid'] . " and a.isagent =1  ".$condition." and b.issend!=-1  group by a.id order by kk desc";
		}elseif(!empty($_GPC['orderstaus']) && $_GPC['orderstaus']== '2'){
			$sql = " select a.*,sum(b.money) as sss from " . tablename('ewei_shop_member') ." as a left JOIN ".tablename('ewei_shop_commission_order_log')." as b on a.uniacid = b.uniacid and a.openid = b.openid where b.issend = 0 and a.uniacid = " . $_W['uniacid'] . " and a.isagent =1  ".$condition."    group by a.id order by sss desc";
		}elseif(!empty($_GPC['orderstaus']) && $_GPC['orderstaus'] == '3'){
			$sql = " select a.*,sum(b.money) as sss from " . tablename('ewei_shop_member') ." as a left JOIN ".tablename('ewei_shop_commission_order_log')." as b on a.uniacid = b.uniacid and a.openid = b.openid where b.issend = 1 and a.uniacid = " . $_W['uniacid'] . " and a.isagent =1  ".$condition."    group by a.id order by sss desc";
		}else{

			$sql = 'select *  from ' . tablename('ewei_shop_member')  .' as a where uniacid = ' . $_W['uniacid'] . ' and isagent =1 '.$condition.'  ORDER BY agentcount desc';
		}
		if (empty($_GPC['export'])) {
			$sql .= ' limit ' . (($pindex - 1) * $psize) . ',' . $psize;
		}
		// dump($sql);die;
		$list = pdo_fetchall($sql, $params);
		// dump($list);die;
		$total = pdo_fetchcolumn('select count(*)  from ' . tablename('ewei_shop_member') .' as a where a.uniacid = ' . $_W['uniacid'] . ' and a.isagent =1  '.$condition.'');
		
		$now = " and NOW()>=DATE_SUB(from_unixtime(b.finishtime), INTERVAL -15 DAY) ";

		foreach ($list as &$row) {
			$commission_ok = pdo_fetchcolumn('select sum(money) from '.tablename('ewei_shop_commission_order_log').' as a left join '.tablename('ewei_shop_order').' as b on a.orderid = b.id  where b.status=3 and a.uniacid =:uniacid and a.openid=:openid and a.issend=1',array(':uniacid'=>$_W['uniacid'],':openid'=>$row['openid']));
			$commission_pay = pdo_fetchcolumn('select sum(commission) from '.tablename('ewei_shop_commission_apply').' where uniacid =:uniacid and mid=:mid and status=3',array(':uniacid'=>$_W['uniacid'],':mid'=>$row['id']));
			$commission = pdo_fetch('select count(*) as total_count from '.tablename('ewei_shop_commission_order_log').' where uniacid =:uniacid and openid=:openid and issend!=-1',array(':uniacid'=>$_W['uniacid'],':openid'=>$row['openid']));
			$unsettled = pdo_fetchcolumn('select sum(money) from '.tablename('ewei_shop_commission_order_log').' as a left join '.tablename('ewei_shop_order').' as b on a.orderid = b.id  where b.status=3 and a.uniacid =:uniacid and a.openid=:openid and a.issend=0'.$now,array(':uniacid'=>$_W['uniacid'],':openid'=>$row['openid']));
			$commissions = pdo_fetch('select sum(a.money) as total,b.finishtime from '.tablename('ewei_shop_commission_order_log').' as a left join '.tablename('ewei_shop_order').' as b on a.orderid = b.id  where b.status=3 and a.uniacid =:uniacid and a.openid=:openid and a.issend!=-1'.$now,array(':uniacid'=>$_W['uniacid'],':openid'=>$row['openid']));

			
			$member['commission_total'] = $commissions['total']?$commissions['total']:'0.00';//累计佣金

			// dump($now);die;
			
			$member['commission_count'] = number_format($commission['total_count'], '2');//累计佣金条数
			$member['commission_ok'] = number_format($commission_ok, '2');//已结算佣金总计
			$member['commission_pay'] = number_format($commission_pay, '2');//已打款佣金总计
			//剩余未结算
			$member['unsettled'] = number_format($unsettled, '2');
			
			
			$diycommissiondata = iunserializer($row['diycommissiondata']);
			$tempinfo = '';
			foreach ($diycommissiondata as $diyv) {
				if(is_array($diyv)){
					foreach ($diyv as $vv) {
						$tempinfo.=$vv;
					}
					$tempinfo .="<br>";
				}else{
					$tempinfo .=$diyv."<br>";
				}
			}

			$levelnames = pdo_fetch('SELECT levelname FROM ' . tablename('ewei_shop_member_level') .'where id = '.$row['level']);			
			$row['diyinfo'] = $tempinfo;
			$row['credit1'] = m('member')->getCredit($row['openid'], 'credit1');
			$row['credit2'] = m('member')->getCredit($row['openid'], 'credit2');
			$row['levelnames'] = $levelnames['levelname'];
			$row['commission_total'] = $member['commission_total'];
			$row['commission_ok'] = $member['commission_ok'];
			$row['commission_pay'] = $member['commission_pay'];
			$row['unsettled'] = $member['unsettled'];

			if (p('diyform') && !empty($row['diymemberfields']) && !empty($row['diymemberdata'])) {
				$diyformdata_array = p('diyform')->getDatas(iunserializer($row['diymemberfields']), iunserializer($row['diymemberdata']));
				$diyformdata = '';

				foreach ($diyformdata_array as $da) {
					$diyformdata .= $da['name'] . ': ' . $da['value'] . "\r\n";
				}

				$row['member_diyformdata'] = $diyformdata;
			}

			if (p('diyform') && !empty($row['diycommissionfields']) && !empty($row['diycommissiondata'])) {
				$diyformdata_array = p('diyform')->getDatas(iunserializer($row['diycommissionfields']), iunserializer($row['diycommissiondata']));
				$diyformdata = '';

				foreach ($diyformdata_array as $da) {
					$diyformdata .= $da['name'] . ': ' . $da['value'] . "\r\n";
				}

				$row['agent_diyformdata'] = $diyformdata;
			}
		}

		unset($row);
		// dump($list);die;

		if ($_GPC['export'] == '1') {

			$now = " and NOW()>=DATE_SUB(from_unixtime(b.finishtime), INTERVAL -15 DAY) ";

			foreach ($list as &$row) {
				$commission_ok = pdo_fetchcolumn('select sum(money) from '.tablename('ewei_shop_commission_order_log').' as a left join '.tablename('ewei_shop_order').' as b on a.orderid = b.id  where b.status=3 and a.uniacid =:uniacid and a.openid=:openid and a.issend=1',array(':uniacid'=>$_W['uniacid'],':openid'=>$row['openid']));
				$commission_pay = pdo_fetchcolumn('select sum(commission) from '.tablename('ewei_shop_commission_apply').' where uniacid =:uniacid and mid=:mid and status=3',array(':uniacid'=>$_W['uniacid'],':mid'=>$row['id']));
				$commission = pdo_fetch('select count(*) as total_count from '.tablename('ewei_shop_commission_order_log').' where uniacid =:uniacid and openid=:openid and issend!=-1',array(':uniacid'=>$_W['uniacid'],':openid'=>$row['openid']));
				$unsettled = pdo_fetchcolumn('select sum(money) from '.tablename('ewei_shop_commission_order_log').' as a left join '.tablename('ewei_shop_order').' as b on a.orderid = b.id  where b.status=3 and a.uniacid =:uniacid and a.openid=:openid and a.issend=0'.$now,array(':uniacid'=>$_W['uniacid'],':openid'=>$row['openid']));
				$commissions = pdo_fetch('select sum(a.money) as total,b.finishtime from '.tablename('ewei_shop_commission_order_log').' as a left join '.tablename('ewei_shop_order').' as b on a.orderid = b.id  where b.status=3 and a.uniacid =:uniacid and a.openid=:openid and a.issend!=-1'.$now,array(':uniacid'=>$_W['uniacid'],':openid'=>$row['openid']));

				$member['commission_total'] = number_format($commissions['total'], '2');//累计佣金
				$member['commission_count'] = number_format($commission['total_count'], '2');//累计佣金条数
				$member['commission_ok'] = number_format($commission_ok, '2');//已结算佣金总计
				$member['commission_pay'] = number_format($commission_pay, '2');//已打款佣金总计
				//剩余未结算
				$member['unsettled'] = number_format($unsettled, '2');

				//获取分公司
				$a = pdo_get('ewei_shop_branch', array('branch_id' => $row['branch']), array('branch_name'));
				$b = pdo_get('ewei_shop_detp', array('detp_id' => $row['detp']), array('detp_name'));
				$c = pdo_get('ewei_shop_group', array('group_id' => $row['groups']), array('group_name'));

				$row['branch_name'] =$a['branch_name'];
				$row['detp_name'] =$b['detp_name'];
				$row['group_name'] =$c['group_name'];
				
				
				$diycommissiondata = iunserializer($row['diycommissiondata']);
				$tempinfo = '';
				foreach ($diycommissiondata as $diyv) {
					if(is_array($diyv)){
						foreach ($diyv as $vv) {
							$tempinfo.=$vv;
						}
						$tempinfo .="<br>";
					}else{
						$tempinfo .=$diyv."<br>";
					}
				}

				$levelnames = pdo_fetch('SELECT levelname FROM ' . tablename('ewei_shop_member_level') .'where id = '.$row['level']);			
				$row['diyinfo'] = $tempinfo;
				$row['credit1'] = m('member')->getCredit($row['openid'], 'credit1');
				$row['credit2'] = m('member')->getCredit($row['openid'], 'credit2');
				$row['levelnames'] = $levelnames['levelname'];
				$row['commission_total'] = $member['commission_total'];
				$row['commission_ok'] = $member['commission_ok'];
				$row['commission_pay'] = $member['commission_pay'];
				$row['unsettled'] = $member['unsettled'];

				if (p('diyform') && !empty($row['diymemberfields']) && !empty($row['diymemberdata'])) {
					$diyformdata_array = p('diyform')->getDatas(iunserializer($row['diymemberfields']), iunserializer($row['diymemberdata']));
					$diyformdata = '';

					foreach ($diyformdata_array as $da) {
						$diyformdata .= $da['name'] . ': ' . $da['value'] . "\r\n";
					}

					$row['member_diyformdata'] = $diyformdata;
				}

				if (p('diyform') && !empty($row['diycommissionfields']) && !empty($row['diycommissiondata'])) {
					$diyformdata_array = p('diyform')->getDatas(iunserializer($row['diycommissionfields']), iunserializer($row['diycommissiondata']));
					$diyformdata = '';

					foreach ($diyformdata_array as $da) {
						$diyformdata .= $da['name'] . ': ' . $da['value'] . "\r\n";
					}

					$row['agent_diyformdata'] = $diyformdata;
				}
					$row['createtime'] = date('Y-m-d H:i', $row['createtime']);
					$row['groupname'] = empty($row['groupname']) ? '无分组' : $row['groupname'];
					$row['levelname'] = empty($row['levelname']) ? '普通会员' : $row['levelname'];
					$row['realname'] = str_replace('=', '', $row['realname']);
					$row['nickname'] = str_replace('=', '', $row['nickname']);
			}

			unset($row);
			// dump($list);die;
			m('excel')->export($list, array(
			'title'   => '代理佣金-' . date('Y-m-d-H-i', time()),
			'columns' => array(
				array('title' => '昵称', 'field' => 'nickname', 'width' => 18),
				array('title' => '姓名', 'field' => 'realname', 'width' => 12),
				array('title' => '手机号', 'field' => 'mobile', 'width' => 14),
				array('title' => 'openid', 'field' => 'openid', 'width' => 24),
				array('title' => '会员等级', 'field' => 'levelnames', 'width' => 12),
				array('title' => '已累计佣金', 'field' => 'commission_total', 'width' => 12),
				array('title' => '已结算佣金', 'field' => 'commission_ok', 'width' => 12),
				array('title' => '未结算佣金', 'field' => 'unsettled', 'width' => 12),
				array('title' => '开户银行', 'field' => 'number', 'width' => 12),
				array('title' => '开户支行', 'field' => 'opening', 'width' => 12),
				array('title' => '银行卡号', 'field' => 'bankcard', 'width' => 12),
				array('title' => '公司', 'field' => 'branch_name', 'width' => 12),
				array('title' => '部门', 'field' => 'detp_name', 'width' => 12),
				array('title' => '组', 'field' => 'group_name', 'width' => 12),
				array('title' => '注册时间', 'field' => 'createtime', 'width' => 12),
				// array('title' => ''.$_W['shopset']['trade']['credittext'].'', 'field' => 'credit1', 'width' => 12),
				// array('title' => '余额', 'field' => 'credit2', 'width' => 12),
				// array('title' => '成交订单数', 'field' => 'ordercount', 'width' => 12),
				// array('title' => '成交总金额', 'field' => 'ordermoney', 'width' => 12)
				)
			));
		}

		
		// dump($list);die;
		$pager = pagination2($total, $pindex, $psize);
		// load()->func('tpl');
		include $this->template();
	}

	public function edit()
	{
		global $_W;
		global $_GPC;

		$id = intval($_GPC['id']);
		$sql = 'select *  from ' . tablename('ewei_shop_member') .' where id = '.$id;
		$list = pdo_fetch($sql);
		//获取分公司
		$a = pdo_get('ewei_shop_branch', array('branch_id' => $list['branch']), array('branch_name'));
		$b = pdo_get('ewei_shop_detp', array('detp_id' => $list['detp']), array('detp_name'));
		$c = pdo_get('ewei_shop_group', array('group_id' => $list['groups']), array('group_name'));

		$sql = 'select levelname  from ' . tablename('ewei_shop_member_level') .' where id = '.$list['level'];
		$levelnames = pdo_fetch($sql);			

		$list['levelnames'] = $levelnames['levelname'];
		
		$list['branch_name'] =$a['branch_name'];
		$list['detp_name'] =$b['detp_name'];
		$list['group_name'] =$c['group_name'];

		$now = " and NOW()>=DATE_SUB(from_unixtime(b.finishtime), INTERVAL -15 DAY) ";

		$commission_ok = pdo_fetchcolumn('select sum(money) from '.tablename('ewei_shop_commission_order_log').' as a left join '.tablename('ewei_shop_order').' as b on a.orderid = b.id  where b.status=3 and a.uniacid =:uniacid and a.openid=:openid and a.issend=1',array(':uniacid'=>$_W['uniacid'],':openid'=>$list['openid']));
		$commission_pay = pdo_fetchcolumn('select sum(commission) from '.tablename('ewei_shop_commission_apply').' where uniacid =:uniacid and mid=:mid and status=3',array(':uniacid'=>$_W['uniacid'],':mid'=>$list['id']));
		$commission = pdo_fetch('select count(*) as total_count from '.tablename('ewei_shop_commission_order_log').' where uniacid =:uniacid and openid=:openid and issend!=-1',array(':uniacid'=>$_W['uniacid'],':openid'=>$list['openid']));
		$unsettled = pdo_fetchcolumn('select sum(money) from '.tablename('ewei_shop_commission_order_log').' as a left join '.tablename('ewei_shop_order').' as b on a.orderid = b.id  where b.status=3 and a.uniacid =:uniacid and a.openid=:openid and a.issend=0'.$now,array(':uniacid'=>$_W['uniacid'],':openid'=>$list['openid']));
		$commissions = pdo_fetch('select sum(a.money) as total,b.finishtime from '.tablename('ewei_shop_commission_order_log').' as a left join '.tablename('ewei_shop_order').' as b on a.orderid = b.id  where b.status=3 and a.uniacid =:uniacid and a.openid=:openid and a.issend!=-1'.$now,array(':uniacid'=>$_W['uniacid'],':openid'=>$list['openid']));

		$list['commission_total'] = number_format($commissions['total'], '2');//累计佣金
		$list['commission_count'] = number_format($commission['total_count'], '2');//累计佣金条数
		$list['commission_ok'] = number_format($commission_ok, '2');//已结算佣金总计
		$list['commission_pay'] = number_format($commission_pay, '2');//已打款佣金总计

		//剩余未结算
		$list['unsettled'] = number_format($unsettled, '2');

		if ($_W['ispost']) {

			$id = $_GPC['id'];
			$userinfo = pdo_fetch('select *  from ' . tablename('ewei_shop_member') .' where uniacid = ' . $_W['uniacid'] . ' and id = '.$id);
			$sqls = "select o.id from " . tablename('ewei_shop_order_goods') . " og left join " . tablename('ewei_shop_order') . ' o on o.uniacid=og.uniacid and o.id=og.orderid ' . ' left join ' . tablename('ewei_shop_commission_order_log') . ' l on l.uniacid=og.uniacid and l.goodsid=og.goodsid and l.orderid=o.id ' . ' where og.uniacid=:uniacid and o.status=3 and l.money>0 and l.issend>=0 and find_in_set(:id,o.fids) and l.openid=:openid  and NOW()>=DATE_SUB(from_unixtime(o.finishtime), INTERVAL -15 DAY)'.$condition.' group by o.id ';
			$paras[':uniacid'] = $_W['uniacid'];
			$paras[':id'] = $userinfo['id'];
			$paras[':openid'] = $userinfo['openid'];
			$list = pdo_fetchall($sqls,$paras);
			foreach ($list as $k => $vo) {
				$res = pdo_update('ewei_shop_commission_order_log',array('issend'=>1),array('openid' => $userinfo['openid'],'uniacid'=>$_W['uniacid'],'issend'=>0,'orderid'=>$vo['id']));

				if($res){
					show_json();
				} else {
					show_json(0,'数据保存失败，请刷新重试');
				}
			}
		}

		include $this->template();
	}

	protected function post()
	{

	
		include $this->template();
	}

	public function getorderlist()
	{
		global $_W;
		global $_GPC;

		$userid = intval($_GPC['id']);
		$userids = $userid;
		$searchtime = trim($_GPC['searchtime']);
		// dump($searchtime,$_GPC['time'],$searchtime,in_array($searchtime, array('create', 'pay', 'send', 'finish')));die;
		if (!empty($searchtime) && is_array($_GPC['time']) && in_array($searchtime, array('create', 'pay', 'send', 'finish'))) {
			// dump('22');die;
			$starttime = strtotime($_GPC['time']['start']);
			$endtime = strtotime($_GPC['time']['end']);
			$condition .= ' AND o.' . $searchtime . 'time >= :starttime AND o.' . $searchtime . 'time <= :endtime ';
			$paras[':starttime'] = $starttime;
			$paras[':endtime'] = $endtime;
		}

		if (!empty($_GPC['searchfield']) && !empty($_GPC['keyword'])) {
			$searchfield = trim(strtolower($_GPC['searchfield']));
			$_GPC['keyword'] = trim($_GPC['keyword']);
			$paras[':keyword'] = htmlspecialchars_decode($_GPC['keyword'], ENT_QUOTES);
			$sqlcondition = '';

			if ($searchfield == 'ordersn') {
				$condition .= ' AND locate(:keyword,o.ordersn)';
			}
			else if ($searchfield == 'member') {
				$condition .= ' AND (locate(:keyword,m.realname)>0 or locate(:keyword,m.mobile)>0 or locate(:keyword,m.nickname)>0)';
			}
			else if ($searchfield == 'mid') {
				$condition .= ' AND m.id = :keyword';
			}
			else if ($searchfield == 'address') {
				$condition .= ' AND ( locate(:keyword,a.realname)>0 or locate(:keyword,a.mobile)>0 or locate(:keyword,o.carrier)>0)';
			}
			else if ($searchfield == 'location') {
				$condition .= ' AND ( locate(:keyword,a.province)>0 or locate(:keyword,a.city)>0 or locate(:keyword,a.area)>0 or locate(:keyword,a.address)>0)';
			}
			else if ($searchfield == 'expresssn') {
				$condition .= ' AND locate(:keyword,o.expresssn)>0';
			}
			else if ($searchfield == 'saler') {
				$condition .= ' AND (locate(:keyword,sm.realname)>0 or locate(:keyword,sm.mobile)>0 or locate(:keyword,sm.nickname)>0 or locate(:keyword,s.salername)>0 )';
			}
			else if ($searchfield == 'verifycode') {
				$condition .= ' AND (verifycode=:keyword or locate(:keyword,o.verifycodes)>0)';
			}
			else if ($searchfield == 'store') {
				$condition .= ' AND (locate(:keyword,store.storename)>0)';
				$sqlcondition = ' left join ' . tablename('ewei_shop_store') . ' store on store.id = o.verifystoreid and store.uniacid=o.uniacid';
			}
			else if ($searchfield == 'goodstitle') {
				$sqlcondition = ' inner join ( select DISTINCT(og.orderid) from ' . tablename('ewei_shop_order_goods') . ' og left join ' . tablename('ewei_shop_goods') . ' g on g.id=og.goodsid where og.uniacid = \'' . $uniacid . '\' and (locate(:keyword,g.title)>0)) gs on gs.orderid=o.id';
			}
			else if ($searchfield == 'goodssn') {
				$sqlcondition = ' inner join ( select DISTINCT(og.orderid) from ' . tablename('ewei_shop_order_goods') . ' og left join ' . tablename('ewei_shop_goods') . ' g on g.id=og.goodsid where og.uniacid = \'' . $uniacid . '\' and (((locate(:keyword,g.goodssn)>0)) or (locate(:keyword,og.goodssn)>0))) gs on gs.orderid=o.id';
			}
			else if ($searchfield == 'goodsoptiontitle') {
				$sqlcondition = ' inner join ( select  DISTINCT(og.orderid) from ' . tablename('ewei_shop_order_goods') . ' og left join ' . tablename('ewei_shop_goods') . ' g on g.id=og.goodsid where og.uniacid = \'' . $uniacid . '\' and (locate(:keyword,og.optionname)>0)) gs on gs.orderid=o.id';
			}
			else if ($searchfield == 'attachstore') {
				$condition .= ' AND (locate(:keyword,store.storename)>0)';
				$sqlcondition = ' left join ' . tablename('ewei_shop_store') . ' store on store.id = o.storeid and store.uniacid=o.uniacid';
				
			}
			else {
				if ($searchfield == 'merch') {
					if ($merch_plugin) {
						$condition .= ' AND (locate(:keyword,merch.merchname)>0)';
						$sqlcondition = ' left join ' . tablename('ewei_shop_merch_user') . ' merch on merch.id = o.merchid and merch.uniacid=o.uniacid';
					}
				}else if ($searchfield=='machineid') {
					$condition .=' AND (locate(:keyword,o.machineid)>0)';
				}else if($searchfield =='refundno'){
					$condition .= ' AND locate(:keyword,r.refundno)>0';
				}
			}
		}



		$agent = m("member")->getMember($_W['openid']);
		$member = pdo_fetch('select id,openid,nickname,avatar,realname,mobile,weixin from ' . tablename('ewei_shop_member') . ' where uniacid=:uniacid and id=:id limit 1',array(':uniacid'=>$_W['uniacid'],':id'=>$userid));
		$level = m("member")->getLevel($member['openid']);

		if($level['level']>=91){
			$sqls = "select sum(og.realprice) from " . tablename('ewei_shop_order_goods') . " og left join " . tablename('ewei_shop_order') . ' o on o.uniacid=og.uniacid and o.id=og.orderid ' . ' left join ' . tablename('ewei_shop_commission_order_log') . ' l on l.uniacid=og.uniacid and l.goodsid=og.goodsid and l.orderid=o.id ' . ' where og.uniacid=:uniacid and o.status>=1 and l.money>0 and l.issend>=0 and find_in_set(:id,o.fids) and l.openid=:openid ';
			$vparams = array(':uniacid'=>$_W['uniacid'],':id'=>$member['id'],':openid'=>$member['openid']);
			$orderprice = pdo_fetchcolumn($sqls,$vparams);
		}
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		if($level['level']>=91){
			$sqls = "select o.id,o.price,o.ordersn,o.createtime,o.status from " . tablename('ewei_shop_order_goods') . " og left join " . tablename('ewei_shop_order') . ' o on o.uniacid=og.uniacid and o.id=og.orderid ' . ' left join ' . tablename('ewei_shop_commission_order_log') . ' l on l.uniacid=og.uniacid and l.goodsid=og.goodsid and l.orderid=o.id ' . ' where og.uniacid=:uniacid and o.status>=1 and l.money>0 and l.issend>=0 and find_in_set(:id,o.fids) and l.openid=:openid '.$condition.' group by o.id order by o.id desc limit ' . (($pindex - 1) * $psize) . ',' . $psize;
			$paras[':uniacid'] = $_W['uniacid'];
			$paras[':id'] = $member['id'];
			$paras[':openid'] = $member['openid'];

			// $paras = array(':uniacid'=>$_W['uniacid'],':id'=>$member['id'],':openid'=>$member['openid']);
		}
		
		$list = pdo_fetchall($sqls,$paras);
		// dump($list);die;
		$realprice = 0;
		foreach ($list as &$v) {
			$v['createtime'] = date('Y-m-d',$v['createtime']);
			$goods = pdo_fetchall('SELECT og.id,og.goodsid,g.thumb,og.price,og.realprice,og.total,g.title,og.optionname,' . 'og.commission1,og.commission2,og.commission3,og.commissions,' . 'og.status1,og.status2,og.status3,' . 'og.content1,og.content2,og.content3 from ' . tablename('ewei_shop_order_goods') . ' og' . ' left join ' . tablename('ewei_shop_goods') . ' g on g.id=og.goodsid  ' . ' where og.orderid=:orderid and og.nocommission=0 and og.uniacid = :uniacid order by og.createtime  desc ', array(':uniacid' => $_W['uniacid'], ':orderid' => $v['id']));
			$goods = set_medias($goods, 'thumb');
			foreach ($goods as $k => $vs) {
				$realprice += $vs['realprice'];
			}
			$v['goodsrealprice'] = $realprice;
			unset($realprice);
			$v['goods'] = $goods;
		}
		unset($v);
		$sqls = "select o.id,o.ordersn from " . tablename('ewei_shop_order') . ' o left join ' . tablename('ewei_shop_commission_order_log') . ' l on l.uniacid=o.uniacid and l.orderid=o.id ' . ' where o.uniacid=:uniacid and o.status=>3 and l.money>0 and find_in_set(:id,o.fids) order by o.id desc ';
		$params = array(':uniacid'=>$_W['uniacid'],':id'=>$member['id']);
		$alllist = pdo_fetchall($sqls,$params);
		include $this->template();
	}



}

?>