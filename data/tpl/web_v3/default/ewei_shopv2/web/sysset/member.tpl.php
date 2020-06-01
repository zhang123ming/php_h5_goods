<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>

<div class="page-header">当前位置：<span class="text-primary">会员设置</span></div>

  <div class="page-content">
      <form action="" method="post" class="form-horizontal form-validate" enctype="multipart/form-data" >



          <div class="form-group">
              <label class="col-lg control-label">会员等级说明链接</label>
              <div class="col-sm-9 col-xs-12">
                  <?php if(cv('sysset.member.edit')) { ?>
                  <div class="input-group">
                      <input type="text" name="data[levelurl]" class="form-control" value="<?php  echo $data['levelurl'];?>" id="levelurl" />
                      <div class="input-group-btn">
                          <div class="btn btn-default" data-toggle="selectUrl" data-input="#levelurl">选择链接</div>
                      </div>
                  </div>
                  <?php  } else { ?>
                  <input type="hidden" name="data[levelurl]" value="<?php  echo $data['levelurl'];?>" />
                  <div class='form-control-static'><?php  echo $data['levelurl'];?></div>
                  <?php  } ?>
              </div>
          </div>
          <div class="form-group">
              <label class="col-lg control-label">会员等级升级依据</label>
              <div class="col-sm-9 col-xs-12">
                  <?php if(cv('sysset.member.edit')) { ?>
                  <label class="radio radio-inline">
                      <input type="radio" name="data[leveltype]" value="0" <?php  if(empty($data['leveltype'])) { ?>checked<?php  } ?>/> 已完成的订单金额
                  </label>
                  <label class="radio radio-inline">
                      <input type="radio" name="data[leveltype]" value="1" <?php  if($data['leveltype']==1) { ?>checked<?php  } ?>/> 已完成的订单数量
                  </label>
                  <span class="help-block">默认为完成订单金额</span>
                  <?php  } else { ?>
                  <input type="hidden" name="data[leveltype]" value="<?php  echo $data['leveltype'];?>" />
                  <div class='form-control-static'>
                      <?php  if(empty($data['leveltype'])) { ?>
                      已完成的订单金额
                      <?php  } else if($data['leveltype']==1) { ?>
                      已完成的订单数量
                      <?php  } ?>
                  </div>
                  <?php  } ?>
              </div>
          </div>

          <div class="form-group">
              <label class="col-lg control-label">会员充值金额</label>
              <div class="col-sm-9 col-xs-12">
                  <?php if(cv('sysset.member.edit')) { ?>
                  <label class="radio radio-inline">
                      <input type="radio" name="data[chargetype]" value="0" <?php  if(empty($data['chargetype'])) { ?>checked<?php  } ?>/> 关闭
                  </label>
                  <label class="radio radio-inline">
                      <input type="radio" name="data[chargetype]" value="1" <?php  if($data['chargetype']==1) { ?>checked<?php  } ?>/> 开启
                  </label>
                  <span class="help-block">默认为完成订单金额</span>
                  <?php  } ?>
              </div>
          </div>

          <div class="form-group">
              <label class="col-lg control-label">会员有效期限</label>
              <div class="col-sm-9 col-xs-12">
                   <div class='input-group fixsingle-input-group'>
                      <input type="text" name="data[timelimit]" class="form-control" value="<?php  echo $data['timelimit'];?>" />
                      <span class='input-group-select'>
                        <select name="data[unit]">
                          <option value="year" <?php  if($data['unit']=='year') { ?>selected="selected"<?php  } ?>>年</option>
                          <option value="month" <?php  if($data['unit']=='month') { ?>selected="selected"<?php  } ?>>月</option>
                          <option value="day" <?php  if($data['unit']=='day') { ?>selected="selected"<?php  } ?>>日</option>
                        </select>
                      </span>
                    
                  </div>
                    <span class="help-block">时间可以切换</span>
              </div>
          </div>
          
            <div class="form-group">
              <label class="col-lg control-label">会员自动降级</label>
              <div class="col-sm-9 col-xs-12">
                <label class="radio radio-inline">
                    <input type="radio" name="data[opendegrade]" value="0" <?php  if(empty($data['opendegrade'])) { ?>checked<?php  } ?>/> 关闭
                </label>
                <label class="radio radio-inline">
                    <input type="radio" name="data[opendegrade]" value="1" <?php  if($data['opendegrade']==1) { ?>checked<?php  } ?>/> 开启
                </label>
  
              </div>
            </div>
         

            <div class="form-group">
              <label class="col-lg control-label">会员降级</label>
                <div class="col-sm-9 col-xs-12">
                  <span class='input-group-select'>
                    <select name="data[degrade]">
                       <option value="" >默认等级</option>
                      <?php  if(is_array($mLevels)) { foreach($mLevels as $level) { ?>
                      <option value="<?php  echo $level['id'];?>" <?php  if($data['degrade']==$level['id']) { ?>selected="selected"<?php  } ?>><?php  echo $level['levelname'];?></option>
                      <?php  } } ?>
                    </select>
                  </span>
    
                </div>
              </div>

          <div class="form-group">
          <label class="col-lg control-label">小程序会员等级申请</label>
            <div class="col-sm-9 col-xs-12">
                <label class="radio radio-inline">
                    <input type="radio" name="data[openapply]" onclick="$('#opendeapply').hide();" value="0" <?php  if(empty($data['openapply'])) { ?>checked<?php  } ?>/> 关闭
                </label>
                <label class="radio radio-inline">
                    <input type="radio" name="data[openapply]" onclick="$('#opendeapply').show();" value="1" <?php  if($data['openapply']==1) { ?>checked<?php  } ?>/> 开启
                </label>
                 <span lass="radio radio-inline" id="opendeapply" style="display: <?php  if($data['openapply']) { ?>''<?php  } else { ?>'none'<?php  } ?>;">
                    <select name="data[limitgrade]">
                       <option value="" >不限制申请等级</option>
                      <?php  if(is_array($mLevels)) { foreach($mLevels as $level) { ?>
                      <option value="<?php  echo $level['id'];?>" <?php  if($data['limitgrade']==$level['id']) { ?>selected="selected"<?php  } ?>><?php  echo $level['levelname'];?></option>
                      <?php  } } ?>
                    </select>
                  </span>
              </div>
          </div>

          <div class="form-group">
            <label class="col-lg control-label">小程序会员申请免审核</label>
            <label class="radio radio-inline">
                <input type="radio" name="data[notapply]" value="0" <?php  if(empty($data['notapply'])) { ?>checked<?php  } ?>/> 关闭
            </label>
            <label class="radio radio-inline">
                <input type="radio" name="data[notapply]" value="1" <?php  if($data['notapply']==1) { ?>checked<?php  } ?>/> 开启
            </label>
          </div>

        
          <div class="form-group">
              <label class="col-lg control-label">会员完善资料</label>
              <div class="col-sm-9 col-xs-12">
                  <?php if(cv('sysset.member.edit')) { ?>
                  <label class="radio radio-inline">
                      <input type="radio" name="data[isopenconsummate]" value="0" <?php  if(empty($data['isopenconsummate'])) { ?>checked<?php  } ?>/> 关闭
                  </label>
                  <label class="radio radio-inline">
                      <input type="radio" name="data[isopenconsummate]"  value="1" <?php  if($data['isopenconsummate']==1) { ?>checked<?php  } ?>/> 开启
                  </label>
                  <?php  } ?>
              </div>
          </div>
          
          <div class="new_content" <?php  if(intval($data['isopenconsummate']) == 0) { ?>style="display:none"<?php  } ?> id="consummatereward">
            <div class="form-group" >
                <label class="col-lg control-label">会员完善资料奖励</label>
                <div class="col-sm-9 col-xs-12">
                   <select class="form-control" id="commissionMode" style="width: 200px;" name="data[consummatetype]">
                      <option <?php  if($data['consummatetype']=='coupon') { ?> selected <?php  } ?> value="coupon">卡券</option>
                    </select>

                </div>
            </div>


            <div class="form-group">
                <?php if( ce('sale.sendticket' ,$item) ) { ?>
                <label class="col-lg control-label must">选择优惠券</label>
                <div class="col-sm-9 col-xs-12">
                    <?php  echo tpl_selector('couponid',array('required'=>false,'multi'=>1,'type'=>'coupon_cp','autosearch'=>1, 'preview'=>true,'url'=>webUrl('sale/coupon/querycplist'),'text'=>'couponname','items'=>$coupons,'readonly'=>true,'buttontext'=>'选择优惠券','placeholder'=>'请选择优惠券'))?>
                </div>
                <?php  } else { ?>
                <?php  if(!empty($item)) { ?>
                <table class="table">
                    <thead>
                    <tr>
                        <th style='width:100px;'>优惠券名称</th>
                        <th style='width:200px;'></th>
                        <th></th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody id="param-items" class="ui-sortable">
                    <?php  if(is_array($coupon)) { foreach($coupon as $row) { ?>
                    <tr class='multi-product-item' data-id="<?php  echo $row['id'];?>">
                        <input type='hidden' class='form-control img-textname' readonly='' value="<?php  echo $row['couponname'];?>">
                        <input type='hidden' value="<?php  echo $row['id'];?>" name="couponid[]">
                        <td style='width:80px;'>
                            <img src="<?php  echo tomedia($row['thumb'])?>" style="width:70px;border:1px solid #ccc;padding:1px"  onerror="this.src='../addons/ewei_shopv2/static/images/nopic.png'">
                        </td>
                        <td style='width:220px;'><?php  echo $row['couponname'];?></td>
                        <td>
                            <input class='form-control valid' type='text' disabled value="<?php  echo $item['coupontotal'];?>" name="coupontotal<?php  echo $row['id'];?>">
                        </td>
                        <td>
                            <input class='form-control valid' type='text' disabled value="<?php  echo $item['couponlimit'];?>" name="couponlimit<?php  echo $row['id'];?>">
                        </td>
                    </tr>
                    <?php  } } ?>
                    </tbody>
                </table>
                <?php  } else { ?>
                暂无优惠券
                <?php  } ?>
                <?php  } ?>
            </div>
          </div>





          <script>
            doConsummate();
            $("input[name='data[isopenconsummate]']").click(function(){
                var current  = $(this).val();
                if (current==1) {
                   $("#consummatereward").show();
                }else{
                   $("#consummatereward").hide();
                }
            
            })

            function doConsummate(){
              var current = $("input[name='data[isopenconsummate]']:checked").val();

              if (current==1) {
                 $("#consummatereward").show();
              }else{
                  $("#consummatereward").hide();
              }

            }

          </script>

          <div class="form-group">
              <label class="col-lg control-label"></label>
              <div class="col-sm-9 col-xs-12">
                  <?php if(cv('sysset.member.edit')) { ?>
                  <input type="submit" value="提交" class="btn btn-primary"  />

                  <?php  } ?>
              </div>
          </div>

      </form>
  </div>

<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>     

<!--efwww_com-->