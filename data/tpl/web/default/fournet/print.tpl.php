<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<ul class="nav nav-tabs">
	<li<?php  if($do=='printlist') { ?> class="active"<?php  } ?>><a href="<?php  echo url('fournet/print/printlist')?>">打印机列表</a></li>
	<li<?php  if($do=='print') { ?> class="active"<?php  } ?>><a href="<?php  echo url('fournet/print/print')?>">添加打印机</a></li>
	<li<?php  if($do=='list') { ?> class="active"<?php  } ?>><a href="<?php  echo url('fournet/print/list')?>">打印模版列表</a></li>
	<li<?php  if($do=='post') { ?> class="active"<?php  } ?>><a href="<?php  echo url('fournet/print/post')?>">添加打印模版</a></li>
	<li<?php  if($do=='printep') { ?> class="active"<?php  } ?>><a href="<?php  echo url('fournet/print/printep')?>">模版市场</a></li>
	<li<?php  if($do=='cs') { ?> class="active"<?php  } ?>><a href="<?php  echo url('fournet/print/cs')?>">打印机状态检测</a></li>
</ul>
<script type="text/javascript">
	<?php  if($notify['mail']['smtp']['type'] == 'custom') { ?>
		$("#smtp").show();
	<?php  } ?>
</script>
<div class="main">
<?php  if($do=='set') { ?>
	<form id="payform" action="<?php  echo url('fournet/print')?>" method="post" class="form-horizontal form">
	
		<div class="panel panel-default">
			<div class="panel-heading">
				打印机设置选项
			</div>
			<div class="panel-body">
                    <div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">打印机名称</label>
						<div class="col-sm-9 col-xs-12">
							<input type="text" name="name" class="form-control" value="<?php  echo $print['name'];?>" />
							<div class="help-block">打印机名称，用于区别云平台处理</div>
						</div>
					</div>
					<div class="form-group" id="account">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">打印机类型</label>
					<div class="col-sm-9 col-xs-12">
						<select name="type" class="form-control">
                            <option value="进云物联打印机" <?php  if($print['type']=='进云物联打印机') { ?>selected<?php  } ?>>进云物联打印机</option>
						</select>
					<div class="help-block">目前只支持进云物联打印机，网址：<a href="http://www.jinyunzn.com/">http://www.jinyunzn.com/</a></div>
					</div>
				</div>

				<div class="form-group" id="account">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">是否使用</label>
					<div class="col-sm-9 col-xs-12">
						<select name="use" class="form-control">
							<option value="0" <?php  if($print['use']==0) { ?>selected<?php  } ?>>使用中</option>
                            <option value="1" <?php  if($print['use']==1) { ?>selected<?php  } ?>>不使用</option>
						</select>
						<div class="help-block">是否开启打印机！</div>
					</div>
				</div>
                <div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">商户ID</label>
					<div class="col-sm-9 col-xs-12">
						<input type="text" name="num" class="form-control" value="<?php  echo $print['num'];?>" />
						<div class="help-block">打印机的商户Token</div>
					</div>
				</div>
                <div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">API密钥</label>
						<div class="col-sm-9 col-xs-12">
							<input type="text" name="appkey" class="form-control" value="<?php  echo $print['appkey'];?>" />
						<div class="help-block">打印机的API密匙</div>
						</div>
				</div>
                <div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">描述</label>
						<div class="col-sm-9 col-xs-12">
							<input type="text" name="dis" class="form-control" value="<?php  echo $print['dis'];?>" />
							
						</div>
				</div>
			</div>
			</div>
		<div class="form-group col-sm-12">
			<button type="submit" class="btn btn-primary col-lg-1" name="submit" value="提交">提交</button>
			<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
		</div>

	</form>
<?php  } ?>
	<?php  if($do=='print') { ?>
	<form id="payform" action="<?php  echo url('fournet/print/print')?>" method="post" class="form-horizontal form">
	
		<div class="panel panel-default">
			<div class="panel-heading">
				设置打印机参数
			</div>
			<div class="panel-body">
                    <div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">打印机名称</label>
						<div class="col-sm-9 col-xs-12">
							<input type="text" name="name" class="form-control" value="<?php  echo $print['name'];?>" />
							<div class="help-block">打印机名称，用于区别云平台处理</div>
						</div>
					</div>
					<div class="form-group" id="account">
					    <label class="col-xs-12 col-sm-3 col-md-2 control-label">打印机类型</label>
						<div class="col-sm-9 col-xs-12">
							<select name="type" class="form-control">
								<option value="1" <?php  if($print['type']=='1') { ?>selected<?php  } ?>>进云物联打印机</option>
							</select>
						<div class="help-block">目前只支持进云物联打印机，网址：<a href="http://www.jinyunzn.com/">http://www.jinyunzn.com/</a></div>
						</div>
				    </div>

				    <div class="form-group" id="account">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">适用模块</label>
						<div class="col-sm-9 col-xs-12">
							<div id="search-module" style="width:50%;">
								<input type="text" name="keyword" style="width:50%;" placeholder="输入关键词搜索模块"/>
							</div>
							<select name="module" class="form-control">
								<option value="">请选择适用模块</option>
								<?php  if(is_array($modules)) { foreach($modules as $module) { ?>
								<option class='mod' data-title=<?php  echo $module['title'];?> value="<?php  echo $module['name'];?>" <?php  if($printep['module']==$module['name']) { ?>selected<?php  } ?>><?php  echo $module['title'];?></option>
								<?php  } } ?>
							</select>
						<div class="help-block">选择模版适用的功能模块</div>
						</div>
				    </div>
				<div class="form-group" id="account">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">是否默认</label>
					<div class="col-sm-9 col-xs-12">
						<select name="isdefault" class="form-control">
							<option value="1" <?php  if($print['isdefault']==1) { ?>selected<?php  } ?>>默认</option>
                            <option value="0" <?php  if($print['isdefault']==0) { ?>selected<?php  } ?>>不默认</option>
						</select>
						<div class="help-block">是否将打印机设置为默认打印机！默认打印机将在模块未选择打印机的情况下执行模块的打印任务。</div>
					</div>
				</div>
                <div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">商户ID</label>
					<div class="col-sm-9 col-xs-12">
						<input type="text" name="tokend" class="form-control" value="<?php  echo $print['token'];?>" />
						<div class="help-block">打印机的商户Token,从打印机云平台获取</div>
					</div>
				</div>
                <div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">API密钥</label>
						<div class="col-sm-9 col-xs-12">
							<input type="text" name="apikey" class="form-control" value="<?php  echo $print['apikey'];?>" />
						<div class="help-block">打印机的API密匙，从打印机云平台获取</div>
						</div>
				</div>
				
				<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">打印机序列号</label>
						<div class="col-sm-9 col-xs-12">
							<input type="text" name="dtuid" class="form-control" value="<?php  echo $print['dtuid'];?>" placeholder="序列号|终端号"/>
						<div class="help-block">打印机的机身参数，从打印机底部获取</div>
						</div>
				</div>
				<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">打印机设备编码</label>
						<div class="col-sm-9 col-xs-12">
							<input type="text" name="imei" class="form-control" value="<?php  echo $print['imei'];?>" placeholder="SN码|机器码|IMEI码"/>
						<div class="help-block">打印机的机身参数，从打印机底部获取</div>
						</div>
				</div>
                <div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">头部自定义</label>
						<div class="col-sm-9 col-xs-12">
							<input type="text" name="top" class="form-control" value="<?php  echo $print['top'];?>" />
						<div class="help-block">打印内容的头部信息</div>	
						</div>
				</div>
				<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">底部自定义</label>
						<div class="col-sm-9 col-xs-12">
							<input type="text" name="bottom" class="form-control" value="<?php  echo $print['bottom'];?>" />
						<div class="help-block">打印内容的底部信息</div>	
						</div>
				</div>
			</div>
		</div>
		<div class="form-group col-sm-12">
			<button type="submit" class="btn btn-primary col-lg-1" name="submit" value="提交">提交</button>
			<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
			<input type="hidden" name="id" value="<?php  echo $print['id'];?>" />
		</div>

	</form>
	<?php  } ?>
	<?php  if($do=='post') { ?>
	<form id="payform" action="<?php  echo url('fournet/print/post')?>" method="post" class="form-horizontal form">
	
		<div class="panel panel-default">
			<div class="panel-heading">
				设置模版信息
			</div>
			<div class="panel-body">
                    <div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">模版名称</label>
						<div class="col-sm-9 col-xs-12">
							<input type="text" name="name" class="form-control" value="<?php  echo $printep['name'];?>" />
							<div class="help-block">模版名称</div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">模版ID</label>
						<div class="col-sm-9 col-xs-12">
							<input type="text" name="tepid" class="form-control" value="<?php  echo $printep['tepid'];?>" readonly="readonly"/>
							<div class="help-block">模版ID在新建模版后会自动生成，生成后不支持修改。</div>
						</div>
					</div>
					<div class="form-group" id="account">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">适用模块</label>
						<div class="col-sm-9 col-xs-12">
							<div id="search-module" style="width:50%;">
								<input type="text" name="keyword" style="width:50%;" placeholder="输入关键词搜索模块"/>
							</div>
							<select name="module" class="form-control">
								<option value="">请选择适用模块</option>
								<?php  if(is_array($modules)) { foreach($modules as $module) { ?>
								<option class='mod' data-title=<?php  echo $module['title'];?> value="<?php  echo $module['name'];?>" <?php  if($printep['module']==$module['name']) { ?>selected<?php  } ?>><?php  echo $module['title'];?></option>
								<?php  } } ?>
							</select>
						<div class="help-block">选择模版适用的功能模块</div>
						</div>
				    </div>

				<div class="form-group" id="account">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">是否默认</label>
					<div class="col-sm-9 col-xs-12">
						<select name="defaul" class="form-control">
							<option value="1" <?php  if($printep['defaul']==1) { ?>selected<?php  } ?>>默认</option>
                            <option value="0" <?php  if($printep['defaul']==0) { ?>selected<?php  } ?>>不作为默认模版</option>
						</select>
						<div class="help-block">是否设置为相应模块的默认模版！</div>
					</div>
				</div>
                <div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">模版内容</label>
					<div class="col-sm-9 col-xs-12">
						<textarea style="height:200px;" class="form-control" id="content" name="content" cols="70"><?php  echo $printep['content'];?></textarea>
						<div class="help-block">模版内容，变量请以{}括起来，例如{data}</div>
					</div>
				</div>
                
			</div>
		</div>
		<div class="form-group col-sm-12">
			<button type="submit" class="btn btn-primary col-lg-1" name="post" value="提交">提交</button>
			<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
		</div>

	</form>
	<?php  } ?>
	<?php  if($do=='list') { ?>
<div class="panel panel-info">
	<div class="panel-heading">筛选</div>
	<div class="panel-body">
		<!-- 搜索应用 -->
		<form action="./index.php" method="get" class="form-horizontal" role="form">
		<input type="hidden" name="c" value="fournet" />
		<input type="hidden" name="a" value="print" />
		<input type="hidden" name="do" value="list" />
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">模版名称</label>
				<div class="col-sm-6 col-md-8 col-lg-8">
					<input type="text" class="form-control" name="name" value="<?php  echo $_GPC['title'];?>" />
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">适用模块</label>
				<div class="col-sm-6 col-md-8 col-lg-8">
					<div id="search-module" style="width:50%;">
						<input type="text" name="keyword" style="width:50%;" placeholder="输入关键词搜索模块"/>
					</div>
					<select name="module" class="form-control">
						<option value="" selected="selected">不限</option>
						<?php  if(is_array($modules)) { foreach($modules as $m) { ?>
						<option class='mod' data-title=<?php  echo $m['title'];?> value="<?php  echo $m['name'];?>"><?php  echo $m['title'];?></option>
						<?php  } } ?>
					</select>
				</div>
				<div class="pull-right col-xs-12 col-sm-3 col-md-2 col-lg-2">
					<button class="btn btn-default"><i class="fa fa-search"></i> 搜索</button>
				</div>
			</div>
		</form>
	</div>
</div>
<div class="clearfix table-responsive">
	<table class="table table-hover">
	<form method="post" class="form-horizontal" id="form1">
		<input type="hidden" name="do" value="list" />
		<thead class="navbar-inner">
			<tr>
				<th style="width:100px;">删除模版</th>
				<th style="width:100px;">模版编号</th>
				<th style="width:200px;">模版名称</th>
				<th style="width:300px;">模版ID</th>
				<th style="width:150px;">适用模块</th>
				<th style="width:100px;">编辑</th>
				<th style="width:100px;">删除</th>
			</tr>
		</thead>
		<?php  if(is_array($list)) { foreach($list as $item) { ?>
			<tr>
				<td><input type="checkbox" name="ids[]" value="<?php  echo $item['id'];?>"></td>
				<td><?php  echo $item['id'];?></td>
				<td><?php  echo $item['name'];?></td>
				<td><?php  echo $item['tepid'];?></td>
				<td><?php  echo $modules[$item['module']]['title'];?></td>
				<td><a href="<?php  echo url('fournet/print/post', array('id' => $item['id']));?>" class="btn btn-default">编辑</a></td>
				<td>
					<a href="<?php  echo url('fournet/print/list', array('id' => $item['id']));?>" onclick="return confirm('此操作不可恢复，确认删除？');" 
						data-toggle="tooltip" data-placement="top" title="" class="btn btn-default btn-sm" data-original-title="删除"><i class="fa fa-times"></i>
					</a>
				</td>
			</tr>
		<?php  } } ?>
		<tr>
			<td>
				<input type="checkbox" name="" onclick="var ck = this.checked;$(':checkbox').each(function(){this.checked = ck});">
			</td>
			<td colspan="8">
				<input type="submit" name="submit" class="btn btn-primary" value="删除">
				<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
			</td>
		</tr>
	</form>
	</table>
	<!-- 分页导航 -->
	<?php  echo $pager;?>
</div>
<script>
require(['jquery', 'util'], function($, u){
	$('#form1').submit(function(){
		if($(":checkbox[name='ids[]']:checked").size() > 0){
			return confirm('删除后不可恢复，您确定删除吗？');
		}
		u.message('没有选择应用', '', 'error');
		return false;
	});
	$('.btn').hover(function(){
		$(this).tooltip('show');
	},function(){
		$(this).tooltip('hide');
	});
});
</script>
	<?php  } ?>
	<?php  if($do=='printlist') { ?>
	
<div class="clearfix table-responsive">
	<table class="table table-hover">
	<form method="post" class="form-horizontal" id="form1">
		<input type="hidden" name="do" value="printlist" />
		<thead class="navbar-inner">
			<tr>
				<th style="width:100px;">删除打印机</th>
				<th style="width:100px;">打印机编号</th>
				<th style="width:550px;">打印机名称</th>
				
				<th style="width:150px;">适用模块</th>
				<th style="width:100px;">编辑</th>
				<th style="width:100px;">删除</th>
			</tr>
		</thead>
		<?php  if(is_array($list)) { foreach($list as $item) { ?>
			<tr>
				<td><input type="checkbox" name="ids[]" value="<?php  echo $item['id'];?>"></td>
				<td><?php  echo $item['id'];?></td>
				<td><?php  echo $item['name'];?></td>
				<td><?php  echo $modules[$item['module']]['title'];?></td>
				<td><a href="<?php  echo url('fournet/print/print', array('id' => $item['id']));?>" class="btn btn-default">编辑</a></td>
				<td>
					<a href="<?php  echo url('fournet/print/printlist', array('id' => $item['id']));?>" onclick="return confirm('此操作不可恢复，确认删除？');" 
						data-toggle="tooltip" data-placement="top" title="" class="btn btn-default btn-sm" data-original-title="删除"><i class="fa fa-times"></i>
					</a>
				</td>
			</tr>
		<?php  } } ?>
		<tr>
			<td>
				<input type="checkbox" name="" onclick="var ck = this.checked;$(':checkbox').each(function(){this.checked = ck});">
			</td>
			<td colspan="8">
				<input type="submit" name="submit" class="btn btn-primary" value="删除">
				<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
			</td>
		</tr>
	</form>
	</table>
	<!-- 分页导航 -->
	<?php  echo $pager;?>
</div>
<script>
require(['jquery', 'util'], function($, u){
	$('#form1').submit(function(){
		if($(":checkbox[name='ids[]']:checked").size() > 0){
			return confirm('删除后不可恢复，您确定删除吗？');
		}
		u.message('没有选择应用', '', 'error');
		return false;
	});
	$('.btn').hover(function(){
		$(this).tooltip('show');
	},function(){
		$(this).tooltip('hide');
	});
});
</script>
	<?php  } ?>
	<?php  if($do=='printep') { ?>
<div class="panel panel-info">
	<div class="panel-heading">筛选</div>
	<div class="panel-body">
		<!-- 搜索应用 -->
		<form action="./index.php" method="get" class="form-horizontal" role="form">
		<input type="hidden" name="c" value="fournet" />
		<input type="hidden" name="a" value="print" />
		<input type="hidden" name="do" value="list" />
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">模版名称</label>
				<div class="col-sm-6 col-md-8 col-lg-8">
					<input type="text" class="form-control" name="name" value="<?php  echo $_GPC['title'];?>" />
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">适用模块</label>
				<div class="col-sm-6 col-md-8 col-lg-8">
					<div id="search-module" style="width:50%;">
						<input type="text" name="keyword" style="width:50%;" placeholder="输入关键词搜索模块"/>
					</div>
					<select name="module" class="form-control">
						<option value="" selected="selected">不限</option>
						<?php  if(is_array($modules)) { foreach($modules as $m) { ?>
						<option class='mod' data-title=<?php  echo $m['title'];?> value="<?php  echo $m['name'];?>"><?php  echo $m['title'];?></option>
						<?php  } } ?>
					</select>
				</div>
				<div class="pull-right col-xs-12 col-sm-3 col-md-2 col-lg-2">
					<button class="btn btn-default"><i class="fa fa-search"></i> 搜索</button>
				</div>
			</div>
		</form>
	</div>
</div>
<div class="clearfix table-responsive">
	<table class="table table-hover">
	<form method="post" class="form-horizontal" id="form1">
		<input type="hidden" name="do" value="del" />
		<thead class="navbar-inner">
			<tr>
				<th style="width:100px;">模版编号</th>
				<th style="width:100px;">模版名称</th>
				<th style="width:200px;">模版ID</th>
				<th style="width:100px;">适用模块</th>
				<th style="width:250px;">模版内容</th>
				<th style="width:100px;">安装模板</th>
			</tr>
		</thead>
		<?php  if(is_array($list)) { foreach($list as $item) { ?>
			<tr>
				<td><?php  echo $item['id'];?></td>
				<td><?php  echo $item['name'];?></td>
				<td><?php  echo $item['tepid'];?></td>
				<td><?php  echo $modules[$item['module']]['title'];?></td>
				<td><label data="<?php  echo $item['id'];?>" content="<?php  echo base64_decode($item['content']);?>" class='dianji label  label-default label-info'>点击查看</label>
					<div id="<?php  echo $item['id'];?>" data="1" style="width:250px;height:300px;display:none;">
					
					</div>
				</td>
				<td>
				<a href="<?php  echo url('fournet/print/printep', array('id' => $item['id']));?>" class="btn btn-default" style="color:red;"><?php  if(!empty($bendi[$item['tepid']])) { ?>更新<?php  } else { ?>安装<?php  } ?></a>
				</td>
			</tr>
		<?php  } } ?>
	</form>
	</table>
	<!-- 分页导航 -->
	<?php  echo $pager;?>
</div>

	<?php  } ?>
	<?php  if($do=='cs') { ?>
	<div class="alert alert-success alert-dismissable">
				<i class="fa fa-bullhorn alert-link"></i> 打印机设置教程 --
				<a href="http://help.xxxxx.com/index.php?category-view-21" target="_blank" style="border-radius: 4px;width: 84%;padding: 8px;font-size: 14px;background-color: #00bc0c;border-color: #00bc0c;color: #FFF">相关详细教程</a>
	</div>
	<form id="payform" action="<?php  echo url('fournet/print/cs')?>" method="post" class="form-horizontal form">	
	<div class="panel panel-default">
			<div class="panel-heading">
				打印机状态检测
			</div>
			<div class="panel-body">
			<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 control-label">选择打印机</label>
					<div class="col-sm-5 col-xs-12">
						<select class="form-control" name="printid" id="printid" autocomplete="off">
                            <?php  if(is_array($printers)) { foreach($printers as $p) { ?> 
							<option value="<?php  echo $p['id'];?>" <?php  if($item['printid']==$p['id']) { ?>selected<?php  } ?>><?php  echo $p['name'];?></option>
							<?php  } } ?>
                        </select>
					</div>
			</div>
			</div>
	</div>
		<div class="form-group col-sm-12">
			<button type="submit" class="btn btn-primary col-lg-1" name="submit" value="提交">点击测试</button>
			<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
		</div>
		
	</form>
		
		</div>
	<?php  } ?>
</div>
<script type="text/javascript">
require(['bootstrap'],function(){
			$('#search-module input').keyup(function() {
				var a = $(this).val();
				$('.mod').hide();
				$('.mod').each(function() {
					if(a.length > 0 && $(this).attr('data-title').indexOf(a) >= 0) {
					$(this).show();
					}
				});
				if(a.length ==0) {
					$('.iteme').show();
				}
			});
			$(".dianji").click(function(){
				var cid=$(this).attr("data");
				var content=$(this).attr("content");
				content='<textarea style="height:300px;" class="form-control" cols="70">'+content+'</textarea>';
				var yes=$('#'+cid).attr("data");
				if(yes==1){
					$('#'+cid).html(content);
					$('#'+cid).attr("data",0);
					$('#'+cid).show();
				}else{
					$('#'+cid).attr("data",1);
					$('#'+cid).hide();
				}
				
			});
		})
</script>
<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>
