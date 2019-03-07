$(document).ready(function(){

    //captura la informacion que llega por URL (cliente)
    if($.urlParam('capture_client')){
            
        var cliente = $.urlParam('capture_client');

        //Verifica que el Id del cliente sea numerico
        if($.isNumeric(cliente)){
            loadForm(cliente);
        }
    }

    // Busca el cliente con el numero de documento
    $(':input[name="SearchClienteDocument"]').on('keypress', function(event) {

        // detecta la tecla enter solo en el campo de numero de identificacion
        if(event.which == 13){

            if(this.value.length){

                $.ajax({
                    url: base_url+"home/searchClient",
                    type: 'POST',
                    data: {documento : this.value},
                    success : function(resultadoIDdocumento){
                        if(resultadoIDdocumento.id != undefined){

                            window.location.href = base_url + "home?capture_client=" + resultadoIDdocumento.id;
                        }else{
                            
                            AlertMessage(STATES_ERROR,'ERROR','El cliente no existe en el sistema',{loaderBg: '#6b3737'});
                        }
                    }, 
                    error : function(xhr){

                        console.log(xhr.responseJSON);
                    }
                });
                
            }else{

                AlertMessage(STATES_ERROR,'ERROR!!!','El campo no puede esta vacio para la consulta');
            }
        }
    });

    // Busca el cliente con el numero de documento
    $('span#btn-SearchClienteDocument').on('click', function(event) {

        var no_identificacion = event.currentTarget.parentElement.firstElementChild;
        if(no_identificacion.value.length){

            $.ajax({
                url: base_url+"home/searchClient",
                type: 'POST',
                data: {documento : no_identificacion.value},
                success : function(resultadoIDdocumento){
                    if(resultadoIDdocumento.id != undefined){

                        window.location.href = base_url + "home?capture_client=" + resultadoIDdocumento.id;
                    }else{
                        
                        AlertMessage(STATES_ERROR,'ERROR','El cliente no existe en el sistema',{loaderBg: '#6b3737'});
                    }
                }, 
                error : function(xhr){

                    console.log(xhr.responseJSON);
                }
            });
            
            
        }else{

            AlertMessage(STATES_ERROR,'ERROR','El campo no puede esta vacio para la consulta',{loaderBg: '#6b3737'});
        }
    });

    // Muestra el listado de clientes
    $("a#list-alt").on('click', function(event) {

        $(".panels-content").each(function(){
            if($(this).attr("id") != "table-pendientes")
                $(this).hide(0);            
        });

        if(!$('div#table-pendientes').is(':visible')){

            $.ajax({
                url: base_url + 'home/tablePendientesFuncionarios',
                success:function(resultadoTable){
                    
                    if(!$.isEmptyObject(resultadoTable)){                        

                        if(resultadoTable.columns.length > 0){

                            var configTable = {
                                dom: 'Bfrtip',
                                filename: 'Listado_Captura_'+getDateNow().dia+getDateNow().mes+getDateNow().año,
                                buttons: [{
                                    extend: 'excelHtml5',
                                    title: 'Listado_Captura_'+getDateNow().dia+getDateNow().mes+getDateNow().año
                                }]
                            };

                            CargarListadoTabla($('div#table-pendientes > table#table-content'), resultadoTable.columns, resultadoTable.data,configTable);
                            $('#table-pendientes').fadeIn(200);
                            
                        }
                    }else{
                        alert("no hay clientes para visualizar");
                    }
                },
                error:function(xhr){

                    AlertMessage(STATES_ERROR,'RESPUESTA CLIENTE ERROR!!!',xhr.responseText,{hideAfter: false});
                    console.log(xhr.responseText);
                }
            });
        }
    });

    // Carga el cliente dependiendo del tipo de persona que se le envie
    $('a#ReloadClient').on('click', function(event) {

        //Previene la recarga de la pagina al hacer click 
        event.preventDefault();

        var config_ajax = {
            url: base_url + 'home/LoadClientRamdon',
            beforeSend: function(xhr){
                // console.log('Cargando...');
            },
            success: function(result){

                if(result.type == 'success'){

                    window.location.href = base_url + "home?capture_client=" + result.cliente_id
                }else if(result.type == 'error'){
                    
                    AlertMessage(STATES_ERROR,'ERROR',result.messageError,{hideAfter:false});
                }
            },
            error:function(xhr){
                AlertMessage(STATES_ERROR,'ERROR!!!',xhr.responseText,{hideAfter: false});
                console.log(xhr.responseText);
            }
        }

        $.ajax(config_ajax);        
    });
});

