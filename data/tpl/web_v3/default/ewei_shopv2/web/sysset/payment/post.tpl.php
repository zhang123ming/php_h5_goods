<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>
<style>
    .form-horizontal .form-group {
        margin-right: -50px;
    }

    .col-sm-9 {
        padding-right: 0;
    }

    .tm .btn {
        margin-bottom: 5px;
    }

    .table > tbody > tr > td, .table > tbody > tr > th, .table > tfoot > tr > td, .table > tfoot > tr > th, .table > thead > tr > td, .table > thead > tr > th {
        padding: 0;
        margin: 0;
        border: 0;
        text-overflow: clip;
    }
</style>

<div class="page-header">
    当前位置：
    <span class="text-primary"><?php  if(!empty($item['id'])) { ?>编辑<?php  } else { ?>添加<?php  } ?>支付信息
        <small><?php  if(!empty($item['id'])) { ?>修改【<?php  echo $item['title'];?>】<?php  } ?></small>
    </span>
</div>

<div class="page-content">
    <div class="row">
        <div class="col-sm-12">
            <form <?php if( ce('sysset.payment' ,$list) ) { ?>action="" method="post"<?php  } ?> class="form-horizontal form-validate" enctype="multipart/form-data">
                <div class="form-group">
                    <label class="col-lg control-label must">支付名称</label>
                    <div class="col-sm-9 col-xs-12">
                        <?php if( ce('sysset.payment' ,$data) ) { ?>
                        <input type="text" name="title" class="form-control" value="<?php  echo $data['title'];?>" placeholder="方便选择与记忆的支付名称" data-rule-required='true'/>
                        <?php  } else { ?>
                        <div class='form-control-static'><?php  echo $data['title'];?></div>
                        <?php  } ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg control-label">支付方式</label>
                    <div class="col-sm-9 col-xs-12">
                        <?php if( ce('sysset.payment' ,$data) ) { ?>
                        <label class="radio-inline"><input type="radio" name="paytype" value="0" <?php  if(empty($data['paytype'])) { ?>checked="true"<?php  } ?>/> 微信支付</label>
                        <label class="radio-inline"><input type="radio" name="paytype" value="1" <?php  if($data['paytype'] == 1) { ?>checked="true"<?php  } ?>/> 支付宝支付</label>
                        <?php  } else { ?>
                        <div class='form-control-static'><?php  if(empty($item['paytype'])) { ?>微信支付<?php  } else { ?>支付宝支付<?php  } ?></div>
                        <?php  } ?>
                    </div>
                </div>
            <div id="wechat" style="<?php  if($data['paytype'] == 1) { ?>display: none;<?php  } ?>">
                <div class="form-group">
                    <label class="col-lg control-label must">支付类型</label>
                    <div class="col-sm-9 col-xs-12">
                        <?php if( ce('sysset.payment' ,$data) ) { ?>
                        <select class="form-control tpl-category-parent" name="type" id="type">
                            <?php  if(is_array($payment)) { foreach($payment as $key => $val) { ?>
                            <option value="<?php  echo $key;?>" <?php  if($data['type']==$key) { ?>selected="true"<?php  } ?>><?php  echo $val;?></option>
                            <?php  } } ?>
                        </select>
                        <?php  } else { ?>
                        <div class='form-control-static'><?php  echo $payment[$data['type']];?></div>
                        <?php  } ?>
                    </div>
                </div>

                <div class="form-group hidden merch">
                    <label class="col-lg control-label must">服务商公众号(AppId)</label>
                    <div class="col-sm-9">
                        <?php if(cv('sysset.payment.edit')) { ?>
                        <input type="text" name="appid" class="form-control" value="<?php  echo $data['appid'];?>"/>
                        <?php  } else { ?>
                        <div class='form-control-static'><?php  echo $data['appid'];?></div>
                        <?php  } ?>
                    </div>
                </div>

                <div class="form-group hidden merch">
                    <label class="col-lg control-label must">服务商支付商户号(Mch_Id)</label>
                    <div class="col-sm-9">
                        <?php if(cv('sysset.payment.edit')) { ?>
                        <input type="text" name="mch_id" class="form-control" value="<?php  echo $data['mch_id'];?>"/>
                        <?php  } else { ?>
                        <div class='form-control-static'><?php  echo $data['mch_id'];?></div>
                        <?php  } ?>
                    </div>
                </div>

                <div class="form-group hidden wechat">
                    <label class="col-lg control-label must">公众号(AppId)</label>
                    <div class="col-sm-9">
                        <?php if(cv('sysset.payment.edit')) { ?>
                        <input type="text" name="sub_appid" class="form-control" value="<?php  echo $data['sub_appid'];?>"/>
                        <?php  } else { ?>
                        <div class='form-control-static'><?php  echo $data['sub_appid_sub'];?></div>
                        <?php  } ?>
                    </div>
                </div>

                <div class="form-group hidden" id="sub_appsecret">
                    <label class="col-lg control-label">应用密钥(AppSecret)</label>
                    <div class="col-sm-9">
                        <?php if(cv('sysset.payment.edit')) { ?>
                        <input type="text" name="sub_appsecret" class="form-control" value="<?php  echo $data['sub_appsecret'];?>"/>
                        <div class="help-block">只有借用支付公众号绑定了系统或者支付目录和授权站点都是本站的设定,才需要填写此项</div>
                        <?php  } else { ?>
                        <div class='form-control-static'><?php  echo $data['sub_appsecret'];?></div>
                        <?php  } ?>
                    </div>
                </div>

                <div class="form-group" >
                    <label class="col-lg control-label must">支付商户号(Mch_Id)</label>
                    <div class="col-sm-9">
                        <?php if(cv('sysset.payment.edit')) { ?>
                        <input type="text" name="sub_mch_id" class="form-control" value="<?php  echo $data['sub_mch_id'];?>"/>
                        <?php  } else { ?>
                        <div class='form-control-static'><?php  echo $data['sub_mch_id'];?></div>
                        <?php  } ?>
                    </div>
                </div>

                <div class="form-group" >
                    <label class="col-lg control-label must">支付密钥(APIKEY)</label>
                    <div class="col-sm-9">
                        <?php if(cv('sysset.payment.edit')) { ?>
                        <input type="text" name="apikey" class="form-control" value="<?php  echo $data['apikey'];?>"/>
                        <div class="help-block">服务商的 APIKEY,并不是子商户的APIKEY</div>
                        <?php  } else { ?>
                        <div class='form-control-static'><?php  echo $data['apikey_sub'];?></div>
                        <?php  } ?>
                    </div>
                </div>

                <div class="form-group hidden" id="is_raw">
                    <label class="col-lg control-label">是否原生支付</label>
                    <div class="col-sm-9 col-xs-12">
                        <?php if(cv('sysset.payment.edit')) { ?>
                        <label class='radio radio-inline'>
                            <input type='radio' value='0' name='is_raw' <?php  if($data['is_raw']==0) { ?>checked<?php  } ?> /> 否
                        </label>
                        <label class='radio radio-inline'>
                            <input type='radio' value='1' name='is_raw'  <?php  if($data['is_raw']=='1') { ?>checked<?php  } ?> /> 是
                        </label>
                        <?php  } else { ?>
                        <div class='form-control-static'><?php  if($data['set_realname']==1) { ?>隐藏<?php  } else { ?>显示<?php  } ?></div>
                        <?php  } ?>
                    </div>
                </div>

                <div class="form-group hidden cert">
                    <label class="col-lg control-label">CERT证书文件</label>
                    <div class="col-sm-9 col-xs-12">
                        <?php if(cv('sysset.payment.edit')) { ?>
                        <input type="file" name="cert_file" class="form-control" />
                        <span class="help-block">
                                            <?php  if(!empty($data['cert_file'])) { ?>
                                            <span class='label label-success'>已上传</span>
                                            <?php  } else { ?>
                                            <span class='label label-danger'>未上传</span>
                                            <?php  } ?>
                                            下载证书 cert.zip 中的 apiclient_cert.pem 文件</span>
                        <?php  } else { ?>
                        <?php  if(!empty($data['cert_file'])) { ?>
                        <span class='label label-success'>已上传</span>
                        <?php  } else { ?>
                        <span class='label label-danger'>未上传</span>
                        <?php  } ?>
                        <?php  } ?>
                    </div>
                </div>
                <div class="form-group hidden cert">
                    <label class="col-lg control-label">KEY密钥文件</label>
                    <div class="col-sm-9 col-xs-12">
                        <?php if(cv('sysset.payment.edit')) { ?>
                        <input type="file" name="key_file" class="form-control" />
                        <span class="help-block">
                                           <?php  if(!empty($data['key_file'])) { ?>
                                            <span class='label label-success'>已上传</span>
                                            <?php  } else { ?>
                                            <span class='label label-danger'>未上传</span>
                                            <?php  } ?>
                                            下载证书 cert.zip 中的 apiclient_key.pem 文件
                                        </span>
                        <?php  } else { ?>
                        <?php  if(!empty($data['key_file'])) { ?>
                        <span class='label label-success'>已上传</span>
                        <?php  } else { ?>
                        <span class='label label-danger'>未上传</span>
                        <?php  } ?>
                        <?php  } ?>
                    </div>
                </div>
                <div class="form-group hidden cert">
                    <label class="col-lg control-label">ROOT文件</label>
                    <div class="col-sm-9 col-xs-12">
                        <?php if(cv('sysset.payment.edit')) { ?>
                        <input type="file" name="root_file" class="form-control" />
                        <span class="help-block">
                                          <?php  if(!empty($data['root_file'])) { ?>
                                            <span class='label label-success'>已上传</span>
                                            <?php  } else { ?>
                                            <span class='label label-danger'>未上传</span>
                                            <?php  } ?>
                                            下载证书 cert.zip 中的 rootca.pem 文件
                                        </span>
                        <?php  } else { ?>
                        <?php  if(!empty($data['root_file'])) { ?>
                        <span class='label label-success'>已上传</span>
                        <?php  } else { ?>
                        <span class='label label-danger'>未上传</span>
                        <?php  } ?>
                        <?php  } ?>
                    </div>
                </div>
            </div>
            <div id="alipay" style="<?php  if(empty($data['paytype'])) { ?>display: none;<?php  } ?>">
                <div class="form-group">
                    <label class="col-lg control-label must">支付类型</label>
                    <div class="col-sm-9 col-xs-12">
                        <?php if( ce('sysset.payment' ,$data) ) { ?>
                        <select class="form-control tpl-category-parent" name="type" id="alitype">
                            <?php  if(is_array($paytypeali)) { foreach($paytypeali as $key => $val) { ?>
                            <option value="<?php  echo $key;?>" <?php  if($data['alitype']==$key) { ?>selected="true"<?php  } ?>><?php  echo $val;?></option>
                            <?php  } } ?>
                        </select>
                        <?php  } else { ?>
                        <div class='form-control-static'><?php  echo $paytypeali[$data['alitype']];?></div>
                        <?php  } ?>
                    </div>
                </div>
                <div class="form-group" >
                    <label class="col-lg control-label must">APPID</label>
                    <div class="col-sm-9">
                        <?php if(cv('sysset.payset.edit')) { ?>
                        <input class="form-control" name="data[app_alipay_appid]" data-rule-required='true' value="<?php  if(!empty($sec['alipay_sec']['appid'])) { ?><?php  echo $sec['alipay_sec']['appid'];?><?php  } ?>" />
                        <div class="help-block">开放平台应用id</div>
                        <?php  } else { ?>
                        <div class='form-control-static'><?php  if(!empty($sec['alipay_sec']['appid'])) { ?><?php  echo $sec['alipay_sec']['appid'];?><?php  } ?></div>
                        <?php  } ?>
                    </div>
                </div>

                <div class="form-group" >
                    <label class="col-lg control-label must">支付宝公钥(PUBLIC_KEY)</label>
                    <div class="col-sm-9">
                        <?php if(cv('sysset.payset.edit')) { ?>
                        <textarea name="data[app_alipay_public_key]" rows="4" data-rule-required='true' class="form-control"><?php  if(!empty($sec['alipay_sec']['public_key'])) { ?><?php  echo $sec['alipay_sec']['public_key'];?><?php  } ?></textarea>
                        <div class="help-block">一行且不能有空格</div>
                        <?php  } else { ?>
                        <div class='form-control-static'><?php  if(!empty($sec['alipay_sec']['public_key'])) { ?><?php  echo $sec['alipay_sec']['public_key'];?><?php  } ?></div>
                        <?php  } ?>
                    </div>
                </div>

                <div class="form-group" >
                    <label class="col-lg control-label must">应用私钥(PRIVATE_KEY)</label>
                    <div class="col-sm-9">
                        <?php if(cv('sysset.payset.edit')) { ?>
                            <textarea name="data[app_alipay_private_key]" style="<?php  if(!empty($sec['alipay_sec']['private_key'])) { ?>display: none;<?php  } ?>" data-rule-required='true' rows="4" class="form-control"><?php  echo $sec['alipay_sec']['private_key'];?></textarea>
                            <a href="javascript:void(0);" class="text text-edit" style="<?php  if(empty($sec['alipay_sec']['private_key'])) { ?>display: none;<?php  } ?>">修改</a>
                            <div class="help-block">一行且不能有空格</div>
                        <?php  } else { ?>
                            <div class='form-control-static'><?php  if(!empty($sec['alipay_sec']['private_key'])) { ?><?php  echo $sec['alipay_sec']['private_key'];?><?php  } ?></div>
                        <?php  } ?>
                    </div>
                </div>
            </div>

                <div class="form-group">
                    <label class="col-lg control-label"></label>
                    <div class="col-sm-9 col-xs-12">
                        <?php if( ce('sysset.payment' ,$list) ) { ?>
                        <input type="submit" value="提交" class="btn btn-primary"/>
                        <?php  } ?>
                        <a class="btn btn-default " href="<?php  echo webUrl('sysset/payment')?>">返回列表</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(function () {
        $(".text-edit").off("click").on("click",function () {
            $(this).hide().parent().find('textarea').show();
        });
        //支付方式
        $("input[name=paytype]").off("click").on("click",function () {
            var paytype = $(this).val();
            if(paytype==1){//支付宝支付
                $("#wechat").hide();
                $("#alipay").show();
            }else{//微信支付
                $("#alipay").hide();
                $("#wechat").show();
            }
        });

        var type = $("#type");
        type.change(function (e) {
            var $this = $(this);
            var cert = $(".cert");
            var merch = $(".merch");
            var wechat = $(".wechat");
            var is_raw = $("#is_raw");
            var sub_appsecret = $("#sub_appsecret");
            cert.addClass('hidden');
            merch.addClass('hidden');
            wechat.addClass('hidden');
            is_raw.addClass('hidden');
            sub_appsecret.addClass('hidden');
            switch ($this.val())
            {
                case '0':
                    cert.removeClass('hidden');
                    wechat.removeClass('hidden');
                    break;
                case '1':
                    merch.removeClass('hidden');
                    cert.removeClass('hidden');
                    wechat.removeClass('hidden');
                    break;
                case '2':
                    cert.removeClass('hidden');
                    wechat.removeClass('hidden');
                    sub_appsecret.removeClass('hidden');
                    break;
                case '3':
                    merch.removeClass('hidden');
                    cert.removeClass('hidden');
                    wechat.removeClass('hidden');
                    sub_appsecret.removeClass('hidden');
                    break;
                case '4':
                    is_raw.removeClass('hidden');
                    break;

            }
        });
        type.trigger('change');
    });
</script>

<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>