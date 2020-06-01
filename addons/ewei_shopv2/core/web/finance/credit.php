<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Credit_EweiShopV2Page extends WebPage
{
	protected function main($type = 'credit1',$iswxapp = 0)
	{
		global $_W;
		global $_GPC;
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		$iswxapp = !empty($_GPC['iswxapp'])?:$iswxapp;
		$condition = ' and log.uniacid=:uniacid and (log.module=:module1  or log.module=:module2)and m.uniacid=:uniacid  and log.credittype=:credittype';
		$params = array(':uniacid' => $_W['uniacid'], ':module1' => 'ewei_shopv2', ':module2' => 'ewei_shop', ':credittype' => ($type!='wxapp')?$type:'credit2');

		if (!empty($_GPC['keyword'])) {

			$_GPC['keyword'] = trim($_GPC['keyword']);
			$condition .= ' and (m.realname like :keyword or m.nickname like :keyword or m.mobile like :keyword )';
			$params[':keyword'] = '%' . $_GPC['keyword'] . '%';
		}

		if (empty($starttime) || empty($endtime)) {
			$starttime = strtotime('-1 month');
			$endtime = time();
		}

		if (!empty($_GPC['time']['start']) && !empty($_GPC['time']['end'])) {
			$starttime = strtotime($_GPC['time']['start']);
			$endtime = strtotime($_GPC['time']['end']);
			$condition .= ' AND log.createtime >= :starttime AND log.createtime <= :endtime ';
			$params[':starttime'] = $starttime;
			$params[':endtime'] = $endtime;
		}

		if (!empty($_GPC['level'])) {
			$condition .= ' and m.level=' . intval($_GPC['level']);
		}

		if (!empty($_GPC['groupid'])) {
			$condition .= ' and m.groupid=' . intval($_GPC['groupid']);
		}

		$sql = 'select log.*,m.id as mid, m.realname,m.avatar,m.nickname,m.avatar, m.mobile, m.weixin,u.username from ' . tablename('mc_credits_record') . ' log ' . '  left join ' . tablename('ewei_shop_member') . ' m on m.openid=log.openid' . ' left join ' . tablename('ewei_shop_member_group') . ' g on m.groupid=g.id' . ' left join ' . tablename('ewei_shop_member_level') . ' l on m.level =l.id left join ' . tablename('users') . ' u on  u.uid=log.operator where 1 ' . $condition . ' ORDER BY log.createtime DESC ';

		if (empty($_GPC['export'])) {
			$sql .= 'LIMIT ' . (($pindex - 1) * $psize) . ',' . $psize;
		}
		else {
			ini_set('memory_limit', '-1');
		}

		$list = pdo_fetchall($sql, $params);
		if ($_GPC['export'] == 1) {
			if ($_GPC['type'] == 1) {
				plog('finance.credit.credit1.export', '导出'.$_W['shopset']['trade']['credittext'].'明细');
			}
			else {
				plog('finance.credit.credit2.export', '导出余额明细');
			}

			foreach ($list as &$row) {
				$row['createtime'] = date('Y-m-d H:i', $row['createtime']);
				$row['groupname'] = empty($row['groupname']) ? '无分组' : $row['groupname'];
				$row['levelname'] = empty($row['levelname']) ? '普通会员' : $row['levelname'];

				if ($row['credittype'] == 'credit1') {
					$row['credittype'] = ''.$_W['shopset']['trade']['credittext'].'';
				}
				else {
					if ($row['credittype'] == 'credit2') {
						$row['credittype'] = '余额';
					}
				}

				if (empty($row['username'])) {
					$row['username'] = '本人';
				}
			}

			unset($row);
			$columns = array();
			$columns[] = array('title' => '类型', 'field' => 'credittype', 'width' => 12);
			$columns[] = array('title' => '昵称', 'field' => 'nickname', 'width' => 12);
			$columns[] = array('title' => '姓名', 'field' => 'realname', 'width' => 12);
			$columns[] = array('title' => '手机号', 'field' => 'mobile', 'width' => 12);
			$columns[] = array('title' => '会员等级', 'field' => 'levelname', 'width' => 12);
			$columns[] = array('title' => '会员分组', 'field' => 'groupname', 'width' => 12);
			$columns[] = array('title' => $type == 'credit1' ? ''.$_W['shopset']['trade']['credittext'].'变化' : '余额变化', 'field' => 'num', 'width' => 12);
			$columns[] = array('title' => '时间', 'field' => 'createtime', 'width' => 12);
			$columns[] = array('title' => '备注', 'field' => 'remark', 'width' => 24);
			$columns[] = array('title' => '操作人', 'field' => 'username', 'width' => 12);
			m('excel')->export($list, array('title' => ($type == 'credit1' ? '会员'.$_W['shopset']['trade']['credittext'].'明细-' : '会员余额明细') . date('Y-m-d-H-i', time()), 'columns' => $columns));
		}

		$total = pdo_fetchcolumn('select count(*) from ' . tablename('mc_credits_record') . ' log ' . ' left join ' . tablename('ewei_shop_member') . ' m on m.openid=log.openid' . ' left join ' . tablename('ewei_shop_member_group') . ' g on m.groupid=g.id' . ' left join ' . tablename('ewei_shop_member_level') . ' l on m.level =l.id left join ' . tablename('users') . ' u on  u.uid=log.operator where 1 ' . $condition . ' ', $params);
		
		$pager = pagination2($total, $pindex, $psize);
		$groups = m('member')->getGroups();
		$levels = m('member')->getLevels();

		include $this->template('finance/credit');
		
	}

	public function credit1()
	{
		$this->main('credit1');
	}

	public function credit2()
	{
		$this->main('credit2');
	}

	public function wxapp(){
		$this->main('wxapp',1);
	}

	public function otherrecord()
	{
		global $_W;
		global $_GPC;
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;

		if (!empty($_GPC['keyword'])) {
			$_GPC['keyword'] = trim($_GPC['keyword']);
			$condition .= ' and (sm.nickname like :keyword or sm.mobile like :keyword or cc.remark like :keyword)';
			$params[':keyword'] = '%' . $_GPC['keyword'] . '%';
		}

		if (empty($starttime) || empty($endtime)) {
			$starttime = strtotime('-1 month');
			$endtime = time();
		}
		if($_GPC['status']=="defult"||!isset($_GPC['status'])){		
		}else{
			$condition .= ' and cc.status=' . intval($_GPC['status']);	
		}
		if (!empty($_GPC['time']['start']) && !empty($_GPC['time']['end'])) {
			$starttime = strtotime($_GPC['time']['start']);
			$endtime = strtotime($_GPC['time']['end']);
			$condition .= ' AND cc.createtime >= :starttime AND cc.createtime <= :endtime ';
			$params[':starttime'] = $starttime;
			$params[':endtime'] = $endtime;
		}
		if($_GPC['export'] == 1){
			$limit ='';
		}else{
			$limit = "limit ".(($pindex - 1) * $psize).",".$psize;
		}
		$sql = "select cc.id,cc.status,cc.remark,cc.amount,cc.createtime,sm.id as mid,sm.nickname,sm.avatar,sm.nickname,sm.realname,sm.mobile,sm.id as mid from".tablename("ewei_shop_commission_record")."as cc,".tablename("ewei_shop_member")."as sm where cc.uniacid=:uniacid and sm.uniacid=:uniacid and cc.openid = sm.openid".$condition." order by cc.createtime desc ".$limit;
		$params[':uniacid'] = $_W['uniacid'];
		$list = pdo_fetchall($sql,$params);
	
		if ($_GPC['export'] == 1) {
			foreach($list as &$v){
				$v['createtime'] = date('Y-m-d H:y:s',$v['createtime']);
			}
			unset($v);
			m('excel')->export($list, array('title' => '佣金明细 '. date('Y-m-d-H-i', time()), 'columns' =>
				array( 
					array('title' => '会员id', 'field' => 'mid', 'width' => 12), 
					array('title' => '会员名', 'field' => 'nickname', 'width' => 12),
					array('title' => '手机号', 'field' => 'mobile', 'width' => 12),
					array('title' => '金额', 'field' => 'amount','width' => 12),
					array('title' => '备注', 'field' => 'remark', 'width' => 24),
					array('title' => '创建日期', 'field' => 'createtime', 'width' => 12)
					
				)
			));
		}
		$total = pdo_fetchcolumn("select count(*) from".tablename("ewei_shop_commission_record")."as cc,".tablename("ewei_shop_member")."as sm where cc.uniacid=:uniacid and cc.openid = sm.openid".$condition,$params);
		$totalsum = pdo_fetchcolumn("select sum(amount) as totalsum from".tablename("ewei_shop_commission_record")."as cc,".tablename("ewei_shop_member")."as sm where cc.uniacid=:uniacid and cc.openid = sm.openid".$condition,$params);
		$pager = pagination2($total, $pindex, $psize);
		
		include $this->template('finance/otherrecord');
	}
	public function fundrecord()
	{
		global $_W;
		global $_GPC;
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;

		if (!empty($_GPC['keyword'])) {
			$_GPC['keyword'] = trim($_GPC['keyword']);
			$condition .= ' and name like :keyword ';
			$params[':keyword'] = '%' . $_GPC['keyword'] . '%';
		}

		if (empty($starttime) || empty($endtime)) {
			$starttime = strtotime('-1 month');
			$endtime = time();
		}
		
		if (!empty($_GPC['time']['start']) && !empty($_GPC['time']['end'])) {
			$starttime = strtotime($_GPC['time']['start']);
			$endtime = strtotime($_GPC['time']['end']);
			$condition .= ' AND createtime >= :starttime AND createtime <= :endtime ';
			$params[':starttime'] = $starttime;
			$params[':endtime'] = $endtime;
		}
		if($_GPC['export'] == 1){
			$limit ='';
		}else{
			$limit = "limit ".(($pindex - 1) * $psize).",".$psize;
		}
		$sql = "select * from".tablename("ewei_shop_fund_record")."  where uniacid=:uniacid ".$condition." order by id desc ".$limit;
		$params[':uniacid'] = $_W['uniacid'];
		$list = pdo_fetchall($sql,$params);
		if ($_GPC['export'] == 1) {
			foreach($list as &$v){
				$v['createtime'] = date('Y-m-d H:y:s',$v['createtime']);
			}
			unset($v);
			m('excel')->export($list, array('title' => '公司分成明细 '. date('Y-m-d-H-i', time()), 'columns' =>
				array( 
					array('title' => '机器码', 'field' => 'machineid', 'width' => 12),
					array('title' => '名称', 'field' => 'name', 'width' => 12), 
					array('title' => '数量', 'field' => 'amount','width' => 12),
					array('title' => '创建日期', 'field' => 'createtime', 'width' => 12),
					array('title' => '备注', 'field' => 'remark', 'width' => 24)
				)
			));
		}
		$total = pdo_fetchcolumn("select count(*) from".tablename("ewei_shop_fund_record")." where uniacid=:uniacid ".$condition,$params);
		$pager = pagination2($total, $pindex, $psize);
		
		include $this->template('finance/fundrecord');
	}
	public function invoice1()
	{
		include $this->template('finance/invoice');
	}

	public function invoice2()
	{
		include $this->template('finance/invoice');
	}
}

?>
