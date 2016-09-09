$(document).ready(function(){
	
	$(".unstyled li").css("cursor", "pointer");
	
	$(".unstyled li").on("click", function(event){

		$("#icon_menu").val($(this).children('i').attr("class"));
		scrollTo($("#icon_menu"));
			
	});
	
	
});