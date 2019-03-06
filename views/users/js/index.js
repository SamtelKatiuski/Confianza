$(document).ready(function(){

    ListUsers();

    function ListUsers(){
        var urlListUsers = base_url + "users/gesListUsers";
        $.ajax({
            url: urlListUsers,
            type: 'POST',
            data: false,
            success : function(response){

               if(!$.isEmptyObject(response.data)){
                    if(response.columns.length > 0){
                        var configTable = {
                                dom: 'Bfrtip',
                                filename: 'Listado usuarios',
                                buttons: [],
                                destroy : true
                            };

                        CargarListadoTabla($('table#table-listado-usuarios'), response.columns, response.data, configTable);
                        $(".list_users").fadeIn(180);
                        
                    }
                }
            },
            error : function(xhr){
                console.log(xhr);
            }
        });
    }

    //Negación de borrado de usuario
    $(".modal-delete-user .negation-button").on("click", function(){
        $(".modal-delete-user").fadeOut(250);
    });

    //Confirmación de borrado de usuario
    $(".modal-delete-user .confirmation-button").on("click", function(){
        var idUser = $(this).attr("user-id");

        var url = base_url + "users/delete";

        $.ajax({
            type: "POST",
            url: url,
            data: { id : idUser },
            success: function (response) {
                console.log(response);
                if(response)
                    ListUsers();
                else
                    alert("Ha ocurrido un error. Por favor intente mas tarde.");
                

                $(".modal-delete-user").fadeOut(250); 
            },
            error: function (error) {
                alert("Ha ocurrido un error. Por favor intente mas tarde.");
            }
        });

    });
});

function DeleteUser(el){
    //event.preventDefault();

    var id = $(el).attr("id");
    $(".modal-delete-user").fadeIn(250).find(".confirmation-button").attr("user-id", id);    
}