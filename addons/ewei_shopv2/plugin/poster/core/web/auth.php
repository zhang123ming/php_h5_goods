<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}

class Auth_EweiShopV2Page extends PluginWebPage
{
    public function main()
    {
        global $_W;
        global $_GPC;
        $pindex    = max(1, intval($_GPC['page']));
        $psize     = 10;
        $params    = array(':uniacid' => $_W['uniacid']);
        $condition = ' and uniacid=:uniacid ';

        if (!empty($_GPC['keyword'])) {
            $_GPC['keyword'] = trim($_GPC['keyword']);
            $condition .= ' AND `title` LIKE :title';
            $params[':title'] = '%' . trim($_GPC['keyword']) . '%';
        }

        $list = pdo_fetchall('SELECT * FROM ' . tablename('ewei_shop_auth') . ' WHERE 1 ' . $condition . ' ORDER BY isdefault desc,createtime desc LIMIT ' . (($pindex - 1) * $psize) . ',' . $psize, $params);

        unset($row);
        $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('ewei_shop_auth') . ' where 1 ' . $condition . ' ', $params);
        $pager = pagination2($total, $pindex, $psize);
        include $this->template('');
    }

    public function add()
    {
        $this->authpost();
    }

    public function edit()
    {
        $this->authpost();
    }

    protected function authpost()
    {
        global $_W;
        global $_GPC;
        $id            = intval($_GPC['id']);
        $item          = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_auth') . ' WHERE id =:id and uniacid=:uniacid limit 1', array(':id' => $id, ':uniacid' => $_W['uniacid']));

        if (!empty($item)) {
            $data = json_decode(str_replace('&quot;', '\'', $item['data']), true);
        }

        if ($_W['ispost']) {
            
            load()->func('file');
            @rmdirs(IA_ROOT . '/addons/ewei_shopv2/data/auth/' . $_W['uniacid']);
            plog('poster.clear', '清除证书缓存');

            load()->model('account');
            $data         = array('uniacid' => $_W['uniacid'], 'title' => trim($_GPC['title']), 'keyword2' => trim($_GPC['keyword2']), 'bg' => save_media($_GPC['bg']), 'data' => htmlspecialchars_decode($_GPC['data']), 'isdefault' => intval($_GPC['isdefault']), 'createtime' => time(), 'waittext' => trim($_GPC['waittext']));

            if ($data['isdefault'] == 1) {
                pdo_update('ewei_shop_auth', array('isdefault' => 0), array('uniacid' => $_W['uniacid'], 'isdefault' => 1, 'type' => $data['type']));
            }

            if (!empty($id)) {
                pdo_update('ewei_shop_auth', $data, array('id' => $id, 'uniacid' => $_W['uniacid']));
                plog('poster.edit', '修改授权证书 ID: ' . $id);
            } else {
                pdo_insert('ewei_shop_auth', $data);
                $id = pdo_insertid();
                plog('poster.add', '添加授权证书 ID: ' . $id);
            }

            show_json(1, array('url' => webUrl('poster/auth/edit', array('id' => $id, 'tab' => str_replace('#tab_', '', $_GPC['tab'])))));
        }

        $imgroot = $_W['attachurl'];

        if (empty($_W['setting']['remote'])) {
            setting_load('remote');
        }

        if (!empty($_W['setting']['remote']['type'])) {
            $imgroot = $_W['attachurl_remote'];
        }

        if (!$item['bg']) {
            $item['bg'] = $_W['siteroot'] . "addons/ewei_shopv2/plugin/poster/static/images/bg.jpg";
        }

        include $this->template("poster/authpost");
    }

    // 删除证书数据
    public function delete()
    {
        global $_W;
        global $_GPC;
        $id = intval($_GPC['id']);

        if (empty($id)) {
            $id = (is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0);
        }

        $auths = pdo_fetchall('SELECT id,title FROM ' . tablename('ewei_shop_auth') . ' WHERE id in ( ' . $id . ' ) and uniacid=' . $_W['uniacid']);

        foreach ($auths as $auth) {
            pdo_delete('ewei_shop_auth', array('id' => $auth['id'], 'uniacid' => $_W['uniacid']));
            plog('poster.delete', '删除证书 ID: ' . $id . ' 证书名称: ' . $auth['title']);
        }

        show_json(1, array('url' => webUrl('poster.auth')));
    }

    // 清除图片缓存
    public function clear()
    {
        global $_W;
        global $_GPC;
        load()->func('file');
        @rmdirs(IA_ROOT . '/addons/ewei_shopv2/data/auth/' . $_W['uniacid']);
        plog('poster.clear', '清除证书缓存');
        show_json(1, array('url' => webUrl('poster.auth')));
    }

    // 设置默认的授权证书
    public function setdefault()
    {
        global $_W;
        global $_GPC;
        $id   = intval($_GPC['id']);
        $auth = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_auth') . ' WHERE id = \'' . $id . '\'');

        if (empty($auth)) {
            show_json(0, '抱歉，证书不存在或是已经被删除！');
        }

        pdo_update('ewei_shop_auth', array('isdefault' => 0), array('uniacid' => $_W['uniacid'], 'isdefault' => 1));
        pdo_update('ewei_shop_auth', array('isdefault' => 1), array('uniacid' => $_W['uniacid'], 'id' => $auth['id']));
        plog('poster.setdefault', '设置默认授权证书 ID: ' . $id . ' 证书名称: ' . $poster['title']);
        show_json(1);
    }
}
