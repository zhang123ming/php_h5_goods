<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>



<style>

    .tabs-container .tab-pane .panel-body {border-left: none; border-right: none; border-bottom: none;}

</style>



<form action="" method="post" class="form-horizontal form-validate" enctype="multipart/form-data" >

    <div class="page-header">

        当前位置：<span class="text-primary">支付方式</span>

    </div>

<div class="page-content">

    <?php  if($resp['code']!=200) { ?>

    <div class="alert alert-danger">

        <p style="font-size:18px;font-weight: bold;word-break: break-all"><?php  echo $url;?> 访问受限, 请检查您的服务器设置</p>

        <p style="font-size:18px;font-weight: bold;word-break: break-all">HTTP 错误代码: <?php  echo $resp['code'];?></p>

    </div>

    <?php  } ?>

    <div class="tabs-container">

        <ul class="nav nav-tabs" id="myTab">

            <li class="active"><a href="#tab_wechat">微信端</a></li>

            <li><a href="#tab_wap">WAP端</a></li>

            <?php  if(com('h5app')) { ?>

            <li><a href="#tab_app">APP端</a></li>

            <?php  } ?>

            <li><a href="#tab_alipay">支付宝打款</a></li>
            <li><a href="#tab_qukuailian">区块链支付</a></li>
           <?php  if(com('sandpay')) { ?>
            <li><a href="#tab_sandpay">杉德支付</a></li>
            <?php  } ?>
            <li><a href="#tab_paytype">打款方式</a></li>

        </ul>

        <div class="tab-content ">

            <!-- 微信端开始-->

            <div class="tab-pane  active" id="tab_wechat">

                <div class="panel panel-default">

                    <div class="panel-body">

                        <div class="col-sm-9 col-xs-12">

                            <h4 class="set_title">商城支付</h4>

                            <span>

                            <p class="text text-danger">如果是微信支付，授权目录与“支付选项”中的说明不同，应在 公众平台->微信支付->公众号支付 追加一条支付授权目录: <b><?php  echo $_W['siteroot'];?>app/</b>  </p>

                            <p class="text text-danger"><b>特约商户一般申请没有订单或者充值余额退款权限,需要 <a href='http://kf.qq.com/faq/120911VrYVrA150929imAfuU.html' target="_blank">点击跳转</a>  在这里申请一下 </b></p>

                        </span>

                        </div>

                        <div class="col-lg pull-right" style="padding-top:10px;text-align: right" >

                            <input type="checkbox" class="js-switch" name="data[weixin]" value="1" <?php  if($data['weixin']==1) { ?>checked<?php  } ?> />

                        </div>

                    </div>

                    <div class="panel-body" id='certs' <?php  if(empty($data['weixin'])) { ?>style="display:none"<?php  } ?>>

                    <div class="form-group">

                        <label class="col-lg control-label">选择支付</label>

                        <div class="col-sm-9 col-xs-12">

                            <?php if(cv('sysset.payset.edit')) { ?>

                            <select name='data[weixin_id]' class='form-control'>

                                <option value='0'>未选择任何支付</option>

                                <?php  if(is_array($payments)) { foreach($payments as $val) { ?>

                                <option value='<?php  echo $val['id'];?>' <?php  if($data['weixin_id']==$val['id']) { ?>selected<?php  } ?>><?php  echo $val['title'];?></option>

                                <?php  } } ?>

                            </select>

                            <?php  } else { ?>

                            <div class='form-control-static'></div>

                            <?php  } ?>

                        </div>

                    </div>

                </div>

            </div>



            <div class="panel panel-default" >

                <div class="panel-body">

                    <div class="col-sm-9 col-xs-12">

                        <h4 class="set_title">余额支付</h4>

                        <span>开启后，粉丝可以用账户余额去商城消费.</span>

                    </div>

                    <div class="col-lg pull-right" style="padding-top:10px;text-align: right" >

                        <input type="checkbox" class="js-switch"  name="data[credit]"  value="1" <?php  if($data['credit']==1) { ?>checked<?php  } ?> />

                    </div>

                </div>

            </div>

            <div class="panel panel-default" >

                <div class="panel-body">

                    <div class="col-sm-9 col-xs-12">

                        <h4 class="set_title">货到付款</h4>

                        <span>如果要支持货到付款，也需要将商品设置成货到付款。</span>

                    </div>

                    <div class="col-lg pull-right" style="padding-top:10px;text-align: right" >

                        <input type="checkbox" class="js-switch" name="data[cash]" value="1" <?php  if($data['cash']==1) { ?>checked<?php  } ?> />

                    </div>

                </div>

            </div>

        </div>

    <!--微信端 结束-->





    <!--wap端开始-->

    <div class="tab-pane " id="tab_wap">

        <div class="panel panel-default" >

            <div class="panel-body">

                <div class="col-sm-9 col-xs-12">

                    <h4 class="set_title">支付宝支付</h4>

                    <p> 在开启支付宝支付方式前，请到 <a href="<?php  echo url('profile/payment')?>" target="_blank">支付选项</a> 去设置好参数。(微信中支付宝支付也属于WAP支付)</p>

                    <p class="text text-danger">如果未选择新版支付宝，则默认使用旧版。</p>

                    <p class="text text-danger">新版支付宝请到 <a href="<?php  echo weburl('sysset/payment')?>"><b>【支付管理】</b></a>页面添加模板</p>

                    <p class="text text-danger">请在使用新版支付宝之前，确保之前支付宝支付订单已经完结，以免造成不必要的调整。</p>

                </div>

                <div class="col-lg pull-right" style="padding-top:10px;text-align: right" >

                    <input type="checkbox" class="js-switch" name="data[alipay]"  value="1" <?php  if($data['alipay']==1) { ?>checked<?php  } ?> />

                </div>

            </div>

            <div class="form-group" id="alipay" <?php  if($data['alipay']==0) { ?>style="display: none;"<?php  } ?>>

                <label class="col-lg control-label">新版支付宝</label>

                <div class="col-sm-9 col-xs-12">

                    <?php if(cv('sysset.payset.edit')) { ?>

                    <select name='data[alipay_id]' class='form-control'>

                        <option value='0'>未选择任何支付</option>

                        <?php  if(is_array($paymentalis)) { foreach($paymentalis as $val) { ?>

                        <option value='<?php  echo $val['id'];?>' <?php  if($data['alipay_id']==$val['id']) { ?>selected<?php  } ?>><?php  echo $val['title'];?></option>

                        <?php  } } ?>

                    </select>

                    <?php  } else { ?>

                    <div class='form-control-static'></div>

                    <?php  } ?>

                </div>

            </div>

        </div>



    </div>

    <!--wap端结束-->



    <!--app端开始-->

    <div class="tab-pane " id="tab_app">

        <div class="panel panel-default" >

            <div class="panel-body">

                <div class="col-sm-9 col-xs-12">

                    <h4 class="set_title">微信支付</h4>

                    <span> 开启后在APP中可以调起微信进行支付</span>

                </div>

                <div class="col-lg pull-right" style="padding-top:10px;text-align: right" >

                    <input type="checkbox" class="js-switch"  name="data[app_wechat]"  value="1" <?php  if($data['app_wechat']==1) { ?>checked<?php  } ?> />

                </div>

            </div>



            <div class="panel-body" id="app_wechat" <?php  if(empty($data['app_wechat'])) { ?>style="display:none"<?php  } ?>>

            <div class="form-group" >

                <label class="col-lg control-label must">微信AppId</label>

                <div class="col-sm-9">

                    <?php if(cv('sysset.payset.edit')) { ?>

                    <input type="text" name="data[app_wechat_appid]" class="form-control" value="<?php  if(!empty($sec['app_wechat']['appid'])) { ?><?php  echo $sec['app_wechat']['appid'];?><?php  } ?>"/>

                    <?php  } else { ?>

                    <div class='form-control-static'><?php  if(!empty($sec['app_wechat']['appid'])) { ?><?php  echo $sec['app_wechat']['appid'];?><?php  } ?></div>

                    <?php  } ?>

                </div>

            </div>



            <div class="form-group" >

                <label class="col-lg control-label must">微信AppSecret</label>

                <div class="col-sm-9">

                    <?php if(cv('sysset.payset.edit')) { ?>

                    <input type="text" name="data[app_wechat_appsecret]" class="form-control" value="<?php  if(!empty($sec['app_wechat']['appsecret'])) { ?><?php  echo $sec['app_wechat']['appsecret'];?><?php  } ?>"/>

                    <?php  } else { ?>

                    <div class='form-control-static'><?php  if(!empty($sec['app_wechat']['appsecret'])) { ?><?php  echo $sec['app_wechat']['appsecret'];?><?php  } ?></div>

                    <?php  } ?>

                </div>

            </div>



            <div class="form-group" >

                <label class="col-lg control-label must">微信商户名称</label>

                <div class="col-sm-9">

                    <?php if(cv('sysset.payset.edit')) { ?>

                    <input type="text" name="data[app_wechat_merchname]" class="form-control" value="<?php  if(!empty($sec['app_wechat']['merchname'])) { ?><?php  echo $sec['app_wechat']['merchname'];?><?php  } ?>"/>

                    <?php  } else { ?>

                    <div class='form-control-static'><?php  if(!empty($sec['app_wechat']['merchname'])) { ?><?php  echo $sec['app_wechat']['merchname'];?><?php  } ?></div>

                    <?php  } ?>

                </div>

            </div>



            <div class="form-group" >

                <label class="col-lg control-label must">微信商户ID</label>

                <div class="col-sm-9">

                    <?php if(cv('sysset.payset.edit')) { ?>

                    <input type="text" name="data[app_wechat_merchid]" class="form-control" value="<?php  if(!empty($sec['app_wechat']['merchid'])) { ?><?php  echo $sec['app_wechat']['merchid'];?><?php  } ?>"/>

                    <?php  } else { ?>

                    <div class='form-control-static'><?php  if(!empty($sec['app_wechat']['merchid'])) { ?><?php  echo $sec['app_wechat']['merchid'];?><?php  } ?></div>

                    <?php  } ?>

                </div>

            </div>



            <div class="form-group" >

                <label class="col-lg control-label must">微信商户API密钥</label>

                <div class="col-sm-9">

                    <?php if(cv('sysset.payset.edit')) { ?>

                    <input type="text" name="data[app_wechat_apikey]" class="form-control" value="<?php  if(!empty($sec['app_wechat']['apikey'])) { ?><?php  echo $sec['app_wechat']['apikey'];?><?php  } ?>"/>

                    <?php  } else { ?>

                    <div class='form-control-static'><?php  if(!empty($sec['app_wechat']['apikey'])) { ?><?php  echo $sec['app_wechat']['apikey'];?><?php  } ?></div>

                    <?php  } ?>

                </div>

            </div>



            <div class="form-group">

                <label class="col-lg control-label">CERT证书文件</label>

                <div class="col-sm-9 col-xs-12">

                    <input type="hidden" name="data[app_wechat_cert]" value="<?php  echo $data['app_wechat_cert'];?>"/>

                    <?php if(cv('sysset.payset.edit')) { ?>



                    <input type="file" name="app_wechat_cert_file" class="form-control" />

                                <span class="help-block">

                                    <?php  if(!empty($sec['app_wechat']['cert'])) { ?>

                                    <span class='label label-success'>已上传</span>

                                    <?php  } else { ?>

                                    <span class='label label-danger'>未上传</span>

                                    <?php  } ?>

                                    下载证书 cert.zip 中的 apiclient_cert.pem 文件</span>

                    <?php  } else { ?>

                    <?php  if(!empty($sec['app_wechat']['cert'])) { ?>

                    <span class='label label-success'>已上传</span>

                    <?php  } else { ?>

                    <span class='label label-danger'>未上传</span>

                    <?php  } ?>

                    <?php  } ?>



                </div>

            </div>

            <div class="form-group">

                <label class="col-lg control-label">KEY密钥文件</label>

                <div class="col-sm-9 col-xs-12">

                    <input type="hidden" name="data[app_wechat_key]"  value="<?php  echo $data['app_wechat_key'];?>"/>

                    <?php if(cv('sysset.payset.edit')) { ?>



                    <input type="file" name="app_wechat_key_file" class="form-control" />

                                <span class="help-block">

                                   <?php  if(!empty($sec['app_wechat']['key'])) { ?>

                                    <span class='label label-success'>已上传</span>

                                    <?php  } else { ?>

                                    <span class='label label-danger'>未上传</span>

                                    <?php  } ?>

                                    下载证书 cert.zip 中的 apiclient_key.pem 文件

                                </span>

                    <?php  } else { ?>

                    <?php  if(!empty($sec['app_wechat']['key'])) { ?>

                    <span class='label label-success'>已上传</span>

                    <?php  } else { ?>

                    <span class='label label-danger'>未上传</span>

                    <?php  } ?>

                    <?php  } ?>

                </div>

            </div>

            <div class="form-group">

                <label class="col-lg control-label">ROOT文件</label>

                <div class="col-sm-9 col-xs-12">

                    <input type="hidden" name="data[app_wechat_root]" value="<?php  echo $data['app_wechat_root'];?>"/>

                    <?php if(cv('sysset.payset.edit')) { ?>



                    <input type="file" name="app_wechat_root_file" class="form-control" />

                                <span class="help-block">

                                  <?php  if(!empty($sec['app_wechat']['root'])) { ?>

                                    <span class='label label-success'>已上传</span>

                                    <?php  } else { ?>

                                    <span class='label label-danger'>未上传</span>

                                    <?php  } ?>

                                    下载证书 cert.zip 中的 rootca.pem 文件

                                </span>

                    <?php  } else { ?>

                    <?php  if(!empty($sec['app_wechat']['root'])) { ?>

                    <span class='label label-success'>已上传</span>

                    <?php  } else { ?>

                    <span class='label label-danger'>未上传</span>

                    <?php  } ?>

                    <?php  } ?>

                </div>

            </div>

        </div>

    </div>



    <div class="panel panel-default" >

        <div class="panel-body">

            <div class="col-sm-9 col-xs-12">

                <h4 class="set_title">支付宝支付</h4>

                <span> 开启后在APP中可以调起支付宝进行支付</span>

            </div>

            <div class="col-lg pull-right" style="padding-top:10px;text-align: right" >

                <input type="checkbox" class="js-switch" name="data[app_alipay]"  value="1" <?php  if($data['app_alipay']==1) { ?>checked<?php  } ?> />

            </div>

        </div>



        <div class="panel-body" id="app_alipay" <?php  if(empty($data['app_alipay'])) { ?>style="display:none"<?php  } ?>>

        <div class="form-group" >

            <label class="col-lg control-label must">APPID</label>

            <div class="col-sm-9">

                <?php if(cv('sysset.payset.edit')) { ?>

                <input class="form-control" name="data[app_alipay_appid]" value="<?php  if(!empty($sec['app_alipay']['appid'])) { ?><?php  echo $sec['app_alipay']['appid'];?><?php  } ?>" />

                <div class="help-block">开放平台应用id</div>

                <?php  } else { ?>

                <div class='form-control-static'><?php  if(!empty($sec['app_alipay']['appid'])) { ?><?php  echo $sec['app_alipay']['appid'];?><?php  } ?></div>

                <?php  } ?>

            </div>

        </div>



        <div class="form-group" >

            <label class="col-lg control-label must">支付宝公钥(public_key)</label>

            <div class="col-sm-9">

                <?php if(cv('sysset.payset.edit')) { ?>

                <textarea name="data[app_alipay_public_key]" rows="4" class="form-control"><?php  if(!empty($sec['app_alipay']['public_key'])) { ?><?php  echo $sec['app_alipay']['public_key'];?><?php  } ?></textarea>

                <div class="help-block">一行且不能有空格</div>

                <?php  } else { ?>

                <div class='form-control-static'><?php  if(!empty($sec['app_alipay']['public_key'])) { ?><?php  echo $sec['app_alipay']['public_key'];?><?php  } ?></div>

                <?php  } ?>

            </div>

        </div>



        <div class="form-group" >

            <label class="col-lg control-label must">应用私钥(private_key)</label>

            <div class="col-sm-9">

                <?php if(cv('sysset.payset.edit')) { ?>

                <textarea name="data[app_alipay_private_key]" rows="4" class="form-control"><?php  if(!empty($sec['app_alipay']['private_key'])) { ?><?php  echo $sec['app_alipay']['private_key'];?><?php  } ?></textarea>

                <div class="help-block">一行且不能有空格</div>

                <?php  } else { ?>

                <div class='form-control-static'><?php  if(!empty($sec['app_alipay']['private_key'])) { ?><?php  echo $sec['app_alipay']['private_key'];?><?php  } ?></div>

                <?php  } ?>

            </div>

        </div>

    </div>

    </div>

    </div>

    <!--app端结束-->



    <!--支付宝打款 开始-->

    <div class="tab-pane " id="tab_alipay">

        <div class="panel panel-default" >

            <div class="panel-body">

                <div class="col-sm-9 col-xs-12">

                    <h4 class="set_title">支付宝打款</h4>

                    <span> 开启之后,佣金打款,可以使用支付宝(前提是要登录支付宝)</span>

                </div>

                <div class="col-lg pull-right" style="padding-top:10px;text-align: right" >

                    <input type="checkbox" class="js-switch"  name="data[alipay_pay][open]"  value="1" <?php  if(!empty($sec['alipay_pay']['open'])) { ?>checked<?php  } ?> />

                </div>

            </div>



            <div class="panel-body" id="alipay_pay" <?php  if(empty($sec['alipay_pay']['open'])) { ?>style="display:none"<?php  } ?>>

            <div class="form-group" >

                <label class="col-lg control-label must">支付宝应用（APPID）</label>

                <div class="col-sm-9 double">

                    <?php if(cv('sysset.payset.edit')) { ?>

                    <input   type="text" name="data[alipay_pay][appid]" class="form-control"  value="<?php  if(!empty($sec['alipay_pay']['appid'])) { ?><?php  echo $sec['alipay_pay']['appid'];?><?php  } ?>"/>

                    <?php  } else { ?>

                    <div class='form-control-static'><?php  if(!empty($sec['alipay_pay']['appid'])) { ?><?php  echo $sec['alipay_pay']['appid'];?><?php  } ?></div>

                    <?php  } ?>

                </div>

            </div>



            <div class="form-group" >

                <label class="col-lg control-label must">支付宝应用私钥</label>

                <div class="col-sm-9 double">

                    <?php if(cv('sysset.payset.edit')) { ?>

                    <input  type="text" name="data[alipay_pay][appprikey]" class="form-control double" value="<?php  if(!empty($sec['alipay_pay']['appprikey'])) { ?><?php  echo $sec['alipay_pay']['appprikey'];?><?php  } ?>"/>

                    <?php  } else { ?>

                    <div class='form-control-static'><?php  if(!empty($sec['alipay_pay']['appprikey'])) { ?><?php  echo $sec['alipay_pay']['appprikey'];?><?php  } ?></div>

                    <?php  } ?>

                    <div class="help-block">支付宝开放平台应用私钥(一行且不能有空格)</div>

                </div>

            </div>



            <div class="form-group" >

                <label class="col-lg control-label must">支付宝公钥</label>

                <div class="col-sm-9 double">

                    <?php if(cv('sysset.payset.edit')) { ?>

                    <input  type="text" name="data[alipay_pay][public_key]" class="form-control" value="<?php  if(!empty($sec['alipay_pay']['public_key'])) { ?><?php  echo $sec['alipay_pay']['public_key'];?><?php  } ?>"/>

                    <?php  } else { ?>

                    <div class='form-control-static'><?php  if(!empty($sec['alipay_pay']['public_key'])) { ?><?php  echo $sec['alipay_pay']['public_key'];?><?php  } ?></div>

                    <?php  } ?>

                    <div class="help-block">支付宝开发平台公钥(一行且不能有空格)</div>

                </div>

            </div>

        </div>

    </div>

    </div>

    <!--支付宝打款 结束-->

    <!--区块链支付 开始-->

    <div class="tab-pane " id="tab_qukuailian">

        <div class="panel panel-default" >

            <div class="panel-body">

                <div class="col-sm-9 col-xs-12">

                    <h4 class="set_title">区块链支付</h4>

                    <span> 开启之后,可以使用区块链支付</span>

                </div>

                <div class="col-lg pull-right" style="padding-top:10px;text-align: right" >

                    <input type="checkbox" class="js-switch qukuailian"  name="data[qukuailian][open]"  value="1" <?php  if(!empty($data['qukuailian']['open'])) { ?>checked<?php  } ?> />

                </div>

            </div>



