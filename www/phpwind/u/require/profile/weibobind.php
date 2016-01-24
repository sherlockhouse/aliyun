<?php
!function_exists('readover') && exit('Forbidden');

InitGP(array('t', 'type'));

$siteBindService = L::loadClass('WeiboSiteBindService', 'sns/weibotoplatform/service'); /* @var $siteBindService PW_WeiboSiteBindService */
if (!$siteBindService->isOpen()) Showmsg('վ�㻹δ�����ʺ�ͨӦ��');

if (empty($t)) {
	$userBindService = L::loadClass('WeiboUserBindService', 'sns/weibotoplatform/service'); /* @var $userBindService PW_WeiboUserBindService */
	$userBindList = $userBindService->getBindList($winduid);
	
	$weiboLoginService = L::loadClass('WeiboLoginService', 'sns/weibotoplatform/service'); /* @var $weiboLoginService PW_WeiboLoginService */
	$isNotResetPassword = $weiboLoginService->isLoginUserNotResetPassword($winduid);

	$syncer = L::loadClass('WeiboSyncer', 'sns/weibotoplatform'); /* @var $syncer PW_WeiboSyncer */
	$syncSetting = $syncer->getUserWeiboSyncSetting($winduid);
	ifchecked('article_issync', $syncSetting['article']);
	ifchecked('diary_issync', $syncSetting['diary']);
	ifchecked('photos_issync', $syncSetting['photos']);
	ifchecked('group_issync', $syncSetting['group']);
	ifchecked('transmit_issync', $syncSetting['transmit']);
	ifchecked('comment_issync', $syncSetting['comment']);
	
	require_once(R_P.'require/showimg.php');
	list($faceurl) = showfacedesign($winddb['icon'],1,'m');
	
	require_once uTemplate::printEot('profile_weibobind');
	pwOutPut();
} elseif ($t == 'tounbind') {
	define('AJAX', 1);
	
	if (!$siteBindService->isBind($type)) Showmsg('վ�㻹δ֧�ָ�����վ��İ󶨣����߰����ʹ���');
	
	$userBindService = L::loadClass('WeiboUserBindService', 'sns/weibotoplatform/service'); /* @var $userBindService PW_WeiboUserBindService */
	if (!$userBindService->isBind($winduid, $type)) Showmsg('�㻹δ�󶨸�վ�㣬���贴������');
	
	$weiboLoginService = L::loadClass('WeiboLoginService', 'sns/weibotoplatform/service'); /* @var $weiboLoginService PW_WeiboLoginService */
	$isNotResetPassword = $weiboLoginService->isLoginUserNotResetPassword($winduid);
	
	require_once uTemplate::printEot('profile_weibobind_ajax');
	ajax_footer();
} elseif ($t == 'unbind') {
	define('AJAX', 1);
	
	if (!$siteBindService->isBind($type)) Showmsg('վ�㻹δ֧�ָ�����վ��İ󶨣����߰����ʹ���');
	
	$userBindService = L::loadClass('WeiboUserBindService', 'sns/weibotoplatform/service'); /* @var $userBindService PW_WeiboUserBindService */
	if (!$userBindService->isBind($winduid, $type)) Showmsg('�㻹δ�󶨸�վ�㣬������');
	
	$weiboLoginService = L::loadClass('WeiboLoginService', 'sns/weibotoplatform/service'); /* @var $weiboLoginService PW_WeiboLoginService */
	if ($weiboLoginService->isLoginUserNotResetPassword($winduid)) Showmsg('����ʺ�δ�������룬�봴��������ٽ����');
	
	$isSuccess = $userBindService->unbind($winduid, $type);
	
	if (!$isSuccess) Showmsg("���ʧ�ܣ�������");
	
	echo "��Ľ������ɹ�\tjump\tprofile.php?action=weibobind";
	ajax_footer();
} elseif ($t == 'resetandunbind') {
	define('AJAX', 1);
	
	if (!$siteBindService->isBind($type)) Showmsg('վ�㻹δ֧�ָ�����վ��İ󶨣����߰����ʹ���');
	
	$userBindService = L::loadClass('WeiboUserBindService', 'sns/weibotoplatform/service'); /* @var $userBindService PW_WeiboUserBindService */
	if (!$userBindService->isBind($winduid, $type)) Showmsg('�㻹δ�󶨸�վ�㣬������');
	
	PostCheck();
	InitGP(array('resetpwd', 'resetpwd_repeat'), 'P');
	$isSuccess = weiboResetUserPassword($winduid, $resetpwd, $resetpwd_repeat);
	if (!$isSuccess) Showmsg('���Ѵ������룬�����¾�������ͬ');
	
	$isSuccess = $userBindService->unbind($winduid, $type);
	echo $isSuccess ? "��������ͽ������ɹ�" : "�����Ѵ�������������ʧ�ܣ�������";
	echo "\tjump\tprofile.php?action=weibobind";
	ajax_footer();
} elseif ($t == 'setsync') {
	PostCheck();
	InitGP(array('article_issync', 'diary_issync', 'photos_issync', 'group_issync', 'transmit_issync', 'comment_issync'), 'P', 2);
	$syncSetting = array(
		'article' => (bool) $article_issync,
		'diary' => (bool) $diary_issync,
		'photos' => (bool) $photos_issync,
		'group' => (bool) $group_issync,
		'transmit' => (bool) $transmit_issync,
		'comment' => (bool) $comment_issync,
	);
	$syncer = L::loadClass('WeiboSyncer', 'sns/weibotoplatform'); /* @var $syncer PW_WeiboSyncer */
	$syncer->updateUserWeiboSyncSetting($winduid, $syncSetting);

	refreshto('profile.php?action=weibobind','operate_success', 2, true);
} elseif ($t == 'resetpwd') {
	$weiboLoginService = L::loadClass('WeiboLoginService', 'sns/weibotoplatform/service'); /* @var $weiboLoginService PW_WeiboLoginService */
	$isNotResetPassword = $weiboLoginService->isLoginUserNotResetPassword($winduid);
	if (!$isNotResetPassword) Showmsg('���Ѿ��������룬����Ҫ�ٴδ���');
	
	require_once uTemplate::printEot('profile_weibobind');
	pwOutPut();
} elseif ($t == 'setpassword') {
	$weiboLoginService = L::loadClass('WeiboLoginService', 'sns/weibotoplatform/service'); /* @var $weiboLoginService PW_WeiboLoginService */
	$isNotResetPassword = $weiboLoginService->isLoginUserNotResetPassword($winduid);
	if (!$isNotResetPassword) Showmsg('���Ѿ��������룬����Ҫ�ٴδ���');
	
	PostCheck();
	InitGP(array('resetpwd', 'resetpwd_repeat'), 'P');
	$isSuccess = weiboResetUserPassword($winduid, $resetpwd, $resetpwd_repeat);
	if (!$isSuccess) Showmsg('���Ѵ������룬�����¾�������ͬ');
	
	refreshto('profile.php?action=weibobind','��������ɹ�!', 2, true);
} elseif ($t == 'bindsuccess') {
	extract(L::style('',$skinco));
	
	$msg_info = '���ʺųɹ������ڽ��Զ��رգ�';
	require_once uTemplate::printEot('profile_privacy_bindsuccess');
	pwOutPut();
} elseif ($t == 'callback') {
	$userBindService = L::loadClass('WeiboUserBindService', 'sns/weibotoplatform/service'); /* @var $userBindService PW_WeiboUserBindService */
	
	$params = array_merge($_GET, $_POST);
	unset($params['action'], $params['t']);
	$isSuccess = $userBindService->callback($winduid, $params);
	if (true !== $isSuccess) Showmsg($isSuccess ? $isSuccess : '��ʧ�ܣ�������');
	
	ObHeader('profile.php?action=weibobind&t=bindsuccess');
}

