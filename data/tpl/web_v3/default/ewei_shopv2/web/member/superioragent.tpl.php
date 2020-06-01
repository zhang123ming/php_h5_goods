<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>
<div class="page-header">当前位置：<span class="text-primary">会员下线</span></div>
<div class="page-content">
    <form action="./index.php" method="get" class="form-horizontal" role="form" id="form1">
        <input type="hidden" name="c" value="site" />
        <input type="hidden" name="a" value="entry" />
        <input type="hidden" name="m" value="ewei_shopv2" />
        <input type="hidden" name="do" value="web" />
        <input type="hidden" name="r" value="member.superioragent" />
        <input type="hidden" name="iswxapp" value="<?php  echo $iswxapp;?>" />
        <div class="page-toolbar">
        	<span class="pull-left">
	                <?php  echo tpl_daterange('time', array('sm'=>true, 'placeholder'=>'时间筛选'),true);?>
	        </span>
            <div class="input-group">
                
	            
                <span class="input-group-select">
                    <select name="orderstaus" class="form-control" style="width:100px;float: left;"  >
                    	<option value="">排序</option>
                        <option value="1" <?php  if($_GPC['orderstaus']=='1') { ?>selected<?php  } ?>>累计佣金</option>
                        <option value="2" <?php  if($_GPC['orderstaus']=='2') { ?>selected<?php  } ?>>未结算佣金</option>
                        <option value="3" <?php  if($_GPC['orderstaus']=='3') { ?>selected<?php  } ?>>已结算佣金</option>
                    </select>
                </span>
                
                <input type="text" class="form-control " name="realname" value="<?php  echo $_GPC['realname'];?>" placeholder="可搜索 姓名/手机号/ID">
                <span class="input-group-btn">
                    <button class="btn  btn-primary" type="submit"> 搜索</button>
                    <button type="submit" name="export" value="1" class="btn btn-success ">导出</button>
                </span>
            	
            </div>
        </div>
    </form>
    <?php  if(empty($list)) { ?>
	    <div class="panel panel-default">
	        <div class="panel-body empty-data">未查询到相关数据</div>
	    </div>
    <?php  } else { ?>
	    <div class="row">
	        <div class="col-md-12">
	            <table class="table table-hover table-responsive">
		            <thead class="navbar-inner" >
		            <tr>
		                <th style='width:90px;'>会员</th>
		                <th style='width:90px;'>姓名<br/>手机号码</th>
		                <th style='width:55px;'>发展下线</th>
						<th style='width:55px;'>累计佣金</th>
		                <th style='width:55px;'>已结算佣金</th>
		                <th style='width:55px;'>未结算佣金</th>
		                <th style='width:55px;'>更多</th>
	                	<th style='width:70px;'>注册时间</th>
		                <th style='width:70px;'>操作</th>
		            </tr>
		            </thead>
		            <tbody>
		           	<?php  if(is_array($list)) { foreach($list as $row) { ?>
		           		<tr>
		           			<td style="overflow: visible;">
			                    <div  style="display: flex;overflow: hidden;"  rel="pop" data-content="
				                    <span style='color : black'><?php  if(empty($row['nickname'])) { ?>未更新<?php  } else { ?><?php  echo $row['nickname'];?><?php  } ?></span> </br>
				                    <span>ID: </span><?php  echo $row['id'];?> </br>
				                     <span>推荐人：</span> <?php  if(empty($row['agentid'])) { ?>
				                                  <?php  if($row['isagent']==1) { ?>
				                                      总店
				                                      <?php  } else { ?>
				                                     暂无
				                                      <?php  } ?>
				                                <?php  } else { ?>
				                    <?php  if(!empty($row['parentavatar'])) { ?>
				                         <img class='radius50' src='<?php  echo $row['parentavatar'];?>' style='width:20px;height:20px;padding1px;border:1px solid #ccc' onerror='this.src='../addons/ewei_shopv2/static/images/noface.png''/>
				                    <?php  } ?>
				                       [<?php  echo $row['agentid'];?>]
				                        <?php  if(empty($row['parentname'])) { ?>未更新
				                            <?php  } else { ?><?php  echo $row['parentname'];?>
				                        <?php  } ?>
									   <?php  } ?></br>
				                        <span>是否关注：</span>
				                         <?php  if(empty($row['followed'])) { ?>
				                                <?php  if(empty($row['unfollowtime'])) { ?>
				                                未关注
				                                <?php  } else { ?>
				                                取消关注
				                                <?php  } ?>
				                            <?php  } else { ?>
				                                已关注
				                            <?php  } ?></br>
				                            <span>状态:</span>  <?php  if($row['isblack']==1) { ?>黑名单<?php  } else { ?>正常<?php  } ?>
									   ">
				                        <?php if(cv('member.list.view')) { ?>
				                        <a href="<?php  echo webUrl('member/list/detail',array('id' => $row['id']));?>" title='查看会员信息' target='_blank' style="display: flex">
				                            <?php  if(!empty($row['avatar'])) { ?>
				                            <img class="radius50" src='<?php  echo tomedia($row['avatar'])?>' style='width:30px;height:30px;padding:1px;border:1px solid #ccc' onerror="this.src='../addons/ewei_shopv2/static/images/noface.png'"/>
				                            <?php  } ?>
				                        <span style="display: flex;flex-direction: column;justify-content: center;align-items: flex-start;padding-left: 5px">
				                            <span class="nickname">
				                                <?php  if(empty($row['nickname'])) { ?>未更新<?php  } else { ?><?php  echo $row['nickname'];?><?php  } ?>
				                            </span>
				                            
				                        </span>
				                        </a>
				                        <?php  } else { ?>
				                        <?php  if(!empty($row['avatar'])) { ?>
				                        <img class="radius50" src='<?php  echo tomedia($row['avatar'])?>' style='width:30px;height:30px;padding:1px;border:1px solid #ccc' onerror="this.src='../addons/ewei_shopv2/static/images/noface.png'"/>
				                        <?php  } ?>
				                        <span style="display: flex;flex-direction: column;justify-content: center;align-items: flex-start;padding-left: 5px">
				                            <span class="nickname">
				                                <?php  if(empty($row['nickname'])) { ?>未更新<?php  } else { ?><?php  echo $row['nickname'];?><?php  } ?>
				                            </span>
				                            
				                        </span>

				                        <?php  } ?>
			                    </div>
			                </td>
		                	
		           			<td><?php  echo $row['realname'];?> <br/> <?php  echo $row['mobile'];?></td>

		           			<td>		           			                    
		           				<a  href="<?php  echo webUrl('commission/agent/user',array('id' => $row['id']));?>"  target='_blank' data-toggle='popover' data-placement='right' data-html="true" data-trigger='hover' data-content='查看推广下线'>
                        		<?php  echo $row['agentcount'];?>
                    			</a>
                			</td>
                			<td><?php  echo $row['commission_total'];?></td>
		           			<td><?php  echo $row['commission_ok'];?></td>
		           			<td><?php  echo $row['unsettled'];?></td>
		           			<th> 
		           				<a id="btn-submit"  href="<?php  echo webUrl('member/superioragent/edit',array('id' => $row['id']));?>" class='external btn btn-danger btn-sm '>查看详情</a>
		           			</th>
                			<td><?php  echo date('Y-m-d',$row['createtime'])?> <?php  echo date('H:i',$row['createtime'])?></td>

		           			
		           			<td  style="overflow:visible;">
								<a class="btn btn-op btn-operation" href="<?php  echo webUrl('finance/agentcommission/getorderlist',array('id' => $row['id']));?>" target='_blank'>  
									 <span data-toggle="tooltip" data-placement="top" title="" data-original-title="业绩明细">  
										 <i class='icow icow-31'></i>  
									 </span>  
								 </a> 

                                 <a class="btn  btn-op btn-operation" href="<?php  echo webUrl('order/list',array('agentid' => $row['id']));?>"  target='_blank'>
		                            <span data-toggle="tooltip" data-placement="top" title="" data-original-title="推广订单">
		                                <i class='icow icow-tuiguang'></i>
		                            </span>
		                        </a>
                                                                
                                <a class="btn  btn-op btn-operation" href="<?php  echo webUrl('order/list', array('searchfield'=>'member', 'keyword'=>$row['nickname']))?>"  target='_blank'>
		                             <span data-toggle="tooltip" data-placement="top" title="" data-original-title="会员订单">
		                                <i class='icow icow-dingdan2'></i>
		                            </span>
		                        </a>                     
	                		</td>
		           		</tr>
		            <?php  } } ?>
		            </tbody>
	        </table>
	        <?php  echo $pager;?>
	        </div>
	    </div>
 	<?php  } ?>
</div>

<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>