//Cargar formulario de cliente 
function loadForm (cliente){

    //Busca el cliente con el id del cliente
    $.ajax({
        url: base_url + "home/loadFormClient",
        data: {id: cliente},
        dataType: 'html',
        beforeSend:function(){

            // console.log('cargando cliente');
        },
        success:function(response){

            try{

                $(".sarlaf-messages").fadeIn(250);

                //Parsea la respuesta para saber si no html
                var responseLoadform = $.parseJSON(response);

                $.each(responseLoadform, function(indexResult, valResult) {
                    AlertMessage(eval(valResult.type),valResult.title,valResult.message,{hideAfter : false});
                });
            }catch (exception){
                
                $(".sarlaf-messages").fadeOut(100); //Oculta el mensaje para cargar un cliente
                $('input[name="view_cliente"]').val(cliente); //Envia el id del cliente a este campo

                $("#ViewDocumentsClient").show(); //Habilita el boton del los documentos del cliente
                $("#RefreshForm").show(); //Habilita el boton para refrescar al cliente
                $("#ViewCliente").show(); //Habilita el boton para ver el formulario del cliente

                //Carga la visualizacion del formulario del cliente
                $('div#conten-form').fadeIn(400).html(response);
                if($('input[name=estado_form_id]').val() == 1){
                    if($(window).width() > 700){
                        alert("Ajuste la pantalla con las teclas Windows + tecla izquierda");
                    }
                    preview($.urlParam('capture_client'),'FCC');
                }

                //Carga los documentos del cliente despues de cargar el formulario
                $.ajax({
                    url: base_url+'home/tableAllFilesClient',
                    data: {id: cliente},
                    success:function(resultadoDocumentos){
                        var options = {

                            columns:[
                                {
                                    title   : 'NOMBRE DOCUMENTO',
                                    data    : 'NOMBRE_DOC'
                                },
                                {
                                    title   : 'FECHA INGRESO ARCHIVO',
                                    data    : 'FECHA_INGRESO_ACHIVO'
                                },
                                {
                                    title   : 'OPCIONES ARCHIVO',
                                    data    : 'OPCIONES_ARCHIVO'
                                }
                            ],
                            data:resultadoDocumentos
                        };

                        $('table#table-listado-documentos-cliente').DataTable(options);
                    },
                    error:function(xhr){
                        AlertMessage(STATES_ERROR,'RESULTADO FORMULARIOS ERROR!!!',xhr.responseText,{hideAfter: false});
                        console.log(xhr.responseText);
                    },
                    complete:function(xhr){
                        if(xhr.status == 200){
                            AlertMessage(STATES_OK,'DOCUMENTOS OK','Se cargaron comepltamente los archivos del cliente (' + xhr.responseJSON.length + ')');
                        }
                    }
                });

                // Muestra la informacion del cliente
                $("a#ViewCliente").on('click', function(event) {

                    $(".panels-content").each(function(){
                        if($(this).attr("id") != "content-sarlaft")
                            $(this).hide(0);
                        else
                            $(this).show(0);                        
                    });
                    if(!$('#conten-form').is(':visible')){
                        $('#conten-form').fadeIn(200);
                        $('#table-pendientes').fadeOut(200);
                        $('#documentos-clientePDF').fadeOut(200);
                    }


                });

                // Muestra los documentos del cliente despues de cargarlo
                $("a#ViewDocumentsClient").on('click', function(event) {

                    if(!$('#documentos-clientePDF').is(':visible')){
                        $('#documentos-clientePDF').fadeIn(200);
                        $('#table-pendientes').fadeOut(200);
                        $('#conten-form').fadeOut(200);
                    }
                });

                // Refresca el cliente para el momento en que se cambie de estado
                $('a#RefreshForm').on('click', function(event) {

                    var cliente = $('input[name="cliente"]').val();
                    window.location.href = base_url + 'home?capture_client=' + cliente;
                });
            }
        },
        error:function(xhr){

            AlertMessage(STATES_ERROR,'RESPUESTA CLIENTE ERROR!!!',xhr.responseText,{hideAfter: false});
            console.log(xhr.responseText);
        }
    });
}

// Previsualiza la vista del documento del cliente en una pantalla diferente
function preview (cliente,type_doc, id_Archivo){
    var w = window;
    var vanc = (w.innerWidth / 2) || (e.clientWidth / 2) || (g.clientWidth / 2);
    var valt = w.innerHeight|| e.clientHeight|| g.clientHeight;;
    var vlef = (w.innerWidth - vanc) || (e.clientWidth - vanc) || (g.clientWidth - vanc);
    var vtop = 0;
    var argumentos = 'toolbar=1,location=1,directories=0,status=1,menubar=0,resizable=0,width=' + vanc + ',height=' + valt + ',top=' + vtop + ',left=' + vlef + 'scrollbars=1';
    
    if(id_Archivo == undefined)
        var path = base_url + "home/visualizar_archivo/" + cliente + "/" + type_doc;
    else
        var path = base_url + "home/visualizar_archivo/" + cliente + "/" + type_doc + '/' + id_Archivo;

    popUp = window.open(path , 'Archivo Sarlaft' , argumentos, true);
}