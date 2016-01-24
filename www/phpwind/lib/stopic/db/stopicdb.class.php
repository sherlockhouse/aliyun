<?php
/**
 * ר���¼���ݿ��������
 * 
 * @package STopic
 */

!defined('P_W') && exit('Forbidden');

/**
 * ר���¼���ݿ��������
 * 
 * ��װ��ר���¼����ɾ�Ĳ�Ȳ�����ΪPW_STopicService�ṩ���ݿ����
 * 
 * @package STopic
 */
class PW_STopicDB extends BaseDB {
	/**
	 * ����
	 *
	 * @access private
	 * @var string
	 */
	var $_tableName = "pw_stopic";
	var $_bgTableName = "pw_stopicpictures";
	var $_cateTableName = "pw_stopiccategory";
	
	/**
	 * ���ר���¼
	 *
	 * @param array $fieldsData ר���¼��������
	 * @return int �ɹ�����ר��id�����򷵻�0
	 */
	function add($fieldsData) {
		$fieldsData = $this->_checkData($fieldsData);
		if (!$fieldsData) return null;
		$this->_db->update("INSERT INTO " . $this->_tableName . " SET " . $this->_getUpdateSqlString($fieldsData));
		$insertId = $this->_db->insert_id();
		return $insertId;
	}
	
	/**
	 * ɾ��ר���¼
	 *
	 * @param int $stopicId ר��id
	 * @return int ɾ������
	 */
	function delete($stopicId) {
		$this->_db->update("DELETE FROM " . $this->_tableName . " WHERE stopic_id=" . intval($stopicId) . " LIMIT 1");
		return $this->_db->affected_rows();
	}
	
	/**
	 * ����ר���¼
	 *
	 * @param int $stopicId ר��id
	 * @param array $updateData ������������
	 * @return int ��������
	 */
	function update($stopicId, $updateData) {
		$updateData = $this->_checkData($updateData);
		if (!$updateData) return null;
		$this->_db->update("UPDATE " . $this->_tableName . " SET " . $this->_getUpdateSqlString($updateData) . " WHERE stopic_id=" . intval($stopicId) . " LIMIT 1");
		return $this->_db->affected_rows();
	}
	
	/**
	 * ������ʹ����ͬ�ļ�����ר����ļ�������Ϊ��
	 * 
	 * @param int $stopicId ר��id
	 * @param string $fileName ������ļ���
	 * @return int ���¸���
	 */
	function updateFileName($stopicId, $fileName) {
		$stopicId = intval($stopicId);
		if ($stopicId <= 0 || '' == $fileName) return 0;
		
		return $this->_db->update("UPDATE " . $this->_tableName . " SET file_name='' WHERE file_name=" . $this->_addSlashes($fileName) . " AND stopic_id!=" . $stopicId);
	}
	
	/**
	 * �ֶ�����
	 * 
	 * @param int $stopicId ר��id
	 * @param string $fieldName �ֶ���
	 * @return int
	 */
	function increaseField($stopicId, $fieldName) {
		if (!in_array($fieldName, array(
			'used_count',
			'view_count'
		))) return 0;
		$this->_db->update("UPDATE " . $this->_tableName . " SET $fieldName=$fieldName+1 WHERE stopic_id=" . intval($stopicId) . " LIMIT 1");
		return $this->_db->affected_rows();
	}
	
	/**
	 * ��ȡר���¼
	 *
	 * @param int $stopicId ר��id
	 * @return array/null �ҵ�����ר�����ݣ����򷵻�null
	 */
	function get($stopicId) {
		$data = $this->_db->get_one("SELECT * FROM " . $this->_tableName . " WHERE stopic_id=" . intval($stopicId));
		if (!$data) return null;
		return $this->_unserializeData($data);
	}
	
	/**
	 * ȡcount
	 *
	 * @param int $stopicId ר��id
	 * @return array/null �ҵ�����ר�����ݣ����򷵻�null
	 */
	function getCommentNum($stopicId) {
		$stopicId = intval($stopicId);
		if (!$stopicId) return false;
		return $this->_db->get_value("SELECT commentnum FROM " . $this->_tableName . " WHERE stopic_id=" . intval($stopicId));
	}
	
