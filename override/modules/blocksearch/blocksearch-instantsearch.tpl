{* if $instantsearch}
	<script type="text/javascript">
	// <![CDATA[
		function tryToCloseInstantSearch() {
			if ($('#old_center_column').length > 0)
			{
				$('#center_column').remove();
				$('#old_center_column').attr('id', 'center_column');
				$('#center_column').show();
				return false;
			}
		}
		
		instantSearchQueries = new Array();
		function stopInstantSearchQueries(){
			for(i=0;i<instantSearchQueries.length;i++) {
				instantSearchQueries[i].abort();
			}
			instantSearchQueries = new Array();
		}
		
		$("#search_query_{$blocksearch_type}").keyup(function(){
			if($(this).val().length > 0){
				stopInstantSearchQueries();
				instantSearchQuery = $.ajax({
					url: '{if $search_ssl == 1}{$link->getPageLink('search', true)|addslashes}{else}{$link->getPageLink('search')|addslashes}{/if}',
					data: {
						instantSearch: 1,
						id_lang: {$cookie->id_lang},
						q: $(this).val()
					},
					dataType: 'html',
					type: 'POST',
					success: function(data){
						if($("#search_query_{$blocksearch_type}").val().length > 0)
						{
							tryToCloseInstantSearch();
							$('#center_column').attr('id', 'old_center_column');
							$('#old_center_column').after('<div id="center_column" class="' + $('#old_center_column').attr('class') + '">'+data+'</div>');
							$('#old_center_column').hide();
							// Button override
							ajaxCart.overrideButtonsInThePage();
							$("#instant_search_results a.close").click(function() {
								$("#search_query_{$blocksearch_type}").val('');
								return tryToCloseInstantSearch();
							});
							return false;
						}
						else
							tryToCloseInstantSearch();
					}
				});
				instantSearchQueries.push(instantSearchQuery);
			}
			else
				tryToCloseInstantSearch();
		});
	// ]]>
	</script>
{/if *}
{if $ajaxsearch}
	<script type="text/javascript">
	// <![CDATA[
		$('document').ready( function() {
			$("#search_query_{$blocksearch_type}")
				.autocomplete(
					'{if $search_ssl == 1}{$link->getPageLink('search', true)|addslashes}{else}{$link->getPageLink('search')|addslashes}{/if}', {
						minChars: 3,
						max: 10,
						width: 500,
						selectFirst: false,
						scroll: false,
						dataType: "json",
						formatItem: function(data, i, max, value, term) {
							return value;
						},
						parse: function(data) {
						document.getElementById('search_query_{$blocksearch_type}').style.background="#ffffff";
						
						var mytab = new Array();

						if(data == "") {
							//$('#search_query_{$blocksearch_type}').attr("placeholder", "No se encontraron resultados.");
							//background:url('img/magnifying-glass.gif') no-repeat right center; 
							//$('.borde_busqueda').css('background-image', 'url(themes/gomarket/css/modules/blocksearch/img/loading.gif)');

							var vacio = new Array();
							vacio['product_link']='#';
							mytab[mytab.length] = { data: vacio, value: '<div class="busbig"><div class="bus_text_img_left">&nbsp;</div><div class="line_top_bus_bar resp_bus_bar_no_4 bus_text_ver_todo"><a href="#" class="link_line_top_bus">Sin Resultados</a></div><div class="line_top_bus_bar resp_bus_bar_4 bus_text_ver_todo"><a href="#" class="link_line_top_bus">Sin Resultados</a></div><div class="bus_precio line_top_bus_bar">&nbsp;</div><div class="bus_boton line_top_bus_bar"><div class="link_line_top_bus link_line_top_bus_mas">&nbsp;</div></div>'};
							//$('#search_query_{$blocksearch_type}').val("");
						} else {
							
							for (var i = 0; i < data.length; i++) {
								mytab[mytab.length] = { data: data[i], value: '<div class="busbig"><div class="bus_img_left"><div class="link_line_top_bus align_img_bus_bar"><a href="' + data[i].product_link + '" onclick="cargaproduct(this);"><img alt="farmalisto" src="' + data[i].imgs + '"></a></div></div><div class="bus_text line_top_bus_bar resp_bus_bar_no_4"><div class="text_probus_container"><a href="' + data[i].product_link + '" class="link_line_top_bus" onclick="cargaproduct(this);">' + data[i].pname.toLowerCase()  + '</a></div></div><div class="bus_text line_top_bus_bar resp_bus_bar_4"><div class="text_probus_container"><a href="' + data[i].product_link + '" class="link_line_top_bus" onclick="cargaproduct(this);">' + (data[i].pname).substring(0, 48).toLowerCase()  + '. . .</a></div></div><div class="bus_precio line_top_bus_bar"><div class="price_container"><a href="' + data[i].product_link + '" class="link_line_top_bus" onclick="cargaproduct(this);"> $' + data[i].price + '&nbsp;</a></div></div><div class="bus_boton line_top_bus_bar"><div class="link_line_top_bus_2"><a href="index.php?controller=cart&add=1&qty=1&id_product=' + data[i].id_product + '&token=2e617d6e25442930c5c02a6881704405"" rel="ajax_id_product_' + data[i].id_product + '" onclick="cargaproduct(this);"> <span class="bus_bar_span_agregar">Agregar</span> <img alt="farmalisto" src="/img/Carrito.png"> </a></div></div>'}; 

								/*mytab[mytab.length] = { data: data[i], value: '<div class="busbig"><div class="bus_img_left"><div class="link_line_top_bus align_img_bus_bar"><a href="' + data[i].product_link + '" onclick="cargaproduct(this);"><img alt="farmalisto" src="' + data[i].imgs + '"></a></div></div><div class="bus_text line_top_bus_bar resp_bus_bar_no_4"><div class="text_probus_container"><a href="' + data[i].product_link + '" class="link_line_top_bus" onclick="cargaproduct(this);">' + data[i].pname.toLowerCase()  + '</a></div></div><div class="bus_text line_top_bus_bar resp_bus_bar_4"><div class="text_probus_container"><a href="' + data[i].product_link + '" class="link_line_top_bus" onclick="cargaproduct(this);">' + (data[i].pname).substring(0, 48).toLowerCase()  + '. . .</a></div></div><div class="bus_precio line_top_bus_bar"><div class="price_container"><a href="' + data[i].product_link + '" class="link_line_top_bus" onclick="cargaproduct(this);"> $' + data[i].price + '&nbsp;</a></div></div><div class="bus_boton line_top_bus_bar"><div class="link_line_top_bus_2"><a href="/carro-de-la-compra?add=&id_product=' + data[i].id_product + '" rel="ajax_id_product_' + data[i].id_product + '" onclick="cargaproduct(this);"> <span class="bus_bar_span_agregar">Agregar</span> <img alt="farmalisto" src="/img/Carrito.png"> </a></div></div>'}; */

								//console.log(data.toString());
							}

							mytab[mytab.length] = { data: '', value: '<div class="busbig"><div class="bus_text_img_left text_probus_container_tres_puntos"><a href="#" onclick="ver_mas_bus_bar(this);"> ... </a></div><div class="bus_text line_top_bus_bar resp_bus_bar_no_4"><div class="text_probus_container bus_bar_bold_mas"><a href="#" class="link_line_top_bus" onclick="ver_mas_bus_bar(this);"> Ver todos... </a></div></div><div class="bus_text line_top_bus_bar resp_bus_bar_4"><div class="text_probus_container bus_bar_bold_mas"><a href="#" class="link_line_top_bus" onclick="ver_mas_bus_bar(this);">Ver todos...</a></div></div><div class="bus_precio line_top_bus_bar"><div class="price_container"><a href="#" class="link_line_top_bus" onclick="ver_mas_bus_bar(this);"></a></div></div><div class="bus_boton line_top_bus_bar"><div class="link_line_top_bus_2 link_line_top_bus_mas"><a href="#" onclick="ver_mas_bus_bar(this);"><span class="bus_bar_span_agregar">&nbsp;&nbsp;&nbsp;Ver más</span><span class="bus_bar_span_agregar_plus">+</span> </a></div></div></div>'};
						}

							return mytab;
						},
						extraParams: {
							ajaxSearch: 1,
							id_lang: {$cookie->id_lang}
						}
					}
				)
				.result(function(event, data, formatted) {
					$('#search_query_{$blocksearch_type}').val(data.pname);
					/*document.location.href = data.product_link;*/
				})
		});
	// ]]>
	</script>
{/if}


<script type="text/javascript">

	function cargaproduct(object) {

		window.location.href =  object;

	}

	function ver_mas_bus_bar(object) {
		
		document.getElementById('submit_search_instant').click();
	}
	

</script>