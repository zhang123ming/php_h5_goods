<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>
<style>
	.new-address {
		background: -webkit-gradient(linear, 0% 75%, 75% 100%, from(#2caeb2), to(#3fbaa0)) !important;
		margin-bottom: 26px!important;
	}

	.new-address:active,
	.new-address:link,
	.new-address:focus,
	.new-address:hover {
		color: #fff!important;
	}
		.give{
		background: #26ad95!important;
		color: #fff!important;
		border-color: #26ad95!important;
			width: 36px!important;
		height: 16.5px!important;
	}
</style>
<div class='fui-page  fui-page-current fui-page-address'>
    <div class="fui-header">
	<div class="fui-header-left">
	    <a class="back"></a>
	</div>
	<div class="title">收货地址</div> 
	<div class="fui-header-right">&nbsp;</div>
    </div>
    <div class='fui-content navbar' >

	
	<div class='content-empty' <?php  if(!empty($list)) { ?>style='display:none'<?php  } ?>>
	    <!--<i class='icon icon-location'></i>-->
	    <!--<br/>您还没有任何收货地址-->
		<img src="<?php echo EWEI_SHOPV2_STATIC;?>images/noadd.png" style="width: 6rem;margin-bottom: .5rem;"><br/><p style="color: #999;font-size: .75rem">您暂时没有任何收货地址哦！</p>
	</div>
	
	<?php  if(is_array($list)) { foreach($list as $address) { ?>
	<div class="fui-list-group address-item last-line" style='margin-top:0px;'
	     data-addressid="<?php  echo $address['id'];?>">
	    <div  class="fui-list" >
			<!--<div class="fui-list-media">-->
			<!--		<?php  if($address['isdefault']) { ?>-->
			<!--		<img data-toggle='setdefault'  src="/addons/ewei_shopv2/static/images/member/default.png" alt="">-->
			<!--		<?php  } else { ?>-->
			<!--		<img data-toggle='setdefault'  src="/addons/ewei_shopv2/static/images/member/default2.png" alt="">-->
			<!--		<?php  } ?>-->
				
			<!--</div>-->
		<div class="fui-list-inner">
		    <div class="title">
				<div>
						<span class='realname'><?php  echo $address['realname'];?></span> 
						<span class='mobile'><?php  echo $address['mobile'];?></span>
				</div>
				<div>
						
						<span class='default give' style="<?php  if(!$address['isdefault']) { ?>display: none;<?php  } ?>">默认</span>
					
						
				</div>

			</div>
		    <div class="text">
			<span class='address'><?php  echo $address['province'];?><?php  echo $address['city'];?><?php  echo $address['area'];?><?php  if(!empty($new_area) && !empty($address_street)) { ?> <?php  echo $address['street'];?><?php  } ?> <?php  echo $address['address'];?></span>
		    </div>
		    <!-- <div class='bar' >
			<span class='pull-right'>
			    <a class="external" href="<?php  echo mobileUrl('member/address/post',array('id'=>$address['id']))?>" data-nocache="true">
				<i class='icon icon-edit2'></i> 编辑
			    </a>
			    &nbsp;&nbsp;
			    <a data-toggle='delete' class='external'>
				<i class='icon icon-delete'></i> 删除
			    </a>
			</span>

			<label class='radio-inline'>
			    <input type="radio" name='setdefault' data-toggle='setdefault'  class="fui-radio  fui-radio-danger" <?php  if($address['isdefault']) { ?>checked<?php  } ?> /> 设置默认
			</label>
		    </div> -->
		</div>
		<div class="fui-list-angle">
			<a href="<?php  echo mobileUrl('member/address/post',array('id'=>$address['id']))?>" data-nocache="true" style="font-size: 0;">
			<img src="/addons/ewei_shopv2/static/images/member/edit.png" alt="">
		</a>
		</div>
	    </div>
	</div> 
	<?php  } } ?>

    </div>
    <div class='btn-fixed'>
	<a href="<?php  echo mobileUrl('member/address/post')?>" class='btn  btn-danger external block new-address'  data-nocache="true">
		<!-- <i class="icon icon-add"></i> -->
		新建收货地址
	</a>
    </div>
    <script id="tpl_address_item" type="text/html">
	<div class="fui-list-group address-item" style='margin-top:5px;' data-addressid="<%address.id%>">
	    <div  class="fui-list" >
		<div class="fui-list-inner">
		    <div class="title"><span class='realname'><%address.realname%></span> <span class='mobile'><%address.mobile%></span></div>
		    <div class="text">
			<span class='address'><%address.areas%> <%address.address%></span>
		    </div>
		    <div class='bar' >
			<span class='pull-right'>
			    <a class="external" href="<?php  echo mobileUrl('member/address/post')?>&id=<%address.id%>" data-nocache='true'>
				<i class='icon icon-edit'></i> 编辑 
			    </a>
			    &nbsp;&nbsp;
			    <a data-toggle='delete' class='external'>
				<i class='icon icon-delete'></i> 删除
			    </a>
			</span>

			<label class='radio-inline'>
			    <input type="radio" data-toggle='setdefault' class="fui-radio  fui-radio-danger" <%if address.isdefault==1%>checked<%/if%> /> 设置默认
			</label>
		    </div>
		</div>
	    </div>
	</div> 
   </script>
    <script language='javascript'>require(['biz/member/address'], function (modal) {
                    modal.initList();
                });</script>
</div> 
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>
<!--efwww_com-->