<?php
class reportesController extends Controller 
{
	private $_global;
	private $_clientes;
	private $_reportes;
	private $_files;
	private $radicación;
	
	//Se encarga de cargar todos los modulos al iniciar
	public function __construct() {

		if(Session::get('Mundial_authenticate')){

			if(in_array(Session::getLevel(Session::get("Mundial_user_rol")),[Session::getLevel('Gerente'),Session::getLevel('Reportes')])){
				
				try {
					parent::__construct();

					if(file_exists(ROOT . 'Classes/PHPExcel/IOFactory.php')){
						require_once ROOT . 'Classes/PHPExcel/IOFactory.php';
					}else{
						throw new Exception('EL ARCHIVO IOFactory.php NO EXISTE');
					}

					$this->_global = $this->loadModel("global");
					$this->_clientes = $this->loadModel("clientes");     
					$this->_reportes = $this->loadModel("reportes");
					$this->_files = $this->loadModel("files");
					$this->_radicacion = $this->loadModel("radicacion");

				}catch(Exception $e){
					die($e->getMessage());
				}
			}else{
				$this->redireccionar('error', 'access', ['5656']);
			}
		}else{
			$this->redireccionar('error', 'access', ['5656']);
		}
	}

	public function index() {
		$this->_view->titulo = "Reportes"; 
		$this->_view->setJs(array('reportes'));
		$this->_view->renderizar('index','reportes');
	}

	public function cargarReporteGeneral(){

		$data = json_decode(json_encode(Server::post()), true); // Variable de almacenamiento de los valores que llegan por metodo POST
		$return = array();

		$fechasReporte = array();

		if(isset($data["fecha_inicio"]) && isset($data["fecha_fin"])){
			$fechasReporte = array(
				"inicio" => $data["fecha_inicio"],
				"fin"    => $data["fecha_fin"]
			);
		}

		switch ($data['opcion_reporte']) {
			case 'reporte_radicacion_check_list_documents':
				$return = $this->reporteCheckListDocumentosClientes();
				break;
			case 'reporte_gestion_ppes_benef_final':
				$return = $this->reporteGestionPPESBeneficiariofinal();
				break;
			case 'reporte_verificacion_completitud':
				$return = $this->reporteVerificacionCompleitudClientes();
				break;
			case 'reporte_pendientes':
				$return = $this->reportePendientes($fechasReporte);
				break;
			case 'reporte_cruce_clientes_faltantes':
				$return = $this->reporteCruceClientesFaltantes($_FILES);
				break;
			case 'reporte_cruce_clientes_sobrantes':
				$return = $this->reporteCruceClientesSobrantes($_FILES);
				break;
			case 'reporte_facturacion':
				$return = $this->reporteFacturacion($fechasReporte);
				break;
			case 'reporte_capturas':
				$return = $this->reporteCapturas($fechasReporte);
				break;
			case 'reporte_capturas_natural':
				$return = $this->reporteCapturasNaturales($fechasReporte);
				break;
			case 'reporte_capturas_juridico':
				$return = $this->reporteCapturasJuridico($fechasReporte);
				break;
			case 'reporte_actualizacion_documentos':
				$return = $this->reporteActualizacionDocumentos($fechasReporte);
				break;
			default:
				
				break;
		}

		header('Content-Type: application/json;charset=utf-8');
		echo json_encode($return);
	}

	public function reporteCheckListDocumentosClientes(){

		$resultadoReporte = $this->_reportes->getChecklistDocumentosClientes();
		$dataTable = array('data' => array());
		$dataTable["fileName"] = 'REPORTE DE RADICACION + CHECK LIST DOCUMENTOS';
		$dataTable['columns'] = [
			array(
				'dataField'     => 'NUMERO_PLANILLA',
				'dataType'      => 'string',
				'caption' 		=> 'NUMERO PLANILLA'
			),
			array(
				'dataField'     => 'FECHA_RADICACION',
				'dataType'      => 'date',
				'format'  		=> 'dd/MM/yyyy',
				'caption'       => 'FECHA RADICACION'
			),
			array(
				'dataField'     => 'CLIENTE_TIPO_DOCUMENTO_CODIGO',
				'dataType'      => 'string',
				'caption'       => 'TIPO ID CLIENTE'
			),
			array(
				'dataField'     => 'CLIENTE_DOCUMENTO',
				'dataType'      => 'string',
				'caption'       => 'NUMERO ID CLIENTE'
			),
			array(
				'dataField'     => 'NOMBRE_CLIENTE',
				'dataType'      => 'string',
				'caption'       => 'NOMBRE_CLIENTE'
			),
			array(
				'dataField'     => 'RADICACION_PROCESO',
				'dataType'      => 'string',
				'caption'       => 'RADICACION PROCESO'
			),
			array(
				'dataField'     => 'FCC',
				'dataType'      => 'string' 
			),
			array(
				'dataField'     => 'TIPO_FORMULARIO',
				'dataType'      => 'string',
				'caption' 		=> 'FORMULARIO NUEVO'
			),
			array(
				'dataField'     => 'CLIENTE_ESTADO_PROCESO',
				'dataType'      => 'string',
				'caption'		=> 'CLIENTE ESTADO PROCESO'
			),
			array(
				'dataField'     => 'OBSERVACIONES',
				'dataType'      => 'string',
				'caption'		=> 'OBSERVACIONES'
			),
		];

		if(!isset($resultadoReporte['error'])){
			$dataTable["data"] = $resultadoReporte;
		}
		
		return $dataTable;
	}

	public function reporteGestionPPESBeneficiariofinal(){

		$resultadoReporte = $this->_reportes->getGestionPPESBeneficiariofinal();
		$dataTable = array('data' => array());
		$dataTable["fileName"] = 'REPORTE GESTION PPES - BENEFICIARIO FINAL';
		$dataTable['columns'] = [
			array(
				'dataField'     => 'LINEA_NEGOCIO',
				'dataType'      => 'string',
				'caption'       => 'LINEA NEGOCIO'
			),
			array(
				'dataField'     => 'FECHA_RADICACION',
				'dataType'      => 'date',
				'format'  		=> 'dd/MM/yyyy',
				'caption'       => 'FECHA RADICACION'
			),
			array(
				'dataField'     => 'CORREO',
				'dataType'      => 'string',
				'caption'       => 'USUARIO SUSCRIPTOR'
			),
			array(
				'dataField'     => 'CLIENTE_TIPO_DOCUMENTO_CODIGO',
				'dataType'      => 'string'
			),
			array(
				'dataField'     => 'CLIENTE_DOCUMENTO',
				'dataType'      => 'string' 
			),
			array(
				'dataField'     => 'NOMBRE_CLIENTE',
				'dataType'      => 'string',
				'caption'       => 'NOMBRE_CLIENTE'
			),
			array(
				'dataField'     => 'FECHA_GESTION',
				'dataType'      => 'date',
				'format'  		=> 'dd/MM/yyyy',
				'caption'       => 'FECHA GESTION'
			),
			array(
				'dataField'     => 'CONCEPTO',
				'dataType'      => 'string',
				'caption'       => 'CONCEPTO'
			),
			array(
				'dataField'     => 'ANEXO_PPES',
				'dataType'      => 'string',
				'caption'       => 'ANEXO_PPES'
			),
			array(
				'dataField'     => 'ANEXO_ACCIONISTAS',
				'dataType'      => 'string',
				'caption'       => 'ANEXO_ACCIONISTAS'
			),
			array(
				'dataField'     => 'TIPO_FORMULARIO',
				'dataType'      => 'string',
				'caption'       => 'FORMULARIO NUEVO'
			),
		];

		if(!isset($resultadoReporte['error'])){
			$dataTable["data"] = $resultadoReporte;
		}

		return $dataTable;
	}

	public function reporteVerificacionCompleitudClientes(){

		$resultadoReporte = $this->_reportes->getGestionVerificacionCompleitudClientes();
		$dataTable["fileName"] = 'REPORTE DE VERIFICACION Y COMPLETITUD';
		$dataTable = array('data' => array());
		$dataTable['columns'] = [
			array(
				'dataField'     => 'FECHA_RADICACION',
				'dataType'      => 'date',
				'format'  		=> 'dd/MM/yyyy',
				'caption'       => 'FECHA RADICACION'
			),
			array(
				'dataField'     => 'CORREO',
				'dataType'      => 'string',
				'caption'       => 'USUARIO SUSCRIPTOR'
			),
			array(
				'dataField'     => 'CLIENTE_TIPO_DOCUMENTO_CODIGO',
				'dataType'      => 'string'
			),
			array(
				'dataField'     => 'CLIENTE_DOCUMENTO',
				'dataType'      => 'string' 
			),
			array(
				'dataField'     => 'NOMBRE_CLIENTE',
				'dataType'      => 'string',
				'caption'       => 'NOMBRE_CLIENTE'
			),
			array(
				'dataField'     => 'FECHA_COMPLETITUD',
				'dataType'      => 'datetime',
				'format'  		=> 'dd/MM/yyyy hh:mm a',
				'caption'       => 'FECHA COMPLETITUD'
			),
			array(
				'dataField'     => 'GESTION_CAMPOS_COMPLETADOS',
				'dataType'      => 'string',
				'caption'       => 'CAMPOS COMPLETADOS'
			),
			array(
				'dataField'     => 'FECHA_VERIFICACION',
				'dataType'      => 'datetime',
				'format'  		=> 'dd/MM/yyyy hh:mm a',
				'caption'       => 'FECHA VERIFICACION'
			),
			array(
				'dataField'     => 'PREGUNTA_CAMPOS_COMPLETADOS',
				'dataType'      => 'string',
				'caption'       => 'SE COMPLATETARON LOS CAMPOS ?'
			),
			array(
				'dataField'     => 'GESTION_OBSERVACIONES',
				'dataType'      => 'string',
				'caption'       => 'OBSERVACIONES'
			),
		];
		if(!isset($resultadoReporte['error'])){

			$dataTable["data"] = $resultadoReporte;
		}

		return $dataTable;
	}

	public function reportePendientes($fechasReporte = array()){
		$resultadoReporte = $this->_reportes->getPendientesClientes($fechasReporte);
		$dataTable = array('data' => array());
		$dataTable["fileName"] = 'REPORTE FORMULARIOS PENDIENTES';
		$dataTable['columns'] = [
			array(
				'dataField'     => 'FECHA_RADICACION',
				'dataType'      => 'date',
				'format'  		=> 'dd/MM/yyyy',
				'caption'       => 'FECHA RADICACION'
			),
			array(
				'dataField'     => 'CORREO',
				'dataType'      => 'string',
				'caption'       => 'USUARIO SUSCRIPTOR'
			),
			array(
				'dataField'     => 'CLIENTE_TIPO_DOCUMENTO_CODIGO',
				'dataType'      => 'string'
			),
			array(
				'dataField'     => 'CLIENTE_DOCUMENTO',
				'dataType'      => 'string' 
			),
			array(
				'dataField'     => 'NOMBRE_CLIENTE',
				'dataType'      => 'string',
				'caption'       => 'NOMBRE DEL CLIENTE'
			),
			array(
				'dataField'     => 'FECHA_PROCESO',
				'dataType'      => 'string',
				'caption'       => 'FECHA PROCESO'
			),
			array(
				'dataField'     => 'ESTADO_PROCESO',
				'dataType'      => 'string',
				'caption'       => 'ESTADO PROCESO CLIENTE'
			),
			array(
				'dataField'     => 'FECHA_ENVIO_CORREO',
				'dataType'      => 'string',
				'caption'       => 'FECHA ENVÍO CORREO'
			),
			array(
				'dataField'     => 'DOCUMENTO_PENDIENTE_CODIGO',
				'dataType'      => 'string',
				'caption'       => 'DOCUMENTO PENDIENTE'
			),
			array(
				'dataField'     => 'OBSERVACION',
				'dataType'      => 'string',
				'caption'       => 'OBSERVACIÓN RADICACIÓN'
			)
		];

		if(!isset($resultadoReporte['error'])){
			$dataTable["data"] = $resultadoReporte;
		}
		
		return $dataTable;
	}
	
