<?php
if (!(defined('IN_IA'))) {
	exit('Access Denied');
}

class Shoptips_EweiShopV2Page extends PluginWebPage
{
	public function main()
	{
		global $_W;
		$list = pdo_fetchall('SELECT * FROM ' . tablename('ewei_shop_shoptips') . ' WHERE uniacid = \'' . $_W['uniacid'] . '\' ORDER BY displayorder DESC');

		include $this->template();
	}

	public function add()
	{
		$this->post();
	}

	public function edit()
	{
		$this->post();
	}

	public function post()
	{
		global $_W;
		global $_GPC;

		$id = intval($_GPC['id']);
		if(!empty($id)){
			$shoptips = pdo_fetch('select * from ' . tablename('ewei_shop_shoptips') . ' where id=:id ',array(':id'=>$id));
			if(!empty($shoptips)){
				$showtype = explode(',',$shoptips['showtype']);
			}
		}

		if ($_W['ispost']) {
			if(empty($_GPC['title'])){
				show_json(0,'标题不能为空！');
			}
			if(empty($_GPC['title'])){
				show_json(0,'内容不能为空！');
			}
			if(empty($_GPC['title'])){
				show_json(0,'显示位置不能为空！');
			}
			
			if(!empty($id)){
				$shoptips['displayorder'] = $_GPC['displayorder'];
				$shoptips['title'] = $_GPC['title'];
				$shoptips['rtitle'] = $_GPC['rtitle'];
				$shoptips['thumb'] = $_GPC['thumb'];
				$shoptips['content'] = $_GPC['content'];
				$shoptips['showtype'] = $_GPC['showtype'];
				$shoptips['status'] = $_GPC['status'];
				$shoptips['istop'] = $_GPC['istop'];
				pdo_update('ewei_shop_shoptips',$shoptips,array('uniacid'=>$_W['uniacid'],'id'=>$id));
				show_json(1);
			} else {
				$shoptips['displayorder'] = $_GPC['displayorder'];
				$shoptips['title'] = $_GPC['title'];
				$shoptips['rtitle'] = $_GPC['rtitle'];
				$shoptips['thumb'] = $_GPC['thumb'];
				$shoptips['content'] = $_GPC['content'];
				$shoptips['showtype'] = $_GPC['showtype'];
				$shoptips['createtime'] = time();
				$shoptips['uniacid'] = $_W['uniacid'];
				$shoptips['status'] = $_GPC['status'];
				$shoptips['istop'] = $_GPC['istop'];
				pdo_insert('ewei_shop_shoptips',$shoptips);
				$id = pdo_insertid();
				if(!empty($id)){
					show_json(1,array('url' => webUrl('app/shoptips')));
				} else {
					show_json(0);
				}
			}
		}

		include $this->template();
	}

	public function enabled()
	{
		global $_W;
		global $_GPC;

		$id = intval($_GPC['id']);
		$status['status'] = $_GPC['status'];
		pdo_update('ewei_shop_shoptips',$status,array('uniacid'=>$_W['uniacid'],'id'=>$id));
		
		show_json(1, array('url' => referer()));
	}

	public function delete()
	{
		global $_W;
		global $_GPC;

		$id = intval($_GPC['id']);
		$item = pdo_fetch('select * from ' . tablename('ewei_shop_shoptips') . ' where id=:id ',array(':id'=>$id));
		if(empty($item)){
			show_json(0,'抱歉，公告不存在或已被删除！');
		}
		pdo_delete('ewei_shop_shoptips',array('uniacid'=>$_W['uniacid'],'id'=>$id));
		show_json(1);
	}

	public function displayorder()
	{
		global $_W;
		global $_GPC;

		$id = intval($_GPC['id']);
		$val['displayorder'] = $_GPC['value'];
		pdo_update('ewei_shop_shoptips',$val,array('uniacid'=>$_W['uniacid'],'id'=>$id));
		show_json(1, array('url' => referer()));
	}

}
// style="padding-left:10rpx;color:#8C8C8C;overflow:hidden;view-overflow:ellipsis;white-space:nowrap;"

?>