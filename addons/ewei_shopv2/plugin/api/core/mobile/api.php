<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}

class Api_EweiShopV2Page extends Page
{
    /*
    param  string $myKey 验证密钥
    param  array $_GPC=array{

    'otime' 传递过来的验证时间
    'mid'   借用发送红包的用户
    'key'   传递过来的加密密钥
    }

     */
    public function commissionCreateRelationship()
    {
        global $_W, $_GPC;
        $shareID = $_GPC['shareid'];
        $openID  = $_GPC['openid'];

        load()->model('mc');

        if (!$shareID || !$openID || $openID == $shareID) {
            $value = 0;
        } else {
            $sql      = "SELECT * FROM " . tablename("ewei_shop_member") . " WHERE uniacid=$_W[uniacid] and openid='$openID'";
            $authData = pdo_fetch($sql);
            if ($authData['agentid'] > 0) {
                $value = 1;
            } else {
                $sql       = "SELECT * FROM " . tablename("ewei_shop_member") . " WHERE uniacid=$_W[uniacid] and openid='$shareID'";
                $shareData = pdo_fetch($sql);
                if (!$shareData) {
                    $uid    = mc_openid2uid($shareID);
                    $mc     = mc_fetch($uid, array('realname', 'nickname', 'mobile', 'avatar', 'resideprovince', 'residecity', 'residedist', 'gender'));
                    $member = array('uniacid' => $_W['uniacid'], 'uid' => $uid, 'openid' => $shareID, 'realname' => (!(empty($mc['realname'])) ? $mc['realname'] : ''), 'mobile' => (!(empty($mc['mobile'])) ? $mc['mobile'] : ''), 'nickname' => (!(empty($mc['nickname'])) ? $mc['nickname'] : ''), 'nickname_wechat' => (!(empty($mc['nickname'])) ? $mc['nickname'] : ''), 'avatar' => (!(empty($mc['avatar'])) ? $mc['avatar'] : ''), 'avatar_wechat' => (!(empty($mc['avatar'])) ? $mc['avatar'] : ''), 'gender' => (!(empty($mc['gender'])) ? $mc['gender'] : '-1'), 'province' => (!(empty($mc['resideprovince'])) ? $mc['resideprovince'] : ''), 'city' => (!(empty($mc['residecity'])) ? $mc['residecity'] : ''), 'area' => (!(empty($mc['residedist'])) ? $mc['residedist'] : ''), 'createtime' => time(), 'status' => 0);
                    pdo_insert('ewei_shop_member', $member);
                    $agentid = pdo_insertid();
                } else {
                    $agentid = $shareData['id'];
                }
                if ($authData) {
                    $data = array(
                        'agentid' => $agentid,
                    );
                    pdo_update("ewei_shop_member", $data, array('id' => $authData['id']));
                } else {
                    $uid    = mc_openid2uid($openID);
                    $mc     = mc_fetch($uid, array('realname', 'nickname', 'mobile', 'avatar', 'resideprovince', 'residecity', 'residedist', 'gender'));
                    $member = array('uniacid' => $_W['uniacid'], 'uid' => $uid, 'openid' => $openID, 'realname' => (!(empty($mc['realname'])) ? $mc['realname'] : ''), 'mobile' => (!(empty($mc['mobile'])) ? $mc['mobile'] : ''), 'nickname' => (!(empty($mc['nickname'])) ? $mc['nickname'] : ''), 'nickname_wechat' => (!(empty($mc['nickname'])) ? $mc['nickname'] : ''), 'avatar' => (!(empty($mc['avatar'])) ? $mc['avatar'] : ''), 'avatar_wechat' => (!(empty($mc['avatar'])) ? $mc['avatar'] : ''), 'gender' => (!(empty($mc['gender'])) ? $mc['gender'] : '-1'), 'province' => (!(empty($mc['resideprovince'])) ? $mc['resideprovince'] : ''), 'city' => (!(empty($mc['residecity'])) ? $mc['residecity'] : ''), 'area' => (!(empty($mc['residedist'])) ? $mc['residedist'] : ''), 'createtime' => time(), 'status' => 0, 'agentid' => $agentid);

                    pdo_insert("ewei_shop_member", $member);
                }
                $value = 100;
            }
        }
        echo $value;
    }

