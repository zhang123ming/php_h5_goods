<?php
if (!(defined('IN_IA'))) {
	exit('Access Denied');
}

require EWEI_SHOPV2_PLUGIN . 'app/core/page_mobile.php';
class Board_EweiShopV2Page extends AppMobilePage
{
	public function index()
	{
		global $_GPC;
		global $_W;

		$id = $_GPC['id'];
		$uniacid = $_W['uniacid'];
		$openid = $_W['openid'];
		$board = pdo_fetchall('select id,title,logo,banner from ' . tablename('ewei_shop_sns_board') . ' where uniacid=:uniacid and id=:id ', array(':uniacid' => $uniacid,':id' => $id));
		$follow = pdo_fetchall('select * from ' . tablename('ewei_shop_sns_board_follow') . ' where uniacid=:uniacid and bid=:bid and openid=:openid', array(':uniacid' => $_W['uniacid'],'bid' => $id,'openid' => $openid));
		$board[0]['followcount'] = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_sns_board_follow') . ' where uniacid=:uniacid and bid=:bid ', array(':bid' => $board[0]['id'],':uniacid' => $uniacid));
		$board[0]['postcount'] = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_sns_post') . ' where uniacid=:uniacid and bid=:bid and pid = 0 and deleted=0 and checked=1 ', array(':bid' => $board[0]['id'],'uniacid' => $uniacid));
		$top = pdo_fetchall('select id,bid,openid,istop,title from ' . tablename('ewei_shop_sns_post') . ' where uniacid=:uniacid and istop = 1 and pid = 0 and deleted = 0 ', array(':uniacid' => $uniacid));
		$post = pdo_fetchall('select id,isbest,istop,isboardtop,isboardbest,avatar,title,images,nickname,content,createtime from ' . tablename('ewei_shop_sns_post') . ' where uniacid=:uniacid and bid=:bid and pid = 0 and deleted = 0 order by createtime desc', array(':uniacid' => $uniacid,':bid' => $id));
		$manager = pdo_fetchall('select bid from ' . tablename('ewei_shop_sns_manager') . ' where uniacid=:uniacid and openid=:openid and bid=:bid ', array(':uniacid' => $uniacid,':openid' => $post[0]['openid'],':bid' => $board[0]['id']));
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

		app_json(array('post' => $post,'board' => $board,'follow' => $follow,'boardtop' => $top));
	}

