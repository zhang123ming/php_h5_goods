<?php defined('IN_IA') or exit('Access Denied');?><link href="../addons/ewei_shopv2/plugin/diypage/static/css/foxui.diy.css?v=201705261600"rel="stylesheet"type="text/css"/>
<style type="text/css">
    .diymenu .item .inner {background: <?php  echo $diymenu['style']['bgcolor'];?>;}
    .diymenu .item .inner:before,
    .diymenu .item .inner:after {border-color: <?php  echo $diymenu['style']['bordercolor'];?>;}
    .diymenu .item .inner .text {color: <?php  echo $diymenu['style']['textcolor'];?>;}
    .diymenu .item .inner .icon {color: <?php  echo $diymenu['style']['iconcolor'];?>;}
    .diymenu .item.on .inner {background: <?php  echo $diymenu['style']['bgcoloron'];?>;}
    .diymenu .item.on .inner .text {color: <?php  echo $diymenu['style']['textcoloron'];?>;}
    .diymenu .item.on .inner .icon {color: <?php  echo $diymenu['style']['iconcoloron'];?>;}
    .diymenu .item .child {border-color: <?php  echo $diymenu['style']['childbordercolor'];?>; background-color: <?php  echo $diymenu['style']['childbgcolor'];?>;}
    .diymenu .item .child a {color: <?php  echo $diymenu['style']['childtextcolor'];?>;}
    .diymenu .item .child a:after {border-color: <?php  echo $diymenu['style']['childbordercolor'];?>; color: <?php  echo $diymenu['style']['childtextcolor'];?>;}
    .diymenu .item .child .arrow:before {background: <?php  echo $diymenu['style']['childbordercolor'];?>;}
    .diymenu .item .child .arrow:after {background: <?php  echo $diymenu['style']['childbgcolor'];?>;}
    .diymenu .item .inner .badge {background: <?php  echo $diymenu['style']['cartbgcolor'];?>;}
</style>
<?php 
    function menuon($url){
        global $_GPC;
        if(strexists($url, 'http://') || strexists($url, 'https://') || empty($url)){
            return;
        }
        $r = trim($_GPC['r']);
        if($r=='diypage') {
            $id = strstr($url,"id=");
            $id = str_replace('id=', '', $id);
            if(intval($_GPC['id'])==$id) {
                return 'on';
            }
        } else {
            $r = strstr($url,"r=");
            $r = str_replace('r=', '', $r);
            if(trim($_GPC['r'])==$r){
                return 'on';
            }
        }
        return;
    }
?>
<div class="diymenu" style="<?php  if(!is_mobile()) { ?> position: absolute;max-width: 750px;<?php  } else { ?>position: fixed;<?php  } ?>">
    <?php  if(is_array($diymenu['data'])) { foreach($diymenu['data'] as $item) { ?>
        <div class="item item-col-<?php  echo count($diymenu['data'])?> <?php  if(count($item['child'])==0 && $diymenu['params']['navstyle']==0) { ?><?php  echo menuon($item['linkurl'])?><?php  } ?>" <?php  if(count($item['child'])>0 && $diymenu['params']['navstyle']==0) { ?>onclick="showSubMenu(this)"<?php  } else { ?>onclick="location.href = '<?php  echo $item['linkurl'];?>'"<?php  } ?> <?php  if(strstr($item['linkurl'], 'r=member.cart')) { ?>id="menucart"<?php  } ?>>
            <div class="inner <?php  if($diymenu['params']['navstyle']==1) { ?>image<?php  } ?> <?php  echo $diymenu['params']['navfloat'];?>">
                <?php  if(strstr($item['linkurl'], 'r=member.cart') && $cartcount>0) { ?>
                    <span class="badge"><?php  echo $cartcount;?></span>
                <?php  } ?>
                <?php  if($diymenu['params']['navstyle']==0) { ?>

                    <?php  if(!empty($item['iconclass'])) { ?>
                        <span class="icon <?php  echo $item['iconclass'];?> <?php  echo $diymenu['params']['navfloat'];?>"></span>
                    <?php  } ?>
                    <span class="text <?php  echo $diymenu['params']['navfloat'];?>" <?php  if(empty($item['iconclass'])) { ?> style="margin-top: 10px; font-size: 15px;"<?php  } ?>><?php  echo $item['text'];?></span>
                <?php  } else if($diymenu['params']['navstyle']==1) { ?>
                    <img src="<?php  echo tomedia($item['imgurl'])?>" />
                <?php  } ?>
            </div>
            <?php  if(count($item['child'])>0 && $diymenu['params']['navstyle']==0) { ?>
                <div class="child">
                    <?php  if(is_array($item['child'])) { foreach($item['child'] as $child) { ?>
                        <?php  if(!empty($child['text'])) { ?>
                            <a href="<?php  echo $child['linkurl'];?>" class="external"><?php  echo $child['text'];?></a>
                        <?php  } ?>
                    <?php  } } ?>
                    <span class="arrow"></span>
                </div>
            <?php  } ?>
        </div>
    <?php  } } ?>
</div>
<?php  if($diymenu['params']['navstyle']==0) { ?>
    <script>
        function showSubMenu(obj) {
            $(obj).toggleClass('on').siblings().removeClass('on');
            $(obj).find('.child').toggleClass('in');
            $(obj).siblings().find('.child').removeClass('in')
        }
        $(function () {
            var len = $(".diymenu .child").length;
            setTimeout(function () {
                $(".diymenu .child").each(function (i) {
                    var width = $(this).outerWidth();
                    var margin = -(width / 2);
                    var left = '50%';
                    var pleft = $(this).position().left - width / 2;
                    if(i==0 && pleft<2){
                        left = 2;
                        margin = 0;
                        var pwidth = $(this).closest('.item').width();
                        var arrowleft = pwidth / 2;
                        var oldleft = parseFloat( $(this).find('.arrow').css('left').replace('px','') );
                        $(this).find('.arrow').css({'left': arrowleft-10, 'margin-left': 0});
                    }
                    else if(i+1==len) {
                        var thisleft = parseFloat( $(this).offset().left.toString().replace('px',''));
                        if((thisleft+width)>$(document).width()){
                            var pleft = $(this).closest('.item').offset().left;
                            left =  $(document).width() - width - 2 - pleft;
                            margin = 0;
                            var pwidth = $(this).closest('.item').width();
                            var itemleft = (pwidth / 2) + pleft;
                            var childleft = $(document).width() - width - 2;
                            var arrowleft = itemleft - childleft;
                            $(this).find('.arrow').css({'left': arrowleft-10, 'margin-left': 0});
                        }
                    }
                    $(this).css({'position': 'absolute', 'left': left, 'margin-left': margin, 'z-index': 0});
                })
            }, 500);
        })
    </script>
<?php  } ?>
<!--yifuyuanma-->