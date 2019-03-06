<?php 
class migracionController extends Controller
{
	private $_clientes,$_radicacion;

    public function __construct() {

    	if(Session::get('Mundial_authenticate')){
    		
			if(in_array(Session::getLevel(Session::get("Mundial_user_rol")),[Session::getLevel('Gerente')])){

		    	try {

		    		if(file_exists(ROOT . 'Classes/PHPExcel/IOFactory.php')){
		    			require_once ROOT . 'Classes/PHPExcel/IOFactory.php';
		    		}else{
		    			throw new Exception('EL ARCHIVO IOFactory.php NO EXISTE');
		    		}

			        parent::__construct();

			        $this->_crud = $this->loadModel("crud");
			        $this->_model = $this->loadModel("migracion"); 
			        $this->_radicacion = $this->loadModel("radicacion"); 
			        $this->_clientes = $this->loadModel("clientes"); 
			        $this->_files = $this->loadModel("files");

		    	} catch (Exception $e) {

		    		die("ERROR!!! ".$e->getMessage());
		    	}
			}else{
				$this->redireccionar('error', 'access', ['5656']);
			}
		}else{
			$this->redireccionar('error', 'access', ['5656']);
		}
    }

    public function index(){
        // carga las vistas y archivos
        $this->_view->setJs(array('migracion'));
        $this->_view->renderizar('index', 'migracion');
    }

