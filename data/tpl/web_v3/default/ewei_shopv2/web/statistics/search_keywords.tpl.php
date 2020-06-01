<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>

<div class="page-header">当前位置：<span class="text-primary">搜索词排行</span></div>

        <div class="page-content">
            <form action="./index.php" method="get" class="form-horizontal">
                <input type="hidden" name="c" value="site" />
                <input type="hidden" name="a" value="entry" />
                <input type="hidden" name="m" value="ewei_shopv2" />
                <input type="hidden" name="do" value="web" />
                <input type="hidden" name="r"  value="statistics.search_keywords" />
                <div class="page-toolbar">
                    <div class="pull-left">
                        <?php  echo tpl_daterange('datetime', array('sm'=>true,'placeholder'=>'搜索时间'),true);?>
                    </div>
                    <div class="input-group">
                        <input type="text" class="form-control"  name="title" value="<?php  echo $_GPC['title'];?>" placeholder="搜索词"/>
        				<span class="input-group-btn">
        					<button class="btn btn-primary" type="submit"> 搜索</button>
                            <?php if(cv('statistics.search_keywords.export')) { ?>
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
                    <th style="width:100px;">搜索词</th>
                    <th style="width: 80px;">搜索次数</th>
                    <th style="width: 100px;">搜索时间</th>
                    <!--<th style="width: 150px;">操作</th>-->
                </tr>
                </thead>
                <tbody>
                <?php  if(is_array($list)) { foreach($list as $key => $item) { ?>
                <tr>
                    <td><?php  if(($pindex -1)* $psize + $key + 1<=3) { ?>
                        <labe class='label label-danger' style='padding:8px;'>&nbsp;<?php  echo ($pindex -1)* $psize + $key + 1?>&nbsp;</labe>
                        <?php  } else { ?>
                        <labe class='label label-default'  style='padding:8px;'>&nbsp;<?php  echo ($pindex -1)* $psize + $key + 1?>&nbsp;</labe>
                        <?php  } ?>
                    </td>
                    <td>
                        <?php  echo $item['search_keywords'];?></td>
                      
                    <td><?php  echo $item['search_count'];?></td>
                    <td><?php  echo $item['createTime'];?></td>
                    <!--<td></td>-->
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