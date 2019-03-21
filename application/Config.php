<?php

ini_set("display_errors", true); // False en servidor
ini_set('memory_limit','-1');
ini_set('post_max_size', '30M');
ini_set('upload_max_filesize', '30M');
ini_set('max_input_time', '5');

//Configuracion de rutas
// define("BASE_URL", "/mundialcalidad/");//Ruta principal - LOCAL
define("BASE_URL", 	'/' . basename(ROOT) . '/');//Ruta principal - LOCAL

// var_dump(BASE_URL);
// exit;

define("DEFAULT_CONTROLLER", "users");//Controller por defecto
define("DEFAULT_LAYOUT", "default");//Vista por defecto

// -----------------------------------------------------------------

//Informacion de la aplicacion
define("APP_NAME", "Seguros Confianza"); //Nombre de la aplicacion
define("APP_CLIENT_NAME", "confianza"); //Directorios especificos segun cliente


// -----------------------------------------------------------------
//Llave de encripción conexiones
define("DB_KEYHASH", "/0UwUlWKcKnbrC/0Fzyw9MIxA+pOYdxv9wBe1+ckvLkh2X71BWoMpsGrgAXL5wRgLqXWCH0P92XTh9AjfKU6lw==");
define("FTP_KEYHASH", "zxoxa6bpYE8d+wTNX/SofWM3fRPYdS+PiCg/6C1Uice7poZBW3AfhCNBav+6kXXPjoCV3Y7LqkG6wMI/4YpWgS2nxKT9hV8T4fQBMGOL6scW3GDkb65FSuYRk00bgf8D");
define("MAIL_KEYHASH", "EkttLUiNUJ7cXCmEq1awEH7arUAcdBRjardcBAosgJJsFSDAikC45/hAhT4upeWX6D0nxkFSiq4VgUSwwjHA7A==");

//Configuracion de la base de datos
define("DB_HOST", "w7n0G8yBaqg8NXZyKbQCezSQZC9V/t9ERBfSu9N8i+4=");//host
define("DB_USER", "vxPBrZrFgz4tEsP88Tf6hwxNPano29mNGX0GqHTILUk=");//usuario
define("DB_PASS", "IKwnoUN/O//xxlSZM/fPH9uhGJSsK2vBccgafyrc0K4=");//contraseña
define("DB_NAME", "UewBwcLHpMhA1wYRhtAQ6pTfzdsvhgmzEQlZintyLjg=");//nombre de la base de datos
define("DB_CHAR", "utf8");//charset
                        
//Contraseña genérica de usuarios
define("DEFAULT_PASSWORD", "Temporal123");     

//Configuracion de FTP
//define("FTP_HOST","/IPBOmUPdAhKrKOOcgwt6qgJ3wbT+xbKtMEOGCVKrYg=");	//anterior
define("FTP_HOST","wSnv0344voRTUJVBH08ivhaXdiwNpK/3qk3b0CxQ3AE=");
define("FTP_USERNAME", "v1cEPh45o7Blw+CuL+OEIyza3+gi3aEPFuTmRUTyWaI=");
define("FTP_PASSWORD","FM7SsKVoXywWhw0V0gx0pDZ8v5+NWgyFZRvwjbrx6r4="); 

//Configuración para envío de correos
define("MAIL_SMTP","BsGw8AXCHlUU+je4RND5BCV3KnRKr6BQnRixu6R7Eh4=");
define("MAIL_PORT", 25);
define("MAIL_USER","AFDyDOQnvHzIF07rBfLBLtRrCSJgUM/8QrhWjtKeTqhfEZT8B+MVJ5UR1lx51WoWR6ZrdmCMwBrbjWbkLroTZA==");
define("MAIL_PASSWORD","23M6UQedcVARDuVwrZPyUgdBqBb1UOVxbV3IzSHUBkA=");

define('COMPLETITUD', 6);
define('DEVUELTO', 2);
define('FINALIZADO', 3);
define('FINALIZADO_INTENTOS', 14);
define('FINALIZADO_TIPOLOGIA', 9);
define('MIGRACION', 13);
// define('PENDIENTE_DOCUMENTOS', 4);
define('PENDIENTE_SARLAFT', 12);
define('PENDIENTE_TIPOLOGIA', 10);
define('PENDIENTE_FIRMA_HUELLA_ENTREVISTA', 11);
define('PROCESO_CAPTURA', 1);
define('RADICACION', 7);
define('VERIFICACION',5);

//Ruta de archivos organizados por robot
define("FOLDERS_PATH", ROOT . "organizados" . DIR_SEP);

define("ROOT_ORGANIZED_FILE", 'C:\RobotOrganizador\origen' . DIR_SEP);