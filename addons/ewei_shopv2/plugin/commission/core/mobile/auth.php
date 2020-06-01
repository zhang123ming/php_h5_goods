<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

require EWEI_SHOPV2_PLUGIN . 'commission/core/page_login_mobile.php';
class Auth_EweiShopV2Page extends CommissionMobileLoginPage
{
	public function main()
	{
		global $_W;
		global $_GPC;

		$mid = intval($_GPC['mid']);
		$openid = $_W['openid'];
		$member = m('member')->getMember($openid);
		$share_set = set_medias(m('common')->getSysset('share'), 'icon');
		$can = false;
		if (empty($member['isagent']) || empty($member['status'])) {
			header('location: ' . mobileUrl('commission/register'));
			exit();
		}

		$set = $this->set;

		if ($_W['ispost']) {
			$isrefresh=$_GPC['refresh'];

			if(!empty($isrefresh)){
				$auth = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_auth') . ' WHERE uniacid=:uniacid and isdefault=1 limit 1',array(':uniacid'=>$_W['uniacid']));
	            if(empty($auth)){
	                return '';
	            }

				load()->func('file');
            	$path = IA_ROOT . '/addons/ewei_shopv2/data/auth/' . $_W['uniacid'] . '/';
		        $md5  = md5(json_encode(array('siteroot' => $_W['siteroot'], 'openid' => $member['openid'], 'bg' => $auth['bg'], 'data' => $auth['data'], 'version' => 1)));
		        $file = $md5 . '.png';

		        @unlink($path.$file);
			}
			$p = p('poster');
			$img = '';
			$img = $p->createCommissionPoster($openid, 0, 5);
			
			show_json(1, array('img' => $img . '?t=' . TIMESTAMP));
		}
		include $this->template();
	}
}

?>
