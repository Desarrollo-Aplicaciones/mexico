// alert("incluye archivo js");

// inicializacion de variables
var array_list_cart_rules = [];
var array_list_products = [];
var counterCoupon = 0;

$(function() {

	// tomar ruta del directorio
	var module_dir = $("#module_template_dir").val();
	var path = $("#pathModule").val();

	// deshabilitar boton enter en formulario de nuevo descuento progresivo
	$("#form_progressive_discounts").keypress(function(e) {
		if (e.which == 13) {
			return false;
		}
	});

	// imprimir lista seleccionable de los estados de ordenes que aplican descuneto progresivo
	$.ajax({
		type: "POST",
		url: module_dir+"/ajaxProgressiveDiscounts.php",
		data: {
			action: "search_states_orders"
		},
		success: function(html) {
			jQuery('#list_states_orders').html(html);
		}
	});

	/******** form_progressive_discounts ********/
	// cart_rules
	$("#button_search_coupon").click(
		function(){

			var txt_coupon = $("#txt_coupon").val();

			if ( txt_coupon != "" ) {
				$.ajax({
					type: "POST",
					url: module_dir+"/ajaxProgressiveDiscounts.php",
					data: {
						action: "search_coupon",
						search: txt_coupon
					},
					success: function(item) {
						if ( item != 0 && item != "" ) {
							$("#txt_coupon").val("");
							$("#alert_coupon").css("display","none");
							$("#list_cart_rules").css("display","block");

							var coupon = jQuery.parseJSON(item);
							var arrayCoupon = $.map(coupon, function(el) { return el; });
							flag = counterCoupon++;

							array_list_cart_rules["id_"+coupon.id_cart_rule+"_"+flag] = arrayCoupon;

							row = "<tr id='line_"+coupon.id_cart_rule+"_"+flag+"'>";

							var scale = document.getElementById("rows_cart_rules").rows.length;

							row += "<td id='scale_"+coupon.id_cart_rule+"_"+flag+"'>"+scale+"</td>";
							row += "<td>"+coupon.id_cart_rule+"</td>";
							row += "<td>"+coupon.name+"</td>";
							row += "<td>"+parseInt(coupon.reduction_percent)+"%</td>";
							row += "<td>$"+parseInt(coupon.reduction_amount)+"</td>";

							if ( coupon.reduction_product != 0 )  {
								row += "<td>"+coupon.reduction_product+"</td>";
							} else {
								row += "<td><img src='"+module_dir+"/icon/disabled.gif' title='Inactivo'></td>";
							}

							if ( coupon.free_shipping == 1 ) {
								row += "<td><img src='"+module_dir+"/icon/enabled.gif' title='Activo'></td>";
							} else {
								row += "<td><img src='"+module_dir+"/icon/disabled.gif' title='Inactivo'></td>";
							}

							if ( coupon.gift_product != 0 ) {
								row += "<td>"+coupon.gift_product+"</td>";
							} else {
								row += "<td><img src='"+module_dir+"/icon/disabled.gif' title='Inactivo'></td>";
							}

							row += '<td><img id="button_remove_coupon" onclick="removeCoupon(\''+coupon.id_cart_rule+'_'+flag+'\');" src="'+module_dir+'/icon/delete.gif" title="Quitar"></td>';

							row += "</tr>";

							$("#header_table_coupon").after(row);

						} else {
							$("#alert_coupon").css("display","block");	
						}
					}
				});
			}
		}
	);

	// products
	$("#button_search_product").click(
		function(){

			var txt_product = $("#txt_product").val();

			if ( txt_product != "" ) {
				$.ajax({
					type: "POST",
					url: module_dir+"/ajaxProgressiveDiscounts.php",
					data: {
						action: "search_product",
						search: txt_product
					},
					success: function(item) {
						if ( item != 0 && item != 1 && item != "" ) {
							$("#txt_product").val("");
							$("#alert_product").css("display","none");
							$("#alert_productExistPD").css("display","none");
							$("#alert_productExistList").css("display","none");
							$("#list_products").css("display","block");

							var product = jQuery.parseJSON(item);
							var arrayProduct = $.map(product, function(el) { return el; });

							// se valida si ya existe el producto en el listado
							var validateExistProductList = false;
							for (var key_id in array_list_products) {
								if (key_id == ("id_"+product.reference) ) {
									validateExistProductList = true;
								}
							}

							if ( !validateExistProductList ) {
								array_list_products["id_"+product.reference] = arrayProduct;

								row = "<tr id='line_"+product.reference+"'>";
								row += "<td>"+product.id_product+"</td>";
								row += "<td>"+product.reference+"</td>";
								row += "<td>"+product.name+"</td>";
								row += "<td>"+parseInt(product.tax)+"%</td>";
								row += "<td>$"+product.price+"</td>";
								row += "<td>$"+product.price_tax+"</td>";

								row += '<td><img id="button_remove_product" onclick="removeProduct(\''+product.reference+'\');" src="'+module_dir+'/icon/delete.gif" title="Quitar"></td>';

								row += "</tr>";

								$("#header_table_product").after(row);
							} else {
								$("#alert_productExistList").css("display","block");
							}

						} else {
							if ( item == 1) {
								$("#alert_productExistPD").css("display","block");
							} else {
								$("#alert_product").css("display","block");
							}
						}
					}
				});
			}
		}
	);

	$("#button_save").click(

		function(){
			var txt_name = $("#txt_name").val();
			var txt_description = $("#txt_description").val();
			var txt_frequency = $("#txt_frequency").val();
			var txt_periods = $("#txt_periods").val();
			var txt_limit_shopping_customer = $("#txt_limit_shopping_customer").val();
			var txt_reset = $("#txt_reset").val();
			var txt_cycles = $("#txt_cycles").val();
			var chk_state = document.getElementById('chk_state').checked;
			var chk_states_orders = "";
			$('input:checkbox[name=chk_state_order]:checked').each(function(){
				chk_states_orders += $(this).val()+",";
			});
			chk_states_orders = chk_states_orders.substring(0, chk_states_orders.length-1);

			var errors = "";
			var countererror = 0;

			if ( txt_name == "" || txt_description == "" || txt_frequency == "" || txt_periods == "" || txt_limit_shopping_customer == "" || txt_reset == "" || txt_cycles == "" || chk_states_orders == "" ) {
				errors += (countererror+=1)+". Faltan campos por llenar.<br>";
			}

			if ( $.isEmptyObject(array_list_cart_rules) ) {
				errors += (countererror+=1)+". El listado de cupones no puede estar vacio.<br>";
			}

			if ( $.isEmptyObject(array_list_products) ) {
				errors += (countererror+=1)+". El listado de productos no puede estar vacio.<br>";
			}

			if ( errors != "" ){
				$("#block_error").html("<strong>Se encontraron los siguientes errores:</strong> <br>"+errors);
				$("#block_error").css("display","block");
			} else {
				$("#block_error").html("");
				$("#block_error").css("display","none");

				string_list_cart_rules = ArrayToString('cart_rules');
				string_list_products = ArrayToString('products');

				$.ajax({
					type: "POST",
					url: module_dir+"/ajaxProgressiveDiscounts.php",
					data: {
						action: "add_new_progressive_discount",
						name : txt_name,
						description : txt_description,
						frequency : txt_frequency,
						periods : txt_periods,
						limit_shopping_customer : txt_limit_shopping_customer,
						reset : txt_reset,
						cycles : txt_cycles,
						state : chk_state,
						states_orders : chk_states_orders,
						list_cart_rules : string_list_cart_rules,
						list_products : string_list_products
					},
					success: function(response) {

						if ( response == "true" ) {
							alert("Se ha creado exitosamente el descuento progresivo.");
						} else {
							alert("Error almacenando el descuento progresivo. Por favor intentelo nuevamente.");
						}
						
						var home_page_module = $("#txt_home_page_module").val();
						location.href = home_page_module;
					}
				});
			}
		}
	);

});

