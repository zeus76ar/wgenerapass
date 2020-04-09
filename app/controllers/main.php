<?php
// funciones
function validarGenerar()
{
	$retorno = '';

	//validaciones
	if ($_POST["longitud"] == '')
	{
		$retorno = 'Falta la Longitud.';
	}
	elseif (!isset($_POST["numeros"]) && !isset($_POST["letras"]))
	{
		$retorno = 'Falta la opcion Numeros o Letras.';
	}

	return $retorno;
}

function validarDescargar()
{
	$retorno = '';

	//validaciones
	if ($_POST["numeros_gen"] === '')
	{
		$retorno = 'Falta el Listado Generado.';
	}

	return $retorno;
}

function generar()
{
	include str_replace('controllers', '', dirname(__FILE__)) . 
	'/lib/class/numero.class.php';
		
	$on = new Numero();
	
	$retorno = array();
	$retorno['error'] = '';
	$retorno['info'] = '';
	$retorno['dato'] = '';
		
	$retorno['error'] = validarGenerar();
	
	//
	if ($retorno['error'] == '')
	{
		// numeros: 48 a 57
		// letras minusculas: 97 a 122
		// simbolos: 35, 42, 43, 45 (#, *, +, -)

		$rangos = array();
		$rangos['numeros']['min'] = 48;
		$rangos['numeros']['max'] = 57;

		$rangos['letras']['min'] = 97;
		$rangos['letras']['max'] = 122;

		$rangos['simbolos']['min'] = 0;
		$rangos['simbolos']['max'] = 3;
			
		$cod_simbolos = array();
		$cod_simbolos[0] = 35;
		$cod_simbolos[1] = 42;
		$cod_simbolos[2] = 43;
		$cod_simbolos[3] = 45;

		$tipos_limite = 5;
		$tipos_min = 1;
		$tipos_max = $tipos_limite;
		
		$tipos_a = $tipos_b = '';
		
		if (isset($_POST["numeros"]))
		{
			$agregar = 0;
			$tipos_a = 'numeros';

			if (isset($_POST["letras"])){
				$agregar = $tipos_limite;
				$tipos_b = 'letras';
				
				if (isset($_POST["simbolos"]))
				{
					$agregar = $tipos_limite * 2;
				}
			}
			elseif (isset($_POST["simbolos"]))
			{
				$agregar = $tipos_limite;
				$tipos_b = 'simbolos';
			}
		}
		elseif (isset($_POST["letras"]))
		{
			$agregar = 0;
			$tipos_a = 'letras';

			if (isset($_POST["simbolos"]))
			{
				$agregar = $tipos_limite;
				$tipos_b = 'simbolos';
			}
		}
		
		$tipos_max += $agregar;

		$cont_numeros = $cont_letras = $cont_simbolos = 0;

		for ($i = 1; $i <= $_POST["longitud"]; $i++)
		{
			//genero que tipo de codigo obtengo
			$aleatorio = $on->numeroAleatorio($tipos_min, $tipos_max);

			if ($aleatorio <= $tipos_limite)
			{
				if ($tipos_a == 'numeros')
				{
					$aleatorio = $on->numeroAleatorio($rangos['numeros']['min'],
					$rangos['numeros']['max']);

					$cont_numeros++;
				}
				else
				{
					$aleatorio = $on->numeroAleatorio($rangos['letras']['min'],
					$rangos['letras']['max']);

					$cont_letras++;
				}
			}
			elseif (($aleatorio > $tipos_limite) &&
			($aleatorio <= ($tipos_limite * 2)))
			{
				if ($tipos_b == 'letras')
				{
					$aleatorio = $on->numeroAleatorio($rangos['letras']['min'],
					$rangos['letras']['max']);

					$cont_letras++;
				}
				else
				{
					$aleatorio = $on->numeroAleatorio($rangos['simbolos']['min'],
					$rangos['simbolos']['max']);

					$aleatorio = $cod_simbolos[$aleatorio];
					$cont_simbolos++;
				}
			}
			else
			{
				$aleatorio = $on->numeroAleatorio($rangos['simbolos']['min'],
				$rangos['simbolos']['max']);

				$aleatorio = $cod_simbolos[$aleatorio];
				$cont_simbolos++;
			}//fin if
				
			//reviso si se han generado al menos un codigo
			//de cada opcion elegida
			if ($i == $_POST["longitud"])
			{
				if (isset($_POST["numeros"]) && ($cont_numeros == 0))
				{
					$aleatorio = $on->numeroAleatorio($rangos['numeros']['min'],
					$rangos['numeros']['max']);
				}
				
				if (isset($_POST["letras"]) && ($cont_letras == 0))
				{
					$aleatorio = $on->numeroAleatorio($rangos['letras']['min'],
					$rangos['letras']['max']);
				}

				if (isset($_POST["simbolos"]) && ($cont_simbolos == 0))
				{
					$aleatorio = $on->numeroAleatorio($rangos['simbolos']['min'],
					$rangos['simbolos']['max']);

					$aleatorio = $cod_simbolos[$aleatorio];
				}
			}
				
			$retorno['dato'] .= sprintf('%c', $aleatorio);
		}//fin for
	}//fin if
		
	echo json_encode($retorno);
}

function descargar()
{
	$error = validarDescargar();
	
	if ($error === '')
	{
		$arch_contenido = str_replace('<p>', '', $_POST['numeros_gen']);
		$arch_contenido = str_replace('</p>', (chr(13).chr(10)), $arch_contenido);
		
		$arch_contenido = 'Generado por wGeneraPass' . 
		(chr(13).chr(10)) . $arch_contenido;
		
		$arch_nombre = ('passwords-' . date('ymd') . '.txt');

		// Definir headers
		header('Content-Type: application/force-download');
		header('Content-Disposition: attachment; filename=' . $arch_nombre);
		header('Content-Transfer-Encoding: binary');
		header('Content-Length: ' . mb_strlen($arch_contenido));

		echo $arch_contenido;
	}
	else 
	{
		echo $error;
	}
}

// main
foreach ($_POST as $i => $valor)
{
	$_POST[$i] = trim($valor);
}

if (isset($_GET['generar']))
{
	generar();
}
elseif (isset($_GET['descargar']))
{
	descargar();
}
