<?php defined('IN_IA') or exit('Access Denied');?><?php  $member=m('member')->getMember($_W['openid']); $agentName=pdo_fetchcolumn('select nickname from '.tablename('ewei_shop_member').' where uniacid=:uniacid and id=:id',array(':uniacid'=>$_W['uniacid'],':id'=>$member['agentid']))?>
<?php  if(empty($diyitem['params']['style']) || $diyitem['params']['style']=='default1') { ?>
   <div style="overflow: hidden;height: 7.5rem;position: relative;background: #fff">
       <div class="headinfo" style="<?php  if(!empty($diyitem['style']['background'])) { ?>border: none;<?php  } ?>;z-index: 100">
           <?php  if(!empty($diyitem['params']['seticon'])) { ?>
                <a class="setbtn" style="color: <?php  echo $diyitem['style']['textcolor'];?>;" href="<?php  echo $diyitem['params']['setlink'];?>" data-nocache="true"><i class="icon <?php  echo $diyitem['params']['seticon'];?>"></i></a>
            <?php  } ?>
           <div class="child">
               <?php  if(!$_W['shopset']['trade']['closecredit2']) { ?>
               <div class="title" style="color: <?php  echo $diyitem['style']['textcolor'];?>;font-size: <?php  echo $diyitem['style']['fontsize'];?>px;"><?php  echo $diyitem['info']['textmoney'];?></div>
               <div class="num" style="color: <?php  echo $diyitem['style']['textlight'];?>;font-size: <?php  echo $diyitem['style']['fontsize'];?>px;"><?php  echo $diyitem['info']['money'];?></div>
               <?php  if(!empty($diyitem['params']['leftnav'])) { ?>
               <a class="btn" style="color: <?php  echo $diyitem['style']['textcolor'];?>; border-color: <?php  echo $diyitem['style']['textcolor'];?>;font-size: <?php  echo $diyitem['style']['fontsize'];?>px;" href="<?php  echo $diyitem['params']['leftnavlink'];?>" data-nocache="true"><?php  echo $diyitem['params']['leftnav'];?></a>
               <?php  } ?>
               <?php  } ?>
           </div>
           <div class="child userinfo" style="color: <?php  echo $diyitem['style']['textcolor'];?>;">
               <a href="<?php  echo mobileUrl('member/info/face')?>" data-nocache="true" style="color: <?php  echo $diyitem['style']['textcolor'];?>;">
                   <div class="face <?php  echo $diyitem['style']['headstyle'];?>"><img src="<?php  echo $diyitem['info']['avatar'];?>" onerror="this.src='../addons/ewei_shopv2/static/images/noface.png'"></div>
                   <div class="name" style="font-size: <?php  echo $diyitem['style']['fontsize'];?>px;"><?php  echo $diyitem['info']['nickname'];?></div>
               </a>
               <?php  if(!empty($diyitem['params']['levellink'])) { ?>
               <a class="level" href="<?php  echo $diyitem['params']['levellink'];?>" style="font-size: <?php  echo $diyitem['style']['fontsize'];?>px;">等级：[<?php  echo $diyitem['info']['levelname'];?>] <i class="icon icon-question1" style="font-size: 0.6rem;"></i></a>
               <?php  } else { ?>
               <div class="level" style="font-size: <?php  echo $diyitem['style']['fontsize'];?>px;">等级：[<?php  echo $diyitem['info']['levelname'];?>]</div>
               <?php  } ?>
               <div style="font-size: <?php  echo $diyitem['style']['fontsize'];?>px;">ID：(<?php  echo $member['id'];?>)</div>
           </div>
           <div class="child">
              <?php  if(!$_W['shopset']['trade']['closecredit']) { ?>
               <div class="title" style="color: <?php  echo $diyitem['style']['textcolor'];?>;"><?php  echo $diyitem['info']['textcredit'];?></div>
               <div class="num" style="color: <?php  echo $diyitem['style']['textlight'];?>;"><?php  echo $diyitem['info']['credit'];?></div>
               <?php  if(!empty($diyitem['params']['rightnav'])) { ?>
               <a class="btn" style="color: <?php  echo $diyitem['style']['textcolor'];?>; border-color: <?php  echo $diyitem['style']['textcolor'];?>; font-size: <?php  echo $diyitem['style']['fontsize'];?>px;" href="<?php  echo $diyitem['params']['rightnavlink'];?>" data-nocache="true"><?php  echo $diyitem['params']['rightnav'];?></a>
               <?php  } ?>
              <?php  } ?>
           </div>
       </div>
       <div class="member_header"style="background: <?php  echo $diyitem['style']['background'];?>;border-color: <?php  echo $diyitem['style']['background'];?>;font-size: <?php  echo $diyitem['style']['fontsize'];?>px;"></div>
   </div>
<?php  } else if($diyitem['params']['style']=='default2') { ?>
    <div class="headinfo style-2" style="background: <?php  echo $diyitem['style']['background'];?>; <?php  if(!empty($diyitem['style']['background'])) { ?>border: none;<?php  } ?>">
        <?php  if(!empty($diyitem['params']['seticon'])) { ?>
          <a class="setbtn" style="color: <?php  echo $diyitem['style']['textcolor'];?>;" href="<?php  echo $diyitem['params']['setlink'];?>" data-nocache="true"><i class="icon <?php  echo $diyitem['params']['seticon'];?>"></i></a>
        <?php  } ?>
          <div class="face <?php  echo $diyitem['style']['headstyle'];?>"><img src="<?php  echo $diyitem['info']['avatar'];?>" onerror="this.src='../addons/ewei_shopv2/static/images/noface.png'"></div>
          <div class="inner" style="color: <?php  echo $diyitem['style']['textcolor'];?>;">
              <div class="name" style="font-size: <?php  echo $diyitem['style']['fontsize'];?>px;"><?php  echo $diyitem['info']['nickname'];?></div>
                <?php  if(!empty($diyitem['params']['levellink'])) { ?>
                <a class="level" href="<?php  echo $diyitem['params']['levellink'];?>" style="color: <?php  echo $diyitem['style']['textcolor'];?>;font-size: <?php  echo $diyitem['style']['fontsize'];?>px;">等级：[<?php  echo $diyitem['info']['levelname'];?>] <i class="icon icon-question1" style="ffont-size: <?php  echo $diyitem['style']['fontsize'];?>px;"></i></a>
                <?php  } else { ?>
                <div class="level" style="font-size: <?php  echo $diyitem['style']['fontsize'];?>px;">等级：[<?php  echo $diyitem['info']['levelname'];?>]</div>
                <?php  } ?>
                <div style="font-size: <?php  echo $diyitem['style']['fontsize'];?>px;">ID：(<?php  echo $member['id'];?>)</div>
              <?php  if(!$_W['shopset']['trade']['closecredit2']) { ?>
              <div class="credit" style="font-size: <?php  echo $diyitem['style']['fontsize'];?>px;"><?php  echo $diyitem['info']['textmoney'];?>: <span style="color: <?php  echo $diyitem['style']['textlight'];?>;"><?php  echo $diyitem['info']['money'];?></span></div>
              <?php  } ?>
              <?php  if(!$_W['shopset']['trade']['closecredit']) { ?>
              <div class="credit" style="font-size: <?php  echo $diyitem['style']['fontsize'];?>px;"><?php  echo $diyitem['info']['textcredit'];?>: <span style="color: <?php  echo $diyitem['style']['textlight'];?>;"><?php  echo $diyitem['info']['credit'];?></span></div>
              <?php  } ?>
          </div>
    </div>
<?php  } else if($diyitem['params']['style']=='default3') { ?>
    <div class="headinfo style-2" style="background: <?php  echo $diyitem['style']['background'];?>; <?php  if(!empty($diyitem['style']['background'])) { ?>border: none;<?php  } ?>">
        <?php  if(!empty($diyitem['params']['seticon'])) { ?>
          <a class="setbtn" style="color: <?php  echo $diyitem['style']['textcolor'];?>;" href="<?php  echo $diyitem['params']['setlink'];?>" data-nocache="true"><i class="icon <?php  echo $diyitem['params']['seticon'];?>"></i></a>
        <?php  } ?>
        <div class="face <?php  echo $diyitem['style']['headstyle'];?>"><img src="<?php  echo $diyitem['info']['avatar'];?>" onerror="this.src='../addons/ewei_shopv2/static/images/noface.png'"></div>
        <div class="child" style="width: 50%;padding-bottom: 0px;">
          <div class="inner" style="color: <?php  echo $diyitem['style']['textcolor'];?>;text-align:left;">
              <div class="name" style="font-size: <?php  echo $diyitem['style']['fontsize'];?>px;"><?php  echo $diyitem['info']['nickname'];?></div>
              <?php  if(!$_W['shopset']['trade']['closecredit2']) { ?>
              <div class="credit" style="font-size: <?php  echo $diyitem['style']['fontsize'];?>px;"><?php  echo $diyitem['info']['textmoney'];?>: <span style="color: <?php  echo $diyitem['style']['textlight'];?>;"><?php  echo $diyitem['info']['money'];?></span></div>
              <?php  } ?>
              <?php  if(!$_W['shopset']['trade']['closecredit']) { ?>
              <div class="credit" style="font-size: <?php  echo $diyitem['style']['fontsize'];?>px;"><?php  echo $diyitem['info']['textcredit'];?>: <span style="color: <?php  echo $diyitem['style']['textlight'];?>;"><?php  echo $diyitem['info']['credit'];?></span></div>
              <?php  } ?>
          </div>
        </div>
        <div class="child" style="width: 40%;padding-bottom: 0px;">
              <?php  if(!empty($diyitem['params']['levellink'])) { ?>
              <a class="level" href="<?php  echo $diyitem['params']['levellink'];?>" style="color: <?php  echo $diyitem['style']['textcolor'];?>;font-size: <?php  echo $diyitem['style']['fontsize'];?>px;">等级：[<?php  echo $diyitem['info']['levelname'];?>] <i class="icon icon-question1" style="font-size: 0.6rem;"></i></a>
              <?php  } else { ?>
              <div class="level" style="color: yellow;" style="font-size: <?php  echo $diyitem['style']['fontsize'];?>px;"><strong>等级：[<?php  echo $diyitem['info']['levelname'];?>]</strong></div>
              <?php  } ?>
            <?php  if(!empty($diyitem['params']['defaultnav'])) { ?>
             <a class="btn" style="color: <?php  echo $diyitem['style']['textcolor'];?>; border-color: <?php  echo $diyitem['style']['textcolor'];?>;font-size: <?php  echo $diyitem['style']['fontsize'];?>px;" href="<?php  echo $diyitem['params']['defaultnavlink'];?>" data-nocache="true"><?php  echo $diyitem['params']['defaultnav'];?></a>
             <?php  } ?>
        </div>
    </div>
<?php  } ?>
<!--yifuyuanma-->