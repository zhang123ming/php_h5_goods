<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>
<link rel="stylesheet" type="text/css" href="../addons/ewei_shopv2/template/mobile/default/static/css/coupon.css?v=2.0.0">
<link rel="stylesheet" type="text/css" href="../addons/ewei_shopv2/template/mobile/default/static/css/coupon-new.css?v=2017030302">
<style>
    .yen{border:none;height:0.75rem;width:0.75rem;display: inline-block;background: #ff4753;color:#fff;font-size:0.4rem;line-height: 0.8rem;text-align: center;
        font-style: normal;border-radius: 0.75rem;-webkit-border-radius: 0.75rem;}
    .order-create-page .fui-list.goods-item .fui-list-inner{
        height: 4.5rem;
        align-self: center;
    }
    .order-create-page .fui-list.goods-item .fui-list-angle{
        height: 4.5rem;
        align-self: center;
    }
    .order-create-page .fui-list-inner .subtitle{
        display: block;
    }
</style>
<div class='fui-page order-create-page'>
    <div class="fui-header">
        <div class="fui-header-left">
            <a class="back"></a>
        </div>
        <div class="title"><?php  echo $headtitle;?></div>
        <div class="fui-header-right" data-nomenu="true">&nbsp;</div>
    </div>
    <div class='fui-content  navbar'>
        <div class="bg-gray">
        <?php  if(count($carrier_list)>0 && !$isverify && !$isvirtual&&!$isonlyverifygoods) { ?>
        <div id="carrierTab" class="fui-tab fui-tab-danger" style="margin-bottom: 0">
            <a data-tab="tab1" class="active">快递配送</a>
            <?php  if(empty($notselfverify)) { ?>
            <a data-tab="tab2">上门自提</a>
            <?php  } ?>
        </div>
        <?php  } ?>
        

        <?php  if($hastask2) { ?>
         <div class="fui-cell-group">
            <div class="fui-cell">
                <div class="fui-cell-label">
                    开通服务
                </div>
                <div class="fui-cell-info c000">
                   <div class="fui-cell-remark noremark">
                    <?php  echo $virtualGoods['title'];?>
                   </div>
                </div>
            </div>
        </div>
        <?php  } ?>

        <?php  if(!empty($quickinfo)) { ?>
        <div class="fui-cell-group">
            <a class="fui-cell external" href="<?php  echo mobileUrl('quick', array('id'=>$quickinfo['id']))?>">
                <div class="fui-cell-info">数据来自快速购买: <?php  echo $quickinfo['title'];?></div>
                <div class="fui-cell-remark"></div>
            </a>
        </div>
        <?php  } ?>


        <?php  if(!$isverify && !$isvirtual&&!$isonlyverifygoods) { ?>
            <!--地址选择-->
            <div class="fui-list-group" id='addressInfo' data-addressid="<?php  echo intval($address['id'])?>" style="    margin: 0 0 .5rem;">
                <a  class="fui-list <?php  if(empty($address)) { ?>external<?php  } ?>"
                    <?php  if(empty($address)) { ?>
                        href="<?php  echo mobileUrl('member/address/post')?>"
                    <?php  } else { ?>
                        href="<?php  echo mobileUrl('member/address/selector')?>"
                    <?php  } ?>
                data-nocache="true">
                    <div class="fui-list-media">
                        <!-- <i class="icon icon-dingwei" <?php  if(empty($address)) { ?>style='display:none'<?php  } ?>></i> -->
                        <img style=" width: 1.25rem; height: auto;<?php  if(empty($address)) { ?>display:none<?php  } ?> " src="/addons/ewei_shopv2/static/images/d-address.png" alt="">

                    </div>
                    <div class="fui-list-inner" >
                        <div class="title has-address" <?php  if(empty($address)) { ?>style='display:none'<?php  } ?>>
                        收货人：
                            <span class='realname'><?php  echo $address['realname'];?></span>
                            <span class='mobile'><?php  echo $address['mobile'];?></span>
                        </div>
                        <div class="text has-address" <?php  if(empty($address)) { ?>style='display:none'<?php  } ?>>
                            <span class='address'><?php  echo $address['province'];?><?php  echo $address['city'];?><?php  echo $address['area'];?><?php  if(!empty($new_area) && !empty($address_street)) { ?> <?php  echo $address['street'];?><?php  } ?> <?php  echo $address['address'];?></span>
                        </div>
                        <div class="text no-address" <?php  if(!empty($address)) { ?>style='display:none'<?php  } ?>>暂无地址，请去<i>添加地址</i></div>
                    </div>
                    <div class="fui-list-angle">
                        <div class="angle"></div>
                    </div>
                </a>
            </div>

            <!--自提点选择-->
            <div class="fui-list-group"  id="carrierInfo" style="display: none;margin: 0 0 .5rem">
                <a class="fui-list" href="<?php  echo mobileUrl('store/selector', array('type'=>1,'merchid'=>$merch_id))?>" data-nocache='true'>
                    <div class="fui-list-media" style="display: none">
                        <!-- <i class="icon icon-dingwei"></i> -->
                        <img style=" width: 1.25rem; height: auto;" src="/addons/ewei_shopv2/static/images/d-address.png" alt="">

                    </div>
                    <div class="fui-list-inner">
                        <div class="no-address">
                            <!-- <i class="icon icon-icon02"></i> -->
                             请选择自提门店</div>
                        <div class="title has-address" style="display: none"><span class='storename'>请选择itinerary门店</span></div>
                        <!--<div class="subtitle">-->
                            <!--<span style="overflow: hidden;    display: inline-block;">收货人：</span>-->
                            <!--<span class='realname'><?php  echo $carrier_list[0]['realname'];?></span>-->
                            <!--<span class='mobile' id="carrierInfo_mobile"><?php  echo $carrier_list[0]['mobile'];?></span>-->
                        <!--</div>-->
                        <div class="text" style="display: none"><span class='address'><span class="pickstore">[门店自提]</span><?php  echo $carrier_list[0]['address'];?></span></div>
                    </div>
                    <div class="fui-list-angle">
                        <div class="angle"></div>
                    </div>
                </a>

            </div>
        <?php  } ?>
    <!--联系填写-->
    <?php  if($sysset['set_realname']==0 || $sysset['set_mobile']==0) { ?>
        <div class="fui-cell-group sm" id="memberInfo" <?php  if(!$isverify && !$isvirtual) { ?>style="display:none"<?php  } ?>>
            <?php  if($sysset['set_realname']==0) { ?>
                <div class="fui-cell">
                    <div class="fui-cell-label sm">联系人</div>
                    <div class="fui-cell-info c000"><input type="text" placeholder="请输入联系人" data-set="<?php  echo $sysset['set_realname'];?>" name='carrier_realname' class="fui-input" value="<?php  echo $member['realname'];?>"/></div>
                </div>
            <?php  } ?>
            <?php  if($sysset['set_mobile']==0) { ?>
                <div class="fui-cell">
                    <div class="fui-cell-label sm">联系电话</div>
                    <div class="fui-cell-info c000"><input type="tel" placeholder="请输入联系电话" data-set="<?php  echo $sysset['set_mobile'];?>" name='carrier_mobile' class="fui-input" value="<?php  echo $member['carrier_mobile'];?>"/></div>
                </div>
            <?php  } ?>
        </div>
    <?php  } ?>


