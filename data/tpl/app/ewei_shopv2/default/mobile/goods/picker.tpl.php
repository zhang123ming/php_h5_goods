<?php defined('IN_IA') or exit('Access Denied');?>
<script type="text/html" id="option-picker">
    <div class="option-picker">
	<div class="option-picker-inner">
	<div class="option-picker-cell goodinfo">
	    <div class="closebtn"><i class="icon icon-guanbi1"></i></div>
	    <div class="img"><img class='thumb' src="<%goods.thumb%>" /></div>
	    <div class="info info-price text-danger">
			<?php  if($threen &&(!empty($threenprice['price'])||!empty($threenprice['discount']))) { ?>
			<span>&yen<span class=''>
			<?php  if(!empty($threenprice['price'])) { ?>
			<?php  echo $threenprice['price'];?>
			<?php  } else if(!empty($threenprice['discount'])) { ?>
			<?php  echo $threenprice['discount']*$goods['minprice'];?>
			<?php  } ?>
			<?php  } else { ?>
			<span>
				￥
				<span class='price'>
				<?php  if($taskGoodsInfo) { ?>
				<?php  echo $taskGoodsInfo['price'];?>
				<?php  } else { ?>
				<%if goods.ispresell>0 && (goods.preselltimeend == 0 || goods.preselltimeend > goods.thistime)%>
				<%goods.presellprice%>
				<%else%>
				<%if goods.maxprice == goods.minprice%><%goods.minprice%><%else%><%goods.minprice%>~<%goods.maxprice%><%/if%>
				<%/if%>
					<?php  } ?>
				</span>
			</span>

			<?php  } ?>
		</div>
	    <div class="info info-total">
			<%if seckillinfo==false || ( seckillinfo && seckillinfo.status==1) %>
	    		<%if goods.showtotal != 0%><%if goods.unite_total != 0%>总<%/if%>库存 <span class='total'><%goods.total%></span> 件<%/if%>
			<%/if%>
	    </div>
	    <div class="info info-titles"><%if specs.length>0%>请选择规格<%/if%></div>
	</div>
	<div class="option-picker-options">
	<%each specs as spec%>
	    <div class="option-picker-cell option spec">
		<div class="title"><%spec.title%></div>
		<div class="select">
		 <%each spec.items as item%>
		      <a href="javascript:;" class="btn btn-default btn-sm nav spec-item spec-item<%item.id%>" data-id="<%item.id%>" data-thumb="<%item.thumb%>"> <%item.title%> </a>
			<%/each%>
		</div>
	    </div>
	<%/each%> 
	<%=diyformhtml%>

	 <%if seckillinfo==false || ( seckillinfo && seckillinfo.status==1) %>
		<div class="fui-cell-group tb-line" style="margin-top:0">
			<div class="fui-cell">
			<div class="fui-cell-label" style="color:#333;font-size:.8rem;">购买数量</div>
			<div class="fui-cell-info"></div>
			<div class="fui-cell-mask noremark">
				 <div class="fui-number small">
					<div class="minus">-</div>
					<input class="num" type="tel" name="" value="<%if goods.minbuy>0%><%goods.minbuy%><%else%>1<%/if%>"/>
					<div class="plus ">+</div>
				</div>
			</div>
		</div>
			<%else%>
			   <input class="num" type="hidden" name="" value="1"/>
		<%/if%>
	</div>

        <?php  if($goods['protocolshow']==1) { ?>
        <div class="fui-cell-group tb-line">
                <div class="fui-cell small ">
                    <div class="fui-cell-info">
                        <label class="checkbox-inline">
                            <input type="checkbox" class="fui-checkbox-primary open_withdrawprotocol" id="agree"  /> 我已经阅读并了解<a id="btn-apply" style="color:#337ab7;">【购买协议】</a>。
                        </label>

                    </div>
                </div>

        </div>
        <style type="text/css">
			.verify-pop {
			    position: absolute;
			    width: 100%;
			    z-index: 1001;
			}
			.verify-pop {
			    position: fixed;
			     top: 0;
			    bottom: 0;
			    background-color: #333;
			}
        	.verify-pop .qrcode {
			    width: 250px;
			    margin-left: -125px;
			    border-radius: 0.5rem;
			    height: auto;
			    overflow: hidden;
			}
        	.qrcode .text{
        		width: 100%;
			    word-wrap: break-word;
			    font-size: 0.7rem;
			    color: #ef4f4f;
			    line-height: 1rem;
			    height: 15rem;
			    overflow-y: auto;
        	}
        	.qrcode .title {
        		text-align: center;
    		    font-size: 1rem;
    		    height: 1.5rem;
        	}
        </style>
        <div class="pop-apply-hidden" style="display: none;">
            <div class="verify-pop pop">
                <div class="close"><i class="icon icon-roundclose"></i></div>
                <div class="qrcode">
                    <div class="inner">
                        <div class="title">购买协议</div>
                        <div class="text"><?php  echo $goods['protocolcontent'];?></div>
                    </div>
                    <div class="inner-btn" style="padding: 0.5rem;">
                        <div class="btn btn-danger" style="width: 100%; margin: 0;">我已阅读</div>
                    </div>
                </div>
            </div>
        </div>
        <?php  } ?>           
	</div>
	 
	<div class="fui-navbar">
		<a href="javascript:;" class="nav-item btn cartbtn" style='display:none'>加入购物车<%action%></a>
		<a href="javascript:;" class="nav-item btn buybtn"  style='display:none' >立刻购买</a>
		<a href="javascript:;" class="nav-item btn confirmbtn"  style='display:none'>确定</a>
	    <!-- <a href="javascript:;" class="nav-item btn buybtn"  style='display:none' >立刻购买</a>
	    <a href="javascript:;" class="nav-item btn confirmbtn"  style='display:none'>确定</a> -->
	</div>
    </div>
    </div>
</script>
<!--efwww_com-->