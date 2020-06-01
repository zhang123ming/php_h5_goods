<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

require EWEI_SHOPV2_PLUGIN . 'mmanage/core/inc/page_mmanage.php';
class Data_EweiShopV2Page extends MmanageMobilePage
{
	
	public function bymall()
	{
		global $_W;
		global $_GPC;
		if (!cv('order.op.changeaddress')) {
			//show_json(0, '您没有权限');
		}
		$status=1;
		if($_GPC['date']){
			if($_GPC['date']=="preMonth"){
				//上月
				$firstday=date("Y-m-d H:i:s",mktime(0, 0 , 0,date("m")-1,1,date("Y"))); 
				$lastday=date("Y-m-d H:i:s",mktime(23,59,59,date("m") ,0,date("Y")));
				$where=" and (o.paytime between ".strtotime($firstday)." and ".strtotime($lastday).")";
			}elseif($_GPC['date']=="month"){
				//本月
				$firstday = date("Y-m-d H:i:s",mktime(0, 0 , 0,date("m"),1,date("Y")));
				$lastday = date("Y-m-d H:i:s",mktime(23,59,59,date("m"),date("t"),date("Y"))); 
				$where=" and (o.paytime between ".strtotime($firstday)." and ".strtotime($lastday).")";
			}elseif($_GPC['date']=="yestoday"){
				//昨天
				$firstday = date("Y-m-d",strtotime("-1 day"));
				$lastday = date('y-m-d',time()); 
				$where=" and  (o.paytime between ".strtotime($firstday)." and ".strtotime($lastday).")";
			}elseif($_GPC['date']=="today"){
				//今天
				$lastday=strtotime(date('y-m-d'),time());
				$where=" and  (o.paytime > ".$lastday.")";
			}elseif($_GPC['date']=="diy"){
				$firstday = $_GPC['beginTime'];
				$lastday = $_GPC['endTime'];
				$where=" and (o.paytime between ".strtotime($firstday)." and ".strtotime($lastday).")";
			}
		}
		if($_GPC['keyword']){
			$where.=" and s.title like '%$_GPC[keyword]%'";
		}
		$sql="SELECT s.title,s.thumb,SUM(g.price) as priceTotal,g.*,SUM(g.total) as count FROM `ims_ewei_shop_order_goods` g,ims_ewei_shop_order o,ims_ewei_shop_goods s where s.id=g.goodsid and  g.orderid=o.id and o.status=$status and g.uniacid=$_W[uniacid] $where group by g.goodsid,g.optionid";
		$list = pdo_fetchall($sql,array());
		foreach($list as $k=>&$t){
			$t['thumb']=tomedia($t['thumb']);
			$t['marketprice'] = $t['priceTotal']/$t['count'];
		}
		include $this->template();
	}
}

?>
