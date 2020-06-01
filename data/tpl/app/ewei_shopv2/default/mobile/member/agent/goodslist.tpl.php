<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>
<style type="text/css">
	.fui-numbers {
	    backface-visibility: hidden;
	    box-sizing: border-box;
	    position: relative;
	    font-size: 0.8rem;
	    justify-content: space-between;
	    margin: 0;
	    height: 1.5rem;
	    width: 5rem;
	    line-height: 1.5rem;
	    text-align: center;
	    font-size: 14px;
    	color: #222;
    	border: 1px solid #999;
    	border-radius: 1.5rem;
	}
</style>
<div class='fui-page  fui-page-current member-cart-page'>
	<div class="fui-header">
		<div class="fui-header-left">
			<a class="back"></a>
		</div>
		<div class="title">推广产品</div>
		<div class="fui-header-right">
		</div>
	</div>
	<div class='fui-content cart-list'>
		<div id="cart_container">
			<?php  if(!empty($goodslist)) { ?>
			<div class="fui-list-group" id="container" style="margin-top: 0">
			<?php  if(is_array($goodslist)) { foreach($goodslist as $g) { ?>
				<div class="fui-list goods-item align-start">
					<div class="fui-list-media ">
					</div>
					<div class="fui-list-media image-media" style="margin-left:0px;">
						<img data-lazy="<?php  echo tomedia($g['thumb'])?>" class="">
					</div>
					<div class="fui-list-inner">
						<div class="title" style="font-size: 14px">
							<?php  echo $g['title'];?>
						</div>
						<div class="subtitle">
							<?php  echo $g['subtitle'];?>
						</div>
						<div class='price' style="height:1.5rem;line-height: 1.5rem;">
							<span class="bigprice text-danger">￥<span class='marketprice'><?php  echo $g['marketprice'];?></span></span>
							<div class="fui-numbers sharegoods" data-goodscode="<?php  echo $g['goodscode'];?>">分享</div>
						</div>
					</div>
				</div>
			<?php  } } ?>
			</div>
			<?php  } else { ?>
				<div class='content-empty'>
					<img src="<?php echo EWEI_SHOPV2_STATIC;?>images/nolist.png" style="width: 6rem;margin-bottom: .5rem;margin-top: 10px;"><br/><p style="color: #999;font-size: .75rem">暂无可推广的产品哦！</p>
				</div>
			<?php  } ?>
		</div>
	</div>
</div>
<div id="alert-picker">


    <div id="alert-mask"></div>
    <?php  if($commission_data['codeShare'] == 1) { ?>
    <div class="alert-content">
        <div class="alert" style="padding:0;">
            <i class="alert-close alert-close1 icon icon-close"></i>
            <div class="fui-list alert-header" style="-webkit-border-radius: 0.3rem;border-radius: 0.3rem;padding:0;">
                <img src="<?php  echo tomedia($goodscode)?>" width="100%" height="100%" class="alert-goods-img" alt="">
            </div>
        </div>
    </div>
    <?php  } else if($commission_data['codeShare'] == 2) { ?>
    <div class="alert-content">
        <div class="alert2">
            <div class="fui-list alert-header" style="-webkit-border-radius: 0.3rem;border-radius: 0.3rem;padding:0;">
                <img src="<?php  echo tomedia($goodscode)?>" width="100%" height="100%" class="alert-goods-img" alt="">
            </div>
        </div>
    </div>
    <?php  } else { ?>
    <div class="alert-content">
        <div class="alert" style="padding:0;background: #f5f4f9;border:none;-webkit-border-radius: 0.3rem;border-radius: 0.3rem;top:2rem;">
            <i class="alert-close alert-close1 icon icon-close" style="right: -0.7rem;top: -0.8rem;background: #e1040d;border:none;z-index:10"></i>
            <div class="fui-list alert-header" style="-webkit-border-radius: 0.3rem;border-radius: 0.3rem;padding:0;">
                <img src="<?php  echo tomedia($goodscode)?>" class="alert-goods-img" alt="">
            </div>

        </div>
    </div>
    <?php  } ?>
</div>
<script type="text/javascript" src="../addons/ewei_shopv2/static/js/dist/jquery/jquery.qrcode.min.js"></script>
<script type="text/javascript">
    $(function(){
        $(".alert-qrcode-i").html('')
        $(".alert-qrcode-i").qrcode({
            typeNumber: 0,      //计算模式
            correctLevel: 0,//纠错等级
            text:"<?php  echo mobileUrl('goods/detail', array('id'=>$goods['id'],'mid'=>$member['id']),true)?>"
        });
    });
    $(".sharegoods").on("click", function() {
    	var goodscode = $(this).data('goodscode');
    	$(".alert-goods-img").attr('src',goodscode);
    	$("#alert-picker").show()
    })
    $('.alert-close').on("click", function() {
    	console.log(121212);
        $("#alert-picker").hide()
    });
    $('#alert-mask').on("click", function() {
    	console.log(121212);
        $("#alert-picker").hide()
    });
</script>
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>
<!--efwww_com-->