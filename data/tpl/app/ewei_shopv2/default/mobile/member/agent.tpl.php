<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>
<script>document.title = "<?php  echo $this->set['texts']['center']?>"; </script>
<script>
    document.documentElement.style.fontSize =document.documentElement.clientWidth/750*40 +"px";
</script>
<script src="//at.alicdn.com/t/font_82607_6xtuyfrxik6v42t9.js"></script>
<style type="text/css">
	.icon1 {
		width: .8rem; height: .8rem;
		vertical-align: -0.15em;
		fill: currentColor;
		overflow: hidden;
	}
</style>
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('commission/common', TEMPLATE_INCLUDEPATH)) : (include template('commission/common', TEMPLATE_INCLUDEPATH));?>
<div class="fui-page fui-page-current page-commission-index ">
	<div class="fui-header">
		<div class="fui-header-left">
			<!-- <a class="back" onclick='location.back()'></a> -->
		</div>
		<div class="title">代理中心</div>
		<div class="fui-header-right"></div>
	</div>
    <div class='fui-content navbar'>
    <?php  $this->followBar()?>
	<div class="headinfo">
	    <div class="userinfo" style="height: auto;">
			<div class='fui-list'>
				<div class='fui-list-media'><img src="<?php  echo $member['avatar'];?>" /></div>
				<div class='fui-list-info' style="width: 12rem">
				<div class='title'><?php  echo $member['nickname'];?> </div>
					<div class='subtitle' style="margin: 10px 0px;">
						<?php echo $level['levelname']?$level['levelname']:'普通会员'?>
						<!-- <i class='icon icon-question' style='font-size:8px;'></i> -->
					</div>
				</div>
			</div>
			<a class="setbtn external" href="<?php  echo mobileUrl('member/info')?>"><i class="icon icon-shezhi"></i></a>
	    </div>
	</div>
		<div class="fui-cell-group">
          	<a class="fui-cell" href="<?php  echo mobileUrl('member/agent/qrcode')?>">
				<div class="fui-cell-icon ">
					<i class="icon icon-erweima1 text-yellow"></i>
				</div>
				<div class="fui-cell-text"><?php  if($level['level']>91) { ?>代理<?php  } ?>推广二维码</div>
				<div class="fui-cell-remark" style="font-size: 0.5rem;"></div>
			</a>
		</div>
		<!-- <div class="userblock">
			<div class="line usable new">
				<div class="text">
					<div class="num"><?php  echo number_format( $member['credit3'],2)?></div>
					<div class="title">可提现佣金(元)</div>
				</div>
				<?php  if($cansettle) { ?>
				<a  style="color:#fff;background-color:#FEA23F;" class="btn btn-warning external" href="<?php  echo mobileUrl('commission/withdraw2')?>"><span style="line-height: 1;color:#fff;">佣金提现</span></a>
				<?php  } else { ?>
				<div style="color:#fff;background-color:#FEA23F;" class="btn btn-warning disabled" onclick="FoxUI.toast.show('满 <?php  echo $set['withdraw']?> <?php  echo $set['texts']['yuan']?>才能提现!')">佣金提现</div>
				<?php  } ?>
			</div>
		</div> -->
	<div class="fui-block-group new col-2" style='overflow: hidden;'>
		<?php  if($level['level']==91) { ?>
        <a style="border-bottom: 1px solid #ebebeb;" class="fui-block-child new  external" href="<?php  echo mobileUrl('member/agent/goodslist')?>">
            <div class="icon " style="color: #ff4357;"><i class="icon icon-goods1"></i></div>
            <div class="text new">
				<div class="title">推广产品</div>
                <div class=""><span><?php  echo $goodsnum;?></span>个</div>
			</div>
        </a>
        <?php  } ?>
        <!-- <a style="border-bottom: 1px solid #ebebeb;" class="fui-block-child new external" href="<?php  echo mobileUrl('commission/order')?>">
            <div class="icon" style="color: #9ec9f4;"><i class="icon icon-dingdan2"></i></div>
			<div class="text new">
				<div class="title">奖励明细</div>
				<div class=""><span><?php  echo number_format($lognum,0)?></span> 笔</div>
			</div>
        </a> -->
        <a  style="border-bottom: 1px solid #ebebeb;" class="fui-block-child new  external" href="<?php  echo mobileUrl('member/agent/performance')?>">
            <div class="icon" style="color: #ffbe2e;"><i class="icon icon-tixian1"></i></div>
			<div class="text new">
				<div class="title">我的业绩</div>
				<div class=""><span>查看详情</span></div>
			</div>
        </a>
        <a  style="border-bottom: 1px solid #ebebeb;" class="fui-block-child new  external" href="<?php  echo mobileUrl('member/agent/down')?>">
            <div class="icon "  style="color: #ff6e02;"><i class="icon icon-heilongjiangtubiao11"></i></div>
			<div class="text new">
				<div class="title">我的团队</div>
				<div>
					<span><?php  echo number_format($downcount,0)?></span>人
				</div>
			</div>
        </a>
        <a  style="border-bottom: 1px solid #ebebeb;" class="fui-block-child new  external" href="<?php  echo mobileUrl('member/agent/withdrawalinfo')?>">
            <div class="icon" style="color: #ffbe2e; "> <img style="width: 25px;height: 28px;" src="../addons/ewei_shopv2/static/images/agent.png" alt=""></div>
		<!-- <div class="title">提现资料</div> -->
			<div class="text new">
				<div class="title">提现资料</div>
				<div class=""><span>查看详情</span></div>
			</div>
        </a>
        <!-- <a  style="border-bottom: 1px solid #ebebeb;" class="fui-block-child new  external" href="<?php  echo mobileUrl('commission/log')?>">
            <div class="icon "  style="color: #21BDF1;"><i class="icon icon-text1"></i></div>
			<div class="text new">
				<div class="title">提现明细</div>
				<div>
					<span><?php  echo number_format($applynum,0)?></span> 笔
				</div>
			</div>
        </a> -->
	</div>
    </div>
</div>
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>
