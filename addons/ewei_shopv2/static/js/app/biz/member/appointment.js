define(['core', 'tpl'], function(core, tpl) {
	var modal = {};
	modal.init = function(params) {
		params = $.extend({
			returnurl: '',
			template_flag: 1,
			new_area: 0
		}, params || {});		
		$('#btn-submit').click(function() {
			var postdata = {};
			console.log(params.template_flag);
			if (params.template_flag == 1) {
				FoxUI.loader.show('mini');
				$(this).html('处理中...').attr('submit', 1);
				require(['biz/plugin/diyform'], function(diyform) {
					postdata = diyform.getData('.diyform-container');
					FoxUI.loader.hide();
					if (postdata) {
						core.json('member/appointment/submit', {
							memberdata: postdata
						}, function(json) {
							modal.complete(params, json)
						}, true, true)
					} else {
						$('#btn-submit').html('确认提交').removeAttr('submit')
					}
				})
			}
		})
	};
	modal.complete = function(params, json) {
		FoxUI.loader.hide();
		if (json.status == 1) {
			FoxUI.toast.show('保存成功');
			location.href = params.returnurl
			// if (params.returnurl) {
			// 	location.href = params.returnurl
			// } else {
			// 	history.back()

			// }
		} else {
			$('#btn-submit').html('确认提交').removeAttr('submit');
			FoxUI.toast.show(json.result.message)
			location.href = params.returnurl
		}
	};
	return modal
});