<?php
/**
 * �û�������
 * 
 * @author pw team, Oct 18, 2010
 * @copyright 2003-2010 phpwind.net. All rights reserved.
 * @version 
 * @package default
 */
 
!defined('P_W') && exit('Forbidden');
 
class PW_BanUser {
	var $db;
	var $isGM;
	var $isBM;
	
	function PW_BanUser() {
		global $db,$isGM,$isBM;
		$this->db = & $db;
		$this->isGM = & $isGM;
		$this->isBM = & $isBM;
	}
	
	/**
	 * ����û�
	 * 
	 * @param int $uid �������û�id
	 * @param array $params optional
	 * 				fid		���ڰ��id, 
	 * 				limit	�������,
	 * 				type	�������� 1-���� ,2����,
	 * 				range	���Է�Χ 0��飬1ȫ��
	 * 				banip	��ֹ�� IP
	 * 				userip	�û�IP
	 * 				ifmsg	�Ƿ�����Ϣ 1-��,0-��
	 * 				reason	����ԭ��
	 * @access public
	 * @return PW_MembersDB
	 */
	function ban($uid,$params = array()) {
		global $SYSTEM;
		$uid = intval($uid);
		$tid = intval($params['tid']);
		$pid = intval($params['pid']);
		$range = intval($params['range']);
		$type = intval($params['type']);
		$limit = intval($params['limit']);
		$banip = intval($params['banip']);
		$ifmsg = intval($params['ifmsg']);
		$reason = $params['reason'];

		/*����ǰ����ԣ���ȡ������Ϣ*/
		if ($tid > 0) {
			$postInfo = $this->getPostInfo($tid,$pid);
			if($postInfo === false) return 'banuser_post_data_error';
			$fid = $postInfo['fid'];
			$userip = $postInfo['userip'];
		}
		if ($range == 1) {
			//ȫ�ֽ���
			$tid = $fid = 0;
		} else {
			$range = 0;
//			unset($banip);
		}
		
		if ($type != 1) {
			//���ý���
			$type = 2;
			$limit = 0;
		}
		if($uid == $GLOBALS['winduid'])return '���ܶ��Լ�����';
		$userService = L::loadClass('UserService', 'user'); /* @var $userService PW_UserService */
		$banuserDb = $this->_getBanUserDB();
		$userinfo = $userService->get($uid);
		if(!$userinfo) return 'undefined_action';
		/*�ж��Ƿ��Ѿ�����*/

/*		
		$GLOBALS['username'] = $userinfo['username'];//GLOBAL
		if ($userinfo['groupid'] == 6) {
			return 'member_havebanned';
		} elseif (getstatus($userinfo['userstatus'], PW_USERSTATUS_BANUSER)) {
			if ($banuserDb->checkByUidFid($uid,$fid)) return 'member_havebanned';
		}*/
		
		/*���ڽ���,�жϹ���Ա���ܽ��Ե��ʱ��*/
		if ($type == 1) {
			//$pwBanMax = pwRights($this->isBM,'banmax');
			if ($SYSTEM['banmax'] < 0) {
				return 'banuser_no_banright';
			} elseif($SYSTEM['banmax'] != 0 && $limit > $SYSTEM['banmax']) {//banmax=>0������ʱ��
				$GLOBALS['pwBanMax'] = $SYSTEM['banmax'];
				return 'masigle_ban_limit';
			}
		}
		/*�ж��Ƿ���Ȩ��ִ�����ý���*/
		if (!$this->isGM && $type == '2' && !$SYSTEM['bantype']) {
			return 'masigle_ban_right';
		}

		/*�ж��Ƿ���Ȩ��ִ��ȫ�ֽ���*/
		if(!$this->isGM && ($range == 1 || $banip) && $SYSTEM['banuser'] != '2'){
			return 'masigle_ban_range';
		} 
		/*�ж��Ƿ��н�ֹIPȨ��*/
		if($banip && !$SYSTEM['banuserip']){
			return 'masingle_banip_noright';
		}
		
		$userGroups = array($userinfo['groupid']);
		foreach(explode(',',$userinfo['groups']) as $v){
			$v = intval($v);
			$v > 0  && $userGroups[] = $v; 
		}
		//�����û�����
		if($userinfo['groupid'] != '-1'){
			if(!$SYSTEM['banadmin']){
				return '����Ȩ���Ը��û����Ա!';	/*��Ȩ����������*/
			} 
			/*delete from administrators*/
			$this->db->query("DELETE FROM `pw_administrators` WHERE uid=".S::sqlEscape($uid));
		}
		
		/*���Դ���*/
		if($banuserDb->add(
			array(
				'uid'		=> $uid,
				'fid'		=> $fid,
				'type'		=> $type,
				'startdate'	=> $GLOBALS['timestamp'],
				'days'		=> $limit,
				'admin'		=> $GLOBALS['windid'],
				'reason'	=> $reason
			)
		)){
			$updateArray = array('groups'=>'');
			$range && $updateArray['groupid'] = 6; /*clear groups*/
			$userService->update($uid, $updateArray);
			$userService->setUserStatus($uid, PW_USERSTATUS_BANUSER, true);
			
			//�������
			if(in_array(5,$userGroups)){
				$forumService = L::loadClass('forums', 'forum'); /* @var $forumService PwForum */
				$forumService->deleteForumAdmin($userinfo['username']);
			}
			/*������Ϣ*/
			$ifmsg && M::sendNotice(
					array($userinfo['username']),
					array(
						'title' => getLangInfo('writemsg','banuser_title'),
						'content' => getLangInfo('writemsg','banuser_content_'.$type,array(
							'reason'	=> stripslashes($reason),
							'manager'	=> $GLOBALS['windid'],
							'limit'		=> $limit
						)),
					)
			);
	
			if ($banip && $userip) {
				require_once(R_P.'admin/cache.php');
			
				$rs = $this->db->get_one("SELECT db_name,db_value FROM pw_config WHERE db_name='db_ipban'");
				if (strpos($rs['db_value'], $userip) === false) {//IPδ����ֹ�Ÿ���
					$rs['db_value'] .= ($rs['db_value'] ? ',' : '').$userip;
					setConfig('db_ipban', $rs['db_value']);
					updatecache_c();
				}
			}
			$fid && $foruminfo = L::forum($fid);
			$log = array(
				'type'      => 'banuser',
				'username1' => $userinfo['username'],
				'username2' => $GLOBALS['windid'],
				'field1'    => $fid,
				'field2'    => '',
				'field3'    => '',
				'descrip'   => 'banuser_descrip',
				'timestamp' => $GLOBALS['timestamp'],
				'ip'        => $GLOBALS['onlineip'],
				'tid'		=> $tid,
				'forum'		=> $foruminfo['name'],
				'subject'	=> '',
				'affect'	=> '',
				'reason'	=> stripslashes($reason)
			);
			require_once(R_P.'require/writelog.php');
			writelog($log);
			if($foruminfo && $foruminfo['allowhtm']){
					$StaticPage = L::loadClass('StaticPage');
					$StaticPage->update($tid);
			}
			$this->clearUserCache($uid);
		}else{
			return 'banuser_failed';
		}
		return true;
	}

