<?php
if (!(defined('IN_IA'))) 
{
	exit('Access Denied');
}
class Rentadv_EweiShopV2Page extends WebPage 
{
	public function main() 
	{
		global $_W;
		global $_GPC;
		$res = pdo_get('ewei_shop_backpic',array('uniacid'=>$_W['uniacid']));
		if ($_W['ispost']) {
			$data['pics'] = serialize($_GPC['data']);
			$data['uniacid'] = $_W['uniacid'];
			if (empty($res)) {
				pdo_insert('ewei_shop_backpic',$data);
			}else{
				pdo_update('ewei_shop_backpic',$data,array('id'=>$res['id']));
			}
			show_json('1','操作成功');
		}
		$data = unserialize($res['pics']);
		include $this->template();
	}
}
?>