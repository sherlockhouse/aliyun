var portal = {
	channel : "channel",
	invoke : "invokename",
	subinvoke : "subinvoke",
	column : "column",
	invokebox : "invokebox",
	portalbox : "portalbox",
	doing : '',
	/* ����ѡ���� */
	$ : function(id){
		return document.getElementById(id);
	},
	/* ��ȡ���������ͱ����ݽӿ�  */
	getGrade : function(data,action){
		var action = action ? action : "pushto";
		var url = "mode.php?m=area&q=manage&ajax=1&action="+action;
		if (typeof   doing   !=   'undefined') {
			this.doing = doing;
		}
		
		if (this.doing!='') {
			url = url+"&doing="+this.doing;
		}
		var data = data ? data : '';
		var _this = this;
		ajax.send(url,data,function() {
			if("fetch" == action){
				//var portalbox = _this.$(_this.portalbox);
				//portalbox.innerHTML = ajax.runscript(ajax.request.responseText);
				_this.setPortalbox(ajax.request.responseText);
				_this.initFormPushkey();
			}else{
				var haystack = ajax.request.responseText.split("\t");
				_this.pushto(haystack);
			}	
		});
	},
	setPortalbox : function(text){
		var text = text ? text : '';
		var portalbox = this.$(this.portalbox);
		portalbox.innerHTML = text;
		if (getObj('imagetype_ul')) {
			var imageTypeImp = New(imageType,['imagetype_ul']);
		}
	},
	/* ��װ���ͱ����� */
	pushto : function(haystack){
		if(haystack && haystack[0] == 4){
			showDialog("error",haystack[1]);
		}else if(haystack && haystack[0] == 1){
			this.$(this.invoke).outerHTML  = haystack[1];
			this.$(this.subinvoke).outerHTML  = (haystack[2]) ? haystack[2] : this._select();
			//this.$(this.column).outerHTML  = haystack[3];
			this.initNormal();
		}else if(haystack && haystack[0] == 2){
			this.$(this.subinvoke).outerHTML  = (haystack[2]) ? haystack[2] : this._select();
			this.initNormal();
		}
	},
	_select : function(){
		return '<select id="subinvoke"><option>ѡ��λ��</option></select>';
	},
	/* ��ʼ�� */
	init : function(){
		this.initNormal();
		this.initForm();
		this.initFormPushkey();
	},
	/* ������ʼ�� */
	initNormal : function(){
		this.initChannel();
		this.initInvoke();
		this.initSubInvoke();
	},
	/* ��ʼ�������� */
	initForm : function(){
		if(initsubinvoke){
			var data = "&subinvoke="+initsubinvoke;
			this.getGrade(data,"fetch");
		}
	},
	/* ��Ƶ��onchange�¼� */
	initChannel : function(){
		var channel = this.$(this.channel);
		if(channel){
			var _this = this;
			channel.onchange = function(){
				var data = "&channelid="+this.value;
				_this.getGrade(data);
				_this.setPortalbox();
			};
		}
	},
	/* ��ģ��onchange�¼� */
	initInvoke : function(){
		var invoke = this.$(this.invoke);
		if(invoke){
			var _this = this;
			invoke.onchange = function(){
				var channelId = _this.$(_this.channel).value;
				var data = "&channelid="+channelId+"&invokename="+this.value;
				_this.getGrade(data);
				_this.setPortalbox();
			};
		}
	},
	/* ����ģ��onchange�¼� */
	initSubInvoke : function(){
		var subinvoke = this.$(this.subinvoke);
		if(subinvoke){
			var _this = this;
			subinvoke.onchange = function(){
				var channelId = _this.$(_this.channel).value;
				var invoke = _this.$(_this.invoke).value;
				var data = "&channelid="+channelId+"&invoke="+invoke+"&ifpush="+ifpush+"&selid="+selid+"&subinvoke="+this.value;
				if (pushdataid) {
					data += "&pushdataid="+pushdataid;
				}
				_this.getGrade(data,"fetch");
			};
		}
	},
	/* �󶨳�ʼ�����ύ�¼� */
	initFormSubInvoke : function(){
		var subinvoke = this.$("invokepieceid");
		if(subinvoke){
			var form = this.$("subinvokeform");
			subinvoke.onchange = function(){
				setTimeout(function(){
					 form.submit();
				 },0);
			};
		}
	},
	
	initFormPushkey : function(){
		var pushkeybutton = this.$("pushkeybutton");
		var pushkey = this.$("pushkey");
		if(pushkey){
			var _this = this;
			try{
				pushkeybutton.onclick = function(){
					if("" == pushkey.value){
						return ;
					}
					var channelId = _this.$(_this.channel).value;
					var invoke = _this.$(_this.invoke).value;
					var subinvoke = _this.$(_this.subinvoke).value;
					var data = "&channelid="+channelId+"&invoke="+invoke+"&ifpush=4&subinvoke="+subinvoke+"&selid="+pushkey.value;
					_this.getGrade(data,"fetch");
				};
			}catch(e){}
		}
	}
	
}
/*�Ż����������*/
var initPortal = function(){
	portal.init();
};
var initFormSubInvoke = function(){
	portal.initFormSubInvoke();
};
