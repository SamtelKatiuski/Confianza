$(document).ready(function(){

    $('div.panel.section-form').each(function(index,el){
        if(!$(el).find(':input').length){
            $(el).hide();
        }
    });

    // variable para la funcionalidad de la habilitacion de los anexos y los accionistas
    var Anexos = {
        ppes: 0,
        accionistas : 0
    };

    /*===================================================================
    =            FUNCION DE AGREGAR SECCION DE ANEXO PPES O ACCIONISTAS =
    =====================================================================*/
    	
    	$('body').find('div#anexo_accionistas div.modal-body').find('table  tbody  select.add_anexos_accionistas').on('change', function(event) {
            
    		if(this.value == '3'){
                if($(this).attr('anexo_accionistas') == undefined){
                    $(this).attr('anexo_accionistas',true);
                    Anexos.accionistas++;
                }
            }else{
                if($(this).attr('anexo_accionistas') != undefined){
                    $(this).removeAttr('anexo_accionistas');
                    Anexos.accionistas--;
                }
    		}

    		if(Anexos.accionistas){
    			$('div#anexo_accionistas div.modal-body button#agregar_anexo_accionistas').show();
                $('input[type="hidden"][name="anexo_sub_accionistas"]').val(1);
    		}else{
    			$('div#anexo_accionistas div.modal-body button#agregar_anexo_accionistas').hide();
                $('input[type="hidden"][name="anexo_sub_accionistas"]').val(0);
    		}
    	});

        // Funcion para agregar anexo ppes de accionistas si se da SI en cualquier pregunta PEP O ACCIONISTA
        $('body').on('click','input[type="radio"].add_anexos_ppes_juridico',function(event){

            if($(this).val() == 1){
                $(this).attr('agrega_ppes',true);
                Anexos.ppes++;
            }else{
                $('input[type="radio"][name="'+$(this).attr('name')+'"][value="1"]').removeAttr('agrega_ppes');
                Anexos.ppes--;
            }

            if(Anexos.ppes){
                $('input[type="radio"][name="anexo_preguntas_ppes"][value="1"]').prop('checked',true);
                $('input[type="radio"][name="anexo_preguntas_ppes"][value="0"]').prop('checked',false).removeAttr('checked');
                checkboxEnabledField(true,this,'div#add_anexos_ppes_juridico','hide');
            }else{
                $('input[type="radio"][name="anexo_preguntas_ppes"][value="0"]').prop('checked',true);
                $('input[type="radio"][name="anexo_preguntas_ppes"][value="1"]').prop('checked',false).removeAttr('checked');
                checkboxEnabledField(false,this,'div#add_anexos_ppes_juridico','hide');
            }
        });

        /*---------- VALIDACIONES ANEXO PEPS  ----------*/

        $('body').on('click', 'button#btn-guardar-anexo-accionista', function(event){
	    	var errores = validarAnexo('accionistas');
	    	if(errores.length){
	    		alert('los campos del accionista numero ' + errores.join(',') + ' no se han llenado completamente por favor completarlos');
	    	}else{
	    		$('div#anexo_accionistas').modal('hide');
	    	}
	    });

	    $('body').on('click', 'div#anexo_accionistas div.modal-content div.modal-footer button.btn-danger', function(event) {
	    	var errores = validarAnexo('accionistas');
	    	if(errores.length){
	    		if(confirm('Desea continuar sin completar los campos ?')){
	    			$.each(errores,function(index,el){
	    				$('div#anexo_accionistas div.modal-body table > tbody > tr').eq((el-1)).find(':input').val('').removeAttr('value');
	    				$('div#anexo_accionistas div.modal-body table > tbody > tr').eq((el-1)).find(':input').prop('checked', false);
	    			});
	    			$('div#anexo_accionistas').modal('hide');
	    		}
	    	}else{
	    		$('div#anexo_accionistas').modal('hide');
	    	}
	    });

        /*---------- VALIDACIONES ANEXO PEPS  ----------*/

        $('body').on('click', 'div#anexo_preguntas_ppes div.modal-content div.modal-footer button.btn-danger', function(event) {
	    	var errores = validarAnexo('peps');
	    	if(errores.length){
	    		if(confirm('Desea continuar sin completar los campos ?')){
	    			$.each(errores,function(index,el){
	    				$('div#anexo_preguntas_ppes div.modal-body table > tbody > tr').eq((el-1)).find(':input').val('').removeAttr('value');
	    				$('div#anexo_preguntas_ppes div.modal-body table > tbody > tr').eq((el-1)).find(':input').prop('checked', false);
	    			});
	    			$('div#anexo_preguntas_ppes').modal('hide');
	    		}
	    	}else{
	    		$('div#anexo_preguntas_ppes').modal('hide');
	    	}
	    });

        $('body').on('click','button#btn-validar-anexo-peps',function(event){
        	var errores = validarAnexo('peps');
	    	if(errores.length){
	    		alert('los campos del anexo peps numero ' + errores.join(',') + ' no se han llenado completamente por favor completarlos');
	    	}else{
	    		$('div#anexo_preguntas_ppes').modal('hide');
	    	}
        });

        /*---------------   FIN VALIDACIONES --------------------------*/

        if($('div#anexo_accionistas div.modal-body table > tbody').find('select option[value="3"]:selected') != undefined){

		    if($('div#anexo_accionistas div.modal-body table > tbody').find('select option[value="3"]:selected').length){

		    	$('div#anexo_accionistas div.modal-body table > tbody').find('select option[value="3"]:selected').each(function(index,el){
		    		$(el).attr('add_sub_accionista',true);
		    		Anexos.accionistas++
		    	});

		    	$('div#anexo_accionistas div.modal-body').find('button#agregar_anexo_accionistas').show();
		    }
	    }

        if($('input[type="radio"][value="1"]:checked.add_anexos_ppes_juridico') != undefined){

            if($('input[type="radio"][value="1"]:checked.add_anexos_ppes_juridico').length){

                $('input[type="radio"][value="1"]:checked.add_anexos_ppes_juridico').each(function(index, el) {
                    $(el).attr('agrega_ppes',true);
                    Anexos.ppes++;
                });
            }

            if(Anexos.ppes){
                $('div#add_anexos_ppes_juridico').show();
            }
        }

    /*=====  End of FUNCION DE AGREGAR SECCION DE ANEXO PPES O ACCIONISTAS  ======*/

    if($('input[name="estado_form_id"]').val() == 6){
        if($('body').find('form[name="form-captura-persona-juridica"]').find('div[data-active-verificacion="false"]').length > 0){
            $('body').find('form[name="form-captura-persona-juridica"]').find('div[data-active-verificacion="false"]').each(function(index, el) {
                $(el).prop('hidden',true);
            });
        }
    }
    
    if(!$.inArray(eval($('input[name="estado_form_id"]').val()),[5,6,9]) != -1){
        // configuracion de las biñetas seguimiento de la captura de datos
        $('body').on('click', 'ol#tags-proceso-form-juridico li', function(event) {            
            event.preventDefault();
            data_active = event.currentTarget
            if($.inArray(eval($('input[name="estado_form_id"]').val()),[5,6]) != -1){
                $('strong[id="estado_form_sarlaft"]').text(data_active.dataset.formJuridica);
                $('input[name="estado_form_id"]').val(data_active.dataset.formProceso);
                $('body').find('button.btn-guardar').html('Guardar ' + data_active.dataset.formJuridica);
            }

            if(data_active.dataset.formJuridica == 'verificacion'){
                $('body').find('form[name="form-captura-persona-juridica"]').find('div[data-active-verificacion="false"]').each(function(index, el) {
                    $(el).attr('data-active-verificacion',true);
                    $(el).removeAttr('hidden');
                });
            }else if(data_active.dataset.formJuridica == 'completitud'){
                $('body').find('form[name="form-captura-persona-juridica"]').find('div[data-active-verificacion="true"]').each(function(index, el) {
                    $(el).attr('data-active-verificacion',false);
                    $(el).prop('hidden',true);
                });
            }else{
                $('body').find('form[name="form-captura-persona-juridica"]').find('div[data-active-verificacion="false"]').each(function(index, el) {
                    $(el).removeAttr('hidden');
                });
            }
        });
    }

    $(".modal-advertencia .confirmation-button").on("click", function(){
        $(".modal-advertencia").fadeOut(250);
    });

    //Verifica el estado de la tipologia para poder adicional o no la observación de esta
    $('body').on('change','select[name="estado_tipologia"]', function(event) {
        if($(this).val() == 10){
            $('div#observaciones_tipologias').fadeIn(150);
        }else{
            $('div#observaciones_tipologias').fadeOut(300);
        }
    });

    $('button#btn-guardar-formulario').on('click', function(event) {
        // Inicializa la variables de la captura del formulario

        var form = $('form[name="form-captura-persona-juridica"]');
        var formName = form.name
debugger;
        if($.inArray(eval($(form).find('input[name="estado_form_id"]').val()),[1,2,4,13,11,3,15,16,9,14]) != -1){
debugger;
            GuardarFormularioCaptura(form);
        }else if($.inArray(eval($(form).find('input[name="estado_form_id"]').val()),[6,5]) != -1){
debugger;
            if($('select[name="llamada_cliente_sarlaft"]').val() != 'MODIFICACION'){
                    GuardarFormularioCompletitud_Verificacion(form);
            }else{
                GuardarFormulario(form);
            }
        }else{
            debugger;
            alert("Error");
        }
    });

    $('body').find(".select2").select2({
        allowClear:true,
        placeholder: 'Seleccione una opción'
    });

    /*==============================================================
    =            Section CONFIGURACION CIIU AUTOCOMPLETAR            =
    ================================================================*/

	    if($('body').find('select[name="ofi_principal_ciiu"]').val() != undefined || $('body').find('input[name="ofi_principal_ciiu_cod"]').val() != undefined){
	        if($('body').find('select[name="ofi_principal_ciiu"]').val().length){
	            $('body').find('input[name="ofi_principal_ciiu_cod"]').val($('body').find('select[name="ofi_principal_ciiu"]').val());
	        }else if($('body').find('input[name="ofi_principal_ciiu_cod"]').val().length){
	            $('body').find('select[name="ofi_principal_ciiu"]').val($('body').find('input[name="ofi_principal_ciiu_cod"]').val()).trigger('change');
	        }
	    }

	    $('input[name="ofi_principal_ciiu_cod"]').on('change', function(event) {
	        $('select[name="ofi_principal_ciiu"]').val(event.currentTarget.value).trigger('change');
	    });

	    $('select[name="ofi_principal_ciiu"]').on('change', function(event) {
	        $('input[name="ofi_principal_ciiu_cod"]').val(event.currentTarget.value);
	        if(event.currentTarget.value == 117){
	            $('div#ofi_principal_ciiu_cod_otro').fadeIn(150);
	        }else{
	            $('div#ofi_principal_ciiu_cod_otro').fadeOut(150);
	            $('input[name="ofi_principal_ciiu_otro"]').val('');
	        }
	    });

	/*=====  End of Section CONFIGURACION CIIU AUTOCOMPLETAR  ======*/

    $('div.panel.section-form').each(function(index,el){
        if(!$(el).find(':input').length){
            $(el).hide();
        }
    });

    $('body').on('click', 'button#guardar-formulario-captura', function() {
        $('div#modal-juridico').modal('hide');
        GuardarFormulario($('form[name="form-captura-persona-juridica"]'));
    });

    if($.inArray($('body').find('input[type="hidden"][name="estado_form_id"]').val(),[6]) != -1){
        $('body').find('[completitud="false"]').each(function(index,el){
            $(el).hide();
        });
    }else if($.inArray($('body').find('input[type="hidden"][name="estado_form_id"]').val(),[5]) != -1){
        $('body').find('[verificacion="false"]').each(function(index,el){
            $(el).hide();
        });
    }

    $('body').on('change','select[name="llamada_cliente_sarlaft"]',function(event){

        if(this.value.length){                    
            if(this.value != "MODIFICACION"){
                $('div#numero_llamada_inbound').removeClass('hidden');
                if(this.value == 'OUTBOUND'){
                    $('select[name="estado_tipologia"] > option[value="8"]').prop('selected','selected');
                }
            }else{
                $('div#numero_llamada_inbound').addClass('hidden');
                $('input[name="numero_llamada_inbound"]').val('');
                $('select[name="estado_tipologia"] > option[value="11"]').prop('selected','selected');
            }
            $("#btn-guardar-formulario").prop("disabled",false);
        }else{
            $('div#numero_llamada_inbound').addClass('hidden');
            $('input[name="numero_llamada_inbound"]').val('');
            $("#btn-guardar-formulario").prop("disabled",true);
        }
    });
});