	public function detail()
	{
		global $_GPC;
		global $_W;

		$id = $_GPC['id'];
		$uniacid = $_W['uniacid'];

		$post = pdo_fetchall('select id,bid,openid,isbest,isboardtop,istop,isboardbest,images,views,avatar,title,nickname,content,createtime from ' . tablename('ewei_shop_sns_post') . ' where uniacid=:uniacid and id=:id and pid = 0 and deleted = 0 ', array(':uniacid' => $uniacid,':id' => $id));

		$time = date("Y-m-d h:i:s",$post[0]['createtime']);
		$posts['post'] = $post[0];
		$posts['time'] = $time;

		if(!empty($post[0]['images'])){
			$images = iunserializer($post[0]['images']);
			foreach($images as $k => $v){
				$i[$k] = tomedia($v);
			}
			$posts['images'] = $i;
		}


		$set1 = pdo_fetch('select * from ' . tablename('ewei_shop_sysset') . ' where uniacid=:uniacid limit 1', array(':uniacid' => $uniacid));
		$sets = iunserializer($set1);
		$plugins = iunserializer($sets['plugins']);
		$isadmin = in_array($_W['openid'],$plugins['sns']);

		$board = pdo_fetchall('select title,id,banner from ' . tablename('ewei_shop_sns_board') . ' where uniacid=:uniacid and id=:id ', array(':uniacid' => $uniacid,':id' => $post[0]['bid']));
		$posts['board'] = $board[0];
		pdo_update('ewei_shop_sns_post', array('views' => $post[0]['views'] + 1), array('id' => $post[0]['id']));
		$posts['post']['views'] = $post[0]['views'] + 1;
		$user = pdo_fetchall('select id,level,sign from ' . tablename('ewei_shop_sns_member') . ' where uniacid=:uniacid and openid=:openid and isblack = 0 ', array(':uniacid' => $uniacid,':openid' => $post[0]['openid']));
		$posts['user'] = $user[0];
		$level = pdo_fetchall('select * from ' . tablename('ewei_shop_sns_level') . ' where uniacid=:uniacid and id=:id and enabled = 1 ', array(':uniacid' => $uniacid,':id' => $user[0]['level']));
		if(!empty($level)){
			$posts['level'] = $level[0];
		} else {
			$posts['level']['levelname'] = "社区粉丝";
			$posts['level']['color'] = "#333";
			$posts['level']['bg'] = "#999";
		}

		$manager = pdo_fetchall('select bid from ' . tablename('ewei_shop_sns_manage') . ' where uniacid=:uniacid and openid=:openid and bid=:bid ', array(':uniacid' => $uniacid,':openid' => $_W['openid'],':bid' => $board[0]['id']));
		$posts['manager'] = $manager[0];

		$boardfollow = pdo_fetchall('select * from ' . tablename('ewei_shop_sns_board_follow') . ' where uniacid=:uniacid and openid=:openid and bid=:bid', array(':uniacid' => $uniacid,':openid' => $post[0]['openid'],':bid' => $board[0]['id']));
		$posts['boardfollow'] = $boardfollow;

		$like = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_sns_like') . ' where pid=:pid and uniacid=:uniacid limit 1', array(':pid' => $post[0]['id'],':uniacid'=>$_W['uniacid']));
		$replycount = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_sns_post') . ' where pid=:pid and deleted=0 and checked=1 limit 1', array(':pid' => $post[0]['id']));
		$isgood = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_sns_like') . ' where uniacid=:uniacid and pid=:pid and openid=:openid  limit 1', array(':uniacid' => $_W['uniacid'], ':pid' => $post[0]['id'], ':openid' => $_W['openid']));

		$comment = pdo_fetchall('select id,bid,pid,rpid,openid,images,nickname,content,createtime,avatar from ' . tablename('ewei_shop_sns_post') . ' where uniacid=:uniacid and pid=:pid and checked = 1  order by createtime desc', array(':uniacid' => $_W['uniacid'], ':pid' => $post[0]['id']));
		foreach ($comment as $k => $v){
			if(!empty($comment[$k]['images'])){
				$i = array();
				$images = iunserializer($comment[$k]['images']);
				foreach($images as $key => $value){
					$i[$key] = tomedia($value);
				}
				$comment[$k]['images'] = $i;
			}
			$comment[$k]['like'] = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_sns_like') . ' where pid=:pid and uniacid=:uniacid limit 1', array(':pid' => $comment[$k]['id'],':uniacid' => $_W['uniacid']));
			$comment[$k]['createtime'] = date("Y-m-d h:i:s",$comment[$k]['createtime']);
			$level = pdo_fetchall('select * from ' . tablename('ewei_shop_sns_level') . ' where uniacid=:uniacid and openid=:openid and enabled = 1 ', array(':uniacid' => $uniacid,':openid' => $comment[$k]['openid']));
			if(!empty($level)){
				$comment[$k]['levelname'] = $level[0]['levelname'];
				$comment[$k]['color'] = $level[0]['color'];
				$comment[$k]['bg'] = $level[0]['bg'];
			} else {
				$comment[$k]['levelname'] = "社区粉丝";
				$comment[$k]['color'] = "#333";
				$comment[$k]['bg'] = "#999";
			}
			$m = pdo_fetchall('select bid from ' . tablename('ewei_shop_sns_manage') . ' where uniacid=:uniacid and openid=:openid and bid=:bid ', array(':uniacid' => $uniacid,':openid' => $comment[$k]['openid'],':bid' => $board[0]['id']));
			if(!empty($m)){
				$comment[$k]['ismanager'] = $m[0]['bid'];
			}
			if(!empty($comment[$k]['rpid'])){
				$reply = pdo_fetchall('select nickname,content from ' . tablename('ewei_shop_sns_post') . ' where uniacid=:uniacid and id=:id and deleted = 0 ', array(':uniacid' => $uniacid,':id' => $comment[$k]['rpid']));
				$comment[$k]['repname'] = $reply[0]['nickname'];
				$comment[$k]['repcontent'] = $reply[0]['content'];
			}
		}

		app_json(array('posts' => $posts,'like' => $like,'replycount' => $replycount,'complain' => $complain,'comment' => $comment,'manager' => $manager,'isadmin' => $isadmin));
	}