	public function reporteCruceClientesFaltantes($dataFile){

		$loadFile = Helpers::LoadFile($dataFile['archivo_cruce_zurich'],['XLSX','XLS']);

		if(!isset($loadFile['error'])){

			$dataTable = array('data' => array());
			$dataTable["fileName"] = 'REPORTE PRODUCCION SARLAFT -  FALTANTES';

			$FileTemp = $loadFile['success']['ruta_temp'];
			$FileTempType = PHPExcel_IOFactory::identify($FileTemp);
			$objReader = PHPExcel_IOFactory::createReader($FileTempType);
			$objPHPExcel = $objReader->load($FileTemp);
			$sheet = $objPHPExcel->getSheet(0); 
			$highestRow = $sheet->getHighestRow(); 
			$highestColumn = $sheet->getHighestColumn();

			$dataTable['columns'] = [
				array(
					'dataField'     => 'FECHA_EMISION',
					'dataType'      => 'datetime',
					'format'  		=> 'dd/MM/yyyy hh:mm a',
					'caption'       => 'FECHA EMISION'
				),
				array(
					'dataField'     => 'FECHA_INICIO_VIGENCIA',
					'dataType'      => 'date',
					'format'  		=> 'dd/MM/yyyy',
					'caption'       => 'FECHA INICIO VIGENCIA'
				),
				array(
					'dataField'     => 'FECHA_FIN_VIGENCIA',
					'dataType'      => 'date',
					'format'  		=> 'dd/MM/yyyy',
					'caption'       => 'FECHA FIN VIGENCIA'
				),
				array(
					'dataField'     => 'TIPO_NEGOCIO_SEGMENTO',
					'dataType'      => 'string',
					'caption'       => 'TIPO DE NEGOCIO SEGMENTO' 
				),
				array(
					'dataField'     => 'NUMERO_MASTER_POLIZA',
					'dataType'      => 'number',
					'caption'       => 'NUMERO MASTER PÓLIZA'
				),
				array(
					'dataField'     => 'POLIZA_RENOVADA',
					'dataType'      => 'string',
					'caption'       => 'PÓLIZA RENOVADA'
				),
				array(
					'dataField'     => 'NUMERO_POLIZA',
					'dataType'      => 'string',
					'caption'       => 'NÚMERO PÓLIZA'
				),
				array(
					'dataField'     => 'TIPO_IDENTIFICACION_TOMADOR',
					'dataType'      => 'string',
					'caption'       => 'TIPO DE IDENTIFICACIÓN TOMADOR'
				),
				array(
					'dataField'     => 'IDENTIFICACION_TOMADOR',
					'dataType'      => 'string',
					'caption'       => 'IDENTIFICACIÓN TOMADOR'
				),
				array(
					'dataField'     => 'NOMBRE_TOMADOR',
					'dataType'      => 'string',
					'caption'       => 'NOMBRE TOMADOR'
				),
				array(
					'dataField'     => 'CLASIFICACION',
					'dataType'      => 'string',
					'caption'       => 'CLASIFICACIÓN'
				),
				array(
					'dataField'     => 'DESCRIPCION_BRANCH',
					'dataType'      => 'string',
					'caption'       => 'DESCRIPCIÓN BRANCH'
				),
				array(
					'dataField'     => 'NOMBRE_INTERMEDIARIO',
					'dataType'      => 'string',
					'caption'       => 'NOMBRE INTERMEDIARIO'
				),
				array(
					'dataField'     => 'POLIZA_RAMO_DESCRIPCION',
					'dataType'      => 'string',
					'caption'       => 'PÓLIZA RAMO DESCRIPCIÓN'
				),
				array(
					'dataField'     => 'ANIO_EMISION',
					'dataType'      => 'string',
					'caption'       => 'AÑO DE EMISIÓN'
				)
			];

			if($highestRow > 0){
						
				for ($row = 2; $row <= $highestRow; $row++){ 

					//Lee La fila y columnas del archivo empezando de la columna A , Fila 2
					$DataFile = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, TRUE, FALSE);
					$TempRowData = $DataFile[0];

					$getClientRobot = $this->_files->getClientRobot($TempRowData[10]);

					if(!isset($getClientRobot['error'])){
						if(!$getClientRobot){
							$dataTable["data"][] = array(
								'FECHA_EMISION'                 => $TempRowData[0],
								'FECHA_INICIO_VIGENCIA'         => $TempRowData[1],
								'FECHA_FIN_VIGENCIA'            => $TempRowData[2],
								'TIPO_NEGOCIO_SEGMENTO'         => $TempRowData[3],
								'NUMERO_MASTER_POLIZA'          => $TempRowData[4],
								'POLIZA_RENOVADA'               => $TempRowData[5],
								'NUMERO_POLIZA'                 => $TempRowData[6],
								'TIPO_IDENTIFICACION_TOMADOR'   => $TempRowData[7],
								'IDENTIFICACION_TOMADOR'        => $TempRowData[8],
								'NOMBRE_TOMADOR'                => $TempRowData[9],
								'CLASIFICACION'                 => $TempRowData[10],
								'DESCRIPCION_BRANCH'            => $TempRowData[11],
								'NOMBRE_INTERMEDIARIO'          => $TempRowData[12],
								'POLIZA_RAMO_DESCRIPCION'       => $TempRowData[13],
								'ANIO_EMISION'                  => $TempRowData[14]
							); 
						}
					}
				}
			}

			unlink($FileTemp);        
			return $dataTable;
		}
	}

	public function reporteCruceClientesSobrantes($dataFile){

		$AllDocumentsFilesRobot = $this->_files->getAllDocumentsFilesRobot();

		if(!isset($AllDocumentsFilesRobot['error'])){

			$loadFile = Helpers::LoadFile($dataFile['archivo_cruce_zurich'],['XLSX','XLS']);
			$dataTable = array('data' => array());
			$dataTable["fileName"] = 'REPORTE PRODUCCION SARLAFT -  FALTANTES';

			if(!isset($loadFile['error'])){

				$FileTemp = $loadFile['success']['ruta_temp'];
				$FileTempType = PHPExcel_IOFactory::identify($FileTemp);
				$objReader = PHPExcel_IOFactory::createReader($FileTempType);
				$objPHPExcel = $objReader->load($FileTemp);
				$sheet = $objPHPExcel->getSheet(0); 
				$highestRow = $sheet->getHighestRow(); 
				$highestColumn = $sheet->getHighestColumn();

				$dataTable['columns'] = [					
					array(
						'dataField'     => 'LINEA_NEGOCIO',
						'dataType'      => 'string',
						'caption'       => 'LINEA_NEGOCIO'
					),
					array(
						'dataField'     => 'TIPO_DOCUMENTO',
						'dataType'      => 'string',
						'caption'       => 'TIPO DE DOCUMENTO' 
					),
					array(
						'dataField'     => 'IDENTIFICACION_TOMADOR',
						'dataType'      => 'string',
						'caption'       => 'IDENTIFICACION TOMADOR'
					),
					array(
						'dataField'     => 'CLIENTE_RADICADO',
						'dataType'      => 'string',
						'caption'       => 'CLIENTE RADICADO'
					)
				];

				if($highestRow > 0){
							
					for ($row = 2; $row <= $highestRow; $row++){

						//Lee La fila y columnas del archivo empezando de la columna A , Fila 2
						$documentFile = $sheet->getCell('I'.$row)->getValue();
						$documentClients[] = trim($documentFile);
					}

					foreach ($AllDocumentsFilesRobot as $valueDocumentClient) {

						if(array_search((string)$valueDocumentClient,$documentClients) !== false){

							$getClientRobot = $this->_files->getClientRobot((string)$valueDocumentClient);
							$getClientRadicado = $this->_radicacion->searchClientRadicado((string)$valueDocumentClient);
							$getTipoDocumento = $this->_global->getTipoDocumentoByID($getClientRobot['TIPO_IDENT_CLIENTE']);
							$getLineaNegocio = $this->_global->getLineaNegocioByID($getClientRadicado['LINEA_NEGOCIO']);

							$dataTable["data"][] = array(
								'LINEA_NEGOCIO'             => $getLineaNegocio['NOMBRE'],
								'TIPO_DOCUMENTO'            => $getTipoDocumento['codigo'],
								'IDENTIFICACION_TOMADOR'    => $getClientRobot['NUMERO_IDENT_CLIENTE'],
								'CLIENTE_RADICADO'          => (($getClientRadicado) ? 'SI' : 'NO')
							);
						}
					}
				}
			}

			unlink($loadFile['success']['ruta_temp']);        
			return $dataTable;
		}
	}

	public function reporteFacturacion($fechasReporte = array()){
		
		$resultadoReporte = $this->_reportes->getFacturacion($fechasReporte);
		$dataTable = array('data' => array());
		$dataTable["fileName"] = 'REPORTE FACTURACION';
		$dataTable['columns'] = [
			array(
				'dataField' 	=> 'FECHA_RADICACION',
				'dataType'  	=> 'date',
				'format' 		=> 'dd/MM/yyyy',
				'caption'   	=> 'FECHA RADICACION'
			),
			array(
				'dataField' 	=> 'USUARIO_ASISTEMYCA',
				'dataType'  	=> 'string',
				'caption'   	=> 'USUARIO RADICADOR'
			),
			array(
				'dataField' 	=> 'FECHA_CAPTURA',
				'dataType'  	=> 'date',
				'format' 		=> 'dd/MM/yyyy',
				'caption'   	=> 'FECHA CAPTURA'
			),	
			array(
				'dataField' 	=> 'USUARIO_CAPTURA',
				'dataType'  	=> 'string',
				'caption'   	=> 'USUARIO CAPTURA'
			),
			array(
				'dataField'     => 'FORMULARIO_REPETIDO',
				'dataType'      => 'string',
				'caption' 		=> 'FORMULARIO REPETIDO'
			),
			array(
				'dataField'     => 'NUMERO_PLANILLA',
				'dataType'      => 'string',
				'caption' 		=> 'NUMERO PLANILLA'
			),
			array(
				'dataField'     => 'FECHA_DILIGENCIAMIENTO',
				'dataType'      => 'date',
				'format' 		=> 'dd/MM/yyyy',
				'caption'       => 'FECHA DILIGENCIAMIENTO'
			),
			array(
				'dataField'     => 'USUARIO_SUSCRIPTOR',
				'dataType'      => 'string',
				'caption'       => 'USUARIO SUSCRIPTOR'
			),
			array(
				'dataField'     => 'CANT_DOCUMENTOS',
				'dataType'      => 'string',
				'caption'       => 'CANTIDAD DOCUMENTOS'
			),
			array(
				'dataField'     => 'SEPARADO',
				'dataType'      => 'string',
				'caption'       => 'SEPARADO'
			),
			array(
				'dataField'     => 'USUARIO_ASISTEMYCA',
				'dataType'      => 'string',
				'caption'       => 'USUARIO ASISTEMYCA'
			),
			array(
				'dataField'     => 'NUMERO_RADICACION',
				'dataType'      => 'string',
				'caption'       => 'NUMERO RADICACION'
			),
			array(
				'dataField'     => 'TIPO_CLIENTE_CODIGO',
				'dataType'      => 'string',
				'caption'       => 'TIPO CLIENTE CODIGO'
			),
			array(
				'dataField'     => 'CLIENTE_DOCUMENTO',
				'dataType'      => 'string',
				'caption'       => 'NUMERO ID CLIENTE'
			),
			array(
				'dataField'     => 'NOMBRE_TOMADOR',
				'dataType'      => 'string',
				'caption'       => 'NOMBRE TOMADOR'
			),
			array(
				'dataField'     => 'PROCESO_COMPLETITUD',
				'dataType'      => 'datetime',
				'format'  		=> 'dd/MM/yyyy hh:mm a',
				'caption'       => 'FECHA COMPLETITUD'
			),
			array(
				'dataField'     => 'PROCESO_VERIFICACION',
				'dataType'      => 'datetime',
				'format'  		=> 'dd/MM/yyyy hh:mm a',
				'caption'       => 'FECHA VERIFICACION'
			),
			array(
				'dataField'     => 'ESTADO_TIPOLOGIA',
				'dataType'      => 'string',
				'caption'       => 'ESTADO TIPOLOGIA'
			),
			array(
				'dataField'     => 'INTENTO_LLAMADA',
				'dataType'      => 'number',
				'caption'       => 'INTENTO LLAMADA'
			),
			array(
				'dataField'     => 'FORMA_RECEPCION',
				'dataType'      => 'string',
				'caption'       => 'FORMA RECEPCION'
			),
			array(
				'dataField'     => 'TIPO_FORMULARIO',
				'dataType'      => 'string',
				'caption'       => 'TIPO FORMULARIO NUEVO/VIEJO'
			),
			array(
				'dataField'     => 'TIPO_LLAMADA',
				'dataType'      => 'string',
				'caption'       => 'TIPO DE LLAMADA (INBOUND/OUTBOUND)'
			),
			array(
				'dataField'     => 'ESTADO_PROCESO',
				'dataType'      => 'string',
				'caption'       => 'STATUS'
			),
			array(
				'dataField'     => 'FECHA_PROCESO_CAPTURA',
				'dataType'      => 'datetime',
				'format'  		=> 'dd/MM/yyyy hh:mm a',
				'caption'       => 'FECHA CAPTURA'
			),
			array(
				'dataField'     => 'FORMULARIO_CAPTURADO',
				'dataType'      => 'string',
				'caption'       => 'FORMULARIO CAPTURADO'
			),
			array(
				'dataField'     => 'OBSERVACION',
				'dataType'      => 'string',
				'caption'       => 'OBSERVACIÓN RADICACIÓN'
			)
		];

		if(!isset($resultadoReporte['error'])){
			$dataTable["data"] = $resultadoReporte;
		}  

		return $dataTable;
	}

	public function reporteCapturas($fechasReporte = array()){

		$resultadoReporte = $this->_reportes->getDatosClientesCapturados($fechasReporte);
		$dataTable = array('data' => array());
		$dataTable["fileName"] = 'REPORTE CAPTURA';
		$dataTable['columns'] = [
			array(
				'dataField' 	=> 'FECHA_RADICACION',
				'dataType'  	=> 'date',
				'format'  		=> 'dd/MM/yyyy',
				'caption'   	=> 'FECHA RADICACION'
			),
			array(
				'dataField' 	=> 'USUARIO_ASISTEMYCA',
				'dataType'  	=> 'string',
				'caption'   	=> 'USUARIO RADICADOR'
			),
			array(
				'dataField' 	=> 'FECHA_CAPTURA',
				'dataType'  	=> 'date',
				'format'  		=> 'dd/MM/yyyy',
				'caption'   	=> 'FECHA CAPTURA'
			),	
			array(
				'dataField' 	=> 'USUARIO_CAPTURA',
				'dataType'  	=> 'string',
				'caption'   	=> 'USUARIO CAPTURA'
			),
			array(
				'dataField'     => 'NOMBRE_TOMADOR',
				'dataType'      => 'string',
				'caption'       => 'NOMBRE TOMADOR'
			),
			array(
				'dataField'     => 'IDENTIFICACION_TOMADOR',
				'dataType'      => 'string',
				'caption'       => 'IDENTIFICACION TOMADOR'
			),
			array(
				'dataField'     => 'FECHA_DILIGENCIAMIENTO',
				'dataType'      => 'date',
				'format'  		=> 'dd/MM/yyyy',
				'caption'       => 'FECHA DILIGENCIAMIENTO'
			),
			array(
				'dataField'     => 'PROCESO',
				'dataType'      => 'string',
				'caption'       => 'PROCESO'
			),
			array(
				'dataField'     => 'TIPO_SOLICITUD',
				'dataType'      => 'string',
				'caption'       => 'TIPO_SOLICITUD'
			),
			array(
				'dataField'     => 'CIUDAD_DILIGENCIAMIENTO',
				'dataType'      => 'string',
				'caption'       => 'CIUDAD DILIGENCIAMIENTO'
			),
			array(
				'dataField'     => 'SUCURSAL',
				'dataType'      => 'string',
				'caption'       => 'SUCURSAL'
			),
			array(
				'dataField'     => 'CLASE_VINCULACION',
				'dataType'      => 'string',
				'caption'       => 'CLASE VINCULACION'
			),
			array(
				'dataField'     => 'CLASE_VINCULACION_OTRO',
				'dataType'      => 'string',
				'caption'       => 'OTRO? CUAL'
			),
			array(
				'dataField'     => 'TOMADOR_ASEGURADO',
				'dataType'      => 'string',
				'caption'       => 'TOMADOR ASEGURADO'
			),
			array(
				'dataField'     => 'TOMADOR_ASEGURADO_OTRO',
				'dataType'      => 'string',
				'caption'       => 'OTRO? CUAL'
			),
			array(
				'dataField'     => 'TOMADOR_BENEFICIARIO',
				'dataType'      => 'string',
				'caption'       => 'TOMADOR BENEFICIARIO'
			),
			array(
				'dataField'     => 'TOMADOR_BENEFICIARIO_OTRO',
				'dataType'      => 'string',
				'caption'       => 'OTRO? CUAL'
			),
			array(
				'dataField'     => 'ASEGURADO_BENEFICIARIO',
				'dataType'      => 'string',
				'caption'       => 'ASEGURADO BENEFICIARIO'
			),
			array(
				'dataField'     => 'ASEGURADO_BENEFICIARIO_OTRO',
				'dataType'      => 'string',
				'caption'       => 'OTRO? CUAL'
			),
			array(
				'dataField'     => 'PRIMER_APELLIDO',
				'dataType'      => 'string',
				'caption'       => 'PRIMER APELLIDO'
			),
			array(
				'dataField'     => 'SEGUNDO_APELLIDO',
				'dataType'      => 'string',
				'caption'       => 'SEGUNDO APELLIDO'
			),
			array(
				'dataField'     => 'NOMBRES',
				'dataType'      => 'string',
				'caption'       => 'NOMBRES'
			),
			array(
				'dataField'     => 'NOMBRE_O_RAZON_SOCIAL',
				'dataType'      => 'string',
				'caption'       => 'NOMBRE O RAZON SOCIAL'
			),
			array(
				'dataField'     => 'TIPO_DOCUMENTO',
				'dataType'      => 'string',
				'caption'       => 'TIPO DE DOCUMENTO'
			),
			array(
				'dataField'     => 'COD_DOCUMENTO',
				'dataType'      => 'string',
				'caption'       => 'CODIGO DE DOCUMENTO'
			),
			array(
				'dataField'     => 'TIPO_SOCIEDAD',
				'dataType'      => 'string',
				'caption'       => 'TIPO DE SOCIEDAD'
			),
			array(
				'dataField'     => 'LUGAR_EXPEDICION',
				'dataType'      => 'string',
				'caption'       => 'LUGAR DE EXPEDICION'
			),
			array(
				'dataField'     => 'SEXO',
				'dataType'      => 'string',
				'caption'       => 'SEXO F/M'
			),
			array(
				'dataField'     => 'ESTADO_CIVIL',
				'dataType'      => 'string',
				'caption'       => 'ESTADO CIVIL'
			),
			array(
				'dataField'     => 'FECHA_EXPEDICION_DOCUMENTO_PN',
				'dataType'      => 'date',
				'format'  		=> 'dd/MM/yyyy',
				'caption'       => 'FECHA EXPEDICION DOCUMENTO PERSONA NATURAL(XX/XX/XXXX)'
			),
			array(
				'dataField'     => 'FECHA_NACIMIENTO_PN',
				'dataType'      => 'date',
				'format'  		=> 'dd/MM/yyyy',
				'caption'       => 'FECHA NACIMIENTO PERSONA NATURAL(XX/XX/XXXX)'
			),
			array(
				'dataField'     => 'LUGAR_NACIMIENTO_PN',
				'dataType'      => 'string',
				'caption'       => 'LUGAR NACIMIENTO PERSONA NATURAL'
			),
			array(
				'dataField'     => 'NACIONALIDAD_1_PN',
				'dataType'      => 'string',
				'caption'       => 'NACIONALIDAD 1 PERSONA NATURAL'
			),
			array(
				'dataField'     => 'NACIONALIDAD_2_PN',
				'dataType'      => 'string',
				'caption'       => 'NACIONALIDAD 2 PERSONA NATURAL'
			),
			array(
				'dataField'     => 'DIRECCION_OFICINA_PRINCIPAL_RESIDENCIA',
				'dataType'      => 'string',
				'caption'       => 'DIRECCION OFICINA PRINCIPAL / RESIDENCIA'
			),
			array(
				'dataField'     => 'TIPO_EMPRESA',
				'dataType'      => 'string',
				'caption'       => 'TIPO EMPRESA'
			),
			array(
				'dataField'     => 'CIIU_ACTIVIDAD_ECONOMICA',
				'dataType'      => 'string',
				'caption'       => 'CIIU ACTIVIDAD ECONOMICA'
			),
			array(
				'dataField'     => 'CIIU_ACTIVIDAD_ECONOMICA_OTRA',
				'dataType'      => 'string',
				'caption'       => 'CIIU ACTIVIDAD ECONOMICA OTRA'
			),
			array(
				'dataField'     => 'CIIU_COD',
				'dataType'      => 'string',
				'caption'       => 'CIIU (COD)'
			),
			array(
				'dataField'     => 'SECTOR',
				'dataType'      => 'string',
				'caption'       => 'SECTOR'
			),
			array(
				'dataField'     => 'TIPO_ACTIVIDAD',
				'dataType'      => 'string',
				'caption'       => 'TIPO ACTIVIDAD'
			),
			array(
				'dataField'     => 'BREVE_DESCRIPCION',
				'dataType'      => 'string',
				'caption'       => 'BREVE DESCRIPCION DEL OBJETO SOCIAL'
			),
			array(
				'dataField'     => 'DEPARTAMENTO_OFICINA_RESIDENCIA',
				'dataType'      => 'string',
				'caption'       => 'DEPARTAMENTO OFICINA / RESIDENCIA'
			),
			array(
				'dataField'     => 'CIUDAD_OFICINA_RESIDENCIA',
				'dataType'      => 'string',
				'caption'       => 'CIUDAD OFICINA / RESIDENCIA'
			),
			array(
				'dataField'     => 'TELEFONO_OFICINA_RESIDENCIA',
				'dataType'      => 'string',
				'caption'       => 'TELEFONO OFICINA PJ / RESIDENCIA PN'
			),
			array(
				'dataField'     => 'CELULAR',
				'dataType'      => 'string',
				'caption'       => 'CELULAR'
			),
			array(
				'dataField'     => 'PAGINA_WEB',
				'dataType'      => 'string',
				'caption'       => 'PAGINA WEB'
			),
			array(
				'dataField'     => 'CORREO_ELECTRONICO',
				'dataType'      => 'string',
				'caption'       => 'EMAIL'
			),
			array(
				'dataField'     => 'SUCURSAL_DEPARATAMENTO',
				'dataType'      => 'string',
				'caption'       => 'DEPARTAMENTO SUCURSAL'
			),
			array(
				'dataField'     => 'SUCURSAL_CIUDAD',
				'dataType'      => 'string',
				'caption'       => 'CIUDAD SUCURSAL'
			),
			array(
				'dataField'     => 'SUCURSAL_DIRECCION',
				'dataType'      => 'string',
				'caption'       => 'DIRECCION SUCURSAL'
			),
			array(
				'dataField'     => 'SUCURSAL_TELEFONO',
				'dataType'      => 'string',
				'caption'       => 'TELEFONO SUCURSAL'
			),
			array(
				'dataField'     => 'SUCURSAL_FAX',
				'dataType'      => 'string',
				'caption'       => 'SUCURSAL FAX'
			),
			array(
				'dataField'     => 'OCUPACION',
				'dataType'      => 'string',
				'caption'       => 'OCUPACION'
			),
			array(
				'dataField'     => 'CARGO',
				'dataType'      => 'string',
				'caption'       => 'CARGO'
			),
			array(
				'dataField'     => 'EMPRESA_DONDE_TRABAJA_PN',
				'dataType'      => 'string',
				'caption'       => 'EMPRESA DONDE TRABAJA PN'
			),
			array(
				'dataField'     => 'DIRECCION_EMPRESA_PN',
				'dataType'      => 'string',
				'caption'       => 'DIRECCION EMPRESA PN'
			),
			array(
				'dataField'     => 'CIUDAD_EMPRESA_PN',
				'dataType'      => 'string',
				'caption'       => 'CIUDAD EMPRESA PN'
			),
			array(
				'dataField'     => 'DEPARTAMENTO_EMPRESA_PN',
				'dataType'      => 'string',
				'caption'       => 'DEPARTAMENTO EMPRESA PN'
			),
			array(
				'dataField'     => 'TELEFONO_EMPRESA_PN',
				'dataType'      => 'string',
				'caption'       => 'TELEFONO EMPRESA PN'
			),
			array(
				'dataField'     => 'ACTIVIDAD_SEVUNDARIA_PN',
				'dataType'      => 'string',
				'caption'       => 'ACTIVIDAD SECUNDARIA PN'
			),
			array(
				'dataField'     => 'CIIU_SECUNDARIA_PN',
				'dataType'      => 'string',
				'caption'       => 'CIIU SECUNDARIA PN'
			),
			array(
				'dataField'     => 'REPRESENTANTE_LEGAL_PRIMER_APELLIDO',
				'dataType'      => 'string',
				'caption'       => 'REPRESENTANTE LEGAL PRIMER APELLIDO'
			),
			array(
				'dataField'     => 'REPRESENTANTE_LEGAL_SEGUNDO_APELLIDO',
				'dataType'      => 'string',
				'caption'       => 'REPRESENTANTE LEGAL SEGUNDO APELLIDO'
			),
			array(
				'dataField'     => 'REPRESENTANTE_LEGAL_NOMBRES',
				'dataType'      => 'string',
				'caption'       => 'REPRESENTANTE LEGAL NOMBRES'
			),
			array(
				'dataField'     => 'REPRESENTANTE_TIPO_DOCUMENTO',
				'dataType'      => 'string',
				'caption'       => 'TIPO DE DOCUMENTO RL'
			),
			array(
				'dataField'     => 'REPRESENTANTE_COD_DOCUMENTO',
				'dataType'      => 'string',
				'caption'       => 'COD DE DOCUMENTO RL'
			),
			array(
				'dataField'     => 'REPRESENTANTE_DOCUMENTO',
				'dataType'      => 'string',
				'caption'       => 'NUMERO IDENTIFICACION RL'
			),
			array(
				'dataField'     => 'REPRESENTANTE_FECHA_EXPEDICION',
				'dataType'      => 'date',
				'format'  		=> 'dd/MM/yyyy',
				'caption'       => 'FECHA DE EXPEDICIÓN RL (xx/xx/xxxx)'
			),
			array(
				'dataField'     => 'REPRESENTANTE_LUGAR_EXPEDICION',
				'dataType'      => 'string',
				'caption'       => 'LUGAR DE EXPEDICIÓN RL'
			),
			array(
				'dataField'     => 'REPRESENTANTE_FECHA_NACIMIENTO',
				'dataType'      => 'date',
				'format'  		=> 'dd/MM/yyyy',
				'caption'       => 'FECHA NACIMIENTO RL'
			),
			array(
				'dataField'     => 'REPRESENTANTE_LUGAR_NACIMIENTO',
				'dataType'      => 'string',
				'caption'       => 'LUGAR DE NACIMIENTO RL'
			),
			array(
				'dataField'     => 'REPRESENTANTE_NACIONALIDAD_1',
				'dataType'      => 'string',
				'caption'       => 'NACIONALIDAD 1 RL'
			),
			array(
				'dataField'     => 'REPRESENTANTE_NACIONALIDAD_2',
				'dataType'      => 'string',
				'caption'       => 'NACIONALIDAD 2 RL'
			),
			array(
				'dataField'     => 'REPRESENTANTE_EMAIL',
				'dataType'      => 'string',
				'caption'       => 'EMAIL RL'
			),
			array(
				'dataField'     => 'REPRESENTANTE_DIRECCION_RESIDENCIA',
				'dataType'      => 'string',
				'caption'       => 'Dirección RESIDENCIA RL'
			),
			array(
				'dataField'     => 'REPRESENTANTE_CIUDAD_RESIDENCIA',
				'dataType'      => 'string',
				'caption'       => 'CIUDAD RESIDENCIA RL'
			),
			array(
				'dataField'     => 'REPRESENTANTE_DEPARTAMENTO_RESIDENCIA',
				'dataType'      => 'string',
				'caption'       => 'DEPARTAMENTO RESIDENCIA RL '
			),
			array(
				'dataField'     => 'REPRESENTANTE_PAIS_RESIDENCIA',
				'dataType'      => 'string',
				'caption'       => 'PAIS RESIDENCIA RL'
			),
			array(
				'dataField'     => 'REPRESENTANTE_TELEFONO_RESIDENCIA',
				'dataType'      => 'string',
				'caption'       => 'TELEFONO RESIDENCIA RL'
			),
			array(
				'dataField'     => 'REPRESENTANTE_CELULAR_RESIDENCIA',
				'dataType'      => 'string',
				'caption'       => 'CELULAR RL'
			),
			array(
				'dataField'     => 'REPRESENTANTE_PERSONA_PUBLICA',
				'dataType'      => 'string',
				'caption'       => 'ALGUNOS DE LOS ADMINISTRADORES, REPRESENTANTES LEGALES MIEMBROS DE LA JUNTA DIRECTIVA ES UNA PERSONA PUBLICAMENTE EXPUESTA? '
			),
			array(
				'dataField'     => 'REPRESENTANTE_RECURSOS_PUBLICOS',
				'dataType'      => 'string',
				'caption'       => 'POR SU CARGO O ACTIVIDAD ALGUNO DE LOS ADMINISTRADORES REPRESENTANTES LEGALES, MIEMBROS DE LA JUNTA DIRECTIVA , ADMINISTRA RECURSOS PUBLICOS ?'
			),
			array(
				'dataField'     => 'REPRESENTANTE_OBLIGACIONES_TRIBUTARIAS',
				'dataType'      => 'string',
				'caption'       => 'ES USTED SUJETO DE OBLIGACIONES TRIBUTARIAS EN OTRO PAIS O GRUPO DE PAISES ?'
			),
			array(
				'dataField'     => 'REPRESENTANTE_OBLIGACIONES_TRIBUTARIAS_CUAL',
				'dataType'      => 'string',
				'caption'       => 'CUALES'
			),
			array(
				'dataField'     => 'PERSONA_PUBLICA_PN',
				'dataType'      => 'string',
				'caption'       => 'ES USTED UNA PERSONA PUBLICAMENTE EXPUESTA?'
			),
			array(
				'dataField'     => 'VINCULO_PERSONA_PUBLICA_PN',
				'dataType'      => 'string',
				'caption'       => 'EXISTE ALGUN VINCULO ENTRE USTED Y UNA PERSONA CONSIDERADA PUBLICAMENTE EXPUESTA?'
			),
			array(
				'dataField'     => 'RECURSOS_PUBLICOS_PN',
				'dataType'      => 'string',
				'caption'       => 'POR SU CARGO O ACTIVIDAD ADMINISTRA RECURSOS PUBLICOS ?'
			),
			array(
				'dataField'     => 'A1_TIPO_ID',
				'dataType'      => 'string',
				'caption'       => 'A.1 TIPO ID'
			),
			array(
				'dataField'     => 'A1_NUMERO_ID',
				'dataType'      => 'string',
				'caption'       => 'A.1 NUMERO ID'
			),
			array(
				'dataField'     => 'A1_NOMBRES_APELLIDOS_COMPLETOS',
				'dataType'      => 'string',
				'caption'       => 'A.1 NOMBRES Y APELLIDOS COMPLETOS'
			),
			array(
				'dataField'     => 'A1_PARTICIPACION',
				'dataType'      => 'string',
				'caption'       => 'A.1 % PARTICIPACION'
			),
			array(
				'dataField'     => 'A1_PERSONA_JURIDICA_QUE_COTIZA_EN_BOLSA',
				'dataType'      => 'string',
				'caption'       => 'A.1 ES PERSONA JURIDICA QUE COTIZA EN BOLSA?'
			),
			array(
				'dataField'     => 'A1_PERSONA_PUBLICAMENTE_EXPUESTA_O_VINCULADO_CON_UNA_DE_ELLAS',
				'dataType'      => 'string',
				'caption'       => 'A.1 ES PERSONA PUBLICAMENTE EXPUESTA O VINCULADO CON UNA DE ELLAS?'
			),
			array(
				'dataField'     => 'A1_SUJETO_DE_TRIBUTACION_EN_OTRO_PAIS',
				'dataType'      => 'string',
				'caption'       => 'A.1 ES SUJETO DE TRIBUTACION EN OTRO PAIS U OTRO GRUPO DE PAISES?'
			),
			array(
				'dataField'     => 'A2_TIPO_ID',
				'dataType'      => 'string',
				'caption'       => 'A.2 TIPO ID'
			),
			array(
				'dataField'     => 'A2_NUMERO_ID',
				'dataType'      => 'string',
				'caption'       => 'A.2 NUMERO ID'
			),
			array(
				'dataField'     => 'A2_NOMBRES_APELLIDOS_COMPLETOS',
				'dataType'      => 'string',
				'caption'       => 'A.2 NOMBRES Y APELLIDOS COMPLETOS'
			),
			array(
				'dataField'     => 'A2_PARTICIPACION',
				'dataType'      => 'string',
				'caption'       => 'A.2 % PARTICIPACION'
			),
			array(
				'dataField'     => 'A2_PERSONA_JURIDICA_QUE_COTIZA_EN_BOLSA',
				'dataType'      => 'string',
				'caption'       => 'A.2 ES PERSONA JURIDICA QUE COTIZA EN BOLSA?'
			),
			array(
				'dataField'     => 'A2_PERSONA_PUBLICAMENTE_EXPUESTA_O_VINCULADO_CON_UNA_DE_ELLAS',
				'dataType'      => 'string',
				'caption'       => 'A.2 ES PERSONA PUBLICAMENTE EXPUESTA O VINCULADO CON UNA DE ELLAS?'
			),
			array(
				'dataField'     => 'A2_SUJETO_DE_TRIBUTACION_EN_OTRO_PAIS',
				'dataType'      => 'string',
				'caption'       => 'A.2 ES SUJETO DE TRIBUTACION EN OTRO PAIS U OTRO GRUPO DE PAISES?'
			),
			array(
				'dataField'     => 'A3_TIPO_ID',
				'dataType'      => 'string',
				'caption'       => 'A.3 TIPO ID'
			),
			array(
				'dataField'     => 'A3_NUMERO_ID',
				'dataType'      => 'string',
				'caption'       => 'A.3 NUMERO ID'
			),
			array(
				'dataField'     => 'A3_NOMBRES_APELLIDOS_COMPLETOS',
				'dataType'      => 'string',
				'caption'       => 'A.3 NOMBRES Y APELLIDOS COMPLETOS'
			),
			array(
				'dataField'     => 'A3_PARTICIPACION',
				'dataType'      => 'string',
				'caption'       => 'A.3 % PARTICIPACION'
			),
			array(
				'dataField'     => 'A3_PERSONA_JURIDICA_QUE_COTIZA_EN_BOLSA',
				'dataType'      => 'string',
				'caption'       => 'A.3 ES PERSONA JURIDICA QUE COTIZA EN BOLSA?'
			),
			array(
				'dataField'     => 'A3_PERSONA_PUBLICAMENTE_EXPUESTA_O_VINCULADO_CON_UNA_DE_ELLAS',
				'dataType'      => 'string',
				'caption'       => 'A.3 ES PERSONA PUBLICAMENTE EXPUESTA O VINCULADO CON UNA DE ELLAS?'
			),
			array(
				'dataField'     => 'A3_SUJETO_DE_TRIBUTACION_EN_OTRO_PAIS',
				'dataType'      => 'string',
				'caption'       => 'A.3 ES SUJETO DE TRIBUTACION EN OTRO PAIS U OTRO GRUPO DE PAISES?'
			),
			array(
				'dataField'     => 'A4_TIPO_ID',
				'dataType'      => 'string',
				'caption'       => 'A.4 TIPO ID'
			),
			array(
				'dataField'     => 'A4_NUMERO_ID',
				'dataType'      => 'string',
				'caption'       => 'A.4 NUMERO ID'
			),
			array(
				'dataField'     => 'A4_NOMBRES_APELLIDOS_COMPLETOS',
				'dataType'      => 'string',
				'caption'       => 'A.4 NOMBRES Y APELLIDOS COMPLETOS'
			),
			array(
				'dataField'     => 'A4_PARTICIPACION',
				'dataType'      => 'string',
				'caption'       => 'A.4 % PARTICIPACION'
			),
			array(
				'dataField'     => 'A4_PERSONA_JURIDICA_QUE_COTIZA_EN_BOLSA',
				'dataType'      => 'string',
				'caption'       => 'A.4 ES PERSONA JURIDICA QUE COTIZA EN BOLSA?'
			),
			array(
				'dataField'     => 'A4_PERSONA_PUBLICAMENTE_EXPUESTA_O_VINCULADO_CON_UNA_DE_ELLAS',
				'dataType'      => 'string',
				'caption'       => 'A.4 ES PERSONA PUBLICAMENTE EXPUESTA O VINCULADO CON UNA DE ELLAS?'
			),
			array(
				'dataField'     => 'A4_SUJETO_DE_TRIBUTACION_EN_OTRO_PAIS',
				'dataType'      => 'string',
				'caption'       => 'A.4 ES SUJETO DE TRIBUTACION EN OTRO PAIS U OTRO GRUPO DE PAISES?'
			),
			array(
				'dataField'     => 'A5_TIPO_ID',
				'dataType'      => 'string',
				'caption'       => 'A.5 TIPO ID'
			),
			array(
				'dataField'     => 'A5_NUMERO_ID',
				'dataType'      => 'string',
				'caption'       => 'A.5 NUMERO ID'
			),
			array(
				'dataField'     => 'A5_NOMBRES_APELLIDOS_COMPLETOS',
				'dataType'      => 'string',
				'caption'       => 'A.5 NOMBRES Y APELLIDOS COMPLETOS'
			),
			array(
				'dataField'     => 'A5_PARTICIPACION',
				'dataType'      => 'string',
				'caption'       => 'A.5 % PARTICIPACION'
			),
			array(
				'dataField'     => 'A5_PERSONA_JURIDICA_QUE_COTIZA_EN_BOLSA',
				'dataType'      => 'string',
				'caption'       => 'A.5 ES PERSONA JURIDICA QUE COTIZA EN BOLSA?'
			),
			array(
				'dataField'     => 'A5_PERSONA_PUBLICAMENTE_EXPUESTA_O_VINCULADO_CON_UNA_DE_ELLAS',
				'dataType'      => 'string',
				'caption'       => 'A.5 ES PERSONA PUBLICAMENTE EXPUESTA O VINCULADO CON UNA DE ELLAS?'
			),
			array(
				'dataField'     => 'A5_SUJETO_DE_TRIBUTACION_EN_OTRO_PAIS',
				'dataType'      => 'string',
				'caption'       => 'A.5 ES SUJETO DE TRIBUTACION EN OTRO PAIS U OTRO GRUPO DE PAISES?'
			),
			array(
				'dataField'     => 'A5_SUJETO_DE_TRIBUTACION_EN_OTRO_PAIS_DESC',
				'dataType'      => 'string',
				'caption'       => 'INDIQUE CUAL ?'
			),
			array(
				'dataField'     => 'PPE_VINCULO_RELACION_1',
				'dataType'      => 'string',
				'caption'       => 'VINCULO RELACION'
			),
			array(
				'dataField'     => 'PPE_NOMBRE_COMPLETO_1',
				'dataType'      => 'string',
				'caption'       => 'NOMBRE COMPLETO'
			),
			array(
				'dataField'     => 'PPE_TIPO_IDENTIFICACION_1',
				'dataType'      => 'string',
				'caption'       => 'TIPO DE IDENTIFICACION'
			),
			array(
				'dataField'     => 'PPE_NUMERO_IDENTFICACION_1',
				'dataType'      => 'string',
				'caption'       => 'NUMERO DE IDENTFICACION'
			),
			array(
				'dataField'     => 'PPE_NACIONALIDAD_1',
				'dataType'      => 'string',
				'caption'       => 'NACIONALIDAD'
			),
			array(
				'dataField'     => 'PPE_ENTIDAD_1',
				'dataType'      => 'string',
				'caption'       => 'ENTIDAD'
			),
			array(
				'dataField'     => 'PPE_CARGO_1',
				'dataType'      => 'string',
				'caption'       => 'CARGO'
			),
			array(
				'dataField'     => 'PPE_FECHA_DESVINCULACION_1',
				'dataType'      => 'string',
				'caption'       => 'FECHA DESVINCULACION'
			),
			array(
				'dataField'     => 'PPE_VINCULO_RELACION_2',
				'dataType'      => 'string',
				'caption'       => 'VINCULO RELACION'
			),
			array(
				'dataField'     => 'PPE_NOMBRE_COMPLETO_2',
				'dataType'      => 'string',
				'caption'       => 'NOMBRE COMPLETO'
			),
			array(
				'dataField'     => 'PPE_TIPO_IDENTIFICACION_2',
				'dataType'      => 'string',
				'caption'       => 'TIPO DE IDENTIFICACION'
			),
			array(
				'dataField'     => 'PPE_NUMERO_IDENTFICACION_2',
				'dataType'      => 'string',
				'caption'       => 'NUMERO DE IDENTFICACION'
			),
			array(
				'dataField'     => 'PPE_NACIONALIDAD_2',
				'dataType'      => 'string',
				'caption'       => 'NACIONALIDAD'
			),
			array(
				'dataField'     => 'PPE_ENTIDAD_2',
				'dataType'      => 'string',
				'caption'       => 'ENTIDAD'
			),
			array(
				'dataField'     => 'PPE_CARGO_2',
				'dataType'      => 'string',
				'caption'       => 'CARGO'
			),
			array(
				'dataField'     => 'PPE_FECHA_DESVINCULACION_2',
				'dataType'      => 'string',
				'caption'       => 'FECHA DESVINCULACION'
			),
			array(
				'dataField'     => 'PPE_VINCULO_RELACION_3',
				'dataType'      => 'string',
				'caption'       => 'VINCULO RELACION'
			),
			array(
				'dataField'     => 'PPE_NOMBRE_COMPLETO_3',
				'dataType'      => 'string',
				'caption'       => 'NOMBRE COMPLETO'
			),
			array(
				'dataField'     => 'PPE_TIPO_IDENTIFICACION_3',
				'dataType'      => 'string',
				'caption'       => 'TIPO DE IDENTIFICACION'
			),
			array(
				'dataField'     => 'PPE_NUMERO_IDENTFICACION_3',
				'dataType'      => 'string',
				'caption'       => 'NUMERO DE IDENTFICACION'
			),
			array(
				'dataField'     => 'PPE_NACIONALIDAD_3',
				'dataType'      => 'string',
				'caption'       => 'NACIONALIDAD'
			),
			array(
				'dataField'     => 'PPE_ENTIDAD_3',
				'dataType'      => 'string',
				'caption'       => 'ENTIDAD'
			),
			array(
				'dataField'     => 'PPE_CARGO_3',
				'dataType'      => 'string',
				'caption'       => 'CARGO'
			),
			array(
				'dataField'     => 'PPE_FECHA_DESVINCULACION_3',
				'dataType'      => 'string',
				'caption'       => 'FECHA DESVINCULACION'
			),
			array(
				'dataField'     => 'PPE_VINCULO_RELACION_4',
				'dataType'      => 'string',
				'caption'       => 'VINCULO RELACION'
			),
			array(
				'dataField'     => 'PPE_NOMBRE_COMPLETO_4',
				'dataType'      => 'string',
				'caption'       => 'NOMBRE COMPLETO'
			),
			array(
				'dataField'     => 'PPE_TIPO_IDENTIFICACION_4',
				'dataType'      => 'string',
				'caption'       => 'TIPO DE IDENTIFICACION'
			),
			array(
				'dataField'     => 'PPE_NUMERO_IDENTFICACION_4',
				'dataType'      => 'string',
				'caption'       => 'NUMERO DE IDENTFICACION'
			),
			array(
				'dataField'     => 'PPE_NACIONALIDAD_4',
				'dataType'      => 'string',
				'caption'       => 'NACIONALIDAD'
			),
			array(
				'dataField'     => 'PPE_ENTIDAD_4',
				'dataType'      => 'string',
				'caption'       => 'ENTIDAD'
			),
			array(
				'dataField'     => 'PPE_CARGO_4',
				'dataType'      => 'string',
				'caption'       => 'CARGO'
			),
			array(
				'dataField'     => 'PPE_FECHA_DESVINCULACION_4',
				'dataType'      => 'string',
				'caption'       => 'FECHA DESVINCULACION'
			),
			array(
				'dataField'     => 'SUB_ACCIONISTA_TIPO_ID_1',
				'dataType'      => 'string',
				'caption'       => 'TIPO ID'
			),
			array(
				'dataField'     => 'SUB_ACCIONISTA_NUMERO_ID_1',
				'dataType'      => 'string',
				'caption'       => 'NUMERO ID'
			),
			array(
				'dataField'     => 'SUB_ACCIONISTA_RAZON_SOCIAL_NOMBRES_APELLIDOS_1',
				'dataType'      => 'string',
				'caption'       => 'RAZON SOCIAL/ NOMBRES APELLIDOS'
			),
			array(
				'dataField'     => 'SUB_ACCIONISTA_PARTICIPACION_1',
				'dataType'      => 'string',
				'caption'       => '%PARTICIPACION'
			),
			array(
				'dataField'     => 'SUB_ACCIONISTA_NOMBRE_RAZON_SOCIAL_DE_LA_SOCIEDAD_A_LA_QUE_ES_ACCIONISTA_1',
				'dataType'      => 'string',
				'caption'       => 'NOMBRE RAZON SOCIAL DE LA SOCIEDAD A LA QUE ES ACCIONISTA'
			),
			array(
				'dataField'     => 'SUB_ACCIONISTA_NIT_1',
				'dataType'      => 'string',
				'caption'       => 'NIT'
			),
			array(
				'dataField'     => 'SUB_ACCIONISTA_TIPO_ID_2',
				'dataType'      => 'string',
				'caption'       => 'TIPO ID'
			),
			array(
				'dataField'     => 'SUB_ACCIONISTA_NUMERO_ID_2',
				'dataType'      => 'string',
				'caption'       => 'NUMERO ID'
			),
			array(
				'dataField'     => 'SUB_ACCIONISTA_RAZON_SOCIAL_NOMBRES_APELLIDOS_2',
				'dataType'      => 'string',
				'caption'       => 'RAZON SOCIAL/ NOMBRES APELLIDOS'
			),
			array(
				'dataField'     => 'SUB_ACCIONISTA_PARTICIPACION_2',
				'dataType'      => 'string',
				'caption'       => '%PARTICIPACION'
			),
			array(
				'dataField'     => 'SUB_ACCIONISTA_NOMBRE_RAZON_SOCIAL_DE_LA_SOCIEDAD_A_LA_QUE_ES_ACCIONISTA_2',
				'dataType'      => 'string',
				'caption'       => 'NOMBRE RAZON SOCIAL DE LA SOCIEDAD A LA QUE ES ACCIONISTA'
			),
			array(
				'dataField'     => 'SUB_ACCIONISTA_NIT_2',
				'dataType'      => 'string',
				'caption'       => 'NIT'
			),
			array(
				'dataField'     => 'SUB_ACCIONISTA_TIPO_ID_3',
				'dataType'      => 'string',
				'caption'       => 'TIPO ID'
			),
			array(
				'dataField'     => 'SUB_ACCIONISTA_NUMERO_ID_3',
				'dataType'      => 'string',
				'caption'       => 'NUMERO ID'
			),
			array(
				'dataField'     => 'SUB_ACCIONISTA_RAZON_SOCIAL_NOMBRES_APELLIDOS_3',
				'dataType'      => 'string',
				'caption'       => 'RAZON SOCIAL/ NOMBRES APELLIDOS'
			),
			array(
				'dataField'     => 'SUB_ACCIONISTA_PARTICIPACION_3',
				'dataType'      => 'string',
				'caption'       => '%PARTICIPACION'
			),
			array(
				'dataField'     => 'SUB_ACCIONISTA_NOMBRE_RAZON_SOCIAL_DE_LA_SOCIEDAD_A_LA_QUE_ES_ACCIONISTA_3',
				'dataType'      => 'string',
				'caption'       => 'NOMBRE RAZON SOCIAL DE LA SOCIEDAD A LA QUE ES ACCIONISTA'
			),
			array(
				'dataField'     => 'SUB_ACCIONISTA_NIT_3',
				'dataType'      => 'string',
				'caption'       => 'NIT'
			),
			array(
				'dataField'     => 'SUB_ACCIONISTA_TIPO_ID_4',
				'dataType'      => 'string',
				'caption'       => 'TIPO ID'
			),
			array(
				'dataField'     => 'SUB_ACCIONISTA_NUMERO_ID_4',
				'dataType'      => 'string',
				'caption'       => 'NUMERO ID'
			),
			array(
				'dataField'     => 'SUB_ACCIONISTA_RAZON_SOCIAL_NOMBRES_APELLIDOS_4',
				'dataType'      => 'string',
				'caption'       => 'RAZON SOCIAL/ NOMBRES APELLIDOS'
			),
			array(
				'dataField'     => 'SUB_ACCIONISTA_PARTICIPACION_4',
				'dataType'      => 'string',
				'caption'       => '%PARTICIPACION'
			),
			array(
				'dataField'     => 'SUB_ACCIONISTA_NOMBRE_RAZON_SOCIAL_DE_LA_SOCIEDAD_A_LA_QUE_ES_ACCIONISTA_4',
				'dataType'      => 'string',
				'caption'       => 'NOMBRE RAZON SOCIAL DE LA SOCIEDAD A LA QUE ES ACCIONISTA'
			),
			array(
				'dataField'     => 'SUB_ACCIONISTA_NIT_4',
				'dataType'      => 'string',
				'caption'       => 'NIT'
			),
			array(
				'dataField'     => 'INGRESOS',
				'dataType'      => 'string',
				'caption'       => 'INGRESOS MENSUALES'
			),
			array(
				'dataField'     => 'EGRESOS',
				'dataType'      => 'string',
				'caption'       => 'EGRESOS MENSUALES'
			),
			array(
				'dataField'     => 'ACTIVOS',
				'dataType'      => 'string',
				'caption'       => 'ACTIVOS'
			),
			array(
				'dataField'     => 'PASIVOS',
				'dataType'      => 'string',
				'caption'       => 'PASIVOS'
			),
			array(
				'dataField'     => 'PATRIMONIO',
				'dataType'      => 'string',
				'caption'       => 'PATRIMONIO'
			),
			array(
				'dataField'     => 'OTROS_INGRESOS',
				'dataType'      => 'string',
				'caption'       => 'OTROS INGRESOS'
			),
			array(
				'dataField'     => 'CONCEPTO_OTROS_INGRESOS',
				'dataType'      => 'string',
				'caption'       => 'CONCEPTO OTROS INGRESOS'
			),
			array(
				'dataField'     => 'DECLARACION_ORIGEN_FONDOS',
				'dataType'      => 'string',
				'caption'       => 'DECLARACION ORIGEN DE FONDOS '
			),
			array(
				'dataField'     => 'TRANSACCIONES_MONEDA_EXTRANJERA',
				'dataType'      => 'string',
				'caption'       => 'REALIZA TRANSACCIONES EN MONEDA EXTRANJERA? ¿SI O  NO?'
			),
			array(
				'dataField'     => 'TRANSACCIONES_MONEDA_EXTRANJERA_CUAL',
				'dataType'      => 'string',
				'caption'       => 'SI? CUAL'
			),
			array(
				'dataField'     => 'TRANSACCIONES_MONEDA_EXTRANJERA_OTRAS',
				'dataType'      => 'string',
				'caption'       => 'OTRAS? INDIQUE OTRAS OPERACIONES'
			),
			array(
				'dataField'     => 'PRODUCTOS_EXTERIOR',
				'dataType'      => 'string',
				'caption'       => 'POSEE PRODUCTOS FINANCIEROS EN EL EXTERIOR ?'
			),
			array(
				'dataField'     => 'CUENTAS_MONEDA_EXTRANJERA',
				'dataType'      => 'string',
				'caption'       => 'POSEEE CUENTAS EN MONEDA EXTRANJERA?'
			),
			array(
				'dataField'     => 'PRODUCTO_TIPO_PRODUCTO_1',
				'dataType'      => 'string',
				'caption'       => 'TIPO DE PRODUCTO'
			),
			array(
				'dataField'     => 'PRODUCTO_IDENTIFICACION_O_NUMERO_DE_PRODUCTO_1',
				'dataType'      => 'string',
				'caption'       => 'IDENTIFICACION O NUMERO DE PRODUCTO'
			),
			array(
				'dataField'     => 'PRODUCTO_ENTIDAD_1',
				'dataType'      => 'string',
				'caption'       => 'ENTIDAD'
			),
			array(
				'dataField'     => 'PRODUCTO_MONTO_1',
				'dataType'      => 'string',
				'caption'       => 'MONTO'
			),
			array(
				'dataField'     => 'PRODUCTO_CIUDAD_1',
				'dataType'      => 'string',
				'caption'       => 'CIUDAD'
			),
			array(
				'dataField'     => 'PRODUCTO_PAIS_1',
				'dataType'      => 'string',
				'caption'       => 'PAIS'
			),
			array(
				'dataField'     => 'PRODUCTO_MONEDA_1',
				'dataType'      => 'string',
				'caption'       => 'MONEDA'
			),
			array(
				'dataField'     => 'RECLAMACIONES',
				'dataType'      => 'string',
				'caption'       => 'HA PRESENTADO RECLAMACIONES O HA RECIBIDO INDEMNIZACION EN SEGUROS EN LOS DOS ULTIMOS AÑOS ?'
			),
			array(
				'dataField'     => 'RECLAMACION_ANIO',
				'dataType'      => 'string',
				'caption'       => 'AÑO'
			),
			array(
				'dataField'     => 'RECLAMACION_RAMO',
				'dataType'      => 'string',
				'caption'       => 'RAMO'
			),
			array(
				'dataField'     => 'RECLAMACION_COMPANIA',
				'dataType'      => 'string',
				'caption'       => 'COMPANIA'
			),
			array(
				'dataField'     => 'RECLAMACION_VALOR',
				'dataType'      => 'string',
				'caption'       => 'VALOR'
			),
			array(
				'dataField'     => 'RECLAMACION_RESULTADO',
				'dataType'      => 'string',
				'caption'       => 'RESULTADO'
			)
		];

		if(!isset($resultadoReporte['error'])){
			foreach ($resultadoReporte as $keyResultadoReporte => $valueResultadoReporte) {

				$anexo_PPEs_cliente = $this->_clientes->getAllAnexosPPEClientById($valueResultadoReporte['CLIENTE_ID']);
				foreach ($anexo_PPEs_cliente as $keyAnexoPPE => $valueAnexoPPE) {

					$resultadoReporte[$keyResultadoReporte]['PPE_VINCULO_RELACION_' . ($keyAnexoPPE+1)] = $valueAnexoPPE['ppes_vinculo_relacion'];
					$resultadoReporte[$keyResultadoReporte]['PPE_NOMBRE_COMPLETO_' . ($keyAnexoPPE+1)] = $valueAnexoPPE['ppes_nombre'];
					$resultadoReporte[$keyResultadoReporte]['PPE_TIPO_IDENTIFICACION_' . ($keyAnexoPPE+1)] = $valueAnexoPPE['ppes_tipo_identificacion'];
					$resultadoReporte[$keyResultadoReporte]['PPE_MOTIVO_RECONOCIMIENTO_' . ($keyAnexoPPE+1)] = $valueAnexoPPE['ppes_motivo'];
					$resultadoReporte[$keyResultadoReporte]['PPE_NUMERO_IDENTFICACION_' . ($keyAnexoPPE+1)] = $valueAnexoPPE['ppes_no_documento'];
					$resultadoReporte[$keyResultadoReporte]['PPE_ENTIDAD_' . ($keyAnexoPPE+1)] = $valueAnexoPPE['ppes_entidad'];
					$resultadoReporte[$keyResultadoReporte]['PPE_CARGO_' . ($keyAnexoPPE+1)] = $valueAnexoPPE['ppes_cargo'];
					$resultadoReporte[$keyResultadoReporte]['PPE_FECHA_DESVINCULACION_' . ($keyAnexoPPE+1)] = $valueAnexoPPE['ppes_desvinculacion'];
				}

				$anexo_Productos_cliente = $this->_clientes->getAllProductosClienteById($valueResultadoReporte['CLIENTE_ID']);
				foreach ($anexo_Productos_cliente as $keyProducto => $valueProducto) {

					$resultadoReporte[$keyResultadoReporte]['PRODUCTO_TIPO_PRODUCTO_' . ($keyProducto+1)] = $valueProducto['tipo_producto'];
					$resultadoReporte[$keyResultadoReporte]['PRODUCTO_IDENTIFICACION_O_NUMERO_DE_PRODUCTO_' . ($keyProducto+1)] = $valueProducto['identificacion_producto'];
					$resultadoReporte[$keyResultadoReporte]['PRODUCTO_ENTIDAD_' . ($keyProducto+1)] = $valueProducto['entidad'];
					$resultadoReporte[$keyResultadoReporte]['PRODUCTO_MONTO_' . ($keyProducto+1)] = $valueProducto['monto'];
					$resultadoReporte[$keyResultadoReporte]['PRODUCTO_CIUDAD_' . ($keyProducto+1)] = $valueProducto['ciudad'];
					$resultadoReporte[$keyResultadoReporte]['PRODUCTO_PAIS_' . ($keyProducto+1)] = $valueProducto['pais'];
					$resultadoReporte[$keyResultadoReporte]['PRODUCTO_MONEDA_' . ($keyProducto+1)] = $valueProducto['moneda'];
				}

				if($valueResultadoReporte['TIPO_PERSONA'] == 'JUR'){

					$anexo_Accionistas_cliente = $this->_clientes->getAccionistasClienteById($valueResultadoReporte['CLIENTE_ID']);
					foreach ($anexo_Accionistas_cliente as $keyAccionista => $valueAccionista) {

						$resultadoReporte[$keyResultadoReporte]['A' . ($keyAccionista+1) . '_TIPO_ID'] = $valueAccionista['accionista_tipo_documento'];
						$resultadoReporte[$keyResultadoReporte]['A' . ($keyAccionista+1) . '_NUMERO_ID'] = $valueAccionista['accionista_documento'];
						$resultadoReporte[$keyResultadoReporte]['A' . ($keyAccionista+1) . '_NOMBRES_APELLIDOS_COMPLETOS'] = $valueAccionista['accionista_nombres_completos'];
						$resultadoReporte[$keyResultadoReporte]['A' . ($keyAccionista+1) . '_PARTICIPACION'] = $valueAccionista['accionista_participacion'];
						$resultadoReporte[$keyResultadoReporte]['A' . ($keyAccionista+1) . '_PERSONA_JURIDICA_QUE_COTIZA_EN_BOLSA'] = $valueAccionista['accionista_cotiza_bolsa'];
						$resultadoReporte[$keyResultadoReporte]['A' . ($keyAccionista+1) . '_PERSONA_PUBLICAMENTE_EXPUESTA_O_VINCULADO_CON_UNA_DE_ELLAS'] = $valueAccionista['accionista_persona_publica'];
						$resultadoReporte[$keyResultadoReporte]['A' . ($keyAccionista+1) . '_SUJETO_DE_TRIBUTACION_EN_OTRO_PAIS'] = ($valueAccionista['accionista_obligaciones_otro_pais'] == 1) ? $valueAccionista['accionista_obligaciones_otro_pais_desc'] : $valueAccionista['accionista_obligaciones_otro_pais'] ;
					}

					$anexo_SubAccionistas_cliente = $this->_clientes->getSubAccionistasClienteById($valueResultadoReporte['CLIENTE_ID']);
					foreach ($anexo_SubAccionistas_cliente as $keySubAccionista => $valueSubAccionista) {

						$resultadoReporte[$keyResultadoReporte]['SUB_ACCIONISTA_TIPO_ID_' . ($keySubAccionista+1)] = $valueSubAccionista['sub_accionista_tipo_documento'];
						$resultadoReporte[$keyResultadoReporte]['SUB_ACCIONISTA_NUMERO_ID_' . ($keySubAccionista+1)] = $valueSubAccionista['sub_accionista_numero_id'];
						$resultadoReporte[$keyResultadoReporte]['SUB_ACCIONISTA_RAZON_SOCIAL_NOMBRES_APELLIDOS_' . ($keySubAccionista+1)] = $valueSubAccionista['sub_accionista_razon_social'];
						$resultadoReporte[$keyResultadoReporte]['SUB_ACCIONISTA_PARTICIPACION_' . ($keySubAccionista+1)] = $valueSubAccionista['sub_accionista_participacion'];
						$resultadoReporte[$keyResultadoReporte]['SUB_ACCIONISTA_NOMBRE_RAZON_SOCIAL_DE_LA_SOCIEDAD_A_LA_QUE_ES_ACCIONISTA_' . ($keySubAccionista+1)] = $valueSubAccionista['sub_accionista_nombre_sociedad_accionista'];
						$resultadoReporte[$keyResultadoReporte]['SUB_ACCIONISTA_NIT_' . ($keySubAccionista+1)] = $valueSubAccionista['sub_accionista_documento'];
					}
				}

				if(!empty(trim($valueResultadoReporte['CLASE_VINCULACION']))){
					$clase_vinculaciones = explode(',',$valueResultadoReporte['CLASE_VINCULACION']);
					$resultado_vinculos = array();

					foreach ($clase_vinculaciones as $valueClaseVinculo) {
						$resultVinculo = $this->_global->getVinculacionesByid($valueClaseVinculo);
						array_push($resultado_vinculos,$resultVinculo['desc_vinculacion']);
					}

					$resultadoReporte[$keyResultadoReporte]['CLASE_VINCULACION'] = implode(', ',$resultado_vinculos);
				}


				if(!empty(trim($valueResultadoReporte['TOMADOR_ASEGURADO'])) && !is_null($valueResultadoReporte['TOMADOR_ASEGURADO'])){
					$relacion_tomador_asegurado = explode(',',$valueResultadoReporte['TOMADOR_ASEGURADO']);
					$resultado_tomador_asegurado = array();

					foreach ($relacion_tomador_asegurado as $valueTomadorAsegurado) {
						$resultTomadorAseg = $this->_global->getRelacionesByid($valueTomadorAsegurado);
						array_push($resultado_tomador_asegurado,$resultTomadorAseg['desc_relacion']);
					}

					$resultadoReporte[$keyResultadoReporte]['TOMADOR_ASEGURADO'] = implode(', ',$resultado_tomador_asegurado);
				}

				if(!empty(trim($valueResultadoReporte['TOMADOR_BENEFICIARIO'])) && !is_null($valueResultadoReporte['TOMADOR_BENEFICIARIO'])){
					$relacion_tomador_beneficiario = explode(',',$valueResultadoReporte['TOMADOR_BENEFICIARIO']);
					$resultado_tomador_beneficiario = array();

					foreach ($relacion_tomador_beneficiario as $valueTomadorBeneficiario) {
						$resultTomadorBenef = $this->_global->getRelacionesByid($valueTomadorBeneficiario);
						array_push($resultado_tomador_beneficiario,$resultTomadorBenef['desc_relacion']);
					}

					$resultadoReporte[$keyResultadoReporte]['TOMADOR_BENEFICIARIO'] = implode(', ',$resultado_tomador_beneficiario);
				}

				if(!empty(trim($valueResultadoReporte['ASEGURADO_BENEFICIARIO'])) && !is_null($valueResultadoReporte['TOMADOR_BENEFICIARIO'])){
					$relacion_asegurado_beneficiario = explode(',',$valueResultadoReporte['ASEGURADO_BENEFICIARIO']);
					$resultado_asegurado_beneficiario = array();

					foreach ($relacion_asegurado_beneficiario as $valueAseguradoBeneficiario) {
						$resultAseguradoBenef = $this->_global->getRelacionesByid($valueAseguradoBeneficiario);
						array_push($resultado_asegurado_beneficiario,$resultAseguradoBenef['desc_relacion']);
					}

					$resultadoReporte[$keyResultadoReporte]['ASEGURADO_BENEFICIARIO'] = implode(', ',$resultado_asegurado_beneficiario);
				}
			}
			//var_dump($resultadoReporte);
			$dataTable["data"] = $resultadoReporte;

		}

		return $dataTable;
	}

	public function reporteCapturasNaturales($fechasReporte = array()){

		$resultadoReporte = $this->_reportes->getDatosClientesCapturadosNaturales($fechasReporte);
		$dataTable = array('data' => array());
		$dataTable["fileName"] = 'REPORTE CAPTURA CLIENTES NATURALES';
		$dataTable['columns'] = [
			array(
				'dataField' => 'FECHA_RADICACION',
				'dataType'  => 'date',
				'format'  	=> 'dd/MM/yyyy',
				'caption'   => 'FECHA RADICACION (mm/dd/yyyy)'
			),
			array(
				'dataField' => 'USUARIO_RADICACION',
				'dataType'  => 'string',
				'caption'   => 'USUARIO RADICACION (mm/dd/yyyy)'
			),
			array(
				'dataField' => 'FECHA_CAPTURA',
				'dataType'  => 'date',
				'format'  	=> 'dd/MM/yyyy',
				'caption'   => 'FECHA CAPTURA (mm/dd/yyyy)'
			),			
			array(
				'dataField' => 'FECHA_VERIFICACION',
				'dataType'  => 'date',
				'format'  	=> 'dd/MM/yyyy',
				'caption'   => 'FECHA VERIFICACION (mm/dd/yyyy)'
			),
			array(
				'dataField' => 'FECHA_DILIGENCIAMIENTO',
				'dataType'  => 'date',
				'format'  	=> 'dd/MM/yyyy',
				'caption'   => 'FECHA DILIGENCIAMIENTO'
			),
			array(
				'dataField'     => 'CIUDAD_DILIGENCIAMIENTO',
				'dataType'      => 'string',
				'caption'       => 'CIUDAD DILIGENCIAMIENTO'
			),
			array(
				'dataField'     => 'SUCURSAL',
				'dataType'      => 'string',
				'caption'       => 'SUCURSAL'
			),
			array(
				'dataField' => 'COD_ASEG',
				'dataType'  => 'string',
				'caption'   => 'COD ASEG'
			),
			array(
				'dataField' => 'PRIMER_APELLIDO',
				'dataType'  => 'string',
				'caption'   => 'PRIMER APELLIDO'
			),
			array(
				'dataField' => 'SEGUNDO_APELLIDO',
				'dataType'  => 'string',
				'caption'   => 'SEGUNDO APELLIDO'
			),
			array(
				'dataField' => 'NOMBRES',
				'dataType'  => 'string',
				'caption'   => 'NOMBRES'
			),
			array(
				'dataField' => 'TIPO_DOCUMENTO',
				'dataType'  => 'string',
				'caption'   => 'TIPO DOCUMENTO'
			),
			// array(
			// 	'dataField' => 'COD_DOCUMENTO',
			// 	'dataType'  => 'string',
			// 	'caption'   => 'COD DOCUMENTO'
			// ),
			array(
				'dataField' => 'NUMERO_DOCUMENTO',
				'dataType'  => 'string',
				'caption'   => 'NUMERO DOCUMENTO'
			),
			array(
				'dataField' => 'LUGAR_NACIMIENTO',
				'dataType'  => 'string',
				'caption'   => 'LUGAR NACIMIENTO'
			),
			array(
				'dataField' => 'FECHA_NACIMIENTO',
				'dataType'  => 'date',
				'format'  	=> 'dd/MM/yyyy',
				'caption'   => 'FECHA NACIMIENTO'
			),
			array(
				'dataField' => 'OCUPACION_1',
				'dataType'  => 'string',
				'caption'   => 'OCUPACION 1'
			),
			array(
				'dataField' => 'OCUPACION_2',
				'dataType'  => 'string',
				'caption'   => 'OCUPACION 2'
			),
			array(
				'dataField' => 'CIIU_COD',
				'dataType'  => 'string',
				'caption'   => 'CIIU COD'
			),
			// array(
			// 	'dataField' => 'CIIU_ACTIVIDAD_ECONOMICA',
			// 	'dataType'  => 'string',
			// 	'caption'   => 'CIIU ACTIVIDAD ECONOMICA'
			// ),
			array(
				'dataField' => 'TIPO_ACTIVIDAD',
				'dataType'  => 'string',
				'caption'   => 'TIPO ACTIVIDAD'
			),
			array(
				'dataField' => 'EMPRESA_DONDE_TRABAJA',
				'dataType'  => 'string',
				'caption'   => 'EMPRESA DONDE TRABAJA'
			),
			array(
				'dataField' => 'CARGO',
				'dataType'  => 'string',
				'caption'   => 'CARGO'
			),
			array(
				'dataField' => 'CIUDAD_EMPRESA',
				'dataType'  => 'string',
				'caption'   => 'CIUDAD EMPRESA'
			),
			array(
				'dataField' => 'DIRECCION_EMPRESA',
				'dataType'  => 'string',
				'caption'   => 'DIRECCION EMPRESA'
			),
			array(
				'dataField' => 'TELEFONO_EMPRESA',
				'dataType'  => 'string',
				'caption'   => 'TELEFONO EMPRESA'
			),
			array(
				'dataField' => 'DIRECCION_RESIDENCIA',
				'dataType'  => 'string',
				'caption'   => 'DIRECCION RESIDENCIA'
			),
			array(
				'dataField' => 'CIUDAD_RESIDENCIA',
				'dataType'  => 'string',
				'caption'   => 'CIUDAD RESIDENCIA'
			),
			array(
				'dataField' => 'DEPARTAMENTO_RESIDENCIA',
				'dataType'  => 'string',
				'caption'   => 'DEPARTAMENTO RESIDENCIA'
			),
			array(
				'dataField' => 'TELEFONO_RESIDENCIA',
				'dataType'  => 'string',
				'caption'   => 'TELEFONO RESIDENCIA'
			),
			array(
				'dataField' => 'CELULAR',
				'dataType'  => 'string',
				'caption'   => 'CELULAR'
			),
			array(
				'dataField' => 'INGRESOS',
				'dataType'  => 'number',
				'caption'   => 'INGRESOS'
			),
			array(
				'dataField' => 'EGRESOS',
				'dataType'  => 'number',
				'caption'   => 'EGRESOS'
			),
			array(
				'dataField' => 'ACTIVOS',
				'dataType'  => 'number',
				'caption'   => 'ACTIVOS'
			),
			array(
				'dataField' => 'PASIVOS',
				'dataType'  => 'number',
				'caption'   => 'PASIVOS'
			),
			array(
				'dataField' => 'PATRIMONIO',
				'dataType'  => 'number',
				'caption'   => 'PATRIMONIO'
			),
			array(
				'dataField' => 'OTROS_INGRESOS',
				'dataType'  => 'number',
				'caption'   => 'OTROS INGRESOS'
			),
			array(
				'dataField' => 'CONCEPTO_OTROS_INGRESOS',
				'dataType'  => 'string',
				'caption'   => 'CONCEPTO OTROS INGRESOS'
			),
			array(
				'dataField' => 'TRANSACCIONES_MONEDA_EXTRANJERA',
				'dataType'  => 'string',
				'caption'   => 'TRANSACCIONES MONEDA EXTRANJERA'
			),
			array(
				'dataField' => 'TRANSACCIONES_MONEDA_EXTRANJERA_CUAL',
				'dataType'  => 'string',
				'caption'   => 'TRANSACCIONES MONEDA EXTRANJERA CUAL'
			),
			array(
				'dataField' => 'TRANSACCIONES_MONEDA_EXTRANJERA_OTRAS',
				'dataType'  => 'string',
				'caption'   => 'TRANSACCIONES MONEDA EXTRANJERA OTRAS'
			),
			array(
				'dataField' => 'PRODUCTO_TIPO_PRODUCTO_1',
				'dataType'  => 'string',
				'caption'   => 'TIPO DE PRODUCTO'
			),
			array(
				'dataField' => 'PRODUCTO_IDENTIFICACION_O_NUMERO_DE_PRODUCTO_1',
				'dataType'  => 'string',
				'caption'   => 'IDENTIFICACION O NUMERO DE PRODUCTO'
			),
			array(
				'dataField' => 'PRODUCTO_ENTIDAD_1',
				'dataType'  => 'string',
				'caption'   => 'ENTIDAD'
			),
			array(
				'dataField' => 'PRODUCTO_MONTO_1',
				'dataType'  => 'string',
				'caption'   => 'MONTO'
			),
			array(
				'dataField' => 'PRODUCTO_CIUDAD_1',
				'dataType'  => 'string',
				'caption'   => 'CIUDAD'
			),
			array(
				'dataField' => 'PRODUCTO_PAIS_1',
				'dataType'  => 'string',
				'caption'   => 'PAIS'
			),
			array(
				'dataField' => 'PRODUCTO_MONEDA_1',
				'dataType'  => 'string',
				'caption'   => 'MONEDA'
			),
			array(
				'dataField' => 'PRODUCTO_TIPO_PRODUCTO_2',
				'dataType'  => 'string',
				'caption'   => 'TIPO DE PRODUCTO'
			),
			array(
				'dataField' => 'PRODUCTO_IDENTIFICACION_O_NUMERO_DE_PRODUCTO_2',
				'dataType'  => 'string',
				'caption'   => 'IDENTIFICACION O NUMERO DE PRODUCTO'
			),
			array(
				'dataField' => 'PRODUCTO_ENTIDAD_2',
				'dataType'  => 'string',
				'caption'   => 'ENTIDAD'
			),
			array(
				'dataField' => 'PRODUCTO_MONTO_2',
				'dataType'  => 'string',
				'caption'   => 'MONTO'
			),
			array(
				'dataField' => 'PRODUCTO_CIUDAD_2',
				'dataType'  => 'string',
				'caption'   => 'CIUDAD'
			),
			array(
				'dataField' => 'PRODUCTO_PAIS_2',
				'dataType'  => 'string',
				'caption'   => 'PAIS'
			),
			array(
				'dataField' => 'PRODUCTO_MONEDA_2',
				'dataType'  => 'string',
				'caption'   => 'MONEDA'
			),
			array(
				'dataField' => 'DECLARACION_ORIGEN_FONDOS',
				'dataType'  => 'string',
				'caption'   => 'DECLARACION_ORIGEN_FONDOS'
			),
			array(
				'dataField' => 'TIPO_DE_ACTIVIDAD',
				'dataType'  => 'string',
				'caption'   => 'TIPO DE ACTIVIDAD'
			)
		];

		if(!isset($resultadoReporte['error'])){
			foreach ($resultadoReporte as $keyReporte => $dataReporte) {

				$anexo_Productos_cliente = $this->_clientes->getAllProductosClienteById($dataReporte['CLIENTE_ID']);
				foreach ($anexo_Productos_cliente as $keyProducto => $valueProducto) {

					$resultadoReporte[$keyReporte]['PRODUCTO_TIPO_PRODUCTO_' . ($keyProducto + 1)] = $valueProducto['tipo_producto'];
					$resultadoReporte[$keyReporte]['PRODUCTO_IDENTIFICACION_O_NUMERO_DE_PRODUCTO_' . ($keyProducto + 1)] = $valueProducto['identificacion_producto'];
					$resultadoReporte[$keyReporte]['PRODUCTO_ENTIDAD_' . ($keyProducto + 1)] = $valueProducto['entidad'];
					$resultadoReporte[$keyReporte]['PRODUCTO_MONTO_' . ($keyProducto + 1)] = $valueProducto['monto'];
					$resultadoReporte[$keyReporte]['PRODUCTO_CIUDAD_' . ($keyProducto + 1)] = $valueProducto['ciudad'];
					$resultadoReporte[$keyReporte]['PRODUCTO_PAIS_' . ($keyProducto + 1)] = $valueProducto['pais'];
					$resultadoReporte[$keyReporte]['PRODUCTO_MONEDA_' . ($keyProducto + 1)] = $valueProducto['moneda'];
				}				
			}

			$dataTable["data"] = $resultadoReporte;
		}

		return $dataTable;
	}

	public function reporteCapturasJuridico($fechasReporte = array()){

		$resultadoReporte = $this->_reportes->getDatosClientesCapturadosJuridicos($fechasReporte);
		$dataTable = array('data' => array());
		$dataTable["fileName"] = 'REPORTE CAPTURA CLIENTES JURIDICOS';
		$dataTable['columns'] = [
			array(
				'dataField' => 'FECHA_RADICACION',
				'dataType'  => 'date',
				'format'  	=> 'dd/MM/yyyy',
				'caption'   => 'FECHA RADICACION'
			),
			array(
				'dataField' => 'USUARIO_RADICACION',
				'dataType'  => 'string',
				'caption'   => 'USUARIO RADICACION'
			),
			array(
				'dataField' => 'FECHA_CAPTURA',
				'dataType'  => 'date',
				'format'  	=> 'dd/MM/yyyy',
				'caption'   => 'FECHA CAPTURA'
			),
			array(
				'dataField' => 'FECHA_ACTUALIZACION',
				'dataType'  => 'date',
				'format'  	=> 'dd/MM/yyyy',
				'caption'   => 'FECHA ACTUALIZACION'
			),
			array(
				'dataField' => 'FECHA_DILIGENCIAMIENTO',
				'dataType'  => 'date',
				'format'  	=> 'dd/MM/yyyy',
				'caption'   => 'FECHA DILIGENCIAMIENTO'
			),
			array(
				'dataField'     => 'CIUDAD_DILIGENCIAMIENTO',
				'dataType'      => 'string',
				'caption'       => 'CIUDAD DILIGENCIAMIENTO'
			),
			array(
				'dataField'     => 'SUCURSAL',
				'dataType'      => 'string',
				'caption'       => 'SUCURSAL'
			),
			array(
				'dataField' => 'COD_ASEG',
				'dataType'  => 'string',
				'caption'   => 'COD ASEG'
			),
			// array(
			// 	'dataField' => 'COD_DOCUMENTO',
			// 	'dataType'  => 'string',
			// 	'caption'   => 'CODIGO DOCUMENTO'
			// ),
			array(
				'dataField' => 'TIPO_DOCUMENTO',
				'dataType'  => 'string',
				'caption'   => 'TIPO DOCUMENTO'
			),
			array(
				'dataField' => 'NOMBRE_TOMADOR',
				'dataType'  => 'string',
				'caption'   => 'NOMBRE TOMADOR'
			),
			array(
				'dataField' => 'IDENTIFICACION_TOMADOR',
				'dataType'  => 'string',
				'caption'   => 'IDENTIFICACION TOMADOR'
			),
			array(
				'dataField' => 'REPRESENTANTE_LEGAL_PRIMER_APELLIDO',
				'dataType'  => 'string',
				'caption'   => 'REPRESENTANTE LEGAL PRIMER APELLIDO'
			),
			array(
				'dataField' => 'REPRESENTANTE_LEGAL_SEGUNDO_APELLIDO',
				'dataType'  => 'string',
				'caption'   => 'REPRESENTANTE LEGAL SEGUNDO APELLIDO'
			),
			array(
				'dataField' => 'REPRESENTANTE_LEGAL_NOMBRES',
				'dataType'  => 'string',
				'caption'   => 'REPRESENTANTE LEGAL NOMBRES'
			),
			array(
				'dataField' => 'REPRESENTANTE_TIPO_DOCUMENTO',
				'dataType'  => 'string',
				'caption'   => 'REPRESENTANTE TIPO DOCUMENTO'
			),
			array(
				'dataField' => 'REPRESENTANTE_DOCUMENTO',
				'dataType'  => 'string',
				'caption'   => 'REPRESENTANTE DOCUMENTO'
			),
			array(
				'dataField' => 'REPRESENTANTE_DIRECCION_RESIDENCIA',
				'dataType'  => 'string',
				'caption'   => 'REPRESENTANTE DIRECCION RESIDENCIA'
			),
			array(
				'dataField' => 'REPRESENTANTE_CIUDAD_RESIDENCIA',
				'dataType'  => 'string',
				'caption'   => 'REPRESENTANTE CIUDAD RESIDENCIA'
			),
			array(
				'dataField' => 'REPRESENTANTE_TELEFONO_RESIDENCIA',
				'dataType'  => 'string',
				'caption'   => 'REPRESENTANTE TELEFONO RESIDENCIA'
			),
			array(
				'dataField' => 'SUCURSAL_DIRECCION',
				'dataType'  => 'string',
				'caption'   => 'SUCURSAL DIRECCION'
			),
			array(
				'dataField' => 'SUCURSAL_CIUDAD',
				'dataType'  => 'string',
				'caption'   => 'SUCURSAL CIUDAD'
			),
			array(
				'dataField' => 'SUCURSAL_TELEFONO',
				'dataType'  => 'string',
				'caption'   => 'SUCURSAL TELEFONO'
			),
			array(
				'dataField' => 'SUCURSAL_FAX',
				'dataType'  => 'string',
				'caption'   => 'SUCURSAL FAX'
			),
			array(
				'dataField' => 'CELULAR',
				'dataType'  => 'string',
				'caption'   => 'CELULAR'
			),
			array(
				'dataField' => 'TELEFONO',
				'dataType'  => 'string',
				'caption'   => 'TELEFONO'
			),
			array(
				'dataField' => 'TIPO_EMPRESA',
				'dataType'  => 'string',
				'caption'   => 'TIPO EMPRESA'
			),
			array(
				'dataField' => 'CIIU_COD',
				'dataType'  => 'string',
				'caption'   => 'CIIU COD'
			),
			// array(
			// 	'dataField' => 'ACTIVIDAD_ECONOMICA',
			// 	'dataType'  => 'string',
			// 	'caption'   => 'ACTIVIDAD ECONOMICA'
			// ),
			array(
				'dataField' => 'ACCIONISTA_1_NOMBRES_APELLIDOS_COMPLETOS',
				'dataType'  => 'string',
				'caption'   => 'ACCIONISTA 1 NOMBRES APELLIDOS COMPLETOS'
			),
			array(
				'dataField' => 'ACCIONISTA_1_TIPO_ID',
				'dataType'  => 'string',
				'caption'   => 'ACCIONISTA 1 TIPO ID'
			),
			array(
				'dataField' => 'ACCIONISTA_1_NUMERO_ID',
				'dataType'  => 'string',
				'caption'   => 'ACCIONISTA 1 NUMERO ID'
			),
			array(
				'dataField' => 'ACCIONISTA_2_NOMBRES_APELLIDOS_COMPLETOS',
				'dataType'  => 'string',
				'caption'   => 'ACCIONISTA 2 NOMBRES APELLIDOS COMPLETOS'
			),
			array(
				'dataField' => 'ACCIONISTA_2_TIPO_ID',
				'dataType'  => 'string',
				'caption'   => 'ACCIONISTA 2 TIPO ID'
			),
			array(
				'dataField' => 'ACCIONISTA_2_NUMERO_ID',
				'dataType'  => 'string',
				'caption'   => 'ACCIONISTA 2 NUMERO ID'
			),
			array(
				'dataField' => 'ACCIONISTA_3_NOMBRES_APELLIDOS_COMPLETOS',
				'dataType'  => 'string',
				'caption'   => 'ACCIONISTA 3 NOMBRES APELLIDOS COMPLETOS'
			),
			array(
				'dataField' => 'ACCIONISTA_3_TIPO_ID',
				'dataType'  => 'string',
				'caption'   => 'ACCIONISTA 3 TIPO ID'
			),
			array(
				'dataField' => 'ACCIONISTA_3_NUMERO_ID',
				'dataType'  => 'string',
				'caption'   => 'ACCIONISTA 3 NUMERO ID'
			),
			array(
				'dataField' => 'ACCIONISTA_4_NOMBRES_APELLIDOS_COMPLETOS',
				'dataType'  => 'string',
				'caption'   => 'ACCIONISTA 4 NOMBRES APELLIDOS COMPLETOS'
			),
			array(
				'dataField' => 'ACCIONISTA_4_TIPO_ID',
				'dataType'  => 'string',
				'caption'   => 'ACCIONISTA 4 TIPO ID'
			),
			array(
				'dataField' => 'ACCIONISTA_4_NUMERO_ID',
				'dataType'  => 'string',
				'caption'   => 'ACCIONISTA 4 NUMERO ID'
			),
			array(
				'dataField' => 'ACCIONISTA_5_NOMBRES_APELLIDOS_COMPLETOS',
				'dataType'  => 'string',
				'caption'   => 'ACCIONISTA 5 NOMBRES APELLIDOS COMPLETOS'
			),
			array(
				'dataField' => 'ACCIONISTA_5_TIPO_ID',
				'dataType'  => 'string',
				'caption'   => 'ACCIONISTA 5 TIPO ID'
			),
			array(
				'dataField' => 'ACCIONISTA_5_NUMERO_ID',
				'dataType'  => 'string',
				'caption'   => 'ACCIONISTA 5 NUMERO ID'
			),
			array(
				'dataField' => 'INGRESOS',
				'dataType'  => 'number',
				'caption'   => 'INGRESOS'
			),
			array(
				'dataField' => 'EGRESOS',
				'dataType'  => 'number',
				'caption'   => 'EGRESOS'
			),
			array(
				'dataField' => 'ACTIVOS',
				'dataType'  => 'number',
				'caption'   => 'ACTIVOS'
			),
			array(
				'dataField' => 'PASIVOS',
				'dataType'  => 'number',
				'caption'   => 'PASIVOS'
			),
			array(
				'dataField' => 'PATRIMONIO',
				'dataType'  => 'number',
				'caption'   => 'PATRIMONIO'
			),
			array(
				'dataField' => 'OTROS_INGRESOS',
				'dataType'  => 'number',
				'caption'   => 'OTROS INGRESOS'
			),
			array(
				'dataField' => 'CONCEPTO_OTROS_INGRESOS',
				'dataType'  => 'string',
				'caption'   => 'CONCEPTO OTROS INGRESOS'
			),
			array(
				'dataField' => 'TRANSACCIONES_MONEDA_EXTRANJERA',
				'dataType'  => 'string',
				'caption'   => 'TRANSACCIONES MONEDA EXTRANJERA'
			),
			array(
				'dataField' => 'TRANSACCIONES_MONEDA_EXTRANJERA_CUAL',
				'dataType'  => 'string',
				'caption'   => 'TRANSACCIONES MONEDA EXTRANJERA CUAL'
			),
			array(
				'dataField' => 'TRANSACCIONES_MONEDA_EXTRANJERA_OTRAS',
				'dataType'  => 'string',
				'caption'   => 'TRANSACCIONES MONEDA EXTRANJERA OTRAS'
			),
			array(
				'dataField' => 'PRODUCTO_TIPO_PRODUCTO_1',
				'dataType'  => 'string',
				'caption'   => 'PRODUCTO TIPO PRODUCTO 1'
			),
			array(
				'dataField' => 'PRODUCTO_IDENTIFICACION_O_NUMERO_DE_PRODUCTO_1',
				'dataType'  => 'string',
				'caption'   => 'PRODUCTO IDENTIFICACION O NUMERO DE PRODUCTO 1'
			),
			array(
				'dataField' => 'PRODUCTO_ENTIDAD_1',
				'dataType'  => 'string',
				'caption'   => 'PRODUCTO ENTIDAD 1'
			),
			array(
				'dataField' => 'PRODUCTO_MONTO_1',
				'dataType'  => 'string',
				'caption'   => 'PRODUCTO MONTO 1'
			),
			array(
				'dataField' => 'PRODUCTO_CIUDAD_1',
				'dataType'  => 'string',
				'caption'   => 'PRODUCTO CIUDAD 1'
			),
			array(
				'dataField' => 'PRODUCTO_PAIS_1',
				'dataType'  => 'string',
				'caption'   => 'PRODUCTO PAIS 1'
			),
			array(
				'dataField' => 'PRODUCTO_MONEDA_1',
				'dataType'  => 'string',
				'caption'   => 'PRODUCTO MONEDA 1'
			),
			array(
				'dataField' => 'PRODUCTO_TIPO_PRODUCTO_2',
				'dataType'  => 'string',
				'caption'   => 'PRODUCTO TIPO PRODUCTO 2'
			),
			array(
				'dataField' => 'PRODUCTO_IDENTIFICACION_O_NUMERO_DE_PRODUCTO_2',
				'dataType'  => 'string',
				'caption'   => 'PRODUCTO IDENTIFICACION O NUMERO DE PRODUCTO 2'
			),
			array(
				'dataField' => 'PRODUCTO_ENTIDAD_2',
				'dataType'  => 'string',
				'caption'   => 'PRODUCTO ENTIDAD 2'
			),
			array(
				'dataField' => 'PRODUCTO_MONTO_2',
				'dataType'  => 'string',
				'caption'   => 'PRODUCTO MONTO 2'
			),
			array(
				'dataField' => 'PRODUCTO_CIUDAD_2',
				'dataType'  => 'string',
				'caption'   => 'PRODUCTO CIUDAD 2'
			),
			array(
				'dataField' => 'PRODUCTO_PAIS_2',
				'dataType'  => 'string',
				'caption'   => 'PRODUCTO PAIS 2'
			),
			array(
				'dataField' => 'PRODUCTO_MONEDA_2',
				'dataType'  => 'string',
				'caption'   => 'PRODUCTO MONEDA 2'
			),
			array(
				'dataField' => 'DECLARACION_ORIGEN_FONDOS',
				'dataType'  => 'string',
				'caption'   => 'DECLARACION ORIGEN FONDOS'
			)
		];

		if(!isset($resultadoReporte['error'])){
			foreach ($resultadoReporte as $keyReporte => $dataReporte) {

				$anexo_Accionistas_cliente = $this->_clientes->getAccionistasClienteById($dataReporte['CLIENTE_ID']);
				foreach ($anexo_Accionistas_cliente as $keyAccionista => $valueAccionista) {

					$resultadoReporte[$keyReporte]['ACCIONISTA_' . ($keyAccionista + 1) . '_TIPO_ID'] = $valueAccionista['accionista_tipo_documento'];
					$resultadoReporte[$keyReporte]['ACCIONISTA_' . ($keyAccionista + 1) . '_NUMERO_ID'] = $valueAccionista['accionista_documento'];
					$resultadoReporte[$keyReporte]['ACCIONISTA_' . ($keyAccionista + 1) . '_NOMBRES_APELLIDOS_COMPLETOS'] = $valueAccionista['accionista_nombres_completos'];
					$resultadoReporte[$keyReporte]['ACCIONISTA_' . ($keyAccionista + 1) . '_PARTICIPACION'] = $valueAccionista['accionista_participacion'];
					$resultadoReporte[$keyReporte]['ACCIONISTA_' . ($keyAccionista + 1) . '_PERSONA_JURIDICA_QUE_COTIZA_EN_BOLSA'] = $valueAccionista['accionista_cotiza_bolsa'];
					$resultadoReporte[$keyReporte]['ACCIONISTA_' . ($keyAccionista + 1) . '_PERSONA_PUBLICAMENTE_EXPUESTA_O_VINCULADO_CON_UNA_DE_ELLAS'] = $valueAccionista['accionista_persona_publica'];
					$resultadoReporte[$keyReporte]['ACCIONISTA_' . ($keyAccionista + 1) . '_SUJETO_DE_TRIBUTACION_EN_OTRO_PAIS'] = ($valueAccionista['accionista_obligaciones_otro_pais'] == 1) ? $valueAccionista['accionista_obligaciones_otro_pais_desc'] : $valueAccionista['accionista_obligaciones_otro_pais'] ;
				}

				$anexo_SubAccionistas_cliente = $this->_clientes->getSubAccionistasClienteById($dataReporte['CLIENTE_ID']);
				foreach ($anexo_SubAccionistas_cliente as $keySubAccionista => $valueSubAccionista) {

					$resultadoReporte[$keyReporte]['SUB_ACCIONISTA_TIPO_ID_' . ($keySubAccionista+1)] = $valueSubAccionista['sub_accionista_tipo_documento'];
					$resultadoReporte[$keyReporte]['SUB_ACCIONISTA_NUMERO_ID_' . ($keySubAccionista+1)] = $valueSubAccionista['sub_accionista_numero_id'];
					$resultadoReporte[$keyReporte]['SUB_ACCIONISTA_RAZON_SOCIAL_NOMBRES_APELLIDOS_' . ($keySubAccionista+1)] = $valueSubAccionista['sub_accionista_razon_social'];
					$resultadoReporte[$keyReporte]['SUB_ACCIONISTA_PARTICIPACION_' . ($keySubAccionista+1)] = $valueSubAccionista['sub_accionista_participacion'];
					$resultadoReporte[$keyReporte]['SUB_ACCIONISTA_NOMBRE_RAZON_SOCIAL_DE_LA_SOCIEDAD_A_LA_QUE_ES_ACCIONISTA_' . ($keySubAccionista+1)] = $valueSubAccionista['sub_accionista_nombre_sociedad_accionista'];
					$resultadoReporte[$keyReporte]['SUB_ACCIONISTA_NIT_' . ($keySubAccionista+1)] = $valueSubAccionista['sub_accionista_documento'];
				}

				$anexo_Productos_cliente = $this->_clientes->getAllProductosClienteById($dataReporte['CLIENTE_ID']);
				foreach ($anexo_Productos_cliente as $keyProducto => $valueProducto) {

					$resultadoReporte[$keyReporte]['PRODUCTO_TIPO_PRODUCTO_' . ($keyProducto + 1)] = $valueProducto['tipo_producto'];
					$resultadoReporte[$keyReporte]['PRODUCTO_IDENTIFICACION_O_NUMERO_DE_PRODUCTO_' . ($keyProducto + 1)] = $valueProducto['identificacion_producto'];
					$resultadoReporte[$keyReporte]['PRODUCTO_ENTIDAD_' . ($keyProducto + 1)] = $valueProducto['entidad'];
					$resultadoReporte[$keyReporte]['PRODUCTO_MONTO_' . ($keyProducto + 1)] = $valueProducto['monto'];
					$resultadoReporte[$keyReporte]['PRODUCTO_CIUDAD_' . ($keyProducto + 1)] = $valueProducto['ciudad'];
					$resultadoReporte[$keyReporte]['PRODUCTO_PAIS_' . ($keyProducto + 1)] = $valueProducto['pais'];
					$resultadoReporte[$keyReporte]['PRODUCTO_MONEDA_' . ($keyProducto + 1)] = $valueProducto['moneda'];
				}				
			}

			$dataTable["data"] = $resultadoReporte;
		}

		return $dataTable;
	}

	public function reporteActualizacionDocumentos($fechasReporte = array()){
		$resultadoReporte = $this->_reportes->getDatosFechasDocumentos($fechasReporte);
				
		$dataTable = array('data' => array());
		$dataTable["fileName"] = 'REPORTE ACTUALIZACION DOCUMENTOS';
		$dataTable['columns'] = [
			array(
				'dataField' => 'FECHA_RADICACION',
				'dataType'  => 'date',
				'format'  	=> 'dd/MM/yyyy',
				'caption'   => 'FECHA RADICACION'
			),
			array(
				'dataField' => 'USUARIO_RADICADOR',
				'dataType'  => 'string',
				'caption'   => 'USUARIO RADICADOR'
			),
			array(
				'dataField' => 'TIPO_ID_CLIENTE',
				'dataType'  => 'string',
				'caption'   => 'TIPO ID CLIENTE'
			),
			array(
				'dataField' => 'NOMBRE_CLIENTE',
				'dataType'  => 'string',
				'caption'   => 'NOMBRE CLIENTE'
			),
			array(
				'dataField' => 'FECHA_ULTIMA_ACTUALIZACION_RUT',
				'dataType'  => 'date',
				'format'  	=> 'MM/yyyy',
				'caption'   => 'FECHA ULTIMA ACTUALIZACION RUT'
			),
			array(
				'dataField' => 'FECHA_ULTIMA_ACTUALIZACION_CCO',
				'dataType'  => 'date',
				'format'  	=> 'MM/yyyy',
				'caption'   => 'FECHA ULTIMA ACTUALIZACION CCO'
			),
			array(
				'dataField' => 'FECHA_ULTIMA_ACTUALIZACION_DDC',
				'dataType'  => 'date',
				'format'  	=> 'MM/yyyy',
				'caption'   => 'FECHA ULTIMA ACTUALIZACION DDC'
			),
			array(
				'dataField' => 'FECHA_ULTIMA_ACTUALIZACION_ACC',
				'dataType'  => 'date',
				'format'  	=> 'MM/yyyy',
				'caption'   => 'FECHA ULTIMA ACTUALIZACION ACC'
			),
			array(
				'dataField' => 'FECHA_ULTIMA_ACTUALIZACION_EFC',
				'dataType'  => 'date',
				'format'  	=> 'MM/yyyy',
				'caption'   => 'FECHA ULTIMA ACTUALIZACION EFC'
			),
			array(
				'dataField' => 'FECHA_ULTIMA_ACTUALIZACION_EFI',
				'dataType'  => 'date',
				'format'  	=> 'MM/yyyy',
				'caption'   => 'FECHA ULTIMA ACTUALIZACION EFI'
			),
			array(
				'dataField' => 'FECHA_ULTIMA_ACTUALIZACION_NEF',
				'dataType'  => 'date',
				'format'  	=> 'MM/yyyy',
				'caption'   => 'FECHA ULTIMA ACTUALIZACION NEF'
			),
			array(
				'dataField' => 'FECHA_ULTIMA_ACTUALIZACION_RTA',
				'dataType'  => 'date',
				'format'  	=> 'yyyy',
				'caption'   => 'FECHA ULTIMA ACTUALIZACION RTA'
			),
			array(
				'dataField' => 'FECHA_ULTIMA_ACTUALIZACION_RET',
				'dataType'  => 'date',
				'format'  	=> 'yyyy',
				'caption'   => 'FECHA ULTIMA ACTUALIZACION RET'
			),
		];

		if(!isset($resultadoReporte['error'])){

			$reporteClientes = array();
			$reporteClientesFinal = array();

			foreach($resultadoReporte as $resultado){
				$reporteClientes[$resultado['CLIENTE_ID']]['CLIENTE_ID'] = $resultado['CLIENTE_ID'];
				$reporteClientes[$resultado['CLIENTE_ID']]['FECHA_RADICACION'] = $resultado['FECHA_RADICACION'];
				$reporteClientes[$resultado['CLIENTE_ID']]['USUARIO_RADICADOR'] = $resultado['USUARIO_RADICADOR'];
				$reporteClientes[$resultado['CLIENTE_ID']]['TIPO_ID_CLIENTE'] = $resultado['TIPO_ID_CLIENTE'];
				$reporteClientes[$resultado['CLIENTE_ID']]['NOMBRE_CLIENTE'] = $resultado['NOMBRE_CLIENTE'];
				$reporteClientes[$resultado['CLIENTE_ID']]['FECHA_ULTIMA_ACTUALIZACION_' . $resultado['TIPO_DOC']] = $resultado['FECHA_EMISION'];
			}
			
			foreach($reporteClientes as $reporte){
				array_push($reporteClientesFinal, $reporte);
			}

			$dataTable["data"] = $reporteClientesFinal;
		}

		return $dataTable;
	}

}