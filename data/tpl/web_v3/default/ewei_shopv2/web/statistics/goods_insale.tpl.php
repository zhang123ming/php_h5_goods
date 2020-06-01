<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>
<style>
    tbody tr td{
        position: relative;
    }
    tbody tr  .icow-weibiaoti--{
        visibility: hidden;
        display: inline-block;
        color: #fff;
        height:18px;
        width:18px;
        background: #e0e0e0;
        text-align: center;
        line-height: 18px;
        vertical-align: middle;
    }
    tbody tr:hover .icow-weibiaoti--{
        visibility: visible;
    }
    tbody tr  .icow-weibiaoti--.hidden{
        visibility: hidden !important;
    }
    .full .icow-weibiaoti--{
        margin-left:10px;
    }
    .full>span{
        display: -webkit-box;
        display: -webkit-flex;
        display: -ms-flexbox;
        display: flex;
        vertical-align: middle;
        align-items: center;
    }
    tbody tr .label{
        margin: 5px 0;
    }
    .goods_attribute a{
        cursor: pointer;
    }
</style>
<div class="page-header">
    当前位置：<span class="text-primary">在售商品</span>
</div>
<div class="page-content">

    <form action="./index.php" method="get" class="form-horizontal form-search" role="form">
        <input type="hidden" name="c" value="site" />
        <input type="hidden" name="a" value="entry" />
        <input type="hidden" name="m" value="ewei_shopv2" />
        <input type="hidden" name="do" value="web" />
        <input type="hidden" name="r"  value="statistics.goods_insale" />
        <div class="page-toolbar">
            <div class="input-group col-sm-6 pull-right">
                <span class="input-group-select">
                    <select name="cate" class='form-control select2' style="width:200px;" data-placeholder="商品分类">
                        <option value="" <?php  if(empty($_GPC['cate'])) { ?>selected<?php  } ?> >商品分类</option>
                            <?php  if(is_array($category)) { foreach($category as $c) { ?>
                        <option value="<?php  echo $c['id'];?>" <?php  if($_GPC['cate']==$c['id']) { ?>selected<?php  } ?> ><?php  echo $c['name'];?></option>
                            <?php  } } ?>
                    </select>
                </span>
                <input type="text" class="input-sm form-control" name='keyword' value="<?php  echo $_GPC['keyword'];?>" placeholder="ID/名称/编号/条码<?php  if($merch_plugin) { ?>/商户名称<?php  } ?>">
                <span class="input-group-btn">
                    <button class="btn btn-primary" type="submit"> 搜索</button><?php if(cv('statistics.goods_insale.export')) { ?>
                            
                            <?php  } ?>
                            <button type="submit" name="export" value="1" class="btn btn-success">导出</button>
                            <button type="submit" name="export" value="2" class="btn btn-success">导出(按规格)</button>
                </span>
            </div>
        </div>
    </form>
    <?php  if(count($list)>0 && cv('goods.main')) { ?>
    <div class="row">
        <div class="col-md-12">
            
    <table class="table table-responsive">
        <thead class="navbar-inner">
            <tr>
                <th style="width:20%;">商品编号</th>
                <th style="width:20%;">商品</th>
                <th style="width: 20%;">价格</th>
                <th style="width: 20%;">库存</th>
                <th style="width: 20%;">销量</th>
            </tr>

        </thead>
        <tbody>
            <?php  if(is_array($list)) { foreach($list as $item) { ?>
            <tr class="movecolor">
                <td><?php  echo $item['goodssn'];?></td>
                <td class='full' >
                        <span>
                            <span style="display: block;width: 100%;">
                            <?php if(cv('goods.edit')) { ?>
                                <a href='javascript:;' style="overflow : hidden;text-overflow: ellipsis;display: -webkit-box;-webkit-line-clamp: 2;-webkit-box-orient: vertical;">
                                    <?php  echo $item['title'];?>
                                </a>
                            <?php  } else { ?>
                                  <?php  echo $item['title'];?>
                            <?php  } ?>
                                <?php  if(!empty($category[$item['pcate']])) { ?>
                                    <span class="text-danger">[<?php  echo $category[$item['pcate']]['name'];?>]</span>
                                <?php  } ?>
                                <?php  if(!empty($category[$item['ccate']])) { ?>
                                    <span class="text-info">[<?php  echo $category[$item['ccate']]['name'];?>]</span>
                                <?php  } ?>
                                <?php  if(!empty($category[$item['tcate']]) && intval($shopset['catlevel'])==3) { ?>
                                    <span class="text-info">[<?php  echo $category[$item['tcate']]['name'];?>]</span>
                                <?php  } ?>
								<?php  if($item['isstatustime']) { ?>
								<?php  if($item['statustimestart']) { ?><br>上架:<?php  echo date("Y-m-d H:i:s",$item['statustimestart']);?><?php  } ?>
								<?php  if($item['statustimeend']) { ?><br>下架:<?php  echo date("Y-m-d H:i:s",$item['statustimeend'])?><?php  } ?>
								<?php  } ?>
                        </span>
                        </span>
                </td>

                <td>&yen;
                    <?php  if($item['hasoption']==1) { ?>
                    <?php if(cv('goods.edit')) { ?>
                     <span data-toggle='tooltip' title='多规格不支持快速修改'><?php  echo $item['marketprice'];?></span>
                    <?php  } else { ?>
                     <?php  echo $item['marketprice'];?>
                    <?php  } ?>
                    <?php  } else { ?>
                    <?php if(cv('goods.edit')) { ?>

                    <a href='javascript:;'><?php  echo $item['marketprice'];?></a>
                    <?php  } else { ?>
                    <?php  echo $item['marketprice'];?>
                    <?php  } ?><?php  } ?>

                </td>

                <td>
                    <?php  if(!empty($item['hoteldaystock'])) { ?>
                        <span data-toggle='tooltip' title='民宿类商品显示每日库存'><?php  echo $item['hoteldaystock'];?>/日</span>
                    <?php  } else if($item['hasoption']==1) { ?>
                        <?php if(cv('goods.edit')) { ?>
                            <span data-toggle='tooltip' title='多规格不支持快速修改'>
                                <?php  if($item['total']<=$goodstotal) { ?><span class="text-danger"><?php  echo $item['total'];?></span><?php  } else { ?><?php  echo $item['total'];?><?php  } ?>
                            </span>
                        <?php  } else { ?>
                            <?php  if($item['total']<=$goodstotal) { ?><span class="text-danger"><?php  echo $item['total'];?></span><?php  } else { ?><?php  echo $item['total'];?><?php  } ?>
                        <?php  } ?>
                    <?php  } else { ?>
                        <?php if(cv('goods.edit')) { ?>
                            <a href='javascript:;' >
                                <?php  if($item['total']<=$goodstotal) { ?><span class="text-danger"><?php  echo $item['total'];?></span><?php  } else { ?><?php  echo $item['total'];?><?php  } ?>
                            </a>
                        <?php  } else { ?>
                            <?php  if($item['total']<=$goodstotal) { ?><span class="text-danger"><?php  echo $item['total'];?></span><?php  } else { ?><?php  echo $item['total'];?><?php  } ?>
                        <?php  } ?>
                    <?php  } ?>
                </td>
                <td><?php  echo $item['salesreal'];?></td>

            
                    </tr>
            <?php  if(!empty($item['merchname']) && $item['merchid'] > 0) { ?>
            <tr style="background: #f9f9f9">
                <td colspan='<?php  if($goodsfrom=='cycle') { ?>9<?php  } else { ?>10<?php  } ?>' style='text-align: left;border-top:none;padding:5px 0;' class='aops'>
                    <span class="text-default" style="margin-left: 10px;">商户名称：</span><span class="text-info"><?php  echo $item['merchname'];?></span>
                </td>
            </tr>
            <?php  } ?>
            <?php  } } ?>
       </tbody>
        <tfoot>
        <tr>
            
            <td    <?php  if($goodsfrom!='cycle') { ?>colspan="4"<?php  } else { ?>colspan="3" <?php  } ?>>
                
            </td>
            <td colspan="5" style="text-align: right">
                <?php  echo $pager;?>
            </td>
        </tr>
        </tfoot>
    </table>
    </div>
</div>
  <?php  } else { ?>
    <div class="panel panel-default">
        <div class="panel-body empty-data">暂时没有任何商品!</div>
    </div>
  <?php  } ?>
