<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>
<div class="page-header">
    当前位置：<span class="text-primary">批量发货</span>
</div>
<div class="page-content">
    <div class="summary_box">
        <div class="summary_title">
            <span class=" title_inner">批量发货</span>
        </div>
        <div class="summary_lg">
            功能介绍: 使用excel快速导入进行订单发货
            <p>如重复导入数据将以最新导入数据为准，请谨慎使用</p>
            <p>数据导入订单状态自动修改为已发货</p>
            <p>一次导入的数据不要太多,大量数据请分批导入,建议在服务器负载低的时候进行</p>
            使用方法:
            <p>1. 下载Excel模板文件并录入信息</p>
            <p>2. 选择快递公司</p>
            <p>3. 上传Excel导入</p>
            格式要求：  Excel第一列必须为订单编号，第二列必须为快递单号，请确认订单编号与快递单号的备注
        </div>
    </div>

    <form id="importform" class="form-horizontal form" action="" method="post" enctype="multipart/form-data">

        <input type="hidden" name="typeid" value="<?php  echo $item['id'];?>"/>
        <div class='form-group'>

            <div class="form-group">
                <label class="col-lg control-label must">默认快递公司</label>
                <div class="col-sm-5 goodsname"  style="padding-right:0;" >
                    <select class="form-control" name="express" id="express">
                        <option value="" data-name="">其他快递</option>

                        <?php  if(is_array($express_list)) { foreach($express_list as $value) { ?>
                        <option value="<?php  echo $value['express'];?>" data-name="<?php  echo $value['name'];?>"><?php  echo $value['name'];?></option>
                        <?php  } } ?>

                    </select>
                    <input type='hidden' name='expresscom' id='expresscom' value="<?php  echo $refund['expresscom'];?>"/>
                </div>
            </div>

            <div class="form-group">
                <label class="col-lg control-label must">EXCEL</label>

                <div class="col-sm-5 goodsname"  style="padding-right:0;" >
                    <input type="file" name="excelfile" class="form-control" />
                    <span class="help-block">如果遇到数据重复则将进行数据更新</span>
                </div>
            </div>

        </div>

        <div class='form-group'>
            <div class="col-sm-12">
                <div class="modal-footer" style="text-align: left">
                    <?php if(cv('order.batchsend.main')) { ?>
                    <button type="submit" class="btn btn-primary" name="cancelsend" value="yes">确认导入</button>
                    <?php  } ?>

                    <?php if(cv('order.batchsend.import')) { ?>
                    <a class="btn btn-primary" href="<?php  echo webUrl('order/batchsend/import')?>" ><i class="fa fa-download" title=""></i> 下载Excel模板文件</a>
                    <?php  } ?>
                    <a class='btn btn-default' href="<?php  echo webUrl('goods/virtual/temp')?>">返回列表</a>
                </div>
            </div>
        </div>
    </form>
</div>

<script language='javascript'>
    $(function(){

        $('#importform').submit(function(){
            if(!$(":input[name=excelfile]").val()){
                tip.msgbox.err("您还未选择Excel文件哦~");
                return false;
            }
        })

        $("#express").change(function () {
            var obj = $(this);
            var sel = obj.find("option:selected").attr("data-name");
            $("#expresscom").val(sel);
        });

    })

</script>

<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>

<!--efwww_com-->