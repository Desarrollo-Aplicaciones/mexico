$( document ).ready(function() {
	//Animación de los Acordeones del contenedor gris en responsive.
    $( '.ctn-toggle-display' ).click(function() {
	  	if( $( '#' + $( this ).attr( 'id' ) + '-display' ).is( ":hidden" ) ){
	 		$( this ).find( ".btn-display" ).css( "transform", "rotate(45deg)" );
	 	}
	 	else {
	  		$( this ).find( ".btn-display" ).css( "transform", "rotate(0deg)" );
	 	}
	  	$( '#' + $( this ).attr( 'id' ) + '-display' ).slideToggle("slow");
	});
	
	//Animación btn Más - menos del footer
	$( '#btn-display-footer' ).click(function(){
		if($( '#ctn-footer-display' ).is( ":hidden" )) {
	 		$( '#btn-footer-plus').css( "transform", "rotate(45deg)" );
	 		$( '#ctn-gray-end-footer' ).css( {marginTop: '20px'} );
	  		$( '.sep-horizontal-z1' ).css( {marginBottom: '5px'} );
	  		$("html, body").animate({ scrollTop: $(document).height() }, 1000);
	  		$( '#ctn-footer-display' ).slideToggle("slow");
	  	}
	  	else {
	  		$( '#btn-footer-plus' ).css( "transform", "rotate(0deg)" );
	  		$( '#ctn-gray-end-footer' ).css( {marginTop: '-36px'} );
	  		$( '.sep-horizontal-z1' ).css( {marginBottom: '0px'} );
	  		$( '#ctn-footer-display' ).slideToggle("slow");
	  	}
	});

	//Animación del inputo y boton suscribir del footer.
	$( '#btn-email-newsletter' ).click(function(){
		$( this ).addClass( "m-suscribir" ).prop("disasbled", true);
		var email = $( '#email-newsletter' ).val();


		$.post( baseDir + "ajax_newsletter.php", { mail: email })
			.done(function( data ) {
				if (data == "ok"){
	    			$( '#btn-email-newsletter' ).css( { width: "100%" } );
					$( '#email-newsletter' ).css( { width: "0px", padding: "0px" } );
					$( '#email-error' ).fadeOut("slow");
					setTimeout(function(){
						$( '#btn-email-newsletter' ).removeClass( "m-suscribir" ).text("¡Gracias por suscribirte!").prop("disabled", true);
					}, 500);
	    		}
	    		else {
	    			if (data == "error5"){
	    				$( '#email-error' ).fadeIn("slow").text("Error: Por favor ingrese un correo.");
	    			}
	    			else if (data == "error2"){
	    				$( '#email-error' ).fadeIn("slow").text("Error: Por favor ingrese un correo valido.");
	    			}
					setTimeout(function(){
						$( '#btn-email-newsletter' ).removeClass( "m-suscribir" ).prop("disabled", false);
					}, 1000);
	    		}
			});
	});
});