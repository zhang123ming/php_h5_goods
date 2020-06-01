<?php defined('IN_IA') or exit('Access Denied');?><?php  if(!empty($diyitem['params']['content'])) { ?>
    <div class="diy-richtext" style="background: <?php  echo $diyitem['style']['background'];?>; padding: <?php  echo $diyitem['style']['padding'];?>px;">
        <?php  echo base64_decode($diyitem['params']['content'])?>
    </div>
<?php  } ?>
<!---yi fu yuan ma54mI5p2D5omA5pyJ-->