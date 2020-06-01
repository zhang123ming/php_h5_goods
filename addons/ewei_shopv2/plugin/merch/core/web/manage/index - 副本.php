<?php

//dezend by http://www.yunlu99.com/ QQ:270656184
require EWEI_SHOPV2_PLUGIN . 'merch/core/inc/page_merch.php';
class Index_EweiShopV2Page extends MerchWebPage
{
	public function main()
	{
		global $_W;
		
		global $_GPC;
		$user = pdo_fetch('select `id`,`logo`,`merchname`,`desc` from ' . tablename('ewei_shop_merch_user') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $_W['uniaccount']['merchid'], ':uniacid' => $_W['uniacid']));
		$order_sql = 'select id,ordersn,createtime,address,price,invoicename from ' . tablename('ewei_shop_order') . ' where uniacid = :uniacid and merchid=:merchid and isparent=0 and deleted=0 AND ( status = 1 or (status=0 and paytype=3) ) ORDER BY createtime ASC LIMIT 20';
		$order = pdo_fetchall($order_sql, array(':uniacid' => $_W['uniacid'], ':merchid' => $_W['merchid']));
		foreach ($order as &$value) {
			$value['address'] = iunserializer($value['address']);
		}
		unset($value);
		$order_ok = $order;
		$merchid = $_W['merchid'];
		$url = mobileUrl('merch', array('merchid' => $merchid), true);
		$qrcode = m('qrcode')->createQrcode($url);

		$xcxcode = $this->get_wxapp_store_qrcode();

