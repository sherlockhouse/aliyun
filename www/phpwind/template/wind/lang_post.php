<?php
!function_exists('readover') && exit('Forbidden');

$lang['post'] = array (

'hide_post'		=> '[color=red]���������Ҫ����[/color]',
'post_post'		=> '[��������]',
'sell_post'		=> '[���ݳ���]',
//'info_post_1'	=> '[size=2][color=gray]����¥��{$GLOBALS[old_author]}��{$GLOBALS[wtof_oldfile]}����� {$GLOBALS[atcarray][subject]} :[/color][/size]',
'info_post_1'	=> '[url={$GLOBALS[db_bbsurl]}/u.php?username={$GLOBALS[old_author]}]{$GLOBALS[old_author]}[/url][color=gray]:[/color]',
//'info_post_2'	=> '[size=2][color=gray]���õ�{$GLOBALS[article]}¥{$GLOBALS[old_author]}��{$GLOBALS[wtof_oldfile]}����� {$GLOBALS[atcarray][subject]} :[/color][/size]',
'info_post_2'	=> '[url={$GLOBALS[db_bbsurl]}/u.php?username={$GLOBALS[old_author]}]{$GLOBALS[old_author]}[/url][color=gray]:[/color]',
'edit_post'		=> '������{$GLOBALS[altername]}��{$GLOBALS[timeofedit]}���±༭',

);
?>