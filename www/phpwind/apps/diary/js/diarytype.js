//�Զ���������������
function getDiaryTypeConfig() {
	this.createUrl = "apps.php?q=ajax&a=adddiarytype";
	this.delUrl = "apps.php?q=ajax&a=deldiarytype";
	this.upUrl = "apps.php?q=ajax&a=eidtdiarytype";
}
var typeConfig = new getDiaryTypeConfig();



function add_dtid(u,id) {/*д��־ҳ�������*/
	if (isNaN(u) && winduid != u) {
		showDialog('error','�Ƿ�����');
		read.menu.style.top="450px";
	}
	ajax.send('apps.php?q=ajax&a=adddiarytype','u='+u+'&b=1',function(){
		var rText = ajax.request.responseText.split('\t');
		if (rText[0]=='success') {
			if (rText[1]=='1') {
				read.setMenu(create_dtid(id));
				read.menupz();
				read.menu.style.top="450px";
			} else {
				showDialog('error','�Ƿ�����');
				read.menu.style.top="450px";
			}
		}
	});
}

function create_dtid(id) {/*д��־ҳ�������*/
	selid = objCheck(id);
	var maindiv	= elementBind('div','','','width:300px;height:100%');
	var title = elementBind('div','','popTop');
	title.innerHTML = '������־����';
	maindiv.appendChild(title);
	var innerdiv = addChild(maindiv,'div','','p10');
	var ul = addChild(innerdiv,'ul','');
	var li = addChild(ul,'li');
	li.innerHTML = '��������';

	var text= elementBind('input','','input','margin-left:10px');
	text.setAttribute('type','text');
	li.appendChild(text);
	var innername = document.createTextNode(' С��20�ֽ�');
	li.appendChild(innername);
	text.focus();

	var footer	= addChild(maindiv,'div','','popBottom','');
	var tar	= addChild(footer,'div','','tar');
	var ok	= elementBind('span','','btn2','');
	ok.setAttribute('type','button');
	ok.innerHTML = '<span><button type="button">ȷ��</button></span>';


	ok.onclick	= function () {
		var name = char_cv(text.value);
		if (name.length<1) {
			showDialog('error','<font color="red">����</font> ���Ʋ���Ϊ��');
			read.menu.style.top="450px";
			return false;
		}
		ajax.send('apps.php?q=ajax&a=adddiarytype','u='+winduid+'&name='+ajax.convert(name),function(){
			var rText = ajax.request.responseText.split('\t');
			if (rText[0] == 'success') {
				var dtid = rText[1] - 0;
				if (isNaN(dtid)==false) {
					var option = elementBind('option');
					option.innerHTML = name;
					getObj('dtid_add').parentNode.getElementsByTagName('a')[0].innerHTML= name;
					//var innername = document.createTextNode(name);
					//option.appendChild(innername);
					option.value = dtid;
					option.selected = 'selected';
					selid.insertBefore(option,null);
					showDialog('success','������ӳɹ�!',2);
					read.menu.style.top="450px";
					closep();
				} else {
					showDialog('error','�Ƿ�����');
					read.menu.style.top="450px";
				}
			} else {
				ajax.guide();
			}
		});
	}

	var cansel	= elementBind('span','','bt2','');
	cansel.type	= 'button';
	cansel.innerHTML= '<span><button type="button">ȡ��</button></span>';

	cansel.onclick	= closep;

	tar.appendChild(ok);
	tar.appendChild(cansel);

	return maindiv;
}

function optionsel(id,ifsendweibo) {/*Ȩ��ѡ��*/
	copy = objCheck('if_copy');
	if (isNaN(id)) {
		showDialog('error','�Ƿ�����');
	}
	if (id == '0') {
		copy.disabled = '';
		copy.checked = 'checked';
	} else if (id == '1') {
		copy.disabled = '';
		copy.checked = 'checked';
	} else if (id == '2') {
		copy.disabled = 'disabled';
		copy.checked = '';
	}
	if(ifsendweibo){
		sendweibo = objCheck('lab_weibo');
		if(id == '0'){
			sendweibo.style.display = '';
			sendweibo.checked = true;
		}else{
			sendweibo.style.display = 'none';
			sendweibo.checked = false;
		}
	}
}

function delDiary(id,u,space){/*ɾ����־*/
	ajax.send('apps.php?q=ajax&a=deldiary&id='+id+'&u='+u,'',function(){
		var rText = ajax.request.responseText.split('\t');
		if (rText[0] == 'success') {
			var element = document.getElementById('diary_'+id);
			if (element) {
				element.parentNode.removeChild(element);
				if (space != 2) {
					window.location.href = basename;
				}
			} else {
					window.location.reload();
			}
		} else {
			ajax.guide();
		}
	});
}

function Copydiary(did,dtid,privacy) {/*��־ת��*/
	ajax.send('apps.php?q=ajax&a=copydiary&did='+did+'&dtid='+dtid+'&privacy='+privacy,'',function(){
		var rText = ajax.request.responseText.split('\t');
		if (rText[0]=='success') {
			read.setMenu(create_copy(rText[1]));
			read.menupz();
		} else {
			ajax.guide();
		}
	});
}

function create_copy(did) {/*ת����ʾ*/
	var maindiv	= elementBind('div','','','width:300px;');
	var title = elementBind('div','','popTop');
	title.innerHTML = 'ת����ʾ';
	maindiv.appendChild(title);
	var innerdiv = addChild(maindiv,'div','','p15');
	var ul = addChild(innerdiv,'ul','');
	var li = addChild(ul,'li');
	li.innerHTML = 'ת�سɹ�����־�Ѵ����ҵ���־�У��Ƿ�Ҫȥ�����';

	var footer	= addChild(maindiv,'div','','popBottom','');
	var tar	= addChild(footer,'div','','tar');


	var ok	= elementBind('span','','btn2','');
	ok.innerHTML = '<span><button type="button">ȷ��</button></span>';

	ok.onclick	= function () {
		window.location.href = 'apps.php?q=diary&a=detail&did='+did;
	}


	var cansel	= elementBind('span','','bt2','');
	cansel.innerHTML = '<span><button type="button">�ر�</button></span>';
	cansel.onclick	= closep;

	tar.appendChild(ok);
	tar.appendChild(cansel);

	return maindiv;
}

function ajaxpage(url,type,u,space) {/*�����־*/
	ajax.send(url,'',function() {
		var rText = ajax.request.responseText.split('\t');
		if (rText[0] == 'success') {
			if (rText[1]) {
				var tourl = rText[2];
				window.location.href = tourl + 'did=' + rText[1];
			} else {
				ajax.request.responseText = type == 'next' ? '�Ѿ������һƪ��־' : '�Ѿ��ǵ�һƪ��־';
				ajax.guide();
			}
		} else {
			ajax.guide();
		}
	});
	return false;
}



function deldiaryatt(did,aid) {
	if(!confirm('ȷ��Ҫɾ���˸�����')) return false;
	ajax.send('apps.php?q=diary&ajax=1','action=delatt&did='+did+'&aid='+aid,function(){
		if (ajax.request.responseText == 'success') {
			var o = getObj('att_'+aid);
			o.parentNode.removeChild(o);
			showDialog('success','ɾ���ɹ�!',2);
		} else {
			ajax.guide();
		}
	});
}

