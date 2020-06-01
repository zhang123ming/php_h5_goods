<?php defined('IN_IA') or exit('Access Denied');?> 
     
	 <div class="form-group">
		<label class="col-lg control-label">购物券优惠券使用说明</label>
		<div class="col-sm-9 col-xs-12">
			  <?php if(cv('sale.coupon.set')) { ?>
                                 <?php  echo tpl_ueditor('data[consumedesc]',$data['consumedesc'])?>
			   <span class='help-block'>统一说明会放到购物券单独说明的前面</span>
                            <?php  } else { ?>
                            <textarea id='consumedesc' style='display:none'><?php  echo $data['consumedesc'];?></textarea>
							
                            <a href='javascript:preview_html("#consumedesc")' class="btn btn-default">查看内容</a>
                            <?php  } ?>
		</div>
	</div>
		 
	 <div class="form-group">
		<label class="col-lg control-label">充值优惠券使用说明</label>
		<div class="col-sm-9 col-xs-12">
			  <?php if(cv('sale.coupon.set')) { ?>
                            <?php  echo tpl_ueditor('data[rechargedesc]',$data['rechargedesc'])?>
							<span class='help-block'>统一说明会放到充值券单独说明的前面</span>
                            <?php  } else { ?>
                            <textarea id='rechargedesc' style='display:none'><?php  echo $data['rechargedesc'];?></textarea>
                            <a href='javascript:preview_html("#rechargedesc")' class="btn btn-default">查看内容</a>
                            <?php  } ?>
		</div>
	</div>
		 
 

