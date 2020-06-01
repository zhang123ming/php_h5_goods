<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Decodeset_EweiShopV2Page extends PluginWebPage
{
	public function main()
	{
		
		global $_W;
		global $_GPC;

		$item = pdo_fetch('select * from ' . tablename('ewei_shop_bottledoctor_decodeset') . ' where  uniacid=:uniacid ', array(':uniacid' => $_W['uniacid']));	
		if ($_W['ispost']) {
			$data = array('uniacid' => $_W['uniacid'],'enabled' => intval($_GPC['enabled']),'timeSet' => $_GPC['timeSet'],'communicationSet' => $_GPC['communicationSet']);

			if (!empty($item)) {
				pdo_update('ewei_shop_bottledoctor_decodeset', $data,array('uniacid' => $_W['uniacid'],'id' =>$item['id']));
			}else{
				pdo_insert('ewei_shop_bottledoctor_decodeset', $data);
   				$itemid = pdo_insertid();
			}
			show_json(1, array('url' => webUrl('bottledoctor/decodeset', array('op' => 'display'))));
		}

		include $this->template();
	}

}

?>
