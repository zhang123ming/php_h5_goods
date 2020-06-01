define(['core', 'tpl'], function(core, tpl) {
    var modal = { page: 1, keyword: '', cateid: 0 };
    modal.init = function(params) {
        modal.keyword = params.keyword ? params.keyword : '';
        modal.cateid = params.cateid ? params.cateid : 0;
        modal.page = 1;
        modal.lat = '';
        modal.lng = '';
        modal.range = 2000;
        modal.sorttype = 0;
        isscroll = true;

        if (modal.cateid > 0) {
            $('.sortmenu_cate ul li').each(function() {
                if ($(this).attr('cateid') == modal.cateid) {
                    $('#sortmenu_cate_text').html($(this).attr('text'));
                    isscroll = false;
                }
            });
        }


        $(".sortMenu > li").off("click").on("click", function() {
            var menuclass = $(this).attr("data-class");
            if ($("." + menuclass + "").css("display") == "none") {
                $(".sortMenu > div").hide();
                $("." + menuclass + "").show();
                $(".sort-mask").show();
                isscroll = false;
            } else {
                $("." + menuclass + "").hide();
                $(".sort-mask").hide();
                isscroll = false;
            }

        });

        $(".sort-mask").off("click").on("click", function() {
            $(this).hide();
            $(".sortMenu > div").hide();
            isscroll = false;
        });

        $('.sortmenu_rule ul li').click(function() {
            modal.range = $(this).attr('range');
            var text = $(this).attr('text');
            $('#sortmenu_rule_text').html(text);
            $('.sortmenu_rule').hide();
            modal.page = 1;
            $(".container").empty();
            $(".sort-mask").hide();
            $(".sortMenu > div").hide();
            isscroll = true;
            modal.getList()
        });

        $('.sortmenu_cate ul li').click(function() {
            modal.cateid = $(this).attr('cateid');
            var text = $(this).attr('text');
            $('#sortmenu_cate_text').html(text);
            $('.sortmenu_cate').hide();
            modal.page = 1;
            $(".container").empty();
            $(".sort-mask").hide();
            $(".sortMenu > div").hide();
            isscroll = true;
            modal.getList()
        });

        $('.sortmenu_sort ul li').click(function() {
            modal.sorttype = $(this).attr('sorttype');
            var text = $(this).attr('text');
            $('#sortmenu_sort_text').html(text);
            $('.sortmenu_sort').hide();
            modal.page = 1;
            $(".container").empty();
            $(".sort-mask").hide();
            $(".sortMenu > div").hide();
            isscroll = true;
            modal.getList()
        });


        $('.fui-content').infinite({
            onLoading: function() {
                modal.getList()
            }
        });

        $('#submit').click(function() {
            modal.keyword = $('.search').val();
            isscroll = true;
            modal.getList()
        });

        if (modal.page == 1) {
            modal.getList()
        }
    };
    modal.getList = function() {
        
        var geolocation = new BMap.Geolocation();
        console.log(geolocation);
        geolocation.getCurrentPosition(function(r) {
            var _this = this;
            if (this.getStatus() == BMAP_STATUS_SUCCESS) {
                modal.lat = r.point.lat;
                modal.lng = r.point.lng;
            }

            core.json('store/getstorelist', { page: modal.page, keyword: modal.keyword, lat: modal.lat, lng: modal.lng, range: modal.range, sorttype: modal.sorttype }, function(ret) {
                var result = ret.result;
                console.log(result)
                if (result.total <= 0) {
                    $('.content-empty').show();
                    // $('.container').hide();
                    $('.fui-content').infinite('stop')
                } else {
                    $('.content-empty').hide(); 
                    $('.container').show();
                    $('.fui-content').infinite('init');
                    if (result.list.length <= 0 || result.list.length < result.pagesize) {
                        $('.fui-content').infinite('stop')
                    }
                }
                console.log(isscroll);
                if(isscroll){
                    modal.page++;
                }else{
                    modal.page=1;
                }
                core.tpl('.container', 'tpl_merch_list_user', result, modal.page > 2);
            }, { enableHighAccuracy: true })
        })
    };

    return modal
});