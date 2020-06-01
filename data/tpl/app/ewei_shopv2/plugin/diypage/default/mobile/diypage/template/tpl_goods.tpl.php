<?php defined('IN_IA') or exit('Access Denied');?><?php  if(!empty($diyitem['data'])) { ?>
    <?php $textyuan = !empty($_W['shopset']['commission']['texts']['yuan'])?$_W['shopset']['commission']['texts']['yuan']:'元'?>
    <?php $textjifen = !empty($_W['shopset']['trade']['credittext'])?$_W['shopset']['trade']['credittext']:'积分'?>
    <?php  if(empty($diyitem['params']['goodsscroll'])) { ?>
  <style>

    .fui-goods-group.block .fui-goods-item {
        overflow: hidden;
        background-color:#fff !important;
        width: 47%;
        margin-left: 2%;
        padding: 0;
        border-radius: 6px;
        margin-bottom: 8px;
    }
  
    .fui-goods-group.block{
        padding: 0!important;
    }
</style>
        <div class="fui-goods-group <?php  echo $diyitem['style']['liststyle'];?>" style="background: <?php  echo $diyitem['style']['background'];?>;">
            <?php  if(is_array($diyitem['data'])) { foreach($diyitem['data'] as $goodsitem) { ?>
                <a class="fui-goods-item" data-goodsid="<?php  echo $goodsitem['gid'];?>" href="<?php echo mobileUrl(empty($diyitem['params']['goodstype'])?'goods/detail':'creditshop/detail', array('id'=>$goodsitem['gid']))?>" data-nocache="true" style="position: relative;">
                    <div class="image <?php echo $diyitem['style']['liststyle']=='block one'?'auto':''?>  <?php  if($diyitem['params']['showicon']==1) { ?><?php  echo $diyitem['style']['iconstyle'];?><?php  } ?>" data-text="
                            <?php  if($diyitem['style']['goodsicon']=='recommand') { ?>推荐<?php  } ?>
                            <?php  if($diyitem['style']['goodsicon']=='hotsale') { ?>热销<?php  } ?>
                            <?php  if($diyitem['style']['goodsicon']=='isnew') { ?>新上<?php  } ?>
                            <?php  if($diyitem['style']['goodsicon']=='sendfree') { ?>包邮<?php  } ?>
                            <?php  if($diyitem['style']['goodsicon']=='istime') { ?>限时卖<?php  } ?>
                            <?php  if($diyitem['style']['goodsicon']=='bigsale') { ?>促销<?php  } ?>
                        " <?php  if($diyitem['style']['liststyle']!='block one') { ?>data-lazy-background="<?php  echo tomedia($goodsitem['thumb'])?>"<?php  } else { ?>style="background:none; min-height: 50px;"<?php  } ?>>
                        <?php  if($diyitem['style']['liststyle']=='block one') { ?>
                            <img class="exclude" src="<?php  echo tomedia($_W['shopset']['shop']['loading'])?>" data-lazy="<?php  echo tomedia($goodsitem['thumb'])?>" />
                        <?php  } ?>
                        <?php  if($diyitem['params']['showicon']==2) { ?>
                            <div class="goodsicon <?php  echo $diyitem['params']['iconposition'];?>  "
                                 <?php  if($diyitem['params']['iconposition']=='left top') { ?>
                                    style="top: <?php  echo $diyitem['style']['iconpaddingtop'];?>px; left: <?php  echo $diyitem['style']['iconpaddingleft'];?>px; text-align: left;"
                                 <?php  } else if($diyitem['params']['iconposition']=='right top') { ?>
                                    style="top: <?php  echo $diyitem['style']['iconpaddingtop'];?>px; right: <?php  echo $diyitem['style']['iconpaddingleft'];?>px; text-align: right;"
                                 <?php  } else if($diyitem['params']['iconposition']=='left bottom') { ?>
                                    style="bottom: <?php  echo $diyitem['style']['iconpaddingtop'];?>px; left: <?php  echo $diyitem['style']['iconpaddingleft'];?>px; text-align: left;"
                                 <?php  } else if($diyitem['params']['iconposition']=='left bottom') { ?>
                                    style="bottom: <?php  echo $diyitem['style']['iconpaddingtop'];?>px; right: <?php  echo $diyitem['style']['iconpaddingleft'];?>px; text-align: right;"
                                 <?php  } ?>
                            >
                                <?php  if($diyitem['params']['showicon']==1) { ?>


                                <?php  } else if($diyitem['params']['showicon']==2 && !empty($diyitem['params']['goodsiconsrc'])) { ?>
                                    <img class="exclude" src="<?php  echo tomedia($diyitem['params']['goodsiconsrc'])?>" onerror="this.src=''" style="width: <?php  echo $diyitem['style']['iconzoom'];?>%;" />
                                <?php  } ?>
                            </div>
                        <?php  } ?>
                    </div>
                    <?php  if($diyitem['params']['showtitle']==1 || $diyitem['params']['showprice']==1) { ?>
                        <div class="detail">
                            <?php  if($diyitem['params']['showtitle']==1) { ?>
                                <div class="name" style="color: <?php  echo $diyitem['style']['titlecolor'];?>;">
                                    <?php  if($goodsitem['bargain']>0) { ?>
                                        <label style="background-color: <?php  echo $diyitem['style']['buybtncolor'];?>; padding: 0px 2px; color: #fff; font-size: 0.6rem">砍价</label>
                                    <?php  } ?>
                                    <?php  if(!empty($diyitem['params']['goodstype']) && $diyitem['params']['showtag']>0) { ?>
                                        <label style="background-color: <?php  echo $diyitem['style']['tagbackground'];?>; padding: 0px 2px; color: #fff; font-size: 0.6rem"><?php  if($diyitem['params']['showtag']==1&&$goodsitem['productprice']>0) { ?>&yen<?php  echo $goodsitem['productprice'];?><?php  } else if($diyitem['params']['showtag']==2) { ?><?php  if($goodsitem['gtype']==0) { ?>商品<?php  } else if($goodsitem['gtype']==1) { ?>优惠券<?php  } else if($goodsitem['gtype']==2) { ?>余额<?php  } else if($goodsitem['gtype']==3) { ?>红包<?php  } ?><?php  } else if($diyitem['params']['showtag']==3) { ?><?php  if($goodsitem['ctype']==1) { ?>抽奖<?php  } else { ?>兑换<?php  } ?><?php  } ?></label>
                                    <?php  } ?>
                                    <?php  echo $goodsitem['title'];?>
                                </div>
                                
                                <span style="display: inline-block; height: 0.7rem;line-height: 0.8rem;overflow: hidden;font-size: 0.6rem;"><?php  echo $goodsitem['subtitle'];?></span>
        
                            <?php  } ?>
                            <?php  if(!empty($goodsitem['commission'])) { ?>
                            <!--<p class="productprice" style="display: inline-block;overflow: hidden;font-size: 0.6rem;height:1.2rem;line-height: 1.2rem;">-->
                            <!--    <span class="buy buybtn-1" style="background: #FCE0E0;color: #fe5455;border: #fe5455 1px solid;padding: 3px;border-radius: 3px;">邀请下单：￥<?php  echo $goodsitem['commission'];?></span>-->
                            <!--</p>-->
                            <?php  } else { ?>
                            <!--<p class="productprice" style="display: inline-block;overflow: hidden;font-size: 0.6rem;height:1.2rem;line-height: 1.2rem;">-->
                            <!--    <span class="buy buybtn-1" style="padding: 3px;border-radius: 3px;"></span>-->
                            <!--</p>-->
                            <?php  } ?>
                            <?php  if($diyitem['params']['showprice']==1) { ?>

                           
                            <p class="productprice <?php  if(empty($diyitem['params']['showproductprice']) && $diyitem['params']['showsales']!=1) { ?>noheight<?php  } ?>" style="line-height: 1rem;height: 1rem;" >

                                    <?php  if((!empty($diyitem['params']['showproductprice']) && $goodsitem['productprice']>0 && $goodsitem['productprice']>$goodsitem['price'])) { ?>
                                    <span style="color: <?php  echo $diyitem['style']['productpricecolor'];?>;"><?php echo !empty($diyitem['params']['productpricetext'])?$diyitem['params']['productpricetext']:''?><span <?php  if(!empty($diyitem['params']['productpriceline'])) { ?>style="text-decoration: line-through;"<?php  } ?>>&yen;<?php  echo $goodsitem['productprice'];?></span></span>
                                    <?php  } ?>
                                    <?php  if($diyitem['params']['showsales']==1) { ?>
                                    <span style="color: <?php  echo $diyitem['style']['salescolor'];?>;"><?php echo !empty($diyitem['params']['salestext'])?$diyitem['params']['salestext']:'销量'?>: <?php  echo $goodsitem['sales'];?></span>
                                    
                                    <?php  } ?>


                            </p>
                                <div class="price">

                                    <span class="text" style="color: <?php  echo $diyitem['style']['pricecolor'];?>;">
                                        <?php  if(empty($diyitem['params']['goodstype'])) { ?>
                                            <p class="minprice">&yen;<?php  echo $goodsitem['price'];?></p>
                                        <?php  } else { ?>
                                            <p>
                                                <?php  if($goodsitem['price']==0&&$goodsitem['credit']==0) { ?>免费
                                                <?php  } else if($goodsitem['price']>0&&$goodsitem['credit']==0) { ?><?php  echo $goodsitem['price'];?><small><?php  echo $textyuan;?></small>
                                                <?php  } else if($goodsitem['price']==0&&$goodsitem['credit']>0) { ?><?php  echo $goodsitem['credit'];?><small><?php  echo $textjifen;?></small>
                                                <?php  } else if($goodsitem['price']>0&&$goodsitem['credit']>0) { ?><?php  echo $goodsitem['credit'];?><small><?php  echo $textjifen;?></small>+<?php  echo $goodsitem['price'];?><small><?php  echo $textyuan;?></small><?php  } ?>
                                            </p>
                                        <?php  } ?>

                                    </span>
                                    <?php  if(!empty($diyitem['style']['buystyle']) && empty($goodsitem['bargain']) && empty($diyitem['params']['goodstype'])) { ?>
                                        <?php  if($diyitem['style']['buystyle']=='buybtn-1') { ?>
                                            <span class="buy buybtn-1" style="border-color: <?php  echo $diyitem['style']['buybtncolor'];?>;color:  <?php  echo $diyitem['style']['buybtncolor'];?>">购买</span>
                                        <?php  } else if($diyitem['style']['buystyle']=='buybtn-2') { ?>
                                            <span class="buy buybtn-2" style="background-color: <?php  echo $diyitem['style']['buybtncolor'];?>;">购买</span>
                                        <?php  } else if($diyitem['style']['buystyle']=='buybtn-3') { ?>
                                            <span class="buy buybtn-3" style="background-color: <?php  echo $diyitem['style']['buybtncolor'];?>;"><i class="icon icon-cartfill"></i></span>
                                        <?php  } else if($diyitem['style']['buystyle']=='buybtn-4') { ?>
                                            <span class="buy buybtn-4" style="border-color: <?php  echo $diyitem['style']['buybtncolor'];?>;"><i class="icon icon-cart" style="color: <?php  echo $diyitem['style']['buybtncolor'];?>"></i></span>
                                        <?php  } else if($diyitem['style']['buystyle']=='buybtn-5') { ?>
                                            <span class="buy buybtn-5" style="border-color: <?php  echo $diyitem['style']['buybtncolor'];?>;"><i class="icon icon-add" style="color:  <?php  echo $diyitem['style']['buybtncolor'];?>"></i></span>
                                        <?php  } else if($diyitem['style']['buystyle']=='buybtn-6') { ?>
                                            <span class="buy buybtn-6" style="background-color: <?php  echo $diyitem['style']['buybtncolor'];?>;"><i class="icon icon-add"></i></span>
                                        <?php  } ?>
                                    <?php  } else if(!empty($goodsitem['bargain'])) { ?>
                                        <span class="buy buybtn-1" style="border-color: <?php  echo $diyitem['style']['buybtncolor'];?>;color:  <?php  echo $diyitem['style']['buybtncolor'];?>">砍</span>
                                    <?php  } else if(!empty($diyitem['params']['goodstype'])) { ?>
                                        <span class="buy buybtn-2" style="background-color: <?php  echo $diyitem['style']['buybtncolor'];?>;"><?php  if(!empty($goodsitem['ctype'])) { ?>抽奖<?php  } else { ?>兑换<?php  } ?></span>
                                    <?php  } ?>
                                </div>
                            <?php  } ?>
                        </div>
                    <?php  } ?>
                    <?php  if($goodsitem['total']<=0 && empty($diyitem['params']['goodstype'])) { ?>
                        <?php  if($diyitem['params']['saleout']>-1) { ?>
                            <?php  if($diyitem['params']['saleout']==0) { ?>
                            <div class="salez" style="background-image: url('<?php  echo tomedia($_W['shopset']['shop']['saleout'])?>'); "></div>
                            <?php  } ?>
                            <?php  if($diyitem['params']['saleout']==1) { ?>
                                <div class="salez diy" style="background-image: url('../addons/ewei_shopv2/plugin/diypage/static/images/default/saleout-<?php  echo $diyitem['style']['saleoutstyle'];?>.png');"></div>
                            <?php  } ?>
                        <?php  } ?>
                    <?php  } ?>
                </a>
            <?php  } } ?>
        </div>
    <?php  } else { ?>
        <div class="swiper swiper-<?php  echo $diyitemid;?>" data-element=".swiper-<?php  echo $diyitemid;?>" data-view="<?php  if($diyitem['style']['liststyle']=='block three') { ?>3<?php  } else if($diyitem['style']['liststyle']=='block one') { ?>1<?php  } else { ?>2<?php  } ?>" data-free="true" data-btn="true">
            <div class="swiper-container fui-goods-group <?php  echo $diyitem['style']['liststyle'];?>" style="background: <?php  echo $diyitem['style']['background'];?>;">
                <div class="swiper-wrapper">
                    <?php  if(is_array($diyitem['data'])) { foreach($diyitem['data'] as $goodsitem) { ?>
                    <a class="swiper-slide fui-goods-item data-goodsid="<?php  echo $goodsitem['gid'];?>" href="<?php echo mobileUrl(empty($diyitem['params']['goodstype'])?'goods/detail':'creditshop/detail', array('id'=>$goodsitem['gid']))?>" data-nocache="true" style="position: relative;">
                        <div class="image   <?php  if($diyitem['params']['showicon']==1) { ?><?php  echo $diyitem['style']['iconstyle'];?><?php  } ?>" data-text="
                            <?php  if($diyitem['style']['goodsicon']=='recommand') { ?>推荐<?php  } ?>
                            <?php  if($diyitem['style']['goodsicon']=='hotsale') { ?>热销<?php  } ?>
                            <?php  if($diyitem['style']['goodsicon']=='isnew') { ?>新上<?php  } ?>
                            <?php  if($diyitem['style']['goodsicon']=='sendfree') { ?>包邮<?php  } ?>
                            <?php  if($diyitem['style']['goodsicon']=='istime') { ?>限时卖<?php  } ?>
                            <?php  if($diyitem['style']['goodsicon']=='bigsale') { ?>促销<?php  } ?>
                        " style="background-image: url(<?php  echo tomedia($goodsitem['thumb'])?>)">
                            <?php  if($diyitem['params']['showicon']==1 || $diyitem['params']['showicon']==2) { ?>
                                <div class="goodsicon <?php  echo $diyitem['params']['iconposition'];?>"
                                     <?php  if($diyitem['params']['iconposition']=='left top') { ?>
                                        style="top: <?php  echo $diyitem['style']['iconpaddingtop'];?>px; left: <?php  echo $diyitem['style']['iconpaddingleft'];?>px; text-align: left;"
                                     <?php  } else if($diyitem['params']['iconposition']=='right top') { ?>
                                        style="top: <?php  echo $diyitem['style']['iconpaddingtop'];?>px; right: <?php  echo $diyitem['style']['iconpaddingleft'];?>px; text-align: right;"
                                     <?php  } else if($diyitem['params']['iconposition']=='left bottom') { ?>
                                        style="bottom: <?php  echo $diyitem['style']['iconpaddingtop'];?>px; left: <?php  echo $diyitem['style']['iconpaddingleft'];?>px; text-align: left;"
                                     <?php  } else if($diyitem['params']['iconposition']=='left bottom') { ?>
                                        style="bottom: <?php  echo $diyitem['style']['iconpaddingtop'];?>px; right: <?php  echo $diyitem['style']['iconpaddingleft'];?>px; text-align: right;"
                                     <?php  } ?>
                                 >
                                    <?php  if($diyitem['params']['showicon']==1) { ?>
                                        <!--<img src="../addons/ewei_shopv2/plugin/diypage/static/images/default/goodsicon-<?php  echo $diyitem['style']['goodsicon'];?>.png" style="width: <?php  echo $diyitem['style']['iconzoom'];?>%;" />-->
                                    <?php  } else if($diyitem['params']['showicon']==2 && !empty($diyitem['params']['goodsiconsrc'])) { ?>
                                        <img src="<?php  echo tomedia($diyitem['params']['goodsiconsrc'])?>" onerror="this.src=''" style="width: <?php  echo $diyitem['style']['iconzoom'];?>%;" />
                                    <?php  } ?>
                                </div>
                            <?php  } ?>
                        </div>
                        <?php  if($diyitem['params']['showtitle']==1 || $diyitem['params']['showprice']==1) { ?>
                            <div class="detail">
                                <?php  if($diyitem['params']['showtitle']==1) { ?>
                                    <div class="name" style="color: <?php  echo $diyitem['style']['titlecolor'];?>; ">
                                        <?php  if($goodsitem['bargain']>0) { ?>
                                        <label style="background-color: <?php  echo $diyitem['style']['buybtncolor'];?>; padding: 0px 2px; color: #fff; font-size: 0.6rem">砍价</label>
                                        <?php  } ?>
                                        <?php  if(!empty($diyitem['params']['goodstype']) && $diyitem['params']['showtag']>0) { ?>
                                            <label style="background-color: <?php  echo $diyitem['style']['tagbackground'];?>; padding: 0px 2px; color: #fff; font-size: 0.6rem"><?php  if($diyitem['params']['showtag']==1&&$goodsitem['productprice']>0) { ?>&yen<?php  echo $goodsitem['productprice'];?><?php  } else if($diyitem['params']['showtag']==2) { ?><?php  if($goodsitem['gtype']==0) { ?>商品<?php  } else if($goodsitem['gtype']==1) { ?>优惠券<?php  } else if($goodsitem['gtype']==2) { ?>余额<?php  } else if($goodsitem['gtype']==3) { ?>红包<?php  } ?><?php  } else if($diyitem['params']['showtag']==3) { ?><?php  if($goodsitem['ctype']==1) { ?>抽奖<?php  } else { ?>兑换<?php  } ?><?php  } ?></label>
                                        <?php  } ?>
                                        <?php  echo $goodsitem['title'];?>
                                    </div>
                                <?php  } ?>
                                <?php  if($diyitem['params']['showprice']==1) { ?>
                                <?php  if((!empty($diyitem['params']['showproductprice']) && $goodsitem['productprice']>0 && $goodsitem['productprice']>$goodsitem['price']) || $diyitem['params']['showsales']==1) { ?>
                          
                                <p class="productprice <?php  if(empty($diyitem['params']['showproductprice']) && $diyitem['params']['showsales']!=1) { ?>noheight<?php  } ?>">
                                    <?php  if(!empty($diyitem['params']['showproductprice']) && $goodsitem['productprice']>0 && $goodsitem['productprice']>$goodsitem['price']) { ?>
                                    <span style="color: <?php  echo $diyitem['style']['productpricecolor'];?>;"><?php echo !empty($diyitem['params']['productpricetext'])?$diyitem['params']['productpricetext']:'原价'?>:<span <?php  if(!empty($diyitem['params']['productpriceline'])) { ?>style="text-decoration: line-through;"<?php  } ?>>&yen<?php  echo $goodsitem['productprice'];?></span></span>
                                    <?php  } ?>
                                    <?php  if($diyitem['params']['showsales']==1) { ?>
                                    <span style="color: <?php  echo $diyitem['style']['salescolor'];?>;"><?php echo !empty($diyitem['params']['salestext'])?$diyitem['params']['salestext']:'销量'?>: <?php  echo $goodsitem['sales'];?></span>
                                    <?php  } ?>
                                </p>
                                <?php  } else { ?>
                                 <p class="productprice">
                                      <span><?php  echo $goodsitem['subtitle'];?></span>
                                 </p>
                                <?php  } ?>
                                <?php  if(!empty($goodsitem['commission'])) { ?>
                                <!--<p class="productprice" style="height:1.2rem;line-height: 1.2rem;">-->
                                <!--    <span class="buy buybtn-1" style="background: #FCE0E0;color: #fe5455;border: #fe5455 1px solid;padding: 3px;border-radius: 3px;">邀请下单：￥<?php  echo $goodsitem['commission'];?></span>-->
                                <!--</p>-->
                                <?php  } else { ?>
                                <!--<p class="productprice" style="height:1.2rem;line-height: 1.2rem;">-->
                                <!--    <span class="buy buybtn-1" style="padding: 3px;border-radius: 3px;height:1.5rem;line-height: 1.5rem;"></span>-->
                                <!--</p>-->
                                <?php  } ?>
                                <div class="price">
                                        <span class="text" style="color: <?php  echo $diyitem['style']['pricecolor'];?>;">
                                            <?php  if(empty($diyitem['params']['goodstype'])) { ?>
                                                <p class="minprice">&yen<?php  echo $goodsitem['price'];?></p>
                                            <?php  } else { ?>
                                                <p>
                                                    <?php  if($goodsitem['price']==0&&$goodsitem['credit']==0) { ?>免费
                                                    <?php  } else if($goodsitem['price']>0&&$goodsitem['credit']==0) { ?><?php  echo $goodsitem['price'];?><small><?php  echo $textyuan;?></small>
                                                    <?php  } else if($goodsitem['price']==0&&$goodsitem['credit']>0) { ?><?php  echo $goodsitem['credit'];?><small><?php  echo $textjifen;?></small>
                                                    <?php  } else if($goodsitem['price']>0&&$goodsitem['credit']>0) { ?><?php  echo $goodsitem['credit'];?><small><?php  echo $textjifen;?></small>+<?php  echo $goodsitem['price'];?><small><?php  echo $textyuan;?></small><?php  } ?>
                                                </p>
                                            <?php  } ?>

                                        </span>
                                        <?php  if(!empty($diyitem['style']['buystyle']) && empty($goodsitem['bargain']) && empty($diyitem['params']['goodstype'])) { ?>
                                    <?php  if($diyitem['style']['buystyle']=='buybtn-1') { ?>
                                        <span class="buy buybtn-1" style="border-color: <?php  echo $diyitem['style']['buybtncolor'];?>;color:  <?php  echo $diyitem['style']['buybtncolor'];?>">购买</span>
                                        <?php  } else if($diyitem['style']['buystyle']=='buybtn-2') { ?>
                                        <span class="buy buybtn-2" style="background-color: <?php  echo $diyitem['style']['buybtncolor'];?>;">购买</span>
                                        <?php  } else if($diyitem['style']['buystyle']=='buybtn-3') { ?>
                                        <span class="buy buybtn-3" style="background-color: <?php  echo $diyitem['style']['buybtncolor'];?>;"><i class="icon icon-cartfill"></i></span>
                                        <?php  } else if($diyitem['style']['buystyle']=='buybtn-4') { ?>
                                        <span class="buy buybtn-4" style="border-color: <?php  echo $diyitem['style']['buybtncolor'];?>;"><i class="icon icon-cart" style="color: <?php  echo $diyitem['style']['buybtncolor'];?>"></i></span>
                                        <?php  } else if($diyitem['style']['buystyle']=='buybtn-5') { ?>
                                        <span class="buy buybtn-5" style="border-color: <?php  echo $diyitem['style']['buybtncolor'];?>;"><i class="icon icon-add" style="color:  <?php  echo $diyitem['style']['buybtncolor'];?>"></i></span>
                                        <?php  } else if($diyitem['style']['buystyle']=='buybtn-6') { ?>
                                        <span class="buy buybtn-6" style="background-color: <?php  echo $diyitem['style']['buybtncolor'];?>;"><i class="icon icon-add"></i></span>
                                        <?php  } ?>
                                        <?php  } else if(!empty($goodsitem['bargain'])) { ?>
                                        <span class="buy" style="background-color: <?php  echo $diyitem['style']['buybtncolor'];?>;">砍</span>
                                        <?php  } else if(!empty($diyitem['params']['goodstype'])) { ?>
                                        <span class="buy buybtn-3" style="background-color: <?php  echo $diyitem['style']['buybtncolor'];?>;"><?php  if(!empty($goodsitem['ctype'])) { ?>抽奖<?php  } else { ?>兑换<?php  } ?></span>
                                        <?php  } ?>
                                    </div>
                                <?php  } ?>
                            </div>
                        <?php  } ?>

                        <?php  if($goodsitem['total']<=0 && empty($diyitem['params']['goodstype'])) { ?>
                            <?php  if($diyitem['params']['saleout']>-1) { ?>
                                <?php  if($diyitem['params']['saleout']==0) { ?>
                                    <div class="salez" style="background-image: url('<?php  echo tomedia($_W['shopset']['shop']['saleout'])?>'); "></div>
                                <?php  } ?>
                                <?php  if($diyitem['params']['saleout']==1) { ?>
                                    <div class="salez diy" style="background-image: url('../addons/ewei_shopv2/plugin/diypage/static/images/default/saleout-<?php  echo $diyitem['style']['saleoutstyle'];?>.png');"></div>
                                <?php  } ?>
                            <?php  } ?>
                        <?php  } ?>
                    </a>
                    <?php  } } ?>
                </div>

                <div class="swiper-button-next swiper-button-white"></div>
                <div class="swiper-button-prev swiper-button-white"></div>
            </div>
        <script>
            var goodsGroup = $(".swiper-<?php  echo $diyitemid;?> .fui-goods-group");
            var swiperBtnTop= goodsGroup.find('.image').outerHeight() * 0.5;
            var swiperBtn = goodsGroup.find('.swiper-button-white');
            var swiperBtnMarginTop = swiperBtnTop - (swiperBtn.height() * 0.5)+10;
            swiperBtn.css({'top':0, 'margin-top': swiperBtnMarginTop})
        </script>
        </div>
    <?php  } ?>
<?php  } ?>

<!---yi fu yuan ma54mI5p2D5omA5pyJ-->