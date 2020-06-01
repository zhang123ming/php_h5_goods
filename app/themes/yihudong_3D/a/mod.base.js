/*@亚丁设计 wx.yihudong.cn */
//隐藏地址栏


//COOKIE的操作 - 写
function SetCookie(name,val,years){
    var d=years*365*24*60*60*1000 || 365;
    var D=new Date();
    D.setTime(D.getTime+d);
    document.cookie=name+'='+escape(val)+';expires='+D.toGMTString();
}
function GetCookie(name){
    var arr,reg=new RegExp('(^| )'+name+'=([^;]*)(;|$)');
    if(arr=document.cookie.match(reg)){
        return arr[2];
    }else{
        return null;
    }
}
//是否第一次访问
// if(GetCookie('WIT_Visit')!=1){
// 	SetCookie('WIT_Visit',1);
// 	window.location.href='/';
// }
//Zepto
Zepto(function($){
	//图片懒加载
	if($('.IMG').length>0){
		$('.IMG').imglazyload();
	}



	//首页导航交互
	var homeNav=$('.home-nav li');
	// var homeFoot=$('.home-foot');
	setTimeout(function(){
		homeNav.eq(1).show();
	},300);
	setTimeout(function(){
		homeNav.eq(2).show();
	},500);
	setTimeout(function(){
		homeNav.eq(3).show();
	},600);
	setTimeout(function(){
		homeNav.filter('.link').show();
	},800);
	//通用导航显示隐藏
	var baseNav=$('.base-nav-menu');
	baseNav.click(function(){
		var span = $(this).find('span');
		if(span.attr('class') == 'open'){
		        span.removeClass('open');
		        span.addClass('close');
		        $('.base-nav-btn').removeClass('open');
		        $('.base-nav-btn').addClass('close');
		}else{
		        span.removeClass('close');
		        span.addClass('open');
		        $('.base-nav-btn').removeClass('close');
		        $('.base-nav-btn').addClass('open');
		}
	});
	baseNav.on('touchmove',function(event){event.preventDefault();});
	



	//微信首页展开关闭
	var n1dt=$('.n1-menu .dt');
	var n1ddbox=$('.n1-menu .ddbox');
	var n1arrow=$('#J_n1Main .arrow');
	n1dt.each(function(i){
		$(this).click(function(){
			var box=n1ddbox.eq(i);
			var dds=box.find('.dd').length;
			var arrow=$(this).find('.arrow');
			console.log(arrow)
			if(box.height()==0){
				box.animate({height:dds*47+1+'px'},120,'ease-in');
				arrow.addClass('arrow-up');
			}else{
				box.animate({height:0},120,'ease-out');
				arrow.removeClass('arrow-up');
			}
			
		});
	});
});