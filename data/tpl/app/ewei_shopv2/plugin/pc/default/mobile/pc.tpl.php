<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('pc/layout/_header', TEMPLATE_INCLUDEPATH)) : (include template('pc/layout/_header', TEMPLATE_INCLUDEPATH));?>

<link href="../addons/ewei_shopv2/plugin/pc/template/mobile/default/static/css/index.css" rel="stylesheet" type="text/css">

<script src="../addons/ewei_shopv2/plugin/pc/template/mobile/default/static/js/waypoints.js"></script>

<script type="text/javascript" src="../addons/ewei_shopv2/plugin/pc/template/mobile/default/static/js/home_index.js"

        charset="utf-8"></script>

<!--[if IE 6]>

<script type="text/javascript" src="../addons/ewei_shopv2/plugin/pc/template/mobile/default/static/js/ie6.js"

        charset="utf-8"></script>

<![endif]-->

<style type="text/css">

    .category {

        display: block !important;

    }

</style>

<div class="clear"></div>



<!-- HomeFocusLayout Begin-->

<div class="home-focus-layout fui-page">

    <ul id="fullScreenSlides" class="full-screen-slides">

        <?php  if(is_array($slides)) { foreach($slides as $slide) { ?>

            <li style="background: <?php echo empty($slide['backcolor'])?'#DDD':$slide['backcolor']?> url('<?php  echo tomedia($slide['thumb'])?>') no-repeat center top">

                <a href="<?php  echo $slide['link'];?>" target="_blank" title="<?php  echo $slide['advname'];?>">&nbsp;</a>

            </li>

        <?php  } } ?>

    </ul>

    <div class="jfocus-trigeminybox">

        <a class="limited_time" title="限时打折" href="#">

            <div class="clock-wrap">



            </div>

        </a>

        <div class="jfocus-trigeminy">

            <ul>

                <li>

                <?php  if(is_array($recommends)) { foreach($recommends as $recommend) { ?>

                    <a href="<?php  echo $recommend['link'];?>" target="_blank" title="<?php  echo $recommend['advname'];?>">

                        <img src="../addons/ewei_shopv2/plugin/pc/template/mobile/default/static/images/loading.gif"

                             rel="lazy" style="width: 250px;height: 164px"

                             data-url="<?php  echo tomedia($recommend['thumb'])?>" alt="<?php  echo $recommend['advname'];?>"></a>

                <?php  } } ?>

                </li>

            </ul>

        </div>

    </div>

    <script type="text/javascript">

        update_screen_focus();

    </script>



    <div class="right-sidebar">

        <div class="right-panel">

            <?php  if($user=check_login()) { ?>

            <div class="loginBox">

                <div class="exitPanel">

                    <img src=<?php  if(empty($user['avatar'])) { ?>"/addons/ewei_shopv2/plugin/pc/template/web/static/images/default_user_portrait.gif"<?php  } else { ?>

                        "<?php  echo $user['avatar'];?>"<?php  } ?> alt=""/>

                    <div class="message">

                        <p class="name">Hi,

                            <a href="<?php  echo mobileUrl('pc.member')?>"><?php  echo $user['nickname'];?></a>

                        </p>

                        <p class="logOut qiueExt">

                            [<a href="<?php  echo mobileUrl('pc.account.logout')?>">退出登录</a>]

                        </p>

                    </div>

                    <div class="clear"></div>

                </div>

                <!-- 买家信息 -->



                <div class="txtPanel">

                    <a href="<?php  echo mobileUrl('pc.order',array('status'=>-2)) ?>" class="line">

                        <p class="num"></p>

                        <p class="txt">待付款</p>

                    </a> <a target="_blank" href="<?php  echo mobileUrl('pc.order',array('status'=>2)) ?>"

                            class="line">

                        <p class="num"></p>

                        <p class="txt">待收货</p>

                    </a> <a target="_blank" href="<?php  echo mobileUrl('pc.order',array('status'=>3)) ?>">

                        <p class="num"></p>

                        <p class="txt">待评价</p>

                    </a>

                </div>



                <?php  } else { ?>

                <div class="loginBox">

                    <div class="welcomePanel"><img src="/addons/ewei_shopv2/plugin/pc/template/web/static/images/default_user_portrait.gif">

                        <p>Hi，欢迎光临，请登录</p>

                    </div>

                    <div class="loginPanel">

                        <a href="<?php  echo mobileUrl('pc.account.login')?>" rel="nofollow">

                            <span class="loginTxt">

                                <img alt="" src="../addons/ewei_shopv2/plugin/pc/template/mobile/default/static/images/u-me.png">登录</span>

                        </a>

                        <a href="<?php  echo mobileUrl('pc.account.register')?>" rel="nofollow">

                            <span class="reigsterTxt">

                                <img alt="" src="../addons/ewei_shopv2/plugin/pc/template/mobile/default/static/images/u-pencil.png">注册</span>

                        </a>

                    </div>

                </div>

                <?php  } ?>





                <div class="securePanel">

                    <li><img alt="买家保障"

                             src="../addons/ewei_shopv2/plugin/pc/template/mobile/default/static/images/u-promise.png">

                        <p>买家保障</p>

                    </li>

                    <li><img alt="商家认证"

                             src="../addons/ewei_shopv2/plugin/pc/template/mobile/default/static/images/u-quality.png">

                        <p>商家认证</p>

                    </li>

                    <li><img alt="安全交易"

                             src="../addons/ewei_shopv2/plugin/pc/template/mobile/default/static/images/u-safe.png">

                        <p>安全交易</p>

                    </li>

                </div>

                <div class="panelimg-side">

                    <ul>

                        <li><?php  show_adv('首页右侧广告',230,165)?></li>

                    </ul>

                </div>



                <div class="clear"></div>

            </div>

        </div>

    </div>

    <!--HomeFocusLayout End-->

