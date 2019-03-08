$(document).ready(function(){

    $('select[name="opcion_reporte"]').on('change',function(event){
        if($.inArray(event.target.value,['reporte_cruce_clientes_faltantes','reporte_cruce_clientes_sobrantes']) != -1){
            $('div#archivo_cruce_zurich').removeClass('hidden');
            $('div#filtro_fechas').addClass('hidden');
        }else if($.inArray(event.target.value,['reporte_pendientes' ,'reporte_facturacion' ,'reporte_capturas' ,'reporte_capturas_natural' ,'reporte_capturas_juridico', 'reporte_actualizacion_documentos']) != -1){
            $('div#filtro_fechas').removeClass('hidden');
            $('div#archivo_cruce_zurich').addClass('hidden');

            $('input#fecha_inicio').val("");
            $('input#fecha_fin').val("");
        }else{
            $('div#archivo_cruce_zurich, div#filtro_fechas').addClass('hidden');
            $('input[name="archivo_cruce_zurich"]').val('');
        }
    });

    $('form[name="form-reportes-generales"]').on('submit', function(event) {

        event.preventDefault();
        var Send = false;
        var form = $(event.target);

        if(form.find('select[name="opcion_reporte"]').val().length){
            if($.inArray(form.find('select[name="opcion_reporte"]').val(),['reporte_cruce_clientes_faltantes','reporte_cruce_clientes_sobrantes']) != -1){
                if(form.find('input[name="archivo_cruce_zurich"]').val().length){
                    Send = true;
                }else{
                    alert('No se ha cargado ningun archivo');
                }
            }else if($.inArray(form.find('select[name="opcion_reporte"]').val(),['reporte_pendientes' ,'reporte_facturacion' ,'reporte_capturas' ,'reporte_capturas_natural' ,'reporte_capturas_juridico', 'reporte_actualizacion_documentos']) != -1){
                if(($('input#fecha_inicio').val() == '' || $('input#fecha_fin').val() == '' )){
                    alert("No se han seleccionado fechas de rango de reporte.")
                }else {
                    Send = true;
                }
            }else{
                Send = true;
            }
        }else{
            alert("Seleccione una opcion del listado");
        }


        if(Send){

            var url = $(form).attr('action');
            var method = $(form).attr('method');
            var enctype = $(form).attr('enctype');
            var dataObject = new FormData(form[0]);

            var configSend = {
                processData : false,
                contentType: false,
                beforeSend : function(){
                    form.find('button[id="subir_archivo_migracion"]').html('Cargando...').prop('disabled', true);
                },
                success : function(response){
                    if(response.data != undefined){
                        $('table[name="tabla-reportes-generales"]').show(0);
                        var configDataGrid = {
                            height: 550,
                            dataSource : response.data,
                            columns: response.columns,
                            export : {
                                enabled : true,
                                allowExportSelectedData: true,
                                fileName: response.fileName
                            },
                            destroy : true
                        };

                        CargarListadoTablaDataGrid($('table[name="tabla-reportes-generales"]'),configDataGrid);
                    }else{
                        AlertMessage(STATES_ERROR, "Error en visualización de reporte", "No se ha podido generar el reporte seleccionado");
                        $('table[name="tabla-reportes-generales"]').hide(0);
                    }

                },
                error : function(xhr){
                    console.log(xhr.responseText);
                    AlertMessage(STATES_ERROR, 'ERROR!!!', 'LA FUNCION NO DE DEVOLVIO LA RESPUESTA ESPERADA',{hideAfter:false , loaderBg: '#6b3737'});
                },
                complete : function(){
                    form.find('button[id="subir_archivo_migracion"]').html('Completado ').prop('disabled', false).attr('type', 'button');
                    form.find('button[id="reload_page"]').removeAttr('hidden').attr('type', 'button').removeClass('hidden');
                }
            }

            SendData(url,dataObject,method,configSend);
        }
    });
});
