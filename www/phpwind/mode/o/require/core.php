<?php
!defined('P_W') && exit('Forbidden');
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
		return parent::_loadClass($className, 'mode/o/lib/' . parent::_formatDir($dir), $isGetInstance);
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
