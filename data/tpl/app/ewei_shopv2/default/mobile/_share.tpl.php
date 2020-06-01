<?php defined('IN_IA') or exit('Access Denied');?><?php  $this->shopShare()?>
<?php  $routes=explode('.',$_GPC['r']);?>
<!--<?php-->
<!--  if(is_weixin()&&empty($_W['fans']['nickname'])){-->
<!--    $_SESSION['dest_url'] = urlencode($_W['siteurl']."&checkinfo=1");-->
<!--    $state = 'we7sid-' . $_W['session_id'];-->
<!--    $url = $_W['siteroot']. "app/index.php?i=".$_W['uniacid']."&c=auth&a=oauth&scope=userinfo";-->
<!--        $callback = urlencode($url);-->

<!--    $oauth_account = WeAccount::create($_W['account']['oauth']);-->
<!--    $forward = $oauth_account->getOauthUserInfoUrl($callback, $state);-->
<!--  }-->
<!--?>-->
<?php  if(is_weixin()&&empty($_W['fans']['nickname'])) { ?>
<!--<script type='text/javascript'>location.href='<?php  echo $forward;?>';</script>-->
<?php  } ?>
<script language="javascript">
    window.shareData = <?php  echo json_encode($_W['shopshare'])?>;
    setTimeout(function(){
        $.ajax({
            type: "GET",
            url:'<?php  echo mobileUrl('index.share_url',array(),true);?>',
            data:{url:location.href},
        dataType: "json",
            success: function(data){
            jssdkconfig = data.result || { jsApiList:[] };
           
            jssdkconfig.debug = false;
            jssdkconfig.jsApiList = ['checkJsApi','onMenuShareTimeline','onMenuShareAppMessage','onMenuShareQQ','onMenuShareWeibo','showOptionMenu', 'hideMenuItems', 'onMenuShareQZone', 'scanQRCode','openLocation'];
            wx.config(jssdkconfig);
            wx.ready(function () {
                <?php  if(!empty($routes['0'])&&in_array($routes['0'],$_W['share_close'])) { ?>
                 wx.hideOptionMenu();
                <?php  } else { ?>
                 wx.showOptionMenu();
                <?php  } ?>
                <?php  if(!empty($_W['shopshare']['hideMenus'])) { ?>
                    wx.hideMenuItems({
                        menuList: <?php  echo  json_encode($_W['shopshare']['hideMenus'])?>
                });
                <?php  } ?>

                    window.shareData.success = "<?php  echo $_W['shopshare']['way'];?>";
                    if(window.shareData.success){
                        var success = window.shareData.success;
                        window.shareData.success = function(){
                            eval(success)
                        };
                    }
                    wx.onMenuShareAppMessage(window.shareData);
                    wx.onMenuShareTimeline(window.shareData);
                    wx.onMenuShareQQ(window.shareData);
                    wx.onMenuShareWeibo(window.shareData);
                    wx.onMenuShareQZone(window.shareData);
                });
        },
        error:function(e){
            console.log(JSON.stringify(e));
        }
    });
    },500);

    <?php  if(!empty($_W['shopset']['wap']['open']) && !is_weixin()) { ?>
    //  Share to qq
    require(['//qzonestyle.gtimg.cn/qzone/qzact/common/share/share.js'], function(setShareInfo) {
        setShareInfo({
            title: "<?php  echo $_W['shopshare']['title'];?>",
            summary: "<?php  echo str_replace(array('\r','\n'),'',$_W['shopshare']['desc'])?>",
            pic: "<?php  echo $_W['shopshare']['imgUrl'];?>",
            url: "<?php  echo $_W['shopshare']['link'];?>"
        });
    });
    <?php  } ?>
</script> 