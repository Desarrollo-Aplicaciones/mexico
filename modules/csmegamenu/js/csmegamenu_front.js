$(window).load(function(){
	resizeWidth();
});
(function($,sr){
  // debouncing function from John Hann
  // http://unscriptable.com/index.php/2009/03/20/debouncing-javascript-methods/
  var debounce = function (func, threshold, execAsap) {
      var timeout;

      return function debounced () {
          var obj = this, args = arguments;
          function delayed () {
              if (!execAsap)
                  func.apply(obj, args);
              timeout = null; 
          };

          if (timeout)
              clearTimeout(timeout);
          else if (execAsap)
              func.apply(obj, args);

          timeout = setTimeout(delayed, threshold || 100); 
      };
  }
// smartresize 
 jQuery.fn[sr] = function(fn){  return fn ? this.bind('resize', debounce(fn)) : this.trigger(sr); };

})(jQuery,'smartresize');

var TO = false;
$(window).smartresize(function(){
if(TO !== false)
    clearTimeout(TO);
 TO = setTimeout(resizeWidth, 100); //400 is time in miliseconds
 //resizeWidth();
});

function resizeWidth()
{
	var menuWidth = 1180 - $("#menu").outerWidth();
	var numColumn = 6;
	var currentWidth = $(".container_24").outerWidth() - $("#menu").outerWidth();
	//alert(currentWidth);
	if (currentWidth <= menuWidth) {
		new_width_column = currentWidth / numColumn;
		$('#menu div.options_list').each(function(index, element) { 
			var options_list = $(this).next();
			$(this).width(parseFloat(options_list.css("width"))/menuWidth*numColumn * new_width_column); 		
		});
		$('#menu div.option').each(function(index, element) {
			var option = $(this).next();
		$(this).width(parseFloat(option.css("width"))/menuWidth*numColumn * new_width_column);
		$("ul", this).width(parseFloat(option.css("width"))/menuWidth*numColumn * new_width_column);
		
		});
		$('#menu ul.column').each(function(index, element) {
			var column = $(this).next();
		$(this).width(parseFloat(column.css("width"))/menuWidth*numColumn * new_width_column);
		});
	}
	
	$('#menu ul > li > div.options_list').each(function(index, element) {
		var menu_left= $(".cs_mega_menu_cat").width()-10;
		if (menu_left > 0) {
			$(this).css('margin-left',menu_left+ 'px');
		}
		else
			$(this).css('margin-left', '0px');
	});
}




