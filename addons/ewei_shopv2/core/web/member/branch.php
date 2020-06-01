<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Branch_EweiShopV2Page extends WebPage
{
	//fen列表
	public function main()
	{
		global $_W;
		global $_GPC;
		$pindex = max(1, intval($_GPC['page']));
		$psize = 15;

		$list = pdo_fetchall('SELECT * FROM ' . tablename('ewei_shop_branch') . ' ORDER BY status DESC limit ' . (($pindex - 1) * $psize) . ',' . $psize);

		$total = pdo_fetchcolumn('SELECT count(*) FROM ' . tablename('ewei_shop_branch'));
		$pager = pagination2($total, $pindex, $psize);
		include $this->template();
	}

	//添加分公司
	public function add()
	{
		global $_W;
		global $_GPC;

		if ($_W['ispost']) {

			if (empty($_GPC['branch_name'])) {
				show_json(0, array('message' => '请输入分公司名称'));
			}
			// 获取公司名称
			$branch_name = $_GPC['branch_name'];
			
			
			//插入数据
			$res = pdo_insert('ewei_shop_branch', array('branch_name' => $branch_name));
			if($res){
				show_json(1,'添加成功');
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

		$res = pdo_update('ewei_shop_branch',array('status'=>$status),array('branch_id' => $id));
		show_json(1, array('url' => referer()));
	}

	protected function post()
	{

		

		// dump("断点22");die;
		include $this->template();
	}




}