function isIpad() 
	{
		if(navigator.userAgent.match(/iPad/i))
		{
				return true;
		}
		else
		{
			return false;
		}
	}
function detectIPadOrientation ()
	{

		if ( orientation == 0 ||orientation == 180) {
			return 0;
		}
		else if ( orientation == 90 || orientation == -90) {
			return 90;
		}
	 }
 function getHtmlHide1(moreInsert1,numLiItem) 
 {
	var htmlLiHide="";
	if($("#cs_megamenu_more").length==0)
	{
		for(var i=(moreInsert1+1);i<=numLiItem;i++)
		{
			$liclass=$('#menu ul li.level-1:nth-child('+i+')').attr('class');
			$liclass=$liclass.replace("level-1","level-2");
			htmlLiHide+='<li class="'+$liclass+'">'+$('#menu ul li.level-1:nth-child('+i+')').html()+'</li>';
		}
	}
	return htmlLiHide;
 }
	 
 function getHtmlHide2(moreInsert2,numLiItem) 
 {
	var htmlLiHide="";
	if($("#cs_megamenu_more").length==0)
	{
		for(var i=(moreInsert2+1);i<=numLiItem;i++)
		{
			$liclass=$('#menu ul li.level-1:nth-child('+i+')').attr('class');
			$liclass=$liclass.replace("level-1","level-2");
			htmlLiHide+='<li class="'+$liclass+'">'+$('#menu ul li.level-1:nth-child('+i+')').html()+'</li>';
		}
	}
	return htmlLiHide;
 }
 function refeshMenuIpad(moreInsert1,moreInsert2,htmlLiHide1,htmlLiHide2)
	{
		if($("#cs_megamenu_more").length>=0)
		{
			$("ul li#cs_megamenu_more").remove();
			if(isIpad()&&detectIPadOrientation()==90)
			{
				$(htmlLiHide2).insertAfter('#menu ul li:nth-child('+moreInsert2+')');
			}
			if(isIpad()&&detectIPadOrientation()==0)
			{	
				$(htmlLiHide1).insertAfter('#menu ul li:nth-child('+moreInsert1+')');
			}
		}
	}
function refeshMenuRespon(htmlMenu)
	{
		if($("#cs_megamenu_more").length>=0)
		{
			$("#menu ul.ul_mega_menu li.level-1").remove();
			$("#menu ul.ul_mega_menu").html(htmlMenu);
			
		}
	}
