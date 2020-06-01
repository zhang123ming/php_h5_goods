<?php defined('IN_IA') or exit('Access Denied');?> <?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>
 <style>
    .trorder td{
        border-top:none !important;
     }
 </style>

<div class="page-header">
    当前位置：<span class="text-primary"><?php  echo $applytitle;?>提现申请</span>
</div>

<div class="page-content">
    <form action="./index.php" method="get" class="form-horizontal  table-search" role="form" id="form1">
        <input type="hidden" name="c" value="site" />
        <input type="hidden" name="a" value="entry" />
        <input type="hidden" name="m" value="ewei_shopv2" />
        <input type="hidden" name="do" value="web" />
        <input type="hidden" name="r" value="commission.apply" />
        <input type="hidden" name="status" value="<?php  echo $status;?>" />
        <div class="page-toolbar m-b-sm m-t-sm">
            <div class="col-sm-3">
                <div class='input-group'>
                    <?php  echo tpl_form_field_daterange('time', array('starttime'=>date('Y-m-d H:i', $starttime),'endtime'=>date('Y-m-d H:i', $endtime)),true);?>

                </div>

            </div>
            <div class="col-sm-3">
                <div class="input-group">
                    <input type="number" class="form-control"  name="psize" value="<?php  echo $_GPC['psize'];?>" placeholder="请输入每页条数"/>
                </div>
            </div>

            <div class="col-sm-6 pull-right">
                <div class="input-group">
                    <div class="input-group-select">
                        <select name='timetype'   class='form-control' >
                            <option value=''>不按时间</option>
                            <?php  if($status>=1) { ?><option value='applytime' <?php  if($_GPC['timetype']=='applytime') { ?>selected<?php  } ?>>申请时间</option><?php  } ?>
                            <?php  if($status>=2) { ?><option value='checktime' <?php  if($_GPC['timetype']=='checktime') { ?>selected<?php  } ?>>审核时间</option><?php  } ?>
                            <?php  if($status>=3) { ?><option value='paytime' <?php  if($_GPC['timetype']=='paytime') { ?>selected<?php  } ?>>打款时间</option><?php  } ?>
                        </select>
                    </div>
                    <div class="input-group-select">
                         <select name='agentlevel' class='form-control  input-sm select-md' style="width:100px;">
                             <option value=''>等级</option>
                             <?php  if(is_array($agentlevels)) { foreach($agentlevels as $level) { ?>
                             <option value='<?php  echo $level['id'];?>' <?php  if($_GPC['agentlevel']==$level['id']) { ?>selected<?php  } ?>><?php  echo $level['levelname'];?></option>
                             <?php  } } ?>
                         </select>
                    </div>
                    <div class="input-group-select">
                        <select name='searchfield'  class='form-control  input-sm select-md'   style="width:110px;"  >
                            <option value='member' <?php  if($_GPC['searchfield']=='member') { ?>selected<?php  } ?>>会员信息</option>
                            <option value='applyno' <?php  if($_GPC['searchfield']=='applyno') { ?>selected<?php  } ?>>提现单号</option>
                        </select>
                    </div>
                    <input type="text" class="form-control input-sm"  name="keyword" value="<?php  echo $_GPC['keyword'];?>" placeholder="请输入关键词"/>
                    <span class="input-group-btn">
                        <button class="btn  btn-primary" type="submit"> 搜索</button>
                           <?php if(cv('commission.apply.export')) { ?>
                                <button type="submit" name="export" value="1" class="btn btn-success ">导出</button>
                            <?php  } ?>
                    </span>
                </div>

            </div>
        </div>
    </form>

    <?php  if(count($list)>0) { ?>
    <?php  $col=7?>
    <table class="table ">
        <thead class="navbar-inner">
        <tr style="background: #f7f7f7">
            <th>提现单号</th>
            <th>分销等级</th>
            <th style="width:230px;">提现方式</th>
            <th>申请佣金</th>
            <th><?php  if($status==3) { ?>实际到账<?php  } else { ?>实际佣金<?php  } ?></br>已发送金额 (微信红包)</th>
            <th>提现手续费</th>
            <th>手续费率</th>
            <?php  if($status==-1) { ?>
            <?php  $col++?>
            <th>无效时间</th>
            <?php  } else if($status>=3) { ?>
            <?php  $col++?>
            <th>打款时间</th>
            <?php  } else if($status>=2) { ?>
            <?php  $col++?>
            <th>审核时间</th>
            <?php  } else if($status>=1) { ?>
            <?php  $col++?>
            <th>申请时间</th>
            <?php  } ?>
        </tr>
        <tr></tr>
        </thead>
        <tbody>
        <?php  if(is_array($list)) { foreach($list as $row) { ?>
        <tr class="trorder" style="border-bottom: none">
            <td colspan="<?php  echo $col;?>" style="background: #f7f7f7">
               提现单号： <?php  echo $row['applyno'];?>
            </td>
        </tr>
        <tr class="trorder" style="border-top: none;">
            <td>
                <?php if(cv('member.list.view')) { ?>
                <a  href="<?php  echo webUrl('member/list/detail',array('id' => $row['mid']));?>" target='_blank'>
                    <img class="radius50" src='<?php  echo tomedia($row['avatar'])?>' style='width:30px;height:30px;padding1px;border:1px solid #ccc'  onerror="this.src='../addons/ewei_shopv2/static/images/noface.png'"/> <?php  echo $row['nickname'];?><br />姓名：<?php  echo $row['applyrealname'];?>
                </a>
                <?php  } else { ?>
                <img class="radius50" src='<?php  echo tomedia($row['avatar'])?>' style='width:30px;height:30px;padding1px;border:1px solid #ccc' onerror="this.src='../addons/ewei_shopv2/static/images/noface.png'" /> <?php  echo $row['nickname'];?><br />姓名：<?php  echo $row['applyrealname'];?>
                <?php  } ?>
                <br/>
            </td>
            <td><?php  echo $row['levelname'];?></td>
            <td>
                <?php  if($row['type']==0) { ?>
                <i class="icow icow-yue text-warning"></i>余额
                <?php  } else if($row['type']==1) { ?>
               <i class="icow icow-weixinzhifu text-success"></i> 微信钱包
                <?php  } else if($row['type']==2) { ?>
               <i class="icow icow-zhifubaozhifu text-primary"></i>支付宝
                <div><?php  echo $row['realname'];?></div><div><?php  echo $row['alipay'];?></div>
                <?php  } else if($row['type']==3) { ?>
                <i class="text-primary icow icow-icon"></i>银行卡
                <div><?php  echo $row['bankname'];?></div><div><?php  echo $row['bankcard'];?></div>
                <?php  } ?>
            </td>
            <td class="text-danger"><?php  echo $row['commission'];?></td>
            <?php  if($set['commissionsettletype']==1) { ?>
                <td><?php  echo $row['commission'];?></td>
            <?php  } else { ?>
                <td class="text-danger">
                    <?php  if($row['deductionmoney'] > 0) { ?>
                    <?php  echo $row['realmoney'];?>
                    <?php  } else { ?>
                    <?php  echo $row['commission'];?>
                    <?php  } ?>
                    </br>
                    <?php  if((float)$row['sendmoney'] != 0) { ?><?php  echo $row['sendmoney'];?><?php  } else { ?><?php  } ?>
                </td>
            <?php  } ?>

            <td><a data-toggle='popover' data-content="
                             手续费减免范围: <br/><?php  echo $row['beginmoney'];?>~<?php  echo $row['endmoney'];?>
                                " data-html="true" data-trigger="hover"><?php  echo $row['deductionmoney'];?></a></td>
            <td><?php  echo $row['charge'];?>%</td>
            <td >
                <?php  if($row['status']!=1) { ?><a data-toggle='popover' data-content="
                             <?php  if($status>=1 && $row['status']!=1) { ?>申请时间: <br/><?php  echo date('Y-m-d',$row['applytime'])?><br/><?php  echo date('H:i',$row['applytime'])?><?php  } ?>
                             <?php  if($status>=2 && $row['status']!=2) { ?><br/>审核时间: <br/><?php  echo date('Y-m-d',$row['checktime'])?><br/><?php  echo date('H:i',$row['checktime'])?><?php  } ?>
                             <?php  if($status>=3 && $row['status']!=3) { ?><br/>付款时间: <br/><?php  echo date('Y-m-d',$row['paytime'])?><br/><?php  echo date('H:i',$row['paytime'])?><?php  } ?>
                             <?php  if($status==-1) { ?><br/>无效时间: <br/><?php  echo date('Y-m-d',$row['invalidtime'])?><br/><?php  echo date('H:i',$row['invalidtime'])?><?php  } ?>

                                " data-html="true" data-trigger="hover"><?php  } ?>
                <?php  if($status>=1) { ?>
                <?php  echo date('Y-m-d',$row['applytime'])?> <?php  echo date('H:i',$row['applytime'])?>
                <?php  } else if($status>=2) { ?>
                <?php  echo date('Y-m-d',$row['checktime'])?> <?php  echo date('H:i',$row['applytime'])?>
                <?php  } else if($status>=3) { ?>
                <?php  echo date('Y-m-d',$row['paytime'])?> <?php  echo date('H:i',$row['paytime'])?>
                <?php  } else if($status==-1) { ?>
                <?php  echo date('Y-m-d',$row['invalidtime'])?> <?php  echo date('H:i',$row['invalidtime'])?>
                <?php  } ?>
                <?php  if($row['status']!=1) { ?><i class="fa fa-question-circle"></i></a><?php  } ?>
            </td>
        </tr>
        <form <?php if(cv('commission.apply.check|commission.apply.pay|commission.apply.cancel')) { ?>action="" method='post'<?php  } ?> class='form-horizontal form-validate'>

            <input type="hidden" name="c" value="site" />
            <input type="hidden" name="a" value="entry" />
            <input type="hidden" name="m" value="ewei_shopv2" />
            <input type="hidden" name="do" value="web" />
            <input type="hidden" name="r" value="commission.apply" />
            <input type="hidden" name="id" value="<?php  echo $row['id'];?>" />
            <input type="hidden" name="commission" value="<?php  echo $row['commission'];?>" />
            <input type="hidden" name="status" value="<?php  echo $row['status'];?>" />
            <tr class="trorder" style="border-bottom:none">
                <td colspan="<?php  echo $col;?>" style="background: #f7f7f7;height:60px;">
                   
                        <!-- <div class="isshow" style="float:left;">
                            <a class=' btn btn-success  btn-sm btn-op btn-operation' >
                                  <span data-toggle="tooltip" data-placement="top" title="">
                                        选择操作
                                   </span>
                            </a>
                        </div> -->
                        <!-- <div class="menu_list" style="display:none;position:absolute;float:left;margin-left:150px;"> -->
                        <div class="menu_list" style="position:absolute;float:left;">
                            <?php  if($row['status']==1) { ?>
                            <?php if(cv('commission.apply.refuse')) { ?>
                            <input type="submit" name="submit_refuse" value="驳回申请" class="btn btn-danger" onclick='return refuse()'/>
                            <?php  } ?>
                            <?php if(cv('commission.apply.check')) { ?>
                            <input type="submit" name="submit_check" value="提交审核" class="btn btn-primary" onclick='return check()'/>
                            <?php  } ?>
                            <?php  } ?>

                            <?php  if($row['status']==2) { ?>

                            <?php if(cv('commission.apply.cancel')) { ?>
                            <input type="submit" name="submit_cancel" value="重新审核" class="btn btn-default"  onclick='return cancel()'/>
                            <?php  } ?>
                            <?php if(cv('commission.apply.pay')) { ?>
                            <?php  if(empty($row['type'])) { ?>
                            <input type="submit" name="submit_pay" value="打款到余额账户" class="btn btn-primary"  style='margin-left:10px;' onclick='return pay_credit()'/>
                            <?php  } else if($row['type'] == 1) { ?>
                            <input type="submit" name="submit_pay" value="打款到微信钱包" class="btn btn-primary" style='margin-left:10px;' onclick='return pay_weixin()'/>
                            <?php  } else if($row['type'] == 2) { ?>
                            <input type="submit" name="submit_pay" value="确认打款到支付宝" class="btn btn-primary" style='margin-left:10px;' onclick='return pay_alipay()'/>
                            <?php  } else if($row['type'] == 3) { ?>
                            <input type="submit" name="submit_pay" value="确认打款到银行卡" class="btn btn-primary" style='margin-left:10px;' onclick='return pay_bank()'/>

                            <?php  } ?>
                            <input type="submit" name="submit_pay" value="手动处理" class="btn btn-warning" style='margin-left:10px;' onclick='return payed()'/>
                            <?php  } ?>
                            <?php  } ?>
                            <?php  if($row['status']==-1) { ?>
                            <?php if(cv('commission.apply.cancel')) { ?>
                            <input type="submit" name="submit_cancel" value="重新审核" class="btn btn-default"  onclick='return cancel()'/>
                            <?php  } ?>

                            <?php  } ?>
                            <?php  if($row['status']==3) { ?>
                            <input type="button" class="btn btn-default" name="submit" onclick="history.go(-1)" value="返回" style='margin-left:10px;' /> 
                            <?php  } ?>
                        </div>
                </td>
            </tr>
        </form>
        <tr></tr>
        <?php  } } ?>
        </tbody>
        <tfoot style="border: none;">
            <tr>
                <td colspan="<?php  echo $col;?>">
                    <?php  if($_GPC['status']>0) { ?>
                    <div style="float: left">
                        <div style="float: left;">
                            <?php  if($_GPC['status']==3) { ?>实际到账：<?php  } else { ?>实际佣金：<?php  } ?>
                            <i class="icow icow-yue text-warning"></i>余额 <span class="text-danger"><?php echo $count1?:0?></span>元&emsp;&emsp;
                        </div>
                        <div style="float: left;">
                            <i class="icow icow-weixinzhifu text-success"></i>微信钱包 <span class="text-danger"><?php echo $count2?:0?></span>元&emsp;&emsp;
                        </div>
                        <div style="float: left;">
                            <i class="icow icow-zhifubaozhifu text-primary"></i>支付宝 <span class="text-danger"><?php echo $count3?:0?></span>元&emsp;&emsp;
                        </div>
                        <div style="float: left;">
                            <i class="text-primary icow icow-icon"></i>银行卡 <span class="text-danger"><?php echo $count4?:0?></span>元&emsp;&emsp;
                        </div>
                    </div>
                    <?php  } ?>
                    <div style="float: right"><?php  echo $pager;?></div>
                </td>
            </tr>
        </tfoot>
    </table>
    <?php  } else { ?>
    <div class='panel panel-default'>
        <div class='panel-body' style='text-align: center;padding:30px;'>
            暂时没有任何<?php  echo $applytitle;?>提现申请!
        </div>
    </div>
    <?php  } ?>
</div>
<script type="text/javascript">
    // $(".isshow").click(function(){
    //     if($(this).next().css("display")=="none"){
    //         $(this).next().show();
    //     }else{
    //         $(this).next().hide();
    //     }
    // });
</script>


<script language='javascript'>
    function check() {
        var pass = true;
        if (!pass) {
            return false;
        }
        $(':input[name=r]').val('commission.apply.check');
        return confirm('确认已核实成功并要提交?\r\n(提交后还可以撤销审核状态, 申请将恢复到申请状态)');
    }
    function refuse() {
        $(':input[name=r]').val('commission.apply.refuse');
        return confirm('确认驳回申请?\r\n( 分销商可以重新提交提现申请)');
    }
    function cancel() {
       $(':input[name=r]').val('commission.apply.cancel');
        return confirm('确认撤销审核?\r\n( 所有状态恢复到申请状态)');
    }
    function pay_credit() {
        $(':input[name=r]').val('commission.apply.pay');
        return confirm('确认打款到此用户的余额账户?');
    }
    function pay_weixin() {
        $(':input[name=r]').val('commission.apply.pay');
        return confirm('确认打款到此用户的微信钱包?');
    }
    function pay_alipay() {
        $(':input[name=r]').val('commission.apply.pay');
        return confirm('确认打款到此用户的支付宝? 姓名:' + $("#realname").html() + ' 支付宝帐号:' + $("#alipay").html());
    }

    function pay_bank() {
        $(':input[name=r]').val('commission.apply.pay');
        return confirm('确认打款到此用户的银行卡? ' + $("#bankname").html() + ' 姓名: 卡号:' + $("#bankcard").html());
    }

    function payed() {
        $(':input[name=r]').val('commission.apply.payed');
        return confirm('选择手动处理 , 系统不进行任何打款操作!\r\n请确认你已通过线下方式为用户打款!!!\r\n是否进行手动处理 ');
    }
</script>

<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>
<!-- yi fu yuan ma wang -->