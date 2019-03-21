<?php 

class radicacionController extends Controller
{
    private $_crud;
    private $_model;
    private $_clientes;
    private $_global;
    private $_files;
    private $_pendientes;

    public function __construct() {
        // Si se encuentra iniciada la sesión se redirecciona al controlador principal
        if(Session::get('Mundial_authenticate')){

            if(in_array(Session::getLevel(Session::get("Mundial_user_rol")),[Session::getLevel('Gerente'),Session::getLevel('Operador Radicador')])){

                try {
                    
                    parent::__construct();
                    $this->_crud = $this->loadModel("crud");
                    $this->_model = $this->loadModel("radicacion");
                    $this->_clientes = $this->loadModel("clientes"); 
                    $this->_global = $this->loadModel("global");
                    $this->_files = $this->loadModel("files");
                    $this->_pendientes = $this->loadModel("pendientes");
                } catch (Exception $e) {

                    $this->redireccionar('error');
                }
            }else{
                $this->redireccionar("error","access","5656");
            }
        }else{
            $this->redireccionar("error","access","5656");
        }
    }

    //Visualizacion de la pantalla principal
    public function index(){


        //Visualiza la pagina de radicacion con los modelos que se necesiten
        $this->_view->titulo = "Radicacion";
        $this->_view->SelectTipoDocumento = $this->_global->getAllTypeClients();
        $this->_view->CantidadRadicacionesUser = $this->_model->cantidadRadicacionesUser($_SESSION["Mundial_authenticate_user_id"]);
        $this->_view->setJS(array('radicacion'));
        $this->_view->renderizar('index','radicacion');
    }

    //Busca un cliente dentro del sistema
    public function searchRadicadoCliente(){

        if(Server::RequestMethod("POST")){

            $data = json_decode(json_encode(Server::post()),true);

            $InfoRadicado = $this->_model->searchClientRadicado($data['documentClient']);

            if(!isset($InfoRadicado['error'])){

                if($InfoRadicado){

                    if($InfoRadicado["ESTADO_PROCESO_ID"] == 4){
                        $documentos_pendientes_SQL = $this->_clientes->getAllFilesPendientClient($InfoRadicado["CLIENTE_ID"]);
                        foreach ($documentos_pendientes_SQL as $documento_pendiente) {
                            $documentos_pendientes["CADENA_PENDIENTES"][] = $documento_pendiente["DOCUMENTOS_PENDIENTES_CODIGO"];
                        }
                    }
                    $sucursal                              = $this->_global->getSucursales();
                    $anio                                  = $this->_global->getAnios();
                    $usersConfianza                        = $this->_model->getAllUsuariosConfiaza();
                    $this->titulo                          = "Radicacion";
                    $InfoRadicado["NUM_DOCUMENTO_CLIENTE"] = $data['documentClient'];
                    $tipo_documento_cliente                = $this->_global->getTipoDocumentoByID($InfoRadicado['TIPO_DOCUMENTO_CLIENTE']);
                    $this->SelectTipoDocumento             = $this->_global->getAllTypeClients();
                    $AbreviadosDocumentos                  = $this->_global->AbreviadosDocumentos();
                    $TiposDocumentos                       = $this->_global->TipoDocumentos();
                    $cantidadRadicacionesUser              = $this->_model->cantidadRadicacionesUser($_SESSION["Mundial_authenticate_user_id"]);
                    $tiposDocumentosClientes               = $this->_global->TipoDocumentos();
                    $tiposDocumentosSarlaft                = $this->_global->AbreviadosDocumentos();
                    //JAV01
                    $name = $this->_global->nameClient($data['documentClient']);
                    if(!empty($name)){
                        $nombreCliente = array_shift($name);
                    }

                    header('Content-type:text/html; charset=UTF-8');
                    require_once ROOT . "views" . DIR_SEP ."radicacion" . DIR_SEP . "index.phtml";
                }else{ 

                    $getClientRobot = $this->_files->getClientRobot($data['documentClient']);
                    
                    if($getClientRobot){

                        $message = json_encode(array(
                            "type"              => "warning", 
                            "tipo_documento"    => $getClientRobot['TIPO_IDENT_CLIENTE'],
                            "id"                => $data['documentClient'],
                            "message"           => "El documento N°{$data['documentClient']} existe pero no esta registrado, desea registrarlo ?"
                        ));
                    }else{

                        $message = json_encode(array(
                            "type"      => "warning2", 
                            "id"        => $data['documentClient'],
                            "message"   => "El documento N°{$data['documentClient']} no existe en la base de datos desea registrarlo ?"
                        ));
                    }

                    header('Content-Type: application/json');
                    echo $message;
                }
            }else{

                $message = json_encode(
                    array(
                        "type"      => "error", 
                        "find"      => "ERROR",
                        "message"   => "La consulta genero el siguiente error : " . $InfoRadicado['error'] 
                    )
                );

                header('Content-Type: application/json');
                echo $message;
            }
        }else{
            $this->redireccionar('error','access',array('5656'));
        }
    }

