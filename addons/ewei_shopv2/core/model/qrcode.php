<?php
class Qrcode_EweiShopV2Model
{
	/**
     * 商城二维码
     * @global type $_W
     * @param type $mid
     * @return string
     */
	public function createShopQrcode($mid = 0, $posterid = 0,$url='')
	{
		global $_W;
		global $_GPC;
		$path = IA_ROOT . '/addons/ewei_shopv2/data/qrcode/' . $_W['uniacid'] . '/';
		
		if (!is_dir($path)) {
			load()->func('file');
			mkdirs($path);
		}
		if(empty($url)){
			$url = mobileUrl('', array('mid' => $mid), true);
		}

		if (!empty($posterid)) {
			$url .= '&posterid=' . $posterid;
		}

		$file = 'shop_qrcode_' . $posterid . '_' . $mid . '.png';
		$qrcode_file = $path . $file;

		if (!is_file($qrcode_file)) {
			require_once IA_ROOT . '/framework/library/qrcode/phpqrcode.php';
			QRcode::png($url, $qrcode_file, QR_ECLEVEL_L, 4);
		}

		return $_W['siteroot'] . 'addons/ewei_shopv2/data/qrcode/' . $_W['uniacid'] . '/' . $file;
	}

	/**
     * 产品二维码
     * @global type $_W
     * @param type $goodsid
     * @return string
     */
	public function createGoodsQrcode($mid = 0, $goodsid = 0, $posterid = 0)
	{
		global $_W;
		global $_GPC;
		$path = IA_ROOT . '/addons/ewei_shopv2/data/qrcode/' . $_W['uniacid'];

		if (!is_dir($path)) {
			load()->func('file');
			mkdirs($path);
		}

		$url = mobileUrl('goods/detail', array('id' => $goodsid, 'mid' => $mid), true);

		if (!empty($posterid)) {
			$url .= '&posterid=' . $posterid;
		}

		$file = 'goods_qrcode_' . $posterid . '_' . $mid . '_' . $goodsid . '.png';
		$qrcode_file = $path . '/' . $file;

		if (!is_file($qrcode_file)) {
			require_once IA_ROOT . '/framework/library/qrcode/phpqrcode.php';
			QRcode::png($url, $qrcode_file, QR_ECLEVEL_L, 4);
		}

		return $_W['siteroot'] . 'addons/ewei_shopv2/data/qrcode/' . $_W['uniacid'] . '/' . $file;
	}

	public function createQrcode($url,$name='')
	{
		global $_W;
		global $_GPC;
		$path = IA_ROOT . '/addons/ewei_shopv2/data/qrcode/' . $_W['uniacid'] . '/';

		if (!is_dir($path)) {
			load()->func('file');
			mkdirs($path);
		}

		$file = md5(base64_encode($url)) . '.jpg';
		$qrcode_file = $path . $file;

		if (!is_file($qrcode_file)) {
			require_once IA_ROOT . '/framework/library/qrcode/phpqrcode.php';
			QRcode::png($url, $qrcode_file, QR_ECLEVEL_L, 4,4, false,$name);
		}

		return $_W['siteroot'] . 'addons/ewei_shopv2/data/qrcode/' . $_W['uniacid'] . '/' . $file;
	}


	public function createWxCode($url,$logo='')
	{
		global $_W;
		global $_GPC;
		$path = IA_ROOT . '/addons/ewei_shopv2/data/merchqrcode/' . $_W['uniacid'] . '/';

		$wxcodepath = $path . 'wxcode/';
		$logopath  =$path . 'logo/';

		if (!is_dir($path)||!is_dir($wxcodepath)||!is_dir($logopath)) {
			load()->func('file');
			mkdirs($path);
			mkdirs($wxcodepath);
			mkdirs($logopath);
		}


		$file = md5(base64_encode($url)) . '.png';
		$qrcode_file = $wxcodepath. $file;              //小程序码路径

		if (!is_file($qrcode_file)) {

			$param=array('path' =>$url ,'width'=>430);
			$qrcode=p('app')->getCodelimit($param);


			if (!(is_error($qrcode))) {
				file_put_contents($qrcode_file,$qrcode);
			}else{
				return false;
			}
		}
		

		if ($logo!='') {
			
			$logofile_name = md5(base64_encode($logo)).".png";
			$logo_file = $logopath.$logofile_name;      //logo存放路径
			if (!is_file($logo_file)) {
				$logoimg = $this->yuan_img($logo);
				imagepng($logoimg,$logo_file);
				imagedestroy($logoimg);


				$target_im = imagecreatetruecolor(192,192);		//创建一个新的画布（缩放后的），从左上角开始填充透明背景	
				imagesavealpha($target_im, true); 
				$trans_colour = imagecolorallocatealpha($target_im, 0, 0, 0, 127); 
				imagefill($target_im, 0, 0, $trans_colour);                
				 
				$o_image = imagecreatefrompng($logo_file);   //获取上文已保存的修改之后头像的内容

				list($wwidth,$wheight,$wtype)=getimagesize($logo_file);
				imagecopyresampled($target_im,$o_image, 0, 0,0, 0, 192, 192, $wwidth, $wheight);	
				imagepng($target_im,$logo_file);
				imagedestroy($target_im);
				
			}
			
			$composefile_name=md5(json_encode(array('url'=>$url,'logo'=>$logo))).".png";
			$compose_file = $path.$composefile_name;

			if (!is_file($compose_file)) {
				$qrcode_file=$this->create_pic_watermark($qrcode_file,$logo_file,"center",$compose_file);
			}
			
			return $_W['siteroot'] . '/addons/ewei_shopv2/data/merchqrcode/' . $_W['uniacid'] . '/'.$composefile_name;
		}else{
			return $_W['siteroot'] . '/addons/ewei_shopv2/data/merchqrcode/' . $_W['uniacid'] . '/wxcode/'.$file;

		}
	}


