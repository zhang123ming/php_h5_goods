<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>
 
<div class="page-header">
    <span>当前位置：<span class="text-primary"> <?php  if(!empty($item['id'])) { ?>编辑<?php  } else { ?>添加<?php  } ?>角色 <small><?php  if(!empty($item['id'])) { ?>修改【<?php  echo $item['rolename'];?>】<?php  } ?></small></span></span>

</div>
 <div class="page-content">
     <div class="page-sub-toolbar">
         <span class=''>
            <?php if(cv('perm.role.add')) { ?>
                <a class="btn btn-primary btn-sm" href="<?php  echo webUrl('perm/role/add')?>">添加新角色</a>
            <?php  } ?>
        </span>
     </div>
    <form action="" method="post" class="form-horizontal form-validate" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php  echo $item['id'];?>" />

                <div class="form-group">
                    <label class="col-lg control-label must">角色</label>
                    <div class="col-sm-9 col-xs-12">
                        <?php if( ce('perm.role' ,$item) ) { ?>
                        <input type="text" name="rolename" class="form-control" value="<?php  echo $item['rolename'];?>" data-rule-required="true" />
                        <?php  } else { ?>
                        <div class='form-control-static'><?php  echo $item['rolename'];?></div>
                        <?php  } ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg control-label">状态</label>
                    <div class="col-sm-9 col-xs-12">
                         <?php if( ce('perm.role' ,$item) ) { ?>
                        <label class='radio-inline'>
                            <input type='radio' name='status' value=1' <?php  if($item['status']==1) { ?>checked<?php  } ?> /> 启用
                        </label>
                        <label class='radio-inline'> 
                            <input type='radio' name='status' value=0' <?php  if($item['status']==0) { ?>checked<?php  } ?> /> 禁用
                        </label>
                        <span class="help-block">如果禁用，则当前角色的操作员全部会禁止使用</span>
                        <?php  } else { ?>
                        <div class='form-control-static'><?php  if($item['status']==1) { ?>启用<?php  } else { ?>禁用<?php  } ?></div>
                        <?php  } ?>
                    </div>
                </div>
                <?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('perm/perms', TEMPLATE_INCLUDEPATH)) : (include template('perm/perms', TEMPLATE_INCLUDEPATH));?>
                <?php if( ce('perm.role' ,$item) ) { ?>
                 <?php  } else { ?>
                 <script language='javascript'>
                     $(function(){
                         $(':checkbox').attr('disabled',true);
                     })
                     </script>
                     <?php  } ?>
                    <div class="form-group"></div>
            <div class="form-group">
                    <label class="col-lg control-label"></label>
                    <div class="col-sm-9 col-xs-12">
                           <?php if( ce('perm.role' ,$item) ) { ?>
                            <input type="submit" value="提交" class="btn btn-primary"  />
                            
                        <?php  } ?>
                       <input type="button" name="back" onclick='history.back()' <?php if(cv('perm.role.add|perm.role.edit')) { ?>style='margin-left:10px;'<?php  } ?> value="返回列表" class="btn btn-default" />
                    </div>
            </div>
 
 
    
    </form>
 </div>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>

<!--efwww_com54mI5p2D5omA5pyJ-->