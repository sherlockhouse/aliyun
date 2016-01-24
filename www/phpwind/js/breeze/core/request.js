/*
* @request ģ��
* @ͨ��http�������Զ������
* @depends base
*/
Breeze.namespace('request',function(B) {
	var win = window, doc = document, body = doc.body,
		now = (new Date).getTime(),
		jsre = /=\?(&|$)/,
		rquery = /\?/,
		rts = /(\?|&)_=.*?(&|$)/,
		rurl = /^(\w+:)?\/\/([^\/?#]+)/,
		r20 = /%20/g;

		//�ж������Ƿ�ɹ�
		function httpSuccess( xhr ) {
			try {
				// IE error sometimes returns 1223 when it should be 204 so treat it as success, see #1450
				return !xhr.status && location.protocol === "file:" ||
					// Opera returns 0 when status is 304
					( xhr.status >= 200 && xhr.status < 300 ) ||
					xhr.status === 304 || xhr.status === 1223 || xhr.status === 0;
			} catch(e) {}

			return false;
		}
		//��ȫ��ִ��script
		function $exec(text){
			if (!text) return text;
			if (win.execScript){
				win.execScript(text);
			} else {
				var script = doc.createElement('script');
				script.setAttribute('type', 'text/javascript');
				try {
					script.appendChild( doc.createTextNode( "window." + now + "=1;" ) );
				} catch(e) {}
				if ( window[now] ) {
					script.appendChild( doc.createTextNode( data ) );
				} else {
					script.text = data;
				}
				doc.head.appendChild(script);
				doc.head.removeChild(script);
			}
			return text;
		};
        
        //���ݲ�ͬ��contentType����ͬ��AJAX��������
		function httpData( xhr, type, s ) {
			var ct = xhr.getResponseHeader("content-type") || "",
				xml = type === "xml" || !type && ct.indexOf("xml") >= 0,
				data = xml ? xhr.responseXML : xhr.responseText;

			if ( xml && data.documentElement.nodeName === "parsererror" ) {
				throw "parsererror" ;
			}
			
			// The filter can actually parse the response
			if ( typeof data === "string" ) {
				// Get the JavaScript object, if JSON is used.
				if ( type === "json" || !type && ct.indexOf("json") >= 0 ) {
					data = parseJSON( data );
                    
				// If the type is "script", eval it in global context
				} else if ( type === "script" || !type && ct.indexOf("javascript") >= 0 ) {
					$exec( data );
				}
			}

			return data;
		}
		
		function parseJSON( data ) {
			if ( typeof data !== "string" || !data ) {
				return null;
			}

			// Make sure leading/trailing whitespace is removed (IE can't handle it)
			data = B.trim( data );
			
			// Make sure the incoming data is actual JSON
			// Logic borrowed from http://json.org/json2.js
			if ( /^[\],:{}\s]*$/.test(data.replace(/\\(?:["\\\/bfnrt]|u[0-9a-fA-F]{4})/g, "@")
				.replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g, "]")
				.replace(/(?:^|:|,)(?:\s*\[)+/g, "")) ) {

				// Try to use the native JSON parser first
				return win.JSON && win.JSON.parse ?
					win.JSON.parse( data ) :
					(new Function("return " + data))();

			} else {
				throw "Invalid JSON: " + data ;
			}
		}
		
		function $empty(){};
		
        function isValidParamValue(val) {
            var t = typeof val;
            return val === null || (t !== 'object' && t !== 'function');
        }

	B.mix(B, {
		/**
		 * @description ����JSON<br />����Ϊ�ϸ��json��ʽ,key����Ϊ˫����
		 * @exports require as Breeze.require
		 * @params {string} Ҫ�������ַ���
		 */
		parseJSON: parseJSON,
		
		/**
		 * @description ������ת��Ϊ�����ַ�����(form kissy)<br />
		 * @exports require as Breeze.require
		 * @params {string} Ҫ�������ַ���
		 */
		param: function(o) {
            if (!B.isPlainObject(o)){return ''}
            var buf = [], key, val;
            for (key in o) {
                val = o[key];
                key = encodeURIComponent(key);

                // val Ϊ��Ч�ķ�����ֵ
                if (isValidParamValue(val)) {
                    buf.push(key, '=', encodeURIComponent(val + ''), '&');
                }
                // val Ϊ�ǿ�����
                else if (B.isArray(val) && val.length) {
                    for (var i = 0, len = val.length; i < len; ++i) {
                        if (isValidParamValue(val[i])) {
                            buf.push(key, '[]=', encodeURIComponent(val[i] + ''), '&');
                        }
                    }
                }
            }
            buf.pop();
            return buf.join('');
        },
		_ajaxSettings: {
			url: location.href,
			global: true,
			type: "GET",
			contentType: "application/x-www-form-urlencoded",
			processData: true,
			async: true,
			/*
			timeout: 0,
			data: null,
			username: null,
			password: null,
			traditional: false,
			*/
			// Create the request object; Microsoft failed to properly
			// implement the XMLHttpRequest in IE7 (can't request local files),
			// so we use the ActiveXObject when it is available
			// This function can be overriden by calling jQuery.ajaxSetup
			xhr: win.XMLHttpRequest && (win.location.protocol !== "file:" || !win.ActiveXObject) ?
				function() {
					return new win.XMLHttpRequest();
				} :
				function() {
					try {
						return new win.ActiveXObject("Microsoft.XMLHTTP");
					} catch(e) {}
				},
			accepts: {
				xml: "application/xml, text/xml",
				html: "text/html",
				script: "text/javascript, application/javascript",
				json: "application/json, text/javascript",
				text: "text/plain",
				_default: "*/*"
			}
		},
		/**
		 * @description ����get����<br />
		 * @exports require as Breeze.require
		 * @params {String} Ҫ���󵽵�URL[����]
		 * @params {Object || String} Ҫ����URL����,����Ϊ�����URL������ʽ�ַ���(��ѡ)
		 * @params {Function} ����ɹ���Ļص�����(��ѡ)
		 */
		get:function(url,data,callback,type){
			if (typeof data == 'function') {//��������ʱ�����ĸı�
				type = type || callback;
				callback = data;
				data = null;
			}
			return this.ajax({
				type: "GET",
				url: url,
				data: data,
				success: callback,
				dataType: type
			});
		},

		/**
		 * @description ����һ��script�ļ�,֧�ֿ������<br />
		 * @exports require as Breeze.require
		 * @params {String} Ҫ���󵽵�URL[����]
		 * @params {Function} ����ɹ���Ļص�����(��ѡ)
		 */
		getScript: function( url, callback ) {
			return this.get(url, null, callback, "script");
		},

		/**
		 * @description ����һ��json�ļ�,֧�ֿ������<br />
		 * @exports require as Breeze.require
		 * @params {String} Ҫ���󵽵�URL[����]
		 * @params {Object || String} Ҫ����URL����,����Ϊ�����URL������ʽ�ַ���(��ѡ)
		 * @params {Function} ����ɹ���Ļص�����(��ѡ)
		 */
		getJSON: function( url, data, callback ) {
			return this.get(url, data, callback, "json");
		},
		
		/**
		 * @description ����POST����<br />
		 * @exports require as Breeze.require
		 * @params {String} Ҫ���󵽵�URL[����]
		 * @params {Object || String} Ҫ����URL����,����Ϊ�����URL������ʽ�ַ���(��ѡ)
		 * @params {Function} ����ɹ���Ļص�����(��ѡ)
		 */
		post: function( url, data, callback, type ) {
			// shift arguments if data argument was omited
			if (typeof data == 'function') {//��������ʱ�����ĸı�
				type = type || callback;
				callback = data;
				data = {};
			}

			return this.ajax({
				type: "POST",
				url: url,
				data: data,
				success: callback,
				dataType: type
			});
		},

		/**
		 * @description Breeze Ajax��ȫ������<br />
		 * @exports require as Breeze.require
		 * @params {Object} ��������
		 * @params {Function} ����ɹ���Ļص�����(��ѡ)
		 */
		ajaxSetup: function( settings ) {
			B.mix( this._ajaxSettings, settings,true );
		},

		/**
		 * ajaxģ���еĺ��ĺ���
		 * @description ajax����<br />
		 * @exports require as Breeze.require
		 * @params {Object} ��������
		 * @params {Function} ����ɹ���Ļص�����(��ѡ)
		 */
		ajax: function(origSettings){
			var s = B.merge({},this._ajaxSettings, origSettings,true);
			var jsonp, status, data, errMsg;
			callbackContext = origSettings && origSettings.context || s,
			type = s.type.toUpperCase();
			
            if ( s.data && s.processData && typeof s.data !== "string" ) {
			    s.data = this.param( s.data);
		    }
			if ( s.dataType === "script" && s.cache === null ) {
				s.cache = false;
			}
			//��������cache
			if ( s.cache === false && type === "GET" ) {
				var ts = now;

				// try replacing _= if it is there
				var ret = s.url.replace(rts, "$1_=" + ts + "$2");

				// if nothing was replaced, add timestamp to the end
				s.url = ret + ((ret === s.url) ? (rquery.test(s.url) ? "&" : "?") + "_=" + ts : "");
			}

			// If data is available, append data to url for get requests
			if ( s.data && type === "GET" ) {
				s.url += (rquery.test(s.url) ? "&" : "?") + s.data;
			}
			if(s.dataType === "script") {
				var head = doc.getElementsByTagName("head")[0] || doc.documentElement,
                node = doc.createElement('script');
				node.src = s.url;
				if (s.scriptCharset) node.charset = s.scriptCharset;
				node.async = true;

				var done = false;

				// Attach handlers for all browsers
				node.onload = node.onreadystatechange = function() {
					if ( !done && (!this.readyState ||
							this.readyState === "loaded" || this.readyState === "complete") ) {
						done = true;
						success();
						complete();

						// Handle memory leak in IE
						node.onload = node.onreadystatechange = null;
						if ( head && node.parentNode ) {
							head.removeChild( node );
						}
					}
				};
				//ie6��bug������insertBefore����appendChild
				head.insertBefore(node,head.firstChild);
				return undefined;
			}
			//��������ʼ
			var requestDone = false, xhr = s.xhr();
			if ( !xhr ) {
				return;
			}
			
			// Open the socket
			// Passing null username, generates a login popup on Opera (#2865)
			if ( s.username ) {
				xhr.open(s.type, s.url, s.async, s.username, s.password);
			} else {
				xhr.open(s.type, s.url, s.async);
			}
			
			//����httpͷ
			try{
			    if ( s.data || origSettings && origSettings.contentType ) {
				    xhr.setRequestHeader("Content-Type", s.contentType);
				    
			    }
			    xhr.setRequestHeader("Accept", s.dataType && s.accepts[ s.dataType ] ?
				    s.accepts[ s.dataType ] + " */*" :
				    s.accepts._default );
				    
			    xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
			    // Set the Accepts header for the server, depending on the dataType
			    
			}catch(e){}
			
			// Allow custom headers/mimetypes and early abort
			if ( s.beforeSend && s.beforeSend.call(callbackContext, xhr, s) === false ) {
				// close opended socket
				xhr.abort();
				return false;
			}
			
			var onreadystatechange = xhr.onreadystatechange = function(isTimeout) {
				if ( !xhr || xhr.readyState === 0 || isTimeout === "abort" ) {
				// Opera doesn't call onreadystatechange before this point
				// so we simulate the call
					complete();
					requestDone = true;
				}else if ( !requestDone && xhr && (xhr.readyState === 4 || isTimeout === "timeout") ){
					requestDone = true;
					xhr.onreadystatechange = $empty;
					status = isTimeout === "timeout" ?"timeout" :
						httpSuccess( xhr )?"success":"error";
				    if ( status === "success" ) {
					    // Watch for, and catch, XML document parse errors
					    try {
						    // process the data (runs the xml through httpData regardless of callback)
						    data = httpData( xhr, s.dataType, s );
					    } catch(err) {
						    status = "parsererror";
						    errMsg = err;
					    }
					    if ( status === "success") {
						    success();
				        } else {
					        error();
				        }
					    complete();
				    }

				    if ( isTimeout === "timeout" ) {
					    xhr.abort();
				    }
				    // Stop memory leaks
				    if ( s.async ) {
					    xhr = null;
				    }
				}
			}
			
			// Override the abort handler, if we can (IE doesn't allow it, but that's OK)
			// Opera doesn't fire onreadystatechange at all on abort
			try {
				var oldAbort = xhr.abort;
				xhr.abort = function() {
					if ( xhr ) {
						oldAbort.call( xhr );
					}
					onreadystatechange( "abort" );
				};
			} catch(e) { }


			// Timeout checker
			if ( s.async && s.timeout > 0 ) {
				setTimeout(function() {
					// Check to see if the request is still happening
					if ( xhr && !requestDone ) {
						onreadystatechange( "timeout" );
					}
				}, s.timeout);
			}
			// Send the data
			try {
				xhr.send( type === "POST" || type === "PUT" || type === "DELETE" ? s.data : null );
			} catch(e) {
				error();
				complete();
			}
			function success(){
				if(s.success){
					s.success.call( callbackContext, data, status, xhr );
				}
			}

			function complete(){
				if(s.complete){
					s.complete.call( callbackContext,xhr, status );
				}
			}

			function error(){
				if(s.error){
					s.error.call( s.context || s, status, errMsg );
				}
			}
			return xhr;
		}
	});
});

/**
 * TODO:
 *   - parseJSON�������Զ�������ʹ��,Ҫ��Ҫ��¶��API��һ����?
 */