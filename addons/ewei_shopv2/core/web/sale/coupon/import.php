<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

define('IA_ROOT', str_replace('\\', '/', dirname(dirname(__FILE__))));
require_once IA_ROOT . '/framework/library/phpexcel/PHPExcel/Reader/CSV.php';
class Import_EweiShopV2Page extends ComWebPage
{
	public function __construct($_com = 'coupon')
	{
		parent::__construct($_com);
	}

	
	public function main()
	{		
		global $_W;
		global $_GPC;
		$uploadStart = '0';
		$uploadnum = '0';
		$excelurl = $_W['siteroot'] . 'addons\ewei_shopv2\data\import\coupon\test.xlsx';

		//点击了提交操作后读取表格数据
		if ($_W['ispost']) {
			$rows = m('excel')->import('excelfile');

			//file_put_contents(time().".txt", http_build_query($row));
			$num = count($rows);		
			$i = 0;
			$colsIndex = array();
			foreach ($rows[0] as $cols => $col) {
				if ($col == 'id') {
					$colsIndex['id'] = $i;
				}
				if ($col == 'uniacid') {
					$colsIndex['uniacid'] = $i                                                       ;
				}

				if ($col == 'couponname') {
					$colsIndex['couponname'] = $i                                                       ;
				}

				if ($col == 'deduct') {
					$colsIndex['deduct'] = $i;
				}

				if ($col == 'enough') {
					$colsIndex['enough'] = $i;
				}

				if ($col == 'timelimit') {
					$colsIndex['timelimit'] = $i;
				}

				if ($col == 'timestart') {
					$colsIndex['timestart'] = $i;
				}
				if ($col == 'timeend') {
					$colsIndex['timeend'] = $i;
				}
				if ($col == 'limitgoodtype') {
					$colsIndex['limitgoodtype'] = $i;
				}
				if ($col == 'limitgoodids') {
					$colsIndex['limitgoodids'] = $i;
				}
				if ($col == 'couponcode') {
					$colsIndex['couponcode'] = $i;
				}
				if ($col == 'total') {
					$colsIndex['total'] = $i;
				}					
				++$i;
			}
		//把表格数据赋值给数据库对应字段

			$filename = $_FILES['excelfile']['name'];
			$filename = substr($filename, 0, strpos($filename, '.'));		
			$rows = array_slice($rows, 2, count($rows) - 2);
			$items = array();
			$num=0;
			foreach ($rows as $rownu => $col) {
				$item = array();
				$item['id'] = $col[$colsIndex[id]];
				$item['uniacid'] = $col[$colsIndex[uniacid]];
				$item['couponname'] = $col[$colsIndex[couponname]];
				$item['deduct'] = $col[$colsIndex[deduct]];
				$item['enough'] = $col[$colsIndex[enough]];
				$item['timelimit'] = $col[$colsIndex[timelimit]];
				$item['timestart'] = strtotime($col[$colsIndex[timestart]]);
				$item['timeend'] = strtotime($col[$colsIndex[timeend]]);
				$item['limitgoodtype'] = $col[$colsIndex[limitgoodtype]];
				$item['limitgoodids'] = $col[$colsIndex[limitgoodids]];
				$item['couponcode'] = $col[$colsIndex[couponcode]];	
				$item['total'] = $col[$colsIndex[total]];										
				$optionpics = array();			
				$items[] = $item;
				++$num;
			}
// var_dump($item);die;
			session_start();
			$_SESSION['importCSV'] = $items;	//把值先存在session中	
			//echo "<pre>";
			//var_dump($items);die;	
			$uploadStart = '1';     //开始上传
			$uploadnum = $num;
		}

		include $this->template();
	}

	public function fetch()
	{
		global $_GPC;
		set_time_limit(0);
		$num = intval($_GPC['num']);
		$totalnum = intval($_GPC['totalnum']);
		session_start();
		$items = $_SESSION['importCSV'];    //获取session中的数据	
		$ret = $this->save_importcsv($items[$num]);   //存入数据库
		plog('importCSV.main', '优惠券码批量导入' . $ret[id]);
 
		if ($totalnum <= $num + 1) {
			unset($_SESSION['importCSV']);
		}
		exit(json_encode($ret));
	}


	public function save_importcsv($item = array(), $id = 0){
		global $_W; 
		$data = array(
			// 'id' => $_W['id'], 
			'uniacid' =>$_W['uniacid'],
			'couponname' =>$item['couponname'], 
			'deduct' => $item['deduct'], 
			'enough' =>$item['enough'], 
			'timelimit' => $item['timelimit'], 
			'timestart' =>$item['timestart'], 
			'timeend' => $item['timeend'],
			'limitgoodtype'=>$item['limitgoodtype'], 
			'limitgoodids' => $item['limitgoodids'], 
			'couponcode' =>$item['couponcode'], 
			'total' =>$item['total'], 	
			'createtime'=>time(),		
			  );

		/*if (empty($item['merchid'])) {
			$data['discounts'] = '{"type":"0","default":"","default_pay":""}';
		}

		if (!empty($merchid)) {
			if (empty($_W['merch_user']['goodschecked'])) {
				$data['checked'] = 1;
			}
			else {
				$data['checked'] = 0;
			}
		}*/
		pdo_insert('ewei_shop_coupon', $data);//存入数据库
		$id = pdo_insertid();
		// $thumb_url = array();		

		// $data['thumb_url'] = serialize($thumb_url);
		// pdo_insert('ewei_shop_goods', $data);
		// $goodsid = pdo_insertid();
		// $content = $item['content'];			
		// $html = $content;	
		// $d = array('content' => $html);
		// pdo_update('ewei_shop_goods', $d, array('id' => $id));

		return array('result' => '1', 'id' => $id);

	}
	
}

?>
