<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>
<style>
    .multi-item {margin-bottom: 18px;}
    .input-group-addon{width: 100%;}
    .fixsingle-input-group {width: 330px;}
</style>
<div class="page-header">
    当前位置：<span class="text-primary"><?php  if(!empty($item)) { ?>编辑<?php  } else { ?>新建<?php  } ?>商品组 <small><?php  if(!empty($item)) { ?>(名称: <?php  echo $item['name'];?>)<?php  } ?></small></span>
</div>

<div class="page-content">
    <form <?php if( ce('goods.group' ,$item) ) { ?>action="" method="post"<?php  } ?> class="form-validate form-horizontal ">

        <div class="form-group">
            <label class="col-lg control-label">商品组名称</label>
            <div class="col-sm-9">
                <?php if( ce('goods.group' ,$item) ) { ?>
                    <input type="text" class="form-control valid" name="name" value="<?php  echo $item['name'];?>" data-rule-required="true" placeholder="请输入商品组名称" />
                <?php  } else { ?>
                    <div class='form-control-static'><?php  echo $item['name'];?></div>
                <?php  } ?>
            </div>
        </div>

        <div class="form-group">
            <label class="col-lg control-label">商品组状态</label>
            <div class="col-sm-9 col-xs-12">
                <?php if( ce('goods.group' ,$item) ) { ?>
                    <label class="radio-inline">
                        <input type="radio" name="enabled" value="1" <?php  if($item['enabled']==1) { ?>checked="checked"<?php  } ?>> 启用
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="enabled" value="0" <?php  if($item['enabled']==0 || empty($item)) { ?>checked="checked"<?php  } ?>> 禁用
                    </label>
                <?php  } else { ?>
                    <div class='form-control-static'><?php  if($item['enabled']==1) { ?>启用<?php  } else { ?>禁用<?php  } ?></div>
                <?php  } ?>
            </div>
        </div>

        <div class="form-group">
            <label class="col-lg control-label">限制购买最大次数</label>
            <div class="col-sm-9 col-xs-12">
                <?php if( ce('goods.group' ,$item) ) { ?>
                    <div>
                    <label class="radio-inline">
                        <input type="radio" name="isbuylimit" id="enable" value="1" <?php  if(!empty($item['isbuylimit'])) { ?>checked="checked"<?php  } ?>> 启用
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="isbuylimit" id="disable" value="0" <?php  if(empty($item['isbuylimit'])) { ?>checked="checked"<?php  } ?>> 禁用
                    </label>
                    <span class="help-block">如果启用时月份为 0,则不限制按月购买次数</span>
                    </div>
                    
                    <div class="col-sm-10 col-xs-12" id="memberbuy" <?php  if(empty($item['isbuylimit'])) { ?>style="display: none"<?php  } ?>>
                        <?php  if(is_array($levels)) { foreach($levels as $item) { ?>
                        <div class="input-group fixsingle-input-group" style="margin-top: 10px">
                            <span class="input-group-addon"><?php  echo $item['levelname'];?></span>
                            <input type="text" name="buylimit[<?php  echo $item['id'];?>][count]" value="<?php  echo $buylimit[$item['id']]['count'];?>" style="width: 80px" class="form-control">
                            <span class="input-group-addon">次</span>
                            <input type="text" name="buylimit[<?php  echo $item['id'];?>][month]" value="<?php  echo $buylimit[$item['id']]['count'];?>" style="width: 80px" class="form-control">
                            <span class="input-group-addon">月</span>
                        </div>
                        <?php  } } ?>
                    </div>
                    
                <?php  } else { ?>
                    <div class='form-control-static'><?php  if($item['enabled']==1) { ?>启用<?php  } else { ?>禁用<?php  } ?></div>
                <?php  } ?>
            </div>
        </div>

        <div class="form-group">
            <label class="col-lg control-label">组内商品</label>
            <div class="col-sm-9">
                <div class="form-group" style="height: auto; display: block;">
                    <div class="col-sm-12 col-xs-12">
                        <?php if( ce('goods.group' ,$item) ) { ?>
                            <?php  echo tpl_selector('goodsids',array('preview'=>true,'readonly'=>true, 'required'=>'true', 'multi'=>1,'url'=>webUrl('goods/query'),'items'=>$goods,'buttontext'=>'选择商品','placeholder'=>'请选择商品'))?>
                        <?php  } else { ?>
                            <div class="input-group multi-img-details container ui-sortable">
                                <?php  if(is_array($goods)) { foreach($goods as $item) { ?>
                                <div data-name="goodsid" data-id="426" class="multi-item">
                                    <img src="<?php  echo tomedia($item['thumb'])?>" class="img-responsive img-thumbnail">
                                    <div class="img-nickname"><?php  echo $item['title'];?></div>
                                </div>
                                <?php  } } ?>
                            </div>
                        <?php  } ?>
                    </div>
                </div>

            </div>
        </div>

        <div class="form-group">
            <label class="col-lg control-label"></label>
            <div class="col-sm-9">
                <?php if( ce('goods.group' ,$item) ) { ?>
                <input type="submit" class="btn btn-primary" value="保存">
                <?php  } ?>
                <a class="btn btn-default" href="<?php  echo webUrl('goods/group')?>">返回列表</a>
            </div>
        </div>

    </form>
</div>

<script type="text/html" id="tpl_member_buy">
    <div class="input-group fixsingle-input-group" style="margin-top: 10px">
        <span class="input-group-addon">{levelname}</span>
        <input type="text" name="buylimit[{id}][count]" value="" style="width: 80px" class="form-control">
        <span class="input-group-addon">次</span>
        <input type="text" name="buylimit[{id}][mount]" value="" style="width: 80px" class="form-control">
        <span class="input-group-addon">月</span>
    </div>
</script>

<?php if( ce('goods.group' ,$item) ) { ?>
    <script language="javascript">
        require(['jquery.ui'],function(){
            $('.multi-img-details').sortable();
        })
    </script>
<?php  } ?>

<script type="text/javascript">
$('#enable').click(function(){
    $('#memberbuy').show();
});
$('#disable').click(function(){
    $('#memberbuy').hide();
});
</script>

<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>
<!--www-efwww-com-->