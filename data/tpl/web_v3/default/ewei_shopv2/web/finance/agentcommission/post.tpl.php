<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>

<div class="page-header">当前位置：<span class="text-primary">代理详情</span></div>
<div class="page-content">

	<form <?php  if('finance.agentcommission.edit') { ?>action="" method='post'<?php  } ?> class='form-horizontal form-validate'>
 <input type="hidden" name="id" value="<?php  echo $list['id'];?>" id="mid" />
	<div class="tabs-container">

		<div class="tabs">
			
				
			<div class="form-group">
			    <label class="col-lg control-label">代理</label>
			    <div class="col-sm-9 col-xs-12">
			        <img class="radius50" src="<?php  echo $list['avatar'];?>" style='width:50px;height:50px;padding:1px;border:1px solid #ccc' onerror="this.src='../addons/ewei_shopv2/static/images/noface.png'"/>
			        <?php  if(strexists($list['openid'],'sns_wa')) { ?><i class="icow icow-xiaochengxu" style="color: #7586db;vertical-align: middle;" data-toggle="tooltip" data-placement="bottom" data-original-title="来源: 小程序"></i><?php  } ?>
			        <?php  if(strexists($list['openid'],'sns_qq')||strexists($list['openid'],'sns_wx')||strexists($list['openid'],'wap_user')) { ?><i class="icow icow-app" style="color: #44abf7;vertical-align: middle;" data-toggle="tooltip" data-placement="bottom" data-original-title="来源: 全网通(<?php  if(strexists($list['openid'],'wap_user')) { ?>手机号注册<?php  } else { ?>APP<?php  } ?>)"></i><?php  } ?>
			        <?php  echo $list['nickname'];?>
			    </div>
			</div>
			<div class="form-group">
			    <label class="col-lg control-label">等级</label>
			    <div class="col-sm-9 col-xs-12">
			        <input class="form-control" type="text" name="levelnames" disabled  value="<?php  echo $list['levelnames'];?>" style="background:#fff;border:none;"/>
			    </div>
			</div>
			<div class="form-group">
			    <label class="col-lg control-label">真实姓名</label>
			    <div class="col-sm-9 col-xs-12">
			    		<input class="form-control" type="text"  disabled id="realname" value="<?php  echo $list['realname'];?>" style="background:#fff;border:none;"/>
			    </div>
			</div>
			<div class="form-group">
			    <label class="col-lg control-label">手机号</label>
			    <div class="col-sm-9 col-xs-12">
			            <input class="form-control" type="text"  disabled  id="mobile"  value="<?php  echo $list['mobile'];?>" style="background:#fff;border:none;"/>
			    </div>
			</div>
			<div class="tab-content ">

				<div class="form-group">
				    <label class="col-lg control-label">开户银行</label>
				    <div class="col-sm-9 col-xs-12">
				        <input class="form-control" type="text"  disabled  id="number" value="<?php  echo $list['number'];?>" style="background:#fff;border:none;"/>
				    </div>
				</div>
			</div>
			<div class="tab-content ">

				<div class="form-group">
				    <label class="col-lg control-label">开户支行</label>
				    <div class="col-sm-9 col-xs-12">
				        <input class="form-control" type="text"  disabled  id="opening" value="<?php  echo $list['opening'];?>"  style="background:#fff;border:none;" />
				    </div>
				</div>
			</div>
			<div class="tab-content ">

				<div class="form-group">
				    <label class="col-lg control-label">银行卡号</label>
				    <div class="col-sm-9 col-xs-12">
				        <input class="form-control" type="text" disabled  id="bankcard" value="<?php  echo $list['bankcard'];?>"  style="background:#fff;border:none;" />
				    </div>
				</div>
			</div>
			<div class="tab-content ">

				<div class="form-group">
				    <label class="col-lg control-label">公司</label>
				    <div class="col-sm-9 col-xs-12">
				        <input class="form-control" type="text"  disabled  id="branch_name" value="<?php  echo $list['branch_name'];?>"   style="background:#fff;border:none;"/>
				    </div>
				</div>
			</div>
			<div class="tab-content ">

				<div class="form-group">
				    <label class="col-lg control-label">部门</label>
				    <div class="col-sm-9 col-xs-12">
				        <input class="form-control" type="text"  disabled  id="detp_name" value="<?php  echo $list['detp_name'];?>" style="background:#fff;border:none;" />
				    </div>
				</div>
			</div>
			<div class="tab-content ">

				<div class="form-group">
				    <label class="col-lg control-label">组</label>
				    <div class="col-sm-9 col-xs-12">
				        <input class="form-control" type="text"  disabled  id="group_name" value="<?php  echo $list['group_name'];?>"  style="background:#fff;border:none;"/>
				    </div>
				</div>
			</div>
			<div class="form-group">
			    <label class="col-lg control-label">已积累佣金总额</label>
			    <div class="col-sm-9 col-xs-12">
			    	<input class="form-control" type="text"  disabled  id="commission_total" value="<?php  echo $list['commission_total'];?>"  style="background:#fff;border:none;"/>
			           
			    </div>
			</div>
			<div class="form-group">
			    <label class="col-lg control-label">已结算佣金总额</label>
			    <div class="col-sm-9 col-xs-12">
			    	<input class="form-control" type="text"  disabled  id="commission_ok" value="<?php  echo $list['commission_ok'];?>"  style="background:#fff;border:none;"/>
			           
			    </div>
			</div>
			<div class="form-group">
			    <label class="col-lg control-label">剩余未结算佣金总额</label>
			    <div class="col-sm-9 col-xs-12">
			    	<input class="form-control" type="text"  disabled  id="unsettled" value="<?php  echo $list['unsettled'];?>"  style="background:#fff;border:none;"/>
			    </div>
			</div>
		</div>
	</div>
	<div class="form-group"></div>	
          <div class="form-group">
		<label class="col-lg control-label"></label>
		<div class="col-sm-9 col-xs-12">
			<?php  if($list['commission_total'] > $list['commission_ok'] &&  $list['unsettled'] != 0) { ?>
				<?php if(cv('finance.agentcommission.edit')) { ?>
				<input type="submit" id="bindsubmit"  value="结算" class="btn btn-primary" />
				<?php  } ?>
			<?php  } ?>
			<input type="button" class="btn btn-default" name="submit" onclick="history.go(-1)" value="返回列表" <?php if(cv('member.branch')) { ?>style='margin-left:10px;'<?php  } ?> />
		</div>
	</div>

