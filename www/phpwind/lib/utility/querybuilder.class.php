<?php
! defined ( 'P_W' ) && exit ( 'Forbidden' );
/*
 * �ṹ����ѯ�����װ��
 * @author L.IuHu.I@2010/10/19 developer.liuhui@gmail.com
 */
! defined ( 'PW_COLUMN' ) && define ( 'PW_COLUMN', 'column' ); //��ѯ�ֶ�
! defined ( 'PW_EXPR' ) && define ( 'PW_EXPR', 'expr' ); //��ѯ���ʽ
! defined ( 'PW_ORDERBY' ) && define ( 'PW_ORDERBY', 'orderby' ); //����
! defined ( 'PW_GROUPBY' ) && define ( 'PW_GROUPBY', 'groupby' ); //����
! defined ( 'PW_LIMIT' ) && define ( 'PW_LIMIT', 'limit' ); //��ҳ
! defined ( 'PW_ASC' ) && define ( 'PW_ASC', 'asc' ); //����
! defined ( 'PW_DESC' ) && define ( 'PW_DESC', 'desc' ); //����
define ( 'PW_DEBUG', 0 ); //�Ƿ������Դ�ӡ
class PW_QueryBuilder {
	/**
	 * �����������
	 * @param $tableName   ���ݱ�����,��:pw_threads
	 * @param $col_names   �ֶ���������,��:array('tid'=>1,'fid'=>2)
	 */
	function insertClause($tableName, $col_names) {
		if (! $tableName || ! is_array ( $col_names ))
			return '';
		$sql = "INSERT INTO " . S::sqlMetadata ( $tableName ) . " ";
		$sql .= $this->_parseSetSQL ( $col_names );
		$this->_smallHook ( 'insert', $sql, array ($tableName ), $col_names );
		return $sql;
	}
	/**
	 * �����滻���
	 * @param $tableName   ���ݱ�����,��:pw_threads
	 * @param $col_names   �ֶ���������,��:array('tid'=>1,'fid'=>2)
	 */
	function replaceClause($tableName, $col_names) {
		if (! $tableName || ! is_array ( $col_names ))
			return '';
		$sql = "REPLACE INTO " . S::sqlMetadata ( $tableName ) . " ";
		$sql .= $this->_parseSetSQL ( $col_names );
		$this->_smallHook ( 'replace', $sql, array ($tableName ), $col_names );
		return $sql;
	}
	/**
	 * ���ɲ�ѯ���
	 * @param $tableName         ���ݱ�����,��:pw_threads
	 * @param $where_statement   ��ѯ�������,��:fid=:fid and ifcheck=:ifcheck,ע����沿�����ֶ�һ��
	 * @param $where_conditions  ��ѯ��������,��array(1,2),�����ڵ��������˳�򱣳�һ��
	 * @param $expand            ��չ����,��ѡ,˵������
	 * $expand = array(
	 * PW_COLUMN  = array('fid','tid'),//��Ҫ��ѯ���ֶ�,Ĭ��Ϊ*,����Ϊ����
	 * PW_EXPR    = array('count(*) as c','max(tid)'),//����Ĳ�ѯ,��ͳ��,���/Сֵ,����Ϊ����
	 * PW_ORDERBY = array('postdate'=> PW_ASC,'tid'=>PW_DESC),//��������,�ֶ�=>��/����ʽ,����Ϊ����
	 * PW_GROUPBY = array('tid'),//��������,����
	 * PW_LIMIT   = array(offset,limit),��ѯ��������ѯ����
	 * );
	 */
	function selectClause($tableName, $where_statement = null, $where_conditions = null, $expand = null) {
		if (! $tableName)
			return '';
		list ( $where_statement, $fields ) = $this->_parseStatement ( $where_statement, $where_conditions );
		$sql = "SELECT ";
		$sql .= $this->_parseColumns ( isset ( $expand [PW_COLUMN] ) ? $expand [PW_COLUMN] : '', isset ( $expand [PW_EXPR] ) ? $expand [PW_EXPR] : '' );
		$sql .= " FROM " . S::sqlMetadata ( $tableName ) . " ";
		($where_statement) && $sql .= " WHERE " . $where_statement;
		(isset ( $expand [PW_GROUPBY] )) && $sql .= $this->_parseGroupBy ( $expand [PW_GROUPBY] );
		(isset ( $expand [PW_ORDERBY] )) && $sql .= $this->_parseOrderBy ( $expand [PW_ORDERBY] );
		(isset ( $expand [PW_LIMIT] )) && $sql .= $this->_parseLimit ( $expand [PW_LIMIT] );
		$this->_smallHook ( 'select', $sql, array ($tableName ), $fields );
		return $sql;
	}
	/**
	 * ���ɸ������
	 * @param $tableName        ���ݱ�����,��pw_threads
	 * @param $where_statement  ͬ�� selectClause()����
	 * @param $where_conditions ͬ�� selectClause()����
	 * @param $col_names        �ֶ���������,��:array('tid'=>1,'fid'=>2)
	 * @param $expand           ͬ�� selectClause()����˵��,��ֻ�����򲿷�
	 */
	function updateClause($tableName, $where_statement = null, $where_conditions = null, $col_names, $expand = null) {
		if (! $tableName || (! is_array ( $col_names ) && ! isset ( $expand [PW_EXPR] )))
			return '';
		list ( $where_statement, $fields ) = $this->_parseStatement ( $where_statement, $where_conditions );
		$sql = "UPDATE " . S::sqlMetadata ( $tableName ) . " ";
		$sql .= $this->_parseSetSQL ( $col_names, (isset ( $expand [PW_EXPR] ) ? $expand [PW_EXPR] : '') );
		($where_statement) && $sql .= " WHERE " . $where_statement;
		(isset ( $expand [PW_ORDERBY] )) && $sql .= $this->_parseOrderBy ( $expand [PW_ORDERBY] );
		(isset ( $expand [PW_LIMIT] )) && $sql .= $this->_parseLimit ( $expand [PW_LIMIT] );
		$this->_smallHook ( 'update', $sql, array ($tableName ), $fields, $col_names );
		return $sql;
	}
	/**
	 * ����ɾ�����
	 * @param $tableName        ���ݱ�����,��pw_threads
	 * @param $where_statement  ͬ�� selectClause()����
	 * @param $where_conditions ͬ�� selectClause()����
	 * @param $col_names        �ֶ���������,��:array('tid'=>1,'fid'=>2)
	 * @param $expand           ͬ�� selectClause()����˵��,��ֻ�����򲿷�
	 */
	function deleteClause($tableName, $where_statement = null, $where_conditions = null, $expand = null) {
		if (! $tableName)
			return '';
		list ( $where_statement, $fields ) = $this->_parseStatement ( $where_statement, $where_conditions );
		$sql = "DELETE FROM " . S::sqlMetadata ( $tableName ) . " ";
		($where_statement) && $sql .= " WHERE " . $where_statement;
		(isset ( $expand [PW_ORDERBY] )) && $sql .= $this->_parseOrderBy ( $expand [PW_ORDERBY] );
		(isset ( $expand [PW_LIMIT] )) && $sql .= $this->_parseLimit ( $expand [PW_LIMIT] );
		$this->_smallHook ( 'delete', $sql, array ($tableName ), $fields );
		return $sql;
	}
	/**
	 * ͨ�ò�ѯ�����װ
	 * @param $format      ��ѯ����ʽ,ע�����ݱ�������:pw_table����ʽ,�������pw_table1,pw_table2
	 * @param $parameters  ��ѯ������
	 */
	function buildClause($format, $parameters, $clauses = array()) {
		if (! $format || ! is_array ( $parameters ))
			return '';
		list ( $sql, $matchInfo ) = $this->_parseStatement ( $format, $parameters, true );
		list ( $tables, $fields ) = $this->_parseMatchs ( $matchInfo );
		$this->_smallHook ( trim ( substr ( $format, 0, 7 ) ), $sql, $tables, $fields );
		return $sql;
	}
	/**
	 * ˽�н���ƥ��������ȡ���ݱ����ƺ������ֶ�
	 * @param $matchInfo  ƥ�������� 
	 */
	function _parseMatchs($matchInfo) {
		if (! $matchInfo) {
			return array (array (), array () );
		}
		foreach ( $matchInfo as $k => $v ) {
			if (strpos ( $k, 'pw_table' ) !== false) {
				$tables [] = $v;
				unset ( $matchInfo [$k] );
			}
		}
		return array ($tables, $matchInfo );
	}
	