</div>



<div class="home-sale-layout wrapper">

    <div class="left-layout"><div class="index-sale">

            <ul class="tabs-nav">

                <li class="tabs-selected"><h3>新品上架</h3></li>

                <li class=""><h3>热卖商品</h3></li>

                <li class=""><h3>疯狂抢购</h3></li>

                <li class=""><h3>猜您喜欢</h3></li>

                <li class=""><h3>热评商品</h3></li>

            </ul>

            <div class="tabs-panel sale-goods-list ">

                <ul>

                    <?php  if(is_array($new_list['list'])) { foreach($new_list['list'] as $g) { ?>

                    <li>

                        <dl>

                            <dt class="goods-name"><a target="_blank" href="<?php  echo mobileUrl('pc.goods.detail',array('id'=>$g['id']))?>" title="<?php  echo $g['title'];?>">

                                    <?php  echo $g['title'];?></a></dt>

                            <dd class="goods-thumb">

                                <a target="_blank" href="<?php  echo mobileUrl('pc.goods.detail',array('id'=>$g['id']))?>">

                                    <img src="<?php  echo tomedia($g['thumb'])?>" alt="<?php  echo $g['title'];?>" />

                                </a></dd>

                            <dd class="goods-price">商城价：<em>&yen;<?php  echo $g['marketprice'];?></em></dd>

                        </dl>

                    </li>

                    <?php  } } ?>



                </ul>

            </div>

            <div class="tabs-panel sale-goods-list tabs-hide">

                <ul>

                    <?php  if(is_array($hot_list['list'])) { foreach($hot_list['list'] as $g) { ?>

                        <li>

                            <dl>

                                <dt class="goods-name"><a target="_blank" href="<?php  echo mobileUrl('pc.goods.detail',array('id'=>$g['id']))?>" title="<?php  echo $g['title'];?>">

                                        <?php  echo $g['title'];?></a></dt>

                                <dd class="goods-thumb">

                                    <a target="_blank" href="<?php  echo mobileUrl('pc.goods.detail',array('id'=>$g['id']))?>">

                                        <img src="<?php  echo tomedia($g['thumb'])?>" alt="<?php  echo $g['title'];?>" />

                                    </a></dd>

                                <dd class="goods-price">商城价：<em>&yen;<?php  echo $g['marketprice'];?></em></dd>

                            </dl>

                        </li>

                    <?php  } } ?>



                </ul>

            </div>

            <div class="tabs-panel sale-goods-list tabs-hide">

                <ul>

                    <?php  if(is_array($time_list['list'])) { foreach($time_list['list'] as $g) { ?>

                        <li>

                            <dl>

                                <dt class="goods-name"><a target="_blank" href="<?php  echo mobileUrl('pc.goods.detail',array('id'=>$g['id']))?>" title="<?php  echo $g['title'];?>">

                                        <?php  echo $g['title'];?></a></dt>

                                <dd class="goods-thumb">

                                    <a target="_blank" href="<?php  echo mobileUrl('pc.goods.detail',array('id'=>$g['id']))?>">

                                        <img src="<?php  echo tomedia($g['thumb'])?>" alt="<?php  echo $g['title'];?>" />

                                    </a></dd>

                                <dd class="goods-price">商城价：<em>&yen;<?php  echo $g['marketprice'];?></em></dd>

                            </dl>

                        </li>

                    <?php  } } ?>



                </ul>

            </div>

            <div class="tabs-panel sale-goods-list tabs-hide">

                <ul>

                    <?php  if(is_array($discount_list['list'])) { foreach($discount_list['list'] as $g) { ?>

                        <li>

                            <dl>

                                <dt class="goods-name"><a target="_blank" href="<?php  echo mobileUrl('pc.goods.detail',array('id'=>$g['id']))?>" title="<?php  echo $g['title'];?>">

                                        <?php  echo $g['title'];?></a></dt>

                                <dd class="goods-thumb">

                                    <a target="_blank" href="<?php  echo mobileUrl('pc.goods.detail',array('id'=>$g['id']))?>">

                                        <img src="<?php  echo tomedia($g['thumb'])?>" alt="<?php  echo $g['title'];?>" />

                                    </a></dd>

                                <dd class="goods-price">商城价：<em>&yen;<?php  echo $g['marketprice'];?></em></dd>

                            </dl>

                        </li>

                    <?php  } } ?>



                </ul>

            </div>

            <div class="tabs-panel sale-goods-list tabs-hide">

                <ul>

                    <?php  if(is_array($recommand_list['list'])) { foreach($recommand_list['list'] as $g) { ?>

                        <li>

                            <dl>

                                <dt class="goods-name"><a target="_blank" href="<?php  echo mobileUrl('pc.goods.detail',array('id'=>$g['id']))?>" title="<?php  echo $g['title'];?>">

                                        <?php  echo $g['title'];?></a></dt>

                                <dd class="goods-thumb">

                                    <a target="_blank" href="<?php  echo mobileUrl('pc.goods.detail',array('id'=>$g['id']))?>">

                                        <img src="<?php  echo tomedia($g['thumb'])?>" alt="<?php  echo $g['title'];?>" />

                                    </a></dd>

                                <dd class="goods-price">商城价：<em>&yen;<?php  echo $g['marketprice'];?></em></dd>

                            </dl>

                        </li>

                    <?php  } } ?>



                </ul>

            </div>

        </div>

    </div>

    <div class="right-sidebar">

        <div class="title">

            <h3>商城公告</h3>

        </div>

        <div class="news">

            <ul>

                <?php  if(is_array($notice_list)) { foreach($notice_list as $notice) { ?>

                <li><a target="_blank" href="<?php  echo mobileUrl('pc.shop.notice.detail',array('mk'=>'detail','id'=>$notice['id'])) ?>" title="<?php  echo $notice['title'];?>"><?php  echo truncateCn($notice['title'],11)?></a>

                    <time style="float:right;margin-right: 10px">(<?php  echo date('m-d',$notice['createtime'])?>)</time>

                </li>

                <?php  } } ?>

            </ul>

        </div>

        <div class="ntrance">

            <ul>

                <li><a rel="nofollow" href="#" target="_self"><i class="i_ico01"></i>推广返利</a></li>

                <li><a rel="nofollow" href="#" target="_blank"><i class="i_ico02"></i>7大服务</a></li>

                <li><a rel="nofollow" href="#" target="_blank"><i class="i_ico03"></i>导购流程</a></li>

                <li><a rel="nofollow" href="#" target="_self"><i class="i_ico04"></i>物流自提</a></li>

                <li><a rel="nofollow" href="#" target="_self"><i class="i_ico05"></i>招商入驻</a></li>

                <li><a rel="nofollow" href="#" target="_self"><i class="i_ico06"></i>商家管理</a></li>



            </ul>

        </div>

    </div>

