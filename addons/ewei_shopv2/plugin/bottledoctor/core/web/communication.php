<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Communication_EweiShopV2Page extends PluginWebPage
{
	public function main()
	{
		global $_W;
		global $_GPC;
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		// var_dump($_GPC);die;
		$condition = 'uniacid=:uniacid';
		$params = array(':uniacid' => $_W['uniacid']);

		if ($_GPC['enabled'] != '') {
			$condition .= ' and enabled = :enabled';
			$params[':enabled'] = $_GPC['enabled'];
		}
		
		if ($_GPC['communicationtype'] != '') {
			$condition .= ' and communicationtype = :communicationtype';
			$params[':communicationtype'] = $_GPC['communicationtype'];
		}

		if ($_GPC['category'] != '') {
			$condition .= ' and categoryid = :categoryid';
			$params[':categoryid'] = $_GPC['category'];
		}

		$list = pdo_fetchall('SELECT * FROM ' . tablename('ewei_shop_bottledoctor_communion') . ' WHERE ' . $condition .' ORDER BY id desc LIMIT ' . (($pindex - 1) * $psize) . ',' . $psize, $params);	

		foreach ($list as &$v) {
			$v['images'] = unserialize($v['images']);
		}

		unset($v);
		$total = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_bottledoctor_communion') . ' where ' . $condition, $params);
		
		$categorys = pdo_fetchall('SELECT * FROM ' . tablename('ewei_shop_bottledoctor_category') .' WHERE uniacid=:uniacid ', array(':uniacid' => $_W['uniacid']));
				
		$pager = pagination2($total, $pindex, $psize);

		include $this->template();
	}

	public function edit()
	{
		$this->post();
	}

	protected function post()
	{
		global $_W;
		global $_GPC;
		// $psql = "select u.*,m.openid from ".tablename('ewei_shop_perm_user').' u left join '.tablename('ewei_shop_member').' m on u.openid=m.openid where u.uid=:uid and u.uniacid=:uniacid and u.username=:username';
		// $merchUser = pdo_fetch($psql,array(':uid'=>$_W['uid'],':uniacid'=>$_W['uniacid'],':username'=>$_W['username']));
		$merchUser = pdo_fetch("SELECT * FROM".tablename("ewei_shop_perm_user")."WHERE uniacid=:uniacid and uid=:uid",array(':uid' => $_W['uid'],'uniacid' => $_W['uniacid']));
		$id = intval($_GPC['id']);
		if ($_W['ispost']) {
			$data = array();
			if (!empty($id)) {
				$data['descripTion'] = $_GPC['descripTion'];
				$data['enabled'] = intval($_GPC['enabled']);
				$data['uniacid'] =  $_W['uniacid'];
				$data['name'] = trim($_GPC['name']);
				pdo_update('ewei_shop_bottledoctor_communion', $data, array('id' => $id));
				plog('bottledoctor.communication.edit', '修改分类 ID: ' . $id);
			}

			if (!empty($_GPC['answer'])) {

				$answerdata = array();
				
				$sql = "select categoryid from ".tablename('ewei_shop_bottledoctor_communion').' where  id=:id and uniacid=:uniacid';
				$list = pdo_fetch($sql,array(':id'=>$id,':uniacid'=>$_W['uniacid']));

				$answerdata['answer']=$_GPC['answer'];
				$answerdata['openid'] = $merchUser['openid'];
				$answerdata['uniacid'] = $_W['uniacid'];
				$answerdata['docname'] = $merchUser['username'];
				$answerdata['createTime'] = time();
				$answerdata['enabled']=1;
				$answerdata['communicationtype']=1;
				$answerdata['categoryid'] = $list['categoryid'];
				$answerdata['communicationid'] = $id;
				pdo_insert('ewei_shop_bottledoctor_answer', $answerdata);
				$id = pdo_insertid();
			}

			show_json(1, array('url' => webUrl('bottledoctor/communication', array('op' => 'display'))));
		}

		$answeritem = pdo_fetch('select * from ' . tablename('ewei_shop_bottledoctor_answer') . ' where communicationid=:id and uniacid=:uniacid and openid=:openid order by createTime desc limit 1', array(':id' => $id, ':uniacid' => $_W['uniacid'],':openid'=>$merchUser['openid']));

		$item = pdo_fetch('select * from ' . tablename('ewei_shop_bottledoctor_communion') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $id, ':uniacid' => $_W['uniacid']));	
		$item['images'] = unserialize($item['images']);
		$item['answer'] = $answeritem['answer'];

		include $this->template();
	}

	public function delete()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		$item = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_bottledoctor_communion') . ' WHERE id = \'' . $id . '\' AND uniacid=' . $_W['uniacid'] . '');
		
		if (empty($item)) {
			message('抱歉，消息不存在或是已经被删除！', webUrl('bottledoctor/communication', array('op' => 'display')), 'error');
		}
		pdo_delete('ewei_shop_bottledoctor_communion',array('id' => $id));
		pdo_delete('ewei_shop_bottledoctor_answer',array('communicationid' => $id));
		show_json(1);
	}
	
}

?>
