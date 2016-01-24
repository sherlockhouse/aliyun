<?php
!defined('P_W') && exit('Forbidden');

/**
 * APP���
 *
 * @package APP
 */
class PW_AppClient {
	var $_db;
	function PW_Appclient() {
		global $db_siteappkey, $timestamp, $db_sitehash, $db_siteownerid, $db_siteid, $db_charset, $db_appifopen, $pwServer, $db_server_url,$db_bbsname;
		$db_bbsurl = S::escapeChar("http://" . $pwServer['HTTP_HOST'] . substr($pwServer['PHP_SELF'], 0, strrpos($pwServer['PHP_SELF'], '/')));
		if (!file_exists(D_P . "data/bbscache/forum_appinfo.php")) {
			require_once (R_P . "admin/cache.php");
			updatecache_f();
		}
		//* @include_once pwCache::getPath(D_P . "data/bbscache/forum_appinfo.php");
		extract(pwCache::getData(D_P . "data/bbscache/forum_appinfo.php", false));
		$this->_db = $GLOBALS['db'];
		$this->appkey = $db_siteappkey;
		$this->timestamp = $timestamp;
		$this->siteid = $db_siteid;
		$this->siteownerid = $db_siteownerid;
		$this->sitehash = $db_sitehash;
		$this->bbsname = $db_bbsname;
		$this->bbsurl = $db_bbsurl;
		$this->charset = $db_charset;
		$this->appifopen = $db_appifopen;
		$this->server_url = $db_server_url;
		$this->appinfo = $forum_appinfo;
	}

	/**
	 * ��ȡ������APP�б�
	 */
	function getApplist() {
		global $db_apps_list;
		$this->_appsdb = $appsdb = array();
		$appsdb = $db_apps_list;
		!is_array($appsdb) && $appsdb = array();
		foreach($appsdb as $value) {
			if ($value['appstatus'] == 1 && $value['status'] == 1) {
				$this->_appsdb[$value['appid']]['appid'] = $value['appid'];
				$this->_appsdb[$value['appid']]['name'] = $value['name'];
			}
		}
		if (!$this->_appsdb || !$this->appifopen) {
			$this->_appsdb = array();
		}
		return $this->_appsdb;
	}

	/**
	 * ��ȡ����APP�б�
	 */
	function userApplist($uids, $appids = '', $arrt = 0) {
		if (!$uids) return false;
		$this->_app_array = array();
		$this->_appslist = $this->getApplist();
		$sql_uid = $sql_appid = '';
		if (is_numeric($uids)) {
			$sql_uid .= ' uid=' . S::sqlEscape($uids);
		} elseif (is_array($uids)) {
			$sql_uid .= ' uid IN(' . S::sqlImplode($uids) . ')';
		}
		if (is_numeric($appids)) {
			$sql_appid .= ' AND appid=' . S::sqlEscape($appids);
		} elseif (is_array($appids)) {
			$sql_appid .= ' AND appid IN(' . S::sqlImplode($appids) . ')';
		}
			//$query = $this->_db->query("SELECT uid,appid,appname FROM pw_userapp WHERE $sql_uid $sql_appid");
		if (perf::checkMemcache()){
			$appids = is_array($appids) ? $appids : array(intval($appids));
			$_cacheService = Perf::gatherCache('pw_userapp');
			$array = $_cacheService->getUserappsCacheByUids($uids);
			foreach($array as $v) {
				if (in_array($v['appid'],$appids)) continue;
				if ($this->_appslist[$v['appid']]) {
					if ($arrt == 1) {
						$this->_app_array[$v['appid']] = $v['appname'];
					} elseif ($arrt == 2) {
						$this->_app_array[$v['uid']][$v['appid']] = $v;
					} else {
						$this->_app_array[] = $v;
					}
				}
			}	
		} else {
			$query = $this->_db->query("SELECT uid,appid,appname FROM pw_userapp WHERE $sql_uid $sql_appid");
			while ($rt = $this->_db->fetch_array($query)) {
				if ($this->_appslist[$rt['appid']]) {
					if ($arrt == 1) {
						$this->_app_array[$rt['appid']] = $rt['appname'];
					} elseif ($arrt == 2) {
						$this->_app_array[$rt['uid']][$rt['appid']] = $rt;
					} else {
						$this->_app_array[] = $rt;
					}
				}
			}
		}
		
		if (!$this->_app_array || !$this->appifopen) {
			$this->_app_array = array();
		}
		return $this->_app_array;
	}
	function getUserAppsByUid($uid){
		$uid = intval($uid);
		$apps = array();
		if (perf::checkMemcache()){
			$apps = $_cacheService->getUserappsCacheByUids($uid);
		} else {
			$query = $this->_db->query("SELECT * FROM pw_userapp WHERE uid=".S::sqlEscape($uid));
			while ($rt = $this->db->fetch_array($query)) {
				$apps[] = $rt;
			}
		}
		return $apps;
	}
	