	public function yuan_img($imgpath) {
		$ext     = pathinfo($imgpath);
		$src_img = null;
		switch ($ext['extension']) {
		case 'jpg':
			$src_img = imagecreatefromjpeg($imgpath);
			break;
		case 'png':
			$src_img = imagecreatefrompng($imgpath);
			break;
		}
		$wh  = getimagesize($imgpath);
		$w   = $wh[0];
		$h   = $wh[1];
		$w   = min($w, $h);
		$h   = $w;
		$img = imagecreatetruecolor($w, $h);
		//这一句一定要有
		imagesavealpha($img, true);
		//拾取一个完全透明的颜色,最后一个参数127为全透明
		$bg = imagecolorallocatealpha($img, 255, 255, 255, 127);
		imagefill($img, 0, 0, $bg);
		$r   = $w / 2; //圆半径
		$y_x = $r; //圆心X坐标
		$y_y = $r; //圆心Y坐标
		for ($x = 0; $x < $w; $x++) {
			for ($y = 0; $y < $h; $y++) {
				$rgbColor = imagecolorat($src_img, $x, $y);
				if (((($x - $r) * ($x - $r) + ($y - $r) * ($y - $r)) < ($r * $r))) {
					imagesetpixel($img, $x, $y, $rgbColor);
				}
			}
		}
		return $img;
	}

	function create_pic_watermark($dest_image,$watermark,$locate,$filepath){
	    list($dwidth,$dheight,$dtype)=getimagesize($dest_image);
	    list($wwidth,$wheight,$wtype)=getimagesize($watermark);
	    $types=array(1 => "GIF",2 => "JPEG",3 => "PNG",
	        4 => "SWF",5 => "PSD",6 => "BMP",
	        7 => "TIFF",8 => "TIFF",9 => "JPC",
	        10 => "JP2",11 => "JPX",12 => "JB2",
	        13 => "SWC",14 => "IFF",15 => "WBMP",16 => "XBM");
	    $dtype=strtolower($types[$dtype]);//原图类型
	    $wtype=strtolower($types[$wtype]);//水印图片类型
	    $created="imagecreatefrom".$dtype;
	    $createw="imagecreatefrom".$wtype;
	    $imgd=$created($dest_image);
	    $imgw=$createw($watermark);
	    switch($locate){
	        case 'center':
	           $x= ($dwidth - $wwidth) / 2;//组合之后logo左上角所在坐标点
	           $y= ($dheight-$wheight) / 2;//组合之后logo左上角所在坐标点
	            break;
	        case 'left_buttom':
	            $x=1;
	            $y=($dheight-$wheight-2);
	            break;
	        case 'right_buttom':
	            $x=($dwidth-$wwidth-1);
	            $y=($dheight-$wheight-2);
	            break;
	        default:
	            die("未指定水印位置!");
	            break;
	    }
	    imagecopy($imgd,$imgw,$x,$y,0,0, $wwidth,$wheight);
	    $save="image".$dtype;
	    //保存到服务器
	  
	    imagepng($imgd,$filepath); //保存
	    imagedestroy($imgw);
	    imagedestroy($imgd);
	    //传回处理好的图片
	    return $filepath;
	}
}

if (!defined('IN_IA')) {
	exit('Access Denied');
}

?>
