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
            if(n.withdraw<=0){
                 FoxUI.toast.show('提现金额不能为0');
                 return;
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
            if (k == 0) {
                d = l
            } else if (k == 1) {
                d = l
            } else if (k == 2) {
            	d = l
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
            if (k == 1) {
                var m = '提现到余额只能到账50%,确认要'+ d +"?"
            } else {
                var m = '提现到积分将全额到账,确认要' + d+"?"
            }
            FoxUI.confirm(m, function() {
                b.html('正在处理...').attr('stop', 1);
                p.json('commission/queuewithdraw/pay', {
                    type: k,
                    // realname: e,
                    // alipay: f,
                    // alipay1: g,
                    // bankname: h,
                    // bankcard: i,
                    // bankcard1: j
                }, function(a) {
                    if (a.status == 0) {
                        b.removeAttr('stop').html(d);
                        FoxUI.toast.show(a.result.message);
                        location.href = p.getUrl('commission/queue');
                        return
                    }
                    if (a.status == 1) {
                        b.removeAttr('stop').html(d);
                        FoxUI.toast.show(a.result.message);
                        location.href = p.getUrl('commission/queue');
                        return
                    }
                    FoxUI.toast.show('申请已经提交，请等待审核!');
                    location.href = p.getUrl('commission/queue')
                }, true, true)
            })
        })
    };
    return r
});