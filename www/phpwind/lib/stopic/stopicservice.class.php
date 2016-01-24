<?php
/**
 * ר��ҵ��������ļ�
 * 
 * @package STopic
 */

!defined('P_W') && exit('Forbidden');

/**
 * ר��ҵ��������
 * 
 * �ṩ����ר��ҵ������ķ���ӿڣ�����ר�Ȿ���ҵ�������ר����������ר�ⱳ��ͼƬ�����ȡ�
 *
 * @package STopic
 */
class PW_STopicService {
	/**
	 * ר������
	 * 
	 * @var array
	 */
	var $_stopicConfig = null;

	/**
	 * ��ȡר��������
	 * 
	 * @return int
	 */
	function getCommentNum($stopicId) {
		$stopicId = intval($stopicId);
		if (!$stopicId) return false;
		$stopicDB = $this->_getSTopicDB();
		return $stopicDB->getCommentNum($stopicId);
	}
	
	/**
	 * ���»ظ���
	 * 
	 * @param array $fieldsData
	 * @param int $commentid 
	 * @return boolean 
	 */
	function updateCommentnum($num,$stopicId) {
		$stopicId = intval($stopicId);
		if($stopicId < 1 || !$num) return false;
		$stopicDB = $this->_getSTopicDB();
		return $stopicDB->updateCommentnum($num,$stopicId);
	}
	
	/**
	 * ��ȡר����ò����б�
	 * 
	 * @return array ר�Ⲽ���б�����
	 */
	function getLayoutList() {
		$layoutTypes= $this->_getSTopicConfig('layoutTypes');
		$layoutList	= array ();
		foreach ( $layoutTypes as $typeName => $typeDesc ) {
			$tmp = $this->getLayoutInfo ($typeName);
			if ($tmp)
				$layoutList [$typeName] = $tmp;
		}
		return $layoutList;
	}

	/**
	 * ��ȡר�Ⲽ��������Ϣ
	 * 
	 * @param string $typeName �������ƣ���type1v0,type1v1��
	 * @return array ������������
	 */
	function getLayoutInfo($typeName) {
		$stopicConfig = $this->_getSTopicConfig ();
		$checkDir = $stopicConfig ['layoutPath'] . $typeName . "/";
		if (! is_dir ( $checkDir ))
			return false;

		foreach ( $stopicConfig ['layoutConfig'] as $checkFile ) {
			if (! is_file ( $checkDir . $checkFile ))
				return false;
		}
		$checkData = array ();
		$checkData ['logo'] = $stopicConfig ['layoutBaseUrl'] . $typeName . "/" . $stopicConfig ['layoutConfig'] ['logo'];
		$checkData ['html'] = $checkFile . $stopicConfig ['layoutConfig'] ['html'];
		$checkData ['desc'] = $stopicConfig ['layoutTypes'] [$typeName];
		return $checkData;
	}
	
	/**
	 * ��ȡר��Ĭ�ϵķ����ʽcss����
	 * 
	 * @param string $defaultStyle Ĭ�Ϸ����ʽ����Ĭ��ֵΪbaby_org
	 * @return array �����ʽcss��������
	 */
	function getLayoutDefaultSet($defaultStyle = 'baby_org') {
		$styleConfig = $this->getStyleConfig('baby_org');
		if (empty($styleConfig)) { 
			return $this->_getSTopicConfig('layout_set');
		} else {
			$layoutSet = $styleConfig['layout_set'];
			$layoutSet['bannerurl'] = $this->getStyleBanner($defaultStyle);
			return $layoutSet;
		}
	}

	/**
	 * ��ȡר��ķ����ʽcss����
	 * 
	 * @param string $style �����ʽ��
	 * @return array �����ʽcss��������
	 */
	function getLayoutSet($style) {
		$stylePath = $this->_getSTopicConfig('stylePath');
		if ($style && is_dir($stylePath.$style)) {
			return $this->getStyleConfig($style,'layout_set');
		}
		return $this->getLayoutDefaultSet();
	}

