<?php
!function_exists('readover') && exit('Forbidden');
$lang = array(
	'back'				=> '�� ��',

	'chinaz'			=> '���ChinaZ.com��ͼƬ��������',
	'chinaz_alt'		=> '�й�վ��վ����Ϊ������վ�ṩ����',
	'chinaz_logo'		=> 'http://www.chinaz.com/images/chinaz.gif',
	'chinaz_name'		=> '�й�վ��վ',
	'chinaz_url'		=> 'http://www.chinaz.com',
	'ckpwd'				=> '�ظ�����',

	'dbattachurl'		=> '����url��ַ����http:// ��ͷ�ľ��Ե�ַ��Ϊ��ʹ��Ĭ��',
	'dbcharset'			=> "Mysql��������(���ñ��룺gbk��big5��utf8��latin1){$crlf}���������̳��������������Ҫ���ô������޸�{$crlf}�벻Ҫ������Ĵ�����򽫿��ܵ�����̳������������",
	'dbhost'			=> '���ݿ������',
	'dbhostweb'			=> '����վ������',
	'dbifhostweb'		=> '�Ƿ�Ϊ��վ��',
	'dbinfo'			=> '���±���������������ݿ������˵�����޸�',
	'dbmanager'			=> "��ʼ�˽�ӵ����̳������Ȩ�ޣ���pwforums v6.3��֧�ֶ��ش�ʼ�ˣ��û���������ķ�����{$crlf}����1����./data/sql_config.php���ļ���������Ϊ777����NT����������ȡ��ֻ�������û�Ȩ������ΪFull Control����ȫ���ƣ�NT����������Ȼ����ԭʼ��ʼ���ʺŵ�¼��̨���ڸ�����̳��ʼ�˴������������޸Ĳ�����������Ϻ��ٽ�./data/sql_config.php���ļ���������Ϊ644����NT����������ֻ����NT�������������Ƽ���{$crlf}����2���ü��±���./data/sql_config.php�ļ����ڡ���ʼ���û������顱�м����µ��û������硰\$manager = array('admin');������Ϊ��\$manager = array('admin','phpwind');�����ڡ���ʼ���������顱�м����µ����룬�硰\$manager_pwd = array('21232f297a57a5a743894a0e4a801fc3');������Ϊ��\$manager_pwd = array('21232f297a57a5a743894a0e4a801fc3','21232f297a57a5a743894a0e4a801fc3');�������С�21232f297a57a5a743894a0e4a801fc3��������Ϊadmin��md5�ļ��ܴ��������Դ���һ���µ��ļ��ڸ�Ŀ¼��test.php�����ļ�����Ϊ \"<?php echo md5('��������');?>\" ���ڵ�ַ������http://�����̳/test.php���md5���ܺ�����룬����ǵ�ɾ���ļ�test.php��",
	'dbmanagername'		=> "��ʼ���û�������",
	'dbmanagerpwd'		=> '��ʼ����������',
	'dbname'			=> '���ݿ���',
	'database'			=> '���ݿ�����',
	'dbpconnect'		=> '�Ƿ�־�����',
	'dbpre'				=> '�����ַ�',
	'dbpres'			=> '���ݿ��ǰ׺',
	'dbpw'				=> '���ݿ�����',
	'dbuser'			=> '���ݿ��û���',

	'error_777'			=> '<b>{#filename}</b> �ļ����ļ���777���Լ�ⲻͨ��',
	'error_777s'		=> '<b>{#filenames}</b> ���ļ����ļ���777���Լ�ⲻͨ��',
	'error_admin'		=> '��admin.php�ĳ�{#db_adminfile}����ܼ�������',
	'error_adminemail'	=> '��ʼ�˵������䲻��Ϊ��',
	'error_adminname'	=> '��ʼ���û�������Ϊ��',
	'error_adminpwd'	=> '��ʼ�����벻��Ϊ��',
	'error_ckpwd'		=> '�����������벻ͬ',
	'error_delrecycle'	=> '���¼��̨ɾ������վ��飬��ǰ̨����������̨����վ����������Ӳ���',
	'error_nodatabase'	=> '��û�и� <b>{#dbname}</b> ���ݿ�Ĳ���Ȩ�޻�ָ�������ݿ� <b>{#dbname}</b> ������,������Ȩ�޽���,����ϵ����������Ա!',
	'error_dbhost'		=> '���ݿ����������Ϊ��',
	'error_dbname'		=> '���ݿ�������Ϊ��',
	'error_dbpw'		=> '��������ݿ�����Ϊ�գ��Ƿ�ʹ�ÿյ����ݿ�����',
	'error_dbuser'		=> '���ݿ��û�������Ϊ��',
	'error_delinstall'	=> 'ϵͳ�޷�ɾ��{#basename}�����¼FTPɾ�����ļ�',
	'error_forums'		=> '���ID���󣬷Ƿ�����!',
	'error_forums1'		=> '��̳����һ����Ϊ��',
	'error_forums2'		=> '��̳���һ����Ϊ��',
	'error_forums3'		=> '����д����̳��������̳���������Ϊ��',
	'error_forums4'		=> '����д����̳���������̳��������Ϊ��',
	'error_mysqli'		=> 'ע�⣺���ķ����������õ���MySQL4.1.3�汾��������ʹ�� mysqli�������Զ�����Ϊ mysql',
	'error_nothing'		=> '������Ŀû����д',
	'error_table'		=> '{#pw_table}�Ѿ����ڶ���PW��̳���ݿ⣬�������ݿ�����������ɾ��{#pw_table}���ٽ�������',
	'error_unfind'		=> '<b>{#filename}</b> �ļ����ļ��в�����,���½��ļ��л��½���Ӧ���ļ������ļ����ɣ�',
	'error_unfinds'		=> '<b>{#filenames}</b> ���ļ����ļ��в����ڣ����½��ļ��л��½���Ӧ���ļ������ļ����ɣ�',

	'forums1'			=> '��̳����һ',
	'forums2'			=> '��̳���һ',
	'forums3'			=> '��̳�����',
	'forums4'			=> '��̳����',
	'forumsmsg'			=> '��д����Ͱ������',

	'hacklist'			=> '����б�',
	'have_file'			=> '���Ѿ���װ�� phpwind���������°�װ����ɾ�����ļ���{#bbsurl}/data/{#lockfile}���ٽ��а�װ',
	'have_upfile'		=> '���Ѿ������� phpwind������������������ɾ�����ļ���{#bbsurl}/data/{#lockfile}.lock���ٽ��к�������',
	'have_install'		=> '���ݿ�<span class="s1">{#dbname}</span>���Ѿ���װ��phpwind.��������װ�������ԭ�������ݣ�ʹ���������ݿ��롰������һ������������',



	'header_pw'			=> '�ٷ���̳',
	'header_help'		=> 'ʹ���ֲ�',

	'judg_1'			=> '������ʤ',
	'judg_2'			=> '������ʤ',
	'judg_3'			=> '˫��սƽ',

	'last'				=> '�� ��',
	'link_index'		=> 'ϵͳǰ̨��ַ',
	'link_admin'		=> 'ϵͳ��̨��ַ',
	'link_phpwind'		=> 'PW�ٷ���̳',
	'log_help'			=> '<li><a href="http://www.phpwind.com/help/" target="_blank" class="black">ȫ�����߰����ֲ�</a></li><li><a href="http://www.phpwind.net/read-htm-tid-621045.html" target="_blank" class="black">phpwind7�Ƽ�����</a></li><li><a href="http://www.phpwind.net/read-htm-tid-691673.html" target="_blank" class="black">phpwind 7.5 css������</a></li><li><a href="http://www.phpwind.net/read-htm-tid-696195.html" target="_blank" class="black">PW 6.32��PW��������̳�</a></li><li><a href="http://www.phpwind.net/read-htm-tid-704022.html" target="_blank" class="black">�Ż�ģʽ��˵��</a></li><li><a href="http://www.phpwind.net/read-htm-tid-704023.html" target="_blank" class="black">ģʽ�����˵��</a></li><li class="right"><a href="http://www.phpwind.net/thread-htm-fid-2.html" target="_blank" class="black">������Դ�����</a></li>',
	'log_install'		=> '<p>1�����л�������PHP+MYSQL</p><p>2����װ����</p><p>�� Linux �� Freebsd �������°�װ����</p>
		<p>��һ����</p><p>ʹ��ftp���ߣ��ö�����ģʽ������������uploadĿ¼�µ������ļ��ϴ������Ŀռ䣬�����ϴ���Ŀ¼Ϊ upload��</p>
		<p>�ڶ�����</p><p>��ȷ������Ŀ¼���ļ�����Ϊ (777) ��дģʽ��</p><div class="c"></div>
		<div style="padding:.5em 2em">
			<span class="black" style="width:250px; float:left">attachment</span><span class="black" style="padding-right:2em">data</span><br />
			<span class="black" style="width:250px; float:left">attachment/cn_img</span><span class="black" style="padding-right:2em">data/bbscache</span><br />
			<span class="black" style="width:250px; float:left">attachment/photo</span><span class="black" style="padding-right:2em">data/groupdb</span><br/>
			<span class="black" style="width:250px; float:left">attachment/thumb</span><span class="black" style="padding-right:2em">data/guestcache</span><br/>
			<span class="black" style="width:250px; float:left">attachment/upload</span><span class="black" style="padding-right:2em">data/style</span><br/>
			<span class="black" style="width:250px; float:left">attachment/mini</span><span class="black" style="padding-right:2em">data/tmp</span><br/>
			<span class="black" style="width:250px; float:left">html</span><span class="black" style="padding-right:2em">data/tplcache</span><br/>
			<span class="black" style="width:250px; float:left">html/read</span><span class="black" style="padding-right:2em">data/tplcache</span><br/>
			<span class="black" style="width:250px; float:left">html/channel</span><span class="black" style="padding-right:2em">data/forums</span><br/>
			<span class="black" style="width:250px; float:left">html/portal/bbsindex</span><span class="black" style="padding-right:2em">html/portal/bbsindex/main.htm</span><br/>
			<span class="black" style="width:250px; float:left">html/portal/bbsindex/config.htm</span><span class="black" style="padding-right:2em">html/portal/bbsindex/index.html</span><br/>
			<span class="black" style="width:250px; float:left">html/portal/bbsradio</span><span class="black" style="padding-right:2em">html/portal/bbsradio/main.htm</span><br/>
			<span class="black" style="width:250px; float:left">html/portal/bbsradio/config.htm</span><span class="black" style="padding-right:2em">html/portal/bbsradio/index.html</span><br/>
			<span class="black" style="width:250px; float:left">html/portal/oindex</span><span class="black" style="padding-right:2em">html/portal/oindex/main.htm</span><br/>
			<span class="black" style="width:250px; float:left">html/portal/oindex/config.htm</span><span class="black" style="padding-right:2em">html/portal/oindex/index.html</span><br/>
			<span class="black" style="width:250px; float:left">html/portal/groupgatherleft/main.htm</span><span class="black" style="padding-right:2em">html/portal/groupgatherleft/config.htm</span><br/>
			<span class="black" style="width:250px; float:left">html/portal/groupgatherleft/index.html</span><span class="black" style="padding-right:2em">html/portal/groupgatherright/main.htm</span><br/>
			<span class="black" style="width:250px; float:left">html/portal/groupgatherright/config.htm</span><span class="black" style="padding-right:2em">html/portal/groupgatherright/index.html</span><br/>
			<span class="black" style="width:250px; float:left">html/portal/userlist/main.htm</span><span class="black" style="padding-right:2em">html/portal/userlist/config.htm</span><br/>
			<span class="black" style="width:250px; float:left">html/portal/userlist/index.html</span><span class="black" style="padding-right:2em">html/portal/usermix/main.htm</span><br/>
			<span class="black" style="width:250px; float:left">html/portal/usermix/config.htm</span><span class="black" style="padding-right:2em">html/portal/usermix/index.html</span><br/>
			<span class="black" style="width:250px; float:left">html/stopic</span><span class="black" style="padding-right:2em">js/</span><br/>
		</div>
		<p>��������</p><p>���� <small>http://yourwebsite/upload/{#basename}</small> ��װ�������밲װ�����Ϣ�����ϣ���ɰ�װ��</p>
		<p>�� Windows �������°�װ����</p><p>��һ����</p><p>ʹ��ftp���ߣ�������������uploadĿ¼�µ������ļ��ϴ������Ŀռ䣬�����ϴ���Ŀ¼Ϊ upload��</p>
		<p>�ڶ�����</p><p>���� <small>http://yourwebsite/upload/{#basename}</small> ��װ�������밲װ�����Ϣ�����ϣ���ɰ�װ��</p>',
	'log_install_t'		=> '��װ��֪',
	'log_marketing'		=> '<div>�𾴵�վ����</div><p>��л��ѡ��phpwind ����ϵͳ�����ڱ˴˵����������ǳ���������phpwindһվʽӪ���ƻ���</p><p>phpwindһվʽӪ���ǰ������վ����ȡ�����������Ӫ��ƽ̨��������������ɫ���ڣ�</p><div style="padding:1em 2em">��ֽ��phpwind ����ϵͳ��ʹ���Ͷ�������������ӵ���Ӧ�֣�<br />����רҵ�����Ż��Ĺ����룬���������������ԣ�<br />����һ���ʵս����Ӫ�������ۻ�����ԣ�<br />ֵ�������ĺ�������뾫����ѡ�Ĺ���Ʒ����Google Adsense�����������â�Ƽ����޹�˾��<br />�������ƵĽ��㷽ʽ��������������������ҽ��н��㣬�������������Ϊ100Ԫ��</div><p><a href="http://union.phpwind.com/question.php" target="_blank" class="black"><u>����phpwindһվʽӪ��</u></a>&nbsp; &nbsp;<a href="http://www.phpwind.net/thread.php?fid=82" target="_blank" class="black"><u>������</u></a></p><p>�ӽ�վ����Ǯ�� phpwind��Զ��������ϵĻ�飡</p>',
	'log_marketing_t'	=> 'phpwind����Ӫ��',
	'log_partner'		=> '<p>������ڿ�Դ��ҵ����ǰ���ĽŲ���phpwindЯ�ָ���WEBӦ�ÿ�����飬�����������졣�ڴ������Ƽ����������PW Forums������������������Ͻ�Ϊ���ṩ���ཨվѡ��</p><div class="c"></div><div style="padding:2em"><a href="http://www.dedecms.com" target="_blank" class="black" style="width:250px; float:left">DedeCms֯�����ݹ���ϵͳ</a><a href="http://www.phpwind.net/thread.php?fid=90" target="_blank" class="black" style="padding-right:2em">������</a><br /><a href="http://www.dedecms.com" target="_blank" class="black" style="width:250px; float:left">CMSWARE˼ά��վ���ݹ���ϵͳ</a><a href="http://www.phpwind.net/thread.php?fid=92" target="_blank" class="black" style="padding-right:2em">������</a><a href="http://www.phpwind.net/read.php?tid=527223" target="_blank" class="black">�������Ͻ̳�</a><br /><a href="http://www.php168.com" target="_blank" class="black" style="width:250px; float:left">PHP168��վϵͳ</a><a href="http://www.phpwind.net/thread.php?fid=91" target="_blank" class="black" style="padding-right:2em">������</a><a href="http://down2.php168.com/mv/6.rar" target="_blank" class="black">�������Ͻ̳�</a><br/><a href="http://www.shopex.com.cn" target="_blank" class="black" style="width:250px; float:left">ShopEx�����̵��̳�ϵͳ</a><a href="http://www.phpwind.net/thread.php?fid=93" target="_blank" class="black" style="padding-right:2em">������</a><a href="http://www.phpwind.net/read.php?tid=527222" target="_blank" class="black">�������Ͻ̳�</a></div><p>�й�վ��վChinaZ.com����Ը���վ���ṩ��Ѷ����Դ�������վ����ӵ�й�������Դ�뷢���������ļ��ۼ�վ�������ܵļ�����Ѷ�������й�վ��վ�����ڴ���ݸ�վ��ԭ����ѶȦ����������վ�����ͽ�վ���飬�ø����˹�ע������վ����������վ��</p><p>phpwind���й�վ��վ�����໥��ͬ����Ϊս�Ժ�����飬ϣ���ܹ���δ��Я�ֳ������绥���빲����Ϊվ���ṩ�����ܶ�ķ���</p><p>���ڷ��� <a href="http://www.chinaz.com/zhuanti/phpwind/index.htm" target="_blank" class="black"><u>ChinaZ.com</u></a> �˽�phpwind��������Ϣ����༼���ο����ϣ�</p>',
	'log_partner_t'		=> '�������',
	'log_repair'		=> '<p>(һ) �����汾��phpwind v{#from_version}��{#wind_repair}��</p>
		<p>(��) ���ð汾��phpwind v{#from_version}</p>
		<p>(��) ���²���:</p>
		<p>��һ�����򲹶�ǰ����ر�������, ��������ʧ�ܵ������ݶ�ʧ</p>
		<p>�ڶ��������uploadĿ¼�´��� images��attachment��data Ŀ¼,�뽫�ĳ�����̳��Ӧ��Ŀ¼����ע: ���Ե���̳��̨�� ������-��ȫ���Ż�-��̬Ŀ¼�� ��鿴</p>
		<p>��������ʹ��ftp���ߣ�������������uploadĿ¼�µ������ļ��ϴ������Ŀռ䣬�����ϴ���Ŀ¼Ϊ upload��</p>
		<p>���Ĳ������� <small>http://yourwebsite/upload/{#basename}</small> �������³��򣬰���ʾ���в���, ֱ��������ɣ�</p>',
	'log_resources'		=> '<p>��л��ѡ��ʹ��phpwind ����ϵͳ������������ǵĲ�Ʒ���������κ����ʻ��飬��ʱ��ӭ������Ϣ�����ٷ���̳��http://www.phpwind.net�����������ǵĵ�������<font color="#f79646"><b>client@phpwind.net</b></font>��</p><br /><p>�������Ҫ��������̳����������ݷ�������ϵͳ�����ǳ��������Ƽ����ǵĺ�����顪��PHP168��<br />����<a href="http://bbs.php168.com/read-bbs-tid-205437.html" target="_blank" style="color:#00727c">������ҳ</a>&nbsp; &nbsp; &nbsp; &nbsp; <a href="http://www.phpwind.net/thread-htm-fid-91.html" target="_blank" style="color:#00727c">������</a></p><p>�������Ҫ��������̳��������������ϵͳ�����ǳ����Ƽ����ǵĺ�����顪��ShopEX��<br />����<a href="http://www.phpwind.com/shopex" target="_blank" style="color:#00727c">������ҳ</a>&nbsp; &nbsp; &nbsp; &nbsp; <a href="http://www.phpwind.net/thread-htm-fid-93.html" target="_blank" style="color:#00727c">������</a></p><p>�������Ҫ��������̳������Ĳ���ϵͳ�����ǳ��������Ƽ����ǵĲ��Ͳ�Ʒ����LxBlog��<br />����<a href="http://www.phpwind.com/introduce.php?action=introduce&job=bloginfo" target="_blank" style="color:#00727c">�ٷ���ҳ</a>&nbsp; &nbsp; &nbsp; &nbsp; <a href="http://www.phpwind.net/thread-htm-fid-21.html" target="_blank" style="color:#00727c">������</a>&nbsp; &nbsp; &nbsp; &nbsp; <a href="http://www.phpwind.net/read-htm-tid-620820.html" target="_blank" style="color:#00727c">��̳�벩������ͼ�Ľ̳�</a></p><p>�������Ҫ���Ի�������̳����ӭ������phpwind�ٷ���̳��ȡ������Դ��phpwind��������ʮ�ҵ������Ŷ���ҵ���飬��վ��һ����δ����</p><p><a href="http://www.phpwind.net/hack.php?H_name=hackcenter&action=style" target="_blank" style="color:#00727c">��ȡ���������̳���</a>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <a href="http://www.phpwind.net/hack.php?H_name=hackcenter&action=hack" target="_blank" style="color:#00727c">��ȡ���������̳���</a></p>',
	'log_union'			=> '<p>��װ��ɺ�������ͼ�����Ӷ�¥�����Ϸ������ְ�������ͳһͶ�ŵĹ�棬�������涼��վ�����У����ɰ�������ͳһ���㡣����������� <a href="http://union.phpwind.com/news.php?action=read&nid=46" target="_blank" style="color:#00727c">http://union.phpwind.com/news.php?action=read&nid=46</a>��</p><p>���ڣ��ڰ汾��װ���֮ǰ�������ɿ�������������3������������λ��</p><p><input type="checkbox" name="banners" value="1" CHECKED> ȫվ���� 468*60 <input type="checkbox" name="atcbottoms" value="1"> ���������·� 468*60 <input type="checkbox" name="footers" value="1"> ȫվ�ײ� 760*90 </p><p>�������δע�ᰢ�������ʺţ��벻Ҫ���ġ���������ͼ�滹��������Ĺ�棬�ӵ�һ��PV�͵����ʼ�����ݽ��ᱣ����Ϊ��Ԥ�����ʻ��У�ֱ����ע���¼Ӫ��ƽ̨��<a href="http://union.phpwind.com" target="_blank" style="color:#00727c">http://union.phpwind.com</a>�����󶨰��������ʺź󣨿���Ӫ��ƽ̨һ������ɰ��������ʺŵ�ע����󶨣�����������ʱ��¼��������ƽ̨��<a href="http://www.alimama.com" target="_blank" style="color:#00727c">http://www.alimama.com</a>���鿴����ͼ�������ݲ��������档<a href="http://www.phpwind.net/read-htm-tid-633350.html" target="_blank" style="color:#00727c">�鿴ͼ��˵��</a></p><p>�������ӵ�а��������ʺţ�ֻ���¼Ӫ��ƽ̨���м���������ɡ�</p><p>7��15��-8��15��,��<a href="http://www.phpwind.net/read.php?tid=633351" target="_blank" style="color:#00727c">�����������ֽ𣬷ּ�����</a>�����ʽ��ʼ�������ע��</p>',
	'log_unionmsgt'		=> '��ӭ��ʹ�ò�����phpwind V7.5 sp3 ����ϵͳ��',
	'log_unionmsgc'		=> '�𾴵�վ����\n\n������ӭ��ʹ�ò�����phpwind V7.5 sp3����ϵͳ���ٷ�v6.3 RC��ʾ��3��15�տ������񣬰汾������ε��������ƣ��������ȶ�����RC�湫�������ڼ䣬�緢��BUG���������汾���κν��顢�������ӭ������������лظ�[url]http://www.phpwind.net/read.php?tid=603810[/url]��\n\n�������⣬��4��30����ͨ��phpwind����Ӫ��ƽָ̨����վ�������Ի�ð��������ṩ�����߹�������Ӫ����̨�Ǽ�ע�����������裨[url]www.alimama.com[/url]��ƽ̨��վ��������������[b]��������[/b]��\n����1.�����ȼ������а��������潻��ƽ̨���ڶ๦�ܣ����������������������и������ƹ�ģʽ�ȣ�\n����2.phpwind����Ӫ��ƽ̨�ۼ����볬��100Ԫ��վ��������ֱ��������վ������뱣�Ϸ����������ȶ��������£���ø����ֵ��\n����3.��δ����phpwindվ�������ð��������潻��ƽ̨��phpwind��̳�����ʵ�ָ����ݡ������ж������Ƶ����߹�����\n����ͬʱ��phpwind����Ӫ��ƽ̨Google Adsense���»��ߡ�������Ӫ����һ�ڣ�����ʱֹͣͶ�š�\n\n����[b]��������ʣ�[url]http://www.phpwind.net/read-htm-tid-602463.html[/url]��[/b]\n\nphpwind�ٷ�\n2008-4-29',
	'log_update'		=> '<li><a href="http://www.phpwind.net/read-htm-tid-704022.html" target="_blank" class="black">�Ż�ģʽ��</a></li><li><a href="http://www.phpwind.net/read-htm-tid-681220.html" target="_blank" class="black">PW7.0֮����</a></li><li><a href="http://www.phpwind.net/read-htm-tid-677057.html" target="_blank" class="black">̽��PW7.0֮�û�����</a></li><li><a href="http://www.phpwind.net/read-htm-tid-679230.html" target="_blank" class="black">PW7.0 ֮ �Զ�����</a></li><li><a href="http://www.phpwind.net/read-htm-tid-674290.html" target="_blank" class="black">PW7.0 ֮���밲ȫ����</a></li><li><a href="http://www.phpwind.net/read-htm-tid-680607.html" target="_blank" class="black">�̵�7.0ϸ�ڹ��� (һ��</a></li><li><a href="http://www.phpwind.net/read-htm-tid-680768.html" target="_blank" class="black">�̵�7.0ϸ�ڹ��� (����</a></li><li><a href="http://www.phpwind.net/read-htm-tid-681088.html" target="_blank" class="black">�̵�7.0ϸ�ڹ��� (����</a></li><li class="right"><a href="http://www.phpwind.net/thread-htm-fid-78.html" target="_blank" class="black">�˽��������</a></li>',
	'log_upto'			=> '<p>1�����л�������PHP+MYSQL��</p><p>2�����ð汾��{#from_version}��</p><p>3���������裺</p><p>�� Linux �� Freebsd �������°�װ������</p><p>��һ����</p><p>����ǰ����ر�����̳�ļ�������, ��������ʧ�ܵ������ݶ�ʧ</p><p>�ڶ�����</p><p>�뽫 upload Ŀ¼�ڵ� images Ŀ¼����Ϊ��̳��ͼƬĿ¼����ע: ���Ե���̳��̨�� ȫ�� ��鿴��</p><p>��������</p><p>ʹ��ftp�����еĶ�����ģʽ������������� upload ��������ļ������ϴ���������̳Ŀ¼�������ϴ���Ŀ¼�Ծ�Ϊ upload���������ļ�(<small>{#basename}</small>)�ϴ��� upload ��</p><p>���Ĳ���</p><p>���� <small>http://yourwebsite/upload/{#basename}</small> �������򣬰�������ʾ��������, ֱ������������</p><p>�� Windows �������°�װ������</p><p>��һ����</p><p>����ǰ����ر�����̳�ļ�������, ��������ʧ�ܵ������ݶ�ʧ</p><p>�ڶ�����</p><p>�뽫 upload Ŀ¼�ڵ� images Ŀ¼����Ϊ��̳��ͼƬĿ¼����ע: ���Ե���̳��̨�� �������� ��鿴��</p><p>��������</p><p>ʹ��ftp���ߣ������������ upload ��������ļ������ϴ���������̳Ŀ¼�������ϴ���Ŀ¼�Ծ�Ϊ upload���������ļ�(<small>{#basename}</small>)�ϴ��� upload ��</p><p>���Ĳ���</p><p>���� <small>http://yourwebsite/upload/{#basename}</small> �������򣬰�������ʾ��������, ֱ������������</p>',
	'log_upto_t'		=> '������֪',
	'log_repair_t'		=> '������֪',

	'managermsg'		=> '��ʼ����Ϣ',

	'name'				=> '�û���',
	'nf_reply'			=> '���»ظ�',
	'nf_new'			=> '��������',
	'nf_dig'			=> '���¾���',
	'nf_pic'			=> '����ͼƬ',

	'promptmsg'			=> '��ʾ��Ϣ',
	'pwd'				=> '����',

	'redirect'			=> '�Զ���ת���ɹ���������',
	'redirect_msg'		=> '�������ڸ���������������̱Ƚ�������������Ҫ�����������ӵ�ʱ�䣬�����ĵȴ�......',

	'redirect_msgs'		=> ' &nbsp; &nbsp; <font color="red">{#start}</font> TO <font color="red">{#end}</font>',

	'setform_1'			=> '������Ϣ',
	'setfrom_1_inro'	=> '<table cellspacing="1" cellpadding="1" align="left" width="100%" style="background:#D4EFF7;text-align:left"><tr><td width="30%" align="center" style="background:#fff;height:25px;"><b>��  ϵ ��:</b></td><td style="background:#fff;padding-left:5px"><p contentEditable=true></p></td></tr><tr><td width="30%" align="center" style="background:#fff;height:25px;"><b>��ϵ��ʽ:</b></td><td style="background:#fff;padding-left:5px"><p contentEditable=true></p></td></tr><tr><td width="30%" align="center" style="background:#fff;height:25px;"><b>��������:</b></td><td style="background:#fff;padding-left:5px"><p contentEditable=true></p></td></tr><tr><td width="30%" align="center" style="background:#fff;height:25px;"><b>����λ��:</b></td><td style="background:#fff;padding-left:5px"><p contentEditable=true></p></td></tr><tr><td width="30%" align="center" style="background:#fff;height:25px;"><b><font color="#ff3300">����</font>�۸�:</b></td><td style="background:#fff;padding-left:5px"><p contentEditable=true></p></td></tr><tr><td width="30%" align="center" style="background:#fff;height:25px;"><b>���ݲ��:</b></td><td style="background:#fff;padding-left:5px"><p contentEditable=true></p></td></tr><tr><td width="30%" align="center" style="background:#fff;height:25px;"><b>�������:</b></td><td style="background:#fff;padding-left:5px"><p contentEditable=true></p></td></tr><tr><td width="30%" align="center" style="background:#fff;height:25px;"><b>�������:</b></td><td style="background:#fff;padding-left:5px"><p contentEditable=true></p></td></tr><tr><td width="30%" align="center" style="background:#fff;height:25px;"><b>�����:</b></td><td style="background:#fff;padding-left:5px"><p contentEditable=true></p></td></tr></table>',
	'showmsg'			=> '��װ��ʾ��Ϣ',
	'showmsg_upto'		=> '������ʾ��Ϣ',
	'sqlconfig_file'	=> '���ݿ������ļ�����д����./data/sql_config.php���ļ���������Ϊ777����NT����������ȡ��ֻ�������û�Ȩ������ΪFull Control����ȫ���ƣ�NT��������',
	'step_null'			=> '',
	'step_next'			=> '��һ��',
	'step_prev'			=> '��һ��',
	'step_1_upto'		=> '������֪',
	'step_1_left_upto'	=> '����������Ķ��������˵��',
	'step_1_left_repair'=> '����������Ķ��������˵��',
	'step_1_right_upto'	=> '��ʼ����',
	'step_1_right_repair'=> '��ʼ����',
	'step_readme'		=> '�Ķ���װЭ��',
	'step_readme_left'	=> '����������Ķ������װЭ��',
	'step_readme_right'	=> 'ͬ��Э�飬��һ��',
	'step_database'		=> '��д���ݿ���Ϣ�봴ʼ����Ϣ',
	'step_database_left'	=> '�Ķ���װЭ��',
	'step_database_right'	=> '��װ��ϸ��Ϣ',
	'step_hack'			=> 'ѡ����Ҫ��װ�Ĳ��',
	'step_view'			=> '��װ��ϸ��Ϣ',
	'step_view_left'	=> '��д���ݿ���Ϣ',
	'step_view_right'	=> 'ȷ�ϰ�װ���',
	'step_resources'	=> '��Դ����',
	'step_resources_left'=> '������鲢�������',
	'step_5'			=> 'Ĭ��ģʽѡ��',
	'step_5_left'		=> '��װ��ϸ��Ϣ',
	'step_5_right'		=> '��Դ����',
	'step_6'			=> '������鲢�������',
	'step_6_left'		=> 'ȡ�����˳�',
	'step_6_right'		=> ' �� �� ',
	'step_finish'		=> '��ɲ���',
	'step_union'		=> '����Ӫ��',
	'step_union_right'	=> '��һ��',
	'step_app'		    => 'APP���ӽ����ɹ���',
	'writable_success'	=> '��д',
	'writable_error'	=> '����д',
	'success'			=> '�� ��',
	'success_1'			=> 'ͬ�ⰲװ!',
	'success_2'			=> '{#filename} 777���Լ��ͨ��!',
	'success_3'			=> 'ϵͳ���ô����ɹ�!',
	'success_3_1'		=> '�������ݱ� {#tablename} ... �ɹ�!',
	'success_3_2'		=> '��ʼ����Ϣ�����ɹ�!',
	'success_5'			=> 'Ĭ��ģʽѡ��ɹ�!',
	'success_7'			=> '��� {#value} �༭���!',
	'success_7_1'		=> '��� {#value} �����ɹ�!',
	'success_7_2'		=> '���ݸ��³ɹ�!',
	'success_4'			=> '�����ӳɹ�!',
	'success_4_1'		=> '����������ӳɹ�!',
	'success_4_2'		=> 'ϵͳ����������!',
	'success_install'	=> '��ϲ�������� phpwind v{#wind_version}�Ѿ���װ�ɹ���',
	'success_repair'	=> '��ϲ�������� phpwind v{#wind_version}�Ѿ����³ɹ���',
	'success_upto'		=> '��ϲ�������� phpwind v{#wind_version}�Ѿ������ɹ���',
	'welcome_msg'		=> '��ӭ���� phpwind ��װ�򵼣���װǰ����ϸ�Ķ���װ˵����ſ�ʼ��װ����װ�ļ�����ͬ���ṩ���й������װ��˵����������ϸ�Ķ�������<div style="margin-top:.5em">��װ�����������κ����� &nbsp;<a href="http://www.phpwind.net/thread-htm-fid-2.html" target="_blank" class="black"><u><b>�뵽�ٷ�������Ѱ�����</b></u></a></div>',
	'welcome_msgupto'	=> '��ӭ���� phpwind �����򵼣�����ǰ����ϸ�Ķ� ����˵�����ÿ��ϸ�ں���ܿ�ʼ�����������ļ�����ͬ���ṩ���й����������˵��������ͬ����ϸ�Ķ����Ա�֤�������̵�˳�����С�',
	'welcome_msgrepair'	=> '��ӭ���� phpwind �����򵼣�����ǰ����ϸ�Ķ� ����˵�����ÿ��ϸ�ں���ܿ�ʼ���¡������ļ�����ͬ���ṩ���й�������µ�˵��������ͬ����ϸ�Ķ����Ա�֤���½��̵�˳�����С�',
	'finish_exit'		=>'��  ��',
	'title_install'		=> 'phpwind ��װ����',
	'title_repair'		=> 'phpwind ��������',
	'title_upto'		=> 'phpwind ��������',
	'env_os'			=> '��UNIX',
	'unlimited'			=> '������',
	'environment_check' => '��⻷��',
	'insert_data'		=> '��������',
	'install_complete'	=> '��ɰ�װ',
	'check_enrironment'	=> '�������',
	'current_server'	=> '��ǰ������',
	'recommend_env'		=> '�Ƽ�����',
	'lowest_env'		=> '���Ҫ��',
	'os'				=> '����ϵͳ',
	'phpversion'		=> 'PHP�汾',
	'attach_upload'		=> '�����ϴ�',
	'disk_space'		=> '���̿ռ�',
	'right_check'		=> 'Ŀ¼���ļ�Ȩ�޼��',
	'current_status'	=> '��ǰ״̬',
	'required_status'	=> '����״̬',
	'recheck'			=> '���¼��',
	'databaseinfo'		=> '���ݿ���Ϣ',
	'databasetiop'		=> '���ݿ��������ַ��һ��Ϊlocalhost',
	'dbpretip'			=> '����ʹ��Ĭ�ϣ�ͬһ���ݿⰲװ�����̳ʱ���޸�',
	'manager'			=> '����Ա�ʺ�',
	'install_completed'	=> '��װ��ɣ��������',
	'complete_tips'		=> '��������Զ���ת�������˹���Ԥ',
	'installing'		=> '���ڰ�װ...',

	//7.3 Start
	'step_pre'			=> '��һ��',
	'step_next'			=> '��һ��',
	'accept'			=> '�� ��',
	'left_info'			=> '<dt style="margin:0;">���¼�¼</dt>
        <dd style="padding-top:5px;"><a href="http://www.phpwind.net/read-htm-tid-1251662.html" target="_blank">phpwind 8.5������</a></dd>
        <dd><a href="http://www.phpwind.net/read-htm-tid-1251648.html" target="_blank">phpwind 8.5 bug�޸��б�</a></dd>
        <dt>�����ĵ�</dt>
        <dd style="padding-top:5px;"><a href="http://www.phpwind.net/read-htm-tid-1251651.html" target="_blank">��װ�̳�</a></dd>
		<dd><a href="http://www.phpwind.net/read-htm-tid-1251656.html" target="_blank">�����̳�</a></dd>
		<dd><a href="http://www.phpwind.net/read-htm-tid-1251658.html" target="_blank">�����װ�̳�</a></dd>
        <dd><a href="http://www.phpwind.net/read-htm-tid-1251659.html" target="_blank">���װ�̳�</a></dd>
        <dd><a href="http://www.phpwind.net/read-htm-tid-1250119.html" target="_blank">���ݿ�ṹ�ֲ�</a></dd>
        <dd><a href="http://www.phpwind.net/thread-htm-fid-54.html" target="_blank">���߷���</a></dd>
		<dd><a href="http://faq.phpwind.net/" target="_blank">��������</a></dd>',
	'step_problem'		=> '<p>�����������������κ����⣬</p> <p><a href="http://www.phpwind.net/thread-htm-fid-2.html" target="_blank"><strong>�뵽�ٷ���̳Ѱ�����</strong></a></p>',
	'step_deldir'		=> '������ʾ���뼰ʱɾ��admin/code.php��template/admin/code.htm��hack/app�����ļ���',

	//update
	'update_1'			=> '�Ķ�������֪',
	'update_2'			=> '���������׶�',
	'update_3'			=> '��Դ����',
	'update_finish'		=> '�����ɹ���',
	'admin_name'		=> '��ʼ���ʺţ�',
	'admin_pwd'			=> '���룺',
	'admin_login_2'		=> ' �� ¼ ',
	'login_error'		=> '�û�����������󣬵�¼ʧ��',

	//install
	'install_1'			=> '�Ķ���װЭ��',
	'install_2'			=> '��д������Ϣ',
	'install_3'			=> 'ѡ����Ҫ��װ�Ĳ��',
	'install_4'			=> '��װ��ϸ��Ϣ',
	'install_5'			=> 'Ĭ��ģʽѡ��',
	'install_6'			=> '��Դ����',
	'install_finish'	=> '��װ�ɹ���',
	'forum_finish'		=> '��鴴�����',
	'app_limit'			=> '�����õ����������ܺͳ�����250',

	'app_1'				=>'��Ӱ��',
	'app_2'				=>'����Ϊ���������������͵�����',
	'app_3'				=>'ÿ����������',
	'app_4'				=>'��ʾ��<br />1�����ϲ�����Ϊ������һ���µ�APP�ʻ����ʻ���Ϣ������͵�admin�ġ�����Ϣ���䡱�С�<br />
2��Ŀǰ��ÿ����������Ϊ250�����������ã���������ʱ��¼��APPƽ̨>���ӽ���>�Զ����ء������޸Ļ�رղ�����������ƿ��ڡ���̳��̨>���������޸ġ�<br />
3���ύ�����ز�������24Сʱ����Чִ�С�<br />
4���ڱ��ذ�װ����̳�����ϲ�����Ч��<br />
5���鿴��ϸ�̳� <a href=http://www.phpwind.net/read.php?tid=753545 target=_blank>http://www.phpwind.net/read.php?tid=753545</a>',
	'app_5'				=>'APP ���ӽ�����һ���ʵվ�����ݣ��ƹ��Լ�վ���Ӧ�á�<br />
#��ʵ����<br />
ͨ��phpwind APP���ӽ�����Ϊphpwindվ����վ���ṩһ��ƽ̨��ʵ��վ����վ��֮���������Դ���������ϴ����ӵ�ƽ̨����ƽ̨�������������Ӧ��������Ҫ�����������������趨����ѡ�������ݷ��࣬�������ֶ����غ��Զ��������ַ�ʽ��<br />
#�ƹ�վ��<br />
ÿһ����������ʱ������֤����ԭ����ַ�����ӣ��������޸ġ����ӱ���¼��Խ�ࡢ��¼���ӵ�վ���PRֵȨ��Խ�ߣ���վ�������Ȩ��Ҳ��Խ�ߣ��Ӷ�������վ�������������¼����PRֵ��<br />
�������뿪ͨ��ѿռ�һ����װ��̳��ʱ��ϵͳ���������������ӽ����������������ӡ���Ҳ����ѡ��������������֮�����ʱ����APPƽ̨>�������>���ӽ��������޸����á�<br />
��ϸ���� <a href=http://www.phpwind.net/read-htm-tid-700917.html target=_blank>http://www.phpwind.net/read-htm-tid-700917.html</a><br />
��̳��ѯ <a href=http://www.phpwind.net/thread-htm-fid-100.html target=_blank>http://www.phpwind.net/thread-htm-fid-100.html</a><br />
�鿴APP����Ӧ�� <a href=http://www.phpwind.net/read-htm-tid-717216.html target=_blank>http://www.phpwind.net/read-htm-tid-717216.html</a>',
	'app_subject'		=> 'APPƽ̨��Ϣ',
	'app_content1'		=> "�𾴵�վ����<br />
ͨ������APPƽ̨�ϵ���ʱ�ʻ������ѿ���APPƽ̨�����ӽ������еġ��Զ����ء���������ʱ�ʻ���Ϣ���¡�<br />
�û�����{#username}<br />
���룺{#pwd}<br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;������ʱ�û����������ҽ���һ���޸Ļ��ᣬ�밴�մ˽̳̽��в�����<a href=http://www.phpwind.net/read.php?tid=753581 target=_blank>http://www.phpwind.net/read.php?tid=753581</a>�����в�����Ϊ�˱�֤�ʻ�����Ϣ��ȫ����¼APPƽ̨ʱ����Ҫ����д�Լ��ĳ��õ������䣬�����佫��Ϊ����޸Ĵ��ʻ���Ϣ������Ҫƾ֤��<br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;���ѿ����ġ����ӽ���>�Զ����ء�����ʱ��¼APPƽ̨�޸����ã�Ҳ���Գ����ֶ����ط�ʽ��ͨ����ȷ����ɸѡ����վ������������ٽ������ء��������أ���������������վ����ԭ�����ӵ����ӽ������ģ����������ӱ�����վ�����غ������ж��ᱣ��ԭ����ַ��ͨ�����ַ�ʽ�����������վ������Ȩ�أ��������ƹ㡣<br />
���ӽ�����ϸ�̳�����ʣ�<a href=http://www.phpwind.net/read-htm-tid-746186.html target=_blank>http://www.phpwind.net/read-htm-tid-746186.html</a><br />
����APPƽ̨������Ӧ��Ҳ�ܰﵽ�������������<a href=http://www.phpwind.net/read-htm-tid-717216.html target=_blank>http://www.phpwind.net/read-htm-tid-717216.html</a>�������κ���������ѯapp@phpwind.com��",
	'app_content2'		=> "�𾴵�վ����<br />APP ���ӽ�����һ���ʵվ�����ݣ��ƹ��Լ�վ���Ӧ�á�<br />
#��ʵ����
ͨ��phpwind APP���ӽ�����Ϊ phpwind վ����վ���ṩһ��ƽ̨��ʵ��վ����վ��֮���������Դ���������ϴ����ӵ�ƽ̨����ƽ̨�������������Ӧ��������Ҫ�����������������趨����ѡ�������ݷ��࣬�������ֶ����غ��Զ��������ַ�ʽ��<br />
#�ƹ�վ��
ÿһ����������ʱ������֤����ԭ����ַ�����ӣ��������޸ġ����ӱ���¼��Խ�ࡢ��¼���ӵ�վ���PRֵȨ��Խ�ߣ���վ�������Ȩ��Ҳ��Խ�ߣ��Ӷ�������վ�������������¼����PRֵ��<br />
�������뿪ͨ��ѿռ�һ����װ��̳��ʱ��ϵͳ���������������ӽ����������������ӡ���Ҳ����ѡ��������������֮�����ʱ����APPƽ̨>�������>���ӽ��������޸����á�<br />
��ϸ���� <a href=http://www.phpwind.net/read-htm-tid-700917.html target=_blank>http://www.phpwind.net/read-htm-tid-700917.html</a><br />
��̳��ѯ <a href=http://www.phpwind.net/thread-htm-fid-100.html target=_blank>http://www.phpwind.net/thread-htm-fid-100.html</a>l<br />
�鿴APP����Ӧ�� <a href=http://www.phpwind.net/read-htm-tid-717216.html target=_blank>http://www.phpwind.net/read-htm-tid-717216.html</a>",

	//7.3 End
############################## SQL LANG ##############################
	'act_1'				=> '���˸��Уϣӣŵ����㡢�㡢��û�ҿ�..',
	'act_2'				=> '���˾���˵����������ѽ!ѽ!ѽ!',
	'act_3'				=> '�����Ц������֮��,��Ȼû���ҵĶ���...',
	'act_4'				=> '����һ��,��ʼ��ર�વ�������',
	'act_5'				=> '����ɤ�ӳ��������������,̫����',
	'action_1'			=> '�ȿ�',
	'action_2'			=> '����',
	'action_3'			=> '��Ц',
	'action_4'			=> 'ʹ��',
	'action_5'			=> '����',
	'anonymousname'		=> '����',

	'colony'			=> '����Ȧ',
	'credit_descrip'	=> '�Զ������',
	'credit_name'		=> '������',
	'credit_unit'		=> '��',

	'db_adminreason'	=> '�����\n�����ˮ\n�Ƿ�����\n���治��\n�ظ�����\n\n��������\nԭ������',
	'db_admingradereason'=> '�������£�֧�֣�\n�����Ǹ���',
	'db_charset'		=> 'gbk',
	'db_currencyname'	=> '��Ԫ',
	'db_currencyunit'	=> '��',
	'db_creditname'		=> '����ֵ',
	'db_creditunit'		=> '��',
	'db_moneyname'		=> 'ͭ��',
	'db_moneyunit'		=> 'ö',
	'db_rvrcname'		=> '����',
	'db_rvrcunit'		=> '��',
	'db_whybbsclose'	=> '��վ�����У����Ժ����',
	'db_visitmsg'		=> '��վ�����ڲ�����',
	'db_floorunit'		=> '¥',
	'db_floorname_1'	=> '¥��',
	'db_floorname_2'	=> 'ɳ��',
	'db_floorname_3'	=> '���',
	'db_floorname_4'	=> '�ذ�',
	'db_sitemsg_1'		=> '����ɫ*�Ķ��Ǳ�����Ŀ������д��ȫ���޷�ע��',
	'db_sitemsg_2'		=> '������������շ��ʼ��ĵ�������',
	'db_sitemsg_3'		=> '����������ɻ��߷Ǹ��˵��ԣ���������Cookie��Ч��Ϊ ��ʱ���Ա�֤�˻���ȫ',
	'db_sitemsg_4'		=> '�������д��ƪ�����ֲ����Ϸ��������Ϊ�ݸ�',
	'db_sitemsg_5'		=> '������ύ��һ��ʧ���ˣ������á��ָ����ݡ����ָ���������',
	'db_sitemsg_6'		=> '�����ϴ���Ҫ��ѡ���ļ�����ѡ���ϴ�',

	'debateclass1'		=> '��ѧ����',
	'debateclass2'		=> 'Ӱ������',
	'debateclass3'		=> '����ʱ��',
	'debateclass4'		=> '��������',
	'debateclass5'		=> '��������',
	'debateclass6'		=> '����̬',
	'debateclass7'		=> '�������',
	'default_atc'		=> 'Ĭ�Ϸ���',
	'default_forum'		=> 'Ĭ�ϰ��',
	'default_recycle'	=> '����վ',

	'help_1'			=> '��������',
	'help_2'			=> 'ע�ᡢ��¼',
	'help_3'			=> '��������',
	'help_4'			=> '��Ӻͱ༭��������',
	'help_5'			=> 'ѡ����',
	'help_6'			=> 'ѡ��Ĭ�ϱ༭��',
	'help_7'			=> '�����ʼ�',
	'help_8'			=> '��������',
	'help_9'			=> '��������',
	'help_10'			=> '���������',
	'help_11'			=> '������������������ͶƱ��',
	'help_12'			=> '����������',
	'help_13'			=> '����ظ�',
	'help_14'			=> '���ù���',
	'help_15'			=> '�����ϴ�',
	'help_16'			=> '��������',
	'help_17'			=> '�ҵ�����',
	'help_18'			=> '���ù���',
	'help_19'			=> '�ղع���',
	'help_20'			=> '����Ȧ����',
	'help_21'			=> '����Ϣ����',
	'help_22'			=> '��������',
	'help_23'			=> '���Ӿٱ�����',
	'help_24'			=> '�������',
	'help_25'			=> '����ʹ��',
	'help_26'			=> 'Rss�ۺ�',
	'help_27'			=>'Wind Code',
	'helpd_2'			=> 'ע�᷽�����������û��ע�ᣬ�����ο�״̬�����̳�ģ���ͷ�����������Կ���������δ��¼&#160;ע�ᡱ�������������ע�ᡱ����д��Ӧ����Ϣ���Ϳ������ע���ˡ�\n��վ�����õĲ�ͬ���ο͵������ʹ����̳��Ȩ�޻��ܵ��ܶ����ƣ������ϲ�������̳������������ע�ᡣ\n��¼������������Ѿ�ע���˸���̳����������վ��ҳͷ���ĵ�¼ģ����е�¼��Ҳ������ҳ��ͷ���������������¼���������¼ҳ����е�¼���������οͷ��ʵ�ҳ�棬Ҳ���е�¼��ʾҳ����֡�',
	'helpd_3'			=> '������������룬���ڵ�¼ҳ�������һ����롱�������û�����ϵͳ���Զ��������뵽������Ч���������С�',
	'helpd_4'			=> '������롰������塱�µġ��༭�������ϡ����Ϳ��Զ��Լ���������Ϣ�����޸��ˡ�',
	'helpd_5'			=> '������롰������塱�µġ��༭�������ϡ����ҵ�����̳�ɿ��������ݡ�һ�����ڸ���Ŀ���С�ѡ���񡱵�ѡ��������б���ѡ��ϲ���ķ�񣬵�����ύ����ť���Ϳ����ˡ�',
	'helpd_6'			=> '������롰������塱�µġ��༭�������ϡ����ҵ�����̳�ɿ��������ݡ�һ�����ڸ���Ŀ���С�ѡ��Ĭ��ʹ�õı༭����ѡ�ѡ��ϰ��ʹ�õı༭����������ύ����',
	'helpd_7'			=> '������롰������塱�µġ��༭�������ϡ����ڡ���̳�ɿ��������ݡ�һ�����±ߣ��ҵ����Ƿ�����ʼ�����ѡ�񡰽����ʼ�����������ύ����',
	'helpd_9'			=> '�������б�ҳ��������Ķ�ҳ�棬���Կ�����������ͼ�꣬������ɽ�������������ҳ�棬���û�з���Ȩ�ޣ�������ʾ������ֻ̳���ض��û�����ܷ�������,�뵽������鷢��,����ߵȼ�!�����֡�\n����ò���������ȫ�ķ������ܣ�Ҳ�����������б�ҳ��ײ��Ŀ��ٷ���ģ����з���������',
	'helpd_10'			=> '�������б�ҳ��������Ķ�ҳ�棬�����������ͼ���������������ҳ�棬����ʱ�����ӱ༭���·��ҵ������۴���������ǰ��ĸ�ѡ����ѡ�������ѡ��ʻ�ɫ��˵���ð�鲻��������������������Ȩ�޲���������д�û�Ա������Ҫ֧����ͭ��������ע�ⲻ�ܳ���֧�����ֵ����\nͬ����Ҳ�����������б�ҳ��ײ��Ŀ��ٷ���ģ����з�������ʱ���ö�����Ҫ֧����ͭ��������',
	'helpd_11'			=> '������������������ͶƱ��','','�������б�ҳ��������Ķ�ҳ�棬�����ͣ������ͼ����ʱ��������ڸð���з�����������������ͶƱ����Ȩ��ʱ���ͻ����һ�������˵����˵�������ʾ����Ʒ�����͡�ͶƱ�������Ҫ���������ͼ��ɽ�����Ӧ�����ⷢ��ҳ�淢���µ����⡣',
	'helpd_12'			=> '�������б�ҳ��������Ķ�ҳ�棬�����������ͼ����뷢��ҳ�棬�ڷ���ʱ��ѡ���ݱ༭���������������ѡ�򣬻����ڿ��ٷ�������ѡ�������ѡ��ʻ�ɫ��˵���ð�鲻��������������������Ȩ�޲�������',
	'helpd_13'			=> '�������Ķ�ҳ�������ظ�����ť����ظ�ҳ��ظ���������Ҳ������ҳ���·��Ŀ��ٷ��������лظ���',
	'helpd_14'			=> '����Ҫ���õ�����¥���ϵ�����ã��������õ�ǰ¥�����ݣ�Ҳ������Wind Code����������ã�����Ҫ���õ����ݷ���[quote] ��Ҫ���õ�����[/quote]�м伴�ɡ�',
	'helpd_15'			=> '�ڷ���ҳ���µĸ����ϴ�����������ť���ϴ���Ч��׺���͵ĸ�����ͬʱ������������Ը����������������������ظ�������Ҫ������ֵ��',
	'helpd_16'			=> '��һ��������ʱ������Ҫ�ڵ�������򡱰�ť�����ͭ�������ڹ�������������������ͻ�۵���Ӧ��ͭ������ͬʱ����ɹ��������Ķ����������ݡ�',
	'helpd_17'			=> '�ڡ�������塱�ġ��ҵ����⡱�£����Բ鿴�ҵ����⣬�ҵĻظ����ҵľ������ҵ�ͶƱ���ҵĽ��׵�',
	'helpd_19'			=> '�������Ķ�ҳ�棬���Կ�������ӡ | ��ΪIE�ղ� | �ղ����� | ��һ���� | ��һ���⡱��������ղ����⡱�󣬿����ڡ�������塱�µġ��ղؼС��￴���ղص����⣬ͬʱ���Զ��ղص�������з������',
	'helpd_20'			=> '����Ȧ�����Բ����ʽ���ڣ�����ͨ������������Ѵ�������Ȧ��ʵ�ֺ�ͬһ����Ȧ��Ա������ܵĻ�����',
	'helpd_21'			=> '����ͨ����Աͷ����Ϣ����������¥�����Ϣ��ťʵ�ֻ�Ա֮�以������Ϣ��',
	'helpd_22'			=> '����������ġ����������ӽ�������ҳ�棬������ҳ���£�����ͨ���ؼ��ʻ��û������������⡢�ظ��Լ��������������������򼶱�ͬ�͸���̳���ò�ͬ������Ȩ�޿��ܻ��ܵ����ơ�',
	'helpd_23'			=> 'Э��վ���������Ӽ�ء��ٱ����������Ƽ��������ӵĹ��ܡ�������¥�������������ٱ�����д���ɲ��ύ����ʵ���˶Ե�ǰ¥�����Ӿٱ��Ĳ�����',
	'helpd_24'			=> '�Ƕ���̳���ܵ�һ����Ҫ���䡣���ͨ���ѳ����ļ��ϴ���hackĿ¼�£��ں�̨����Ӳ���������а�װ�������ڡ���������ж���Ӧ�Ĳ�����б༭�����ж�ء������װ�ú󲢿�����ͨ������������ʾ��ֱ����ʾ���ò��������ʾ�ڡ��������񡱵������˵��л���ֱ����ʾ�ڵ������ϡ�',
	'helpd_25'			=> '�Ի�Աʹ�õĵ����ڻ�Աͷ����Ϣ����ͷ��ť��ѡ�񣬶�����ʹ�õĵ��������Ӳ������ġ�ʹ�õ��ߡ���ѡ�񡣴򿪵����б����Կ����Լ����еĵ����������������Ϊ����Ҫ�������ʹ�á�',
	'helpd_26'			=> '�����ṩ�˼������õ�Rss���ġ���ҳ�ġ�Rss���ı������������ӡ�������ҳ���µġ�Rss���ı������������ӡ��Ͱ��ҳ���µġ�Rss���ı������������ӡ���',
	'helpd_27'			=> '<table><tr class="tr3 tr"><td><font color="#5a6633" face="verdana">[quote]</font>�����õ����ݣ���Ҫ�������ڷ���ʱ���ò��ظ�����¥�������<font color="#5a6633" face="verdana">[/quote]</font></td><td><table cellpadding="5" cellspacing="1" width="94%" bgcolor="#000000" align="center"><tr><td class="f_one">�����õ����Ӻ����Ļظ�����</td></tr></table></td></tr><tr class="tr3 tr"><td><font color="#5a6633" face="verdana">[code]</font><font color="#5a6633"></font><font face="courier" color="#333333"><br />echo "phpwind ��ӭ��!"\r</font><font color="#5a6633" face="verdana">[/code]</font></td><th><div class="tpc_content" id="read_553959"><h6 class="quote">Copy code</h6><blockquote id="code1">echo "phpwind ��ӭ��!"</blockquote></div></th></tr><tr class="tr3 tr"><td><font face="verdana" color="5a6633">[url]</font><font face="verdana">http://www.phpwind.net</font><font color="5a6633">[/url] </font></td><td><a href="http://www.phpwind.net" target="_blank"><font color="#000066">http://www.phpwind.net</font></a></td></tr><tr class="tr3 tr"><td><font face="verdana" color="5a6633">[url=http://www.phpwind.net]</font><font face="verdana">phpwind</font><font color="5a6633">[/url]</font></td><td><a href="http://www.phpwind.net"><font color="000066">PHPwind</font></a></font></td></tr><tr class="tr3 tr"><td><font face="verdana" color="5a6633">[email]</font><font face="verdana">fengyu@163.com</font><font color="5a6633">[/email]</font></td><td><a href="mailto:fengyu@163.com"><font color="000066">fengyu@163.com</font></a></td></tr><tr class="tr3 tr"><td><font face="verdana" color="5a6633">[email=fengyu@163.com]</font><font face="verdana">email me</font><font color="5a6633">[/email]</font></td><td><a href="mailto:fengyu@163.com"><font color="000066">email me</font></a></td></tr><tr class="tr3 tr"> <td><font face="verdana" color="5a6633">[b]</font><font face="verdana">������</font><font color="5a6633" face="verdana">[/b]</font> </td><td><font face="verdana"><b>������</b></font> </td></tr><tr class="tr3 tr"><td><font face="verdana" color="5a6633">[i]</font><font face="verdana">б����<font color="5a6633">[/i]</font> </font></td><td><font face="verdana"><i>б����</i></font> </td></tr><tr class="tr3 tr"><td><font face="verdana" color="5a6633">[u]</font><font face="verdana">�»���</font><font color="5a6633">[/u]</font></td><td><font face="verdana"><u>�»���</u></font> </td></tr><tr class="tr3 tr"> <td><font face=verdana color=5a6633>[align=center(����������left������right)]</font>λ���м�<font color="5a6633">[/align]</font></td><td><font face="verdana"><div align="center">�м����</div></font></td></tr><tr class="tr3 tr"> <td><font face="verdana" color="5a6633">[size=4]</font><font face="verdana">�ı������С<font color="5a6633">[/size] </font> </font></td><td><font face="verdana">�ı������С </font></td></tr><tr class="tr3 tr"> <td><font face="verdana" color="5a6633">[font=</font><font color="5a6633">����_gb2312<font face="verdana">]</font></font><font face="verdana">�ı�����<font color="5a6633">[/font] </font> </font></td><td><font face="verdana"><font face=����_gb2312>�ı�����</font> </font></td></tr><tr class="tr3 tr"> <td><font face="verdana" color="5a6633">[color=red]</font><font face="verdana">�ı���ɫ<font color="5a6633">[/color] </font> </font></td><td><font face="verdana" color="red">�ı���ɫ</font><font face="verdana"> </font></td></tr><tr class="tr3 tr"> <td><font face="verdana" color="5a6633">[img]</font><font face="verdana">http://www.phpwind.net/logo.gif<font color="5a6633">[/img]</font> </font></td><td><img src="logo.gif" /></font> </td></tr><tr class="tr3 tr"> <td><font face=���� color="#333333"><font color="#5a6633">[fly]</font>����������Ч<font color="#5a6633">[/fly]</font> </font></td><td><font face=����&nbsp; &nbsp; color="#333333"><marquee scrollamount="3" behavior="alternate" width="90%">����������Ч</marquee></font></td></tr><tr class="tr3 tr"> <td><font face=���� color="#333333"><font color="#5a6633">[move]</font>����������Ч<font color="#5a6633">[/move]</font> </font></td><td><font face=���� color="#333333"> <marquee scrollamount="3" width="90%">����������Ч</marquee></font></td></tr><tr class="tr3 tr"><td><font face=���� color="#333333"><font color="#5a6633">[flash=400,300]</font>http://www.phpwind.net/wind.swf<font color="#5a6633">[/flash]</font> </font></td><td><font face=���� color="#333333">����ʾflash�ļ�</font> </td></tr><tr class="tr3 tr"><td><font face=���� color="#333333"><font color="#5a6633">[iframe]</font>http://www.phpwind.net<font color="#5a6633">[/iframe]</font> </font></td><td><font face=���� color="#333333">����������ճ����ҳ(��̨Ĭ�Ϲر�)</font> </td></tr><tr class="tr3 tr"><td><font color=#5a6633>[glow=255(���),red(��ɫ),1(�߽�)]</font>Ҫ��������Ч��������<font color="#5a6633">[/glow]</font></td><td align="center"><font face=���� color="#333333"><table width="255" style="filter:glow(color=red, direction=1)"><tr><td align="center">Ҫ������ɫ����Ч��������</td></tr></table></font></td></tr></table>',
	'home_1'			=> '��������',
	'home_2'			=> '���ű�ǩ',
	'home_3'			=> '�Ƽ�����',
	'home_4'			=> '��������',
	'home_5'			=> 'վ����Ϣ',
	'home_6'			=> '�������',
	'home_7'			=> '���»ظ�',
	'home_8'			=> '��������',
	'home_9'			=> '��Ա����',

	'level_1'			=> '�ο�',
	'level_3'			=> '����Ա',
	'level_4'			=> '�ܰ���',
	'level_5'			=> '��̳����',
	'level_6'			=> '��ֹ����',
	'level_7'			=> 'δ��֤��Ա',
	'level_8'			=> '������·',
	'level_9'			=> '����',
	'level_10'			=> '��ʿ',
	'level_11'			=> 'ʥ��ʿ',
	'level_12'			=> '������',
	'level_13'			=> '����ʹ��',
	'level_14'			=> '����ʹ��',
	'level_15'			=> '��ʹ',
	'level_16'			=> '������Ա',

	'medaldesc_1'		=> 'лл��Ϊ������չ�����Ĳ���ĥ��Ĺ���!',
	'medaldesc_2'		=> '���͵�Ϊ��̳�����Ͷ����ջ���֣���л��!',
	'medaldesc_3'		=> 'лл��Ϊ������������,�ذ䷢�˽�!',
	'medaldesc_4'		=> '��Ϊ��̳���������⹱��,лл��!',
	'medaldesc_5'		=> 'Ϊ������������ԵĽ��鱻����,�ذ䷢�˽�!',
	'medaldesc_6'		=> 'лл����������ԭ����Ʒ,�ذ䷢�˽�!',
	'medaldesc_7'		=> '��ͼ����,���ƴ�ʦ!',
	'medaldesc_8'		=> '�ܹ������ṩ���ʵ�����ˮ��Դ��,�ɵ��������!',
	'medaldesc_9'		=> '�����кܴ�Ľ������Եõ��������!',
	'medaldesc_10'		=> '�������ܸ����˴�������,лл��!',
	'medalname_1'		=> '����ɾͽ�',
	'medalname_2'		=> '�������',
	'medalname_3'		=> '������ʹ��',
	'medalname_4'		=> '���⹱�׽�',
	'medalname_5'		=> '����ӽ�',
	'medalname_6'		=> 'ԭ���ȷ潱',
	'medalname_7'		=> '��ͼ��ʦ��',
	'medalname_8'		=> '��ˮ��Ž�',
	'medalname_9'		=> '���˽�����',
	'medalname_10'		=> '��Ĭ��ʦ��',
	'money'				=> 'ͭ��',

	'ol_whycolse'		=> 'ϵͳû�п�������֧������!',

	'plan_1'			=> '��ʱ����Ⱥ����Ϣ',
	'plan_2'			=> '�Զ��������',
	'plan_3'			=> '�������ն���Ϣ',
	'plan_4'			=> '����������֪ͨ',
	'plan_5'			=> '�����Ŷӹ��ʷ���',
	'plan_6'			=> 'ѫ���Զ�����',
	'plan_7'			=> '����ͷ���Զ�����',

	'rg_banname'		=> '����,����Ա,admin,����',
	'rg_rgpermit'		=> '���������û�ʱ����ʾ���Ѿ�ͬ�����ر����¡� <br /><br />��ӭ�����뱾վ��μӽ��������ۣ���վ��Ϊ������̳��Ϊά�����Ϲ������������ȶ��������Ծ������������ <br /><br />һ���������ñ�վΣ�����Ұ�ȫ��й¶�������ܣ������ַ�������Ἧ��ĺ͹���ĺϷ�Ȩ�棬�������ñ�վ���������ƺʹ���������Ϣ��<br />�� ��һ��ɿ�����ܡ��ƻ��ܷ��ͷ��ɡ���������ʵʩ�ģ�<br />��������ɿ���߸�������Ȩ���Ʒ���������ƶȵģ�<br />��������ɿ�����ѹ��ҡ��ƻ�����ͳһ�ģ�<br />�����ģ�ɿ�������ޡ��������ӣ��ƻ������Ž�ģ�<br />�����壩�������������ʵ��ɢ��ҥ�ԣ������������ģ�<br />������������⽨���š����ࡢɫ�顢�Ĳ�����������ɱ���ֲ�����������ģ�<br />�����ߣ���Ȼ�������˻���������ʵ�̰����˵ģ����߽����������⹥���ģ�<br />�����ˣ��𺦹��һ��������ģ�<br />�����ţ�����Υ���ܷ��ͷ�����������ģ�<br />����ʮ��������ҵ�����Ϊ�ġ�<br /><br />�����������أ����Լ������ۺ���Ϊ����<br />������ֹ�������û�ʱʹ����ر�վ�Ĵʻ㣬���Ǵ������衢�ٰ�����ҥ��Ļ������京��ĸ������Խ���ע���û����������ǻὫ��ɾ����<br />�ġ���ֹ���κη�ʽ�Ա�վ���и����ƻ���Ϊ��<br />�塢�������Υ��������ط��ɷ������Ϊ����վ�Ų��������ĵ�¼��̳��Ϣ������¼���ɣ���Ҫʱ�����ǻ�����صĹ��ҹ������ṩ������Ϣ�� ',
	'rg_welcomemsg'		=> '��л����ע�ᣬ��ӭ���ĵ�����ϣ�������ܸ����������֣���෢�԰ɣ���վȫ�������Ա�����ʺ�<br />����ע����Ϊ:$rg_name',
	'rg_whyregclose'	=> '����Ա�ر���ע��!',
	'rvrc'				=> '����',

	'sharelinks'		=> 'PHPwind�ٷ���̳',
	'smile'				=> 'Ĭ�ϱ���',

	'tool_1'			=> '��������',
	'tool_1_inro'		=> '�ɽ��Լ�����������',
	'tool_2'			=> '���㿨',
	'tool_2_inro'		=> '�ɽ��Լ����и�������,����ͭ��,����,����ֵ,����',
	'tool_3'			=> '��Ŀ��',
	'tool_3_inro'		=> '���Խ��Լ������ӱ��������ʾ',
	'tool_4'			=> '�ö�I',
	'tool_4_inro'		=> '�ɽ��Լ�����������ڰ�����ö����ö�ʱ��Ϊ6Сʱ',
	'tool_5'			=> '�ö�II',
	'tool_5_inro'		=> '�ɽ��Լ�����������ڷ������ö����ö�ʱ��Ϊ6Сʱ',
	'tool_6'			=> '�ö�III',
	'tool_6_inro'		=> '�ɽ��Լ������������������̳���ö����ö�ʱ��Ϊ6Сʱ',
	'tool_7'			=> '��ǰ����',
	'tool_7_inro'		=> '���԰��Լ������������ǰ���������ڰ��ĵ�һҳ',
	'tool_8'			=> '������',
	'tool_8_inro'		=> '�ɸ����Լ�����̳���û���',
	'tool_9'			=> '����I',
	'tool_9_inro'		=> '���Խ��Լ������Ӽ�Ϊ����I',
	'tool_10'			=> '����II',
	'tool_10_inro'		=> '���Խ��Լ������Ӽ�Ϊ����II',
	'tool_11'			=> '��������',
	'tool_11_inro'		=> '���Խ��Լ��������������������������Ա�ظ�����',
	'tool_12'			=> '�������',
	'tool_12_inro'		=> '���Խ���Լ���������������������Ա���Իظ�����',
	'tool_13'			=> '�ʻ�',
	'tool_13_inro'		=> '���Ը����������Ƽ���',
	'tool_14'			=> '����',
	'tool_14_inro'		=> '���Ը����Ӽ����Ƽ���',
	'tool_15'			=> '������',
	'tool_15_inro'		=> 'ʹ�ú�����Ӽ����ױ�(-100,100)',
	'tool_16'			=> '���տ�',
	'tool_16_inro'		=> '�Զ���Ϣ��ʽ���͸����ѣ�ף�������տ���',
	'tool_17'			=> '����',
	'tool_17_inro'		=> '������ʹ�ã�ÿ��һ�������Ӷ���12Сʱǰ',
	'tool_18'			=> '��ͷ��',
	'tool_18_inro'		=> 'ʹ�ú��öԷ�ͷ���Ϊ��ͷ����Ч������24Сʱ�򵽶Է�ʹ����ϴ��Ϊֹ',
	'tool_19'			=> '��ԭ��',
	'tool_19_inro'		=> '�����ͷ����Ч��',
	'tool_20'			=> '͸�Ӿ�',
	'tool_20_inro'		=> '���û�ʹ�� �鿴�û�IP',
	'tool_21'			=> '�����',
	'tool_21_inro'		=> 'ʹ�ú󣬲��ܶԸ��û�ʵ����ͷ��Ч����48Сʱ����Ч',
	'tool_22'			=> 'ʱ�տ�',
	'tool_22_inro'		=> '������ʹ�ã������ӷ�����12Сʱ��',

	'admin_login'		=> '�����ô�ʼ�˵�¼�ٽ�����������',

	'block_1'			=> '��������',
	'block_2'			=> '���»ظ�',
	'block_3'			=> '��������',
	'block_4'			=> '�ظ�����',
	'block_5'			=> '��������',
	'block_6'			=> '��Ǯ����',
	'block_7'			=> '��������',
	'block_8'			=> '���շ���',
	'block_9'			=> '����������',
	'block_10'			=> '����������',
	'block_11'			=> '���ű�ǩ',
	'block_12'			=> '���±�ǩ',
	'block_13'			=> '����ͼƬ',
	'block_14'			=> '����ͼƬ(Ƶ��)',
	'block_15'			=> '���Ż(��վ)',
	'block_16'			=> '���½���(��վ)',
	'block_17'			=> '���Ž���(��վ)',
	'block_18'			=> '����ʱ������',
	'block_19'			=> '���շ�������',
	'block_20'			=> '�·�������',
	'block_21'			=> '��������',
	'block_22'			=> '����������',
	'block_23'			=> '��������(��վ)',
	'block_24'			=> '���Ż(Ƶ��)',
	'block_25'			=> '����ֵ����',
	'block_26'			=> '���ױ����а�',
	'block_27'			=> '��ҳ����',
	'block_28'			=> '��������(Ƶ��)',
	'block_29'			=> '��������(��վ)',
	'block_30'			=> '��������(Ƶ��)',
	'block_31'			=> '��������(Ƶ��)',
	'block_32'			=> '��������(���)',
	'block_33'			=> '��������(���)',
	'block_34'			=> '���»ظ�(���)',
	'block_35'			=> 'Ƶ��ҳ����',
	'block_36'			=> '�������(Ƶ��)',
	'block_37'			=> '��������(Ƶ��)',
	'block_38'			=> '���ջ(��վ)',
	'block_39'			=> '���ջ(Ƶ��)',
	'block_40'			=> '����ͼƬ(���)',
	'block_41'			=> '���»(��վ)',
	'block_42'			=> '���»(Ƶ��)',
	'block_43'			=> '���½���(Ƶ��)',
	'block_44'			=> '���Ž���(Ƶ��)',
	'block_45'			=> '���ս���(��վ)',
	'block_46'			=> '���ս���(Ƶ��)',
	'block_47'			=> '��������(��վ)',
	'block_48'			=> '��������(Ƶ��)',
	'stamp_1'			=> '��������',
	'stamp_2'			=> '�û�����',
	'stamp_3'			=> '�������',
	'stamp_4'			=> '��ǩ����',
	'stamp_5'			=> 'ͼƬ',
	'stamp_6'			=> '�',
	'stamp_7'			=> '����',
	'stamp_8'			=> '����',
	'fourmtype_2'		=> '����',
	'fourmtype_5'		=> 'Ƶ��ҳ����',
	'mode_area_name'	=> '�Ż�ģʽ',
	'mode_pw_name'		=> '��̳ģʽ',
	'mode_o_name'		=> '��������',
	'mode_area_header'	=> '�Ż�',
	'mode_pw_header'	=> '��̳',
	'mode_o_header'		=> '��������',
	'mode_pgcon_index'	=> '��ҳ',
	'mode_pgcon_cate'	=> 'Ƶ��ҳ',
	'mode_pgcon_thread'	=> '�б�ҳ',
	'mode_pgcon_m_home'	=> '��̬',

	//7.3.2
	'diary_o_uploadsize'=> 'a:5:{s:3:"jpg";i:300;s:4:"jpeg";i:300;s:3:"png";i:400;s:3:"gif";i:400;s:3:"bmp";i:400;}',

	'config_noexists'	=> '���ݿ������ļ�������,��������д������Ϣ',
	'install_initdata'	=> "���ڳ�ʼ������,���Ժ�...$GLOBALS[stepstring]",
	'undefined_action'	=> '�Ƿ�����,�뷵��',
	'action_success'	=> '�˲��������,�������һ������',
	'promptmsg_database'=> '������װ',

	'Site.Header'		=> 'ͷ�����~	~��ʾ��ҳ���ͷ����һ����ͼƬ��flash��ʽ��ʾ���������ʱϵͳ�����ѡȡһ����ʾ',
	'Site.Footer'		=> '�ײ����~	~��ʾ��ҳ��ĵײ���һ����ͼƬ��flash��ʽ��ʾ���������ʱϵͳ�����ѡȡһ����ʾ',
	'Site.NavBanner1'	=> '����ͨ��~	~��ʾ�������������棬һ����ͼƬ��flash��ʽ��ʾ���������ʱϵͳ�����ѡȡһ����ʾ',
	'Site.PopupNotice'	=> '�������[����]~	~��ҳ�����½��Ը����Ĳ㵯����ʾ���˹��������Ҫ����������ش��ڲ���',
	'Site.FloatRand'	=> 'Ư�����[���]~	~�Ը�����ʽ��ҳ�������Ư���Ĺ��',
	'Site.FloatLeft'	=> 'Ư�����[��]~	~�Ը�����ʽ��ҳ�����Ư���Ĺ�棬�׳ƶ������[��]',
	'Site.FloatRight'	=> 'Ư�����[��]~	~�Ը�����ʽ��ҳ���ұ�Ư���Ĺ�棬�׳ƶ������[��]',
	'Mode.TextIndex'	=> '���ֹ��[��ҳ]~	~��ʾ��ҳ��ĵ������棬һ�������ַ�ʽ��ʾ��ÿ��������棬����������������ʾ',
	'Mode.Forum.TextRead'	=> '���ֹ��[����ҳ]~	~��ʾ��ҳ��ĵ������棬һ�������ַ�ʽ��ʾ��ÿ��������棬����������������ʾ',
	'Mode.Forum.TextThread'	=> '���ֹ��[����ҳ]~	~��ʾ��ҳ��ĵ������棬һ�������ַ�ʽ��ʾ��ÿ��������棬����������������ʾ',
	'Mode.Forum.Layer.TidRight'	=> '¥����[�����Ҳ�]~	~�����������Ҳ࣬һ����ͼƬ��������ʾ������������ʱϵͳ�����ѡȡһ����ʾ',
	'Mode.Forum.Layer.TidDown'	=> '¥����[�����·�]~	~�����������·���һ����ͼƬ��������ʾ������������ʱϵͳ�����ѡȡһ����ʾ',
	'Mode.Forum.Layer.TidUp'		=> '¥����[�����Ϸ�]~	~�����������Ϸ���һ����ͼƬ��������ʾ������������ʱϵͳ�����ѡȡһ����ʾ',
	'Mode.Forum.Layer.TidAmong'	=> '¥����[¥���м�]~	~����������¥��֮�䣬һ����ͼƬ��������ʾ������������ʱϵͳ�����ѡȡһ����ʾ',
	'Mode.Layer.Index'		=> '��ҳ�������~	~��������ҳ�����֮�䣬һ����ͼƬ��������ʾ������������ʱϵͳ�����ѡȡһ����ʾ',

	'nav_home_main' => '��ҳ',
	'nav_bbs_main' => '��̳',
	'nav_o_main' => '��������',
	'nav_sort_bbs' => 'ͳ������',
	'nav_app_bbs' => 'APPӦ��',
	'nav_hack_bbs' => '��������',
	'nav_member_bbs' => '��Ա�б�',
	'nav_lastpost_bbs' => '��������',
	'nav_digest_bbs' => '������',
	'nav_search_bbs' => '����',
	'nav_faq_bbs' => '����',
	'nav_sort_basic_bbs' => '������Ϣ',
	'nav_sort_ipstate_bbs' => '����IPͳ��',
	'nav_sort_team_bbs' => '�����Ŷ�',
	'nav_sort_admin_bbs' => '�������',
	'nav_sort_online_bbs' => '���߻�Ա',
	'nav_sort_member_bbs' => '��Ա����',
	'nav_sort_forum_bbs' => '�������',
	'nav_sort_article_bbs' => '��������',
	'nav_sort_taglist_bbs' => '��ǩ����',
	'nav_index_o' => '����������ҳ',
	'nav_home_o' => '�ҵ���ҳ',
	'nav_user_o' => '���˿ռ�',
	'nav_friend_o' => '����',
	'nav_browse_o' => '��㿴��',
);

$appinfo =array(
	'226'=>array(
		'cup' => '167',
		'cid' => '226',
		'name' => '���ʾ���',
		'ifshow' => '0',
	),

	'210'=>array(
		'cup' => '170',
		'cid' => '210',
		'name' => '��Ϸ��',
		'ifshow' => '0',
	),

	'209'=>array(
		'cup' => '170',
		'cid' => '209',
		'name' => '���|DV',
		'ifshow' => '0',
	),

	'208'=>array(
		'cup' => '170',
		'cid' => '208',
		'name' => 'PC|�ʼǱ�',
		'ifshow' => '0',
	),

	'207'=>array(
		'cup' => '170',
		'cid' => '207',
		'name' => '�ֻ�|PDA',
		'ifshow' => '0',
	),

	'206'=>array(
		'cup' => '170',
		'cid' => '206',
		'name' => '�ǿ���Դ',
		'ifshow' => '0',
	),

	'205'=>array(
		'cup' => '170',
		'cid' => '205',
		'name' => 'ҵ����Ѷ',
		'ifshow' => '0',
	),

	'204'=>array(
		'cup' => '171',
		'cid' => '204',
		'name' => '����װ|Ů��װ',
		'ifshow' => '0',
	),

	'203'=>array(
		'cup' => '171',
		'cid' => '203',
		'name' => '����ָ��',
		'ifshow' => '0',
	),

	'202'=>array(
		'cup' => '171',
		'cid' => '202',
		'name' => '���о���',
		'ifshow' => '0',
	),

	'201'=>array(
		'cup' => '171',
		'cid' => '201',
		'name' => '����|��ױ',
		'ifshow' => '0',
	),

	'200'=>array(
		'cup' => '171',
		'cid' => '200',
		'name' => '��Ʒ��ѡ',
		'ifshow' => '0',
	),

	'199'=>array(
		'cup' => '172',
		'cid' => '199',
		'name' => '��������',
		'ifshow' => '0',
	),

	'198'=>array(
		'cup' => '172',
		'cid' => '198',
		'name' => '��������',
		'ifshow' => '0',
	),

	'211'=>array(
		'cup' => '170',
		'cid' => '211',
		'name' => '�����ҵ�',
		'ifshow' => '0',
	),

	'212'=>array(
		'cup' => '169',
		'cid' => '212',
		'name' => '�ȵ��ע',
		'ifshow' => '0',
	),

	'225'=>array(
		'cup' => '167',
		'cid' => '225',
		'name' => '̨������',
		'ifshow' => '0',
	),

	'224'=>array(
		'cup' => '167',
		'cid' => '224',
		'name' => '�й�����',
		'ifshow' => '0',
	),

	'223'=>array(
		'cup' => '167',
		'cid' => '223',
		'name' => '�������',
		'ifshow' => '0',
	),

	'222'=>array(
		'cup' => '167',
		'cid' => '222',
		'name' => '������ʷ',
		'ifshow' => '0',
	),

	'221'=>array(
		'cup' => '167',
		'cid' => '221',
		'name' => '����̸��',
		'ifshow' => '0',
	),

	'220'=>array(
		'cup' => '168',
		'cid' => '220',
		'name' => '���ʹ��',
		'ifshow' => '0',
	),

	'219'=>array(
		'cup' => '168',
		'cid' => '219',
		'name' => ' ����|Ӱ��',
		'ifshow' => '0',
	),

	'218'=>array(
		'cup' => '168',
		'cid' => '218',
		'name' => '����|Ц̸',
		'ifshow' => '0',
	),

	'217'=>array(
		'cup' => '168',
		'cid' => '217',
		'name' => '����|ռ��',
		'ifshow' => '0',
	),

	'216'=>array(
		'cup' => '168',
		'cid' => '216',
		'name' => '��Ϸ|����',
		'ifshow' => '0',
	),

	'215'=>array(
		'cup' => '168',
		'cid' => '215',
		'name' => '����|����|����',
		'ifshow' => '0',
	),

	'214'=>array(
		'cup' => '169',
		'cid' => '214',
		'name' => '�Լ���Ѷ',
		'ifshow' => '0',
	),

	'213'=>array(
		'cup' => '169',
		'cid' => '213',
		'name' => '�ڱ���ȫ',
		'ifshow' => '0',
	),

	'197'=>array(
		'cup' => '172',
		'cid' => '197',
		'name' => '���б���',
		'ifshow' => '0',
	),

	'196'=>array(
		'cup' => '172',
		'cid' => '196',
		'name' => '�ݳ�|�糡',
		'ifshow' => '0',
	),

	'195'=>array(
		'cup' => '172',
		'cid' => '195',
		'name' => '�Ҿ�|װ��',
		'ifshow' => '0',
	),

	'184'=>array(
		'cup' => '174',
		'cid' => '184',
		'name' => '��վ����Щ��',
		'ifshow' => '0',
	),

	'177'=>array(
		'cup' => '176',
		'cid' => '177',
		'name' => 'ȫ���ƽ���',
		'ifshow' => '0',
	),

	'178'=>array(
		'cup' => '176',
		'cid' => '178',
		'name' => '�ɽ�|��ѧ��',
		'ifshow' => '0',
	),

	'179'=>array(
		'cup' => '176',
		'cid' => '179',
		'name' => '������ѵ',
		'ifshow' => '0',
	),

	'181'=>array(
		'cup' => '176',
		'cid' => '181',
		'name' => '������',
		'ifshow' => '0',
	),

	'182'=>array(
		'cup' => '175',
		'cid' => '182',
		'name' => '����',
		'ifshow' => '0',
	),

	'183'=>array(
		'cup' => '174',
		'cid' => '183',
		'name' => 'ITȦ',
		'ifshow' => '0',
	),

	'180'=>array(
		'cup' => '176',
		'cid' => '180',
		'name' => '����Ա',
		'ifshow' => '0',
	),

	'101'=>array(
		'cup' => '0',
		'cid' => '101',
		'name' => '���',
		'ifshow' => '0',
	),

	'166'=>array(
		'cup' => '101',
		'cid' => '166',
		'name' => '����ʱ��',
		'ifshow' => '0',
	),

	'186'=>array(
		'cup' => '101',
		'cid' => '186',
		'name' => '����ʱ��',
		'ifshow' => '0',
	),

	'189'=>array(
		'cup' => '101',
		'cid' => '189',
		'name' => '����|����',
		'ifshow' => '0',
	),

	'190'=>array(
		'cup' => '173',
		'cid' => '190',
		'name' => '����',
		'ifshow' => '0',
	),

	'191'=>array(
		'cup' => '173',
		'cid' => '191',
		'name' => '����',
		'ifshow' => '0',
	),

	'192'=>array(
		'cup' => '173',
		'cid' => '192',
		'name' => '����',
		'ifshow' => '0',
	),

	'193'=>array(
		'cup' => '172',
		'cid' => '193',
		'name' => '���|��ʳ',
		'ifshow' => '0',
	),

	'194'=>array(
		'cup' => '172',
		'cid' => '194',
		'name' => '����|ҽ��',
		'ifshow' => '0',
	),

	'187'=>array(
		'cup' => '101',
		'cid' => '187',
		'name' => '����|��Դ|ͨѶ',
		'ifshow' => '0',
	),

	'188'=>array(
		'cup' => '101',
		'cid' => '188',
		'name' => '����|����',
		'ifshow' => '0',
	),

	'167'=>array(
		'cup' => '0',
		'cid' => '167',
		'name' => '����',
		'ifshow' => '0',
	),

	'168'=>array(
		'cup' => '0',
		'cid' => '168',
		'name' => '����',
		'ifshow' => '0',
	),

	'169'=>array(
		'cup' => '0',
		'cid' => '169',
		'name' => '����',
		'ifshow' => '0',
	),

	'170'=>array(
		'cup' => '0',
		'cid' => '170',
		'name' => '����',
		'ifshow' => '0',
	),

	'171'=>array(
		'cup' => '0',
		'cid' => '171',
		'name' => 'ʱ��',
		'ifshow' => '0',
	),

	'172'=>array(
		'cup' => '0',
		'cid' => '172',
		'name' => '����',
		'ifshow' => '0',
	),

	'174'=>array(
		'cup' => '0',
		'cid' => '174',
		'name' => 'IT',
		'ifshow' => '0',
	),

	'176'=>array(
		'cup' => '0',
		'cid' => '176',
		'name' => '������ѵ',
		'ifshow' => '0',
	),

	'175'=>array(
		'cup' => '0',
		'cid' => '175',
		'name' => '��ѧ����',
		'ifshow' => '0',
	),

	'173'=>array(
		'cup' => '0',
		'cid' => '173',
		'name' => '����',
		'ifshow' => '0',
	),

);