$(document).scroll(function (e) {
	heightTop = $(this).scrollTop();
	if (heightTop > 90) {
		$(".pod_menu .content_menu").css('margin-top', heightTop - 128);
	}
	else {
		$(".pod_menu .content_menu").css('margin-top', -40);
	}
})
var arhiv = {
    self : false,
    e    : false,
    filter: false
}
$(document).ready(function () {

	//$('.selectpicker').selectpicker();
	$('.menu_left_list').on('click','h5',function(){
		$(this).closest('.list_left_menu_filter').find('.novis_type').slideToggle();
	});

	$('body').on('click', '.fon_error', function () {
		errorMessenger('2', '', this);
	});
    $('body').on('click','.clear_filter',function(e){
        e.preventDefault();
        if(arhiv.filter != false)
        $(".right_content").html(arhiv.filter);
    })

    $("body").on('click', '.helper_filter a', function (e) {
        e.preventDefault();
        url = $(this).attr('href');
        data = new Object();
        data['type'] = 1;
        data['contents'] = s;
        succ = "filter_content_right";
        ajaxgo(url, data, succ, '','','GET');
		$('html, body').animate({scrollTop:300}, 'slow');
    });

	$("body").on('click', '#filter_left input[type="checkbox"]', function () {
        if(arhiv.filter == false){
            arhiv.filter = $(".right_content").html();
        }
        arhiv.self = this;
		var newURL = window.location.protocol + "//" + window.location.host + "" + window.location.pathname;
		s = $(this).closest('form').serialize();
		url = newURL;
		data = new Object();
		data['type'] = 1;
		data['contents'] = s;
		succ = "filter_content_count";
		if(history.pushState) {
			history.pushState({"id":100}, document.title, newURL+'?'+s);
		}
		ajaxgo(url, data, succ, 'json','','GET');
	});


	$('body #menu').on('keyup', 'input[name="label_menu"]', function () {
		simbol = $(this).val();
		simbol_eng = translit(simbol);
		$("input[name='route_menu']").val(simbol_eng);
	});
	$('body #template').on('keyup', '.name_type_field', function () {
		simbol = $(this).val();
		simbol_eng = translit(simbol);
		$(this).closest('tr').find('.alias_type_field').val(simbol_eng);
	});
	$('body #contentsform').on('keyup', 'input[name="name_contents"]', function () {
		simbol = $(this).val();
		simbol_eng = translit(simbol);
		$("input[name='alias_contents']").val(simbol_eng);
	});
	$('body #section').on('keyup', 'input[name="name_section"]', function () {
		simbol = $(this).val();
		simbol_eng = translit(simbol);
		$("input[name='alias_section']").val(simbol_eng);
	});
	$('body #brand').on('keyup', 'input[name="name_brand"]', function () {
		simbol = $(this).val();
		simbol_eng = translit(simbol);
		$("input[name='alias_brand']").val(simbol_eng);
	});
	$('body #model').on('keyup', 'input[name="name_model"]', function () {
		simbol = $(this).val();
		simbol_eng = translit(simbol);
		$("input[name='alias_model']").val(simbol_eng);
	});
	$('body').on('click', '.close_error', function () {
		errorMessenger('2', '', this);
	});




});
/*dateciper*/
function datetimepicker() {
	$('.datetimepicker').datetimepicker({});
}
function datepicker() {
	$('.datepicker').datetimepicker({
		pickTime: false
	});
}
/*число*/
function integer() {

	$(".integer").keydown(function (e) {
		var keyPressed;
		if (!e) var e = window.event;
		if (e.keyCode) keyPressed = e.keyCode;
		else if (e.which) keyPressed = e.which;
		var hasDecimalPoint = (($(this).val().split('.').length - 1) > 0);
		if (keyPressed == 46 || keyPressed == 8 || ((keyPressed == 190 || keyPressed == 110) && (!hasDecimalPoint)) || keyPressed == 9 || keyPressed == 27 || keyPressed == 13 ||
			// Allow: Ctrl+A
			(keyPressed == 65 && e.ctrlKey === true) ||
			// Allow: home, end, left, right
			(keyPressed >= 35 && keyPressed <= 39)) {
			// let it happen, don't do anything
			return;
		}
		else {
			// Ensure that it is a number and stop the keypress
			if (e.shiftKey || (keyPressed < 48 || keyPressed > 57) && (keyPressed < 96 || keyPressed > 105 )) {
				e.preventDefault();
			}
		}
	});
}
/*tinymce*/
function tinimce() {
	tinymce.init({
		selector: ".wiswigs",
		plugins: [
			"advlist autolink lists link image charmap print preview hr anchor pagebreak",
			"searchreplace wordcount visualblocks visualchars code fullscreen",
			"insertdatetime media nonbreaking save table contextmenu directionality",
			"emoticons template paste textcolor jbimages"
		],
		toolbar1: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | jbimages",
		toolbar2: "print preview media | forecolor backcolor emoticons",
		image_advtab: true,
		templates: [
			{title: 'Test template 1', content: 'Test 1'},
			{title: 'Test template 2', content: 'Test 2'}
		]
	});
}
/*translit*/

