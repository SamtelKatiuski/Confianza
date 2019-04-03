<?php

//Menu global de las vistas en forma de arreglo
// id => Nombre de controlador o página actual
// titulo => Texto a mostrar en enlace
// enlace => Ruta de enlace
// user_rol => Tipo de usuario que verá el menú

$menu = array();

if(Session::get("Mundial_authenticate")){

    if(Session::getLevel(Session::get('Mundial_user_rol')) == Session::getLevel('Gerente')){

        $menu = array(
            [
                'id' => 'consulta_fechas',
                'titulo' => 'Consulta de fechas',
                'enlace' => BASE_URL . 'consultaFechas',
                'user_rol' => 'Consulta Fechas'          
            ],
            [
                'id'    => 'radicacion',
                'titulo'=> 'Radicacion',
                'enlace'=> BASE_URL . 'radicacion',
                'user_rol' => 'Gerente'
            ],
            [
                'id'    => 'Captura',
                'titulo'=> 'Captura' ,
                'enlace'=> BASE_URL . 'home',
                'user_rol' => 'Gerente'
            ],
            [
                'id'    => 'visualizacion',
                'titulo'=> 'Visualizacion',
                'enlace'=> BASE_URL . 'visualizacion',
                'user_rol' => 'Gerente'
            ],
            [
                'id'    => 'pendientes',
                'titulo'=> 'Pendientes',
                'enlace'=> BASE_URL . 'pendientes',
                'user_rol' => 'Gerente'
            ],
            [
                'id' => 'migracion',
                'titulo' => 'Migración',
                'enlace' => BASE_URL . 'migracion',
                'user_rol' => 'Gerente'          
            ],
            [
                'id' => 'reportes',
                'titulo' => 'Reportes Excel',
                'enlace' => BASE_URL . 'reportes',
                'user_rol' => 'Gerente'
            ],
            [
                'id' => 'usuarios',
                'titulo' => 'Usuarios',
                'enlace' => BASE_URL . 'users/gestion_usuarios',
                'user_rol' => 'Gerente'
            ],
        );
    }

    if(Session::getLevel(Session::get('Mundial_user_rol')) == Session::getLevel('Operador Asistemyca')){

        $menu = array (
            [
                'id'    => 'visualizacion',
                'titulo'=> 'Visualizacion',
                'enlace'=> BASE_URL . 'visualizacion',
                'user_rol' => 'Operador Asistemyca',
            ], [
                'id'    => 'home',
                'titulo'=> 'Captura' ,
                'enlace'=> BASE_URL . 'home',
                'user_rol' => 'Operador Asistemyca',
            ]
        );
    }

    if(Session::getLevel(Session::get("Mundial_user_rol")) == Session::getLevel('Operador Mundial')){

        $menu = array(
            [
                'id'    => 'visualizacion',
                'titulo'=> 'Visualizacion',
                'enlace'=> BASE_URL . 'visualizacion',
                'user_rol' => 'Operador Mundial'
            ],
        );
    }

    if(Session::getLevel(Session::get("Mundial_user_rol")) == Session::getLevel('Reportes')){

        $menu = array(
            [
                'id' => 'reportes',
                'titulo' => 'Reportes Excel',
                'enlace' => BASE_URL . 'reportes',
                'user_rol' => 'Reportes'
            ],
            [
                'id'    => 'visualizacion',
                'titulo'=> 'Visualizacion',
                'enlace'=> BASE_URL . 'visualizacion',
                'user_rol' => 'Reportes'
            ],
        );
    }

    if(Session::getLevel(Session::get("Mundial_user_rol")) == Session::getLevel('Consulta Fechas')){

        $menu = array (
            [
                'id' => 'consulta_fechas',
                'titulo' => 'Consulta de fechas',
                'enlace' => BASE_URL . 'consultaFechas',
                'user_rol' => 'Consulta Fechas'          
            ],
        );
    }

    if(Session::getLevel(Session::get("Mundial_user_rol")) == Session::getLevel('Operador Radicador')){

        $menu = array(
            [
                'id'    => 'radicacion',
                'titulo'=> 'Radicacion',
                'enlace'=> BASE_URL . 'radicacion',
                'user_rol' => 'Operador Radicador'
            ],
            [
                'id'    => 'home',
                'titulo'=> 'Captura' ,
                'enlace'=> BASE_URL . 'home',
                'user_rol' => 'Operador Radicador'
            ],
            [
                'id'    => 'visualizacion',
                'titulo'=> 'Visualizacion',
                'enlace'=> BASE_URL . 'visualizacion',
                'user_rol' => 'Operador Radicador'
            ],
            [
                'id'    => 'pendientes',
                'titulo'=> 'Pendientes',
                'enlace'=> BASE_URL . 'pendientes',
                'user_rol' => 'Operador Radicador'
            ],
        );
    }

    if(Session::getLevel(Session::get("Mundial_user_rol")) == Session::getLevel('Perfil Intermedio')){

        $menu = array(
            [
                'id' => 'consulta_fechas',
                'titulo' => 'Consulta de fechas',
                'enlace' => BASE_URL . 'consultaFechas',
                'user_rol' => 'Perfil Intermedio'          
            ],
            [
                'id'    => 'visualizacion',
                'titulo'=> 'Visualizacion',
                'enlace'=> BASE_URL . 'visualizacion',
                'user_rol' => 'Perfil Intermedio'
            ],
        );
    }
}