<div class="fui-list-group" >
    <?php  $i=0?>


    <?php  if(is_array($goods_list)) { foreach($goods_list as $key => $list) { ?>
    <?php  if($i !=0 ) { ?>
        <div style="height: .5rem;background: #f3f3f3"></div>
    <?php  } ?>
    <?php  $i++?>
    <div class="fui-list-group-title">
            <img style="width: .75rem;vertical-align: text-bottom;margin-right:0.25rem;" src="/addons/ewei_shopv2/static/images/dianpu1.png" alt="">
        <?php  echo $list['shopname'];?></div>
    <?php  if(is_array($list['goods'])) { foreach($list['goods'] as $g) { ?>
    <input type='hidden' name='goodsid[]' value="<?php  echo $g['id'];?>" />
    <input type='hidden' name='optionid[]' value="<?php  echo $g['optionid'];?>" />
    <div class="fui-list goods-item align-start">       
        <?php  if($g['ispackage']==1) { ?>
        <div class="fui-list-media">        
            <img id="" class="round" src="<?php  echo tomedia($g['thumb'])?>">
        </div>
        <div class="fui-list-inner">
            
            <div class="subtitle">
                <?php  if($g['seckillinfo'] && $g['seckillinfo']['status']==0) { ?><span class='fui-label fui-label-danger'><?php  echo $g['seckillinfo']['tag'];?></span><?php  } ?>
                <?php  if(empty($g['isnodiscount']) && !empty($g['dflag'])) { ?><span class='fui-label fui-label-danger'>折扣</span><?php  } ?>
                <?php  if($g['type']==4) { ?><span class='fui-label fui-label-danger'>批发</span><?php  } ?>
                <?php  echo $g['title'];?>
            </div>
            <?php  if($g['subtitle']) { ?>
            <div class="text ">
                <?php  echo $g['subtitle'];?>
            </div>
            <?php  } ?>
            <?php  if(!empty($g['optionid'])) { ?>
            <div class="text ">
                <?php  echo $g['optiontitle'];?>
            </div>
            <?php  } ?>

        </div>
        <?php  } else { ?>
         <div class="fui-list-media">
            <a href="<?php  echo mobileUrl('goods/detail',array('id'=>$g['goodsid']))?>">
                <img id="" class="round" src="<?php  echo tomedia($g['thumb'])?>">
            </a>
        </div>
        <div class="fui-list-inner">
            <a href="<?php  echo mobileUrl('goods/detail',array('id'=>$g['goodsid']))?>">
                <div class="subtitle">
                    <?php  if($g['seckillinfo'] && $g['seckillinfo']['status']==0) { ?><span class='fui-label fui-label-danger'><?php  echo $g['seckillinfo']['tag'];?></span><?php  } ?>
                    <?php  if(empty($g['isnodiscount']) && !empty($g['dflag'])) { ?><span class='fui-label fui-label-danger'>折扣</span><?php  } ?>
                    <?php  if($g['type']==4) { ?><span class='fui-label fui-label-danger'>批发</span><?php  } ?>
                    <?php  echo $g['title'];?>
                </div>


            </a>
            <?php  if($g['subtitle']) { ?>
            <!-- <div class="text ">
                <?php  echo $g['subtitle'];?>
            </div> -->
            <?php  } ?>
            <?php  if(!empty($g['optionid'])) { ?>
            <div class="text ">
                <?php  echo $g['optiontitle'];?>
            </div>
            <?php  } ?>
            <div class="jiage">
                    <span style="font-size: .65rem;color: #000">￥<span class="marketprice"><?php  if($g['packageprice'] > $g['unitprice']) { ?><?php  echo $g['packageprice'];?><?php  } else if($g['marketprice'] > $g['unitprice']) { ?><?php  echo $g['marketprice'];?><?php  } else { ?><?php  echo $g['unitprice'];?><?php  } ?></span></span>
                    <div class="num">
                        <?php  if($taskgoodsprice) { ?>
                        <?php  $total = 1;?>
                        x1<input class="num shownum" type="hidden" name="" value="1"/>
                        <?php  } else if($changenum && !$isgift) { ?>
                        <div class="fui-number small" data-value="<?php  echo $total;?>" data-unit="<?php  echo $g['unit'];?>" data-maxbuy="<?php  echo $g['totalmaxbuy'];?>" data-minbuy="<?php  echo $g['minbuy'];?>" data-goodsid="<?php  echo $g['goodsid'];?>">
                            <div class="minus">-</div>
                            <input class="num shownum" type="tel" name="" value="<?php  echo $total;?>"/>
                            <div class="plus">+</div>
                        </div>
                        <?php  } else { ?>
                        x<?php  echo $g['total'];?><input class="num shownum" type="hidden" name="" value="<?php  echo $total;?>"/>
                        <?php  } ?>
                    </div>
                </div>
        </div>
        <?php  } ?>

    </div>
    <?php  } } ?>
    <?php  } } ?>





    <script type="text/javascript">
        $(function(){
            $(".package-goods-img").height($(".package-goods-img").width());
        })
    </script>
    <div class='fui-cell-group' style="margin-top: 0">
        <?php  if(is_array($giftGood)) { foreach($giftGood as $item) { ?>
        <div class="fui-cell" style="padding:0 ">
            <div class="fui-list goods-item" style="width:100%;">
                <div class="fui-list-media image-media" style="position: initial;">
                    <a href="javascript:void(0);">
                        <img class="round" src="<?php  echo tomedia($item['thumb'])?>" data-lazyloaded="true">
                    </a>
                </div>
                <div class="fui-list-inner">
                    <a href="javascript:void(0);">
                        <div class="text" style="color: #000">
                            <?php  echo $item['title'];?><br /><span class="fui-label fui-label-danger">赠品</span>
                        </div>
                    </a>
                </div>
                <div class='fui-list-angle'>
                    <span class="price" style="display: inline-block;font-size:.65rem;color: #000 ">&yen;<del class='marketprice'><?php  echo $item['marketprice'];?></del></span>
                </div>
            </div>
        </div>
        <?php  } } ?>

        <?php  if(!empty($fullbackgoods)) { ?>
        <div class="fui-cell" id="fullbackgoods" <?php  if($fullbackgoods['minallfullbackallprice']<=0 && $fullbackgoods['minallfullbackallratio']<=0) { ?>style="display: none"<?php  } ?>>
        <div class="fui-cell-label" style='width:auto' >全返详情</div>
        <div class="fui-cell-info" style="text-align: right;">
            <span class="fui-cell-remark noremark" style="font-size: 0.6rem;color:#333;">
                <i class="yen">&yen;</i>
                <?php  if($fullbackgoods['type']>0) { ?>
                <?php  if($fullbackgoods['credit']==1) { ?>总积分<?php  } else { ?>总金额<?php  } ?> <span class="text-danger"><?php  echo price_format($fullbackgoods['minallfullbackallratio'],2)?>%</span> ，每天返<span class="text-danger"><?php  echo price_format($fullbackgoods['fullbackratio'],2)?>%</span>，时间：<span class="text-danger"><?php  echo $fullbackgoods['day'];?></span>天
                <?php  } else { ?>
                <?php  if($fullbackgoods['credit']==1) { ?>总积分<?php  } else { ?>总金额 &yen;<?php  } ?><?php  echo price_format($fullbackgoods['minallfullbackallprice'],2)?>，每天返<?php  echo price_format($fullbackgoods['fullbackprice'],2)?>，时间：<?php  echo $fullbackgoods['day'];?>天
                <?php  } ?>
            </span>
        </div>
    </div>
    <?php  } ?>

    <!-- <div class="fui-cell  lineblock ">
        <div class="fui-cell-info c000" style="text-align: right;">共 <span id='goodscount' class='text-danger bigprice'><?php  echo $total;?></span> 件 实付：<span class="text-danger bigprice">&yen; <span class='<?php  if(!$packageid && empty($exchangeOrder)) { ?>goodsprice<?php  } ?>'><?php  echo number_format($goodsprice,2)?></soan></span></div>
    </div> -->

