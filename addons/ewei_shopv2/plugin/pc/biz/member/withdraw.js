define(['core', 'tpl'], function(core, tpl) {
    var modal = {};
    modal.allow = function() {
        if (!$('#money').isNumber() || $('#money').isEmpty()) {
            return false
        } else {
            var money = parseFloat($('#money').val());
            if (money <= 0) {
                return false
            }
            if (modal.min > 0) {
                if (money < modal.min) {
                    return false
                }
            }
            if (money > modal.max) {
                return false
            }
        } if (modal.withdrawcharge > 0 && money != 0) {
            var deductionmoney = money / 100 * modal.withdrawcharge;
            deductionmoney = Math.round(deductionmoney * 100) / 100;
            if (deductionmoney >= modal.withdrawbegin && deductionmoney <= modal.withdrawend) {
                deductionmoney = 0
            }
            var realmoney = money - deductionmoney;
            realmoney = Math.round(realmoney * 100) / 100;
            $("#deductionmoney").html(deductionmoney);
            $("#realmoney").html(realmoney);
            $(".charge-group").show()
        }
        return true
    };
    modal.init = function(params) {
        modal.withdrawcharge = params.withdrawcharge;
        modal.withdrawbegin = params.withdrawbegin;
        modal.withdrawend = params.withdrawend;
        modal.min = params.min;
        modal.max = params.max;
        $('#btn-all').click(function() {
            if (modal.max <= 0) {
                return
            }
            $('#money').val(modal.max);
            if (!modal.allow()) {
                $('#btn-next').addClass('disabled')
            } else {
                $('#btn-next').removeClass('disabled')
            }
        });
        $('#money').bind('input propertychange', function() {
            if (!modal.allow()) {
                $('#btn-next').addClass('disabled')
            } else {
                $('#btn-next').removeClass('disabled')
            }
        });
        $('#btn-next').click(function() {
            var money = $.trim($('#money').val());
            if ($(this).attr('submit')) {
                return
            }
            if (!modal.allow()) {
                return
            }
            if ($('.btn-withdraw').attr('submit')) {
                return
            }
            var money = $('#money').val();
            if (!$('#money').isNumber()) {
                FoxUI.toast.show('请输入提现金额!');
                return
            }
            var msg = '确认要提现 ' + money + ' 元吗?';
            if (modal.withdrawcharge > 0) {
                msg += ' 扣除手续费 ' + $("#deductionmoney").html() + ' 元,实际到账金额 ' + $("#realmoney").html() + ' 元'
            }
            FoxUI.confirm(msg, function() {
                $('.btn-withdraw').attr('submit', 1);
                core.json('member/withdraw/submit', {
                    money: money
                }, function(rjson) {
                    if (rjson.status != 1) {
                        $('.btn-widthdraw').removeAttr('submit');
                        FoxUI.toast.show(rjson.result.message);
                        return
                    }
                    FoxUI.toast.show('提现申请成功，请等待审核!');
                    location.href = core.getUrl('member/log', {
                        type: 1
                    })
                }, true, true)
            })
        })
    };
    return modal
});