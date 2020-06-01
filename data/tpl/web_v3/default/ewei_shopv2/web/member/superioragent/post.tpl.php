<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>

<div class="page-header">当前位置：<span class="text-primary">会员详情</span></div>
<div class="page-content">
		<form action="./index.php" method="get" class="form-horizontal" role="form" id="form1">
        <input type="hidden" name="c" value="site" />
        <input type="hidden" name="a" value="entry" />
        <input type="hidden" name="m" value="ewei_shopv2" />
        <input type="hidden" name="do" value="web" />
        <input type="hidden" name="r" value="member.superioragent.edit" />
        <input type="hidden" name="id" value="<?php  echo $list['id'];?>" id="mid" />
			<div class="form-group">
			    <label class="col-lg control-label">请输入上级代理</label>
			    
				    <div class="input-group">
				    <div class="col-sm-90 col-xs-12">
				    	<input type="text" class="form-control " name="superioragentid" id="superioragentid" value="<?php  echo $res['id'];?>" style='width: 60%;' placeholder="请输入上级代理ID">
			                <span class="input-group-btn">
			                    <button class="btn  btn-primary" id="ss"type="submit"> 搜索</button>
			                </span>
				    </div>
					</div>
				
			</div>
			<?php  if(!empty($res)) { ?>
				<div class="form-group">
			    <label class="col-lg control-label">代理</label>
				    <div class="col-sm-9 col-xs-12">
				        <img class="radius50" src="<?php  echo $res['avatar'];?>" style='width:50px;height:50px;padding:1px;border:1px solid #ccc' onerror="this.src='../addons/ewei_shopv2/static/images/noface.png'"/>
				        <?php  if(strexists($res['openid'],'sns_wa')) { ?><i class="icow icow-xiaochengxu" style="color: #7586db;vertical-align: middle;" data-toggle="tooltip" data-placement="bottom" data-original-title="来源: 小程序"></i><?php  } ?>
				        <?php  if(strexists($res['openid'],'sns_qq')||strexists($res['openid'],'sns_wx')||strexists($res['openid'],'wap_user')) { ?><i class="icow icow-app" style="color: #44abf7;vertical-align: middle;" data-toggle="tooltip" data-placement="bottom" data-original-title="来源: 全网通(<?php  if(strexists($res['openid'],'wap_user')) { ?>手机号注册<?php  } else { ?>APP<?php  } ?>)"></i><?php  } ?>
				        <?php  echo $res['nickname'];?>
				        <?php  if($list['superioragentid'] !== $res['id'] ) { ?> <button  class="btn btn-primary">未锁定</button><?php  } else { ?><button  class="btn btn-danger" >已锁定</button><?php  } ?>
				    </div>
				</div>
			<?php  } ?>
		</form>
	<form  action="./index.php" method="post"  role="form" id="form1"  class='form-horizontal form-validate'>
		<input type="hidden" name="c" value="site" />
        <input type="hidden" name="a" value="entry" />
        <input type="hidden" name="m" value="ewei_shopv2" />
        <input type="hidden" name="do" value="web" />
        <input type="hidden" name="r" value="member.superioragent.edit" />
 		<input type="hidden" name="id" value="<?php  echo $list['id'];?>" id="mid" />
 		<input type="hidden" name="agentid" value="<?php  echo $list['agentid'];?>" />
 		<?php  if($res['id'] == "") { ?><input type="hidden" name="superioragentid" value="<?php  echo $list['superioragentid'];?>" id="mid" /><?php  } else { ?><input type="hidden" name="superioragentid" value="<?php  echo $res['id'];?>" id="mid" /><?php  } ?>
	<div class="tabs-container">

		<div class="tabs">
			
				
			<div class="form-group">
			    <label class="col-lg control-label">会员</label>
			    <div class="col-sm-9 col-xs-12">
			        <img class="radius50" src="<?php  echo $list['avatar'];?>" style='width:50px;height:50px;padding:1px;border:1px solid #ccc' onerror="this.src='../addons/ewei_shopv2/static/images/noface.png'"/>
			        <?php  if(strexists($list['openid'],'sns_wa')) { ?><i class="icow icow-xiaochengxu" style="color: #7586db;vertical-align: middle;" data-toggle="tooltip" data-placement="bottom" data-original-title="来源: 小程序"></i><?php  } ?>
			        <?php  if(strexists($list['openid'],'sns_qq')||strexists($list['openid'],'sns_wx')||strexists($list['openid'],'wap_user')) { ?><i class="icow icow-app" style="color: #44abf7;vertical-align: middle;" data-toggle="tooltip" data-placement="bottom" data-original-title="来源: 全网通(<?php  if(strexists($list['openid'],'wap_user')) { ?>手机号注册<?php  } else { ?>APP<?php  } ?>)"></i><?php  } ?>
			        <?php  echo $list['nickname'];?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  推荐人ID：<?php  echo $list['agentid'];?>&nbsp;&nbsp;&nbsp;<?php  echo $levelnames['levelname'];?>

			    </div>


			</div>
			
			<!--<div class="form-group">-->
			<!--    <label class="col-lg control-label">是否升级为区代</label>-->
			<!--    <div class="col-sm-9 col-xs-12">-->
			<!--        <label class="radio-inline"><input type="radio" name="level" value="1" >是</label>-->
			<!--        <label class="radio-inline" ><input type="radio" name="level" value="0" checked>否</label>-->
			        <!-- <span class="help-block">固定上级后，任何条件也无法改变其上级，如果不选择上级分销商，且固定上级，则上级永远为总店（是分销商）或无上线（非分销商）</span>   -->  
			<!--    </div>-->
			<!--</div>-->

			<div class="form-group">
			    <label class="col-lg control-label">真实姓名</label>
			    <div class="col-sm-9 col-xs-12">
			    		<input class="form-control" type="text"  name="realname" id="realname" value="<?php  echo $list['realname'];?>"/>
			    </div>
			</div>
			<div class="form-group">
			    <label class="col-lg control-label">手机号</label>
			    <div class="col-sm-9 col-xs-12">
			            <input class="form-control" type="text"   name="mobile"   id="mobile"  value="<?php  echo $list['mobile'];?>"/>
			    </div>
			</div>
			<div class="tab-content ">

				<div class="form-group">
				    <label class="col-lg control-label">开户银行</label>
				    <div class="col-sm-9 col-xs-12">
				        <input class="form-control" type="text"   name="number"   id="number" value="<?php  echo $list['number'];?>" />
				    </div>
				</div>
			</div>
			<div class="tab-content ">

				<div class="form-group">
				    <label class="col-lg control-label">开户支行</label>
				    <div class="col-sm-9 col-xs-12">
				        <input class="form-control" type="text"    name="opening"  id="opening" value="<?php  echo $list['opening'];?>"  />
				    </div>
				</div>
			</div>
			<div class="tab-content ">

				<div class="form-group">
				    <label class="col-lg control-label">银行卡号</label>
				    <div class="col-sm-9 col-xs-12">
				        <input class="form-control" type="text"   name="bankcard"  id="bankcard" value="<?php  echo $list['bankcard'];?>" />
				    </div>
				</div>
			</div>
			<div class="tab-content ">

				<div class="form-group">
				    <label class="col-lg control-label">公司</label>
				    <div class="col-sm-9 col-xs-12">
				        <input class="form-control" type="text"   name="branch_name"   id="branch_name" value="<?php  echo $list['branch_name'];?>" />
				    </div>
				</div>
			</div>
			<div class="tab-content ">

				<div class="form-group">
				    <label class="col-lg control-label">部门</label>
				    <div class="col-sm-9 col-xs-12">
				        <input class="form-control" type="text"   name="detp_name"   id="detp_name" value="<?php  echo $list['detp_name'];?>"/>
				    </div>
				</div>
			</div>
			<div class="tab-content ">

				<div class="form-group">
				    <label class="col-lg control-label">组</label>
				    <div class="col-sm-9 col-xs-12">
				        <input class="form-control" type="text"   name="group_name"   id="group_name" value="<?php  echo $list['group_name'];?>" />
				    </div>
				</div>
			</div>
			
		</div>
	</div>
	<div class="form-group"></div>	
          <div class="form-group">
		<label class="col-lg control-label"></label>
		<div class="col-sm-9 col-xs-12">

			<?php if(cv('member.superioragent.edit')) { ?>
			<input type="submit" id="bindsubmit"  value="保存" class="btn btn-primary" />
			<?php  } ?>

			<input type="button" class="btn btn-default" name="submit" onclick="history.go(-1)" value="返回列表" <?php if(cv('member.branch')) { ?>style='margin-left:10px;'<?php  } ?> />
		</div>
	</div>

</form>

</div>


<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>
<!--efwww_com-->