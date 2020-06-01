<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Adminlist_EweiShopV2Page extends PluginWebPage
{
	public function main()
	{
		global $_W;
		global $_GPC;
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		$condition = 'uniacid=:uniacid and communicationgrade=1';
		$params = array(':uniacid' => $_W['uniacid']);

		if ($_GPC['category'] != '') {
			$condition .= ' and categoryid like :category';
			$params[':category'] = '%' .$_GPC['category']. '%';
		}

		if (isset($_GPC['keyword'])) {
			$_GPC['keyword'] = trim($_GPC['keyword']);			
			$condition .= ' and (nickname like :keyword or id LIKE :keyword)';
			$params[':keyword'] = '%' . $_GPC['keyword'] . '%';
		}

		$list = pdo_fetchall('SELECT * FROM ' . tablename('ewei_shop_member') . ' WHERE '. $condition .' ORDER BY id desc LIMIT ' . (($pindex - 1) * $psize) . ',' . $psize, $params);
		foreach ($list as $key => &$value) {
			
			rtrim(',',$value['categoryid']);

			$value['categoryname'] = pdo_fetchall('SELECT name from '.tablename('ewei_shop_bottledoctor_category') .' where id in ('.$value['categoryid'].') and uniacid=:uniacid ORDER BY id ASC',array(':uniacid'=>$_W['uniacid']));	

		}

		$categorys=pdo_fetchall('SELECT * FROM '.tablename('ewei_shop_bottledoctor_category').'where uniacid="'.$_W['uniacid'].'"');	

		unset($value);

		$total = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_member') . ' where ' . $condition, $params);

		$pager = pagination2($total, $pindex, $psize);
		include $this->template();
	}

	public function edit()
	{
		$this->post();
	}

	protected function post()
	{
		global $_W;
		global $_GPC;
		// echo "<pre>";
		// var_dump($_GPC['categoryid']);exit;
		$id = intval($_GPC['id']);	
		$list = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_member').'where uniacid="'.$_W['uniacid'].'" and id="'.$id.'"');
		$category=pdo_fetchall('SELECT * FROM '.tablename('ewei_shop_bottledoctor_category').'where uniacid="'.$_W['uniacid'].'"');	
		if ($_W['ispost']) {	
			$data = array();
			if(!empty($_GPC['categoryid'])){					
					$categoryidArr = $_GPC['categoryid'];
					$categoryid = implode(',', $categoryidArr);
					$data['categoryid'] = $categoryid;
				}

			if (!empty($id)) {						
				pdo_update('ewei_shop_member', $data , array('id' => $id, 'uniacid' => $_W['uniacid']));
			}

			show_json(1, array('url' => webUrl('bottledoctor/adminlist', array('op' => 'display'))));
		}
		
		include $this->template();
	}
	
}

?>
