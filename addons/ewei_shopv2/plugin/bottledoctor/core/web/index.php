<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Index_EweiShopV2Page extends PluginWebPage
{
	public function main()
	{
		global $_W;
		include $this->template();
	}

	public function data()
	{
		global $_W;
		$boardcount = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_bottledoctor_category') . ' where uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid']));
		$postcount = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_bottledoctor_communion') . ' where uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid']));
		$replycount = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_bottledoctor_answer') . ' where uniacid=:uniacid  limit 1', array(':uniacid' => $_W['uniacid']));
		
		show_json(1, array('boardcount' => $boardcount, 'postcount' => $postcount, 'replycount' => $replycount));
	}

}

?>
