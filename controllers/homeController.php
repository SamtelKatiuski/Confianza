<?php
class homeController extends Controller {

    //Variables globales
    private $_global;
    private $_crud;
    private $_files;
    private $_clientes;
    private $_pendientes;
    private $_radicacion;
    
    public function __construct() {

        // Si se encuentra iniciada la sesión se redirecciona al controlador principal
        if(Session::get('Mundial_authenticate')){

            if(in_array(Session::getLevel(Session::get("Mundial_user_rol")),[
                Session::getLevel('Gerente'),
                Session::getLevel('Operador Asistemyca'),
                Session::getLevel('Operador Radicador')
            ])){
                
                try {

                    parent::__construct();

                    //Inicia a primera instancia los modelos
                    $this->_global = $this->loadModel("global");
                    $this->_crud = $this->loadModel("crud");
                    $this->_files = $this->loadModel("files");
                    $this->_clientes = $this->loadModel("clientes");
                    $this->_pendientes = $this->loadModel("pendientes");
                    $this->_radicacion = $this->loadModel("radicacion");

                } catch (Exception $e) {
                    die($e->getMessage());
                }
            }else{
                $this->redireccionar("error","access","5656");
            }
        }else{
            $this->redireccionar("error","access","5656");
        }
    }

    //Pagina principal
    public function index(){
        //Carga los archivos
        $this->_view->setJs(array('home'));
        $this->_view->renderizar('index','home');
    }

    //Obtiene un cliente al random un cliente al ramdon que se encuentre en estado 1 - PROCESO CAPTURA
    public function LoadClientRamdon() {

        if (Server::RequestMethod("POST")) {

            //Trae un formulario al random de un cliente nuevo para capturarlo
            $captura_cliente = $this->_clientes->getClientCaptureRamdon();

            if(!isset($captura_cliente['error'])){

                if($captura_cliente){

                    $return["type"] = 'success';
                    $return["cliente_id"] = $captura_cliente["CLIENTE_ID"];

                    echo json_encode($return);
                }else{

                    $return["type"] = 'error';
                    $return["messageError"] = 'No hay clientes para captura';

                    echo json_encode($return);
                }
            }else{

                echo $captura_cliente['error'];
            }
        }else{
            $this->redireccionar('error','access',404);
        }
    }

