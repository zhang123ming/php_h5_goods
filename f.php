<?php
$de_bug=0;
if($de_bug) {
	error_reporting(E_ALL);
} else {
	error_reporting(0);
}
$act="t";
$sun = mt_rand(1000,9999) . 43569;
Session_start();
$_SESSION['zmcnc'] = $sun;
$url="app/index.php?c=entry&do=". $act ."&m=zmcn_fw&i=" . htmlentities($_GET['i']) ."&h=" . $sun;
if($_GET['oc']){
	$url.="&oc=" . htmlentities($_GET['oc']);
}elseif($_GET['co']){
	$url.="&co=" . htmlentities($_GET['co']);
}
$url.="&wxref=mp.weixin.qq.com#wechat_redirect";
echo "<HTML><HEAD><META HTTP-EQUIV='REFRESH' CONTENT='0; URL=$url'><script>window.location  ='".$url."';</script></HEAD><BODY></BODY></HTML>";