	function updateCommentnum($exp='+1',$stopicId) {
		$stopicId = intval($stopicId);
		if($stopicId < 1 || !$exp) return false;
		
		$num = intval(trim($exp,'+-'));
		if (strpos($exp,'+') !== false) {
			return $this->_db->update(pwQuery::buildClause("UPDATE :pw_table SET commentnum=commentnum+" . S::sqlEscape($num) . ' WHERE stopic_id=:stopic_id', array($this->_tableName, $stopicId)));
		} else {
			return $this->_db->update(pwQuery::buildClause("UPDATE :pw_table SET commentnum=commentnum-" . S::sqlEscape($num) . ' WHERE stopic_id=:stopic_id', array($this->_tableName, $stopicId)));
		}
		return false;
	}
	
	/**
	 * ��ȡδ������ר��
	 * 
	 * @return null|array ר������
	 */
	function getEmpty() {
		$data = $this->_db->get_one("SELECT * FROM " . $this->_tableName . " WHERE layout_config='' AND block_config=''");
		if (!$data) return null;
		return $this->_unserializeData($data);
	}
	
	/**
	 * ���ݹؼ��ֲ�ѯר����
	 *
	 * @param string $keyword �ؼ��֣�Ϊ�����������
	 * @return int ר����
	 */
	function countByKeyWord($keyword = '', $categoryId = 0) {
		$sqlAdd = array();
		if ('' != $keyword) $sqlAdd[] = " title LIKE " . $this->_addSlashes('%' . $keyword . '%') . " ";
		if ($categoryId > 0) $sqlAdd[] = " category_id=" . $this->_addSlashes($categoryId) . " ";
		$sqlAdd = count($sqlAdd) ? " WHERE " . implode(" AND ", $sqlAdd) : "";
		$rt = $this->_db->get_one("SELECT COUNT(*) AS total_num FROM " . $this->_tableName . " " . $sqlAdd);
		return $rt['total_num'];
	}
	
	/**
	 * ���ݹؼ��ַ�ҳ��ѯר���¼
	 *
	 * @param int $page ҳ��>=1
	 * @param int $perPage ÿҳ��¼��>=1
	 * @param string $keyword �ؼ��֣�Ϊ�����������
	 * @return array ר����������
	 */
	function findByKeyWordInPage($page, $perPage, $keyword = '', $categoryId = 0) {
		$page = intval($page);
		$perPage = intval($perPage);
		if ($page <= 0 || $perPage <= 0) return array();
		
		$offset = ($page - 1) * $perPage;
		
		$sqlAdd = array();
		if ('' != $keyword) $sqlAdd[] = " a.title LIKE " . $this->_addSlashes('%' . $keyword . '%') . " ";
		if ($categoryId > 0) $sqlAdd[] = " a.category_id=" . $this->_addSlashes($categoryId) . " ";
		$sqlAdd = count($sqlAdd) ? " WHERE " . implode(" AND ", $sqlAdd) : "";
		
		$query = $this->_db->query("SELECT a.*,c.title as catetitle FROM " . $this->_tableName . " a LEFT JOIN " . $this->_cateTableName . " c ON a.category_id=c.id $sqlAdd ORDER BY a.create_date DESC LIMIT $offset,$perPage");
		return $this->_getAllResultFromQuery($query);
	}
	
	/**
	 * �������ҳ������Чר���¼
	 *
	 * @param int $page ҳ��>=1
	 * @param int $perPage ÿҳ��¼��>=1
	 * @param int $categoryId ���࣬0���������
	 * @return array ר����������
	 */
	function findValidByCategoryIdInPage($page, $perPage, $categoryId = 0) {
		$page = intval($page);
		$perPage = intval($perPage);
		$categoryId = intval($categoryId);
		if ($page <= 0 || $perPage <= 0) return array();
		
		$offset = ($page - 1) * $perPage;
		$sqlAdd = $categoryId ? " AND category_id=$categoryId " : "";
		$nowTime = time();
		$query = $this->_db->query("SELECT * FROM " . $this->_tableName . "  WHERE start_date<=$nowTime AND end_date>$nowTime $sqlAdd ORDER BY create_date DESC LIMIT $offset,$perPage");
		return $this->_getAllResultFromQuery($query);
	}
	