function validarAnexo(anexo){

    var errores = new Array();
    if(anexo == 'accionistas'){

        $('div#anexo_accionistas div.modal-body table > tbody > tr').each(function(index, el) {

            var con = 0;
            $(el).find("input").not("[type=radio]").each(function(){
                if($(this).val().length)
                    con++;
            });
            if(con || $(el).find(':input#accionista_tipo_documento_'+ (index+1)).val().length){

                if(!$(el).find(':input#accionista_tipo_documento_'+ (index+1)).val() ||
                    !$(el).find(':input#accionista_documento_' + (index+1)).val().length ||
                    !$(el).find(':input#accionista_nombres_completos_' + (index+1)).val().length ) {
                    
                    errores.push((index+1));
                }
            }
        });

        return errores;
    }else if(anexo == 'peps'){

        $('div#anexo_preguntas_ppes div.modal-body table > tbody > tr').each(function(index, el) {

            var con = 0;
            $(el).find("input").each(function(){
                if($(this).val().length)
                    con++;
            });

            if(con || $(el).find(":input#anexo_ppes_tipo_identificacion_" + (index+1)).val() != "" ){

                if(!$(el).find(':input#anexo_ppes_tipo_identificacion_' + (index+1)).val().length ||
                    !$(el).find(':input#anexo_ppes_nombre_' + (index+1)).val().length ||
                    !$(el).find(':input#anexo_ppes_no_documento_' + (index+1)).val().length){

                    errores.push((index+1));
                }
            }
        });

        return errores;
    }
}

