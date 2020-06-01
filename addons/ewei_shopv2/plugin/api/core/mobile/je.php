<?php
class Je_EweiShopV2Page extends Page
{
	public function search()
	{
		global $_W;
		global $_GPC;
		//验证请求的参数是否合法

		$appkey = 'kmd4c2cbb6e3cc5a737';
		$token = '7bf9d2f377b3a97b193fc3a9b0cc8a1b';
		$varsion = 'v1.0';
		$signs = md5($appkey.$token.$_REQUEST['timestamp'].$varsion);
		//验证失败时返回提示
		// if($signs != $_REQUEST['sign']){
		// 	$error = ['isSuccess' => false,'errorCode' => 50,'data' => 'Sign verification failed or empty'];
		// 	exit(json_encode($error));
		// }

		// $signs = md5($_REQUEST['appkey'].$_REQUEST['token'].$_REQUEST['timestamp'].$_REQUEST['varsion']);
		// //验证失败时返回提示
		// if($signs != $_REQUEST['sign']){
		// 	$error = ['isSuccess' => false,'errorCode' => 50,'data' => 'Sign verification failed or empty'];
		// 	exit(json_encode($error));
		// }

		// $signs = md5($_REQUEST['appkey'].$_REQUEST['token'].$_REQUEST['timestamp'].$_REQUEST['varsion']);
		// //验证失败时返回提示
		// if($signs != $_REQUEST['sign']){
		// 	$error = ['isSuccess' => false,'errorCode' => 50,'data' => 'Sign verification failed or empty'];
		// 	exit(json_encode($error));
		// }


		$condition = '';
		// file_put_contents('s100search.txt', var_export($_REQUEST,true).date('Ymd H:i:s')."\r\n",FILE_APPEND);
		// $content = $_GPC;
		$content = json_decode(file_get_contents('php://input'),true);
		if(empty($content)){
			$content = $_GPC;
		}
		file_put_contents('s100search.txt', var_export($content,true).date('Ymd H:i:s')."\r\n",FILE_APPEND);
		if(!empty($content['code'])){
			$condition .= ' and ordersn = "'.$content['code'].'"';
		}
		if(!empty($content['status'])){
			if($content['status'] == '0'){
				$status = '1';
			}
			if($content['status'] == '1'){
				$status = '2';
			}
			if($content['status'] == '2'){
				$status = '3';
			}
			if($content['status'] == '3'){
				$status = '-1';
			}
			$condition .= ' and status = "'.$status.'"';
		}
		if(!empty($content['startTime']) && !empty($content['endTime'])){
			$condition .= ' and (createtime between '.$content['startTime'].' and '.$content['endTime'].')';
		}
		if(!empty($content['startPayTime']) && !empty($content['endPayTime'])){
			$condition .= ' and (paytime between '.$content['startPayTime'].' and '.$content['endPayTime'].')';
		}
		$sqls = "select id from `ims_ewei_shop_order` where uniacid=".$_W['uniacid'].$condition;
		if(empty($content['pageSize'])){
			$content['pageSize'] = 10;
		}
		$ordercount = pdo_fetchall($sqls);
		// var_dump($ordercount);die;
		if(!empty($content['pageSize'])){
			$pageNo = $content['pageNo'];
			if(empty($pageNo)){
				$pageNo = 1;
			}
			$condition .= ' limit '.($pageNo-1)*$content['pageSize'].','.$content['pageSize'];
		}
		$sql = "select id,ordersn,createtime,paytime,paytype,price,status,address,remark,dispatchprice,refundid,discountprice,couponprice from `ims_ewei_shop_order` where uniacid=".$_W['uniacid'].$condition;

		$order = pdo_fetchall($sql);
		if(!empty($order)){
			$returnorder = array();
			foreach ($order as $key => $value) {
				$array['code'] = $value['ordersn'];
				$array['createTime'] = $value['createtime'];
				$array['payTime'] = $value['paytime'];
				$array['total'] = $value['price'];
				$array['remarks'] = $value['remarks']?$value['remarks']:'';
				$array['freight'] = $value['dispatchprice'];
				$array['discount'] = $value['discountprice']+$value['couponprice'];
				$array['receiptTitle'] = '';
				if($value['status'] == '0'){
					$array['status'] = '2';
				}
				if($value['status'] == '1'){
					$array['status'] = '4';
				}
				if($value['status'] == '2'){
					$array['status'] = '5';
				}
				if($value['status'] == '3'){
					$array['status'] = '6';
				}
				if($value['status'] == '-1'){
					$array['status'] = '0';
				}
				if(!empty($value['refundid'])){
					$refund = pdo_fetch('select status,refundtype from '. tablename('ewei_shop_order_refund') . 'where uniacid=:uniacid and orderid=:orderid and status=:status and refundtype=:refundtype',array(':uniacid' => $_W['uniacid'],':orderid' => $value['id'],':status' => '1',':refundtype'=>'0'));
					if(!empty($refund)){
						$array['status'] = '100';
					}
				}
				if($value['paytype'] == '1'){
					$array['payWay'] = '在线支付,余额支付';
				}
				if($value['paytype'] == '11'){
					$array['payWay'] = '后台操作,后台支付';
				}
				if($value['paytype'] == '21'){
					$array['payWay'] = '在线支付,微信支付';
				}
				//处理地址
				$address = unserialize($value['address']);
				$array['consigneeName'] = $address['realname'];
				$array['consigneeMobile'] = $address['mobile'];
				$array['postcode'] = '';
				$array['province'] = $address['province'];
				$array['city'] = $address['city'];
				$array['county'] = $address['area'];
				$array['realname'] = $address['realname'];
				$array['address'] = $address['address'];
				$array['source'] = '小程序';
				if($value['status'] == '0'){
					$array['type'] = '1';
				}
				if($value['status'] == '1'){
					$array['type'] = '1';
				}
				if($value['status'] == '2'){
					$array['type'] = '1';
				}
				if($value['status'] == '3'){
					$array['type'] = '1';
				}
				if($value['status'] == '-1'){
					$array['type'] = '2';
				}
				$returnorder[$key] = $array;
				$goods = pdo_fetchall('select o.goodsid,o.price,o.total,o.optionid,o.optionname,o.goodssn,g.title,g.goodssn,g.weight,g.unit from '. tablename('ewei_shop_order_goods') .'o left join'. tablename('ewei_shop_goods').' g on o.goodsid=g.id where o.uniacid=:uniacid and o.orderid=:orderid',array(':uniacid' => $_W['uniacid'],':orderid' => $value['id']));
				$item = array();
				foreach ($goods as $k => $v) {
					$detail['name'] = $v['title'];
					$detail['sku_code'] = $v['optionname'];
					$detail['sku_id'] = $v['optionid'];
					$detail['goods_code'] = $v['goodssn'];
					$detail['goods_id'] = $v['goodsid'];
					$detail['quantity'] = $v['total'];
					$detail['price'] = $v['price'];
					$detail['brand'] = '米思阳';
					$detail['weight'] = !empty($v['weight'])?$v['weight']:'';
					$detail['unit'] = $v['unit']?$v['unit']:'';
					$item[$k] = $detail;
				}
				if(count($item)=='1'){
					$returnorder[$key]['itemList'] = array($item['0']);
				} else {
					$returnorder[$key]['itemList'] = $item;
				}
				
			}
			if(!empty($returnorder)){
				$return['isSuccess'] = true;
				$return['errorCode'] = 0;
				$data['pageNo'] = intval($pageNo);
				$data['pageSize'] = intval($content['pageSize']);
				$data['totalCount'] = intval(count($ordercount));
				if(count($returnorder) == '1'){
					$data['list'] = array($returnorder['0']);
				} else {
					$data['list'] = $returnorder;
				}
				
				$return['data'] = $data;
				if($pageNo*$content['pageSize']>=count($ordercount) || empty($content['pageSize'])){
					$return['hasNextPage'] = false;
					$return['nextPage'] = '';
				}
				if($pageNo*$content['pageSize']<count($ordercount) && !empty($content['pageSize'])){
					$return['hasNextPage'] = true;
					$return['nextPage'] = intval($pageNo+1);
				}
				if(!empty($content['pageSize'])){
					$return['totalPageCount'] = intval(ceil(count($ordercount)/$content['pageSize']));
				} else {
					$return['totalPageCount'] = 1;
				}
				if($pageNo == 1){
					$return['hasPrePage'] = false;
					$return['prePage'] = '';
				}
				if($pageNo > 1){
					$return['hasPrePage'] = true;
					$return['prePage'] = intval($pageNo-1);
				}
				$return['start'] = intval(($pageNo-1)*$content['pageSize']+1);
				
			} else {
				$return['isSuccess'] = false;
				$return['errorCode'] = '';
			}
			exit(json_encode($return));
		} else {
			$error = ['isSuccess' => false,'errorCode' => 50,'data' => 'Cannot find content that matches the condition'];
			exit(json_encode($error));
		}
	}

