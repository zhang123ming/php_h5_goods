<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('commission/common', TEMPLATE_INCLUDEPATH)) : (include template('commission/common', TEMPLATE_INCLUDEPATH));?>
<?php  if($this->set['pageway']==1) { ?>
<style type="text/css">

.pagination {
    display: inline-block;
    padding-left: 0;
    margin: 0;
    border-radius: 4px;
}
.pagination>li {
    display: inline;
}
.pagination > li > span.nobg {
    background: none;
    border: 0;
}
.pagination>li:last-child>a, .pagination>li:last-child>span {
    border-top-right-radius: 4px;
    border-bottom-right-radius: 4px;
}
.pagination>li>a, .pagination>li>span {
    position: relative;
    float: left;
    padding: 6px 12px;
    margin-left: -1px;
    line-height: 1.42857143;
    color: #337ab7;
    text-decoration: none;
    background-color: #fff;
    border: 1px solid #ddd;
}
.pagination >.active>a{
	background: #ff9326;
}
.pager-nav{
	/*display: none;*/
}
.pagination > li > a, .pagination > li > span {
    margin-right: 5px;
    color: #000;
    line-height: 12px;
    font-size: 12px;
    padding: 7px 7px;
}
.pagination>li:first-child>a, .pagination>li:first-child>span {
    margin-left: 0;
    border-top-left-radius: 4px;
    border-bottom-left-radius: 4px;
}
</style>
<?php  } ?>
<div class="fui-page fui-page-current page-commission-down">
    <div class="fui-header">
        <div class="fui-header-left">
            <a class="back"></a>
        </div>
        <div class="title"><?php  echo $this->set['texts']['mydown']?>(<?php  echo $total;?>)</div>
    </div>
    <div class="fui-content navbar">
        <?php  if($this->set['level']>=2) { ?>
        <div class="fui-tab fui-tab-warning" id="tab">
            <a <?php  if($this->set['pageway']==0||($this->set['pageway']==1&&$_GPC['level']<=1)) { ?>class="active"<?php  } ?> href="<?php  if($this->set['pageway']==1) { ?><?php  echo mobileUrl("commission/down",array('level'=>1))?><?php  } else { ?>javascript:void(0)<?php  } ?>" data-tab='level1'><?php  echo $this->set['texts']['c1']?>(<?php  echo $level1;?>)<?php  if(!empty($lev1) && $datas['checkpower'] ==1) { ?><div class="badge" style="background-color: #ff0011;"><?php  echo $lev1;?></div><?php  } ?></a>
            <?php  if($this->set['level']>=2) { ?><a <?php  if($this->set['pageway']==1&&$_GPC['level']==2) { ?>class="active"<?php  } ?> href="<?php  if($this->set['pageway']==1) { ?><?php  echo mobileUrl("commission/down",array('level'=>2))?><?php  } else { ?>javascript:void(0)<?php  } ?>" data-tab='level2'><?php  echo $this->set['texts']['c2']?>(<?php  echo $level2;?>)<?php  if(!empty($lev2) && $datas['checkpower'] ==1) { ?><div class="badge" style="background-color: #ff0011;"><?php  echo $lev2;?></div><?php  } ?></a><?php  } ?>
            <?php  if($this->set['level']>=3) { ?><a <?php  if($this->set['pageway']==1&&$_GPC['level']==3) { ?>class="active"<?php  } ?> href="<?php  if($this->set['pageway']==1) { ?><?php  echo mobileUrl("commission/down",array('level'=>3))?><?php  } else { ?>javascript:void(0)<?php  } ?>" data-tab='level3'><?php  echo $this->set['texts']['c3']?>(<?php  echo $level3;?>)<?php  if(!empty($lev3) && $datas['checkpower'] ==1) { ?><div class="badge" style="background-color: #ff0011;"><?php  echo $lev3;?></div><?php  } ?></a><?php  } ?>
        </div>
        <?php  } ?>

        <div class="fui-title"><i class="icon icon-star text-danger"></i> 代表已成为<?php  echo $this->set['texts']['agent']?>的<?php  echo $this->set['texts']['down']?>
            
        </div>
        <div class="fui-list-group" id="container">
        	<?php  if($this->set['pageway']==1) { ?>
        	<?php  if(is_array($lists)) { foreach($lists as $user) { ?>
        	<a <?php  if($datas['checkpower'] == 1&& $member['agentpower']!='0' && $user.status==0 && $user.isagent==1) { ?> href='<?php  echo mobileUrl("commission/power")?>&id=<?php  echo $user['id'];?>'<?php  } else { ?> href='<?php  echo mobileUrl("commission/downorders")?>&id=<?php  echo $user['id'];?>'<?php  } ?>>
			<div class="fui-list" style="height: 3.5rem;margin-bottom: 0.5rem">
				<div class="fui-list-media">
					<?php  if($user['avatar']) { ?>
					<img data-lazy="<?php  echo $user['avatar'];?>" class="round" style="width:2rem;height:2rem">
					<?php  } else { ?>
					<i class="icon icon-my2" id="<?php  echo $user['id'];?>"></i>
					<?php  } ?>
				</div>
				<div class="fui-list-inner">
					<div class="row">
					      <div class="row-text" >
						 <?php  if($user['isagent']==1&&$user['status']==1) { ?>
						  <i class="icon icon-star text-danger"></i>
						  <?php  } ?>
						  <?php  if($user['nickname']) { ?><?php  echo $user['nickname'];?><?php  } else { ?>未获取<?php  } ?>
					      
					      </div>
					</div>
					<div class="subtitle">
					     <?php  if($user['isagent']==1&&$user['status']==1) { ?>
					    成为<?php  echo $this->set['texts']['agent']?>时间: <?php  echo $user['agenttime'];?>
					    <?php  } else { ?>
					    注册时间: <?php  echo $user['createtime'];?>
					   <?php  } ?>   
					</div>
				</div>

				<div class="row-remark">
					<?php  if($datas['checkpower'] == 1) { ?>
					<?php  if($user['isagent']==1&&$user['status']==0) { ?>
					<div style="font-color:red">待审核</div>
					<?php  } ?>
					<?php  } ?>

					<?php  if($user['isagent']==1&&$user['status']==1) { ?>

						<p><?php  echo $_S['commission']['texts']['commission_total']?> : <?php  echo $user['commission_total'];?></p>
						<p><?php  echo $user['agentcount'];?>个<?php  echo $_S['commission']['texts']['agent']?></p>
						<?php  if($user['ordercount'] !=0) { ?>
							<p><?php  echo $user['ordercount'];?>个订单</p>
						<?php  } ?>

					<?php  } else { ?>

						<p>消费 : <?php  echo $user['moneycount'];?><?php  echo $this->set['texts']['yuan']?></p>
						<p><?php  echo $user['ordercount'];?>个订单</p>

					<?php  } ?>
					 
				</div>
			</div>
			</a>
			<?php  } } ?>
			<?php  } ?>
        </div>
        <?php  if($this->set['pageway']==1) { ?>
        <div>
        	<?php  echo $pager;?>
        </div>
        <?php  } else { ?>
        <div class='infinite-loading'><span class='fui-preloader'></span><span class='text'> 正在加载...</span></div>
        <?php  } ?>
		<div class='content-empty'  style='display:<?php  if(!empty($lists)) { ?>none<?php  } ?>;'>
			<!--<i class='icon icon-group'></i><br/>暂时没有任何数据-->
			<img src="<?php echo EWEI_SHOPV2_STATIC;?>images/nomeb.png" style="width: 6rem;"><br/><p style="color: #999;font-size: .75rem">暂时没有任何数据</p>
		</div>

    </div>


	<script id='tpl_commission_down_list' type='text/html'>
		<%each list as user%>
		<?php  if($datas['checkpower'] == '1' && $member['agentpower']!='0') { ?>
			<%if user.status==0 && user.isagent==1%>
			<a href='<?php  echo mobileUrl("commission/power")?>&id=<%user.id%>'>
			<%else%>
			<a href='<?php  echo mobileUrl("commission/downorders")?>&id=<%user.id%>'>
			<%/if%>
		<?php  } else { ?>
			<a href='<?php  echo mobileUrl("commission/downorders")?>&id=<%user.id%>'>
		<?php  } ?>
		<div class="fui-list" style="height: 3.5rem;margin-bottom: 0.5rem">
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
					  <%if user.isagent==1 && user.status==1%>
					  <i class="icon icon-star text-danger"></i>
					  <%/if%>
					  <%if user.nickname%><%user.nickname%><%else%>未获取<%/if%>
				      
				      </div>
				</div>
				<div class="subtitle">
				      <%if user.isagent==1 && user.status==1%>
				    成为<?php  echo $this->set['texts']['agent']?>时间: <%user.agenttime%>
				    <%else%>
				    注册时间:  <%user.createtime%>
				    <%/if%>    
				</div>
			</div>

			<div class="row-remark">
				<?php  if($datas['checkpower'] == 1) { ?>
				<%if user.status==0 && user.isagent==1%>
				<div style="font-color:red">待审核</div>
				<%/if%>
				<?php  } ?>

				<%if user.isagent==1 && user.status==1%>

					<p><?php  echo $_S['commission']['texts']['commission_total']?> : <%user.commission_total%></p>
					<p><%user.agentcount%>个<?php  echo $_S['commission']['texts']['agent']?></p>

					<%if user.ordercount !=0%>
						<p><%user.ordercount%>个订单</p>
					<%/if%>

				<%else%>

					<p>消费 : <%user.moneycount%><?php  echo $this->set['texts']['yuan']?></p>
					<p><%user.ordercount%>个订单</p>

				<%/if%>
				 
			</div>
		</div>
		</a>
		<%/each%>
	</script>
<?php  if($this->set['pageway']==0) { ?>
	<script language='javascript'>
		require(['../addons/ewei_shopv2/plugin/commission/static/js/down.js'], function (modal) {
			modal.init({fromDetail: false});
		});
	</script>
<?php  } ?>
</div>
<?php  $this->footerMenus()?>
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>
