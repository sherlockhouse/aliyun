<?php
!defined('R_P') && exit('Forbidden');

//if (!$winduid) ObHeader('index.php?m=o');
if (!$winduid) {
	Showmsg('not_login');
}
$USCR = 'user_home';
define('WEIBO_RELATION_NUM', 200); //΢��������ϵ��
$perpage = 20;
require_once(R_P . 'u/lib/space.class.php');
require_once(R_P.'require/functions.php');
//* include_once pwCache::getPath(D_P.'data/bbscache/level.php');
//* include_once pwCache::getPath(D_P.'data/bbscache/o_config.php');
extract(pwCache::getData(D_P.'data/bbscache/level.php', false));
extract(pwCache::getData(D_P.'data/bbscache/o_config.php', false));
extract(pwCache::getData(D_P.'data/bbscache/dbreg.php', false));
require_once(R_P.'require/credit.php'); 
$newSpace = new PwSpace($winduid);
$space = $newSpace->getInfo(); 
$finishPercentage = getMemberInfoFinishPercentage($winduid);

//$winddb['medals'] && $listmedals = getMedalIconsByUid($winduid);
//ѫ��
if ($db_md_ifopen) { 
	$medalService = L::loadClass('MedalService', 'medal');
	$medalList    = $medalService->getUserMedals($winduid,'all',$winddb['medals']); //��ȡ��Ա�Ѿ�ӵ�е�ѫ��
}
//������ʾ��
$usercredit = array(                   	
	'postnum'	=> $winddb['postnum'],
	'digests'	=> $winddb['digests'],
	'rvrc'		=> $winddb['rvrc'],
	'money'		=> $winddb['money'],
	'credit'	=> $winddb['credit'],
	'currency'	=> $winddb['currency'],
	'onlinetime'=> $winddb['onlinetime']
);
foreach ((array)$credit->get($winduid,'CUSTOM') as $key => $value) {  //��Ǯ�����֡�����
	$usercredit[$key] = $value;
}

$upgradeset  = unserialize($db_upgrade);
$totalcredit = CalculateCredit($usercredit,$upgradeset); 

if ($o_punchopen) {
	$punchReward  = unserialize($o_punch_reward);
	$punch_moneyname = $credit->cType[$punchReward['type']];
	$reloadMoney = $usercredit[$punchReward['type']]+$punchReward['num'];
}
$moneyType = $punch_moneyname ? $punch_moneyname : $db_moneyname; //��ʾ��Ǯ����
$moneyNum = $o_punchopen ? $usercredit[$punchReward['type']] : $winddb['money']; //��ʾ��Ǯ����

$last = $percent = 0;
!$lneed && $lneed = array();
$copyLneed = $lneed;
foreach ($lneed as $key=>$value){
	if($value > $totalcredit){
		$last = $value;break;
	} elseif ($totalcredit >= $value && $value == end($copyLneed)) {
		$last = $value;
		break;
	}
}
$percent = $last ? ceil(($totalcredit/$last) * 100) : 0;
require_once R_P . 'require/showimg.php';
list($faceurl) = showfacedesign($winddb['icon'],1,'m'); //ͷ��

$weiboService = L::loadClass('weibo','sns'); /* @var $weiboService PW_Weibo */
$weiboList = $weiboService->getUserAttentionWeibos($winduid, array(), 1, 20);
$weiboCount = $weiboService->getUserAttentionWeibosCount($winduid, array());
$weiboCount > WEIBO_RELATION_NUM && $weiboService->deleteAttentionRelation($winduid, $weiboCount);
$o_weibopost == '0' && $weiboLiveList = $weiboService->getWeiboLives(21);//������ֱ��
$weiboCount > WEIBO_RELATION_NUM && $weiboCount = WEIBO_RELATION_NUM;
$pages = numofpage($weiboCount, 1, ceil($weiboCount/20), 'apps.php?q=weibo&do=attention&', 10, 'weiboList.filterWeibo');
if (!$db_toolbar) {
	$pwForumList = array();
	//* include_once pwCache::getPath(D_P.'data/bbscache/forumlist_cache.php');
	pwCache::getData(D_P.'data/bbscache/forumlist_cache.php');
	if ($pwForumAllList && $GLOBALS['groupid'] == 3) {
		$pwForumList = array_merge($pwForumList,$pwForumAllList);
	}
}

//���Ż���
$topicService = L::LoadClass('topic','sns'); /* @var $topicService PW_Topic */
$weiboHotTopics = $topicService->getWeiboHotTopics();
$topicHot = array();
$n = 0;
foreach($weiboHotTopics as $key=>$topic){
	if(++$n > 6 )break;
	$topic['urlTopic'] = urlencode($topic['topicname']);
	$topicHot[$key] = $topic;
}

(empty($winddb['honor']) || !$_G['allowhonor']) && $winddb['honor'] = getLangInfo('other','whattosay');