	/**
	 * ��ȡ�����ʽ�б�
	 * 
	 * @return array �����ʽ�б�����
	 */
	function getStyles() {
		$stylePath = $this->_getSTopicConfig('stylePath');
		$fp	= opendir($stylePath);
		$styles	= array();
		while ($styleDir = readdir($fp)) {
			if (in_array($styleDir,array('.','..')) || strpos($styleDir,'.')!==false) continue;
			$styles[$styleDir] = array(
				'name'=>$this->getStyleConfig($styleDir,'name'),
				'minipreview'=>$this->getStyleMiniPreview($styleDir),
				'preview'=>$this->getStylePreview($styleDir),
			);
		}
		return $styles;
	}

	/**
	 * ��ȡ�����ʽԤ������ͼurl
	 * 
	 * @param string $style �����ʽ��
	 * @return string ����ͼurl
	 */
	function getStyleMiniPreview($style) {
		return $this->_getSTopicConfig('styleBaseUrl').$style.'/'.$this->_getSTopicConfig('styleMiniPreview');
	}

	/**
	 * ��ȡ�����ʽԤ��ͼurl
	 * 
	 * @param string $style �����ʽ��
	 * @return string Ԥ��ͼurl
	 */
	function getStylePreview($style) {
		return $this->_getSTopicConfig('styleBaseUrl').$style.'/'.$this->_getSTopicConfig('stylePreview');
	}
	
	/**
	 * ��ȡ�����ʽ�ĺ��ͼƬurl
	 * 
	 * @param string $style �����ʽ��
	 * @return string ���ͼƬurl
	 */
	function getStyleBanner($style) {
		$temp = $this->getStyleConfig($style,'banner');
		if ($temp) {
			if (strpos($temp,'http')===false) {
				$temp = $GLOBALS['db_bbsurl'].'/'.$temp;
			}
			return $temp;
		}
		if ($style && file_exists($this->_getSTopicConfig('stylePath').$style.'/'.$this->_getSTopicConfig('styleBanner'))) {
			return $this->_getSTopicConfig('styleBaseUrl').$style.'/'.$this->_getSTopicConfig('styleBanner');
		}
		return 'http://';
	}
	
	/**
	 * ��ȡ�����ʽ������
	 * 
	 * @param string $style �����ʽ��
	 * @param string $key �����ʽ��������
	 * @return mixed ���$keyΪ���򷵻ط����ʽ�������飬���򷵻�ָ���������ֵ
	 */
	function getStyleConfig($style,$key='') {
		static $styles = array();
		if (!isset($styles[$style])) {
			$stylePath = $this->_getSTopicConfig('stylePath');
			if (file_exists($stylePath.$style.'/config.php')) {
				$styles[$style] = include S::escapePath($stylePath.$style."/config.php");
			} else {
				$styles[$style] = array();
			}
		}
		if ($key) {
			return isset($styles[$style][$key]) ? $styles[$style][$key] : '';
		}
		return $styles[$style];
	}

	/**
	 * ����ר��html�ļ�
	 * 
	 * @param int $stopic_id ר��id
	 * @return null
	 */
	function creatStopicHtml($stopic_id) {
		global $db_charset,$wind_version,$db_bbsurl,$db_htmifopen;
		$stopic	= $this->getSTopicInfoById($stopic_id);
		if (!$stopic) return false;
		$tpl_content	= $this->getStopicContent($stopic_id,0);
		@extract($stopic, EXTR_SKIP);
		if (defined('A_P')) {
			include(A_P.'template/stopic.htm');
		} else {
			include(R_P.'apps/stopic/template/stopic.htm');
		}
		$output = str_replace(array('<!--<!---->','<!---->'),array('',''),ob_get_contents());
		ob_end_clean();
		$stopicDir	= $this->getStopicDir($stopic_id, $stopic['file_name']);
		$output = parseHtmlUrlRewrite($output, $db_htmifopen);
		pwCache::writeover($stopicDir,$output);
		ObStart();
	}

