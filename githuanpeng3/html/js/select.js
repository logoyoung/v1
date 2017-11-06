	/**
基于Jquery的自定义select标签
**/
//console.log($);
$(function(){
	(function(){
		var 
		_select,
		_selectObject,
		_selectClass = 'mySelect',
		_optionClass = 'options',
		_myOption    = 'myOption',
		_selectDefCss      = {
						
		},
		_optionDefCss      = {
						
		},
		_display     = false;
		_selected    = null,
		_animate     = false;
		_current     = 0;



		_select = function(selector,context){
			return new _select.prototype.init(selector,context);
		};
		_select.prototype = {
			init:function(){
				var 
				s = arguments[0],
				c = arguments[1];
				this._selectObject = $(s);
				console.log(this);
				this.defaultCur();
				this.addDefStyle($(s),_selectDefCss);
				this.addDefStyle($(s).find('.'+_optionClass),_optionDefCss);
				this.events();//console.log($('.myOption:eq(0)').attr('value')+'000');
				//this.getSelectValues($(s+' .'+_myOption+':eq(0)').text(),$(s+' .'+_myOption+':eq(0)').attr('value'));
				return this;

			},
			events:function(){
				var o = this;
				o._selectObject.click(function(e){
					e.stopPropagation();
					o.show();
					_display = true;//alert(1);
				})
				o._selectObject.find('.'+_myOption+'').click(function(e){
					e.stopPropagation();//console.log(o);
					o.getSelectValues($(this).text(),$(this).attr('value'));
					o.setCurrent($(this));
					o.close();
				})
				$(document).click(function(){
					o.close();
					_display = false;//alert(1);
				});
				
			},
			show:function(){
				this._selectObject.find('.'+_optionClass).css('display','block');
				_display = true;
			},
			close:function(){
				this._selectObject.find('.'+_optionClass).css('display','none');
				_display = false;
			},
			getSelectValues:function(text,v){//console.log(text)
				this._selectObject.find('.selected').text(text);
				if(typeof (v) != 'undefined')
					this._selectObject.find('.selected,input').attr('value',v);
			},
			addDefStyle:function(s,cssStyle){
				//console.log(s);
				return s.css(cssStyle); 
			},
			setCurrent:function(o){
				this._selectObject.find('.'+_myOption).removeClass('cur');
				o.addClass('cur');
				
				//_current = parseInt(o.index());console.log(_current);
			},
			defaultCur:function(){
				var cur = false;
				var options = this._selectObject.find('.'+_myOption);
				for(var i in options){
					var mat = /cur/i;
					if(mat.test(options[i].className)){
						cur = true;
						break;
					}
				}
				if(!cur){
					var defaultObj = this._selectObject.find('.'+_myOption).eq(0);
					defaultObj.addClass('cur');
				}
				else
					var defaultObj = this._selectObject.find('.'+_myOption+'.cur');
				this.getSelectValues(defaultObj.text(),defaultObj.attr('value'));
			}

		};

		_select.prototype.init.prototype = _select.prototype;
		window.Select = _select;

	})($);
	//Select('.mySelect');
})
