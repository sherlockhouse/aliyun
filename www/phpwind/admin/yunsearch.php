<?php
/**
 * ��������̨����
 * @author L.IuHu.I@2011 developer.liuhui@gmail.com
 */
require_once(R_P.'lib/cloudwind/cloudwind.class.php');
$_service = CloudWind::getPlatformCheckServerService ();
if ($_service->checkCloudWind () < 9) {
	ObHeader ( $admin_file . '?adminjob=yunbasic' );
}
if (! $_service->getSiteScale ()) {
	Showmsg ( '�ף�����վ������û����������ѹ��������ʱ���ٿ������������ס�' );
}
if (! $db_yunsearch_search) {
	if ($_POST ['step'] == 2) {
		CLOUDWIND_SECURITY_SERVICE::gp ( array ('db_yunsearch_search' ), 'P', 2 );
		setConfig ( 'db_yunsearch_search', $db_yunsearch_search );
		updatecache_c ();
		Showmsg ( '���������óɹ� ' );
	}
	ifcheck ( $db_yunsearch_search, 'yunsearch_search' );
}
$yunManageUrl = $_service->getYunSearchManageUrl ();
include PrintEot ( 'yunsearch' );