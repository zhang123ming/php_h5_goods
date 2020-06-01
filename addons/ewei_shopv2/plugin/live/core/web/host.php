<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}

class Host_EweiShopV2Page extends PluginWebPage
{
    public function main()
    {
        global $_W;
        global $_GPC;
        $pindex = max(1, intval($_GPC['page']));
        $psize = 20;
        $params = array(':uniacid' => $_W['uniacid']);
        $condition = '';
        $keyword = trim($_GPC['keyword']);
        if (!(empty($keyword)))
        {
            $condition .= ' and ( hostname like :keyword or realname like :keyword or phone like :keyword)';
            $params[':keyword'] = '%' . $keyword . '%';
        }
        if ($_GPC['status'] != '')
        {
            $condition .= ' and status=' . intval($_GPC['status']);
        }
        $sql = 'select  *  from ' . tablename('ewei_shop_live_host') . '   where uniacid=:uniacid ' . $condition . ' ORDER BY  applytime desc';
        if (empty($_GPC['export']))
        {
            $sql .= ' limit ' . (($pindex - 1) * $psize) . ',' . $psize;
        }
        $list = pdo_fetchall($sql, $params);
        foreach($list as &$val){
            $val['nickname'] = pdo_fetchcolumn("SELECT nickname FROM `ims_ewei_shop_member` WHERE uniacid={$_W['uniacid']} and openid='{$val['openid']}'");
        }

        $total = pdo_fetchcolumn('select count(*) from' . tablename('ewei_shop_live_host') . '  where uniacid = :uniacid ' . $condition, $params);
//        if ($_GPC['export'] == '1')
//        {
//            ca('merch.user.export');
//            plog('merch.user.export', '导出申请数据');
//            foreach ($list as &$row )
//            {
//                $row['applytime'] = ((empty($row['applytime']) ? '-' : date('Y-m-d H:i', $row['applytime'])));
//                $row['statusstr'] = ((empty($row['status']) ? '待审核' : (($row['status'] == 1 ? '已入驻' : '驳回'))));
//            }
//            unset($row);
//            m('excel')->export($list, array( 'title' => '数据-' . date('Y-m-d-H-i', time()), 'columns' => array( array('title' => 'ID', 'field' => 'id', 'width' => 12), array('title' => '名', 'field' => 'hostname', 'width' => 24), array('title' => '主营项目', 'field' => 'salecate', 'width' => 12), array('title' => '商家简介', 'field' => 'desc', 'width' => 24), array('title' => '联系人', 'field' => 'realname', 'width' => 12), array('title' => '手机号', 'field' => 'moible', 'width' => 12), array('title' => '申请时间', 'field' => 'applytime', 'width' => 12), array('title' => '状态', 'field' => 'statusstr', 'width' => 12) ) ));
//        }
        $pager = pagination($total, $pindex, $psize);
        load()->func('tpl');
        include $this->template();
    }

    public function post()
    {
        global $_W;
        global $_GPC;
        $id = intval($_GPC['id']);
        $uniacid = $_W['uniacid'];
        $item = pdo_fetch('select * from ' . tablename('ewei_shop_live_host') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $id, ':uniacid' => $_W['uniacid']));
        $idcard1 = tomedia($item['idcard1']);
        $idcard2 = tomedia($item['idcard2']);
        if (empty($item))
        {
            if ($_W['ispost'])
            {
                show_json(0, '未找到主播申请!');
            }
            $this->message('未找到主播申请!', webUrl('live/host', array('status' => 0)), 'error');
        }
        $member = m('member')->getMember($item['openid']);

        $category = pdo_fetchall('select * from ' . tablename('ewei_shop_live_category') . ' where uniacid = ' . $uniacid . ' and enabled = 1 ');
        $liveidentity = array(
            array('type' => 1, 'identity' => 'huajiao', 'name' => '花椒直播'),
            array('type' => 1, 'identity' => 'yizhibo', 'name' => '一直播'),
            array('type' => 1, 'identity' => 'inke', 'name' => '映客直播')
        );
        if ($_W['ispost'])
        {
            $status = intval($_GPC['status']);
            $reason = trim($_GPC['reason']);
            if ($status == -1)
            {
                if (empty($reason))
                {
                    show_json(0, '请填写驳回理由.');
                }
            }

            $item['status'] = $status;
            $item['reason'] = $reason;
            $item['hostname'] = trim($_GPC['hostname']);
            $item['realname'] = trim($_GPC['realname']);
            $item['phone'] = trim($_GPC['phone']);
            if ($status == 1)
            {
                //审核通过生成直播间
                $room = array(
                    'uniacid'=>$_W['uniacid'],
                    'title'=>$item['hostname']."的直播间",
                    'category'=>$_GPC['category'],
                    'mid'=>$member['id'] ? $member['id'] : 0,
                    'livetype'=>1,
                    'liveidentity'=>$_GPC['identity'],
                    'screen'=>1,
                    'url'=>$item['liveurl'],
                    'covertype'=>0,
                    'status'=>1
                );
                $res = pdo_insert('ewei_shop_live',$room);
                if($res){
                    pdo_update('ewei_shop_live_host', $item, array('id' => $item['id']));
                    show_json(1);
                }else{
                    show_json(0,'直播间生成失败');
                }
            }
            else if ($status == -1)
            {
                pdo_update('ewei_shop_live_host', $item, array('id' => $item['id']));
            }
            show_json(1);
        }
        include $this->template();
    }

    public function delete()
    {
        global $_W;
        global $_GPC;
        $id = intval($_GPC['id']);

        if (empty($id))
        {
            $id = ((is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0));
        }
        $regs = pdo_fetchall('SELECT id,hostname FROM ' . tablename('ewei_shop_live_host') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);
        foreach ($regs as $reg )
        {
            pdo_delete('ewei_shop_live_host', array('id' => $reg['id']));
            plog('live.host.delete', '删除主播申请 <br/> 主播名称:  ' . $reg['hostname']);
        }
        show_json(1, array('url' => referer()));
    }
}

?>
