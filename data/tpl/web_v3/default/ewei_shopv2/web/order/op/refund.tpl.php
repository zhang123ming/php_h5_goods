<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>
<style type='text/css'>
    .order-container{
        display: -webkit-box;
        display: -webkit-flex;
        display: -ms-flexbox;
        display: flex;
    }
    .order-container-left,.order-container-right{
        -webkit-box-flex: 1;
        -webkit-flex: 1;
        -ms-flex: 1;
        flex: 1;
    }
	.ordertable { width:100%;position: relative;margin-bottom:10px}
	.ordertable tr td:first-child { text-align: right }
	.ordertable tr td {padding:8px 5px 0;vertical-align: top}
	.ordertable1 tr td { text-align: right; }
	.ops .btn { padding:5px 10px;}
    <?php  if(count($step_array)>4) { ?>.ui-step-4 li { width:20%;}<?php  } ?>
    .order-container-right{
        padding:30px 0 0 15px ;
    }
    .trorder td{
        text-align: center;
    }
    .navbar-inner tr th{
        text-align: center
    }
    .table .trorder td{
        border-right:1px solid #efefef;
    }
    .table .trorder td:nth-of-type(1){
        border:none;
    }
</style>
<div class="page-header"> <span class="text-primary">维权处理</span></div>
<div class="page-content">
<?php  if(!empty($step_array)) { ?>
<div class="step-region" >
	<ul class="ui-step ui-step-4" >

        <?php  if(is_array($step_array)) { foreach($step_array as $k1 => $v1) { ?>
        <li <?php  if($v1['done']==1) { ?>class="ui-step-done"<?php  } ?>>
            <div class="ui-step-number"><?php  echo $v1['step'];?></div>
            <div class="ui-step-title"><?php  echo $v1['title'];?></div>
            <div class="ui-step-meta"><?php  if(!empty($v1['time'])) { ?><?php  echo date('Y-m-d',$v1['time'])?><br/><?php  echo date('H:i:s',$v1['time'])?><?php  } ?></div>
        </li>
        <?php  } } ?>


	</ul>
</div>
<?php  } ?>
   <form class="form-horizontal form" action="" method="post">
        <input type="hidden" name="id" value="<?php  echo $item['id'];?>" />
        
        <input type="hidden" name="dispatchid" value="<?php  echo $dispatch['id'];?>" />
		  <?php  if(!empty($refund)) { ?>

		<!--div class='panel panel-default'>
			<div class='panel-body'>
				 <h4 class="m-t-none m-b">退款申请</h4>
				 <table class='ordertable' style='table-layout:fixed'>
						 <tr>
							 <td style='width:80px'>退款原因：</td>
						            <td><?php  echo $refund['reason'];?></td>
						 </tr>
						  <tr>
							 <td style='width:80px'>退款原因：</td>
						            <td><?php  echo $refund['reason'];?></td>
						 </tr>
						<?php  if($refund['status']==1) { ?>
						<tr>
							 <td style='width:80px'>退款时间：</td>
						            <td><?php  echo date('Y-m-d H:i:s',$item['refundtime'])?></td>
						 </tr>
						<?php  } ?>

						  <?php if(cv('order.op.refund')) { ?>
						  <tr>
							 <td style='width:80px'></td>
						            <td><?php  if($refund['status']==0) { ?>
									<a class="btn btn-danger btn-sm" href="javascript:;" onclick="$('#modal-refund').find(':input[name=id]').val('<?php  echo $item['id'];?>')" data-toggle="modal" data-target="#modal-refund">处理退款申请</a>
									<?php  } else if($refund['status']==-1) { ?>
									<span class='label label-default'>已拒绝</span>
									<?php  } else if($refund['status']==1) { ?>
									<span class='label label-danger'>已退款</span>
									<?php  } ?></td>
						 </tr>

							<?php  } ?>

				 </table>
			</div>
		</div-->
		   <?php  } ?>

 
	<div  class='row order-container'>
		<div class="order-container-left" style="border-right: 1px solid #efefef">
			<div class='panel-body' >
				 <h4 class="m-t-none m-b">维权信息</h4>
				 <div class="form-group" style='padding:0 10px;'>
					 <table class='ordertable' style='table-layout:fixed'>
						 <tr>
							 <td style='width:80px'>维权类型：</td>
						            <td><?php  echo $r_type[$refund['rtype']];?></td>
						 </tr>

                         <?php  if($refund['rtype']!=2) { ?>
                         <tr>
                             <td style='width:80px'>退款金额：</td>
                             <td><?php  echo $refund['applyprice'];?>(元)</td>
                         </tr>
                         <tr>
                             <td style='width:80px'>退还<?php  echo $_W['shopset']['trade']['credittext'];?>：</td>
                             <td><?php  echo $item['deductcredit'];?>(个)</td>
                         </tr>
                         <?php  } ?>

                         <tr>
                             <td style='width:80px'>维权原因：</td>
                             <td><?php  echo $refund['reason'];?></td>
                         </tr>

                         <tr>
                             <td style='width:80px'>维权说明：</td>
                             <td style="white-space: normal;"><?php  echo $refund['content'];?></td>
                         </tr>

                         <?php  if(!empty($refund['imgs'])) { ?>
                         <tr>
                             <td style='width:80px'>图片凭证：</td>
                             <td>
                                 <?php  if(is_array($refund['imgs'])) { foreach($refund['imgs'] as $k1 => $v1) { ?>
                                 <a target='_blank' href='<?php  echo tomedia($v1)?>'><img style='width:100px;;padding:1px;border:1px solid #ccc' src='<?php  echo tomedia($v1)?>'></a>
                                 <?php  break;?>
                                 <?php  } } ?>
                                 &nbsp;
                                 <?php  if(count($refund['imgs']) > 1) { ?>
                                 <a data-toggle='popover' data-html='true' data-placement='right'
                                    data-content="<table style='width:100%;'>
                        <?php  if(is_array($refund['imgs'])) { foreach($refund['imgs'] as $k1 => $v1) { ?>
                        <tr>
                        <td  style='border:none;text-align:right;padding:5px;'>
                            <a target='_blank' href='<?php  echo tomedia($v1)?>'><img style='width:100px;;padding:1px;border:1px solid #ccc' src='<?php  echo tomedia($v1)?>'></a>
                        </td>
                        </tr>
                        <?php  } } ?>
                </table>
">查看全部</a>
<?php  } ?>
                             </td>
                         </tr>
                         <?php  } ?>

                         <tr>
                             <td style='width:80px'>维权编号：</td>
                             <td><?php  echo $refund['refundno'];?></td>
                         </tr>

                         <tr>
                             <td style='width:80px'>订单编号：</td>
                             <td><a href="<?php  echo webUrl('order/detail', array('id' => $item['id']))?>" target="_blank" title=""><?php  echo $item['ordersn'];?></a></td>
                         </tr>

						 <tr>
							<td>订单金额：</td>
              <?php  if($item['invoice']==1) { ?>
              <td>￥<?php  echo number_format($item['invoiceprice'],2)?></td>
              <?php  } else { ?>
							<td>￥<?php  echo number_format($item['price'],2)?></td>
              <?php  } ?>
						 </tr>

                         <tr>
                             <td>买家：</td>
                             <td><?php  echo $member['nickname'];?> <a data-toggle='popover' data-html='true' data-trigger="hover" data-placement='right'
                                                          data-content="<table style='width:100%;'>
                <tr>
                    <td  style='border:none;text-align:right;' colspan='2'><img src='<?php  echo $member['avatar'];?>' style='width:100px;height:100px;padding:1px;border:1px solid #ccc' /></td>
                </tr>
                                <tr>
                    <td  style='border:none;text-align:right;'>昵称：</td>
                    <td  style='border:none;text-align:right;'><?php  echo $member['nickname'];?></td>
                </tr>
                <tr>
                    <td  style='border:none;text-align:right;'>姓名：</td>
                    <td  style='border:none;text-align:right;'><?php  echo $member['realname'];?></td>
                </tr>
                <tr>
                    <td  style='border:none;text-align:right;'>ID：</td>
                    <td  style='border:none;text-align:right;'><?php  echo $member['id'];?></td>
                </tr>
                <tr>
                    <td  style='border:none;text-align:right;'>手机号：</td>
                    <td  style='border:none;text-align:right;'><?php  echo $member['mobile'];?></td>
                </tr>
                <tr>
                    <td  style='border:none;text-align:right;'>微信号：</td>
                    <td  style='border:none;text-align:right;'><?php  echo $member['weixin'];?></td>
                </tr>
                </table>
