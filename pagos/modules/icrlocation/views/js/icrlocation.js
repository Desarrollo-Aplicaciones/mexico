// Patrón Módulo JS:
// http://j.mp/module-pattern

// Redefinir: $, window, document, undefined.
var icrlocation = (function($, window, document, undefined) {
	// Llama automáticamente todas las funciones en icrlocation.init
	$(document).ready(function() {
		icrlocation.go();
	});
	
	var locPatt = /^([A-Z]{1,2}[0-9]{1,2})\.([0-9]{2})\.(I|D)\.([0-9]{2})\.([A-Z])$/,
		icrPatt = /^\s|([A-Z]{3}[0-9]{3})$/gm;
 
	// Expone el contenido de icrlocation.
	return {
		// icrlocation.go
		go: function() {
			var i, j = this.init;

			for (i in j) {
				// Todo lo que ejecuta en icrlocation.init
				j.hasOwnProperty(i) && j[i]();
			}
		},
		// icrlocation.init
		init: {
			warehouse: function() {
				// Llamado al cargar la página
				// Todavía puede ser llamado de forma individual, a través de:
				// icrlocation.init.warehouse();

				// Obtiene el valor del almacen y lo agrega a el campo de ubicación
				var loc = [];
				$( "#warehouse" ).change(function() {
					$this = $( this );
					$( "#location" ).val(function( index, value ) {
						loc = value.split(".");
						loc[0] = $this.val();
						return loc.join(".");
					});
				});
			},
			form: function() {
				// Llamado al cargar la página
				// Todavía puede ser llamado de forma individual, a través de:
				// icrlocation.init.form();
				
				// Agrega un método de validación personalizado para el formato de la ubicación
				jQuery.validator.addMethod( "location" , function(value, element) {
					// Válida el formato de la ubicación por medio de una expresión regular
					return this.optional( element ) || locPatt.test( value );
				});
				
				// Agrega un método de validación personalizado para el formato de los ICR
				jQuery.validator.addMethod( "icr" , function(value, element) {
					// Válida el formato del ICR por medio de una expresión regular
					if(value.match(icrPatt) !== null)
						return (value.match(icrPatt).length == value.split("\n").length) && icrPatt.test( value );

					return false;
				});

				// Válida el formulario
				$( "#form-icr-location-add" ).validate({
					errorClass: "invalid",
					messages: {
						'icr': {
							required: "Por favor ingrese un ICR.",
							icr: "Por favor ingrese un ICR válido."
						},
						warehouse: "Por favor seleccione un almacén.",
						location: {
							required: "Por favor ingrese una ubicación.",
							location: "Por favor ingrese una ubicación válida."
						}
					},
					submitHandler: function(form) {
						// Vuelve y válida para evitar problemas con los clones						
						form.submit();
					}
				});
			},
			errorIcr: function() {
				try {
					// Agrega los ICRs que fallaron al textarea
					// Depende del mensaje de error
					var icrs = $.map($(".module_error").text().split("|")[1].split(","), $.trim).join('\n');
					if( typeof icrs == "string" )
						$("textarea.icr").text(icrs);
				} catch (excepcion) {}
			}
		}
	};
// Parámetros: jQuery, window, document.
})(jQuery, this, this.document);