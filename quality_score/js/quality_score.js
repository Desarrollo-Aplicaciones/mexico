// tomar data
var get = atob( window.location.search.substr(1) ).split( "," );


// print stars
$(".star").on({
	click: function() {
		$(".star").removeClass("selected").addClass("active");
		$(this).addClass("selected");
	},

	mouseenter: function() {
		$('.star').removeClass("active red yellow");
		var idStar = $( this ).attr('id').split("_")[1];
		for ( i = 1; i <= idStar; i++ ) {
			if ( idStar <= 3 ) {
				$('#star_' + i).addClass("red");
			} else {
				$('#star_' + i).removeClass("red").addClass("yellow");
			}
		}
	},

	mouseleave: function() {
		if ( !$('.star').hasClass("active") ) {
			$(".star.selected").mouseenter().click();
		}
	}
});


// pintar estrella seleccionada en el correo
$('#star_' + get[0]).mouseenter().click();

if ( get[0] <= 3 ) {
	$('#title_survey').html("AYÚDANOS A MEJORAR");
	$('#text_survey').html("Calificaste nuestro servicio con "+get[0]+" estrellas");
} else {
	$('#title_survey').html("¡GRACIAS POR CALIFICAR NUESTRO SERVICIO!");
	$('#text_survey').css("display","none");
}



// href logo
$('#shop').attr("href", window.location.origin);



// boton ir a la tienda
$("#go_to_the_store").click( function() {
	window.location.href = window.location.origin;
});



// boton guardar encuesta
$("#end_survey").click( function() {
	var id_customer = get[1];
	var id_order = get[2];
	var mail_customer = get[3];
	var comments = $("#comment").val();
	var qualification = $(".star.selected").prop("id").split("_")[1];;

	$.ajax({
		type : "post",
		url : "ajax_quality_score.php",
		data : {
			"id_customer" : id_customer,
			"id_order" : id_order,
			"mail_customer" : mail_customer,
			"comments" : comments,
			"qualification" : qualification
		},
		beforeSend: function(ev) {
			//$("#error_survey").remove();
			if ( id_customer == "" || id_order == "" || mail_customer == "" || !qualification ) {
				//$("#stars_quality_score").append("<label id='error_survey'>error</label>");
				ev.abort();
				location.reload();
			}
		},
		success: function(response) {
			window.location.href = response;
		}
	})

});