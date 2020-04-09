<?php
/*
version: 18.05.14
Autor: Ariel Balmaceda.
Compatible con PHP 5.
*/
 
class Numero{
    protected $error;//tipo string
    
    //para convertir numero a letra
    protected $limite_enteros;//longitud maxima parte entera
    protected $limite_decimales;//longitud maxima parte decimal
    protected $mayor_quince;//para 15 a 19
    protected $veinte;//para 20 a 29
    protected $resto_cent;// para 100 a 499
    protected $quinientos;//para 500
    protected $setecientos;//para 700
    protected $novecientos;//para 900
    protected $miles;//para >1000
    protected $unidadesp;
    protected $millones;//para >1000000
    protected $unidades;//para 0-9
    protected $pri_decena;//para 10-15
    protected $resto_dec;//para 30-90
    
    function __construct()
    {
        $this->error='';
        
        $this->limite_enteros = 9;
        $this->limite_decimales = 2;
        $this->mayor_quince = "dieci";
        $this->veinte = "veintei" ;
        $this->resto_cent = "cientos";
        $this->quinientos = "quinientos";
        $this->setecientos = "sete";
        $this->novecientos = "nove";
        $this->miles = "mil";
        $this->unidadesp = "un";
        $this->millones = "millones";
        $this->unidades = array();
        $this->pri_decena = array();
        $this->resto_dec= array();
    }

    // metodos privados
    protected function _verificarNumero($numero, $tipo)
    {
        // $numero - ejemplos
        // formato 'es'   - formato 'am'
        // 12.120,99        12,120.99
        // 130              130
        // 2.546            2,546
        //
        if ($tipo == 'es')
        {
            $numero = str_replace('.', '', $numero);
            $numero = str_replace(',', '.', $numero);
        }
        else
        {
            $numero = str_replace(',', '', $numero);
        }

        $strnumero = number_format($numero, 2, ".", "");
        $this->error = "";
        
        if (trim($strnumero) == "")
        {
            $this->error = "Falta el numero a procesar !!";
            return;
        }
        
        if (!is_numeric($strnumero))
        {
            $this->error = "El parametro pasado NO es nu numero !!";
            return;
        }
        
        $partes = explode('.', $strnumero);

        if (strlen($partes[0]) > $this->limite_enteros)
        {
            $this->error = "La parte entera NO puede ser mayor a " . 
            $this->limite_enteros . " cifras !!";
            return;
        }
        /*
        if (strlen($partes[1]) > $this->limite_decimales)
        {
            $this->error = "La parte decimal NO puede ser mayor a " . 
            $this->limite_decimales . " cifras !!";
            return;
        }
        */
        return $strnumero;
    }
    
    protected function _cargarTextos()
    {
        //Cargo las unidades
        $this->unidades[0] = "cero";;
        $this->unidades[1] = "uno";
        $this->unidades[2] = "dos";
        $this->unidades[3] = "tres";
        $this->unidades[4] = "cuatro";
        $this->unidades[5] = "cinco";
        $this->unidades[6] = "seis";
        $this->unidades[7] = "siete";
        $this->unidades[8] = "ocho";
        $this->unidades[9] = "nueve";
        //Cargo la Primera Decena
        $this->pri_decena[10] = "diez";
        $this->pri_decena[11] = "once";
        $this->pri_decena[12] = "doce";
        $this->pri_decena[13] = "trece";
        $this->pri_decena[14] = "catorce";
        $this->pri_decena[15] = "quince";
        //Cargo el resto de las decenas
        $this->resto_dec[3] = "treinta y";
        $this->resto_dec[4] = "cuarenta y";
        $this->resto_dec[5] = "cincuenta y";
        $this->resto_dec[6] = "sesenta y";
        $this->resto_dec[7] = "setenta y";
        $this->resto_dec[8] = "ochenta y";
        $this->resto_dec[9] = "noventa y";
    }
  
    protected function _convertirUnidades($num)
    {
        return $this->unidades[($num * 1)];
    }
 
