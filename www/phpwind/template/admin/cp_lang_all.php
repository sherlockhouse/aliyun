<?php
!function_exists('readover') && exit('Forbidden');

$lang['all'] = array (

'reg_member'		=> '��ͨ��Ա',

'operate'			=> '����:',
'reason'			=> 'ԭ��:',

'record_rvrc'		=> '����',
'record_money'		=> '��Ǯ',
'record_credit'		=> '֧�ֶ�',

'forum_cate'		=> '��-',
'forum_cate1'		=> '��-',
'forum_cate2'		=> '��-',
'forum_cate3'		=> '��-',

'send_welcome'		=> '���ã���ӭ���� $GLOBALS[db_bbsurl].',
'send_feast'		=> '���ã�$windid�������ǽ��գ�����̳ȫ�����ף��������죡�����ص��������Ƹ���$money��������$rvrc ��Ϊ���������Ц�ɡ�ף��������~',
'whole_notice'		=> 'ȫ�ֹ���',
'cms_notice'		=> '����ϵͳ',
'all_notice'		=> '���й���',
'add_notice'		=> '��ӹ���',
'read_ischeck'		=> '������δͨ����֤',
'read_deleted'		=> '�����Ѿ���ɾ��',

'sqlinfo'			=> '* ���ݿ������Ϣ����\r\n* ������ֵ������ϵ����������,ѯ�ʾ�������ݿ������Ϣ',
'dbhost'			=> '// ���ݿ� ������ �� IP ��ַ�������ݿ�˿ڲ���3306������ ������ �� IP ��ַ����ӡ�:����˿ڡ���'
						. '������������localhost���˿���3307�������Ϊ��localhost:3307��',
'dbuser'			=> '// ���ݿ��û��������룬���Ӻͷ��� MySQL ���ݿ�ʱ������û��������룬���Ƽ�ʹ�ÿյ����ݿ����롣',
'dbname'			=> '// ���ݿ�������̳������ʹ�õ����ݿ�����',
'database'			=> '// ���ݿ����ͣ���Чѡ���� mysql �� mysqli����pwforums v6.3.2��'
						. '������mysqli��֧�֣������Ը��ã�Ч�����ܸ��ȶ�����mysql���Ӹ��ȶ�\r\n\t'
						. '// ���������������� PHP5.1.0����߰汾 �� MySQL4.1.3����߰汾�����Գ���ʹ�� mysqli��',
'PW'				=> '// �����ַ�����������ÿһ�׳���ķ���',
'pconnect'			=> '// �Ƿ�־����ӣ��ݲ�֧��mysqli',
'charset'			=> '* Mysql��������(���ñ��룺gbk��big5��utf8��latin1)\r\n'
						. '* ���������̳��������������Ҫ���ô������޸�\r\n'
						. '* �벻Ҫ������Ĵ�����򽫿��ܵ�����̳������������',
'managerinfo'		=> '* ��ʼ�˽�ӵ����̳������Ȩ�ޣ���pwforums v6.3��֧�ֶ��ش�ʼ�ˣ��û���������ķ�����\r\n'
						. '* ����1����./data/sql_config.php���ļ���������Ϊ777����NT����������ȡ��ֻ�������û�Ȩ������ΪFull Control����ȫ���ƣ�NT����������\r\n'
						. '* Ȼ����ԭʼ��ʼ���ʺŵ�¼��̨���ڸ�����̳��ʼ�˴������������޸Ĳ�����\r\n'
						. '* ������Ϻ��ٽ�./data/sql_config.php���ļ���������Ϊ644����NT����������ֻ����NT�������������Ƽ���\r\n'
						. '* ����2���ü��±���./data/sql_config.php�ļ����ڡ���ʼ���û������顱�м����µ��û�����\r\n'
						. '* �硰\$manager = array(\'admin\');������Ϊ��\$manager = array(\'admin\',\'phpwind\');�����ڡ���ʼ���������顱�м����µ����룬\r\n'
						. '* �硰\$manager_pwd = array(\'21232f297a57a5a743894a0e4a801fc3\');��\r\n'
						. '* ����Ϊ��\$manager_pwd = array(\'21232f297a57a5a743894a0e4a801fc3\',\'21232f297a57a5a743894a0e4a801fc3\');����\r\n'
						. '* ���С�21232f297a57a5a743894a0e4a801fc3��������Ϊadmin��md5�ļ��ܴ��������Դ���һ���µ��ļ��ڸ�Ŀ¼��test.php����\r\n'
						. '* �ļ�����Ϊ "<?php echo md5(\'��������\');?>" ���ڵ�ַ������http://�����̳/test.php���md5���ܺ�����룬����ǵ�ɾ���ļ�test.php��',
'managername'		=> '// ��ʼ���û�������',
'managerpwd'		=> '// ��ʼ����������',
'hostweb'			=> '* ����վ�����ã�Ĭ��Ϊ1����������վ��',
'attach_url'		=> '* ����url��ַ����http:// ��ͷ�ľ��Ե�ַ��Ϊ��ʹ��Ĭ��',
'slaveConfig'		=> '* ������д�˴������Ѿ����ú����������ݿ⣬�������ж�д���봦��ǰ��������������ݿ⣬����������������ݿ⡣�����Ը�������ʵ��������ö�̨�����ݿ⣬����˵����ο�����������ݿ�',				
'week_1'			=> '����һ',
'week_2'			=> '���ڶ�',
'week_3'			=> '������',
'week_4'			=> '������',
'week_5'			=> '������',
'week_6'			=> '������',
'week_0'			=> '������',

'mode_bbs_mname'	=> '��̳ģʽ',
'mode_bbs_title'	=> '��̳',
'mode_o_mname'		=> '��������',
'mode_o_title'		=> '��԰',

'nav_index'			=> '��ҳ',
'mode_o_nav_home'	=> '�ҵ���ҳ',
'mode_o_nav_user'	=> '���˿ռ�',
'mode_o_nav_friend'	=> '����',
'mode_o_nav_browse'	=> '��㿴��',

'mode_bbs_nav'		=> "��ԱӦ��,app,,root\n"
						. "��������,lastpost,searcher.php?sch_time=newatc,root\n"
						. "������,digest,searcher.php?digest=1,root\n"
						. "��������,hack,,root\n"
						. "��Ա�б�,member,member.php,root\n"
						. "ͳ������,sort,sort.php,root\n"
						. "������Ϣ,sort_basic,sort.php,sort\n"
						. "����IPͳ��,sort_ipstate,sort.php?action=ipstate,sort\n"
						. "�����Ŷ�,sort_team,sort.php?action=team,sort\n"
						. "�������,sort_admin,sort.php?action=admin,sort\n"
						. "���߻�Ա,sort_online,sort.php?action=online,sort\n"
						. "��Ա����,sort_member,sort.php?action=member,sort\n"
						. "�������,sort_forum,sort.php?action=forum,sort\n"
						. "��������,sort_article,sort.php?action=article,sort\n"
						. "��ǩ����,sort_taglist,link.php?action=taglist,sort\n"

);
?>