<?php
!defined('P_W') && exit('Forbidden');
!defined('CMS_BASEURL') && define("CMS_BASEURL", "index.php?m=cms&");;

/**
 * @param string $_page ��ǰҳ����Ϣ(list,view)
 * @param string $_definedSeo �Զ���SEO������Ϣ
 * @param string $_fname �������
 * @param string $_types ������Ϣ
 * @param string $_subject ��������
 * @param string $_tags ��ǩ
 * @param string $_summary ժҪ
 */
function cmsSeoSettings($_page = 'index', $_definedSeo = '', $_column = '', $_subject = '', $_tags = '', $_summary = '') {
	global $cms_sitename, $cms_seoset;
	/* ��վ���ƣ���Ŀ���ƣ��������ƣ���ǩ���ƣ����¸�Ҫ  */
	$_targets = array('{wzmc}', '{lmmc}', '{armc}', '{tmc}', '{wzgy}');
	$_replace = array($cms_sitename, $_column, $_subject, $_tags, $_summary);
	
	/*��ȡSEO������Ϣ  �Զ���->��̨����->Ĭ��*/
	empty($_definedSeo['title']) &&	$_definedSeo['title'] = $cms_seoset['title'][$_page];
	empty($_definedSeo['metaDescription']) && $_definedSeo['metaDescription'] = $cms_seoset['metaDescription'][$_page];
	empty($_definedSeo['metaKeywords'])	&& $_definedSeo['metaKeywords'] = $cms_seoset['metaKeywords'][$_page];
	
	/*�����������Ϊ�������Ĭ������*/
	$_default = array('title' => '{armc} | {lmmc} - {wzmc}', 'descp' => '{wzgy} | {armc}', 
		'keywords' => '{tmc} , {armc} | {lmmc} - {wzmc}');
	
	return seoSettings($_definedSeo, $_replace, $_default, $_targets);
}

/**
 * ��ȡ��������Ȩ��
 * @param string $username
 * @param array $_G
 * return bool
 */
function getPostPurview($username, $_G) {
	if (isGM($username)) return true;
	if (isset($_G['cms_post']) && $_G['cms_post']) return true;
	return false;
}

/**
 * ����û��Ƿ��й�������Ȩ��
 * @param unknown_type $name
 * @param unknown_type $cid
 * @return string|string|string|boolean
 */
function checkEditPurview($name, $cid='') {
	if (isGM($name)) return true;
	if (!$name) return false;
	$cms_editadmin = L::config('cms_editadmin', 'cms_config');
	if (!S::isArray($cms_editadmin)) return false;
	if (empty($cid)) {
		$_keys = array_keys($cms_editadmin);
		foreach ($_keys as $key) {
			if (S::inArray($name, $cms_editadmin[$key])) return true;
		}
		return false;
	}
	return S::inArray($name, $cms_editadmin[$cid]);
}

/**
 * ��ȡҳ��λ��
 * @param $cid
 * @param $id
 * @param $columns
 */
function getPosition($cid, $id = 0, $columns = array(), $cms_sitename = '') {
	if (!$columns) {
		$columnService = C::loadClass('columnservice');
		$columns = $columnService->findAllColumns();
	}
	$postion = $cms_sitename ? "<a href='index.php?m=cms'>$cms_sitename</a>":'<a href="index.php?m=cms">��Ѷ</a>';
	if (!$cid) {return $postion . '<em>&gt;</em>�����б�';}
	$columnLists = getColumnList($columns, $cid);

	foreach ($columnLists as $value) {
		$postion .= '<em>&gt;</em><a href="' . getColumnUrl($value['column_id']) . '">' . $value['name'] . '</a>';
	}
	if (!$id) {return $postion;}
	return $postion . '<em>&gt;</em>��������';
}

/**
 * ��ȡ��Ŀ���ӵ�ַ
 * @param unknown_type $cid
 */
function getColumnUrl($cid) {
	$cid = (int) $cid;
	return CMS_BASEURL.'q=list&column=' . $cid;
}

/**
 * ��ȡ�������ӵ�ַ
 * @param $id
 */
function getArticleUrl($id) {
	$id = (int) $id;
	return CMS_BASEURL.'q=view&id=' . $id;
}

function getColumnList($columns, $cid) {
	static $list = array();
	if (!$cid) return $list;
	$thisColumn = $columns[$cid];
	
	array_unshift($list, $thisColumn);
	
	$parentColumnId = $thisColumn['parent_id'];
	
	return getColumnList($columns, $parentColumnId);
}

/**
 * ���������б�
 */
function updateArticleHits() {
	global $hitsize, $hitfile, $db;
	if (file_exists($hitfile)) {
		if (!$hitsize) $hitsize = @filesize($hitfile);
		if ($hitsize < 10240) {
			$hitarray = explode("\t", readover($hitfile));
			$hits = array_count_values($hitarray);
			$count = 0;
			$hits_a = '';
			foreach ($hits as $key => $val) {
				$hits_a .= ",('$key','$val')";
				if (++$count > 300) break;
			}
			if ($hits_a) {
				$hits_a = trim($hits_a, ', ');
				$db->query("CREATE TEMPORARY TABLE heap_hitupdate (article_id INT(10) UNSIGNED NOT NULL ,hits SMALLINT(6) UNSIGNED NOT NULL) TYPE = HEAP");
				$db->update("INSERT INTO heap_hitupdate (article_id,hits) VALUES $hits_a");
				$db->update("UPDATE pw_cms_articleextend as a, heap_hitupdate as h SET a.hits = a.hits+h.hits WHERE a.article_id=h.article_id");
				$db->query("DELETE FROM heap_hitupdate");
			}
			unset($hitarray, $hits, $hits_a);
		}
		pwCache::deleteData($hitfile);
	}
}

function checkReplyPurview() {
	global $_G,$windid;
	if (isGM($windid) || (isset($_G['cms_replypost']) && $_G['cms_replypost'])) return true;
	return false;
}

class cmsTemplate {
	
	var $dir;

	function cmsTemplate() {
		$this->dir = M_P . 'template/';
	}

	function getpath($template, $EXT = 'htm') {
		$srcTpl = $this->dir . 'default/' . "$template.$EXT";
		$tarTpl = D_P . "data/tplcache/cms_" . $template . '.' . $EXT;
		
		if (!file_exists($srcTpl)) return false;
		
		if (pwFilemtime($tarTpl) > pwFilemtime($srcTpl)) return $tarTpl;
		
		return modeTemplate($srcTpl, $tarTpl);
	}

	function getDefaultDir() {
		return $this->dir . 'default/';
	}

	//static function
	function printEot($template, $EXT = 'htm') {
		static $uTemplate = null;
		isset($uTemplate) || $uTemplate = new template(new cmsTemplate());
		return $uTemplate->printEot($template, $EXT);
	}
}

class C extends PW_BaseLoader {

	/**
	 * ���ļ��ļ������
	 * 
	 * @param string $className �������
	 * @param string $dir Ŀ¼��ĩβ����Ҫ'/'
	 * @param boolean $isGetInstance �Ƿ�ʵ����
	 * @return mixed
	 */
	function loadClass($className, $dir = '', $isGetInstance = true) {
		return parent::_loadClass($className, 'mode/cms/lib/' . parent::_formatDir($dir), $isGetInstance);
	}

	/**
	 * ����db��
	 * @param $className
	 */
	function loadDB($dbName, $dir = '') {
		parent::_loadBaseDB();
		return C::loadClass($dbName . 'DB', parent::_formatDir($dir) . 'db');
	}
}
?>