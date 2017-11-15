$(document).ready(function() {
  // Cantidad máxima de productos
  var maxFields = "25";
  
  // Agrega un nuevo producto
  $('#add-product').click(function(e) {
    e.preventDefault();
    if($( '.product' ).length < maxFields) {
      $( '.product:last' ).clone(true).hide().insertAfter( '.product:last' ).slideDown();
      calcProductIndex();
    } else {
      alert('Sólo puedes ingresar '+maxFields+' productos, en caso de requerir más favor de enviarnos un email con el listado');
    }
  });

  // Elimina el producto seleccionado
  $('.delete').click(function(e) {
    e.preventDefault();
    $(this).parent('.product').slideUp("normal", function() { 
      $(this).remove();
      calcProductIndex();
    });
  });

  // Calcula los índices del los productos
  function calcProductIndex() {
    $( '.product' ).each(function( index ) {
      var $this = $( this );
      $this.children('select').attr('name', 'products['+ index +'][cod]');
      $this.children('input').attr('name', 'products['+ index +'][qty]');
    });

    if($( '.product' ).length > 1) {
      $( '.delete' ).show();
    } else {
      $( '.delete' ).hide();
    }
  }

  // Envia el formulario
  // The mailto tag should only accept the following parameters:
  // subject=subject text subject of e-mail
  // body=body text
  $( "#formulario form" ).submit(function( event ) {
    // Stop form from submitting normally
    event.preventDefault();

    // Cachea el objeto formulario
    $form = $(this);

    // Oculta todos los mensajes de error
    $(".form-error").hide();

    // Envia los datos
    $.post( $form.attr("action"), $form.serialize())
    .done(function( data ) {
      console.log( data );
      if (data.success) {
        $("#message").html(data.message).show();
      } else {
        // Muetra los errores independiente
        $.each( data.form, function( key, value ) {
          //console.log( value.input + ": " + value.message );
          $("#error-" + value.input).html(value.message).show();
        });
      }
    });
  });
});