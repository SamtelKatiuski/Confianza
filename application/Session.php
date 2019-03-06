<?php

class Session
{
	//Metodo mediante el cual se inicializa la sesion
	public static function init()
	{
		//Iniciar la sesion
		session_start();
	}

	//Metodo encargado de destruir las variables de sesion o la sesion completa
	public static function destroy($key = false)
	{
		//Verifica la existencia de un valor recibido
		if($key)
		{
			//Verifica si el valor recibido es un arreglo
			if(is_array($key))
			{
				//En caso de ser un arreglo, se itera el arreglo
				for($i=0; $i< count($key); $i ++)
				{
					//Verifica la existencia de esa variable de session
					if(isset($_SESSION[$key[$i]]))
					{
						//En caso de su existencia, se destruye
						unset($_SESSION[$key[$i]]);
					}
				}
			}
			else
			{
				//Si no es un arreglo, se verifica la existencia de la variable de session
				if(isset($_SESSION[$key]))
				{
					//En caso de su existencia, se destruye
					unset($_SESSION[$key]);
				}
			}
		}
		else
		{
			//En caso de no recibir un parametro, se destruye la session completa
			session_destroy();
		}
	}

	//Metodo encargado de inicializar o declarar variables de sesion
	//Recibe el nombre de la variable, y su valor
	public static function set($key , $value)
	{
		//Verifica que el nombre de la variable no sea nulo
		if(!empty($key))
		{
			//En caso tal que no sea nula, se declara
			$_SESSION[$key] = $value;			
		}
	}

	//Metodo encargado de retornar el valor de una variable de sesion
	//Recibe el nombre a buscar
	public static function get($key)
	{
		//Verifica la existencia de la variable de session
		//En caso de que exista, se retorna
		if(isset($_SESSION[$key]))
			return $_SESSION[$key];
	}


	//Metodo encargado de los permisos de usuario
	//Recibe el nivel de permiso 
	public static function access($level)
	{
		//Verifiaca que no exista una autenticacion
		if(!Session::get('Mundial_authenticate'))
		{
			//En caso de no existir, se redirecciona al controlador de errores
			header("Location: " . BASE_URL . 'error');
			exit;
		}
		
		//Se verifica que el nivel recibido sea mayor que el nivel del usuario
		if(Session::getLevel($level) > Session::getLevel(Session::get('Mundial_user_rol')))
		{
			//Si es mayor, se redirecciona al controlador de errores			
			header("Location: " . BASE_URL . 'error/access/5656');
			exit;
		}
	}

	//Metodo encargado de los permidos de usuario en las vistas
	//Recibe el nivel de permiso
	public static function access_view($level)
	{
		//En caso de no estar autenticado, retorna falso
		if(!Session::get('Mundial_authenticate'))
		{
			return false;
		}
		
		//Se verifica que el nivel recibido sea mayor que el nivel del usuario
		//De ser asi, se retorna falso
		if(Session::getLevel($level) > Session::getLevel(Session::get('Mundial_user_rol')))
		{
			return false;
		}

		//De lo contrario retorna verdadero
		return true;
	}

	//Metodo encargado de retornar el nivel
	public static function getLevel($level)
	{
		//Se declaran los roles de usuario con sus valores
		$role['Operador Radicador'] = 6;
		$role['Gerente'] = 5;
		$role['Operador Mundial'] = 4;
		$role['Operador Asistemyca'] = 3;
		$role['Consulta Fechas'] = 2;
		$role['Reportes'] = 1;

		//Se verifica si el nivel recibido,no se encuentra en los roles
		if(!array_key_exists($level, $role))
		{
			//De ser así, se informa de un error
			Session::destroy();
			header("Location: " . BASE_URL . "error");	
		}
		else
		{	
			//De lo contrario, se retorna el rol con el nivel recibido
			return $role[$level];
		}
	}


}