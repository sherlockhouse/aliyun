/**
 * @fileoverview �л����
 * @author yuyang <yuyangvi@gmail.com>
 * @version 1.0
 * @depends base
 */
Breeze.namespace('util.scrollable',function(B){
	var cfg = {
        triggers: [],
        panels: [],

        // �Ƿ��д���
        hasTriggers: true,

        // ��������
        triggerType: 'mouse', // or 'click'
        // �����ӳ�
        //delay: .1, // 100ms

        activeIndex: 0, // markup ��Ĭ�ϼ����Ӧ����� index һ��
        activeTriggerCls: 'b_current',

        // �ɼ���ͼ���ж��ٸ� panels
        steps: 1
    };
	function Scrollable(opt){
		var self = this;
		self.triggers = opt.triggers;
		self.panels = opt.panels;
		delete opt.triggers;
		delete opt.panels;
		self.opt = B.merge({}, cfg, opt);
		if(this.opt.hasTriggers){
			B.require('event', function(B){self.bindTrigglers()});
		}
		/*B.$$(triggers)(B.addEvent, trggerType, self.trigger);*/
	};
	Scrollable.prototype = {
		/**
		 * ������¼�
		 */
		bindTrigglers: function(){
			var self = this;
			self.triggers.forEach(function(n, i){
				B.addEvent(n, 'click', function(){
					self.focusTrigger(i);
				});
			});
		},
		/**
		 * �����¼�
		 */
		 focusTrigger: function(index){
            //var self = this;
            //if (!self._triggerIsValid()) return; // �ظ����

            //this._cancelSwitchTimer(); // ���磺�������������̵������ʱ�����������л�����ȡ������
            this.switchTo(index);
		 },
		 
		 /**
		  * �л�Ч��
		  */
		 switchTo: function(index){
			if (this.opt.activeIndex == index){
				return;
			}
			var self = this, cfg = self.opt,
				triggers = self.triggers, panels = self.panels,
				activeIndex = self.opt.activeIndex,
				steps = cfg.steps,
				fromIndex = activeIndex * steps, toIndex = index * steps;
           	// if (!self._triggerIsValid()) return self; // �ٴα����ظ�����
            
			/*if (self.fire(EVENT_BEFORE_SWITCH, {toIndex: index}) === false) return self;

            // switch active trigger
            if (cfg.hasTriggers) {
                self._switchTrigger(activeIndex > -1 ? triggers[activeIndex] : null, triggers[index]);
            }

            // switch active panels
            if (direction === undefined) {
                direction = index > activeIndex ? FORWARD : BACKWARD;
            }
			*/
            // switch view
            self.switchView(
                panels.slice(fromIndex, fromIndex + steps),
                panels.slice(toIndex, toIndex + steps),
                index);

            // update activeIndex
            self.opt.activeIndex = index;

            return self; // chain*/
		 },
		 //��ʾ������
		 switchView: function(fromPanels, toPanels, index){
			/*B.require('util.animate', function(B){*/
				fromPanels.forEach(function(n){
					B.hide(n);
					//B.animaten.style.display = 'none';
				});
				toPanels.forEach(function(n){
					B.show(n);
				});
			/*});*/
			//B.css(fromPanels, DISPLAY, NONE);
            //B.css(toPanels, DISPLAY, BLOCK);
            // fire onSwitch events
            //this._fireOnSwitch(index);

		 }
	};
	/**
	 * Tabs
	 */
	B.util.tabs = function(selector){
		B.$$(selector).forEach(function(n){
			var triggers = B.$$('.B_tab_trigger', n);
			var panels = B.$$('.B_tab_panel', n);
			new Scrollable({triggers:triggers,panels:panels});
		});
	}
});