//Constantes
STATES_SQL_FAIL = 0;
STATES_OK = 1;
STATES_VALIDATION_FAIL = 2;
STATES_DENIED = 3;
STATES_WARNING = 4;
STATES_BLUE = 5;
STATES_INFO = 6;
STATES_ERROR = 7;
VAR_ERROR = [];

function ValidateEmail(a) {
    var parents = $(a).parents('.form-group').length ? $(a).parents('.form-group') : $(a).parent('.form-group');
    if(!IfEmpty(a)){

        emailRegex = /^[-\w.%+]{1,64}@(?:[A-Z0-9-]{1,63}\.){1,125}[A-Z]{2,63}$/i;
        if (!emailRegex.test(a.value)) {

            parents.children('small.help-error-block').text("* El formato de correo es invalido").addClass('text-danger');
            if($.inArray(a, VAR_ERROR) == -1){
                VAR_ERROR.push(a);
            }
        }else{

            parents.children("small.help-error-block").empty().removeClass('text-danger');
            VAR_ERROR = $.grep(VAR_ERROR, function(value) {
              return value != a ;
            });
        }
    }else{

        ValidateFieldEmpty(a);
    }
}

function getDateNow(date){
    
    var fecha = !date ? new Date() : new Date(date);
    var fecha_actual = {
        dia : (fecha.getDate().toString().length === 1) ? ('0'+fecha.getDate()) : (fecha.getDate().toString()), 
        mes: (((fecha.getMonth()+1).toString().length) === 1) ? '0'+(fecha.getMonth()+1) : (fecha.getMonth()+1).toString(), 
        año : fecha.getFullYear().toString()
    }
    return fecha_actual;
}

function getHoursNow(time){

    var hora = !time ? new Date() : new Date(time);
    var time = {
        hora: (hora.getHours().toString().length === 1) ? '0'+hora.getHours() : hora.getHours(),
        minuto : (hora.getMinutes().toString().length === 1) ? '0'+hora.getMinutes() : hora.getMinutes(),
        segundo: hora.getSeconds()
    };

    return time;
}

function OnlyLetters(a) {
    a.value = a.value.replace(/[^a-zA-ZàèìòùÀÈÌÒÙáéíóúýÁÉÍÓÚÝâêîôûÂÊÎÔÛãñõÃÑÕäëïöüÿÄËÏÖÜŸçÇßØøÅåÆæœ\s]*$/g, "");
}

function WithoutAccents(a) {
    // a.value = a.value.replace(/[^a-zA-Z0-9\s\.]*$/g, "");
    a.value = a.value.normalize('NFD').replace(/[\u0300-\u036f]/g, "");
}

function ValidateRegex(regex,a){
    a.value = a.value.replace(regex, "");
}

var normalize = (function() {
  var from = "ÃÀÁÄÂÈÉËÊÌÍÏÎÒÓÖÔÙÚÜÛãàáäâèéëêìíïîòóöôùúüûÑñÇç", 
      to   = "AAAAAEEEEIIIIOOOOUUUUaaaaaeeeeiiiioooouuuuNncc",
      mapping = {};
 
  for(var i = 0, j = from.length; i < j; i++ )
      mapping[ from.charAt( i ) ] = to.charAt( i );
 
  return function( str ) {
      var ret = [];
      for( var i = 0, j = str.length; i < j; i++ ) {
          var c = str.charAt( i );
          if( mapping.hasOwnProperty( str.charAt( i ) ) )
              ret.push( mapping[ c ] );
          else
              ret.push( c );
      }      
      return ret.join( '' );
  }
})();

function ValidatePhone(a){

    var NameField = $(a).attr('name');
    var parents = $(a).parents('.form-group').length ? $(a).parents('.form-group') : $(a).parent('.form-group');
    if(IfEmpty(a) && a.value.length < 7){

        parents.children('small.help-error-block').text("* No puede contener menos de 7 digitos").addClass('text-danger');
        if($.inArray(a, VAR_ERROR) == -1){
            VAR_ERROR.push(a);
        }
    }else{

        if(parents.children('small.help-error-block').text().length){

            parents.children("small.help-error-block").empty().removeClass('text-danger');
            VAR_ERROR = $.grep(VAR_ERROR, function(value) {
              return value != a ;
            });
        }
    }
}

