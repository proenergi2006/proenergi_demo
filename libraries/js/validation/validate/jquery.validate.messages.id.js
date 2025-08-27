(function( factory ) {
	if ( typeof define === "function" && define.amd ) {
		define( ["jquery", "../jquery.validate"], factory );
	} else if (typeof module === "object" && module.exports) {
		module.exports = factory( require( "jquery" ) );
	} else {
		factory( jQuery );
	}
}(function( $ ) {

/*
 * Translated default messages for the jQuery validation plugin.
 * Locale: ID (Indonesia; Indonesian)
 */
$.extend( $.validator.messages, {
	required: "Kolom ini belum diisi atau dipilih",
	remote: "Harap benarkan kolom ini.",
	email: "Silakan masukkan format email yang benar.",
	url: "Silakan masukkan format URL yang benar.",
	date: "Format tanggal dd/mm/yyyy.",
	dateISO: "Silakan masukkan format tanggal(ISO) yang benar.",
	time: "Format waktu yang benar antara 00:00 dan 23:59",
	number: "Silakan masukkan angka yang benar.",
	digits: "Harap masukan angka saja.",
	creditcard: "Harap masukkan format kartu kredit yang benar.",
	equalTo: "Harap masukkan nilai yg sama dengan sebelumnya.",
	maxlength: $.validator.format( "Input dibatasi hanya {0} karakter." ),
	minlength: $.validator.format( "Input tidak kurang dari {0} karakter." ),
	rangelength: $.validator.format( "Panjang karakter yg diizinkan antara {0} dan {1} karakter." ),
	range: $.validator.format( "Harap masukkan nilai antara {0} dan {1}." ),
	max: $.validator.format( "Harap masukkan nilai lebih kecil atau sama dengan {0}." ),
	min: $.validator.format( "Nilai harus lebih besar dari {0}." )
});
return $;
}));

$.extend($.validator, {
	showErrorField: function(a,b){
		let label = $('<label id="'+a+'-error" class="error">'+b+'</label>');
		let kolom = $("#"+a);
		kolom.parents(".form-group").addClass("has-error");
		config.validation.errorPlacement.call(null, label, kolom);
	},
});
