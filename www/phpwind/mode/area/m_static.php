<?php
!defined('P_W') && exit('Forbidden');
define('AREA_STATIC','1');
S::gp(array('type'));
!$type && $type = 'channel';
//Ƶ����ط���
if ($type == 'channel') {
	define('AJAX',1);
	S::gp(array('alias'));
	$ChannelService = L::loadClass('channelService', 'area');
	$channelInfo = $ChannelService->getChannelInfoByAlias($alias);
	!$channelInfo && Showmsg('Ƶ��������');
	
	$areaLevelService = L::loadClass('arealevel', 'area');
	$ifEditAdmin = $areaLevelService->getAreaLevel($winduid,$channelInfo['id']);
	!$ifEditAdmin && Showmsg("��û��Ȩ�޸��´˾�̬ҳ");
	
	//��ù���ģ��
	
	require M_P.'index.php';
	if ($db_advertdb['Site.PopupNotice'] || $db_advertdb['Site.FloatLeft'] || $db_advertdb['Site.FloatRight'] || $db_advertdb['Site.FloatRand']) {
		require PrintEot('advert');
	}
	aliasStatic($channelInfo['alias']);
	echo getLangInfo('msg','operate_success');
	ajax_footer();exit;
} elseif ( $type == 'autostatic') {
	S::gp(array('alias'));
	//* include_once pwCache::getPath(D_P.'data/bbscache/area_config.php');
	pwCache::getData(D_P.'data/bbscache/area_config.php');
	if (!$alias || !$area_channels[$alias]) exit; //Ƶ��������
	if (!$area_statictime) exit;	//δ���ø���ʱ��
	$channelInfo = $area_channels[$alias];
	if ($db_distribute) {
		$file = S::escapePath(AREA_PATH.$alias.'/index.html');
		if ($channelInfo['statictime'] && pwFilemtime($file)<$channelInfo['statictime']) {
			require M_P.'index.php';
			aliasStatic($channelInfo['alias']);
			touch($file,$channelInfo['statictime']);
			exit;
		}
	}

	if ($channelInfo['statictime'] && $channelInfo['statictime']+$area_statictime*60>$timestamp) exit;
	
	require M_P.'index.php';
	aliasStatic($channelInfo['alias']);
	$chanelService->updateChannelStaticTime($alias,$timestamp);
} elseif ( $type == 'read') {
	S::gp(array('id'));
	$areaLevelService = L::loadClass('arealevel', 'area');
	$ifEditAdmin = $areaLevelService->getAreaLevelByUserId($winduid);
	!$ifEditAdmin && Showmsg("no_right_to_static");
}
?>