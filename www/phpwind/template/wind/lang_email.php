<?php
!function_exists('readover') && exit('Forbidden');

$lang['email'] = array (

'email_check_subject'	=> '�������� {$GLOBALS[db_bbsname]} ��Ա�ʺŵı�Ҫ����!',
'email_check_content'	=> '<html><head><meta http-equiv="Content-Type" content="text/html; charset=gb18030" /><title>�����ʺ�</title></head><body><div align="center"><table cellpadding="0" cellspacing="1" style="border:3px solid #d9e9f1;background:#7fbddd; text-align:left;"><tr><td style="padding:0;"><table cellpadding="30" cellspacing="0" style="border:1px solid #ffffff;background:#f7f7f7;width:500px;"><tr><td style="line-height:2;font-size:12px;"><div style="font-size:14px;margin-bottom:10px;font-weight:700;">Hi, {$GLOBALS[regname]}</div>
						<p style="color:#ff6600;margin:0;">���������������ɼ��</p>
						<div style="padding:8px 10px;margin:10px 0 5px;background:#ffffff;border:1px solid #cbcbcb;word-break:break-all;word-wrap:break-word;line-height:1.5;font-size:14px;"><a href="{$GLOBALS[db_bbsurl]}/{$GLOBALS[db_registerfile]}?step=finish&vip=activating&r_uid={$GLOBALS[winduid]}&pwd={$GLOBALS[rgyz]}&toemail={$GLOBALS[regemail]}" style="color:#3366cc;">{$GLOBALS[db_bbsurl]}/{$GLOBALS[db_registerfile]}?step=finish&vip=activating&r_uid={$GLOBALS[winduid]}&pwd={$GLOBALS[rgyz]}</a></div>
						<div style="color:#999999;margin-bottom:5px;">������ܵ�����ӣ��븴�Ƶ�ַ��ճ����������ĵ�ַ�����</div>
						����󾡿�ɾ�����ʼ��������ʺ���Ϣй©<div style="border-top:1px solid #e2e2e2;background:#ffffff;overflow:hidden;height:1px;*height:2px;margin:10px 0;"></div>��ӭ������{$GLOBALS[db_bbsname]}�������Ʊ��ܺ������ʺ���Ϣ<p style="margin:0;"><span style="padding-right:5em;">�û�����{$GLOBALS[regname]}</span>���룺{$GLOBALS[sRegpwd]}</p>
						<div style="border-top:1px solid #e2e2e2;background:#ffffff;overflow:hidden;height:1px;*height:2px;margin:10px 0;"></div>
						����������룬���Ե������һ����룬Ҳ����д�������Ա�����趨��<br />
						������ַ��<a href="{$GLOBALS[db_bbsurl]}" style="color:#3366cc;">{$GLOBALS[db_bbsurl]}</a></td></tr></table></td></tr></table></div></body></html>',
'email_check_content_resend'	=> '<html><head><meta http-equiv="Content-Type" content="text/html; charset=gb18030" /><title>�����ʺ�</title></head><body><div align="center"><table cellpadding="0" cellspacing="1" style="border:3px solid #d9e9f1;background:#7fbddd; text-align:left;"><tr><td style="padding:0;"><table cellpadding="30" cellspacing="0" style="border:1px solid #ffffff;background:#f7f7f7;width:500px;"><tr><td style="line-height:2;font-size:12px;"><div style="font-size:14px;margin-bottom:10px;font-weight:700;">Hi, {$GLOBALS[regname]}</div>
						<p style="color:#ff6600;margin:0;">���������������ɼ��</p>
						<div style="padding:8px 10px;margin:10px 0 5px;background:#ffffff;border:1px solid #cbcbcb;word-break:break-all;word-wrap:break-word;line-height:1.5;font-size:14px;"><a href="{$GLOBALS[db_bbsurl]}/{$GLOBALS[db_registerfile]}?step=finish&vip=activating&r_uid={$GLOBALS[winduid]}&pwd={$GLOBALS[rgyz]}&toemail={$GLOBALS[regemail]}" style="color:#3366cc;">{$GLOBALS[db_bbsurl]}/{$GLOBALS[db_registerfile]}?step=finish&vip=activating&r_uid={$GLOBALS[winduid]}&pwd={$GLOBALS[rgyz]}</a></div>
						<div style="color:#999999;margin-bottom:5px;">������ܵ�����ӣ��븴�Ƶ�ַ��ճ����������ĵ�ַ�����</div>
						����󾡿�ɾ�����ʼ��������ʺ���Ϣй©<div style="border-top:1px solid #e2e2e2;background:#ffffff;overflow:hidden;height:1px;*height:2px;margin:10px 0;"></div>
						<div style="border-top:1px solid #e2e2e2;background:#ffffff;overflow:hidden;height:1px;*height:2px;margin:10px 0;"></div>
						����������룬���Ե������һ����룬Ҳ����д�������Ա�����趨��<br />
						������ַ��<a href="{$GLOBALS[db_bbsurl]}" style="color:#3366cc;">{$GLOBALS[db_bbsurl]}</a></td></tr></table></td></tr></table></div></body></html>',

'email_additional'		=> 'Reply-To:{$GLOBALS[fromemail]}\r\nX-Mailer: phpwind�����ʼ����',

'email_welcome_subject'	=> '{$GLOBALS[regname]},����,��л��ע��{$GLOBALS[db_bbsname]}',
'email_welcome_content'	=> '<html><head><meta http-equiv="Content-Type" content="text/html; charset=gb18030" /><title>��л��ע��</title></head><body><div align="center"><table cellpadding="0" cellspacing="1" style="border:3px solid #d9e9f1;background:#7fbddd; text-align:left;"><tr><td style="padding:0;"><table cellpadding="30" cellspacing="0" style="border:1px solid #ffffff;background:#f7f7f7;width:500px;"><tr><td style="line-height:2;font-size:12px;"><div style="font-size:14px;margin-bottom:10px;font-weight:700;">Hi, {$GLOBALS[regname]}</div>{$GLOBALS[db_bbsname]}��ӭ���ļ��룡<p style="margin:0;"><span style="padding-right:5em;">����ע����Ϊ�� {$GLOBALS[regname]}</span>��������Ϊ�� {$GLOBALS[sRegpwd]}</p><div style="border-top:1px solid #e2e2e2;background:#ffffff;overflow:hidden;height:1px;*height:2px;margin:10px 0;"></div>�뾡��ɾ�����ʼ��������ʺ���Ϣй©<br />����������룬���Ե������һ����룬Ҳ����д�������Ա�����趨��<br />������ַ��<a href="{$GLOBALS[db_bbsurl]}">{$GLOBALS[db_bbsurl]}</a></td></tr></table></td></tr></table></div></body></html>',

'email_sendpwd_subject'	=> '{$GLOBALS[db_bbsname]} �����ط�',
'email_sendpwd_content'	=> '<html><head><meta http-equiv="Content-Type" content="text/html; charset=gb18030" /><title>�����ط�</title></head><body><div align="center"><table cellpadding="0" cellspacing="1" style="border:3px solid #d9e9f1;background:#7fbddd; text-align:left;"><tr><td style="padding:0;"><table cellpadding="30" cellspacing="0" style="border:1px solid #ffffff;background:#f7f7f7;width:500px;"><tr><td style="line-height:2;font-size:12px;">�뵽�������ַ�޸����룺<div style="padding:8px 10px;margin:10px 0 5px;background:#ffffff;border:1px solid #cbcbcb;word-break:break-all;word-wrap:break-word;line-height:1.5;font-size:14px;"><a href="{$GLOBALS[db_bbsurl]}/sendpwd.php?action=getback&pwuser={$GLOBALS[pwuser]}&submit={$GLOBALS[submit]}&st={$GLOBALS[timestamp]}">{$GLOBALS[db_bbsurl]}/sendpwd.php?action=getback&pwuser={$GLOBALS[pwuser]}&submit={$GLOBALS[submit]}&st={$GLOBALS[timestamp]}</a></div>�޸ĺ����μ���������<br />��ӭ���� {$GLOBALS[db_bbsname]} ���ǵ���ַ��:<a href="{$GLOBALS[db_bbsurl]}">{$GLOBALS[db_bbsurl]}</a></td></tr></table></td></tr></table></div></body></html>',

'email_reply_subject'	=> '����{$GLOBALS[db_bbsname]}�������лظ�',
'email_reply_content'	=> '<html><head><meta http-equiv="Content-Type" content="text/html; charset=gb18030" /><title>�����лظ�</title></head><body><div align="center"><table cellpadding="0" cellspacing="1" style="border:3px solid #d9e9f1;background:#7fbddd; text-align:left;"><tr><td style="padding:0;"><table cellpadding="30" cellspacing="0" style="border:1px solid #ffffff;background:#f7f7f7;width:500px;"><tr><td style="line-height:2;font-size:12px;"><div style="font-size:14px;margin-bottom:10px;font-weight:700;">Hi</div>����{$GLOBALS[db_bbsname]}�ʼ���ʹ<br />����{$GLOBALS[db_bbsname]}���������: {$GLOBALS[old_title]}<br />�������˻ظ�.������עһ�°�<br /><a href="{$GLOBALS[db_bbsurl]}/read.php?fid={$GLOBALS[fid]}&tid={$GLOBALS[tid]}">{$GLOBALS[db_bbsurl]}/read.php?fid={$GLOBALS[fid]}&tid={$GLOBALS[tid]}</a><br />�´������˲�������ʱ,�ҽ�����������</td></tr></table></td></tr></table></div></body></html>',

'email_invite_subject'	=> '��������{$GLOBALS[windid]}����������{$GLOBALS[db_bbsname]}',
'email_invite_content'	=> '<html><head><meta http-equiv="Content-Type" content="text/html; charset=gb18030" /><title>����ע����</title></head><body><div align="center"><table cellpadding="0" cellspacing="1" style="border:3px solid #d9e9f1;background:#7fbddd; text-align:left;"><tr><td style="padding:0;"><table cellpadding="30" cellspacing="0" style="border:1px solid #ffffff;background:#f7f7f7;width:500px;"><tr><td style="line-height:2;font-size:12px;">����{$GLOBALS[db_bbsname]}��̳��������������£�
<div style="padding:8px 10px;margin:10px 0;background:#ffffff;border:1px solid #cbcbcb;word-break:break-all;word-wrap:break-word;line-height:1.5;font-size:14px;">{$GLOBALS[invlink]}</div>ע���ַ��<a href="{$GLOBALS[db_bbsurl]}/{$GLOBALS[db_registerfile]}">{$GLOBALS[db_bbsurl]}/{$GLOBALS[db_registerfile]}</a></td></tr></table></td></tr></table></div></body></html>',
'email_invite_content_new' => '<html><head><meta http-equiv="Content-Type" content="text/html; charset=gb18030" /><title>����ע����
</title></head><body><div align="center">
<table cellpadding="0" cellspacing="1" style="border:3px solid #d9e9f1;background:#7fbddd; text-align:left;">
<tr>
<td style="padding:0;">
<table cellpadding="30" cellspacing="0" style="border:1px solid #ffffff;background:#f7f7f7;width:500px;">
<tr><td>{$GLOBALS[extranote]}</td></tr>
<tr><td style="line-height:2;font-size:12px;"><div style="padding:8px 10px;margin:10px 0;background:#ffffff;border:1px solid 

#cbcbcb;word-break:break-all;word-wrap:break-word;line-height:1.5;font-size:14px;">{$GLOBALS[invlink]}</div></td></tr>
</table>
</td></tr></table></div></body></html>',

'emailcheck_subject'	=> 'PHPwind�����ʼ����ͼ��',
'emailcheck_content'	=> 'PHPwind�����ʼ����ͼ��ɹ�!',
'email_mode_o_title'	=> '��������{$GLOBALS[windid]}����������{$GLOBALS[db_bbsname]}',
'email_mode_o_content'	=> '<html><head><meta http-equiv="Content-Type" content="text/html; charset=gb18030" /><title>����ע����</title></head><body><div align="center"><table cellpadding="0" cellspacing="1" style="border:3px solid #d9e9f1;background:#7fbddd; text-align:left;"><tr><td style="padding:0;"><table cellpadding="30" cellspacing="0" style="border:1px solid #ffffff;background:#f7f7f7;width:500px;"><tr><td style="line-height:2;font-size:12px;">����{$GLOBALS[windid]}������{$GLOBALS[db_bbsname]}�Ͻ����˸�����ҳ������Ҳ���벢��Ϊ�ҵĺ��ѡ�<br />{$GLOBALS[extranote]}\n\n�����������ӣ����ܺ������룺<br /><a href="{$GLOBALS[invite_url]}">{$GLOBALS[invite_url]}</a><br />{$GLOBALS[db_bbsname]} (<a href="{$GLOBALS[db_bbsurl]}">{$GLOBALS[db_bbsurl]}</a>)</td></tr></table></td></tr></table></div></body></html>',
'email_groupactive_invite_subject' => '{$GLOBALS[windid]}����������{$GLOBALS[objectName]}',
'email_groupactive_invite_content' => '����{$GLOBALS[windid]}������{$GLOBALS[db_bbsname]}�Ϸ����˻{$GLOBALS[objectName]}������Ҳ���벢��Ϊ�ҵĺ��ѡ�<br />�{$GLOBALS[objectName]}���ܣ�<br />{$GLOBALS[objectDescrip]}<br /><div id="customdes">{$GLOBALS[customdes]}</div>�����������ӣ����ܺ�������<br /><a href="{$GLOBALS[invite_url]}">{$GLOBALS[invite_url]}</a>',
'email_group_invite_subject' => '{$GLOBALS[windid]}����������Ⱥ��{$GLOBALS[objectName]}',
'email_group_invite_content' => '����{$GLOBALS[windid]}������{$GLOBALS[db_bbsname]}�Ϸ�����Ⱥ��{$GLOBALS[objectName]}������Ҳ���벢��Ϊ�ҵĺ��ѡ�<br />Ⱥ��{$GLOBALS[objectName]}���ܣ�<br />{$GLOBALS[objectDescrip]} [<a href="{$GLOBALS[db_bbsurl]}/apps.php?q=group&cyid={$GLOBALS[cyid]}">�鿴Ⱥ��</a>]<br /><div id="customdes">{$GLOBALS[customdes]}</div>�����������ӣ����ܺ�������<br /><a href="{$GLOBALS[invite_url]}">{$GLOBALS[invite_url]}</a>',

);
?>