<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>
  
<div class="page-header">
    <span>当前位置：<span class="text-primary">角色管理</span></span>
</div>
<div class="page-content">
   <form action="./index.php" method="get" class="form-horizontal" role="form">
                <input type="hidden" name="c" value="site" />
                <input type="hidden" name="a" value="entry" />
                <input type="hidden" name="m" value="ewei_shopv2" />
                <input type="hidden" name="do" value="web" />
                <input type="hidden" name="r"  value="perm.role" />
            <div class="page-toolbar">
	                <div class="col-sm-6">
                         <span class="">
                               <?php  if('perm.role.add') { ?>
                                       <a class='btn btn-primary btn-sm' href="<?php  echo webUrl('perm/role/add')?>"><i class="fa fa-plus"></i> 添加新角色</a>
                               <?php  } ?>
                        </span>
                    </div>
			 
                            <div class="col-sm-6 pull-right">
			 		 

				<div class="input-group">
                        <div class="input-group-select">
                            <select name="status" class='form-control'>
                                <option value="" <?php  if($_GPC['status'] == '') { ?> selected<?php  } ?>>状态</option>
                                <option value="1" <?php  if($_GPC['status']== '1') { ?> selected<?php  } ?>>启用</option>
                                <option value="0" <?php  if($_GPC['status'] == '0') { ?> selected<?php  } ?>>禁用</option>
                            </select>
                        </div>
                        <input type="text" class="input-sm form-control" name='keyword' value="<?php  echo $_GPC['keyword'];?>" placeholder="请输入关键词"> <span class="input-group-btn">

                     <button class="btn btn-primary" type="submit"> 搜索</button> </span>
                </div>

            </div>
</div>
  </form>
 <?php  if(count($list)>0) { ?>
            <div class="page-table-header">
                <input type='checkbox' />
                <div class="btn-group">
                    <?php if(cv('perm.role.edit')) { ?>
                    <button class="btn btn-default btn-sm btn-operation" type="button" data-toggle='batch' data-href="<?php  echo webUrl('perm/role/status',array('status'=>1))?>">
                        <i class='icow icow-qiyong'></i> 启用</button>
                    <button class="btn btn-default btn-sm btn-operation" type="button" data-toggle='batch'  data-href="<?php  echo webUrl('perm/role/status',array('status'=>0))?>">
                        <i class='icow icow-jinyong'></i> 禁用</button>
                    <?php  } ?>
                    <?php if(cv('perm.role.delete')) { ?>
                    <button class="btn btn-default btn-sm btn-operation" type="button" data-toggle='batch-remove' data-confirm="确认要删除?" data-href="<?php  echo webUrl('perm/role/delete')?>">
                        <i class='icow icow-shanchu1'></i> 删除</button>
                    <?php  } ?>

                </div>
            </div>
            <table class="table table-hover  table-responsive">
                <thead>
                    <tr>
                         <th style="width:25px;"></th>
                        <th>角色名称</th>
                        <th>操作员数量</th>
                        <th>状态</th>
                        <th style="width: 65px;">操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php  if(is_array($list)) { foreach($list as $row) { ?>
                    <tr>
                         <td><input type='checkbox'   value="<?php  echo $row['id'];?>"/></td>
                        <td><?php  echo $row['rolename'];?></td>
                        <td><?php  echo $row['usercount'];?></td>                            
                                 <td>
                                   <span class='label <?php  if($row['status']==1) { ?>label-primary<?php  } else { ?>label-default<?php  } ?>'
										  <?php if(cv('shop.adv.edit')) { ?>
										  data-toggle='ajaxSwitch' 
										  data-switch-value='<?php  echo $row['status'];?>'
										  data-switch-value0='0|禁用|label label-default|<?php  echo webUrl('perm/role/status',array('status'=>1,'id'=>$row['id']))?>'  
										  data-switch-value1='1|启用|label label-success|<?php  echo webUrl('perm/role/status',array('status'=>0,'id'=>$row['id']))?>'  
										  <?php  } ?>
										>
										  <?php  if($row['status']==1) { ?>启用<?php  } else { ?>禁用<?php  } ?></span>
                                </td>
                        <td>
                          <?php if(cv('perm.role.edit|perm.role.view')) { ?><a class='btn btn-default btn-sm btn-operation btn-op' href="<?php  echo webUrl('perm/role/edit', array('id' => $row['id']))?>">
                             <span data-toggle="tooltip" data-placement="top" title="" data-original-title="<?php if(cv('perm.role.edit')) { ?>编辑<?php  } else { ?>查看<?php  } ?>">
                                <i class="icow icow-bianji2"></i>
                             </span>
                            </a><?php  } ?>
                          <?php if(cv('perm.role.delete')) { ?><a class='btn btn-default  btn-sm btn-operation btn-op' data-toggle="ajaxRemove"  href="<?php  echo webUrl('perm/role/delete', array('id' => $row['id']))?>" data-confirm="确认删除此角色吗？">
                              <span data-toggle="tooltip" data-placement="top" title="" data-original-title="删除">
                                        <i class='icow icow-shanchu1'></i>
                                   </span>
                        </a><?php  } ?>
                        </td>

                    </tr>
                    <?php  } } ?>
               
                </tbody>
                <tfoot>
                    <tr>
                        <td><input type="checkbox"></td>
                        <td>
                                <div class="input-group-btn">
                                    <?php if(cv('perm.role.edit')) { ?>
                                    <button class="btn btn-default btn-sm btn-operation" type="button" data-toggle='batch' data-href="<?php  echo webUrl('perm/role/status',array('status'=>1))?>">
                                        <i class='icow icow-qiyong'></i> 启用</button>
                                    <button class="btn btn-default btn-sm btn-operation" type="button" data-toggle='batch'  data-href="<?php  echo webUrl('perm/role/status',array('status'=>0))?>">
                                        <i class='icow icow-jinyong'></i> 禁用</button>
                                    <?php  } ?>
                                    <?php if(cv('perm.role.delete')) { ?>
                                    <button class="btn btn-default btn-sm btn-operation" type="button" data-toggle='batch-remove' data-confirm="确认要删除?" data-href="<?php  echo webUrl('perm/role/delete')?>">
                                        <i class='icow icow-shanchu1'></i> 删除</button>
                                    <?php  } ?>

                                </div>
                        </td>
                        <td colspan="3" class="text-right"> <?php  echo $pager;?></td>
                    </tr>
                </tfoot>
            </table>

         <?php  } else { ?>
<div class='panel panel-default'>
	<div class='panel-body' style='text-align: center;padding:30px;'>
		 暂时没有任何角色!
	</div>
</div>
<?php  } ?>
       </form>
</div>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>

<!--efwww_com-->