</div>
</div>

<?php  if($isgift) { ?>
<input type="hidden" name="giftid" id="giftid" value="<?php  echo $giftid;?>">
<div class="fui-cell-group sm ">
    <div class="fui-cell">
        <?php  if(count($gifts)>1) { ?>
        <div class='fui-cell-text fui-cell-giftclick'>
            赠品：<label id="gifttitle">请选择赠品</label>
        </div>
        <?php  } else { ?>
        <?php  if(is_array($gifts)) { foreach($gifts as $item) { ?>
        <div class='fui-cell-text' onclick="javascript:window.location.href='<?php  echo mobileUrl('goods/gift',array('id'=>$item['id']))?>'">
            赠品：<?php  echo $gifttitle;?>
        </div>
        <?php  } } ?>
        <?php  } ?>
        <div class='fui-cell-remark'></div>
    </div>
</div>
<?php  } ?>
<?php  if($invoiceset['set_invoice']==1||$hasinvoice==1) { ?>
<div class="fui-cell-group" style="padding:0.75rem 0.6rem 0.65rem;">
    <div class="invoice-bx">
        <div class="invoice-title">
          <input id="invoiceCheckbox" type="checkbox" value="1" class="invoice-checkbox">
            <label for="invoiceCheckbox">开具发票</label>
        </div>
        <div class="invoice-operate" id="content" style="display: none;font-size: 0.7rem;margin-top:20px;" >    
            <div class="fui-cell">
                <div class="fui-cell-label sm">发票类型</div>
                <div class="fui-cell-info c000">
                    <select name="type" class='form-control input-sm select-md' id="invoiceType" >
                        <option value="1">增值税发票</option>
                        <option value="0">普通发票</option>
                        
                    </select>
                </div>
            </div>
            <div class="fui-cell">
                <div class="fui-cell-label sm">发票内容</div>
                <div class="fui-cell-info c000">
                    <select name="content" id="invoiceContent" class='form-control input-sm select-md'>
                        <option value="0">账目明细</option>
                        <option value="1">商品类型</option>
                    </select>
                </div>
            </div>
            <div class="fui-cell">
                <div class="fui-cell-label sm">抬头类型</div>
                <div class="col-sm-9 col-xs-12">
                    <input id="invoiceCheckbox_personal" type="radio" name="raisedType" data-checked="true" value="0" />
                    个人
                    <input id="invoiceCheckbox_company"  type="radio" name="raisedType" value="1"/>企业
                </div>
            </div>
            <div class="fui-cell">
                <div class="fui-cell-label sm">发票抬头</div>
                <div class="fui-cell-info c000">
                    <input type="text" placeholder="请输入发票抬头" name='raised' class="fui-input" value="" />
                </div>
            </div>
            <div class="invoice-tax-no" style="display: none;">
                <div class="fui-cell">
                    <div class="fui-cell-label sm">识别号</div>
                    <div class="fui-cell-info c000">
                        <input  type="text" placeholder="请输入纳税人识别号"  name='number' class="fui-input" value=""/>
                    </div>
                </div>
                 
            </div>
        </div>
        
    </div> 
</div>

<?php  } ?>
<script type="text/javascript">
    $('#Topush').change(function(){
        if($(this).prop('checked')){
            $('.Topush-operate').css('display','block');
        }else{
            $('.Topush-operate').css('display','none');
        }
    });
