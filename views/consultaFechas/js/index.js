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
					
				if(response.tipo_documento != undefined)
	        		$("#div_ult_fecha_expedicion_rut").fadeIn(150).find("input[id=ult_fecha_expedicion_rut]").val(response.utima_fecha_expedicion_docs["RUT"]);
	        	else
					$("#div_ult_fecha_expedicion_rut").fadeOut(0).find("input[id=ult_fecha_expedicion_rut]").val("");
					
				if(response.tipo_documento != undefined)
	        		$("#div_ult_fecha_expedicion_cco").fadeIn(150).find("input[id=ult_fecha_expedicion_cco]").val(response.utima_fecha_expedicion_docs["CCO"]);
	        	else
					$("#div_ult_fecha_expedicion_cco").fadeOut(0).find("input[id=ult_fecha_expedicion_cco]").val("");
					
				if(response.tipo_documento != undefined)
	        		$("#div_ult_fecha_expedicion_ddc").fadeIn(150).find("input[id=ult_fecha_expedicion_ddc]").val(response.utima_fecha_expedicion_docs["DDC"]);
	        	else
	        		$("#div_ult_fecha_expedicion_ddc").fadeOut(0).find("input[id=ult_fecha_expedicion_ddc]").val("");

				if(response.tipo_documento != undefined)
	        		$("#div_ult_fecha_expedicion_acc").fadeIn(150).find("input[id=ult_fecha_expedicion_acc]").val(response.utima_fecha_expedicion_docs["ACC"]);
	        	else
	        		$("#div_ult_fecha_expedicion_acc").fadeOut(0).find("input[id=ult_fecha_expedicion_acc]").val("");

				if(response.tipo_documento != undefined)
	        		$("#div_ult_fecha_expedicion_efc").fadeIn(150).find("input[id=ult_fecha_expedicion_efc]").val(response.utima_fecha_expedicion_docs["EFC"]);
	        	else
					$("#div_ult_fecha_expedicion_efc").fadeOut(0).find("input[id=ult_fecha_expedicion_efc]").val("");
					
				if(response.tipo_documento != undefined)
	        		$("#div_ult_fecha_expedicion_efi").fadeIn(150).find("input[id=ult_fecha_expedicion_efi]").val(response.utima_fecha_expedicion_docs["EFI"]);
	        	else
					$("#div_ult_fecha_expedicion_efi").fadeOut(0).find("input[id=ult_fecha_expedicion_efi]").val("");
					
				if(response.tipo_documento != undefined)
	        		$("#div_ult_fecha_expedicion_nef").fadeIn(150).find("input[id=ult_fecha_expedicion_nef]").val(response.utima_fecha_expedicion_docs["NEF"]);
	        	else
					$("#div_ult_fecha_expedicion_nef").fadeOut(0).find("input[id=ult_fecha_expedicion_nef]").val("");
					
				if(response.tipo_documento != undefined)
	        		$("#div_ult_fecha_expedicion_rta").fadeIn(150).find("input[id=ult_fecha_expedicion_rta]").val(response.utima_fecha_expedicion_docs["RTA"]);
	        	else
					$("#div_ult_fecha_expedicion_rta").fadeOut(0).find("input[id=ult_fecha_expedicion_rta]").val("");
					
				if(response.tipo_documento != undefined)
	        		$("#div_ult_fecha_expedicion_ret").fadeIn(150).find("input[id=ult_fecha_expedicion_ret]").val(response.utima_fecha_expedicion_docs["RET"]);
	        	else
	        		$("#div_ult_fecha_expedicion_ret").fadeOut(0).find("input[id=ult_fecha_expedicion_ret]").val("");

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