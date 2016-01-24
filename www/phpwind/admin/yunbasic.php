<?php
require_once (R_P . 'lib/cloudwind/cloudwind.class.php');
$_checkService = CloudWind::getPlatformCheckServerService ();
$cloudstatus = $_checkService->checkCloudWind ();
list ( $bbsname, $bbsurl, $bbsversion, $cloudversion ) = $_checkService->getSiteInfo ();
CLOUDWIND_SECURITY_SERVICE::gp ( array ('step' ) );
$step = (! $step && $cloudstatus < 9) ? (($cloudstatus == 1) ? 5 : (($cloudstatus == 3) ? 3 : (empty ( $step ) ? 1 : $step))) : $step;
if ($step == 1) {
	//show agreement
} elseif ($step == 2) {
	CLOUDWIND_SECURITY_SERVICE::gp ( 'agree' );
	if ($agree != 1)
		Showmsg ( '��û��ͬ�⡶phpwind�Ʒ���ʹ��Э�顷', $basename . '&step=1' );
	
	if (! $_checkService->checkHost ())
		Showmsg ( '�޷������Ʒ������������Ƿ�Ϊ���ػ���', $basename . '&step=1' );
	
	if (! $_checkService->getServerStatus ()) {
		list ( $fsockopen, $parse_url, $isgethostbyname, $gethostbyname ) = $_checkService->getFunctionsInfo ();
		list ( $searchHost, $searchIP, $searchPort, $searchPing ) = $_checkService->getSearchHostInfo ();
		list ( $defendHost, $defendIp, $defendPort, $defendPing ) = $_checkService->getDefendHostInfo ();
	} else {
		$step = 3;
	}
} elseif ($step == 3) {
	if (! $_checkService->getServerStatus ())
		Showmsg ( '�������ĩͨ��������ϵ��̳�ռ��ṩ�̽��' );
} elseif ($step == 4) {
	CLOUDWIND_SECURITY_SERVICE::gp ( array ('siteurl', 'sitename', 'bossname', 'bossphone', 'search', 'defend' ) );
	
	if (! $siteurl || ! $sitename || ! $bossname || ! $bossphone)
		Showmsg ( 'վ����Ϣ����д����', $basename . '&step=3' );
	
	if (! ($marksite = $_checkService->markSite ()))
		Showmsg ( '�Ʒ�����֤ʧ�ܣ�������', $basename . '&step=3' );
	
	if (! CloudWind::yunApplyPlatform ( $siteurl, $sitename, $bossname, $bossphone, $marksite )) {
		$marksite = $_checkService->markSite ( false );
		Showmsg ( '�����Ʒ���ʧ�ܣ��������������', $basename . '&step=3' );
	}
	(is_null ( $db_yun_model )) && $_checkService->setYunMode ( array () );
	$_checkService->initServices ( $search, $defend );
	$step = 5;
} else {
	$yundescribe = $_checkService->getYunDescribe ();
}
include PrintEot ( 'yunbasic' );

