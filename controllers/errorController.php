<?php

//Clase encargada del control de errores de la aplicacion
class errorController extends Controller
{
	//Metodo constructor de la clase
	public function __construct()
	{
		//Metodo constructor de la clase padre
		parent::__construct();
	}

	//Metodo encargado de la vista principal del controlador
	public function index()
	{
		//Se envia como parametro a la vista el titulo
		$this->_view->titulo = "Error";
		//Se envia como parametro a la vista el mensaje de error
		$this->_view->message = $this->_getError();

		$this->_view->setJs(array('functions'));

		//Se renderiza la vista index del controlador
		$this->_view->renderizar('index');
	}

	//Metodo encargado del acceso de usuarios
	public function access($code = false)
	{

		//Verifica la existencia de un codifo
		if($code)
		{
			//De ser asi se envia como parametro a la vista el titulo
			$this->_view->titulo = "Error";
			//Se envia a la vista como parametro el mensaje de error del codigo recibido
			$this->_view->message  = $this->_getError($code);
			$this->_view->code     = $code;
			$this->_view->codeType = $this->_getErrorType($code);
			//Se renderiza la vista access del controlador
			$this->_view->setJs(array('functions'));
			$this->_view->renderizar('access');
		}
		else
		{
			//De lo contrario se envia al controlador de errores 
			$this->redireccionar('error');
		}
	}

	//Metodo privado, encargado de recibir el codigo de error
	private function _getError($code = false){
		//Se verifica la existencia de un codigo
		if($code){
			//Se verifica que el codigo sea un entero
			if(is_int($code)){
				//Se asocia el valor del codigo
				$cod = $code;
			}
		}else{
			//De lo contrario el error sera el valor por defecto
			$code = 'default';
		}

		//Se definen los codigos de error con sus respectivos mensajes
		$error['default']= "Ha ocurrido un error y la pagina no puede mostrarse correctamente.";
		$error['5656']   = "No cuenta con la autorización para ingresar al módulo deseado.";
		$error['404']    = "La página que intenta buscar no existe.";

		//Verifica la existencia del codigo dentro del arreglo de errores
		if(array_key_exists($code, $error)){
			//De ser así se retorna el valor del error
			return $error[$code];
		}else{
			//De lo contrario se retorna el error por defecto
			return $error['default'];
		}
	}

	//Metodo privado, encargado de recibir el codigo de error
	private function _getErrorType($code = false){
		//Se verifica la existencia de un codigo
		if($code){
			//Se verifica que el codigo sea un entero
			if(is_int($code)){
				//Se asocia el valor del codigo
				$cod = $code;
			}
		}else{
			//De lo contrario el error sera el valor por defecto
			$code = 'default';
		}

		//Se definen los codigos de error con sus respectivos mensajes
		$error['default']= "";
		$error['5656']   = "Restricted access.";
		$error['404']    = "Not found";

		//Verifica la existencia del codigo dentro del arreglo de errores
		if(array_key_exists($code, $error)){
			//De ser así se retorna el valor del error
			return $error[$code];
		}else{
			//De lo contrario se retorna el error por defecto
			return $error['default'];
		}
	}
}