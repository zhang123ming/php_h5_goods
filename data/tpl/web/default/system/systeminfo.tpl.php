<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<div class="we7-page-title">系统信息</div>
<ul class="we7-page-tab"></ul>
<div class="main">
		
	<table class="table we7-table table-hover site-list">
		<col widtd="120px" />
		<col widtd="120px" />
		<tr>
			<th colspan="2" class="text-left">用户信息</th>
		</tr>
		<tr>
			<td class="text-left">用户ID</td>
			<td class="text-left"><?php  echo $info['uid'];?></td>
		</tr>
		<tr>
			<td class="text-left">当前公众号</td>
			<td class="text-left"><?php  echo $info['account'];?></td>
		</tr>
	</table>
	
	<table class="table we7-table table-hover site-list">
		<tr>
			<th colspan="2" class="text-left">系统信息</th>
		</tr>
		<tr>
			<td class="text-left">系统程序版本</td>
			<td class="text-left">WeEngine <?php  echo IMS_VERSION;?> Release <?php  echo IMS_RELEASE_DATE;?> &nbsp; &nbsp;

			</td>
		</tr>
		<tr>
			<td class="text-left">产品系列</td>
			<td class="text-left">
				<?php  if(IMS_FAMILY == 'v') { ?>
				您的产品是商业版
				<?php  } else if(IMS_FAMILY == 's') { ?>
				您的产品是商业版
				<?php  } else if(IMS_FAMILY == 'x') { ?>
				您的产品是商业版
				<?php  } else { ?>
				您的产品是商业版
				<?php  } ?>
			</td>
		</tr>
		<tr>
			<td class="text-left">服务器系统</td>
			<td class="text-left"><?php  echo $info['os'];?></td>
		</tr>
		<tr>
			<td class="text-left">PHP版本 </td>
			<td class="text-left">PHP Version <?php  echo $info['php'];?></td>
		</tr>
		<tr>
			<td class="text-left">服务器软件</td>
			<td class="text-left"><?php  echo $info['sapi'];?></td>
		</tr>
		<tr>
			<td class="text-left">服务器 MySQL 版本</td>
			<td class="text-left"><?php  echo $info['mysql']['version'];?></td>
		</tr>
		<tr>
			<td class="text-left">上传许可</td>
			<td class="text-left"><?php  echo $info['limit'];?></td>
		</tr>
		<tr>
			<td class="text-left">当前数据库尺寸</td>
			<td class="text-left"><?php  echo $info['mysql']['size'];?></td>
		</tr>
		<tr>
			<td class="text-left">当前附件根目录</td>
			<td class="text-left"><?php  echo $info['attach']['url'];?></td>
		</tr>
		<tr>
			<td class="text-left">当前附件尺寸</td>
			<td class="text-left"><?php  echo $info['attach']['size'];?></td>
		</tr>
	</table>
</div>
<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>
