<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>
<style type='text/css'>
    .moresearch { padding:0px 10px;}
    .moresearch .col-sm-2 {
        padding:0 5px
    }
    .popover{
        width:170px;
        font-size:12px;
        line-height: 21px;
        color: #0d0706;
    }
    .popover span{
        color: #b9b9b9;
    }
    .nickname{
        display: inline-block;
        max-width:200px;
        overflow: hidden;
        text-overflow:ellipsis;
        white-space: nowrap;
        vertical-align: middle;
    }

    .tooltip-inner{
        border:none;
    }
    .info{
        height: 100%;
        width:370px;
        float:left;
        border-right:1px solid #efefef;
        padding: 40px 20px;
        line-height: 25px;
    }
    .info i{
        display: inline-block;
        width:80px;
        text-align: right;
        color: #999;
    }
</style>
<div class="page-header">
    当前位置：<span class="text-primary">业绩明细</span></small></span>
</div>
<div class="page-content">
    <div class="page-sub-toolbar">
        <span class=''>
            <a class="btn btn-default  btn-sm" href="<?php  echo referer()?>">返回列表</a>
        </span>
    </div>
<div style="height: 180px;border: 1px solid #efefef;margin-bottom: 30px">
        <div class="info">
            <img class="pull-left" src='<?php  echo $member['avatar'];?>' style='width:100px;height:100px;border:1px solid #ccc;padding:1px'  onerror="this.src='../addons/ewei_shopv2/static/images/noface.png'"/>
           <div class="pull-left">
              <i> 昵称：</i><?php  echo $member['nickname'];?><br/>
              <i> 姓名：</i><?php  echo $member['realname'];?> <br/>
               <i>手机号：</i><?php  echo $member['mobile'];?> <br/>
               <i>微信号：</i> <?php  echo $member['weixin'];?><br/>
           </div>
        </div>
        <div class="info" style="text-align: center">
            <!-- <p class="pull-left" style="color: #999;width: 120px;"> 下级分销商：</p> -->
            <div  class="pull-left" style="text-align: left">
                <!-- 订单数：<span class="text-danger"><?php  echo $totalordercount;?></span> 个<br/> -->
                总业绩：<span class="text-danger"><?php echo $totalperformance?$totalperformance:0?></span> 元
            </div>
        </div>
        <div style="padding: 40px 20px;float: left">
            <!-- 下级会员(非分销商): <span style='color:red'><?php  echo $level11;?></span> 人    <br/> -->
        </div>
        <div style="clear: both"></div>
</div>

<form action="./index.php" method="get" class="form-horizontal" role="form" id="form1">
    <input type="hidden" name="c" value="site" />
    <input type="hidden" name="a" value="entry" />
    <input type="hidden" name="m" value="ewei_shopv2" />
    <input type="hidden" name="do" value="web" />
    <input type="hidden" name="r" value="commission.agent.user" />
    <input type="hidden" name="id" value="<?php  echo $agentid;?>" />
    <?php  if(count($list)>0) { ?>

    <table class="table table-hover table-responsive">
        <thead class="navbar-inner" >
        <tr>
            <th>商品</th>
            <th>数量</th>
            <th>消费总额</th>
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
            <td>
                <?php  echo $row['total'];?>
            </td>

            <td>
                <?php  echo $row['goodsprice'];?>
            </td>
        </tr>
        <?php  } } ?>
        </tbody>
    </table>
    <?php  echo $pager;?>

    <?php  } else { ?>
    <div class='panel panel-default'>
        <div class='panel-body' style='text-align: center;padding:30px;'>
            暂时没有任何记录!
        </div>
    </div>
    <?php  } ?>
    </form>
    </div>
    <script language="javascript">
        require(['bootstrap'],function(){
            $("[rel=pop]").popover({
                trigger:'manual',
                placement : 'right',
                title : $(this).data('title'),
                html: 'true',
                content : $(this).data('content'),
                animation: false
            }).on("mouseenter", function () {
                var _this = this;
                $(this).popover("show");
                $(this).siblings(".popover").on("mouseleave", function () {
                    $(_this).popover('hide');
                });
            }).on("mouseleave", function () {
                var _this = this;
                setTimeout(function () {
                    if (!$(".popover:hover").length) {
                        $(_this).popover("hide")
                    }
                }, 100);
            });
        });
    </script>
    <?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>
<!-- yi fu yuan ma wang -->