<?php
!defined('P_W') && exit('Forbidden');
/**
 * �Ż�ǰ̨�������
 * @author liuhui @2010-3-10
 */
S::gp(array("invokename","channelid","action","selid"));
$invokename = pwConvert(urldecode($invokename),$db_charset,'utf8');
$levelService = L::loadclass("AreaLevel", 'area');
if(empty($action)){
	/*�Ż��������*/
	$portalPageService = L::loadClass('portalpageservice', 'area');
	$channelid = $portalPageService->getSignForManage($channelid);
	
	$invokeService = L::loadClass('invokeservice', 'area');
	$invokeInfo = $invokeService->getInvokeByName($invokename);
	
	if (!$invokeInfo) {
		echo $levelService->language("area_no_invoke");
	}

	$invokename = trim(strip_tags($invokename));
	$level = $levelService->getAreaLevel($winduid,$channelid,$invokename);
	if($level){
		//��ȡƵ������
		list($title,$baseUrl) = array("ģ�����ݹ���-".$invokeInfo['title'],"mode.php?m=area&q=manage&invokename=".urlencode($invokename)."&channelid=".$channelid);
		require_once areaLoadFrontView('area_dialog');
	}else{
		echo $levelService->language("area_no_level");
	}
	ajax_footer();
}elseif($action == "pushto"){
	/*��������/�Ƽ�����*/
	$level = $levelService->getAreaLevelByUserId($winduid);
	if($level){
		list($title,$baseUrl) = array("�������ͻ��Ƽ�","mode.php?m=area&q=manage&action=pushto&selid=$selid");
		require_once areaLoadFrontView('area_dialog');
	}else{
		echo $levelService->language("area_no_pushto");
	}
	ajax_footer();
}
