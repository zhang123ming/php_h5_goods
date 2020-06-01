<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

// define('SNS_CREDIT_POST', 0);
// define('SNS_CREDIT_REPLY', 1);
// define('SNS_CREDIT_TOP', 2);
// define('SNS_CREDIT_TOP_CANCEL', 3);
// define('SNS_CREDIT_TOP_BOARD', 4);
// define('SNS_CREDIT_TOP_BOARD_CANCEL', 5);
// define('SNS_CREDIT_BEST', 6);
// define('SNS_CREDIT_BEST_CANCEL', 7);
// define('SNS_CREDIT_BEST_BOARD_CANCEL', 8);
// define('SNS_CREDIT_DELETE_POST', 9);
// define('SNS_CREDIT_DELETE_REPLY', 10);
// define('SNS_MESSAGE_REPLY', 20);

if (!class_exists('BottledoctorModel')) {
	class BottledoctorModel extends PluginModel
	{
		public function checkMember()
		{
			global $_W;
			global $_GPC;

			if (!empty($_W['openid'])) {
				$member = pdo_fetch('select * from ' . tablename('ewei_shop_sns_member') . ' where uniacid=:uniacid and openid=:openid limit 1', array(':uniacid' => $_W['uniacid'], ':openid' => $_W['openid']));

				if (empty($member)) {
					$member = array('uniacid' => $_W['uniacid'], 'openid' => $_W['openid'], 'createtime' => time());
					pdo_insert('ewei_shop_sns_member', $member);
				}
				else {
					if (!empty($member['isblack'])) {
						show_message('禁止访问，请联系客服!');
					}
				}
			}
		}

		public function getMember($openid)
		{
			global $_W;
			global $_GPC;
			$member = m('member')->getMember($openid);
			$sns_member = pdo_fetch('select * from ' . tablename('ewei_shop_sns_member') . ' where uniacid=:uniacid and openid=:openid limit 1', array(':uniacid' => $_W['uniacid'], ':openid' => $member['openid']));

			if (empty($sns_member)) {
				$member['sns_credit'] = 0;
				$member['sns_level'] = 0;
				$member['notupgrade'] = 0;
			}
			else {
				$member['sns_id'] = $sns_member['id'];
				$member['sns_credit'] = $sns_member['credit'];
				$member['sns_level'] = $sns_member['level'];
				$member['sns_sign'] = $sns_member['sign'];
				$member['sns_notupgrade'] = $sns_member['notupgrade'];
			}

			return $member;
		}

		/**
         * 获取分类
         * @param bool $all
         * @return mixed
         */
		public function getCategory($all = true)
		{
			global $_W;
			$condition = ($all ? '' : ' and `status` = 1');
			return pdo_fetchall('select * from ' . tablename('ewei_shop_sns_category') . ' where uniacid=:uniacid ' . $condition . ' and enabled = 1 order by displayorder desc', array(':uniacid' => $_W['uniacid']), 'id');
		}

		/**
         * 获取一个社区
         * @param string $bid
         * @return mixed
         */
		public function getBoard($bid = '0')
		{
			global $_W;
			return pdo_fetch('select * from ' . tablename('ewei_shop_sns_board') . ' where uniacid=:uniacid and id=:id  limit 1', array(':uniacid' => $_W['uniacid'], ':id' => $bid));
		}

		/**
         * 获取一个帖子
         * @param string $pid
         * @return mixed
         */
		public function getPost($pid = '0')
		{
			global $_W;
			return pdo_fetch('select * from ' . tablename('ewei_shop_sns_post') . ' where uniacid=:uniacid and id=:id  limit 1', array(':uniacid' => $_W['uniacid'], ':id' => $pid));
		}

		/**
         * 是否是版主
         * @param int $bid
         * @return bool
         */
		public function isManager($bid = 0, $openid = '')
		{
			global $_W;

			if (empty($openid)) {
				$openid = $_W['openid'];
			}

			$count = pdo_fetchcolumn('select count(*)  from ' . tablename('ewei_shop_sns_manage') . ' where uniacid=:uniacid and bid=:bid and openid=:openid limit 1', array(':uniacid' => $_W['uniacid'], ':bid' => $bid, ':openid' => $openid));
			return 0 < $count;
		}

		/**
         * 是否是超级管理员
         * @param int $bid
         * @return bool
         */
		public function isSuperManager($openid = '')
		{
			global $_W;

			if (empty($openid)) {
				$openid = $_W['openid'];
			}

			$set = $this->getSet();
			$managers = explode(',', $set['managers']);
			return in_array($openid, $managers);
		}

		/**
         * 社区话题数
         * @param $bid
         * @return mixed
         */
		public function getPostCount($bid)
		{
			global $_W;
			return pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_sns_post') . "\r\n            where uniacid=:uniacid and bid=:bid and pid=0 and deleted = 0 limit 1", array(':uniacid' => $_W['uniacid'], ':bid' => $bid));
		}

		/**
         * 社区关注数
         * @param $bid
         * @return mixed
         */
		public function getFollowCount($bid)
		{
			global $_W;
			return pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_sns_board_follow') . "\r\n            where uniacid=:uniacid and bid=:bid limit 1", array(':uniacid' => $_W['uniacid'], ':bid' => $bid));
		}

		/**
         * 是否关注社区
         * @param $bid
         * @return bool
         */
		public function isFollow($bid)
		{
			global $_W;
			$count = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_sns_board_follow') . ' where uniacid=:uniacid and bid=:bid and openid=:openid limit 1', array(':uniacid' => $_W['uniacid'], ':bid' => $bid, ':openid' => $_W['openid']));
			return 0 < $count;
		}

		/**
         * 获取置顶帖
         * @param int $bid
         */
		public function getTops($bid = 0)
		{
			global $_W;
			$condition = ' and ( istop=1';

			if (!empty($bid)) {
				$condition .= ' or (isboardtop=1 and bid=' . intval($bid) . ')';
			}

			$condition .= ')';
			return pdo_fetchall('select id,title,istop,isboardtop from ' . tablename('ewei_shop_sns_post') . ' where uniacid=:uniacid ' . $condition . ' and pid=0 and deleted=0  order by istop desc,replytime desc', array(':uniacid' => $_W['uniacid']));
		}

		/**
         * 积分设置
         * @param string $openid
         * @param int $bid
         * @param int $type
         */
		public function setCredit($openid = '', $bid = 0, $type = -1)
		{
			$board = $this->getBoard($bid);

			if (empty($board)) {
				return NULL;
			}

			$credit = 0;
			$log = '';
			if (($type == SNS_CREDIT_POST) || ($type == SNS_CREDIT_DELETE_POST)) {
				$credit = $board['postcredit'];
				$log = '人人社区发表话题奖励积分: +' . $credit;

				if ($type == SNS_CREDIT_DELETE_POST) {
					$credit = 0 - $credit;
					$log = '人人社区被删除话题扣除积分: -' . abs($credit);
				}
			}
			else {
				if (($type == SNS_CREDIT_REPLY) || ($type == SNS_CREDIT_DELETE_REPLY)) {
					$credit = $board['replycredit'];
					$log = '人人社区发表评论奖励积分: +' . $credit;

					if ($type == SNS_CREDIT_DELETE_REPLY) {
						$log = '人人社区被删除评论奖励积分: -' . abs($credit);
						$credit = 0 - $credit;
					}
				}
				else {
					if (($type == SNS_CREDIT_TOP) || ($type == SNS_CREDIT_TOP_CANCEL)) {
						$credit = $board['topcredit'];
						$log = '人人社区话题被全站置顶奖励积分: +' . $credit;

						if ($type == SNS_CREDIT_TOP_CANCEL) {
							$credit = 0 - $credit;
							$log = '人人社区话题被取消全站置顶扣除积分: ' . abs($credit);
						}
					}
					else {
						if (($type == SNS_CREDIT_TOP_BOARD) || ($type == SNS_CREDIT_TOP_BOARD_CANCEL)) {
							$credit = $board['topboardcredit'];
							$log = '人人社区话题被版块置顶奖励积分: +' . $credit;

							if ($type == SNS_CREDIT_TOP_BOARD_CANCEL) {
								$credit = 0 - $credit;
								$log = '人人社区话题被版块置顶奖励积分: -' . abs($credit);
							}
						}
						else {
							if (($type == SNS_CREDIT_BEST) || ($type == SNS_CREDIT_BEST_CANCEL)) {
								$credit = $board['bestcredit'];
								$log = '人人社区话题被全站精华奖励积分: +' . $credit;

								if ($type == SNS_CREDIT_TOP_BOARD_CANCEL) {
									$credit = 0 - $credit;
									$log = '人人社区话题被全站精华奖励积分: -' . abs($credit);
								}
							}
							else {
								if (($type == SNS_CREDIT_BEST_BOARD) || ($type == SNS_CREDIT_BEST_BOARD_CANCEL)) {
									$credit = $board['bestboardcredit'];
									$log = '人人社区话题被版块精华奖励积分: +' . $credit;

									if ($type == SNS_CREDIT_BEST_BOARD_CANCEL) {
										$credit = 0 - $credit;
										$log = '人人社区话题被取消版块精华奖励积分: -' . abs($credit);
									}
								}
							}
						}
					}
				}
			}

			if (0 < abs($credit)) {
				m('member')->setCredit($openid, 'credit1', $credit, array(0, $log));
				$member = $this->getMember($openid);
				$newcredit = $member['sns_credit'] + $credit;

				if ($newcredit <= 0) {
					$newcredit = 0;
				}

				pdo_update('ewei_shop_sns_member', array('credit' => $newcredit), array('id' => $member['sns_id']));
				$this->upgradeLevel($openid);
			}
		}

		public function getLevel($openid)
		{
			global $_W;
			global $_S;

			if (empty($openid)) {
				return false;
			}

			$member = $this->getMember($openid);
			if (!empty($member) && !empty($member['sns_level'])) {
				$level = pdo_fetch('select * from ' . tablename('ewei_shop_sns_level') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $member['sns_level'], ':uniacid' => $_W['uniacid']));

				if (!empty($level)) {
					return $level;
				}
			}

			return array('levelname' => empty($set['levelname']) ? '社区粉丝' : $set['levelname'], 'color' => empty($set['levelcolor']) ? '#333' : $set['levelcolor'], 'bg' => empty($set['levelbg']) ? '#eee' : $set['levelbg']);
		}

		public function upgradeLevel($openid)
		{
			global $_W;
			$member = $this->getMember($openid);

			if ($member['sns_notupgrade']) {
				return NULL;
			}

			$credit = $member['sns_credit'];
			$set = $this->getSet();
			$leveltype = intval($set['leveltype']);
			$level = false;

			if (empty($leveltype)) {
				$level = pdo_fetch('select * from ' . tablename('ewei_shop_sns_level') . ' where uniacid=:uniacid  and enabled=1 and ' . $credit . ' >= credit and credit>0  order by credit desc limit 1', array(':uniacid' => $_W['uniacid']));
			}
			else {
				if ($leveltype == 1) {
					$post = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_sns_post') . ' where uniacid=:uniacid and openid=:openid and pid=0 and checked=1', array(':uniacid' => $_W['uniacid'], ':openid' => $_W['openid']));
					$level = pdo_fetch('select * from ' . tablename('ewei_shop_sns_level') . ' where uniacid=:uniacid and enabled=1 and ' . $post . ' >= `post` and `post`>0  order by `post` desc limit 1', array(':uniacid' => $_W['uniacid']));
				}
			}

			if (empty($level)) {
				return NULL;
			}

			if ($level['id'] == $member['sns_level']) {
				return NULL;
			}

			$oldlevel = $this->getLevel($openid);
			$canupgrade = false;

			if (empty($oldlevel['id'])) {
				$canupgrade = true;
			}
			else if (empty($leveltype)) {
				if ($oldlevel['credit'] < $level['credit']) {
					$canupgrade = true;
				}
			}
			else {
				if ($oldlevel['post'] < $level['post']) {
					$canupgrade = true;
				}
			}

			if ($canupgrade) {
				pdo_update('ewei_shop_sns_member', array('level' => $level['id']), array('id' => $member['sns_id']));
				$this->sendMemberUpgradeMessage($openid, $member['nickname'], $oldlevel, $level);
			}
		}

		/**
         * 升级消息
         * @param $openid
         * @param $oldlevel
         * @param $level
         */
		public function sendMemberUpgradeMessage($openid, $nickname, $oldlevel, $level)
		{
			$set = $this->getSet();
			$tm = $set['tm'];

			if (empty($tm['upgrade_content'])) {
				return NULL;
			}

			$message = $tm['upgrade_content'];
			$message = str_replace('[昵称]', $nickname, $message);
			$message = str_replace('[新等级]', $oldlevel['levelname'], $message);
			$message = str_replace('[旧等级]', $level['levelname'], $message);
			$message = str_replace('[时间]', date('Y-m-d H:i:s', time()), $message);
			$msg = array(
				'keyword1' => array('value' => !empty($tm['upgrade_title']) ? $tm['upgrade_title'] : '社区等级升级', 'color' => '#73a68d'),
				'keyword2' => array('value' => $message, 'color' => '#73a68d')
				);

			if (!empty($tm['templateid'])) {
				m('message')->sendTplNotice($openid, $tm['templateid'], $msg);
			}
			else {
				m('message')->sendCustomNotice($openid, $msg);
			}
		}

		/**
         * 评论消息
         * @param $openid
         * @param $data
         */
		public function sendReplyMessage($openid, $data)
		{
			$set = $this->getSet();
			$tm = $set['tm'];

			if (empty($tm['reply_content'])) {
				return NULL;
			}

			$message = $tm['reply_content'];
			$message = str_replace('[评论者]', $data['nickname'], $message);
			$message = str_replace('[版块]', $data['boardtitle'], $message);
			$message = str_replace('[话题]', $data['posttitle'], $message);
			$message = str_replace('[内容]', $data['content'], $message);
			$message = str_replace('[时间]', date('Y-m-d H:i:s', $data['createtime']), $message);
			$msg = array(
				'keyword1' => array('value' => !empty($tm['reply_title']) ? $tm['reply_title'] : '您的话题有新的评论', 'color' => '#73a68d'),
				'keyword2' => array('value' => $message, 'color' => '#73a68d')
				);
			$url = mobileUrl('sns/post', array('id' => $data['id']), true);

			if (!empty($tm['templateid'])) {
				m('message')->sendTplNotice($openid, $tm['templateid'], $msg, $url);
			}
			else {
				m('message')->sendCustomNotice($openid, $msg, $url);
			}
		}

		public function timeBefore($the_time)
		{
			$now_time = time();
			$dur = $now_time - $the_time;

			if ($dur < 0) {
				return $the_time;
			}

			if ($dur < 60) {
				return '刚刚';
			}

			if ($dur < 3600) {
				return floor($dur / 60) . '分钟前';
			}

			if ($dur < 86400) {
				return floor($dur / 3600) . '小时前';
			}

			if ($dur < 259200) {
				return floor($dur / 86400) . '天前';
			}

			return date('m-d', $the_time);
		}

		/**
         * 获取所有会员等级
         * @global type $_W
         * @return type
         */
		public function getLevels($all = true)
		{
			global $_W;
			$condition = '';

			if (!$all) {
				$condition = ' and enabled=1';
			}

			return pdo_fetchall('select * from ' . tablename('ewei_shop_sns_level') . ' where uniacid=:uniacid' . $condition . ' order by id asc', array(':uniacid' => $_W['uniacid']));
		}

		public function replaceContent($content)
		{
			return str_replace("\n", '<br/>', preg_replace('/\\[EM(\\w+)\\]/', '<img src="../addons/ewei_shopv2/plugin/sns/static/images/face/${1}.gif" class="emoji" />', $content));
		}

		public function check($member, $board, $isPost = false)
		{
			global $_W;
			global $_GPC;
			$levelid = $member['level'];
			$groupid = $member['groupid'];
			$levels = ($isPost ? $board['postlevels'] : $board['showlevels']);

			if ($levels != '') {
				$arr = explode(',', $levels);

				if (!in_array($levelid, $arr)) {
					if ($_W['isajax']) {
						return error(-1, '会员等级限制');
					}

					return error(-1, '您的会员等级没有权限浏览此版块');
				}
			}

			$levels = ($isPost ? $board['postgroups'] : $board['showgroups']);

			if ($levels != '') {
				$arr = explode(',', $levels);

				if (!in_array($groupid, $arr)) {
					if ($_W['isajax']) {
						return error(-1, '会员组限制');
					}

					return error(-1, '您的会员组没有权限浏览此版块');
				}
			}

			$levels = ($isPost ? $board['postsnslevels'] : $board['showsnslevels']);

			if ($levels) {
				$arr = explode(',', $levels);

				if (!in_array($member['sns_level'], $arr)) {
					if ($_W['isajax']) {
						return error(-1, '社区等级限制');
					}

					return error(-1, '您的社区等级没有权限浏览此版块');
				}
			}

			$plugin_commission = p('commission');

			if ($plugin_commission) {
				$set = $plugin_commission->getSet();

				if (!empty($board['notagent'])) {
					if (!$member['status'] && !$member['isagent']) {
						if ($_W['isajax']) {
							return error(-1, '非' . $set['texts']['agent']);
						}

						return error(-1, '您不是' . $set['texts']['agent'] . '，没有权限浏览此版块');
					}
				}

				$levels = ($isPost ? $board['postagentlevels'] : $board['showagentlevels']);

				if ($levels) {
					$arr = explode(',', $levels);

					if (!in_array($member['agentlevel'], $arr)) {
						if ($_W['isajax']) {
							return error(-1, $set['texts']['agent'] . '等级限制');
						}

						return error(-1, '您的' . $set['texts']['agent'] . '等级没有权限浏览此版块');
					}
				}
			}

			$plugin_globonus = p('globonus');

			if ($plugin_globonus) {
				$set = $plugin_globonus->getSet();

				if (!empty($board['notpartner'])) {
					if (!$member['partnerstatus'] && !$member['ispartner']) {
						if ($_W['isajax']) {
							return error(-1, '非' . $set['texts']['partner']);
						}

						return error(-1, '您不是' . $set['texts']['partner'] . '，没有权限浏览此版块');
					}
				}

				$levels = ($isPost ? $board['postpartnerlevels'] : $board['showaagentlevels']);

				if ($levels) {
					$arr = explode(',', $levels);

					if (!in_array($member['aagentlevel'], $arr)) {
						if ($_W['isajax']) {
							return error(-1, $set['texts']['partner'] . '等级限制');
						}

						return error(-1, '您的' . $set['texts']['partner'] . '等级没有权限浏览此版块');
					}
				}
			}

			return true;
		}

		public function getAvatar($avatar = '')
		{
			if (empty($avatar)) {
				$set = $this->getSet();
				$avatar = (empty($set['head']) ? '../addons/ewei_shopv2/plugin/sns/static/images/head.jpg' : tomedia($set['head']));
			}

			return $avatar;
		}
	


	//开始文章管理
		public function doShare($aid, $shareid, $myid)
		{
			global $_W;
			global $_GPC;
			if (empty($aid) || empty($shareid) || empty($myid) || ($shareid == $myid)) {
				return NULL;
			}

			$article = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_article') . ' WHERE id=:aid and article_state=1 and uniacid=:uniacid limit 1 ', array(':aid' => $aid, ':uniacid' => $_W['uniacid']));

			if (empty($article)) {
				return NULL;
			}

			$profile = m('member')->getMember($shareid);
			$myinfo = m('member')->getMember($myid);
			if (empty($myinfo) || empty($profile)) {
				return NULL;
			}

			$shopset = $_W['shopset'];
			$givecredit = intval($article['article_rule_credit']);
			$givemoney = floatval($article['article_rule_money']);
			$my_click = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('ewei_shop_article_share') . ' WHERE aid=:aid and click_user=:click_user and uniacid=:uniacid ', array(':aid' => $article['id'], ':click_user' => $myid, ':uniacid' => $_W['uniacid']));

			if (!empty($my_click)) {
				$givecredit = intval($article['article_rule_credit2']);
				$givemoney = floatval($article['article_rule_money2']);
			}

			if (!empty($article['article_hasendtime']) && ($article['article_endtime'] < time())) {
				return NULL;
			}

			$readtime = $article['article_readtime'];

			if ($readtime <= 0) {
				$readtime = 4;
			}

			$clicktime = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('ewei_shop_article_share') . ' WHERE aid=:aid and share_user=:share_user and click_user=:click_user and uniacid=:uniacid ', array(':aid' => $article['id'], ':share_user' => $shareid, ':click_user' => $myid, ':uniacid' => $_W['uniacid']));

			if ($readtime <= $clicktime) {
				return NULL;
			}

			$all_click = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('ewei_shop_article_share') . ' WHERE aid=:aid and share_user=:share_user and uniacid=:uniacid ', array(':aid' => $article['id'], ':share_user' => $shareid, ':uniacid' => $_W['uniacid']));

			if ($article['article_rule_allnum'] <= $all_click) {
				$givecredit = 0;
				$givemoney = 0;
			}
			else {
				$day_start = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
				$day_end = mktime(0, 0, 0, date('m'), date('d') + 1, date('Y')) - 1;
				$day_click = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('ewei_shop_article_share') . ' WHERE aid=:aid and share_user=:share_user and click_date>:day_start and click_date<:day_end and uniacid=:uniacid ', array(':aid' => $article['id'], ':share_user' => $shareid, ':day_start' => $day_start, ':day_end' => $day_end, ':uniacid' => $_W['uniacid']));

				if ($article['article_rule_daynum'] <= $day_click) {
					$givecredit = 0;
					$givemoney = 0;
				}
			}

			$toto = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('ewei_shop_article_share') . ' WHERE aid=:aid and share_user=:click_user and click_user=:share_user and uniacid=:uniacid ', array(':aid' => $article['id'], ':share_user' => $shareid, ':click_user' => $myid, ':uniacid' => $_W['uniacid']));

			if (!empty($toto)) {
				return NULL;
			}

			if ((0 < $article['article_rule_credittotal']) || (0 < $article['article_rule_moneytotal'])) {
				$creditlast = 0;
				$moneylast = 0;
				$firstreads = pdo_fetchcolumn('select count(distinct click_user) from ' . tablename('ewei_shop_article_share') . ' where aid=:aid and uniacid=:uniacid limit 1', array(':aid' => $article['id'], ':uniacid' => $_W['uniacid']));
				$allreads = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_article_share') . ' where aid=:aid and uniacid=:uniacid limit 1', array(':aid' => $article['id'], ':uniacid' => $_W['uniacid']));
				$secreads = $allreads - $firstreads;

				if (0 < $article['article_rule_credittotal']) {
					if (!empty($article['article_advance'])) {
						$creditlast = $article['article_rule_credittotal'] - (($firstreads + ($article['article_virtualadd'] ? $article['article_readnum_v'] : 0)) * $article['article_rule_creditm']) - ($secreads * $article['article_rule_creditm2']);
					}
					else {
						$creditout = pdo_fetchcolumn('select sum(add_credit) from ' . tablename('ewei_shop_article_share') . ' where aid=:aid and uniacid=:uniacid limit 1', array(':aid' => $article['id'], ':uniacid' => $_W['uniacid']));
						$creditlast = $article['article_rule_credittotal'] - $creditout;
					}
				}

				if (0 < $article['article_rule_moneytotal']) {
					if (!empty($article['article_advance'])) {
						$moneylast = $article['article_rule_moneytotal'] - (($firstreads + ($article['article_virtualadd'] ? $article['article_readnum_v'] : 0)) * $article['article_rule_moneym']) - ($secreads * $article['article_rule_moneym2']);
					}
					else {
						$moneyout = pdo_fetchcolumn('select sum(add_money) from ' . tablename('ewei_shop_article_share') . ' where aid=:aid and uniacid=:uniacid limit 1', array(':aid' => $article['id'], ':uniacid' => $_W['uniacid']));
						$moneylast = $article['article_rule_moneytotal'] - $moneyout;
					}
				}

				($creditlast <= 0) && ($creditlast = 0);
				($moneylast <= 0) && ($moneylast = 0);

				if ($creditlast <= 0) {
					$givecredit = 0;
				}

				if ($moneylast <= 0) {
					$givemoney = 0;
				}
			}

			$insert = array('aid' => $article['id'], 'share_user' => $shareid, 'click_user' => $myid, 'click_date' => time(), 'add_credit' => $givecredit, 'add_money' => $givemoney, 'uniacid' => $_W['uniacid']);
			pdo_insert('ewei_shop_article_share', $insert);

			if (0 < $givecredit) {
				m('member')->setCredit($profile['openid'], 'credit1', $givecredit, array(0, $shopset['name'] . ' 文章营销奖励积分'));
			}

			if (0 < $givemoney) {
				m('member')->setCredit($profile['openid'], 'credit2', $givemoney, array(0, $shopset['name'] . ' 文章营销奖励余额'));
			}

			if ((0 < $givecredit) || (0 < $givemoney)) {
				$article_sys = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_article_sys') . ' WHERE uniacid=:uniacid limit 1 ', array(':uniacid' => $_W['uniacid']));
				$detailurl = mobileUrl('member', NULL, true);
				$p = '';

				if (0 < $givecredit) {
					$p .= $givecredit . '个积分、';
				}

				if (0 < $givemoney) {
					$p .= $givemoney . '元余额';
				}

				$msg = array(
					'first'    => array('value' => '您的奖励已到帐！', 'color' => '#4a5077'),
					'keyword1' => array('title' => '任务名称', 'value' => '分享得奖励', 'color' => '#4a5077'),
					'keyword2' => array('title' => '通知类型', 'value' => '用户通过您的分享进入文章《' . $article['article_title'] . '》，系统奖励您' . $p . '。', 'color' => '#4a5077'),
					'remark'   => array('value' => '奖励已发放成功，请到会员中心查看。', 'color' => '#4a5077')
					);

				if (!empty($article_sys['article_message'])) {
					m('message')->sendTplNotice($profile['openid'], $article_sys['article_message'], $msg, $detailurl);
				}
				else {
					m('message')->sendCustomNotice($profile['openid'], $msg, $detailurl);
				}
			}
		}

		public function mid_replace($content)
		{
			global $_GPC;
			preg_match_all('/href\\=["|\\\'](.*?)["|\\\']/is', $content, $links);

			foreach ($links[1] as $key => $lnk) {
				$newlnk = $this->href_replace($lnk);
				$content = str_replace($links[0][$key], 'href="' . $newlnk . '"', $content);
			}

			return $content;
		}

		public function href_replace($lnk)
		{
			global $_GPC;
			$newlnk = $lnk;
			if (strexists($lnk, 'ewei_shop') && !strexists($lnk, '&mid')) {
				if (strexists($lnk, '?')) {
					$newlnk = $lnk . '&mid=' . intval($_GPC['mid']);
				}
				else {
					$newlnk = $lnk . '?mid=' . intval($_GPC['mid']);
				}
			}

			return $newlnk;
		}

		public function perms()
		{
			return array(
	'article' => array(
		'text'     => $this->getName(),
		'isplugin' => true,
		'child'    => array(
			'cate' => array('text' => '分类设置', 'addcate' => '添加分类-log', 'editcate' => '编辑分类-log', 'delcate' => '删除分类-log'),
			'page' => array('text' => '文章设置', 'add' => '添加文章-log', 'edit' => '修改文章-log', 'delete' => '删除文章-log', 'showdata' => '查看数据统计', 'otherset' => '其他设置', 'report' => '举报记录')
			)
		)
	);
		}
	//结束文章管理
	}
}

?>
