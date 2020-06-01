<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>
<script type="text/javascript" src="../addons/ewei_shopv2/static/js/dist/area/cascade.js"></script>
<div class="page-header">
    当前位置：<span class="text-primary">联系方式</span>
</div>

  <div class="page-content">
      <form action="" method="post" class="form-horizontal form-validate" enctype="multipart/form-data" >


          <div class="form-group">
              <label class="col-lg control-label">客服电话</label>
              <div class="col-sm-9 col-xs-12">
                  <?php if(cv('sysset.contact.edit')) { ?>
                  <input type="text" name="data[phone]" class="form-control" value="<?php  echo $data['phone'];?>" />
                  <?php  } else { ?>
                  <input type="hidden" name="data[phone]" value="<?php  echo $data['phone'];?>" />
                  <div class='form-control-static'><?php  echo $data['phone'];?></div>
                  <?php  } ?>
              </div>
          </div>

          <div class="form-group">
              <label class="col-lg control-label">所在城市</label>
              <div class="col-sm-9 col-xs-12">
                  <?php if(cv('sysset.shop.edit')) { ?>
                  <select id="sel-provance" name='data[province]' onchange="selectCity();" class="form-control" style='width:200px;display: inline-block' >
                      <option value="" selected="true">省/直辖市</option>
                  </select>
                  <select id="sel-city" name='data[city]'  onchange="selectcounty(0)" class="form-control" style='width:200px;display: inline-block' >
                      <option value="" selected="true">请选择</option>
                  </select>
                  <script language='javascript'>
                      cascdeInit("<?php  echo $new_area?>","0","<?php  echo $data['province'];?>","<?php  echo $data['city'];?>","0");
                  </script>

                  <?php  } else { ?>
                  <textarea name="data[description]" class="form-control richtext" cols="70" style="display:none"><?php  echo $data['description'];?></textarea>
                  <div class='form-control-static'><?php  echo $data['description'];?></div>
                  <?php  } ?>
              </div>
          </div>

          <div class="form-group">
              <label class="col-lg control-label">所在地址</label>
              <div class="col-sm-9 col-xs-12">
                  <?php if(cv('sysset.contact.edit')) { ?>
                  <input type="text" name="data[address]" class="form-control" value="<?php  echo $data['address'];?>" />
                  <?php  } else { ?>
                  <input type="hidden" name="data[address]" value="<?php  echo $data['address'];?>" />
                  <div class='form-control-static'><?php  echo $data['address'];?></div>
                  <?php  } ?>
              </div>
          </div>


          <div class="form-group">
              <label class="col-lg control-label"></label>
              <div class="col-sm-9 col-xs-12">
                  <?php if(cv('sysset.contact.edit')) { ?>
                  <input type="submit"  value="提交" class="btn btn-primary"  />
                  <?php  } ?>
              </div>
          </div>

      </form>
  </div>

<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>     
<!--efwww_com-->