/*
 * @fileoverflow event ģ��<br/>
 * ��event����ķ�װ�Ͷ��¼������ɾ��
 * @author chenchaoqu <chaoren1641@gmail.com>
 * @version 1.0 
 */
Breeze.namespace('event', function (B) {
    var win = window, doc = document, body = doc.body,

    // Is the DOM ready to be used? Set to true once it occurs.
	isReady = false,

    // The functions to execute on DOM ready.
	readyList = [],

    // Has the ready events already been bound?
	readyBound = false,
	cache = {},
	guid = 1;

    /*
    * ��չevent����
    */
    function _extentEvent(event) {
        //�޷���дeventĳЩ����,��target,�����¿���
        var e = {};
        for (var i in event) { e[i] = event[i]; }
        e.preventDefault = function () {
            if (event.preventDefault) event.preventDefault();
            else event.returnValue = false;
        };
        e.stopPropagation = function () {
            if (event.stopPropagation) event.stopPropagation();
            else event.cancelBubble = true;
        }

        e.halt = function () { e.preventDefault(); e.stopPropagation() };

        e.target = event.target || event.srcElement;

        var type = e.type;

        //check if target is a textnode (safari)
        while (e.target && e.target.nodeType == 3) e.target = e.target.parentNode;

        /*
        * ��IE�£�
        *   ֧��keyCode
        *   ��֧��which��charCode,����ֵΪ undefined
        * ��Firefox�£�
        *   ֧��keyCode�������ܼ��⣬������ֵʼ��Ϊ 0
        *   ֧��which��charCode�����ߵ�ֵ��ͬ
        * ��Opera�£�
        *   ֧��keyCode��which�����ߵ�ֵ��ͬ
        *   ��֧��charCode��ֵΪ undefined
        */
        e.keyCode = event.which || event.keyCode;

        //from mootools
        if (type.match(/(click|mouse|menu)/i)) {
            var win = window, doc = win.document;
            doc = (!doc.compatMode || doc.compatMode == 'CSS1Compat') ? doc.documentElement : doc.body;
            e.page = {
                x: e.pageX || e.clientX + doc.scrollLeft,
                y: e.pageY || e.clientY + doc.scrollTop
            };
            e.client = {
                x: (e.pageX) ? e.pageX - win.pageXOffset : e.clientX,
                y: (e.pageY) ? e.pageY - win.pageYOffset : e.clientY
            };
            if (e.type.match(/DOMMouseScroll|mousewheel/)) {
                e.wheelDelta = (event.wheelDelta) ? e.wheelDelta / 120 : -(e.detail || 0) / 3;
            }
            e.rightClick = (e.which == 3) || (e.button == 2);
            if (!event.relatedTarget && event.fromElement) {
                e.relatedTarget = (event.fromElement === event.target) ? event.toElement : event.fromElement;
            }
        };
        return e;
    }


    function _handleEvent(event) {
        var returnValue = true;
        event = _extentEvent(event || ((this.ownerDocument || this.document || this).parentWindow || window).event); //��չevent
        var handlers = this.events[event.type];
        for (var i in handlers) {
            this.$$handler = handlers[i];
            if (this.$$handler(event) === false) returnValue = false;
        }
        return returnValue;
    };


    B.mix(B, /** @lends Breeze */{
    /**
    * @description ����¼�
    * @see http://dean.edwards.name/weblog/2005/10/add-event2/
    * @params {Object} Ҫ����¼���Ԫ�ض���
    * @params {String} �¼�����
    * @params {Function} �¼�������
    */
    addEvent: function (element, type, handler) {
        if (!element || !type || typeof handler != "function") { return; } //�������Ϸ�
        //textNode and comment
        if (element.nodeType == 3 || element.nodeType == 8)
            return;

        if (B.UA.ie && element.setInterval)
            element = win;
        if (!handler.$$guid) handler.$$guid = guid++;
        if (!element.events) element.events = {};
        var handlers = element.events[type];
        if (!handlers) {
            handlers = element.events[type] = {};
            if (element['on' + type]) handlers[0] = element['on' + type];
            element['on' + type] = _handleEvent;
        }
        handlers[handler.$$guid] = handler;
        cache[handler.$$guid] = element; //���cache,ie unloadʱ��
    },

    /**
    * @description �Ƴ��¼�
    * @params {Object} Ҫ�Ƴ��¼���Ԫ�ض��� 
    * @params {String} �¼�����(��ѡ)
    * @params {Function} �¼�������(��ѡ)
    */
    removeEvent: function (element, type /* optional */, handler /* optional */) {
        // delete the event handler from the hash table
        if (!handler) {
            if (element.events && element.events[type])
                delete element.events[type];
        }
        if (!type) {
            for (var i in element.events) {
                delete element.events[i];
            }
        }
        if (element.events && element.events[type] && handler.$$guid) {
            delete element.events[type][handler.$$guid];
        }
    },

    /**
    * @description ���Ԫ��ʱѭ��������ͬ�¼�
    * @params {Object} Ҫ�����¼�Ԫ��
    * @params {Function} ��һ�ε�������ĺ���
    * @params {Function} �ڶ��ε�������ĺ���
    * @example B.get("#one")("goggleClick",fn1,fn2);
    */
    toggleClick: function (element, fn, fn2) {
        if (!fn2) { addEvent(element, "click", fn); }
        else {
            element.toggle = true;
            this.addEvent(element, "click", function (e) {
                element.toggle == true ? fn.call(this, e) : fn2.call(this, e);
                element.toggle = !element.toggle;
            });
        }
    },
    /**
    * @description �����ڻ򽫳��ֵ�Ԫ�ذ��¼�
    * @params {String} Ԫ��CSS2.1ѡ����
    * @params {String} �¼�����
    * @params {Function} �¼�������
    * @example B.get("#one")("live","click",fn);
    **/
    live: function (selector, type, fn) {
        var d = doc,
			atta = !!d.attachEvent,
			noBubble = /blur|focus/i.test(type);
        if (noBubble) {//if onblur or onfocus
            d = body;
            if (atta) { type += 'in'; } //if ie:focusin
        }
        var self = this;
        B.require('dom', function (B) {
            self.addEvent(d, type, function (e) {
                var elements = B.$$(selector),
					    el = e.target;
                for (var i = 0, j = elements.length; i < j; i++) {
                    if (elements[i] != d && elements[i] == el) {
                        fn.call(el, e);
                    }
                }
				e.preventDefault();
            });
        });
    },

    /**
    * @description �����ڻ򽫳��ֵ�Ԫ�ذ��¼�
    * @params {Object} Ҫ�����¼�Ԫ��
    * @params {String} �¼�����
    * @example B.get("#one")("live","click",fn);
    **/
	trigger: function (el, type){
		/*return B.UA.ie ? el[type]() : el['on'+type]({
			type: type,
			target: el
		});*/
		if (el.events && el.events[type]){
				var handles = el.events[type];
				for(var i in handles){
					var handle = handles[i];
					if(B.isFunction(handle)){
						var evt = {};
						evt.preventDefault = function(){};
						evt.target = el;
						handle.call(handle,evt);
					}
				}
			}
		}
});

    // Prevent memory leaks in IE
    if (win.attachEvent && !win.addEventListener) {
        win.attachEvent('onunload', function () {
            for (var i in cache) {
                B.removeEvent(cache[i]);
            }
        });
        //����<body onload="fn" ������
        doc.onreadystatechange = function () {
            if (win.onload && win.onload != _handleEvent) {
                B.addEvent(win, 'load', win.onload);
                win.onload = _handleEvent;
            }
        }
    }
    
    /*
    * ��ʽ
    */
	['addEvent','removeEvent','live'].forEach(function(p) {
        B.extend(p,function() {
            var arg = B.makeArray(arguments);
            for(var i = 0,j = this.nodes.length; i < j; i++) {
                var el = this.nodes[i];
                B[p].apply(el,[el].concat(arg));
            }
            return this;
        });
    });
});

/**
 * TODO:
 *   - live�Ѿ�ʵ��,��Ϊѡ�������Ҫʱʱˢ��,�ʲ�����ֻ�ܴ�ѡ��������,�����ܴ�HTMLElementList
 */