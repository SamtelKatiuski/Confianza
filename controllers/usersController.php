<?php 

class usersController extends Controller 
{
    private $_user;	

    public function __construct(){

        parent::__construct();

        $this->_user = $this->loadModel("users");
    }

    //Inicio de sesión
    public function index(){
        
        //Si se encuentra iniciada la sesión se redirigue a controlador principal
        if(Session::get('Mundial_authenticate')){
            $this->redireccionar("home");
        }else{   
            //Visualizar pantalla de logueo inicial
            $this->_view->titulo = "Iniciar Sesión";            
            $this->_view->setJs(array('login'));
            $this->_view->renderizar('login');  
        }   
    }  

    //Cerrar session por inactividad
    public function timeout()
    {
      session_destroy(); //Destruir Session
      header('Location: '.BASE_URL);
    }  

    //Iniciar sesión
    public function login(){
        //Solo si la petición se realiza por POST
        if(Server::RequestMethod("POST"))
        {

            //Obtener datos enviados
            $username = Server::post('username');
            $password = Server::post('password');

            //Buscar si existe usuario
            $userExist = $this->_user->exist($username);
            
            if($userExist)
            {                    
                
                //Obtener información completa de usuario
                $userInfo = $this->_user->getData($username);
                $userInfo = array_shift($userInfo);

                //Verificar estado de usuario
                if($userInfo['status'] == 1)
                {

                    $url = $_SERVER["HTTP_HOST"];
                    $roles = array(2,4);
                    if($url==="localhost" || $url==="127.0.0.1")
                    {
                      $ipLocal="localhost"; //asistemyca.proinsoft.com.co
                      $ipExterna="127.0.0.1"; //asistemyca.proinsoft.com.co.mocha6007.mochahost.com
                    }
                    elseif($url==="mundialasistemyca.proinsoft.com.co" || $url==="asistemyca.proinsoft.com.co.mocha6007.mochahost.com")
                    {
                      $ipLocal="mundialasistemyca.proinsoft.com.co";
                      $ipExterna="mundialasistemyca.proinsoft.com.co.mocha6007.mochahost.com";
                    }
                    elseif($url==="172.30.50.27:8089")
                    {
                      $ipLocal="172.30.50.27:8089";
                      $ipExterna="186.154.203.157:8089";
                    }
                    else {
                      $ipLocal="172.30.50.27:8089"; 
                      $ipExterna="186.154.203.157:8089";
                    }

                    if( true)//( $url===$ipExterna && (in_array($userInfo['role_type'], $roles)) ) || $url ===$ipLocal) //Validacion de logueo de Ip con Perfil
                    {
                        $hashKey     = $userInfo['hash_key'];  
                        $passUser    = $userInfo['password'];   

                        //Cifrar contraseña ingresada con llave de usuario
                        $password    = Security::encode($password, $hashKey);
                        
                        //Verificación de coincidencia de contraseñas
                        if($password == $passUser)  
                        {     
                            //Inicio de variables de sesión
                            Session::set('Mundial_authenticate', true);
                            Session::set('Mundial_authenticate_user_id', $userInfo['id']);
                            Session::set('Mundial_authenticate_name_user', $userInfo['nombre']);
                            Session::set('Mundial_authenticate_username', $username);
                            Session::set('Mundial_user_rol', $userInfo['role']);
                            Session::set('Mundial_rol', $userInfo['role_type']);
                            
                            $current_date_time = date("Y-m-d");
                            $hour=date("H:i:s");
                            $datetime_change_password = date('Y-m-d', strtotime($userInfo['password_change_datetime']));

                            $sid = Security::generateRandomKey().$current_date_time."_".$hour;

                            $valSid = $this->_user->iniciarSid($sid,$userInfo['id']);
                            Session::set('sid_session', $sid);

                            if($current_date_time >= $datetime_change_password)
                            {
                                //Se debe actualizar la contraseña
                                Session::set("Mundial_change_password", true);
                            }
                            
                            echo json_encode((int)$userInfo['role_type']) ;
                        }
                        else  
                        {          
                            echo json_encode(-11); // Contraseña incorrecta     
                        }
                    }
                    else
                    {
                        echo json_encode(-14); // Perfil bloqueado para logueo externo
                    }                 
                }
                else
                {
                    echo json_encode(-12); // Usuario inactivo
                }
            }
            else
            {
                echo json_encode(-13); // Usuario no existe
            }
        }else{               
            $this->redireccionar("error","access", array("5656"));
        } 
    }

    //Gestión de usuarios, modificación, eliminación, visualización
    public function gestion_usuarios(){

        //Solo si cuenta con los permisos
        if(Session::get('Mundial_authenticate')){

            if(in_array(Session::getLevel(Session::get("Mundial_user_rol")),[Session::getLevel('Gerente')])){
                //Visualizar pantalla 
                $this->_view->titulo = "Gestión de Usuarios.";            
                $this->_view->setJs(array('index'));
                $this->_view->renderizar('gestion','users');
                
            }else{
                $this->redireccionar("error/access/5656");
            }
        }else{
            $this->redireccionar("error/access/5656");
        }
    }   

