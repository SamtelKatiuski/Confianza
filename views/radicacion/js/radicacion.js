var selected_radicacion = false;
$(document).ready(function() {

	disabledFields = $(":disabled");


	// Al dar Enter busca el documento del cliente radicado en el sistema
	$('body').on('keypress','input[name="numero_identificacion"]',function(event) {

		//Detecta la tecla enter solo en el campo de numero de identificacion
		if(event.which == 13){
			var continuar = false;

			if(selected_radicacion){
				if(confirm("Tiene una radicación seleccionada, desea continuar?")){
					continuar = true;
				}
			}else {
				continuar = true;
			}

			if(continuar){
				selected_radicacion = false;
				if(!IfEmpty(event.target)){

					SearchRadicacion(this.value);
				}else{

					resetForm('form-radicacion');
					ValidateFieldEmpty(event.target);
					$('#nuevo-radicado').addClass('hidden');
					$('#estado_cliente').text('');
					$('#NRadicado').empty();
					$('#ClienteRadicado').empty();
					disabledFields.prop('disabled', 'true');
				}
			}
		}
	});

	//Al dar clic busca el documento del cliente radicado en del sistema
	$('body').on('click', 'span#searchRadicacion', function(event) {

		var no_identificacion = this.parentElement.firstElementChild;
		var continuar = false;

		if(selected_radicacion){
			if(confirm("Tiene una radicación seleccionada, desea continuar?")){
				continuar = true;
			}
		}else {
			continuar = true;
		}

		if(continuar){
			selected_radicacion = false;
			if(!IfEmpty(no_identificacion)){

				SearchRadicacion(no_identificacion.value);
			}else{

				resetForm('form-radicacion');
				ValidateFieldEmpty(no_identificacion);
				$('#nuevo-radicado').addClass('hidden');
				$('#estado_cliente').text('');
				$('#NRadicado').empty();
				$('#ClienteRadicado').empty();
				disabledFields.prop('disabled', 'true');
			}
		}
		
	});

	

    //Al dar click en el check de formulario sarlaft habilita y requiere la fecha de diligenciamiento
    $('body').on('click', 'input[name="formulario_sarlaft"]', function(event) {

    	if($(this).is(':checked')){

    		$(this).val(1);
    		$('input[name="fecha_diligenciamiento"]').attr('data-required', 'true');
    		$('div#formulario').find('input[type="radio"][name="formulario"]').eq(0).attr('data-required', true);
    		checkboxEnabledField(false,this,'input[name="fecha_diligenciamiento"]','disabled');
    		checkboxEnabledField(false,this,'input[name="sin_fecha_diligenciamiento"]','disabled');
    	}else{
    		$(this).val(0);
    		$('input[name="fecha_diligenciamiento"]').removeAttr('data-required').prop('disabled',true).val('');
    		$('input[name="sin_fecha_diligenciamiento"]').prop('disabled',true).parent().find('small.help-error-block').empty();
    		$('div#formulario').find('input[type="radio"][name="formulario"]').eq(0).removeAttr('data-required');
    	}
    });

    //Al dar click en el check de sin fecha deshabilita y no la deja requerido en el formulario
    $('body').on('click', 'input[name="sin_fecha_diligenciamiento"]', function(event) {

    	if($(this).is(':checked')){

    		$('input[name="fecha_diligenciamiento"]').prop('disabled',true)
    			.removeAttr('data-required')
    			.val('')
    			.parent().find('small.help-error-block').empty();
    		$('input[name="devuelto"][value="Si"]').prop('checked',true).trigger('click');
    	}else{

    		checkboxEnabledField(false,this,'input[name=fecha_diligenciamiento]','disabled');
    		$('input[name="fecha_diligenciamiento"]').attr('data-required', 'true');
    		$('input[name="check_observaciones"]').removeAttr('data-required');
    		$('input[name="devuelto"][value="No"]').prop('checked',false).trigger('click');
    	}
    });

    //Función al dar click en digital se coloca en digitalizado NO y si es fisico digitalizado SI
	$('body').on('click', 'input[name="tipo_medio"]', function(){
		var temp = 'fieldset#digitalizado';
		$.each($("input[name='digitalizado']"), function(indexdigitalizado, valdigitalizado) {
			if($(valdigitalizado).is(':checked')){
				$(valdigitalizado).prop('checked',false);
			}
		});
		if($(this).val() == 'Fisico'){
			$("input[name='digitalizado'][value='Si']").prop('checked',true);
		}else{
			$("input[name='digitalizado'][value='No']").prop('checked',true);
		}
	});

	//Al dar click en separado requiere y muestra la cantidad de documentos separados
	$('body').on('click', 'input[name="separado"]', function(){
		var temp = 'div#cantidad_separado';
		if($(this).val() == 'Si'){
			checkboxEnabledField(true,this,temp,'hide');
			$('input[name="cantidad_separada"]').attr('data-required', true);
		}else{
			checkboxEnabledField(false,this,temp,'hide');
			$('input[name=cantidad_separada]').removeAttr('data-required');
			$('input[name=cantidad_separada]').val('');
		}
	});

	//Al dar devuelto Si se muestran la observaciones y se checkea que si hay observaciones
	$('body').on('click', 'input[name="devuelto"]', function(){

		temp = "input[name='check_observaciones']";
		$('textarea[name=radicacion_observacion]').parent('.form-group').children('small').empty();

		//Verifica si el campo que acabo de hacer check el valor es si y esta checkeado
		if($(this).is(':checked') && $(this).val() == "Si"){

			//Chequea el check de observaciones y lo deja requerido
			$(temp).attr('data-required',true).prop('checked',true);

			//Habilita y deja visualizar el campo de texto para enviar las observaciones en la radicacion
			// $('input[name="correo_radicacion"]').parents('div.col-md-6').removeClass('hidden');
			checkboxEnabledField(true,this,'textarea[name=radicacion_observacion]','hide');
			$('textarea[name=radicacion_observacion]').attr('data-required',true); //deja requerido el campo
		}else{

			// $('input[name="correo_radicacion"]').parents('div.col-md-6').addClass('hidden');
			$('input[name="check_observaciones"]').parents('div.col-md-6').addClass('hidden');
			//Verifica si el campo de texto de observaciones tenia algun dato digitado
			if($('textarea[name=radicacion_observacion]').val() != ''){

				//Confirma si desea eliminar lo que habia escrito en la observacion
				if(confirm('Existe observaciones en esta radicacion desea eliminarlas ? ')){

					//Deschequea el check de observaciones
					$(temp).removeAttr('data-required')
						.prop('checked',false);
					
					//Limpia y elimina el requerimiento de campo de observacion
					$('textarea[name=radicacion_observacion]').removeAttr('data-required').val('');
					checkboxEnabledField(false,this,'textarea[name=radicacion_observacion]','hide');

				}
				else{

					//Deja el capo como estaba si la respuesta de la confirmaciones falsa
					checkboxEnabledField(false,this,temp,'disabled');
					$("input[name='devuelto'][value='Si']").prop('checked',true);
				}

			}else{

				//Deschequea el checkbox de observaciones
				$(temp).prop('checked',false).removeAttr('data-required');
				checkboxEnabledField(false,this,'textarea[name=radicacion_observacion]','hide');
				$('textarea[name=radicacion_observacion]').removeAttr('data-required');

			}
		}
	});

	var LastSelect = false;
	//Al seleccionar AREA no deja requerido el formlario
	$('body').on('change', 'select[name="radicacion_proceso"]', function(event) {

		if(this.value == 'AREA'){

			$('div#formulario').find('input[type="radio"][data-required="true"]').removeAttr('data-required');

			if($("#table-content-files-rename").find("tbody").find("tr").length){

				if(confirm("Tiene archivos cargados, ¿desea eliminarlos?")){

					$("#table-content-files-rename").find("tbody").find("tr").remove();
					if($("#formulario_sarlaft").is(":checked")){

						$("#formulario_sarlaft").click();
					}
				}else{

					$(this).find("option")
						.removeAttr('selected')
						.filter('[value="' + LastSelect + '"]')
						.attr('selected', true)
						.parent().val(LastSelect);
				}
			}
		}else if(this.value == 'LEGAL'){

			if($("#table-content-files-rename").find("tbody").find("tr").length){

				if(confirm("Tiene archivos cargados, ¿desea eliminarlos?")){

					$("#table-content-files-rename").find("tbody").find("tr").remove();
					if($("#formulario_sarlaft").is(":checked")){

						$("#formulario_sarlaft").click();
					}
				}else{
					$(this).find("option")
						.removeAttr('selected')
						.filter('[value="' + LastSelect + '"]')
						.attr('selected', true)
						.parent().val(preValue);
				}
			}
		}
		LastSelect = this.value;
	});
		
    var count_files = 0;
	//Agrega una fila para agregar un archivo de renombramiento
	$('body').on('click', 'button#add-file-rename', function(event) {
		count_files++;
		var rowFileRename = '<tr id="' + count_files + '">'+
								'<th>'+
									'<input type="file" name="archivo_renombramiento_' + count_files + '"/>'+
								'</th>'+
								'<th>'+
									'<input type="text" name="file_renombrado[' + count_files + ']" placeholder="Nombre Archivo Renombrado" readonly="readonly" class="form-control renombramiento-file"/ style="text-transform: uppercase;">'+
								'</th>'+
								'<th>'+
									'<div class="btn-group">'+
										'<button type="button" class="btn btn-default btn-xs" onclick="renombrarArchivo(this,' + count_files + ')"><span class="glyphicon glyphicon-pencil"></span></button>'+
										'<button type="button" class="btn btn-danger btn-xs delete-file-rename"><span class="glyphicon glyphicon-trash"></span></button>'+
									'</div>'+
								'</th>'+
							'</tr>';
		$('table#table-content-files-rename tbody').append(rowFileRename);
		$(':input[name="cantidad_documentos"]').val($('table#table-content-files-rename tbody tr').length);
	});

	//Elimina una de las filas que se de clic en los archivos de renombramiento
	$('body').on('click', 'button.delete-file-rename', function(event) {
		$(this).parents('tr').remove();
		$(':input[name="cantidad_documentos"]').val($('table#table-content-files-rename tbody tr').length);
		validarSarlarf();
	});

	$('body').on('click', 'button#btn-cargar-renombramiento', function(event) {

		var requeridos = [
			'renombramiento_tipo_documento',
			'renombramiento_tipo_id',
			'renombramiento_numero_identificacion',
			'renombramiento_nombre_cliente',
			'renombramiento_fecha_actual',
			'renombramiento_anio_actual'
		];

		var datosCompletos = 0;

		$.each(requeridos, function(index, val) {
			if($(':input[name="'+val+'"]').val() != undefined && $(':input[name="'+val+'"]').val().length){
				requeridos = $.grep(requeridos, function(value) {
				  return value != val;
				});
			}else{
				alert('Debe diligenciar el campo ' + $('label[for="' + val + '"]').text() + ' como minimo para el renombramiento');
			}
		});

		if(!requeridos.length){

			var nameRenombramiento = new Array();
			var data = [
				'renombramiento_tipo_documento',
				'renombramiento_tipo_id',
				'renombramiento_numero_identificacion',
				'renombramiento_nombre_cliente',
				'renombramiento_fecha_actual',
				'renombramiento_anio_actual',
				'renombramiento_no_siniestro',
				'renombramiento_tipo_id_consorcio',
				'renombramiento_num_id_consorcio',
				'renombramiento_nombre_consorcio'
			];

			$.each(data, function(index, val) {

				if($(':input[name="'+val+'"]').val() != undefined && $(':input[name="'+val+'"]').val().length){

					if(val == 'renombramiento_tipo_documento'){

						nameRenombramiento.push($(':input[name="' + val + '"]').val());
					}else if(val == 'renombramiento_fecha_actual'){

						var date = getDateNow($(':input[name="' + val + '"]').val().replace(/\-/g, '/'));
						nameRenombramiento.push(date.dia + date.mes + date.año);
					}else{

						nameRenombramiento.push($(':input[name="'+val+'"]').val());
					}
				}
			});

			$('table#table-content-files-rename tbody').find('tr#'+$('input[id="pos_file_rename"]').val()+' th').find(':input[name="file_renombrado[' + $(':input[id="pos_file_rename"]').val() + ']"]').val(nameRenombramiento.join('-'));
			$('div#modal-renombramiento-file').modal('hide');
		}

		validarSarlarf();
	});

	$('body').on('click', 'button#save-new-client', function(event) {

		if($('select[name="tipo_documento"]').val().length){

			if($('input[name="documentClient"]').val().length){

				var Send = false;

				if($('select[name="tipo_documento"]').val() == 3 && $('input[name="documentClient"]').val().length > 8 || $('input[name="documentClient"]').val().length == 3){
					Send = true;
				}else if($('select[name="tipo_documento"]').val() != 3) {
					Send = true;
				}

				if(Send){

					var formNewClient = $('form[name="form-radicacion-new-cliente"]');
					var url = $(formNewClient).attr('action');
					var data = $(formNewClient).serializeArray();
					var method = $(formNewClient).attr('method');

					config_ajax = {

						beforeSend:function(){
							$("body").find('div#modal-radicacion div.modal-footer').html('<h4 class="text-left">Un momento se esta registrando el cliente...</h4>');
						},
						success:function(response){

							if(response.carga_cliente.resultado){

								$('div#modal-new-client').on('hidden.bs.modal', function () {
									
									AlertMessage(STATES_OK,'EXITO !!!','Se cargaron Correctamente Los Datos del Cliente.');

									//Ejecuta la funcion de buscar nueva mente el cliente ya radicado para generar la primera radicacion
									SearchRadicacion($('input[name="documentClient"]').val());
								}).modal('hide');
							}else{

								//ERROR CUANDO EL CLIENTE NO SE REGISTRO CORRECTAMENTE
								AlertMessage(STATES_ERROR,'ERROR !!!','el cliente no se pudo cargar en el sistema.');
							}
						}
					}

					SendData(url,data,method,config_ajax);
				}else{

					alert('El número de Identificación del cliente debe ser mayor a 10 digitos');
				}
			}else{
				alert('El numero del documento del cliente se encuentra vacio');
			}
		}else{
			alert('No se ha escogido el tipo de documento del cliente');
		}
	});
});

