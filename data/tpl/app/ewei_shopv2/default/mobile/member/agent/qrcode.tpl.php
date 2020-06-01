<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('commission/common', TEMPLATE_INCLUDEPATH)) : (include template('commission/common', TEMPLATE_INCLUDEPATH));?>
<div class="fui-page fui-page-current page-commission-shares">
    <div class="fui-header">
        <div class="fui-header-left">
            <a class="back"></a>
        </div>
        <div class="title"><?php  if($level['level']>91) { ?>代理<?php  } ?>推广二维码</div>
        <div class="fui-header-right"></div>
    </div>
     <!-- 系统生成图片 开始 -->
    <!--<div class="fui-content" style="background: #fff;">-->
    <!--    <div style="padding: 0;overflow: hidden;height: auto;">-->
	   <!-- <div class='fui-cell-group'>-->
    <!--        <img style="width:100%;" src="<?php  echo $img;?>" />-->
	   <!-- </div>-->
    <!--    </div>-->
    <!--</div>-->
    <div style="display: none;">
        <!-- $qr['qrimg']  $poster['bg'] -->
        <img id="qr" src="<?php  echo $qr['qrimg']?>" >
         <img id="poster" src="<?php  echo $poster['bg']?>" >
    </div>
    <script type="text/javascript" src="../addons/ewei_shopv2/static/js/app/biz/member/qrcode.js"></script>
     <div class="fui-content">
      <div style="width: 100%;height: 100%;overflow: hidden;">
        <div style="height: 100%;width: 100%;">
            <!-- <img style="width:100%;height: 100%" src="<?php  echo $img;?>" /> -->
        <img  style="width: 100%;height: 100%"  id="agent">
        </div>
        </div>
  </div>
</div>
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>
