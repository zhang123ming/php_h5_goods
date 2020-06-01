<?php

if (!defined('IN_IA')) {
	exit('Access Denied');
}

require EWEI_SHOPV2_PLUGIN . 'app/core/page_mobile.php';
class Comment_EweiShopV2Page extends AppMobilePage
{
	public function __construct()
	{
		parent::__construct();
		$trade = m('common')->getSysset('trade');

		if (!empty($trade['closecomment'])) {
			app_error(AppError::$OrderCanNotComment);
		}
	}

	
	public function submit()
	{
		global $_W;
		global $_GPC;
		// $arr = [];
		// preg_match_all("/\/face\/\d{1,3}.gif/",$_GPC['message'],$arr);
		// $xiaoxi =explode("#", $_GPC['message']);

		// $msg=implode("#",$xiaoxi);

		// $newarr =array();
		// foreach($arr[0] as $k=>$v){
		// 	$newarr[] = $_W[siteroot]."addons/ewei_shopv2/plugin/bottledoctor/static/images".$v;
		// }

		$openid = $_W['openid'];
		$uniacid = $_W['uniacid'];
		$message = $_GPC['message'];
		$member = m('member')->getMember($openid);
		$communicationgrade =  $member['communicationgrade'];
		$categoryid=$_GPC['categoryid'];		
		$communicationtype=$_GPC['communicationtype'];
		//是否需要核审
		$sql = "select auditing from ".tablename('ewei_shop_bottledoctor_category').' where  id=:id';
		$category_list = pdo_fetch($sql,array(':id'=>$categoryid));		
		if($category_list["auditing"]==1){
			$enabled = 1;
		}else{
			$enabled = 0;
		}

		$comment = array('uniacid' => $uniacid, 'enabled' => $enabled ,'categoryid' => $categoryid , 'communicationtype' => $_GPC['communicationtype'] ,'openid' => $openid);
		if ($communicationgrade==1 && $_GPC['inans'] == true) {
            if($_GPC['communicationid']!=''){
            	if($_GPC['message']!=''||$_GPC['voice']!=''|| $_GPC['imgs']!=''){
					$comment['answer'] = $_GPC['message'];
					$comment['voice'] = $_GPC['voice'];
					$comment['voiceTime'] = $_GPC['TimeIng'];
					$comment['docimages'] = is_array($_GPC['imgs']) ? iserializer($_GPC['imgs']) : iserializer(array());
					$comment['docheadimgurl'] = $member['avatar'];
					$comment['communicationid'] = $_GPC['communicationid'];
					$comment['docname'] = $member['nickname'];
					$comment['createTime'] = time();
					pdo_insert('ewei_shop_bottledoctor_answer', $comment);
            	}
            }            
		}else{
			if($_GPC['message']!=''|| $_GPC['imgs']!=''){
				$comment['images'] = is_array($_GPC['imgs']) ? iserializer($_GPC['imgs']) : iserializer(array());
				$comment['descripTion'] = $_GPC['message'];
				$comment['emoticon'] = $_GPC['emoticon'];
				$comment['voice'] = $_GPC['voice'];
				$comment['headimgurl'] = $member['avatar'];
				$comment['name'] = $member['nickname'];
				$comment['createTime'] = time();
				pdo_insert('ewei_shop_bottledoctor_communion', $comment);
			}
			
		}	
		$id = pdo_insertid();	

		app_json(array('communicationgrade' =>$member['communicationgrade'],'id'=>$id,'auditing'=>$category_list["auditing"]));
	}

