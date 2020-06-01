<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}

class Reg_EweiShopV2Page  extends MobileLoginPage
{
    public function main(){
        global $_W,$_GPC;
        $category = pdo_fetchall('select * from ' . tablename('ewei_shop_live_category') . ' where uniacid = ' . $_W['uniacid'] . ' and enabled = 1 ');
        $liveidentity = array(
            array('type' => 1, 'identity' => 'yizhibo', 'name' => '一直播'),
            array('type' => 1, 'identity' => 'inke', 'name' => '映客直播')
        );

        $item = pdo_fetch("SELECT * FROM `ims_ewei_shop_live_host` WHERE uniacid={$_W['uniacid']} and openid='{$_W['openid']}'");

        if($_W['ispost']){
            $host = array(
                'uniacid'=>$_W['uniacid'],
                'openid'=>$_W['openid'],
                'identity'=>$_GPC['liveidentity'],
                'liveurl'=>$_GPC['liveurl'],
                'hostname'=>$_GPC['hostname'],
                'salecate'=>$_GPC['salecate'],
                'realname'=>$_GPC['realname'],
                'phone'=>$_GPC['phone'],
                'idcard1'=>$_GPC['idcard1'],
                'idcard2'=>$_GPC['idcard2'],
                'status'=>0,
                'applytime'=>time()
            );

            if($item){
                $res=pdo_update('ewei_shop_live_host',$host,array('openid'=>$_W['openid']));
            }else{
                $res=pdo_insert('ewei_shop_live_host',$host);
            }
            if($res){
                show_json(1);
            }else{
                show_json(0,'提交失败');
            }
        }
        include $this->template();
    }
}

?>