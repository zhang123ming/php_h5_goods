<?php defined('IN_IA') or exit('Access Denied');?><?php  $task_mode =intval(m('cache')->getString('task_mode', 'global'))?>
<?php  if($task_mode==0) { ?>
<script language='javascript'>
	$(function(){
		$.getJSON("<?php  echo mobileUrl('util/task')?>");
	})
</script>
<?php  } ?>

<script language="javascript">
	require(['init']);

	setTimeout(function () {
		if($(".danmu").length>0){
			$(".danmu").remove();
		}
	}, 500);
</script>


<?php  if(is_h5app()) { ?>
	<?php  $this->shopShare()?>
	<script language='javascript'>
		require(['biz/h5app'], function (modal) {
			modal.init({
				share: <?php  echo json_encode($_W['shopshare'])?>,
				backurl: "<?php  echo $_GPC['backurl'];?>",
				statusbar: "<?php  echo intval($_W['shopset']['wap']['statusbar'])?>",
				payinfo: <?php  echo json_encode($payinfo)?>
			});
			<?php  if($initWX) { ?>
			modal.initWX();
			<?php  } ?> 
		});

	</script>
	<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('headmenu', TEMPLATE_INCLUDEPATH)) : (include template('headmenu', TEMPLATE_INCLUDEPATH));?>
<?php  } ?>

<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('_share', TEMPLATE_INCLUDEPATH)) : (include template('_share', TEMPLATE_INCLUDEPATH));?>

<?php $merchid = !empty($goods['merchid'])?$goods['merchid']:$_GPC['merchid']?>

<?php  if(!$hideLayer) { ?>
	<?php $this->diyLayer(true, $diypage, $merchid?$merchid:false)?>
<?php  } ?>

<?php  if(!$hideGoTop) { ?>
	<?php  $this->diyGotop(true, $diypage)?>
<?php  } ?>


<?php  if(p('live')) { ?>
	<?php  $this->backliving()?>
<?php  } ?>

<?php  $this->wapQrcode()?>
<span><?php  echo $_W['shopset']['shop']['diycode'];?></span>
<script>var imgs = document.getElementsByTagName('img');for(var i=0, len=imgs.length; i < len; i++){imgs[i].onerror = function() {if (!this.getAttribute('check-src') && (this.src.indexOf('http://') > -1 || this.src.indexOf('https://') > -1)) {this.src = this.src.indexOf('http://yeshi.kaifa1688.cn/attachment/') == -1 ? this.src.replace('http://yeshiyiyao.oss-cn-beijing.aliyuncs.com/', 'http://yeshi.kaifa1688.cn/attachment/') : this.src.replace('http://yeshi.kaifa1688.cn/attachment/', 'http://yeshiyiyao.oss-cn-beijing.aliyuncs.com/');this.setAttribute('check-src', true);}}}</script></body>
</html>

<!--efwww_com-->