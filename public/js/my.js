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

    if ($('*').is("#wiswig-smiles")) {
        $("#wiswig-smiles").emojioneArea();
    }

    $('body').on('click', '.remove-bl', function () {
        $(this).closest('.b-v').remove();
    });

    $('body').on('click', '.delete.comment', function () {
        comment_id = $(this).data('comment_id');
        $.ajax({
            url: '/comments/delete/',
            data: {comment_id: comment_id},
            type: "POST",
            async: true,
            dataType: "json",
            success: function (d) {
                if (d.error == 0) {
                    $("li.comment-id-" + d.text).animate({
                        opacity: 0.25,
                        left: '+=50',
                        height: 'toggle'
                    }, 1000, function () {
                        $(this).remove();
                        size = $("#comments-list li").size();
                        $("#count-comments").html(size);
                    });
                    toastr.success('Комментарий удален', null, {"closeButton": true});

                }
                else {
                    toastr.error(d.text, null, {"closeButton": true});
                }
            }
        });

    });

    $('body').on('click', '#comment-send', function () {

        text = $(".emojionearea-editor").html();
        book_id = $(this).data('book_id');

        $.ajax({
            url: '/comments/add/',
            data: {text: text, book_id: book_id},
            type: "POST",
            async: true,
            dataType: "json",
            success: function (d) {

                if (d.error == 0) {
                    $("#comments-list").append(d.text).show('slow');
                    $("#no-comment").addClass('no-visible');
                    toastr.success('Комментарий добавлен', null, {"closeButton": true});
                    $(".emojionearea-editor").html("");
                    size = $("#comments-list li").size();
                    $("#count-comments").html(size);
                }
                else {
                    toastr.error(d.text, null, {"closeButton": true});
                }
            }
        });

    });

    $('.make-switch.is-radio').on('switch-change', function () {

        book_id = $(this).data('book_id');
        $.ajax({
            url: '/add-book-like/',
            data: {book_id: book_id},
            type: "POST",
            async: true,
            dataType: "json",
            success: function (d) {
                if (d.error == 0) {
                    $('#like-number').html(d.text);
                    toastr.success('Изменение ваших лайков', null, {"closeButton": true});
                }
                else {
                    toastr.error(d.text, null, {"closeButton": true});
                }
            }
        });
    })

    $('body').on('change', '#status-my-book', function () {

        status_id = $(this).val();
        book_id = $(this).data('book_id');

        $.ajax({
            url: '/add-status-book/',
            data: {status_id: status_id, book_id: book_id},
            type: "POST",
            async: true,
            dataType: "json",
            success: function (d) {
                if (d.error == 0) {
                    toastr.success('Изменение статуса книги', null, {"closeButton": true});
                }
                else {
                    toastr.error(d.text, null, {"closeButton": true});
                }
            }
        });

    });

    $('body').on('click', '#click-my-book', function () {
        book_id = $(this).data('book_id');
        $.ajax({
            url: '/add-my-book/',
            data: {book_id: book_id},
            type: "POST",
            async: true,
            dataType: "json",
            success: function (d) {
                if (d.error == 0) {
                    toastr.success('Изменение вашей библиотеке', null, {"closeButton": true});
                    $("#click-my-book").replaceWith(d.text);
                }
                else {
                    toastr.error(d.text, null, {"closeButton": true});
                }
            }
        });
    });

    $('body').on('click', '.url-click', function (e) {
        url = $(this).data('url');
        window.location.href = url;

    });

    $('body').on('click', '.dropdown', function () {

        if ($(this).hasClass('open')) {
            $(this).removeClass("open");
        }
        else {
            $(this).addClass("open");
        }
    });

    $(".disabled").on('click', 'a', function (e) {
        e.preventDefault();
    })

    $(".rating").on('click', 'span', function () {
        self = this;
        stars = $(this).data('stars');
        id_book = $(this).closest('.rating').data('id_book');

        $.ajax({
            url: "/stars/",
            method: "GET",
            dataType: "json",
            data: {
                stars: stars,
                id_book: id_book
            },
            success: function (json) {
                if (json.err == 0) {
                    html = "";
                    stars = 5;
                    aver_value = json.stars;
                    transform_value = stars - aver_value;

                    html = "";

                    for (i = 1; i <= 5; i++) {
                        class_star = " ";
                        if (i > transform_value) {
                            class_star = " class = 'rait-sel' ";
                        }
                        html += '<span ' + class_star + ' data-stars = "' + (stars - i + 1) + '">&#9734;</span>';
                    }
                    html += '<p class = "count_stars">Кол-во голосов: ' + json.count + '</p>';
                    $(self).closest('.rating').html(html);
                    toastr.success('Оценка принята', null, {"closeButton": true});
                }
                else {
                    toastr.error('Возникла ошибка', null, {"closeButton": true});
                }

            }

        });
    });

    $('body').on('click', '[data-ad-enable]', function (e)
    {alert(123);
        var self = $(this);
        var ad_id = self.data('ad_id');
        var page = self.data('page');
        $.ajax({
            url: '/ad-stat-add/',
            data: {ad_id: ad_id, page: page},
            type: "POST"
        });

    });
});

function datepicker() {
    $('.y-m-d').datepicker({
        pickTime: false,
        format: 'yyyy-mm-dd'

    });
}


