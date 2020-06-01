<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>

<div class="page-header">当前位置：<span class="text-primary">快捷导航管理</span></div>

<div class="page-content">
    <div class="alert alert-primary">提示：您可在此页面可对快捷导航进行编辑、删除、推动排序(导航名称最多四个字符)。</div>

    <?php  if(empty($list)) { ?>
        <div class="panel panel-default">
            <div class="panel-body empty-data">您还未添加快捷导航，请至页面顶部快捷导航 -> 自定义快捷导航添加</div>
        </div>
    <?php  } else { ?>

    <form action="" method="post" class="form-validate" novalidate="novalidate">
        <input name="datas" type="hidden" id="datas" />
        <table class="table">
            <thead>
            <tr>
                <th style="width:60px;"></th>
                <th style="width: 200px;">导航名称</th>
                <th style="width: 100px;">导航颜色</th>
                <th style="width: 180px;">加粗显示</th>
                <th>导航链接</th>
                <th style="width: 60px;">操作</th>
            </tr></thead>
            <tbody id="tbody" class="ui-sortable">
            <?php  if(is_array($list)) { foreach($list as $index => $item) { ?>
                <tr>
                    <td>
                        <a href="javascript:;" class="btn btn-default btn-sm btn-move"><i class="fa fa-arrows"></i></a>
                    </td>
                    <td>
                        <input type="text" class="form-control funbar-text" name="text[]" value="<?php  echo $item['text'];?>" maxlength="4" placeholder="最多四个字符" />
                    </td>
                    <td>
                        <input type="color" value="<?php echo !empty($item['color'])?$item['color']:'#666666'?>" class="form-control funbar-color" name="color[]" style="padding: 0; width: 80px;" />
                    </td>
                    <td style="padding-top: 3px;">
                        <label class="radio-inline"><input class="funbar-bold-1" type="radio" name="bold_<?php  echo $index;?>[]" <?php  if(!empty($item['bold'])) { ?>checked="checked"<?php  } ?> />加粗</label>
                        <label class="radio-inline"><input class="funbar-bold-0" type="radio" name="bold_<?php  echo $index;?>[]" <?php  if(empty($item['bold'])) { ?>checked="checked"<?php  } ?> />不加粗</label>
                    </td>
                    <td>
                        <input type="text" value="<?php  echo $item['href'];?>" class="form-control funbar-href" name="href[]" readonly />
                    </td>
                    <td>
                        <span class="btn btn-danger  btn-sm btn-remove"><i class="fa fa-remove"></i></span>
                    </td>
                </tr>
            <?php  } } ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="6">
                        <input type="submit" class="btn btn-primary" value="保存">
                    </td>
                </tr>
            </tfoot>
        </table>
    </form>

    <script type="text/javascript">
        $(".btn-remove").click(function () {
            var _this = $(this);
            tip.confirm("确定删除吗？删除后保存才生效", function () {
                _this.closest('tr').remove();
            });
        });
        require(['jquery.ui'] ,function(){
            $("#tbody").sortable({handle: '.btn-move'});
        });
        $("form").submit(function () {
            var form = $(this);
            var obj = [];
            $("#tbody tr").each(function (index, item) {
                var text = $.trim($(this).find('.funbar-text').val());
                if(text==''){
                    tip.msgbox.err('请填写导航名称');
                    $(this).find('.funbar-text').focus();
                    form.attr('stop', 1);
                    return false;
                }
                var color =  $(this).find('.funbar-color').val();
                var bold =  $(this).find('.funbar-bold-1').is(':checked')?1:0;
                var href =  $.trim($(this).find('.funbar-href').val());
                obj.push({href:href, text: text, color: color, bold: bold});
            });
            $("#datas").val(JSON.stringify(obj));
        });
    </script>

    <?php  } ?>
</div>
 
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>     
<!--efwww_com-->