<?php
!defined('M_P') && exit('Forbidden');
!defined('USED_HEAD') && define('USED_HEAD', 1);
require (L::style('', $skinco, true));
$db_menuinit .= "'td_userinfomore' : 'menu_userinfomore'";
//ͷ��ͳһΪ��̳ͷ�����
$db_menuinit .= ",'td_u' : 'menu_u'";
$db_menuinit .= ",'td_home' : 'menu_home'";

if ($db_menu) $db_menuinit .= ",'td_sort' : 'menu_sort'";
//ͷ��ͳһΪ��̳ͷ������

list($_Navbar,$_LoginInfo) = pwNavBar();
foreach($_Navbar['main'] as $key => $value) {
	if($value['subs']){
		$ifHaveSubs=true;
		break;
	}
}
if( "wind" != $tplpath  && file_exists(D_P.'data/style/'.$tplpath.'_css.htm')){
	$css_path = D_P.'data/style/'.$tplpath.'_css.htm';
}else{
	$css_path = D_P.'data/style/wind_css.htm';
}

$pwModeImg = "mode/$db_mode/images";
$pwModeJs = "mode/$db_mode/js";

$tablewidth = '960px;';

require_once PrintEot('header');