<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}
require EWEI_SHOPV2_PLUGIN . 'app/core/page_mobile.php';
class Merch_EweiShopV2Page extends AppMobilePage
{

	public function setting()
	{
		global $_W;
		global $_GPC;

		if ($_W['setting']['remote']['type'] == ATTACH_FTP) {
			$_W['attachurl'] = $_W['attachurl_remote'] = $_W['setting']['remote']['ftp']['url'] . '/';
		} elseif ($_W['setting']['remote']['type'] == ATTACH_OSS) {
			$_W['attachurl'] = $_W['attachurl_remote'] = $_W['setting']['remote']['alioss']['url'] . '/';
		} elseif ($_W['setting']['remote']['type'] == ATTACH_QINIU) {
			$_W['attachurl'] = $_W['attachurl_remote'] = $_W['setting']['remote']['qiniu']['url'] . '/';
		} elseif ($_W['setting']['remote']['type'] == ATTACH_COS) {
			$_W['attachurl'] = $_W['attachurl_remote'] = $_W['setting']['remote']['cos']['url'] . '/';
		}

		
	}

	public function get_list() 
	{
		global $_W;
		global $_GPC;
		$data = array();
		$pindex = max(1, intval($_GPC['page']));
		$psize = 30;
		$lat = floatval($_GPC['lat']);
		$lng = floatval($_GPC['lng']);
		$sorttype = $_GPC['sorttype'];
		$range = $_GPC['range'];
		$this->setting();
		$disopt=array(
			array('range'=>'500','title'=>'附近'), 
			array('range'=>'0.5','title'=>'500米'),
			array('range'=>'1','title'=>'1000米'),
			array('range'=>'2','title'=>'2000米'),
			array('range'=>'5','title'=>'5000米'),
			array('range'=>'10000','title'=>'5000米以上'),

		);
		$merch_set = m('common')->getPluginset('merch');
		if (!empty($merch_set['near_distance'])) {
			$range = $merch_set['near_distance'];
			$disopt = array(
				'near' => $range
			);
			
		}
		
		$this->model=p('merch');
		if (empty($range)) 
		{
			$range = 500;
		}
		if (!(empty($_GPC['keyword']))) 
		{
			$data['like'] = array('merchname' => $_GPC['keyword']);
		}
		if (!(empty($_GPC['cateid']))) 
		{
			$data['cateid'] = $_GPC['cateid'];
		}
		$data = array_merge($data, array('status' => 1, 'field' => 'id,uniacid,merchname,salecate,desc,logo,groupid,cateid,address,tel,lng,lat,mobile'));
		//salecate值为新增，读取主营的值
		if (!(empty($sorttype))) 
		{
			$data['orderby'] = array('id' => 'desc');
		}
		$merchuser = $this->model->getMerch($data);



		if (!(empty($merchuser))) 
		{
			$data = array();
			$data = array_merge($data, array( 'status' => 1, 'orderby' => array('displayorder' => 'desc', 'id' => 'asc') ));
			$category = $this->model->getCategory($data);
			$cate_list = array();

			if (!(empty($category))) 
			{
				foreach ($category as $k => $v ) 
				{
					$cate_list[$v['id']] = $v;
				}
			}
			foreach ($merchuser as $k => $v ) 
			{
				if (($lat != 0) && ($lng != 0) && !(empty($v['lat'])) && !(empty($v['lng']))) 
				{
					$distance = m('util')->GetDistance($lat, $lng, $v['lat'], $v['lng'], 200);
					
					if ((0 < $range) && ($range < $distance))
					{	
						unset($merchuser[$k]);
						continue;
					}
					$merchuser[$k]['distance'] = $distance;
				}
				else 
				{
					$merchuser[$k]['distance'] = 100000;


				}

				$merchuser[$k]['catename'] = $cate_list[$v['cateid']]['catename'];
				$merchuser[$k]['url'] = mobileUrl('merch/map', array('merchid' => $v['id']));
				$merchuser[$k]['merch_url'] = mobileUrl('merch', array('merchid' => $v['id']));
				$merchuser[$k]['logo'] = tomedia($v['logo']);
				// var_dump($merchuser);die();
			}


		}

		$total = count($merchuser);
		if ($sorttype == 0) 
		{
			$merchuser = m('util')->multi_array_sort($merchuser, 'distance');
		}
		$start = ($pindex - 1) * $psize;
		if (!(empty($merchuser))) 
		{
			$merchuser = array_slice($merchuser, $start, $psize);
		}
		
		app_json(array('list' => $merchuser,'disopt'=>$disopt,'cates'=>$category,'total' => $total, 'pagesize' => $psize));
	}
/*小程序商品列表展示*/
	public function goods_list()
	{
		global $_W;
		global $_GPC;
		$this->setting();
		$set = p('merch')->getPluginsetByMerch('merch');

		// 判断是否开启商品同步，开启则显示全部商品（总店+自身多商户）
		$synchro_status = $set['is_opensynchro'];
		$merchid = $_GPC['id']; 
		$isrecommand = $_GPC['isrecommand'];
		$isnew = $_GPC['isnew'];
		$args = array('page' => intval($_GPC['page']), 'pagesize' => 6, 'isrecommand' => $isrecommand, 'isnew'=>$isnew,'order' => 'displayorder desc,createtime desc', 'by' => '', 'merchid' => intval($merchid), 'is_opensynchro' => intval($synchro_status));
		// 将获取到的is_opensynchro值传入$args才可以使下面的getList生效
		$recommand = m('goods')->getList($args);
		// echo "<pre>";
		// var_dump($recommand);die;
		app_json(array('list' => $recommand['list'], 'pagesize' => $args['pagesize'], 'total' => $recommand['total'], 'page' => intval($_GPC['page'])));

	}

