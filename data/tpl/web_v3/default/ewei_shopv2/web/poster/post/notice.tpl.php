<?php defined('IN_IA') or exit('Access Denied');?><div class="form-group">
    <label class="col-lg control-label">通知模板消息ID（任务处理通知）</label>
    <div class="col-sm-9 col-xs-12">
        <?php if( ce('poster' ,$item) ) { ?>
        <input type="text" name="templateid" class="form-control" value="<?php  echo $item['templateid'];?>" />
        <span class="help-block">公众平台模板消息ID:  OPENTM200605630，如果不填写，则使用客服消息发送通知</span>
        <?php  } else { ?>
        <div class='form-control-static'><?php  echo $item['templateid'];?></div>
        <?php  } ?>
    </div>
</div> 

<div class="form-group">
    <label class="col-lg control-label">推荐者通知</label>
    <div class="col-sm-9 col-xs-12">
        <?php if( ce('poster' ,$item) ) { ?>

        <textarea class="form-control" name="subtext"><?php  echo $item['subtext'];?></textarea>
        <span class="help-block">例如：[nickname] 通过您的二维码关注了公众号! 获得了 [credit] 个<?php  echo $_W['shopset']['trade']['credittext'];?>,[money]元奖励!</span>
        <span class="help-block">[nickname] 为扫码者昵称 [credit] 奖励的<?php  echo $_W['shopset']['trade']['credittext'];?> [money] 奖励的钱 <?php  if($plugin_coupon) { ?>[couponname]优惠券名称 [couponnum] 优惠券张数 <?php  } ?></span>
        <?php  } else { ?>
        <div class='form-control-static'><?php  echo $item['subtext'];?></div>
        <?php  } ?>
    </div>
</div> 
<div class="form-group">
    <label class="col-lg control-label">关注者通知</label>
    <div class="col-sm-9 col-xs-12">
        <?php if( ce('poster' ,$item) ) { ?>
        <textarea class="form-control" name="entrytext"><?php  echo $item['entrytext'];?></textarea>
        <span class="help-block">例如：您扫描了 [nickname] 的二维码关注了公众号! 获得了 [credit] 个<?php  echo $_W['shopset']['trade']['credittext'];?>,[money]元奖励!</span>
        <span class="help-block">[nickname] 为推荐者昵称 [credit] 奖励的<?php  echo $_W['shopset']['trade']['credittext'];?> [money] 奖励的钱 <?php  if($plugin_coupon) { ?>[couponname]优惠券名称 [couponnum] 优惠券张数 <?php  } ?></span>
        <?php  } else { ?>
        <div class='form-control-static'><?php  echo $item['entrytext'];?></div>
        <?php  } ?>
    </div>
</div> 

<div class="form-group">
    <label class="col-lg control-label">关注者现金奖励入账描述</label>
    <div class="col-sm-9 col-xs-12">
        <?php if( ce('poster' ,$item) ) { ?>
        <input type="text" name="subpaycontent" class="form-control" value="<?php  echo $item['subpaycontent'];?>" />
        <span class="help-block">默认为：您通过 [nickname]的推广二维码扫码关注的奖励</span>
        <span class="help-block">[nickname]为推荐者昵称</span>
        <?php  } else { ?>
        <div class='form-control-static'><?php  echo $item['subpaycontent'];?></div>
        <?php  } ?>
    </div>
</div> 

<div class="form-group">
    <label class="col-lg control-label">推荐者现金奖励入账描述</label>
    <div class="col-sm-9 col-xs-12">
        <?php if( ce('poster' ,$item) ) { ?>
        <input type="text" name="recpaycontent" class="form-control" value="<?php  echo $item['recpaycontent'];?>" />
        <span class="help-block">默认为：推荐 [nickname] 扫码关注的奖励</span>
        <span class="help-block">[nickname]为扫码者昵称</span>
        <?php  } else { ?>
        <div class='form-control-static'><?php  echo $item['recpaycontent'];?></div>
        <?php  } ?>
    </div>
</div> 
<!--e-f-w-w-w-->