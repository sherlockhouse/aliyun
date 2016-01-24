/*
* util.localStorage ģ��
* ����������ش洢
*/
Breeze.namespace('util.localStorage', function(B){
    var loc = {},
    win = window,doc = win.document,
    localStorageName = 'localStorage',
    globalStorageName = 'globalStorage',
    storage;
    
    /* ���л�,��loc.set("preson",{"name","superman"})
    *  Ŀǰû�п��ǵ��洢javascript���󵽱���,�ʴ˷�����Ԥ��,ֱ��return value;
    */
    function serialize(value) {
		if(value&&value.replace(/\s+/g,'')!=''){
			return value;
		}
    }
    function deserialize(value) {
        return value;
    }
    /*
    *   ���ǵ�get,set �ȶ������,����ʹ���ж��������Ϊ��֧
    */
    if (localStorageName in win && win[localStorageName]) {//chrome firefox opera
        storage = win[localStorageName]
        loc.set = function(key, val) { 
            storage.setItem(key, serialize(val)); 
        }
        
        loc.get = function(key) { 
            return deserialize(storage.getItem(key)); 
        }
        
        loc.remove = function(key) { 
            storage.removeItem(key);
        }
        
        loc.clear = function() { 
            storage.clear();
        }
    }else if (globalStorageName in win && win[globalStorageName]) {
        storage = win[globalStorageName][location.hostname];
        loc.set = function(key, val) { 
            storage[key] = serialize(val);
        }
        loc.get = function(key) { 
            return deserialize(storage[key] && storage[key].value);
        }
        loc.remove = function(key) { 
            delete storage[key];
        }
        loc.clear = function() { 
            for (var key in storage ) delete storage[key];
        }
    }else if(doc.documentElement.addBehavior) {//ie
		try{
			var el = doc.documentElement;
			el.addBehavior('#default#userData');
			el.load(localStorageName);
			loc.set = function(key, val) {
				el.setAttribute(key, serialize(val));
				el.save(localStorageName);
			}
			loc.get = function(key) {
				 return deserialize(el.getAttribute(key))
			}
			loc.remove = function(key) {
				el.removeAttribute(key)
				el.save(localStorageName)
			}
			loc.clear = function() {
				var attributes = el.XMLDocument.documentElement.attributes;
				el.load(localStorageName);
				for (var i=0, attr; attr = attributes[i]; i++) {
					el.removeAttribute(attr.name);
				}
				el.save(localStorageName);
			}
		}catch(e){}
    }
    B.util.localStorage = loc;
});