	/**
	 * ����ר��
	 * 
	 * @param $fieldsData ר���������飬��Ӧ���ݿ����ֶ�
	 * @return int ר��id��ʧ���򷵻�0
	 */
	function addSTopic($fieldsData) {
		if (!is_array($fieldsData) || !count($fieldsData)) return 0;
		$fieldsData['create_date'] = time();

		$stopicDB = $this->_getSTopicDB();
		$stopicPicturesDB = $this->_getSTopicPicturesDB();
		$stopicId = $stopicDB->add($fieldsData);
		//if ($stopicId && isset($fieldsData['copy_from']) && $fieldsData['copy_from']) $stopicDB->increaseField($fieldsData['copy_from'], 'used_count');
		if ($stopicId && isset($fieldsData['bg_id']) && $fieldsData['bg_id']) $stopicPicturesDB->increaseField($fieldsData['bg_id'], 'num');
		return $stopicId;
	}

	/**
	 * ɾ�����ר��
	 * 
	 * @param array $stopicIds ר��id����
	 * @return int ɾ������
	 */
	function deleteSTopics($stopicIds) {
		$success = 0;
		foreach ( $stopicIds as $stopicId ) {
			$success += $this->deleteSTopicById ( $stopicId );
		}
		return $success;
	}

	/**
	 * ɾ������ר��
	 * 
	 * @param int $stopicId ר��id
	 * @return bool �Ƿ�ɹ�
	 */
	function deleteSTopicById($stopicId) {
		$stopicDB = $this->_getSTopicDB();
		$stopicPicturesDB = $this->_getSTopicPicturesDB();
		$stopicUnitDB = $this->_getSTopicUnitDB();

		$stopicData = $stopicDB->get($stopicId);
		if (null == $stopicData) return false;
		$isSuccess = (bool) $stopicDB->delete($stopicId);
		if ($isSuccess && $stopicData['bg_id']) $stopicPicturesDB->increaseField($stopicData['bg_id'], 'num', -1);
		if ($isSuccess) {
			$stopicUnitDB->deleteAll($stopicId);
			$this->_delFile($this->getStopicDir($stopicId, $stopicData['file_name']));
		}
		return $isSuccess;
	}

	/**
	 * ɾ���ļ�
	 * 
	 * @access protected
	 * @see P_unlink
	 * @param string $fileName �ļ���
	 * @return bool �Ƿ�ɹ�
	 */
	function _delFile($fileName) {
		return P_unlink($fileName);
	}

	/**
	 * ����ר���¼
	 * 
	 * @param int $stopicId ר��id
	 * @param array $updateData Ҫ���µ���������
	 * @return bool �Ƿ��и���
	 */
	function updateSTopicById($stopicId, $updateData) {
		$stopicDB = $this->_getSTopicDB();
		$stopicPicturesDB = $this->_getSTopicPicturesDB();
		$stopicData = $stopicDB->get($stopicId);
		if (null == $stopicData) return false;

		$isSuccess = (bool) $stopicDB->update($stopicId,$updateData);
		if (isset($updateData['bg_id']) && $updateData['bg_id'] != $stopicData['bg_id']) {
			if ($stopicData['bg_id']) $stopicPicturesDB->increaseField($stopicData['bg_id'], 'num', -1);
			if ($updateData['bg_id']) $stopicPicturesDB->increaseField($updateData['bg_id'], 'num');
		}
		if (isset($updateData['file_name'])) {
			$stopicDB->updateFileName($stopicId, $updateData['file_name']);
			if ($updateData['file_name'] != $stopicData['file_name'] && '' != $stopicData['file_name']) {
				$this->_delFile($this->getStopicDir($stopicId, $stopicData['file_name']));
			}
		}
		return $isSuccess;
	}

	/**
	 * ��ȡר����Ϣ
	 * 
	 * @param $stopicId
	 * @return array|null ר����������
	 */
	function getSTopicInfoById($stopicId) {
		$stopicDB = $this->_getSTopicDB();

		$stopic = $stopicDB->get($stopicId);
		if ($stopic) $stopic['bg_url'] = $stopic['bg_id'] ? $this->_getBackgroundUrl($stopic['bg_id']) : "";

		return $stopic;
	}

