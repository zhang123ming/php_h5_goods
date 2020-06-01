<?php defined('IN_IA') or exit('Access Denied');?><?php  if(!empty($diyitem['data'])) { ?>
    <div class="fui-cell-group fui-cell-click external" style="margin-top: <?php  echo $diyitem['style']['margintop'];?>px; background-color: <?php  echo $diyitem['style']['background'];?>;">
        <?php  if(is_array($diyitem['data'])) { foreach($diyitem['data'] as $listmenuitem) { ?>
            <a class="fui-cell" href="<?php  echo $listmenuitem['linkurl'];?>" data-nocache="true">
                <?php  if(!empty($listmenuitem['iconclass'])) { ?>
                    <div class="fui-cell-icon" style="color: <?php  echo $diyitem['style']['iconcolor'];?>;font-size: <?php  echo $diyitem['style']['fontsize'];?>px;"><i class="icon <?php  echo $listmenuitem['iconclass'];?>" style="font-size: <?php  echo $diyitem['style']['fontsize'];?>px;"></i></div>
                <?php  } ?>
                <div class="fui-cell-text" style="color: <?php  echo $diyitem['style']['textcolor'];?>;font-size: <?php  echo $diyitem['style']['fontsize'];?>px;margin-left:5px;"><?php  echo $listmenuitem['text'];?></div>
                <div class="fui-cell-remark" style="color: <?php  echo $diyitem['style']['remarkcolor'];?>;font-size: <?php  echo $diyitem['style']['fontsize'];?>px;">
                    <?php  if($listmenuitem['dotnum']>0) { ?>
                        <span class="badge"><?php  echo $listmenuitem['dotnum'];?></span>
                    <?php  } else { ?>
                        <?php  echo $listmenuitem['remark'];?>
                    <?php  } ?>
                </div>
            </a>
        <?php  } } ?>

    </div>
<?php  } ?>
<!--yifuyuanma-->