	public function intro(){

		global $_W;
		global $_GPC;

		$this->setting();
		$uniacid = $_W['uniacid'];
		$mid = intval($_GPC['mid']);
		$merchid = intval($_GPC['id']);

		$this->model=p('merch');
		if (!($merchid)) {

			app_error('没有找到此商户');
		}


		$merch = $this->model->getListUserOne($merchid);

		$merch['logo'] = tomedia($merch['logo']);
		$url = "pages/changce/merch/detail?id=".$merchid;
		$merch['wxcode']=m('qrcode')->createWxCode($url,$merch['logo']);
		app_json(array('merch' =>$merch));
	}


	public function get_detail(){

		global $_W;
		global $_GPC;

		$this->setting();
		$uniacid = $_W['uniacid'];
		$mid = intval($_GPC['mid']);
		$merchid = intval($_GPC['id']);

		$this->model=p('merch');
		if (!($merchid)) {

			app_error('没有找到此商户');
		}


		$merch = $this->model->getListUserOne($merchid);

		$merch['logo'] = tomedia($merch['logo']);
		$url = "pages/changce/merch/detail?id=".$merchid;
		$merch['wxcode']=m('qrcode')->createWxCode($url,$merch['logo']);
		$result=array('merch' =>$merch);
		app_json($result);

	}