    protected function _convertirDecenas($num)
    {
        $retorno = "";
        $auxi = "";
        
        if ($this->numeroEntre($num, 10, 15)) $retorno = $this->pri_decena[($num*1)];
        
        if ($this->numeroEntre($num, 16, 19)) $retorno = ($this->mayor_quince.$this->_convertirUnidades(substr($num, -1)));
        
        if ($this->numeroEntre($num, 20, 29))
        {
            if ($num == 20)
            {
                $retorno = substr($this->veinte, 0, (strlen($this->veinte) - 1));
            }
            else
            {
                $retorno = (substr($this->veinte, 0, (strlen($this->veinte) - 2)).substr($this->veinte, -1).$this->_convertirUnidades(substr($num, -1)));
            }
        }

        if ($this->numeroEntre($num, 30, 99))
        {
            $auxi = $this->resto_dec[(substr($num, 0, 1)*1)];
            
            switch (substr($num, -1))
            {
                case "0": $retorno = substr($auxi, 0, (strlen($auxi) - 2)); break;
                default: $retorno = ($auxi." ".$this->unidades[(substr($num, -1)*1)]);
            }
        }
        
        return $retorno;
    }

    protected function _convertirCentenas($num)
    {
        $retorno = "";
        
        switch (substr($num, 0, 1))
        {
            case "1"://100 a 199
                if ($num == 100)
                {
                    $retorno = substr($this->resto_cent, 0, 4);
                }
                else
                {
                    $retorno = substr($this->resto_cent, 0, 6) . " ";
                }
                break;
            case "5"://500 a 599
                $retorno = $this->quinientos . " ";
                break;
            case "7"://700 To 799
                $retorno = ($this->setecientos . $this->resto_cent . " ");
                break;
            case "9"://900 To 999
                $retorno = ($this->novecientos . $this->resto_cent . " ");
                break;
            default://200 To 499, 600 To 699, 800 To 899
                $retorno = $this->_convertirUnidades(substr($num, 0, 1)) . 
                $this->resto_cent . " ";
        }
        
        return $retorno;
    }

    protected function _convertirUnidMil($num)
    {
        $retorno = "";
        
        switch (substr($num, 0, 1))
        {
            case "1"://1000 a 1999
               //$retorno = ($this->unidadesp . " " . $this->miles . " ");
               $retorno = ($this->miles . " ");
            break;
            default://2000 a 9999
               $retorno = $this->_convertirUnidades(substr($num, 0, 1)) . 
               " " . $this->miles . " ";
        }
        
        return $retorno;
    }

    protected function _convertirDecMil($num)
    {
        $retorno = "";
        
        $retorno = $this->_convertirDecenas(substr($num, 0, 2));
        //Aqui reviso los casos para 21,31,...,91 que para los miles cambia (ej: 21.000 - veintiun mil, no veintiuno mil)
        if (substr(substr($num, 0, 2), -1) == "1")
        {
            if ((substr($num, 0, 2) * 1) > 11) $retorno = (substr($retorno, 0, (strlen($retorno) - 1)));
        }
        
        $retorno .= (" " . $this->miles . " ");
        
        return $retorno;
    }

    protected function _convertirCentMil($num)
    {
        $retorno = "";
        $retorno = trim($this->_convertirCentenas(substr($num, 0, 3)));
        
        if (substr(substr($num, 0, 3), -2) == "00") $retorno .= (" " . $this->miles);
        
        return $retorno . " ";
    }
 
    protected function _convertirUnidMillon($num)
    {
        $retorno = "";
        
        if (substr($num,0, 1) == "1")
        {//1000000 to 1999999
           $retorno = ($this->unidadesp . " " . substr($this->millones, 0, 6) . " ");
        }
        else
        {//2000000 to 9999999
           $retorno = ($this->_convertirUnidades(substr($num, 0, 1)) . " " . 
           $this->millones . " ");
        }
        
        return $retorno;
    }

    protected function _convertirDecMillon($num)
    {
        $retorno="";
        
        $retorno = $this->_convertirDecenas(substr($num, 0, 2));
        
        //Aqui reviso los casos para 21,31,...,91 que para los millones cambia (ej: 21.000.000 - veintiun millones, no veintiuno milllones).
        if (substr(substr($num, 0, 2), -1) == "1")
        {
            if ((substr($num, 0, 2) * 1) > 11) $retorno = (substr($retorno, 0,  (strlen($retorno) - 1)));
        }
        
        $retorno .= (" " . $this->millones . " ");
        
        return $retorno;
    }
    
