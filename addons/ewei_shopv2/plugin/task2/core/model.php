<?php
//
?>
<?php
if (!(defined('IN_IA'))) {
	exit('Access Denied');
}


set_time_limit(0);
class Task2Model extends PluginModel
{

	/**
     * 数据库注册字段
     * @var array
     */
	public $ewei_shop_task2_list = array('id', 'uniacid', 'displayorder', 'title', 'image', 'type', 'status', 'picktype', 'starttime', 'endtime', 'stop_type', 'stop_limit', 'stop_time', 'stop_cycle', 'repeat_type', 'repeat_interval', 'repeat_cycle', 'demand', 'reward', 'followreward', 'design_data', 'design_bg', 'goods_limit', 'notice', 'requiregoods', 'native_data', 'native_data2', 'native_data3', 'reward3', 'reward2', 'level2', 'level3');
	public $ewei_shop_task2_set = array('uniacid', 'entrance', 'keyword', 'upgrade_link','upgrade_cate','upgrade_intro','qrcode_desc','cover_title', 'cover_img', 'cover_desc', 'msg_pick', 'msg_progress', 'msg_finish', 'msg_follow', 'isnew', 'bg_img');
	public $ewei_shop_task2_record = array('id', 'uniacid', 'taskid', 'tasktitle', 'tasktype', 'task_demand', 'openid', 'nickname', 'picktime', 'stoptime', 'finishtime', 'task_progress', 'reward_data', 'followreward_data', 'taskimage', 'design_data', 'design_bg', 'require_goods', 'level1', 'level2', 'reward_data1', 'reward_data2');

	public function isnew()
	{
		global $_W;
		$uniacid = pdo_fetchcolumn('select uniacid from ' . tablename('ewei_shop_task2_set') . ' where uniacid = ' . $_W['uniacid']);

		if (empty($uniacid)) {
			pdo_insert('ewei_shop_tas2k_set', array('uniacid' => $_W['uniacid']));
		}


		return pdo_fetchcolumn('select isnew from ' . tablename('ewei_shop_task2_set') . ' where uniacid = ' . $_W['uniacid']);
	}


	public function uploadImage($img)
	{
		load()->func('communication');
		$account = m('common')->getAccount();
		$access_token = $account->fetch_token();
		$url = 'http://file.api.weixin.qq.com/cgi-bin/media/upload?access_token=' . $access_token . '&type=image';
		$ch1 = curl_init();
		$data = array('media' => '@' . $img);

		if (version_compare(PHP_VERSION, '5.5.0', '>')) {
			$data = array('media' => curl_file_create($img));
		}


		curl_setopt($ch1, CURLOPT_URL, $url);
		curl_setopt($ch1, CURLOPT_POST, 1);
		curl_setopt($ch1, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch1, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch1, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch1, CURLOPT_POSTFIELDS, $data);
		$content = @json_decode(curl_exec($ch1), true);

		if (!(is_array($content))) {
			$content = array('media_id' => '');
		}


		curl_close($ch1);
		return $content['media_id'];
	}



	public function checkMember($openid = '', $acc = '')
	{
		global $_W;
		$redis = redis();

		if (empty($acc)) {
			$acc = WeiXinAccount::create();
		}


		$userinfo = $acc->fansQueryInfo($openid);
		$userinfo['avatar'] = $userinfo['headimgurl'];
		load()->model('mc');
		$uid = mc_openid2uid($openid);

		if (!(empty($uid))) {
			pdo_update('mc_members', array('nickname' => $userinfo['nickname'], 'gender' => $userinfo['sex'], 'nationality' => $userinfo['country'], 'resideprovince' => $userinfo['province'], 'residecity' => $userinfo['city'], 'avatar' => $userinfo['headimgurl']), array('uid' => $uid));
		}


		pdo_update('mc_mapping_fans', array('nickname' => $userinfo['nickname']), array('uniacid' => $_W['uniacid'], 'openid' => $openid));
		$model = m('member');
		$member = $model->getMember($openid);

		if (empty($member)) {
			if (!(is_error($redis))) {
				$member = $redis->get($openid . '_task_checkMember');

				if (!(empty($member))) {
					return json_decode($member, true);
				}

			}


			$mc = mc_fetch($uid, array('realname', 'nickname', 'mobile', 'avatar', 'resideprovince', 'residecity', 'residedist'));
			$member = array('uniacid' => $_W['uniacid'], 'uid' => $uid, 'openid' => $openid, 'realname' => $mc['realname'], 'mobile' => $mc['mobile'], 'nickname' => (!(empty($mc['nickname'])) ? $mc['nickname'] : $userinfo['nickname']), 'avatar' => (!(empty($mc['avatar'])) ? $mc['avatar'] : $userinfo['avatar']), 'gender' => (!(empty($mc['gender'])) ? $mc['gender'] : $userinfo['sex']), 'province' => (!(empty($mc['resideprovince'])) ? $mc['resideprovince'] : $userinfo['province']), 'city' => (!(empty($mc['residecity'])) ? $mc['residecity'] : $userinfo['city']), 'area' => $mc['residedist'], 'createtime' => time(), 'status' => 0);
			pdo_insert('ewei_shop_member', $member);
			$member['id'] = pdo_insertid();
			$member['isnew'] = true;

			if (!(is_error($redis))) {
				$redis->set($openid . '_task_checkMember', json_encode($member), 20);
			}

		}
		 else {
			$member['nickname'] = $userinfo['nickname'];
			$member['avatar'] = $userinfo['headimgurl'];
			$member['province'] = $userinfo['province'];
			$member['city'] = $userinfo['city'];
			pdo_update('ewei_shop_member', $member, array('id' => $member['id']));
			$member['isnew'] = false;
		}

		return $member;
	}


	public function responseUnsubscribe($param = '')
	{
		global $_W;

		if (isset($param['openid']) && !(empty($param['openid']))) {
			$openid = $param['openid'];
			$where = array('uniacid' => $_W['uniacid'], 'joiner_id' => $openid);
			$task_info = pdo_fetch('SELECT join_user FROM ' . tablename('ewei_shop_task_join') . 'WHERE failtime>' . time() . ' and is_reward=0 and join_id in (SELECT join_id from ' . tablename('ewei_shop_task_joiner') . ' where uniacid=:uniacid and joiner_id=:joiner_id and join_status=1)', array(':uniacid' => $_W['uniacid'], ':joiner_id' => $openid));

			if ($task_info) {
				$member = $this->checkMember($openid);
				pdo_update('ewei_shop_task_joiner', array('join_status' => 0), $where);
				$updatesql = 'UPDATE ' . tablename('ewei_shop_task_join') . ' SET completecount = completecount-1 WHERE failtime>' . time() . ' and is_reward=0 and join_id in (SELECT join_id from ' . tablename('ewei_shop_task_joiner') . ' where uniacid=:uniacid and joiner_id=:joiner_id and join_status=1)';
				pdo_query($updatesql, array(':uniacid' => $_W['uniacid'], ':joiner_id' => $openid));

				foreach ($task_info as $val ) {
					m('message')->sendCustomNotice($val['join_user'], '您推荐的用户[' . $member['nickname'] . ']取消了关注，您失去了一个小伙伴');
				}
			}

		}

	}