"><i class='icow icow-help' style="font-size: 13px;color: #2f3434;margin-left: -3px"></i></a></td>
                         </tr>

                         <tr>
                             <td style='width:80px'>付款时间：</td>
                             <td><?php  echo date('Y-m-d H:i:s', $item['paytime'])?></td>
                         </tr>

                         <tr>
                             <td style='width:80px'>付款方式：</td>
                             <td> <?php  if($item['paytype'] == 0) { ?>未支付<?php  } ?>
                                 <?php  if($item['paytype'] == 1) { ?>余额支付<?php  } ?>
                                 <?php  if($item['paytype'] == 11) { ?>后台付款<?php  } ?>
                                 <?php  if($item['paytype'] == 21) { ?>微信支付<?php  } ?>
                                 <?php  if($item['paytype'] == 22) { ?>支付宝支付<?php  } ?>
                                 <?php  if($item['paytype'] == 23) { ?>银联支付<?php  } ?>
                                 <?php  if($item['paytype'] == 3) { ?>货到付款<?php  } ?>
                             </td>
                         </tr>

                     </table>

                </div>
			</div>
		</div>

        <div class="order-container-right" >
            <div class='panel-body' style='height:450px;' >

                <div class='row'>
                    <div class='col-sm-3 control-label'>维权状态: </div>
                    <div class="col-sm-9 col-xs-12">
                        <h3 class="form-control-static">
                            <?php  if($refund['status'] == -2) { ?><span class="text-default">客户取消<?php  echo $r_type[$refund['rtype']];?></span>

                            <?php  } else if($refund['status'] == -1) { ?><span class="text-default">已拒绝<?php  echo $r_type[$refund['rtype']];?></span>

                            <?php  } else if($refund['status'] == 0) { ?><span class="text-warning">等待商家处理申请</span>

                            <?php  } else if($refund['status'] == 1) { ?><span class="text-default"><?php  echo $r_type[$refund['rtype']];?>完成</span>

                            <?php  } else if($refund['status'] == 3) { ?><span class="text-warning">等待客户退回物品</span>

                            <?php  } else if($refund['status'] == 4) { ?><span class="text-warning">客户退回物品，等待商家重新发货</span>

                            <?php  } else if($refund['status'] == 5) { ?><span class="text-warning">等待客户收货</span><?php  } ?>

                        </h3>
                    </div>
                </div>


                <div class="form-group">
                    <label class="col-sm-3 control-label"></label>
                    <div class="col-sm-9 col-xs-12">
                        <p class="form-control-static ops">
                            <?php  if($item['merchid'] == 0) { ?>
                                <?php  if(($refund['status'] == 0 || $refund['status'] > 1)) { ?>
                                <a class="btn btn-primary btn-xs" data-toggle="ajaxModal"  href="<?php  echo webUrl('order/op/refund/submit', array('id' => $item['id'],'refundid' => $refund['id']))?>">处理申请</a>
                                <?php  } ?>
                            <?php  } ?>
                        </p>
                    </div>
                </div>

                <?php  if($refund['rtype']>0 && !empty($refund['expresssn'])) { ?>
                <div class='row'>
                    <div class='col-sm-3 control-label' style='padding-top:10px;'>
                        客户退回物品 :
                    </div>
                    <div class="col-sm-9 col-xs-12">
                            <div class="form-control-static">
                                快递公司: <?php  if(empty($refund['expresscom'])) { ?>其他快递<?php  } else { ?><?php  echo $refund['expresscom'];?><?php  } ?><br>
                                快递单号: <?php  echo $refund['expresssn'];?> &nbsp;&nbsp;&nbsp;<a class='op' data-toggle="ajaxModal" href="<?php  echo webUrl('util/express', array('id' => $item['id'],'express'=>$refund['express'],'expresssn'=>$refund['expresssn']))?>">查看物流</a><br>
                                退回时间: <?php  echo date('Y-m-d H:i:s', $refund['sendtime'])?>
                            </div>
                    </div>
                </div>
                <?php  } ?>

                <?php  if($refund['rtype']==2 && !empty($refund['rexpresssn'])) { ?>
                <div class='row'>
                    <div class='col-sm-3 control-label' style='padding-top:10px;'>
                        商家寄出物品 :
                    </div>
                    <div class="col-sm-9 col-xs-12">
                        <div class="form-control-static">
                            快递公司: <?php  if(empty($refund['rexpresscom'])) { ?>其他快递<?php  } else { ?><?php  echo $refund['rexpresscom'];?><?php  } ?><br>
                            快递单号: <?php  echo $refund['rexpresssn'];?> &nbsp;&nbsp;&nbsp;<a class='op' data-toggle="ajaxModal" href="<?php  echo webUrl('util/express', array('id' => $item['id'],'express'=>$refund['rexpress'],'expresssn'=>$refund['rexpresssn']))?>">查看物流</a><br>
                            发货时间: <?php  echo date('Y-m-d H:i:s', $refund['returntime'])?>
                        </div>
                    </div>
                </div>
                <?php  } ?>


                <!--<div class='order-tips'>-->
                    <!--<div class='row order-tips-title'>友情提醒</div>-->
                      <!--<div class='row order-tips-row'>订单为货到付款，请您务必联系买家确认后再进行发货</div>-->
                <!--</div>-->

            </div>

        </div>
 
