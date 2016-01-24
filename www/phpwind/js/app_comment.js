var comment_case = '';
var comment_handel = '';

var PW_Comment = {

	/**
	 *  ��̬����¼�ȴ���ʵ����ظ�����ʾ�ظ��б�ͻظ���
	 *  @author zhudong
	 *  @param  type		���۵Ķ���
	 *  @param  id			���۶����ID
	 *  @return string      ҳ������ʾ��HTML
	*/
	showCommList : function(type,id,a){
		var otherreplynum = type == 'groupwrite' ? 3 : 1;
		var commentBox = getObj('combox_'+type+'_'+id);
		if (type == 'groupwrite') {
			this._getGroupWriteCommentHTML(type,id);
		} else {
			this._getCommentHTML(type,id);
		}
		
		a.innerHTML = '����ظ�';
		a.onclick	= function () {
			var replynum = commentBox.getElementsByTagName('dl').length - otherreplynum;
			this.innerHTML = '�ظ�(' + replynum + ')';
			this.onclick = function() {
				if (type == 'groupwrite') {
					if (getObj('comm_list_'+type+'_'+id).style.display == 'none') {
						getObj('comment_short_box_'+id).style.display = 'none';
						getObj('comm_list_'+type+'_'+id).style.display = '';
						this.innerHTML	= '����ظ�';
					} else {
						getObj('comment_short_box_'+id).style.display = '';
						getObj('comm_list_'+type+'_'+id).style.display = 'none';
						var replynum = commentBox.getElementsByTagName('dl').length - otherreplynum;
						this.innerHTML	= '�ظ�(' + replynum + ')';
						//this.onclick = this.showCommList(type,id,a);
					}
				} else {
					if (commentBox.style.display=='none') {
						commentBox.style.display = '';
						this.innerHTML	= '����ظ�';
					} else {
						commentBox.style.display = 'none';
						var replynum = commentBox.getElementsByTagName('dl').length - 1;
						this.innerHTML	= '�ظ�(' + replynum + ')';
					}
				}
			};
			if (type == 'groupwrite') {
				getObj('comment_short_box_'+id).style.display = '';
				getObj('comm_list_'+type+'_'+id).style.display = 'none';
			} else {
				commentBox.style.display = 'none';
			}
		}
		setTimeout(function() {
			try{
				getObj('comm_input_'+ type+'_'+id+'_0').focus();
			}catch(e){}
		}, 1000);

		if(IsElement('upPanel')){
			getObj('upPanel').scrollTop+= 50;
		}else{
			document.documentElement.scrollTop += 50;
		}

	},


	/**
	 *  ��ȡ���۵�HTML
	 *  @author zhudong
	 *  @param  type		���۵Ķ���
	 *  @param  id			���۶����ID
	 *  @return string      ���۵�����
	*/

	_getCommentHTML : function(type,id) {
		ajax.send('apps.php?q=ajax&a=showcommlist','type='+type+'&id='+id,function(){
			var rText = ajax.runscript(ajax.request.responseText);
			var commentBox = getObj('combox_'+type+'_'+id);
			commentBox.innerHTML = rText;
		});
	},


	/**
	 *  ��ȡ���۵�HTML
	 *  @author zhudong
	 *  @param  type		���۵Ķ���
	 *  @param  id			���۶����ID
	 *  @return string      ���۵�����
	*/

	_getGroupWriteCommentHTML : function(type,id) {
		ajax.send('apps.php?q=ajax&a=showgroupwritecommlist','type='+type+'&id='+id,function(){
			var rText = ajax.runscript(ajax.request.responseText);
			var commentBox = getObj('combox_'+type+'_'+id);
			commentBox.innerHTML = rText;
		});
	},
	
	/**
	 *  ɾ�����۷��صĽ��
	 *  @author zhudong
	 *  @return null      
	*/

	delOneCommentResponse : function() {
		
		var rText = ajax.request.responseText.split('\t');
		if (rText[0] == 'success') {
			delElement('comment_'+rText[1]);
		} else {
			ajax.guide();
		}
	},

	/**
	 *  ɾ�������۷��صĽ��
	 *  @author zhudong
	 *  @return null      
	*/

	delOneSubCommentResponse : function() {
		
		var rText = ajax.request.responseText.split('\t');
		if (rText[0] == 'success') {
			delElement('subcomment_'+rText[1]);
		} else {
			ajax.guide();
		}
	},

	_getContent : function (type,typeid,upid) {
		var inputObj = getObj('comm_input_'+type+'_'+typeid+'_'+upid)
		var content = inputObj.value;
		return content;
	
	},



	/**
	 *  ����һ������
	 *  @author zhudong
	 *  @param type  ������������
	 *  @param typeid ������������ID
	 *  @param upid ���۵��ϼ�ID
	 *  @param position ���۵���λ��
	 *  @return null      
	*/

	sendComment : function(type,typeid,upid,position) {

		var content = this._getContent(type,typeid,upid);
		this._checkContent(content);
		if (this._checkContent(content) == false) return false;
		this._getOneNewComment(type,typeid,upid,position,content);

	},
	
	/**
	 *  ��ȡһ���µ����۵�HTML
	 *  @author zhudong
	 *  @return HTML      
	*/

	_getOneNewComment : function(type,typeid,upid,position,title){
		
		ajax.send('apps.php?q=ajax&a=commreply','type='+type+'&id='+typeid+'&upid='+upid+'&position='+position+'&title='+ajax.convert(title),function(){
			var oneCommentHtml = ajax.request.responseText;
			oneCommentHtml = this.runscript(oneCommentHtml);
			if (oneCommentHtml.indexOf('<') != -1){
				if(position == 1) {
					insertComment_1(oneCommentHtml,type,typeid);			
				} else if (position == 2){
					insertComment_2(oneCommentHtml,type,typeid);
				} else if (position == 3) {
					insertComment_3(oneCommentHtml,type,upid);		
				} else if (position == 4) {
					insertComment_4(oneCommentHtml,type,typeid);		
				} else if (position == 5) {
					insertComment_5(oneCommentHtml,type,typeid);		
				}
				var inputObj = getObj('comm_input_'+type+'_'+typeid+'_'+upid);
				inputObj.value = '';
				if(typeof getonmouseout!='undefined'){
					getonmouseout();
				}
			} else {
				ajax.guide();
			}
		});
	
	},


	/**
	 *  ����һ������
	 *  @author zhudong
	 *  @return null      
	*/

	_checkContent : function(content) {
		if (content) {
			var content_length = strlen(content);
			if (content_length < 3) {
				showDialog('error','��������������������3�ֽ�','2');
				return false;
			} else if (content_length >= 200) {
				showDialog('error','���������������ܶ���200�ֽ�','2');
				return false;
			}
		} else {
			showDialog('error','���ݲ���Ϊ��');
			return false;
		}

	},

	createCommentInput : function(type,typeid,upid) {
		
		var commentInputObj = getObj('comment_input_'+type+'_'+typeid+'_'+upid);

		if (commentInputObj.style.display == 'none') {
			commentInputObj.style.display = '';
			var comm_input = getObj('comm_input_'+type+'_'+typeid+'_'+upid);
			comm_input.focus();
			if(IsElement('upPanel')){
				getObj('upPanel').scrollTop+= 50;
			}else{
				document.documentElement.scrollTop += 50;
			}

		} else {
			
			commentInputObj.style.display = 'none';
			
		}
	
	},

	simpleReply : function (type,typeid,username) {
		getObj('comm_input_'+type+'_'+typeid+'_0').value = '�ظ�'+username+':';
		if(window.getSelection && is_webkit)
		{
			getObj('comm_input_'+type+'_'+typeid+'_0').select();
			var sel = window.getSelection();
			sel.collapseToEnd();
			delete sel;
		}else{
			getObj('comm_input_'+type+'_'+typeid+'_0').focus();
		}
	}

}

