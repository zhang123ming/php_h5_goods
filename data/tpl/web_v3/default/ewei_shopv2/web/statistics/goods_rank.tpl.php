<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>

<div class="page-header">当前位置：<span class="text-primary">商品销售排行</span></div>

        <div class="page-content">
            <form action="./index.php" method="get" class="form-horizontal">
                <input type="hidden" name="c" value="site" />
                <input type="hidden" name="a" value="entry" />
                <input type="hidden" name="m" value="ewei_shopv2" />
                <input type="hidden" name="do" value="web" />
                <input type="hidden" name="r"  value="statistics.goods_rank" />
                <div class="page-toolbar">
                    <div class="pull-left">
                        <?php  echo tpl_daterange('datetime', array('sm'=>true,'placeholder'=>'下单时间'),true);?>
                        <?php  echo tpl_daterange('paytime', array('sm'=>true,'placeholder'=>'付款时间'),true);?>
                    </div>
                   
                    <div class="input-group">
                        <span class="input-group-select">
                            <select name='status'  class='form-control'>
                                <option value='' <?php  if($_GPC['status']=='') { ?>selected<?php  } ?>>订单状态</option>
                                <option value='1' <?php  if($_GPC['status']=='1') { ?>selected<?php  } ?>>待发货</option>
                                <option value='2' <?php  if($_GPC['status']=='2') { ?>selected<?php  } ?>>待收货</option>
                                <option value='3' <?php  if($_GPC['status']=='3') { ?>selected<?php  } ?>>已完成</option>
                            </select>
                        </span>
                        <span class="input-group-select">
                              <select name='orderby'  class='form-control '>
                                <option value='' <?php  if($_GPC['orderby']=='') { ?>selected<?php  } ?>>排序</option>
                                <option value='0' <?php  if($_GPC['orderby']=='0') { ?>selected<?php  } ?>>按销售额</option>
                                <option value='1' <?php  if($_GPC['orderby']=='1') { ?>selected<?php  } ?>>按销售量</option>
                            </select>
                        </span>
                        <input type="text" class="form-control"  name="title" value="<?php  echo $_GPC['title'];?>" placeholder="商品名称"/>
        				<span class="input-group-btn">
        					<button class="btn btn-primary" type="submit"> 搜索</button>
                            <?php if(cv('statistics.goods_rank.export')) { ?>
                            <button type="submit" name="export" value="1" class="btn btn-success">导出</button>
                            <?php  } ?>
        				</span>
                    </div>

                </div>
            </form>

            <?php  if(empty($list)) { ?>
            <div class="panel panel-default">
                <div class="panel-body empty-data">未查询到相关数据</div>
            </div>
            <?php  } else { ?>
            <table class="table table-hover">
                <thead>
                <tr>
                    <th style='width:80px;'>排行</th>
                    <th>商品名称</th>
                    <th style="width: 150px;">销售量</th>
                    <th style="width: 150px;">销售额</th>
                </tr>
                </thead>
                <tbody>
                <?php  if(is_array($list)) { foreach($list as $key => $row) { ?>
                <tr>
                    <td><?php  if(($pindex -1)* $psize + $key + 1<=3) { ?>
                        <labe class='label label-danger' style='padding:8px;'>&nbsp;<?php  echo ($pindex -1)* $psize + $key + 1?>&nbsp;</labe>
                        <?php  } else { ?>
                        <labe class='label label-default'  style='padding:8px;'>&nbsp;<?php  echo ($pindex -1)* $psize + $key + 1?>&nbsp;</labe>
                        <?php  } ?>
                    </td>
                    <td>
                        <img src="<?php  echo tomedia($row['thumb'])?>" style="width: 30px; height: 30px;border:1px solid #ccc;padding:1px;"  onerror="this.src='../addons/ewei_shopv2/static/images/nopic.png'" >
                        <?php  echo $row['title'];?></td>
                    <td><?php  echo $row['count'];?></td>
                    <td><?php  echo $row['money'];?></td>
                </tr>
                <?php  } } ?>
                </tbody>
                <tfoot >
                    <tr>
                        <td colspan="4" style="text-align: right">
                            <?php  echo $pager;?>
                        </td>
                    </tr>
                </tfoot>
            </table>
            <?php  } ?>
        </div>

<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>
<!--efwww_com-->