function translit(text) {
	return text.replace(/([а-яё])|([\s_-])|([^a-z\d])/gi,
		function (all, ch, space, words, i) {
			if (space || words) {
				return space ? '-' : '';
			}
			var code = ch.charCodeAt(0),
				index = code == 1025 || code == 1105 ? 0 :
					code > 1071 ? code - 1071 : code - 1039,
				t = ['yo', 'a', 'b', 'v', 'g', 'd', 'e', 'zh',
					'z', 'i', 'y', 'k', 'l', 'm', 'n', 'o', 'p',
					'r', 's', 't', 'u', 'f', 'h', 'c', 'ch', 'sh',
					'shch', '', 'y', '', 'e', 'yu', 'ya'
				];
			return t[index];
		});
}

function table() {
	$(function () {
		$.extend($.tablesorter.themes.bootstrap, {
			// these classes are added to the table. To see other table classes available,
			// look here: http://twitter.github.com/bootstrap/base-css.html#tables
			table: 'table table-bordered',
			header: 'bootstrap-header', // give the header a gradient background
			footerRow: '',
			footerCells: '',
			icons: '', // add "icon-white" to make them white; this icon class is added to the <i> in the header
			sortNone: 'bootstrap-icon-unsorted',
			sortAsc: 'icon-chevron-up',
			sortDesc: 'icon-chevron-down',
			active: '', // applied when column is sorted
			hover: '', // use custom css here - bootstrap class may not override it
			filterRow: '', // filter row class
			even: '', // odd row zebra striping
			odd: ''  // even row zebra striping
		});

		// call the tablesorter plugin and apply the uitheme widget
		$("#table1").tablesorter({
			// this will apply the bootstrap theme if "uitheme" widget is included
			// the widgetOptions.uitheme is no longer required to be set
			theme: "bootstrap",

			widthFixed: true,

			headerTemplate: '{content} {icon}', // new in v2.7. Needed to add the bootstrap icon!

			// widget code contained in the jquery.tablesorter.widgets.js file
			// use the zebra stripe widget if you plan on hiding any rows (filter widget)
			widgets: [ "uitheme", "filter", "zebra" ],

			widgetOptions: {
				// using the default zebra striping class name, so it actually isn't included in the theme variable above
				// this is ONLY needed for bootstrap theming if you are using the filter widget, because rows are hidden
				zebra: ["even", "odd"],

				// reset filters button
				filter_reset: ".reset"

				// set the uitheme widget to use the bootstrap theme class names
				// this is no longer required, if theme is set
				// ,uitheme : "bootstrap"
			}
		})
	});
}
var booking = {

	add: function (self, e, id_contents) {
		url = "/ajax/2";
		data = new Object();
		data['id_contents'] = id_contents;
		data['money_all']	= $("input[name = 'money_all']").val();
		data['id_cb']		= $("input[name = 'id_cb']").val();
		data['id_category']		= $("input[name = 'id_category']").val();
		succ = "bookingAdd";
		ajaxgo(url, data, succ);
	},
	clear: function (self, e) {
		url = "/ajax/3";
		succ = "bookingClear";
		ajaxgo(url, 0, succ);
	},
	send: function (self, e) {
		url = "/ajax/6";
		succ = "bookingSend";
		ajaxgo(url, 0, succ);
	},
	pay: function (self, e) {
		e.preventDefault();
		url = "/ajax/7";
		succ = "bookingPay";
		form = $(self).closest('form').serialize();
		data = form;
		ajaxgo(url, data, succ, 'json');
	}
}
var basket = {
	countProduct: function (self, e, id_content) {
		count = $(self).val();
		url = "/ajax/4";
		data = new Object();
		data['type'] = 4;
		data['id_content'] = id_content;
		data['set_count'] = count;
		succ = "basket_replace";
		ajaxgo(url, data, succ);
	},
	del: function (self, e, id_content) {
		url = "/ajax/5";
		data = new Object();
		data['type'] = 5;
		data['id_content'] = id_content;
		succ = "basket_replace";
		ajaxgo(url, data, succ);
	}
}

