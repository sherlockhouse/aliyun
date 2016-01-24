/**
* @fileoverview ��������
*
* @author yuyang <yuyangvi@gmail.com>
* @version 1.0
*/
Breeze.namespace('util.dialog', function(B){
	var dialogDef = {
		/**
		 * Ĭ�ϵĵ���ID
		 * @type String
		 */
		id: 'pw_box',
		/**
		 * �Ƿ��ظ������Ѿ������ĵ���
		 * @type Boolean
		 */
		reuse: true,
		/**
		 * ����������HTML
		 * @type String
		 */
		data: null,
		/**
		* ��λ����
		* @type Array
		* �ĸ�Ԫ�ص����飬�ֱ�����
		* <ol><li>��һ��Ԫ��Ϊ'left'(��������),'leftalign'�����ض��룩,'center'(����),'right'���������У�,'rightalign'�����ض��룩�е�һ��,</li>
		* <li>�ڶ���Ԫ��Ϊ'top'(��������),'topalign'(���ض���),'center'(����),'bottom'(��������),'bottomalign'(���ض���)�е�һ��</li>
		* <li>������Ԫ��Ϊ���֣���ʾ���ҵ�ƫ��ֵ,����������Ϊ0</li>
		* <li>���ĸ�Ԫ��Ϊ���֣���ʾ���µ�ƫ��ֵ,����������Ϊ0</li></ol>
		*/
		pos: ['center','center',0,0],
		/**
		 * ��λ���õĲ�����
		 * @type HTMLElement
		 */
		posrel: null,
		/**
		 * ����ƿ��Զ��ر�
		 * @type Boolean
		 */
		autoHide: false, 
		/**
		 * ����
		 * @type Boolean
		 */
		mask: false,
		/**
		 * �����������ִ�к����������ô˰��¼������档
		 * @type Function
		 */
		callback: null,
		outWin: false
	};
	
	/**
	 * @class ������
	 */
	function Mask(){
		this.mask;
	}
	Mask.prototype={
		create: function(){
			if(this.mask){
				B.css(this.mask, 'display', '');
			}else{
				this.mask =  B.createElement('div', {}, {
					position: 'absolute',
					top: 0,
					left: 0,
					width: B.width(window),
					height: B.height(window),
					'background-color': '#000000',
					opacity: 0.6
				});
				var self = this;
				B.require('event', function(B){
					B.addEvent( window, 'resize', function(){
						self.resize.call(self);
					});
				}); 
				document.body.appendChild(this.mask);
			}
		},
		closep: function(){
			B.css(this.mask, 'display', 'none');
		},
		distory: function(){
			B.remove(this.mask);
		},
		resize: function(){
			B.css(this.mask,{
				width: B.width(window),
				height: B.height(window)
			});
		}
	};
	var mask = new Mask();
	/**
	 * ������
	 */
	function Dialog(setting, ele)
	{
		if( !(this instanceof Dialog) ){
			return new Dialog(setting, ele);
		}
		/**
		 * ���յ�����
		 * @private {Setting}
		 */
		var popwin, self = this;
		B.merge(self, dialogDef, setting);
		ele && (self.posrel = ele);
		
		/*
		* IE6����select
		if(B.UA.ie === 6) {
		    B.$$('select').forEach(function(n){
		        n.style.visibility = 'hidden';
		    });
		}
		*/
		/**
		 * չ������
		 */
		B.require('dom', function(B){
			var popwin = B.$('#' + self.id);
			//�趨����
			if(self.mask){
				mask.create();
			}
			if (!popwin || !self.reuse){//�������û����
				//���ɵ���
				popwin = B.$query(B.createElement(self.data))(B.attr, 'id', self.id)(B.css,{position:'absolute',display:'none','z-index':'99999'})();
				
				//�趨�߿�
				self.width && B.css(popwin, 'width', self.width);
				self.height && B.css(popwin, 'height', self.height);
				document.body.appendChild(popwin);
				//�󶨹ر��¼�
				B.require('event', function(B){
					B.$$query('.B_close', popwin)(B.addEvent, 'click', function(e){
						self.closep();
						e.preventDefault();
					});
				});
			}else{
				B.css(popwin, {display:'none'});
			}
			//��ʾ
			B.query(popwin)
				.layerOut(self.pos, self.posrel, self.outWin)
				.css({display:'block', backgroundColor:'#ffffff'});
			self.win = popwin;
			self.callback && self.callback(self);
			//���¼�
			if(self.autoHide){
				B.require('event',function(B){
					var stopp = function(e){
						e.stopPropagation();
					};
					var closep = function(e){
						self.closep();
						B.removeEvent(document, 'click', closep);
						B.removeEvent(self.posrel, 'click', stopp);
						B.removeEvent(popwin,   'click', stopp);
					}
					B.addEvent(document, 'click', closep);
					B.addEvent(self.posrel, 'click', stopp);
					B.addEvent(popwin,   'click', stopp);
				});
			}
			
			//���϶�
			if(B.$('.B_drag_handle', popwin)){
				B.require('util.draggable', function(){
					B.util.draggable('#'+self.id, '.B_drag_handle');
				});
			}
		});
		
		return self;
	}
	
	Dialog.prototype = {
		closep: function(){
			if(this.reuse){
				B.css(this.win, 'display', 'none');
			} else {
				B.remove(this.win);
			}
			this.mask && mask.closep();
			var self = this;
			//ie6 select����
			if(B.UA.ie === 6) {
		        B.$$('select').forEach(function(n){
		            n.style.visibility = 'visible';
		        });
		    }
			//TODO:�����ڴ�
			setTimeout(function(){delete self;}, 0);
			//delete this;
		}
	};
	
	B.util.dialog = Dialog;

	/**
	 * ��λ��λ��
	 * @type Array
	 */
	function layerOut(popwin, pos, rel, isOutWin)
	{
		var res = rel ? B.offset(rel) : 
			{
				left: (pos[0].indexOf('left') < 0) ? B.width(document.body) : 0,
				top:  (pos[1].indexOf('top') < 0) ? B.height(document) : 0
			};
		//���������ҳ��������У����б�Ϊ����
		if (!rel) {
			['left','right'].indexOf(pos[0])>-1 && (pos[0]+='Align');
			['top','bottom'].indexOf(pos[1])>-1 && (pos[1]+='Align');
		}
		
		//����X��λ��
		if (pos[0].indexOf('right') > -1 || pos[0]=='center') {
			pos[0] == 'center' && rel && (res.left *= 2);
			rel && (res.left += B.width(rel));
		}

		if (['left', 'rightAlign', 'center'].indexOf(pos[0])>-1){
			res.left -= B.width(popwin);
			pos[0] == 'center' && (res.left /= 2);
		}
		//res.left += B.scrollLeft();
		
		//����Y��λ��
		if (pos[1].indexOf('bottom') > -1 || pos[1]=='center') {
			pos[1] == 'center' && rel && (res.top *= 2);
			rel && (res.top += B.height(rel));
		}

		if (['top', 'bottomAlign','center'].indexOf(pos[1])>-1) {
			res.top -= B.height(popwin);
			pos[1] == 'center' && (res.top /= 2);
		}
		//res.top += B.scrollTop();
		
		//����ƫ��
		pos[2] && (res.left += parseInt(pos[2]));
		pos[3] && (res.top += parseInt(pos[3]));
		
		//��ֹ�ƶ�����Ļ����
		if(!isOutWin){
			if( res.left < B.scrollLeft() ){
				res.left = B.scrollLeft();
			}else if( res.left > B.scrollLeft()+B.width(window)-B.width(popwin) ){
				res.left = B.scrollLeft()+B.width(window)-B.width(popwin);
			}
			
			if( res.top < B.scrollTop() ){
				res.top = B.scrollTop();
			}else if( res.top > B.scrollTop()+B.height(window)-B.height(popwin) ){
				res.top = B.scrollTop()+B.height(window)-B.height(popwin);
			}
		}
		B.css(popwin, res);
	}
	B.extend('layerOut', function() {
		var arg = B.makeArray(arguments),finalEls = [];
		for(var i = 0,j = this.nodes.length; i < j; i++) {
			var el = this.nodes[i],
				result = layerOut.apply(el,[el].concat(arg));
			finalEls = finalEls.concat(B.makeArray(result || []));
		}
		//����ǻ�ȡԪ��,���µ�ǰthis.el
		if(finalEls.length > 0){
			this.nodes = finalEls;
			this.length = finalEls.length;
		}
		return this;
	});
	/**
	 * ���Ѻ;���
	 */
	B.util.alert = function(str, elem, p){
		Dialog({
			pos: p ? p : ['center', 'center'],
			id:'dialog-alert',
			data:'<div class="B_menu B_dialog_alert B_p10B"><div style="width:200px;"><h4 class="B_h B_drag_handle">��ʾ</h4><p class="B_mb10">'+str+'</p><div class="tac"><span class="B_btn2"><span><button type="button" class="B_close">�ر�</button></span></span></div></div></div>',
			reuse: true/*,
			//mask:true*/
		},elem);
	}
});