	public function  apply(){

		global $_W;
		global $_GPC;
		$this->setting();

		
		$set = $_W['shopset']['merch'];
		if (empty($set['apply_openmobile'])) 
		{
			app_error(1,'未开启商户入驻申请');
		}
		$reg = pdo_fetch('select * from ' . tablename('ewei_shop_merch_reg') . ' where openid=:openid and uniacid=:uniacid limit 1', array(':openid' => $_W['openid'], ':uniacid' => $_W['uniacid']));
		$user = false;

		if (!(empty($reg['status']))) 
		{
			$user = pdo_fetch('select * from ' . tablename('ewei_shop_merch_user') . ' where openid=:openid and uniacid=:uniacid limit 1', array(':openid' => $_W['openid'], ':uniacid' => $_W['uniacid']));
		}
		if (!(empty($user)) && (1 <= $user['status'])) 
		{
			app_error(1,'您已经申请，无需重复申请!');
		}
		$apply_set = array();
		$apply_set['open_protocol'] = $set['open_protocol'];
		$aply_set['applycontent'] = $set['applycontent'];
		if (empty($set['applytitle'])) 
		{
			$apply_set['applytitle'] = '入驻申请协议';
		}
		else 
		{
			$apply_set['applytitle'] = $set['applytitle'];
		}
		// var_dump($aaply_set);die();
		$template_flag = 0;
		$diyform_plugin = p('diyform');
		$fields = array();
		if ($diyform_plugin) 
		{
			$area_set = m('util')->get_area_config_set();
			$new_area = intval($area_set['new_area']);
			if (!(empty($set['apply_diyform'])) && !(empty($set['apply_diyformid']))) 
			{
				$template_flag = 1;
				$diyform_id = $set['apply_diyformid'];
				if (!(empty($diyform_id))) 
				{
					$formInfo = $diyform_plugin->getDiyformInfo($diyform_id);
					$fields = $formInfo['fields'];
					$diyform_data = iunserializer($reg['diyformdata']);
					$member = m('member')->getMember($_W['openid']);
					$f_data = $diyform_plugin->getDiyformData($diyform_data, $fields, $member);
				}


				$appDatas = $diyform_plugin->wxApp($fields, $f_data, $this->member);
			}
		}
		if ($_W['ispost']) 
		{
			if (empty($set['apply_openmobile'])) 
			{
				show_json(1,'未开启商户入驻申请!');
			}
			if (!(empty($user)) && (1 <= $user['status'])) 
			{
				show_json(1,'您已经申请，无需重复申请!');
			}
			$uname = trim($_GPC['uname']);
			$upass = $_GPC['upass'];
			if (empty($uname)) 
			{
				show_json(1,'请填写帐号!');
			}
			if (empty($upass)) 
			{
				show_json(1,'请填写密码!');
			}
			$where1 = ' uname=:uname';
			$params1 = array(':uname' => $uname);
			if (!(empty($reg))) 
			{
				$where1 .= ' and id<>:id';
				$params1[':id'] = $reg['id'];
			}
			$usercount1 = pdo_fetchcolumn('select count(1) from ' . tablename('ewei_shop_merch_reg') . ' where ' . $where1 . ' limit 1', $params1);
			$where2 = ' username=:username';
			$params2 = array(':username' => $uname);
			$usercount2 = pdo_fetchcolumn('select count(1) from ' . tablename('ewei_shop_merch_account') . ' where ' . $where2 . ' limit 1', $params2);
			if ((0 < $usercount1) || (0 < $usercount2)) 
			{
				show_json(1,'帐号 ' . $uname . ' 已经存在,请更改!');
			}
			$upass = m('util')->pwd_encrypt($upass, 'E');
			$data = array('uniacid' => $_W['uniacid'], 'openid' => $_W['openid'], 'status' => 0, 'realname' => trim($_GPC['realname']), 'mobile' => trim($_GPC['mobile']), 'uname' => $uname, 'upass' => $upass, 'merchname' => trim($_GPC['merchname']), 'salecate' => trim($_GPC['salecate']), 'desc' => trim($_GPC['desc']));
			if ($template_flag == 1) 
			{
				$mdata = $_GPC['mdata'];
				$insert_data = $diyform_plugin->getInsertData($fields, $mdata);
				$datas = $insert_data['data'];
				$m_data = $insert_data['m_data'];
				$mc_data = $insert_data['mc_data'];
				$data['diyformfields'] = iserializer($fields);
				$data['diyformdata'] = $datas;
			}
			if (empty($reg)) 
			{
				$data['applytime'] = time();
				pdo_insert('ewei_shop_merch_reg', $data);
			}
			else 
			{
				pdo_update('ewei_shop_merch_reg', $data, array('id' => $reg['id']));
			}
			
			show_json(1,"更新成功");
		}	
		$fields['length']=count($fields);
		$result=array(
			'canapply'=>$set['apply_openmobile'],
			'set'=>$set,
			'myuser'=>$user,
			'diyform'=> array(
				'fields' => $appDatas['fields'],
				'f_data' =>$appDatas['f_data'],
			),
			'reg'=>$reg?$reg:[],
		);
		// echo "<pre/>";
		// var_dump($result);die();
		app_json($result);
	}
	public function bangding(){
		global $_W;
		global $_GPC;
		$openid = $_W['openid'];
		//var_dump($openid);die;
		//var_dump($_W['openid']);die;
		$data = array(	 
		'shop_id' => $_GPC['shop_id']);
		//var_dump($data);die;
		
		//update tablename . ewei_shop_member set shop_id = shop_id where id = 
		pdo_update('ewei_shop_member', $data, array('openid' => $openid)); 
		//$update = pdo_update('ewei_shop_member', $data, array('openid' => $openid));
		//var_dump($update);die;
		
	}

	/**
     * 商家地图地图
     */
	public function map()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		$merchid = intval($_GPC['merchid']);
		$verifyset=m('common')->getSysset('verify');	

		$merch_user = pdo_fetch('select * from ' . tablename('ewei_shop_merch_user') . ' where id=:id and uniacid=:uniacid', array(':id' => $id, ':uniacid' => $_W['uniacid']));

		$merch_user['logo'] = empty($merch_user['logo']) ? $_W['shopset']['shop']['logo'] : $merch_user['logo'];
		$merch_user['logo'] = tomedia($merch_user['logo']);
		$gcj02 = $this->Convert_BD09_To_GCJ02($merch_user['lat'], $merch_user['lng']);
		$merch_user['lat'] = $gcj02['lat'];
		$merch_user['lng'] = $gcj02['lng'];
		app_json(array('merch_user' => $merch_user));
	}

	public function Convert_BD09_To_GCJ02($lat, $lng)
	{
		$x_pi = (3.1415926535897931 * 3000) / 180;
		$x = $lng - 0.0064999999999999997;
		$y = $lat - 0.0060000000000000001;
		$z = sqrt(($x * $x) + ($y * $y)) - (2.0000000000000002E-5 * sin($y * $x_pi));
		$theta = atan2($y, $x) - (3.0000000000000001E-6 * cos($x * $x_pi));
		$lng = $z * cos($theta);
		$lat = $z * sin($theta);
		return array('lat' => $lat, 'lng' => $lng);
	}
}

?>