	/**
	 * ���ĳ���û�
	 * 
	 * @param int $uid �û�id
	 * @param array $params
	 * 				ifmsg	�Ƿ�����Ϣ 1-��,0-��
	 * 				reason	����ԭ��
	 * 				fid		���ID
	 * 				tid		
	 */
	function banfree($uid,$params = array()){
		$uid = intval($uid);
		$tid = intval($params['tid']);
		$pid = intval($params['pid']);
		$ifmsg = intval($params['ifmsg']);
		$reason = $params['reason'];
		
		$banuserDb = $this->_getBanUserDB();
		$userService = L::loadClass('UserService', 'user'); /* @var $userService PW_UserService */
		$userinfo = $userService->get($uid);
		if(!$userinfo) return 'undefined_action';
		$GLOBALS['username'] = $userinfo['username'];
		if($banuserDb->deleteByUserId($uid)){
			//�������
			$userService->setUserStatus($uid, PW_USERSTATUS_BANUSER, false);
			//* $userinfo['groupid'] == 6 && $this->db->update("UPDATE `pw_members` SET groupid='-1' WHERE `uid`=".S::sqlEscape($uid));
			$userinfo['groupid'] == 6 && pwQuery::update('pw_members', 'uid=:uid', array($uid), array('groupid'=>-1));
			$ifmsg && M::sendNotice(
					array($userinfo['username']),
					array(
						'title' => getLangInfo('writemsg','banuser_free_title'),
						'content' => getLangInfo('writemsg','banuser_free_content',array(
							'reason'	=> stripslashes($reason),
							'manager'	=> $GLOBALS['windid']
						))
					)
			);
			/*����ǰ����ԣ���ȡ������Ϣ*/
			if($tid > 0){
				$postInfo = $this->getPostInfo($tid,$pid);
				if($postInfo === false) return 'banuser_post_data_error';
				$fid = $postInfo['fid'];
				$userip = $postInfo['userip'];
				
				//�����ֹ��IP/
				require_once(R_P.'admin/cache.php');
				$ipban=$this->db->get_one("SELECT db_value FROM pw_config WHERE db_name='db_ipban'");
				$bannedIps = explode(',', $ipban['db_value']);
				if (in_array($userip, $bannedIps)) {
					$bannedIps = array_filter(str_replace($userip, '', $bannedIps));
					setConfig('db_ipban', implode(',', $bannedIps));
					updatecache_c();
				}
				$fid && $foruminfo = L::forum($fid);
				if($foruminfo){
					//omit write log
					if ($foruminfo['allowhtm']) {
						$StaticPage = L::loadClass('StaticPage');
						$StaticPage->update($tid);
					}
				}
			}
			$this->clearUserCache($uid);
		}else{
			//δ������
			return 'banuser_notfound';
		}
		return true;
	}
	