	function getUserAppByUidAndAppid($uid,$appid){
		$uid = intval($uid);
		$appid = intval($appid);
		if (perf::checkMemcache()){
			$apps = $_cacheService->getUserappsCacheByUids($uid);
			foreach ($apps as $v){
				if ($v['appid'] == $appid) return $v;		
			}
		} else {
			return $this->_db->get_one("SELECT * FROM pw_userapp WHERE uid=".S::sqlEscape($uid)." AND appid=".S::sqlEscape($appid));
		}
		return array();
	}
	
	function deleteUserAppByUidAndAppid($uid,$appid){
		pwQuery::delete('pw_userapp', 'uid=:uid AND appid=:appid', array($uid,$appid));
	}
	
	/** ��ȡ���APP��Ϣ
	 *
	 * @param int $fid ���ID
	 * @param string $position ��̳��ʾAPPӦ�õ�λ��,���絥��forum_erect ���� forum_erect,forum_across ���� subforum_erect,subforum_across
	 * 'forum_erect' => '1', //��ҳ(һ��)�������
	 * 'forum_across' => '1', //��ҳ(һ��)������
	 * 'subforum_erect' => '1', //�����������
	 * 'subforum_across' => '1', //����������
	 * 'thread' => '1', //����б�ҳ������
	 * 'read' => '1', //����ҳ��ҳ������
	 * @param string $appids ��ʾ��APPӦ��ID,����17 ���� 13,17 �������գ�����ʾ����
	 * @return array ��ʾ����λ�õ�����
	 */
	function showForumappinfo($fid, $position = 'forum_erect', $appid = 0) {
		global $db_apps_list;
		if (!is_numeric($fid) && !$fid) return false;
		$positiondb = explode(",", $position);
		$appinfodb = array();
		$foruminfo['appinfo'] = $this->appinfo[$fid];
		!is_array($foruminfo['appinfo']) && $foruminfo['appinfo'] = array();
		foreach($foruminfo['appinfo'] as $key => $value) {
			if ($appid && $appid != $key) {
				continue;
			}
			foreach($positiondb as $val) {
				if ($value['position'][$val]) {
					if ($key == $appid && $db_apps_list[$appid]['status'] == 1) {
						$appinfo = $value['c_text'] . ":" . $value['mms_emailcode'] . "." . $fid . $value['mms_domain'];
					}
					$appinfodb[$val][] = $appinfo;
				}
			}
		}
		$newappinfodb = array();
		foreach($appinfodb as $p => $info) {
			$appinfo = '';
			foreach($info as $val) {
				$appinfo .= $val . ' ';
			}
			$newappinfodb[$p] = $appinfo;
		}
		return $newappinfodb;
	}

	/**
	 * ��ȡ����ǩ��
	 */
	function getApicode() {
		$code = base64_encode(md5(md5($this->siteownerid . $this->appkey) . $this->timestamp . $this->sitehash) . $this->timestamp . '$sitehash=' . $this->sitehash);
		return $code;
	}

	/**
	 * ��ȡ������url��ַ
	 */
	function getTaojinUrl($system = 'index', $mode = 'index', $action = 'index'){
		global $winduid, $windid;

		$param = array(
			'pw_appId'		=> '17',
			'pw_uid'		=> $winduid,
			'pw_siteurl'	=> $this->bbsurl,
			'pw_t'			=> $this->timestamp,
			'pw_system'		=> $system,
			'pw_mode'		=> $mode,
			'pw_action'		=> $action,
			'pw_query'		=> $this->getApicode(),
		);

		$url = 'http://app.phpwind.net/pwbbsapi.php?m=taoke&';
        ksort($param);
        foreach ( $param as $key => $value ) {
            $url .= "$key=" . urlencode ( $value ) . '&';
        }
        $hash = $param ['pw_system'] .'&'.$param ['pw_mode'].'&'.$param ['pw_action'] .'&'.$param ['pw_appId'] . '&' . $param ['pw_uid'] . '&' . $param ['pw_siteurl']  . '&' . $param ['pw_t'] . '&' . $param['pw_query'];
        $url .= 'pw_sig=' . md5 ( $hash . $this->siteownerid );
        return $url;
	}

