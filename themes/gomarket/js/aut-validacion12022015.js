/**
 * @author Esteban Rincón Correa
 */
$(document).ready(function() { validarCampos(); });
function validarEmail(campo) {
		email = $('#'+campo).val();
	    expr = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	    if(email.length>0){
		    if ( !expr.test(email) )
			    {
		    		error="Campo requerido.";
		        }
		    else{
		    		$('#'+campo).attr("style", "border-color:#3A9B37");
		    		error="";
		    		$('#error'+campo).html(error);
		    		return true;
			    }
	    	}
	    else {
    		error="Campo requerido.";
        }
	    $('#error'+campo).html(error);
	    if (error){
    		$('#'+campo).attr("style", "border-color:#A5689C;background-color:#FFFAFA");
    	    $('#'+campo).focus();
		    }
		return false;
	}
	function validarContra(campo){
		pass = $('#'+campo).val();
		if(pass.length>0){
		    if (pass.length<5)
			    {
		    		error="Campo requerido.";
		        }
		    else{
		    		$('#'+campo).attr("style", "border-color:#3A9B37");
		    		error="";
		    		$('#error'+campo).html(error);
		    		return true;
			    }
	    	}
	    else {
    		error="Campo requerido.";
        }
	    $('#error'+campo).html(error);
	    if (error){
    		$('#'+campo).attr("style", "border-color:#A5689C;background-color:#FFFAFA");
    		$('#'+campo).focus();
		    }
	    //if(!error){return true;}
	    return false;
	}
	/*function tipoUsuario(tipo)
	{
		myArray = [ "registrado" , "invitado" ];
		myArray.forEach( restore );
		$('#op'+tipo).click();
		$('#rb'+tipo).html('<div class="radioselected"></div>');
		$('#tit'+tipo).removeAttr("class");
		$('#tit'+tipo).attr("class","currentOpcion");
	

	function restore(elem)
	{
		$('#rb'+elem).empty();
		$('#tit'+elem).removeAttr("class");
		$('#tit'+elem).attr("class","opcionCuenta");
	}*/
	function validarTitulo()
	{
		if($('[name=id_gender]').val()){
			error=""
			$('[name=id_gender]').attr("style", "border-color:#3A9B37");
			$('#errortit').html(error);
			return true;
			}
		else{
			error="requerido";
			$('[name=id_gender]').attr("style", "border-color:#A5689C;background-color:#FFFAFA");
			$('#id_gender1').focus();
			}
		$('#errortit').html(error);
		return false;
	}
	function validarConf()
	{
		if (validarContra("conf-passwd") && validarContra("passwdr") && $('#conf-passwd').val() == $('#passwdr').val())
		{
			error = "";
			$('#conf-passwd').attr("style", "border-color:#3A9B37");
			$('#errorconf-passwd').html(error);
			return true;
		}
		error = "Las contraseñas no coinciden.";
		$('#errorconf-passwd').html(error);
		$('#conf-passwd').attr("style", "border-color:#A5689C;background-color:#FFFAFA");
		$('#conf-passwd').focus();
		return false;
	}
	function validarNombre(tipo){
		nombre = $('#'+tipo).val();
		if (nombre.length < 3){
			error = "Campo requerido."
			}
		else{
			letra = nombre.split("");
			ref = "";
			cont = 1;
			for (i = 0; i < letra.length; i++){
				if (isNaN(letra[i]) || letra[i] == " "){
					if (ref == letra[i]){cont++;}
					else{cont = 1;}
					if (cont < 3){
			    		error="";
			    	}
					else{error = "Campo requerido.";i=letra.length;}
					ref = letra[i];
				}
				else{error = "Campo requerido.";i=letra.length;}
			}

		}
	    $('#error'+tipo).html(error);
	    if (error){
    		$('#'+tipo).attr("style", "border-color:#A5689C;background-color:#FFFAFA");
    	    $('#'+tipo).focus();
    	    return false;
		    }
	    else{
	    	$('#'+tipo).attr("style", "border-color:#3A9B37");
    		return true;
	    	}
	}
	function validarSelect(nombre){
		valor = $("#"+nombre).val();
		if (valor){
			error = "";
			$('#error'+nombre).html(error);
    		$('#'+nombre).attr("style", "border-color:#3A9B37");
    	    return true;
			}
		error = "Campo requerido.";
		$('#error'+nombre).html(error);
		$('#'+nombre).attr("style", "border-color:#A5689C;background-color:#FFFAFA");
	    $('#'+nombre).focus();
	    return false;
	}
	function validarDni(nombre){
		valor = $("#"+nombre).val();
		if(valor){
			tipo = $("#id").val();
			letra = valor.split("");
			ref = "";
			cont = 0;
			for (i = 0; i < letra.length; i++){
				if ( letra[i] != " "){
					if (ref == letra[i]){cont++;}
					else{cont = 0;}
					if (cont < 5){
						comp = 0;
						opciones = ['01234','12345','23456','34567','45678','56789','67890','78901','89012','90123',
									'43210','54321','65432','76543','87654','98765','09876','10987','21098','32109'];
						$.each(opciones, function(index, value){res=valor.split(value);
							  if ( res.length > 1 ){comp++;};
						});
						if(comp > 1 ||
							((tipo != 3 && tipo != 2 ) && !((valor > 9999 && valor < 100000000) || (valor > 1000000000 && valor < 4099999999)))
						){
							error = "Campo requerido.";
						}
						else {
							exp = /([a-zA-Z]{4})+([0-9]{6})+([a-zA-Z]{6})+[a-zA-Z0-9]+[0-9]/;
							if (1/*valor.match(exp)*/) {
								error="";
							    } else {
							    	error="Campo requerido";
							    } 
							}
			    	}
					else{error = "Campo requerido.";i=letra.length;}
					ref = letra[i];
				}
				else{error = "Campo requerido.";i=letra.length;}
			}
		}
		else {error = "requerido"}
		$('#error'+nombre).html(error);
	    if (error){
    		$('#'+nombre).attr("style", "border-color:#A5689C;background-color:#FFFAFA");
    	    $('#'+nombre).focus();
    	    return false;
		    }
	    else{
	    	$('#'+nombre).attr("style", "border-color:#3A9B37");
    		return true;
	    	}
	}
	function validarTelefono(nombre){
		valor = $("#"+nombre).val();
		if(valor){
			if (isNaN(valor) || !(valor.length == 10)){
				error = "Campo requerido.";
			}
			else{error="";}
		}
		else {error = "Campo requerido"}
		$('#error'+nombre).html(error);
	    if (error){
    		$('#'+nombre).attr("style", "border-color:#A5689C;background-color:#FFFAFA");
    	    $('#'+nombre).focus();
    	    return false;
		    }
	    else{
	    	$('#'+nombre).attr("style", "border-color:#3A9B37");
    		return true;
	    	}
		}
	function validarDireccion(nombre){
		valor = $("#"+nombre).val();
		if (valor.length < 10){error = "Campo requerido.";}
		else {error = "";}
		$('#error'+nombre).html(error);
		 if (error){
	    		$('#'+nombre).attr("style", "border-color:#A5689C;background-color:#FFFAFA");
	    	    $('#'+nombre).focus();
	    	    return false;
			    }
		    else{
		    	$('#'+nombre).attr("style", "border-color:#3A9B37");
	    		return true;
		    	}
	}
	function validarTOS(check){
		if($("#TOS"+check).is(':checked')) {  
			error = "";
        } else {  
        	$("#TOS"+check).attr("class", "TOSunselected");
        	error = "Campo requerido.";
        } 
		$('#errorTOS'+check).html(error);
		if (error){
    	    $("#TOS"+check).focus();
    	    return false;
		    }
	    else{
	    	$("#TOS"+check).removeAttr("class")
    		return true;
	    	}
	}
	function validarNumero(nombre){
		valor = $("#"+nombre).val();
		if(valor){
			if (isNaN(valor) || !(valor.length == 5)){
				error = "Campo requerido.";
			}
			else{error="";}
		}
		else {error = "Campo requerido"}
		$('#error'+nombre).html(error);
	    if (error){
    		$('#'+nombre).attr("style", "border-color:#A5689C;background-color:#FFFAFA");
    	    $('#'+nombre).focus();
    	    return false;
		    }
	    else{
	    	$('#'+nombre).attr("style", "border-color:#3A9B37");
    		return true;
	    	}
	}
	function validarCampos()
	{
		$('#rbregistrado').click(function() {tipoUsuario("registrado");});
		$('#rbinvitado').click(function() {tipoUsuario("invitado");});
		$('#email').keyup(function() {validarEmail("email");});
		$('#email').change(function() {validarEmail("email");});
		$('#passwd').focus(function() {validarEmail("email");});
		$('#passwd').keyup(function() {validarContra("passwd");});
		$('#TOSlogin').click(function() {validarTOS("login");});
		$('#SubmitLogin').click(function() {
			if(!(validarEmail("email") && validarContra("passwd") && validarTOS("login")))
			return false;
			});
		$('#reg-email').focus(function() {validarTitulo();});
		$('#reg-email').keyup(function() {validarEmail("reg-email");});
		$('#reg-email').change(function() {validarEmail("reg-email");});
		$('#passwdr').focus(function() {validarEmail("reg-email");});
		$('#passwdr').keyup(function() {validarContra("passwdr");});
		$('#conf-passwd').focus(function() {validarContra("passwdr");});
		$('#conf-passwd').keyup(function() {validarConf();});
		$('#customer_firstname').focus(function() {validarConf();});
		$('#customer_firstname').keyup(function() {validarNombre("customer_firstname");});
		$('#customer_firstname').change(function() {validarNombre("customer_firstname");});
		$('#customer_lastname').focus(function() {validarNombre("customer_firstname");});
		$('#customer_lastname').keyup(function() {validarNombre("customer_lastname");});
		$('#customer_lastname').change(function() {validarNombre("customer_lastname");});
		$('#id').focus(function() {validarNombre("customer_lastname");});
		/*$('#id').change(function() {validarSelect("id");});
		$('#dni').focus(function() {validarSelect("id");});
		$('#dni').keyup(function() {validarDni("dni");});
		$('#dni').change(function() {validarDni("dni");});
		$('#days').focus(function() {validarDni("dni");});*/
		$('#days').focus(function() {validarNombre("customer_lastname");});
		$('#days').change(function() {validarSelect("days");});
		$('#months').focus(function() {validarSelect("days");});
		$('#months').change(function() {validarSelect("months");});
		$('#years').focus(function() {validarSelect("months");});
		$('#years').change(function() {validarSelect("years");});
		$('#TOSreg').click(function() {validarTOS("reg");});
		
		$('#submitAccount').click(function() {
			if(!(
					validarTitulo() && validarEmail("reg-email") &&
					validarContra("passwdr") && validarConf() &&
					validarNombre("customer_firstname") && validarNombre("customer_lastname") &&
					/*validarSelect("id") && validarDni("dni") && */
					validarSelect("days") && validarSelect("months") && validarSelect("years") &&
					validarTOS("reg")
			))
			return false;
			});
		
		$('#guest_email').keyup(function() {validarEmail("guest_email");});
		$('#guest_email').change(function() {validarEmail("guest_email");});
		$('#guest_firstname').focus(function() {validarEmail("guest_email");});
		$('#guest_firstname').keyup(function() {validarNombre("guest_firstname");});
		$('#guest_firstname').change(function() {validarNombre("guest_firstname");});
		$('#guest_lastnamei').focus(function() {validarNombre("guest_firstname");});
		$('#guest_lastnamei').keyup(function() {validarNombre("guest_lastnamei");});
		$('#guest_lastnamei').change(function() {validarNombre("guest_lastnamei");});
		/*$('#dni2').focus(function() {validarNombre("guest_lastnamei");});
		$('#dni2').focus(function() {$('#id').val("1");});
		$('#dni2').keyup(function() {validarDni("dni2");});
		$('#dni2').change(function() {validarDni("dni2");});
		$('#phone').focus(function() {validarDni("dni2");});*/
		$('#phone').focus(function() {validarNombre("guest_lastnamei");});
		$('#phone').keyup(function() {validarTelefono("phone");});
		$('#phone').change(function() {validarTelefono("phone");});
		$('#address1').focus(function() {validarTelefono("phone");});
		$('#address1').keyup(function() {validarDireccion("address1");});
		$('#address1').change(function() {validarDireccion("address1");});
		$('#postcode').focus(function() {validarDireccion("address1");});
		$('#estado').focus(function() {validarNumero("postcode");});
		$('#estado').change(function() {validarSelect("estado");});
		$('#ciudad').focus(function() {validarSelect("estado");});
		$('#ciudad').change(function() {validarSelect("ciudad");});
		$('#id_colonia').focus(function() {validarSelect("ciudad");});
		$('#id_colonia').change(function() {validarSelect("id_colonia");});
		$('#TOSquest').click(function() {validarTOS("quest");});
		
		$('#submitGuest').click(function() {
			$('#id').val("1");
			if(!(
					validarEmail("guest_email") && validarNombre("guest_firstname") &&
					validarNombre("guest_lastnamei") && /*validarDni("dni2") &&*/
					validarTelefono("phone") && validarDireccion("address1") &&
					validarSelect("estado") && validarSelect("ciudad") &&
					validarSelect("id_colonia") && validarNumero("postcode") &&
					validarTOS("quest")
			))
			return false;
			});
	}