<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('pc/layout/_header', TEMPLATE_INCLUDEPATH)) : (include template('pc/layout/_header', TEMPLATE_INCLUDEPATH));?>

<link href="../addons/ewei_shopv2/plugin/pc/template/mobile/default/static/css/home_goods.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="../addons/ewei_shopv2/plugin/pc/template/mobile/default/static/js/cloudzoom.js"></script>
<style type="text/css">
    .ncs-goods-picture .levelB, .ncs-goods-picture .levelC {
        cursor: url("../addons/ewei_shopv2/plugin/pc/template/mobile/default/static/images/shop/zoom.cur"), pointer;
    }
    .ncs-goods-picture .levelD {
        cursor: url("../addons/ewei_shopv2/plugin/pc/template/mobile/default/static/images/shop/hand.cur"), move \9;
    }
    .nch-sidebar-all-viewed {
        display: block;
        height: 20px;
        text-align: center;
        padding: 9px 0;
    }
</style>

<div class="wrapper pr fui-page">
    <input type="hidden" id="lockcompare" value="unlock"/>
    <div class="ncs-detail ownshop">
        <!-- S 商品图片 -->
        <div id="ncs-goods-picture" class="ncs-goods-picture">
            <div class="gallery_wrap">
                <div class="gallery"><img title="鼠标滚轮向上或向下滚动，能放大或缩小图片哦~" src="<?php  echo $goods['thumb'];?>" class="cloudzoom"  data-cloudzoom="zoomImage: '<?php  echo $goods['thumb'];?>'"></div>
            </div>
            <div class="controller_wrap">
                <div class="controller">
                    <ul>
                        <?php  if(is_array($thumbs)) { foreach($thumbs as $thumb) { ?>
                            <li><img title="鼠标滚轮向上或向下滚动，能放大或缩小图片哦~" class='cloudzoom-gallery' src="<?php  echo tomedia($thumb)?>" data-cloudzoom="useZoom: '.cloudzoom', image: '<?php  echo tomedia($thumb)?>', zoomImage: '<?php  echo tomedia($thumb)?>' " width="60" height="60">
                            </li>
                        <?php  } } ?>
                    </ul>
                </div>
            </div>
        </div>
        <!-- S 商品基本信息 -->
        <div class="ncs-goods-summary">
            <div class="name">
                <h1><?php  echo $goods['title'];?></h1>
                <strong><?php  echo $goods['subtitle'];?></strong></div>
            <div class="ncs-meta">
                <!-- S 商品参考价格 -->
                <dl>
                    <dt>市&nbsp;场&nbsp;价：</dt>
                    <dd class="cost-price"><strong>&yen;<?php  echo $goods['productprice'];?></strong></dd>
                </dl>
                <!-- E 商品参考价格 -->
                <!-- S 商品发布价格 -->
                <dl>
                    <dt>商&nbsp;城&nbsp;价：</dt>
                    <dd class="price">
                        <strong>&yen;<?php  if($goods['minprice']==$goods['maxprice']) { ?><?php  echo $goods['minprice'];?><?php  } else { ?><?php  echo $goods['minprice'];?>~<?php  echo $goods['maxprice'];?><?php  } ?></strong>
                    </dd>
                </dl>
                <dl class="rate">
                    <dt>商品评分：</dt>
                    <!-- S 描述相符评分 -->
                    <dd><span class="raty" data-score="5"></span><a href="#ncGoodsRate">共有<?php  echo $commons['count']['all'];?>条评价</a></dd>
                    <!-- E 描述相符评分 -->
                </dl>
                <!-- E 商品发布价格 -->
                <div class="ncs-goods-code"></div>
            </div>
            <?php  if($goods['isdiscount'] && $goods['isdiscount_time']>=time()) { ?>
                <!-- S 促销 -->
                <div class="ncs-sale">
                    <dl>
                        <dt>促&#12288;&#12288;销：</dt>
                        <dd class="promotion-info">
                            <span class="sale-name"><?php echo empty($goods['isdiscount_title'])?'促销':$goods['isdiscount_title']?></span>
                            <!-- S 限时折扣 -->
                        <span class="sale-rule w400">直降<em>&yen;41.00</em>
                        最低2件起，挥泪大甩卖                        </span>
                            <!-- E 限时折扣  -->
                            <!-- S 团购-->
                            <!-- E 团购 -->
                            <!--S 满就送 -->
                            <!--E 满就送 -->
                        </dd>
                    </dl>
                    <!-- S 赠品 -->
                    <!-- E 赠品 -->
                </div>
                <!-- E 促销 -->
            <?php  } ?>
            <!--送达时间 begin  -->
            <!--送达时间 end  -->
            <div class="ncs-key" style="padding: 10px 30px">
                <?php  if($hasServices) { ?>
                    <?php  if($goods['cash']==2) { ?><i class='icon icon-roundcheck text-danger '></i>货到付款<?php  } ?>
                    <?php  if($goods['quality']) { ?><i class='icon icon-roundcheck text-danger '></i>正品保证<?php  } ?>
                    <?php  if($goods['repair']) { ?><i class='icon icon-roundcheck text-danger'></i>保修<?php  } ?>
                    <?php  if($goods['invoice']) { ?><i class='icon icon-roundcheck text-danger'></i>发票<?php  } ?>
                    <?php  if($goods['seven']) { ?><i class='icon icon-roundcheck text-danger'></i>7天退换<?php  } ?>
                <?php  } ?>
            </div>

            <div class="ncs-logistics"><!-- S 物流与运费新布局展示  -->
                <dl id="ncs-freight" class="ncs-freight">
                    <dd class="ncs-freight_box">
                        <div id="ncs-freight-selector" class="ncs-freight-select">
                            <div id="ncs-freight-prompt" style="padding-left: 120px"><strong><?php  if($goods['total'] <=0) { ?>无货<?php  } else { ?>有货<?php  } ?></strong>
                                <?php  if(is_array($goods['dispatchprice'])) { ?>
                                    <?php  if($goods['type']==1) { ?>
                                        <span>快递: <?php  echo number_format($goods['dispatchprice']['min'],2)?>
                                            ~ <?php  echo number_format($goods['dispatchprice']['max'],2)?></span>
                                    <?php  } ?>
                                <?php  } else { ?>
                                    <?php  if($goods['type']==1) { ?>
                                        <span>快递: <?php echo $goods['dispatchprice'] == 0 ? '包邮' : number_format($goods['dispatchprice'],2)?></span>
                                    <?php  } ?>
                                <?php  } ?>
                            </div>
                    </dd>
                </dl>
                <!-- S 物流与运费新布局展示  -->
            </div>
            <!-- S 购买数量及库存 -->
            <?php  if($goods['canbuy']) { ?>
                <div class="ncs-buy">
                    <div class="ncs-figure-input">
                        <input name="" type="tel" id="quantity" value="1" size="3" maxlength="6" class="input-text num">
                        <a href="javascript:void(0)" class="increase plus">&nbsp;</a>
                        <a  href="javascript:void(0)" class="decrease minus">&nbsp;</a>
                    </div>
                    <div class="ncs-point" style="display: none;"><i></i>
                        <!-- S 库存 -->
                        <span>您选择的商品库存<strong nctype="goods_stock">55</strong>件
                        <!-- E 库存 -->
                    </div>
                    <!-- S 提示已选规格及库存不足无法购买 -->
                    <!-- E 提示已选规格及库存不足无法购买 -->
                    <div class="ncs-btn">
                        <!-- v3-b10 限制购买-->
                        <?php  if($canAddCart) { ?>
                            <!-- 加入购物车-->
                            <a href="javascript:void(0);" nctype="addcart_submit" class="addcart cartbtn" title="添加购物车"><i class="icon-shopping-cart"></i>添加购物车</a>

                        <?php  } ?>
                        <!-- 立即购买-->
                        <a href="javascript:void(0);" nctype="buynow_submit" class="buynow " title="立即购买">立即购买</a>
                        <!-- v3-b10 end-->
                        <!-- S 加入购物车弹出提示框 -->
                        <div class="ncs-cart-popup">
                            <dl>
                                <dt>成功添加到购物车<a title="关闭" onClick="$('.ncs-cart-popup').css({'display':'none'});">X</a>
                                </dt>
                                <dd>购物车共有 <strong id="bold_num"></strong> 种商品 总金额为：<em id="bold_mly" class="saleP"></em>
                                </dd>
                                <dd class="btns">
                                    <a href="javascript:void(0);" class="ncs-btn-mini ncs-btn-green" onclick="location.href='/index.php?act=cart'">查看购物车</a>
                                    <a href="javascript:void(0);" class="ncs-btn-mini" value="" onclick="$('.ncs-cart-popup').css({'display':'none'});">继续购物</a>
                                </dd>
                            </dl>
                        </div>
                        <!-- E 加入购物车弹出提示框 -->
                    </div>
                    <!-- E 购买按钮 -->
                </div>
            <?php  } else { ?>
                <div class="fui-cell-group fui-cell-click">
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
                            <?php  } ?></div>
                    </div>
                </div>
            <?php  } ?>
            <!-- E 购买数量及库存 -->
            <!--E 商品信息 -->
        </div>
        <!-- E 商品图片及收藏分享 -->
        <div class="ncs-handle">
            <!-- S 收藏 -->
            <a class="nav-item favorite-item <?php  if($isFavorite) { ?>active<?php  } ?>"data-isfavorite="<?php  echo intval($isFavorite)?>">
                <span class="icon <?php  if($isFavorite) { ?>icon-likefill<?php  } else { ?>icon-like<?php  } ?>"></span>
                <span class="label" style="font-size: 16px;line-height: 16px;">关注</span>
            </a>
        </div>
        <!--S 店铺信息-->
        <div style="position: absolute; z-index: 2; top: -1px; right: -1px;">
            <!--店铺基本信息 S-->
            <div class="ncs-info">
                <div class="title">
                    <h4><?php  echo $shop['name'];?></h4>
                </div>
                <div class="content">
                    <dl class="messenger">
                        <dt>联系方式：</dt>
                        <dd><span member_id="1"></span>
                        </dd>
                    </dl>
                    <!--只有实名认证实体店认证后才显示保障体系 by 9yetech.com -->
                    <!--保证金金额-->
                    <div class="goto"><a href="<?php  echo $shop['uro'];?>&r=pc">进入商家店铺</a><a href="javascript:collect_store('1','count','store_collect')">收藏店铺<em nctype="store_collect">0</em></a></div>
                    <div class="shop-other" id="shop-other">
                        <ul>
                            <li class="ncs-info-btn-map"><a href="javascript:void(0)" class="pngFix"><span>店铺地图</span><b></b> <!-- 店铺地图 -->
                                    <div class="ncs-info-map" id="map_container" style="width:208px;height:208px;"></div>
                                </a></li>
                            <li class="ncs-info-btn-qrcode"><a href="javascript:void(0)" class="pngFix"><span>店铺二维码</span><b></b>
                                    <p class="ncs-info-qrcode"></p>
                                </a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!--S 看了又看 -->
            <div class="ncs-lal">
                <div class="title">看了又看</div>
                <div class="content">
                    <ul>
                        <?php  if(is_array($history)) { foreach($history as $_g) { ?>
                            <li>
                                <div class="goods-pic"><a title="<?php  echo $_g['title'];?>" href="<?php  echo mobileUrl('pc.goods.detail',array('id'=>$_g['goodsid']))?>">
                                        <img alt="" src="<?php  echo $_g['thumb'];?>" rel="lazy" data-url="<?php  echo $_g['thumb'];?>"/>
                                    </a></div>
                                <div class="goods-price">￥<?php  echo $_g['marketprice'];?></div>
                            </li>
                        <?php  } } ?>
                    </ul>
                </div>
            </div>
            <!--E 看了又看 -- >
           </div>
        <!--E 店铺信息 -->
        </div>
        <div class="clear"></div>
    </div>

    <div id="content" class="ncs-goods-layout expanded">
        <div class="ncs-goods-main" id="main-nav-holder">
            <div class="tabbar pngFix" id="main-nav">
                <div class="ncs-goods-title-nav">
                    <ul id="categorymenu">
                        <li class="current"><a id="tabGoodsIntro" href="#">商品详情</a></li>
                        <li><a id="tabGoodsRate" href="#">商品评价<em>(<?php  echo $commons['count']['all'];?>)</em></a></li>
                    </ul>
                    <div class="switch-bar"><a href="javascript:void(0)" id="fold">&nbsp;</a></div>
                </div>
            </div>
            <?php  if(count($params)>0) { ?>
            <?php  } ?>
            <div class="ncs-intro">
                <div class="content bd" id="ncGoodsIntro">
                    <ul class="nc-goods-sort">
                        <?php  if(!empty($params)) { ?>
                            <?php  if(is_array($params)) { foreach($params as $p) { ?>
                                <li><?php  echo $p['title'];?>：<?php  echo $p['value'];?></li>
                            <?php  } } ?>
                        <?php  } else { ?>
                            <div class="fui-cell-info text-align">商品没有参数</div>
                        <?php  } ?>
                    </ul>
                    <div class="ncs-goods-info-content">
                        <div class="default">
                            <?php  echo $goods['content'];?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="ncs-comment">
                <div class="ncs-goods-title-bar hd">
                    <h4><a href="javascript:void(0);">商品评价</a></h4>
                </div>
                <div class="ncs-goods-info-content bd" id="ncGoodsRate">
                    <div class="top">
                        <div class="rate">
                            <p><strong><?php  echo $commons['percent'];?></strong><sub>%</sub>好评</p>
                            <span>共有<?php  echo $commons['count']['all'];?>人参与评分</span></div>
                        <div class="percent">
                            <dl>
                                <dt>好评<em>(<?php  echo $commons['percent'];?>%)</em></dt>
                                <dd><i style="width: <?php  echo $commons['percent'];?>%"></i></dd>
                            </dl>
                            <dl>
                                <dt>中评<em>(<?php  echo intval($commons['count']['normal']/$commons['count']['all']* 100)?>%)</em></dt>
                                <dd>
                                    <i style="width: <?php  echo intval($commons['count']['normal']/$commons['count']['all']* 100)?>%"></i>
                                </dd>
                            </dl>
                            <dl>
                                <dt>差评<em>(<?php  echo intval($commons['count']['bad']/$commons['count']['all']* 100)?>%)</em></dt>
                                <dd>
                                    <i style="width: <?php  echo intval($commons['count']['bad']/$commons['count']['all']* 100)?>%"></i>
                                </dd>
                            </dl>
                        </div>
                        <div class="btns"><span>您可对已购商品进行评价</span>
                            <p><a href="<?php  echo mobileUrl('pc.order') ?>"
                                  class="ncs-btn ncs-btn-red" target="_blank"><i class="icon-comment-alt"></i>评价商品</a>
                            </p>
                        </div>
                    </div>

                    <div class="ncs-goods-title-nav">
                        <ul id="comment_tab">
                            <li data-type="all" class="current"><a href="javascript:void(0);">商品评价(<?php  echo $commons['count']['all'];?>)</a></li>
                            <li data-type="good"><a href="javascript:void(0);">好评(<?php  echo $commons['count']['good'];?>)</a></li>
                            <li data-type="normal"><a href="javascript:void(0);">中评(<?php  echo $commons['count']['normal'];?>)</a>
                            </li>
                            <li data-type="bad"><a href="javascript:void(0);">差评(<?php  echo $commons['count']['bad'];?>)</a></li>
                        </ul>
                    </div>
                    <!-- 商品评价内容部分 -->
                    <div id="goodscomments" class="ncs-commend-main"></div>
                </div>
            </div>
            <div class="ncg-salelog">
            </div>
            <div class="ncs-recommend">
            </div>
        </div>

        <div class="ncs-sidebar">
            <div class="ncs-message-bar">
                <div class="default">
                    <h5><a href="<?php  echo $shop['url'];?>&r=pc" title="<?php  echo $shop['name'];?>"><?php  echo $shop['name'];?></a></h5>
                    <span member_id="1"></span>
                </div>
            </div>

            <div class="ncs-sidebar-container ncs-top-bar">
                <div class="title">
                    <h4>商品排行</h4>
                </div>

                <div class="content">
                    <ul class="ncs-top-tab pngFix">
                        <li id="hot_sales_tab" class="current"><a href="#">热销商品排行</a></li>
                    </ul>
                    <div id="hot_sales_list" class="ncs-top-panel">
                        <ol>
                            <?php  if(is_array($hotList['list'])) { foreach($hotList['list'] as $_g) { ?>
                                <li>
                                    <dl>
                                        <dt>
                                            <a href="<?php  echo mobileUrl('pc.goods.detail',array('id'=>$_g['id'])) ?>"><?php  echo $_g['title'];?></a>
                                        </dt>
                                        <dd class="goods-pic">
                                            <a href="<?php  echo mobileUrl('pc.goods.detail',array('id'=>$_g['id'])) ?>">
                                            <span class="thumb size40"><i></i>
                                                <img src="<?php  echo $_g['thumb'];?>" onload="javascript:DrawImage(this,40,40);"></span>
                                            </a>
                                            <p>
                                            <span class="thumb size100"><i></i>
                                                <img src="<?php  echo $_g['thumb'];?>" onload="javascript:DrawImage(this,100,100);" title="<?php  echo $_g['title'];?>">
                                            </span>
                                            </p>
                                        </dd>
                                        <dd class="price pngFix"><?php  echo $_g['marketprice'];?></dd>
                                        <dd class="selled pngFix">售出：<strong><?php  echo $_g['sales'];?></strong>笔</dd>
                                    </dl>
                                </li>
                            <?php  } } ?>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('pc/goods/picker', TEMPLATE_INCLUDEPATH)) : (include template('pc/goods/picker', TEMPLATE_INCLUDEPATH));?>

