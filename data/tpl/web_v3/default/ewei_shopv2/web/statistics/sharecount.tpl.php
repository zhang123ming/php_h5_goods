<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>
<div class="page-header">当前位置：<span class="text-primary">分享统计</span></div>
<div class="page-content">
    <form action="./index.php" method="get" class="form-horizontal" role="form" id="form1">
        <input type="hidden" name="c" value="site" />
        <input type="hidden" name="a" value="entry" />
        <input type="hidden" name="m" value="ewei_shopv2" />
        <input type="hidden" name="do" value="web" />
        <input type="hidden" name="r" value="statistics.sharecount" />
        <input type="hidden" name="iswxapp" value="<?php  echo $iswxapp;?>" />
        <div class="page-toolbar">
        	<span class="pull-left">
                <?php  echo tpl_daterange('time', array('sm'=>true, 'placeholder'=>'时间筛选'),true);?>
            </span>
            <div class="input-group">

            
                
          
            
                
                <input type="text" class="form-control " name="realname" value="<?php  echo $_GPC['realname'];?>" placeholder="可搜索 商品名称/商品ID">
                <span class="input-group-btn">
                    <button class="btn  btn-primary" type="submit"> 搜索</button>
                    <button type="submit" name="export" value="1" class="btn btn-success ">导出</button>
                </span>
            </div>
        </div>
    </form>
    <?php  if(empty($list)) { ?>
	    <div class="panel panel-default">
	        <div class="panel-body empty-data">未查询到相关数据</div>
	    </div>
    <?php  } else { ?>
	    <div class="row">
	        <div class="col-md-12">
	            <table class="table table-hover table-responsive">
		            <thead class="navbar-inner" >
		            <tr>
		                <th style='width:100px;'>商品</th>
		                <th style='width:100px;'>分享数</th>
		                <th style='width:100px;'>最新时间</th>
		                <th style='width:100px;'>操作</th>
		            </tr>
		            </thead>
		            <tbody>
		           	<?php  if(is_array($list)) { foreach($list as $row) { ?>
		           		<tr>
		           			<td style="overflow: hidden">
				                <div rel="pop">
				                    <div style="display: flex">
				                        <span data-toggle='tooltip' title='<?php  echo $row["title"];?>'>
				                        <?php  if(!empty($row["thumb"])) { ?>
				                        <img src='<?php  echo tomedia($row["thumb"])?>' style='width:50px;padding:1px;border:1px solid #ccc' onerror="this.src='../addons/ewei_shopv2/static/images/noface.png'"/>
				                        <?php  } ?>
				                        <?php  if(empty($row["title"])) { ?>未更新<?php  } else { ?><?php  echo $row["title"];?><?php  } ?>
				                        </span>
				                    </div>
				                </div>
				            </td>
		           			<th><?php  echo $row["count"];?></th>
		           			<td><?php  echo $row["update_time"];?></td>
		           			<th>
		           				 <a id="btn-submit"  href="<?php  echo webUrl('statistics/sharecount/post',array('id' => $row['goods_id']));?>" class='external btn btn-danger btn-sm '>查看详情</a>
		           			</th>
		           		</tr>
		            <?php  } } ?>
		            </tbody>
	        </table>
	        <?php  echo $pager;?>
	        </div>
	    </div>
 	<?php  } ?>
</div>

<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>