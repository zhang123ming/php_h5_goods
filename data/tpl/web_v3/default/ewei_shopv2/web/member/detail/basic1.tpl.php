<?php defined('IN_IA') or exit('Access Denied');?><input type="hidden" class="form-control" name="data[mobileverify]" value="1"/>
<div class="form-group">
    <label class="col-lg control-label">手机号码</label>
    <div class="col-sm-9 col-xs-12">
        <input type="text" class="form-control" name="data[mobile]"/>
    </div>
</div>
<div class="form-group">
    <label class="col-lg control-label">用户密码</label>
    <div class="col-sm-9 col-xs-12">
        <input type="text" class="form-control" name="data[pwd]"/>
    </div>
</div>
<div class="form-group">
    <label class="col-lg control-label">会员等级</label>
    <div class="col-sm-9 col-xs-12">
        <?php if(cv('member.list.edit')) { ?>
        <select name='data[level]' class='form-control'>
            <option value=''><?php echo empty($shop['levelname'])?'普通会员':$shop['levelname']?></option>
            <?php  if(is_array($levels)) { foreach($levels as $level) { ?>
            <option value='<?php  echo $level['id'];?>' <?php  if($merchUser&&$merchUser['level']<=$level['level']) { ?> hidden <?php  } ?> <?php  if($member['level']==$level['id']) { ?>selected<?php  } ?>><?php  echo $level['levelname'];?></option>
            <?php  } } ?>
        </select>
        <?php  } else { ?>
        <div class='form-control-static'>
            <?php  if(empty($member['level'])) { ?>
            <?php echo empty($shop['levelname'])?'普通会员':$shop['levelname']?>
            <?php  } else { ?>
            <?php  echo pdo_fetchcolumn('select levelname from '.tablename('ewei_shop_member_level').' where id=:id limit 1',array(':id'=>$member['level']))?>
            <?php  } ?>
        </div>
        <?php  } ?>
    </div>
</div>
<div class="form-group">
    <label class="col-lg control-label">会员分组</label>
    <div class="col-sm-9 col-xs-12">
        <?php if(cv('member.list.edit')) { ?>
        <select name='data[groupid]' class='form-control'>
            <option value=''>无分组</option>
            <?php  if(is_array($groups)) { foreach($groups as $group) { ?>
            <option value='<?php  echo $group['id'];?>' <?php  if($member['groupid']==$group['id']) { ?>selected<?php  } ?>><?php  echo $group['groupname'];?></option>
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
    <label class="col-lg control-label">真实姓名</label>
    <div class="col-sm-9 col-xs-12">
        <input type="text" class="form-control" name="data[realname]"/>
    </div>
</div>


<script type="text/javascript">
    $(function () {
        $(".btn-maxcredit").unbind('click').click(function () {
            var val = $(this).val();
            if(val==1){
                $(".maxcreditinput").css({'display':'inline-block'});
            }else{
                $(".maxcreditinput").css({'display':'none'});
            }
        });
    })

     cascdeInit("<?php  echo $new_area?>","<?php  echo $address_street?>","<?php echo isset($member['agentprovince'])?$member['agentprovince']:''?>","<?php echo isset($member['agentcity'])?$member['agentcity']:''?>","<?php echo isset($member['agentarea'])?$member['agentarea']:''?>","''");
</script>
<!--efwww_com-->