// Verifica el formulario de captura antes de guardarlo
function GuardarFormularioCaptura(form){
	if (!ValidateForm(form)){

	    if(VAR_ERROR.length > 0){

	        var html = '';

	        $('div#modal-juridico').on('show.bs.modal',function(){

                $(this).off('show.bs.modal');
	            $(this).find('.modal-content').find('.modal-title').text('ALGUNOS CAMPOS ESTAN VACIOS ESTA DE ACUERDO ?')
	            $(this).find('.modal-content').find('.modal-body').html('<div class="container-fluid"><ul style="list-style-type: circle;">');

	            $.each(VAR_ERROR, function(indexItem, valItem) {
	                if($('label[for="' + valItem.name + '"]').length){
	                    html += '<li style="line-height: 1.5;">'+$('label[for="' + valItem.name + '"]').text().replace(':','');
	                }
	            });

	            $('div#modal-juridico').find('.modal-content').find('.modal-body ul').html(html);
	            $('div#modal-juridico').find('.modal-content').find('.modal-footer').find('button.btn-guardar').html('Guardar <span class="glyphicon glyphicon-save"></span>').attr('id','guardar-formulario-captura');
	        }).modal({keyboard: false, backdrop: 'static', show:true});
	    }
	}else {
        
        GuardarFormulario(form);
    }
}

