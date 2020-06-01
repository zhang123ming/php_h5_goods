<?php
if (!defined('IN_IA')) {
    exit("Access Denied");
}
class Search_keywords_EweiShopV2Page extends WebPage
{
    public function main()
    {
        global $_W;
        global $_GPC;
        $pindex = max(1, intval($_GPC['page']));
        $psize = 20;
        $params = array();
        $total = pdo_fetchcolumn('select  count(*) from ' . tablename('ewei_search_keywords'));
        if (empty($starttime) || empty($endtime)) {
            $starttime = strtotime('-1 month');
            $endtime = time();
        }
        if (!empty($_GPC['datetime'])) {
            $starttime = date('Y-m-d H:i:s', strtotime($_GPC['datetime']['start']));
            $endtime = date('Y-m-d H:i:s', strtotime($_GPC['datetime']['end']));

            $sql = "select * from" . tablename('ewei_search_keywords') . " where createTime BETWEEN '" . $starttime . "' and '" . $endtime . "' and search_keywords  Like '%" . $_GPC['title'] . "%' order by  search_count desc";
            $data = pdo_fetchall($sql, $params);
            $list = $data;
        } else {
            $sql = "select * from" . tablename('ewei_search_keywords') . "order by  search_count desc";
            $data = pdo_fetchall($sql, $params);
            $list = $data;
        }

        
       
    
        if ($_GPC['export'] == 1) {
            $item = pdo_fetchall('select  * from ' . tablename('ewei_search_keywords'));
            ca('statistics.search_keywords.export');
            
            m('excel')->export($item, array(
                'title'   => '搜索词统计-' . date('Y-m-d-H-i', time()),
                'columns' => array(
                    array('title' => '搜索词', 'field' => 'search_keywords', 'width' => 36),
                    array('title' => '次数', 'field' => 'search_count', 'width' => 20),
                    array('title' => '时间', 'field' => 'createTime', 'width' => 20),

                )
            ));
            plog('statistics.search_keywords.export', '搜索词统计');
        }

        load()->func('tpl');
        $pager = pagination2($total, $pindex, $psize);
        include $this->template();
    }
}
