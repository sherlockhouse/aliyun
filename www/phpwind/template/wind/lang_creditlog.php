<?php
!function_exists('readover') && exit('Forbidden');

$lang['creditlog'] = array (

	/*
	* ��̳��ز���
	*/
	'main_buygroup'		=> '[b]{$L[username]}[/b] ʹ���û�����ݹ����ܣ������û���($L[gptitle])���{$L[days]}�졣\n���ѻ��֣�[b]{$L[cname]}[/b]��Ӱ�죺{$L[affect]}��',
	'main_showsign'		=> '[b]{$L[username]}[/b] ʹ��ǩ��չʾ���ܡ�\n���ѻ��֣�[b]{$L[cname]}[/b]��Ӱ�죺{$L[affect]}��',
	'main_forumsell'	=> '[b]{$L[username]}[/b] ����[b]{$L[fname]}[/b]������Ȩ��{$L[days]}�죬\n���ѻ��֣�[b]{$L[cname]}[/b]��Ӱ�죺{$L[affect]}��',
	'main_changereduce'	=> '[b]{$L[username]}[/b] ʹ�� [b]{$L[cname]}[/b] -> [b]{$L[tocname]}[/b] ����ת�����ܡ�\nת�����֣�[b]{$L[cname]}[/b]��Ӱ�죺{$L[affect]}��',
	'main_changeadd'	=> '[b]{$L[username]}[/b] ʹ�� [b]{$L[fromcname]}[/b] -> [b]{$L[cname]}[/b] ����ת�����ܡ�\nת�����֣�[b]{$L[cname]}[/b]��Ӱ�죺{$L[affect]}��',
	'main_virefrom'		=> '[b]{$L[username]}[/b] ���û� [b]{$L[toname]}[/b] ת��{$L[cname]}��\nת�����֣�[b]{$L[cname]}[/b]��Ӱ�죺{$L[affect]}��',
	'main_vireto'		=> '[b]{$L[username]}[/b] �յ��û� [b]{$L[fromname]}[/b] ת�ʵ�{$L[cname]}��\nת�����֣�[b]{$L[cname]}[/b]��Ӱ�죺{$L[affect]}��',
	'main_olpay'		=> '[b]{$L[username]}[/b] ʹ�����߳�ֵ���ܣ���ֵ��{$L[number]}��\n��ֵ���֣�[b]{$L[cname]}[/b]��Ӱ�죺{$L[affect]}��',

	/*
	* ע��
	*/
	'reg_register'		=> '[b]{$L[username]}[/b] ע��ɹ���\n�������֣�[b]{$L[cname]}[/b]��Ӱ�죺{$L[affect]}��',

	/*
	* ������ز���
	*/
	'topic_upload'		=> '[b]{$L[username]}[/b] �ڰ�� {$L[fname]} �ϴ�������\n�������֣�[b]{$L[cname]}[/b]��Ӱ�죺{$L[affect]}��',
	'topic_download'	=> '[b]{$L[username]}[/b] �ڰ�� {$L[fname]} ���ظ�����\n���Ļ��֣�[b]{$L[cname]}[/b]��Ӱ�죺{$L[affect]}��',
	'topic_Post'		=> '[b]{$L[username]}[/b] �ڰ�� {$L[fname]} �������⡣\n�������֣�[b]{$L[cname]}[/b]��Ӱ�죺{$L[affect]}��',
	'topic_Reply'		=> '[b]{$L[username]}[/b] �ڰ�� {$L[fname]} �ظ����ӡ�\n�������֣�[b]{$L[cname]}[/b]��Ӱ�죺{$L[affect]}��',
	'topic_Digest'		=> '[b]{$L[username]}[/b] �ڰ�� {$L[fname]} �����ⱻ {$L[operator]} ���þ�����\n�������֣�[b]{$L[cname]}[/b]��Ӱ�죺{$L[affect]}��',
	'topic_Delete'		=> '[b]{$L[username]}[/b] �ڰ�� {$L[fname]} �����ⱻ {$L[operator]} ɾ����\n�۳����֣�[b]{$L[cname]}[/b]��Ӱ�죺{$L[affect]}��',
	'topic_Deleterp'	=> '[b]{$L[username]}[/b] �ڰ�� {$L[fname]} �Ļظ��� {$L[operator]} ɾ����\n�۳����֣�[b]{$L[cname]}[/b]��Ӱ�죺{$L[affect]}��',
	'topic_Undigest'	=> '[b]{$L[username]}[/b] �ڰ�� {$L[fname]} �����ⱻ {$L[operator]} ȡ��������\n�۳����֣�[b]{$L[cname]}[/b]��Ӱ�죺{$L[affect]}��',
	'topic_buy'			=> '[b]{$L[username]}[/b] ���� [b][url={$GLOBALS[db_bbsurl]}/read.php?tid=$L[tid]]{$L[subject]}[/url][/b] �����Ķ�Ȩ�ޡ�\n���Ļ��֣�[b]{$L[cname]}[/b]��Ӱ�죺{$L[affect]}��',
	'topic_sell'		=> '[b]{$L[username]}[/b] �� {$L[buyer]} ���� [b][url={$GLOBALS[db_bbsurl]}/read.php?tid=$L[tid]]{$L[subject]}[/url][/b] �����Ķ�Ȩ�޳ɹ���\n��û��֣�[b]{$L[cname]}[/b]��Ӱ�죺{$L[affect]}��',
	'topic_attbuy'		=> '[b]{$L[username]}[/b] ���򸽼�����Ȩ�ޡ�\n���Ļ��֣�[b]{$L[cname]}[/b]��Ӱ�죺{$L[affect]}��',
	'topic_attsell'		=> '[b]{$L[username]}[/b] �� {$L[buyer]} ���۸�������Ȩ�ޡ�\n��û��֣�[b]{$L[cname]}[/b]��Ӱ�죺{$L[affect]}��',


	/*
	* ������ز���
	*/
	'credit_showping'	=> '[b]{$L[username]}[/b] ��������ӣ�[url={$GLOBALS[db_bbsurl]}/read.php?tid={$L[tid]}]{$L[subject]}[/url] �� [b]{$L[operator]}[/b] ���֡�\nԭ��{$L[reason]}\n�������֣�[b]{$L[cname]}[/b]��Ӱ�죺{$L[affect]}��',
	'credit_delping'	=> '[b]{$L[username]}[/b] ��������ӣ�[url={$GLOBALS[db_bbsurl]}/read.php?tid={$L[tid]}]{$L[subject]}[/url] �� [b]{$L[operator]}[/b] ȡ�����֡�\nԭ��{$L[reason]}\n�۳����֣�[b]{$L[cname]}[/b]��Ӱ�죺{$L[affect]}��',

	/*
	* ������ز���
	*/
	'reward_new'		=> '[b]{$L[username]}[/b] �ڰ�� {$L[fname]} �������͡�\n��Ѵ𰸣�{$L[cbval]} {$L[cbtype]}���������ˣ�{$L[caval]} {$L[catype]}\n���Ļ��֣�[b]{$L[cname]}[/b]��Ӱ�죺{$L[affect]}��',
	'reward_modify'		=> '[b]{$L[username]}[/b] �ڰ�� {$L[fname]} ׷�����ͻ��֡�\n��Ѵ𰸣�+{$L[cbval]} {$L[cbtype]}���������ˣ�+{$L[caval]} {$L[catype]}\n���Ļ��֣�[b]{$L[cname]}[/b]��Ӱ�죺{$L[affect]}��',
	'reward_answer'		=> '[b]{$L[username]}[/b] �ڰ�� {$L[fname]} �����Ѵ𰸽�����\n�������֣�[b]{$L[cname]}[/b]��Ӱ�죺{$L[affect]}��',
	'reward_active'		=> '[b]{$L[username]}[/b] �ڰ�� {$L[fname]} ����������˽�����\n�������֣�[b]{$L[cname]}[/b]��Ӱ�죺{$L[affect]}��',
	'reward_return'		=> '[b]{$L[username]}[/b] �ڰ�� {$L[fname]} ���������Ѿ�������\nϵͳ���ػ��֣�[b]{$L[cname]}[/b]��Ӱ�죺{$L[affect]}��',

	/*
	* �����ز���
	*/
	'hack_banksave1'	=> '[b]{$L[username]}[/b] �����л��ڴ�\n������֣�[b]{$L[cname]}[/b]��Ӱ�죺{$L[affect]}��',
	'hack_banksave2'	=> '[b]{$L[username]}[/b] �����ж��ڴ�\n������֣�[b]{$L[cname]}[/b]��Ӱ�죺{$L[affect]}��',
	'hack_bankdraw1'	=> '[b]{$L[username]}[/b] �����л���ȡ�\nȡ�����֣�[b]{$L[cname]}[/b]��Ӱ�죺{$L[affect]}��',
	'hack_bankdraw2'	=> '[b]{$L[username]}[/b] �����ж���ȡ�\nȡ�����֣�[b]{$L[cname]}[/b]��Ӱ�죺{$L[affect]}��',
	'hack_cytransfer'	=> '[b]{$L[username]}[/b] ������Ȧ [b]{$L[cnname]}[/b] ת�ø� {$L[toname]}��\n���Ļ��֣�[b]{$L[cname]}[/b]��Ӱ�죺{$L[affect]}��',
	'hack_cycreate'		=> '[b]{$L[username]}[/b] ��������Ȧ [b]{$L[cnname]}[/b]��\n���Ļ��֣�[b]{$L[cname]}[/b]��Ӱ�죺{$L[affect]}��',
	'hack_cyjoin'		=> '[b]{$L[username]}[/b] ����Ⱥ�� [b]{$L[cnname]}[/b]��\n���Ļ��֣�[b]{$L[cname]}[/b]��Ӱ�죺{$L[affect]}��',
	'hack_cydonate'		=> '[b]{$L[username]}[/b] ��Ⱥ [b]{$L[cnname]}[/b] �ʻ���ֵ���֡�\n���Ļ��֣�[b]{$L[cname]}[/b]��Ӱ�죺{$L[affect]}��',
	'hack_cyvire'		=> '[b]{$L[username]}[/b] �������Ⱥ [b]{$L[cnname]}[/b] �Ļ���ת��\n�����ߣ�{$L[operator]}��\nת�ʻ��֣�[b]{$L[cname]}[/b]��Ӱ�죺{$L[affect]}��',
	'hack_cyalbum'		=> '[b]{$L[username]}[/b] ������һ����� {[b]{$L[aname]}[/b]}��\n���Ļ��֣�[b]{$L[cname]}[/b]��Ӱ�죺{$L[affect]}��',
	'hack_dbpost'		=> '[b]{$L[username]}[/b] ����������⡣\n�������֣�[b]{$L[cname]}[/b]��Ӱ�죺{$L[affect]}��',
	'hack_dbreply'		=> '[b]{$L[username]}[/b] ������۹۵㡣\n�������֣�[b]{$L[cname]}[/b]��Ӱ�죺{$L[affect]}��',
	'hack_dbdelt'		=> '[b]{$L[username]}[/b] ����ı������ⱻ {$L[operator]} ɾ����\n�۳����֣�[b]{$L[cname]}[/b]��Ӱ�죺{$L[affect]}��',
	'hack_toolubuy'		=> '[b]{$L[username]}[/b] ���û� [b]{$L[seller]}[/b] ����{$L[nums]}�� [b]{$L[toolname]}[/b] ���ߡ�\n���Ļ��֣�[b]{$L[cname]}[/b]��Ӱ�죺{$L[affect]}��',
	'hack_toolbuy'		=> '[b]{$L[username]}[/b] ����{$L[nums]}�� [b]{$L[toolname]}[/b] ���ߡ�\n���Ļ��֣�[b]{$L[cname]}[/b]��Ӱ�죺{$L[affect]}��',
	'hack_toolsell'		=> '[b]{$L[username]}[/b] ���û� [b]{$L[buyer]}[/b] ���۵��� [b]{$L[toolname]}[/b]��\n��û��֣�[b]{$L[cname]}[/b]��Ӱ�죺{$L[affect]}��',
	'hack_teampay'		=> '[b]{$L[username]}[/b] ��� {$L[datef]} �·ݿ��˽�����\n��û��֣�[b]{$L[cname]}[/b]��Ӱ�죺{$L[affect]}��',
	'hack_invcodebuy'	=> '[b]{$L[username]}[/b] ���� {$L[invnum]} �������롣\n���Ļ��֣�[b]{$L[cname]}[/b]��Ӱ�죺{$L[affect]}��',
	/* phpwind.net */
	'hack_creditget'	=> '[b]{$L[username]}[/b] ��ȡ���ֶһ��������͵Ļ��֡�\n��ȡ���֣�[b]{$L[cname]}[/b]��Ӱ�죺{$L[affect]}��',
	'hack_creditaward'	=> '[b]{$L[username]}[/b] ʹ�û��ֶһ���Ʒ [b]{$L[subject]}[/b] {$L[num]}����\n�һ����֣�[b]{$L[cname]}[/b]��Ӱ�죺{$L[affect]}��',


	/*
	* �������
	*/
	'share_Delete'		=> '[b]{$L[username]}[/b] ɾ������۳����֡�\n�۳����֣�[b]{$L[cname]}[/b]��Ӱ�죺{$L[affect]}��',
	'share_Post'		=> '[b]{$L[username]}[/b] ����������ӻ��֡�\n���ӻ��֣�[b]{$L[cname]}[/b]��Ӱ�죺{$L[affect]}��',

	/*
	* ��־���
	*/
	'diary_Delete'		=> '[b]{$L[username]}[/b] ɾ����־�۳����֡�\n�۳����֣�[b]{$L[cname]}[/b]��Ӱ�죺{$L[affect]}��',
	'diary_Post'		=> '[b]{$L[username]}[/b] ������־���ӻ��֡�\n���ӻ��֣�[b]{$L[cname]}[/b]��Ӱ�죺{$L[affect]}��',

	/*
	* Ⱥ�����
	*/
	'groups_Uploadphoto'=> '[b]{$L[username]}[/b] �ϴ���Ƭ���ӻ��֡�\n���ӻ��֣�[b]{$L[cname]}[/b]��Ӱ�죺{$L[affect]}��',
	'groups_Deletephoto'=> '[b]{$L[username]}[/b] ɾ����Ƭ�۳����֡�\n�۳����֣�[b]{$L[cname]}[/b]��Ӱ�죺{$L[affect]}��',
	'groups_Createalbum'=> '[b]{$L[username]}[/b] �������۳����֡�\n�۳����֣�[b]{$L[cname]}[/b]��Ӱ�죺{$L[affect]}��',
	'groups_Postarticle'=> '[b]{$L[username]}[/b] �����������ӻ��֡�\n���ӻ��֣�[b]{$L[cname]}[/b]��Ӱ�죺{$L[affect]}��',
	'groups_Deletearticle'=> '[b]{$L[username]}[/b] ɾ�����¿۳����֡�\n�۳����֣�[b]{$L[cname]}[/b]��Ӱ�죺{$L[affect]}��',
	'groups_Joingroup'  => '[b]{$L[username]}[/b] ����Ⱥ��۳����֡�\n�۳����֣�[b]{$L[cname]}[/b]��Ӱ�죺{$L[affect]}��',
	'groups_Creategroup'=> '[b]{$L[username]}[/b] ����Ⱥ��۳����֡�\n�۳����֣�[b]{$L[cname]}[/b]��Ӱ�죺{$L[affect]}��',

	/*
	* ������
	*/
	'photos_Deletephoto'=> '[b]{$L[username]}[/b] ɾ����Ƭ�۳����֡�\n�۳����֣�[b]{$L[cname]}[/b]��Ӱ�죺{$L[affect]}��',
	'photos_Uploadphoto'=> '[b]{$L[username]}[/b] �ϴ���Ƭ���ӻ��֡�\n���ӻ��֣�[b]{$L[cname]}[/b]��Ӱ�죺{$L[affect]}��',
	'photos_Createalbum'=> '[b]{$L[username]}[/b] �������۳����֡�\n�۳����֣�[b]{$L[cname]}[/b]��Ӱ�죺{$L[affect]}��',

	/*
	* ���������
	*/
	'weibo_Post'=> '[b]{$L[username]}[/b] �������������ӻ��֡�\n���ӻ��֣�[b]{$L[cname]}[/b]��Ӱ�죺{$L[affect]}��',
	'weibo_Delete'=> '[b]{$L[username]}[/b] ɾ�������¿۳����֡�\n�۳����֣�[b]{$L[cname]}[/b]��Ӱ�죺{$L[affect]}��',


	/*
	* ����
	*/
	'other_finishjob'   => '[b]{$L[username]}[/b] ��� [b]{$GLOBALS[job]}[/b] ����,���ϵͳ ���͵Ļ��֡�\n�������֣�[b]{$L[cname]}[/b]��Ӱ�죺{$L[affect]}��',
	'other_finishpunch'   => '[b]{$L[username]}[/b] ��� [b]ÿ�մ�[/b] ����,���ϵͳ ���͵Ļ��֡�\n�������֣�[b]{$L[cname]}[/b]��Ӱ�죺{$L[affect]}��',
	'other_present'		=> '[b]{$L[username]}[/b] ����� [b]{$L[admin]}[/b] ���������������͵Ļ��֡�\n���ͻ��֣�[b]{$L[cname]}[/b]��Ӱ�죺{$L[affect]}��',
	'other_propaganda'  =>'[b]{$L[username]}[/b] ���ڷ����������ӻ�����ͻ��֡�\n���ͻ��֣�[b]{$L[cname]}[/b]��Ӱ�죺{$L[affect]}��',


);
?>