    //Guarda el radicado
    public function saveRadicacion(){

        if(Server::RequestMethod("POST")){

            try {
                
                $data = json_decode(json_encode(Server::post()), true);
                $columnsSQL = $this->_global->getColumnsTable('zr_radicacion');
                $moveFiles = array();

                // Valida y retorna el valor la data
                $dataQuery = Helpers::formatData($columnsSQL,$data);

                // Valida si el retorno de limpieza de los datos no retorna vacio
                if(!empty($dataQuery)){

                    $return = array();

                    // Valida que lleguen archivos
                    if($_FILES){

                        // Variables default para la validacion de retorno
                        $files_move = array(
                            'files_move_total' => 0,
                            'files_move_ok' => 0,
                            'files_move_error' => array(),
                        );

                        // Recorre los archivos y los renombra dependiendo de la estructura pasada desde el formulario

                        foreach ($data['file_renombrado'] as $keyFile => $valueFile) {

                            $archivo[$keyFile-1] = explode("~", $valueFile);$valueFile = $archivo[$keyFile-1][0];
                            $fecha = explode(":", $archivo[$keyFile-1][1]);$archivo[$keyFile-1][1] = $fecha[1];

                            $files_move['files_move_total']++;
                            $_FILES['archivo_renombramiento_' . $keyFile]['name'] = strtoupper(Security::normalizeChars(Security::limpiarCadena($valueFile)).'.'.pathinfo($_FILES['archivo_renombramiento_' . $keyFile]['name'], PATHINFO_EXTENSION));
                            $resultMoveRenombramiento = Helpers::LoadFile($_FILES['archivo_renombramiento_' . $keyFile],['PNG','PDF','DOC','DOCX','JPG','JPEG','XLSX','XLS'],ROOT_ORGANIZED_FILE);

                            // Valida que los arhcivos de hallan cargado correctamente en el sistema
                            if(!isset($resultMoveRenombramiento['error'])){

                                //Obtiene el nombre del archivo para insertar en relación_archivo_radicacion
                                $moveFiles[$keyFile-1]['nombre'] = $resultMoveRenombramiento['success']['ruta_temp'];
                                $moveFiles[$keyFile-1]['fecha'] = $archivo[$keyFile-1][1];

                                $files_move['files_move_ok']++;

                                $TipoArchivo = $this->_files->getFileIDByCodigo(substr($valueFile,0,3));

                                $consultaArchivoPendiente = $this->_clientes->VerifyFilePendientClient($data["cliente_id"],$TipoArchivo['id']);

                                if($consultaArchivoPendiente){

                                    $resultadoLiberaPendiente = $this->_pendientes->setReleaseFileClientPending($data["cliente_id"],$TipoArchivo['id']);

                                    if(isset($resultadoLiberaPendiente['error'])){
                                        throw new Exception('Error al liberar los pendientes del cliente por : ' . $resultadoLiberaPendiente['error']);
                                    }
                                }
                            }else{
                                $files_move['files_move_error'][$valueFile] = $resultMoveRenombramiento['error'];
                            }
                        }

                        if($files_move['files_move_ok'] > 0 && empty($files_move['files_move_error'])){

                            $countDocumentsPendingClient = $this->_pendientes->getCountDocumentsPendingClientById($dataQuery["cliente_id"]);

                            // Retorna el resultado de la insercion de los datos
                            $anterior_fecha_diligenciamiento = $this->_model->getUltFechaDiligenciamientoSarlaftByClienteId($data["cliente_id"]);

                            //Retorna el resultado del tipo de persona por un cliente ID
                            $Tipo_cliente = $this->_clientes->getTypeClienteID($dataQuery["cliente_id"]);

                            if(!$countDocumentsPendingClient){

                                if(isset($data['ESTADO_PROCESO_ID'])){

                                    if($data['ESTADO_PROCESO_ID'] == 12){

                                        $this->_crud->Save('zr_estado_proceso_clientes_sarlaft', array(
                                            'PROCESO_USUARIO_ID'                => $_SESSION['Mundial_authenticate_user_id'],
                                            'PROCESO_CLIENTE_ID'                => $dataQuery["cliente_id"],
                                            'PROCESO_FECHA_DILIGENCIAMIENTO'    => $anterior_fecha_diligenciamiento["ULT_FECHA_DILIGENCIAMIENTO"],
                                            'ESTADO_PROCESO_ID'                 => 1
                                        ));
                                    }else if($data['ESTADO_PROCESO_ID'] == 4){

                                        if($Tipo_cliente['TIPO_PERSONA'] == 'NAT'){

                                            $InfoSarlaftByClienteId = $this->_clientes->getInfoClienteNaturalByClienteId($dataQuery['cliente_id']);
                                        }else{

                                            $InfoSarlaftByClienteId = $this->_clientes->getInfoClienteJuridicoByClienteId($dataQuery["cliente_id"]);
                                        }

                                        if(!isset($InfoSarlaftByClienteId['error'])){

                                            $campos_requeridos = $this->_clientes->camposRequeridos($Tipo_cliente['TIPO_PERSONA']);
                                            $campos_vacios = array();
                                            foreach ($campos_requeridos as $valueCampo) {

                                                if(array_key_exists($valueCampo,$InfoSarlaftByClienteId)){

                                                    if(empty(trim($InfoSarlaftByClienteId[$valueCampo]))){
                                                        array_push($campos_vacios,$valueCampo);
                                                    }else{
                                                        if($valueCampo == 'anexo_preguntas_ppes' && $InfoSarlaftByClienteId['anexo_preguntas_ppes'] == 1){
                                                            $anexo_preguntas_ppes = $this->_clientes->getAllAnexosPPEClientById($dataQuery['cliente_id']);
                                                            if(!$anexo_preguntas_ppes){
                                                                array_push($campos_vacios,$valueCampo);
                                                            }
                                                        }else if($valueCampo == 'anexo_accionistas' && $InfoSarlaftByClienteId['anexo_accionistas'] == 1){
                                                            $anexo_accionistas = $this->_clientes->getAccionistasClienteById($dataQuery['cliente_id']);
                                                            if(!$anexo_accionistas){
                                                                array_push($campos_vacios,$valueCampo);
                                                            }
                                                        }else if($valueCampo == 'sub_anexo_accionistas' && $InfoSarlaftByClienteId['sub_anexo_accionistas'] == 1){
                                                            $sub_anexo_accionistas = $this->_clientes->getSubAccionistasClienteById($dataQuery['cliente_id']);
                                                            if(!$sub_anexo_accionistas){
                                                                array_push($campos_vacios,$valueCampo);
                                                            }
                                                        }
                                                    }
                                                }
                                            }

                                            if(!empty($campos_vacios)){
                                                $changeStatusClient['ESTADO_PROCESO_ID'] = 6;
                                            }else{
                                                $changeStatusClient['ESTADO_PROCESO_ID'] = 5;
                                            }

                                            if(isset($changeStatusClient['ESTADO_PROCESO_ID'])){

                                                if(isset($dataQuery['fecha_diligenciamiento'])){

                                                    if($dataQuery['fecha_diligenciamiento'] == $anterior_fecha_diligenciamiento["ULT_FECHA_DILIGENCIAMIENTO"]){
                                                        $this->_crud->Save('zr_estado_proceso_clientes_sarlaft', array(
                                                            'PROCESO_USUARIO_ID'                => $_SESSION["Mundial_authenticate_user_id"],
                                                            'PROCESO_CLIENTE_ID'                => $dataQuery["cliente_id"],
                                                            'PROCESO_FECHA_DILIGENCIAMIENTO'    => $dataQuery["fecha_diligenciamiento"],
                                                            'ESTADO_PROCESO_ID'                 => $changeStatusClient['ESTADO_PROCESO_ID']
                                                        ));
                                                    }
                                                }else{

                                                    $this->_crud->Save('zr_estado_proceso_clientes_sarlaft', array(
                                                        'PROCESO_USUARIO_ID'                => $_SESSION["Mundial_authenticate_user_id"],
                                                        'PROCESO_CLIENTE_ID'                => $dataQuery["cliente_id"],
                                                        'PROCESO_FECHA_DILIGENCIAMIENTO'    => $anterior_fecha_diligenciamiento["ULT_FECHA_DILIGENCIAMIENTO"],
                                                        'ESTADO_PROCESO_ID'                 => $changeStatusClient['ESTADO_PROCESO_ID']
                                                    ));
                                                }
                                            }
                                        }else{
                                            throw new Exception('ERROR AL LIBERAR EL PROCESO POR: ' . $InfoSarlaftByClienteId['error']);
                                        }
                                    }
                                }
                            }

                            $return['renombramiento'] = array(
                                'type' => 'STATES_OK',
                                'titulo' => 'Exito!!!',
                                'message' => 'No Archivos renombrado y subidos correctamente :  ' . $files_move['files_move_ok'] . ' de : ' . $files_move['files_move_total']
                            );


                            $dataQuery["funcionario_id"] = $_SESSION["Mundial_authenticate_user_id"];
                            $NRadicado = $this->_model->NRadicado();
                            $dataQuery["consecutivo"] = $NRadicado["CONSECUTIVO_RADICADO"];

                            $date = date('Ymd');
                            $TempNRadicado = $date.$data["cliente_id"].'-'.$NRadicado['CONSECUTIVO_RADICADO'];
                            $cliente_repetido = 0;
                            if(isset($dataQuery["fecha_diligenciamiento"])){

                                if((date('Y-m-d',strtotime($dataQuery["fecha_diligenciamiento"])) == date('Y-m-d',strtotime($anterior_fecha_diligenciamiento["ULT_FECHA_DILIGENCIAMIENTO"])))){
                                    $cliente_repetido++;
                                    $dataQuery['repetido'] = 1;                              
                                }
                            }

                            if(isset($dataQuery['repetido']) && $dataQuery['repetido'] == 1){
                                $dataQuery['devuelto'] = 'Si';
                            }

                            // Retorna el resultado de la insercion de los datos
                            $resultado_save_radicado = $this->_crud->Save('zr_radicacion',$dataQuery, true);
                            

                            if(!(isset($resultado_save_radicado['error']) || is_null($resultado_save_radicado))){

                                $return['radicacion'] = array(
                                    'nuevo_cliente' => false,
                                    'cliente_repetido' => false,
                                    'type'  => 'STATES_OK',
                                    'titulo' => 'Exito!!!',
                                    'message' => 'se guardo correctamente el radicado número ' . $TempNRadicado
                                );

                                // Almacenamiento de relación entre archivo subido y radicación - JAV01 - 20180409
                                
                                foreach ($moveFiles as $fileMoved) {

                                    $partesRuta = explode('\\', $fileMoved['nombre']);
                                    if(strlen($fileMoved['fecha'])==7){
                                        $fileMoved['fecha'] .= "-31";
                                    }else if(strlen($fileMoved['fecha'])==4){
                                        $fileMoved['fecha'] .= "-12-31";
                                    }
                                    $dataRel = array(
                                        "RADICACION_ID" => $resultado_save_radicado,
                                        "CLIENTE_ID" => $dataQuery["cliente_id"],
                                        "NOMBRE_ARCHIVO" => $partesRuta[count($partesRuta)-1],
                                        "FECHA_EMISION" => $fileMoved['fecha']
                                    );

                                    $this->_crud->Save("relacion_archivo_radicacion", $dataRel);
                                    
                                }

                                if($cliente_repetido > 0){
                                    $return['radicacion']['cliente_repetido'] = true;
                                }

                                if($data["devuelto"] == 'No'){

                                    if(isset($dataQuery["formulario_sarlaft"])){

                                        if(isset($dataQuery["fecha_diligenciamiento"]) && $dataQuery["formulario_sarlaft"] == 1){

                                            if($Tipo_cliente['TIPO_PERSONA'] == 'NAT'){

                                                $VerifyClientExist = $this->_clientes->getInfoClienteNaturalByClienteId($dataQuery["cliente_id"]);
                                            }else{

                                                $VerifyClientExist = $this->_clientes->getInfoClienteJuridicoByClienteId($dataQuery["cliente_id"]);
                                            }

                                            if(!isset($VerifyClientExist['error'])){

                                                if(!$VerifyClientExist){

                                                    $insert_new = $this->_clientes->insertClient($Tipo_cliente["TIPO_PERSONA"],array('cliente' => $data["cliente_id"]));

                                                    if(!isset($insert_new['error'])){

                                                        $ingresarProcesoCliente = $this->_crud->Save('zr_estado_proceso_clientes_sarlaft', array(
                                                                'PROCESO_USUARIO_ID'                => $_SESSION['Mundial_authenticate_user_id'],
                                                                'PROCESO_CLIENTE_ID'                => $dataQuery["cliente_id"],
                                                                'PROCESO_FECHA_DILIGENCIAMIENTO'    => $dataQuery["fecha_diligenciamiento"],
                                                                'ESTADO_PROCESO_ID'                 => 1,
                                                                'RADICACION_ID'                     => $resultado_save_radicado
                                                              )
                                                        );

                                                        if(!isset($ingresarProcesoCliente['error'])){
                                                            $return["radicacion"]["nuevo_cliente"] = true;
                                                        }else{
                                                            throw new Exception('La gestion del cliente no se guardo correctamente por : ' . $ingresarProcesoCliente['error']);
                                                        }
                                                    }else{
                                                        throw new Exception('No se guardo correctamente el cliente por : ' . $insert_new['error']);
                                                    }
                                                }else{

                                                    if(date('Y-m-d',strtotime($dataQuery["fecha_diligenciamiento"])) > date('Y-m-d',strtotime($anterior_fecha_diligenciamiento["ULT_FECHA_DILIGENCIAMIENTO"]))){

                                                        $ingresarProcesoCliente = $this->_crud->Save('zr_estado_proceso_clientes_sarlaft', array(
                                                                'PROCESO_USUARIO_ID'                => $_SESSION['Mundial_authenticate_user_id'],
                                                                'PROCESO_CLIENTE_ID'                => $dataQuery["cliente_id"],
                                                                'PROCESO_FECHA_DILIGENCIAMIENTO'    => $dataQuery["fecha_diligenciamiento"],
                                                                'ESTADO_PROCESO_ID'                 => 1,
                                                                'RADICACION_ID'                     => $resultado_save_radicado 
                                                            )
                                                        );

                                                        if(!isset($ingresarProcesoCliente['error'])){
                                                            $return["radicacion"]["nuevo_cliente"] = true;
                                                        }else{
                                                            throw new Exception('El estado del cliente no se guardo correctamente por : ' . $ingresarProcesoCliente['error']);
                                                        }
                                                    }else if((date('Y-m-d',strtotime($dataQuery["fecha_diligenciamiento"])) == date('Y-m-d',strtotime($anterior_fecha_diligenciamiento["ULT_FECHA_DILIGENCIAMIENTO"]))) && $data['ESTADO_PROCESO_ID'] == 2){
                                                        
                                                        $ingresarProcesoCliente = $this->_crud->Save('zr_estado_proceso_clientes_sarlaft', array(
                                                                'PROCESO_USUARIO_ID'                => $_SESSION['Mundial_authenticate_user_id'],
                                                                'PROCESO_CLIENTE_ID'                => $dataQuery["cliente_id"],
                                                                'PROCESO_FECHA_DILIGENCIAMIENTO'    => $dataQuery["fecha_diligenciamiento"],
                                                                'ESTADO_PROCESO_ID'                 => 1,
                                                                'RADICACION_ID'                     => $resultado_save_radicado
                                                            )
                                                        );

                                                        if(!isset($ingresarProcesoCliente['error'])){
                                                            $return["radicacion"]["nuevo_cliente"] = true;
                                                        }else{
                                                            throw new Exception('El estado del cliente no se guardo correctamente por : ' . $ingresarProcesoCliente['error']);
                                                        }
                                                    }
                                                }
                                            }else{
                                                throw new Exception('No se pudo verificar el cliente por : ' . $VerifyClientExist['error']);
                                            }
                                        }else{
                                            throw new Exception('Falta enviar la fecha de diligenciamiento');
                                        }
                                    }
                                }else if($data["devuelto"] == 'Si'){

                                    if(isset($data["formulario_sarlaft"])){

                                        $ingresarProcesoCliente = $this->_crud->Save(
                                            'zr_estado_proceso_clientes_sarlaft',
                                            array(
                                                'PROCESO_USUARIO_ID'                => $_SESSION["Mundial_authenticate_user_id"],
                                                'PROCESO_CLIENTE_ID'                => $dataQuery["cliente_id"],
                                                'PROCESO_FECHA_DILIGENCIAMIENTO'    => isset($dataQuery["fecha_diligenciamiento"]) ? $dataQuery["fecha_diligenciamiento"] : '0000-00-00',
                                                'ESTADO_PROCESO_ID'                 => 2,
                                                'RADICACION_ID'                     => $resultado_save_radicado 
                                            )
                                        );
                                    }

                                    if(isset($ingresarProcesoCliente['error'])){
                                        throw new Exception('El estado del cliente no se guardo correctamente por : ' . $ingresarProcesoCliente['error']);
                                    }

                                    $remitente = array(
                                        'email' => Security::decode(MAIL_USER,MAIL_KEYHASH), 
                                        'name' => 'Informes Mundial'
                                    );

                                    $templatePath = ROOT . "public/templates/devueltos.html";
                                    $content_template = file_get_contents($templatePath);

                                    $cids = array(
                                        "[cid:DOCUMENTO]",
                                        "[cid:DESCRIPCION_DEVUELTO]",
                                        "[cid:RADICADO]"
                                    );

                                    $replace = array(
                                        $data['numero_identificacion'],
                                        strtoupper(trim($data['radicacion_observacion'])),
                                        $TempNRadicado
                                    );

                                    $bodyHTML = str_replace($cids, $replace, $content_template);

                                    $SendCorreos = Email::configEmail(
                                        array(
                                            'remitente' => $remitente,
                                            'receptor' => array('email' => $data['correo_radicacion'], 'name' => ''),
                                            'isHTML' => true,
                                            'Asunto' => "Informa de Novedad radicacion N°{$TempNRadicado} - Estado Devuelto",
                                            'Contenido' => $bodyHTML,
                                            'Contenido-noHTML' => 'El informe de este correo es dado a que la radicacion de los documentos quedaron en estado devuelto por el motivo de: &nbsp'."<strong>".strtoupper(trim($data['radicacion_observacion']))."</strong>"
                                        ),
                                        array(
                                            "path" => ROOT . 'public/img/logo_mundial.png',
                                            "cid" => "LOGO_MUNDIAL"
                                        )
                                    );

                                    if((bool)$SendCorreos){

                                        $return['correo'] = array(
                                            'type' => 'STATES_OK',
                                            'titulo' => 'Exito!!!',
                                            'message' => 'Se envio correctamente el correo ' . $data["correo_radicacion"] . ' al cliente ' . $data['numero_identificacion']
                                        );
                                    }else{

                                        $return['correo'] = array(
                                            'type' => 'STATES_ERROR',
                                            'titulo' => 'ERROR !!!',
                                            'message' => 'No se pudo enviar el correo ' . $data['correo_radicacion'] . ' al cliente ' . $data['numero_identificacion'] . ' por favor verifique el correo'
                                        );
                                    }
                                }
                            }else{
                                throw new Exception('Error al guardar por : ' . $resultado_save_radicado['error']);
                            }
                        }

                        if($files_move['files_move_error'] > 0){

                            foreach($files_move['files_move_error']  as $keyFileError => $valueFileError){

                                array_push($return,array(
                                        'renombramiento' => array(
                                            'type' => 'STATES_ERROR',
                                            'titulo' => 'Error!!!',
                                            'message' => $keyFileError .' - '. $valueFileError['message']
                                        )
                                    )
                                );
                            }
                        }
                    }else{
                        throw new Exception('No se obtuvieron documentos para radicar');
                    }

                    echo json_encode($return);
                }else{
                    throw new Exception('La información para procesar llego vacia');
                }
            } catch (Exception $e) {

                echo json_encode(array(
                        array(
                        'type' => 'STATES_ERROR',
                        'title' => 'ERROR AL GUARDAR',
                        'message' => 'DESCRIPCION : ' . $e->getMessage()
                    ))
                );
            }
        }else{
            $this->redireccionar('error','access','5656');
        }
    }

