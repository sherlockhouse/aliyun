<?php
!defined('P_W') && exit('Forbidden');
class PW_OnlineService{
	
	var $page_index = array('index' => 1, 'thread' => 2, 'read' => 3, 'cate' => 4, 'mode' => 5, 'other' => 6);
	var $db;	
	// ����ͬһ��ip�����οͣ�����������µ�tokenʱ����Ҫ��lastvisit���뵱ǰtimestamp��$tokenTime���ڵ��ο�ɾ��
	var $tokenTime = 60;
	
	/**
	 * �������ߵĵ�¼�û���Ϣ
	 *
	 * @return boolean
	 */
	function updateOnlineUser() { 
		global $fid, $tid, $timestamp, $winduid, $windid, $onlineip, $groupid, $wind_in, $db_onlinetime, $db_ipstates, $db_today, $lastvisit, $tdtime,$db;
		if ($winduid < 1) return false;
		
		$ifhide = $GLOBALS['_G']['allowhide'] && GetCookie('hideid') ? 1 : 0;
		$pwSQL = S::sqlSingle(array('uid' => $winduid, 
									'ip' => $this->_ip2long($onlineip), 
									'groupid' => $groupid, 
									'username' => $windid, 
									'lastvisit' => $timestamp, 
									'fid' => $fid, 
									'tid' => $tid, 
									'action' => $this->page_index[$wind_in], 
									'ifhide' => $ifhide));
		// ���һ��ʱ��ɾ�������û�������Ƶ��ɾ�����������½�
		if ($timestamp % 20 == 0){
			$db->update('DELETE FROM pw_online_user WHERE lastvisit<' . S::sqlEscape($timestamp - $db_onlinetime));
		}
		return $db->update('REPLACE INTO pw_online_user SET ' . $pwSQL);
	}
	
	/**
	 * �������ߵ��ο���Ϣ
	 *
	 * @return boolean
	 */
	function updateOnlineGuest(){
		global $fid, $tid, $timestamp, $onlineip,$db_onlinetime,$wind_in,$db;
		if (!($guestInfo = $this->getGuestInfo())){
			return false;
		}

		$ifhide = $GLOBALS['_G']['allowhide'] && GetCookie('hideid') ? 1 : 0;
		if ($guestInfo['token'] == 0){
			// ɾ�����ڵ��οͻ���ͬIP��60���ڸ��¹����οͣ���ֹ����ˢ��������Ϊ��
			$db->update('DELETE FROM pw_online_guest WHERE lastvisit<' . S::sqlEscape($timestamp - $db_onlinetime) . 
				' OR (ip = ' . S::sqlEscape($guestInfo['ip']) . ' AND  lastvisit>' . S::sqlEscape($timestamp - $this->tokenTime) . ')');
			$token = rand(1,255);
			$this->setGuestToken($token);
		} else {
			// ���һ��ʱ��ɾ�������û�������Ƶ��ɾ�����������½�
			if ($timestamp % 20 == 0){
				$db->update('DELETE FROM pw_online_guest WHERE lastvisit<' . S::sqlEscape($timestamp - $db_onlinetime));
			}
			$token = $guestInfo['token'];
		}

		$pwSQL = S::sqlSingle(array('ip' => $guestInfo['ip'], 
									'token' => $token,
									'lastvisit' => $timestamp, 
									'fid' => $fid, 
									'tid' => $tid, 
									'action' => $this->page_index[$wind_in], 
									'ifhide' => $ifhide));
		$db->update("REPLACE INTO pw_online_guest SET " . $pwSQL);
	}
	
	/**
	 * ���û���¼ʱ���ô˽ӿڣ�ɾ�����ڡ������ο͡���ļ�¼
	 *
	 * @return boolean
	 */
	function deleteOnlineGuest($guestInfo = null){
		if (!$guestInfo  && !($guestInfo = $this->getGuestInfo())){
			return false;
		}
		global $db;
		return $db->update('DELETE FROM pw_online_guest WHERE ip=' . S::sqlEscape($guestInfo['ip']) . ' AND token = ' .  S::sqlEscape($guestInfo['token']));
	}
	
