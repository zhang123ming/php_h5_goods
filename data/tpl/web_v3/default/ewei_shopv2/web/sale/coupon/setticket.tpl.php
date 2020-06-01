<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>
<div class="page-header">
    当前位置：<span class="text-primary">活动设置</span>
      
</div>
<div class="page-content">
    <div class="page-toolbar">
        <span class="alert alert-primary">
            暂不支持小程序
        </span>
    </div>
    <ul class="nav nav-arrow-next nav-tabs" id="myTab">
        <li style="position: relative;">
            <a href="<?php  echo webUrl('sale/coupon/shareticket')?>">分享发券</a>
            <span style="position:absolute;right: 9px;top: -7px;"><img src="../addons/ewei_shopv2/static/images/new.png" alt="" ></span>
        </li>
        <li class="active" style="position: relative;">
            <a href="<?php  echo webUrl('sale/coupon/setticket')?>">新人发券</a>
            <span style="position:absolute;right: 9px;top: -7px;"><img src="../addons/ewei_shopv2/static/images/new.png" alt="" ></span>
        </li>
        <li>
            <a href="<?php  echo webUrl('sale/coupon/sendtask')?>">满额送券</a>
        </li>
        <li >
            <a href="<?php  echo webUrl('sale/coupon/goodssend')?>">购物送券</a>
        </li>
        <li >
            <a href="<?php  echo webUrl('sale/coupon/usesendtask')?>">用券送券</a>
        </li>
         <li >
            <a href="<?php  echo webUrl('sale/coupon/sharewxticket')?>">小程序分享送券</a>
        </li>
    </ul>

    <form <?php if( ce('sale.sendticket' ,$item) ) { ?>action="" method="post"<?php  } ?> class="form-horizontal form-validate" enctype="multipart/form-data">

    <div class="alert alert-primary" style="margin-top: 0.8rem">
        <p>说明：</p>
        <p>领取条件：无消费记录的用户即可领取。</p>
        <p>优惠券设置：最多可选择三张优惠券且优惠券须是在有效期内,此处发放的优惠券数量不影响库存！</p>
        <p>活动期限：若不设置活动期限，则默认为活动长期有效。若设置活动期限，则优惠券在活动期限内有效。</p>
    </div>

    <input type="hidden" name="id" value="<?php  echo $item['id'];?>" />
    <div class="tab-content ">
        <div class="tab-pane active">
            <div class="panel-body">
                <div class="form-group">
                    <label class="col-lg control-label">开启活动</label>
                    <div class="col-xs-12 col-sm-8">
                        <div class="input-group">
                            <?php if( ce('sale.package' ,$item) ) { ?>
                            <label class="radio radio-inline">
                                <input type="radio" name="status" value="1" <?php  if(intval($item['status']) ==1 ) { ?>checked="checked"<?php  } ?>> 是
                            </label>
                            <label class="radio radio-inline">
                                <input type="radio" name="status" value="0" <?php  if(intval($item['status']) ==0) { ?>checked="checked"<?php  } ?>> 否
                            </label>
                            <?php  } else { ?>
                            <div class='form-control-static'><?php  if(intval($item['status']) ==1 ) { ?>开启<?php  } else { ?>关闭<?php  } ?></div>
                            <?php  } ?>
                        </div>
                    </div>
                </div>

                <div class="new_content" <?php  if(intval($item['status']) == 0) { ?>style="display:none"<?php  } ?>>
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

                    <div class="form-group">
                        <label class="col-lg control-label">设置活动期限</label>
                        <div class="col-xs-12 col-sm-8">
                            <div class="input-group">
                                <?php if( ce('sale.package' ,$item) ) { ?>
                                <label class="radio radio-inline">
                                    <input type="radio" name="expiration" value="1" <?php  if(intval($item['expiration']) ==1 ) { ?>checked="checked"<?php  } ?>> 是
                                </label>
                                <label class="radio radio-inline">
                                    <input type="radio" name="expiration" value="0" <?php  if(intval($item['expiration']) ==0) { ?>checked="checked"<?php  } ?>> 否
                                </label>
                                <?php  } else { ?>
                                <div class='form-control-static'><?php  if(intval($item['expiration']) ==1 ) { ?>开启<?php  } else { ?>关闭<?php  } ?></div>
                                <?php  } ?>
                            </div>
                        </div>
                    </div>

                <div class="form-group presell_info" <?php  if(intval($item['expiration']) == 0) { ?>style="display:none"<?php  } ?> id="exptime">
                <label class="col-lg control-label">活动有效期限</label>
                <div class="col-sm-9 col-xs-12">
                    <?php if( ce('goods' ,$item) ) { ?>
                    <div class="input-group">
                <span class="input-group-addon">
                    开始时间
                </span>
                        <?php echo tpl_form_field_date('starttime', !empty($item['starttime']) ? date('Y-m-d H:i',$item['starttime']) : date('Y-m-d H:i'),true)?>
                <span class="input-group-addon">
                    结束时间
                </span>
                        <?php echo tpl_form_field_date('endtime', !empty($item['endtime']) ? date('Y-m-d H:i',$item['endtime']) : date('Y-m-d H:i'),true)?>
                    </div>
                    <span class='help-block'></span>
                    <?php  } else { ?>
                    <div class='form-control-static'>
                        <?php  if($item['expiration']==1) { ?> <?php  echo date('Y-m-d H:i',$item['starttime'])?> - <?php  echo date('Y-m-d H:i',$item['endtime'])?> <?php  } ?>
                    </div>
                    <?php  } ?>
                </div>
            </div>

                </div>
            </div>
        </div>

    </div>
    <input type="hidden" name="id" value="<?php  echo $item['id'];?>">
    <input type="hidden" name="cpids" value="<?php  echo $item['cpid'];?>">
    <?php if( ce('sale.sendticket' ,$item) ) { ?>
    <div class="form-group">
        <label class="col-lg control-label"></label>
        <div class="col-sm-9 col-xs-12">
            <input type="submit"  value="提交" class="btn btn-primary" />
        </div>
    </div>
    <?php  } ?>

    </form>
</div>
<script>
    $(function(){
        $("input[name='expiration']").change(function(){
            if($(this).val() == '1'){
                $('#exptime').css('display','block');
            }else if($(this).val() == '0'){
                $('#exptime').css('display','none');
            }
        });

        $("input[name='status']").change(function(){
            if($(this).val() == '1'){
                $('.new_content').css('display','block');
            }else if($(this).val() == '0'){
                $('.new_content').css('display','none');
            }
        });
    });
</script>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>
<!--efwww_com-->