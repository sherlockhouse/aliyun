<?php
! defined ( 'P_W' ) && exit ( 'Forbidden' );
/**
 * ͨ�þۺϷ���
 */
class GatherInfo_General_Service {

	/**
	 * �����ӵ����ݸı��ˣ���Ҫ�������ӻ���
	 *
	 * @param array $information ��ʽarray('tid'=>$tids)
	 * @return boolean
	 */
	function changeThreadWithThreadIds($information) {
		if (!Perf::checkMemcache() || !isset($information['tid'])) return true;
		$threadIds = is_array ( $information ['tid'] ) ? $information ['tid'] : array ($information ['tid'] );
		$_cacheService = Perf::gathercache ( 'pw_threads' );
		$_cacheService->clearCacheForTmsgByThreadIds ( $threadIds );
		return $_cacheService->clearCacheForThreadByThreadIds ( $threadIds );
	}
	
	/**
	 * ��Щ��ѯ����޷�����ɽṹ����ѯ������ͨ��gatherinfo�ռ�
	 *
	 * @param array $information
	 * @return boolean
	 */
	function changeThreads($information) {
		return Perf::gatherQuery('update', array('pw_threads'), $information);
	}
	
	/**
	 * ��Щ��ѯ����޷�����ɽṹ����ѯ������ͨ��gatherinfo�ռ�
	 *
	 * @param array $information
	 * @return boolean
	 */
	function changePosts($information) {
		return Perf::gatherQuery('update', array($information['_tablename']), $information);
	}
	
	function deletePosts($information){
		return Perf::gatherQuery('delete', array($information['_tablename']), $information);
	}

	/**
	 * ������ϸ��Ϣ�ı�ʱ����Ҫ���»���
	 *
	 * @param array $information
	 * @return boolean
	 */
	function changeTmsgWithThreadIds($information){
		if (!Perf::checkMemcache() || !isset($information['tid'])) return true;
		$threadIds = is_array ( $information ['tid'] ) ? $information ['tid'] : array ($information ['tid'] );
		$_cacheService = Perf::gathercache ( 'pw_threads' );
		return $_cacheService->clearCacheForTmsgByThreadIds ( $threadIds );
	}

	/**
	 * �ð��ı䣬 ��Ҫ���������б���, ��ʽarray('fid'=>$fids)
	 *
	 * @param array $information
	 * @return boolean
	 */
	function changeThreadWithForumIds($information){
		if (!Perf::checkMemcache() || !isset($information['fid'])) return true;
		$forumIds = is_array ( $information ['fid'] ) ? $information ['fid'] : array ($information ['fid'] );
		$_cacheService = Perf::gathercache ( 'pw_threads' );
		return $_cacheService->clearCacheForThreadListByForumIds ( $forumIds );
	}

	/**
	 * ���Ӹ���ʱ����Ҫ���������б� �� ��lib/forum/postmodify.class.php(363)����
	 *
	 * @param array $information  
	 * @return boolean
	 */
	function changeThreadListWithThreadIds($information){
		if (!Perf::checkMemcache() || !isset($information['tid'])) return true;
		$threadIds = is_array ( $information ['tid'] ) ? $information ['tid'] : array ($information ['tid'] );
		$_cacheService = Perf::gathercache ( 'pw_threads' );
		$threads = $_cacheService->getThreadsByThreadIds($threadIds);
		if (is_array($threads)){
			$fid = array();
			foreach ($threads as $thread){
				$fid[] = $thread['fid'];
			}
			$fid && $_cacheService->clearCacheForThreadListByForumIds ( $fid );
		}
		return true;
	}

	/**
	 * �����û�������Ϣʱ�������Ӧ����
	 *
	 * @param array $information ��ʽarray('uid'=>$uids)
	 * @return boolean
	 */
	function changeMembersWithUserIds($information){
		if (!isset($information['uid'])) return true;
		$userIds = is_array ( $information ['uid'] ) ? $information ['uid'] : array ($information ['uid'] );
		if (Perf::checkMemcache()){
			$_cacheService = Perf::gathercache ( 'pw_members' );
			return $_cacheService->clearCacheForMembersByUserIds( $userIds );
			//$_cacheService->clearCacheForMemberDataByUserIds( $userIds );
			//return $_cacheService->clearCacheForMemberInfoByUserIds( $userIds );
		}else {
			$_cacheService = Perf::gatherCache('pw_membersdbcache');
			return $_cacheService->clearMembersDbCacheByUserIds( $userIds );			
		}
	}

	/**
	 * �����û�Data��Ϣʱ�������Ӧ����
	 *
	 * @param array $information ��ʽarray('uid'=>$uids)
	 * @return boolean
	 */
	function changeMemberDataWithUserIds($information){
		if (!Perf::checkMemcache() || !isset($information['uid'])) return true;
		$userIds = is_array ( $information ['uid'] ) ? $information ['uid'] : array ($information ['uid'] );
		$_cacheService = Perf::gathercache ( 'pw_members' );
		return $_cacheService->clearCacheForMemberDataByUserIds( $userIds );
	}

