<?php
//
?>
<?php
if (!(defined('IN_IA'))) {
	exit('Access Denied');
}


@session_start();
class Index_EweiShopV2Page extends PluginWebPage
{
	private $new = false;

	public function __construct()
	{
		parent::__construct();
		global $_GPC;
		global $_W;
		$this->new = $this->model->isnew();
	}


	/**
     * 任务概况
     */
	public function main()
	{
		global $_W;
		global $_GPC;
		$date = date('Y-m-d H:i:s');
		$task2S = array();
		$task2S[0] = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_task2_record') . ' where uniacid = ' . $_W['uniacid']);
		$task2S[1] = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_task2_record') . ' where uniacid = ' . $_W['uniacid'] . ' and finishtime > \'0000-00-00 00:00:00\' ');
		$task2S[2] = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_task2_record') . ' where uniacid = ' . $_W['uniacid'] . ' and finishtime = \'0000-00-00 00:00:00\' and stoptime < \'' . $date . '\'');
		$task2S[3] = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_task2_record') . ' where uniacid = ' . $_W['uniacid'] . ' and finishtime = \'0000-00-00 00:00:00\' and stoptime > \'' . $date . '\'');
		include $this->template('task2/new/index');
	}

	/**
     * 任务列表
     */
	public function tasklist()
	{
		global $_GPC,$_W;
		$page = (int) $_GPC['page'];
		$psize = 15;
		$page = max(1, $page);

		$pstart = ($page - 1) * $psize;
		$params = array(':uniacid' => $_W['uniacid']);
		$type = 'shop';
		$keyword = trim($_GPC['keyword']);
		$condition = '';
		if (!(empty($type)) || !(empty($keyword))) {
			$condition = ' and `title` like \'%' . $keyword . '%\' and `type` like \'%' . $type . '%\' ';
		}

		$field = 'id,displayorder,title,image,type,starttime,endtime,status';
		$sql = 'select ' . $field . ' from ' . tablename('ewei_shop_task2_list') . ' where uniacid = :uniacid ' . $condition . '  order by displayorder desc,id desc limit ' . $pstart . ' , ' . $psize;

		$list = pdo_fetchall($sql, $params);
		$countsql = substr($sql, 0, strpos($sql, 'order by'));
		$countsql = str_replace($field, 'count(*)', $countsql);
		$count = pdo_fetchcolumn($countsql, $params);
		$page = pagination2($count, $page, $psize);
		
		include $this->template('task2/new/tasklist');
	}

	/**
     * 任务记录
     */
	public function record()
	{
		global $_W;
		global $_GPC;
		$psize = 20;
		$page = (int) $_GPC['page'];
		$page = max(1, $page);
		$pstart = ($page - 1) * $psize;
		$params = array(':uniacid' => $_W['uniacid']);
		$condition = '';
		$keyword = trim($_GPC['keyword']);
		!(empty($keyword)) && ($condition .= ' and tasktitle like \'%' . $keyword . '%\' ');
		$starttime = $_GPC['time']['start'];
		!(empty($starttime)) && ($condition .= ' and picktime > \'' . $starttime . '\' ');
		$endtime = $_GPC['time']['end'];
		!(empty($endtime)) && ($condition .= ' and picktime < \'' . $endtime . '\' ');
		$field = '*';
		$sql = 'select ' . $field . ' from ' . tablename('ewei_shop_task2_record') . 'where uniacid = :uniacid ' . $condition . ' order by id desc limit ' . $pstart . ' , ' . $psize;
		$records = pdo_fetchall($sql, $params);
		$countsql = substr($sql, 0, strpos($sql, 'order by'));
		$countsql = str_replace($field, 'count(*)', $countsql);
		$count = pdo_fetchcolumn($countsql, $params);
		$page = pagination2($count, $page, $psize);
		include $this->template('task2/new/record');
	}

	public function universaldata(){
		global $_W;
		global $_GPC;
		$psize = 20;
		$page = (int) $_GPC['page'];
		$page = max(1, $page);
		$pstart = ($page - 1) * $psize;
		$params = array(':uniacid' => $_W['uniacid']);
		$field = '*';

		if ($_GPC[taskid]) {
			$condition =  "and taskid='{$_GPC[taskid]}'";
		}
		$sql = 'select ' . $field . ' from ' . tablename('ewei_shop_task2_universaldata') . 'where uniacid = :uniacid ' . $condition . ' order by id desc limit ' . $pstart . ' , ' . $psize;
		$params = array(':uniacid' => $_W['uniacid']);

		$records = pdo_fetchall($sql, $params);
		$lists = array(); 
		foreach ($records as $key=> $value) {
			$member=m('member')->getInfo($value['openid']);
			$task = $this->model->getThisTask($value['taskid']);
			$value['title']=$task[title];
			$value['nickname']=$member['nickname'];
			$value['realname']=$member['realname'];
			$value['universaldata']=unserialize($value[universaldata]);
			$lists[$key]=$value;
		}

		$countsql = substr($sql, 0, strpos($sql, 'order by'));
		$countsql = str_replace($field, 'count(*)', $countsql);
		$count = pdo_fetchcolumn($countsql, $params);
		$page = pagination2($count, $page, $psize);

		include $this->template('task2/new/universaldata');

	}

	

	/**
     * 任务消息通知
     */
	public function notice()
	{
		global $_W;
		global $_GPC;

		if ($_W['ispost']) {
			$msg = $_GPC['msg'];
			$data['msg_pick'] = $msg['pick'];
			$data['msg_progress'] = $msg['progress'];
			$data['msg_finish'] = $msg['finish'];
			$data['msg_follow'] = $msg['follow'];
			$saveRet = $this->model->taskSave('ewei_shop_task_set', $data);
			empty($saveRet) && show_json(0, '保存失败');
			show_json(1);
		}


		$msg = $this->model->getMessageSet();
		include $this->template('task/new/notice');
	}

	/**
     * 编辑任务设置
     */
	public function setting()
	{
		global $_W;
		global $_GPC;
		$set = pdo_get('ewei_shop_task2_set', array('uniacid' => $_W['uniacid']));

		if ($_W['ispost']) {
			$set['entrance'] = intval($_GPC['entrance']);
			$set['keyword'] = trim($_GPC['keyword']);
			$set['upgrade_link'] = trim($_GPC['upgrade_link']);
			$set['upgrade_cate'] = $_GPC['upgrade_cate'];
			$set['upgrade_intro'] = trim($_GPC['upgrade_intro']);
			$set['qrcode_desc'] = trim($_GPC['qrcode_desc']);
			$set['cover_title'] = trim($_GPC['cover_title']);
			$set['cover_img'] = trim($_GPC['cover_img']);
			$set['bg_img'] = trim($_GPC['bg_img']);
			$set['cover_desc'] = trim($_GPC['cover_desc']);
			$saveRet = $this->model->taskSave('ewei_shop_task2_set', $set);
			empty($saveRet) && show_json(0, '保存失败');
			show_json(1);
		}


		include $this->template('task2/new/setting');
	}

	/**
     * 设置封面入口
     * @param $keyword
     * @param $img
     * @param $desc
     * @return bool
     */
	protected function saveCover($keyword, $img, $desc, $title)
	{
		global $_W;
		$entranceName = 'ewei_shop:任务中心入口封面';
		$rid = pdo_fetchcolumn('select id from ' . tablename('rule') . ' where uniacid = ' . $_W['uniacid'] . ' and `name` = \'' . $entranceName . '\'');

		if (empty($rid)) {
			if (pdo_insert('rule', array('uniacid' => $_W['uniacid'], 'name' => $entranceName, 'module' => 'cover', 'status' => 1))) {
				$rid = pdo_insertid();
			}

		}


		if (pdo_fetchcolumn('select count(1) from ' . tablename('rule_keyword') . ' where content = \'' . $keyword . '\' and uniacid = ' . $_W['uniacid'] . ' and rid <> ' . $rid)) {
			show_json(0, '关键词已存在，请更换');
		}


		$kid = pdo_fetchcolumn('select id from ' . tablename('rule_keyword') . ' where rid = ' . $rid);

		if (empty($kid)) {
			pdo_insert('rule_keyword', array('rid' => $rid, 'uniacid' => $_W['uniacid'], 'module' => 'cover', 'content' => $keyword, 'type' => 1, 'status' => 1));
		}
		 else {
			pdo_update('rule_keyword', array('rid' => $rid, 'uniacid' => $_W['uniacid'], 'module' => 'cover', 'content' => $keyword, 'type' => 1, 'status' => 1), array('id' => $kid));
		}

		$cid = pdo_fetchcolumn('select id from ' . tablename('cover_reply') . ' where rid = ' . $rid . ' and uniacid = ' . $_W['uniacid']);

		if (empty($cid)) {
			pdo_insert('cover_reply', array('uniacid' => $_W['uniacid'], 'rid' => $rid, 'module' => 'ewei_shopv2', 'title' => $title, 'description' => $desc, 'thumb' => $img, 'url' => mobileUrl('task', NULL, 1)));
		}
		 else {
			pdo_update('cover_reply', array('uniacid' => $_W['uniacid'], 'rid' => $rid, 'module' => 'ewei_shopv2', 'title' => $title, 'description' => $desc, 'thumb' => $img, 'url' => mobileUrl('task', NULL, 1)), array('id' => $cid));
		}

		return true;
	}

	/**
     * 添加/编辑任务
     */
	public function post()
	{
		global $_W;
		global $_GPC;
		$id = (int) $_GPC['id'];

		!(empty($id)) && ($task = $this->model->getThisTask($id));
		if (empty($task) && !(empty($id))) {
			header('location:' . webUrl('task2.post'));
			exit();
		}


		if ($_W['ispost']) {
			
		
			$posttype = trim($_GPC['posttype']);
			$data['title'] = trim($_GPC['title']);
			empty($data['title']) && show_json(0, '请填写标题');
			$data['image'] = trim($_GPC['image']);
			empty($data['image']) && show_json(0, '请上传任务图');
			$data['type'] = trim($_GPC['type']);

			$data['uniacid'] = $_W['uniacid'];
			$data['returnrate'] = $_GPC['returnrate'];
			$data['subtitle']  = $_GPC['subtitle'];

			$opentime = $_GPC['opentime'];
			$data['starttime'] = $opentime['start'];
			empty($data['starttime']) && show_json(0, '请选择开始时间');
			$data['endtime'] = $opentime['end'];
			empty($data['endtime']) && show_json(0, '请选择结束时间');
			$data['displayorder'] = $_GPC['displayorder'];
			$interval = strtotime($data['endtime']) - strtotime($data['starttime']);

			$data['share_title']=$_GPC['share_title']?$_GPC['share_title']:$data['title'];
			$data['share_icon'] = $_GPC['share_icon']?$_GPC['share_icon']:$data['image'];
			$data['description'] = $_GPC['description']?$_GPC['description']:$data['subtitle'];

			if (0 < ($interval - (86400 * 30))) {
			}


		
			$data['picktype'] = intval($_GPC['picktype']);

			if ($data['picktype'] != '1') {
				$data['stop_type'] = intval($_GPC['stoptype']);
			
				$data['repeat_type'] = intval($_GPC['repeattype']);
			}

			if (is_array($_GPC['thumbs'])) 
			{
				$thumbs = $_GPC['thumbs'];
				$thumb_url = array();
				foreach ($thumbs as $th ) 
				{
					$thumb_url[] = trim($th);
				}
				
				$data['thumb_url'] = serialize(m('common')->array_images($thumb_url));
			}

			$data['projectdetail'] = $_GPC['projectdetail'];
			$data['shopinpolicy']  = $_GPC['shopinpolicy'];
			$data['shopintroduce'] = $_GPC['shopintroduce'];
			$data['qrcode'] = $_GPC['qrcode'];
			

			if ($id) {
				pdo_update('ewei_shop_task2_list',$data,array('id' => $id));
				$saveRet = $id;
			}else{
				pdo_insert('ewei_shop_task2_list',$data);
				$saveRet = pdo_insertid();	

			}

			empty($saveRet) && show_json(0, '数据保存失败，请检查');
			show_json(1, array('message' => '保存成功', 'url' => webUrl('task2.post', array('id' => $saveRet))));
		}
		 else {
			
			$task2=$task;
		 	$piclist= unserialize($task[thumb_url]);
			if (empty($task2)) {
				$task['starttime'] = date('Y-m-d H:i:s');
				$task['endtime'] = date('Y-m-d H:i:s', time() + (86400 * 7));
			}


	
			include $this->template('task2/new/post');
		}
	}

	/**
     * 预览任务详情
     */
	public function preview()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		$sql = 'select rc.*,rw.* from ' . tablename('ewei_shop_task_record') . ' rc left join ' . tablename('ewei_shop_task_reward') . ' rw on rc.id = rw.recordid where rc.id = ' . $id . ' and rc.uniacid = ' . $_W['uniacid'];
		$data = pdo_fetchall($sql);
		include $this->template('task/new/preview');
	}

	/**
     * 删除任务
     */
	public function delete()
	{
		global $_W;
		global $_GPC;
		$ids = $_GPC['ids'];
		$this->model->deleteTask($ids);
		show_json(1);
	}

	

	/**
     * 排序设置
     */
	public function setdisplayorder()
	{
		global $_W;
		global $_GPC;
		$id = (int) $_GPC['id'];
		$value = (int) $_GPC['value'];
		pdo_update('ewei_shop_task2_list', array('displayorder' => $value), array('id' => $id, 'uniacid' => $_W['uniacid']));
		show_json(1);
	}

}


?>