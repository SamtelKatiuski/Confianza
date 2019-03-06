var ERRORS = [];
$(document).ready(function(){

    
    $(".search-folders-form").on("submit", function(event){
        event.preventDefault();

        var urlAjax = base_url + "visualizacion/folder_search";
        var form = $(this);
        var messages = form.find(".form_messages");
        messages.fadeOut(150);

        if($(form).find("input:text").filter(function() { return this.value == ""; }).length == $(form).find("input").length){
            messages.fadeIn(250).html("Ingrese algún criterio de busqueda.");
        }else{
            $.ajax({
                url: urlAjax,
                type: 'POST',
                dataType: 'html',         
                data: form.serialize(),
                success : function(response){
                    $(".folder-content").fadeIn(250);
                    $(".folder-list-content").html(response);
                },
                error : function(error){
                    console.log(error);             
                }
            });         
        }
    });

    $(document).on('click','.folder-list-content label',function(event){
        var obj = $(this).parent();
        var folderName = obj.html();
        var urlAjax = base_url + "visualizacion/getNextLevelFolder";
        $("*").removeClass('selected');

        if(obj.hasClass('active')){
            obj.removeClass('active')
                .addClass('inactive')
                .attr('open_folder','no');
            if(obj.children('ul') != undefined){
                obj.find('ul').remove();
            }
        }else{
            if(obj.hasClass('folder') || obj.hasClass('new-level')){
                var parms = JSON.parse(obj.attr("parms"));
                var data = "padre=" + obj.attr("path").replace("&","@");
                data +="&anio_filtro=" + parms.anio_filtro;
                data +="&nombre_cliente=" + parms.nombre_cliente;
                data +="&numero_documento=" + parms.numero_documento;
                data +="&poliza=" + parms.poliza;
                data +="&siniestro=" + parms.siniestro;
                data +="&linea_negocio=" + parms.linea_negocio;

                $.ajax({
                    url: urlAjax,
                    type: 'POST',
                    dataType: 'html',
                    data: encodeURI(data),
                    success : function(response){
                        if(obj.children('ul') != undefined){
                            obj.find('ul').remove();
                        }
                        var a = JSON.parse(response);
                        obj.removeClass("inactive")
                            .addClass("active")
                            .append(a.res)
                            .attr("open_folder","yes");
                    },
                    error : function(error){
                        console.log(error);
                    }
                });
            }

            if(obj.hasClass('file')){
                var path =  obj.attr("path");
                var urlAjaxView = base_url + "visualizacion/fileViewer";

                $(".files-viewer").html("<div class='loading-file'>Cargando...</div>").fadeIn(250);

                setTimeout(function () {
                    $.ajax({
                        url: urlAjaxView,
                        type: 'POST',
                        dataType: 'html',
                        data: {pathFolder: path},
                        success : function(response)
                        {
                            obj.addClass('selected');

                            $(".files-viewer").html(response).fadeIn(250);

                            if(response.indexOf("iframe") != -1)
                                $(".files-viewer").css({ height : "860px"});

                        },
                        error : function(error)
                        {
                            console.log(error);
                        }
                    });
                }, 1200);
            }
        }
    });
});
