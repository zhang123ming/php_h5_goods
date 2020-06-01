contentWidth = typeof(contentWidth)!='undefined' ? contentWidth : 1242;
minScale = typeof(minScale)!='undefined' ? minScale : 0.25;
maxScale = typeof(maxScale)!='undefined' ? maxScale : 4.0;
userScale = typeof(userScale)!='undefined' ? userScale : 1;

var screenWidth =  parseInt(window.screen.width);
var scale = screenWidth/contentWidth;
if(userScale)
{
	minScale = scale;
	maxScale = scale;
}
var ua = navigator.userAgent;

if (/Android (\d+\.\d+)/.test(ua)){
	var version = parseFloat(RegExp.$1);
	// andriod 2.3
	
	if(version>2.3){
		viewportContent = 'width='+contentWidth+', initial-scale='+scale+', minimum-scale = '+minScale+', maximum-scale = '+maxScale+', target-densitydpi=device-dpi';
		// andriod 2.3
		function orientationChange()
		{
			setTimeout(function(){
				screenWidth = parseInt(window.screen.width);
				scale = screenWidth/contentWidth;
				if(userScale)
				{
					minScale = scale;
					maxScale = scale;
				}
				document.querySelector("meta[name=viewport]").setAttribute('content','width='+contentWidth+',initial-scale='+scale+', minimum-scale='+minScale+', maximum-scale='+maxScale+', target-densitydpi=device-dpi');
			},500);
		}
		window.onorientationchange = orientationChange;
	}else{
		viewportContent = 'width='+contentWidth+', target-densitydpi=device-dpi,user-scalable=no';
	}
} else {
	viewportContent = 'width='+contentWidth+', target-densitydpi=device-dpi,user-scalable=no';
}
document.write('<meta name="viewport" content="'+viewportContent+'">');

if(/ipad|iphone|ipod/.test(navigator.userAgent.toLowerCase()))
{	
	if(!document.querySelector("meta[name=apple-mobile-web-app-capable]"))
	{
		appleCapable = document.createElement('meta');
		appleCapable.setAttribute('name','apple-mobile-web-app-capable');
		appleCapable.setAttribute('content','yes');
		document.getElementsByTagName('head')[0].appendChild(appleCapable);
	}else{
		document.querySelector("meta[name=apple-mobile-web-app-capable]").setAttribute('content','yes');
	}
	
	if(!document.querySelector("meta[name=apple-touch-fullscreen]"))
	{
		appleTouchFullScreen = document.createElement('meta');
		appleTouchFullScreen.setAttribute('name','apple-touch-fullscreen');
		appleTouchFullScreen.setAttribute('content','yes');
		document.getElementsByTagName('head')[0].appendChild(appleTouchFullScreen);
	}else{
		document.querySelector("meta[name=apple-touch-fullscreen]").setAttribute('content','yes');
	}
}
