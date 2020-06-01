<?php
use OAuth2\GrantType\RefreshToken;
use OAuth2\GrantType\UserCredentials;
use OAuth2\Request;
use OAuth2\Response;
use OAuth2\Scope;
use OAuth2\Server;
use OAuth2\Storage\Memory;
class Oauth_EweiShopV2Page extends Page
{
	public function _initialize()
	{
		require_once(__DIR__.'/OAuth2/Autoloader.php');
		OAuth2\Autoloader::register();
	}

	private function server()
	{
		require_once __DIR__. "/OAuth2/Autoloader.php";
	    OAuth2\Autoloader::register();
	    $pdo = new \PDO('mysql:host=localhost;dbname=weizan', "weizan", "346ty4sa43454");
	    
	    //创建存储的方式
	    $storage = new \OAuth2\Storage\Pdo($pdo);
	    
	    //创建server
	    $server = new \OAuth2\Server($storage);

	    // 添加 Authorization Code 授予类型
	    $server->addGrantType(new \OAuth2\GrantType\AuthorizationCode($storage));

	    return $server;
	}

	// 授权页面和授权 为了获取access_token
	public function authorize()
	{
	    //获取server对象
	    $server = $this->server();
	    $request = \OAuth2\Request::createFromGlobals();
	    $response = new \OAuth2\Response();

	    // 验证 authorize request
	    // 这里会验证client_id，redirect_uri等参数和client是否有scope
	    // 设置回调时的提示参数
	    if (!$server->validateAuthorizeRequest($request, $response)) {
	    	$pdo = $server->getStorage('client');
	        //获取oauth_clients表的对应的client应用的数据
	        $clientid = $request->query('client_id');
	        if(empty($clientid)){
	        	$clientid = $request->request('client_id');
	        }
	        $clientInfo = $pdo->getClientDetails($clientid);

	        $error_description = $response->parameters;
	        $location = $clientInfo['redirect_uri'].'?error=access_denied&error_description='.$error_description['error_description'];
	        $response->setHttpHeaders(['Cache-Control'=>'no-store','Location'=>$location]);
	        if($_SERVER['REMOTE_ADDR']=='59.42.236.23'){
	        	var_dump($response->getHttpHeader('Location'));die;
	        }
	        $response->send();
	        die;
	    }
	    $state = $request->query('state');
	    if(empty($state)){
	    	$state = $request->request('state');
	    }
	    // 显示授权登录页面
	    if (empty($_POST['username']) && empty($_POST['pass'])) {
	        //获取client类型的storage getClientUserDetails
	        //不过这里我们在server里设置了storage，其实都是一样的storage->pdo.mysql
	        $pdo = $server->getStorage('client');
	        //获取oauth_clients表的对应的client应用的数据
	        $clientInfo = $pdo->getClientDetails($request->query('client_id'));

	        include $this->template('api/oauth/authorize');
	        exit();
	    }

	    //深绘回掉地址
	    $redirect_uri = $request->request('redirect_uri');
	    if(empty($_POST['isAgree'])){
	    	$is_authorized = true;
		    // 当然这部分常规是基于自己现有的帐号系统验证
		    if (!$this->checkLogin($request)) {
		        $is_authorized = false;
		    }

		    // 这里是授权获取code，并拼接Location地址返回相应
		    // Location的地址类似： http://s100.kemanduo.cc
		    // 这里的$uid不是上面oauth_users表的uid, 是自己系统里的帐号的id，你也可以省略该参数
		    $server->handleAuthorizeRequest($request, $response, $is_authorized, $uid);
		    
	       	if ($is_authorized) {
	        	// 这里会创建Location跳转，你可以直接获取相关的跳转url，查看生成的code，用于debug
	        	$code = substr($response->getHttpHeader('Location'), strpos($response->getHttpHeader('Location'), 'code=')+5, 40);
	        	$location = $redirect_uri.'?code='.$code.'&state='.$state;
	        	$response->setHttpHeaders(['Cache-Control'=>'no-store','Location'=>$location]);
	       	} else {
	       		//账号密码有误时，需要修改返回数据
	       		$arr = ['Cache-Control'=>'no-store','error_description'=>'登录信息与密码不匹配'];
	        	$response->setParameters($arr);
	        	$location = $redirect_uri.'?error=access_denied&error_description=登录信息与密码不匹配';
	        	$response->setHttpHeaders(['Cache-Control'=>'no-store','Location'=>$location]);
	       	}
	       	if($_SERVER['REMOTE_ADDR']=='59.42.236.23' || $_SERVER['REMOTE_ADDR']=='59.42.237.248'){
				var_dump($response->getHttpHeader('Location'));die;
			}
	       	// var_dump($response->getHttpHeader('Location'));die;
		    $response->send();
	    } else {
	    	//取消授权是返回的数据
	    	$location = $redirect_uri.'?error=access_denied&error_description=授权失败';
	        $response->setHttpHeaders(['Cache-Control'=>'no-store','Location'=>$location]);
	        // var_dump($response->getHttpHeader('Location'));die;
	    	$response->send();
	    }
		    
	}

	/**
	 * 具体基于自己现有的帐号系统验证
	 * @param $request
	 * @return bool
	 */
	private function checkLogin($request)
	{
		$sql = 'SELECT * FROM `oauth_users` where username="'.$request->request('username').'" and password="'.$request->request('pass').'"';
		$result = pdo_fetch($sql);
	    if (!$result) {
	        return $uid = false;
	    }

	    return $uid = true;
	}