	/**
	 * ��ȡ������Ϣ
	 * @param $tid
	 * @param $pid
	 */
	function getPostInfo($tid,$pid=0){
		$tid = intval($tid);
		$pid = intval($pid);
		if($tid > 0){
			//* $threadService = L::loadClass('threads', 'forum'); /* @var $threadService PW_Threads */
			//* $threadInfo = $threadService->getThreads($tid,true);
			
			$_cacheService = Perf::gatherCache('pw_threads');
			$threadInfo = $_cacheService->getThreadAndTmsgByThreadId($tid);		
			
			if(!$threadInfo){
				return false;
			}
			$fid = $threadInfo['fid'];
			$userip = $threadInfo['userip'];
			
			//�ظ�
			if($pid > 0){
				$postTable = GetPtable($threadInfo['ptable']);
				$postInfo = $this->db->get_one(
					"SELECT authorid,userip FROM $postTable 
						WHERE pid= ".S::sqlEscape($pid)." 
						AND tid=".S::sqlEscape($tid). "
						AND authorid= ".S::sqlEscape($uid)
				);
				if(!$postInfo){
					return false;
				}
				$userip = $postInfo['userip'];
			}
			return array('fid'=>$fid,'userip'=>$userip);
		}
		return false;
	}
	
	/**
	 * @return PW_BanUserDB
	 */
	function _getBanUserDB() {
		return L::loadDB('BanUser', 'user');
	}
	
	/*
	 * ���datastore,usercache����Ϣ
	 * */
	function clearUserCache($uid){
		//* $_cache = getDatastore();
		//* $_cache->delete('UID_'.$uid);
		$userCacheService = L::loadClass('UserCache', 'user'); /* @var $userCacheService PW_UserCache */
		$userCacheService->delete($uid);
	}
}