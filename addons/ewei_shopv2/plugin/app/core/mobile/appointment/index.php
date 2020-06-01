<?php
if (!(defined('IN_IA'))) {
	exit('Access Denied');
}


require EWEI_SHOPV2_PLUGIN . 'app/core/page_mobile.php';
class Index_EweiShopV2Page extends AppMobilePage
{
	protected function diyformData()
	{
		global $_W;
		global $_GPC;
		$diyform_plugin = p('diyform');
		$order_formInfo = false;
		$diyform_set = false;
		$orderdiyformid = 0;
		$fields = array();
		$f_data = array();


		if ($diyform_plugin) {
			$diyform_set = $_W['shopset']['diyform'];

			if (!(empty($diyform_set['count_diyform_open']))) {
				$orderdiyformid = intval($diyform_set['count_diyform']);
				if (!(empty($orderdiyformid))) {
					$order_formInfo = $diyform_plugin->getDiyformInfo($orderdiyformid);
					$fields = $order_formInfo['fields'];
					$f_data = $diyform_plugin->getDiyformData($diyform_data, $fields, $this->member);
				}

			}

		}


		$appDatas = array();

		if ($diyform_plugin) {
			$appDatas = $diyform_plugin->wxApp($fields, $f_data, $this->member);
		}

		return array('diyform_plugin' => $diyform_plugin, 'order_formInfo' => $order_formInfo, 'diyform_set' => $diyform_set, 'orderdiyformid' => $orderdiyformid, 'fields' => $appDatas['fields'], 'f_data' => $appDatas['f_data']);
	}

	public function main()
	{
		global $_W;
		global $_GPC;
		$diyform_data = $this->diyformData();
		$openbind = $_W['shopset']['diyform']['openbind'];
		extract($diyform_data);
		$member = $this->member;
		$result = array(
			'fields'  => (!(empty($order_formInfo)) ? $fields : false),
			'f_data'  => (!(empty($order_formInfo)) ? $f_data : false),
			'openbind' =>$openbind,
			);
		app_json($result);
	}

	public function submit()
	{
		global $_W;
		global $_GPC;
		$data = array();
		$diyform_data = $this->diyformData();
		extract($diyform_data);					
		if ($diyform_plugin) {
			$diydata = $_GPC['diydata'];

			if (is_string($diydata)) {
				$diyformdatastring = htmlspecialchars_decode(str_replace('\\', '', $diydata));
				$diydata = @json_decode($diyformdatastring, true);
			}
			foreach ($fields as $v) {
				if (!empty($v['tp_must'])) {
					if (empty($diydata[$v['diy_type']])) {
						show_json(0,'请输入'.$v['tp_name']);	
					}
				}
			}

			if (is_array($diydata) && !empty($order_formInfo)) {
				$diyform_data = $diyform_plugin->getInsertData($fields, $diydata, true);
				$idata = $diyform_data['data'];
				$data['diyformfields'] = $diyform_plugin->getInsertFields($fields);
				$data['diyfieldsdata'] = $idata;
			}
		}
		
		$member = m('member')->getMember($_W['openid']);
		$data['openid'] = $_W['openid'];
		$data['uniacid'] = $_W['uniacid'];
		$data['createTime'] = time();
		$data['nickname'] = $member['nickname'];
		$data['mobile'] = $_GPC['mobile'];
		$allcount = $_W['shopset']['diyform']['count_diyform_sum'];
		$sqlcount = pdo_fetchcolumn('SELECT count(*) FROM'.tablename('ewei_shop_diyform_information').'where uniacid='.$_W['uniacid']." and status=0 and openid='".$_W['openid']."'");
		if (empty($allcount)||$allcount>$sqlcount) {
			pdo_insert('ewei_shop_diyform_information', $data);
			pdo_update('ewei_shop_member',array("need_up"=>1), array('id'=>$member['id']));	
			app_json(1);	
		}
		else {
			show_json(0,'失败！您已提交过'.$allcount.'条信息！');		
		}			
		
	}
}

?>
