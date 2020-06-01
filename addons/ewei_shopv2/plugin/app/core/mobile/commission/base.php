<?php

if (!defined('IN_IA')) {
	exit('Access Denied');
}

require EWEI_SHOPV2_PLUGIN . 'app/core/page_mobile.php';
class Base_EweiShopV2Page extends AppMobilePage
{
	public function __construct()
	{
		parent::__construct();
		global $_W;
		global $_GPC;
		if (($_W['action'] != 'commission.register') && ($_W['action'] != 'myshop') && ($_W['action'] != 'share')) {
			$member = $this->member;
			if (($member['isagent'] != 1) || ($member['status'] != 1)) {
				app_error(AppError::$CommissionReg, '非分销商不可访问!');
			}
		}

		$this->model = p('commission');
		$this->set = $this->model->getSet();
	}
}

?>
