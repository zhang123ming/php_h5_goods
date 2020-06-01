<?php

if (!defined('IN_IA')) {
	exit('Access Denied');
}

require EWEI_SHOPV2_PLUGIN . 'app/core/page_mobile.php';
class Teamlist_EweiShopV2Page extends AppMobilePage
{
	public function get_list()
	{
		global $_W;
		global $_GPC;
		$pindex = max(1, intval($_GPC['page']));
		$psize = 1000;
		$article_category=18;
		$openid = $_GPC['openid'];		
		$condition = ' and f.uniacid = :uniacid';
		$params = array(':uniacid' => $_W['uniacid']);
		$member = m('member')->getMember($openid);
		$sql = 'SELECT COUNT(*) FROM ' . tablename('ewei_shop_bottledoctor_category') . ' f where 1 ' . $condition;
		$total = pdo_fetchcolumn($sql, $params);
		$sql = 'SELECT * FROM ' . tablename('ewei_shop_bottledoctor_category') . ' f ' . ' where 1 ' . $condition . ' ORDER BY id DESC,displayorder LIMIT ' . (($pindex - 1) * $psize) . ',' . $psize;
		$list = pdo_fetchall($sql, $params);


		foreach ($memberlist as $key => &$value) {			
			rtrim(',',$value['categoryid']);
		}

		$result = array(
			'list'     => array(),
			'total'    => $total,
			'pagesize' => $psize,
			);
		$search = isset($_GPC['search'])?$_GPC['search']:'';
		if(empty($search)){
			$newlist = array();
			foreach ($list as &$row) {
				if($row['enabled']=='1'){
					$row['picthumb'] = tomedia($row['thumb']);
					$row['communicationgrade'] = $member['communicationgrade'];
					// if(mb_strlen($row['name'])>6){
					// 	$row['name'] = mb_substr($row['name'],0,6)+'...';
					// }	
					$newlist[] =&$row;
				}		
			}
			unset($row);
			$result['list'] = $newlist;
			app_json($result);
		}else{
			$newlist = array();
			$preg = "/\S*".$search."\S*/";
			foreach ($list as &$row) {
				if(preg_match($preg, $row['name']) && $row['enabled']=='1'){
					$row['picthumb'] = tomedia($row['thumb']);
					$row['communicationgrade'] = $member['communicationgrade'];
					// if(mb_strlen($row['name'])>6){
					// 	$row['name'] = mb_substr($row['name'],0,5)+'...';
					// }
					$newlist[] =&$row;
				}
			}
			unset($row);
			$result['list'] = $newlist;
			app_json($result);
		}
	}

	

}

?>
