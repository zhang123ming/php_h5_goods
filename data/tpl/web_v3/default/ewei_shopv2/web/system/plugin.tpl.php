<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>
 <div class="page-header">
     当前位置：<span class="text-primary">应用信息设置 (应用关闭状态时只有超级管理员才能使用，安装后默认为关闭)</span>

          </div>
 
<div class="page-content">
    <span class='pull-right col-sm-4' style='margin-bottom:10px;'>
          <form action="./index.php" method="get" class="form-horizontal" role="form" id="form1">
              <input type="hidden" name="c" value="site" />
              <input type="hidden" name="a" value="entry" />
              <input type="hidden" name="m" value="ewei_shopv2" />
              <input type="hidden" name="do" value="web" />
              <input type="hidden" name="r" value="system.plugin" />


              <div class="col-sm-12">

                  <div class="input-group" style='padding:0;'>
                      <input type="text" class="input-sm form-control" name='keyword' value="<?php  echo $_GPC['keyword'];?>" placeholder="插件名称/插件标识">
                                        <span class="input-group-btn">

                                     <button class="btn btn-primary" type="submit"> 搜索</button> </span>
                  </div>

              </div>

          </form>
     </span>
    <form method='post' class="form-horizontal form-validate">

        <table class="table">
            <thead class="navbar-inner">
            <tr>
                <th style='width:100px;'>图标</th>
                <th style=''>标识</th>
                <th  style='width:80px;'>开关</th>
                <th style='width:100px'>排序</th>
                <th style='width:150px;' >插件名称</th>
                <th>插件简介</th>
            </tr>
            </thead>
            <tbody>
            <?php  if(is_array($list)) { foreach($list as $row) { ?>

            <tr>
                <td>
                    <input type='hidden'  name="thumb[<?php  echo $row['id'];?>]" value="<?php  echo $row['thumb'];?>" />
                    <img onclick="selectImage(this)" onerror="this.src='<?php echo EWEI_SHOPV2_LOCAL;?>static/images/yingyong.png'"
                         src="<?php  if(empty($row['thumb'])) { ?><?php echo EWEI_SHOPV2_LOCAL;?>static/images/yingyong.png<?php  } else { ?><?php  echo tomedia($row['thumb'])?><?php  } ?>"
                         <?php  if(!empty($row['thumb'])) { ?>
                    data-toggle='popover'
                    data-html ='true'
                    data-placement='top'
                    data-trigger ='hover'
                    data-content="<img src='<?php  echo tomedia($row['thumb'])?>' style='width:30px;height:30px;' />"
                    <?php  } ?>
                    style="width:40px;height:40px">
                </td>
                <td><?php  echo $row['identity'];?></td>
                <td>
                    <?php  if($row['identity']=='system') { ?>
                    --
                    <input type='hidden' name='status[<?php  echo $row['id'];?>]' value="0" />
                    <?php  } else { ?>
                    <label class='checkbox-inline'>
                        <input type='checkbox' name='status[<?php  echo $row['id'];?>]' value="1" <?php  if($row['status']==1) { ?>checked<?php  } ?> /> 开启
                    </label>

                    <?php  } ?>

                </td>
                <td><input type="text" class="form-control" name="displayorder[<?php  echo $row['id'];?>]" value="<?php  echo $row['displayorder'];?>"></td>
                <td><input type="text" class="form-control" name="name[<?php  echo $row['id'];?>]" value="<?php  echo $row['name'];?>"></td>

                <td><textarea  class="form-control desc" name="desc[<?php  echo $row['id'];?>]" style='height:35px;resize:none;' ><?php  echo $row['desc'];?></textarea></td>
            </tr>
            <?php  } } ?>
            <tr>
                <td colspan='6'>
                    <input type="submit"  class="btn btn-primary" value="提交">

                </td>
            </tr>
            </tbody>
        </table>
        <?php  echo $pager;?>

    </form>
</div>
<script language='javascript'>
    $(function(){
        $('.desc').focus(function(){
            $(this).css('height','80px');
        }).blur(function(){
            $(this).css('height','35px');
        })
    });
    
    function selectImage(obj){
		util.image('',function(val){
			 
			$(obj).attr('src',val.url).popover({
				trigger: 'hover',
				html: true,
				container: $(document.body),
				content: "<img src='" + val.url  + "' style='width:40px;height:40px;' />",
				placement: 'top'
			});
			
			var group  =$(obj).parent();
		         
			group.find(':hidden').val(val.url), group.find('i').show().unbind('click').click(function(){
				$(obj).attr('src',"<?php echo EWEI_SHOPV2_LOCAL;?>static/images/nopic100.jpg");
				group.find(':hidden').val('');
				group.find('i').hide();
				$(obj).popover('destroy');
			});
		});
	}
        
    
</script>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>
<!--efwww_com54mI5p2D5omA5pyJ-->