function postBoard(){
	var title = getObj('board').value;
	var uid = getObj('board_uid').value;
	if (typeof(uid) == "undefined") {
		showDialog('error','û��Ҫ���ԵĶ���');
		return false;
	}
	if (title.length < 3) {
		showDialog('error','����������������3�ֽ�');
		return false;
	}
	
	if (title.length > 200) {
		showDialog('error','�����������ö���200�ֽ�');
		return false;
	}
	
	if (uid == winduid) {
		showDialog('error','�����Լ����Լ�����');
		return false;
	}
	ajax.send('apps.php?q=ajax&a=postboard','&uid='+uid+'&title='+ajax.convert(title),function(){
		var rText = ajax.runscript(ajax.request.responseText);
		if (rText.indexOf('<') != -1){
			var createboardbox = getObj('createboardbox');
			createboardbox.innerHTML = rText + createboardbox.innerHTML;
			getObj('board').value = '';
		} else {
			ajax.guide();
		}
	});
}
function delBoard(bid){
	if (isNaN(bid)) {
		showDialog('error','����');
	}
	ajax.send('apps.php?q=ajax&a=delboard','id='+bid,function(){
		var rText = ajax.request.responseText;
		if (rText=='success') {
			delElement('board_'+bid);
		} else {
			ajax.send();
		}
	});
}
function creatBoard(container,bid,face,title){
	if (isNaN(bid)) {
		showDialog('error','����');
	}
	container = objCheck(container);
	var dl	= elementBind('dl','board_'+bid,'cc');
	var dt	= elementBind('dt');
	var img	= elementBind('img','','img-50');
	img.src	= face;
	var img_a = elementBind('a');
	img_a.href = 'u.php';
	img_a.appendChild(img);
	dt.appendChild(img_a);
	var dd  = elementBind('dd','','dd60');
	var del_a = elementBind('a','','del fr mr10','cursor: pointer;');
	del_a.setAttribute('onclick',"pwConfirm('�Ƿ�ȷ��ɾ����������',this,function(){delBoard('"+bid+"')})");
	del_a.innerHTML = 'ɾ��';
	var username_a = elementBind('a','','b');
	username_a.href = 'u.php';
	username_a.innerHTML = windid;
	var postdate_span = elementBind('span','','gray');
	var date = new Date();
	var thispost = dateFormat(date,'yyyy-mm-dd hh:ii:ss');
	postdate_span.innerHTML = thispost+': ';
	var title_p = elementBind('p','','f14');
	title_p.innerHTML = title;
	
	dd.appendChild(del_a);
	dd.appendChild(username_a);
	dd.appendChild(postdate_span);
	dd.appendChild(title_p);
	dl.appendChild(dt);
	dl.appendChild(dd);
	/*
	var dd2 = elementBind('dd','','dd30 ddbor');
	var username_a = elementBind('a');
	username_a.href = 'mode.php?q=user';
	username_a.innerHTML = windid;
	var postdate_span = elementBind('span','','f10 gray');
	var date = new Date();
	var thispost = dateFormat(date,'yyyy-mm-dd hh:ii:ss');
	postdate_span.innerHTML = thispost+': ';
	var br = elementBind('br');
	var title_span = elementBind('span');
	title_span.innerHTML = title;
	dd2.appendChild(username_a);
	dd2.appendChild(postdate_span);
	dd2.appendChild(br);
	dd2.appendChild(title_span);
	dl.appendChild(dt);
	dl.appendChild(dd);
	dl.appendChild(dd2);
	*/
	var createboardbox = getObj('createboardbox');
	createboardbox.insertBefore(dl,createboardbox.firstChild);
}