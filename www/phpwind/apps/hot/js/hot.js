function quickRateRequest(objId,typeId,option){
	var url = "hack.php?H_name=rate&action=ajax&job=hot&objectid="+objId+"&typeid="+typeId+"&optionid="+option;
	read.guide();
	ajax.send(url,'',function(){
		if (ajax.request.responseText == null) { 
			ajax.request.responseText = "��δ�����������۹���!";
		}
		ajax.guide();
	});
}
