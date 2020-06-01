define(['core', 'tpl'], function(core, tpl) {
    var modal = {
        page: 1,
        type: 0
    };
    modal.init = function(params) {
        modal.type = params.type;
        FoxUI.tab({
            container: $('#tab'),
            handlers: {
                tab1: function() {
                    modal.changeTab(0)
                },
                tab2: function() {
                    modal.changeTab(1)
                },
                tab3: function() {
                    modal.changeTab(2)
                },
                tab4: function() {
                    modal.changeTab(3)
                },
                tab5: function() {
                    modal.changeTab(4)
                },
            }
        });
        $('.fui-content').infinite({
            onLoading: function() {
                modal.getList()
            }
        });
        if (modal.page == 1) {
            modal.getList()
        }
    };
    modal.changeTab = function(type) {
        $('.container').html(''), $('.infinite-loading').show(), $('.content-empty').hide(), modal.page = 1, modal.type = type, modal.getList()
    };
    modal.changeTotal = function(obj, status) {
        switch (status) {
            case 0:
                if ($('[data-tab="tab1"]').html() == '所有明细') {
                    $('[data-tab="tab1"]').append('(' + obj.total + ')');
                    $('.statustotal').html("累计金额 : " + obj.sum + '元')
                } else {
                    $('.statustotal').html("累计金额 : " + obj.sum + '元')
                }
                break;
            case 1:
                if ($('[data-tab="tab2"]').html() == '可提现') {
                    $('.tab2').append('(' + obj.total + ')');
                    $('.statustotal').html("可提现金额 : " + obj.sum + '元')
                } else {
                    $('.statustotal').html("可提现金额 : " + obj.sum + '元')
                }
                break;
            case 2:
                if ($('[data-tab="tab3"]').html() == '已提现') {
                    $('[data-tab="tab3"]').append('(' + obj.total + ')');
                    $('.statustotal').html("已提现金额 : " + obj.sum + '元')
                } else {
                    $('.statustotal').html("已提现金额 : " + obj.sum + '元')
                }
                break;
        }
    };
    modal.getList = function() {
        core.json('commission/queue/get_list', {
            page: modal.page,
            type: modal.type,
        }, function(ret) {
            var result = ret.result;
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
            modal.changeTotal(result,result.status);
            modal.page++;
            core.tpl('.container', 'tpl_commission_queue_list', result, modal.page > 1)
        })
    };
    return modal
});