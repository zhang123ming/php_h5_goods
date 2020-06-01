define(['core', 'tpl'], function(core, tpl) {
	var modal = {
		page: 1,
		type: 0
	};
	modal.init = function(params) {
		modal.type = params.type;
		$('.fui-content').infinite({
			onLoading: function() {
				modal.getList()
			}
		});
		if (modal.page == 1) {
			modal.getList()
		}
	};
	modal.getList = function() {
		core.json('member/myteam/get_log', {
			page: modal.page
		}, function(ret) {
			var result = ret.result;
			if (result.total <= 0) {
				$('.container1').hide();
				$('.content-empty').show();
				$('.fui-content').infinite('stop')
			} else {
				$('.container1').show();
				$('.content-empty').hide();
				$('.fui-content').infinite('init');
				if (result.list.length <= 0 || result.list.length < result.pagesize) {
					$('.fui-content').infinite('stop')
				}
			}
			modal.page++;
			core.tpl('.container1', 'tpl_log_list', result, modal.page > 1)
		})
	};
	return modal
});