<?php
if (!defined('IN_IA')) 
{
	exit('Access Denied');
}
class Index_EweiShopV2Page extends PluginWebPage 
{
	public function main() 
	{
		global $_W;
		if (cv('groupaward.agent')) 
		{
			header('location: ' . webUrl('groupaward/agent'));
			exit();
			return;
		}
		if (cv('groupaward.level')) 
		{
			header('location: ' . webUrl('groupaward/level'));
			exit();
			return;
		}
		if (cv('groupaward.bonus')) 
		{
			header('location: ' . webUrl('groupaward/bonus'));
			exit();
			return;
		}
		if (cv('groupaward.bonus.send')) 
		{
			header('location: ' . webUrl('groupaward/bonus/send'));
			exit();
			return;
		}
		if (cv('groupaward.notice')) 
		{
			header('location: ' . webUrl('groupaward/notice'));
			exit();
			return;
		}
		if (cv('groupaward.set')) 
		{
			header('location: ' . webUrl('groupaward/set'));
			exit();
		}
	}
	public function notice() 
	{
		global $_W;
		global $_GPC;
		if ($_W['ispost']) 
		{
			$data = ((is_array($_GPC['data']) ? $_GPC['data'] : array()));
			m('common')->updatePluginset(array( 'groupaward' => array('tm' => $data) ));
			plog('groupaward.notice.edit', '修改通知设置');
			show_json(1);
		}
		$data = m('common')->getPluginset('groupaward');
		$template_list = pdo_fetchall('SELECT id,title FROM ' . tablename('ewei_shop_member_message_template') . ' WHERE uniacid=:uniacid ', array(':uniacid' => $_W['uniacid']));
		include $this->template();
	}
	public function set() 
	{
		global $_W;
		global $_GPC;
		if ($_W['ispost']) 
		{
			$data = ((is_array($_GPC['data']) ? $_GPC['data'] : array()));
			// var_dump($data);die;
			m('common')->updatePluginset(array('groupaward' => $data));
			m('cache')->set('template_' . $this->pluginname, $data['style']);
			plog('groupaward.set.edit', '修改基本设置' );
			show_json(1, array('url' => webUrl('groupaward/set', array('tab' => str_replace('#tab_', '', $_GPC['tab'])))));
		}
		// $styles = array();
		// $dir = IA_ROOT . '/addons/ewei_shopv2/plugin/' . $this->pluginname . '/template/mobile/';

		// if ($handle = opendir($dir)) 
		// {
		// 	while (($file = readdir($handle)) !== false) 
		// 	{
		// 		if (($file != '..') && ($file != '.')) 
		// 		{
		// 			if (is_dir($dir . '/' . $file)) 
		// 			{
		// 				$styles[] = $file;
		// 			}
		// 		}
		// 	}
		// 	closedir($handle);
		// }
		$data = m('common')->getPluginset('groupaward');
		// $data['awardMode'] = 4;
		$commissionSet = m('common')->getPluginset('commission');
		if ($data['awardMode']==5) {
			$levels = m('member')->getLevels();
		}
		// echo '<pre>';
		// var_dump($data);exit;
		include $this->template();
	}
}
?>