</script>

<!-- <div class='fui-cell-group fui-cell-click fui-sale-group noborder'  id="city-picker">
    <a class="external" href="<?php  echo mobileUrl('store/verifystorelist',array('goodsid'=>$_GPC['id'],'mid'=>$_GPC['mid'],'optionid'=>$_GPC['optionid'],'total'=>$_GPC['total'],'levelid'=>$_GPC['levelid']))?>">
        <div class='fui-cell'>
            <div class='fui-cell-text' style="font-size: 0.7rem;line-height: 0.7rem">选择门店</div>
            <div class='fui-cell-remark'><?php  echo $store_name;?></div>
        </div>
    </a>
</div> -->



<?php  if(empty($exchangeOrder) && empty($taskgoodsprice) && empty($packageid) && empty($if_bargain['bargain'])) { ?>
<div class="fui-cell-group">
    <?php  if(empty($nocoupon)) { ?>
    <?php  if($coupondeduct>0) { ?>
    <div class="fui-cell">
        <div class="fui-cell-label" style="width: auto;">优惠券最高可抵 <span id='coupondeduct' class="text-danger"><?php  echo number_format($coupondeduct,2)?></span>元</div>
        <div class="fui-cell-info"></div>
    </div>
    <?php  } ?>
    <div id='coupondiv' class="fui-cell fui-cell-click" <?php  if($couponcount<=0) { ?>style='display:none'<?php  } ?>>
        <div class='fui-cell-label'>优惠券</div>
        <div class='fui-cell-info'></div>
        <div class='fui-cell-remark'>
            <img id="couponloading" src="../addons/ewei_shopv2/static/images/loading.gif" style="vertical-align: middle;display: none;" width="20" alt=""/>
            <div class='badge badge-danger' <?php  if($couponcount<=0) { ?>style='display:none'<?php  } ?>><?php  echo $couponcount;?></div>
        <span class='text' <?php  if($couponcount>0) { ?>style='display:none'<?php  } ?>>无可用</span>
        </div>
    </div>
    <?php  } ?>

<?php  if($deductcredit>0) { ?>
<div class="fui-cell">
    <div class="fui-cell-label" style="width: auto;"> <span id="deductcredit_info" class='text-danger'><?php  echo $deductcredit;?></span> <?php  echo $_W['shopset']['trade']['credittext'];?>可抵扣 ￥<span id="deductcredit_money" class='text-danger'><?php  echo number_format($deductmoney,2)?></span> 元</div>
    <div class="fui-cell-info"></div>
    <div class="fui-cell-remark noremark"><input id="deductcredit" data-credit="<?php  echo $deductcredit;?>" data-money='<?php  echo $deductmoney;?>' type="checkbox" class="fui-switch fui-switch-small fui-switch-danger pull-right"></div>
</div>
<?php  } ?>
<?php  if($deductcredit2>0) { ?>
<div class="fui-cell">
    <div class="fui-cell-label" style="width: auto;"><?php  echo $_W['shopset']['trade']['moneytext'];?>可抵扣 <span id='deductcredit2_money' class="text-danger"><?php  echo number_format($deductcredit2,2)?></span>元</div>
    <div class="fui-cell-info"></div>
    <div class="fui-cell-remark noremark info"><input id="deductcredit2" data-credit2="<?php  echo $deductcredit2;?>" type="checkbox"  class="fui-switch fui-switch-small fui-switch-danger pull-right"></div>
</div>
<?php  } ?>

</div>

<?php  if(!empty($stores)) { ?>
<script language='javascript' src='https://api.map.baidu.com/api?v=2.0&ak=ZQiFErjQB7inrGpx27M1GR5w3TxZ64k7&s=1'></script>
<div class='fui-according-group'>
    <div class='fui-according expanded'>
        <div class='fui-according-header'>
            <!--<i class='icon icon-shop'></i>-->
            <span class="text">适用门店</span>
            <span class="remark" style="margin-right: .2rem"><div class="badge"><?php  echo count($stores)?></div></span>
        </div>
        <div class="fui-according-content store-container  fui-cell-group">
            <?php  if(is_array($stores)) { foreach($stores as $item) { ?>
            <!--<div  class="fui-list store-item" data-lng="<?php  echo floatval($item['lng'])?>" data-lat="<?php  echo floatval($item['lat'])?>">-->
                <!--<div class="fui-list-media">-->
                    <!--<i class='icon icon-shop'></i>-->
                <!--</div>-->
                <!--<div class="fui-list-inner store-inner">-->
                    <!--<div class="title"><span class='storename'><?php  echo $item['storename'];?></span></div>-->
                <!--</div>-->
                <!--<div class="fui-list-angle ">-->
                    <!--&lt;!&ndash;<?php  if(!empty($item['tel'])) { ?><a href="tel:<?php  echo $item['tel'];?>" class='external '><i class=' icon icon-phone' style='color:green'></i></a><?php  } ?>&ndash;&gt;-->
                    <!--&lt;!&ndash;<a href="<?php  echo mobileUrl('store/map',array('id'=>$item['id'],'merchid'=>$item['merchid']))?>" class='external' ><i class='icon icon-location' style='color:#f90'></i></a>&ndash;&gt;-->
                    <!---->
                <!--</div>-->
            <!--</div>-->
            <a  href="<?php  echo mobileUrl('store/detail',array('id'=>$item['id'],'merchid'=>$item['merchid']))?>"  class="fui-cell store-item external"
                data-lng="<?php  echo floatval($item['lng'])?>"
                data-lat="<?php  echo floatval($item['lat'])?>">
                <div class="fui-cell-icon">
                    <!-- <i class='icon icon-dingwei1'></i> -->
                    <img style=" width: .7rem; height: auto;" src="/addons/ewei_shopv2/static/images/d-address.png" alt="">

                </div>
                <div class="fui-cell-text">
                    <div class="title"><span class='storename'><?php  echo $item['storename'];?></span></div>
                </div>
                <div class="fui-cell-remark ">
                    查看
                </div>
            </a>
            <?php  } } ?>
            <?php  if(count($stores)>3) { ?>
            <div class='show-allshop'><span class='show-allshop-btn'>加载更多门店</span></div>
            <?php  } ?>
        </div>
        <div id="nearStore" style="display:none">
            <div class='fui-list store-item'  id='nearStoreHtml'></div>
        </div>
    </div></div>
<?php  } ?>
<?php  } ?>


