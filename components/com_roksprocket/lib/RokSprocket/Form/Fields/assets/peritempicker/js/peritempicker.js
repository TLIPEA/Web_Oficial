
((function(){
	if (typeof this.RokSprocket == 'undefined') this.RokSprocket = {};
	var OnInputEvent = (Browser.name == 'ie' && Browser.version <= 9) ? 'keypress' : 'input';

	this.PerItemPicker = new Class({

		Implements: [Options, Events],
		options: {},

		initialize: function(options){
			this.setOptions(options);

			this.attach();
		},

		getPickers: function(){
			this.pickers = document.getElements('[data-peritempicker]');

			return this.pickers;
		},

		attach: function(picker){
			var pickers = (picker ? new Elements([picker]).flatten() : this.getPickers());

			this.fireEvent('beforeAttach', pickers);

			pickers.each(function(picker){
				var select = picker.getElement('select'),
					display = picker.getElement('[data-peritempicker-display]'),
					input = picker.getElement('#' + picker.get('data-peritempicker-id'));

				var change = select.retrieve('roksprocket:pickers:change', function(event){
						this.change.call(this, event, select);
					}.bind(this)),
					keypress = display.retrieve('roksprocket:pickers:input', function(event){
						this.keypress.call(this, event, display, input, select);
					}.bind(this)),
					focus = display.retrieve('roksprocket:pickers:focus', function(event){
						this.focus.call(this, event, display, input);
					}.bind(this)),
					blur = display.retrieve('roksprocket:pickers:blur', function(event){
						this.blur.call(this, event, display, input, select);
					}.bind(this));

				if (!input.get('value').test(/^-([a-z]{1,})-$/)){
					display.store('display_value', display.get('value') || '');
					display.store('display_datatitle', display.get('data-original-title') || '');
					input.store('user_value', input.get('value') || '');
				}

				select.addEvent('change', change);
				display.addEvent(OnInputEvent, keypress);
				display.addEvent('focus', focus);
				display.addEvent('blur', blur);
				display.twipsy({placement: 'above', offset: 5, html: false});

			}, this);

			this.fireEvent('afterAttach', pickers);
		},

		detach: function(picker){
			var pickers = (picker ? new Elements([picker]).flatten() : this.pickers);

			this.fireEvent('beforeDetach', pickers);

			pickers.each(function(picker){
				var change = picker.retrieve('roksprocket:pickers:change'),
					keypress = picker.retrieve('roksprocket:pickers:input'),
					select = picker.getElement('select'),
					display = picker.getElement('[data-peritempicker-display]');

				select.removeEvent('change', change);
				display.removeEvent(OnInputEvent, keypress);

			}, this);

			if (!picker) document.store('roksprocket:pickers:document', false).removeEvent('click', this.bounds.document);

			this.fireEvent('afterDetach', pickers);
		},

		change: function(event, select){
			var value = select.get('value'),
				parent = select.getParent('.peritempicker-wrapper'),
				hidden = parent.getElement('input[type=hidden]'),
				display = parent.getElement('[data-peritempicker-display]'),
				dropdown = parent.getElement('.sprocket-dropdown [data-toggle]'),
				title = dropdown.getElement('span.name');

			if (value.test(/^-([a-z]{1,})-$/)){
				parent.addClass('peritempicker-noncustom');
				title.set('text', select.getElement('[value='+value+']').get('text'));

				display.set('value', select.get('value'));
				hidden.set('value', value);
			} else {
				parent.removeClass('peritempicker-noncustom');
				title.set('text', '');

				if (display.get('value').test(/^-([a-z]{1,})-$/)){
					display.set('value', display.retrieve('display_value', '')).set('data-original-title', display.retrieve('display_datatitle', ''));
					hidden.set('value', hidden.retrieve('user_value', ''));
				}

				this.keypress(false, display, hidden, select);
			}

		},

		keypress: function(event, display, input, select){
			var twipsy = display.retrieve('twipsy'),
				value = display.get('value');

			this.update(input, value);
			if (twipsy && event !== false){
				twipsy.setContent()[value.length ? 'show' : 'hide']();
			}
		},

		focus: function(event, display, input){
			new TextArea(input, display);
		},

		blur: function(event, display, input, select){
			var twipsy = display.retrieve('twipsy');

			if (twipsy) twipsy.hide();
			//if (ta && !ta.hasFocus) ta.dispose();
		},
		update: function(input, settings){
			input = document.id(input);

			// RokSprocket.SiteURL is always available

			var parent = input.getParent('[data-peritempicker]'),
				display = parent.getElement('[data-peritempicker-display]'),
				value = display.get('value');

			display
				.set('value', value).store('display_value', value)
				.set('data-original-title', value).store('display_datatitle', value)
				.twipsy({placement: 'above', offset: 5, html: false});

			input.set('value', value).store('juser_value', value);
		}

	});

	var TextArea = new Class({
		Implements: [Options, Events],
		options: {},
		initialize: function(input, display, options){
			this.setOptions(options);

			this.input = document.id(input);
			this.display = document.id(display);
			this.wrapper = null;
			this.textarea = null;

			this.build();
		},

		build: function(){
			this.wrapper = new Element('div.peritempicker-textarea-wrapper').adopt(
				new Element('span[data-peritempicker-close].close', {html: '&times;'}),
				new Element('textarea.peritempicker-textarea')
			).inject(document.body);

			this.wrapper.styles({position: 'absolute'});
			this.textarea = this.wrapper.getElement('textarea');

			this.attach();
			this.show();

			return this;
		},

		destroy: function(){
			this.detach();
			this.wrapper.dispose();

			return this;
		},

		attach: function(){
			var keypress = this.wrapper.retrieve('roksprocket:pickers:textarea', function(event){
					this.keypress.call(this, event);
				}.bind(this)),
				close = this.wrapper.retrieve('roksprocket:pickers:close', function(event){
					this.keypress.call(this, event);
					this.destroy.call(this, event);
				}.bind(this));

			document.body.addEvent('keyup:keys(esc)', close);
			this.textarea.addEvent('keydown', keypress);
			this.wrapper.addEvents({
				'blur:relay(textarea)': close,
				'click:relay(.close)': close
			});

			return this;
		},

		detach: function(){
			var keypress = this.wrapper.retrieve('roksprocket:pickers:textarea'),
				close = this.wrapper.retrieve('roksprocket:pickers:close');

			document.body.removeEvent('keyup:keys(esc)', close);
			this.textarea.removeEvent('keydown', keypress);
			this.wrapper.removeEvents({
				'blur:relay(textarea)': close,
				'click:relay(.close)': close
			});

			return this;
		},

		keypress: function(event){
			var value = this.textarea.get('value');

			this.input.set('value', value);
			this.display.set('value', value);

			if (event && event.type == 'keydown'){
				if (event.key == 'tab'){
					var next = this.input.getNext('[type!=hidden]');
					next.set('tabindex', 0).focus();
					next.set('tabindex', null);
				}
			}

			return this;
		},

		show: function(){
			this.wrapper.styles({display: 'block'}).position({relativeTo: this.display});
			this.textarea.set('value', this.display.get('value'));
			this.textarea.focus();

			return this;
		},

		hide: function(){
			this.wrapper.styles({display: 'none'});

			return this;
		},

		toElement: function(){
			return this.wrapper;
		}
	});

	window.addEvent('domready', function(){
		this.RokSprocket.peritempicker = new PerItemPicker();
	});

})());