<div class="form-group">
                <label class="col-lg control-label">会员中心开启状态</label>
                <div class="col-sm-9 col-xs-12">
                    <?php if(cv('sale.coupon.set')) { ?>
					<label class="radio-inline "><input type="radio" name="data[closemember]" value="0" <?php  if($data['closemember']==0) { ?>checked<?php  } ?>> 开启</label>
					<label class="radio-inline"><input type="radio" name="data[closemember]"  value="1" <?php  if($data['closemember']==1) { ?>checked<?php  } ?>> 关闭</label>
					<span class="help-block">是否开启会员中心优惠券</span>
					<?php  } else { ?>
					<div class='form-control-static'>
						<?php  if($data['closemember']==0) { ?>
						开启
						<?php  } else if($data['closemember']==1) { ?>
						关闭
						<?php  } ?>
					</div>
                    <?php  } ?>
                </div>
   </div>				
	<div class="form-group">
                <label class="col-lg control-label">领券中心开启状态</label>
                <div class="col-sm-9 col-xs-12">
                    <?php if(cv('sale.coupon.set')) { ?>
					<label class="radio-inline "><input type="radio" name="data[closecenter]" value="0" <?php  if($data['closecenter']==0) { ?>checked<?php  } ?>> 开启</label>
					<label class="radio-inline"><input type="radio" name="data[closecenter]"  value="1" <?php  if($data['closecenter']==1) { ?>checked<?php  } ?>> 关闭</label>
					<?php  } else { ?>
					<div class='form-control-static'>
						<?php  if($data['closecenter']==0) { ?>
						开启
						<?php  } else if($data['closecenter']==1) { ?>
						关闭
						<?php  } ?>
					</div>
                    <?php  } ?>
                </div>
			</div>
	<div class="form-group">
        <label class="col-lg control-label">优惠券合并使用</label>
        <div class="col-sm-9 col-xs-12">
            <?php if(cv('sale.coupon.set')) { ?>
			<label class="radio-inline "><input type="radio" name="data[mergecoupon]" value="1" <?php  if($data['mergecoupon']==1) { ?>checked<?php  } ?>> 开启且多出部分不可退</label>
			<label class="radio-inline "><input type="radio" name="data[mergecoupon]" value="2" <?php  if($data['mergecoupon']==2) { ?>checked<?php  } ?>> 开启且多出部分可退</label>
			<label class="radio-inline"><input type="radio" name="data[mergecoupon]"  value="0" <?php  if($data['mergecoupon']==0) { ?>checked<?php  } ?>> 关闭</label>
			<span class="help-block">开启表示同一订单可使用多张优惠券，仅针对立减劵和按比例优惠有效</span>
			<?php  } else { ?>
			<div class='form-control-static'>
				<?php  if($data['mergecoupon']==1) { ?>
				开启
				<?php  } else if($data['mergecoupon']==0) { ?>
				关闭
				<?php  } ?>
			</div>
            <?php  } ?>
        </div>
   </div>
   <div class="form-group">
        <label class="col-lg control-label">商品类别区分</label>
        <div class="col-sm-9 col-xs-12">
            <?php if(cv('sale.coupon.set')) { ?>
			<label class="radio-inline "><input type="radio" name="data[goodscate]" value="1" <?php  if($data['goodscate']==1) { ?>checked<?php  } ?>> 开启</label>
			<label class="radio-inline"><input type="radio" name="data[goodscate]"  value="0" <?php  if($data['goodscate']==0) { ?>checked<?php  } ?>> 关闭</label>
			<span class="help-block">开启 购买商品赠送的优惠券无法在此商品类别中使用</span>
			<?php  } else { ?>
			<div class='form-control-static'>
				<?php  if($data['goodscate']==1) { ?>
				开启
				<?php  } else if($data['goodscate']==0) { ?>
				关闭
				<?php  } ?>
			</div>
            <?php  } ?>
        </div>
   </div>
   <div class="form-group">
        <label class="col-lg control-label">优惠券区分供应商</label>
        <div class="col-sm-9 col-xs-12">
            <?php if(cv('sale.coupon.set')) { ?>
			<label class="radio-inline "><input type="radio" name="data[useapart]" value="1" <?php  if($data['useapart']==1) { ?>checked<?php  } ?>> 开启</label>
			<label class="radio-inline"><input type="radio" name="data[useapart]"  value="0" <?php  if($data['useapart']==0) { ?>checked<?php  } ?>> 关闭</label>
			<span class="help-block">开启 按比例返的优惠券只能购买其他商家的产品时使用</span>
			<?php  } else { ?>
			<div class='form-control-static'>
				<?php  if($data['useapart']==1) { ?>
				开启
				<?php  } else if($data['useapart']==0) { ?>
				关闭
				<?php  } ?>
			</div>
            <?php  } ?>
        </div>
   </div>
   <div class="form-group">
        <label class="col-lg control-label">F/B商家区分</label>
        <div class="col-sm-9 col-xs-12">
            <?php if(cv('sale.coupon.set')) { ?>
			<label class="radio-inline "><input type="radio" name="data[fbapart]" value="1" <?php  if($data['fbapart']==1) { ?>checked<?php  } ?>> F端可用于B端</label>
			<label class="radio-inline"><input type="radio" name="data[fbapart]"  value="2" <?php  if($data['fbapart']==2) { ?>checked<?php  } ?>> B端可用于F端</label>
			<label class="radio-inline"><input type="radio" name="data[fbapart]"  value="0" <?php  if($data['fbapart']==0) { ?>checked<?php  } ?>> 不区分</label>
			<span class="help-block">此处用来确定F端B端产生的 按比例消费优惠券能否用于相互之间的消费</span>
			<?php  } else { ?>
			<div class='form-control-static'>
				<?php  if($data['fbapart']==1) { ?>
				开启
				<?php  } else if($data['fbapart']==0) { ?>
				关闭
				<?php  } ?>
			</div>
            <?php  } ?>
        </div>
   </div>
   <div class="form-group">
        <label class="col-lg control-label">线下发送优惠券费率</label>
        <div class="col-sm-9 col-xs-12 " >
            <div class='input-group fixsingle-input-group'>
                <span class='input-group-addon'>每发送1元优惠券需消耗</span>
                <input type='text' class='form-control' name='data[couponrate]'  value="<?php  echo $data['couponrate'];?>"/>
                <span class='input-group-addon'>元余额</span>
             </div>   
        </div>
   </div>
   


	 <script type="text/javascript" language="javascript" >
		 function display(num){
			 $("#"+num).show();
		 }

		 function disappear(num){
			 $("#"+num).hide();
		 }
	 </script>


	 <div class="form-group">
		<label class="col-lg control-label">用户推送优惠卷模板</label>
		<div class="col-sm-9 col-xs-12 position-parent">
			<?php if(cv('sale.coupon.set')) { ?>
				 <label class="radio-inline" >
					 <input type="radio" name="data[showtemplate]"  value="1" <?php  if($data['showtemplate'] == 1) { ?>checked="true"<?php  } ?> />
					 <a href="#" onmouseover="display('t1')" onmouseout="disappear('t1')">模板1</a>
				 </label>
				 <label class="radio-inline"'>
					<input type="radio" name="data[showtemplate]" value="2" <?php  if($data['showtemplate'] == 2) { ?>checked="true"<?php  } ?> />
					<a href="#" onmouseover="display('t2')" onmouseout="disappear('t2')">模板2(默认)</a>
				 </label>
			 <?php  } else { ?>
				<div class='form-control-static'>
					 <?php  if($data['showtemplate']==1) { ?>
						模板1
					 <?php  } else { ?>
						模板2(默认)
					 <?php  } ?>
				 </div>
			 <?php  } ?>
			<div id="t1" class="position-t" style="display: none;">
				<img src="<?php  echo $_W['siteroot'];?>addons/ewei_shopv2/static/images/t1.png" width="100%">
			</div>
			<div id="t2" class="position-t" style="display: none;">
				<img src="<?php  echo $_W['siteroot'];?>addons/ewei_shopv2/static/images/t2.png" width="100%">
			</div>
		</div>
	</div>

	 <style type="text/css">
		 .position-parent{position: relative;}
		 .position-t{position: absolute;bottom: 30px;border:1px solid #777;padding:5px;background: #fff;-webkit-border-radius: 5px;-moz-border-radius: 5px;border-radius: 5px;
		 z-index: 10000;width:300px;}
	 </style>

<!--efwww_com-->