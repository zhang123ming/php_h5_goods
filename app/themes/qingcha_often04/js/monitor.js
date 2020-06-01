/*!
 * blue_tracker Javascript Library
 * version - v1.0.1 (2015-06-05)
 * author huangtao 330177233@qq.com
 */
 function aB(){	 
	        this.getletter=function(num){
				if (num < 10) { 
					return num; 
				} 
				else { 
					if (num == 10) { return "A" } 
					if (num == 11) { return "B" } 
					if (num == 12) { return "C" } 
					if (num == 13) { return "D" } 
					if (num == 14) { return "E" } 
					if (num == 15) { return "F" } 
				}				
			}
			this.UrlEncode=function(s) 
			{ 
				var hex=''    
				var i,j,t 
					
				j=0 
				for (i=0; i<s.length; i++) 
				{ 				
					if (s.charCodeAt(i) > 65535) { return ("err!") } 
					first = Math.round(s.charCodeAt(i)/4096 - .5); 
					temp1 = s.charCodeAt(i) - first * 4096; 
					second = Math.round(temp1/256 -.5); 
					temp2 = temp1 - second * 256; 
					third = Math.round(temp2/16 - .5); 
					fourth = temp2 - third * 16;   					
					t = (""+this.getletter(third)+this.getletter(fourth));				
					if (t=='25') 
					{ 
						 t=''; 
					 } 
					  hex += '%' + t; 
				} 
				return hex; 
			} 		 
		 this.baseurl = 'http://tracking.blue-dot.cn/minitor/nosql.php'; //监测PHP文件所在的服务器路径
		 //this.urlstring = this.UrlEncode(window.location.href);
		 this.urlstring = encodeURIComponent(window.location.href);
		 this.version = 'v1';
		 this.imgAdd = function(){
			 if(!this.img)
			 {
				var img = new Image(),
					id = 'img' + new Date();
					img.id = id;
					img.onload = img.onerror = img.onabort = function() { window[id] = undefined; };
					window[id] = img;	
					this.img  = img;
			 }
		 }
		 this.init = function(){
			 /*
			 * 初始化浏览数据
			 * 参数 prjid,openid,unionid
			 */
			 this.keywords = '';
			 this.imgAdd();
			 this.prjid = this.data.prjid;
			 this.openid = this.data.openid;
			 this.unionid = this.data.unionid;
			 this.otherid = this.data.otherid;
			 //this.referrer = this.UrlEncode(document.referrer);
			 this.referrer = encodeURIComponent(document.referrer);
			 this.tag = encodeURIComponent(this.data.tag);
			 this.readTitle = encodeURIComponent(document.title);
			 
				for(var i in document.getElementsByTagName("meta"))
				{  
				  if(document.getElementsByTagName("meta")[i].name&&document.getElementsByTagName("meta")[i].name.toLowerCase()=='keywords')
				  {
					 this.keywords = encodeURIComponent(document.getElementsByTagName("meta")[i].content);
				  }
				  if(document.getElementsByTagName("meta")[i].name&&document.getElementsByTagName("meta")[i].name.toLowerCase()=='description')
				  {
	
						 this.description = encodeURIComponent(document.getElementsByTagName("meta")[i].content);
					  
					    if( this.description == 'undefined' ){
							this.description = '';
						}
					  
				  }
				} 			 
			 
			 var url=this.baseurl+"?";
				url+="prjid="+this.prjid+"&user="+this.openid+'&unionid='+this.unionid;
				url+="&urlstring=" + this.urlstring+'&referrer='+this.referrer+'&version='+this.version+'&tag='+this.tag+'&otherid='+this.otherid+'&readtitle='+this.readTitle+'&keywords='+this.keywords+'&description='+this.description;		 
			 this.img.src = url;
         /** 遍历DOM绑定有 的onclick事件 **/	
			var tags = document.getElementsByTagName ('*');
			for ( var i = 0; i < tags.length; i++)
			{
			  var index = 0;
			  var dom = tags[i];
			  while (dom.children.length && index < dom.children.length)
			  {
				if(dom.children[index].getAttribute('monitor'))
				{
				  var button_monitor = dom.children[index].getAttribute('monitor');
				  dom.children[index].addEventListener('click',(function(button_monitor){
				//	e.preventDefault();
					 //添加监听事件
					 return function(){blue_tracker_monitor('eventlog',{
						  'eventkey':encodeURIComponent(button_monitor)                
					 });}
				  })(button_monitor),false);
				}
				index++;
			  }
			}  		 
						 
		}
		this.eventlog = function(){
			 /*
			 * 用户浏览事件监测
			 * 参数 eventKey
			 */		
			this.eventkey = this.data.eventkey; 
			if(!this.tag) this.tag = encodeURIComponent(this.data.tag);
			var url=this.baseurl+"?a=eventlog&";
				url+="prjid="+this.prjid+"&user="+this.openid+'&unionid='+this.unionid;
				url+="&eventkey="+this.eventkey+"&urlstring=" +this.urlstring+'&referrer='+this.referrer+'&version='+this.version+'&tag='+this.tag+'&otherid='+this.otherid+'&readtitle='+this.readTitle;
				this.imgAdd();
				this.img.src = url;
		}
		this.sharelog = function(){
			 /*
			 * 用户分享事件监测
			 * 参数 shareurl,shareType
			 */	
			this.sharetype = this.data.sharetype; 
			this.shareurl = encodeURIComponent(this.data.shareurl); 
			this.sharekey = this.data.sharekey;
			if(!this.tag) this.tag = encodeURIComponent(this.data.tag);
			var url=this.baseurl+"?a=eventlog&";
				url+="prjid="+this.prjid+"&user="+this.openid+'&unionid='+this.unionid;
				url+="&shareurl="+this.shareurl+"&urlstring=" +this.urlstring+'&referrer='+this.referrer+'&sharetype='+this.sharetype+'&version='+this.version+'&tag='+this.tag+'&sharekey='+this.sharekey+'&otherid='+this.otherid;
				this.imgAdd();
				this.img.src = url;			 
		}
	 }
 function aG(action,obj)
 {
	 if(!blue_tracker)
	 blue_tracker = new aB;
 
	 if( typeof( obj.openid ) == 'undefined' ) obj.openid = '';
	 if( typeof( obj.unionid ) == 'undefined' ) obj.unionid = '';
	 if( typeof( obj.prjid ) == 'undefined' ) obj.prjid = '';
	 if( typeof( obj.otherid ) == 'undefined' ) obj.otherid = '';
	 if( typeof( obj.tag ) == 'undefined' ) obj.tag = '';
	 
	 blue_tracker.data = obj;
	 return ((typeof(action) == 'string' && typeof(obj) == 'object' && typeof(blue_tracker[action]) == 'function') && blue_tracker[action]()) || blue_tracker;
 }
 var blue_tracker;
 var blue_tracker_monitor = aG;


