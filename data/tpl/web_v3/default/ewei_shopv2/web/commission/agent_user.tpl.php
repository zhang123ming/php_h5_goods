<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>
<style type='text/css'>
    .moresearch { padding:0px 10px;}
    .moresearch .col-sm-2 {
        padding:0 5px
    }
    .popover{
        width:170px;
        font-size:12px;
        line-height: 21px;
        color: #0d0706;
    }
    .popover span{
        color: #b9b9b9;
    }
    .nickname{
        display: inline-block;
        max-width:200px;
        overflow: hidden;
        text-overflow:ellipsis;
        white-space: nowrap;
        vertical-align: middle;
    }

    .tooltip-inner{
        border:none;
    }
    .info{
        height: 100%;
        width:370px;
        float:left;
        border-right:1px solid #efefef;
        padding: 40px 20px;
        line-height: 25px;
    }
    .info i{
        display: inline-block;
        width:80px;
        text-align: right;
        color: #999;
    }
</style>
<div class="page-header">
    当前位置：<span class="text-primary">推广下线 <small>总数: <span class='text-danger'><?php  echo $total;?></span></small></span>
</div>
<div class="page-content">
    <div class="page-sub-toolbar">
        <span class=''>
            <a class="btn btn-default  btn-sm" href="<?php  echo referer()?>">返回列表</a>
        </span>
    </div>
<div style="height: 180px;border: 1px solid #efefef;margin-bottom: 30px">
        <div class="info">
            <img class="pull-left" src='<?php  echo $member['avatar'];?>' style='width:100px;height:100px;border:1px solid #ccc;padding:1px'  onerror="this.src='../addons/ewei_shopv2/static/images/noface.png'"/>
           <div class="pull-left">
              <i> 昵称：</i><?php  echo $member['nickname'];?><br/>
              <i> 姓名：</i><?php  echo $member['realname'];?> <br/>
               <i>手机号：</i><?php  echo $member['mobile'];?> <br/>
               <i>微信号：</i> <?php  echo $member['weixin'];?><br/>
           </div>
        </div>
        <div class="info" style="text-align: center">
            <p class="pull-left" style="color: #999;width: 120px;"> 下级分销商：</p>
            <div  class="pull-left">
                总共：<span class="text-danger"><?php  echo $total;?></span> 人<br/>
                <?php  if($this->set['level']>=1) { ?>一级：<span class="text-danger"><?php  echo $level1;?> </span>  人<?php  } ?><br/>
                <?php  if($this->set['level']>=2) { ?>二级：<span class="text-danger"><?php  echo $level2;?></span>  人<?php  } ?><br/>
                <?php  if($this->set['level']>=3) { ?>三级： <span class="text-danger"><?php  echo $level3;?></span> 人<?php  } ?><br/>
                点击： <span class="text-danger"><?php  echo $member['clickcount'];?></span> 次
            </div>
        </div>
        <div style="padding: 40px 20px;float: left">
            下级会员(非分销商): <span style='color:red'><?php  echo $level11;?></span> 人    <br/>
        </div>
        <div style="clear: both"></div>
</div>

