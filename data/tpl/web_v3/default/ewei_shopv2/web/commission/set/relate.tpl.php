<?php defined('IN_IA') or exit('Access Denied');?><div class="form-group-title" style="margin-top: -25px">上下线关系设置</div>
           <div class="form-group">
                <label class="col-lg control-label">成为下线条件</label>
                <div class="col-sm-9 col-xs-12">
                	<?php if(cv('commission.set.edit')) { ?>
	                    <label class="radio-inline"><input type="radio"  name="data[become_child]" value="0" <?php  if($data['become_child'] ==0) { ?> checked="checked"<?php  } ?> /> 首次点击分享链接</label>
	                    <label class="radio-inline"><input type="radio"  name="data[become_child]" value="1" <?php  if($data['become_child'] ==1) { ?> checked="checked"<?php  } ?> /> 首次下单</label>
	                    <label class="radio-inline"><input type="radio"  name="data[become_child]" value="2" <?php  if($data['become_child'] ==2) { ?> checked="checked"<?php  } ?> /> 首次付款</label>
	                    <span class='help-block'>首次点击分享链接： 可以自由设置分销商条件</span>
	                    <span class='help-block'>首次下单/首次付款： 无条件不可用</span>
                    <?php  } else { ?>
                    	<?php  if($data['become_child'] ==0) { ?>首次点击分享链接<?php  } ?>
                    	<?php  if($data['become_child'] ==1) { ?>首次下单<?php  } ?>
                    	<?php  if($data['become_child'] ==2) { ?>首次下单<?php  } ?>
                    <?php  } ?>
                </div>
            </div>

            <div class="form-group" style="display:none;">
                <label class="col-lg control-label">审核下级权限</label>
                <div class="col-sm-9 col-xs-12">
                    <label class="radio-inline"><input type="radio" name="data[checkpower]" value="0" <?php  if($data['checkpower'] ==0) { ?> checked="checked" <?php  } else { ?> checked="checked" <?php  } ?>>关闭</label>
                    <label class="radio-inline"><input type="radio" name="data[checkpower]" value="1" <?php  if($data['checkpower'] ==1) { ?> checked="checked"<?php  } ?>>开启</label>
                </div>
            </div>
            <div class="form-group" id="powertype" style="display:none;">
                <label class="col-lg control-label">权限分配类型</label>
                <div class="col-sm-9 col-xs-12">
                    <label class="radio-inline"><input type="radio" name="data[powertype]" value="0" <?php  if($data['powertype'] ==0) { ?> checked="checked" <?php  } else { ?> checked="checked" <?php  } ?>>按会员</label>
                    <label class="radio-inline"><input type="radio" name="data[powertype]" value="1" <?php  if($data['powertype'] ==1) { ?> checked="checked"<?php  } ?>>按等级</label>
                </div>
            </div>
            <div class="form-group" id="userlevel" style="display:none;">
                <?php  if(!empty($agentlevels)) { ?>
                    <label class="col-lg control-label">分销等级</label>
                    <div class="col-sm-9 col-xs-12">
                        <label class="checkbox-inline"><?php  echo $agentdefault;?>：</label>
                        <input type="hidden" name="data[power][<?php  echo $agentdefault;?>]" value="<?php  if($array[$agentdefault]) { ?><?php  echo $array[$agentdefault];?><?php  } ?>">
                        <?php  if(is_array($agentlevels)) { foreach($agentlevels as $res) { ?>
                        <label class="checkbox-inline"><input type="checkbox" name="<?php  echo $agentdefault;?>" <?php  if(in_array($res['id'],$power[$agentdefault])) { ?>checked="checked"<?php  } ?> value="<?php  echo $res['id'];?>" ><?php  echo $res['levelname'];?></label>
                        <?php  } } ?>
                    </div>
                    <?php  if(is_array($agentlevels)) { foreach($agentlevels as $agents) { ?>
                    <label class="col-lg control-label"></label>
                    <div class="col-sm-9 col-xs-12">
                        <label class="checkbox-inline"><?php  echo $agents['levelname'];?>：</label>
                        <input type="hidden" name="data[power][<?php  echo $sgents['levelname'];?>]" value="<?php  if($array[$agents['levelname']]) { ?><?php  echo $array[$agents['levelname']];?><?php  } ?>">
                        <?php  if(is_array($agentlevels)) { foreach($agentlevels as $res) { ?>
                        <label class="checkbox-inline"><input type="checkbox" <?php  if(in_array($res['id'],$power[$agents['levelname']])) { ?>checked="checked"<?php  } ?> name="<?php  echo $agents['levelname'];?>" value="<?php  echo $res['id'];?>" ><?php  echo $res['levelname'];?></label>
                        <?php  } } ?>
                    </div>
                    <?php  } } ?>
                <?php  } else { ?>
                    <label class="col-lg control-label">会员等级</label>
                    <div class="col-sm-9 col-xs-12">
                        <label class="checkbox-inline"><?php  echo $userdefault;?>：</label>
                        <input type="hidden" name="data[power][<?php  echo $userdefault;?>]" value="<?php  if($array[$userdefault]) { ?><?php  echo $array[$userdefault];?><?php  } ?>">
                        <?php  if(is_array($userlevels)) { foreach($userlevels as $res) { ?>
                        <label class="checkbox-inline"><input type="checkbox" <?php  if(in_array($res['id'],$power[$userdefault])) { ?>checked="checked"<?php  } ?> name="<?php  echo $userdefault;?>" value="<?php  echo $res['id'];?>" ><?php  echo $res['levelname'];?></label>
                        <?php  } } ?>
                    </div>
                    <?php  if(is_array($userlevels)) { foreach($userlevels as $user) { ?>
                    <label class="col-lg control-label"></label>
                    <div class="col-sm-9 col-xs-12">
                        <label class="checkbox-inline"><?php  echo $user['levelname'];?>：</label>
                        <input type="hidden" name="data[power][<?php  echo $user['levelname'];?>]" value="<?php  if($array[$user['levelname']]) { ?><?php  echo $array[$user['levelname']];?><?php  } ?>">
                        <?php  if(is_array($userlevels)) { foreach($userlevels as $res) { ?>
                        <label class="checkbox-inline"><input type="checkbox" <?php  if(in_array($res['id'],$power[$user['levelname']])) { ?>checked="checked"<?php  } ?> name="<?php  echo $user['levelname'];?>" value="<?php  echo $res['id'];?>" ><?php  echo $res['levelname'];?></label>
                        <?php  } } ?>
                    </div>
                    <?php  } } ?>
                <?php  } ?>
            </div>
            <div class="form-group">
                <label class="col-lg control-label">分享链接建立关系</label>
                <div class="col-sm-9 col-xs-12">
                    <?php if(cv('commission.set.edit')) { ?>
                     <label class="radio-inline"><input type="radio"  name="data[showmid]" value="0" <?php  if(empty($data['showmid'])) { ?> checked="checked"<?php  } ?> /> 开启</label>
                    <label class="radio-inline"><input type="radio"  name="data[showmid]" value="1" <?php  if($data['showmid'] ==1) { ?> checked="checked"<?php  } ?> /> 关闭</label>
                   
                    <?php  } else { ?>
                    <?php  if(empty($data['showmid'])) { ?>开启<?php  } else { ?>关闭<?php  } ?>
                    <?php  } ?>
                    <span class='help-block'>小程序不生效</span>
                </div>

            </div>
	    
    <div class="form-group-title">分销资格设置</div>
		
            <div class="form-group">
                <label class="col-lg control-label">成为分销商条件</label>
                <div class="col-sm-9 col-xs-12">
                	<?php if(cv('commission.set.edit')) { ?>
	                	<label class="radio-inline"><input type="radio"  name="data[become]" value="0" <?php  if($data['become'] ==0) { ?> checked="checked"<?php  } ?> data-needcheck="1" onclick="showBecome(this)" /> 无条件</label>
	                    <label class="radio-inline"><input type="radio"  name="data[become]" value="1" <?php  if($data['become'] ==1) { ?> checked="checked"<?php  } ?> data-needcheck="1" onclick="showBecome(this)"/> 申请</label>
	                    <label class="radio-inline"><input type="radio"  name="data[become]" value="2" <?php  if($data['become'] ==2) { ?> checked="checked"<?php  } ?> data-needcheck="1" onclick="showBecome(this)"/> 消费次数</label>
						<label class="radio-inline"><input type="radio"  name="data[become]" value="3" <?php  if($data['become'] ==3) { ?> checked="checked"<?php  } ?> data-needcheck="1" onclick="showBecome(this)"/> 消费金额</label>
						<label class="radio-inline"><input type="radio"  name="data[become]" value="4" <?php  if($data['become'] ==4) { ?> checked="checked"<?php  } ?> data-needcheck="1" onclick="showBecome(this)"/> 购买商品</label>
					<?php  } else { ?>
						<?php  if($data['become'] ==0) { ?>无条件<?php  } ?>
						<?php  if($data['become'] ==1) { ?>申请<?php  } ?>
						<?php  if($data['become'] ==2) { ?>消费次数<?php  } ?>
						<?php  if($data['become'] ==3) { ?>消费金额<?php  } ?>
						<?php  if($data['become'] ==4) { ?>购买商品<?php  } ?>
					<?php  } ?>
                </div> 
            </div>
           <div class="form-group become become2"  <?php  if($data['become']!='2' ) { ?>style="display:none"<?php  } ?>>
                    <label class="col-lg control-label "></label>
                    <div class="col-sm-9 col-xs-12">
                    	<?php if(cv('commission.set.edit')) { ?>
                           <div class='input-group' >
	                            <div class='input-group-addon'>消费达到</div>
	                            <input type='text' class='form-control' name='data[become_ordercount]' value="<?php  echo $data['become_ordercount'];?>" />
	                            <div class='input-group-addon'>次</div>
                        	</div>
                        <?php  } else { ?>
                        	消费达到 <?php  echo $data['become_ordercount'];?>次
                        <?php  } ?>
                    </div>
           </div>
          <div class="form-group  become become3" <?php  if($data['become']!='3' ) { ?>style="display:none"<?php  } ?>>
                    <label class="col-lg control-label" ></label>
                    <div class="col-sm-9 col-xs-12">
                    	<?php if(cv('commission.set.edit')) { ?>
                           <div class='input-group' >
	                            <div class='input-group-addon'>消费达到</div>
	                            <input type='text' class='form-control' name='data[become_moneycount]' value="<?php  echo $data['become_moneycount'];?>" />
	                            <div class='input-group-addon'>元</div>
	                        </div>
	                    <?php  } else { ?>
	                    	消费达到 <?php  echo $data['become_moneycount'];?>元
	                    <?php  } ?>
                    </div>
           </div>
           <div class="form-group become become3" <?php  if($data['become']!='3' ) { ?>style="display:none"<?php  } ?>>
            <label class="col-sm-2 control-label">消费金额关联应用</label>
            <div class="col-sm-9 col-xs-12" >
                <?php if(cv('commission.set.edit')) { ?>
                <label for="indiana" class="checkbox-inline">
                    <input type="checkbox" name="data[become3][indiana]" value="1" id="indiana" <?php  if(!empty($data[become3]['indiana'])) { ?>checked="true"<?php  } ?> /> 一元夺宝
                </label>
                <label for="lottery" class="checkbox-inline">
                    <input type="checkbox" name="data[become3][lottery]" value="1" id="lottery" <?php  if(!empty($data[become3]['lottery'])) { ?>checked="true"<?php  } ?> /> 大转盘
                </label>
                <div class='help-block'>提示: 不勾选则默认只有商城</div>
                <?php  } else { ?> 
                <div class='form-control-static'>
                    <?php  if($data[become3]['indiana']==1) { ?>一元夺宝; <?php  } ?>
                    <?php  if($data[become3]['lottery']==1) { ?>大转盘; <?php  } ?>
                    
                </div>
                <?php  } ?>
            </div>
        </div>
         <div class="form-group  become become4" <?php  if($data['become']!='4' ) { ?>style="display:none"<?php  } ?>>
                    <label class="col-lg control-label" ></label>
                    <div class="col-sm-9 col-xs-12">
                    	<?php if(cv('commission.set.edit')) { ?>
                              <?php  echo tpl_selector('become_goodsid',array('url'=>webUrl('goods/query'), 'items'=>$goods,'multi'=>1,'preview'=>true,'buttontext'=>'选择商品','placeholder'=>'请输入商品标题'))?>
                        <?php  } else { ?>
                        	<?php  if(!empty($goods)) { ?>
                                <?php  if(is_array($goods)) { foreach($goods as $item) { ?>
                        		<a href="<?php  echo webUrl('goods/edit',array('id'=>$item['id']))?>" target="_blank"><?php  echo $item['title'];?>(ID: <?php  echo $item['id'];?>)</a>
                                <?php  } } ?>
                        	<?php  } else { ?>
                        		未选择商品
                        	<?php  } ?>
                        <?php  } ?>
                    </div>
           </div>

           
          <div class="form-group becomecon">
                <label class="col-lg control-label"></label>
                <div class="col-sm-5 becomecheck">
                	<?php if(cv('commission.set.edit')) { ?>
                    <label class="radio-inline"><input type="radio"  name="data[become_check]" value="0" <?php  if($data['become_check'] ==0) { ?> checked="checked"<?php  } ?> /> 需要</label>
                    <label class="radio-inline"><input type="radio"  name="data[become_check]" value="1" <?php  if($data['become_check'] ==1) { ?> checked="checked"<?php  } ?> /> 不需要</label>
                    <span class="help-block">是否需要审核</span>
                    <?php  } else { ?>
                    	<?php  if($data['become_check']==0) { ?>需要审核<?php  } else { ?>不需要审核<?php  } ?>
                    <?php  } ?>
                </div>
                <div class="col-sm-4 becomeconsume"  <?php  if(empty($data['become']) || $data['become']=='1') { ?>style="display:none"<?php  } ?>>
                	<?php if(cv('commission.set.edit')) { ?>
                    <label class="radio-inline"><input type="radio"  name="data[become_order]" value="0" <?php  if($data['become_order'] ==0) { ?> checked="checked"<?php  } ?> /> 付款后</label>
                    <label class="radio-inline"><input type="radio"  name="data[become_order]" value="1" <?php  if($data['become_order'] ==1) { ?> checked="checked"<?php  } ?> /> 完成后</label>
                    <span class="help-block">消费条件统计的方式</span>
                    <?php  } else { ?>
                    	消费条件统计的方式: <?php  if($data['become_order'] ==0) { ?>付款后<?php  } else { ?>完成后<?php  } ?>
                    <?php  } ?>
                </div>
           </div>
          

            <div class="form-group protocol-group" <?php  if($data['become'] !=1) { ?>style="display: none;"<?php  } ?>>
            <label class="col-lg control-label">显示申请协议</label>
            <div class="col-sm-8">
                <?php if(cv('commission.set.edit')) { ?>
                <label class="radio-inline"><input type="radio"  name="data[open_protocol]" value="1" <?php  if($data['open_protocol'] ==1) { ?> checked="checked"<?php  } ?> /> 显示</label>
                <label class="radio-inline"><input type="radio"  name="data[open_protocol]" value="0" <?php  if($data['open_protocol'] ==0) { ?> checked="checked"<?php  } ?> /> 隐藏</label>
                <?php  } else { ?>
                <?php  if($data['open_protocol'] ==0) { ?>隐藏<?php  } else { ?>显示<?php  } ?>
                <?php  } ?>
            </div>
            </div>
            <div class="form-group protocol-group" <?php  if($data['become'] !=1) { ?>style="display: none;"<?php  } ?>>
            <label class="col-lg control-label">显示提现协议</label>
            <div class="col-sm-8">
                <?php if(cv('commission.set.edit')) { ?>
                <label class="radio-inline"><input type="radio"  name="data[open_withdrawprotocol]" value="1" <?php  if($data['open_withdrawprotocol'] ==1) { ?> checked="checked"<?php  } ?> /> 显示</label>
                <label class="radio-inline"><input type="radio"  name="data[open_withdrawprotocol]" value="0" <?php  if($data['open_withdrawprotocol'] ==0) { ?> checked="checked"<?php  } ?> /> 隐藏</label>
                <?php  } else { ?>
                <?php  if($data['open_withdrawprotocol'] ==0) { ?>隐藏<?php  } else { ?>显示<?php  } ?>
                <?php  } ?>
            </div>
            </div>
             <div class="form-group protocol-group" <?php  if($data['become'] !=1) { ?>style="display: none;"<?php  } ?>>
                <label class="col-lg control-label">小程序申请模板</label>
                <div class="col-sm-8" >
                    <?php if(cv('commission.set.edit')) { ?>
                    <select name="data[template_flag]">
                     <option value="0"  <?php  if($data['template_flag']==0) { ?>selected="selected"<?php  } ?>>默认模板1</option>
                     <option value="1" <?php  if($data['template_flag']==1) { ?>selected="selected"<?php  } ?>>默认模板2</option>
                    </select>

                    <?php  } else { ?> 
                    <div class='form-control-static'>
                       
            
                    </div>
                    <?php  } ?>
                </div>
            </div>

           <div class="form-group">
                <label class="col-lg control-label">分销商必须完善资料</label>
                <div class="col-sm-9 col-xs-12">
                	<?php if(cv('commission.set.edit')) { ?>
                    <label class="radio-inline"><input type="radio"  name="data[become_reg]" value="0" <?php  if($data['become_reg'] ==0) { ?> checked="checked"<?php  } ?> /> 需要</label>
                    <label class="radio-inline"><input type="radio"  name="data[become_reg]" value="1" <?php  if($data['become_reg'] ==1) { ?> checked="checked"<?php  } ?> /> 不需要</label>
                    <span class="help-block">分销商在分销或提现时是否必须完善资料</span>
                    <?php  } else { ?>
                    	分销商在分销或提现时是否必须完善资料: <?php  if($data['become_reg'] ==0) { ?>需要<?php  } else { ?>不需要<?php  } ?>
                    <?php  } ?>
                </div>
           </div>

            <div class="form-group">
                <label class="col-lg control-label">非分销商链接</label>
                <div class="col-sm-9 col-xs-12">
                    <?php if(cv('commission.set.edit')) { ?>
                    <input type="text" name="data[no_commission_url]" class="form-control" value="<?php  echo $data['no_commission_url'];?>">
                    <?php  } else { ?>
                    <?php  echo $data['no_commission_url'];?>
                    <?php  } ?>
                    <span class="help-block">自定义非分销商点击分销中心链接 ; 如果不填写 则走默认;</span>
                </div>
            </div>