    public function loadMigrateFileXLSX(){
    	if(Server::RequestMethod("POST")){

    		$data = json_decode(json_encode(Server::post()),true); // Variable de almacenamiento de los valores que llegan por metodo POST
    		$return = array();

    		try {

    			$loadFile = Helpers::LoadFile($_FILES["archivo_migracion"],['XLSX','XLS']);

    			if(!isset($loadFile['error'])){

    				$regexInfoFile = preg_match("/(migracion).+\-+(\d{8})/",strtolower($_FILES["archivo_migracion"]["name"]),$InfoFile);

    				if($regexInfoFile){

    					$dateFile = substr($InfoFile[2],0,2)."-".substr($InfoFile[2],2,2)."-".substr($InfoFile[2],4);

    					if($this->validateDate($dateFile,'d-m-Y')){

    						if(!empty($data) && isset($data["tipo_migracion"])){
							        						
        						$FileMigracion = $loadFile['success']['ruta_temp'];
								$FileMigracionType = PHPExcel_IOFactory::identify($FileMigracion);
					            $objReader = PHPExcel_IOFactory::createReader($FileMigracionType);
					            $objPHPExcel = $objReader->load($FileMigracion);

					            $ClientesMigracion = array();

        						switch ($data["tipo_migracion"]) {

        							case 'radicacion':

        								$ULT_FECHA_MIGRACION = $this->_model->findOldDateMigrate('RADICACION');

			        					if(!empty($ULT_FECHA_MIGRACION["ULT_FECHA_MIGRACION"])){

			        						if(date('Y-m-d',strtotime($dateFile)) < $ULT_FECHA_MIGRACION["ULT_FECHA_MIGRACION"]){

			        							throw new Exception('LA FECHA DE MIGRACION ES MAS ANTIGUA QUE LA ULTIMA FECHA DE MIGRACION '.$ULT_FECHA_MIGRACION["ULT_FECHA_MIGRACION"]);
			        						}
			        					}

					        			$sheet = $objPHPExcel->getSheet(0); 
								        $highestRow = $sheet->getHighestRow(); 
								        $highestColumn = $sheet->getHighestColumn();

								        // Variable global para almacenamiento de errores en el sistema
					        			$resultadoMigracionRadicacion = array(
					        				'radicaciones_total' => $highestRow,
					        				'save_cliente_ok' => 0,
					        				'save_cliente_error' => 0,
					        				'radicaciones_ok' => 0, 
					        				'radicaciones_error' => 0
					        			);

					        			$saveInitMigracion = $this->_crud->Save('zr_migraciones',array(
					        				'TIPO_MIGRACION' => 'RADICACION',
					        				'FECHA_MIGRACION_ARCHIVO' => date('Y-m-d',strtotime($dateFile)),
					        				'NOMBRE_ARCHIVO' => $_FILES["archivo_migracion"]["name"],
					        				'TOTAL_REGISTROS' => $resultadoMigracionRadicacion["radicaciones_total"],
					        				'TOTAL_COMPLETADOS' => $resultadoMigracionRadicacion["radicaciones_ok"],
					        				'TOTAL_ERRORES' => $resultadoMigracionRadicacion["radicaciones_error"],
					        				'ESTADO_PROCESO' => 'INICIA MIGRACION RADICACIONES'
					        			));


					        			//Interpreta un loop para recorrer el total de columnas con el total de filas
								        for ($row = 2; $row <= $highestRow; $row++){ 

								        	//Lee La fila y columnas del archivo empezando de la columna A , Fila 2
								        	$DataFileXLSX = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, TRUE, FALSE);
								        	$TempRowData = $DataFileXLSX[0];

								        	try {

							            		if(!is_null($TempRowData[1])){

							            			$documento = trim($TempRowData[1]);

							            			$cliente_exist = $this->_files->VerifyClientExist($documento);

							            			if(!isset($cliente_exist['error'])){

														if($cliente_exist["CARGADO_ROBOT"] >= 1){
															
															$getClient = $this->_files->getClientRobot($TempRowData[1]);

															if(!isset($getClient['error']) && !empty($getClient["NUMERO_IDENT_CLIENTE"])){

																if($cliente_exist["CARGADO_SISTEMA"] == 0){

																	$resultadoSaveCliente = $this->_crud->Save('clientes',array(
																		"tipo_documento" => $getClient["TIPO_IDENT_CLIENTE"], 
																		"documento" => $getClient["NUMERO_IDENT_CLIENTE"]
																	));

																	if(isset($resultadoSaveCliente['error'])){

																		$resultadoMigracionRadicacion["save_cliente_error"]++;
																		throw new Exception('NO SE PUEDE AL GUARDAR CLIENTE CON IDENTIFICACION ' . $getClient["NUMERO_IDENT_CLIENTE"] .' LINEA : ' . $row . ' POR :' . $resultadoSaveCliente['error']);
																	}else{
																		$resultadoMigracionRadicacion["save_cliente_ok"]++;
																	}
																}
															}else{
																throw new Exception("No su puede consultar el dentro de la tabla archivo_organizado por: " . $getClient['error']);
															}


															$cliente_id = $this->_clientes->getClienteIDByDocument($documento);

															$exist_fecha_diligenciamiento = $this->_radicacion->getFechaDiligenciamientoSarlaftByClienteId($cliente_id["id"],date('Y-m-d',strtotime($TempRowData[2])));

															$NRadicado = $this->_radicacion->NRadicado();
															$resultadoSaveRadicado = $this->_crud->Save('zr_radicacion',array(
																'funcionario_id' => $TempRowData[0],
																'cliente_id' => $cliente_id["id"],
																'consecutivo' => $NRadicado["CONSECUTIVO_RADICADO"],
																'fecha_diligenciamiento' => $TempRowData[2],
																'numero_planilla' => $TempRowData[3],
																'tipo_cliente' => ucwords($TempRowData[4]),
																'tipo_medio' => ucwords($TempRowData[5]),
																'devuelto' => ucwords($TempRowData[6]),
																'separado' => ucwords($TempRowData[7]),
																'cantidad_separada' => $TempRowData[8],
																'digitalizado' => ($TempRowData[5] == "DIGITAL") ? 'No' : 'Si',
																'formulario' => ucwords($TempRowData[9]),
																'cantidad_documentos' => $TempRowData[10],	
																'medio_recepcion' => ucwords($TempRowData[13]),
																'radicacion_proceso' => $TempRowData[14],
																'formulario_repetido' => ($exist_fecha_diligenciamiento) ? 1 : 0,
																'correo_radicacion' => strtolower($TempRowData[15]),
																'linea_negocio_id' => $TempRowData[16],
																'radicacion_observacion' => $TempRowData[17],
																'formulario_sarlaft' => $TempRowData[18],
																'created' => $TempRowData[19]
															));

															if(isset($resultadoSaveRadicado['error'])){

																$resultadoMigracionRadicacion['radicaciones_error']++;
																throw new Exception('EL CLIENTE ' . $documento . ' DE LA LINEA ' . $row . ' LA RADICACION NO SE ESTA GUARDANDO CORRECTAMENTE POR : ' . $resultadoSaveRadicado['error']);
															}else{

																$resultadoMigracionRadicacion['radicaciones_ok']++;
															}
														}else{

															$resultadoMigracionRadicacion["save_cliente_error"]++;
															throw new Exception('EL CLIENTE ' . $documento . ' DE LA LINEA ' . $row . ' NO CUENTA CON NINGUN ARCHIVO REGISTRADO EN EL SISTEMA');
														}
							            			}else{
							            				$resultadoMigracionRadicacion["save_cliente_error"]++;
							            				throw new Exception("No se pudo verificar el cliente por: " . $cliente_exist['error']);
							            			}
							            		}else{

							            			$resultadoMigracionRadicacion["save_cliente_error"]++;
							            			throw new Exception('EL CLIENTE DE LA LINEA: ' . $row . 'ESTA VACIO');
							            		}
								        	} catch (Exception $excep_radicacion) {

								        		$saveErrorMigracion = $this->_crud->Save('zr_migraciones',array(
								    				'TIPO_MIGRACION' => 'RADICACION',
								    				'FECHA_MIGRACION_ARCHIVO' => date('Y-m-d',strtotime($dateFile)),
								    				'NOMBRE_ARCHIVO' => $_FILES["archivo_migracion"]["name"],
								    				'TOTAL_REGISTROS' => $resultadoMigracionRadicacion["radicaciones_total"],
								    				'TOTAL_COMPLETADOS' => $resultadoMigracionRadicacion["radicaciones_ok"],
								    				'TOTAL_ERRORES' => $resultadoMigracionRadicacion["radicaciones_error"],
								    				'ESTADO_PROCESO' => 'ERROR EXCEPCION EN EL PROCESO RADICACION DESCRIPCION : ' . $excep_radicacion->getMessage()
								    			));
								        	}
								        }

								        if($resultadoMigracionRadicacion["save_cliente_ok"] != 0){
								        	array_push($return,array(
								        		'type' => "STATES_OK", 
					                            'title' => 'MIGRACION COMPLETADOS',
					                            'message' => 'No CLIENTES NUEVOS REGISTRADOS ' . $resultadoMigracionRadicacion["save_cliente_ok"] . ' de ' . $resultadoMigracionRadicacion["radicaciones_total"]
					                        ));
								        }

								        if($resultadoMigracionRadicacion["save_cliente_error"] != 0){
								        	array_push($return,array(
								        		'type' => "STATES_ERROR", 
					                            'title' => 'MIGRACION ERRORES',
					                            'message' => 'No CLIENTES NO REGISTRADOS ' . $resultadoMigracionRadicacion["save_cliente_error"] . ' de ' . $resultadoMigracionRadicacion["radicaciones_total"]
					                        ));
								        }

								        if($resultadoMigracionRadicacion["radicaciones_ok"] != 0){
								        	array_push($return,array(
								        		'type' => "STATES_OK", 
					                            'title' => 'MIGRACION COMPLETADOS',
					                            'message' => 'No RADICACIONES REGISTRADAS ' . $resultadoMigracionRadicacion["radicaciones_ok"] . ' de ' . $resultadoMigracionRadicacion["radicaciones_total"]
					                        ));
								        }

								        if($resultadoMigracionRadicacion["radicaciones_error"] != 0){
								        	array_push($return,array(
								        		'type' => "STATES_ERROR", 
					                            'title' => 'MIGRACION ERRORES',
					                            'message' => 'No RADICACIONES NO REGISTRADAS ' . $resultadoMigracionRadicacion["radicaciones_error"] . ' de ' . $resultadoMigracionRadicacion["radicaciones_total"]
					                        ));
								        }

								        $saveInitMigracion = $this->_crud->Save('zr_migraciones',array(
					        				'TIPO_MIGRACION' => 'RADICACION',
					        				'FECHA_MIGRACION_ARCHIVO' => date('Y-m-d',strtotime($dateFile)),
					        				'NOMBRE_ARCHIVO' => $_FILES["archivo_migracion"]["name"],
					        				'FECHA_MIGRACION_SISTEMA' => date('Y-m-d H:i:s'),
					        				'TOTAL_REGISTROS' => $resultadoMigracionRadicacion["radicaciones_total"],
					        				'TOTAL_COMPLETADOS' => $resultadoMigracionRadicacion["radicaciones_ok"],
					        				'TOTAL_ERRORES' => $resultadoMigracionRadicacion["radicaciones_error"],
					        				'ESTADO_PROCESO' => 'FINALIZACION MIGRACION RADICACIONES'
					        			));

								        if(isset($saveInitMigracion['error'])){

								        	throw new Exception('NO SE GUARDO EL REGISTRO DE FINALIZACION DEL ARCHIVO POR : ' . $saveInitMigracion['error']);
								        }else{

								        	echo json_encode($return);
								        }
								        
								        if(!unlink($loadFile['success']['ruta_temp'])){

								        	throw new Exception('EL ARCHIVO NO SE ELIMINO DE SU RUTA TEMPORAL');
								        }

					        			break;

        							case 'captura':

        								$ULT_FECHA_MIGRACION = $this->_model->findOldDateMigrate('CAPTURA');

			        					if(!empty($ULT_FECHA_MIGRACION["ULT_FECHA_MIGRACION"])){

			        						if(date('Y-m-d',strtotime($dateFile)) < $ULT_FECHA_MIGRACION["ULT_FECHA_MIGRACION"]){

			        							throw new Exception('LA FECHA DE MIGRACION ES MAS ANTIGUA QUE LA ULTIMA FECHA DE MIGRACION '.$ULT_FECHA_MIGRACION["ULT_FECHA_MIGRACION"]);
			        						}
			        					}

        								if(isset($data["opc_migracion_captura"])){

        									$namesFile = $objPHPExcel->getSheetNames();

        									if(in_array($data["opc_migracion_captura"],$namesFile)){

							        			$sheet = $objPHPExcel->getSheetByName($data["opc_migracion_captura"]); 
										        $highestRow = $sheet->getHighestRow(); 
										        $highestColumn = $sheet->getHighestColumn();

									        	// Variable global para almacenamiento de errores en el sistema
							        			$resultadoMigracionCaptura = array(
							        				'capturas_total' => $highestRow,
							        				'capturas_ok' => 0, 
							        				'capturas_error' => 0
							        			);

							        			// Guarda el inicio de proceso de migracion radicacion
							        			$saveInitMigracionJuridico = $this->_crud->Save('zr_migraciones',array(
							        				'TIPO_MIGRACION' => 'CAPTURA',
							        				'FECHA_MIGRACION_ARCHIVO' => date('Y-m-d',strtotime($dateFile)),
							        				'NOMBRE_ARCHIVO' => $_FILES["archivo_migracion"]["name"],
							        				'FECHA_MIGRACION_SISTEMA' => date('Y-m-d H:i:s'),
							        				'TOTAL_REGISTROS' => $resultadoMigracionCaptura["capturas_total"],
							        				'TOTAL_COMPLETADOS' => $resultadoMigracionCaptura["capturas_ok"],
							        				'TOTAL_ERRORES' => $resultadoMigracionCaptura["capturas_error"],
							        				'ESTADO_PROCESO' => 'INICIA MIGRACION PERSONA '.$data["opc_migracion_captura"]
							        			));

							        			if ($data["opc_migracion_captura"] == "CLIENTES_NATURALES"){

							        				for ($row = 2; $row <= $highestRow; $row++){

											        	//Lee La fila y columnas del archivo empezando de la columna A , Fila 2
											        	$DataFileXLSX = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, TRUE, FALSE);
											        	$TempRowData = $DataFileXLSX[0];

											        	try {

								            				if(!is_null($TempRowData[1])){

								            					$cliente_id = $this->_clientes->getClienteIDByDocument(trim($TempRowData[1]));

									            				if(!isset($cliente_id['error']) && isset($cliente_id["id"])){

									            					$InfoClienteSarlaft = $this->_clientes->getInfoClienteNaturalByClienteId($cliente_id["id"]);
																	$dataCapturaFile = array(
																		'cliente' => $cliente_id["id"],
																		'ciudad_diligenciamiento' => $TempRowData[3],
																		'sucursal' => $TempRowData[4],
																		'tipo_solicitud' => $TempRowData[5],
																		'clase_vinculacion' => $TempRowData[6],
																		'clase_vinculacion_otro' => $TempRowData[7],
																		'relacion_tom_asegurado' => $TempRowData[8],
																		'relacion_tom_asegurado_otra' => $TempRowData[9],
																		'relacion_tom_beneficiario' => $TempRowData[10],
																		'relacion_tom_beneficiario_otra' => $TempRowData[11],
																		'relacion_aseg_beneficiario' => $TempRowData[12],
																		'relacion_aseg_beneficiario_otra' => $TempRowData[13],
																		'primer_apellido' => $TempRowData[14],
																		'segundo_apellido' => $TempRowData[15],
																		'primer_nombre' => $TempRowData[16],
																		'segundo_nombre' => $TempRowData[17],
																		'lugar_expedicion_documento' => $TempRowData[18],
																		'sexo' => $TempRowData[19],
																		'fecha_expedicion_documento' => $TempRowData[20],
																		'fecha_nacimiento' => $TempRowData[21],
																		'lugar_nacimiento' => $TempRowData[22],
																		'nacionalidad_1' => $TempRowData[23],
																		'nacionalidad_2' => $TempRowData[24],
																		'estado_civil' => $TempRowData[25],
																		'direccion_residencia' => $TempRowData[26],
																		'departamento_residencia' => $TempRowData[27],
																		'ciudad_residencia' => $TempRowData[28],
																		'telefono' => $TempRowData[29],
																		'celular' => $TempRowData[30],
																		'correo_electronico' => $TempRowData[31],
																		'actividad_eco_principal' => $TempRowData[32],
																		'actividad_eco_principal_otra' => $TempRowData[33],
																		'trabaja_actualmente' => $TempRowData[34],
																		'sector' => $TempRowData[35],
																		'ciiu_cod' => $TempRowData[36],
																		'tipo_actividad' => $TempRowData[37],
																		'tipo_actividad_otro' => $TempRowData[38],
																		'ocupacion' => $TempRowData[39],
																		'cargo' => $TempRowData[40],
																		'empresa_donde_trabaja' => $TempRowData[41],
																		'departamento_empresa' => $TempRowData[42],
																		'ciudad_empresa' => $TempRowData[43],
																		'direccion_empresa' => $TempRowData[44],
																		'telefono_empresa' => $TempRowData[45],
																		'actividad_secundaria' => $TempRowData[46],
																		'ciiu_secundario' => $TempRowData[47],
																		'ingresos' => $TempRowData[48],
																		'egresos' => $TempRowData[49],
																		'activos' => $TempRowData[50],
																		'pasivos' => $TempRowData[51],
																		'patrimonio' => $TempRowData[52],
																		'otros_ingresos' => $TempRowData[53],
																		'desc_otros_ingresos' => $TempRowData[54],
																		'declaracion_origen_fondos' => $TempRowData[55],
																		'persona_publica' => $TempRowData[56],
																		'vinculo_persona_publica' => $TempRowData[57],
																		'productos_publicos' => $TempRowData[58],
																		'obligaciones_tributarias_otro_pais' => $TempRowData[59],
																		'desc_obligaciones_tributarias_otro_pais' => $TempRowData[60],
																		'anexo_preguntas_ppes' => $TempRowData[61],
																		'operaciones_moneda_extranjera' => $TempRowData[62],
																		'tipo_operaciones_moneda_extranjera' => $TempRowData[63],
																		'desc_operacion_mon_extr' => $TempRowData[64],
																		'cuentas_moneda_exterior' => $TempRowData[65],
																		'productos_exterior' => $TempRowData[66],
																		'reclamaciones' => $TempRowData[67],
																		'reclamacion_anio' => $TempRowData[68],
																		'reclamacion_ramo' => $TempRowData[69],
																		'reclamacion_compania' => $TempRowData[70],
																		'reclamacion_valor' => $TempRowData[71],
																		'reclamacion_resultado' => $TempRowData[72],
																		'reclamacion_anio_2' => $TempRowData[73],
																		'reclamacion_ramo_2' => $TempRowData[74],
																		'reclamacion_compania_2' => $TempRowData[75],
																		'reclamacion_valor_2' => $TempRowData[76],
																		'reclamacion_resultado_2' => $TempRowData[77],
																		'huella' => $TempRowData[78],
																		'firma' => $TempRowData[79],
																		'entrevista' => $TempRowData[80],
																		'entrevista_fecha' => $TempRowData[81],
																		'entrevista_observaciones' => $TempRowData[82],
																		'entrevista_resultado' => $TempRowData[83]
																	);

																	if(!isset($InfoClienteSarlaft['error'])){

																		if(!$InfoClienteSarlaft){

																			$resultadoSaveCliente = $this->_crud->Save('cliente_sarlaft_natural',$dataCapturaFile);

																			if(isset($resultadoSaveCliente['error'])){												     			       			

													     			       		$resultadoMigracionCaptura["capturas_error"]++;
												     			       			throw new Exception('EL CLIENTE ' . $TempRowData[1] . ' NO SE GUARDO EN EL SISTEMA POR : ' . $resultadoSaveCliente['error']);
													     			       	}
																		}else{

																			$ult_fecha_diligenciamiento = $this->_radicacion->getUltFechaDiligenciamientoSarlaftByClienteId($cliente_id["id"]);

																			$dataQuery = array();

																			foreach ($InfoClienteSarlaft as $keyInfoCliente=> $valueInfoCliente) {

																				if($TempRowData[2] >= $ult_fecha_diligenciamiento["ULT_FECHA_DILIGENCIAMIENTO"]){

																					if(isset($dataCapturaFile[$keyInfoCliente])){

																						$dataQuery[$keyInfoCliente] = $dataCapturaFile[$keyInfoCliente];
																					}
																				}
																			}

																			if(!empty($dataQuery)){
																				
																				$dataQuery["id"] = $InfoClienteSarlaft["id"];

																				$resultadoSaveCliente = $this->_crud->Update('cliente_sarlaft_natural',$dataQuery);

																				if(!isset($resultadoSaveCliente['error'])){

													     			       			$resultadoMigracionCaptura["capturas_ok"]++;
													     			       			$GuardarGestionCaptura = $this->_crud->Save(
															                            'gestion_clientes_captura',
															                            array(
															                                'GESTION_USUARIO_ID' => $TempRowData[0],
															                                'GESTION_CLIENTE_ID' => $cliente_id["id"],
															                                'GESTION_FECHA_DILIGENCIAMIENTO' => $TempRowData[2]
															                            )
															                        );

															                        if(isset($GuardarGestionCaptura['error'])){

															                        	$resultadoMigracionCaptura["capturas_error"]++;
															                        	throw new Exception('NO SE PUDO GUARDAR LA GESTION DE LA CAPTURA DEL CLIENTE ' . $TempRowData[1] . ' LINEA ' . $row . ' POR : ' . $GuardarGestionCaptura['error']);
															                        }
																				}else{

																					$resultadoMigracionCaptura["capturas_error"]++;
																	    			throw new Exception('NO SE ESTA ACTUALIZANDO LA INFORMACION DEL CLIENTE ' . $TempRowData[1] . ' LINEA NUMERO : ' . $row . ' POR: ' . $resultadoSaveCliente['error']);
												     			       			}
																			}
																		}
																	}else{
																		throw new Exception('HUBO UN ERROR AL CONSULTAR ENL CLIENTE POR : ' . $InfoClienteSarlaft['error']);
																	}
									            				}else{

											     			    	$resultadoMigracionCaptura["capturas_error"]++;
									            					throw new Exception('EL NUMERO DE IDENTIFICACION ' . $TempRowData[1] . ' NO EXISTE EN EL SISTEMA LINEA NUMERO : ' . $row);
											     			    }
								            				} else {

										            			$resultadoMigracionCaptura["capturas_error"]++;
										            			throw new Exception('EL NUMERO DE CLIENTE ESTA VACIO EN LA LINEA NUMERO : '. $row);
										            		}
											        	} catch (Exception $excep_captura) {

											        		$saveErrorMigracion = $this->_crud->Save('zr_migraciones',array(
											    				'TIPO_MIGRACION' => 'CAPTURA',
											    				'FECHA_MIGRACION_ARCHIVO' => date('Y-m-d',strtotime($dateFile)),
											    				'NOMBRE_ARCHIVO' => $_FILES["archivo_migracion"]["name"],
											    				'FECHA_MIGRACION_SISTEMA' => date('Y-m-d H:i:s'),
											    				'TOTAL_REGISTROS' => $resultadoMigracionCaptura["capturas_total"],
											    				'TOTAL_COMPLETADOS' => $resultadoMigracionCaptura["capturas_ok"],
											    				'TOTAL_ERRORES' => $resultadoMigracionCaptura["capturas_error"],
											    				'ESTADO_PROCESO' => "ERROR EXCEPCION EN EL PROCESO CAPTURA " . $data["opc_migracion_captura"] . ' DESCRIPCION : ' . $excep_captura->getMessage()
											    			));
											        	}
											        }
							        			} else if ($data["opc_migracion_captura"] == "CLIENTES_JURIDICOS") {

							        				for ($row = 2; $row <= $highestRow; $row++){ 

											        	//Lee La fila y columnas del archivo empezando de la columna A , Fila 2
											        	$DataFileXLSX = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, TRUE, FALSE);
											        	$TempRowData = $DataFileXLSX[0];

											        	try {

								            				if(!is_null($TempRowData[1])){

								            					$cliente_id = $this->_clientes->getClienteIDByDocument(trim($TempRowData[1]));

									            				if(!isset($cliente_id['error']) && isset($cliente_id["id"])){

									            					$InfoClienteSarlaft = $this->_clientes->getInfoClienteJuridicoByClienteId($cliente_id["id"]);
																	$dataCapturaFile = array(
																		'cliente' => $cliente_id["id"],
																		"ciudad_diligenciamiento" => $TempRowData[3],
																		"sucursal" => $TempRowData[4],
																		"tipo_solicitud" => $TempRowData[5],
																		"clase_vinculacion" => $TempRowData[6],
																		"clase_vinculacion_otro" => $TempRowData[7],
																		"relacion_tom_asegurado" => $TempRowData[8],
																		"relacion_tom_asegurado_otra" => $TempRowData[9],
																		"relacion_tom_beneficiario" => $TempRowData[10],
																		"relacion_tom_beneficiario_otra" => $TempRowData[11],
																		"relacion_aseg_beneficiario" => $TempRowData[12],
																		"relacion_aseg_beneficiario_otra" => $TempRowData[13],
																		"razon_social" => $TempRowData[14],
																		"info_basica_digito_verificacion" => $TempRowData[15],
																		"info_basica_tipo_sociedad" => $TempRowData[16],
																		"info_basica_tipo_sociedad_otro" => $TempRowData[17],
																		"ofi_principal_direccion" => $TempRowData[18],
																		"ofi_principal_tipo_empresa" => $TempRowData[19],
																		"ofi_principal_departamento_empresa" => $TempRowData[20],
																		"ofi_principal_ciudad_empresa" => $TempRowData[21],
																		"ofi_principal_telefono" => $TempRowData[22],
																		"ofi_principal_fax" => $TempRowData[23],
																		"ofi_principal_pagina_web" => $TempRowData[24],
																		"ofi_principal_email" => $TempRowData[25],
																		"ofi_principal_ciiu" => $TempRowData[26],
																		"ofi_principal_ciiu_otro" => $TempRowData[27],
																		"ofi_principal_ciiu_cod" => $TempRowData[28],
																		"ofi_principal_sector" => $TempRowData[29],
																		"ofi_principal_breve_descripcion_objeto_social" => $TempRowData[30],
																		"sucursal_direccion" => $TempRowData[31],
																		"sucursal_departamento" => $TempRowData[32],
																		"sucursal_ciudad" => $TempRowData[33],
																		"sucursal_telefono" => $TempRowData[34],
																		"sucursal_fax" => $TempRowData[35],
																		"sucursal_correo" => $TempRowData[36],
																		"rep_legal_primer_apellido" => $TempRowData[37],
																		"rep_legal_segundo_apellido" => $TempRowData[38],
																		"rep_legal_nombres" => $TempRowData[39],
																		"rep_legal_tipo_documento" => $TempRowData[40],
																		"rep_legal_tipo_documento_otro" => $TempRowData[41],
																		"rep_legal_documento" => $TempRowData[42],
																		"rep_legal_fecha_exp_documento" => $TempRowData[43],
																		"rep_legal_lugar_expedicion" => $TempRowData[44],
																		"rep_legal_fecha_nacimiento" => $TempRowData[45],
																		"rep_legal_lugar_nacimiento" => $TempRowData[46],
																		"rep_legal_nacionalidad_1" => $TempRowData[47],
																		"rep_legal_nacionalidad_2" => $TempRowData[48],
																		"rep_legal_email" => $TempRowData[49],
																		"rep_legal_direccion_residencia" => $TempRowData[50],
																		"rep_legal_pais_residencia" => $TempRowData[51],
																		"rep_legal_departamento_residencia" => $TempRowData[52],
																		"rep_legal_ciudad_residencia" => $TempRowData[53],
																		"rep_legal_telefono_residencia" => $TempRowData[54],
																		"rep_legal_celular_residencia" => $TempRowData[55],
																		"rep_legal_persona_publica" => $TempRowData[56],
																		"rep_legal_recursos_publicos" => $TempRowData[57],
																		"rep_legal_obligaciones_tributarias" => $TempRowData[58],
																		"rep_legal_obligaciones_tributarias_indique" => $TempRowData[59],
																		"anexo_accionistas" => $TempRowData[60],
																		"anexo_sub_accionistas" => $TempRowData[61],
																		"anexo_preguntas_ppes" => $TempRowData[62],
																		"ingresos" => $TempRowData[63],
																		"egresos" => $TempRowData[64],
																		"activos" => $TempRowData[65],
																		"pasivos" => $TempRowData[66],
																		"patrimonio" => $TempRowData[67],
																		"otros_ingresos" => $TempRowData[68],
																		"desc_otros_ingresos" => $TempRowData[69],
																		"declaracion_origen_fondos" => $TempRowData[70],
																		"operaciones_moneda_extranjera" => $TempRowData[71],
																		"tipo_operaciones_moneda_extranjera" => $TempRowData[72],
																		"tipo_operaciones_moneda_extranjera_otro" => $TempRowData[73],
																		"cuentas_moneda_exterior" => $TempRowData[74],
																		"productos_exterior" => $TempRowData[75],
																		"reclamaciones" => $TempRowData[76],
																		"reclamacion_anio" => $TempRowData[77],
																		"reclamacion_ramo" => $TempRowData[78],
																		"reclamacion_compania" => $TempRowData[79],
																		"reclamacion_valor" => $TempRowData[80],
																		"reclamacion_resultado" => $TempRowData[81],
																		"reclamacion_anio_2" => $TempRowData[82],
																		"reclamacion_ramo_2" => $TempRowData[83],
																		"reclamacion_compania_2" => $TempRowData[84],
																		"reclamacion_valor_2" => $TempRowData[85],
																		"reclamacion_resultado_2" => $TempRowData[86],
																		"huella" => $TempRowData[87],
																		"firma" => $TempRowData[88],
																		"entrevista" => $TempRowData[89],
																		"entrevista_fecha" => $TempRowData[90],
																		"entrevista_observacion" => $TempRowData[91],
																		"entrevista_resultado" => $TempRowData[92]
																	);

																	if(!isset($InfoClienteSarlaft['error'])){

																		if(!$InfoClienteSarlaft){

																			$resultadoSaveCliente = $this->_crud->Save('cliente_sarlaft_juridico',$dataCapturaFile);

																			if(isset($resultadoSaveCliente['error'])){

																				$resultadoMigracionCaptura["capturas_error"]++;
												     			       			throw new Exception('NO SE ESTA REGISTRANDO LA INFORMACION DEL CLIENTE ' . $TempRowData[1] . ' LINEA NUMERO : ' . $row . ' POR: ' . $resultadoSaveCliente['error']);
												     			       		}
																		}else{

																			$ult_fecha_diligenciamiento = $this->_radicacion->getUltFechaDiligenciamientoSarlaftByClienteId($cliente_id["id"]);

																			$dataQuery = array();

																			if($TempRowData[2] >= $ult_fecha_diligenciamiento["ULT_FECHA_DILIGENCIAMIENTO"]){

																				foreach ($InfoClienteSarlaft as $keyInfoCliente => $valueInfoCliente) {

																					if(isset($dataCapturaFile[$keyInfoCliente])){

																						$dataQuery[$keyInfoCliente] = $dataCapturaFile[$keyInfoCliente];
																					}
																				}
																			}

																			if(!empty($dataQuery)){

																				$dataQuery["id"] = $InfoClienteSarlaft["id"];

																				$resultadoSaveCliente = $this->_crud->Update('cliente_sarlaft_juridico',$dataQuery);

																				if(!isset($resultadoSaveCliente['error'])){

													     			       			$resultadoMigracionCaptura["capturas_ok"]++;
													     			       			$GuardarGestionCaptura = $this->_crud->Save(
															                            'gestion_clientes_captura',
															                            array(
															                                'GESTION_USUARIO_ID' => $TempRowData[0],
															                                'GESTION_CLIENTE_ID' => $cliente_id['id'],
															                                'GESTION_FECHA_DILIGENCIAMIENTO' => $TempRowData[2]
															                            )
															                        );

													     			       			if(isset($GuardarGestionCaptura['error'])){
																                        	
															                        	$resultadoMigracionCaptura["capturas_error"]++;
															                        	throw new Exception('NO SE GUARDAO LA GESTION DE LA CAPTURA DEL CLIENTE ' . $TempRowData[1] . ' LINEA ' . $row . ' POR : ' . $GuardarGestionCaptura['error']);
															                        }
													     			       		}else{

													     			       			$resultadoMigracionCaptura["capturas_error"]++;
													     			       			throw new Exception('NO SE ESTA REGISTRANDO LA INFORMACION DEL CLIENTE ' . $TempRowData[1] . ' LINEA NUMERO : ' . $row . ' POR: ' . $resultadoSaveCliente['error']);
													     			       		}
																			}
																		}
																	}else{
																		throw new Exception('HUBO UN ERROR AL CONSULTAR ENL CLIENTE POR : ' . $InfoClienteSarlaft['error']);
																	}
											     			    }else{

											     			    	$resultadoMigracionCaptura["capturas_error"]++;
									            					throw new Exception('EL NUMERO DE IDENTIFICACION ' . $TempRowData[1] . ' NO EXISTE EN EL SISTEMA LINEA NUMERO : ' . $row);
											     			    }
															} else {

										            			$resultadoMigracionCaptura["capturas_error"]++;
										            			throw new Exception('EL NUMERO DE CLIENTE ESTA VACIO EN LA LINEA NUMERO : '. $row);
										            		}
											        	} catch (Exception $excep_captura) {

											        		$saveErrorMigracion = $this->_crud->Save('zr_migraciones',array(
											    				'TIPO_MIGRACION' => 'CAPTURA',
											    				'FECHA_MIGRACION_ARCHIVO' => date('Y-m-d',strtotime($dateFile)),
											    				'NOMBRE_ARCHIVO' => $_FILES["archivo_migracion"]["name"],
											    				'FECHA_MIGRACION_SISTEMA' => date('Y-m-d H:i:s'),
											    				'TOTAL_REGISTROS' => $resultadoMigracionCaptura["capturas_total"],
											    				'TOTAL_COMPLETADOS' => $resultadoMigracionCaptura["capturas_ok"],
											    				'TOTAL_ERRORES' => $resultadoMigracionCaptura["capturas_error"],
											    				'ESTADO_PROCESO' => "ERROR EXCEPCION EN EL PROCESO CAPTURA " . $data["opc_migracion_captura"] . ' DESCRIPCION : ' . $excep_captura->getMessage()
											    			));
											        	}

											        }
							        			} else {
							        				throw new Exception('EL NOMBRE DE LA HOJA NO ESTA DESCRITO CORRECTAMENTE');
							        			}

										        if($resultadoMigracionCaptura["capturas_ok"] != 0){
										        	array_push($return,array(
										        		'type' => "STATES_OK", 
							                            'title' => 'MIGRACION COMPLETADOS',
							                            'message' => 'No CAPTURAS REGISTRADAS '.$resultadoMigracionCaptura["capturas_ok"]." de ".$resultadoMigracionCaptura["capturas_total"]
							                        ));
										        }

										        if($resultadoMigracionCaptura["capturas_error"] != 0){
										        	array_push($return,array(
										        		'type' => "STATES_ERROR", 
							                            'title' => 'MIGRACION ERRORES',
							                            'message' => 'No CAPTURAS NO REGISTRADAS '.$resultadoMigracionCaptura["capturas_error"]." de ".$resultadoMigracionCaptura["capturas_total"]
							                        ));
										        }

									        	$saveInitMigracion = $this->_crud->Save('zr_migraciones',array(
							        				'TIPO_MIGRACION' => 'CAPTURA',
							        				'FECHA_MIGRACION_ARCHIVO' => date('Y-m-d',strtotime($dateFile)),
							        				'NOMBRE_ARCHIVO' => $_FILES["archivo_migracion"]["name"],
							        				'FECHA_MIGRACION_SISTEMA' => date('Y-m-d H:i:s'),
							        				'TOTAL_REGISTROS' => $resultadoMigracionCaptura["capturas_total"],
							        				'TOTAL_COMPLETADOS' => $resultadoMigracionCaptura["capturas_ok"],
							        				'TOTAL_ERRORES' => $resultadoMigracionCaptura["capturas_error"],
							        				'ESTADO_PROCESO' => 'FINALIZACION MIGRACION PERSONA '.$data["opc_migracion_captura"]
							        			));

							        			if(!is_bool($saveInitMigracion)){

										        	throw new Exception('NO SE GUARDO EL REGISTRO DE FINALIZACION DEL ARCHIVO');
										        }else{

										        	echo json_encode($return);
										        }
										        
										        if(!unlink($loadFile['success']['ruta_temp'])){

										        	throw new Exception('EL ARCHIVO NO SE ELIMINO DE SU RUTA TEMPORAL');
										        }

        									} else{
        										throw new Exception('EL NOMBRE DE LA HOJA '. $data["opc_migracion_captura"] .' NO ES EL MISMO QUE SOLICITA EN EL DOCUMENTO ');
        									}													        			
	        							}
						        		break;

						        	case 'anexos':

						        		$ULT_FECHA_MIGRACION = $this->_model->findOldDateMigrate('ANEXOS');

			        					if(!empty($ULT_FECHA_MIGRACION["ULT_FECHA_MIGRACION"])){

			        						if(date('Y-m-d',strtotime($dateFile)) < $ULT_FECHA_MIGRACION["ULT_FECHA_MIGRACION"]){

			        							throw new Exception('LA FECHA DE MIGRACION ES MAS ANTIGUA QUE LA ULTIMA FECHA DE MIGRACION '.$ULT_FECHA_MIGRACION["ULT_FECHA_MIGRACION"]);
			        						}
			        					}

						        		// Recorre los nombre de la hojas del archivo
						        		foreach ($objPHPExcel->getSheetNames() as $valueNameSheet) {

						        			$sheet = $objPHPExcel->getSheetByName($valueNameSheet); // Carga cada hoja de calculo
						        			$highestRow = $sheet->getHighestRow(); // Total de fila por cada hoja de calculo
										    $highestColumn = $sheet->getHighestColumn(); // Total de columnas por cada hoja de calculo

					        				//Inicializa una variable para enviar los valores de la migracion todos empiezan en 0
					        				$resultadoMigracion = array(
					        					'anexos_total' => $highestRow,
					        					'anexos_ok' => 0,
					        					'anexos_error' => 0 
					        				);

					        				// Guarda el inicio de proceso de migracion radicacion
						        			$saveInitMigracion = $this->_crud->Save('zr_migraciones',array(
						        				'TIPO_MIGRACION' => 'ANEXOS',
						        				'FECHA_MIGRACION_ARCHIVO' => date('Y-m-d',strtotime($dateFile)),
						        				'NOMBRE_ARCHIVO' => $_FILES["archivo_migracion"]["name"],
						        				'FECHA_MIGRACION_SISTEMA' => date('Y-m-d H:i:s'),
						        				'TOTAL_REGISTROS' => $resultadoMigracion["anexos_total"],
						        				'TOTAL_COMPLETADOS' => $resultadoMigracion["anexos_ok"],
						        				'TOTAL_ERRORES' => $resultadoMigracion["anexos_error"],
						        				'ESTADO_PROCESO' => 'INICIA MIGRACION ' . $valueNameSheet
						        			));

										    // Verifica si el documento tiene mas de un campo ya que el primero es el del titulo
						        			if($highestRow > 1){

							        			// Separa por cada tipo de archivo ejecutar una funcion diferente con la informacion
									        	switch ($valueNameSheet) {

						        					case 'ANEXO_PPES':
						        						
						        						for ($row = 2; $row <= $highestRow; $row++){ 

												        	//Lee La fila y columnas del archivo empezando de la columna A , Fila 2
												        	$DataFileXLSX = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, TRUE, FALSE);
												        	$TempRowData = $DataFileXLSX[0];

												        	$cliente_id = $this->_clientes->getClienteIDByDocument(trim($TempRowData[0]));
												        	$dataAnexoPPE = array(
												        		'cliente_id' => $cliente_id["id"],
												        		'ppes_vinculo_relacion' => $TempRowData[2],
												        		'ppes_nombre' => $TempRowData[3], 
												        		'ppes_tipo_identificacion' => $TempRowData[4], 
												        		'ppes_no_documento' => $TempRowData[5], 
												        		'ppes_nacionalidad' => $TempRowData[6], 
												        		'ppes_entidad' => $TempRowData[7], 
												        		'ppes_cargo' => $TempRowData[8], 
												        		'ppes_desvinculacion' => $TempRowData[9]
												        	);

												        	try {
													        	if(!is_null($dataAnexoPPE["cliente_id"]) && !empty($dataAnexoPPE["cliente_id"])){

													        		$getAnexoPPE = $this->_clientes->getAnexoPPE($dataAnexoPPE["cliente_id"],$TempRowData[4],$TempRowData[5]);

													        		if(!$getAnexoPPE){

													        			$resultadoSaveAnexoPPE = $this->_crud->Save('zr_anexos_ppes',$dataAnexoPPE);
													        		}else{

													        			foreach ($getAnexoPPE as $keyAnexoPPE => $valueAnexoPPE) {
													        				if(isset($dataAnexoPPE[$keyAnexoPPE])){
													        					if(!is_null($valueAnexoPPE) || !empty($valueAnexoPPE)){
																					if(!is_null($TempRowData[1])){
																						$dataAnexoPPE[$keyAnexoPPE] = $valueAnexoPPE;
																					}																			        						
													        					}
													        				}
													        			}

													        			$dataAnexoPPE["id"] = $getAnexoPPE["id"];
													        			$resultadoSaveAnexoPPE = $this->_clientes->updateAnexos('zr_anexos_ppes',$dataAnexoPPE);
													        		}


													        		if(!isset($resultadoSaveAnexoPPE['error'])){

													        			$resultadoMigracion["anexos_ok"]++;
													        		}else{

													        			$resultadoMigracion["anexos_error"]++;
													        			throw new Exception('NO SE REGISTRO LOS DATOS DEL CLIENTE  '. $TempRowData[0] .' FILA: '. $row . ' POR: '. $resultadoSaveAnexoPPE['error']);
													        		}
													        	}else{

													        		$resultadoMigracion["anexos_error"]++;
													        		throw new Exception('EL DOCUMENTO ' . $TempRowData[0] . ' DE LA FILA: '. $row .' NO EXISTE EN EL SISTEMA');
													        	}
												        	} catch (Exception $ex) {

												        		// Guarda el inicio de proceso de migracion radicacion
											        			$resultadoMigracionAnexos = $this->_crud->Save('zr_migraciones',array(
											        				'TIPO_MIGRACION' => 'ANEXOS',
											        				'FECHA_MIGRACION_ARCHIVO' => date('Y-m-d',strtotime($dateFile)),
											        				'NOMBRE_ARCHIVO' => $_FILES["archivo_migracion"]["name"],
											        				'FECHA_MIGRACION_SISTEMA' => date('Y-m-d H:i:s'),
											        				'TOTAL_REGISTROS' => $resultadoMigracion["anexos_total"],
											        				'TOTAL_COMPLETADOS' => $resultadoMigracion["anexos_ok"],
							        								'TOTAL_ERRORES' => $resultadoMigracion["anexos_error"],
											        				'ESTADO_PROCESO' => 'EXCEPCION ' . $valueNameSheet ." ". $ex->getMessage()
											        			));
												        	}
												        }

												        if($resultadoMigracion["anexos_ok"] != 0){
												        	array_push($return,array(
												        		'type' => "STATES_OK", 
									                            'title' => 'MIGRACION COMPLETADOS',
									                            'message' => 'No '. $valueNameSheet .' REGISTRADAS '.$resultadoMigracion["anexos_ok"]." de ".$resultadoMigracion["anexos_total"]
									                        ));
												        }

												        if($resultadoMigracion["anexos_error"] != 0){
												        	array_push($return,array(
												        		'type' => "STATES_ERROR", 
									                            'title' => 'MIGRACION ERRORES',
									                            'message' => 'No ' . $valueNameSheet . 'NO REGISTRADAS '.$resultadoMigracion["anexos_error"]." de ".$resultadoMigracion["anexos_total"]
									                        ));
												        }

						        						break;

						        					case 'ANEXO_ACCIONISTAS':
						        						
						        						for ($row = 2; $row <= $highestRow; $row++){ 

												        	//Lee La fila y columnas del archivo empezando de la columna A , Fila 2
												        	$DataFileXLSX = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, TRUE, FALSE);
												        	$TempRowData = $DataFileXLSX[0];

												        	$cliente_id = $this->_clientes->getClienteIDByDocument(trim($TempRowData[0]));

												        	try {
												        		if($cliente_id){

														        	$dataAnexoAccionista = array(
														        		'cliente_id' => $cliente_id["id"],
														        		'accionista_tipo_documento' => $TempRowData[2],
																		'accionista_documento' => $TempRowData[3],
																		'accionista_nombres_completos' => $TempRowData[4],
																		'accionista_participacion' => $TempRowData[5],
																		'accionista_cotiza_bolsa' => $TempRowData[6],
																		'accionista_persona_publica' => $TempRowData[7],
																		'accionista_obligaciones_otro_pais' => $TempRowData[8],
																		'accionista_obligaciones_otro_pais_desc' => $TempRowData[9]
														        	);

														        	if(!is_null($dataAnexoAccionista["cliente_id"]) && !empty($dataAnexoAccionista["cliente_id"])){

														        		$getAnexoAccionista = $this->_clientes->getAnexoAccionista($dataAnexoAccionista["cliente_id"],$TempRowData[2],$TempRowData[3]);

														        		if(!$getAnexoAccionista){

														        			$resultadoSaveAnexoPPE = $this->_crud->Save('accionistas',$dataAnexoAccionista);
														        		}else{

														        			foreach ($getAnexoAccionista as $keyAnexoAccionista => $valueAnexoAccionista) {
														        				if(isset($dataAnexoAccionista[$keyAnexoAccionista])){
														        					if(!is_null($valueAnexoAccionista) || !empty($valueAnexoAccionista)){
																						if(!is_null($TempRowData[1])){
																							$dataAnexoAccionista[$keyAnexoAccionista] = $valueAnexoAccionista;
																						}																			        						
														        					}
														        				}
														        			}
														        			
														        			$dataAnexoAccionista["id"] = $getAnexoAccionista["id"];
														        			$resultadoSaveAnexoPPE = $this->_clientes->updateAnexos('accionistas',$dataAnexoAccionista);
														        		}

														        		if(!isset($resultadoSaveAnexoPPE['error'])){

														        			$resultadoMigracion["anexos_ok"]++;
														        		}else{

														        			$resultadoMigracion["anexos_error"]++;
														        			throw new Exception('NO SE REGISTRO LOS DATOS DEL CLIENTE  '. $TempRowData[0] .' FILA: '. $row . ' POR: '. $resultadoSaveAnexoPPE['error']);
														        		}
														        	}else{

														        		$resultadoMigracion["anexos_error"]++;
														        		throw new Exception('EL DOCUMENTO ' . $TempRowData[0] . ' DE LA FILA: '. $row .' NO EXISTE EN EL SISTEMA');
														        	}
													        	}else{

													        		$resultadoMigracion["anexos_error"]++;
														        	throw new Exception('EL CLIENTE ' . $TempRowData[0] .  ' FILA: '. $row .' NO ESTA REGISTRADO EN LA PLATAFORMA');
														        }
												        	} catch (Exception $ex) {

												        		// Guarda el inicio de proceso de migracion radicacion
											        			$resultadoMigracionAnexos = $this->_crud->Save('zr_migraciones',array(
											        				'TIPO_MIGRACION' => 'ANEXOS',
											        				'FECHA_MIGRACION_ARCHIVO' => date('Y-m-d',strtotime($dateFile)),
											        				'NOMBRE_ARCHIVO' => $_FILES["archivo_migracion"]["name"],
											        				'FECHA_MIGRACION_SISTEMA' => date('Y-m-d H:i:s'),
											        				'TOTAL_REGISTROS' => $resultadoMigracion["anexos_total"],
											        				'TOTAL_COMPLETADOS' => $resultadoMigracion["anexos_ok"],
							        								'TOTAL_ERRORES' => $resultadoMigracion["anexos_error"],
											        				'ESTADO_PROCESO' => 'EXCEPCION ' . $valueNameSheet ." ". $ex->getMessage()
											        			));
												        	}
												        }

												        if($resultadoMigracion["anexos_ok"] != 0){
												        	array_push($return,array(
												        		'type' => "STATES_OK", 
									                            'title' => 'MIGRACION COMPLETADOS',
									                            'message' => 'No '. $valueNameSheet .' REGISTRADAS '.$resultadoMigracion["anexos_ok"] . ' de ' . $resultadoMigracion["anexos_total"]
									                        ));
												        }

												        if($resultadoMigracion["anexos_error"] != 0){
												        	array_push($return,array(
												        		'type' => "STATES_ERROR", 
									                            'title' => 'MIGRACION ERRORES',
									                            'message' => 'No ' . $valueNameSheet . ' NO REGISTRADAS '.$resultadoMigracion["anexos_error"] . ' de '.$resultadoMigracion["anexos_total"]
									                        ));
												        }

						        						break;

						        					case 'ANEXO_SUB_ACCIONISTAS':
						        							
					        							for ($row = 2; $row <= $highestRow; $row++){ 

												        	//Lee La fila y columnas del archivo empezando de la columna A , Fila 2
												        	$DataFileXLSX = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, TRUE, FALSE);
												        	$TempRowData = $DataFileXLSX[0];

												        	$cliente_id = $this->_clientes->getClienteIDByDocument(trim($TempRowData[0]));

												        	try {
												        		if($cliente_id){
												        			
														        	$dataAnexoSubAccionista = array(
														        		'cliente_id' => $cliente_id["id"],
																		'sub_accionista_tipo_documento' => $TempRowData[2],
																		'sub_accionista_numero_id' => $TempRowData[3],
																		'sub_accionista_razon_social' => $TempRowData[4],
																		'sub_accionista_participacion' => $TempRowData[5],
																		'sub_accionista_nombre_sociedad_accionista' => $TempRowData[6],
																		'sub_accionista_documento' => $TempRowData[7]
														        	);

														        	if(!is_null($dataAnexoSubAccionista["cliente_id"]) && !empty($dataAnexoSubAccionista["cliente_id"])){

														        		$getAnexoSubAccionista = $this->_clientes->getAnexoSubAccionista($dataAnexoSubAccionista["cliente_id"],$TempRowData[2],$TempRowData[3]);

														        		if(!$getAnexoSubAccionista){

														        			$resultadoSaveAnexoSubAccionista = $this->_crud->Save('sub_accionistas',$dataAnexoSubAccionista);
														        		}else{

														        			foreach ($getAnexoSubAccionista as $keyAnexoSubAccionista => $valueAnexoSubAccionista) {
														        				if(isset($dataAnexoSubAccionista[$keyAnexoSubAccionista])){
														        					if(!is_null($valueAnexoSubAccionista) || !empty($valueAnexoSubAccionista)){
																						if(!is_null($TempRowData[1])){
																							$dataAnexoSubAccionista[$keyAnexoSubAccionista] = $valueAnexoSubAccionista;
																						}																			        						
														        					}
														        				}
														        			}
														        			
														        			$dataAnexoSubAccionista["id"] = $getAnexoSubAccionista["id"];
														        			$resultadoSaveAnexoSubAccionista = $this->_clientes->updateAnexos('sub_accionistas',$dataAnexoSubAccionista);
														        		}

														        		if(!isset($resultadoSaveAnexoSubAccionista['error'])){

														        			$resultadoMigracion["anexos_ok"]++;
														        		}else{

														        			$resultadoMigracion["anexos_error"]++;
														        			throw new Exception('NO SE REGISTRO LOS DATOS DEL CLIENTE  '. $TempRowData[0] .' FILA: '. $row . ' POR: '. $resultadoSaveAnexoSubAccionista['error']);
														        		}
														        	}else{

														        		$resultadoMigracion["anexos_error"]++;
														        		throw new Exception('EL DOCUMENTO ' . $TempRowData[0] . ' DE LA FILA: '. $row .' NO EXISTE EN EL SISTEMA');
														        	}
													        	}else{

													        		$resultadoMigracion["anexos_error"]++;
														        	throw new Exception('EL CLIENTE IDENTIFICADO ' . $TempRowData[0] .  ' FILA: '. $row .' NO ESTA REGISTRADO EN LA PLATAFORMA');
														        }
												        	} catch (Exception $ex) {

												        		// Guarda el inicio de proceso de migracion radicacion
											        			$resultadoMigracionAnexos = $this->_crud->Save('zr_migraciones',array(
											        				'TIPO_MIGRACION' => 'ANEXOS',
											        				'FECHA_MIGRACION_ARCHIVO' => date('Y-m-d',strtotime($dateFile)),
											        				'NOMBRE_ARCHIVO' => $_FILES["archivo_migracion"]["name"],
											        				'FECHA_MIGRACION_SISTEMA' => date('Y-m-d H:i:s'),
											        				'TOTAL_REGISTROS' => $resultadoMigracion["anexos_total"],
											        				'TOTAL_COMPLETADOS' => $resultadoMigracion["anexos_ok"],
							        								'TOTAL_ERRORES' => $resultadoMigracion["anexos_error"],
											        				'ESTADO_PROCESO' => 'EXCEPCION ' . $valueNameSheet ." ". $ex->getMessage()
											        			));
												        	}
												        }

												        if($resultadoMigracion["anexos_ok"] != 0){
												        	array_push($return,array(
												        		'type' => "STATES_OK", 
									                            'title' => 'MIGRACION COMPLETADOS',
									                            'message' => 'No '. $valueNameSheet .' REGISTRADAS '.$resultadoMigracion["anexos_ok"]." de ".$resultadoMigracion["anexos_total"]
									                        ));
												        }

												        if($resultadoMigracion["anexos_error"] != 0){
												        	array_push($return,array(
												        		'type' => "STATES_ERROR", 
									                            'title' => 'MIGRACION ERRORES',
									                            'message' => 'No ' . $valueNameSheet . 'NO REGISTRADAS '.$resultadoMigracion["anexos_error"]." de ".$resultadoMigracion["anexos_total"]
									                        ));
												        }

						        						break;

						        					case 'PRODUCTOS':
						        							
						        						for ($row = 2; $row <= $highestRow; $row++){ 

												        	//Lee La fila y columnas del archivo empezando de la columna A , Fila 2
												        	$DataFileXLSX = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, TRUE, FALSE);
												        	$TempRowData = $DataFileXLSX[0];

												        	$cliente_id = $this->_clientes->getClienteIDByDocument(trim($TempRowData[0]));

												        	try {
												        		if($cliente_id){
												        			
														        	$dataAnexoProducto = array(
														        		'cliente_id' => $cliente_id["id"],
																		'tipo_producto' => $TempRowData[2],
																		'identificacion_producto' => $TempRowData[3],
																		'entidad' => $TempRowData[4],
																		'monto' => $TempRowData[5],
																		'ciudad' => $TempRowData[6],
																		'pais' => $TempRowData[7],
																		'moneda' => $TempRowData[8]
														        	);

														        	if(!is_null($dataAnexoProducto["cliente_id"]) && !empty($dataAnexoProducto["cliente_id"])){

														        		$getAnexoProducto = $this->_clientes->getAnexoProducto($dataAnexoProducto["cliente_id"],$TempRowData[2],$TempRowData[3]);

														        		if(!$getAnexoProducto){

														        			$resultadoSaveAnexoProducto = $this->_crud->Save('productos',$dataAnexoProducto);
														        		}else{

														        			foreach ($getAnexoProducto as $keyAnexoProducto => $valueAnexoProducto) {
														        				if(isset($dataAnexoProducto[$keyAnexoProducto])){
														        					if(!is_null($valueAnexoProducto) || !empty($valueAnexoProducto)){
																						if(!is_null($TempRowData[1])){
																							$dataAnexoProducto[$keyAnexoProducto] = $valueAnexoProducto;
																						}																			        						
														        					}
														        				}
														        			}
														        			
														        			$dataAnexoProducto["id"] = $getAnexoProducto["id"];
														        			$resultadoSaveAnexoSubAccionista = $this->_clientes->updateAnexos('sub_accionistas',$dataAnexoProducto);
														        		}


														        		if(!isset($resultadoSaveAnexoProducto['error'])){

														        			$resultadoMigracion["anexos_ok"]++;
														        		}else{

														        			$resultadoMigracion["anexos_error"]++;
														        			throw new Exception('NO SE REGISTRO LOS DATOS DEL CLIENTE  '. $TempRowData[0] .' FILA: '. $row . ' POR: '. $resultadoSaveAnexoProducto['error']);
														        		}
														        	}else{

														        		$resultadoMigracion["anexos_error"]++;
														        		throw new Exception('EL DOCUMENTO ' . $TempRowData[0] . ' DE LA FILA: '. $row .' NO EXISTE EN EL SISTEMA');
														        	}
													        	}else{

													        		$resultadoMigracion["anexos_error"]++;
														        	throw new Exception('EL CLIENTE IDENTIFICADO ' . $TempRowData[0] .  ' FILA: '. $row .' NO ESTA REGISTRADO EN LA PLATAFORMA');
														        }
												        	} catch (Exception $ex) {

												        		// Guarda el inicio de proceso de migracion radicacion
											        			$resultadoMigracionAnexos = $this->_crud->Save('zr_migraciones',array(
											        				'TIPO_MIGRACION' => 'ANEXOS',
											        				'FECHA_MIGRACION_ARCHIVO' => date('Y-m-d',strtotime($dateFile)),
											        				'NOMBRE_ARCHIVO' => $_FILES["archivo_migracion"]["name"],
											        				'FECHA_MIGRACION_SISTEMA' => date('Y-m-d H:i:s'),
											        				'TOTAL_REGISTROS' => $resultadoMigracion["anexos_total"],
											        				'TOTAL_COMPLETADOS' => $resultadoMigracion["anexos_ok"],
							        								'TOTAL_ERRORES' => $resultadoMigracion["anexos_error"],
											        				'ESTADO_PROCESO' => 'EXCEPCION ' . $valueNameSheet ." ". $ex->getMessage()
											        			));
												        	}
												        }

												        if($resultadoMigracion["anexos_ok"] != 0){
												        	array_push($return,array(
												        		'type' => "STATES_OK", 
									                            'title' => 'MIGRACION COMPLETADOS',
									                            'message' => 'No '. $valueNameSheet .' REGISTRADAS '.$resultadoMigracion["anexos_ok"]." de ".$resultadoMigracion["anexos_total"]
									                        ));
												        }

												        if($resultadoMigracion["anexos_error"] != 0){
												        	array_push($return,array(
												        		'type' => "STATES_ERROR", 
									                            'title' => 'MIGRACION ERRORES',
									                            'message' => 'No ' . $valueNameSheet . ' NO REGISTRADAS '.$resultadoMigracion["anexos_error"]." de ".$resultadoMigracion["anexos_total"]
									                        ));
												        }

						        						break;
						        					
						        					default:
						        						throw new Exception('NO HAY NINGUNA ACCION SOBRE ESTA HOJA ' . $valueNameSheet);
						        						break;
									        	}
									        }

									        // Guarda el inicio de proceso de migracion radicacion
						        			$saveInitMigracion = $this->_crud->Save('zr_migraciones',array(
						        				'TIPO_MIGRACION' => 'ANEXOS',
						        				'FECHA_MIGRACION_ARCHIVO' => date('Y-m-d',strtotime($dateFile)),
						        				'NOMBRE_ARCHIVO' => $_FILES["archivo_migracion"]["name"],
						        				'FECHA_MIGRACION_SISTEMA' => date('Y-m-d H:i:s'),
						        				'TOTAL_REGISTROS' => $resultadoMigracion["anexos_total"],
						        				'TOTAL_COMPLETADOS' => $resultadoMigracion["anexos_ok"],
						        				'TOTAL_ERRORES' => $resultadoMigracion["anexos_error"],
						        				'ESTADO_PROCESO' => 'FINALIZACION MIGRACION ' . $valueNameSheet
						        			));

						        			if(isset($saveInitMigracion['error'])){

									        	throw new Exception('NO SE GUARDO EL REGISTRO DE FINALIZACION DEL ARCHIVO ' . $valueNameSheet);
									        	exit;
									        }
						        		}

						        		if(count($return)){
						        			echo json_encode($return);
						        		}

						        		if(!unlink($loadFile['success']['ruta_temp'])){

								        	throw new Exception('EL ARCHIVO NO SE ELIMINO DE SU RUTA TEMPORAL');
								        }
						        		break;

        							case 'completitud_verificacion':

        								$ULT_FECHA_MIGRACION = $this->_model->findOldDateMigrate('COMPLETITUD_VERIFICACION');

			        					if(!empty($ULT_FECHA_MIGRACION["ULT_FECHA_MIGRACION"])){

			        						if(date('Y-m-d',strtotime($dateFile)) < $ULT_FECHA_MIGRACION["ULT_FECHA_MIGRACION"]){

			        							throw new Exception('LA FECHA DE MIGRACION ES MAS ANTIGUA QUE LA ULTIMA FECHA DE MIGRACION '.$ULT_FECHA_MIGRACION["ULT_FECHA_MIGRACION"]);
			        						}
			        					}

										$sheet = $objPHPExcel->getSheet(0); 
								        $highestRow = $sheet->getHighestRow(); 
								        $highestColumn = $sheet->getHighestColumn();

							        	// Variable global para almacenamiento de errores en el sistema
					        			$resultadoMigracionCompletitudVerificacion = array(
					        				'completitud_total' => $highestRow,
					        				'completitud_ok' => 0, 
					        				'completitud_error' => 0
					        			);


					        			// Guarda el inicio de proceso de migracion radicacion
					        			$saveInitMigracionJuridico = $this->_crud->Save('zr_migraciones',array(
					        				'TIPO_MIGRACION' => 'COMPLETITUD_VERIFICACION',
					        				'FECHA_MIGRACION_ARCHIVO' => date('Y-m-d',strtotime($dateFile)),
					        				'NOMBRE_ARCHIVO' => $_FILES["archivo_migracion"]["name"],
					        				'FECHA_MIGRACION_SISTEMA' => date('Y-m-d H:i:s'),
					        				'TOTAL_REGISTROS' => $resultadoMigracionCompletitudVerificacion["completitud_total"],
					        				'TOTAL_COMPLETADOS' => $resultadoMigracionCompletitudVerificacion["completitud_ok"],
					        				'TOTAL_ERRORES' => $resultadoMigracionCompletitudVerificacion["completitud_error"],
					        				'ESTADO_PROCESO' => 'INICIA MIGRACION COMPLETITUD Y VERIFICACION'
					        			));

					        			
				        				for ($row = 2; $row <= $highestRow; $row++){

				        					$DataFileXLSX = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, TRUE, FALSE);
								        	$TempRowData = $DataFileXLSX[0];

								        	try {

								        		if(!is_null($TempRowData[0])){

								        			$cliente_id = $this->_clientes->getClienteIDByDocument(trim($TempRowData[0]));

						            				if(!isset($cliente_id['error']) && isset($cliente_id["id"])){

						            					$dataCompletitudVerificacion = array(
															'GESTION_USUARIO_ID'             	=> 1,
															'GESTION_CLIENTE_ID'             	=> $cliente_id["id"],
															'GESTION_FECHA_DILIGENCIAMIENTO' 	=> $TempRowData[1],
															'GESTION_TIPOLOGIA'              	=> 'TELEFONICA',
															'GESTION_ESTADO_TIPOLOGIA_ID'    	=> $TempRowData[2],
															'GESTION_NO_INTENTOS'            	=> $TempRowData[3],
															'GESTION_PROCESO_ID'             	=> $TempRowData[4],
															'FECHA_GESTION' 				 	=> $TempRowData[5] 
						            					);

						            					$resultadoSaveCliente = $this->_crud->Save('gestion_clientes_completitud_verificacion', $dataCompletitudVerificacion);

						            					if(!isset($resultadoSaveCliente['error'])){

						            						$resultadoMigracionCompletitudVerificacion["completitud_total"]++;
					            						}else{

								     			       		$resultadoMigracionCompletitudVerificacion["completitud_error"]++;
							     			       			throw new Exception('EL CLIENTE ' . $TempRowData[1] . ' NO SE GUARDO EN EL SISTEMA POR : ' . $resultadoSaveCliente['error']);
								     			       	}
						            				}else{

								     			    	$resultadoMigracionCompletitudVerificacion["completitud_error"]++;
						            					throw new Exception('EL NUMERO DE IDENTIFICACION ' . $TempRowData[0] . ' NO EXISTE EN EL SISTEMA LINEA NUMERO : ' . $row);
								     			    }
								        		}else {

							            			$resultadoMigracionCompletitudVerificacion["completitud_error"]++;
							            			throw new Exception('EL NUMERO DE CLIENTE ESTA VACIO EN LA LINEA NUMERO : '. $row);
							            		}
								        	} catch (Exception $excep_captura) {

								        		$saveErrorMigracion = $this->_crud->Save('zr_migraciones',array(
								    				'TIPO_MIGRACION' => 'COMPLETITUD_VERIFICACION',
								    				'FECHA_MIGRACION_ARCHIVO' => date('Y-m-d',strtotime($dateFile)),
								    				'NOMBRE_ARCHIVO' => $_FILES["archivo_migracion"]["name"],
								    				'FECHA_MIGRACION_SISTEMA' => date('Y-m-d H:i:s'),
								    				'TOTAL_REGISTROS' => $resultadoMigracionCompletitudVerificacion["completitud_total"],
								    				'TOTAL_COMPLETADOS' => $resultadoMigracionCompletitudVerificacion["completitud_total"],
								    				'TOTAL_ERRORES' => $resultadoMigracionCompletitudVerificacion["completitud_error"],
								    				'ESTADO_PROCESO' => "ERROR EXCEPCION EN EL PROCESO DE COMPLETITUD Y VERIFICACION " . $data["opc_migracion_captura"] . ' DESCRIPCION : ' . $excep_captura->getMessage()
								    			));
								        	}

				        				}
				        				

