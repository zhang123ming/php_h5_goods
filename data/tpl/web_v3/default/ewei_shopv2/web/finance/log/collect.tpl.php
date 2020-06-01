<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>

<div class="page-header">当前位置：<span class="text-primary">转账审核</span></div>

<div class="page-content">
    <form action="./index.php" method="get" class="form-horizontal form-search" role="form">
        <input type="hidden" name="c" value="site" />
        <input type="hidden" name="a" value="entry" />
        <input type="hidden" name="m" value="ewei_shopv2" />
        <input type="hidden" name="do" value="web" />
        <input type="hidden" name="r"  value="finance.log.collect" />

        <div class="page-toolbar">
<!--             <div class="col-sm-2">
                <?php if(cv('shop.comment.add')) { ?>
                    <a class='btn btn-primary btn-sm' href="<?php  echo webUrl('shop/comment/add')?>"><i class='fa fa-plus'></i> 添加虚拟评论</a>
                <?php  } ?>
            </div> -->
            <div class="col-sm-9 pull-right">
                <div class='input-group input-group-sm'  style='float:left;'  >
                    <?php  echo tpl_daterange('time', array('sm'=>true,'placeholder'=>'选择时间'),true);?>
                </div>
                <div class="input-group">
                    <span class="input-group-select">
                        <select name='status' class='form-control'>
                            <option value='' <?php  if($_GPC['fade']=='') { ?>selected<?php  } ?>>是否完成</option>
                            <option value='0' <?php  if($_GPC['fade']=='0') { ?>selected<?php  } ?>>未完成</option>
                            <option value='1' <?php  if($_GPC['fade']=='1') { ?>selected<?php  } ?> >已完成</option>
                        </select>
                    </span>
                    <input type="text" class="form-control" name='keyword' value="<?php  echo $_GPC['keyword'];?>" placeholder="会员ID/手机号/昵称">
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
        <div class="page-table-header">
            <input type='checkbox' />
            <div class="btn-group">
                <?php if(cv('shop.comment.delete')) { ?>
                <button class="btn btn-default btn-sm btn-operation" type="button" data-toggle='batch-remove' data-confirm="确认要删除?" data-href="<?php  echo webUrl('shop/comment/delete')?>">
                    <i class='icow icow-shanchu1'></i> 删除</button>
                <?php  } ?>
            </div>
        </div>
        <table class="table table-hover table-responsive">
            <thead>
                <tr>
                    <th style='width:50px;'></th>
                    <th style='width:200px;'>会员</th>
                    <th style='width:200px;'>联系方式</th>
                    <th style='width:200px;'>充值凭证</th>
                    <th style='width:200px;'>充值金额</th>
                    <th style='width:200px;'>充值状态</th>
                    <th style='width:200px;'>申请时间</th>
                    <th style='width:200px;'>操作</th>
                </tr>
            </thead>
            <tbody>
                <?php  if(is_array($list)) { foreach($list as $row) { ?>
                    <tr>
                        <td>
                            <input type='checkbox'   value="<?php  echo $row['id'];?>"/>
                        </td>
                        <td>
                            <img src="<?php  echo tomedia($row['avatar'])?>" style="width: 50px; height: 50px;border:1px solid #ccc;padding:1px;" onerror="this.src='../addons/ewei_shopv2/static/images/noface.png'">
                            <span><?php  echo $row['nickname'];?></span>
                        </td>
                        <td>
                            <small><?php  echo $row['mobile'];?></small>
                        </td>
                        <td >
                             <a class="btn  btn-op btn-operation" data-toggle="ajaxModal"
                              href="<?php  echo webUrl('finance/log/images', array('id'=>$row['id']))?>"
                               title=''>
                                 <span data-toggle="tooltip" data-placement="top" title="" data-original-title="查看">
                                  点击查看
                                </span>
                            </a>
                        </td>
                        <td >
                            <span class='text' style="color:red;"><?php  echo $row['amount'];?></span>
                        </td>
                        <td style="overflow:visible;">
                             <span class='label <?php  if($row['status']==1) { ?>label-primary<?php  } else { ?>label-default<?php  } ?>'>
                                <?php  if($row['status']==1) { ?>已完成<?php  } else { ?>待审核<?php  } ?>
                            </span>
                        </td>
                        <td >
                            <?php  echo date('Y-m-d', $row['createtime'])?><br/><?php  echo date('H:i:s', $row['createtime'])?>
                        </td>
                        <td>
                            <?php  if($row['status']!=1) { ?>
                            <a class='btn btn-op btn-operation'  style="cursor:pointer;" data-toggle='batch' data-href="<?php  echo webUrl('finance/log/collect_check',array('id'=>$row['id'],'status'=>1))?>"  data-confirm='确认要通过充值申请吗?' disabled>
                                <span data-toggle="tooltip" data-placement="top" data-original-title="确认充值">
                                    <i class='icon icon-check'></i>
                                </span>
                            </a>
                            <?php  } ?>
                            <a class='btn btn-op btn-operation'  data-toggle='ajaxRemove'   href="<?php  echo webUrl('finance/log/collect_delete', array('id' => $row['id']))?>" data-confirm="确认删除吗？">
                                <span data-toggle="tooltip" data-placement="top" data-original-title="删除">
                                    <i class='icow icow-shanchu1'></i>
                                </span>
                            </a>
                        </td>
                    </tr>
                <?php  } } ?>
            </tbody>
            <tfoot>
                <tr>
                    <td><input type="checkbox"></td>
                    <td colspan="2">
                        <div class="btn-group">
                            <?php if(cv('shop.comment.delete')) { ?>
                            <button class="btn btn-default btn-sm btn-operation" type="button" data-toggle='batch-remove' data-confirm="确认要删除?" data-href="<?php  echo webUrl('shop/comment/delete')?>">
                                <i class='icow icow-shanchu1'></i> 删除</button>
                            <?php  } ?>
                        </div>
                    </td>
                    <td colspan="7" style="text-align: right">
                        <?php  echo $pager;?>
                    </td>
                </tr>
            </tfoot>
        </table>
    <?php  } ?>
</div>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>

<!--6Z2S5bKb5piT6IGU5LqS5Yqo572R57uc56eR5oqA5pyJ6ZmQ5YWs5Y+4-->