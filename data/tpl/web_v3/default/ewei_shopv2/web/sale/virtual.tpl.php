<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>
<div class='page-header'><span>当前位置：<span class="text-primary">关注回复</span></span> </div>
<?php  if(in_array('ewei_shopv2', $module_ban)) { ?>
<div class="alert alert-danger">
    <p style="font-size:18px;font-weight: bold;word-break: break-all">请先到系统设置开启订阅消息  <a href="<?php  echo url('extension/subscribe')?>">点击进入</a></p>
</div>
<?php  } ?>
<div class="page-content">
    <form id="dataform"    <?php if(cv('sale.virtual.edit')) { ?>action="" method="post"<?php  } ?> class="form-horizontal form-validate">
      
          
          <div class="panel panel-default" >
         <div class="panel-body">
        <div class="col-sm-9 col-xs-12">
            <h4  class="set_title">开启关注回复</h4>
            <span> 设置自定义关注回复</span>
        </div>
        <div class="col-lg pull-right" style="padding-top:10px;text-align: right" >
            <?php if(cv('sale.virtual.edit')) { ?>
	<input type="checkbox" class="js-switch" name="data[status]" value="1" <?php  if($data['status']==1) { ?>checked<?php  } ?> />
               <?php  } else { ?>
               <?php  if($data['status']==1) { ?>
               <span class='text-success'>开启</span>
               <?php  } else { ?>
               <span class='text-default'>关闭</span>
               <?php  } ?>
               <?php  } ?>
        </div>
 </div>  
 
                <div id='virtual'  <?php  if(empty($data['status'])) { ?>style="display:none"<?php  } ?>>
                  <div class="form-group">
                       <label class="col-lg control-label">虚拟会员数</label>
                       <div class="col-sm-9 col-xs-12">
                           <?php if(cv('sale.virtual.edit')) { ?>
                          <div class='input-group'>
                                   <input type="text" name="data[virtual_people]"  value="<?php  echo $data['virtual_people'];?>" class="form-control" />
                                   <span class='input-group-addon'>人</span>
                           </div>
                           <div class="help-block"> 会员数  =  虚拟会员数  +  真实关注数!</div>
                           <?php  } else { ?>
                           <div class='form-control-static'><?php  echo $data['virtual_people'];?>人</div>
                           <?php  } ?>
                       </div>
                   </div> 
                
                
                  <div class="form-group">
                       <label class="col-lg control-label">虚拟分销商数</label>
                       <div class="col-sm-9 col-xs-12">
                           <?php if(cv('sale.virtual.edit')) { ?>
                           <div class='input-group'>
                               <input type="text" name="data[virtual_commission]"  value="<?php  echo $data['virtual_commission'];?>" class="form-control" />
                               <span class='input-group-addon'>人</span>
                           </div>
                           <div class="help-block"> 分销商数  =  虚拟分销商数  +  真实分销商数!</div>
                           <?php  } else { ?>
                           <div class='form-control-static'><?php  echo $data['virtual_commission'];?>人</div>
                           <?php  } ?>
                       </div>
                   </div>

              <div class="form-group">
                  <label class="col-lg control-label">首次关注回复内容</label>
                  <div class="col-sm-9 col-xs-12">
                      <?php if(cv('sale.virtual.edit')) { ?>
                      <textarea name="data[virtual_text]" class="form-control" rows="5"><?php  echo $data['virtual_text'];?></textarea>
                      <div class="help-block"> 可用变量 : [昵称] [会员数] [分销商数] [排名]<br>
                      可用代码 : <?php  echo htmlspecialchars("<a href='链接'>进入商城</a>",ENT_QUOTES) ?>
                      </div>
                      <?php  } else { ?>
                      <div class='form-control-static'><?php  echo $data['virtual_text'];?>人</div>
                      <?php  } ?>
                  </div>
              </div>

              <div class="form-group">
                  <label class="col-lg control-label">重新关注回复内容</label>
                  <div class="col-sm-9 col-xs-12">
                      <?php if(cv('sale.virtual.edit')) { ?>
                      <textarea name="data[virtual_text2]" class="form-control" rows="5"><?php  echo $data['virtual_text2'];?></textarea>
                      <div class="help-block"> 可用变量 : [昵称] [会员数] [分销商数] <br>
                          可用代码 : <?php  echo htmlspecialchars("<a href='链接'>进入商城</a>",ENT_QUOTES) ?>
                      </div>
                      <?php  } else { ?>
                      <div class='form-control-static'><?php  echo $data['virtual_text2'];?>人</div>
                      <?php  } ?>
                  </div>
              </div>
          </div>
        </div>
          
				 
                   <?php if(cv('sale.virtual.edit')) { ?>
                <div class="form-group"></div>
                   <div class="form-group">
                            
                           <div class="col-sm-9 col-xs-12">
                                 <input type="submit"  value="保存设置" class="btn btn-primary"/>
                                 
                           </div>
                    </div>
                <?php  } ?>
 
     
    </form>
</div>
 
<script language='javascript'>
  
                $(function () {
                    $(":checkbox[name='data[status]']").click(function () {
                        if ($(this).prop('checked')) {
                            $("#virtual").show();
                        }
                        else {
                            $("#virtual").hide();
                        }
                    })
                   

                })
         
             
	</script>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>

<!--efwww_com-->