	/**
	 * ��ȡ��ר�⣨ר��δ����κ����ݣ�
	 * 
	 * @return array|null ר����������
	 */
	function getEmptySTopic() {
		$stopicDB = $this->_getSTopicDB();
		$stopic = $stopicDB->getEmpty();
		return $stopic;
	}

	/**
	 * ��ȡר�����
	 * 
	 * @param string $keyword ��ѯ�ؼ���
	 * @param int $categoryId ����id
	 * @return int
	 */
	function countSTopic($keyword = '', $categoryId = 0) {
		$stopicDB = $this->_getSTopicDB();
		return $stopicDB->countByKeyWord ($keyword, $categoryId);
	}

	/**
	 * ��ҳ��ѯר��
	 * 
	 * @param int $page �ڼ�ҳ
	 * @param int $perPage ÿҳ��¼��
	 * @param string $keyword �ؼ���
	 * @param int $categoryId ����id
	 * @return array ר�����ݶ�ά����
	 */
	function findSTopicInPage($page, $perPage, $keyword = '', $categoryId = 0) {
		$stopicDB = $this->_getSTopicDB();
		$page = intval ( $page );
		$perPage = intval ( $perPage );
		if ($page <= 0 || $perPage <= 0) return array ();
		$result	= $stopicDB->findByKeyWordInPage($page, $perPage, $keyword, $categoryId);
		foreach ($result as $key=>$value) {
			$result[$key]['url'] = $this->getStopicUrl($value['stopic_id'], $value['file_name']);
			$result[$key]['create_date'] = get_date($value['create_date']);
		}
		return $result;
	}

	/**
	 * ���ݷ����ҳ��ȡ��Ч��ר���б�
	 * 
	 * @param int $page �ڼ�ҳ
	 * @param int $perPage ÿҳ��¼��
	 * @param int $categoryId ����id
	 * @return array ר�����ݶ�ά����
	 */
	function findValidCategorySTopicInPage($page, $perPage, $categoryId = 0) {
		$stopicDB = $this->_getSTopicDB();
		$page = intval ( $page );
		$perPage = intval ( $perPage );
		if ($page <= 0 || $perPage <= 0)
			return array ();

		return $stopicDB->findValidByCategoryIdInPage ( $page, $perPage, $categoryId );
	}

	/**
	 * ���ݷ����ҳ��ȡʹ���ʸߵ�ר���б�
	 * 
	 * @param int $limit ����
	 * @param int $categoryId ����id
	 * @return array ר�����ݶ�ά����
	 */
	function findUsefulSTopicInCategory($limit, $categoryId = 0) {
		$stopicDB = $this->_getSTopicDB();
		$limit = intval ( $limit );
		if ($limit <= 0) return array ();

		return $this->_lardBackground($stopicDB->findByCategoryIdOrderByUsedInPage(1, $limit, $categoryId));
	}

	/**
	 * �õ�������ר�����ݴ��Ŀ¼
	 * 
	 * @param int $stopic_id ר��id
	 * @param string $file_name �ļ���
	 * @return string �ļ�·��
	 */
	function getStopicDir($stopic_id, $file_name='') {
		$stopic_id = (int) $stopic_id;
		if (!$stopic_id) return false;
		if ('' == $file_name) $file_name = $stopic_id;
		$stopicDir	= S::escapePath($this->_getSTopicConfig('htmlDir'));
		if (!file_exists($stopicDir)) {
			if (mkdir($stopicDir)) {
				@chmod($stopicDir,0777);
			} else {
				showmsg('stopic_htm_is_not_777');
			}
		}
		return $stopicDir.'/'.$file_name.$this->_getSTopicConfig('htmlSuffix');
	}

	/**
	 * ��ȡר���url
	 * 
	 * @param int $stopic_id ר��id
	 * @param string $file_name �ļ���
	 * @return string|bool ר��url
	 */
	function getStopicUrl($stopic_id, $file_name) {
		if ('' == $file_name) return false;
		$stopicDir = $this->getStopicDir($stopic_id, $file_name);
		if ($stopicDir && file_exists($stopicDir)) {
			return $this->_getSTopicConfig('htmlUrl').'/'.$file_name.$this->_getSTopicConfig('htmlSuffix');
		} else {
			return false;
		}
	}

