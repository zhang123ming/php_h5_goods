<?php
// if (!defined('IN_IA')) {
// 	exit('Access Denied');
// }

// class Invoice_EweiShopV2Page extends WebPage
// {

// 	public function invoice_record(){


// 		include $this->template();
// 	}
// }
?>
<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Invoice_EweiShopV2Page extends WebPage
{
	public function __construct($_com = 'verify')
	{
		parent::__construct($_com);
	}
	public function invoice()
	{		
		global $_W;
		global $_GPC;
		$condition = ' a.uniacid = :uniacid ';
		$params = array(':uniacid' => $_W['uniacid']);

		if ($_GPC['status'] != '') {
			$condition .= ' and a.status = :status';
			$params[':status'] = $_GPC['status'];
		}

		if (isset($_GPC['keyword'])) {
			$_GPC['keyword'] = trim($_GPC['keyword']);			
			$condition .= ' and (a.number like :keyword)';
			$params[':keyword'] = '%' . $_GPC['keyword'] . '%';
			// var_dump($params[':keyword'],$_GPC['keyword']);die;
		}
		$sql = "SELECT a.*,b.nickname,b.province,b.city,b.area FROM ". tablename('ewei_shop_invoice') . " a inner join ".tablename('ewei_shop_member')." b on a.openid=b.openid WHERE " . $condition . " ORDER BY itemid desc";
	
		$list = pdo_fetchall($sql, $params);
		include $this->template();
	}
	
	public function add()
	{
		$this->post();
	}

	public function edit()
	{
		$this->post();
	}

	public function delete()
	{
		global $_W;
		global $_GPC;
		$itemid = intval($_GPC['itemid']);	
		 if (empty($itemid)) {
		 	$itemid = (is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0);
		 }
		$items = pdo_fetchall('SELECT itemid FROM ' . tablename('ewei_shop_invoice') . ' WHERE itemid in( ' . $itemid . ' ) AND uniacid=' . $_W['uniacid']);	
		foreach ($items as $item) {
			pdo_delete('ewei_shop_invoice', array('itemid' => $item['itemid']));						
		}

		show_json(1, array('url' => referer()));		
	}


	protected function post()
	{		
		global $_W;
		global $_GPC;
		$itemid = intval($_GPC['itemid']);
		$item = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_invoice') . ' WHERE itemid =:itemid and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':itemid' => $itemid));
		$saler=m('member')->getMember($item['openid']);
		$express_list = m('express')->getExpressList(1);
		if ($_W['ispost']) {
			$data = array('uniacid' => $_W['uniacid'], 'openid' => trim($_GPC['openid']),'uid' => intval($_GPC['uid']),'orderID' => trim($_GPC['orderID']),'expresscom' => trim($_GPC['expresscom']),'expresssn' => trim($_GPC['expresssn']),'express' => trim($_GPC['express']),'amount' => trim($_GPC['amount']),'raised' => trim($_GPC['raised']),'raisedType' => trim($_GPC['raisedType']),'type' => intval($_GPC['type']), 'content' => intval($_GPC['content']), 'status' => intval($_GPC['status']),'updateTime'=>time());
			$data['expresscom']=$express_list[$_GPC['express']]['name'];
			if ($_GPC['raisedType']==1) {
				$data['number'] =$_GPC['number'];
			}
			if (!empty($itemid)) {							
				pdo_update('ewei_shop_invoice', $data, array('itemid' => $itemid, 'uniacid' => $_W['uniacid']));				
			}else{	
				$data['createTime']=time();					
				pdo_insert('ewei_shop_invoice', $data);
				$itemid = pdo_insertid();
			}

			show_json(1, array('url' => webUrl('finance/invoice/invoice')));

		}		
		include $this->template();

	}
	public function setinvoice()
	{		
		global $_W;
		global $_GPC;
		if ($_W['ispost']) {
			ca('sysset.invoice.edit');
			$data = (is_array($_GPC['data']) ? $_GPC['data'] : array());
			m('common')->updateSysset(array('invoice' => $data));	
			plog('sysset.invoice.edit', '修改设置-发票设置');			
			show_json(1);
		}
		
		$url = mobileUrl('invoicing', array(), true);
		
		$data = m('common')->getSysset('invoice');
		
		include $this->template();
	}
	
}

?>
