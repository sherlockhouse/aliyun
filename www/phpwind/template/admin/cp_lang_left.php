<?php
!function_exists('readover') && exit('Forbidden');
require GetLang('purview');
$nav_manager = array(
	'name'		=> '��ʼ��',
	'items'		=> array(
		'manager',
		'rightset',
		'optimize',
		'diyoption',
		'advanced',
		'usercenter'=> array(
			'name'		=> '�û�����',
			'items'		=> array(
				'ucset',
				'ucapp',
				'uccredit',
				'ucnotify',
			),
		),
	),
);

$nav_left = array(
	'config' => array(
		'name' => 'ȫ��',
		'items' => array(
			'basic',
			'customnav',
			'regset'	=> array(
				'name'	=> 'ע������',
				'items'	=> array(
					'reg',
					'customfield',
					'invite',
					'propagateset'
				),
			),
			'credit',
			'member',
			'editer',
			/*
			'creditset'	=> array(
				'name'	=> '��������',
				'items'	=> array(
					'credit',
					'creditdiy',
					'creditchange',
				)
			),
			*/
			'attftp'	=> array(
				'name'	=>'��������',
				'items'	=>array(
					'att',
					'attachment',
					'attachrenew',
					'attachstats',
				)
			),
			'safe',
			'seoset',
			'messageset',
			'searcher',
			'email',
			'userpay',
			'help',
			//'wap',
			'sethtm',
			//'pcache',
		),
	),
	'consumer' => array(
		'name'	=> '�û�',
		'items'	=> array(
			'level',
			'upgrade',
			'usermanage',
			'banuser',
			'bansignature',
			'usercheck',
			'userstats',
			//'customcredit',
			'uptime',
		),
	),
	'contentforums'	=> array(
		'name'	=> '����',
		'items'	=> array(
			/*'contentmanage' => array(
				'name'	=> '���ݹ���',
				'items'=> array(*/
					'article',
					'photos_manage',
					'diary_manage',
					'groups_manage',
					//'app_share',
					'weibo_manage',
					'o_comments',
					'message',
					'reportmanage' => array(
						'name' => '�ٱ�����',
						'items' => array(
							'reportcontent',
							'reportremind'
						)
					),
					'draftset',
					'recycle',
				/*),
			),*/
			'contentcheck'	=> array(
				'name'	=> '�������',
				'items'	=> array(
					'tpccheck',
					'setbwd',
					'urlcheck',
				),
			),
			'rulelist'	=> array(
				'name'=>'���ݹ�������',
				'items'=>array(
				)
			),
			'tagset',
			//'pwcode',
			//'setform',
			//'overprint',
		),
	),
	'datacache'	=> array(
		'name'	=> '����',
		'items'	=> array(
			'bakup',
			'aboutcache',
			'record',
			'filecheck',
			'ipban',
			'viewtoday',
			'postindex',
			'datastate',
			'ystats',
			'creditlog',
			'tucool',
		),
	),
	'applicationcenter'	=> array(
		'name'	=> 'Ӧ��',
		'items'	=> array(
		'onlineapplication' => array(
				'name'	=> 'Ӧ������',
				'items' => array(
					'appset',
					'onlineapp',
					'i9p',
					'blooming',
					'taolianjie',
					'sinaweibo',
					'yunstatistics',
					'cnzz'
				),
			),
			'admincollege' => array(
				'name'	=> 'վ��ħ��',
				'items' => array(
				//	'products',    beta��������Ŀ¼
					'hacksource',
					'stylesource',
				),
			),
			'hackcenter',
			'hookcenter',
			//'appslist',
			'photos_set',
			'diary_set',
			'groups_set',
			//'app_share',
			'hot',
			'weibo_set',
			'medal_manage',
			'postcate',
			'activity',
			'topiccate',
			/*
			'basicapp' => array(
				'name' => '����Ӧ��',
				'items' => array(
					
				),
			),
			*/
		),
	),
	'markoperation'=> array(
		'name'	=> '��Ӫ',
		'items'	=> array(
			'setadvert',
			'stopic',
			'job',
			'announcement',
			'sendmail',
			'sendmsg',
			'present',
			'share',
			'plantodo',
			'team',
			'kmd_set',
//			'setads',
			//'sitemap',
		),
	),
	'cloudcomputing' => array(
		'name'  => '�ƹ���',
		'items'  => array(
			'yunbasic',
			'yuncheckserver',
			'yunsearch',
			'yundefend',
			'acloud',
			'acloudinfo',
			'cloudcaptcha',
			'authentication',
		),
	),
	
	'modelist' => array(
		'name'	=> 'ģʽ����',
		'items'=> array(
			'modeset',
			//'modestamp',
			//'modepush',
		),
	),
	'bbs' => array(
		'name'	=> '��̳ģʽ',
		'items'=> array(
			'bbssetting',
			'setforum',
			'interfacesettings',
			/*
			'forums' => array(
				'name'	=> '������',
				'items'=> array(
					'setforum',
					'uniteforum',
					'forumsell',
					'creathtm',
				),
			),
			*/
			'singleright',
			'setstyles',
			'rebang',
		),
	),
	'o' => array(
		'name'	=> '��������',
		'items'=> array(
			'o_global',
			'o_tags',
			'o_skin',
			'o_commend',
		)
	),
	'wap'		=> array(
			'name'  => 'WAP����', 
			'items' => array(
				'wapconfig',
				'wapsettings',
				'wapadvert',
			)
		),
);

/*��̬����ģʽ�˵�*/
if (isset($db_modes)) {
	foreach ($db_modes as $key => $value) {
		if ($value['ifopen'] && file_exists(R_P.'mode/'.$key.'/config/cp_lang_left.php')) {
			include R_P.'mode/'.$key.'/config/cp_lang_left.php';
		}
	}
}

/*��̬������չ�˵�*/
$extentPath = R_P.'require/extents/menu';
if ($fp = opendir($extentPath)) {
	while (($filename = readdir($fp))) {
		if($filename=='..' || $filename=='.') continue;
		$leftFile = $extentPath.'/'.$filename.'/cp_lang_left.php';
		if (file_exists($leftFile)) include $leftFile;
	}
}

?>