<?php
! function_exists ( 'readover' ) && exit ( 'Forbidden' );
/**
 * ����˵��
 * ��ά���� array('����λ��1'=> array(
 * '������1'=>array('�ļ�·��=>'1','����������'=>'classname1'),
 * '������2'=>array('�ļ�·��=>'2','����������'=>'classname2'),
 * '����λ��2'=> array(
 * '������3'=>array('�ļ�·��=>'3','����������'=>'classname3'),
 * '������4'=>array('�ļ�·��=>'4','����������'=>'classname4'),
 * );
 * 1,����λ��1:��ʾչʾ��λ�ã���right_search ��չʾ��������������ұ�,һ��λ�ÿɵ��ö����չ����
 * 2,������1:���õ����Ʊ�ʶ��
 * 3,�ļ�·��:��ʾ������չ��������ļ�·��
 * 4,����������:PW_classname1Searcher ע��ֻ��Ҫ�ṩclassname1 ��PW_dianpuSearcher��classnameΪdianpu
 */
$configs = array ();

if ($db_modes ['dianpu'] ['ifopen']) {
	$configs ['right_search'] = array ('dianpu_dianpu' => array ('path' => R_P . 'mode/dianpu/lib/searcher/dianpusearcher.class.php', 'classname' => 'dianpu' ) );
}
