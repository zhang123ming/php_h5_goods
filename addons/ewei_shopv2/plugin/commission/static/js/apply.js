define(['core', 'tpl'], function(p, q) {
    var r = {};
    r.init = function(n) {
        var o = $('#applytype').data("type");
        if (o == 2) {
            $('.ab-group').show();
            $('.ab-group2').hide();
            $('.alipay-group').show();
            $('.bank-group').hide()
        } else if (o == 3) {
            $('.ab-group2').show();
            $('.ab-group').hide();
            $('.alipay-group').hide();
            $('.bank-group').show()
        } else {
            $('.ab-group').hide();
            $('.alipay-group').hide();
            $('.bank-group').hide()
        }
        $('.applyradio').click(function() {
            var a = $('.applyradio');
            for (var i = 0; i < a.length; i++) {
                a.eq(i).find(".fui-radio").attr("id", "");
                a.eq(i).find(".fui-radio").removeAttr("checked")
            }
            $(this).find(".fui-radio").attr("id", "applytype");
            $(this).find(".fui-radio").prop("checked", "checked");
            var b = $(this).find(".fui-radio").data("type");
            if (b == 2) {
                $('.ab-group').show();
                $('.ab-group2').hide();
                $('.alipay-group').show();
                $('.bank-group').hide()
            } else if (b == 3) {
                $('.ab-group2').show();
                $('.ab-group').hide();
                $('.alipay-group').hide();
                $('.bank-group').show()
            } else {
                $('.ab-group2').hide();
                $('.ab-group').hide();
                $('.alipay-group').hide();
                $('.bank-group').hide()
            }
        });
        $('#chargeinfo').click(function() {
            $('.charge-group').toggle()
        });
        $('.btn-submit').click(function() {
            var b = $(this);
            if (b.attr('stop')) {
                return
            }
            var c = p.getNumber($('#current').html());
            if (c < n.withdraw) {
                FoxUI.toast.show('满 ' + n.withdraw + ' 元才能提现!');
                return
            }
            var d = '';
            var e = '';
            var f = '';
            var g = '';
            var h = '';
            var i = '';
            var j = '';
            var k = $('#applytype').data("type");
            var l = $('#applytype').closest(".fui-cell").find(".fui-cell-info").html();
            if (k == undefined) {
                FoxUI.toast.show('未选择提现方式，请您选择提现方式后重试!');
                return
            }
            if(n.open_withdrawprotocol==1){
            	if($('.open_withdrawprotocol').prop('checked')==false){
            		FoxUI.toast.show('请阅读并了解'+$('#btn-apply').html()+'!');
                	return
            	}
            }
            if (k == 0) {
                d = l
            } else if (k == 1) {
                d = l
            } else if (k == 2) {
                if ($('#realname').isEmpty()) {
                    FoxUI.toast.show('请填写姓名!');
                    return
                }
                if ($('#alipay').isEmpty()) {
                    FoxUI.toast.show('请填写支付宝帐号!');
                    return
                }
                if ($('#alipay1').isEmpty()) {
                    FoxUI.toast.show('请填写确认帐号!');
                    return
                }
                if ($('#alipay').val() != $('#alipay1').val()) {
                    FoxUI.toast.show('支付宝帐号与确认帐号不一致!');
                    return
                }
                e = $('#realname').val();
                f = $('#alipay').val();
                g = $('#alipay1').val();
                d = l + "?<br>姓名:" + e + "<br>支付宝帐号:" + f
            } else if (k == 3) {
                if ($('#realname2').isEmpty()) {
                    FoxUI.toast.show('请填写姓名!');
                    return
                }
                if ($('#bankcard').isEmpty()) {
                    FoxUI.toast.show('请填写银行卡号!');
                    return
                }
                if (!$('#bankcard').isNumber()) {
                    FoxUI.toast.show('银行卡号格式不正确!');
                    return
                }
                if ($('#bankcard1').isEmpty()) {
                    FoxUI.toast.show('请填写确认卡号!');
                    return
                }
                if ($('#bankcard').val() != $('#bankcard1').val()) {
                    FoxUI.toast.show('银行卡号与确认卡号不一致!');
                    return
                }
                e = $('#realname2').val();
                i = $('#bankcard').val();
                j = $('#bankcard1').val();
                h = $('#bankname').find("option:selected").html();
                d = l + "?<br>姓名:" + e + "<br>" + h + " 卡号:" + $('#bankcard').val()
            }
            if (k < 2) {
                var m = '确认要' + d + "?"
            } else {
                var m = '确认要' + d
            }
            if(n.uniacid==103){
                m = '提现到余额,只能到账65%的余额及15%的积分,确认要提现吗?'
            }
            FoxUI.confirm(m, function() {
                b.html('正在处理...').attr('stop', 1);
                p.json('commission/apply', {
                    type: k,
                    realname: e,
                    alipay: f,
                    alipay1: g,
                    bankname: h,
                    bankcard: i,
                    bankcard1: j
                }, function(a) {
                    if (a.status == 0) {
                        b.removeAttr('stop').html(d);
                        FoxUI.toast.show(a.result.message);
                        location.href = p.getUrl('commission/withdraw');
                        return
                    }
                    if (a.status == 1) {
                        b.removeAttr('stop').html(d);
                        FoxUI.toast.show(a.result.message);
                        location.href = p.getUrl('commission/withdraw');
                        return
                    }
                    FoxUI.toast.show('申请已经提交，请等待审核!');
                    location.href = p.getUrl('commission/withdraw')
                }, true, true)
            })
        })
        $("#btn-apply").unbind('click').click(function() {
            var html = $(".pop-apply-hidden").html();
            container = new FoxUIModal({
                content: html,
                extraClass: "popup-modal",
                maskClick: function() {
                    container.close()
                }
            });
            container.show();
            $('.verify-pop').find('.close').unbind('click').click(function() {
                container.close()
            });
            $('.verify-pop').find('.btn').unbind('click').click(function() {
                container.close()
            })
        })
    };
    return r
});