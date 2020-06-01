var js=document.scripts;
jsPath=js[js.length-1].src.substring(0,js[js.length-1].src.lastIndexOf("/")+1);
$('head').append("<link href='"+jsPath+"css/style.css' rel='stylesheet' type='text/css' />");
(function($){
	$.fn.popmenu = function(options){
		$(this).empty();
		var strHtml = "<div class='top_bar'><nav><ul id='top_menu' class='top_menu'><input type='checkbox' id='plug-btn' class='plug-menu'/></ul></nav></div>";
		var strData = [];
		var settings = {
			datas: strData
		};
		var obj = $.extend(settings, options);
		return this.each(function(){
			$(this).html(strHtml);
			var menuItem = "";
			$.each(obj.datas, function (idx, item){
				menuItem += "<li class='out'><a href='"+item.url+"'><img src='"+item.img+"'><label>"+item.name+"</label></a></li>";
			});
			$('.top_menu').append(menuItem);
			$(".plug-menu").click(function(){
		        var li = $(this).parents('ul').find('li');
		        if(li.attr("class") == "on"){
	                li.removeClass("on");
	                li.addClass("out");
		        }else{
	                li.removeClass("out");
	                li.addClass("on");
		        }
	        });
		});
	};
})(jQuery);