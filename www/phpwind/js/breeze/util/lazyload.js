/*
* lazyload ģ��
* 
*/
Breeze.namespace('util.lazyload', function(B) {
    var win = window,doc = document,
        defaultConfig = {
            container :win,
            img_data : 'data-src',
            area_cls : 'bz-lazyLoad',
            delay : 100,//resizeʱ��socrllʱ�ӳٴ���,����Ƶ������,100����������Ӿ�����
            placeholder :''//ͼƬռλ��
        }
    
    function Lazyload(selector,settings) {
        var self = this;
        B.merge(self, defaultConfig, settings);
        if( !(self instanceof Lazyload) ) {
			return new Lazyload(selector, settings);
		}
		B.require('dom','event',function(B) {
		   var lazyImgs = [],lazyAreas = [],
		    container = self.container.nodeType===1 ? self.container :win,
		    threshold = function() {
		        if(container===win) {
		            var scrollTop =  win.pageYOffset || container.scrollTop || doc.documentElement.scrollTop || doc.body.scrollTop,
		            eHeight = doc.documentElement.innerHeight || ducument.body.clientHeight || ducument.documentElement.clientHeight;
		            return scrollTop + eHeight;
		        }
		        return B.offset(container).top + container.clientHeight;
		    },
		    eHeight = function() {return container.innerHeight || container.clientHeight;}
		    B.$$(selector).forEach(function (n) {
                if(n.nodeName === 'IMG' && n.getAttribute(self.img_data)) {
                    lazyImgs.push(n);
                    if(self.placeholder!==''){
                        n.src = self.placeholderl;
                    }
                }
                if(n.nodeName === 'TEXTAREA' && B.hasClass(n,self.area_cls)) {
                    lazyAreas.push(n);
                }
            });
            //����image	
            var _loadImgs = function() {
		        lazyImgs.forEach(function(n) {
		            if(!n.src || n.src==='') {
		                if(B.offset(n).top <= threshold()) {
		                    n.src = n.getAttribute(self.img_data);
		                }
		            }
		        });
	        };
        	
        	//����textarea	
	        var _loadAreas = function() {
        	    lazyAreas.forEach(function(n) {
        	        var isHide = true,
        	            top = B.offset(isHide?n.parentNode:n).top;
        	        if(B.hasClass(n,self.area_cls)) {//��û�м��ص�ʱ��ż���,�п����Ѿ����ع�
        	            if(top <= threshold()) {
        	                n.style.display = 'none';n.className = '';
        	                var elem = B.createElement('<div>' + n.value + '</div>');
        	                B.insertBefore(elem,n);
        	            }
        	        }
        	    });
	        };
	        
	        var _loadAll = function() {
	            var timer;
                if(timer) {
                    clearTimeout(timer);
                }
                timer = setTimeout(function() {//�ӳ�ִ��
                    _loadImgs();
                    _loadAreas();
                },self.delay);
	        }
            B.addEvent(container,'scroll',function() {
                _loadAll();
            });
            B.addEvent(container,'resize',function() {
                _loadAll();
            }); 
            _loadAll();//Ĭ�ϼ���һ��
		});
    }
    
    
    /**
	 * @description �����ӳټ���
	 * @params {String} Ҫ�ӳټ��ص�Ԫ��
	 * @params {PlainObject} ���ò��� ����{
	 *                                   container://Ҫ����������Ԫ��,
	 *                                   delay://�ӳټ��ص�ʱ��,
	 *                                   placeholder://ͼƬռλ��,
	 *                                   img_data : //ͼƬ���Զ�������,
     *                                   area_cls : //textarea��class��
	 *                                   }
	 */
	B.util.lazyload = function(selector,settings) {
	    Lazyload(selector,settings);
    }
});
/*
��ǰֻ�����������ʱ���ӳټ���,��������Ϻ����õ�,������ʱ������,��ͼƬ�ӳټ�����,
ֻ�ܼ�img-data����,����src�ʹﲻ���ӳ�Ч��
*/