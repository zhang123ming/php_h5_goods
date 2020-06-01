<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>

<div class="page-header">当前位置：<span class="text-primary">部门列表</span></div>

<div class="page-content">

    <form action="./index.php" method="get" class="form-horizontal table-search" role="form">
        <input type="hidden" name="c" value="site"/>
        <input type="hidden" name="a" value="entry"/>
        <input type="hidden" name="m" value="ewei_shopv2"/>
        <input type="hidden" name="do" value="web"/>
        <input type="hidden" name="r" value="member.detp"/>
        <?php if(cv('member.detp.add')) { ?>
        <a class="btn btn-primary btn-sm" href="<?php  echo webUrl('member/detp/add',array('isAdd'=>1,'id'=>$_GPC['id']));?>"><i class="fa fa-plus"></i> 添加部门</a>
        <?php  } ?>
    </form>
        <div class="row">
            <div class="col-md-12">

                <table class="table table-responsive">
                    <thead>
                    <tr>
                        <th><input type="checkbox" value=""></th>
                        <th>分公司</th>
						<th>状态</th>
                        <th style="width: 125px;text-align: center;">操作</th>
                    </tr>
                    </thead>
                    <tbody>
                        
                    <?php  if(is_array($list)) { foreach($list as $item) { ?>
                        <tr>
                            <td>
                                <input type="checkbox" value="<?php  echo $item['detp_id'];?>">
                            </td>
                            <td><?php  echo $item['detp_name'];?></td>
                            <td>
                                <span class='label <?php  if($item['status']==1) { ?>label-primary<?php  } else { ?>label-default<?php  } ?>'
                                <?php if(cv('member.detp.edit')) { ?>
                                data-toggle='ajaxSwitch'
                                data-switch-value='<?php  echo $item['status'];?>'
                                data-switch-value0='0|禁用|label label-default|<?php  echo webUrl('member/detp/status',array('status'=>1,'id'=>$item['detp_id']))?>'
                                data-switch-value1='1|启用|label label-primary|<?php  echo webUrl('member/detp/status',array('status'=>0,'id'=>$item['detp_id']))?>'
                                <?php  } ?>>
                                <?php  if($item['status']==1) { ?>启用<?php  } else { ?>禁用<?php  } ?></span>
                            </td>
                            <td>
                                <a class="btn btn-danger btn-sm" href="<?php  echo webUrl('member/groups',array('id'=>$item['detp_id']));?>"><i ></i> 查看组</a>
                            </td>
                        </tr>
                    <?php  } } ?>
                        <td colspan="4" style="text-align: right">
                            <?php  echo $pager;?>
                        </td>
                    </tfoot>
                </table>
            </div>
        </div>

   
</div>


<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>