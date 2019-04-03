$(document).ready(function(){

    //Generar sugerencia de username por nombre
    $("#full_name").on("blur", function(){
        var text = "value=" + $(this).val();

        if( $(this).val() != ""){
            
            var url = base_url + "users/generateUsername";

            $.ajax({
                type: "POST",
                url: url,
                data: text,
                success: function (response) { 
                    $("#username").val(response);              
                },
                error: function (error) {}
            });
        }

    });

    //Validaciones de formulario de creación de usuarios
    $("#new_user_form").on("submit", function(event){
        event.preventDefault();

        var form = $(this);

        $("*").removeClass("error-input");
        var errMsg = $(".form-messages");
        errMsg.find(".error-message").html("");

        //Verificación de valores de campos
        if($("#full_name").val() == "" || $.trim($("#full_name").val()) == "")
        {
            errMsg.fadeIn(250).find(".error-message").html("Ingrese el nombre completo del usuario.");
            $("#full_name").addClass("error-input").focus();
        }
        else if($("#username").val() == "" || $.trim($("#username").val()) == "")
        {
            errMsg.fadeIn(250).find(".error-message").html("Ingrese el nombre de usuario.");
            $("#username").addClass("error-input").focus();
        }
        else if($("#role").val() == 0 )
        {
            errMsg.fadeIn(250).find(".error-message").html("Seleccione el tipo de usuario.");
            $("#role").addClass("error-input").focus();
        }
        else if (!/^(([^<>()[\]\.,;:\s@\"]+(\.[^<>()[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i.test($("#correo").val()) && $("#li_correo").is(":visible")){
            errMsg.fadeIn(250).find(".error-message").html("Verifique formato de correo electrónico");
            $("#correo").addClass("error-input").focus();
            return;
        }else{
            //Envío de información a servidor de manera asincrona
            
            // var data = "full_name=" + $.trim($("#full_name").val());
            // data += "&username=" + $.trim($("#username").val());
            // data +=  "&correo=" + $.trim($("#correo").val());
            // data +=  "&role=" + $("#role").val();
            
            var url = form.attr("action");
            
            $.ajax({
                type: "POST",
                url: url,
                data: form.serializeArray(),
                success: function (response) {
                    console.log(response);
                    switch(response)
                    {
                        case 1:
                            //Usuario creado de forma correcta
                            errMsg.fadeIn(250).find(".error-message").html("Usuario creado con éxito.").animate({backgroundColor : "#83e891" , borderColor : "#5ec16c"}, 120).fadeIn(230);

                            setTimeout(function () {
                                window.location = base_url + "users/gestion_usuarios";
                            }, 1200);
                            
                            break;
                        case 2:
                            //Usuario ya existe
                            errMsg.fadeIn(250).find(".error-message").html("El nombre de usuario ya existe.").fadeIn(230);
                            $("#username").addClass("error-input").focus();
                            break;
                        case 0:
                            // No se ha podido crear el usuario
                            errMsg.fadeIn(250).find(".error-message").html("No se ha podido regitrar el usuario, intente de nuevo.").fadeIn(230);                            
                            break;
                        default:
                            errMsg.fadeIn(250).find(".error-message").html("Ha ocurrido un error. Por favor intente mas tarde.").fadeIn(230);
                            break;
                    }
                    
                },
                error: function (error) {
                    console.log(error);
                    errMsg.fadeIn(250).find(".error-message").html("Ha ocurrido un error. Por favor intente mas tarde.").fadeIn(230);
                }

            });
        }
        
    });
    
    $("#role").change(function(){
        if($("#role").val() == '4' || $("#role").val() == '7'){
            $("#li_correo").fadeIn(100);
        }
        else{
            $("#li_correo").fadeOut(100);
        }
    });

});