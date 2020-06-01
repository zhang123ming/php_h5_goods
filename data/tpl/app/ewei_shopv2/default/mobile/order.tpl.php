<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>
<style>
     .fui-list-group-title.lineblock2:before{
        content: "";
        position: absolute;
        left: .5rem;
        bottom: 0;
        right: .5rem;
        height: 1px;
        border-top: 1px solid #ebebeb;
        -webkit-transform-origin: 0 100%;
        -ms-transform-origin: 0 100%;
        transform-origin: 0 100%;
        -webkit-transform: scaleY(0.5);
        -ms-transform: scaleY(0.5);
        transform: scaleY(0.5);
    }
      .active{
        color: #26ad95!important;
       
    }
    .order-list-page .fui-tab.fui-tab-danger a.active:after{
        border-bottom-color: #26ad95!important;
    }
   .cancel{
        color:#999!important;
        border-color: #999!important;
    }
    .payment,.confirm{
        color: #26ad95!important;
        border-color: #26ad95!important;
    }
  .state{
        color: #26ad95!important;
    }
      .see{
        color: #999 !important;
        border-color: #999!important;
    }
    .fui-navbar ~ .fui-content, .fui-content.navbar{
        padding-bottom: 0;
    }
</style>

<div class='fui-page order-list-page '>
    <div class="fui-header">
        <div class="fui-header-left">
            <a class="back"></a>
        </div>
        <div class="title">我的订单</div>
        <div class="fui-header-right">
            <a class="icon icon-delete external">回收站</a>
        </div>
    </div>
    <?php  if($_GPC['status']=='4') { ?>
    <div id="tab" class="fui-tab fui-tab-danger">
        <a href="<?php  echo mobileUrl('order')?>" class="external">其他订单</a>
        <a class='external active'  data-status=''>退/换货</a>
    </div>

    <?php  } else { ?>
        <div id="tab" class="fui-tab fui-tab-danger">
            <a data-tab="tab"  class="external <?php  if($_GPC['status']=='') { ?>active<?php  } ?>" data-status=''>全部</a>
            <a data-tab="tab0" class="external <?php  if($_GPC['status']=='0') { ?>active<?php  } ?>"  data-status='0'>待付款</a>
            <a data-tab="tab1" class="external <?php  if($_GPC['status']=='1') { ?>active<?php  } ?>"  data-status='1'>待发货</a>
            <a data-tab="tab2" class="external <?php  if($_GPC['status']=='2') { ?>active<?php  } ?>"  data-status='2'>待收货</a>
            <a data-tab="tab3" class="external <?php  if($_GPC['status']=='3') { ?>active<?php  } ?>"  data-status='3'>已完成</a>
        </div>
    <?php  } ?>

    <div class='fui-content navbar order-list'>

        <div class='fui-content-inner'>
            <div class='content-empty' style='display:none;'>
                <!--<i class='icon icon-lights'></i><br/>暂时没有任何订单<br/><a href="<?php  echo mobileUrl()?>" class='btn btn-default-o external'>到处逛逛</a>-->
                <img src="<?php echo EWEI_SHOPV2_STATIC;?>images/nolist.png" style="width: 6rem;margin-bottom: .5rem;"><br/><p style="color: #999;font-size: .75rem">您暂时没有任何订单哦！</p><br/><a href="<?php  echo mobileUrl()?>" class='btn btn-sm btn-danger-o external' style="border-radius: 100px;height: 1.9rem;line-height:1.9rem;width:  7rem;font-size: .75rem;color: #26ad95;border-color: #26ad95;">去首页逛逛吧</a>
            </div>

            <div class='container'></div>
            <div class='infinite-loading'><span class='fui-preloader'></span><span class='text'> 正在加载...</span></div>
        </div>
        <?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('_copyright', TEMPLATE_INCLUDEPATH)) : (include template('_copyright', TEMPLATE_INCLUDEPATH));?>
    </div>

    <script id='tpl_order_index_list' type='text/html'>

        <%each list as order%>
        <div class='fui-list-group order-item' data-orderid="<%order.id%>" data-verifycode="<%order.verifycode%>">
            <a href="<?php  echo mobileUrl('order/detail')?>&id=<%order.id%>" data-nocache='true'>
                <div class='fui-list-group-title lineblock2 '>
                    订单号: <%order.ordersn%>
                    <span class='status state <%order.statuscss%>'><%order.statusstr%></span>
                </div>
                <%each order.goods[0].goods as g%>
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
                        &yen; <span class='marketprice'><%g.price/g.total%><br/>    <span style="color: #999">x<%g.total%></span>
                    </div>

                </div>

                <%/each%>

                <div class='fui-list-group-title lineblock'>
                    <span class='status noremark'>共 <span><%order.goods.length%></span> 个商品 实付: <span class='text-danger'>&yen; <span class="bigprice"><%order.price%></span></span></span>
                </div>
            </a>
            <div class='fui-list-group-title lineblock   opblock' >
        <span class='status noremark'>

        <%if order.userdeleted==1%>
            <%if order.status==3 || order.status==-1%>
            <div class="btn btn-default btn-default-o order-deleted" data-orderid="<%order.id%>">彻底删除订单</div>
            <%/if%>
            <%if order.status==3%>
            <div class="btn btn-default btn-default-o order-recover" data-orderid="<%order.id%>">恢复订单</div>
            <%/if%>
        <%/if%>


        <%if order.userdeleted==0%>
      <%if order.status==0%>
        <div class="btn btn-default btn-default-o order-cancel cancel">取消订单
            <select data-orderid="<%order.id%>"class="btn btn-sm btn-default-o">

                <option value="">不取消了</option>
                <option value="我不想买了">我不想买了</option>
                <option value="信息填写错误，重新拍">信息填写错误，重新拍</option>
                <option value="同城见面交易">同城见面交易</option>
                <option value="其他原因">其他原因</option>
            </select>
        </div>
        <%if order.paytype!=3%>
        <a class="btn btn-sm btn-danger-o external payment" href="<?php  echo mobileUrl('order/pay')?>&id=<%order.id%>" data-nocache="true" >付款</a>
        <%/if%>
    <%/if%>

    <%if order.canverify&&order.status!=-1&&order.status!=0%>
    <div class="btn btn-sm btn-danger-o order-verify" data-nocache="true" data-orderid="<%order.id%>" data-verifytype="<%order.verifytype%>" style="margin-left:.5rem;" >
        <i class="icon icon-erweimazhuanhuan"></i>
        <span><%if order.dispatchtype==1%>我要取货<%else%>我要使用<%/if%></span>
    </div>
    <%/if%>

    <%if order.status==3 || order.status==-1%>
    <div class="btn btn-default btn-default-o order-delet see" data-orderid="<%order.id%>">删除订单</div>
    <%/if%>

    <?php  if(empty($trade['closecomment'])) { ?>
    <%if order.status==3 && order.iscomment==1%>
    <a class="btn btn-default btn-default-o" data-nocache="true" href="<?php  echo mobileUrl('order/comment')?>&id=<%order.id%>">追加评价</a>
    <%/if%>

    <%if order.status==3 && order.iscomment==0%>
    <a class="btn btn-sm btn-danger-o confirm" data-nocache="true" href="<?php  echo mobileUrl('order/comment')?>&id=<%order.id%>">评价</a>
    <%/if%>
    <?php  } ?>

    <%if order.status>1 && order.addressid>0%>
    <a class="btn  see btn-default btn-default-o" href="<?php  echo mobileUrl('order/express')?>&id=<%order.id%>" data-nocache="true">查看物流</a>
    <%/if%>

    <%if order.status==2%>
    <div class="btn btn-sm confirm btn-danger-o order-finish" data-orderid="<%order.id%>">确认收货</div>
    <%/if%>

    <%if order.canrefund%>
            <a class="btn btn-default btn-default-o" data-nocache="true" href="<?php  echo mobileUrl('order/refund')?>&id=<%order.id%>"><%if order.status==1%>申请退款<%else%>申请售后<%/if%><%if order.refundstate>0%>中<%/if%></a>
            <%/if%>
        <%/if%>

        </span>
            </div>
        </div>
        <%/each%>
    </script>
    <?php  if(com('verify')) { ?>
    <?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('order/verify', TEMPLATE_INCLUDEPATH)) : (include template('order/verify', TEMPLATE_INCLUDEPATH));?>
    <?php  } ?>
    <script language='javascript'>require(['biz/order/list'], function (modal) {
        modal.init({fromDetail:false,status:"<?php  echo $_GPC['status'];?>",merchid:<?php  echo intval($_GPC['merchid'])?>});
    });</script>

    <?php  $this->footerMenus()?>
</div>

<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>
<!--efwww_com-->