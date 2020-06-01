<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<ol class="breadcrumb we7-breadcrumb">
	<a href="<?php  echo url('user/display');?>"><i class="wi wi-back-circle"></i> </a>
	<li><a href="<?php  echo url('user/display');?>">用户管理</a></li>
	<li>编辑用户详情</li>
</ol>
<div id="js-user-edit-modulestpl" ng-controller="UserEditModulesTpl" ng-cloak>
	<div class="user-head-info we7-padding-bottom" >
		<img ng-src="{{profile.avatar}}" class="img-circle user-avatar pull-left">
		<h3 class="pull-left" ng-bind="user.username"></h3>
		<div class="user-edit pull-right">
			<a href="<?php  echo url('user/display/recycle', array('uid' => $_GPC['uid']))?>" class="btn btn-primary">禁用</a>
		</div>
	</div>
	<div class="btn-group we7-btn-group we7-padding-bottom">
		<a href="<?php  echo url('user/edit/edit_base', array('uid' => $_GPC['uid']))?>" class="btn btn-default">基础信息</a>
		<a href="<?php  echo url('user/edit/edit_modules_tpl', array('uid' => $_GPC['uid']))?>" class="btn btn-default active">应用模板权限</a>
		<a href="<?php  echo url('user/edit/edit_account', array('uid' => $_GPC['uid']))?>" class="btn btn-default">使用账号列表</a>
	</div>

	<div class="panel we7-panel user-permission">
		<div class="panel-heading">
			<span>所属用户组：<span ng-bind="group_info.name"></span></span>
			<a href="javascript:;" class="color-default pull-right" data-toggle="modal" data-target="#group" ng-click="editGroup()">修改</a>
		</div>
		<div class="modal fade" id="group" role="dialog">
			<div class="we7-modal-dialog modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
						<div class="modal-title">修改用户组</div>
					</div>
					<div class="modal-body">
						<div class="form-group">
							<select class="we7-select" ng-model="changeGroup">
								<option value="">请选择所属用户组</option>
								<option ng-value="group.id" ng-repeat="group in groups" ng-selected="group.id == group_info.id" ng-bind="group.name"></option>
							</select>
							<span class="help-block"></span>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-primary" data-dismiss="modal" ng-click="httpChange('groupid')">确定</button>
						<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
					</div>
				</div>
			</div>
		</div>
		<div class="panel-body" ng-repeat="(packid, pack) in group_info.package_detail">
			<div class="permission-heading">
				<span ng-bind="pack.name"></span>
				<span ng-if="packid == '-1'">所有服务</span>
				<div class="pull-right permission-edit" ng-style="{'width': 'auto'}">
					<a href="javascript:;" class="color-default js-unfold" data-toggle="collapse" data-target="#demo-{{packid}}" ng-click="changeText($event)">展开</a>
				</div>
			</div>
			<table id="demo-{{pack.id}}" class="table we7-table table-hover collapse" ng-if="packid != '-1'">
				<col width="90px" />
				<col width="835px" />
				<tr class="permission-apply">
					<td class="vertical-middle">公众号应用</td>
					<td>
						<ul>
							<li ng-repeat="module in pack.modules" class="col-sm-2 text-over text-left">
								<img ng-src="{{ module.logo }}" alt="">
								{{ module.title }}
							</li>
						</ul>
					</td>
				</tr>
				<tr class="permission-apply">
					<td class="vertical-middle">小程序应用</td>
					<td>
						<ul>
							<li ng-repeat="module in pack.wxapp" class="col-sm-2 text-over text-left">
								<img ng-src="{{ module.logo }}" alt="">
								{{ module.title }}
							</li>
						</ul>
					</td>
				</tr>
				<tr class="permission-template">
					<td class="vertical-middle">模板</td>
					<td><ul><li ng-repeat="tpl in pack.templates"><span class="label label-info" ng-bind="tpl.title"></span></li></ul></td>
				</tr>
			</table>
			<table id="demo-{{packid}}" class="table we7-table table-hover collapse" ng-if="packid == -1">
				<col width="90px" />
				<col width="835px" />
				<tr class="permission-apply">
					<td class="vertical-middle">应用</td>
					<td><ul><li><span class="label label-danger">系统所有模块</span></li></ul></td>
				</tr>
				<tr class="permission-template">
					<td class="vertical-middle">模板</td>
					<td><ul><li><span class="label label-danger">系统所有模板</span></li></ul></td>
				</tr>
			</table>
		</div>
	</div>
</div>
<script type="text/javascript">
	angular.module('userManageApp').value('config', {
		user: <?php echo !empty($user) ? json_encode($user) : 'null'?>,
		profile: <?php echo !empty($profile) ? json_encode($profile) : 'null'?>,
		group_info: <?php echo !empty($group_info) ? json_encode($group_info) : 'null'?>,
		groups: <?php echo !empty($groups) ? json_encode($groups) : 'null'?>,
		links: {
			editGroup: "<?php  echo url('user/edit/edit_modules_tpl')?>",
		},
	});
	angular.bootstrap($('#js-user-edit-modulestpl'), ['userManageApp']);
</script>
<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>