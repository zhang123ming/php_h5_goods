<?php
class Express_EweiShopV2Model
{
	/**
     * 获取快递列表
     */
	public function getExpressList($type=0)
	{
		global $_W;
		$sql = 'select * from ' . tablename('ewei_shop_express') . ' where status=1 order by displayorder desc,id asc';
		$data = pdo_fetchall($sql);
		if($type){
			$list=array();
			foreach($data as $k=>$v){
				if($type==2){
					$list[$v['name']]=$v;
				}else{
					$list[$v['express']]=$v;
				}
			}
			return $list;
		}else{
			return $data;
		}
	}
}

if (!defined('IN_IA')) {
	exit('Access Denied');
}

?>
