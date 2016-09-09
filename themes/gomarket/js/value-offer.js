

// Autor: Faber Herrera
// Descripción: Se encarga de animar los numeros de oferta de valor
//              y agregar el caracter "+" y la puntuación de los
//              números.


$( document ).ready(function() {

function addCommas(n){
    var rx=  /(\d+)(\d{3})/;
    return String(n).replace(/^\d+/, function(w){
        while(rx.test(w)){
            w= w.replace(rx, '$1.$2');
        }
        return w;
    });
}

    $('.number-exp').each(function () {
        var typeNum = $(this).data("typeNum");
        $(this).prop('Counter',0).animate({
            Counter: $(this).text()
        }, {
            duration: 3000,
            easing: 'swing',
            step: function (now) {
            	if (typeNum == "int"){
            		var Num = $(this).text()
            		$(this).text("+"+addCommas(Math.round(now)));
            	}
                
            	if (typeNum == "float"){
            		$(this).text(Math.round(now * 10) / 10);
            	}
            }
        });
    });
});