</div>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>
<script>
    $(document).on("click", '[data-toggle="ajaxEdit2"]',
            function (e) {
                var _this = $(this)
                $(this).addClass('hidden')
                var obj = $(this).parent().find('a'),
                        url = obj.data('href') || obj.attr('href'),
                        data = obj.data('set') || {},
                        html = $.trim(obj.text()),
                        required = obj.data('required') || true,
                        edit = obj.data('edit') || 'input';
                var oldval = $.trim($(this).text());
                e.preventDefault();

                submit = function () {
                    e.preventDefault();
                    var val = $.trim(input.val());
                    if (required) {
                        if (val == '') {
                            tip.msgbox.err(tip.lang.empty);
                            return;
                        }
                    }
                    if (val == html) {
                        input.remove(), obj.html(val).show();
                        //obj.closest('tr').find('.icow').css({visibility:'visible'})
                        return;
                    }
                    if (url) {
                        $.post(url, {
                            value: val
                        }, function (ret) {

                            ret = eval("(" + ret + ")");
                            if (ret.status == 1) {
                                obj.html(val).show();

                            } else {
                                tip.msgbox.err(ret.result.message, ret.result.url);
                            }
                            input.remove();
                        }).fail(function () {
                            input.remove(), tip.msgbox.err(tip.lang.exception);
                        });
                    } else {
                        input.remove();
                        obj.html(val).show();
                    }
                    obj.trigger('valueChange', [val, oldval]);
                },
                        obj.hide().html('<i class="fa fa-spinner fa-spin"></i>');
                var input = $('<input type="text" class="form-control input-sm" style="width: 80%;display: inline;" />');
                if (edit == 'textarea') {
                    input = $('<textarea type="text" class="form-control" style="resize:none;" rows=3 width="100%" ></textarea>');
                }
                obj.after(input);

                input.val(html).select().blur(function () {
                    submit(input);
                    _this.removeClass('hidden')

                }).keypress(function (e) {
                    if (e.which == 13) {
                        submit(input);
                        _this.removeClass('hidden')
                    }
                });

            })

    $(".movecolor").mouseover(function(){
    	$(this).css('background','#e7e7e7');
    })
    $(".movecolor").mouseout(function(){
    	$(this).css('background','white');
    })
</script>

<!--www-efwww-com-->