    //获取验证码
    public function verifycode()
    {
        global $_W;
        global $_GPC;
        $set     = m('common')->getSysset(array('shop', 'wap'));
        $mobile  = trim($_GPC['mobile']);
        $temp    = trim($_GPC['temp']);
        $imgcode = trim($_GPC['imgcode']);

        if (empty($mobile)) {
            show_json(0, '请输入手机号');
        }

        if (!empty($_SESSION['verifycodesendtime']) && (time() < ($_SESSION['verifycodesendtime'] + 60))) {
            show_json(0, '请求频繁请稍后重试');
        }

        if (!empty($set['wap']['smsimgcode'])) {
            if (empty($imgcode)) {
                show_json(0, '请输入图形验证码');
            }

            $imgcodehash = md5(strtolower($imgcode) . $_W['config']['setting']['authkey']);

            if ($imgcodehash != trim($_GPC['__code'])) {
                show_json(-1, '请输入正确的图形验证码');
            }
        }

        $data   = m('common')->getSysset('wap');
        $sms_id = $data['sms_bind'];

        if (empty($sms_id)) {
            exit(json_encode(array('status' => 0, 'msg' => '短信发送失败(NOSMSID)')));
        }

        $code     = random(5, true);
        $shopname = $_W['shopset']['shop']['name'];
        $ret      = com('sms')->send($mobile, $sms_id, array('验证码' => $code, '商城名称' => !empty($shopname) ? $shopname : '商城名称'));

        if ($ret['status']) {
            // $arr['code']=$code;
            // $arr['verifycodesendtime']=time();
            // file_put_contents('mysession.txt',date('Y-m-d H:i:s').":".var_export($ret,true)."\r\n",FILE_APPEND);
            exit(json_encode(array('status' => $ret['status'], 'msg' => '发送成功', 'code' => $code, 'verifycodesendtime' => time())));
        }

        exit(json_encode(array('status' => 0, 'msg' => $ret['message'])));
    }

