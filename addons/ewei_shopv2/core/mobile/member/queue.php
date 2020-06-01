<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Queue_EweiShopV2Page extends MobileLoginPage
{

	public function main()
	{
        global $_GPC,$_W;
        $commissionSet = $_W['shopset']['commission'];
        $member = m('member')->getMember($_W['openid']);
        // 状态,0是分销商,1是合伙人,2是没在队列中的代理商,3是在队列中的代理商,4是已经返了的代理商,5是排队已经过去,还未返钱的
        $status = 0;
        $m_level = m('member')->getLevel($_W['openid']);
        if ($m_level['levelname'] == '普通会员') {
            $m_level['level'] = 0;
        }
        $queueTotalSql = " SELECT COUNT(*) FROM `ims_ewei_shop_queued_rebate` WHERE uniacid = ".$_W['uniacid'];
        $queueTotal = pdo_fetchcolumn($queueTotalSql);

        // 随机出30个名单,让前台页面去展示
        $randomLimit = $queueTotal>30 ? rand(0,abs($queueTotal-30)) : 0;

        $queueOKSql = " SELECT LEFT(RPAD(LEFT(a.nickname,1),LENGTH(a.nickname),'*'),10)AS nickname,b.price FROM `ims_ewei_shop_member` AS a, ims_ewei_shop_queued_rebate AS b WHERE a.openid = b.openid AND a.uniacid = b.uniacid AND b.openid <> '".$member['openid']."' LIMIT ".$randomLimit.",30 ";
        $queueOK = pdo_fetchall($queueOKSql);  // 这个为页面轮播变量取值
        if (count($queueOK)<30){
            $num1 = count($queueOK);
            $num2 = 30-$num1;
            $avgPrice = round(array_sum(array_column($queueOK,'price'))/$num1);
            if (empty($queueOK)){
                $avgPrice = 5000;
            }

            $memberTotalSql = " SELECT COUNT(*)AS total FROM ims_ewei_shop_member WHERE uniacid = ".$_W['uniacid'];
            $memberTotal = pdo_fetchcolumn($memberTotalSql);
            $memberTotal = empty($memberTotal) ? 0:$memberTotal;

            $memberLimit = $memberTotal>$num2?rand(0,abs($memberTotal-$num2)) : 0;

            $randomNameSql = " SELECT LEFT(RPAD(LEFT(nickname,1),LENGTH(nickname),'*'),10)AS nickname FROM ims_ewei_shop_member WHERE openid NOT IN ( SELECT openid FROM ims_ewei_shop_queued_rebate WHERE `status`<>1) AND nickname <> '' AND id <> ".$member['id']." LIMIT ".$memberLimit.",".$num2;
            $randomName = pdo_fetchall($randomNameSql);

            for ($i=1;$i<=$num2;$i++){
                if (empty($randomName[$i-1]['nickname'])){
                    break;
                }
                $queueOK[$num1+$i]['nickname'] = $randomName[$i-1]['nickname'];
                $queueOK[$num1+$i]['price'] = $avgPrice;
            }

        }
        shuffle($queueOK);

        if ($m_level['level']>91&&$m_level['level']<95){
			// 身份是代理商
			// 1.查询是否是在排队中
            $tblQueueSql = " SELECT * FROM `ims_ewei_shop_queued_rebate` WHERE openid = '".$_W['openid']."' AND uniacid = ".$_W['uniacid'];
            $tblQueue = pdo_fetch($tblQueueSql);
            if (empty($tblQueue)){
                // 没在队列中
                // 如果不是排队中?  您不在排队中哦
                $status = 2;
			}else{
            	if ($tblQueue['status']!=1){
                    $tblQueue['createtime'] = date('Y-m-d',$tblQueue['createtime']);
                    // 2.查询排多少位
                    $rankSql = " SELECT b.rank FROM(SELECT	t.openid, @rownum := @rownum + 1 AS rank FROM(SELECT @rownum := 0) r,(SELECT * FROM	ims_ewei_shop_queued_rebate	ORDER BY createtime ASC) AS t) AS b WHERE b.openid = '".$_W['openid']."'";
                    $rank = pdo_fetchcolumn($rankSql);

					$expectedRank = ($rank*10)+1;
					if ($expectedRank>$queueTotal){
                        $interval = $expectedRank-$queueTotal;
                        $status = 3;
					}else{
                        $interval = 0;
                        $status = 5;
					}
				}
            	if ($tblQueue['status']==1){ // 已经返了的
                    $status = 4;
				}
			}
		}
        if($m_level['level']>94){
        	// 身份是合伙人
            $status = 1;
		}
        if ($m_level['level']<91){
            // 身份是分销商,跳转到购买代理礼包页面
            $stime =  strtotime(date('Y-m-d',time()));
            $url = mobileUrl('goods', array('cate' => md5($stime)),true);
            $status = 0;
		}


		include $this->template('member/queue');
	}
}

?>
