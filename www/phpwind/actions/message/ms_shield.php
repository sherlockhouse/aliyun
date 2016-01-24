<?php
!defined('P_W') && exit('Forbidden');
if(isset($_GET['ajax']) && $_GET['ajax'] == 1){
	define('AJAX','1');
}
empty($subtype) && $subtype = 'shield';
$normalUrl = $baseUrl."?type=shield";
!empty($winduid) && $userId = $winduid;
S::gp(array('action'), 'GP');

$app_array = getUserApplist();
$defaultShield = $messageServer->getDefaultShields($app_array);
$shieldHtml = createHTML($app_array);
$nav = $action ? array($action => 'class="current"'):array('shield'=>'class="current"');
$noticevoiceStatus = getstatus($winddb['userstatus'], PW_USERSTATUS_NOTICEVPICE); //��Ϣ��ʾ��״̬
$noticevoiceStatus = ($noticevoiceStatus == 1) ? "checked" : "";
if(empty($action)){
	if($_POST['step'] == 2){
		PostCheck();
		S::gp(array('shieldinfo','blacklist','blackgroup','noticevoice'), 'GP');
		//�Ƿ�����Ϣ��ʾ��
		$userService = L::loadClass("userservice", 'user');	
		$userService->setUserStatus($winduid, PW_USERSTATUS_NOTICEVPICE, (int)$noticevoice);
		//���εĺ�����
		if($shieldinfo && $messageServer->getMsKey('shieldinfo')){
			$newShield = createShield($shieldinfo);
			$shieldlist = compareShield($defaultShield,$newShield);
			$messageServer->setMsConfig(array('shieldinfo'=>serialize($shieldlist)),$userId);
		}
		//���ε��û���
		if($_G['msggroup'] && $messageServer->getMsKey('blackgroup')){	
			//* include_once pwCache::getPath(D_P.'data/bbscache/level.php');
			pwCache::getData(D_P.'data/bbscache/level.php');
			$blackInsert = array();
			foreach($ltitle as $key => $value){
				if(!$blackgroup[$key]){
					$blackInsert[] = $key;
				}
			}	
			$messageServer->setMsConfig(array('blackgroup'=>serialize($blackInsert)),$userId);			
		}
		//��������
		if($messageServer->getMsKey('blacklist')){
  			$blacklist = explode(',',$blacklist);		 	
			$messageServer->setMsConfig(array('blacklist'=>serialize($blacklist)),$userId);	
		}
		refreshto($normalUrl,'operate_success');
	}
    $config = $messageServer->getMsConfigs($userId);
    $config['shieldinfo'] && $shield = unserialize($config['shieldinfo']);
    $allShieldCheck = $shield ? 0 : 1; 
    $config['blacklist'] && $blacklist = implode(',',unserialize($config['blacklist']));
	if ($_G['msggroup']) {
	 	//* include_once pwCache::getPath(D_P.'data/bbscache/level.php');	
	 	pwCache::getData(D_P.'data/bbscache/level.php');	
		$config['blackgroup'] && $blackgroup = unserialize($config['blackgroup']);
		$allColonyCheck = $blackgroup ? 0 :1;
		$usergroup = '';
		foreach ($ltitle as $key => $value) {
				if($allColonyCheck){
					$checked = 'checked';
				}else{
					if ($blackgroup && in_array($key,$blackgroup)) {
						$checked = '';
					} else {
						$checked = 'checked';
					}
				}
				$usergroup .= "<li><input type=\"checkbox\" name=\"blackgroup[$key]\" value=\"$key\" $checked>$value</li>";
		}
	}	
}    
!defined('AJAX') && include_once R_P.'actions/message/ms_header.php';
require messageEot('shield');
if (defined('AJAX')) {
	ajax_footer();
} else {
	pwOutPut();
}

function compareShield($defaultShield,$newShield){
	$insertArray = array();
	foreach($defaultShield as $key => $value){
		if($newShield[$key]){
			$insertArray[$key] = $newShield[$key];
		}else{
			$insertArray[$key] = 0;
		}
	}
	return $insertArray;
}

function createShield($shield){
		$tmp = array();
		if(is_array($shield)){
			foreach($shield as $key => $value){
					foreach($value as $subkey=>$subvalue){
						if(is_array($subvalue)){
							foreach($subvalue as $thirdkey=>$thirdvalue){
								$tmp[$key.'_'.$subkey.'_'.$thirdkey] = 1;
							}
						}else{
							$tmp[$key.'_'.$subkey] = 1;
						}
					}
			}
		}else{
			$tmp = array($key);
		}
		return $tmp;
}
function createHTML($applist){
	$html = array(
	    'sms' => array(
	            'name' => 'վ����',
	            'value' => 1,
	            'sub' => array(
						/* modified for phpwind8.5
	                    'message' => array(
	                    			'name'=>'���Ѹ��ҿռ������','value'=>1
									 ),
						'comment' => array(
	                    		'name'=>'���Ѷ��ҵ�����',
	                    		'value'=>1,
	                    		'sub'=>array(
									'diary' => array('name'=>'���������ҵ���־','value'=>1),
									'photo' => array('name'=>'���������ҵ����','value'=>1),
									)
								),
						*/
			    		'ratescore'=>array('name'=>'����','value'=>1),
						'reply'=>array('name'=>'���ӻظ�','value'=>1)         	  
	                )
	
	        ),
	    'notice' => array(
	            'name'  => '֪ͨ',
	            'value' => 1,
	            'sub'   => array(
	         			'guestbook' => array(
	                    			'name'=>'�ҵĿռ�����������','value'=>1
									 ),
						'comment' => array(
	                    		'name'=>'�ҵĿռ�����������',
	                    		'value'=>1,
	                    		'sub'=>array(
									'diary' => array('name'=>'�ҵ���־����������','value'=>1),
									'photo' => array('name'=>'�ҵ��������������','value'=>1),
									)
								),	          
	                	'postcate' => array(
	                		'name' => '�Ź�֪ͨ','value' => 1
	                	),
	                	'active'  => array(
	                		'name' => '�֪ͨ','value' => 1
	                	),
	                	'website' => array(
	                		'name' => 'ϵͳ֪ͨ','value' => 1
	                	),
	                	'apps' => array(
	                		'name' => 'Ӧ��֪ͨ','value' => 1
	                	)
			    		     	  
	                )
	
	        ),
	    'request' => array(
	            'name'  => '����',
	            'value' => 1,
	            'sub'   => array(                  
				    'friend' => array('name' => '��������','value' => 1),
				    'group'  => array('name' => 'Ⱥ������','value' => 1),
				    //'apps'   => array('name' => 'Ӧ�ð�װ����','value' => 1)
	          	    )
	        )
	);
	if(!empty($applist)){
		$html['notice']['sub']['app']['name'] = 'Ӧ��֪ͨ';
		$html['notice']['sub']['app']['value'] = 1;
		foreach($applist as $key=>$value){
			$html['notice']['sub']['app']['sub'][$value['appid']]['name'] = $value['appname'];	
	 		$html['notice']['sub']['app']['sub'][$value['appid']]['value'] = 1;
	 	}
	}
	return $html;
}
?>