    public function payRedpack()
    {
        global $_W;
        global $_GPC;

        //发红包muserid
        $myKey      = 'AQWERTYUIHFHFBXNAOLDAJSDKADKDGHNBML';
        $credittype = 'credit2';
        $credits    = 0 - $_GPC['payments_money'];
        $remark     = "发送红包扣款";
        $typestr    = '余额';
        $userInfo   = m('member')->getMidMember($_GPC['mid']);

        $_W['shopset']['shop']['name'] = "红包领取";
        //判断是否符合验证
        $apikey = md5(md5($_GPC['mid'] . $myKey) . $_GPC['otime']);
        if ($apikey != $_GPC['key']) {
            $errmsg = "发送失败,错误代码：ApiRedPack002";
            show_message($errmsg, '', 'error');
        }

        //超时
        if (time() - $_GPC['otime'] > 60) {
            $errmsg = "发送失败,错误代码：ApiRedPack003";
            show_message($errmsg, '', 'error');
        }

        if ($userInfo['credit2'] <= 0 || $_GPC['payments_money'] > $userInfo['credit2']) {
            $errmsg = "发送失败,错误代码：ApiRedPack001";
            show_message($errmsg, '', 'error');
        }

       

        if ($_GPC['note']) {
            $item = pdo_fetch('select * from ' . tablename('ewei_shop_member_log') . ' where uniacid=:uniacid and note=:note', array(':uniacid' => $_W[uniacid], ':note' => $_GPC['note']));
            //var_dump($item);die;
            if ($item) {
                show_message("红包已经发放，不要重复领取", '', 'error');

                if ($_GPC['callback'] && $res) {
                    $url = $_GPC['callback'] . '&amount=' . $_GPC['payments_money'] . "&success=1";
                    header("Location:" . $url);
                }
            }
        }
        m('member')->setCredit($userInfo['openid'], $credittype, $credits, array($userInfo['uid'], $typestr . ' ' . $remark));
        file_put_contents('mysql2018040415.txt', $_SERVER['QUERY_STRING'] . "\r\n", FILE_APPEND);
        $set   = m('common')->getSysset('shop');
        $logno = m('common')->createNO('member_log', 'logno', 'Api');
        $data  = array('openid' => $userInfo['openid'], 'logno' => $logno, 'uniacid' => $_W['uniacid'], 'type' => '0', 'createtime' => TIMESTAMP, 'status' => '1', 'title' => $set['name'] . '发送红包扣款', 'money' => $credits, 'remark' => $remark, 'rechargetype' => 'system', 'note' => $_GPC[note]);
        pdo_insert('ewei_shop_member_log', $data);
        $logid = pdo_insertid();
        if ($logid) {

            $params = array('openid' => $_W['openid'], 'tid' => $logno, 'send_name' => "扫码得红包", 'money' => $_GPC['payments_money'], 'wishing' => '恭喜发财,大吉大利!', 'act_name' => '越扫越开心', 'remark' => $desc);

            $res      = m('common')->sendredpack($params);
            $senddata = array('error' => $res, 'tid' => $logno, 'money' => $_GPC['payments_money']);
            pdo_update('ewei_shop_member_log', array('senddata' => json_encode($senddata)), array('id' => $logid));
            if ($_GPC['callback'] && $res) {

                $url = $_GPC['callback'] . '&amount=' . $_GPC['payments_money'] . "&success=1";
                //header("Location:".$url);
                $errmsg = "成功发送，请回微信进行领取";
                show_message($errmsg, $url, 'success');

            }
        }

    }
/**
 * 一物一码抽奖发送红包
 * @Author   seanyang                
 * @DateTime 2019-02-19T19:43:15+0800
 * @return   [type]                   [description]
 */
    public function payGicaiRedpacket()
    {
        global $_W,$_GPC;
         //发红包muserid
        $credittype = 'credit2';
        $credits    = 0 - $_GPC['payments_money'];
        $remark     = "发送红包扣款";
        $typestr    = '余额';
        $userInfo   = m('member')->getMidMember($_GPC['mid']);
        if ($userInfo['credit2'] <= 0 || $_GPC['payments_money'] > $userInfo['credit2']) {
            $arr = array(
                'error' => 1,
                'message' => '账户余额不足'
            );
            echo json_encode($arr);
            exit();
        }
        if ($_GPC['payments_money']<1) {
            $arr = array(
                'error' => 1,
                'message' => '发送金额不能少于1元'
            );
            echo json_encode($arr);
            exit();
        }
        
        m('member')->setCredit($userInfo['openid'], $credittype, $credits, array($userInfo['uid'], $typestr . ' ' . $remark));
        $set   = m('common')->getSysset('shop');
        $logno = m('common')->createNO('member_log', 'logno', 'Api');
        $data  = array('openid' => $userInfo['openid'], 'logno' => $logno, 'uniacid' => $_W['uniacid'], 'type' => '0', 'createtime' => TIMESTAMP, 'status' => '1', 'title' => $set['name'] . '发送红包扣款', 'money' => $credits, 'remark' => $remark, 'rechargetype' => 'system', 'note' => $_GPC[note]);
        pdo_insert('ewei_shop_member_log', $data);
        $logid = pdo_insertid();

        if ($logid) {
           $arr = array(
                'error' =>0 , 
                'message' =>'余额扣款成功'
            );
        }else{
            $arr = array(
                'error' =>1,
                'message'=>'余额扣款失败'
            );
        }
        
        echo json_encode($arr);
        exit();
    }


