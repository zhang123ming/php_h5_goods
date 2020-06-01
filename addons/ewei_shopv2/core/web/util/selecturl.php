<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Selecturl_EweiShopV2Page extends WebPage
{
	protected $full = false;
	protected $platform = false;

	public function __construct()
	{
		global $_W;
		global $_GPC;
		$this->full = intval($_GPC['full']);
		$this->platform = trim($_GPC['platform']);
		$this->defaultUrl = trim($_GPC['url']);
	}

	public function main()
	{
		global $_W;
		global $_GPC;
		$syscate = m('common')->getSysset('category');

		if (0 < $syscate['level']) {
			$categorys = pdo_fetchall('SELECT id,name,parentid FROM ' . tablename('ewei_shop_category') . ' WHERE enabled=:enabled and uniacid= :uniacid  ', array(':uniacid' => $_W['uniacid'], ':enabled' => '1'));
		}
		$groups = pdo_fetchall('select id,name from '.tablename('ewei_shop_goods_group').' where uniacid=:uniacid and enabled=:enabled', array(':uniacid' => $_W['uniacid'], ':enabled' => '1'));
		if (p('diypage')) {
			$diypage = p('diypage')->getPageList();

			if (!empty($diypage)) {
				$allpagetype = p('diypage')->getPageType();
			}
		}

		if (p('quick')) {
			$quickList = p('quick')->getPageList();
		}


		$platform = $this->platform;
		$full = $this->full;

		if ($platform == 'wxapp' && !empty($this->defaultUrl) && strexists($this->defaultUrl, '/pages/web/index')) {
			$defaultUrl = urldecode($this->defaultUrl);
			$defaultUrl = str_replace('/pages/web/index?url=https://', '', $defaultUrl);
		}
		$allUrls = array(
			array(
				'name' => '商城页面',
				'list' => array(
					array('name' => '商城首页', 'url' => mobileUrl(NULL, NULL, $full), 'url_wxapp' => '/pages/index/index'),
					array('name' => '分类导航', 'url' => mobileUrl('shop/category', NULL, $full), 'url_wxapp' => '/pages/shop/caregory/index'),
					array('name' => '全部商品', 'url' => mobileUrl('goods', NULL, $full), 'url_wxapp' => '/pages/goods/index/index'),
					// array('name' => '秒杀首页', 'url' => mobileUrl('seckill', NULL, $full), 'url_wxapp' => '/seckill/pages/index/index'),
					array('name' => '公告页面', 'url' => mobileUrl('shop/notice', NULL, $full), 'url_wxapp' => '/pages/shop/notice/index/index'),
					array('name' => '关于我们', 'url' => '', 'url_wxapp' => '/pages/shop/about/index'),
					array('name' => '购物车', 'url' => mobileUrl('member/cart', NULL, $full), 'url_wxapp' => '/pages/member/cart/index')
					)
				),
			array(
				'name' => '信息提交',
				'list' => array(
					array('name' => '自定义信息提交', 'url' => mobileUrl('member/appointment', NULL, $full), 'url_wxapp' => '/pages/member/appointment/index'),
					)
				),
			array(
				'name' => '商品属性',
				'list' => array(
					array('name' => '推荐商品', 'url' => mobileUrl('goods', array('isrecommand' => 1), $full), 'url_wxapp' => '/pages/goods/index/index?isrecommand=1'),
					array('name' => '新品上市', 'url' => mobileUrl('goods', array('isnew' => 1), $full), 'url_wxapp' => '/pages/goods/index/index?isnew=1'),
					array('name' => '热卖商品', 'url' => mobileUrl('goods', array('ishot' => 1), $full), 'url_wxapp' => '/pages/goods/index/index?ishot=1'),
					array('name' => '促销商品', 'url' => mobileUrl('goods', array('isdiscount' => 1), $full), 'url_wxapp' => '/pages/goods/index/index?isdiscount=1'),
					array('name' => '卖家包邮', 'url' => mobileUrl('goods', array('issendfree' => 1), $full), 'url_wxapp' => '/pages/goods/index/index?issendfree=1'),
					array('name' => '限时抢购', 'url' => mobileUrl('goods', array('istime' => 1), $full), 'url_wxapp' => '/pages/goods/index/index?istime=1')
					)
				),
			array(
				'name' => '会员中心',
				'list' => array(
					0  => array('name' => '会员中心', 'url' => mobileUrl('member', NULL, $full), 'url_wxapp' => '/pages/member/index/index'),
					1  => array('name' => '我的订单(全部)', 'url' => mobileUrl('order', NULL, $full), 'url_wxapp' => '/pages/order/index'),
					2  => array('name' => '待付款订单', 'url' => mobileUrl('order', array('status' => 0), $full), 'url_wxapp' => '/pages/order/index?status=0'),
					3  => array('name' => '待发货订单', 'url' => mobileUrl('order', array('status' => 1), $full), 'url_wxapp' => '/pages/order/index?status=1'),
					4  => array('name' => '待收货订单', 'url' => mobileUrl('order', array('status' => 2), $full), 'url_wxapp' => '/pages/order/index?status=2'),
					5  => array('name' => '退换货订单', 'url' => mobileUrl('order', array('status' => 4), $full), 'url_wxapp' => '/pages/order/index?status=4'),
					6  => array('name' => '已完成订单', 'url' => mobileUrl('order', array('status' => 3), $full), 'url_wxapp' => '/pages/order/index?status=3'),
					7  => array('name' => '我的收藏', 'url' => mobileUrl('member/favorite', array(), $full), 'url_wxapp' => '/pages/member/favorite/index'),
					8  => array('name' => '我的足迹', 'url' => mobileUrl('member/history', array(), $full), 'url_wxapp' => '/pages/member/history/index'),
					9  => array('name' => '会员充值', 'url' => mobileUrl('member/recharge', array(), $full), 'url_wxapp' => '/pages/member/recharge/index'),
					10 => array('name' => '余额明细', 'url' => mobileUrl('member/log', array(), $full), 'url_wxapp' => '/pages/member/log/index'),
					11 => array('name' => '余额提现', 'url' => mobileUrl('member/withdraw', array(), $full), 'url_wxapp' => '/pages/member/withdraw/index'),
					12 => array('name' => '我的资料', 'url' => mobileUrl('member/info', array(), $full), 'url_wxapp' => '/pages/member/info/index'),
					13 => array('name' => ''.$_W['shopset']['trade']['credittext'].'排行', 'url' => mobileUrl('member/rank', array(), $full), 'url_wxapp' => ''),
					14 => array('name' => '消费排行', 'url' => mobileUrl('member/rank/order_rank', array(), $full), 'url_wxapp' => ''),
					16 => array('name' => '收货地址管理', 'url' => mobileUrl('member/address', array(), $full), 'url_wxapp' => '/pages/member/address/index'),
					// 18 => array('name' => '我的全返', 'url' => mobileUrl('member/fullback', array(), $full), 'url_wxapp' => ''),
					// 19 => array('name' => '记次时商品', 'url' => mobileUrl('verifygoods', array(), $full), 'url_wxapp' => '/pages/verifygoods/index'),
					20 => array('name' => '会员申请页', 'url' => mobileUrl('', array(), $full), 'url_wxapp' => '/pages/member/user/apply'),
					21 => array('name' => '会员权益页', 'url' => mobileUrl('', array(), $full), 'url_wxapp' => '/pages/member/user/index'),
					22 => array('name' => '线下消费送优惠券', 'url' => mobileUrl('member/couponcharge', array(), $full), 'url_wxapp' => ''),
					// 24  => array('name' => '门店列表', 'url' => mobileUrl(NULL, NULL, $full), 'url_wxapp' => '/pages/order/store/index'),
					// 25 => array('name' => '我的团队', 'url' => mobileUrl('member/myteam', array(), $full), 'url_wxapp' => ''),
					// 26 => array('name' => '线下核销', 'url' => mobileUrl('verify/page', array(), $full), 'url_wxapp' => ''),
					// 27 => array('name' => '我的门店', 'url' => mobileUrl('member/myowner', array(), $full), 'url_wxapp' => ''),
					28 => array('name' => '线下充值', 'url' => mobileUrl('member/collect', array(), $full), 'url_wxapp' => ''),
					32 => array('name' => '积分明细', 'url' => mobileUrl('member/integral', array(), $full), 'url_wxapp' => '/pages/member/log/credit'),
					38 => array('name' => '我的评论', 'url' =>'', 'url_wxapp' => '/pages/member/comment/index'),
					39 => array('name' => '余额转账', 'url' => mobileUrl('member/transfer', array(), $full), 'url_wxapp' => ''),
					40 => array('name' => '代理中心', 'url' => mobileUrl('member/agent', array(), $full), 'url_wxapp' => ''),
					)
				)
			// array(
			// 	'name' => '商家入驻',
			// 	'list' => array(
			// 		0  => array('name' => '商家入驻', 'url' => mobileUrl('merch/register', array(), $full), 'url_wxapp' => '/pages/changce/merch/apply'),
			// 		1  => array('name' => '商家列表', 'url' => mobileUrl('merch/list/merchuser', array(), $full), 'url_wxapp' => '/pages/changce/merch/index'),
			// 		3  => array('name' => '商家地图', 'url' => mobileUrl('merch/nearby_map', array(), $full), 'url_wxapp' => ''),
			// 		)
			// 	)
			);
		if ($platform) {
			unset($allUrls[2]['list'][13]);
			unset($allUrls[2]['list'][14]);
			unset($allUrls[2]['list'][15]);
			unset($allUrls[2]['list'][18]);
		}

		if ($platform) {
			$customs = pdo_fetchall('select id,`name` from ' . tablename('ewei_shop_wxapp_page') . ' where uniacid = :uniacid and `type` = 20 and status = 1 ', array(':uniacid' => $_W['uniacid']));
			if (!empty($customs) && $platform != 'wxapp_tabbar') {
				$addUrl = array(
					'name' => '自定义页面',
					'list' => array()
					);
				$urllist = array();

				foreach ($customs as $key => $value) {
					if ($type == 'topmenu') {
						$urllist[$key] = array('name' => $value['name'], 'url' => '', 'url_wxapp' => '/pages/custom/index?pageid=' . $value['id']);
						$diypage = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_wxapp_page') . ' WHERE id=:id AND uniacid = :uniacid', array(':id' => $value['id'], ':uniacid' => $_W['uniacid']));
						$diypageData = json_decode(base64_decode($diypage['data']), true);

						if (!empty($diypageData['items'])) {
							foreach ($diypageData['items'] as $dk => $dv) {
								if ($dv['id'] == 'topmenu') {
									unset($urllist[$key]);
								}
							}
						}
					}
					else {
						$urllist[$key] = array('name' => $value['name'], 'url' => '', 'url_wxapp' => '/pages/custom/index?pageid=' . $value['id']);
					}
				}

				$addUrl['list'] = $urllist;
				array_push($allUrls, $addUrl);
			}

			unset($allUrls[2]['list'][13]);
			unset($allUrls[2]['list'][14]);
			unset($allUrls[2]['list'][15]);
			unset($allUrls[2]['list'][18]);
			unset($allUrls[2]['list'][19]);

			if ($platform == 'wxapp_tabbar') {
				unset($allUrls[1]);
				unset($allUrls[2]['list'][2]);
				unset($allUrls[2]['list'][3]);
				unset($allUrls[2]['list'][4]);
				unset($allUrls[2]['list'][5]);
				unset($allUrls[2]['list'][6]);
			}
		}



		if (p('commission')) {
			$allUrls[] = array(
	'name' => m('plugin')->getName('commission'),
	'list' => array(
		array('name' => '分销中心', 'url' => mobileUrl('commission', NULL, $full), 'url_wxapp' => '/pages/commission/index'),
		array('name' => '成为分销商', 'url' => mobileUrl('commission/register', NULL, $full), 'url_wxapp' => '/pages/commission/register/index'),
		array('name' => '我的小店', 'url' => mobileUrl('commission/myshop', NULL, $full), 'url_wxapp' => '/pages/commission/index'),
		// array('name' => '分销佣金/佣金提现', 'url' => mobileUrl('commission/withdraw2', NULL, $full), 'url_wxapp' => '/pages/commission/withdraw/index'),
		array('name' => '佣金提现', 'url' => mobileUrl('commission/withdraw2', NULL, $full), 'url_wxapp' => '/pages/commission/apply/seconds'),
		array('name' => '佣金明细', 'url' => mobileUrl('commission/order', NULL, $full), 'url_wxapp' => 'pages/commission/order/index'),
		array('name' => '我的下线', 'url' => mobileUrl('commission/down', NULL, $full), 'url_wxapp' => '/pages/commission/down/index'),
		array('name' => '提现明细', 'url' => mobileUrl('commission/log', NULL, $full), 'url_wxapp' => '/pages/commission/log/index'),
		array('name' => '发票记录', 'url' => mobileUrl('invoicing', NULL, $full), 'url_wxapp' => '/pages/member/invoice/index'),
		array('name' => '推广二维码', 'url' => mobileUrl('commission/qrcode', NULL, $full), 'url_wxapp' => '/pages/commission/qrcode/index'),
		array('name' => '小店设置', 'url' => mobileUrl('commission/myshop/set', NULL, $full), 'url_wxapp' => '/pages/commission/index'),
		array('name' => '佣金排名', 'url' => mobileUrl('commission/rank', NULL, $full), 'url_wxapp' => ''),
		array('name' => '自选商品', 'url' => mobileUrl('commission/myshop/select', NULL, $full), 'url_wxapp' => ''),
		array('name' => '分销佣金', 'url' => mobileUrl('commission/cashbe', NULL, $full), 'url_wxapp' => '/pages/commission/cashbe/index'),
		array('name' => '订单列表', 'url' => mobileUrl('commission/all_order', NULL, $full), 'url_wxapp' => ''),
		// array('name' => '分销/代理佣金', 'url' => mobileUrl('commission/withdrawlist', NULL, $full)),
		array('name' => '授权证书', 'url' => mobileUrl('commission/auth', NULL, $full), 'url_wxapp' => ''),
		),
		
	);
		}

		if (p('article')) {
			$allUrls[] = array(
	'name' => m('plugin')->getName('article'),
	'list' => array(
		array('name' => '文章列表页面', 'url' => mobileUrl('article/list', NULL, $full), 'url_wxapp' => '/pages/article/index')
		)
	);
		}


			$allUrls[] = array(
	'name' => '超级券',
	'list' => array(
		array('name' => '领取优惠券', 'url' => mobileUrl('sale/coupon', NULL, $full), 'url_wxapp' => '/pages/sale/coupon/index/index'),
		array('name' => '我的优惠券', 'url' => mobileUrl('sale/coupon/my', NULL, $full), 'url_wxapp' => '/pages/sale/coupon/my/index/index')
		)
	);


		if (p('groups')) {
			$allUrls[] = array(
	'name' => m('plugin')->getName('groups'),
	'list' => array(
		array('name' => '拼团首页', 'url' => mobileUrl('groups', NULL, $full), 'url_wxapp' => '/application/pages/groups/index/index'),
		array('name' => '活动列表', 'url' => mobileUrl('groups/category', NULL, $full), 'url_wxapp' => '/application/pages/groups/category/index'),
		array('name' => '我的订单', 'url' => mobileUrl('groups/orders', NULL, $full), 'url_wxapp' => '/application/pages/groups/orders/index'),
		array('name' => '我的团', 'url' => mobileUrl('groups/team', NULL, $full), 'url_wxapp' => '/application/pages/groups/team/index')
		)
	);
		}

		if (p('mr')) {
			$allUrls[] = array(
	'name' => m('plugin')->getName('mr'),
	'list' => array(
		array('name' => '充值页面', 'url' => mobileUrl('mr', NULL, $full), 'url_wxapp' => '/pages/mr/index/index'),
		array('name' => '充值记录', 'url' => mobileUrl('mr/order', NULL, $full), 'url_wxapp' => '/pages/mr/order/index')
		)
	);
		}

		if (p('sns')) {
			$allUrls[] = array(
	'name' => m('plugin')->getName('sns'),
	'list' => array(
		array('name' => '社区首页', 'url' => mobileUrl('sns', NULL, $full), 'url_wxapp' => '/application/pages/sns/index/index'),
		array('name' => '全部板块', 'url' => mobileUrl('sns/board/lists', NULL, $full), 'url_wxapp' => '/application/pages/sns/blocks/index'),
		array('name' => '我的社区', 'url' => mobileUrl('sns/user', NULL, $full), 'url_wxapp' => '/application/pages/sns/user/index')
		)
	);
		}

		if (p('bottledoctor')) {
			$allUrls[] = array(
				'name' => m('plugin')->getName('bottledoctor'),
				'list' => array(
					array('name' => '交流', 'url' => mobileUrl('bottledoctor', NULL, $full), 'url_wxapp' => '/application/pages/communication/mothercommunication/index'),
					array('name' => '互动', 'url' => mobileUrl('bottledoctor', NULL, $full), 'url_wxapp' => '/application/pages/communication/doctorcommunication/communicationlist/index'),
					
					)
				);
		}

		

		if (p('sign')) {
			$allUrls[] = array(
	'name' => m('plugin')->getName('sign'),
	'list' => array(
		array('name' => '签到首页', 'url' => mobileUrl('sign', NULL, $full), 'url_wxapp' => '/pages/sign/index'),
		array('name' => '签到排行', 'url' => mobileUrl('sign/rank', NULL, $full), 'url_wxapp' => '/pages/sign/rank/rank')
		)
	);
		}

		if (p('qa')) {
			$allUrls[] = array(
	'name' => m('plugin')->getName('qa'),
	'list' => array(
		array('name' => '帮助首页', 'url' => mobileUrl('qa', NULL, $full), 'url_wxapp' => '/pages/qa/index/index'),
		array('name' => '全部分类', 'url' => mobileUrl('qa/category', NULL, $full), 'url_wxapp' => '/pages/qa/category/index'),
		array('name' => '全部问题', 'url' => mobileUrl('qa/question', NULL, $full), 'url_wxapp' => '/pages/qa/question/index')
		)
	);
		}

		if (p('bargain')) {
			$allUrls[] = array(
	'name' => m('plugin')->getName('bargain'),
	'list' => array(
		array('name' => '砍价首页', 'url' => mobileUrl('bargain', NULL, $full), 'url_wxapp' => '/application/pages/bargain/index/index')
		)
	);
		}

		if (p('task')) {
			$allUrls[] = array(
	'name' => m('plugin')->getName('task'),
	'list' => array(
		array('name' => '首页', 'url' => mobileUrl('task', NULL, $full), 'url_wxapp' => '/pages/task/index/index')
		)
	);
		}

		if (p('creditshop')) {
			$allUrls[] = array(
	'name' => m('plugin')->getName('creditshop'),
	'list' => array(
		array('name' => '商城首页', 'url' => mobileUrl('creditshop', NULL, $full), 'url_wxapp' => '/application/pages/creditshop/index'),
		array('name' => '全部商品', 'url' => mobileUrl('creditshop/lists', NULL, $full), 'url_wxapp' => '/application/pages/creditshop/lists/index'),
		array('name' => '我的', 'url' => mobileUrl('creditshop/log', NULL, $full), 'url_wxapp' => '/application/pages/creditshop/log/index'),
		array('name' => '参与记录', 'url' => mobileUrl('creditshop/creditlog', NULL, $full), 'url_wxapp' => '/application/pages/creditshop/creditlog/index')
		)
	);
		}

		if (p('seckill')) {
			$allUrls[] = array(
	'name' => m('plugin')->getName('seckill'),
	'list' => array(
		array('name' => '秒杀首页', 'url' => mobileUrl('seckill', NULL, $full), 'url_wxapp' => '/application/pages/seckill/index/index')
		)
	);
		}

		if (p('newstore')) {
			$allUrls[] = array(
	'name' => m('plugin')->getName('newstore'),
	'list' => array(
		array('name' => '门店列表', 'url' => mobileUrl('newstore/stores', NULL, $full), 'url_wxapp' => '/pages/newstore/stores/index')
		)
	);
		}

		if(p('live')){
			$allUrls[] = array(
				'name' => m('plugin')->getName('live'),
				'list' => array(
					array('name' => '直播首页', 'url' => mobileUrl('live', NULL, $full)),
					array('name' => '直播管理/申请','url' => mobileUrl('live/myroom',NULL,$full))
				)
			);
		}
		include $this->template();
	}

	public function query()
	{
		global $_W;
		global $_GPC;
		$type = trim($_GPC['type']);
		$kw = trim($_GPC['kw']);
		$full = intval($_GPC['full']);
		$platform = trim($_GPC['platform']);
		if (!empty($kw) && !empty($type)) {
			if ($type == 'good') {
				$list = pdo_fetchall('SELECT id,title,productprice,marketprice,thumb,sales,unit,minprice FROM ' . tablename('ewei_shop_goods') . ' WHERE uniacid= :uniacid and status=:status and deleted=0 AND title LIKE :title ', array(':title' => '%' . $kw . '%', ':uniacid' => $_W['uniacid'], ':status' => '1'));
				$list = set_medias($list, 'thumb');
			}
			else if ($type == 'article') {
				$list = pdo_fetchall('select id,article_title from ' . tablename('ewei_shop_article') . ' where article_title LIKE :title and article_state=1 and uniacid=:uniacid ', array(':uniacid' => $_W['uniacid'], ':title' => '%' . $kw . '%'));
			}
			else if ($type == 'coupon') {
				$list = pdo_fetchall('select id,couponname,coupontype from ' . tablename('ewei_shop_coupon') . ' where couponname LIKE :title and uniacid=:uniacid ', array(':uniacid' => $_W['uniacid'], ':title' => '%' . $kw . '%'));
			}
			else if ($type == 'groups') {
				$list = pdo_fetchall('select id,title from ' . tablename('ewei_shop_groups_goods') . ' where title LIKE :title and status=1 and deleted=0 and uniacid=:uniacid ', array(':uniacid' => $_W['uniacid'], ':title' => '%' . $kw . '%'));
			}
			else if ($type == 'sns') {
				$list_board = pdo_fetchall('select id,title from ' . tablename('ewei_shop_sns_board') . ' where title LIKE :title and status=1 and enabled=0 and uniacid=:uniacid order by id desc ', array(':uniacid' => $_W['uniacid'], ':title' => '%' . $kw . '%'));
				$list_post = pdo_fetchall('select id,title from ' . tablename('ewei_shop_sns_post') . ' where title LIKE :title and checked=1 and deleted=0 and uniacid=:uniacid order by id desc ', array(':uniacid' => $_W['uniacid'], ':title' => '%' . $kw . '%'));
				$list = array();
				if (!empty($list_board) && is_array($list_board)) {
					foreach ($list_board as &$board) {
						$board['type'] = 0;
						$board['url'] = mobileUrl('sns/board', array('id' => $board['id'], 'page' => 1), $full);
					}

					unset($board);
					$list = array_merge($list, $list_board);
				}

				if (!empty($list_post) && is_array($list_post)) {
					foreach ($list_post as &$post) {
						$post['type'] = 1;
						$post['url'] = mobileUrl('sns/post', array('id' => $post['id']), $full);
					}

					unset($post);
					$list = array_merge($list, $list_post);
				}
			}
			else {
				if ($type == 'creditshop') {
					$list = pdo_fetchall('select id, thumb, title, price, credit, money from ' . tablename('ewei_shop_creditshop_goods') . ' where title LIKE :title and status=1 and deleted=0 and uniacid=:uniacid ', array(':uniacid' => $_W['uniacid'], ':title' => '%' . $kw . '%'));
				}
			}
		}

		include $this->template('util/selecturl_tpl');
	}
}

?>
