function delActivity(id){/*ɾ���*/
	ajax.send('apps.php?q=activity&a=delactivity&action=delactivity&ajax=1&id='+id,'',function(){
		var rText = ajax.request.responseText.split('\t');
		if (rText[0] == 'success') {
			var element = document.getElementById('activity_'+id);
			if (element) {
				element.parentNode.removeChild(element);
				showDialog('success','ɾ���ɹ�!', 2);
			} else {
				window.location.reload();
			}
		} else if (rText[0] == 'mode_o_delactivity_permit_err') {
			showDialog('error','��û��Ȩ��ɾ�����˵Ļ', 2);
		} else {
			showDialog('error','ɾ��ʧ��', 2);
		}
	});
}