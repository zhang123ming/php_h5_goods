<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Myteam_EweiShopV2Page extends MobileLoginPage
{
	
	public function main(){
		global $_W,$_GPC;
		$member = m('member')->getMember($_W['openid']);
		$level = m('member')->getLevel($member['openid']);
		$sql = 'select count(*) as count from '.tablename('ewei_shop_member').' where agentid=:agentid and uniacid =:uniacid';
		$sql1 = 'select count(*) as count from '.tablename('ewei_shop_member').' where find_in_set('.$member['id'].',fids) and uniacid =:uniacid';
		$count=pdo_fetchcolumn($sql,array(':agentid'=>$member['id'],':uniacid'=>$_W['uniacid']));
		$allcount=pdo_fetchcolumn($sql1,array(':uniacid'=>$_W['uniacid']));

		$set =  p('commission')->getSet();
		$amemberlimits = iunserializer($member['memberlimits']);
		$memberlimits = array();
		if(isset($amemberlimits['VIP1']) && $amemberlimits['VIP1']>0){
			$memberlimits['VIP1'] = $amemberlimits['VIP1'];
		}
		if(isset($amemberlimits['VIP2']) && $amemberlimits['VIP2']>0){
			$memberlimits['VIP2'] = $amemberlimits['VIP2'];
		}
		if(isset($amemberlimits['texu']) && $amemberlimits['texu']>0){
			$memberlimits['特许经销商'] = $amemberlimits['texu'];
		}
		if(isset($amemberlimits['zuanshi']) && $amemberlimits['zuanshi']>0){
			$memberlimits['钻石经销商'] = $amemberlimits['zuanshi'];
		}

		$arr_agent = pdo_fetchall('select agent91,agent92,agent93,agent94,agent95,agent96,agent97,agent98,agent99,agent100 from '.tablename('ewei_shop_member').' where openid=:id and uniacid =:uniacid',array(":id"=>$_W['openid'],':uniacid'=>$_W['uniacid']));
		foreach($arr_agent[0] as $v){
			if(!empty($v)){
				$parent_id = $v;
				break;
			}else{
				$parent_id = false;
			}
		}
		if($parent_id){
			$parent_member =  m('member')->getMember($parent_id);
			$parent_level =  m('member')->getLevel($parent_id);
		}
		include $this->template();
	}


	public function spacelog(){
		global $_W,$_GPC;
		include $this->template('member/spacelog');
	}

	public function get_log(){
		global $_W,$_GPC;
		
		$pindex = max(1, intval($_GPC['page']));
		$psize = 10;
		$condition = ' and s.openid=:openid or s.otheropenid=:otheropenid';
		$params = array(':openid' => $_W['openid'], ':otheropenid' => $_W['openid']);
		$sql = "SELECT s.*,m.nickname FROM ".tablename('ewei_shop_space_limit')." s left join ".tablename('ewei_shop_member')." m on s.otheropenid=m.openid";
		$list = pdo_fetchall($sql . ' where 1 ' . $condition . ' order by s.createtime desc LIMIT ' . (($pindex - 1) * $psize) . ',' . $psize, $params);
		$total = pdo_fetchcolumn("SELECT count(*) FROM ".tablename('ewei_shop_space_limit')." s left join ".tablename('ewei_shop_member')." m on s.openid=m.openid where 1 " . $condition, $params);

		foreach ($list as &$row) {
			$row['createtime'] = date('Y-m-d H:i:s', $row['createtime']);
		}

		unset($row);

		show_json(1, array('list' => $list, 'total' => $total, 'pagesize' => $psize));
	}

	public function get_list(){
		global $_W,$_GPC;
		$mymember = m('member')->getMember($_W['openid']);
		$mylevel =  m('member')->getLevel($_W['openid']);
		$agentid = intval($_GPC['id']);
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		$set =  p('commission')->getSet();
		$search_need_up = $_GPC['search_need_up'];
		$condition = ' and m.agentid=:agentid and m.uniacid=:uniacid';
		$need_up_condition = ' and m.need_up=1 and l.level<99';
		$params = array(':uniacid' => $_W['uniacid'], ':agentid' => $agentid);
		
		$sql = "SELECT m.id,m.uniacid,m.nickname,m.realname,m.openid,m.agentid,m.fids,m.avatar,m.need_up,m.memberlimit,l.id as levelid,l.levelname,l.level FROM ".tablename('ewei_shop_member')." m LEFT JOIN ".tablename('ewei_shop_member_level')." l ON m.LEVEL = l.id ";
		$list = pdo_fetchall($sql . ' where 1 ' . $condition . ' order by m.childtime desc LIMIT ' . (($pindex - 1) * $psize) . ',' . $psize, $params);
		$total = pdo_fetchcolumn("SELECT count(*) FROM ".tablename('ewei_shop_member')." m LEFT JOIN ".tablename('ewei_shop_member_level')." l ON m.LEVEL = l.id where 1 " . $condition, $params);
		$need_up_count = pdo_fetchcolumn("SELECT count(*) FROM ".tablename('ewei_shop_member')." m LEFT JOIN ".tablename('ewei_shop_member_level')." l ON m.LEVEL = l.id where 1 ". $need_up_condition . $condition , $params);
		
		$need_up_list = pdo_fetchall($sql . ' where 1 '. $need_up_condition . $condition , $params);
		if($search_need_up == 1){
			$list = $need_up_list;
		}
		foreach ($list as &$row) {
			$row['nickname'] = empty($row['realname'])? $row['nickname'] : $row['realname'];
			$row['levelname'] = (empty($row['level'])?'默认等级':$row['levelname']);
			$sql1 = 'select count(*) as count from '.tablename('ewei_shop_member').' where find_in_set('.$row['id'].',fids) and uniacid =:uniacid';
			$row['count']=pdo_fetchcolumn($sql1,array(':uniacid'=>$_W['uniacid']));
		}
		$count = count($list);
		unset($row);
		show_json(1, array('list' => $list, 'total' => $total, 'pagesize' => $psize,'need_up_count'=>$need_up_count,'count'=>$count));
	}

	public function get_levels(){
		global $_W,$_GPC;
		$list = m('member')->getLevels();
		show_json(1, array('list' => $list));
	}

	// 修改等级
	public function update(){
		global $_W,$_GPC;
		$set =  p('commission')->getSet();
		$mymember = m('member')->getMember($_W['openid']);
		$mylevel = m('member')->getLevel($_W['openid']);
		$memberlimits =iunserializer($mymember['memberlimits']);
		$level = $_GPC['levelid'];
		$otheropenid = $_GPC['openid'];
		$member = m('member')->getMember($otheropenid);

		if (empty($member)) {
			show_json(0,'用户信息不存在');	
		}
		if ($mymember['memberlimit']<1) {
			show_json(0,'名额不足');
		}
		$updatedata = p('commission')->getAgentId($member['agentid'],$member);
		$updatedata['level'] = $level;
		$level_level = pdo_fetchcolumn('select level from '.tablename('ewei_shop_member_level').' where uniacid=:uniacid and id=:id',array(':uniacid'=>$_W['uniacid'],':id'=>$level));
		if ($level_level>90) {
			$updatedata['isagent'] = 1;
			$updatedata['status'] = 1;
			$updatedata['agenttime'] = time();
		}
		$status = pdo_update('ewei_shop_member',$updatedata, array('id'=>$member['id']));
		$teamlist = pdo_fetchall('select id,agentid,level from '.tablename('ewei_shop_member').' where uniacid=:uniacid and find_in_set('.$member['id'].',fids) order by fids asc',array(':uniacid'=>$_W['uniacid']));
		
		foreach ($teamlist as $k2 => $v2) {
			$agentdata = p('commission')->getAgentId($v2['agentid'],$v2);
			//unset($agentdata1['fids']);
			pdo_update('ewei_shop_member',$agentdata,array('id'=>$v2['id']));
		}
		$mycount = $mymember['memberlimit'];
		if (empty($member['level'])) {
			$mycount = $this->insertlog($otheropenid,-1);
		}
		show_json(1,array('mycount' => $mycount));

	}


	// 赠予名额
	public function give(){
		global $_W,$_GPC;
		$count = $_GPC['count'];
		$otheropenid = $_GPC['openid'];
		$mymember = m('member')->getMember($_W['openid']);
		if($count < 1){
			show_json(0,array('message'=>'请输入名额'));
		}
		if ($mymember['memberlimit']<$count) {
			show_json(0,'名额不足');
		}
		$member = m('member')->getMember($otheropenid);
		if (empty($member)) {
			show_json(0,array('message'=>'请选择会员'));
		}else{
			$level = pdo_get('ewei_shop_member_level',array('id'=>$member['level']));
			// file_put_contents('test.txt', var_export($level),FILE_APPEND);
			if ($level['level']<91) {
				show_json(0,array('message'=>'不能给该会员等级赠送名额'));
			}
		}
		$updateother = pdo_query("update ims_ewei_shop_member set memberlimit=memberlimit+".$count." where openid='".$otheropenid."' and uniacid=".$_W['uniacid']);

		$mycount = $this->insertlog($otheropenid,$count,1);

		show_json(1, array('mycount' => $mycount));
	}

	// 添加记录
	public function insertlog($otheropenid,$count,$type=0){
		global $_W,$_GPC;
		if (!empty($type)) {
			$count = 0-$count;
		}
		
		$update = pdo_query("update ims_ewei_shop_member set memberlimit=memberlimit+".$count." where openid='".$_W['openid']."' and uniacid=".$_W['uniacid']);

		$tomember = m('member')->getMember($otheropenid);
		$tomember['nickname'] = empty($tomember['nickname']) ? '未更新' : $tomember['nickname'];
		$tomember['nickname'] = empty($tomember['realname']) ? $tomember['nickname'] : $tomember['realname'];
		$member =m('member')->getMember($_W['openid']);
		$member['nickname'] = empty($member['realname']) ? $member['nickname'] : $member['realname'];
		$data = array(
			'uniacid'=>$_W['uniacid'],
			'createtime'=>TIMESTAMP,
		);
		
		$data['times'] = $count;
		$data['remark'] =empty($type) ? $tomember['nickname'].' 注册消耗' : ' 赠予:'.$tomember['nickname'];
		$data['openid'] = $_W['openid'];
		pdo_insert('ewei_shop_space_limit', $data);
		if (!empty($type)) {
			$data['times'] = 0-$count;
			$data['remark'] = $member['nickname'].' 赠予';
			$data['openid'] = $tomember['openid'];
			pdo_insert('ewei_shop_space_limit', $data);
		}
		

		$mycount = pdo_fetchcolumn("select memberlimit from ".tablename('ewei_shop_member')." where uniacid=:uniacid and openid=:openid",array(':uniacid'=>$_W['uniacid'],':openid'=>$_W['openid']));

		return $mycount;
	}


	// 解除关系
	public function remove(){
		global $_W,$_GPC;

		$otheropenid = $_GPC['openid'];
		$member = m('member')->getMember($otheropenid);
		$data = p('commission')->getAgentId(0,$member);
		$data['agentid'] = 0;
		$data['isagent'] = 0;
		$data['status'] = 0;
		$update = pdo_update('ewei_shop_member', $data, array('openid'=>$otheropenid, 'uniacid' => $_W['uniacid']));

		$teamlist = pdo_fetchall('select id,agentid,level from '.tablename('ewei_shop_member').' where uniacid=:uniacid and find_in_set('.$member['id'].',fids) order by fids asc',array(':uniacid'=>$_W['uniacid']));

		foreach ($teamlist as $k2 => $v2) {
			$agentdata = p('commission')->getAgentId($v2['agentid'],$v2);
			pdo_update('ewei_shop_member',$agentdata,array('id'=>$v2['id']));
		}

		show_json(1,'解除成功');
	}
	//获取升级信息
	public function get_upinfo(){
		global $_W,$_GPC;
		$openid = $_GPC['openid'];
		$info = pdo_fetch('SELECT * FROM'.tablename('ewei_shop_diyform_information').'where uniacid='.$_W['uniacid']." and status=0 and openid='".$openid."' order by id desc");
		$ainfo = iunserializer($info['diyfieldsdata']);
		$upinfo = array();
		$upinfo['name'] = $ainfo['diyxingming'];
		$upinfo['tel'] = $ainfo['diylianxidianhua'];
		$upinfo['time'] = date("Y-m-d H:i:s",$info['createTime']);
		$upinfo['level'] = $ainfo['diyshenqingdengji'];
		show_json(1,array('upinfo'=>$upinfo,'test'=>$info));
	}
}
?>
