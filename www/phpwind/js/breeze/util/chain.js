/*
* chain ģ��
* ��Breeze�ṩ��ʽ��������
*/
Breeze.namespace('util.chain',function(B){
    Function.prototype.method = function(name,fn) {
        this.prototype[name] = fn;
        return this;
    };
    B.require('dom','event','util.animate',function(B) {
        (function() {
            function _$(el) {
                if ( B.isString(el) ) {
                    this.el = B.$$(el);
                } else {
                    if(el && el.nodeType && el.nodeType===1) {
                        this.el = B.makeArray(el);
                    }else {
                        this.el = [];
                    }
                }
                this[0] = this.el[0];
                this.length = this.el.length;
            };
            /*
            * �ʺ�ѡ�����Ĳ�������
            */
            ['addClass','removeClass','arrt','removeAttr','createElement','remove','css','data','parent','children','prev','next','siblings','addEvent','removeEvent','hide','show','slideDown','slideUp','fadeIn','fadeOut','html'].forEach(function(p) {
                _$.method(p,function() {
                    var arg = B.makeArray(arguments),finalEls = [];
                    //��������set����,������get����,��getԪ�ؼ���ʱ,��Ҫ�ı䵱ǰ��elements
                    for(var i = 0,j = this.el.length; i < j; i++) {
                        var el = this.el[i],
                            result = B[p].apply(el,[el].concat(arg));
                        finalEls = B.makeArray(result || []).concat(finalEls);
                    }
                    if(finalEls.length > 0){
                        this.el = finalEls;
                        this.length = finalEls.length;
                    }
                    return this;
                });
            });
            /*
            * ��ǰԪ�ز���Ϊ��һ��������
            */
            ['insertBefore', 'insertAfter'].forEach(function(p) {
                _$.method(p,function() {
                    var arg = B.makeArray(arguments);
                    for(var i = 0,j = this.el.length; i < j; i++) {
                        var el = this.el[i];
                        B[p].apply(el,arg.push(el));
                    }
                });
            });
            /*
            * animate��������Ϊ����һ��util�ռ����,����д����
            */
            _$.method('animate',function(style, speed, easing, callback) {
                for(var i = 0,j = this.el.length; i < j; i++) {
                    B.util.animate(this.el[i],style, speed, easing, callback);
                }
                return this;
            });
            
            B['_$'] = function(el){return new _$(el);}
            B['_$'].forEach = B.forEach;
            B['_$'].every = B.every;
            B['_$'].some = B.some;
            B['_$'].map = B.map;
            B['_$'].filter = B.filter;
            B.extend = function(name, fn) {
                _$.method(name, fn);
                return this;
            };
        })();   
       
    });
});

/*
* TODO:
*    Ŀǰ��û���㹻���õ�API,ֻ��ʵ���˲���,���Ԫ���г��õ�html(),val()������
*/