	/**
	 * ��ȡ���ӽ����ϴ��б�
	 */
	function getThreadsUrl($system = 'index', $mode = 'index', $action = 'index', $fid = 2) {
		global $winduid, $windid, $groupid;
		if (!is_numeric($fid) && !$fid) $fid = 2;
		$param = array(
			'pw_appId' => '8',
			'pw_charset' => $this->charset,
			'pw_uid' => $winduid,
			'pw_siteurl' => $this->bbsurl,
			'pw_t' => $this->timestamp,
			'pw_system' => $system,
			'pw_username' => $windid,
			'pw_fid' => $fid,
			'pw_mode' => $mode,
			'pw_action' => $action,
			'pw_groupid' => $groupid,
			'pw_query' => $this->getApicode(),
		);
		$url = 'http://app.phpwind.net/pwbbsapi.php?m=blooming&';
		ksort($param);
		foreach($param as $key => $value) {
			$url .= "$key=" . urlencode($value) . '&';
		}
		$hash = $param['pw_system'] . '&' . $param['pw_mode'] . '&' . $param['pw_action'] . '&' . $param['pw_appId'] . '&' . $param['pw_uid'] . '&' . $param['pw_siteurl'] . '&' . $param['pw_t'] . '&' . $param['pw_query'];
		$url .= 'pw_sig=' . md5($hash . $this->siteownerid);
		return $url;
	}

	/**
	 * ��ȡ���ӽ���Ȩ��
	 */
	function getThreadRight() {
        global $windid,$groupid, $db_threadconfig;
        $put = array();
        $t = $db_threadconfig;

        if (is_array($t)) {
            if ($t['ifopen'] == 1) {
                $isManage = ($groupid == 3) ? 1 : 0;//manage?
                //���غ��ϴ�Ȩ��
                $put['down']['admin'] = ($isManage == 1 && $t['if_admin_down'] == 1) ? 1 : 0;
                $put['down']['other'] = array();
                $put['post']['admin'] = ($isManage == 1 && $t['if_admin_post'] == 1) ? 1 : 0;
				$put['post']['other'] = array();


                if ($t['if_other_down'] == 1) {
                    foreach ($t['permissions'] as $v) {
                        if ($v['username'] == $windid) {
                            $fid_arr		= explode(',',$v['fid']);
                            $if_down_arr	= explode(',',$v['if_down']);
                            $if_post_arr	= explode(',',$v['if_post']);
                            for ($i = 0;$i < count($fid_arr);$i++) {
                                if ($if_down_arr[$i] == 1) {
                                    $put['down']['other'][] = $fid_arr[$i];
                                }
                                if ($if_post_arr[$i] == 1) {
                                    $put['post']['other'][] = $fid_arr[$i];
                                }
                            }
                            break;
                        }
                    }
                }
            }
        }
        return $put;
    }

	/**
	 * ��ȡAPP iframe
	 */
	function getAppIframe($app_id) {
		global $admin_name;
		$app_serverurl = $this->server_url . '/appsmanager.php';
		$param = array(
			'pw_sitehash' => $this->sitehash,
			'pw_fromurl' => $this->bbsurl . "/admin.php?adminjob=app",
			'pw_time' => $this->timestamp,
			'pw_user' => $admin_name,
			'pw_appid' => $app_id,
		);
		$url = $app_serverurl . '?';
		ksort($param);
		foreach($param as $key => $value) {
			$url .= "$key=" . urlencode($value) . '&';
		}
		$arg = 'pw_appid=' . $param['pw_appid'] . '&pw_user=' . $param['pw_user'] . '&pw_time=' . $param['pw_time'];
		$url .= 'pw_sig=' . md5($arg . $this->siteownerid);
		return $url;
	}

