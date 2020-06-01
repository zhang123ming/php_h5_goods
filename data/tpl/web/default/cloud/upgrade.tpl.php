<?php defined('IN_IA') or exit('Access Denied');?><style type="text/css">
<!--
.STYLE1 {color: #0033CC}
-->
</style>
<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<div class="upgrade-content">
	
	<?php  if(!empty($upgrade) && !empty($upgrade['upgrade'])) { ?>
	<form action="" class="form we7-form">
		<div class="upgrade-info we7-padding-bottom">
			<div class="panel we7-panel">
				<div class="panel-heading we7-padding">
					<span class="col-sm-2 we7-padding-none color-gray">最新版本</span>
					<span class="col-sm-7 we7-padding-none">系统 <?php  echo $upgrade['family'];?><?php  echo $upgrade['version'];?>【<?php  echo $upgrade['release'];?>】版</span>
				</div>
				<div class="panel-body we7-padding">
					<div class="form-group">
						<label for="" class="control-label color-gray col-sm-2">需要更新文件</label>
						<div class="form-controls col-sm-7 form-control-static"><?php  echo count($upgrade['files'])?> 个</div>
						<span class="color-default col-sm-3 text-right"><a href="#upgrade-file" data-toggle="modal" >查看</a></span>
					</div>
					<div class="form-group">
						<label for="" class="control-label color-gray col-sm-2">需要更新数据库</label>
						<div class="form-controls col-sm-7 form-control-static"><?php  echo count($upgrade['database'])?> 项</div>
						<span class="color-default col-sm-3 text-right"><a href="#upgrade-databases" data-toggle="modal" >查看</a></span>
					</div>
					<div class="form-group  we7-padding-none">
						<label for="" class="control-label color-gray col-sm-2">更新协议事项</label>
						<div class="form-controls col-sm-10 form-control-static">
							<div class="">
								<input id='agreement_0' type="checkbox" name='agreement_0' autocomplete="off" />
								<label for="agreement_0">确保您的系统文件酷微米云端文件保持一致，避免被非法篡改！</label>
							</div>
							<div class="">
								<input id='agreement_1' type="checkbox" name='agreement_1' autocomplete="off"/>
								<label for="agreement_1">已经做好了相关文件的备份工作，认同酷微米的更新行为并自愿承担更新所存在的风险！</label>
							</div>
							<div class="">
								<input id='agreement_2' type="checkbox" name='agreement_2' autocomplete="off"/>
								<label for="agreement_2">如果使用过程中遇到问题请到<a href="https://www.kuweimi.com/" target="_blank" class="STYLE1">酷微米</a>发帖交流！</label>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="text-center">
				<input type="button" name="" id="forward" value="一键更新" class="btn btn-danger" />
				<input name="rollback" type="button" value="撤回更新" class="btn btn-default" data-toggle="modal" data-target="#rollback-panel" />
			</div>
		</div>
	</form>
	<?php  } else { ?>
	<form action="" method="post">
		<div class="upgrade-info we7-padding-bottom">
			<div class="panel we7-panel">
				<div class="panel-heading we7-padding">
					<span class="we7-padding-none color-gray">当前版本为最新版本，您可以点击此按钮, 立即检查是否有新版本。</span>
				</div>
			</div>
			<div class="text-center">
				<input name="submit" type="submit" value="立即检查新版本" class="btn btn-danger" />
				<input name="rollback" type="button" value="撤回更新" class="btn btn-default" data-toggle="modal" data-target="#rollback-panel" />
				<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
			</div>
		</div>
	</form>
	<?php  } ?>
	<div class="panel we7-panel">
		<div class="panel-heading">
			更新通知
		</div>
		<div class="panel-body we7-padding">
			<ul class="list-unstyled">
			<script type="text/javascript" src="https://www.kuweimi.com/api.php?mod=js&bid=58"></script>
			</ul>
		</div>
	</div>
	<div class="panel we7-panel">
		<div class="panel-heading">
			功能模块
		</div>
		<div class="panel-body we7-padding">
			<ul class="list-unstyled">
			<script type="text/javascript" src="https://www.kuweimi.com/api.php?mod=js&bid=57"></script>
			</ul>
		</div>
	</div>
</div>
<div class="modal fade" id="upgrade-file" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog we7-modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">
					<span aria-hidden="true">&times;</span>
					<span class="sr-only">Close</span>
				</button>
				<h4 class="modal-title">更新文件</h4>
			</div>
			<div class="modal-body color-dark">
				<?php  if(is_array($upgrade['files'])) { foreach($upgrade['files'] as $line) { ?>
				<div><span style="display:inline-block; width:30px;"><?php  if(is_file(IA_ROOT . $line)) { ?>M<?php  } else { ?>A<?php  } ?></span><?php  echo $line;?></div>
				<?php  } } ?>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary"  data-dismiss="modal">确定</button>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="upgrade-databases" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog we7-modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">
					<span aria-hidden="true">&times;</span>
					<span class="sr-only">Close</span>
				</button>
				<h4 class="modal-title">更新数据库</h4>
			</div>
			<div class="modal-body color-dark">
				<?php  if(is_array($upgrade['database'])) { foreach($upgrade['database'] as $line) { ?>
				<div class="row">
					<div class="col-sm-2">表名:</div>
					<div class="col-sm-4"><?php  echo $line['tablename'];?></div>
					<?php  if(!empty($line['new'])) { ?>
					<div class="col-sm-6">New</div>
					<?php  } else { ?>
					<div class="col-sm-6"><?php  if(!empty($line['fields'])) { ?>fields: <?php  echo $line['fields'];?>; <?php  } ?><?php  if(!empty($line['indexes'])) { ?>indexes: <?php  echo $line['indexes'];?><?php  } ?></div>
					<?php  } ?>
				</div>
				<?php  } } ?>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" data-dismiss="modal">确定</button>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="rollback-panel" tabindex="-1" role="dialog" aria-labelledby="rollback-label">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">更新回滚列表</h4>
			</div>
			<div class="modal-body">
				<div class="alert alert-danger">
					如果要恢复更早的记录请直接查看 <b>/data/patch/</b> 目录
				</div>
				<div class="alert alert-success">
					恢复时，请手动将此目录中的文件上传至网站即可（选中全部文件和目录直接上传）
				</div>
				<?php  if(!empty($patchs)) { ?>
				<table class="table">
					<tr>
						<th style="width: 200px">日期</th>
						<th >路径</th>
					</tr>
					<?php  if(is_array($patchs)) { foreach($patchs as $path) { ?>
					<tr>
						<td><?php  echo date('Y-m-d')?> <?php  echo substr($path, 0, 2)?>:<?php  echo substr($path, 2, 2)?></td>
						<td><?php  echo $path;?></td>
					</tr>
					<?php  } } ?>
				</table>
				<?php  } else { ?>
				今天暂无更新
				<?php  } ?>
			</div>
		</div>
	</div>
</div>
<?php  if(!empty($upgrade) && !empty($upgrade['upgrade'])) { ?>
<script type="text/javascript">
	$('#forward').click(function(){
		var a = $("#agreement_0").is(':checked');
		var b = $("#agreement_1").is(':checked');
		var c = $("#agreement_2").is(':checked');
		if(a && b && c) {
			if(confirm('更新将直接覆盖本地文件, 请注意备份文件和数据. \n\n**另注意** 更新过程中不要关闭此浏览器窗口.')) {
				location.href = '<?php  echo url("cloud/process");?>';
			}
		} else {
			util.message("抱歉，更新前请仔细阅读更新协议！", '', 'error');
			return false;
		}
	});
</script>
<?php  } ?>
<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>