    //Guarda un cliente nuevo en el sistema
    public function saveClientNew(){

        if(Server::RequestMethod("POST")){

            $data = json_decode(json_encode(Server::post()), true);

            $resultado_save = array(
                "carga_cliente" => array(
                    "resultado" => false,
                    "message" => "El Cliente no se cargo correctamente en la base de datos"
                )
            );

            // Guardan los datos del clientes en la tabla clientes
            $resultadoGuardarCliente = $this->_crud->Save('clientes',
                array(
                    "tipo_documento" => Security::normalizeChars(Security::limpiarCadena($data['tipo_documento'])),
                    "documento" => Security::normalizeChars(Security::limpiarCadena($data['documentClient']))
                )
            );

            if(!isset($resultadoGuardarCliente['error'])){
                
                $resultado_save["carga_cliente"]["resultado"] = true;
                $resultado_save["carga_cliente"]["message"] = "Se cargo correctamente el cliente !!!";
            }

            // resultado sobre errores al cargar los datos en la base de datos
            echo json_encode($resultado_save);
        }else{
            $this->redireccionar('error','access','5656');
        }
    }

    //Obtiene la informacion de la tabla de zr_tipo_documento dependiendo del tipo de proceso que se le envie
    public function abreviadosByProceso(){

        if(Server::RequestMethod("POST")){

            $radicacion_proceso = Server::post('radicacion_proceso');
            $AllAbreviadosSarlaftByProceso = $this->_model->getAllAbreviadosSarlaftByProceso($radicacion_proceso);
            $listadoAbreviados = '<option value=""></option>';

            foreach ($AllAbreviadosSarlaftByProceso as $valueAbreviadoSarlaft) {
                $listadoAbreviados .= "<option value='{$valueAbreviadoSarlaft['codigo']}'>{$valueAbreviadoSarlaft['descripcion']} ({$valueAbreviadoSarlaft['codigo']})</option>";
            }
            echo $listadoAbreviados;
        }else{
            $this->redireccionar('error','access','5656');
        }
    }

