<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>

<div class="page-header">当前位置：<span class="text-primary">评论管理</span></div>

<div class="page-content">
    <form action="./index.php" method="get" class="form-horizontal form-search" role="form">
        <input type="hidden" name="c" value="site" />
        <input type="hidden" name="a" value="entry" />
        <input type="hidden" name="m" value="ewei_shopv2" />
        <input type="hidden" name="do" value="web" />
        <input type="hidden" name="r"  value="shop.comment" />

        <div class="page-toolbar">
            <div class="col-sm-2">
                <?php if(cv('shop.comment.add')) { ?>
                    <a class='btn btn-primary btn-sm' href="<?php  echo webUrl('shop/comment/add')?>"><i class='fa fa-plus'></i> 添加虚拟评论</a>
                <?php  } ?>
            </div>
            <div class="col-sm-9 pull-right">
                <div class='input-group input-group-sm'  style='float:left;'  >
                    <?php  echo tpl_daterange('time', array('sm'=>true,'placeholder'=>'请选择评论时间'),true);?>
                </div>
                <div class="input-group">
                    <span class="input-group-select">
                        <select name='replystatus' class='form-control'>
                            <option value='' <?php  if($_GPC['replystatus']=='') { ?>selected<?php  } ?>>状态</option>
                            <option value='0' <?php  if($_GPC['replystatus']=='0') { ?>selected<?php  } ?>>需要首次回复</option>
                            <option value='1' <?php  if($_GPC['replystatus']=='1') { ?>selected<?php  } ?> >需要追加回复</option>
                        </select>
                    </span>
                    <span class="input-group-select">
                        <select name='fade' class='form-control'>
                            <option value='' <?php  if($_GPC['fade']=='') { ?>selected<?php  } ?>>类型</option>
                            <option value='0' <?php  if($_GPC['fade']=='0') { ?>selected<?php  } ?>>模拟评价</option>
                            <option value='1' <?php  if($_GPC['fade']=='1') { ?>selected<?php  } ?> >真实评价</option>
                        </select>
                    </span>
                    <input type="text" class="form-control" name='keyword' value="<?php  echo $_GPC['keyword'];?>" placeholder="订单号/商品">
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
                    <th style="width:25px;"></th>
                    <th style='width:50px;'>商品</th>
                    <th style=''></th>
                    <th style='width:100px;'>评价者</th>
                    <th style='width:95px;'>评分等级</th>
                    <th style='width:90px;'>评价状态</th>
                    <th style='width:90px;'>回复状态</th>
                    <th style='width:115px;'>审核状态</th>
                    <th style='width:90px;'>评价时间</th>
                    <th style='width:115px;'>操作</th>
                </tr>
            </thead>
            <tbody>
                <?php  if(is_array($list)) { foreach($list as $row) { ?>
                    <tr>
                        <td>
                            <input type='checkbox'   value="<?php  echo $row['id'];?>"/>
                        </td>
                        <td>
                            <img src="<?php  echo tomedia($row['thumb'])?>" style="width: 30px; height: 30px;border:1px solid #ccc;padding:1px;" onerror="this.src='../addons/ewei_shopv2/static/images/nopic.png'">
                        </td>
                        <td>
                            <?php  echo $row['title'];?><br/><small><?php  if(empty($row['openid'])) { ?>模拟评价<?php  } else { ?><?php  echo $row['ordersn'];?><?php  } ?></small>
                        </td>
                        <td >
                            <span data-toggle='tooltip' title='<?php  echo $row['nickname'];?>'><img class="radius50" src="<?php  echo tomedia($row['headimgurl'])?>" style="width: 30px; height: 30px;border:1px solid #ccc;padding:1px;" onerror="this.src='../addons/ewei_shopv2/static/images/noface.png'">
                            <?php  echo $row['nickname'];?></span>
                        </td>
                        <td style="color:#ff6600">
                            <?php  if($row['level']>=1) { ?><i class='fa fa-star'></i><?php  } else { ?><i class='fa fa-star-o'></i><?php  } ?>
                            <?php  if($row['level']>=2) { ?><i class='fa fa-star'></i><?php  } else { ?><i class='fa fa-star-o'></i><?php  } ?>
                            <?php  if($row['level']>=3) { ?><i class='fa fa-star'></i><?php  } else { ?><i class='fa fa-star-o'></i><?php  } ?>
                            <?php  if($row['level']>=4) { ?><i class='fa fa-star'></i><?php  } else { ?><i class='fa fa-star-o'></i><?php  } ?>
                            <?php  if($row['level']>=5) { ?><i class='fa fa-star'></i><?php  } else { ?><i class='fa fa-star-o'></i><?php  } ?>
                        </td>
                        <td>
                            <?php  if(!empty($row['append_content'])) { ?>
                            <span class='text-warning'>追加了评价</span>
                            <?php  } else { ?>
                            <span class='text-primary'>首次回复</span>
                            <?php  } ?>
                        </td>
                        <td>
                            <?php  if(empty($row['reply_content'])) { ?>
                            <span class='text-danger'>未首次回复</span>
                            <?php  } else { ?>
                            <span class='text-danger'>已首次回复</span>
                            <?php  } ?>
                            <br/>
                            <?php  if(!empty($row['append_content'])) { ?>
                                <?php  if(empty($row['append_reply_content'])) { ?>
                                <span class='text-warning'>未追加回复</span>
                                <?php  } ?>
                            <?php  } ?>
                        </td>
                        <td style="overflow:visible;">
                            <span class='<?php  if($row['checked']==0) { ?>text-success<?php  } else if($row['checked']==1) { ?>text-warning<?php  } else if($row['checked']==2) { ?>text-danger<?php  } ?>'>
                            <?php  if($row['checked']==0) { ?>首次评价通过<?php  } else if($row['checked']==1) { ?>首次评价审核中<?php  } else if($row['checked']==2) { ?>首次评价不通过<?php  } ?></span>
                            <?php  if(!empty($row['append_content'])) { ?>
                                <br>
                                <span class='<?php  if($row['replychecked']==0) { ?>text-success<?php  } else if($row['replychecked']==1) { ?>text-warning<?php  } else if($row['replychecked']==2) { ?>text-danger<?php  } ?>'>
                                <?php  if($row['replychecked']==0) { ?>追加评价通过<?php  } else if($row['replychecked']==1) { ?>追加评价审核中<?php  } else if($row['replychecked']==2) { ?>追加评价不通过<?php  } ?></span>
                            <?php  } ?>
                        </td>
                        <td >
                            <?php  echo date('Y-m-d', $row['createtime'])?><br/><?php  echo date('H:i:s', $row['createtime'])?>
                        </td>
                        <td>
                            <a class='btn btn-op btn-operation'  href="<?php  echo webUrl('shop/comment/post', array('id' => $row['id'], 'type' => 1))?>">
                                <span data-toggle="tooltip" data-placement="top" data-original-title="审核">
                                    <i class='icow icow-icon19'></i>
                                </span>
                            </a>

                            <?php  if(!empty($row['openid'])) { ?>
                                <?php if(cv('shop.comment.post')) { ?>
                                <a class='btn btn-op btn-operation'  href="<?php  echo webUrl('shop/comment/post', array('id' => $row['id']))?>">
                                    <span data-toggle="tooltip" data-placement="top" data-original-title="回复">
                                        <i class='icow icow-huifu1'></i>
                                    </span>
                                </a>
                                <?php  } ?>
                            <?php  } else { ?>
                                <?php if(cv('shop.comment.edit')) { ?>
                                <a class='btn btn-op btn-operation'  href="<?php  echo webUrl('shop/comment/edit', array( 'id' => $row['id']))?>">
                                    <span data-toggle="tooltip" data-placement="top" data-original-title="修改">
                                                <i class='icow icow-bianji2'></i>
                                            </span>
                                </a>
                                <?php  } ?>
                            <?php  } ?>
                            <?php if(cv('shop.comment.add')) { ?>
                                <a class='btn btn-op btn-operation'  href="<?php  echo webUrl('shop/comment/add', array( 'goodsid' => $row['goodsid']))?>">
                                    <span data-toggle="tooltip" data-placement="top" data-original-title="添加此商品评价">
                                    <i class='icow icow-tianjia'></i>
                                </span>
                                </a>
                            <?php  } ?>
                            <?php if(cv('shop.comment.delete')) { ?>
                                <a class='btn btn-op btn-operation'  data-toggle='ajaxRemove'   href="<?php  echo webUrl('shop/comment/delete', array('id' => $row['id']))?>" data-confirm="确认删除此评价吗？">
                                    <span data-toggle="tooltip" data-placement="top" data-original-title="删除">
                                        <i class='icow icow-shanchu1'></i>
                                    </span>
                                </a>
                            <?php  } ?>
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

<!--efwww_com-->