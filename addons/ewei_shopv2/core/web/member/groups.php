<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Groups_EweiShopV2Page extends WebPage
{
	//部门列表
	public function main()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		$pindex = max(1, intval($_GPC['page']));
		$psize = 15;

		$list = pdo_fetchall('SELECT * FROM ' . tablename('ewei_shop_group') . ' where detp_id = '.$id.' ORDER BY status DESC limit ' . (($pindex - 1) * $psize) . ',' . $psize);
		$total = pdo_fetchcolumn('SELECT count(*) FROM ' . tablename('ewei_shop_group'));
		$pager = pagination2($total, $pindex, $psize);
		include $this->template();
	}

	//添加部门
	public function add()
	{
		global $_W;
		global $_GPC;

		if ($_W['ispost']) {

			if (empty($_GPC['group_name'])) {
				show_json(0, array('message' => '请输入组名称'));
			}
			// 获取部门名称
			$group_name = $_GPC['group_name'];
			// 获取分公司ID
			$id = $_GPC['id'];
			
			
			//插入数据
			$res = pdo_insert('ewei_shop_group', array('group_name' => $group_name,'detp_id' => $id));
			if($res){
				show_json(1,'添加组成功');
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

		$res = pdo_update('ewei_shop_group',array('status'=>$status),array('group_id' => $id));
		show_json(1, array('url' => referer()));
	}

	protected function post()
	{

		

		// dump("断点22");die;
		include $this->template();
	}




}