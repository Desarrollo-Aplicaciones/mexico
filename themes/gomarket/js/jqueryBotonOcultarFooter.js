$(document).ready(function(){
        $(".btn_ocultar_text_footer").click(function(){
            $(".btn_ocultar_text_footer").fadeOut(400);
            $(".btn_mostrar_text_footer").fadeIn(400);
            $("#contenedorTextoFooter").slideUp(400);
        });
        $(".btn_mostrar_text_footer").click(function(){
            $(".btn_mostrar_text_footer").fadeOut(400);
            $(".btn_ocultar_text_footer").fadeIn(400);
            $("#contenedorTextoFooter").slideDown(400);
            $('html, body').animate({ scrollTop: $('#abajo').offset().top }, 'slow');
        });
    });

