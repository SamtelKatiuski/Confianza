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
            $datosRelacionArchivo = $this->_clientes->getUltimaExpedicionDoc($id_cliente['id']);
            if(!isset($ult_fecha_actualizacion['error']) && !isset($datosRelacionArchivo['error'])){
                $documentosValidos = ['RUT', 'CCO', 'DDC', 'ACC', 'EFC', 'EFI', 'NEF', 'RTA', 'RET'];
                $abreviadosFecha = [];
                foreach ($datosRelacionArchivo as $valueRelacionArchivo) {
                    $abreviado = explode('-', $valueRelacionArchivo['NOMBRE_ARCHIVO'])[0];
                    if (in_array($abreviado, $documentosValidos)) {
                        if (array_key_exists($abreviado, $abreviadosFecha)) {
                            foreach ($abreviadosFecha[$abreviado] as $keyAbreviadoFecha => $valueAbreviadoFecha) {
                                if (strtotime($valueRelacionArchivo['FECHA_EMISION']) > strtotime($valueAbreviadoFecha)) {
                                    $abreviadosFecha[$abreviado][$keyAbreviadoFecha] = $valueRelacionArchivo['FECHA_EMISION'];
                                }
                            }
                        } else {
                            $abreviadosFecha[$abreviado][] = $valueRelacionArchivo['FECHA_EMISION'];
                        }
                    }
                }
                foreach ($abreviadosFecha as $keyAbreviadoFecha => $valueAbreviadoFecha) {
                    $abreviadosFecha[$keyAbreviadoFecha][0] = date('d-m-Y', strtotime($valueAbreviadoFecha[0]));
                }
                if(is_null($ult_fecha_actualizacion["FECHA_ULT_ACTUALIZACION"]) && !empty($abreviadosFecha)){
                    
                    echo json_encode(
                        array(
                            'ultima_fecha_actualizacion' => '',
                            'respuesta_consulta' => '<li>Le informamos que su cliente no cuenta con ningún formulario físico, este deberá ser diligenciado y enviado a nuestras oficinas en la calle 37 No 14-38 en Bogotá.</li>',
                            'utima_fecha_expedicion_docs' => $abreviadosFecha
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
                            'respuesta_consulta' => '<li>Agradecemos informar a su cliente sobre nuestras líneas gratuitas de actualización de información telefónica XXXXXXXXXXX opción 3 a nivel nacional.</li>',
                            'utima_fecha_expedicion_docs' => $abreviadosFecha
                        ));
                    }else if($interval < 365){
                        echo json_encode(array(
                            'ultima_fecha_actualizacion' => $ult_fecha_actualizacion["FECHA_ULT_ACTUALIZACION"],
                            'nombre_cliente' => $ult_fecha_actualizacion["NOMBRE_CLIENTE"],
                            'tipo_documento' => strtoupper($ult_fecha_actualizacion["TIPO_DOCUMENTO"]),
                            'respuesta_consulta' => '<li>No es necesario el diligenciamiento de un nuevo formulario Sarlaft. Sin embargo si se encuentra próximo a vencerse, su cliente podrá actualizar la información vía telefónica a  través de nuestra línea XXXXXXXXXXX opción 3 a nivel nacional.</li>',
                            'utima_fecha_expedicion_docs' => $abreviadosFecha
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