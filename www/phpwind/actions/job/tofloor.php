<?php
!defined('P_W') && exit('Forbidden');

S::gp(array('cyid', 'floor'), 'GP', 2);
$linkurl = "read.php?tid=$tid&ds=1";
$page = 1;
$tid = intval($tid);
$floor = intval($floor);
(!$floor || !$tid) && Showmsg('data_error');
//������Ϣ
$_cacheService = Perf::gatherCache('pw_threads');
$read = $_cacheService->getThreadAndTmsgByThreadId($tid);
S::isArray($read) or Showmsg('data_error');
$read['replies'] < $floor && Showmsg('¥������������');
$fid = $read['fid'];
//foruminfo
$foruminfo = L::forum($fid);
$forumset = $foruminfo['forumset'];

//��������
$replayOrder = GetCookie('rorder');
if ($replayOrder && is_array($replayOrder) && array_key_exists($tid,$replayOrder)) {
	$orderby = $replayOrder[$tid] == 'desc' ? 'desc' : 'asc';
} else {
	$forumset['replayorder'] && $orderby = $forumset['replayorder'] == '1' ? 'asc' : 'desc';
	$threadorder = bindec(getstatus($read['tpcstatus'],4).getstatus($read['tpcstatus'],3));
	$threadorder && $threadorder != 3 && $orderby = $threadorder == '1' ? 'asc' : 'desc';
}
!$orderby && $orderby = 'asc';

//�����ö���
$topNum = $read['topreplays'] ? $read['topreplays'] : 0; 
//����
if ($orderby == 'desc') {
	$adjustNum = $read['replies'] - $floor;
	$topNum + $adjustNum >= $db_readperpage && $adjustNum += 1;
	$limit = $read['replies'] - $floor;
} else {
	$adjustNum = $floor + ($floor >= $db_readperpage ? 1 : 0);
	$limit = $floor-1;
}


$pw_posts = GetPtable('N', $tid);
$pid = $db->get_value("SELECT pid FROM $pw_posts WHERE tid=$tid AND ifcheck=1 ORDER by ifreward DESC,postdate $orderby ".S::sqlLimit($limit,1));
$page = ceil(($topNum + $adjustNum)/$db_readperpage);
$headerUrl = "$linkurl&toread=1&page=$page#$pid";
ObHeader($headerUrl);