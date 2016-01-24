<?php
!function_exists('adminmsg') && exit('Forbidden');
$siteBindInfoService = L::loadClass('WeiboSiteBindInfoService', 'sns/weibotoplatform/service'); /* @var $siteBindInfoService PW_WeiboSiteBindInfoService */

$templatesConfig = array(
	'article' => array(
		'title' => '��������',
		'description' => '{title}Ϊ���ӱ��� ; {content}Ϊ��������ժҪ ; {url}Ϊ���ӵ�ַ',
	),
	'diary' => array(
		'title' => '��־����',
		'description' => '{title}Ϊ��־����;  {content}Ϊ��־����ժҪ;  {url}Ϊ��־��ַ',
	),
	'group_active' => array(
		'title' => 'Ⱥ��',
		'description' => '{title}ΪȺ������; {content}ΪȺ������ժҪ; {url}ΪȺ����ַ',
	),
	'cms' => array(
		'title' => '��������',
		'description' => '{title}Ϊ���±���; {content}Ϊ��������ժҪ;  {url}Ϊ���µ�ַ',
	),
	'photos' => array(
		'title' => '���',
		'description' => '{photo_count}Ϊ��Ƭ����;  {url}Ϊ����ַ',
	),
	'group_photos' => array(
		'title' => 'Ⱥ�����',
		'description' => '{photo_count}Ϊ��Ƭ����;  {url}ΪȺ������ַ',
	),
);

InitGP(array('step', 'templates'));
if ($step == 'edit' && !empty($templates)) {
	$warningMessage = '';
	foreach ($templatesConfig as $key => $value) {
		if (!isset($templates[$key]) || '' == $templates[$key]) $warningMessage = '����΢��ģ�治��Ϊ��';
	}
	if (!$warningMessage) {
		$siteBindInfoService->saveWeiboTemplates($templates);
		$warningMessage = '��ϲ, ���óɹ���';
	}
}

$templatesSet = $siteBindInfoService->getWeiboTemplates();

include PrintTemplate('custom_weibo_template');
exit();
?>