<script type="text/javascript" src="../addons/ewei_shopv2/static/js/dist/jquery/jquery.qrcode.min.js"></script>
<script type="text/javascript">
    goodsid:"<?php  echo $goods['id'];?>";
    $(function () {
        $(".ncs-goods-code").html('')
        $(".ncs-goods-code").qrcode({
            typeNumber: 0,      //计算模式
            correctLevel: 0,//纠错等级
            width: 100,
            height: 100,
            text: "<?php  echo $_W['siteroot'].'app/'.mobileUrl('goods/detail', array('id'=>$goods['id']))?>"/*<?php  echo $_W['siteroot'].'app/'.mobileUrl('goods/detail', array('id'=>$goods['id']))?>*/
        });

        // 放大镜效果 产品图片
        CloudZoom.quickStart();

        // 图片切换效果
        $(".controller li").first().addClass('current');
        $('.controller').find('li').mouseover(function () {
            $(this).first().addClass("current").siblings().removeClass("current");
        });



        // 商品内容介绍Tab样式切换控制

        $('#categorymenu').find("li").click(function () {
            $('#categorymenu').find("li").removeClass('current');
            $(this).addClass('current');
        });

        // 商品详情默认情况下显示全部

        $('#tabGoodsIntro').click(function () {
            $('.bd').css('display', '');
            $('.hd').css('display', '');
        });

        // 点击地图隐藏其他以及其标题栏

        $('#tabStoreMap').click(function () {
            $('.bd').css('display', 'none');
            $('#ncStoreMap').css('display', '');
            $('.hd').css('display', 'none');
        });

        // 点击评价隐藏其他以及其标题栏
        $('#tabGoodsRate').click(function () {
            $('.bd').css('display', 'none');
            $('#ncGoodsRate').css('display', '');
            $('.hd').css('display', 'none');
        });
        // 点击成交隐藏其他以及其标题

        $('#tabGoodsTraded').click(function () {
            $('.bd').css('display', 'none');
            $('#ncGoodsTraded').css('display', '');
            $('.hd').css('display', 'none');
        });
        //评价列表

        $('#comment_tab').on('click', 'li', function () {
            $('#comment_tab li').removeClass('current');
            $(this).addClass('current');
            load_comments($(this).attr('data-type'));
        });
    });

    function load_comments(level) {
        var url = '<?php  echo mobileUrl('pc.goods.detail.get_comment_list', array('id' =>$goods['id'],'mid' => $_GPC['mid'],'page'=>1,'getcount'=>1))?>';
        url += '&level=' + level;
        $("#goodscomments").load(url, function () {
        });
    }
    load_comments('all');