	/**
	 * �����ఴʹ�ô��������ҳ����ר���¼
	 *
	 * @param int $page ҳ��>=1
	 * @param int $perPage ÿҳ��¼��>=1
	 * @param int $categoryId ���࣬0���������
	 * @return array ר����������
	 */
	function findByCategoryIdOrderByUsedInPage($page, $perPage, $categoryId = 0) {
		$page = intval($page);
		$perPage = intval($perPage);
		$categoryId = intval($categoryId);
		if ($page <= 0 || $perPage <= 0) return array();
		
		$offset = ($page - 1) * $perPage;
		$sqlAdd = $categoryId ? " WHERE category_id=$categoryId " : "";
		$query = $this->_db->query("SELECT a.*, b.path FROM " . $this->_tableName . " a LEFT JOIN " . $this->_bgTableName . " b ON a.bg_id=b.id $sqlAdd ORDER BY used_count DESC LIMIT $offset,$perPage");
		return $this->_getAllResultFromQuery($query);
	}
	
	/**
	 * ͳ��ʹ��ĳ������ר����
	 *
	 * @param int $backgroundId ����id
	 * @return int ʹ�ø���
	 */
	function countByBackgroundId($backgroundId) {
		$backgroundId = intval($backgroundId);
		if ($backgroundId <= 0) return 0;
		
		return $this->_db->get_value("SELECT COUNT(*) FROM " . $this->_tableName . " WHERE bg_id=$backgroundId");
	}
	
	/**
	 * ������ͳ��ר�����
	 * 
	 * @param int $categoryId
	 * @return int
	 */
	function countByCategoryId($categoryId) {
		$categoryId = intval($categoryId);
		if ($categoryId <= 0) return 0;
		
		return $this->_db->get_value("SELECT COUNT(*) FROM " . $this->_tableName . " WHERE category_id=$categoryId");
	}
	
	/**
	 * �����ļ�����ȡר��
	 * 
	 * @param int $stopicId
	 * @param string $fileName ר�Ᵽ����ļ���
	 * @return null|array ר������
	 */
	function getByFileNameAndExcept($stopicId, $fileName) {
		$stopicId = intval($stopicId);
		if ($stopicId <= 0 || '' == $fileName) return null;
		
		return $this->_db->get_one("SELECT * FROM " . $this->_tableName . " WHERE file_name=" . $this->_addSlashes($fileName) . " AND stopic_id!=" . $stopicId);
	}
	
	/**
	 * ���ֶ�
	 */
	function getStruct() {
		return array(
			'stopic_id',
			'title',
			'category_id',
			'bg_id',
			'copy_from',
			'layout',
			'create_date',
			'start_date',
			'end_date',
			'used_count',
			'view_count',
			'block_config',
			'layout_config',
			'nav_config',
			'banner_url',
			'seo_keyword',
			'seo_desc',
			'file_name'
		);
	}
	
	/**
	 * �����ֶ�
	 * 
	 * @param array $data
	 */
	function _checkData($data) {
		if (!is_array($data) || !count($data)) return null;
		$data = $this->_checkAllowField($data, $this->getStruct());
		$data = $this->_serializeData($data);
		return $data;
	}
	
	/**
	 * ���л�����
	 * 
	 * @param array $data
	 */
	function _serializeData($data) {
		if (isset($data['layout_config']) && is_array($data['layout_config'])) $data['layout_config'] = serialize($data['layout_config']);
		if (isset($data['block_config']) && is_array($data['block_config'])) $data['block_config'] = serialize($data['block_config']);
		if (isset($data['nav_config']) && is_array($data['nav_config'])) $data['nav_config'] = serialize($data['nav_config']);
		return $data;
	}
	
	/**
	 * �����л�����
	 * 
	 * @param array $data
	 */
	function _unserializeData($data) {
		if ($data['layout_config']) $data['layout_config'] = unserialize($data['layout_config']);
		if ($data['block_config']) $data['block_config'] = unserialize($data['block_config']);
		if ($data['nav_config']) $data['nav_config'] = unserialize($data['nav_config']);
		return $data;
	}
}

