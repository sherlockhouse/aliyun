<?php
!defined('PW_UPLOAD') && exit('Forbidden');

//INIT
$db->update("DELETE FROM pw_nav WHERE type!='head' AND type!='foot'");
$query = $db->query("SELECT * FROM pw_config WHERE db_name IN ('db_modes','db_mode','db_hackdb','db_modedomain')");
while ($rt = $db->fetch_array($query)) {
	$$rt['db_name'] = unserialize($rt['db_value']);
	if (empty($$rt['db_name'])) {
		$$rt['db_name'] = array();
	}
}

$navConfigService = L::loadClass('navconfig', 'site'); /* @var $navConfigService PW_NavConfig */
$adds = 0;


//MAIN
$view = 20;
$vieworder = array('area' => '1', 'bbs' => '2' ,'cms' => '5');
foreach ($db_modes as $key => $value) {
	if ($key == 'cloudcomputing' || $key == 'wap') continue;
	$pos = array();
	$value['ifopen'] = 1;
	if (isset($db_modedomain[$key]) && $db_modedomain[$key]) {
		$link = 'http://' . $db_modedomain[$key];
	} elseif ('o' == $key) {
		$pos = array('o');
		$link = 'u.php';
		$vieworder[$key] = 8;
		$value['ifopen'] = 0;
	} elseif ('cms' == $key) {
		$value['title'] = '��Ѷ';
		$link = 'index.php?m=' . $key;
	} elseif ('bbs' == $key) {
		$pos = array('bbs,area,cms,o,srch,group');
		$link = 'index.php?m=' . $key;
	} else {
		$link = 'index.php?m=' . $key;
	}
	$adds += (bool)$navConfigService->add(PW_NAV_TYPE_MAIN, array('nkey' => $key,
		'pos' => $pos ? $pos : array('bbs,area,cms,srch,group'),
		'title' => ($value['title'] ? $value['title'] : $value['m_name']),
		'style' => '',
		'link' => $link,
		'alt' => '',
		'target' => 0,
		'view' => $vieworder[$key] ? $vieworder[$key] : $view++,
		'upid' => 0,
		'isshow' => $value['ifopen'],
	));
}

//�����ĸ�Ƶ������
$adds += (bool)$navConfigService->add(PW_NAV_TYPE_MAIN, array('nkey' => 'area_baby', 'pos' => '-1', 'title' => '����', 'style' => '', 'link' => 'html/channel/baby', 'alt' => '', 'target' => 0, 'view' => 11, 'upid' => 2, 'isshow' => 1));
$adds += (bool)$navConfigService->add(PW_NAV_TYPE_MAIN, array('nkey' => 'area_delicious', 'pos' => '-1', 'title' => '��ʳ', 'style' => '', 'link' => 'html/channel/delicious', 'alt' => '', 'target' => 0, 'view' => 12, 'upid' => 2, 'isshow' => 1));
$adds += (bool)$navConfigService->add(PW_NAV_TYPE_MAIN, array('nkey' => 'area_auto', 'pos' => '-1', 'title' => '����', 'style' => '', 'link' => 'html/channel/auto', 'alt' => '', 'target' => 0, 'view' => 13, 'upid' => 2, 'isshow' => 1));
$adds += (bool)$navConfigService->add(PW_NAV_TYPE_MAIN, array('nkey' => 'area_decoration', 'pos' => '-1', 'title' => '��װ', 'style' => '', 'link' => 'html/channel/decoration', 'alt' => '', 'target' => 0, 'view' => 14, 'upid' => 2, 'isshow' => 1));
$adds += (bool)$navConfigService->add(PW_NAV_TYPE_MAIN, array('nkey' => 'area_tucool', 'pos' => array('bbs,area,cms,o,srch,group'), 'title' => 'ͼ��', 'style' => '', 'link' => 'html/channel/tucool', 'alt' => '', 'target' => 0, 'view' => 4, 'upid' => 0, 'isshow' => 1));

$adds += (bool)$navConfigService->add(PW_NAV_TYPE_MAIN, array('nkey' => 'group', 'pos' => array('bbs,area,cms,srch,group'), 'title' => 'Ⱥ��', 'style' => '', 'link' => 'group.php', 'alt' => '', 'target' => 0, 'view' => 6, 'upid' => 0, 'isshow' => 1));
$adds += (bool)$navConfigService->add(PW_NAV_TYPE_MAIN, array('nkey' => '', 'pos' => array('bbs,area,cms,srch,group'), 'title' => '�㳡', 'style' => '', 'link' => 'index.php?m=o', 'alt' => '', 'target' => 0, 'view' => 7, 'upid' => 0, 'isshow' => 0));


