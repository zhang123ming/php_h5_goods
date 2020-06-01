var box_user_img = document.getElementById("box_user_img"); //user logo
var box_header_title = document.getElementById("box_header_title"); //user end title
var box_section_img = document.getElementById("box_section_img"); //goods pic
var box_footer_qrcode = document.getElementById("box_footer_qrcode"); //qrcode
var box_footer_title = document.getElementById("box_footer_title"); //footer title
var box_footer_price = document.getElementById("box_footer_price"); //price 
drawAndShareImage();
function drawAndShareImage() {
	var canvas = document.createElement("canvas");
	canvas.width = document.body.clientWidth; //获取父级盒子的w
	canvas.height = document.body.clientHeight; //获取父级盒子的H
	// console.log("宽度：",canvas.width,"===高度：",canvas.height);
	canvas.background = "#ff0000";
	var context = canvas.getContext("2d");
	var base64 = "";
	context.rect(0, 0, canvas.width, canvas.height);
	context.fillStyle = "#fff"; //设置背景颜色
	context.fill();
	context.fillStyle = "#000000";
	// title
	var titleW=canvas.width*0.7;
	// console.log("title  width===",titleW);
	drawText(context, box_header_title.innerHTML, 90, 20,titleW);
	context.fillStyle = "#red";
var  secTitleH=canvas.height*0.7;
var secTitleW=canvas.width*0.5;
// console.log("secH:",secTitleH,"===secW:",secTitleW);
	drawText(context, box_footer_title.innerHTML, 145, secTitleH, secTitleW);
	context.fillStyle = "#FF0000";
	context.font = "25px  Courier New";
	var priceH=canvas.height*0.82;
	// console.log("priceH::",priceH);
	context.fillText(box_footer_price.innerHTML, 145, priceH);
	context.fillStyle = "#999999";
	context.font = "18px  Courier New";
// 底部字体
	var textBottom=canvas.height*0.92;
	// console.log("字体底部===",textBottom);
var textLeft=canvas.width/2-100;
// console.log("宽度",textLeft);
	context.fillText("长按二维码保存图片",textLeft, textBottom);
	var myImage2 = new Image();
	myImage2.setAttribute('crossOrigin', 'anonymous');
	myImage2.src = box_user_img.src +'?time=' + new Date();
              	myImage2.onload = function () {
		context.drawImage(myImage2, 20, 20, 60, 60); //(x,y,w,h)
		var myImage3 = new Image();
		myImage3.setAttribute('crossOrigin', 'anonymous');
		myImage3.src = box_section_img.src+'?time=' + new Date();
               	myImage3.onload = function () {
               		var picH=canvas.height*0.49;
               		// console.log("picH",picH);
			context.drawImage(myImage3, 0, 100, canvas.width, picH);
			var myImage4 = new Image();
			myImage4.setAttribute('crossOrigin', 'anonymous');
			myImage4.src = box_footer_qrcode.src+'?time=' + new Date();
			myImage4.onload = function () {
				var qrcodeH=canvas.height*0.7;
				// console.log("二维码高度",qrcodeH);
				context.drawImage(myImage4, 20, qrcodeH, 120, 120);
				base64 = canvas.toDataURL("image/png");
				var img = document.getElementById('avatar');
				img.setAttribute('src', base64);
			}
		}
	}
}
// 字体换行
function drawText(context, t, x, y, w) {
	var chr = t.split("");
	var temp = "";
	var row = [];
	context.font = "15px Arial";
	context.fillStyle = "black";
	context.textBaseline = "middle";
	for (var a = 0; a < chr.length; a++) {
		if (context.measureText(temp).width < w && context.measureText(temp + (chr[a])).width <= w) {
			temp += chr[a];
		} else {
			row.push(temp);
			temp = chr[a];
		}
	}
	row.push(temp);
	for (var b = 0; b < 2; b++) {
		var str = row[b];
		if (b == 1) {
			str = str.substring(0, str.length - 1) + '...';
		}
		context.fillText(str, x, y + (b + 1) * 20);
	}
}

function getBase64Image(img) {
	var canvas = document.createElement("canvas");
	canvas.width = img.width;
	canvas.height = img.height;
	var ctx = canvas.getContext("2d");
	ctx.drawImage(img, 0, 0, img.width, img.height);
	var dataURL = canvas.toDataURL("image/png");
	console.log("dataURL====>>>", dataURL);
	return dataURL
}


function main(url) {
	var img = document.createElement('img');
	img.setAttribute("crossOrigin", "Anonymous")
	img.src = url; //此处自己替换本地图片的地址
	img.onload = function () {
		var data = getBase64Image(img);
		var img1 = document.createElement('img');
		img1.src = data;
		document.body.appendChild(img1);
		console.log("data====>", data);
		return data;
	}
}