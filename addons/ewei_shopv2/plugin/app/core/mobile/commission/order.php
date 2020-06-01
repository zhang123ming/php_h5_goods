<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

require __DIR__ . '/base.php';
class Order_EweiShopV2Page extends Base_EweiShopV2Page
{

	public function get_list()
	{
		global $_W;
		global $_GPC;

		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		$openid = $_W['openid'];
		$member = $this->model->getInfo2($openid);
		$agentLevel = $this->model->getLevel($openid);
		$level = intval($this->set['level']);
		$set = $this->getSet();
		$status = trim($_GPC['status']);
		$condition = '';
		if ($status !='') {
			$condition .=' and log.issend = '.$status;
		}

		$sql = 'select log.*,o.ordersn,o.openid as buyer_openid from '.tablename('ewei_shop_commission_order_log').' log left join '.tablename('ewei_shop_order').' o on o.id=log.orderid where log.uniacid=:uniacid and log.openid=:openid '.$condition.' order by log.createtime desc limit '.($pindex - 1) * $psize.','. $psize;
		$params[':uniacid']=$_W['uniacid'];
		$params[':openid'] = $openid;
		$list = pdo_fetchall($sql,$params);
		foreach ($list as &$v) {
			$v['createtime'] = date('Y-m-d H:i:s',$v['createtime']);
			$v['status'] = empty($v['issend']) ? '未发放':'已发放';
			if (!empty($this->set['openorderdetail'])) {
					$goods = pdo_fetchall('SELECT og.id,og.goodsid,g.thumb,og.price,og.total,g.title,og.optionname from ' . tablename('ewei_shop_order_goods') . ' og' . ' left join ' . tablename('ewei_shop_goods') . ' g on g.id=og.goodsid  ' . ' where og.orderid=:orderid and og.nocommission=0 and og.uniacid = :uniacid order by og.createtime  desc ', array(':uniacid' => $_W['uniacid'], ':orderid' => $v['orderid']));
					$goods = set_medias($goods, 'thumb');
					$v['order_goods'] = set_medias($goods, 'thumb');
				}
				if (!empty($this->set['openorderbuyer'])) {
					$v['buyer'] = m('member')->getMember($v['buyer_openid']);
				}
		}
		$total = pdo_fetchcolumn('select count(*) from '.tablename('ewei_shop_commission_order_log').' log where log.uniacid=:uniacid and log.openid=:openid '.$condition,array(':uniacid'=>$_W['uniacid'],':openid'=>$openid));

		$comtotal = $member['commission_total'];
		
		app_json(array('list' => $list, 'pagesize' => $psize, 'total' => $total, 'comtotal' => $comtotal, 'textyuan' => $this->set['texts']['yuan'], 'textorder' => $this->set['texts']['order'], 'textctotal' => $this->set['texts']['commission_total'], 'openorderdetail' => $this->set['openorderdetail'], 'openorderbuyer' => $this->set['openorderbuyer']));
	}

	public function get_orderlist()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$mid = $_GPC['mopenid'];

		if (empty($mid)) {
			app_error(AppError::$ParamsError);
		}
		$member=m('member')->getMember($mid);
		$openid=$member['openid'];

		$pindex = max(1, intval($_GPC['page']));
		$psize = 10;
		$show_status = $_GPC['status'];
		$r_type = array('退款', '退货退款', '换货');
		$condition = ' and openid=:openid and ismr=0 and deleted=0 and uniacid=:uniacid ';
		$params = array(':uniacid' => $uniacid, ':openid' => $openid);
		$merchdata = $this->merchData();
		extract($merchdata);
		$condition .= ' and merchshow=0 ';

		if ($show_status != '') {
			$show_status = intval($show_status);

			switch ($show_status) {
			case 0:
				$condition .= ' and status=0 and paytype!=3';
				break;

			case 2:
				$condition .= ' and (status=2 or status=0 and paytype=3)';
				break;

			case 4:
				$condition .= ' and refundstate>0';
				break;

			case 5:
				$condition .= ' and userdeleted=1 ';
				break;

			default:
				$condition .= ' and status=' . intval($show_status);
			}

			if ($show_status != 5) {
				$condition .= ' and userdeleted=0 ';
			}

		}
		 else {
			$condition .= ' and userdeleted=0 ';
		}

		$com_verify = com('verify');
		$list = pdo_fetchall('select id,ordersn,price,invoice,invoiceprice,userdeleted,isparent,refundstate,paytype,status,addressid,refundid,isverify,dispatchtype,verifytype,verifyinfo,verifycode,iscomment,isCash,earnest,isprepay,balance,isprepaysuccess from ' . tablename('ewei_shop_order') . ' where 1 ' . $condition . ' order by createtime desc LIMIT ' . (($pindex - 1) * $psize) . ',' . $psize, $params);
		$total = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_order') . ' where 1 ' . $condition, $params);
		$refunddays = intval($_W['shopset']['trade']['refunddays']);

