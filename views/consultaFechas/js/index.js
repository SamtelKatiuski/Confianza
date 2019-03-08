$(document).ready(function () {
	$('form[name="formulario_consulta_clientes_sarlaft"]').on('submit', function (event) {
		event.preventDefault();

		var config_ajax = {
			beforeSend: function () {
				$(this).find('button#btn-consultar').html('Consultando...').prop('disabled', true);
			},
			success: function (response) {
				if (response.nombre_cliente != undefined)
					$("#client_name").fadeIn(150).find("input[name=nombre_cliente]").val(response.nombre_cliente);
				else
					$("#client_name").fadeOut(0).find("input[name=nombre_cliente]").val("");

				if (response.tipo_documento != undefined)
					$("#tipo_documento").fadeIn(150).find("input[name=tipo_documento]").val(response.tipo_documento);
				else
					$("#tipo_documento").fadeOut(0).find("input[name=tipo_documento]").val("");

				if (response.utima_fecha_expedicion_docs["RUT"]) {
					var nombre = response.utima_fecha_expedicion_docs["RUT"][0];
					var arrayFecha = nombre.split('-');
					$("#div_ult_fecha_expedicion_rut").fadeIn(150).find("input[id=ult_fecha_expedicion_rut]").val(arrayFecha[2] + '/' + arrayFecha[1]);
				} else {
					$("#div_ult_fecha_expedicion_rut").fadeOut(0).find("input[id=ult_fecha_expedicion_rut]").val("");
				}

				if (response.utima_fecha_expedicion_docs["CCO"]) {
					var nombre = response.utima_fecha_expedicion_docs["CCO"][0];
					var arrayFecha = nombre.split('-');
					$("#div_ult_fecha_expedicion_cco").fadeIn(150).find("input[id=ult_fecha_expedicion_cco]").val(arrayFecha[2] + '/' + arrayFecha[1]);
				} else {
					$("#div_ult_fecha_expedicion_cco").fadeOut(0).find("input[id=ult_fecha_expedicion_cco]").val("");
				}

				if (response.utima_fecha_expedicion_docs["DDC"]) {
					var nombre = response.utima_fecha_expedicion_docs["DDC"][0];
					var arrayFecha = nombre.split('-');
					$("#div_ult_fecha_expedicion_ddc").fadeIn(150).find("input[id=ult_fecha_expedicion_ddc]").val(arrayFecha[2] + '/' + arrayFecha[1]);
				} else {
					$("#div_ult_fecha_expedicion_ddc").fadeOut(0).find("input[id=ult_fecha_expedicion_ddc]").val("");
				}

				if (response.utima_fecha_expedicion_docs["ACC"]) {
					var nombre = response.utima_fecha_expedicion_docs["ACC"][0];
					var arrayFecha = nombre.split('-');
					$("#div_ult_fecha_expedicion_acc").fadeIn(150).find("input[id=ult_fecha_expedicion_acc]").val(arrayFecha[2] + '/' + arrayFecha[1]);
				} else {
					$("#div_ult_fecha_expedicion_acc").fadeOut(0).find("input[id=ult_fecha_expedicion_acc]").val("");
				}

				if (response.utima_fecha_expedicion_docs["EFC"]) {
					var nombre = response.utima_fecha_expedicion_docs["EFC"][0];
					var arrayFecha = nombre.split('-');
					$("#div_ult_fecha_expedicion_efc").fadeIn(150).find("input[id=ult_fecha_expedicion_efc]").val(arrayFecha[2] + '/' + arrayFecha[1]);
				} else {
					$("#div_ult_fecha_expedicion_efc").fadeOut(0).find("input[id=ult_fecha_expedicion_efc]").val("");
				}

				if (response.utima_fecha_expedicion_docs["EFI"]) {
					var nombre = response.utima_fecha_expedicion_docs["EFI"][0];
					var arrayFecha = nombre.split('-');
					$("#div_ult_fecha_expedicion_efi").fadeIn(150).find("input[id=ult_fecha_expedicion_efi]").val(arrayFecha[2] + '/' + arrayFecha[1]);
				} else {
					$("#div_ult_fecha_expedicion_efi").fadeOut(0).find("input[id=ult_fecha_expedicion_efi]").val("");
				}

				if (response.utima_fecha_expedicion_docs["NEF"]) {
					var nombre = response.utima_fecha_expedicion_docs["NEF"][0];
					var arrayFecha = nombre.split('-');
					$("#div_ult_fecha_expedicion_nef").fadeIn(150).find("input[id=ult_fecha_expedicion_nef]").val(arrayFecha[2] + '/' + arrayFecha[1]);
				} else {
					$("#div_ult_fecha_expedicion_nef").fadeOut(0).find("input[id=ult_fecha_expedicion_nef]").val("");
				}

				if (response.utima_fecha_expedicion_docs["RTA"]) {
					var nombre = response.utima_fecha_expedicion_docs["RTA"][0];
					var arrayFecha = nombre.split('-');
					$("#div_ult_fecha_expedicion_rta").fadeIn(150).find("input[id=ult_fecha_expedicion_rta]").val(arrayFecha[2]);
				} else {
					$("#div_ult_fecha_expedicion_rta").fadeOut(0).find("input[id=ult_fecha_expedicion_rta]").val("");
				}

				if (response.utima_fecha_expedicion_docs["RET"]) {
					var nombre = response.utima_fecha_expedicion_docs["RET"][0];
					var arrayFecha = nombre.split('-');
					$("#div_ult_fecha_expedicion_ret").fadeIn(150).find("input[id=ult_fecha_expedicion_ret]").val(arrayFecha[2]);
				} else {
					$("#div_ult_fecha_expedicion_ret").fadeOut(0).find("input[id=ult_fecha_expedicion_ret]").val("");
				}

				$(':input#ult_fecha_actualizacion').val(response.ultima_fecha_actualizacion);
				$('table#table-respuesta').find('th#resppuesta-consulta').html(response.respuesta_consulta);
				$('table#table-respuesta').show();
			},
			error: function (xhrs) {
				AlertMessage(STATES_ERROR, 'ERROR!!!', 'LA FUNCION NO DE DEVOLVIO LA RESPUESTA ESPERADA', {
					hideAfter: false,
					stack: false
				});
			},
			complete: function () {

				$(this).find('button#btn-consultar').html('Consultar <span class="glyphicon glyphicon-search"></span>');
			}
		};

		SendData(base_url + 'consultaFechas/consulta_ult_estado', {
			documentoCliente: $(':input[name="documento_cliente"]').val()
		}, 'POST', config_ajax);
	});
});