function OnlyNumbers(a) {
    a.value = a.value.replace(/[^0-9]/g, "");
}

function OnlyNumbersDecimals(a) {
    a.value = a.value.replace(/[^0-9\.]/g, "")
}

function IfEmpty(a){
    if(!a.value.length){
        return true;
    }else{
        return false;
    }
}

function ValidateFieldEmpty(a){

    var parents = $(a).parents('.form-group').length ? $(a).parents('.form-group') : $(a).parent('.form-group');
    if(IfEmpty(a)){

        parents.children('small.help-error-block').text("* El Campo no puede estar vacio").addClass('text-danger');
        if($.inArray(a, VAR_ERROR) == -1){
                VAR_ERROR.push(a);
        }
    }else{

        if(parents.children('small.help-error-block').text().length != 0){
            parents.children("small.help-error-block").empty().removeClass('text-danger');
            VAR_ERROR = $.grep(VAR_ERROR, function(value) {
              return value != a ;
            });
        }
    }
}

function ValidateFieldSelect(a){
    var parents = $(a).parents('.form-group').length ? $(a).parents('.form-group') : $(a).parent('.form-group');
    if(!a.value.length){

        parents.children('small.help-error-block').text("* Por favor seleccionar una option").addClass('text-danger');
        if($.inArray(a, VAR_ERROR) == -1){
            VAR_ERROR.push(a);
        }
    }else{

        parents.children("small[class='help-error-block text-danger']").empty().removeClass('text-danger');
        VAR_ERROR = $.grep(VAR_ERROR, function(value) {
            return value != a ;
        });
    }
}

function ValidateFieldRadio(a){
    var parents = $(a).parents('.form-group').length ? $(a).parents('.form-group') : $(a).parent('.form-group');
    if(!$('body').find('[type="radio"][name="'+$(a).attr('name')+'"]:checked').length || $('body').find('[type="radio"][name="'+$(a).attr('name')+'"]:checked').val() == ''){

        parents.children('small.help-error-block').text("* No hay ningun campo seleccionado").addClass('text-danger');
        if($.inArray(a, VAR_ERROR) == -1){
            VAR_ERROR.push(a);
        }
    }else{

        if(parents.children("small[class='help-error-block text-danger']").text().length){
            parents.children("small[class='help-error-block text-danger']").empty().removeClass('text-danger');
            VAR_ERROR = $.grep(VAR_ERROR, function(value) {
              return value != a ;
            });
        }
    }
}

function ValidateDate(a){
    dateRegex = /^\d{4}-((0\d)|(1[012]))-(([012]\d)|3[01])$/;
    var parents = $(a).parents('.form-group').length ? $(a).parents('.form-group') : $(a).parent('.form-group');
    if(!IfEmpty(a)){

        if(!a.value.match(dateRegex)){

            parents.children('small.help-error-block').text("* Fecha invalida").addClass('text-danger');
            if($.inArray(a, VAR_ERROR) == -1){
                VAR_ERROR.push(a);
            }
        }else{

            parents.children('small.help-error-block').empty().removeClass('text-danger');
            VAR_ERROR = $.grep(VAR_ERROR, function(value) {
              return value != a ;
            });
        }
    }else{

        ValidateFieldEmpty(a);
    }
}

function ValidateTime(a){
    dateRegex = /^([0-1]?[0-9]|2[0-4]):([0-5][0-9])(:[0-5][0-9])?$/;
    var parents = $(a).parents('.form-group').length ? $(a).parents('.form-group') : $(a).parent('.form-group');
    if(!IfEmpty(a)){

        if(!a.value.match(dateRegex)){
            parents.children('small.help-error-block').text("* Hora invalida").addClass('text-danger');
            if($.inArray(a, VAR_ERROR) == -1){
                VAR_ERROR.push(a);
            }
        }else{
            parents.children('small.help-error-block').empty().removeClass('text-danger');
            VAR_ERROR = $.grep(VAR_ERROR, function(value) {
              return value != a ;
            });
        }
    }else{

        ValidateFieldEmpty(a);
    }
}

