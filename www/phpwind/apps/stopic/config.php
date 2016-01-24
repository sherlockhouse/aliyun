<?php
/**
 * ר�������ļ�
 * 
 * ���������
 * <code>
 *   bgUploadPath ����ͼƬ�ϴ�·��
 *   bgBaseUrl ����ͼƬ����url
 *   bgDefalutPath ����ͼƬĬ�ϴ��Ŀ¼
 *   bgDefalutUrl ����ͼƬĬ��url
 *   layoutPath �������ݴ��Ŀ¼
 *   layoutBaseUrl ���ֻ���url
 *   stylePath �����ʽ���ݴ��Ŀ¼
 *   styleBanner �����ʽ���ͼƬ�ļ���
 *   styleMiniPreview �����ʽ����ͼ�ļ���
 *   styleBaseUrl �����ʽ����url
 *   layoutConfig ��������
 *   layoutTypes ���������б�
 *   layout_set Ĭ�Ϸ����ʽ��css����
 *   htmlSuffix ר�Ᵽ���ļ�����չ��
 *   htmlDir ר�Ᵽ��Ŀ¼
 *   htmlUrl ר��url
 *   blockTypes ģ�������б�
 * </code>
 * 
 * @package STopic
 */

!defined('P_W') && exit('Forbidden');

return array(
	"bgUploadPath" => R_P . (isset($GLOBALS['db_attachname']) && '' != $GLOBALS['db_attachname'] ? $GLOBALS['db_attachname'] : 'attachment') . "/stopic/",
	"bgBaseUrl" => $GLOBALS['db_bbsurl'] . "/attachment/stopic/",

	"bgDefalutPath" => A_P . "data/uploadbg/",
	"bgDefalutUrl" => $GLOBALS['db_bbsurl'] . "/apps/stopic/data/uploadbg/",

	"layoutPath" => A_P."data/layout/",
	"layoutBaseUrl" => $GLOBALS['db_bbsurl'] . "/apps/stopic/data/layout/",

	"stylePath" => A_P."data/style/",
	"styleBanner" => "banner.jpg",
	"stylePreview" => "preview.jpg",
	"styleMiniPreview" => "mini_preview.jpg",
	"styleBaseUrl"	=> $GLOBALS['db_bbsurl'] . "/apps/stopic/data/style/",

	"layoutConfig" => array(
		"logo" => "logo.png",
		"html" => "layout.htm",
	),
	"layoutTypes" => array(
		"type1v0" => "ֱ��",
		"type1v1" => "1:1",
		"type1v2" => "1:2",
		"type2v1" => "2:1",
		"type1v1v1" => "1:1:1",
	),
	"layout_set" => array(
		'bannerurl'		=> $GLOBALS['db_bbsurl'] . '/apps/stopic/data/style/wedding_pink/banner.jpg',
		'bgcolor'		=> '#cd6587',
		'areabgcolor'	=> '#ffffff',
		'fontcolor'		=> '#e46882',
		'navfontcolor'	=> '#ffffff',
		'navbgcolor'	=> '#ce5683',
		"othercss"		=> <<<EOT
.wrap{width:960px;margin:0 auto 0;overflow:hidden;}/*ר�����ݿ��*/
#main{padding:10px;}/*ר���ڱ߾�*/
.zt_nav li{float:left;line-height:35px; font-size:14px;margin:0 10px; white-space:nowrap;}/*������ʽ*/
.itemDraggable{border:1px solid #eaebe6;margin-bottom:10px;overflow:hidden;}/*ģ����߿�*/
.itemDraggable .itemHeader{background:#ce5683 url(apps/stopic/data/style/wedding_pink/h-pink.png) right 0 repeat-x;padding:4px 10px; font-weight:700;color:#fff;}/*������*/
.itemDraggable .itemContent{padding:4px 10px;}/*ģ���ڱ߾�*/
.itemDraggable .itemContent li{line-height:24px;}/*�б��и�*/
EOT
	),

	'htmlSuffix'=>'.html',

	"htmlDir" => R_P.('' != $GLOBALS['db_stopicdir'] ? $GLOBALS['db_stopicdir'] : $GLOBALS['db_htmdir'].'/stopic'),
	"htmlUrl" => $GLOBALS['db_bbsurl'].'/'.('' != $GLOBALS['db_stopicdir'] ? $GLOBALS['db_stopicdir'] : $GLOBALS['db_htmdir'].'/stopic'),
	
	"blockTypes" => array(
		"banner" => "ͷ�����",
		"nvgt" => "����",
		"thrd" => "�����б�",
		"thrdSmry" => "����ժҪ",
		"pic" => "ͼƬ",
		"picTtl" => "ͼƬ������",
		"picArtcl" => "ͼ�Ļ���",
		"picPlyr" => "ͼƬ������",
		"spclTpc" => "��������",
		"html" => "�Զ������",
		"comment" => "ר������",
	),
);

?>