<?php
ob_start();

//Clase Controller es abstracta para que no pueda ser instanciada
abstract class Controller 
{
	//Argumento protegido de la clase
	//Solo lo podran acceder sus hijos
	protected $_view;

	//Método constructor de la clase
	public function __construct()
	{	
		$this->_view = new View(new Request);
	}

	//Metodo index, abstracto para obligar a todos sus hijos a que lo implementen
	abstract public function index();

	//Metodo protegido, solo sus hijos lo pueden acceder
	//recibe el nombre del modelo
	protected function loadModel($modelo)
	{
		//El nombre del modelo recibido, se le agrega la cadena 'Model'
		$modelo = $modelo . 'Model';

		//Se busca el archivo dentro del directorio de modelos
		$rutaModelo = ROOT . "models" . DIR_SEP . $modelo . ".php";

		//Se verifica si el archivo existe y es legible
		if(is_readable($rutaModelo))
		{
			//En caso de su existencia, se requiere el archivo
			require_once $rutaModelo;

			//Se instancia el modelo llamado y se retorna
			$modelo = new $modelo;			
			return $modelo;
		}
		else
		{
			//Si no existe, se genera una excepcion indicando un error
			throw new Exception("Error de modelo");
			
		}
	}

	//Metodo protegido, solo sus hijos lo pueden acceder
	//recibe el nombre de una libreria a buscar
	protected function getLibrary($libreria)
	{
		//Se busca la ruta de la libreria
		$rutaLibreria = ROOT . "libs" . DIR_SEP . $libreria . ".php";

		//Se verifica la existencia y legibilidad de la libreria
		if(is_readable($rutaLibreria))
		{
			//En caso de su existencia se requiere el archivo
			require_once $rutaLibreria;
		}
		else
		{
			//Si no existe, se genera una excepcion indicando un error
			throw new Exception("Error de librería.");
			
		}
	}

	//Metodo protegido, solo sus hijos lo pueden acceder
	//Se encarga de filtar y retornar el valor recibido 
	protected function getTexto($_value)
	{
		//Se verifica que exista el valor enviado y que no este vacio
		if(isset($_POST[$_value]) && !empty($_POST[$_value]))
		{
			//De ser así se filtra y se retorna
			$_POST[$_value]= htmlspecialchars($_POST[$_value], ENT_QUOTES);
			return $_POST[$_value];
		}

		//De lo contrario se retorna vacio
		return '';
	}

	//Metodo protegido, solo sus hijos lo pueden acceder
	//Se encarga de filtar y retornar un numero recibido
	protected function getInt($_value)
	{
		//Se verifica que exista el valor enviado y que no este vacio
		if(isset($_POST[$_value]) && !empty($_POST[$_value]))
		{
			//De ser así se filtra convirtiendolo a entero, y se retorna;
			$_POST[$_value]= filter_input(INPUT_POST, $_value, FILTER_VALIDATE_INT);
			return $_POST[$_value];
		}

		//De lo contrario se retorna 0
		return 0;
	}

	//Metodo protegido, solo sus hijos lo pueden acceder
	//Se encarga de redireccionar la aplicacion a una ruta especifica
	protected function redireccionar($controller = false, $method = false, $args = array())
	{

		$method = $method ? $method : '';

		//Se verifica si recibio algun valor en el parametro
		if($controller){

			$args_text = '';
			if(!empty($args)){
				if(is_array($args)){
					$args_text = DIR_SEP . implode(DIR_SEP , $args);
				}else{
					$args_text = DIR_SEP . $args;	
				}
			}

			if($method){
				$method = DIR_SEP . $method;
			}else{
				$method = '';
			}

			header("Location: ". BASE_URL . $controller . $method . $args_text);			
		}
		else
		{
			//De lo contrario se redirecciona a la ruta por defecto
			header("Location: ". BASE_URL);
			exit;
		}
	}
}