// Verifica el formulario de completitud antes de guardarlo
function GuardarFormularioCompletitud_Verificacion(form){

    $('div#modal-juridico').find('.modal-content').find('.modal-footer').find('button.btn-guardar').addClass('hidden');
    if($('body').find('input[type="checkbox"][value="DOCUMENTAL"]').is(':checked') && $('body').find('input[type="checkbox"][value="TELEFONICA"]').is(':checked')){
        
        if($('body').find('select[name="estado_tipologia"]').val().length){

            if($('body').find('select[name="estado_tipologia"]').val() != 8){

                if(!ValidateForm(form)){

                    if(VAR_ERROR.length > 0){

                        var html = '';

                        $('div#modal-juridico').on('show.bs.modal',function(){

                            $(this).off('show.bs.modal');
                            $(this).find('.modal-content').find('.modal-title').text('ALGUNOS CAMPOS ESTAN VACIOS ESTA DE ACUERDO ?')
                            $(this).find('.modal-content').find('.modal-body').html('<div class="container-fluid"><ul style="list-style-type: circle;">');

                            $.each(VAR_ERROR, function(indexItem, valItem) {
                                if($('label[for="' + valItem.name + '"]').length){
                                    html += '<li style="line-height: 1.5;">'+$('label[for="' + valItem.name + '"]').text().replace(':','');
                                }
                            });

                            $(this).find('.modal-content').find('.modal-body ul').html(html);
                            $(this).find('.modal-content').find('.modal-footer').find('button.btn-guardar').html('Guardar <span class="glyphicon glyphicon-save"></span>').attr('id','guardar-formulario-captura').removeClass('hidden');
                        }).modal({keyboard: false, backdrop: 'static', show:true});
                    }
                }else{
                    GuardarFormulario(form);
                }
            }else{

                if(!ValidateForm(form)){

                    if(VAR_ERROR.length > 0){

                        var html = '';

                        $('div#modal-juridico').find('.modal-content').find('.modal-title').text('ALGUNOS CAMPOS ESTAN VACIO DEBE COMPLETARLOS ?');
                        $('div#modal-juridico').find('.modal-content').find('.modal-body').html('<div class="container-fluid"><ul style="list-style-type: circle;">');
                            $.each(VAR_ERROR, function(indexItem, valItem) {
                                if($('label[for="' + valItem.name + '"]').length){
                                    html += '<li style="line-height: 1.5;">'+$('label[for="' + valItem.name + '"]').text().replace(':','');
                                }
                            });
                        $('div#modal-juridico').find('.modal-content').find('.modal-body ul').html(html);
                        $('div#modal-juridico').modal('show');
                    }
                }else{

                    // if($(':input[name="anexo_preguntas_ppes"]').val() == 1){

                    //     var dataEmptyAnexo = 0;
                    //     $('div#anexo_preguntas_ppes').find('.modal-content .modal-body table#table_ppes_pe_publica').find('tbody').find(':input[data-anexo-required="true"]').each(function(index,el){
                    //         if(!el.value.length){
                    //             dataEmptyAnexo++;
                    //         }
                    //     });
                    //     if(dataEmptyAnexo == 21){
                    //         if(confirm("El anexo PEPS se encuentra vacio, esta de acuerdo ?")){
                    //             $('div#anexo_preguntas_ppes').modal('show');
                    //         }else{
                    //             $('input.anexo_ppes[value="1"]:checked').prop('checked', false);
                    //             $('input[name="anexo_preguntas_ppes"]').val(0);
                    //             $('#btn_anexo_preguntas_ppes').hide();
                    //         }
                    //     }
                    // }else{
                        GuardarFormulario(form);
                    // }
                }
            }
        }else{
            alert("Seleccion un estado de la tipologia");
        }
    }else if($('body').find('input[type="checkbox"][value="DOCUMENTAL"]').is(':checked')){

        if(!ValidateForm(form)){

            if(VAR_ERROR.length > 0){

                var html = '';

                $('div#modal-juridico').on('show.bs.modal',function(){

                    $(this).off('show.bs.modal');
                    $(this).find('.modal-content').find('.modal-title').text('ALGUNOS CAMPOS ESTAN VACIO DEBE COMPLETARLOS ?')
                    $(this).find('.modal-content').find('.modal-body').html('<div class="container-fluid"><ul style="list-style-type: circle;">');

                    $.each(VAR_ERROR, function(indexItem, valItem) {
                        if($('label[for="' + valItem.name + '"]').length){
                            html += '<li style="line-height: 1.5;">'+$('label[for="' + valItem.name + '"]').text().replace(':','');
                        }
                    });

                    $(this).find('.modal-content').find('.modal-body ul').html(html);
                }).modal({keyboard: false, backdrop: 'static', show:true});
            }
        }else{
            

            // if($(':input[name="anexo_preguntas_ppes"]').val() == 1){

            //     var dataEmptyAnexo = 0;
            //     $('div#anexo_preguntas_ppes').find('.modal-content .modal-body table#table_ppes_pe_publica').find('tbody').find(':input[data-anexo-required="true"]').each(function(index,el){
            //         if(!el.value.length){
            //             dataEmptyAnexo++;
            //         }
            //     });
            //     if(dataEmptyAnexo == 21){
            //         if(confirm("El anexo PEPS se encuentra vacio, esta de acuerdo ?")){
            //             $('div#anexo_preguntas_ppes').modal('show');
            //         }else{
            //             $('input.anexo_ppes[value="1"]:checked').prop('checked', false);
            //             $('input[name="anexo_preguntas_ppes"]').val(0);
            //             $('#btn_anexo_preguntas_ppes').hide();
            //         }
            //     }
            // }else{
                GuardarFormulario(form);
            // }
        }
    }else if($('body').find('input[type="checkbox"][value="TELEFONICA"]').is(':checked')){

        if($('body').find('select[name="estado_tipologia"]').val().length){

            if($('body').find('select[name="estado_tipologia"]').val() != 8){

                if(!ValidateForm(form)){

                	if(VAR_ERROR.length > 0){

				        var html = '';
                        
                        $('div#modal-juridico').on('show.bs.modal',function(){

                            $(this).off('show.bs.modal');
                            $(this).find('.modal-content').find('.modal-title').text('ALGUNOS CAMPOS ESTAN VACIOS ESTA DE ACUERDO ?')
                            $(this).find('.modal-content').find('.modal-body').html('<div class="container-fluid"><ul style="list-style-type: circle;">');

                            $.each(VAR_ERROR, function(indexItem, valItem) {
                                if($('label[for="' + valItem.name + '"]').length){
                                    html += '<li style="line-height: 1.5;">'+$('label[for="' + valItem.name + '"]').text().replace(':','');
                                }
                            });

                            $(this).find('.modal-content').find('.modal-body ul').html(html);
                            $(this).find('.modal-content').find('.modal-footer').find('button.btn-guardar').html('Guardar <span class="glyphicon glyphicon-save"></span>').attr('id','guardar-formulario-captura').removeClass('hidden');
                        }).modal({keyboard: false, backdrop: 'static', show:true});
				    }
                }else{
                    GuardarFormulario(form);
                }
            }else{


            	if(!ValidateForm(form)){

	            	if(VAR_ERROR.length > 0){

				        var html = '';

				        $('div#modal-juridico').on('show.bs.modal',function(){

				            $(this).find('.modal-content').find('.modal-title').text('ALGUNOS CAMPOS ESTAN VACIOS ESTA DE ACUERDO ?')
				            $(this).find('.modal-content').find('.modal-body').html('<div class="container-fluid"><ul style="list-style-type: circle;">');

				            $.each(VAR_ERROR, function(indexItem, valItem) {
				                if($('label[for="' + valItem.name + '"]').length){
				                    html += '<li style="line-height: 1.5;">'+$('label[for="' + valItem.name + '"]').text().replace(':','');
				                }
				            });

				            $(this).find('.modal-content').find('.modal-body ul').html(html);
				        }).modal({keyboard: false, backdrop: 'static', show:true});
				    }
				}else{
                	GuardarFormulario(form);
				}
            }
        }else{
            alert("Seleccion un estado de la tipologia");
        }
    }else{
        alert('Seleccione una tipologia');
    }
}

