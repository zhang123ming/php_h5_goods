
define(['core', 'tpl', 'biz/goods/picker', 'biz/plugin/diyform','biz/goods/wholesalePicker'], function(core, tpl, picker, wholesalePicker) {
	var defaults = {
		keywords: '',
		isrecommand: '',
		ishot: '',
		isnew: '',
		isdiscount: '',
		issendfree: '',
		istime: '',
		cate: '',
		order: '',
		by: 'desc',
		merchid: 0,
		pid:''
	};
	var modal = {
		page: 1,
		params: {},
		lastcate: false
	};
	modal.init = function(params) {
		
		modal.params = $.extend(defaults, params || {});
		var cid={};
		modal.params['cid']={cid};
		modal.closeFilter();
		var leng = $.trim($('.container').html());
		if (leng == '') {
			modal.page = 1;
			modal.categorylist()
		}
		$('form').submit(function() {
			$('.container').empty();
			modal.params = defaults;
			modal.page = 1;
			modal.params.keywords = $('#search').val();
			modal.categorylist();
			return false
		});
		$('#search').bind('input propertychange', function() {
			if ($.trim($(this).val()) == '') {
				$('.container').empty();
				$('.sort .item-price').removeClass('desc').removeClass('asc');
				modal.params = defaults;
				modal.page = 1;
				modal.params.keywords = '';
				modal.categorylist()
			}
		});
		$('.sort .item').click(function() {
			var keywords = modal.params.keywords;
			var order = $(this).data('order') || '';
			if (order == '') {
				if (modal.params.order == order) {
					return
				}
				modal.params = defaults
			} else if (order == 'minprice') {
				$(this).removeClass('asc').removeClass('desc');
				if (modal.params.order == order) {
					if (modal.params.by == 'desc') {
						modal.params.by = 'asc';
						$(this).addClass('asc')
					} else {
						modal.params.by = 'desc';
						$(this).addClass('desc')
					}
				} else {
					modal.params.by = 'asc';
					$(this).addClass('asc')
				}
				modal.params.order = order
			} else if (order == 'sales') {
				if (modal.params.order == order) {
					return
				}
				modal.params = defaults, modal.params.order = 'sales', modal.params.by = 'desc'
			}
			$('.sort .item.on').removeClass('on'), $(this).addClass('on');
			if (order != 'minprice') {
				$('.sort .item-price').removeClass('desc').removeClass('asc')
			}
			if (order == 'filter') {
				modal.showFilter();
				return
			}
			modal.params.keywords = keywords;
			modal.page = 1;
			$('.container').html(''), $('.infinite-loading').show(), $(".content-empty").hide();
			modal.categorylist()
		});
		$('#listblock').click(function() {
			if ($(this).hasClass('icon-app')) {
				$(this).removeClass('icon-app').addClass('icon-sort')
			} else {
				$(this).removeClass('icon-sort').addClass('icon-app')
			}
			$('.container').toggleClass('block')
		});
		$('.fui-content').infinite({
			onLoading: function() {
				modal.categorylist()
			}
		});
		modal.bindEvents()
	};
	modal.showFilter = function() {
		$('.fui-mask-m').show().addClass('visible');
		$('.screen').addClass('in');
		$('.screen .btn').unbind('click').click(function() {			
			var type = $(this).data('type');
			var cateid = $(this).data('cateid');
			var prentid = $(this).data('cateprent');
			console.log(cateid)
			if ($(this).hasClass('btn-danger-p')) {
				$(this).removeClass('btn-danger-p').addClass('btn-default-op');
				$(this).find('.icon').hide()
			} else {
				$(this).removeClass('btn-default-op').addClass('btn-danger-p');
				$(this).find('.icon').show()
			}
		});
		$('.screen .cancel').unbind('click').click(function() {
			modal.cancelFilter()
		});
		$('.screen .confirm').unbind('click').click(function() {
			var cid = {};
			$('.screen .btn').each(function(i,item) {
				var type = $(this).data('type');
				var cateid = $(this).data('cateid');
				var prentid = $(this).data('prentid');
				if ($(this).hasClass('btn-danger-p')) {
					modal.params[type] = "1";
					if (cid[[prentid]]) {
						cid[prentid] +=","+cateid ;
					}else{
						cid[prentid] =cateid ;
					}
					
				} else {
					modal.params[type] = "";
				}
			});			
			modal.params['cid']={cid};
			console.log(modal.params);
			modal.closeFilter();
			$('.container').html(''), $('.infinite-loading').show(), $(".content-empty").hide();
			modal.page = 1, modal.categorylist()
		});
		$('.fui-mask-m').unbind('click').click(function() {
			modal.closeFilter()
		})
	};
	modal.cancelFilter = function() {
		modal.closeFilter();
		$('.screen .btn').each(function() {
			if ($(this).hasClass('btn-danger-p')) {
				$(this).removeClass('btn-danger-p').addClass('btn-default-op');
				$(this).find('.icon').hide();
				modal.params[$(this).data('type')] = ""

			}
		});
		var cid={}
		modal.params['cid']={cid};
		defaults.cate = modal.params.categoryid;
		console.log(modal.params,defaults);
		modal.params = defaults, modal.categorylist()
	};
	modal.closeFilter = function() {
		$('.fui-mask-m').removeClass('visible').transitionEnd(function() {
			$('.fui-mask-m').hide()
		});
		$('.screen').removeClass('in')
	};
	modal.bindEvents = function() {		
		$('.catebuy').unbind('click').click(function() {	
			var pid=modal.params.pid;
			var openid=modal.params.openid;
			var categoryid = modal.params.categoryid;
			var goodsid = $(this).closest('.fui-goods-item').data('goodsid');
			var type = $(this).closest('.fui-goods-item').data('type');
			
			if (type == 20) {
				location.href = core.getUrl('goods/categorydetail', {
					goodsid: goodsid,
					marketprice: marketprice,
					pid:pid,
				})
			} else {
				if ($(this).data('type') == 4) {
					wholesalePicker.open({
						goodsid: goodsid,
					})
				} else {
					picker.open({
						pid:pid,
						goodsid: goodsid,
						total: 1,
						optionid: 0,
						categoryid:categoryid,
						openid:openid,
					})
				}
			}
		})
	};
	modal.categorylist = function() {
		modal.params.page = modal.page;
		modal.params['cid'] = JSON.stringify(modal.params['cid']);
		console.log(modal.params);
		core.json('goods/categorydetail/categorylist',modal.params, function(ret) {
			$('.infinite-loading').hide();
			var result = ret.result;
			console.log(result.total);
			if (result.total <= 0) {
				$('.content-empty').show();
				$('.fui-content').infinite('stop')
			} else {
				$('.content-empty').hide();
				$('.fui-content').infinite('init');
				if (result.list.length <= 0 || result.list.length < result.pagesize) {
					modal.stopLoading = true;
					$('.fui-content').infinite('stop')
				}
			}
			modal.page++;
			core.tpl('.container', 'tpl_goods_list', result, modal.page > 1);
			modal.bindEvents()
		})
	};
	return modal
});