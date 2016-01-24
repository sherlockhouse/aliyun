<?php
! function_exists ( 'readover' ) && exit ( 'Forbidden' );
/**
 * sphinxʵʱ�������� ��������/���ƻ�����/��Ҫ�����֧��
 * @author L.IuHu.I@2010-11-20
 * ���������ƿ��Զ����޸�
 * ����������
 * 1,������,�� threadsindex
 * 2,��������,�� addthreadsindex
 * 3,�ϲ�����������  indexer --merge threadsindex addthreadsindex --merge-dst-range deleted 0 0
 * Ŀǰ��������������,����ڸ�����������ʱ��Ҫ��������������,��汾�� threadsindex, tmsgsindex, threadsallindex��������
 * Ĭ�ϵ�����¸�����������ѡ��,��������������������������������б���
 * 
 * ����������������Դ���Դ�ʵʱ�������л�ȡ,���ݱ�ṹ����
 * -- TableName pw_delta_threads ʵʱ������
 * -- Created By phpwind@2010-11-20
 * -- Fields id       ����ID(��tid,uid,did,pid)
 * -- Fields state    ״̬ 0/1�����Զ��壩
 * CREATE TABLE pw_delta_tablename(
 * 	id int(10) unsigned not null auto_increment,
 * 	state tinyint(3) unsigned not null default 0,
 * 	primary key (id)
 * )ENGINE=MyISAM;
 * �������ƻ�����Ҫ���������ı�,��pw_delta_threads,pw_delta_posts,pw_delta_users,pw_delta_diary��
 */
class PW_RealTimeSearcher {
	
	var $_sphinxConfig = array (); //��������
	var $_threadIndexs = array ('threadsindex', 'tmsgsindex', 'threadsallindex' ); //�������������б�
	var $_userIndexs = array ('membersindex' ); //�û����������б�
	var $_diaryIndexs = array ('diarysindex', 'diarycontentsindex', 'diaryallsindex' ); //��־���������б�
	var $_postIndexs = array ('postsindex' ); //�ظ����������б�
	var $_deleteFiled = 'authorid'; //�ϲ����������ֶ�
	var $_tableNames = array ('thread' => 'pw_delta_threads', 'post' => 'pw_delta_posts', 'member' => 'pw_delta_members', 'diary' => 'pw_delta_diarys' );
	
	function PW_RealTimeSearcher() {
		$this->_sphinxConfig = ($GLOBALS ['db_sphinx']) ? $GLOBALS ['db_sphinx'] : array ('host' => 'localhost', 'port' => 3312 );
	}
	
	function syncData($type, $operate, $ids) {
		$ids = (S::isArray ( $ids )) ? $ids : array ($ids );
		$indexes = array ('thread' => $this->_threadIndexs, 'post' => $this->_postIndexs, 'user' => $this->_userIndexs, 'diary' => $this->_diaryIndexs );
		if (! isset ( $this->_tableNames [$type] ) || ! isset ( $indexes [$type] )) {
			return false;
		}
		switch ($operate) {
			case 'insert' :
				return $this->_logDelta ( $this->_tableNames [$type], $ids, 0 );
				break;
			case 'update' :
				return $this->_logDelta ( $this->_tableNames [$type], $ids, 0 );
				break;
			case 'delete' :
				return $this->_doSync ( $indexes [$type], array ($this->_deleteFiled ), $ids, 1 );
				break;
			default :
				break;
		}
		return true;
	}
	
	function _logDelta($tableName, $ids, $state) {
		if (! S::isArray ( $ids ))
			return false;
		$_tmp = array ();
		foreach ( $ids as $id ) {
			$_tmp [] = array ('id' => $id, 'state' => $state );
		}
		$GLOBALS ['db']->update ( "REPLACE INTO " . S::sqlMetadata ( $tableName ) . " (id,state) VALUES " . S::sqlMulti ( $_tmp ) );
	}
	
	function _doSync($indexes, $attrs, $ids, $state) {
		if (! S::isArray ( $ids )) {
			return false;
		}
		$_tmp = array ();
		foreach ( $ids as $id ) {
			$_tmp [$id] = array ($state );
		}
		return $this->_syncData ( $indexes, $attrs, $_tmp );
	}
	
	function _syncData($indexes, $attrs, $values) {
		$sphinxAPI = $this->_getSphinxAPI ();
		list ( $host, $port ) = array ($this->_sphinxConfig ['host'], $this->_sphinxConfig ['port'] );
		$sphinxAPI->SetServer ( $host, ( int ) $port );
		$sphinxAPI->SetConnectTimeout ( 1 );
		return $sphinxAPI->UpdateAttributes ( implode ( ',', $indexes ), $attrs, $values );
	}
	
	function _getSphinxAPI() {
		L::loadClass ( 'sphinx', 'utility', false );
		return new SphinxClient ();
	}

}