//less обновление
function destroyLessCache(pathToCss) { // e.g. '/css/' or '/stylesheets/'

	var host = window.location.host;
	var protocol = window.location.protocol;
	var keyPrefix = protocol + '//' + host + pathToCss;

	for (var key in window.localStorage) {
		if (key.indexOf(keyPrefix) === 0) {
			delete window.localStorage[key];
		}
	}
}

$(document).ready(function () {
	destroyLessCache('/css/');
	//$('.selectpicker').selectpicker();
	$('body').on('click', '.dropdown', function(){
	
		if($(this).hasClass('open')){
				$(this).removeClass("open");
		}
		else{
				$(this).addClass("open");
		}
	});

	$(".disabled").on('click', 'a', function(e){
		e.preventDefault();
	})
	$("body").on('click', '.add-comment', function(){
		serialize = $(this).closest('form').serialize();
		$.ajax({
			url: "/comment/add/",
			data:{data:serialize},
			type: "POST",
			async: true,
			dataType:"json",
			success: function (d) {
				if(d.error == 0 && d.count_comm != 0){
					$("#block_comm").html(d.text);
					$(".wysihtml5-sandbox").contents().find('body').html("");
				}
				else{
					alert(d.text);
				}
			}
		});
	});


    $(".rating").on('click', 'span', function(){
        self = this;
        stars = $(this).data('stars');
        id_book = $(this).closest('.rating').data('id_book');

        $.ajax({
            url  : "https://www.booklot.ru/stars/",
            method: "GET",
            dataType: "json",
            data : {
                stars : stars,
                id_book : id_book
            },
            success: function(json){
                if(json.err == 0){
                    html = "";
                    stars = 5;
                    aver_value = json.stars;
                    transform_value = stars-aver_value;

                    html = "";

                    for(i = 1; i <= 5; i++){
                        class_star = " ";
                        if(i > transform_value){
                            class_star = " class = 'rait-sel' ";
                        }
                        html += '<span '+class_star+' data-stars = "'+(stars-i+1)+'">&#9734;</span>';
                    }
                    html += '<p class = "count_stars">Кол-во голосов: '+json.count+'</p>';
                    $(self).closest('.rating').html(html);

                }

            }

        });
    });

});

function datepicker() {
	$('.y-m-d').datepicker({
		pickTime: false,
		format: 'yyyy-mm-dd'

	});
}



function open_cit(self, e) {
	e.preventDefault();
	$(".cit-comm").css('display','none');
	$(".red-comm").css('display','none');
	$(self).closest('.comment-footer').find(".cit-comm").css('display','block');
	$(self).closest('.comment-footer').find(".wysihtml5").wysihtml5();
}

function open_red(self, e) {
	e.preventDefault();
	$(".cit-comm").css('display','none');
	$(".red-comm").css('display','none');
	$(self).closest('.comment-footer').find(".red-comm").css('display','block');
	$(self).closest('.comment-footer').find(".wysihtml5").wysihtml5();
}

function cit_comm(self, e) {
	e.preventDefault();
	serialize = $(self).closest('form').serialize();
	$.ajax({
		url: "/comment/cit-comm/",
		data:{data:serialize},
		type: "POST",
		async: true,
		dataType:"json",
		success: function (d) {
			if(d.error == 0 && d.count_comm != 0){
				$("#block_comm").html(d.text);
				$(".wysihtml5-sandbox").contents().find('body').html("");
			}
			else{
				alert(d.text);
			}
		}
	});
}


function red_comm(self, e) {
	e.preventDefault();
	serialize = $(self).closest('form').serialize();
	$.ajax({
		url: "/comment/red-comm/",
		data:{data:serialize},
		type: "POST",
		async: true,
		dataType:"json",
		success: function (d) {
			if(d.error == 0 && d.count_comm != 0){
				$("#block_comm").html(d.text);

			}
			else{
				alert(d.text);
			}
		}
	});
}

function del_comm(self, e) {
	e.preventDefault();
	c = $(self).closest('.comments-list');
	$.ajax({
		url: "/comment/del/",
		data:{id:c.data('id')},
		type: "POST",
		async: true,
		dataType:"json",
		success: function (d) {
			if(d.error == 0){
				$("#block_comm").html(d.text);
			}
			else{
				alert(d.text);
			}
		}
	});
}
function ajaxCommOnline(){

	$.ajax({
		url: "/comment/online/",
		data:{id:$("input[name='id']").val()},
		type: "POST",
		async: true,
		dataType:"json",
		success: function (d) {
			if(d.error == 0 && d.count_comm != 0){
				$("#block_comm").html(d.text);
			}
			else{
				//alert(d.text);
			}
		}
	});

}

