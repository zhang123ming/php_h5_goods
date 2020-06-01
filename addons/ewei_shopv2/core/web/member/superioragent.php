<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Superioragent_EweiShopV2Page extends WebPage
{
	//会员下线列表
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
				$condition = " and  a.id = ".intval($_GPC['realname']);
			}else{
				$condition = " and  a.realname Like '%" . $_GPC['realname'] . "%'";
				$_GPC['realname'] = trim($_GPC['realname']);
				$params['realname'] = '%' . $_GPC['realname'] . '%';
			}
			if (!empty($_GPC['time']['start']) && !empty($_GPC['time']['end'])) {
				$starttime = strtotime($_GPC['time']['start']);
				$endtime = strtotime($_GPC['time']['end']);
				$condition .= " AND a.createtime >= '".$starttime."' AND a.createtime <= '".$endtime."' ";
			}

		}else{
			if (!empty($_GPC['time']['start']) && !empty($_GPC['time']['end'])) {
				$starttime = strtotime($_GPC['time']['start']);
				$endtime = strtotime($_GPC['time']['end']);
				$condition .= " AND a.createtime >= '".$starttime."' AND a.createtime <= '".$endtime."' ";
			}
		}

		if (!empty($_GPC['orderstaus']) && $_GPC['orderstaus']== '1') {
			$sql = " select a.*,sum(b.money) as kk from " . tablename('ewei_shop_member') ." as a left JOIN ".tablename('ewei_shop_commission_order_log')." as b on a.uniacid = b.uniacid and a.openid = b.openid where a.uniacid = " . $_W['uniacid'] . " and a.level = 0  and a.isagent =1  ".$condition." and b.issend!=-1  group by a.id order by kk desc";
		}elseif(!empty($_GPC['orderstaus']) && $_GPC['orderstaus']== '2'){
			$sql = " select a.*,sum(b.money) as sss from " . tablename('ewei_shop_member') ." as a left JOIN ".tablename('ewei_shop_commission_order_log')." as b on a.uniacid = b.uniacid and a.openid = b.openid where b.issend = 0 and a.uniacid = " . $_W['uniacid'] . "  and a.level = 0  and a.isagent =1  ".$condition."    group by a.id order by sss desc";
		}elseif(!empty($_GPC['orderstaus']) && $_GPC['orderstaus'] == '3'){
			$sql = " select a.*,sum(b.money) as sss from " . tablename('ewei_shop_member') ." as a left JOIN ".tablename('ewei_shop_commission_order_log')." as b on a.uniacid = b.uniacid and a.openid = b.openid where b.issend = 1 and a.uniacid = " . $_W['uniacid'] . " and a.level = 0  and a.isagent =1  ".$condition."    group by a.id order by sss desc";
		}else{
			$sql = 'select id,uid,nickname,level,realname,avatar,agentcount,mobile,createtime,openid  from ' . tablename('ewei_shop_member')  .' as a where uniacid = ' . $_W['uniacid'] . ' and a.level = 0 '.$condition.' ORDER BY agentcount desc';
		}
		
		if (empty($_GPC['export'])) {
			$sql .= ' limit ' . (($pindex - 1) * $psize) . ',' . $psize;
		}

		$list = pdo_fetchall($sql, $params);
		// dump($list);die;
		$total = pdo_fetchcolumn('select count(*)  from '  . tablename('ewei_shop_member')  .' as a where uniacid = ' . $_W['uniacid'] . ' and a.level = 0 '.$condition.' ORDER BY agentcount desc');
		
		$now = " and NOW()>=DATE_SUB(from_unixtime(b.finishtime), INTERVAL -15 DAY) ";

		foreach ($list as &$row) {
			$commission_ok = pdo_fetchcolumn('select sum(money) from '.tablename('ewei_shop_commission_order_log').' as a left join '.tablename('ewei_shop_order').' as b on a.orderid = b.id  where b.status=3 and a.uniacid =:uniacid and a.openid=:openid and a.issend=1',array(':uniacid'=>$_W['uniacid'],':openid'=>$row['openid']));
			$commission_pay = pdo_fetchcolumn('select sum(commission) from '.tablename('ewei_shop_commission_apply').' where uniacid =:uniacid and mid=:mid and status=3',array(':uniacid'=>$_W['uniacid'],':mid'=>$row['id']));
			$commission = pdo_fetch('select count(*) as total_count from '.tablename('ewei_shop_commission_order_log').' where uniacid =:uniacid and openid=:openid and issend!=-1',array(':uniacid'=>$_W['uniacid'],':openid'=>$row['openid']));
			$unsettled = pdo_fetchcolumn('select sum(money) from '.tablename('ewei_shop_commission_order_log').' as a left join '.tablename('ewei_shop_order').' as b on a.orderid = b.id  where b.status=3 and a.uniacid =:uniacid and a.openid=:openid and a.issend=0'.$now,array(':uniacid'=>$_W['uniacid'],':openid'=>$row['openid']));
			$commissions = pdo_fetch('select sum(a.money) as total,b.finishtime from '.tablename('ewei_shop_commission_order_log').' as a left join '.tablename('ewei_shop_order').' as b on a.orderid = b.id  where b.status=3 and a.uniacid =:uniacid and a.openid=:openid and a.issend!=-1'.$now,array(':uniacid'=>$_W['uniacid'],':openid'=>$row['openid']));
			$member['commission_total'] = $commissions['total']?$commissions['total']:'0.00';//累计佣金
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
		
			$row['diyinfo'] = $tempinfo;
			$row['credit1'] = m('member')->getCredit($row['openid'], 'credit1');
			$row['credit2'] = m('member')->getCredit($row['openid'], 'credit2');

			$row['commission_total'] = $member['commission_total'];
			$row['commission_ok'] = $member['commission_ok'];
			$row['commission_pay'] = $member['commission_pay'];
			$row['unsettled'] = $member['unsettled'];

			
		}

		unset($row);
		

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
				array('title' => '注册时间', 'field' => 'createtime', 'width' => 12),
				// array('title' => ''.$_W['shopset']['trade']['credittext'].'', 'field' => 'credit1', 'width' => 12),
				// array('title' => '余额', 'field' => 'credit2', 'width' => 12),
				// array('title' => '成交订单数', 'field' => 'ordercount', 'width' => 12),
				// array('title' => '成交总金额', 'field' => 'ordermoney', 'width' => 12)
				)
			));
		}

		

		$pager = pagination2($total, $pindex, $psize);
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
		$agent = pdo_get('ewei_shop_member', array('id' => $list['agentid']), array('level'));
		$sql = 'select levelname  from ' . tablename('ewei_shop_member_level') .' where id = '.$agent['level'];
		$levelnames = pdo_fetch($sql);			
		
		$list['levelnames'] = $levelnames['levelname'];
		
		$list['branch_name'] =$a['branch_name'];
		$list['detp_name'] =$b['detp_name'];
		$list['group_name'] =$c['group_name'];

		if(!empty($_GPC['superioragentid'])){
			$sqls = " select id,avatar,openid,nickname from  ". tablename('ewei_shop_member') ." where id = ".$_GPC['superioragentid'];
		}else{
			$sqls = " select id,avatar,openid,nickname from  ". tablename('ewei_shop_member') ." where id = ".$list['superioragentid'];
		}

		$res = pdo_fetch($sqls);

		if ($_W['ispost']) {

			$id = intval($_GPC['id']);
			$sql = 'select *  from ' . tablename('ewei_shop_member') .' where id = '.$id;
			$list = pdo_fetch($sql);
			$superioragentid = intval($_GPC['superioragentid'])?intval($_GPC['superioragentid']):0;
			$realname = !empty(trim($_GPC['realname']))?trim($_GPC['realname']):$list['realname'];
			$mobile = !empty(trim($_GPC['mobile']))?trim($_GPC['mobile']):$list['mobile'];
			$number = !empty(trim($_GPC['number']))?trim($_GPC['number']):$list['number'];
			$opening = !empty(trim($_GPC['opening']))?trim($_GPC['opening']):$list['opening'];
			$bankcard = !empty(trim($_GPC['bankcard']))?trim($_GPC['bankcard']):$list['bankcard'];
			$branch_name = trim($_GPC['branch_name']);//获取所在公司
			$detp_name = trim($_GPC['detp_name']);//获取所在部门
			$group_name = trim($_GPC['group_name']);//获取所在组

			$data = array(
				'superioragentid' => $superioragentid,
				'mobile'   => $mobile,
				'realname' => $realname,
				'bankcard' => $bankcard,
				'opening'  => $opening,
				'number'   => $number
			);

			if($_GPC['level'] == "1"){
				$data['level'] = 371;
				$adata['agentid'] = $_GPC['agentid'];
				if($adata['agentid']){

					$ainviter = p('commission')->getAgentId($adata['agentid'],$list);
					pdo_update('ewei_shop_member',$ainviter,array('id'=>$list['id']));
					$teamlist = pdo_fetchall('select * from '.tablename('ewei_shop_member').' where uniacid=:uniacid and find_in_set('.$list['id'].',fids) order by fids asc',array(':uniacid'=>$_W['uniacid']));
					foreach ($teamlist as $k => $v) {
						$agentdata1 = p('commission')->getAgentId($v['agentid'],$v);
						pdo_update('ewei_shop_member',$agentdata1,array('id'=>$v['id']));
					}

					$adata = array_merge($adata,$ainviter);
				}else{
					$ainviter = p('commission')->getAgentId($adata['agentid'],$list);

					pdo_update('ewei_shop_member',$ainviter,array('id'=>$list['id']));
					$teamlist = pdo_fetchall('select * from '.tablename('ewei_shop_member').' where uniacid=:uniacid and find_in_set('.$list['id'].',fids) order by fids asc',array(':uniacid'=>$_W['uniacid']));
					foreach ($teamlist as $k => $v) {
						$agentdata1 = p('commission')->getAgentId($v['agentid'],$v);
						
						pdo_update('ewei_shop_member',$agentdata1,array('id'=>$v['id']));
					}
				}
			}
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
			// dump($data);die;
			$res = pdo_update("ewei_shop_member",$data,array('id'=>$id));
			if($res!==false){
				show_json();
			} else {
				show_json(0,'保存失败，请刷新重试');
			}
		}

		include $this->template();
	}

	protected function post()
	{

	
		include $this->template();
	}





}