    //Obtener listado de usuarios
    public function gesListUsers(){

        Session::access("Gerente");

        if(Server::RequestMethod("POST")){

            //Envío de datos a la vista
            $usuarios = $this->_user->loadUsers();

            $table["data"] = array();

            if(!empty($usuarios)){
                $table["columns"][] = array(
                    'title' => "NOMBRE",
                    'data'  => 'nombre'
                );

                $table["columns"][] = array(
                    'title' => "USUARIO",
                    'data'  => 'username'
                );

                $table["columns"][] = array(
                    'title' => "TIPO DE USUARIO",
                    'data'  => 'rol_name'
                );

                $table["columns"][] = array(
                    'title' => "ESTADO",
                    'data'  => 'status'
                );

                $table["columns"][] = array(
                    'title' => "OPCIONES",
                    'data'  => 'opciones'
                );

                foreach ($usuarios as $user){
                    $temp = array();
                    $usrActive = true;

                    foreach ($table["columns"] as $value) {
                        if($value["title"] != 'OPCIONES' && $value["title"] != 'ESTADO')
                            $temp[$value["data"]] = $user[$value["data"]];
                        else if($value["title"] == 'ESTADO'){
                            $temp[$value["data"]] = ($user[$value["data"]] == 1 ? 'Activo' : 'Inactivo');
                            $usrActive = ($user[$value["data"]] == 1 ? true : false);
                        }
                        else{

                            $temp[$value["data"]] =
                            '<a class="user-item-option-update btn btn-sm btn-info" href="' . BASE_URL . 'users/modificar_usuario?id=' . $user['id'] . '"><span class="glyphicon glyphicon-pencil"></span></a>';

                            if($usrActive)
                            {
                                $temp[$value["data"]] .= ' <a class="user-item-option-delete btn btn-sm btn-primary" href="#" id=' . $user['id'] . ' onclick="DeleteUser(this)"><span class="glyphicon glyphicon-eye-close"></span></a>';
                            }
                            
                            $temp[$value["data"]] .= ' <a title="Eliminar" class="user-item-option-delete btn btn-sm btn-danger" href="javascript:;" id=' . $user['id'] . ' onclick="DeleteUser(this)"><span class="glyphicon glyphicon-trash"></span></a>';
                        }
                    }

                    array_push($table["data"], $temp);
                }

            }

            echo json_encode($table);

        }else{
            $this->redireccionar("error/access/5656");
        }
    }


    //Eliminar usuario
    public function delete(){
        Session::access("Gerente");

		if(Server::RequestMethod("POST")){
            //Solo se conceden permiso de ingreso a perfil de Gerente
            $id = Server::post("id");
            echo json_encode($this->_user->delete($id));
        }else{               
            $this->redireccionar("error/access/5656");
        } 		
    }
    

    //Crear nuevo usuario
    public function nuevo_usuario(){
        Session::access("Gerente");
        //Solo si cuenta con los permisos
        if(Session::get('Mundial_authenticate'))
        {
            //Solo se conceden permiso de ingreso a perfil de Gerente
            Session::access("Gerente");

            //Visualizar pantalla 
            $this->_view->titulo = "Registrar usuario.";            
            $this->_view->setJs(array('add'));

            //Envío de datos a la vista
            $this->_view->roles = $this->_user->getRoles();
            
        }
        else
        {               
            $this->redireccionar("error/access/5656");
        }   

        $this->_view->renderizar('add','users');  
    }

	
	public function modificar_usuario(){
        //Solo si cuenta con los permisos
        if(Session::get('Mundial_authenticate'))
        {
            $id = htmlspecialchars($_GET["id"]);			
			if($id)
            {
                //Solo se conceden permiso de ingreso a perfil de Gerente
                Session::access("Gerente");

                //Visualizar pantalla 
                $this->_view->titulo = "Modificar usuario.";            
                $this->_view->setJs(array('update'));
					
                //Envío de datos a la vista
                $this->_view->roles = $this->_user->getRoles();

                $userinfo = $this->_user->getUserInfo($id);

                $this->_view->password = Security::decode($userinfo['password'], $userinfo['hash_key']);

                if(!count($userinfo))
                    $this->redireccionar("error/access/5656");

                $this->_view->user = $userinfo;

            }
            else
            {
                $this->redireccionar("error");
            }
            
        }
        else
        {               
            $this->redireccionar("error/access/5656");
        }   
		$this->_idGet = $id ;
        $this->_view->renderizar('update','users');  	
    }

    //Cambio de contraseña por vencimiento
    public function change_invalid_password(){
        if(Server::RequestMethod("POST"))
        {
            $userInfo = $this->_user->getData(Session::get("Mundial_authenticate_username"));
            $userInfo = array_shift($userInfo);

            $key = $userInfo['hash_key'];

            $password = Security::encode(Server::post("password"), $key);

            if($password == $userInfo['password'])
            {
                $newKey = Security::generateRandomKey();

                $data = array(
                    "password" => Security::encode(Server::post("new_password"), $newKey),
                    "key" => $newKey
                );

                $res = $this->_user->change_password($data);

                if($res)
                {
                    Session::set("Mundial_change_password", false);
                    echo 1; // Cambio de contraseña correcto
                }
                else
                    echo 0; // Error en cambio de contraseña
            }
            else
            {
                echo 2; // Contraseña incorrecta
            }            
        }
        else
        {               
            $this->redireccionar("error/access/5656");
        } 
    }

