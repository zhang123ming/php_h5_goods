<?php
if (!(defined('IN_IA'))) {
	exit('Access Denied');
}

require EWEI_SHOPV2_PLUGIN . 'app/core/page_mobile.php';
class User_EweiShopV2Page extends AppMobilePage
{
	public function main()
	{
		global $_GPC;
		global $_W;

		$uniacid = $_W['uniacid'];
		$id = $_GPC['id'];

		if(!empty($id)){
			$member = pdo_fetchall('select * from ' . tablename('ewei_shop_sns_member') . ' where uniacid=:uniacid and id=:id and isblack = 0 ', array(':uniacid' => $uniacid,':id'=>$id));
			$openid = $member[0]['openid'];

		} else {
			$openid = $_W['openid'];
			$member = pdo_fetchall('select * from ' . tablename('ewei_shop_sns_member') . ' where uniacid=:uniacid and openid=:openid and isblack = 0 ', array(':uniacid' => $uniacid,':openid' => $openid));

		}

		$userinfo = pdo_fetchall('select * from ' . tablename('ewei_shop_member') . ' where uniacid=:uniacid and openid=:openid  ', array(':uniacid' => $uniacid,':openid' => $openid));
		if(empty($userinfo)){
			$userinfo = pdo_fetchall('select avatar,nickname from ' . tablename('ewei_shop_sns_post') . ' where uniacid=:uniacid and openid=:openid limit 1 ', array(':uniacid' => $uniacid,':openid' => $openid));
		}

		$level = pdo_fetchall('select * from ' . tablename('ewei_shop_sns_level') . ' where uniacid=:uniacid and id=:id and enabled = 1 ', array(':uniacid' => $uniacid,':id' => $member[0]['level']));
		if(empty($level)){
			$level['levelname'] = "社区粉丝";
			$level['color'] = "#333";
			$level['bg'] = "#999";
		}

		$boardcount = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_sns_board_follow') . ' where uniacid=:uniacid and openid=:openid', array(':uniacid' => $_W['uniacid'], ':openid' => $openid));
		$postcount = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_sns_post') . ' where uniacid=:uniacid and openid=:openid and pid=0 and deleted = 0 and checked=1', array(':uniacid' => $_W['uniacid'], ':openid' => $openid));
		
		$boards = pdo_fetchall('select b.id,b.logo,b.title from ' . tablename('ewei_shop_sns_board_follow') . ' f ' . ' left join ' . tablename('ewei_shop_sns_board') . ' b on f.bid = b.id ' . '   where f.uniacid=:uniacid and f.openid=:openid limit 5', array(':uniacid' => $_W['uniacid'], ':openid' => $openid));
		$boards = set_medias($boards, 'logo');
		$followcount = count($boards);
		$count['boardcount'] = $boardcount;
		$count['postcount'] = $postcount;
		$count['fllowcount'] = $followcount;
		$count['openid'] = $_W['openid'];
		$count['uopenid'] = $openid;

		$posts = pdo_fetchall('select p.id,p.images,p.title ,p.views, b.title as boardtitle,b.logo as boardlogo from ' . tablename('ewei_shop_sns_post') . ' p ' . ' left join ' . tablename('ewei_shop_sns_board') . ' b on p.bid = b.id ' . '   where p.uniacid=:uniacid and p.openid=:openid and pid=0 and deleted=0 and checked=1 order by createtime desc limit 5', array(':uniacid' => $_W['uniacid'], ':openid' => $openid));
		foreach ($posts as &$r) {
			$images = iunserializer($r['images']);
			$thumb = '';
			if (is_array($images) && !empty($images)) {
				$thumb = $images[0];
			}

			if (empty($thumb)) {
				$thumb = $r['boardlogo'];
			}

			$r['thumb'] = tomedia($thumb);
		}
		unset($r);

		if ($openid == $_W['openid']) {
			$replycount = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_sns_post') . ' where uniacid=:uniacid and openid=:openid and pid>0 and deleted = 0 and checked=1', array(':uniacid' => $_W['uniacid'], ':openid' => $openid));
			$count['replycount'] = $replycount;
			$replys = pdo_fetchall("select p.id, p.content, p.views,\r\n                  parent.id as parentid, \r\n                  parent.nickname as parentnickname,parent.title as parenttitle ,\r\n                  rparent.nickname as rparentnickname\r\n                  from " . tablename('ewei_shop_sns_post') . ' p ' . ' left join ' . tablename('ewei_shop_sns_post') . ' parent on p.pid = parent.id ' . ' left join ' . tablename('ewei_shop_sns_post') . ' rparent on p.rpid = rparent.id ' . '   where p.uniacid=:uniacid and p.openid=:openid and p.pid>0 and p.deleted=0 and p.checked=1 order by p.createtime desc limit 5', array(':uniacid' => $_W['uniacid'], ':openid' => $openid));

			foreach ($replys as &$r) {
				$parentnickname = $r['rparentnickname'];

				if (empty($parentnickname)) {
					$parentnickname = $r['parentnickname'];
				}

				$r['parentnickname'] = $parentnickname;
			}

			unset($r);
		}

		app_json(array('member' => $member,'userinfo' => $userinfo,'count' => $count,'posts' => $posts,'boards' => $boards,'replys' => $replys,'level' => $level));
	}

