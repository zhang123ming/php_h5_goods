<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Index_EweiShopV2Page extends PluginWebPage
{
	public function main(){
		global $_W;$_GPC;
		if(cv('routes.routes')){
			header('location: ' . webUrl('routes/routes'));
		}else if(cv('routes.bus')){
			header('location: ' . webUrl('routes/bus'));
		}else{
			header('location: ' . webUrl('routes/staff'));
		}
	}
}

?>