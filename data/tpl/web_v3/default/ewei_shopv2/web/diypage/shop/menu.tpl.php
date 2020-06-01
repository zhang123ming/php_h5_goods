<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>

<div class="page-header">当前位置：<span class="text-primary">自定义菜单设置</span></div>

<div class="page-content">

    <form action="" <?php if(cv('diypage.shop.menu.save')) { ?>method="post"<?php  } ?> class="form-horizontal form-validate">

        <div class="alert alert-warning">注意：商城页面如果是diy页面，自定义菜单请至diy页面编辑中设置。</div>

        <div class="form-group">
            
            <label class="col-lg control-label">商城菜单</label>
            <div class="col-sm-9 col-xs-12">
                        <div class="col-sm-6 col-xs-6" style="padding:0;">
                            <div class="input-group">
                                <div class="input-group-addon">微信端</div>
                                <select class="form-control valid select2"<?php if(cv('diypage.shop.menu.save')) { ?>name="menu[shop]"<?php  } else { ?>readonly<?php  } ?>>
                                <option value="">系统默认</option>
                                <?php  if(is_array($list)) { foreach($list as $item) { ?>
                                <option value="<?php  echo $item['id'];?>" <?php  if($data['menu']['shop']==$item['id']) { ?>selected="selected"<?php  } ?>><?php  echo $item['name'];?></option>
                                <?php  } } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xs-6" style="padding:0;">
                            <div class="input-group">
                                <div class="input-group-addon" style="border-left: 0; border-right: 0">WAP端</div>
                                <select class="form-control valid select2"<?php if(cv('diypage.shop.menu.save')) { ?>name="menu[shop_wap]"<?php  } else { ?>readonly<?php  } ?>>
                                <option value="">系统默认</option>
                                <?php  if(is_array($list)) { foreach($list as $item) { ?>
                                <option value="<?php  echo $item['id'];?>" <?php  if($data['menu']['shop_wap']==$item['id']) { ?>selected="selected"<?php  } ?>><?php  echo $item['name'];?></option>
                                <?php  } } ?>
                                </select>
                            </div>
                        </div>
            </div>
        </div>

        <?php  if($_W['plugin']!='merch' && !isset($_W['merch'])) { ?>

            <?php  if(is_array($pluginList)) { foreach($pluginList as $plugin => $status) { ?>
                <?php  if(!empty($status) && p($plugin)) { ?>
                    <div class="form-group">
                        <label class="col-lg control-label"><?php  echo m('plugin')->getName($plugin)?></label>
                        <div class="col-sm-9 col-xs-12">
                            <div class="input-group">
                                <div class="input-group-addon">微信端</div>
                                <select class="form-control valid select2"<?php if(cv('diypage.shop.menu.save')) { ?>name="menu[<?php  echo $plugin;?>]"<?php  } else { ?>readonly<?php  } ?>>
                                    <option value="">系统默认</option>
                                    <?php  if(is_array($list)) { foreach($list as $item) { ?>
                                        <option value="<?php  echo $item['id'];?>" <?php  if($data['menu'][$plugin]==$item['id']) { ?>selected="selected"<?php  } ?>><?php  echo $item['name'];?></option>
                                    <?php  } } ?>
                                </select>
                                <div class="input-group-addon" style="border-left: 0; border-right: 0">WAP端</div>
                                <select class="form-control valid select2"<?php if(cv('diypage.shop.menu.save')) { ?>name="menu[<?php  echo $plugin;?>_wap]"<?php  } else { ?>readonly<?php  } ?>>
                                    <option value="">系统默认</option>
                                    <?php  if(is_array($list)) { foreach($list as $item) { ?>
                                        <option value="<?php  echo $item['id'];?>" <?php  if($data['menu'][$plugin.'_wap']==$item['id']) { ?>selected="selected"<?php  } ?>><?php  echo $item['name'];?></option>
                                    <?php  } } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                <?php  } ?>
            <?php  } } ?>

        <?php  } ?>

        <?php if(cv('diypage.shop.menu.save')) { ?>
            <div class="form-group">
                <label class="col-lg control-label"></label>
                <div class="col-sm-9 col-xs-12">
                    <input type="submit" value="提交" class="btn btn-primary">
                </div>
            </div>
        <?php  } ?>

    </form>

</div>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>
<!--yifuyuanma-->