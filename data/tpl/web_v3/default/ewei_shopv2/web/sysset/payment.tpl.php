<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>
<div class="page-header">
    当前位置：<span class="text-primary">支付模板管理</span></div>

<div class="page-content">
<form action="" method="post">
    <form action="./index.php" method="get" class="form-horizontal form-search" role="form">
        <input type="hidden" name="c" value="site"/>
        <input type="hidden" name="a" value="entry"/>
        <input type="hidden" name="m" value="ewei_shopv2"/>
        <input type="hidden" name="do" value="web"/>
        <input type="hidden" name="r" value="sysset.payment"/>
        <div class="page-toolbar">
            <div class="col-sm-4">
                <?php if(cv('sysset.payment.add')) { ?>
                <a class='btn btn-primary btn-sm' href="<?php  echo webUrl('sysset/payment/add')?>"><i class="fa fa-plus"></i> 添加新模板</a>
                <?php  } ?>
            </div>


            <div class="col-sm-6 pull-right">


                <div class="input-group">
                    <input type="text" class="input-sm form-control" name='keyword' value="<?php  echo $_GPC['keyword'];?>" placeholder="请输入关键词">
                    <span class="input-group-btn">
                        <button class="btn btn-primary" type="submit"> 搜索</button>
                    </span>
                </div>

            </div>
        </div>
    </form>


    <?php  if(count($list)>0) { ?>
    <?php if(cv('sysset.payment.delete')) { ?>
    <div class="page-table-header">
        <input type="checkbox">
        <div class="btn-group">
            <button class="btn btn-default btn-sm btn-operation dropdown-toggle" type="button" data-toggle='batch-remove' data-confirm="确认要删除?" data-href="<?php  echo webUrl('sysset/payment/delete')?>">
                <i class='icow icow-shanchu1'></i> 删除
            </button>
        </div>
    </div>
    <?php  } ?>
    <table class="table table-responsive table-hover">
        <thead>
        <tr>
            <th style="width:25px;"></th>
            <th>模板名称</th>
            <th>支付类型</th>
            <th style="width: 70px;">操作</th>
        </tr>
        </thead>
        <tbody>
        <?php  if(is_array($list)) { foreach($list as $row) { ?>
        <tr>
            <td>
                <input type='checkbox' value="<?php  echo $row['id'];?>"/>
            </td>
            <td><?php  echo $row['title'];?></td>
            <td>
                <span class="text-success">
                    <?php  if($row['paytype']==0) { ?>
                    <?php echo isset($payment[$row['type']]) ? $payment[$row['type']] : '未知';?>
                    <?php  } else if($row['paytype']==1) { ?>
                    <?php echo isset($paytypeali[$row['type']]) ? $paytypeali[$row['type']] : '未知';?>
                    <?php  } ?>
                </span>
            </td>
            <td>
                <?php if(cv('sysset.payment.edit')) { ?>
                <a class='btn btn-default  btn-sm btn-op btn-operation' href="<?php  echo webUrl('sysset/payment/edit', array('id' => $row['id']))?>">
                    <span data-toggle="tooltip" data-placement="top" title="" data-original-title=" <?php if(cv('sysset.payment.edit')) { ?>编辑<?php  } else { ?>查看<?php  } ?>">
                         <?php if(cv('sysset.payment.edit')) { ?>
                         <i class='icow icow-bianji2'></i>
                        <?php  } else { ?>
                            <i class="icow icow-chakan-copy"></i>
                        <?php  } ?>
                    </span>
                </a>
                <?php  } ?>
                <?php if(cv('sysset.payment.delete')) { ?>
                <a class='btn btn-default  btn-sm btn-op btn-operation' data-toggle='ajaxRemove' href="<?php  echo webUrl('sysset/payment/delete', array('id' => $row['id']))?>" data-confirm="确认删除此模板吗？">
                    <span data-toggle="tooltip" data-placement="top" title="" data-original-title="删除">
                         <i class='icow icow-shanchu1'></i>
                    </span>
                </a>
                <?php  } ?>
        </tr>
        <?php  } } ?>
        </tbody>
        <tfoot>
            <tr>
                <td>
                    <input type="checkbox">
                </td>
                <td>
                    <div class="btn-group">
                        <button class="btn btn-default btn-sm btn-operation dropdown-toggle" type="button" data-toggle='batch-remove' data-confirm="确认要删除?" data-href="<?php  echo webUrl('sysset/payment/delete')?>">
                            <i class='icow icow-shanchu1'></i> 删除
                        </button>
                    </div>
                </td>
                <td colspan="2"> <?php  echo $pager;?></td>
            </tr>
        </tfoot>
    </table>
    <?php  } else { ?>
    <div class='panel panel-default'>
        <div class='panel-body' style='text-align: center;padding:30px;'>
            暂时没有任何支付模板!
        </div>
    </div>
    <?php  } ?>


</form>
</div>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>