	/**
	 * ��ȡ����APP�б�
	 */
	function getOnlineApp() {
		global $admin_name;
		$app_list = $this->server_url . '/adminlist.php';
		$param = array(
			'pw_sitehash' => $this->sitehash,
			'pw_fromurl' => $this->bbsurl . "/admin.php?adminjob=app",
			'pw_time' => $this->timestamp,
			'pw_user' => $admin_name,
		);
		$arg = implode('|', $param);
		ksort($param);
		$url = $app_list . '?';
		foreach($param as $key => $value) {
			$url .= "$key=" . urlencode($value) . '&';
		}
		$url .= 'pw_sig=' . md5($arg . $this->siteownerid);
		return $url;
	}

	/**
	 * APP��̳״̬
	 */
	function alertAppState($admintype) {
		global $admin_name, $db_bbsname, $db_timedf;
		$param = array(
			'pw_sitehash' => $this->sitehash,
			'pw_fromurl' => $this->bbsurl . "/admin.php?adminjob=app",
			'pw_time' => $this->timestamp,
			'pw_user' => $admin_name,
		);
		if ($admintype == 'open') {
			$param = array_merge($param, array(
				'action' => 'open',
				'sitename' => $db_bbsname,
				'siteurl' => $this->bbsurl,
				'charset' => $this->charset,
				'timedf' => $db_timedf
			));
		} elseif ($admintype == 'close') {
			$param['action'] = 'close';
		}
		ksort($param);
		$str = $arg = '';
		foreach($param as $key => $value) {
			if ($value) {
				$str .= "$key=" . urlencode($value) . '&';
				$arg .= "$key=$value&";
			}
		}
		$str .= 'pw_sig=' . md5($arg . $this->siteownerid);
		return $str;
	}

	/**
	 * �ж��Ƿ�Ϊ����
	 */
	function isLocalhost($host) {
		if ($host && strpos($host, 'localhost') === false && strpos($host, '127.0') === false && strpos($host, '127.1') === false && !preg_match('/^192.168.*/', $host) && !preg_match('/^10.*/', $host)) {
			$islocalhost = false;
		} else {
			$islocalhost = true;
		}
		return $islocalhost;
	}

	/**
	 * ��ͳ��
	 */
	function getYunStatisticsUrl() {
		$yunStatisticsUrl = 'http://tongji.phpwind.com/statistic/?' . $this->_bulidQueryString(array(
			'app_key' => $this->sitehash,
			'timestamp' => $this->timestamp,
			'v' => '1.0',
		), $this->siteownerid);
		return $yunStatisticsUrl;
	}

	function _bulidQueryString($params ,$appKey) {
		ksort($params);
		reset($params);
		$pairs = array();
		foreach ($params as $key => $value) {
			$pairs[] = urlencode($key) . '=' . $value;
		}
		$string = implode('&', $pairs);
		$string.= '&sig=' . md5($string .'&' . $appKey);
		return $string;
	}

	/*************************վ���������****************************/

	/**
	 * ȷ���ʺ��Ƿ����
	 */
	function checkUsername($appid) {
		
		if (empty($appid)) return false;

		$siteappkey = $this->_checkUsername($appid);
		
		if (!empty($siteappkey['status'])) {
			setConfig('db_siteappkey', $siteappkey['siteid']);
			updatecache_c();
			return true;
		}

		return false;
	}

	/**
	 * ȷ���ʺ��Ƿ����
	 */
	function _checkUsername($appid) {

		$platformApiClient = $this->_getPlatformApiClient();
		
		$params = array(
			'username' => $appid,
			'charset' => $this->charset
		);

		L::loadClass('json', 'utility', false);
		$Json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
		return $Json->decode($platformApiClient->post('webmaster.onlineapp.checkusername' ,$params));
	}

	/**
	 * ע��վ�������ʺ�
	 */
	function registerWebmaster($fields) {

		$params = $this->_checkRegisterWebmaster($fields);
		if (empty($params)) return array('status' => false ,'code' => '-1');

		$platformApiClient = $this->_getPlatformApiClient();

		L::loadClass('json', 'utility', false);
		$Json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
		return $Json->decode($platformApiClient->post('webmaster.onlineapp.register' ,$params));
	}

