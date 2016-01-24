<?php
!defined('PW_UPLOAD') && exit('Forbidden');

if (isset($db_modes)) {
	$modes = $db_modes;
	$modepages = $db_modepages;
} else {
	InitGP(array('db_mode'));
	$modes = array (
		'bbs'	=> array('m_name' => '��̳ģʽ','ifopen' => 1,'title' => '��̳'),
		'area'	=> array('m_name' => '�Ż�ģʽ','ifopen' => 1,'title' => '�Ż�'),
		'o'		=> array('m_name' => '��������','ifopen' => 1,'title' => '��������'),
		'cms'   => array('m_name' => '����ģʽ','ifopen' => 1,'title' => '����')
	);
	$modepages = array ( 'area' => array ( 'index' => array ( 'name' => '��ҳ','template' => 'index' ), 'cate' => array ( 'name' => 'Ƶ��ҳ','template' => 'cate' ), 'thread' => array ( 'name' => '�б�ҳ', 'template' => 'thread' ) ), 'o' => array ( 'index' => array ( 'name' => '��ҳ', 'template' => 'index' ), 'm_home' => array ( 'name' => '��̬', 'template' => 'm_home' ) ) );
}
$db_modes = $db_modepages = array();
$db_modes['bbs'] = $modes['bbs'];
$modelist = ModeList();
foreach ($modelist as $key=>$value) {
	$db_modes[$key] = $modes[$key];
	$db_modepages[$key] = $modepages[$key];
}
if (!$db_mode || $db_mode=='bbs') {
	$db_mode = (count($db_modes) > 1 && !$db_modes[$db_mode]) ? 'bbs' : 'area';
}
$db_modes = addslashes(serialize($db_modes));
$db_modepages = addslashes(serialize($db_modepages));
$db->update("REPLACE INTO pw_config SET db_name='db_modes',vtype='array',db_value= '$db_modes'");
$db->update("REPLACE INTO pw_config SET db_name='db_mode',vtype='string',db_value= '$db_mode'");
$db->update("REPLACE INTO pw_config SET db_name='db_modepages',vtype='array',db_value= '$db_modepages'");
$db->update("REPLACE INTO pw_config SET db_name='db_ifpwcache',db_value= '567'");

function ModeList(){
	$modes = array();
	if ($fp = opendir(R_P.'mode')) {
		while (($modedir = readdir($fp))) {
			if (strpos($modedir,'.')===false) {
				$infodb = array();
				if (function_exists('file_get_contents')) {
					$filedata = @file_get_contents(R_P."mode/$modedir/info.xml");
				} else {
					$filedata = readover(R_P."mode/$modedir/info.xml");
				}
				if (preg_match('/\<modename\>(.+?)\<\/modename\>\s+\<descrip\>(.+?)\<\/descrip\>/is',$filedata,$infodb)) {
					$infodb[1] && $modename = Char_cv(str_replace(array("\n"),'',$infodb[1]));
				}
				$modes[$modedir] = array('m_name' => $modename,'ifopen'=>1,'title' => $modename);
			}
		}
		closedir($fp);
	}
	return $modes;
}

?>