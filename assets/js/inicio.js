// funciones
function numeroGenerado(data)
{
    var datos, contenido, generados;

    eval('datos=' + data);
    
    if (datos.info != '') alertify.alert(('Info: ' + datos.info));
	
    if (datos.error == ''){
        generados = $('#hgenerados').val();
        
        if (generados.length > 0) generados += ';';
        
        generados += datos.dato.toString();
        
        $('#hgenerados').val(generados);
        
        $('#ultimo_generado').html(datos.dato);
        
        contenido = ('<p>' + datos.dato + '</p>' + $('#lista_generados').html());
        
        $('#lista_generados').html(contenido);

        if ($('#descargar').hasClass('hidden'))
        {
            $('#descargar').removeClass('hidden');
        }
    }else{
        alertify.alert(('Error: ' + datos.error));
    }

    habilitarBotones(true);
}

function validarOpciones()
{
    var retorno=true, texto='';
    
	if (!$('#simbolos').is(':checked') &&
	!$('#numeros').is(':checked') && !$('#letras').is(':checked')) {
		retorno=false;
		texto='Debe seleccionar letras, numeros o simbolos';
        
        alertify.alert(texto);
    }
	
    if ($('#simbolos').is(':checked')) {
		if (!$('#numeros').is(':checked') && !$('#letras').is(':checked')) {
			retorno=false;
			texto='Debe seleccionar tambien letras o numeros';
			
			alertify.alert(texto);
		}
    }
    
    return retorno;
}

function validarLimite()
{
    var generados = $('#hgenerados').val();
    var partes = generados.split(';');
    var retorno = ((partes.length >= limite)?false:true);

    return retorno;
}

function ocultarOpciones()
{
    //muestro el resumen y oculto las opciones
    var texto='<span class="negrita">Longitud:</span> ' + $('#longitud').val() +
    '<br />' + '<span class="negrita">Incluir:</span> ';
    var hay_opcion=false;
    
    if ($('#numeros').is(':checked')){
        texto += 'Numeros';
        hay_opcion=true;
    }
    
    if ($('#letras').is(':checked')){
        if (hay_opcion) texto+=', ';
        
        texto += 'Letras';
        hay_opcion=true;
    }
    
    if ($('#simbolos').is(':checked')){
        if (hay_opcion) texto+=', ';
        
        texto += 'Simbolos';
    }
    
    $('#resumen').html(texto);
    $('#resumen').css('display', 'block');
    
    $('#borde_opciones').css('display', 'none');

    $('#reset').removeClass('hidden');
}

function habilitarBotones(opcion)
{
    var atributo = 'disabled';

    // valor: true - habilitar, false - dehabilitar
    if (opcion)
    {
        $('#generar').removeAttr(atributo);
        $('#reset').removeAttr(atributo);
    }
    else
    {
        $('#generar').attr(atributo, 1);
        $('#reset').attr(atributo, 1);
    }
}

// main
var limite = 20;

$(document).ready(function() {
    $('#frmnumeros').submit(function(event){
        event.preventDefault();
        
        if (! validarLimite())
        {
            var texto = 'Limite de passwords alcanzado: ' + limite;
            
            alertify.alert(texto);
            return;
        }
        
        $('#longitud').val(parseInt($('#longitud').val()));
        
        if (validarOpciones()) 
        {
            ocultarOpciones();
            habilitarBotones(false);
            enviarDatos('frmnumeros', numeroGenerado);
        }  
    });

    $('#reset').click(function(){
        $('#longitud').val(4);
        $('#numeros').attr('checked', false);
        $('#letras').attr('checked', false);
        $('#simbolos').attr('checked', false);
        //$('#hgenerados').val('');
        
        $('#ultimo_generado').html('&nbsp;');
        $('#lista_generados').html('');
        
        //
        $('#resumen').css('display', 'none');
        $('#borde_opciones').css('display', 'block');

        $('#reset').addClass('hidden');
    });

    $('#descargar').click(function() {
        $('#numeros_gen').val($('#lista_generados').html());

        $('#frmgenerados').submit();
    });
});