	/**
	 * ע��ǰ�߼��жϲ���
	 */
	function _checkRegisterWebmaster($fields) {

		isset($fields['username']) && $username = $fields['username'];
		isset($fields['email']) && $email = $fields['email'];
		isset($fields['password']) && $password = $fields['password'];
		isset($fields['repassword']) && $repassword = $fields['repassword'];
		
		$params = array(
			'username' => $username,
			'email' => $email,
			'password' => $password,
			'repassword' => $repassword,
			'siteid' => $this->siteid,
			'siteownerid' => $this->siteownerid,
			'sitehash' => $this->sitehash,
			'timestamp' => $this->timestamp,
			'sitename' => $this->bbsname,
			'siteurl' => $this->bbsurl,
			'charset' => $this->charset,
		);

		return $params;
		
	}

	/**
	 * ��ȡ��������Ϣ
	 */
	function getErrorRegCodeMsg($code) {
		switch ($code) {
			case '2':
				$msg = '����������Ƿ���ȷ';break;
			case '3':
				$msg = '����������վ����Կ��';break;
			case '4':
				$msg = '�Բ�������д����Ϣ��ƥ�䣡';break;
			case '5':
				$msg = '�Բ�������д���û����ѱ�ʹ�ã�';break;
			case '6':
				$msg = '�Բ�������д�������ʽ����';break;
			case '7':
				$msg = '�Բ�������д�������ѱ�ʹ�ã�';break;
			case '8':
				$msg = '�Բ�������д��������ʽ����';break;
			case '9':
				$msg = '�Բ�������д�����볤�Ȳ���ȷ����6-20λ';break;
			case '10':
				$msg = '�Բ���������Կ�ѱ�ʹ�ã�����ϵ�ٷ���';break;
			case '11':
				$msg = '�Բ������Ĳ���δ�ɹ���������';break;
			case '12':
				$msg = '�Բ���������������벻һ��';break;
			case '13':
				$msg = '�û������ȱ�����2-16����֮��';break;
			default:
				$msg = '�Բ���ͨ��ʧ�ܣ�������';
		}
		return $msg;
	}

	/**
	 * ����վ�������ʺţ����µ�¼��
	 */
	function linkWebmaster($fields) {

		$params = $this->_checkLinkWebmaster($fields);

		$platformApiClient = $this->_getPlatformApiClient();

		L::loadClass('json', 'utility', false);
		$Json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);