	/**
	 * ˽�н���SET���ֽṹ����
	 * @param $arrays
	 */
	function _parseSetSQL($arrays, $expr = null) {
		if (! is_array ( $arrays ) && ! $expr) {
			return '';
		}
		$sets = " SET ";
		if ($expr) {
			foreach ( $expr as $v ) {
				$sets .= " " . $v . ",";
			}
		}
		if ($arrays) {
			foreach ( $arrays as $k => $v ) {
				$sets .= " " . S::sqlMetadata ( $k ) . " = " . S::sqlEscape ( $v ) . ",";
			}
		}
		$sets = trim ( $sets, "," );
		return ($sets) ? $sets : '';
	}
	/**
	 * ˽�н�����ʽģ�壬��ʵ�ָ�ʽ�����ƥ��
	 * @param $statement
	 * @param $conditions
	 */
	function _parseStatement($statement, $conditions, $isCheck = false) {
		if (! $statement || ! is_array ( $conditions ))
			return array ('', array () );
		preg_match_all ( '/:(\w+)/', $statement, $matchs );
		if (! $matchs [0])
			return array ('', array () );
		$fields = array ();
		//fix WooYun-2011-02720.��лRay�� http://www.wooyun.org/bugs/wooyun-2010-02720 �ϵķ���
		$seg = randstr(4);
		$statement = preg_replace ('/(:\w+)/', $seg . '${1}' . $seg, $statement );
		foreach ( $matchs [0] as $k => $field ) {
			$fields [$matchs [1] [$k]] = $conditions [$k];
			$param = (is_array ( $conditions [$k] )) ? S::sqlImplode ( $conditions [$k] ) : (($isCheck && strpos ( $field, 'pw_table' ) !== false) ? $conditions [$k] : S::sqlEscape ( $conditions [$k] ));
			$statement = str_replace ( $seg . $field . $seg, $param, $statement );
		}
		return array ($statement, $fields );
	}
	
