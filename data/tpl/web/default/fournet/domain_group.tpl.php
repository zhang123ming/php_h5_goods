<?php defined('IN_IA') or exit('Access Denied');?><?php  if($do == 'post') { ?>
<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<style type='text/css'>
    .topmenu { position: relative; width:100%;float:left;background:#efefef;}
    .topmenu ul li a { color:#000}
    .topmenu .dropdown-menu li a { color:#666}
    .topmenu ul li { content:'|'}
</style>
<nav class="navbar navbar-default">
    <div class="container-fluid topmenu">

        <ul class="nav navbar-nav"> 
            <li <?php  if($_GPC['do']=='manage') { ?>class="active"<?php  } ?>><a href="<?php  echo url('fournet/domain/manage')?>"><i class='fa fa-sitemap'></i>域名绑定管理</a></li>
            <?php  if($_GPC['do']=='post') { ?>   <li class="active"><a href="<?php  echo url('fournet/domain/post')?>"><i class='fa fa-plus'></i>增加域名绑定</a></li><?php  } ?>
           <?php  if($_W['isfounder']) { ?><li <?php  if($_GPC['do']=='group') { ?>class="active"<?php  } ?>><a href="<?php  echo url('fournet/domain/group')?>"><i class='fa fa-comments-o'></i>公众号限制</a></li>
             <li <?php  if($_GPC['do']=='help') { ?>class="active"<?php  } ?>><a href="<?php  echo url('fournet/domain/help')?>"><i class='fa fa-question'></i>使用说明</a></li><?php  } ?>
        </ul>
    </div>
</nav>
<div class="main">
    <form class="form-horizontal form" action="" method="post">
        <div class="panel panel-default">
            <div class="panel-heading">
                公众号/分组功能限定
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">套餐分组/公众号</label>
                    <div class="col-sm-9 col-xs-12">
                        <div class="input-group">
                            <span class="input-group-addon"> 
                            <input type="radio" <?php  if($item['id']) { ?>disabled="disabled"<?php  } ?> name="isaccount" <?php  if(empty($item['isaccount'])) { ?>checked<?php  } ?> value="0"/>套餐分组
                            <input id="isaccount" type="radio" <?php  if($item['id']) { ?>disabled="disabled"<?php  } ?> name="isaccount" <?php  if($item['isaccount']==1) { ?>checked<?php  } ?> value="1"/>公众号
                            </span>
                            <input type='hidden' name="groupid" id="groupid" value="<?php  echo $item['groupid'];?>"/>
                            <input type='text' readonly="readonly"  name='title' id="title" value="<?php  echo $item['title'];?>" class='form-control'/>
                            <?php  if(empty($item['id'])) { ?><div class="input-group-btn">
                                <a class="btn btn-primary" id="select"><i class="fa fa-external-link"></i>选择</a>
                            </div><?php  } ?>
                        </div>
                        <span class='help-block'>选择公众号或公众号套餐分组; 套餐分组为<a href="./index.php?c=account&a=groups&">服务套餐列表</a></span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">域名数量</label>
                    <div class="col-sm-9 col-xs-12">
                        <div class="input-group">
                           <span class="input-group-addon">最多 </span>
                                <input type='number' min="0" value="<?php  echo $item['limit'];?>" name="limit"  class='form-control' />
                            <span class="input-group-addon">个 </span>
                        </div>
                        <span class='help-block'>可绑定域名数量限制,0:无限制</span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">域名类型</label>
                    <div class="col-sm-9 col-xs-12">
                        <div class="input-group">
                          
                            <label class="checkbox-inline">
                                <input type='checkbox' value="1" name="domain1" <?php  if(($item['domain']&1)==1) { ?>checked="checked"<?php  } ?> />独立域名
                            </label>
                            <label class="checkbox-inline">
                                <input type='checkbox' value="2" name="domain2" <?php  if(($item['domain']&2)==2 ||empty($item['domain'])) { ?>checked="checked"<?php  } ?>/>子域名
                            </label>
                        </div>
                        <span class='help-block'>独立域名,子域名必须有一个选择,为空时默认子域名</span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">可绑定对象</label>
                    <div class="col-sm-9 col-xs-12">
                        <div class="input-group">
                            <label class="checkbox-inline">
                                <input type='checkbox' value="1" name="type1" <?php  if(($item['type']&1)==1) { ?>checked="checked"<?php  } ?> />公众号
                            </label>
                            <label class="checkbox-inline">
                                <input type='checkbox' value="2" name="type2" <?php  if(($item['type']&2)==2||empty($item['type'])) { ?>checked="checked"<?php  } ?>/>模块
                            </label>
                        </div>
                        <span class='help-block'>公众号,模块必须有一个选择,为空时默认模块</span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">可操作角色</label>
                    <div class="col-sm-9 col-xs-12">
                        <div class="input-group">
                            <label class="checkbox-inline">
                                <input type='checkbox' value="1" name="right1" <?php  if(($item['right']&1)==1) { ?>checked="checked"<?php  } ?>/>管理员
                            </label>
                            <label class="checkbox-inline">
                                <input type='checkbox' value="2" name="right2" <?php  if(($item['right']&2)==2) { ?>checked="checked"<?php  } ?> />操作员
                            </label>
                        </div>
                        <span class='help-block'>可以绑定域名的角色;没权限时,只能查看列表;全不选,只有后台Founder才权操作</span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                    <div class="col-sm-9 col-xs-12">
                        <input type="submit" name="submit" value="提交" class="btn btn-primary"/>
                        <input type="hidden" name="token" value="<?php  echo $_W['token'];?>"/>
                        <input type="button" name="back" onclick='history.back()' style='margin-left:10px;' value="取消返回列表" class="btn btn-default" />
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<div id="modal-select"  class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header"><button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button><h3>选择公众号</h3></div>
            <div class="modal-body" >
                <div class="row">
                    <div class="input-group">
                        <input type="text" class="form-control" name="keyword" value="" id="search-kwd-select" placeholder="请输入关键字" />
                        <span class='input-group-btn'><button type="button" class="btn btn-default" onclick="search_select();">搜索</button></span>
                    </div>
                </div>
                <div id="module-menus-select" style="padding-top:5px;"></div>
            </div>
            <div class="modal-footer"><a href="#" class="btn btn-default" data-dismiss="modal" aria-hidden="true">关闭</a></div>
        </div>
    </div>
</div>
<script type="text/javascript">
    <!--
    function search_select() {
 
        $("#module-menus-select").html("正在搜索....");
        var url="<?php  echo url('fournet/domain/group',array('op'=>'query'))?>";
        var type=0;
        if($('#isaccount').is(':checked')){
            type=1;
        }
        $.get(url, {type:type,keyword: $.trim($('#search-kwd-select').val())}, function(dat){
            $('#module-menus-select').html(dat);
        });
    }
    function select_item(o) {
        $("#groupid").val(o.id);
        $("#title").val( o.title);
        $("#modal-select .close").click();
    }

    require(['bootstrap', 'util'],function($, u) {
        
        $('#select').click(function() {
            $('#modal-select').modal();
 
        });
        $('form').submit(function(){
            var  title=$('#title').val().trim();
            
            if(title==''){
                $('#title').focus();
                u.message('请选择套餐分组/公众号!');
                return false;
            }
            return true;
        });
       
    });


    //-->
       
 
</script>
<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>
<?php  } else if($do == 'query') { ?>
<div style='max-height:500px;overflow:auto;'>
<table class="table table-hover">
    <tbody>   
        <?php  if(is_array($ds)) { foreach($ds as $row) { ?>
        <tr>
            <td><?php  echo $row['title'];?></td>
            <td style="width:80px;"><a href="javascript:;" onclick='select_item(<?php  echo json_encode($row);?>)'>选择</a></td>
        </tr>
        <?php  } } ?>
        <?php  if(count($ds)<=0) { ?>
        <tr> 
            <td colspan='4' align='center'>未找到数据</td>
        </tr>
        <?php  } ?>
    </tbody>
</table>
</div>
<?php  } else { ?>
<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<style type='text/css'>
    .topmenu { position: relative; width:100%;float:left;background:#efefef;}
    .topmenu ul li a { color:#000}
    .topmenu .dropdown-menu li a { color:#666}
    .topmenu ul li { content:'|'}
</style>
<nav class="navbar navbar-default">
    <div class="container-fluid topmenu">

        <ul class="nav navbar-nav"> 
            <li <?php  if($_GPC['do']=='manage') { ?>class="active"<?php  } ?>><a href="<?php  echo url('fournet/domain/manage')?>"><i class='fa fa-sitemap'></i>域名绑定管理</a></li>
            <?php  if($_GPC['do']=='post') { ?>   <li class="active"><a href="<?php  echo url('fournet/domain/post')?>"><i class='fa fa-plus'></i>增加域名绑定</a></li><?php  } ?>
           <?php  if($_W['isfounder']) { ?><li <?php  if($_GPC['do']=='group') { ?>class="active"<?php  } ?>><a href="<?php  echo url('fournet/domain/group')?>"><i class='fa fa-comments-o'></i>公众号限制</a></li>
             <li <?php  if($_GPC['do']=='help') { ?>class="active"<?php  } ?>><a href="<?php  echo url('fournet/domain/help')?>"><i class='fa fa-question'></i>使用说明</a></li><?php  } ?>
        </ul>
    </div>
</nav>
<div class="panel panel-info">
    <div class="panel-heading">公众号限制列表(<?php  echo $total;?>条数据)  </div>
    <table class="table table-hover">
        <thead class="navbar-inner">
        <tr>
            <th width="220px">套餐分组/公众号</th>
            <th width="90px">域名数量</th>
            <th>域名类型</th>
            <th>可绑定对象</th>
            <th>可操作角色</th>
            <th width="130px">操作</th>
        </tr>
        </thead>
        <tbody>
        <?php  if(is_array($list)) { foreach($list as $item) { ?>
        <tr>
            <td><?php  if(empty($item['groupid'])) { ?><span style="color: red;">所有公众号(默认配置)</span><?php  } else { ?><?php  echo $item['title'];?> [<?php echo $item['isaccount']?'公众号':'套餐'?>]<?php  } ?></td>
            <td><?php echo empty($item['limit'])?'无限制':$item['limit']?></td>
            <td><?php  if(($item['domain']&1)==1) { ?>独立域名 <?php  } ?><?php  if(($item['domain']>1)||empty($item['domain'])) { ?>子域名<?php  } ?></td>
            <td><?php  if(($item['type']&1)==1) { ?>公众号 <?php  } ?><?php  if(($item['type']>1)) { ?>模块<?php  } ?></td>
            <td><?php  if(($item['right']&1)==1) { ?>管理员 <?php  } ?><?php  if(($item['right']&2)==2) { ?>操作员<?php  } ?><?php  if(empty($item['right'])) { ?>无<?php  } ?></td>
            <td>
                <a href="<?php  echo url('fournet/domain/group',array('op'=>'post','id' => $item['id']))?>" title="编辑"
                   class=""btn  btn-default"><i class="fa fa-edit"></i>编辑</a>&nbsp;
                <?php  if(!empty($item['groupid'])) { ?>  <a onclick="return confirm('确认要删除此条数据吗？'); return false;"
                   href="<?php  echo url('fournet/domain/group',array('op' => 'delete', 'id' => $item['id']))?>"
                   title="删除" class=""btn btn-default"><i class="fa fa-remove"></i>删除</a><?php  } ?>
            </td>
        </tr>
        <?php  } } ?>
        <tr>
            <td colspan='6'>
                [限制选取优先关系: 指定公众号 > 指定套餐 > 默认配置]&nbsp; <a class='btn btn-default' href="<?php  echo url('fournet/domain/group',array('op'=>'post'))?>"><i class='fa fa-plus'></i>增加限制</a>
            </td>
        </tr>
        </tbody>
    </table>
    <div class="panel-footer" style="padding-bottom: 1px;">
        <?php  echo $pager;?>
    </div>
</div>
<script type="text/javascript">
    <!--
    require(['bootstrap', 'util'],function($, u) {
        $('.query').click(function() {
            var item=$(this);
            $('#query').val(item.attr('data-id'));
            $('#queryname').html(item.text()+'<span class="caret"></span>');
        });
    });
    //-->
</script>
<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>
<?php  } ?>
