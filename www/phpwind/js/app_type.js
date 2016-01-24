// JavaScript Document �������� ��־���ղء������Զ�������JS
var ismouseupval = 0;
function addType(id,u,type){
	if (getObj('dt_add')) {
		changeCreatInputDisplay('dt_add');
		return true;
	}
	creatDiaryTypeInput(id,u,type);
}
function creatDiaryTypeInput(id,u,type){
	var parent	= getObj(id);
	var li	= addChild(parent,'li','dt_add');//����<li id='dt_add'></li>

	var text= elementBind('input','','input','width:45px;');//����<input class="input" type="text">
	text.setAttribute('type','text');
	text.maxLength  = 17;
	li.appendChild(text);//����<li id='dt_add'><input class="txa dtwidth" type="text"></li>
	text.focus();

	var ok	= elementBind('span','','btn2','margin:0 0 0 3px');
	ok.innerHTML = '<span><button type="button">ȷ��</button></span>';
	
	ok.onclick	= function () {
		var name = char_cv(text.value);
		if (name.length<1) {
			showDialog('error','�������Ʋ���Ϊ��');
			return false;
		}
		ajax.send(typeConfig.createUrl,'u='+u+'&name='+ajax.convert(name),function(){
			var rText = ajax.request.responseText.split('\t');//����php�е�explode
			if (rText[0] == 'success') {
				var dtid = rText[1] - 0;
				if (isNaN(dtid)==false) {
					creatDiaryType('dt_-1',dtid,name,u,type);
					delElement('dt_add');
				} else {
					showDialog('error','�Ƿ�����');
				}
			} else {
				ajax.guide();
			}
		});
	}
	li.appendChild(ok);//����<li id='dt_add'><input class="bt3" type="button" value="ȷ��" onclick=""></li>
	var cancel	= elementBind('span','','bt2','margin:0 0 0 3px');
	cancel.innerHTML = '<span><button type="button">ȡ��</button></span>';

	cancel.onclick	= function () {
		text.value = '';
		li.style.display = 'none';
	}
	li.appendChild(cancel);
}

function changeCreatInputDisplay(id){
	var dt_add = getObj(id);
	if (dt_add.style.display =='none') {
		dt_add.style.display = '';
	} else {
		dt_add.style.display = 'none';
	}
}
function creatDiaryType(latter,dtid,name,u,type){
	latter = objCheck(latter);
	var parent = latter.parentNode;//ul
	var li = elementBind('li','dt_'+dtid);//����<li id='dt_1' onmouseover="changSpanDisplay(this);" onmouseout="changSpanDisplay(this);"></li>
	li.style.overflow = "hidden";
	li.onmouseout = function(){isMouseUp(dtid,1);};
	li.onmouseover = function(){isMouseUp(dtid,0)};
	addUrl = 'apps.php?q=diary&dtid='+dtid;
	if (type == 'collection') {
		addUrl = 'apps.php?q=collection&a=list&ftype='+dtid;
	}
	li.innerHTML = '<a style="visibility:hidden" title="ɾ��" class="adel cp mr5" id ="del_'+dtid+'"   onclick="pwConfirm(\'�Ƿ�ȷ��ɾ��\',this,function(){delType(\''+dtid+'\',\''+u+'\')})">ɾ��</a><a style="visibility:hidden" title="�༭" id ="edit_'+dtid+'" class="aedit mr5 cp"  onclick="showEidt(\''+dtid+'\',\''+u+'\');">�༭</a><a href="'+addUrl+'">'+name+'</a> <cite id="dnum_'+dtid+'">[0]</cite>';
	parent.insertBefore(li,latter);//��ul�У�id="dt_null"��liǰ��̬������liһ��
}

function delType(dtid,u){

	ajax.send(typeConfig.delUrl,'u='+u+'&dtid='+dtid,function(){
		var rText = ajax.request.responseText;
		if (rText == 'success') {
			delElement('dt_'+dtid);
		} else {
			ajax.guide();
		}
	});
}

function changSpanDisplay(id){
	id = objCheck(id);
	var spans = id.getElementsByTagName('span');
	for (var i=0;i<spans.length;i++) {
		if (spans[i].style.display == 'none') {
			spans[i].style.display = '';
		} else {
			spans[i].style.display = 'none';
		}
	}
}
function showEidt(dtid,u){
	ismouseupval = 1;
	var li = getObj('dt_'+dtid);
	if(getObj('dnum_'+dtid)){
		getObj('dnum_'+dtid).style.display = 'none';
	}

	var spans = li.getElementsByTagName('span');
	for (var i=0;i<spans.length;i++) {
		spans[i].style.display = 'none';
	}
	var a = li.getElementsByTagName('a');
	for (var i=0;i<a.length;i++) {
		a[i].style.display = 'none';
	}

	var text= elementBind('input','','input','width:45px;');
	text.setAttribute('type','text');
	text.maxLength  = 17;
	text.value	= a[i-1].innerHTML;
	li.insertBefore(text,a[0]);
	text.focus();
	var ok	= elementBind('span','','btn2','margin:0 0 0 3px');
	ok.innerHTML = '<span><button type="button">ȷ��</button></span>';


	var cancel	= elementBind('span','','bt2','margin:0 0 0 3px');
	cancel.innerHTML = '<span><button type="button">ȡ��</button></span>';

	ok.onclick	= function () {
		var name = char_cv(text.value);
		if (name.length<1) {
			showDialog('error','�������Ʋ���Ϊ��');
			return false;
		}
		ajax.send(typeConfig.upUrl,'u='+u+'&name='+name+'&dtid='+dtid,function(){
			var rText = ajax.request.responseText;
			if (rText == 'success') {
				if(getObj('dnum_'+dtid)){
					getObj('dnum_'+dtid).style.display = '';
				}
				cancelEdit(li,name);
			} else {
				ajax.guide();
			}
		});
		ismouseupval = 0;
	}

	cancel.onclick	= function () {
		getObj('dnum_'+dtid).style.display = '';
		cancelEdit(li);
	}
	li.insertBefore(ok,a[0]);
	li.insertBefore(cancel,a[0]);

	
}


function cancelEdit(li,name){
	li = objCheck(li);
	delElement(li.getElementsByTagName('input')[0]);
	var span = li.getElementsByTagName('span');
	var i = 0;
	while (i<span.length) {
		delElement(span[i]);
		i++;
	}
	changSpanDisplay(li);
	var a = li.getElementsByTagName('a');
	for (var i=0;i<a.length;i++) {
		if (name) {
			a[i].innerHTML	= name;
		}
		a[i].style.display	= '';
	}
}

//����ƶ���ȥ��ʾ���ƿ�����ʾ
function isMouseUp(k,type) {
	var del = getObj("del_"+k);
	var edit = getObj("edit_"+k);
	if (ismouseupval == 0) {
		if (type == 0) {
			del.style.visibility = "visible";
			edit.style.visibility = "visible";
		} else {
			del.style.visibility = "hidden";
			edit.style.visibility = "hidden";
		}
	}
}

