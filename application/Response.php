<?php	


class Response
{
	// Metodo principal donde se recibe una peticion para procesarla
	// recibe como parametro un objeto de la clase Request
	public static function run(Request $peticion)
	{
		// Se obtiene el controlador desde el objeto de la clase Request
		// añadiendo al final la cadena 'Controller'
		$controller = $peticion->getControlador(). "Controller";

		//Busca el controlador en el directorio donde se almacenan
		$rutaControlador = ROOT. "controllers". DIR_SEP . $controller . ".php";

		//Se obtiene el metodo del controlador desde la peticion
		$metodo = $peticion->getMetodo();

		//Se obtienen los argumentos o parametros
		// del metodo del controlador desde la peticion
		$args = $peticion->getArgs();

		// Se verifica si existe y es legible el controlador
		if(is_readable($rutaControlador))
		{
			//En caso de que exista, se requiere el archivo
			require_once $rutaControlador;

			//Se instancia el controlador
			$controller = new $controller;

			//Se verifica si existe y es legible el metodo del controlador
			if(is_callable(array($controller, $metodo)))
			{
				//En caso de que exista, se busca el metodo desde la peticion
				$metodo = $peticion->getMetodo();
			}
			else
			{
				//De lo contrario el metodo es index
				$metodo = 'index';
			}

			//Se verifica la existencia de los parametros
			if(isset($args))
			{
				//En caso de que existan, se llama la ruta completa
				// con el controlador, el metodo, y los argumentos o parametros
				call_user_func_array(array($controller, $metodo), $args);
			}
			else
			{
				//En caso de que no existan, se envia solo el controlador y el metodo
				call_user_func(array($controller, $metodo));
			}
		}
		else 
		{

			//En caso de que no exista el controlador se 
			//redirecciona al controlador de errores
			header("Location: error/access/404");
			
		}
	}
}
