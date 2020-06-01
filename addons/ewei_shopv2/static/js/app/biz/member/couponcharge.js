define(['core', 'tpl'], function(core, tpl) {
	var modal = {};
	modal.init = function(params) {
		$(document).on('click','#btn-credit',function(){

			FoxUI.confirm('确认要提交吗?', '提醒', function() {
				var money = $('#money').val();
				var agentid = $('#memberid').val();
				if ($.isEmpty(agentid)) {
					FoxUI.toast.show('请输入会员ID');
					return
				}
				if (money <= 0) {
					FoxUI.toast.show('消费金额必须大于0!');
					return
				}
				if (!$('#money').isNumber()) {
					FoxUI.toast.show('请输入数字金额!');
					return
				}
				
				core.json('member/couponcharge',{
					agentid:agentid,
					money:money
				},function(rjson){
					console.log(rjson)
					if (rjson.status==0) {
						FoxUI.toast.show(rjson.result.message)
					}
					if (rjson.status==2) {
						FoxUI.toast.show(rjson.result.message)
						location.href = core.getUrl('member');
					}
					if (rjson.status==1) {
						FoxUI.toast.show('优惠券发放成功!');
						location.href = core.getUrl('member');
						return
					}
				},true,true)
			}, function() {
				console.log('cancel')
			})
		})
	};
	return modal
});