					        			if($resultadoMigracionCompletitudVerificacion["completitud_total"] != 0){
								        	array_push($return,array(
								        		'type' => "STATES_OK", 
					                            'title' => 'MIGRACION COMPLETADOS',
					                            'message' => 'No PROCESOS REGISTRADOS COMPLETITUD VERIFICACION '.$resultadoMigracionCompletitudVerificacion["completitud_total"]." de ".$resultadoMigracionCompletitudVerificacion["capturas_total"]
					                        ));
								        }

								        if($resultadoMigracionCompletitudVerificacion["completitud_error"] != 0){
								        	array_push($return,array(
								        		'type' => "STATES_ERROR", 
					                            'title' => 'MIGRACION ERRORES',
					                            'message' => 'No PROCESOS NO REGISTRADOS COMPLETITUD VERIFICACION '.$resultadoMigracionCompletitudVerificacion["completitud_error"]." de ".$resultadoMigracionCompletitudVerificacion["capturas_total"]
					                        ));
								        }

							        	$saveInitMigracion = $this->_crud->Save('zr_migraciones',array(
					        				'TIPO_MIGRACION' => 'COMPLETITUD_VERIFICACION',
					        				'FECHA_MIGRACION_ARCHIVO' => date('Y-m-d',strtotime($dateFile)),
					        				'NOMBRE_ARCHIVO' => $_FILES["archivo_migracion"]["name"],
					        				'FECHA_MIGRACION_SISTEMA' => date('Y-m-d H:i:s'),
					        				'TOTAL_REGISTROS' => $resultadoMigracionCompletitudVerificacion["completitud_total"],
					        				'TOTAL_COMPLETADOS' => $resultadoMigracionCompletitudVerificacion["completitud_total"],
					        				'TOTAL_ERRORES' => $resultadoMigracionCompletitudVerificacion["completitud_error"],
					        				'ESTADO_PROCESO' => 'FINALIZACION MIGRACION COMPLETITUD Y VERIFICACION'
					        			));