	/**
	 * ���û��˳��ǵ��ô˽ӿڣ�ɾ�����ڡ������û�����ļ�¼
	 *
	 * @return boolean
	 */
	function deleteOnlineUser($userId){
		if (($userId = intval($userId)) < 1) return false;
		global $db;
		return $db->update("DELETE FROM pw_online_user WHERE uid=" . S::sqlEscape($userId));
	}
	
	/**
	 * ��ȡ�������ߵĵ�¼�û��б�, רΪsort.phpͳ����������ʹ��
	 *
	 * @param integer $start ҳ��
	 * @param integer $perpage ÿҳ��Ŀ
	 * @param integer &$number �ش�������������������
	 * @return array
	 */
	function getAllOnlineWithPaging($start, $perpage, &$number){
		$online_user_num = $this->countOnlineUser();
		$online_guest_num = $this->countOnlineGuest();
		if ($start * $perpage <= $online_user_num){
			$all = $this->getOnlineUser($start, $perpage);
		}else if (($start-1) * $perpage + 1> $online_user_num){
			$all = $this->getOnlineGuest($start, $perpage);
		}else{
			$all = array_merge($this->getOnlineUser($start, $perpage), $this->getOnlineGuest(1, $perpage));
		}
		$number = $online_user_num + $online_guest_num;
		return $all;
	}
	
	/**
	 * ��ȡ���������û��б�������¼�û����ο�, ���ṩ��ҳ
	 *
	 * @return array
	 */
	function getAllOnline(){
		return array_merge((array)$this->getOnlineUser(), (array)$this->getOnlineGuest());
	}		
		
	/**
	 * ��ȡ���������û���֧�ַ�ҳ��������$start��$perpage�������ȡȫ���û�
	 *
	 * @param int $start 
	 * @param int $perpage
	 * @return array
	 */
	function getOnlineUser($start = 0, $perpage = 20){
		global $db;
		$limit = $start < 1 ? '' : S::sqlLimit(($start - 1) * $perpage, $perpage);
		$query = $db->query('SELECT * FROM pw_online_user ' . $limit);
		$page_reverse_index = array_flip($this->page_index);
		$users = array();
		while ($rt = $db->fetch_array($query)){
			$rt['ip'] = long2ip($rt['ip']);
			$rt['action'] = $page_reverse_index[$rt['action']];
			$users[] = $rt;
		}
		return $users;
	}
	
	/**
	 * ��ȡ���������οͣ�֧�ַ�ҳ��������$start��$perpage�������ȡȫ���û�
	 *
	 * @param int $start 
	 * @param int $perpage
	 * @return array
	 */
	function getOnlineGuest($start = 0, $perpage = 20){
		global $db;
		$limit = $start < 1 ? '' : S::sqlLimit(($start - 1) * $perpage, $perpage);
		$query = $db->query('SELECT * FROM pw_online_guest ' . $limit);
		$page_reverse_index = array_flip($this->page_index);
		$guests = array();
		while ($rt = $db->fetch_array($query)){
			$rt['ip'] = long2ip($rt['ip']);
			$rt['action'] = $page_reverse_index[$rt['action']];
			$guests[] = $rt;
		}
		return $guests;
	}
	
	/**
	 * ��ȡ���������û����û�������uid��Ϊkey
	 *
	 * @return array
	 */
	function getOnlineUserName(){
		global $db;
		$query = $db->query('SELECT uid, username FROM pw_online_user');
		$users = array();
		while ($rt = $db->fetch_array($query)){
			$users[$rt['uid']] = $rt['username'];
		}
		return $users;		
	}
	
