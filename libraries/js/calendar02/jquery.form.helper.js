/**
 * Form input type number HTML 5
 * Referensi : jQuery number plug-in 2.1.5
 * Achmad Sarwat Untuk Sinori
**/
if(!jQuery) throw new Error("Bootstrap Form Helpers requires jQuery");
var ASBTimePickerDelimiter = ":",
    ASBTimePickerMeridian = {
        am: "AM",
        pm: "PM"
    },
	_keydown = {
		codes : {
			46 : 127,
			188 : 44,
			109 : 45,
			190 : 46,
			191 : 47,
			192 : 96,
			220 : 92,
			222 : 39,
			221 : 93,
			219 : 91,
			173 : 45,
			187 : 61, //IE Key codes
			186 : 59, //IE Key codes
			189 : 45, //IE Key codes
			110 : 46  //IE Key codes
		},
		shifts : {
			96 : "~",
			49 : "!",
			50 : "@",
			51 : "#",
			52 : "$",
			53 : "%",
			54 : "^",
			55 : "&",
			56 : "*",
			57 : "(",
			48 : ")",
			45 : "_",
			61 : "+",
			91 : "{",
			93 : "}",
			92 : "|",
			59 : ":",
			39 : "\"",
			44 : "<",
			46 : ">",
			47 : "?"
		}
}; + function($) {
    "use strict";
    var ASNumberSelect = function(element, options) {
        this.options = $.extend({}, $.fn.asbnomor.defaults, options), this.$element = $(element), this.initInput()
    };
    ASNumberSelect.prototype = {
        constructor: ASNumberSelect,
        initInput: function() {
            this.options.buttons === true && (this.$element.wrap('<div class="input-group asb-nomor-wrap"></div>'), 
			this.$element.parent().append('<span class="input-group-addon asb-nomor-addon"><span class="asb-nomor-btn inc"><span class="glyphicon glyphicon-chevron-up"></span></span><span class="asb-nomor-btn dec"><span class="glyphicon glyphicon-chevron-down"></span></span></span>')), 
			this.$element.on("change.asbnomor.data-api", ASNumberSelect.prototype.change), 
			this.options.keyboard === true && this.$element.on("keydown.asbnomor.data-api", ASNumberSelect.prototype.keydown), 
			this.options.buttons === true && this.$element.parent().on("mousedown.asbnomor.data-api", ".inc", ASNumberSelect.prototype.btninc).on("mousedown.asbnomor.data-api", ".dec", ASNumberSelect.prototype.btndec), 
			this.formatNumber()
        },
        keydown: function(evt) {
            var $datanya, code = (evt.keyCode ? evt.keyCode : evt.which), chara = '';
            if ($datanya = $(this).data("asbnomor"), $datanya.$element.is(".disabled") || $datanya.$element.attr("disabled") !== void 0) return true;
			if(_keydown.codes.hasOwnProperty(code)){
				code = _keydown.codes[code];
			}
			if (!evt.shiftKey && (code >= 65 && code <= 90)){
				code += 32;
			} else if (!evt.shiftKey && (code >= 69 && code <= 105)){
				code -= 48;
			} else if (evt.shiftKey && _keydown.shifts.hasOwnProperty(code)){
				chara = _keydown.shifts[code];
			}
			chara = (chara == '')?String.fromCharCode(code):chara;

			if(code != 8 && code != 45 && code != 127 && !chara.match(/[0-9]/)){
				var key = (evt.keyCode ? evt.keyCode : evt.which);
				if(key == 46 || key == 8 || key == 127 || key == 9 || key == 27 || key == 13 ||
				((key == 65 || key == 82 || key == 80 || key == 83 || key == 70 || key == 72 || key == 66 || key == 74 || key == 84 || key == 90|| key == 61 || key == 173 || 
				  key == 48) && (evt.ctrlKey || evt.metaKey) === true) ||
				((key == 86 || key == 67 || key == 88) && (evt.ctrlKey || evt.metaKey ) === true) ||
				((key >= 35 && key <= 40)) ||
				((key >= 112 && key <= 123))
				){
					switch(key){
						case 38:
							$datanya.increment();
							break;
						case 40:
							$datanya.decrement();
							break;
					}
					return;
				}
				evt.preventDefault();
				return true
			}            
            return true
        },
        mouseup: function(element) {
            var $selector, timeoutnya, intervalnya;
			$selector = element.data.btn, setTimeout(function() {$selector.$element.siblings().children().removeClass("klik")}, 80), 
			timeoutnya = $selector.$element.data("timer"), clearTimeout(timeoutnya), intervalnya = $selector.$element.data("interval"), clearInterval(intervalnya)
        },
        btninc: function() {
			var $datanya, $handler;
			return $(this).addClass('klik'), $datanya = $(this).parent().parent().find(".asb-nomor").data("asbnomor"), $datanya.$element.is(".disabled") || $datanya.$element.attr("disabled") !== void 0 ? true : ($datanya.increment(), $handler = setTimeout(function() {
                var tempVar;
                tempVar = setInterval(function() {
                    $datanya.increment()
                }, 80), $datanya.$element.data("interval", tempVar)
            }, 750), $datanya.$element.data("timer", $handler), $(document).one("mouseup", {
                btn: $datanya
            }, ASNumberSelect.prototype.mouseup), true)
		},
        btndec: function() {
            var $datanya, $handler;
            return $(this).addClass('klik'), $datanya = $(this).parent().parent().find(".asb-nomor").data("asbnomor"), $datanya.$element.is(".disabled") || $datanya.$element.attr("disabled") !== void 0 ? true : ($datanya.decrement(), $handler = setTimeout(function() {
                var tempVar;
                tempVar = setInterval(function() {
                    $datanya.decrement()
                }, 80), $datanya.$element.data("interval", tempVar) 
            }, 750), $datanya.$element.data("timer", $handler), $(document).one("mouseup", {
                btn: $datanya
            }, ASNumberSelect.prototype.mouseup), true)
        },
        change: function() {
            var datanya = $(this).data("asbnomor");
            return datanya.$element.is(".disabled") || datanya.$element.attr("disabled") !== void 0 ? true : (datanya.formatNumber(), true)
        },
        increment: function() {
            var nilai;
            nilai = this.getValue(), nilai += 1, this.$element.val(nilai).change(); 
        },
        decrement: function() {
            var nilai;
            nilai = this.getValue(), nilai -= 1, this.$element.val(nilai).change()
        },
        getValue: function() {
            var nilai = this.$element.val();
			return nilai !== "-1" && (nilai = String(nilai).replace(/\D/g, "")), String(nilai).length === 0 && (nilai = this.options.min), parseInt(nilai)
        },
        formatNumber: function() {
			var nilai, MaxNilaiLength, nilaiLength, limitLength;
            if (nilai = this.getValue(), nilai > this.options.max && (nilai = this.options.wrap === true ? this.options.min : this.options.max), nilai < this.options.min && (nilai = this.options.wrap === true ? this.options.max : this.options.min), this.options.zeros === true) 
				for (MaxNilaiLength = String(this.options.max).length, nilaiLength = String(nilai).length, limitLength = nilaiLength; MaxNilaiLength > limitLength; limitLength += 1) nilai = "0" + nilai;
            nilai !== this.$element.val() && this.$element.val(nilai)
        }
    };
    var old = $.fn.asbnomor;
	$.fn.asbnomor = function(evt) {
		return this.each(function() {
			var $selector, $datanya, isAttach;
			$selector = $(this), $datanya = $selector.data("asbnomor"), isAttach = typeof evt == "object" && evt, 
			$datanya || $selector.data("asbnomor", $datanya = new ASNumberSelect(this, isAttach)), 
			typeof evt === "string" && $datanya[evt].call($selector)
		})
	}, $.fn.asbnomor.Constructor = ASNumberSelect, $.fn.asbnomor.defaults = {
		min: 0,
		max: 9999,
		zeros: false,
		keyboard: true,
		buttons: true,
		wrap: false
	}, $.fn.asbnomor.noConflict = function() {
		$.fn.asbnomor = old;
		return this
	}, $(document).ready(function() {
		$("form input[type='text'].asb-nomor, form input[type='number'].asb-nomor").each(function(){
			var $element = $(this);
			$element.asbnomor($element.data());
		})
	})
}(jQuery), + function($) {
    "use strict";
    function getTimeValue(jamnya, menit, detik) {
		jamnya = String(jamnya), jamnya.length === 1 && (jamnya = "0"+jamnya), 
		menit = String(menit), menit.length === 1 && (menit = "0"+menit),
		detik = String(detik), detik.length !== 0 && (detik.length === 1 ? (detik = ASBTimePickerDelimiter+"0"+detik) : (detik = ASBTimePickerDelimiter+detik));
		return jamnya+ASBTimePickerDelimiter+menit+detik;
    }
    function isDialogOpen() {
        var $selector;
        $("[data-toggle=asb-timepicker]").each(function(evt){
			$selector = getSelector($(this));
			if($selector.hasClass("open")){
				$selector.trigger(evt = $.Event("hide.asbtimepicker"));
				if(!evt.isDefaultPrevented()){
					$selector.removeClass("open").trigger("hidden.asbtimepicker"); 
					return void 0;
				} else return true;
			} else return true;
        })
    }
    function getSelector($selector){
		return $selector.closest(".asb-timepicker")
	}
    var ASTimePickerBootstrap = function(element, options){
            this.options = $.extend({}, $.fn.asbtimepicker.defaults, options), this.$element = $(element), this.initPopover()
	};
    ASTimePickerBootstrap.prototype = {
        constructor: ASTimePickerBootstrap,
        setTime: function() {
            var defaultValue, objTanggal, arrValue, jamnya, menit, extDetik, detik, extMode, modenya;
            defaultValue = this.options.value, 
			extMode = "", 
			modenya = "",
			detik = "",
			extDetik = "", 
			defaultValue === "now" || defaultValue === void 0 ? 
			(
				objTanggal = new Date, 
				jamnya = objTanggal.getHours(), 
				menit = objTanggal.getMinutes(), 
				this.options.showseconds === true && (detik = objTanggal.getSeconds(), extDetik = ASBTimePickerDelimiter+detik), 
				this.options.mode === "12h" && (jamnya > 12 ? (jamnya -= 12, extMode = " "+ASBTimePickerMeridian.pm, modenya = "pm") : (extMode = " "+ASBTimePickerMeridian.am, modenya = "am")), 
				defaultValue === "now"  && this.$element.find('.asb-timepicker-toggle > input[type="text"]').val(getTimeValue(jamnya, menit, detik) + extMode), 
				this.$element.data("hour", jamnya), 
				this.$element.data("minute", menit), 
				this.$element.data("second", detik), 
				this.$element.data("mode", modenya)
			) : 
			(
				arrValue = String(defaultValue).split(ASBTimePickerDelimiter), 
				jamnya = arrValue[0], 
				menit = arrValue[1], 
				detik = arrValue[2], 
				this.options.mode === "12h" && (this.options.showseconds === false ? (arrValue = String(menit).split(" "), menit = arrValue[0], modenya = arrValue[1] === ASBTimePickerMeridian.pm ? "pm" : "am") : (arrValue = String(detik).split(" "), detik = arrValue[0], modenya = arrValue[1] === ASBTimePickerMeridian.pm ? "pm" : "am")), 
				this.$element.find('.asb-timepicker-toggle > input[type="text"]').val(defaultValue), 
				this.$element.data("hour", jamnya), 
				this.$element.data("minute", menit), 
				this.$element.data("second", detik), 
				this.$element.data("mode", modenya)
			)
        },
        initPopover: function() {
            var addonKiri, addonKanan, addonGroup, detikKolom, meredianKolom, limitHour;
            addonKiri = "", 
			addonKanan = "", 
			addonGroup = "", 
			detikKolom = "", 
			meredianKolom = "", 
			limitHour = "23",
			this.options.showseconds === true && (detikKolom = '<td class="separator">'+ASBTimePickerDelimiter+'</td><td class="second"><input type="text" class="'+this.options.classname+' input-sm asb-nomor"  data-min="0" data-max="59" data-zeros="true" data-wrap="true" maxlength="2" /></td>'), 
			this.options.icon !== "" && (this.options.align === "right" ? addonKanan = '<span class="input-group-addon"><i class="'+this.options.icon+'"></i></span>' : addonKiri = '<span class="input-group-addon"><i class="'+this.options.icon+'"></i></span>', addonGroup = "input-group"), 
			//this.options.mode === "12h" && (meredianKolom = '<td class="meridian"><select class="asb-meredian-timepicker" name="meredian-'+this.options.name+'"><option value="am">AM</option><option value="pm">PM</option></select></td>', limitHour = "11"), 
			this.options.mode === "12h" && (meredianKolom = '<td class="meridian"><div class="input-group asb-meredian-wrap"><input type="text" maxlength="2" class="form-control input-sm asb-meredian-timepicker" readonly /><span class="input-group-addon asb-meredian-addon"><span class="asb-meredian-btn up"><span class="glyphicon glyphicon-chevron-up"></span></span><span class="asb-meredian-btn down"><span class="glyphicon glyphicon-chevron-down"></span></span></span></div>', limitHour = "11"), 
			this.$element.html('<div class="'+addonGroup+' asb-timepicker-toggle" data-toggle="asb-timepicker">'+addonKiri+'<input type="text" name="'+this.options.name+'" id="'+this.options.id+'" class="'+this.options.classname+'" '+this.options.attributes+' readonly />'+addonKanan+'</div><div class="asb-timepicker-dialog"><table class="table"><tbody><tr><td class="hour"><input type="text" class="'+this.options.classname+' input-sm asb-nomor" data-min="0" data-max="'+limitHour+'" data-zeros="true" data-wrap="true" maxlength="2" /></td><td class="separator">'+ASBTimePickerDelimiter+'</td><td class="minute"><input type="text" class="'+this.options.classname+' input-sm asb-nomor" data-min="0" data-max="59" data-zeros="true" data-wrap="true" maxlength="2" /></td>'+detikKolom+meredianKolom+'</tr></tbody></table></div>'), 
			this.$element.on("click.asbtimepicker.data-api touchstart.asbtimepicker.data-api", "[data-toggle=asb-timepicker]", ASTimePickerBootstrap.prototype.toggle).on("click.asbtimepicker.data-api touchstart.asbtimepicker.data-api", ".asb-timepicker-dialog > table", function(evt) {
                evt.stopPropagation();
            }),
			this.$element.find(".asb-nomor").each(function() {
                var $selector;
                $selector = $(this), $selector.asbnomor($selector.data()), $selector.on("change", ASTimePickerBootstrap.prototype.change)
            }),
			this.$element.find(".asb-meredian-timepicker").each(function() {
                var $selector;
                //$selector = $(this), $selector.on("change", ASTimePickerBootstrap.prototype.change)
                $selector = $(this), 
				$selector.siblings().on("mousedown.asbnomor.data-api", ".asb-meredian-btn", function(){
					var btnKlik = $(this);
					btnKlik.addClass('klik'), $selector.val(($selector.val() === "PM" ? "AM" : "PM")), $selector.trigger("change"), 
					setTimeout(function() {btnKlik.removeClass('klik')}, 80)
				}),
				$selector.on("change", ASTimePickerBootstrap.prototype.change)
            }),
			this.setTime(), 
			this.updatePopover()
        },
        updatePopover: function(){
            var jamnya, menit, detik, modenya;
            jamnya = this.$element.data("hour"), 
			menit = this.$element.data("minute"), 
			detik = this.$element.data("second"), 
			modenya = this.$element.data("mode"), 
			this.$element.find(".hour input[type=text]").val(jamnya).change(), 
			this.$element.find(".minute input[type=text]").val(menit).change(), 
			this.$element.find(".second input[type=text]").val(detik).change(), 
			this.$element.find(".asb-meredian-timepicker").val(modenya.toUpperCase())
        },
        change: function(){
            var $selector, $element, $datanya, $detiknya, $modenya;
			$selector = $(this), 
			$element = getSelector($selector), 
			$datanya = $element.data("asbtimepicker"), 
			$datanya && $datanya !== "undefined" && (
				$modenya = "", $detiknya = "",
				$datanya.options.showseconds === true && ($detiknya = ASBTimePickerDelimiter+$element.find(".second input[type=text]").val()), 
				$datanya.options.mode === "12h" && ($modenya = " "+ASBTimePickerMeridian[$element.find(".asb-meredian-timepicker").val().toLowerCase()]), 
				$element.find('.asb-timepicker-toggle > input[type="text"]').val($element.find(".hour input[type=text]").val() + ASBTimePickerDelimiter + $element.find(".minute input[type=text]").val() + $detiknya + $modenya), 
				$element.trigger("change.asbtimepicker")
			);
			return false;
        },
        toggle: function(evt) {
            var $selector, $element, isOpen;
            if ($selector = $(this), $element = getSelector($selector), $element.is(".disabled") || $element.attr("disabled") !== void 0) return true;
            if (isOpen = $element.hasClass("open"), isDialogOpen(), !isOpen) {
                if ($element.trigger(evt = $.Event("show.asbtimepicker")), evt.isDefaultPrevented()) return true;
                $element.toggleClass("open").trigger("shown.asbtimepicker"), $selector.focus()
            }
            return false
        }
    };
    
	var old = $.fn.asbtimepicker;
	$.fn.asbtimepicker = function(evt) {
		return this.each(function(){
			var $selector, $datanya, isAttach;
			$selector = $(this), $datanya = $selector.data("asbtimepicker"), isAttach = typeof evt == "object" && evt, this.type = "asbtimepicker", 
			$datanya || $selector.data("asbtimepicker", $datanya = new ASTimePickerBootstrap(this, isAttach)), 
			typeof evt === "string" && $datanya[evt].call($selector)
		})
	}, $.fn.asbtimepicker.Constructor = ASTimePickerBootstrap, $.fn.asbtimepicker.defaults = {
		icon: "fa fa-clock",
		align: "left",
		name: "",
		id: "",
		value: "",
		classname: "form-control",
		attributes: "",
		mode: "24h",
		showseconds: false
    }, $.fn.asbtimepicker.noConflict = function() {
		$.fn.asbtimepicker = old;
		return this
    };
    var hooknya;
    $.asbtimepicker && (hooknya = $.asbtimepicker), $.asbtimepicker = {
        get: function(element) {
			return $(element).hasClass("asb-timepicker") ? $(element).find('.asb-timepicker-toggle > input[type="text"]').val() : hooknya ? hooknya.get(element) : void 0
        },
        set: function(element, opt) {
            var $datanya;
            if ($(element).hasClass("asb-timepicker")) $datanya = $(element).data("asbtimepicker"), $datanya.options.value = opt, $datanya.setTime(), $datanya.updatePopover();
            else if (hooknya) return hooknya.set(element, opt)
        }
    }, $(document).ready(function() {
        $("div.asb-timepicker").each(function() {
            var $element;
            $element = $(this), $element.asbtimepicker($element.data())
        })
    }), $(document).on("click.asbtimepicker.data-api", isDialogOpen)
}(jQuery);