function ajaxgo(url, data, succ, datatype, self, type) {
    $(".autoloading").css('display','block');
	type=(type)?type:'POST';
	$.ajax({
		url: url,
		data: {data: data},
		type: type,
		dataType: datatype,
		success: function (d) {
            $(".autoloading").css('display','none');
			if (!d) {
				errorMessenger('1', "Ошибка запроса");
				return false;
			}
			if (/^[\s]*error.*/ig.test(d)) {
				errorMessenger('1', d);
				return false;
			}
			switch (succ) {
				case 'addTemplateContent':
					$('#addTemplateContent').html(d);
					datepicker();
					datetimepicker();
					tinimce();
					integer();
					break;
                case 'filter_content_right':
                    $('.left_content').html(d);
                    break;
                case  'filter_content_count':
                    $(".helper_filter").remove();
                    $(arhiv.self).closest('label').append('<div class="helper_filter"><a href = "'+ window.location.pathname + '?' + d.url+'&con=1">'+ d.count+'</a></div>');
                    break;
                case  'filter_content_count2':
                    $(".helper_filter").remove();
                    $("#price_money").append('<div class="helper_filter money"><a href = "'+ window.location.pathname + '?' + d.url+'&con=1">'+ d.count+'</a></div>');
                    break;
				case 'filter_content':
					if(d.success == true){
						$(".left_content").html(d.result);
						$("#filter_left").html(d.filter);
					}
					break;
				case 'bookingAdd':
					$('#basket').html(d);
					if (d == 0) {
						$('#checkout').attr('class', 'novis');
						$("#clear_basket").attr('class', 'novis');
						$('#basket').attr('class', 'badge');
						$('#basket').html(0);
						$("#basket_url").html("Корзина");
					}
					else {
						$('#checkout').attr('class', 'btn btn-danger vis');
						$("#clear_basket").attr('class', 'btn vis');
						$('#basket').attr('class', 'col_tov badge-success');
						$('#basket').html(d);
						$("#basket_url").html('<a href="/basket">Корзина</a>');
					}
					break;
				case 'bookingClear':
					if (d == 1) {
						$('#checkout').attr('class', 'novis');
						$("#clear_basket").attr('class', 'novis');
						$('#basket').attr('class', 'col_tov');
						$('#basket').html(0);
						$("#basket_url").html("Корзина");
					}
					break;
				case 'basket_replace':
					location.reload();
					break;
				case 'bookingSend':
					errorMessenger('1', d);
					break;
				case 'bookingPay':
					$(".text-error").remove();
					if (d.err == 1) {
						$.each(d, function (k, v) {
							$("input[name='" + k + "']").closest('td').prepend('<p class="text-error">' + v + '</p>');
						});
						return false;
					}
//
//					$('#checkout').attr('class', 'novis');
//					$("#clear_basket").attr('class', 'novis');
//					$('#basket').attr('class', 'badge');
//					$('#basket').html(0);
//					$("#basket_url").html("Корзина");
					$("#bookingSend").html("Спасибо за заказ, менеджер с вами свяжется для детальной информации");
                    setTimeout(function() { location.reload() }, 2000);
                    break;
			}
		}
	});
}


function errorMessenger(type, message, self) {
	switch (type) {
		case '1':
			if ($('body').hasClass('.error_block')) {
				errorMessenger(2);
			}
			$('body').append('<div class="error_block"><div class="fon_error"></div><div  class="block_content"><div class="nodrop"><div class="close_error"></div>' + message + '</div></div></div>');
			$('.block_content').css({'top': $(document).scrollTop() + 100});
			$(".block_content").draggable({cancel: '.nodrop', cursor: "crosshair" });
			break;
		case '2':
			$(self).closest('.error_block').remove();
			break;
		case '3':
			$('.error_block').remove();
			break;
	}
}

var category = {
	self : false,
	e : false,
	switcher : function(self,e,money,id_cb,id_category){
		category.self = self;
		category.e = e;
		$("#price").text(money);
		$("input[name = 'money_all']").val(money);
		$("input[name = 'id_cb']").val(id_cb);
		$("input[name = 'id_category']").val(id_category);
		$.each($(self).closest('ul').find('li'),function(a,b){
			$(this).attr('class','');
		});
		$(self).attr('class','active');
	}
}

