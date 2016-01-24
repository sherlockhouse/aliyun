<?php

!defined('PW_UPLOAD') && exit('Forbidden');

$pcfield = array(
	'1' => array(
		'fieldid' => '1',
		'name' => '���',
		'fieldname' => 'pctype',
		'pcid' => '1',
		'vieworder' => '1',
		'type' => 'radio',
		'rules' => array(
			'0' => '1=������',
			'1' => '2=������',
			'2' => '3=������',
			'3' => '4=�ҵ���',
			'4' => '5=��װ��',
			'5' => '6=����ԭ��',
			'6' => '7=ʳƷ��',
			'7' => '8=����',
		),
		'ifable' => '1',
		'ifsearch' => '1',
		'ifasearch' => '1',
		'threadshow' => '1',
		'ifmust' => '0',
		'ifdel' => '1',
		'textsize' => '0',
		'descrip' => '',
	),
	'2' => array(
		'fieldid' => '2',
		'name' => '����ʱ��',
		'fieldname' => 'begintime',
		'pcid' => '1',
		'vieworder' => '2',
		'type' => 'calendar',
		'rules' => '',
		'ifable' => '1',
		'ifsearch' => '1',
		'ifasearch' => '1',
		'threadshow' => '0',
		'ifmust' => '1',
		'ifdel' => '1',
		'textsize' => '0',
		'descrip' => '',
	),
	'3' => array(
		'fieldid' => '3',
		'name' => '��ֹʱ��',
		'fieldname' => 'endtime',
		'pcid' => '1',
		'vieworder' => '3',
		'type' => 'calendar',
		'rules' => '',
		'ifable' => '1',
		'ifsearch' => '1',
		'ifasearch' => '1',
		'threadshow' => '1',
		'ifmust' => '1',
		'ifdel' => '1',
		'textsize' => '0',
		'descrip' => '',
	),
	'4' => array(
		'fieldid' => '4',
		'name' => '��Ʒ����',
		'fieldname' => 'limitnum',
		'pcid' => '1',
		'vieworder' => '4',
		'type' => 'number',
		'rules' => array(
			'minnum' => '1',
			'maxnum' => '100',
		),
		'ifable' => '1',
		'ifsearch' => '0',
		'ifasearch' => '0',
		'threadshow' => '1',
		'ifmust' => '0',
		'ifdel' => '1',
		'textsize' => '0',
		'descrip' => '���������ƣ������գ�',
	),
	'5' => array(
		'fieldid' => '5',
		'name' => '��������',
		'fieldname' => 'objecter',
		'pcid' => '1',
		'vieworder' => '5',
		'type' => 'radio',
		'rules' => array(
			'0' => '1=�����û�',
			'1' => '2=������',
		),
		'ifable' => '1',
		'ifsearch' => '0',
		'ifasearch' => '1',
		'threadshow' => '0',
		'ifmust' => '0',
		'ifdel' => '1',
		'textsize' => '0',
		'descrip' => '',
	),
	'6' => array(
		'fieldid' => '6',
		'name' => '�Ź���',
		'fieldname' => 'price',
		'pcid' => '1',
		'vieworder' => '7',
		'type' => 'text',
		'rules' => '',
		'ifable' => '1',
		'ifsearch' => '0',
		'ifasearch' => '0',
		'threadshow' => '0',
		'ifmust' => '0',
		'ifdel' => '1',
		'textsize' => '0',
		'descrip' => '',
	),
	'7' => array(
		'fieldid' => '7',
		'name' => 'Ѻ��',
		'fieldname' => 'deposit',
		'pcid' => '1',
		'vieworder' => '8',
		'type' => 'text',
		'rules' => '',
		'ifable' => '1',
		'ifsearch' => '0',
		'ifasearch' => '0',
		'threadshow' => '0',
		'ifmust' => '0',
		'ifdel' => '1',
		'textsize' => '0',
		'descrip' => '������������֧��Ѻ��',
	),
	'8' => array(
		'fieldid' => '8',
		'name' => '֧����ʽ',
		'fieldname' => 'payway',
		'pcid' => '1',
		'vieworder' => '9',
		'type' => 'radio',
		'rules' => array(
			'0' => '1=֧����֧��',
			'1' => '2=�ֽ�֧��',
		),
		'ifable' => '1',
		'ifsearch' => '0',
		'ifasearch' => '1',
		'threadshow' => '0',
		'ifmust' => '0',
		'ifdel' => '1',
		'textsize' => '0',
		'descrip' => '',
	),
	'9' => array(
		'fieldid' => '9',
		'name' => '��ϵ��',
		'fieldname' => 'contacter',
		'pcid' => '1',
		'vieworder' => '10',
		'type' => 'text',
		'rules' => '',
		'ifable' => '1',
		'ifsearch' => '1',
		'ifasearch' => '0',
		'threadshow' => '0',
		'ifmust' => '1',
		'ifdel' => '1',
		'textsize' => '0',
		'descrip' => '',
	),
	'10' => array(
		'fieldid' => '10',
		'name' => '�绰',
		'fieldname' => 'tel',
		'pcid' => '1',
		'vieworder' => '11',
		'type' => 'text',
		'rules' => '',
		'ifable' => '1',
		'ifsearch' => '0',
		'ifasearch' => '0',
		'threadshow' => '0',
		'ifmust' => '0',
		'ifdel' => '1',
		'textsize' => '4',
		'descrip' => '',
	),
	'11' => array(
		'fieldid' => '11',
		'name' => '-',
		'fieldname' => 'phone',
		'pcid' => '1',
		'vieworder' => '11',
		'type' => 'text',
		'rules' => '',
		'ifable' => '1',
		'ifsearch' => '0',
		'ifasearch' => '0',
		'threadshow' => '0',
		'ifmust' => '0',
		'ifdel' => '1',
		'textsize' => '14',
		'descrip' => '���磺0571-12345678',
	),
	'12' => array(
		'fieldid' => '12',
		'name' => '�ֻ�',
		'fieldname' => 'mobile',
		'pcid' => '1',
		'vieworder' => '12',
		'type' => 'text',
		'rules' => '',
		'ifable' => '1',
		'ifsearch' => '0',
		'ifasearch' => '0',
		'threadshow' => '0',
		'ifmust' => '1',
		'ifdel' => '1',
		'textsize' => '0',
		'descrip' => '',
	),
	'13' => array(
		'fieldid' => '13',
		'name' => 'ͼƬ�ϴ�',
		'fieldname' => 'pcattach',
		'pcid' => '1',
		'vieworder' => '13',
		'type' => 'upload',
		'rules' => '',
		'ifable' => '1',
		'ifsearch' => '0',
		'ifasearch' => '0',
		'threadshow' => '0',
		'ifmust' => '0',
		'ifdel' => '1',
		'textsize' => '0',
		'descrip' => '',
	),
	'14' => array(
		'fieldid' => '14',
		'name' => '���',
		'fieldname' => 'pctype',
		'pcid' => '2',
		'vieworder' => '1',
		'type' => 'radio',
		'rules' => array(
			'0' => '1=����',
			'1' => '2=�۲� ',
			'2' => '3=���',
			'3' => '4=����',
			'4' => '5=�տ�',
			'5' => '6=����',
		),
		'ifable' => '1',
		'ifsearch' => '1',
		'ifasearch' => '1',
		'threadshow' => '1',
		'ifmust' => '0',
		'ifdel' => '1',
		'textsize' => '0',
		'descrip' => '',
	),
	'15' => array(
		'fieldid' => '15',
		'name' => '����ʱ��',
		'fieldname' => 'begintime',
		'pcid' => '2',
		'vieworder' => '2',
		'type' => 'calendar',
		'rules' => '',
		'ifable' => '1',
		'ifsearch' => '1',
		'ifasearch' => '1',
		'threadshow' => '0',
		'ifmust' => '1',
		'ifdel' => '1',
		'textsize' => '0',
		'descrip' => '',
	),
	'16' => array(
		'fieldid' => '16',
		'name' => '����ʱ��',
		'fieldname' => 'endtime',
		'pcid' => '2',
		'vieworder' => '3',
		'type' => 'calendar',
		'rules' => '',
		'ifable' => '1',
		'ifsearch' => '1',
		'ifasearch' => '1',
		'threadshow' => '1',
		'ifmust' => '1',
		'ifdel' => '1',
		'textsize' => '0',
		'descrip' => '',
	),
	'17' => array(
		'fieldid' => '17',
		'name' => '��ص�',
		'fieldname' => 'address',
		'pcid' => '2',
		'vieworder' => '4',
		'type' => 'text',
		'rules' => '',
		'ifable' => '1',
		'ifsearch' => '0',
		'ifasearch' => '1',
		'threadshow' => '1',
		'ifmust' => '0',
		'ifdel' => '1',
		'textsize' => '0',
		'descrip' => '',
	),
	'18' => array(
		'fieldid' => '18',
		'name' => '��������',
		'fieldname' => 'limitnum',
		'pcid' => '2',
		'vieworder' => '5',
		'type' => 'number',
		'rules' => array(
			'minnum' => '1',
			'maxnum' => '100',
		),
		'ifable' => '1',
		'ifsearch' => '0',
		'ifasearch' => '0',
		'threadshow' => '0',
		'ifmust' => '0',
		'ifdel' => '1',
		'textsize' => '0',
		'descrip' => '�ˣ������ƣ������գ�',
	),
	'19' => array(
		'fieldid' => '19',
		'name' => '��������',
		'fieldname' => 'objecter',
		'pcid' => '2',
		'vieworder' => '6',
		'type' => 'radio',
		'rules' => array(
			'0' => '1=�����û�',
			'1' => '2=������',
		),
		'ifable' => '1',
		'ifsearch' => '0',
		'ifasearch' => '1',
		'threadshow' => '0',
		'ifmust' => '0',
		'ifdel' => '1',
		'textsize' => '0',
		'descrip' => '',
	),
	'20' => array(
		'fieldid' => '20',
		'name' => '�Ա�����',
		'fieldname' => 'gender',
		'pcid' => '2',
		'vieworder' => '7',
		'type' => 'radio',
		'rules' => array(
			'0' => '1=ȫ��',
			'1' => '2=������',
			'2' => '3=��Ů��',
		),
		'ifable' => '1',
		'ifsearch' => '0',
		'ifasearch' => '1',
		'threadshow' => '0',
		'ifmust' => '0',
		'ifdel' => '1',
		'textsize' => '0',
		'descrip' => '',
	),
	'21' => array(
		'fieldid' => '21',
		'name' => '�����',
		'fieldname' => 'price',
		'pcid' => '2',
		'vieworder' => '8',
		'type' => 'text',
		'rules' => '',
		'ifable' => '1',
		'ifsearch' => '0',
		'ifasearch' => '0',
		'threadshow' => '0',
		'ifmust' => '0',
		'ifdel' => '1',
		'textsize' => '0',
		'descrip' => 'Ԫ/��',
	),
	'22' => array(
		'fieldid' => '22',
		'name' => '֧����ʽ',
		'fieldname' => 'payway',
		'pcid' => '2',
		'vieworder' => '9',
		'type' => 'radio',
		'rules' => array(
			'0' => '1=֧����֧��',
			'1' => '2=�ֽ�֧��',
		),
		'ifable' => '1',
		'ifsearch' => '0',
		'ifasearch' => '1',
		'threadshow' => '0',
		'ifmust' => '0',
		'ifdel' => '1',
		'textsize' => '0',
		'descrip' => '',
	),
	'23' => array(
		'fieldid' => '23',
		'name' => '��ϵ��',
		'fieldname' => 'contacter',
		'pcid' => '2',
		'vieworder' => '10',
		'type' => 'text',
		'rules' => '',
		'ifable' => '1',
		'ifsearch' => '0',
		'ifasearch' => '0',
		'threadshow' => '0',
		'ifmust' => '1',
		'ifdel' => '1',
		'textsize' => '0',
		'descrip' => '',
	),
	'24' => array(
		'fieldid' => '24',
		'name' => '�绰',
		'fieldname' => 'tel',
		'pcid' => '2',
		'vieworder' => '11',
		'type' => 'text',
		'rules' => '',
		'ifable' => '1',
		'ifsearch' => '0',
		'ifasearch' => '0',
		'threadshow' => '0',
		'ifmust' => '0',
		'ifdel' => '1',
		'textsize' => '4',
		'descrip' => '',
	),
	'25' => array(
		'fieldid' => '25',
		'name' => '-',
		'fieldname' => 'phone',
		'pcid' => '2',
		'vieworder' => '11',
		'type' => 'text',
		'rules' => '',
		'ifable' => '1',
		'ifsearch' => '0',
		'ifasearch' => '0',
		'threadshow' => '0',
		'ifmust' => '0',
		'ifdel' => '1',
		'textsize' => '14',
		'descrip' => '���磺0571-123456778',
	),
	'26' => array(
		'fieldid' => '26',
		'name' => '�ֻ�',
		'fieldname' => 'mobile',
		'pcid' => '2',
		'vieworder' => '12',
		'type' => 'text',
		'rules' => '',
		'ifable' => '1',
		'ifsearch' => '0',
		'ifasearch' => '0',
		'threadshow' => '0',
		'ifmust' => '1',
		'ifdel' => '1',
		'textsize' => '0',
		'descrip' => '',
	),
	'27' => array(
		'fieldid' => '27',
		'name' => 'ͼƬ�ϴ�',
		'fieldname' => 'pcattach',
		'pcid' => '2',
		'vieworder' => '13',
		'type' => 'upload',
		'rules' => '',
		'ifable' => '1',
		'ifsearch' => '0',
		'ifasearch' => '0',
		'threadshow' => '0',
		'ifmust' => '0',
		'ifdel' => '1',
		'textsize' => '0',
		'descrip' => '',
	),
	'28' => array(
		'fieldid' => '28',
		'name' => '����',
		'fieldname' => 'wangwang',
		'pcid' => '1',
		'vieworder' => '14',
		'type' => 'text',
		'rules' => '',
		'ifable' => '1',
		'ifsearch' => '0',
		'ifasearch' => '0',
		'threadshow' => '0',
		'ifmust' => '0',
		'ifdel' => '1',
		'textsize' => '0',
		'descrip' => '',
	),
	'29' => array(
		'fieldid' => '29',
		'name' => '�г���',
		'fieldname' => 'mprice',
		'pcid' => '1',
		'vieworder' => '6',
		'type' => 'text',
		'rules' => '',
		'ifable' => '1',
		'ifsearch' => '0',
		'ifasearch' => '0',
		'threadshow' => '0',
		'ifmust' => '1',
		'ifdel' => '1',
		'textsize' => '0',
		'descrip' => '',
	),
	'30' => array(
		'fieldid' => '30',
		'name' => 'QQ',
		'fieldname' => 'qq',
		'pcid' => '1',
		'vieworder' => '15',
		'type' => 'text',
		'rules' => '',
		'ifable' => '1',
		'ifsearch' => '0',
		'ifasearch' => '0',
		'threadshow' => '0',
		'ifmust' => '0',
		'ifdel' => '0',
		'textsize' => '0',
		'descrip' => '',
	),
);
foreach ($pcfield as $key => $value){

	is_array($value['rules']) && $value['rules'] = serialize($value['rules']);
	$db->update("REPLACE INTO pw_pcfield SET ".pwSqlSingle(array(
		'fieldid'		=> $value['fieldid'],		'name'	=> $value['name'],
		'fieldname'		=> $value['fieldname'],		'pcid'	=> $value['pcid'],
		'vieworder'		=> $value['vieworder'],		'type'	=> $value['type'],
		'rules'			=> $value['rules'],			'ifable'=> $value['ifable'],
		'ifsearch'		=> $value['ifsearch'],		'ifasearch'	=> $value['ifasearch'],
		'threadshow'	=> $value['threadshow'],	'ifmust'	=> $value['ifmust'],
		'ifdel'			=> $value['ifdel'],			'textsize'	=> $value['textsize'],
		'descrip'		=> $value['descrip']
	)));
}
?>