	/**
	 * ��ȡר���html����
	 * 
	 * @param int $stopic_id ר��id
	 * @param bool $ifadmin �Ƿ��Ǻ�̨����ʱʹ�õ�html
	 * @return string
	 */
	function getStopicContent($stopic_id,$ifadmin) {
		$stopic	= $this->getSTopicInfoById($stopic_id);
		$units	= $this->getStopicUnitsByStopicId($stopic_id);
		$blocks	= $this->getBlocks();

		$parseStopicTpl	= L::loadClass('ParseStopicTpl','stopic');
		$tpl_content	= $parseStopicTpl->exute($this,$stopic,$units,$blocks,$ifadmin);
		return $tpl_content;
	}
	
	/**
	 * ר�Ᵽ����ļ��Ƿ�ʹ��
	 * 
	 * @param int $stopicId ר��id
	 * @param string $fileName �ļ���
	 * @return bool
	 */
	function isFileUsed($stopicId, $fileName) {
		$stopicId = intval($stopicId);
		$stopicDB = $this->_getSTopicDB();
		$isFind = $stopicDB->getByFileNameAndExcept($stopicId, $fileName);
		return $isFind && file_exists($this->getStopicDir($stopicId, $fileName));
	}

	/**
	 * ����һ��ר�����
	 *
	 * @param array $fieldData ר�������������
	 * @return int ����id��ʧ�ܷ���0
	 */
	function addCategory($fieldData) {
		$stopicCategoryDB = $this->_getSTopicCategoryDB();
		return $stopicCategoryDB->add($fieldData);
	}

	/**
	 * ����һ��ר�����
	 *
	 * @param array $fieldData ר�������������
	 * @param int $categoryId ����id
	 * @return int|null �Ƿ���³ɹ�
	 */
	function updateCategory($fieldData, $categoryId) {
		$stopicCategoryDB = $this->_getSTopicCategoryDB();
		$categoryId = intval ( $categoryId );
		if ($categoryId<= 0) {
			return NULL;
		}
		return $stopicCategoryDB->update($fieldData,$categoryId);
	}

	/**
	 * ɾ��һ��ר����� ͬʱ���±�������
	 *
	 * @param int $categoryId ����id
	 * @return int
	 */
	function deleteCategory($categoryId) {
		$stopicPicturesDB = $this->_getSTopicPicturesDB();
		$stopicCategoryDB = $this->_getSTopicCategoryDB();

		$categoryId = intval ( $categoryId );
		if ($categoryId <= 0 || ! $this->isAllowDeleteCategory ( $categoryId )) {
			return NULL;
		}
		return ($stopicCategoryDB->delete ( $categoryId )) ? $stopicPicturesDB->updateByCategoryId ( array("categoryid"=>0),$categoryId ) : NULL;
	}

	/**
	 * �Ƿ�����ɾ������
	 * 
	 * Ĭ��ר�ⲻ��ɾ��/�����������ר�ⲻ��ɾ��
	 *
	 * @param int $categoryId ����id
	 * @return bool
	 */
	function isAllowDeleteCategory($categoryId) {
		$stopicDB = $this->_getSTopicDB();
		$stopicCategoryDB = $this->_getSTopicCategoryDB();
		if ($stopicDB->countByCategoryId($categoryId)) return false;
		$category = $stopicCategoryDB->get($categoryId);
		if (!$category || $category['status'] == 1) return false;
		return true;
	}

	/**
	 * ��ȡ����ר�����
	 *
	 * @return array ר������ά����
	 */
	function getCategorys() {
		$stopicCategoryDB = $this->_getSTopicCategoryDB();
		return $stopicCategoryDB->gets ();
	}

	/**
	 * ��ȡĳ��������Ϣ
	 *
	 * @param int $categoryId ����id
	 * @return array ר�������������
	 */
	function getCategory($categoryId) {
		$stopicCategoryDB = $this->_getSTopicCategoryDB();
		return $stopicCategoryDB->get ( $categoryId );
	}