    //Obtiene listado de radicaciones por cliente
    public function getListRadicacion(){

        if(Server::RequestMethod("POST")){

            $cliente = Server::post("documento");

            $data = $this->_model->getListRadicacion($cliente);
            $lastRadicacionId = array_shift($this->_model->getLastRadicacion($cliente));
            
            $table["data"] = array();
            
            if(!empty($data)){

                $columnsName = array_keys($data[0]);

                foreach ($columnsName as $valueColumn) {

                    if($valueColumn != "ID_RADICACION"){

                        $table["columns"][] = array(
                            'title' => str_replace("_", " ", strtoupper($valueColumn)),
                            'data' => $valueColumn
                        );                
                    }
                }

                $table["columns"][count($columnsName) - 1] = array("title" => "OPCIONES" , "data" => 'OPCIONES');


                foreach ($data as $valueData) {
                    $tempData = array();

                    foreach ($columnsName as $valueTable) {        
                        if(isset($valueData[$valueTable])){
                            $tempData[$valueTable] = $valueData[$valueTable];                    
                        }
                    }

                    if($valueData['ID_RADICACION'] == $lastRadicacionId['ID_RADICACION']){

                        $tempData["OPCIONES"] = "
                            <button class='btn btn-sm btn-info' onclick='EditRadicacion(this," . $valueData['ID_RADICACION'] . ")'>
                                <span class='glyphicon glyphicon-pencil'></span>
                            </button>
                            <button class='btn btn-sm btn-danger' style='margin-left:3px;' onclick='DeleteRadicacion(this," . $valueData['ID_RADICACION'] . ")'>
                                <span class='glyphicon glyphicon-trash'></span>
                            </button>
                        ";
                    }else{
                        $tempData["OPCIONES"] = "";                        

                    }

                    array_push($table["data"],$tempData);
                }
            }

            echo json_encode($table);


        }else{
            $this->redireccionar("error", "access","5656");
        }
    }

