<?php
!function_exists('readover') && exit('Forbidden');

$lang['right_title'] = array(
	'basic'		=> '����Ȩ��',
	'read'		=> '����Ȩ��',
	'att'		=> '����Ȩ��',
	'group'		=> 'Ⱥ��Ȩ��',
	'message'	=> '��ϢȨ��',
	'special'	=> '�û��鹺��',
	'system'	=> '����Ȩ��'
);
$lang['right'] = array (
	'message' => array(
		'allowmessege'	=> array(
			'title'	=> '������Ϣ',
			'desc'  => '�����󣬴��û�����û����Է�����Ϣ',
			'html'	=> '<ul class="list_A list_80 cc"><li><input type="radio" value="1" name="group[allowmessege]" $allowmessege_Y />����</li><li><input type="radio" value="0" name="group[allowmessege]" $allowmessege_N />�ر�</li></ul>'
		),
		'maxmsg'	=> array(
			'title'	=> '�ɴ洢�������Ϣ��Ŀ',
			'desc'	=> 'ֻͳ�ƶ���Ϣ�Ͷ��˶Ի�',
			'html'	=> '<input class="input input_wa" value="$maxmsg" name="group[maxmsg]" />'
		),
		'maxsendmsg'	=> array(
			'title'	=> 'ÿ���������Ϣ��Ŀ',
			'html'	=> '<input class="input input_wa" value="$maxsendmsg" name="group[maxsendmsg]" />'
		),
		'messagecontentsize' => array(
			'title'   => 'ÿ����Ϣ��������ֽ���',
			'html'    => '<input class="input input_wa" name="group[messagecontentsize]" value="$messagecontentsize" />'
		),
		'msggroup'	=> array(
			'title'	=> 'ֻ�����ض��û������Ϣ',
			'html'	=> '<ul class="list_A list_80 cc"><li><input type="radio" value="1" name="group[msggroup]" $msggroup_Y />����</li><li><input type="radio" value="0" name="group[msggroup]" $msggroup_N />�ر�</li></ul>'
		),
		'multiopen' => array(
			'title'	  => '���Ͷ�����Ϣ',
			'desc'	  => '�����󣬴��û�����û����Է��Ͷ�����Ϣ',
			'html'    => '<ul class="list_A list_80 cc"><li><input type="radio" value="1" name="group[multiopen]" $multiopen_Y />����</li><li><input type="radio" value="0" name="group[multiopen]" $multiopen_N />�ر�</li></ul>'
		)
	),
	'basic' => array(
		'allowvisit' => array(
			'title'	=> 'վ�����',
			'desc'	=> '�رպ��û������ܷ���վ����κ�ҳ��',
			'html'	=> '<ul class="list_A list_80 cc"><li><input type="radio" value="1" name="group[allowvisit]" $allowvisit_Y />����</li><li><input type="radio" value="0" name="group[allowvisit]" $allowvisit_N />�ر�</li></ul>'
		),
		'allowhide' => array(
			'title'	=> '�����¼',
			'desc'	=> '�������û����������¼վ��',
			'html'	=> '<ul class="list_A list_80 cc"><li><input type="radio" value="1" name="group[allowhide]" $allowhide_Y />����</li><li><input type="radio" value="0" name="group[allowhide]" $allowhide_N />�ر�</li></ul>'
		),
		'userbinding' => array(
			'title'	=> '���˺Ű�',
			'desc'	=> '�׳���װ�',
			'desc'	=> '�������û����Խ��ж��˺Ű�',
			'html'	=> '<ul class="list_A list_80 cc"><li><input type="radio" value="1" name="group[userbinding]" $userbinding_Y />����</li><li><input type="radio" value="0" name="group[userbinding]" $userbinding_N />�ر�</li></ul>'
		),
		'allowread'	=> array(
			'title'	=> '�������',
			'desc'	=> '�������û������������',
			'html'	=> '<ul class="list_A list_80 cc"><li><input type="radio" value="1" name="group[allowread]" $allowread_Y />����</li><li><input type="radio" value="0" name="group[allowread]" $allowread_N />�ر�</li></ul>'
		),
		'allowsearch'	=> array(
			'title'	=> '��������',
			'desc'	=> '�޸��û�������Ȩ��',
			'html'	=> '<ul class="list_A"><li><input type="radio" value="0" name="group[allowsearch]" $allowsearch_0 />������</li>
			<li><input type="radio" value="1" name="group[allowsearch]" $allowsearch_1 />���������������</li>
			<li><input type="radio" value="2" name="group[allowsearch]" $allowsearch_2 />��������������⡢����</li>
			<li><input type="radio" value="3" name="group[allowsearch]" $allowsearch_3 />��������ȫ������(�����ظ�����)</li></ul>'
		),
		'allowmember'	=> array(
			'title'	=> '�鿴��Ա�б�',
			'desc'	=> '�������û����Բ鿴��Ա�б�',
			'html'	=> '<ul class="list_A list_80 cc"><li><input type="radio" value="1" name="group[allowmember]" $allowmember_Y />����</li><li><input type="radio" value="0" name="group[allowmember]" $allowmember_N />�ر�</li></ul>'
		),
		'allowviewonlineread'	=> array(
			'title'	=> '�鿴���߻�Ա����ҳ��',
			'desc'	=> '������û����û��鿴���߻�Ա��ǰ���ڵ�ҳ��',
			'html'	=> '<ul class="list_A list_80 cc"><li><input type="radio" value="1" name="group[allowviewonlineread]" $allowviewonlineread_Y />����</li><li><input type="radio" value="0" name="group[allowviewonlineread]" $allowviewonlineread_N />�ر�</li></ul>'
		),
		/*
		'allowprofile'	=> array(
			'title'	=> '�鿴��Ա����',
			'html'	=> '<ul class="list_A list_80 cc"><li><input type="radio" value="1" name="group[allowprofile]" $allowprofile_Y />����</li><li><input type="radio" value="0" name="group[allowprofile]" $allowprofile_N />�ر�</li></ul>'
		),
		*/
		'atclog' => array(
			'title'	=> '�鿴���Ӳ�����¼',
			'desc'	=> '�����û��鿴�Լ����ӵı��������',
			'html'	=> '<ul class="list_A list_80 cc"><li><input type="radio" value="1" name="group[atclog]" $atclog_Y />����</li><li><input type="radio" value="0" name="group[atclog]" $atclog_N />�ر�</li></ul>'
		),
		//ȥ��չ������@modify panjl@2010-11-2
		/*
		'show' => array(
			'title'	=> 'ʹ��չ��',
			'html'	=> '<ul class="list_A list_80 cc"><li><input type="radio" value="1" name="group[show]" $show_Y />����</li><li><input type="radio" value="0" name="group[show]" $show_N />�ر�</li></ul>'
		),
		*/
		'allowreport' => array(
			'title'	=> 'ʹ�þٱ�',
			'desc'	=> '�����󣬴��û�����û�����ʹ�þٱ�����',
			'html'	=> '<ul class="list_A list_80 cc"><li><input type="radio" value="1" name="group[allowreport]" $allowreport_Y />����</li><li><input type="radio" value="0" name="group[allowreport]" $allowreport_N />�ر�</li></ul>'
		),
		'upload' => array(
			'title'	=> 'ͷ���ϴ�',
			'desc'	=> "������������ <a href=\"$admin_file?adminjob=member\" onclick=\"parent.PW.Dialog({id:'member',url:'$admin_file?adminjob=member',name:'��Ա���'});return false;\">ȫ��->��Ա���->ͷ������</a> �п���ͷ���ϴ����ܲ���Ч",
			'html'	=> '<ul class="list_A list_80 cc"><li><input type="radio" value="1" name="group[upload]" $upload_Y />����</li><li><input type="radio" value="0" name="group[upload]" $upload_N />�ر�</li></ul>'
		),
		'allowportait'	=> array(
			'title'	=> 'ͷ������',
			'desc'	=> '�����󣬴��û�����û��������ⲿ��վͼƬ���ӵ�ַ��Ϊ�Լ���ͷ��',
			'html'	=> '<ul class="list_A list_80 cc"><li><input type="radio" value="1" name="group[allowportait]" $allowportait_Y />����</li><li><input type="radio" value="0" name="group[allowportait]" $allowportait_N />�ر�</li></ul>'
		),
		'allowhonor'	=> array(
			'title'	=> '����ǩ��',
			'desc'	=> '�����󣬴��û�����û�����ʹ�ø���ǩ������',
			'html'	=> '<ul class="list_A list_80 cc"><li><input type="radio" value="1" name="group[allowhonor]" $allowhonor_Y />����</li><li><input type="radio" value="0" name="group[allowhonor]" $allowhonor_N />�ر�</li></ul>'
		),
		/*'allowmessege'	=> array(
			'title'	=> '������Ϣ',
			'html'	=> '<ul class="list_A list_80 cc"><li><input type="radio" value="1" name="group[allowmessege]" $allowmessege_Y />����</li><li><input type="radio" value="0" name="group[allowmessege]" $allowmessege_N />�ر�</li></ul>'
		),*/
		'allowsort'	=> array(
			'title'	=> '�鿴ͳ������',
			'desc'	=> '�����󣬴��û�����û����Բ鿴ͳ������',
			'html'	=> '<ul class="list_A list_80 cc"><li><input type="radio" value="1" name="group[allowsort]" $allowsort_Y />����</li><li><input type="radio" value="0" name="group[allowsort]" $allowsort_N />�ر�</li></ul>'
		),
		'alloworder'=> array(
			'title'	=> '��������',
			'html'	=> '<ul class="list_A list_80 cc"><li><input type="radio" value="1" name="group[alloworder]" $alloworder_Y />����</li><li><input type="radio" value="0" name="group[alloworder]" $alloworder_N />�ر�</li></ul>'
		),
		'viewipfrom'	=> array(
			'title'	=> '�鿴ip��Դ',
			'desc'	=> "�����̳ģʽ�½���������<a href=\"$admin_file?adminjob=interfacesettings&adminitem=read\">�����Ķ�ҳ����</a>�رմ˹��ܣ������������Ч",
			'html'	=> '<ul class="list_A list_80 cc"><li><input type="radio" value="1" name="group[viewipfrom]" $viewipfrom_Y />����</li><li><input type="radio" value="0" name="group[viewipfrom]" $viewipfrom_N />�ر�</li></ul>'
		),
		'searchtime'	=> array(
			'title'	=> '��������ʱ����[��]',
			'html'	=> '<input class="input input_wa" name="group[searchtime]" value="$searchtime" />'
		),
		/*'schtime' => array(
			'title'	=> '��������ʱ�䷶Χ',
			'html'	=> '<select name="group[schtime]" class="select_wa">
				<option value="all" $schtime_all>��������</option>
				<option value="86400" $schtime_86400>1���ڵ�����</option>
				<option value="172800" $schtime_172800>2���ڵ�����</option>
				<option value="604800" $schtime_604800>1�����ڵ�����</option>
				<option value="2592000" $schtime_2592000>1�����ڵ�����</option>
				<option value="5184000" $schtime_5184000>2�����ڵ�����</option>
				<option value="7776000" $schtime_7776000>3�����ڵ�����</option>
				<option value="15552000" $schtime_15552000>6�����ڵ�����</option>
				<option value="31536000" $schtime_31536000>1���ڵ�����</option>
			</select>'
		),*/
		'signnum' => array(
			'title'	=> '����ǩ������ֽ���',
			'desc'	=> 'Ϊ0������',
			'html'	=> '<input class="input input_wa" name="group[signnum]" value="$signnum" />'
		),
		'imgwidth' => array(
			'title'	=> 'ǩ���е�ͼƬ�����',
			'desc'	=> "����ʹ��<a href=\"$admin_file?adminjob=member\"  onclick=\"parent.PW.Dialog({id:'member',url:'$admin_file?adminjob=member',name:'��Ա���'});return false;\">ȫ��->��Ա���->ǩ������</a>�������",
			'html'	=> '<input class="input input_wa" name="group[imgwidth]" value="$imgwidth" />'
		),
		'imgheight' => array(
			'title'	=> 'ǩ���е�ͼƬ���߶�',
			'desc'	=> "����ʹ��<a href=\"$admin_file?adminjob=member\"  onclick=\"parent.PW.Dialog({id:'member',url:'$admin_file?adminjob=member',name:'��Ա���'});return false;\">ȫ��->��Ա���->ǩ������</a>�������",
			'html'	=> '<input class="input input_wa" name="group[imgheight]" value="$imgheight" />'
		),
		'fontsize'	=> array(
			'title'	=> 'ǩ����[size]��ǩ���ֵ',
			'desc'	=> "����ʹ��<a href=\"$admin_file?adminjob=member\"  onclick=\"parent.PW.Dialog({id:'member',url:'$admin_file?adminjob=member',name:'��Ա���'});return false;\">ȫ��->��Ա���->ǩ������</a>�������",
			'html'	=> '<input class="input input_wa" name="group[fontsize]" value="$fontsize" />'
		),
		/*'maxmsg'	=> array(
			'title'	=> '������Ϣ��Ŀ',
			'desc'	=> '�����Ϣ��Ϊ������Ϣ��������Ⱥ����Ϣ��ϵͳ��Ϣ',
			'html'	=> '<input class="input input_wa" value="$maxmsg" name="group[maxmsg]" />'
		),*/
		/*'maxsendmsg'	=> array(
			'title'	=> 'ÿ������Ͷ���Ϣ��Ŀ',
			'html'	=> '<input class="input input_wa" value="$maxsendmsg" name="group[maxsendmsg]" />'
		),*/
		/*'msggroup'	=> array(
			'title'	=> 'ֻ�����ض��û������Ϣ',
			'html'	=> '<ul class="list_A list_80 cc"><li><input type="radio" value="1" name="group[msggroup]" $msggroup_Y />����</li><li><input type="radio" value="0" name="group[msggroup]" $msggroup_N />�ر�</li></ul>'
		),*/
		/*'ifmemo'	=> array(
			'title'	=> '��ʹ�ñ��Ĺ���',
			'html'	=> '<ul class="list_A list_80 cc"><li><input type="radio" value="1" name="group[ifmemo]" $ifmemo_Y />����</li><li><input type="radio" value="0" name="group[ifmemo]" $ifmemo_N />�ر�</li></ul>'
		),*/
		'pergroup' =>	array(
			'title'	=> '�鿴�û���Ȩ��',
			'html'	=> '<ul class="list_A list_80 cc"><li><input type="checkbox" name="group[pergroup][]" value="member" $pergroup_sel[member] />��Ա��</li><li><input type="checkbox" name="group[pergroup][]" value="system" $pergroup_sel[system] />ϵͳ��</li><li><input type="checkbox" name="group[pergroup][]" value="special" $pergroup_sel[special] />������</li></ul>'
		),
		'maxfavor'	=> array(
			'title'	=> '�ղؼ�����',
			'desc'  => '�����ղؼп����ղص���Ϣ����',
			'html'	=> '<input class="input input_wa" value="$maxfavor" name="group[maxfavor]" />'
		),
		'maxgraft'	=> array(
			'title'	=> '�ݸ�������',
			'desc'  => '���òݸ���������ɵ���Ϣ����',
			'html'	=> '<input class="input input_wa" value="$maxgraft" name="group[maxgraft]" />'
		),
		'pwdlimitime'	=> array(
			'title'	=> 'ǿ���û���������[��]',
			'desc'	=> '0�����ձ�ʾ������',
			'html'	=> '<input class="input input_wa" value="$pwdlimitime" name="group[pwdlimitime]" />'
		),
		/*'maxcstyles'	=> array(
			'title'	=> '�Զ���������',
			'desc'	=> '(����0�����գ�������ʹ���Զ�����)',
			'html'	=> '<input class="input input_wa" value="$maxcstyles" name="group[maxcstyles]" />'
		)*/
		'allowat'	=> array(
			'title'	=> '����@������',
			'desc'	=> '�����󣬴��û�����û�����@�ᵽ����',
			'html'	=> '<ul class="list_A list_80 cc"><li><input type="radio" value="1" name="group[allowat]" $allowat_Y />����</li><li><input type="radio" value="0" name="group[allowat]" $allowat_N />�ر�</li></ul>'
		),
		'atnum'	=> array(
			'title'	=> '��@�û���',
			'desc'	=> '��@�û������ޣ�0�����ձ�ʾ������',
			'html'	=> '<input class="input input_wa" value="$atnum" name="group[atnum]" />'
		),
		'allowvideo' => array(
			'title'	=> '������Ƶ��',
			'desc'	=> '������û����û�����Ƶ��',
			'html'	=> '<ul class="list_A list_80 cc"><li><input type="radio" value="1" name="group[allowvideo]" $allowvideo_Y />����</li><li><input type="radio" value="0" name="group[allowvideo]" $allowvideo_N />�ر�</li></ul>'
		),
		'allowmusic' => array(
			'title'	=> '����������',
			'desc'	=> '������û����û���������',
			'html'	=> '<ul class="list_A list_80 cc"><li><input type="radio" value="1" name="group[allowmusic]" $allowmusic_Y />����</li><li><input type="radio" value="0" name="group[allowmusic]" $allowmusic_N />�ر�</li></ul>'
		),
	),
	'read'	=> array(
		'allowpost'	=> array(
			'title'	=> '��������',
			'desc'	=> '�����󣬴��û�����û����Է�������',
			'html'	=> '<ul class="list_A list_80 cc"><li><input type="radio" value="1" name="group[allowpost]" $allowpost_Y />����</li><li><input type="radio" value="0" name="group[allowpost]" $allowpost_N />�ر�</li></ul>'
		),
		'specialtopics' => array(
			'title'	=> '��������������',
			'desc'	=> '�����޸�ͶƱѡ������û��޸��Լ��ѷ����ͶƱ��ѡ��<br/>��ѡ���û����û��ɷ�����������⣬��Ȩ��ͬʱ�ܰ��Ȩ���еĿɷ�����������Ϳ���',
			'html'	=> '
				<input type="checkbox" name="group[allownewvote]" value="1" $allownewvote_Y /> ͶƱ��
				&nbsp; <select class="elect_wa" name="group[modifyvote]">
					<option value="1" $modifyvote_Y>�����޸�ͶƱѡ��</option>
					<option value="0" $modifyvote_N>�������޸�ͶƱѡ��</option>
				</select>
				<ul class="list_120 cc">
					<li><input type="checkbox" name="group[allowreward]" value="1" $allowreward_Y /> ������</li>
					<li><input type="checkbox" name="group[allowgoods]" value="1" $allowgoods_Y /> ��Ʒ��</li>
					<li><input type="checkbox" name="group[allowdebate]" value="1" $allowdebate_Y /> ������</li>
					<li><input type="checkbox" name="group[allowmodelid]" value="1" $allowmodelid_Y /> ������Ϣ��</li>
					<li><input type="checkbox" name="group[allowpcid]" value="1" $allowpcid_Y /> �Ź���</li>
					<li><input type="checkbox" name="group[allowactivity]" value="1" $allowactivity_Y /> ���</li>
				</ul>
				<div style="height:1px;background:#dde9f5;overflow:hidden;margin:5px 0;"></div>
				<ul class="list_120 cc">
					<li><input type="checkbox" name="group[robbuild]" value="1" $robbuild_Y /> ��¥��</li>
					<li><input type="checkbox" name="group[htmlcode]" value="1" $htmlcode_Y /> html��</li>
					<li><input type="checkbox" name="group[anonymous]" value="1" $anonymous_Y /> ������</li>
					<li><input type="checkbox" name="group[allowhidden]" value="1" $allowhidden_Y /> ������</li>
					<li><input type="checkbox" name="group[allowsell]" value="1" $allowsell_Y /> ������</li>
					<li><input type="checkbox" name="group[allowencode]" value="1" $allowencode_Y /> ������</li>
				</ul>'
		),
		'allowrp'	=> array(
			'title'	=> '�ظ�����',
			'desc'	=> '�����󣬴��û�����û����Իظ�����',
			'html'	=> '<ul class="list_A list_80 cc"><li><input type="radio" value="1" name="group[allowrp]" $allowrp_Y />����</li><li><input type="radio" value="0" name="group[allowrp]" $allowrp_N />�ر�</li></ul>'
		),
		/*'allownewvote'	=> array(
			'title'	=> '����ͶƱ',
			'desc'	=> '�����󣬴��û�����û����Է���ͶƱ',
			'html'	=> '<ul class="list_A list_80 cc"><li><input type="radio" value="1" name="group[allownewvote]" $allownewvote_Y />����</li><li><input type="radio" value="0" name="group[allownewvote]" $allownewvote_N />�ر�</li></ul>'
		),
		'modifyvote'	=> array(
			'title'	=> '�޸ķ����ͶƱѡ��',
			'desc'	=> '�����󣬴��û�����û���Ȩ���޸ķ����ͶƱѡ��',
			'html'	=> '<ul class="list_A list_80 cc"><li><input type="radio" value="1" name="group[modifyvote]" $modifyvote_Y />����</li><li><input type="radio" value="0" name="group[modifyvote]" $modifyvote_N />�ر�</li></ul>'
		),*/
		'allowvote'	=> array(
			'title' => '����ͶƱ',
			'desc'	=> '�����󣬴��û�����û����Բ���ͶƱ',
			'html'	=> '<ul class="list_A list_80 cc"><li><input type="radio" value="1" name="group[allowvote]" $allowvote_Y />����</li><li><input type="radio" value="0" name="group[allowvote]" $allowvote_N />�ر�</li></ul>'
		),
		'viewvote'	=> array(
			'title'	=> '�鿴ͶƱ�û�',
			'desc'	=> '�����󣬴��û�����û����Բ鿴ͶƱ�û�',
			'html'	=> '<ul class="list_A list_80 cc"><li><input type="radio" value="1" name="group[viewvote]" $viewvote_Y />����</li><li><input type="radio" value="0" name="group[viewvote]" $viewvote_N />�ر�</li></ul>'
		),
		'leaveword'	=>	array(
			'title'	=> '¥������',
			'desc'	=> '�����󣬴��û�����û���ʹ��¥�����Թ���',
			'html'	=> '<ul class="list_A list_80 cc"><li><input type="radio" value="1" name="group[leaveword]" $leaveword_Y />����</li><li><input type="radio" value="0" name="group[leaveword]" $leaveword_N />�ر�</li></ul>'
		),
		'dig'	=> array(
			'title'	=> '�Ƽ�����',
			'desc'	=> '�����󣬴��û�����û������Ƽ�����',
			'html'	=> '<ul class="list_A list_80 cc"><li><input type="radio" value="1" name="group[dig]" $dig_Y />����</li><li><input type="radio" value="0" name="group[dig]" $dig_N />�ر�</li></ul>'
		),
		/*'allowactive'	=> array(
			'title'	=> '�����',
			'html'	=> '<ul class="list_A list_80 cc"><li><input type="radio" value="1" name="group[allowactive]" $allowactive_Y />����</li><li><input type="radio" value="0" name="group[allowactive]" $allowactive_N />�ر�</li></ul>'
		),*/
		/*'allowreward'	=> array(
			'title'	=> '������',
			'desc'	=> '�����󣬴��û�����û����Է���������',
			'html'	=> '<ul class="list_A list_80 cc"><li><input type="radio" value="1" name="group[allowreward]" $allowreward_Y />����</li><li><input type="radio" value="0" name="group[allowreward]" $allowreward_N />�ر�</li></ul>'
		),
		'allowgoods'	=> array(
			'title'	=> '��Ʒ��',
			'desc'	=> '�����󣬴��û�����û����Է�����Ʒ��',
			'html'	=> '<ul class="list_A list_80 cc"><li><input type="radio" value="1" name="group[allowgoods]" $allowgoods_Y />����</li><li><input type="radio" value="0" name="group[allowgoods]" $allowgoods_N />�ر�</li></ul>'
		),
		'allowdebate'	=> array(
			'title'	=> '������',
			'desc'	=> '�����󣬴��û�����û����Է��������',
			'html'	=> '<ul class="list_A list_80 cc"><li><input type="radio" value="1" name="group[allowdebate]" $allowdebate_Y />����</li><li><input type="radio" value="0" name="group[allowdebate]" $allowdebate_N />�ر�</li></ul>'
		),
		'allowmodelid'	=> array(
			'title'	=> '������Ϣ��',
			'desc'	=> '�����󣬴��û�����û����Է��������Ϣ��',
			'html'	=> '<ul class="list_A list_80 cc"><li><input type="radio" value="1" name="group[allowmodelid]" $allowmodelid_Y />����</li><li><input type="radio" value="0" name="group[allowmodelid]" $allowmodelid_N />�ر�</li></ul>'
		),
		'allowpcid'	=> array(
			'title'	=> '�Ź���',
			'desc'	=> '�����󣬴��û�����û����Է����Ź���',
			'html'	=> '<ul class="list_A list_80 cc"><li><input type="radio" name="group[allowpcid]" value="1" $allowpcid_Y/>����</li><li><input type="radio" name="group[allowpcid]" value="0" $allowpcid_N />�ر�</li>'
			//<li><input type="checkbox" name="group[allowpcid][]" value="2" $allowpcid_sel[2] />�</li></ul>'
		),
		'allowactivity'	=> array(
			'title'	=> '���',
			'desc'	=> '�����󣬴��û�����û����Է�����',
			'html'	=> '<ul class="list_A list_80 cc"><li><input type="radio" value="1" name="group[allowactivity]" $allowactivity_Y />����</li><li><input type="radio" value="0" name="group[allowactivity]" $allowactivity_N />�ر�</li></ul>'
		),
		'robbuild'	=> array(
			'title'	=> '��¥��',
			'desc'	=> '�����󣬴��û�����û����Է�����¥��',
			'html'	=> '<ul class="list_A list_80 cc"><li><input type="radio" value="1" name="group[robbuild]" $robbuild_Y />����</li><li><input type="radio" value="0" name="group[robbuild]" $robbuild_N />�ر�</li></ul>'
		),
		'htmlcode'	=> array(
			'title'	=> '����html��',
			'desc'	=> '�⽫ʹ�û�ӵ��ֱ�ӱ༭ html Դ�����Ȩ��!',
			'html'	=> '<ul class="list_A list_80 cc"><li><input type="radio" value="1" name="group[htmlcode]" $htmlcode_Y />����</li><li><input type="radio" value="0" name="group[htmlcode]" $htmlcode_N />�ر�</li></ul>'
		),
		'allowhidden'	=> array(
			'title'	=> '������',
			'desc'	=> 'ע��������ͬʱ�ܰ��Ȩ�����ƣ��������Ȩ���еķ������������ܷ���Ч',
			'html'	=> '<ul class="list_A list_80 cc"><li><input type="radio" value="1" name="group[allowhidden]" $allowhidden_Y />����</li><li><input type="radio" value="0" name="group[allowhidden]" $allowhidden_N />�ر�</li></ul>'
		),
		'allowsell'	=> array(
			'title'	=> '������',
			'desc'	=> 'ע��������ͬʱ�ܰ��Ȩ�����ƣ��������Ȩ���еķ�����������ܷ���Ч',
			'html'	=> '<ul class="list_A list_80 cc"><li><input type="radio" value="1" name="group[allowsell]" $allowsell_Y />����</li><li><input type="radio" value="0" name="group[allowsell]" $allowsell_N />�ر�</li></ul>'
		),
		'allowencode'	=> array(
			'title'	=> '������',
			'desc'	=> 'ע��������ͬʱ�ܰ��Ȩ�����ƣ��������Ȩ���еķ�����������ܷ���Ч',
			'html'	=> '<ul class="list_A list_80 cc"><li><input type="radio" value="1" name="group[allowencode]" $allowencode_Y />����</li><li><input type="radio" value="0" name="group[allowencode]" $allowencode_N />�ر�</li></ul>'
		),
		'anonymous'	=> array(
			'title'	=> '������',
			'desc'	=> 'ע��������ͬʱ�ܰ��Ȩ�����ƣ��������Ȩ���еķ������������ܷ���Ч',
			'html'	=> '<ul class="list_A list_80 cc"><li><input type="radio" value="1" name="group[anonymous]" $anonymous_Y />����</li><li><input type="radio" value="0" name="group[anonymous]" $anonymous_N />�ر�</li></ul>'
		),*/
		
		'allowdelatc'	=> array(
			'title'	=> 'ɾ���Լ�������',
			'desc'	=> '�����󣬴��û�����û�����ɾ���Լ�������',
			'html'	=> '<ul class="list_A list_80 cc"><li><input type="radio" value="1" name="group[allowdelatc]" $allowdelatc_Y />����</li><li><input type="radio" value="0" name="group[allowdelatc]" $allowdelatc_N />�ر�</li></ul>'
		),
		'atccheck'	=> array(
			'title'	=> '���������',
			'desc'	=> '��������������ʱ,����������Ƿ���Ҫ����Ա��ˣ�����ֻ���ڿ�������������ʱ��Ч',
			'html'	=> '<ul class="list_A list_80 cc"><li><input type="radio" value="1" name="group[atccheck]" $atccheck_Y />����</li><li><input type="radio" value="0" name="group[atccheck]" $atccheck_N />�ر�</li></ul>'
		),
		'allowreplyreward' => array(
			'title'	=> '�������û�������',
			'desc'	=> '������û����û��ڷ���ʱ���ظ���һ���Ļ��ֽ���',
			'html'	=> '<ul class="list_A list_80 cc"><li><input type="radio" value="1" name="group[allowreplyreward]" $allowreplyreward_Y />����</li><li><input type="radio" value="0" name="group[allowreplyreward]" $allowreplyreward_N />�ر�</li></ul>'
		),
		'allowremotepic' => array(
			'title'	=> '��������Զ��ͼƬ',
			'desc'	=> '������û����û��ڷ���ʱ��Զ��ͼƬ���ػ�',
			'html'	=> '<ul class="list_A list_80 cc"><li><input type="radio" value="1" name="group[allowremotepic]" $allowremotepic_Y />����</li><li><input type="radio" value="0" name="group[allowremotepic]" $allowremotepic_N />�ر�</li></ul>'
		),
		'postlimit'	=> array(
			'title'	=> 'ÿ����෢����',
			'desc'	=> '0�����ձ�ʾ������',
			'html'	=> '<input class="input input_wa" value="$postlimit" name="group[postlimit]" />'
		),
		'allowbuykmd' => array(
			'title'	=> '�����������',
			'desc'	=> '',
			'html'	=> '<ul class="list_A list_80 cc"><li><input type="radio" value="1" name="group[allowbuykmd]" $allowbuykmd_Y />����</li><li><input type="radio" value="0" name="group[allowbuykmd]" $allowbuykmd_N />�ر�</li></ul>'
		),
		'postpertime'	=> array(
			'title'	=> '��������ʱ����ƣ��룩',
			'desc'	=> '�趨��ʱ�������û���������������0�����ձ�ʾ�����ƣ��˹���ԭ��Ϊ����ˮԤ��',
			'html'	=> '<input class="input input_wa" value="$postpertime" name="group[postpertime]" />'
		),
		'edittime'	=> array(
			'title'	=> '�ɱ༭ʱ����ƣ����ӣ�',
			'desc'	=> '�û������ɹ��󣬿������趨��ʱ��������±༭���ӣ�0�����ձ�ʾ�����ƣ��˹���ԭ��Ϊ���༭ʱ��Լ��[����]',
			'html'	=> '<input class="input input_wa" value="$edittime" name="group[edittime]" />'
		),
		//������������������@modify panjl@2010-11-2
		'posturlnum'	=> array(
			'title'	=> '����������������',
			'desc'	=> '�������ﵽ�趨֮��ſ��Է�������ӵ����ӣ�0�����ձ�ʾ�����ƣ������ÿɷ�ֹע���ע��󷢲������ӵĹ����',
			'html'	=> '<input class="input input_wa" value="$posturlnum" name="group[posturlnum]" />'
		),
		'media'	=> array(
			'title'	=> '��ý���Զ�չ��',
			'html'	=> '<ul class="list_A list_80 cc"><li><input type="checkbox" name="group[media][]" value="flash" $media_sel[flash] />flash</li><li>
			<input type="checkbox" name="group[media][]" value="wmv" $media_sel[wmv] />wmv</li><li>
			<input type="checkbox" name="group[media][]" value="rm" $media_sel[rm] />rm</li><li>
			<input type="checkbox" name="group[media][]" value="mp3" $media_sel[mp3] />mp3</li></ul>'
		),
		'markable'	=> array(
			'title'	=> '��������Ȩ��',
			'desc'	=> '��������������ʼ��������Ȩ��',
			'html'	=> '<ul class="list_A cc td2_wp"><li><input type="radio" value="0" name="group[markable]" $markable_0 />��</li><li>
			<input type="radio" value="1" name="group[markable]" $markable_1 />��������</li><li>
			<input type="radio" value="2" name="group[markable]" $markable_2 />�����ظ�����</li></ul>'
		),
		/*'maxcredit'	=> array(
			'title' => '��������<font color=blue> ˵����</font>ÿ�����л����ܺ������������ֵ���',
			'html'	=> '<input type="text" class="input input_wa" value="$maxcredit" name="group[maxcredit]" />'
		),
		'marklimit' => array(
			'title'	=> '��������<font color=blue> ˵����</font>ÿ�����ֵ�������Сֵ',
			'html'	=> '��С <input type=text size="3" class="input" value="$minper" name="group[marklimit][0]" /> ��� <input type=text size="3" class="input" value="$maxper" name="group[marklimit][1]" />'
		),*/
		'markset'	=> array(
			'title'	=> '������������',
			'desc'	=> '��ѡ��ѡ�л����������ޡ����������κ�һ������/��Ϊ0��ǰ̨���޷�ʹ�ø���������',
			'html'	=> $credit_type
		)
		/*'markdt'	=> array(
			'title'	=> '�����Ƿ���Ҫ�۳�������Ӧ�Ļ���',
			'html'	=> '<ul class="list_A list_80 cc"><li><input type="radio" value="1" name="group[markdt]" $markdt_Y />����</li><li><input type="radio" value="0" name="group[markdt]" $markdt_N />�ر�</li></ul>'
		),
		'postpertime'	=> array(
			'title'	=> '��ˮԤ��',
			'desc'	=> '���������ڲ��ܷ�����0�����ձ�ʾ������',
			'html'	=> '<input class="input input_wa" value="$postpertime" name="group[postpertime]" />'
		),
		'edittime'	=> array(
			'title'	=> '�༭ʱ��Լ��[����]',
			'desc'	=> '�����趨ʱ���ܾ��û��༭��0�����ձ�ʾ������',
			'html'	=> '<input class="input input_wa" value="$edittime" name="group[edittime]" />'
		),
		//������������������@modify panjl@2010-11-2
		'posturlnum'	=> array(
			'title'	=> '��������������',
			'desc'	=> '����Ա�ķ�������������ﵽ�趨ֵ�Ժ�Ϳ��Է�������ӵ����ӡ�������Ϊ��ֹע���ע��󷢴������ӵ�ַ�Ĺ�档0�����ձ�ʾ������',
			'html'	=> '<input class="input input_wa" value="$posturlnum" name="group[posturlnum]" />'
		)*/
	),
	'group' =>array(
		'allowcreate'=>array(
			'title'=>'������Ⱥ�����',
			'desc' =>"0 �� ���� ��ʾû���ƣ���Ҫ��Ⱥ����������п���<a href=\"$admin_file?adminjob=apps&admintype=groups_set\">��������Ⱥ��</a>����Ч",
			'html'=>'<input size="35" class="input" value="$allowcreate" name="group[allowcreate]" />'
		),
		'allowjoin'=>array(
			'title'=>'�������Ⱥ�����',
			'desc' =>'0 �� ���� ��ʾû����',
			'html'=>'<input size="35" class="input" value="$allowjoin" name="group[allowjoin]" />'
		)
	),
	'att'	=> array(
		'allowupload'	=> array(
			'title'	=> '�ϴ�����Ȩ��',
			'desc'	=> '���ڰ�����ô������ϴ�������������۳�������',
			'html'	=> '<ul class="list_A"><li><input type="radio" value="0" name="group[allowupload]" $allowupload_0 />�������ϴ�����</li><li><input type="radio" value="1" name="group[allowupload]" $allowupload_1 />�����ϴ����������հ�����ý�����۳�����</li><li><input type="radio" value="2" name="group[allowupload]" $allowupload_2 />�����ϴ���������������۳�����</li></ul>'
		),
		'allowdownload'	=> array(
			'title'	=> '���ظ���Ȩ��',
			'desc'	=> '���ڰ�����ô��������ظ�����������۳�������',
			'html'	=> '<ul class="list_A"><li><input type="radio" value="0" name="group[allowdownload]" $allowdownload_0 />���������ظ���</li><li><input type="radio" value="1" name="group[allowdownload]" $allowdownload_1 />�������ظ��������հ�����ý�����۳�����</li><li><input type="radio" value="2" name="group[allowdownload]" $allowdownload_2 />�������ظ�������������۳�����</li></ul>'
		),
		'allownum'	=> array(
			'title'	=> 'һ������ϴ���������',
			'html'	=> '<input class="input input_wa" value="$allownum" name="group[allownum]" />'
		),
		'uploadtype'	=> array(
			'title'	=> '�����ϴ��ĺ�׺�ͳߴ�',
			'desc'	=> "<font color=\"red\">ϵͳ�����ϴ��������ߴ�Ϊ{$maxuploadsize},</font>������ʹ��վ��ȫ���е�����",
			'html'	=> '<div class="admin_table_b"><table cellpadding="0" cellspacing="0">
				<tbody id="mode" style="display:none"><tr>
					<td><input class="input input_wc" name="filetype[]" value=""></td>
					<td><input class="input input_wc" name="maxsize[]" value=""></td><td><a href="javascript:;" onclick="removecols(this);">[ɾ��]</a></td>
				</tr></tbody>
				<tr>
					<td>��׺��(<b>Сд</b>)</td>
					<td>���ߴ�(KB)</td>
					<td><a href="javascript:;" class="s3" onclick="addcols(\'mode\',\'ft\');">[���]</a></td>
				</tr>
				{$upload_type}
				<tbody id="ft"></tbody>
			</table></div>
			<script type="text/javascript">
			addcols(\'mode\',\'ft\');
			</script>'
		)
	),
	'special' => array(
		'allowbuy'	=> array(
			'title'	=> '������',
			'desc'	=> "�����ù��ܺ���ͬʱ��<a href=\"$admin_file?adminjob=plantodo\"><font color=\"blue\">�ƻ�����</font></a>����������ͷ���Զ����ա�����.",
			'html'	=> '<ul class="list_A list_80 cc"><li><input type="radio" value="1" name="group[allowbuy]" $allowbuy_Y />����</li><li><input type="radio" value="0" name="group[allowbuy]" $allowbuy_N />�ر�</li></ul>'
		),
		'selltype'	=> array(
			'title'	=> '�����鹺�����',
			'html'	=> '<select name="group[selltype]" class="select_wa">$special_type</select>'
		),
		'sellprice'	=> array(
			'title'	=> 'ÿ�ռ۸�[����]',
			'html'	=> '<input type="text" class="input input_wa" name="group[sellprice]" value="$sellprice" />'
		),
		'rmbprice'	=> array(
			'title'	=> 'ÿ�ռ۸�[�ֽ�]',
			'desc'	=> '���ô˼۸񣬽�����ͨ������֧��������',
			'html'	=> '<input type="text" class="input input_wa" name="group[rmbprice]" value="$rmbprice" />'
		),
		'selllimit'	=> array(
			'title'	=> '������������������',
			'html'	=> '<input type="text" class="input input_wa" name="group[selllimit]" value="$selllimit" />'
		),
		'sellinfo'	=> array(
			'title'	=> '����������',
			'desc'	=> '������д����˵���͸��û���ӵ�е�����Ȩ��',
			'html'	=> '<textarea name="group[sellinfo]" class="textarea">$sellinfo</textarea>'
		)
	),
	'system'	=> array(
		'superright' => array(
			'title'	=> '��������Ȩ��',
			'desc'	=> "<font color=\"red\">��</font>������������԰���Ȩ�����ö����а����Ч�����磺����Ա��<br /><font color=\"red\">��</font>������������԰���Ȩ�����ö����а����Ч����ʱ���Ҫ���õ������Ĺ���Ȩ�ޣ���Ҫ��<a href=\"$admin_file?adminjob=singleright\">����û�Ȩ��</a>���������<br />��ע�����༭����Ȩ��ʱ����������������а�鶼ӵ��Ȩ�ޣ��ر������ֻ�ڱ����ӵ��Ȩ�ޣ�",
			'html'	=> '<ul class="list_A list_80 cc"><li><input type="radio" value="1" name="group[superright]" $superright_Y />����</li><li><input type="radio" value="0" name="group[superright]" $superright_N />�ر�</li></ul>'
		),
		'enterreason' => array(
			'title' => 'ǿ���������ԭ��',
			'desc' => '������ǰ̨��������в����������������ԭ�򡣿ɱ������ڹ�������ϵĲ�͸��������վ���Ա����',
			'html'	=> '<ul class="list_A list_80 cc"><li><input type="radio" value="1" name="group[enterreason]" $enterreason_Y />����</li><li><input type="radio" value="0" name="group[enterreason]" $enterreason_N />�ر�</li></ul>'
		),
		'colonyright' => array(
			'title'	=> 'Ⱥ�����Ȩ��',
			'desc'	=> "�����˴�Ȩ�ޣ������û����߱�����Ⱥ�����Ա�Ĺ���Ȩ��",
			'html'	=> '<ul class="list_A list_80 cc"><li><input type="radio" value="1" name="group[colonyright]" $colonyright_Y />����</li><li><input type="radio" value="0" name="group[colonyright]" $colonyright_N />�ر�</li></ul>'
		),
		'forumcolonyright' => array(
			'title'	=> '�������Ⱥ�����Ȩ��',
			'desc'	=> "�����˴�Ȩ�ޣ������û����߱����������Ⱥ��Ĺ���Ȩ��",
			'html'	=> '<ul class="list_A list_80 cc"><li><input type="radio" value="1" name="group[forumcolonyright]" $forumcolonyright_Y />����</li><li><input type="radio" value="0" name="group[forumcolonyright]" $forumcolonyright_N />�ر�</li></ul>'
		)
	),
	'systemforum' => array(
		'posthide'	=> array(
			'title'	=> '�鿴������',
			'html'	=> '<ul class="list_A list_80 cc"><li><input type="radio" value="1" name="group[posthide]" $posthide_Y />����</li><li><input type="radio" value="0" name="group[posthide]" $posthide_N />�ر�</li></ul>'
		),
		'sellhide'	=> array(
			'title'	=> '�鿴������',
			'html'	=> '<ul class="list_A list_80 cc"><li><input type="radio" value="1" name="group[sellhide]" $sellhide_Y />����</li><li><input type="radio" value="0" name="group[sellhide]" $sellhide_N />�ر�</li></ul>'
		),
		'encodehide'	=> array(
			'title'	=> '�鿴������',
			'html'	=> '<ul class="list_A list_80 cc"><li><input type="radio" value="1" name="group[encodehide]" $encodehide_Y />����</li><li><input type="radio" value="0" name="group[encodehide]" $encodehide_N />�ر�</li></ul>'
		),
		'anonyhide'	=> array(
			'title'	=> '�鿴������',
			'html'	=> '<ul class="list_A list_80 cc"><li><input type="radio" value="1" name="group[anonyhide]" $anonyhide_Y />����</li><li><input type="radio" value="0" name="group[anonyhide]" $anonyhide_N />�ر�</li></ul>'
		),
		'activitylist'=> array(
			'title'	=> '���������б�',
			'desc'	=> '����֮���в鿴�����б������б�Ⱥ�����ŵȲ���Ȩ��',
			'html'	=> '<ul class="list_A list_80 cc"><li><input type="radio" value="1" name="group[activitylist]" $activitylist_Y />����</li><li><input type="radio" value="0" name="group[activitylist]" $activitylist_N />�ر�</li></ul>'
		),
		'postpers'	=> array(
			'title'	=> '��ˮ',
			'desc'	=> '���ܹ�ˮʱ������',
			'html'	=> '<ul class="list_A list_80 cc"><li><input type="radio" value="1" name="group[postpers]" $postpers_Y />����</li><li><input type="radio" value="0" name="group[postpers]" $postpers_N />�ر�</li></ul>'
		),
		'replylock'	=> array(
			'title'	=> '�ظ�������',
			'html'	=> '<ul class="list_A list_80 cc"><li><input type=radio value=1 $replylock_Y name=group[replylock]>����</li><li><input type=radio value=0 $replylock_N name=group[replylock]>�ر�</li></ul>'
		),
		'viewip'	=> array(
			'title'	=> '�鿴IP',
			'desc'	=> '�������ʱ��ʾ,����Ա�����ܸù�������',
			'html'	=> '<ul class="list_A list_80 cc"><li><input type="radio" value="1" name="group[viewip]" $viewip_Y />����</li><li><input type="radio" value="0" name="group[viewip]" $viewip_N />�ر�</li></ul>'
		),
		'topped'	=> array(
			'title'	=> '�ö�Ȩ��',
			'html'	=> '<ul class="list_A"><li><input type="radio" value="0" name="group[topped]" $topped_0 />��</li><li>
			<input type="radio" value="1" name="group[topped]" $topped_1 />����ö�</li><li>
			<input type="radio" value="2" name="group[topped]" $topped_2 />����ö�,�����ö�</li><li>
			<input type="radio" value="3" name="group[topped]" $topped_3 />����ö�,�����ö�,���ö�</li><li>
			<input type="radio" value="4" name="group[topped]" $topped_4 />�������ö�</li></ul>'
		),
		'replayorder' => array(
			'title'	  => '���ӻظ���ʾ˳��',
			'desc'    => '�������ڱ༭����ʱ���û��ܹ��������ӻظ�����ʾ˳��',
			'html'	  => '<ul class="list_A list_80 cc"><li><input type="radio" value="1" name="group[replayorder]" $replayorder_Y />����</li><li><input type="radio" value="0" name="group[replayorder]" $replayorder_N />�ر�</li></ul>',
		),
		'digestadmin'	=> array(
			'title'	=> 'ǰ̨����',
			'html'	=> '<ul class="list_A list_80 cc"><li><input type="radio" value="1" name="group[digestadmin]" $digestadmin_Y />����</li><li><input type="radio" value="0" name="group[digestadmin]" $digestadmin_N />�ر�</li></ul>'
		),
		'lockadmin'	=> array(
			'title'	=> 'ǰ̨����',
			'html'	=> '<ul class="list_A list_80 cc"><li><input type="radio" value="1" name="group[lockadmin]" $lockadmin_Y />����</li><li><input type="radio" value="0" name="group[lockadmin]" $lockadmin_N />�ر�</li></ul>'
		),
		'pushadmin'	=> array(
			'title'	=> 'ǰ̨��ǰ',
			'html'	=> '
			<ul class="list_A list_80 cc fl mr20"><li><input type="radio" value="1" name="group[pushadmin]" $pushadmin_Y />����</li><li><input type="radio" value="0" name="group[pushadmin]" $pushadmin_N />�ر�</li></ul>'
		),
		'pushtime'	=> array(
			'title'	=> '��ǰʱ������[Сʱ]',
			'desc'	=> '���ջ�0��ʾ������',
			'html'	=> '<input type="text" value="$pushtime" name="group[pushtime]" class="input input_wa" />'
		),
		'coloradmin'	=> array(
			'title'	=> 'ǰ̨����',
			'html'	=> '<ul class="list_A list_80 cc"><li><input type="radio" value="1" name="group[coloradmin]" $coloradmin_Y />����</li><li><input type="radio" value="0" name="group[coloradmin]" $coloradmin_N />�ر�</li></ul>'
		),
		'downadmin'	=> array(
			'title'	=> 'ǰ̨ѹ��',
			'html'	=> '<ul class="list_A list_80 cc"><li><input type="radio" value="1" name="group[downadmin]" $downadmin_Y />����</li><li><input type="radio" value="0" name="group[downadmin]" $downadmin_N />�ر�</li></ul>'
		),
		'replaytopped' => array(
			'title'    => 'ǰ̨�����ö�',
			'html'	   => '<ul class="list_A list_80 cc"><li><input type="radio" value="1" name="group[replaytopped]" $replaytopped_Y />����</li><li><input type="radio" value="0" name="group[replaytopped]" $replaytopped_N />�ر�</li></ul>',
		),
		'tpctype'	=> array(
			'title'	=> '����������',
			'desc'	=> '<font color=blue> ˵����</font>���������������Ȩ��',
			'html'	=> '<ul class="list_A list_80 cc"><li><input type="radio" value="1" name="group[tpctype]" $tpctype_Y />����</li><li><input type="radio" value="0" name="group[tpctype]" $tpctype_N />�ر�</li></ul>'
		),
		'tpccheck'	=> array(
			'title'	=> '������֤����',
			'desc'	=> '<font color=blue> ˵����</font>ǰ̨������֤����Ȩ��',
			'html'	=> '<ul class="list_A list_80 cc"><li><input type="radio" value="1" name="group[tpccheck]" $tpccheck_Y />����</li><li><input type="radio" value="0" name="group[tpccheck]" $tpccheck_N />�ر�</li></ul>'
		),
		'delatc'	=> array(
			'title'	=> '����ɾ������',
			'desc'	=> '<font color=blue> ˵����</font>ǰ̨���ӹ���Ȩ��',
			'html'	=> '<ul class="list_A list_80 cc"><li><input type="radio" value="1" name="group[delatc]" $delatc_Y />����</li><li><input type="radio" value="0" name="group[delatc]" $delatc_N />�ر�</li></ul>'
		),
		'moveatc'	=> array(
			'title'	=> '�����ƶ�����',
			'desc'	=> '<font color=blue> ˵����</font>ǰ̨���ӹ���Ȩ��',
			'html'	=> '<ul class="list_A list_80 cc"><li><input type="radio" value="1" name="group[moveatc]" $moveatc_Y />����</li><li><input type="radio" value="0" name="group[moveatc]" $moveatc_N />�ر�</li></ul>'
		),
		'copyatc'	=> array(
			'title'	=> '������������',
			'desc'	=> '<font color=blue> ˵����</font>ǰ̨���ӹ���Ȩ��',
			'html'	=> '<ul class="list_A list_80 cc"><li><input type="radio" value="1" name="group[copyatc]" $copyatc_Y />����</li><li><input type="radio" value="0" name="group[copyatc]" $copyatc_N />�ر�</li></ul>'
		),
		'modother'	=> array(
			'title'	=> 'ɾ����һ����[�����ظ�]',
			'html'	=> '<ul class="list_A list_80 cc"><li><input type="radio" value="1" name="group[modother]" $modother_Y />����</li><li><input type="radio" value="0" name="group[modother]" $modother_N />�ر�</li></ul>'
		),
		'deltpcs'	=> array(
			'title'	=> '�༭�û�����',
			'html'	=> '<ul class="list_A list_80 cc"><li><input type="radio" value="1" name="group[deltpcs]" $deltpcs_Y />����</li><li><input type="radio" value="0" name="group[deltpcs]" $deltpcs_N />�ر�</li></ul>'
		),
		'viewcheck'	=> array(
			'title'	=> '�鿴��Ҫ��֤������',
			'html'	=> '<ul class="list_A list_80 cc"><li><input type="radio" value="1" name="group[viewcheck]" $viewcheck_Y />����</li><li><input type="radio" value="0" name="group[viewcheck]" $viewcheck_N />�ر�</li></ul>'
		),
		'viewclose'	=> array(
			'title'	=> '�鿴�ر�����',
			'html'	=> '<ul class="list_A list_80 cc"><li><input type="radio" value="1" name="group[viewclose]" $viewclose_Y />����</li><li><input type="radio" value="0" name="group[viewclose]" $viewclose_N />�ر�</li></ul>'
		),
		'delattach'	=> array(
			'title'	=> 'ɾ������',
			'html'	=> '<ul class="list_A list_80 cc"><li><input type="radio" value="1" name="group[delattach]" $delattach_Y />����</li><li><input type="radio" value="0" name="group[delattach]" $delattach_N />�ر�</li></ul>'
		),
		'shield'	=> array(
			'title'	=> '���ε�һ����',
			'html'	=> '<ul class="list_A list_80 cc"><li><input type="radio" value="1" name="group[shield]" $shield_Y />����</li><li><input type="radio" value="0" name="group[shield]" $shield_N />�ر�</li></ul>'
		),
		'unite'	=> array(
			'title'	=> '�ϲ�����',
			'html'	=> '<ul class="list_A list_80 cc"><li><input type="radio" value="1" name="group[unite]" $unite_Y />����</li><li><input type="radio" value="0" name="group[unite]" $unite_N />�ر�</li></ul>'
		),
		'split'	=> array(
			'title'	=> '�������',
			'html'	=> '<ul class="list_A list_80 cc"><li><input type="radio" value="1" name="group[split]" $split_Y />����</li><li><input type="radio" value="0" name="group[split]" $split_N />�ر�</li></ul>'
		),
		'remind'	=> array(
			'title'	=> '���ӹ�������',
			'html'	=> '<ul class="list_A list_80 cc"><li><input type="radio" value="1" name="group[remind]" $remind_Y />����</li><li><input type="radio" value="0" name="group[remind]" $remind_N />�ر�</li></ul>'
		),
		'pingcp'	=> array(
			'title'	=> '�������ּ�¼',
			'html'	=> '<ul class="list_A list_80 cc"><li><input type="radio" value="1" name="group[pingcp]" $pingcp_Y />����</li><li><input type="radio" value="0" name="group[pingcp]" $pingcp_N />�ر�</li></ul>'
		),
		'inspect'	=> array(
			'title'	=> '����������Ķ�',
			'desc'	=> '<font color=blue> ˵����</font>���ڰ�����Ȩ���￪���󷽿���',
			'html'	=> '<ul class="list_A list_80 cc"><li><input type="radio" value="1" name="group[inspect]" $inspect_Y />����</li><li><input type="radio" value="0" name="group[inspect]" $inspect_N />�ر�</li></ul>'
		),
		'allowtime'	=> array(
			'title'	=> '���ܰ�鷢��ʱ��������',
			'html'	=> '<ul class="list_A list_80 cc"><li><input type="radio" value="1" name="group[allowtime]" $allowtime_Y />����</li><li><input type="radio" value="0" name="group[allowtime]" $allowtime_N />�ر�</li></ul>'
		),
		'banuser'	=> array(
			'title'	=> '�����û���Ȩ��',
			'desc'	=> '<font color="blue">˵��:</font><br />
			<font color="red">�޽���Ȩ��:</font>���û�����Ȩ�޶Ի�Ա���н��Բ���<br />
			<font color="red">���а��:</font>(�н���Ȩ��)���ұ����Ի�Ա�����а���ж�ûȨ�޷���<br />
			<font color="red">��һ���</font>(�н���Ȩ��)���ұ����Ի�Ա���������ڰ��ûȨ�޷���,������������п��Է���',
			'html'	=> '<ul class="list_A"><li><input type="radio" value="0" name="group[banuser]" $banuser_0 />�޽���Ȩ��</li><li><input type="radio" value="1" name="group[banuser]" $banuser_1 />��һ���</li><li><input type="radio" value="2" name="group[banuser]" $banuser_2 />���а��</li></ul>'
		),
		'bantype'	=> array(
			'title'	=> '���ý����û�',
			'html'	=> '<ul class="list_A list_80 cc"><li><input type="radio" value="1" name="group[bantype]" $bantype_Y />����</li><li><input type="radio" value="0" name="group[bantype]" $bantype_N />�ر�</li></ul>'
		),
		'banmax'	=> array(
			'title'	=> '����ʱ������',
			'desc'	=> '<font color=blue> ˵����</font>���Ի�Ա���������',
			'html'	=> '<input type=text class="input input_wa" value="$banmax" name="group[banmax]" />'
		),
		'banuserip' => array(
			'title' => '��ֹip',
			'desc'  => '������ӵ�н�ֹipȨ��',
			'html'	=> '<ul class="list_A list_80 cc"><li><input type="radio" value="1" name="group[banuserip]" $banuserip_Y />����</li><li><input type="radio" value="0" name="group[banuserip]" $banuserip_N />�ر�</li></ul>'
		),
		'banadmin'	=> array(
			'title'	=> '�ɽ��Թ�����',
			'desc'	=> '<font color=blue> ˵����</font>�����󣬿��Խ��������û��飬����ϵͳ��������û���',
			'html'	=> '<ul class="list_A list_80 cc"><li><input type="radio" value="1" name="group[banadmin]" $banadmin_Y />����</li><li><input type="radio" value="0" name="group[banadmin]" $banadmin_N />�ر�</li></ul>'
		),
		'bansignature' => array(
			'title' => '��ֹ����ǩ��',
			'desc'  => '������ӵ�н�ֹ����ǩ��Ȩ��',
			'html'	=> '<ul class="list_A list_80 cc"><li><input type="radio" value="1" name="group[bansignature]" $bansignature_Y />����</li><li><input type="radio" value="0" name="group[bansignature]" $bansignature_N />�ر�</li></ul>'
		),
		'areapush'	=> array(
			'title'	=> '�Ż�����',
			'desc'	=> '<font color=blue> ˵����</font>�Ƿ������Ż�ģʽ����ҳ��Ƶ��ҳ�������ӵ�Ȩ��',
			'html'	=> '<ul class="list_A list_80 cc"><li><input type="radio" value="1" name="group[areapush]" $areapush_Y />����</li><li><input type="radio" value="0" name="group[areapush]" $areapush_N />�ر�</li></ul>'
		),
		'overprint'	=> array(
			'title'	=> '����ӡ��',
			'desc'	=> '<font color=blue> ˵����</font>�Ƿ��и����Ӽ�ӡ��Ч����Ȩ��',
			'html'	=> '<ul class="list_A list_80 cc"><li><input type="radio" value="1" name="group[overprint]" $overprint_Y />����</li><li><input type="radio" value="0" name="group[overprint]" $overprint_N />�ر�</li></ul>'
		),
		'tcanedit'	=> array(
			'title'	=> '�ɱ༭����������',
			'desc'	=> '<font color=blue> ˵����</font>�ɱ༭�����������',
			'html'	=> '<ul class="list_A list_80 cc">
			<li><label><input type="checkbox" name="group[tcanedit][]" value="3" $tcanedit_sel[3]/>' . $ltitle[3] . '</label></li>
			<li><label><input type="checkbox" name="group[tcanedit][]" value="4" $tcanedit_sel[4]/>' . $ltitle[4] . '</label></li>
			<li><label><input type="checkbox" name="group[tcanedit][]" value="5" $tcanedit_sel[5]/>' . $ltitle[5] . '</label></li></ul>'
		),
		'deldiary'	=> array(
			'title'	=> '��־ɾ��Ȩ��',
			'desc'	=> '<font color=blue> ˵����</font>�������ɾ�������û���־',
			'html'	=> '<ul class="list_A list_80 cc"><li><input type="radio" value="1" name="group[deldiary]" $deldiary_Y />����</li><li><input type="radio" value="0" name="group[deldiary]" $deldiary_N />�ر�</li></ul>'
		),
		'delalbum'	=> array(
			'title'	=> '���ɾ��Ȩ��',
			'desc'	=> '<font color=blue> ˵����</font>�������ɾ�������û�������Ƭ',
			'html'	=> '<ul class="list_A list_80 cc"><li><input type="radio" value="1" name="group[delalbum]" $delalbum_Y />����</li><li><input type="radio" value="0" name="group[delalbum]" $delalbum_N />�ر�</li></ul>'
		),
		'delweibo'	=> array(
			'title'	=> '������ɾ��Ȩ��',
			'desc'	=> '<font color=blue> ˵����</font>�������ɾ�������û�������',
			'html'	=> '<ul class="list_A list_80 cc"><li><input type="radio" value="1" name="group[delweibo]" $delweibo_Y />����</li><li><input type="radio" value="0" name="group[delweibo]" $delweibo_N />�ر�</li></ul>'
		),
		'delactive'	=> array(
			'title'	=> '�ɾ��Ȩ��',
			'desc'	=> '<font color=blue> ˵����</font>�������ɾ���������Ļ',
			'html'	=> '<ul class="list_A list_80 cc"><li><input type="radio" value="1" name="group[delactive]" $delactive_Y />����</li><li><input type="radio" value="0" name="group[delactive]" $delactive_N />�ر�</li></ul>'
		),
		'recommendactive'	=> array(
			'title'	=> '��Ƽ�Ȩ��',
			'desc'	=> '<font color=blue> ˵����</font>������ɲ����������Ļ�Ƽ�',
			'html'	=> '<ul class="list_A list_80 cc"><li><input type="radio" value="1" name="group[recommendactive]" $recommendactive_Y />����</li><li><input type="radio" value="0" name="group[recommendactive]" $recommendactive_N />�ر�</li></ul>'
		)
	)
);
?>