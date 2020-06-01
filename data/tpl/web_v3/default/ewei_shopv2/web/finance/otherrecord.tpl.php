<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>
<div class="page-header">当前位置：<span class="text-primary">佣金明细</span></div>
<div class="page-content">
    <form action="./index.php" method="get" class="form-horizontal" role="form" id="form1">
        <input type="hidden" name="c" value="site" />
        <input type="hidden" name="a" value="entry" />
        <input type="hidden" name="m" value="ewei_shopv2" />
        <input type="hidden" name="do" value="web" />
        <input type="hidden" name="r" value="finance.credit.otherrecord" />
        <div class="page-toolbar">
            <div class="input-group">
                <span class="pull-left">
                    <?php  echo tpl_daterange('time', array('sm'=>true,'placeholder'=>'操作时间'),true);?>
                </span>
                <span class="pull-right" style="width:200px;" >
                     <input type="text" class="form-control "  name="keyword" value="<?php  echo $_GPC['keyword'];?>" placeholder="请输入会员信息/备注信息" />
                </span>
               
                <span class="input-group-btn">
                    <button class="btn  btn-primary" type="submit"> 搜索</button>
                    <?php if(cv('finance.credit.otherrecord.export')) { ?>
                            <button type="submit" name="export" value="1" class="btn btn-success ">导出</button>
                    <?php  } ?>
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
            <table class="table">
                <thead class="navbar-inner">
                <tr>
                    <th style='width:120px;'>粉丝</th>
                    <th style='width:100px;'>会员信息</th>
                    <th style='width:80px;'>返利金额</th>
                    <th style='width:80px;'>备注</th>
                    <th style='width:80px;'></th>
                    <th style='width:100px;'>操作时间</th>
                </tr>
                </thead>
                <tbody>
                <?php  if(is_array($list)) { foreach($list as $row) { ?>
                <tr >
                    <td>
                    <?php if(cv('member.list.detail')) { ?>
                    <a  href="<?php  echo webUrl('member/list/detail',array('id' => $row['mid']));?>" target='_blank'>
                        <img  class="radius50"  src='<?php  echo tomedia($row['avatar'])?>' style='width:30px;height:30px;padding:1px;border:1px solid #ccc' onerror="this.src='../addons/ewei_shopv2/static/images/noface.png'"/> <?php  echo $row['nickname'];?>
                    </a>
                    <?php  } else { ?>
                    <img  class="radius50"  src='<?php  echo tomedia($row['avatar'])?>' style='width:30px;height:30px;padding1px;border:1px solid #ccc' onerror="this.src='../addons/ewei_shopv2/static/images/noface.png'" /> <?php  echo $row['nickname'];?>
                    <?php  } ?>

                    </td>
                    <td><?php  echo $row['realname'];?><br/><?php  echo $row['mobile'];?></td>
                    <td><?php  echo $row['amount'];?></td>
                    <td data-toggle='popover' data-html='true' data-placement='top' data-trigger='hover' data-content='<?php  echo $row['remark'];?>'><?php  echo $row['remark'];?></td>
                    <td>
                    
                    </td>
                    <td><?php  echo date('Y-m-d H:i:s',$row['createtime'])?></td>
                </tr>
                <?php  } } ?>
                </tbody>
                <tfoot>
                <tr>
                    <td>
                        <div class="btn-group"></div>
                    </td>
                    <td colspan="5" style="text-align: right">
                        <?php  echo $pager;?>
                    </td>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <?php  } ?>
</div>
<script type="text/javascript">
    var str = '<span style="background:none;border:0;">共<?php  echo $totalsum;?>元</span>';
    $('.nobg').before(str).css('margin-right','10px');
</script>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>
<!--efwww_com-->