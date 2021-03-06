function prepararIdParaJquery(id_objeto)
{
    if (id_objeto.substring(0,1)!= '#') id_objeto = '#'+id_objeto;
    
    return id_objeto;
}

function enviarDatos(id_form, nom_funcion)
{
    id_form = prepararIdParaJquery(id_form);
	
    var url = $(id_form).attr("action");
    
    NProgress.start();
    
    $.post(url, $(id_form).serializeArray(), function(data){
        NProgress.done();
		
		nom_funcion(data);
	});
}

function mostrarVentanaDescarga(url)
{
    var opciones='';
    
    window.open(url, 'vDescarga', opciones);
}