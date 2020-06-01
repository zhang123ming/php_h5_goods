<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class ImportPayments_EweiShopV2Page extends WebPage
{
	public function main()
	{
		global $_W;
		global $_GPC;
		if ($_W['ispost']) {
			$rows = m('excel')->import('excelfile');
			$num = count($rows);
			$time = time();
			$i = 4;
			$err_array = array();
			set_time_limit(0);
			foreach ($rows as $rownum => $col) {

				if ($rownum>=4&&$rownum<$num) {
					$paymentsData['tradeTime'] =gmdate('Y-m-d H:i:s',intval(($col[0] - 25569) * 3600 * 24)); //转换成1970年以来的秒数
					$paymentsData['paymentID'] = $col[1];		
					$paymentsData['orderSn']=$ordersn=str_replace('`','', $col[2]);
					$paymentsData['uniacid']=$_W['uniacid'];
					if ($col['3']=='公众号支付') {

						$paymentsData['tradeType']='wechat';

					}elseif ($col['3']=='小程序支付') {

						$paymentsData['tradeType']='wechat';
					}

					$paymentsData['statusValue']=$statusValue = $col['4'];

					$paymentsData['money']=floatval($col['5']);

					$sql = 'select id,status,refundid from ' . tablename('ewei_shop_order') . ' where ordersn=:ordersn and uniacid=:uniacid and isparent=0 and merchid=:merchid';


					if ($statusValue=='买家已支付') {
						$paymentsData['status']=1;

						$sql .= ' and (status=-1 or status=0) limit 1';

					}else{
						$paymentsData['status']=0;
					}
					
					$order = pdo_fetch($sql, array(':ordersn' => $ordersn, ':uniacid' => $_W['uniacid'], ':merchid' => 0));
					


					if (!empty($order)) {

						if ($statusValue=='买家已支付') {
							$data = array(
								'paytime'=>strtotime($paymentsData['tradeTime']),
								'paytype'=>21,
								'status'=>1
							);
						}elseif ($statusValue=='全额退款完成') {

							$data = array(
								'paytype'=>21,
								'status'=>-1
							);
								
						}
						pdo_update('ewei_shop_order', $data, array('id' => $order['id']));
					}

					$osql="select itemID from ".tablename('ewei_shop_kmd_payments')." where orderSn=:ordersn and uniacid=:uniacid";

	
					$payments_record=pdo_fetch($osql, array(':ordersn' => $ordersn, ':uniacid' => $_W['uniacid']));
					if ($payments_record) {
						$paymentsData['updateTime']=time();
						pdo_update('ewei_shop_kmd_payments', $paymentsData,array('itemID' => $payments_record['itemID']));
					}else{
						$paymentsData['createTime']=time();
						pdo_insert('ewei_shop_kmd_payments', $paymentsData);	
					}
					

				}
				
			}
			unset($col);
			$url = webUrl('order/importPayments');
			$this->message($msg . $tip, $url, '');
		}

		include $this->template();
	}
	public function batch()
	{
		global $_W;
		global $_GPC;
		
	}
	public function import()
	{
		$columns = array();
		$columns[] = array('title' => '订单编号', 'field' => '', 'width' => 32);
		$columns[] = array('title' => '快递单号', 'field' => '', 'width' => 32);
		$columns[] = array('title' => '快递公司', 'field' => '', 'width' => 32);
		m('excel')->temp('批量发货数据模板', $columns);
	}
}

?>