function validarSarlarf(){

	var cantFilesSAA = 0;
	$("#table-content-files-rename tbody tr").each(function(){
		var value = $(this).find("input[name='file_renombrado[" + $(this).attr("id") + "]']").val();
		var subs = value.substring(0, 3);
		if(subs == "FCC"){
			cantFilesSAA++;
		}
	});

	if(cantFilesSAA){
		if(!$("#formulario_sarlaft").is(':checked')){
			$("#formulario_sarlaft").trigger('click');
		}
	}else{
		if($("#formulario_sarlaft").is(':checked'))
			$("#formulario_sarlaft").trigger('click');
	}
}

/**
 * Esta funcion se encarga de abrir la ventana modal con el formulario para que el usuario genere el renombramiento del archivo que esta seleccionando
 * @param  "element" elemento seleccionado cuando se da click en el boton de editar
 * @param  "pos"  Posicion en la que se esta editando el archivo para renombrar
 */
function renombrarArchivo(element,pos){

	if($(element).parents('tr').children('th').eq(0).find(':input[type="file"]').val().length){

		if($('select[name="radicacion_proceso"]').val().length){

			$('div#modal-renombramiento-file').on('show.bs.modal',function(){

				$(this).off('show.bs.modal');
				$.post(base_url + 'radicacion/abreviadosByProceso', {radicacion_proceso : $('select[name="radicacion_proceso"]').val()}, function(result, textStatus, xhr) {

					if(textStatus == 'success'){
						$('select[name="renombramiento_tipo_documento"]').html(result);
						$('input[id="pos_file_rename"]').val(pos);
					}
				},'html');

			}).modal('show');
		}else{
			alert('Seleccione un proceso de radicacion');
		}
	}else{
		alert('Seleccione un archivo para renombrar');
	}
}

