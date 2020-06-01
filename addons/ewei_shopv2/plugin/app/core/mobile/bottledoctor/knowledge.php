<?php

if (!defined('IN_IA')) {
	exit('Access Denied');
}

require EWEI_SHOPV2_PLUGIN . 'app/core/page_mobile.php';
class Knowledge_EweiShopV2Page extends AppMobilePage
{
	public function get_list()
	{
		global $_W;
		global $_GPC;
		$pindex = max(1, intval($_GPC['page']));
		$psize = 10;
		$article_category=18;
		$condition = ' and f.uniacid = :uniacid and f.article_category = :article_category';
		$params = array(':uniacid' => $_W['uniacid'],':article_category'=>$article_category);
		$sql = 'SELECT COUNT(*) FROM ' . tablename('ewei_shop_bottledoctorarticle') . ' f where 1 ' . $condition;
		$total = pdo_fetchcolumn($sql, $params);
		$sql = 'SELECT f.id,f.article_title,f.article_content,f.article_date,f.article_category FROM ' . tablename('ewei_shop_bottledoctorarticle') . ' f ' . ' where 1 ' . $condition . ' ORDER BY `id` DESC LIMIT ' . (($pindex - 1) * $psize) . ',' . $psize;
		$list = pdo_fetchall($sql, $params);
		$result = array(
			'list'     => array(),
			'total'    => $total,
			'pagesize' => $psize,
			);
		// foreach ($list as $k=>$row) {
		// 	$result['list'][$k]['article_title'] = $row['article_title'];
		// 	$result['list'][$k]['article_content'] = $row['article_content'];
			
		// }
		foreach ($list as &$row) {
			$row['descrption'] =strip_tags($row['article_content']);
			// $result['article_content'] = $row['article_content'];
			
		}
		unset($row);
		$result['list'] = $list;
		app_json($result);
	}

	public function detailed()
	{
		global $_W;
		global $_GPC;
		$id=$_GPC['id'];
		$number = intval($_GPC['readnumber']);
		$sql = "update ".tablename('ewei_shop_bottledoctorarticle').' set readnumber = readnumber +'.$number.' where id = '.$id;
		pdo_query($sql);
		$condition = 'f.uniacid = :uniacid and f.id = :id';
		$params = array(':uniacid' => $_W['uniacid'],':id' => $id);
		$sql = 'SELECT f.id,f.article_title,f.article_content,f.article_date FROM ' . tablename('ewei_shop_bottledoctorarticle') . ' f ' . ' where ' . $condition;
		$list = pdo_fetchall($sql, $params);
		app_json(array(
			'list'     => $list
			));

	}
	public function teaminfo()
	{
		global $_W;
		global $_GPC;
		$pindex = max(1, intval($_GPC['page']));
		$psize = 10;
		$article_category=19;
		$condition = ' and f.uniacid = :uniacid and f.article_category = :article_category';
		$params = array(':uniacid' => $_W['uniacid'],':article_category'=>$article_category);
		$sql = 'SELECT COUNT(*) FROM ' . tablename('ewei_shop_bottledoctorarticle') . ' f where 1 ' . $condition;
		$total = pdo_fetchcolumn($sql, $params);
		$sql = 'SELECT f.id,f.article_title,f.article_content,f.article_date,f.article_category FROM ' . tablename('ewei_shop_bottledoctorarticle') . ' f ' . ' where 1 ' . $condition . ' ORDER BY `id` DESC LIMIT ' . (($pindex - 1) * $psize) . ',' . $psize;
		$list = pdo_fetchall($sql, $params);
		app_json(array(
			'list'     => $list,
			'total'    => $total,
			'pagesize' => $psize,
			));
	}

	public function get_all_list(){
		global $_W;
		global $_GPC;
		$pindex = max(1, intval($_GPC['page']));
		$psize = 10;
		$sql = "SELECT id from".tablename('ewei_shop_bottledoctorarticle_category').'where uniacid = :uniacid and category_name = :category_name';
		$params = array(':uniacid' => $_W['uniacid'],':category_name'=>$_GPC['category_name']);
		$article_category = pdo_fetchcolumn($sql, $params);

		$condition = ' and f.uniacid = :uniacid and article_category = :article_category';
		if($_GPC['search']){
			$searchs = ' and article_title like "%'.$_GPC['search'].'%"';
		}else{
			$searchs = '';
		}
		
		$params = array(':uniacid' => $_W['uniacid'],':article_category' => $article_category);
		$sql = 'SELECT COUNT(*) FROM ' . tablename('ewei_shop_bottledoctorarticle') . ' f where 1 ' . $condition;
		$total = pdo_fetchcolumn($sql, $params);
		$sql = 'SELECT f.id,f.article_title,f.article_content,f.article_date,f.article_category,f.readnumber FROM ' . tablename('ewei_shop_bottledoctorarticle') . ' f ' . ' where 1 ' . $condition . $searchs . ' ORDER BY `id` DESC LIMIT ' . (($pindex - 1) * $psize) . ',' . $psize;
		$list = pdo_fetchall($sql, $params);

		foreach ($list as &$row) {
			$row['descrption'] =strip_tags($row['article_content']);
			// $result['article_content'] = $row['article_content'];
			
		}
		app_json(array(
			'list'     => $list,
			'total'    => $total,
			'pagesize' => $psize,
			));
	
	}
	
}

?>
