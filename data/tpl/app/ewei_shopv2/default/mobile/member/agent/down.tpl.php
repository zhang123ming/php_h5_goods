<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('commission/common', TEMPLATE_INCLUDEPATH)) : (include template('commission/common', TEMPLATE_INCLUDEPATH));?>
<div class="fui-page fui-page-current page-commission-down">
    <div class="fui-header">
        <div class="fui-header-left">
            <a class="back"></a>
        </div>
        <div class="title">我的团队</div>
    </div>
    <div class="fui-content">
        <?php  if($set['level']>=2) { ?>
        <div class="fui-tab fui-tab-warning" id="tab">
            <a <?php  if($_GPC['level']<=1) { ?>class="active"<?php  } ?> href="javascript:void(0)" data-tab='level1' ><?php  echo $l1;?>(<?php echo $level1?$level1:0?>)</a>
            <?php  if($set['level']>=2 && $level['level']!=92) { ?><a <?php  if($_GPC['level']==2) { ?>class="active"<?php  } ?> href="javascript:void(0)" data-tab='level2'><?php  echo $l2;?>(<?php echo $level2?$level2:0?>)</a><?php  } ?>
        </div>
        <input id="levelid1" type="hidden" name="levelid1" value="<?php  echo $le1['id'];?>">
        <input id="levelid2" type="hidden" name="levelid2" value="<?php  echo $le2['id'];?>">
        <?php  } ?>
        <div class="fui-list-group" id="container">
        </div>
        <div class='infinite-loading'><span class='fui-preloader'></span><span class='text'> 正在加载...</span></div>
		<div class='content-empty'  style='display:<?php  if(!empty($lists)) { ?>none<?php  } ?>;'>
			<img src="<?php echo EWEI_SHOPV2_STATIC;?>images/nomeb.png" style="width: 6rem;"><br/><p style="color: #999;font-size: .75rem">暂时没有任何数据</p>
		</div>
    </div>

	<script id='tpl_commission_down_list' type='text/html'>
		<%each list as user%>
		<a class="fui-list" style="height: 3.5rem;margin-top: 0.5rem" href="<?php  echo mobileUrl('member.agent.order')?>&userid=<%user.id%>">
			<div class="fui-list-media">
				<%if user.avatar%>
				<img data-lazy="<%user.avatar%>" class="round" style="width:2rem;height:2rem">
				<%else%>
				<i class="icon icon-my2" id="<%user.id%>"></i>
				<%/if%>
			</div>
			<div class="fui-list-inner">
				<div class="row">
			    	<div class="row-text" >
				  		<%if user.nickname%><%user.nickname%><%else%>未获取<%/if%>
			    	</div>
				</div>
				<div class="subtitle">
				    等级: <%user.ulevelname%>
				</div>
				<div class="subtitle">
				    注册时间:  <%user.createtime%>  
				</div>
			</div>

			<div class="row-remark">
				<p><%if user.islevel==1%>业绩<%else%>消费<%/if%> : <%user.moneycount%><?php  echo $this->set['texts']['yuan']?></p>
				<p><%user.ordercount%>个订单</p>
			</div>
		</a>
		<%/each%>
	</script>
	<script language='javascript'>
		require(['../addons/ewei_shopv2/static/js/app/biz/member/down.js'], function (modal) {
			modal.init({fromDetail: true});
		});
	</script>
</div>
<!-- <?php  $this->footerMenus()?> -->
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>
