<?php
!defined('P_W') && exit('Forbidden');
define('AJAX','1');
S::gp(array('type'));
if ($type == 'showcard') {
	S::gp(array('uid','username'));
	$uid = intval($uid);
	$cardInfo = getCardData($uid,$winduid,$username);
	$cardInfo['status'] = S::isArray($cardInfo) ? 'success' : 'fail';

	echo pwJsonEncode($cardInfo);
	ajax_footer();
}

/**
 * ��װС��Ƭ����
 *
 * @param int $uid �û�ID
 * @param int $winduid ��ǰ�û�id
 * @param bool $username �û���
 * @return array
 */
function getCardData($uid,$winduid,$username) {
	extract(pwCache::getData(R_P . "data/bbscache/level.php", false));
	if (($uid < 1 && !trim($username)) || $username == '�ο�' || $username == '����') return array('username'=>'�ο�','memtitle'=>$ltitle[2]);
	$userService = L::loadClass('UserService', 'user');
	if ($uid) {
		$userInfo = $userService->get($uid,true,true);
	} elseif ($username) {
		$userInfo = $userService->getByUserName($username,true,true);
	}
	if(!S::isArray($userInfo)) return array();

	require_once (R_P . 'require/showimg.php');
	list($faceimage) = showfacedesign($userInfo['icon'], 1, 's');
	
	$userInfo['groupid'] == '-1' && $userInfo['groupid'] = $userInfo['memberid'];
	!array_key_exists($userInfo['groupid'],(array)$lpic) && $userInfo['groupid'] = 8;
	
	$online = checkOnline($userInfo['thisvisit']);
	$onlineRead = $online ? getOnlineViewing($userInfo['uid'],$userInfo['username']) : array();
	$user = array (
		'mine'			=> $userInfo['uid'] == $winduid || !$winduid ? 0 : 1,
		'uid' 			=> $userInfo['uid'],
		'username'  	=> $userInfo['username'],
		'icon'  		=> $faceimage,
		'memtitle'		=> $ltitle[$userInfo['groupid']],
		'genderClass'	=> $userInfo['gender'],
		'viewTid' 		=> (isset($onlineRead['tid']) && $onlineRead['tid']) ? $onlineRead['tid'] : '',
		'viewFid'  		=> (isset($onlineRead['fid']) && $onlineRead['fid']) ? $onlineRead['fid'] : '',
		'online'		=> $online ? 1 : 0
	);
	
	$user['medals'] = getMedalsByUid($userInfo['uid']); // ѫ��

	$memberTagsService = L::loadClass('memberTagsService', 'user');
	$user['memberTags'] = $memberTagsService->makeClassTags($memberTagsService->getMemberTagsByUid($userInfo['uid'])); //��ǩ

	$attentionSerivce = L::loadClass('Attention', 'friend'); /* @var $attentionSerivce PW_Attention */
	$user['attention'] = $attentionSerivce->isFollow($winduid, $userInfo['uid']) ? 1 : 0; //��ע
	return $user;
}

/**
 * ��ȡ�û����ڿ�
 *
 * @param int $uid �û�ID
 * @param bool $username �û���
 * @return array
 */
function getOnlineViewing($uid,$username) {
	global $db_online,$_G;
	$uid = intval($uid);
	if ($uid < 1 || !$username || !$_G['allowviewonlineread']) return array();
	return $db_online ? getViewingByDB($uid) : getViewingByFile($username);
}

/**
 * ��DB��ȡ�û����ڿ�
 *
 * @param int $uid �û�ID
 * @return array
 */
function getViewingByDB($uid) {
	$onlineService = L::loadClass('OnlineService', 'user');
	$online = $onlineService->getOnlineUserByUserId($uid);
	if (!$online) return array();
	return array(
		'tid'  => $online['tid'] ? $online['tid'] : '',
		'fid'  => $online['fid'] ? $online['fid'] : ''
	);
}

/**
 * ��ȡѫ��
 *
 * @param int $uid �û�id
 * @return array
 */
function getMedalsByUid($uid) {
	global $db_md_ifopen;
	if (!$db_md_ifopen) return array();
	
	$medalService = L::loadClass('MedalService', 'medal');
	$openMedal    = $medalService->getAllOpenMedals(); 
	$userMedal    = $medalService->getUserMedals($uid); //��ȡ��Ա�Ѿ�ӵ�е�ѫ��
	$medalList    = $userMedalIdArr = array();
	
	foreach ($userMedal as $v) {
		$v['isuser'] = 1;
		$medalList[] = $v;
		$userMedalIdArr[] = $v['medal_id'];
	}
	return $medalList;
}

/**
 * ���ļ������ȡ�û����ڿ�
 *
 * @param int $username �û���
 * @return array
 */
function getViewingByFile($username) {
	$onlinedb = is_file(D_P.'data/bbscache/online.php') ? openfile(D_P.'data/bbscache/online.php') : array();
	if (!$onlinedb) return array();
	foreach ($onlinedb as $v) {
		if (!trim($v)) continue;
		$online = explode("\t",$v);
		if ($online[0] == $username) break;
	}
	return array(
		'tid'  => ( isset($online[4]) && $online[4] ) ? $online[4] : '',
		'fid'  => ( isset($online[3]) && $online[3] ) ? $online[3] : ''
	);
}

