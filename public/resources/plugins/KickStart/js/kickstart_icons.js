
jQuery(document).ready(function($){

	
	/*---------------------------------
		Icons
	-----------------------------------*/
	$('.icon').each(function(){
		$(this).append('<span aria-hidden="true">'+$(this).attr('data-icon')+'</span>')
		.css('display', 'inline-block');
	});
	
	
});
