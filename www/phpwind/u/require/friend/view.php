<?php
!defined('R_P') && exit('Forbidden');
empty($space) && Showmsg('�����ʵĿռ䲻����!');

if (!$newSpace->viewRight('index')) {
	Showmsg('�ÿռ�������˽����û��Ȩ�޲鿴!');
}
$basename = "u.php?a=$a&uid=$uid&";

$count  = 0;
$friendsService = L::loadClass('Friend', 'friend'); /* @var $friendsService PW_Friend */

/* �ҳ���¼�ߵĺ���array(0=>uid1,1=>uid2,.......n=>uidn)*/
$uids = array();

$count = (int)$friendsService->countUserFriends($uid);
$page > ceil($count/$db_perpage) && $page = ceil($count/$db_perpage);
$friends = $count ? $friendsService->findUserFriendsInPage($uid, $page, $db_perpage) : array();
foreach ($friends as $key => $friend) {
	$uids[] = $friend['uid'];
}

$attentionSerivce = L::loadClass('attention', 'friend'); /* @var $attentionSerivce PW_Attention */
$myAttentionUids = $attentionSerivce->getUidsInFollowListByFriendids($winduid, $uids);

foreach ($friends as $key => $friend) {
	if (!S::inArray($friend['uid'], $myAttentionUids)) continue;
	$friends[$key]['attention'] = true;
}

$pages = numofpage($count,$page,ceil($count/$db_perpage),"{$basename}");
require_once (uTemplate::printEot('space_friend'));
pwOutPut();
?>