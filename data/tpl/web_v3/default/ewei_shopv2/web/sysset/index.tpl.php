<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>

<div class="page-header">当前位置：<span class="text-primary">商城设置</span></div>

    <div class="page-content">
        <form action="" method="post" class="form-horizontal form-validate" enctype="multipart/form-data" >
            <div class="form-group">
                <label class="col-lg control-label">商城名称</label>
                <div class="col-sm-9 col-xs-12">
                    <?php if(cv('sysset.shop.edit')) { ?>
                    <input type="text" name="data[name]" class="form-control" value="<?php  echo $data['name'];?>" />
                    <?php  } else { ?>
                    <input type="hidden" name="data[name]" value="<?php  echo $data['name'];?>"/>
                    <div class='form-control-static'><?php  echo $data['name'];?></div>
                    <?php  } ?>

                </div>
            </div>
            <div class="form-group">
                <label class="col-lg control-label">商城LOGO</label>
                <div class="col-sm-9 col-xs-12">
                    <?php if(cv('sysset.shop.edit')) { ?>
                    <?php  echo tpl_form_field_image2('data[logo]', $data['logo'])?>
                    <span class='help-block'>正方型图片</span>
                    <?php  } else { ?>
                    <input type="hidden" name="data[logo]" value="<?php  echo $data['logo'];?>"/>
                    <?php  if(!empty($data['logo'])) { ?>
                    <a href='<?php  echo tomedia($data['logo'])?>' target='_blank'>
                    <img src="<?php  echo tomedia($data['logo'])?>" style='width:100px;border:1px solid #ccc;padding:1px' />
                    </a>
                    <?php  } ?>
                    <?php  } ?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-lg control-label">商城简介</label>
                <div class="col-sm-9 col-xs-12">
                    <?php if(cv('sysset.shop.edit')) { ?>
                    <textarea name="data[description]" class="form-control richtext"rows="5"><?php  echo $data['description'];?></textarea>
                    <?php  } else { ?>
                    <textarea name="data[description]" class="form-control richtext" rows="5" style="display:none"><?php  echo $data['description'];?></textarea>
                    <div class='form-control-static'><?php  echo $data['description'];?></div>
                    <?php  } ?>
                </div>
            </div>

            <div class="form-group">
                <label class="col-lg control-label">店招</label>
                <div class="col-sm-9 col-xs-12">
                    <?php if(cv('sysset.shop.edit')) { ?>
                    <?php  echo tpl_form_field_image2('data[img]', $data['img'])?>
                    <span class='help-block'>商城首页店招，建议尺寸640*450</span>
                    <?php  } else { ?>
                    <input type="hidden" name="data[img]" value="<?php  echo $data['img'];?>"/>
                    <?php  if(!empty($data['img'])) { ?>
                    <a href='<?php  echo tomedia($data['img'])?>' target='_blank'>
                    <img src="<?php  echo tomedia($data['img'])?>" style='width:200px;border:1px solid #ccc;padding:1px' />
                    </a>
                    <?php  } ?>
                    <?php  } ?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-lg control-label">商城海报</label>
                <div class="col-sm-9 col-xs-12">
                    <?php if(cv('sysset.shop.edit')) { ?>
                    <?php  echo tpl_form_field_image2('data[signimg]', $data['signimg'])?>
                    <span class='help-block'>推广海报，建议尺寸640*640</span>
                    <?php  } else { ?>
                    <input type="hidden" name="data[signimg]" value="<?php  echo $data['signimg'];?>"/>
                    <?php  if(!empty($data['signimg'])) { ?>
                    <a href='<?php  echo tomedia($data['signimg'])?>' target='_blank'>
                    <img src="<?php  echo tomedia($data['signimg'])?>" style='width:100px;border:1px solid #ccc;padding:1px' />
                    </a>
                    <?php  } ?>
                    <?php  } ?>

                </div>
            </div>
            <div class="form-group">
                <label class="col-lg control-label">开启登录</label>
                <div class="col-sm-9 col-xs-12">
                    <?php if(cv('sysset.shop.edit')) { ?>
                    <label class="radio-inline">
                        <input type="radio" name="data[isopenlogin]" value="0" <?php  if(empty($data['isopenlogin'])) { ?>checked=""<?php  } ?>>关闭
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="data[isopenlogin]" value="1" <?php  if($data['isopenlogin']=="1") { ?>checked=""<?php  } ?>> 开启
                    </label>
                    <?php  } else { ?>
                    <input type="hidden" name="data[name]" value="<?php  echo $data['name'];?>"/>
                    <div class='form-control-static'><?php  if($data['shopmode']=="show") { ?>展示版<?php  } else { ?>购物版<?php  } ?></div>
                    <?php  } ?>
                </div>
            </div>

			<div class="form-group">
                <label class="col-lg control-label">商城模式</label>
                <div class="col-sm-9 col-xs-12">
                    <?php if(cv('sysset.shop.edit')) { ?>
                    <label class="radio-inline">
                        <input type="radio" name="data[shopmode]" value="sale" <?php  if(empty($data['shopmode']) || $data['shopmode']=="sale") { ?>checked=""<?php  } ?>>购物版
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="data[shopmode]" value="show" <?php  if($data['shopmode']=="show") { ?>checked=""<?php  } ?>> 展示版
                    </label>
                    <?php  } else { ?>
                    <input type="hidden" name="data[name]" value="<?php  echo $data['name'];?>"/>
                    <div class='form-control-static'><?php  if($data['shopmode']=="show") { ?>展示版<?php  } else { ?>购物版<?php  } ?></div>
                    <?php  } ?>
                    <div class="help-block"> 启用展示版则无法购物</div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-lg control-label">支持快递</label>
                <div class="col-sm-9 col-xs-12">
                    <?php if(cv('sysset.shop.edit')) { ?>
                    <label class="radio-inline">
                        <input type="radio" name="data[isexpressopen]" value="1" <?php  if(empty($data['isexpressopen'])||$data['isexpressopen']==1) { ?> checked=""<?php  } ?>>开
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="data[isexpressopen]" value="2" <?php  if($data['isexpressopen']==2) { ?> checked=""<?php  } ?>>关
                    </label>
                    <?php  } else { ?>
                    <input type="hidden" name="data[name]" value="<?php  echo $data['name'];?>"/>
                    <div class='form-control-static'><?php  if($data['shopmode']=="show") { ?>展示版<?php  } else { ?>购物版<?php  } ?></div>
                    <?php  } ?>
                </div>
            </div>

            <div class="form-group">
                <label class="col-lg control-label">后台报单</label>
                <div class="col-sm-9 col-xs-12">
                    <?php if(cv('sysset.shop.edit')) { ?>
                    <label class="radio-inline">
                        <input type="radio" name="data[isdeclaration]" value="1" <?php  if($data['isdeclaration']==1) { ?> checked=""<?php  } ?>>开
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="data[isdeclaration]" value="0" <?php  if($data['isdeclaration']==0) { ?> checked=""<?php  } ?>>关
                    </label>
                    <?php  } ?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-lg control-label">报单充值余额</label>
                <div class="col-sm-9 col-xs-12">
                    <?php if(cv('sysset.shop.edit')) { ?>
                    <label class="radio-inline">
                        <input type="radio" name="data[isrecharge]" value="1" <?php  if($data['isrecharge']==1) { ?> checked=""<?php  } ?>>开
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="data[isrecharge]" value="0" <?php  if($data['isrecharge']==0) { ?> checked=""<?php  } ?>>关
                    </label>
                    <?php  } ?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-lg control-label">身份证填写</label>
                <div class="col-sm-9 col-xs-12">
                    <?php if(cv('sysset.shop.edit')) { ?>
                    <label class="radio-inline">
                        <input type="radio" name="data[isuserid]" value="1" <?php  if($data['isuserid']==1) { ?> checked=""<?php  } ?>>开
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="data[isuserid]" value="0" <?php  if($data['isuserid']==0) { ?> checked=""<?php  } ?>>关
                    </label>
                    <div class="help-block"> 如果开启此选项,小程序端收货地址和会员资料需要填写</div>
                    <?php  } ?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-lg control-label">签到购商品</label>
                <div class="col-sm-9 col-xs-12">
                    <?php if(cv('sysset.shop.edit')) { ?>
                    <label class="radio-inline">
                        <input type="radio" name="data[is_sign_goods]" value="1" <?php  if($data['is_sign_goods']==1) { ?> checked=""<?php  } ?>>开
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="data[is_sign_goods]" value="0" <?php  if($data['is_sign_goods']==0) { ?> checked=""<?php  } ?>>关
                    </label>
                    <?php  } ?>
                </div>
            </div>

            <div class="form-group">
                <label class="col-lg control-label">获取未关注者信息</label>
                <div class="col-sm-9 col-xs-12">
                    <?php if(cv('sysset.shop.edit')) { ?>
                    <label class="radio-inline">
                        <input type="radio" name="data[getinfo]" value="1" <?php  if(empty($data['getinfo']) || $data['getinfo']==1) { ?>checked=""<?php  } ?>> 开启
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="data[getinfo]" value="2" <?php  if($data['getinfo']==2) { ?>checked=""<?php  } ?>> 关闭
                    </label>
                    <?php  } else { ?>
                    <input type="hidden" name="data[name]" value="<?php  echo $data['name'];?>"/>
                    <div class='form-control-static'><?php  if($data['getinfo']==0) { ?>关闭<?php  } else { ?>开启<?php  } ?></div>
                    <?php  } ?>
                    <div class="help-block"> 如果开启此选项,则是会弹出绿色微信授权框</div>
                </div>
            </div>

            <div class="form-group">
                <label class="col-lg control-label">售罄图标</label>
                <div class="col-sm-9 col-xs-12">
                    <?php if(cv('sysset.shop.edit')) { ?>
                    <?php  echo tpl_form_field_image2('data[saleout]', $data['saleout'])?>
                    <span class='help-block'>商品售罄图标，建议尺寸80*80，空则不显示</span>
                    <?php  } else { ?>
                    <input type="hidden" name="data[saleout]" value="<?php  echo $data['saleout'];?>"/>
                    <?php  if(!empty($data['saleout'])) { ?>
                    <a href='<?php  echo tomedia($data['saleout'])?>' target='_blank'>
                    <img src="<?php  echo tomedia($data['saleout'])?>" style='width:200px;border:1px solid #ccc;padding:1px' />
                    </a>
                    <?php  } ?>
                    <?php  } ?>
                </div>
            </div>

            <div class="form-group">
                <label class="col-lg control-label">加载图标</label>
                <div class="col-sm-9 col-xs-12">
                    <?php if(cv('sysset.shop.edit')) { ?>
                    <?php  echo tpl_form_field_image2('data[loading]', $data['loading'])?>
                    <span class='help-block'>商品列表图片加载图标，建议尺寸100*100(根据实际需求调整)，空则不显示</span>
                    <?php  } else { ?>
                    <input type="hidden" name="data[loading]" value="<?php  echo $data['loading'];?>"/>
                    <?php  if(!empty($data['loading'])) { ?>
                    <a href=""<?php  echo tomedia($data['loading'])?>" target='_blank'>
                    <img src="<?php  echo tomedia($data['loading'])?>" style='width:200px;border:1px solid #ccc;padding:1px' />
                    </a>
                    <?php  } ?>
                    <?php  } ?>
                </div>
            </div>

            <div class="form-group">
                <label class="col-lg control-label">全局统计代码</label>
                <div class="col-sm-9 col-xs-12">
                    <?php if(cv('sysset.shop.edit')) { ?>
                    <textarea name="data[diycode]" class="form-control richtext"  rows="5"><?php  echo $data['diycode'];?></textarea>
                    <?php  } else { ?>
                    <textarea name="data[diycode]" class="form-control richtext" style="display:none"  rows="5"><?php  echo $data['diycode'];?></textarea>
                    <div class='form-control-static'><?php  echo $data['diycode'];?></div>
                    <?php  } ?>
                </div>
            </div>

            <div class="form-group">
                <label class="col-lg control-label">开启导航条</label>
                <div class="col-sm-9 col-xs-12">
                    <?php if(cv('sysset.shop.edit')) { ?>
                    <label class="radio-inline"><input type="radio" name="data[funbar]" value="1" <?php  if(!empty($data['funbar'])) { ?>checked=""<?php  } ?>> 开启</label>
                    <label class="radio-inline"><input type="radio" name="data[funbar]" value="0" <?php  if(empty($data['funbar'])) { ?>checked=""<?php  } ?>> 关闭</label>
                    <?php  } else { ?>
                    <input type="hidden" name="data[name]" value="<?php  echo $data['name'];?>"/>
                    <div class='form-control-static'><?php  if(empty($data['funbar'])) { ?>关闭<?php  } else { ?>开启<?php  } ?></div>
                    <?php  } ?>
                    <div class="help-block"> 如果开启此选项，导航内容请到导航条中编辑</div>
                </div>
            </div>

            <div class="form-group">
                <label class="col-lg control-label">商品列表库存预警</label>
                <div class="col-sm-9 col-xs-12">
                    <input class="form-control" name="data[goodstotal]" value="<?php  echo intval($data['goodstotal'])?>"/>
                    <span class="help-block">当后台商品列表中商品库存小于此值时特殊标记(值为零时不提示)</span>
                </div>
            </div>

            <div class="form-group">
                 <label class="col-lg control-label">关于我们</label>
                <div class="col-sm-9 col-xs-12">
                    <div class="">
                       
                        <?php  echo tpl_ueditor('data[aboutus]',$data['aboutus'],array('height'=>'300'))?>
                       
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">数据分析</label>
                <div class="col-sm-9 col-xs-12">
                    <?php if(cv('sysset.shop.edit')) { ?>
                        <input type="text" name="data[diycount]" class="form-control" value="<?php  echo $data['diycount'];?>" />
                    <?php  } else { ?>
                        <input type="hidden" name="data[name]" value="<?php  echo $data['diycount'];?>"/>
                        <div class='form-control-static'><?php  echo $data['diycount'];?></div>
                    <?php  } ?>

                </div>
            </div>
            <div class="form-group">
                <label class="col-lg control-label">企业微信配置</label>
                <div class="col-sm-9 col-xs-12">
                    <label class="radio-inline"><input type="radio" class="open_wxwork" name="data[open_wxwork]" value="1" <?php  if(!empty($data['open_wxwork'])) { ?>checked=""<?php  } ?>> 开启</label>
                    <label class="radio-inline"><input type="radio" class="open_wxwork" name="data[open_wxwork]" value="0" <?php  if(empty($data['open_wxwork'])) { ?>checked=""<?php  } ?>> 关闭</label>
                    <div class="help-block"> 如果开启此选项，需要配置企业微信信息</div>
                    <div id="wx_work" style="display:<?php  if(empty($data['open_wxwork'])) { ?>none<?php  } else { ?>block<?php  } ?>;">
                        <label style="width:100%;">企业ID：<input class="form-control" type="text" name="data[corp_id]" value="<?php  echo $data['corp_id'];?>"></label><br>
                        <label style="width:100%;">通讯录Secret：<input class="form-control" type="text" name="data[corp_tsecret]" value="<?php  echo $data['corp_tsecret'];?>"></label><br>
                        <label style="width:100%;">应用AgentID：<input class="form-control" type="text" name="data[corp_agentid]" value="<?php  echo $data['corp_agentid'];?>"></label><br>
                        <label style="width:100%;">应用Secret：<input class="form-control" type="text" name="data[corp_secret]" value="<?php  echo $data['corp_secret'];?>"></label><br>
                    </div>
                </div>
                </div>
            </div>


            <div class="form-group">
                <label class="col-lg control-label"></label>
                <div class="col-sm-9 col-xs-12">
                    <?php if(cv('sysset.shop.edit')) { ?>
                    <input type="submit" value="提交" class="btn btn-primary"  />
                    <?php  } ?>
                </div>
            </div>
        </form>
    </div>
 <script type="text/javascript">
    $(".open_wxwork").click(function(){
        var wxval = $(this).val();
        if (wxval == 1) {
            $("#wx_work").css('display','block');
        } else {
            $("#wx_work").css('display','none');
        }
    })
</script>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>
<!--efwww_com54mI5p2D5omA5pyJ-->