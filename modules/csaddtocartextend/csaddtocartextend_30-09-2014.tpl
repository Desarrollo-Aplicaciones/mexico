<!-- CS add to cart extend module -->
<script type="text/javascript">
//<![CDATA[
	{if $page_name=="category"}
		$(document).ajaxComplete(function( event,request, settings ) {
			$('#add_to_cart input').attr('onclick', 'return OnAddclickDetail();');
			$('a.ajax_add_to_cart_button').attr('onclick', 'return OnAddclickCategory($(this));');
		});
	{/if}
	$(window).ready(function(){
		$('#add_to_cart input').attr('onclick', 'return OnAddclickDetail();');
		$('a.ajax_add_to_cart_button').attr('onclick', 'return OnAddclickCategory($(this));');
	});
	
	function OnAddclickDetail() {
		var image_detail = $('#view_full_size img').attr('src');
		image_detail = image_detail.replace("large_default", "small_default"); 
		var name_detail = $('div#pb-left-column h1').html();
		var id_detailt = $("input[name=id_product]").val();
		var link_detail = "{$__PS_BASE_URI__}index.php?id_product=" + id_detailt + "&controller=product";
		var string_info = "<a href=" + link_detail + " class=\"product_img_link\"><img src='" +  image_detail + "'/></a>" + "<h3><a href=" + link_detail + ">" + name_detail + "</a></h3> {l s='ha sido agregado al ' mod='csaddtocartextend'}<a href='{$__PS_BASE_URI__}index.php?controller=order&paso=inicial' class='your_cart'>{l s='carrito' mod='csaddtocartextend'}</a>" ;
		$.ambiance({
			message: string_info, 
			type: "success",
			timeout: 7
		});
	}
	
	function OnAddclickCategory(element) {
		var id_product = element.attr('rel').substring(16);
		var html_product = element.parent().html();
		$("body").append("<div id=\"add_to_card_extend_"+ id_product + "\" style=\"display:none\">" + html_product + "</div>")
		var image_p = $("#add_to_card_extend_" + id_product + " div.image").html();
		image_p = image_p.replace("home_default", "small_default"); 
		var name_p = $("#add_to_card_extend_" + id_product + " div.name_product").html();
		$('div').remove("#add_to_card_extend_" + id_product + "");
		$.ambiance({
			message: image_p + name_p + "{l s='ha sido agregado al ' mod='csaddtocartextend'}<a href='{$__PS_BASE_URI__}index.php?controller=order&paso=inicial' class='your_cart'>{l s='carrito' mod='csaddtocartextend'}</a>", 
			type: "success",
			timeout:7
		});
		
	}
//]]
</script>
<!-- /CS add to cart extend module -->

