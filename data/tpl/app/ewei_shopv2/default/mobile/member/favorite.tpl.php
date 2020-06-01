<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>
<style>
	.fui-header~.fui-content {
		top: 36px;
	}

	.container {
		padding-top: 8px;

	}

	.fui-navbar~.fui-content,
	.fui-content.navbar {
		padding-bottom: 0;
	}

	.fui-list-group {
		background: #f1f1f1;
		margin-top: 8px;
	}

	.fui-list {
		background-color: #fff;
		width: 95%;
		margin-left: 2.5%;
		border-radius: 5px;
		margin-top: 8px;

	}
	.noclick {
		overflow: hidden;
	}

	.member-cart-page .fui-footer.editmode .fui-list .fui-list-angle,
	.fui-list-media {
		margin-top: -13px;
	}
</style>
<div class='fui-page  fui-page-current member-cart-page member-favorite-page ' style="background-color: #fff;">
    <div class="fui-header">
	<div class="fui-header-left">
	    <a class="back"></a>
	</div>
	<div class="title">商品收藏</div> 
	<div class="fui-header-right btn-edit" style="display:none">编辑</div>
    </div>

    <div class='fui-content ' >

	<div class='content-empty' style='display:none;'>
	     <img src="<?php echo EWEI_SHOPV2_STATIC;?>images/nofavorite.png" style="width: 6rem;" alt=""><br/>
	     您还没有关注任何商品，何不现在就去逛逛~<br/>
	     			 <a href="<?php  echo mobileUrl()?>" class='btn btn-sm btn-danger-o external'style="border-radius: 100px;height: 1.9rem;line-height:1.9rem;width: 7rem;font-size: .75rem;color:#26Ad95;border-color:#26ad95;margin-top:20px;">去首页逛逛吧</a>

	</div>
	  <div class='fui-list-group container' style="display:none;"></div>
	  <div class='infinite-loading'><span class='fui-preloader'></span><span class='text'> 正在加载...</span></div>
    </div>
    <div class="fui-footer editmode">
	<div class="fui-list noclick">
	    <div class="fui-list-media">
		<label class="checkbox-inline editcheckall"><input type="checkbox" name="checkbox" class="fui-radio fui-radio-danger " />&nbsp;全选</label>
	    </div>

	    <div class='fui-list-angle'>
				<div class="btn  btn-sm btn-default-o btn-finish  " style="color:#999;border-color:#999;">完成</div>
		<div class="btn btn-sm  btn-danger-o btn-delete  disabled">删除</div>
	    </div>
	</div>
    </div>
 
    <script id="tpl_member_favorite_list" type="text/html">
	 
	    <%each list as g index%>
	    <div class="fui-list goods-item align-start" data-id="<%g.id%>" data-goodsid="<%g.goodsid%>">
		<div class="fui-list-media editmode">
		   <input type="checkbox" name="checkbox" class="fui-radio fui-radio-danger edit-item"/>
		</div> 

		<div class="fui-list-media image-media">
		    <a href="<?php  echo mobileUrl('goods/detail')?>&id=<%g.goodsid%>">
			<img data-lazy="<%g.thumb%>" class="round">
		    </a>
		</div>
		<div class="fui-list-inner">
		    <a href="<?php  echo mobileUrl('goods/detail')?>&id=<%g.goodsid%>">
			<div class="text">
			  <%g.title%>
			</div>
		    </a>
			<div class="text"><span class="text-danger">￥<%g.marketprice%><%if g.productprice>0%></span> <span class='oldprice'>￥<%g.productprice%></span><%/if%></div>
		   
		</div>
	    </div>
	  <%/each%>
    </script>
    <script language='javascript'>require(['biz/member/favorite'], function (modal) {
                modal.init();
     });</script>
</div>

<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>
<!--efwww_com-->