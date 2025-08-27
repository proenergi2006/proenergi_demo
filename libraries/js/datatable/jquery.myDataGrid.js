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

			if (!options.modal) {
				if ($(document).find("div#" + form.prop('id') + "-modal").length == 0) {
					var opt_modal = 'data-backdrop="true" data-keyboard="false"';
					$("body").append('' +
						'<div class="modal modal-loading-data" id="' + form.prop('id') + '-modal" tabindex="-1" role="dialog" aria-hidden="true" ' + opt_modal + '>' +
						'<div class="modal-dialog"></div>' +
						'</div>');
				}
				$("div#" + form.prop('id') + "-modal").modal();
			} else {
				if ($(options.modal).find("div#" + form.prop('id') + "-modal").length == 0) {
					$(options.modal).append('<div class="mydatagrid-loading" id="' + form.prop('id') + '-modal"></div>');
				}
				$(options.modal).css({ "overflow": "hidden", "min-height": "110px" });
				$("div#" + form.prop('id') + "-modal").css("display", "block");
			}
			return this.each(function () {
				$.ajax({
					type: options.type,
					url: options.url,
					data: options.data,
					cache: false,
					dataType: "json",
					success: function (data) {
						var dom1 = $("<div>");
						var dom2 = $("<div>", { class: "row" }).appendTo(dom1);
						if (!options.infoPageCenter) {
							var dom3 = $("<div>", { class: "col-sm-5 col-md-5" }).appendTo(dom2);
							var dom4 = $("<div>", { class: "col-sm-7 col-md-7" }).appendTo(dom2);
							if (data.totalData > 0) {
								dom3.prepend('<div class="text-left-rsp">' + data.infoData + '</div>');
								dom4.prepend(methods.pageNav(form, data.pages, data.page, callback).addClass("text-right-rsp"));
							}
						} else {
							var dom3 = $("<div>", { class: "col-sm-12" }).appendTo(dom2);
							if (data.totalData > 0) {
								dom3.prepend(methods.pageNav(form, data.pages, data.page, callback).addClass("text-center"));
								dom3.prepend('<div class="text-center">' + data.infoData + '</div>');
							}
						}
						var domCek1 = form.find('tbody').length;
						if (domCek1 > 0)
							form.find('tbody').html("").append(data.items);
						else
							form.html("").append(data.items);

						if (options.footerPage == true) {
							if (data.totalData > 0) {
								dom1.addClass("box-footer");
							} else {
								dom1.addClass("box-footer no-border no-padding");
							}

							if (form.parent().next().is("div.box-footer")) {
								form.parent().next().remove();
								form.parent().after(dom1);
							} else {
								form.parent().after(dom1);
							}
						} else if (options.footerPage == false) {
							dom1.addClass("footer");
							if (form.next().is("div.footer")) {
								form.next().remove();
								form.after(dom1);
							} else {
								form.after(dom1);
							}
						}

						if (!options.modal) {
							$("div#" + form.prop('id') + "-modal").modal('hide');
						} else {
							$(options.modal).css("overflow", "");
							$("div#" + form.prop('id') + "-modal").css("display", "").remove();
						}
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
		pageNav: function (form, pages, page, callback) {
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

			page = (page == "") ? 1 : parseInt(page);
			pages = parseInt(pages);
			wrapper = $('<div>');
			container = $('<ul>', { "class": "pagination" }).appendTo(wrapper);
			first = $('<li>').prepend($('<span>').html($('<i>', { "class": "fa fa-angle-double-left" }))).appendTo(container);
			prev = $('<li>').prepend($('<span>').html($('<i>', { "class": "fa fa-angle-left" }))).appendTo(container);
			if (page > 1) {
				first.addClass("first").css("cursor", "pointer").children().attr("data-page", 1).bind("click", { "page": 1 }, clickHandler);
				prev.addClass("prev").css("cursor", "pointer").children().attr("data-page", (page - 1)).bind("click", { "page": (page - 1) }, clickHandler);
			} else {
				first.addClass("first disabled");
				prev.addClass("prev disabled");
			}

			if (pages <= buttons) {
				for (var i = 1; i <= pages; i++) {
					numbers = $('<li>').css("cursor", "pointer").prepend($('<span>', { "data-page": i }).html(i)).appendTo(container);
					if (page == i) numbers.addClass("active").css("cursor", "default");
					methods.btnResponsive(page, pages, i, numbers);
					numbers.on("click", { page: i }, clickHandler);
				}
			} else if ((page + half) > pages) {
				for (var i = (pages - buttons + 1); i <= pages; i++) {
					numbers = $('<li>').css("cursor", "pointer").prepend($('<span>', { "data-page": i }).html(i)).appendTo(container);
					if (page == i) numbers.addClass("active").css("cursor", "default");
					methods.btnResponsive(page, pages, i, numbers);
					numbers.on("click", { page: i }, clickHandler);
				}
			} else if (page <= pages) {
				if ((page - half) <= 0) {
					for (var i = 1; i <= buttons; i++) {
						numbers = $('<li>').css("cursor", "pointer").prepend($('<span>', { "data-page": i }).html(i)).appendTo(container);
						if (page == i) numbers.addClass("active").css("cursor", "default");
						methods.btnResponsive(page, pages, i, numbers);
						numbers.on("click", { page: i }, clickHandler);
					}
				} else {
					for (var i = (page - half); i <= (page + half); i++) {
						numbers = $('<li>').css("cursor", "pointer").prepend($('<span>', { "data-page": i }).html(i)).appendTo(container);
						if (page == i) numbers.addClass("active").css("cursor", "default");
						methods.btnResponsive(page, pages, i, numbers);
						numbers.on("click", { page: i }, clickHandler);
					}
				}
			}

			next = $('<li>').prepend($('<span>').html($('<i>', { "class": "fa fa-angle-right" }))).appendTo(container);
			last = $('<li>').prepend($('<span>').html($('<i>', { "class": "fa fa-angle-double-right" }))).appendTo(container);
			if (page < pages) {
				next.addClass("next").css("cursor", "pointer").children().attr("data-page", (page + 1)).bind("click", { "page": (page + 1) }, clickHandler);
				last.addClass("last").css("cursor", "pointer").children().attr("data-page", pages).bind("click", { "page": pages }, clickHandler);
			} else {
				next.addClass("next disabled");
				last.addClass("last disabled");
			}
			return (wrapper);
		},
	};

	$.fn.ajaxGrid = function (methodOrOptions) {
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
		footerPage: true,
		infoPageCenter: false,
	};
})(jQuery);