	/**
	 * �������Ƿ����
	 * 
	 * @param string $categoryName ��������
	 * @return bool
	 */
	function isCategoryExist($categoryName) {
		$stopicCategoryDB = $this->_getSTopicCategoryDB();
		return $stopicCategoryDB->getByName($categoryName) ? true : false;
	}

	/**
	 * �ϴ�����ͼƬ ������һ��ͼƬ ��¼
	 *
	 * @param array $fileArray �ϴ��ļ���������
	 * @return string �ļ�����[20090819152809.jpg]
	 */
	function uploadPicture($fileArray, $categoryId, $creator) {
		$stopicPicturesDB = $this->_getSTopicPicturesDB();
		$uploadPictureClass = $this->_setUploadPictureClass();
		if (count ( $fileArray ) < 0 || intval ( $categoryId ) < 0 || trim ( $creator ) == "") {
			return null;
		}
		$filename = $uploadPictureClass->upload ( $fileArray );
		if ($filename === FALSE) {
			return null;
		}
		$fieldData = array (
			"title" => time(),
			"categoryid" => intval($categoryId),
			"path" => trim ($filename),
			"creator" => $creator
		);
		return $stopicPicturesDB->add ( $fieldData );
	}

	/**
	 * ��ȡ�ļ��ϴ���
	 * 
	 * @access protected
	 */
	function _setUploadPictureClass() {
		$tempUpdatePicture = L::loadClass('UpdatePicture');
		$tempUpdatePicture->init($this->_getSTopicConfig ('bgUploadPath'));
		return $tempUpdatePicture;
		//return new UpdatePicture ($this->_getSTopicConfig ('bgUploadPath'));
	}

	/**
	 * ���±���ͼƬ
	 *
	 * @param int $fieldData ����ͼƬ��������
	 * @param int $pictureId ����ͼƬif
	 * @return int|null
	 */
	function updatePicture($fieldData, $pictureId) {
		$stopicPicturesDB = $this->_getSTopicPicturesDB();
		$pictureId = intval ( $pictureId );
		if ($pictureId <= 0) {
			return NULL;
		}
		return $stopicPicturesDB->update($fieldData,$pictureId);
	}

	/**
	 * ɾ������ͼƬ ɾ�����ݲ�ɾ������ͼƬ
	 *
	 * @param int $pictureId
	 * @return int|null
	 */
	function deletePicture($pictureId) {
		$stopicPicturesDB = $this->_getSTopicPicturesDB();
		$uploadPictureClass = $this->_setUploadPictureClass();
		$pictureId = intval ( $pictureId );
		if ($pictureId <= 0) return null;
		if (!$this->isAllowDeletePicture($pictureId)) return null;
		$picture = $stopicPicturesDB->get($pictureId);
		if (!$picture) return null;
		return ($stopicPicturesDB->delete ( $pictureId )) ? $uploadPictureClass->delete ( $picture ['path'] ) : "";
	}

	/**
	 * �Ƿ�����ɾ������ͼƬ
	 *
	 * @param int $pictureId
	 * @return bool
	 */
	function isAllowDeletePicture($pictureId) {
		$stopicDB = $this->_getSTopicDB();
		return $stopicDB->countByBackgroundId($pictureId) ? false : true;
	}

	/**
	 * ��ȡ����ͼƬ
	 *
	 * @param int $categoryId ����id��Ϊ0��������
	 * @return array
	 */
	function getPictures($categoryId = 0) {
		$stopicPicturesDB = $this->_getSTopicPicturesDB();
		$categoryId = intval ( $categoryId );
		if ($categoryId < 0) return array();

		return $this->_lardBackground( $categoryId
			? $stopicPicturesDB->getsByCategoryId ($categoryId)
			: $stopicPicturesDB->gets() );
	}

	/**
	 * ��ҳ��ȡר�ⱳ��ͼƬ
	 * 
	 * @param int $page
	 * @param int $perPage
	 * @param int $categoryId ����id
	 * @return array ����ͼƬ��ά����
	 */
	function getBackgroundsInPage($page, $perPage, $categoryId=0) {
		$stopicPicturesDB = $this->_getSTopicPicturesDB();
		return $this->_lardBackground($stopicPicturesDB->getsInPage($page, $perPage, $categoryId));
	}

