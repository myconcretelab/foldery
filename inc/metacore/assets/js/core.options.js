jQuery(document).ready(function($) {
	"use strict";
	var blog_tab = $('.EasyTabs');
	if(blog_tab.length > 0){
		blog_tab.easytabs();
	}
	/* multiple-field */
	$(document.body).on('change', '.multiple-field select', function(e) {
		"use strict";
		var values = $(this).val();
		$(this).parent().find('input').val(values.join(','));
		return;
	});
	/* color */
	if($('.color-field').length > 0){
		$('.color-field').each(function() {
			var _opacity = false;
			var rgba = $(this).attr('data-rgba');
			if(rgba != undefined && rgba == 'true'){
				_opacity = true;
			}
			$(this).find('input').minicolors({opacity: _opacity, theme: 'bootstrap'});
		});
	}
	/* icon */
	var icon_field;
	$(document.body).on('click', '.icon-field input', function(e) {
		"use strict";
		icon_field = $(this);
		return;
	});
	$(document.body).on('click', '#TB_ajaxContent ul li', function(e) {
		"use strict";
		var icon_class = $(this).find('i').attr('class');
		$(icon_field).val(icon_class);
		$(icon_field).parent().find('i').attr('class',icon_class);
		return;
	});
	/** switch */
	$('.switch-field').each(function(e) {
		"use strict";
		var _switch = $(this).find('input');
		var follow = $(this).attr('data-follow');
		XOptionsFollow(_switch, follow);
	});
	$(document.body).on('click', '.switch-field', function(e) {
		"use strict";
		var _switch = $(this).find('input');
		//check follow ids
		var follow = $(this).attr('data-follow');
		var on = $(this).attr('data-on');
		var off = $(this).attr('data-off');
		//switch actions
		if (_switch.val() == off) {
			_switch.val(on);
			$(this).removeClass('off').addClass('on');
		} else {
			_switch.val(off);
			$(this).removeClass('on').addClass('off');
		}
		XOptionsFollow(_switch, follow);
		return;
	});
	/** slider */
	$('.slider-field .slider-item').each(function() {
		"use strict";
		var srart = 0, max = 100, min = 0;
		if($(this).attr('data-start') != ''){
			srart = parseInt($(this).attr('data-start'));
		}
		if($(this).attr('data-max') != ''){
			max = parseInt($(this).attr('data-max'));
		}
		if($(this).attr('data-min') != ''){
			min = parseInt($(this).attr('data-min'));
		}
		$(this).noUiSlider({
			start: [srart],
			connect: "lower",
			range: {
				'min': [min],
				'max': [max]
			},
			format: wNumb({
				mark: '.',
				decimals: 1
			}),
		});
		var slider_field = $(this).parents('.slider-field');
		$(this).Link('lower').to(slider_field.find('input'));
		$(this).Link('lower').to(slider_field.find('span.curent'));
	})
	/** date time */
	$('.date-field').each(function() {
		"use strict";
		var data_type = $(this).attr('data-type');
		var data_format = $(this).attr('data-format');
		switch (data_type) {
		case 'date':
			$(this).find('input').datetimepicker({
				format: data_format,
				timepicker:false
			});
			break;
		case 'time':
			$(this).find('input').datetimepicker({
				format: data_format,
				datepicker:false
			});
			break;
		default:
			$(this).find('input').datetimepicker({
				format: data_format
			});
			break;
		}
	});
	/** number */
	$(document.body).on('click', '.number-field i', function(e) {
		"use strict";
		var input = $(this).parent().parent().find('input');
		var value = parseInt(input.val());
		if(value == 'NaN'){ value = 0; }
		if($(this).hasClass('fa-caret-up')){
			value++;
			input.val(value);
		} else {
			value--;
			input.val(value);
		}
		return;
	});
	$('.number-field input').change(function(e) {
		if(isNaN($(this).val())){
			alert('Only Enter Numeric');
			$(this).val('');
		}
	});
	$('.number-field input').keydown(function(e) {
		var value = parseInt($(this).val());
		if(value == 'NaN'){ value = 0; }
		if(e.which == 38){
			value++;
			$(this).val(value);
		}
		if(e.which == 40) {
			value--;
			$(this).val(value);
		}
	});
	/* image select */
	$(document.body).on('click', '.image-field ul li', function(e) {
		var value = $(this).attr('data-value');
		$(this).parent().find('li.active').removeClass('active');
		$(this).addClass('active');
		$(this).parents('.image-field').find('input').val(value);
	});
	/* show or hide elements */
	function XOptionsFollow(element, data) {
		"use strict";
		if (data != undefined) {
			data = jQuery.parseJSON(decodeURIComponent(data));
			var active = [];
			if (jQuery.isPlainObject(data)) {
				jQuery.each(data, function(index) {
					"use strict";
					if (element.val() == index) {
						active = data[index];
						jQuery.each(data[index], function(_index) {
							"use strict";
							jQuery('' + data[index][_index] + '').css({
								display : 'block'
							});
						});
					} else {
						jQuery.each(data[index], function(_index) {
							"use strict";
							var off = data[index][_index];
							if (jQuery.inArray(off, active) != 1) {
								jQuery('' + off + '').css({
									display : 'none'
								});
							}
						});
					}
				})
			}
		}
	}
});