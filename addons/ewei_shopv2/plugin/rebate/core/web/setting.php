<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Setting_EweiShopV2Page extends PluginWebPage
{
	public function main()
	{
		global $_W;
		global $_GPC;

		include $this->template();

	}
}

?>
