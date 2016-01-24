<?php
!defined('P_W') && exit('Forbidden');

/**
 * ��ǩ�����
 * @package  PW_MemberTagsService
 * @author phpwind @2010-12-27
 */
class PW_MemberTagsService {
	var $_hour = 3600;
	var $_timestamp = null;

	
	function PW_MemberTagsService() {
		global $timestamp;
		$this->_timestamp = $timestamp;
		$this->_memberTagDbFile = D_P . 'data/bbscache/membertagsdata.php';
	}
	
	/**
	 * ��ӱ�ǩ
	 * 
	 * @param array $fieldsData �������飬�����ݿ��ֶ�Ϊkey
	 * @return int 
	 */
	function addTags($fieldsData) {
		$fieldsData = $this->checkFieldsData($fieldsData);
		if (!S::isArray($fieldsData)) return false;
		$memberTagsDb = $this->_getMemberTagsDB();
		return $memberTagsDb->insert($fieldsData);
	}
	
	/**
	 * ���±�ǩ
	 * 
	 * @param array $fieldsData �������飬�����ݿ��ֶ�Ϊkey
	 * @return boolean 
	 */
	function updateTags($fieldsData,$tagid) {
		$tagid = intval($tagid);
		$fieldsData = $this->checkFieldsData($fieldsData);
		if ($tagid < 1 || !S::isArray($fieldsData)) return false;
		$memberTagsDb = $this->_getMemberTagsDB();
		return (bool)$memberTagsDb->update($fieldsData,$tagid);
	}
	
	/**
	 * ���±�ǩʹ������
	 * 
	 * @param int $tagid ��ǩ
	 * @return boolean
	 */
	function updateNumByTagId($tagid,$num) {
		$tagid = intval($tagid);
		$num = intval($num);
		if ($tagid < 1 || !$num) return false;
		$memberTagsDb = $this->_getMemberTagsDB();
		return (bool)$memberTagsDb->updateNumByTagId($tagid,$num);
	}

	/**
	 * ����ɾ����ǩ
	 * 
	 * @param array $tagids
	 * @return boolean
	 */
	function deleteTagsByTagIds($tagids) {
		if(!S::isArray($tagids)) return false;
		$memberTagsDb = $this->_getMemberTagsDB();
		if ($memberTagsDb->deleteTagsByTagIds($tagids)) return (bool)$this->deleteMemberTagsByTagId($tagids);
		return false;
	}
	
	/**
	 * ���ݱ�ǩ����ȡ��ǩ��Ϣ
	 * 
	 * @param string $tagName
	 * @return array
	 */
	function getTagsByTagName($tagName) {
		$tagName = trim($tagName);
		if ($tagName == '') return array();
		$memberTagsDb = $this->_getMemberTagsDB();
		return $memberTagsDb->getTagsByTagName($tagName);
	}	
	
	/**
	 * ����û���ǩ
	 * 
	 * @param array $fieldsData ��������
	 */
	function addMemberTags($fieldsData) {
		$fieldsData = $this->checkRelationsFieldsData($fieldsData);
		if (!S::isArray($fieldsData)) return false;
		$memberTagsRelationsDB = $this->_getMemberTagsRelationsDB();
		$memberTagsRelationsDB->insertMemberTags($fieldsData);
		return true;
	}
	
	/**
	 * ͳ���û���ǩ
	 * 
	 * @param int $userid
	 * @return int 
	 */
	function countTagsByUid($userid) {
		$userid = intval($userid);
		if ($userid <= 0) return false;
		$memberTagsRelationsDB = $this->_getMemberTagsRelationsDB();
		return $memberTagsRelationsDB->countTagsByUid($userid);
	}
	
	/**
	 * ���ݱ�ǩIDɾ���û���ǩ
	 * 
	 * @param int $tagid  ��ǩID
	 * @param int $userid
	 * @return boolean
	 */
	function deleteMemberTags($tagid,$userid) {
		$tagid = intval($tagid);
		$userid = intval($userid);
		if ($tagid < 1 || $userid < 1) return false;
		$memberTagsRelationsDB = $this->_getMemberTagsRelationsDB();
		return (bool)$memberTagsRelationsDB->deleteMemberTags($tagid,$userid);
	}
	
