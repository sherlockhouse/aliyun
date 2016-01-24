/**
* @fileoverview �����ļ���������������������ͻ��������ķ�װ
* @author yuyang <yuyangvi@gmail.com>
* @version 1.0
*/
(function () {
    if (!window.Breeze) {
        /**
        * Function ��չ
        */
        Function.prototype.bind = function () {
            var fn = this, args = Array.prototype.slice.call(arguments, 0), object = args.shift();
            return function () {
                return fn.apply(object,
			  args.concat(Array.prototype.slice.call(arguments, 0)));
            };
        };
        /**
        * ʹIE���������indexOf����.
        * @memberOf Array
        * @returns ���֣���ʾԪ���������е�������ַ��������������оͷ���-1
        * @type int
        */
        if (!Array.prototype.indexOf) {
            Array.prototype.indexOf = function (elt /*, from*/) {
                var len = this.length >>> 0;

                var from = Number(arguments[1]) || 0;
                from = (from < 0)
				 ? Math.ceil(from)
				 : Math.floor(from);
                if (from < 0)
                    from += len;

                for (; from < len; from++) {
                    if (from in this &&
				  this[from] === elt)
                        return from;
                }
                return -1;
            };
        }
        window.isQueue = false;
        var loadQueue = [],
		modQueue = [],

		isLoading = false,
		loadingIndex = 0,
		runVerson = '1.0.1109',

		startQueue = function () {
		    if (loadQueue.length === 0) {
		        isQueue = false;
		        return;
		    }
		    isQueue = true;
		    var mod = loadQueue.shift();
		    (loadingIndex > 0) && (loadingIndex--);
		    switch (typeof mod) {
		        case 'string': //ģ��
		            if (modQueue.indexOf(mod) == -1) {
		                var script = document.createElement('script');
		                script.id = mod.replace('.', '-');

		                var i = mod.indexOf('.');
		                if (i < 0) {
		                    mod = 'core.' + mod;
		                } else {
		                    //�����ϲ������ռ�
		                    var basemod = mod.slice(0, i);
		                    Breeze[basemod] || (Breeze[basemod] = {});
		                }
		                script.src = Breeze.path + mod.replace('.', '/') + '.js?v='+runVerson;
		                isQueue = false;
		                isLoading = true;
						//document.body.appendChild(script);
		                document.getElementsByTagName('head')[0].appendChild(script);
		            } else {
		                startQueue();
		            }
		            break;
		        case 'function': //��ִ
		            mod(Breeze);
		            startQueue();
		        default:
		    }
		},
		word,
        win = window,
        doc = win.document,
        // Is the DOM ready to be used? Set to true once it occurs.
	    isReady = false,

        // The functions to execute on DOM ready.
	    readyList = [],

        // Has the ready events already been bound?
	    readyBound = false;
        /** 
        * @construct
        */
        Breeze = {
            version: '1.0.0',
            path: BREEZE_BASE_PATH,/*function () {
                return document.getElementById('B_script_base').src.replace('core/base.js', '');
            } (),*/
            /**
            * @description �������е�js�ļ��ͺ�������������<br />
            * �����������ַ������ߺ�������
            * @exports require as Breeze.require
            * @params {String|Function} fun
            */
            require: function () {
                var args = Array.prototype.slice.call(arguments),
				prequeue = loadQueue.slice(0, loadingIndex),
				afterqueue = loadQueue.slice(loadingIndex);
                loadQueue = prequeue.concat(args, afterqueue);
                loadingIndex += args.length;
                isQueue || isLoading || startQueue();
            },
            /**
            * @description �����ռ�, ���ڸ�ģ���ļ�����,��ע���Լ��������ģ�顣
            * @param {String} modName ��Ӧ������
            *
            */
            namespace: function (modName, fn) {
                modQueue.push(modName);
                loadingIndex = 0;
                fn(Breeze);
                isLoading = false;
                isQueue || startQueue();
            },

            /**
            * @description �������Ƿ�������������
            * @param {Object} o ����
            * @returns {Boolean}
            */
            isPlainObject: function (o) {
                return o && o.toString() === '[object Object]' && !o['nodeType'] && !o['setInterval'];
            },
            /**
            * @description ��dom������ɺ�ִ��
            * @params {Function} DOM������ɺ�Ҫִ�еĺ���
            * @example B.ready(function(){
                                do some thing...
                                });
            */
            ready: function (fn) {
                // Attach the listeners
                if (!readyBound) this._bindReady();

                // If the DOM is already ready
                if (isReady) {
                    // Execute the function immediately
                    fn.call(win, this);
                } else {
                    // Remember the function for later
                    readyList.push(fn);
                }
            },
            /**
            * Binds ready events.
            */
            _bindReady: function () {
                var self = this,
					doScroll = doc.documentElement.doScroll,
					eventType = doScroll ? 'onreadystatechange' : 'DOMContentLoaded',
					COMPLETE = 'complete',
					fire = function () {
					    self._fireReady();
					};

                // Set to true once it runs
                readyBound = true;

                // Catch cases where ready() is called after the
                // browser event has already occurred.
                if (doc.readyState === COMPLETE) {
                    return fire();
                }

                // w3c mode
                if (doc.addEventListener) {
                    function domReady() {
                        doc.removeEventListener(eventType, domReady, false);
                        fire();
                    }

                    doc.addEventListener(eventType, domReady, false);

                    // A fallback to window.onload, that will always work
                    win.addEventListener('load', fire, false);
                }
                // IE event model is used
                else {
                    function stateChange() {
                        if (doc.readyState === COMPLETE) {
                            doc.detachEvent(eventType, stateChange);
                            fire();
                        }
                    }

                    // ensure firing before onload, maybe late but safe also for iframes
                    doc.attachEvent(eventType, stateChange);

                    // A fallback to window.onload, that will always work.
                    win.attachEvent('onload', fire);

                    if (win == win.top) { // not an iframe
                        function readyScroll() {
                            try {
                                // Ref: http://javascript.nwbox.com/IEContentLoaded/
                                doScroll('left');
                                fire();
                            } catch (ex) {
                                setTimeout(readyScroll, 1);
                            }
                        }
                        readyScroll();
                    }
                }
            },

            /**
            * Executes functions bound to ready event.
            */
            _fireReady: function () {
                if (isReady) return;

                // Remember that the DOM is ready
                isReady = true;

                // If there are functions bound, to execute
                if (readyList) {
                    // Execute all of them
                    var fn, i = 0;
                    while (fn = readyList[i++]) {
                        fn.call(win, this);
                    }

                    // Reset the list of functions
                    readyList = null;
                }
            },
            /**
            * @description ������ж�<br/>
            * �����ж�������ĺ��ĺ�javascript�İ汾��
            * ��ie,webkit,opera��gecko����
            * ֵΪ���ֱ�ʾ�İ汾�ţ�������Ǹú��ģ�ֵΪ0
            * @example Breeze.UA.ie
            */
            UA: function () {
                var o = {
                    ie: 0,
                    gecko: 0,
                    webkit: 0,
                    opera: 0
                },
				ua = navigator.userAgent,
				m,
				numberify = function (s) {
				    var c = 0;
				    return parseFloat(s.replace(/\./g, function () {
				        return (c++ == 1) ? '' : '.';
				    }));
				};
                if (ua) {
                    m = ua.match(/AppleWebKit\/([^\s]*)/);
                    if (m && m[1]) {
                        o.webkit = numberify(m[1]);
                    }

                };

                if (!o.webkit) { // not webkit
                    // @todo check Opera/8.01 (J2ME/MIDP; Opera Mini/2.0.4509/1316; fi; U; ssr)
                    m = ua.match(/Opera[\s\/]([^\s]*)/);
                    if (m && m[1]) {
                        o.opera = numberify(m[1]);
                    } else { // not opera or webkit
                        m = ua.match(/MSIE\s([^;]*)/);
                        if (m && m[1]) {
                            o.ie = numberify(m[1]);
                        } else { // not opera, webkit, or ie
                            m = ua.match(/Gecko\/([^\s]*)/);
                            if (m) {
                                o.gecko = 1; // Gecko detected, look for revision
                                m = ua.match(/rv:([^\s\)]*)/);
                                if (m && m[1]) {
                                    o.gecko = numberify(m[1]);
                                }
                            }
                        }
                    }
                }
                return o;
            } (),
            /***
            * @description �������Ժϲ�
            * @param a ���ӹ��Ķ���
            * @param b ������������Ը�����һ������,
            * @param overWrite ȷ���Ƿ���Ѿ��е�����ֱ�Ӹ���
            * @returns �ӹ���Ķ���
            */
            mix: function (/**Object*/a, /**Object*/b, /**Boolean*/overWrite) {
                for (var p in b) {
                    if (overWrite || (typeof a[p] == 'undefined')) {
                        a[p] = b[p];
                    }
                }
                return a;
            },
            /***
            * @description ����󸲸�
            * @param {Object} object ��Ҫ���ǵĶ���
            * @return �ӹ���Ķ���
            */
            merge: function (o) {
                var a = arguments, i, l = a.length;
                for (i = 1; i < l; i = i + 1) {
                    Breeze.mix(o, a[i], true);
                }
                return o;
            },
            /**
            * @description ��������ת��Ϊ����
            * @param {ArrayLike} array ������
            * @returns Array �ӹ��������
            */
            makeArray: function (o/*=====, results=====*/) {
                if (B.isArray(o)) return o;
                if (typeof o.length !== 'number' || B.isString(o) || B.isFunction(o)) {
                    return [o];
                }
                return Array.prototype.slice.call(o, 0);
                /*=====
                if ( results ) {
                results.push.apply( results, array);
                return results;
                }
				
                return array;
                =====*/
            },
            /**
            * @description ���ַ�����������Ŀհ�ȥ��
            * @param {String} str �ַ���
            * @return �ӹ�����ַ���
            */
            trim: function (str) {
                if (typeof str === 'string') {
                    return str.trim ? str.trim() : str.replace(/\s+$/, '');
                }
                return '';
            },
            /**
            * @description �������Ƿ��Ǻ���
            * @param {Object} obj �������
            * @returns Boolean
            */
            isFunction: function (obj) {
                return Object.prototype.toString.call(obj) === "[object Function]";
            },
            /**
            * @description �������Ƿ�Ϊ����
            * @param {Object} obj�������
            * @returns Boolean
            */
            isArray: function (obj) {
                return Object.prototype.toString.call(obj) === '[object Array]';
            },
            /**
            * @description �������Ƿ�Ϊ�ַ���
            * @param {Object} obj�������
            * @returns Boolean
            */
            isString: function (o) {
                return Object.prototype.toString.call(o) === '[object String]';
            },
            /**
            * @description �趨��д
            */
            shortcut: function (s) {
                word && (window[word] = undefined);
                window[word = s] = Breeze;
            },
            /**
            * @description ����CSS
            * @param {String} url ��ַ
            */
            loadCSS: function (url, id) {
                var css = document.createElement('link');
                css.type = 'text/css';
				css.rel = 'stylesheet';
				css.href = url;
				if (id){
					css.id = id;
				}
                document.body.appendChild(css);
				return css;
            }
        };

        /**
        * Set Shortcut
        */
        Breeze.shortcut('B');


        var bindNative = function () {
            ['every', 'forEach', 'filter', 'map', 'some'].forEach(function (n) {
                B[n] = function () {
                    var arg = arguments, l = arg.length;
                    if (l == 0) {
                        return null;
                    }
                    var newarg = Breeze.makeArray(arg);
                    return Array.prototype[n].apply(newarg.shift(), newarg);
                }
            });
        };
        if (Array.prototype.some) {
            bindNative();
        } else {
           B.require('native', bindNative);
        }
        /**
        * @name Breeze.$
        * @function
        */
        var bindDom = function (B) {
            if (typeof Sizzle !== 'undefined') {//����,����sizzle;
                /**
                * @lends Breeze
                * @description XX����
                */
                B.$$ = Sizzle;
                /**
                * @lends Breeze
                * @description XX����
                */
                B.$ = function (selector, parentNode) {
                    var results = Sizzle(selector, parentNode);
                    return results.length ? results[0] : null;
                }
            } else {//������,����querySelector
                B.$ = function (selector, parentNode) {
                    parentNode = parentNode || document;
                    return parentNode.querySelector(selector);
                };
                B.$$ = function (selector, parentNode) {
                    parentNode = parentNode || document;
                    var ar = parentNode.querySelectorAll(selector), l = ar.length, res = [];
                    for (var i = 0; i < l; i++) {
                        res.push(ar[i]);
                    }
                    return res;
                };
            }
        };
        if (document.querySelectorAll) {
            bindDom(B);
        } else {
            B.require('sizzle', bindDom);
        }




        /*
        * ��ʽ���Ĵ���
        */
        Function.prototype.method = function (name, fn) {
            this.prototype[name] = fn;
            return this;
        };
        function _$(el) {
            if (B.isString(el)) {
                this.nodes = B.$$(el);
            } else {
                if (el && el.nodeType && el.nodeType === 1) {
                    this.nodes = B.makeArray(el);
                } else {
                    this.nodes = [];
                }
            }
            this[0] = this.nodes[0];
            this.length = this.nodes.length;
            //return this.nodes;
        };
        B.query = function (el) { return new _$(el); }
        B.extend = function (name, fn) {
            _$.method(name, fn);
            return this;
        };
    }
})();