$(function(){
	$(".alert-close").click(function(){
		if($(this).closest(".flash-alert").hasClass('flash-show')){
			$(this).closest(".flash-alert").removeClass('flash-show').addClass('flash-hide').delay(2000).hide(500);
		}
	});
	$(window).bind("load resize", function(){
		bottom = parseInt($(".flash-alert.fixed").css("bottom"));
		$(".flash-alert.fixed").each(function(i,e){
			increment 	= parseInt(($(this).innerHeight()+5));
			$(this).css({"bottom": bottom+"px"});
			bottom = (bottom + increment);
		});
	});
});
