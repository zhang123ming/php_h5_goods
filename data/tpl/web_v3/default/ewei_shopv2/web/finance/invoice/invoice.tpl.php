<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>
 
<div class="page-header">
    当前位置：<span class="text-primary">发票记录</span>
</div>
<div class="page-content"> 				   
  <form action="" method="get">   
  				   <input type="hidden" name="c" value="site" />
                   <input type="hidden" name="a" value="entry" />
                   <input type="hidden" name="m" value="ewei_shopv2" />
                   <input type="hidden" name="do" value="web" />
                   <input type="hidden" name="r" value="finance.invoice.invoice" />   
		<div class="page-toolbar">
			<div class="col-sm-6">
			    <?php if(cv('finance.invoice.add')) { ?>
			    <a class='btn btn-primary btn-sm' href="<?php  echo webUrl('finance/invoice/add')?>"><i class='fa fa-plus'></i> 添加发票</a>
			    <?php  } ?>
			</div>
			<div class="input-group">
			    <div class="input-group-select">
			        <select name="status" class='form-control input-sm select-md'>
			            <option value=""  <?php  if($_GPC['status']=='') { ?>selected<?php  } ?>>状态</option>
			            <option value="0" <?php  if($_GPC['status']=='0') { ?>selected<?php  } ?>>待审核</option>
			            <option value="1" <?php  if($_GPC['status']=='1') { ?>selected<?php  } ?>>已通过</option>
			            <option value="2" <?php  if($_GPC['status']=='2') { ?>selected<?php  } ?>>拒绝</option>
			        </select>
			    </div>
			         <input type="text" class="input-sm form-control" name='keyword' value="<?php  echo $_GPC['keyword'];?>" placeholder="纳税人识别号"> <span class="input-group-btn">
			         <button class="btn btn-primary" type="submit"> 搜索</button> </span>
			    </div>
			</div>
		</div> 
  </form>
 
 <?php  if(count($list)>0) { ?>
	<div class="page-table-header">
	    <input type="checkbox">
	    <div class="btn-group">	        
			<?php if(cv('finance.invoice.edit')) { ?>
				<button class="btn btn-default btn-sm btn-operation" type="button" data-toggle='batch-remove' data-confirm="确认要删除?" data-href="<?php  echo webUrl('finance/invoice/delete')?>"><i class='icow icow-shanchu1'></i> 删除</button>
			<?php  } ?>
	    </div>
	</div>
    <table class="table table-hover table-responsive">
        <thead>
            <tr>
				<th style="width:25px;"></th>			
				<th style='width:50px'>买家</th>
				<th style='width:50px'>买家地址</th>
				<th style='width:50px'>物流公司</th>
				<th style='width:50px'>物流单号</th>
				<th style='width:50px'>物流公司代码</th>
				<th style='width:50px'>开票金额</th>
				<th style="width:60px;">发票抬头</th>
			    <th style="width:90px;">纳税人识别号</th>	
			    <th style='width:50px'>发票类型</th>
			    <th style="width:60px;">发票内容</th>			    
			    <th style="width:60px;">创建时间</th>
			    <th style="width:60px;">最后一次更新时间</th>
			    <th style="width:60px;">开具状态</th>
			    <th style="width: 125px;">操作</th>
			</tr>
        </thead>
        <tbody>
            <?php  if(is_array($list)) { foreach($list as $row) { ?>
			<tr>				
				<td>
					<input type='checkbox' value="<?php  echo $row['itemid'];?>"/>
				</td>
			    <td><?php  echo $row['nickname'];?></td>
			    <td><?php  echo $row['province'];?><?php  echo $row['city'];?><?php  echo $row['area'];?></td>	
			    <td><?php  echo $row['expresscom'];?></td>	
			    <td><?php  echo $row['expresssn'];?></td>	
			    <td><?php  echo $row['express'];?></td>	
			    <td><?php  echo $row['amount'];?></td>	    
				<td><?php  echo $row['raised'];?></td>
				<td><?php  echo $row['number'];?></td>
			    <td>
					<?php  if($row['type']==0) { ?>普通发票<?php  } else { ?>增值税发票<?php  } ?>
			    </td>
			    <td>
					<?php  if($row['content']==0) { ?>账目明细<?php  } else { ?>商品类型<?php  } ?>
			    </td>	
			    <td><?php  echo date('y-m-d H:i:s',$row['createTime'])?></td>
			    <td><?php  echo date('y-m-d H:i:s',$row['updateTime'])?></td>
			    <td>
					<?php  if($row['status']==1) { ?>已通过<?php  } else if($row['status']==2) { ?>拒绝<?php  } else if($row['status']==0) { ?>待审核<?php  } ?>
			    </td>
			    
			    <td>
				<?php if(cv('invoice.edit|invoice.view')) { ?>
				<a class='btn btn-default btn-sm btn-op btn-operation' href="<?php  echo webUrl('finance.invoice.edit', array('itemid' => $row['itemid']))?>">
				   <span data-toggle="tooltip" data-placement="top" title="" data-original-title="<?php if(cv('finance.invoice.edit')) { ?>编辑<?php  } else { ?>查看<?php  } ?>">
				      <?php if(cv('finance.invoice.edit')) { ?>
				        <i class="icow icow-bianji2"></i>
				      <?php  } else { ?>
				        <i class="icow icow-chakan-copy"></i>
				      <?php  } ?>
				  </span>
				</a>
				<?php  } ?>

				<?php if(cv('finance.invoice.edit')) { ?>
				<a class='btn btn-default btn-sm btn-op btn-operation'  data-toggle='ajaxRemove' href="<?php  echo webUrl('finance/invoice/delete', array('itemid' => $row['itemid']))?>" data-confirm="确认删除此发票吗？">
				   <span data-toggle="tooltip" data-placement="top" title="" data-original-title="删除">
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
						<?php if(cv('finance.invoice.delete')) { ?>
							<button class="btn btn-default btn-sm btn-operation" type="button" data-toggle='batch-remove' data-confirm="确认要删除?" data-href="<?php  echo webUrl('finance/invoice/delete')?>"><i class='icow icow-shanchu1'></i> 删除</button>
						<?php  } ?>
                    </div>
                </td>
                <td colspan="5" class="text-right"> <?php  echo $pager;?></td>
            </tr>
            <tr><td colspan="5" class="text-right"> <?php  echo $pager;?></td></tr>
        </tfoot>
    </table>
	<?php  } else { ?>
		<div class='panel panel-default'>
			<div class='panel-body' style='text-align: center;padding:30px;'>
				 没有发票!
			</div>
		</div>
	<?php  } ?>
</div>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>