	public function notice_complain($templete, $member, $poster, $scaner = '', $type = 1)
	{
		global $_W;
		$reward_type = 'sub';
		$openid = $scaner['openid'];

		if ($type == 2) {
			$reward_type = 'rec';
			$openid = $member['openid'];
		}
		if ($templete) {
			$templete = trim($templete);
			$templete = str_replace('[任务执行者昵称]', $member['nickname'], $templete);
			$templete = str_replace('[任务名称]', $poster['title'], $templete);

			if ($poster['poster_type'] == 1) {
				$templete = str_replace('[任务目标]', $poster['needcount'], $templete);
			}
			 else if ($poster['poster_type'] == 2) {
				$reward_data = unserialize($poster['reward_data']);
				$reward_data = array_shift($reward_data['rec']);
				$templete = str_replace('[任务目标]', $reward_data['needcount'], $templete);
			}


			$templete = str_replace('[任务领取时间]', date('Y年m月d日 H:i', $poster['timestart']) . '-' . date('Y年m月d日 H:i', $poster['timeend']), $templete);

			if (!(empty($scaner))) {
				$templete = str_replace('[海报扫描者昵称]', $scaner['nickname'], $templete);
			}


			if ($poster['reward_data']) {
				$poster['reward_data'] = unserialize($poster['reward_data']);
				$templete = str_replace('[余额奖励]', $poster['reward_data'][$reward_type]['money']['num'], $templete);

				if (isset($poster['reward_data'][$reward_type]['coupon']['total'])) {
					$templete = str_replace('[奖励优惠券]', $poster['reward_data'][$reward_type]['coupon']['total'], $templete);
				}
				 else {
					$templete = str_replace('[奖励优惠券]', '', $templete);
				}

				$templete = str_replace('[积分奖励]', $poster['reward_data'][$reward_type]['credit'], $templete);
				$reward_text = '';

				foreach ($poster['reward_data'][$reward_type] as $key => $val ) {
					if ($key == 'credit') {
						$reward_text .= '积分' . $val . ' |';
					}


					if ($key == 'goods') {
						$reward_text .= '指定商品' . count($val) . '件';
					}


					if ($key == 'money') {
						$reward_text .= '余额' . $val['num'] . '元 |';
					}


					if ($key == 'coupon') {
						$reward_text .= '优惠券' . $val['total'] . '张 |';
					}


					if ($key == 'bribery') {
						$reward_text .= '红包' . $val . '元 |';
					}

				}

				$templete = str_replace('[关注奖励列表]', $reward_text, $templete);
			}
			 else {
				$templete = str_replace('[余额奖励]', '0', $templete);
				$templete = str_replace('[奖励优惠券]', '0', $templete);
				$templete = str_replace('[积分奖励]', '0', $templete);
			}

			if (isset($poster['completecount'])) {
				$notcomplete = intval($poster['needcount'] - $poster['completecount']);

				if ($notcomplete <= 0) {
					$notcomplete = 0;
				}


				$templete = str_replace('[还需完成数量]', $notcomplete, $templete);
				$templete = str_replace('[完成数量]', intval($poster['completecount']), $templete);
			}


			if (isset($poster['okdays'])) {
				$templete = str_replace('[海报有效期]', date('Y年m月d日 H:i', $poster['okdays']), $templete);
			}


			$db_data = pdo_fetchcolumn('select `data` from ' . tablename('ewei_shop_task_default') . ' where uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid']));
			$res = '';

			if (!(empty($db_data))) {
				$res = unserialize($db_data);
			}


			$rankinfo = array();
			$rankinfoone = array(1 => $res['taskranktitle'] . '1', 2 => $res['taskranktitle'] . '2', 3 => $res['taskranktitle'] . '3', 4 => $res['taskranktitle'] . '4', 5 => $res['taskranktitle'] . '5');
			$rankinfotwo = array(1 => $res['taskranktitle'] . 'Ⅰ', 2 => $res['taskranktitle'] . 'Ⅱ', 3 => $res['taskranktitle'] . 'Ⅲ', 4 => $res['taskranktitle'] . 'Ⅳ', 5 => $res['taskranktitle'] . 'Ⅴ');
			$rankinfothree = array(1 => $res['taskranktitle'] . 'A', 2 => $res['taskranktitle'] . 'B', 3 => $res['taskranktitle'] . 'C', 4 => $res['taskranktitle'] . 'D', 5 => $res['taskranktitle'] . 'E');

			if ($res['taskranktype'] == 1) {
				$rankinfo = $rankinfoone;
			}
			 else if ($res['taskranktype'] == 2) {
				$rankinfo = $rankinfotwo;
			}
			 else if ($res['taskranktype'] == 3) {
				$rankinfo = $rankinfothree;
			}
			 else {
				$rankinfo = $rankinfoone;
			}

			if (isset($poster['reward_rank']) && !(empty($poster['reward_rank']))) {
				$templete = str_replace('[任务阶段]', $rankinfo[$poster['reward_rank']], $templete);
			}


