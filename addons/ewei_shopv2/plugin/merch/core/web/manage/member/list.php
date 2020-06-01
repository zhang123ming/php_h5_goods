<?php
if (!(defined('IN_IA'))) 
{
	exit('Access Denied');
}
require EWEI_SHOPV2_PLUGIN . 'merch/core/inc/page_merch.php';
class List_EweiShopV2Page extends MerchWebPage 
{
	public function main(){
		global $_W;
		global $_GPC;
		$merchid = $_W['merch_user']['id'];
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		$params = array(':uniacid' => $_W['uniacid'],':merchid'=>$merchid);
		if (!empty($_GPC['mid'])) {
			$condition .= ' and dm.id=:mid';
			$params[':mid'] = intval($_GPC['mid']);
		}

		if (!empty($_GPC['realname'])) {
			$_GPC['realname'] = trim($_GPC['realname']);
			$condition .= ' and ( m.realname like :realname or m.nickname like :realname or m.mobile like :realname or mm.id like :realname)';
			$params[':realname'] = '%' . $_GPC['realname'] . '%';
		}

		if (empty($starttime) || empty($endtime)) {
			$starttime = strtotime('-1 month');
			$endtime = time();
		}

		if (!empty($_GPC['time']['start']) && !empty($_GPC['time']['end'])) {
			$starttime = strtotime($_GPC['time']['start']);
			$endtime = strtotime($_GPC['time']['end']);
			$condition .= ' AND m.createtime >= :starttime AND m.createtime <= :endtime ';
			$params[':starttime'] = $starttime;
			$params[':endtime'] = $endtime;
		}

		if ($_GPC['level'] != '') {
			$condition .= ' and mm.level=' . intval($_GPC['level']);
		}

		if ($_GPC['groupid'] != '') {
			$condition .= ' and groupid=' . intval($_GPC['groupid']);
		}

		$join = '';

		if ($_GPC['followed'] != '') {
			if ($_GPC['followed'] == 2) {
				$condition .= ' and f.follow=0 and f.unfollowtime<>0';
			}
			else {
				$condition .= ' and f.follow=' . intval($_GPC['followed']) . ' and f.unfollowtime=0 ';
			}

			$join .= ' join ' . tablename('mc_mapping_fans') . ' f on f.openid=dm.openid';
		}

		if ($_GPC['isblack'] != '') {
			$condition .= ' and m.isblack=' . intval($_GPC['isblack']);
		}
		// var_dump($condition);exit;
		$sql = 'select mm.id as id,mm.level as level,mm.openid as openid,mm.agentid as agentid,m.realname as realname,m.avatar as avatar,m.nickname as nickname,m.createtime as createtime from ' . tablename('ewei_shop_merch_member') . 'as mm,'.tablename('ewei_shop_member'). ' as m where m.openid=mm.openid and m.uniacid=mm.uniacid '.$condition.' and  mm.merchid = :merchid and mm.uniacid=:uniacid ORDER BY mm.id DESC';

		if (empty($_GPC['export'])) {
			$sql .= ' limit ' . (($pindex - 1) * $psize) . ',' . $psize;
		}
		else {
			ini_set('memory_limit', '-1');
		}

		$list = pdo_fetchall($sql, $params);
		// echo "<pre>";
		// var_dump($sql);exit;
		$list_group = array();
		$list_level = array();
		$list_agent = array();
		$list_fans = array();

		foreach ($list as $val) {
			$list_group[] = trim($val['groupid'], ',');
			$list_level[] = trim($val['level'], ',');
			$list_agent[] = trim($val['agentid'], ',');
			$list_fans[] = trim($val['openid'], ',');
		}
		$memberids = array_keys($list);
		isset($list_group) && ($list_group = array_values(array_filter($list_group)));

		if (!empty($list_group)) {
			$res_group = pdo_fetchall('select id,groupname from ' . tablename('ewei_shop_member_group') . ' where id in (' . implode(',', $list_group) . ')', array(), 'id');
		}

		isset($list_level) && ($list_level = array_values(array_filter($list_level)));

		if (!empty($list_level)) {
			$res_level = pdo_fetchall('select id,levelname from ' . tablename('ewei_shop_merch_member_level') . ' where id in (' . implode(',', $list_level) . ')', array(), 'id');
		}

		isset($list_fans) && ($list_fans = array_values(array_filter($list_fans)));

		if (!empty($list_fans)) {
			$res_fans = pdo_fetchall('select fanid,openid,follow as followed, unfollowtime from ' . tablename('mc_mapping_fans') . ' where openid in (\'' . implode('\',\'', $list_fans) . '\')', array(), 'openid');
		}
		$shop = m('common')->getSysset('shop');
		foreach ($list as &$row) {
			if($row['agentid']>0){
				$res_agent = pdo_fetch('select m.nickname as agentnickname,m.avatar as agentavatar from ' . tablename('ewei_shop_member') . ' as m,'.tablename('ewei_shop_merch_member').' as mm where m.openid=mm.openid and mm.merchid = '.$merchid.' and mm.id = '.$row['agentid']);
				if($res_agent){
					$row['agentnickname'] = $res_agent['agentnickname'];
					$row['agentavatar'] = $res_agent['agentavatar'];	
				}

			}
			$row['groupname'] = isset($res_group[$row['groupid']]) ? $res_group[$row['groupid']]['groupname'] : '';
			$row['levelname'] = isset($res_level[$row['level']]) ? $res_level[$row['level']]['levelname'] : '';
			$row['followed'] = isset($res_fans[$row['openid']]) ? $res_fans[$row['openid']]['followed'] : '';
			$row['unfollowtime'] = isset($res_fans[$row['openid']]) ? $res_fans[$row['openid']]['unfollowtime'] : '';
			$row['fanid'] = isset($res_fans[$row['openid']]) ? $res_fans[$row['openid']]['fanid'] : '';
			$row['levelname'] = empty($row['levelname']) ?$shop['levelname'] :$row['levelname'];
			$row['ordercount'] = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_order') . ' where uniacid=:uniacid and openid=:openid and merchid = :merchid and status=3', array(':uniacid' => $_W['uniacid'], ':openid' => $row['openid'],':merchid'=>$merchid));
			$row['ordermoney'] = pdo_fetchcolumn('select sum(price) from ' . tablename('ewei_shop_order') . ' where uniacid=:uniacid and openid=:openid and merchid = :merchid and status=3', array(':uniacid' => $_W['uniacid'], ':openid' => $row['openid'],':merchid'=>$merchid));
			$row['credit1'] = m('member')->getCredit($row['openid'], 'credit1');
			$row['credit2'] = m('member')->getCredit($row['openid'], 'credit2');
		}
		unset($row);
		// echo '<pre>';
		// // var_dump($list);exit;
		// var_dump($shop['levelname']);exit;
		if ($_GPC['export'] == '1') {
			plog('member.list', '导出会员数据');

			foreach ($list as &$row) {
				var_dump($row);
				$row['createtime'] = date('Y-m-d H:i', $row['createtime']);
				$row['groupname'] = empty($row['groupname']) ? '无分组' : $row['groupname'];
				$row['levelname'] = empty($row['levelname']) ? $shop['levelname'] : $row['levelname'];
				$row['realname'] = str_replace('=', '', $row['realname']);
				$row['nickname'] = str_replace('=', '', $row['nickname']);
			}
			unset($row);
			m('excel')->export($list, array(
	'title'   => '会员数据-' . date('Y-m-d-H-i', time()),
	'columns' => array(
		array('title' => '昵称', 'field' => 'nickname', 'width' => 12),
		array('title' => '姓名', 'field' => 'realname', 'width' => 12),
		array('title' => '手机号', 'field' => 'mobile', 'width' => 12),
		array('title' => 'openid', 'field' => 'openid', 'width' => 24),
		array('title' => '会员等级', 'field' => 'levelname', 'width' => 12),
		array('title' => '会员分组', 'field' => 'groupname', 'width' => 12),
		array('title' => '注册时间', 'field' => 'createtime', 'width' => 12),
		array('title' => ''.$_W['shopset']['trade']['credittext'].'', 'field' => 'credit1', 'width' => 12),
		array('title' => '余额', 'field' => 'credit2', 'width' => 12),
		array('title' => '成交订单数', 'field' => 'ordercount', 'width' => 12),
		array('title' => '成交总金额', 'field' => 'ordermoney', 'width' => 12)
		)
	));
		}

		$total = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_merch_member') . 'as mm,'.tablename('ewei_shop_member'). ' as m where m.openid=mm.openid and m.uniacid=mm.uniacid and mm.merchid = :merchid and mm.uniacid=:uniacid ORDER BY mm.id DESC',$params);
		$pager = pagination2($total, $pindex, $psize);
		$opencommission = false;
		$plug_commission = p('commission');
		if ($plug_commission) {
			$comset = $plug_commission->getSet();

			if (!empty($comset)) {
				$opencommission = true;
			}
		}

		$groups = m('member')->getGroups();
		$levels = m('member')->getmerch_Levels($merchid);
		$set = m('common')->getSysset();
		$default_levelname = (empty($set['shop']['levelname']) ? '普通等级' : $set['shop']['levelname']);
		include $this->template();
	}
	public function detail()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		$member = pdo_fetch('select mm.id as id,mm.level as level,mm.openid as openid,mm.agentid as agentid,m.realname as realname,m.avatar as avatar,m.nickname as nickname,m.createtime as createtime from ' . tablename('ewei_shop_merch_member') . 'as mm,'.tablename('ewei_shop_member'). ' as m where m.openid=mm.openid and m.uniacid=mm.uniacid and mm.id=:id',array(':id'=>$id));
		
