define(['core', 'tpl', 'biz/plugin/diyform'], function(core, tpl, diyform) {
	var modal = {
		params: {
			orderid: 0,
			goods: [],
			coupon_goods: [],
			merchs: [],
			iscarry: 0,
			isverify: 0,
			isvirtual: 0,
			isonlyverifygoods: 0,
			addressid: 0,
			contype: 0,
			couponid: 0,
			wxid: 0,
			wxcardid: 0,
			wxcode: "",
			isnodispatch: 0,
			nodispatch: '',
			packageid: 0,
			backurl:"",
			realprice:"",
		}
	};
	modal.init = function(params) {
		modal.params = $.extend(modal.params, params || {});
		modal.params.couponid = 0;
		$('#coupondiv').find('.fui-cell-label').html('优惠券');
		$('#coupondiv').find('.fui-cell-info').html('');
		var discountprice = core.getNumber($(".discountprice").val());
		var isdiscountprice = core.getNumber($(".isdiscountprice").val());
		if (discountprice > 0) {
			$('.discount').show()
		}
		if (isdiscountprice > 0) {
			$('.isdiscount').show()
		}
		var loadAddress = false;
		if (typeof(window.selectedAddressData) !== 'undefined') {
			loadAddress = window.selectedAddressData
		} else if (typeof(window.editAddressData) !== 'undefined') {
			loadAddress = window.editAddressData;
			loadAddress.address = loadAddress.areas.replace(/ /ig, '') + ' ' + loadAddress.address
		}
		if (loadAddress) {
			modal.params.addressid = loadAddress.id;
			$('#addressInfo .has-address').show();
			$('#addressInfo .no-address').hide();
			$('#addressInfo .icon-dingwei').show();
			$('#addressInfo .realname').html(loadAddress.realname);
			$('#addressInfo .mobile').html(loadAddress.mobile);
			$('#addressInfo .address').html(loadAddress.address);
			$('#addressInfo a').attr('href', core.getUrl('member/address/selector'));
			$('#addressInfo a').click(function() {
				window.orderSelectedAddressID = loadAddress.id
			})
		}
		var loadStore = false;
		if (typeof(window.selectedStoreData) !== 'undefined') {
			loadStore = window.selectedStoreData;
			modal.params.storeid = loadStore.id;
			$('#carrierInfo .storename').html(loadStore.storename);
			$('#carrierInfo .realname').html(loadStore.realname);
			$('#carrierInfo_mobile').html(loadStore.mobile);
			$('#carrierInfo .address').html(loadStore.address);
			$('#carrierInfo').find('.no-address').css("display", "none");
			$('#carrierInfo').find('.has-address').css("display", "block");
			$('#carrierInfo').find('.fui-list-media').css("display", "block");
			$('#carrierInfo').find('.text').css("display", "block")
		}
		FoxUI.tab({
			container: $('#carrierTab'),
			handlers: {
				tab1: function() {
					modal.params.iscarry = 0;
					$('#addressInfo').show(), $('#carrierInfo').hide(), $('#memberInfo').hide(), $('#showdispatchprice').show();
					modal.caculate()
				},
				tab2: function() {
					modal.params.iscarry = 1;
					$('#addressInfo').hide(), $('#carrierInfo').show(), $('#memberInfo').show(), $('#showdispatchprice').hide();
					modal.caculate()
				}
			}
		});
		var number = $('.fui-number');
		if (number.length > 0) {
			var maxbuy = number.data('maxbuy') || 0,
				goodsid = number.data('goodsid'),we
				minbuy = number.data('minbuy') || 0,
				unit = number.data('unit') || '件';
			number.numbers({
				max: maxbuy,
				min: minbuy,
				minToast: "{min}" + unit + "起售",
				maxToast: "最多购买{max}" + unit,
				callback: function(num) {
					$.each(modal.params.goods, function() {
						if (this.goodsid == goodsid) {
							this.total = num;
							return false
						}
					});
					$.each(modal.params.coupon_goods, function() {
						if (this.goodsid == goodsid) {
							this.total = num;
							return false
						}
					});
					modal.params.contype = 0;
					modal.params.couponid = 0;
					modal.params.wxid = 0;
					modal.params.wxcardid = "";
					modal.params.wxcode = "";
					modal.params.couponmerchid = 0;
					$('#coupondiv').find('.fui-cell-label').html('优惠券');
					$('#coupondiv').find('.fui-cell-info').html('');
					$('#goodscount').html(num);
					var marketprice = core.getNumber(number.closest('.goods-item').find('.marketprice').html()) * num;
					$('.goodsprice').html(core.number_format(marketprice, 2));
					modal.caculate()
				}
			})
		}
		 
		$("#invoiceType").change(function(){
	       if (modal.params.fromcart==1) {
			 var goodsprice =$("input[name=invoicePrice]").val()
			 // console.log(goodsprice);
				
			}else{
			  var goodsprice =core.getNumber($('.goodsprice').html());
			}
		    

			var invoiceType = $("#invoiceType").val();
			console.log(invoiceType);
		 	if (invoiceType==0) {
	            var invoice = goodsprice*modal.params.commoninvoice;
	            console.log(invoice);
	        }else if(invoiceType==1){
	            var invoice = goodsprice*modal.params.addinvoice;
	        }
	    
	        $(".invoicePrice").html(core.number_format(invoice, 2)); 
	        modal.totalPrice(0);
	       
	    })


		$('#deductcredit').click(function() {
			modal.calcCouponPrice()
		});
		$('#deductcredit2').click(function() {
			modal.calcCouponPrice()
		});
		modal.bindCoupon();
		$(document).click(function() {
			$('input,select,textarea').each(function() {
				$(this).attr('data-value', $(this).val())
			});
			$(':checkbox,:radio').each(function() {
				$(this).attr('data-checked', $(this).prop('checked'))
			})
		});
		$('input,select,textarea').each(function() {
			var value = $(this).attr('data-value') || '';
			if (value != '') {
				$(this).val(value)
			}
		});
		$(':checkbox,:radio').each(function() {
			var value = $(this).attr('data-checked') === 'true' ? true : false;
			$(this).prop('checked', value)
		});
		$('.buy-btn').click(function() {
			modal.submit(this, params.token)
		});
		modal.caculate();
	};
	modal.giftPicker = function() {
		modal.giftPicker = new FoxUIModal({
			content: $('#option-picker-modal').html(),
			extraClass: 'picker-modal',
			maskClick: function() {
				modal.packagePicker.close()
			}
		})
	};
	modal.bindCoupon = function() {
		$('#coupondiv').unbind('click').click(function() {
			$('#couponloading').show();
			core.json('sale/coupon/util/query', {
				money: 0,
				type: 0,
				merchs: modal.params.merchs,
				goods: modal.params.goods
			}, function(rjson) {
				console.log(rjson);
				$('#couponloading').hide();
				if (rjson.result.coupons.length > 0 || rjson.result.wxcards.length > 0) {
					$('#coupondiv').show().find('.badge').html(rjson.result.coupons.length + rjson.result.wxcards.length).show();
					$('#coupondiv').find('.text').hide();
					require(['biz/sale/coupon/picker'], function(picker) {
						picker.show({
							couponMerge:modal.params.couponMerge,
							couponid: modal.params.couponid,
							coupons: rjson.result.coupons,
							wxcards: rjson.result.wxcards,
							onCancel: function() {
								modal.params.contype = 0;
								modal.params.couponid = 0;
								modal.params.wxid = 0;
								modal.params.wxcardid = "";
								modal.params.wxcode = "";
								modal.params.couponmerchid = 0;
								$('#coupondiv').find('.fui-cell-label').html('优惠券');
								$('#coupondiv').find('.fui-cell-info').html('');
								modal.calcCouponPrice()
							},
							onSelected: function(data) {
								console.log(data);
								modal.params.contype = data.contype;
								if (modal.params.contype == 1) {
									modal.params.couponid = 0;
									modal.params.wxid = data.wxid;
									modal.params.wxcardid = data.wxcardid;
									modal.params.wxcode = data.wxcode;
									modal.params.couponmerchid = data.merchid;
									$('#coupondiv').find('.fui-cell-label').html('已选择');
									$('#coupondiv').find('.fui-cell-info').html(data.couponname);
									$('#coupondiv').data(data);
									modal.calcCouponPrice()
								} else if (modal.params.contype == 2) {
									modal.params.couponid = data.couponid;
									modal.params.wxid = 0;
									modal.params.wxcardid = "";

									modal.params.wxcode = "";
									modal.params.couponmerchid = data.merchid;
									$('#coupondiv').find('.fui-cell-label').html('已选择');
									$('#coupondiv').find('.fui-cell-info').html(data.couponname);
									$('#coupondiv').data(data);
									modal.calcCouponPrice()
								} else {
									modal.params.contype = 0;
									modal.params.couponid = 0;
									modal.params.wxid = 0;
									modal.params.wxcardid = "";
									modal.params.wxcode = "";
									modal.params.couponmerchid = 0;
									$('#coupondiv').find('.fui-cell-label').html('优惠券');
									$('#coupondiv').find('.fui-cell-info').html('');
									modal.calcCouponPrice()
								}
							}
						})
					})
				} else {
					FoxUI.toast.show('未找到优惠券!');
					modal.hideCoupon()
				}
			}, false, true)
		})
	};
	modal.hideCoupon = function() {
		$('#coupondiv').hide();
		$('#coupondiv').find('.badge').html('0').hide();
		$('#coupondiv').find('.text').show()
	};
	modal.caculate = function() {
		var goodsprice = core.getNumber($('.goodsprice').html());
		var taskdiscountprice = core.getNumber($(".taskdiscountprice").val());
		var lotterydiscountprice = core.getNumber($(".lotterydiscountprice").val());
		var discountprice = core.getNumber($(".discountprice").val());
		var isdiscountprice = core.getNumber($(".isdiscountprice").val());	
		var invoicePrice = core.getNumber($(".invoicePrice").val());		

		var totalprice = goodsprice - taskdiscountprice - lotterydiscountprice - discountprice - isdiscountprice;
		if ($('.shownum').length > 0) {
			totalprice = core.getNumber($('.marketprice').html()) * parseInt($('.shownum').val())
		}
		if (modal.params.fromcart == 0) {
			if (modal.params.goods.length == 1) {
				modal.params.goods[0].total = parseInt($('.shownum').val())
			}
	
			var invoiceType = $("#invoiceType").val();			
			var invoice=0;
		 	if (invoiceType==0) {
	            var invoice = goodsprice*modal.params.commoninvoice;
	        }else if(invoiceType==1){
	            var invoice = goodsprice*modal.params.addinvoice;
	        }

		    $(".invoicePrice").html(core.number_format(invoice, 2)); 

		}
		core.json('paymentcode/select/caculate', {
			totalprice: totalprice,
			addressid: modal.params.addressid,
			dispatchid: modal.params.dispatchid,
			dflag: modal.params.iscarry,
			goods: modal.params.goods,
			liveid: modal.params.liveid
		}, function(getjson) {
			console.log(getjson);
			if (getjson.status == 1) {
				if (modal.params.iscarry) {
					$('.dispatchprice').html('0.00')
				} else {
					$('.dispatchprice').html(core.number_format(getjson.result.price, 2))
				}
				if (getjson.result.taskdiscountprice) {
					$('#taskdiscountprice').val(core.number_format(getjson.result.taskdiscountprice, 2))
				}
				if (getjson.result.lotterydiscountprice) {
					$('#lotterydiscountprice').val(core.number_format(getjson.result.lotterydiscountprice, 2))
				}
				if (getjson.result.discountprice) {
					$('#discountprice').val(core.number_format(getjson.result.discountprice, 2))
				}
				if (getjson.result.buyagain) {
					$('#buyagain').val(core.number_format(getjson.result.buyagain, 2));
					$('#showbuyagainprice').html(core.number_format(getjson.result.buyagain, 2)).parents(".fui-cell").show()
				}
				if (getjson.result.isdiscountprice) {
					$('#isdiscountprice').val(core.number_format(getjson.result.isdiscountprice, 2))
				}
				if (getjson.result.deductcredit) {
					$('#deductcredit_money').html(core.number_format(getjson.result.deductmoney, 2));
					$('#deductcredit_info').html(getjson.result.deductcredit);
					$("#deductcredit").data('credit', getjson.result.deductcredit);
					$("#deductcredit").data('money', core.number_format(getjson.result.deductmoney, 2))
				}
				if (getjson.result.deductcredit2) {
					$('#deductcredit2_money').html(getjson.result.deductcredit2);
					$("#deductcredit2").data('credit2', getjson.result.deductcredit2)
				}
				if (getjson.result.seckillprice > 0) {
					$('#seckillprice').show();
					$('#seckillprice_money').html(core.number_format(getjson.result.seckillprice, 2))
				} else {
					$('#seckillprice').hide();
					$('#seckillprice_money').html(0)
				}
				if (getjson.result.couponcount > 0) {
					$('#coupondiv').show().find('.badge').html(getjson.result.couponcount).show();
					$('#coupondiv').find('.text').hide()
				} else {
					modal.params.couponid = 0;
					$('#coupondiv').hide().find('.badge').html(0).hide()
				}

				if (getjson.result.merch_deductenough_money > 0) {
					$('#merch_deductenough').hide();
				} else {
					$('#merch_deductenough').show();
				}
				if (getjson.result.deductenough_money > 0) {
					$('#deductenough').hide();
				} else {
					$('#deductenough').show();
				}

				if (getjson.result.coupondeduct>0) {
					$('#coupondeduct').html(core.number_format(getjson.result.coupondeduct,2));
				}
				if (getjson.result.merchs) {
					modal.params.merchs = getjson.result.merchs
				}
				if (getjson.result.isnodispatch == 1) {
					modal.isnodispatch = 1;
					modal.nodispatch = getjson.result.nodispatch;
					FoxUI.toast.show(modal.nodispatch)
				} else {
					modal.isnodispatch = 0;
					modal.nodispatch = ''
				}
				modal.calcCouponPrice()
			}
		}, true, true)
	};

	modal.totalPrice = function(couponprice) {
		var goodsprice = core.getNumber($('.goodsprice').html());
		var couponprice = couponprice;
		var taskdiscountprice = core.getNumber($(".showtaskdiscountprice").html());
		var lotterydiscountprice = core.getNumber($(".showlotterydiscountprice").html());
		var discountprice = core.getNumber($(".showdiscountprice").html());
		var isdiscountprice = core.getNumber($(".showisdiscountprice").html());
		var buyagainprice = core.getNumber($("#buyagain").val());
		
		var totalprice = goodsprice - taskdiscountprice - lotterydiscountprice - discountprice - isdiscountprice - couponprice - buyagainprice;	

		var dispatchprice = core.getNumber($(".dispatchprice").html());
		var merch_enoughprice = 0;
		if ($("#merch_deductenough_money").length > 0 && $("#merch_deductenough_money").html() != '') {
			merch_enoughprice = core.getNumber($("#merch_deductenough_money").html())
		}
		var enoughprice = 0;
		if ($("#deductenough_money").length > 0 && $("#deductenough_money").html() != '') {
			enoughprice = core.getNumber($("#deductenough_money").html())
		}

		if ($("#invoiceCheckbox").is(":checked")) {
			var invoice =core.getNumber($(".invoicePrice").html());
		}else{
			var invoice =0;
		}	
		totalprice = totalprice - merch_enoughprice - enoughprice + dispatchprice+invoice;
		console.log(totalprice);
		if(totalprice <= 0){
			$(".oppaytype").hide();
			$(".oppaybtn").show();
		}
		var deductprice = 0;
		if ($("#deductcredit").length > 0) {
			if ($("#deductcredit").prop('checked')) {
				deductprice = core.getNumber($("#deductcredit").data('money'));
				if ($("#deductcredit2").length > 0) {
					var td1 = core.getNumber($("#deductcredit2").data('credit2'));
					if (totalprice - deductprice >= 0) {
						var td = totalprice - deductprice;
						if (td > td1) {
							td = td1
						}
						$("#deductcredit2_money").html(core.number_format(td, 2));
						$("#deductcredit2_money").val(totalprice)
					}
				}
			} else {
				if ($("#deductcredit2").length > 0) {
					var td = core.getNumber($("#deductcredit2").data('credit2'));
					$("#deductcredit2_money").html(core.number_format(td, 2));
					$("#deductcredit2_money").val(totalprice)
				}
			}
		}
		var deductprice2 = 0;
		if ($("#deductcredit2").length > 0) {
			if ($("#deductcredit2").prop('checked')) {
				deductprice2 = core.getNumber($("#deductcredit2_money").html())
			}
		}
		totalprice = totalprice - deductprice - deductprice2;
		if (totalprice <= 0) {
			totalprice = 0
		}
		$('.totalprice').html(core.number_format(totalprice));
		modal.bindCoupon();
		return totalprice
	};
	modal.calcCouponPrice = function() {
		var goodsprice = core.getNumber($('.goodsprice').html());
		$('#coupondeduct_div').hide();
		$('#coupondeduct_text').html('');
		$('#coupondeduct_money').html('0');
		var deductprice = 0;
		var taskdiscountprice = core.getNumber($(".taskdiscountprice").val());
		var lotterydiscountprice = core.getNumber($(".lotterydiscountprice").val());
		var discountprice = core.getNumber($(".discountprice").val());
		var isdiscountprice = core.getNumber($(".isdiscountprice").val());
		if (modal.params.couponid == 0 && modal.params.wxid == 0) {
			if (taskdiscountprice > 0) {
				$(".showtaskdiscountprice").html($(".taskdiscountprice").val());
				$('.istaskdiscount').show()
			} else {
				$('.istaskdiscount').hide()
			}
			if (lotterydiscountprice > 0) {
				$(".showlotterydiscountprice").html($(".lotterydiscountprice").val());
				$('.islotterydiscount').show()
			} else {
				$('.islotterydiscount').hide()
			}
			if (discountprice > 0) {
				$(".showdiscountprice").html($(".discountprice").val());
				$('.discount').show()
			} else {
				$('.discount').hide()
			}
			if (isdiscountprice > 0) {
				$(".showisdiscountprice").html($(".isdiscountprice").val());
				$('.isdiscount').show()
			} else {
				$('.isdiscount').hide()
			}
			return modal.totalPrice(0)
		}
		core.json('paymentcode/select/getcouponprice', {
			merchs: modal.params.merchs,
			goods: modal.params.coupon_goods,
			coupon_goods: modal.params.coupon_goods,
			goodsprice: goodsprice,
			couponid: modal.params.couponid,
			contype: modal.params.contype,
			wxid: modal.params.wxid,
			wxcardid: modal.params.wxcardid,
			wxcode: modal.params.wxcode,
			discountprice: discountprice,
			isdiscountprice: isdiscountprice
		}, function(getjson) {
			console.log(getjson);
			if (getjson.status == 1) {
				$('#coupondeduct_text').html(getjson.result.coupondeduct_text);
				deductprice = getjson.result.deductprice;
				var discountpricenew = getjson.result.discountprice;
				var isdiscountpricenew = getjson.result.isdiscountprice;
				if (discountpricenew > 0) {
					$(".showdiscountprice").html(discountpricenew);
					$('.discount').show()
				} else {
					$(".showdiscountprice").html(0);
					$('.discount').hide()
				}
				if (isdiscountpricenew > 0) {
					$(".showisdiscountprice").html(isdiscountpricenew);
					$('.isdiscount').show()
				} else {
					$(".showisdiscountprice").html(0);
					$('.isdiscount').hide()
				}
				if (deductprice > 0) {
					$('#coupondeduct_div').show();
					$('#coupondeduct_money').html(core.number_format(deductprice, 2))
				}
				var couponid = getjson.result.couponid;
				var totalcoupon = getjson.result.totalcoupon;

				if (couponid!=0) {
					modal.params.couponid=couponid;
					modal.params.totalcoupon = totalcoupon;
				}
			} else {
				if (discountprice > 0) {
					$(".showdiscountprice").html($(".discountprice").val());
					$('.discount').show()
				} else {
					$('.discount').hide()
				}
				if (isdiscountprice > 0) {
					$(".showisdiscountprice").html($(".isdiscountprice").val());
					$('.isdiscount').show()
				} else {
					$('.isdiscount').hide()
				}
				if (getjson.status==2) {
					$('#coupondiv').find('.fui-cell-label').html('优惠券');
					$('#coupondiv').find('.fui-cell-info').html('');
					FoxUI.toast.show('当前订单不能合并优惠券')
				}
				deductprice = 0
			}
			return modal.totalPrice(deductprice)
		}, true, true)
	};
	modal.submit = function(obj, token) {
		var $this = $(obj);
		var giftid = parseInt($("#giftid").val());
		
		var diyformdata = '';
		if (modal.params.orderdiyformid != '0') {
			var diyformdata = diyform.getData('.diyform-container');
			if (!diyformdata) {
				return
			}
		}
		if (modal.params.fromcart == 0) {
			if (modal.params.goods.length == 1) {
				modal.params.goods[0].total = parseInt($('.shownum').val())
			}
		}

		if ($("#invoiceCheckbox").is(":checked")) {
			modal.params.isinvoice=1;
			modal.params.invoiceType=$("#invoiceType").val();
			modal.params.raisedType=$("#raisedType").val();	
			modal.params.invoiceContent=$("#invoiceContent").val();
			modal.params.raisedType = $("input[name=raisedType]:checked").val();
			modal.params.raised =$("input[name='raised']").val();
			modal.params.number =$("input[name='number']").val();

			if (modal.params.raisedType==2) {

			modal.params.raisedNumber =$("input[name='number']").val()
			}
			modal.params.invoicePrice = core.getNumber($('.invoicePrice').html());	
			modal.totalPrice(0);
		}
		$this.attr('stop', 1);
		var data = {
			'orderid': modal.params.orderid,
			'id': modal.params.id,
			'goods': modal.params.goods,
			'giftid': giftid,
			'gdid': modal.params.gdid,
			'liveid': modal.params.liveid,
			'diydata': diyformdata,
			'dispatchtype': modal.params.iscarry ? 1 : 0,
			'fromcart': modal.params.fromcart,
			'carrierid': modal.params.iscarry ? modal.params.storeid : 0,
			'addressid': !modal.params.iscarry ? modal.params.addressid : 0,
			'carriers': (modal.params.iscarry || modal.params.isvirtual || modal.params.isverify) ? {
				'carrier_realname': $(':input[name=carrier_realname]').val(),
				'carrier_mobile': $(':input[name=carrier_mobile]').val(),
				'realname': $('#carrierInfo .realname').html(),
				'mobile': $('#carrierInfo_mobile').html(),
				'storename': $('#carrierInfo .storename').html(),
				'address': $('#carrierInfo .address').html()
			} : '',
			'remark': $("#remark").val(),
			'deduct': ($("#deductcredit").length > 0 && $("#deductcredit").prop('checked')) ? 1 : 0,
			'deduct2': ($("#deductcredit2").length > 0 && $("#deductcredit2").prop('checked')) ? 1 : 0,
			'contype': modal.params.contype,
			'couponid': modal.params.couponid,
			'totalcoupon':modal.params.totalcoupon ? modal.params.totalcoupon : 0,
			'wxid': modal.params.wxid,
			'wxcardid': modal.params.wxcardid,
			'wxcode': modal.params.wxcode,
			'invoicename': $('#invoicename').val(),
			'submit': true,
			'token': token,
			'packageid': modal.params.packageid,
			'fromquick': modal.params.fromquick,
			'invoiceType':modal.params.invoiceType,
			'invoiceContent':modal.params.invoiceContent,
			'raisedType':modal.params.raisedType,
			'raised':modal.params.raised,
			'raisedNumber':modal.params.raisedNumber,
			'isinvoice':modal.params.isinvoice,
			'invoicePrice':modal.params.invoicePrice,
			'number':modal.params.number,
			'paytype':$("input[name='paytype']").val(),
			'goodstotalprice':$("text[class='totalprice']").html()
		};
		console.log(data);
		if(data.goodstotalprice > 0){
			FoxUI.confirm('确认要支付吗?', '提醒', function() {
				modal.pay(data,$this)
			}, function() {
				$this.removeAttr('stop')
			})
		} else {
			modal.pay(data,$this)
		}
			
		
	};

	modal.pay = function (data,then){
		FoxUI.loader.show('mini');
		core.json('paymentcode/select/submit', data, function(ret) {
			// console.log(ret);
			then.removeAttr('stop', 1);
			console.log(ret);
			if (ret.status == 0) {
				FoxUI.loader.hide();
				FoxUI.toast.show(ret.result.message);
				return
			}
			if (ret.status == -1) {
				FoxUI.loader.hide();
				FoxUI.alert(ret.result.message);
				return
			}
			if(ret.status == 1){
				var oid = ret.result.orderid;
				var type = data.paytype;
				var wechat = ret.result.wechat;

				if (type == 'wechat') {
					if (core.ish5app()) {
						appPay('wechat', null, null, true);
						return
					}
					modal.payWechat(ret)
				} else if (type == 'credit') {

					location.href = core.getUrl('paymentcode/select/complete', {
						id: ret.result.orderid,
						paytype:data.paytype,
					});
				}
			}
		}, false, true)

	}
	modal.payWechat = function(ret) {
		var wechat = ret.result.wechat;
		if (!wechat.success) {
			return
		}
		if (wechat.weixin) {
			function onBridgeReady() {
				WeixinJSBridge.invoke('getBrandWCPayRequest', {
					'appId': wechat.appid ? wechat.appid : wechat.appId,
					'timeStamp': wechat.timeStamp,
					'nonceStr': wechat.nonceStr,
					'package': wechat.package,
					'signType': wechat.signType,
					'paySign': wechat.paySign
				}, function(res) {
					if (res.err_msg == 'get_brand_wcpay_request:ok') {
						location.href = core.getUrl('paymentcode/select/complete', {
							id: ret.result.orderid,
							paytype:'wechat',
						});
					} else if (res.err_msg == 'get_brand_wcpay_request:cancel') {
						FoxUI.toast.show('取消支付')
					} else {
						FoxUI.toast.show(res.err_msg)
					}
					ret.removeAttr('stop')
				})
			}
			if  (typeof WeixinJSBridge  ==  "undefined") {
				if ( document.addEventListener ) {
					document.addEventListener('WeixinJSBridgeReady',  onBridgeReady,  false)
				} else  if  (document.attachEvent) {
					document.attachEvent('WeixinJSBridgeReady',  onBridgeReady);
					document.attachEvent('onWeixinJSBridgeReady',  onBridgeReady)
				}
			} else {
				onBridgeReady()
			}
		}
	};
	return modal
});