<!--             <div class="panel-body" id="qukuailian" <?php  if(empty($data['qukuailian']['open'])) { ?>style="display:none"<?php  } ?>>

            <div class="form-group" >

                <label class="col-lg control-label must">支付宝应用（APPID）</label>

                <div class="col-sm-9 double">

                    <?php if(cv('sysset.payset.edit')) { ?>

                    <input   type="text" name="data[alipay_pay][appid]" class="form-control"  value="<?php  if(!empty($sec['alipay_pay']['appid'])) { ?><?php  echo $sec['alipay_pay']['appid'];?><?php  } ?>"/>

                    <?php  } else { ?>

                    <div class='form-control-static'><?php  if(!empty($sec['alipay_pay']['appid'])) { ?><?php  echo $sec['alipay_pay']['appid'];?><?php  } ?></div>

                    <?php  } ?>

                </div>

            </div>



            <div class="form-group" >

                <label class="col-lg control-label must">支付宝应用私钥</label>

                <div class="col-sm-9 double">

                    <?php if(cv('sysset.payset.edit')) { ?>

                    <input  type="text" name="data[alipay_pay][appprikey]" class="form-control double" value="<?php  if(!empty($sec['alipay_pay']['appprikey'])) { ?><?php  echo $sec['alipay_pay']['appprikey'];?><?php  } ?>"/>

                    <?php  } else { ?>

                    <div class='form-control-static'><?php  if(!empty($sec['alipay_pay']['appprikey'])) { ?><?php  echo $sec['alipay_pay']['appprikey'];?><?php  } ?></div>

                    <?php  } ?>

                    <div class="help-block">支付宝开放平台应用私钥(一行且不能有空格)</div>

                </div>

            </div>



            <div class="form-group" >

                <label class="col-lg control-label must">支付宝公钥</label>

                <div class="col-sm-9 double">

                    <?php if(cv('sysset.payset.edit')) { ?>

                    <input  type="text" name="data[alipay_pay][public_key]" class="form-control" value="<?php  if(!empty($sec['alipay_pay']['public_key'])) { ?><?php  echo $sec['alipay_pay']['public_key'];?><?php  } ?>"/>

                    <?php  } else { ?>

                    <div class='form-control-static'><?php  if(!empty($sec['alipay_pay']['public_key'])) { ?><?php  echo $sec['alipay_pay']['public_key'];?><?php  } ?></div>

                    <?php  } ?>

                    <div class="help-block">支付宝开发平台公钥(一行且不能有空格)</div>

                </div>

            </div>

        </div> -->

    </div>

    </div>

    <!--区块链支付 结束-->
 <!--杉德支付开始-->

 <div class="tab-pane " id="tab_sandpay">

    <div class="panel panel-default" >

        <div class="panel-body">

            <div class="col-sm-9 col-xs-12">

                <h4 class="set_title">杉德支付</h4>

                <span> 开启后可使用杉德第三方支付</span>

            </div>

            <div class="col-lg pull-right" style="padding-top:10px;text-align: right" >

                <input type="checkbox" class="js-switch sandpay"  name="data[sandpay]"  value="1" <?php  if($data['sandpay']==1) { ?>checked<?php  } ?> />

            </div>
        </div>
        <div class="panel-body" id="sandpay" <?php  if(empty($data['sandpay'])) { ?>style="display:none"<?php  } ?>>
        <div class="form-group" >

            <label class="col-lg control-label must">选择方式</label>

            <div class="col-sm-9">

                <?php if(cv('sysset.payset.edit')) { ?>

                <select name='data[sandpay_type]' class='form-control'>
                    <option selected  value='0'>微信扫码支付</option>
                    <option <?php  if($sec['sandpay_pay']['type']=='1') { ?> selected <?php  } ?> value='1'>公众号支付</option>
                </select>

                <?php  } else { ?>

                <div class='form-control-static'><?php  if(!empty($sec['sandpay_pay']['mid'])) { ?><?php  echo $sec['sandpay_pay']['mid'];?><?php  } ?></div>

                <?php  } ?>

            </div>

        </div>
        <div class="form-group" >

            <label class="col-lg control-label must">商户号</label>

            <div class="col-sm-9">

                <?php if(cv('sysset.payset.edit')) { ?>

                <input type="text" name="data[sandpay_mid]" class="form-control" value="<?php  if(!empty($sec['sandpay_pay']['mid'])) { ?><?php  echo $sec['sandpay_pay']['mid'];?><?php  } ?>"/>

                <?php  } else { ?>

                <div class='form-control-static'><?php  if(!empty($sec['sandpay_pay']['mid'])) { ?><?php  echo $sec['sandpay_pay']['mid'];?><?php  } ?></div>

                <?php  } ?>

            </div>

        </div>
        
        <div class="form-group" >

            <label class="col-lg control-label must">支付密钥</label>

            <div class="col-sm-9">

                <?php if(cv('sysset.payset.edit')) { ?>

                <input type="text" name="data[sandpay_key]" class="form-control" value="<?php  if(!empty($sec['sandpay_pay']['key'])) { ?><?php  echo $sec['sandpay_pay']['key'];?><?php  } ?>"/>

                <?php  } else { ?>

                <div class='form-control-static'><?php  if(!empty($sec['sandpay_pay']['key'])) { ?><?php  echo $sec['sandpay_pay']['key'];?><?php  } ?></div>

                <?php  } ?>

            </div>

        </div>
        <div class="form-group" >

            <label class="col-lg control-label must">公众号/小程序appid</label>

            <div class="col-sm-9">

                <?php if(cv('sysset.payset.edit')) { ?>

                <input type="text" name="data[sandpay_appid]" class="form-control" value="<?php  if(!empty($sec['sandpay_pay']['appid'])) { ?><?php  echo $sec['sandpay_pay']['appid'];?><?php  } ?>"/>

                <?php  } else { ?>

                <div class='form-control-static'><?php  if(!empty($sec['sandpay_pay']['appid'])) { ?><?php  echo $sec['sandpay_pay']['appid'];?><?php  } ?></div>

                <?php  } ?>

            </div>

        </div>

        <div class="form-group">

            <label class="col-lg control-label">cer公钥文件</label>

            <div class="col-sm-9 col-xs-12">

                <input type="hidden" name="data[sandpay_public_key]" value="<?php  echo $data['sandpay_public_key'];?>"/>

                <?php if(cv('sysset.payset.edit')) { ?>
                <input type="file" name="sandpay_public_key_file" class="form-control" />

                            <span class="help-block">

                                <?php  if(!empty($sec['sandpay_pay']['public_key'])) { ?>

                                <span class='label label-success'>已上传</span>

                                <?php  } else { ?>

                                <span class='label label-danger'>未上传</span>

                                <?php  } ?>

                                下载的.cer 文件</span>

                <?php  } else { ?>

                <?php  if(!empty($sec['sandpay_pay']['public_key'])) { ?>

                <span class='label label-success'>已上传</span>

                <?php  } else { ?>

                <span class='label label-danger'>未上传11<?php  echo $sec['sandpay_pay']['public_key'];?></span>

                <?php  } ?>

                <?php  } ?>
            </div>

        </div>

        <div class="form-group">

            <label class="col-lg control-label">pfx密钥文件</label>

            <div class="col-sm-9 col-xs-12">

                <input type="hidden" name="data[sandpay_private_key]"  value="<?php  echo $data['sandpay_private_key'];?>"/>

                <?php if(cv('sysset.payset.edit')) { ?>



                <input type="file" name="sandpay_private_key_file" class="form-control" />

                            <span class="help-block">

                               <?php  if(!empty($sec['sandpay_pay']['private_key'])) { ?>

                                <span class='label label-success'>已上传</span>

                                <?php  } else { ?>

                                <span class='label label-danger'>未上传</span>

                                <?php  } ?>

                                下载的.pfx文件

                            </span>

                <?php  } else { ?>

                <?php  if(!empty($sec['sandpay_pay']['private_key'])) { ?>

                <span class='label label-success'>已上传</span>

                <?php  } else { ?>

                <span class='label label-danger'>未上传</span>

                <?php  } ?>

                <?php  } ?>

            </div>

        </div>

    </div>

