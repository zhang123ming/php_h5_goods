function shareShow(){
	layer.open({
		type: 2,
		title: false,
		closeBtn: false,
		shadeClose: true,
		area: ['100%', '270px'],
		offset: 'rb',
		shift: 2,
		content: 'sharebox.asp'
	});
}
function shareClose(){
	layer.closeAll();
}
/*
var shareContent = "<div class='sharebox'><ul class='video_list'>";
shareContent += "<li><a href='javascript:void(0);' title='分享到微信朋友圈'><img src='images/share/weixinpengyou_popover.png'/><div class='video_list_explain'>微信朋友圈</div></a></li>";
shareContent += "<li><a href='javascript:void(0);' title='分享到微信好友'><img src='images/share/weixin_popover.png'/><div class='video_list_explain'>微信好友</div></a></li>";
shareContent += "<li><a href='javascript:void(0);' title='分享到QQ好友'><img src='images/share/qq_popover.png'/><div class='video_list_explain'>QQ好友</div></a></li>";
shareContent += "<li><a href='javascript:void(0);' title='分享到QQ空間'><img src='images/share/qzone_popover.png'/><div class='video_list_explain'>QQ空間</div></a></li>";
shareContent += "<li><a href='javascript:void(0);' title='分享到騰訊微博'><img src='images/share/tencent_popover.png'/><div class='video_list_explain'>騰訊微博</div></a></li>";
shareContent += "<li><a href='javascript:void(0);' title='分享到短信'><img src='images/share/message_popover.png'/><div class='video_list_explain'>短信</div></a></li>";
shareContent += "</ul><div class='cancelbar'><div><a href='javascript:void(0);' onclick='shareClose();return false;'>取消分享</a></div></div></div>";
document.write(shareContent);
*/