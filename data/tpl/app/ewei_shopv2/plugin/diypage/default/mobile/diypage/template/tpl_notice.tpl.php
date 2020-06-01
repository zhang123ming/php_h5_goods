<?php defined('IN_IA') or exit('Access Denied');?><?php  if(!empty($diyitem['data'])) { ?>
    <div class="fui-notice" style="height:1.9rem;vertical-align: middle;margin-bottom: 0rem; background: <?php  echo $diyitem['style']['background'];?>; border-color: <?php  echo $diyitem['style']['bordercolor'];?>;" data-speed="<?php  echo $diyitem['params']['speed'];?>">
        <?php  if(!empty($diyitem['params']['iconurl'])) { ?>
        <div class="image" style="height:1.5rem;"><img  src="<?php  echo tomedia($diyitem['params']['iconurl'])?>" onerror="this.src='../addons/ewei_shopv2/static/images/hotdot.jpg'"></div>
        <?php  } ?>
        <div class="icon" style="padding-top: 0.3rem;"><i class="icon icon-notification1" style="vertical-align: middle;font-size: 0.7rem; color: <?php  echo $diyitem['style']['iconcolor'];?>;"></i></div>
        <div class="text" style="padding-top: 0.3rem;height:1.5rem;text-align:left;font-weight:bold;color: <?php  echo $diyitem['style']['color'];?>;">
            <ul>
                <?php  if(is_array($diyitem['data'])) { foreach($diyitem['data'] as $noticeitem) { ?>
                    <?php  if($diyitem['params']['noticedata']==0) { ?>
                        <li><a href="<?php  if(!empty($noticeitem['linkurl'])) { ?><?php  echo $noticeitem['linkurl'];?><?php  } else { ?><?php  echo mobileUrl('shop/notice/detail', array('id'=>$noticeitem['id'], 'merchid'=>$page['merch']))?><?php  } ?>" style="color: <?php  echo $diyitem['style']['color'];?>;" data-nocache="true"><?php  echo $noticeitem['title'];?></a></li>
                    <?php  } else if($diyitem['params']['noticedata']==1) { ?>
                        <li><a href="<?php  echo $noticeitem['linkurl'];?>" style="color: <?php  echo $diyitem['style']['color'];?>;" data-nocache="true"><?php  echo $noticeitem['title'];?></a></li>
                    <?php  } ?>
                <?php  } } ?>
            </ul>
        </div>
    </div>
<?php  } ?>
<!---yi fu yuan ma54mI5p2D5omA5pyJ-->