</div>

</div>

<!--杉德支付结束-->



    <!--微信打款方式 开始-->

    <div class="tab-pane " id="tab_paytype">

        <div class="panel-body">

            <div class="form-group" >

                <label class="col-lg control-label must">佣金打款</label>

                <div class="col-sm-9">

                    <?php if(cv('sysset.payset.edit')) { ?>

                    <label class="radio-inline"><input type="radio" name="data[paytype][commission]" value="0" <?php  if(empty($data['paytype']['commission'])) { ?>checked="checked"<?php  } ?>/>企业打款</label>

                    <label class="radio-inline"><input type="radio" name="data[paytype][commission]" value="1" <?php  if($data['paytype']['commission'] == '1') { ?>checked="checked"<?php  } ?>/>红包付款</label>

                    <?php  } else { ?>

                    <div class='form-control-static'><?php  if(empty($data['paytype']['commission'])) { ?>企业打款<?php  } else { ?>红包付款<?php  } ?></div>

                    <?php  } ?>

                    <span class="help-block">一般 小额打款 可以用微信红包 建议使用微信打款金额 超过1000</span>

                </div>

            </div>



            <div class="form-group" >

                <label class="col-lg control-label must">提现申请</label>

                <div class="col-sm-9">

                    <?php if(cv('sysset.payset.edit')) { ?>

                    <label class="radio-inline"><input type="radio" name="data[paytype][withdraw]" value="0" <?php  if(empty($data['paytype']['withdraw'])) { ?>checked="checked"<?php  } ?>/>企业打款</label>

                    <label class="radio-inline"><input type="radio" name="data[paytype][withdraw]" value="1" <?php  if($data['paytype']['withdraw'] == '1') { ?>checked="checked"<?php  } ?>/>红包付款</label>

                    <?php  } else { ?>

                    <div class='form-control-static'><?php  if(empty($data['paytype']['withdraw'])) { ?>企业打款<?php  } else { ?>红包付款<?php  } ?></div>

                    <?php  } ?>

                    <span class="help-block">一般 小额打款 可以用微信红包 建议使用微信打款金额 超过1000</span>

                </div>

            </div>



            <div class="form-group" >

                <label class="col-lg control-label must">红包金额</label>

                <div class="col-sm-9">

                    <?php if(cv('sysset.payset.edit')) { ?>

                    <label class="radio-inline"><input type="radio" name="data[paytype][redpack]" value="0" <?php  if(empty($data['paytype']['redpack'])) { ?>checked="checked"<?php  } ?>/>188元</label>

                    <label class="radio-inline"><input type="radio" name="data[paytype][redpack]" value="1" <?php  if($data['paytype']['redpack'] == '1') { ?>checked="checked"<?php  } ?>/>288元</label>

                    <label class="radio-inline"><input type="radio" name="data[paytype][redpack]" value="2" <?php  if($data['paytype']['redpack'] == '2') { ?>checked="checked"<?php  } ?>/>388元</label>

                    <?php  } else { ?>

                    <div class='form-control-static'><?php  if(empty($data['paytype']['redpack'])) { ?>188元<?php  } else if($data['paytype']['redpack'] == '1') { ?>288元<?php  } else { ?>388元<?php  } ?></div>

                    <?php  } ?>

                    <span class="help-block">如果选用 红包打款 , 请选择每个红包最大面值;<br/>如果是 申请额度是 500 元 选择的是 188元 红包 将会发送 两个188元红包 和 一个 124元 红包</span>

                </div>

            </div>

        </div>

    </div>





    </div>

    </div>

    <span class="">

        <?php if(cv('sysset.payset.edit')) { ?>

            <input type="submit" value="提交" class="btn btn-primary"  />

        <?php  } ?>

    </span>

