/*! Data-Grid versi 1
 * 2015 Achmad Sarwat
 */

(function ($) {
    "use strict";
    var methods = {
        init: function (options, callback) {
            var form = this;
            options = (!form.data('tabelGridAjax') || form.data('tabelGridAjax') == null) ? methods._saveOptions(form, options) : form.data('tabelGridAjax');
            options = $.extend(true, {}, options, { data: { element: form.prop("id") } });

            return this.each(function () {
                form.trigger("sukses:beforeLoad");

                $.ajax({
                    type: options.type,
                    url: options.url,
                    data: options.data,
                    cache: false,
                    dataType: "json",
                    success: function (data) {
                        if (options.infoPage) {
                            if (options.infoPageClass) {
                                $(options.infoPageClass).html(data.infoData);
                            }
                        }

                        if (options.linkPage) {
                            if (options.linkPageClass) {
                                $(options.linkPageClass).html(methods.pageLink(form, data.totalData, data.hasilnya, callback));
                            }
                        }

                        var domCek1 = form.find('tbody').length;
                        if (domCek1 > 0)
                            form.find('tbody').html("").append(data.items);
                        else
                            form.html("").append(data.items);

                        if (callback && typeof callback === "function") {
                            callback();
                        }
                        form.trigger("sukses:diload");
                    }
                });
            });
        },
        _saveOptions: function (form, options) {
            var userOptions = $.extend(true, {}, $.defaultValue, options);
            form.data('tabelGridAjax', userOptions);
            return userOptions;
        },
        draw: function (opt, callback) {
            var form = $(this);
            if (typeof opt === 'object') {
                var oldOptions = form.data('tabelGridAjax');
                var newOptions = $.extend(opt.data, { start: 1 });
                var userOptions = $.extend(true, {}, oldOptions, opt);
                form.data('tabelGridAjax', userOptions);
            }
            return methods.init.apply(form, arguments, callback);
        },
        btnResponsive: function (page, pages, i, elm) {
            var j = 3, k = 1;
            if (pages > j) {
                if (page == 1) {
                    if (i > j) elm.addClass("hidden-xs");
                } else if (page == pages) {
                    if (i <= (pages - j)) elm.addClass("hidden-xs");
                } else {
                    if (i < page - k || i > page + k) elm.addClass("hidden-xs");
                }
            }
        },
        pageLen: function (len, callback) {
            var form = $(this);
            var oldOptions = form.data('tabelGridAjax');
            var userOptions = $.extend(true, {}, oldOptions, { data: { length: len, start: 1 } });
            form.data('tabelGridAjax', userOptions);
            return methods.init.apply(form, arguments, callback);
        },
        pageMove: function (hal, form, callback) {
            var oldOptions = form.data('tabelGridAjax');
            var userOptions = $.extend(true, {}, oldOptions, { data: { start: hal } });
            form.data('tabelGridAjax', userOptions);
            return methods.init.apply(form, arguments, callback);
        },
        pageLink: function (form, total_rows, arrpages, callback) {
            var prev, first, last, next, numbers, container, wrapper;
            var
                width = $(window).width(),
                buttons = (width > 767 ? 7 : 3),
                half = Math.floor(buttons / 2);
            var clickHandler = function (e) {
                e.preventDefault();
                if (!$(e.currentTarget).hasClass("disabled") && !$(e.currentTarget).hasClass("active")) {
                    methods.pageMove((e.data.page), form, callback);
                }
            };

            if ((total_rows == 0 || arrpages.per_page == 0) || (arrpages.num_pages === 1)) {
                return '';
            }

            if (buttons < 0) {
                return '';
            }

            var uri_page_number = arrpages.cur_page;

            var start = ((arrpages.cur_page - buttons) > 0) ? arrpages.cur_page - (buttons - 1) : 1;
            var end = ((arrpages.cur_page + buttons) < arrpages.num_pages) ? arrpages.cur_page + buttons : arrpages.num_pages;

            wrapper = $('<div>');
            container = $('<ul>', { "class": "pagination" }).appendTo(wrapper);
            first = $('<li>').prepend($('<span>').html($('<i>', { "class": "fa fa-angle-double-left" }))).appendTo(container);
            prev = $('<li>').prepend($('<span>').html($('<i>', { "class": "fa fa-angle-left" }))).appendTo(container);

            first.addClass("first").css("cursor", "pointer").children().attr("data-page", 1).bind("click", { "page": 1 }, clickHandler);
            var cekPrev = (arrpages.cur_page === 1) ? 1 : uri_page_number - 1;
            prev.addClass("prev").css("cursor", "pointer").children().attr("data-page", cekPrev).bind("click", { "page": cekPrev }, clickHandler);


            if (arrpages.num_pages <= buttons) {
                for (var i = 1; i <= arrpages.num_pages; i++) {
                    numbers = $('<li>').css("cursor", "pointer").prepend($('<span>', { "data-page": i }).html(i)).appendTo(container);
                    if (arrpages.cur_page == i) numbers.addClass("active").css("cursor", "default");
                    methods.btnResponsive(arrpages.cur_page, arrpages.num_pages, i, numbers);
                    numbers.on("click", { page: i }, clickHandler);
                }
            } else if ((arrpages.cur_page + half) > arrpages.num_pages) {
                for (var i = (arrpages.num_pages - buttons + 1); i <= arrpages.num_pages; i++) {
                    numbers = $('<li>').css("cursor", "pointer").prepend($('<span>', { "data-page": i }).html(i)).appendTo(container);
                    if (arrpages.cur_page == i) numbers.addClass("active").css("cursor", "default");
                    methods.btnResponsive(arrpages.cur_page, arrpages.num_pages, i, numbers);
                    numbers.on("click", { page: i }, clickHandler);
                }
            } else if (arrpages.cur_page <= arrpages.num_pages) {
                if ((arrpages.cur_page - half) <= 0) {
                    for (var i = 1; i <= buttons; i++) {
                        numbers = $('<li>').css("cursor", "pointer").prepend($('<span>', { "data-page": i }).html(i)).appendTo(container);
                        if (arrpages.cur_page == i) numbers.addClass("active").css("cursor", "default");
                        methods.btnResponsive(arrpages.cur_page, arrpages.num_pages, i, numbers);
                        numbers.on("click", { page: i }, clickHandler);
                    }
                } else {
                    for (var i = (arrpages.cur_page - half); i <= (arrpages.cur_page + half); i++) {
                        numbers = $('<li>').css("cursor", "pointer").prepend($('<span>', { "data-page": i }).html(i)).appendTo(container);
                        if (arrpages.cur_page == i) numbers.addClass("active").css("cursor", "default");
                        methods.btnResponsive(arrpages.cur_page, arrpages.num_pages, i, numbers);
                        numbers.on("click", { page: i }, clickHandler);
                    }
                }
            }

            next = $('<li>').prepend($('<span>').html($('<i>', { "class": "fa fa-angle-right" }))).appendTo(container);
            last = $('<li>').prepend($('<span>').html($('<i>', { "class": "fa fa-angle-double-right" }))).appendTo(container);

            var cekNext = (arrpages.cur_page === arrpages.num_pages) ? arrpages.num_pages : arrpages.cur_page + 1;
            next.addClass("next").css("cursor", "pointer").children().attr("data-page", cekNext).bind("click", { "page": cekNext }, clickHandler);

            var cekLast = arrpages.num_pages;
            last.addClass("last").css("cursor", "pointer").children().attr("data-page", cekLast).bind("click", { "page": cekLast }, clickHandler);

            return (wrapper);
        },
    };

    $.fn.ajaxGridNew = function (methodOrOptions) {
        var form = $(this);
        if (typeof (methodOrOptions) == 'string' && methodOrOptions.charAt(0) != '_' && methods[methodOrOptions]) {
            return methods[methodOrOptions].apply(form, Array.prototype.slice.call(arguments, 1));
        } else if (typeof methodOrOptions === 'object' || !methodOrOptions) {
            return methods.init.apply(form, arguments);
        } else {
            return $.error("Method " + methodOrOptions + " does not exist on jQuery.gajelas");
        }
    };
    $.defaultValue = {
        type: "post",
        url: "",
        data: {},
        infoPage: true,
        infoPageClass: "",
        linkPage: true,
        linkPageClass: "",
        footerPage: true,
        infoPageCenter: false,
    };
})(jQuery);