	public function search()
	{
		global $_W;
		global $_GPC;
		$openid = $_W['openid'];
		$uniacid = $_W['uniacid'];	
		$condition = 'uniacid = :uniacid and enabled=1 and communicationtype=0';
		$params = array(':uniacid' => $_W['uniacid']);
		$docimages=array();
		if (!empty($_GPC['content'])) {
			$_GPC['content'] = trim($_GPC['content']);
			$condition .= ' and ( descripTion like :content)';
			$params[':content'] = '%' . $_GPC['content'] . '%';
		}
		$sql = 'SELECT * FROM ' . tablename('ewei_shop_bottledoctor_communion') . ' where ' . $condition . ' ORDER BY `createTime` ASC';
		$list = pdo_fetchall($sql, $params);
		foreach($list as &$v){
			$v['answer']=pdo_fetchall('SELECT docimages,docheadimgurl,answer from ' . tablename('ewei_shop_bottledoctor_answer').'where communicationid=:communicationid and uniacid=:uniacid',array(':communicationid'=>$v['id'],':uniacid'=>$_W['uniacid']));
			foreach ($v['answer'] as $key => &$index) {
				$index['docimages'] = unserialize($index['docimages']);				
			}
			$v['images'] = unserialize($v['images']);
			$v['createTime'] = date('Y/m/d H:i', $v['createTime']);
		
		}
		unset($v);
		app_json(array('list' =>$list ));
	}
  
  
    public function getinfo_mod()
    {
  		global $_W;
		global $_GPC;
		$pindex = max(1,$_GPC['pindex']);
	 	$psize = 20;
		$openid = $_GPC['openid'];
		$uniacid = $_W['uniacid'];
		$member = m('member')->getMember($openid);
		$condition = 'uniacid = :uniacid and communicationtype=0';
		$sql = 'SELECT * FROM ' . tablename('ewei_shop_bottledoctor_communion') . ' where ' . $condition . ' ORDER BY createTime desc limit '.($pindex-1)*$psize.','.$psize;
		$params = array(':uniacid' => $_W['uniacid']);
		$list = pdo_fetchall($sql,$params);

		if($_GPC['desc']!='true'){
			sort($list);	
		}

		foreach($list as &$v){
			$v['images'] = unserialize($v['images']);
			$v['createTime'] = date('Y/m/d H:i', $v['createTime']);			
			
		}

		$decoset = pdo_fetch("select * from".tablename('ewei_shop_bottledoctor_decodeset').'where uniacid="'.$uniacid.'"');

		unset($v);
		app_json(array('list' =>$list,'openid'=>$openid,'decoset' => $decoset,'member'=>$member,'pindex'=>$pindex));
  	}

	 public function getinfo_doc()
	{
		global $_W;
		global $_GPC;
		$id=$_GPC['id'];		
		$openid = $_GPC['openid'];
		$uniacid = $_W['uniacid'];
		$member = m('member')->getMember($openid);

		$memberlist = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_member') . ' WHERE  uniacid='. $_W['uniacid'].' and openid="'.$openid.'"');
		$isin=explode(',', $memberlist['categoryid']);

		if (in_array($_GPC['id'], $isin)) {
			$insan=true;
		}else if(!in_array($_GPC['id'], $isin)){
			$insan=false;
		}
		$pindex = max(1,$_GPC['pindex']);
	 	$psize = 20;
		$condition = 'uniacid = :uniacid and categoryid = :id';
		$params = array(':uniacid' => $_W['uniacid'],':id' => $id);

		if ($_GPC['check'] ==1) {
			$condition .= ' and openid = :openid';
			$params[':openid'] = $openid;
		}

		$sql = 'SELECT id, images,headimgurl,descripTion,name,createTime,openid,enabled FROM ' . tablename('ewei_shop_bottledoctor_communion') . ' where ' . $condition . ' ORDER BY createTime desc limit '.($pindex-1)*$psize.','.$psize;

		$list = pdo_fetchall($sql, $params);
		if($_GPC['desc']!='true'){
			sort($list);	
		}
		$docimages=array();
		foreach($list as &$v){
			$v['answer'] = pdo_fetchall('SELECT docimages,docheadimgurl,answer,voice,voiceTime,id,mid,openid,createTime from '.tablename('ewei_shop_bottledoctor_answer') .' where communicationid=:communicationid and uniacid=:uniacid ORDER BY id ASC',array(':uniacid'=>$_W['uniacid'],':communicationid'=>$v['id']));	
			
			foreach ($v['answer'] as $key => &$index) {
				$index['docimages'] = unserialize($index['docimages']);				
				$index['voiceTime'] = intval($index['voiceTime']/1000);
				$index['openid'] = trim($index['openid']);
				$index['createTime'] = date('Y/m/d H:i', $index['createTime']);
				if($index['voice'] && $_GPC['openid']!=$index['openid']){
					if ($index['mid']) {
					$indexArr=explode(',', $index['mid']);					
					if (in_array($member['id'], $indexArr)) {
						$index['red']=false;
					}
					}else{					
						$index['red']=true;
					}
				}
			}
			$v['images'] = unserialize($v['images']);
			$v['createTime'] = date('Y/m/d H:i', $v['createTime']);	
			$v['communicationgrade'] = $member['communicationgrade'];
			if (in_array($_GPC['id'], $isin)) {
					$v['isinid']=true;
				}else if(!in_array($_GPC['id'], $isin)){
					$v['isinid']=false;
				}
				
		}	
		$decoset = pdo_fetch("select * from".tablename('ewei_shop_bottledoctor_decodeset').'where uniacid="'.$uniacid.'"');		
		unset($v);
		app_json(array('list' =>$list,'member'=>$member,'openid'=>$openid,'isinid'=>$isinid,'insan' => $insan,'decoset' => $decoset,'pindex'=>$pindex));

	}