	/**
	 * ��ȡĳһ���������û�
	 *
	 * @param integer $forumId
	 * @return array
	 */
	function getOnlineUserByForumId($forumId){
		global $db;
		if (($forumId = intval($forumId)) < 1) return false;
		$query = $db->query('SELECT * FROM pw_online_user WHERE fid=' . S::sqlEscape($forumId));
		$onlineUsers = array();
		while($rt = $db->fetch_array($query)){
			$onlineUsers[] = $rt;
		}
		return $onlineUsers;
	}
	
	/**
	 * ����userid��pw_online_user���ȡһ����¼
	 *
	 * @param integer $userId
	 * @return array
	 */
	function getOnlineUserByUserId($userId){
		global $db;
		return $db->get_one('SELECT * FROM pw_online_user WHERE uid=' . S::sqlEscape($userId));
	}
	
	/**
	 * ͳ�����ߵĵ�¼�û���Ŀ
	 *
	 * @return integer
	 */
	function countOnlineUser(){
		global $db;
		$rt = $db->get_one('SELECT COUNT(*) AS sum FROM pw_online_user');
		return $rt ? $rt['sum'] : $rt;
	}	
	
	/**
	 * ͳ�����������ο���Ŀ
	 *
	 * @return integer
	 */
	function countOnlineGuest(){
		global $db;
		$rt = $db->get_one("SELECT COUNT(*) AS sum FROM pw_online_guest");
		return $rt ? $rt['sum'] : $rt;		
	}
	
	/**
	 * ͳ�����������û���������¼�û����ο�
	 *
	 * @return integer
	 */
	function countAllOnline(){
		return (int)$this->countOnlineUser() + (int)$this->countOnlineGuest();
	}
	
	/**
	 * ͳ��ָ��ip����������
	 *
	 * @param integer $ip
	 * @return integer
	 */
	function countOnlineGuestByIp($ip){
		if (!$ip) return false;
		global $db;
		$rt = $db->get_one('SELECT COUNT(*) AS sum FROM pw_online_guest WHERE ip = ' . S::sqlEscape($ip) );
		return $rt ? $rt['sum'] : 0;
	}
	
	/**
	 * д�ο����Ƶ�cookie
	 *
	 */
	function setGuestToken($token = 0){
		return $token ? Cookie('oltoken', StrCode($this->_ip2long($GLOBALS['onlineip']) . "\t" . $token)) : Cookie('oltoken', 'init');
	}

	/**
	 * д��ǰ���߻�Ա���������ο�����cookie
	 *
	 */
	function setOnlineNumber(){
		return Cookie('online_info',  $GLOBALS['timestamp'] . "\t" .(int)$this->countOnlineUser() . "\t" . $this->countOnlineGuest());
	}
	
	/**
	 * ��cookie��ȡ�ο���Ϣ
	 * ipchange ��ʾip�Ƿ�ı��ˣ����adsl���û�
	 *
	 * @return array
	 */
	function getGuestInfo(){
		static $guestInfo = null;
		if (isset($guestInfo)) return $guestInfo;		
		list($ip, $token) = explode("\t", StrCode(GetCookie('oltoken'), 'DECODE'));
		$onlineip = $this->_ip2long($GLOBALS['onlineip']);
		if ($ip != $onlineip || $token > 254 || $token < 1) {
			$guestInfo = array('ip' => $onlineip, 'token' => 0);
			$guestInfo['ipchange'] = ($ip != $onlineip && $token > 0 && $token < 255) ? true : false;
		}else {
			$guestInfo = array('ip' => $onlineip, 'token' => $token , 'ipchange' => false);
		}
		return $guestInfo;
	}	
	
	/**
	 * ��װ��ip2long��������Ҫ��ip��ַ������'unknown'
	 *
	 * @param string $ip
	 * @return int
	 */
	function _ip2long($ip){
		/**
		$ip = ip2long($ip);
		if ($ip === false || $ip == -1) $ip = ip2long('0.0.0.0');
		return $ip;
		**/
		list(, $ip) = unpack('l',pack('l',ip2long($ip)));
		return $ip;		
	}
}