    //Consultar radicación para modificar
    public function searchRadicadoById(){

        if(Server::RequestMethod("POST")){

            $id = Server::post("idRadicacion");

            $InfoRadicado = $this->_model->searchClientRadicadoById($id);

            if(!isset($InfoRadicado['error'])){

                if($InfoRadicado){
                    $sucursal                              = $this->_global->getSucursales();
                    $anio                                  = $this->_global->getAnios();
                    $usersConfianza                        = $this->_model->getAllUsuariosConfiaza();
                    $this->titulo                          = "Radicacion";
                    $InfoRadicado["NUM_DOCUMENTO_CLIENTE"] = $InfoRadicado['CLIENTE_DOCUMENTO'];
                    $tipo_documento_cliente                = $this->_global->getTipoDocumentoByID($InfoRadicado['TIPO_DOCUMENTO_CLIENTE']);
                    $this->SelectTipoDocumento             = $this->_global->getAllTypeClients();
                    $AbreviadosDocumentos                  = $this->_global->AbreviadosDocumentos();
                    $TiposDocumentos                       = $this->_global->TipoDocumentos();
                    $cantidadRadicacionesUser              = $this->_model->cantidadRadicacionesUser($_SESSION["Mundial_authenticate_user_id"]);
                    $tiposDocumentosClientes               = $this->_global->TipoDocumentos();
                    $tiposDocumentosSarlaft                = $this->_global->AbreviadosDocumentos();

                    //JAV01
                    $name = $this->_global->nameClient($InfoRadicado['CLIENTE_DOCUMENTO']);
                    if(!empty($name)){
                        $nombreCliente = array_shift($name);
                    }
                    // $nombreCliente                         = array_shift($this->_global->nameClient($data['documentClient']));

                    header('Content-type:text/html; charset=UTF-8');
                    require_once ROOT . "views" . DIR_SEP ."radicacion" . DIR_SEP . "index.phtml";
                }else{ 

                    $getClientRobot = $this->_files->getClientRobot($data['documentClient']);
                    
                    if($getClientRobot){

                        $message = json_encode(array(
                            "type"              => "warning", 
                            "tipo_documento"    => $getClientRobot['TIPO_IDENT_CLIENTE'],
                            "id"                => $data['documentClient'],
                            "message"           => "El documento N°{$data['documentClient']} existe pero no esta registrado, desea registrarlo ?"
                        ));
                    }else{

                        $message = json_encode(array(
                            "type"      => "warning2", 
                            "id"        => $data['documentClient'],
                            "message"   => "El documento N°{$data['documentClient']} no existe en la base de datos desea registrarlo ?"
                        ));
                    }

                    header('Content-Type: application/json');
                    echo $message;
                }
            }else{

                $message = json_encode(
                    array(
                        "type"      => "error", 
                        "find"      => "ERROR",
                        "message"   => "La consulta genero el siguiente error : " . $InfoRadicado['error'] 
                    )
                );

                header('Content-Type: application/json');
                echo $message;
            }
        }else{
            $this->redireccionar('error','access',array('5656'));
        }
    }

