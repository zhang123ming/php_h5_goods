<?php defined('IN_IA') or exit('Access Denied');?><div class="form-group">
    <label class="col-lg control-label">选择协议</label>
    <div class="col-sm-9 col-xs-12">
    <?php if(cv('commission.set.edit')) { ?>
    <label class="radio-inline"><input type="radio" class="protocoltype" name="data[protocoltype]"  value="0" <?php  if(empty($data['protocoltype'])) { ?> checked="checked"<?php  } ?> /> 申请协议</label>
    <label class="radio-inline"><input type="radio" class="protocoltype" name="data[protocoltype]"  value="1" <?php  if($data['protocoltype'] ==1) { ?> checked="checked"<?php  } ?> /> 提现协议</label>
    <?php  } else { ?>
    <?php  } ?>
    <span class="help-block"></span>
    </div> 
</div>
<div class="form-group">
    <label class="col-lg control-label">协议名称</label>
    <div class="col-sm-9 col-xs-12">
        <?php if(cv('commission.set.edit')) { ?>
        <input type='text' class='form-control protocoltitle' name='<?php  if($data['protocoltype']==0) { ?>data[applytitle]<?php  } else { ?>data[paytitle]<?php  } ?>' value="<?php  if($data['protocoltype']==0) { ?><?php  echo $data['applytitle'];?><?php  } else { ?><?php  echo $data['paytitle'];?><?php  } ?>" />
        <?php  } else { ?>
        <div class='form-control-static'><?php  echo $data['applytitle'];?></div>
        <?php  } ?>
    </div>
</div>

<div class="form-group">
    <label class="col-lg control-label">协议内容</label>
    <div class="col-sm-9 col-xs-12">
        <?php if(cv('commission.set.edit')) { ?>
        <div class="protocolbody0" <?php  if(empty($data['protocoltype'])) { ?> style="display: block;"<?php  } else { ?> style="display: none;" <?php  } ?>>
            <?php  echo tpl_ueditor('data[applycontent]',$data['applycontent'],array('height'=>200))?>  
        </div>
        <div class="protocolbody1" <?php  if(empty($data['protocoltype'])) { ?> style="display: none;"<?php  } else { ?> style="display: block;" <?php  } ?>>
            <?php  echo tpl_ueditor('data[paycontent]',$data['paycontent'],array('height'=>200))?>  
        </div>
        <?php  } else { ?>
        <textarea id='applycontent' style='display:none'><?php  echo $data['applycontent'];?></textarea>
        <a href='javascript:preview_html("#applycontent")' class="btn btn-default">查看内容</a>
        <?php  } ?>
    </div>
</div>
<script type="text/javascript">
    $('.protocoltype').click(function(){
        if($(this).val()==0){
            $('.protocoltitle').attr('name','data[applytitle]');
            $('.protocoltitle').val('<?php  echo $data['applytitle']?>');
            $('.protocolbody0').css('display','block');
             $('.protocolbody1').css('display','none');
        }else{
            $('.protocoltitle').attr('name','data[paytitle]');
            $('.protocoltitle').val('<?php  echo $data['paytitle']?>');
            $('.protocolbody0').css('display','none');
            $('.protocolbody1').css('display','block');
        }
    });
</script>


<!-- yi fu yuan ma wang -->