<?php
!function_exists('readover') && exit('Forbidden');

$debatestand = 0;
if ($groupid != 'guest' && !$tpc_locked) {
	$debatestand = $db->get_value("SELECT standpoint FROM pw_debatedata WHERE pid='0' AND tid=".S::sqlEscape($tid)."AND authorid=".S::sqlEscape($winduid));
	$debatestand = (int)$debatestand;
	${'debate_'.$debatestand} = 'SELECTED';
}
if ($page == 1) {
	$debate = $db->get_one("SELECT obtitle,retitle,endtime,obvote,revote,obposts,reposts,umpire,umpirepoint,debater,judge FROM pw_debates WHERE tid=".S::sqlEscape($tid));
}
$stand = (int)S::getGP('stand');
if (!$uid && $read['replies'] > 0 && $stand > 0 && $stand < 4) {
	if ($stand == 3) {
		$rt = $db->get_one("SELECT COUNT(*) AS n FROM pw_debatedata WHERE pid>'0' AND tid=".S::sqlEscape($tid));
		$read['replies'] -= $rt['n'];
		$sqladd = " AND dd.standpoint IS NULL";
	} else {
		$rt = $db->get_one("SELECT COUNT(*) AS n FROM pw_debatedata WHERE pid>'0' AND tid=".S::sqlEscape($tid)." AND standpoint=".S::sqlEscape($stand));
		$read['replies'] = $rt['n'];
		$sqladd = " AND dd.standpoint=".S::sqlEscape($stand);
	}
	$urladd = "&stand=$stand";
	$count = $read['replies']+1;
	$numofpage = ceil($count/$db_readperpage);
	if ($page == 'e' || $page > $numofpage) {
		$page = $numofpage;
	}
}
$fieldadd .= ',dd.standpoint,dd.vote';
$tablaadd .= ' LEFT JOIN pw_debatedata dd ON t.pid=dd.pid';
$special = 'read_debate';

//for read page
$specialTips = '';
if($debate[judge]){
	$specialTips .= "�˱����ѽ��������� <b><a href=\"u.php?username=$debate[umpire]\">$debate[umpire]</a></b> ����:";
	if($debate[judge]==1){
		$specialTips .= "<b class=\"s3\">����ʤ</b>";
	}elseif($debate[judge]==3){
		$specialTips .= "<b class=\"s8\">����ʤ</b>";
	}else{
		$specialTips .= "<b class=\"s1\">ƽ��</b>";
	}
	$specialTips .= '����ѱ���:<a href="u.php?username='.$debate[debater].'" target="_blank"><b class="s7">'.$debate[debater].'</b></a>';
}elseif($debate['endtime'] < $timestamp){
	$specialTips .= '�˱����ѽ������ȴ������������۽��������';
}else{
	$debate['endtime'] = get_date($debate['endtime'],"Y-m-d H:i");
	$specialTips .= "���۽���ʱ�䣺{$debate[endtime]} �� ���У�<a href=\"u.php?username={$debate[umpire]}\" class=\"mr10 _cardshow\" target=\"_blank\" data-card-url=\"pw_ajax.php?action=smallcard&type=showcard&username=".rawurlencode($debate['umpire'])."\" data-card-key=$debate[umpire] >$debate[umpire]</a>";
}if($windid==$debate[umpire]){
	$specialTips .= ' <a id="judgedebate" href="javascript:void(0)" class="s4" onClick="sendmsg(\'pw_ajax.php?action=debate&do=judge&tid='.$tid."','','judgedebate')\"/>[���е���]</a>";
}

$tmpVotes = $debate[revote]+$debate[obvote];
$tmpob = $tmpVotes ? round($debate[obvote]/$tmpVotes,2)*100 : 0;
$tmpre = $tmpVotes ? round($debate[revote]/$tmpVotes,2)*100 : 0;
?>