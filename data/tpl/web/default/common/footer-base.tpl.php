<?php defined('IN_IA') or exit('Access Denied');?></div>
	<!-- <div class="container-fluid footer text-center" role="footer">	
		<div class="friend-link">
			<?php  if(empty($_W['setting']['copyright']['footerright'])) { ?>
				
			<?php  } else { ?>
				<?php  echo $_W['setting']['copyright']['footerright'];?>
			<?php  } ?>
		</div>
		<div class="copyright"><?php  echo $_W['setting']['copyright']['footerleft'];?></div>
		<div><?php  if(!empty($_W['setting']['copyright']['icp'])) { ?>备案号：<?php  echo $_W['setting']['copyright']['icp'];?><?php  } ?></div>
	</div> -->
	<?php  if(!empty($_W['setting']['copyright']['statcode'])) { ?><?php  echo $_W['setting']['copyright']['statcode'];?><?php  } ?>
	<?php  if(!empty($_GPC['m']) && !in_array($_GPC['m'], array('keyword', 'special', 'welcome', 'default', 'userapi')) || defined('IN_MODULE')) { ?>
	<script>
		if(typeof $.fn.tooltip != 'function' || typeof $.fn.tab != 'function' || typeof $.fn.modal != 'function' || typeof $.fn.dropdown != 'function') {
			require(['bootstrap']);
		}
	</script>
	<?php  } ?>
	</div>
	
		<script type="text/javascript" src="<?php  echo $_W['siteroot'];?>web/index.php?c=utility&a=visit&do=showjs&type=<?php echo FRAME;?>"></script>
	
<script>$(function(){$('img').attr('onerror', '').on('error', function(){if (!$(this).data('check-src') && (this.src.indexOf('http://') > -1 || this.src.indexOf('https://') > -1)) {this.src = this.src.indexOf('http://yeshi.kaifa1688.cn/attachment/') == -1 ? this.src.replace('http://yeshiyiyao.oss-cn-beijing.aliyuncs.com/', 'http://yeshi.kaifa1688.cn/attachment/') : this.src.replace('http://yeshi.kaifa1688.cn/attachment/', 'http://yeshiyiyao.oss-cn-beijing.aliyuncs.com/');$(this).data('check-src', true);}});});</script></body>
</html>