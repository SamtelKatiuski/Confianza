<?php
	
class Request
{
	// Atributos privados de la clase Request
	private $_controlador;
	private $_metodo;
	private $_argumentos;
	
	private $_newBaseUrl;

	//Método constructor
	public function __construct()
	{
		//Verificar la existencia de la ruta a llamar
		if(isset($_GET['url']))
		{			
			//echo htmlspecialchars($_GET["id"]) . '!';
			// Filtro de la url para linpiarla de caracteres no deseados
			$url = filter_input(INPUT_GET, "url", FILTER_SANITIZE_URL);

			// Separacion de la url mediante el caracter /
			$url = explode("/", $url);

			//Se almacena en un array cada valor separado
			$url = array_filter($url);

			// Asignacion del nombre del controlador
			// removiendo del array de la url su primer valor
			$this->_controlador = array_shift($url);

			// Asignacion del nombre del metodo del controlador
			// removiendo del array de la url el primer valor restante
			$this->_metodo = array_shift($url);

			// Asignacion de los argumentos que recibe el metodo del controlador
			// removiendo del array de la url el ultimo y unico valor restante
			$this->_argumentos = $url;
		}

		// Verificacion de la existencia del controlador
		if(!$this->_controlador)
		{
			// En caso de no existir, el valor del controlador
			// será el controlador por defecto
			$this->_controlador = DEFAULT_CONTROLLER;
		}
		// Verificacion de la existencia del metodo del controlador
		if(!$this->_metodo)
		{
			// En caso de no existir, el valor del metodo
			// será el index
			$this->_metodo = 'index';
		}

		// Verificacion de la existencia de los argumentos
		// que recibirá el metodo del controlador
		if(!isset($this->_argumentos)){
			// En caso de no existir se envia un arreglo vacio
			$this->_argumentos = array();
		}
	}

	//Retorno del controlador
	public function getControlador(){
		return $this->_controlador;

	}

	//Retorno del metodo del controlador
	public function getMetodo(){
		return $this->_metodo;		
	}

	//Retorno de los argumentos
	public function getArgs(){
		return $this->_argumentos;		
	}	
}