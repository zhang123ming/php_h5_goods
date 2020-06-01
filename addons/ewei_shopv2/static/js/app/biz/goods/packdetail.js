define(['core', 'tpl', 'biz/goods/picker'], function(core, tpl, picker, diyform) {
	var modal = {
		params: {}
	};
	modal.init = function(params) {
		modal.params = $.extend(modal.params, params || {});
		console.log(modal.params);
		var packageid = modal.params.packageid;
		var goods = modal.params.goods;	
		var ispackage = modal.params.ispackage;
		var goodsempty = modal.params.goodsempty;
		var allprice = 0;
		if(ispackage==1){
			$(".allprice").each(function(index) {
				allprice += parseFloat($("#allprice" + index + "").attr("data-allprice"),2);

			})		
			$(".totalprice").html(allprice);
		}		
		$('#package-buy-a').unbind('click').click(function() {		
			var goods = [];
			var stop = 0;
			$(".goods-item").each(function(index) {
				var goodsid = $("#goodsid" + index + "").attr("data-goodsid");
				var optionid = $("#optionid" + index + "").val();
				if (optionid === undefined) {
					optionid = 0
				} else if (!optionid) {
					FoxUI.toast.show('请选择规格!');
					stop = 1;
					return false
				}
				goods[index] = {
					goodsid: goodsid,
					optionid: optionid
				}
			});
			if (stop == 1) {
				return false
			}
			goods = JSON.stringify(goods);
			if ($(this).attr('stop')) {
				return
			}
			location.href = core.getUrl('order/create', {
				packageid: packageid,
				goods: goods,
			})
		});
		$('#package-buy-b').unbind('click').click(function() {		
			var goods = [];
			var stop = 0;
			$(".goods-item").each(function(index) {
				var goodsid = $("#goodsid" + index + "").attr("data-goodsid");
				var optionid = $("#optionid" + index + "").val();
				if (optionid === undefined) {
					optionid = 0
				} else if (!optionid) {
					FoxUI.toast.show('请选择规格!');
					stop = 1;
					return false
				}
				if(goodsempty){
					FoxUI.toast.show('请选齐套餐商品!');
					stop = 1;
					return false
				}
				goods[index] = {
					goodsid: goodsid,
					optionid: optionid
				}
			});
			if (stop == 1) {
				return false
			}
			goods = JSON.stringify(goods);
			if ($(this).attr('stop')) {
				return
			}
			location.href = core.getUrl('order/create', {
				packageid: packageid,
				goods: goods,
				ispackage:modal.params.ispackage,
			})
		});
		$(".optionbtn").click(function() {
			var goodsid = $(this).attr("data-goodsid");
			var default_option = $(".inputoption" + goodsid + "").val();
			$.ajax({
				url: core.getUrl('goods/package/option', {
					pid: packageid,
					goodsid: goodsid
				}),
				dataType: "json",
				async: true,
				type: "GET",
				beforeSend: function() {},
				success: function(data) {
					if (data.status > 0) {
						$(".dispatching-info").html(html);
						var option = data.result;
						var html = '';
						for (var o in option) {
							if (option[o].optionid) {
								html += "<i class='package-i' data-optionid='" + option[o].optionid + "' data-price='" + option[o].packageprice + "' data-goodsid='" + goodsid + "'>" + option[o].title + "</i>"
							}
						}
						$(".dispatching-info").html(html)
					} else {
						$(".dispatching-info").html('暂无规格！')
					}
					if (default_option) {
						$(".package-i").each(function() {
							if ($(this).attr("data-optionid") == default_option) {
								$(this).addClass("active")
							}
						})
					}
					$(".package-i").on("click", function() {
						$(".package-i").removeClass("active");
						var goodsid = $(this).addClass("active").attr("data-goodsid");
						var optionid = $(this).attr("data-optionid");
						var packageprice = $(this).attr("data-price");
						var option = $(this).html();
						$(".packoption" + goodsid + "").html(option);
						$(".marketprice" + goodsid + "").html(packageprice);
						$(".inputoption" + goodsid + "").val(optionid);
						var totalprice = 0;
						$(".marketprice").each(function() {
							totalprice += parseFloat($(this).html())
						});
						$(".totalprice").html(totalprice.toFixed(2))
					})
				},
				complete: function() {},
				error: function() {}
			});
			modal.packagePicker = new FoxUIModal({
				content: $('#option-picker-modal').html(),
				extraClass: 'picker-modal',
				maskClick: function() {
					modal.packagePicker.close()
				}
			});
			modal.packagePicker.container.find('.btn-danger').click(function() {
				modal.packagePicker.close()
			});
			modal.packagePicker.show()
		})
	};
	modal.optionPicker = function(action) {
		picker.open({
			goodsid: modal.goodsid,
			total: modal.total,
			split: ';',
			optionid: modal.optionid,
			showConfirm: true,
			autoClose: false,
			mustbind: modal.mustbind,
			backurl: modal.backurl,
			onConfirm: function(total, optionid, optiontitle) {
				modal.total = total;
				modal.optionid = optionid;
				$('.option-selector').html("已选: 数量x" + total + " " + optiontitle);
				if (action == 'buy') {
					if ($('.diyform-container').length > 0) {
						var diyformdata = diyform.getData('.diyform-container');
						if (!diyformdata) {
							return
						} else {
							core.json('order/create/diyform', {
								id: modal.goodsid,
								diyformdata: diyformdata
							}, function(ret) {
								$.router.load(core.getUrl('order/create', {
									id: modal.goodsid,
									optionid: modal.optionid,
									total: modal.total,
									gdid: ret.result.goods_data_id
								}), true)
							}, true, true);
							picker.close()
						}
					} else {
						picker.close();
						$.router.load(core.getUrl('order/create', {
							id: modal.goodsid,
							optionid: modal.optionid,
							total: modal.total
						}), false)
					}
				} else if (action == 'cart') {
					if ($('.diyform-container').length > 0) {
						var diyformdata = diyform.getData('.diyform-container');
						if (!diyformdata) {
							return
						} else {
							core.json('order/create/diyform', {
								id: modal.goodsid,
								diyformdata: diyformdata
							}, function(ret) {
								cart.add(modal.goodsid, modal.optionid, modal.total, diyformdata, function(ret) {
									$('.cart-item').find('.badge').html(ret.cartcount).removeClass('out').addClass('in');
									window.cartcount = ret.cartcount
								})
							}, true, true);
							picker.close()
						}
					} else {
						cart.add(modal.goodsid, modal.optionid, modal.total, false, function(ret) {
							$('.cart-item').find('.badge').html(ret.cartcount).removeClass('out').addClass('in');
							window.cartcount = ret.cartcount
						});
						picker.close()
					}
				} else {
					picker.close()
				}
			}
		})
	};
	return modal
});