    //Cambio de contraseña por el dministrador
    public function change_password_admin()
    {
        $_fullId = Server::post("full_id");
        $exist = $this->_user->existId(Server::post("username"), $_fullId);

        if(!$exist)
        {
            $user = array_shift($this->_user->getUserInfo($_fullId));

            $newKey = Security::generateRandomKey();

            $data = array(
                "id" => $_fullId,
                "password" => Security::encode(Server::post("new_password"), $newKey),
                "key" => $newKey
            );

            $res = $this->_user->change_password_admin($data);

            if($res)
            {
                Session::set("Zurich_change_password", false);
                echo 1; // Cambio de contraseña correcto
            }
            else
                echo 0; // Error en cambio de contraseña
        }

    }

    //Generar sugerencia de nombre de usuario por nombre
    public function generateUsername(){
        if(Server::RequestMethod("POST"))
        {
            $existe = false;
            $cadena = '';

            $nombre = Server::post("value");

            $string = Security::normalizeChars($nombre);

            if(strpos($string, " ") > 0)
                $fullName = explode(" ", $string);
            else
                $fullName = $string;

            $username = '';

            if(is_array($fullName))
            {
                if(isset($fullName[2]))
                {
                    $username = $fullName[0] . "." . $fullName[2];
                }
                else if(isset($fullName[1]))
                {
                    $username = $fullName[0] . "." . $fullName[1];
                }
            }
            else
            {
                $username = strtolower($fullName . "_" . rand(0,100));
            }

            $cadena = $username;

            do
            {

                $existe = $this->_user->exist($cadena);

                if($existe)
                    $cadena = strtolower($username . rand(0,100));

            }
            while($existe);
            

            echo json_encode(strtolower($cadena));
        
        }
        else
        {               
            $this->redireccionar("error/access/5656");
        } 
    }

    //Function para guardar los usuarios creados
    public function register(){
        Session::access("Gerente");
		if(Server::RequestMethod("POST"))
        {
			$exist = $this->_user->exist(Server::post("username"));

            if(!$exist){

                //Llave de cifrado de datos aleatoria
                $hash_key = Security::generateRandomKey();

                //Arreglo con datos para inserción
                $data = array(
                    "nombre" => trim(Server::post("full_name")),
                    "username" => trim(Server::post("username")),
                    "password" => Security::encode(DEFAULT_PASSWORD, $hash_key),
                    "hash_key" => $hash_key,
                    "role" => Server::post("role"),
                    "correo" => trim(Server::post("correo"))
                );

                //Inserción de datos.
                $result = $this->_user->insert($data);

                if($result){
                    echo json_encode(1); // Usuario creado
                }else{
                    echo json_encode(0); // Usuario no ha sido creado
                }
            }else{
                echo json_encode(2); // Nombre de usuario ya existe
            }
        }else{               
            $this->redireccionar("error",'access',array('5656'));
        } 
    }

    //Function para guardar los usuarios modificados
    public function update(){
        Session::access("Gerente");

       if(Server::RequestMethod("POST"))
        {
			$_fullId = Server::post("full_id");	
			$exist = $this->_user->existId(Server::post("username"), $_fullId);
			
            if(!$exist)
            {
               $user = array_shift($this->_user->getUserInfo($_fullId));

                //Arreglo con datos para inserción
                $data = array(
                    "id" => $_fullId,
                    "nombre" => trim(Server::post("full_name")),
                    "username" => trim(Server::post("username")),
                    "role" => Server::post("role"),
                    "correo" => trim(Server::post("correo")),
                    "status" => Server::post("status")
                );

                //Modificación de datos.
                $result = $this->_user->update($data);

                if($result){
                    echo json_encode(1); // Usuario creado
                }else {
                    echo json_encode(0); // Usuario no ha sido creado
                }
            }
            else
            {
                echo json_encode(2); // Nombre de usuario ya existe
            }
        }else {               
            $this->redireccionar("error/access/5656");
        }  
		// $this->redireccionar("gestion_usuarios");
    }

    //Bloquear
    public function bloquearUser()
    {
      if(Server::RequestMethod("POST"))
      {
          //Obtener datos enviados
          $username = Server::post('username');
          //Buscar si existe usuario
          $userExist = $this->_user->exist($username);

          if($userExist)
          {
            //Arreglo con datos para inserción
            $data = array(
                "username" => $username,
            );

            //Modificación de datos.
            $result = $this->_user->bloquear($data);

            if($result)
            {
                echo json_encode(1); // Usuario bloqueado
            }
            else
            {
                echo json_encode(0); // Usuario no bloqueado
            }
          }
          else {
            echo json_encode(2); // Usuario no existe
          }
       }
    }

    //Cerrar sesión
    public function logout(){
		Session::destroy(); 
		$this->redireccionar();
    } 
}