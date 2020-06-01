<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}
class Bus_EweiShopV2Page extends PluginWebPage
{
	public function main(){
		global $_W;$_GPC;
		include $this->template("routes/bus/bus");
	}
}

?>