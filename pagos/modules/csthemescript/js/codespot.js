$(document).ready(function() {		
		$().UItoTop({ easingType: 'easeOutQuart' });
});
$(document).ready(function(){
	if($(window).width()<=767)
	{
		show_hide_footer();
		$("div.comments_advices a").click(function(){
			
			$('.title_hide_show').removeClass('selected');
			$('.content_hide_show').hide();
			$("#title_idTab5").addClass("selected");
			$('#idTab5').show();
		});
	}
	else
	{
		$("#toTop").hide();
	}
	$(window).resize(function(){
		if($(window).width()<=767)
		{
			//show_hide_footer();
			if(!isMobile())
			{	
				show_hide_footer();
			}
		}
		else
		{
			$("#footer .f_block_content").show();
		}
	});
	function show_hide_footer() 
	{
		$('#footer .f_block_content').hide();
		$('.show_hide_footer').addClass("footer_close");
		$('.show_hide_footer').toggle(	
			function(){
				$(this).addClass("footer_open");
				$(this).removeClass("footer_close");
				$(this).parent().children('.f_block_content').slideDown(300);
				
			},
			function(){
				$(this).addClass("footer_close");
				$(this).removeClass("footer_open");
				$(this).parent().children('.f_block_content').slideUp();
				
			}
		);
	}
});
	
		
	
