<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}

class Appointment_EweiShopV2Page extends PluginWebPage
{
    public function main()
    {
        global $_W;
        global $_GPC;  
        $openbind = $_W['shopset']['diyform']['openbind'];     
        $pindex = max(1, intval($_GPC['page']));
        $psize = 20;
        $params = array();
        $condition = '';
        $keyword = trim($_GPC['keyword']);
        if(!empty($keyword)){
            $re = pdo_fetchall('select openid from '.tablename('ewei_shop_member').' where uniacid=:uniacid and ( realname like :keyword or nickname like :keyword or mobile like :keyword or openid like :keyword)',array(':uniacid'=>$_W['uniacid'],':keyword'=>'%' .$keyword. '%'),'openid');
            if ($re) {
                $openidarr =  array_keys($re);
                    $openids = array_map(function($v){
                        return "'".$v."'";
                    }, $openidarr);
                $openids = implode(',', $openids);
                $condition .= ' and openid in ('.$openids.')';
            }
        }
        if ($_GPC['status'] != '') {
            $condition .= ' and status=' . intval($_GPC['status']);
        }

        $sql = 'select * from'.tablename('ewei_shop_diyform_information') .'where uniacid='.$_W['uniacid'] . $condition . ' ORDER BY createTime desc';
        $sql .= ' limit ' . (($pindex - 1) * $psize) . ',' . $psize;
        $list = pdo_fetchall($sql, $params);
        foreach ($list as &$v) {
           
            $v['diyfieldsdata'] = iunserializer($v['diyfieldsdata']); 
            $v['diyformfields'] = iunserializer($v['diyformfields']);
            /*小程序提交的自定义表单信息*/
            foreach ($v['diyformfields'] as $k => $value) {
                if($v['diyfieldsdata'][$k]){
                    $v['is_app'] = 1;
                }
            }
            $v['createTime'] = date('Y-m-d H:i', $v['createTime']);  
            $v['checktime'] = empty($v['checktime']) ? '' : date('Y-m-d H:i',$v['checktime']);
            $v['member']     =m('member') ->getMember($v['openid']);
        }
        $total = pdo_fetchcolumn('select count(id) from' . tablename('ewei_shop_diyform_information').' where uniacid =' . $_W['uniacid'] . $condition, $params);
            unset($v);
        $pager = pagination2($total, $pindex, $psize);

        if ($_GPC['export'] == 1) {
            ca('diyform.appointment.export');
            foreach ($list as &$prints) {
                foreach ($prints['diyformfields'] as $key => $value) {
                    foreach ($value as $k => $v) {
                       $prints['diyinformation'].=$v['tp_name']."：".$prints['diyfieldsdata'][$k]." ";
                    }
                   
                }
            }
            m('excel')->export($list, array('title'   => '报告-' . date('Y-m-d-H-i', time()),'columns' => array(
                array('title' => '用户', 'field' => 'nickname', 'width' => 24),
                array('title' => '创建时间', 'field' => 'createTime', 'width' => 24),
                array('title' => '自定义信息', 'field' => 'diyinformation', 'width' => 100)
            )
        ));
            plog('diyform.appointment.export', '导出报告');
        }
        load()->func('tpl');
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

        $datas = pdo_fetchall('SELECT * FROM ' . tablename('ewei_shop_diyform_information') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);

        foreach ($datas as $data) {
             pdo_delete('ewei_shop_diyform_information', array('id' => $data['id']));                        
       }
        show_json(1, array('url' => referer()));
    }
   
    public function check()
    {
        global $_W;
        global $_GPC;
        $id = intval($_GPC['id']);
        if (empty($id)) {
            $id = (is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0);
        }

        $status = intval($_GPC['status']);
        $datas = pdo_fetchall('SELECT * FROM ' . tablename('ewei_shop_diyform_information') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);
        foreach ($datas as $data) {
            if ($data['status'] === $status) {
                continue;
            }
            if ($status == 1) {
                pdo_update('ewei_shop_diyform_information', array('status' => 1,'checktime'=>time()), array('id' => $data['id'], 'uniacid' => $_W['uniacid']));
                pdo_update('ewei_shop_member', array("need_up"=>0), array('openid' => $data['openid']));
                plog('diyfrom.appointment.check', '审核信息:  ID: ' . $data['id'] . ' /  ' . $data['openid']);
            }
            else {
                pdo_update('ewei_shop_diyform_information', array('status' => 0,'checktime'=>0), array('id' => $data['id'], 'uniacid' => $_W['uniacid']));
                plog('diyform.appointment.check', '取消审核信息:  ID: ' . $data['id'] . ' /  ' . $data['openid'] );
            }
        }
        show_json(1, array('url' => referer()));
    }

    public function edit()
    {
        $this->post();
    }

    protected function post()
    {
        global $_W;
        global $_GPC;
        $id = intval($_GPC['id']);
        $odata = pdo_fetchall('SELECT * FROM ' . tablename('ewei_shop_diyform_information') . ' WHERE id =' . $id . ' AND uniacid=' . $_W['uniacid']);
        $condition =" and id=:id";
        $params[':id'] = $id;
        if ($_W['ispost']) {
            $data = array();
            if (!empty($id)) {                
                $data['status'] = intval($_GPC['status']);
                $data['remarks'] = $_GPC["remarks"];
                $data['checktime'] = time();
                pdo_update('ewei_shop_diyform_information', $data, array('id' => $id));
                if($data['status']==1){
                    pdo_update('ewei_shop_member', array("need_up"=>0), array('openid' => $odata[0]["openid"]));
                }
                plog('diyform.appointment.edit', '修改分类 ID: ' . $id);
            }            

            show_json(1, array('url' => webUrl('diyform/appointment', array('op' => 'display'))));
        }

        $sql = 'select * from'.tablename('ewei_shop_diyform_information') .'where uniacid='.$_W['uniacid'] . $condition . ' ORDER BY createTime desc';
        $item = pdo_fetch($sql, $params); 
        $member = m('member')->getMember($item['openid']);
        $item['diyfieldsdata'] = unserialize($item['diyfieldsdata']);
        $item['diyformfields'] = unserialize($item['diyformfields']);
         /*小程序提交的自定义表单信息*/
        foreach ($item['diyformfields'] as $k => $value) {
            if($item['diyfieldsdata'][$k]){
                $item['is_app'] = 1;
            }
        }
        $item['headimgurl'] = $member['avatar'];  
        include $this->template("diyform/post");
    }
}

?>
