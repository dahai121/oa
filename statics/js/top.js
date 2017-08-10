//v4 header navi
function HeaderNavi(settings) {
	this.settings = jQuery.extend({
		navi  : 'index',
		navi2 : ''
	}, settings);
	var navi_controller = this;
	
	var navi_timer = null;
	var _nav = $(".head_menu ul li");
	var _nav_conts = $(".head_nav_2 > div");

	function clear_all_navi_on() {
		_nav.each(function() {
			$(this).find('a').removeClass($(this).attr('cf') + '_on');
		});
	}

	function reset_navi() {
		clear_all_navi_on();
		var navion = _nav.filter('[navi]').attr('navi');
		_nav.find('.' + navion).addClass(navion + '_on');
		_nav_conts.hide().filter('[cf=' + navion + ']').show();
	}

	_nav.hover(function() {
		if(navi_timer) clearTimeout(navi_timer);
		_nav_conts.eq(_nav.index(this)).show().siblings().hide();
		clear_all_navi_on();
		var my_class = $(this).attr('cf');
		$(this).find('a').addClass(my_class + "_on");
	}, function() {
		navi_timer = setTimeout(function() {
			reset_navi();			
		}, 200);
	});

	_nav_conts.hover(function() {
		clearTimeout(navi_timer);
	}, function() {
		navi_timer = setTimeout(function() {
			reset_navi();			
		}, 400);
	});
	
	var navi_pos = this.settings.navi;
	_nav.find('.'+navi_pos).addClass(navi_pos+'_on').parent().attr('navi', navi_pos);
	_nav_conts.hide().filter('[cf="' + navi_pos + '"]').show();
	if(this.settings.navi2 != '') {
		var st ='text-decoration:underline; font-weight:bold; color:#322d29;';
		_nav_conts.find('a').each(function(){
			var url = $(this).attr('href');
			var fs = navi_controller.settings.navi2;		
			if(url.endsWith(fs)) {
				$(this).attr('style',st);
			}
		});
	}
	
	var scrolltotopOffsetY = 202;
	if(typeof __navi_id != "undefined" && __navi_id =="index") {
		scrolltotopOffsetY = 164;
	}
	
	var scrolltotop = {			
			
			//startline: Integer. Number of pixels from top of doc scrollbar is scrolled before showing control
			//scrollto: Keyword (Integer, or "Scroll_to_Element_ID"). How far to scroll document up when control is clicked on (0=top).
			setting: {startline:20, scrollto:0, scrollduration:300, fadeduration:[500, 100]},
			controlHTML: '<img src="statics/images/besttop.gif" style="width:24px; height:73px" />', //HTML for control, which is auto wrapped in DIV w/ ID="topcontrol"
			controlattrs: {offsetx:0, offsety:scrolltotopOffsetY}, //offset of control relative to right/ bottom of window corner
			anchorkeyword: '#top', //Enter href value of HTML anchors on the page that should also act as "Scroll Up" links

			state: {isvisible:false, shouldvisible:false},

			scrollup:function(){
				if (!this.cssfixedsupport) //if control is positioned using JavaScript
					this.$control.css({opacity:0}); //hide control immediately after clicking it
				var dest=isNaN(this.setting.scrollto)? this.setting.scrollto : parseInt(this.setting.scrollto);
				if (typeof dest=="string" && jQuery('#'+dest).length==1) //check element set by string exists
					dest=jQuery('#'+dest).offset().top;
				else
					dest=0;
				this.$body.animate({scrollTop: dest}, this.setting.scrollduration);
			},

			keepfixed:function(){
				var $window=jQuery(window);
				var controlx=($window.scrollLeft() + $window.width())/2;
				var controly=$window.scrollTop() + $window.height() - this.$control.height() - this.controlattrs.offsety;
				this.$control.css({left:controlx+'px', top:controly+'px'});
			},

			togglecontrol:function(){
				var scrolltop=jQuery(window).scrollTop();
				if (!this.cssfixedsupport)
					this.keepfixed();
				this.state.shouldvisible=(scrolltop>=this.setting.startline)? true : false;
				if (this.state.shouldvisible && !this.state.isvisible){
					this.$control.stop().animate({opacity:1}, this.setting.fadeduration[0]);
					this.state.isvisible=true;
				}
				else if (this.state.shouldvisible==false && this.state.isvisible){
					this.$control.stop().animate({opacity:0}, this.setting.fadeduration[1]);
					this.state.isvisible=false;
				}
			},
			
			init:function(){
				jQuery(document).ready(function($){
					var mainobj=scrolltotop;
					var iebrws=document.all;
					mainobj.cssfixedsupport=!iebrws || iebrws && document.compatMode=="CSS1Compat" && window.XMLHttpRequest; //not IE or IE7+ browsers in standards mode
					mainobj.$body=(window.opera)? (document.compatMode=="CSS1Compat"? $('html') : $('body')) : $('html,body');
					mainobj.$control=$('<div id="topcontrol">'+mainobj.controlHTML+'</div>')
						.css({position:mainobj.cssfixedsupport? 'fixed' : 'absolute', bottom:mainobj.controlattrs.offsety, left:"50%","marginLeft":"575px" ,opacity:0, cursor:'pointer'})
						.attr({title:'返回顶部'})
						.click(function(){mainobj.scrollup(); return false;})
						.appendTo('body');
					if (document.all && !window.XMLHttpRequest && mainobj.$control.text()!='')
						mainobj.$control.css({width:mainobj.$control.width()});
					mainobj.togglecontrol();
					$('a[href="' + mainobj.anchorkeyword +'"]').click(function(){
						mainobj.scrollup();
						return false;
					});
					$(window).bind('scroll resize', function(e){
						mainobj.togglecontrol();
					});
				});
			}
		};

		scrolltotop.init();
}