	/**
	 * ���ݱ�ǩtagids����ɾ���û���ǩ
	 * 
	 * @param array $tagids  ��ǩID����
	 * @param int $userid
	 * @return boolean
	 */
	function deleteMemberTagsByTagId($tagids) {
		if(!S::isArray($tagids)) return false;
		$memberTagsRelationsDB = $this->_getMemberTagsRelationsDB();
		return (bool)$memberTagsRelationsDB->deleteMemberTagsByTagId($tagids);
	}
	
	/**
	 * ����uid��tagid����
	 * 
	 * @param int $tagid
	 * @param int $userid
	 * @return array
	 */
	function getTagsByTagidAndUid($tagid,$userid) {
		$tagid = intval($tagid);
		$userid = intval($userid);
		if ($tagid < 1 || $userid < 1) return array();
		$memberTagsRelationsDB = $this->_getMemberTagsRelationsDB();
		return $memberTagsRelationsDB->getTagsByTagidAndUid($tagid,$userid);
	}
	
	/**
	 * ����uid��ȡ��ǩids
	 * 
	 * @param int $uid
	 * @return array
	 */
	function getMemberTagIdsByUid($uid) {
		$uid = intval($uid);
		if ($uid <= 0) return array();
		$memberTagsRelationsDB = $this->_getMemberTagsRelationsDB();
		$tmpTagids = $memberTagsRelationsDB->getTagIdsByUid($uid);
		$tagids = array();
		foreach ((array)$tagids as $value) {
			$tagid[] = $value['tagid'];
		}
		return $tagid;
	}
	
	/**
	 * ���ݱ�ǩid��ȡ�û�uids
	 * 
	 * @param int $tagid
	 * @return array
	 */
	function getUidsByTagid($tagid, $start, $num) {
		$tagid = intval($tagid);
		$start = intval($start);
		$num = intval($num);
		if ($tagid < 1 || $start < 0 || $num < 1) return array();
		$memberTagsRelationsDB = $this->_getMemberTagsRelationsDB();
		return $memberTagsRelationsDB->getUidsByTagid($tagid, $start, $num);
	}
	
	/**
	 * ���ݱ�ǩtagids������ȡ�û�uids
	 * 
	 * @param int $tagids
	 * @return array
	 */
	function getUidsByTagids($tagids) {
		if(!S::isArray($tagids)) return array();
		$memberTagsRelationsDB = $this->_getMemberTagsRelationsDB();
		$userIds = $memberTagsRelationsDB->getUidsByTagids($tagids);
		$uids = array();
		foreach ((array)$userIds as $u) {
			$uids[] = $u['userid'];
		}
		return $uids;
	}
	
	/**
	 * ����uids��ȡ��ǩ
	 * 
	 * @param array $uids
	 * @return array
	 */
	function getTagsByUidsForSource($uids) {
		if (!S::isArray($uids)) return array();
		$tagsDb = $this->_getMemberTagsDB();
		return $tagsDb->getTagsByUids($uids);
	}
	
	/**
	 * ����uids��ȡ��ǩ
	 * 
	 * @param array $uids
	 * @return array
	 */
	function getTagsByUids($uids,$tags) { 
		if (!S::isArray($uids)) return array();
		$memberTagsDb = $this->_getMemberTagsDB();
		$tmptags = $memberTags = array();

		foreach ((array)$memberTagsDb->getTagsByUids($uids) as $value) {
			$urlencodeTagName = urlencode($value['tagname']);
			$tmptags[$value['userid']][] = ($tags['tagid'] != $value['tagid']) ? "<a href=\"u.php?a=friend&type=find&type=find&according=tags&step=2&f_keyword=$urlencodeTagName\" class=\"f12\" title=\"$value[tagname]\" alt=\"$value[tagname]\">$value[tagname]</a>" : '';
		} 
		$tagName = urlencode($tags['tagname']);
		$keywordsTags = "<a href=\"u.php?a=friend&type=find&type=find&according=tags&step=2&f_keyword=$tagName\" class=\"s2 b\" title=\"$tags[tagname]\" alt=\"$tags[tagname]\" >$tags[tagname]</a>";
		foreach ($tmptags as $key => $tagHtmls) {
			$memberTags[$key] = $keywordsTags . ' ' . implode(' ',$tagHtmls);
		}
		return $memberTags;
	}
	
