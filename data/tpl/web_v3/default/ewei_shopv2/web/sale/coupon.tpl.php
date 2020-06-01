<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>
<div class='page-header'>
	当前位置：<span class="text-primary">优惠券管理 </span>
</div>


<div class="page-content">
	<form action="./index.php" method="get" class="form-horizontal table-search" role="form" id="form1">
		<input type="hidden" name="c" value="site" />
		<input type="hidden" name="a" value="entry" />
		<input type="hidden" name="m" value="ewei_shopv2" />
		<input type="hidden" name="do" value="web" />
		<input type="hidden" name="r" value="sale.coupon" />
		<div class="page-toolbar">

			<span class='col-sm-4'>
					<?php if(cv('sale.coupon.add')) { ?>
							<a class='btn btn-primary btn-sm' href="<?php  echo webUrl('sale/coupon/add')?>"><i class='fa fa-plus'></i> 添加购物优惠券</a>
							<a class='btn btn-warning btn-sm' href="<?php  echo webUrl('sale/coupon/add',array('type'=>1))?>"><i class='fa fa-plus'></i> 添加充值优惠券</a>
						<?php  if(p('commission')) { ?>
							<a class='btn btn-info btn-sm' href="<?php  echo webUrl('sale/coupon/add',array('type'=>2))?>"><i class='fa fa-plus'></i> 添加收银台优惠券</a>
						<?php  } ?>
					<?php  } ?>
			</span>
			<div class="col-sm-8 pull-right">
				<div class='input-group input-group-sm' style='float:left;'   >
					<?php  echo tpl_daterange('time', array('placeholder'=>'创建时间'),true);?>
				</div>

				<div class="input-group">
					<span class="input-group-select">
						<select name='gettype'  class='form-control  input-sm select-md'   style="width:100px;padding:0 5px;"  >
							<option value=''>领券中心</option>
							<option value='0' <?php  if($_GPC['gettype']=='0') { ?>selected<?php  } ?>>不显示</option>
							<option value='1' <?php  if($_GPC['gettype']=='1') { ?>selected<?php  } ?>>显示</option>
						</select>
					</span>
					<span class="input-group-select">
						<select name='type' class='form-control  input-sm select-md'   style="width:100px;"  >
							<option value=''>类型</option>
							<option value='0' <?php  if($_GPC['type']=='0') { ?>selected<?php  } ?>>购物</option>
							<option value='1' <?php  if($_GPC['type']=='1') { ?>selected<?php  } ?>>充值</option>
						</select>
					</span>
					<span class="input-group-select">
							<select name='catid' class='form-control  input-sm select-md'   style="width:100px;"  >
								<option value=''>分类</option>
								<?php  if(is_array($category)) { foreach($category as $k => $c) { ?>
								<option value='<?php  echo $k;?>' <?php  if($_GPC['catid']==$k) { ?>selected<?php  } ?>><?php  echo $c['name'];?></option>
								<?php  } } ?>
							</select>
					</span>
					<input type="text" class="form-control" name='keyword' value="<?php  echo $_GPC['keyword'];?>" placeholder="优惠券名称"> <span class="input-group-btn">

                                     <button class="btn btn-primary" type="submit"> 搜索</button> </span>
				</div>

			</div>
		</div>
	</form>

	<?php  if(count($list)>0) { ?>
	<div class="page-table-header">
		<input type="checkbox">
			<span class="btn-group">
				<?php if(cv('sale.coupon.delete')) { ?>
				<button class="btn btn-default btn-sm dropdown-toggle btn-operation" type="button" data-toggle='batch-remove' data-confirm="确认要删除?" data-href="<?php  echo webUrl('sale/coupon/delete')?>">
					<i class='icow icow-shanchu1'></i>删除
				</button>
				<?php  } ?>
			</span>
	</div>
	<table class="table table-hover table-responsive">
		<thead class="navbar-inner" >
		<tr>
			<th style="width:25px;"><input type='checkbox' /></th>
			<th style="width:50px;">排序</th>
			<th style="">优惠券名称</th>
			<th style=" ">使用条件<br/>优惠<br/>剩余数量</th>
			<th style="">领取中心</th>
			<th style="width:100px;" >口令玩法人数 猜中人数</th>
			<th style="width:100px;">创建时间</th>
			<th style="width: 130px;">操作</th>
		</tr>
		</thead>
		<tbody>

		<?php  if(is_array($list)) { foreach($list as $row) { ?>
		<tr>
			<td>
				<input type='checkbox'   value="<?php  echo $row['id'];?>"/>
			</td>
			<td>
				<?php if(cv('sale.coupon.edit')) { ?>
				<a href='javascript:;' data-toggle='ajaxEdit' data-href="<?php  echo webUrl('sale/coupon/displayorder',array('id'=>$row['id']))?>" ><?php  echo $row['displayorder'];?></a>
				<?php  } else { ?>
				<?php  echo $row['displayorder'];?>
				<?php  } ?>
			</td>

			<td><?php  if($row['coupontype']==0) { ?>
				<label class='label label-success'>购物</label>
				<?php  } else if($row['coupontype']==1) { ?>
				<label class='label label-warning'>充值</label>
				<?php  } else if($row['coupontype']==2) { ?>
				<label class='label label-info'>收银台</label>
				<?php  } ?>
				<?php  if(!empty($row['catid'])) { ?>
				<label class='label label-primary'><?php  echo $category[$row['catid']]['name'];?></label>
				<?php  } ?>
				<br/><?php  echo $row['couponname'];?>
			</td>
			<td><?php  if($row['enough']>0) { ?>
				<span class="text-danger">满<?php  echo $row['enough'];?>可用</span>
				<?php  } else { ?>
				<span class="text-warning">不限</span>
				<?php  } ?>

				<?php  if($row['backtype']==0) { ?>
				<br/>立减 <?php  echo $row['deduct'];?> 元
				<?php  } else if($row['backtype']==1) { ?>
				<br/>打 <?php  echo $row['discount'];?> 折
				<?php  } else if($row['backtype']==2) { ?>
				<?php  if($row['backmoney']>0) { ?><br/>返 <?php  echo $row['backmoney'];?> 余额;<?php  } ?>
				<?php  if($row['backcredit']>0) { ?><br/>返 <?php  echo $row['backcredit'];?> <?php  echo $_W['shopset']['trade']['credittext'];?>;<?php  } ?>
				<?php  if($row['backredpack']>0) { ?><br/>返 <?php  echo $row['backredpack'];?> 红包;<?php  } ?>
				<?php  } else if($row['backtype']==3) { ?>
					<br>每消费一元送<?php  echo $row['backrate'];?>元优惠劵
				<?php  } ?>
				<br/>
				<?php if(cv('coupon.log.view')) { ?>
				<a href="<?php  echo webUrl('sale/coupon/log',array('couponid'=>$row['id']))?>"
				   data-toggle='popover'
				   data-html='true'
				   data-trigger='hover'
				   data-content="已使用: <?php  echo $row['usetotal'];?> <br/> 已发出: <?php  echo $row['gettotal'];?>">
					<?php  if($row['total']==-1) { ?>无限<?php  } else { ?><?php  echo $row['total'] -  $row['gettotal']?><?php  } ?> <i class='fa fa-question-circle'></i>
				</a>
				<?php  } else { ?>
				<span><?php  if($row['total']==-1) { ?>无限<?php  } else { ?><?php  echo $row['total'] -  $row['gettotal']?><?php  } ?></span>
				<?php  } ?>
			</td>




			<td><?php  if($row['gettype']==0) { ?>
				<span class="text-default">不显示</span>
				<?php  } else { ?>

				<?php  if($row['credit']>0 || $row['money']>0) { ?>
				<?php  if($row['credit']>0) { ?><span class='text-primary'><?php  echo $row['credit'];?> <?php  echo $_W['shopset']['trade']['credittext'];?></span><br/><?php  } ?>
				<?php  if($row['money']>0) { ?><span class=text-danger'><?php  echo $row['money'];?> 元现金</span><br/><?php  } ?>
				<?php  } else { ?>
				<span class='text-warning'>免费</span>
				<?php  } ?>
				<?php  } ?>
			</td>
			<td><?php  echo $row['pwdjoins'];?> / <?php  echo $row['pwdoks'];?></td>
			<td><?php  echo date('Y-m-d',$row['createtime'])?><br/><?php  echo date('H:i',$row['createtime'])?></td>
			<td>

				<div class="btn-group">
					<?php if(cv('sale.coupon.edit')) { ?>
					<a class='btn btn-default btn-operation btn-op' href="<?php  echo webUrl('sale/coupon/edit',array('id' => $row['id']));?>">
						 <span data-toggle="tooltip" data-placement="top" title="" data-original-title="<?php if(cv('sale.coupon.edit')) { ?>编辑<?php  } else { ?>查看<?php  } ?>">
								<i class='icow icow-bianji2'></i>
						   </span>
					</a>

					<?php  } ?>
					<a href="javascript:;" data-url="<?php  echo mobileUrl('sale/coupon/detail', array('id' => $row['id']),true)?>"  class="btn btn-default js-clip btn-operation btn-op">
						 <span data-toggle="tooltip" data-placement="top"  data-original-title="复制链接">
							   <i class='icow icow-lianjie2'></i>
						   </span>
					</a>
					<a href="javascript:;" data-url="/pages/sale/coupon/detail/index?id=<?php  echo $row['id'];?>"  class="btn btn-default js-clip btn-operation btn-op">
						 <span data-toggle="tooltip" data-placement="top"  data-original-title="小程序链接">
							   <i class='icow icow-homeW'></i>
						   </span>
					</a>
					<?php if(cv('sale.coupon.delete')) { ?>
					<a class='btn btn-default btn-operation btn-op' data-toggle='ajaxRemove' href="<?php  echo webUrl('sale/coupon/delete',array('id' => $row['id']));?>" data-confirm="确定要删除该优惠券吗？">
						 <span data-toggle="tooltip" data-placement="top" title="" data-original-title="删除">
								<i class='icow icow-shanchu1'></i>
						   </span>
					</a>

					<?php  } ?>
					<a href="javascript:void(0);" class="btn btn-default btn-operation btn-op" data-toggle="popover" data-trigger="hover" data-html="true"
					   data-content="<img src='<?php  echo $row['qrcode'];?>' width='130' alt='链接二维码'>" data-placement="auto right">
						<i class="icow icow-erweima3"></i>
					</a>




					<!--	<?php if(cv('sale.coupon.send')) { ?>
                        <a  class='btn btn-primary  btn-sm' href="<?php  echo webUrl('sale/coupon/send',array('couponid' => $row['id']));?>"><i class='fa fa-send'></i> 发放优惠券</a>

                        <?php  } ?>-->

				</div>


			</td>
		</tr>
		<?php  } } ?>
		</tbody>
	<tfoot>
		<tr>
			<td colspan="3">
				<input type="checkbox">
				<span class="btn-group">
					<?php if(cv('sale.coupon.delete')) { ?>
					<button class="btn btn-default btn-sm dropdown-toggle btn-operation" type="button" data-toggle='batch-remove' data-confirm="确认要删除?" data-href="<?php  echo webUrl('sale/coupon/delete')?>">
						<i class='icow icow-shanchu1'></i>删除
					</button>
					<?php  } ?>
				</span>
			</td>
			<td colspan="5" style="text-align: right"><?php  echo $pager;?></td>							
		</tr>
	</tfoot>
	</table>
	<?php  } else { ?>
	<div class='panel panel-default'>
		<div class='panel-body' style='text-align: center;padding:30px;'>
			暂时没有任何优惠券!
		</div>
	</div>
	<?php  } ?>
	</form>
</div>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>
<!--efwww_com54mI5p2D5omA5pyJ-->