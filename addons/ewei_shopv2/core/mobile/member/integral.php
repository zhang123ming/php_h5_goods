<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Integral_EweiShopV2Page extends MobileLoginPage
{
	public function main()
	{
		global $_W;
		global $_GPC;
	    $condition = ' and uniacid =:uniacid and credittype = "credit1" and openid =:openid';
		$params = array(':uniacid' => $_W['uniacid'], ':openid' => $_W['openid']);
		$list = pdo_fetchall('select * from ' . tablename('mc_credits_record') . ' where 1 ' . $condition . ' order by createtime desc',$params);
        foreach ($list as $key => &$value) {
        	if($value['uid']==0){
        	$value['remark'] = substr($value['remark'],0,strpos($value['remark'], 'O'));
        	}
        	$value['createtime'] = date('Y-m-d H:i', $value['createtime']);
        }
		include $this->template();
	}
}

?>