	/**
	 * ��ȡϵͳĬ�ϱ���ͼƬ���û��ϴ��ı���ͼƬ�б�
	 * 
	 * ϵͳĬ�ϱ���ͼƬ��idΪ��������-1,-2
	 * 
	 * @param int $categoryId ����id
	 * @return array ����ͼƬ��ά����
	 */
	function getPicturesAndDefaultBGs($categoryId = 0) {
		$defaults = $this->_getDefaultBackGrounds();
		$thisTypePictures = $this->getPictures($categoryId);
		return array_merge($defaults,$thisTypePictures);
	}

	/**
	 * ��ȡ����ͼƬurl
	 * 
	 * @access protected
	 * @param int $bgId ����ͼƬid
	 * @return string
	 */
	function _getBackgroundUrl($bgId) {
		$stopicPicturesDB = $this->_getSTopicPicturesDB();
		if ($bgId<0) return $this->_getDefaultBackgroundUrl($bgId);

		$bg = $stopicPicturesDB->get($bgId);
		return $bg['path'] ? $this->_getSTopicConfig ('bgBaseUrl') . $bg ['path'] : "";
	}

	/**
	 * ��ȡϵͳĬ�ϱ���ͼƬurl
	 * 
	 * @access protected
	 * @param int $bgId ����ͼƬid
	 * @return string
	 */
	function _getDefaultBackgroundUrl($bgId) {
		$bgId = (int) $bgId;
		$bgId = abs($bgId);
		if (file_exists($this->_getSTopicConfig('bgDefalutPath').$bgId.'.jpg')) {
			return $this->_getSTopicConfig('bgDefalutUrl').$bgId.'.jpg';
		}
		return '';
	}

	/**
	 * ��ȡϵͳĬ�ϱ���ͼƬ�б�
	 * 
	 * @access protected
	 * @return array
	 */
	function _getDefaultBackGrounds() {
		$backPath = $this->_getSTopicConfig('bgDefalutPath');
		$fp	= opendir($backPath);
		$backs	= array();

		while ($back = readdir($fp)) {
			if (in_array($back,array('.','..')) || !strpos($back,'.jpg')) continue;
			$id	= $this->_getDefaultBackGroudId($back);
			$backs[] = array(
				'id'	=> $id,
				'categoryid'	=> 'defalut',
				'thumb_url'	=> $this->_getDefaultBackgroundUrl($id)
			);
		}
		return $backs;
	}

	/**
	 * ��ȡϵͳĬ�ϱ���ͼƬid
	 * 
	 * Ŀǰ��ת������ΪͼƬ��תΪ������ΪͼƬid
	 * 
	 * @access protected
	 * @param string $filename ϵͳĬ�ϱ���ͼƬ��
	 * @return int
	 */
	function _getDefaultBackGroudId($filename) {
		$temp = (int) $filename;
		if (!$temp || $temp<0) return false;
		return 0-$temp;
	}

	/**
	 * ͳ�Ʊ���ͼƬ����
	 *
	 * @param int $categoryId ����id��Ϊ0��ͳ������
	 * @return int
	 */
	function countPictures($categoryId = 0) {
		$stopicPicturesDB = $this->_getSTopicPicturesDB();
		return $categoryId ? $stopicPicturesDB->countByCategoryId($categoryId) : $stopicPicturesDB->count();
	}

	/**
	 * �ӹ�����ͼƬ����
	 * 
	 * ��������������ͼƬurl
	 * 
	 * @access protected
	 * @param array $bgList ����ͼƬ����
	 * @return array
	 */
	function _lardBackground($bgList) {
		foreach ($bgList as $key => $bg) {
			$bgList[$key]['thumb_url'] = $bg['path'] ? $this->_getSTopicConfig('bgBaseUrl') . "thumb_" . $bg ['path'] : "";
		}
		return $bgList;
	}

	/**
	 * ��ȡģ�������б�
	 * 
	 * @return ģ�������б�
	 */
	function getBlocks() {
		return $this->_getSTopicConfig('blockTypes');
	}

