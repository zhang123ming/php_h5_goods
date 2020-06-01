<?php defined('IN_IA') or exit('Access Denied');?><div class="region-goods-details row">
	<div class="region-goods-left col-sm-2">
		核销类商品规则
	</div>
	<div class="region-goods-right col-sm-10">





		<div class="form-group" >
			<label class="col-sm-2 control-label">核销次数</label>
			<div class="col-sm-6 col-xs-12">
				<?php if( ce('goods' ,$item) ) { ?>
				<div class="input-group">
					<input type="text" name="verifygoodsnum" id="verifygoodsnum" class="form-control" value="<?php  echo $item['verifygoodsnum'];?>" />
					<span class="input-group-addon">次</span>
				</div>
				<span class="help-block" style="margin-bottom: 0">此商品可以核销次数,不填或填写0及以下为默认不限次数</span>
				<?php  } else { ?>
				<div class='form-control-static'><?php  echo $item['verifygoodsnum'];?> 次</div>
				<?php  } ?>
			</div>
		</div>

		<div class="form-group">
			<label class="col-sm-2 control-label">有效期类型</label>
			<div class="col-sm-9">
				<?php if( ce('goods' ,$item) ) { ?>
				<label class="radio-inline"><input type="radio" onclick="$('.showverifydays').show();$('.showverifylimitdate').hide();"   name="verifygoodslimittype" value="0" <?php  if(empty($item['verifygoodslimittype']) ) { ?>checked="true"<?php  } ?>  /> 购买后有效</label>
				<label class="radio-inline"><input type="radio" onclick="$('.showverifydays').hide();$('.showverifylimitdate').show();"   name="verifygoodslimittype" value="1" <?php  if($item['verifygoodslimittype'] == 1) { ?>checked="true"<?php  } ?>   /> 指定过期日期</label>
				<?php  } else { ?>
				<div class='form-control-static'><?php  if(empty($item['verifygoodslimittype'])) { ?>购买后有效<?php  } else { ?>指定过期日期<?php  } ?></div>
				<?php  } ?>
			</div>
		</div>

		<div class="form-group showverifydays" <?php  if(!empty($item['verifygoodslimittype'])) { ?> style ='display:none;'<?php  } ?>>
		<label class="col-sm-2 control-label">有效天数</label>
		<div class="col-sm-6 col-xs-12">
			<?php if( ce('goods' ,$item) ) { ?>
			<div class="input-group">
				<input type="text" name="verifygoodsdays" id="verifygoodsdays" class="form-control" value="<?php  echo $item['verifygoodsdays'];?>" />
				<span class="input-group-addon">天</span>
			</div>
			<span class="help-block">自购买之日起多少天内有效,不写默认365天</span>
			<?php  } else { ?>
			<div class='form-control-static'><?php  echo $item['verifygoodsdays'];?> 天</div>
			<?php  } ?>
		</div>
	</div>


	<div class="form-group showverifylimitdate" <?php  if(empty($item['verifygoodslimittype'])) { ?> style ='display:none;'<?php  } ?>>
	<label class="col-sm-2 control-label">过期日期</label>
	<div class="col-sm-6 col-xs-12">
		<?php if( ce('goods' ,$item) ) { ?>
		<div class="input-group">
			<?php echo tpl_form_field_date('verifygoodslimitdate', !empty($item['verifygoodslimitdate']) ? date('Y-m-d H:i',$item['verifygoodslimitdate']) : date('Y-m-d H:i'),true)?>
		</div>
		<span class="help-block">无论何时购买此商品,到达指定时间后都将过期,无法核销.</span>
		<?php  } else { ?>
		<div class="col-sm-4 col-xs-6">
			<div class='form-control-static'>
				<?php  echo date('Y-m-d H:i',$item['verifygoodslimitdate'])?>}
			</div>
		</div>
		<?php  } ?>
	</div>
</div>
	</div>
</div>

<!--www-efwww-com-->