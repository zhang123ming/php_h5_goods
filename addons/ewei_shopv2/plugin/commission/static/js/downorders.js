define(['core', 'tpl'], function(core, tpl) {
    var modal = {
        page: 1,
        status: 'default',
    };
    modal.init = function(options) {
        modal.page = 1;
        modal.id = options.id;
        $('.fui-content').infinite({
            onLoading: function() {
                modal.getList()
            }
        });
        if (modal.page == 1) {
            modal.getList()
        }
        FoxUI.tab({
            container: $('#tab'),
            handlers: {
                status: function() {
                    modal.changeTab('default', $('#tab').attr('data-id'))
                },
                status0: function() {
                    modal.changeTab(0, $('#tab').attr('data-id'))
                },
                status1: function() {
                    modal.changeTab(1, $('#tab').attr('data-id'))
                },
                status2: function() {
                    modal.changeTab(2, $('#tab').attr('data-id'))
                },
                status3: function() {
                    modal.changeTab(3, $('#tab').attr('data-id'))
                },
            }
        })
    };
    modal.changeTab = function(status, id) {
        $('.fui-content').infinite('init');
        $('.content-empty').hide(), $('.infinite-loading').show(), $('#container').html('');
        modal.page = 1, modal.status = status, modal.id = id, modal.getList()
    };
    modal.loading = function() {
        modal.page++
    };
    modal.changeTotal = function(obj, status) {
        switch (status) {
            case 'default':
                if ($('[data-tab=status]').html() == '所有') {
                    $('[data-tab=status]').append('(' + obj.count + ')');
                    $('.statustotal').html("累计订单金额 : " + obj.total + '元')
                } else {
                    $('.statustotal').html("累计订单金额 : " + obj.total + '元')
                }
                break;
            case 0:
                console.log($('[data-tab=status0]').html());
                if ($('[data-tab=status0]').html() == '待付款') {
                    $('[data-tab=status0]').append('(' + obj.count + ')');
                    $('.statustotal').html("待付款订单金额 : " + obj.total + '元')
                } else {
                    $('.statustotal').html("待付款订单金额 : " + obj.total + '元')
                }
                break;
            case 1:
                if ($('[data-tab=status1]').html() == '待发货') {
                    $('[data-tab=status1]').append('(' + obj.count + ')');
                    $('.statustotal').html("待发货订单金额 : " + obj.total + '元')
                } else {
                    $('.statustotal').html("待发货订单金额 : " + obj.total + '元')
                }
                break;
            case 2:
                if ($('[data-tab=status2]').html() == '待收货') {
                    $('[data-tab=status2]').append('(' + obj.count + ')');
                    $('.statustotal').html("待收货订单金额 : " + obj.total + '元')
                } else {
                    $('.statustotal').html("待收货订单金额 : " + obj.total + '元')
                }
                break;
            case 3:
                if ($('[data-tab=status3]').html() == '已完成') {
                    $('[data-tab=status3]').append('(' + obj.count + ')');
                    $('.statustotal').html("已完成订单金额 : " + obj.total + '元')
                } else {
                    $('.statustotal').html("已完成订单金额 : " + obj.total + '元')
                }
                break
        }
    };
    modal.getList = function() {
        core.json('commission/downorders/get_list', {
            page: modal.page,
            status: modal.status,
            id: modal.id,
        }, function(ret) {
            var result = ret.result;
            if (result.list.length <= 0) {
                $('.content-empty').show();
                $('.fui-content').infinite('stop');
            } else {
                $('.content-empty').hide();
                $('.fui-content').infinite('init');
                if (result.list.length <= 0 || result.list.length < result.pagesize) {
                    $('.fui-content').infinite('stop')
                }

                modal.changeTotal(result, modal.status);
            }
            modal.page++;
            core.tpl('#container', 'tpl_commission_commissionorders_list', result, modal.page);
            FoxUI.according.init()
        })
    };
    return modal
});