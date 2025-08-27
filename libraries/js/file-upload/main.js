$(function(){
    'use strict';

	var optionsFileSingle = {
		url				: $base_url+'/web/__get_upload_lcr_single.php',
		dataType		: 'json',
		autoUpload		: false,
		acceptFileTypes	: /(\.|\/)(gif|jpe?g|png)$/i,
		maxFileSize		: 1048576, //1MB
        previewMaxWidth	: 80,
        previewMaxHeight: 100,
		uploadTemplateId	: null,
    	downloadTemplateId	: null,        
		disableImageResize	: /Android(?!.*Chrome)|Opera/.test(window.navigator.userAgent),
		uploadTemplate	: function(o){
			var rows = $();
			$.each(o.files, function(index, file){
				var row = $('<tr class="template-upload fade">'+
					'<td><div class="preview text-center"></div></td>'+
					'<td>'+
						'<p class="name">'+file.name+'</p><p class="size">Processing...</p><strong class="error text-danger"></strong>'+
						'<div class="progress" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">'+
							'<div class="progress-bar progress-bar-info" style="width:0%;"></div>'+
						'</div>'+
						(!index && !o.options.autoUpload ?'<button class="btn btn-primary btn-sm start" disabled>Start</button> ' :'')+
						(!index ? '<button class="btn btn-warning btn-sm cancel">Cancel</button>' : '')+
					'</td></tr>');
				rows = rows.add(row);
			});
			return rows;			
		},
		downloadTemplate: function(o){
			var rows = $();
			$.each(o.files, function(index, file){
				var row = $('<tr class="template-download fade">'+
					'<td><div class="preview text-center">'+
						(file.thumbnailUrl ?'<a href="'+file.url+'" title="'+file.title+'" data-gallery><img src="'+file.thumbnailUrl+'" /></a>' :'')+
					'</div></td>'+
					'<td>'+
						'<p class="name">'+file.name_original+'</p>'+
						'<p class="size">'+o.formatFileSize(file.size)+'</p>'+
						(file.error ?'<strong class="error text-danger">'+file.error+'</strong>' :'')+
						'<button class="btn btn-danger btn-sm delete" data-type="'+file.deleteType+'" data-url="'+file.deleteUrl+'">Hapus</button>'+
					'</td></tr>');
				rows = rows.add(row);
			});
			return rows;			
		},
	};
	var optionsFilenya = {
		url				: $base_url+'/web/__get_upload_lcr.php',
		dataType		: 'json',
		autoUpload		: false,
		acceptFileTypes	: /(\.|\/)(gif|jpe?g|png)$/i,
		maxFileSize		: 1048576, //1MB
        previewMaxWidth	: 80,
        previewMaxHeight: 100,
		uploadTemplateId	: null,
    	downloadTemplateId	: null,        
		disableImageResize	: /Android(?!.*Chrome)|Opera/.test(window.navigator.userAgent),
		uploadTemplate	: function(o){
			var rows = $();
			$.each(o.files, function(index, file){
				var row = $('<tr class="template-upload fade">'+
					'<td><div class="preview text-center"></div></td>'+
					'<td>'+
						'<p class="name">'+file.name+'</p><p class="size">Processing...</p><strong class="error text-danger"></strong>'+
						'<div class="progress" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">'+
							'<div class="progress-bar progress-bar-info" style="width:0%;"></div>'+
						'</div>'+
					'</td>' +
					'<td><input type="text" name="titleKantor[]" class="form-control input-sm" /></td>'+
					'<td class="text-center">'+
						(!index && !o.options.autoUpload ?'<button class="btn btn-primary btn-sm start" disabled>Start</button> ' :'')+
						(!index ? '<button class="btn btn-warning btn-sm cancel">Cancel</button>' : '')+
					'</td></tr>');
				rows = rows.add(row);
			});
			return rows;			
		},
		downloadTemplate: function(o){
			var rows = $();
			$.each(o.files, function(index, file){
				var row = $('<tr class="template-download fade">'+
					'<td><div class="preview text-center">'+
						(file.thumbnailUrl ?'<a href="'+file.url+'" title="'+file.title+'" data-gallery><img src="'+file.thumbnailUrl+'" /></a>' :'')+
					'</div></td>'+
					'<td>'+
						'<p class="name">'+file.name_original+'</p>'+
						'<p class="size">'+o.formatFileSize(file.size)+'</p>'+
						(file.error ?'<strong class="error text-danger">'+file.error+'</strong>' :'')+
					'</td>'+
					'<td><div class="form-control input-sm">'+file.title+'</div></td>'+
					'<td class="text-center">'+
						'<button class="btn btn-danger btn-sm delete" data-type="'+file.deleteType+'" data-url="'+file.deleteUrl+'">Hapus</button>'+
					'</td>'+
					'</tr>');
				rows = rows.add(row);
			});
			return rows;			
		},
	};
	
	
	$('#jalanfile').fileupload(optionsFilenya).on('fileuploadsubmit', function (e, data){
		var arrKtg	= {"jalanfile":"jalan", "kantorfile":"kantor", "mediafile":"media", "storagefile":"storage", "inletfile":"inlet", "ukurfile":"ukur", "keteranganfile":"keterangan"};
		var nilai	= $(this).attr("id");
		var inputs 	= data.context.find("input[name='titleKantor[]']");
		if(inputs.val() == ""){
			alert("Keterangan gambar belum diisi....");
			data.context.find('button.start').prop('disabled', false);
			return false;
		} else{
			data.formData = {idr : $("input[name='idr']").val(), idk : $("input[name='idk']").val(), titleKantor : inputs.val(), ktg : arrKtg[nilai]}
		}
	});

	$('#kantorfile').fileupload(optionsFilenya).on('fileuploadsubmit', function (e, data){
		var arrKtg	= {"jalanfile":"jalan", "kantorfile":"kantor", "mediafile":"media", "storagefile":"storage", "inletfile":"inlet", "ukurfile":"ukur", "keteranganfile":"keterangan"};
		var nilai	= $(this).attr("id");
		var inputs 	= data.context.find("input[name='titleKantor[]']");
		if(inputs.val() == ""){
			alert("Keterangan gambar belum diisi....");
			data.context.find('button.start').prop('disabled', false);
			return false;
		} else{
			data.formData = {idr : $("input[name='idr']").val(), idk : $("input[name='idk']").val(), titleKantor : inputs.val(), ktg : arrKtg[nilai]}
		}
	});

	$('#mediafile').fileupload(optionsFilenya).on('fileuploadsubmit', function (e, data){
		var arrKtg	= {"jalanfile":"jalan", "kantorfile":"kantor", "mediafile":"media", "storagefile":"storage", "inletfile":"inlet", "ukurfile":"ukur", "keteranganfile":"keterangan"};
		var nilai	= $(this).attr("id");
		var inputs 	= data.context.find("input[name='titleKantor[]']");
		if(inputs.val() == ""){
			alert("Keterangan gambar belum diisi....");
			data.context.find('button.start').prop('disabled', false);
			return false;
		} else{
			data.formData = {idr : $("input[name='idr']").val(), idk : $("input[name='idk']").val(), titleKantor : inputs.val(), ktg : arrKtg[nilai]}
		}
	});

	$('#storagefile').fileupload(optionsFilenya).on('fileuploadsubmit', function (e, data){
		var arrKtg	= {"jalanfile":"jalan", "kantorfile":"kantor", "mediafile":"media", "storagefile":"storage", "inletfile":"inlet", "ukurfile":"ukur", "keteranganfile":"keterangan"};
		var nilai	= $(this).attr("id");
		var inputs 	= data.context.find("input[name='titleKantor[]']");
		if(inputs.val() == ""){
			alert("Keterangan gambar belum diisi....");
			data.context.find('button.start').prop('disabled', false);
			return false;
		} else{
			data.formData = {idr : $("input[name='idr']").val(), idk : $("input[name='idk']").val(), titleKantor : inputs.val(), ktg : arrKtg[nilai]}
		}
    });
	$('#inletfile').fileupload(optionsFilenya).on('fileuploadsubmit', function (e, data){
		var arrKtg	= {"jalanfile":"jalan", "kantorfile":"kantor", "mediafile":"media", "storagefile":"storage", "inletfile":"inlet", "ukurfile":"ukur", "keteranganfile":"keterangan"};
		var nilai	= $(this).attr("id");
		var inputs 	= data.context.find("input[name='titleKantor[]']");
		if(inputs.val() == ""){
			alert("Keterangan gambar belum diisi....");
			data.context.find('button.start').prop('disabled', false);
			return false;
		} else{
			data.formData = {idr : $("input[name='idr']").val(), idk : $("input[name='idk']").val(), titleKantor : inputs.val(), ktg : arrKtg[nilai]}
		}
    });
	$('#ukurfile').fileupload(optionsFilenya).on('fileuploadsubmit', function (e, data){
		var arrKtg	= {"jalanfile":"jalan", "kantorfile":"kantor", "mediafile":"media", "storagefile":"storage", "inletfile":"inlet", "ukurfile":"ukur", "keteranganfile":"keterangan"};
		var nilai	= $(this).attr("id");
		var inputs 	= data.context.find("input[name='titleKantor[]']");
		if(inputs.val() == ""){
			alert("Keterangan gambar belum diisi....");
			data.context.find('button.start').prop('disabled', false);
			return false;
		} else{
			data.formData = {idr : $("input[name='idr']").val(), idk : $("input[name='idk']").val(), titleKantor : inputs.val(), ktg : arrKtg[nilai]}
		}
    });
	$('#keteranganfile').fileupload(optionsFilenya).on('fileuploadsubmit', function (e, data){
		var arrKtg	= {"jalanfile":"jalan", "kantorfile":"kantor", "mediafile":"media", "storagefile":"storage", "inletfile":"inlet", "ukurfile":"ukur", "keteranganfile":"keterangan"};
		var nilai	= $(this).attr("id");
		var inputs 	= data.context.find("input[name='titleKantor[]']");
		if(inputs.val() == ""){
			alert("Keterangan gambar belum diisi....");
			data.context.find('button.start').prop('disabled', false);
			return false;
		} else{
			data.formData = {idr : $("input[name='idr']").val(), idk : $("input[name='idk']").val(), titleKantor : inputs.val(), ktg : arrKtg[nilai]}
		}
    });
	
	$('#jalanfile, #kantorfile, #storagefile, #inletfile, #ukurfile, #mediafile, #keteranganfile').on('fileuploaddestroy', function(e, data){
		if(confirm("Apakah anda yakin ?")) $("#loading_modal").modal(); 
		else return false; 
	}).on('fileuploaddestroyed', function(e, data){
		$("#loading_modal").modal("hide");
	});

	$('#petafile').fileupload($.extend(optionsFileSingle, {formData : { idr:$("input[name='idr']").val(), idk:$("input[name='idk']").val(), ktg:"peta" }} ));
	$('#bongkarfile').fileupload($.extend(optionsFileSingle, {formData : { idr:$("input[name='idr']").val(), idk:$("input[name='idk']").val(), ktg:"bongkar" }} ));

	$('#petafile, #bongkarfile').on('fileuploaddestroy', function(e, data){
		if(confirm("Apakah anda yakin ?")) $("#loading_modal").modal();
		else return false;
	}).on('fileuploaddestroyed', function(e, data){
		$(this).find(".fileinput-button").removeClass("disabled");
		$(this).find("input:file").prop("disabled", false);
		$("#loading_modal").modal("hide");
	}).on('fileuploadadd', function (e, data){
		$(this).find(".fileinput-button").addClass("disabled");
		$(this).find("input:file").prop("disabled", true);
	}).on('fileuploadfail', function (e, data){
		$(this).find(".fileinput-button").removeClass("disabled");
		$(this).find("input:file").prop("disabled", false);
	});

	$(document).ready(function(){
		$('#jalanfile').addClass('fileupload-processing');
		$.ajax({
			url		: $base_url+'/web/__get_upload_lcr.php',
			data	: {ktg : "jalan", idr: $("input[name='idr']").val(), idk: $("input[name='idk']").val()},
			dataType: 'json',
			context	: $('#jalanfile')[0]
		}).always(function(){
			$(this).removeClass('fileupload-processing');
		}).done(function(result){
			$(this).fileupload('option', 'done').call(this, $.Event('done'), {result: result});
		});

		$('#kantorfile').addClass('fileupload-processing');
		$.ajax({
			url		: $base_url+'/web/__get_upload_lcr.php',
			data	: {ktg : "kantor", idr: $("input[name='idr']").val(), idk: $("input[name='idk']").val()},
			dataType: 'json',
			context	: $('#kantorfile')[0]
		}).always(function(){
			$(this).removeClass('fileupload-processing');
		}).done(function(result){
			$(this).fileupload('option', 'done').call(this, $.Event('done'), {result: result});
		});

		$('#storagefile').addClass('fileupload-processing');
		$.ajax({
			url		: $base_url+'/web/__get_upload_lcr.php',
			data	: {ktg : "storage", idr: $("input[name='idr']").val(), idk: $("input[name='idk']").val()},
			dataType: 'json',
			context	: $('#storagefile')[0]
		}).always(function(){
			$(this).removeClass('fileupload-processing');
		}).done(function(result){
			$(this).fileupload('option', 'done').call(this, $.Event('done'), {result: result});
		});

		$('#ukurfile').addClass('fileupload-processing');
		$.ajax({
			url		: $base_url+'/web/__get_upload_lcr.php',
			data	: {ktg : "ukur", idr: $("input[name='idr']").val(), idk: $("input[name='idk']").val()},
			dataType: 'json',
			context	: $('#ukurfile')[0]
		}).always(function(){
			$(this).removeClass('fileupload-processing');
		}).done(function(result){
			$(this).fileupload('option', 'done').call(this, $.Event('done'), {result: result});
		});

		$('#inletfile').addClass('fileupload-processing');
		$.ajax({
			url		: $base_url+'/web/__get_upload_lcr.php',
			data	: {ktg : "inlet", idr: $("input[name='idr']").val(), idk: $("input[name='idk']").val()},
			dataType: 'json',
			context	: $('#inletfile')[0]
		}).always(function(){
			$(this).removeClass('fileupload-processing');
		}).done(function(result){
			$(this).fileupload('option', 'done').call(this, $.Event('done'), {result: result});
		});

		$('#mediafile').addClass('fileupload-processing');
		$.ajax({
			url		: $base_url+'/web/__get_upload_lcr.php',
			data	: {ktg : "media", idr: $("input[name='idr']").val(), idk: $("input[name='idk']").val()},
			dataType: 'json',
			context	: $('#mediafile')[0]
		}).always(function(){
			$(this).removeClass('fileupload-processing');
		}).done(function(result){
			$(this).fileupload('option', 'done').call(this, $.Event('done'), {result: result});
		});

		$('#keteranganfile').addClass('fileupload-processing');
		$.ajax({
			url		: $base_url+'/web/__get_upload_lcr.php',
			data	: {ktg : "keterangan", idr: $("input[name='idr']").val(), idk: $("input[name='idk']").val()},
			dataType: 'json',
			context	: $('#keteranganfile')[0]
		}).always(function(){
			$(this).removeClass('fileupload-processing');
		}).done(function(result){
			$(this).fileupload('option', 'done').call(this, $.Event('done'), {result: result});
		});

		$('#petafile').addClass('fileupload-processing');
		$.ajax({
			url		: $base_url+'/web/__get_upload_lcr_single.php',
			data	: {ktg : "peta", idr: $("input[name='idr']").val(), idk: $("input[name='idk']").val()},
			dataType: 'json',
			context	: $('#petafile')[0]
		}).always(function(){
			$(this).removeClass('fileupload-processing');
		}).done(function(result){
			if(result.files.length > 0){
				$('#petafile').find(".fileinput-button").addClass("disabled");
				$('#petafile').find("input:file").prop("disabled", true);
			} else{
				$('#petafile').find(".fileinput-button").removeClass("disabled");
				$('#petafile').find("input:file").prop("disabled", false);
			}
			$(this).fileupload('option', 'done').call(this, $.Event('done'), {result: result});
		});

		$('#bongkarfile').addClass('fileupload-processing');
		$.ajax({
			url		: $base_url+'/web/__get_upload_lcr_single.php',
			data	: {ktg : "bongkar", idr: $("input[name='idr']").val(), idk: $("input[name='idk']").val()},
			dataType: 'json',
			context	: $('#bongkarfile')[0]
		}).always(function(){
			$(this).removeClass('fileupload-processing');
		}).done(function(result){
			if(result.files.length > 0){
				$('#bongkarfile').find(".fileinput-button").addClass("disabled");
				$('#bongkarfile').find("input:file").prop("disabled", true);
			} else{
				$('#bongkarfile').find(".fileinput-button").removeClass("disabled");
				$('#bongkarfile').find("input:file").prop("disabled", false);
			}
			$(this).fileupload('option', 'done').call(this, $.Event('done'), {result: result});
		});

	});
});