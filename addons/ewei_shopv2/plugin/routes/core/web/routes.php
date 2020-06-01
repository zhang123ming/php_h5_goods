<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}
class Routes_EweiShopV2Page extends PluginWebPage
{
	public function main(){
		global $_W;$_GPC;
		$routes = pdo_fetchAll('SELECT * FROM '.tablename('ewei_route')." WHERE uniacid=:uniacid",array(':uniacid'=>$_W['uniacid']));
		include $this->template("routes/routes/routes");
	}


	public function add()
	{
		$this->post();
	}

	public function post($routeId){
		global $_W,$_GPC;
		if(!empty($routeId)){
			$route = pdo_fetch('SELECT * FROM '.tablename('ewei_route')." WHERE uniacid=:uniacid and id=:id",array(':uniacid'=>$_W['uniacid'],':id'=>$routeId));
			$stations = pdo_fetchAll('SELECT * FROM '.tablename('ewei_route_station')." WHERE uniacid=:uniacid and route_id=:id",array(':uniacid'=>$_W['uniacid'],':id'=>$routeId));
		}
		if($_W['ispost']){
			$routeData = array();
			$routeData['uniacid'] = $_W['uniacid'];
			$routeData['route_name'] = $_GPC['route_name'];
			$routeData['route_length'] = $_GPC['route_length'];
			$routeData['first_run_time'] = $_GPC['first_run_time'];
			$routeData['last_run_time'] = $_GPC['last_run_time'];
			$routeData['shop_time'] = $_GPC['shop_time'];
			if(empty($routeId)){
				pdo_insert('ewei_route',$routeData);
				$routeId = pdo_insertid();
			}else{
				pdo_update('ewei_route',$routeData,array('id'=>$routeId));
			}
			$stationData = array();
			$station_name = $_GPC['station_name'];
			$station_length = $_GPC['station_length'];
			$reach_time = $_GPC['reach_time'];
			$lng = $_GPC['map']['lng'];
			$lat = $_GPC['map']['lat'];
			$addresss = $_GPC['addresss'];
			$stationCount = count($station_name);
			if($stationCount){
				for($i=0;$i<$stationCount;$i++){
					$stationData[$i]['route_id'] = $routeId;
					$stationData[$i]['uniacid'] = $_W['uniacid'];
					$stationData[$i]['station_name'] = $station_name[$i];
					$stationData[$i]['station_length'] = $station_length[$i];
					$stationData[$i]['reach_time'] = $reach_time[$i];
					$stationData[$i]['lng'] = $lng[$i+1];
					$stationData[$i]['lat'] = $lat[$i+1];
					$stationData[$i]['address'] = $addresss[$i];
					pdo_insert('ewei_route_station',$stationData[$i]);
				}
			}

		}
		include $this->template("routes/routes/post");
	}

	public function edit(){
		global $_W,$_GPC;
		$this->post($_GPC['id']);
	}


	public function tpl(){
		global $_W,$_GPC;
		include $this->template('routes/routes/tpl_station');
	}
}

?>