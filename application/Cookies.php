<?php

class Cookies
{
    //Método para crear cookies
    public static function set($key , $value)
    {
        //Verifica que el nombre de la variable no sea nulo
        if(!empty($key))
        {
            setcookie($key, $value , 0 , BASE_URL);
        }
    }
    
    //Metodo encargado de retornar el valor de una cookie
	//Recibe el nombre a buscar
    public static function get($key)
    {
        //Verifica la existencia de la cookie
		//En caso de que exista, se retorna
        if(isset($_COOKIE[$key]))        
            return $_COOKIE[$key];        
    }
    
    //Método encargado de desrtuir cookie
    public static function destroy($key)
    {
        //Verifica que no este vacia la variable
        if(!empty($key))
        {
            //Verifica si es un arreglo
            if(is_array($key))
            {
                //Destruye cada cookie en el arreglo
                foreach($key as $k)
                {
                    if(isset($_COOKIE[$k]))
                        unset($_COOKIE[$k]);
                }
            }
            else
            {
                //Si no es arreglo, destruye cookie
                if(isset($_COOKIE[$key]))
                    unset($_COOKIE[$key]);
            }
        }
            
    }
}