	public function best()
	{
		global $_GPC;
		global $_W;

		$id = $_GPC['id'];
		$uniacid = $_W['uniacid'];
		$boardbest = pdo_fetchall('select id,bid,openid,isbest,images,avatar,title,nickname,content,createtime from ' . tablename('ewei_shop_sns_post') . ' where uniacid=:uniacid and isbest = 1 and pid = 0 and deleted = 0 ', array(':uniacid' => $uniacid));
		$post = pdo_fetchall('select id,bid,openid,isbest,isboardbest,images,avatar,title,nickname,content,createtime from ' . tablename('ewei_shop_sns_post') . ' where uniacid=:uniacid and bid=:bid and isboardbest = 1 and  pid = 0 and deleted = 0 ', array(':uniacid' => $uniacid,':bid' => $id));
		foreach ($boardbest as $k => $v) {
            $boardbest[$k]['createtime'] = date("Y-m-d H:i:s", $v['createtime']);
			if(!empty($boardbest[$k]['images'])){
				$i = array();
				$images = iunserializer($boardbest[$k]['images']);
				foreach($images as $key => $value){
					$i[$key] = tomedia($value);
				}
				$boardbest[$k]['images'] = $i;
			}
        }
		foreach ($post as $k => $v) {
            $post[$k]['createtime'] = date("Y-m-d H:i:s", $v['createtime']);

			if(!empty($post[$k]['images'])){
				$i = array();
				$images = iunserializer($post[$k]['images']);
				foreach($images as $key => $value){
					$i[$key] = tomedia($value);
				}
				$post[$k]['images'] = $i;
			}
        }

		app_json(array('post' => $post,'boardbest' => $boardbest));

	}

	public function relate ()
	{
		global $_GPC;
		global $_W;

		$openid = $_W['openid'];
		$uniacid = $_W['uniacid'];

		$bid = pdo_fetchall('select bid from ' . tablename('ewei_shop_sns_board_follow') . ' where uniacid=:uniacid and openid=:openid  ', array(':uniacid' => $uniacid,':openid' => $openid));

		if(!empty($bid)){
			foreach($bid as $k => $v){
				$b = pdo_fetchall('select * from ' . tablename('ewei_shop_sns_board') . ' where uniacid=:uniacid and id=:id ', array(':uniacid' => $uniacid,':id' => $v['bid']));
				$board[$k] = $b[0];
				$board[$k]['followcount'] = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_sns_board_follow') . ' where uniacid=:uniacid and bid=:bid ', array(':bid' => $v['bid'],':uniacid' => $uniacid));
				$board[$k]['postcount'] = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_sns_post') . ' where uniacid=:uniacid and bid=:bid and pid = 0 and deleted=0 and checked=1 ', array(':bid' => $v['bid'],'uniacid' => $uniacid));
			}
		}	
		// var_dump($board);
		app_json(array('board' => $board));
	}

	public function publish (){
		global $_GPC;
		global $_W;

		$openid = $_W['openid'];
		$uniacid = $_W['uniacid'];
		$userinfo = pdo_fetchall('select * from ' . tablename('ewei_shop_member') . ' where uniacid=:uniacid and openid=:openid  ', array(':uniacid' => $uniacid,':openid' => $openid));

		$data['openid'] = $openid;
		if(!empty($userinfo)){
			$data['avatar'] = $userinfo[0]['avatar'];
			$data['nickname'] = $userinfo[0]['nickname'];
			$data['createtime'] = time();
			$data['replytime'] = $data['createtime'];
			$data['checked'] = 1;
			$data['uniacid'] = $uniacid;
			if(!empty($_GPC['images'])){
				$data['images'] = serialize($_GPC['images']);
			}
			$data['bid'] = $_GPC['values']['bid'];
			$data['title'] = $_GPC['values']['title'];
			$data['content'] = $_GPC['values']['contents'];
			$res = pdo_insert('ewei_shop_sns_post', $data);
		}

		
				
		
		app_json(array('res' => $res,'bid' => $_GPC['values']['bid']));
		
	}