	/**
	 * ˽�н�����ѯ�ֶβ���
	 * @param $columns    �ֶ�����
	 * @param $statements �����ѯ���
	 */
	function _parseColumns($columns, $statements) {
		$sql = '';
		if ($columns) {
			foreach ( $columns as $column ) {
				$sql .= S::sqlMetadata ( $column ) . ",";
			}
		}
		if ($statements) {
			foreach ( $statements as $statement ) {
				$sql .= $statement . ",";
			}
		}
		return ($sql) ? rtrim ( $sql, ',' ) : '*';
	}
	/**
	 * ˽�н����������
	 * @param $groupBy
	 */
	function _parseGroupBy($groupBys) {
		if (! $groupBys)
			return '';
		$sql = ' GROUP BY ';
		foreach ( $groupBys as $field ) {
			$sql .= S::sqlMetadata ( $field ) . ',';
		}
		$sql = rtrim ( $sql, ',' );
		return $sql;
	}
	/**
	 * ˽�ý����������
	 * @param $orderBy
	 */
	function _parseOrderBy($orderBy) {
		if (! $orderBy)
			return '';
		$orderBy = (is_array ( $orderBy )) ? $orderBy : array ($orderBy );
		$sql = " ORDER BY ";
		foreach ( $orderBy as $field => $sort ) {
			if (! in_array ( strtolower ( $sort ), array (PW_DESC, PW_ASC ) ))
				continue;
			$sql .= S::sqlMetadata ( $field ) . " " . $sort . ",";
		}
		$sql = rtrim ( $sql, ',' );
		return $sql;
	}
	/**
	 * ˽�н�����ҳ���
	 * @param $offset
	 * @param $row_count
	 */
	function _parseLimit($limits) {
		$offset = S::int ( $limits [0] );
		$row_count = S::int ( $limits [1] );
		return ($offset >= 0 && $row_count > 0) ? " LIMIT " . $offset . "," . $row_count : '';
	}
	/**
	 * ����SQL���
	 * @param $sql
	 */
	function _debug($sql) {
		if (PW_DEBUG) {
			var_dump ( $sql );
		}
	}
	/**
	 * С���ӽӿ�,����ʵ�ֿ���չ
	 * @param $operate    ������Ϊ,��ѡinsert/replace/update/select
	 * @param $tableName  ���ݱ�����
	 * @param $fields     ���������ֶ�
	 */
	function _smallHook($operate, $sql, $tableNames = array(), $fields = array(), $expand = array()) {
		$this->_debug ( $sql );
		Perf::gatherQuery ( $operate, $tableNames, $fields, $expand );
		return true;
	}
}