//Busca un cliente con radicacion dentro del sistema por el numero de documento
var SearchRadicacion = function(cliente){

	//Consulta el documento del cliente dentro del sistema
	$.ajax({
		url: base_url+'radicacion/searchRadicadoCliente',
		data: {documentClient: cliente},
		dataType:'html',
		success: function(response,status,xhr){

			if(xhr.getResponseHeader("content-type") == 'text/html; charset=UTF-8'){

				ListRadicacion(cliente);

	        	//Carga el formulario en el div de content
				$('.content').html(response);

				//Selecciona todo los disabled que halla al iniciar el formulario
				disabledFields = $(":disabled");

				
				//Solo si el cliente es diferente de viejo al cargarlo le quita los disabled
				if($('#ClienteRadicado').text() != 'Viejo'){
					disabledFields.prop('disabled',false);
				}

				//Al cargar el formulario le coloca disabled a la fecha de diligenciamiento y al check de sin fecha para activarla con el check de formulario sarlaft
			    $('input[name="fecha_diligenciamiento"]').prop('disabled', true);  
			    $('input[name="sin_fecha_diligenciamiento"]').prop('disabled', true);

			    //Funcion para que el listado tenga un buscador interno
			    $('select[name="renombramiento_tipo_documento"]').select2({
			    	theme: "bootstrap",
			        allowClear:true,
			        placeholder: 'Seleccione una opción',
			        width: '100%',
			        dropdownParent: $('div#modal-renombramiento-file')
			    });			    


				//Verifica si al momento que llega le formulario este se encuentra con observaciones la ultimavez 
				if($('input[name=devuelto][value=Si]').is(':checked')){
					$('input[name=check_observaciones]').prop('checked',true);
					checkboxEnabledField(true,'input[name=devuelto][value=Si]','textarea[name=radicacion_observacion]','hide');
				}else{
					$('input[name=check_observaciones]').prop('disabled',true);
					$('textarea[name=radicacion_observacion]').hide();
				}

				//Al dar click en un nuevo radicado se ejecuta y se limpia el formulario
				$('button#nuevo-radicado').on('click', function() {

					if(confirm("Esta seguro de crear una nueva radicación?")){

						$(this).addClass('hidden');

						$("#NRadicado").empty();
						$("#estado_cliente").empty();
						$("#ClienteRadicado")
							.text('Nuevo')
							.attr('class','label label-danger');

						resetForm('form-radicacion');
						disabledFields.prop('disabled', false);

						//Al cargar el formulario le coloca disabled a la fecha de diligenciamiento y al check de sin fecha para activarla con el check de formulario sarlaft
				        $('input[name="fecha_diligenciamiento"]').prop('disabled', true);  
				        $('input[name="sin_fecha_diligenciamiento"]').prop('disabled', true);
				        $('input[name="check_observaciones"]').prop('disabled', true);
				        $('textarea[name="radicacion_observacion"]').empty().hide();

						$('input[name="numero_identificacion"').val(cliente);
						$('div[id="cantidad_separado"').hide();
						$('div#table-renombramientos').removeClass('hidden');
					}
				});

				//Realiza la accion de verificacion del formulario
				$('button#verificar_radicacion').on('click', function(event) {
					
					var form = $('form[name="form-radicacion"]');

					if(form.find('table#table-content-files-rename tbody').children('tr').length){

						var renombramientos = {
							total_renombrados : 0,
							documentos_renombrados : new Array()
						};

						form.find('table#table-content-files-rename tbody').find(':input.renombramiento-file').each(function(index, el) {
							if(el.value.length){
								renombramientos['total_renombrados']++;
								renombramientos['documentos_renombrados'].push(el.value);
							}
						});

						if(form.find('table#table-content-files-rename tbody').children('tr').length === renombramientos['total_renombrados']){

							var html = "";

							//Valida el formulario antes de pasarlo
							if(ValidateForm(form)){

								var data = form.serializeArray();
								if(data.length){

									$('div#modal-radicacion').on('show.bs.modal', function(event) {

										var modal = $(this);
										form.find('small.help-error-block').empty();
										$.each(data, function(indexData, valData) {
											if(valData.value.length){
												if($(form).find('.categoria[data-label="'+valData.name+'"]').length){
													html += '<li>' + $(form).find('.categoria[data-label="'+valData.name+'"]').text() + ' : ' + valData.value + '</li>';
												}
											}
										});

										html += '<hr><h3>Documentos Renombrados</h3>';
										$.each(renombramientos['documentos_renombrados'], function(index, val) {
											html += '<li>' + val.toUpperCase() + '</li>';
										});

										modal.find('div.modal-content').find('div.modal-body').html('<ul>'+html+'</ul>');
									}).modal('show');
								}
							}else{

								$('div#modal-errores').on('show.bs.modal', function(event) {

									//Genera la ventana modal con la cantidad de los campos que encontro vacios
									$.each(VAR_ERROR, function(indexData, valData) {
										html += '<li>'+$('[data-label="'+valData.name+'"]').text()+'</li>';
									});
									$("#modal-errores .modal-content .lista_errores").html(html);
								}).modal('show');
							}
						}else{
							alert('No se ha completado de cargar los datos de un archivo, por favor verificaque');
						}
					}else{
						alert('No se ha cargado ningun archivo para esta radicacion');
					}
				});

				//Guarda el formulario de radicacion
				$('button#guardar_radicacion').on('click', function(event) {

					var form = $('form[name="form-radicacion"]');
					//Envio del la informacion
					$.ajax({
						url: $(form).attr('action'),
						method: $(form).attr('method'),
						data: new FormData(form[0]),
						processData : false,
						contentType: false,
						beforeSend : function(){
							$("#modal-radicacion").find('.modal-footer').html('<h4 class="pull-left"> Un momento se esta guardando la radicación </h4>');
						},
						success : function(response){

							if(response.length != 0 && response['radicacion'] != undefined){

								if(response['radicacion'].nuevo_cliente == true){

									if(confirm(response['radicacion'].message + " , Desea ir a realizar la captura de datos ?")){

										window.location.href = base_url + 'home?capture_client='+$("input[name='cliente_id']").val()
									}else{

										location.reload();
									}
								}else if(response['radicacion'].cliente_repetido == true){
									alert('Formulario repetido guardado con exito!!!');
									window.location.href = base_url + 'home?capture_client=' + $("input[name='cliente_id']").val()
								}
							}

							var countMessage = response.length;
							var configAlert = {
								afterHidden : function () {
									if(!countMessage){
										location.reload();
									}else{
										countMessage--;
									}
								}
							}

							$.each(response, function(indexResult, valResult) {
								AlertMessage(eval(valResult.type),valResult.titulo,valResult.message,configAlert);
							});
						},
						error : function(xhr){

							$("#modal-radicacion").find('.modal-footer').html('<h4 class="pull-left"> Error al guardar la radicación </h4>');
							AlertMessage(STATES_ERROR,'RESPUESTA CLIENTE ERROR!!!',xhr.responseText,{hideAfter: false});
                			console.log(xhr.responseText);
						},
						complete : function(xhr){

							$("#modal-radicacion").find('.modal-footer').html('<h4 class="pull-left"> ' + xhr.responseJSON['radicacion'].message + ' </h4>');
						}
					});	
				});	
			}else{

				//Parsea la respuesta para saber si no html
				var error = $.parseJSON(response);

				//Si al parcearlo envia error muestra la alerta de error
				if(error.type == 'error'){

					AlertMessage(STATES_ERROR,'ERROR !!!', error.message,{hideAfter : false});
					
					$('#nuevo-radicado').addClass('hidden');
					$('#estado_cliente').text('');
					$('#NRadicado').empty();
					$('#ClienteRadicado').empty();
					disabledFields.prop('disabled', 'true');
				}else if(error.type == 'warning'){

					//Confirma el registro del cliente
					if(confirm(error.message)){

						$.ajax({
							url: base_url + 'radicacion/saveClientNew',
							data: {tipo_documento: error.tipo_documento, documentClient: error.id},
							success:function(result){

								if(result.carga_cliente.resultado){
								
									AlertMessage(STATES_OK,'EXITO !!!','Se cargaron Correctamente Los Datos del Cliente.');

									//Ejecuta la funcion de buscar nueva mente el cliente ya radicado para generar la primera radicacion
									SearchRadicacion(error.id);
								}else{

									//ERROR CUANDO EL CLIENTE NO SE REGISTRO CORRECTAMENTE
									AlertMessage(STATES_ERROR,'ERROR !!!','el cliente no se pudo cargar en el sistema.');
								}
							},
							error: function(xhr){

								AlertMessage(STATES_ERROR,'RESPUESTA CLIENTE ERROR!!!',xhr.responseText,{hideAfter: false});
                				console.log(xhr.responseText);
							}
						});
					}else{

						//Si no registra el cliente limpia la vista
						$('#nuevo-radicado').addClass('hidden');
						$('#estado_cliente').text('');
						$('#NRadicado').empty();
						$('#ClienteRadicado').empty();

						//Deshabilita los campos que desde un pricipio estaban deshabilitados
						disabledFields.prop('disabled',true);
					}
				}else if(error.type == 'warning2'){ //Si el tipo de error es warning es porque el cliente existe pero no se a radicado la primera vez

					//Confirma el registro del cliente
					if(confirm(error.message)){

						$('div#modal-new-client').on('show.bs.modal',function(){
							
							$(this).off('show.bs.modal');
							$('input[name="documentClient"]').val($("input[name='numero_identificacion']").val());
							$('#nuevo-radicado').addClass('hidden');
							$('#estado_cliente').text('');
							$('#NRadicado').empty();
							$('#ClienteRadicado').empty();

							//Deshabilita los campos que desde un pricipio estaban deshabilitados
							disabledFields.prop('disabled',true);
						}).modal('show');
					}else{

						//Si no registra el cliente limpia la vista
						resetForm('form-radicacion');
						$('#nuevo-radicado').addClass('hidden');
						$('#table-renombramientos').addClass('hidden');
						$('#estado_cliente').text('');
						$('#NRadicado').empty();
						$('#ClienteRadicado').empty();

						//Deshabilita los campos que desde un pricipio estaban deshabilitados
						disabledFields.prop('disabled',true);
					}
				}
			}
		},
		error: function(xhr){

			AlertMessage(STATES_ERROR,'RESPUESTA CLIENTE ERROR!!!',xhr.responseText,{hideAfter: false});
			console.log(xhr.responseText);
		}
	});
};

