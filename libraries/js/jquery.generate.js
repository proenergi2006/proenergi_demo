/*!
 * Author: Abdullah A Almsaeed
 * Date: 4 Jan 2014
 * Description:
 *      This file should be included in all pages
 !**/

/*
 * Modified By Achmad Sarwat 29/11/2014
 */
var $base_url = base_url; // ALVIN
$.fn.modal.Constructor.prototype.enforceFocus = function() {};
	

var config = {
	validation : {
		onkeyup: false,
		onclick: false,
		onfocusout: false,
		errorPlacement: function(error, element){
			//console.log((element.prop("type") === 'checkbox' && element.parent(".iradio_square-blue").length > 0));
			//console.log((element.prop("type") === 'checkbox' && element.parent(".icheckbox_square-blue").length > 0));
			if(element.parent().hasClass('input-group'))
				error.insertAfter(element.parent());
			else if(element.siblings().hasClass('select2-container'))
				error.insertAfter(element.siblings());
			else if(element.prop("type") === 'file' && element.parents(".file-input").length > 0)
				error.insertAfter(element.parents(".file-input"));
			else if(element.prop("type") === 'file' && element.parents(".simple-fileupload").length > 0)
				error.insertAfter(element.parents(".simple-fileupload"));
			else if(element.prop("type") === 'radio' && element.parent(".iradio_square-blue").length > 0)
				error.insertAfter(element.parents(".radio").first().parent());
			else if(element.prop("type") === 'checkbox' && element.parent(".icheckbox_square-blue").length > 0)
				error.insertAfter(element.parents(".radio").first().parent());
			else
				error.insertAfter(element);
		},
		highlight: function (element, errorClass, validClass){
			$(element).parents(".form-group").first().addClass("has-error");
		},
		unhighlight: function (element, errorClass, validClass){
			$(element).parents(".form-group").first().removeClass("has-error");
		},
		submitHandler: function(form){
			$("body").addClass("loading");
			form.submit();
		}, 
	},
	icheck : {},
	select2 : {}, 
	datepicker : {}, 
	datatable : {},
	fileupload : {},
};

