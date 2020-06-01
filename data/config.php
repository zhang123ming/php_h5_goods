<?php
defined('IN_IA') or exit('Access Denied');

$config = array();
$config['db']['host'] = '127.0.0.1';
$config['db']['database'] = 'yeshi_kaifa1688_';//新项目请更换对应参数
$config['db']['username'] = 'root';//新项目请更换对应参数
$config['db']['password'] = 'root';//新项目请更换对应参数
$config['db']['port'] = '3306';
$config['db']['charset'] = 'utf8';
$config['db']['pconnect'] = 0;
$config['db']['tablepre'] = 'ims_';

// --------------------------  CONFIG COOKIE  --------------------------- //
$config['cookie']['pre'] = '57da_';
$config['cookie']['domain'] = '';
$config['cookie']['path'] = '/';

// --------------------------  CONFIG SETTING  --------------------------- //
$config['setting']['charset'] = 'utf-8';
$config['setting']['cache'] = 'mysql';
$config['setting']['timezone'] = 'Asia/Shanghai';
$config['setting']['memory_limit'] = '256M';
$config['setting']['filemode'] = 0644;
$config['setting']['authkey'] = 'yeshi';//新项目请更换对应参数
$config['setting']['founder'] = '1';
$config['setting']['development'] = 0;
$config['setting']['referrer'] = 0;

// --------------------------  CONFIG UPLOAD  --------------------------- //
$config['upload']['image']['extentions'] = array('gif', 'jpg', 'jpeg', 'png');
$config['upload']['image']['limit'] = 5000;
$config['upload']['attachdir'] = 'attachment';
$config['upload']['audio']['extentions'] = array('mp3');
$config['upload']['audio']['limit'] = 5000;

// --------------------------  HTTPS UP  --------------------------- //
$config['setting']['https'] = 0;

// redis配置 为标准系统配置，新布置站点 请更换数据库参数 $config['setting']['redis']['database']，保证与其他站点不一致
// $config['setting']['redis']['server'] = '127.0.0.1';//如果redis服务器在别的机器，请填写机器的IP地址。
// $config['setting']['redis']['port'] = 6379;
// $config['setting']['redis']['pconnect'] = 1;
// $config['setting']['redis']['timeout'] = 1;
// $config['setting']['redis']['requirepass'] = '';
// $config['setting']['redis']['database'] = 15;//同一服务器 不同站点确保数据库不一样