    protected function _convertirCentMillon($num)
    {
        $retorno = "";
         
        $retorno = trim($this->_convertirCentenas(substr($num, 0, 3)));
        
        if (substr(substr($num, 0, 3), -2) == "00") $retorno .= (" " . $this->millones);
        
        return $retorno . " ";
    }

    // metodos publicos
    public function getError()
    {
        return $this->error;
    }
    
    public function numeroEntre($num, $min, $max)
    {
        return (($num >= $min) && ($num <= $max));
    }
    
    public function numeroAleatorio($min, $max)
    {
        return mt_rand($min, $max);
    }
    
    public function contarEntre($min, $max)
    {
        $contador = 0;
        
        for ($i=$min; $i <= $max; $i++)
        {
            $contador++;
        }
        
        return $contador;
    }
    
    // Funciones para convertir un numeroa letra (formato moneda)
    public function escribirNumero($numero, $tipo='es')
    {
        //$numero: numero a escribir
        //$tipo: 'es' - numero en formato español, 'am' - numero en 
        //formato americano (ingles)
        // 
        $strnumero = $this->_verificarNumero($numero, $tipo);
        
        if (trim($this->error) != "") return "";
            
        $this->_cargarTextos();
        $partes = explode(".", $strnumero);
        $tempo = "";
        $retorno = "";
        $ind = 0;
    
        //proceso la parte entera
        while ($ind < strlen($partes[0]))
        {
            $tempo = substr($partes[0], $ind);
            
            switch (strlen($tempo))
            {
                case 1: //Unidades
                    if ((substr($tempo, 0, 1) != "0") || (trim($retorno) == "")) $retorno .= $this->_convertirUnidades($tempo);
                break;
                case 2://Decenas
                    if (($tempo * 1) >= 10)
                    {
                        $retorno .= $this->_convertirDecenas($tempo);
                        $ind++;//ajuste para las decenas
                    }
                break;
                case 3://Centenas
                    if (($tempo * 1) >= 100) $retorno .= $this->_convertirCentenas($tempo);
                break;
                case 4://Unid. de mil
                    if (($tempo * 1) >= 1000) $retorno .= $this->_convertirUnidMil($tempo);
                break;
                case 5://Dec. de mil
                    if (($tempo * 1) >= 10000)
                    {
                        $retorno .= $this->_convertirDecMil($tempo);
                        $ind++;//ajuste para las decenas
                    }
                break;
                case 6://Cent. de mil
                    if (($tempo * 1) >= 100000) $retorno .= $this->_convertirCentMil($tempo);
                break;
                case 7://Unid. de millon
                    if (($tempo * 1) >= 1000000) $retorno .= $this->_convertirUnidMillon($tempo);
                break;
                case 8://Dec. de millon
                    if (($tempo * 1) >= 10000000)
                    {
                        $retorno .= $this->_convertirDecMillon($tempo);
                        $ind++;//ajuste para las decenas
                    }
                break;
                case 9://Cent. de millon
                    $retorno .= $this->_convertirCentMillon($tempo);
            }
            $ind++;
        }
    
        //proceso los decimales
        if (($partes[1] * 1) > 0)
        {
            $retorno .= ", ";
            $ind = 0;
            
            while($ind < strlen($partes[1]))
            {
                $tempo = substr($partes[1], $ind);
                
                switch (strlen($tempo))
                {
                    case 1: //Unidades
                        if ((substr($tempo, 0, 1) != "0") || (trim($retorno) == "")) $retorno .= $this->_convertirUnidades($tempo);
                    break;
                    case 2://Decenas
                        if (($tempo * 1) >= 10)
                        {
                            $retorno .= $this->_convertirDecenas($tempo);
                            $ind++;//ajuste para las decenas
                        }
                    break;
                }

                $ind++;
            }
        }
        
        return trim($retorno);
    }
}//fin clase