					        			if(!is_bool($saveInitMigracion)){

								        	throw new Exception('NO SE GUARDO EL REGISTRO DE FINALIZACION DEL ARCHIVO');
								        }else{

								        	echo json_encode($return);
								        }
								        
								        if(!unlink($loadFile['success']['ruta_temp'])){

								        	throw new Exception('EL ARCHIVO NO SE ELIMINO DE SU RUTA TEMPORAL');
								        }

        								break;
        							default:
        								throw new Exception('LA FUNCION QUE SE ESPECIFICA PARA MIGRAR NO EXISTE');
        								break;
        						}
        					}
    					}else{
    						throw new Exception('EL NOMBRE DEL ARCHIVO NO TIENE LA ESTRUCTURA DEFINIDA DE FECHA (fecha migracion ejemplo "02012018")');
    					}
    				}else{
    					throw new Exception('EL NOMBRE DEL ARCHIVO NO TIENE LA ESTRUCTURA DEFINIDA (debe llevar la palabra migracion-fechamigracion)');
    				}
    			}else{
    				throw new Exception($loadFile['error']);
    			}
    		} catch (Exception $e) {
                array_push($return,array(
	        		'type' => "STATES_ERROR", 
                    'title' => 'MIGRACION ERRORES',
                    'message' => 'ERROR EXCEPCION '. $e->getMessage()
                ));
                echo json_encode($return);
    		}
    	}else{
            $this->redireccionar();
        }
    }

    // Realiza un check list de los documento del cliente por cada linea de negocio a la que pertenezca
    public function ChecklistDocumentosByCliente($cliente,$tipo_cliente,$files_anexos = array()) {
        // Inicializa la variable de retorno
        $documentos_requiridos = array();

        $check_list = array();

        // Como primer instancia requiere el documento SAA
        $documentos_requiridos[]= "SAA";

        $lineas_negocio_cliente = $this->_clientes->getAllLineaNegocioClienteBy($cliente);

        if(!empty($lineas_negocio_cliente)){

            $documentos_pendientes = $this->_clientes->getAllFilesPendientClient($cliente);

            foreach ($lineas_negocio_cliente as $linea_cliente_id) {

                // pone por debajo el número de documento para cuando lo seleccione vaya al formulario
                $documentos_requiridos = $this->_clientes->getDocumentosRequeridosLineaNegocio($linea_cliente_id["LINEA_NEGOCIO_ID"],$tipo_cliente);

                if($documentos_requiridos){

                    foreach ($documentos_requiridos as $doc_requerido){

                        $doc_requerido = $doc_requerido["DOCUMENTO_REQUERIDO"];

                        $DOCUMENTO_ID = $this->_files->getFileIDByCodigo($doc_requerido);

                        if(!in_array($doc_requerido,array_column($documentos_pendientes, "DOCUMENTOS_PENDIENTES_CODIGO"))){

                            $VerifyFilePendientExist = $this->_clientes->VerifyFilePendientClient($cliente,$DOCUMENTO_ID["id"]);

                            if($VerifyFilePendientExist["DOCUMENTO_EXISTE"] == 0){

                                if($doc_requerido == 'APEP'){

                                    if(in_array('APEP',$files_anexos)){

                                        $temp_check = $this->_checkFileClient($doc_requerido, $cliente);
                                        if(!$temp_check){

                                            if(!in_array($doc_requerido, $check_list)){
                                                $check_list[] = $doc_requerido;
                                            }

                                            $this->_crud->Save('zr_clientes_pendientes_documentos',array(
                                                'CLIENTE_ID' => $cliente, 
                                                'TIPO_DOC_CLIENTE' => $tipo_doc_cliente, 
                                                'DOCUMENTO_PENDIENTE' => (int)$DOCUMENTO_ID["id"])
                                            );
                                        }
                                    }

                                }else{

                                    $temp_check = $this->_checkFileClient($doc_requerido, $cliente);
                                    if(!$temp_check){

                                        if(!in_array($doc_requerido, $check_list)){
                                            $check_list[] = $doc_requerido;
                                        }   

                                        $this->_crud->Save('zr_clientes_pendientes_documentos',array(
                                            'CLIENTE_ID' => $cliente,
                                            'DOCUMENTO_PENDIENTE' => (int)$DOCUMENTO_ID["id"])
                                        );
                                    }
                                }
                            }
                        }else{

                            if($doc_requerido == 'APEP'){
                                if(!in_array('APEP',$files_anexos)){
                                    $this->_clientes->DeleteFilePendienteClienteById($cliente,$DOCUMENTO_ID["id"]);
                                }
                            }

                            $VerifyFilePendientExist = $this->_clientes->VerifyFilePendientClient($cliente,$DOCUMENTO_ID["id"]);

                            if($VerifyFilePendientExist["DOCUMENTO_EXISTE"] != 0){
                                if(!in_array($doc_requerido, $check_list)){
                                    $check_list[] = $doc_requerido;
                                }
                            }
                        }
                    }
                }
            }
        }

        return $check_list;
    }

    // Verifica si el documento del cliente existe en las carpetas que estan por FTP
    public function _checkFileClient($type_doc,$id) {
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

    // Valida que la fecha que llega venga en el formato en el que se esta solicitando
    public function validateDate($date, $format = 'Y-m-d H:i:s') {
	    $d = DateTime::createFromFormat($format, $date);
	    return $d && $d->format($format) == $date;
	}

	// Verifica y libera cada cliente en estado migración y lo envia a finalizado o pendiente por documentos
	public function liberacionPendientesMigracion(){

		//Obtiene la informacion de clientes en estado migracion 
		$clientesMigracion = $this->_model->LiberarClientesMigracion();

        if(!isset($clientesMigracion['error'])){

            $result = array('clientes_liberados' => 0);
            $files_anexos = array();
            $return = array();

            foreach ($clientesMigracion as $valueClienteMig) {

            	if($valueClienteMig["tipo_persona"] == 'NAT'){
            		$getAnexoPPE = $this->_clientes->getInfoClienteNaturalByClienteIdId($valueClienteMig["cliente_id"]);
            	}else{
            		$getAnexoPPE = $this->_clientes->getInfoClienteJuridicoByClienteIdId($valueClienteMig["cliente_id"]);
            	}
            	
        		if($getAnexoPPE['anexo_preguntas_ppes'] == 1){
        			array_push($files_anexos,'APEP');
        		}

                $documentos_pendientes_cliente = $this->ChecklistDocumentosByCliente($valueClienteMig["cliente_id"],$valueClienteMig["tipo_persona"],$files_anexos);

                if(!empty($documentos_pendientes_cliente)){

                    if($valueClienteMig["estado_formulario_id"] != 4){

                        $resultadoSaveGestionCliente = $this->_crud->Save(
                            'zr_estado_proceso_clientes_sarlaft',
                            array(
                                'PROCESO_USUARIO_ID' 				=> $_SESSION["Mundial_authenticate_user_id"],
                                'PROCESO_CLIENTE_ID' 				=> $valueClienteMig["cliente_id"],
                                'PROCESO_FECHA_DILIGENCIAMIENTO'	=> $valueClienteMig["FECHA_DILIGENCIAMIENTO_ACTUAL"],
                                'ESTADO_PROCESO_ID' 				=> 4,
                                'PROCESO_ACTIVO' 					=> 0,
                            )
                        );
                    }
                }else{

                    if($valueClienteMig["estado_formulario_id"] != 3){

                    	$result['clientes_liberados']++;
                        $resultadoSaveGestionCliente = $this->_crud->Save(
                            'zr_estado_proceso_clientes_sarlaft',
                            array(
                                'PROCESO_USUARIO_ID' 				=> $_SESSION["Mundial_authenticate_user_id"],
                                'PROCESO_CLIENTE_ID' 				=> $valueClienteMig["cliente_id"],
                                'PROCESO_FECHA_DILIGENCIAMIENTO' 	=> $valueClienteMig["FECHA_DILIGENCIAMIENTO_ACTUAL"],
                                'ESTADO_PROCESO_ID' 				=> 3,
                                'PROCESO_ACTIVO' 					=> 0,
                            )
                        );
                    }
                }
            }

            if($result['clientes_liberados'] != 0){
	            array_push($return,array(
	        		'type' => "STATES_OK", 
                    'title' => 'MIGRACION COMPLETADOS',
                    'message' => 'No clientes liberados '.$result['clientes_liberados']
                ));
            }else{
            	array_push($return,array(
	        		'type' => "STATES_ERROR", 
                    'title' => 'MIGRACION COMPLETADOS',
                    'message' => 'No clientes No liberados 0'
                ));
            }

            echo json_encode($return);

        }else{
        	throw new Exception('EL SISTEMA ARROJA ERROR: ' . $clientesMigracion['error']);
        }
	}
}