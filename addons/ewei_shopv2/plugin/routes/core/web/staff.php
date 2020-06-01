<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}
class Staff_EweiShopV2Page extends PluginWebPage
{
	public function main(){
		global $_W;$_GPC;
		include $this->template();
	}
}

?>