<?php
!function_exists('readover') && exit('Forbidden');

$lang['_othermsg'] = '\n\n[b]���ӣ�[/b][url=$GLOBALS[db_bbsurl]/read.php?tid={$L[tid]}]{$L[subject]}[/url]\n'
					. '[b]�������ڣ�[/b]{$L[postdate]}\n'
					. '[b]���ڰ�飺[/b][url=$GLOBALS[db_bbsurl]/thread.php?fid={$L[fid]}]{$L[forum]}[/url]\n'
					. '[b]����ʱ�䣺[/b]{$L[admindate]}\n'
					. '[b]�������ɣ�[/b]{$L[reason]}\n\n'
					. '��̳�������֪ͨ����Ϣ���Ա��ι���������κ����飬������ȡ����ϵ��';
$lang['_othermsg1'] = '\n\n[b]���ӣ�[/b][url=$GLOBALS[db_bbsurl]/job.php?action=topost&tid={$L[tid]}&pid={$L[pid]}]{$L[subject]}[/url]\n'
					. '[b]�������ڣ�[/b]{$L[postdate]}\n'
					. '[b]���ڰ�飺[/b][url=$GLOBALS[db_bbsurl]/thread.php?fid={$L[fid]}]{$L[forum]}[/url]\n'
					. '[b]����ʱ�䣺[/b]{$L[admindate]}\n'
					. '[b]�������ɣ�[/b]{$L[reason]}\n\n'
					. '��̳�������֪ͨ����Ϣ���Ա��ι���������κ����飬������ȡ����ϵ��';
$lang['_othermsg_colony'] = '\n\n[b]���ӣ�[/b][url=$GLOBALS[db_bbsurl]/job.php?action=topost&tid={$L[tid]}&pid={$L[pid]}]{$L[subject]}[/url]\n'
							. '[b]�������ڣ�[/b]{$L[postdate]}\n'
							. '[b]���ڰ�飺[/b][url=$GLOBALS[db_bbsurl]/thread.php?fid={$L[fid]}]{$L[forum]}[/url]\n'
							. '[b]����ʱ�䣺[/b]{$L[admindate]}\n'
							. '[b]�������ɣ�[/b]{$L[reason]}\n\n'
							. '��̳�������֪ͨ����Ϣ���Ա��ι���������κ����飬������ȡ����ϵ��';

