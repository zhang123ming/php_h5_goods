<?php defined('IN_IA') or exit('Access Denied');?><div class="form-group">
    <label class="col-lg control-label">分销层级</label>
    <div class="col-sm-8">
    	<?php if(cv('commission.set.edit')) { ?>
			<label class="radio-inline"><input type="radio"  name="data[level]" value="0" <?php  if($data['level'] ==0) { ?> checked="checked"<?php  } ?> /> 不开启</label>
			<label class="radio-inline"><input type="radio"  name="data[level]" value="1" <?php  if($data['level'] ==1) { ?> checked="checked"<?php  } ?> /> 一级分销</label>
			<label class="radio-inline"><input type="radio"  name="data[level]" value="2" <?php  if($data['level'] ==2) { ?> checked="checked"<?php  } ?> /> 二级分销</label>
			<!-- <label class="radio-inline"><input type="radio"  name="data[level]" value="3" <?php  if($data['level'] ==3) { ?> checked="checked"<?php  } ?> /> 三级分销</label> -->
			<div class='help-block'>默认佣金比例请到<a href='<?php  echo webUrl('commission/level')?>' target='_blank'>【分销商等级】</a>进行设置</div>
		<?php  } else { ?>
			<?php  if($data['level'] ==0) { ?>不开启<?php  } ?>
			<?php  if($data['level'] ==1) { ?>一级分销<?php  } ?>
			<?php  if($data['level'] ==2) { ?>二级分销<?php  } ?>
			<!-- <?php  if($data['level'] ==3) { ?>三级分销<?php  } ?> -->
		<?php  } ?>
    </div>
</div> 
<!-- <div class="form-group">
    <label class="col-lg control-label">分销内购</label>
    <div class="col-sm-9 col-xs-12">
    	<?php if(cv('commission.set.edit')) { ?>
			<label class="radio-inline"><input type="radio"  name="data[selfbuy]" value="1" <?php  if($data['selfbuy'] ==1) { ?> checked="checked"<?php  } ?> /> 开启</label>
			<label class="radio-inline"><input type="radio"  name="data[selfbuy]" value="0" <?php  if($data['selfbuy'] ==0) { ?> checked="checked"<?php  } ?> /> 关闭</label>
			<span class='help-block'>开启分销内购，分销商自己购买商品，享受一级佣金，上级享受二级佣金，上上级享受三级佣金</span>
		<?php  } else { ?>
			<?php  if($data['selfbuy'] ==0) { ?>关闭<?php  } else { ?>开启<?php  } ?>
		<?php  } ?>
    </div>
</div>

<div class="form-group">
        <label class="col-lg control-label">开启复购</label>
        <div class="col-sm-9 col-xs-12">
            <?php if(cv('commission.set.edit')) { ?>
                <label class="radio-inline"><input type="radio" <?php  if($data['repurchase']==1) { ?>checked<?php  } ?>  name="data[repurchase]" value="1"   /> 按订单开启</label>
                <label class="radio-inline"><input type="radio" <?php  if($data['repurchase']==2) { ?>checked<?php  } ?>  name="data[repurchase]" value="2"   /> 按产品开启</label>
                <label class="radio-inline"><input type="radio" <?php  if($data['repurchase']==0) { ?>checked<?php  } ?>  name="data[repurchase]" value="0"  /> 关闭</label>
                <span class='help-block'>开启复购，按订单：在用户支付第二个订单时按照复购参数返利;<br>按产品：用户在第二次购买相同产品时按照复购参数返利；
                </span>
            <?php  } else { ?>
                <?php  if($data['repurchase'] ==0) { ?>关闭<?php  } else { ?>开启<?php  } ?>
            <?php  } ?>
        </div>
</div>

<div class="form-group">
    <label class="col-lg control-label">模式</label>
    <div class="col-sm-9 col-xs-12">
    	<?php if(cv('commission.set.edit')) { ?>
            <?php  if($_W['username']=='admin') { ?>
    		<select class="form-control" id="commissionMode" style="width: 200px;" name="data[commissionMode]">
                <option value='0'>无</option>
    			<option <?php  if($data['commissionMode']==100) { ?> selected <?php  } ?> value='100'>代理极差模式</option>
    		</select>
            <span class='help-block' style="color: red;">在此模式下会产生不同会员级别的收益，代理收益不参与分销商升级条件，会员等级必须大于90，比例方可生效</span>
            <?php  } else { ?>
            <?php  if($data['commissionMode']==100) { ?>
            代理极差模式
            <span class='help-block' style="color: red;">在此模式下会产生不同会员级别的收益，代理收益不参与分销商升级条件，会员等级必须大于90，比例方可生效</span>
            <?php  } else { ?>
            无
            <?php  } ?>
            <?php  } ?>
            
		<?php  } else { ?>
			<?php  if($data['commissionMode']) { ?>开启<?php  } ?>
		<?php  } ?>
    </div>
</div> -->
<div id="commission100" class="commission">
    <?php  if(is_array($mLevels)) { foreach($mLevels as $m) { ?>
    <?php  if($m['level']>90) { ?>
    <div class="form-group" >
        <label class="col-lg control-label"><?php  echo $m['levelname'];?></label>
        <div class="col-sm-8">
            <?php if(cv('commission.set.edit')) { ?>
            返利&nbsp;&nbsp;<input type="text"  name="agent[<?php  echo $m['id'];?>][commission]"  style="width: 100px;display: inline-block;" class="form-control" value="<?php  echo $m['commission'];?>"  />%
            <?php  } else { ?>
            <?php echo empty($m['commission'])?0:$m['commission']?>%
            <?php  } ?>
        </div>

    </div>
    <?php  } ?>
    <?php  } } ?>
  
    <div class="form-group">
        <label class="col-lg control-label">同级收益</label>
        <div class="col-sm-8">
            <?php if(cv('commission.set.edit')) { ?>
            <input type="text" name="data[samegrade]"  style="width: 100px;display: inline-block;" class="form-control" value="<?php  echo $data['samegrade'];?>" />%
            <?php  } else { ?>
            <?php echo empty($data['samegrade'])?0:$data['samegrade']?>%
            <?php  } ?>
        </div>
    </div>
</div>


<script type="text/javascript">
    $(function(){
       
        var comMode="<?php  echo $data['commissionMode'];?>";
        changeMode(comMode);
        $('#commissionMode').change(function(){
            var i = parseInt($('#commissionMode').val());
           changeMode(i);
        })
        $("#creditRate").change(function(){
            var value = parseFloat($('#creditRate').val());
            if (value<0||value>100) {
                FoxUi.toast.show('请输入0-100的数字')
            }
            $('#creditRateaT').text((100-value)+'%');
        })

        function changeMode(i){

            $('.commission').hide();
            $('.commission input').attr('disabled',true)
            if (i>0) {
                $('#commission'+i).show();
                $('#commission'+i+' input').attr('disabled',false)
            }  
        }
    })
</script>
