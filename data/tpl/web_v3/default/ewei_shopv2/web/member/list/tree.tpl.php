<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>
<style type="text/css">
    * {
        padding: 0;
        margin: 0;
        font-family: "microsoft yahei";
    }

    ul li {
        list-style-type: none;
    }

    .box {
        width: 200px;
        /*border: 1px solid red;*/
    }

    .innerUl ul {
        margin-left: 40px;
        /*border: 1px solid blue;*/
    }

    .menuUl li {
        margin: 10px 0;
    }

    .menuUl li span:hover {
        text-decoration: underline;
        cursor: pointer;
    }

    .menuUl li i { margin-right: 10px; top: 0px; cursor: pointer; color: #161616;font-size: 28px;}
    .menuUl li span{ color: #161616;font-size: 18px;}
</style>
<div class="page-header">当前位置：<span class="text-primary">会员结构树</span></div>
<div class="page-content">
	<form action="./index.php" method="get" class="form-horizontal table-search" role="form">
        <input type="hidden" name="c" value="site"/>
        <input type="hidden" name="a" value="entry"/>
        <input type="hidden" name="m" value="ewei_shopv2"/>
        <input type="hidden" name="do" value="web"/>
        <input type="hidden" name="r" value="member.list.tree"/>
 
        <!-- <?php if(cv('member.list.achievement')) { ?>
        <a class="btn btn-primary btn-sm" href="<?php  echo webUrl('member/achievement/list')?>"><i class="fa fa-plus"></i> 会员业绩</a>
        <?php  } ?> -->
        <div class="page-toolbar" style="margin-top: 20px">
            <div class="input-group">

                <input type="text" class="form-control " name="keyword" value="<?php  echo $_GPC['keyword'];?>" placeholder="可搜索手机号/ID">
                <span class="input-group-btn">
                    <button class="btn  btn-primary" type="submit"> 搜索</button>
                </span>
            </div>
        </div>
    </form>
     <div class="ibox-content">
	      <div class="innerUl"></div>
	  </div>
</div>
 <script src="<?php  echo EWEI_SHOPV2_LOCAL?>static/js/web/proTree.js"></script>
  <script type="text/javascript">
	 $(function(){
	  // console.log(<?php  echo $list;?>);
	 })
      //后台传入的 标题列表
      var arr = <?php  echo $list;?>;
     // var arr = [{
     //     id: 1,
     //     name: "一级标题",
     //     pid: 0
     // }, {
     //     id: 2,
     //     name: "二级标题",
     //     pid: 0
     // }, {
     //     id: 3,
     //     name: "2.1级标题",
     //     pid: 2
     // }, {
     //     id: 4,
     //     name: "2.2级标题",
     //     pid: 2
     // }, {
     //     id: 5,
     //     name: "1.1级标题",
     //     pid: 1
     // }, {
     //     id: 6,
     //     name: "1.2级标题",
     //     pid: 1
     // }, {
     //     id: 7,
     //     name: "1.21级标题",
     //     pid: 6
     // }, {
     //     id: 8,
     //     name: "三级标题",
     //     pid: 0
     // }, {
     //     id: 9,
     //     name: "1.22级标题",
     //     pid: 6
     // }, {
     //     id: 10,
     //     name: "1.221级标题",
     //     pid: 9
     // }, {
     //     id: 11,
     //     name: "1.2211级标题",
     //     pid: 10
     // }, {
     //     id: 12,
     //     name: "1.2212级标题",
     //     pid: 10
     // }

     // ];
      //标题的图标是集成bootstrap 的图标  更改 请参考bootstrap的字体图标替换自己想要的图标
      $(".innerUl").ProTree({
          arr: arr,
          simIcon: "fa fa-male",//单个标题字体图标 不传默认glyphicon-file
          mouIconOpen: "fa fa-user",//含多个标题的打开字体图标  不传默认glyphicon-folder-open
          mouIconClose:"fa fa-user-plus",//含多个标题的关闭的字体图标  不传默认glyphicon-folder-close
//          callback: function(id,name) {
//              alert("你选择的id是" + id + "，名字是" + name);
//          }

      })
  </script>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>