<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>

<div class="page-header">当前位置：<span class="text-primary">销售统计</span></div>
<div class="page-content">
    <form action="./index.php" method="get" class="form-horizontal table-search">
            <input type="hidden" name="c" value="site" />
            <input type="hidden" name="a" value="entry" />
            <input type="hidden" name="m" value="ewei_shopv2" />
            <input type="hidden" name="do" value="web" />
            <input type="hidden" name="r"  value="statistics.sale" />

        <div class="page-toolbar">
            <div class="input-group">
                <span></span>
                 <span class="input-group-select">
                    <select name='searchtime'  class='form-control'>
                        <option value='default' <?php  if($_GPC['searchtime']=='default'||!$_GPC['searchtime']) { ?>selected<?php  } ?>>默认搜索</option>
                        <option value='timequantum' <?php  if($_GPC['searchtime']=='timequantum') { ?>selected<?php  } ?>>按时间段</option>
                    </select>
                </span>
                 <span class="input-group-btn" id="timequantum" style="display:<?php  if($_GPC['searchtime']=='timequantum') { ?><?php  } else { ?>none;<?php  } ?>">
                    <?php  echo tpl_form_field_daterange('time', array('starttime'=>date('Y-m-d H:i', $starttime),'endtime'=>date('Y-m-d H:i', $endtime)),true);?>
                </span>
                <span class="input-group-select timeother">
                    <select name="year" class='form-control'>
                        <?php  if(is_array($years)) { foreach($years as $y) { ?>
                        <option value="<?php  echo $y['data'];?>"  <?php  if($y['selected']) { ?>selected="selected"<?php  } ?>><?php  echo $y['data'];?>年</option>
                        <?php  } } ?>
                    </select>
                </span>
                <span class="input-group-select timeother">
                    <select name="month" class='form-control'>
                        <option value=''>月份</option>
                        <?php  if(is_array($months)) { foreach($months as $m) { ?>
                        <option value="<?php  echo $m['data'];?>"  <?php  if($m['selected']) { ?>selected="selected"<?php  } ?>><?php  echo $m['data'];?>月</option>
                        <?php  } } ?>
                    </select>
                </span>
                <span class="input-group-select timeother">
                     <select name="day" class='form-control'>
                         <option value=''>日期</option>
                     </select>
                </span>
                <span class="input-group-select">
                    <select name="type" class='form-control'>
                        <option value='0' <?php  if($_GPC['type']==0) { ?>selected="selected"<?php  } ?>>交易额</option>
                        <option value='1' <?php  if($_GPC['type']==1) { ?>selected="selected"<?php  } ?>>交易量</option>

                    </select>
                </span>
                <span class="input-group-select">
                    <select name="byType" class='form-control'>
                        <option value='default' <?php  if($_GPC['byType']=='default') { ?>selected="selected"<?php  } ?>>按时间</option>
                        <option value='store' <?php  if($_GPC['byType']=='store') { ?>selected="selected"<?php  } ?>>按门店</option>
                        <option value='distributor' <?php  if($_GPC['byType']=='distributor') { ?>selected="selected"<?php  } ?>>按门店配送人</option>
                    </select>
                </span>
                 <span class="input-group-select">
                    <select name="status" class='form-control'>
                        <option value='' <?php  if($_GPC['status']=='') { ?>selected="selected"<?php  } ?>>全部订单</option>
                        <option value='1' <?php  if($_GPC['status']=='1') { ?>selected="selected"<?php  } ?>>待发货</option>
                        <option value='2' <?php  if($_GPC['status']=='2') { ?>selected="selected"<?php  } ?>>待收货</option>
                        <option value='3' <?php  if($_GPC['status']=='3') { ?>selected="selected"<?php  } ?>>已完成</option>
                    </select>
                </span>
                <span class="input-group-btn">
                    <button class="btn btn-primary" type="submit"> 搜索</button>
                     <?php if(cv('statistics.sale.export')) { ?>
                    <button type="submit" name="export" value='1' class="btn btn-success">导出</button>
                    <?php  } ?>
                </span>
            </div>

        </div>

 </form>


