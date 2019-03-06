<?php 

class ConsultaFechasController extends Controller 
{
	//Variables globales
    private $_global;
    private $_crud;
    private $_files;
    private $_clientes;
    private $_radicacion;
    private $_model;

	public function __construct(){
        if(Session::get('Mundial_authenticate')){

            if(in_array(Session::getLevel(Session::get("Mundial_user_rol")),[Session::getLevel('Gerente'),Session::getLevel('Consulta Fechas')])){

                try {

                    parent::__construct();

                    //Inicia a primera instancia los modelos
                    $this->_global = $this->loadModel("global");
                    $this->_crud = $this->loadModel("crud");
                    $this->_files = $this->loadModel("files");
                    $this->_clientes = $this->loadModel("clientes");
                    $this->_radicacion = $this->loadModel("radicacion");
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

	public function index(){
		$this->_view->setJs(array('index'));
        $this->_view->renderizar('index','consulta_fechas');
	}

	public function consulta_ult_estado(){

        if(!is_null(Session::get('Mundial_authenticate'))){

            $documento_cliente = Server::post("documentoCliente");
            
            $id_cliente = $this->_clientes->getClienteIDByDocument($documento_cliente);
            $tipo_persona_cliente = $this->_clientes->getTypeClienteID($id_cliente["id"]);

            $ult_fecha_actualizacion = $this->_clientes->UltFechaProcesoSarlaft($tipo_persona_cliente["TIPO_PERSONA"],$documento_cliente);
            
            if(!isset($ult_fecha_actualizacion['error'])){

                if(is_null($ult_fecha_actualizacion["FECHA_ULT_ACTUALIZACION"])){
                    
                    echo json_encode(
                        array(
                            'ultima_fecha_actualizacion' => '',
                            'respuesta_consulta' => '<li>Le informamos que su cliente no cuenta con ningún formulario físico, este deberá ser diligenciado y enviado a nuestras oficinas en la calle 37 No 14-38 en Bogotá.</li>'
                        )
                    );
                }else{                    

                    $nombre_cliente = $ult_fecha_actualizacion["NOMBRE_CLIENTE"];
                    $date1=date_create($ult_fecha_actualizacion["FECHA_ULT_ACTUALIZACION"]);
                    $date2=date_create(date('Y-m-d'));
                    $diff=date_diff($date1,$date2);
                    $interval = intval($diff->format("%R%a days"));

                    if($interval > 365){
                        
                        echo json_encode(array(
                            'ultima_fecha_actualizacion' => $ult_fecha_actualizacion["FECHA_ULT_ACTUALIZACION"],
                            'nombre_cliente' => $ult_fecha_actualizacion["NOMBRE_CLIENTE"],
                            'tipo_documento' => strtoupper($ult_fecha_actualizacion["TIPO_DOCUMENTO"]),
                            'respuesta_consulta' => '<li>Agradecemos informar a su cliente sobre nuestras líneas gratuitas de actualización de información telefónica 018000112684 opción 3 a nivel nacional.</li>'
                        ));
                    }else if($interval < 365){

                        echo json_encode(array(
                            'ultima_fecha_actualizacion' => $ult_fecha_actualizacion["FECHA_ULT_ACTUALIZACION"],
                            'nombre_cliente' => $ult_fecha_actualizacion["NOMBRE_CLIENTE"],
                            'tipo_documento' => strtoupper($ult_fecha_actualizacion["TIPO_DOCUMENTO"]),
                            'respuesta_consulta' => '<li>No es necesario el diligenciamiento de un nuevo formulario Sarlaft. Sin embargo si se encuentra próximo a vencerse, su cliente podrá actualizar la información vía telefónica a  través de nuestra línea 018000112684 opción 3 a nivel nacional.</li>'
                        ));
                    }
                    
                }

            }
        }else{

            //Visualizar pantalla de logueo inicial
            $this->_view->redireccionar('login');
        }
    }
}