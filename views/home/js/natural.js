$(document).ready(function(){    

    $('div.panel.section-form').each(function(index,el){
        if(!$(el).find(':input').length){
            $(el).hide();
        }
    });

    var AnexosPEP = 0;

    // Muestra el boton de anexos ppe para la captura de datos en el formulario de captura
    $('body').on('click', '.anexo_ppes', function(event) {
        if($(this).val() != "SI" && $(':input[type="radio"][name="'+$(this).attr('name')+'"][value="SI"]').attr('add_anexos_ppe') != undefined){

            $('input[type="radio"][name="'+$(this).attr('name')+'"][value="SI"]').removeAttr('add_anexos_ppe');

            if(AnexosPEP){
                AnexosPEP--;
            }
        }else if($('input[type="radio"][name="'+$(this).attr('name')+'"][value="SI"]').attr('add_anexos_ppe') == undefined && $(this).val() == "SI"){

            $(this).attr('add_anexos_ppe',true);
            AnexosPEP++;
        }

        if(AnexosPEP){
            $("div#btn_anexo_preguntas_ppes").fadeIn(300);
            $(':input[type="hidden"][name="anexo_preguntas_ppes"]').val(1);
        }else{
            $("div#btn_anexo_preguntas_ppes").fadeOut(300);
            $(':input[type="hidden"][name="anexo_preguntas_ppes"]').val(0);
        }
    });

    /*---------- VALIDACIONES ANEXO PEPS  ----------*/

        $('body').on('click', 'div#anexo_preguntas_ppes div.modal-content div.modal-footer button.btn-danger', function(event) {
            var errores = validarAnexo('peps');
            if(!errores.length){
                if(confirm('¿Desea continuar sin completar los campos? Todos los campos se vaciarán')){
                    $.each($('div#anexo_preguntas_ppes div.modal-body table > tbody > tr').find('select'),function(index,el){
                        $(el).val('');
                    });
                    $.each($('div#anexo_preguntas_ppes div.modal-body table > tbody > tr').find('input[type="text"]'),function(index,el){
                        $(el).val('').removeAttr('value');
                    });
                    $.each($('div#anexo_preguntas_ppes div.modal-body table > tbody > tr').find('input[type="date"]'),function(index,el){
                        $(el).val('').removeAttr('value');
                    });
                    $.each($('div#anexo_preguntas_ppes div.modal-body table > tbody > tr').find('input[type="radio"]'),function(index,el){
                        $(el).val('').prop('checked', false);
                    });
                    $('div#anexo_preguntas_ppes').modal('hide');
                }
            }else{
                $.each($('div#anexo_preguntas_ppes div.modal-body table > tbody > tr').find('select'),function(index,el){
                    $(el).val('');
                });
                $.each($('div#anexo_preguntas_ppes div.modal-body table > tbody > tr').find('input[type="text"]'),function(index,el){
                    $(el).val('').removeAttr('value');
                });
                $.each($('div#anexo_preguntas_ppes div.modal-body table > tbody > tr').find('input[type="date"]'),function(index,el){
                    $(el).val('').removeAttr('value');
                });
                $.each($('div#anexo_preguntas_ppes div.modal-body table > tbody > tr').find('input[type="radio"]'),function(index,el){
                    $(el).val('').prop('checked', false);
                });
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

    // verifica el estado de la tipologia para poder adicional o no la observación de esta
    $('body').on('change','select[name="estado_tipologia"]', function(event) {

        if($(this).val() == 10){
            $('div#observaciones_tipologias').fadeIn(150);
        }else{
            $('div#observaciones_tipologias').fadeOut(300);
        }
    });

    //Cargar ciudades por departamento
    $('select#departamento_residencia').on("change", function(){
        loadCities(this, $("select#ciudad_residencia"));
    });

    // Carga las ciudades en las empresas
    $('select#departamento_empresa').on("change", function(){
        loadCities(this, $("select#ciudad_empresa"));
    });
    
    if($('input[name="estado_form_id"]').val() == 6){
        if($('body').find('form[name="form-captura-persona-natural"]').find('div[data-active-verificacion="false"]').length > 0){
            $('body').find('form[name="form-captura-persona-natural"]').find('div[data-active-verificacion="false"]').each(function(index, el) {
                $(el).prop('hidden',true);
            });
        }
    }
        
    // configuracion de las biñetas seguimiento de la captura de datos
    $('ol#tags-proceso-form-natural li').on('click',function(event) {
        event.preventDefault();
        data_active = $($(this)[0]).data('formNatural');

        $('strong[id="estado_form_sarlaft"]').text(data_active);
        if(data_active == 'verificacion'){

            if($(this).parent('ol').find('.no-completitud').length == 0){
                $('input[type="hidden"][name="estado_form_id"]').val('6,5');
            }

            $('body').find('form[name="form-captura-persona-natural"]').find('div[data-active-verificacion="false"]').each(function(index, el) {
                $(el).attr('data-active-verificacion',true);
                $(el).removeAttr('hidden');
            });
        }else{
            
            $('input[type="hidden"][name="estado_form_id"]').val(6);
            $('body').find('form[name="form-captura-persona-natural"]').find('div[data-active-verificacion="true"]').each(function(index, el) {
                $(el).attr('data-active-verificacion',false);
                $(el).prop('hidden',true);
            });
        }
    });

    // configuracion trabaja actualmente
    var required_datos_empresa_natural;
    $('input[name="trabaja_actualmente"]').on('click',function(event) {
        if($(this).attr('value') == 0){
            required_datos_empresa_natural = $('div#datos_empresa_natural').find('label.required-field');
            required_datos_empresa_natural.removeClass('required-field');
        }else{
            $('div#datos_empresa_natural').css("display","block");
            if(required_datos_empresa_natural.length){
                required_datos_empresa_natural.addClass('required-field');
            }
        }
    });

    if($('input[type="radio"][value=1]:checked.anexo_ppes').length > 0){
        $('input[type="radio"][value=1]:checked.anexo_ppes').each(function(index, el) {
            $(el).attr('add_anexos_ppe',true);
            AnexosPEP++;
        });
        $('input[type="hidden"][name="anexo_preguntas_ppes"]').val(1);
    }

    // Primero Valida el envio de un formulario lanzandole la alerta de campos vacios
    $('button#btn-guardar-formulario').on('click',function(event) {

        // Inicializa la variables de la captura del formulario
        var form = $('form[name="form-captura-persona-natural"]');
        var formName = form.name

        if($.inArray(eval($(form).find('input[name="estado_form_id"]').val()),[1,3,4,13,11,15,16,14]) != -1){

            GuardarFormularioCaptura(form);
        }else if($.inArray(eval($(form).find('input[name="estado_form_id"]').val()),[6,5]) != -1){

            if($('select[name="llamada_cliente_sarlaft"]').val() != 'MODIFICACION'){
                    GuardarFormularioCompletitud_Verificacion(form);
            }else{
                GuardarFormulario(form);
            }
        }
    });

    $('body').find(".select2").select2({
        allowClear:true,
        placeholder: 'Seleccione una opción'
    });

    /*==============================================================
    =            Section CONFIGURACION CIIU AUTOCOMPLETAR            =
    ================================================================*/
    
        if($('body').find('select[name="actividad_eco_principal"]').val() != undefined && $('body').find('select[name="actividad_eco_principal"]').val().length){
            $('body').find('input[name="ciiu_cod"]').val($('body').find('select[name="actividad_eco_principal"]').val());
        }else if($('body').find('input[name="ciiu_cod"]').val() != undefined && $('body').find('input[name="ciiu_cod"]').val().length){
            $('body').find('select[name="actividad_eco_principal"]').val($('body').find('input[name="ciiu_cod"]').val()).trigger('change');
        }

        if($('body').find('select[name="actividad_secundaria"]').val() != undefined && $('body').find('select[name="actividad_secundaria"]').val().length){
            $('body').find('input[name="ciiu_secundario"]').val($('body').find('select[name="actividad_secundaria"]').val());
        }else if($('body').find('input[name="ciiu_secundario"]').val() != undefined && $('body').find('input[name="ciiu_secundario"]').val().length){
            $('body').find('select[name="actividad_secundaria"]').val($('body').find('input[name="ciiu_secundario"]').val()).trigger('change');
        }

        $('input[name="ciiu_cod"]').on('change', function(event) {
            $('select[name="actividad_eco_principal"]').val(event.currentTarget.value).trigger('change');
        });

        $('select[name="actividad_eco_principal"]').on('change', function(event) {
            $('input[name="ciiu_cod"]').val(event.currentTarget.value);
            if(event.currentTarget.value == 2222){
                $('div#actividad_eco_principal_otra').fadeIn(150);
            }else{
                $('div#actividad_eco_principal_otra').fadeOut(150);
                $('input[name="actividad_eco_principal_otra"]').val('');
            }
        });
    
        $('input[name="ciiu_secundario"]').on('change', function(event) {
            $('select[name="actividad_secundaria"]').val(event.currentTarget.value).trigger('change');
        });

        $('select[name="actividad_secundaria"]').on('change', function(event) {
            $('input[name="ciiu_secundario"]').val(event.currentTarget.value);
        });

    /*=====  End of Section CONFIGURACION CIIU AUTOCOMPLETAR  ======*/

    $('body').on('click', 'button#guardar-formulario-captura', function() {
        $('div#modal-natural').modal('hide');
        GuardarFormulario($('form[name="form-captura-persona-natural"]'));
    });

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
    if(anexo == 'peps'){

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

            $('div#modal-natural').on('show.bs.modal',function(){

                $(this).off('show.bs.modal');
                $(this).find('.modal-content').find('.modal-title').text('ALGUNOS CAMPOS ESTAN VACIOS ESTA DE ACUERDO ?')
                $(this).find('.modal-content').find('.modal-body').html('<div class="container-fluid"><ul style="list-style-type: circle;">');

                $.each(VAR_ERROR, function(indexItem, valItem) {
                    if(valItem.type == 'checkbox'){
                        html += '<li style="line-height: 1.5;">'+$('label[for="' + $(valItem).attr('id') + '"]').text().replace(':','');
                    }else if($('label[for="' + $(valItem).attr('name') + '"]').length){
                        html += '<li style="line-height: 1.5;">'+$('label[for="' + $(valItem).attr('name') + '"]').text().replace(':','');
                    }
                });

                $(this).find('.modal-content').find('.modal-body ul').html(html);
                $(this).find('.modal-content').find('.modal-footer').find('button.btn-guardar').html('Guardar <span class="glyphicon glyphicon-save"></span>').attr('id','guardar-formulario-captura');
            }).modal('show');
        }
    }else {
        
        GuardarFormulario(form);
    }
}

// Verifica el formulario de completitud antes de guardarlo
function GuardarFormularioCompletitud_Verificacion(form){

    $('div#modal-natural').find('.modal-content').find('.modal-footer').find('button.btn-guardar').addClass('hidden');

    if($('body').find('input[type="checkbox"][value="TELEFONICA"]').is(':checked')){


        if($('body').find('select[name="estado_tipologia"]').val().length){

            if($('body').find('select[name="estado_tipologia"]').val() == 8){

                if(!ValidateForm(form)){

                    if(VAR_ERROR.length > 0){
    
                        var html = '';

                        $('div#modal-natural').on('show.bs.modal',function(){

                            $(this).off('show.bs.modal');
                            $(this).find('.modal-content').find('.modal-title').text('ALGUNOS CAMPOS ESTAN VACIO DEBE COMPLETARLOS ?')
                            $(this).find('.modal-content').find('.modal-body').html('<div class="container-fluid"><ul style="list-style-type: circle;">');

                            $.each(VAR_ERROR, function(indexItem, valItem) {
                                if($('label[for="' + $(valItem).attr('name') + '"]').length){
                                    html += '<li style="line-height: 1.5;">'+$('label[for="' + $(valItem).attr('name') + '"]').text().replace(':','');
                                }
                            });

                            $(this).find('.modal-content').find('.modal-body ul').html(html);
                        }).modal('show');
                    }
                }else{
                    GuardarFormulario(form);
                }
            }else{

                if(!ValidateForm(form)){

                    if(VAR_ERROR.length > 0){

                        var html = '';

                        $('div#modal-natural').on('show.bs.modal',function(){

                            $(this).find('.modal-content').find('.modal-title').text('ALGUNOS CAMPOS ESTAN VACIOS ESTA DE ACUERDO ?')
                            $(this).find('.modal-content').find('.modal-body').html('<div class="container-fluid"><ul style="list-style-type: circle;">');

                            $.each(VAR_ERROR, function(indexItem, valItem) {
                                if($('label[for="' + $(valItem).attr('name') + '"]').length){
                                    html += '<li style="line-height: 1.5;">'+$('label[for="' + $(valItem).attr('name') + '"]').text().replace(':','');
                                }
                            });

                            $('div#modal-natural').find('.modal-content').find('.modal-body ul').html(html);
                            $('div#modal-natural').find('.modal-content').find('.modal-footer').find('button.btn-guardar').html('Guardar <span class="glyphicon glyphicon-save"></span>').attr('id','guardar-formulario-captura');
                        }).modal('show');
                    }
                }else{

                   // if($('input[name="anexo_preguntas_ppes"]').val() == 1){

                   //      var dataEmptyAnexo = 0;
                   //      $('div#anexo_preguntas_ppes').find('.modal-content .modal-body table#table_ppes_pe_publica').find('tbody').find(':input[data-anexo-required="true"]').each(function(index,el){
                   //          if(!el.value.length){
                   //              dataEmptyAnexo++;
                   //          }
                   //      });
                        
                   //      if(dataEmptyAnexo == 21){
                   //          if(confirm("El anexo PEPS se encuentra vacio, esta de acuerdo ?")){
                   //              $('div#anexo_preguntas_ppes').modal('show');
                   //          }else{
                   //              $('input.anexo_ppes[value="1"]:checked').prop('checked', false);
                   //              $('input[name="anexo_preguntas_ppes"]').val(0);
                   //              $('#btn_anexo_preguntas_ppes').hide();
                   //          }
                   //      }
                   //  }else{
                        GuardarFormulario(form);
                    // }
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

    if(confirm('Esta seguro de que la informacion esta completamente diligenciada ?')){
        
        var url = 'home/saveCaptura';
        var dataObject = $(form).serializeArray(); 
        var method = 'POST';

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

        SendData(url,dataObject,method,config_ajax);
    }
}
