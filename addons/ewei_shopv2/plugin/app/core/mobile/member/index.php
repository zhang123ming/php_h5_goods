<?php
  
?>
<?php
if (!(defined('IN_IA'))) {
	exit('Access Denied');
}


require EWEI_SHOPV2_PLUGIN . 'app/core/page_mobile.php';
class Index_EweiShopV2Page extends AppMobilePage
{
	public function main()
	{
		global $_W;
		global $_GPC;
		$member = $this->member;

		if (empty($member)) {
			app_error(AppError::$UserNotFound);
		}


		$level = m('member')->getLevel($_W['openid']);
		$open_creditshop = p('creditshop') && $_W['shopset']['creditshop']['centeropen'];
		$params = array(':uniacid' => $_W['uniacid'], ':openid' => $_W['openid']);
		$merch_plugin = p('merch');
		$merch_data = m('common')->getPluginset('merch');
		if ($merch_plugin && $merch_data['is_openmerch']) {
			$statics = array('order_0' => pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_order') . ' where openid=:openid and status=0  and uniacid=:uniacid limit 1', $params), 'order_1' => pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_order') . ' where openid=:openid and status=1 and refundid=0 and uniacid=:uniacid limit 1', $params), 'order_2' => pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_order') . ' where openid=:openid and status=2 and refundid=0 and uniacid=:uniacid limit 1', $params), 'order_4' => pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_order') . ' where openid=:openid and refundstate=1 and uniacid=:uniacid limit 1', $params), 'cart' => pdo_fetchcolumn('select ifnull(sum(total),0) from ' . tablename('ewei_shop_member_cart') . ' where uniacid=:uniacid and openid=:openid and deleted=0 ', $params), 'favorite' => pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_member_favorite') . ' where uniacid=:uniacid and openid=:openid and deleted=0 ', $params));
		}
		 else {
			$statics = array('order_0' => pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_order') . ' where openid=:openid and ismr=0 and status=0  and uniacid=:uniacid and isparent=0 limit 1', $params), 'order_1' => pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_order') . ' where openid=:openid and ismr=0 and status=1 and refundid=0 and uniacid=:uniacid and isparent=0 limit 1', $params), 'order_2' => pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_order') . ' where openid=:openid and ismr=0 and status=2 and refundid=0 and uniacid=:uniacid and isparent=0 limit 1', $params), 'order_4' => pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_order') . ' where openid=:openid and ismr=0 and refundstate=1 and uniacid=:uniacid and isparent=0 limit 1', $params), 'cart' => pdo_fetchcolumn('select ifnull(sum(total),0) from ' . tablename('ewei_shop_member_cart') . ' where uniacid=:uniacid and openid=:openid and deleted=0 and selected = 1', $params), 'favorite' => pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_member_favorite') . ' where uniacid=:uniacid and openid=:openid and deleted=0 and `type`=0', $params));
		}

		$hascoupon = false;
		$hascouponcenter = false;
		$plugin_coupon = com('coupon');

		if ($plugin_coupon) {
			$time = time();
			$sql = 'select count(*) from ' . tablename('ewei_shop_coupon_data') . ' d';
			$sql .= ' left join ' . tablename('ewei_shop_coupon') . ' c on d.couponid = c.id';
			$sql .= ' where d.openid=:openid and d.uniacid=:uniacid and  d.used=0 ';
			$sql .= ' and (   (c.timelimit = 0 and ( c.timedays=0 or c.timedays*86400 + d.gettime >=unix_timestamp() ) )  or  (c.timelimit =1 and c.timestart<=' . $time . ' && c.timeend>=' . $time . ')) order by d.gettime desc';
			$statics['coupon'] = pdo_fetchcolumn($sql, array(':openid' => $_W['openid'], ':uniacid' => $_W['uniacid']));
			$pcset = $_W['shopset']['coupon'];

			if (empty($pcset['closemember'])) {
				$hascoupon = true;
				$coupon_text = '领取优惠券';
			}


			if (empty($pcset['closecenter'])) {
				$hascouponcenter = true;
			}


			$couponcenter_text = '我的优惠券';
		}


		$hasglobonus = false;
		$plugin_globonus = p('globonus');

		if ($plugin_globonus) {
			$plugin_globonus_set = $plugin_globonus->getSet();
			$hasglobonus = !(empty($plugin_globonus_set['open'])) && !(empty($plugin_globonus_set['openmembercenter']));
		}


		$hasabonus = false;
		$plugin_abonus = p('abonus');

		if ($plugin_abonus) {
			$plugin_abonus_set = $plugin_abonus->getSet();
			$hasabonus = !(empty($plugin_abonus_set['open'])) && !(empty($plugin_abonus_set['openmembercenter']));

			if ($hasabonus) {
				$abonus_text = m('plugin')->getName('abonus');

				if (empty($abonus_text)) {
					$abonus_text = '区域代理';
				}

			}

		}


		$hasqa = false;
		$plugin_qa = p('qa');

		if ($plugin_qa) {
			$plugin_qa_set = $plugin_qa->getSet();

			if (!(empty($plugin_qa_set['showmember']))) {
				$hasqa = true;
				$qa_text = m('plugin')->getName('qa');

				if (empty($qa_text)) {
					$qa_text = '帮助中心';
				}

			}

		}


		$hassign = false;
		$com_sign = p('sign');

		if ($com_sign) {
			$com_sign_set = $com_sign->getSet();

			if (!(empty($com_sign_set['iscenter']))) {
				$hassign = true;
				$sign_text = ((empty($_W['shopset']['trade']['credittext']) ? ''.$_W['shopset']['trade']['credittext'].'' : $_W['shopset']['trade']['credittext']));
				$sign_text .= ((empty($com_sign_set['textsign']) ? '签到' : $com_sign_set['textsign']));
				$url_sign = mobileUrl('sign', NULL, true);
				$sign_url_arr = explode('?', $url_sign);
				$sign_url_domain = $sign_url_arr[0];
				$sign_url_params = urlencode($sign_url_arr[1]);

				if (empty($sign_text)) {
					$sign_text = ''.$_W['shopset']['trade']['credittext'].'签到';
				}

			}

		}


		$commission = false;
		$commission_text = '';
		$commission_url = '';
		if (p('commission') && intval(0 < $_W['shopset']['commission']['level']) && empty($_W['shopset']['app']['hidecom'])) {
			$commission = true;

			if (!($member['agentblack'])) {
				if (($member['isagent'] == 1) && ($member['status'] == 1)) {
					$commission_url = '/pages/commission/index';
					$commission_text = ((empty($_W['shopset']['commission']['texts']['center']) ? '分销中心' : $_W['shopset']['commission']['texts']['center']));
				}
				 else {
					$commission_url = '/pages/commission/register/index';
					$commission_text = ((empty($_W['shopset']['commission']['texts']['become']) ? '成为分销商' : $_W['shopset']['commission']['texts']['become']));
				}
			}

		}


		$copyright = m('common')->getCopyright();
		$hasFullback = true;
		$ishidden = m('common')->getSysset('fullback');

		if ($ishidden['ishidden'] == true) {
			$hasFullback = false;
		}


		$haveverifygoods = m('verifygoods')->checkhaveverifygoods($_W['openid']);

		if (!(empty($haveverifygoods))) {
			$verifygoods = m('verifygoods')->getCanUseVerifygoods($_W['openid']);
		}

		$leveldeadline=!empty($member['leveldeadline'])?date('Y-m-d',$member['leveldeadline']):"无限期";
		$result = array('id' => $member['id'], 'avatar' => $member['avatar'], 'nickname' => $member['nickname'], 'openid'=>$member['openid'],'moneytext' => $_W['shopset']['trade']['moneytext'], 'credittext' => $_W['shopset']['trade']['credittext'], 'credit1' => $member['credit1'], 'credit2' => $member['credit2'], 'open_recharge' => (empty($_W['shopset']['trade']['closerecharge']) ? 1 : 0), 'open_creditshop' => intval($open_creditshop), 'open_withdraw' => intval($_W['shopset']['trade']['withdraw']), 'logtext' => ($_W['shopset']['trade']['withdraw'] == 1 ? $_W['shopset']['trade']['moneytext'] . '明细' : '充值记录'), 'levelurl' => ($_W['shopset']['shop']['levelurl'] == NULL ? '' : $_W['shopset']['shop']['levelurl']), 'levelname' => (empty($level['id']) ? ((empty($_W['shopset']['shop']['levelname']) ? '普通会员' : $_W['shopset']['shop']['levelname'])) : $level['levelname']), 'statics' => $statics, 'haveverifygoods' => $haveverifygoods, 'verifygoods' => $verifygoods, 'hascoupon' => $hascoupon, 'hasFullback' => $hasFullback, 'coupon_text' => $coupon_text, 'hascouponcenter' => $hascouponcenter, 'couponcenter_text' => $couponcenter_text, 'hasabonus' => $hasabonus, 'abonus_text' => $abonus_text, 'commission' => $commission, 'commission_text' => $commission_text, 'commission_url' => $commission_url, 'hasqa' => $hasqa, 'qa_text' => $qa_text, 'hassign' => $hassign, 'sign_text' => $sign_text, 'sign_url_domain' => $sign_url_domain, 'sign_url_params' => $sign_url_params, 'hasrank' => intval($_W['shopset']['rank']['status']) == 1, 'rank_text' => ''.$_W['shopset']['trade']['credittext'].'排行', 'hasorderrank' => intval($_W['shopset']['rank']['order_status']) == 1, 'orderrank_text' => '消费排行', 'copyright' => (!(empty($copyright)) && !(empty($copyright['copyright'])) ? $copyright['copyright'] : ''), 'customer' => intval($_W['shopset']['app']['customer']), 'phone' => intval($_W['shopset']['app']['phone']),'background'=>$level['background'],'leveldeadline'=>$leveldeadline);

		if (!(empty($result['customer']))) {
			$result['customercolor'] = ((empty($_W['shopset']['app']['customercolor']) ? '#ff55555' : $_W['shopset']['app']['customercolor']));
		}


		if (!empty($result['phone'])) {
			$result['phonecolor'] = ((empty($_W['shopset']['app']['phonecolor']) ? '#ff5555' : $_W['shopset']['app']['phonecolor']));
			$result['phonenumber'] = ((empty($_W['shopset']['app']['phonenumber']) ? '#ff5555' : $_W['shopset']['app']['phonenumber']));
		}

		if (empty($member['mobileverify']) || empty($member['mobile'])) {
			if (!(empty($_W['shopset']['app']['openbind'])) || !(empty($_W['shopset']['wap']['open']))) {
				$result['needbind'] = 1;

				$result['bindtext'] = ((!(empty($_W['shopset']['app']['openbind'])) && !(empty($_W['shopset']['app']['bindtext'])) ? $_W['shopset']['app']['bindtext'] : '绑定手机号可合并或同步您其他账号数据'));
			}

		}

		$memberSet=m('common')->getSysset('member');
		if (!empty($memberSet['openapply'])) {
			if (!empty($memberSet['limitgrade'])) {
				
				$sql = "SELECT * FROM ".tablename('ewei_shop_member_level')." WHERE uniacid=$_W[uniacid] AND id=$memberSet[limitgrade]";
				$limitgrade=pdo_fetch($sql);

				$sql = "SELECT * FROM ".tablename('ewei_shop_member_level')." WHERE uniacid=$_W[uniacid] AND id=$member[level]";

				$currentgrade = pdo_fetch($sql);
				if ($currentgrade[level]<$limitgrade[level]) {
					//不满足条件
					$result['notsatisfygrade']=false;
					$result['jumpUrl']='/pages/member/user/apply';
				}

			}
			
		}

		
		$plugin_bargain = checkPlugin('bargain');
		$result['bargain'] = $plugin_bargain;


		app_json($result);
	}

	public function userLevelInfo(){
		global $_W;
		global $_GPC;
		$member = $this->member;

		if (empty($member)) {
			app_error(AppError::$UserNotFound);
		}

		$level = m('member')->getLevel($_W['openid']);

		$result = array('id' => $member['id'], 'avatar' => $member['avatar'],'leveldeadline'=>date('Y-m-d H:i',$member['leveldeadline']), 'nickname' => $member['nickname'],  'levelname' => (empty($level['id']) ? ((empty($_W['shopset']['shop']['levelname']) ? '普通会员' : $_W['shopset']['shop']['levelname'])) : $level['levelname']),'background'=>$level['background'],'content'=>$level['content']);

		app_json($result);


	}
	/**
	 * [bindMacid 床保姆绑定 蓝牙设备mac地址]
	 * @Author   seanyang         
	 * @DateTime 2018-12-28T11:14:26+0800
	 * @return   [type]                   [description]
	 */
	public function bindMacid(){
		global $_W,$_GPC;
		$member = $this->member;
		$macid = $_GPC['macid'];
		//查询是否绑定过
		$hasmacid = pdo_fetch('select * from '.tablename('ewei_shop_machine_rent_record').' where uniacid=:uniacid and macid=:macid  and status in (1,2)',array(':uniacid'=>$_W['uniacid'],':macid'=>$macid));
		if (empty($hasmacid)) {
			// 没有绑定 绑定新用户 同时激活
			$rent_record = pdo_fetch('select * from '.tablename('ewei_shop_machine_rent_record')." where uniacid=:uniacid and openid=:openid and macid='' and status in (1,2) limit 1",array(':uniacid'=>$_W['uniacid'],':openid'=>$_W['openid']));
			if (!empty($rent_record)) {
				$data = array(
					'begintime'=>time(),
					'endtime'=>strtotime('+'.$rent_record['periodtime'].' day'),
					'macid'=>$macid,
				);
				pdo_update('ewei_shop_machine_rent_record',$data,array('id'=>$rent_record['id']));
			}
		}else{
			
		}
		
	}
	/**
	 * [checkMacid 检测蓝牙是否激活]
	 * @Author   seanyang                
	 * @DateTime 2019-01-11T15:36:43+0800
	 * @return   [type]                   [description]
	 */
	public function checkMacid(){
		global $_W,$_GPC;
		$macid = $_GPC['macid'];
		if (empty($macid)) {
			return app_json(array('mac_status'=>0,'message'=>'设备未激活，不可使用'));
		}
		$machine = pdo_fetch('select * from '.tablename('ewei_shop_machine_rent_record').' where uniacid=:uniacid and macid=:macid and status in (1,2)',array(':uniacid'=>$_W['uniacid'],':macid'=>$macid));
		if (!empty($machine)) {
			if ($machine['endtime']<time()&&$machine['status']==2 || $machine['status']==-1) {
				pdo_update('ewei_shop_machine_rent_record',array('status'=>-1),array('id'=>$machine['id']));
				return app_json(array('mac_status'=>0,'message'=>'设备到期，不可使用'));
			}
			return app_json(array('mac_status'=>1,'message'=>'设备已激活'));
		}else{
			return app_json(array('mac_status'=>0,'message'=>'设备未激活，不可使用'));
		}
	}
	/**
	 * 记录设备故障
	 * @Author   seanyang                 
	 * @DateTime 2019-02-25T15:28:14+0800
	 * @return   [type]                   [description]
	 */
	public function errorLog(){
		global $_W,$_GPC;
		$code = $_GPC['code'];
		$macid = $_GPC['macid'];
		$machine = pdo_fetch('select * from '.tablename('ewei_shop_machine_rent_record').' where uniacid=:uniacid and macid=:macid and status in (1,2)',array(':uniacid'=>$_W['uniacid'],':macid'=>$macid));
		$arr = array('101'=>'左侧身电机故障','102'=>'床帘电机故障','103'=>'右侧身电机故障','104'=>'抬腿电机故障','105'=>'升降电机故障','106'=>'抬腰电机故障','107'=>'倾斜电机故障','108'=>'大背电机故障');
		if ($arr[$code]&&!empty($machine)) {
			$data = array(
				'uniacid'=>$_W['uniacid'],
				'macid'=>$macid,
				'error_code'=>$code,
				'message'=>$arr[$code],
				'createtime'=>time(),
				'merchid'=>$machine['merchid']
			);
			pdo_insert('ewei_shiop_machine_error_log',$data);
			return app_json(array('status' => 1,'msg'=>'蓝牙：'.$macid.$arr[$code]));
		}else{
			return app_json(array('status'=>0,'msg'=>'success'));
		}
		
	}
}


?>