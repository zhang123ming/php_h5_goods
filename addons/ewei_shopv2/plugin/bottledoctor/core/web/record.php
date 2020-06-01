<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Record_EweiShopV2Page extends PluginWebPage
{
	public function main()
	{
		global $_W;
		global $_GPC;
		$select_category = (empty($_GPC['category']) ? '' : ' and a.article_category=' . intval($_GPC['category']) . ' ');
		$select_title = (empty($_GPC['keyword']) ? '' : ' and a.article_title LIKE \'%' . $_GPC['keyword'] . '%\' ');
		$page = (empty($_GPC['page']) ? '' : $_GPC['page']);
		$pindex = max(1, intval($page));
		$psize = 20;
		$articles = array();
		$articles = pdo_fetchall('SELECT a.*,c.category_name FROM ' . tablename('ewei_shop_bottledoctorarticle') . ' a left join ' . tablename('ewei_shop_bottledoctorarticle_category') . ' c on c.id=a.article_category  WHERE a.uniacid= :uniacid ' . $select_title . $select_category . ' order by article_date desc LIMIT ' . (($pindex - 1) * $psize) . ',' . $psize, array(':uniacid' => $_W['uniacid']));
		$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('ewei_shop_bottledoctorarticle') . ' a left join ' . tablename('ewei_shop_bottledoctorarticle_category') . ' c on c.id=a.article_category  WHERE a.uniacid= :uniacid ' . $select_title . $select_category, array(':uniacid' => $_W['uniacid']));
		$pager = pagination2($total, $pindex, $psize);

		if (!empty($articles)) {
			foreach ($articles as $key => &$value) {
				$url = mobileUrl('bottledoctor/record', array('aid' => $value['id']), true);
				$value['qrcode'] = m('qrcode')->createQrcode($url);
			}
		}

		$articlenum = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('ewei_shop_bottledoctorarticle') . ' WHERE uniacid= :uniacid ', array(':uniacid' => $_W['uniacid']));
		$categorys = pdo_fetchall('SELECT * FROM ' . tablename('ewei_shop_bottledoctorarticle_category') . ' WHERE uniacid=:uniacid ', array(':uniacid' => $_W['uniacid']));
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

	protected function post()
	{
		global $_W;
		global $_GPC;
		$aid = intval($_GPC['aid']);

		if ($_W['ispost']) {
			$article = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_bottledoctorarticle') . ' WHERE id=:aid and uniacid=:uniacid limit 1 ', array(':aid' => $aid, ':uniacid' => $_W['uniacid']));
			$data = array('article_title' => trim($_GPC['article_title']), 'article_content' => m('common')->html_images($_GPC['editor'], true), 'article_category' => intval($_GPC['article_category']), 'article_date_v' => trim($_GPC['article_date_v']), 'article_mp' => trim($_GPC['article_mp']), 'article_author' => trim($_GPC['article_author']), 'article_readnum_v' => intval($_GPC['article_readnum_v']), 'article_state' => intval($_GPC['article_state']), 'uniacid' => $_W['uniacid']);
			$advs = array();

			if (is_array($_GPC['adv_img'])) {
				foreach ($_GPC['adv_img'] as $key => $img) {
					if (empty($img)) {
						continue;
					}

					$advs[] = array('img' => trim($img), 'link' => $_GPC['adv_link'][$key]);
				}
			}

			

			if (empty($aid)) {
				$data['article_date'] = date('Y-m-d H:i:s');
				pdo_insert('ewei_shop_bottledoctorarticle', $data);
				$aid = pdo_insertid();
				plog('bottledoctor.record.add', '添加文章 ID: ' . $aid . ' 标题: ' . $data['article_title']);
			}
			else {
				pdo_update('ewei_shop_bottledoctorarticle', $data, array('id' => $aid));
				plog('bottledoctor.record.edit', '编辑文章 ID: ' . $aid . ' 标题: ' . $article['article_title']);
			}

			

			show_json(1, array('url' => webUrl('bottledoctor/record/edit', array('aid' => $aid, 'tab' => str_replace('#tab_', '', $_GPC['tab'])))));
		}

		$categorys = pdo_fetchall('SELECT * FROM ' . tablename('ewei_shop_bottledoctorarticle_category') . ' WHERE uniacid=:uniacid ', array(':uniacid' => $_W['uniacid']));
		$article = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_bottledoctorarticle') . ' WHERE id=:aid and uniacid=:uniacid limit 1 ', array(':aid' => $aid, ':uniacid' => $_W['uniacid']));

		if (!empty($article)) {
			
		
			$article['article_rule_moneylast'] = 0;
			$article['article_rule_creditreallast'] = 0;
			$article['article_rule_moneyreallast'] = 0;
		
		}

		$mp = pdo_fetch('SELECT acid,uniacid,name FROM ' . tablename('account_wechats') . ' WHERE uniacid=:uniacid ', array(':uniacid' => $_W['uniacid']));
		$article_sys = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_bottledoctorarticle_sys') . ' WHERE uniacid=:uniacid limit 1 ', array(':uniacid' => $_W['uniacid']));
		$levels = array();
		$levels['member'] = m('member')->getLevels(false);
		array_unshift($levels['member'], array('id' => 'default', 'levelname' => '默认等级'));

		if (p('commission')) {
			$levels['commission'] = p('commission')->getLevels(true, true);
		}

		include $this->template();
	}

	public function delete()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);

		if (empty($id)) {
			$id = (is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0);
		}

		$items = pdo_fetchall('SELECT id,article_title FROM ' . tablename('ewei_shop_bottledoctorarticle') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);

		foreach ($items as $item) {
			pdo_delete('ewei_shop_bottledoctorarticle', array('id' => $item['id']));
			$keyword = pdo_fetch('SELECT * FROM ' . tablename('rule_keyword') . ' WHERE content=:content and module=:module and uniacid=:uniacid limit 1 ', array(':module' => 'ewei_shopv2', ':uniacid' => $_W['uniacid']));

			if (!empty($keyword)) {
				pdo_delete('rule_keyword', array('id' => $keyword['id']));
				pdo_delete('rule', array('id' => $keyword['rid']));
			}

			pdo_delete('ewei_shop_bottledoctorarticle_log', array('aid' => $item['id']));
			pdo_delete('ewei_shop_bottledoctorarticle_share', array('aid' => $item['id']));
			plog('bottledoctor.record.delete', '删除文章 ID: ' . $item['id'] . ' 标题: ' . $item['article_title'] . ' ');
		}

		show_json(1, array('url' => referer()));
	}

	
	public function state()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);

		if (empty($id)) {
			$id = (is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0);
		}

		$items = pdo_fetchall('SELECT id,article_title FROM ' . tablename('ewei_shop_bottledoctorarticle') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);

		foreach ($items as $item) {
			pdo_update('ewei_shop_bottledoctorarticle', array('article_state' => intval($_GPC['state'])), array('id' => $item['id']));
			plog('bottledoctor.record.edit', ('修改文章状态<br/>ID: ' . $item['id'] . '<br/>标题: ' . $item['article_title'] . '<br/>状态: ' . $_GPC['state']) == 1 ? '开启' : '关闭');
		}

		show_json(1, array('url' => referer()));
	}

	protected function delKey($keyword)
	{
		global $_W;

		if (empty($keyword)) {
			return NULL;
		}

		$keyword = pdo_fetch('SELECT * FROM ' . tablename('rule_keyword') . ' WHERE content=:content and `module`=:module and uniacid=:uniacid limit 1 ', array(':content' => $keyword, ':module' => 'ewei_shopv2', ':uniacid' => $_W['uniacid']));

		if (!empty($keyword)) {
			pdo_delete('rule_keyword', array('id' => $keyword['id']));
			pdo_delete('rule', array('id' => $keyword['rid']));
		}
	}
}

?>