	public function see()
	{
		global $_W;
		global $_GPC;
		$openid = $_W['openid'];
		$member = m('member')->getMember($openid);

		$condition = 'uniacid = :uniacid and openid=:openid and enabled=1';

		$params = array(':uniacid' => $_W['uniacid'],':openid' => $openid);
		$sql = 'SELECT id, images,headimgurl,descripTion,name,createTime FROM ' . tablename('ewei_shop_bottledoctor_communion') . ' where ' . $condition . ' ORDER BY createTime ASC';
		$list = pdo_fetchall($sql, $params);
		$docimages=array();
		foreach($list as &$v){
			$v['answer'] = pdo_fetchall('SELECT docimages,docheadimgurl,answer from '.tablename('ewei_shop_bottledoctor_answer') .' where communicationid=:communicationid and uniacid=:uniacid and openid = :openid',array(':uniacid'=>$_W['uniacid'],':openid'=>$openid));	
			foreach ($v['answer'] as $key => &$index) {
				$index['docimages'] = unserialize($index['docimages']);				
				
			}

			$v['images'] = unserialize($v['images']);
			$v['createTime'] = date('Y/m/d H:i', $v['createTime']);		
		}
		unset($v);
		app_json(array('list' =>$list));

	}

	public function record(){
	  	global $_W;
		global $_GPC;
		$openid = $_W['openid'];
		$member = m('member')->getMember($openid);
		$ansid = $_GPC['ansid'];
		$mid = $member['id'];
		$uniacid = $_W['uniacid'];
		$arr = pdo_fetch("select mid from".tablename('ewei_shop_bottledoctor_answer').'where id="'.$ansid.'"')['mid'];
		if($arr&&substr_count($arr,$mid)<=0){
			$mid = $arr.",".$mid;
			$data = array('id' => $ansid, 'mid' => $mid, 'uniacid' => $uniacid);
			pdo_update('ewei_shop_bottledoctor_answer', $data, array('id' => $ansid));
		}elseif(empty($arr)){
			$data = array('id' => $ansid, 'mid' => $mid, 'uniacid' => $uniacid);
			pdo_update('ewei_shop_bottledoctor_answer', $data, array('id' => $ansid));
		}

	  }

	public function delete(){
		global $_W;
		global $_GPC;	
		$ansid = intval($_GPC['ansid']);
		$comid = intval($_GPC['comid']);	
		 if ($ansid) {
		 	pdo_delete('ewei_shop_bottledoctor_answer', array('id' => $ansid));
		 }
		if ($comid) {
		 	pdo_delete('ewei_shop_bottledoctor_communion', array('id' => $comid));
		 	pdo_delete('ewei_shop_bottledoctor_answer', array('communicationid' => $comid));
		 }
		show_json(1, array('url' => referer()));		
	}
}

?>
