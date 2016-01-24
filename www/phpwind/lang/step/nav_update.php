<?php
!defined('PW_UPLOAD') && exit('Forbidden');
$navConfigService = L::loadClass('navconfig', 'site'); /* @var $navConfigService PW_NavConfig */
$adds = 0;


//4.1.	ԭ������

//ԭ�������������������������������Ż��ĵ���������Ҫ�޸�
$areaNav = $navConfigService->getByKey('area');
$areaNavId = $areaNav && isset($areaNav['nid']) ? $areaNav['nid'] : 0;
$navConfigService->update($areaNavId, array('floattype' => 'cross', 'listtype' => 'space', 'selflisttype' => 'space'));

//�����������ӣ��Ż�Ƶ��
$channelService = L::loadClass('channelService', 'area');
foreach ($channelService->getChannels() as $alias => $channel) {
	if (!$navConfigService->getByKey('area_' . $alias)) {
		$link = "index.php?m=area&alias=" . $alias;
		$isShow = in_array($alias, array('bbsindex', 'home')) ? 0 : 1;
		$adds += (bool) $navConfigService->add(PW_NAV_TYPE_MAIN, array('nkey' => 'area_' . $alias, 'pos' => '-1', 'title' => $channel['name'], 'link' => $link, 'view' => $areaNav['view']++, 'upid' => 0, 'isshow' => $isShow));
	}
}
//�����������ӣ�Ⱥ��ۺ�
$adds += (bool)$navConfigService->add(PW_NAV_TYPE_MAIN, array('nkey' => 'group', 'pos' => '-1', 'title' => 'Ⱥ��', 'style' => '', 'link' => 'group.php', 'alt' => '', 'target' => 0, 'view' => 3, 'upid' => 0, 'isshow' => 1));

//4.2.	ԭģʽ����

//ԭ�Ż�ģʽ�������������Ϊ�������С��Ż����Ķ���������
$db->update("UPDATE pw_nav SET type=".pwEscape(PW_NAV_TYPE_MAIN).", upid=".pwEscape($areaNavId)." WHERE type='area_navinfo'");

//ԭ��̳ģʽ�������������Ϊ�����Ҳർ����
$db->update("UPDATE pw_nav SET type=".pwEscape(PW_NAV_TYPE_HEAD_RIGHT).", pos='bbs,area' WHERE type='bbs_navinfo'");

//ԭȦ��ģʽ������ɾ����
$db->update("DELETE FROM pw_nav WHERE type='o_navinfo'");

//4.3. 	ԭ�����������������Ϊ������ർ����
$db->update("UPDATE pw_nav SET type=".pwEscape(PW_NAV_TYPE_HEAD_LEFT)." WHERE type='head'");

//4.4.	ԭ�ײ��������Զ������ݱ������������Ӽ���Ĭ�ϵ�������ϵ���ǡ���ͼ�桢�ֻ����
$db->update("DELETE FROM pw_nav WHERE type=".pwEscape(PW_NAV_TYPE_FOOT)." AND link IN (".pwImplode(array($db_ceoconnect, 'simple/', 'm/index.php')).")");
$defaults = array(
	array('pos' => '-1', 'title' => '��ϵ����', 'link' => $db_ceoconnect, 'view'=>1, 'target' => 0, 'isshow' => 1),
	array('pos' => '-1', 'title' => '��ͼ��', 'link' => 'simple/', 'view'=>2, 'target' => 0, 'isshow' => 1),
	array('pos' => '-1', 'title' => '�ֻ����', 'link' => 'm/', 'view'=>3, 'target' => 0, 'isshow' => 1),
);
foreach ($defaults as $key => $value) {
	$adds += (bool)$navConfigService->add(PW_NAV_TYPE_FOOT, $value);
}