//����
if ($db_job_isopen) {
	$isApplyJob = false;
	$jobService = L::loadclass("job", 'job');
	$myJobList = array();
	$myJobList = $jobService->appendJobDetailInfo($jobService->getAppliedJobs($winduid));
	if (count($myJobList)) $myJobList = array_slice($myJobList, 0, 2);
	if (empty($myJobList)) {
		$isApplyJob = true;
		$myJobList = $jobService->appendJobDetailInfo($jobService->getCanApplyJobs($winduid, $groupid));
		if (count($myJobList)) $myJobList = array_slice($myJobList, 0, 2);
	}
}
list($isPunch,$showPunch) = isPunchRoutine();//ÿ�մ�
if ($showPunch) {
	$behaviorService = L::loadClass('behaviorservice','user');
	$punchBehavior = $behaviorService->getBehaviorStatistic($winduid,'continue_punch');
	if ($isPunch) {
		//δ��
		$unPunchDays = $winddb['punch'] > 0 ? ceil(($tdtime - PwStrtoTime(get_date($winddb['punch'],'Y-m-d'))) / 86400) : 1;
		$punchText =  $unPunchDays > 1 ? "{$unPunchDays}��δ��" : "ÿ�մ�";
	} else {
		//�Ѵ�
		$punchBehavior['num'] or $punchBehavior['num'] = 1; 
		$punchText = "����{$punchBehavior['num']}���";
	}
		
}

$modelList = array('recommendUsers' => 3,'visitor' => 6, 'friendsBirthday' => array('num' => 3,'expire' => 21600 ), 'tags' => 8 );
$o_weibopost == '0' && $modelList['friend'] = 6;
$spaceData = $newSpace->getSpaceData($modelList);
$o_weibopost == '0' && $myFriends = $spaceData['friend'];//�ҵĺ���
$latestVisits = $spaceData['visitor'];//����ÿ�
$recommendUsers = $spaceData['recommendUsers'];//���Ƽ���עģ��
$birthdays = $spaceData['friendsBirthday'];//��������
$tmpmemberTags = $spaceData['tags'];//���˱�ǩ
if (S::isArray($tmpmemberTags)) {
	$memberTagsService = L::loadClass('memberTagsService', 'user');
	$memberTags = $memberTagsService->makeClassTags($tmpmemberTags);
}
/* platform weibo app */
$isSiteBindWeibo = false;
$siteBindService = L::loadClass('WeiboSiteBindService', 'sns/weibotoplatform/service'); /* @var $siteBindService PW_WeiboSiteBindService */
if ($siteBindService->isOpen()) {
	$isSiteBindWeibo = true;
	$userBindService = L::loadClass('WeiboUserBindService', 'sns/weibotoplatform/service'); /* @var $userBindService PW_WeiboUserBindService */
	$userBindList = $userBindService->getBindList($winduid);
	$isUserBindOne = $userBindService->isBindOne($winduid);
}

/*������������Ϣ���ĵĻ���*/
//����Ϣ������Ϣ��Ŀͳ��
/*
$messageServer = L::loadClass('message', 'message');
list($messageNumber,$noticeNumber,$requestNumber,$groupsmsNumber) = $messageServer->getUserStatistics($winduid);
$messageNum = $messageNumber+$noticeNumber+$requestNumber+$groupsmsNumber;
$messageNumber = $messageNumber ? '('.$messageNumber.')' : '';
$noticeNumber = $noticeNumber ? '('.$noticeNumber.')' : '';
$requestNumber = $requestNumber ? '('.$requestNumber.')' : '';
$groupsmsNumber = $groupsmsNumber ? '('.$groupsmsNumber.')' : '';
*/
require_once(uTemplate::printEot('user_home'));
pwOutPut();

function getMemberInfoFinishPercentage($userId) {
	$userService = L::loadClass('UserService', 'user'); /* @var $userService PW_UserService */
	$data = $userService->get($userId, true, false, true);
	$needFields = array('introduce', 'oicq', 'aliww', 'signature', 'msn', 'yahoo', 'site', 'location', 'honor', 'bday');
	foreach (L::config('customfield','customfield') as $field) {
		$needFields[] = 'field_' . $field['id'];
	}
	$total = count($needFields);
	$finish = 0;
	foreach ($needFields as $field) {
		if ('' != $data[$field]) $finish++;
	}
	return ceil(round($finish * 1.0 / $total, 3) * 100);
}

function isPunchRoutine(){
	global $o_punchopen,$o_punch_usergroup,$o_punch_reward,$groupid,$winddb,$tdtime;
	if(!$o_punchopen){
		return array(false,false);
	}
	$usergroup = ($o_punch_usergroup) ? explode(",",$o_punch_usergroup) : array();
	if($usergroup && !in_array($groupid,$usergroup)){
		return array(false,false);
	}
	list($todayStart,$todayEnd) = array($tdtime,$tdtime+86400);
	if($winddb['punch']>$todayStart && $winddb['punch']< $todayEnd){
		return array(false,true);
	}
	return array(true,true);
}