$(document).ready(function(){
    "use strict";

    /* Sidebar tree view */
    $(".sidebar .treeview").tree();

	//Activate tooltips
    $("[data-toggle='tooltip']").tooltip();

	//Enable sidebar toggle
    $("[data-toggle='offcanvas']").click(function(e) {
        e.preventDefault();

        //If window is small enough, enable sidebar push menu
        if ($(window).width() <= 767) {
            $('.row-offcanvas').toggleClass('active');
            $('.left-side').removeClass("collapse-left");
            $(".right-side").removeClass("strech");
            $('.row-offcanvas').toggleClass("relative");
        } else {
            //Else, enable content streching
            $('.left-side').toggleClass("collapse-left");
            $(".right-side").toggleClass("strech");
        }
    });

    //Add hover support for touch devices
    $('.btn').bind('touchstart', function() {
        $(this).addClass('hover');
    }).bind('touchend', function() {
        $(this).removeClass('hover');
    });


    /*     
     * Add collapse and remove events to boxes
     */
    $("[data-widget='collapse']").click(function() {
        //Find the box parent        
        var box = $(this).parents(".box").first();
        //Find the body and the footer
        var bf = box.find(".box-body, .box-footer");
        if (!box.hasClass("collapsed-box")) {
            box.addClass("collapsed-box");
            //Convert minus into plus
            $(this).children(".fa-minus").removeClass("fa-minus").addClass("fa-plus");
            bf.slideUp(500);
        } else {
            box.removeClass("collapsed-box");
            //Convert plus into minus
            $(this).children(".fa-plus").removeClass("fa-plus").addClass("fa-minus");
            bf.slideDown(500);
        }
    });

	/*
	 * INITIALIZE BUTTON TOGGLE
	 * ------------------------
	 */
	$('.btn-group[data-toggle="btn-toggle"]').each(function() {
		var group = $(this);
		$(this).find(".btn").click(function(e) {
			group.find(".btn.active").removeClass("active");
			$(this).addClass("active");
			e.preventDefault();
		});
	
	});

    $("[data-widget='remove']").click(function() {
        //Find the box parent        
        var box = $(this).parents(".box").first();
        box.slideUp();
    });

    $("[data-alert='remove']").click(function() {
        //Find the alert parent        
        var Alert = $(this).parents(".alert").first();
        Alert.slideUp();
    });

    // if (role_id>1) {
	    $(".table").on("dblclick", ".clickable-row", function(){
	        window.document.location = $(this).data("href");
	    });
	// }

	/*
     * We are gonna initialize all checkbox and radio inputs to 
     * iCheck plugin in.
     * You can find the documentation at http://fronteed.com/iCheck/
     */
    $("input[type='checkbox']:not(.simple), input[type='radio']:not(.simple)").iCheck({
        checkboxClass: 'icheckbox_square-blue',
        radioClass: 'iradio_square-blue'
    });

	$(".select2").select2({
		placeholder: "Pilih salah satu",
		allowClear: true
	});
	
	$(document).on("select2:open", function(e){
		window.setTimeout(function () {
			if($(document.querySelector('.select2-container--open .select2-search__field')).is(":focus") === false){
				document.querySelector('.select2-container--open .select2-search__field').focus();
			}
		}, 0);		
	});

	$(document).on("change", ".form-inputfile", function(e){
		var $input = $(this), $label = $input.next(".label-inputfile"), labelVal = $label.html(), fileName = e.target.value.split( '\\' ).pop();
		if(fileName){
			let resetnya = $label.find("input").parent();
			$label.find(".input-file-reset").removeClass("input-file-reset-hide");				
			$label.find("input").removeClass("form-control-hide").val(fileName);
			resetnya.append('<span class="form-inputfile-reset">&times;</span>');
		}
	}).on("click", ".form-inputfile-reset", function(e){
        e.preventDefault();
		let inputan = $(this);
		let wrapper = inputan.parents(".simple-fileupload").first();
		let objmain = wrapper.children(".form-inputfile");
		wrapper.children(".label-inputfile").find("input").val(""); objmain.val(""); inputan.remove();
	});

	$('#telepon, #telp_customer, #fax_customer, #telp_sup, #fax_sup, #telp, .telepon, #telp_up, #fax_up, #postalcode_customer').mask('000000000000');
	
	$("a.delete").click(function(){ return confirm("Apakah anda yakin ?"); });

	function terbilang(x){
		var satuan = new Array("", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas");
		if(x < 12)
			return satuan[x];
		else if(x < 20)
			return terbilang(parseInt(x - 10))+" Belas";
		else if(x < 100)
			return terbilang(parseInt(x / 10))+" Puluh "+terbilang(parseInt(x % 10));
		else if(x < 200)
			return " Seratus "+terbilang(parseInt(x - 100));
		else if(x < 1000)
			return terbilang(parseInt(x/100))+" Ratus "+terbilang(parseInt(x % 100));
		else if(x < 2000)
			return " Seribu "+terbilang(parseInt(x - 1000));
		else if(x < 1000000)
			return terbilang(parseInt(x/1000))+" Ribu "+terbilang(parseInt(x % 1000));
		else if(x < 1000000000)
			return terbilang(parseInt(x/1000000))+" Juta "+terbilang(parseInt(x % 1000000));
	}

});

$(window).on("load resize", function() {
	var topOffset 	= $("header.main-header").height();
	var botOffset	= $("section.main-footer").outerHeight();
	var ttlOffset 	= ($("section.content-header").outerHeight());
	var width 		= ($(window).innerWidth() > 0) ? $(window).innerWidth() : $(screen).width();
	var height 		= ($(window).innerHeight() > 0) ? $(window).innerHeight() : $(screen).height();
	var heightWrap	= (height - botOffset - ttlOffset);
	height 			= (height - topOffset);
	if (height < 1) 
		height = 1;
	if (height > topOffset){
		var jmenu = parseInt(height);
		var offsetContent 	= ($("section.main-footer").outerHeight());
		var titleContent 	= ($("section.content-header").outerHeight());
		$("aside.right-side").css("min-height", (parseInt(heightWrap))+"px");
		$("section.content").css("min-height", (jmenu - offsetContent - titleContent)+"px");
	}
	var offsetSidebar = $(".sidebar .user-panel").height();
	$(".sidebar-menu").slimscroll({
		alwaysVisible : false,
		height: jmenu+"px",
		color: "rgba(0,0,0,1)",
		size: "15px",
		borderRadius: "0px",			
	}).css({"width":"100%"});
	$(".sidebar-menu").css({"height":"auto", "max-height":(jmenu - topOffset - offsetSidebar + 30)});
	$(".sidebar .slimScrollDiv").css({"height":"auto", "max-height":(jmenu - topOffset - offsetSidebar + 30)});
	
	var offsetHeader = 350;
	var limitHeader  = (jmenu - 136);
	offsetHeader 	 = (offsetHeader < limitHeader)?offsetHeader:limitHeader;
	$(".dropdown-menu .menu").slimscroll({
		height: offsetHeader+"px",
		alwaysVisible: false,
		size: "3px"
	}).css("width", "100%");
	$(".dropdown-menu .menu").css({"height":"auto", "max-height":offsetHeader});
	$(".dropdown-menu .slimScrollDiv").css({"height":"auto", "max-height":offsetHeader});
});


function rupiah(args){
	if(!isNaN(args)){
		var num 	= args.toString();
		var negasi	= "";
		negasi		= (num.substr(0,1)== '-')?'-':'';
		num			= (num.substr(0,1)== '-')?num.substr(1):num;
		var rupiah  = "";
		var rp 		= num.length;
		while (rp > 3){
			var s  	= num.length - 3;
			rupiah 	= "."+ num.substr(s)+ rupiah;
			num  	= num.substr(0,s);
			rp 	   	= num.length;
		}
		rupiah = num + rupiah;
		return negasi+rupiah;
	}
}
function ucwords(str) {
  return (str + '').replace(/^([a-z\u00E0-\u00FC])|\s+([a-z\u00E0-\u00FC])/g, function($1) {
	  return $1.toUpperCase();
	});
}

function setErrorFocus(elemnya, formnya, isFocus){
	var scrolnya = formnya.scrollTop() + (elemnya.offset().top - formnya.position().top) + (elemnya.height()/2);
	$("html, body").animate({ scrollTop: scrolnya - ($("header.main-header").height() + $("section.content-header").height())}, 500);
	if(isFocus){
		elemnya.focus();
	}
}

