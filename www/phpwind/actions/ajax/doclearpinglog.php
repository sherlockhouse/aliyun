<?php
!defined('P_W') && exit('Forbidden');

S::gp(array(
	'fid',
	'tid',
	'pid'
), null, 2);
//$db->update("UPDATE pw_pinglog SET ifhide=1 WHERE fid=" . S::sqlEscape($fid) . " AND tid=" . S::sqlEscape($tid) . " AND pid=" . S::sqlEscape($pid) . " AND pinger=" . S::sqlEscape($windid));
pwQuery::update('pw_pinglog', 'fid=:fid  AND tid=:tid AND pid=:pid AND pinger=:pinger', array($fid,$tid,$pid,$windid), array('ifhide'=>1));
if ($db->affected_rows()) {
	echo "������ֶ�̬�ɹ�!";
	$pingService = L::loadClass("ping", 'forum');
	$pingService->update_markinfo($fid, $tid, $pid);
} else {
	echo "û����Ҫ��յ����ֶ�̬";
}
ajax_footer();
