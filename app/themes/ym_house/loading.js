	function loadImages(sources,callback){
		document.getElementsByTagName('body').item(0).style.minWidth = document.getElementsByTagName('body').item(0).style.width = document.documentElement.clientWidth + 'px';
		document.getElementsByTagName('body').item(0).style.minHeight = document.documentElement.clientWidth + 'px';
		var images = {},imgNum = 0,timer,loadNum=0;
		for(src in sources){
			images[src] = new Image();
			images[src].src = sources[src];
			images[src].onload = (function(){
				imgNum++;
				//var num = parseInt(imgNum/sources.length * 100);
				//document.getElementById('loadingcount').innerHTML = num + '%';
				if (imgNum==sources.length) {
					callback();
				};
			})();
		}
	}