	/**
	 * ��ȡģ������
	 * 
	 * @param string $typeId ģ��������
	 * @return array
	 */
	function getBlockById($typeId) {
		$blockTypes = $this->_getSTopicConfig('blockTypes');
		return $blockTypes[$typeId];
	}

	/**
	 * ���ר��ģ��
	 * 
	 * @param array $fieldData ģ����������
	 * @return int ģ��id
	 */
	function addUnit($fieldData) {
		$stopicUnitDB = $this->_getSTopicUnitDB();
		return $stopicUnitDB->add($fieldData);
	}

	/**
	 * ����ģ������
	 * 
	 * @param int $stopic_id ר��id
	 * @param string $html_id ģ����html�е�id
	 * @param array $fieldData ��������
	 * @return bool �Ƿ���³ɹ�
	 */
	function updateUnitByFild($stopic_id,$html_id,$fieldData) {
		$stopicUnitDB = $this->_getSTopicUnitDB();
		return $stopicUnitDB->updateByFild($stopic_id,$html_id,$fieldData);
	}

	/**
	 * ɾ�����ר��ģ��
	 * 
	 * @param int $stopic_id ר��id
	 * @param array $html_ids ģ��id����
	 * @return int ɾ������
	 */
	function deleteUnits($stopic_id,$html_ids) {
		$stopicUnitDB = $this->_getSTopicUnitDB();
		return $stopicUnitDB->deletes($stopic_id,$html_ids);
	}

	/**
	 * ��ȡר��ģ���б�
	 * 
	 * @param int $stopic_id ר��id
	 * @return array
	 */
	function getStopicUnitsByStopicId($stopic_id) {
		$stopicUnitDB = $this->_getSTopicUnitDB();
		return $stopicUnitDB->getStopicUnits($stopic_id);
	}

	/**
	 * ��ȡר���һ��ģ������
	 * 
	 * @param int $stopic_id ר��id
	 * @param string $html_id ģ��id
	 * @return array
	 */
	function getStopicUnitByStopic($stopic_id,$html_id) {
		$stopicUnitDB = $this->_getSTopicUnitDB();
		return $stopicUnitDB->getByStopicAndHtml($stopic_id,$html_id);
	}

	/**
	 * ��ȡģ��htmlģ������
	 * 
	 * @param array $block_data ģ���������
	 * @param string $block_type ģ������
	 * @param int $block_id ģ��id
	 * @return string
	 */
	function getHtmlData($block_data, $block_type, $block_id=null) {
		$block_job = 'show';
		include S::escapePath(A_P."/template/admin/block/$block_type.htm");
		$output = ob_get_contents();
		ob_clean();
		return $output;
	}

	/**
	 * ����ר�����ݿ��������
	 * 
	 * @access protected
	 * @return PW_STopicDB
	 */
	function _getSTopicDB() {
		return L::loadDB('STopic', 'stopic');
	}
	
	/**
	 * ����ר�ⱳ��ͼƬ���ݿ��������
	 * 
	 * @access protected
	 * @return PW_STopicPicturesDB
	 */
	function _getSTopicPicturesDB() {
		return L::loadDB('STopicPictures', 'stopic');
	}
	
	/**
	 * ����ר��������ݿ��������
	 * 
	 * @access protected
	 * @return PW_STopicCategoryDB
	 */
	function _getSTopicCategoryDB() {
		return L::loadDB('STopicCategory', 'stopic');
	}
	
	/**
	 * ����ר��ģ�����ݿ��������
	 * 
	 * @access protected
	 * @return PW_STopicUnitDB
	 */
	function _getSTopicUnitDB() {
		return L::loadDB('STopicUnit', 'stopic');
	}

	/**
	 * ��ȡר������
	 * 
	 * @access protected
	 * @param string $key ��ȡר�����õ��Ϊ�����ȡ����
	 * @return mixed ����ֵ
	 */
	function _getSTopicConfig($key = '') {
		if (null == $this->_stopicConfig) {
			$this->_stopicConfig = include A_P."config.php";
		}
		if ($key) {
			return $this->_stopicConfig[$key];
		}
		return $this->_stopicConfig;
	}
}

