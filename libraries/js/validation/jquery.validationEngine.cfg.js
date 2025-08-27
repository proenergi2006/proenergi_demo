$(function(){
	$(".form-validasi").validationEngine();	
	$(".form-validasi-wysiwyg").validationEngine('attach',{
	onValidationComplete: function(form, status){
		if(status == true){
			CKEDITOR.instances.task.updateElement();
			if($("#task").val() == ""){
				buildPromptManual($("#cke_task"), "Field ini tidak boleh kosong");
			}else {
				form.validationEngine('detach');
				form.submit();
			}
		}
	}});
	$(".form-wysiwyg-finding").validationEngine('attach',{
	onValidationComplete: function(form, status){
		if(status == true){
			CKEDITOR.instances.finding.updateElement();
			CKEDITOR.instances.solution.updateElement();
			if($("#finding").val() == ""){
				buildPromptManual($("#cke_finding"), "Field ini tidak boleh kosong");
			}else {
				form.validationEngine('detach');
				form.submit();
			}
		}
	}});
});
function zeroPassCheck(field, rules, i, options){
	if(field.val() == ""){
		return options.allrules.zeroPassCheck.alertText;
	}
}
function timPickerCheck(field, rules, i, options){
	if(field.val() == "0:00:00" || field.val() == "00:00:00"){
		return options.allrules.timePicker.alertText;
	}
}
function maxnya(field, rules, i, options){
	var nilai = rules[i + 2];
	if (parseInt(field.val()) > parseInt(nilai))
		return options.allrules.maxnya.alertText.replace("::nilai",nilai);
}
function confirmPassCheck(field, rules, i, options){
	var equalsField = rules[i + 2];
	if (field.val() != $("#" + equalsField).val())
		return options.allrules.confirmPassCheck.alertText;
}
function volumeCheck(field, rules, i, options){
	var nilai = field.val();
	if(nilai < 1000)
		return options.allrules.volumeCheck.alertText;
}
function fileCheck(field, rules, i, options){
	var maxiSize = 2 * 1024 * 1024;
	var regxFile = new RegExp("(.*?)\.(jpg|jpeg|png|pdf|rar)$");
	if(field.val()){
		var fileSize = field[0].files[0].size;
		if(fileSize > maxiSize)
			return options.allrules.filesizeCheck.alertText.replace("*|*","2MB");
		else if(!(regxFile.test(field.val().toLowerCase())))
			return options.allrules.filetypeCheck.alertText.replace("*|*",".jpg, .jpeg, .png, .pdf, .rar");
	}
}
function potoCheck(field, rules, i, options){
	var maxiSize = 2 * 1024 * 1024;
	var regxFile = new RegExp("(.*?)\.(jpg|jpeg|png)$");
	if(field.val()){
		var fileSize = field[0].files[0].size;
		if(fileSize > maxiSize)
			return options.allrules.filesizeCheck.alertText.replace("*|*","2MB");
		else if(!(regxFile.test(field.val().toLowerCase())))
			return options.allrules.filetypeCheck.alertText.replace("*|*",".jpg, .jpeg, .png");
	}
}

function buildPromptManual(field, promptText){
	var prompt 			= $('<div>').addClass("formError "+field.attr("id")+"-formError");
	var promptContent 	= $('<div>').addClass("formErrorContent").html(promptText).appendTo(prompt);
	var arrow 			= $('<div>').addClass("formErrorArrow");
	arrow.addClass("formErrorArrowTop");
	prompt.append(arrow);
	$("body").append(prompt);
	var pos = calculatePosition(field, prompt);
	prompt.css({
		"top": pos.callerTopPosition,
		"left": pos.callerleftPosition,
		"marginTop": "-3px",
		"opacity": 0
	});
	return prompt.animate({"opacity": 0.87});
}

function calculatePosition(field, promptElmt){
	var promptTopPosition, promptleftPosition, marginTopSize;
	var fieldWidth 		= field.width();
	var promptHeight 	= promptElmt.height();
	var offset 			= field.offset();
	promptTopPosition 	= offset.top;
	promptleftPosition 	= offset.left;
	promptTopPosition += -promptHeight - 5;
	return {
		"callerTopPosition": promptTopPosition + "px",
		"callerleftPosition": promptleftPosition + "px",
	};
}
function updatePrompt(field){
	var prompt = $("body").find("."+field.attr("id")+"-formError");
	if(prompt.length > 0){
		var pos = calculatePosition(field, prompt);
		css = {
			"top": pos.callerTopPosition,
			"left": pos.callerleftPosition
		};
		prompt.css(css);
	}
}

