var qr = document.getElementById("qr").src;
var poster = document.getElementById("poster").src;
drawAndShareImage();
function drawAndShareImage() {
    var canvas = document.createElement("canvas");
    canvas.width = document.body.clientWidth; //获取父级盒子的w
    canvas.height = document.body.clientHeight; //获取父级盒子的H
    var context = canvas.getContext("2d");
    var base64 = "";
    context.rect(0, 0, canvas.width, canvas.height);
    var myImage2 = new Image();
    myImage2.setAttribute('crossOrigin', 'anonymous');
    myImage2.src = poster + '?time=' + new Date();
    myImage2.onload = function () {
        context.drawImage(myImage2, 0, 0, canvas.width, canvas.height);
        var myImage3 = new Image();
        myImage3.setAttribute('crossOrigin', 'anonymous');
        myImage3.src = qr + '?time=' + new Date();
        myImage3.onload = function () {
            var img3H=canvas.height*0.7;
            var img3L=(canvas.width-130)/2;
            // console.log("H==>",img3H,"===>W:",img3L);
            // context.drawImage(myImage3,50,50, img3W,img3H); //(x,y,w,h)
            context.drawImage(myImage3, img3L,img3H, 130, 130); //(x,y,w,h)
            base64 = canvas.toDataURL("image/png");
            var img = document.getElementById('agent');
            img.setAttribute('src', base64);
        }
    }
}