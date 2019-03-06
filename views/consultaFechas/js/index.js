$(document).ready(function(){
	$('form[name="formulario_consulta_clientes_sarlaft"]').on('submit',function(event){
		event.preventDefault();

		var config_ajax = {
	        beforeSend : function(){

	            $(this).find('button#btn-consultar').html('Consultando...').prop('disabled', true);
	        },
	        success : function(response){
	        	if(response.nombre_cliente != undefined)
	        		$("#client_name").fadeIn(150).find("input[name=nombre_cliente]").val(response.nombre_cliente);
	        	else	        		
	        		$("#client_name").fadeOut(0).find("input[name=nombre_cliente]").val("");
	        	
	        	if(response.tipo_documento != undefined)
	        		$("#tipo_documento").fadeIn(150).find("input[name=tipo_documento]").val(response.tipo_documento);
	        	else
	        		$("#tipo_documento").fadeOut(0).find("input[name=tipo_documento]").val("");


	            $(':input#ult_fecha_actualizacion').val(response.ultima_fecha_actualizacion);
	            $('table#table-respuesta').find('th#resppuesta-consulta').html(response.respuesta_consulta);
	            $('table#table-respuesta').show();
	        },
	        error : function(xhrs){
	            AlertMessage(STATES_ERROR, 'ERROR!!!', 'LA FUNCION NO DE DEVOLVIO LA RESPUESTA ESPERADA',{hideAfter:false, stack : false});
	        },
	        complete : function(){

	            $(this).find('button#btn-consultar').html('Consultar <span class="glyphicon glyphicon-search"></span>');
	        }
	    };

		SendData(base_url + 'consultaFechas/consulta_ult_estado',{ documentoCliente: $(':input[name="documento_cliente"]').val()},'POST',config_ajax);
	});
});