</form>

</div>

<script type="text/javascript">
    	$('#bindsubmit').click(function() {
            if ($('#bindsubmit').attr('stop')) {
                return
            }
            if ($('#realname').val() =="") {
            	alert("请核实真实姓名")
                return false;
            }
            if ($('#mobile').val() =="") {
            	alert("请核实手机号")
                return false;
            }
            if ($('#number').val() =="") {
            	alert("请核实开户银行")
                return false;
            }
            if ($('#opening').val() =="") {
            	alert("请核实开户支行")
                return false;
            }
            if ($('#bankcard').val() =="") {
            	alert("请核实银行卡号")
                return false;
            }
            if ($('#branch_name').val() =="") {
            	alert("请核实公司")
                return false;
            }
            if ($('#detp_name').val() =="") {
            	alert("请核实部门")
                return false;
            }
            if ($('#group_name').val() =="") {
            	alert("请核实组")
                return false;
            }
            
            $('#bindsubmit').html('正在绑定...').attr('stop', 1);
            $.post("<?php  echo mobileUrl('finance/agentcommission/edit','',true)?>", {
                mobile: $('#mobile').val(),
                realname: $('#realname').val(),
                number: $('#number').val(),
                opening: $('#opening').val(),
                id: $("#mid").val(),
                commission_total: $("#commission_total").val(),
                unsettled: $("#unsettled").val(),
                commission_ok: $("#commission_ok").val(),
            }, function(b) {
            	if (b.status == '1') {
                    alert("提交成功！");
                	window.location.href = "<?php  echo mobileUrl('finance/agentcommission')?>";
                    return
                }
                if (b.status == 0) {
                    FoxUI.toast.show(b.result.message);
                    $('#bindsubmit').html('立即绑定').removeAttr('stop');
                    return
                }
                alert("提交成功！");
                window.location.href = "<?php  echo mobileUrl('finance/agentcommission')?>";
            }, 'json')
        });
    </script>



<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>
<!--efwww_com-->