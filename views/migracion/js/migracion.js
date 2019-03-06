$(document).ready(function(){
	
	$('select[name="tipo_migracion"]').on('change',function(event){
		if(event.target.value.length){
			
			if(event.target.value == 'captura'){

				$('div#archivo_migracion').removeClass('hidden');
				$('div#opc_migracion_captura').removeClass('hidden');

			}else{

				$('select[name="opc_migracion_captura"]').val('');
				$('div#opc_migracion_captura').addClass('hidden');

				if (event.target.value == 'liberacionpendientesmigracion'){

					$('div#archivo_migracion').addClass('hidden');
					$('input[name="archivo_migracion"]').val('');
				}else{

					$('div#archivo_migracion').removeClass('hidden');
				}
			}
			
			$('button[id="subir_archivo_migracion"]').removeAttr('disabled').removeClass('disabled');
		}
	});

	$('body').on('submit', function(event){
		event.preventDefault();
		var form = $(event.target);

		if(form.find('select[name="tipo_migracion"]').val().length){

			if(form.find('select[name="tipo_migracion"]').val() != 'liberacionpendientesmigracion'){
				if(form.find('input[name="archivo_migracion"]').val().length){
					
					var url = $(form).attr('action'); 
					var method = $(form).attr('method'); 
					var enctype = $(form).attr('enctype');
					var dataObject = new FormData(form[0]);
					
					var config_ajax = {

						processData : false,
						contentType: false,
						beforeSend : function(){
							form.find('button[id="subir_archivo_migracion"]').html('Cargando...').prop('disabled', true);
						},
						success : function(response){
							console.log(response);
							$.each(response, function(indexResult, valResult) {
								AlertMessage(eval(valResult.type),valResult.titulo,valResult.message,{hideAfter:false, loaderBg: '#6b3737'});
							});
						},
						error : function(xhr){
							console.log(xhr);
							AlertMessage(STATES_ERROR, 'ERROR!!!', 'LA FUNCION NO DE DEVOLVIO LA RESPUESTA ESPERADA',{hideAfter:false , loaderBg: '#6b3737'});
						},
						complete : function(){
							form.find('button[id="subir_archivo_migracion"]').html('Completado ').prop('disabled', false).attr('type', 'button');
							form.find('button[id="reload_page"]').removeAttr('hidden').attr('type', 'button').removeClass('hidden');
						}
					};	

					SendData(url,dataObject,method,config_ajax);				
				}else{
					alert("no se ha cargado un archivo para continuar el proceso");
				}
			}else{

				var url = base_url + 'migracion/liberacionPendientesMigracion'; 
				var method = 'POST'; 
				var dataObject = form.serializeArray();
				
				var config_ajax = {
					async: true,
					beforeSend : function(){
						form.find('button[id="subir_archivo_migracion"]').html('Cargando...').prop('disabled', true);
					},
					success : function(response){
						console.log(response);
						$.each(response, function(indexResult, valResult) {
							AlertMessage(eval(valResult.type),valResult.titulo,valResult.message,{hideAfter:false, loaderBg: '#6b3737'});
						});
					},
					error : function(xhr){
						console.log(xhr);
						AlertMessage(STATES_ERROR, 'ERROR!!!', 'LA FUNCION NO DE DEVOLVIO LA RESPUESTA ESPERADA',{hideAfter:false , loaderBg: '#6b3737'});
					},
					complete : function(){
						form.find('button[id="subir_archivo_migracion"]').html('Completado ').prop('disabled', false).attr('type', 'button');
						form.find('button[id="reload_page"]').removeAttr('hidden').attr('type', 'button').removeClass('hidden');
					}
				};	

				SendData(url,dataObject,method,config_ajax);
			}
		}else{
			alert("Por favor especificar el tipo de migracion");
		}
	});
});