<div class="fui-cell-group">
    <input type="hidden" id="weight" name='weight' value="<?php  echo $weight;?>" />
    <?php  if(!empty($exchangeOrder)) { ?>
    <div class="fui-cell">
        <div class="fui-cell-label" >兑换券</div>
        <div class="fui-cell-info"></div>
        <div class="fui-cell-remark noremark"><span style="color: red;">- &yen; <?php  echo number_format($exchangecha,2);?></span></div>
    </div>
    <?php  } ?>
    <div class="fui-cell">
        <div class="fui-cell-label" >小计</div>
        <div class="fui-cell-info"></div>
        <div class="fui-cell-remark noremark">&yen; <span class='<?php  if(!$packageid && empty($exchangeOrder)) { ?>goodsprice<?php  } ?>'>
            <?php  if(!empty($exchangeOrder)) { ?><?php  echo $exchangeprice;?><?php  } else if($taskgoodsprice) { ?><?php  echo $taskgoodsprice;?><?php  } else { ?><?php  echo number_format($goodsprice,2)?><?php  } ?>
        </span></div>
    </div>
    <input type="hidden" name="invoicePrice" value="<?php  echo $hasinvoiceMoney;?>">
    <?php  if(empty($exchangeOrder) && empty($taskgoodsprice)) { ?>
    <?php  if(!$packageid) { ?>
    <?php  if(empty($if_bargain['bargain'])) { ?>
    <div class="fui-cell"  style="display: none">
        <div class="fui-cell-label" style='width:auto' >重复购买优惠</div>
        <div class="fui-cell-info"></div>
        <div class="fui-cell-remark noremark">-￥<span id='showbuyagainprice' class='showbuyagainprice'></span></div>
        <input type="hidden" id='buyagain' class='buyagainprice'  value="<?php  echo number_format($buyagainprice,2)?>" />
    </div>
    <?php  } ?>
    <div class="fui-cell istaskdiscount"  style="display: none">
        <div class="fui-cell-label" style='width:auto' >任务活动优惠</div>
        <div class="fui-cell-info"></div>
        <div class="fui-cell-remark noremark">-&yen; <span id='showtaskdiscountprice' class='showtaskdiscountprice'></span></div>
        <input type="hidden" id='taskdiscountprice' class='taskdiscountprice'  value="<?php  echo number_format($taskdiscountprice,2)?>" />
    </div>

    <div class="fui-cell islotterydiscount"  style="display: none">
        <div class="fui-cell-label" style='width:auto' >游戏活动优惠</div>
        <div class="fui-cell-info"></div>
        <div class="fui-cell-remark noremark">-&yen; <span id='showlotterydiscountprice' class='showlotterydiscountprice'></span></div>
        <input type="hidden" id='lotterydiscountprice' class='lotterydiscountprice'  value="<?php  echo number_format($lotterydiscountprice,2)?>" />
    </div>

    <div class="fui-cell discount"  style="display: none">
        <div class="fui-cell-label" style='width:auto' >会员优惠</div>
        <div class="fui-cell-info"></div>
        <div class="fui-cell-remark noremark">-&yen; <span id='showdiscountprice' class='showdiscountprice'></span></div>
        <input type="hidden" id='discountprice' class='discountprice'  value="<?php  echo number_format($discountprice,2)?>" />
    </div>

    <div class="fui-cell isdiscount"  style="display: none">
        <div class="fui-cell-label" style='width:auto' >促销优惠</div>
        <div class="fui-cell-info"></div>
        <div class="fui-cell-remark noremark">-&yen; <span id='showisdiscountprice' class='showisdiscountprice'></span></div>
        <input type="hidden" id='isdiscountprice' class='isdiscountprice'  value="<?php  echo number_format($isdiscountprice,2)?>" />
    </div>

    <div class="fui-cell" id="deductenough" <?php  if(!$saleset['showenough']) { ?>style='display:none'<?php  } ?>>
    <div class="fui-cell-label" style='width:auto' >商城优惠 <span style="font-size: .6rem">：单笔满 ￥<span id="deductenough_enough"><?php  echo number_format($saleset['enoughmoney'],2)?></span> 元立减￥<?php  if($saleset['showenough']) { ?><?php  echo number_format($saleset['enoughdeduct'],2)?><?php  } ?></span></div>
    <div class="fui-cell-info"></div>
    <div class="fui-cell-remark noremark">-&yen; <span id='deductenough_money'><?php  if($saleset['showenough']) { ?><?php  echo number_format($saleset['enoughdeduct'],2)?><?php  } ?></span></div>
</div>

<div class="fui-cell" id="merch_deductenough" <?php  if(!$merch_saleset['merch_showenough']) { ?>style='display:none'<?php  } ?>>
<div class="fui-cell-label" style='width:auto' >商户单笔满 <span id="merch_deductenough_enough" class='text-danger'><?php  echo number_format($merch_saleset['merch_enoughmoney'],2)?></span> 元立减</div>
<div class="fui-cell-info"></div>
<div class="fui-cell-remark noremark">-&yen; <span id='merch_deductenough_money'><?php  if($merch_saleset['merch_showenough']) { ?><?php  echo number_format($merch_saleset['merch_enoughdeduct'],2)?><?php  } ?></span></div>
</div>

<div class="fui-cell" id="seckillprice"  <?php  if($seckill_price<=0) { ?>style="display: none"<?php  } ?>>
<div class="fui-cell-label" style='width:auto' >秒杀优惠</div>
<div class="fui-cell-info"></div>
<div class="fui-cell-remark noremark">-&yen; <span id="seckillprice_money"><?php  echo number_format($seckill_price,2)?></span></div>
</div>

<?php  } ?>

<?php  } ?>

