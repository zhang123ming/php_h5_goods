<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>

<div class="page-header">
    当前位置：<span class="text-primary"><?php  if(!empty($level['id'])) { ?>编辑<?php  } else { ?>添加<?php  } ?>会员等级<?php  if(!empty($level['id'])) { ?>(<?php  echo $level['levelname'];?>)<?php  } ?></span>
</div>

<div class="page-content">
    <form <?php if( ce('member.agent' ,$detail) ) { ?>action="" method="post"<?php  } ?> class="form-horizontal form-validate" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php  echo $detail['id'];?>" />
        <?php  if(!empty($agent)) { ?>
        <div class="form-group">
            <label class="col-lg control-label">推荐人</label>
            <div class="col-sm-9 col-xs-12">
                <img class="radius50" src="<?php  echo $agent['avatar'];?>" style='width:50px;height:50px;padding:1px;border:1px solid #ccc' onerror="this.src='../addons/ewei_shopv2/static/images/noface.png'"/>
                <?php  if(strexists($agent['openid'],'sns_wa')) { ?><i class="icow icow-xiaochengxu" style="color: #7586db;vertical-align: middle;" data-toggle="tooltip" data-placement="bottom" data-original-title="来源: 小程序"></i><?php  } ?>
                <?php  if(strexists($agent['openid'],'sns_qq')||strexists($agent['openid'],'sns_wx')||strexists($agent['openid'],'wap_user')) { ?><i class="icow icow-app" style="color: #44abf7;vertical-align: middle;" data-toggle="tooltip" data-placement="bottom" data-original-title="来源: 全网通(<?php  if(strexists($agent['openid'],'wap_user')) { ?>手机号注册<?php  } else { ?>APP<?php  } ?>)"></i><?php  } ?>
                <?php  echo $agent['nickname'];?>
            </div>
        </div>
        <?php  } ?>
        <div class="form-group">
            <label class="col-lg control-label">粉丝</label>
            <div class="col-sm-9 col-xs-12">
                <img class="radius50" src="<?php  echo $member['avatar'];?>" style='width:50px;height:50px;padding:1px;border:1px solid #ccc' onerror="this.src='../addons/ewei_shopv2/static/images/noface.png'"/>
                <?php  if(strexists($member['openid'],'sns_wa')) { ?><i class="icow icow-xiaochengxu" style="color: #7586db;vertical-align: middle;" data-toggle="tooltip" data-placement="bottom" data-original-title="来源: 小程序"></i><?php  } ?>
                <?php  if(strexists($member['openid'],'sns_qq')||strexists($member['openid'],'sns_wx')||strexists($member['openid'],'wap_user')) { ?><i class="icow icow-app" style="color: #44abf7;vertical-align: middle;" data-toggle="tooltip" data-placement="bottom" data-original-title="来源: 全网通(<?php  if(strexists($member['openid'],'wap_user')) { ?>手机号注册<?php  } else { ?>APP<?php  } ?>)"></i><?php  } ?>
                <?php  echo $member['nickname'];?>
            </div>
        </div>
        <div class="form-group">
            <label class="col-lg control-label">申请等级</label>
            <div class="col-sm-9 col-xs-12">
                <div class='form-control-static'><?php  echo $level['levelname'];?></div>             
            </div>
        </div>
        <div class="form-group">
            <label class="col-lg control-label">手机号</label>
            <div class="col-sm-9 col-xs-12">
                <div class='form-control-static'><?php  echo $memberdata['mobile'];?></div>             
            </div>
        </div>
        
        <div class="form-group">
            <label class="col-lg control-label">真实姓名</label>
            <div class="col-sm-9 col-xs-12">
            <input type="text" name="realname" value="<?php  echo $member['realname'];?>" class="form-control">
                <!-- <div class='form-control-static'><?php  echo $member['realname'];?></div>              -->
            </div>
        </div>

        <div class="form-group">
            <label class="col-lg control-label">分公司</label>
            <div class="col-sm-9 col-xs-12">
        
                <select name='branch' class='form-control' id="branch">
                    <option value='0'>请选择分公司</option>
                    
                    <?php  if(is_array($branch)) { foreach($branch as $item) { ?>
                        <option value="<?php  echo $item['branch_id'];?>" <?php  if($item['branch_id']==$member['branch']) { ?>selected<?php  } ?> ><?php  echo $item['branch_name'];?></option>
                    <?php  } } ?>
                </select>
        
            </div>
        </div>

        <div class="form-group">
            <label class="col-lg control-label">部门</label>
            <div class="col-sm-9 col-xs-12">
                <select name='detp' class='form-control' id="detp">
                    <option value='0'>请选择部门</option>
                    <?php  if(is_array($detp)) { foreach($detp as $v2) { ?>
                        <option value="<?php  echo $v2['detp_id'];?>"  <?php  if($v2['detp_id']==$member['detp']) { ?>selected<?php  } ?> ><?php  echo $v2['detp_name'];?></option>
                    <?php  } } ?>
                </select>          
            </div>
        </div>

        <div class="form-group">
            <label class="col-lg control-label">组</label>
            <div class="col-sm-9 col-xs-12">
                <select name='groups' class='form-control' id="groups">
                    <option value='0'>请选择组</option>
                    <?php  if(is_array($group)) { foreach($group as $v3) { ?>
                        <option value="<?php  echo $v3['group_id'];?>"  <?php  if($v3['group_id']==$member['groups']) { ?>selected<?php  } ?> ><?php  echo $v3['group_name'];?></option>
                    <?php  } } ?>

                </select>
            </div>
        </div>
        
        <div class="form-group">
            <label class="col-lg control-label">状态</label>
            <div class="col-sm-9 col-xs-12">
                <?php if( ce('shop.adv' ,$item) ) { ?>
                    <label class='radio-inline'>
                        <input type='radio' name='status' value='1' <?php  if($detail["status"]==1) { ?>checked<?php  } ?> /> 已审核
                    </label>
                    <label class='radio-inline'>
                        <input type='radio' name='status' value='0' <?php  if($detail["status"]==0) { ?>checked<?php  } ?> /> 待审核
                    </label>
                    <label class='radio-inline'>
                        <input type='radio' name='status' value='-1' <?php  if($detail["status"]==-1) { ?>checked<?php  } ?> /> 已驳回
                    </label>
                <?php  } else { ?>
                    <div class='form-control-static'><?php  if(empty($item['status'])) { ?>待审核<?php  } else if($detail["status"]==1) { ?>已审核<?php  } else if($detail["status"]==-1) { ?>已驳回<?php  } ?></div>
                <?php  } ?>
            </div>
        </div>
        <div class="form-group">
            <label class="col-lg control-label">审核备注</label>
            <div class="col-sm-9 col-xs-12 subtitle">
                <textarea id="" name="remark" id="remark" rows="5"  class="form-control" data-parent=".remark" maxlength="100" data-rule-maxlength="100"><?php  echo $detail['remark'];?></textarea>
                <div class="help-block">备注内容的长度请控制在100字以内,驳回申请请填写审核备注</div>
            </div>
        </div>

        <div class="form-group"></div>
        <div class="form-group">
            <label class="col-lg control-label"></label>
            <div class="col-sm-9 col-xs-12">
                <?php if( ce('member.level' ,$level) ) { ?>
                    <input type="submit" value="提交" class="btn btn-primary"  />
                <?php  } ?>
                <input type="button" name="back" onclick='history.back()' <?php if(cv('member.agent.add|member.agent.edit')) { ?>style='margin-left:10px;'<?php  } ?> value="返回列表" class="btn btn-default" />
            </div>
        </div>
    </form>

</div>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>

<!--efwww_com-->