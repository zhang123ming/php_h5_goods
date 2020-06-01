<?php defined('IN_IA') or exit('Access Denied');?><!doctype html>
<html lang="zh">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title><?php  if(empty($this->merch_user)) { ?><?php  echo $_W['shopset']['shop']['name'];?><?php  } else { ?><?php  echo $this->merch_user['merchname']?><?php  } ?></title>
    <meta name="renderer" content="webkit|ie-comp|ie-stand">
    <style type="text/css">
        body {
            _behavior: url('../addons/ewei_shopv2/plugin/pc/template/mobile/default/static/css/csshover.htc');
        }
    </style>
    <link rel="shortcut icon" href="<?php  echo tomedia($_W['shopset']['shop']['logo'])?>"/>
    <link href="../addons/ewei_shopv2/plugin/pc/template/mobile/default/static/css/base.css" rel="stylesheet" type="text/css">
    <link href="../addons/ewei_shopv2/plugin/pc/template/mobile/default/static/css/foxui.css" rel="stylesheet" type="text/css">
    <link href="../addons/ewei_shopv2/plugin/pc/template/mobile/default/static/css/style.css" rel="stylesheet" type="text/css">
    <link href="../addons/ewei_shopv2/plugin/pc/template/mobile/default/static/css/home_header.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" type="text/css" href="../addons/ewei_shopv2/static/fonts/iconfont.css">
  <script>(function() {var _53code = document.createElement("script");_53code.src = "https://tb.53kf.com/code/code/a3a434cdd4bfec1afab99ca1827ef5833/1";var s = document.getElementsByTagName("script")[0]; s.parentNode.insertBefore(_53code, s);})();</script>
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="../addons/ewei_shopv2/plugin/pc/template/mobile/default/static/js/html5shiv.js"></script>
    <script src="../addons/ewei_shopv2/plugin/pc/template/mobile/default/static/js/respond.min.js"></script>
    <![endif]-->
    <!--[if IE 6]>
    <script src="../addons/ewei_shopv2/plugin/pc/template/mobile/default/static/js/IE6_PNG.js"></script>
    <script>
        DD_belatedPNG.fix('.pngFix');
    </script>
    <script>
        // <![CDATA[
        if((window.navigator.appName.toUpperCase().indexOf("MICROSOFT")>=0)&&(document.execCommand))
        try{
        document.execCommand("BackgroundImageCache", false, true);
           }
        catch(e){}
        // ]]>
        </script>
    <![endif]-->
    <script>
        var COOKIE_PRE = 'COOKIE_PRE';
        var _CHARSET = 'utf-8';
        var SITEURL = '/app/index.php?i=1&c=entry&m=ewei_shopv2&do=mobile&r=pc';
        var SHOP_SITE_URL = '/app/index.php?i=1&c=entry&m=ewei_shopv2&do=mobile&r=pc';
        var SHOP_TEMPLATES_URL = '../addons/ewei_shopv2/plugin/pc/template/mobile/default/static';
    </script>
    <script src="../addons/ewei_shopv2/plugin/pc/template/mobile/default/static/js/jquery.js"></script>
    <script src="../addons/ewei_shopv2/plugin/pc/template/mobile/default/static/js/common.js" charset="utf-8"></script>
    <script src="../addons/ewei_shopv2/plugin/pc/template/mobile/default/static/js/jquery-ui/jquery.ui.js"></script>
    <script src="../addons/ewei_shopv2/plugin/pc/template/mobile/default/static/js/jquery.validation.min.js"></script>
    <script src="../addons/ewei_shopv2/plugin/pc/template/mobile/default/static/js/jquery.masonry.js"></script>
    <script src="../addons/ewei_shopv2/plugin/pc/template/mobile/default/static/js/dialog/dialog.js" id="dialog_js"
            charset="utf-8"></script>
    <script type="text/javascript">
        var PRICE_FORMAT = '&yen;%s';
        $(function () {
            //首页左侧分类菜单
            $(".category ul.menu").find("li").each(
                    function () {
                        $(this).hover(
                                function () {
                                    var cat_id = $(this).attr("cat_id");
                                    var menu = $(this).find("div[cat_menu_id='" + cat_id + "']");
                                    menu.show();
                                    $(this).addClass("hover");
                                    var menu_height = menu.height();
                                    if (menu_height < 60) menu.height(80);
                                    menu_height = menu.height();
                                    var li_top = $(this).position().top;
                                    $(menu).css("top", -li_top + 38);
                                },
                                function () {
                                    $(this).removeClass("hover");
                                    var cat_id = $(this).attr("cat_id");
                                    $(this).find("div[cat_menu_id='" + cat_id + "']").hide();
                                }
                        );
                    }
            );
            $(".head-user-menu dl").hover(function () {
                        $(this).addClass("hover");
                    },
                    function () {
                        $(this).removeClass("hover");
                    });
            $('#button').click(function () {
                if ($('#keyword').val() == '') {
                    return false;
                }
            });
        });
    </script>
    <script src="<?php  echo EWEI_SHOPV2_LOCAL?>static/js/require.js"></script>
    <script src="<?php  echo EWEI_SHOPV2_LOCAL?>static/js/myconfig-app.js"></script>
    <script language="javascript">require(['core'], function (modal){modal.init({siteUrl: "<?php  echo $_W['siteroot'];?>",baseUrl: "<?php  echo mobileUrl('ROUTES')?>"})});</script>
    <style>
        a {
            text-decoration: none;
        }
    </style>
