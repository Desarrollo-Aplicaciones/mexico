function loadGoogleFont(name,style)
{
	if($.cookie(name))
	{
		$('head').append('<link id="link_' + name + '" rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=' + $.cookie(name) + '">');
		$('' + style + '').css('font-family',$.cookie(name));
		$('#result_' + name).html($.cookie(name));
		$('#result_' + name).css('font-family',$.cookie(name));
		$('#' + name + '').val($.cookie(name));
		if($('#' + name + '').val() == $.cookie(name))
			$('#' + name + '').attr("selected","selected");
	}
}

function loadGoogleFontFromConfig(elem,style)
{
	$('#result_' + elem).html(style);
	$('#result_' + elem).css('font-family',style);;
	if($("#result_" + elem + "").html() != "")
	$('head').append('<link id="link_' + elem + '" rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=' + style + '">');
$("select#" + elem + " > option").each(function(index, element) {
			if($(element).val() == $("#result_" + elem + "").html())
				$(element).attr("selected","selected");
	  });
}
function showResultChooseFont(id,classE)
{
	var id_result = 'result_' + id;
	$("#" + id_result).html("" + $("#" + id).val() + "");
	$("#" + id_result).css("font-family","" + $("#" + id).val() + "");
	$('link#link_' + id).remove();
	if($("#" + id).val() != "")
	{
		$('head').append('<link id="link_' + id + '" rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=' + $("#" + id).val() + '">');
		$.cookie(''+ id +'',$("#" + id).val());
		$('' + classE + '').css('font-family',$.cookie(id));
	}
}
function changeOptionColumn(column)
{
	$('div.option_columns div').hide();
	$('div.' + column + '').show();
}
function isMobile() {
		if(navigator.userAgent.match(/Android/i) ||
			navigator.userAgent.match(/webOS/i) ||
			navigator.userAgent.match(/iPad/i) ||
			navigator.userAgent.match(/iPhone/i) ||
			navigator.userAgent.match(/iPod/i)
			){
				return true;
		}
		else return false;
}
function get_cookies_array() {
	var cookies_list = { };
    if (document.cookie && document.cookie != '') {
    var split = document.cookie.split(';');
        for (var i = 0; i < split.length; i++) {
            var name_value = split[i].split("=");
            name_value[0] = name_value[0].replace(/^ /, '');
            cookies_list[decodeURIComponent(name_value[0])] = decodeURIComponent(name_value[1]);
        }
    }
    return cookies_list;
}
if(isMobile())
{ 
	jQuery(document).ready(function($) {
		$('.cpanel_icon').hide();
	});
}
jQuery(document).ready(function($) {
	$('input[name=color_template]').click(function(){
			clearCookie();
			$.cookie('color_template',$(this).val());
			location.reload();
	});	
$('input[name=radio_setting_column]').click(function(){
			$.cookie('setting_column',$('select[name=setting_column]').val() + '&' + $(this).val() );
			location.reload();
	
	});			
$('input[value=' + $.cookie('color_template') + ']').attr('checked', 'true');
	
	var styleTextbox = '<style id="setconfig">#page{	width: 1200px;   box-shadow: 0 0 8px #666666;    margin: 0 auto;}@media only screen and (min-width: 1024px) and (max-width: 1279px) {	#page{	width: 960px;}}@media only screen and (max-width: 1023px){#page{margin:0 auto;border-radius:0;box-shadow: 0 0 0 #fff;}}@media only screen and (min-width: 768px) and (max-width: 1023px) {	#page {width: 768px;}}@media only screen and (max-width: 767px){#page{width:100%;}}</style>';
	if($.cookie('mode_css'))
	{
		if($.cookie('mode_css') == '1200px')
		{
			$('#page').css('width','');
			$('body').append(styleTextbox);
		}
		else
		{
			$('body style#setconfig').remove();
			$("#page").css('width',$.cookie('mode_css'));
		}
	}
	$('input[name=mode_css][value=box]').click(function(){
			$('#page').css('width','');
			if ( !$("link#setconfig").length ) {
				$('body').append(styleTextbox);
			}
			
			$.cookie('mode_css', '1200px');
			$.cookie('mode_css_input', 'box');
	});	
	$('input[name=mode_css][value=wide]').click(function(){
			$('#page').css('width','100%');
			$('body style#setconfig').remove();
			$.cookie('mode_css', '100%');
			$.cookie('mode_css_input', 'wide');
	});		
	$("#cs_reset_setting").click(function() {
	var cookies = get_cookies_array();
	for(var name in cookies) {
		if(name != 'display' && name!= 'display_class')
	  $.cookie(name,null);
	}
	$(".pattern_item").removeClass("active");
	location.reload();
	});
	initCPanel();
		function initCPanel() {
			var $marginRighty = $('.cpanelContainer .cpanel_content_container');
			$marginRighty.animate({
				marginLeft: -($marginRighty.outerWidth()-40)
			});
			$marginRighty.addClass(parseInt($marginRighty.css('marginLeft'),10) == 0 ? "cpanel_closed" : "cpanel_opened").removeClass(parseInt($marginRighty.css('marginLeft'),10) == 0 ? "cpanel_opened" : "cpanel_closed");
		}
		$('.cpanelContainer .cpanel_icon').click(function() {
			$('.cpanelContainer .cpanel_content_container').show(); 
			
			var $marginRighty = $('.cpanelContainer .cpanel_content_container');
			$marginRighty.animate({
				marginLeft: parseInt($marginRighty.css('marginLeft'),10) == 0 ? -($marginRighty.outerWidth()-40) : 0
			});
			
			$marginRighty.addClass(parseInt($marginRighty.css('marginLeft'),10) == 0 ? "cpanel_closed" : "cpanel_opened").removeClass(parseInt($marginRighty.css('marginLeft'),10) == 0 ? "cpanel_opened" : "cpanel_closed");
		});
		$('#improved .head').click(function(e){
			e.preventDefault();
			
			 $(this).closest('li').find('.content').slideToggle('slow', function() {
				if ($(this).closest('li').find('.content').is(':hidden'))
				{
					$(this).closest('li').find('.content').hide();
				}
				else
				{
					$('#improved .content').hide();
					$(this).closest('li').find('.content').show();
				}
			});     
		});
		if($.cookie('cookie_bg_pattern')) {
			$('body').css('background-image', 'url("' + $.cookie('cookie_bg_pattern') + '")');
		}
});

function  clearCookie()
{
	var cookie_list = get_cookies_array();
		for(var name in cookie_list) 
		{
			if(name != 'display' && name!= 'display_class')
				$.cookie(name,null);
		}
		$(".pattern_item").removeClass("active");
	
}