/**
   以下为分享API封装

var monitorJS = document.createElement("script");
monitorJS.src = 'http://res.wx.qq.com/open/js/jweixin-1.0.0.js';
monitorJS.type = 'text/javascript';
document.getElementsByTagName('head')[0].appendChild(monitorJS);

function wechatShareCall(configAry)
{
	   if(typeof configAry!="object") return false;
	   var configData = configAry;
        //微信分享
        wx.config({
            //debug: true,
            appId: configData.appId,
            timestamp: configData.timestamp,
            nonceStr: configData.nonceStr,
            signature: configData.signature,
            jsApiList: [
                "onMenuShareTimeline", "onMenuShareAppMessage"
            ]
        });
        wx.error(function(res) {
            for (i in res) {
                alert(i);
                alert(res[i]);
            }
        });
        wx.ready(function() {
            // 在这里调用 API
            wx.onMenuShareTimeline({
                title: configData.shareTitle, // 分享标题
                link: configData.shareurl, // 分享链接
                imgUrl: configData.imgUrl, // 分享图标
                //imgUrl:'http://test.bluewebonline.com/nba_cny/view/templates/images/shareImg/cards_share.jpg',
                success: function() {
                    // 用户确认分享后执行的回调函数
                    //alert('分享成功！');
                    blue_tracker_monitor("sharelog", {'shareurl':configData.shareurl,'sharekey':'1','sharetype':'Timeline'});
                },
                cancel: function() {
                    // 用户取消分享后执行的回调函数
                    //alert(2);
                }
            });

            wx.onMenuShareAppMessage({
                title: configData.shareTitle, // 分享标题
                desc: configData.sharecont,
                link: configData.shareurl, // 分享链接
                imgUrl: configData.imgUrl, // 分享图标
                //imgUrl:'http://test.bluewebonline.com/nba_cny/view/templates/images/shareImg/cards_share.jpg',
                type: '', // 分享类型,music、video或link，不填默认为link
                dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
                success: function() {
                    // 用户确认分享后执行的回调函数
                    //alert('分享成功！！');
                    blue_tracker_monitor("sharelog", {'shareurl':configData.shareurl,'sharekey':'2','sharetype':'AppMessage'});
                },
                cancel: function() {
                    // 用户取消分享后执行的回调函数
                    // alert(2);
                }
            });

        });
}
**/
