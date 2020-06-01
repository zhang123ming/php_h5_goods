<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>
<div class="page-header">  当前位置：<span class="text-primary">商品价格修复</span></div>
<div class="page-content">
    <form class="form-horizontal form-search">
        <div class="form-group" >
            <label class="col-lg control-label must">修复价格为0问题</label>
            <div class="col-sm-9">
                <a data-toggle="ajaxPost" data-href="<?php  echo webUrl('goods/goodsprice')?>" class="btn btn-primary btn-sm"> 点击修复</a>
                <div class="help-block"> 价格有问题或者显示为空的可以点击此修复!</div>
            </div>
        </div>
        <div class="form-group" >
            <label class="col-lg control-label must">修复重复会员</label>
            <div class="col-sm-9">
                <a data-toggle="ajaxPost" data-href="<?php  echo webUrl('member/list/repeat')?>" class="btn btn-primary btn-sm"> 点击修复</a>
                <div class="help-block"> 价格有问题或者显示为空的可以点击此修复!</div>
            </div>
        </div>
    </form>
</div>

<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>
<!--efwww_com-->