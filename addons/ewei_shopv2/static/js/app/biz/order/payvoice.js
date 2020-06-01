define(['core', 'tpl'], function(core, tpl) {
	var modal = {
		params: {}
	};
	modal.init = function(params) {
		var defaults = {
			orderid: 0,
			wechat: {
				success: false
			},
			cash: {
				success: false
			},
			alipay: {
				success: false
			},
		};
		modal.params = $.extend(defaults, params || {});
		$('.pay-btn').unbind('click').click(function() {
			var btn = $(this);
			modal.pay(btn)
			
		});
	};
	modal.pay = function(btn) {
		var type = btn.data('type') || '';
		if (type == '') {
			return
		}
		if (btn.attr('stop')) {
			return
		}
		btn.attr('stop', 1);
		if (type == 'wechat') {
			if (core.ish5app()) {
				appPay('wechat', null, null, true);
				return
			}
			modal.payWechat(btn)
		} else if (type == 'alipay') {
			if (core.ish5app()) {
				appPay('alipay', null, null, true);
				return
			}
			modal.payAlipay(btn)
		} else if (type == 'credit') {
			FoxUI.confirm('确认要支付吗?', '提醒', function() {

				modal.complete(btn, type)
			}, function() {

				btn.removeAttr('stop')
			})
		} else if (type == 'peerpay') {
			location.href = core.getUrl('order/pay/peerpay', {
				id: modal.params.orderid
			});
			return
		} else {
			modal.complete(btn, type)
		}
	};
	

	
	modal.complete = function(btn, type) {
		var peerpay = $('#peerpay').text();
		var peerpaymessage = $('#peerpaymessage').val();
		FoxUI.loader.show('mini');
		setTimeout(function() {
			core.json('invoicing/index/complete', {
				id: modal.params.itemid,
				ordersn: modal.params.orderID,
				type: type,
				peerpay: peerpay,
				peerpaymessage: peerpaymessage
			}, function(pay_json) {
				if (pay_json.status == 1) {
					location.href = core.getUrl('invoicing/index/success', {
						id: modal.params.itemid,
						result: pay_json.result.result,
						backurl:modal.params.backurl
					});
					return
				}
				FoxUI.loader.hide();
				btn.removeAttr('stop');
				FoxUI.toast.show(pay_json.result.message)
			}, false, true)
		}, 1000)
	};
	return modal
});