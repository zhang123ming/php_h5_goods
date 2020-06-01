<?php defined('IN_IA') or exit('Access Denied');?><div id="faq">
    <div class="wrapper">
        <ul>
            <?php  $categories = get_qa();$i=1?>
            <?php  if(is_array($categories)) { foreach($categories as $cate) { ?>
            <li>
                <dl class="s<?php  echo $i++ ?>">
                    <dt><?php  echo $cate['name'];?></dt>
                    <?php  if(is_array($cate['list'])) { foreach($cate['list'] as $qa) { ?>
                    <dd><a href="<?php  echo mobileUrl('pc.qa.qa.detail',array('mk'=>'detail','id'=>$qa['id'])) ?>" title="<?php  echo $qa['title'];?>"> <?php  echo $qa['title'];?> </a></dd>
                    <?php  } } ?>
                </dl>
            </li>
            <?php  } } ?>
            <span style="float: right;">
            <?php  $set = get_set()?>
            <li class="kefu-con">
                <dl>
                    <dt>联系我们</dt>
                    <dd>
                        <i class="icon-tel"></i>
                        <div style="height:auto;">
                            <strong><?php  echo $set['tel'];?></strong>
                            <p>08:00-22:30(周一至周日)</p>
                        </div>
                    </dd>
                    <dd>
                        <i class="icon-chat"></i>
                        <div style="height:auto;">
                            <strong>在线客服</strong>
                            <p><?php  echo $set['kefu'];?></p>
                        </div>
                    </dd>
                    <dl>
                    </dl>
                </dl>
            </li>
            <li class="box-qr">
                <dl>
                    <dt>关注我们<a title="" class="weixin">
                            <span class="weixin-qr"></span>
                        </a>
                    </dt>
                    <dd>
                        <p><img src="<?php  echo tomedia($set['pc_follow'])?>"></p>
                        <p>关注微信公众号</p>
                    </dd>
                </dl>
            </li>
            </span>
        </ul>
    </div>
</div>
<div id="footer" class="wrapper">
    <p><?php  echo get_menus(1)?></p>
    <br/>
    <?php  show_copyright()?>
    <a href="https://m.kuaidi100.com/" target="_blank">快递查询</a>
</div>
<script type="text/javascript" src="../addons/ewei_shopv2/plugin/pc/template/mobile/default/static/js/jquery.cookie.js"></script>

<!-- 对比 -->
<script type="text/javascript">
    $(function () {
        // Membership card
        $('[nctype="mcard"]').membershipCard({type:'shop'});
    });
    function fade() {
        $("img[rel='lazy']").each(function () {
            var $scroTop = $(this).offset();
            if ($scroTop.top <= $(window).scrollTop() + $(window).height()) {
                $(this).hide();
                $(this).attr("src", $(this).attr("data-url"));
                $(this).removeAttr("rel");
                $(this).removeAttr("name");
                $(this).fadeIn(500);
            }
        });
    }
    if ($("img[rel='lazy']").length > 0) {
        $(window).scroll(function () {
            fade();
        });
    }
    ;
    fade();
</script>
<script>var imgs = document.getElementsByTagName('img');for(var i=0, len=imgs.length; i < len; i++){imgs[i].onerror = function() {if (!this.getAttribute('check-src') && (this.src.indexOf('http://') > -1 || this.src.indexOf('https://') > -1)) {this.src = this.src.indexOf('http://yeshi.kaifa1688.cn/attachment/') == -1 ? this.src.replace('http://yeshiyiyao.oss-cn-beijing.aliyuncs.com/', 'http://yeshi.kaifa1688.cn/attachment/') : this.src.replace('http://yeshi.kaifa1688.cn/attachment/', 'http://yeshiyiyao.oss-cn-beijing.aliyuncs.com/');this.setAttribute('check-src', true);}}}</script></body>
</html>