<form action="./index.php" method="get" class="form-horizontal" role="form" id="form1">
    <input type="hidden" name="c" value="site" />
    <input type="hidden" name="a" value="entry" />
    <input type="hidden" name="m" value="ewei_shopv2" />
    <input type="hidden" name="do" value="web" />
    <input type="hidden" name="r" value="commission.agent.user" />
    <input type="hidden" name="id" value="<?php  echo $agentid;?>" />
    <div class="page-toolbar m-b-sm m-t-sm ">
        <div class="col-sm-12 pull-right">
            <span class="pull-left">
                 <?php  echo tpl_daterange('time', array('sm'=>true, 'placeholder'=>'成为分销商时间'),true);?>
            </span>
            <div class="input-group">
                <span class="input-group-select">
                     <select name='isagent'  class='form-control  input-sm'  >
                         <option value=''>是否分销商</option>
                         <option value='0' <?php  if($_GPC['isagent']=='0') { ?>selected<?php  } ?>>不是</option>
                         <option value='1' <?php  if($_GPC['isagent']=='1') { ?>selected<?php  } ?>>是</option>
                     </select>
                </span>
                <span class="input-group-select">
                     <select name='level' class='form-control  input-sm' >
                         <option value=''>下线层级</option>
                         <?php  if($this->set['level']>=1) { ?><option value='1' <?php  if($_GPC['level']=='1') { ?>selected<?php  } ?>>一级下线</option><?php  } ?>
                         <?php  if($this->set['level']>=2) { ?><option value='2' <?php  if($_GPC['level']=='2') { ?>selected<?php  } ?>>二级下线</option><?php  } ?>
                         <?php  if($this->set['level']>=3) { ?><option value='3' <?php  if($_GPC['level']=='3') { ?>selected<?php  } ?>>三级下线</option><?php  } ?>
                     </select>
                </span>
                <span class="input-group-select">
                     <select name='followed' class='form-control  input-sm'>
                         <option value=''>关注</option>
                         <option value='0' <?php  if($_GPC['followed']=='0') { ?>selected<?php  } ?>>未关注</option>
                         <option value='1' <?php  if($_GPC['followed']=='1') { ?>selected<?php  } ?>>已关注</option>
                         <option value='2' <?php  if($_GPC['followed']=='2') { ?>selected<?php  } ?>>取消关注</option>
                     </select>
                </span>
                 <span class="input-group-select">
                     <select name='agentlevel' class='form-control  input-sm'>
                         <option value=''>等级</option>
                         <?php  if(is_array($agentlevels)) { foreach($agentlevels as $level) { ?>
                         <option value='<?php  echo $level['id'];?>' <?php  if($_GPC['agentlevel']==$level['id']) { ?>selected<?php  } ?>><?php  echo $level['levelname'];?></option>
                         <?php  } } ?>
                     </select>
                </span>
                <span class="input-group-select">
                    <select name='isagentblack'  class='form-control  input-sm'    >
                        <option value=''>黑名单</option>
                        <option value='0' <?php  if($_GPC['isagentblack']=='0') { ?>selected<?php  } ?>>否</option>
                        <option value='1' <?php  if($_GPC['isagentblack']=='1') { ?>selected<?php  } ?>>是</option>
                    </select>
                </span>
                <span class="input-group-select">
                    <select name='searchfield'  class='form-control'   style="width:120px;"  >
                        <option value='member' <?php  if($_GPC['searchfield']=='member') { ?>selected<?php  } ?>>下线信息</option>
                        <option value='parent' <?php  if($_GPC['searchfield']=='parent') { ?>selected<?php  } ?>>推荐人信息</option>
                    </select>
                </span>
                <input type="text" class="form-control input-sm"  name="keyword" value="<?php  echo $_GPC['keyword'];?>" placeholder="昵称/姓名/手机号"/>
				 <span class="input-group-btn">
                     <button class="btn  btn-primary" type="submit"> 搜索</button>
                     <button type="submit" name="export" value="1" class="btn btn-success ">导出</button>

				</span>
            </div>

        </div>
    </div>
    <?php  if(count($list)>0) { ?>

    <table class="table table-hover table-responsive">
        <thead class="navbar-inner" >
        <tr>

            <th>粉丝</th>
            <th>姓名<br/>手机号码</th>
            <th>等级</th>
            <th>消费总额</th>
            <th>下级分销商</th>
            <th>注册时间</th>
            <th>审核时间</th>
            <th>状态</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        <?php  if(is_array($list)) { foreach($list as $row) { ?>
        <tr>
            <td style="overflow: visible">
                <div rel="pop" data-content="
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
                       <?php  } ?></br>
                       [<?php  echo $row['agentid'];?>]<?php  if(empty($row['parentname'])) { ?>未更新<?php  } else { ?><?php  echo $row['parentname'];?><?php  } ?>
					   <?php  } ?></br>
					   <span>是否关注：</span>
                         <?php  if(empty($row['followed'])) { ?>
                                <?php  if(empty($row['uid'])) { ?>
                               未关注
                                <?php  } else { ?>
                               取消关注
                                <?php  } ?>
                                <?php  } else { ?>
                               已关注
                                <?php  } ?> </br>
                            <span>状态:</span>  <?php  if($row['isblack']==1) { ?>黑名单<?php  } else { ?>正常<?php  } ?>

					   ">
                    <?php if(cv('member.list.view')) { ?>
                    <a href="<?php  echo webUrl('member/list/detail',array('id' => $row['id']));?>" title='会员信息' target='_blank' style="display: flex">
                        <span data-toggle='tooltip' title='<?php  echo $row['nickname'];?>'>
                        <?php  if(!empty($row['avatar'])) { ?>
                        <img class="radius50" src='<?php  echo tomedia($row['avatar'])?>' style='width:30px;height:30px;padding1px;border:1px solid #ccc' onerror="this.src='../addons/ewei_shopv2/static/images/noface.png'"/>
                        <?php  } ?>
                        <?php  if(empty($row['nickname'])) { ?>未更新<?php  } else { ?><?php  echo $row['nickname'];?><?php  } ?>
                        </span>
                    </a>
                    <?php  } else { ?>
                        <span data-toggle='tooltip' title='<?php  echo $row['nickname'];?>'>
                        <?php  if(!empty($row['avatar'])) { ?>
                        <img class="radius50" src='<?php  echo tomedia($row['avatar'])?>' style='width:30px;height:30px;padding1px;border:1px solid #ccc' onerror="this.src='../addons/ewei_shopv2/static/images/noface.png'"/>
                        <?php  } ?>
                        <?php  if(empty($row['nickname'])) { ?>未更新<?php  } else { ?><?php  echo $row['nickname'];?><?php  } ?>
                        </span>
                    <?php  } ?>
                </div>
            </td>

            <td><?php  echo $row['realname'];?> <br/> <?php  echo $row['mobile'];?></td>
            <td><?php  if($row['isagent']==1) { ?>
                分销等级：<?php  if(empty($row['levelname'])) { ?> <?php echo empty($this->set['levelname'])?'普通等级':$this->set['levelname']?><?php  } else { ?><?php  echo $row['levelname'];?><?php  } ?>
                <br/>
                会员等级：<?php  echo $row['mlevelname'];?>
            </td>
            <?php  } else { ?>
            -<?php  } ?>

            <td><?php echo $row['orderprice']?$row['orderprice']:0?></td>
            <td >
                <?php if(cv('commission.agent.user')) { ?>
                <a  href="<?php  echo webUrl('commission/agent/user',array('id' => $row['id']));?>"  target='_blank' data-toggle='popover' data-placement='right' data-html="true" data-trigger='hover' data-content='查看推广下线'>
                    <?php  echo $row['levelcount'];?>
                </a>
                <?php  } else { ?>
                <?php  echo $row['levelcount'];?>
                <?php  } ?>

            </td>
            <td><?php  echo date('Y-m-d',$row['createtime'])?><br/><?php  echo date('H:i',$row['createtime'])?></td>
            <td><?php  if(!empty($row['agenttime']) && $row['isagent']==1) { ?>
                <?php  echo date('Y-m-d',$row['agenttime'])?><br/><?php  echo date('H:i',$row['agenttime'])?>
                <?php  } else { ?>
                -
                <?php  } ?>
            </td>
            <td>

                <?php  if($row['isagent']==1) { ?>
                <span class='label <?php  if($row['status']==1) { ?>label-primary<?php  } else { ?>label-default<?php  } ?>'
                <?php if(cv('commission.agent.check')) { ?>
                data-toggle='ajaxSwitch'
                data-confirm ='确认要<?php  if($row['status']==1) { ?>取消审核<?php  } else { ?>审核通过<?php  } ?>?'
                data-switch-value='<?php  echo $row['status'];?>'
                data-switch-value0='0|未审核|label label-default|<?php  echo webUrl('commission/agent/check',array('status'=>1,'id'=>$row['id']))?>'
                data-switch-value1='1|已审核|label label-success|<?php  echo webUrl('commission/agent/check',array('status'=>0,'id'=>$row['id']))?>'
                <?php  } ?>
                >
                <?php  if($row['status']==1) { ?>已审核<?php  } else { ?>未审核<?php  } ?></span>
                <br/>


                <?php  } else { ?>
                -
                <?php  } ?>
            </td>


            <td  style="overflow:visible;">

                <div class="btn-group btn-group-sm">
                    <?php  if($row['isagent']==1) { ?>
                        <?php if(cv('order.list')) { ?>
                        <a class="btn  btn-op btn-operation" href="<?php  echo webUrl('order/list',array('agentid' => $row['id']));?>" title='推广订单'  target='_blank'>
                                        <span data-toggle="tooltip" data-placement="top" title="" data-original-title="推广订单">
                                            <i class='icow icow-tuiguang'></i>
                                        </span>
                        </a>
                        <?php  } ?>
                    <?php if(cv('commission.agent.delete')) { ?>
                    <a class="btn  btn-op btn-operation" data-toggle='ajaxRemove' href="<?php  echo webUrl('commission/agent/delete',array('id' => $row['id']));?>" title="删除" data-confirm="确定要删除该分销商吗？">
                                       <span data-toggle="tooltip" data-placement="top" title="" data-original-title="删除分销商">
                                               <i class='icow icow-shanchu1'></i>
                                            </span>
                    </a>
                    <?php  } ?>
                    <?php  } ?>
                    <?php if(cv('order')) { ?>
                    <a class="btn  btn-op btn-operation" href="<?php  echo webUrl('order/list', array('searchfield'=>'member', 'keyword'=>$row['nickname']))?>" title='会员订单'  target='_blank'>
                                     <span data-toggle="tooltip" data-placement="top" title="" data-original-title="会员订单">
                                        <i class='icow icow-dingdan2'></i>
                                    </span>
                    </a>
                    <?php  } ?>
                    <?php if(cv('finance.recharge.credit1|finance.recharge.credit2')) { ?>
                    <a class="btn  btn-op btn-operation" data-toggle="ajaxModal" href="<?php  echo webUrl('finance/recharge', array('type'=>'credit1','id'=>$row['id']))?>" title='充值积分'>
                                    <span data-toggle="tooltip" data-placement="top" title="" data-original-title="充值">
                                       <i class='icow icow-31'></i>
                                    </span>
                    </a>
                    <?php  } ?>


                    </ul>
                </div>


            </td>
        </tr>
        <?php  } } ?>
        </tbody>
    </table>
    <?php  echo $pager;?>

    <?php  } else { ?>
    <div class='panel panel-default'>
        <div class='panel-body' style='text-align: center;padding:30px;'>
            暂时没有任何分销商!
        </div>
    </div>
    <?php  } ?>
    </form>
    </div>
    <script language="javascript">



        require(['bootstrap'],function(){
            $("[rel=pop]").popover({
                trigger:'manual',
                placement : 'right',
                title : $(this).data('title'),
                html: 'true',
                content : $(this).data('content'),
                animation: false
            }).on("mouseenter", function () {
                var _this = this;
                $(this).popover("show");
                $(this).siblings(".popover").on("mouseleave", function () {
                    $(_this).popover('hide');
                });
            }).on("mouseleave", function () {
                var _this = this;
                setTimeout(function () {
                    if (!$(".popover:hover").length) {
                        $(_this).popover("hide")
                    }
                }, 100);
            });


        });


    </script>


    <?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>
<!-- yi fu yuan ma wang -->