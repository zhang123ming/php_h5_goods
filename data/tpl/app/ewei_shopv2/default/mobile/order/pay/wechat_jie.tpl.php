<?php defined('IN_IA') or exit('Access Denied');?><div class="order-verify-hidden order-weixinpay-hidden" style="display: none; z-index: 9999">
    <div class="verify-pop">

        <div class="qrcode" style="top:1rem;">
            <div class="loading"><i class="icon icon-qrcode1"></i> 正在生成二维码</div>
            <img class="qrimg" src="" />
        </div>
        <div class="tip" style="top:270px;">
            <p>支付金额: <span class='text-danger'>￥ <span  id="qrmoney">-</span></span></p>
        </div>
        <div class="tip" style="top:290px;">
            <p>&nbsp;</p>
            <p>&nbsp;</p>
            <p>长按或扫描二维码, 进行订单支付</p>
            <p>支付完成之后, 会自动跳转到支付成功页面</p>
            <p>&nbsp;</p>
            <p>
            <div class="btn btn-default btn-sm" id="btnWeixinJieCancel">取消支付 </div>
            </p>
        </div>
    </div>
</div>
<!--efwww_com-->