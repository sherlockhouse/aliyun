<?php
!defined('P_W') && exit('Forbidden');
if (isset($db_modes['area'])) {
	$nav_left['area'] = array('name' => '�Ż�ģʽ', 
		'items' => array(
			/*'areaCore' => array('name' => '�Ż���������', 
				'items' => array('area_channel_manage', 'area_module', 'area_selecttpl', 'area_level_manage', 
					'area_static_manage')),*/
			'area_channel_manage',
			'area_module',
			//'area_selecttpl',
			'area_level_manage',
			'area_static_manage',
			'area_pushdata',
			//'area_page_manage'
		)
	);
}
?>