</head>
<body>
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('pc/layout/top', TEMPLATE_INCLUDEPATH)) : (include template('pc/layout/top', TEMPLATE_INCLUDEPATH));?>
<!-- PublicHeadLayout Begin -->
<!-- 顶部广告展开效果-->
<div class="wrapper" id="top-banner">
    <a href="javascript:void(0);" title="关闭"></a>
    <?php  show_adv('商城顶部广告',1200,90,'_blank')?>
</div>
<!-- 顶部广告展开效果-->
<div class="header-wrap">
    <header class="public-head-layout wrapper">
        <h1 class="site-logo">
            <a href="<?php  echo mobileUrl('pc')?>">
                <img src="<?php  echo tomedia($_W['shopset']['shop']['pc_logo'])?>" class="pngFix">
            </a>
        </h1>
        <div class="site-ad">
            <?php  show_adv('顶部logo边广告',100,70)?>
        </div>
        <div class="head-search-bar">
            <div id="search">
                <ul class="tab">
                    <li act="search" class="current"><span>商品</span><i class="arrow"></i></li>
                </ul>
            </div>
            <form class="search-form" method="get" action="index.php">
                <input type="hidden" name="i" value="<?php  echo $_GPC['i']?>">
                <input type="hidden" name="c" value="entry">
                <input type="hidden" name="m" value="ewei_shopv2">
                <input type="hidden" name="do" value="mobile">
                <input type="hidden" name="r" value="pc.goods">
                <input type="hidden" value="store_list" id="search_act" name="act">
                <input placeholder="请输入您要搜索的店铺关键字" name="keywords" id="keyword" type="text" class="input-text" value="<?php  echo $_GPC['keywords']?>" maxlength="60" x-webkit-speech="" lang="zh-CN" onwebkitspeechchange="foo()" x-webkit-grammar="builtin:search">
                <input type="submit" id="button" value="搜索" class="input-submit">
            </form>
            <!--搜索关键字-->
            <?php  $keywords = $_W['shopset']['shop']['keywords']?>
            <?php  $keywords = explode(',',$keywords)?>
            <div class="keyword">热门搜索：
                <ul>
                    <?php  if(is_array($keywords)) { foreach($keywords as $keyword) { ?>
                    <li><a href="<?php  echo mobileUrl('pc.goods',array('keywords'=>$keyword))?>"><?php  echo $keyword;?></a></li>
                    <?php  } } ?>
                </ul>
            </div>

        </div>

        <div class="head-user-menu">
            <a href="<?php  echo mobileUrl('pc.order')?>">
            <dl class="my-mall">
                <dt><span class="ico"></span>我的商城<i class="arrow"></i></dt>
            </dl>
            </a>
            <a href="<?php  echo mobileUrl('pc.member.cart')?>">
            <dl class="my-cart">
                <dt><span class="ico"></span>购物车结算<i class="arrow"></i></dt>
            </dl>
            </a>
        </div>
    </header>