<script type="text/javascript">
    $(document).ready(function () {
        var type = $('input[name="data[checkpower]"]:checked').val();
        if(type == 0){
            $("#powertype").hide();
        } else {
            $("#powertype").show();
        }
        var level = $('input[name="data[powertype]"]:checked').val();
        if(level == 0 || type == 0){
            $("#userlevel").hide();
        } else {
            $("#userlevel").show();
        }

        $('input[name="data[checkpower]"]').click(function(){
            var val = $(this).val();
            var level2 = $('input[name="data[powertype]"]:checked').val();
            if(val == 0){
                $("#powertype").hide();
                $("#userlevel").hide();
            } else {
                $("#powertype").show();
            }
            if (val == 1 && level2 == 1) {
                $("#userlevel").show();
            }
        });
        $('input[name="data[powertype]"]').click(function(){
            var vals = $(this).val();
            if(vals == 0){
                $("#userlevel").hide();
            } else {
                $("#userlevel").show();
            }
        });

        $("input[type='checkbox']").click(function () {
            var th = $(this).attr("name");
            var arr = new Array();
            $("input[name='"+th+"']:checked").each(function (i,m) {
                arr += $(this).val()+",";
            });
            var len = $("input[name='data[power]["+th+"]']").length;
            var content = "<input type='hidden' name='data[power]["+th+"]' value='"+arr+"'>";
            if(len < 1){
                $("#userlevel").append(content);
            } else {
                $("input[name='data[power]["+th+"]']").val(arr);
            }
            
        });
    });
</script>      
<!-- yi fu yuan ma wang -->