	// 生成并返回token
	public function token()
	{
	    $server = $this->server();
        /** @var Response $response */
        $response = $server->handleTokenRequest(\OAuth2\Request::createFromGlobals());
        $result = $response->getParameters();
        exit(json_encode($result));
	}

	// 模拟回调测试
	// 客户端回调，来自server端的Location跳转到此
	// 此处会携带上code和你自定义的state
	public function cb()
	{
		require_once(__DIR__.'/OAuth2/Autoloader.php');
		OAuth2\Autoloader::register();

	    $request = \OAuth2\Request::createFromGlobals();
	    echo "<pre>";
	    var_dump($request);
	}

	//生成单位为毫秒的时间戳
	public function msectime() {
		list($msec, $sec) = explode(' ', microtime());
		$msectime = (float)sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);
		return $msectime;
	}

	//检测请求类型
	public function checktokentype()
	{
		global $_W;
		global $_GPC;
		require_once(__DIR__.'/OAuth2/Autoloader.php');
		OAuth2\Autoloader::register();
		$response = new \OAuth2\Response();
	    $request = \OAuth2\Request::createFromGlobals();
		if(empty($_GPC['client_id'])){
			exit('{"error":"invalid_request","error_description":"This client is invalid or must authenticate using a client secret"}');
		}
		if(!$_SERVER['REQUEST_METHOD'] == 'POST'){
			exit('{"error":"invalid_request","error_description":"Request must be post"}');
		}

		file_put_contents('s100posttype.txt', var_export($_REQUEST,true).date('Ymd H:i:s')."\r\n",FILE_APPEND);
		if($_REQUEST['grant_type'] == 'authorization_code'){
			$url = "http://s100.kemanduo.cc/app/index.php?i=4&c=entry&m=ewei_shopv2&do=mobile&r=api.oauth.token";
		    $data = [
		        'grant_type' => 'authorization_code',
		        'code' => $_REQUEST['code'],
		        'client_id' => $_REQUEST['client_id'],
		        'client_secret' => $_REQUEST['client_secret'],
		        'redirect_uri' => 'http://standalone.deepdraw.biz'
		    ];
		    // file_put_contents('s100apidata.txt', var_export($data,true).date('Ymd H:i:s')."\r\n",FILE_APPEND);
		    //todo 自定义的处理判断
		    $state = $_REQUEST['state'];

		    $tokendata = $this->http_post($url, $data);
		    $res = json_decode($tokendata);
		    if(!empty($res->error)){
		    	exit($tokendata);
		    } else {
		    	$result['access_token'] = $res->access_token;
			    $result['code'] = 0;
			    $result['expires_in'] = $res->expires_in;
			    $result['refresh_token'] = $res->refresh_token;
			    $result['time'] = $this->msectime();
			    $result['token_type'] = '';
			    $result['uid'] = 1;
			    $result['user_nick'] = 'missshine';
			    $datas = json_encode($result);
			    exit($datas);
		    }

		}
		if($_REQUEST['grant_type'] == 'refresh_token'){
			$url = "http://s100.kemanduo.cc/app/index.php?i=4&c=entry&m=ewei_shopv2&do=mobile&r=api.oauth.refresh_token";
		    $data = [
		        'grant_type' => $_REQUEST['grant_type'],
		        'refresh_token' => $_REQUEST['refresh_token'],
		        'client_id' => $_REQUEST['client_id'],
		        'client_secret' => $_REQUEST['client_secret']
		    ];

		    $state = $_REQUEST['state'];
		    $location = $location.'?state='.$state;

		    $responsedata = ihttp_post($url, $data);

		    $res = json_decode($responsedata['content']);
		    if($res->error){
		    	exit($responsedata['content']);
		    } else {
		    	$result['access_token'] = $res->access_token;
			    $result['code'] = 0;
			    $result['expires_in'] = $res->expires_in;
			    $result['refresh_token'] = $res->refresh_token;
			    $result['time'] = $this->msectime();
			    $result['token_type'] = '';
			    $result['uid'] = 1;
			    $result['user_nick'] = 'missshine';
			    exit(json_encode($result));
		    }

		}
		if(empty($_REQUEST['grant_type'])){
			file_put_contents('s100tokenerror.txt', var_export($_REQUEST,true).date('Ymd H:i:s')."\r\n",FILE_APPEND);
			exit('{"error":"invalid_request","error_description":"No grant type was found in the request"}');
		}
	}
	
	//获取token
	public function getOauthTokenData()
	{
		require_once(__DIR__.'/OAuth2/Autoloader.php');
		OAuth2\Autoloader::register();
		$response = new \OAuth2\Response();
	    $request = \OAuth2\Request::createFromGlobals();
	    $url = "http://s100.kemanduo.cc/app/index.php?i=4&c=entry&m=ewei_shopv2&do=mobile&r=api.oauth.token";
	    $data = [
	        'grant_type' => $request->query('grant_type'),
	        'code' => $request->query('code'),
	        'client_id' => $request->query('client_id'),
	        'client_secret' => $request->query('client_secret'),
	        'redirect_uri' => $request->query('redirect_uri')
	    ];

	    //todo 自定义的处理判断
	    $state = $request->query('state');
	    // return $state;
	    $location = $request->query('redirect_uri');
	    $location = $location.'?state='.$state;

	    $tokendata = $this->http_post($url, $data);
	    $res = json_decode($tokendata);
	    if(!empty($res->error)){
	    	return $tokendata;
	    	// $this->http_post($location,$tokendata);
	    } else {
	    	$result['access_token'] = $res->access_token;
		    $result['code'] = 0;
		    $result['expires_in'] = $res->expires_in;
		    $result['refresh_token'] = $res->refresh_token;
		    $result['time'] = $this->msectime();
		    $result['token_type'] = '';
		    $result['uid'] = 1;
		    $result['user_nick'] = 'DEEPDRAW';
		    $datas = json_encode($result);
		    return $datas;
	    }
	    // $this->http_post($location, $datas);
	}

	 private function http_post($url, $param = '', $post_file = false)
    {
        $oCurl = curl_init();
        if (stripos($url, 'https://') !== false) {
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
        }
        if (is_string($param) || $post_file) {
            $strPOST = $param;
        } else {
            $aPOST = array();
            foreach ($param as $key => $val) {
                $aPOST[] = $key.'='.urlencode($val);
            }
            $strPOST = join('&', $aPOST);
        }
        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
        if ($strPOST != '') {
            curl_setopt($oCurl, CURLOPT_POST, true);
            curl_setopt($oCurl, CURLOPT_POSTFIELDS, $strPOST);
        }
        $sContent = curl_exec($oCurl);

        return $sContent;
    }
    /**
     * 获取一个Oauth对象，里面设置范围；两种授权方式password,refresh_token
     * @return Server
     */
    private function getServer()
    {
        require_once(__DIR__.'/OAuth2/Autoloader.php');
		OAuth2\Autoloader::register();
	    $dsn= 'mysql:dbname=weizan;host=localhost';
		$username = 'weizan';
		$password = '346ty4sa43454';
	    //创建存储的方式
	    $storage = new OAuth2\Storage\Pdo(array('dsn' => $dsn, 'username' => $username, 'password' => $password));
	    $server = new \OAuth2\Server($storage);
        //范围
        $defaultScope = 'basic';
        $memory = new Memory(array('default_scope' => $defaultScope));
        $scopeUtil = new Scope($memory);
        $server->setScopeUtil($scopeUtil);

        // 添加 Authorization Code 授予类型
	    $server->addGrantType(new \OAuth2\GrantType\AuthorizationCode($storage));
	    // 客户端证书  
		$server->addGrantType(new OAuth2\GrantType\ClientCredentials($storage));

		// 用户凭据
		$server->addGrantType(new OAuth2\GrantType\UserCredentials($storage));
        return $server;

    }

    // 创建刷新token的server
	private function refresh_token_server()
	{
		require_once __DIR__. "/OAuth2/Autoloader.php";
	    OAuth2\Autoloader::register();
	    $pdo = new \PDO('mysql:host=localhost;dbname=weizan', "weizan", "346ty4sa43454");
	    $storage = new \OAuth2\Storage\Pdo($pdo);

	    $config = [
	        'always_issue_new_refresh_token' => true,
	        'refresh_token_lifetime'         => 2419200,
	    ];

	    $server = new \OAuth2\Server($storage, $config);

	    // 添加一个 RefreshToken 的类型
	    $server->addGrantType(new \OAuth2\GrantType\RefreshToken($storage, [
	        'always_issue_new_refresh_token' => true
	    ]));

	    // 添加一个token的Response
	    $server->addResponseType(new \OAuth2\ResponseType\AccessToken($storage, $storage, [
	        'refresh_token_lifetime' => 2419200,
	    ]));

	    return $server;
	}

	// 刷新token
	public function refresh_token()
	{
	    $server = $this->refresh_token_server();
	    $server->handleTokenRequest(\OAuth2\Request::createFromGlobals())->send();
	    exit();
	}

	//用户端请求刷新token
	public function client_refresh_token()
	{
		$server = $this->server();
	    $request = \OAuth2\Request::createFromGlobals();
	    $response = new \OAuth2\Response();
	    $url = "http://s100.kemanduo.cc/app/index.php?i=4&c=entry&m=ewei_shopv2&do=mobile&r=api.oauth.refresh_token";
	    $data = [
	        'grant_type' => $request->query('grant_type'),
	        'refresh_token' => $request->query('refresh_token'),
	        'client_id' => $request->query('client_id'),
	        'client_secret' => $request->query('client_secret')
	    ];
	    $state = $request->query('state');
	    $location = $location.'?state='.$state;

	    $responsedata = ihttp_post($url, $data);

	    $res = json_decode($responsedata['content']);
	    if($res->error){
	    	exit($responsedata['content']);
	    } else {
	    	$result['access_token'] = $res->access_token;
		    $result['code'] = 0;
		    $result['expires_in'] = $res->expires_in;
		    $result['refresh_token'] = $res->refresh_token;
		    $result['time'] = $this->msectime();
		    $result['token_type'] = '';
		    $result['uid'] = 1;
		    $result['user_nick'] = 'DEEPDRAW';
		    exit(json_encode($result));
	    }
	}

	// 检测资源访问权限
	public function res1()
	{
	    $server = $this->server();
	    $request = \OAuth2\Request::createFromGlobals();
	    // Handle a request to a resource and authenticate the access token
	    //验证访问令牌
	    if (!$server->verifyResourceRequest(\OAuth2\Request::createFromGlobals())) {
	        // $res = $server->getResponse()->send();
	        exit('0');
	    }
	    $token = $server->getAccessTokenData(\OAuth2\Request::createFromGlobals());
	    
	    //权限
	    $scopes = explode(" ", $token['scope']);
	    
	    // todo scope验证规则
	    if (!$this->checkScope('basic', $scopes)) {
	        exit('1');
	    }
	    exit('2');
	}

	// 检测scope（权限）的方法
	private function checkScope($myScope, $scopes)
	{
	    return in_array($myScope, $scopes);
	}

	//产品添加，更新
	public function product()
	{
		global $_W, $_GPC;
		$response = array();
		if(empty($_GPC['method'])){
			$response['request_id'] = uniqid();
			$response['error_response'] = ['code' => 61,'msg' => '没有找到要访问的接口'];
			$response['timestamp'] = $this->msectime();
			exit(json_encode($response));
		}
		if(empty($_GPC['session'])){
			$response['request_id'] = uniqid();
			$response['error_response'] = ['code' => 50,'msg' => 'token为空'];
			$response['timestamp'] = $this->msectime();
			exit(json_encode($response));
		}
		if(empty($_GPC['app_key'])){
			$response['request_id'] = uniqid();
			$response['error_response'] = ['code' => 50,'msg' => 'AppKey为空'];
			$response['timestamp'] = $this->msectime();
			exit(json_encode($response));
		}
		if(empty($_GPC['sign_method'])){
			$response['request_id'] = uniqid();
			$response['error_response'] = ['code' => 50,'msg' => '签名算法为空'];
			$response['timestamp'] = $this->msectime();
			exit(json_encode($response));
		}
		if(empty($_GPC['sign'])){
			$response['request_id'] = uniqid();
			$response['error_response'] = ['code' => 50,'msg' => '签名结果为空'];
			$response['timestamp'] = $this->msectime();
			exit(json_encode($response));
		}
		
		$url = "http://weizannew.1.y01.cn/app/index.php?i=37&c=entry&m=ewei_shopv2&do=mobile&r=api.oauth.oauth.res1";
		$data = ['access_token' => $_GPC['session']];
		$islifetoken = $this->http_post($url, $data);
		if($islifetoken == '0'){
			$response['request_id'] = uniqid();
			$response['error_response'] = ['code' => 63,'msg' => 'token过期'];
			$response['timestamp'] = $this->msectime();
			exit(json_encode($response));

		} else if ($islifetoken == '1'){
			$response['request_id'] = uniqid();
			$response['error_response'] = ['code' => 65,'msg' => '没有权限访问该数据'];
			$response['timestamp'] = $this->msectime();
			exit(json_encode($response));

		}
		// file_put_contents('s100product.txt', var_export($_GPC,true).date('Ymd H:i:s')."\r\n",FILE_APPEND);
		// file_put_contents('s100product.txt', var_export(json_decode(file_get_contents('php://input'),true),true).date('Ymd H:i:s')."\r\n",FILE_APPEND);
		if($_GPC['method'] == 'add.product'){
			//添加产品
			$content = json_decode(file_get_contents('php://input'),true);
			
			$goods = array();
			if(!empty($content['product'])){
				$product = $content['product'];
				$goods['uniacid'] = $_W['uniacid'];
				$goods['goodssn'] = $product['code'];
				$goods['title'] = $product['title'];
				$goods['type'] = 1;
				$goods['createtime'] = time();
				$goods['totalcnf'] = 1;
				$goods['total'] = 1;
				$goods['isrecommand'] = 1;
				$goods['isnew'] = 1;
				$goods['ishot'] = 1;
				$goods['issendfree'] = 1;
				$goods['quality'] = 1;
				$goods['seven'] = 1;
				$goods['ednum'] = 1;
				$goods['province'] = '广东省';
				$goods['city'] = '广州市';
				$goods['thumb'] = 'http://img2.y01.cn/images/4/2019/01/tw1rB61P0Wv067PRrnvm36M1y1ph0b.gif';
				if(!empty($product['salesInfo'])){
					if(!empty($product['salesInfo']['retailPrice'])){
						$goods['marketprice'] = $product['salesInfo']['retailPrice'];
						$goods['productprice'] = $product['salesInfo']['retailPrice'];
					}
					if($product['salesInfo']['retailPrice'] == 'ON_SALE'){
						$goods['status'] == 1;
					} else {
						$goods['status'] == 0;
					}
				}
				pdo_insert('ewei_shop_goods', $goods);
				$goodsid = pdo_insertid();
				if(empty($goodsid)){
					$response['request_id'] = uniqid();
					$response['error_response'] = ['code' => 50,'msg' => '添加失败！'];
					$response['timestamp'] = $this->msectime();
					exit(json_encode($response));
				}
				//产品参数
				$params = array();
				$fieldValues = $product['fieldValues'];
				if(!empty($fieldValues)){
					$i = 1;
					$params[0]['uniacid'] = $_W['uniacid'];
					$params[0]['goodsid'] = $goodsid;
					$params[0]['title'] = '品牌';
					$params[0]['displayorder'] = 0;
					$params[0]['value'] = '米思阳';
					foreach ($fieldValues as $key => $value) {
						if($value['field']['name'] == '质量等级'){
							if(!empty($value['texts'])){
								$params[$i]['uniacid'] = $_W['uniacid'];
								$params[$i]['goodsid'] = $goodsid;
								$params[$i]['title'] = $value['field']['name'];
								$params[$i]['displayorder'] = $i;
								$params[$i]['value'] = $value['texts']['0'];
								$i += 1;
							}
						}
						if($value['field']['name'] == '面料'){
							if(!empty($value['texts'])){
								$params[$i]['uniacid'] = $_W['uniacid'];
								$params[$i]['goodsid'] = $goodsid;
								$params[$i]['title'] = $value['field']['name'];
								$params[$i]['displayorder'] = $i;
								$params[$i]['value'] = $value['texts']['0'];
								$i += 1;
							}
						}
						if($value['field']['name'] == '材质'){
							if(!empty($value['texts'])){
								$params[$i]['uniacid'] = $_W['uniacid'];
								$params[$i]['goodsid'] = $goodsid;
								$params[$i]['title'] = $value['field']['name'];
								$params[$i]['displayorder'] = $i;
								$params[$i]['value'] = $value['texts']['0'];
								$i += 1;
							}
						}
						if($value['field']['name'] == '材质成分'){
							if(!empty($value['texts'])){
								$params[$i]['uniacid'] = $_W['uniacid'];
								$params[$i]['goodsid'] = $goodsid;
								$params[$i]['title'] = $value['field']['name'];
								$params[$i]['displayorder'] = $i;
								$params[$i]['value'] = $value['texts']['0'];
								$i += 1;
							}
						}
						if($value['field']['name'] == '成分含量'){
							if(!empty($value['texts'])){
								$params[$i]['uniacid'] = $_W['uniacid'];
								$params[$i]['goodsid'] = $goodsid;
								$params[$i]['title'] = $value['field']['name'];
								$params[$i]['displayorder'] = $i;
								$params[$i]['value'] = $value['texts']['0'];
								$i += 1;
							}
						}
						if($value['field']['name'] == '名称'){
							$texts = $value['texts'];
							if(!empty($texts)){
								pdo_update('ewei_shop_goods',array('subtitle'=>$texts['0'],'shorttitle'=>$texts['0']),array('id'=>$goodsid));
							}
						}
					}
					if(!empty($params)){
						$pcount = count($params);
						for ($i=0; $i < $pcount; $i++) { 
							pdo_insert('ewei_shop_goods_param', $params[$i]);
						}
					}
				}
			} else {
				$response['request_id'] = uniqid();
				$response['error_response'] = ['code' => 50,'msg' => '产品字段参数为空'];
				$response['timestamp'] = $this->msectime();
				exit(json_encode($response));
			}

			//资源图
			if(!empty($content['pictures'])){
				$pictures = $content['pictures'];
				$first_key = 'ALL';
				$i = 0;
				$picturesUrl = array();
				foreach ($pictures[$first_key] as $key => $value) {
					if($key=='HOME'){
						foreach ($value as $ke => $val) {
							if($first_key=='JD'){
								foreach ($val as $kk => $vv) {
									if(!empty($vv['url'])){
										if($i==0){
											$pic = $vv['url'];
										} else {
											$picturesUrl[$i-1] = $vv['url'];
										}
										$i += 1;
									}
								}
							} else {
								if(!empty($val['url'])){
									if($i==0){
										$pic = $val['url'];
									} else {
										$picturesUrl[$i-1] = $val['url'];
									}
									$i += 1;
								}	
							}
						}
					}
				}
				$gthumb['thumb'] = $pic;
				$picturesUrl=array_unique($picturesUrl);
				$gthumb['thumb_url'] = serialize($picturesUrl);
				pdo_update("ewei_shop_goods", $gthumb, array('id' => $goodsid));
			}

			//详情图
			if(!empty($content['vision'])){
				$vision = $content['vision'];
				$i = 0;
				$visions = array();
				$goodscontent = '';
				foreach ($vision as $key => $value) {
					$screenShotSectionUrls = $value['screenShotSectionUrls'];
					foreach ($screenShotSectionUrls as $k => $v) {
						$visions[$i] = $v;
						$goodscontent = $goodscontent."<p><img src='".$v."'/></p>";
						$i +=1;
					}
				}
				$gcontent['content'] = $goodscontent;
				pdo_update("ewei_shop_goods", $gcontent, array('id' => $goodsid));
			}

			//产品库存(这里的多规格库存只适合服装类)
			if(!empty($content['skus'])){
				$skus = $content['skus'];
				pdo_update('ewei_shop_goods',array('hasoption' => '1'),array('uniacid'=>$_W['uniacid'],'id'=>$goodsid));
				$optionsname = ['0' => '颜色','1' => '尺码'];

				$spec1 = array();
				$spec2 = array();
				$i = 0;
				foreach ($skus as $key => $value) {
					$spec1[$i] = $key;
					$j = 0;
					foreach ($value as $k => $v) {
						$spec2[$j] = $k;
						$j += 1;
					}
					$i += 1;
				}
				foreach ($optionsname as $key => $value) {
					$spec = array();
					$spec['uniacid'] = $_W['uniacid'];
					$spec['goodsid'] = $goodsid;
					$spec['title'] = $value;
					$spec['displayorder'] = $key;
					pdo_insert('ewei_shop_goods_spec', $spec);
					$specid = pdo_insertid();
					$speccontent = array();
					if($key == '0'){
						foreach ($spec1 as $k => $v) {
							$specitem['uniacid'] = $_W['uniacid'];
							$specitem['specid'] = $specid;
							$specitem['title'] = $v;
							$specitem['show'] = 1;
							$specitem['displayorder'] = $k;
							pdo_insert('ewei_shop_goods_spec_item', $specitem);
							$speccontent[$k] = pdo_insertid();
							$specoptions[$v] = $speccontent[$k];
						}
						$gspec['content'] = serialize($speccontent);
						pdo_update("ewei_shop_goods_spec", $gspec, array('id' => $specid, 'goodsid' => $goodsid));
						
					} else {
						foreach ($spec2 as $k => $v) {
							$specitem['uniacid'] = $_W['uniacid'];
							$specitem['specid'] = $specid;
							$specitem['title'] = $v;
							$specitem['show'] = 1;
							$specitem['displayorder'] = $k;
							pdo_insert('ewei_shop_goods_spec_item', $specitem);
							$speccontent[$k] = pdo_insertid();
							$specoptions[$v] = $speccontent[$k];
						}
						$gspec['content'] = serialize($speccontent);
						pdo_update("ewei_shop_goods_spec", $gspec, array('id' => $specid, 'goodsid' => $goodsid));
					}
				}
				foreach ($skus as $skey => $svalue) {
					foreach ($svalue as $sk => $sv) {
						$goodsoption['uniacid'] = $_W['uniacid'];
						$goodsoption['goodsid'] = $goodsid;
						$goodsoption['title'] = $skey."+".$sk;
						$goodsoption['marketprice'] = $sv['价格'];
						$goodsoption['stock'] = $sv['数量'];
						$goodsoption['specs'] = $specoptions[$skey]."_".$specoptions[$sk];
						$goodsoption['goodssn'] = $sv['货号'];
						$goodsoption['productsn'] = $sv['商家编码'];
						pdo_insert('ewei_shop_goods_option', $goodsoption);
					}
				}
			}
			$response['request_id'] = uniqid();
			$response['success_response'] = ['code' => $goods['goodssn'],'id' => $goodsid];
			$response['timestamp'] = $this->msectime();
			exit(json_encode($response));

		} else if($_GPC['method'] == 'update.product.details'){
			if(!empty($_GPC['ware_id'])){
				$goodsid = $_GPC['ware_id'];
				$content = json_decode(file_get_contents('php://input'),true);
				$goods = pdo_fetch('select id,status,title,goodssn from' . tablename('ewei_shop_goods') . 'where id=:id and uniacid=:uniacid ', array(':id' => $goodsid, ':uniacid' => $_W['uniacid']));
				if(empty($goods)){
					$response['request_id'] = uniqid();
					$response['error_response'] = ['code' => 53,'msg' => '根据提供的id无法查询到相关产品'];
					$response['timestamp'] = $this->msectime();
					exit(json_encode($response));
				}
				if($goods['goodssn'] != $content['code']){
					$response['request_id'] = uniqid();
					$response['error_response'] = ['code' => 51,'msg' => '该id对应的产品货号与提供的货号不一致，货号不允许修改'];
					$response['timestamp'] = $this->msectime();
					exit(json_encode($response));
				}
				
				if(!empty($content)){
					$goods['title'] = $content['title'];
					$goods['type'] = 1;
					$goods['totalcnf'] = 1;
					if(!empty($content['salesInfo'])){
						if(!empty($content['salesInfo']['retailPrice'])){
							$goods['marketprice'] = $content['salesInfo']['retailPrice'];
							$goods['productprice'] = $content['salesInfo']['retailPrice'];
						}
						if($content['salesInfo']['retailPrice'] == 'ON_SALE'){
							$goods['status'] == 1;
						} else {
							$goods['status'] == 0;
						}
					}
					pdo_update('ewei_shop_goods', $goods,array('id' => $_GPC['ware_id'],'uniacid' => $_W['uniacid']));

					//产品参数
					$params = array();
					$fieldValues = $content['fieldValues'];
					if(!empty($fieldValues)){
						$i = 1;
						foreach ($fieldValues as $key => $value) {
							if($value['field']['name'] == '质量等级'){
								$value['texts'] = ltrim($value['texts'],'"[');
								$value['texts'] = rtrim($value['texts'],']"');
								if(!empty($value['texts'])){
									$params[$i]['title'] = $value['field']['name'];
									$params[$i]['displayorder'] = $i;
									$params[$i]['value'] = $value['texts'];
									$i += 1;
								}
							}
							if($value['field']['name'] == '面料名称'){
								$value['texts'] = ltrim($value['texts'],'"[');
								$value['texts'] = rtrim($value['texts'],']"');
								if(!empty($value['texts'])){
									$params[$i]['title'] = $value['field']['name'];
									$params[$i]['displayorder'] = $i;
									$params[$i]['value'] = $value['texts'];
									$i += 1;
								}
							}
							if($value['field']['name'] == '成分及含量'){
								$value['texts'] = ltrim($value['texts'],'"[');
								$value['texts'] = rtrim($value['texts'],']"');
								if(!empty($value['texts'])){
									$params[$i]['title'] = $value['field']['name'];
									$params[$i]['displayorder'] = $i;
									$params[$i]['value'] = $value['texts'];
									$i += 1;
								}
							}
						}

						if(!empty($params)){
							$pcount = count($params);
							for ($i=1; $i < $pcount+1; $i++) {
								$formerparam = pdo_fetch('select * from ' . tablename('ewei_shop_goods_param') . 'where uniacid=:uniacid and goodsid=:goodsid and title=:title ' ,array(':uniacid' => $_W['uniacid'], ':goodsid' => $goodsid, 'title' => $params[$i]['title']));
								if(empty($formerparam)){
									$params[$i]['uniacid'] = $_W['uniacid'];
									$params[$i]['goodsid'] = $goodsid;
									pdo_insert('ewei_shop_goods_param',$params[$i]);
								} else {
									pdo_update('ewei_shop_goods_param',$params[$i],array('uniacid' => $_W['uniacid'] ,'goodsid' => $goodsid,'title' => $params[$i]['title']));
								}
							}
						}
					}
					$response['request_id'] = uniqid();
					$response['success_response'] = ['code' => $goods['goodssn'],'id' => $goodsid];
					$response['timestamp'] = $this->msectime();
					exit(json_encode($response));
				} else {
					$response['request_id'] = uniqid();
					$response['error_response'] = ['code' => 50,'msg' => '没有找到修改内容'];
					$response['timestamp'] = $this->msectime();
					exit(json_encode($response));
				}
			} else {
				$response['request_id'] = uniqid();
				$response['error_response'] = ['code' => 50,'msg' => 'ID为空'];
				$response['timestamp'] = $this->msectime();
				exit(json_encode($response));
			}

		} else if($_GPC['method'] == 'update.product.pictures'){
			//产品资源图更新
			$goodsid = $_GPC['ware_id'];
			if(!empty($goodsid)){
				$goods = pdo_fetch('select id,status,title from' . tablename('ewei_shop_goods') . 'where id=:id and uniacid=:uniacid ', array(':id' => $goodsid, ':uniacid' => $_W['uniacid']));
				if(!empty($goods)){
					//获取http post 的内容
					$pictures = json_decode(file_get_contents('php://input'),true);
					if(!empty($pictures)){
						$first_key = 'ALL';
						$i = 0;
						$picturesUrl = array();
						foreach ($pictures[$first_key] as $key => $value) {
							if($key=='HOME'){
								foreach ($value as $ke => $val) {
									if($first_key=='JD'){
										foreach ($val as $kk => $vv) {
											if($i==0){
												$pic = $vv['url'];
											} else {
												$picturesUrl[$i-1] = $vv['url'];
											}
											
											$i += 1;
										}
									} else {
										if($i==0){
											$pic = $val['url'];
										} else {
											$picturesUrl[$i-1] = $val['url'];
										}
										
										$i += 1;
									}
								}
							}
						}
						$gthumb['thumb'] = $pic;
						$picturesUrl=array_unique($picturesUrl);
						$gthumb['thumb_url'] = serialize($picturesUrl);
						pdo_update("ewei_shop_goods", $gthumb, array('id' => $goodsid));
					}
					$response['request_id'] = uniqid();
					$response['success_response'] = ['code' => $goods['goodssn'],'id' => $goodsid];
					$response['timestamp'] = $this->msectime();
					exit(json_encode($response));
				} else {
					$response['request_id'] = uniqid();
					$response['error_response'] = ['code' => 53,'msg' => '根据提供的id无法查询到相关产品'];
					$response['timestamp'] = $this->msectime();
					exit(json_encode($response));
				}
			} else {
				$response['request_id'] = uniqid();
				$response['error_response'] = ['code' => 50,'msg' => 'ID为空'];
				$response['timestamp'] = $this->msectime();
				exit(json_encode($response));
			}

		} else if($_GPC['method'] == 'update.product.vision.resource'){
			//产品详情图更新
			$goodsid = $_GPC['ware_id'];
			if(!empty($goodsid)){
				$goods = pdo_fetch('select id,status,title from' . tablename('ewei_shop_goods') . 'where id=:id and uniacid=:uniacid ', array(':id' => $goodsid, ':uniacid' => $_W['uniacid']));
				if(!empty($goods)){
					$vision = json_decode(file_get_contents('php://input'),true);
					$i = 0;
					$visions = array();
					$goodscontent = '';
					foreach ($vision as $key => $value) {
						$screenShotSectionUrls = $value['screenShotSectionUrls'];
						foreach ($screenShotSectionUrls as $k => $v) {
							$visions[$i] = $v;
							$goodscontent = $goodscontent."<p><img src='".$v."'/></p>";
							$i +=1;
						}
					}
					$gcontent['content'] = $goodscontent;
					pdo_update("ewei_shop_goods", $gcontent, array('id' => $goodsid));
					$response['request_id'] = uniqid();
					$response['success_response'] = ['code' => $goods['goodssn'],'id' => $goodsid];
					$response['timestamp'] = $this->msectime();
					exit(json_encode($response));
				} else {
					$response['request_id'] = uniqid();
					$response['error_response'] = ['code' => 53,'msg' => '根据提供的id无法查询到相关产品'];
					$response['timestamp'] = $this->msectime();
					exit(json_encode($response));
				}
			} else {
				$response['request_id'] = uniqid();
				$response['error_response'] = ['code' => 50,'msg' => 'ID为空'];
				$response['timestamp'] = $this->msectime();
				exit(json_encode($response));
			}

		} else if($_GPC['method'] == 'update.product.skus'){
			//更新多规格库存
			$goodsid = $_GPC['ware_id'];
			if(!empty($goodsid)){
				$goods = pdo_fetch('select id,status,title from' . tablename('ewei_shop_goods') . 'where id=:id and uniacid=:uniacid ', array(':id' => $goodsid, ':uniacid' => $_W['uniacid']));
				if(empty($goods)){
					$response['request_id'] = uniqid();
					$response['error_response'] = ['code' => 53,'msg' => '根据提供的id无法查询到相关产品'];
					$response['timestamp'] = $this->msectime();
					exit(json_encode($response));
				}
				$skus = json_decode(file_get_contents('php://input'),true);
				if(!empty($skus)){
					pdo_delete('ewei_shop_goods_spec', array('goodsid' => $goodsid, 'uniacid' => $_W['uniacid']));
					pdo_delete('ewei_shop_goods_option', array('goodsid' => $goodsid, 'uniacid' => $_W['uniacid']));
					pdo_update('ewei_shop_goods',array('hasoption' => '1'),array('uniacid'=>$_W['uniacid'],'id'=>$goodsid));
					$optionsname = ['0' => '颜色','1' => '尺码'];
					$spec1 = array();
					$spec2 = array();
					$i = 0;
					foreach ($skus as $key => $value) {
						$spec1[$i] = $key;
						$j = 0;
						foreach ($value as $k => $v) {
							$spec2[$j] = $k;
							$j += 1;
						}
						$i += 1;
					}
					foreach ($optionsname as $key => $value) {
						$spec = array();
						$spec['uniacid'] = $_W['uniacid'];
						$spec['goodsid'] = $goodsid;
						$spec['title'] = $value;
						$spec['displayorder'] = $key;
						pdo_insert('ewei_shop_goods_spec', $spec);
						$specid = pdo_insertid();
						$speccontent = array();
						if($key == '0'){
							foreach ($spec1 as $k => $v) {
								$specitem['uniacid'] = $_W['uniacid'];
								$specitem['specid'] = $specid;
								$specitem['title'] = $v;
								$specitem['show'] = 1;
								$specitem['displayorder'] = $k;
								pdo_insert('ewei_shop_goods_spec_item', $specitem);
								$speccontent[$k] = pdo_insertid();
								$specoptions[$v] = $speccontent[$k];
							}
							$gspec['content'] = serialize($speccontent);
							pdo_update("ewei_shop_goods_spec", $gspec, array('id' => $specid, 'goodsid' => $goodsid));
							
						} else {
							foreach ($spec2 as $k => $v) {
								$specitem['uniacid'] = $_W['uniacid'];
								$specitem['specid'] = $specid;
								$specitem['title'] = $v;
								$specitem['show'] = 1;
								$specitem['displayorder'] = $k;
								pdo_insert('ewei_shop_goods_spec_item', $specitem);
								$speccontent[$k] = pdo_insertid();
								$specoptions[$v] = $speccontent[$k];
							}
							$gspec['content'] = serialize($speccontent);
							pdo_update("ewei_shop_goods_spec", $gspec, array('id' => $specid, 'goodsid' => $goodsid));
						}
					}
					foreach ($skus as $skey => $svalue) {
						foreach ($svalue as $sk => $sv) {
							$goodsoption['uniacid'] = $_W['uniacid'];
							$goodsoption['goodsid'] = $goodsid;
							$goodsoption['title'] = $skey."+".$sk;
							$goodsoption['marketprice'] = $sv['价格'];
							$goodsoption['stock'] = $sv['数量'];
							$goodsoption['specs'] = $specoptions[$skey]."_".$specoptions[$sk];
							$goodsoption['goodssn'] = $sv['货号'];
							$goodsoption['productsn'] = $sv['条形码'];
							pdo_insert('ewei_shop_goods_option', $goodsoption);
						}
					}
					$response['request_id'] = uniqid();
					$response['success_response'] = ['code' => $goods['goodssn'],'id' => $goodsid];
					$response['timestamp'] = $this->msectime();
					exit(json_encode($response));
				}
					
			} else {
				$response['request_id'] = uniqid();
				$response['error_response'] = ['code' => 50,'msg' => 'ID为空'];
				$response['timestamp'] = $this->msectime();
				exit(json_encode($response));
			}

		} else if($_GPC['method'] == 'find.product'){
			$goodsid = $_GPC['ware_id'];
			$goods = pdo_fetch('select id,status,title from' . tablename('ewei_shop_goods') . 'where id=:id and uniacid=:uniacid ', array(':id' => $goodsid, ':uniacid' => $_W['uniacid']));
			if(!empty($goods)){
				$response['request_id'] = uniqid();
				$response['success_response'] = ['code' => $goods['goodssn'],'id' => $goodsid];
				$response['timestamp'] = $this->msectime();
				exit(json_encode($response));
			} else {
				$response['request_id'] = uniqid();
				$response['error_response'] = ['code' => 53,'msg' => '根据提供的id无法查询到相关产品'];
				$response['timestamp'] = $this->msectime();
				exit(json_encode($response));
			}
		} else {
			$response['request_id'] = uniqid();
			$response['error_response'] = ['code' => 61,'msg' => '没有找到要访问的接口'];
			$response['timestamp'] = $this->msectime();
			exit(json_encode($response));
		}
	}
}