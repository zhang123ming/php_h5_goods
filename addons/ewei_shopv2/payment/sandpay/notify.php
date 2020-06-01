<?php
class sandPay
{
	public $post;
	public $subject;
	public $body;
	public $strs;
	public $type;
	public $total_fee;
	public $setting;
	public $sec;
	public $isapp = false;

	public function __construct()
	{
		global $_W;
		$sign = $_POST['sign']; //签名
		$signType = $_POST['signType']; //签名方式
		$data = stripslashes($_POST['data']); //支付数据
		$charset = $_POST['charset']; //支付编码
		$result = json_decode($data, true); //data数据
		
	    // file_put_contents('sandpay1.txt', var_export($_POST,true),FILE_APPEND);
	    // file_put_contents('sandpay2.txt', var_export($result,true),FILE_APPEND);
	
		$this->post = $result['body'];
		if (!empty($this->post['refundAmount'])&&isset($this->post['refundAmount'])) {
			$this->type=2;//退款通知
			file_put_contents('sandrefund.txt', var_export($result,true)."\r\n",FILE_APPEND);
		}else{
			$this->type=0;//订单支付通知
			// file_put_contents('sandpay.txt', var_export($result,true)."\r\n",FILE_APPEND);
		}
		$this->type = intval($this->strs[1]);
		$this->total_fee = round($this->post['totalAmount']/100, 2);
		$this->data = $data;
		$this->sign = $sign;
		
		$this->init();
	}

	public function init()
	{
		if ($this->type == '0') {
			$this->order();
		}
		else if ($this->type == '1') {
			$this->recharge();
		}

		exit("respCode=000000");
	}

	/**
     * 订单支付
     */
	public function order()
	{
		// if (!$this->publicMethod()) {
		// 	exit('order');
		// }

		$tid = $this->post['orderCode'];

		if (strexists($tid, 'GJ')) {
			$tids = explode('GJ', $tid);
			$tid = $tids[0];
		}

		$sql = 'SELECT * FROM ' . tablename('core_paylog') . ' WHERE `tid`=:tid and `module`=:module limit 1';
		$params = array();
		$params[':tid'] = $tid;
		$params[':module'] = 'ewei_shopv2';
		$log = pdo_fetch($sql, $params);

		$GLOBALS['_W']['uniacid'] = intval($log['uniacid']);
		$_W['uniacid'] = intval($log['uniacid']);
		$comm = m('common');
		$pubkey_url = IA_ROOT.'/addons/ewei_shopv2/cert/sandpay'.$_W['uniacid'].'.cer';
		$pubkey = $comm->loadX509Cert($pubkey_url);
		$res = $comm->verify($this->data, $this->sign, $pubkey);
		if ($res!=1) {
			exit('file');
		}
		
		if ($this->total_fee != $log['fee']) {
			exit('fail');
		}

		if (!empty($log) && ($log['status'] == '0')) {
			$site = WeUtility::createModuleSite($log['module']);

			if (!is_error($site)) {
				$method = 'payResult';

				if (method_exists($site, $method)) {
					$ret = array();
					$ret['acid'] = $log['acid'];
					$ret['uniacid'] = $log['uniacid'];
					$ret['result'] = 'success';
					$ret['type'] = 'sandpay';
					$ret['from'] = 'return';
					$ret['tid'] = $log['tid'];
					$ret['user'] = $log['openid'];
					$ret['fee'] = $log['fee'];
					//paytype =24 杉德支付
					pdo_update('ewei_shop_order', array('paytype' => 24), array('uniacid' => $log['uniacid'], 'ordersn' => $log['tid']));
					$result = $site->$method($ret);

					if ($result) {
						$record = array();
						$record['status'] = '1';
						$record['type'] = 'sandpay';
						pdo_update('core_paylog', $record, array('plid' => $log['plid']));
						pdo_update('ewei_shop_order', array('paytype' => 24, 'apppay' => $this->isapp ? 1 : 0, 'transid' => $this->post['trade_no']), array('ordersn' => $log['tid'], 'uniacid' => $log['uniacid']));
					}
				}
			}
		}
	}
	/**
     * 会员充值
     */
	public function recharge()
	{
		global $_W;

		if (!$this->publicMethod()) {
			exit('recharge');
		}

		$logno = trim($this->post['out_trade_no']);

		if (empty($logno)) {
			exit();
		}

		$log = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_member_log') . ' WHERE `uniacid`=:uniacid and `logno`=:logno limit 1', array(':uniacid' => $_W['uniacid'], ':logno' => $logno));

		if ($this->post['total_fee'] != $log['money']) {
			exit('fail');
		}

		if (!empty($log) && empty($log['status'])) {
			pdo_update('ewei_shop_member_log', array('status' => 1, 'rechargetype' => 'alipay', 'apppay' => $this->isapp ? 1 : 0, 'transid' => $this->post['trade_no']), array('id' => $log['id']));
			$shopset = m('common')->getSysset('shop');
			m('member')->setCredit($log['openid'], 'credit2', $log['money'], array(0, $shopset['name'] . '会员充值:credit2:' . $log['money']));
			m('member')->setRechargeCredit($log['openid'], $log['money']);
			com_run('sale::setRechargeActivity', $log);
			com_run('coupon::useRechargeCoupon', $log);
			m('notice')->sendMemberLogMessage($log['id']);
		}
	}
}

error_reporting(0);
define('IN_MOBILE', true);
require dirname(__FILE__) . '/../../../../framework/bootstrap.inc.php';
require IA_ROOT . '/addons/ewei_shopv2/defines.php';
require IA_ROOT . '/addons/ewei_shopv2/core/inc/functions.php';
require IA_ROOT . '/addons/ewei_shopv2/core/inc/plugin_model.php';
require IA_ROOT . '/addons/ewei_shopv2/core/inc/com_model.php';

new sandPay();

?>