	public function mytheme ()
	{
		global $_GPC;
		global $_W;
		$openid = $_W['openid'];
		$uniacid = $_W['uniacid'];

		$post = pdo_fetchall('select id,isbest,isboardbest,avatar,images,title,nickname,content,createtime from ' . tablename('ewei_shop_sns_post') . ' where uniacid=:uniacid and openid=:openid and pid = 0 and deleted = 0 order by createtime desc', array(':uniacid' => $uniacid,':openid' => $openid));
		foreach ($post as $key => $val) {
			if(!empty($post[$key]['images'])){
				$i = array();
				$images = iunserializer($post[$key]['images']);
				foreach($images as $k => $v){
					$i[$k] = tomedia($v);
				}
				$post[$key]['images'] = $i;
			}
            $post[$key]['createtime'] = date("Y-m-d H:i:s", $val['createtime']);
            $post[$key]['postcount'] = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_sns_post') . ' where uniacid=:uniacid and pid=:pid and deleted=0 and checked=1 ', array(':pid' => $post[$key]['id'],'uniacid' => $uniacid));
            $post[$key]['followcount'] = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_sns_like') . ' where uniacid=:uniacid and pid=:pid ', array(':pid' => $post[$key]['id'],':uniacid' => $uniacid));
        }

		app_json(array('post' => $post));
	}

	public function myreplys ()
	{
		global $_GPC;
		global $_W;
		$openid = $_W['openid'];
		$uniacid = $_W['uniacid'];

		$replys = pdo_fetchall("select p.bid,p.id, p.content, p.views,\r\n                  parent.id as parentid, \r\n                  parent.nickname as parentnickname,parent.title as parenttitle ,\r\n                  rparent.nickname as rparentnickname\r\n                  from " . tablename('ewei_shop_sns_post') . ' p ' . ' left join ' . tablename('ewei_shop_sns_post') . ' parent on p.pid = parent.id ' . ' left join ' . tablename('ewei_shop_sns_post') . ' rparent on p.rpid = rparent.id ' . '   where p.uniacid=:uniacid and p.openid=:openid and p.pid>0 and p.deleted=0 and p.checked=1 order by p.createtime desc limit 5', array(':uniacid' => $_W['uniacid'], ':openid' => $openid));

		foreach ($replys as $k => $v) {
			$res = 	pdo_fetch('select logo,title from ' . tablename('ewei_shop_sns_board') . ' where uniacid=:uniacid and id=:id and status = 1', array(':uniacid' => $uniacid,':id' => $v['bid']));
			$replys[$k]['logo'] = $res['logo'];
			$replys[$k]['title'] = $res['title'];
		}

		foreach ($replys as &$r) {
			$parentnickname = $r['rparentnickname'];

			if (empty($parentnickname)) {
				$parentnickname = $r['parentnickname'];
			}

			$r['parentnickname'] = $parentnickname;

		}

		unset($r);
		
		app_json(array('replys' => $replys));
	}

	public function sign()
	{
		global $_GPC;
		global $_W;
		
		$sign = $_GPC['sval'];
		pdo_update('ewei_shop_sns_member', array('sign' => $sign), array('openid' => $_W['openid'], 'uniacid' => $_W['uniacid']));

		show_json(1);
	}

	public function delcomment ()
	{
		global $_GPC;
		global $_W;

		$id = $_GPC['id'];
		if(!empty($id)){
			$del = pdo_delete('ewei_shop_sns_post', array('uniacid' => $_W['uniacid'], 'openid' => $_W['openid'], 'id' => $id));
		}
		
		app_json(array('del' => $del));
	}
}