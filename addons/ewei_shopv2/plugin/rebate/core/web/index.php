<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Index_EweiShopV2Page extends PluginWebPage
{
	public function main()
	{
		global $_W;
		global $_GPC;

		$sql="SELECT * FROM ".tablename('ewei_shop_rebate')." WHERE uniacid=:uniacid";
		$params  = array(':uniacid' => $_W['uniacid']);

		$list = pdo_fetchall($sql, $params);

		foreach ($list as $key => &$value) {

			$sql="SELECT title,thumb FROM ".tablename("ewei_shop_goods")." WHERE uniacid=:uniacid AND id in ($value[goodsid]) ";
			$params=array(':uniacid' => $_W['uniacid']);

			$value[goodinfo]=pdo_fetchall($sql, $params);

		}
		include $this->template();

	}


	public function detail()
	{
		global $_W;
		global $_GPC;

		$id = $_GPC['id'];


		$sql="SELECT * FROM ".tablename('ewei_shop_rebate')." WHERE uniacid=:uniacid and rebate_id=:id";
		$params  = array(':uniacid' => $_W['uniacid'],':id'=>$id);
		
		$item = pdo_fetch($sql,$params);

		$commissionData=unserialize($item[data]);

		$goodsArray = $item['goodsid'];

		$mLevels = m('member')->getLevels();	

		
		if($item['type']==1){

			$goods = pdo_fetchall('SELECT id,title,thumb FROM ' . tablename('ewei_shop_goods') . " WHERE uniacid = :uniacid and id in ({$goodsArray})", array(':uniacid' => $_W['uniacid']));

		}

		if ($item) {
			$reward = unserialize($item['rebate_data']);
		}
		

		if ($_W['ispost']) {
			

			if ($_GPC['type']==1&&empty($_GPC['goodsid'])) {
				$this->message("请选择商品");
			}


			foreach ($_GPC['goodsid'] as $goodsid) {
				$sql="SELECT * FROM ".tablename('ewei_shop_rebate')." WHERE uniacid='{$_W[uniacid]}' AND FIND_IN_SET($goodsid,goodsid)";
				$item =pdo_fetch($sql);
				if (!empty($item)&&$item['rebate_id']!=$id) {
					show_json(0, '商品id为'.$goodsid.'已有返利方案，不可重复加入，请检查商品');
				}
			}


			$goodstr = implode(',', $_GPC['goodsid']);
			$data=array(
				'status'=>$_GPC['status'],
				'type' =>$_GPC['type'],
				'pay_money'=>$_GPC['pay_money'],
				'goodsid'=>$goodstr,
				'uniacid'=>$_W['uniacid'],
				'data'=>serialize($_GPC['data'])
				 );
			$reward = array();
			$rec_reward = htmlspecialchars_decode($_GPC['reward_data']);
			$rec_reward = json_decode($rec_reward, 1);
			$rec_data = array();

			if (!empty($rec_reward)) {
				foreach ($rec_reward as $val) {
					$rank = intval($val['rank']);
					if ($val['type'] == 1) {
						$rec_data[$rank]['credit'] = intval($val['num']);
					}else if ($val['type'] == 2) {
						$rec_data[$rank]['integral'] = intval($val['num']);
					}
					else if ($val['type'] == 4) {
						$goods_id = intval($val['goods_id']);
						$goods_name = trim($val['goods_name']);
						$goods_img = trim($val['img']);
						$goods_price = floatval($val['goods_price']);
						$goods_total = intval($val['goods_total']);
						$goods_totalcount = intval($val['goods_totalcount']);
						$goods_spec = intval($val['goods_spec']);
						$goods_specname = trim($val['goods_specname']);

						if (isset($rec_data[$rank]['goods'][$goods_id]['spec'])) {
							$oldspec = $rec_data[$rank]['goods'][$goods_id]['spec'];
						}
						else {
							$oldspec = array();
						}

						$rec_data[$rank]['goods'][$goods_id] = array('id' => $goods_id, 'img' => $goods_img, 'title' => $goods_name, 'marketprice' => $goods_price, 'total' => $goods_total, 'count' => $goods_totalcount, 'spec' => $oldspec);

						if (0 < $goods_spec) {
							$rec_data[$rank]['goods'][$goods_id]['spec'][$goods_spec] = array('goods_spec' => $goods_spec, 'goods_specname' => $goods_specname, 'marketprice' => $goods_price, 'total' => $goods_total);
						}
						else {
							$rec_data[$rank]['goods'][$goods_id]['spec'] = '';
						}
					}
					
				}
			}

			$data['rebate_data'] = serialize($rec_data);


			
			if ($id) {
				$res = pdo_update('ewei_shop_rebate', $data, array('rebate_id' => $id, 'uniacid' => $_W['uniacid']));
				if ($res) {
					plog('rebate.edit', '修改返利奖励活动 ID: ' . $item['rebate_id'] . '<br>');
				}
				else {
					show_json(0, '更新操作失败');
				}

				show_json(1, array('url' => webUrl('rebate')));
			}
			else {

				$data['addtime']=time();

				$res = pdo_insert('ewei_shop_rebate', $data);
				$id = pdo_insertid();

				if ($res) {
					plog('rebate.edit', '添加返利奖励活动 ID: ' . $id . '<br>');
				}
				else {
					show_json(0, '添加操作失败');
				}

				show_json(1, array('url' => webUrl('rebate', array('id' => $id))));
			}
		}

		include $this->template();
	}


	public function delete()
	{
		global $_W;
		global $_GPC;

		$id=intval($_GPC['id']);

		if (empty($id)) {
		 $id = !empty($_GPC['ids'])?implode(',', $_GPC['ids']):0;
		}


		$items = pdo_fetchall("SELECT rebate_id FROM ".tablename("ewei_shop_rebate")." WHERE uniacid=$_W[uniacid] AND rebate_id in ($id)");

		foreach ($items as $value) {
			pdo_delete('ewei_shop_rebate', array('rebate_id' => $value['rebate_id']));
			plog('rebate.delete', '彻底删除奖励方案<br/>ID: ' . $value['rebate_id']);
		}

		show_json(1, array('url' => referer()));

	}

}

?>