    //Obtiene la informacion del formulario de cliente
    public function loadFormClient() {

        if (Server::RequestMethod("POST")) {

            try {

                // ID del cliente que al cual se le van a capturar los datos
                $id = Server::post("id");

                //Verifica el tipo de cliente dependiendo del ID que envie para captura 
                $tipo_cliente = $this->_clientes->getTypeClienteID($id);                

                if($tipo_cliente){

                    $InfoCliente = $this->_clientes->getInfoClientID($tipo_cliente['TIPO_PERSONA'],$id);

                    if(!isset($InfoCliente['error'])){
                        
                        $chk_sarlaft_client = $this->_clientes->getInfoFileByClientId($id,'FCC');

                        if($chk_sarlaft_client && isset($chk_sarlaft_client["FOLDER_ARCHIVO"]) && file_exists(FOLDERS_PATH . $chk_sarlaft_client["FOLDER_ARCHIVO"])){

                            if($tipo_cliente['TIPO_PERSONA'] == 'NAT'){

                                $cliente = $this->_clientes->getInfoClienteNaturalByClienteId($id);
                            }else if($tipo_cliente['TIPO_PERSONA'] == 'JUR'){

                                $cliente = $this->_clientes->getInfoClienteJuridicoByClienteId($id);
                            }else{

                                throw new Exception('No se encontro el tipo de persona');
                            }

                            if(!isset($cliente['error'])){

                                if ($tipo_cliente['TIPO_PERSONA'] == 'NAT') {
                                    foreach ($verificado = $this->_clientes->getVerificadoSarlaftNatural($cliente['id']) as $keyVerificado => $valueVerificado) {
                                        if (!in_array($keyVerificado, ['id', 'cliente_sarlaft_natural_id'])) {
                                            $cliente[$keyVerificado] = $valueVerificado;
                                        }
                                    }
                                } else if ($tipo_cliente['TIPO_PERSONA'] == 'JUR') {
                                    foreach ($verificado = $this->_clientes->getVerificadoSarlaftJuridico($cliente['id']) as $keyVerificado => $valueVerificado) {
                                        if (!in_array($keyVerificado, ['id', 'cliente_sarlaft_juridico_id'])) {
                                            $cliente[$keyVerificado] = $valueVerificado;
                                        }
                                    }
                                }

                                if($cliente){

                                    if(!isset($_SESSION['cliente']) || empty($_SESSION['cliente'])){
                                        
                                        $cliente_ocupado = $this->_clientes->ActivateByClienteId($id,1);
                                        $_SESSION['cliente'] = $id;
                                    }

                                    if(isset($_SESSION['cliente']) && !empty($_SESSION['cliente'])){
                                        if($_SESSION['cliente'] != $id){
                                            
                                            $libera_cliente_antiguo = $this->_clientes->ActivateByClienteId($_SESSION['cliente'],0);
                                            if(isset($libera_cliente_antiguo['error'])){

                                                throw new Exception("Error para liberar cliente activos");
                                            } else{

                                                $cliente_ocupado = $this->_clientes->ActivateByClienteId($id,1);
                                                if(isset($cliente_ocupado['error'])){

                                                    throw new Exception("Error al tratar de ocupar el cliente " . $id);
                                                }else{
                                                     $_SESSION['cliente'] = $id;
                                                }
                                            }
                                        }
                                    }


                                   $linea_negocio_id = $this->_clientes->getLineaNegocioCliente($id);

                                    // Envia el Titulo del formulario dependiendo del tipo de cliente
                                    if($InfoCliente['TIPO_PERSONA'] == "NAT"){

                                        $titulo = "Persona Natural"; 
                                    }else if($InfoCliente['TIPO_PERSONA'] == "JUR"){

                                        $titulo = "Persona Juridica";
                                    }

                                    /*=====================================
                                    =            DEFAULT VISTA            =
                                    =====================================*/
                                    
                                        $paises                  = $this->_global->getCountries();
                                        $ocupaciones = $this->_global->getOccupations();
                                        $vias = $this->_global->getMultiParam('via');
                                        $literales = $this->_global->getMultiParam('literal');
                                        $orientacion = $this->_global->getMultiParam('orientacion');
                                        $detallePredio = $this->_global->getMultiParam('detalle_predio');
                                        $departamentos           = $this->_global->getDepartaments();
                                        $ciudades                = $this->_global->getCities();
                                        $vinculaciones           = $this->_clientes->getConnections();
                                        $relaciones              = $this->_clientes->getRelations();
                                        $estado_civil            = $this->_clientes->getCivilStates();
                                        // $param_direcciones    = $this->_clientes->getAddressParam();
                                        $actividades_principales = $this->_clientes->getActividadesPrincipales();
                                        $tipos_actividades       = $this->_clientes->getTiposActividades();
                                        $sectores                = $this->_clientes->getSector();
                                        $opMonExtra              = $this->_clientes->getOperacionesMonedaExtranjera();
                                        $tiposDocumentos         = $this->_global->getAllTypeClients();
                                        $tipologias              = $this->_clientes->getTipologies();
                                        $lineas_negocio          = $this->_clientes->getLineaNegocio();
                                        $vinculo_relacion = $this->_clientes->getVinculoRelacion('Natural');
                                        $vinculo_relacion_juridico = $this->_clientes->getVinculoRelacion('Juridico');
                                        $anio = $this->_global->getAnios();
                                        $monedas = $this->_global->getMonedas();
                                        $sucursal = $this->_global->getSucursales();
                                        $tipos_empresa = $this->_clientes->getTipoEmpresa();
                                        if(!in_array($InfoCliente["estado_formulario_id"],[1,2])){

                                            $getGestionCompletitud = $this->_clientes->getAllGestionesCompletitudVerificacion($InfoCliente['cliente_id'],$InfoCliente["fecha_diligenciamiento"]);

                                            $cliente["no_intentos"] = 0;
                                            $cliente["observaciones_tipologia"] = '';

                                            if($getGestionCompletitud){
                                                $getGestionCompletitud = end($getGestionCompletitud);
                                                $cliente["tipologia"]               = $getGestionCompletitud['GESTION_TIPOLOGIA'];
                                                $cliente["estado_tipologia"]        = $getGestionCompletitud['GESTION_ESTADO_TIPOLOGIA_ID'];
                                                $cliente["no_intentos"]             = $getGestionCompletitud["GESTION_NO_INTENTOS"];
                                                $cliente["observaciones_tipologia"] = $getGestionCompletitud["GESTION_OBSERVACIONES"];
                                                $cliente["tipologia"]               = $getGestionCompletitud['GESTION_TIPOLOGIA'];
                                            }
                                        }        

                                        //JAV01
                                        $name = $this->_global->nameClient($InfoCliente['documento']);
                                        if(!empty($name)){
                                            $nombreCliente = array_shift($name);
                                        }

                                        //Tipos de sociedad del cliente
                                        $tiposSociedad = $this->_clientes->getSocietyType();

                                        //Tipos de documento de persona natural
                                        $natural = $this->_clientes->getClientsTypePerson("NAT");

                                        //Tipos de documento de persona jurídica
                                        $juridico = $this->_clientes->getClientsTypePerson("JUR");
                                    
                                    /*=====  End of DEFAULT VISTA  ======*/

                                    // solo si anexa preguntas ppes
                                    if($cliente["anexo_preguntas_ppes"] == 1){
                                        $anexo_ppes = $this->_clientes->getAllAnexosPPEClientById($id);
                                    }

                                    // solo si anexa productos los extrae
                                    if($cliente["productos_exterior"] == 'SI'){
                                        $productos_financieros = $this->_clientes->getAllProductosClienteById($InfoCliente["cliente_id"]);
                                    }

                                    if($InfoCliente["TIPO_PERSONA"] == "JUR"){

                                        // solo si llegan anexo_accionistas si es Juridico
                                        if($cliente["anexo_accionistas"] == 'SI'){
                                            $accionistas = $this->_clientes->getAccionistasClienteById($id);
                                            foreach ($accionistas as $valueAccionista) {
                                                $accionistasVerificados[] = $this->_clientes->getVerificadoAccionistas($valueAccionista['id']);
                                            }
                                        }

                                        // solo si se anexan sub_accionistas
                                        if($cliente["anexo_sub_accionistas"] == 1){
                                            $sub_accionistas = $this->_clientes->getSubAccionistasClienteById($id);
                                        }
                                    }

                                    $repeat = false;
                                    $radicaciones = $this->_radicacion->cantidadRadicacionesCliente($id, $InfoCliente['fecha_diligenciamiento']);
                                    
                                    if($radicaciones){

                                        if($radicaciones["CANTIDAD_RADICACIONES"] > 1){

                                            $repeat = true;
                                        }
                                    }


                                    $anexos = array();
                                    if(!is_null($cliente["anexo_preguntas_ppes"]) && $cliente["anexo_preguntas_ppes"] == 1){
                                        $anexos[] = 'APEP';
                                    }

                                    if(in_array($InfoCliente['estado_formulario_id'],[12])){

                                        if(in_array($InfoCliente['ANT_ESTADO_PROCESO_ID'],[1])){

                                            $ingresarProcesoCliente = $this->_crud->Save(
                                                'zr_estado_proceso_clientes_sarlaft',
                                                array(
                                                    'PROCESO_USUARIO_ID' => $_SESSION["Mundial_authenticate_user_id"],
                                                    'PROCESO_CLIENTE_ID' => $InfoCliente['cliente_id'],
                                                    'PROCESO_FECHA_DILIGENCIAMIENTO' => $InfoCliente['fecha_diligenciamiento'],
                                                    'ESTADO_PROCESO_ID' => 3
                                                )
                                            );
                                        }else{

                                            $ingresarProcesoCliente = $this->_crud->Save(
                                                'zr_estado_proceso_clientes_sarlaft',
                                                array(
                                                    'PROCESO_USUARIO_ID' => $_SESSION["Mundial_authenticate_user_id"],
                                                    'PROCESO_CLIENTE_ID' => $InfoCliente['cliente_id'],
                                                    'PROCESO_FECHA_DILIGENCIAMIENTO' => $InfoCliente['fecha_diligenciamiento'],
                                                    'ESTADO_PROCESO_ID' => 1
                                                )
                                            );                                            
                                        }


                                        if(!isset($ingresarProcesoCliente['error'])){
                                            echo "<script>location.reload();</script>";
                                        }          
                                    }

                                    // Envia el tipo de vista
                                    if($InfoCliente["TIPO_PERSONA"] == "NAT"){
                                        $nameView = "natural";
                                    }else if($InfoCliente["TIPO_PERSONA"] == "JUR"){
                                        $nameView = "juridico";
                                    }

                                    // Verifica si llega la variable $nameView para enviar la vista o si no lo envia  a la 404 
                                    if(isset($nameView) && !empty($nameView)){
                                        $pathView = ROOT . "views/home/" . $nameView . ".phtml";

                                        // Valida si la vista del formulario existe y la requiere dependiendo del caso
                                        if (file_exists($pathView)) {

                                            //Incluir vista necesaria
                                            require_once $pathView;
                                        } else {
                                            echo 404; //Ruta no existe
                                        }

                                    }else{
                                        echo 404;
                                    } 
                                }else{
                                    throw new Exception('El cliente no devuelve ninguna información');
                                }
                            }else{
                                throw new Exception('No se puede visualizar el archivo por : ' . $cliente['error']);
                            }
                        }else{

                            $verifyFilePendientExist = $this->_clientes->VerifyFilePendientClient($id,1);

                            if(!isset($verifyFilePendientExist['error'])){
                                
                                if($verifyFilePendientExist["DOCUMENTO_EXISTE"] == 0){

                                    $savePendiente = $this->_crud->Save(
                                        'zr_clientes_pendientes_documentos',
                                        array(
                                            'CLIENTE_ID' => $id,
                                            'DOCUMENTO_PENDIENTE_ID' => 1
                                        )
                                    );

                                    if(!isset($cliente_sarlaft['error'])){

                                        $ingresarProcesoCliente = $this->_crud->Save(
                                            'zr_estado_proceso_clientes_sarlaft',
                                            array(
                                                'PROCESO_USUARIO_ID' => $_SESSION["Mundial_authenticate_user_id"],
                                                'PROCESO_CLIENTE_ID' => $id,
                                                'ESTADO_PROCESO_ID' => 12
                                            )
                                        );
                                    }else{

                                        throw new Exception('Error al consultar un cliente existente por : ' . $cliente_sarlaft['error']);
                                    }
                                }

                                throw new Exception('Este cliente no tiene sarlaft o se elimino del sistema no se podra continuar con el proceso');
                            }else{

                                throw new Exception('Verificación documento pendiente ' . $verifyFilePendientExist['error']);
                            }
                        }
                    }else{

                        throw new Exception('la consulta genero el siguiente error: ' . $InfoCliente['error']);
                    }
                }else{

                    throw new Exception('No se encuentra ningun formulario para el cliente');
                }                
            } catch (Exception $e) {

                echo json_encode(array(array(
                    'type' => 'STATES_ERROR', 
                    'titulo' => 'ERROR',
                    'message' => 'DESCRIPCION: ' . $e->getMessage()
                )));
            }
        } else {
            $this->redireccionar();
        }
    }