	public function delivery()
	{
		global $_W;
		global $_GPC;

		// //验证请求的参数是否合法
		$appkey = 'kmd4c2cbb6e3cc5a737';
		$token = '7bf9d2f377b3a97b193fc3a9b0cc8a1b';
		$varsion = 'v1.0';
		$signs = md5($appkey.$token.$_REQUEST['timestamp'].$varsion);

		// //验证请求的参数是否合法
		// $signs = md5($_REQUEST['appkey'].$_REQUEST['token'].$_REQUEST['timestamp'].$_REQUEST['varsion']);

		// file_put_contents('s100search.txt', var_export($_REQUEST,true).date('Ymd H:i:s')."\r\n",FILE_APPEND);
		// // //验证请求的参数是否合法
		// $signs = md5($_REQUEST['appkey'].$_REQUEST['token'].$_REQUEST['timestamp'].$_REQUEST['varsion']);

		//验证失败时返回提示
		// if($signs != $_REQUEST['sign']){
		// 	$error = ['isSuccess' => false,'errorCode' => 50,'data' => 'Sign verification failed or empty'];
		// 	exit(json_encode($error));
		// }
		$content = json_decode(file_get_contents('php://input'),true);
		if(empty($content)){
			$content = $_GPC;
		}
		file_put_contents('s100delivery.txt', var_export($content,true).date('Ymd H:i:s')."\r\n",FILE_APPEND);

		if(!empty($content['orderCode'])){
			$order = pdo_fetch('select id,ordersn,price,status,paytype,paytime,sendtime from ' .tablename('ewei_shop_order') . 'where uniacid=:uniacid and ordersn=:ordersn ',array(':uniacid' => $_W['uniacid'],':ordersn' => $content['orderCode']));
			if(empty($order)){
				$error = ['isSuccess' => false,'errorCode' => 50,'data' => ['errorCode'=>50,'errorMsg'=>'根据提供的订单号没有找到订单']];
				exit(json_encode($error));
			}
			if($order['status'] == '0'){
				$error = ['isSuccess' => false,'errorCode' => 50,'data' => ['errorCode'=>50,'errorMsg'=>'订单尚未支付，不能发货']];
				exit(json_encode($error));
			}
			if($order['status'] >= '2' && !empty($order['sendtime'])){
				$error = ['isSuccess' => false,'errorCode' => 50,'data' => ['errorCode'=>50,'errorMsg'=>'订单已发货，不能再次发货']];
				exit(json_encode($error));
			}
			if(empty($content['packageNo'])){
				$error = ['isSuccess' => false,'errorCode' => 50,'data' => ['errorCode'=>50,'errorMsg'=>'运单号为空']];
				exit(json_encode($error));
			}
			$packageNo = explode(',',$content['packageNo']);
			if(count($packageNo) == '1'){
				$data['expresssn'] = $content['packageNo'];
				$data['sendtime'] = time();
				$express = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_express') . ' where express = :express',array(':express' => $content['companyCode']));
				if(empty($express)){
					$error = ['isSuccess' => false,'errorCode' => 50,'data' => ['errorCode'=>50,'errorMsg'=>'根据提供的物流公司编码没有找到对应的物流公司']];
					exit(json_encode($error));
				}
				$data['express'] = $content['companyCode'];
				$data['expresscom'] = $express['name'];
				$data['status'] = '2';
				$res = pdo_update('ewei_shop_order',$data,array('uniacid'=>$_W['uniacid'],'ordersn' => $content['orderCode']));
				if($res){
					$success = ['isSuccess' => true];
					exit(json_encode($success));
				} else {
					$error = ['isSuccess' => false,'errorCode' => 50,'data' => ['errorCode'=>50,'errorMsg'=>'发货失败']];
					exit(json_encode($error));
				}
			} else {
				$goods = pdo_fetchall('select id from '. tablename('ewei_shop_order_goods') . ' where uniacid=:uniacid and orderid=:orderid',array(':uniacid' => $_W['uniacid'],':orderid' =>$order['id']));
				$express = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_express') . ' where express = :express',array(':express' => $content['companyCode']));
				$i = '0';
				$j = '0';
				$time = time();
				foreach ($goods as $key => $value) {
					$item = array();
					if(!empty($packageNo[$i])){
						$data['expresssn'] = $packageNo[$i];
						$j = $i;
						$i += '1';
					} else {
						$data['expresssn'] = $packageNo[$j];
					}
					$data['sendtime'] = $time;
					$data['express'] = $content['companyCode'];
					$data['expresscom'] = $express['name'];
					$data['sendtype'] = $j+='1';
					pdo_update('ewei_shop_order_goods',$data,array('uniacid' => $_W['uniacid'],'id'=>$value['id']));
				}
				$res = pdo_update('ewei_shop_order',array('sendtime'=>$time,'sendtype'=>$j,'status' => '2'),array('uniacid' => $_W['uniacid'],'id'=>$order['id']));
				if($res){
					$success = ['isSuccess' => true];
					exit(json_encode($success));
				} else {
					$error = ['isSuccess' => false,'errorCode' => 50,'data' => ['errorCode'=>50,'errorMsg'=>'发货失败']];
					exit(json_encode($error));
				}
			}
		} else {
			$error = ['isSuccess' => false,'errorCode' => 50,'data' => ['errorCode'=>50,'errorMsg'=>'运单号为空']];
			exit(json_encode($error));
		}
	}

