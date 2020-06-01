<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Attendance_EweiShopV2Page extends WebPage
{
	public function main()
	{
		global $_W;
		global $_GPC;
		$pindex = max(1, intval($_GPC['page']));
		$psize = 40;
		$status = $_GPC['status'];
		$type = $_GPC['type'];
		if(!$type) $type="date";
		$condition = ' and uniacid = :uniacid and type=:type';
		$params = array(':uniacid' => $_W['uniacid'],':type' => $type);
		if (!empty($_GPC['keyword'])) {
			$_GPC['keyword'] = trim($_GPC['keyword']);
			$condition .= ' and name like :keyword';
			$params[':keyword'] = '%' . $_GPC['keyword'] . '%';
		}
		if (!empty($_GPC['name'])) {
			$_GPC['name'] = trim($_GPC['name']);
			$condition .= ' and name like :name';
			$params[':name'] = '%' . $_GPC['name'] . '%';
		}

		if($_GPC['task']=='worktime'){
			set_time_limit(0);
			$list =  pdo_fetchall('SELECT *  FROM ' . tablename('ewei_shop_perm_Attendance') . ' WHERE 1 ' . $condition, $params);
			$overTimeBegin="18:00:00";
			foreach ($list as $k => $v) {
				if($v['second']>$overTimeBegin){
					$overtime=(strtotime($v['second'])-strtotime($overTimeBegin))/3600;
					$overtime=floor($overtime*100)/100;
					$timelimit = $_GPC['timelimit']/60;
					if (!empty($timelimit)) {
						if ($overtime<2) {
							$overtime=0;
						}else{
							$residue = round(fmod($overtime,$timelimit),2);
							if ($timelimit<1) {
								$overtime = $overtime-$residue;
							}else{
								$overtime = floor($overtime);
							}
							
						}
					}
					if($v['overtime']!=$overtime){
						$postdata=array("overtime"=>$overtime);
						pdo_update('ewei_shop_perm_Attendance', $postdata, array('id' => $v['id'], 'uniacid' => $_W['uniacid']));
					}
				}
			}
			
		}

		if ($_GPC['status'] != '') {
			$condition .= ' and status=' . intval($_GPC['status']);
		}
		$psize=40;
		$list = pdo_fetchall('SELECT *  FROM ' . tablename('ewei_shop_perm_Attendance') . ' WHERE 1 ' . $condition . ' ORDER BY id desc LIMIT ' . (($pindex - 1) * $psize) . ',' . $psize, $params);
		$weekarray=array("日","一","二","三","四","五","六");
		foreach ($list as &$row) {
			if($row['date']) $row['week'] =$weekarray[date("w",strtotime($row['date']))];
			
		}
		unset($row);
		$total = pdo_fetchcolumn('SELECT count(*) FROM ' . tablename('ewei_shop_perm_Attendance') . '  WHERE 1 ' . $condition . ' ', $params);
		$pager = pagination2($total, $pindex, $psize);
		$nameList=$this->nameList();
		$typeList=$this->typeList();
		include $this->template();
	}
	public function nameList(){
		global $_W;
		$sql="SELECT name  FROM " . tablename('ewei_shop_perm_Attendance') . " WHERE  uniacid =$_W[uniacid] group by name ";
		$nameList = pdo_fetchall($sql, array());
		return $nameList;	
	}
	public function typeList(){
		$typeList=array(
			'date'=>array('value'=>'date','name'=>'日报表'),
			'month'=>array('value'=>'month','name'=>'月报表'),
			'year'=>array('value'=>'year','name'=>'年报表')
		);
		return $typeList;	
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
		$nameList=$this->nameList();
		$typeList=$this->typeList();
		$id = intval($_GPC['id']);
		$item = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_perm_Attendance') . ' WHERE id =:id and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':id' => $id));
		if($item['type']=='month') $_GPC['batch']=2;
		if ($_W['ispost']||$_GPC['ispost']) {
			$post=$_GPC['post'];
			$post['uniacid']=$_W['uniacid'];
			if (!empty($id)) {
				pdo_update('ewei_shop_perm_Attendance', $post, array('id' => $id, 'uniacid' => $_W['uniacid']));
				plog('perm.role.edit', '修改出勤 ID: ' . $id);
			}
			else {
				if($_GPC['batch']==2){
					$post['type']='month';
					$month=$_GPC['month'];
					if($month>0 and $month<13){
						$month=strtotime("2018-".$month."-01");
						if($_GPC['name']) $nameList=array("name"=>$_GPC['name']);
						foreach($nameList as $k=>$t){
							$post['date']=date("Y-m-d",$month);
							$trueDate=date("Y-m",$month);
							$post['name']=$t['name'];
							$typeDate="date";
							$sql="SELECT sum(overtime) FROM " . tablename('ewei_shop_perm_Attendance') . "  WHERE uniacid =$_W[uniacid] and type='$typeDate' and date like '%$trueDate%' and name='$post[name]' ";
							$total = pdo_fetchcolumn($sql, array());
							$post['overtime']=$total;
							$sql="SELECT id  FROM " . tablename('ewei_shop_perm_Attendance') . " WHERE  uniacid =$_W[uniacid] and type='$post[type]' and date='$post[date]' and name='$post[name]'";
							$check = pdo_fetch($sql);
							if(empty($check['id'])){
								pdo_insert('ewei_shop_perm_Attendance', $post);
								$id = pdo_insertid();
							}else{
								$id=$check['id'];
								pdo_update('ewei_shop_perm_Attendance', $post, array('id' => $id, 'uniacid' => $_W['uniacid']));
							}	
						}
						plog('perm.role.add', '批量创建月报表: ' . $id . ' ');
					}	
				}else{
					$post['type']='date';
					$month=$_GPC['month'];
					if($month>0 and $month<13){
						$time=strtotime("2018-".$month."-1");
						$days=date("t",$time);
						for($i=0;$i<$days;$i++){
							$datetime=$time+$i*86400;
							$post['date']=date("Y-m-d",$datetime);
							$sql="SELECT id  FROM " . tablename('ewei_shop_perm_Attendance') . " WHERE  uniacid =$_W[uniacid] and type='$post[type]' and date='$post[date]' and name='$post[name]'";
							$check = pdo_fetch($sql);
							if(empty($check['id'])){
								pdo_insert('ewei_shop_perm_Attendance', $post);
								$id = pdo_insertid();
							}	
						}
						plog('perm.role.add', '批量添加出勤 ID: ' . $id . ' ');
					}else{
						$sql="SELECT id  FROM " . tablename('ewei_shop_perm_Attendance') . " WHERE  uniacid =$_W[uniacid] and type='$post[type]' and date='$post[date]' and name='$post[name]'";
						$check = pdo_fetch($sql);
						if(empty($check['id'])){
							pdo_insert('ewei_shop_perm_Attendance', $post);
							$id = pdo_insertid();
							plog('perm.role.add', '添加出勤 ID: ' . $id . ' ');
						}
					}
				}	
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

		$items = pdo_fetchall('SELECT id,name FROM ' . tablename('ewei_shop_perm_Attendance') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);

		foreach ($items as $item) {
			pdo_delete('ewei_shop_perm_Attendance', array('id' => $item['id']));
			plog('perm.role.delete', '删除出勤 ID: ' . $item['id'] . ' 出勤名称: ' . $item['name'] . ' ');
		}

		show_json(1, array('url' => referer()));
	}
	public function setValue()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		$first = $_GPC['first'];
		$first=str_replace(chr(13),"",$first);
		$firstArr=explode(" ",$first);
		if($firstArr[0]) $post['first']=$firstArr[0];
		if($firstArr[1]) $post['second']=$firstArr[1];
		$code['status']=0;
		if($post){
			$re=pdo_update('ewei_shop_perm_Attendance', $post, array('id' => $id, 'uniacid' => $_W['uniacid']));
			plog('perm.role.edit', '修改出勤 ID: ' . $id);
			$code=$post;
			$code['status']=1;
		}
		echo json_encode($code);
		exit;
		die();
	}

	public function status()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);

		if (empty($id)) {
			$id = (is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0);
		}

		$status = intval($_GPC['status']);
		$items = pdo_fetchall('SELECT id,name FROM ' . tablename('ewei_shop_perm_Attendance') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);

		foreach ($items as $item) {
			pdo_update('ewei_shop_perm_Attendance', array('status' => $status), array('id' => $item['id']));
			plog('perm.role.edit', '修改出勤状态 ID: ' . $item['id'] . ' 出勤名称: ' . $item['name'] . ' 状态: ' . ($status == 0 ? '禁用' : '启用'));
		}

		show_json(1, array('url' => referer()));
	}

	public function query()
	{
		global $_GPC;
		global $_W;
		$kwd = trim($_GPC['keyword']);
		$params = array();
		$params[':uniacid'] = $_W['uniacid'];
		$condition = ' and uniacid=:uniacid';

		if (!empty($kwd)) {
			$condition .= ' AND `name` LIKE :keyword';
			$params[':keyword'] = '%' . $kwd . '%';
		}

		$ds = pdo_fetchall('SELECT id,name,perms2 FROM ' . tablename('ewei_shop_perm_Attendance') . ' WHERE status=1 ' . $condition . ' order by id asc', $params);
		include $this->template();
		exit();
	}
}

?>
