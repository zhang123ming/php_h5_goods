define(['core', 'tpl'], function(core, tpl) {
    var modal = {};
    modal.init = function(params) {
        modal.params = params;
        modal.params.level = 0;
        var loadAddress = false;
        if (typeof(window.selectedAddressData) !== 'undefined') {
            loadAddress = window.selectedAddressData
        } else if (typeof(window.editAddressData) !== 'undefined') {
            loadAddress = window.editAddressData;
            loadAddress.address = loadAddress.areas.replace(/ /ig, '') + ' ' + loadAddress.address
        }
        if (loadAddress) {
            modal.params.addressid = loadAddress.id;
            $('#addressInfo .has-address').show();
            $('#addressInfo .no-address').hide();
            $('#addressInfo .icon-dingwei').show();
            $('#addressInfo .realname').html(loadAddress.realname);
            $('#addressInfo .mobile').html(loadAddress.mobile);
            $('#addressInfo .address').html(loadAddress.address);
            $('#addressInfo a').attr('href', core.getUrl('member/address/selector'));
            $('#addressInfo a').click(function() {
                window.orderSelectedAddressID = loadAddress.id
            })
        }
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
  			var images = $("[type=hidden]").val();
            if(modal.params.addressid==undefined){
                 if($(this).data('addressid')){
                    var addressid = $(this).data('addressid');
                 }else{
                    FoxUI.toast.show('请添加收货地址');
                    return ;
                 }
            }else{
                var addressid = modal.params.addressid;
            }
            if(images==undefined){
                FoxUI.toast.show('请上传转账凭证');
                return ;
            }
            var setnumber = $('#put-number').val();
            var memberid = $(this).data('memberid');
            var opthions = $(this).data('opthions');
            core.json('member/myyuncang/submit', {
                'id': memberid,
                'number':setnumber,
                'images': images,
                'opthions':opthions,
                'addressid':addressid
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