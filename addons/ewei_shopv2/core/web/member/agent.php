<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Agent_EweiShopV2Page extends WebPage
{
	public function main()
	{
		global $_W;
		global $_GPC;

		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		$set = p('commission')->getSet();
		if (isset($_GPC['status'])&&$_GPC['status']!=-1) {

			$condition = " and  a.status= '{$_GPC[status]}' ";
		}
		
		if (!empty($_GPC['realname'])) {
			$_GPC['realname'] = trim($_GPC['realname']);
			$condition .= ' and ( m.realname like :realname or m.nickname like :realname or m.mobile like :realname or m.id like :realname)';
			$params[':realname'] = '%' . $_GPC['realname'] . '%';
		}

	
		if (!empty($_GPC['time']['start']) && !empty($_GPC['time']['end'])) {
			$starttime = strtotime($_GPC['time']['start']);
			$endtime = strtotime($_GPC['time']['end']);
			$condition .= ' AND a.createtime >= :starttime AND a.createtime <= :endtime ';
			$params[':starttime'] = $starttime;
			$params[':endtime'] = $endtime;
		}


		$sql=" SELECT a.*,m.nickname,m.avatar,m.openid,m.level as oldlevel,m.agentid,m.id as mid,m.realname,m.mobile,m.isblack,m.branch,m.detp,m.groups FROM ".tablename('ewei_shop_member_apply')." as a left join ".tablename('ewei_shop_member')." as m ON m.openid=a.openid WHERE a.uniacid='{$_W[uniacid]}' $condition LIMIT ".(($pindex - 1) * $psize).",".$psize;
	
		$total = pdo_fetchcolumn("SELECT count(*)  from ".tablename('ewei_shop_member_apply')." WHERE uniacid=$_W[uniacid]");

		$list = pdo_fetchall($sql,$params);
	
		foreach ($list as &$row) {
			//2020.5.8
			$branch = pdo_get('ewei_shop_branch', array('branch_id' => $row['branch']), array('branch_name'));
			$detp = pdo_get('ewei_shop_detp', array('detp_id' => $row['detp']), array('detp_name'));
			$groups = pdo_get('ewei_shop_group', array('group_id' => $row['groups']), array('group_name'));
			if(!empty($branch)){
				$row['branch'] = $branch['branch_name']."/".$detp['detp_name']."/".$groups['group_name'];
				
			}else{
				$row['branch'] = "";
			}
			//end
			$item  = pdo_fetch("SELECT levelname from ".tablename('ewei_shop_member_level')." WHERE uniacid='{$_W[uniacid]}' AND id='{$row[oldlevel]}'");
			$agent = pdo_fetch('select avatar,nickname from '.tablename('ewei_shop_member').' where uniacid=:uniacid and id=:id',array(':uniacid'=>$_W['uniacid'],':id'=>$row['agentid']));
			$row['agentavatar'] = $agent['avatar'];
			$row['agentnickname'] = $agent['nickname'];
			$memberdata = unserialize($row[memberdata]);
			foreach ($memberdata as $key => $value) {
				if ($key=='realname') {
					$row['content'] .="姓名：".$value."<br />";
				}elseif ($key=='mobile') {
					$row['content'] .="手机：".$value."<br />";
				}elseif ($key=='birthday') {
					$row['content'] .="生日：".$value."<br />";
				}elseif ($key=='city') {
					$row['content'] .="城市：".$value."<br />";
				}
			}
			$row['levelname'] = $item['levelname'];
			$row['levelname'] = empty($row['levelname']) ? (empty($shop['levelname']) ? '普通会员' : $shop['levelname']) : $row['levelname'];

			$row['applylevel'] =pdo_fetch("select * from ".tablename('ewei_shop_member_level')." WHERE uniacid=$_W[uniacid] AND id=$row[level]");

		
		}
		unset($row);
		$pager = pagination2($total, $pindex, $psize);
		include $this->template();
	}

	public function add()
	{
		$this->post();
	}

	public function edit()
	{
		$this->post();
	}

	protected function post()
	{
		global $_W;
		global $_GPC;

		$id = trim($_GPC['id']);

		$detail = pdo_fetch('select * from ' . tablename('ewei_shop_member_apply') . ' where uniacid=:uniacid and id=:id limit 1',array(':uniacid'=>$_W['uniacid'],':id'=>$id));

		$memberdata = unserialize($detail["memberdata"]);

		$member = m("member")->getMember($detail['openid']);
		$level = pdo_fetch("SELECT id,levelname from ".tablename('ewei_shop_member_level')." WHERE uniacid='{$_W[uniacid]}' AND id='{$detail[level]}'");
		$agent = pdo_fetch('select * from '.tablename('ewei_shop_member').' where uniacid=:uniacid and id=:id limit ',array(':uniacid'=>$_W['uniacid'],':id'=>$member['agentid']));
		
		#2020.5.8
		$branch = pdo_fetchall("SELECT * from ".tablename('ewei_shop_branch'));
		$detp = pdo_fetchall("SELECT * from ".tablename('ewei_shop_detp'));
		$group = pdo_fetchall("SELECT * from ".tablename('ewei_shop_group'));
		#end

		if ($_W['ispost']) {
			//获取公司 部门  组
			$branchid = intval($_GPC['branch']);
			$detpid   = intval($_GPC['detp']);
			$groupid  = intval($_GPC['groups']);
			$realname = $_GPC['realname'];
			//执行修改
			pdo_update('ewei_shop_member', array('branch' => $branchid, 'detp' => $detpid ,'groups'=>$groupid,'realname'=>$realname), array('uniacid'=>$_W['uniacid'],'openid'=>$member['openid']));
			//end
			if($detail['status']!=0){
				show_json(0,'无法重复审核');
			}
			$status = intval($_GPC['status']);
			if($status==0){
				show_json(0,'请选择审核状态');
			}
			if($_GPC['status']==1){
				//更新绑定手机号
				$salt = m('account')->getSalt();
				m('bind')->update($member['id'], array('mobile' => $memberdata['mobile'], 'pwd' => md5($memberdata['pwd'] . $salt), 'salt' => $salt, 'mobileverify' => 1));
				pdo_update('ewei_shop_member',array('level'=>$level['id'],'status'=>1,'isagent'=>1),array('uniacid'=>$_W['uniacid'],'openid'=>$member['openid']));
				$member = m("member")->getMember($detail['openid']);
				//更新关系
				$teamlist = pdo_fetchall('select * from '.tablename('ewei_shop_member').' where uniacid=:uniacid and find_in_set('.$member['id'].',fids) order by fids asc',array(':uniacid'=>$_W['uniacid']));
				foreach ($teamlist as $k => $v) {
					$agentdata1 = p('commission')->getAgentId($v['agentid'],$v);
					pdo_update('ewei_shop_member',$agentdata1,array('id'=>$v['id']));
				}
			}
			//发送消息
			if($status==1){
				$statustext = '已通过';
			}
			if($status==-1){
				$statustext = '驳回申请';
			}
			$messagetext = "代理申请审核通知\n\n";
	    	$messagetext .= "审核状态:".$statustext."\n";
			$messagetext .= "审核时间:".date('Y-m-d H:i')."\n";
			if($_GPC['remark']){
				$messagetext .= "审核备注:".$_GPC['remark']."\n";
			}
			if($status==1){
				$urls = mobileUrl('member.agent',array(),true);
				$messagetext .= "\n<a href='" . $urls . '\'>前往代理中心</a>';
			}
			
			m('message')->sendTexts($detail['openid'], $messagetext);
			pdo_update('ewei_shop_member_apply',array('status'=>$status,'updatetime'=>time(),'remark'=>$_GPC['remark']),array('uniacid'=>$_W['uniacid'],'id'=>$id));
			show_json();
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

		$memberApplys = pdo_fetchall('SELECT * FROM ' . tablename('ewei_shop_member_apply') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);

		foreach ($memberApplys as $apply) {
			pdo_delete('ewei_shop_member_apply', array('id' => $apply['id']));
			plog('member.list.delete', '删除会员申请  ID: ' . $apply['id'] . ' <br/>会员申请信息: ');
		}

		show_json(1, array('url' => referer()));
	}

	public function check()
	{
		global $_W;
		global $_GPC;
		$set = p('commission')->getSet();
		$status=$_GPC['status'];

		$id=$_GPC['id'];

		if (empty($id)) {
			show_json(0, '参数错误');
		}

		if (empty($id)) {
			$id = (is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0);
		}

		$applys = pdo_fetchall('SELECT * FROM ' . tablename('ewei_shop_member_apply') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);
		$data=array(
			'updatetime'=>time(),
			'status'=>$status
		);
		foreach($applys as $item) {
			pdo_update('ewei_shop_member_apply', $data,array('id' => $item[id], 'uniacid' => $_W['uniacid']));

			// $mlevelId = pdo_fetchcolumn();
			pdo_update('ewei_shop_member',array('level' =>$item[level]),array('openid' => $item['openid'], 'uniacid' => $_W['uniacid']));

			$member=m('member')->getMember($item['openid']);
			if (empty($member['informationreward'])) {

				$data = m('common')->getSysset('member');
				if ($data['consummatetype']=='coupon') {
					$couponArr = explode(',', $data['consummatereward']);

					if ($couponArr) {

						foreach ($couponArr as $value) {
							com('coupon')->sendTicket($item['openid'],$value,0);				 	}

					 }
					 pdo_update('ewei_shop_member', array('informationreward'=>1), array('openid' => $item['openid'], 'uniacid' => $_W['uniacid']));
				}

			}
		}

		show_json(1);
		

	}
	public function groupholder(){
		global $_W,$_GPC;
		$id = $_GPC['id'];
		if ($_W['ispost']) {
			$data = array(
				'0'=>$_GPC['share_holder1'],
				'1'=>$_GPC['share_holder2'],
				'2'=>$_GPC['share_holder3'],
				'3'=>$_GPC['share_holder4'],
				'4'=>$_GPC['share_holder5'],
				'5'=>$_GPC['share_holder6'],
				'6'=>$_GPC['share_holder7'],
				'7'=>$_GPC['share_holder8'],
				'8'=>$_GPC['share_holder9']
			);
			$data= array_unique(array_filter($data));
			$data_ok = array();
			foreach ($data as $key => $value) {
				$res = pdo_fetch('select id from '.tablename('ewei_shop_member').' where uniacid=:uniacid and find_in_set('.$value.',share_holder) and id<>:id',array(':uniacid'=>$_W['uniacid'],':id'=>$id));
				if (empty($res)) {
					array_push($data_ok, $value);
				}else{
					$has_other = true;
				}
			}
			$update['share_holder'] = implode(',', $data_ok);
			pdo_update('ewei_shop_member',$update,array('id'=>$id));
			show_json(1);
		}
		$share_holders = pdo_fetchcolumn('select share_holder from '.tablename('ewei_shop_member').' where uniacid=:uniacid and id=:id',array(':uniacid'=>$_W['uniacid'],':id'=>$id));
		$shareids = explode(',', $share_holders);
		foreach ($shareids as $key => $mid) {
			++$key;
			$mm = 'member'.$key;
			$$mm = m('member')->getMember($mid);
		}
		include $this->template();
	}

		/*芭提雅 充值计算返利
	*$param $openid 充值者openid
	*$param $amount 充值金额
	*
	*return 无返回值
	*/
	public function bty_rechargeCommission($openid,$level)
	{
		global $_W;
		$set = p('commission')->getSet();
		$member = m('member')->getMember($openid);
		$levelName = pdo_fetchcolumn('select levelname from '.tablename('ewei_shop_member_level').' where uniacid=:uniacid and level=:level',array(':uniacid'=>$_W['uniacid'],':level'=>$level));
		if(!empty($member)){
			$m1 = m('member')->getMember($member['agentid']);

			if(!empty($m1)){
				$m1_alevel = p('commission')->getLevel($m1['openid']);
				if($m1_alevel['level']>92){
					$amount = $set['agent_level1'][$level];
					$remark = '恭喜您的'.$_W['shopset']['commission']['texts']['c1'].'分销商'.$member['nickname'].'升级成为'.$levelName.'!';
					m('message')->sendTexts($m1['openid'],$remark);
					$this->setLevelRecord($m1['openid'],$amount,'升级'.$levelName.'获得'.$_W['shopset']['commission']['texts']['c1'].'奖励');
					unset($amount);
				}
			}
			if(!empty($m1['agentid'])){
				$m2 = m('member')->getMember($m1['agentid']);
				if(!empty($m2)){
					$m2_alevel = p('commission')->getLevel($m2['openid']);
					if($m2_alevel['level']>92){
						$amount = $set['agent_level2'][$level];
						$remark = '恭喜您的'.$_W['shopset']['commission']['texts']['c2'].'分销商'.$member['nickname'].'升级成为'.$levelName.'!';
					m('message')->sendTexts($m2['openid'],$remark);
						$this->setLevelRecord($m2['openid'],$amount,'升级'.$levelName.'获得'.$_W['shopset']['commission']['texts']['c2'].'奖励');
					}
				}
				if(!empty($m2['agentid'])){
					$m3 = m('member')->getMember($m2['agentid']);
					$m3_alevel = p('commission')->getLevel($m3['openid']);
					if($m3_alevel['level']>92){
						$amount = $set['agent_level3'][$level];
						$remark = '恭喜您的'.$_W['shopset']['commission']['texts']['c3'].'分销商'.$member['nickname'].'升级成为'.$levelName.'!';
						m('message')->sendTexts($m3['openid'],$remark);
						$this->setLevelRecord($m3['openid'],$amount,'升级'.$levelName.'获得'.$_W['shopset']['commission']['texts']['c3'].'奖励');
					}
				}
			}
		}
	}

	public function setLevelRecord($openid,$amount,$remark)
	{
		global $_W;
		m('common')->setRecord($openid,$amount,array($remark,array('111'=>222)));
		$total = pdo_fetchcolumn('select sum(amount) from '.tablename('ewei_shop_commission_record').' where uniacid=:uniacid and openid=:openid',array(':uniacid'=>$_W['uniacid'],':openid'=>$openid));
		pdo_update('ewei_shop_member',array('credit3'=>$total),array('uniacid'=>$_W['uniacid'],'openid'=>$openid));

	}
	public function getOpenid($id)
	{
		global $_W;
		if(!empty($id)){
			$openid = pdo_fetchcolumn('select openid from '.tablename('ewei_shop_member').' where uniacid=:uniacid and id=:id',array(':uniacid'=>$_W['uniacid'],':id'=>$id));
			if($openid){
				return $openid;
			}	
		}else{
			return '不存在的id';
		}
	}

	
}

?>
