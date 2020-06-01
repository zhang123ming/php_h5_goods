<?php
if (!(defined('IN_IA'))) 
{
	exit('Access Denied');
}
define('TM_ABONUS_PAY', 'TM_ABONUS_PAY');
define('TM_ABONUS_UPGRADE', 'TM_ABONUS_UPGRADE');
define('TM_ABONUS_BECOME', 'TM_ABONUS_BECOME');
if (!(class_exists('AbonusModel'))) 
{
	class GroupawardModel extends PluginModel 
	{
		public function getSet($uniacid = 0) 
		{
			$set = parent::getSet($uniacid);
			$set['texts'] = array('aagent' => (empty($set['texts']['aagent']) ? '区域代理' : $set['texts']['aagent']), 'center' => (empty($set['texts']['center']) ? '区域代理中心' : $set['texts']['center']), 'become' => (empty($set['texts']['become']) ? '成为区域代理' : $set['texts']['become']), 'bonus' => (empty($set['texts']['bonus']) ? '分红' : $set['texts']['bonus']), 'bonus_total' => (empty($set['texts']['bonus_total']) ? '累计分红' : $set['texts']['bonus_total']), 'bonus_lock' => (empty($set['texts']['bonus_lock']) ? '待结算分红' : $set['texts']['bonus_lock']), 'bonus_pay' => (empty($set['texts']['bonus_lock']) ? '已结算分红' : $set['texts']['bonus_pay']), 'bonus_wait' => (empty($set['texts']['bonus_wait']) ? '预计分红' : $set['texts']['bonus_wait']), 'bonus_detail' => (empty($set['texts']['bonus_detail']) ? '分红明细' : $set['texts']['bonus_detail']), 'bonus_charge' => (empty($set['texts']['bonus_charge']) ? '扣除个人所得税' : $set['texts']['bonus_charge']));
			return $set;
		}
		public function getLevels($all = true, $default = false) 
		{
			global $_W;
			if ($all) 
			{
				$levels = pdo_fetchall('select * from ' . tablename('ewei_shop_abonus_level') . ' where uniacid=:uniacid order by id asc', array(':uniacid' => $_W['uniacid']));
			}
			else 
			{
				$levels = pdo_fetchall('select * from ' . tablename('ewei_shop_abonus_level') . ' where uniacid=:uniacid and (ordermoney>0 or bonusmoney>0) order by bonus asc', array(':uniacid' => $_W['uniacid']));
			}
			if ($default) 
			{
				$default = array('id' => '0', 'levelname' => (empty($_S['abonus']['levelname']) ? '默认等级' : $_S['abonus']['levelname']), 'bonus1' => $_W['shopset']['abonus']['bonus1'], 'bonus2' => $_W['shopset']['abonus']['bonus2'], 'bonus3' => $_W['shopset']['abonus']['bonus3']);
				$levels = array_merge(array($default), $levels);
			}
			return $levels;
		}
		public function getBonus($openid = '', $params = array()) 
		{
			global $_W;
			if (in_array('ok', $params)) 
			{
				$ret['ok'] = pdo_fetchcolumn('select ifnull(sum(realProfit),0) from ' . tablename('ewei_shop_groupaward_billp') . ' where openid=:openid and status=1 and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':openid' => $openid));
			}
			if (in_array('lock', $params)) 
			{
				$ret['lock'] = pdo_fetchcolumn('select ifnull(sum(realProfit),0) from ' . tablename('ewei_shop_groupaward_billp') . ' where openid=:openid and status<>1 and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':openid' => $openid));
			}
			if (in_array('total', $params)) 
			{
				$ret['total'] = pdo_fetchcolumn('select ifnull(sum(realProfit),0) from ' . tablename('ewei_shop_groupaward_billp') . ' where openid=:openid and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':openid' => $openid));
			}
			return $ret;
		}
		public function sendMessage($openid = '', $data = array(), $message_type = '') 
		{
			global $_W;
			global $_GPC;
			$set = $this->getSet();
			$tm = $set['tm'];
			$templateid = $tm['templateid'];
			$member = m('member')->getMember($openid);
			$usernotice = unserialize($member['noticeset']);
			$advanced = '';
			if (!(is_array($usernotice))) 
			{
				$usernotice = array();
			}
			if (($message_type == TM_ABONUS_PAY) && empty($usernotice['abonus_pay'])) 
			{
				$message = '';
				$paytitle = '';
				if ($data['aagenttype'] == 1) 
				{
					$message = $tm['pay1'];
					$paytitle = ((empty($tm['paytitle1']) ? '省级代理分红发放通知' : $tm['paytitle1']));
				}
				else if ($data['aagenttype'] == 2) 
				{
					$message = $tm['pay2'];
					$paytitle = ((empty($tm['paytitle2']) ? '市级代理分红发放通知' : $tm['paytitle2']));
				}
				else if ($data['aagenttype'] == 3) 
				{
					$message = $tm['pay3'];
					$paytitle = ((empty($tm['paytitle3']) ? '区级代理分红发放通知' : $tm['paytitle3']));
				}
				if (empty($message)) 
				{
					return false;
				}
				$message = str_replace('[昵称]', $member['nickname'], $message);
				$message = str_replace('[时间]', date('Y-m-d H:i:s', time()), $message);
				$message = str_replace('[打款方式]', $data['type'], $message);
				if ($data['aagenttype'] == 1) 
				{
					$message = str_replace('[省级分红金额]', $data['paymoney1'], $message);
					$message = str_replace('[市级分红金额]', $data['paymoney2'], $message);
					$message = str_replace('[区级分红金额]', $data['paymoney3'], $message);
					$advanced = 'pay1_advanced';
				}
				else if ($data['aagenttype'] == 2) 
				{
					$message = str_replace('[市级分红金额]', $data['paymoney2'], $message);
					$message = str_replace('[区级分红金额]', $data['paymoney3'], $message);
					$advanced = 'pay2_advanced';
				}
				else if ($data['aagenttype'] == 3) 
				{
					$message = str_replace('[区级分红金额]', $data['paymoney3'], $message);
					$advanced = 'pay3_advanced';
				}
				$msg = array( 'keyword1' => array('value' => $paytitle, 'color' => '#73a68d'), 'keyword2' => array('value' => $message, 'color' => '#73a68d') );
				return $this->sendNotice($openid, $tm, $advanced, $data, $member, $msg);
			}
			if (($message_type == TM_ABONUS_UPGRADE) && empty($usernotice['abonus_upgrade'])) 
			{
				$message = $tm['upgrade'];
				$message = '';
				$message = '';
				$upgradetitle = '';
				if ($data['aagenttype'] == 1) 
				{
					$message = $tm['upgrade1'];
					$upgradetitle = ((empty($tm['upgradetitle1']) ? '省级代理等级升级通知' : $tm['upgradetitle1']));
				}
				else if ($data['aagenttype'] == 2) 
				{
					$message = $tm['upgrade2'];
					$upgradetitle = ((empty($tm['upgradetitle2']) ? '市级代理等级升级通知' : $tm['upgradetitle2']));
				}
				else if ($data['aagenttype'] == 3) 
				{
					$message = $tm['upgrade3'];
					$upgradetitle = ((empty($tm['upgradetitle3']) ? '区级代理等级升级通知' : $tm['upgradetitle3']));
				}
				if (empty($message)) 
				{
					return false;
				}
				$message = str_replace('[昵称]', $member['nickname'], $message);
				$message = str_replace('[时间]', date('Y-m-d H:i:s', time()), $message);
				$message = str_replace('[旧等级]', $data['oldlevel']['levelname'], $message);
				$message = str_replace('[新等级]', $data['newlevel']['levelname'], $message);
				$advanced = '';
				if ($member['aagenttype'] == 1) 
				{
					$message = str_replace('[旧省级分红比例]', $data['oldlevel']['bonus1'] . '%', $message);
					$message = str_replace('[旧市级分红比例]', $data['oldlevel']['bonus2'] . '%', $message);
					$message = str_replace('[旧区级分红比例]', $data['oldlevel']['bonus3'] . '%', $message);
					$message = str_replace('[新省级分红比例]', $data['newlevel']['bonus1'] . '%', $message);
					$message = str_replace('[新市级分红比例]', $data['newlevel']['bonus2'] . '%', $message);
					$message = str_replace('[新区级分红比例]', $data['newlevel']['bonus3'] . '%', $message);
					$advanced = 'upgrade1_advanced';
				}
				else if ($member['aagenttype'] == 2) 
				{
					$message = str_replace('[旧市级分红比例]', $data['oldlevel']['bonus2'] . '%', $message);
					$message = str_replace('[旧区级分红比例]', $data['oldlevel']['bonus3'] . '%', $message);
					$message = str_replace('[新市级分红比例]', $data['newlevel']['bonus2'] . '%', $message);
					$message = str_replace('[新区级分红比例]', $data['newlevel']['bonus3'] . '%', $message);
					$advanced = 'upgrade2_advanced';
				}
				else if ($member['aagenttype'] == 3) 
				{
					$message = str_replace('[旧区级分红比例]', $data['oldlevel']['bonus3'] . '%', $message);
					$message = str_replace('[新区级分红比例]', $data['newlevel']['bonus3'] . '%', $message);
					$advanced = 'upgrade3_advanced';
				}
				$msg = array( 'keyword1' => array('value' => $upgradetitle, 'color' => '#73a68d'), 'keyword2' => array('value' => $message, 'color' => '#73a68d') );
				return $this->sendNotice($openid, $tm, $advanced, $data, $member, $msg);
			}
			if (($message_type == TM_ABONUS_BECOME) && empty($usernotice['abonus_become'])) 
			{
				$message = $tm['become'];
				if (empty($message)) 
				{
					return false;
				}
				$message = str_replace('[昵称]', $data['nickname'], $message);
				$message = str_replace('[时间]', date('Y-m-d H:i:s', $data['aagenttime']), $message);
				if ($data['aagenttype'] == 1) 
				{
					$aagenttype = '省级代理';
				}
				else if ($data['aagenttype'] == 2) 
				{
					$aagenttype = '市级代理';
				}
				else if ($data['aagenttype'] == 3) 
				{
					$aagenttype = '区级代理';
				}
				$message = str_replace('[代理级别]', $aagenttype, $message);
				$message = str_replace('[代理区域]', $data['aagentareas'], $message);
				$msg = array( 'keyword1' => array('value' => (!(empty($tm['becometitle'])) ? $tm['becometitle'] : '成为区域代理通知'), 'color' => '#73a68d'), 'keyword2' => array('value' => $message, 'color' => '#73a68d') );
				return $this->sendNotice($openid, $tm, 'become_advanced', $data, $member, $msg);
			}
		}
		protected function sendNotice($touser, $tm, $tag, $datas, $member, $msg) 
		{
			global $_W;
			if (!(empty($tm['is_advanced'])) && !(empty($tm[$tag]))) 
			{
				$advanced_template = pdo_fetch('select * from ' . tablename('ewei_shop_member_message_template') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $tm[$tag], ':uniacid' => $_W['uniacid']));
				if (!(empty($advanced_template))) 
				{
					$url = ((!(empty($advanced_template['url'])) ? $this->replaceTemplate($advanced_template['url'], $tag, $datas, $member) : ''));
					$advanced_message = array( 'first' => array('value' => $this->replaceTemplate($advanced_template['first'], $tag, $datas, $member), 'color' => $advanced_template['firstcolor']), 'remark' => array('value' => $this->replaceTemplate($advanced_template['remark'], $tag, $datas, $member), 'color' => $advanced_template['remarkcolor']) );
					$data = iunserializer($advanced_template['data']);
					foreach ($data as $d ) 
					{
						$advanced_message[$d['keywords']] = array('value' => $this->replaceTemplate($d['value'], $tag, $datas, $member), 'color' => $d['color']);
					}
					if (!(empty($advanced_template['template_id']))) 
					{
						m('message')->sendTplNotice($touser, $advanced_template['template_id'], $advanced_message);
					}
					else 
					{
						m('message')->sendCustomNotice($touser, $advanced_message);
					}
				}
			}
			else if (!(empty($tm['templateid']))) 
			{
				m('message')->sendTplNotice($touser, $tm['templateid'], $msg);
			}
			else 
			{
				m('message')->sendCustomNotice($touser, $msg);
			}
			return true;
		}
		protected function replaceTemplate($str, $tag, $data, $member) 
		{
			$arr = array('[昵称]' => $member['nickname'], '[时间]' => date('Y-m-d H:i:s', time()), '[提现方式]' => (!(empty($data['type'])) ? $data['type'] : ''), '[旧等级]' => (!(empty($data['oldlevel']['levelname'])) ? $data['oldlevel']['levelname'] : ''), '[新等级]' => (!(empty($data['newlevel']['levelname'])) ? $data['newlevel']['levelname'] : ''));
			switch ($tag) 
			{
				case 'become_advanced': $arr['[时间]'] = date('Y-m-d H:i:s', $data['aagenttime']);
				$arr['[昵称]'] = $data['nickname'];
				case 'pay1_advanced': $arr['[昵称]'] = $data['nickname'];
				$arr['[时间]'] = date('Y-m-d H:i:s', $data['paytime']);
				$arr['[省级分红金额]'] = $data['paymoney1'];
				$arr['[市级分红金额]'] = $data['paymoney2'];
				$arr['[区级分红金额]'] = $data['paymoney3'];
				case 'pay2_advanced': $arr['[昵称]'] = $data['nickname'];
				$arr['[时间]'] = date('Y-m-d H:i:s', $data['paytime']);
				$arr['[市级分红金额]'] = $data['paymoney2'];
				$arr['[区级分红金额]'] = $data['paymoney3'];
				case 'pay3_advanced': $arr['[昵称]'] = $data['nickname'];
				$arr['[时间]'] = date('Y-m-d H:i:s', $data['paytime']);
				$arr['[区级分红金额]'] = $data['paymoney3'];
				case 'upgrade1_advanced': $arr['[旧省级分红比例]'] = ((!(empty($data['oldlevel']['bonus1'])) ? $data['oldlevel']['bonus1'] . '%' : ''));
				$arr['[旧市级分红金额]'] = ((!(empty($data['oldlevel']['bonus2'])) ? $data['oldlevel']['bonus2'] . '%' : ''));
				$arr['[旧区级分红金额]'] = ((!(empty($data['oldlevel']['bonus3'])) ? $data['oldlevel']['bonus3'] . '%' : ''));
				$arr['[新省级分红比例]'] = ((!(empty($data['newlevel']['bonus1'])) ? $data['newlevel']['bonus1'] . '%' : ''));
				$arr['[新市级分红金额]'] = ((!(empty($data['newlevel']['bonus2'])) ? $data['newlevel']['bonus2'] . '%' : ''));
				$arr['[新区级分红金额]'] = ((!(empty($data['newlevel']['bonus3'])) ? $data['newlevel']['bonus3'] . '%' : ''));
				case 'upgrade1_advanced': $arr['[旧市级分红金额]'] = ((!(empty($data['oldlevel']['bonus2'])) ? $data['oldlevel']['bonus2'] . '%' : ''));
				$arr['[旧区级分红金额]'] = ((!(empty($data['oldlevel']['bonus3'])) ? $data['oldlevel']['bonus3'] . '%' : ''));
				$arr['[新市级分红金额]'] = ((!(empty($data['newlevel']['bonus2'])) ? $data['newlevel']['bonus2'] . '%' : ''));
				$arr['[新区级分红金额]'] = ((!(empty($data['newlevel']['bonus3'])) ? $data['newlevel']['bonus3'] . '%' : ''));
				case 'upgrade3_advanced': $arr['[旧区级分红金额]'] = ((!(empty($data['oldlevel']['bonus3'])) ? $data['oldlevel']['bonus3'] . '%' : ''));
				$arr['[新区级分红金额]'] = ((!(empty($data['newlevel']['bonus3'])) ? $data['newlevel']['bonus3'] . '%' : ''));
			}
			foreach ($arr as $key => $value ) 
			{
				$str = str_replace($key, $value, $str);
			}
			return $str;
		}
		public function getLevel($openid) 
		{
			global $_W;
			if (empty($openid)) 
			{
				return false;
			}
			$member = m('member')->getMember($openid);
			if (empty($member['aagentlevel'])) 
			{
				return false;
			}
			$level = pdo_fetch('select * from ' . tablename('ewei_shop_abonus_level') . ' where uniacid=:uniacid and id=:id limit 1', array(':uniacid' => $_W['uniacid'], ':id' => $member['aagentlevel']));
			return $level;
		}
		public function getInfo($openid, $options = NULL) 
		{
			return p('commission')->getInfo($openid, $options);
		}
		public function upgradeLevelByOrder($openid) 
		{
			global $_W;
			if (empty($openid)) 
			{
				return false;
			}
			$set = $this->getSet();
			if (empty($set['open'])) 
			{
				return false;
			}
			$m = m('member')->getMember($openid);
			if (empty($m)) 
			{
				return;
			}
			$leveltype = intval($set['leveltype']);
			if (($leveltype == 4) || ($leveltype == 5)) 
			{
				if (!(empty($m['aagentnotupgrade']))) 
				{
					return;
				}
				$oldlevel = $this->getLevel($m['openid']);
				if (empty($oldlevel['id'])) 
				{
					$oldlevel = array('levelname' => (empty($set['levelname']) ? '普通代理商' : $set['levelname']), 'bonus' => $set['bonus']);
				}
				$orders = pdo_fetch('select sum(og.realprice) as ordermoney,count(distinct og.orderid) as ordercount from ' . tablename('ewei_shop_order') . ' o ' . ' left join  ' . tablename('ewei_shop_order_goods') . ' og on og.orderid=o.id ' . ' where o.openid=:openid and o.status>=3 and o.uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':openid' => $openid));
				$ordermoney = $orders['ordermoney'];
				$ordercount = $orders['ordercount'];
				if ($leveltype == 4) 
				{
					$newlevel = pdo_fetch('select * from ' . tablename('ewei_shop_abonus_level') . ' where uniacid=:uniacid  and ' . $ordermoney . ' >= ordermoney and ordermoney>0  order by ordermoney desc limit 1', array(':uniacid' => $_W['uniacid']));
					if (empty($newlevel)) 
					{
						return;
					}
					if (!(empty($oldlevel['id']))) 
					{
						if ($oldlevel['id'] == $newlevel['id']) 
						{
							return;
						}
						if ($newlevel['ordermoney'] < $oldlevel['ordermoney']) 
						{
							return;
							if ($leveltype == 5) 
							{
								$newlevel = pdo_fetch('select * from ' . tablename('ewei_shop_abonus_level') . ' where uniacid=:uniacid  and ' . $ordercount . ' >= ordercount and ordercount>0  order by ordercount desc limit 1', array(':uniacid' => $_W['uniacid']));
								if (empty($newlevel)) 
								{
									return;
								}
								if (!(empty($oldlevel['id']))) 
								{
									if ($oldlevel['id'] == $newlevel['id']) 
									{
										return;
									}
									if ($newlevel['ordercount'] < $oldlevel['ordercount']) 
									{
										return;
									}
								}
							}
						}
					}
				}
				else 
				{
					$newlevel = pdo_fetch('select * from ' . tablename('ewei_shop_abonus_level') . ' where uniacid=:uniacid  and ' . $ordercount . ' >= ordercount and ordercount>0  order by ordercount desc limit 1', array(':uniacid' => $_W['uniacid']));
					return;
					return;
					return;
				}
				pdo_update('ewei_shop_member', array('aagentlevel' => $newlevel['id']), array('id' => $m['id']));
				$this->sendMessage($m['openid'], array('nickname' => $m['nickname'], 'oldlevel' => $oldlevel, 'newlevel' => $newlevel), TM_ABONUS_UPGRADE);
			}
			else if ((0 <= $leveltype) && ($leveltype <= 3)) 
			{
				$agents = array();
				if (!(empty($set['selfbuy']))) 
				{
					$agents[] = $m;
				}
				if (!(empty($m['agentid']))) 
				{
					$m1 = m('member')->getMember($m['agentid']);
					if (!(empty($m1))) 
					{
						$agents[] = $m1;
						if (!(empty($m1['agentid'])) && ($m1['isagent'] == 1) && ($m1['status'] == 1)) 
						{
							$m2 = m('member')->getMember($m1['agentid']);
							if (!(empty($m2)) && ($m2['isagent'] == 1) && ($m2['status'] == 1)) 
							{
								$agents[] = $m2;
								if (empty($set['selfbuy'])) 
								{
									if (!(empty($m2['agentid'])) && ($m2['isagent'] == 1) && ($m2['status'] == 1)) 
									{
										$m3 = m('member')->getMember($m2['agentid']);
										if (!(empty($m3)) && ($m3['isagent'] == 1) && ($m3['status'] == 1)) 
										{
											$agents[] = $m3;
										}
									}
								}
							}
						}
					}
				}
				if (empty($agents)) 
				{
					return;
				}
				foreach ($agents as $agent ) 
				{
					$info = $this->getInfo($agent['id'], array('ordercount3', 'ordermoney3', 'order13money', 'order13'));
					if (!(empty($info['aagentnotupgrade']))) 
					{
						continue;
					}
					$oldlevel = $this->getLevel($agent['openid']);
					if (empty($oldlevel['id'])) 
					{
						$oldlevel = array('levelname' => (empty($set['levelname']) ? '普通代理商' : $set['levelname']), 'bonus' => $set['bonus']);
					}
					if ($leveltype == 0) 
					{
						$ordermoney = $info['ordermoney3'];
						$newlevel = pdo_fetch('select * from ' . tablename('ewei_shop_abonus_level') . ' where uniacid=:uniacid and ' . $ordermoney . ' >= ordermoney and ordermoney>0  order by ordermoney desc limit 1', array(':uniacid' => $_W['uniacid']));
						if (empty($newlevel)) 
						{
							continue;
						}
						if (!(empty($oldlevel['id']))) 
						{
							if ($oldlevel['id'] == $newlevel['id']) 
							{
								continue;
							}
							if ($newlevel['ordermoney'] < $oldlevel['ordermoney']) 
							{
								continue;
								if ($leveltype == 1) 
								{
									$ordermoney = $info['order13money'];
									$newlevel = pdo_fetch('select * from ' . tablename('ewei_shop_abonus_level') . ' where uniacid=:uniacid and ' . $ordermoney . ' >= ordermoney and ordermoney>0  order by ordermoney desc limit 1', array(':uniacid' => $_W['uniacid']));
									if (empty($newlevel)) 
									{
										continue;
									}
									if (!(empty($oldlevel['id']))) 
									{
										if ($oldlevel['id'] == $newlevel['id']) 
										{
											continue;
										}
										if ($newlevel['ordermoney'] < $oldlevel['ordermoney']) 
										{
											continue;
											if ($leveltype == 2) 
											{
												$ordercount = $info['ordercount3'];
												$newlevel = pdo_fetch('select * from ' . tablename('ewei_shop_abonus_level') . ' where uniacid=:uniacid  and ' . $ordercount . ' >= ordercount and ordercount>0  order by ordercount desc limit 1', array(':uniacid' => $_W['uniacid']));
												if (empty($newlevel)) 
												{
													continue;
												}
												if (!(empty($oldlevel['id']))) 
												{
													if ($oldlevel['id'] == $newlevel['id']) 
													{
														continue;
													}
													if ($newlevel['ordercount'] < $oldlevel['ordercount']) 
													{
														continue;
														if ($leveltype == 3) 
														{
															$ordercount = $info['order13'];
															$newlevel = pdo_fetch('select * from ' . tablename('ewei_shop_abonus_level') . ' where uniacid=:uniacid  and ' . $ordercount . ' >= ordercount and ordercount>0  order by ordercount desc limit 1', array(':uniacid' => $_W['uniacid']));
															if (empty($newlevel)) 
															{
																continue;
															}
															if (!(empty($oldlevel['id']))) 
															{
																if ($oldlevel['id'] == $newlevel['id']) 
																{
																	continue;
																}
																if ($newlevel['ordercount'] < $oldlevel['ordercount']) 
																{
																	continue;
																}
															}
														}
													}
												}
											}
											else 
											{
												$ordercount = $info['order13'];
												$newlevel = pdo_fetch('select * from ' . tablename('ewei_shop_abonus_level') . ' where uniacid=:uniacid  and ' . $ordercount . ' >= ordercount and ordercount>0  order by ordercount desc limit 1', array(':uniacid' => $_W['uniacid']));
												continue;
												continue;
												continue;
											}
										}
									}
								}
								else 
								{
									$ordercount = $info['ordercount3'];
									$newlevel = pdo_fetch('select * from ' . tablename('ewei_shop_abonus_level') . ' where uniacid=:uniacid  and ' . $ordercount . ' >= ordercount and ordercount>0  order by ordercount desc limit 1', array(':uniacid' => $_W['uniacid']));
									continue;
									continue;
									continue;
									$ordercount = $info['order13'];
									$newlevel = pdo_fetch('select * from ' . tablename('ewei_shop_abonus_level') . ' where uniacid=:uniacid  and ' . $ordercount . ' >= ordercount and ordercount>0  order by ordercount desc limit 1', array(':uniacid' => $_W['uniacid']));
									continue;
									continue;
									continue;
								}
							}
						}
					}
					else 
					{
						$ordermoney = $info['order13money'];
						$newlevel = pdo_fetch('select * from ' . tablename('ewei_shop_abonus_level') . ' where uniacid=:uniacid and ' . $ordermoney . ' >= ordermoney and ordermoney>0  order by ordermoney desc limit 1', array(':uniacid' => $_W['uniacid']));
						continue;
						continue;
						continue;
						$ordercount = $info['ordercount3'];
						$newlevel = pdo_fetch('select * from ' . tablename('ewei_shop_abonus_level') . ' where uniacid=:uniacid  and ' . $ordercount . ' >= ordercount and ordercount>0  order by ordercount desc limit 1', array(':uniacid' => $_W['uniacid']));
						continue;
						continue;
						continue;
						$ordercount = $info['order13'];
						$newlevel = pdo_fetch('select * from ' . tablename('ewei_shop_abonus_level') . ' where uniacid=:uniacid  and ' . $ordercount . ' >= ordercount and ordercount>0  order by ordercount desc limit 1', array(':uniacid' => $_W['uniacid']));
						continue;
						continue;
						continue;
					}
					pdo_update('ewei_shop_member', array('aagentlevel' => $newlevel['id']), array('id' => $agent['id']));
					$this->sendMessage($agent['openid'], array('nickname' => $agent['nickname'], 'oldlevel' => $oldlevel, 'newlevel' => $newlevel), TM_ABONUS_UPGRADE);
				}
			}
		}
		public function upgradeLevelByAgent($openid) 
		{
			global $_W;
			if (empty($openid)) 
			{
				return false;
			}
			$set = $this->getSet();
			if (empty($set['open'])) 
			{
				return false;
			}
			$m = m('member')->getMember($openid);
			if (empty($m)) 
			{
				return;
			}
			$leveltype = intval($set['leveltype']);
			if (($leveltype < 6) || (9 < $leveltype)) 
			{
				return;
			}
			$info = $this->getInfo($m['id'], array());
			if (($leveltype == 6) || ($leveltype == 8)) 
			{
				$agents = array($m);
				if (!(empty($m['agentid']))) 
				{
					$m1 = m('member')->getMember($m['agentid']);
					if (!(empty($m1))) 
					{
						$agents[] = $m1;
						if (!(empty($m1['agentid'])) && ($m1['isagent'] == 1) && ($m1['status'] == 1)) 
						{
							$m2 = m('member')->getMember($m1['agentid']);
							if (!(empty($m2)) && ($m2['isagent'] == 1) && ($m2['status'] == 1)) 
							{
								$agents[] = $m2;
							}
						}
					}
				}
				if (empty($agents)) 
				{
					return;
				}
				foreach ($agents as $agent ) 
				{
					$info = $this->getInfo($agent['id'], array());
					if (!(empty($info['aagentnotupgrade']))) 
					{
						continue;
					}
					$oldlevel = $this->getLevel($agent['openid']);
					if (empty($oldlevel['id'])) 
					{
						$oldlevel = array('levelname' => (empty($set['levelname']) ? '普通代理商' : $set['levelname']), 'bonus' => $set['bonus']);
					}
					if ($leveltype == 6) 
					{
                        $downcount = 0;
						$downs1 = pdo_fetchall('select id from ' . tablename('ewei_shop_member') . ' where agentid=:agentid and uniacid=:uniacid ', array(':agentid' => $m['id'], ':uniacid' => $_W['uniacid']), 'id');
						$downcount += count($downs1);
						if (!(empty($downs1))) 
						{
							$downs2 = pdo_fetchall('select id from ' . tablename('ewei_shop_member') . ' where agentid in( ' . implode(',', array_keys($downs1)) . ') and uniacid=:uniacid', array(':uniacid' => $_W['uniacid']), 'id');
							$downcount += count($downs2);
							if (!(empty($downs2))) 
							{
								$downs3 = pdo_fetchall('select id from ' . tablename('ewei_shop_member') . ' where agentid in( ' . implode(',', array_keys($downs2)) . ') and uniacid=:uniacid', array(':uniacid' => $_W['uniacid']), 'id');
								$downcount += count($downs3);
							}
						}
						$newlevel = pdo_fetch('select * from ' . tablename('ewei_shop_abonus_level') . ' where uniacid=:uniacid  and ' . $downcount . ' >= downcount and downcount>0  order by downcount desc limit 1', array(':uniacid' => $_W['uniacid']));
					}
					else if ($leveltype == 8) 
					{
						$downcount = $info['level1'] + $info['level2'] + $info['level3'];
						$newlevel = pdo_fetch('select * from ' . tablename('ewei_shop_abonus_level') . ' where uniacid=:uniacid  and ' . $downcount . ' >= downcount and downcount>0  order by downcount desc limit 1', array(':uniacid' => $_W['uniacid']));
					}
					if (empty($newlevel)) 
					{
						continue;
					}
					if ($newlevel['id'] == $oldlevel['id']) 
					{
						continue;
					}
					if (!(empty($oldlevel['id']))) 
					{
						if ($newlevel['downcount'] < $oldlevel['downcount']) 
						{
							continue;
						}
					}
					pdo_update('ewei_shop_member', array('aagentlevel' => $newlevel['id']), array('id' => $agent['id']));
					$this->sendMessage($agent['openid'], array('nickname' => $agent['nickname'], 'oldlevel' => $oldlevel, 'newlevel' => $newlevel), TM_ABONUS_UPGRADE);
				}
			}
			else if (!(empty($m['parnternotupgrade']))) 
			{
				return;
			}
			else 
			{
				$oldlevel = $this->getLevel($m['openid']);
				if (empty($oldlevel['id'])) 
				{
					$oldlevel = array('levelname' => (empty($set['levelname']) ? '普通代理商' : $set['levelname']), 'bonus' => $set['bonus']);
				}
				if ($leveltype == 7) 
				{
					$downcount = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_member') . ' where agentid=:agentid and uniacid=:uniacid ', array(':agentid' => $m['id'], ':uniacid' => $_W['uniacid']));
					$newlevel = pdo_fetch('select * from ' . tablename('ewei_shop_abonus_level') . ' where uniacid=:uniacid  and ' . $downcount . ' >= downcount and downcount>0  order by downcount desc limit 1', array(':uniacid' => $_W['uniacid']));
				}
				else if ($leveltype == 9) 
				{
					$downcount = $info['level1'];
					$newlevel = pdo_fetch('select * from ' . tablename('ewei_shop_abonus_level') . ' where uniacid=:uniacid  and ' . $downcount . ' >= downcount and downcount>0  order by downcount desc limit 1', array(':uniacid' => $_W['uniacid']));
				}
				if (empty($newlevel)) 
				{
					return;
				}
				if ($newlevel['id'] == $oldlevel['id']) 
				{
					return;
				}
				if (!(empty($oldlevel['id']))) 
				{
					if ($newlevel['downcount'] < $oldlevel['downcount']) 
					{
						return;
					}
				}
				pdo_update('ewei_shop_member', array('aagentlevel' => $newlevel['id']), array('id' => $m['id']));
				$this->sendMessage($m['openid'], array('nickname' => $m['nickname'], 'oldlevel' => $oldlevel, 'newlevel' => $newlevel), TM_ABONUS_UPGRADE);
			}
		}
		public function upgradeLevelByCommissionOK($openid) 
		{
			global $_W;
			if (empty($openid)) 
			{
				return false;
			}
			$set = $this->getSet();
			if (empty($set['open'])) 
			{
				return false;
			}
			$m = m('member')->getMember($openid);
			if (empty($m)) 
			{
				return;
			}
			$leveltype = intval($set['leveltype']);
			if ($leveltype != 10) 
			{
				return;
			}
			if (!(empty($m['aagentnotupgrade']))) 
			{
				return;
			}
			$oldlevel = $this->getLevel($m['openid']);
			if (empty($oldlevel['id'])) 
			{
				$oldlevel = array('levelname' => (empty($set['levelname']) ? '普通代理商' : $set['levelname']), 'bonus' => $set['bonus']);
			}
			$info = $this->getInfo($m['id'], array('pay'));
			$commissionmoney = $info['commission_pay'];
			$newlevel = pdo_fetch('select * from ' . tablename('ewei_shop_abonus_level') . ' where uniacid=:uniacid  and ' . $commissionmoney . ' >= commissionmoney and commissionmoney>0  order by commissionmoney desc limit 1', array(':uniacid' => $_W['uniacid']));
			if (empty($newlevel)) 
			{
				return;
			}
			if ($oldlevel['id'] == $newlevel['id']) 
			{
				return;
			}
			if (!(empty($oldlevel['id']))) 
			{
				if ($newlevel['commissionmoney'] < $oldlevel['commissionmoney']) 
				{
					return;
				}
			}
			pdo_update('ewei_shop_member', array('aagentlevel' => $newlevel['id']), array('id' => $m['id']));
			$this->sendMessage($m['openid'], array('nickname' => $m['nickname'], 'oldlevel' => $oldlevel, 'newlevel' => $newlevel), TM_ABONUS_UPGRADE);
		}
		public function upgradeLevelByBonus($openid) 
		{
			global $_W;
			if (empty($openid)) 
			{
				return false;
			}
			$set = $this->getSet();
			if (empty($set['open'])) 
			{
				return false;
			}
			$m = m('member')->getMember($openid);
			if (empty($m)) 
			{
				return;
			}
			$leveltype = intval($set['leveltype']);
			if ($leveltype != 11) 
			{
				return;
			}
			if (!(empty($m['aagentnotupgrade']))) 
			{
				return;
			}
			$oldlevel = $this->getLevel($m['openid']);
			if (empty($oldlevel['id'])) 
			{
				$oldlevel = array('levelname' => (empty($set['levelname']) ? '默认等级' : $set['levelname']), 'bonus1' => $set['bonus1'], 'bonus2' => $set['bonus2'], 'bonus3' => $set['bonus3'], 'bonusmoney' => 0);
			}
			$bonusmoney = $this->getBonus($openid, array('ok'));
			$newlevel = pdo_fetch('select * from ' . tablename('ewei_shop_abonus_level') . ' where uniacid=:uniacid  and ' . $bonusmoney['ok'] . ' >= bonusmoney and bonusmoney>0  order by bonusmoney desc limit 1', array(':uniacid' => $_W['uniacid']));
			if (empty($newlevel)) 
			{
				return;
			}
			if ($oldlevel['id'] == $newlevel['id']) 
			{
				return;
			}
			if (!(empty($oldlevel['id']))) 
			{
				if ($newlevel['bonusmoney'] < $oldlevel['bonusmoney']) 
				{
					return;
				}
			}
			pdo_update('ewei_shop_member', array('aagentlevel' => $newlevel['id']), array('id' => $m['id']));
			$member = m('member')->getMember($openid);
			$this->sendMessage($m['openid'], array('nickname' => $m['nickname'], 'aagenttype' => $member['aagenttype'], 'oldlevel' => $oldlevel, 'newlevel' => $newlevel), TM_ABONUS_UPGRADE);
		}
		public function resetMemberFids($id){
			global $_W;
			$id = intval($id);
			if (empty($id)) {
				return true;
			}
			$member = pdo_fetchall('select id,fids from '.tablename('ewei_shop_member').' where uniacid=:uniacid and find_in_set('.$id.',fids)',array(':uniacid'=>$_W['uniacid']));
			$arr = array();
			foreach ($member as $k => $v) {
				$arr = explode(',', $v['fids']);
				foreach ($arr as $kk => $vv) {
					if ($vv==$id) {
						break;
					}else{
						unset($arr[$kk]);
					}
				}
				$fids = implode(',', $arr);
				pdo_update('ewei_shop_member',array('fids' => $fids),array('id'=>$v['id']));
				
			}
			
		}
		public function getBonusData($year = 0, $month = 0, $week = 0, $openid = '') 
		{
			global $_W;
			$set = $this->getSet();
			$commissionSet = m('common')->getPluginset('commission');
			$days = get_last_day($year, $month);
			$starttime = strtotime($year . '-' . $month . '-1');
			$endtime = strtotime($year . '-' . $month . '-' . $days);
			$settletimes = intval($set['settledays']) * 86400;
			if ((1 <= $week) && ($week <= 4)) 
			{
				$weekdays = array();
				$i = $starttime;
				while ($i <= $endtime) 
				{
					$ds = explode('-', date('Y-m-d', $i));
					$day = intval($ds[2]);
					$w = ceil($day / 7);
					if (4 < $w) 
					{
						$w = 4;
					}
					if ($week == $w) 
					{
						$weekdays[] = $i;
					}
					$i += 86400;
				}
				$starttime = $weekdays[0];
				$endtime = strtotime(date('Y-m-d', $weekdays[count($weekdays) - 1]) . ' 23:59:59');
			}
			elseif (empty($month)) {
				$starttime = strtotime($year.'-01-01');
				$endtime = strtotime($year.'-12-31 23:59:59');
			}
			else 
			{
				$endtime = strtotime($year . '-' . $month . '-' . $days . ' 23:59:59');
			}
			$bill = pdo_fetch('select * from ' . tablename('ewei_shop_groupaward_bill') . ' where uniacid=:uniacid and `year`=:year and `month`=:month and `week`=:week limit 1', array(':uniacid' => $_W['uniacid'], ':year' => $year, ':month' => $month, ':week' => $week));
			if (!(empty($bill)) && empty($openid)) 
			{
				return array('ordermoney' => round($bill['ordermoney'], 2), 'ordercount' => $bill['ordercount'], 'totalProfit' => round($bill['totalProfit'], 2), 'totalAgent' => $bill['totalAgent'], 'starttime' => $starttime, 'endtime' => $endtime, 'billid' => $bill['id'], 'old' => true);
			}
			$ordermoney = 0;
            $ccondition = ' and isparent=0';
            if (!empty($set['orderpaytype'])) {
                $ccondition .= ' and isdeclaration=1';
            }
            if ($set['awardMode']==1||$set['awardMode']==3) {
                //唯美 订单完成后 计算
                $orders = pdo_fetchall('select id,openid,price from ' . tablename('ewei_shop_order') . ' where uniacid=' . $_W['uniacid'] . '  and status>=1 '.$ccondition.' and finishtime + ' . $settletimes . '>= ' . $starttime . ' and  finishtime + ' . $settletimes . '<=' . $endtime, array(), 'id');


            }elseif($set['awardMode']==4||$set['awardMode']==5){

                    $orders = pdo_fetch('select sum(price) as totalSale, count(*) as count from ' . tablename('ewei_shop_order') . ' where uniacid=' . $_W['uniacid'] . ' and status>=3 '.$ccondition.' and finishtime + ' . $settletimes . '>= ' . $starttime . ' and  finishtime + ' . $settletimes . '<=' . $endtime);
            }else{
                //密美集 订单付款后即可计算 awarMode == 2

                $orders = pdo_fetchall('select id,openid,price from ' . tablename('ewei_shop_order') . ' where uniacid=' . $_W['uniacid'] . '  and status>=1 '.$ccondition.' and paytime >= ' . $starttime . ' and  paytime <=' . $endtime, array(), 'id');
            }
			
			$pcondition = '';
			if (!(empty($openid))) 
			{
				$pcondition = ' and m.openid=\'' . $openid . '\'';
			}
			//find all agent 
			if ($set['awardMode']==1||$set['awardMode']==3) {
				$asql = 'select l.level,l.levelname, m.id,m.agentid,m.independent,m.openid,m.agent100,m.agent99,m.agent98,m.agent97,m.agent96,m.agent95,m.agent94,m.agent93,m.agent92,m.agent91 from '.tablename('ewei_shop_member').' m left join '.tablename('ewei_shop_member_level').' l on l.id=m.level where m.uniacid=:uniacid and m.isagent=1 and m.status=1 and l.level>='.$set['level'].$pcondition;
			}elseif($set['awardMode']==4||$set['awardMode']==5){
				$arr = pdo_fetchall('select id from'.tablename('ewei_shop_member').' where uniacid=:uniacid and status=1 and isagent=1',array(':uniacid'=>$_W['uniacid']));
				$ids = '';
				foreach($arr as $v){
					$sum = pdo_fetch('select sum(price) as sum from'.tablename('ewei_shop_order').' where find_in_set(\''.$v['id'].'\',fids) and status=3 and uniacid='.$_W['uniacid'])['sum'];
					if($sum>=($set['teamresults']*10000) && !empty($set['teamresults'])){
						$ids .= $v['id'].',';
					}
				}
                $condition = '';
				if(strlen($ids)>0){
					$condition .= ' and (m.id in ('.rtrim($ids,',').') or l.level>='.$set['level'].' ) ';
				}else{
					$condition .=" and l.level>=".$set['level'];
				}
				$asql = 'select m.level as mlevel,l.level,l.levelname, m.id,m.agentid,m.independent,m.openid,m.agent100,m.agent99,m.agent98,m.agent97,m.agent96,m.agent95,m.agent94,m.agent93,m.agent92,m.agent91 from '.tablename('ewei_shop_member').' m left join '.tablename('ewei_shop_member_level').' l on l.id=m.level where m.uniacid=:uniacid and m.isagent=1 and m.status=1 '.$condition.$pcondition;

			}else{
				if ($set['paytype']==3) {//蜜美集 年度奖励只给 发起人
					$asql = 'select l.level,l.levelname, m.id,m.agentid,m.independent,m.openid,m.agent100,m.agent99,m.agent98,m.agent97,m.agent96,m.agent95,m.agent94,m.agent93,m.agent92,m.agent91 from '.tablename('ewei_shop_member').' m left join '.tablename('ewei_shop_member_level').' l on l.id=m.level where m.uniacid=:uniacid and m.isagent=1 and m.status=1 and m.agentid=0'.$pcondition;
				}else{//默认给所有分销者
					$asql = 'select l.level,l.levelname, m.id,m.agentid,m.independent,m.openid,m.agent100,m.agent99,m.agent98,m.agent97,m.agent96,m.agent95,m.agent94,m.agent93,m.agent92,m.agent91 from '.tablename('ewei_shop_member').' m left join '.tablename('ewei_shop_member_level').' l on l.id=m.level where m.uniacid=:uniacid and m.isagent=1 and m.status=1 '.$pcondition;
				}
			}
			$agents = pdo_fetchall($asql,array(':uniacid'=>$_W['uniacid']));
			//统计有资格收益团队返利各等级人数
			if($set['awardMode']==5){
				$level_num = array();
				for($i = $set['level'];$i<=100;$i++){
					$level_num[$i] = 0;
				}
				foreach($agents as $agent){
					$level_num[$agent['level']]+=1;
				}
				$level_num = array_filter($level_num);
			}
			//calculate agent performance
			arsort($set['teamSale']);
			arsort($set['yteamSale']);
			$totalAgent = 0;
			$allProfit =0;
				foreach ($agents as  &$va) {
					$cdata[':uniacid'] = $_W['uniacid'];
					if ($set['awardMode']==1||$set['awardMode']==3) {
						#道悦唯美
						$csql = 'select sum(price) as totalSale from '.tablename('ewei_shop_order').'  where uniacid=:uniacid and status=3 '.$ccondition.' and (agent100=:agentid or agent99=:agentid or agent98=:agentid or agent97=:agentid or agent96=:agentid or agent95=:agentid or agent94=:agentid or agent93=:agentid or agent92=:agentid or agent91=:agentid) and finishtime + ' . $settletimes . '>= ' . $starttime . ' and  finishtime + ' . $settletimes . '<=' . $endtime;
						
						$cdata[':agentid'] = $va['id'];
					}else{
						if ($set['paytype']==3) { 
							//密美集
							$csql = 'select sum(price) as totalSale from '.tablename('ewei_shop_order').' where uniacid=:uniacid and status>=1 '.$ccondition.' and initialid='.$va['id'].' and paytime >= ' . $starttime . ' and  paytime <=' . $endtime;
						}else{
							$csql = 'select sum(price) as totalSale from '.tablename('ewei_shop_order').'  where uniacid=:uniacid and status>=1 '.$ccondition.' and find_in_set('.$va['id'].',fids) and paytime >= ' . $starttime . ' and  paytime <=' . $endtime.' ';
						}

					}
					if($set['awardMode']==4||$set['awardMode']==5){
						$total = $orders;
						$va['totalSale'] = !empty($total) ? $total['totalSale'] : 0;
					}else{
						$total = pdo_fetch($csql,$cdata);//计算总销售额	
						$va['totalSale'] = !empty($total['totalSale']) ? $total['totalSale'] : 0;
					}

					if ($set['paytype']==3&&$set['awardMode']==2) {
						foreach ($set['yteamSale'] as $tk => $tv) {
							if ($va['totalSale']>=$tv) {
								$va['totalProfit'] = $va['totalSale']*$set['yawardRate'][$tk]*0.01;
								break;
							}
						}
					}elseif($set['awardMode']==4){
						$va['totalProfit'] = round(($va['totalSale']*$set['awardRate']*0.01)/count($agents),2);
					}elseif($set['awardMode']==5){
						$va['totalProfit'] = round(($va['totalSale']*$set['awardRate'][$va['level']]*0.01)/$level_num[$va['level']],2);
					}else{
						foreach ($set['teamSale'] as $tk => $tv) {
							if ($va['totalSale']>=$tv) {
								$va['totalProfit'] = $va['totalSale']*$set['awardRate'][$tk]*0.01;
								break;
							}
						}
					}
					//计算同级分红返利
					if(!empty($va['totalProfit'])&&$set['awardRateSamegrade']&&$set['samegradeLevel']&&$set['awardMode']==3){
						$this->getTeamAgent($va['id'],$va['level']);
						if($GLOBALS['row']['id']){
							// $va['aagentid'] = rtrim($GLOBALS['row']['id'],',');
							$arr = explode(',',rtrim($GLOBALS['row']['id'],','));
							foreach($agents as &$item){
								for($i=0;$i<=count($arr);$i++) {
									if(($i+1)>$set['samegradeLevel']){
										continue;
									}else{
										if($arr[$i]==$item['id']){
											$item['samegrade'] += $va['totalSale']*$set['awardRateSamegrade']*0.01;
										}else{
											$samegrade = $va['totalSale']*$set['awardRateSamegrade']*0.01;
											$add = array('id'=>$arr[$i],'samegrade'=>$samegrade);
											array_push($agents,$add);
										}
									}
								}
							}
							unset($GLOBALS['row']);	
						}
						
					}

				}
				unset($va);

				foreach ($agents as $akey => &$vv) {
					if($set['awardMode']==4||$set['awardMode']==5){
							$vv['realProfit'] = $vv['totalProfit'];

					}else{
						$vv['realProfit'] = $vv['totalProfit']+$vv['samegrade'];
						foreach ($agents as $akk => $vvl) {
							if ($set['awardMode']==1) {
								# 道悦唯美...
								$tempdata['agent100'] = $vvl['agent100'];
								$tempdata['agent99'] = $vvl['agent99'];
								$tempdata['agent98'] = $vvl['agent98'];
								$tempdata['agent97'] = $vvl['agent97'];
								$tempdata['agent96'] = $vvl['agent96'];
								$tempdata['agent95'] = $vvl['agent95'];
								$tempdata['agent94'] = $vvl['agent94'];
								$tempdata['agent93'] = $vvl['agent93'];
								$tempdata['agent92'] = $vvl['agent92'];
								$tempdata['agent91'] = $vvl['agent91'];
								$tempdata =  array_filter($tempdata);
								$agentid = array_pop($tempdata);
								if (!empty($agentid)&&$agentid==$vv['id']&&$vv['level']>$vvl['level']) {
									$vv['realProfit'] -=$vvl['totalProfit'];
								}
							}elseif($set['awardMode']!=3){
								// 蜜美及
								if (!empty($vvl['agentid'])&&$vvl['agentid']==$vv['id']) {
									$vv['realProfit'] -=$vvl['totalProfit'];
								}
							}
						}	
					}
					$vv['realProfit'] = $vv['realProfit']>0 ? $vv['realProfit'] : 0;//计算实际分红
					$allProfit +=$vv['realProfit'];
					if ($vv['realProfit']>0) {
						$totalAgent +=1;
					}
				}

			$ordermoney=0;
			if($set['awardMode']==4||$set['awardMode']==5){
				$ordermoney = $orders['totalSale'];
				$ordercount = $orders['count'];
			}else{
				foreach ($orders as $k => $v) {
					$ordermoney +=$v['price'];
				}
				$ordercount = count($orders);		
			}
			return array('agents'=>$agents,'totalAgent'=>$totalAgent,'totalProfit'=>$allProfit,'agentGroup'=>$agentGroup,'orders' => $orders, 'aagents' => $aagents, 'ordermoney' => round($ordermoney, 2), 'ordercount' => $ordercount, 'bonusmoney1' => round($bonusmoney1, 2), 'bonusmoney2' => round($bonusmoney2, 2), 'bonusmoney3' => round($bonusmoney3, 2), 'aagentcount1' => $aagentcount1, 'aagentcount2' => $aagentcount2, 'aagentcount3' => $aagentcount3, 'starttime' => $starttime, 'endtime' => $endtime, 'old' => false);
		}
		public function getTotals() 
		{
			global $_W;
			return array('total0' => pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_abonus_bill') . ' where uniacid=:uniacid and status=0 limit 1', array(':uniacid' => $_W['uniacid'])), 'total1' => pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_abonus_bill') . ' where uniacid=:uniacid and status=1 limit 1', array(':uniacid' => $_W['uniacid'])), 'total2' => pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_abonus_bill') . ' where uniacid=:uniacid and status=2  limit 1', array(':uniacid' => $_W['uniacid'])));
		}
		/*
		*递归获取所有的上线
		*$params    $agentid   传入自己的agentid
		*/
		public function getTeamAgent($agentid,$level){
			global $_W;
			$agent = pdo_fetch('select id,agentid from'.tablename('ewei_shop_member').' where id ='.$agentid);
			if($agent['agentid']){
				$alevel = m('member')->getLevel($agent['agentid'])['level'];
				if($level==$alevel){
					$GLOBALS['row']['id'] .= $agent['agentid'].',';
				}
				$this->getTeamAgent($agent['agentid'],$level);
			}else{
				$GLOBALS['row']['id'] .= '';
			}		
		}
	}
}
?>