</div>
<!-- PublicHeadLayout End -->
<!-- publicNavLayout Begin -->
<nav class="public-nav-layout">
    <div class="wrapper">
        <div class="all-category">
            <div class="title"><i></i>
                <h3><a href="<?php  echo mobileUrl('pc.goods')?>">所有分类</a></h3>
            </div>
            <?php  $cate_index=index_cate();?>
            <?php  if(!empty($cate_index)) { ?>
            <div class="category">
                <ul class="menu">
                    <?php  if(is_array($cate_index['parent']['0'])) { foreach($cate_index['parent']['0'] as $cate_p) { ?>
                    <li cat_id="<?php  echo $cate_p['id'];?>" class="odd">
                        <div class="class">
                            <span class="arrow"></span>
                            <h4><a href="<?php  echo mobileUrl('pc.goods',array('cate'=>$cate_p['id']))?>"><?php  echo $cate_p['name'];?></a></h4>
                        </div>
                        <?php  if(!empty($cate_index['children'][$cate_p['id']])) { ?>
                        <div class="sub-class" cat_menu_id="<?php  echo $cate_p['id'];?>" style="display: none; top: 0px;">
                            <div class="sub-class-content">
                                <div class="recommend-class">
                                    <?php  if(is_array($cate_index['children'][$cate_p['id']])) { foreach($cate_index['children'][$cate_p['id']] as $cate_c) { ?>
                                        <?php  if(!empty($cate_c['isrecommand'])) { ?>
                                        <span><a href="<?php  echo mobileUrl('pc.goods',array('cate'=>$cate_c['id']))?>" title="<?php  echo $cate_c['name'];?>"><?php  echo $cate_c['name'];?></a></span>
                                        <?php  } ?>
                                    <?php  } } ?>
                                </div>
                                <?php  if(is_array($cate_index['children'][$cate_p['id']])) { foreach($cate_index['children'][$cate_p['id']] as $cate_c) { ?>
                                <dl>
                                    <dt <?php  if(empty($cate_index['grandchildren'][$cate_c['id']])) { ?>style="background: none"<?php  } ?>>
                                    <h3><a href="<?php  echo mobileUrl('pc.goods',array('cate'=>$cate_c['id']))?>"><?php  echo $cate_c['name'];?></a></h3>
                                    </dt>
                                    <dd class="goods-class" <?php  if(empty($cate_index['grandchildren'][$cate_c['id']])) { ?>style="background: none"<?php  } ?>>
                                        <?php  if(is_array($cate_index['grandchildren'][$cate_c['id']])) { foreach($cate_index['grandchildren'][$cate_c['id']] as $cate_g) { ?>
                                            <a href="<?php  echo mobileUrl('pc.goods',array('cate'=>$cate_g['id']))?>"><?php  echo $cate_g['name'];?></a>
                                        <?php  } } ?>
                                    </dd>
                                </dl>
                                <?php  } } ?>
                            </div>
                            <div class="sub-class-right">
                                <?php  $i=1?>
                                <?php  if(is_array($cate_index['children'][$cate_p['id']])) { foreach($cate_index['children'][$cate_p['id']] as $cate_c) { ?>
                                    <?php  if($i>3) break;?>
                                    <?php  if(!empty($cate_c['advimg'])) { ?>
                                        <?php  $i++ ?>
                                        <a target="_blank" href="<?php  echo $cate_c['advurl'];?>"><img src="<?php  echo tomedia($cate_c['advimg'])?>" rel="lazy" data-url="<?php  echo tomedia($cate_c['advimg'])?>" style="display: inline;width: 246px;height:123px"></a>
                                    <?php  } ?>
                                <?php  } } ?>
                            </div>
                        </div>
                        <?php  } ?>
                    </li>
                    <?php  } } ?>

                </ul>
            </div>
            <?php  } ?>
        </div>
        <ul class="site-menu">
            <?php  get_menus()?>
        </ul>
    </div>
</nav>
<div class="nch-breadcrumb-layout">
    <?php  if(!empty($nav_link_list)) { ?>
    <div class="nch-breadcrumb wrapper"><i class="icon-home"></i>
        <?php  if(is_array($nav_link_list)) { foreach($nav_link_list as $n) { ?>
            <?php  if(!empty($n['link'])) { ?>
                <span><a href="<?php  echo $n['link'];?>"><?php  echo $n['title'];?></a></span><span class="arrow">></span>
            <?php  } else { ?>
                <span><a href="javascript:void(0)"><?php  echo $n['title'];?></a></span>
            <?php  } ?>
        <?php  } } ?>
    </div>
    <?php  } ?>
</div>
<link href="../addons/ewei_shopv2/plugin/pc/template/mobile/default/static/css/member.css" rel="stylesheet" type="text/css">
