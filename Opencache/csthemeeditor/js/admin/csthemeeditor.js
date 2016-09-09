function colorEvent (colorItem,saveSettingsDefault)
{
	$("#" + colorItem + "").ColorPicker({
		color: "#0000ff",
		onShow: function (colpkr) {
			$(colpkr).fadeIn(500);
			return false;
		},
		onHide: function (colpkr) {
			$(colpkr).fadeOut(500);
			return false;
		},
		onChange: function (hsb, hex, rgb) {
			$("#result_" + colorItem).css("background", "#" + hex); //custom event change
			$("#result_" + colorItem).val("#" + hex);
			if(saveSettingsDefault == false)
			{
				changeColor();
			}
		}
	});
}
function loadGoogleFontFromConfig(elem,style,fsize,fstyle,fweight)
{
	$('#result_' + elem).html(style);
	$('#result_' + elem).css('font-family',style);
	$('#result_' + elem).css('font-size',fsize+'px');
	$('#result_' + elem).css('font-style',fstyle);
	$('#result_' + elem).css('font-weight',fweight);
	if($("#result_" + elem + "").html() != "")
	$('head').append('<link id="link_' + elem + '" rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=' + $("#result_" + elem + "").html() + '">');
$("select#" + elem + " > option").each(function(index, element) {
			if($(element).val() == $("#result_" + elem + "").html())
				$(element).attr("selected","selected");
	  });
}
function changeColor()
{
	$("select#color_template > option").each(function(index, element) {
	if($(element).val() == 'custom')
		$(element).attr("selected","selected");
	});
}
function ShowUploadImage(elem,show)
{
	$('input[name=' + elem + ']').each(function(index, element) {
			$(element).click(function(){
				if($(element).attr('checked')) {
					if($(element).val() == show )
						$("#show_" + show + "").show();
					else 
						$("#show_" + show + "").hide();
				}
			});
			if($(element).attr('checked')) {
					if($(element).val() == show )
						$("#show_" + show + "").show();
					else 
						$("#show_" + show + "").hide();
				}
		});
}
function showResultChooseFont(id,id_result,saveSettingsDefault)
{
	$('link#link_' + id).remove();
	if($("#" + id).val() != "")
		$('head').append('<link id="link_' + id + '" rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=' + $("#" + id).val() + '">');
	$("#" + id_result).html("" + $("#" + id).val() + "");
	$("#" + id_result).css("font-family","" + $("#" + id).val() + "");
	if(saveSettingsDefault == false)
	{
		changeColor();
	}
}
function showFontSize(id,id_result,saveSettingsDefault)
{
	$("#" + id_result).css("font-size","" + $("#" + id).val() + "px");
	if(saveSettingsDefault == false)
	{
		changeColor();
	}
}
function showFontWeight(id,id_result,saveSettingsDefault)
{
	$("#" + id_result).css("font-style","" + $("#" + id).val() + "");
	if(saveSettingsDefault == false)
	{
		changeColor();
	}
}
function showFontWeight2(id,id_result,saveSettingsDefault)
{
	$("#" + id_result).css("font-weight","" + $("#" + id).val() + "");
	if(saveSettingsDefault == false)
	{
		changeColor();
	}
}
function changeOptionColumn(column,saveSettingsDefault)
{
	$('div.option_columns div').hide();
	$('div.' + column + '').show();
	$('input[name="column_class"]').attr('checked', false);
	$('div.' + column + ' input[name="column_class"]:first').attr("checked","checked");
	if(saveSettingsDefault == false)
	{
		changeColor();
	}
}
$(window).ready(function() {
	$('form.csthemeeditor input:radio[name!="show_panel_front_end"]').click(function(){
		changeColor();
	});
});