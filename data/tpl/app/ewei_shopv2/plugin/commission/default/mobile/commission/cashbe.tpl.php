<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('commission/common', TEMPLATE_INCLUDEPATH)) : (include template('commission/common', TEMPLATE_INCLUDEPATH));?>
<style type="text/css">
    .top-content{
        width:100%;
        height:51.19px;
        display: flex;
        flex-direction: row;
        background: #FEA23D;
        color:#fff;
    }
    .top-content div{
        line-height: 51.19px;
        width:100%;
        height:100%;
    }
</style>
<div class='fui-page  fui-page-current member-log-page'>
    <div class="fui-header">
        <div class="fui-header-left">
            <a class="back"></a>
        </div>
        <div class="title"><?php  echo $this->set['texts']['commission1']?></div>
    </div>

    <div class='fui-content navbar' >

        <div class="top-content" >
            <div class="top-content-left" style="background:<?php  echo $pluginset['subject']['background']?>;" >
                <span style="margin-left: 15px;"><?php  echo $this->set['texts']['commission1']?>：<?php  echo $member['credit3'];?></span>
            </div>
<!--             <div class="top-content-right">
                <div id="cash-rigth">
                <a href="<?php  echo mobileUrl('member/cashbewithdraw')?>" style="color:#fff;font-size:20px;" >我要提现</a>
                </div>
            </div> -->
        </div>
        <!-- <div id="tab" class="fui-tab fui-tab-warning">
            <a data-tab="tab1"  class="external <?php  if($_GPC['type']==0) { ?>active<?php  } ?>" data-type='0'>所有明细</a>
            <a data-tab="tab2" class="external <?php  if($_GPC['type']==1) { ?>active<?php  } ?>"  data-type='1'>
            可提现</a>
            <a data-tab="tab3" class="external <?php  if($_GPC['type']==1) { ?>active<?php  } ?>"  data-type='2'>待审核</a>
            <a data-tab="tab4" class="external <?php  if($_GPC['type']==1) { ?>active<?php  } ?>"  data-type='3'>待打款</a>
            <a data-tab="tab5" class="external <?php  if($_GPC['type']==1) { ?>active<?php  } ?>"  data-type='4'>已打款</a>
        </div> -->


        <div class='content-empty' style='display:none;'>
            <i class='icon icon-searchlist'></i><br/>暂时没有任何记录!
        </div>

        <div class='fui-list-group container' style="display:none;"></div>
        <div class='infinite-loading'><span class='fui-preloader'></span><span class='text'> 正在加载...</span></div>
    </div>

    <script id="tpl_commission_cashbe_list" type="text/html">

        <%each list as log%>
        <div class="fui-list goods-item">

            <div class="fui-list-inner">
                <div class='title'>
                    <span style="width:300px;display:inline-block;">金额 : <%log.amount%> 元</span> 
                </div>
                <div class='text'>
                    <span  style="width:300px;display:inline-block;">时间：<%log.createtime%></span>
                </div>
                <div class='text'>
                    
                    <%if log.detail.totalSale>0%>
                    <span style="margin-left:100px;">月度总业绩 : <%log.detail.totalSale%>元</span>
                    <%/if%>
                </div>
            </div>
            <div class='fui-list-angle'>
               <span ><%log.remark%></span>

            </div>

        </div>
        <%/each%>
    </script>

    <script language='javascript'>require(['biz/commission/cashbe'], function (modal) {
        modal.init({type:"<?php  echo $_GPC['type'];?>"});
    });</script>
    
</div>
<?php  $this->footerMenus()?>
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>
<!--efwww_com-->