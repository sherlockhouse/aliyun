<?php
!defined('P_W') && exit('Forbidden');

require_once(R_P.'require/posthost.php');

!$admintype && $admintype = 'appset';

$host = $pwServer['HTTP_HOST'];

$appclient = L::loadClass('AppClient');
$islocalhost = $appclient->isLocalhost($host);

if ($islocalhost && !in_array($admintype,array('appset','cnzz'))) {/*�ж��Ƿ񱾵ص�ַ*/
	adminmsg('localhost_error',"$basename&admintype=appset");
}

if (!$db_siteappkey && !in_array($admintype,array('link','register','cnzz'))) {/*��δע��վ����������ת����ҳ*/
	$admintype = 'appset';
}

if ($db_siteappkey) $msg = $appclient->getUrlChangedMsg();/*�ж�url��ַ�Ƿ�䶯*/

if ($admintype == 'appset') {

	if($adminitem == 'link') {
		S::gp(array('step'), 'P', 2);
		if ($step == 2) {
			S::gp(array('username','password'), 'P');
			
			$siteappkey = $appclient->linkWebmaster(
				array(
					'username' => $username,
					'password' => $password,
				)
			);
			
			if (empty($siteappkey['status'])) {
				
				$jumpUrl = 'javascript:history.go(-1);';
	
				$msg = $appclient->getErrorLinkCodeMsg($siteappkey['code']);
	
				adminmsg($msg,$jumpUrl);
			}
			
			setConfig('db_siteappkey', $siteappkey['siteid']);
			updatecache_c();
	
			adminmsg('operate_success',"$basename&admintype=appset");
		}
	} elseif ($adminitem == 'register') {/*ע��վ������*/

		S::gp(array('step'), 'P', 2);
	
		if ($step == 2) {
			S::gp(array('username','email','password','repassword'), 'P');
			
			$siteappkey = $appclient->registerWebmaster(
				array(
					'username' => $username,
					'email' => $email,
					'password' => $password,
					'repassword' => $repassword,
				)
			);
	
			if (empty($siteappkey['status'])) {
				
				$jumpUrl = 'javascript:history.go(-1);';
	
				$msg = $appclient->getErrorRegCodeMsg($siteappkey['code']);
	
				adminmsg($msg,$jumpUrl);
			}
			
			setConfig('db_siteappkey', $siteappkey['siteid']);
			updatecache_c();
	
			adminmsg('operate_success',"$basename&admintype=appset");
		}
	
		$isLogin = $appclient->loginWebmaster();
		
		$isLogin == 1 && adminmsg('��������Ӧ�������ʺţ������ظ�ע��',"$basename&admintype=appset");
	
		if ($db_siteappkey && $isLogin != 1) {/*���վ������δע�ᣬ�������̳����*/
			setConfig('db_siteappkey', '');
			updatecache_c();
		}
	
	//} elseif ($admintype == 'link') {/*�����ʺ�*/
	
	} else {
		
		/*sitehash check*/
		$updatecache = false;
		$query = $db->query("SELECT db_name,db_value FROM pw_config WHERE db_name='db_siteid' OR db_name='db_siteownerid' OR db_name='db_sitehash'");
		while ($rt = $db->fetch_array($query)) {
			if (($rt['db_name'] == 'db_siteid' && $rt['db_value'] != $db_siteid) || ($rt['db_name'] == 'db_siteownerid' && $rt['db_value'] != $db_siteownerid) || ($rt['db_name'] == 'db_sitehash' && $rt['db_value'] != $db_sitehash)) {
				${$rt['db_name']} = preg_replace('/[^\d\w\_]/is','',$rt['db_value']);
				$updatecache = true;
			}
		}
		$db->free_result($query);
	
		if (!$db_siteid) {
			$db_siteid = generatestr(32);
			setConfig('db_siteid', $db_siteid);
	
			$db_siteownerid = generatestr(32);
			setConfig('db_siteownerid', $db_siteownerid);
	
			$db_sitehash = '10'.SitStrCode(md5($db_siteid.$db_siteownerid),md5($db_siteownerid.$db_siteid));
			setConfig('db_sitehash', $db_sitehash);
			$updatecache = true;
		}
	
		if ($app_version || $updatecache) {
			updatecache_c();
		}
		/*sitehash check*/
	
		/*վ������*/
		$isRegister = false;
		
		$isLogin = $appclient->loginWebmaster();
	
		if ($isLogin != 1) $checkResult = $appclient->checkUsername($db_appid);
	
		if ($checkResult == 1 || $isLogin == 1) {/*�����ж��Ƿ�ע��*/
	
			if ($checkResult == 1) pwCache::getData(D_P.'data/bbscache/config.php');
	
			$isRegister = true;
			$loginUrl = $appclient->getLoginWebmasterUrl($db_siteappkey);
		}
	
		$isRegister == false && $onlineAppListUrl = $appclient->getOnlineAppList();
		/*վ������*/
			
	}

} elseif ($admintype == 'onlineapp') {/*��ԱӦ��*/
	if ($adminitem == 'open') {
		S::gp(array('open_app','updatelist'));
	
		$str = $appclient->alertAppState('open');
	
		$app_set = $db_server_url.'/appset.php';
		if ($response = PostHost($app_set, $str, 'POST')) {
			$response = unserialize($response);
		} else {
			$response = array('result' => 'error', 'error' => 3);
		}
	
		if (empty($response['error']) && $updatelist != 1) {
	
			setConfig('db_appifopen', 1);
			updatecache_c();
		}
	
		adminmsg($response['result'],"$basename&admintype=onlineapp");
	}
	elseif ($adminitem == 'close') {
		$str = $appclient->alertAppState('close');
	
		$app_set = $db_server_url.'/appset.php';
		if ($response = PostHost($app_set, $str, 'POST')) {
			$response = unserialize($response);
		} else {
			$response = array('result' => 'error', 'error' => 3);
		}
		if (empty($response['error'])) {
			setConfig('db_appifopen', 0);
			updatecache_c();
		}
	
		adminmsg($response['result'],"$basename&admintype=onlineapp");
	}
	$appurl = $appclient->getOnlineApp();

}  elseif ($admintype == 'blooming') {/*���ӽ���*/

	$appurl = $appclient->getThreadsUrl('admin', 'blooming', 'index');

}elseif($admintype == 'taolianjie'){/*������*/

	$appurl = $appclient->getTaojinUrl('admin', 'taoke', 'index');

} elseif ($admintype == 'i9p') {/*�����淢*/

	if (empty($_POST['step'])) {

		$appurl = $appclient->getAppIframe('17');
	} elseif ($_POST['step'] == 2) {
		S::gp(array('open_app'));

		$str = $appclient->alertAppState('open');

		$app_set = $db_server_url.'/appset.php';
		if ($response = PostHost($app_set, $str, 'POST')) {
			$response = unserialize($response);
		} else {
			$response = array('result' => 'error', 'error' => 3);
		}

		if (empty($response['error'])) {

			setConfig('db_appifopen', 1);

			updatecache_c();
		}

		adminmsg($response['result'],"$basename&admintype=$admintype");
	}

} elseif ($admintype == 'platformweiboapp') {
	$siteBindService = L::loadClass('WeiboSiteBindService', 'sns/weibotoplatform/service'); /* @var $siteBindService PW_WeiboSiteBindService */
	$appurl = $siteBindService->getAppConfigUrl();
} elseif ($admintype == 'yunstatistics') {/*��ͳ��*/
	
	$yunStatisticsUrl = $appclient->getYunStatisticsUrl();
} elseif ($admintype == 'cnzz') {
	$urls = parse_url($db_bbsurl);
	$domain = $urls['host'];
	include_once (R_P .'require/admvclient.php');
	$adm_oem= new Cnzz_Adm_Oem();
	//�ж�config�ļ��Ƿ�����û�����
	pwCache::getData(D_P . "data/bbscache/adm_config.php");//�û������� �����ļ�
	if (isset($adm_user) && isset($adm_pwd)) {
		$request =	array("adm_user"=>$adm_user,"adm_pwd"=>$adm_pwd,'cms'=>'pw');
		$token = $adm_oem->get_appkey_once($request);
		if ($token<0){
			if($token==-1){
				adminmsg('���ݲ���Ϊ�ջ򴫵ݲ���������');
			}else if($token==-2){
				adminmsg('���ݲ���password����');
			}
		}else{
			$apikey	= $token['adm_key'];
		}
	}else{
		$Key = md5($domain.'KclGiq7H');
		$request = array('cms'=>'pw','domain'=>$domain,'key'=>$Key);
		$token = $adm_oem->reg_user_once($request);
		if ($token<0){//�쳣
			if($token==-1){
				adminmsg('key����');
			} else if($token==-2){
				adminmsg('������������1~64��');	
			} elseif($token==-3){
				adminmsg('�����������󣨱������뺺�֣�');
			} elseif($token==-4){
				adminmsg('�����������ݿ�����');
			} elseif($token==-5){
				adminmsg('IP�û�����ҳ�泬����ֵ����ֵ�ݶ�Ϊ10');
			}
		} elseif (is_array($token) && isset($token)){
			$adm_user	=	$token['adm_user'];
			$adm_pwd	=	$token['adm_pwd'];
			$apikey		=	$token['adm_key'];
			pwCache::setData(D_P.'data/bbscache/adm_config.php',"<?php\r\n\$adm_user=".$adm_user.";\r\n\$adm_pwd=".$adm_pwd."\r\n?>");
		}
	}
}
include PrintEot('app');exit;

function generatestr($len) {
	mt_srand((double)microtime()*1000000);
    $keychars = "abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWYXZ";
	$maxlen = strlen($keychars)-1;
	$str = '';
	for ($i=0;$i<$len;$i++){
		$str .= $keychars[mt_rand(0,$maxlen)];
	}
	return substr(md5($str.microtime().$GLOBALS['HTTP_HOST'].$GLOBALS['pwServer']["HTTP_USER_AGENT"].$GLOBALS['db_hash']),0,$len);
}

function SitStrCode($string,$key,$action='ENCODE'){
	$string	= $action == 'ENCODE' ? $string : base64_decode($string);
	$len	= strlen($key);
	$code	= '';
	for($i=0; $i<strlen($string); $i++){
		$k		= $i % $len;
		$code  .= $string[$i] ^ $key[$k];
	}
	$code = $action == 'DECODE' ? $code : str_replace('=','',base64_encode($code));
	return $code;
}
?>