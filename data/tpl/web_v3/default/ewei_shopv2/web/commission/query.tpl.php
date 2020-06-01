<?php defined('IN_IA') or exit('Access Denied');?><div style='max-height:500px;overflow:auto;min-width:850px;'>
<table class="table table-hover" style="min-width:850px;">
    <tbody>   
        <?php  if(is_array($ds)) { foreach($ds as $row) { ?>
        <tr>
            <td><img src='<?php  echo tomedia($row['avatar'])?>' style='width:30px;height:30px;padding1px;border:1px solid #ccc' /> <?php  echo $row['nickname'];?></td>
            <td>
            <?php  echo $row['realname'];?>
            <span style="padding-left: 5px">
                   <span class="nickname">
                       <?php  if(strexists($row['openid'],'sns_wa')) { ?><i class="icow icow-xiaochengxu" style="color: #7586db;vertical-align: middle;" data-toggle="tooltip" data-placement="top" title="" data-original-title="来源: 小程序"></i><?php  } ?>
                       <?php  if(strexists($row['openid'],'sns_qq')||strexists($row['openid'],'sns_wx')||strexists($row['openid'],'wap_user')) { ?><i class="icow icow-app" style="color: #44abf7;vertical-align: top;" data-toggle="tooltip" data-placement="bottom" data-original-title="来源: 全网通(<?php  if(strexists($row['openid'],'wap_user')) { ?>手机号注册<?php  } else { ?>APP<?php  } ?>)"></i><?php  } ?>
                   </span>
                   <?php  if($row['isblack']==1) { ?>
                        <span class="text-danger">黑名单<i class="icow icow-heimingdan1"style="color: #db2228;margin-left: 2px;font-size: 13px;"></i></span>
                   <?php  } ?>
               </span>
            </td>
            <td>id:<?php  echo $row['id'];?></td>
            <td><?php  echo $row['mobile'];?></td>
            <td style="width:80px;"><a href="javascript:;" onclick='select_member(<?php  echo json_encode($row);?>)'>选择</a></td>
        </tr>
        <?php  } } ?>
        <?php  if(count($ds)<=0) { ?>
        <tr> 
            <td colspan='4' align='center'>未找到分销商</td>
        </tr>
        <?php  } ?>
    </tbody>
</table>
    </div>
<!-- yi fu yuan ma wang -->