<?php defined('IN_IA') or exit('Access Denied');?><?php  if(is_array($list)) { foreach($list as $common) { ?>
<div id="t" class="ncs-commend-floor">
    <div class="user-avatar">
        <a href="#" target="_blank">
            <img src="<?php  echo $common['headimgurl'];?>">
        </a>
    </div>
    <dl class="detail">
        <dt>
            <span class="user-name">
                <a href="#" target="_blank"><?php  echo $common['nickname'];?></a>
            </span>
            <time pubdate="pubdate">[<?php  echo $common['createtime'];?>]</time>
        </dt>
        <dd>用户评分：<span class="raty" data-score="<?php  echo $common['level'];?>"></span></dd>
        <dd class="content">评价详情：<span><?php  echo $common['content'];?></span></dd>
        <?php  if(!empty($common['images'])) { ?>
        <dd>
            晒单图片：
            <ul class="photos-thumb">
                <?php  if(is_array($common['images'])) { foreach($common['images'] as $image) { ?>
                <li><a nctype="nyroModal"
                       href="<?php  echo $image;?>">
                        <img src="<?php  echo $image;?>">
                    </a>
                </li>
                <?php  } } ?>
            </ul>
        </dd>
        <?php  } ?>
    </dl>
</div>
<?php  } } ?>