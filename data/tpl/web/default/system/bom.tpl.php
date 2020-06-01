<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<div class="we7-page-title">检测文件BOM</div>
<ul class="we7-page-tab"></ul>
<div class="main">

	<form action="" method="post" class="we7-form">
		<div class="form-group">
			<label class="control-label col-sm-2">操作说明</label>
			<div class=" alert alert-info col-sm-10">
				<p>系统系统使用utf-8无bom格式的文件编码方式, 如果使用编辑器修改配置或者查看文件时没有注意编辑器设置将可能在被编辑的文件上附加BOM头, 从而造成系统功能异常. </p>
				<p><strong>注意: 在公众平台添加API地址时重复错误时, 请尝试检测BOM异常. </strong></p>
				<p><strong>注意: 使用云平台功能时重复出现错误提示时, 请尝试检测BOM异常. </strong></p>
				<p><strong>注意: 使用 Windows 系统自带的记事本编辑系统源码可能会造成这样的问题. </strong></p>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-2">处理说明</label>
			<div class="alert alert-info col-sm-10">
				<p>为保证系统正常运行, 系统不会尝试修复检测出来的错误文件, 检测完成后请自行使用编辑器修改文件编码方式</p>
			</div>
		</div>
		
		<?php  if(isset($bomtree)) { ?>
		<div class="form-group">
			<label class="control-label col-sm-2">检测结果</label>
			<div class="col-sm-10">
			<?php  if(empty($bomtree)) { ?>
				<p><strong>没有检测到存在BOM的异常文件</strong></p>
			<?php  } else { ?>
					<div class="alert alert-info" style="line-height:20px;">
						<?php  if(is_array($bomtree)) { foreach($bomtree as $line) { ?>
						<div><?php  echo $line;?></div>
						<?php  } } ?>
						</div>

			<?php  } ?>
				<div class=" alert alert-warning ">为保证系统正常运行, 系统不会尝试修复检测出来的错误文件, 检测完成后请自行使用编辑器修改文件编码方式</div>
			</div>
		</div>
		<?php  } ?>
		<div class="form-group">
			<div class="col-sm-offset-2 col-sm-10">
				<input name="submit" type="submit" value="检测BOM异常" class="btn btn-primary span3" />
				<?php  if(isset($bomtree) && !empty($bomtree)) { ?><input name="dispose" type="submit" class="btn btn-info " value="处理BOM异常"/><?php  } ?>
				<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
			</div>
		</div>
	</form>
</div>
<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>