	/**
	 * ���ݱ�ǩ����ȡ�û�uids
	 * 
	 * @param string $tagName
	 * @return array
	 */
	function getUidsByTagName($tagName, $start, $num) {
		$tagName = trim($tagName);
		$start = intval($start);
		$num = intval($num);
		if (!$tagName || $start < 0 || $num < 1) return array(0,array(),array()); 
		$tags = $this->getTagsByTagName($tagName);
		if(!S::isArray($tags)) return array(0,array(),array()); 
		$uids = array();
		list($count,$tagsUids) = $this->getUidsByTagid($tags['tagid'], $start, $num);
		if (!$count) return array(0,array(),array()); 
		foreach ((array)$tagsUids as $value) {
			$uids[] = $value['userid'];
		}
		$memberTags = $this->getTagsByUids($uids,$tags);
		return array($count,$uids,$memberTags);
	}	
	
	/**
	 * ����uid��ȡ��ǩ��Ϣ
	 * 
	 * @param int $uid 
	 * @return array
	 */
	function getMemberTagsByUid($uid) {
		$uid = intval($uid);
		if ($uid <= 0) return array();
		if (perf::checkMemcache()){
			$_cacheService = Perf::gatherCache('pw_members');
			return $_cacheService->getMemberTagsByUserid($uid);			
		}
		return $this->getMemberTagsByUidFromDB($uid);
	}

	function getMemberTagsByUidFromDB($uid) {
		$uid = intval($uid);
		if ($uid <= 0) return array();
		$memberTagsDb = $this->_getMemberTagsDB();
		return $memberTagsDb->getTagsByUid($uid);
	}
	
	/**
	 * ����100�����ű�ǩ
	 * 
	 * @param int $num 
	 * @param int $updatas  ǿ�Ƹ���
	 */
	function setTopCache($num = 100,$updates = null) {
		$tmpnum = intval($num);
		$num = min($tmpnum,100);
		$cachetime = pwFilemtime($this->_memberTagDbFile); 
		if (!file_exists($this->_memberTagDbFile) || $this->_timestamp - $cachetime > $this->_hour || $updates) {
			$memberTagsDb = $this->_getMemberTagsDB();
			$memberTagsData = $memberTagsDb->getTagsByNum($num);
			pwCache::setData($this->_memberTagDbFile, array('memberTagsData' => $memberTagsData),true);
			touch($this->_memberTagDbFile);
		}
	}

	/**
	 * Top�����ȡ8�����ű�ǩ
	 * 
	 * @param int $num 
	 * @return array
	 */
	function getTagsByNum($num = 8) {
		$this->setTopCache();
		if (!file_exists($this->_memberTagDbFile)) return array();
		extract(pwCache::getData($this->_memberTagDbFile, false));
		if(!S::isArray($memberTagsData)) return array();
		$countNum = count($memberTagsData);
		$num = min($countNum,$num);
		$randTags = array_rand($memberTagsData, $num);
		is_array($randTags) || $randTags = array($randTags);//$num==1ʱ��return string
		$tags = array();
		foreach ($randTags as $value) {
			$tags[] = $memberTagsData[$value];
		}
		return $tags;
	}
	
	function countHotTagsNum() {
		if (!file_exists($this->_memberTagDbFile)) $this->setTopCache();
		extract(pwCache::getData($this->_memberTagDbFile, false));
		if(!S::isArray($memberTagsData)) return 0;
		return count($memberTagsData);	
	}
	
	/**
	 * �����Ƿ��������ű�ǩ
	 * 
	 * @param array $tagids
	 * @return boolean
	 */
	function setHotByTagids($tagids,$ifhot) {
		$ifhot = intval($ifhot);
		if($ifhot < 0 || !S::isArray($tagids)) return false;
		$memberTagsDb = $this->_getMemberTagsDB();
		return (bool)$memberTagsDb->setHotByTagids($tagids,$ifhot);
	}
	
	/**
	 * ��̨��������������ǩ��Ϣ
	 * 
	 * @param string $name ��ǩ��
	 * @param int $ifhot �Ƿ��������ű�ǩ
	 * @param int $startnum minʹ���� 
	 * @param int $endnum maxʹ���� 
	 * @return int
	 */
	function countTagsByCondition($name, $ifhot, $startnum, $endnum) {
		$name = trim($name);
		if ((int)$ifhot < 0 || (int)$startnum < 0 || (int)$endnum < 0) return false;
		$memberTagsDb = $this->_getMemberTagsDB();
		return  $memberTagsDb->countTagsByCondition($name, $ifhot, $startnum, $endnum);
	}