</script>

<script language="javascript">
 require(['../addons/ewei_shopv2/static/js/app/biz/goods/detail.js'], function (modal) {
    modal.init({
        goodsid: "<?php  echo $goods['id'];?>"
    });
 });
</script>

<script language="javascript">
    require(['../addons/ewei_shopv2/plugin/pc/biz/goods/detail.js'], function (modal) {
        modal.init({
            goodsid: "<?php  echo $goods['id'];?>"
        });
        core.json('goods/picker', {
            id: modal.goodsid
        }, function(ret) {
            if (ret.status == 0) {
                FoxUI.toast.show('未找到商品!');
                return
            }
            if(ret.result.canbuy==0){
                FoxUI.toast.show('您暂无权限购买!');
                return
            }

            modal.goods = ret.result.goods;
            modal.specs = ret.result.specs;
            modal.options = ret.result.options;
            if (modal.goods.unit == '') {
                modal.goods.unit = '件'
            }
            $('.ncs-figure-input', $('.ncs-buy')).numbers({
                value: modal.total,
                max: modal.goods.maxbuy,
                min: modal.goods.minbuy,
                minToast: "{min}" + modal.goods.unit + "起售",
                maxToast: "最多购买{max}" + modal.goods.unit,
                callback: function(num) {
                    modal.total = num
                }
            });
        }, true, false);
    });

</script>

<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('pc/layout/_footer', TEMPLATE_INCLUDEPATH)) : (include template('pc/layout/_footer', TEMPLATE_INCLUDEPATH));?>