function addMoreOnLoad(moreInsert1,moreInsert2,numLiItem,htmlLiHide1,htmlLiHide2) 
	 {
		if(moreInsert1<numLiItem & moreInsert2<moreInsert1)
		{
				if($(window).width() <=767)
				{
					$("#menu").css("display","none");
					$("#megamenu-responsive").css("display","block");
					$("#megamenu-responsive").css("margin-bottom","10px");
				}
				else
					{
						$("#menu").css("display","block");
						$("#megamenu-responsive").css("display","none");
					}
				if((isIpad()&&detectIPadOrientation()==90 ) ||($(window).width() > 959 && $(window).width()<1280) )//ipad ngang
				{
					refeshMenuRespon(htmlMenu);
					if($("#cs_megamenu_more").length==0)
					{
						$('<li id="cs_megamenu_more" class="menu_item menu_last level-1 parent"><a href="#" class="more title_menu_parent">More</a><div class="option more-menu" style="width:auto"><ul class="column cms">'+htmlLiHide1+'</ul></div>').insertAfter('#menu ul li.level-1:nth-child('+moreInsert1+')');
						
					}
				
					if($("#cs_megamenu_more").length>0)
					{
						for(var j=(moreInsert1+2);j<=(numLiItem+1);j++)
						{
							var delItem=moreInsert1+2;
							$("#menu ul li:nth-child("+delItem+")").remove();
						}
					}
				}
				if((isIpad()&&detectIPadOrientation()==0) || ($(window).width() > 767 && $(window).width()<960))
				{
					if($("#cs_megamenu_more").length==0)
					{
						$('<li id="cs_megamenu_more" class="menu_item menu_last level-1 parent"><a href="#" class="more title_menu_parent">More</a><div class="option more-menu" style="width:auto"><ul class="column cms">'+htmlLiHide2+'</ul></div>').insertAfter('#menu ul li.level-1:nth-child('+moreInsert2+')');
						
					}
				
					if($("#cs_megamenu_more").length>0)
					{
						for(var j=(moreInsert2+2);j<=(numLiItem+1);j++)
						{
							var delItem=moreInsert2+2;
							$("#menu ul li:nth-child("+delItem+")").remove();
						}
					}
				}
		}
		
	 }
	 
	function addMoreResponsive(moreInsert1,moreInsert2,numLiItem,htmlLiHide1,htmlLiHide2) 
	 {
		if(moreInsert1<numLiItem && moreInsert2 <moreInsert1)
		{
			$(window).resize(function(){
				
				if(!isIpad())
				{	
					if($(window).width() <=767)
					{
						$("#menu").css("display","none");
						$("#megamenu-responsive").css("display","block");
						$("#megamenu-responsive").css("margin-bottom","10px");
					}
					else
					{
						$("#menu").css("display","block");
						$("#megamenu-responsive").css("display","none");
					}
					if($(window).width() > 767 && $(window).width()<960)//ipad doc
					{	
						refeshMenuRespon(htmlMenu);
						if($("#cs_megamenu_more").length==0)
						{
							$('<li id="cs_megamenu_more" class="menu_item menu_last level-1 parent"><a href="#" class="more title_menu_parent">More</a><div class="option more-menu" style="width:auto"><ul class="column cms">'+htmlLiHide2+'</ul></div>').insertAfter('#menu ul li.level-1:nth-child('+moreInsert2+')');
							
						}
						if($("#cs_megamenu_more").length>0)
						{
							for(var j=(moreInsert2+2);j<=(numLiItem+1);j++)
							{
								var delItem=moreInsert2+2;
								$("#menu ul li:nth-child("+delItem+")").remove();
								$("#menu ul li:nth-child("+j+")").remove();
							}
						}
						//iconClickIpad();
					}
					else if($(window).width() > 959 && $(window).width()<1280)//ipad ngang
					{	
						refeshMenuRespon(htmlMenu);
						if($("#cs_megamenu_more").length==0)
						{
							$('<li id="cs_megamenu_more" class="menu_item menu_last level-1 parent"><a href="#" class="more title_menu_parent">More</a><div class="option more-menu" style="width:auto"><ul class="column cms">'+htmlLiHide1+'</ul></div>').insertAfter('#menu ul li.level-1:nth-child('+moreInsert1+')');
							
						}
						if($("#cs_megamenu_more").length>0)
						{
							for(var j=(moreInsert1+2);j<=(numLiItem+1);j++)
							{
								var delItem=moreInsert1+2;
								$("#menu ul li:nth-child("+delItem+")").remove();
								$("#menu ul li:nth-child("+j+")").remove();
							}
						}
						//iconClickIpad();
					}
					else if($(window).width()>1279)
					{
						if($("#cs_megamenu_more").length>0)
						{
							refeshMenuRespon(htmlMenu);
						}
					}
				}
			});
			
		}
	}
function iconClickIpad()
	{
		if($(window).width() > 767 && $(window).width()<1024)
			{
				$('#menu ul li.parent').prepend('<p>+</p>');
				$('#menu ul li.parent > p,#menu ul.ul_mega_menu li#cs_megamenu_more > p').click(function(){
					if ($(this).text() =='+'){
						$('#menu ul.ul_mega_menu li.parent > div').slideUp();
						$('#menu ul.ul_mega_menu li.parent > p').text('+');
						$('#menu ul.ul_mega_menu li#cs_megamenu_more > div').slideUp();
						$('#menu ul.ul_mega_menu li#cs_megamenu_more > p').text('+');
						
						$(this).parents('ul.ul_mega_menu li').children('div').slideDown(100);
						$(this).text('-');
					}else{
						$(this).parent('li').children('div').slideUp();
						$(this).text('+');
					}  
					
				});
			}
	}