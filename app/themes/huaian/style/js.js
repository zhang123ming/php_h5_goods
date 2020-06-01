$(function() {
	window.MOUSE_CLICK = 'ontouchstart' in document ? 'touchstart' : 'click';
	window.MOUSE_DOWN = 'ontouchstart' in document ? 'touchstart' : 'mousedown';
	window.MOUSE_UP = 'ontouchend' in document ? 'touchend' : 'mouseup';
	window.MOUSE_MOVE = 'ontouchmove' in document ? 'touchmove' : 'mousemove';
	try {
		var defaultArea = $(".total[name='area']").val().split(",");
	} catch (e) {};
	var area1 = [];

	try {
		var wtop = ($('.info2')[0].offsetTop + 15) + 'px';
		console.log(wtop);
		$('#wrapper').css('top', wtop);
	} catch (e) {};
	
	$('#area li').each(function(i, e) {
		var t = $(this);
		var input = t.find('input').val();
		for (i in defaultArea) {
			if (input == defaultArea[i]) {
				t.find('.mycheck').addClass('on');
				area1.push(t.text());
			}
		}
	});
	if (area1.length != 0) {
		$("#area").prev('dt').text(area1)
	};

	var wtop = $('.tcontent').height() + 'px';
	$('#wrapper').css('top', wtop);

	$(document).on(MOUSE_CLICK, '.onlyone dt', function() {
		$(this).toggleClass("on");
		if ($(this).is('.on')) {
			$(this).siblings("dt").removeClass("on");
			$(this).next().slideDown().siblings('dd').slideUp();
		} else {
			$(this).next().slideUp().siblings('dd').slideUp();
		}
	});

	/*	var h=$('.onlyone dd:first').height();
		if (h>100) {
			$('.onlyone dd:first ul').height(104);
			$('.onlyone dd:first ul').after('<div class="more1">更多</div>')
		};*/
	$('.onlyone dd:first ul li:gt(15)').hide();
	$('.onlyone dd:first ul li').eq(15).after('<div class="more1">更多</div>');

	$(document).on(MOUSE_CLICK, '.onlyone dd:first .more1', function() {
		$(this).remove();
		$('.onlyone dd:first ul li:gt(15)').show();
	})
	$(document).on('click', '.onlyone dd li', function() {
		var b = $(this).find('input')[0];
		var num = $(this).parent('ul').find('.mycheck.on').size();
		if ($(this).parent('ul').data('choose') == 2) {
			if (num < 2) {
				$(this).find('.mycheck').toggleClass('on');
				b.checked = !b.checked;
			} else if ($(this).find('.mycheck').is('.on')) {
				$(this).find('.mycheck').removeClass('on');
				b.checked = false;
			}
			var area = [];
			var total = [];
			$(this).parent('ul').find('.mycheck.on').each(function(i, e) {
				area.push($(this).parent('li').text());
				total.push($(this).next('input').val());
			});
			$(this).parents('dd').prev('dt').text(area);
			$(this).parents('dd').find('.total').val(total);
		} else {
			$(this).siblings().find('.mycheck').removeClass('on');
			$(this).find('.mycheck').toggleClass('on');
			var a = $(this).find('input')[0];
			a.checked = !a.checked;
			$(this).parents('dd').prev('dt').text($(this).text());
			$(this).parents('dd').find('.total').val($(this).find('input').val());
			num = $(this).parent('ul').find('.mycheck.on').size();
			if (num == 0) {
				$(this).parents('dd').prev('dt').text('');
			}
		}
	})
	$(document).on('click', '.faq dt', function() {
		if (!$(this).is('.one')) {
			$(this).toggleClass('on');
			$(this).next().slideToggle();
		} else {
			$('.pricenav li, .faq').hide();
			var link = $(this).data('link');
			var back = '<a class="cbtn3" href="#">返&nbsp;回</a>';
			$('.cbtn3').remove();
			$('.pricenav li.' + link).append(back).show();
			return false;
		}
	});
	$(document).on('click', '.faq dd li', function() {
		if (!$(this).is('.normal') && !$(this).parents('dd').is('.none')) {
			$('.pricenav li, .faq').hide();
			$('.pricenav li .cbtn3').remove();
			var link = $(this).data('link');
			var back = '<a class="cbtn3" href="#">返&nbsp;回</a>';
			$('.pricenav li.' + link).append(back).show();
			return false;
		}
	});

	$(document).on('click', '.pricenav li .cbtn3', function() {
		$('.pricenav li').hide();
		$('.faq').show();
	});
	$('.marketsearch form').on('submit', function(e) {
		var a = $(this).find('dd:first');
		var b = $(this).find('dd:last');
		if (a.find('.mycheck.on').length == 0) {
			alert('请选择' + a.prev('dt').prev('.title').text());
			return false;
		}
		if (b.find('.mycheck.on').length == 0) {
			alert('请选择区域');
			return false;
		}
	});

	//表格滑动宽度调整
	var tdNum = $('#scroller').find('tr:first td').length;
	$('#scroller').width(tdNum * 100);

})