		$levels = m('member')->getmerch_Levels($_W['merch_user']['id']);
		if($_W['ispost']){
			$id = intval($_GPC['id']);
			$level = $_GPC['data']['level'];
			$list = pdo_fetch('select id,agentid,merchid from'.tablename('ewei_shop_merch_member').' where uniacid =:uniacid and id = :id',array(':uniacid'=>$_W['uniacid'],':id'=>$id));
			if (!empty($level)) {
				$level = pdo_fetch('select * from ' . tablename('ewei_shop_merch_member_level') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $level, ':uniacid' => $_W['uniacid']));
				if (empty($level)) {
					show_json(0, '此等级不存在');
				}
				$arr['level'] = $level['id'];     
			}
			else {
				$set = m('common')->getSysset();
				$level = array('levelname' => empty($set['shop']['levelname']) ? '普通等级' : $set['shop']['levelname']);
				$arr['level'] = 0;
			}
			if (!empty($id)) {
				$loginfo .= ' 用户id：' . implode(',', $changeids);
				plog('member.list.edit', $loginfo);
			}
			pdo_update('ewei_shop_merch_member', $arr,$list);
			if($list['agentid']>0){

				$data= p('commission')->getMerchAgentId($list['agentid'],$list,$list['merchid']);
				pdo_update('ewei_shop_merch_member',$data,array('id' => $list['id'],'merchid'=>$list['merchid']));	
			}
			show_json(1);

		}
		include $this->template();
	}
	public function delete()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		if (empty($id)) {
			$id = (is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0);
		}
		$members = pdo_fetchall('SELECT id FROM ' . tablename('ewei_shop_merch_member') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);
		foreach ($members as $member) {
			pdo_delete('ewei_shop_merch_member', array('id' => $member['id']));
			pdo_update('ewei_shop_merch_member',array('agentid'=>0),array('agentid'=>$member['id']));
			plog('member.list.delete', '删除会员  ID: ' . $member['id'] . ' <br/>会员信息: ' . $member['openid'] . '/' . $member['nickname'] . '/' . $member['realname'] . '/' . $member['mobile']);
		}

		show_json(1);
	}

	public function changelevel()
	{
		global $_W;
		global $_GPC;

		if ($_W['ispost']) {
			$toggle = trim($_GPC['toggle']);
			$ids = $_GPC['ids'];
			$levelid = intval($_GPC['level']);
			if (empty($ids) || !is_array($ids)) {
				show_json(0, '请选择要操作的会员');
			}

			if (empty($toggle)) {
				show_json(0, '请选择要操作的类型');
			}

			$ids = array_filter($ids);
			$idsstr = implode(',', $ids);
			$loginfo = '批量修改';
				if (!empty($levelid)) {
					$level = pdo_fetch('select * from ' . tablename('ewei_shop_merch_member_level') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $levelid, ':uniacid' => $_W['uniacid']));

					if (empty($level)) {
						show_json(0, '此等级不存在');
					}
				}
				else {
					$set = m('common')->getSysset();
					$level = array('levelname' => empty($set['shop']['levelname']) ? '普通等级' : $set['shop']['levelname']);
					$arr['level'] = 0;
				}

				$arr = array('level' => $levelid);
				$loginfo .= '用户等级 等级名称：' . $level['levelname'];
			

			$changeids = array();
			$members = pdo_fetchall('select id,agentid,merchid from ' . tablename('ewei_shop_merch_member') . ' WHERE id in( ' . $idsstr . ' ) AND uniacid=' . $_W['uniacid']);
			if (!empty($members)) {
				foreach ($members as $member) {
					pdo_update('ewei_shop_merch_member', $arr, array('id' => $member['id']));
					if($member['agentid']>0){
						$data= p('commission')->getMerchAgentId($member['agentid'],$member,$member['merchid']);
						pdo_update('ewei_shop_merch_member',$data,array('id' => $member['id'],'merchid'=>$member['merchid']));	
					}
					$changeids[] = $member['id'];
				}
			}

			if (!empty($changeids)) {
				$loginfo .= ' 用户id：' . implode(',', $changeids);
				plog('member.list.edit', $loginfo);
			}

			show_json(1);
		}

		include $this->template();
	}
}
?>