</div>



<div class="wrapper">

    <div class="mt10">

        <div class="mt10">

            <?php  show_adv('首页中部广告','1200','90','_blank')?>

        </div>

    </div>

</div>





<!--StandardLayout Begin-->

<?php  $i=1?>

<?php  if(is_array($categories['parent'])) { foreach($categories['parent'] as $parent) { ?>

    <div class="home-standard-layout wrapper <?php  echo $this->style($i)?>">

        <div class="left-sidebar">

            <div class="title">

                <div class="txt-type">

                    <span><?php  echo $i++ ?>F</span>

                    <h2 title="<?php  echo $parent['name'];?>"><?php  echo $parent['name'];?></h2>

                </div>

            </div>

            <div class="recommend-classes">

                <ul>

                    <?php  if(is_array($categories['children'][$parent['id']])) { foreach($categories['children'][$parent['id']] as $children) { ?>

                        <li><a href="#" title="<?php  echo $children['name'];?>" target="_blank"><?php  echo $children['name'];?></a></li>

                    <?php  } } ?>

                </ul>

            </div>

            <div class="left-ads">

                <a href="#" title="" target="_blank">

                    <img src="../addons/ewei_shopv2/plugin/pc/template/mobile/default/static/images/loading.gif" alt=""

                         data-url="<?php  echo tomedia($parent['advimg'])?>"

                         rel="lazy"/>

                </a>

            </div>

            <div class="left-hot-goods">

                <p>热卖促销：</p>

                <ul>

                    <?php  $goodslist = $this->goods_list(array('cate' => $parent['id'], 'pagesize' => 2, 'ishot' => 1))?>

                    <?php  if(is_array($goodslist['list'])) { foreach($goodslist['list'] as $g) { ?>

                        <li><i></i><a target="_blank" title="<?php  echo $g['title'];?>"

                                      href="<?php  echo mobileUrl('pc.goods.detail',array('id'=>$g['id']))?>"><?php  echo $g['title'];?></a>

                        </li>

                    <?php  } } ?>

                </ul>

            </div>

        </div>

        <div class="middle-layout">

            <ul class="right-brand">

                <li>

                    <a href="#" title="" target="_blank">更多</a>

                </li>

            </ul>

            <ul class="tabs-nav">



                <li class="tabs-selected"><i class="arrow"></i>

                    <h3>商品推荐</h3></li>

            </ul>

            <div class="right-side-layout">

                <div class="right-side-focus">



                    <ul>

                        <?php  $goodslist = $this->goods_list(array('cate' => $parent['id'], 'pagesize' => 3, 'isrecommand' => 1))?>

                        <?php  if(is_array($goodslist['list'])) { foreach($goodslist['list'] as $g) { ?>

                            <li><a href="<?php  echo mobileUrl('pc.goods.detail',array('id'=>$g['id']))?>"

                                   title="<?php  echo $g['title'];?>" target="_blank">

                                    <img src="../addons/ewei_shopv2/plugin/pc/template/mobile/default/static/images/loading.gif"

                                         alt="<?php  echo $g['title'];?>"

                                         data-url="<?php  echo $g['thumb'];?>"

                                         rel="lazy"/>

                                </a>

                            </li>

                        <?php  } } ?>



                    </ul>



                </div>



            </div>

            <div class="tabs-panel middle-goods-list">

                <ul>

                    <?php  $goodslist = $this->goods_list(array('cate' => $parent['id'], 'pagesize' => 6));?>

                    <?php  if(is_array($goodslist['list'])) { foreach($goodslist['list'] as $g) { ?>

                        <li>

                            <dl>

                                <dt class="goods-name">

                                    <a target="_blank" href="<?php  echo mobileUrl('pc.goods.detail',array('id'=>$g['id']))?>"

                                       title="<?php  echo $g['title'];?>" target="_blank"><?php  echo $g['title'];?></a></dt>

                                <dd class="goods-thumb">

                                    <a target="_blank" href="<?php  echo mobileUrl('pc.goods.detail',array('id'=>$g['id']))?>">

                                        <img src="<?php  echo $g['thumb'];?>" alt="<?php  echo $g['title'];?>" data-url="<?php  echo $g['thumb'];?>" rel="lazy" style="display: inline;">

                                    </a>

                                </dd>

                                <dd class="goods-price"><em>￥<?php  echo $g['marketprice'];?></em>

                                </dd>

                            </dl>

                        </li>

                    <?php  } } ?>

                </ul>

            </div>





        </div>

    </div>

<?php  } } ?>

