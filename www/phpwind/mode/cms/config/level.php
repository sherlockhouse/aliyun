<?php
!defined('P_W') && exit('Forbidden');
return array(
	'title' => '����Ȩ��',
	'field' => array(
		'cms_post' => array(
			'name' => '��������',
			'type' => 'radio',
			'value'=> array(
				'1'=>'����',
				'0'=>'�ر�'
			),
			'descrip' => '��������ͨ��Ա�û�����û�����Ͷ�壬����Ȩ���û�����û����Է�������'
		),

		'cms_replypost' => array(
			'name' => '��������',
			'type' => 'radio',
			'value'=> array(
				'1'=>'����',
				'0'=>'�ر�'
			),
			'descrip' => '�����󣬸��û�����Զ����½�������'
		),
	),
);
?>