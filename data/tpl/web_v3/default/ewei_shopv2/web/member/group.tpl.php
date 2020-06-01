<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>

<div class="page-header">当前位置：<span class="text-primary">会员分组管理</span></div>
<div class="page-content">

    <form action="./index.php" method="get" class="form-horizontal form-search" role="form">
        <input type="hidden" name="c" value="site" />
        <input type="hidden" name="a" value="entry" />
        <input type="hidden" name="m" value="ewei_shopv2" />
        <input type="hidden" name="do" value="web" />
        <input type="hidden" name="r"  value="member.group" />

        <div class="page-toolbar">
            <div class="pull-left">
                <?php if(cv('member.group.add')) { ?>
                    <a class='btn btn-primary btn-sm' data-toggle="ajaxModal" href="<?php  echo webUrl('member/group/add')?>"><i class='fa fa-plus'></i> 添加会员分组</a>
                <?php  } ?>
            </div>
            <div class="pull-right col-md-6">
                <div class="input-group">
                    <div class="input-group-select">
                        <select name="enabled" class='form-control'>
                            <option value="" <?php  if($_GPC['enabled'] == '') { ?> selected<?php  } ?>>状态</option>
                            <option value="1" <?php  if($_GPC['enabled']== '1') { ?> selected<?php  } ?>>启用</option>
                            <option value="0" <?php  if($_GPC['enabled'] == '0') { ?> selected<?php  } ?>>禁用</option>
                        </select>
                    </div>
                    <input type="text" class=" form-control" name='keyword' value="<?php  echo $_GPC['keyword'];?>" placeholder="请输入关键词">
                    <span class="input-group-btn">
                            <button class="btn btn-primary" type="submit"> 搜索</button>
                        </span>
                </div>
            </div>
        </div>
    </form>

    <?php  if(empty($list)) { ?>
        <div class="panel panel-default">
            <div class="panel-body empty-data">未查询到相关数据</div>
        </div>
    <?php  } else { ?>
        <form action="" method="post" onsubmit="return formcheck(this)">
            <div class="page-table-header">
                <input type='checkbox' />
                <div class="btn-group">
                    <?php if(cv('member.group.delete')) { ?>
                    <button class="btn btn-default btn-sm btn-operation" type="button" data-toggle='batch-remove' data-confirm="确认要删除?" data-href="<?php  echo webUrl('member/group/delete')?>">
                        <i class='icow icow-shanchu1'></i> 删除</button>
                    <?php  } ?>
                </div>
            </div>
            <table class="table table-hover table-responsive">
                <thead>
                    <tr>
                        <th style="width:25px;"></th>
                        <th>分组名称</th>
                        <th>会员数</th>
                        <th style="width: 90px;">操作</th>
                    </tr>
                </thead>
                <tbody>
                        <?php  if(is_array($list)) { foreach($list as $row) { ?>
                            <tr <?php  if($row['id']=='default') { ?>style='background:#eee;'<?php  } ?>>
                                <td>
                                    <?php  if($row['id']!='default') { ?>
                                        <input type='checkbox' value="<?php  echo $row['id'];?>"/>
                                    <?php  } ?>
                                </td>
                                <td><?php  echo $row['groupname'];?></td>
                                <td><?php  echo $row['membercount'];?></td>
                                <td>
                                    <?php if(cv('member.list')) { ?>
                                        <a class='btn btn-op btn-operation' href="<?php  echo webUrl('member/list', array('groupid' => $row['id']))?>">
                                            <span data-toggle="tooltip" data-placement="top" data-original-title="分组会员">
                                                    <i class='icow icow-member'></i>
                                                </span>
                                        </a>
                                    <?php  } ?>
                                    <?php  if($row['id']!='default') { ?>
                                        <?php if(cv('member.group.edit')) { ?>
                                            <a data-toggle="ajaxModal" href="<?php  echo webUrl('member/group/edit', array('id' => $row['id']))?>" class="btn btn-op btn-operation" >
                                                <span data-toggle="tooltip" data-placement="top" data-original-title="修改">
                                                    <i class='icow icow-bianji2'></i>
                                                </span>
                                            </a>
                                        <?php  } ?>
                                        <?php if(cv('member.group.delete')) { ?>
                                            <a data-toggle='ajaxRemove' href="<?php  echo webUrl('member/group/delete', array('id' => $row['id']))?>"class="btn btn-op btn-operation" data-confirm='确认要删除此会员分组吗?'>
                                                <span data-toggle="tooltip" data-placement="top" data-original-title="删除">
                                                   <i class='icow icow-shanchu1'></i>
                                                </span>
                                            </a>
                                        <?php  } ?>
                                    <?php  } ?>
                                </td>
                            </tr>
                        <?php  } } ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td><input type="checkbox"></td>
                        <td colspan="1">
                            <div class="btn-group">
                                <?php if(cv('member.group.delete')) { ?>
                                    <button class="btn btn-default btn-sm btn-operation" type="button" data-toggle='batch-remove' data-confirm="确认要删除?" data-href="<?php  echo webUrl('member/group/delete')?>">
                                        <i class='icow icow-shanchu1'></i> 删除</button>
                                <?php  } ?>
                            </div>
                        </td>
                        <td colspan="2" style="text-align: right">
                            <span class="pull-right" style="line-height: 28px;">(共<?php  echo count($list)?>条记录)</span>
                            <?php  echo $pager;?>
                        </td>
                    </tr>
                </tfoot>
            </table>
        <?php  } ?>
    </form>

</div>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>



<!--efwww_com-->