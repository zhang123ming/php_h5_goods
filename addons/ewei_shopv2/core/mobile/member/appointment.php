<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Appointment_EweiShopV2Page extends MobileLoginPage
{
	protected $member;

	public function __construct()
	{
		global $_W;
		global $_GPC;
		parent::__construct();
		$this->member = m('member')->getInfo($_W['openid']);
	}

	protected function diyformData()
	{
		$template_flag = 0;
		$diyform_plugin = p('diyform');		
		if ($diyform_plugin) {
			$set_config = $diyform_plugin->getSet();
			$count_diyform_open = $set_config['count_diyform_open'];

			if ($count_diyform_open == 1) {
				$template_flag = 1;
				$diyform_id = $set_config['count_diyform'];

				if (!empty($diyform_id)) {
					$formInfo = $diyform_plugin->getDiyformInfo($diyform_id);
					$fields = $formInfo['fields'];
					$diyform_data = iunserializer($this->member['diymemberdata']);
					$f_data = $diyform_plugin->getDiyformData($diyform_data, $fields, $this->member);
				}
			}
		}
		return array('template_flag' => $template_flag, 'set_config' => $set_config, 'diyform_plugin' => $diyform_plugin, 'formInfo' => $formInfo, 'diyform_id' => $diyform_id, 'diyform_data' => $diyform_data, 'fields' => $fields, 'f_data' => $f_data);
	}

	public function main()
	{
		global $_W;
		global $_GPC;
		$diyform_data = $this->diyformData();
		extract($diyform_data);
		$returnurl = $_SERVER["HTTP_REFERER"];
		$member = $this->member;
		$wapset = m('common')->getSysset('wap');
		$area_set = m('util')->get_area_config_set();
		$new_area = intval($area_set['new_area']);
		$show_data = 1;
		if ((!empty($new_area) && empty($member['datavalue'])) || (empty($new_area) && !empty($member['datavalue']))) {
			$show_data = 0;
		}

		include $this->template();
	}

	public function submit()
	{
		global $_W;
		global $_GPC;
		$diyform_data = $this->diyformData();
		extract($diyform_data);		
		$member = m('member') ->getMember($_W['openid']);
		$memberdata = $_GPC['memberdata'];
		$data = array();
		$insert_data = $diyform_plugin->getInsertData($fields, $memberdata);
		$data['diyformfields'] = iserializer(array($fields));
		// $data['diyformfields'] = $diyform_plugin->getInsertFields($fields);	
		$data['diyfieldsdata'] = 	$insert_data['data'];		
		$member = m('member')->getMember($_W['openid']);
		$data['openid'] = $_W['openid'];
		$data['uniacid'] = $_W['uniacid'];
		$data['createTime'] = time();
		$data['nickname'] = $member['nickname'];	
		$allcount = $_W['shopset']['diyform']['count_diyform_sum'];
		$sqlcount = pdo_fetchcolumn('SELECT count(*) FROM'.tablename('ewei_shop_diyform_information').'where uniacid='.$_W['uniacid']." and status=0 and openid='".$_W['openid']."'");
		if (empty($allcount)||$allcount>$sqlcount) {
			pdo_insert('ewei_shop_diyform_information', $data);
			pdo_update('ewei_shop_member',array("need_up"=>1), array('id'=>$member['id']));	
			show_json(1);	
		}
		else {
			show_json(0,'失败！您已提交过'.$allcount.'条信息！');		
		}
	}
}

?>