		return $Json->decode($platformApiClient->post('webmaster.onlineapp.registerbyoldaccount' ,$params));
		
	}

	/**
	 * ����ǰ�߼��жϲ���
	 */
	function _checkLinkWebmaster($fields) {
		
		isset($fields['username']) && $username = $fields['username'];
		isset($fields['password']) && $password = $fields['password'];
		
		$params = array(
			'username' => $username,
			'password' => $password,
			'siteid' => $this->siteid,
			'siteownerid' => $this->siteownerid,
			'sitehash' => $this->sitehash,
			'charset' => $this->charset,
		);

		return $params;
		
	}

	/**
	 * ��ȡ��������Ϣ
	 */
	function getErrorLinkCodeMsg($code) {
		switch ($code) {
			case '3':
				$msg = '����������վ����Կ��';break;
			case '4':
				$msg = '�Բ����û��������ڣ�';break;
			case '5':
				$msg = '�Բ���������������';break;
			case '6':
				$msg = '�Բ�������д����Ϣ��ƥ�䣡';break;
			case '7':
				$msg = '�Բ������Ĳ���δ�ɹ���������';break;
			default:
				$msg = '�Բ���ͨ��ʧ�ܣ�������';
		}
		return $msg;
	}

	/**
	 * ��¼վ������
	 */
	function loginWebmaster() {
		
		$platformApiClient = $this->_getPlatformApiClient();
		
		$params = array('siteappkey' => $this->appkey);
		
		return $platformApiClient->post('webmaster.onlineapp.login' ,$params);
		
	}

	/**
	 * ��ȡվ�����ĵ�¼��ҳ��
	 */
	function getLoginWebmasterUrl($appkey) {
		
		$platformApiClient = $this->_getPlatformApiClient();
		
		$params = array(
			'siteurl' => $this->bbsurl,
			'siteappkey' => $appkey
		);

		return $platformApiClient->buildPageUrl(0 ,'webmaster.onlineapp.index' ,$params);
		
	}

	/**
	 * ��ȡ�Ƽ�Ӧ����Ϣ
	 */
	function getOnlineAppList() {
		
		$platformApiClient = $this->_getPlatformApiClient();

		return $platformApiClient->buildPageUrl(0 ,'webmaster.onlineapp.applist');
		
	}

	/**
	 * �ж�url�Ƿ�Ķ�
	 */
	function isUrlChanged() {

		$platformApiClient = $this->_getPlatformApiClient();
	
		$params = array(
			'siteurl' => $this->bbsurl,
			'siteappkey' => $this->appkey
		);
	
		L::loadClass('json', 'utility', false);
		$Json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
		return $Json->decode($platformApiClient->post('webmaster.onlineapp.checkurl' ,$params));
		
	}
	
	/**
	 * ��ȡurl�Ķ������Ϣ
	 */
	function getUrlChangedMsg() {
		
		$isUrlChanged = $this->isUrlChanged();

		if (empty($isUrlChanged['status'])) {

			return $this->getErrorUrlCodeMsg($isUrlChanged['code']);
		}
		return false;
	}

	/**
	 * ��ȡ��������Ϣ
	 */
	function getErrorUrlCodeMsg($code) {
		
		switch ($code) {
			case '1':
				$msg = 'վ�����ϵ���ַΪ�գ�����ͨ��Ӧ����ҳ�޸����ϣ�';break;
			case '2':
				$msg = false;break;
			case '3':
				$msg = 'վ�����ϵ���ַ�͵�ǰ��һ�£�����ͨ��Ӧ����ҳ�޸����ϣ�';break;
			default:
				$msg = false;
		}
		return $msg;
	}
	
	/**
	 * �ͻ������ɷ���
	 */
	function _getPlatformApiClient() {
		static $client = null;
		if (!$client) {
			L::loadClass('client', 'utility/platformapisdk', false);
			$client = new PlatformApiClient($this->sitehash, $this->siteownerid);
		}
		return $client;
	}
	/*************************վ���������****************************/


	/**
	 * ��ȡϺ�������б�
	 */
	function getMusic($page = 1, $keyword) {
		global $winduid;
		$param = array();
		$param['pw_appIdname'] = 'xiami';
		$param['pw_uid'] = $winduid;
		$param['pw_siteurl'] = $this->bbsurl;
		$param['pw_sitehash'] = $this->sitehash;
		$param['pw_t'] = $this->timestamp;
		$param['pw_bbsapp'] = 1;
		$param['pw_keyword'] = $keyword;
		$param['pw_page'] = $page;
		$url = $this->server_url . '/apps.php?';
		foreach($param as $key => $value) {
			$url .= "$key=" . urlencode($value) . '&';
		}
		$hash = $param['pw_appIdname'] . '|' . $param['pw_uid'] . '|' . $param['pw_siteurl'] . '|' . $param['pw_sitehash'] . '|' . $param['pw_t'];
		$url .= 'pw_sig=' . md5($hash . $this->siteownerid);
		require_once (R_P . 'require/posthost.php');
		$backdata = PostHost($url, '', 'POST');
		if (empty($backdata)) {
			$backdata = PostHost($url, '', 'POST');
		}
		$data = unserialize($backdata);
		return $data;
	}

	/**
	 * ��ȡAPP-iframe�б�
	 */
	function ShowAppsList() {
		global $winduid;
		$param = array();
		$param = array(
			'pw_appId' => 0,
			'pw_uid' => $winduid,
			'pw_siteurl' => $this->bbsurl,
			'pw_sitehash' => $this->sitehash,
			'pw_t' => $this->timestamp
		);
		$arg = implode('|', $param);
		$url = $this->server_url . '/list.php?';
		foreach($param as $key => $value) {
			$url .= "$key=" . urlencode($value) . '&';
		}
		$url .= 'pw_sig=' . md5($arg . $this->siteownerid);
		return $url;
	}

	/**
	 * �Ƴ��û�����APP
	 */
	function MoveAppsList($id) {
		global $winduid;
		$param = array();
		$param = array(
			'pw_appId' => 0,
			'pw_uid' => $winduid,
			'pw_siteurl' => $this->bbsurl,
			'pw_sitehash' => $this->sitehash,
			'pw_t' => $this->timestamp,
			'pw_appId' => $id
		);
		$arg = implode('|', $param);
		$url = $this->server_url . '/list.php?';
		foreach($param as $key => $value) {
			$url .= "$key=" . urlencode($value) . '&';
		}
		$url .= 'pw_sig=' . md5($arg . $this->siteownerid);
		require_once (R_P . 'require/posthost.php');
		PostHost($url, 'op=delapp', 'POST');
	}
}
?>