		if ($is_openmerch == 1) {
			$merch_user = $merch_plugin->getListUser($list, 'merch_user');
		}


		foreach ($list as &$row ) {
			$param = array();

			if ($row['isparent'] == 1) {
				$scondition = ' og.parentorderid=:parentorderid';
				$param[':parentorderid'] = $row['id'];
			}
			 else {
				$scondition = ' og.orderid=:orderid';
				$param[':orderid'] = $row['id'];
			}

			$sql = 'SELECT og.goodsid,og.total,g.title,g.thumb,og.price,og.optionname as optiontitle,og.optionid,op.specs,g.merchid,g.status FROM ' . tablename('ewei_shop_order_goods') . ' og ' . ' left join ' . tablename('ewei_shop_goods') . ' g on og.goodsid = g.id ' . ' left join ' . tablename('ewei_shop_goods_option') . ' op on og.optionid = op.id ' . ' where ' . $scondition . ' order by og.id asc';
			$goods = pdo_fetchall($sql, $param);
			$goods = set_medias($goods, array('thumb'));
			$ismerch = 0;
			$merch_array = array();
			$g = 0;
			$nog = 0;

			foreach ($goods as &$r ) {
				$merchid = $r['merchid'];
				$merch_array[$merchid] = $merchid;

				if (!(empty($r['specs']))) {
					$thumb = m('goods')->getSpecThumb($r['specs']);

					if (!(empty($thumb))) {
						$r['thumb'] = tomedia($thumb);
					}

				}


				if ($r['status'] == 2) {
					$row['gift'][$g] = $r;
					++$g;
				}
				 else {
					$row['nogift'][$nog] = $r;
					++$nog;
				}
			}

			unset($r);

			if (!(empty($merch_array))) {
				if (1 < count($merch_array)) {
					$ismerch = 1;
				}

			}


			if (empty($goods)) {
				$goods = array();
			}


			foreach ($goods as &$r ) {
				$r['thumb'] .= '?t=' . random(50);
			}

			unset($r);
			$goods_list = array();
			$i = 0;

			if ($ismerch) {
				$getListUser = $merch_plugin->getListUser($goods);
				$merch_user = $getListUser['merch_user'];

				foreach ($getListUser['merch'] as $k => $v ) {
					if (empty($merch_user[$k]['merchname'])) {
						$goods_list[$i]['shopname'] = $_W['shopset']['shop']['name'];
					}
					 else {
						$goods_list[$i]['shopname'] = $merch_user[$k]['merchname'];
					}

					$goods_list[$i]['goods'] = $v;
					++$i;
				}
			}
			 else {
				if ($merchid == 0) {
					$goods_list[$i]['shopname'] = $_W['shopset']['shop']['name'];
				}
				 else {
					$merch_data = $merch_plugin->getListUserOne($merchid);
					$goods_list[$i]['shopname'] = $merch_data['merchname'];
				}

				$goods_list[$i]['goods'] = $goods;
			}

			$row['goods'] = $goods_list;
			$statuscss = 'text-cancel';

			switch ($row['status']) {
			case '-1':
				$status = '已取消';
				break;

			case '0':
				if ($row['paytype'] == 3) {
					$status = '待发货';
				}
				 else {
					$status = '待付款';
				}

				$statuscss = 'text-cancel';
				break;

			case '1':
				if ($row['isverify'] == 1) {
					$status = '使用中';
				}
				 else if (empty($row['addressid'])) {
					$status = '待取货';
				}
				 else {
					$status = '待发货';
				}

				$statuscss = 'text-warning';
				break;

			case '2':
				$status = '待收货';
				$statuscss = 'text-danger';
				break;

			case '3':
				if (empty($row['iscomment'])) {
					if ($show_status == 5) {
						$status = '已完成';
					}
					 else {
						$status = ((empty($_W['shopset']['trade']['closecomment']) ? '待评价' : '已完成'));
					}
				}
				 else {
					$status = '交易完成';
				}

				$statuscss = 'text-success';
				break;
			}

			$row['statusstr'] = $status;
			$row['statuscss'] = $statuscss;

			if ((0 < $row['refundstate']) && !(empty($row['refundid']))) {
				$refund = pdo_fetch('select * from ' . tablename('ewei_shop_order_refund') . ' where id=:id and uniacid=:uniacid and orderid=:orderid limit 1', array(':id' => $row['refundid'], ':uniacid' => $uniacid, ':orderid' => $row['id']));

				if (!(empty($refund))) {
					$row['statusstr'] = '待' . $r_type[$refund['rtype']];
				}

			}


			$row['canverify'] = false;
			$canverify = false;

			$row['canverify'] = $canverify;

			if ($is_openmerch == 1) {
				$row['merchname'] = (($merch_user[$row['merchid']]['merchname'] ? $merch_user[$row['merchid']]['merchname'] : $_W['shopset']['shop']['name']));
			}
		}

		unset($row);	
		app_json(array('list' => $list, 'pagesize' => $psize, 'total' => $total, 'page' => $pindex));
	}
}

?>