	/**
	 * �����û�Info��Ϣʱ�������Ӧ����
	 *
	 * @param array $information ��ʽarray('uid'=>$uids)
	 * @return boolean
	 */
	function changeMemberInfoWithUserIds($information){
		if (!Perf::checkMemcache() || !isset($information['uid'])) return true;
		$userIds = is_array ( $information ['uid'] ) ? $information ['uid'] : array ($information ['uid'] );
		$_cacheService = Perf::gathercache ( 'pw_members' );
		return $_cacheService->clearCacheForMemberInfoByUserIds( $userIds );
	}

	/**
	 * �����û�SingleRight��Ϣʱ�������Ӧ����
	 *
	 * @param array $information
	 * @return boolean
	 */
	function changeSingleRightWithUserIds($information){
		if (!Perf::checkMemcache() || !isset($information['uid'])) return true;
		$userIds = is_array ( $information ['uid'] ) ? $information ['uid'] : array ($information ['uid'] );
		$_cacheService = Perf::gathercache ( 'pw_members' );
		return $_cacheService->clearCacheForSingleRightByUserIds( $userIds );
	}

	/**
	 * �����û�MemberCredit��Ϣʱ�������Ӧ����
	 *
	 * @param array $information
	 * @return boolean
	 */
	function changeMemberCreditWithUserIds($information){
		if (!isset($information['uid'])) return true;
		$userIds = is_array ( $information ['uid'] ) ? $information ['uid'] : array ($information ['uid'] );
		if (Perf::checkMemcache()){
			$_cacheService = Perf::gathercache ( 'pw_members' );
			return $_cacheService->clearCacheForMemberCreditByUserIds( $userIds );			
		}else{
			$_cacheService = Perf::gatherCache('pw_membersdbcache');
			return $_cacheService->clearCreditDbCacheByUserIds( $userIds );				
		}		
	}

	/**
	 * �����û�Ⱥ����Ϣ��CmemberAndColony��ʱ�������Ӧ���� 
	 *
	 * @param array $information
	 * @return boolean
	 */
	function changeCmemberAndColonyWithUserIds($information){
		if (!isset($information['uid'])) return true;
		$userIds = is_array ( $information ['uid'] ) ? $information ['uid'] : array ($information ['uid'] );
		if (Perf::checkMemcache()){
			$_cacheService = Perf::gathercache ( 'pw_members' );
			return $_cacheService->clearCacheForCmemberAndColonyByUserIds( $userIds );			
		}else{
			$_cacheService = Perf::gatherCache('pw_membersdbcache');
			return $_cacheService->clearColonyDbCacheByUserIds( $userIds );				
		}
	}
	
	/**
	 * �����û�Ⱥ����Ϣ��CmemberAndColony��ʱ�������Ӧ���� 
	 *
	 * @param array $information
	 * @return boolean
	 */
	/**
	function changeALLMembers($information = null){
		if (!Perf::checkMemcache() || !isset($information['uid'])) return true;
		$userIds = is_array ( $information ['uid'] ) ? $information ['uid'] : array ($information ['uid'] );
		$_cacheService = Perf::gathercache ( 'pw_members' );
		return $_cacheService->clearCacheForCmemberAndColonyByUserIds( $userIds );
	}
	**/
	
	/**
	 * ��Щ��ѯ����޷�����ɽṹ����ѯ������ͨ��gatherinfo�ռ�
	 *
	 * @param array $information
	 * @return boolean
	 */
	function changeForumData($information){
		return Perf::gatherQuery('update', array('pw_forumdata'), $information);
	}
	
	/**
	 * �������ͷ��ظ�ʱ����memcache��ȡ��������Ȼ���������������ֱ��ɾ������
	 *
	 * @param array $information
	 * @return boolean
	 */
	function changeForumDataWithForumId($information = null){
		if (!Perf::checkMemcache()) return true;
		if (!S::isArray($information) || !($information = current($information)) || !isset($information['fid'])) return false;
		$fid = intval($information['fid']);
		$_cacheService = Perf::getCacheService();
		$_cacheInfo = $_cacheService->get(array('all_forums_info', 'forumdata_announce_' . $fid));
		$_unique = $GLOBALS['db_memcache']['hash'];
		// ����indexҳ�����黺��
		if (isset($_cacheInfo[$_unique . 'all_forums_info'])){
			$allForums = $_cacheInfo[$_unique . 'all_forums_info'];
			foreach ($information as $key => $value){
				if (in_array($key, array('article', 'topic','tpost','subtopic'))){
					$allForums[$fid][$key] = $allForums[$fid][$key] + $value;
				}else {
					$allForums[$fid][$key] = $value;
				}
			}
			$_cacheService->set('all_forums_info', $allForums, 300);
		}
		// ����threadҳ�������ͨ�滺��
		if (isset($_cacheInfo[$_unique . 'forumdata_announce_' . $fid])){
			$forums = $_cacheInfo[$_unique . 'forumdata_announce_' . $fid];
			foreach ($information as $key => $value){
				if (in_array($key, array('article', 'topic','tpost','subtopic'))){
					$forums[$key] = $forums[$key] + $value;
				}else {
					$forums[$key] = $value;
				}
			}
			$_cacheService->set('forumdata_announce_' . $fid, $forums, 300);			
		}
		return true;
	}
}