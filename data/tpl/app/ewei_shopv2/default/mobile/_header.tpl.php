<?php defined('IN_IA') or exit('Access Denied');?><!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0 user-scalable=no">
        <meta name="format-detection" content="telephone=no" />
        <title><?php  if(empty($this->merch_user)) { ?><?php  echo $_W['shopset']['shop']['name'];?><?php  } else { ?><?php  echo $this->merch_user['merchname']?><?php  } ?></title>
        <link rel="stylesheet" type="text/css" href="../addons/ewei_shopv2/plugin/commission/static/css/style.css">
        <link rel="stylesheet" type="text/css" href="../addons/ewei_shopv2/static/js/dist/foxui/css/foxui.min.css?v=0.2">
        <link rel="stylesheet" type="text/css" href="../addons/ewei_shopv2/template/mobile/default/static/css/style.css?v=2.0.9">
        <?php  if(is_h5app()) { ?>
        <link rel="stylesheet" type="text/css" href="../addons/ewei_shopv2/template/mobile/default/static/css/h5app.css?v=2.0.3">
        <?php  } ?>

        <link rel="stylesheet" type="text/css" href="<?php  echo EWEI_SHOPV2_LOCAL?>static/fonts/iconfont.css?v=2017070719">
        <!--<link rel="stylesheet" href="//at.alicdn.com/t/font_82607_e93d131lklr6n7b9.css">-->
        <script src="./resource/js/lib/jquery-1.11.1.min.js"></script>
       <script src='//res.wx.qq.com/open/js/jweixin-1.2.0.js'></script>
        <script src="<?php  echo EWEI_SHOPV2_LOCAL?>static/js/app/clipboard.min.js"></script>
       <!--  <script src="<?php  echo EWEI_SHOPV2_LOCAL?>static/js/dist/foxui/js/foxui.min.js"></script> -->
        <script src="<?php  echo EWEI_SHOPV2_LOCAL?>static/js/require.js"></script>
        <script src="<?php  echo EWEI_SHOPV2_LOCAL?>static/js/myconfig-app.js"></script>
        <script language="javascript">require(['core'],function(modal){modal.init({siteUrl: "<?php  echo $_W['siteroot'];?>",baseUrl: "<?php  echo mobileUrl('ROUTES')?>"})});</script>
        <script type="text/javascript" src="https://api.map.baidu.com/api?v=2.0&ak=ZQiFErjQB7inrGpx27M1GR5w3TxZ64k7&s=1"></script>

        <?php  if(!empty($_W['shopset']['shop']['loading'])) { ?>
        <style>.fui-goods-group.block .fui-goods-item .image {background-image: url("<?php  echo tomedia($_W['shopset']['shop']['loading'])?>");}</style>
        <?php  } ?>

        <?php  if(!is_mobile() && !is_weixin() && !is_h5app()) { ?>
        <style type="text/css">
            body {
                position: absolute;;
                max-width: 750px;  margin:auto;
            }
            .fui-navbar {
                max-width:750px;
            }
            .fui-navbar,.fui-footer  {
                max-width:750px;
            }
            .fui-page.fui-page-from-center-to-left,
            .fui-page-group.fui-page-from-center-to-left,
            .fui-page.fui-page-from-center-to-right,
            .fui-page-group.fui-page-from-center-to-right,
            .fui-page.fui-page-from-right-to-center,
            .fui-page-group.fui-page-from-right-to-center,
            .fui-page.fui-page-from-left-to-center,
            .fui-page-group.fui-page-from-left-to-center {
                -webkit-animation: pageFromCenterToRight 0ms forwards;
                animation: pageFromCenterToRight 0ms forwards;
            }
        </style>
        <?php  } ?>

        <?php  if(is_h5app()) { ?>
            <style>
                .page-shop-goods_category .fui-header, .fui-page-group.statusbar .fui-statusbar, .fui-header, .page-goods-list .fui-header {background-color: <?php  echo $_W['shopset']['wap']['headerbgcolor'];?>;}
                .fui-header a.back:before {border-color: <?php  echo $_W['shopset']['wap']['headericoncolor'];?>;}
                .fui-header .title {color: <?php  echo $_W['shopset']['wap']['headercolor'];?>;}
                .fui-header a, .fui-header i {color: <?php  echo $_W['shopset']['wap']['headericoncolor'];?>;}
                <?php  if(is_ios()) { ?>.head-menu-mask, .fui-mask, .fui-mask-m, .order-list-page .fui-tab {top: 3.2rem;}.head-menu{top: 3.65rem}<?php  } ?>
            </style>
        <?php  } ?>

        <style>.danmu {display: none;opacity: 0;}</style>
    </head>

    <body ontouchstart>
		<div class='fui-page-group <?php  if(is_ios()) { ?>statusbar<?php  } ?>'>
            <?php  if($_W['account']['istest']==2) { ?>
            <?php  
                //获取测试时间提示
                $testtime = $_W['account']['testtime'];
                $time = ($testtime - time());//距离的时间(秒)
                $daytime = 24 * 60 * 60;
                $day = floor($time / $daytime); //天
                $hour = floor($time % $daytime / 3600); //小时
                $minute = round(($time % $daytime % 3600 ) / 60 );//分钟
                if($day > 0 || $hour > 0){
                    $msg = "测试系统将于<span style='color: red;font-size: 16px;'>".$day."天".$hour."小时</span>后关闭";
                }else if($day < 0 && $hour > 0){
                    $msg = "测试系统将于<span style='color: red;font-size: 16px;'>".$hour."小时</span>后关闭";
                }else{
                    $msg = "<div style='color: red;font-size: 16px;text-align: center;'>系统关闭，请联系管理员</div>";
                    exit($msg);
                }
            ?>
            <div style="position: fixed;top: 0;background: rgba(255,153,0,0.9);height: 40px;z-index: 1000;text-align: center;width: 100%;font-size: 12px;">
                <span> 当前系统为测试状态</span><br>
                <span > <?php  echo $msg;?></span>
               
            </div>
            <?php  } ?>

            <?php  if(is_h5app()) { ?>
            <div class="fui-statusbar"></div>
            <?php  } ?>
