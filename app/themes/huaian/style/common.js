var mySwiper = new Swiper ('.swiper-container', {
// 如果需要分页器
pagination: '.swiper-pagination',
    touchRatio : 0.8,
	lazyLoading : true,
    onInit: function(swiper){
    	arrowShow(swiper);
  	},
	onSlideChangeEnd: function(swiper){
		arrowShow(swiper);
    }
});       
function skipTo(num){
	mySwiper.slideTo(num);
}
function arrowShow(swiper){
	if(swiper.isEnd){
    	$(".arrow").hide();
	}else{
		$(".arrow").show();
	}
}
function menuShow(){
	$(".menu_bar").fadeToggle("slow");
}