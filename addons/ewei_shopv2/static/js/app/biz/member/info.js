define(['core', 'tpl'], function(core, tpl) {
	var modal = {};
	modal.initPost = function(params) {
		var reqParams = ['foxui.picker'];
		if (params.new_area) {
			reqParams = ['foxui.picker', 'foxui.citydatanew']
		}
		require(reqParams, function() {
			$('#areas').cityPicker({
				title: '请选择所在城市',
				new_area: params.new_area,
				address_street: params.address_street,
				onClose: function(self) {
					var datavalue = $('#areas').attr('data-value');
					var codes = datavalue.split(' ');
					if (params.new_area && params.address_street) {
						var city_code = codes[1];
						var area_code = codes[2];
						city_code = city_code + '';
						area_code = area_code + '';
						var data = loadStreetData(city_code, area_code);
						var street = $('<input type="text" id="street"  name="street" data-value="" value="" placeholder="所在街道"  class="fui-input" readonly=""/>');
						var parents = $('#street').closest('.fui-cell-info');
						$('#street').remove();
						parents.append(street);
						street.cityPicker({
							title: '请选择所在街道',
							street: 1,
							data: data
						})
					}
				}
			});
			if (params.new_area && params.address_street) {
				var datavalue = $('#areas').attr('data-value');
				if (datavalue) {
					var codes = datavalue.split(' ');
					var city_code = codes[1];
					var area_code = codes[2];
					var data = loadStreetData(city_code, area_code);
					$('#street').cityPicker({
						title: '请选择所在街道',
						street: 1,
						data: data
					})
				}
			}
		});
		// $(document).on('click', '#btn-address', function() {
		// 	wx.openAddress({
		// 		success: function(res) {
		// 			$("#realname").val(res.userName);
		// 			$('#mobile').val(res.telNumber);
		// 			$('#address').val(res.detailInfo);
		// 			$('#areas').val(res.provinceName + " " + res.cityName + " " + res.countryName)
		// 		}
		// 	})
		// });
		// $(document).on('click', '#btn-submit', function() {
		// 	if ($(this).attr('submit')) {
		// 		return
		// 	}
		// 	if ($('#realname').isEmpty()) {
		// 		FoxUI.toast.show("请填写收件人");
		// 		return
		// 	}
		// 	var jingwai = /(境外地区)+/.test($('#areas').val());
		// 	var taiwan = /(台湾)+/.test($('#areas').val());
		// 	var aomen = /(澳门)+/.test($('#areas').val());
		// 	var xianggang = /(香港)+/.test($('#areas').val());
		// 	if (jingwai || taiwan || aomen || xianggang) {
		// 		if ($('#mobile').isEmpty()) {
		// 			FoxUI.toast.show("请填写手机号码");
		// 			return
		// 		}
		// 	} else {
		// 		if (!$('#mobile').isMobile()) {
		// 			FoxUI.toast.show("请填写正确手机号码");
		// 			return
		// 		}
		// 	}
		// 	if ($('#areas').isEmpty()) {
		// 		FoxUI.toast.show("请填写所在地区");
		// 		return
		// 	}
		// 	if ($('#address').isEmpty()) {
		// 		FoxUI.toast.show("请填写详细地址");
		// 		return
		// 	}
		// 	$('#btn-submit').html('正在处理...').attr('submit', 1);
		// 	window.editAddressData = {
		// 		realname: $('#realname').val(),
		// 		mobile: $('#mobile').val(),
		// 		address: $('#address').val(),
		// 		areas: $('#areas').val(),
		// 		street: $('#street').val(),
		// 		streetdatavalue: $('#street').attr('data-value'),
		// 		datavalue: $('#areas').attr('data-value'),
		// 		isdefault: $('#isdefault').is(':checked') ? 1 : 0
		// 	};
		// 	core.json('member/address/submit', {
		// 		id: $('#addressid').val(),
		// 		addressdata: window.editAddressData
		// 	}, function(json) {
		// 		$('#btn-submit').html('保存地址').removeAttr('submit');
		// 		window.editAddressData.id = json.result.addressid;
		// 		if (json.status == 1) {
		// 			FoxUI.toast.show('保存成功!');
		// 			history.back()
		// 		} else {
		// 			FoxUI.toast.show(json.result.message)
		// 		}
		// 	}, true, true)
		// })
	};
	modal.init = function(params) {
		params = $.extend({
			returnurl: '',
			template_flag: 0,
			new_area: 0
		}, params || {});
		if (typeof(window.memberData) !== 'undefined') {
			if (memberData.avatar) {
				$(".avatar").attr('src', memberData.avatar)
			}
			if (memberData.nickname) {
				$(".nickname").text(memberData.nickname)
			}
		}
		var reqParams = ['foxui.picker'];
		if (params.new_area) {
			reqParams = ['foxui.picker', 'foxui.citydatanew']
		}
		require(reqParams, function() {
			$('#city').cityPicker({
				new_area: params.new_area,
				showArea: true
			});
			$('#birthday').datePicker()
		});
		$('#btn-submit').click(function() {
			var postdata = {};
			if (params.template_flag == 0) {
				if ($('#realname').isEmpty()) {
					FoxUI.toast.show('请填写姓名');
					return
				}
				if (!$('#mobile').isMobile() && !params.wapopen) {
					FoxUI.toast.show('请填写正确手机号码');
					return
				}
				if ($(this).attr('submit')) {
					return
				}
				var birthday = $('#birthday').val().split('-');
				var citys = $('#city').val().split(' ');
				$(this).html('处理中...').attr('submit', 1);

				postdata = {
					'memberdata': {
						'realname': $('#realname').val(),
						'weixin': $('#weixin').val(),
						'gender': $('#sex').val(),
						'birthyear': $('#birthday').val().length > 0 ? birthday[0] : 0,
						'birthmonth': $('#birthday').val().length > 0 ? birthday[1] : 0,
						'birthday': $('#birthday').val().length > 0 ? birthday[2] : 0,
						'province': $('#city').val().length > 0 ? citys[0] : '',
						'city': $('#city').val().length > 0 ? citys[1] : '',
						'area': $('#city').val().length > 0 ? citys[2] : '',
						'datavalue': $('#city').attr('data-value')
					},
					'mcdata': {
						'realname': $('#realname').val(),
						'gender': $('#sex').val(),
						'birthyear': $('#birthday').val().length > 0 ? birthday[0] : 0,
						'birthmonth': $('#birthday').val().length > 0 ? birthday[1] : 0,
						'birthday': $('#birthday').val().length > 0 ? birthday[2] : 0,
						'resideprovince': $('#city').val().length > 0 ? citys[0] : '',
						'residecity': $('#city').val().length > 0 ? citys[1] : ''
					}
				};
				if (!params.wapopen) {
					postdata.memberdata.mobile = $('#mobile').val();
					postdata.mcdata.mobile = $('#mobile').val()
				}				
				core.json('member/info/submit', postdata, function(json) {
					modal.complete(params, json)
				}, true, true)
			} else {
				FoxUI.loader.show('mini');
				$(this).html('处理中...').attr('submit', 1);
				require(['biz/plugin/diyform'], function(diyform) {
					postdata = diyform.getData('.diyform-container');
					FoxUI.loader.hide();
					if (postdata) {
						core.json('member/info/submit', {
							memberdata: postdata
						}, function(json) {
							modal.complete(params, json)
						}, true, true)
					} else {
						$('#btn-submit').html('确认修改').removeAttr('submit')
					}
				})
			}
		})
	};
	modal.complete = function(params, json) {
		FoxUI.loader.hide();
		if (json.status == 1) {
			FoxUI.toast.show('保存成功');
			if (params.returnurl) {
				location.href = params.returnurl
			} else {
				history.back()
			}
		} else {
			$('#btn-submit').html('确认修改').removeAttr('submit');
			FoxUI.toast.show('保存失败!')
		}
	};
	modal.initFace = function() {
		$("#btn-getinfo").unbind('click').click(function() {
			FoxUI.confirm("确认使用微信昵称、头像吗？<br>使用微信资料保存后才生效", function() {
				var nickname = $.trim($("#nickname").data('wechat'));
				var avatar = $.trim($("#avatar").data('wechat'));
				$("#nickname").val(nickname);
				$("#avatar").attr('src', avatar).data('filename', avatar)
			})
		});
		$("#file-avatar").change(function() {
			var fileid = $(this).attr('id');
			FoxUI.loader.show('mini');
			$.ajaxFileUpload({
				url: core.getUrl('util/uploader'),
				data: {
					file: fileid
				},
				secureuri: false,
				fileElementId: fileid,
				dataType: 'json',
				success: function(res) {
					if (res.error == 0) {
						$("#avatar").attr('src', res.url).data('filename', res.filename)
					} else {
						FoxUI.toast.show("上传失败请重试")
					}
					FoxUI.loader.hide();
					return
				}
			})
		});
		$("#btn-submit").unbind('click').click(function() {
			var _this = $(this);
			if (_this.attr('stop')) {
				FoxUI.toast.show("保存中...");
				return
			}
			var nickname = $.trim($("#nickname").val());
			var mobile = $.trim($("#mobile").val());
			var areas = $.trim($("#areas").val());
			var street = $.trim($("#street").val());
			var address = $.trim($("#address").val());
			var streetdatavalue= $('#street').attr('data-value');
			var datavalue= $('#areas').attr('data-value');
			var avatar = $.trim($("#avatar").data('filename'));
			if (nickname == '') {
				FoxUI.toast.show("请填写昵称");
				return
			}
			if (avatar == '') {
				FoxUI.toast.show("请选择头像");
				return
			}
			_this.attr('stop', 1);
			core.json('member/info/face', {
				nickname: nickname,
				avatar: avatar,
				mobile:mobile,
				areas:areas,
				street:street,
				address:address,
				datavalue:datavalue

			}, function(json) {
				if (json.status == 0) {
					FoxUI.toast.show(json.result.message)
				} else {
					window.memberData = {
						nickname: nickname,
						avatar: $.trim($("#avatar").attr('src'))
					};
					window.history.back()
				}
				_this.removeAttr('stop')
			}, true, true)
		})
	};
	return modal
});