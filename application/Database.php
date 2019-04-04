<?php

//Clase encargada de la conexion y gestion de la base de datos
class Database extends PDO 
{
	public function __construct() 
	{
		//Se envian los parametros de conexion 
		// al metodo constructor de la clase padre

		/* Orden de los parametros:
		*  1- Nombre del host y nombre de la base de datos
		*  2- Nombre del usuario de la base de datos
		*  3- Contraseña de base de datos
		*  4- Charset (UTF-8)
		*/

		try
		{
//echo "\n<br>FTP_HOST decode 94.247.31.194:" . Security::decode("/IPBOmUPdAhKrKOOcgwt6qgJ3wbT+xbKtMEOGCVKrYg=", FTP_KEYHASH);
//echo "\n<br>FTP_HOST decode 127.0.0.1:" . Security::decode("wSnv0344voRTUJVBH08ivhaXdiwNpK/3qk3b0CxQ3AE=", FTP_KEYHASH);
//echo "\n<br>DB_NAME encode::" . Security::encode("Asistemyca_mundial", DB_KEYHASH);

//echo "\n<br>FTP_HOST decode FTP_USERNAME:" . Security::decode("22xjg322Lc/G0b3dniHYGXOMLWNwM0XlM2mZ7/ztidw=", FTP_KEYHASH);

			if(DB_NAME != "" && DB_HOST != "" && DB_USER != "" )
			{
				parent::__construct(
					/* 'mysql:host='. Security::decode(DB_HOST, DB_KEYHASH) . */
					'mysql:host='. '192.165.30.31' .
					';dbname='. Security::decode(DB_NAME, DB_KEYHASH) .
					';chatset='. DB_CHAR,
					Security::decode(DB_USER, DB_KEYHASH),
					Security::decode(DB_PASS, DB_KEYHASH),
					array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES '. DB_CHAR));
			}

		}
		catch(PDOException $ex)
		{		
			echo '<div class="error_dialog">Error al conectar a la base de datos</div>';
			Session::destroy();
		}
	}
}
