<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}

class Myroom_EweiShopV2Page  extends MobileLoginPage
{
    //直播间管理首页
    public function main(){
        global $_W,$_GPC;
        $member = m('member')->getMember($_W['openid']);
        $room = pdo_fetch("SELECT * FROM `ims_ewei_shop_live` WHERE uniacid={$_W['uniacid']} and mid={$member['id']}");
        if(empty($room)){
            header("location:".mobileUrl('live/reg'));
        }
        include $this->template();
    }
    //直播间信息修改
    public function set(){
        global $_W,$_GPC;
        $member = m('member')->getMember($_W['openid']);
        $liveidentity = array(
            array('type' => 1, 'identity' => 'yizhibo', 'name' => '一直播'),
            array('type' => 1, 'identity' => 'inke', 'name' => '映客直播')
        );
        $room = pdo_fetch("SELECT * FROM `ims_ewei_shop_live` WHERE uniacid={$_W['uniacid']} and mid={$member['id']}");
        $nickname = '主播';
        $uid = 'console_1_founder_266';
        //socket
        $wsConfig = json_encode(array('address' => p('live')->getWsAddress(), 'scene' => 'live', 'roomid' => $room['id'], 'uniacid' => $_W['uniacid'], 'uid' => $uid, 'nickname' => $nickname, 'attachurl' => $_W['attachurl']));
        if($_W['isajax']){
            $roomdata = $_GPC['roomdata'];
            $roomdata['livetime'] = time();
            $res = pdo_update("ewei_shop_live",$roomdata,array('uniacid'=>$_W['uniacid'],'mid'=>$member['id']));
            if($res){
                
                show_json(1);
            }else{
                show_json(0,'提交失败或无改动');
            }
        }
        include $this->template();
    }
    //直播间二维码
    public function qrcode(){
        global $_W,$_GPC;
        $member = m('member')->getMember($_W['openid']);
        $roomId = pdo_fetchcolumn("SELECT id FROM `ims_ewei_shop_live` WHERE uniacid={$_W['uniacid']} and mid={$member['id']}");
        $qrcode = p('poster')->createLiveRoomPoster($roomId);
        include $this->template();
    }
    //直播间商品管理
    public function goods(){
        global $_W,$_GPC;
        $member = m('member')->getMember($_W['openid']);
        $roomId = pdo_fetchcolumn("SELECT id FROM `ims_ewei_shop_live` WHERE uniacid={$_W['uniacid']} and mid={$member['id']}");
        $goods = pdo_fetchall("SELECT g.id,g.thumb,g.marketprice,g.title,lg.liveprice FROM `ims_ewei_shop_goods` as g LEFT JOIN `ims_ewei_shop_live_goods` as lg on lg.goodsid=g.id WHERE lg.uniacid={$_W['uniacid']} and lg.liveid={$roomId}");
        foreach($goods as &$good){
            $good['thumb'] = tomedia($good['thumb']);
        }
        include $this->template();
    }
    //获取商城商品
    public function get_goods()
    {
        global $_W;
        global $_GPC;
        $mid = intval($_GPC['mid']);

        if (empty($mid)) {
            $mid = m('member')->getMid();
        }

        $args = array('page' => $_GPC['page'], 'pagesize' => 10, 'nocommission' => 0, 'order' => 'displayorder desc,createtime desc', 'by' => '');



        $list = m('goods')->getList($args);
        show_json(1, array('list' => $list['list'], 'total' => $list['total'], 'pagesize' => $args['pagesize']));
    }

    //选择商品
    public function select()
    {
        global $_W;
        global $_GPC;
        $member = m('member')->getMember($_W['openid']);
        $roomId = pdo_fetchcolumn("SELECT id FROM `ims_ewei_shop_live` WHERE uniacid={$_W['uniacid']} and mid={$member['id']}");

        if ($_W['ispost']) {


            $goods = $_GPC['goods'];
            $goodsids = implode(",",$_GPC['goodsids']);


            foreach($goods as $good){
                $live_good = pdo_fetch("SELECT * FROM `ims_ewei_shop_live_goods` WHERE uniacid={$_W['uniacid']} and liveid={$roomId} and goodsid={$good['id']}");
                //存在商品修改价格
//                if($good['liveprice']){
//                    if($live_good && $live_good['liveprice']!=$good['liveprice']){
//                        pdo_update("ewei_shop_live_goods",array('liveprice'=>$good['liveprice'],'minliveprice'=>$good['liveprice'],'maxliveprice'=>$good['liveprice']),array('uniacid'=>$_W['uniacid'],'liveid'=>$roomId,'goodsid'=>$good['id']));
//                    }
//                }

                //不存在则添加商品
                if(empty($live_good)){
                    pdo_insert('ewei_shop_live_goods',array('uniacid'=>$_W['uniacid'],'goodsid'=>$good['id'],'liveid'=>$roomId,'liveprice'=>$good['liveprice'],'minliveprice'=>$good['liveprice'],'maxliveprice'=>$good['liveprice']));
                }
            }
            if($goodsids){
                $deleteSql = "delete FROM `ims_ewei_shop_live_goods` WHERE uniacid={$_W['uniacid']} and liveid={$roomId} and `goodsid` NOT IN({$goodsids})";
                pdo_update("ewei_shop_live",array('goodsid'=>$goodsids),array('uniacid'=>$_W['uniacid'],'id'=>$roomId));
            }else{
                $deleteSql =  "delete FROM `ims_ewei_shop_live_goods` WHERE uniacid={$_W['uniacid']} and liveid={$roomId}";
                pdo_update("ewei_shop_live",array('goodsid'=>''),array('uniacid'=>$_W['uniacid'],'id'=>$roomId));
            }
            pdo_query($deleteSql);
            show_json(1);
        }

        $goods = array();

        if (!empty($shop['selectgoods'])) {
            $goodsids = explode(',', $shop['goodsids']);

            if (!empty($goodsids)) {
                $goods = pdo_fetchall('select id,title,marketprice,thumb from ' . tablename('ewei_shop_goods') . ' where uniacid=:uniacid and id in ( ' . trim($shop['goodsids']) . ')', array(':uniacid' => $_W['uniacid']));
                $goods = set_medias($goods, 'thumb');
            }
        }

        $set = m('common')->getSysset('shop');

        if ($_W['shopset']['category']['level'] != -1) {
            $category = m('shop')->getCategory();
        }

        include $this->template();
    }

    //直播状态
    public function console(){
        global $_W,$_GPC;
        $member = m('member')->getMember($_W['openid']);
        $room = pdo_fetch("SELECT * FROM `ims_ewei_shop_live` WHERE uniacid={$_W['uniacid']} and mid={$member['id']}");
//        $uid = 'console' . '_' . $_W['uid'] . '_' . $_W['role'] . '_' . $_W['uniacid'];
        $nickname = '主播';
        $uid = 'console_1_founder_266';
        $wsConfig = json_encode(array('address' => p('live')->getWsAddress(), 'scene' => 'live', 'roomid' => $room['id'], 'uniacid' => $_W['uniacid'], 'uid' => $uid, 'nickname' => $nickname, 'attachurl' => $_W['attachurl']));

        include $this->template();
    }
}

?>