<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>
 <div class="page-header">
        当前位置：
        <span class="text-primary">友情链接管理</span>
    </div>
<div class="page-content">
<!-- <div class="page-heading"> 

    <span class='pull-right'>

        <?php if(cv('pc.link.add')) { ?>

        	<a class='btn btn-primary btn-sm' href="<?php  echo webUrl('pc/link/add')?>"><i class='fa fa-plus'></i> 添加友情链接</a>

        <?php  } ?>

    </span>

    <h2>友情链接管理</h2> </div> -->



<form action="./index.php" method="get" class="form-horizontal form-search" role="form">

    <input type="hidden" name="c" value="site" />

    <input type="hidden" name="a" value="entry" />

    <input type="hidden" name="m" value="ewei_shopv2" />

    <input type="hidden" name="do" value="web" />

    <input type="hidden" name="r"  value="pc.link" />

    <div class="page-toolbar row m-b-sm m-t-sm">

        <span class='pull-left'>
            <?php if(cv('pc.link.add')) { ?>

            <a class='btn btn-primary btn-sm' href="<?php  echo webUrl('pc/link/add')?>"><i class='fa fa-plus'></i> 添加友情链接</a>

        <?php  } ?>
        </span>
        <div class="col-sm-6 pull-right">
            <div class="input-group">
                <div class="input-group-select">
                    <select name="status" class='form-control'>
                        <option value="" <?php  if($_GPC['status'] == '') { ?> selected<?php  } ?>>状态</option>
                        <option value="1" <?php  if($_GPC['status']== '1') { ?> selected<?php  } ?>>显示</option>
                        <option value="0" <?php  if($_GPC['status'] == '0') { ?> selected<?php  } ?>>隐藏</option>
                    </select>
                </div>
                <input type="text" class="input-sm form-control" name='keyword' value="<?php  echo $_GPC['keyword'];?>" placeholder="请输入关键词"> 
                <span class="input-group-btn">
                    <button class="btn btn-primary" type="submit"> 搜索</button>
                </span>
            </div>
        </div>

        

    </div>

</form>



<form action="" method="post">

    <?php  if(count($list)>0) { ?>

    <table class="table table-responsive table-hover" >
        <div class="page-table-header">
            <button class="btn btn-default btn-sm"  type="button" data-toggle='refresh'><i class='fa fa-refresh'></i></button>

                <?php if(cv('pc.link.edit')) { ?>

                    <button class="btn btn-default btn-sm" type="button" data-toggle='batch' data-href="<?php  echo webUrl('pc/link/status',array('status'=>1))?>"><i class='fa fa-circle'></i> 显示</button>

                    <button class="btn btn-default btn-sm" type="button" data-toggle='batch'  data-href="<?php  echo webUrl('pc/link/status',array('status'=>0))?>"><i class='fa fa-circle-o'></i> 隐藏</button>

                <?php  } ?>

                <?php if(cv('pc.link.delete')) { ?>  

                <button class="btn btn-default btn-sm" type="button" data-toggle='batch-remove' data-confirm="确认要删除?" data-href="<?php  echo webUrl('pc/link/delete')?>"><i class='fa fa-trash'></i> 删除</button>

                <?php  } ?>
        </div>
        <thead class="navbar-inner">

            <tr>

                <th style="width:25px;"><input type='checkbox' /></th>

                <th style='width:50px'>顺序</th>

                <th style='width: 200px;'>标题</th>

                <th style='width: 400px;'>链接</th>

                <th style='width:60px'>显示</th>

                <th style="width: 145px;">操作</th>

            </tr>

        </thead>

        <tbody>

            <?php  if(is_array($list)) { foreach($list as $row) { ?>

            <tr>

                <td>

                    <input type='checkbox'   value="<?php  echo $row['id'];?>"/>

                </td>

                <td>

                    <?php if(cv('pc.link.edit')) { ?>

                    	<a href='javascript:;' data-toggle='ajaxEdit' data-href="<?php  echo webUrl('pc/link/displayorder',array('id'=>$row['id']))?>" ><?php  echo $row['displayorder'];?></a>

                    <?php  } else { ?>

                   		<?php  echo $row['displayorder'];?> 

                    <?php  } ?>

                </td>



                <td><?php  echo $row['linkname'];?></td>

                <td><?php  echo $row['url'];?></td>

                <td>



                    <span class='label <?php  if($row['status']==1) { ?>label-success<?php  } else { ?>label-default<?php  } ?>' 

                          <?php if(cv('pc.link.edit')) { ?>

	                          data-toggle='ajaxSwitch' 

	                          data-switch-value='<?php  echo $row['status'];?>'

	                          data-switch-value0='0|隐藏|label label-default|<?php  echo webUrl('pc/link/status',array('status'=>1,'id'=>$row['id']))?>'  

	                          data-switch-value1='1|显示|label label-success|<?php  echo webUrl('pc/link/status',array('status'=>0,'id'=>$row['id']))?>'  

                          <?php  } ?>>

                          <?php  if($row['status']==1) { ?>显示<?php  } else { ?>隐藏<?php  } ?>

                    </span>





                    </td>

                    <td style="text-align:left;">

                        <?php if(cv('pc.link.view|pc.link.edit')) { ?>

                        	<a href="<?php  echo webUrl('pc/link/edit', array('id' => $row['id']))?>" class="btn btn-default btn-sm">

                        		<i class='fa fa-edit'></i> <?php if(cv('pc.link.edit')) { ?>修改<?php  } else { ?>查看<?php  } ?>

                        	</a>

                        <?php  } ?>

                        <?php if(cv('pc.link.delete')) { ?>

                        	<a data-toggle='ajaxRemove' href="<?php  echo webUrl('pc/link/delete', array('id' => $row['id']))?>"class="btn btn-default btn-sm" data-confirm='确认要删除此首页导航吗?'><i class="fa fa-trash"></i> 删除</a>

                        <?php  } ?>

                    </td>

                </tr>

                <?php  } } ?> 

                <tr>

                    <td colspan='6'>

                        <div class='pagers' style='float:right;'>

                            <?php  echo $pager;?>			

                        </div>

                    </td>

                </tr>

            </tbody>

        </table>

        <?php  echo $pager;?>

        <?php  } else { ?>

        <div class='panel panel-default'>

            <div class='panel-body' style='text-align: center;padding:30px;'>

                暂时没有任何友情链接!

            </div>

        </div>

        <?php  } ?>



    </form>





    <?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>