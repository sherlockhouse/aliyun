<?php
/**
 * ���actions/job/�µĸ��ֲ�������ļ�
 * Ҫ����һ�����ܣ����ڴ�����ļ�����������ϣ�Ȼ���ڶ�Ӧ�ļ���������Ȩ�޿���.
 * @package  job.php
 */
define('SCR', 'job');
if (isset($_GET['action']) && in_array($_GET['action'], array('mutiupload', 'mutiuploadphoto', 'uploadicon'))) {
	define('CK', 1);
}
require_once ('global.php');
require_once (R_P . 'require/functions.php');

S::gp(array(
	'action'
));
$whiteActions = array(
	'previous', //���������һҳ����һҳ 
	'taglist', //tag �б�
	'tag', //tag�����������б�
	//�����ƶ���link.php���������ʱ����������ҳ�滺�棩

	'redirect', //չ�����������һҳ����һҳ
	'sign', //����Ѷ�
	'preview', //���ӷ���Ԥ��
	'mutiupload', //�฽���ϴ�-���Ӹ���
	'mutiuploadphoto', //�฽���ϴ�-���
	'uploadicon', //�ϴ�ͷ��
	'download', //���ظ���
	'showimg', //��ȡ������һ����Զ�̸�����
	'deldownfile', //ɾ������
	'viewtody', //���յ��û�Ա
	'buytopic', //���ӹ���
	'vote', //ͶƱ
	'reward', //������
	'endreward', //�������Ͳ���
	'rewardmsg', //������->��������Ϣ
	'topost', //����ҳ�����pid��ת��ָ��ҳ��
	'birth', //��ȡ���ջ�Ա
	'erasecookie', //���cookie
	'pcexport', //�Ź������������б�
	'pcjoin', //�Ź�������
	'activity',//�
	'attachbuy', //���۸���
	'pweditor',
	'pwschools',
	'tofloor',//¥��ֱ��
	'remotedownload', //Զ������
	'endrobbuild' // ������¥
);
if (in_array($action, $whiteActions)) {
	require S::escapePath(R_P . 'actions/job/' . $action . '.php');
} else {
	Showmsg('undefined_action');
}

function fseeks($fp, $dbtdsize, $seed) {
	$break = $num = 0;
	while ($break != 1 && $num < $seed) {
		$num++;
		$sdata = fread($fp, $dbtdsize);
		$sdb = explode("\t", $sdata);
		$sdbnext = $sdb[2] * $dbtdsize;
		if ($sdbnext != 'NULL') {
			fseek($fp, $sdbnext, SEEK_SET);
		} else {
			$break = 1;
		}
		$todayshow[] = $sdata;
	}
	return $todayshow;
}

function return_value($tid, $rw_a_name, $rw_a_val) {
	global $db, $pw_posts, $authorid, $author, $onlineip, $forum, $fid, $credit;
	
	if ($rw_a_val < 1) {
		return;
	}
	$p_a = $u_a = array();
	$query = $db->query("SELECT pid,author,authorid FROM $pw_posts WHERE tid=" . S::sqlEscape($tid) . " AND ifreward='0' AND authorid!=" . S::sqlEscape($authorid) . " GROUP BY authorid ORDER BY postdate ASC LIMIT $rw_a_val");
	while ($user = $db->fetch_array($query)) {
		$credit->addLog('reward_active', array(
			$rw_a_name => 1
		), array(
			'uid' => $user['authorid'],
			'username' => $user['author'],
			'ip' => $onlineip,
			'fname' => $forum[$fid]['name']
		));
		$p_a[] = $user['pid'];
		$u_a[] = $user['authorid'];
		$rw_a_val--;
	}
	//$p_a && $db->update("UPDATE $pw_posts SET ifreward='1' WHERE pid IN(" . S::sqlImplode($p_a) . ')');
	$p_a && pwQuery::update($pw_posts, 'pid IN(:pid)', array($p_a), array('ifreward' => '1'));
	$u_a && $credit->setus($u_a, array(
		$rw_a_name => 1
	), false);
	if ($rw_a_val > 0) {
		$credit->addLog('reward_return', array(
			$rw_a_name => $rw_a_val
		), array(
			'uid' => $authorid,
			'username' => $author,
			'ip' => $onlineip,
			'fname' => $forum[$fid]['name']
		));
		$credit->set($authorid, $rw_a_name, $rw_a_val, false);
	}
}

function getuserdb($filename, $offset) {
	global $db_olsize;
	if (!$offset || $offset % ($db_olsize + 1) != 0) {
		return false;
	} else {
		$fp = fopen($filename, "rb");
		flock($fp, LOCK_SH);
		fseek($fp, $offset);
		$Checkdata = fread($fp, $db_olsize);
		fclose($fp);
		return $Checkdata;
	}
}

function checkCreditLimit($creditlimit) {
	global $winddb, $winduid, $db;
	$creditlimit = unserialize($creditlimit);
	foreach ((array)$creditlimit as $key => $value) {
		if (in_array($key, array(
			'money',
			'rvrc',
			'credit',
			'currency'
		))) {
			$key == 'rvrc' && $winddb[$key] = floor($winddb[$key]/10);
			$winddb[$key] < $value && Showmsg('job_vote_creditlimit');
		} else {
			$user_credit = $db->get_value("SELECT value FROM pw_membercredit WHERE uid=" . S::sqlEscape($winduid) . " AND cid=" . S::sqlEscape($key));
			$user_credit < $value && Showmsg('job_vote_creditlimit');
		}
	}
}

function getPcviewdata($pcinfo, $pctype) {
	global $fielddb;
	foreach (explode("{|}", $pcinfo) as $val) {
		if (strpos($val, 'topic[') !== false || strpos($val, 'postcate[') !== false) {
			$name = $value = $type = $fieldid = '';
			$fieldb = array();
			$val = str_replace('&#41;', ')', $val);
			list($name, $value, $type) = explode("(|)", $val);
			
			if ($pctype == 'topic') {
				preg_match("/topic\[(\d+)\]/", $name, $fieldata);
				$fieldid = $fieldata['1'];
				if ($fieldid) {
					if ($fieldid != $newid) {
						strpos($type, 'calendar_') !== false && $value = PwStrtoTime($value);
						$pcdb[$fielddb[$fieldid]] = $value;
					} elseif ($fieldid == $newid) {
						$pcdb[$fielddb[$fieldid]] .= ',' . $value;
					}
				}
				$newid = $fieldid;
			} elseif ($pctype == 'postcate') {
				preg_match("/postcate\[(.+?)\]/", $name, $fieldata);
				$fieldname = $fieldata['1'];
				if ($fieldname) {
					if ($fielddb[$fieldname] != $newid) {
						strpos($type, 'calendar_') !== false && $value = PwStrtoTime($value);
						$pcdb[$fieldname] = $value;
					} elseif ($fielddb[$fieldname] == $newid) {
						$pcdb[$fieldname] .= ',' . $value;
					}
					$newid = $fielddb[$fieldname];
				}
			}
		}
	}
	
	return $pcdb;
}
?>