    //Modificar el radicado
    public function editRadicacion(){

        if(Server::RequestMethod("POST")){

            try {
                
                $data = json_decode(json_encode(Server::post()), true);
                $columnsSQL = $this->_global->getColumnsTable('zr_radicacion');
                $moveFiles = array();

                // Valida y retorna el valor la data
                $dataQuery = Helpers::formatData($columnsSQL,$data);
                // Valida si el retorno de limpieza de los datos no retorna vacio               
                if(!empty($dataQuery)){

                    $return = array();

                    // Valida que lleguen archivos
                    if($_FILES || isset($data["radicacion_id"])){

                        // Variables default para la validacion de retorno
                        $files_move = array(
                            'files_move_total' => 0,
                            'files_move_ok' => 0,
                            'files_move_error' => array(),
                        );
                        // Recorre los archivos y los renombra dependiendo de la estructura pasada desde el formulario

                        if(isset($data['file_renombrado'])){
                            
                            foreach ($data['file_renombrado'] as $keyFile => $valueFile) {

                                $archivo[$keyFile-1] = explode(" ", $valueFile);$valueFile = $archivo[$keyFile-1][0];
                                $fecha = explode(":", $archivo[$keyFile-1][1]);$archivo[$keyFile-1][1] = $fecha[1];

                                $files_move['files_move_total']++;
                                $_FILES['archivo_renombramiento_' . $keyFile]['name'] = strtoupper(Security::normalizeChars(Security::limpiarCadena($valueFile)).'.'.pathinfo($_FILES['archivo_renombramiento_' . $keyFile]['name'], PATHINFO_EXTENSION));
                                $resultMoveRenombramiento = Helpers::LoadFile($_FILES['archivo_renombramiento_' . $keyFile],['PNG','PDF','DOC','DOCX','JPG','JPEG','XLSX','XLS'],ROOT_ORGANIZED_FILE);

                                // Valida que los arhcivos de hallan cargado correctamente en el sistema
                                if(!isset($resultMoveRenombramiento['error'])){

                                    //Obtiene el nombre del archivo para insertar en relación_archivo_radicacion
                                    $moveFiles[$keyFile-1]['nombre'] = $resultMoveRenombramiento['success']['ruta_temp'];
                                    $moveFiles[$keyFile-1]['fecha'] = $archivo[$keyFile-1][1];

                                    $files_move['files_move_ok']++;
                                    $TipoArchivo = $this->_files->getFileIDByCodigo(substr($valueFile,0,3));

                                    $consultaArchivoPendiente = $this->_clientes->VerifyFilePendientClient($data["cliente_id"],$TipoArchivo['id']);

                                    if($consultaArchivoPendiente){

                                        $resultadoLiberaPendiente = $this->_pendientes->setReleaseFileClientPending($data["cliente_id"],$TipoArchivo['id']);

                                        if(isset($resultadoLiberaPendiente['error'])){
                                            throw new Exception('Error al liberar los pendientes del cliente por : ' . $resultadoLiberaPendiente['error']);
                                        }
                                    }
                                }else{
                                    $files_move['files_move_error'][$valueFile] = $resultMoveRenombramiento['error'];
                                }
                            }
                        }

                        if(($files_move['files_move_ok'] > 0 && empty($files_move['files_move_error'])) || isset($data["radicacion_id"]) ){

                            $countDocumentsPendingClient = $this->_pendientes->getCountDocumentsPendingClientById($dataQuery["cliente_id"]);

                            // Retorna el resultado de la insercion de los datos
                            $anterior_fecha_diligenciamiento = $this->_model->getUltFechaDiligenciamientoSarlaftByClienteId($data["cliente_id"]);

                            //Retorna el resultado del tipo de persona por un cliente ID
                            $Tipo_cliente = $this->_clientes->getTypeClienteID($dataQuery["cliente_id"]);

                            if(!$countDocumentsPendingClient){

                                if(isset($data['ESTADO_PROCESO_ID'])){

                                    if($data['ESTADO_PROCESO_ID'] == 12){

                                        $this->_crud->Save('zr_estado_proceso_clientes_sarlaft', array(
                                            'PROCESO_USUARIO_ID'                => $_SESSION['Mundial_authenticate_user_id'],
                                            'PROCESO_CLIENTE_ID'                => $dataQuery["cliente_id"],
                                            'PROCESO_FECHA_DILIGENCIAMIENTO'    => $anterior_fecha_diligenciamiento["ULT_FECHA_DILIGENCIAMIENTO"],
                                            'ESTADO_PROCESO_ID'                 => 1,
                                            'RADICACION_ID'                     => $data["radicacion_id"]
                                        ));
                                    }else if($data['ESTADO_PROCESO_ID'] == 4){

                                        if($Tipo_cliente['TIPO_PERSONA'] == 'NAT'){

                                            $InfoSarlaftByClienteId = $this->_clientes->getInfoClienteNaturalByClienteId($dataQuery['cliente_id']);
                                        }else{

                                            $InfoSarlaftByClienteId = $this->_clientes->getInfoClienteJuridicoByClienteId($dataQuery["cliente_id"]);
                                        }

                                        if(!isset($InfoSarlaftByClienteId['error'])){

                                            $campos_requeridos = $this->_clientes->camposRequeridos($Tipo_cliente['TIPO_PERSONA']);
                                            $campos_vacios = array();
                                            foreach ($campos_requeridos as $valueCampo) {

                                                if(array_key_exists($valueCampo,$InfoSarlaftByClienteId)){

                                                    if(empty(trim($InfoSarlaftByClienteId[$valueCampo]))){
                                                        array_push($campos_vacios,$valueCampo);
                                                    }else{
                                                        if($valueCampo == 'anexo_preguntas_ppes' && $InfoSarlaftByClienteId['anexo_preguntas_ppes'] == 1){
                                                            $anexo_preguntas_ppes = $this->_clientes->getAllAnexosPPEClientById($dataQuery['cliente_id']);
                                                            if(!$anexo_preguntas_ppes){
                                                                array_push($campos_vacios,$valueCampo);
                                                            }
                                                        }else if($valueCampo == 'anexo_accionistas' && $InfoSarlaftByClienteId['anexo_accionistas'] == 1){
                                                            $anexo_accionistas = $this->_clientes->getAccionistasClienteById($dataQuery['cliente_id']);
                                                            if(!$anexo_accionistas){
                                                                array_push($campos_vacios,$valueCampo);
                                                            }
                                                        }else if($valueCampo == 'sub_anexo_accionistas' && $InfoSarlaftByClienteId['sub_anexo_accionistas'] == 1){
                                                            $sub_anexo_accionistas = $this->_clientes->getSubAccionistasClienteById($dataQuery['cliente_id']);
                                                            if(!$sub_anexo_accionistas){
                                                                array_push($campos_vacios,$valueCampo);
                                                            }
                                                        }
                                                    }
                                                }
                                            }

                                            if(!empty($campos_vacios)){
                                                $changeStatusClient['ESTADO_PROCESO_ID'] = 6;
                                            }else{
                                                $changeStatusClient['ESTADO_PROCESO_ID'] = 5;
                                            }

                                            if(isset($changeStatusClient['ESTADO_PROCESO_ID'])){

                                                if(isset($dataQuery['fecha_diligenciamiento'])){

                                                    if($dataQuery['fecha_diligenciamiento'] == $anterior_fecha_diligenciamiento["ULT_FECHA_DILIGENCIAMIENTO"]){
                                                        $this->_crud->Save('zr_estado_proceso_clientes_sarlaft', array(
                                                            'PROCESO_USUARIO_ID'                => $_SESSION["Mundial_authenticate_user_id"],
                                                            'PROCESO_CLIENTE_ID'                => $dataQuery["cliente_id"],
                                                            'PROCESO_FECHA_DILIGENCIAMIENTO'    => $dataQuery["fecha_diligenciamiento"],
                                                            'ESTADO_PROCESO_ID'                 => $changeStatusClient['ESTADO_PROCESO_ID'],
                                                            'RADICACION_ID'                     => $data["radicacion_id"]
                                                        ));
                                                    }
                                                }else{

                                                    $this->_crud->Save('zr_estado_proceso_clientes_sarlaft', array(
                                                        'PROCESO_USUARIO_ID'                => $_SESSION["Mundial_authenticate_user_id"],
                                                        'PROCESO_CLIENTE_ID'                => $dataQuery["cliente_id"],
                                                        'PROCESO_FECHA_DILIGENCIAMIENTO'    => $anterior_fecha_diligenciamiento["ULT_FECHA_DILIGENCIAMIENTO"],
                                                        'ESTADO_PROCESO_ID'                 => $changeStatusClient['ESTADO_PROCESO_ID'],
                                                        'RADICACION_ID'                     => $data["radicacion_id"]
                                                    ));
                                                }
                                            }
                                        }else{
                                            throw new Exception('ERROR AL LIBERAR EL PROCESO POR: ' . $InfoSarlaftByClienteId['error']);
                                        }
                                    }
                                }
                            }

                            $return['renombramiento'] = array(
                                'type' => 'STATES_OK',
                                'titulo' => 'Exito!!!',
                                'message' => 'No Archivos renombrado y subidos correctamente :  ' . $files_move['files_move_ok'] . ' de : ' . $files_move['files_move_total']
                            );

                            $dataQuery["funcionario_id"] = $_SESSION["Mundial_authenticate_user_id"];
                            $NRadicado = $this->_model->NRadicadoAnt($data["radicacion_id"]);
                            $dataQuery["consecutivo"] = $NRadicado["CONSECUTIVO_RADICADO"];

                            $date = date('Ymd');
                            $TempNRadicado = $date.$data["cliente_id"].'-'.$NRadicado['CONSECUTIVO_RADICADO'];
                            $cliente_repetido = 0;

                            if(isset($dataQuery["fecha_diligenciamiento"])){

                                if((date('Y-m-d',strtotime($dataQuery["fecha_diligenciamiento"])) == date('Y-m-d',strtotime($anterior_fecha_diligenciamiento["ULT_FECHA_DILIGENCIAMIENTO"])))){
                                    $cliente_repetido++;
                                    $dataQuery['repetido'] = 1;                              
                                }
                            }

                            // Retorna el resultado de la insercion de los datos
                            $dataQuery["id"] = $data["radicacion_id"];
                            
                            $resultado_save_radicado = $this->_crud->Update('zr_radicacion',$dataQuery);

                            if(!isset($resultado_save_radicado['error'])){

                                $return['radicacion'] = array(
                                    'nuevo_cliente' => false,
                                    'cliente_repetido' => false,
                                    'type'  => 'STATES_OK',
                                    'titulo' => 'Exito!!!',
                                    'message' => 'Se modificó correctamente el radicado número ' . $TempNRadicado
                                );

                                // Almacenamiento de relación entre archivo subido y radicación - JAV01 - 20180409
                                
                                foreach ($moveFiles as $fileMoved) {
                                    $partesRuta = explode('\\', $fileMoved['nombre']);
                                    if(strlen($fileMoved['fecha'])==7){
                                        $fileMoved['fecha'] .= "-31";
                                    }else if(strlen($fileMoved['fecha'])==4){
                                        $fileMoved['fecha'] .= "-12-31";
                                    } 

                                    $dataRel = array(
                                        "RADICACION_ID" => $data["radicacion_id"],
                                        "CLIENTE_ID" => $dataQuery["cliente_id"],
                                        "NOMBRE_ARCHIVO" => $partesRuta[count($partesRuta)-1],
                                        "FECHA_EMISION" => $fileMoved['fecha']
                                    );

                                    $this->_crud->Save("relacion_archivo_radicacion", $dataRel);
                                    
                                }

                                if($cliente_repetido > 0){
                                    $return['radicacion']['cliente_repetido'] = true;
                                }

                                if($data["devuelto"] == 'No'){

                                    if(isset($dataQuery["formulario_sarlaft"])){

                                        if(isset($dataQuery["fecha_diligenciamiento"]) && $dataQuery["formulario_sarlaft"] == 1 || isset($data["radicacion_id"])){

                                            if($Tipo_cliente['TIPO_PERSONA'] == 'NAT'){

                                                $VerifyClientExist = $this->_clientes->getInfoClienteNaturalByClienteId($dataQuery["cliente_id"]);
                                            }else{

                                                $VerifyClientExist = $this->_clientes->getInfoClienteJuridicoByClienteId($dataQuery["cliente_id"]);
                                            }

                                            if(!isset($VerifyClientExist['error'])){

                                                if(!$VerifyClientExist){

                                                    $insert_new = $this->_clientes->insertClient($Tipo_cliente["TIPO_PERSONA"],array('cliente' => $data["cliente_id"]));

                                                    if(!isset($insert_new['error'])){

                                                        $ingresarProcesoCliente = $this->_crud->Save('zr_estado_proceso_clientes_sarlaft', array(
                                                                'PROCESO_USUARIO_ID'                => $_SESSION['Mundial_authenticate_user_id'],
                                                                'PROCESO_CLIENTE_ID'                => $dataQuery["cliente_id"],
                                                                'PROCESO_FECHA_DILIGENCIAMIENTO'    => $dataQuery["fecha_diligenciamiento"],
                                                                'ESTADO_PROCESO_ID'                 => 1,
                                                                'RADICACION_ID'                     => $data["radicacion_id"]
                                                            )
                                                        );

                                                        if(!isset($ingresarProcesoCliente['error'])){
                                                            $return["radicacion"]["nuevo_cliente"] = true;
                                                        }else{
                                                            throw new Exception('La gestion del cliente no se guardo correctamente por : ' . $ingresarProcesoCliente['error']);
                                                        }
                                                    }else{
                                                        throw new Exception('No se guardo correctamente el cliente por : ' . $insert_new['error']);
                                                    }
                                                }else{

                                                    if(date('Y-m-d',strtotime($dataQuery["fecha_diligenciamiento"])) > date('Y-m-d',strtotime($anterior_fecha_diligenciamiento["ULT_FECHA_DILIGENCIAMIENTO"]))){

                                                        $ingresarProcesoCliente = $this->_crud->Save('zr_estado_proceso_clientes_sarlaft', array(
                                                                'PROCESO_USUARIO_ID'                => $_SESSION['Mundial_authenticate_user_id'],
                                                                'PROCESO_CLIENTE_ID'                => $dataQuery["cliente_id"],
                                                                'PROCESO_FECHA_DILIGENCIAMIENTO'    => $dataQuery["fecha_diligenciamiento"],
                                                                'ESTADO_PROCESO_ID'                 => 1,
                                                                'RADICACION_ID'                     => $data["radicacion_id"]
                                                            )
                                                        );

                                                        if(!isset($ingresarProcesoCliente['error'])){
                                                            $return["radicacion"]["nuevo_cliente"] = true;
                                                        }else{
                                                            throw new Exception('El estado del cliente no se guardo correctamente por : ' . $ingresarProcesoCliente['error']);
                                                        }
                                                    }else if((date('Y-m-d',strtotime($dataQuery["fecha_diligenciamiento"])) == date('Y-m-d',strtotime($anterior_fecha_diligenciamiento["ULT_FECHA_DILIGENCIAMIENTO"]))) && $data['ESTADO_PROCESO_ID'] == 2){
                                                        
                                                        $ingresarProcesoCliente = $this->_crud->Save('zr_estado_proceso_clientes_sarlaft', array(
                                                                'PROCESO_USUARIO_ID'                => $_SESSION['Mundial_authenticate_user_id'],
                                                                'PROCESO_CLIENTE_ID'                => $dataQuery["cliente_id"],
                                                                'PROCESO_FECHA_DILIGENCIAMIENTO'    => $dataQuery["fecha_diligenciamiento"],
                                                                'ESTADO_PROCESO_ID'                 => 1,
                                                                'RADICACION_ID'                     => $data["radicacion_id"] 

                                                            )
                                                        );

                                                        if(!isset($ingresarProcesoCliente['error'])){
                                                            $return["radicacion"]["nuevo_cliente"] = true;
                                                        }else{
                                                            throw new Exception('El estado del cliente no se guardo correctamente por : ' . $ingresarProcesoCliente['error']);
                                                        }
                                                    }
                                                }
                                            }else{
                                                throw new Exception('No se pudo verificar el cliente por : ' . $VerifyClientExist['error']);
                                            }
                                        }else{
                                            throw new Exception('Falta enviar la fecha de diligenciamiento');
                                        }
                                    }
                                }else if($data["devuelto"] == 'Si'){

                                    if(isset($dataQuery["formulario_sarlaft"])){

                                        $ingresarProcesoCliente = $this->_crud->Save(
                                            'zr_estado_proceso_clientes_sarlaft',
                                            array(
                                                'PROCESO_USUARIO_ID'                => $_SESSION["Mundial_authenticate_user_id"],
                                                'PROCESO_CLIENTE_ID'                => $dataQuery["cliente_id"],
                                                'PROCESO_FECHA_DILIGENCIAMIENTO'    => isset($dataQuery["fecha_diligenciamiento"]) ? $dataQuery["fecha_diligenciamiento"] : '0000-00-00',
                                                'ESTADO_PROCESO_ID'                 => 2,
                                                'RADICACION_ID'                     => $data["radicacion_id"]  
                                            )
                                        );
                                    }

                                    if(isset($ingresarProcesoCliente['error'])){
                                        throw new Exception('El estado del cliente no se guardo correctamente por : ' . $ingresarProcesoCliente['error']);
                                    }

                                    $cids = array(
                                        "[cid:DOCUMENTO]",
                                        "[cid:DESCRIPCION_DEVUELTO]",
                                        "[cid:RADICADO]"
                                    );

                                    $replace = array(
                                        $data['numero_identificacion'],
                                        strtoupper(trim($data['radicacion_observacion'])),
                                        $TempNRadicado
                                    );

                                    $replace = array(
                                        strtoupper(trim($data['radicacion_observacion']))
                                    );

                                    $bodyHTML = str_replace($cids, $replace, $content_template);

                                    $SendCorreos = Email::configEmail(
                                        array(
                                            'remitente' => $remitente,
                                            'receptor' => array('email' => $data['correo_radicacion'], 'name' => ''),
                                            'isHTML' => true,
                                            'Asunto' => "Informa de Novedad radicacion N°{$TempNRadicado} - Estado Devuelto",
                                            'Contenido' => $bodyHTML,
                                            'Contenido-noHTML' => 'El informe de este correo es dado a que la radicacion de los documentos quedaron en estado devuelto por el motivo de: &nbsp'."<strong>".strtoupper(trim($data['radicacion_observacion']))."</strong>"
                                        ),
                                        array(
                                            "path" => ROOT . 'public/img/logo_mundial.png',
                                            "cid" => "LOGO_MUNDIAL"
                                        )
                                    );

                                    if((bool)$SendCorreos){

                                        $return['correo'] = array(
                                            'type' => 'STATES_OK',
                                            'titulo' => 'Exito!!!',
                                            'message' => 'Se envio correctamente el correo ' . $data["correo_radicacion"] . ' al cliente ' . $data['numero_identificacion']
                                        );
                                    }else{

                                        $return['correo'] = array(
                                            'type' => 'STATES_ERROR',
                                            'titulo' => 'ERROR !!!',
                                            'message' => 'No se pudo enviar el correo ' . $data['correo_radicacion'] . ' al cliente ' . $data['numero_identificacion'] . ' por favor verifique el correo'
                                        );
                                    }
                                }
                            }else{
                                throw new Exception('Error al guardar por : ' . $resultado_save_radicado['error']);
                            }
                        }

                        if($files_move['files_move_error'] > 0){

                            foreach($files_move['files_move_error']  as $keyFileError => $valueFileError){

                                array_push($return,array(
                                        'renombramiento' => array(
                                            'type' => 'STATES_ERROR',
                                            'titulo' => 'Error!!!',
                                            'message' => $keyFileError .' - '. $valueFileError['message']
                                        )
                                    )
                                );
                            }
                        }
                    }else{
                        throw new Exception('No se obtuvieron documentos para radicar');
                    }

                    echo json_encode($return);
                }else{
                    throw new Exception('La información para procesar llego vacia');
                }
            } catch (Exception $e) {

                echo json_encode(array(
                        array(
                        'type' => 'STATES_ERROR',
                        'title' => 'ERROR AL GUARDAR',
                        'message' => 'DESCRIPCION : ' . $e->getMessage()
                    ))
                );
            }
        }else{
            $this->redireccionar('error','access','5656');
        }
    }

