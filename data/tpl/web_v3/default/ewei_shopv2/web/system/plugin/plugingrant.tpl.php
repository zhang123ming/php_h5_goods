<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>
<style type='text/css'>
    .trhead td {  background:#efefef;text-align: center}
    .trbody td {  text-align: center; vertical-align:top;border-left:1px solid #f2f2f2;overflow: hidden; font-size:12px;}
    .trorder { background:#f8f8f8;border:1px solid #f2f2f2;text-align:left;}
    .ops { border-right:1px solid #f2f2f2; text-align: center;}
</style>
<div class="page-header">
    当前位置：<span class="text-primary">授权管理</span>
</div>

<div class="page-content">
    <form action="./index.php" method="get" class="form-horizontal table-search" role="form">
        <input type="hidden" name="c" value="site" />
        <input type="hidden" name="a" value="entry" />
        <input type="hidden" name="m" value="ewei_shopv2" />
        <input type="hidden" name="do" value="web" />
        <input type="hidden" name="r" value="system.plugin.plugingrant" />
        <!--<input type="hidden" name="status" value="<?php  echo $status;?>" />
        <input type="hidden" name="agentid" value="<?php  echo $_GPC['agentid'];?>" />
        <input type="hidden" name="refund" value="<?php  echo $_GPC['refund'];?>" />-->
        <div class="page-toolbar">
            <div class="col-sm-6">
                <div class='input-group'>
                    <a class='btn btn-primary btn-sm' style="margin-right: 10px" href="<?php  echo webUrl('system/plugin/plugingrant/add')?>"><i class='fa fa-plus'></i> 添加授权</a>
                    <?php  echo tpl_form_field_daterange('time', array('starttime'=>date('Y-m-d H:i', $starttime),'endtime'=>date('Y-m-d H:i', $endtime)),true);?>
                </div>
            </div>
            <div class="col-sm-6 pull-right">
                <div class="input-group">
                    <div class="input-group-select">
                        <select name='searchtime'  class='form-control'   >
                            <option value=''>不按时间</option>
                            <option value='create' <?php  if($_GPC['searchtime']=='create') { ?>selected<?php  } ?>>创建时间</option>
                        </select>
                    </div>
                    <div class="input-group-select">
                        <select name='searchfield'  class='form-control  input-sm select-md'   style="width:95px;padding:0 5px;"  >
                            <option value='uniacid' <?php  if($_GPC['searchfield']=='uniacid') { ?>selected<?php  } ?>>公众号</option>
                            <option value='plugin' <?php  if($_GPC['searchfield']=='plugin') { ?>selected<?php  } ?>>应用名称</option>
                        </select>
                    </div>
                    <input type="text" class="form-control input-sm"  name="keyword" value="<?php  echo $_GPC['keyword'];?>" placeholder="请输入关键词"/>
                    <span class="input-group-btn">
                        <button class="btn btn-primary" type="submit"> 搜索</button>
                        <!--<button type="submit" name="export" value="1" class="btn btn-success btn-sm">导出</button>-->
                    </span>
                </div>

            </div>
        </div>

    </form>


    <?php  if(count($list)>0) { ?>
    <table class="table table-hover table-responsive">
        <thead>
        <tr>
            <th style="width:150px;">公众号</th>
            <th style="width:60px;">应用</th>
            <th>&nbsp;</th>
            <th style="width:100px;">授权时长</th>
            <th style="width: 100px;">上次授权</th>
            <th style="width: 100px;text-align: center;">状态</th>
            <th style="width: 65px;text-align: center;">操作</th>
        </tr>
        </thead>
        <tbody>
        <?php  if(is_array($list)) { foreach($list as $row) { ?>
        <tr>
            <td>
                <?php  echo $row['wname'];?>
            </td>
            <td>
                <img src="<?php  echo tomedia($row['thumb'])?>" style="width:40px;height:40px;padding:1px;border:1px solid #ccc;"  onerror="this.src='../addons/ewei_shopv2/static/images/nopic.png'"/>
            </td>
            <td class='full'>
                <?php  echo $row['pname'];?>
            </td>
            <td>
                <?php  if($row['month']==0) { ?>永久<?php  } else { ?><?php  echo $row['month'];?>个月<?php  } ?>
            </td>
            <td><?php  if($row['permlasttime']>0) { ?><?php  echo date('Y-m-d',$row['permlasttime'])?><br /><?php  echo date('H:i',$row['permlasttime'])?><?php  } else { ?>-<?php  } ?></td>
            <td style="text-align: center;">
                <span class='label <?php  if($row['isperm']==1) { ?>label-primary<?php  } else { ?>label-default<?php  } ?>'
                <?php  if($row['isperm']==0) { ?>
                data-toggle='ajaxSwitch'
                data-switch-value='<?php  echo $row['isperm'];?>'
                data-switch-value0='0|授权|label label-default|<?php  echo webUrl('system/plugin/plugingrant/grant',array('id'=>$row['id']))?>'
                data-switch-value1='1|已授权|label label-danger|<?php  echo webUrl('system/plugin/plugingrant/grant',array('id'=>$row['id']))?>'<?php  } ?>>
                <?php  if($row['isperm']==1) { ?>已授权<?php  } else { ?>授权<?php  } ?></span>
            </td>
            <td style="text-align: center;">
                <a class='btn btn-default btn-sm btn-op btn-operation' href="<?php  echo webUrl('system/plugin/plugingrant/edit',array('id' => $row['id']));?>" >
                     <span data-toggle="tooltip" data-placement="top" title="" data-original-title="编辑">
                        <i class="icow icow-bianji2"></i>
                     </span>
                </a>
            </td>
        </tr>
        <?php  } } ?>
        </tbody>
        <tfoot>
            <tr>
                    <td colspan="7" class="text-right">   <?php  echo $pager;?></td>
            </tr>
        </tfoot>
    </table>
    <?php  } else { ?>

    <div class='panel panel-default'>
        <div class='panel-body' style='text-align: center;padding:30px;'>
            暂时没有任何记录!
        </div>
    </div>
    <?php  } ?>
</div>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>

<!--efwww_com54mI5p2D5omA5pyJ-->