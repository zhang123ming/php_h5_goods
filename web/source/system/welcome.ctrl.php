<?php
/**
 * [WECHAT]Copyright (c) 2015 xxxxx.com
 
 */
$_W['page']['title'] = '系统';

load()->model('cloud');

$cloud_registered = cloud_prepare();
$cloud_registered = $cloud_registered === true ? true : false;

template('system/welcome');
