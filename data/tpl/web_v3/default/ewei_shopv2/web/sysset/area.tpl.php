<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>

<div class="page-header"> 当前位置：<span class="text-primary">地址库设置</span>  </div>

<div class="page-content">
    <form action="" method="post" class="form-horizontal form-validate" enctype="multipart/form-data" >


        <div class="form-group">
            <label class="col-lg control-label">地址库选择</label>
            <div class="col-sm-9 col-xs-12">
                <?php if(cv('sysset.area.edit')) { ?>
                <label class="radio-inline">
                    <input type="radio" name="data[new_area]" class="new_area" value="0" <?php  if(empty($data['new_area'])) { ?>checked<?php  } else { ?>disabled<?php  } ?>  /> 旧版
                </label>
                <label class="radio-inline">
                    <input type="radio" name="data[new_area]" class="new_area" value="1" <?php  if($data['new_area']==1) { ?>checked disabled<?php  } ?> /> 新版(数据更新并含街道信息)
                </label>
                <span class='help-block'>新版与旧版数据不兼容,开启后,用户地址库将被清空,并且不能再切换回旧版,请谨慎选择!</span>
                <?php  } else { ?>
                <input type="hidden" name="data[new_area]" value="<?php  echo $data['new_area'];?>" />
                <div class='form-control-static'><?php  if(empty($data['new_area'])) { ?>旧版<?php  } else { ?>新版<?php  } ?></div>
                <?php  } ?>
            </div>
        </div>


        <div class="form-group street-group" <?php  if(empty($data['new_area'])) { ?>style="display:none;"<?php  } ?>>
        <label class="col-lg control-label">收货地址库开启街道</label>
        <div class="col-sm-9 col-xs-12">
            <?php if(cv('sysset.area.edit')) { ?>
            <label class="radio-inline">
                <input type="radio" name="data[address_street]" value="1" <?php  if($data['address_street']==1) { ?>checked<?php  } ?> /> 是
            </label>
            <label class="radio-inline">
                <input type="radio" name="data[address_street]" value="0" <?php  if(empty($data['address_street'])) { ?>checked<?php  } ?> /> 否
            </label>
            <?php  } else { ?>
            <input type="hidden" name="data[address_street]" value="<?php  echo $data['address_street'];?>" />
            <div class='form-control-static'><?php  if(empty($data['address_street'])) { ?>关闭<?php  } else { ?>开启<?php  } ?></div>
            <?php  } ?>
        </div>
</div>


<div class="form-group"></div>
<div class="form-group">
    <label class="col-lg control-label"></label>
    <div class="col-sm-9 col-xs-12">
        <?php if(cv('sysset.area.edit')) { ?>
        <input type="submit" value="提交" class="btn btn-primary"  />

        <?php  } ?>
    </div>
</div>

</form>
</div>

<script language="javascript">
    $(function () {
        $('.new_area').click(function () {
            var type = $(".new_area:checked").val();
            if (type == '1') {
                $('.street-group').show();
            } else {
                $('.street-group').hide();
            }
        });
    })
</script>

<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>


<!--efwww_com-->