ListRadicacion = function(cliente){

	$.ajax({
		url: base_url + "radicacion/getListRadicacion",
		type: 'POST',		
		data: {documento: cliente},
		success : function(response){
			if(!$.isEmptyObject(response.data)){
				if(response.columns.length > 0){
					var configTable = {
                            dom: 'Bfrtip',
                            filename: 'Radicaciones cliente ' + cliente,
                            buttons: [],
                            scrollX : true,
                            order: [7, 'desc']
                        };

                    CargarListadoTabla($('table#table-listado-radicaciones'), response.columns, response.data, configTable);
                    $(".gestion_radicacion").fadeIn(180);
                    $(".dataTables_scrollHeadInner").css({"width":"100%"});
				}
			}
		},
		error : function(xhr){
			AlertMessage(STATES_ERROR,'ERROR!!!', "No se ha podido visualizar las radicaciones del cliente." ,{hideAfter: false, stack : false});
			console.log(xhr);
		}
	});
}

// Modificar radicación seleccionada
function EditRadicacion(el,id){
	
	var doc = $($(el).parents("tr").children("td")[2]).html();
	var continuar = false;

	if(selected_radicacion){
		if(confirm("Tiene una radicación seleccionada, desea seleccionar otra?"))
			continuar = true;
	}else {
		continuar = true;
	}

	if(continuar){
		$.ajax({
			url: base_url+'radicacion/searchRadicadoById',
			data: {idRadicacion: id},
			dataType:'html',
			success: function(response,status,xhr){

				if(xhr.getResponseHeader("content-type") == 'text/html; charset=UTF-8'){

					ListRadicacion(doc);
					selected_radicacion = true;

		        	//Carga el formulario en el div de content
					$('.content').html(response);

					//Selecciona todo los disabled que halla al iniciar el formulario
					disabledFields = $(":disabled");
					disabledFields.prop('disabled', false);

					if($('select[name="medio_recepcion"]').val() != 'CORREO' ){
			    		$('input[name="correo_radicacion"]').parents('div.col-md-6').addClass('hidden');
			    		$('input[name="correo_radicacion"]').removeAttr('data-required');
			    	}else{
			    		$('input[name="correo_radicacion"]').parents('div.col-md-6').removeClass('hidden');
			    		$('input[name="correo_radicacion"]').attr('data-required',true);
			    	}
				    				
				
					//Al cargar el formulario le coloca disabled a la fecha de diligenciamiento y al check de sin fecha para activarla con el check de formulario sarlaft
				    if($('input[name="fecha_diligenciamiento"]').val().length){
				    	$('input[name="fecha_diligenciamiento"]').prop('disabled', false);  
				    	$('input[name="sin_fecha_diligenciamiento"]').prop('disabled', true);
				    }else{
				    	$('input[name="fecha_diligenciamiento"]').prop('disabled', true);  
				    	$('input[name="sin_fecha_diligenciamiento"]').prop('disabled', false);
				    }

				    // $("input[name='cantidad_documentos']").prop("disabled", false).removeAttr('readonly');


				    //Funcion para que el listado tenga un buscador interno
				    $('select[name="renombramiento_tipo_documento"]').select2({
				    	theme: "bootstrap",
				        allowClear:true,
				        placeholder: 'Seleccione una opción',
				        width: '100%',
				        dropdownParent: $('div#modal-renombramiento-file')
				    });

				    $('select[name="medio_recepcion"]').on('change',function(){
				    	if(this.value != 'CORREO' && this.value.length){
				    		$('input[name="correo_radicacion"]').parents('div.col-md-6').addClass('hidden');
				    		$('input[name="correo_radicacion"]').removeAttr('data-required');
				    	}else{
				    		$('input[name="correo_radicacion"]').parents('div.col-md-6').removeClass('hidden');
				    		$('input[name="correo_radicacion"]').attr('data-required',true);
				    	}
				    });


					//Verifica si al momento que llega le formulario este se encuentra con observaciones la ultimavez 
					if($('input[name=devuelto][value=Si]').is(':checked')){
						$('input[name=check_observaciones]').prop('checked',true);
						checkboxEnabledField(true,'input[name=devuelto][value=Si]','textarea[name=radicacion_observacion]','hide');
					}else{
						$('input[name=check_observaciones]').prop('disabled',true);
						$('textarea[name=radicacion_observacion]').hide();
					}

					//Al dar click en un nuevo radicado se ejecuta y se limpia el formulario
					$('button#nuevo-radicado').on('click', function() {

						if(confirm("Esta seguro de crear una nueva radicación?")){
							selected_radicacion = false;
							$(this).addClass('hidden');

							$("#NRadicado").empty();
							$("#estado_cliente").empty();
							$("#ClienteRadicado")
								.text('Nuevo')
								.attr('class','label label-danger');

							resetForm('form-radicacion');
							disabledFields.prop('disabled', false);

							//Al cargar el formulario le coloca disabled a la fecha de diligenciamiento y al check de sin fecha para activarla con el check de formulario sarlaft
					        $('input[name="fecha_diligenciamiento"]').prop('disabled', true);  
					        $('input[name="sin_fecha_diligenciamiento"]').prop('disabled', true);
					        $('input[name="check_observaciones"]').prop('disabled', true);
					        $('textarea[name="radicacion_observacion"]').empty().hide();

							$('input[name="numero_identificacion"').val(doc);
							$('div[id="cantidad_separado"').hide();
							$('div#table-renombramientos').removeClass('hidden');
						}
					});

					//Realiza la accion de verificacion del formulario
					$('button#verificar_radicacion').on('click', function(event) {
						
						var form = $('form[name="form-radicacion"]');

						if(form.find('table#table-content-files-rename tbody').children('tr').length || $("#radicacion_id").val().length){

							var renombramientos = {
								total_renombrados : 0,
								documentos_renombrados : new Array()
							};

							form.find('table#table-content-files-rename tbody').find(':input.renombramiento-file').each(function(index, el) {
								if(el.value.length){
									renombramientos['total_renombrados']++;
									renombramientos['documentos_renombrados'].push(el.value);
								}
							});

							if(form.find('table#table-content-files-rename tbody').children('tr').length === renombramientos['total_renombrados']){

								var html = "";

								//Valida el formulario antes de pasarlo
								if(ValidateForm(form)){

									var data = form.serializeArray();
									if(data.length){

										$('div#modal-radicacion').on('show.bs.modal', function(event) {

											var modal = $(this);
											form.find('small.help-error-block').empty();
											$.each(data, function(indexData, valData) {
												if(valData.value.length){
													if($(form).find('.categoria[data-label="'+valData.name+'"]').length){
														html += '<li>' + $(form).find('.categoria[data-label="'+valData.name+'"]').text() + ' : ' + valData.value + '</li>';
													}
												}
											});

											html += '<hr><h3>Documentos Renombrados</h3>';
											$.each(renombramientos['documentos_renombrados'], function(index, val) {
												html += '<li>' + val.toUpperCase() + '</li>';
											});

											modal.find('div.modal-content').find('div.modal-body').html('<ul>'+html+'</ul>');
										}).modal('show');
									}
								}else{

									$('div#modal-errores').on('show.bs.modal', function(event) {

										//Genera la ventana modal con la cantidad de los campos que encontro vacios
										$.each(VAR_ERROR, function(indexData, valData) {
											html += '<li>'+$('[data-label="'+valData.name+'"]').text()+'</li>';
										});
										$("#modal-errores .modal-content .lista_errores").html(html);
									}).modal('show');
								}
							}else{
								alert('No se ha completado de cargar los datos de un archivo, por favor verificaque');
							}
						}else{
							alert('No se ha cargado ningun archivo para esta radicacion');
						}
					});

					//Modificar el formulario de radicacion
					$('button#modificar_radicacion').on('click', function(event) {

						var form = $('form[name="form-radicacion"]');						
						//Envio del la informacion
						$.ajax({
							url: base_url + "radicacion/editRadicacion",
							method: $(form).attr('method'),
							data: new FormData(form[0]),
							processData : false,
							contentType: false,
							beforeSend : function(){
								$("#modal-radicacion").find('.modal-footer').html('<h4 class="pull-left"> Un momento se esta guardando la radicación </h4>');
							},
							success : function(response){
								if(response.length != 0 && response['radicacion'] != undefined){

									if(response['radicacion'].nuevo_cliente == true){

										if(confirm(response['radicacion'].message + " , Desea ir a realizar la captura de datos ?")){

											window.location.href = base_url + 'home?capture_client='+$("input[name='cliente_id']").val()
										}else{

											location.reload();
										}
									}else if(response['radicacion'].cliente_repetido == true){
										alert('Formulario repetido guardado con exito!!!');
										window.location.href = base_url + 'home?capture_client=' + $("input[name='cliente_id']").val()
									}
								}

								var countMessage = response.length;
								var configAlert = {
									afterHidden : function () {
										if(!countMessage){
											location.reload();
										}else{
											countMessage--;
										}
									}
								}

								$.each(response, function(indexResult, valResult) {
									AlertMessage(eval(valResult.type),valResult.titulo,valResult.message,configAlert);
								});
							},
							error : function(xhr){

								$("#modal-radicacion").find('.modal-footer').html('<h4 class="pull-left"> Error al guardar la radicación </h4>');
								AlertMessage(STATES_ERROR,'RESPUESTA CLIENTE ERROR!!!',xhr.responseText,{hideAfter: false});
	                			console.log(xhr.responseText);
							},
							complete : function(xhr){
								if(xhr.responseJSON['radicacion'].message  != undefined)
									$("#modal-radicacion").find('.modal-footer').html('<h4 class="pull-left"> ' + xhr.responseJSON['radicacion'].message + ' </h4>');
							}
						});	
					});
				}else{

					//Parsea la respuesta para saber si no html
					var error = $.parseJSON(response);

					//Si al parcearlo envia error muestra la alerta de error
					if(error.type == 'error'){

						AlertMessage(STATES_ERROR,'ERROR !!!', error.message,{hideAfter : false});
						
						$('#nuevo-radicado').addClass('hidden');
						$('#estado_cliente').text('');
						$('#NRadicado').empty();
						$('#ClienteRadicado').empty();
						disabledFields.prop('disabled', 'true');
					}else if(error.type == 'warning'){

						//Confirma el registro del cliente
						if(confirm(error.message)){

							$.ajax({
								url: base_url + 'radicacion/saveClientNew',
								data: {tipo_documento: error.tipo_documento, documentClient: error.id},
								success:function(result){

									if(result.carga_cliente.resultado){
									
										AlertMessage(STATES_OK,'EXITO !!!','Se cargaron Correctamente Los Datos del Cliente.');

										//Ejecuta la funcion de buscar nueva mente el cliente ya radicado para generar la primera radicacion
										SearchRadicacion(error.id);
									}else{

										//ERROR CUANDO EL CLIENTE NO SE REGISTRO CORRECTAMENTE
										AlertMessage(STATES_ERROR,'ERROR !!!','el cliente no se pudo cargar en el sistema.');
									}
								},
								error: function(xhr){

									AlertMessage(STATES_ERROR,'RESPUESTA CLIENTE ERROR!!!',xhr.responseText,{hideAfter: false});
	                				console.log(xhr.responseText);
								}
							});
						}else{

							//Si no registra el cliente limpia la vista
							$('#nuevo-radicado').addClass('hidden');
							$('#estado_cliente').text('');
							$('#NRadicado').empty();
							$('#ClienteRadicado').empty();

							//Deshabilita los campos que desde un pricipio estaban deshabilitados
							disabledFields.prop('disabled',true);
						}
					}else if(error.type == 'warning2'){ //Si el tipo de error es warning es porque el cliente existe pero no se a radicado la primera vez

						//Confirma el registro del cliente
						if(confirm(error.message)){

							$('div#modal-new-client').on('show.bs.modal',function(){
								
								$(this).off('show.bs.modal');
								$('input[name="documentClient"]').val($("input[name='numero_identificacion']").val());
								$('#nuevo-radicado').addClass('hidden');
								$('#estado_cliente').text('');
								$('#NRadicado').empty();
								$('#ClienteRadicado').empty();

								//Deshabilita los campos que desde un pricipio estaban deshabilitados
								disabledFields.prop('disabled',true);
							}).modal('show');
						}else{

							//Si no registra el cliente limpia la vista
							resetForm('form-radicacion');
							$('#nuevo-radicado').addClass('hidden');
							$('#table-renombramientos').addClass('hidden');
							$('#estado_cliente').text('');
							$('#NRadicado').empty();
							$('#ClienteRadicado').empty();

							//Deshabilita los campos que desde un pricipio estaban deshabilitados
							disabledFields.prop('disabled',true);
						}
					}
				}
			},
			error: function(xhr){
				AlertMessage(STATES_ERROR,'ERROR!!!',"Ha ocurrido un error al visualizar la radicación.",{hideAfter: false});
				console.log(xhr.responseText);
			}
		});
	}
}

