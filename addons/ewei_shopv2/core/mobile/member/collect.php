<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Collect_EweiShopV2Page extends MobileLoginPage
{
	public function main()
	{
		global $_W;
		global $_GPC;
		
		include $this->template();
	}

	public function upload()
	{
		global $_W;
		global $_GPC;
		$member = m('member')->getMember($_W['openid']);
		include $this->template();
	}

	public function submit()
	{
		global $_W;
		global $_GPC;
		if(empty($_GPC['number'])||empty($_GPC['images'])){
			show_json(0,'充值金额或凭证为空,请重新提交');
		}
		$images = json_encode($_GPC['images']);
		$data = array(
			'uniacid'=>$_W['uniacid'],
			'openid'=>$_W['openid'],
			'createtime'=>time(),
			'amount'=>$_GPC['number'],
			'status'=>0,
			'images'=>$images
		);
		$res = pdo_insert('ewei_shop_member_collect',$data);
		if($res>0){
			show_json(1,array('remark'=>'充值申请提交成功,请等待管理员审核','url'=>mobileUrl('member')));
		}else{
			show_json(0,'充值申请提交失败,请重新提交');
		}
	}
}

?>
