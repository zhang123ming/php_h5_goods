<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>

<div class="page-header">当前位置：<span class="text-primary">添加分公司</span></div>
<div class="page-content">

	<form <?php  if('member.branch.add') { ?>action="" method='post'<?php  } ?> class='form-horizontal form-validate'>
 <input type="hidden" name="" value="" />
	<div class="tabs-container">

		<div class="tabs">
			
				
			<div class="tab-content ">

				<div class="form-group">
				    <label class="col-lg control-label">分公司名称 :</label>
				    <div class="col-sm-9 col-xs-12">
				        <input class="form-control" type="text" name="branch_name"  value="" />
				    </div>
				</div>
			</div>
		</div>
	</div>
	<div class="form-group"></div>	
          <div class="form-group">
		<label class="col-lg control-label"></label>
		<div class="col-sm-9 col-xs-12">
			<?php if(cv('member.branch.add')) { ?>
			<input type="submit"  value="提交" class="btn btn-primary" />
			<?php  } ?>
			<input type="button" class="btn btn-default" name="submit" onclick="history.go(-1)" value="返回列表" <?php if(cv('member.branch')) { ?>style='margin-left:10px;'<?php  } ?> />
		</div>
	</div>

</form>

</div>

<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>
<!--efwww_com-->