<!--StandardLayout End-->



<div class="wrapper mt10">

    <?php  show_adv('首页底部广告','1200','90','_blank')?>

</div>

<div class="index-link wrapper">

    <dl class="website">

        <dt>合作伙伴 | 友情链接<b></b></dt>

        <dd>

            <?php  $links = get_links()?>

            <?php  if(is_array($links)) { foreach($links as $link) { ?>

            <a href="<?php  echo $link['url'];?>" target="_blank" title="<?php  echo $link['linkname'];?>"><?php  echo $link['linkname'];?></a>

            <?php  } } ?>

        </dd>

    </dl>

</div>

<div class="footer-line"></div>

<!--首页底部保障开始-->

<div id="cti">

    <div class="wrapper">

        <ul>

            <li><span class="line"></span> <span class="icon"> <img style="width: 60px;" src="../addons/ewei_shopv2/plugin/pc/template/mobile/default/static/images/shop/7day_60.gif"> </span> <span class="name"> 7天退货 </span> </li>

            <li><span class="line"></span> <span class="icon"> <img style="width: 60px;" src="../addons/ewei_shopv2/plugin/pc/template/mobile/default/static/images/shop/pz_60.gif"> </span> <span class="name"> 品质承诺 </span> </li>

            <li><span class="line"></span> <span class="icon"> <img style="width: 60px;" src="../addons/ewei_shopv2/plugin/pc/template/mobile/default/static/images/shop/psbf_60.gif"> </span> <span class="name"> 破损补寄 </span> </li>

            <li><span class="line"></span> <span class="icon"> <img style="width: 60px;" src="../addons/ewei_shopv2/plugin/pc/template/mobile/default/static/images/shop/jswl_60.gif"> </span> <span class="name"> 急速物流 </span> </li>

        </ul>

    </div>

</div>

<!--首页底部保障结束-->

<!--StandardLayout Begin-->

<div id="nav_box">

    <ul>

        <?php  $i = 1?>

        <?php  if(is_array($categories['parent'])) { foreach($categories['parent'] as $parent) { ?>

        <li class="nav_h_<?php  echo $i;?>"><a href="javascript:;" class="num"><?php  echo $i++?>F</a>

            <a href="javascript:;" class="word"><?php  echo $parent['name'];?></a>

        </li>

        <?php  } } ?>

    </ul>

</div>

<!--StandardLayout End-->

<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('pc/layout/_footer', TEMPLATE_INCLUDEPATH)) : (include template('pc/layout/_footer', TEMPLATE_INCLUDEPATH));?>