<div class="panel panel-default">
    <div class='panel-heading'>
        <?php  if(empty($type)) { ?>交易额<?php  } else { ?>交易量<?php  } ?>：<span style="color:red; "><?php  echo $totalcount;?></span>，
        最高<?php  if(empty($type)) { ?>交易额<?php  } else { ?>交易量<?php  } ?>：<span style="color:red; "><?php  echo $maxcount;?></span> <?php  if(!empty($maxcount_date)) { ?><span>(<?php  echo $maxcount_date;?></span>)<?php  } ?>
    </div>
    <?php  if($_GPC['byType']=='distributor') { ?>
    <div class="panel-body">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th style="width:100px;">配送人</th>
                    <th style="width:100px;">导出</th>
                </tr>
            </thead>

            <tbody>
                <?php  if(is_array($distributorlist)) { foreach($distributorlist as $row) { ?>
                <tr>
                    <td><?php  echo $row['nickname'];?></td>
                    <td>
                        <?php  if($_GPC['searchtime']=='timequantum') { ?>
                        <a href="<?php  echo webUrl('statistics/sale/export',array('distributor'=>$row['openid'],'byType'=>$byType,'timestart'=>$timestart,'timeend'=>$timeend,'searchtime'=>$_GPC['searchtime'],'status'=>$status))?>">导出</a>
                        <?php  } else { ?>
                        <a href="<?php  echo webUrl('statistics/sale/export',array('distributor'=>$row['openid'],'byType'=>$byType,'year'=>$year,'month'=>$month,'day'=>$day,'status'=>$status))?>">导出</a>
                        <?php  } ?>
                    </td>
                </tr>
                <?php  } } ?>
            </tbody>
        </table>
    </div>
    <?php  } else { ?>
    <div class="panel-body">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th style='width:100px;'>
                        <?php  if($_GPC['byType']=='store') { ?>
                        门店名称
                        <?php  } else { ?>
                        <?php  if(empty($_GPC['month'])) { ?>月份<?php  } else { ?>日期<?php  } ?>
                        <?php  } ?>
                    </th>
                    
                    <th style='width:100px;'><?php  if(empty($type)) { ?>交易额<?php  } else { ?>交易量<?php  } ?></th>
                    <?php  if($_GPC['byType']=='store') { ?>
                     <th style="width: 80px;">导出</th>
                    <?php  } ?>
                    <th style="width: 100px;">所占比例</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php  if(is_array($list)) { foreach($list as $row) { ?>
                <tr>
                    <td><?php  echo $row['data'];?></td>
                    <td><?php  echo $row['count'];?></td>
                    <?php  if($_GPC['byType']=='store') { ?>
                     <td>
                        <?php  if($_GPC['searchtime']=='timequantum') { ?>
                        <a href="<?php  echo webUrl('statistics/sale/export',array('storeid'=>$row['storeid'],'byType'=>$byType,'timestart'=>$timestart,'timeend'=>$timeend,'searchtime'=>$_GPC['searchtime'],'status'=>$status))?>" target="_blank">导出<?php  echo $row['storeid'];?></a>
                        <?php  } else { ?>
                        <a href="<?php  echo webUrl('statistics/sale/export',array('storeid'=>$row['storeid'],'byType'=>$byType,'year'=>$year,'month'=>$month,'day'=>$day))?>" target="_blank">导出<?php  echo $row['storeid'];?></a>
                        <?php  } ?>
                    </td>
                    <?php  } ?>
                    <td><span class="process-num" style="color:#000"><?php echo empty($row['percent'])?'':$row['percent'].'%'?></span></td>
                    <td>
                       <div class="progress">
                           <div style="width: <?php  echo $row['percent'];?>%;" class="progress-bar progress-bar-info" ></div>
                       </div>
                    </td>
                </tr>
                <?php  } } ?>
            </tbody>
        </table>
    </div>
    <?php  } ?>
</div>
</div>
<script language='javascript'>
    <?php  if($_GPC['searchtime']=='timequantum') { ?>
     $(".timeother").hide();
    <?php  } else { ?>
    $(".timeother").show();
    <?php  } ?>
    $("select[name=searchtime]").change(function(){

        var currentVal = $(this).val();
        if (currentVal=='timequantum') {
            $("#timequantum").show();
            $(".timeother").hide();
        }else{
            $(".timeother").show();
            $("#timequantum").hide();
        }
    })

    function get_days(){
          
        var year = $('select[name=year]').val();
        var month =$('select[name=month]').val();
        var day  = $('select[name=day]');
       day.get(0).options.length = 0 ;
        if(month==''){
	   day.append("<option value=''>日期</option");
            return;
        }
       
        day.get(0).options.length = 0 ;
        day.append("<option value=''>...</option").attr('disabled',true);
        $.post("<?php  echo webUrl('util/days')?>",{year:year,month:month},function(days){
             day.get(0).options.length = 0 ;
             day.removeAttr('disabled');
             days =parseInt(days);
             day.append("<option value=''>日期</option");
             for(var i=1;i<=days;i++){
                 day.append("<option value='" + i +"'>" + i + "日</option");
             }
          
             <?php  if(!empty($day)) { ?>
                day.val( <?php  echo $day;?>);
             <?php  } ?>
        })
    }
    $('select[name=month]').change(function(){
           get_days();
    })
    
    get_days();
 </script>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>

<!--efwww_com-->