	public function refundorder()
	{
		global $_W;
		global $_GPC;

		$condition = '';
		$content = json_decode(file_get_contents('php://input'),true);
		if(empty($content)){
			$content = $_GPC;
		}
		// $content = json_decode('{"status": "", "cstime": "1531713000", "cetime": "1548777600" , "page": "3" , "page_size": "3"}',true);
		file_put_contents('s100refundorder.txt', var_export($content,true).date('Ymd H:i:s')."\r\n",FILE_APPEND);
		if(!empty($content['status'])){
			$condition .= ' and status = "'.$content['status'].'"';
		} else {
			$condition .= ' and status in (0,1) ';
		}
		if(!empty($content['cstime']) && !empty($content['cetime'])){
			$condition .= ' and (createtime between '.$content['cstime'].' and '.$content['cetime'].')';
		}
		$sqls = "select id,orderid,refundno,price,status,refundtype from `ims_ewei_shop_order_refund` where refundtype='0' and uniacid=".$_W['uniacid'].$condition;
		$totalrefund = pdo_fetchall($sqls);
		if(!empty($content['page_size'])){
			$pageSize = $content['page_size'];
			$pageNo = $content['page'];
			if(empty($pageNo)){
				$pageNo = 1;
			}
			$condition .= ' limit '.($pageNo-1)*$pageSize.','.$pageSize;
		} else {
			$pageSize = 50;
			$pageNo = $content['page'];
			if(empty($pageNo)){
				$pageNo = 1;
			}
			$condition .= ' limit '.($pageNo-1)*$pageSize.','.$pageSize;
		}

		$sql = "select id,orderid,refundno,price,status,refundtype,applyprice,createtime from `ims_ewei_shop_order_refund` where refundtype='0' and uniacid=".$_W['uniacid'].$condition;
		$result = pdo_fetchall($sql);
		if(!empty($result)){
			$data = array();
			foreach ($result as $key => $value) {
				$order = pdo_fetch('select id,ordersn,openid from '.tablename('ewei_shop_order') . ' where uniacid=:uniacid and id=:id',array(':uniacid' => $_W['uniacid'],':id' => $value['orderid']));
				$member = '';
				$member = pdo_fetch('select nickname from ' . tablename('ewei_shop_member') . ' where uniacid=:uniacid and openid=:openid limit 1 ',array(':uniacid'=>$_W['uniacid'],':openid'=>$order['openid']));
				$data['refund_id'] = $value['refundno'];
				$data['sales_no'] = $order['ordersn'];
				$data['refund_amout'] = $value['applyprice'];
				$data['createtime'] = $value['createtime'];
				$data['status'] = $value['status'];
				if(!empty($member)){
					$data['nickname'] = $member['nickname'];
				} else {
					$data['nickname'] = '';
				}
				$goods = pdo_fetchall('select o.goodsid,o.price,o.total,o.optionid,o.optionname,o.goodssn,o.total,g.title,g.goodssn,g.weight,g.unit from '. tablename('ewei_shop_order_goods') .'o left join'. tablename('ewei_shop_goods').' g on o.goodsid=g.id where o.uniacid=:uniacid and o.orderid=:orderid',array(':uniacid' => $_W['uniacid'],':orderid' => $value['orderid']));
				if(count($goods) == '1'){
					$data['name'] = $goods['0']['title'];
					$data['goods_code'] = $goods['0']['goodssn'];
					$data['goods_id'] = $goods['0']['goodsid'];
					$data['quantity'] = $goods['0']['total'];
					$data['sku_id'] = $goods['0']['optionid'];
					$data['sku_code'] = $goods['0']['optionname'];
				} else {
					for($s=0;$s<count($goods);$s++){
						$total += $goods[$s]['total'];
					}
					$data['name'] = $goods['0']['title'];
					$data['goods_code'] = $goods['0']['goodssn'];
					$data['goods_id'] = $goods['0']['goodsid'];
					$data['quantity'] = $total;
					$data['sku_id'] = $goods['0']['optionid'];
					$data['sku_code'] = $goods['0']['optionname'];
				}
				$list[$key] = $data;
			}
			
			$datas = array();
			$datas['isSuccess'] = true;
			$datas['errorCode'] = 0;
			$datas['pageNo'] = $pageNo;
			$datas['pageSize'] = $pageSize;
			$datas['totalCount'] = count($totalrefund);
			if(count($totalrefund)==1){
				$datas['list'] = array($list['0']);
			} else {
				$datas['list'] = $list;
			}
			if($pageNo*$pageSize>=count($totalrefund)){
				$datas['hasNextPage'] = false;
				$datas['nextPage'] = '';
			}
			if($pageNo*$pageSize<count($totalrefund)){
				$datas['hasNextPage'] = true;
				$datas['nextPage'] = intval($pageNo+1);
			}
			$datas['totalPageCount'] = intval(ceil(count($totalrefund)/$pageSize));
			if($pageNo == 1){
				$datas['hasPrePage'] = false;
				$datas['prePage'] = '';
			}
			if($pageNo > 1){
				$datas['hasPrePage'] = true;
				$datas['prePage'] = intval($pageNo-1);
			}
			$datas['start'] = intval(($pageNo-1)*$pageSize+1);
			// var_dump($datas);die;
			exit(json_encode($datas));
		} else {
			$error = ['isSuccess' => false,'errorCode' => 50,'data' => ['errorCode'=>50,'errorMsg'=>'根据提供的内容，没有找到符合条件的退款订单']];
			exit(json_encode($error));
		}
	}

