<?php defined('IN_IA') or exit('Access Denied');?>	<div class="fui-list follow_topbar" style="background: <?php  echo $diyfollowbar['style']['background'];?>">
	   	<div class="fui-list-media">
	   		<img class="<?php  echo $diyfollowbar['params']['iconstyle'];?>" src="<?php  echo $diyfollowbar['logo'];?>">
	   	</div>
	    <div class="fui-list-inner">
	    	<div class="text" style="color: <?php  echo $diyfollowbar['style']['textcolor'];?>;"><?php  echo $diyfollowbar['text'];?></div>
	    </div>
   		<div class="fui-list-angle">
   			<div class="btn btn-success external" data-followurl="<?php  echo $diyfollowbar['link'];?>" data-qrcode="<?php  echo $diyfollowbar['qrcode'];?>" id="followbtn" style="border: none; color: <?php  echo $diyfollowbar['style']['btncolor'];?>; background: <?php  echo $diyfollowbar['style']['btnbgcolor'];?>;"><?php  if(!empty($diyfollowbar['params']['btnicon'])) { ?><i class="icon <?php  echo $diyfollowbar['params']['btnicon'];?>" style="font-size: 0.6rem;"></i> <?php  } ?><?php  if(!empty($diyfollowbar['params']['btntext'])) { ?><?php  echo $diyfollowbar['params']['btntext'];?><?php  } else { ?>点击关注<?php  } ?></div>
   		</div> 
   	</div>

	<?php  if(!empty($diyfollowbar['qrcode'])) { ?>
		<div class="follow_hidden" style="display: none;">
			<div class="verify-pop">
				<div class="close"><i class="icon icon-roundclose"></i></div>
				<div class="qrcode" style="height: 250px;">
					<img class="qrimg" src="" />
				</div>
				<div class="tip">
					<p class="text-white">长按识别二维码关注</p>
					<p class="text-warning" style="color: <?php  echo $diyfollowbar['style']['highlight'];?>;"><?php  echo $_W['shopset']['shop']['name'];?></p>
				</div>
			</div>
		</div>
   	<?php  } ?>
   	<script>
   		$(function(){
   			var _followbtn = $("#followbtn");
   			var _followurl = _followbtn.data("followurl");
   			var _qrcode = _followbtn.data("qrcode");
   			_followbtn.click(function(){
   				if(_qrcode){
   					$('.verify-pop').find('.qrimg').attr('src', _qrcode).show();
   					follow_container = new FoxUIModal({
   						content: $(".follow_hidden").html(),
   						extraClass: "popup-modal",
   						maskClick:function(){
   							follow_container.close();
   						}
   					});
   					follow_container.show();
   					$('.verify-pop').find('.close').unbind('click').click(function () {
		        		follow_container.close();
		        	});
   				}
   				else if(_followurl){
   					location.href = _followurl;
   				}
   				return;
   			});
   		});
   	</script>
   	

<!--yifuyuanma-->