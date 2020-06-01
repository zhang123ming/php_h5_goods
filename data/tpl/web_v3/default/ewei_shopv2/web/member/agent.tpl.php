<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>
<style>
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
</style>
<div class="page-header">当前位置：<span class="text-primary">会员申请列表</span></div>

<div class="page-content">

    <form action="./index.php" method="get" class="form-horizontal table-search" role="form">
        <input type="hidden" name="c" value="site"/>
        <input type="hidden" name="a" value="entry"/>
        <input type="hidden" name="m" value="ewei_shopv2"/>
        <input type="hidden" name="do" value="web"/>
        <input type="hidden" name="r" value="member.agent"/>
        <div class="page-toolbar">

            <span class="pull-left">
                <?php  echo tpl_daterange('time', array('sm'=>true, 'placeholder'=>'申请时间'),true);?>
            </span>
    
            <div class="input-group col-sm-6 pull-right">
                <span class="input-group-select">
                    <select name="status" class='form-control select2' style="width:100px;">
                        <option value="-1">审核状态</option>
                        <option value="0" <?php  if($_GPC['status']==0) { ?>selected<?php  } ?>>未审核</option>
                        <option value="1" <?php  if($_GPC['status']==1) { ?>selected<?php  } ?>>已审核</option>
                    </select>
                </span>
                <input type="text" class="form-control " name="realname" value="<?php  echo $_GPC['realname'];?>" placeholder="可搜索昵称/姓名/手机号/ID">
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
                <div class="page-table-header">
                    <input type="checkbox">
                    <div class="btn-group">
	                  <!-- <a class='btn btn-default btn-sm btn-op btn-operation'  data-toggle='batch' data-href="<?php  echo webUrl('member/agent/check',array('status'=>1))?>"  data-confirm='确认要审核通过?'>
	                    <i class="icow icow-shenhetongguo"></i>审核通过
	                </a>
	                <a class='btn btn-default btn-sm btn-op btn-operation'  data-toggle='batch' data-href="<?php  echo webUrl('member/agent/check',array('status'=>0))?>" data-confirm='确认要取消审核?'>
	                   <i class="icow icow-yiquxiao"></i>取消审核</a> -->
                        <?php if(cv('member.list.delete')) { ?>
                        <button class="btn btn-default btn-sm btn-operation" type="button" data-toggle='batch-remove' data-confirm="确认要删除?" data-href="<?php  echo webUrl('member/agent/delete')?>">
                            <i class="icow icow-shanchu1"></i> 批量删除
                        </button>
                        <?php  } ?>
                      
                    </div>
                </div>
                <table class="table table-responsive">
                    <thead>
                    <tr>
                        <th style="width:25px;"></th>
                        <th style="width: 180px;">粉丝</th>
                        <th style="width: 150px;">所在部门</th>
                        <th style="">当前等级</th>
                        <th style="">申请等级</th>
                        <th style="width: 180px;">填写内容</th>
                        <th style="">申请时间</th>
  						<th>状态</th>
                        <th style="width: 125px;text-align: center;">操作</th>
                    </tr>
                    </thead>
                    <tbody>
                        <?php  if(is_array($list)) { foreach($list as $row) { ?>
                        <tr>
                            <td style="position: relative; ">
                                <input type='checkbox' value="<?php  echo $row['id'];?>" class="checkone"/></td>
                            <td style="overflow: visible">
                                <div rel="pop" style="display: flex" data-content=" <span>ID: </span><?php  echo $row['mid'];?> </br>
                                <span>推荐人：</span> <?php  if(empty($row['agentid'])) { ?>
                                  <?php  if($row['isagent']==1) { ?>
                                      总店
                                      <?php  } else { ?>
                                     暂无
                                      <?php  } ?>
                                <?php  } else { ?>

                                <?php  if(!empty($row['agentavatar'])) { ?>
                                 <img src='<?php  echo $row['agentavatar'];?>' style='width:20px;height:20px;padding1px;border:1px solid #ccc' />
                               <?php  } ?>
                               [<?php  echo $row['agentid'];?>]<?php  if(empty($row['agentnickname'])) { ?>未更新<?php  } else { ?><?php  echo $row['agentnickname'];?><?php  } ?>
                               <?php  } ?>
                               <br/>
                                 <span>真实姓名：</span> <?php  if(empty($row['realname'])) { ?>未填写<?php  } else { ?><?php  echo $row['realname'];?><?php  } ?>
                                <br/>
                               <span>手机号：</span><?php  if(!empty($row['mobileverify'])) { ?><?php  echo $row['mobile'];?><?php  } else { ?>未绑定<?php  } ?> <br/>
                        <br/>
                               <span>状态:</span>  <?php  if($row['isblack']==1) { ?>黑名单<?php  } else { ?>正常<?php  } ?> " >
                                   <img class="img-40" src="<?php  echo tomedia($row['avatar'])?>" style='border-radius:50%;border:1px solid #efefef;' onerror="this.src='../addons/ewei_shopv2/static/images/noface.png'" />
                                   <span style="display: flex;flex-direction: column;justify-content: center;align-items: flex-start;padding-left: 5px">
                                       <span class="nickname">
                                           <?php  if(strexists($row['openid'],'sns_wa')) { ?><i class="icow icow-xiaochengxu" style="color: #7586db;vertical-align: middle;" data-toggle="tooltip" data-placement="top" title="" data-original-title="来源: 小程序"></i><?php  } ?>
                                           <?php  if(strexists($row['openid'],'sns_qq')||strexists($row['openid'],'sns_wx')||strexists($row['openid'],'wap_user')) { ?><i class="icow icow-app" style="color: #44abf7;vertical-align: top;" data-toggle="tooltip" data-placement="bottom" data-original-title="来源: 全网通(<?php  if(strexists($row['openid'],'wap_user')) { ?>手机号注册<?php  } else { ?>APP<?php  } ?>)"></i><?php  } ?>

                                           <?php  if(empty($row['nickname'])) { ?>未更新<?php  } else { ?><?php  echo $row['nickname'];?><?php  } ?>
                                       </span>
                                       <?php  if($row['isblack']==1) { ?>
                                            <span class="text-danger">黑名单<i class="icow icow-heimingdan1"style="color: #db2228;margin-left: 2px;font-size: 13px;"></i></span>
                                       <?php  } ?>
                                   </span>

                                </div>
                            </td>
							<td><?php  echo $row['branch'];?></td>
                            <td><?php  if(empty($row['levelname'])) { ?>普通会员<?php  } else { ?><?php  echo $row['levelname'];?><?php  } ?>
                            </td>
							<td><?php  echo $row['applylevel']['levelname'];?></td>
                            <th><?php  echo $row['content'];?></th>
                            <td><?php  echo date("Y-m-d",$row['createtime'])?><br/><?php  echo date("H:i:s",$row['createtime'])?></td>

                          	<td>
                          		<span class='label <?php  if($row["status"]==1) { ?>label-primary<?php  } else if($row["status"]==0) { ?>label-default<?php  } else if($row["status"]==-1) { ?>label-danger<?php  } ?>'>
								<?php  if($row['status']==1) { ?>已审核<?php  } else if($row["status"]==0) { ?>未审核<?php  } else if($row["status"]==-1) { ?>驳回申请<?php  } ?>
							</span>
                          	</td>
                            <td>
                                <?php if(cv('member.agent.edit|member.agent.view')) { ?>
                                <a href="<?php  echo webUrl('member/agent/edit', array('id' => $row['id']))?>" class="btn btn-op btn-operation">
                                    <span data-toggle="tooltip" data-placement="top" data-original-title="<?php if(cv('member.agent.edit')) { ?>修改<?php  } else { ?>查看<?php  } ?>">
                                        <i class='icow icow-bianji2'></i>
                                    </span>
                                </a>
                                <?php  } ?>
                                <?php if(cv('member.agent.delete')) { ?>
                                <a data-toggle='ajaxRemove' href="<?php  echo webUrl('member/agent/delete', array('id' => $row['id']))?>"class="btn btn-op btn-operation" data-confirm='确认要删除此会员等级吗?'>
                                    <span data-toggle="tooltip" data-placement="top" data-original-title="删除">
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
                        <td colspan="3">
                            <div class="btn-group">
                                <?php if(cv('member.agent.delete')) { ?>
                                <button class="btn btn-default btn-sm btn-operation" type="button" data-toggle='batch-remove' data-confirm="确认要删除?" data-href="<?php  echo webUrl('member/agent/delete')?>">
                                    <i class="icow icow-shanchu1"></i> 批量删除
                                </button>
                                <?php  } ?>
                            </div>
                        </td>
                        <td colspan="4" style="text-align: right">
                            <?php  echo $pager;?>
                        </td>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    <?php  } ?>
</div>


<script language="javascript">

    require(['bootstrap'], function () {
        $("[rel=pop]").popover({
            trigger: 'manual',
            placement: 'right',
            title: $(this).data('title'),
            html: 'true',
            content: $(this).data('content'),
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


    $("[data-toggle='batch-group'], [data-toggle='batch-level']").click(function () {
        var toggle = $(this).data('toggle');
        $("#modal-change .modal-title").text(toggle=='batch-group'?"批量修改会员分组":"批量修改会员等级");
        $("#modal-change").find("."+toggle).show().siblings().hide();
        $("#modal-change-btn").attr('data-toggle', toggle=='batch-group'?'group':'level');
        $("#modal-change").modal();
    });
    $("#modal-change-btn").click(function () {
        var _this = $(this);
        if(_this.attr('stop')){
            return;
        }
        var toggle = $(this).data('toggle');
        var ids = [];
        $(".checkone").each(function () {
            var checked = $(this).is(":checked");
            var id = $(this).val();
            if(checked && id){
                ids.push(id);
            }
        });
        if(ids.length<1){
            tip.msgbox.suc("请选择要批量操作的会员");
            return;
        }
        var option = $("#modal-change .batch-"+toggle+" option:selected");
        var level = option.val();
        var levelname = option.text();
        tip.confirm("确定要将选中会员移动到 "+levelname+" 吗？", function () {
            _this.attr('stop', 1).text("操作中...");
            $.post(biz.url('member/list/changelevel'),{
                level: level,
                ids: ids,
                toggle: toggle
            }, function (ret) {
                $("#modal-change").modal('hide');
                if(ret.status==1){
                    tip.msgbox.suc("操作成功");
                    setTimeout(function () {
                        location.reload();
                    },1000);
                }else{
                    tip.msgbox.err(ret.result.message);
                }
            }, 'json')
        });
    });
</script>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>
<!--efwww_com54mI5p2D5omA5pyJ-->