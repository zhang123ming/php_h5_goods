<?php
defined('IN_IA') or exit('Access Denied');
		global $_W,$_GPC;
        $order_no = $_GPC["order_no"];
        $order = pdo_fetch("SELECT * FROM ".tablename("uni_payorder")." WHERE orderid=:orderid", array(":orderid"=>$order_no));
        if(empty($order)){
            message("订单不存在!",url("shop/member"));
        }
        if($order["status"] <> 1) {
            message("订单待支付状态，如果支付成功请与客服联系!",url("shop/member"));
        }
        if($order["status"] == 1) {
            message("订单支付成功!",url("shop/member"));
            exit;
        }