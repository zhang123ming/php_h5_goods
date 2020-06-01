<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
	<div class="">
		<ol class="breadcrumb we7-breadcrumb">
			<a href="<?php  echo url('platform/qr/list');?>"><i class="wi wi-back-circle"></i> </a>
			<li><a href="<?php  echo url('platform/qr/list');?>">二维码列表</a></li>
			<li><a href="<?php  echo url('platform/qr/post');?>"><?php  if(empty($id)) { ?>新建<?php  } else { ?>编辑<?php  } ?>二维码</a></li>
		</ol>
		<div class="we7-form" id="js-qr-post" ng-controller="QrPost" ng-cloak>
			<form action="<?php  echo url('platform/qr/post')?>" method="post" id="form1">
				<input type="hidden" name="id" value="<?php  echo $row['id'];?>" />
				<input type="hidden" name="acid" value="<?php  echo $row['acid'];?>" />
				<div class="form-group">
					<label for="" class="control-label col-sm-2">二维码名称</label>
					<div class="form-controls col-sm-8">
						<input type="text" id="scene-name" class="form-control" name="scene-name" value="<?php  echo $row['name'];?>" />
					</div>
				</div>
				<?php  if(empty($id)) { ?>
				<div class="form-group">
					<label for="" class="control-label col-sm-2">二维码类型</label>
					<div class="form-controls col-sm-8">
						<div class="form-inline clearfix">
							<select name="qrc-model" class="we7-select col-sm-2">
								<option value="1">临时</option>
								<option value="2">永久</option>
							</select>
							<div class="col-sm-10" ng-show="type == 1">
								<label for="" class="control-label col-sm-2">过期时间</label>
								<div class="input-group col-sm-4">
									<input type="text" id="expire-seconds" class="form-control" name="expire-seconds" value="2592000" />
									<span class="input-group-addon">秒</span>
								</div>
								<span class="help-block color-gray">临时二维码过期时间, 最大为30天（2592000秒）.</span>
							</div>
							<div class="col-sm-10" ng-show="type == 2">
								<label for="" class="control-label col-sm-2">场景值</label>
								<input type="text" class="form-control col-sm-4" placeholder="场景值" id="scene_str" name="scene_str"/>
								<span class="help-block color-gray">场景值不能为空,并且只能为字符串.</span>
							</div>
						</div>
						<span class="help-block clearfix">
							目前有2种类型的二维码, 分别是临时二维码和永久二维码, 前者有过期时间, 最大为30天（2592000秒）, 但能够生成较多数
							量, 后者无过期时间, 数量较少(目前参数只支持1--100000).
						</span>
					</div>
				</div>
				<?php  } ?>
				<?php  echo module_build_form('core', $rid, array('keyword' => false))?>
				<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
				<input type="hidden" name="submit" value="submit">
				<button class="btn btn-primary col-lg-1 submit">提交</button>
			</form>
		</div>
	</div>
<script type="text/javascript">
require(['underscore'], function(){
	angular.module('qrApp').value('config', {
		id: <?php  echo json_encode($id)?>,
	});
	angular.bootstrap($('#js-qr-post'), ['qrApp']);
});
</script>
<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>