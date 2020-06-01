<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>
  
<div class="page-header">
    <span>当前位置：<span class="text-primary">出勤管理</span></span>
</div>
<div class="page-content">
   <form action="./index.php" method="get" class="form-horizontal" role="form">
                <input type="hidden" name="c" value="site" />
                <input type="hidden" name="a" value="entry" />
                <input type="hidden" name="m" value="ewei_shopv2" />
                <input type="hidden" name="do" value="web" />
                <input type="hidden" name="r"  value="perm.attendance" />
            <div class="page-toolbar">
	                <div class="col-sm-6">
                         <span class="">
                               <?php  if('perm.attendance.add') { ?>
                                       <a class='btn btn-primary btn-sm' href="<?php  echo webUrl('perm/attendance/add')?>"><i class="fa fa-plus"></i> 添加新出勤</a>
                                       <a class='btn btn-primary btn-sm' href="<?php  echo webUrl('perm/attendance/add',array('batch'=>1))?>"><i class="fa fa-plus"></i> 添加新出勤[批量]</a>
                                       <a class='btn btn-primary btn-sm' href="<?php  echo webUrl('perm/attendance/add',array('batch'=>2))?>"><i class="fa fa-plus"></i> 生成报表</a>
                               <?php  } ?>
                        </span>
                    </div>
			 
                            <div class="col-sm-6 pull-right">
			 		 

				              <div class="input-group">
                        <div class="input-group-select">
                            <select name="status" class='form-control'>
                                <option value="" <?php  if($_GPC['status'] == '') { ?> selected<?php  } ?>>状态</option>
                                <option value="1" <?php  if($_GPC['status']== '1') { ?> selected<?php  } ?>>已审</option>
                                <option value="0" <?php  if($_GPC['status'] == '0') { ?> selected<?php  } ?>>待审</option>
                            </select>
                            
                        </div>
                        <div class="input-group-select">
                            
                            <select name="name" class='form-control'>
                              <option value="" <?php  if($_GPC['name'] == '') { ?> selected<?php  } ?>>出勤人</option>
                              <?php  if(is_array($nameList)) { foreach($nameList as $t) { ?>
                                <?php  if($t['name']) { ?><option value="<?php  echo $t['name'];?>" <?php  if($_GPC['name'] == $t['name']) { ?> selected<?php  } ?>><?php  echo $t['name'];?></option><?php  } ?>
                              <?php  } } ?>
                                
                            </select>
                        </div>
                        <div class="input-group-select">
                            
                            <select name="type" class='form-control'>
                              <?php  if(is_array($typeList)) { foreach($typeList as $t) { ?>
                                <option value="<?php  echo $t['value'];?>" <?php  if($type == $t['value']) { ?> selected<?php  } ?>><?php  echo $t['name'];?></option>
                              <?php  } } ?>
                                
                            </select>
                        </div>
                        <div class="input-group-select">
                            <select name="timelimit" class='form-control'>
                              <option value="" <?php  if($_GPC['timelimit'] == '') { ?> selected<?php  } ?>>时间限制</option>
                                <option value="30" <?php  if($_GPC['timelimit'] == '30') { ?> selected<?php  } ?>>每半小时</option>  
                                <option value="120" <?php  if($_GPC['timelimit'] == '120') { ?> selected<?php  } ?>>每两小时</option>                              
                            </select>
                        </div>
                        <div class="input-group-select">
                            <select name="task" class='form-control'>
                              <option value="" <?php  if($_GPC['task'] == '') { ?> selected<?php  } ?>>任务</option>
                                <option value="worktime" <?php  if($_GPC['task'] == 'worktime') { ?> selected<?php  } ?>>计算工时</option>                               
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
                    <?php if(cv('perm.attendance.edit')) { ?>
                    <button class="btn btn-default btn-sm btn-operation" type="button" data-toggle='batch' data-href="<?php  echo webUrl('perm/attendance/status',array('status'=>1))?>">
                        <i class='icow icow-qiyong'></i> 已审</button>
                    <button class="btn btn-default btn-sm btn-operation" type="button" data-toggle='batch'  data-href="<?php  echo webUrl('perm/attendance/status',array('status'=>0))?>">
                        <i class='icow icow-jinyong'></i> 待审</button>
                    <?php  } ?>
                    <?php if(cv('perm.attendance.delete')) { ?>
                    <button class="btn btn-default btn-sm btn-operation" type="button" data-toggle='batch-remove' data-confirm="确认要删除?" data-href="<?php  echo webUrl('perm/attendance/delete')?>">
                        <i class='icow icow-shanchu1'></i> 删除</button>
                    <?php  } ?>

                </div>
            </div>
            <table class="table table-hover  table-responsive">
                <thead>
                    <tr>
                         <th style="width:25px;"></th>
                        <th>类型</th>
                        <th>出勤人</th>
                        <th>出勤日期</th>
                        <th>第一次打卡</th>
                        <th>第二次打卡</th>
                        <th>加班时长</th>
                        <th>状态</th>
                        <th style="width: 65px;">操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php  if(is_array($list)) { foreach($list as $row) { ?>
                    <tr>
                         <td><input type='checkbox'   value="<?php  echo $row['id'];?>"/></td>
                        <td><?php  echo $typeList[$row['type']]['name'];?></td>
                        <td><?php  echo $row['name'];?></td>
                        <td><?php  echo $row['date'];?><?php  if($row['type']=='date') { ?><?php  echo $row['week'];?><?php  } ?></td>
                        <td>
                        <?php  if($row['date']) { ?><input type="text" id="first<?php  echo $row['id'];?>" style="width:80%;border:#333;" value="<?php  echo $row['first'];?>">
                        <br><button onclick="setValue(<?php  echo $row['id'];?>);">修改</button>
                        <?php  } ?>
                        </td>
                        <td><span id="second<?php  echo $row['id'];?>"><?php  echo $row['second'];?></span></td>
                        <td><?php  echo $row['overtime'];?></td>                           
                         <td>
                           <span class='label <?php  if($row['status']==1) { ?>label-primary<?php  } else { ?>label-default<?php  } ?>'
                                  <?php if(cv('shop.adv.edit')) { ?>
                                  data-toggle='ajaxSwitch' 
                                  data-switch-value='<?php  echo $row['status'];?>'
                                  data-switch-value0='0|待审|label label-default|<?php  echo webUrl('perm/attendance/status',array('status'=>1,'id'=>$row['id']))?>'  
                                  data-switch-value1='1|已审|label label-success|<?php  echo webUrl('perm/attendance/status',array('status'=>0,'id'=>$row['id']))?>'  
                                  <?php  } ?>
                                >
                                  <?php  if($row['status']==1) { ?>已审<?php  } else { ?>待审<?php  } ?></span>
                        </td>
                        <td>
                          <?php if(cv('perm.attendance.edit|perm.attendance.view')) { ?><a class='btn btn-default btn-sm btn-operation btn-op' href="<?php  echo webUrl('perm/attendance/edit', array('id' => $row['id']))?>">
                             <span data-toggle="tooltip" data-placement="top" title="" data-original-title="<?php if(cv('perm.attendance.edit')) { ?>编辑<?php  } else { ?>查看<?php  } ?>">
                                <i class="icow icow-bianji2"></i>
                             </span>
                            </a><?php  } ?>
                          <?php if(cv('perm.attendance.delete')) { ?><a class='btn btn-default  btn-sm btn-operation btn-op' data-toggle="ajaxRemove"  href="<?php  echo webUrl('perm/attendance/delete', array('id' => $row['id']))?>" data-confirm="确认删除此出勤吗？">
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
                                    <?php if(cv('perm.attendance.edit')) { ?>
                                    <button class="btn btn-default btn-sm btn-operation" type="button" data-toggle='batch' data-href="<?php  echo webUrl('perm/attendance/status',array('status'=>1))?>">
                                        <i class='icow icow-qiyong'></i> 已审</button>
                                    <button class="btn btn-default btn-sm btn-operation" type="button" data-toggle='batch'  data-href="<?php  echo webUrl('perm/attendance/status',array('status'=>0))?>">
                                        <i class='icow icow-jinyong'></i> 待审</button>
                                    <?php  } ?>
                                    <?php if(cv('perm.attendance.delete')) { ?>
                                    <button class="btn btn-default btn-sm btn-operation" type="button" data-toggle='batch-remove' data-confirm="确认要删除?" data-href="<?php  echo webUrl('perm/attendance/delete')?>">
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
		 暂时没有任何出勤!
	</div>
</div>
<?php  } ?>
       </form>
</div>
<script type="text/javascript">
function setValue(itemid){
	var vfirst;
	vfirst = $("#first" + itemid).val();
	$.ajax({
		url:'<?php  echo webUrl("perm/attendance/setValue")?>',
		data:{id:itemid,first:vfirst},
		type:'post',
		async : true, //默认为true 异步
		dataType:'json',
		error:function(){
			alert('error');
		},
		success:function(data){
			if(data.status>0){
				if(data.first){
					 $("#first" + itemid).val(data.first);
				}
				if(data.second){
					 $("#second" + itemid).html(data.second);
				}
			}
		}
	});
}
</script>

<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>

<!--efwww_com-->