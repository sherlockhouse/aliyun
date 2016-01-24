/***************************
 * �°��ϴ��ļ�
 * @author yuyang
 * $Id$
 */
var uploader = {
	startId:0,
	mode:0,
	/**
	 * ��ʼ��
	 */
	init:function(){
		this.flash = swfobject.getObjectById('mutiupload');
	},
	/**
	 * �г����ϴ����ļ�
	 */
	list:function(queue)
	{
		var restLength = this.getRestCount();
		if(restLength<1){
			showDialog('warning','�������Ƭ�����ﵽ���ޣ��޷��ϴ�!',2);
			return false;
		}
		var qlist = document.getElementById('qlist');
		while(j = qlist.rows.length)
		{
			qlist.deleteRow(0);
		}
		for(var i=queue.length-1;i>=0;--i)
		{
			var tr = qlist.insertRow(0);
			var cel1 = tr.insertCell(0);
			cel1.innerHTML = queue[i].name;
			
			var cel2 = tr.insertCell(1);
			cel2.className='wname';
			var desc = queue[i].desc===undefined?queue[i].name:queue[i].desc;
			cel2.innerHTML = '<input type="text" value="'+desc+'" onchange="uploader.storage(this)" />';			
			var cel3 = tr.insertCell(2);
			cel3.innerHTML = uploader.getSize(queue[i].size);
			
			var cel4 = tr.insertCell(3);
			if (queue[i].error == '') {
				cel4.innerHTML = '0%';
			} else {
				switch (queue[i].error) {
					case 'exterror':
						cel4.innerHTML='<span class="s1">���Ͳ�ƥ��</span>';
						tr.error=1;
						break;
					case 'toobig':
						cel4.innerHTML='<span class="s1">��С��������</span>';
						tr.error=1;
						break;
				}
			}
			var cel5 = tr.insertCell(4);
			cel5.innerHTML = '<span onclick="uploader.mutidel(this)" class="updel" style="cursor:pointer;">x</span>';
		}
		this.countFile();
	},
	/**
	 * ɾ��flash�е��ļ�
	 */
	mutidel:function(ele){
		var y = ele.parentNode.parentNode.sectionRowIndex-uploader.startId;
		if(y>=0)
			uploader.flash.remove(ele.parentNode.parentNode.sectionRowIndex-uploader.startId);
	},
	/**
	 * �����С
	 */
	getSize:function(n)
	{
		var pStr = 'BKMGTPEZY';
		var i = 0;
		while(n>1024)
		{
			n=n/1024;
			i++;
		}
		var t = 3-Math.ceil(Math.log(n)/Math.LN10);
		return Math.round(n*Math.pow(10,t))/Math.pow(10,t)+pStr.charAt(i);  
	},
	/**
	 * ���ȿ���
	 */
	progress:function(i,percent)
	{
		document.getElementById('qlist').rows[this.startId+i].getElementsByTagName('td')[3].innerHTML = percent + '%';
	},
	//�����ļ��ϴ��ɹ�
	complete:function(i)
	{
		this.startId++;
	},
	//�����ϴ����
	finish:function(b){
		if(!b){//û�п����ϴ���ͼƬ
			closep();
			if (this.isAlbumFull == true) {
				showDialog('warning','�ϴ�ʧ�ܣ�������ѡ�����!',2);
			} else {
				showDialog('warning','�ϴ�ʧ�ܣ�������ѡ����Ƭ!',2);
			}
		}else{
			read.setMenu(uploader.jumpphoto(uploader.albumId));
			read.menupz();//showDialog('success','�ϴ��ɹ���',2);
		}
	},
	//�����ϴ����������Ŀ
	countFile:function()
	{
		var restLength = this.getRestCount();
		var qlist = document.getElementById('qlist');
		var item,i=0;
		while(qlist.rows[i])
		{
			if(!qlist.rows[i].error)
			{
				if(restLength > 0)
				{
					qlist.rows[i].cells[3].innerHTML ='0%';
					restLength--;
				}else{
					qlist.rows[i].cells[3].innerHTML ='<span class="s1">����������������</span>';
				}
			}
			i++;
		}
	},
	getRestCount:function(){
		return uploader.maxLength;
	},
	storage:function(e){
		uploader.flash.setDesc(e.parentNode.parentNode.sectionRowIndex-uploader.startId,e.value);
	},
	setLimits:function(i){
		uploader.maxLength = i.toString();
	},
	setAlbumId:function(i){
		uploader.albumId = i;
		uploader.flash.setAlbumId(parseInt(i));
	},
	jumpphoto : function(toaid) {
		var maindiv	= elementBind('div','','','width:300px;height:100%');
		var title = elementBind('div','','popTop');
		title.innerHTML = '�ϴ��ɹ�!';
		maindiv.appendChild(title);
		var innerdiv = addChild(maindiv,'div','','p15');
		var ul = addChild(innerdiv,'ul','');
		var li = addChild(ul,'li');
		li.innerHTML = '��Ƭ�ϴ��ɹ����Ƿ�����ϴ���<br />ע������������С�򳬹���������ϴ����ɹ���';

		var footer	= addChild(maindiv,'div','','popBottom','');
		var tar	= addChild(footer,'div','','');
		var ok	= elementBind('span','','btn2','');
		ok.innerHTML = '<span><button type="button">����</button></span>';	;

		ok.onclick	= function () {
			window.location.href = uploader.baseurl + 'a=upload&job=flash&aid=' + toaid;
		}

		var toview	= elementBind('span','','bt2','');
		toview.innerHTML = '<span><button type="button">���</button></span>';
		toview.onclick	= function () {
			window.location.href = uploader.baseurl + 'a=album&aid=' + toaid;
		}

		tar.appendChild(ok);
		tar.appendChild(toview);

		return maindiv;
	},
	error:function(s)
	{
		alert(s);
	}
};