<?php defined('IN_IA') or exit('Access Denied');?><?php  if(!empty($diyitem['data'])) { ?>
    <div class="fui-picture" style=" background: <?php  echo $diyitem['style']['background'];?>;">
        <?php  if(is_array($diyitem['data'])) { foreach($diyitem['data'] as $pictureitem) { ?>
        <a href="<?php  echo $pictureitem['linkurl'];?>" style="display: block; padding: <?php  echo $diyitem['style']['paddingtop'];?>px <?php  echo $diyitem['style']['paddingleft'];?>px;" data-nocache="true">
            <img data-lazy="<?php  echo tomedia($pictureitem['imgurl'])?>" />
        </a>
        <?php  } } ?>
    </div>
<?php  } ?>
<!--yifuyuanma-->