<?php  if($liveprice>0) { ?>
    <input type="hidden" id="liveid" value="<?php  echo $liveid;?>" />
<?php  } ?>

<?php  if(!$isvirtual) { ?>
<div class="fui-cell" id="showdispatchprice">
    <div class="fui-cell-label" ><?php  echo $courier;?></div>
    <div class="fui-cell-info"></div>
    <div class="fui-cell-remark noremark">&yen; <span class='<?php  if(!$packageid && empty($exchangeOrder)) { ?>dispatchprice<?php  } ?>'><?php  if(!empty($exchangeOrder)) { ?><?php  echo $exchangepostage;?><?php  } else if($taskgoodsprice) { ?>2<?php  echo $taskgoodsprice;?><?php  } else { ?><?php  echo number_format($dispatch_price,2)?><?php  } ?></span></div>
</div>
<?php  } ?>

<div class="fui-cell" id="showinvoice" style="display: none;">
    <div class="fui-cell-label" >开具发票</div>
    <div class="fui-cell-info"></div>
    <div class="fui-cell-remark noremark">&yen; <span class='invoicePrice'><?php  if(!empty($exchangeOrder)) { ?><?php  echo $exchangepostage;?><?php  } else if($taskgoodsprice) { ?>2<?php  echo $taskgoodsprice;?><?php  } else { ?><?php  echo number_format($dispatch_price,2)?><?php  } ?></span></div>
