<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Detp_EweiShopV2Page extends WebPage
{
	//部门列表
	public function main()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		$pindex = max(1, intval($_GPC['page']));
		$psize = 15;

		$list = pdo_fetchall('SELECT * FROM ' . tablename('ewei_shop_detp') . ' where branch_id = '.$id.' ORDER BY status DESC limit ' . (($pindex - 1) * $psize) . ',' . $psize);
		$total = pdo_fetchcolumn('SELECT count(*) FROM ' . tablename('ewei_shop_detp'));
		$pager = pagination2($total, $pindex, $psize);
		include $this->template();
	}

	//添加部门
	public function add()
	{
		global $_W;
		global $_GPC;

		if ($_W['ispost']) {

			if (empty($_GPC['detp_name'])) {
				show_json(0, array('message' => '请输入部门名称'));
			}
			// 获取部门名称
			$detp_name = $_GPC['detp_name'];
			// 获取分公司ID
			$id = $_GPC['id'];
			
			
			//插入数据
			$res = pdo_insert('ewei_shop_detp', array('detp_name' => $detp_name,'branch_id' => $id));
			if($res){
				show_json(1,'添加部门成功');
			}else{
				show_json(0, array('message' => '添加失败'));
			}

		}
		$this->post();

	}

	//修改
	public function edit()
	{

		$this->post();
	}
	//修改状态
	public function status()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		$status = intval($_GPC['status']);

		$res = pdo_update('ewei_shop_detp',array('status'=>$status),array('detp_id' => $id));
		show_json(1, array('url' => referer()));
	}

	protected function post()
	{

		

		// dump("断点22");die;
		include $this->template();
	}




}