<?php 

if (!(defined('IN_IA'))) {
	exit('Access Denied');
}

require EWEI_SHOPV2_PLUGIN . 'app/core/page_mobile.php';

/**
 * 1218社区团购类型类
 */
class Group_EweiShopV2Page extends AppMobilePage
{
	
	public function getGoodList()
	{
		global $_W;
		global $_GPC;

		$currenttime=time();
		$args  =array('page' => $_GPC['page'], 'pagesize' => 10,'cate'=>$_GPC['cate']);

		$goods = pdo_fetchall('select id,statustimestart,statustimeend,status,isstatustime from ' . tablename('ewei_shop_goods') . ' where uniacid = ' . $_W['uniacid'] . ' and isstatustime > 0 and deleted = 0 and status < 2');

		foreach ($goods as $key => $value) {
			if (($value['statustimestart'] < time()) && (time() < $value['statustimeend'])) {

				$value['status'] = 1;
			}
			else {
				$value['status'] = 0;
			}

			pdo_update('ewei_shop_goods', array('status' => $value['status']), array('id' => $value['id']));
		}

		$condition = "and `uniacid` = :uniacid AND `deleted` = 0 and status<2 and `checked` = 0 and isstatustime=1  and statustimestart<'{$currenttime}' and statustimeend>'{$currenttime}' and type !=10 ";

		$pagesize=10;
		$page = (!empty($args['page']) ? intval($args['page']) : 1);

		$params=array(':uniacid'=>$_W['uniacid']);

		if (!empty($args['cate'])) {
			$category = m('shop')->getAllCategory();
			$catearr = array($args['cate']);

			foreach ($category as $index => $row) {
				if ($row['parentid'] == $args['cate']) {
					$catearr[] = $row['id'];

					foreach ($category as $ind => $ro) {
						if ($ro['parentid'] == $row['id']) {
							$catearr[] = $ro['id'];
						}
					}
				}
			}

			$catearr = array_unique($catearr);
			$condition .= ' AND ( ';

			foreach ($catearr as $key => $value) {
				if ($key == 0) {
					$condition .= 'FIND_IN_SET(' . $value . ',cates)';
				}
				else {
					$condition .= ' || FIND_IN_SET(' . $value . ',cates)';
				}
			}

			$condition .= ' <>0 )';
		}
		
		
		$sql = "SELECT id as gid,title,subtitle,thumb,marketprice,productprice,minprice,maxprice,isdiscount,isdiscount_time,isstatustime,statustimestart,status,statustimeend,isdiscount_discounts,sales,salesreal,total,description,bargain,`type`,hasoption FROM " . tablename('ewei_shop_goods') . ' where 1 ' . $condition . 'order by displayorder desc';

		$total = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_goods') . ' where 1 ' . $condition . ' ', $params);

		$goods=pdo_fetchall($sql, $params);

		foreach ($goods as $key => &$val) {
			$val['sales']=$val['salesreal']+$val['sales'];
			$val['thumb']=$val['thumb']."?x-oss-process=image/auto-orient,1/resize,p_90/quality,q_90";
			// $sql = "SELECT m.avatar,m.nickname FROM ".tablename('ewei_shop_order_goods')." as o LEFT JOIN ".tablename('ewei_shop_member')." as m ON o.openid=m.openid WHERE o.goodsid=:gid AND m.uniacid=:uniacid limit 3";
			$sql="SELECT m.nickname,m.avatar FROM ".tablename('ewei_shop_order_goods')." as og LEFT JOIN ".tablename('ewei_shop_order')." as o ON  og.orderid=o.id LEFT JOIN ".tablename('ewei_shop_member')."as m ON m.openid=o.openid WHERE og.goodsid=:gid AND m.uniacid=:uniacid order by o.createtime DESC LIMIT 3";
			$val['buyerList'] = pdo_fetchall($sql,array(':gid'=>$val['gid'],":uniacid"=>$_W['uniacid']));

		}

		app_json(array('list'=>$goods, 'pagesize' => $args['pagesize'], 'total' => $total, 'page' => $page));

	}


}


 ?>