			return trim($templete);
		}


		return '';
	}

	public function rec_notice_complain($poster)
	{
		if ($poster['reward_data']) {
			$poster['reward_data'] = unserialize($poster['reward_data']);
			$reward_text = '';

			foreach ($poster['reward_data'] as $key => $val ) {
				if ($key == 'credit') {
					$reward_text .= '积分:' . $val;
				}


				if ($key == 'goods') {
					$reward_text .= '商品:' . count($val) . '个';
				}


				if ($key == 'money') {
					$reward_text .= '奖金:' . $val['num'] . '元';
				}


				if ($key == 'coupon') {
					$reward_text .= '优惠券:' . $val['total'] . '张';
				}


				if ($key == 'bribery') {
					$reward_text .= '红包:' . $val . '元';
				}

			}

			return trim($reward_text);
		}


		return '';
	}

	
	

	public function getRecordsList($page, $taskid)
	{
		global $_W;
		$psize = 20;
		$pstart = ($page - 1) * $psize;
		$sql = 'SELECT * FROM ' . tablename('ewei_shop_task_log') . ' WHERE taskid = :taskid AND uniacid = :uniacid ORDER BY id DESC LIMIT ' . $pstart . ',' . $psize;
		return pdo_fetch($sql, array(':taskid' => $taskid, ':uniacid' => $_W['uniacid']));
	}


	public function __construct($name = '')
	{
		parent::__construct($name);
		$this->taskType = pdo_getall('ewei_shop_task_type');
	}

	/**
     * 得到task_type详情
     * @param string $type
     * @return array|mixed
     */
	public function getTaskType($type = '')
	{
		if (empty($type)) {
			return $this->taskType;
		}


		foreach ($this->taskType as $tasktype ) {
			while ($tasktype['type_key'] == $type) {
				return $tasktype;
			}
		}

		return false;
	}

	/**
     * web 分页查询所有任务
     * @param $page
     * @param int $psize
     * @return array|boolean
     */
	public function getAllTask(&$page, $psize = 15)
	{
		global $_W;
		global $_GPC;
		$page = max(1, $page);
		$pstart = ($page - 1) * $psize;
		$params = array(':uniacid' => $_W['uniacid']);
		$type = trim($_GPC['type']);
		$keyword = trim($_GPC['keyword']);
		$condition = '';
		if (!(empty($type)) || !(empty($keyword))) {
			$condition = ' and `title` like \'%' . $keyword . '%\' and `type` like \'%' . $type . '%\' ';
		}


		$field = '*';
		$sql = 'select ' . $field . ' from ' . tablename('ewei_shop_task_list') . ' where uniacid = :uniacid ' . $condition . '  order by displayorder desc,id desc limit ' . $pstart . ' , ' . $psize;
		$return = pdo_fetchall($sql, $params);
		$countsql = substr($sql, 0, strpos($sql, 'order by'));
		$countsql = str_replace($field, 'count(*)', $countsql);
		$count = pdo_fetchcolumn($countsql, $params);
		$page = pagination2($count, $page, $psize);
		return $return;
	}

	/**
     * task插件公用save方法,添加/编辑任务表
     * @param $table
     * @param $task
     * @param bool $Update 是否支持更新
     * @return bool
     */
	public function taskSave($table, $task, $Update = true)
	{
		global $_W;
		$this->checkDbFormat($table, $task);

		if (!(is_array($task))) {
			return false;
		}


		$task['uniacid'] = $_W['uniacid'];
		$isIdKey = in_array('id', $this->$table);

		if (empty($task['id']) && $isIdKey) {
			pdo_insert($table, $task);
			return pdo_insertid();
		}


		if (!($isIdKey)) {
			$countSql = 'select count(*) from ' . tablename($table) . ' where uniacid = :uniacid';
			$ifExist = pdo_fetchcolumn($countSql, array('uniacid' => $_W['uniacid']));
			if ($ifExist && $Update) {

				pdo_update($table, $task, array('uniacid' => $_W['uniacid']));
				if (empty($task['id'])) {
					return $_W['uniacid'];
				}


				return $task['id'];
			}


			if (!($ifExist)) {
				pdo_insert($table, $task);
				return pdo_insertid();
			}


			return false;
		}
		if ($Update) {
			pdo_update($table, $task, array('id' => $task['id']));

			if (empty($task['id'])) {
				return $_W['uniacid'];
			}


			return $task['id'];
		}


		return false;
	}

	/**
     * 校验数据库字段
     * @param $task
     */
	protected function checkDbFormat($table, &$task)
	{
		if (!(is_array($task)) || !(is_array($this->$table))) {
			return $task = false;
		}


		$field = array_flip($this->$table);
		$diff = array_diff_key($task, $field);

		if (!(empty($diff))) {
			return $task = false;
		}


		foreach ($task as &$t ) {
			if (is_array($t)) {
				$t = json_encode($t);
			}

		}

		return true;
	}

	/**
     * 删除任务
     * @param $ids
     * @return bool
     */
	public function deleteTask($ids)
	{
		$isArr = is_array($ids);
		$isNum = is_numeric($ids);

		if ($isNum) {
			$ids = array($ids);
			$isArr = true;
		}
		if ($isArr) {
			$condition = ' id = \'' . implode(' \' or id = \'', $ids) . '\'';
			return pdo_query('delete from ' . tablename('ewei_shop_task2_list') . ' where ' . $condition);
		}


		if (!($isArr) && !($isNum)) {
			return false;
		}

	}

	/**
     * 得到任务By id
     * @param $id
     * @return bool
     */
	public function getThisTask($id)
	{
		global $_W;

		if (empty($id)) {
			return false;
		}


		return pdo_get('ewei_shop_task2_list', array('id' => $id, 'uniacid' => $_W['uniacid']));
	}

	/**
     * 分页获取任务记录
     * @param $page
     * @param int $psize
     * @return array
     */
	public function getAllRecords(&$page, $psize = 20)
	{
		global $_W;
		global $_GPC;
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
		$return = pdo_fetchall($sql, $params);
		$countsql = substr($sql, 0, strpos($sql, 'order by'));
		$countsql = str_replace($field, 'count(*)', $countsql);
		$count = pdo_fetchcolumn($countsql, $params);
		$page = pagination2($count, $page, $psize);
		return $return;
	}

	/**
     * 分页获取奖励记录
     * @param $page
     * @param int $psize
     * @return array
     */
	public function getAllRewards(&$page, $psize = 20)
	{
		global $_W;
		global $_GPC;
		$page = max(1, $page);
		$pstart = ($page - 1) * $psize;
		$params = array(':uniacid' => $_W['uniacid']);
		$condition = '';
		$keyword = trim($_GPC['keyword']);
		!(empty($keyword)) && ($condition .= ' and tasktitle like \'%' . $keyword . '%\' ');
		$starttime = $_GPC['time']['start'];
		!(empty($starttime)) && ($condition .= ' and gettime > \'' . $starttime . '\' ');
		$endtime = $_GPC['time']['end'];
		!(empty($endtime)) && ($condition .= ' and gettime < \'' . $endtime . '\' ');
		$field = '*';
		$sql = 'select ' . $field . ' from ' . tablename('ewei_shop_task_reward') . ' where uniacid = :uniacid ' . $condition . ' and `get` = 1 order by id desc limit ' . $pstart . ' , ' . $psize;
		$return = pdo_fetchall($sql, $params);
		$countsql = substr($sql, 0, strpos($sql, 'order by'));
		$countsql = str_replace($field, 'count(*)', $countsql);
		$count = pdo_fetchcolumn($countsql, $params);
		$page = pagination2($count, $page, $psize);
		return $return;
	}

	/**
     * 分页获取商品
     * @param string $keyword
     * @param int $page
     */
	public function getGoods_new($keyword = '', $page = 1)
	{
		global $_W;
		$psize = 10;
		$pstart = ($page - 1) * $psize;
		$field = 'id,title,thumb,marketprice,total';
		$like = (($keyword === '' ? $keyword : ' and title like %' . $keyword . '%'));
		$sql = 'select ' . $field . ' from ' . tablename('ewei_shop_goods') . 'where uniacid = :uniacid and status = 1 and deleted = 0' . $like . ' limit ' . $pstart . ' , ' . $psize;
		$countsql = substr($sql, 0, strpos($sql, 'limit'));
		$countsql = str_replace($field, 'count(*)', $countsql);
		$params = array(':uniacid' => $_W['uniacid']);
		$count = pdo_fetchcolumn($countsql, $params);
		$list = pdo_fetchall($sql, $params);
		show_json($count, $list);
	}

	/**
     * 分页获取优惠券
     * @param string $keyword
     * @param int $page
     */
	public function getCoupon($keyword = '', $page = 1)
	{
		global $_W;
		$psize = 10;
		$pstart = ($page - 1) * $psize;
		$field = 'id,couponname';
		$like = (($keyword === '' ? $keyword : ' and title like %' . $keyword . '%'));
		$sql = 'select ' . $field . ' from ' . tablename('ewei_shop_coupon') . 'where uniacid = :uniacid ' . $like . ' limit ' . $pstart . ' , ' . $psize;
		$countsql = substr($sql, 0, strpos($sql, 'limit'));
		$countsql = str_replace($field, 'count(*)', $countsql);
		$params = array(':uniacid' => $_W['uniacid']);
		$count = pdo_fetchcolumn($countsql, $params);
		$list = pdo_fetchall($sql, $params);
		show_json($count, $list);
	}

	protected function stoptime($task)
	{
		global $_W;
		$time = time();
		$stoptime = '0000-00-00 00:00:00';

		if ($task['picktype'] == 1) {
			return $task['endtime'];
		}


		if ($task['stop_type'] == 1) {
			$stoptime = date('Y-m-d H:i:s', $time + $task['stop_limit']);
		}
		 else if ($task['stop_type'] == 2) {
			$stoptime = $task['stop_time'];
		}
		 else if ($task['stop_type'] == 3) {
			switch ($task['stop_cycle']) {
			case '0':
				$stoptime = date('Y-m-d 00:00:00', strtotime('+1 day'));
				break;

			case '1':
				$stoptime = date('Y-m-d 00:00:00', strtotime('next Monday'));
				break;

			case '2':
				$stoptime = date('Y-m-d 00:00:00', mktime(0, 0, 0, date('n') + 1, 1, date('Y')));
				break;
			}
		}


		return $stoptime;
	}

	/**
     * 接任务
     * @param $listid
     * @param $openid
     * @return int
     */
	public function pickTask($taskid, $openid)
	{
		global $_W;
		empty($openid) && ($openid = $_W['openid']);
		$task = $this->getThisTask($taskid);
		$info = m('member')->getInfo($openid);

		if (empty($task)) {
			return error(-1, '任务不存在');
		}


		$canPick = $this->checkCanPick($task, $openid);

		if (is_error($canPick)) {
			return $canPick;
		}


		$stoptime = $this->stoptime($task);
		$taskArr = array('uniacid' => $_W['uniacid'], 'taskid' => $task['id'], 'tasktitle' => $task['title'], 'tasktype' => $task['type'], 'openid' => $openid, 'nickname' => $info['nickname'], 'picktime' => date('Y-m-d H:i:s'),  'taskimage' => $task['image'],  'stoptime' => $stoptime);

		if (($task['type'] == 'poster') && (0 < $task['level2'])) {
			$taskArr['level1'] = $task['demand'];
			$taskArr['reward_data1'] = $task['reward'];
			$taskArr['level2'] = $task['level2'];
			$taskArr['reward_data2'] = $task['reward2'];

			if (0 < $task['level3']) {
				$taskArr['reward_data'] = $task['reward3'];
			}
			 else {
				$taskArr['reward_data'] = $task['reward2'];
			}
		}


		$table = 'ewei_shop_task2_record';
		 pdo_insert($table, $taskArr);
		 $recordid =pdo_insertid();
		if (!($recordid)) {
			return error(1, '任务接取失败了');
		}


		return $recordid;
	}


	/**
     * 检查是否可以接取任务
     * @param $task
     * @param $openid
     * @return bool
     */
	protected function checkCanPick($task, $openid)
	{
		global $_W;

		if (empty($openid)) {
			$openid = $_W['openid'];
		}


		if (empty($openid)) {
			return error(1, '请先登录');
		}


		$sql = 'select * from ' . tablename('ewei_shop_task2_record') . ' where openid = :openid and taskid = :taskid and uniacid = :uniacid order by id desc';
		$lastRecord = pdo_fetch($sql, array(':openid' => $openid, ':taskid' => $task['id'], ':uniacid' => $_W['uniacid']));

		if (empty($lastRecord)) {
			return true;
		}


		if ($task['repeat_type'] == 1) {
			return error(-1, '不能重复接此任务');
		}

		return true;
	}

	/**
     * 两天相差的天数
     * @param $day1
     * @param $day2
     * @return float|int
     */
	protected function diffBetweenTwoDays($day1, $day2)
	{
		$second1 = strtotime(date('Y-m-d', $day1));
		$second2 = strtotime(date('Y-m-d', $day2));

		if ($second1 < $second2) {
			$tmp = $second2;
			$second2 = $second1;
			$second1 = $tmp;
		}


		return ($second1 - $second2) / 86400;
	}

	protected function getRealData2($data)
	{
		$data['left'] = intval(str_replace('px', '', $data['left'])) * 2;
		$data['top'] = intval(str_replace('px', '', $data['top'])) * 2;
		$data['width'] = intval(str_replace('px', '', $data['width'])) * 2;
		$data['height'] = intval(str_replace('px', '', $data['height'])) * 2;
		$data['size'] = intval(str_replace('px', '', $data['size'])) * 2;
		$data['src'] = tomedia($data['src']);
		return $data;
	}

	protected function getQR2($poster, $member)
	{
		global $_W;
		$time = time();
		$expire = strtotime($poster['stoptime']) - $time;

		if (((86400 * 30) - 15) < $expire) {
			$scene_id = 't' . time() . rand(100000, 999999);
		}


		$qr = pdo_fetch('select * from ' . tablename('ewei_shop_task_qr') . ' where openid = :openid and uniacid = :uniacid and recordid = :recordid limit 1', array(':openid' => $member['openid'], ':uniacid' => $_W['uniacid'], ':recordid' => $poster['id']));

		if (empty($qr)) {
			empty($scene_id) && ($scene_id = $this->getSceneID2());
			$result = $this->getSceneTicket2($expire, $scene_id);

			if (is_error($result)) {
				return $result;
			}


			$this->createRule();
			$ims_qrcode = array('uniacid' => $_W['uniacid'], 'acid' => $_W['acid'], 'qrcid' => ($scene_id[0] != 't' ? $scene_id : 0), 'model' => 0, 'name' => 'EWEI_SHOPV2_TASKNEW_POSTER', 'keyword' => 'EWEI_SHOPV2_TASKNEW', 'expire' => $expire, 'createtime' => time(), 'status' => 1, 'url' => $result['url'], 'ticket' => $result['ticket'], 'scene_str' => ($scene_id[0] == 't' ? $scene_id : ''));
			pdo_insert('qrcode', $ims_qrcode);
			$qr = array('uniacid' => $_W['uniacid'], 'openid' => $member['openid'], 'sceneid' => $scene_id, 'ticket' => $result['ticket'], 'recordid' => $poster['id']);
			pdo_insert('ewei_shop_task_qr', $qr);
			$qr['id'] = pdo_insertid();
		}


		return $qr;
	}

	/**
     * 创建TASKNEW回复规则
     */
	protected function createRule()
	{
		global $_W;
		$ruleSql = 'select id from ' . tablename('rule') . ' where `name` = \'ewei_shopv2:task\' and `module` = \'ewei_shopv2\' and uniacid = ' . $_W['uniacid'];
		$ruleCount = pdo_fetchcolumn($ruleSql);

		if (empty($ruleCount)) {
			$rule = array('uniacid' => $_W['uniacid'], 'name' => 'ewei_shopv2:task', 'module' => 'ewei_shopv2', 'status' => 1, 'reply_type' => 1);
			pdo_insert('rule', $rule);
			$ruleCount = pdo_insertid();

			if (empty($ruleCount)) {
				$rule = array('uniacid' => $_W['uniacid'], 'name' => 'ewei_shopv2:task', 'module' => 'ewei_shopv2', 'status' => 1);
				pdo_insert('rule', $rule);
				$ruleCount = pdo_insertid();
			}

		}


		$keywordSql = 'select COUNT(*) from ' . tablename('rule_keyword') . ' where `content` = \'EWEI_SHOPV2_TASKNEW\' and uniacid = ' . $_W['uniacid'];
		$keywordCount = pdo_fetchcolumn($keywordSql);

		if (empty($keywordCount)) {
			$keyword = array('rid' => $ruleCount, 'uniacid' => $_W['uniacid'], 'module' => 'ewei_shopv2', 'content' => 'EWEI_SHOPV2_TASKNEW', 'type' => 1, 'status' => 1);
			dump($keyword);
			pdo_insert('rule_keyword', $keyword);
		}

	}

	public function checkMember2($openid = '', $acc = '')
	{
		global $_W;
		$redis = redis();

		if (empty($acc)) {
			$acc = WeiXinAccount::create();
		}


		$userinfo = $acc->fansQueryInfo($openid);
		$userinfo['avatar'] = $userinfo['headimgurl'];
		load()->model('mc');
		$uid = mc_openid2uid($openid);

		if (!(empty($uid))) {
			pdo_update('mc_members', array('nickname' => $userinfo['nickname'], 'gender' => $userinfo['sex'], 'nationality' => $userinfo['country'], 'resideprovince' => $userinfo['province'], 'residecity' => $userinfo['city'], 'avatar' => $userinfo['headimgurl']), array('uid' => $uid));
		}


		pdo_update('mc_mapping_fans', array('nickname' => $userinfo['nickname']), array('uniacid' => $_W['uniacid'], 'openid' => $openid));
		$model = m('member');
		$member = $model->getMember($openid);

		if (empty($member)) {
			if (!(is_error($redis))) {
				$member = $redis->get($openid . '_task_checkMember');

				if (!(empty($member))) {
					return json_decode($member, true);
				}

			}


			$mc = mc_fetch($uid, array('realname', 'nickname', 'mobile', 'avatar', 'resideprovince', 'residecity', 'residedist'));
			$member = array('uniacid' => $_W['uniacid'], 'uid' => $uid, 'openid' => $openid, 'realname' => $mc['realname'], 'mobile' => $mc['mobile'], 'nickname' => (!(empty($mc['nickname'])) ? $mc['nickname'] : $userinfo['nickname']), 'avatar' => (!(empty($mc['avatar'])) ? $mc['avatar'] : $userinfo['avatar']), 'gender' => (!(empty($mc['gender'])) ? $mc['gender'] : $userinfo['sex']), 'province' => (!(empty($mc['resideprovince'])) ? $mc['resideprovince'] : $userinfo['province']), 'city' => (!(empty($mc['residecity'])) ? $mc['residecity'] : $userinfo['city']), 'area' => $mc['residedist'], 'createtime' => time(), 'status' => 0);
			pdo_insert('ewei_shop_member', $member);
			$member['id'] = pdo_insertid();
			$member['isnew'] = true;

			if (!(is_error($redis))) {
				$redis->set($openid . '_task_checkMember', json_encode($member), 20);
			}

		}
		 else {
			$member['nickname'] = $userinfo['nickname'];
			$member['avatar'] = $userinfo['headimgurl'];
			$member['province'] = $userinfo['province'];
			$member['city'] = $userinfo['city'];
			pdo_update('ewei_shop_member', $member, array('id' => $member['id']));
			$member['isnew'] = false;
		}

		return $member;
	}

	protected function createImage2($imgurl)
	{
		load()->func('communication');
		$resp = ihttp_request($imgurl);

		if (($resp['code'] == 200) && !(empty($resp['content']))) {
			return imagecreatefromstring($resp['content']);
		}


		$i = 0;

		while ($i < 3) {
			$resp = ihttp_request($imgurl);

			if (($resp['code'] == 200) && !(empty($resp['content']))) {
				return imagecreatefromstring($resp['content']);
			}


			++$i;
		}

		return '';
	}

	protected function mergeImage2($target, $data, $imgurl)
	{
		$img = $this->createImage2($imgurl);
		$w = imagesx($img);
		$h = imagesy($img);
		imagecopyresized($target, $img, $data['left'], $data['top'], 0, 0, $data['width'], $data['height'], $w, $h);
		imagedestroy($img);
		return $target;
	}

	protected function mergeHead2($target, $data, $imgurl)
	{
		if ($data['head_type'] == 'default') {
			$img = $this->createImage2($imgurl);
			$w = imagesx($img);
			$h = imagesy($img);
			imagecopyresized($target, $img, $data['left'], $data['top'], 0, 0, $data['width'], $data['height'], $w, $h);
			imagedestroy($img);
			return $target;
		}


		if ($data['head_type'] == 'circle') {
		}
		 else if ($data['head_type'] == 'rounded') {
		}

	}

	protected function mergeText2($target, $data, $text)
	{
		$font = IA_ROOT . '/addons/ewei_shopv2/static/fonts/msyh.ttf';
		$colors = $this->hex2rgb2($data['color']);
		$color = imagecolorallocate($target, $colors['red'], $colors['green'], $colors['blue']);
		@imagettftext($target, $data['size'], 0, $data['left'], $data['top'] + $data['size'], $color, $font, $text);
		return $target;
	}

	protected function hex2rgb2($colour)
	{
		if ($colour[0] == '#') {
			$colour = substr($colour, 1);
		}


		if (strlen($colour) == 6) {
			list($r, $g, $b) = array($colour[0] . $colour[1], $colour[2] . $colour[3], $colour[4] . $colour[5]);
		}
		 else if (strlen($colour) == 3) {
			list($r, $g, $b) = array($colour[0] . $colour[0], $colour[1] . $colour[1], $colour[2] . $colour[2]);
		}
		 else {
			return false;
		}

		$r = hexdec($r);
		$g = hexdec($g);
		$b = hexdec($b);
		return array('red' => $r, 'green' => $g, 'blue' => $b);
	}

	protected function uploadImage2($img)
	{
		load()->func('communication');
		$account = m('common')->getAccount();
		$access_token = $account->fetch_token();
		$url = 'http://file.api.weixin.qq.com/cgi-bin/media/upload?access_token=' . $access_token . '&type=image';
		$ch1 = curl_init();
		$data = array('media' => '@' . $img);

		if (version_compare(PHP_VERSION, '5.5.0', '>')) {
			$data = array('media' => curl_file_create($img));
		}


		curl_setopt($ch1, CURLOPT_URL, $url);
		curl_setopt($ch1, CURLOPT_POST, 1);
		curl_setopt($ch1, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch1, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch1, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch1, CURLOPT_POSTFIELDS, $data);
		$content = @json_decode(curl_exec($ch1), true);

		if (!(is_array($content))) {
			$content = array('media_id' => '');
		}


		curl_close($ch1);
		return $content['media_id'];
	}

	protected function getSceneID2()
	{
		global $_W;
		$acid = $_W['acid'];
		$start = 1;
		$end = 2147483647;
		$scene_id = rand($start, $end);

		if (empty($scene_id)) {
			$scene_id = rand($start, $end);
		}
		while (1) {
			$count = pdo_fetchcolumn('select count(*) from ' . tablename('qrcode') . ' where qrcid=:qrcid and acid=:acid and model=0 limit 1', array(':qrcid' => $scene_id, ':acid' => $acid));

			if ($count <= 0) {
				break;
			}


			$scene_id = rand($start, $end);

			if (empty($scene_id)) {
				$scene_id = rand($start, $end);
			}

		}

		return $scene_id;
	}

	protected function getSceneTicket2($expire, $scene_id)
	{
		global $_W;
		global $_GPC;
		$account = m('common')->getAccount();
		$bb = '{"expire_seconds":' . $expire . ',"action_info":{"scene":{"scene_id":' . $scene_id . '}},"action_name":"QR_SCENE"}';

		if ($scene_id[0] == 't') {
			$bb = '{"action_name": "QR_LIMIT_STR_SCENE", "action_info": {"scene": {"scene_str": "' . $scene_id . '"}}}';
		}


		$token = $account->fetch_token();
		$url = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=' . $token;
		$ch1 = curl_init();
		curl_setopt($ch1, CURLOPT_URL, $url);
		curl_setopt($ch1, CURLOPT_POST, 1);
		curl_setopt($ch1, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch1, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch1, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch1, CURLOPT_POSTFIELDS, $bb);
		$c = curl_exec($ch1);
		$result = @json_decode($c, true);

		if (!(is_array($result))) {
			return false;
		}


		if (!(empty($result['errcode']))) {
			return error(-1, $result['errmsg']);
		}


		$ticket = $result['ticket'];
		return array('barcode' => json_decode($bb, true), 'ticket' => $ticket);
	}

	/**
     * 任务进度检查公用方法
     * @param $num
     * @param $typeKey
     */
	public function checkTaskProgress($num, $typeKey, $recordid = 0, $openid = '', $goodsid = 0)
	{
		global $_W;

		if (empty($openid)) {
			$openid = $_W['openid'];
		}


		if (empty($openid)) {
			return false;
		}


		$type = $this->getTaskType($typeKey);
		$time = date('Y-m-d H:i:s');
		$sqlPassive = 'select * from ' . tablename('ewei_shop_task_list') . ' where uniacid = :uniacid and picktype = 1 and starttime < :starttime and endtime > :endtime';
		$paramsPassive = array(':uniacid' => $_W['uniacid'], ':starttime' => $time, ':endtime' => $time);
		$passiveTask = pdo_fetchall($sqlPassive, $paramsPassive);

		if (!(empty($passiveTask))) {
			foreach ($passiveTask as $task ) {
				if (!(pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_task_record') . ' where taskid = ' . $task['id'] . ' and uniacid = ' . $_W['uniacid']))) {
					$this->pickTask($task['id'], $openid);
				}

			}
		}


		$condtion = '';

		if (!(empty($recordid))) {
			$condtion = ' and id = ' . $recordid . ' ';
		}


		if (!(empty($goodsid))) {
			$condtion .= ' and FIND_IN_SET(\'' . $goodsid . '\',require_goods) ';
		}


		$sql = 'select * from ' . tablename('ewei_shop_task_record') . ' where uniacid = :uniacid ' . $condtion . ' and openid = :openid and tasktype = :tasktype and (stoptime = \'0000-00-00 00:00:00\' or stoptime >\'' . $time . '\') and finishtime = \'0000-00-00 00:00:00\'';
		$params = array(':uniacid' => $_W['uniacid'], ':openid' => $openid, ':tasktype' => $typeKey);
		$allRecord = pdo_fetchall($sql, $params);

		if (!(empty($allRecord))) {
			foreach ($allRecord as $record ) {
				$cache_key = $record['id'] . 'tasknew_' . $openid . '_pro' . $record['task_progress'];
				$height = m('cache')->get($cache_key);

				if (!(empty($height))) {
					m('cache')->del($cache_key);

					return;
				}


				m('cache')->set($cache_key, time());

				if ($typeKey == 'recharge_full') {
					if ($num < $record['task_demand']) {
						continue;
					}

				}


				$record['task_progress'] = $record['task_progress'] + $num;

				if ($record['task_demand'] <= $record['task_progress']) {
					$update_arr = array('task_progress' => $record['task_demand']);
					$update_arr['finishtime'] = date('Y-m-d H:i:s');
					$ret = pdo_update('ewei_shop_task_record', $update_arr, array('id' => $record['id']));

					if ($ret) {
						$this->sentReward($record['id'], $openid);

						if ($type['type_key'] === 'poster') {
							$this->followReward($record['id']);
						}


						$this->taskFinishMessage($record['openid'], $record);
					}

				}
				 else {
					if ($type['type_key'] === 'poster') {
						if ((0 < $record['level1']) && ($record['task_progress'] == $record['level1'])) {
							$this->sentReward($record['id'], $openid, 1);
						}


						if ((0 < $record['level2']) && ($record['task_progress'] == $record['level2'])) {
							$this->sentReward($record['id'], $openid, 2);
						}

					}


					if ($type['once'] != 1) {
						pdo_update('ewei_shop_task_record', array('task_progress' => $record['task_progress']), array('id' => $record['id']));
						$this->taskProgressMessage($record['openid'], $record);

						if ($type['type_key'] === 'poster') {
							$this->followReward($record['id']);
						}

					}

				}
			}
		}

	}

	public function sentReward($recordid, $openid, $level = 0)
	{
		global $_W;
		$time = date('Y-m-d H:i:s');
		$sql = 'select * from ' . tablename('ewei_shop_task_reward') . ' where recordid = :recordid and openid = :openid and uniacid = :uniacid and `get` = 0 and sent = 0 and `level` = ' . $level . ' ';
		$param = array(':recordid' => $recordid, ':uniacid' => $_W['uniacid'], ':openid' => $openid);
		$rewards = pdo_fetchall($sql, $param);

		if (!(empty($rewards))) {
			foreach ($rewards as $k => $reward ) {
				switch ($reward['reward_type']) {
				case 'credit':
					m('member')->setCredit($openid, 'credit1', floatval($reward['reward_data']), array($_W['uid'], '任务中心奖励'));
					pdo_update('ewei_shop_task_reward', array('sent' => 1, 'get' => 1, 'gettime' => $time, 'senttime' => $time), array('id' => $reward['id']));
					break;

				case 'balance':
					m('member')->setCredit($openid, 'credit2', floatval($reward['reward_data']), array($_W['uid'], '任务中心奖励'));
					pdo_update('ewei_shop_task_reward', array('sent' => 1, 'get' => 1, 'gettime' => $time, 'senttime' => $time), array('id' => $reward['id']));
					break;

				case 'redpacket':
					pdo_update('ewei_shop_task_reward', array('get' => 1, 'gettime' => $time), array('id' => $reward['id']));
					break;

				case 'coupon':
					$data = array('uniacid' => $_W['uniacid'], 'merchid' => 0, 'openid' => $openid, 'couponid' => $reward['reward_data'], 'gettype' => 7, 'gettime' => time(), 'senduid' => $_W['uid']);
					pdo_insert('ewei_shop_coupon_data', $data);
					pdo_update('ewei_shop_task_reward', array('sent' => 1, 'get' => 1, 'gettime' => $time, 'senttime' => $time), array('id' => $reward['id']));
					break;

				case 'goods':
					pdo_update('ewei_shop_task_reward', array('get' => 1, 'gettime' => $time), array('id' => $reward['id']));
					break;
				}
			}
		}

	}

	/**
     * 关注奖励发放
     * @param $recordid
     * @return bool
     */
	public function followReward($recordid)
	{
		global $_W;
		$time = date('Y-m-d H:i:s');
		$info = m('member')->getInfo($_W['openid']);
		$record = pdo_fetch('select * from ' . tablename('ewei_shop_task_record') . ' where id = :id and uniacid = :uniacid', array(':id' => $recordid, ':uniacid' => $_W['uniacid']));
		$frewards = json_decode($record['followreward_data'], true);

		if (empty($frewards)) {
			return false;
		}


		foreach ($frewards as $k => $reward ) {
			switch ($k) {
			case 'credit':
				if (0 < $reward) {
					m('member')->setCredit($_W['openid'], 'credit1', floatval($reward), array($_W['uid'], '任务中心关注海报奖励'));
					pdo_insert('ewei_shop_task_reward', array('uniacid' => $_W['uniacid'], 'taskid' => $record['taskid'], 'tasktitle' => $record['tasktitle'], 'tasktype' => $record['tasktype'], 'taskowner' => $record['openid'], 'ownernickname' => $record['nickname'], 'recordid' => $record['id'], 'nickname' => $info['nickname'], 'headimg' => $info['avatar'], 'openid' => $_W['openid'], 'reward_type' => 'credit', 'reward_title' => $reward . '积分', 'reward_data' => $reward, 'sent' => 1, 'get' => 1, 'gettime' => $time, 'senttime' => $time, 'isjoiner' => 1));
				}


				break;

			case 'balance':
				if (0 < $reward) {
					m('member')->setCredit($_W['openid'], 'credit2', floatval($reward), array($_W['uid'], '任务中心关注海报奖励'));
					pdo_insert('ewei_shop_task_reward', array('uniacid' => $_W['uniacid'], 'taskid' => $record['taskid'], 'tasktitle' => $record['tasktitle'], 'tasktype' => $record['tasktype'], 'taskowner' => $record['openid'], 'ownernickname' => $record['nickname'], 'recordid' => $record['id'], 'nickname' => $info['nickname'], 'headimg' => $info['avatar'], 'openid' => $_W['openid'], 'reward_type' => 'balance', 'reward_title' => $reward . '元余额', 'reward_data' => $reward, 'sent' => 1, 'get' => 1, 'gettime' => $time, 'senttime' => $time, 'isjoiner' => 1));
				}


				break;

			case 'redpacket':
				if (0 < $reward) {
					pdo_insert('ewei_shop_task_reward', array('uniacid' => $_W['uniacid'], 'taskid' => $record['taskid'], 'tasktitle' => $record['tasktitle'], 'tasktype' => $record['tasktype'], 'taskowner' => $record['openid'], 'ownernickname' => $record['nickname'], 'recordid' => $record['id'], 'nickname' => $info['nickname'], 'headimg' => $info['avatar'], 'openid' => $_W['openid'], 'reward_type' => 'redpacket', 'reward_title' => $reward . '元微信红包', 'reward_data' => $reward, 'get' => 1, 'gettime' => $time, 'isjoiner' => 1));
				}


				break;

			case 'coupon':
				if (!(empty($reward)) && is_array($reward)) {
					foreach ($reward as $ck => $cv ) {
						$data = array('uniacid' => $_W['uniacid'], 'merchid' => 0, 'openid' => $_W['openid'], 'couponid' => $cv['id'], 'gettype' => 7, 'gettime' => time(), 'senduid' => $_W['uid']);
						$i = 0;

						while ($i < $cv['num']) {
							pdo_insert('ewei_shop_coupon_data', $data);
							pdo_insert('ewei_shop_task_reward', array('uniacid' => $_W['uniacid'], 'taskid' => $record['taskid'], 'tasktitle' => $record['tasktitle'], 'tasktype' => $record['tasktype'], 'taskowner' => $record['openid'], 'ownernickname' => $record['nickname'], 'recordid' => $record['id'], 'nickname' => $info['nickname'], 'headimg' => $info['avatar'], 'openid' => $_W['openid'], 'reward_type' => 'coupon', 'reward_title' => $cv['couponname'], 'reward_data' => $cv['id'], 'sent' => 1, 'get' => 1, 'gettime' => $time, 'senttime' => $time, 'isjoiner' => 1));
							++$i;
						}
					}
				}


				break;
			}

			$sign = 1;
		}

		if (!(empty($sign))) {
			$this->taskPosterFollowMessage($_W['openid'], $record, $info['nickname']);
		}

	}

	/**
     * 任务完成通知
     * @param $openid
     */
	public function taskFinishMessage($openid, $record)
	{
		$msg = $this->getMessageSet('msg_finish');

		if (empty($msg)) {
			return false;
		}


		$taskType = $this->getTaskType($record['type']);
		$desc = $taskType['verb'];

		if (!(empty($taskType['unit']))) {
			$desc .= $record['task_demand'] . $taskType['unit'];
		}


		$tpl = array(
			array('接取时间', $record['picktime']),
			array('截止时间', ($record['stoptime'] == '0000-00-00 00:00:00' ? '无' : $record['stoptime'])),
			array('当前时间', date('Y-m-d H:i:s')),
			array('用户昵称', $record['nickname']),
			array('任务名称', $record['tasktitle']),
			array('任务需求', $desc)
			);

		foreach ($tpl as $t ) {
			$msg = str_replace('[' . $t[0] . ']', $t[1], $msg);
		}

		m('message')->sendCustomNotice($openid, $msg . '\\n', mobileUrl('task.reward', NULL, 1));
	}

	/**
     * 任务进度通知
     * @param $openid
     */
	public function taskProgressMessage($openid, $record)
	{
		$msg = $this->getMessageSet('msg_progress');

		if (empty($msg)) {
			return false;
		}


		$taskType = $this->getTaskType($record['type']);
		$desc = $taskType['verb'];

		if (!(empty($taskType['unit']))) {
			$desc .= $record['task_demand'] . $taskType['unit'];
		}


		$tpl = array(
			array('接取时间', $record['picktime']),
			array('截止时间', ($record['stoptime'] == '0000-00-00 00:00:00' ? '无' : $record['stoptime'])),
			array('当前时间', date('Y-m-d H:i:s')),
			array('用户昵称', $record['nickname']),
			array('任务名称', $record['tasktitle']),
			array('任务需求', $desc),
			array('当前进度', $record['task_progress'] / $record['task_demand'])
			);

		foreach ($tpl as $t ) {
			$msg = str_replace('[' . $t[0] . ']', $t[1], $msg);
		}

		m('message')->sendCustomNotice($openid, $msg . '\\n', mobileUrl('task', NULL, 1));
	}

	/**
     * 海报进度通知
     * @param $openid
     */
	public function taskPosterMessage($openid, $record)
	{
		$msg = '您的任务海报被扫描，任务进度+1\\n\\n';
		m('message')->sendCustomNotice($openid, $msg . '\\n', mobileUrl('task.reward', NULL, 1));
	}

	/**
     * 关注海报奖励通知
     * @param $openid
     */
	public function taskPosterFollowMessage($openid, $record, $nickname = '')
	{
		$msg = $this->getMessageSet('msg_follow');

		if (empty($msg)) {
			return false;
		}


		$tpl = array(
			array('当前时间', date('Y-m-d H:i:s')),
			array('海报所有者昵称', $record['nickname']),
			array('扫描者昵称', $nickname),
			array('任务名称', $record['tasktitle'])
			);

		foreach ($tpl as $t ) {
			$msg = str_replace('[' . $t[0] . ']', $t[1], $msg);
		}

		m('message')->sendCustomNotice($openid, $msg . '\\n', mobileUrl('task.reward', NULL, 1));
	}

	/**
     * 接取任务通知
     * @param $openid
     */
	public function taskPickMessage($openid, $record)
	{
		$msg = $this->getMessageSet('msg_pick');

		if (empty($msg)) {
			return false;
		}


		$taskType = $this->getTaskType($record['type']);
		$desc = $taskType['verb'];

		if (!(empty($taskType['unit']))) {
			$desc .= $record['task_demand'] . $taskType['unit'];
		}


		$tpl = array(
			array('接取时间', $record['picktime']),
			array('截止时间', ($record['stoptime'] == '0000-00-00 00:00:00' ? '无' : $record['stoptime'])),
			array('用户昵称', $record['nickname']),
			array('任务名称', $record['tasktitle']),
			array('任务需求', $desc)
			);

		foreach ($tpl as $t ) {
			$msg = str_replace('[' . $t[0] . ']', $t[1], $msg);
		}

		m('message')->sendCustomNotice($openid, $msg . '\\n', mobileUrl('task.mine', array('status' => 1), 1));
	}

	/**
     * 特惠商品下单检测
     * @param int $taskrewardgoodsid
     * @return bool
     */
	public function getTaskRewardGoodsInfo($taskrewardgoodsid = 0)
	{
		global $_W;

		if (empty($taskrewardgoodsid)) {
			return false;
		}


		$reward = pdo_get('ewei_shop_task_reward', array('id' => $taskrewardgoodsid, 'openid' => $_W['openid'], 'uniacid' => $_W['uniacid'], 'get' => 1, 'sent' => 0, 'reward_type' => 'goods'));

		if (empty($reward)) {
			return false;
		}


		return $reward;
	}

	/**
     * 特惠商品下单完成
     * @param int $taskrewardgoodsid
     * @return bool
     */
	public function setTaskRewardGoodsSent($taskrewardgoodsid = 0)
	{
		global $_W;

		if (empty($taskrewardgoodsid)) {
			return false;
		}


		pdo_update('ewei_shop_task_reward', array('sent' => 1, 'senttime' => date('Y-m-d H:i:s')), array('id' => $taskrewardgoodsid));
		$_SESSION['taskcut'] = NULL;
		return true;
	}

	/**
     * 会员中心入口是否开启
     */
	public function TasknewEntrance()
	{
		global $_W;
		$sql = 'select entrance from ' . tablename('ewei_shop_task_set') . ' where uniacid = ' . $_W['uniacid'];
		return pdo_fetchcolumn($sql);
	}

	/**
     * 获取消息通知设置
     * @param array $field
     * @return bool
     */
	public function getMessageSet($field = array('msg_pick', 'msg_progress', 'msg_finish', 'msg_follow'))
	{
		global $_W;
		$isString = is_string($field);

		if ($isString) {
			$field2 = array($field);
		}
		 else {
			$field2 = $field;
		}

		if (!(is_array($field2))) {
			return false;
		}


		$msg = pdo_get('ewei_shop_task_set', array('uniacid' => $_W['uniacid']), $field2);

		if ($isString) {
			return $msg[$field];
		}


		return $msg;
	}
}


?>