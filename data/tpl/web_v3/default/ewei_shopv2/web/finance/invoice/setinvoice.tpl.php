<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>
<div class="page-header">当前位置：<span class="text-primary">发票设置</span></div>
    <div class="page-content">
        <form action="" method="post" class="form-horizontal form-validate" enctype="multipart/form-data" >
            <div class="summary_box">
                <div class="summary_title">
                    <span class=" title_inner">发票设置说明</span>
                </div>
                <div class="summary_lg">
                    <p>1.如您本次购物选择“普通纸质发票”，系统将为您开具普通纸质发票，
                普通纸质发票是税务机关认可的有效收付款凭证，具有法律效力，可用于报销入账、售后维权等</p>
                    <p>2.如您本次购物选择“增值税专用发票”，系统将为您开具增值税专用发票，
                增值税专用发票是税务机关认可的有效收付款凭证，具有法律效力，可用于报销入账、售后维权等</p>            
                </div>
            </div>

            <div class="form-group">
            <label class="col-lg control-label">发票入口</label>
            <div class="col-sm-9 col-xs-12">
                <p class='form-control-static'>
                    <a href='javascript:;' class="js-clip" title='点击复制链接' data-url="<?php  echo $url;?>" ><?php  echo $url;?></a>
                    <span style="cursor: pointer;" data-toggle="popover" data-trigger="hover" data-html="true"
                          data-content="<img src='<?php  echo m('qrcode')->createQrcode($url)?>' width='130' alt='链接二维码'>" data-placement="auto right">
                        <i class="glyphicon glyphicon-qrcode"></i>
                    </span>
                </p>
            </div>
        </div>
            <div class="form-group">
                <label class="col-lg control-label">发票支持</label>
                <div class="col-sm-9 col-xs-12">                
                    <?php if(cv('creditinvoice.set.edit')) { ?>                          
                    <label class='radio radio-inline'>
                        <input type='radio' value='1' name='data[set_invoice]'  <?php  if($data['set_invoice']==1) { ?>checked<?php  } ?> /> 是
                    </label>
                    <label class='radio radio-inline'>
                        <input type='radio' value='0' name='data[set_invoice]' <?php  if($data['set_invoice']==0) { ?>checked<?php  } ?> /> 否
                    </label>
                    <?php  } else { ?>
                    <div class='form-control-static'><?php  if($data['set_invoice']==1) { ?>是<?php  } else { ?>否<?php  } ?></div>
                    <?php  } ?>
                </div>
            </div>
            <?php if(cv('creditinvoice.set.edit')) { ?>  
            <div class="form-group row">
                <label class="col-sm control-label must"  style="width:140px; margin-left:15px;">普通发票(%)</label>
                <div class="col-md-10">
                    <div class="col-sm-2"  style="padding:0;" >
                        <input type="text" name="data[commoninvoice]" class="form-control" value="<?php echo empty($data['commoninvoice'])?'':$data['commoninvoice']?>" placeholder="普通发票"/>
                    </div>
                    <label class="col-sm control-label must"  style="width:140px; margin-left:15px;">增值税专用发票(%)</label>
                    <div class="col-sm-2">
                      <input type="text" name="data[addinvoice]" class="form-control"value="<?php echo empty($data['addinvoice'])?'':$data['addinvoice']?>" placeholder="增值税专用发票"/>
                    </div>
                </div>
            </div> 
            <?php  } ?>
            
            <div class="form-group">
                <label class="col-lg control-label"></label>
                <div class="col-sm-9 col-xs-12">              
                <input type="submit" value="提交" class="btn btn-primary"  />                
                </div>
            </div>
        </form>
    </div>
    
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>
<!--efwww_com54mI5p2D5omA5pyJ-->