<?php
!defined('P_W') && exit('Forbidden');

S::gp(array('uid'), 'GP');

//$uids = explode(',', $uids);
!$uid && Showmsg('guest_notcard');
require_once(R_P . 'u/require/core.php');

/*app*/
$db_maxnum = 12; //������app

$db_appsdb = $appsdb = array();
if ($db_siteappkey) {
	$app_array = array();
	$appclient = L::loadClass('appclient');
	$app_array = $appclient->userApplist($uid, $appids, 2);
	$appsdb = $appclient->getApplist();
}
/*
app ���ز�������
$db_server_url = 'http://apps.phpwind.net/';
$appsdb = array(
	0 => array('appid' => 2, 'name' => '����һ��'),
	1 => array('appid' => 4, 'name' => '��������'),
	2 => array('appid' => 5, 'name' => '��ͼƵ��'),
	3 => array('appid' => 6, 'name' => '����'),
);
*/
foreach ($appsdb as $value) {
	if ($app_array[$uid][$value['appid']]) {
		$value['ifshow'] = 1;
	} else {
		$value['ifshow'] = 0;
	}
	$db_appsdb[$value['appid']]['ifshow'] = $value['ifshow'];
	$db_appsdb[$value['appid']]['appid'] = $value['appid'];
	$db_appsdb[$value['appid']]['name'] = $value['name'];
}
/*app*/

//һҳ��ʾ����
require_once (R_P . 'require/showimg.php');
//* include_once pwCache::getPath(D_P . 'data/bbscache/level.php');
pwCache::getData(D_P . 'data/bbscache/level.php');
$userdb = array();
$app_with_count = array(
	'topic',
	'diary',
	'photo',
	'owrite',
	'group',
	'share'
);
$info = $db->get_one("SELECT m.uid,m.username,m.groupid,m.memberid,m.icon,m.oicq,m.aliww,m.honor,md.lastpost,md.thisvisit,md.f_num, s.visits,s.tovisits, ud.diary_lastpost,ud.photo_lastpost,ud.owrite_lastpost,ud.group_lastpost,ud.share_lastpost FROM pw_members m LEFT JOIN pw_memberdata md ON m.uid=md.uid LEFT JOIN pw_ouserdata ud ON m.uid=ud.uid LEFT JOIN pw_space s ON m.uid=s.uid WHERE m.uid=" . S::sqlEscape($uid));
list($info['icon']) = showfacedesign($info['icon'], 1, 's');

$friendService = L::loadClass('Friend', 'friend'); /* @var $friendService PW_Friend  */
$info['ismyfriend'] = $friendService->isFriend($winduid, $uid);

$attentionService = L::loadClass('Attention', 'friend'); /* @var $attentionService PW_Attention */
$info['isMyAttention'] = $attentionService->isFollow($winduid, $uid);

$info['systitle'] = $info['groupid'] == '-1' ? '' : $ltitle[$info['groupid']];
$info['memtitle'] = $ltitle[$info['memberid']];
foreach ($app_with_count as $key => $value) {
	$posttime = '';
	list(,$info['appcount'][$value]) = getPostnumByType($value, $info, true);
}

/*��������*/
$userCache = L::loadClass('Usercache', 'user');
$info['appinfo'] = $userCache->get($uid, array(
	'cardtopic'	=> 1,
	'carddiary'	=> 1,
	'cardphoto'	=> 4
));
uasort($info['appinfo'], "appcmp");
$info['app'] = $db_appsdb;

/*���ݺϲ�*/
require_once PrintEot('ajax');
ajax_footer();

function appcmp($a,$b) {
	return $a['postdate'] == $b['postdate'] ? 0 : ($a['postdate'] > $b['postdate'] ? -1 : 1);
}