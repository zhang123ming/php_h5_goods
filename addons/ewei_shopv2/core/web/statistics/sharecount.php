<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Sharecount_EweiShopV2Page extends WebPage
{
	//fen列表
	public function main()
	{
		global $_W;
		global $_GPC;
		$pindex = max(1, intval($_GPC['page']));
		$psize = 15;


		if (!empty($_GPC['realname'])) {
			if(strlen(intval($_GPC['realname'])) >1 ){
				$condition = " a.goods_id = ".intval($_GPC['realname']);
			}else{
				$condition = "  b.title Like '%" . $_GPC['realname'] . "%'";
				$_GPC['realname'] = trim($_GPC['realname']);
				$params['realname'] = '%' . $_GPC['realname'] . '%';
			}

			if (!empty($_GPC['time']['start']) && !empty($_GPC['time']['end'])) {
				$starttime = strtotime($_GPC['time']['start']);
				$endtime = strtotime($_GPC['time']['end']);
				$condition .= " AND a.update_time >= '".date('Y-m-d H:i:s',$starttime)."' AND a.update_time <= '".date('Y-m-d H:i:s',$endtime)."' ";
			
			}
		}else{
			if (!empty($_GPC['time']['start']) && !empty($_GPC['time']['end'])) {
				$starttime = strtotime($_GPC['time']['start']);
				$endtime = strtotime($_GPC['time']['end']);
				$condition = "  a.update_time >= '".date('Y-m-d H:i:s',$starttime)."' AND a.update_time <= '".date('Y-m-d H:i:s',$endtime)."' ";
			
			}
		}

		if(!empty($_GPC['realname']) || !empty($_GPC['time']['start']) && !empty($_GPC['time']['end'])){
			$sql = " select a.*,sum(a.count) as count ,b.thumb,b.title from  ". tablename('ewei_shop_counts') ." as a left join  ". tablename('ewei_shop_goods') ." as b on a.goods_id = b.id where ".$condition."  group by goods_id  order by count desc  ";
			$total = pdo_fetchcolumn(" select count(*) from  ". tablename('ewei_shop_counts') ." as a left join  ". tablename('ewei_shop_goods') ." as b on a.goods_id = b.id where ".$condition."  group by goods_id ");
		}else{
			$sql = " select a.*,sum(a.count) as count ,b.thumb,b.title from  ". tablename('ewei_shop_counts') ." as a left join  ". tablename('ewei_shop_goods') ." as b on a.goods_id = b.id group by goods_id order by count desc ";

			$total = pdo_fetchcolumn(" select count(*) from  ". tablename('ewei_shop_counts') ."  group by goods_id  ");
			
		}
		if (empty($_GPC['export'])) {
			$sql .= ' limit ' . (($pindex - 1) * $psize) . ',' . $psize;
		}
		// dump($sql);die;
		$list = pdo_fetchall($sql, $params);

		if ($_GPC['export'] == '1') {
				
			m('excel')->export($list, array(
			'title'   => '分享统计-' . date('Y-m-d-H-i', time()),
			'columns' => array(
				array('title' => '商品', 'field' => 'title', 'width' => 18),
				array('title' => '统计数', 'field' => 'count', 'width' => 12),
				array('title' => '最新时间', 'field' => 'update_time', 'width' => 12),
				)
			));
		}

		$pager = pagination2($total, $pindex, $psize);
		include $this->template();
	}

	public function post()
	{
		global $_W;
		global $_GPC;
		$pindex = max(1, intval($_GPC['page']));
		$psize = 15;
		$goodsid = intval($_GPC['id']);

		if (!empty($_GPC['realname'])) {
			if(strlen(intval($_GPC['realname'])) >1 ){
				$condition = " and a.mid = ".intval($_GPC['realname']);
			}else{
				$condition = " and  b.nickname Like '%" . $_GPC['realname'] . "%'";
				$_GPC['realname'] = trim($_GPC['realname']);
				$params['realname'] = '%' . $_GPC['realname'] . '%';
			}
			
		}

		if (!empty($_GPC['time']['start']) && !empty($_GPC['time']['end'])) {
			$starttime = strtotime($_GPC['time']['start']);
			$endtime = strtotime($_GPC['time']['end']);
			$condition .= " AND a.update_time >= '".date('Y-m-d H:i:s',$starttime)."' AND a.update_time <= '".date('Y-m-d H:i:s',$endtime)."' ";
			
		}

		$sql = " select a.*,sum(a.count) as count,b.nickname,b.avatar from  ". tablename('ewei_shop_counts') ." as a left join  ". tablename('ewei_shop_member') ." as b on a.mid = b.id where a.goods_id = ".$goodsid."".$condition." group by a.mid  order by count desc  ";
		// dump($sql);die;
		if (empty($_GPC['export'])) {
			$sql .= ' limit ' . (($pindex - 1) * $psize) . ',' . $psize;
		}
		$list = pdo_fetchall($sql, $params);

		$total = count($list);

		if ($_GPC['export'] == '1') {
			
			m('excel')->export($list, array(
			'title'   => '分享用户统计-' . date('Y-m-d-H-i', time()),
			'columns' => array(
				array('title' => '商品id', 'field' => 'goods_id', 'width' => 18),
				array('title' => '粉丝', 'field' => 'nickname', 'width' => 12),
				array('title' => '统计数', 'field' => 'count', 'width' => 12),
				array('title' => '开始时间', 'field' => 'create_time', 'width' => 12),
				array('title' => '结束时间', 'field' => 'update_time', 'width' => 12),
				)
			));
		}
		$pager = pagination2($total, $pindex, $psize);
		include $this->template();

	}

	




}