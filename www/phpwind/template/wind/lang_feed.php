<?php
!function_exists('readover') && exit('Forbidden');

$lang['feed'] = array(
	'post'				=> '���������� [url=$GLOBALS[db_bbsurl]/read.php?tid=$L[tid]]$L[subject][/url]',
	'honor'				=> '������ǩ�� $L[honor]',
	'friend'			=> '�� [url=$GLOBALS[db_bbsurl]/{$GLOBALS[db_userurl]}$L[uid]]$L[friend][/url] ��Ϊ�˺���!',
	'colony_create'		=> '��������Ⱥ��[url={$L[link]}][b]{$L[cname]}[/b][/url],һ��ȥ�����',
	'colony_pass'		=> '������Ⱥ��[url={$L[link]}][b]{$L[cname]}[/b][/url],���ȥ������',
	'share_view'		=> ' $L[type_name] \n $L[share_code][url=$L[link]]$L[title][/url]\n $L[abstract]\n $L[imgs]\n $L[descrip]',
	'o_write'			=> '������һ����¼��$L[text]',
	'photo'				=> ' �ϴ���{$L[num]}����Ƭ�� {$L[text]}',
	'post_board'		=> ' ��[url={$GLOBALS[db_bbsurl]}/{$GLOBALS[db_userurl]}{$L[touid]}]{$L[tousername]}[/url]�����԰���������',
	'diary_data'		=> '������һƪ��־ [url={$GLOBALS[db_bbsurl]}/{#APPS_BASEURL#}q=diary&a=detail&uid={$L[uid]}&did={$L[did]}]{$L[subject]}[/url]\n $L[content]',
	'diary_copy'		=> 'ת����һƪ��־ [url={$GLOBALS[db_bbsurl]}/{#APPS_BASEURL#}&q=diary&a=detail&uid={$L[uid]}&did={$L[did]}]{$L[subject]}[/url]\n $L[content]',
	'colony_post'		=> '��Ⱥ��[url={$GLOBALS[db_bbsurl]}/apps.php?q=group&cyid={$L[cyid]}]{$L[colony_name]}[/url]�з�����һ������[url={$GLOBALS[db_bbsurl]}/apps.php?q=group&a=read&cyid={$L[cyid]}&tid={$L[tid]}]{$L[title]}[/url]',
	'colony_photo'		=> '��Ⱥ��[url={$GLOBALS[db_bbsurl]}/apps.php?q=group&cyid={$L[cyid]}]{$L[colony_name]}[/url]���ϴ���{$L[num]}����Ƭ\n{$L[text]}',
);
?>