<?php
!function_exists('readover') && exit('Forbidden');

$lang['log'] = array (

'bk_save_descrip_1'		=> '[b]{$L[username1]}[/b]ʹ�û��ڴ��ܣ������{$L[field1]}{$GLOBALS[db_moneyname]}',
'bk_save_descrip_2'		=> '[b]{$L[username1]}[/b]ʹ�ö��ڴ��ܣ������{$L[field1]}{$GLOBALS[db_moneyname]}',
'bk_draw_descrip_1'		=> '[b]{$L[username1]}[/b]ʹ�û���ȡ��ܣ�ȡ����{$L[field1]}{$GLOBALS[db_moneyname]}',
'bk_draw_descrip_2'		=> '[b]{$L[username1]}[/b]ʹ�ö���ȡ��ܣ�ȡ����{$L[field1]}{$GLOBALS[db_moneyname]}',
'bk_vire_descrip'		=> '[b]{$L[username1]}[/b]ʹ��ת�ʹ��ܣ�ת�ʸ�[b]{$L[username2]}[/b]'
							.'��{$L[field1]}{$GLOBALS[db_moneyname]}��ת�ʸ��ԣ�{$GLOBALS[memo]}',
'topped_descrip'		=> '���ӣ�[url=$GLOBALS[db_bbsurl]/read.php?tid=$L[tid]]$L[subject][/url]\n'
							.'��������������Ϊ�ö�{$L[topped]}\nԭ��{$L[reason]}',
'untopped_descrip'		=> '���ӣ�[url=$GLOBALS[db_bbsurl]/read.php?tid=$L[tid]]$L[subject][/url]\n'
							.'��������������ö�\nԭ��{$L[reason]}',
'digest_descrip'		=> '���ӣ�[url=$GLOBALS[db_bbsurl]/read.php?tid=$L[tid]]$L[subject][/url]\n'
							.'��������������Ϊ����{$L[digest]}\nԭ��{$L[reason]}\nӰ�죺{$L[affect]}',
'undigest_descrip'		=> '���ӣ�[url=$GLOBALS[db_bbsurl]/read.php?tid=$L[tid]]$L[subject][/url]\n'
							.'������ȡ�����¾���\nԭ��{$L[reason]}\nӰ�죺{$L[affect]}',
'highlight_descrip'		=> '���ӣ�[url=$GLOBALS[db_bbsurl]/read.php?tid=$L[tid]]$L[subject][/url]\n'
							.'�����������±������\nԭ��{$L[reason]}',
'unhighlight_descrip'	=> '���ӣ�[url=$GLOBALS[db_bbsurl]/read.php?tid=$L[tid]]$L[subject][/url]\n'
							.'�����������±���ȡ������\nԭ��{$L[reason]}',
'push_descrip'			=> '���ӣ�[url=$GLOBALS[db_bbsurl]/read.php?tid=$L[tid]]$L[subject][/url]\n'
							.'��������������ǰ\nԭ��{$L[reason]}',
'lock_descrip'			=> '���ӣ�[url=$GLOBALS[db_bbsurl]/read.php?tid=$L[tid]]$L[subject][/url]\n'
							.'����������������\nԭ��{$L[reason]}',
'unlock_descrip'		=> '���ӣ�[url=$GLOBALS[db_bbsurl]/read.php?tid=$L[tid]]$L[subject][/url]\n'
							.'�����������½������\nԭ��{$L[reason]}',
'delrp_descrip'			=> '���ӣ�[url=$GLOBALS[db_bbsurl]/read.php?tid=$L[tid]]$L[subject][/url]\n'
							.'������ɾ���ظ�\nԭ��{$L[reason]}\nӰ�죺{$L[affect]}',
'deltpc_descrip'		=> '���ӣ�[url=$GLOBALS[db_bbsurl]/read.php?tid=$L[tid]]$L[subject][/url]\n'
							.'������ɾ������\nԭ��{$L[reason]}\nӰ�죺{$L[affect]}',
'del_descrip'			=> '���ӣ�[url=$GLOBALS[db_bbsurl]/read.php?tid=$L[tid]]$L[subject][/url]\n'
							.'������������ɾ��\nԭ��{$L[reason]}\nӰ�죺{$L[affect]}',
'move_descrip'			=> '���ӣ�[url=$GLOBALS[db_bbsurl]/read.php?tid=$L[tid]]$L[subject][/url]\n'
							.'�������������ƶ����°��([url=$GLOBALS[db_bbsurl]/thread.php?fid={$L[tofid]}][b]$L[toforum][/b][/url])\n'
							.'ԭ��{$L[reason]}',
'copy_descrip'			=> '���ӣ�[url=$GLOBALS[db_bbsurl]/read.php?tid=$L[tid]]$L[subject][/url]\n'
							.'�����������¸��Ƶ��°��([url=$GLOBALS[db_bbsurl]/thread.php?fid={$L[tofid]}][b]$L[toforum][/b][/url])\n'
							.'ԭ��{$L[reason]}',
'edit_descrip'			=> '���ӣ�[url=$GLOBALS[db_bbsurl]/read.php?tid=$L[tid]]$L[subject][/url]\n'
							.'�������༭����',
'credit_descrip'		=> '���ӣ�[url=$GLOBALS[db_bbsurl]/read.php?tid=$L[tid]]$L[subject][/url]\n'
							.'���������±�����\nԭ��{$L[reason]}\nӰ�죺{$L[affect]}',
'creditdel_descrip'		=> '���ӣ�[url=$GLOBALS[db_bbsurl]/read.php?tid=$L[tid]]$L[subject][/url]\n'
							.'�������������ֱ�ȡ��\nԭ��{$L[reason]}\nӰ�죺{$L[affect]}',
'banuser_descrip'		=> '�û� [b]{$L[username1]}[/b] ������\n�������û�����\nԭ��{$L[reason]}',
'deluser_descrip'		=> '�û� [b]{$L[username1]}[/b] ��ɾ��\n����������ɾ���û�',
'join_descrip'			=> '[b]{$L[username1]}[/b] ����{$GLOBALS[cn_name]}[b]{$L[cname]}[/b]������{$GLOBALS[moneyname]}��{$L[field1]}��',
'donate_descrip'		=> '[b]{$L[username1]}[/b] ʹ�þ��׸�����{$GLOBALS[cn_name]}($L[cname])����{$GLOBALS[moneyname]}��{$L[field1]}��',
'cy_vire_descrip'		=> '[b]{$L[username2]}[/b] ʹ��{$GLOBALS[cn_name]}{$GLOBALS[moneyname]}�����ܣ�'
							.'���û�[b]{$L[username1]}[/b]ת�� {$L[field1]}{$GLOBALS[moneyname]}��ϵͳ��ȡ�����ѣ�{$L[tax]}��',
'shield_descrip'		=> '���ӣ�[url=$GLOBALS[db_bbsurl]/read.php?tid=$L[tid]]$L[subject][/url]\n'
							.'��������������\nԭ��{$L[reason]}',
'banuserip_descrip'		=> '���ӣ�[url=$GLOBALS[db_bbsurl]/read.php?tid=$L[tid]]$L[subject][/url]\n'
							.'��������ֹIP\nԭ��{$L[reason]}',
'signature_descrip'	=> '���ӣ�[url=$GLOBALS[db_bbsurl]/read.php?tid=$L[tid]]$L[subject][/url]\n'
							.'��������ֹǩ��\nԭ��{$L[reason]}',
'unite_descrip'			=> '���ӣ�[url=$GLOBALS[db_bbsurl]/read.php?tid=$L[tid]]$L[subject][/url]\n'
							.'����������ϲ�\nԭ��{$L[reason]}',
'remind_descrip'		=> '���ӣ�[url=$GLOBALS[db_bbsurl]/read.php?tid=$L[tid]]$L[subject][/url]\n'
							.'������������ʾ\nԭ��{$L[reason]}',
'down_descrip'			=> '���ӣ�[url=$GLOBALS[db_bbsurl]/read.php?tid=$L[tid]]$L[subject][/url]\n'
							.'������������ѹ��\nԭ��{$L[reason]}',
'recycle_topic_delete'	=> '���:$L[forum]\n�����������´��������վ�г���ɾ��',
'recycle_topic_restore'	=> '���:$L[forum]\n�����������´��������վ�л�ԭ',
'recycle_topic_empty'	=> '���:$L[forum]\n���������������վ���',
'recycle_reply_delete'	=> '���:$L[forum]\n�����������´ӻظ�����վ�г���ɾ��',
'recycle_reply_restore'	=> '���:$L[forum]\n�����������´ӻظ�����վ�л�ԭ',
'recycle_reply_empty'	=> '���:$L[forum]\n���������ظ�����վ���',
'pushto_descrip'		=> '���ӣ�[url=$GLOBALS[db_bbsurl]/read.php?tid=$L[tid]]$L[subject][/url]\n'
							.'����������������\nԭ��{$L[reason]}'
);
?>