<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>
<div class='fui-page  fui-page-current'>
    <div class="fui-header">
		<div class="fui-header-left">
			<a class="back" onclick='location.back()'></a>
		</div>
		<div class="title">头像/昵称</div>
		<div class="fui-header-right">&nbsp;</div>
	</div>

	<div class='fui-content'>
		<style type="text/css">
			.fui-list-inner {font-size: 0.75rem;}
			.fui-cell-group .fui-cell {padding: 0.7rem 0.5rem;}
			.fui-cell-group .fui-cell .fui-cell-label {padding-left: 0;}
			.fui-list-media img {width: 2.5rem; height: 2.5rem;}
			#file-avatar {opacity: 0; position: absolute; top:0; left: 0; bottom: 0; right: 0; z-index: 9; background: red; width: 100%;}
		</style>
			<div class="fui-list-group">			
				<div class="fui-list">
					<div class="fui-cell-label">会员ID:</div>
					<div class="fui-input">
						<?php if(cv('member.list.edit')) { ?>
						<?php  echo tpl_form_field_editor(array('name'=>'data[id]', 'value'=>$member['id']))?>
						<?php  } else { ?>
							<div class='form-control-static'><?php  echo $member['id'];?></div>
						<?php  } ?>
					</div>
				</div>
			</div>	

			<div class="fui-list-group">
				<input type="file" name="file-avatar" id="file-avatar" />
				<div class="fui-list" id="btn-avatar">
					<div class="fui-list-inner">修改头像</div>
					<div class="fui-list-media" style="margin-right:0px;">
						<img src="<?php  echo $member['avatar'];?>" id="avatar"
							 data-wechat="<?php  echo $member['avatar_wechat'];?>"
							 data-filename="<?php  echo $member['avatar'];?>"
							 onerror="this.src='../addons/ewei_shopv2/static/images/noface.png';" />
					</div>
				</div>
			</div>

			<div class="fui-cell-group">
				<div class="fui-cell must ">
					<div class="fui-cell-label ">修改昵称</div>
					<div class="fui-cell-info"><input type="text" class='fui-input' id='nickname' data-wechat="<?php  echo $member['nickname_wechat'];?>" placeholder="请输入您的昵称"  value="<?php  echo $member['nickname'];?>" /></div>
				</div>
			</div>

		<div style="margin-top:20px;">
			<?php  if(is_weixin()) { ?>
				<div id='btn-getinfo' class='btn btn-success-o block'>读取微信资料</div>
			<?php  } ?>
			<div id='btn-submit' class='btn btn-success block'>保存</div>
		</div>
	</div>
	<script language='javascript'>
		require(['biz/member/info'], function (modal) {
		  	modal.initFace({});
		  	modal.initPost({new_area: <?php  echo $new_area?>, address_street: <?php  echo $address_street?>});
		});
	</script>
	<script language='javascript' type="text/javascript">

        // require(['biz/member/address'], function (modal) {
            // modal.initPost({new_area: <?php  echo $new_area?>, address_street: <?php  echo $address_street?>});
        // });
    </script>
    

</div>
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>


<!--efwww_com-->