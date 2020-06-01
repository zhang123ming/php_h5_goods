<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
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
<?php  if($do == 'post') { ?>
<div class="main">
    <form class="form-horizontal form" action="" method="post">
        <div class="panel panel-default">
            <div class="panel-heading">
                模块入口域名绑定
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">绑定域名</label>
                    <div class="col-sm-9 col-xs-12">
                        <div class="input-group">
                            <input type='text' autocomplete="off" autofocus="true" name='set[domain]' id="domain" value="<?php  echo $item['domain'];?>" class='form-control'/>
                            <span class="input-group-addon">类型:</span>
                            <span class="input-group-addon"> 
                            <?php  if(($limit['domain']&1)==1) { ?><input type="radio" id="type0" name="set[type]" <?php  if(empty($item['type'])) { ?>checked<?php  } ?> value="0"/>独立域名<?php  } ?>
                            <?php  if(($limit['domain']&2)==2||empty($limit['domain']) ) { ?><input type="radio" id="type1" name="set[type]" <?php  if($item['type']==1) { ?>checked<?php  } ?> value="1"/>子域名<?php  } ?>
                            </span>
                        </div>
                        <span class='help-block'>子域名:为系统域名[<?php  echo $host;?>]的下级域名[如:test.<?php  echo $host;?>],只须输入子域名名称[test];<br/>独立域名:完整域名,跟系统域名类似,须输入完整[如:wx.abc.com]</span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">入口类型</label>
                    <div class="col-sm-9 col-xs-12">
                        <div class="input-group">
                            <?php  if(($limit['type']&1)==1) { ?>
                            <label class="radio-inline">
                                <input type="radio" value="1" class="entry"  name="set[all]" id="isaccount1" <?php  if($item['all']) { ?> checked="checked"<?php  } ?>>
                               公众号
                            </label><?php  } ?>
                            <?php  if(($limit['type']&2)==2) { ?>
                            <label class="radio-inline">
                                <input type="radio" value="0" class="entry"   name="set[all]" id="isaccount0"  <?php  if(empty($item['all'])) { ?> checked="checked"<?php  } ?>>
                               模块
                            </label><?php  } ?>
                        </div>
                        <span class='help-block'>公众号:域名对应公众号,app访问url须手工替换域名或用绑定域名登录web后台获取对应的url;<br/>模块:域名对应模块入口</span>
                    </div>
                </div>
                <div class="form-group" id="o-module" style="<?php  if($item['all']) { ?>display:none<?php  } ?>">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                    <div class="col-sm-9 col-xs-12">
                        <div class="input-group">
                            <input type="hidden" id="title_val" name="set[title]" value="<?php  echo $item['title'];?>"/>
                            <span class="input-group-addon"  id="title"><?php echo empty($item['title'])?'入口名称':$item['title']?></span>
                            <input type='text' id="url"  name='set[entry]' value="<?php  echo $item['entry'];?>" class='form-control'/>
                            <div class="input-group-btn">
                                <a class="btn btn-primary" id="select"><i class="fa fa-external-link"></i> 系统链接</a>
                            </div>
                        </div>
                        <span class='help-block'>请选择要绑定的模块功能入口或粘贴输入对应的模块入口url;<br/>**注意:找不到模块的入口,可以手工输入,请确认链接正确,输入须使用相对链接./index.php开关 **
                            
                        </span>
                    </div>
                </div>
         
                <div class="form-group" id="o-account" style="<?php  if(empty($item['all'])) { ?>display:none<?php  } ?>" >
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                    <div class="col-sm-9 col-xs-12">
                        <div class="input-group">
                             <span class="input-group-addon"> 
                                <input type='checkbox' value="1" id="redirect" <?php  if(!empty($item['redirect'])) { ?>checked="checked"<?php  } ?> name="set[redirect]">允许域名跳转到</span>
                                <input type='text' id="domains" name='ext[domains]' placeholder="跳转域名应为全域名已绑定且无跳转;多个域名 | 分隔,随机跳转"
                                   value="<?php echo empty($ext['domains'])?'':$ext['domains']?>" class='form-control'/>
                        </div>
                        <span class='help-block'>允许域名跳转:指app访问有绑定域名的url时,会用指定域名替换绑定域名并跳转;</span>
                    </div>
                </div>
                <?php  if($_W['isfounder']) { ?>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">App限制</label>
                    <div class="col-sm-9 col-xs-12">
                        <div class="input-group">
                            <label class="radio-inline">
                                <input type='radio' value="0" name="all_account" <?php  if(!empty($item['uniacid'])) { ?>checked="checked"<?php  } ?>/>
                                只允许当前公众号"<?php  echo $_W['uniaccount']['name'];?>"访问
                            </label>
                            <label class="radio-inline">
                                <input type='radio' value="1" name="all_account" <?php  if(empty($item['uniacid'])) { ?>checked="checked"<?php  } ?>/>所有公众号都可访问
                            </label>
                        </div>
                        <span class='help-block'>绑定域名只允许当前公众号可用时,非当前公众号app端url使用绑定域名将禁止访问,提示:公众号禁止访问 !;</span>
                    </div>
                </div>
       
       
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">Web后台限制</label>
                    <div class="col-sm-9 col-xs-12">
                        <div class="input-group">
                            <label class="radio-inline">
                                <input type='radio' value="1" name="ext[login]" <?php  if(!empty($ext['login'])) { ?>checked="checked"<?php  } ?>/>允许当前绑定域名Web后台访问
                            </label>
                            <label class="radio-inline">
                                <input type='radio' value="0" name="ext[login]" <?php  if(empty($ext['login'])) { ?>checked="checked"<?php  } ?>/>禁止当前绑定域名Web后台访问
                            </label>
                        </div>
                        <span class='help-block'>是否允许当前绑定域名Web后台访问,在"非绑定域名禁止使用"模式下才有效;公众号回复要使用域名配置api接口,才有效</span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">操作限制</label>
                    <div class="col-sm-9 col-xs-12">
                        <div class="input-group">
                            <label class="checkbox-inline">
                                <input type='checkbox' value="1" name="ext[right]" <?php  if(!empty($ext['right'])) { ?>checked="checked"<?php  } ?>/>禁止修改\删除,只有系统Founder才权操作
                            </label>
                        </div>

                    </div>
                </div><?php  } ?>
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
<script type="text/javascript">
    <!--
 
    require(['bootstrap', 'util'],function($, u) {
        $('#select').click(function() {
            var footer = '<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>';
            var modalobj = u.dialog('请选择链接',['./index.php?c=utility&a=link&callback=selectLinkComplete'],footer,{containerName:'link-container'});
            modalobj.modal({'keyboard': false});
            modalobj.find('.modal-body').css({'height':'380px','overflow-y':'auto' });
            modalobj.modal('show');
            
            window.selectLinkComplete = function (a, b) {
                $('#url').val(a);
                $('#title').html(b);
                $('#title_val').val(b);
                modalobj.modal('hide');
            };
 
        });
        $('form').submit(function(){
            var  domain=$('#domain').val().trim();
            var v=/^[_\-0-9a-z]*$/;
            var msg='请输入有效的子域名!';
            if($('#type0').is(':checked')){
                v=/^[_\-0-9a-z]+(\.[_\-0-9a-z]+)+?$/;
                msg='请输入有效的域名!';
            }
            if(!v.test(domain)){
                $('#domain').focus();
                u.message(msg);
                return false;
            }
            var ismodule=$('#isaccount0').is(':checked');
            if(ismodule) {
                var url = $('#url').val().trim();
                if (!/^\.\/index\.php\?/.test(url)) {
                    $('#url').focus();
                    u.message('请选择效有模块入口!');
                    return false;
                }
            }else{
                var  domains=$('#domains').val().trim();
                if($('#redirect').is(':checked')){
                    if (!/^[0-9a-zA-Z]+[0-9a-zA-Z\.-\|]*?[0-9a-zA-Z]$/.test(domains)) {
                        $('#domains').focus();
                        u.message('请输入有效的跳转域名!');
                        return false; 
                    }
                }
            }
            return true;
        });
        $('.entry').bind('change',function(){
            var value= $(this).val();
            var check=$(this).is(":checked");
            if(check &&value==1){
                $('#o-module').hide();
                $('#o-account').show();
            }else{
                $('#o-account').hide();
                $('#o-module').show();
          
            }
        });
    });


    //-->
       
 
</script>
<?php  } else if($do == 'help') { ?>
<div class="main">
    <?php  if($_W['isfounder']) { ?> <form class="form-horizontal form" action="" method="post"><?php  } ?>
    <div class="panel panel-default">
        <div class="panel-heading">
            域名绑定配置
        </div>
        <div class="panel-body" style="padding-bottom: 0;">
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">App域名限制</label>
                <div class="col-sm-9 col-xs-12">
                    <div class="input-group">
                        <label class="radio-inline"><input type="radio" value="1"  name="protect_app"  <?php  if($set['protect_app']) { ?> checked="checked"<?php  } ?>>非绑定域名提示域名禁止</label>
                        <label class="radio-inline"><input type="radio" value="0"  name="protect_app"  <?php  if(empty( $set['protect_app'])) { ?> checked="checked"<?php  } ?>>不限制,允许所有</label>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">Web域名限制</label>
                <div class="col-sm-9 col-xs-12">
                    <div class="input-group">
                        <label class="radio-inline"><input type="radio" value="1"  name="protect_web"  <?php  if($set['protect_web']) { ?> checked="checked"<?php  } ?>>非绑定域名提示域名禁止</label>
                        <label class="radio-inline"><input type="radio" value="0"  name="protect_web"  <?php  if(empty( $set['protect_web'])) { ?> checked="checked"<?php  } ?>>不限制,允许所有</label>
                    </div>
                    <span class='help-block'>限制web域名时,可在绑定公众号域名时指定域名可以访问web后台</span>
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">系统域名</label>
                <div class="col-sm-9 col-xs-12">
                    <div class="input-group">
                         <span class="input-group-addon">  <?php  if(empty($set['host'])) { ?>未设置域名<?php  } else { ?>已设域名<?php  echo $set['host'];?><?php  } ?>,当前域名:<?php  echo $host;?></span>
                        <input type='text' name='host'  value="<?php echo empty($set['host'])?$host:$set['host']?>" class='form-control'/>
                        <span class="input-group-addon">  <?php  if(empty($set['load'])) { ?>域名处理文件未加载<span class='glyphicon glyphicon-remove'></span><?php  } else { ?>域名处理文件已加载<span class='glyphicon glyphicon-ok'></span><?php  } ?></span>
                    </div>
                    <span class='help-block'>系统域名为微信绑定更新域名;</span>
                </div>
            </div>
            <div class="alert alert-info">
                ***域名处理文件加载后才能处理域名绑定功能***,停用或删除域名绑定功能,请点"删除配置"
            </div>
        </div>
        <div class="panel-footer">
            <input type="hidden" name="token" value="<?php  echo $_W['token'];?>"/></td>
            <div class="btn-group btn-group-justified">
                <div class="btn-group">
                    <button class="btn btn-success"  type="submit" name="submit" value="save"><?php  if(empty($set['load'])) { ?>加载处理文件<?php  } else { ?>更新设置<?php  } ?></button>
                </div>
                <div class="btn-group">
                    <button class="btn btn-danger"  type="submit"  name="submit" value="delete" title="禁止域名绑定或删除模块前使用">删除设置</button>
                </div>
            </div>
        </div>
    </div>
    <?php  if($_W['isfounder']) { ?> </form><?php  } ?>
    <div class="panel panel-info">
        <div class="panel-heading">
            域名说明
        </div>
        <div class="panel-body">
            <div class="form-group clearfix">
				<span>更新配置文件:</span>
				<ul>
				<li>点击上面的更新配置或者手动修改data/config.php(不要用记事本修改)</li>
				<li>
				//++--------------- 域名绑定配置  ---------------// <br>
				$config['setting']['domain']['host']='www.xxxx.com(这里填您平台的域名)';<br>
				$config['setting']['domain']['protect_app']='1';<br>
				$config['setting']['domain']['protect_web']='1';<br>
				$config['setting']['domain']['tip']='域名授权异常';<br>
				if(file_exists(IA_ROOT . "/web/source/fournet/domain.php")){<br>
				   include IA_ROOT . "/web/source/fournet/domain.php";}<br>
				//----------------- 域名绑定配置  -------------++//<br>
				</li>
				</ul>
                <ul>
                    <li>服务器IP应是独立IP,通过IP可以访问后台系统</li>
                    <li>独立域名须绑定好域名解析,域名解析已指向当前服务器IP或原域名</li>
                    <li>子域名任意配置须增加二级域名泛解析,在DNS解析中加入*的泛解析支持或者使用CNAME配置</li>
                </ul>
                <span>特别注意:</span>
                <ul>
                    <li>使用独立域名,js安全域名要做相应修改,访问域名要在3个配置域名中/</li>
                    <li>使用前,请配置oAuth权限,<a href="./index.php?c=mc&a=passport&do=oauth&" title="点击配置">oAuth独立域名</a>,做统一的oauth授权处理;(注:模块应使用官方方法获取oAuth信息)</li>
                    <li>子域名为前域名的子域名,忽略www;如www.abc.com,abc.com的子域名都为*.abc.com;建议用二级域名</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<?php  } else if($do == 'manage') { ?>
<div class="panel panel-info">
    <div class="panel-heading">域名绑定列表(<?php  echo $total;?>条数据)</div>
    <div class="panel-body">
        
        <form action="<?php  echo url('fournet/domain/manage');?>" method="post" class="form-horizontal" role="form">
            <div class="row">
                <div class="col-md-6">
                    <input type="hidden" id="query" name="query" value="<?php  echo $query;?>"/>
                    <div class="input-group">
                        <div class="input-group-btn">
                            <button type="button" id="queryname" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><?php  echo $query_name[$query];?><span class="caret"></span></button>
                            <ul class="dropdown-menu" role="menu" >
                                <?php  if(is_array($query_name)) { foreach($query_name as $k => $v) { ?>
                                <li><a data-id="<?php  echo $k;?>" href="#" class="query"><?php  echo $v;?></a></li>
                                <?php  } } ?>
                            </ul>
                        </div>
                        <input class="form-control" name="keyword" id="" type="text" value="<?php  echo $_GPC['keyword'];?>">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="input-group">
                        <label class="checkbox-inline">
                            <input type='checkbox' value="1" name="all" <?php  if(!empty($_GPC['all'])) { ?>checked="checked"<?php  } ?>/>显示所有
                        </label>
                    </div>
                </div>
                <div class="col-md-1">
                    <button class="btn btn-primary" name="submit" type="submit" value="query"><i class="fa fa-search"></i>搜索
                    </button>
                </div>
            </div>
            </form>
    </div>
    <table class="table table-hover">
        <thead class="navbar-inner">
        <tr>
            <th width="160px">模块名称</th>
            <th>已绑域名</th>
            <th>模块入口</th>
            <th width="130px">操作</th>
        </tr>
        </thead>
        <tbody>
        <?php  if(is_array($list)) { foreach($list as $item) { ?>
        <tr>
            <td><?php  if(empty($item['all'])) { ?><?php  echo $item['module'];?><?php  } else { ?>所有模块<?php  } ?><br/>
                <?php  if(empty($item['uniacid'])) { ?><label class='label label-danger'>所有公众号</label><?php  } else { ?><label class='label label-success'> <?php  echo $item['accountname'];?> </label><?php  } ?></td>
            <td><label class='label label-primary'><?php  echo $item['domain'];?> </label>
                <?php  if(!empty($item['ext']['login'])) { ?><label class='label label-success'>允许Web登录</label><?php  } else { ?><label class='label label-danger'>禁止Web登录</label><?php  } ?> 
                <br/><a href="<?php  echo $item['domain_url'];?>" title="点击测试绑定域名"  target="_blank"><?php  echo $item['domain_url'];?></a> </td>
            <td><label class='label label-success'><?php  if(empty($item['title'])) { ?>自定义<?php  } else { ?><?php  echo $item['title'];?><?php  } ?></label>
                <?php  if(!empty($item['redirect'])) { ?><label class='label label-primary'>自动跳转:<?php  echo $item['ext']['domains'];?> </label><?php  } ?>
                <br/><?php  echo $item['entry'];?></td>
            <td>
                <?php  if($item['edit']) { ?>
                <a href="<?php  echo url('fournet/domain/post',array('id' => $item['id']))?>" title="编辑"
                   class=""btn btn-sm btn-default"><i class="fa fa-edit"></i>编辑</a>&nbsp;
                <a onclick="return confirm('确认要删除此条数据吗？'); return false;"
                   href="<?php  echo url('fournet/domain/manage',array('op' => 'delete', 'id' => $item['id']))?>"
                   title="删除" class=""btn btn-sm btn-default"><i class="fa fa-remove"></i>删除</a>
                <?php  } ?>
            </td>
        </tr>
        <?php  } } ?>
        <?php  if($limit['en_add']) { ?>
        <tr>
            <td colspan='4'>
                <a class='btn btn-default' href="<?php  echo url('fournet/domain/post')?>"><i class='fa fa-plus'></i>增加域名绑定</a>
            </td>
        </tr><?php  } ?>
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
<?php  } ?>
<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>