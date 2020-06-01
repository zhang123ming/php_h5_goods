<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Info_EweiShopV2Page extends MobileLoginPage
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
			$user_diyform_open = $set_config['user_diyform_open'];

			if ($user_diyform_open == 1) {
				$template_flag = 1;
				$diyform_id = $set_config['user_diyform'];

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
		$returnurl = urldecode(trim($_GPC['returnurl']));
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
		$memberdata = $_GPC['memberdata'];

		if ($template_flag == 1) {
			$data = array();
			$m_data = array();
			$mc_data = array();
			$insert_data = $diyform_plugin->getInsertData($fields, $memberdata);
			$data = $insert_data['data'];
			$m_data = $insert_data['m_data'];
			$mc_data = $insert_data['mc_data'];
			$m_data['diymemberid'] = $diyform_id;
			$m_data['diymemberfields'] = iserializer($fields);
			$m_data['diymemberdata'] = $data;
			$temp_data = iunserializer($data);
			foreach ($temp_data as $key => $v) {
				if(!empty($v['province'])) $m_data['province'] = $v['province'];
				if(!empty($v['city'])) $m_data['city'] = $v['city'];
				if(!empty($v['area'])) $m_data['area'] = $v['area'];
			}
			unset($mc_data['credit1']);
			unset($m_data['credit2']);
			pdo_update('ewei_shop_member', $m_data, array('openid' => $_W['openid'], 'uniacid' => $_W['uniacid']));

			if (!empty($this->member['uid'])) {
				if (!empty($mc_data)) {
					unset($mc_data['credit1']);
					unset($mc_data['credit2']);
					m('member')->mc_update($this->member['uid'], $mc_data);
				}
			}
		}
		else {
			$arr = array('realname' => trim($memberdata['realname']), 'weixin' => trim($memberdata['weixin']), 'birthyear' => intval($memberdata['birthyear']), 'birthmonth' => intval($memberdata['birthmonth']), 'birthday' => intval($memberdata['birthday']), 'province' => trim($memberdata['province']), 'city' => trim($memberdata['city']),'area' => trim($memberdata['area']), 'datavalue' => trim($memberdata['datavalue']), 'mobile' => trim($memberdata['mobile']));
			if ((empty($_W['shopset']['app']['isclose']) && !empty($_W['shopset']['app']['openbind'])) || !empty($_W['shopset']['wap']['open'])) {
				unset($arr['mobile']);
			}

			pdo_update('ewei_shop_member', $arr, array('openid' => $_W['openid'], 'uniacid' => $_W['uniacid']));

			if (!empty($this->member['uid'])) {
				$mcdata = $_GPC['mcdata'];
				unset($mcdata['credit1']);
				unset($mcdata['credit2']);
				m('member')->mc_update($this->member['uid'], $mcdata);
			}
		}
		if($_W['shopset']['shop']['open_wxwork']==1 && $_W['shopset']['shop']['corp_id']){
			load()->func("communication");
			//企业ID, 通讯录tsecret,应用asecret
			$mch_id = pdo_fetch("select * from " . tablename("ybmp_corp_conf") . " where tsecret=:tsecret and corp_id=:corp_id", array( ":tsecret" => $_W['shopset']['shop']['corp_tsecret'], ":corp_id" => $_W['shopset']['shop']['corp_id'] ));
			$members = pdo_fetch('select uniacid,uid,openid,mobile,weixin,status,isagent,realname,nickname,avatar from ' . tablename('ewei_shop_member') . ' where uniacid=:uniacid and openid=:openid and isblack = 0', array(':uniacid' => $_W['uniacid'],':openid'=>$_W['openid']));
			$url = "http://s100.kemanduo.cc/app/index.php?i=".$mch_id['mch_id']."&c=entry&a=wxapp&do=Card_useradd&m=yb_mingpian";
			$data = $members;
			$data['mch_id'] = $mch_id['mch_id'];
			ihttp_post($url,$data);
		}

		show_json(1);
	}

	public function face()
	{
		global $_W;
		global $_GPC;

		$member = $this->member;
		// echo "<pre>";
		// var_dump($member['province']);die;
		$areas =$member['province']." ".$member['city'];
		$addressarea = $member['area'];
		$show_data = 1;
		$area_set = m('util')->get_area_config_set();
		$new_area = intval($area_set['new_area']);
		$address_street = intval($area_set['address_street']);
		if ((empty($member['datavalue']))) {
			$show_data = 0;
		}

		if ($_W['ispost']) {	
			$areas = $_GPC['areas'];
			$areaarr = explode(" ",$areas);
			$nickname = trim($_GPC['nickname']);
			$avatar = trim($_GPC['avatar']);
			$province = $areaarr[0];
			$cityarea = $areaarr[1]." ".$areaarr[2];
			$address = $_GPC['address'];
			$datavalue = $_GPC['datavalue'];
			$isdefault = 1;
			if (empty($nickname)) {
				show_json(0, '请填写昵称');
			}

			if (empty($avatar)) {
				show_json(0, '请上传头像');
			}

			pdo_update('ewei_shop_member', array('avatar' => $avatar, 'nickname' => $nickname,"province" => $province,"city" =>$cityarea,"area" => $address,"datavalue" =>$datavalue), array('id' => $member['id'], 'uniacid' => $_W['uniacid']));
			show_json(1);
		}

		include $this->template();
	}
}

?>
