$(document).ready(function(){

    // Habilitar/Deshabilitar campo contraseña
    $(".edit-readonly").on("click", function(){
        var attr = $("#password").attr("readonly");

        if(attr)
        {
            $("#password").prop("readonly", false);
        }
        else
        {
            $("#password").prop("readonly", true);
        }
    });
    
    if(document.getElementById("role").value == '4'){
        $("#li_correo").removeAttr("style");
    }
    else{
        $("#li_correo").css("display","none");
    }
    
    $("#role").change(function(){
        if(document.getElementById("role").value == '4'){
            $("#li_correo").removeAttr("style");
        }
        else{
            $("#li_correo").css("display","none");
        }
    });


    //Validaciones de formulario de modificación de usuario
    $("#update_user_form").on("submit", function(event){
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
        }
        else
        {
            //Envío de información a servidor de manera asincrona

            $.ajax({
                type: "POST",
                url:  form.attr("action"),
                data: form.serializeArray(),
                success: function (response) {
                    switch(response)
                    {
                        case 1:
                            //Usuario creado de forma correcta
                            errMsg.fadeIn(250).find(".error-message").html("Usuario modificado con éxito.").animate({backgroundColor : "#83e891" , borderColor : "#5ec16c"}, 120).fadeIn(230);

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
                    errMsg.fadeIn(250).find(".error-message").html("Ha ocurrido un error. Por favor intente mas tarde.").fadeIn(230);
                }

            });
        }
        
    });
});

function activar()
{
  $("#change_password_modal").css("display","block");
}

function desactivar()
{
  $("#change_password_modal").css("display","none");
}

$( document ).ready(function()
{
  $("#form-pass-change").on("submit", function(a) {
      a.preventDefault(),
      $("*").removeClass("error-input");
      var b = $(".form-messages");

      b.fadeOut(250).find(".error-message").html("");

      if($("#new-password").val() == '' || $.trim($("#new-password").val()) == '')
      {
          b.fadeIn(250).find(".error-message").html("Ingrese su nueva contraseña.");
          $("#new-password").addClass("error-input").focus();
      }else if($("#verification-password").val() == '' || $.trim($("#verification-password").val()) == ''){
          b.fadeIn(250).find(".error-message").html("Verifique su nueva contraseña.");
          $("#verification-password").addClass("error-input").focus();
      }else if($("#verification-password").val() != $("#new-password").val()){
          b.fadeIn(250).find(".error-message").html("Las contraseñas no coinciden.");
          $("#verification-password").addClass("error-input").focus();
      }
      else if(tiene_numeros($("#new-password").val())!=1 || tiene_letras($("#new-password").val())!=1){
          b.fadeIn(250).find(".error-message").html("La contraseña debe ser alfanumerica.");
          $("#verification-password").addClass("error-input").focus();
      }
      else if($("#new-password").val().length<8){
          b.fadeIn(250).find(".error-message").html("La contraseña debe tener minimo 8 caracteres.");
          $("#verification-password").addClass("error-input").focus();
      }
      else{

          b.fadeIn(250).find(".error-message").animate({
              backgroundColor: "transparent",
              border: "none"
          }, 120).html("<img src='" + base_url + "public/img/loading.gif'>"), setTimeout(function() {
              var a = base_url + "users/change_password_admin",
              c = "full_id=" + $("#full_id").val();
              c += "&new_password=" + encodeURIComponent($.trim($("#new-password").val())), b.fadeIn(250).find(".error-message").html(""), $.ajax({
                  type: "POST",
                  url: a,
                  data: c,
                  success: function(a) {
                      "1" == a || 1 == a ? (b.fadeIn(250).find(".error-message").animate({
                          backgroundColor: "#83e891",
                          borderColor: "#5ec16c"
                      }, 120).html("Se ha cambiado de forma correcta su contraseña."), setTimeout(function() {
                          $("#change_password_modal").css("display","none");$(".error-message").fadeOut(250).html("");
                      }, 1500)) : "2" == a || 2 == a ? (b.fadeIn(250).find(".error-message").animate({
                          backgroundColor: "#e32",
                          borderColor: "none"
                      }, 120).html("Contraseña incorrecta."), $("#actual-password").addClass("error-input").focus()) : "0" != a && 0 != a || b.fadeIn(250).find(".error-message").animate({
                          backgroundColor: "#e32",
                          borderColor: "none"
                      }, 120).html("Ha ocurrido un error, por favor intente mas tarde.")
                  },
                  error: function(a) {
                      b.fadeIn(250).find(".error-message").animate({
                          backgroundColor: "#e32",
                          borderColor: "none"
                      }, 120).html("Ha ocurrido un error desconocido, comuniquese con el administrador de la aplicación.")
                  }
              })
          }, 1250);
      }
  });

});