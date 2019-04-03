var intentos_errados=0;
var userForm="";
var userFormAnt="";
$(document).ready(function(){

    //Validación de inicio de sesión
    $(".login-button").on("click", function(event){
        event.preventDefault();

        $("*").removeClass("error-input");
        var errMsg = $(".form-messages");
        errMsg.find(".error-message").html("");

        //Validación de campos
        if($("#username").val() == "" || $.trim($("#username").val()) == "")
        {           
            errMsg.fadeIn(250).find(".error-message").html("Ingrese el nombre de usuario.");
            $("#username").addClass("error-input").focus();
        }
        else if($("#password").val() == "" || $.trim($("#password").val()) == "")
        {           
            errMsg.fadeIn(250).find(".error-message").html("Ingrese la contraseña");
            $("#password").addClass("error-input").focus();
        }
        else
        {
            //Preparación para envío de datos a servidor
            errMsg.fadeOut(0).find(".error-message").html("").fadeOut(0);
            errMsg.fadeIn(250).find(".image-load").fadeIn(120).html("<img src='" + base_url + "public/img/loading.gif'>");

            var data = "username=" + encodeURIComponent($.trim($("#username").val()));
            data += "&password=" + encodeURIComponent($.trim($("#password").val()));

            setTimeout(function () { 

                //Envíos de datos al servidor
                //
                //Respuestas: 
                //          1: Inicio de sesión correcto
                //          2: Usuario no existe
                //          3: Contraseña incorrecta
                //          4: Usuario inactivo
                
                var url = base_url + "users/login";
                errMsg.fadeOut(250).find(".image-load").html("").fadeOut(230);

                userForm=$("#username").val();
                if(intentos_errados==0)
                {
                  userFormAnt=$("#username").val();
                }
                else
                {
                  if(userFormAnt!=userForm)
                  {
                    console.log("User diferent = "+userFormAnt+" - - "+userForm);
                    userFormAnt=userForm;
                    intentos_errados=0;
                  }
                }
                
                $.ajax({
                    type: "POST",
                    url: url,
                    data: data,
                    success: function (response) {
                        switch(response)
                        {
                            case 1:
                                window.location = base_url + "reportes";
                                break;
                            case 2: 
                                window.location = base_url + "consultaFechas";
                                break;
                            case 3:                                
                                window.location = base_url + "home";
                                break;
                            case 4:
                                window.location = base_url + "visualizacion";
                                break;
                            case 5:
                                window.location = base_url + "reportes";
                                break;
                            case 6:
                                window.location = base_url + "radicacion";
                                break;
                            case 7:
                                window.location = base_url + "consultaFechas";
                                break;
                            case -13:
                                //Usuario no existe
                                errMsg.fadeIn(250).find(".error-message").html("El usuario no existe.").fadeIn(230);
                                $("#username").addClass("error-input").focus();
                                break;
                            case -11:

                                //Contraseña incorrecta
                                intentos_errados+=1; //Contador de intentos errados del password
                                alertAdd="";
                                if(intentos_errados==4)
                                {
                                  alertAdd="En el proximo intento errado, se bloqueara el usuaio."; // Ultimo aviso de bloqueo
                                }
                                if(intentos_errados>=5) // Bloquear Usuario por intentos errados
                                {
                                  $.ajax({
                                      url: "./users/bloquearUser",
                                      data: {"username":userForm},
                                      success: function(response)
                                      {
                                        errMsg.fadeIn(250).find(".error-message").html("El Usuario ha sido bloqueado, hable con su administrador.").fadeIn(230);
                                        $("#password").addClass("error-input").focus();
                                      },
                                      error: function(xhr)
                                      {   
                                          //Contraseña incorrecta
                                          errMsg.fadeIn(250).find(".error-message").html("Ha ocurrido un error. Por favor intente mas tarde.").fadeIn(230);
                                          $("#password").addClass("error-input").focus();
                                      }
                                  });
                                }
                                else 
                                {
                                    //Contraseña incorrecta
                                    errMsg.fadeIn(250).find(".error-message").html("Contraseña incorrecta."+alertAdd).fadeIn(230);
                                    $("#password").addClass("error-input").focus();
                                }
                              
                                break;
                            case -12:
                                //Usuario inactivo
                                errMsg.fadeIn(250).find(".error-message").html("El usuario se encuentra inactivo.").fadeIn(230);
                                $("#username").addClass("error-input").focus();
                                break;   
                            case -14:
                                //Bloqueo de Perfil con Ip de acceso
                                errMsg.fadeIn(250).find(".error-message").html("El usuario no esta habilitado para entrar desde una red externa.").fadeIn(230);
                                $("#username").addClass("error-input").focus();
                                break;                         
                            default:
                                errMsg.fadeIn(250).find(".error-message").html("Ha ocurrido un error. Por favor intente mas tarde.").fadeIn(230);
                                break;
                        }
                    },
                    error: function (error) {
                        errMsg.fadeIn(250).find(".error-message").html("Ha ocurrido un error. Por favor intente mas tarde.").fadeIn(230);
                    }

                });
            }, 1890);            
        }
    });
});