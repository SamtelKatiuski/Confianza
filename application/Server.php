<?php

//Clase encargada de interactuar directamente con el servidor y tipos de peticiones
class Server
{
    //Método encargado de obtener el tipo de petición sea POST o GET
    public static function RequestMethod($type_request)
    {
        if ($_SERVER['REQUEST_METHOD'] === $type_request) 
        {
            return true;
        }

        return false;
    }

    //Método encargado de obtener y retornar datos recibidos por método POST
    public static function post($var = false)
    {
        if(self::RequestMethod("POST"))
        {
            if(isset($_POST))
            {
                if($var)
                {
                    if(isset($_POST[$var]))
                    {
                        if(!is_array($_POST[$var]))
                            return trim($_POST[$var]);
                        else
                            return $_POST[$var];
                    }
                }
                else
                {
                    return $_POST;
                }

                return $_POST;
            }
        }
    }
}