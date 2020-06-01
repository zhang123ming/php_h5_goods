define(['core', 'tpl'], function(core, tpl) {
	var modal = {
		page: 1,
		id: 0,
		total: 0,
		levels: [],
		search_need_up:2,
		list:[],
		count:0,
		need_up_count:0,
		need_up_count:0,
	};
	modal.init = function(params) {
		modal.id = params.id;
		modal.total = $('.num').val();
		modal.count = modal.total;
		modal.getlevel();
		$('.fui-content').infinite({
			onLoading: function() {
				modal.getList(modal.id);
			}
		});
		if (modal.page == 1) {
			modal.getList(modal.id)
		}

		$(".fui-number .minus").click(function(){
			$('.plus').removeClass('disabled');
			if(modal.count<0){
				return;
			}else if(modal.count==0){
				$('.minus').addClass('disabled');
				return;
			}
			modal.count--;
			$('.num').val(modal.count);
		});

		$(".fui-number .plus").click(function(){
			$('.minus').removeClass('disabled');
			if(modal.count>modal.total){
				return;
			}else if(modal.count==modal.total){
				$('.plus').addClass('disabled');
				return;
			}
			modal.count++;
			$('.num').val(modal.count);
		});

		$(".num").bind("input propertychang",function(event){
			var value = $(this).val();

			value = (parseInt(value)>parseInt(modal.total))?modal.total:value;
			modal.count=value;

			$('.num').val(value);
		});
		$(".search_need_up").click(function(){
			$('.container').empty();
			modal.search_need_up = 1;
			modal.page = 0;
			modal.getList(modal.id);
		});
		$(".search_all").click(function(){
			$('.container').empty();
			modal.search_need_up = 0;
			modal.page = 0;
			modal.getList(modal.id);
		});

	};
	modal.getList = function() {
		core.json('member/myteam/get_list', {
			page: modal.page,
			id: modal.id,
			search_need_up:modal.search_need_up,
		}, function(ret) {
			console.log("tuanduixinxi");
			console.log(ret);
			var result = ret.result;
			if(modal.list.length == 0){
				modal.list = result.list;
			}
			if(modal.search_need_up == 0){
				if(result.list == modal.list && modal.page >0){
					result.list =[];
					console.log("page"+modal.page);
				}

			}
			if (result.total <= 0) {
				$('.container').hide();
				$('.content-empty').show();
				$('.fui-content').infinite('stop')
			} else {
				$('.container').show();
				$('.content-empty').hide();
				$('.fui-content').infinite('init');
				if (result.list.length <= 0 || result.list.length < result.pagesize) {
					$('.fui-content').infinite('stop')
				}
			}
			modal.page++;
			core.tpl('.container', 'tpl_member_list', result, modal.page > 1)
			$('.update').unbind('click').click(function() {
				modal.showModal(0,$(this).data("level"),$(this).data("openid"));
			});
			$('.give').unbind('click').click(function() {
				modal.showModal(1,"",$(this).data("openid"));
			});
			$('.tishi').unbind('click').click(function() {
				FoxUI.alert('名额不足，请联系总部');
			})
			$('.remove').unbind('click').click(function() {
				var openid = $(this).data("openid");
				core.json('member/myteam/remove', {
					openid: openid,
				}, function(ret) {
					FoxUI.alert('解除关系成功');
					$('.container').html(''), $('.infinite-loading').show(), $(".content-empty").hide();
					modal.page = 1, modal.getList()
				});
			});

			$(".upinfo").unbind('click').click(function(){
				var openid = $(this).data("openid");
				core.json('member/myteam/get_upinfo', {
					openid: openid,
				}, function(ret) {
					console.log(ret);
					var result = ret.result;
					$(".pop-apply-hidden").find(".diytime").html(result.upinfo.time);
					$(".pop-apply-hidden").find(".diylevel").html(result.upinfo.level);
					$(".pop-apply-hidden").find(".diytel").html(result.upinfo.tel);
					$(".pop-apply-hidden").find(".diyname").html(result.upinfo.name);
					$(".pop-apply-hidden").css("display","");
				});
				
			});
			$(".close").unbind('click').click(function(){
				$(".pop-apply-hidden").css("display","none");
			});
			$(".reading").unbind('click').click(function(){
				$(".pop-apply-hidden").css("display","none");
			});
			if(modal.count == 0){
				modal.count = result.count;
			}
			if(modal.need_up_count == 0){
				modal.need_up_count = result.need_up_count;
			}
			$(".need_up_count").html(modal.need_up_count);
			$(".count").html(modal.count);
		})
	};

	modal.getlevel = function() {
		core.json('member/myteam/get_levels', {
		}, function(ret) {
			var list = ret.result;
			modal.levels = list;
			console.log(list);
		})
	};

	modal.showModal = function(type,level,openid) {
		$('.fui-mask-m').show().addClass('visible');
		$('.screen').addClass('in');

		if(type==0){
			$('#selectlevel').show();
			$('#selectspace').hide();
			modal.levels['mylevel']=parseInt($("#level").val());
			console.log(modal.levels);
			modal.levels['otherlevel']=level;

			var title = $('#selectlevel').children("div").get(0);
			$("#selectlevel").empty();
			$("#selectlevel").append(title);
			core.tpl('#selectlevel', 'tpl_level_list', modal.levels, modal.page > 1)
		}else{
			$('#selectspace').show();
			$('#selectlevel').hide();
		}


		$('.screen .cancel').unbind('click').click(function() {
			modal.cancelFilter()
		});
		$('.screen .confirm').unbind('click').click(function() {

			if(type==0){
				var levelid = $("input[name='level']:checked").val();
				if(typeof(levelid)=='undefined'){
					modal.closeFilter();
					return;
				}
				core.json('member/myteam/update', {
					openid: openid,
					levelid: levelid
				}, function(ret) {
					console.log(ret);
					if(ret.status == 0){
						FoxUI.alert(ret.result.message);
					}else{
						var total = ret.result.mycount;
						var html = $("#spacelimit").children("a").get(0);
						$("#spacelimit").empty();
						$("#spacelimit").html("拥有名额："+total+" ");
						$("#spacelimit").append(html);
						modal.total=total;
						FoxUI.alert('调整等级成功');
					}
				})
			}else{
				core.json('member/myteam/give', {
					openid: openid,
					count: modal.count
				}, function(ret) {
					if(ret.status == 0){
						FoxUI.alert(ret.result.message);
					}else{
						var total = ret.result.mycount;
						var html = $("#spacelimit").children("a").get(0);
						$("#spacelimit").empty();
						$("#spacelimit").html("拥有名额："+total+" ");
						$("#spacelimit").append(html);
						modal.total=total
						FoxUI.alert('名额转让成功');
					}
				})
			}


			modal.closeFilter();
			$('.container').html(''), $('.infinite-loading').show(), $(".content-empty").hide();
			modal.page = 1, modal.getList()
		});
		$('.fui-mask-m').unbind('click').click(function() {
			modal.closeFilter()
		})
	};

	modal.cancelFilter = function() {
		modal.closeFilter();
		$('.screen .cate .item nav').removeClass('on');
		$('.screen .cate .item:first-child ~ .item').html('');
		modal.getList()
	};
	modal.closeFilter = function() {
		$('.fui-mask-m').removeClass('visible').transitionEnd(function() {
			$('.fui-mask-m').hide()
		});
		$('.screen').removeClass('in')
	};
	return modal
});