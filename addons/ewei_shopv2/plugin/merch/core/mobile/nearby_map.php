<?php
class Nearby_map_EweiShopV2Page extends PluginMobilePage  
{
	public function main() 
	{
		global $_W;
		global $_GPC;
		 $store = pdo_fetchall('select id,lat,lng,merchname from ' . tablename('ewei_shop_merch_user') . ' where uniacid=:uniacid and status = 1 and lat !="" and lng !="" and logo !="" and tel !=""', array(':uniacid' => $_W['uniacid']));
        foreach ($store as $key => &$value) {
        	$url = "http://api.map.baidu.com/geocoder?location=$value[lat],$value[lng]&output=xml&key=28bcdd84fae25699606ffad27f8da77b";
            $name =file($url);
            $ss = trim($name[14]);
            $substr = mb_substr($ss ,6,3,'utf-8');
            $substrone = mb_substr($substr ,0,1,'utf-8');
            $substrtwo = mb_substr($substr ,1,2,'utf-8');
            $pinyinone= get_first_pinyin($substrone);
            $pinyintwo= get_first_pinyin($substrtwo);
            if(100<=$value['id'] && $value['id']<=1000){
               $number='0';
            }else if(10<=$value['id'] && $value['id']<=100){
               $number='00';
            }else if(0<=$value['id'] && $value['id']<=10){
               $number='000';
            }else{
                $number='';
            }
            $value['merchname'] = $pinyinone.$pinyintwo.$number.$value['id'];
            $value['uniacid'] = $_W['uniacid'];
        }
       include $this->template();
	}
    
}
?>