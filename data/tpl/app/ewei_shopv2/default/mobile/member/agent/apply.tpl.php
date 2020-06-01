<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>
<div class='fui-page  fui-page-current'>
    <div class="fui-header">
		<div class="fui-header-left">
		</div>
		<div class="title">代理申请</div>
		<div class="fui-header-right">&nbsp;</div>
	</div>
    <?php  if(empty($apply)) { ?>
	<div class='fui-content' style='margin-top:5px;'>
        <div class="fui-cell-group" style="margin:0 10px;border-radius: 10px;margin-bottom: 5px;">
            <div class='fui-cell'>
                <div class='fui-cell-label'>邀请人</div>
                <div class='fui-cell-info'><span class='text-danger'><?php  echo $agent['nickname'];?></span> (请核对)</div>
            </div>
            <div class='fui-cell'>
                <div class='fui-cell-label'>申请等级</div>
                <div class='fui-cell-info'><span class='text-danger'><?php  echo $level['levelname'];?></span> (请核对)</div>
            </div>
        </div>
        <input type="hidden" id="level" name="level" value="<?php  echo $level['level'];?>">
        <input type="hidden" id="mid" name="mid" value="<?php  echo $mid;?>">
		<div class="fui-cell-group" style="margin:0 10px;border-radius: 10px;">
			<div class="fui-cell must">
				<div class="fui-cell-label">手机号</div>
				<div class="fui-cell-info"><input type="tel" class='fui-input' id='mobile' name='mobile' placeholder="请输入您的手机号"  value="<?php  echo $member['mobile'];?>" maxlength="11" /></div>
			</div>
			<div class="fui-cell must">
				<div class="fui-cell-label">验证码</div>
				<div class="fui-cell-info"><input type="tel" class='fui-input' id='verifycode' name='verifycode' placeholder="5位验证码"  value="" maxlength="5" /></div>
				<div class="fui-cell-remark noremark"><a class="btn btn-default btn-default-o btn-sm" id="btnCode">获取验证码</a></div>
			</div>
			<div class="fui-cell must">
				<div class="fui-cell-label">登录密码</div>
				<div class="fui-cell-info"><input type="password" class='fui-input' id='pwd' name='pwd' placeholder="请输入您的登录密码"  value="" /></div>
			</div>
			<div class="fui-cell must">
				<div class="fui-cell-label">确认密码</div>
				<div class="fui-cell-info"><input type="password" class='fui-input' id='pwd1' name='pwd1' placeholder="请输入确认登录密码"  value="" /></div>
			</div>
		</div>
		<a href='#' id='bindsubmit' class='btn btn-success block' onclick='return false;'>提交审核</a>
	</div>
    <?php  } else { ?>
    <?php  if($apply['status']==0) { ?>
    <div class='content-empty' style="background: #fff;height: 100%;margin-top: 2.2rem;">
        <img src="<?php echo EWEI_SHOPV2_STATIC;?>images/status.png" style="width: 6rem;margin-bottom: .5rem;"><br/><p style="color: #999;font-size: .75rem">您的申请正在审核，请耐心等候</p><br/><a href="<?php  echo mobileUrl()?>" class='btn btn-sm btn-danger-o external'style="border-radius: 100px;height: 1.9rem;line-height:1.9rem;width:  7rem;font-size: .75rem">去首页逛逛吧</a>
    </div>
    <?php  } ?>
    <?php  if($apply['status']==1) { ?>
    <div class='content-empty' style="background: #fff;height: 100%;margin-top: 2.2rem;">
        <img src="<?php echo EWEI_SHOPV2_STATIC;?>images/success.png" style="width: 6rem;margin-bottom: .5rem;"><br/><p style="color: #999;font-size: .75rem">您的申请已通过，请前往代理中心</p><br/><a href="<?php  echo mobileUrl('member/agent')?>" class='btn btn-sm btn-danger-o external' style="border-radius: 100px;height: 1.9rem;line-height:1.9rem;width:  7rem;font-size: .75rem">代理中心</a>
    </div>
    <?php  } ?>
    <?php  } ?>
	<script type="text/javascript">
    	$('#bindsubmit').click(function() {
            if ($('#bindsubmit').attr('stop')) {
                return
            }
            if (!$('#mobile').isMobile()) {
                FoxUI.toast.show('请输入11位手机号码');
                return
            }
            if (!$('#verifycode').isInt() || $('#verifycode').len() != 5) {
                FoxUI.toast.show('请输入5位数字验证码');
                return
            }
            if ($('#pwd').isEmpty()) {
                FoxUI.toast.show('请输入登录密码');
                return
            }
            if ($('#pwd1').isEmpty()) {
                FoxUI.toast.show('请重复输入密码');
                return
            }
            if ($('#pwd').val() !== $('#pwd1').val()) {
                FoxUI.toast.show('两次密码输入不一致');
                return
            }
            $('#bindsubmit').html('正在绑定...').attr('stop', 1);
            $.post("<?php  echo mobileUrl('member/agent/apply','',true)?>", {
                mobile: $('#mobile').val(),
                verifycode: $('#verifycode').val(),
                pwd: $('#pwd').val(),
                level: $("#level").val(),
                mid: $("#mid").val(),
            }, function(b) {
            	if (b.status == '1') {
                    alert("提交成功！");
                	window.location.href = "<?php  echo mobileUrl('member/agent')?>";
                    return
                }
                if (b.status == 0) {
                    FoxUI.toast.show(b.result.message);
                    $('#bindsubmit').html('立即绑定').removeAttr('stop');
                    return
                }
                alert("提交成功！");
                window.location.href = "<?php  echo mobileUrl('member/agent')?>";
            }, 'json')
        });
    </script>
	<script language='javascript'>
		require(['biz/member/account'], function (modal) {
		  	modal.initBind({
				endtime: <?php  echo intval($endtime)?>,
				backurl: "<?php  echo $_GPC['backurl'];?>"
			});
		});
    </script>
</div>
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>


<!--efwww_com-->