    //Guardar información de cliente
    public function saveCaptura() {

        if (Server::RequestMethod("POST")) {

            try {

                //Recibe los datos de formulario en array asociativo
                $data = Server::post();

                if(!empty($data)){
                    
                    //Trae todos las columnas de una tabla juridico o natural
                    if($data["tipo_cliente"] == "NAT"){
                        $columnsSQL = $this->_global->getColumnsTable('cliente_sarlaft_natural');
                    }else if($data["tipo_cliente"] == "JUR"){
                        $columnsSQL = $this->_global->getColumnsTable('cliente_sarlaft_juridico');
                    }
                    if(!isset($columnsSQL["error"])){

                        $dataQuery = Helpers::formatData($columnsSQL,$data);

                        if(!empty($dataQuery)){

                            //Verifica que halla llegado la identificacion del formulario ID
                            if (!is_null($data["captura_sarlaft_id"]) && !empty(trim($data["captura_sarlaft_id"])) && $data["captura_sarlaft_id"] != 0){

                                // ID del documento que se esta capturando
                                $dataQuery["id"] = $data["captura_sarlaft_id"];

                                if(isset($data["cliente"]) && isset($dataQuery["cliente"])){

                                    if(isset($data['estado_form_id'])){

                                        /*===================================================================
                                        =            VALIDACION CAMBIO DE ESTADO PROCESO CLIENTE            =
                                        ===================================================================*/

                                            // Consulta que exista un formulario SARLAFT con el codigo
                                            $check_list_sarlaft = $this->_clientes->getInfoFileByClientId($dataQuery['cliente'],'FCC');

                                            // Verifica la repuesta de la consulta del formulario
                                            if(!$check_list_sarlaft){

                                                $dataQuery["chk_formulario_sarlaft"] = 0;
                                                $dataQuery["chk_documentos"] = 0;
                                                $resultado_formulario = 12; //ID DEL ESTADO PENDIENTE SARLAFT
                                            }else{
                                                
                                                $dataQuery["chk_formulario_sarlaft"] = 1; //Checkea el campo de verificacion del documento sarlaft
                                                $dataQuery["chk_documentos"] = 1; //Checkea que los documnetos en el sistema estan completos

                                                if(!isset($data['llamada_cliente_sarlaft']) || (strtolower($data['llamada_cliente_sarlaft']) != 'modificacion')){

                                                    /*==========================================================
                                                    =            Section VALIDACION CAMBIO DE ESTADO            =
                                                    ============================================================*/

                                                        //Verifica si el estado del documento se encuentra en estado: PROCESO_CAPTURA, PENDIENTE(FIRMA,HUELLA,ENTREVISTA)
                                                        if(in_array($data['estado_form_id'],[1])){

                                                            $resultado_formulario = 6; // ID DEL ESTADO COMPLETITUD

                                                            //Verifica los campos requeridos para pasar al proceso de VERIFICACIÓN
                                                            $campos_requeridos = $this->_clientes->camposRequeridos($data["tipo_cliente"]);
                                                            
                                                            $resultado_completitud_verificacion = array(
                                                                'campos_vacios' => array()
                                                            );

                                                            //Recorre los campos requeridos del documentos para continuar el proceso
                                                            foreach ($campos_requeridos as $requerido){

                                                                if (isset($data[$requerido])){

                                                                    if($data[$requerido] == ""){
                                                                        array_push($resultado_completitud_verificacion['campos_vacios'],$requerido);
                                                                    }

                                                                }
                                                            }

                                                            if($data["tipo_cliente"] == 'NAT'){

                                                                if($data["trabaja_actualmente"] == 1){

                                                                    if($data["empresa_donde_trabaja"] == ""){ 
                                                                        array_push($resultado_completitud_verificacion['campos_vacios'],'empresa_donde_trabaja'); 
                                                                    }

                                                                    if($data["departamento_empresa"] == ""){  
                                                                        array_push($resultado_completitud_verificacion['campos_vacios'],'departamento_empresa'); 
                                                                    }

                                                                    if($data["ciudad_empresa"] == ""){ 
                                                                        array_push($resultado_completitud_verificacion['campos_vacios'],'ciudad_empresa');  
                                                                    }

                                                                    if($data["direccion_empresa"] == ""){  
                                                                        array_push($resultado_completitud_verificacion['campos_vacios'],'direccion_empresa'); 
                                                                    }
                                                                }
                                                            }

                                                            //Valida que si se dio en anexo_ppes, anexo_accionistas, anexo_subaccionistas estos llegue con datos
                                                            if($data["anexo_preguntas_ppes"] && $data["anexo_preguntas_ppes"] == 1){

                                                                $result_anexo_ppes = 0;

                                                                foreach ($data['anexo_ppes'] as $anexo_ppes) {

                                                                    if(array_filter($anexo_ppes)){

                                                                        $result_anexo_ppes++;
                                                                    }
                                                                }

                                                                if($result_anexo_ppes == 0){
                                                                    array_push($resultado_completitud_verificacion['campos_vacios'], 'anexo_ppes');
                                                                }
                                                            }else if(isset($data["anexo_accionistas"]) && $data["anexo_accionistas"] == 1){

                                                                $result_anexo_accionistas = 0;
                                                                foreach ($data['anexo_ppe_accionistas'] as $anexo_ppe_accionistas) {
                                                                    if(array_filter($anexo_ppe_accionistas)){
                                                                        $result_anexo_accionistas++;
                                                                    }
                                                                }

                                                                if($result_anexo_accionistas == 0){
                                                                    array_push($resultado_completitud_verificacion['campos_vacios'], 'anexo_ppe_accionistas');
                                                                }

                                                                if(isset($data["anexo_sub_accionistas"]) && $data["anexo_sub_accionistas"] == 1){

                                                                    $result_anexo_sub_accionistas = 0;
                                                                    foreach ($data['anexo_accionistas_sub_accionista'] as $anexo_accionistas_sub_accionista) {
                                                                        if(array_filter($anexo_accionistas_sub_accionista)){
                                                                            $result_anexo_sub_accionistas++;
                                                                        }
                                                                    }

                                                                    if($result_anexo_sub_accionistas == 0){
                                                                        array_push($resultado_completitud_verificacion['campos_vacios'], 'anexo_accionistas_sub_accionista');
                                                                    }
                                                                }
                                                            }

                                                            //Verifica si quedaron campos vacios
                                                            if(empty(array_filter($resultado_completitud_verificacion['campos_vacios']))){

                                                                if($data["verificacion"] == 1){

                                                                    $resultado_formulario = 3; // ID DEL ESTADO FINALIZADO
                                                                }else{

                                                                    $resultado_formulario = 5; // ID DEL ESTADO VERIFICACION
                                                                }
                                                            }
                                                        }

                                                        //Verifica si el estado del documento se encuentra en estado: COMPLETITUD, VERIIFICACION O PENDIENTE TIPOLOGIA
                                                        if(in_array($data['estado_form_id'],[6,5])){

                                                            if(isset($data["tipologia"]) && !empty($data["tipologia"])){

                                                                //Verifica si la tipologia que escoge es solo TELEFONICA
                                                                if(in_array('TELEFONICA',$data["tipologia"])){

                                                                    //Verifica que llegue información sobre el estaod de la tipologia
                                                                    if(isset($data["estado_tipologia"])){

                                                                        if(isset($data['no_intentos'])){

                                                                            if($data['tipo_cliente'] == 'NAT'){

                                                                                $cliente = $this->_clientes->getInfoClienteNaturalByClienteId($data['cliente']);
                                                                            }else if($data['tipo_cliente'] == 'JUR'){

                                                                                $cliente = $this->_clientes->getInfoClienteJuridicoByClienteId($data['cliente']);
                                                                            }

                                                                            if($data['estado_form_id'] == 6){

                                                                                $resultado_formulario = 5;
                                                                            }else{

                                                                                $resultado_formulario = 3; // ID ESTADO FORMULARIO FINALIZADO
                                                                            }

                                                                            // aisgna el estado a la varible de respuesta del formulario
                                                                            $no_intentos = ($data['no_intentos']+1);

                                                                            $verificacion_completitud = $this->_clientes->getGestionCompletitud($dataQuery["cliente"],$data["fecha_diligenciamiento"]);

                                                                            // $interval = date_diff(date_create(date('Y-m-d')), date_create($process_completitud_verificacion['FECHA_GESTION']),false)->format('%i');

                                                                            if(in_array($data['estado_form_id'], [5]) && $verificacion_completitud){
                                                                                $no_intentos--;
                                                                            }/*else if($interval > 300){
                                                                                $no_intentos++; // SUMA AUTOMATICAMENTE UN INTENTO MAS
                                                                            }*/

                                                                            //Obtiene la informacion de los campos requeridos para poder cambias de proceso
                                                                            $campos_requeridos = $this->_clientes->camposRequeridos($data["tipo_cliente"]);
                                                                            $resultado_completitud_verificacion = array(
                                                                                'campos_vacios' => array(),
                                                                                'campos_completados' => array()
                                                                            );

                                                                            // Recorre los campos requeridos 
                                                                            foreach ($campos_requeridos as $requerido) {

                                                                                if(isset($data[$requerido])){

                                                                                    if($data[$requerido] == ""){

                                                                                        array_push($resultado_completitud_verificacion['campos_vacios'],$requerido);
                                                                                    }else{

                                                                                        array_push($resultado_completitud_verificacion['campos_completados'],$requerido);
                                                                                    }
                                                                                }
                                                                            }

                                                                            if($data["tipo_cliente"] == 'NAT'){

                                                                                if(isset($data["trabaja_actualmente"]) && $data["trabaja_actualmente"] == 1){

                                                                                    if(isset($data["empresa_donde_trabaja"]) && $data["empresa_donde_trabaja"] == ""){ 
                                                                                        array_push($resultado_completitud_verificacion['campos_vacios'],'empresa_donde_trabaja'); 
                                                                                    }

                                                                                    if(isset($data["departamento_empresa"]) && $data["departamento_empresa"] == ""){  
                                                                                        array_push($resultado_completitud_verificacion['campos_vacios'],'departamento_empresa'); 
                                                                                    }

                                                                                    if(isset($data["ciudad_empresa"]) && $data["ciudad_empresa"] == ""){ 
                                                                                        array_push($resultado_completitud_verificacion['campos_vacios'],'ciudad_empresa');  
                                                                                    }

                                                                                    if(isset($data["direccion_empresa"]) && $data["direccion_empresa"] == ""){  
                                                                                        array_push($resultado_completitud_verificacion['campos_vacios'],'direccion_empresa'); 
                                                                                    }
                                                                                }
                                                                            }

                                                                            if(isset($data["anexo_preguntas_ppes"]) && $data["anexo_preguntas_ppes"] == 1){

                                                                                $result_anexo_ppes = 0;
                                                                                foreach ($data['anexo_ppes'] as $anexo_ppes) {
                                                                                    if(array_filter($anexo_ppes)){
                                                                                        $result_anexo_ppes++;
                                                                                    }
                                                                                }

                                                                                if($result_anexo_ppes == 0){
                                                                                    array_push($resultado_completitud_verificacion['campos_vacios'], 'anexo_ppes');
                                                                                }
                                                                            }

                                                                            if(isset($data["anexo_accionistas"]) && $data["anexo_accionistas"] == 1 && $resultado_formulario != 16){

                                                                                $result_anexo_accionistas = 0;
                                                                                foreach ($data['anexo_ppe_accionistas'] as $anexo_ppe_accionistas) {
                                                                                    if(array_filter($anexo_ppe_accionistas)){
                                                                                        $result_anexo_accionistas++;
                                                                                    }
                                                                                }

                                                                                if($result_anexo_accionistas == 0){
                                                                                    array_push($resultado_completitud_verificacion['campos_vacios'], 'anexo_ppe_accionistas');
                                                                                }

                                                                                if($resultado_formulario != 15 && isset($data["anexo_sub_accionistas"]) && $data["anexo_sub_accionistas"] == 1){

                                                                                    $result_anexo_sub_accionistas = 0;
                                                                                    foreach ($data['anexo_accionistas_sub_accionista'] as $anexo_accionistas_sub_accionista) {
                                                                                        if(array_filter($anexo_accionistas_sub_accionista)){
                                                                                            $result_anexo_sub_accionistas++;
                                                                                        }
                                                                                    }

                                                                                    if($result_anexo_sub_accionistas == 0){
                                                                                        array_push($resultado_completitud_verificacion['campos_vacios'], 'anexo_ppe_accionistas');
                                                                                    }
                                                                                }
                                                                            }

                                                                            // Verifica si no hay campos vacios
                                                                            if(empty(array_filter($resultado_completitud_verificacion['campos_vacios']))){

                                                                                if(in_array($data['estado_tipologia'],[7,6,1])){

                                                                                    $resultado_formulario = 3; // ID ESTADO FORMULARIO FINALIZADO
                                                                                }else if(!in_array($data['estado_tipologia'],[8])){

                                                                                    $resultado_formulario = $data['estado_form_id'];

                                                                                    if(in_array($data['estado_form_id'], [5]) && $verificacion_completitud){
                                                                                        $no_intentos++;
                                                                                    }

                                                                                    if($no_intentos >= 3){

                                                                                        $resultado_formulario = 14; // ID ESTADO FINALIZADO POR INTENTOS
                                                                                    }
                                                                                }else{  

                                                                                    if($data['estado_form_id'] == 6){

                                                                                        if($cliente['verificacion'] == 1){

                                                                                            $resultado_formulario = 3; // ID ESTADO FORMULARIO FINALIZADO
                                                                                        }
                                                                                    }
                                                                                }
                                                                            }else{

                                                                                $resultado_formulario = $data['estado_form_id'];

                                                                                if(in_array($data['estado_tipologia'],[8,7,6,1])){

                                                                                    $resultado_formulario = 3; // RESULTADO DEFAULT FINALIZADO

                                                                                    if($data['estado_form_id'] == 6 && in_array($data['estado_tipologia'],[8])){
                                                                                        
                                                                                        $resultado_formulario = 5;

                                                                                        if($cliente['verificacion'] == 1){

                                                                                            $resultado_formulario = 3; // ID ESTADO FORMULARIO FINALIZADO
                                                                                        }
                                                                                    }
                                                                                }else{

                                                                                    if(in_array($data['estado_form_id'], [5]) && $verificacion_completitud){
                                                                                        $no_intentos++;
                                                                                    }

                                                                                    //Verifica si la cantidad de intentos es mayor a 3
                                                                                    if($no_intentos >= 3){

                                                                                        $resultado_formulario = 14; // ID DEL ESTADO FINALIZADO POR INTENTOS
                                                                                    }
                                                                                }
                                                                            }
                                                                        }else{
                                                                            throw new Exception("LA CANTIDAD DE INTENTOS NO LLEGO AL SISTEMA");
                                                                        }
                                                                    }else{
                                                                        throw new Exception('NO SE ENVIO EL ESTADO DE LA TIPOLOGIA');
                                                                    }
                                                                }else{
                                                                    throw new Exception("NO EXISTE LA TIPOLOGIA QUE INGRESO: " . $data['tipologia']);
                                                                }
                                                            }else{
                                                                throw new Exception("NO SE OBTUVO EL VALOR DE LA TIPOLOGIA");
                                                            }
                                                        }

                                                        if(!in_array($data['estado_form_id'], [1,6,5])){
                                                            
                                                            $resultado_formulario = 3;
                                                        }
                                                    /*=====  End of Section VALIDACION CAMBIO DE ESTADO  ======*/
                                                }else{

                                                    $resultado_formulario = $data['estado_form_id'];
                                                }
                                            }

                                            if(in_array($resultado_formulario,[3,14,9,15,16]) || (isset($data['llamada_cliente_sarlaft']) && strtolower($data['llamada_cliente_sarlaft']) == 'modificacion')){

                                                $resultado_formulario = 3; // ID ESTADO FORMULARIO FINALIZADO

                                                if($data['tipo_cliente'] == 'NAT'){

                                                    $cliente = $this->_clientes->getInfoClienteNaturalByClienteId($data['cliente']);
                                                }else if($data['tipo_cliente'] == 'JUR'){

                                                    $cliente = $this->_clientes->getInfoClienteJuridicoByClienteId($data['cliente']);
                                                }

                                                if(!isset($cliente['error'])){

                                                    $firma = isset($data['firma']) ? $data['firma'] : $cliente['firma'];
                                                    $huella = isset($data['huella']) ? $data['huella'] : $cliente['huella'];
                                                    $entrevista = isset($data['entrevista']) ? $data['entrevista'] : $cliente['entrevista'];

                                                    if($firma != 1 || $huella != 1 || $entrevista != 1){

                                                        $resultado_formulario = 11; // ESTADO PENDIENTE (FIRMA,HUELLA,ENTREVISTA)
                                                    }else{

                                                        $anexo_preguntas_ppes = isset($data['anexo_preguntas_ppes']) ? $data['anexo_preguntas_ppes'] : $cliente['anexo_preguntas_ppes'];

                                                        if($anexo_preguntas_ppes == 1){

                                                            $result_anexo_ppes = 0;

                                                            if(isset($data['anexo_ppes'])){

                                                                foreach ($data['anexo_ppes'] as $anexo_ppes) {
                                                                    if(array_filter($anexo_ppes)){
                                                                        $result_anexo_ppes++;
                                                                    }
                                                                }
                                                            }else{

                                                                if($this->_clientes->getAllAnexosPPEClientById($data['cliente'])){
                                                                    $result_anexo_ppes = count($this->_clientes->getAllAnexosPPEClientById($data['cliente']));
                                                                }
                                                            }

                                                            if($result_anexo_ppes == 0){
                                                                $resultado_formulario = 16; // ID ESTADO FORMULARIO PENDIENTE ANEXOS
                                                            }
                                                        }

                                                        if($data['tipo_cliente'] == 'JUR'){

                                                            $anexo_accionistas = isset($data['anexo_accionistas']) ? $data['anexo_accionistas'] : $cliente['anexo_accionistas'];

                                                            if($anexo_accionistas == 1 && $resultado_formulario != 16){

                                                                $result_anexo_accionistas = 0;

                                                                if(isset($data['anexo_ppe_accionistas'])){
                                                                    foreach ($data['anexo_ppe_accionistas'] as $anexo_ppe_accionistas) {
                                                                        if(array_filter($anexo_ppe_accionistas)){
                                                                            $result_anexo_accionistas++;
                                                                        }
                                                                    }
                                                                }else{

                                                                    if($this->_clientes->getAccionistasClienteById($data['cliente'])){
                                                                        $result_anexo_accionistas = count($this->_clientes->getAccionistasClienteById($data['cliente']));
                                                                    }
                                                                }

                                                                if($result_anexo_accionistas == 0){
                                                                    $resultado_formulario = 15; // ID ESTADO FORMULARIO PENDIENTE ACCIONISTAS
                                                                }

                                                                $anexo_sub_accionistas = isset($data["anexo_sub_accionistas"]) ? $data["anexo_sub_accionistas"] : $cliente['anexo_sub_accionistas'];

                                                                if($resultado_formulario != 15 && $anexo_sub_accionistas == 1){

                                                                    $result_anexo_sub_accionistas = 0;

                                                                    if(isset($data['anexo_accionistas_sub_accionista'])){

                                                                        foreach ($data['anexo_accionistas_sub_accionista'] as $anexo_accionistas_sub_accionista) {
                                                                            if(array_filter($anexo_accionistas_sub_accionista)){
                                                                                $result_anexo_sub_accionistas++;
                                                                            }
                                                                        }
                                                                    }else{

                                                                        if($this->_clientes->getSubAccionistasClienteById($data['cliente'])){
                                                                            $result_anexo_sub_accionistas = count($this->_clientes->getSubAccionistasClienteById($data['cliente']));
                                                                        }
                                                                    }

                                                                    if($result_anexo_sub_accionistas == 0){
                                                                       $resultado_formulario = 15; // ID ESTADO FORMULARIO PENDIENTE ACCIONISTAS
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }

                                                    if($resultado_formulario == 3 && (isset($no_intentos) && $no_intentos >= 3)){
                                                        $resultado_formulario = 14; // ID ESTADO FORMULARIO FINALIZADO POR INTENTOS
                                                    }
                                                }else{
                                                    
                                                    throw new Exception('la consulta del cliente genero el siguiente error: ' . $cliente['error']);
                                                }
                                            }
                                        /*=====  End of VALIDACION CAMBIO DE ESTADO PROCESO CLIENTE  ======*/
                                        
                                        // Actualiza el cliente con la informacion del formulario
                                        $resultadoSaveCaptura = $this->_clientes->updateClient($data["tipo_cliente"],$dataQuery);

                                        //Verifica que no halla generado error en el momento de guardar el cliente dentro del sistema
                                        if(!isset($resultadoSaveCaptura['error'])){
                                            /**
                                             * Guarda los valores correspondientes a los campos de verificacion
                                             */
                                            if ($data["estado_form_id"] === "5") { //Si el estado del formulario es VERIFICACION
                                                if ($data['tipo_cliente'] == 'JUR') {
                                                    $existVerificado = $this->_clientes->getVerificadoSarlaftJuridico($dataQuery['id']);
                                                    $columnsVerificado = $this->_global->getColumnsTable('cliente_sarlaft_juridico_verificado');
                                                    if (!isset($columnsVerificado["error"])) {
                                                        $dataQueryVerificado = Helpers::formatData($columnsVerificado,$data);
                                                        $dataQueryVerificado['cliente_sarlaft_juridico_id'] = $dataQuery['id'];
                                                        if (!empty($dataQueryVerificado)) {
                                                            /**
                                                             * Si ya existe un registro en cliente_sarlaft_juridico_verificado
                                                             */
                                                            if (!isset($existVerificado["error"]) && (!is_null($existVerificado) && !empty($existVerificado))) {
                                                                $dataQueryVerificado['id'] = $existVerificado['id'];
                                                                $updateQueryVerificado = $this->_crud->Update('cliente_sarlaft_juridico_verificado', $dataQueryVerificado);    
                                                            } else if (!isset($existVerificado["error"]) && !$existVerificado) { //Si no existe registro
                                                                $saveQueryVerificado = $this->_crud->Save('cliente_sarlaft_juridico_verificado', $dataQueryVerificado);
                                                            } else {
                                                                throw new Exception('HUBO UN ERROR AL OBTENER LOS DATOS DE VERIFICACION POR: ' . $existVerificado['error']);
                                                            }
                                                        }
                                                    }                                            
                                                } else {
                                                    $existVerificado = $this->_clientes->getVerificadoSarlaftNatural($dataQuery['id']);
                                                    $columnsVerificado = $this->_global->getColumnsTable('cliente_sarlaft_natural_verificado');
                                                    if (!isset($columnsVerificado["error"])) {
                                                        $dataQueryVerificado = Helpers::formatData($columnsVerificado,$data);
                                                        $dataQueryVerificado['cliente_sarlaft_natural_id'] = $dataQuery['id'];
                                                        if (!empty($dataQueryVerificado)) {
                                                            /**
                                                             * Si ya existe un registro en cliente_sarlaft_natural_verificado
                                                             */
                                                            if (!isset($existVerificado["error"]) && (!is_null($existVerificado) && !empty($existVerificado))) {
                                                                $updateQueryVerificado = $this->_crud->Update('cliente_sarlaft_natural_verificado', $dataQueryVerificado);    
                                                            } else if (!isset($existVerificado["error"]) && is_null($existVerificado)) { //Si no existe registro
                                                                $saveQueryVerificado = $this->_crud->Save('cliente_sarlaft_natural_verificado', $dataQueryVerificado);
                                                            } else {
                                                                throw new Exception('HUBO UN ERROR AL OBTENER LOS DATOS DE VERIFICACION POR: ' . $existVerificado['error']);
                                                            }
                                                        }
                                                    }
                                                }                                                
                                            }
                                            
                                            //Verifica si el estado en el que llego el documento es: PROCESO CAPTURA, PENDIENTE(FIRMA,HUELLA,ENTREVISTA),MIGRACION,FINALIZADO
                                            if(in_array($data['estado_form_id'],[1,11,13,3])){

                                                //Guarda la gestion del cliente dentro de la tabla de gestion "gestion_clientes_captura"
                                                $saveGestionCaptura = $this->_crud->Save(
                                                    'gestion_clientes_captura',
                                                    array(
                                                        'GESTION_USUARIO_ID' => $_SESSION["Mundial_authenticate_user_id"],
                                                        'GESTION_CLIENTE_ID' => $data["cliente"],
                                                        'GESTION_FECHA_DILIGENCIAMIENTO' => $data["fecha_diligenciamiento"]
                                                    )
                                                );

                                                //Verifica que no halla habido error al guardar la gestion dele cliente
                                                if(isset($GestionCaptura['error'])){
                                                    throw new Exception('HUBO UN ERROR AL GUARDAR LA GESTION DE LA CAPTURA POR: ' . $saveGestionCaptura['error']);
                                                }
                                            }

                                            //Verifica si el estado en el que llego el documento es: COMPLETITUD, VERIFICACION, PENDIENTE TIPOLOGIA
                                            if(in_array($data['estado_form_id'],[6,5])){

                                                //Datos de la gestion cuando se realiza el proceso
                                                $dataGestionCompletitud = array(
                                                    'GESTION_USUARIO_ID'                => $_SESSION["Mundial_authenticate_user_id"],
                                                    'GESTION_CLIENTE_ID'                => $data['cliente'],
                                                    'GESTION_FECHA_DILIGENCIAMIENTO'    => date('Y-m-d',strtotime($data["fecha_diligenciamiento"])),
                                                    'GESTION_TIPOLOGIA'                 => implode(',',$data["tipologia"]),
                                                    'GESTION_ESTADO_TIPOLOGIA_ID'       => !empty($data["estado_tipologia"]) ? $data["estado_tipologia"] : NULL,
                                                    'GESTION_NO_INTENTOS'               => isset($no_intentos) ? $no_intentos : 0,
                                                    'GESTION_OBSERVACIONES'             => isset($data["observaciones_tipologia"]) ? $data["observaciones_tipologia"] : NULL,
                                                    'GESTION_CAMPOS_VACIOS'             => isset($resultado_completitud_verificacion['campos_vacios']) ? (!empty($resultado_completitud_verificacion['campos_vacios']) ? implode(',',$resultado_completitud_verificacion['campos_vacios']) : NULL) : NULL,
                                                    'GESTION_CAMPOS_COMPLETADOS'        => isset($resultado_completitud_verificacion['campos_completados']) ? (!empty($resultado_completitud_verificacion['campos_completados']) ? implode(',',$resultado_completitud_verificacion['campos_completados']) : NULL) : NULL,
                                                    'GESTION_PROCESO_ID'                => $data['estado_form_id']
                                                );

                                                $saveGestionCompleitud = $this->_crud->Save('gestion_clientes_completitud_verificacion',$dataGestionCompletitud);                                                
                                            }
                                            
                                            //Verifica el cambio de estado del formulario y lo guarda en el la tabla de "zr_estado_proceso_clientes_sarlaft"
                                            if(($resultado_formulario != $data['estado_form_id']) || ((isset($data['llamada_cliente_sarlaft']) && in_array(strtolower($data['llamada_cliente_sarlaft']),['inbound','outbound'])) && (isset($data['numero_llamada_inbound']) && $data['numero_llamada_inbound'] != ""))){

                                                //Obtener numero de radicado de formulario - JAV01 - 20180424
                                                $numeroRadicado = $this->_clientes->getRadicacionId($data['cliente'], date('Y-m-d',strtotime($data["fecha_diligenciamiento"])));

                                                //Datos en el momento de guardar una gestion del docuemnto
                                                $saveProcesoFormulario = $this->_crud->Save(
                                                    'zr_estado_proceso_clientes_sarlaft',
                                                    array(
                                                        'PROCESO_USUARIO_ID' => $_SESSION["Mundial_authenticate_user_id"],
                                                        'PROCESO_CLIENTE_ID' => $data['cliente'],
                                                        'PROCESO_FECHA_DILIGENCIAMIENTO' => date('Y-m-d',strtotime($data["fecha_diligenciamiento"])),
                                                        'ESTADO_PROCESO_ID' => $resultado_formulario,
                                                        'PROCESO_INOUTBOUND' => isset($data['llamada_cliente_sarlaft']) ? $data['llamada_cliente_sarlaft'] : NULL,
                                                        'PROCESO_NUM_INOUTBOUND' => isset($data['numero_llamada_inbound']) ? $data['numero_llamada_inbound'] : NULL,
                                                        'RADICACION_ID' => $numeroRadicado['id']
                                                    )
                                                );

                                                //Verifica que no halla ocurrido alguna Excepcion en el momento de guarda la gestion del cliente
                                                if(isset($saveProcesoFormulario['error'])){
                                                    throw new Exception('HUBO UN ERROR AL GUARDAR EL PROCESO DEL CLIENTE POR : ' . $saveProcesoFormulario['error']);
                                                }
                                            }

                                            /*===================================
                                            =            ANEXOS PPES            =
                                            ===================================*/

                                                //Guarda los datos obtenidos en el formulario de ppes si en este caso estos datos no llegan vacios
                                                if(isset($data["anexo_preguntas_ppes"]) && $data["anexo_preguntas_ppes"] == 1 && isset($data["anexo_ppes"])){

                                                    //Obtiene los nombres de las columnas
                                                    $columnsSQLAnexoPEPS = $this->_global->getColumnsTable('zr_anexos_ppes');

                                                    foreach ($data["anexo_ppes"] as $valueAnexosCliente) {

                                                        //Combina los valores con los nombres de las columnas
                                                        $TempAnexos = Helpers::formatData($columnsSQLAnexoPEPS,$valueAnexosCliente);

                                                        if(!empty(array_filter($TempAnexos))){

                                                            $TempAnexos["cliente_id"] = $dataQuery["cliente"];

                                                            if(isset($valueAnexosCliente["anexo_id"])){

                                                                $TempAnexos["id"] = $valueAnexosCliente["anexo_id"];

                                                                $updateAnexos = $this->_clientes->updateAnexos('zr_anexos_ppes',$TempAnexos);

                                                                if(isset($updateAnexos['error'])){
                                                                    throw new Exception('No se pudo actualizar la informacion del anexo: ' . $TempAnexos['ppes_nombre'] . ' por: ' . $updateAnexos['error']);
                                                                }
                                                            }else{

                                                                $saveAnexos = $this->_clientes->saveAnexos('zr_anexos_ppes',$TempAnexos);

                                                                if(isset($saveAnexos['error'])){
                                                                    throw new Exception('No se pudo registrar la informacion del anexo: ' . $TempAnexos['ppes_nombre'] . ' por: ' . $saveAnexos['error']);
                                                                }
                                                            }
                                                        }
                                                    }
                                                }

                                            /*=====  End of ANEXOS PPES  ======*/


                                            if($data["tipo_cliente"] == "JUR"){

                                                /*================================================================
                                                =            ANEXO ACCIONISTAS O ANEXO SUB ACCIONISTAS           =
                                                =================================================================*/

                                                    $anexo_accionistas = 0;

                                                    //Guarda los datos obtenidos en el formulario de ppes si en este caso estos datos no llegan vacios
                                                    if(isset($data["anexo_accionistas"]) && $data["anexo_accionistas"] == 'SI' && isset($data["anexo_ppe_accionistas"])){

                                                        //Obtiene los nombres de las columnas
                                                        $columnsSQLAccionistas = $this->_global->getColumnsTable('accionistas');

                                                        $accionistaActual = 0;

                                                        foreach ($data["anexo_ppe_accionistas"] as $valueAnexosAccionistasCliente) {

                                                            $accionistaActual++;

                                                            //Combina los valores con los nombres de las columnas
                                                            $TempAnexoAccionistas = Helpers::formatData($columnsSQLAccionistas,$valueAnexosAccionistasCliente);

                                                            if(!empty(array_filter($TempAnexoAccionistas))){

                                                                $TempAnexoAccionistas["cliente_id"] = $data["cliente"];

                                                                if(isset($valueAnexosAccionistasCliente["anexo_accionista_id"])){

                                                                    $TempAnexoAccionistas["id"] = $valueAnexosAccionistasCliente["anexo_accionista_id"];

                                                                    $updateAccionista = $this->_clientes->updateAnexos('accionistas',$TempAnexoAccionistas);

                                                                    if(!isset($updateAccionista['error'])){
                                                                        $anexo_accionistas++;
                                                                        if ($data['estado_form_id'] == '5') {
                                                                            $columnsAccionistasVerificados = $this->_global->getColumnsTable('accionistas_verificados');
                                                                            if (!isset($columnsAccionistasVerificados['error'])) {
                                                                                $accionistasVerificados = [];
                                                                                /**
                                                                                 * Obtiene los valores de verificación del accionista correspondiente
                                                                                 */
                                                                                foreach ($data as $keyData => $valueData) {
                                                                                    if (substr_count($keyData, 'verificacion_accionista_'.$accionistaActual)) {
                                                                                        $accionistasVerificados[$keyData] = $valueData;
                                                                                    }
                                                                                }
                                                                                /**
                                                                                 * Obtiene los datos de verificado y los guarda en la tabla
                                                                                 */
                                                                                if (isset($accionistasVerificados) && !empty($accionistasVerificados)) {
                                                                                    /**
                                                                                     * Reemplazo las claves para que concuerden con las columnas de la tabla
                                                                                     */
                                                                                    foreach ($accionistasVerificados as $keyAccionista => $valueAccionista) {
                                                                                        if (substr_count($keyAccionista, 'documento')) {
                                                                                            $accionistasVerificados[str_replace($keyAccionista, 'verificacion_accionista_documento', $keyAccionista)] = $valueAccionista;
                                                                                        } else if (substr_count($keyAccionista, 'nombres')) {
                                                                                            $accionistasVerificados[str_replace($keyAccionista, 'verificacion_accionista_nombres', $keyAccionista)] = $valueAccionista;
                                                                                        } else if (substr_count($keyAccionista, 'participacion')) {
                                                                                            $accionistasVerificados[str_replace($keyAccionista, 'verificacion_accionista_participacion', $keyAccionista)] = $valueAccionista;
                                                                                        }
                                                                                        unset($accionistasVerificados[$keyAccionista]);
                                                                                    }
                                                                                    /**
                                                                                     * Si existe el id para actualizar en la tabla
                                                                                     */
                                                                                    if (isset($data['verificacion_accionista_'.$accionistaActual.'_id']) && !empty($data['verificacion_accionista_'.$accionistaActual.'_id'])) {
                                                                                        $accionistasVerificados['id'] = $data['verificacion_accionista_'.$accionistaActual.'_id'];
                                                                                        $updateAccionistaVerificado = $this->_crud->Update('accionistas_verificados', $accionistasVerificados);
                                                                                    }
                                                                                }
                                                                            }
                                                                        }
                                                                    }else{
                                                                        throw new Exception('No se pudo actualizar la informacion del accionista: ' . $TempAnexoAccionistas['accionista_documento'] . ' por: ' . $updateAccionista['error']);
                                                                    }
                                                                }else{
                                                                    $saveAccionista = $this->_clientes->saveAnexos('accionistas',$TempAnexoAccionistas, true);
                                                                    if(!isset($saveAccionista['error'])){
                                                                        $anexo_accionistas++;
                                                                        if ($data['estado_form_id'] == '5') {
                                                                            $columnsAccionistasVerificados = $this->_global->getColumnsTable('accionistas_verificados');
                                                                            if (!isset($columnsAccionistasVerificados['error'])) {
                                                                                $accionistasVerificados = [];
                                                                                /**
                                                                                 * Obtiene los valores de verificación del accionista correspondiente
                                                                                 */
                                                                                foreach ($data as $keyData => $valueData) {
                                                                                    if (substr_count($keyData, 'verificacion_accionista_'.$accionistaActual)) {
                                                                                        $accionistasVerificados[$keyData] = $valueData;
                                                                                    }
                                                                                }
                                                                                /**
                                                                                 * Obtiene los datos de verificado y los guarda en la tabla
                                                                                 */
                                                                                if (isset($accionistasVerificados) && !empty($accionistasVerificados)) {
                                                                                    /**
                                                                                     * Reemplazo las claves para que concuerden con las columnas de la tabla
                                                                                     */
                                                                                    foreach ($accionistasVerificados as $keyAccionista => $valueAccionista) {
                                                                                        if (substr_count($keyAccionista, 'documento')) {
                                                                                            $accionistasVerificados[str_replace($keyAccionista, 'verificacion_accionista_documento', $keyAccionista)] = $valueAccionista;
                                                                                        } else if (substr_count($keyAccionista, 'nombres')) {
                                                                                            $accionistasVerificados[str_replace($keyAccionista, 'verificacion_accionista_nombres', $keyAccionista)] = $valueAccionista;
                                                                                        } else if (substr_count($keyAccionista, 'participacion')) {
                                                                                            $accionistasVerificados[str_replace($keyAccionista, 'verificacion_accionista_participacion', $keyAccionista)] = $valueAccionista;
                                                                                        }
                                                                                        unset($accionistasVerificados[$keyAccionista]);
                                                                                    }
                                                                                    $accionistasVerificados['accionista_id'] = $saveAccionista['LAST_ID'];
                                                                                    $saveAccionistaVerificado = $this->_crud->Save('accionistas_verificados', $accionistasVerificados);
                                                                                }
                                                                            }
                                                                        }
                                                                    }else{
                                                                        throw new Exception('No se pudo registrar la informacion del accionista: ' . $TempAnexoAccionistas['accionista_documento'] . ' por: ' . $saveAccionista['error']);
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                
                                                    if($anexo_accionistas){

                                                        // guarda los datos obtenidos en el formulario de ppes si en este caso estos datos no llegan vacios
                                                        if(isset($data["anexo_accionistas_sub_accionista"])){

                                                            //Obtiene los nombre de las columnas
                                                            $columnsSQLSubAccionistas = $this->_global->getColumnsTable('sub_accionistas');

                                                            foreach ($data["anexo_accionistas_sub_accionista"] as $valueAnexosSubAccionistasCliente) {

                                                                //Combina los valores con los nombres de las columnas
                                                                $TempAnexoSubAccionistas = Helpers::formatData($columnsSQLSubAccionistas,$valueAnexosSubAccionistasCliente);

                                                                if(!empty(array_filter($TempAnexoSubAccionistas))){

                                                                    $TempAnexoSubAccionistas["cliente_id"] = $dataQuery["cliente"];

                                                                    if(isset($valueAnexosSubAccionistasCliente["anexo_sub_accionista_id"])){

                                                                        $TempAnexoSubAccionistas["id"] = $valueAnexosSubAccionistasCliente["anexo_sub_accionista_id"];

                                                                        $updateSubAccionista = $this->_clientes->updateAnexos('sub_accionistas',$TempAnexoSubAccionistas);

                                                                        if(isset($updateSubAccionista['error'])){
                                                                            throw new Exception('No se pudo actualizar la informacion del sub accionista: ' . $TempAnexoSubAccionistas['accionista_documento'] . ' por: ' . $updateSubAccionista['error']);
                                                                        }
                                                                    }else{

                                                                        $saveSubAccionistas = $this->_clientes->saveAnexos('sub_accionistas',$TempAnexoSubAccionistas);

                                                                        if(isset($saveSubAccionistas['error'])){
                                                                            throw new Exception('No se pudo actualizar la informacion del sub accionista: ' . $TempAnexoSubAccionistas['accionista_documento'] . ' por: ' . $saveSubAccionistas['error']);
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                /*=====  End of ANEXO ACCIONISTAS O ANEXO SUB ACCIONISTAS  ======*/
                                            }

                                            /*=========================================
                                            =            PRODUCTOS CLIENTE            =
                                            =========================================*/

                                                //Guarda los datos de los productos // 
                                                if(isset($data["productos_financieros"])){

                                                    //Obtiene los nombre de las columnas
                                                    $columnsSQLProFin = $this->_global->getColumnsTable('productos');

                                                    foreach ($data["productos_financieros"] as $dataProducto) {

                                                        //Combina los valores con los nombres de las columnas
                                                        $TempProFinancieros = Helpers::formatData($columnsSQLProFin,$dataProducto);

                                                        if(!empty(array_filter($TempProFinancieros))){

                                                            $TempProFinancieros["cliente_id"] = $dataQuery["cliente"];

                                                            if(isset($dataProducto["producto_id"])){

                                                                $TempProFinancieros["id"] = $dataProducto["producto_id"];

                                                                $updateProducto = $this->_clientes->updateAnexos('productos',$TempProFinancieros);

                                                                if(isset($updateProducto['error'])){
                                                                    throw new Exception('No se pudo actualizar la informacion del producto: ' . $TempProFinancieros['identificacion_producto'] . ' por: ' . $updateProducto['error']);
                                                                }
                                                            }else{

                                                                $saveProducto = $this->_clientes->saveAnexos('productos',$TempProFinancieros);
                                                                
                                                                if(isset($saveProducto['error'])){
                                                                    throw new Exception('No se pudo actualizar la informacion del producto: ' . $TempProFinancieros['identificacion_producto'] . ' por: ' . $saveProducto['error']);
                                                                } 
                                                            }
                                                        }
                                                    }
                                                }

                                            /*=====  End of PRODUCTOS CLIENTE  ======*/


                                            //Resultado respuesta al momento de guardar un cliente en el sistema
                                            $resultado = array();

                                            array_push($resultado,array(
                                                'type' => "STATES_OK", 
                                                'title' => 'Actualizado Con Exito!!!',
                                                'message' => 'Se actualizaron correctamente los datos de la captura'
                                            ));

                                            // Verifica si los documentos quedaron incompletos en el momento de subir la información
                                            if($dataQuery["chk_documentos"] == 0){

                                                array_push($resultado,array(
                                                    'type' => "STATES_ERROR", 
                                                    'title' => 'Faltan algunos documentos del cliente',
                                                    'message' => 'Por favor cargue los archivos faltantes queda en estado PENDIENTE'
                                                ));
                                            }

                                            //Verifica si todos los documentos del un cliente se encuentran OK
                                            if($dataQuery["chk_documentos"] == 1){

                                                array_push($resultado,array(
                                                    'type' => "STATES_OK", 
                                                    'title' => 'Cliente con todos los documentos',
                                                    'message' => 'Este cliente tienen todos los documentos al dia'
                                                ));
                                            }

                                            //Verifica si el documento quedo en el estado de: FINALIZADO POR TIPOLOGIA
                                            if($resultado_formulario == 9){

                                                array_push($resultado,array(
                                                    'type' => "STATES_ERROR", 
                                                    'title' => 'Finalizado',
                                                    'message' => 'Este cliente finalizo por el estado de la tipologia'
                                                ));
                                            }
                                            //Verifica si el documento quedo en el estado de: PENDIENTE(FIRMA,HUELLA,ENTREVISTA)
                                            if($resultado_formulario == 11){

                                                array_push($resultado,array(
                                                    'type' => "STATES_ERROR", 
                                                    'title' => 'pendiente',
                                                    'message' => 'Este cliente finalizo, pero queda pendiente por firma, huella o entrevista'
                                                ));
                                            }

                                            //Verifica si el documento quedo en el estado de: PENDIENTE POR TIPOLIGIA
                                            if($resultado_formulario == 10){
                                                array_push($resultado,array(
                                                    'type' => "STATES_ERROR", 
                                                    'title' => 'PENDIENTE TIPOLOGIA',
                                                    'message' => 'Algunos Campos quedaron vacios espere a ser reasignado'
                                                ));
                                            }

                                            //Retorna la informacion con la cabecera JSON como respuesta de la captura
                                            header('Content-Type: application/json');
                                            echo json_encode($resultado);
                                        }else{

                                            throw new Exception('NO SE GUARDO LA INFORMACION DEL CLIENTE POR: ' . $resultadoSaveCaptura['error']);
                                        }
                                    }else{

                                        throw new Exception('NO SE OBTUVO EL ESTADO DEL FORMULARIO');
                                    }
                                }else{

                                    throw new Exception('NO SE OBTUVO LA IDENTIFICACION DEL CLIENTE');
                                }
                            }else{

                                throw new Exception('NO SE OBTUVO LA IDENTIFICACION DEL FORMULARIO');
                            }
                        }else{

                            throw new Exception('NO SE RETORNO NINGUN AL TRATAR DE CRUZAR LA INFORMACION CON LAS TABLAS DE LA BASE DE DATOS');
                        }
                    }else{

                        throw new Exception('HUBO UN ERROR AL TRAER LOS VALORES DE LA TABLAS ' . $columnsSQL["error"]);
                    }
                }else{
                    throw new Exception('NO LLEGARON DATOS AL SISTEMA');
                }
            } catch (Exception $e) {

                echo json_encode(array(array(
                    'type' => 'STATES_ERROR',
                    'title' => 'ERROR AL GUARDAR',
                    'message' => $e->getMessage()
                )));
            }            
        }else{

            $this->redireccionar();
        }
    }

    //Guardar productos por cliente por el id
    public function guardarProductos($columns,$data) {
        if (Server::RequestMethod("POST")) {
            //Recibir datos de formulario y deserializar en array asociativo
            $data = json_decode(Server::post("arreglo"), true);
            $cont = 0;
            $cant = count($data);

            if ($cant) {
                foreach ($data as $row) {
                    $res = $this->_clientes->saveProduct($row);

                    if ($res)
                        $cont++;
                }
            }

            echo ($cant == $cont ? 1 : 0);
        }
        else {
            $this->redireccionar();
        }
    }

    //Obtiene un archivo PDF especifico por el cliente id y el tipo de documento
    public function visualizar_archivo($cliente,$tipo_doc, $id_archivo = false) {

        $clientInfo = $this->_clientes->getInfoFileClient($tipo_doc,$cliente,$id_archivo,true);
        
        // Conectar a FTP
        $ftp = new FTP(Security::decode(FTP_HOST, FTP_KEYHASH));
        $ftp->login(Security::decode(FTP_USERNAME, FTP_KEYHASH), Security::decode(FTP_PASSWORD, FTP_KEYHASH), true);

        $file = $ftp->get($clientInfo['FOLDER_ARCHIVO'],$clientInfo['NOMBRE_ARCHIVO']);
        
        header("Content-type: application/pdf");
        header("Content-disposition: inline; filename=" . $file);
        readfile($file);
        unlink($file);
    }

    //Verifica si el documento del cliente existe en las carpetas que estan por FTP
    public function checkFileClient($type_doc,$id) {

        $clientInfo = $this->_clientes->getInfoFileClient($type_doc,$id,false,true);
        if($clientInfo){

            //INICIALIZA CONEXION FTP
            $ftp = new FTP(Security::decode(FTP_HOST, FTP_KEYHASH));
            $ftp->login(Security::decode(FTP_USERNAME, FTP_KEYHASH), Security::decode(FTP_PASSWORD, FTP_KEYHASH), true);

            if ((bool)$ftp->get($clientInfo['FOLDER_ARCHIVO'], $clientInfo['NOMBRE_ARCHIVO'])) {
                return true;
            }else {
                return false;
            }
        }else{
            return false;
        }
        $ftp->close();
    }

    //Obtiene todos los archivos que tienen un cliente por id
    public function getAllFilesClient(){
        if (Server::RequestMethod("POST")) {
            $clientInfo = $this->_clientes->getInfoFileClient(false,22,false);
            if($clientInfo){
                echo json_encode($clientInfo);
            }else{
                echo json_encode(false);
            }
        }else{
            $this->redireccionar();
        }
    }

    //Carga listado de ciudades por departamento - parms: id_departamento
    public function getCities() {//Solo si la petición se realiza por POST
        if (Server::RequestMethod("POST")) {
            $id = Server::post("id");

            $ciudades = $this->_global->getCities($id);

            $html = '<option value="">Seleccion una ciudad...</option>';
            if (count($ciudades)) {
                foreach ($ciudades as $ciudad) {
                    $html .= '<option value="' . $ciudad['id'] . '">' . $ciudad['nombre_ciudad'] . '</option>';
                }
            }
            echo $html;
        } else {
            $this->redireccionar("../login");
        }
    }

    //Cargar listado de ciudades por departamento - parms: id_departamento
    public function getDepartaments() {//Solo si la petición se realiza por POST
        if (Server::RequestMethod("POST")) {
            $id = Server::post("id");

            $departamentos = $this->_global->getDepartaments($id);

            $html = '<option value="">Seleccion un departamento...</option>';
            if (count($departamentos)) {
                foreach ($departamentos as $departamento) {
                    $html .= '<option value="' . $departamento['id'] . '">' . $departamento['nombre_departamento'] . '</option>';
                }
            }

            echo $html;
        } else {
            $this->redireccionar("../login");
        }
    }

    //Carga los datos de los clientes capturados por el funcionario
    public function tablePendientesFuncionarios() {
        $resultado_query = $this->_clientes->getAllClientesByUser($_SESSION["Mundial_authenticate_user_id"]);

        if($resultado_query){

            // devuelve los valores para la tabla
            $return["data"] = array();

            // devuelve los nombres de la columnas para la tabla
            $columnsTable = array_keys($resultado_query[0]);
            foreach ($columnsTable as $valueColumn) {
                if($valueColumn != 'ESTADO_CLIENTE_ID'){
                    $return["columns"][] = array('title' => str_replace("_", " ", $valueColumn), 'data' => $valueColumn);
                }
            }

            foreach ($resultado_query as $valueData) {
                $tempData = array();

                foreach ($columnsTable as $valueTable) {

                    if($valueTable == 'ESTADO_CLIENTE') {

                        if($valueData["ESTADO_CLIENTE_ID"] == 4 || $valueData["ESTADO_CLIENTE_ID"] == 2 || $valueData["ESTADO_CLIENTE_ID"] == 10 || $valueData["ESTADO_CLIENTE_ID"] == 12 || $valueData["ESTADO_CLIENTE_ID"] == 11 || $valueData["ESTADO_CLIENTE_ID"] == 9){
                            $tempData[$valueTable] = '<span class="label label-danger">'.strtolower($valueData[$valueTable]).'</span>';
                        }else if($valueData["ESTADO_CLIENTE_ID"] == 1){
                            $tempData[$valueTable] = '<span class="label label-default">'.strtolower($valueData[$valueTable]).'</span>';
                        }else if ($valueData["ESTADO_CLIENTE_ID"] == 3){
                            $tempData[$valueTable] = '<span class="label label-success">'.strtolower($valueData[$valueTable]).'</span>';
                        }else{
                            $tempData[$valueTable] = '<span class="label label-warning">'.strtolower($valueData[$valueTable]).'</span>';
                        }
                    }else if($valueTable != 'ESTADO_CLIENTE_ID'){
                        $tempData[$valueTable] = $valueData[$valueTable];
                    }
                }

                array_push($return["data"],$tempData);
            }

            if(isset($resultado_query) && count($resultado_query) > 0){
                echo json_encode($return);
            }
        }else{
            echo json_encode(false);
        }
    }

    //Carga los archivos de el clientes que se esta capturando
    public function tableAllFilesClient() {

        if (Server::RequestMethod("POST")) {

            $cliente = Server::post("id");

            $clientInfo = $this->_clientes->getInfoFileClient(false,$cliente);            

            if(!array_key_exists(0,$clientInfo)){
                $clientInfo = array($clientInfo);
            }
            $rutas = array();
            foreach ($clientInfo as $valueFile) {

                $nombreTempArchivo = FOLDERS_PATH.$valueFile["FOLDER_ARCHIVO"].'/'.$valueFile["NOMBRE_ARCHIVO"];
                
                array_push($rutas,array(
                    'OPCIONES_ARCHIVO' => array(
                        "<a href='javascript:void(0)' onclick=preview({$cliente},'{$valueFile["TIPO_DOCUMENTO"]}','{$valueFile["ID_ARCHIVO"]}') class='btn btn-xs btn-link visualizar_documento'><span class='glyphicon glyphicon-eye-open'></span></a>"
                    ),
                    'NOMBRE_DOC' => $valueFile["TIPO_DOCUMENTO"], 
                    'FECHA_INGRESO_ACHIVO' => $valueFile["FECHA_INGRESO_ACHIVO"]
                ));
            } 


            if(!empty($rutas)){

                header('Content-Type: application/json');
                echo json_encode($rutas);
            }
        }else{
            $this->redireccionar();
        }
    }

    //Carga el listado de tipos de documento
    public function getTypesDocument(){
        if (Server::RequestMethod("POST")) {
            $tiposDocumentos = $this->_global->getAllTypeClients();

            $html = '<option value=""></option>';
            foreach ($tiposDocumentos as $row) {
                $html .= "<option value='" . $row['id'] . "'>" . $row['descripcion'] . "(" . $row['codigo'] . ")" . "</option>";
            }
            echo $html;
        } else {
            $this->redireccionar("../login");
        }
    }

    //Consulta el id de un cliente por documento dentro del sistema
    public function searchClient() {
        if (Server::RequestMethod("POST")) {
            $documento = Server::post("documento");
            echo json_encode($this->_clientes->getClienteIDByDocument($documento));
        } else {
            $this->redireccionar();
        }
    }
        //Elimina el id de un Accionista por documento dentro del sistema
        public function deleteAccionista() {
            if(Server::RequestMethod("POST")){
                $id = Server::post("idAccionista");
    
                $response = $this->_crud->Delete("accionistas",$id);
    
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
            if (Server::RequestMethod("POST")) {
                $id = Server::post("id");
                echo json_encode($this->_clientes->eliminarAccionistas($documento));
            } else {
                $this->redireccionar();
            }
        }
        
}