	public function comment(){
		global $_GPC;
		global $_W;

		$openid = $_W['openid'];
		$uniacid = $_W['uniacid'];
		$userinfo = pdo_fetchall('select * from ' . tablename('ewei_shop_member') . ' where uniacid=:uniacid and openid=:openid  ', array(':uniacid' => $uniacid,':openid' => $openid));
		$data['openid'] = $openid;
		if(!empty($userinfo)){
			$data['avatar'] = $userinfo[0]['avatar'];
			$data['nickname'] = $userinfo[0]['nickname'];
			$data['createtime'] = time();
			$data['checked'] = 1;
			$data['uniacid'] = $uniacid;
		}

		$res = array();
		if(!empty($_GPC['images'])){
			$data['images'] = serialize($_GPC['images']);
		}
		if(!empty($_GPC['values']) && !empty($_GPC['values']['contents'])){
			if(!empty($_GPC['values']['rpid'])){
				$data['rpid'] = $_GPC['values']['rpid'];
			}
			$data['pid'] = $_GPC['values']['id'];
			$data['bid'] = $_GPC['values']['bid'];
			$data['content'] = $_GPC['values']['contents'];
			$res = pdo_insert('ewei_shop_sns_post', $data);
		} else {
			return "回复内容不能为空";
		}

		app_json(array('res' => $res,'bid' => $_GPC['values']['id']));
	}

	public function changelike ()
	{
		global $_GPC;
		global $_W;

		$openid = $_W['openid'];
		$pid = $_GPC['id'];
		$postid = $_GPC['postid'];

		$live = pdo_fetchall('select * from ' . tablename('ewei_shop_sns_like') . ' where uniacid=:uniacid and pid=:pid and openid=:openid', array(':uniacid' => $_W['uniacid'],'pid' => $pid,'openid' => $openid));
		$board = pdo_fetchall('select bid from ' . tablename('ewei_shop_sns_post') . ' where uniacid=:uniacid and id=:id', array(':uniacid' => $_W['uniacid'],'id' => $pid));

		if(!empty($live)){
			pdo_delete('ewei_shop_sns_like', array('uniacid' => $_W['uniacid'], 'openid' => $openid, 'pid' => $pid));

		} else {

			$datas['openid'] = $openid;
			$datas['pid'] = $pid;
			$datas['uniacid'] = $_W['uniacid'];
			pdo_insert('ewei_shop_sns_like',$datas);
		}
		
		app_json(array('id' => $postid,'bid' => $board[0]['bid']));
	}

	public function changefollow()
	{
		global $_GPC;
		global $_W;

		$openid = $_W['openid'];
		$id = $_GPC['id'];

		$follow = pdo_fetchall('select * from ' . tablename('ewei_shop_sns_board_follow') . ' where uniacid=:uniacid and bid=:bid and openid=:openid', array(':uniacid' => $_W['uniacid'],'bid' => $id,'openid' => $openid));
		if(!empty($follow)){
			pdo_delete('ewei_shop_sns_board_follow', array('uniacid' => $_W['uniacid'], 'bid' => $id,'openid' => $openid));
		} else {
			$data['uniacid'] = $_W['uniacid'];
			$data['bid'] = $id;
			$data['openid'] = $openid;
			$data['createtime'] = time();
			pdo_insert('ewei_shop_sns_board_follow', $data);
		}
		app_json(array('id' => $id));
	}

	public function complain()
	{
		global $_GPC;
		global $_W;

		$cates = pdo_fetchall('select * from ' . tablename('ewei_shop_sns_complaincate') . ' where uniacid=:uniacid ', array(':uniacid' => $_W['uniacid']));

		app_json(array('cates' => $cates));
	}

	public function subcomplain()
	{
		global $_W;
		global $_GPC;

		$uniacid = $_W['uniacid'];
		$id = $_GPC['values']['pid'] ? $_GPC['values']['pid'] : $_GPC['values']['id'];//话题ID
		$type = $_GPC['values']['complaincate'];//分类ID
		$content = trim($_GPC['values']['content']);

		$post = pdo_fetchall('select openid,pid,id from ' . tablename('ewei_shop_sns_post') . ' where uniacid=:uniacid and id=:id and deleted = 0', array(':uniacid' => $uniacid,':id' => $id));

		if(!empty($post)){
			$data['uniacid'] = $uniacid;
			$data['type'] = $type;
			$data['postsid'] = $post[0]['id'];
			$data['defendant'] = $post[0]['openid'];
			$data['complainant'] = $_W['openid'];
			$data['complaint_text'] = $content;
			$data['complaint_type'] = $type;
			$data['createtime'] = time();
		}

		// $res = 1;
		$res = pdo_insert('ewei_shop_sns_complain', $data);
		app_json(array('res' => $res,'id' => $_GPC['values']['id'])); 
	}

