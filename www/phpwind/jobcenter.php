<?php
define('SCR','jobcenter');
require_once('global.php');
!$winduid && Showmsg('not_login');
S::gp(array("action"));
if (!$db_job_isopen && $action != 'punch') {
	Showmsg('��Ǹ���û�����ϵͳ��û�п���');
}

$USCR = 'set_jobcenter';
//����
$homenavigation = array();
$navConfigService = L::loadClass('navconfig', 'site');
$homenavigation = $navConfigService->userHomeNavigation(PW_NAV_TYPE_MAIN, 'o');

if (file_exists(R_P . "u/require/jobcenter/jobcenter.php")) {
	require_once(R_P. 'u/require/core.php');
	require_once(R_P . 'u/lib/space.class.php');
	$newSpace = new PwSpace($winduid);
	$space =& $newSpace->getInfo();
	//* include_once pwCache::getPath(D_P . 'data/bbscache/o_config.php');
	pwCache::getData(D_P . 'data/bbscache/o_config.php');
	require_once S::escapePath(R_P . "u/require/jobcenter/jobcenter.php");
} else {
	Showmsg('undefined_action');
}
exit;
?>