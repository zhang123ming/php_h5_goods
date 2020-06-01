<?php defined('IN_IA') or exit('Access Denied');?><?php  if(!empty($diyitem['data'])) { ?>
    <?php  if(empty($diyitem['params']['showtype']) || count($diyitem['data'])<=intval($diyitem['params']['rownum'])) { ?>
        <div class="fui-picturew row-<?php  echo $diyitem['params']['rownum'];?>" style="padding: <?php  echo $diyitem['style']['paddingtop'];?>px <?php  echo $diyitem['style']['paddingleft'];?>px; background: <?php  echo $diyitem['style']['background'];?>;">
            <?php  if(is_array($diyitem['data'])) { foreach($diyitem['data'] as $pictureitem) { ?>
                <a class="item" style="padding: <?php  echo $diyitem['style']['paddingtop'];?>px <?php  echo $diyitem['style']['paddingleft'];?>px;" href="<?php  echo $pictureitem['linkurl'];?>">
                    <div class="image">
                        <img src="<?php  echo tomedia($pictureitem['imgurl'])?>">
                        <?php  if(!empty($pictureitem['title'])) { ?>
                            <div class="title" style="color: <?php  echo $diyitem['style']['titlecolor'];?>; text-align: <?php  echo $diyitem['style']['titlealign'];?>;"><?php  echo $pictureitem['title'];?></div>
                        <?php  } ?>
                    </div>
                    <div  style="color: <?php  echo $diyitem['style']['textcolor'];?>; text-align: <?php  echo $diyitem['style']['textalign'];?>;"><?php  echo $pictureitem['text'];?></div>
                </a>
            <?php  } } ?>
        </div>
    <?php  } else { ?>
        <div class="swiper swiper-<?php  echo $diyitemid;?>" data-element=".swiper-<?php  echo $diyitemid;?>" data-view="<?php  echo $diyitem['params']['rownum'];?>" data-btn="true">
            <div class="swiper-container fui-picturew row-<?php  echo $diyitem['params']['rownum'];?>" style="padding: <?php  echo $diyitem['style']['paddingtop'];?>px <?php  echo $diyitem['style']['paddingleft'];?>px; background: <?php  echo $diyitem['style']['background'];?>;">
                <div class="swiper-wrapper">
                    <?php  if(is_array($diyitem['data'])) { foreach($diyitem['data'] as $pictureitem) { ?>
                        <a class="swiper-slide item" style="padding: <?php  echo $diyitem['style']['paddingtop'];?>px <?php  echo $diyitem['style']['paddingleft'];?>px;" href="<?php  echo $pictureitem['linkurl'];?>">
                            <div class="image">
                                <img src="<?php  echo tomedia($pictureitem['imgurl'])?>">
                                <?php  if(!empty($pictureitem['title'])) { ?>
                                    <div class="title" style="color: <?php  echo $diyitem['style']['titlecolor'];?>; text-align: <?php  echo $diyitem['style']['titlealign'];?>;"><?php  echo $pictureitem['title'];?></div>
                                <?php  } ?>
                            </div>
                            <div class="text center" style="color: <?php  echo $diyitem['style']['textcolor'];?>; text-align: <?php  echo $diyitem['style']['textalign'];?>;"><?php  echo $pictureitem['text'];?></div>
                        </a>
                    <?php  } } ?>
                </div>
                <?php  if(!empty($diyitem['params']['showbtn'])) { ?>
                    <div class="swiper-button-next swiper-button-white"></div>
                    <div class="swiper-button-prev swiper-button-white"></div>
                <?php  } ?>
            </div>
        </div>
    <?php  } ?>
<?php  } ?>
<!---yi fu yuan ma54mI5p2D5omA5pyJ-->