	public function refunds()
	{
		global $_W;
		global $_GPC;

		$content = json_decode(file_get_contents('php://input'),true);
		if(empty($content)){
			$content = $_GPC;
		}
		file_put_contents('s100refunds.txt', var_export($content,true).date('Ymd H:i:s')."\r\n",FILE_APPEND);
		$refundno = $content['refundsn'];
		$refund = pdo_fetch('select * from ' . tablename('ewei_shop_order_refund') . ' where uniacid=:uniacid and refundno=:refundno limit 1', array(':uniacid' => $_W['uniacid'],':refundno' => $refundno));
		if(empty($refund)){
			$error = ['isSuccess'=>flase,'errorCode'=>50,'data'=>['errorCode'=>50,'errorMsg'=>'未找到退款订单']];
			exit(json_encode($error));
		}
		
		if($refund['status'] == '1'){
			$error = ['isSuccess'=>flase,'errorCode'=>50,'data'=>['errorCode'=>50,'errorMsg'=>'订单已退款，不需要重复操作']];
			exit(json_encode($error));
		}

		$item = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_order') . ' WHERE id = :id and uniacid=:uniacid Limit 1', array(':id' => $refund['orderid'], ':uniacid' => $_W['uniacid']));

		if($item['isCash']==1){
			$item['price']=$item['earnest'];
		}
		
		$refundstatus = intval($content['refundstatus']);
		$refundcontent = trim($content['refundcontent']);
		$time = time();
		$change_refund = array();
		$uniacid = $_W['uniacid'];

		if ($refundstatus == '1') {
			$order_price = $item['price'];
			$ordersn = $item['ordersn'];
			if (!empty($item['isprepay'])&&!empty($item['isprepaysuccess'])) {
				$ordersnwk =$ordersn."WK";
			}

			if (!empty($item['ordersn2'])) {
				$var = sprintf('%02d', $item['ordersn2']);
				$ordersn .= 'GJ' . $var;
			}

			$realprice = $refund['applyprice'];
			$goods = pdo_fetchall('SELECT g.id,g.credit, o.total,o.realprice,g.isfullback FROM ' . tablename('ewei_shop_order_goods') . ' o left join ' . tablename('ewei_shop_goods') . ' g on o.goodsid=g.id ' . ' WHERE o.orderid=:orderid and o.uniacid=:uniacid', array(':orderid' => $item['id'], ':uniacid' => $uniacid));
			$refundtype = 0;
			if (empty($item['transid']) && ($item['paytype'] == 22) && empty($item['apppay'])) {
				$item['paytype'] = 23;
			}

			$ispeerpay = m('order')->checkpeerpay($item['id']);

			if (!empty($ispeerpay)) {
				$item['paytype'] = 21;
			}


			if ($item['paytype'] == 1) {
				m('member')->setCredit($item['openid'], 'credit2', $realprice, array(0, $shopset['name'] . '退款: ' . $realprice . '元 订单号: ' . $item['ordersn']));
				$result = true;
			} else if ($item['paytype'] == 21) {
				if ($item['apppay'] == 2) {

					if ($item['isprepay']&&$item['isprepaysuccess']) {

						$order_price1=$realprice1=$item['price']-$item['balance'];
						$order_price2=$realprice2=$item['balance'];
						$refund['refundno2'] = m('common')->createNO('order_refund', 'refundno', 'SR');
						$result1 = m('finance')->wxapp_refund($item['openid'], $ordersn, $refund['refundno'], $order_price1 * 100, $realprice1 * 100, !empty($item['apppay']) ? true : false);
						$result2 = m('finance')->wxapp_refund($item['openid'], $ordersnwk, $refund['refundno2'], $order_price2 * 100, $realprice2 * 100, !empty($item['apppay']) ? true : false);
					}else{
						$result = m('finance')->wxapp_refund($item['openid'], $ordersn, $refund['refundno'], $order_price * 100, $realprice * 100, !empty($item['apppay']) ? true : false);
					}

					
				}
				else {
					$realprice = round($realprice - $item['deductcredit2'], 2);

					if (!empty($ispeerpay)) {
						$pid = $ispeerpay['id'];
						$peerpaysql = 'SELECT * FROM ' . tablename('ewei_shop_order_peerpay_payinfo') . ' WHERE pid = :pid';
						$peerpaylist = pdo_fetchall($peerpaysql, array(':pid' => $pid));

						if (empty($peerpaylist)) {
							$error = ['isSuccess'=>flase,'errorCode'=>50,"data"=>['errorCode'=>50,'errorMsg'=>'没有人帮他代付过,无需退款']];
							exit(json_encode($error));
						}

						foreach ($peerpaylist as $k => $v) {
							if (empty($v['tid'])) {
								m('member')->setCredit($v['openid'], 'credit2', $v['price'], array(0, $shopset['name'] . '退款: ' . $realprice . '元 代付订单号: ' . $item['ordersn']));
								$result = true;
								continue;
							}

							$result = m('finance')->refund($v['openid'], $v['tid'], $refund['refundno'] . $v['id'], $v['price'] * 100, $v['price'] * 100, !empty($item['apppay']) ? true : false);
						}
					}
					else {
						if (0 < $realprice) {
							if (empty($item['isborrow'])) {
								$result = m('finance')->refund($item['openid'], $ordersn, $refund['refundno'], $order_price * 100, $realprice * 100, !empty($item['apppay']) ? true : false);
							}
							else {
								$result = m('finance')->refundBorrow($item['borrowopenid'], $ordersn, $refund['refundno'], $order_price * 100, $realprice * 100, !empty($item['ordersn2']) ? 1 : 0);
							}
						}
					}
				}

				$refundtype = 2;
			}
			if (is_error($result)) {
				$error = ['isSuccess'=>flase,'errorCode'=>50,"data"=>['errorCode'=>50,'errorMsg'=>$result['message']]];
				exit(json_encode($error));
			}

			if (0 < $goods['isfullback']) {
				m('order')->fullbackstop($item['id']);
			}

			$credits = m('order')->getGoodsCredit($goods);

			if (0 < $credits) {
			}

			if (0 < $item['deductcredit']) {
				m('member')->setCredit($item['openid'], 'credit1', $item['deductcredit'], array('0', $shopset['name'] . '购物返还抵扣'.$_W['shopset']['trade']['credittext'].' '.$_W['shopset']['trade']['credittext'].': ' . $item['deductcredit'] . ' 抵扣金额: ' . $item['deductprice'] . ' 订单号: ' . $item['ordersn']));
			}

			if (!empty($refundtype)) {
				if ($realprice < 0) {
					$item['deductcredit2'] = $refund['applyprice'];
				}

				m('order')->setDeductCredit2($item);
			}

			$change_refund['reply'] = '';
			$change_refund['status'] = 1;
			$change_refund['refundtype'] = $refundtype;
			$change_refund['price'] = $realprice;
			$change_refund['refundtime'] = $time;

			if (empty($refund['operatetime'])) {
				$change_refund['operatetime'] = $time;
			}

			pdo_update('ewei_shop_order_refund', $change_refund, array('id' => $item['refundid']));
			m('order')->setGiveBalance($item['id'], 2);
			m('order')->setStocksAndCredits($item['id'], 2);

			if ($refund['orderprice'] == $refund['applyprice']) {
				if (com('coupon') && !empty($item['couponid'])) {
					com('coupon')->returnConsumeCoupon($item['id']);
				}
			}

			pdo_update('ewei_shop_order', array('refundstate' => 0, 'status' => -1, 'refundtime' => $time), array('id' => $item['id'], 'uniacid' => $uniacid));

			foreach ($goods as $g) {
				$salesreal = pdo_fetchcolumn('select ifnull(sum(total),0) from ' . tablename('ewei_shop_order_goods') . ' og ' . ' left join ' . tablename('ewei_shop_order') . ' o on o.id = og.orderid ' . ' where og.goodsid=:goodsid and o.status>=1 and o.uniacid=:uniacid limit 1', array(':goodsid' => $g['id'], ':uniacid' => $uniacid));
				pdo_update('ewei_shop_goods', array('salesreal' => $salesreal), array('id' => $g['id']));
			}

			$log = '订单退款 ID: ' . $item['id'] . ' 订单号: ' . $item['ordersn'] . '操作来源：巨益接口操作';

			plog('order.op.refund.submit', $log);
			m('notice')->sendOrderMessage($item['id'], true);
			$success = ['isSuccess'=>true];
			exit(json_encode($success));
		} else {
			pdo_update('ewei_shop_order_refund', array('reply' => $refundcontent, 'status' => -1, 'endtime' => $time), array('id' => $item['refundid']));
			plog('order.op.refund.submit', '订单退款拒绝 ID: ' . $item['id'] . ' 订单号: ' . $item['ordersn'] . ' 原因: ' . $refundcontent);
			pdo_update('ewei_shop_order', array('refundstate' => 0), array('id' => $item['id'], 'uniacid' => $uniacid));
			m('notice')->sendOrderMessage($item['id'], true);
			$success = ['isSuccess'=>true];
			exit(json_encode($success));
		}
	}

}