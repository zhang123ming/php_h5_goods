define(['core', 'tpl'], function(core, tpl) {
    var modal = {
        page: 1,
        cateid: 0
    };
    modal.init = function(params) {
        modal.template = params.template;
		modal.cateid = params.cateid;
        FoxUI.loader.show('mini');
        if (modal.template == 2) {
            if (!modal.toBottledoctor) {
                modal.page = 1;
                //modal.cateid =0;
            } else {
                modal.toBottledoctor = false
            }
            $(document).off('click', "#listTab .item").on('click', "#listTab .item", function() {
                var left = 0;
                var tab = $(this).closest(".fui-tab-scroll");
                var container = tab.find(".container");
                var cateid = $(this).data('cateid');
                modal.page = 1;
                modal.cateid = cateid;
                $(this).addClass('on').siblings().removeClass('on');
                if (container.length > 0) {
                    left = container.scrollLeft()
                }
                tab.html(tab.html());
                tab.find(".container").scrollLeft(left);
                FoxUI.loader.show('mini');
                $("#container").html("");
                modal.getList()
            });
            modal.bindEvents()
        }
        $('.fui-content').infinite({
            onLoading: function() {
                modal.getList()
            }
        });
        modal.getList()
    };
    modal.bindEvents = function() {
        $("#container .fui-list").click(function() {
            modal.toBottledoctor = true
        })
    };
    modal.getList = function() {
        var data = {
            page: modal.page
        };
        if (modal.template == 2) {
            data.cateid = modal.cateid
        }
        if (modal.page > 1) {
            $(".infinite-loading").show()
        }
        $(".content-empty").hide();
        $("#container").show();
        $.get(core.getUrl('bottledoctor/list/getlist'), data, function(html) {
            FoxUI.loader.hide();
            if (html != '') {
                modal.page++;
                $("#container").append(html);
                $('.fui-content').infinite('init');
                modal.bindEvents()
            } else {
                $('.fui-content').infinite('stop');
                $(".infinite-loading").hide();
                if (modal.page == 1) {
                    $(".content-empty").show();
                    $("#container").hide()
                }
            }
        })
    };
    return modal
});