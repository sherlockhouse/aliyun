<?php
defined('P_W') || exit('Forbidden');
$forumName = $db_bbsname;
InitGP(array('page','fid','q'));

$q = trim(str_replace("%","",$q));
(!is_numeric($page) || $page < 1) && $page = 1;
(!is_numeric($fid) || $page < 1) && $fid = 0;
$thisp = 0;

if($_G['allowsearch'] == '0') wap_msg('�����ڵ��û���û������Ȩ��','index.php');
function search($fid) {
	global $page, $thisp, $q, $db,$wap_perpage;
	/* ��������Service -- searchThreads ������������ */
	$searcherService = L::loadclass ( 'searcher', 'search' );
	$result = $searcherService->searchThreads($q,1, '','','',array(),$page,$wap_perpage,array());
	$start = ($page - 1) * $wap_perpage;
	if (is_array($result[1])) {
		foreach ($result[1] as $k => $v) {
			$id++;
			$v['anonymous'] && $v['author'] = $db_anonymousname;
			$v['id'] = $id;
			$hots[] = $v;
		}
	}
	return $hots;
}
wap_header();
require_once PrintWAP('search');
wap_footer();
?>
