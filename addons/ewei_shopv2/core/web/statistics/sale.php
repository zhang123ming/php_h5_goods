<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Sale_EweiShopV2Page extends WebPage
{
	public function export()
	{
		global $_W;
		global $_GPC;
		$storeid=intval($_GPC['storeid']);
		$distributor=$_GPC['distributor'];
		$year=$_GPC['year'];
		$month=intval($_GPC['month']);
		$day=intval($_GPC['day']);
		$status = intval($_GPC['status']); 

		if(!$month){
			$beginmonth=1;
			$endmonth=12;	
		}else{
			$beginmonth=$endmonth=$month;
		}
		if(!$day){
			$beginday=1;
			$endday=get_last_day($year, $endmonth);;	
		}else{
			$beginday=$endday=$day;
		}

		if ($distributor) {
			$sql="SELECT * FROM ".tablename('ewei_shop_store')." WHERE uniacid=:uniacid AND distributor='{$distributor}'";
			$storeList = pdo_fetchall($sql,array(':uniacid'=>$_W['uniacid']));
	
			$storeid=implode(',',array_column($storeList,'id'));
			
		}
		if ($_GPC['searchtime']=='timequantum') {
			$starttime=strtotime($_GPC['timestart']);
			$endtime=strtotime($_GPC['timeend']);
		}else{
			$starttime=strtotime($year . '-' . $beginmonth . '-' . $beginday . ' 00:00:00');
			$endtime=strtotime($year . '-' . $endmonth . '-' . $endday . ' 23:59:59');
		}

		
		$where=" and (o.paytime between ".$starttime." and ".$endtime.")";

		if ($_GPC['status']) {
			$status=$_GPC['status'];

			$sql = "SELECT g.title,og.*,SUM(og.total) as gcount,SUM(og.price) as priceTotal FROM ".tablename('ewei_shop_order_goods')." as og LEFT JOIN ".tablename('ewei_shop_order')." as o ON og.orderid = o.id left join".tablename('ewei_shop_goods')." as g on og.goodsid=g.id WHERE o.status=$status and g.uniacid=$_W[uniacid] and o.storeid in ({$storeid}) $where group by og.goodsid,og.optionid";
		}else{
			$status=1;
			$sql = "SELECT g.title,og.*,SUM(og.total) as gcount,SUM(og.price) as priceTotal FROM ".tablename('ewei_shop_order_goods')." as og LEFT JOIN ".tablename('ewei_shop_order')." as o ON og.orderid = o.id left join".tablename('ewei_shop_goods')." as g on og.goodsid=g.id WHERE o.status>=$status and g.uniacid=$_W[uniacid] and o.storeid in ({$storeid}) $where group by og.goodsid,og.optionid";

		}
		

		$list = pdo_fetchall($sql,array());
		$i=0;
	
		foreach($list as $k=>&$t){

			if ($distributor) {
				foreach ($storeList as $key => $value) {

					if ($_GPC['status']) {
						$sql="SELECT SUM(g.total) as ggtotal FROM `ims_ewei_shop_order_goods` as g left join ims_ewei_shop_order as o ON g.orderid =o.id where o.status=$status and g.uniacid=$_W[uniacid] and o.storeid= {$value['id']} and g.goodsid='{$t[goodsid]}' $where";
					}else{
						$sql="SELECT SUM(g.total) as ggtotal FROM `ims_ewei_shop_order_goods` as g left join ims_ewei_shop_order as o ON g.orderid =o.id where o.status>=$status and g.uniacid=$_W[uniacid] and o.storeid= {$value['id']} and g.goodsid='{$t[goodsid]}' $where";
					}

					$storetotal = pdo_fetch($sql);
					$t['total'.$value['id']] = $storetotal['ggtotal'];
				}

			}
			$t['marketprice']=$t['priceTotal']/$t['gcount'];			
			$i++;
			$t['id']=$i;
		}

		$columns[] = array('title' => '序号', 'field' => 'id', 'width' => 12);
		$columns[] = array('title' => '产品名称', 'field' => 'title', 'width' => 24);
		$columns[] = array('title' => '规格', 'field' => 'optionname', 'width' => 12);
		$columns[] = array('title' => '数量', 'field' => 'gcount', 'width' => 12);
		$columns[] = array('title' => '单价', 'field' => 'marketprice', 'width' => 12);
		$columns[] = array('title' => '小计', 'field' => 'priceTotal', 'width' => 12);

		foreach ($storeList as $k=>&$t) {
			$columns[] = array('title' =>$t['storename'] ,'field'=>'total'.$t[id],'width'=>12);
		}

		$date=$year;
		if($month) $date.="-".$month;
		if($day) $date.="-".$day;

		/*维权申请订单*/
		$sql="SELECT count(*) FROM ".tablename('ewei_shop_order')." as o WHERE o.uniacid='{$_W[uniacid]}' AND o.refundstate>0 and o.refundid<>0 $where";
		$refundapplycounts =pdo_fetchcolumn($sql);

		$sql="SELECT COUNT(*) FROM ".tablename('ewei_shop_order')." as o WHERE o.uniacid='{$_W[uniacid]}' AND o.refundtime<>0 $where";
		$refundfinishcounts = pdo_fetchcolumn($sql);
		if (!empty($refundapplycounts)||!empty($refundfinishcounts)) {
			$list[]=array(
				'id'=>"维权申请订单：".$refundapplycounts."笔     维权完成订单：".$refundfinishcounts."笔",
				"merge"=>true
			);
		}

		$list[]=array(
			"id"=>"日期：{$date}     地址：{$store[storename]}   {$store[tel]}",
			"merge"=>true,
		);
		$list[]=array(
			"id"=>"审核：        配送员：      经手人：     ",
			"merge"=>true,
		);	
		m('excel')->export($list, array('firstInsert'=>true,'firstInsertText'=>$_W['shopset']['shop']['name'],'lastMerge'=>true,'title' =>'门店订单表', 'columns' => $columns));
	}	
	public function main()
	{
		global $_W;
		global $_GPC;
		$operation = (!empty($_GPC['op']) ? $_GPC['op'] : 'display');
		$years = array();
		$current_year = date('Y');
		$year = (empty($_GPC['year']) ? $current_year : $_GPC['year']);
		$i = $current_year - 10;

		while ($i <= $current_year) {
			$years[] = array('data' => $i, 'selected' => $i == $year);
			++$i;
		}

		if (empty($starttime) || empty($endtime)) {
			$starttime = strtotime('-1 month');
			$endtime = time();
		}

		$months = array();
		$current_month = date('m');
		$month = $_GPC['month'];
		$i = 1;

		while ($i <= 12) {
			$months[] = array('data' => $i, 'selected' => $i == $month);
			++$i;
		}

		


		$day = intval($_GPC['day']);
		$type = intval($_GPC['type']);
		$byType = $_GPC['byType'];
		$status = intval($_GPC['status']);
		$list = array();
		$totalcount = 0;
		$maxcount = 0;
		$maxcount_date = '';
		$maxdate = '';
		$countfield = (empty($type) ? 'sum(price)' : 'count(*)');
		$typename = (empty($type) ? '交易额' : '交易量');
		$dataname = (empty($month) ? '月份' : '日期');



		if($byType=="distributor"){

			if ($_GPC['searchtime']=='timequantum') {

				$starttime = strtotime($_GPC['time']['start']);
				$endtime =   strtotime($_GPC['time']['end']);
				$timestart = $_GPC['time']['start'];
				$timeend =   $_GPC['time']['end'];

			}

			$distributorgroup = pdo_fetchall("SELECT distributor from".tablename('ewei_shop_store')." WHERE uniacid=:uniacid and distributor!='' group by distributor ",array(':uniacid'=>$_W['uniacid']));

			$distributorlist=array();
			foreach ($distributorgroup as $key => $value) {
				$distributorlist[] = m('member')->getMember($value['distributor']);

			}
		}elseif($byType=="store"){

			if(!$month){
				$beginmonth=1;
				$endmonth=12;	
			}else{
				$beginmonth=$endmonth=$month;
			}
			if(!$day){
				$beginday=1;
				$endday=get_last_day($year, $endmonth);;	
			}else{
				$beginday=$endday=$day;
			}
			if ($_GPC['searchtime']=='timequantum') {

				$starttime = strtotime($_GPC['time']['start']);
				$endtime =   strtotime($_GPC['time']['end']);
				$timestart = $_GPC['time']['start'];
				$timeend =   $_GPC['time']['end'];

			}else{
				$starttime=strtotime($year . '-' . $beginmonth . '-' . $beginday . ' 00:00:00');
				$endtime=strtotime($year . '-' . $endmonth . '-' . $endday . ' 23:59:59');
			}

			
			$array=array(':uniacid' => $_W['uniacid'], ':starttime' => $starttime, ':endtime' => $endtime);


			$storeList=pdo_fetchall('SELECT id as storeid,storename FROM ' . tablename('ewei_shop_store') . ' WHERE uniacid=:uniacid', array(':uniacid'=>$_W[uniacid]));
			
			foreach($storeList as $k=>$t){
				$nexthour = $hour + 1;
				
				if ($status) {
					$count=pdo_fetchcolumn('SELECT ifnull(' . $countfield . ',0) as cnt FROM ' . tablename('ewei_shop_order') . ' WHERE uniacid=:uniacid and status='.$status.' and storeid='.$t['storeid'].' and paytime >=:starttime and paytime <=:endtime', $array);
				}else{
					$count=pdo_fetchcolumn('SELECT ifnull(' . $countfield . ',0) as cnt FROM ' . tablename('ewei_shop_order') . ' WHERE uniacid=:uniacid and status>=1 and storeid='.$t['storeid'].' and paytime >=:starttime and paytime <=:endtime', $array);
				}


				
				
				
				$dr = array('data' => $t['storename'], 'count' =>$count,'storeid'=>$t['storeid']);
				$totalcount += $dr['count'];

				if ($maxcount < $dr['count']) {
					$maxcount = $dr['count'];
					$maxcount_date = $t['storename'];
				}

				$list[] = $dr;
				++$hour;
			}
		}else{
			if (!empty($year) && !empty($month) && !empty($day)) {
				$hour = 0;

				while ($hour < 24) {
					$nexthour = $hour + 1;

					if ($status) {
						$count=pdo_fetchcolumn('SELECT ifnull(' . $countfield . ',0) as cnt FROM ' . tablename('ewei_shop_order') . ' WHERE uniacid=:uniacid and status='.$status.' and paytime >=:starttime and paytime <=:endtime', array(':uniacid' => $_W['uniacid'], ':starttime' => strtotime($year . '-' . $month . '-' . $day . ' ' . $hour . ':00:00'), ':endtime' => strtotime($year . '-' . $month . '-' . $day . ' ' . $hour . ':59:59')));
					}else{

						$count=pdo_fetchcolumn('SELECT ifnull(' . $countfield . ',0) as cnt FROM ' . tablename('ewei_shop_order') . ' WHERE uniacid=:uniacid and status>=1 and paytime >=:starttime and paytime <=:endtime', array(':uniacid' => $_W['uniacid'], ':starttime' => strtotime($year . '-' . $month . '-' . $day . ' ' . $hour . ':00:00'), ':endtime' => strtotime($year . '-' . $month . '-' . $day . ' ' . $hour . ':59:59')));
					}
					$dr = array('data' => $hour . '点 - ' . $nexthour . '点', 'count' =>$count );
					$totalcount += $dr['count'];
					
					if ($maxcount < $dr['count']) {
						$maxcount = $dr['count'];
						$maxcount_date = $year . '年' . $month . '月' . $day . '日 ' . $hour . '点 - ' . $nexthour . '点';
					}

					$list[] = $dr;
					++$hour;
				}
			}else {
				if (!empty($year) && !empty($month)) {
					$lastday = get_last_day($year, $month);
					$d = 1;

					while ($d <= $lastday) {

						if ($status) {
							$count= pdo_fetchcolumn('SELECT ifnull(' . $countfield . ',0) as cnt FROM ' . tablename('ewei_shop_order') . ' WHERE uniacid=:uniacid and status='.$status.' and isparent=0 and paytime >=:starttime and paytime <=:endtime', array(':uniacid' => $_W['uniacid'], ':starttime' => strtotime($year . '-' . $month . '-' . $d . ' 00:00:00'), ':endtime' => strtotime($year . '-' . $month . '-' . $d . ' 23:59:59')));
						}else{
							$count=pdo_fetchcolumn('SELECT ifnull(' . $countfield . ',0) as cnt FROM ' . tablename('ewei_shop_order') . ' WHERE uniacid=:uniacid and status>=1 and isparent=0 and paytime >=:starttime and paytime <=:endtime', array(':uniacid' => $_W['uniacid'], ':starttime' => strtotime($year . '-' . $month . '-' . $d . ' 00:00:00'), ':endtime' => strtotime($year . '-' . $month . '-' . $d . ' 23:59:59')));
						}

						$dr = array('data' => $d, 'count' =>$count );
						$totalcount += $dr['count'];

						if ($maxcount < $dr['count']) {
							$maxcount = $dr['count'];
							$maxcount_date = $year . '年' . $month . '月' . $d . '日';
						}

						$list[] = $dr;
						++$d;
					}
				}
				else {
					if (!empty($year)) {
						foreach ($months as $k => $m) {
							$lastday = get_last_day($year, $k + 1);
							if ($status) {
								$count=pdo_fetchcolumn('SELECT ifnull(' . $countfield . ',0) as cnt FROM ' . tablename('ewei_shop_order') . ' WHERE uniacid=:uniacid and status='.$status.' and paytime >=:starttime and paytime <=:endtime', array(':uniacid' => $_W['uniacid'], ':starttime' => strtotime($year . '-' . $m['data'] . '-01 00:00:00'), ':endtime' => strtotime($year . '-' . $m['data'] . '-' . $lastday . ' 23:59:59')));
							}else{
								$count=pdo_fetchcolumn('SELECT ifnull(' . $countfield . ',0) as cnt FROM ' . tablename('ewei_shop_order') . ' WHERE uniacid=:uniacid and status>=1 and paytime >=:starttime and paytime <=:endtime', array(':uniacid' => $_W['uniacid'], ':starttime' => strtotime($year . '-' . $m['data'] . '-01 00:00:00'), ':endtime' => strtotime($year . '-' . $m['data'] . '-' . $lastday . ' 23:59:59')));
							}
							
							$dr = array('data' => $m['data'], 'count' =>$count);
							$totalcount += $dr['count'];


							if ($maxcount < $dr['count']) {
								$maxcount = $dr['count'];
								$maxcount_date = $year . '年' . $m['data'] . '月';
							}

							$list[] = $dr;
						}
					}
				}
			}
		}	
		$diycount=getdiycount();
		if($diycount['order']>1){
			$totalcount=$totalcount*$diycount['order'];
			$maxcount=$maxcount*$diycount['order'];
		}
		foreach ($list as $key => &$row) {
			$diycount=getdiycount();
			if($diycount['order']>1){
				$row['count']=$row['count']*$diycount['order'];
				$list[$key]['percent']=$row['count'];
			}
			$list[$key]['percent'] = number_format(($row['count'] / (empty($totalcount) ? 1 : $totalcount)) * 100, 2);
		}



		unset($row);

		if ($_GPC['export'] == 1) {
			ca('statistics.sale.export');
			$list[] = array('data' => $typename . '总数', 'count' => $totalcount);
			$list[] = array('data' => '最高' . $typename, 'count' => $maxcount);
			$list[] = array('data' => '发生在', 'count' => $maxcount_date);
			m('excel')->export($list, array(
	'title'   => '交易报告-' . (!empty($year) && !empty($month) ? $year . '年' . $month . '月' : $year . '年'),
	'columns' => array(
		array('title' => $dataname, 'field' => 'data', 'width' => 12),
		array('title' => $typename, 'field' => 'count', 'width' => 12),
		array('title' => '所占比例(%)', 'field' => 'percent', 'width' => 24)
		)
	));
			plog('statistics.sale.export', '导出销售统计');
		}

		include $this->template('statistics/sale');
	}
}

?>