// Guarda el formulario en la base de datos
function GuardarFormulario(form){
    var url = base_url + 'home/saveCaptura';
    var dataObject = $(form).serializeArray(); 

    var config_ajax = {

        beforeSend : function(){

            $(form).find('button.btn-guardar').html('Guardando...').prop('disabled', true);
        },
        success : function(response){

            var total_alerts = response.length;
            
            $.each(response, function(indexResult, valResult) {
                                
                var configAlert = {
                    afterHidden: function(){

                        total_alerts--;
                        if(total_alerts == 0){
                            window.location.reload();
                        }
                    }
                }

                if(eval(valResult.type) == STATES_ERROR){
                    configAlert['hideAfter'] = false;
                }

                AlertMessage(eval(valResult.type),valResult.title,valResult.message,configAlert);
            });
        },
        error : function(xhrs){
            console.log(xhrs);
            AlertMessage(STATES_ERROR, 'ERROR!!!', 'LA FUNCION NO DE DEVOLVIO LA RESPUESTA ESPERADA',{hideAfter:false});
        },
        complete : function(){

            $(form).find('button.btn-guardar').html('Guardado <span class="glyphicon glyphicon-ok"></span>').prop('disabled',false);
        }
    };

    SendData(url,dataObject,'POST',config_ajax);
}
//Funcion para eliminar accionista
function DeleteAccionista(el, id, documento){
	if(id != undefined){

		if(confirm("Está seguro de eliminar el Accionista " + $("#"+documento).val() + "?")){

			$.ajax({
				url: base_url + "home/deleteAccionista",
				data: {idAccionista: id},
				success : function(response){
					if(response.error != undefined){
						AlertMessage(STATES_ERROR,'ERROR!!!', "Ha ocurrido un error al intentar eliminar el Accionista. ERROR: [" + response.message + "]", {hideAfter : false , stack : false});	
					}else{
						$(el).parents("tr").remove();
						AlertMessage(STATES_OK,'EXITO!!!', response.message , {
							afterHidden : function(){
								//location.reload();
							}, 
							stack : false}
						);	
						
					}
				},
				error : function(xhr){
					console.log(xhr);
					AlertMessage(STATES_ERROR,'ERROR!!!', "Ha ocurrido un error al intentar eliminar el Accionista.", {hideAfter : false , stack : false});
				}
			});
		}
	}else{
		AlertMessage(STATES_ERROR,'ERROR!!!', "No se ha encontrado un cliente para eliminar. ", {hideAfter : false , stack : false});
	}
}