</form>

</div>



<script type="text/javascript">

    require(['bootstrap'], function () {

        $('#myTab a').click(function (e) {

            e.preventDefault();

            $(this).tab('show');

        })

    });

    $(function () {

        $(":checkbox[name='data[weixin]']").click(function () {

            var weixin_sub = $(":checkbox[name='data[weixin_sub]']");

            if ($(this).prop('checked')) {

                $("#certs").show();

                if(weixin_sub.next().hasClass("checked")){

                    weixin_sub.next().click();

                }

            }

            else {

                $("#certs").hide();

            }

        });

        //支付宝开启，关闭

        $(":checkbox[name='data[alipay]']").click(function () {

            if ($(this).prop('checked')) {

                $("#alipay").show();

            }else {

                $("#alipay").hide();

            }

        });





        $(":checkbox[name='data[weixin_jie]']").click(function () {

            var weixin_jie_sub = $(":checkbox[name='data[weixin_jie_sub]']");

            if ($(this).prop('checked')) {

                $("#weixin_jie").show();

                if(weixin_jie_sub.next().hasClass("checked")){

                    weixin_jie_sub.next().click();

                }

            }

            else {

                $("#weixin_jie").hide();

            }

        });



        $(":checkbox[name='data[weixin_sub]']").click(function () {

            var weixin = $(":checkbox[name='data[weixin]']");

            if ($(this).prop('checked')) {

                $("#weixin_sub").show();

                if(weixin.next().hasClass("checked")){

                    weixin.next().click();

                }

            }

            else {

                $("#weixin_sub").hide();

            }

        });



        $(":checkbox[name='data[weixin_jie_sub]']").click(function () {

            var weixin_jie = $(":checkbox[name='data[weixin_jie]']");

            if ($(this).prop('checked')) {

                $("#weixin_jie_sub").show();

                if(weixin_jie.next().hasClass("checked")){

                    weixin_jie.next().click();

                }

            }

            else {

                $("#weixin_jie_sub").hide();

            }

        });



        $(":checkbox[name='data[app_wechat]']").click(function () {

            if ($(this).prop('checked')) {

                $("#app_wechat").show();

            }

            else {

                $("#app_wechat").hide();

            }

        });



        $(":checkbox[name='data[app_alipay]']").click(function () {

            if ($(this).prop('checked')) {

                $("#app_alipay").show();

            }

            else {

                $("#app_alipay").hide();

            }

        });

        $(".qukuailian").click(function () {

            if ($(this).prop('checked')) {

                $("#qukuailian").show();

            }

            else {

                $("#qukuailian").hide();

            }

        });
        $(".sandpay").click(function () {
            if ($(this).prop('checked')) {
                $("#sandpay").show();
            }
            else {
                $("#sandpay").hide();
            }
        });


        $(":checkbox[name='data[alipay_pay][open]']").click(function () {

            if ($(this).prop('checked')) {

                $("#alipay_pay").show();

            }

            else {

                $("#alipay_pay").hide();

            }

        });



    })
</script>

<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>