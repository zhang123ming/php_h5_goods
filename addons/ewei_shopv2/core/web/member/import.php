<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}

define('IA_ROOT', str_replace('\\', '/', dirname(dirname(__FILE__))));
require_once IA_ROOT . '/framework/library/phpexcel/PHPExcel/Reader/CSV.php';
class Import_EweiShopV2Page extends WebPage
{
    public function main()
    {
        global $_W;
        global $_GPC;
        $uploadStart = '0';
        $uploadnum   = '0';
        $excelurl    = $_W['siteroot'] . 'addons\ewei_shopv2\data\import\member\temp.xlsx';

        //点击了提交操作后读取表格数据
        if ($_W['ispost']) {
            $rows      = m('excel')->import('excelfile');
            $num       = count($rows);
            $i         = 0;
            $colsIndex = array();

            foreach ($rows[0] as $cols => $col) {

                if ($col == 'openid') {
                    $colsIndex['openid'] = $i;
                }

                if ($col == 'nickname') {
                    $colsIndex['nickname'] = $i;
                }

                if ($col == 'amount') {
                    $colsIndex['amount'] = $i;
                }

                if ($col == 'credit2') {
                    $colsIndex['credit2'] = $i;
                }

                if ($col == 'agentopenid') {
                    $colsIndex['agentopenid'] = $i;
                }

                if ($col == 'level') {
                    $colsIndex['level'] = $i;
                }

                ++$i;
            }
            //把表格数据赋值给数据库对应字段

            $filename = $_FILES['excelfile']['name'];
            $filename = substr($filename, 0, strpos($filename, '.'));
            $rows     = array_slice($rows, 2, count($rows) - 2);
            $items    = array();
            $num      = 0;
            foreach ($rows as $rownu => $col) {
                $item                = array();
                $item['openid']      = $col[$colsIndex['openid']];
                $item['nickname']    = $col[$colsIndex['nickname']];
                $item['amount']      = $col[$colsIndex['amount']];
                $item['credit2']     = $col[$colsIndex['credit2']];
                $item['agentopenid'] = $col[$colsIndex['agentopenid']];
                $item['level']       = $col[$colsIndex['level']];
                $items[]             = $item;
                ++$num;
            }

            session_start();
            $_SESSION['importCSV'] = $items; //把值先存在session中
            $uploadStart           = '1'; //开始上传
            $uploadnum             = $num;
        }

        include $this->template();
    }

    public function fetch()
    {
        global $_GPC;
        set_time_limit(0);
        $num      = intval($_GPC['num']);
        $totalnum = intval($_GPC['totalnum']);
        session_start();
        $items = $_SESSION['importCSV']; //获取session中的数据
        $ret   = $this->save_importcsv($items[$num]); //存入数据库

        plog('importCSV.main', '会员批量导入' . $ret[id]);

        if ($totalnum <= $num + 1) {
            unset($_SESSION['importCSV']);
            $this->lock();
        }
        exit(json_encode($ret));
    }

    public function save_importcsv($item = array(), $id = 0)
    {
        global $_W;

        $isexist=$this->isexist($item['openid']);
        if($isexist){
            return; 
        }

        $memberinfo = array(
            'uniacid'    => $_W['uniacid'],
            'openid'     => $item['openid'],
            'nickname'   => $item['nickname'],
            'credit2'    => $item['credit2'],
            'level'      => intval($item['level']),
            'isagent'    => intval($item['level']) > 0 ? 1 : 0,
            'status'     => intval($item['level']) > 0 ? 1 : 0,
            'createtime' => TIMESTAMP,
        );

        $commission = array(
            'uniacid'    => $_W['uniacid'],
            'openid'     => $item['openid'],
            'createtime' => TIMESTAMP,
            'amount'     => $item['amount'],
            'remark'     => '系统充值' . $item['amount'],
        );

        pdo_insert('ewei_shop_member', $memberinfo); //存入商城会员表

        session_start();
        $id                 = pdo_insertid();
        $_SESSION['lock'][] = array('id' => $id, 'agentopenid' => $item['agentopenid'] ,'level' => $memberinfo['level']);

        if ($item['amount'] > 0) {
            pdo_insert('ewei_shop_commission_record', $commission); //存入会员佣金表
        }

        return array('result' => '1', 'id' => $id);

    }

    public function lock()
    {
        global $_W, $_GPC;

        session_start();

        foreach ($_SESSION['lock'] as $key => $value) {
            if (empty($value['agentopenid'])) {
                continue;
            }
            $agentid = pdo_fetch('SELECT id FROM ' . tablename('ewei_shop_member') . ' WHERE openid=:openid', array('openid' => $value['agentopenid']));

            $count = pdo_update('ewei_shop_member', array('agentid' => $agentid['id']), array('id' => $value['id'], 'uniacid' => $_W['uniacid']));

            $ainviter = p('commission')->getAgentId($agentid['id'], array('level'=>$value['level']));

            pdo_update('ewei_shop_member', $ainviter, array('id' => $value['id']));
            $teamlist = pdo_fetchall('select * from ' . tablename('ewei_shop_member') . ' where uniacid=:uniacid and find_in_set(' . $value['id'] . ',fids) order by fids asc', array(':uniacid' => $_W['uniacid']));
            foreach ($teamlist as $k => $v) {
                $agentdata1 = p('commission')->getAgentId($v['agentid'], $v);
                pdo_update('ewei_shop_member', $agentdata1, array('id' => $v['id']));
            }
            // file_put_contents('mysession.txt', date('Y-m-d H:i:s') . ":" . var_export($count, true) . "\r\n", FILE_APPEND);
        }

        unset($_SESSION['lock']);
    }

    public function isexist($openid)
    {
        global $_W, $_GPC;

        $count = pdo_fetch('SELECT count(*) as count FROM ' . tablename('ewei_shop_member') . ' WHERE openid=:openid AND uniacid=:uniacid', array(':openid' => $openid, ':uniacid' => $_W['uniacid']));

        if ($count['count'] == 1) {
            return true;
        }
        return false;
    }

}