	public function btnDelete()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];

		$bid = pdo_fetchall('select bid from ' . tablename('ewei_shop_sns_post') . ' where uniacid=:uniacid and id=:id and pid = 0 and deleted = 0', array(':uniacid' => $_W['uniacid'],':id' => $id));
		if(!empty($bid)){
			pdo_delete('ewei_shop_sns_post', array('uniacid' => $_W['uniacid'], 'id' => $id));
			pdo_delete('ewei_shop_sns_post', array('uniacid' => $_W['uniacid'], 'pid' => $id));
		}
		// var_dump($bid);
		app_json(array('bid' => $bid[0]['bid']));
	}

	public function btnBest()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];

		$bid = pdo_fetchall('select bid,isboardbest from ' . tablename('ewei_shop_sns_post') . ' where uniacid=:uniacid and id=:id and pid = 0 and deleted = 0', array(':uniacid' => $_W['uniacid'],':id' => $id));
		if ($bid[0]['isboardbest'] == 0 && !empty($bid)){
			$best = 1;
			pdo_update('ewei_shop_sns_post', array('isboardbest' => $best), array('id' => $id));
		} else {
			$best = 0;
			pdo_update('ewei_shop_sns_post', array('isboardbest' => $best), array('id' => $id));
		}

		app_json(array('id' => $id));
	}

	public function btntop()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];

		$top = pdo_fetch('select bid,isboardtop from ' . tablename('ewei_shop_sns_post') . ' where uniacid=:uniacid and id=:id and pid = 0 and deleted = 0', array(':uniacid' => $_W['uniacid'],':id' => $id));
		if ($top['isboardtop'] == 0 && !empty($top)){
			$top = 1;
			pdo_update('ewei_shop_sns_post', array('isboardtop' => $top), array('id' => $id));
		} else {
			$top = 0;
			pdo_update('ewei_shop_sns_post', array('isboardtop' => $top), array('id' => $id));
		}

		app_json(array('id' => $id));
	}

	public function delcomment()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$post = pdo_fetch('select pid,images from ' . tablename('ewei_shop_sns_post') . ' where uniacid=:uniacid and id=:id  and deleted = 0', array(':uniacid' => $_W['uniacid'],':id' => $id));
		if(!empty($post)){
			pdo_delete('ewei_shop_sns_post', array('uniacid' => $_W['uniacid'], 'id' => $id));
		}
		
		app_json(array('id' => $post['pid']));
	}

	public function btnalltop()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];

		$top = pdo_fetch('select bid,istop from ' . tablename('ewei_shop_sns_post') . ' where uniacid=:uniacid and id=:id and pid = 0 and deleted = 0', array(':uniacid' => $_W['uniacid'],':id' => $id));
		if ($top['istop'] == 0 && !empty($top)){
			$top = 1;
			pdo_update('ewei_shop_sns_post', array('istop' => $top), array('id' => $id));
		} else {
			$top = 0;
			pdo_update('ewei_shop_sns_post', array('istop' => $top), array('id' => $id));
		}

		app_json(array('id' => $id));
	}

	public function btnallBest()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];

		$bid = pdo_fetch('select bid,isbest from ' . tablename('ewei_shop_sns_post') . ' where uniacid=:uniacid and id=:id and pid = 0 and deleted = 0', array(':uniacid' => $_W['uniacid'],':id' => $id));
		if ($bid['isbest'] == 0 && !empty($bid)){
			$best = 1;
			pdo_update('ewei_shop_sns_post', array('isbest' => $best), array('id' => $id));
		} else {
			$best = 0;
			pdo_update('ewei_shop_sns_post', array('isbest' => $best), array('id' => $id));
		}

		app_json(array('id' => $id));
	}
}