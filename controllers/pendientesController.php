<?php 

class pendientesController extends Controller
{

    public function __construct() {

        if(Session::get('Mundial_authenticate')){

            if(in_array(Session::getLevel(Session::get("Mundial_user_rol")),[Session::getLevel('Gerente'),Session::getLevel('Operador Radicador')])){

                try {

                    parent::__construct();

                    $this->_model = $this->loadModel("pendientes");
                    $this->_clientes = $this->loadModel("clientes");
                    $this->_global = $this->loadModel("global");
                    $this->_crud = $this->loadModel("crud");
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            }else{
                $this->redireccionar('error', 'access', ['5656']);
            }
        }else{
            $this->redireccionar('error', 'access', ['5656']);
        }
    }

    //Metodo principal del controlador
    public function index() {
        $this->_view->setJs(array('pendientes'));
        $this->_view->renderizar('index', 'pendientes');
    }

    //Carga los datos para la tabla de pendientes
    public function getDataTableClientesPendientes() {

        if (Server::RequestMethod("POST")) {

            $data = Server::post("fechas");
            $fec_ini = $data["fecha_inicio"] . " 00:00:00";
            $fec_fin = $data["fecha_fin"] . " 23:59:59";

            $clientes_pendientes = $this->_model->getAllClientesPendientes($fec_ini, $fec_fin);

            //Retorna los valores de la tabla
            $return = array();
            if(!isset($clientes_pendientes['error'])){

                // Encabezado de la tabla
                $return["columns"][] = array('title' => 'Sel.', 'data' => 'SELECTOR');
                $return["columns"][] = array('title' => 'TIPO DOCUMENTO', 'data' => 'CLIENTE_TIPO_DOCUMENTO_CODIGO');                
                $return["columns"][] = array('title' => 'DOCUMENTO CLIENTE', 'data' => 'DOCUMENTO_CLIENTE');
                $return["columns"][] = array('title' => 'NOMBRE DEL CLIENTE', 'data' => 'NOMBRE_CLIENTE');
                $return["columns"][] = array('title' => 'ESTADO DEL CLIENTE', 'data' => 'ESTADO_PROCESO');
                $return["columns"][] = array('title' => 'FECHA DILIGENCIAMIENTO', 'data' => 'FECHA_DILIGENCIAMIENTO');
                $return["columns"][] = array('title' => 'CORREO ELECTRÓNICO', 'data' => 'CORREO_RADICACION');
                $return["columns"][] = array('title' => 'FECHA RADICACIÓN', 'data' => 'FECHA_RADICACION');
                $return["columns"][] = array('title' => 'HORA RADICACIÓN', 'data' => 'HORA_RADICACION');
                $return["columns"][] = array('title' => 'OBSERVACIÓN', 'data' => 'OBSERVACION');

                foreach ($clientes_pendientes as $keyClient =>  $dataCliente) {
                    
                    //Recorre los valores asignados para cada columna
                    foreach ($return["columns"] as  $column) {
                            
                        if($column['data'] == 'SELECTOR'){
                            $return["data"][$keyClient][$column['data']] = '
                                <div class="checkbox">
                                  <label class="text-center">
                                    <input type="checkbox" value="' . $dataCliente['id'] . '" proceso-id="' . $dataCliente['ID_PROCESO'] . '"  onchange="CheckToSendMail(this)">  
                                    <span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>                                  
                                  </label>
                                </div>';
                        }else if($column['data'] == 'ESTADO_PROCESO'){
                            $return["data"][$keyClient][$column['data']] = str_replace('_', ' ', $dataCliente[$column['data']]);
                        }else{
                            $return["data"][$keyClient][$column['data']] = $dataCliente[$column['data']];
                        }
                    }
                }

                echo json_encode($return);
            }
        }else{
            $this->redireccionar('../pendientes');
        }
    }

    //Envío de correo de pendientes
    public function sendMailPendientes(){
        if (Server::RequestMethod("POST")) {
            $data = Server::post("listIdPendientes");

            $total = count($data);
            $enviados = 0;

            $remitente = array(
                'email' => Security::decode(MAIL_USER,MAIL_KEYHASH), 
                'name' => 'Informes Mundial'
            );

            $return = array();

            $templatePath = ROOT . "public/templates/pendientes.html";
            $content_template = file_get_contents($templatePath);

            foreach ($data as $row) {
                $split = explode("-",$row);
                $info = array_shift($this->_model->getInfoPenddingById($split[0], $split[1]));

                $receptor = array('email' => $info['CORREO_RADICACION'], 'name' => $info["NOMBRE_CLIENTE"]);

                if($info["CORREO_RADICACION"] == "" || is_null($info["CORREO_RADICACION"])){
                    $receptor["email"] = $remitente["email"];
                }

                $cids = array(
                    "[cid:CLIENTE_NOMBRE]",
                    "[cid:DOCUMENTO_CLIENTE]",
                    "[cid:PENDIENTE_ESTADO]"
                );

                $replace = array(
                    $info["NOMBRE_CLIENTE"],
                    $info["DOCUMENTO_CLIENTE"],
                    $info["ESTADO_PROCESO"]
                );

                $bodyHTML = str_replace($cids, $replace, $content_template);

                $SendCorreos = Email::configEmail(
                    array(
                        'remitente' => $remitente,
                        'receptor' => $receptor,
                        'isHTML' => true,
                        'Asunto' => "Cliente con información pendiente.",
                        'Contenido' => $bodyHTML,
                        'Contenido-noHTML' => "Se informa que el cliente " . $info["NOMBRE_CLIENTE"] . "  identificado con el documento " . $info["DOCUMENTO_CLIENTE"] . " se encuentra pendiente en el sistema por la siguiente razón: " . $info["ESTADO_PROCESO"]
                    ),
                    array(
                        "path" => ROOT . 'public/img/logo_mundial.png',
                        "cid" => "LOGO_MUNDIAL"
                    )
                );

                if($SendCorreos === true){
                    $enviados++;

                    $fecha = date("Y-m-d h:i:s");
                    $fechaEnvioCorreo = array(
                        'fecha_envio_correo' => $fecha ,
                        'id' => $info["RADICACION_ID"]
                    );

                    $this->_crud->Update("zr_radicacion", $fechaEnvioCorreo);
                }else{
                    $envidos = 0;
                    break;
                }
            }

            if($enviados != 0){
                $return = array(
                    'type' => 'STATES_OK',
                    'titulo' => 'EXITO!!!',
                    'message' => 'Se han enviado exitosamente ' . $enviados . " correos de " . $total . "."
                );
            }else{
                $return = array(
                    'type' => 'STATES_ERROR',
                    'titulo' => 'ERROR!!!',
                    'message' => 'Ha ocurrido un error al enviar los correos. Por favor verifique los correos.'
                );

            }

            echo json_encode($return);
        }else{
            $this->redireccionar("error","access", array("5656"));
        }
    }
}