function DeleteRadicacion(el,id){

	var row = $(el).parents("tr");
	var radicacionNumber = $(row.children("td")[0]).html();
	if(confirm("Está seguro que desea eliminar esta radicación?")){

		$.ajax({
			url: base_url + "radicacion/deleteRadicacion",
			data: {radicacionId: id},
			success : function(response){
				if(!response.error){
					row.remove();
					AlertMessage(STATES_OK,'EXITO!!!', "Se ha eliminado Correctamente la radicación No. " + radicacionNumber, {stack : false, hideAfter : 1500 ,
						afterHidden : function(){
							selected_radicacion = false;
							SearchRadicacion($("#numero_identificacion").val());
						}});
				}else{
					AlertMessage(STATES_ERROR,'ERROR!!!', "Ha ocurrido un error al eliminar la radicación No. " + radicacionNumber + ". ERROR: [" + response.message + "]", {hideAfter : false , stack : false});
				}
			},
			error : function(xhr){
				console.log(xhr);
				AlertMessage(STATES_ERROR,'ERROR!!!', "Ha ocurrido un error al eliminar la radicación No. " + radicacionNumber, {hideAfter : false , stack : false});
			}
		});
		
	}
}

//Funcion para eliminar clientes
function DeleteCliente(el, id){
	if(id != undefined){

		if(confirm("Está seguro de eliminar el cliente " + $("#numero_identificacion").val() + "?")){

			$.ajax({
				url: base_url + "radicacion/deleteCliente",
				data: {idCliente: id},
				success : function(response){
					if(response.error != undefined){
						AlertMessage(STATES_ERROR,'ERROR!!!', "Ha ocurrido un error al intentar eliminar el cliente. ERROR: [" + response.message + "]", {hideAfter : false , stack : false});	
					}else{
						$(el).prop("disabled", true);
						AlertMessage(STATES_OK,'EXITO!!!', response.message , {
							afterHidden : function(){
								location.reload();
							}, 
							stack : false}
						);	
						
					}
				},
				error : function(xhr){
					console.log(xhr);
					AlertMessage(STATES_ERROR,'ERROR!!!', "Ha ocurrido un error al intentar eliminar el cliente.", {hideAfter : false , stack : false});
				}
			});
		}
	}else{
		AlertMessage(STATES_ERROR,'ERROR!!!', "No se ha encontrado un cliente para eliminar. ", {hideAfter : false , stack : false});
	}
}