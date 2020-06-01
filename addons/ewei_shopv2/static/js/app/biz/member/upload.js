define(['core', 'tpl'], function(core, tpl) {
    var modal = {};
    modal.init = function(params) {
        modal.params = params;
        modal.params.level = 0;
        $('.fui-stars').stars({
            'clearIcon': 'icon icon-round_close',
            'icon': 'icon icon-star',
            'selectedIcon': 'icon icon-xing',
            'onSelected': function(value) {
                modal.params.level = value
            }
        });
        $('.fui-uploader').uploader({
            uploadUrl: core.getUrl('util/uploader'),
            removeUrl: core.getUrl('util/uploader/remove')
        });
        $(".fui-images").off("click").on("click", function() {
            if ($(this).find("img")) {
                core.showImages('.fui-images li img')
            }
        });
        $('.btn_query').click(function() {
  			var images = [];
            $("[type=hidden]").each(function(i){
            	images.push($("[type=hidden]").eq(i).val());
            });
            if(images.length<=0){
                FoxUI.toast.show('请上传转账凭证');
                return ;
            }
            if($('#put-number').val()==''){
                FoxUI.toast.show('充值金额不能为空');
                return ;
            }
            var setnumber = $('#put-number').val();
            var memberid = $(this).data('memberid');
            core.json('member/collect/submit', {
                'id': memberid,
                'number':setnumber,
                'images': images
            }, function(ret) {
                if (ret.status == 1) {
                    var res = ret.result;
                	FoxUI.toast.show(res.remark);
                    setTimeout(function(){
                        window.location.href=res.url;
                    },1000);
                    return;
                }
                FoxUI.toast.show(ret.result.message)
            }, true, true)
        })
    };
    return modal
});