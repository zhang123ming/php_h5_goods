define(['core', 'tpl'], function(core, tpl, op) {
	var modal = {
		params: {}
	};
	modal.init = function() {
		var verify_container = $('.verify-container');
		var status = verify_container.data('status'),
			orderid = verify_container.data('orderid');
		verify_container.find('.verify-cell').each(function() {
			var cell = $(this);
			var verifycode = cell.data('verifycode');
			cell.find('.verify-checkbox').unbind('click').click(function() {
				core.json('verify/select', {
					id: orderid,
					verifycode: verifycode
				}, function(ret) {
					setTimeout(function() {
						if ($('.verify-checkbox:checked').length <= 0) {
							$('.order-query').find('span').html('全部使用')
						} else {
							$('.order-verify').find('span').html('确认使用(' + $('.verify-checkbox:checked').length + ")")
						}
					}, 0)
				}, true, true)
			})
		});
		$(".fui-number").numbers({
			minToast: "最少核销{min}次",
			maxToast: "最多核销{max}次"
		});
		$('.order-query').click(function() {
			modal.verify($(this))
		})
	};
	modal.verify = function(btn) {
		var tip = "",
			status = btn.data('status'),
			servermoney = btn.data('servermoney'),
			orderid = btn.data('orderid');
		var times = parseInt($('.shownum').val());
		var verifycode = '';
		if (status == 1) {
			tip = "确认服务吗?"
		}else{
			tip = "确认要拒绝服务吗?"
		}
		FoxUI.confirm(tip, function() {
			core.json('verify/server_complete', {
				id: orderid,
				status: status,
				servermoney:servermoney
			}, function(ret) {
				if (ret.status == 0) {
					FoxUI.toast.show(ret.result.message);
					return
				}
				if(status==1){
					location.href = core.getUrl('verify/success', {
						id: orderid
					})
				}else{
					location.href = core.getUrl('verify/fail', {
						id: orderid
					})
				}
			})
		})
	};
	return modal
});