    public function footerMenus($diymenuid = 0, $ismerch = 0, $texts = array())
	{

		global $_W;
		global $_GPC;

		
		$diymenuid=$_GPC['menuID'];
		if (!empty($diymenuid)) {
			$menu = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_diypage_menu') . ' WHERE id=:id and uniacid=:uniacid limit 1 ', array(':id' => $diymenuid, ':uniacid' => $_W['uniacid']));

			if (!empty($menu)) {
				$menu = $menu['data'];
				$menu = base64_decode($menu);
				$diymenu = json_decode($menu, true);
				$showdiymenu = true;
			}
		}

		if ($showdiymenu) {
			include $this->template('diypage/menu');
		}
		else {
			if (($controller == 'commission') && ($routes[1] != 'myshop')) {
				include $this->template('commission/_menu');
			}
			else if ($controller == 'creditshop') {
				include $this->template('creditshop/_menu');
			}
			else if ($controller == 'groups') {
				include $this->template('groups/_groups_footer');
			}
			else if ($controller == 'merch') {
				include $this->template('merch/_menu');
			}
			else if ($controller == 'mr') {
				include $this->template('mr/_menu');
			}
			else if ($controller == 'newmr') {
				include $this->template('newmr/_menu');
			}
			else if ($controller == 'sign') {
				include $this->template('sign/_menu');
			}
			else if ($controller == 'sns') {
				include $this->template('sns/_menu');
			}
			else if ($controller == 'seckill') {
				include $this->template('seckill/_menu');
			}
			else if ($controller == 'mmanage') {
				include $this->template('mmanage/_menu');
			}
			else if ($ismerch) {
				include $this->template('merch/_menu');
			}
			else {
				include $this->template('_menu');
			}
		}

	}
    public function synchronizationShop()
    {
        global $_W, $_GPC;
        if ($_GPC['unid']) {
            $agents = pdo_fetchall('select uniacid,uid,openid,mobile,weixin,realname,nickname,avatar from ' . tablename('ewei_shop_member') . ' where uniacid=:uniacid and status = 1 and isagent = 1 and isblack = 0 and mobile !="" limit 50', array(':uniacid' => $_GPC['unid']));
            $members = pdo_fetchall('select uniacid,uid,openid,mobile,nickname,avatar from ' . tablename('ewei_shop_member') . ' where uniacid=:uniacid and isblack = 0', array(':uniacid' => $_GPC['unid']));
            $result['agents'] = $agents;
            $result['members'] = $members;
            echo json_encode($result);exit();
        }
    }

    public function checkShop()
    {
        global $_W, $_GPC;
        $uniacid = $_GPC['i'];
        $result = pdo_fetch('select sets from ' . tablename('ewei_shop_sysset') . ' where uniacid=:uniacid', array(':uniacid' => $uniacid));
        $res = unserialize($result['sets']);
        $data['open_wxwork'] = $res['shop']['open_wxwork'];
        $data['corp_id'] = $res['shop']['corp_id'];
        $data['corp_tsecret'] = $res['shop']['corp_tsecret'];
        $data['corp_agentid'] = $res['shop']['corp_agentid'];
        $data['corp_secret'] = $res['shop']['corp_secret'];
        echo json_encode($data);exit();
    }

    public function sendcardnews()
    {
        global $_W, $_GPC;
        $uniacid = $_GPC['i'];
        $url = $_GPC['urls'];
        if(!empty($url)){
            $url = $url."&user_id=".$_GPC['user_id'];
        }
        $memberid = $_GPC['userid'];
        $member = pdo_fetch('select * from ' . tablename('ewei_shop_member') . ' where uniacid=:uniacid and uid=:uid', array(':uniacid' => $uniacid,':uid' => $memberid));
        $agent = pdo_fetch('select * from ' . tablename('ewei_shop_member') . ' where uniacid=:uniacid and id=:id', array(':uniacid' => $uniacid,':id' => $member['agentid']));
        $content = "会员 < ".$agent['nickname']." > 给您发来一条消息";
        file_put_contents('mynotice.txt', $content);
        m('message')->sendCustomNotice($member['openid'],$content,$url);
        echo json_encode($url);exit();
    }
}
