define(['core', 'tpl'], function(core, tpl) {
	var modal = {};
	modal.init = function() {
		core.json("commission/auth",{refresh:0},function(ret) {
			$("#posterimg").find('.fui-cell-group').remove();
			$("#posterimg").find('img').attr('src', ret.result.img).show();
			$("#refreshdiv").show();
		}, false, true)
	};

	$("#refresh").click(function(){
		core.json("commission/auth",{refresh:1},function(ret) {
			$("#posterimg").find('.fui-cell-group').remove();
			$("#posterimg").find('img').attr('src', ret.result.img).show()
		}, false, true)
	})
	return modal
});