    //Eliminar el radicado por id
    public function deleteRadicacion(){

        if(Server::RequestMethod("POST")){
            $id = Server::post("radicacionId");

            $filesRadicacion = $this->_model->getFilesRadicacion($id);

            foreach ($filesRadicacion as $row) {
                
                $Info = $this->_clientes->getInfoFileClient(false, $row["CLIENTE_ID"],false, true);

                $nameFile  = $Info["FOLDER_ARCHIVO"] . "/" . $row["NOMBRE_ARCHIVO"];
                $pathFiles = str_replace("\\", "/", FOLDERS_PATH . $nameFile);
                
                $globalPathFiles = str_replace("\\", "/", FOLDERS_PATH);

                while(file_exists($pathFiles) && $pathFiles !=  $globalPathFiles && $pathFiles != str_replace("\\", "/", $Info["FOLDER_ARCHIVO"]) ){                
                    $this->deleteFiles($pathFiles);
                }

            }
            
            $resultado = $this->_crud->Delete('zr_radicacion',$id);

            if(!isset($resultado['error'])){
                echo json_encode(array(
                    "error" => false
                ));
            }else{
                echo json_encode(array(
                    "error" => true,
                    "message" => $resultado['error']
                ));
            }

        }else{
            $this->redireccionar('error','access',array('5656'));
        }
    }

