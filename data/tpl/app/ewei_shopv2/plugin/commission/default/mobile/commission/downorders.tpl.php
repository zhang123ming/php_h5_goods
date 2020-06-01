<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>

<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('commission/common', TEMPLATE_INCLUDEPATH)) : (include template('commission/common', TEMPLATE_INCLUDEPATH));?>
<div class="fui-page fui-page-current page-commission-order">
    <div class="fui-header">
        <div class="fui-header-left">
            <a class="back"></a>
        </div>
        <div class="title">订单明细</div>
		 
    </div>
    <div class="fui-content navbar">
        <div class='fui-cell-group' style='margin-top:0px;background: #fea23d;color: #fff;'>
            <div class='fui-cell'>
                <div class='fui-cell-info statustotal'  style='width:auto;color: #fff;'>累计订单金额：0</div>
            </div>
        </div>
        <div class="fui-tab fui-tab-warning" id="tab" data-id='<?php  echo $_GPC["id"];?>'>
            <a class="active" data-tab='status' >所有</a>
            <a href="javascript:void(0)" data-tab='status0' >待付款</a>
            <a href="javascript:void(0)" data-tab='status1' >待发货</a>
            <a href="javascript:void(0)" data-tab='status2' >待收货</a>
            <a href="javascript:void(0)" data-tab='status3' >已完成</a>
        </div>

        <div class='content-empty' style='display:none;'>
            <i class='icon icon-list'></i><br/>暂时没有任何订单
        </div>
        <div  id="container"></div>
        <div class='infinite-loading'><span class='fui-preloader'></span><span class='text'> 正在加载...</span></div>
   </div>
</div>

<script id='tpl_commission_commissionorders_list' type='text/html'>
    <%each list as order%>
        <div class='fui-list-group order-item' data-orderid="<%order.id%>" data-verifycode="<%order.verifycode%>">
                <div class='fui-list-group-title lineblock2 '>
                    订单号: <%order.ordersn%>
                    <span class='status <%order.statuscss%>' style="float:right;"><%order.statusstr%></span>
                </div>
                <%each order.goods as g%>
                <div class="fui-list goods-list">
                    <div class="fui-list-media" style="<%if g.status==2%><%/if%>">
                        <img data-lazy="<%g.thumb%>" class="">
                    </div>
                    <div class="fui-list-inner">
                        <div class="text goodstitle towline"><%if g.seckill_task%><span class="fui-label fui-label-danger"><%g.seckill_task.tag%></span><%/if%><%g.title%></div>
                        <%if g.status==2%><span class="fui-label fui-label-danger">赠品</span><%/if%>
                        <%if g.optionid!='0'%><div class='subtitle'  style="color:#999;"><%g.optiontitle%></div><%/if%>

                    </div>
                    <div class='fui-list-angle'>
                        &yen; <span class='marketprice'><%g.marketprice%><br/>    <span style="color: #999">x<%g.total%></span>
                    </div>

                </div>

                <%/each%>

                <div class='fui-list-group-title lineblock'>
                    <span class='status noremark'>共 <span><%order.goods.length%></span> 个商品 实付: <span class='text-danger'>&yen; <span class="bigprice"><%order.price%></span></span></span>
                </div>
        </div>
    <%/each%>
</script>
<script language='javascript'>
    require(['../addons/ewei_shopv2/plugin/commission/static/js/downorders.js'], function (modal) {
        modal.init({fromDetail: false,id:"<?php  echo $_GPC['id'];?>"});
    });
</script>
<?php  $this->footerMenus()?>
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>