function showObj(id){
	if(!IsElement(id)){ return false;}
	getObj(id).style.display = '';
}

function hiddenObj(id){
	if(!IsElement(id)){ return false;}
	getObj(id).style.display = 'none';
}


function insertComment_1(oneCommentHtml,type,typeid) {
	var commListBoxObj = getObj('comm_list_'+type+'_'+typeid);
	commListBoxObj.innerHTML = oneCommentHtml + commListBoxObj.innerHTML;
}


function insertComment_2(oneCommentHtml,type,typeid) {

	var createcommentbox = getObj('createcommentbox');
	createcommentbox.innerHTML = oneCommentHtml + createcommentbox.innerHTML;
	if(IsElement('comment_num')) {
		var commentNum = getObj('comment_num').innerHTML;
		commentNum = parseInt(commentNum) + 1;
		getObj('comment_num').innerHTML = commentNum;
	}

}


function insertComment_3(oneCommentHtml,type,upid) {
	
	var createcommentbox = getObj('subcommentlist_'+type+'_'+upid);
	createcommentbox.innerHTML = createcommentbox.innerHTML + oneCommentHtml;

}


function insertComment_4(oneCommentHtml,type,typeid) {
	
	var createcommentbox = getObj('subcommentlist_'+type+'_'+typeid);
	createcommentbox.innerHTML = createcommentbox.innerHTML + oneCommentHtml;

}

function insertComment_5(oneCommentHtml,type,typeid) {
	
	var commListBoxObj = getObj('commlist_'+type+'_'+typeid);
	commListBoxObj.innerHTML = commListBoxObj.innerHTML + oneCommentHtml;

}