var SEND_MAIL = [];
var CANTIDAD_PENDIENTES = 0;
var SELECTED_ALL = false;
$(document).ready(function() {

	// carga la informacion de los pendientes en la tabla de pendientes
	$("form[name=formulario_consulta_pendientes]").on("submit", function(e){
		e.preventDefault();

		var form = $(this);
		$(".help-error-block").html("");
		
		if(ValidateForm(form)){			
			$.ajax({
				url: base_url + 'pendientes/getDataTableClientesPendientes',
				type: 'POST',
				data: form.serializeArray(),
				success : function(resultadoTablaPendientes){
					$("button#sendMailPendings").find("#button_text").html("Enviar correo.");
					$("button#sendMailPendings").find(".glyphicon").removeClass('glyphicon-ok').addClass('glyphicon-envelope');
					if(!$.isEmptyObject(resultadoTablaPendientes)){
						CANTIDAD_PENDIENTES = (resultadoTablaPendientes.data != undefined ? resultadoTablaPendientes.data.length : 0);

						$("#totalPendientes").html(CANTIDAD_PENDIENTES);
						$("#cantSeleccionados").html("0");

						$("#sendMailContainer, #container-pendings").removeClass("hidden");

						var changePage = false;

						var configTable = {
				            dom: 'Bfrtip',
				            filename: 'Listado_Pedientes_'+getDateNow().dia+getDateNow().mes+getDateNow().año,
				            buttons: [
				            	{
				            		text : "Seleccionar todos.",
				            		action : function(e, dt, node, config){
			            				if(!SELECTED_ALL){
					            			$("table#table-pendientes tbody tr td").each(function(){
				            					$(this).find(":input[type=checkbox]").prop("checked", true).trigger("change");
				            				});				            				
				            				SELECTED_ALL = true;
			            				}else {
			            					$("table#table-pendientes tbody tr td").each(function(){
				            					$(this).find(":input[type=checkbox]").prop("checked", false).trigger("change");
				            				});
			            					SELECTED_ALL = false;
			            				}
				            		}
				            	},
				            	{
				            		extend: 'excelHtml5',
                                    title: 'Listado_Pedientes_'+getDateNow().dia+getDateNow().mes+getDateNow().año
				            	}
				            ],
				            fnDrawCallback : function(e){
				            	if(e.iDraw != changePage){
				            		changePage = e.iDraw;
				            		SELECTED_ALL = false;
				            	}
				            },
				            scrollX : true,
				            destroy : true
				        };

						CargarListadoTabla($('.content-list-pendientes').find('table[name="table-pendientes"]'),resultadoTablaPendientes.columns,resultadoTablaPendientes.data,configTable);

						$(".dataTables_scrollHeadInner").css({"width":"100%"});
					}else{
						$("#sendMailContainer").addClass("hidden");
				        $("#liberar-clientes-pendientes").addClass('hidden');
						$('.content-list-pendientes').html('<div class="alert alert-danger" role="alert"> No existen registros pendientes </div>');
					}
				},
				error : function(xhr){
					console.log(xhr.responseText);
					$("#sendMailContainer").addClass("hidden");
					$("#liberar-clientes-pendientes").addClass('hidden');
					$('.content-list-pendientes').html('<div class="alert alert-danger" role="alert"> Ha ocurrido un error al obtener la lista de pendientes. </div>');
				}
			});
		}else{

			$.each(VAR_ERROR, function(i, el) {
				$(el).parents(".fields-container").children(".help-error-block").html("Complete este campo.");
			});
		}
	});
	
	
	//Enviar correo de pendientes seleccionados
	$("button#sendMailPendings").on("click", function(){
		if(confirm("Está seguro de enviar los correos de los clientes seleccionados?")){
			$.ajax({
				url: base_url + 'pendientes/sendMailPendientes',
				type: 'POST',
				data: {listIdPendientes: SEND_MAIL},
				beforeSend : function(){
					$("button#sendMailPendings").prop("disabled", true).find("#button_text").html("Enviando correo...");
				},
				success : function(response){
					AlertMessage(eval(response.type), response.titulo, response.message, { hideAfter : 2500, stack : false} );
				},
				error : function(xhr){
					AlertMessage(STATES_ERROR, "ERROR!!!", "Ha ocurrido un error al enviar el correo. Error: [" + xhr.responseText + "]", { hideAfter:false , stack : false} );
				},
				complete : function(){
					$("button#sendMailPendings").find("#button_text").html("Correo enviado");
					$("button#sendMailPendings").find(".glyphicon").removeClass('glyphicon-envelope').addClass('glyphicon-ok');
				}
			});
			
		}
	});
	
});

function CheckToSendMail(el){
	var id = $(el).val() + "-" + $(el).attr("proceso-id");

	if($(el).is(":checked")){
		if($.inArray(id, SEND_MAIL) === -1){
			SEND_MAIL.push(id);
		}
	}else{
		if($.inArray(id, SEND_MAIL) !== -1){
			var pos = SEND_MAIL.indexOf(id);
			if(pos != -1){
				SEND_MAIL.splice(pos , 1);
			}
		}
	}

	$("#cantSeleccionados").html(SEND_MAIL.length);

	if(SEND_MAIL.length)
		$("#sendMailPendings").removeAttr("disabled")
	else
		$("#sendMailPendings").prop("disabled", true)
}