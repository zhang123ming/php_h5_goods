<?php defined('IN_IA') or exit('Access Denied');?><div class="form-group">
    <label class="col-sm-2 control-label must">海报名称</label>
    <div class="col-sm-9 col-xs-12">
        <?php if( ce('poster' ,$item) ) { ?>
        <input type="text" name="title" class="form-control" value="<?php  echo $item['title'];?>" data-rule-required="true" />
        <?php  } else { ?>
        <div class='form-control-static'><?php  echo $item['title'];?></div>
        <?php  } ?>
    </div>
</div>

<div class="form-group">
    <label class="col-sm-2 control-label must">海报类型</label>
    <div class="col-sm-9 col-xs-12">
        <?php if( ce('poster' ,$item) ) { ?>
        <label class="radio-inline">
            <input type="radio" name="type" value="1" <?php  if($item['type']==1) { ?>checked<?php  } ?> /> 商城海报
        </label>

        <!-- <label class="radio-inline">
            <input type="radio" name="type" value="2" <?php  if($item['type']==2) { ?>checked<?php  } ?> /> 小店海报
        </label> -->

        <label class="radio-inline">
            <input type="radio" name="type" value="3" <?php  if($item['type']==3) { ?>checked<?php  } ?> /> 商品海报
        </label>

        <label class="radio-inline"> 
            <input type="radio" name="type" value="4" <?php  if($item['type']==4) { ?>checked<?php  } ?> /> 关注海报
        </label>
        <!-- <label class="radio-inline">
            <input type="radio" name="type" value="101" <?php  if($item['type']==101) { ?>checked<?php  } ?> /> 直播间海报
        </label> -->
        <?php  } else { ?>
        <div class='form-control-static'>
            <?php  if($item['type']==1) { ?>商城海报<?php  } ?>
            <?php  if($item['type']==2) { ?>小店海报<?php  } ?>
            <?php  if($item['type']==3) { ?>商品海报<?php  } ?>
            <?php  if($item['type']==4) { ?>关注海报<?php  } ?>
            <?php  if($item['type']==101) { ?>直播间海报<?php  } ?>
        </div>
        <?php  } ?>
    </div> 
</div> 

<div class="form-group">
    <label class="col-sm-2 control-label must">回复关键词</label>
    <div class="col-sm-9 col-xs-12">
        <?php if( ce('poster' ,$item) ) { ?>
        <input type="text" name="keyword2" class="form-control" value="<?php  echo $item['keyword2'];?>" data-rule-required="true" />
        <span class='help-block'>如果是商品海报 ，回复关键词是 关键词+商品ID</span>
        <?php  } else { ?>
        <div class='form-control-static'><?php  echo $item['keyword'];?></div>
        <?php  } ?>
    </div>
</div>
<div class="form-group">
    <label class="col-sm-2 control-label">会员是否自动分组</label>
    <div class="col-sm-9 col-xs-12">
        <?php if( ce('poster' ,$item) ) { ?>
        <label class="radio-inline">
            <input type="radio" name="ismembergroup" value="0" <?php  if(empty($item['ismembergroup'])) { ?>checked<?php  } ?> /> 否
        </label>
        <label class="radio-inline">
            <input type="radio" name="ismembergroup" value="1" <?php  if($item['ismembergroup']==1) { ?>checked<?php  } ?> /> 是
        </label>
        <span class='help-block'>开启会员自动分组，扫描二维码时会员将添加到指定分组</span>
        <?php  } else { ?>
        <div class='form-control-static'><?php  if($item['ismembergroup']==1) { ?>是<?php  } else { ?>否<?php  } ?></div>
        <?php  } ?>
    </div>
</div>
<div class="form-group" id="membergroup" <?php  if($item['ismembergroup']==0) { ?>style="display:none;"<?php  } ?>>
<label class="col-sm-2 control-label">指定会员分组</label>
<div class="col-sm-9 col-xs-12">
    <?php if( ce('poster' ,$item) ) { ?>
    <select name='membergroupid' class='form-control'>
        <option value=''>无分组</option>
        <?php  if(is_array($groups)) { foreach($groups as $group) { ?>
        <option value='<?php  echo $group['id'];?>' <?php  if($item['membergroupid']==$group['id']) { ?>selected<?php  } ?>><?php  echo $group['groupname'];?></option>
        <?php  } } ?>
    </select>
    <?php  } else { ?>
    <div class='form-control-static'>
        <?php  if(empty($member['groupid'])) { ?>
        无分组
        <?php  } else { ?>
        <?php  echo pdo_fetchcolumn('select groupname from '.tablename('ewei_shop_member_group').' where id=:id limit 1',array(':id'=>$member['groupid']))?>
        <?php  } ?>
    </div>
    <?php  } ?>
</div>
</div>

<div class="form-group">
    <label class="col-sm-2 control-label">是否默认</label>
    <div class="col-sm-9 col-xs-12">
        <?php if( ce('poster' ,$item) ) { ?>
        <label class="radio-inline">
            <input type="radio" name="isdefault" value="0" <?php  if(empty($item['isdefault'])) { ?>checked<?php  } ?> /> 否
        </label>
        <label class="radio-inline">
            <input type="radio" name="isdefault" value="1" <?php  if($item['isdefault']==1) { ?>checked<?php  } ?> /> 是
        </label>
        <span class='help-block'>是否是海报类型的默认设置，一种海报只能一个默认设置</span>
        <?php  } else { ?>
        <div class='form-control-static'><?php  if($item['isdefault']==1) { ?>是<?php  } else { ?>否<?php  } ?></div>
        <?php  } ?>
    </div> 
</div>
<div class="form-group">
    <label class="col-sm-2 control-label">生成等待文字</label>
    <div class="col-sm-9 col-xs-12">
        <?php if( ce('poster' ,$item) ) { ?>

        <textarea name="waittext" class="form-control"  ><?php  echo $item['waittext'];?></textarea>
        <span class="help-block">例如：您的专属海报正在拼命生成中，请等待片刻...</span>
        <?php  } else { ?>
        <div class='form-control-static'><?php  if(empty($item['waittext'])) { ?>未填写<?php  } else { ?><?php  echo $item['waittext'];?><?php  } ?></div>
        <?php  } ?>

    </div>
</div>
<script type="text/javascript">
    $(function () {
        $("input[name='ismembergroup']").off('click').on('click',function () {
            if($(this).val()==1){
                $("#membergroup").show();
            }else{
                $("#membergroup").hide();
            }
        })
    })
</script>