</div>

<div class="fui-cell" id='coupondeduct_div' style='display:none'>
    <div class="fui-cell-label" style='width:auto' id='coupondeduct_text' ></div>
    <div class="fui-cell-info"></div>
    <div class="fui-cell-remark noremark">-&yen; <span id="coupondeduct_money">0</span></div>
</div>
<?php  if(!empty($order_formInfo)) { ?>
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('diyform/formfields', TEMPLATE_INCLUDEPATH)) : (include template('diyform/formfields', TEMPLATE_INCLUDEPATH));?>
<?php  } else if(!$hastask2) { ?>
<div class="fui-cell-group" style="margin-top: 0;">
    <div class="fui-cell fui-cell-textarea">
        <div class="fui-cell-label" style="margin:.15rem 0 0 0">
            留言
        </div>
        <div class="fui-cell-info c000">
            <textarea rows="2" placeholder="给卖家留言～～" id='remark'></textarea>
        </div>
    </div>
</div>
<?php  } ?>
</div>

</div>
<?php  if($isgift) { ?>
<div id='gift-picker-modal' style="margin:-100%;">
    <div class='gift-picker'>
        <div class="fui-cell-group fui-sale-group" style='margin-top:0;'>
            <div class="fui-cell">
                <div class="fui-cell-text dispatching">
                    请选择赠品:
                    <div class="dispatching-info" style="max-height:12rem;overflow-y: auto ">
                        <?php  if(is_array($gifts)) { foreach($gifts as $item) { ?>
                        <div class="fui-list goods-item align-start" data-giftid="<?php  echo $item['id'];?>">
                            <div class="fui-list-media">
                                <input type="radio" name="checkbox" class="fui-radio fui-radio-danger gift-item" value="<?php  echo $item['id'];?>" style="display: list-item;">
                            </div>
                            <div class="fui-list-inner">
                                <?php  if(is_array($item['gift'])) { foreach($item['gift'] as $gift) { ?>
                                <div class="fui-list">
                                    <div class="fui-list-media image-media" style="position: initial;">
                                        <a href="javascript:void(0);">
                                            <img class="round" src="<?php  echo tomedia($gift['thumb'])?>" data-lazyloaded="true">
                                        </a>
                                    </div>
                                    <div class="fui-list-inner">
                                        <a href="javascript:void(0);">
                                            <div class="text">
                                                <?php  echo $gift['title'];?>
                                            </div>
                                        </a>
                                    </div>
                                    <div class='fui-list-angle'>
                                        <span class="price">&yen;<del class='marketprice'><?php  echo $gift['marketprice'];?></del></span>
                                    </div>
                                </div>
                                <?php  } } ?>
                            </div>
                        </div>
                        <?php  } } ?>
                    </div>
                </div>
            </div>
            <div class='btn btn-danger block'>确定</div>
        </div>
    </div>
</div>
<?php  } ?>