    //Funcion para eliminación de clientes - JAV01
    public function deleteCliente(){
        if(Server::RequestMethod("POST")){
            $id = Server::post("idCliente");

            $Info = $this->_clientes->getInfoFileClient(false, $id, false, true);

            $delFiles = $this->_files->deleteFilesClient($Info["DOCUMENTO_CLIENTE"]);

            $pathFiles = str_replace("\\", "/", FOLDERS_PATH . (strpos("/", $Info["FOLDER_ARCHIVO"]) != -1 ?  explode("/", $Info["FOLDER_ARCHIVO"])[0] : $Info["FOLDER_ARCHIVO"]));
            
            $globalPathFiles = str_replace("\\", "/", FOLDERS_PATH);

            while(file_exists($pathFiles) && $pathFiles != $globalPathFiles){                
                $this->deleteFiles($pathFiles);
            }

            $response = $this->_crud->Delete("clientes", $id);

            if(isset($response["error"])){
                echo json_encode(array(
                    "error" => true,
                    "message" => $response["error"]
                ));
            }else{
                echo json_encode(array(
                    "message" => "Se ha eliminado correctamente el cliente."
                ));
            }
        }else{
            $this->redireccionar('error','access',array('5656'));
        }
    }

    //Eliminar archivos de clientes
    public function deleteFiles($path){
        $globalPathFiles = str_replace("\\", "/", FOLDERS_PATH);
        if($path !=  $globalPathFiles ){
            if (!(@rmdir($path) || @unlink($path))){
                $list = glob($path . '*', GLOB_MARK);
                foreach ($list as $file) {   
                    if($file != FOLDERS_PATH)             
                        $this->deleteFiles($file);
                }
            }
        }
    }
}