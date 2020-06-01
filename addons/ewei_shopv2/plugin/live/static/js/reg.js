define(['core','tpl'],function(core,tpl){
    var modal = {};
    modal.init= function(params){
        modal.params = $.extend(modal.params, params || {});
        $('.fui-uploader').uploader({
            uploadUrl: core.getUrl('util/uploader'),
            removeUrl: core.getUrl('util/uploader/remove')
        });

        $('.btn-submit').click(function () {
            var btn = $(this);
            if (btn.attr('stop')) {
                return
            }
            var html = btn.html();
            if ($('#liveidentity').isEmpty()) {
                FoxUI.toast.show('请选择直播平台');
                return
            }
            if($('#liveurl').isEmpty()){
                FoxUI.toast.show('请填写直播地址!');
                return
            }
            if ($('#hostname').isEmpty()) {
                FoxUI.toast.show('请填写主播名称!');
                return
            }

            if ($('#salecate').isEmpty()) {
                console.log($('#salecate'))
                FoxUI.toast.show('请选择直播分类!');
                return
            }
            if ($('#realname').isEmpty()) {
                FoxUI.toast.show('请填写联系人!');
                return
            }
            if (!$('#phone').isMobile()) {
                FoxUI.toast.show('请填写联系人手机!');
                return
            }
            // if ($('#imgFile0').isEmpty()) {
            //     FoxUI.toast.show('请上传身份证正面!');
            //     return
            // }
            // if ($('#imgFile1').isEmpty()) {
            //     FoxUI.toast.show('请上传身份证反面!');
            //     return
            // }
            var data = {
                'liveidentity':$('#liveidentity').val(),
                'liveurl':$('#liveurl').val(),
                'hostname':$('#hostname').val(),
                'salecate': $('#salecate').val(),
                'realname': $('#realname').val(),
                'phone': $('#phone').val(),
                'idcard1':  $('#image1').find('li').data('filename'),
                'idcard2': $('#image2').find('li').data('filename'),

            }



            btn.attr('stop', 1).html('正在处理...');
            core.json('live/reg', data, function (pjson) {
                if (pjson.status == 0) {
                    btn.removeAttr('stop').html(html);
                    FoxUI.toast.show(pjson.result.message);
                    return
                }

                FoxUI.message.show({
                    icon: 'icon icon-info text-warning',
                    content: "您的申请已经提交，请等待我们联系您!",
                    buttons: [{
                        text: '先去商城逛逛', extraClass: 'btn-danger', onclick: function () {
                            location.href = core.getUrl('')
                        }
                    }]
                });

            }, true, true)
        });
    };
    return modal
});