//验证身份证
var vcity = {
	11: "\u5317\u4eac",
	12: "\u5929\u6d25",
	13: "\u6cb3\u5317",
	14: "\u5c71\u897f",
	15: "\u5185\u8499\u53e4",
	21: "\u8fbd\u5b81",
	22: "\u5409\u6797",
	23: "\u9ed1\u9f99\u6c5f",
	31: "\u4e0a\u6d77",
	32: "\u6c5f\u82cf",
	33: "\u6d59\u6c5f",
	34: "\u5b89\u5fbd",
	35: "\u798f\u5efa",
	36: "\u6c5f\u897f",
	37: "\u5c71\u4e1c",
	41: "\u6cb3\u5357",
	42: "\u6e56\u5317",
	43: "\u6e56\u5357",
	44: "\u5e7f\u4e1c",
	45: "\u5e7f\u897f",
	46: "\u6d77\u5357",
	50: "\u91cd\u5e86",
	51: "\u56db\u5ddd",
	52: "\u8d35\u5dde",
	53: "\u4e91\u5357",
	54: "\u897f\u85cf",
	61: "\u9655\u897f",
	62: "\u7518\u8083",
	63: "\u9752\u6d77",
	64: "\u5b81\u590f",
	65: "\u65b0\u7586",
	71: "\u53f0\u6e7e",
	81: "\u9999\u6e2f",
	82: "\u6fb3\u95e8",
	91: "\u56fd\u5916"
};
checkCard = function(card) {
	if (isCardNo(card) === false || checkProvince(card) === false || checkBirthday(card) === false || checkParity(card) === false) {
		return false;
	} else {
		return true;
	}
};

//检查号码是否符合规范，包括长度，类型
isCardNo = function(card) {
	//身份证号码为15位或者18位，15位时全为数字，18位前17位为数字，最后一位是校验位，可能为数字或字符X
	var reg = /(^\d{15}$)|(^\d{17}(\d|X|x)$)/;
	if (reg.test(card) === false) {
		return false;
	}
};

//取身份证前两位,校验省份
checkProvince = function(card) {
	var province = card.substr(0, 2);
	if (vcity[province] == undefined) {
		return false;
	}
	return true;
};

//检查生日是否正确
checkBirthday = function(card) {
	var len = card.length;
	//身份证15位时，次序为省（3位）市（3位）年（2位）月（2位）日（2位）校验位（3位），皆为数字
	if (len == '15') {
		var re_fifteen = /^(\d{6})(\d{2})(\d{2})(\d{2})(\d{3})$/;
		var arr_data = card.match(re_fifteen);
		var year = arr_data[2];
		var month = arr_data[3];
		var day = arr_data[4];
		var birthday = new Date('19' + year + '/' + month + '/' + day);
		return verifyBirthday('19' + year, month, day, birthday);
	}
	//身份证18位时，次序为省（3位）市（3位）年（4位）月（2位）日（2位）校验位（4位），校验位末尾可能为X
	if (len == '18') {
		var re_eighteen = /^(\d{6})(\d{4})(\d{2})(\d{2})(\d{3})([0-9]|X|x)$/;
		var arr_data = card.match(re_eighteen);
		var year = arr_data[2];
		var month = arr_data[3];
		var day = arr_data[4];
		var birthday = new Date(year + '/' + month + '/' + day);
		return verifyBirthday(year, month, day, birthday);
	}
	return false;
};

//校验日期
verifyBirthday = function(year, month, day, birthday) {
	var now = new Date();
	var now_year = now.getFullYear();
	//年月日是否合理
	if (birthday.getFullYear() == year && (birthday.getMonth() + 1) == month && birthday.getDate() == day) {
		//判断年份的范围（3岁到100岁之间)
		var time = now_year - year;
		if (time >= 3 && time <= 100) {
			return true;
		}
		return false;
	}
	return false;
};

//校验位的检测
checkParity = function(card) {
	//15位转18位
	card = changeFivteenToEighteen(card);
	var len = card.length;
	if (len == '18') {
		var arrInt = new Array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
		var arrCh = new Array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
		var cardTemp = 0,
			i, valnum;
		for (i = 0; i < 17; i++) {
			cardTemp += card.substr(i, 1) * arrInt[i];
		}
		valnum = arrCh[cardTemp % 11];
		if (valnum == card.substr(17, 1)) {
			return true;
		}
		return false;
	}
	return false;
};

//15位转18位身份证号
changeFivteenToEighteen = function(card) {
	if (card.length == '15') {
		var arrInt = new Array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
		var arrCh = new Array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
		var cardTemp = 0,
			i;
		card = card.substr(0, 6) + '19' + card.substr(6, card.length - 6);
		for (i = 0; i < 17; i++) {
			cardTemp += card.substr(i, 1) * arrInt[i];
		}
		card += arrCh[cardTemp % 11];
		return card;
	}
	return card;
};

function isChinese(temp) {
	var re = /^[\u4E00-\u9FA5]+$/;
	if (re.test(temp)) return true;
	return false;
}