function ifchecked($out, $var) {
	$GLOBALS[$out] = $var ? ' checked' : '';
}
function weiboResetUserPassword($userId, $password, $repeatPassword) {
	global $db_ckpath, $db_ckdomain;
	
	if ('' == $password || '' == $repeatPassword) Showmsg('�������벻��Ϊ��');
	
	$rg_config  = L::reg();
	list($rg_regminpwd,$rg_regmaxpwd) = explode("\t", $rg_config['rg_pwdlen']);
	$register = L::loadClass('Register', 'user');
	$register->checkPwd($password, $repeatPassword);
	
	$weiboLoginService = L::loadClass('WeiboLoginService', 'sns/weibotoplatform/service'); /* @var $weiboLoginService PW_WeiboLoginService */
	$isSuccess = $weiboLoginService->resetLoginUserPassword($userId, $password);
	if (!$isSuccess) return false;
	
	$userService = L::loadClass('UserService', 'user'); /* @var $userService PW_UserService */
	$user = $userService->get($userId);
	Cookie("winduser",StrCode($userId."\t".PwdCode($user['password'])."\t".$user['safecv']));
	Cookie("ck_info",$db_ckpath."\t".$db_ckdomain);
	Cookie('lastvisit','',0);
	//�Զ���ȡѫ��_start
	require_once(R_P.'require/functions.php');
	doMedalBehavior($userId,'continue_login');
	//�Զ���ȡѫ��_end
	return true;
}