	/**
	 * ��������������ǩ��Ϣ
	 * 
	 * @param string $name ��ǩ��
	 * @param int $ifhot �Ƿ��������ű�ǩ
	 * @param int $startnum minʹ���� 
	 * @param int $endnum maxʹ���� 
	 * @param int $page
	 * @param int $perpage
	 * @return array
	 */
	function getTagsByCondition($name, $ifhot, $startnum, $endnum, $start, $num) {
		$name = trim($name);
		$start = intval($start);
		$num = intval($num);
		if ((int)$ifhot < 0 || (int)$startnum < 0 || (int)$endnum < 0 || $start < 0 || $num < 1) return array(0,array());
		$total =  $this->countTagsByCondition($name, $ifhot, $startnum, $endnum);
			
		if (!$total) return array(0,array());
		$memberTagsDb = $this->_getMemberTagsDB();
		return array($total,$memberTagsDb->getTagsByCondition($name, $ifhot, $startnum, $endnum, $start, $num));
	}

	function makeClassTags($tags) {
		if(!S::isArray($tags)) return array();
		$arrayClass1 = array('A','B','C','D');
		$arrayClass2 = array('E','F','G','H');
		$i = 0;
		$tmpArray = array();
		foreach ($tags as $tag) {
			$array['tagname'] = $tag['tagname'];
			$arrayClass = $i%2 ? $arrayClass1 : $arrayClass2;
			$rand_keys = array_rand($arrayClass,1);
			$array['className'] = 'tagbg' . $arrayClass[$rand_keys];
			$tmpArray[] = $array;
			$i++;
		}
		return $tmpArray;
	}
	/**
	 *�������key
	 * 
	 * @return array ����$fieldsData
	 */
	function checkFieldsData($fieldsData){
		$data = array();
		if(isset($fieldsData['tagid'])) $data['tagid'] = intval($fieldsData['tagid']);
		if(isset($fieldsData['tagname'])) $data['tagname'] = trim($fieldsData['tagname']);
		if(isset($fieldsData['num'])) $data['num'] = intval($fieldsData['num']);
		if(isset($fieldsData['ifhot'])) $data['ifhot'] = intval($fieldsData['ifhot']);
		return $data;
	}

	/**
	 *���membertags_relations����key
	 * 
	 * @return array ����$fieldsData
	 */
	function checkRelationsFieldsData($fieldsData){
		$data = array();
		if(isset($fieldsData['tagid'])) $data['tagid'] = intval($fieldsData['tagid']);
		if(isset($fieldsData['userid'])) $data['userid'] = intval($fieldsData['userid']);
		if(isset($fieldsData['crtime'])) $data['crtime'] = intval($fieldsData['crtime']);
		return $data;
	}
	
	/**
	 * Ϊ�����������tag
	 * @param array $data
	 */
	function addUserTags($data){
		if (!S::isArray($data)) return $data;
		$uids = $tags = $tagsData = array();
		foreach ($data as $k=>$v ) {
			$v['uid'] && $uids[] = $v['uid'];
		}
		if ($uids) {
			$tagsService = L::loadClass('memberTagsService', 'user');
			$tagsData = $tagsService->getTagsByUidsForSource(array_unique($uids));
		}
		
		if ($tagsData) {
			foreach($tagsData as $v){
				$tags[$v['userid']][] = $v['tagname'];
			}
		}
		foreach ($data as $k=>$v) {
			if (isset($tags[$v['uid']])) {
			}
			$data[$k]['tags'] = $tags[$v['uid']] ? implode(' ', $tags[$v['uid']]) : 'TA��û�б�ǩ';
		}
		return $data;
	}
	/**
	 * ���ر�ǩdao
	 * 
	 * @return PW_TagsDB
	 */
	function _getMemberTagsDB() {
		return L::loadDB('MemberTags', 'user');
	}
	
	/**
	 * �����û���ǩdao
	 * 
	 * @return PW_TagsDB
	 */
	function _getMemberTagsRelationsDB() {
		return L::loadDB('memberTagsRelations', 'user');
	}
}