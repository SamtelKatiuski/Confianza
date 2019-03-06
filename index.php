<?php

	//Variables que se usarán a lo largo de la aplicación
	date_default_timezone_set('America/Bogota');

	// Separador de directorio 
	define("DIR_SEP", DIRECTORY_SEPARATOR);

	// La ruta directa del directorio especifico
	define("ROOT", dirname(__FILE__) . DIR_SEP);

	// Ruta de aplicacion
	define("APP_PATH", ROOT . "application" . DIR_SEP);

	// Llamada de todos los archivos dentro de la aplicacion	
	require_once APP_PATH . "Config.php";
	require_once APP_PATH . "Helpers.php";
	require_once APP_PATH . "Email.php";
	require_once APP_PATH . "Request.php";
	require_once APP_PATH . "Response.php";
	require_once APP_PATH . "Controller.php";
	require_once APP_PATH . "FTP.php";
	require_once APP_PATH . "Model.php";
	require_once APP_PATH . "View.php";
	require_once APP_PATH . "Database.php";
	require_once APP_PATH . "Session.php";
	require_once APP_PATH . "Cookies.php";
	require_once APP_PATH . "Server.php";
	require_once APP_PATH . "Security.php";
	require_once ROOT . "models/usersModel.php"; // Se carga para validad SID  de Logueo

	error_reporting(0);
	// Inicializar variables de session
	Session::init();	

	// Envio de nueva peticion
	try
	{
		// echo Security::encode("",DB_KEYHASH);
		//echo Security::decode(FTP_PASSWORD,FTP_KEYHASH);
		// exit;
		
		if(Session::get('Mundial_authenticate')) //Validacion de SID, una sola Session por Usuario
		{
			$user = new usersModel;
			$userInfo = $user->getUserInfo(Session::get('Mundial_authenticate_user_id'));

			$sidValido = $userInfo["sid"];
			if(! (Session::get('sid_session')=== $sidValido))
			{
				session_destroy(); //Destruir Session
	      		header('Location: '.BASE_URL."?inv_sid=1");//Redireccionar al Loguin
			}
		}
		Response::run(new Request);
		// phpinfo();	
	}
	catch(Exception $ex)
	{
		echo $ex->getMessage();
	}

