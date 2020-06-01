<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Package_EweiShopV2Page extends MobilePage
{
	public function main()
	{
		global $_W;
		global $_GPC;
		$openid = $_W['openid'];
		$uniacid = $_W['uniacid'];
		$ispackage = $_GPC['ispackage'];
		$goodsid = intval($_GPC['goodsid']);
		$packages_goods = array();
		$packages = array();
		$goodsid_array = array();

		if($ispackage==1){
			$packages = pdo_fetchall('SELECT id,title,thumb,price,goodsid,ispackage FROM ' . tablename('ewei_shop_package') . "\r\n                    WHERE uniacid = " . $uniacid . ' and starttime <= ' . time() . ' and endtime >= ' . time() . ' and deleted = 0 and status = 1 and ispackage=1 ORDER BY id DESC');
		}else{
			if ($goodsid) {
			$packages_goods = pdo_fetchall('SELECT id,pid FROM ' . tablename('ewei_shop_package_goods') . "\r\n                    WHERE uniacid = " . $uniacid . ' and goodsid = ' . $goodsid . ' group by pid  ORDER BY id DESC');

			foreach ($packages_goods as $key => $value) {
				$packages[$key] = pdo_fetch('SELECT id,title,thumb,price,goodsid FROM ' . tablename('ewei_shop_package') . "\r\n                    WHERE uniacid = " . $uniacid . ' and id = ' . $value['pid'] . ' and starttime <= ' . time() . ' and endtime >= ' . time() . ' and deleted = 0 and status = 1  ORDER BY id DESC');
			}

			$packages = array_values(array_filter($packages));
			}
			else {
			$packages = pdo_fetchall('SELECT id,title,thumb,price,goodsid FROM ' . tablename('ewei_shop_package') . "\r\n                    WHERE uniacid = " . $uniacid . ' and starttime <= ' . time() . ' and endtime >= ' . time() . ' and deleted = 0 and status = 1 ORDER BY id DESC');
			}
		}

		if (empty($packages)) {
			$this->message('套餐不存在或已删除!', mobileUrl(), 'error');
		}

		foreach ($packages as $key => $value) {
			$goods = explode(',', $value['goodsid']);

			foreach ($goods as $k => $val) {
				$g = pdo_fetch('SELECT id,marketprice FROM ' . tablename('ewei_shop_goods') . "\r\n                    WHERE uniacid = " . $uniacid . ' and id = ' . $val . '  ORDER BY id DESC');
				$goods['goodsprice'] += $g['marketprice'];
			}

			$packages[$key]['goodsprice'] = $goods['goodsprice'];
		}

		$packages = set_medias($packages, array('thumb'));
		include $this->template();
	}

	public function detail()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$openid = $_W['openid'];
		$pid = intval($_GPC['pid']);
		$package = pdo_fetch('SELECT id,title,price,freight,share_title,share_icon,share_desc,ispackage FROM ' . tablename('ewei_shop_package') . "\r\n                    WHERE uniacid = " . $uniacid . ' and id = ' . $pid . ' ');
		$packgoods = array();
		$packgoods = pdo_fetchall('SELECT id,uniacid,title,thumb,marketprice,packageprice,`option`,goodsid,pid,categoryid,categorystatus FROM ' . tablename('ewei_shop_package_goods') . "\r\n                    WHERE uniacid = " . $uniacid . ' and pid = ' . $pid . '  ORDER BY id DESC');

		$packgoods = set_medias($packgoods, array('thumb'));
		if($package['ispackage']!=1){			
			$option = array();
			foreach ($packgoods as $key => $value) {
				$option_array = array();
				$option_array = explode(',', $value['option']);

				if (0 < $option_array[0]) {
					$pgo = pdo_fetch('SELECT id,title,packageprice FROM ' . tablename('ewei_shop_package_goods_option') . "\r\n                    WHERE uniacid = " . $uniacid . ' and pid = ' . $pid . ' and goodsid = ' . $value['goodsid'] . ' and optionid = ' . $option_array[0] . ' ');
					$packgoods[$key]['packageprice'] = $pgo['packageprice'];
				}
			}
		}else{			
			$gettotal = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_package_category_goods') . " WHERE uniacid = " . $uniacid . ' and pid = ' . $pid ." and openid = "."'".$_W['openid']."'");
			
			foreach ($packgoods as $key => &$value) {	
					//分类订单表更新开始
					$diyformdata = iunserializer($_SESSION['diyformdata']['diyformdata']);
					$c_data = array();
					$log_data = array();
					$log=array();		
					$packid=pdo_fetch('SELECT id FROM' . tablename('ewei_shop_package_goods').'WHERE categoryid='.$_GPC['categoryid']);
					if($_GPC['goodsid']){			
						$c_data['categorystatus'] = 1;
						$log_data['goodsid'] = $_GPC['goodsid'];
						pdo_update('ewei_shop_package_goods', $c_data, array('categoryid' => $_GPC['categoryid']));			
						pdo_update('ewei_shop_package_category_goods', $log_data, array('packid' => $packid,'openid' => $_W['openid']));
						$goodslist = pdo_fetchall('SELECT * FROM ' . tablename('ewei_shop_goods') . "WHERE uniacid = " . $uniacid . ' and id = ' . $_GPC['goodsid'] .'  ORDER BY id DESC');
					}
					if($_SESSION['optionid']){			
						$log_data['option'] = substr($_SESSION['optionid'], 6);
						file_put_contents('text.txt', $log_data['option']."\r\n",FILE_APPEND);
						pdo_update('ewei_shop_package_category_goods', $log_data, array('packid' => $packid,'openid' => $_W['openid']));
						session_unset();
						session_destroy();
					}
					//分类订单表更新结束

				// 分类商品查找开始	
					/*获取页面传值开始*/
					$categorygoods = pdo_fetch('SELECT id,title,thumb,marketprice,packageprice,`option`,goodsid,pid,openid FROM ' . tablename('ewei_shop_package_category_goods') . " WHERE uniacid = " . $uniacid . ' and pid = ' . $pid . ' and packid = '.$value['id']. " and openid = "."'".$_W['openid']."'".'  ORDER BY id DESC');

					if(empty($categorygoods['goodsid'])&&empty($categorygoods['option'])){
						$package['goodsempty'] = 1;
					}

					$value['option'] = $categorygoods['option'];
					$value['goodsid'] = $categorygoods['goodsid'];
					$packimg = pdo_fetch('SELECT id,uniacid,thumb FROM ' . tablename('ewei_shop_goods') . " WHERE uniacid = " . $uniacid . ' and id = ' . $categorygoods['goodsid'] .'  ORDER BY id DESC');
					$value['packimg'] = $packimg['thumb'];

					/*获取页面传值结束*/							
					$goodsoption = pdo_fetch('SELECT id,marketprice,weight,title,productprice FROM' . tablename('ewei_shop_goods_option').'WHERE  id='.$categorygoods['option']);		
					$marketprice = pdo_fetch('SELECT marketprice FROM' . tablename('ewei_shop_goods') . 'WHERE uniacid='.$uniacid.' and id='.$categorygoods['goodsid']);						
					if($categorygoods['option']){												
						$optiondata=array('title'=>$goodsoption['title'],'packageprice'=>$goodsoption['marketprice'],'marketprice'=>$marketprice['marketprice']);
						pdo_update('ewei_shop_package_category_goods',$optiondata,array('option' => $categorygoods['option'],'openid' => $_W['openid']));
						$value['packageprice'] = $goodsoption['marketprice'];
					}else{	
						$elseoptiondata=array('packageprice'=>$marketprice['marketprice'],'marketprice'=>$marketprice['marketprice']);
						pdo_update('ewei_shop_package_category_goods',$elseoptiondata,array('goodsid' => $categorygoods['goodsid'],'openid' => $_W['openid']));
						$value['packageprice'] = $marketprice['marketprice'];
					}
					$goodslog = pdo_fetch('SELECT id,title FROM' . tablename('ewei_shop_goods') . 'WHERE uniacid='.$uniacid.' and id='.$categorygoods['goodsid']);
					$value['goodname'] = $goodslog['title'];					
					$value['weight'] = $goodsoption['weight'];
					$value['cate'] = $goodsoption['title'];

				// 分类商品查找结束

				if($package['ispackage']==1 && $gettotal==0 && $_W['openid']!=''){	
						$log['openid'] = $_W['openid'];
						$log['pid'] = $value['pid'];
						$log['uniacid'] = $_W['uniacid'];
						$log['packid'] = $value['id'];
						pdo_insert('ewei_shop_package_category_goods', $log);

					}
			}			
		}
		$_W['shopshare'] = array('title' => !empty($package['share_title']) ? $package['share_title'] : $package['title'], 'imgUrl' => !empty($package['share_icon']) ? tomedia($package['share_icon']) : tomedia($package['thumb']), 'desc' => !empty($package['share_desc']) ? $package['share_desc'] : $_W['shopset']['shop']['name'], 'link' => mobileUrl('goods/package/detail', array('pid' => $package['id']), true));
		include $this->template('goods/packdetail');
	}

	public function option()
	{
		global $_W;
		global $_GPC;
		$openid = $_W['openid'];
		$uniacid = intval($_W['uniacid']);
		$pid = intval($_GPC['pid']);
		$goodsid = intval($_GPC['goodsid']);
		$optionid = array();
		$option = array();
		$packgoods = pdo_fetch('SELECT id,title,`option` FROM ' . tablename('ewei_shop_package_goods') . "\r\n                    WHERE uniacid = " . $uniacid . ' and goodsid = ' . $goodsid . ' and pid = ' . $pid . '  ORDER BY id DESC');
		$optionid = explode(',', $packgoods['option']);

		foreach ($optionid as $key => $value) {
			$option[$key] = pdo_fetch('SELECT id,title,packageprice,optionid,goodsid FROM ' . tablename('ewei_shop_package_goods_option') . "\r\n                    WHERE uniacid = " . $uniacid . ' and goodsid = ' . $goodsid . ' and optionid = ' . intval($value) . ' ORDER BY id DESC');
		}

		show_json(1, $option);
	}
	public function inscribe()
	{
		
		include $this->template('goods/inscribe');
	}

}

?>