//FOOT
$db->update("DELETE FROM pw_nav WHERE type='foot' AND link IN (".pwImplode(array($db_ceoconnect, 'simple/', 'm/index.php')).")");
$defaults = array(
	array('pos' => '-1', 'title' => '��ϵ����', 'link' => 'sendemail.php', 'view'=>1, 'target' => 0, 'isshow' => 1),
	array('pos' => '-1', 'title' => '��ͼ��', 'link' => 'simple/', 'view'=>2, 'target' => 0, 'isshow' => 1),
	array('pos' => '-1', 'title' => '�ֻ����', 'link' => 'm/introduce.php', 'view'=>3, 'target' => 0, 'isshow' => 1),
);
foreach ($defaults as $key => $value) {
	$adds += (bool)$navConfigService->add(PW_NAV_TYPE_FOOT, $value);
}


//LEFT
$defaults = array(
	array('pos' => array('bbs,area,cms,srch,group'), 'title' => '10���ӽ�վ', 'link' => 'http://www.phpwind.com/easysite/index.php', 'view'=>1, 'target' => 1, 'isshow' => 1),
	array('pos' => array('bbs,area,cms,srch,group'), 'title' => '����ת��', 'link' => 'http://www.phpwind.net/convert.php', 'view'=>2, 'target' => 1, 'isshow' => 1),
	array('pos' => array('bbs,area,cms,srch,group'), 'title' => 'վ�����', 'link' => 'http://www.phpwind.net/thread-htm-fid-125.html', 'view'=>3, 'target' => 1, 'isshow' => 1),
);
foreach ($defaults as $key => $value) {
	$adds += (bool)$navConfigService->add(PW_NAV_TYPE_HEAD_LEFT, $value);
}


//no RIGHT again from 8.7
$db->update("UPDATE pw_nav SET type=".pwEscape(PW_NAV_TYPE_HEAD_LEFT)." WHERE type='head'");
$db->update("DELETE FROM pw_nav WHERE type=".pwEscape(PW_NAV_TYPE_HEAD_LEFT)." AND link=".pwEscape('faq.php'));
$defaults = array(
	'hack' => array(
		'data' => array('pos' => array('bbs,area,cms,srch,group'), 'nkey' => 'hack', 'title' => '��������', 'link' => '', 'view'=>1, 'target' => 0, 'isshow' => 1),
		'subs' => array(),
	),
	'sort' => array(
		'data' => array('pos' => array('bbs,area,cms,srch,group'), 'nkey' => 'sort', 'title' => 'ͳ������', 'link' => 'sort.php', 'view'=>2, 'target' => 0, 'isshow' => 1),
		'subs' => array(),
	),
	'help' => array(
		'data' => array('pos' => array('bbs,area,cms,srch,group'), 'title' => '����', 'link' => 'faq.php', 'view'=>3, 'target' => 0, 'isshow' => 1),
	),
);

$view = 1;
unset($db_hackdb['app']);
foreach ($db_hackdb as $value) {
	list($title, $key, $valid) = $value;
	$link = 'toolcenter' == $key ? 'profile.php?action=toolcenter' : 'hack.php?H_name=' . $key;
	$defaults['hack']['subs'][$key] = array('nkey' => 'hack_' . $key, 'title' => $title, 'link' => $link, 'view' => $view++, 'isshow' => ($valid ? 1 : 0));
}

$view = 1;
//include R_P . 'lang/wind/cp_lang_all.php';
$bbsNavConfig = "��ԱӦ��,app,,root\n"
	. "��������,lastpost,searcher.php?sch_time=newatc,root\n"
	. "������,digest,searcher.php?digest=1,root\n"
	. "��������,hack,,root\n"
	. "��Ա�б�,member,member.php,root\n"
	. "ͳ������,sort,sort.php,root\n"
	. "������Ϣ,sort_basic,sort.php,sort\n"
	. "����IPͳ��,sort_ipstate,sort.php?action=ipstate,sort\n"
	. "�����Ŷ�,sort_team,sort.php?action=team,sort\n"
	. "����ͳ��,sort_admin,sort.php?action=admin,sort\n"
	. "���߻�Ա,sort_online,sort.php?action=online,sort\n"
	. "��Ա����,sort_member,sort.php?action=member,sort\n"
	. "�������,sort_forum,sort.php?action=forum,sort\n"
	. "��������,sort_article,sort.php?action=article,sort\n"
	. "��ǩ����,sort_taglist,link.php?action=taglist,sort\n";
foreach (explode("\n", $bbsNavConfig) as $value) {
	if (!trim($value)) continue;
	list($title, $key, $link, $upkey) = explode(',', trim($value));
	if ($upkey == 'sort') {
		$defaults['sort']['subs'][$key] = array('nkey' => $key, 'title' => trim($title), 'link' => trim($link), 'view' => $view++, 'isshow' => 1);
	}
}

foreach ($defaults as $key => $value) {
	$navId = $navConfigService->add(PW_NAV_TYPE_HEAD_LEFT, $value['data']);
	if (isset($value['subs'])) {
		foreach ($value['subs'] as $sub) {
			$sub['upid'] = $navId;
			$navConfigService->add(PW_NAV_TYPE_HEAD_LEFT, $sub);
		}
	}
}


?>