<?php
/**
 * 企业官网DIY模块定义
 *
 * @author 众惠科技
 * @url http://bbs.we7.cc/
 */
global $_W;
defined('IN_IA') or exit('Access Denied');
define('ST_ROOT',IA_ROOT.'/addons/zofui_sitetemp/');
define('ST_URL',$_W['siteroot'].'addons/zofui_sitetemp/');
define('MODULE','zofui_sitetemp');
require_once(ST_ROOT.'class/autoload.php');

class Zofui_sitetempModule extends WeModule {

	public function settingsDisplay($settings) {
		global $_W, $_GPC;

    	
		if(checksubmit('submit')) {
			$_GPC = Util::trimWithArray($_GPC);


			$dat = array(
				'frompass' => $_GPC['frompass'],
				'mail' => $_GPC['mail'],
            );


			if ($this->saveSettings($dat)) {
                message('保存成功', 'refresh');
            }
		}
		
		//设置模板id
		/*if( empty( $settings['mid'] ) ) {

			load() -> model('account');
			$account = WeAccount::create( $_W['acid'] );
			$access_token = $account->getAccessToken();	

			if( !empty( $access_token ) ) {
				$res = $this->getMidList($access_token,0,20);

				$res = json_decode($res,true);
				if( !empty( $res ) && $res['errmsg'] == 'ok' && $res['errcode'] == '0' && !empty( $res['list'] ) ) {

					foreach ($res['list'] as $v ) {
						if( $v['title'] == '待办事务提醒' ) {
							$template_id = $v['template_id'];
							$isset = 1;
							break;
						}
					}

					// 查余下的5个
					if( !$isset && count( $res['list'] ) >= 20 ){
						$res = $this->getMidList($access_token,20,5);

						$res = json_decode($res,true);
						if( !empty( $res ) && $res['errmsg'] == 'ok' && $res['errcode'] == '0' && !empty( $res['list'] ) ) {

							foreach ($res['list'] as $v ) {
								if( $v['title'] == '待办事务提醒' ) {
									$template_id = $v['template_id'];
									$isset = 1;
									break;
								}

							}
						}
					}
				}
				if( !$isset ) { // 不存在 加入
					$url = 'https://api.weixin.qq.com/cgi-bin/wxopen/template/add?access_token='.$access_token;
					$res = Util::httpPost( $url , json_encode( array('id'=>'AT0279','keyword_id_list'=>array(16,17)) ) );

					$res = json_decode( $res , true );
					if( !empty( $res['template_id'] ) ) {
						$template_id = $res['template_id'];
					}
				}

				if( !empty( $template_id ) ) {
					$settings['mid'] = $template_id;
					$this->saveSettings($settings);
				}
			}
		}*/
		//



	    if(!pdo_indexexists('zofui_sitetemp_bar', 'tempid')) {
	      pdo_query("ALTER TABLE ".tablename('zofui_sitetemp_bar')." ADD INDEX tempid(`tempid`);");
	    }
	    
	    if(!pdo_indexexists('zofui_sitetemp_page', 'tempid')) {
	      pdo_query("ALTER TABLE ".tablename('zofui_sitetemp_page')." ADD INDEX tempid(`tempid`);");
	    }
	    
	    if(!pdo_indexexists('zofui_sitetemp_temp', 'number')) {
	      pdo_query("ALTER TABLE ".tablename('zofui_sitetemp_temp')." ADD INDEX number(`number`);");
	    }

		pdo_query("CREATE TABLE IF NOT EXISTS ".tablename('zofui_sitetemp_admin')." (
		  	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		  	`uniacid` int(11) unsigned NOT NULL DEFAULT '0',
		  	`openid` varchar(64) DEFAULT '',
		 	 PRIMARY KEY (`id`),
		 	 KEY `uniacid` (`uniacid`),
		 	 KEY `openid` (`openid`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

	  	pdo_query("CREATE TABLE IF NOT EXISTS ".tablename('zofui_sitetemp_copyright')." (
		  	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			`status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0开启 1关闭',
			`content` text,
			PRIMARY KEY (`id`),
			KEY `uniacid` (`status`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;");


	    if(!pdo_fieldexists('zofui_sitetemp_temp', 'issetsystem')) {
	      pdo_query("ALTER TABLE ".tablename('zofui_sitetemp_temp')." ADD `issetsystem` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否平台自己设置的系统模板 0不是 1是的';");
	    }

	  	pdo_query("CREATE TABLE IF NOT EXISTS ".tablename('zofui_sitetemp_article')." (
		    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		    `uniacid` int(11) unsigned NOT NULL DEFAULT '0',
		    `content` mediumtext COMMENT '文章内容',
		    `title` varchar(2000) DEFAULT NULL,
		    `img` varchar(350) DEFAULT NULL COMMENT '封面图片',
		    `number` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '排序 越大越前',
		    `time` varchar(15) DEFAULT NULL COMMENT '时间',
		    `readed` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '阅读量',
		    `author` varchar(64) DEFAULT NULL COMMENT '作者',
		    PRIMARY KEY (`id`),
		    KEY `uniacid` (`uniacid`),
		    KEY `number` (`number`)
	  	) ENGINE=MyISAM DEFAULT CHARSET=utf8;");


	 	pdo_query("CREATE TABLE IF NOT EXISTS ".tablename('zofui_sitetemp_smtp')." (
		    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		    `uniacid` int(11) unsigned NOT NULL DEFAULT '0',
		    `type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0网易 1QQ',
		    `account` varchar(64) DEFAULT NULL COMMENT '发送账户',
		    `pass` varchar(64) DEFAULT NULL COMMENT '授权码',
		    `name` varchar(64) DEFAULT NULL,
		    `sign` varchar(255) DEFAULT NULL,
		    PRIMARY KEY (`id`),
		    KEY `uniacid` (`uniacid`)
	  	) ENGINE=MyISAM DEFAULT CHARSET=utf8;");
	 	

	 	
	    pdo_query("CREATE TABLE IF NOT EXISTS ".tablename('zofui_sitetemp_artsort')." (
	        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	        `uniacid` int(11) unsigned NOT NULL DEFAULT '0',
	        `name` varchar(32) DEFAULT NULL,
	        `number` int(10) NOT NULL DEFAULT '0' COMMENT '排序序号 越大越前',
	        PRIMARY KEY (`id`),
	        KEY `uniacid` (`uniacid`)
	    ) ENGINE=MyISAM  DEFAULT CHARSET=utf8;");
	    
	    
	    if(!pdo_fieldexists('zofui_sitetemp_article', 'sortid')) {
	        pdo_query("ALTER TABLE ".tablename('zofui_sitetemp_article')." ADD `sortid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '分类id';");
	    }	  	
	 	


		include $this->template('web/setting');
	}




	public function getMidList($token,$start,$num){

		$url = 'https://api.weixin.qq.com/cgi-bin/wxopen/template/list?access_token='.$token;
		$res = Util::httpPost( $url , json_encode( array('offset'=>$start,'count'=>$num) ) );
		return $res;
	}


}