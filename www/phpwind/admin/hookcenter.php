<?php
!function_exists('adminmsg') && exit('Forbidden');
$basename="$admin_file?adminjob=hookcenter";
$db_hookset = $db_hookset ? $db_hookset : array();
$hooks = $usedHooks = pwHook::getSystemHooks();
$unUsedHooks = array();

if ($fp = opendir(R_P.'hook')) {
	while (($hookdir = readdir($fp))) {
		if (strpos($hookdir,'.')!==false || in_array($hookdir,pwHook::getSystemHooks())) continue;
		if (isset($db_hookset[$hookdir])) {
			$usedHooks[] = $hookdir;
		} else {
			$unUsedHooks[] = $hookdir;
		}
		$hooks[] = $hookdir;
	}
	closedir($fp);
}

if (!$action) {
	$hookmode = array("","");
	($db_hookmode == 0) ? $hookmode[0] = "checked" : $hookmode[1] = "checked";
	include PrintEot('hookcenter');exit;
} elseif ($action=='install') {
	S::gp(array('hook'),'G');
	if (!$hook || !in_array($hook,$hooks)) adminmsg('��չ������');
	if (isset($db_hookset[$hook])) adminmsg('����չ�Ѱ�װ');
	$db_hookset[$hook] = 1;
	setConfig('db_hookset', $db_hookset);
	updatecache_c();
	updateHookCache($hook);
	adminmsg('operate_success');
} elseif ($action=='uninstall') {
	S::gp(array('hook'),'G');
	if (!$hook || !in_array($hook,$hooks)) adminmsg('��չ������');
	if (!isset($db_hookset[$hook])) adminmsg('����չδ��װ');
	unset($db_hookset[$hook]);
	setConfig('db_hookset', $db_hookset);
	updatecache_c();
	
	adminmsg('operate_success');
} elseif ($action=='updatecache') {
	S::gp(array('hook'),'G');
	if (!$hook || !in_array($hook,$hooks)) adminmsg('��չ������');
	if (!pwHook::checkHook($hook)) adminmsg('����չδ��װ');
	updateHookCache($hook);
	adminmsg('operate_success');
} elseif ($action=='setmode') {
	S::gp(array('hookmode'),'P');
	$hookmode = $hookmode ? 1 : 0;
	setConfig('db_hookmode', $hookmode);
	updatecache_c();
	adminmsg('operate_success');
}

function updateHookCache($hook) {
	L::loadClass('hook','hook',false);
	$pwHook = new PW_Hook($hook);
	$pwHook->packHookFiles();
}