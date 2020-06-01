<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>
<style>
    .fui-cell-group:not(.fui-cell-group-o):before{
        border:0
    }
    .fui-cell-group:first-child{
        margin-top: 0;
    }
     .save-info{
        background: -webkit-gradient(linear, 0% 75%, 75% 100%, from(#2caeb2), to(#3fbaa0)) !important;
    }
    .save-info:active,
	.save-info:link,
	.save-info:focus,
	.save-info:hover {
		color: #fff!important;
	}
</style>
<div class='fui-page  fui-page-current fui-page-address-post'>
    <div class="fui-header">
        <div class="fui-header-left">
            <a class="back"></a>
        </div>
        <div class="title"><?php  if(empty($address)) { ?>添加地址<?php  } else { ?>编辑地址<?php  } ?></div>
        <div class="fui-header-right"></div>
    </div>
    <div class='fui-content'>
        <?php  if(is_weixin() && $_W['shopset']['trade']['shareaddress']!=0) { ?>
        <!--div class="fui-cell-group order-info noborder">
            <a class="fui-cell" id="btn-address">
                <div class="fui-cell-text">
                    从微信选择地址
                </div>
                <div class="fui-cell-remark text-danger">
                </div>
            </a>


        </div-->
        <?php  } ?>
        <div class="bg-gray">
        <form method='post' class='form-ajax' >
            <input type='hidden' id='addressid' value="<?php  echo $address['id'];?>"/>
            <div class='fui-cell-group' style="margin-top: 0;">
                <div class='fui-cell'>

                    <!-- <div class='fui-cell-label'>收货人</div> -->
                    <div class='fui-cell-info '><input type="text" id='realname'  name='realname' value="<?php  echo $address['realname'];?>" placeholder="收货人" class="fui-input"/></div>
                </div>
                <div class='fui-cell'>
                    <!-- <div class='fui-cell-label'>联系电话</div> -->

                    <div class='fui-cell-info '><input type="tel" id='mobile' name='mobile' value="<?php  echo $address['mobile'];?>" placeholder="手机号"  class="fui-input"/></div>
                </div>

                <div class='fui-cell'>
                    <!-- <div class='fui-cell-label'>所在地区</div> -->
                    <div class='fui-cell-info '><input type="text" id='areas'  name='areas' data-value="<?php  if(!empty($show_data) && !empty($address)) { ?><?php  echo $address['datavalue'];?><?php  } ?>" value="<?php  if(!empty($show_data) && !empty($address)) { ?><?php  echo $address['province'];?> <?php  echo $address['city'];?> <?php  echo $address['area'];?><?php  } ?>" placeholder="选择省/市/区"  class="fui-input" readonly=""/></div>
                </div>

                <?php  if(!empty($new_area) && !empty($address_street)) { ?>
                <div class='fui-cell'>
                    <div class='fui-cell-label'>所在街道</div>
                    <div class='fui-cell-info'><input type="text" id='street'  name='street' data-value="<?php  if(!empty($address)) { ?><?php  echo $address['streetdatavalue'];?><?php  } ?>" value="<?php  if(!empty($address)) { ?><?php  echo $address['street'];?><?php  } ?>" placeholder="所在街道"  class="fui-input" readonly=""/></div>
                </div>
                <?php  } ?>

                <div class='fui-cell'>
                    <!-- <div class='fui-cell-label'>详细地址</div> -->
                    <div class='fui-cell-info '><input type="text" id='address' name='address' value="<?php  echo $address['address'];?>" placeholder='详细地址：如道路/门牌号/小区/单元室等'  class="fui-input"/></div>
                </div>


            
            </div>

            <div class="fui-cell-group">
                    <?php  if(empty($address['isdefault'])) { ?>
                    <div class="fui-cell ">
                        <div class="fui-cell-label" style="width:auto">设置默认地址</div>
                        <div class="fui-cell-info ">
                            <input type="checkbox" id='isdefault' class="fui-switch fui-switch-danger pull-right">
                        </div>
                    </div>

                <?php  } ?>
            </div>



        </form>
    </div>
    </div>
    <div class="btn-fixed">
           <?php  if(is_weixin() && ($_W['shopset']['trade']['shareaddress']||!empty($_W['shopset']['trade']['shareaddress'])) ) { ?>
           
        
        <a id="btn-address" class='btn btn-danger-o block'>一键获取微信位置</a>
        <?php  } ?> 
        <a id="btn-submit" class='external btn btn-danger block save-info'>保存</a>
        <?php  if(!empty(address)) { ?>
        <a class='btn btn-default-o block' data-toggle="delete" data-addressid="<?php  echo $address['id'];?>">删除</a>
        <?php  } ?>

    </div>
    <script language='javascript' type="text/javascript">

        require(['biz/member/address'], function (modal) {
            modal.initPost({new_area: <?php  echo $new_area?>, address_street: <?php  echo $address_street?>});
        });
    </script>
    <script language='javascript'>require(['biz/member/address'], function (modal) {
        modal.initList();
    });</script>
</div>

<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>
<!--efwww_com54mI5p2D5omA5pyJ-->