</div>

       <br>
       <div >
           <!--<div class="panel-heading">-->
               <!--<span>商品信息</span>-->
           <!--</div>-->
           <div class=" table-responsive goods_list" style="overflow: hidden;">
               <table class="table" style="border: 1px solid #efefef;">
                   <thead class="navbar-inner">
                   <tr>
                       <th style="width:15%;text-align: left">商品标题</th>
                       <th style="width:10%;">商品规格</th>
                       <th style="width:10%;">商品编号</th>
                       <th style="width:10%;">商品条码</th>
                       <th style="width:10%;">单价(元)</th>
                       <th style="width:5%;">数量</th>
                       <th style="width:15%;">折扣前/折扣后(元)</th>
                       <!--<th style="width:5%;">操作</th>-->
                   </tr>
                   </thead>
                   <?php  if(is_array($item['goods'])) { foreach($item['goods'] as $goods) { ?>
                   <tr class="trorder">
                       <td style="text-align: left">
                           <?php  if($category[$goods['pcate']]['name']) { ?>
                           <span class="text-error">[<?php  echo $category[$goods['pcate']]['name'];?>] </span><?php  } ?><?php  if($children[$goods['pcate']][$goods['ccate']]['1']) { ?>
                           <span class="text-info">[<?php  echo $children[$goods['pcate']][$goods['ccate']]['1'];?>] </span>
                           <?php  } ?>
                           <?php  echo $goods['title'];?>
                       </td>
                       <td><?php  if(!empty($goods['optionname'])) { ?><span class="label label-info"><?php  echo $goods['optionname'];?></span><?php  } ?></td>
                       <td><?php  echo $goods['goodssn'];?></td>
                       <td><?php  echo $goods['productsn'];?></td>
                       <td><?php  echo $goods['marketprice'];?></td>
                       <td><?php  echo $goods['total'];?></td>
                       <td><?php  echo $goods['orderprice'];?>/<?php  echo $goods['realprice'];?>
                           <?php  if(intval($goods['changeprice'])!=0) { ?>
                           <br/>(改价<?php  if($goods['changeprice']>0) { ?>+<?php  } ?><?php  echo number_format(abs($goods['changeprice']),2)?>)
                           <?php  } ?>
                       </td>
                       <!--td>
                           <a href="<?php  echo webUrl('goods/edit', array('id' => $goods['id']))?>" class="btn btn-default btn-sm" title="编辑"><i class="fa fa-edit"></i></a>&nbsp;&nbsp;
                       </td-->
                   </tr>
                   <?php  } } ?>
                   <tr class="trorder">
                       <td colspan="2" style="padding-left: 20px"> <?php  if($item['ispackage']) { ?>
                           <span class="text-danger" style="color:red;">（套餐优惠价：&yen;<?php  echo number_format($item['price'],2)?><?php  if($item['dispatchprice'] ) { ?>，含运费：&yen;<?php  echo number_format($item['dispatchprice'],2)?><?php  } ?>）</span>
                           <?php  } ?>
                       </td>
                       <?php $colspan=$item['ispackage']?4:5?>
                       <?php  if($showdiyform) { ?>
                       <?php  $colspan++?>
                       <?php  } ?>
                       <td colspan="<?php  echo $colspan;?>" style="padding-right: 60px;text-align: right">
                           <div class="price">
                               <p> <span class="price-inner">商品小计：</span><span style="font-weight: bold">￥<?php  echo number_format( $item['goodsprice'] ,2)?></span></p>
                               <p><span class="price-inner">运费：</span>￥<?php  echo number_format( $item['olddispatchprice'],2)?></p>
                               <?php  if(!$item['ispackage']) { ?>
                                   <?php  if($item['taskdiscountprice']>0) { ?>
                                   <p><span class="price-inner">任务活动优惠：</span><span class="text-danger">-￥<?php  echo number_format( $item['taskdiscountprice'],2)?></span></p>
                                   <?php  } ?>
                                   <?php  if($item['lotterydiscountprice']>0) { ?>
                                   <p><span class="price-inner">游戏活动优惠：</span><span class="text-danger">-￥<?php  echo number_format( $item['lotterydiscountprice'],2)?></span></p>
                                   <?php  } ?>
                                   <?php  if($item['discountprice']>0) { ?>
                                   <p><span class="price-inner">会员折扣：</span><span class="text-danger">-￥<?php  echo number_format( $item['discountprice'],2)?></span></p>
                                   <?php  } ?>
                                   <?php  if($item['deductprice']>0) { ?>
                                   <p><span class="price-inner"><?php  echo $_W['shopset']['trade']['credittext'];?>抵扣：</span><span class="text-danger">-￥<?php  echo number_format( $item['deductprice'],2)?></span></p>
                                   <?php  } ?>
                                   <?php  if($item['deductcredit2']>0) { ?>
                                   <p><span class="price-inner">余额抵扣：</span><span class="text-danger">-￥<?php  echo number_format( $item['deductcredit2'],2)?></span></p>
                                   <?php  } ?>
                                   <?php  if($item['deductenough']>0) { ?>
                                   <p><span class="price-inner">满额立减：</span><span class="text-danger">-￥<?php  echo number_format( $item['deductenough'],2)?></span></p>
                                   <?php  } ?>
                                   <?php  if($item['couponprice']>0) { ?>
                                   <p><span class="price-inner">优惠券优惠：</span><span class="text-danger">-￥<?php  echo number_format( $item['couponprice'],2)?></span></p>
                                   <?php  } ?>
                               <?php  } ?>

                               <?php  if(intval($item['changeprice'])!=0) { ?>
                               <p>
                                   <span class="price-inner">卖家改价：</span>
                                   <span style='<?php  if(0<$item['changeprice']) { ?>color:green<?php  } else { ?>color:red<?php  } ?>'><?php  if(0<$item['changeprice']) { ?>+<?php  } else { ?>-<?php  } ?>￥<?php  echo number_format(abs($item['changeprice']),2)?></span>
                               </p>
                               <?php  } ?>

                               <?php  if(intval($item['changedispatchprice'])!=0) { ?>
                               <p>
                                   <span class="price-inner">卖家改运费：</span>
                                   <span style='<?php  if(0<$item['changedispatchprice']) { ?>color:green<?php  } else { ?>color:red<?php  } ?>'><?php  if(0<$item['changedispatchprice']) { ?>+<?php  } else { ?>-<?php  } ?>￥<?php  echo abs($item['changedispatchprice'])?></span>
                               </p>
                               <?php  } ?>

                               <p><span class="price-inner">实付款：</span><span style="font-size: 14px;font-weight: bold;color: #e4393c">￥<?php  echo number_format($item['price'],2)?><?php  if($item['isprepay']&&$item['isprepaysuccess']) { ?>(包含尾款：<?php  echo number_format($item['balance'],2)?>)<?php  } ?></span></p>
                           </div>
                       </td>
                   </tr>
               </table>
           </div>
       </div>
   </form>
</div>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>

<!--efwww_com-->