$lang['writemsg'] = array (
	'olpay_title'			=> '���ֳ�ֵ֧���ɹ�.',
	'olpay_content'			=> '���ױҳ�ֵ֧���ɹ�������Ҫ��¼֧����ʹ�á�[color=red]ȷ���ջ�[/color]��������ɱ��ν��ס�\n'
								. 'ȷ���ջ���ϵͳ���Զ������Ľ��ױ��ʻ����г�ֵ��',
	'olpay_content_2'		=> '���ֳ�ֵ�ɹ������γ�ֵ��{$L[number]}RMB���ܹ����{$L[cname]}������{$L[currency]}��\nллʹ�ã�',

	'toolbuy_title'			=> '���߹���֧���ɹ�!',
	'toolbuy_content'		=> '���߹���ɹ�������֧����{$L[fee]}RMB��������[b]{$L[toolname]}[/b]���� [b]{$L[number]}[/b] ��!',

	'forumbuy_title'		=> '������Ȩ�޹���֧���ɹ�!',
	'forumbuy_content'		=> '������Ȩ�޹���ɹ�������֧����{$L[fee]}RMB�������ð�� [b]{$L[fname]}[/b] �������� [b]{$L[number]}[/b] ��!',

	'groupbuy_title'		=> '�����鹺��֧���ɹ�!',
	'groupbuy_content'		=> '��������ݹ���ɹ�������֧����{$L[fee]}RMB���������û��� [b]{$L[gname]}[/b] ��� [b]{$L[number]}[/b] ��!',

	'virement_title'		=> '���л��֪ͨ!!',
	'virement_content'		=> '�û�{$L[windid]}ͨ�����и���ת��{$L[to_money]}ԪǮ��'
								. 'ϵͳ�Զ�����ǰ����Ϣ�ӵ���Ĵ���У������Ϣ�����������¿�ʼ����\nת�ʸ��ԣ�{$L[memo]}',
	'metal_add'				=> '����ѫ��֪ͨ',
	'metal_post_title'	    => '����ѫ���������ύ',
	'metal_post_content'    => '����ѫ���������ύ�����������\n\nѫ�����ƣ�{$L[mname]}\n������{$L[windid]}\n���ɣ�{$L[reason]}',
	'metal_add_content'		=> '��������ѫ��\n\nѫ�����ƣ�{$L[mname]}\n������{$L[windid]}\n���ɣ�{$L[reason]}',
	'metal_cancel'			=> '�ջ�ѫ��֪ͨ',
	'metal_cancel_content'	=> '����ѫ�±��ջ�\n\nѫ�����ƣ�{$L[mname]}\n������{$L[windid]}\n���ɣ�{$L[reason]}',
	'metal_cancel_text'		=> '����ѫ�±��ջ�\n\nѫ�����ƣ�{$L[medalname]}\n������SYSTEM\n���ɣ�����',
	'metal_refuse'			=> 'ѫ������δͨ��',
	'metal_refuse_content'	=> '����ѫ������δͨ�����\n\nѫ�����ƣ�{$L[mname]}\n������{$L[windid]}\n���ɣ�{$L[reason]}',
	'medal_apply_title'		=> 'ѫ������֪ͨ!',
	'medal_apply_content'	=> '�û� {$L[username]} �� {$L[time]} ������ {$L[medal]} ѫ�£���������ˡ�',
	'vire_title'			=> '���ױ�ת��֪ͨ',
	'vire_content'			=> '�û� [b]{$L[windid]}[/b] ʹ�û���ת�ʹ��ܣ�����ת�� {$L[paynum]} {$L[cname]}����ע����ա�',
	'cyvire_title'			=> '{$L[cn_name]}{$L[moneyname]}ת��֪ͨ',
	'cyvire_content'		=> '{$L[cn_name]}([url=$GLOBALS[db_bbsurl]/hack.php?'
								. 'H_name=colony&cyid={$L[cyid]}&job=view&id={$L[cyid]}]'
								. '{$L[all_cname]}[/url])����Աʹ��{$L[moneyname]}�����ܣ�'
								. '����ת�� {$L[currency]} {$L[moneyname]}����ע����ա�',
	'donate_title'			=> '{$L[cn_name]}����֪ͨ��Ϣ',
	'donate_content'		=> '�û�{$L[windid]}ͨ�����׹��ܣ���{$L[cn_name]}({$L[allcname]})'
								. '����{$L[moneyname]}��{$L[sendmoney]}��',

	'top_title'				=> '�������ӱ��ö�.',
	'untop_title'			=> '�������ӱ�����ö�.',
	'top_content'			=> '����������ӱ� [b]{$L[manager]}[/b] ִ�� [b]�ö�[/b] ����'.$lang['_othermsg'],
	'untop_content'			=> '�������ӱ� [b]{$L[manager]}[/b] ִ�� [b]����ö�[/b] ����'.$lang['_othermsg'],
	'digest_title'			=> '�������ӱ���Ϊ������',
	'digest_content'		=> '����������ӱ� [b]{$L[manager]}[/b] ִ�� [b]����[/b] ����\n\n'
								. '������Ӱ�죺{$L[affect]}'.$lang['_othermsg'],
	'undigest_title'		=> '�������ӱ�ȡ������',
	'undigest_content'		=> '����������ӱ� [b]{$L[manager]}[/b] ִ�� [b]ȡ������[/b] ����\n\n'
								. '������Ӱ�죺{$L[affect]}'.$lang['_othermsg'],
	'lock_title'			=> '�������ӱ�����',
	'lock_content'			=> '����������ӱ� [b]{$L[manager]}[/b] ִ�� [b]����[/b] ����'.$lang['_othermsg'],
	'lock_title_2'			=> '�������ӱ��ر�',
	'lock_content_2'		=> '����������ӱ� [b]{$L[manager]}[/b] ִ�� [b]�ر�[/b] ����'.$lang['_othermsg'],
	'unlock_title'			=> '�������ӱ��������',
	'unlock_content'		=> '����������ӱ� [b]{$L[manager]}[/b] ִ�� [b]�������[/b] ����'.$lang['_othermsg'],
	'push_title'			=> '�������ӱ���ǰ',
	'push_content'          => '����������ӱ� [b]{$L[manager]}[/b] [b]��ǰ�� {$L[timelimit]} Сʱ[/b]'.$lang['_othermsg'],
	'recommend_title'		=> '�������ӱ��Ƽ�',
	'recommend_content'		=> '����������ӱ� [b]{$L[manager]}[/b] ִ�� [b]�Ƽ�[/b] ����'.$lang['_othermsg'],
	'pushto_title'			=> '�������ӱ�����',
	'pushto_content'		=> '����������ӱ� [b]{$L[manager]}[/b] ִ�� [b]����[/b] ����'.$lang['_othermsg'],
	'unhighlight_title'		=> '�������ӱ��ⱻȡ��������ʾ',
	'unhighlight_content'	=> '����������ӱ� [b]{$L[manager]}[/b] ִ�� [b]����ȡ������[/b] ����'.$lang['_othermsg'],
	'highlight_title'		=> '�������ӱ��ⱻ������ʾ',
	'highlight_content'		=> '����������ӱ� [b]{$L[manager]}[/b] ִ�� [b]�������[/b] ����'.$lang['_othermsg'],
	'del_title'				=> '�������ӱ�ɾ��',
	'del_content'			=> '����������ӱ� [b]{$L[manager]}[/b] ִ�� [b]ɾ��[/b] ����\n\n'
								. '������Ӱ�죺{$L[affect]}'.$lang['_othermsg'],
	'move_title'			=> '�������ӱ��ƶ�',
	'move_content'			=> '����������ӱ� [b]{$L[manager]}[/b] ִ�� [b]�ƶ�[/b] ����\n\n'
								. '[b]Ŀ�İ�飺[/b][url=$GLOBALS[db_bbsurl]/thread.php?fid={$L[tofid]}]{$L[toforum]}[/url]'.$lang['_othermsg'],
	'copy_title'			=> '�������ӱ����Ƶ��°��',
	'copy_content'			=> '����������ӱ� [b]{$L[manager]}[/b] ִ�� [b]����[/b] ����\n\n'
								. '[b]Ŀ�İ�飺[/b][url=$GLOBALS[db_bbsurl]/thread.php?fid={$L[tofid]}]{$L[toforum]}[/url]'.$lang['_othermsg'],
	'ping_title'			=> '"{$L[sender]}"��"{$L[receiver]}"����������',
	'ping_content'			=> '"{$L[sender]}"��[b]"{$L[receiver]}"[/b]������  ִ�� [b]����[/b] ����\n\n'
								. 'Ӱ�죺{$L[affect]}'.$lang['_othermsg1'],
	'delping_title'			=> '"{$L[receiver]}"�����ӱ�"{$L[sender]}"ȡ������',
	'delping_content'		=> '"{$L[receiver]}"�����ӱ�[b]"{$L[sender]}"[/b] ִ�� [b]ȡ������[/b] ����\n\n'
								. 'Ӱ�죺{$L[affect]}'.$lang['_othermsg1'],
	'deltpc_title'			=> '�������ӱ�ɾ��',
	'deltpc_content'		=> '����������ӱ� [b]{$L[manager]}[/b] ִ�� [b]ɾ��[/b] ����\n\n'
								. 'Ӱ�죺{$L[affect]}'.$lang['_othermsg'],
	'delrp_title'			=> '���Ļظ���ɾ��',
	'delrp_content'			=> '������Ļظ��� [b]{$L[manager]}[/b] ִ�� [b]ɾ��[/b] ����\n\n'
								. '������Ӱ�죺{$L[affect]}'.$lang['_othermsg'],
	'reward_title_1'		=> '���Ļظ�����Ϊ��Ѵ�!',
	'reward_content_1'		=> '���Ļظ�����Ϊ��Ѵ�!\n\n������Ӱ�죺{$L[affect]}'.$lang['_othermsg'],
	'reward_title_2'		=> '���Ļظ�����������˽���!',
	'reward_content_2'		=> '���Ļظ�����������˽���!\n\n������Ӱ�죺{$L[affect]}'.$lang['_othermsg'],
	'endreward_title_1'		=> '�������ͱ�ȡ��!',
	'endreward_title_2'		=> '�������ͱ�ǿ�ƽ᰸!',
	'endreward_content_1'	=> '����û�к��ʵĴ𰸣��������ͱ�����Ա [b]{$L[manager]}[/b] ִ�� [b]ȡ��[/b] ����!\n\n'
								. 'ϵͳ������:{$L[affect]}'.$lang['_othermsg'],
	'endreward_content_2'	=> '��������ʱ��δ�����������н᰸,���Ѿ��к��ʵĴ𰸣����Ա�[b]{$L[manager]}[/b] ִ�� [b]ǿ�ƽ᰸[/b] ����\n\n'
								. 'ϵͳ���������л���'.$lang['_othermsg'],
	'rewardmsg_title'		=> '������(���:{$L[tid]})������!',
	'rewardmsg_content'		=> '�𾴵İ���:\n\t\t����!\n\t\t���ڸô�������û�в������ʴ𰸣����������᰸,'
								. 'ϣ������ϸ��֤��������ƽ����!'.$lang['_othermsg'],
	'rewardmsg_notice_title'	=> '����������֪ͨ!',
	'rewardmsg_notice_content'	=> '�����������Ѿ����ڣ�ϵͳ������������������,���������Ȩǿ�н᰸!\n\n'
								. '[b]���ӣ�[/b][url=$GLOBALS[db_bbsurl]/read.php?tid={$L[tid]}]{$L[subject]}[/url]\n'
								. '[b]�������ڣ�[/b]{$L[postdate]}\n'
								. '[b]���ڰ�飺[/b][url=$GLOBALS[db_bbsurl]/thread.php?fid={$L[fid]}]{$L[name]}[/url]\n\n'
								. '��̳�������֪ͨ����Ϣ���Ա��ι���������κ����飬������ȡ����ϵ��',
	'shield_title_2'		=> '�����������ⱻɾ��',
	'shield_content_2'		=> '��������������ⱻ [b]{$L[manager]}[/b] [b]ɾ��[/b]'.$lang['_othermsg'],
	'shield_title_1'		=> '�������ӱ�����',
	'shield_content_1'		=> '����������ӱ� [b]{$L[manager]}[/b] [b]����[/b]'.$lang['_othermsg'],
	'shield_title_0'		=> '�������ӱ�ȡ������',
	'shield_content_0'		=> '����������ӱ� [b]{$L[manager]}[/b] ִ�� [b]ȡ������[/b] ����'.$lang['_othermsg'],
	'bansignature_title_0'	=> '������̳ǩ�����������',
	'bansignature_title_1'	=> '������̳ǩ������ֹ',
	'bansignature_content_1'=> '������̳ǩ���� [b]{$L[manager]}[/b] ��{$L[admindate]}ִ�� [b]��ֹ[/b] ����\n'.
							   '[b]��������:[/b]{$L[reason]}',
	'bansignature_content_0'=> '������̳ǩ���� [b]{$L[manager]}[/b] ��{$L[admindate]}ִ�� [b]�������[/b] ����',
	'remind_title'			=> '�������ӱ����ѹ���',
	'remind_content'		=> '����������ӱ� [b]{$L[manager]}[/b] ִ�� [b]���ѹ���[/b] ����'.$lang['_othermsg'],
	'unite_title'			=> '�������ӱ�{$L[manager]}�ϲ�',
	'unite_content'			=> '����������ӱ� [b]{$L[manager]}[/b] ִ�� [b]�ϲ�[/b] ����'.$lang['_othermsg'],
	'unite_owner_content'	=> '����������ӱ� [b]{$L[manager]}[/b] ִ�� [b]�ϲ�[/b] ����',
	'leaveword_title'		=> '�������ӱ�����',
	'leaveword_content'		=> '[b]{$L[author]}[/b] ��������������������ˡ�'.$lang['_othermsg'],
	'birth_title'			=> '$L[userName],ף�����տ���',
	'birth_content'			=> '[img]$GLOBALS[db_bbsurl]/u/images/birthday.gif[/img]\r\n'
								. '��Ը�������գ�Ϊ������һ����������̻Ի͵�һ����\r\n'
								. 'ֻϣ�����ÿһ�춼���֡�������������������Ҫ�ܶ������졢���գ�\r\n'
								. '���յ������ҡҷһ��������ÿһ֧�����ҵ�ףԸ�����տ��֣�\r\n\r\n'
								. '--------------------------------------- {$L[fromUsername]} ��������ֿ��ף����\r\n\r\n',
	'down_title'			=> '�������ӱ�ִ��ѹ������',
	'down_content'			=> '����������ӱ� [b]{$L[manager]}[/b] [b]ѹ���� {$L[timelimit]} Сʱ[/b]'.$lang['_othermsg'],
	'change_type_title'		=> '�������ӱ��޸����������',
	'change_type_content'	=> '��������������ⱻ [b]{$L[manager]}[/b] [b]�޸����������Ϊ��{$L[type]}[/b]'.$lang['_othermsg'],
	'check_title'			=> '����������ͨ�����',
	'check_content'			=> '��������������ⱻ [b]{$L[manager]}[/b] [b]ͨ�����[/b]'.$lang['_othermsg'],

	'post_pass_title'		=> '������Ļظ��Ѿ�ͨ�����!',
	'post_pass_content'		=> '���Ļظ����Ѿ�ͨ����ˡ�[url=$GLOBALS[db_bbsurl]/read.php?tid={$L[tid]}]����鿴[/url]\n\n',
	'subject_reply_title'	=> '��{$L[windid]}���ظ��ˡ�{$L[author]}�����������[{$L[title]}]',
	'subject_replytouser_title' => '��{$L[windid]}��������[{$L[title]}]�лظ�����',
	'subject_reply_content' => '{$L[windid]}˵��$L[content]\n\n[url=$GLOBALS[db_bbsurl]/job.php?action=topost&tid={$L[tid]}&pid={$L[pid]}]�鿴�ظ�[/url] [url=$GLOBALS[db_bbsurl]/read.php?tid={$L[tid]}]�鿴����[/url]\n\n',
	'advert_buy_title'		=> '���Ĺ��λ������ͨ�����',
	'advert_buy_content'	=> '�����λ�ļ۸�Ϊ��{$L[creditnum]} {$L[creditypename]} ÿ��\n\n'
								. '�㹺�������Ϊ��{$L[days]}',
	'advert_apply_title'	=> '������λ����֪ͨ!',
	'advert_apply_content'	=> '�û� {$L[username]} �� {$L[time]} ������ {$L[days]} ��Ĺ��չʾ����������ˡ�',

	'friend_add_title_1'	=> '����ϵͳ֪ͨ��{$L[username]}�����������������ĺ�������',
	'friend_add_content_1'	=> '[url=$GLOBALS[db_bbsurl]/{$GLOBALS[db_userurl]}{$L[uid]}]{$L[username]}[/url] �����������������ĺ���������',
	'friend_add_title_2'	=> '����ϵͳ֪ͨ��{$L[username]} �������Ϊ����',
	'friend_add_content_2'	=> '[url={$GLOBALS[db_bbsurl]}/{$GLOBALS[db_userurl]}{$L[uid]}]{$L[username]}[/url] �������Ϊ���ѡ��Ƿ�ͬ�⣿\n\n$L[msg]\n\n',
	'friend_delete_title'	=> '����ϵͳ֪ͨ��{$L[username]} ��������ĺ��ѹ�ϵ',
	'friend_delete_content'	=> '[url={$GLOBALS[db_bbsurl]}/{$GLOBALS[db_userurl]}{$L[uid]}]{$L[username]}[/url] ��������ĺ��ѹ�ϵ��',
	'friend_accept_title'	=> '����ϵͳ֪ͨ��{$L[username]} ͨ�������ĺ�������',
	'friend_accept_content' => '[url={$GLOBALS[db_bbsurl]}/{$GLOBALS[db_userurl]}{$L[uid]}]{$L[username]}[/url] ͨ�������ĺ�������',
	'friend_acceptadd_title'=> '����ϵͳ֪ͨ��{$L[username]} ͨ�������ĺ�������,������Ϊ����',
	'friend_acceptadd_content'	=> '[url={$GLOBALS[db_bbsurl]}/{$GLOBALS[db_userurl]}{$L[uid]}]{$L[username]}[/url] ͨ�������ĺ�������,������Ϊ���ѡ�',

	'friend_refuse_title'	=> '����ϵͳ֪ͨ��{$L[username]} �ܾ������ĺ�������',
	'friend_refuse_content'	=> '[url={$GLOBALS[db_bbsurl]}/{$GLOBALS[db_userurl]}{$L[uid]}]{$L[username]}[/url]'
								. '�ܾ������ĺ�������\r\n\r\n[b]�ܾ����ɣ�[/b]{$L[msg]}\r\n\r\n',
	'friend_agree_title'	=> '����ϵͳ֪ͨ��{$L[username]} ͨ�������ĺ�������',
	'friend_agree_content'	=> '[url={$GLOBALS[db_bbsurl]}/{$GLOBALS[db_userurl]}{$L[uid]}]{$L[username]}[/url]'
								. 'ͨ�������ĺ�������\r\n\r\n',
	'user_update_title'		=> 'ϵͳ֪ͨ',
	'user_update_content'	=> '�װ���{$L[username]}�����Ƿǳ����˵ĸ����㣬��ո�������{$L[membername]}����������һ��{$L[upmembername]}������{$L[userneed]}�֣�Ҫ����Ŭ��Ŷ��<a href="profile.php?action=permission" target="_blank">�鿴��Ա��Ȩ��</a>',
	'banuser_title'			=> 'ϵͳ����֪ͨ',
	'banuser_content_1'		=> '���Ѿ�������Ա{$L[manager]} ����,����ʱ��Ϊ{$L[limit]} ��\n\n{$L[reason]}',
	'banuser_content_2'		=> '���Ѿ�������Ա{$L[manager]} ����\n\n{$L[reason]}',
	'banuser_content_3'		=> '���Ѿ�������Ա{$L[manager]} ����\n\n{$L[reason]}',

	'banuser_free_title'	=> '�������֪ͨ',
	'banuser_free_content'	=> '���Ѿ�������Ա{$L[manager]} �������\n\n{$L[reason]}',

	'onlinepay_logistics'	=> '������˾��{$L[logistics]}\r\n�������ţ�{$L[orderid]}',
	'goods_pay_title'		=> '��Ҹ���֪ͨ!',
	'goods_pay_content'		=> '��� [b]{$L[buyer]}[/b] �� {$L[buydate]} �µ�����Ʒ [b][url={$GLOBALS[db_bbsurl]}/read.php?tid={$L[tid]}]{$L[goodsname]}[/url][/b] �Ѿ����������Ϣ���£�\r\n\r\n{$L[descrip]}\r\n\r\n��ȷ�Ϻ󣬾��췢��!',
	'goods_send_title'		=> '���ҷ���֪ͨ!',
	'goods_send_content'	=> '���� {$L[buydate]} �������Ʒ[b][url={$GLOBALS[db_bbsurl]}/read.php?tid={$L[tid]}]{$L[goodsname]}[/url][/b]������ [b]{$L[seller]}[/b] �Ѿ�������������ϢΪ��\r\n\r\n{$L[descrip]}',

	'sharelink_apply_title'		=> '����������������֪ͨ!',
	'sharelink_apply_content'	=> '�û� {$L[username]} �� {$L[time]} ��������������չʾ����������ˡ�',

	'sharelink_pass_title'		=> '����������������ͨ��֪ͨ!',
	'sharelink_pass_content'	=> '���ύ��������������������ͨ����ˡ�',

	'o_addadmin_title'		=> 'Ⱥ��֪ͨ[{$L[cname]}]��������Ϊ����Ա��!',
	'o_addadmin_content'	=> '�������Ⱥ��[url={$L[curl]}]{$L[cname]}[/url]���Ѿ�������Ϊ����Ա�ˣ��Ͽ�ȥ����!',
	'o_deladmin_title'		=> 'Ⱥ��֪ͨ[{$L[cname]}]������ȡ������Ա�����!',
	'o_deladmin_content'	=> '�������Ⱥ��[url={$L[curl]}]{$L[cname]}[/url]���Ѿ�����ȡ������Ա����ˣ��Ͽ�ȥ����!',
	'o_check_title'			=> 'Ⱥ��֪ͨ[{$L[cname]}]��������ʽ����Ⱥ����!',
	'o_check_content'		=> '����ǰ��������Ⱥ��[url={$L[curl]}]{$L[cname]}[/url]���Ѿ���ʽ��׼�������ˣ�[url={$L[curl]}]�Ͽ�ȥ����[/url]!',

	'o_friend_success_title'	=> '����ϵͳ֪ͨ������{$L[username]}��Ϊ�˺���',
	'o_friend_success_cotent'	=> 'ͨ��������ѣ�����[url={$L[myurl]}]{$L[username]}[/url]��Ϊ�˺���',

	'o_board_success_title'		=> '"{$L[sender]}"��"{$L[receiver]}"������',
	'o_board_success_cotent'	=> '{$L[content]} \n[url={$GLOBALS[db_bbsurl]}/u.php?a=board&uid={$L[touid]}]�鿴��������[/url]',

	'o_share_success_title'		=> '"{$L[sender]}"������"{$L[receiver]}"�ķ���',
	'o_share_success_cotent'	=> '{$L[title]}\n\n[url={$GLOBALS[db_bbsurl]}/apps.php?q=share]ȥ�ҵķ���ҳ��[/url]',
	'o_write_success_title'		=> '"{$L[sender]}"������"{$L[receiver]}"�ļ�¼',
	'o_write_success_cotent'	=> '{$L[title]}\n\n[url={$GLOBALS[db_bbsurl]}/apps.php?q=write]ȥ�ҵļ�¼ҳ��[/url]',
	'o_photo_success_title'		=> '"{$L[sender]}"������"{$L[receiver]}"����Ƭ',
	'o_photo_success_cotent'	=> '{$L[title]}\n\n[url={$GLOBALS[db_bbsurl]}/apps.php?username={$L[receiver]}&q=photos&a=view&pid={$L[id]}]ȥ����Ƭҳ��[/url]',
	'o_diary_success_title'		=> '"{$L[sender]}"������"{$L[receiver]}"����־',
	'o_diary_success_cotent'	=> '{$L[title]}\n\n[url={$GLOBALS[db_bbsurl]}/apps.php?username={$L[receiver]}&q=diary&a=detail&did={$L[id]}]�鿴��ϸ��־[/url]',

	'inspect_title'				=> '��������ѱ������Ķ�',
	'inspect_content'			=> '����������ӱ� [b]{$L[manager]}[/b] ִ�� [b]����[/b] ����\r\n[b]���ӱ��⣺[/b]<a target="_blank" href="read.php?tid={$L[tid]}">{$L[subject]}</a>\r\n[b]�������ڣ�[/b]{$L[postdate]}\r\n[b]�������ɣ�[/b]{$L[reason]}',
	
	
	'report_title'				=> '�л�Ա�ٱ�������Ϣ���뼰ʱ����',
	'report_content'		=> '��ٱ��������ѱ�����Ա [b]{$L[manager]}[/b]����\r\n [b]���ͣ�[/b]{$L[type]}\r\n[b]�������ڣ�[/b]{$L[admindate]}\r\n[b]���ľٱ����ɣ�[/b]{$L[reason]}\r\n[b]���ӵ�ַ��[/b][url={$L[url]}]����[/url]',
	'report_content_1_1'	=> '����������,�����Ϊ������!'.$lang['_othermsg'],
	'report_content_1_0'	=> '����������,�����Ϊ������!'.$lang['_othermsg1'],
	'report_content_0_0'	=> '�л�Ա�ٱ�������Ϣ���뼰ʱ����!'
							. '\n\n[b]���ͣ�$L[type]\n'
							. '[b]����ʱ�䣺[/b]{$L[admindate]}\n'
							. '[b]�ٱ����ɣ�[/b]{$L[reason]}\n\n'
							. '[b]���ӵ�ַ��[/b][url={$L[url]}]����[/url]\n\n',
							
	'report_deal_title'			=> '���ٱ������ݱ��ѱ�����Ա����',
	'report_deal_content'		=> '��ٱ��������ѱ�����Ա [b]{$L[manager]}[/b]����\r\n [b]���ͣ�[/b]{$L[type]}\r\n[b]�������ڣ�[/b]{$L[admindate]}\r\n[b]���ľٱ����ɣ�[/b]{$L[reason]}\r\n[b]���ӵ�ַ��[/b][url={$L[url]}]����[/url]',
	
	
	
	
	/*'birth_title'				=> '{$L[userName]},ף�����տ��֣�',
	'birth_content'				=> '���տ��֣������ҵ�ף����ף�㿪��ÿһ�죡',*/

	'group_attorn_title'		=> 'ת��Ⱥ��֪ͨ',
	'group_attorn_content'		=> '[b]{$L[username]}[/b]��Ⱥ�顰[url={$GLOBALS[db_bbsurl]}/apps.php?q=group&cyid={$L[cyid]}]{$L[cname]}[/url]��ת�ø������������ǹ��ڴ�Ⱥ��Ľ��ܣ�{$L[descrip]}',

	'group_invite_title'		=> 'ת��Ⱥ��֪ͨ',
	'email_groupactive_invite_subject' => '{$GLOBALS[windid]}����������{$GLOBALS[objectName]}������Ϊ���ĺ���',
	'email_groupactive_invite_content' => '����{$GLOBALS[windid]}������{$GLOBALS[db_bbsname]}�Ϸ����˻{$GLOBALS[objectName]}�������ǹ������Ľ��ܣ��Ͽ����ɣ�<br />�{$GLOBALS[objectName]}��飺<br />{$GLOBALS[objectDescrip]}<br /><div id="customdes">{$GLOBALS[customdes]}</div>�����������ӣ����ܺ�������<br /><a href="{$GLOBALS[invite_url]}">{$GLOBALS[invite_url]}</a>',
	'email_group_invite_subject' => '{$GLOBALS[windid]}����������Ⱥ��{$GLOBALS[objectName]}������Ϊ���ĺ���',
	'email_group_invite_content' => '����{$GLOBALS[windid]}������{$GLOBALS[db_bbsname]}�Ϸ�����Ⱥ��{$GLOBALS[objectName]}�������ǹ������Ľ��ܣ��Ͽ����ɣ���<br />Ⱥ��{$GLOBALS[objectName]}��飺<br />{$GLOBALS[objectDescrip]} [<a href="{$GLOBALS[db_bbsurl]}/apps.php?q=group&cyid={$GLOBALS[id]}">�鿴Ⱥ��</a>]  [<a href="{$GLOBALS[db_bbsurl]}/apps.php?q=group&cyid={$GLOBALS[id]}&a=join&invite_flag=1&step=1" onclick="return ajaxurl(this)">����Ⱥ��</a>]<br /><div id="customdes">{$GLOBALS[customdes]}</div>�����������ӣ����ܺ�������<br /><a href="{$GLOBALS[invite_url]}">{$GLOBALS[invite_url]}</a>',
	'message_group_invite_content' => '{$GLOBALS[windid]}������Ⱥ��<a href="{$GLOBALS[db_bbsurl]}/apps.php?q=group&cyid={$GLOBALS[id]}">{$GLOBALS[objectName]}</a><br />Ⱥ��{$GLOBALS[objectName]}���ܣ�{$GLOBALS[objectDescrip]} ',
	'message_groupactive_invite_subject' => '{$GLOBALS[windid]}����������{$GLOBALS[objectName]}',
	'message_groupactive_invite_content' => '{$GLOBALS[windid]}���������Ⱥ��<a href="{$GLOBALS[db_bbsurl]}/apps.php?q=group&cyid={$GLOBALS[cyid]}">{$GLOBALS[colonyName]}</a>�Ļ��{$GLOBALS[objectName]}��<br />����ܣ�{$GLOBALS[objectDescrip]}<br /><a href="{$GLOBALS[db_bbsurl]}/apps.php?q=group&a=active&cyid={$GLOBALS[cyid]}&job=view&id={$GLOBALS[id]}">��ȥ����</a>�ɣ� ',
	'filtermsg_thread_pass_title'	=> '�����дʡ�������������Ѿ�ͨ�����!',
	'filtermsg_thread_pass_content'	=> '�����������ӣ�{$L[subject]}���Ѿ�ͨ����ˡ�\n\n',
	'filtermsg_thread_del_title'	=> '�����дʡ������������������������ݱ�����Աɾ��!',
	'filtermsg_thread_del_content'	=> '�����������ӣ�{$L[subject]}��������������ݱ�����Աɾ����\n\n',
	'filtermsg_post_pass_title'		=> '�����дʡ�������Ļظ��Ѿ�ͨ�����!',
	'filtermsg_post_pass_content'	=> '�������ڣ�{$L[subject]}�Ļظ����Ѿ�ͨ����ˡ�\n\n',
	'filtermsg_post_del_title'		=> '�����дʡ�������Ļظ�������������ݱ�����Աɾ��!',
	'filtermsg_post_del_content'	=> '�������ڣ�{$L[subject]}�Ļظ���������������ݱ�����Աɾ����\n\n',
	'colony_join_title_check'		=> 'Ⱥ������[{$L[cname]}]��{$GLOBALS[windid]}������룬�����',
	'colony_join_content_check'		=> '<a href="{$GLOBALS[db_userurl]}{$GLOBALS[winduid]}" target="_blank">{$GLOBALS[windid]}</a>�������Ⱥ��{$L[cname]}������ˡ�<a href="{$L[colonyurl]}">�鿴����</a>��',
	'colony_join_title'				=> 'Ⱥ��֪ͨ[{$L[cname]}]:{$GLOBALS[windid]}�ѳɹ�����',
	'colony_join_content'		=> '�û�<a href="{$GLOBALS[db_userurl]}{$GLOBALS[winduid]}" target="_blank">{$GLOBALS[windid]}</a>������Ⱥ��[{$L[cname]}] <a href="{$L[colonyurl]}">��ȥ������</a>',
	'o_del_title'				=> 'Ⱥ��֪ͨ[{$L[cname]}]���ѽ����Ƴ���Ⱥ!',
	'o_del_content'			=> '<a href="{$GLOBALS[db_userurl]}{$GLOBALS[winduid]}" target="_blank">{$GLOBALS[windid]}</a>�Ѿ������Ƴ� [url=$L[curl]]{$L[cname]}[/url] Ⱥ��!',

	//�Ź�
	'activity_pcjoin_new_title'		=> '{$L[username]}�����μ��������Ź��',
	'activity_pcjoin_new_content'	=> '[url=$GLOBALS[db_bbsurl]/{$GLOBALS[db_userurl]}{$L[uid]}]{$L[username]}[/url]�����μ�����������Ź��[url=$GLOBALS[db_bbsurl]/read.php?tid={$L[tid]}]��{$L[subject]}��[/url]\r\n\r\n' . '�������ڣ�{$L[createtime]}'.'\r\n'.'���ڰ�飺[url=$GLOBALS[db_bbsurl]/thread.php?fid={$L[fid]}]{$L[fname]}[/url]',
	
	//�

	//����
	'activity_signup_new_title'		=> '{$L[username]}�����μ������Ļ',
	'activity_signup_new_content'	=> '[url=$GLOBALS[db_bbsurl]/{$GLOBALS[db_userurl]}{$L[uid]}]{$L[username]}[/url]�����μ���������Ļ[url=$GLOBALS[db_bbsurl]/read.php?tid={$L[tid]}]��{$L[subject]}��[/url]\r\n\r\n' . '�������ڣ�{$L[createtime]}'.'\r\n'.'���ڰ�飺[url=$GLOBALS[db_bbsurl]/thread.php?fid={$L[fid]}]{$L[fname]}[/url]',
	

	//ɾ��
	'activity_signup_close_title'		=> '���ӻ�йر���{$L[username]}',
	'activity_signup_close_content'		=> '���ӻ��{$L[subject]}���йر��˱�����[url=$GLOBALS[db_bbsurl]/{$GLOBALS[db_userurl]}{$L[uid]}]{$L[username]}[/url]\r\n\r\n' . '[url=$GLOBALS[db_bbsurl]/read.php?tid={$L[tid]}]�鿴�����[/url]',
	'activity_signuper_close_title'		=> '���ı����ѱ�{$L[username]}�ر�',
	'activity_signuper_close_content'	=> '[url=$GLOBALS[db_bbsurl]/{$GLOBALS[db_userurl]}{$L[uid]}]{$L[username]}[/url]�ѹر����ڡ�{$L[subject]}����еı�����Ϣ\r\n\r\n' . '[url=$GLOBALS[db_bbsurl]/read.php?tid={$L[tid]}]�鿴�����[/url]',

	//�ر�
	'activity_close_pay_title'				=> '���ر���{$L[username]}�ķ���',
	'activity_close_pay_content'			=> '���ѹر���[url=$GLOBALS[db_bbsurl]/{$GLOBALS[db_userurl]}{$L[uid]}]{$L[username]}[/url]�ڻ��{$L[subject]}���е�׷�ӷ���{$L[totalcash]}Ԫ\r\n\r\n' . '[url=$GLOBALS[db_bbsurl]/read.php?tid={$L[tid]}]�鿴�����[/url]',
	'activity_close_signuper_pay_title'		=> '{$L[username]}�ر������ķ���',
	'activity_close_signuper_pay_content'	=> '[url=$GLOBALS[db_bbsurl]/{$GLOBALS[db_userurl]}{$L[uid]}]{$L[username]}[/url]�ѹر������ڻ��{$L[subject]}���е�׷�ӷ���{$L[totalcash]}Ԫ\r\n\r\n' . '[url=$GLOBALS[db_bbsurl]/read.php?tid={$L[tid]}]�鿴�����[/url]',

	//֧��
	'activity_payed_title'				=> '{$L[username]}֧���˻����',
	'activity_payed_content'			=> '[url=$GLOBALS[db_bbsurl]/{$GLOBALS[db_userurl]}{$L[uid]}]{$L[username]}[/url]ͨ��֧����֧���ˡ�{$L[subject]}���Ļ����{$L[totalcash]}Ԫ\r\n\r\n' . '[url=$GLOBALS[db_bbsurl]/read.php?tid={$L[tid]}]�鿴�����[/url]',
	'activity_payed2_content'			=> '[url=$GLOBALS[db_bbsurl]/{$GLOBALS[db_userurl]}{$L[uid]}]{$L[username]}[/url]ͨ��֧����֧���ˡ�{$L[subject]}����׷�ӷ���{$L[totalcash]}Ԫ\r\n\r\n' . '[url=$GLOBALS[db_bbsurl]/read.php?tid={$L[tid]}]�鿴�����[/url]',
	'activity_payed_signuper_title'		=> '��֧���˻����',
	'activity_payed_signuper_content'	=> '���ѳɹ�֧���ˡ�{$L[subject]}���Ļ����{$L[totalcash]}Ԫ\r\n\r\n' . '[url=$GLOBALS[db_bbsurl]/read.php?tid={$L[tid]}]�鿴�����[/url]',
	'activity_payed2_signuper_content'	=> '���ѳɹ�֧���ˡ�{$L[subject]}����׷�ӷ���{$L[totalcash]}Ԫ\r\n\r\n' . '[url=$GLOBALS[db_bbsurl]/read.php?tid={$L[tid]}]�鿴�����[/url]',

	//����
	'activity_payed_from_title'			=> '��֧����{$L[username]}�Ļ����',
	'activity_payed_from_content'		=> '���ѳɹ���[url=$GLOBALS[db_bbsurl]/{$GLOBALS[db_userurl]}{$L[uid]}]{$L[username]}[/url]֧���ˡ�{$L[subject]}���Ļ����{$L[totalcash]}Ԫ\r\n\r\n' . '[url=$GLOBALS[db_bbsurl]/read.php?tid={$L[tid]}]�鿴�����[/url]',
	'activity_payed2_from_content'		=> '���ѳɹ���[url=$GLOBALS[db_bbsurl]/{$GLOBALS[db_userurl]}{$L[uid]}]{$L[username]}[/url]֧���ˡ�{$L[subject]}����׷�ӷ���{$L[totalcash]}Ԫ\r\n\r\n' . '[url=$GLOBALS[db_bbsurl]/read.php?tid={$L[tid]}]�鿴�����[/url]',

	//������
	'activity_payed_signuper_from_title'	=> '{$L[username]}֧�������Ļ����',
	'activity_payed_signuper_from_content'	=> '[url=$GLOBALS[db_bbsurl]/{$GLOBALS[db_userurl]}{$L[uid]}]{$L[username]}[/url]����֧���ˡ�{$L[subject]}���Ļ����{$L[totalcash]}Ԫ\r\n\r\n' . '[url=$GLOBALS[db_bbsurl]/read.php?tid={$L[tid]}]�鿴�����[/url]',
	'activity_payed2_signuper_from_content'	=> '[url=$GLOBALS[db_bbsurl]/{$GLOBALS[db_userurl]}{$L[uid]}]{$L[username]}[/url]����֧���ˡ�{$L[subject]}����׷�ӷ���{$L[totalcash]}Ԫ\r\n\r\n' . '[url=$GLOBALS[db_bbsurl]/read.php?tid={$L[tid]}]�鿴�����[/url]',

	//ȷ��֧��
	'activity_confirmpay_title'				=> '���޸���{$L[username]}��֧��״̬',
	'activity_confirmpay_content'			=> '���ѽ�[url=$GLOBALS[db_bbsurl]/{$GLOBALS[db_userurl]}{$L[uid]}]{$L[username]}[/url]�ڻ��{$L[subject]}����{$L[totalcash]}Ԫ���õ�֧��״̬��Ϊ����֧����\r\n\r\n' . '[url=$GLOBALS[db_bbsurl]/read.php?tid={$L[tid]}]�鿴�����[/url]',
	'activity_confirmpay2_content'	=> '���ѽ�[url=$GLOBALS[db_bbsurl]/{$GLOBALS[db_userurl]}{$L[uid]}]{$L[username]}[/url]�ڻ��{$L[subject]}����{$L[totalcash]}Ԫ׷�ӷ��õ�֧��״̬��Ϊ����֧����\r\n\r\n' . '[url=$GLOBALS[db_bbsurl]/read.php?tid={$L[tid]}]�鿴�����[/url]',
	'activity_confirmpay_signuper_title'	=> '{$L[username]}�޸�������֧��״̬',
	'activity_confirmpay_signuper_content'	=> '[url=$GLOBALS[db_bbsurl]/{$GLOBALS[db_userurl]}{$L[uid]}]{$L[username]}[/url]�ѽ����ڻ��{$L[subject]}����{$L[totalcash]}Ԫ���õ�֧��״̬��Ϊ����֧����\r\n\r\n' . '[url=$GLOBALS[db_bbsurl]/read.php?tid={$L[tid]}]�鿴�����[/url]',
	'activity_confirmpay2_signuper_content'	=> '[url=$GLOBALS[db_bbsurl]/{$GLOBALS[db_userurl]}{$L[uid]}]{$L[username]}[/url]�ѽ����ڻ��{$L[subject]}����{$L[totalcash]}Ԫ׷�ӷ��õ�֧��״̬��Ϊ����֧����\r\n\r\n' . '[url=$GLOBALS[db_bbsurl]/read.php?tid={$L[tid]}]�鿴�����[/url]',

	//�˿�
	'activity_refund_title'				=> '���˻���{$L[username]}�ķ���',
	'activity_refund_content'			=> '���ѳɹ��˻�[url=$GLOBALS[db_bbsurl]/{$GLOBALS[db_userurl]}{$L[uid]}]{$L[username]}[/url]�ڻ��{$L[subject]}���еķ���{$L[totalcash]}Ԫ\r\n\r\n' . '[url=$GLOBALS[db_bbsurl]/read.php?tid={$L[tid]}]�鿴�����[/url]',
	'activity_refund2_content'			=> '���ѳɹ��˻�[url=$GLOBALS[db_bbsurl]/{$GLOBALS[db_userurl]}{$L[uid]}]{$L[username]}[/url]�ڻ��{$L[subject]}���е�׷�ӷ���{$L[totalcash]}Ԫ\r\n\r\n' . '[url=$GLOBALS[db_bbsurl]/read.php?tid={$L[tid]}]�鿴�����[/url]',
	'activity_refund_signuper_title'	=> '{$L[username]}�˻������ķ���',
	'activity_refund_signuper_content'	=> '[url=$GLOBALS[db_bbsurl]/{$GLOBALS[db_userurl]}{$L[uid]}]{$L[username]}[/url]�ѳɹ��˻����ڻ��{$L[subject]}���еķ���{$L[totalcash]}Ԫ\r\n\r\n' . '[url=$GLOBALS[db_bbsurl]/read.php?tid={$L[tid]}]�鿴�����[/url]',
	'activity_refund2_signuper_content'	=> '[url=$GLOBALS[db_bbsurl]/{$GLOBALS[db_userurl]}{$L[uid]}]{$L[username]}[/url]�ѳɹ��˻����ڻ��{$L[subject]}���е�׷�ӷ���{$L[totalcash]}Ԫ\r\n\r\n' . '[url=$GLOBALS[db_bbsurl]/read.php?tid={$L[tid]}]�鿴�����[/url]',

	//׷�ӻ����
	'activity_additional_title'			=> '{$L[username]}׷������Ļ����',
	'activity_additional_content'		=> '[url=$GLOBALS[db_bbsurl]/{$GLOBALS[db_userurl]}{$L[uid]}]{$L[username]}[/url]׷���ˡ�{$L[subject]}���Ļ����{$L[totalcash]}Ԫ\r\n\r\n' . '[url=$GLOBALS[db_bbsurl]/read.php?tid={$L[tid]}]�鿴�����[/url]',

	//ȡ���
	'activity_cancel_title'				=> '��{$L[subject]}�����ȡ��',
	'activity_cancel_content'			=> '��{$L[subject]}���δ�ﵽ�����������Զ�ȡ�����뼰ʱ�˿��ѽ��ɵı�������\r\n\r\n' . '[url=$GLOBALS[db_bbsurl]/read.php?tid={$L[tid]}]�鿴�����[/url]',
	'activity_cancel_signuper_title'	=> '��{$L[subject]}�����ȡ��',
	'activity_cancel_signuper_content'	=> '��{$L[subject]}���δ�ﵽ�����������Զ�ȡ��������ϵ��������˻ر�������\r\n\r\n' . '[url=$GLOBALS[db_bbsurl]/read.php?tid={$L[tid]}]�鿴�����[/url]',

	//���ɾ�������ӱ�ɾ����
	'activity_delete_title'				=> '��{$L[subject]}�����ɾ��',
	'activity_delete_content'			=> '��{$L[subject]}���������Աɾ�����뼰ʱ�˿��ѽ��ɵı�������\n\n'
					. '��̳�������֪ͨ����Ϣ���Ա��ι���������κ����飬������ȡ����ϵ��',
	'activity_delete_signuper_content'	=> '��{$L[subject]}���������Աɾ��������ϵ��������˻ر�������\n\n'
					. '[url=$GLOBALS[db_bbsurl]/mode.php?m=o&q=activity&see=feeslog]�鿴�ҵĻ������ͨ��־[/url]',
					
	'split_title'			=> '�������ӱ����',
	'split_content'		    => '�����������{$L[spiltInfo]}�� [b]{$L[manager]}[/b] ִ�� [b]���[/b] ����\n\n'
								. '����ԭ��{$L[msg]}\n\n'
								. '��̳�������֪ͨ����Ϣ���Ա��ι���������κ����飬������ȡ����ϵ��',
								
	//������
	'kmd_manage_pass_title'	=> '���Ŀ�����������ͨ�����',
	'kmd_manage_pass_content'	=> '���ã�����[color=blue]{$L[creadtime]}[/color]����Ŀ�������[color=blue]{$L[subject]}[/color]��ͨ����ˣ��ƹ㵽��ʱ��Ϊ��[color=blue]{$L[endtime]}[/color]���������⣬����ϵ����Ա',	
	'kmd_manage_repulse_title'	=> '���Ŀ���������δͨ�����',
	'kmd_manage_repulse_content'	=> '���ã�����[color=blue]{$L[creadtime]}[/color] ����Ŀ������� [color=blue]{$L[subject]}[/color]δͨ����ˡ��������⣬����ϵ����Ա',					
    'kmd_manage_pay_back_title'  => '���Ŀ������������˿�ɹ�!',
	'kmd_manage_pay_back_content'  => ' ���ã����� [color=blue]{$L[creadtime]}[/color]  ����Ŀ�������  [color=blue]{$L[subject]}[/color]������Ϊ [color=blue]{$L[rmb]}[/color]Ԫ�� ���˿�ɹ�����ע����ա��������⣬����ϵ����Ա��  ',		
	'kmd_manage_pay_title'  =>  '���Ŀ�����������֧���ɹ�!',
	'kmd_manage_pay_content'  =>	'���ã����� [color=blue]{$L[creadtime]}[/color]  ����Ŀ�������  [color=blue]{$L[subject]}[/color] ������Ϊ [color=blue]{$L[rmb]}[/color]Ԫ���ѳɹ�֧������ȴ���ˣ����ͨ���󼴿�������ʾ���������⣬����ϵ����Ա��',
	'kmd_review_title'	=> '����ˡ�$L[username]���ύ���������룬��ȷ���Ƿ���֧��',
	'kmd_review_content'	=> '[url=$GLOBALS[db_bbsurl]/u.php?username=$L[username]]$L[username][/url]�ύ�˿��������룬�ƹ��飺[url=$GLOBALS[db_bbsurl]/thread.php?fid=$L[fid]]$L[forumname][/url]���ƹ���ã�[color=orange]$L[money]Ԫ[/color]����鿴������̨ȷ��֧��״̬��',
	'kmd_review_user_title' => '���Ŀ����������ύ�ɹ�����ȴ����',
	'kmd_review_user_content' => '������Ŀ����ƣ��ƹ��飺[url=$GLOBALS[db_bbsurl]/thread.php?fid=$L[fid]]$L[forumname][/url]���ƹ���ã�[color=orange]$L[money]Ԫ[/color]���ѳɹ��ύ����̨��֧��������ϵ����Ա��˿�ͨ��',
	'kmd_review_thread_change_title' => '����ˡ�$L[username]���ύ���������Ӹ�������',
	'kmd_review_thread_add_title' => '����ˡ�$L[username]���ύ�����������������',
	'kmd_review_thread_content' => '[url=$GLOBALS[db_bbsurl]/u.php?username=$L[username]]$L[username][/url]�ύ�˿������ƹ����룬�ƹ����ӣ�[url=$GLOBALS[db_bbsurl]/read.php?tid=$L[tid]]$L[threadtitle][/url]��������̨�������״̬��',
	'kmd_review_user_thread_title' => '���Ŀ������ƹ������ύ�ɹ�����ȴ����',
	'kmd_review_user_thread_content' => '������Ŀ������ƹ����ӣ�[url=$GLOBALS[db_bbsurl]/read.php?tid=$L[tid]]$L[threadtitle][/url]���ѳɹ��ύ����̨����ȴ�����Ա��˿�ͨ��',

	'kmd_admin_paylog_checked_title' => '���Ŀ����ƹ��������ѿ�ͨ�ɹ� ',
	'kmd_admin_paylog_checked_content' => '������Ŀ����ƣ��ƹ��飺[url=$GLOBALS[db_bbsurl]/thread.php?fid=$L[fid]]$L[forumname][/url]���ƹ���ã�$L[price]Ԫ���ѳɹ���ͨ�����ڿ�����[url=$GLOBALS[db_bbsurl]/apps.php?q=kmd]��������-������[/url] ����ƹ������ˡ�',
	'kmd_admin_paylog_reject_title' => '���Ŀ����ƹ�������δ����׼',
	'kmd_admin_paylog_reject_content' => '������Ŀ����ƣ��ƹ��飺[url=$GLOBALS[db_bbsurl]/thread.php?fid=$L[fid]]$L[forumname][/url]���ƹ���ã�$L[price]Ԫ��δ����׼��',
	'kmd_admin_thread_checked_title' => '���Ŀ������ƹ������ѱ�ͨ��',
	'kmd_admin_thread_checked_content' => '������Ŀ������ƹ����ӣ�[url=$GLOBALS[db_bbsurl]/read.php?tid=$L[tid]]$L[subject][/url]���ѱ�ͨ����������Ӧ���鿴Ч����',
	'kmd_admin_thread_reject_title' => '���Ŀ������ƹ������ѱ��ܾ�',
	'kmd_admin_thread_reject_content' => '������Ŀ������ƹ����ӣ�������Ŀ������ƹ����ӣ�[url=$GLOBALS[db_bbsurl]/read.php?tid=$L[tid]]$L[subject][/url]���ѱ��ܾ����뻻���������ԡ����ѱ��ܾ����뻻���������ԡ�',
	'kmd_admin_kmd_canceled_title' => '������ԭ�����Ŀ������ѱ�����Ա����',
	'kmd_admin_kmd_canceled_content' => '�Բ������Ŀ������漰���������ѱ�����Ա������������������ϵվ����ͨ��',
);
?>