<?php
/**
 * �����ʽ����
 * 
 * ���������
 * <code>
 *   name �����ʽ��
 *   banner ���ͼƬ��Ե�ַ
 *   layout_set �����ʽcss����
 *     bgcolor ������ɫ
 *     areabgcolor ���鱳����ɫ
 *     fontcolor ������ɫ
 *     navfontcolor �����ļ���ɫ
 *     navbgcolor ����������ɫ
 *     othercss ������ʽ
 * </code>
 * 
 * @package STopic
 */

!defined('P_W') && exit('Forbidden');
return array(
	'name'	=>'ĸӤ_��',
	'banner'=>'apps/stopic/data/style/baby_org/banner.jpg',
	'layout_set' => array(
		'bgcolor'		=> '#ffdf98',
		'areabgcolor'	=> '#ffffff',
		'fontcolor'		=> '#c58809',
		'navfontcolor'	=> '#ffffff',
		'navbgcolor'	=> '#e1a521',
		'othercss'		=> '.wrap{width:960px;margin:0 auto 0;overflow:hidden;}/*ר�����ݿ��*/
#main{padding:10px;}/*ר���ڱ߾�*/
.zt_nav li{float:left;line-height:35px; font-size:14px;margin:0 10px; white-space:nowrap;}/*������ʽ*/
.itemDraggable{margin-top:10px;overflow:hidden;}/*ģ����߿�*/
.itemDraggable .itemHeader{background:#f7f7f7 url(apps/stopic/data/style/baby_org/h-orange.png) right 0 repeat-x;padding:6px 10px 5px; font-weight:700;color:#fff;border:1px solid #e9be62;}/*������*/
.itemDraggable .itemContent{padding:4px 10px;border:1px solid #eaebe6;}/*ģ���ڱ߾�*/
.itemDraggable .itemContent li{line-height:24px;}/*�б��и�*/
'
	),
);
?>