<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('sale/coupon/util/picker', TEMPLATE_INCLUDEPATH)) : (include template('sale/coupon/util/picker', TEMPLATE_INCLUDEPATH));?>
<script type="text/javascript" src="https://api.map.baidu.com/api?v=2.0&ak=ZQiFErjQB7inrGpx27M1GR5w3TxZ64k7&s=1"></script>

<script language='javascript'>require(['biz/order/create'], function (modal) {modal.init(<?php  echo json_encode($createInfo)?>); });</script>
</div>
<div class="fui-navbar order-create-checkout">
    <a href="javascript:;" class="nav-item total">
        <p style="color: #999"><?php  if($packageid) { ?><span class="text-danger" style="font-size: 0.6rem;">(套餐优惠&yen;<?php  echo number_format($marketprice-$goodsprice,2)?>)</span><?php  } ?>
            合计：<span class="text-danger  bigprice">&yen; <span class="<?php  if(!$packageid && empty($exchangeOrder)) { ?>totalprice<?php  } ?>">
                <?php  if(!empty($exchangeOrder)) { ?><?php  echo $exchangerealprice;?><?php  } else if($taskgoodsprice) { ?><?php  echo $taskgoodsprice;?><?php  } else { ?><?php  echo number_format($realprice,2)?><?php  } ?></span></span>
        </p>
    </a>
    <a href="javascript:;"  class="nav-item btn btn-danger buybtn"><span>立即支付</span></a>
</div>
</div>
<script type="text/javascript">
    $("#invoiceCheckbox").click(function(){
        if ($(this).prop("checked")) {            
            $(".invoice-operate").show();
            $("#showinvoice").show();
        }else{
            $(".invoice-operate").hide();
            $("#showinvoice").hide();
             // $("#showinvoice").html('')
        }
    })

</script>

<script type="text/javascript">
    
    $("#invoiceCheckbox_personal").click(function(){
            $(".invoice-tax-no").hide();
    }),
    $("#invoiceCheckbox_company").click(function(){
            $(".invoice-tax-no").show();

    })
</script>
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>

<!--efwww_com54mI5p2D5omA5pyJ-->