		include $this->template('shop/index');
	}
	
	//小程序码生成开始
	public function get_wxapp_store_qrcode(){
		global $_W;
		global $_GPC;
		// echo "<pre>";
		// var_dump($_W['merchid']);die;
		//生成二维码begin
		$merchid = $_W['merchid'];
		//var_dump($shop_id);die;
		$access_token = $this->access_token();
	    $path="pages/changce/merch/detail?id=".$merchid;
	    //var_dump($path);die;
	    //$path="pages/changce/merch/detail?id=162";
	    $width=430;
		$post_data = array(
			'path' =>$path , 
			'width'=>$width,
		);
		// $post_data='{"path":"'.$path.'","width":'.$width.'}';
	    //var_dump($post_data);die;
	    $url="https://api.weixin.qq.com/wxa/getwxacode?access_token=".$access_token;
	    //适用于需要的码数量较少的业务场景
	    

	    $result=$this->api_notice_increment($url,json_encode($post_data));
	    //var_dump($result);die;
	    $url = ATTACHMENT_ROOT.'images/mini_qrcode_'.$merchid.'.png';
		$re = file_put_contents($url, $result);
		$url = $_W['attachurl'].'images/mini_qrcode_'.$merchid.'.png';

	    return $url;
	    //生成二维码end
	}
	public function quit()
	{
		global $_W;
		global $_GPC;
		isetcookie('__merch_' . $_W['uniacid'] . '_session', -7 * 86400);
		isetcookie('__uniacid', -7 * 86400);
		isetcookie('__acid', -7 * 86400);
		unset($_SESSION['__merch_uniacid']);
		header('location: ' . merchUrl('login') . '&i=' . $_W['uniacid']);
		exit;
	}
	public function updatepassword()
	{
		global $_W;
		global $_GPC;
		$no_left = true;
		if ($_W['ispost']) {
			$password = trim($_GPC['password']);
			$newpassword = trim($_GPC['newpassword']);
			$surenewpassword = trim($_GPC['surenewpassword']);
			strlen($newpassword) < 6 && show_json(0, '密码至少是6位!');
			$newpassword != $surenewpassword && show_json(0, '两次输入密码不一致!');
			$item = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_merch_account') . ' WHERE id=:id AND uniacid=:uniacid AND merchid=:merchid', array(':id' => $_W['uniaccount']['id'], ':uniacid' => $_W['uniacid'], ':merchid' => $_W['merchid']));
			$item['pwd'] != md5($password . $item['salt']) && show_json(0, '原密码输入不正确!');
			$date = array('salt' => random(8));
			$newpassword = md5($newpassword . $date['salt']);
			$date['pwd'] = $newpassword;
			pdo_update('ewei_shop_merch_account', $date, array('id' => $_W['uniaccount']['id'], 'uniacid' => $_W['uniacid'], 'merchid' => $_W['merchid']));
			show_json(1);
		}
		include $this->template();
	}
	public function searchlist()
	{
		global $_W;
		global $_GPC;
		$return_arr = array();
		$menu = m('system')->getSubMenus(true, true);
		$keyword = trim($_GPC['keyword']);
		if (empty($keyword) || empty($menu)) {
			show_json(1, array('menu' => $return_arr));
		}
		foreach ($menu as $index => $item) {
			if (strexists($item['title'], $keyword) || strexists($item['desc'], $keyword) || strexists($item['keywords'], $keyword) || strexists($item['topsubtitle'], $keyword)) {
				if (m('system')->cv($item['route'])) {
					$return_arr[] = $item;
				}
			}
		}
		show_json(1, array('menu' => $return_arr));
	}
	public function search()
	{
		global $_W;
		global $_GPC;
		$keyword = trim($_GPC['keyword']);
		$list = array();
		$history = $_GPC['merch_history_search'];
		if (empty($history)) {
			$history = array();
		} else {
			$history = htmlspecialchars_decode($history);
			$history = json_decode($history, true);
		}
		if (!empty($keyword)) {
			$submenu = m('system')->getSubMenus(true, true);
			if (!empty($submenu)) {
				foreach ($submenu as $index => $submenu_item) {
					$top = $submenu_item['top'];
					if (strexists($submenu_item['title'], $keyword) || strexists($submenu_item['desc'], $keyword) || strexists($submenu_item['keywords'], $keyword) || strexists($submenu_item['topsubtitle'], $keyword)) {
						if (m('system')->cv($submenu_item['route'])) {
							if (!is_array($list[$top])) {
								$title = !empty($submenu_item['topsubtitle']) ? $submenu_item['topsubtitle'] : $submenu_item['title'];
								if (strexists($title, $keyword)) {
									$title = str_replace($keyword, '<b>' . $keyword . '</b>', $title);
								}
								$list[$top] = array('title' => $title, 'items' => array());
							}
							if (strexists($submenu_item['title'], $keyword)) {
								$submenu_item['title'] = str_replace($keyword, '<b>' . $keyword . '</b>', $submenu_item['title']);
							}
							if (strexists($submenu_item['desc'], $keyword)) {
								$submenu_item['desc'] = str_replace($keyword, '<b>' . $keyword . '</b>', $submenu_item['desc']);
							}
							$list[$top]['items'][] = $submenu_item;
						}
					}
				}
			}
			if (empty($history)) {
				$history_new = array($keyword);
			} else {
				$history_new = $history;
				foreach ($history_new as $index => $key) {
					if ($key == $keyword) {
						unset($history_new[$index]);
					}
				}
				$history_new = array_merge(array($keyword), $history_new);
				$history_new = array_slice($history_new, 0, 20);
			}
			isetcookie('merch_history_search', json_encode($history_new), 7 * 86400);
			$history = $history_new;
		}
		include $this->template();
	}
	public function clearhistory()
	{
		global $_W;
		global $_GPC;
		if ($_W['ispost']) {
			$type = intval($_GPC['type']);
			if (empty($type)) {
				isetcookie('merch_history_url', '', -7 * 86400);
			} else {
				isetcookie('merch_history_search', '', -7 * 86400);
			}
		}
		show_json(1);
	}
	public function switchversion()
	{
		global $_W;
		global $_GPC;
		$route = trim($_GPC['route']);
		$id = intval($_GPC['id']);
		$set = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_version') . ' WHERE uid=:uid AND `type`=1', array(':uid' => $_W['merchuid']));
		$data = array('type' => 1, 'version' => !empty($_W['shopversion']) ? 0 : 1);
		if (empty($set)) {
			$data['uid'] = $_W['merchuid'];
			pdo_insert('ewei_shop_version', $data);
		} else {
			pdo_update('ewei_shop_version', $data, array('id' => $set['id']));
		}
		$params = array();
		if (!empty($id)) {
			$params['id'] = $id;
		}
		load()->model('cache');
		cache_clean();
		cache_build_template();
		header('location: ' . webUrl($route, $params));
		exit;
	}

	public function access_token()
    {
        global $_W;
		global $_GPC;

		$appid = $_W['shopset']['app']['appid']; 
        $secret = $_W['shopset']['app']['secret'];
        //var_dump($appid);die;
        //var_dump($secret);die;

        //$tokenUrl="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$secret;
        $tokenUrl="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=wx9abac05ef67aacee&secret=1c25803e6b07e9ce48bb64fa07900f47";
        
    	$getArr=array();
    	$tokenArr=json_decode($this->send_post($tokenUrl,$getArr,"GET"));
    	$access_token=$tokenArr->access_token;
    	//var_dump($access_token);die;
    	return $access_token;

    }

    public function send_post($url, $post_data,$method='POST') {
    $postdata = http_build_query($post_data);
    $options = array(
      	'http' => array(
        'method' => $method, //or GET
        'header' => 'Content-type:application/x-www-form-urlencoded',
        'content' => $postdata,
        'timeout' => 15 * 60 // 超时时间（单位:s）
      )
    );

    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    return $result;
	
  }

    public function api_notice_increment($url, $data){

    $ch = curl_init();//创建一个新cURL资源
    $header = "Accept-Charset: utf-8";
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $tmpInfo = curl_exec($ch);//抓取URL并把它传递给浏览器
    if (curl_errno($ch)) {
      return false;
    }else{
       //var_dump($tmpInfo);die;
      return $tmpInfo;
    }
  }  

//小程序码生成结束
}
  	

?>