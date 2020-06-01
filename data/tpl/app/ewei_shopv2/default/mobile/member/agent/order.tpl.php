<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('commission/common', TEMPLATE_INCLUDEPATH)) : (include template('commission/common', TEMPLATE_INCLUDEPATH));?>
<div class="fui-page fui-page-current page-commission-order">
    <div class="fui-header">
        <div class="fui-header-left">
            <a class="back"></a>
        </div>
        <div class="title">订单详情</div>
		 
    </div>
    <div class="fui-content" style="padding-bottom:10px;">
        <div class='fui-cell-group' style="margin-top:0px;color: #ffsf;">
            <div class='fui-cell'>
                <div class='fui-cell-info' style='width:auto;'>订单总额：<?php echo $orderprice?$orderprice:0?>元</div>
            </div>
        </div>
        <div class='content-empty' style='display:none;'>
            <i class='icon icon-list'></i><br/>暂时没有任何订单
        </div>
        <div  id="container"></div>
        <div class='infinite-loading'><span class='fui-preloader'></span><span class='text'> 正在加载...</span></div>
   </div>
</div>
<input type="hidden" id="userid" name="userid" value="<?php  echo $userid;?>">
<script id='tpl_commission_order_list' type='text/html'>
    <%each list as order%>
    <div class='fui-list-group'>
        <%each order.goods as g%>
        <div class="fui-list no-border" style="height: 3.25rem;margin-top: .1rem">
            <div class="fui-list-media">
                <img src="<%g.thumb%>" class="round" style='width:2.5rem;height:2.5rem;'>
            </div>
            <div class="fui-list-inner">
                <div class="row">
                    <div class="row-text" style="font-size: .6rem;color: #000"><%g.title%></div>
                </div>
                <div class="subtitle" style="font-size: .6rem;color: #999;"><%g.optionname%>x<%g.total%></div>
            </div>
            <div class="row-remark">
                ￥<%g.realprice%>
            </div>
        </div>
        <%/each%>
        <div class='fui-list'>
            <span class="left" style="font-size: .55rem;color: #999;line-height: .90rem">订单编号：<%order.ordersn%><br>下单时间：<%order.createtime%>
            </span>
        </div>
    </div>
    <%/each%>
</script>

<script language='javascript'>
    require(['../addons/ewei_shopv2/static/js/app/biz/member/order.js'], function (modal) {
    modal.init({fromDetail:false});
});
</script>
<!-- <?php  $this->footerMenus()?> -->
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>

