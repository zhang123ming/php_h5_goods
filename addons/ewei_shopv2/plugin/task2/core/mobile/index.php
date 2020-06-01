<?php
//
?>
<?php
if (!(defined('IN_IA'))) {
	exit('Access Denied');
}

class index_EweiShopV2Page extends PluginMobilePage
{
	private $new = false;

	public function __construct()
	{
		parent::__construct();
	}

	
	public function newtask()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$res = $this->model->getNewTask($id);

		if (is_string($res)) {
			show_json(0, $res);
		}


		show_json(1, $res);
	}

	/**
     * 任务中心首页
     */
	public function main()
	{
		global $_W,$_GPC;
		$my = m('member')->getInfo($_W['openid']);
		$info = m('member')->getInfo($_W['openid']);

		$condition = ' and `type` like \'%shop%\' ';
	
		if ($_GPC['keywords']) {
			
			$condition .= "and `title` like '%{$_GPC[keywords]}%' ";
		}
		
		if (!$_GPC['type']||$_GPC['type']=='multiple') {
			$orderby = 'displayorder desc,id desc ';
		}elseif ($_GPC['type']=='time') {
			
			$orderby = ' id DESC';
		}

		$field = '*';
		$sql = 'select ' . $field . ' from ' . tablename('ewei_shop_task2_list') . ' where uniacid = :uniacid ' . $condition . '  order by '.$orderby;
		$params = array(':uniacid' => $_W['uniacid']);
		$list = pdo_fetchall($sql, $params);

		$set = pdo_fetchcolumn('select bg_img from ' . tablename('ewei_shop_task_set') . ' where uniacid = ' . $_W['uniacid']);
		include $this->template('task2/index');
	}

	/*我的中心*/
	public function mine()
	{

		global $_W,$_GPC;
		$my = m('member')->getInfo($_W['openid']);
		$info = m('member')->getInfo($_W['openid']);

		$type=$_GPC['type']?$_GPC['type']:'favorite';
		$params=array(":uniacid"=>$_W['uniacid']);

		if ($type=='favorite') {
			$sql="SELECT * FROM ".tablename('ewei_shop_task2_favorite')."  where uniacid=:uniacid and openid='".$_W[openid]."'";
		}else{

			$sql="SELECT * FROM ".tablename('ewei_shop_task2_record')." where uniacid=:uniacid and openid='".$_W[openid]."'";
		}

		$list = pdo_fetchall($sql,$params);

		if ($type=='favorite') {
			foreach ($list as $key=> $value) {
				$sql="SELECT * FROM ".tablename('ewei_shop_task2_list')." WHERE uniacid=:uniacid and id=".$value[taskid];
				$item=pdo_fetch($sql,$params);
				$value['tasktitle']=$item['title'];
				$value['tasksubtitle']=$item['subtitle'];
				$value['taskimage']=$item['image'];
				$list[$key]=$value;
			}
		}
		include $this->template('task2/mine');

	}


	public function comment()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$openid = $_W['openid'];

		$member = m('member')->getMember($openid);

		if ($member['level']==0) {
			$this->message("请先升级,才可评价");
		}
		$taskid = intval($_GPC['taskid']);

		$task = pdo_fetch("select * from " . tablename('ewei_shop_task2_list') . " where id={$taskid} and uniacid=:uniacid  limit 1", array(':uniacid' => $uniacid));
		if (empty($task)) {
			header('location: ' . mobileUrl('task2'));
			exit();
		}

		if ($_W['ispost']) {
			$comments = $_GPC['comments'];

			foreach ($comments as $key => $c) {
				$old_comm = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_task2_comment') . ' where uniacid=:uniacid and taskid=:taskid limit 1', array(':uniacid' => $_W['uniacid'], ':taskid' => $taskid));

				if (empty($old_comm)) {
					$comment = array('uniacid' => $uniacid, 'taskid' => $taskid,  'content' => trim($c['content']), 'images' => is_array($c['images']) ? iserializer($c['images']) : iserializer(array()), 'openid' => $openid, 'nickname' => $member['nickname'], 'headimgurl' => $member['avatar'], 'createtime' => time(), 'checked' => $checked);
					pdo_insert('ewei_shop_task2_comment', $comment);

				}else{

					$comment = array('append_content' => trim($c['content']), 'append_images' => is_array($c['images']) ? iserializer($c['images']) : iserializer(array()), 'replychecked' => $checked);
					pdo_update('ewei_shop_task2_comment', $comment, array('uniacid' => $_W['uniacid'], 'taskid' => $taskid));

				}
			}
			show_json(1);
		}

		if (2 <= $mytask['iscomment']) {
			$this->message('您已经评价过了!', mobileUrl('task2/detail', array('id' => $taskid)));
		}

		include $this->template('task2/comment');
	}

	
	public function saveuniversal()
	{
		global $_W,$_GPC;
		$taskid = $_GPC['taskid'];
		$universaldata = serialize( [
			'reportmobile'=>$_GPC['reportmobile'],
			'reportname'=>$_GPC['reportname']
		]);

		$sql = "SELECT * FROM ".tablename('ewei_shop_task2_universaldata')." where uniacid=:uniacid and taskid=".$taskid ." and openid='".$_W[openid]."'";

		$params = array(':uniacid' =>$_W['uniacid']);
		$item = pdo_fetch($sql,$params);

		if ($item) {
			show_json(0, '请勿重复报名');
		}

		$data= array('uniacid' =>$_W['uniacid'] ,'openid'=>$_W['openid'],'universaldata'=>$universaldata,'taskid'=>$taskid);
		pdo_insert('ewei_shop_task2_universaldata',$data);
		$res = pdo_insertid();
		if ($res) {
		 	show_json(1,'登记成功');
		 }else{

		 	show_json(0,'登记失败');
		 }

	}

	/**
     * 任务详情
     */
	public function detail()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		$rid = intval($_GPC['rid']);

		$showQrcode = $_GPC['showQrcode']?true:false;


		$sql="select * from ".tablename('ewei_shop_task2_comment')." where uniacid=:uniacid and taskid=:taskid order  by id desc LIMIT 50";
		$params= array(':uniacid' =>$_W['uniacid'] ,':taskid'=>$id );

		$commentlist = pdo_fetchall($sql,$params);

		foreach ($commentlist as &$row) {
			$row['headimgurl'] = tomedia($row['headimgurl']);
			$row['createtime'] = date('Y-m-d H:i', $row['createtime']);
			$row['images'] = set_medias(iunserializer($row['images']));
			$row['reply_images'] = set_medias(iunserializer($row['reply_images']));
			$row['append_images'] = set_medias(iunserializer($row['append_images']));
			$row['append_reply_images'] = set_medias(iunserializer($row['append_reply_images']));
			$row['nickname'] = cut_str($row['nickname'], 1, 0) . '**' . cut_str($row['nickname'], 1, -1);
		}

		unset($row);
	
		$member = m('member')->getInfo($_W['openid']);
		if (!$member) {
			$this->message('账户不存在');
		}

		$set = pdo_fetch('select * from ' . tablename('ewei_shop_task2_set') . ' where uniacid = ' . $_W['uniacid']);
	
		if (!(empty($id))) {

			$sql = 'select * from ' . tablename('ewei_shop_task2_list') . ' where id = :id and uniacid = :uniacid';
			$detail = pdo_fetch($sql, array(':id' => $id, ':uniacid' => $_W['uniacid']));

			$isFavorite = $this->isFavorite($id);


			if ($detail) {
				$err = '';
				$thumbs =  unserialize($detail['thumb_url']);
				$detail['shopintroduce']=htmlspecialchars_decode($detail['shopintroduce']);
				$detail['projectdetail']=htmlspecialchars_decode($detail['projectdetail']);
				$detail['shopinpolicy']=htmlspecialchars_decode($detail['shopinpolicy']);
			}
				
		}


		if (empty($detail)) {
			$this->message('任务不存在');
		}

		$_W['shopshare'] = array('title' => $detail['share_title'], 'imgUrl' => tomedia($detail['share_icon']) , 'desc' => $detail['description']?$detail['description']:$_W['shopset']['shop']['name']);

		$com = p('commission');

		$backurl=base64_encode(mobileUrl('task2.detail',array('id'=>$detail['id'])));

		if ($com) {
			$cset = $_W['shopset']['commission'];

			if (!empty($cset)) {
				if (($member['isagent'] == 1) && ($member['status'] == 1)) {
					$_W['shopshare']['link'] = mobileUrl('task2/detail', array('id' => $detail['id'], 'mid' => $member['id']), true);
				}
				else {
					if (!empty($_GPC['mid'])) {
						$_W['shopshare']['link'] = mobileUrl('task2/detail', array('id' => $detail['id'], 'mid' => $_GPC['mid']), true);
					}
				}
			}
		}


		include $this->template('task2/detail_new');
	}

	public function isFavorite($id = '')
	{
		global $_W;
		$count = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_task2_favorite') . ' where taskid=:taskid and deleted=0 and openid=:openid and uniacid=:uniacid limit 1', array(':taskid' => $id, ':openid' => $_W['openid'], ':uniacid' => $_W['uniacid']));
		return 0 < $count;
	}


	/**
     * 接任务
     */
	public function picktask()
	{
		global $_W;
		global $_GPC;

		if ($_W['ispost']) {
			$openid = $_W['openid'];
			$taskid = intval($_GPC['id']);
			empty($taskid) && show_json(0, '任务不存在');
			$ret = $this->model->pickTask($taskid, $openid);

			if (is_error($ret)) {
				show_json(0, $ret['message']);
			}


			logg('id', $ret);
			show_json(1, $ret);
		}

	}

	public function toggle()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		$isfavorite = intval($_GPC['isfavorite']);

		$goods = pdo_fetch('select * from ' . tablename('ewei_shop_task2_list') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $id, ':uniacid' => $_W['uniacid']));

		if (empty($goods)) {
			show_json(0, '项目未找到');
		}

		$data = pdo_fetch("select id,deleted from"  . tablename('ewei_shop_task2_favorite') .  " where uniacid=:uniacid and taskid='{$id}' and openid=:openid limit 1", array(':uniacid' => $_W['uniacid'], ':openid' => $_W['openid']));

		if (empty($data)) {
			if (!empty($isfavorite)) {
				$data = array('uniacid' => $_W['uniacid'], 'taskid' => $id, 'openid' => $_W['openid'], 'createtime' => time());
				pdo_insert('ewei_shop_task2_favorite', $data);
			}
		}
		else {

			pdo_update('ewei_shop_task2_favorite', array('deleted' => $isfavorite ? 0 : 1), array('id' => $data['id'], 'uniacid' => $_W['uniacid']));
		}

		show_json(1, array('isfavorite' => $isfavorite == 1));
	}

	public function upgrade()
	{
		global $_W;
		global $_GPC;

		$set = pdo_fetch('select * from ' . tablename('ewei_shop_task2_set') . ' where uniacid = ' . $_W['uniacid']);

		$cate=$set['upgrade_cate'];
		$order="minprice";
		$by  = "DESC";

		$args = array(
			'cate' =>$cate,
			'order' =>$order, 
			'by' => $by
		);

		$goods = m('goods')->getList($args);

		$goodsList=$goods['list'];

		include $this->template('task2/upgrade');
	}

}


?>