function ValidateCheckbox(a){
    var parents = $(a).parents('.form-group').length ? $(a).parents('.form-group') : $(a).parent('.form-group');
    if(!$('body').find(parents).find(':input[type="checkbox"]:checked').length){
        parents.children('small.help-error-block').text("* No hay ningun campo seleccionado").addClass('text-danger');
        if($.inArray(a, VAR_ERROR) == -1){
            VAR_ERROR.push(a);
        }
    }else{
        var checkbox = 0;
        
        $('body').find(parents).find(':input[type="checkbox"]:checked').each(function(index,el){
            if(el.value.length){
                checkbox++;
            }
        });

        if(checkbox == 0){

            parents.children('small.help-error-block').text("* No hay ningun campo seleccionado").addClass('text-danger');
            if($.inArray(a, VAR_ERROR) == -1){
                VAR_ERROR.push(a);
            }
        }else{

            if(parents.children("small.help-error-block").text() != undefined && parents.children("small.help-error-block").text().length){
                parents.children("small.help-error-block text-danger").empty().removeClass('text-danger');
            }

            VAR_ERROR = $.grep(VAR_ERROR, function(value) {
              return value != a ;
            });
        }
    }
}

$(function() {

    // Esta funcion de ajaxSetup carga los valores que utilizaran por defecto al momento de llamar cualquier ajax dentro del sistema
    // Desactivando cache, en caso de requerirse para alguna función especifica se debe activar, de esta misma forma, con valor true..
    $.ajaxSetup({ 
        cache: false,
        method: 'POST',
        dataType: 'json'
    });
});

// funcion de envio de datos por ajax
function SendData(url,data,method,config_ajax){

    var respuesta = false;

    var optionsAjax = {
        url: url,
        type: method,
        dataType: 'json', 
        data: data,
        success: function(response){
            respuesta = response;
        },
        error: function(){
            console.log("!Opsss error, la respuesta de datos no llego correctamente");
        }
    }

    if(config_ajax){
        $.each(config_ajax, function(index, val) {
            optionsAjax[index] = val;
        });
    }
    
    $.ajax(optionsAjax);


    return respuesta;
}

function ValidateForm(form){

    VAR_ERROR = [];
    $.each($('body').find(form).find(':input[name][data-required="true"]'),function (index,el){

        if($(el).data('required') != undefined && $(el).data('required') == true){
            ValidateField(el,el.nodeName,el.type);
        }
    });

    if(VAR_ERROR != 0 || isNaN(VAR_ERROR)){
        return false;
    }else{
        return true;
    }
}

function ValidateField(field,tag,type){
    if(tag != "" && field != ""){
        switch(tag){
            case 'INPUT':
                if(type == 'radio'){
                    ValidateFieldRadio(field);
                }
                if(type == 'text'){
                    ValidateFieldEmpty(field);
                }
                if(type == 'number'){
                    ValidateFieldEmpty(field);
                }
                if(type == 'date'){
                    ValidateDate(field);
                }
                if(type == 'time'){
                    ValidateTime(field);
                }
                if(type == 'email'){
                    ValidateEmail(field);
                }
                if(type == 'checkbox'){
                    ValidateCheckbox(field);
                }
                if (type == 'hidden') {
                    ValidateFieldEmpty(field);
                }
                break;
            case 'SELECT':
                ValidateFieldSelect(field);
                break;
            case 'TEXTAREA':
                ValidateFieldEmpty(field);
                break;
        }
    }
}

function AlertMessage(status,titulo,message,msg_options) {

    var options = {
        heading: 'Notificacion Mundial',
        text: 'No se envio mensaje',
        showHideTransition: 'slide',
        icon: 'error'
    }

    if(status != null && status != undefined){

        options["heading"] = titulo;
        options["text"] = message;
        switch (status) {
            case STATES_OK:
                options["icon"] = "success";
                break;
            case STATES_WARNING:
                options["icon"] = "warning";
                break;
            case STATES_BLUE:
                options["icon"] = "info";
                break;
            case STATES_INFO:
                options["icon"] = "info";
                break;
            case STATES_ERROR:
                options["icon"] = "error";
                options["loaderBg"] = '#6b3737';
                break;
            default:
                options["icon"] = "error";
                break;
        }
    }

    if(msg_options){
        $.each(msg_options, function(index, val) {
            options[index] = val;
        });
    }

    $.toast(options);
}

