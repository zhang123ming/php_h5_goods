<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<div class="clearfix">
	<ul class="nav nav-tabs">
		<li <?php  if($do=='site_setting') { ?> class="active"<?php  } ?>><a href="<?php  echo url('system/site_setting');?>"><i class="fa fa-edit"></i> 站点信息</a></li>
	</ul>
		<div class="form-group"></div>
	<form action="" method="post" class="form-horizontal form" onsubmit="return formcheck(this)">
		<div class="panel panel-default">
			<div class="panel-heading">
				站点资料
			</div>
			<div class="panel-body">
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">站点ID</label>
					<div class="col-sm-10 col-md-10 col-lg-10">
						<input type="text" name="key" class="form-control" value="<?php  echo $site['key'];?>">
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">通信密钥</label>
					<div class="col-sm-10 col-lg-10">
						<input type="text" name="password" class="form-control" value="<?php  echo $site['password'];?>">
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">网站名称</label>
					<div class="col-sm-10 col-lg-10">
						<input type="text" name="sitename" value="<?php  echo $site['sitename'];?>" class="form-control">
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">网站URL</label>
					<div class="col-sm-10 col-lg-10">
						<input type="text"  value="<?php  echo $site['siteurl'];?>" class="form-control" name="siteurl">
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">网站IP</label>
					<div class="col-sm-10 col-lg-10">
						<input type="text" name="ip" value="<?php  echo $site['ip'];?>" class="form-control">
					</div>
				</div>
	
			</div>
		</div>
		<div class="form-group col-sm-12">
			<button type="submit" class="btn btn-primary col-lg-1 " name="submit" value="提交">提交</button>
		</div>
	</form>
	
	</div>
<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>