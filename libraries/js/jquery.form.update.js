$(function(){
	var formValidasiCfg = {
		submitHandler: function(form) {
			$("#loading_modal").modal({keyboard:false, backdrop:'static'});

			var sert = $("#sert_file"), npwp = $("#npwp_file"), siup = $("#siup_file"), tdpn = $("#tdp_file"), dokumen_lainnya = $("#dokumen_lainnya_file");

			var param1 = $("#CaptchaCode").val();
			var param2 = $("#BDC_UserSpecifiedCaptchaId").val();
			var param3 = $("#BDC_VCID_ExampleCaptcha").val();
			var param4 = $("#BDC_BackWorkaround_ExampleCaptcha").val();
			var param5 = $("#BDC_Hs_ExampleCaptcha").val();
			var param6 = $("#BDC_SP_ExampleCaptcha").val();
		
			var cekflag1 = cek_uploadnya(sert.val(), npwp.val(), siup.val(), tdpn.val(), dokumen_lainnya.val());
			var cekflag2 = cek_captcha(param1, param2, param3, param4, param5, param6);

			if($("#cekkolnup").is(":checked") && $("#nup_fee").val() == ""){
				$("#loading_modal").modal("hide");
				$.validator.showErrorField('nup_fee', "Kolom ini belum diisi atau dipilih");
				setErrorFocus($("#nup_fee"), $("form#gform"), false);
			} else if(cekflag1.error != ""){
				$("#loading_modal").modal("hide");
				$("#CaptchaDIV").empty();
				$("#CaptchaDIV").html(cekflag1.captcha);
				$("#CaptchaCode").val("");
				swal.fire({
					allowOutsideClick: false, icon: "warning", width: '350px',
					html:'<p style="font-size:14px; font-family:arial;">'+cekflag1.error+'</p>'
				});
			} else if(cekflag2 != '1'){
				$("#loading_modal").modal("hide");
				$("#CaptchaDIV").empty();
				$("#CaptchaDIV").html(cekflag2);
				$("#CaptchaCode").val("");
				swal.fire({
					allowOutsideClick: false, icon: "warning", width: '350px',
					html:'<p style="font-size:14px; font-family:arial;">Code Captcha Salah</p>'
				});
			} else{
				form.submit();
			}
		}	
	};
	$("form#gform").validate($.extend(true,{},config.validation,formValidasiCfg));


	function cek_uploadnya(param1, param2, param3, param4, param5){
		var hasil = {error : "", captcha : ""};
		$.ajax({
			url			: $base_url+"/customer/check-upload-dokumen.php",
			type		: "post",
			data		: {sert: param1, npwp: param2, siup: param3, tdpn: param4, dokumen_lainnya: param5},
			dataType	: "json",
			cache 		: false, 
			async 		: false,
			success: function(response){
				hasil = {error : response.error, captcha : response.captcha};
			}
		});
		return hasil;
	}

	function cek_captcha(param1, param2, param3, param4, param5, param6){
		var hasil 		= null;
		var _captcha 	= param1;

		if(_captcha != ""){
			$.ajax({
				type: 'POST',
				url: '../web/action/cek_captcha.php',
				async: false,
				data: {
					'BDC_UserSpecifiedCaptchaId' : param2,
					'BDC_VCID_ExampleCaptcha': param3,
					'BDC_BackWorkaround_ExampleCaptcha' : param4,
					'BDC_Hs_ExampleCaptcha' : param5,
					'BDC_SP_ExampleCaptcha' : param6,
					'CaptchaCode': _captcha
				},
				success: function(response){
					hasil = response;
				},
			});
		}
		return hasil;
	}

	$("select#prov_customer").change(function(){
		$("select#kab_customer").val("").trigger('change').select2('close');
		$("select#kab_customer option").remove();
		$.ajax({
			type	: "POST",
			url		: "./__get_kabupaten.php",
			dataType: 'json',
			data	: { q1 : $("select#prov_customer").val() },
			cache	: false,
			success : function(data){ 
				if(data.items != ""){
					$("select#kab_customer").select2({ 
						data 		: data.items, 
						placeholder : "Pilih salah satu", 
						allowClear 	: true, 
					});
					return false;
				}
			}
		});
	});

	$("select#prov_billing").change(function(){
		$("select#kab_billing").val("").trigger('change').select2('close');
		$("select#kab_billing option").remove();
		$.ajax({
			type	: "POST",
			url		: "./__get_kabupaten.php",
			dataType: 'json',
			data	: { q1 : $("select#prov_billing").val() },
			cache	: false,
			success : function(data){ 
				if(data.items != ""){
					$("select#kab_billing").select2({ 
						data 		: data.items, 
						placeholder : "Pilih salah satu", 
						allowClear 	: true, 
					});
					return false;
				}
			}
		});
	});

	$(".registration-form").on("change", "select#jenis_payment", function(){
		var nilai = $(this).val();
		if(nilai != "CREDIT"){
			$("#jwp").addClass("hide");
			$("#jwp2").addClass("hide");
			$("#top_payment").val("");
		} else{
			$("#jwp").removeClass("hide");
			$("#jwp2").removeClass("hide");
		}
	});

	$(".registration-form").on("ifChecked", "input[name='tipe_bisnis']", function(){
		var nilai = $(this).val();
		if(nilai == 10){
			$("#tipe_bisnis_lain").removeAttr("disabled");
		} else{
			$("#tipe_bisnis_lain").attr("disabled", "disabled").val("");
		}
	});

	$(".registration-form").on("ifChecked", "input[name='ownership']", function(){
		var nilai = $(this).val();
		if(nilai == 8){
			$("#ownership_lain").removeAttr("disabled");
		} else{
			$("#ownership_lain").attr("disabled", "disabled").val("");
		}
	});

	$(".registration-form").on("ifChecked", "input[name='payment_schedule']", function(){
		var nilai = $(this).val();
		if(nilai == 2){
			$("#payment_schedule_other").removeAttr("disabled");
		} else{
			$("#payment_schedule_other").attr("disabled", "disabled").val("");
		}
	});

	$(".registration-form").on("ifChecked", "input[name='payment_method']", function(){
		var nilai = $(this).val();
		if(nilai == 5){
			$("#payment_method_other").removeAttr("disabled");
		} else{
			$("#payment_method_other").attr("disabled", "disabled").val("");
		}
	});

	$(".registration-form").on("ifChecked", "input[name='logistik_env']", function(){
		var nilai = $(this).val();
		if(nilai == 3){
			$("#logistik_env_other").removeAttr("disabled");
		} else{
			$("#logistik_env_other").attr("disabled", "disabled").val("");
		}
	});

	$(".registration-form").on("ifChecked", "input[name='logistik_storage']", function(){
		var nilai = $(this).val();
		if(nilai == 3){
			$("#logistik_storage_other").removeAttr("disabled");
		} else{
			$("#logistik_storage_other").attr("disabled", "disabled").val("");
		}
	});

	$(".registration-form").on("ifChecked", "input[name='logistik_hour']", function(){
		var nilai = $(this).val();
		if(nilai == 3){
			$("#logistik_hour_other").removeAttr("disabled");
		} else{
			$("#logistik_hour_other").attr("disabled", "disabled").val("");
		}
	});

	$(".registration-form").on("ifChecked", "input[name='logistik_volume']", function(){
		var nilai = $(this).val();
		if(nilai == 3){
			$("#logistik_volume_other").removeAttr("disabled");
		} else{
			$("#logistik_volume_other").attr("disabled", "disabled").val("");
		}
	});

	$(".registration-form").on("ifChecked", "input[name='logistik_quality']", function(){
		var nilai = $(this).val();
		if(nilai == 2){
			$("#logistik_quality_other").removeAttr("disabled");
		} else{
			$("#logistik_quality_other").attr("disabled", "disabled").val("");
		}
	});

	$(".registration-form").on("click", ".file-upload", function(){
		var thisBtn = $(this);
		var element = thisBtn.data("file");
		var multiple =thisBtn.data("multiple");
		var topWrap = thisBtn.parents(".input-group").first();
		var infoSts = topWrap.siblings(".info-status");
		var filenya = $("#"+element);
		var progSts = $("#checkModal").find(".modal-body > .progress-status");
		var progBox = $("#checkModal").find(".modal-body > .progress");
		var progBar = progBox.children();
		var wrap 	= $("#"+element+"_wrap");
		if(filenya.val() == ""){
			infoSts.removeClass("hide").html("* File masih kosong");
			return false;
		} else if(!window.File || !window.FileReader || !window.FileList || !window.Blob){
			infoSts.removeClass("hide").html("* Upgrade browser anda, karena browser anda sekarang belum mendukung fitur yang kita butuhkan!");
			return false;
		} else{
			var formdata = new FormData();
			formdata.append("image_file", filenya[0].files[0]);
			formdata.append("kategori", element);
			
			var beforeUpload = function(){
				progSts.addClass("hide").html("");
				progBox.addClass("hide");
				thisBtn.removeClass("disabled");
				$("#btnSubmit").removeClass("disabled");

				var fsize = filenya[0].files[0].size, ftype = filenya[0].files[0].type, fname = filenya[0].files[0].name;
				var ext = fname.substr(fname.lastIndexOf(".")).toLowerCase();
				if(ext != ".jpg" && ext != ".jpeg" && ext != ".png" && ext != ".pdf" && ext != ".rar" && ext != ".zip"){
					infoSts.removeClass("hide").html("* Extensi file tidak diperbolehkan...");
					return false
				} else if(element != 'sert_file' && fsize > (2 * 1024 * 1024)) {
					infoSts.removeClass("hide").html("* Ukuran file terlalu besar...");
					return false
				} else if(element == 'sert_file' && fsize > (10 * 1024 * 1024)) {
					infoSts.removeClass("hide").html("* Ukuran file terlalu besar...");
					return false
				} else{
					infoSts.addClass("hide").html("");
					$("#checkModal").find(".modal-header > #clsmdl").addClass("hide");
					$("#checkModal").modal({backdrop: 'static', keyboard: false});
					progSts.removeClass("hide").html("");
					progBox.removeClass("hide");
					thisBtn.addClass("disabled");
					$("#btnSubmit").addClass("disabled");
				}
			}
			
			$.ajax({
				url			: $base_url+"/customer/upload-dokumen.php",
				type		: "post",
				data		: formdata,
				dataType	: "json",
				cache 		: false,
				processData	: false,
				contentType	: false,
				beforeSend	: beforeUpload,
				xhr: function(){
					var xhr = $.ajaxSettings.xhr();
					xhr.upload.onprogress = function(progress){
						var percentage = Math.floor((progress.loaded / progress.total) * 100);
						progBar.css("width", percentage+"%").text(percentage+"%");
						progSts.html("Uploaded "+progress.loaded+" bytes of "+progress.total+" bytes");
					};
					return xhr;
				},
				success: function(status){
					if(status.error){
						wrap.children(".preview-file").first().remove();
						infoSts.removeClass("hide").html(status.error);
					} else{
						if(multiple == undefined) {
							wrap.children(".form-group").first().remove();
							wrap.append('<div class="preview-file">'+status.answer+'</div>');
						}else{
							filenya.val('');
							wrap.append('<br><div class="preview-file">'+status.answer+'</div>');
						}
						
					}
					progBox.addClass("hide");
					thisBtn.removeClass("disabled");
					$("#btnSubmit").removeClass("disabled");
					$("#checkModal").modal("hide");
					return false; 
				}
			});
		}
	});

	$(".registration-form").on("click", ".sert_file_del, .npwp_file_del, .siup_file_del, .tdp_file_del, .dokumen_lainnya_del, .dokumen_lainnya_file_del", function(){
		var thisBtn = $(this), wrap = thisBtn.parents(".file-wrap"), element = thisBtn.attr("class").replace("_del", "");
		// alert(thisBtn.attr("class"))
		// alert(thisBtn.data("source"))
		// return
		$("#checkModal").find(".modal-header > #clsmdl").removeClass("hide");
		$("#checkModal").find(".modal-body > .status-global").removeClass("hide");
		$("#checkModal").find(".modal-body > .progress-status").addClass("hide").html('');
		$("#checkModal").modal();
		$.ajax({
			url			: $base_url+"/customer/delete-upload-dokumen.php",
			type		: "post",
			data		: {file:thisBtn.attr("class"), source:thisBtn.data("source") ? thisBtn.data("source") : ''},
			dataType	: "json",
			cache 		: false,
			success: function(status){
				if(status.error){
					$("#checkModal").find(".modal-body > .status-global").addClass("hide");
					$("#checkModal").find(".modal-body > .progress-status").removeClass("hide").html('<p style="font-size:14px;">'+status.error+'</p>');
				} else{
					if(thisBtn.data("source")) {
						var del_ = thisBtn.parents(".preview-file");
						del_.remove();
					}else{
						var dom1 = $("<div>", {class: "form-group"});
						var dom2 = $("<label>", {class: "sr-only"}).appendTo(dom1);
						var dom3 = $("<div>", {class: "input-group"}).appendTo(dom1);
						var sub1 = $("<input>", {type: "file", name: element, id: element, class: "form-control-file"}).appendTo(dom3);
						var sub2 = $("<span>", {class: "input-group-btn"}).appendTo(dom3);
						var dom4 = $("<div>", {class: "info-status hide"}).appendTo(dom1);
						sub2.html('<button type="button" class="btn btn-info btn-flat file-upload" data-file="'+element+'"><i class="fa fa-upload jarak-kanan"></i> Upload</button>');
						wrap.html(dom1);
						$("#checkModal").find(".modal-body > .status-global").addClass("hide");
						$("#checkModal").modal("hide");
					}
				}
				return false; 
			}
		});
	});

});		