function tiene_numeros(texto){
    numeros="0123456789";
   for(i=0; i<texto.length; i++){
      if (numeros.indexOf(texto.charAt(i),0)!=-1){
         return 1;
      }
   }
   return 0;
}

function tiene_letras(texto)
{
   letras="abcdefghyjklmnñopqrstuvwxyzABCDEFGHYJKLMNÑOPQRSTUVWXYZ";
   texto = texto.toLowerCase();
   for(i=0; i<texto.length; i++){
      if (letras.indexOf(texto.charAt(i),0)!=-1){
         return 1;
      }
   }
   return 0;
}


$(document).ready(function() {

    //Calendario
    $( ".calendar-button" ).datepicker({             
        dateFormat: 'yy-mm-dd',
        changeMonth: true,
        changeYear: true,
        maxDate: '-1',
        yearRange: '-100:+0'
    });

    window.onbeforeunload = function(a) {

        if (null != popUp && popUp.close(), user_busy) return a = a || window.event, a && (a.returnValue = "¿Está seguro de salir?"), "¿Está seguro de salir?"
    }
    
    $(".close-modal-box").on("click", function() {

        $(this).parent().parent().parent().fadeOut(250);
    });

    $(".modal-change-password .confirmation-button").on("click", function() {

        $(".change-password-modal-form").submit();
    });

    $(".change-password-modal-form").on("submit", function(a) {

        a.preventDefault(), 
        $("*").removeClass("error-input");
        var b = $(".form-messages");

        b.fadeOut(250).find(".error-message").html(""); 
        
        if($("#actual-password").val() == '' || $.trim($("#actual-password").val()) == "") {

            b.fadeIn(250).find(".error-message").html("Ingrese su contraseña.");
            $("#actual-password").addClass("error-input").focus();
        }else if($("#new-password").val() == '' || $.trim($("#new-password").val()) == ''){

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
                var a = base_url + "users/change_invalid_password",
                    c = "password=" + encodeURIComponent($.trim($("#actual-password").val()));
                c += "&new_password=" + encodeURIComponent($.trim($("#new-password").val())), b.fadeIn(250).find(".error-message").html(""), $.ajax({
                    type: "POST",
                    url: a,
                    data: c,
                    success: function(a) {
                        "1" == a || 1 == a ? (b.fadeIn(250).find(".error-message").animate({
                            backgroundColor: "#83e891",
                            borderColor: "#5ec16c"
                        }, 120).html("Se ha cambiado de forma correcta su contraseña."), setTimeout(function() {
                            location.reload()
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

    $(".sub-menu-user").on("click", function() {

        var a = $(".lateral-user-menu-content"),
            d = a.find(".lateral-user-menu");

        if(a.is(":hidden")) {

            a.fadeIn(250);
            d.animate({
                right: 0
            }, 150);
            
            $(".line").addClass("hide-menu").removeClass("show-menu");
        }else{

            d.animate({
                right: (d.outerWidth()) * -1
            }, 150);
            a.fadeOut(250);
            $(".line").addClass("show-menu").removeClass("hide-menu");
        }
    });

    // JAV01
    $(".lateral-user-menu-content").on("click", function(e){
        var target = e.target;
        if($(target).attr("class") != 'lateral-user-menu'){
            $(".lateral-user-menu-content").fadeOut(150);
            $(".lateral-user-menu").animate({
                right: ($(".lateral-user-menu").outerWidth()) * -1
            }, 150);
            $(".line").addClass("show-menu").removeClass("hide-menu");
        }
    });

    $('body').on('keyup',".currency", function(event) {
        this.value = formatCurrency(this.value);
    });

    $('body').on('focus',".currency", function(event) {
        this.value = formatCurrency(this.value);
    });

    $('body').on('blur',".currency", function(event) {
        this.value = this.value.replace(/,/g, "");
    });

    $.fn.select2.defaults.set("theme", "bootstrap");
});

//Cargar listado de ciudades por departamento
function loadCities(element, callback) {

    var config_ajax = {

        dataType: 'html',
        success : function(response) {
            callback.html(response);
        },
        error : function(error) {
            console.log(error);
        }
    }

    SendData(base_url + "home/getCities",{id : element.value},'POST',config_ajax);
}

function formatCurrency(value){
    return value.toString().replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

function checkboxEnabledField(status,e,field,callback){
    if(status === true){
        if(callback == 'hide')
            if($(e).is(":checked"))
                $(field).fadeIn(150);
            else
                $(field).fadeOut(300);

        if(callback == 'disabled')
            if($(e).is(":checked"))
                $(field).attr('disabled',true);

        if(callback == 'modal'){
            if($(e).is(':checked')){
                $(field).modal({
                        backdrop: 'static',
                        keyboard: true, 
                        show: true
                });
                $("div.nav-bar").css("z-index","999");
            }else{
                $(field).modal('hide');
            }
        }

    }else{
        if(callback == 'hide')
            $(field).fadeOut(300);
        if(callback == 'disabled')
            $(field).attr('disabled',false);
        if(callback == 'modal'){
            $(field).modal('hide');
        }
    }
}

$.urlParam = function(name){
    var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
    if(results){
        return results[1];
    }else{
        return false;
    }   
}

// function para cargar los datos a una tabla dinamicamente
function CargarListadoTabla($table,$columns,$data,config){

    var optionsTable = {
        data: $data,
        columns: $columns,
        language: {
            sProcessing:    "Procesando...",
            sLengthMenu:    "Mostrar _MENU_ registros",
            sZeroRecords:   "No se encontraron resultados",
            sEmptyTable:    "Ningún dato disponible en esta tabla",
            sInfo:          "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            sInfoEmpty:     "Mostrando registros del 0 al 0 de un total de 0 registros",
            sInfoFiltered:  "(filtrado de un total de _MAX_ registros)",
            sInfoPostFix:   "",
            sSearch:        "Buscar:",
            sUrl:           "",
            sInfoThousands:  ",",
            sLoadingRecords: "Cargando...",
            oPaginate: {
                sFirst:    "Primero",
                sLast:    "Último",
                sNext:    "Siguiente",
                sPrevious: "Anterior"
            },
            oAria: {
                sSortAscending:  ": Activar para ordenar la columna de manera ascendente",
                sSortDescending: ": Activar para ordenar la columna de manera descendente"
            }
        }
    }

    if(config){
        $.each(config, function(index, val) {
            optionsTable[index] = val;
        });
    }


    $table.DataTable(optionsTable);
}


function CargarListadoTablaDataGrid($table,addConfig){

    var configDefault = {
        showColumnLines: true,
        showRowLines: true,
        rowAlternationEnabled: true,
        showBorders: true,
        paging: {
            pageSize: 10
        },
        pager: {
            showPageSizeSelector: true,
            allowedPageSizes: [5, 10, 20],
            showInfo: true
        },
        columnsAutoWidth: true,
        filterRow: {
            visible: true,
            applyFilter: "auto"
        },
        searchPanel: {
            visible: true,
            width: 240,
            placeholder: "Search..."
        },
        headerFilter: {
            visible: true
        },
        selection: {
            mode: "multiple"
        },
        export: {
            enabled: true,
            allowExportSelectedData: true
        },
        groupPanel: {
            visible: true
        },
        columnResizingMode: "nextColumn",
        columnMinWidth: 50,
        columnAutoWidth: true,
    }

    if(addConfig){
        $.each(addConfig, function(index, val) {
            configDefault[index] = val;
        });
    }


    $table.dxDataGrid(configDefault);
}

//Reinicia todo el formulario
function resetForm(formName){
    $(':input','form[name=' + formName + ']')
     .not(':button, :submit, :reset, :hidden').each(function(index,el){
        resetField(el);
     });
}

//Reinicia un campo por el tipo de campo
function resetField(field){
    if(field.value.length){
        switch(field.nodeName){
            case 'INPUT':
                if(field.type == 'radio'){
                    $(field).prop('checked', false);
                }else if(field.type == 'checkbox'){
                    $(field).prop('checked', false);
                }else{
                    $(field).val('').removeAttr('value');
                }
                break;
            case 'SELECT':
                $(field).prop('selectedIndex',0);
                break;
            case 'TEXTAREA':
                $(field).text('').val('');
                break;
        }
    }
}