// valida que en el input solo se ingresen numeros
function validateNumbers(e) {
	var keynum = window.event ? window.event.keyCode : e.which;
	if ((keynum == 8) || (keynum == 46)){
		return true;
	}
	
	return /\d/.test(String.fromCharCode(keynum));
}

// convertir array en string
function ArrayToString(convert) {
	var string = "";

	if ( convert == "products" ) {
		for (key in array_list_products) {
			string += array_list_products[key].toString();
			string += "///";
		}
	}

	if ( convert == "cart_rules" ) {
		for (key in array_list_cart_rules) {
			string += array_list_cart_rules[key].toString();
			string += "///";
		}
	}

	return string.slice( 0, (string.length-3) );
}

// remover cupones de la lista de descuento progresivo
function removeCoupon(id_cart_rule) {
	for (var key_id in array_list_cart_rules) {

		if (key_id == ("id_"+id_cart_rule) ) {

			delete array_list_cart_rules["id_"+id_cart_rule];
			$("#line_"+id_cart_rule).remove();

			if ( $.isEmptyObject(array_list_cart_rules) ) {
				$("#list_cart_rules").css("display","none");
			}

		}	
	}

	// re organizar contadores de la escala en la lista de cupones
	var counterscale = 1;
	for (var key_id in array_list_cart_rules) {
		$("#scale_"+key_id.slice(3, 100)).html(counterscale++);
	}
}

// remover cupones de la lista de descuento progresivo
function removeProduct(reference_product) {
	for (var key_id in array_list_products) {

		if (key_id == ("id_"+reference_product) ) {

			delete array_list_products["id_"+reference_product];
			$("#line_"+reference_product).remove();

			if ( $.isEmptyObject(array_list_products) ) {
				$("#list_products").css("display","none");
			}

		}	
	}
}

// ver detalles del descuento progresivo
$(".fancybox").fancybox();
function viewDetailProgressiveDiscount(id_progressive_discount) {

	var module_dir = $("#module_template_dir").val();

	$.ajax({
		type: "POST",
		url: module_dir+"/ajaxProgressiveDiscounts.php",
		data: {
			action: "view_detail_progressive_discount",
			idProgressiveDiscount : id_progressive_discount
		},
		success: function(html) {
			jQuery('#detailProgresiveDiscount').html(html);
		}
	});
}

function changeStatus(id_progressive_discount, state) {

	var module_dir = $("#module_template_dir").val();

	if (confirm('Confirma que desea inactivar el descuento progresivo con ID: '+id_progressive_discount+'?. Una vez inactivo el descuento progresivo no se podra activar nuevamente.')) {
		$.ajax({
			type: "POST",
			url: module_dir+"/ajaxProgressiveDiscounts.php",
			data: {
				action: "changeStatus",
				idprogressivediscount : id_progressive_discount,
				newstate : state
			},
			success: function(response) {
				if ( response == "true" ) {
					var home_page_module = $("#txt_home_page_module").val();
					alert("Se ha inactivado el descuento progresivo exitosamente.");
					location.href = home_page_module;
				} else {
					alert("Error cambiando el estado del descuento progresivo. Intente de nuevo.");
				}
			}
		});
	}
}