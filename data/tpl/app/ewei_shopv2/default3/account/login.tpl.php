<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>
<link rel="stylesheet" type="text/css" href="../addons/ewei_shopv2/template/account/default3/style.css?v=2.0.0">
<style type="text/css">
    .header {background-image: url("<?php echo empty($set['wap']['bg'])?'../addons/ewei_shopv2/template/account/default3/bg.jpg':tomedia($set['wap']['bg'])?>"); background-repeat: no-repeat}
    .btn {background: <?php  if(!empty($set['wap']['color'])) { ?><?php  echo $set['wap']['color'];?><?php  } else { ?>#43afcf<?php  } ?>;}
    .text a {color: <?php  if(!empty($set['wap']['color'])) { ?><?php  echo $set['wap']['color'];?><?php  } else { ?>#43afcf<?php  } ?>;}
</style>
<div class="fui-page">
    <?php  if(is_h5app()) { ?>
    <div class="fui-header">
        <div class="fui-header-left">
            <a class="back"> </a>
        </div>
        <div class="title">用户登录</div>
        <div class="fui-header-right" data-nomenu="true"></div>
    </div>
    <?php  } ?>
    <div class="fui-content" >
        <div class="header">
            <div class="logo">
                <img src="<?php  echo tomedia($set['shop']['logo'])?>" />
            </div>
            <div class="name"><?php  echo $set['shop']['name'];?></div>
        </div>
            <div class="fui-cell-group">
                <div class="fui-cell">
                    <div class="fui-cell-icon">
                        <i class="icon icon-people"></i>
                    </div>
                    <div class="fui-cell-info">
                        <input type="tel" placeholder="请输入手机号" class="fui-input" maxlength="11" name="mobile" id="mobile" value="<?php  echo trim($_GPC['mobile'])?>" />
                    </div>
                </div>
                <div class="fui-cell">
                    <div class="fui-cell-icon">
                        <i class="icon icon-lock"></i>
                    </div>
                    <div class="fui-cell-info">
                        <input type="password" placeholder="请输入密码" class="fui-input" name="pwd" id="pwd" />
                    </div>
                    <a class="fui-cell-remark" href="<?php  echo $set['wap']['forgeturl'];?>">忘记密码</a>
                </div>
            </div>

        <div class="btn"  style="background: #42afd0;" id="btnSubmit">立即登录</div>
        <div class="text">
            <!--<p>还没有帐号? <a href="<?php  echo $set['wap']['regurl'];?>">立即注册</a></p>-->
        </div>
        <?php  if(is_h5app()) { ?>
            <?php  if(!empty($sns['wx']) || !empty($sns['qq'])) { ?>
            <div class="sns-login">
                <div class="title">
                    <div class="text">第三方登录</div>
                </div>
                <div class="icons">
                    <?php  if(!empty($sns['wx'])) { ?>
                    <div class="item green btn-sns" data-sns="wx" <?php  if(is_ios()) { ?>style="display: none;" id="threeWX"<?php  } ?>><i class="icon icon-wechat1"></i></div>
                    <?php  } ?>
                    <?php  if(!empty($sns['qq'])) { ?>
                    <div class="item blue btn-sns" data-sns="qq"><i class="icon icon-qq" style="font-size: 1.5rem"></i></div>
                    <?php  } ?>
                </div>
            </div>
            <?php  } ?>
        <?php  } ?>

        <script language='javascript'>
            require(['biz/member/account'], function (modal) {
                modal.initLogin({backurl:'<?php  echo $backurl;?>'});
            });
        </script>
    </div>
</div>

<?php  if(is_ios()) { ?>
    <?php  $initWX=true?>
<?php  } ?>
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>