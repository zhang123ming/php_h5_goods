<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>
<link rel="stylesheet" type="text/css" href="../addons/ewei_shopv2/template/account/default3/style.css?v=2.0.0">
<style type="text/css">
    .header {background-image: url("<?php echo empty($set['wap']['bg'])?'../addons/ewei_shopv2/template/account/default3/bg.jpg':tomedia($set['wap']['bg'])?>"); background-repeat: n}
    .btn {background: <?php  if(!empty($set['wap']['color'])) { ?><?php  echo $set['wap']['color'];?><?php  } else { ?>#43afcf<?php  } ?>;}
    .text a {color: <?php  if(!empty($set['wap']['color'])) { ?><?php  echo $set['wap']['color'];?><?php  } else { ?>#43afcf<?php  } ?>;}
</style>
<div class="fui-page">
    <?php  if(is_h5app()) { ?>
    <div class="fui-header">
        <div class="fui-header-left">
            <a class="back"> </a>
        </div>
        <div class="title"><?php  if(empty($type)) { ?>用户注册<?php  } else { ?>找回密码<?php  } ?></div>
        <div class="fui-header-right" data-nomenu="true"></div>
    </div>
    <?php  } ?>
    <div class="fui-content" >
        <div class="header" style="background: ;">
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
                <?php  if(!empty($set['wap']['smsimgcode'])) { ?>
                    <div class="fui-cell">
                        <div class="fui-cell-icon">
                            <i class="icon icon-safe"></i>
                        </div>
                        <div class="fui-cell-info">
                            <input type="tel" placeholder="请输入图形验证码" class="fui-input" maxlength="4" name="verifycode2" id="verifycode2">
                        </div>
                        <img class="fui-cell-remark noremark" src="../web/index.php?c=utility&a=code&r=<?php  echo time()?>" style="width: 5rem" id="btnCode2">
                    </div>
                <?php  } ?>
                <div class="fui-cell">
                    <div class="fui-cell-icon">
                        <i class="icon icon-email"></i>
                    </div>
                    <div class="fui-cell-info">
                        <input type="tel" placeholder="请输入5位短信验证码" class="fui-input" maxlength="5" name="verifycode" id="verifycode">
                    </div>
                    <a class="fui-cell-remark noremark" href="javascript:;" id="btnCode">获取验证码</a>
                </div>
                <div class="fui-cell">
                    <div class="fui-cell-icon">
                        <i class="icon icon-lock"></i>
                    </div>
                    <div class="fui-cell-info">
                        <input type="password" placeholder="请输入密码" class="fui-input" name="pwd" id="pwd" />
                    </div>
                </div>
                <div class="fui-cell">
                    <div class="fui-cell-icon">
                        <i class="icon icon-lock"></i>
                    </div>
                    <div class="fui-cell-info">
                        <input type="password" placeholder="请重复输入密码" class="fui-input" name="pwd1" id="pwd1">
                    </div>
                </div>
            </div>

        <div class="btn" id="btnSubmit"><?php  if(empty($type)) { ?>立即注册<?php  } else { ?>立即找回<?php  } ?></div>
        <div class="text">
            <p>已有帐号? <a href="<?php  echo $set['wap']['loginurl'];?>">立即登录</a></p>
        </div>

        <script language='javascript'>
            require(['biz/member/account'], function (modal) {
                modal.initRf({backurl:'<?php  echo $backurl;?>', type: <?php  echo intval($type)?>, endtime: <?php  echo intval($endtime)?>, imgcode: <?php  echo intval($set['wap']['smsimgcode'])?>});
            });
        </script>
    </div>
</div>
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>