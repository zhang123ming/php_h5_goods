<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>
<!-- <meta name="referrer" content="no-referrer" charset="utf-8"> -->
<style>
    .fui-cell-group{width:100%;}
    .fullback-title{color:#999999;font-size:0.7rem;padding:0.75rem 0 0.5rem 0;}
    .fullback-info{}
    .fullback-info p{height:1rem;line-height: 1rem;font-size:0.625rem;color:#333;display: inline-block;padding:0 0.5rem 0 0;}
    .fullback-info p i{border:none;height:0.75rem;width:0.75rem;display: inline-block;background: #ff4753;color:#fff;font-size:0.4rem;line-height: 0.8rem;text-align: center;
        font-style: normal;border-radius: 0.75rem;-webkit-border-radius: 0.75rem;margin-right:0.25rem;}
    .fullback-remark{line-height: 1rem;font-size:0.6rem;color:#666;padding:0.2rem 0;}

</style>
<link rel="stylesheet" type="text/css" href="../addons/ewei_shopv2/template/mobile/default/static/css/coupon.css?v=2.0.0">
<link rel="stylesheet" type="text/css" href="../addons/ewei_shopv2/template/mobile/default/static/css/coupon-new.css?v=2017030302">
<link rel="stylesheet" href="../addons/ewei_shopv2/static/js/dist/swiper/swiper.min.css">
<input type="hidden" name="goodsid" value="<?php  echo $goodsids;?>" id="goodsid" />
<?php  if(!empty($islive)) { ?>
    <link rel="stylesheet" type="text/css" href="../addons/ewei_shopv2/plugin/live/static/css/living.css?v=20170628" />
<?php  } ?>

<div class='fui-page fui-page-current  page-goods-detail' id='page-goods-detail-index'>
    <?php  $this->followBar(false,$goods['merchid'],$shopname)?>
    <?php  if(empty($err)) { ?>
    <div class="fui-header">
        <div class="fui-header-left">
            <a class="back" id="btn-back"></a>
        </div>
        <div class="title">
            商品详情
        </div>
        <div class="fui-header-right"></div>
    </div>
    <?php  } else { ?>
    <div class="fui-header ">
        <div class="fui-header-left">
            <a class="back" id="btn-back"></a>
        </div>
        <div class="title">
            找不到宝贝
        </div>
    </div>
    <?php  } ?>

    <?php  if(empty($err)) { ?>

<!--参数已完成-->
    <?php  if(count($params)>0) { ?>
    <div class="fui-content param-block  <?php  if(!$goods['canbuy']) { ?>notbuy<?php  } ?>">
        <div class="fui-cell-group notop">
            <?php  if(!empty($params)) { ?>
            <?php  if(is_array($params)) { foreach($params as $p) { ?>
            <div class="fui-cell">
                <div class="fui-cell-label md" ><?php  echo $p['title'];?></div>
                <div class="fui-cell-info overflow md"><?php  echo $p['value'];?></div>
            </div>
            <?php  } } ?>

            <?php  } else { ?>
            <div class="fui-cell">
                <div class="fui-cell-info text-align">商品没有参数</div>
            </div>
            <?php  } ?>
        </div>
    </div>
    <?php  } ?>
<!--评价-->
    <?php  if(!$new_temp) { ?>
    <div class="fui-header comment-header">
        <div class="fui-header-left">
            <a class="btn-back" id="comment-hide"></a>
        </div>
        <div class="title">
            商品评论
        </div>
        <div class="fui-header-right"></div>
    </div>
    <div class='fui-content comment-block  <?php  if(!$goods['canbuy']) { ?>notbuy<?php  } ?>' data-getcount='1' id='comments-list-container'>

        <div class='fui-icon-group col-5 '>
            <div class='fui-icon-col' data-level='all'><span class='text-danger'>全部<br/><span class="count"></span></span></div>
            <div class='fui-icon-col' data-level='good'><span>好评<br/><span class="count"></span></span></div>
            <div class='fui-icon-col' data-level='normal'><span>中评<br/><span class="count"></span></span></div>
            <div class='fui-icon-col' data-level='bad'><span>差评<br/><span class="count"></span></span></div>
            <div class='fui-icon-col' data-level='pic'><span>晒图<br/><span class="count"></span></span></div>
        </div>
        <div class='content-empty' style='display:none;'>
            <i class='icon icon-community'></i><br/>暂时没有任何评价
        </div>
        <div class='container' id="comments-all"></div>
        <div class='infinite-loading'><span class='fui-preloader'></span><span class='text'> 正在加载...</span></div>
    </div>
<!--商品详情已完成-->
    <div class="fui-content detail-block  <?php  if(!$goods['canbuy']) { ?>notbuy<?php  } ?>">
        <div class="text-danger look-basic"><i class='icon icon-unfold'></i> <span>上拉返回商品详情</span></div>
        <div class='content-block content-images'></div>
    </div>
<?php  } ?>

<div class='fui-content basic-block pulldown <?php  if(!$goods['canbuy']) { ?>notbuy<?php  } ?>'>


<?php  if(!empty($err)) { ?>
<div class='content-empty'>
    <i class='icon icon-search'></i><br/> 宝贝找不到了~ 您看看别的吧 ~<br/><a href="<?php  echo mobileUrl()?>" class='btn btn-default-o external'>到处逛逛</a>
</div>
<?php  } else { ?>
<?php  if($commission_data['qrcodeshare'] > 0 && $level['level']<=91) { ?>
<a href="javascript:;" id="dianji"><i class="icon icon-qrcode" id="alert-click"></i></a>
<?php  } ?>

<!--轮播已完成-->
<div class='fui-swipe goods-swipe'>
    <?php  if(!empty($islive)) { ?>
        <a class="external isliving" href="<?php  echo mobileUrl('live/room', array('id'=>$liveid))?>">直播中</a>
    <?php  } ?>

    <div class='fui-swipe-wrapper'>
        <?php  if(is_array($thumbs)) { foreach($thumbs as $thumb) { ?>
        <div class='fui-swipe-item'><img src="<?php echo EWEI_SHOPV2_PLACEHOLDER;?>"  data-lazy="<?php  echo tomedia($thumb)?>" /></div>
        <?php  } } ?>
    </div>
    <div class='fui-swipe-page'></div>
    <?php  if($goods['total']<=0 && !empty($_W['shopset']['shop']['saleout'])) { ?>
    <div class="salez">
        <img src="<?php  echo tomedia($_W['shopset']['shop']['saleout'])?>">
    </div>
    <?php  } ?>
</div>

<!--秒杀完成-->
<?php  if(!empty($seckillinfo)) { ?>
<div class="seckill-container <?php  if($seckillinfo['status']==1) { ?>notstart<?php  } ?>"
     data-starttime="<?php  echo $seckillinfo['starttime']-time();?>"
     data-endtime="<?php  echo $seckillinfo['endtime']-time();?>"
     data-status="<?php  echo $seckillinfo['status'];?>">
    <div class="fui-list seckill-list" style="" >
        <div class="fui-list-media seckill-price">
            &yen;<span><?php  echo $seckillinfo['price'];?></span>
        </div>
        <div class="fui-list-inner">
            <?php  if(empty($seckillinfo['tag'])) { ?>

            <?php  } else { ?>
                <div class="text">
                    <span class="stitle" style="white-space:nowrap; overflow:hidden; max-width:2.6rem;"><?php  echo $seckillinfo['tag'];?></span>
                </div>
            <?php  } ?>
            <div class="text"><span class="oldprice">&yen;<?php  echo $goods['minprice'];?></span></div>
        </div>
    </div>
    <div class="fui-list seckill-list1" >
        <div class="fui-list-inner">
            <div class="text "><?php  if($seckillinfo['status']==0) { ?>已出售 <?php  echo $seckillinfo['percent'];?>%<?php  } ?></div>
            <div class="text "><?php  if($seckillinfo['status']==0) { ?><span class="process"><div class="inner" style="width:<?php  echo $seckillinfo['percent'];?>%"></div></span><?php  } ?></div>
        </div>
    </div>
    <div class="fui-list seckill-list2" style="" >
        <div class="fui-list-inner">
            <div class="text ">距<?php  if($seckillinfo['status']==1) { ?>开始<?php  } else { ?>结束<?php  } ?>还有</div>
            <div class="text timer">
                <span class="time-hour">-</span>&nbsp;:&nbsp;<span class="time-min">-</span>&nbsp;:&nbsp;<span class="time-sec">-</span>
            </div>
        </div>
    </div>
</div>
<?php  } ?>


<div class="fui-cell-group fui-detail-group" >
    <div class="fui-cell">
        <div class="fui-cell-text name ">
            <!--预售完成-->
            <?php  if($ispresell==1) { ?><i class="fui-tag fui-tag-danger">预</i><?php  } ?>
            <?php  echo $goods['title'];?>
        </div>

    </div>
    <?php  if(!empty($goods['subtitle'])) { ?>
    <div class="fui-cell goods-subtitle">
        <span class='text-danger'><?php  echo $goods['subtitle'];?></span>
    </div>
    <?php  } ?>

    <!--批发-->
    <?php  if($goods['type']==4) { ?>
    <!--批发价格-->
    <div class="fui-cell goods-bulk">
        <div class="fui-cell-text  flex">
            <?php  if($goods['intervalfloor']>0) { ?>
                <span>
                    <p class="price"><small>&yen;</small><?php  echo $goods['intervalprice1'];?></p>
                    <p><?php  echo $goods['intervalnum1'];?> <?php  if($goods['intervalfloor']>1) { ?>-<?php  echo intval($goods['intervalnum2'])-1?>件<?php  } else { ?>件以上<?php  } ?></p>
                </span>
            <?php  } ?>
            <?php  if($goods['intervalfloor']>1) { ?>
                <span>
                    <p class="price"><small>&yen;</small><?php  echo $goods['intervalprice2'];?></p>
                    <p><?php  echo $goods['intervalnum2'];?><?php  if($goods['intervalfloor']>2) { ?>-<?php  echo intval($goods['intervalnum3'])-1?>件<?php  } else { ?>件以上<?php  } ?></p>
                </span>
            <?php  } ?>
            <?php  if($goods['intervalfloor']>2) { ?>
                <span>
                    <p class="price"><small>&yen;</small><?php  echo $goods['intervalprice3'];?></p>
                    <p>><?php  echo $goods['intervalnum3'];?>件</p>
                </span>
            <?php  } ?>
        </div>
    </div>
    <?php  } ?>

    <!--正常价格-->
    <?php  if(empty($seckillinfo)&&$goods['type']!=4) { ?>
    <div class="fui-cell">
        <div class="fui-cell-text price" >

            <?php  if($islive) { ?>
                <span class="live-price">直播价</span>
            <?php  } ?>

			<span class="text-danger" style="vertical-align: middle;">
                <!--任务-->
                <?php  if(!empty($taskGoodsInfo)) { ?>
                    &yen;<?php  echo $taskGoodsInfo['price'];?>
                <!--三n营销-->
                <?php  } else if($threen &&(!empty($threenprice['price'])||!empty($threenprice['discount']))) { ?>
                    <?php  if(!empty($threenprice['price'])) { ?>
                        <?php  echo $threenprice['price'];?>
                    <?php  } else if(!empty($threenprice['discount'])) { ?>
                        <?php  echo $threenprice['discount']*$goods['minprice'];?>
                    <?php  } ?>
                <?php  } else { ?>

                <!--最正常价格-->
                &yen;<?php  if($goods['ispresell']>0 && ($goods['preselltimeend'] == 0 || $goods['preselltimeend'] > time())) { ?>
                        <!--预售-->
                        <?php  echo $goods['presellprice'];?>
                    <?php  } else { ?>
                        <!--基础价格-->
                        <?php  if($goods['minprice']==$goods['maxprice']) { ?>
                            <?php  echo $goods['minprice'];?>
                        <?php  } else { ?>
                            <?php  echo $goods['minprice'];?>~<?php  echo $goods['maxprice'];?>
                        <?php  } ?>
                    <?php  } ?>
                <!--促销-->
                    <?php  if($goods['isdiscount'] && $goods['isdiscount_time']>=time()) { ?>
                         <span class="original">&yen;<?php  echo $goods['productprice'];?></span>
                    <?php  } else { ?>
                        <?php  if($goods['productprice']>0) { ?>
                            <span  class="original">&yen;<?php  echo $goods['productprice'];?></span>
                        <?php  } ?>
                    <?php  } ?>
                <?php  } ?>
			</span>
        </div>
        <?php  if(empty($share['goods_detail_close']) && $level['level']<=91) { ?>
        <a class="fui-cell-remark share"  <?php  if(!empty($goods['sharebtn']) && $member['isagent']==1 && $member['status']==1) { ?> href="<?php  echo mobileUrl('commission/qrcode', array('goodsid'=>$goods['id']))?>" <?php  } else { ?> id='btn-share' <?php  } ?>>
        <i class="icon icon-share"></i>
        <span>分享</span>
        </a>
        <?php  } ?>
    </div>
    <?php  if($goods['usercommission']) { ?>
    <!--<div style="font-size: 0.6rem;height:1.2rem;line-height: 1.2rem;padding-left:0.5rem;margin-top:5px;"><span class="buy buybtn-1" style="background: #FCE0E0;color: #fe5455;border: #fe5455 1px solid;padding: 3px;border-radius: 3px;">邀请下单 : ￥<?php  echo $goods['usercommission'];?></span></div>-->
    <?php  } ?>

    <!--促销倒计时-->
    <?php  if($goods['isdiscount'] && $goods['isdiscount_time']>=time()) { ?>
    <div class="row row-time">
        <div id='discount-container' class='fui-labeltext fui-labeltext-danger'
             data-now="<?php  echo date('Y-m-d H:i:s')?>"
             data-end="<?php  echo date('Y-m-d H:i:s', $goods['isdiscount_time'])?>"
             data-end-label='<?php echo empty($goods['isdiscount_title'])?'促销':$goods['isdiscount_title']?>' >
        <div class='label'><?php echo empty($goods['isdiscount_title'])?'促销':$goods['isdiscount_title']?></div>
        <div class='text'>
            <span class="day number text-danger" >-</span><span class="time">天</span>
            <span class="hour number text-danger">-</span><span class="time">小时</span>
            <span class="minute number text-danger">-</span><span class="time">分</span>
            <span class="second number text-danger">-</span><span class="time">秒</span>
        </div>
    </div>

</div>
<?php  } ?>

<!--限时购完成-->
<?php  if($goods['istime']) { ?>
<div class="row row-time">
    <div id='time-container' class='fui-labeltext fui-labeltext-danger'
         data-now="<?php  echo date('Y-m-d H:i:s')?>"
         data-start-label='距离限时购开始'
         data-end-label='距离限时购结束'
         data-end-text='活动已经结束，下次早点来~'
         data-start="<?php  echo date('Y-m-d H:i:s', $goods['timestart'])?>"
         data-end="<?php  echo date('Y-m-d H:i:s', $goods['timeend'])?>"
    >
        <div class='label'>限时购</div>
        <div class='text'>
            <span class="day number"></span><span class="time">天</span><span class="hour number"></span><span class="time">小时</span><span class="minute number"></span><span class="time">分</span><span class="second number"></span><span class="time">秒</span>
        </div>
    </div>
</div>
<?php  } ?>
<?php  } ?>

<!--预售倒计时完成-->
<?php  if($goods['ispresell']==1 && ($goods['preselltimestart'] > time() || $goods['preselltimeend'] > time())) { ?>
<div class="row row-time">
    <div id='time-presell' class='fui-labeltext fui-labeltext-danger'
         data-now="<?php  echo date('Y-m-d H:i:s')?>"
         data-start-label='距离预售开始'
         data-end-label='距离预售结束'
         data-end-text='活动已经结束，下次早点来~'
         data-start="<?php  echo date('Y-m-d H:i:s', $goods['preselltimestart'])?>"
         data-end="<?php  echo date('Y-m-d H:i:s', $goods['preselltimeend'])?>">
        <div class='label'>预售</div>
        <div class='text'>
            <span class="day number"></span><span class="time">天</span><span class="hour number"></span><span class="time">小时</span><span class="minute number"></span><span class="time">分</span><span class="second number"></span><span class="time">秒</span>
        </div>
    </div>
</div>
<?php  } ?>

<?php  if($goods['canbuy']) { ?>
<div class="fui-cell">
    <div class="fui-cell-text flex">
        <?php  if(is_array($goods['dispatchprice'])) { ?>
            <?php  if($goods['type']==1 && $goods['isverify']!=2) { ?>
            <span><?php  echo $courier;?>：<?php  echo number_format($goods['dispatchprice']['min'],2)?> ~ <?php  echo number_format($goods['dispatchprice']['max'],2)?></span>
            <?php  } ?>
        <?php  } else { ?>
            <?php  if($goods['type']==1 && $goods['isverify']!=2) { ?>
            <?php  if($goods['issendfree'] == 1) { ?>
            <span><?php  echo $courier;?>：包邮</span>
            <?php  } else { ?>
            <span><?php  echo $courier;?>：<?php echo $goods['dispatchprice'] == 0 ? '0.00' : number_format($goods['dispatchprice'],2)?></span>
            <?php  } ?>
            <?php  } ?>
        <?php  } ?>
        <?php  if($seckillinfo==false || ( $seckillinfo && $seckillinfo.status==1)) { ?>
        <?php  if($goods['showtotal'] == 1) { ?>
        <span>库存：<?php  echo $goods['total'];?></span>
        <?php  } ?>

        <?php  if($goods['showsales'] == 1) { ?>
        <span>销量： <?php  echo number_format($goods['sales'],0)?> <?php  echo $goods['unit'];?></span>
        <?php  } ?>

        <?php  } else { ?>
        <span></span>
        <span></span>
        <?php  } ?>


        <?php  if($goods['province'] != '请选择省份' && $goods['city'] != '请选择城市') { ?>
        <span><?php  echo $goods['province'];?> <?php  echo $goods['city'];?></span>
        <?php  } ?>
    </div>
</div>
<?php  } ?>
</div>

<!--预售提示完成-->
<?php  if($goods['ispresell']==1 && ( $goods['preselltimeend'] > time() ||  $goods['preselltimeend'] == 0)) { ?>
<div class='fui-cell-group fui-cell-click  fui-sale-group' style='margin-top:0'>
    <div class="fui-list">
        <div class="fui-list-media" style="margin-right: 0;align-self: flex-start;-webkit-align-self: flex-start">
            <div class='fui-cell-text'>
                <span style="font-size: 0.65rem;color: #666;vertical-align: top">预售：</span>
            </div>
        </div>
        <div class="fui-list-inner" style="font-size:0.65rem;color:#666;">
            <?php  if($goods['preselltimeend'] > 0) { ?>
            结束时间：<?php  echo date('m月d日 H:i:s', $goods['preselltimeend'])?><br />
            <?php  } ?>
            预计发货：<?php  if($goods['presellsendtype']>0) { ?>购买后<?php  echo $goods['presellsendtime'];?>天发货<?php  } else { ?><?php  echo date('m月d日', $goods['presellsendstatrttime'])?><?php  } ?>
        </div>
    </div>
</div>
<?php  } ?>

<!-- 商品可用优惠券 -->
<?php  if(com('coupon')&&$coupons) { ?>
    <div class="fui-cell-group fui-cell-click fui-sale-group noborder">
        <div class="fui-cell fui-sale-cell  coupon-selector">
            <div class="fui-cell-label ">
                    <span style="">优惠券</span>
            </div>
            <div class="fui-cell-text">
                
                <?php  if(is_array($coupons)) { foreach($coupons as $index => $couponmini) { ?>
                    <?php  if($index<5) { ?>
                        <span class="coupon-mini"><?php  if($couponmini['backpre']) { ?>&yen;<?php  } ?><?php  echo price_format($couponmini['backmoney'])?><?php  if($couponmini['backtype']==1) { ?>折<?php  } ?></span>
                    <?php  } ?>
                <?php  } } ?>                
            </div>
            <div class="fui-cell-remark">去领券</div>
        </div>
    </div>

    <!--商品可用优惠码-->
    <!--<div class="form-group">
            <label class="col-sm-2 control-label">优惠码</label>
            <div class="col-sm-9 col-xs-12">
                <input type="text" id="send_total" name="send_total" class="form-control" value="1" data-rule-min='1' data-rule-required='true' /><br/>
                <span>此处受总数限制，如果剩余张数不足以发放给选定会员数量，则无法发放</span>
            </div>
    </div>-->
    
<?php  } ?>
<div class="fui-cell-group  fui-sale-group nomargin">

    <!-- 会员等级 -->
    <?php  if(empty($goods['isdiscount']) || (!empty($goods['isdiscount']) &&$goods['isdiscount_time']<time())) { ?>
        <?php  if(!empty($memberprice) && $memberprice!=$goods['minprice'] && !empty($level)) { ?>
            <div class="fui-cell fui-sale-cell">
                <div class="fui-cell-label" style="padding-top: 0.1rem">会员</div>
                <div class="fui-cell-text" style="white-space: normal;">
                    <div class="sale-line">
                        <span class="sale-tip"><?php  echo $level['levelname'];?></span>
                        可享受 <span class="text-danger">&yen;<?php  echo $memberprice;?></span> 的价格
                    </div>
                </div>
            </div>
        <?php  } ?>
    <?php  } ?>

    <?php  if(($enoughs && count($enoughs)>0 && empty($seckillinfo)) || ($hasSales && empty($seckillinfo)) || ((!empty($goods['deduct']) && $goods['deduct'] != '0.00')  || !empty($goods['credit'])) || (floatval($goods['buyagain'])>0 && empty($seckillinfo)) || (!empty($fullbackgoods) && $isfullback)) { ?>
    <div class="fui-cell fui-sale-cell" id="picker-sales">
        <div class="fui-cell-label" >活动</div>
        <div class="fui-cell-text">
            <!-- 满减 -->
            <?php  if($enoughs && count($enoughs)>0 && empty($seckillinfo)) { ?>
            <div class="sale-line">
                <span class="sale-tip">满减</span>
                <span>全场满 <span class="text-danger">&yen;<?php  echo $enoughs[0]['enough'];?></span> 立减 <span class="text-danger">&yen;<?php  echo $enoughs[0]['money'];?></span></span>
            </div>
            <?php  } ?>

            <!-- 包邮 -->
            <?php  if($hasSales && empty($seckillinfo)) { ?>
                <?php  if((!is_array($goods['dispatchprice']) && $goods['type']==1 && $goods['isverify']!=2 && $goods['dispatchprice']==0) || (($enoughfree && $enoughfree==-1) || ($enoughfree>0 || $goods['ednum']>0 || $goods['edmoney']>0))) { ?>
                <div class="sale-line">
                    <span class="sale-tip">包邮</span>
                    <span>
                           <?php  if(!is_array($goods['dispatchprice'])) { ?>
                            <?php  if($goods['type']==1 && $goods['isverify']!=2) { ?>
                            <!-- <?php  if($goods['dispatchprice']==0) { ?> -->
                            本商品包邮;
                            <!-- <?php  } ?> -->
                            <?php  } ?>
                            <?php  } ?>

                            <?php  if($enoughfree && $enoughfree==-1) { ?>
                            <?php  if(!empty($merch_set['enoughfree'])) { ?>本店<?php  } else { ?>全场<?php  } ?>包邮
                            <?php  } else { ?>
                            <?php  if($goods['ednum']>0) { ?>单品满 <span class="text-danger"><?php  echo $goods['ednum'];?></span>
                            <?php echo empty($goods['unit'])?'件':$goods['unit']?>包邮; <?php  } ?>
                            <?php  if($goods['edmoney']>0) { ?>单品满 <span class="text-danger">&yen;<?php  echo $goods['edmoney'];?></span> 包邮; <?php  } ?>
                            <?php  if($enoughfree) { ?><?php  if(!empty($merch_set['enoughfree'])) { ?>本店<?php  } else { ?>全场<?php  } ?>满 <span class="text-danger">&yen;<?php  echo $enoughfree;?></span> 包邮; <?php  } ?>
                            <?php  } ?>
                        </span>
                </div>
                <?php  } ?>
            <?php  } ?>

            <!-- <?php  echo $_W['shopset']['trade']['credittext'];?> -->
            <?php  if((!empty($goods['deduct']) && $goods['deduct'] != '0.00')  || !empty($goods['credit'])) { ?>
            <div class="sale-line">
                <span class="sale-tip"><?php  echo $_W['shopset']['trade']['credittext'];?></span>
                <span>
                        <?php  if(!empty($goods['deduct']) && $goods['deduct'] != '0.00') { ?>最高抵扣 <span class="text-danger">&yen;<?php  echo $goods['deduct'];?></span><?php  } ?> <?php  if(!empty($goods['credit'])) { ?>购买赠送 <span class="text-danger"><?php  echo $goods['credit'];?></span> <?php  echo $_W['shopset']['trade']['credittext'];?><?php  } ?>
                    </span>
            </div>
            <?php  } ?>

            <!-- 二次购买 -->
            <?php  if(floatval($goods['buyagain'])>0 && empty($seckillinfo)) { ?>
            <div class="sale-line">
                <span class="sale-tip">复购</span>
                <span>此商品重复购买可享受 <span class="text-danger"><?php  echo $goods['buyagain'];?></span> 折优惠<?php  if(empty($goods['buyagain_sale'])) { ?><br>重复购买 不与其他优惠共享<?php  } ?></span>
            </div>
            <?php  } ?>

            <!--全返-->
            <?php  if(!empty($fullbackgoods) && $isfullback) { ?>
            <div class="sale-line">
                <span class="sale-tip">全返</span>
                <span>
                    该商品享受
                    <?php  if($fullbackgoods['type']>0) { ?>
                        <span class="text-danger">
                            <?php  if($goods['hasoption'] > 0) { ?>
                                <?php  if($fullbackgoods['minallfullbackallprice'] == $fullbackgoods['maxallfullbackallprice']) { ?>
                                    <?php  echo $fullbackgoods['minallfullbackallratio'];?>%
                                <?php  } else { ?>
                                    <?php  echo $fullbackgoods['minallfullbackallratio'];?>% ~ <?php  echo $fullbackgoods['maxallfullbackallratio'];?>%
                                <?php  } ?>
                            <?php  } else { ?>
                                <?php  echo $fullbackgoods['minallfullbackallratio'];?>%
                            <?php  } ?>
                        </span>
                    <?php  } else { ?>
                        <span class="text-danger">
                            <?php  if($goods['hasoption'] > 0) { ?>
                                <?php  if($fullbackgoods['minallfullbackallprice'] == $fullbackgoods['maxallfullbackallprice']) { ?>
                            <?php  if($fullbackgoods['credit']==1) { ?>积分<?php  } else { ?>&yen;<?php  } ?>
                            <?php  echo $fullbackgoods['minallfullbackallprice'];?>
                                <?php  } else { ?>
                            <?php  if($fullbackgoods['credit']==1) { ?>积分<?php  } else { ?>&yen;<?php  } ?>
                            <?php  echo $fullbackgoods['minallfullbackallprice'];?> ~ <?php  if($fullbackgoods['credit']==1) { ?>积分<?php  } else { ?>&yen;<?php  } ?>
                            <?php  echo $fullbackgoods['maxallfullbackallprice'];?>
                                <?php  } ?>
                            <?php  } else { ?>
                            <?php  if($fullbackgoods['credit']==1) { ?>积分<?php  } else { ?>&yen;<?php  } ?>
                            <?php  echo $fullbackgoods['minallfullbackallprice'];?>
                            <?php  } ?>
                        </span>
                    <?php  } ?>
                    的全返
                </span>
            </div>
            <?php  } ?>

        </div>
        <div class="fui-cell-remark"></div>
    </div>
    <?php  } ?>

    <div class="fui-cell fui-sale-cell" id="picker-params">
            <div class="fui-cell-label ">
                    <span style="">参数</span>
            </div>
            <div class="fui-cell-text">
                    品牌型号     
            </div>
            <div class="fui-cell-remark">
                    查看
            </div>
        </div>
</div>


<!--赠品-->
<?php  if($gifts && $goods['total'] > 0) { ?>
<div class="fui-cell-group  fui-sale-group nomargin">
    <div class="fui-cell fui-sale-cell sendgoods">
        <div class="fui-cell-label">赠品</div>
        <?php  if(count($gifts)>1) { ?>
            <div class="fui-cell-text fui-cell-giftclick">
                <label id="gifttitle">请选择赠品</label>
                <input type="hidden" name="giftid" id="giftid" value="">
            </div>
        <?php  } else { ?>
            <?php  if(is_array($gifts)) { foreach($gifts as $item) { ?>
                <div class='fui-cell-text' onclick="javascript:window.location.href='<?php  echo mobileUrl('goods/gift',array('id'=>$item['id']))?>'">
                    <?php  echo $gifttitle;?><input type="hidden" name="giftid" id="giftid" value="<?php  echo $item['id'];?>">
                </div>
            <?php  } } ?>
        <?php  } ?>
        <div class="fui-cell-remark"></div>
    </div>
</div>
<?php  } ?>


<!--服务完成-->
<?php  if($hasServices || $labelname) { ?>
<div class="fui-cell-group fui-option-group" style='margin-top:0;'>

    <div class="goods-label-demo">
        <div class="goods-label-list
        <?php  if(empty($style['style'])) { ?>goods-label-style1
        <?php  } else if($style['style']==1) { ?>goods-label-style2
        <?php  } else if($style['style']==2) { ?>goods-label-style3
        <?php  } else if($style['style']==3) { ?>goods-label-style4
        <?php  } else if($style['style']==4) { ?>goods-label-style5<?php  } ?>">
            <?php  if($goods['cash']==2) { ?><span class="<?php  if($style['style']<2) { ?>cl-3 cl-4 cl-2<?php  } ?>"><i></i><strong>货到付款</strong></span><?php  } ?>
            <?php  if($goods['quality']) { ?><span class="<?php  if($style['style']<2) { ?>cl-3 cl-4 cl-2<?php  } ?>"><i></i><strong>正品保证</strong></span><?php  } ?>
            <?php  if($goods['repair']) { ?><span class="<?php  if($style['style']<2) { ?>cl-3 cl-4 cl-2<?php  } ?>"><i></i><strong>保修</strong></span><?php  } ?>
            <?php  if($goods['invoice']) { ?><span class="<?php  if($style['style']<2) { ?>cl-3 cl-4 cl-2<?php  } ?>"><i></i><strong>发票</strong></span><?php  } ?>
            <?php  if($goods['seven']) { ?><span class="<?php  if($style['style']<2) { ?>cl-3 cl-4 cl-2<?php  } ?>"><i></i><strong>7天退换</strong></span><?php  } ?>
            <?php  if($labelname) { ?>
            <?php  if(is_array($labelname)) { foreach($labelname as $item) { ?>
            <span class="<?php  if($style['style']<2) { ?>cl-3 cl-4 cl-2<?php  } ?>"><i></i><strong><?php  echo $item;?></strong></span>
            <?php  } } ?>
            <?php  } ?>
            <div style="clear: both;"></div>
        </div>
    </div>


</div>
<?php  } ?>

<!--配送区域完成-->
<?php  if($has_city) { ?>
<div class='fui-cell-group fui-cell-click fui-sale-group noborder'  id="city-picker">
    <div class='fui-cell'>
        <div class='fui-cell-text' style="font-size: 0.7rem;line-height: 0.7rem"><?php  if(empty($onlysent)) { ?>不<?php  } else { ?>只<?php  } ?>配送区域:
            <?php  if(is_array($citys)) { foreach($citys as $item) { ?>
            <?php  echo $item;?>
            <?php  } } ?>
        </div>
        <div class='fui-cell-remark'></div>
    </div>
</div>
<?php  } ?>
<?php  if(!empty($goods['isverify'])) { ?>
<!-- <div class='fui-cell-group fui-cell-click fui-sale-group noborder'  id="city-picker">
    <a class="external"  href="<?php  echo mobileUrl('store/verifystorelist',array('goodsid'=>$goods['id'],'typeaddress'=>'1'))?>">
        <div class='fui-cell'>
            <div class='fui-cell-text' style="font-size: 0.7rem;line-height: 0.7rem">选择门店</div>
            <div class='fui-cell-remark'><?php  echo $_SESSION['store_item']['name'];?></div>
        </div>
    </a>
</div> -->
<?php  } ?>

<?php  if(!empty($stores)) { ?>
<script language='javascript' src='https://api.map.baidu.com/api?v=2.0&ak=ZQiFErjQB7inrGpx27M1GR5w3TxZ64k7&s=1'></script>
<div class='fui-according-group'>
    <div class='fui-according expanded'>
        <div class='fui-according-header'>
            <span class="text">适用门店</span>
            <span class="remark"><div class="badge"><?php  echo count($stores)?></div></span>
        </div>
        <div class="fui-according-content store-container fui-cell-group">
            <?php  if(is_array($stores)) { foreach($stores as $item) { ?>
            <a  href="<?php  echo mobileUrl('store/detail',array('id'=>$item['id'],'merchid'=>$item['merchid']))?>"  class="fui-cell store-item external"
                  data-lng="<?php  echo floatval($item['lng'])?>"
                  data-lat="<?php  echo floatval($item['lat'])?>">
                <div class="fui-cell-icon">
                    <i class='icon icon-dingwei1'></i>
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

<!--数量规格-->
<?php  if($goods['canbuy']) { ?>
    <?php  if($goods['type']!=4) { ?>
    <div class="fui-cell-group fui-cell-click">
        <div class="fui-cell">
            <div class="fui-cell-text option-selector">请选择<?php  if(empty($spec_titles)) { ?>数量<?php  } else { ?><?php  echo $spec_titles;?>等<?php  } ?></div>
            <div class="fui-cell-remark"></div>
        </div>
    </div>
    <?php  } ?>
<?php  } else { ?>
<div class="fui-cell-group fui-cell-click nobuy" id="nobuy">
    <div class="fui-cell">
        <div class="fui-cell-text">
            <?php  if($goods['userbuy']==0) { ?>
           您已经超出最大<?php  echo $goods['usermaxbuy'];?>件购买量
            <?php  } else if($goods['levelbuy']==0) { ?>
            您当前会员等级没有购买权限
            <?php  } else if($goods['groupbuy']==0) { ?>
            您所在的用户组没有购买权限
            <?php  } else if($goods['timebuy'] ==-1) { ?>
            未到开始抢购时间!
            <?php  } else if($goods['timebuy'] ==1) { ?>
            抢购时间已经结束!
            <?php  } else if($goods['total'] <=0) { ?>
            商品已经售罄!
            <?php  } else { ?>
            <script>
                $("#nobuy").hide();
            </script>
            <?php  } ?></div>
    </div>
</div>

<?php  } ?>


<?php  if($packages && $goods['total'] > 0) { ?>
<?php  if(count($packages)<=3) { ?>
<style>
    .package-goods{padding:0.2rem 1rem;}
</style>
<div class="fui-cell-group fui-comment-group">
    <div class="fui-cell fui-cell-click">
        <div class="fui-cell-text desc">相关套餐</div>
        <div class="fui-cell-text desc label" onclick="javascript:window.location.href='<?php  echo mobileUrl('goods/package',array('goodsid'=>$goods['id']))?>'">更多套餐</div>
        <div class="fui-cell-remark"></div>
    </div>
    <div class="fui-cell">
        <div class="fui-cell-text comment ">
            <div class="fui-list package-list">
                <?php  if(is_array($packages)) { foreach($packages as $item) { ?>
                <div class="fui-list-inner package-goods" onclick="javascript:window.location.href='<?php  echo mobileUrl('goods/package/detail',array('pid'=>$package_goods['pid']))?>'">
                    <img src="<?php  echo tomedia($item['thumb'])?>" class="package-goods-img" alt="<?php  echo $item['title'];?>">
                    <span><?php  echo $item['title'];?></span>
                </div>
                <?php  } } ?>
            </div>
        </div>
    </div>
</div>
<?php  } else { ?>
<div class="fui-cell-group fui-comment-group">
    <div class="fui-cell fui-cell-click">
        <div class="fui-cell-text desc">相关套餐</div>
        <div class="fui-cell-text desc label" onclick="javascript:window.location.href='<?php  echo mobileUrl('goods/package',array('goodsid'=>$goods['id']))?>'">更多套餐</div>
        <div class="fui-cell-remark"></div>
    </div>
    <div id="product" class="fui-list">
        <span class="prev fui-list-media"><i class="icon icon-left"></i></span>
        <div id="content" class="fui-list-inner">
            <div id="content_list" onclick="javascript:window.location.href='<?php  echo mobileUrl('goods/package/detail',array('pid'=>$package_goods['pid']))?>'">
                <?php  if(is_array($packages)) { foreach($packages as $item) { ?>
                <dl class="package-goods package-goods3">
                    <dt><img class="package-goods-img" src="<?php  echo tomedia($item['thumb'])?>"/></dt>
                    <dd><?php  echo $item['title'];?></dd>
                </dl>
                <?php  } } ?>
            </div>
        </div>
        <span class="next fui-list-media"><i class="icon icon-right1"></i></span>
    </div>
</div>
<script type="text/javascript">
    $(function(){
        var page = 1;
        var i = 3; //每版放4个图片
        //向后 按钮
        $("span.next").click(function(){    //绑定click事件
            var content = $("div#content");
            var content_list = $("div#content_list");
            var v_width = content.width();
            var len = content.find("dl").length;
            var page_count = Math.ceil(len / i) ;   //只要不是整数，就往大的方向取最小的整数
            if( !content_list.is(":animated") ){    //判断“内容展示区域”是否正在处于动画
                if( page == page_count ){  //已经到最后一个版面了,如果再向后，必须跳转到第一个版面。
                    content_list.animate({ left : '0px'}, "slow"); //通过改变left值，跳转到第一个版面
                    page = 1;
                }else{
                    content_list.animate({ left : '-='+v_width }, "slow");  //通过改变left值，达到每次换一个版面
                    page++;
                }
            }
        });
        //往前 按钮
        $("span.prev").click(function(){
            var content = $("div#content");
            var content_list = $("div#content_list");
            var v_width = content.width();
            var len = content.find("dl").length;
            var page_count = Math.ceil(len / i) ;   //只要不是整数，就往大的方向取最小的整数
            if(!content_list.is(":animated") ){    //判断“内容展示区域”是否正在处于动画
                if(page == 1 ){  //已经到第一个版面了,如果再向前，必须跳转到最后一个版面。
                    content_list.animate({ left : '-='+v_width*(page_count-1) }, "slow");
                    page = page_count;
                }else{
                    content_list.animate({ left : '+='+v_width }, "slow");
                    page--;
                }
            }
        });
    });
</script>
<?php  } ?>

<script type="text/javascript">
    $(function(){
        $(".package-goods-img").height($(".package-goods-img").width());
    })
</script>
<?php  } ?>

<div id='comments-container'></div>

<!--店铺信息-->
<div class="fui-cell-group fui-shop-group">
    <a class='fui-list' href="<?php  echo $shopdetail['btnurl2'];?>">
        <div class='fui-list-media'>
            <img data-lazy="<?php  echo tomedia($shopdetail['logo'])?>" />
        </div>
        <div class='fui-list-inner'>
            <div class='title'><?php  echo $shopdetail['shopname'];?></div>
            <div class='subtitle'><?php  echo $shopdetail['description'];?></div>
        </div>
        <div class="fui-list-angle " style="margin-right: 0.2rem">
                <span class="btn btn-default-o external goshop"><?php  if(!empty($shopdetail['btntext2'])) { ?><?php  echo $shopdetail['btntext2'];?><?php  } else { ?>进店逛逛<?php  } ?></span>
        </div>
    </a>
</div>
<div class="fui-cell-group goods-detail-group">
    <div class="fui-cell">
        <div class="fui-cell-info">商品详情</div>
    </div>
</div>
<div class="goods-detail-content"></div>
<!--购买后显示-->
<?php  if($buyshow==1 && !empty($goods['buycontent'])) { ?>
<div class="fui-cell-group">
    <div class="fui-cell">
        <div class="content-block"><?php  echo $goods['buycontent'];?></div>
    </div>
</div>
<?php  } ?>

<?php  if($new_temp) { ?>
<?php  if(count($params)>0 || $showComments) { ?>
<div class="fui-tab fui-tab-danger detail-tab" id="tabnew">
    <a data-tab="tab2" class="active">详情</a>
    <?php  if(count($params)>0) { ?>
    <a data-tab="tab3">参数</a>
    <?php  } ?>
    <?php  if($showComments) { ?>
    <a data-tab="tab4">评价</a>
    <?php  } ?>
</div>
<?php  } else { ?>
<div class="fui-cell-group">
    <div class="fui-cell">
        <div class="fui-cell-info">商品详情</div>
    </div>
</div>
<?php  } ?>
<div class="detail-tab-panel">
    <div class="tab-panel show detail-block" data-tab="tab2">
        <div class="content-block content-images"></div>
    </div>
    <div class="tab-panel" data-tab="tab3">
        <div class="fui-cell-group">
            <?php  if(!empty($params)) { ?>
            <?php  if(is_array($params)) { foreach($params as $p) { ?>
            <div class="fui-cell">
                <div class="fui-cell-label" ><?php  echo $p['title'];?></div>
                <div class="fui-cell-info overflow"><?php  echo $p['value'];?></div>
            </div>
            <?php  } } ?>
            <?php  } else { ?>
            <div class="fui-cell">
                <div class="fui-cell-info text-align">商品没有参数</div>
            </div>
            <?php  } ?>
        </div>
    </div>
    <div class="tab-panel comment-block" data-tab="tab4" data-getcount='1' id='comments-list-container'>
        <div class='fui-icon-group col-5 '>
            <div class='fui-icon-col' data-level='all'><span class='text-danger'>全部<br/><span class="count"></span></span></div>
            <div class='fui-icon-col' data-level='good'><span>好评<br/><span class="count"></span></span></div>
            <div class='fui-icon-col' data-level='normal'><span>中评<br/><span class="count"></span></span></div>
            <div class='fui-icon-col' data-level='bad'><span>差评<br/><span class="count"></span></span></div>
            <div class='fui-icon-col' data-level='pic'><span>晒图<br/><span class="count"></span></span></div>
        </div>
        <div class='content-empty' style='display:none;'>暂时没有任何评价
        </div>
        <div class='container' id="comments-all"></div>
        <div class='infinite-loading'><span class='fui-preloader'></span><span class='text'> 正在加载...</span></div>
    </div>
</div>
<?php  } else { ?>
<!-- <div class="fui-cell-group">
    <div class="fui-cell">
        <div class="fui-cell-text text-center look-detail"><i class='icon icon-fold'></i> <span>上拉查看图文详情</span></div>
    </div>
</div> -->
<?php  } ?>

<?php  } ?>
</div>

<!--赠品弹层-->
<?php  if($isgift && $goods['total'] > 0) { ?>
<div id='gift-picker-modal' style="margin:-100%;">
    <div class='gift-picker'>
        <div class='fui-cell-title text-center' style="background: white;">请选择赠品</div>
        <div class="fui-cell-group fui-sale-group" style="margin-top:0; overflow-y: auto;">
            <div class="fui-cell">
                <div class="fui-cell-text dispatching">
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
        </div>
        <div class='btn btn-danger block fullbtn'>确定</div>
    </div>
</div>
<?php  } ?>
<!--底部按钮-->

<?php  if($goods['canbuy']) { ?>
<div class="fui-navbar bottom-buttons">
    <a class="nav-item external" href="https://tb.53kf.com/code/client/a3a434cdd4bfec1afab99ca1827ef5833/1">
        <span class="icon icon-message"></span>
        <span class="label" >客服</span>
    </a>
    <a class="nav-item favorite-item <?php  if($isFavorite) { ?>active<?php  } ?>" data-isfavorite="<?php  echo intval($isFavorite)?>">
        <span class="icon <?php  if($isFavorite) { ?>icon-likefill<?php  } else { ?>icon-like<?php  } ?>"></span>
        <span class="label" >收藏</span>
    </a>

    <a class="nav-item cart-item" href="<?php  echo mobileUrl('member/cart')?>" data-nocache="true" he="" id="menucart">
        <span id="cardtotal" class='badge <?php  if($cartCount<=0) { ?>out<?php  } else { ?>in<?php  } ?>'><?php  echo $cartCount;?></span>
        <span class="icon icon-cart"></span>
        <span class="label">购物车</span>
    </a>
    <?php  if($canAddCart) { ?>
    <a  class="nav-item " style="text-align: right;" >
        <span class="btn cartbtn"  data-type="<?php  echo $goods['type'];?>">加入购物车</span>
    </a>
    <?php  } ?>
    <?php  if(!empty($seckillinfo) && $seckillinfo['status']==1) { ?>
    <a  class="nav-item btn buybtn seckill-notstart">原价购买</a>
    <?php  } else { ?>
    <a  class="nav-item " style="text-align: left;" >
        <span class="btn buybtn" data-type="<?php  echo $goods['type'];?>">立刻购买</span>
    </a>
    <?php  } ?>
</div>
<?php  } ?>

<!--配送区域弹层-->
<?php  if($has_city) { ?>
<div id='city-picker-modal' style="margin: -100%;">
    <div class='city-picker'>
        <div class='fui-cell-title text-center' style="background: white;"><?php  if(empty($onlysent)) { ?>不<?php  } else { ?>只<?php  } ?>配送区域</div>
        <div class="fui-cell-group fui-sale-group" style="margin-top:0; overflow-y: auto;">
            <div class="fui-cell">
                <div class="fui-cell-text dispatching">
                    <div class="dispatching-info">
                        <?php  if(is_array($citys)) { foreach($citys as $item) { ?>
                        <?php  if(!empty($item)) { ?>
                        <i><?php  echo $item;?></i>
                        <?php  } ?>
                        <?php  } } ?>
                    </div>
                </div>
            </div>
        </div>
        <div class='btn btn-danger block fullbtn'>确定</div>
    </div>
</div>
<?php  } ?>

<!-- 产品参数 -->
<div id="picker-params-modal" style="margin: -100%">
    <div class='picker-params page-goods-detail'>
        <div class='fui-cell-title text-center' style="background: white;">
            商品参数
            <!-- <div class="closebtn"><i class="icon icon-guanbi1"></i></div> -->
        </div>

        <div class="fui-cell-group fui-params-group noborder" style="margin-top:0; overflow-y: auto;">
            <?php  if(!empty($params)) { ?>
            <?php  if(is_array($params)) { foreach($params as $p) { ?>
            <div class="fui-cell">
                <div class="fui-cell-label" ><?php  echo $p['title'];?></div>
                <div class="fui-cell-info"><?php  echo $p['value'];?></div>
            </div>
            <?php  } } ?>
            <?php  } else { ?>
            <div class="fui-cell">
                <div class="fui-cell-info text-align">商品没有参数</div>
            </div>
            <?php  } ?>
        </div>
        <div class="params-fullbtn">
            <div class="btn btn-danger block ">确定</div>
        </div>
    </div>
</div>
<!-- 产品参数 -->

<!-- 促销活动层 ---------------------------------------------------------------------------------------->
<div id="picker-sales-modal" style="margin: -100%">
    <div class='picker-sales page-goods-detail'>
        <div class='fui-cell-title text-center' style="background: white;">活&nbsp;动</div>
        <div class="fui-cell-group fui-sale-group noborder" style="margin-top:0; overflow-y: auto;">
            <!-- 商城满减 -->
            <?php  if($enoughfree || ($enoughs && count($enoughs)>0)) { ?>
                <?php  if($enoughs && count($enoughs)>0 && empty($seckillinfo)) { ?>
                <div class="fui-cell fui-sale-cell">
                    <div class="fui-cell-label">
                        <span class="sale-tip">满减</span>
                    </div>
                    <div class="fui-cell-text" style="white-space: inherit;">
                        <?php  if(is_array($enoughs)) { foreach($enoughs as $key => $enough) { ?>
                        <div>全场 满<span class="text-danger">&yen;<?php  echo $enough['enough'];?></span>立减<span class="text-danger">&yen;<?php  echo $enough['money'];?></span></div>
                        <?php  } } ?>
                    </div>
                </div>
                <?php  } ?>
            <?php  } ?>

            <!-- 商户满减 -->
            <?php  if(!empty($merch_set['enoughdeduct']) && empty($seckillinfo)) { ?>
            <div class="fui-cell fui-sale-cell">
                <div class="fui-cell-label">
                    <span class="sale-tip">满减</span>
                </div>
                <div class="fui-cell-text" style="white-space: inherit;">
                    <?php  if(is_array($merch_set['enoughs'])) { foreach($merch_set['enoughs'] as $key => $enough) { ?>
                    <div>商户 满<span class="text-danger">&yen;<?php  echo $enough['enough'];?></span>立减<span class="text-danger">&yen;<?php  echo $enough['give'];?></span></div>
                    <?php  } } ?>
                </div>
            </div>
            <?php  } ?>

            <!-- 包邮 -->
            <?php  if($hasSales && empty($seckillinfo)) { ?>
                <?php  if((!is_array($goods['dispatchprice']) && $goods['type']==1 && $goods['isverify']!=2 && $goods['dispatchprice']==0) || (($enoughfree && $enoughfree==-1) || ($enoughfree>0 || $goods['ednum']>0 || $goods['edmoney']>0))) { ?>
                <div class="fui-cell fui-sale-cell">
                    <div class="fui-cell-label">
                        <span class="sale-tip">包邮</span>
                    </div>
                    <div class="fui-cell-text">
                        <?php  if(!is_array($goods['dispatchprice'])) { ?>
                            <?php  if($goods['type']==1 && $goods['isverify']!=2) { ?>
                                <?php  if($goods['dispatchprice']==0) { ?>
                                <div>本商品包邮</div>
                                <?php  } ?>
                            <?php  } ?>
                        <?php  } ?>
                        <?php  if($enoughfree && $enoughfree==-1) { ?><div>全场包邮</div>
                        <?php  } else { ?>
                            <?php  if($enoughfree>0) { ?><div>全场满<span class="text-danger">&yen;<?php  echo $enoughfree;?></span>包邮</div><?php  } ?>
                            <?php  if($goods['ednum']>0) { ?><div>单品满<span class="text-danger"><?php  echo $goods['ednum'];?></span> <?php echo empty($goods['unit'])?'件':$goods['unit']?>包邮</div><?php  } ?>
                            <?php  if($goods['edmoney']>0) { ?><div>单品满<span class="text-danger">&yen;<?php  echo $goods['edmoney'];?></span>包邮</div><?php  } ?>
                        <?php  } ?>
                    </div>
                </div>
                <?php  } ?>
            <?php  } ?>

            <!-- <?php  echo $_W['shopset']['trade']['credittext'];?> -->
            <?php  if((!empty($goods['deduct']) && $goods['deduct'] != '0.00')  || !empty($goods['credit'])) { ?>
            <div class="fui-cell fui-sale-cell">
                <div class="fui-cell-label">
                    <span class="sale-tip"><?php  echo $_W['shopset']['trade']['credittext'];?></span>
                </div>
                <div class="fui-cell-text">
                    <?php  if(!empty($goods['deduct']) && $goods['deduct'] != '0.00') { ?><div>最高抵扣<span class="text-danger">&yen;<?php  echo $goods['deduct'];?></span></div><?php  } ?>
                    <?php  if(!empty($goods['credit'])) { ?><div>购买赠送<span class="text-danger"><?php  echo $goods['credit'];?></span><?php  echo $_W['shopset']['trade']['credittext'];?></div><?php  } ?>
                </div>
            </div>
            <?php  } ?>

            <!-- 二次购买 -->
            <?php  if(floatval($goods['buyagain'])>0 && empty($seckillinfo)) { ?>
            <div class="fui-cell fui-sale-cell">
                <div class="fui-cell-label">
                    <span class="sale-tip">复购</span>
                </div>
                <div class="fui-cell-text">
                    此商品重复购买可享受<span class="text-danger"><?php  echo $goods['buyagain'];?></span>折优惠<?php  if(empty($goods['buyagain_sale'])) { ?><br>重复购买 不与其他优惠共享<?php  } ?>
                </div>
            </div>
            <?php  } ?>

            <?php  if($isfullback) { ?>
            <div class="fui-cell fui-sale-cell">
                <div class="fui-cell-label">
                    <span class="sale-tip">全返</span>
                </div>
                <div class="fui-cell-text" style="white-space: inherit;">
                    <div class="fullback-info">
                    <p style="display: block;"><i>&yen;</i>全返总额：
                        <?php  if($fullbackgoods['type']>0) { ?>
                        <?php  if($goods['hasoption'] > 0) { ?>
                        <?php  if($fullbackgoods['minallfullbackallratio'] == $fullbackgoods['maxallfullbackallratio']) { ?>
                        <?php  echo price_format($fullbackgoods['minallfullbackallratio'],2)?>%
                        <?php  } else { ?>
                        <?php  echo price_format($fullbackgoods['minallfullbackallratio'],2)?>% ~ <?php  echo price_format($fullbackgoods['maxallfullbackallratio'],2)?>%
                        <?php  } ?>
                        <?php  } else { ?>
                        <?php  echo price_format($fullbackgoods['minallfullbackallratio'],2)?>%
                        <?php  } ?>
                        <?php  } else { ?>
                        <?php  if($goods['hasoption'] > 0) { ?>
                        <?php  if($fullbackgoods['minallfullbackallprice'] != $fullbackgoods['maxallfullbackallprice']) { ?>
                        <?php  if($fullbackgoods['credit']==1) { ?>积分<?php  } else { ?>&yen;<?php  } ?>
                        <?php  echo price_format($fullbackgoods['minallfullbackallprice'],2)?>
                        <?php  } else { ?>
                        <?php  if($fullbackgoods['credit']==1) { ?>积分<?php  } else { ?>&yen;<?php  } ?>
                        <?php  echo price_format($fullbackgoods['minallfullbackallprice'],2)?> ~<?php  if($fullbackgoods['credit']==1) { ?>积分<?php  } else { ?>&yen;<?php  } ?><?php  echo price_format($fullbackgoods['maxallfullbackallprice'],2)?>
                        <?php  } ?>
                        <?php  } else { ?>
                        <?php  if($fullbackgoods['credit']==1) { ?>积分<?php  } else { ?>&yen;<?php  } ?>
                        <?php  echo price_format($fullbackgoods['minallfullbackallprice'],2)?>
                        <?php  } ?>
                        <?php  } ?>
                    </p>
                    <p style="display: block;"><i>&yen;</i>每天返：
                        <?php  if($fullbackgoods['type']>0) { ?>
                        <?php  if($goods['hasoption'] > 0) { ?>
                        <?php  if($fullbackgoods['minfullbackratio'] == $fullbackgoods['maxfullbackratio']) { ?>
                        <?php  echo price_format($fullbackgoods['minfullbackratio'],2)?>%
                        <?php  } else { ?>
                        <?php  echo price_format($fullbackgoods['minfullbackratio'],2)?>% ~ <?php  echo price_format($fullbackgoods['maxfullbackratio'],2)?>%
                        <?php  } ?>
                        <?php  } else { ?>
                        <?php  echo price_format($fullbackgoods['fullbackratio'],2)?>%
                        <?php  } ?>
                        <?php  } else { ?>
                        <?php  if($goods['hasoption'] > 0) { ?>
                        <?php  if($fullbackgoods['minfullbackprice'] == $fullbackgoods['maxfullbackprice']) { ?>
                        <?php  if($fullbackgoods['credit']==1) { ?>积分<?php  } else { ?>&yen;<?php  } ?><?php  echo price_format($fullbackgoods['minfullbackprice'],2)?>
                        <?php  } else { ?>
                        <?php  if($fullbackgoods['credit']==1) { ?>积分<?php  } else { ?>&yen;<?php  } ?><?php  echo price_format($fullbackgoods['minfullbackprice'],2)?> ~ <?php  if($fullbackgoods['credit']==1) { ?>积分<?php  } else { ?>&yen;<?php  } ?><?php  echo price_format($fullbackgoods['maxfullbackprice'],2)?>
                        <?php  } ?>
                        <?php  } else { ?>
                        <?php  if($fullbackgoods['credit']==1) { ?>积分<?php  } else { ?>&yen;<?php  } ?><?php  echo price_format($fullbackgoods['fullbackprice'],2)?>
                        <?php  } ?>
                        <?php  } ?>
                    </p>
                    <p style="display: block;"><i>&yen;</i>时间：
                        <?php  if($goods['hasoption'] > 0) { ?>
                        <?php  if($fullbackgoods['minday'] == $fullbackgoods['maxday']) { ?>
                        <?php  echo $fullbackgoods['minday'];?>
                        <?php  } else { ?>
                        <?php  echo $fullbackgoods['minday'];?> ~ <?php  echo $fullbackgoods['maxday'];?>
                        <?php  } ?>
                        <?php  } else { ?>
                        <?php  echo $fullbackgoods['day'];?>
                        <?php  } ?>天
                    </p>
                    </div>
                    <?php  if($fullbackgoods['startday']>0) { ?>
                    <div class="fullback-remark" style="line-height: inherit;">
                        确认收货<?php  echo $fullbackgoods['startday'];?>天后开始全返。如申请退款，则全返金额退还。
                    </div>
                    <?php  } ?>
                </div>
            </div>
            <?php  } ?>
        </div>
        <div class="fullbtn">
            <div class="btn btn-danger block ">确定</div>
        </div>
    </div>
</div>


<?php  if($goods['type']==4) { ?>
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('goods/wholesalePicker', TEMPLATE_INCLUDEPATH)) : (include template('goods/wholesalePicker', TEMPLATE_INCLUDEPATH));?>
<?php  } else { ?>
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('goods/picker', TEMPLATE_INCLUDEPATH)) : (include template('goods/picker', TEMPLATE_INCLUDEPATH));?>
<?php  } ?>

<!--评论模版-->
<?php  if($getComments) { ?>
<script type='text/html' id='tpl_goods_detail_comments_list'>
    <div class="fui-cell-group fui-comment-group">
        <%each list as comment%>
        <div class="fui-cell">
            <div class="fui-cell-icon">
                    <div class='img'><img src='<%comment.headimgurl%>'/></div>
            </div>
            <div class="fui-cell-text comment ">
                <div class="info head">
                   
                    <div class='nickname'><%comment.nickname%></div>

                    <div class="date"><%comment.createtime%></div>
                    <div class="star star1">
                        <span <%if comment.level>=1%>class="shine"<%/if%>>★</span>
                        <span <%if comment.level>=2%>class="shine"<%/if%>>★</span>
                        <span <%if comment.level>=3%>class="shine"<%/if%>>★</span>
                        <span <%if comment.level>=4%>class="shine"<%/if%>>★</span>
                        <span <%if comment.level>=5%>class="shine"<%/if%>>★</span>
                    </div>
                </div>
                <div class="remark"><%comment.content%></div>
                <%if comment.images.length>0%>
                <div class="remark img">
                    <%each comment.images as img%>
                    <div class="img"><img data-lazy="<%img%>" /></div>
                    <%/each%>
                </div>
                <%/if%>

                <%if comment.reply_content%>
                <div class="reply-content" style="background:#EDEDED;">
                    掌柜回复：<%comment.reply_content%>
                    <%if comment.reply_images.length>0%>
                    <div class="remark img">
                        <%each comment.reply_images as img%>
                        <div class="img"><img data-lazy="<%img%>" /></div>
                        <%/each%>
                    </div>
                    <%/if%>
                </div>
                <%/if%>
                <%if comment.append_content && comment.replychecked==0%>
                <div class="remark reply-title">用户追加评价</div>
                <div class="remark"><%comment.append_content%></div>
                <%if comment.append_images.length>0%>
                <div class="remark img">
                    <%each comment.append_images as img%>
                    <div class="img"><img data-lazy="<%img%>" /></div>
                    <%/each%>
                </div>
                <%/if%>
                <%if comment.append_reply_content%>
                <div class="reply-content" style="background:#EDEDED;">
                    掌柜回复：<%comment.append_reply_content%>
                    <%if comment.append_reply_images.length>0%>
                    <div class="remark img">
                        <%each comment.append_reply_images as img%>
                        <div class="img"><img data-lazy="<%img%>" /></div>
                        <%/each%>
                    </div>
                    <%/if%>
                </div>
                <%/if%>
                <%/if%>
            </div>
        </div>
        <%/each%>
    </div>
</script>
<!--评价-->
<script type='text/html' id='tpl_goods_detail_comments'>
    <div class="fui-cell-group fui-comment-group">

        <div class="fui-cell fui-cell-click">
            <div class="fui-cell-text desc">评价(<%count.all%>)</div>
            <!-- <div class="fui-cell-text desc label"><span><%percent%>%</span> 好评</div> -->
            <div class="fui-cell-remark btn-show-allcomment">全部评论</div>
        </div>
        <%each list as comment%>
        <div class="fui-cell comment-item">
            <div class="fui-cell-icon">
                    <div class="img"><img src='<%comment.headimgurl%>'></div>
            </div>
            <div class="fui-cell-text comment ">
                <div class="info">
                    
                    <div class="nickname"><%comment.nickname%> </div>
                        
                   
                    <div class="date"><%comment.createtime%></div>
                    <div class="star">
                            <span <%if comment.level>=1%>class="shine"<%/if%>>★</span>
                            <span <%if comment.level>=2%>class="shine"<%/if%>>★</span>
                            <span <%if comment.level>=3%>class="shine"<%/if%>>★</span>
                            <span <%if comment.level>=4%>class="shine"<%/if%>>★</span>
                            <span <%if comment.level>=5%>class="shine"<%/if%>>★</span>
                        </div>
                </div>
                <div class="remark"><%comment.content%></div>
                <%if comment.images.length>0%>
                <div class="remark img">
                    <%each comment.images as img index%>
                        <%if index<3%>
                        <div class="img"><img data-lazy="<%img%>" height="50" /></div>
                        <%/if%>
                    <%/each%>
                </div>
                <%/if%>
            </div>
        </div>
        <%/each%>
       <!-- <div style="text-align: center;margin: 0.8rem 0">
           <span class="btn btn-default-o external btn-show-allcomment">查看全部评价</span>
       </div> -->
    </div>
</script>
<?php  } ?>

<?php  } else { ?>

<div class='fui-content'>
    <div class='content-empty'>
        <i class='icon icon-searchlist'></i><br/> 商品已经下架，或者已经删除!<br/><a href="<?php  echo mobileUrl()?>" class='btn btn-default-o external'>到处逛逛</a>
    </div>
</div>
<?php  } ?>

<!--分享弹层-->
<div id='cover'>
    <div class='fui-mask-m visible'></div>
    <div class='arrow'></div>
    <div class='content'><?php  if(empty($share['goods_detail_text'])) { ?>请点击右上角<br/>通过【发送给朋友】<br/>邀请好友购买<?php  } else { ?><?php  echo $share['goods_detail_text'];?><?php  } ?></div>
</div>


<!--优惠券弹层完成-->
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('sale/coupon/util/couponpicker', TEMPLATE_INCLUDEPATH)) : (include template('sale/coupon/util/couponpicker', TEMPLATE_INCLUDEPATH));?>
<script language="javascript">
    require(['biz/goods/detail'], function (modal) {
        modal.init({
            goodsid:"<?php  echo $goods['id'];?>",
            getComments : "<?php  echo $getComments;?>",
            // storeid : "<?php  echo $_GPC['storeid'];?>",
            // storename : "<?php  echo $_GPC['storename'];?>",
            seckillinfo: <?php  echo json_encode($seckillinfo)?>,
            attachurl_local:"<?php  echo $GLOBALS['_W']['attachurl_local'];?>",
            attachurl_remote:"<?php  echo $GLOBALS['_W']['attachurl_remote'];?>",
            coupons: <?php  echo json_encode($coupons)?>,
            new_temp: <?php  echo $new_temp;?>,
            liveid: <?php  echo $liveid;?>
        });
    });
</script>
</div>

<!--分享二维码弹层-->

<div id="box_alert" style="display: none;">
    <div class="box_header">
         <img id="box_user_img" src="<?php  echo $codedata['portrait']['thumb']?>" width="100%" height="100%" class="alert-goods-img" alt="">
           <p id="box_header_title"><?php  echo $codedata['shopname']['text']?></p>
    </div>
<div id="box_section">
      <img id="box_section_img" src="<?php  echo $codedata['thumb']['thumb']?>" width="100%" height="100%" class="alert-goods-img" alt="">
</div>
<div id="box_footer">
    <img id="box_footer_qrcode" src="<?php  echo $codedata['qrcode']['thumb']?>" width="100%" height="100%" class="alert-goods-img" alt="">
    <!-- title -->
       <p id="box_footer_title"><?php  echo $codedata['title']['text']?></p>
    <!-- price -->
    <p id="box_footer_price"><?php  echo $codedata['price']['text']?></p>
</div>
</div>
  <script type="text/javascript" src="../addons/ewei_shopv2/static/js/detail.js"></script>
<div id="alert-picker">
    <script type="text/javascript" src="../addons/ewei_shopv2/static/js/dist/jquery/jquery.qrcode.min.js"></script>
    <script type="text/javascript">
        $(function(){
            $(".alert-qrcode-i").html('')
            $(".alert-qrcode-i").qrcode({
                typeNumber: 0,      //计算模式
                correctLevel: 0,//纠错等级
                text:"<?php  echo mobileUrl('goods/detail', array('id'=>$goods['id'],'mid'=>$mid),true)?>"
            });

        });
    </script>

    <div id="alert-mask"></div>
    <?php  if($commission_data['codeShare'] == 1) { ?>
    <div class="alert-content">
        <div class="alert" style="padding:0;">
            <i class="alert-close alert-close1 icon icon-close"></i>
            <div class="fui-list alert-header" style="-webkit-border-radius: 0.3rem;border-radius: 0.3rem;padding:0;">
            <!-- <img src="<?php  echo tomedia($goodscode)?>" width="100%" height="100%" class="alert-goods-img" alt=""> -->
              <img id="avatar" width="100%" height="100%" class="alert-goods-img" crossOrigin="Anonymous" alt="">
                <!-- <img src="<?php  echo $parameter['codedata']['portrait']['thumb']?>" width="100%" height="100%" class="alert-goods-img" alt=""> -->
                <!-- <img src="<?php  m('goods')->createcode(tomedia($parameter))?>" width="100%" height="100%" class="alert-goods-img" alt=""> -->
                <!-- <?php  m('goods')->createcode(tomedia($parameter))?> -->
                <!-- <script src="../../../../core/mobile/goods/detail.php" type="text/javascript"></script> -->
                <!-- <?php  m('goods')->createcode(tomedia($parameter))?> -->
            </div>
            <!--<div class="fui-list alert-header">
                <div class="fui-list-media">
                    <img class="round" src="<?php  echo tomedia($_W['shopset']['shop']['logo'])?>">
                </div>
                <div class="fui-list-inner">
                    <?php  echo $_W['shopset']['shop']['name'];?>
                </div>
            </div>
            <img src="<?php  echo tomedia($goods['thumb'])?>" class="alert-goods-img" alt="">
            <div class="fui-list alert-qrcode">
                <div class="fui-list-media">
                    <i class="alert-qrcode-i"></i>
                </div>
                <div class="fui-list-inner alert-content">
                    <h2 class="alert-title"><?php  echo $goods['title'];?></h2>
                    <span>&yen;<?php  if($goods['minprice']==$goods['maxprice']) { ?><?php  echo $goods['minprice'];?><?php  } else { ?><?php  echo $goods['minprice'];?>~<?php  echo $goods['maxprice'];?><?php  } ?></span>
                    <?php  if($goods['isdiscount'] && $goods['isdiscount_time']>=time()) { ?>
                    <del>&yen;<?php  echo $goods['productprice'];?></del>
                    <?php  } else { ?>
                    <?php  if($goods['productprice']>0) { ?><del>&yen;<?php  echo $goods['productprice'];?></del><?php  } ?>
                    <?php  } ?>
                </div>
            </div>
            <div class="share-text1">截屏保存分享给您的朋友</div>-->
        </div>
    </div>
    <?php  } else if($commission_data['codeShare'] == 2) { ?>
    <div class="alert-content">
        <div class="alert2">
            <div class="fui-list alert-header" style="-webkit-border-radius: 0.3rem;border-radius: 0.3rem;padding:0;">
                <!-- <img src="<?php  echo tomedia($goodscode)?>" width="100%" height="100%" class="alert-goods-img" alt=""> -->
                <!-- <img src="<?php  echo $parameter['codedata']['thumb']['thumb']?>" width="100%" height="100%" class="alert-goods-img" alt=""> -->
                         <img id="avatar" width="100%" height="100%" class="alert-goods-img" crossOrigin="Anonymous" alt="">

            </div>
            <!--<div class="fui-list alert2-goods">
                <div class="fui-list-media">
                    <img src="<?php  echo tomedia($goods['thumb'])?>" class="alert2-goods-img" alt="">
                </div>
                <div class="fui-list-inner">
                    <h2 class="alert2-title"><?php  echo $goods['title'];?></h2>
                    <span>&yen;<?php  if($goods['minprice']==$goods['maxprice']) { ?><?php  echo $goods['minprice'];?><?php  } else { ?><?php  echo $goods['minprice'];?>~<?php  echo $goods['maxprice'];?><?php  } ?></span>
                    <?php  if($goods['isdiscount'] && $goods['isdiscount_time']>=time()) { ?>
                    <del>&yen;<?php  echo $goods['productprice'];?></del>
                    <?php  } else { ?>
                    <?php  if($goods['productprice']>0) { ?><del>&yen;<?php  echo $goods['productprice'];?></del><?php  } ?>
                    <?php  } ?>
                </div>
            </div>
            <div class="alert2-qrcode">
                <i class="alert-qrcode-i"></i>
            </div>
            <div class="share-text2">截屏保存分享给您的朋友</div>
            <a href="javascript:void(0);" class="alert-close2"><?php  echo $_W['shopset']['shop']['name'];?></a>-->
        </div>
    </div>
    <?php  } else { ?>
    <div class="alert-content">
        <div class="alert" style="padding:0;background: #f5f4f9;border:none;-webkit-border-radius: 0.3rem;border-radius: 0.3rem;top:2rem;">
            <i class="alert-close alert-close1 icon icon-close" style="right: -0.7rem;top: -0.8rem;background: #e1040d;border:none;z-index:10"></i>
            <div class="fui-list alert-header" style="-webkit-border-radius: 0.3rem;border-radius: 0.3rem;padding:0;">
                <!-- <img src="<?php  echo tomedia($goodscode)?>" class="alert-goods-img" alt=""> -->
           
                <!-- <img src="<?php  echo $parameter['codedata']['thumb']['thumb']?>" width="100%" height="100%" class="alert-goods-img" alt=""> -->
                        <img id="avatar" width="100%" height="100%" class="alert-goods-img" crossOrigin="Anonymous" alt="">

            </div>
        </div>
    </div>
    <?php  } ?>
</div>

<style type="text/css">
    .share-text1{text-align: center;padding:0.5rem 0.5rem 0;font-size:0.6rem;color:#666;line-height: 1rem;}
    .share-text2{text-align: center;padding:0 0.5rem 0.5rem;font-size:0.6rem;color:#666;line-height: 1rem;}
</style>


<script type="text/javascript">
    $('#dianji').click(function() {
        if ($('#dianji').attr('stop')) {
            return
        }
        
        
        $('#dianji').html('').attr('stop', 1);
        $.get("<?php  echo mobileUrl('goods/detail','',true)?>", {
            status: '1',
            goodsid: $('#goodsid').val()
           
        }, function(b) {
            
        }, 'json')
    });
</script>





<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>
<!--www-efwww-com-->