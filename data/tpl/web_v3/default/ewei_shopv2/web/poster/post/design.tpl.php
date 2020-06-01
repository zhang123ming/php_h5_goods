<?php defined('IN_IA') or exit('Access Denied');?><div class="form-group" id='goodsdiv' <?php  if(empty($goods)) { ?>style="display:none"<?php  } ?>>
                    <label class="col-lg control-label">选择商品</label>
                  <div class="col-sm-9 col-xs-12">
                          <?php if( ce('postera' ,$item) ) { ?>
                          <?php  echo tpl_selector('goodsid', array('url'=>webUrl('shop/query'), 'items'=>$goods,'placeholder'=>'请输入商品标题','buttontext'=>'选择商品'))?>
                         
                      <?php  } else { ?>
                             <div class='form-control-static'>[<?php  echo $goods['id'];?>]<?php  echo $goods['title'];?></div>
                             <?php  } ?>
                             
                    </div>
</div>	

<div class="form-group">

    <div class="col-sm-12">
        <table style='width:100%;'>
            <tr>
                <td style='width:320px;padding:10px;' valign='top'>
                    <div id='poster'>
                        <?php  if(!empty($item['bg'])) { ?>
                        <img src='<?php  echo tomedia($item['bg'])?>' class='bg'/>
                             <?php  } ?>
                             <?php  if(!empty($data)) { ?>
                             <?php  if(is_array($data)) { foreach($data as $key => $d) { ?>

                             <div class="drag" type="<?php  echo $d['type'];?>" index="<?php  echo $key+1?>" style="zindex:<?php  echo $key+1?>;left:<?php  echo $d['left'];?>;top:<?php  echo $d['top'];?>;
                             width:<?php  echo $d['width'];?>;height:<?php  echo $d['height'];?>" 
                             src="<?php  echo $d['src'];?>" size="<?php  echo $d['size'];?>" color="<?php  echo $d['color'];?>"
                             > 
                            <?php  if($d['type']=='qr') { ?>
                            <img src="../addons/ewei_shopv2/plugin/poster/static/images/qr.jpg" />
                            <?php  } else if($d['type']=='head') { ?>
                            <img src="../addons/ewei_shopv2/plugin/poster/static/images/head.jpg" />
                            <?php  } else if($d['type']=='img' || $d['type']=='thumb') { ?>
                            <img src="<?php echo empty($d['src'])?'../addons/ewei_shopv2/plugin/poster/static/images/img.jpg':tomedia($d['src'])?>" />
                            <?php  } else if($d['type']=='nickname') { ?>
                            <div class=text style="font-size:<?php  echo $d['size'];?>;color:<?php  echo $d['color'];?>" id="content"><?php  echo $d['content'];?></div> 
                            <?php  } else if($d['type']=='title') { ?>
                            <div class=text style="font-size:<?php  echo $d['size'];?>;color:<?php  echo $d['color'];?>">商品名称</div> 
                            <?php  } else if($d['type']=='marketprice') { ?>
                            <div class=text style="font-size:<?php  echo $d['size'];?>;color:<?php  echo $d['color'];?>">商品现价</div> 
                            <?php  } else if($d['type']=='productprice') { ?>
                            <div class=text style="font-size:<?php  echo $d['size'];?>;color:<?php  echo $d['color'];?>">商品原价</div> 
                            <?php  } ?>
                            <div class="rRightDown"> </div><div class="rLeftDown"> </div><div class="rRightUp"> </div><div class="rLeftUp"> </div><div class="rRight"> </div><div class="rLeft"> </div><div class="rUp"> </div><div class="rDown"></div>
                        </div>
                        <?php  } } ?> 
                        <?php  } ?>
                    </div>

                </td>
                <td valign='top' style='padding:10px;'>
                    <?php if( ce('poster' ,$item) ) { ?>
                    <div class='panel panel-default'>
                        <div class='panel-body'>
                            <div class="form-group" id="bgset">
                                <label class="col-lg control-label">背景图片</label>
                                <div class="col-sm-9 col-xs-12">
                                    <?php  echo tpl_form_field_image2('bg',$item['bg'])?>
                                    <span class='help-block'>背景图片尺寸: 640 * 1008</span>
                                </div>


                            </div>
                            <div class="form-group">
                                <label class="col-lg control-label">海报元素</label>
                                <div class="col-sm-9 col-xs-12">
                                    <button class='btn btn-default btn-com' type='button' data-type='head' >头像</button>
                                    <button class='btn btn-default btn-com' type='button' data-type='nickname' >昵称</button>
                                    <button class='btn btn-default btn-com' type='button' data-type='qr' >二维码</button>
                                    <button class='btn btn-default btn-com' type='button' data-type='img' >图片</button>
                                    <span id="goodsparams" <?php  if($item['type']!=3) { ?>style="display:none"<?php  } ?>>
                                          <br />
                                        <button class='btn btn-default btn-com' type='button' data-type='title' >商品名称</button>    
                                        <button class='btn btn-default btn-com' type='button' data-type='thumb' >商品图片</button>    
                                        <button class='btn btn-default btn-com' type='button' data-type='marketprice' >商品现价</button>    
                                        <button class='btn btn-default btn-com' type='button' data-type='productprice' >商品原价</button>    
                                    </span>
                                </div>
                            </div>
                            <div id='qrset' style='display:none'>
                                <div class="form-group">
                                    <label class="col-lg control-label">二维码尺寸</label>
                                    <div class="col-sm-9 col-xs-12">
                                        <select id='qrsize' class='form-control'>
                                            <option value='1'>1</option>
                                            <option value='2'>2</option>
                                            <option value='3'>3</option>
                                            <option value='4'>4</option>
                                            <option value='5'>5</option>
                                            <option value='6'>6</option>
                                        </select>
                                    </div>

                                </div>
                            </div>

                            <div id='nameset' style='display:none'>
                                <div class="form-group">
                                    <label class="col-lg control-label">昵称颜色</label>
                                    <div class="col-sm-9 col-xs-12">
                                        <?php  echo tpl_form_field_color('color')?>
                                    </div>

                                </div>
                                <div class="form-group">
                                    <label class="col-lg control-label">主体内容</label>
                                    <div class="col-sm-6 col-xs-6">
                                        <div>
                                            <input type="text" id="diycontent" class="form-control" placeholder="请输入内容 <?php  echo $i;?>" value="<?php  echo $data[0]['content'];?>"/>
                                            <div class='help-block'>模板变量: [昵称]</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg control-label">昵称大小</label>
                                    <div class="col-sm-4">
                                        <div class='input-group'>
                                            <input type="text" id="namesize" class="form-control namesize" placeholder="例如: 14,16"  />
                                            <div class='input-group-addon'>px</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg control-label">辅助对齐</label>
                                    <div class="col-sm-4">
                                        <div class='input-group'>
                                            <select id="alignment" class="form-control">
                                                <option value="-1"></option>
                                                <option value="0" <?php  if($data[0]['alignment']=="0") { ?>selected<?php  } ?>>居左</option>
                                                <option value="1" <?php  if($data[0]['alignment']=="1") { ?>selected<?php  } ?>>居中</option>
                                                <option value="2" <?php  if($data[0]['alignment']=="2") { ?>selected<?php  } ?>>居右</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group" id="imgset" style="display:none">
                                <label class="col-lg control-label">图片设置</label>
                                <div class="col-sm-9 col-xs-12">
                                    <?php  echo tpl_form_field_image2('img')?>
                                </div>
                            </div>

                        </div>
                    </div>
                    <?php  } ?>
                </td>
            </tr>
        </table>
    </div>
</div>
<!--e-f-w-w-w-->