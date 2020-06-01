<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('commission/common', TEMPLATE_INCLUDEPATH)) : (include template('commission/common', TEMPLATE_INCLUDEPATH));?>

<div class="fui-page fui-page-current page-commission-down">
    <div class="fui-header">
        <div class="fui-header-left">
            <a class="back"></a>
        </div>
        <div class="title">我的业绩</div>
    </div>
    <div class="fui-content navbar">
    	<div class='fui-cell-group' style="margin-top:0px;color: #ffsf;">
            <div class='fui-cell'>
                <div class='fui-cell-info' style='width:auto;'>总业绩：<?php  echo $totalperformance;?>元</div>
            </div>
        </div>
        <div class="fui-list-group" id="container">
        	<?php  if(is_array($list)) { foreach($list as $item) { ?>
        	<?php  if($item['goodsprice']>0) { ?>
			<div class="fui-list" style="height: 3.5rem;">
				<div class="fui-list-media">
					<img data-lazy="<?php  echo $item['thumb'];?>" style="width:2rem;height:2rem">
				</div>
				<div class="fui-list-inner">
					<div class="row">
					      <div class="row-text" >
						  <?php  echo $item['title'];?>
					      </div>
					</div>
					<div class="subtitle">
					</div>
				</div>

				<div class="row-remark">
					<p>消费 : <?php  echo $item['goodsprice'];?>元</p>
				</div>
			</div>
			<?php  } ?>
			<?php  } } ?>
        </div>
		<div class='content-empty'  style='display:<?php  if(!empty($list)) { ?>none<?php  } ?>;'>
			<img src="<?php echo EWEI_SHOPV2_STATIC;?>images/nomeb.png" style="width: 6rem;"><br/><p style="color: #999;font-size: .75rem">暂时没有任何数据</p>
		</div>
    </div>
</div>
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>