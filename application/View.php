<?php

class View
{
	//Argumentos privados de la clase
	//Solo los podran acceder sus hijos
	private $_controlador;
	private $_js;
	
	//Metodo constructor de la calse, recibe un objeto de la clase Request
	public function __construct(Request $peticion)
	{
		//Se obtiene el controlador desde la peticion realizada
		$this->_controlador = $peticion->getControlador();
		//Se crea un array que almacenara los archivos de javascript
		$this->_js = array();
	}


	//Metodo encargado de renderizar la vista relacionada con el controlador	
	public function renderizar($vista, $item = false)
	{	
		//Incluye archivo de menu
		require_once ROOT . 'public/menu/menu.php';
		//Arreglo vacio que tomara los archivos de javascript de cada vista
		$js = array();

		//Se verifica que no este vacio el arreglo
		//En caso de no ser así, se asocian los dos arreglos
		if(count($this->_js))
		{
			$js = $this->_js;
		}
		
		//Arreglo con las rutas de los archivos de estilo, scripts, imagenes, y el menu
		$_layoutParams = array(
			'ruta_css'     => BASE_URL . "views/layouts/". DEFAULT_LAYOUT . "/css/",
			'ruta_img'     => BASE_URL . "views/layouts/". DEFAULT_LAYOUT . "/img/",
			'ruta_js'      => BASE_URL . "views/layouts/". DEFAULT_LAYOUT . "/js/",	
			'menu'         => $menu,
			'js'           => $js
		);
		
		//Ruta de la vista recibida dependiendo del controlador
		$rutaVista = ROOT . "views" . DIR_SEP . $this->_controlador . DIR_SEP . $vista . ".phtml";

		//Se verifica que la ruta exista y sea legible
		if(is_readable($rutaVista))
		{
			//En caso de que exista, se incluye
			include_once ROOT . "views" . DIR_SEP . "layouts" . DIR_SEP . DEFAULT_LAYOUT . DIR_SEP . "default.phtml"; 			
		}
		else
		{
			//De lo contrario de redirecciona al controlador de errores
			header("Location: " . BASE_URL . 'error/access/404');			
		}
	}

	//Metodo encargado de incluir a cada vista sus archivos de javascript
	public function setJs(array $js,$public_js = false)
	{

		if($public_js){

			//Verifica si el valor recibido es un arreglo y si no esta vacio
			if(is_array($js) && count($js)){

				//Se itera el arreglo recibido
				for($i = 0 ; $i < count($js) ; $i++){
					//Se almacena la ruta de los archivos que recibe dependiendo del controlador
					$this->_js[] = BASE_URL .  'public/js/' . $js[$i] . ".js";
				}
			}else{

				//En caso de que no sea un array y este vacio, se generra un error
				throw new Exception("Error de js");			
			}
		}else{

			//Verifica si el valor recibido es un arreglo y si no esta vacio
			if(is_array($js) && count($js)){

				//Se itera el arreglo recibido
				for($i = 0 ; $i < count($js) ; $i++)
				{
					//Se almacena la ruta de los archivos que recibe dependiendo del controlador
					$this->_js[] = BASE_URL .  'views/' . $this->_controlador . '/js/' . $js[$i] . ".js";
				}
			}
			else
			{
				//En caso de que no sea un array y este vacio, se generra un error
				throw new Exception("Error de js");			
			}
			
		}

	}
}