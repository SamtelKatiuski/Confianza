<?php

class clientesModel extends Model 
{

    public function __construct(){
        parent::__construct();       
    }

    //Verifica que un cliente se encuentra en estado activo dentro de la plataforma
    public function ActivateByClienteId($cliente_id,$status_activate){
        $sql = "UPDATE zr_estado_proceso_clientes_sarlaft SET PROCESO_ACTIVO = :status_activate WHERE PROCESO_CLIENTE_ID = :cliente_id";

        $result = $this->_db->prepare($sql);
        $result->bindValue(':cliente_id',$cliente_id,PDO::PARAM_INT);
        $result->bindValue(':status_activate',$status_activate,PDO::PARAM_INT);
        $resultado = $result->execute();

        if(!is_null($result->errorInfo()[2]))
            return $result->errorInfo()[2];
        else
            return $resultado;
    }
    
    //Obtiene la informacion de clientes con sarlaft en la plataforma
    public function getAllClientesSarlaft() {
        $sql = "SELECT * FROM clientes_sarlaft";

        $result = $this->_db->prepare($sql);
        $result->execute();

        if(!is_null($result->errorInfo()[2]))
            return $result->errorInfo()[2];
        else
            return $result->fetchAll(PDO::FETCH_ASSOC); 
    }

    // Obtiene la informacion de un cliente existe en la plataforma por ID
    public function getInfoClientID($tipo_persona, $cliente_id) {
        $sql = "CALL consulta_cliente_sarlaft (:tipo_persona,:cliente_id)";

        $result = $this->_db->prepare($sql);
        $result->bindValue(':tipo_persona',$tipo_persona,PDO::PARAM_INT);
        $result->bindValue(':cliente_id',$cliente_id,PDO::PARAM_INT);
        $resultado = $result->execute();

        if(!is_null($result->errorInfo()[2]))
            return array('error' => $result->errorInfo()[2]);
        else
            return $result->fetch(PDO::FETCH_ASSOC);
    }
   
    //Obtiene listado de clientes que han sido manipulados por un funcionario ( día actual )
    public function getAllClientesByUser($user_id) {
        $sql = 'SELECT
                    clientes.id AS CLIENTE_ID,
                    clientes.documento AS CLIENTE_DOCUMENTO,
                    estados_sarlaft.id AS ESTADO_CLIENTE_ID,
                    estados_sarlaft.desc_type AS ESTADO_CLIENTE,
                    TD.tipo_persona AS TIPO_CLIENTE,
                    CS.NOMBRE_CLIENTE
                FROM clientes
                    INNER JOIN clientes_sarlaft CS ON CS.cliente_id = clientes.id
                    INNER JOIN tipos_documentos TD ON TD.id = clientes.tipo_documento
                    LEFT JOIN estados_sarlaft ON CS.estado_formulario_id = estados_sarlaft.id';

        $result = $this->_db->prepare($sql);

        $resultado = $result->execute();

        if(!is_null($result->errorInfo()[2]))
            return $result->errorInfo()[2];
        else
            return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    //Obtiene la informacion de un cliente que se necesite capturar de forma aleatoria
    public function getClientCaptureRamdon() {

        $sql = "SELECT 
                    ZEPCS.PROCESO_CLIENTE_ID AS CLIENTE_ID
                FROM    zr_estado_proceso_clientes_sarlaft ZEPCS
                WHERE 
                    ZEPCS.ESTADO_PROCESO_ID IN (1,13)
                    AND ZEPCS.FECHA_PROCESO = (
                        SELECT 
                            MAX(FECHA_PROCESO) 
                        FROM zr_estado_proceso_clientes_sarlaft 
                        WHERE PROCESO_CLIENTE_ID = ZEPCS.PROCESO_CLIENTE_ID
                    )
                    AND PROCESO_ACTIVO = 0
                ORDER BY
                    ID
                ASC";

        $result = $this->_db->prepare($sql);
        $resultado = $result->execute();

        if(!is_null($result->errorInfo()[2]))
            return array('error' => $result->errorInfo()[2]);
        else
            return $result->fetch(PDO::FETCH_ASSOC);
    }

    /*===============================================================
    =            FUNCIONES TABLA archivo_organizado            =
    ===============================================================*/
    
        // Obtener información de un solo archivo o todos los archivos de un cliente
        public function getInfoFileClient($type,$id,$id_Archivo = false, $ult = false) {
            $RUTATEMP = json_encode(FOLDERS_PATH);

            $and_file = '';

            if($type){
                $and_file .= 'AND archivo_organizado.ID_TIPO_DOC = :doc';
            }

            if($id_Archivo){
                $and_file .= ' AND archivo_organizado.ID = :id_Archivo';
            }

            $sql = "SELECT DISTINCT
                        archivo_organizado.id AS ID_ARCHIVO,
                        TD.codigo AS CODIGO_CLIENTE,
                        Cliente.documento AS DOCUMENTO_CLIENTE, 
                        REPLACE(CONCAT(archivo_organizado.CARPETA,'/',archivo_organizado.NOMBRE_ARCHIVO),'\\\\','/') AS RUTA_ARCHIVO,
                        REPLACE(REPLACE(archivo_organizado.CARPETA,$RUTATEMP,''),'\\\\','/') AS FOLDER_ARCHIVO,
                        archivo_organizado.NOMBRE_ARCHIVO AS NOMBRE_ARCHIVO,
                        archivo_organizado.ID_TIPO_DOC AS TIPO_DOCUMENTO,
                        archivo_organizado.FECHA_ROBOT AS FECHA_INGRESO_ACHIVO
                    FROM archivo_organizado
                        INNER JOIN clientes Cliente 
                            ON archivo_organizado.NUMERO_IDENT_CLIENTE = Cliente.documento
                        INNER JOIN tipos_documentos TD
                            ON Cliente.tipo_documento = TD.id
                    WHERE 
                        Cliente.id = :id                         
                        {$and_file}";

            $result = $this->_db->prepare($sql);
            $result->bindValue(":id", $id);

            if($type)
                $result->bindValue(":doc", $type);

            if($id_Archivo)
                $result->bindValue(":id_Archivo", $id_Archivo);
            
            $resultado = $result->execute();

            if(!is_null($result->errorInfo()[2]))
                return array('error' => $result->errorInfo()[2]);
            else
                if($ult){
                    $resultado_data = $result->fetchAll(PDO::FETCH_ASSOC);
                    $ultimo_data = count($resultado_data)-1;
                    return ((bool)$resultado_data) ? $resultado_data[$ultimo_data] : false;
                }else{
                    return $result->fetchAll(PDO::FETCH_ASSOC);
                }
        }

        // Obtiene la información de un solo archivo de un cliente por el id
        public function getInfoFileByClientId($cliente_id,$type_doc) {
            $RUTATEMP = json_encode(FOLDERS_PATH);
            
            $sql = "SELECT
                        TD.codigo AS CODIGO_CLIENTE,
                        Cliente.documento AS DOCUMENTO_CLIENTE, 
                        REPLACE(CONCAT(archivo_organizado.CARPETA,'/',archivo_organizado.NOMBRE_ARCHIVO),'\\\\','/') AS RUTA_ARCHIVO,
                        REPLACE(REPLACE(archivo_organizado.CARPETA,$RUTATEMP,''),'\\\\','/') AS FOLDER_ARCHIVO,
                        archivo_organizado.NOMBRE_ARCHIVO AS NOMBRE_ARCHIVO,
                        archivo_organizado.ID_TIPO_DOC AS TIPO_DOCUMENTO,
                        archivo_organizado.FECHA_ROBOT AS FECHA_INGRESO_ACHIVO
                    FROM archivo_organizado
                        INNER JOIN clientes Cliente 
                            ON archivo_organizado.NUMERO_IDENT_CLIENTE = Cliente.documento
                        INNER JOIN tipos_documentos TD
                            ON Cliente.tipo_documento = TD.id
                    WHERE
                        Cliente.id = :cliente_id
                        AND archivo_organizado.ID_TIPO_DOC = :type_doc
                        AND IF(archivo_organizado.radicacion_id <> 0, 
                            archivo_organizado.radicacion_id = (
                                SELECT 
                                ZR.id 
                                FROM zr_radicacion ZR 
                                WHERE 
                                ZR.repetido = 0
                                AND ZR.cliente_id = Cliente.id
                                AND ZR.created = (SELECT MAX(B.created) FROM zr_radicacion B WHERE B.cliente_id = ZR.cliente_id AND B.repetido = 0)
                            ),
                            !IFNULL(archivo_organizado.radicacion_id, 0)
                        )
                    ";

            $result = $this->_db->prepare($sql);
            $result->bindValue(":cliente_id", $cliente_id);
            $result->bindValue(":type_doc", $type_doc);
            $resultado = $result->execute();

            if(!is_null($result->errorInfo()[2]))
                return array('error' => $result->errorInfo()[2]);
            else
                return $result->fetch(PDO::FETCH_ASSOC);
        }
    
    /*=====  End of FUNCIONES TABLA archivo_organizado  ======*/ 

    /*==========================================
    =            FUNCIONES TABLA clientes       =
    ============================================*/
    
        //Guarda un cliente en la base de datos
        public function saveClient($data) {
            foreach ($data as $KeyDataSQL => $valueDataSQL) {
                $valuesColumnsSQL[":".$KeyDataSQL] = $valueDataSQL;
            }

            $columnsSQL = implode(",", array_keys($data));
            $dataSQL = implode(",", array_keys($valuesColumnsSQL));
            $sql  = "INSERT INTO clientes ({$columnsSQL}) VALUES ({$dataSQL})";

            $result = $this->_db->prepare($sql);
            foreach ($valuesColumnsSQL as $keyData => $valueData) {
                if(is_numeric($valueData)){
                    $param = PDO::PARAM_INT;
                }elseif(is_bool($valueData)){
                    $param = PDO::PARAM_BOOL;
                }elseif(is_null($valueData)){
                    $param = PDO::PARAM_INT;
                }elseif(is_string($valueData)){
                    $param = PDO::PARAM_STR;
                }else{
                    $param = FALSE;
                }

                if($param)
                    $result->bindValue($keyData,$valueData,$param);
            }

            $resultado = $result->execute();

            if(!is_null($result->errorInfo()[2]))
                return array('error' => $result->errorInfo()[2]);
            else
                return $resultado;
        }

        // Obtiene Tipo de Cliente Por ID
        public function getTypeClienteID($id) {
            $sql = "SELECT 
                        TD.id AS tipo_documento,
                        TD.codigo AS CODIGO_CLIENTE, 
                        TD.tipo_persona AS TIPO_PERSONA
                    FROM clientes Cliente 
                        INNER JOIN tipos_documentos TD ON Cliente.tipo_documento = TD.id  
                    WHERE Cliente.id = :id";

            $result = $this->_db->prepare($sql);
            $result->bindValue(":id", $id);

            $resultado = $result->execute();

            if(!is_null($result->errorInfo()[2]))
                return array('error' => $result->errorInfo()[2]);
            else
                return $result->fetch(PDO::FETCH_ASSOC);;
        }

        // Obtiene el id del cliente por el N de documento
        public function getClienteIDByDocument($doc) {

            $result = $this->_db->prepare("SELECT id FROM clientes WHERE documento = :doc");
            $result->bindValue(":doc", $doc);
            $result->execute();

            if(!is_null($result->errorInfo()[2]))
                return array('error' => $result->errorInfo()[2]);
            else
                return $result->fetch(PDO::FETCH_ASSOC);   
        }

    /*=====  End of FUNCIONES TABLA clientes  ======*/

    /*=================================================================================================
    =            FUNCIONES TABLAS clientes_sarlaft_natural y clientes_sarlaft_juridico            =
    =================================================================================================*/

        public function getInfoClienteNaturalByClienteId($cliente_id){
            $sql = "SELECT * FROM cliente_sarlaft_natural WHERE cliente = :cliente_id";
            $result = $this->_db->prepare($sql);
            $result->bindValue(":cliente_id", $cliente_id);
            $resultado = $result->execute();
            if(!is_null($result->errorInfo()[2]))
                return array('error' => $result->errorInfo()[2]);
            else
                return $result->fetch(PDO::FETCH_ASSOC);
        }

        public function getInfoClienteJuridicoByClienteId($cliente_id){
            $sql = "SELECT * FROM cliente_sarlaft_juridico WHERE cliente = :cliente_id";

            $result = $this->_db->prepare($sql);
            $result->bindValue(":cliente_id", $cliente_id);
            $resultado = $result->execute();

            if(!is_null($result->errorInfo()[2]))
                return array('error' => $result->errorInfo()[2]);
            else
                return $result->fetch(PDO::FETCH_ASSOC);
        }

        //Guardar información de clientes naturales y juridicos en la tabla especifica
        public function insertClient($type,$data) {
            if($type == "NAT"){
                $table = "cliente_sarlaft_natural";
            }else if($type == "JUR"){
                $table = "cliente_sarlaft_juridico";
            }
            foreach ($data as $KeyDataSQL => $valueDataSQL) {
                $valuesColumnsSQL[":".$KeyDataSQL] = $valueDataSQL;
            }

            $columnsSQL = implode(",", array_keys($data));
            $dataSQL = implode(",", array_keys($valuesColumnsSQL));
            $sql  = "INSERT INTO {$table} ({$columnsSQL}) VALUES ({$dataSQL})";

            $result = $this->_db->prepare($sql);
            foreach ($valuesColumnsSQL as $keyData => $valueData) {
                if(is_numeric($valueData)){
                    $param = PDO::PARAM_INT;
                }elseif(is_bool($valueData)){
                    $param = PDO::PARAM_BOOL;
                }elseif(is_null($valueData)){
                    $param = PDO::PARAM_INT;
                }elseif(is_string($valueData)){
                    $param = PDO::PARAM_STR;
                }else{
                    $param = FALSE;
                }

                if($param)
                    $result->bindValue($keyData,$valueData,$param);
            }

            $resultado = $result->execute();

            if(!is_null($result->errorInfo()[2]))
                return array('error' => $result->errorInfo()[2]);
            else
                return $resultado;
        }

        //Actualiza la información un formulario cliente sarlaft
        public function updateClient($type,$data){

            if($type == "NAT"){
                $table = "cliente_sarlaft_natural";
            }else if($type == "JUR"){
                $table = "cliente_sarlaft_juridico";
            }else{
                return array('error' => 'No se especifico el tipo de cliente para la actualizacion');
            }

            foreach ($data as $KeyDataSQL => $valueDataSQL) {
                $valuesColumnsSQL[":".$KeyDataSQL] = $valueDataSQL;
            }

            foreach ($data as $KeyDataSQL => $valueDataSQL) {
                $valuesColumnsSQL[":".$KeyDataSQL] = $valueDataSQL;
                if($KeyDataSQL != 'id'){
                    $salidaSQL[] = $KeyDataSQL."=".":".$KeyDataSQL;
                }
            }

            $salidaSQL = implode(",", $salidaSQL);

            $sql  = "UPDATE {$table} SET {$salidaSQL} WHERE {$table}.id=:id";

            $result = $this->_db->prepare($sql);

            foreach ($valuesColumnsSQL as $keyData => $valueData) {

                if(is_numeric($valueData)){
                    $param = PDO::PARAM_INT;
                }elseif(is_bool($valueData)){
                    $param = PDO::PARAM_BOOL;
                }elseif(is_null($valueData)){
                    $param = PDO::PARAM_INT;
                }elseif(is_string($valueData)){
                    $param = PDO::PARAM_STR;
                }else{
                    $param = FALSE;
                }

                if($param)
                    $result->bindValue($keyData,$valueData,$param);
            }

            $resultado = $result->execute();

            if(!is_null($result->errorInfo()[2]))
                return array('error' => $result->errorInfo()[2]);
            else
                return $resultado;
        }
    
    /*=====  End of FUNCIONES TABLAS clientes_sarlaft_natural y clientes_sarlaft_juridico  ======*/


    /*=================================================================
    =            FUNCIONES CON LOS ANEXOS clientes_sarlaft            =
    =================================================================*/
    
        // Guarda los anexos del cliente natural o juridico
        public function saveAnexos($table, $data, $last_id = false) {
            foreach ($data as $KeyDataSQL => $valueDataSQL) {
                $valuesColumnsSQL[":".$KeyDataSQL] = $valueDataSQL;
            }

            $columnsSQL = implode(",", array_keys($data));
            $dataSQL = implode(",", array_keys($valuesColumnsSQL));
            $sql  = "INSERT INTO {$table} ({$columnsSQL}) VALUES ({$dataSQL})";

            $result = $this->_db->prepare($sql);
            foreach ($valuesColumnsSQL as $keyData => $valueData) {
                if(is_numeric($valueData)){
                    $param = PDO::PARAM_INT;
                }elseif(is_bool($valueData)){
                    $param = PDO::PARAM_BOOL;
                }elseif(is_null($valueData)){
                    $param = PDO::PARAM_INT;
                }elseif(is_string($valueData)){
                    $param = PDO::PARAM_STR;
                }else{
                    $param = FALSE;
                }

                if($param)
                    $result->bindValue($keyData,$valueData,$param);
            }

            $resultado = $result->execute();

            if(!is_null($result->errorInfo()[2])) {
                return $result->errorInfo()[2];
            } else {
                if ($last_id) {
                    $result = $this->_db->prepare('SELECT LAST_INSERT_ID() AS LAST_ID');
                    $result->execute();
                    return $result->fetch(PDO::FETCH_ASSOC);
                }
                return $resultado;
            }
        }

        // Actualiza la informacion de los anexos del cliente por el id
        public function updateAnexos($table,$data) {

            foreach ($data as $KeyDataSQL => $valueDataSQL) {
                $valuesColumnsSQL[":".$KeyDataSQL] = $valueDataSQL;
                if($KeyDataSQL != 'id'){
                    $salidaSQL[] = $KeyDataSQL."=".":".$KeyDataSQL;
                }
            }

            $salidaSQL = implode(",", $salidaSQL);

            $sql  = "UPDATE {$table} SET {$salidaSQL} WHERE {$table}.id = :id";

            $result = $this->_db->prepare($sql);

            foreach ($valuesColumnsSQL as $keyData => $valueData) {

                if(is_numeric($valueData)){
                    $param = PDO::PARAM_INT;
                }elseif(is_bool($valueData)){
                    $param = PDO::PARAM_BOOL;
                }elseif(is_null($valueData)){
                    $param = PDO::PARAM_INT;
                }elseif(is_string($valueData)){
                    $param = PDO::PARAM_STR;
                }else{
                    $param = FALSE;
                }

                if($param)
                    $result->bindValue($keyData,$valueData,$param);
            }


            $resultado = $result->execute();

            if(!is_null($result->errorInfo()[2]))
                return array('error' => $result->errorInfo()[2]);
            else
                return $resultado;
        }

        // Obtiene la informacion de anexos PPE de un cliente por el id
        public function getAllAnexosPPEClientById($cliente_id) {

            $sql = "SELECT * FROM zr_anexos_ppes WHERE cliente_id = :cliente_id";
            $result = $this->_db->prepare($sql);
            $result->bindValue(":cliente_id",$cliente_id,PDO::PARAM_INT);
            $resultado = $result->execute();

            if(!is_null($result->errorInfo()[2]))
                return array('error' => $result->errorInfo()[2]);
            else
                return $result->fetchAll(PDO::FETCH_ASSOC);
        }

        // Obtiene la informacion de un anexo PPE por el cliente id , ppes_tipo_identificacion, ppes_no_documento
        public function getAnexoPPE($cliente_id,$ppes_tipo_identificacion,$ppes_no_documento) {
            $sql = "SELECT
                        *
                    FROM
                        zr_anexos_ppes
                    WHERE
                        cliente_id = :cliente_id
                    AND ppes_tipo_identificacion = :ppes_tipo_identificacion
                    AND ppes_no_documento = :ppes_no_documento";

            $resul = $this->_db->prepare($sql);
            $resul->bindValue(":cliente_id", $cliente_id,PDO::PARAM_INT);
            $resul->bindValue(":ppes_tipo_identificacion", $ppes_tipo_identificacion,PDO::PARAM_INT);
            $resul->bindValue(":ppes_no_documento", $ppes_no_documento);
            $resul->execute();

            return $resul->fetch(PDO::FETCH_ASSOC);
        }

        // Obtiene la informacion de accionistas por el cliente id
        public function getAccionistasClienteById($cliente_id) {
            $resul = $this->_db->prepare("SELECT * FROM accionistas WHERE cliente_id = :cliente_id ORDER BY id ASC");
            $resul->bindValue(":cliente_id", $cliente_id);
            $resul->execute();

            return $resul->fetchAll(PDO::FETCH_ASSOC);
        }

        // Obtiene la informacion de un anexo PPE por el cliente id , accionista_tipo_documento, accionista_documento
        public function getAnexoAccionista($cliente_id,$accionista_tipo_documento,$accionista_documento) {
            $sql = "SELECT
                        *
                    FROM
                        accionistas
                    WHERE
                        cliente_id = :cliente_id
                    AND accionista_tipo_documento = :accionista_tipo_documento
                    AND accionista_documento = :accionista_documento";

            $resul = $this->_db->prepare($sql);
            $resul->bindValue(":cliente_id", $cliente_id,PDO::PARAM_INT);
            $resul->bindValue(":accionista_tipo_documento", $accionista_tipo_documento,PDO::PARAM_INT);
            $resul->bindValue(":accionista_documento", $accionista_documento);
            $resul->execute();

            return $resul->fetch(PDO::FETCH_ASSOC);
        }

        // Obtiene la informacion de subaccionista por el cliente id
        public function getSubAccionistasClienteById($cliente_id) {
            $sql = "SELECT * from sub_accionistas where cliente_id = :cliente_id";
            $result = $this->_db->prepare($sql);
            $result->bindValue(':cliente_id',$cliente_id,PDO::PARAM_INT);
            $result->execute();

            if(!is_null($result->errorInfo()[2]))
                return array('error' => $result->errorInfo()[2]);
            else
                return $result->fetchAll(PDO::FETCH_ASSOC);
        }

        // Obtiene la informacion de un anexo PPE por el cliente id , sub_accionista_tipo_documento, sub_accionista_numero_id
        public function getAnexoSubAccionista($cliente_id,$sub_accionista_tipo_documento,$sub_accionista_numero_id) {
            $sql = "SELECT
                        *
                    FROM
                        sub_accionistas
                    WHERE
                        cliente_id = :cliente_id
                    AND sub_accionista_tipo_documento = :sub_accionista_tipo_documento
                    AND sub_accionista_numero_id = :sub_accionista_numero_id";

            $resul = $this->_db->prepare($sql);
            $resul->bindValue(":cliente_id", $cliente_id,PDO::PARAM_INT);
            $resul->bindValue(":sub_accionista_tipo_documento", $sub_accionista_tipo_documento,PDO::PARAM_INT);
            $resul->bindValue(":sub_accionista_numero_id", $sub_accionista_numero_id);
            $resul->execute();

            return $resul->fetch(PDO::FETCH_ASSOC);
        }

        // Obtiene la informacion de los productos de cliente por el id
        public function getAllProductosClienteById($cliente_id) {
            $resul = $this->_db->prepare("SELECT * FROM productos WHERE cliente_id = :cliente_id ORDER BY id ASC");
            $resul->bindValue(":cliente_id", $cliente_id,PDO::PARAM_INT);
            $resul->execute();

            return $resul->fetchAll(PDO::FETCH_ASSOC);
        }

        // Obtiene la informacion de un anexo Producto por el cliente id , tipo_producto, identificacion_producto
        public function getAnexoProducto($cliente_id,$tipo_producto,$identificacion_producto) {
            $sql = "SELECT
                        *
                    FROM
                        sub_accionistas
                    WHERE
                        cliente_id = :cliente_id
                    AND tipo_producto = :tipo_producto
                    AND identificacion_producto = :identificacion_producto";

            $resul = $this->_db->prepare($sql);
            $resul->bindValue(":cliente_id", $cliente_id,PDO::PARAM_INT);
            $resul->bindValue(":tipo_producto", $tipo_producto,PDO::PARAM_INT);
            $resul->bindValue(":identificacion_producto", $identificacion_producto);
            $resul->execute();

            return $resul->fetch(PDO::FETCH_ASSOC);
        }
    
    /*=====  End of FUNCIONES CON LOS ANEXOS clientes_sarlaft  ======*/
    

    // Obtiene la informacion de tipos de documentos por tipo de persona
    public function getClientsTypePerson($type) {
        $resul = $this->_db->prepare("SELECT * FROM tipos_documentos WHERE tipo_persona = :type OR tipo_persona = 'UND'");
        $resul->bindValue(":type", $type);            
        $resul->execute();
        return $resul->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtiene la informacion de tipos de vinculacion
    public function getConnections() {
        $resul = $this->_db->prepare("SELECT * FROM vinculaciones ORDER BY id");        
        $resul->execute();

        return $resul->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtiene la informacion de relaciones de tomador - asegurado - beneficiario 
    public function getRelations() {
        $resul = $this->_db->prepare("SELECT * FROM  tipos_relaciones_tom_ase_bene ORDER BY id");
        $resul->execute();

        return $resul->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtiene la informacion de estados civiles
    public function getCivilStates() {
        $resul = $this->_db->prepare("SELECT * FROM estados_civiles ORDER BY id ASC");
        $resul->execute();

        return $resul->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtiene parametría de direcciones 
    public function getAddressParam() {
        $resul = $this->_db->prepare("SELECT * FROM direcciones_param ORDER BY code ASC");
        $resul->execute();

        return $resul->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtiene la informacion de actividades económicas
    public function getActividadesPrincipales() {
        $resul = $this->_db->prepare("SELECT * FROM tipos_actividades_principales ORDER BY id ASC");
        $resul->execute();

        return $resul->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtiene la informacion de tipos de actividad
    public function getTiposActividades() {
        $resul = $this->_db->prepare("SELECT * FROM tipos_actividades ORDER BY id ASC");
        $resul->execute();

        return $resul->fetchAll(PDO::FETCH_ASSOC); }

    // Obtiene la informacion de operaciones extranjeras
    public function getOperacionesMonedaExtranjera() {
        $resul = $this->_db->prepare("SELECT * FROM tipos_operaciones_moneda_extranjera ORDER BY id ASC");
        $resul->execute();

        return $resul->fetchAll(PDO::FETCH_ASSOC); }

    // Obtiene la informacion de sectores
    public function getSector() {
        $resul = $this->_db->prepare("SELECT * FROM sector ORDER BY id ASC");
        $resul->execute();

        return $resul->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtiene la informacion de tipos de sociedad
    public function getSocietyType() {
        $resul = $this->_db->prepare("SELECT * FROM tipos_sociedad ORDER BY id");

        $resul->execute();
        return $resul->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtiene la iformacion de las lineas de negocio
    public function getLineaNegocio(){
        $sql = "SELECT * FROM linea_negocio";
        $result = $this->_db->prepare($sql);
        $result->execute();
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtiene la informacion de las capturas que se han hecho -- no se esta utilizando
    public function getDataCapture($pendientes = false) {
        $sql = "SELECT
                    CONCAT(TD.codigo,'-',Cliente.documento) AS documento,
                    ClienteN_Sarlaft.*
                FROM zr_radicacion Radicacion
                    INNER JOIN cliente_sarlaft_natural ClienteN_Sarlaft ON ClienteN_Sarlaft.cliente = Radicacion.cliente_id
                    INNER JOIN clientes Cliente ON ClienteN_Sarlaft.cliente = Cliente.id
                    INNER JOIN tipos_documentos TD ON Cliente.tipo_documento = TD.id
                GROUP BY    Radicacion.cliente_id";

        $result = $this->_db->prepare($sql);
        $result->execute();

        return $result->fetchAll(PDO::FETCH_OBJ);
    }

    // Obtiene la informacion de la tipoligias
    public function getTipologies() {
        $sql = "SELECT * FROM zr_tipologias";
        $result = $this->_db->prepare($sql);
        $resultado = $result->execute();

        if(!is_null($result->errorInfo()[2]))
            return $result->errorInfo()[2];
        else
            return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtiene los documentos requeridos dependiendo de la linea negocio y el tipo de cliente
    public function getDocumentosRequeridosLineaNegocio($linea_negocio,$tipo_cliente) {
        $sql = "SELECT 
                    documento_requerido AS DOCUMENTO_REQUERIDO
                FROM documentos_requeridos
                WHERE linea_negocio = :linea_negocio AND tipo_cliente = :tipo_cliente";

        $result = $this->_db->prepare($sql);
        $result->bindValue(":linea_negocio",$linea_negocio,PDO::PARAM_INT);
        $result->bindValue(":tipo_cliente",$tipo_cliente,PDO::PARAM_STR);
        $result->execute();

        if(!is_null($result->errorInfo()[2]))
            return $result->errorInfo()[2];
        else
            return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    // Verifica si el cliente tiene un archivo pendiente en la plataforma
    public function VerifyFilePendientClient($cliente,$file) {
        $sql = "SELECT
                    COUNT(id) AS DOCUMENTO_EXISTE
                FROM 
                    zr_clientes_pendientes_documentos
                WHERE 
                    CLIENTE_ID = :cliente_id
                    AND DOCUMENTO_PENDIENTE_ID = :tipo_doc
                    AND RECIBIDO = 0";

        $result = $this->_db->prepare($sql);
        $result->bindValue(':cliente_id',$cliente,PDO::PARAM_INT);
        $result->bindValue(':tipo_doc',$file,PDO::PARAM_INT);

        $resultado = $result->execute();

        if(!is_null($result->errorInfo()[2]))
            return array('error' => $result->errorInfo()[2]);
        else
            return $result->fetch(PDO::FETCH_ASSOC);
    }

    public function DeleteFilePendienteClienteById($cliente_id,$file_id){
        $sql = "DELETE FROM zr_clientes_pendientes_documentos WHERE CLIENTE_ID = :cliente_id AND DOCUMENTO_PENDIENTE_ID = :file_id";

        $result = $this->_db->prepare($sql);
        $result->bindValue(':cliente_id',$cliente_id,PDO::PARAM_INT);
        $result->bindValue(':file_id',$file_id,PDO::PARAM_INT);

        $resultado = $result->execute();

        if(!is_null($result->errorInfo()[2]))
            return $result->errorInfo()[2];
        else
            return $resultado;
    }

    // Obtiene los documentos pendientes del cliente en la plataforma
    public function getAllFilesPendientClient($cliente) {
        $sql = "SELECT 
                    DOCUMENTO_PENDIENTE_ID AS DOCUMENTOS_PENDIENTES_ID,
                    zr_tipo_documento.codigo AS DOCUMENTOS_PENDIENTES_CODIGO
                FROM 
                    zr_clientes_pendientes_documentos
                    INNER JOIN zr_tipo_documento 
                        ON zr_clientes_pendientes_documentos.DOCUMENTO_PENDIENTE_ID = zr_tipo_documento.id
                WHERE 
                    CLIENTE_ID = :cliente_id
                    AND RECIBIDO = 0";

        $result = $this->_db->prepare($sql);
        $result->bindValue(':cliente_id',$cliente,PDO::PARAM_INT);

        $resultado = $result->execute();

        if(!is_null($result->errorInfo()[2]))
            return $result->errorInfo()[2];
        else
            return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtiene todas las lineas de negocio del cliente
    public function getLineaNegocioCliente($cliente_id) {
        $sql = "SELECT DISTINCT
                    linea_negocio_id AS ID_LINEA
                FROM
                    zr_radicacion
                WHERE
                    zr_radicacion.formulario_sarlaft = 1
                    AND zr_radicacion.radicacion_proceso = 'LEGAL'
                    AND zr_radicacion.linea_negocio_id != 3
                    AND cliente_id = :cliente_id";
        $result = $this->_db->prepare($sql);
        $result->bindValue(':cliente_id',$cliente_id,PDO::PARAM_INT);
        $result->execute();
        return $result->fetchAll(PDO::FETCH_COLUMN);
    }

    //Obtiene las lineas de negocio que tiene un cliente en la plataforma diferentes a 3 - SIN LINEA
    public function getAllLineaNegocioClienteBy($cliente) {
        $sql = "SELECT DISTINCT
                    linea_negocio_id AS LINEA_NEGOCIO_ID,
                    linea_negocio.NOMBRE AS NOMBRE_LINEA_NEGOCIO
                FROM
                    zr_radicacion
                INNER JOIN linea_negocio ON linea_negocio.ID_LINEA = zr_radicacion.linea_negocio_id
                    AND zr_radicacion.formulario_sarlaft = 1
                    AND zr_radicacion.radicacion_proceso = 'LEGAL'
                    AND zr_radicacion.linea_negocio_id != 3
                WHERE
                    zr_radicacion.cliente_id = :cliente_id";

        $result = $this->_db->prepare($sql);
        $result->bindValue(':cliente_id',$cliente,PDO::PARAM_INT);

        $resultado = $result->execute();

        if(!is_null($result->errorInfo()[2]))
            return $result->errorInfo()[2];
        else
            return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    //Obtiene el estado del proceso en el que se encuentra un cliente
    public function getUltEstadoProcesoByClienteId($cliente_id) {

        $sql = "SELECT 
                    ES.desc_type AS ESTADO_PROCESO_CLIENTE_SARLAFT,
                    ES.id AS ESTADO_PROCESO_CLIENTE_SARLAFT_ID
                FROM zr_estado_proceso_clientes_sarlaft CS
                    INNER JOIN estados_sarlaft ES ON ES.id = CS.estado_formulario_id
                WHERE
                    CS.cliente_id = :cliente_id";
                    
        $result = $this->_db->prepare($sql);
        $result->bindValue(':tp_cliente',$tp_cliente);
        $result->bindValue(':cliente_id',$cliente,PDO::PARAM_INT);

        $resultado = $result->execute();

        if(!is_null($result->errorInfo()[2]))
            return $result->errorInfo()[2];
        else
            return $result->fetch(PDO::FETCH_ASSOC);
    }

    //Obtiene el estado del proceso en el que se encuentra un cliente
    public function getEstadoProcesoCliente($cliente_id) {
        $sql = "SELECT 
                    ES.desc_type AS ESTADO_PROCESO_CLIENTE_SARLAFT,
                    ES.id AS ESTADO_PROCESO_CLIENTE_SARLAFT_ID
                FROM zr_estado_proceso_clientes_sarlaft CS
                    INNER JOIN estados_sarlaft ES ON ES.id = CS.estado_formulario_id
                WHERE
                    CS.cliente_id = :cliente_id";
                    
        $result = $this->_db->prepare($sql);
        $result->bindValue(':cliente_id',$cliente,PDO::PARAM_INT);

        $resultado = $result->execute();

        if(!is_null($result->errorInfo()[2]))
            return $result->errorInfo()[2];
        else
            return $result->fetch(PDO::FETCH_ASSOC);
    }

    //Obtiene la informacion de las gestiones que se le han realizado a un cliente en un fehca especifica
    public function getAllGestionesCompletitudVerificacion($cliente_id,$fecha_diligenciamiento) {

        $sql = "SELECT
                    *
                FROM 
                    gestion_clientes_completitud_verificacion GCCV
                WHERE 
                    GCCV.GESTION_CLIENTE_ID = :cliente_id
                    AND GCCV.GESTION_FECHA_DILIGENCIAMIENTO = :fecha_diligenciamiento
                ";

        $result = $this->_db->prepare($sql);
        $result->bindValue(':cliente_id',$cliente_id,PDO::PARAM_INT);
        $result->bindValue(':fecha_diligenciamiento',$fecha_diligenciamiento);
        $resultado = $result->execute();

        if(!is_null($result->errorInfo()[2]))
            return array('error' => $result->errorInfo()[2]);
        else
            return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    //Obtiene la informacion de las gestiones que se le han realizado a un cliente en completitud por fecha de diligenciamiento
    public function getGestionCompletitud($cliente_id,$fecha_diligenciamiento) {

        $sql = "SELECT
                    *
                FROM 
                    gestion_clientes_completitud_verificacion GCCV
                WHERE 
                    GCCV.GESTION_CLIENTE_ID = :cliente_id
                    AND GCCV.FECHA_GESTION = (
                        SELECT MAX(GCCV1.FECHA_GESTION)
                            FROM gestion_clientes_completitud_verificacion GCCV1 
                        WHERE 
                            GCCV1.GESTION_CLIENTE_ID = GCCV.GESTION_CLIENTE_ID 
                            AND GCCV1.GESTION_FECHA_DILIGENCIAMIENTO = :fecha_diligenciamiento
                            AND GCCV1.GESTION_PROCESO_ID = 6
                    )";

        $result = $this->_db->prepare($sql);
        $result->bindValue(':cliente_id',$cliente_id,PDO::PARAM_INT);
        $result->bindValue(':fecha_diligenciamiento',$fecha_diligenciamiento);
        $resultado = $result->execute();

        if(!is_null($result->errorInfo()[2]))
            return array('error' => $result->errorInfo()[2]);
        else
            return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    //Obtiene la informacion de las gestiones que se le han realizado a un cliente en verifcacion por fecha de diligenciamiento
    public function getGestionVerificacion($cliente_id,$fecha_diligenciamiento) {

        $sql = "SELECT
                    *
                FROM 
                    gestion_clientes_completitud_verificacion GCCV
                WHERE 
                    GCCV.GESTION_CLIENTE_ID = :cliente_id
                    AND GCCV.FECHA_GESTION = (
                        SELECT MAX(GCCV1.FECHA_GESTION)
                            FROM gestion_clientes_completitud_verificacion GCCV1 
                        WHERE 
                            GCCV1.GESTION_CLIENTE_ID = GCCV.GESTION_CLIENTE_ID 
                            AND GCCV1.GESTION_FECHA_DILIGENCIAMIENTO = :fecha_diligenciamiento
                            AND GCCV1.GESTION_PROCESO_ID = 5
                        )";

        $result = $this->_db->prepare($sql);
        $result->bindValue(':cliente_id',$cliente_id,PDO::PARAM_INT);
        $result->bindValue(':fecha_diligenciamiento',$fecha_diligenciamiento);
        $resultado = $result->execute();

        if(!is_null($result->errorInfo()[2]))
            return array('error' => $result->errorInfo()[2]);
        else
            return $result->fetch(PDO::FETCH_ASSOC);
    }

    //Obtiene la ultima fecha en la que se gestiono el cliente en la plataforma
    public function getUltFechaGestionClienteById($cliente_id,$formulario_id,$fecha_diligenciamiento) {
        $sql = "SELECT
                    MAX(PROCESO_FECHA_DILIGENCIAMIENTO) AS ULT_FECHA_GESTION_FORMULARIO
                FROM
                    zr_estado_proceso_clientes_sarlaft
                WHERE
                    PROCESO_CLIENTE_ID  = :cliente_id
                AND PROCESO_FORMULARIO_ID = :formulario_id
                AND PROCESO_FECHA_DILIGENCIAMIENTO = :fecha_diligenciamiento";

        $result = $this->_db->prepare($sql);
        $result->bindValue(':cliente_id',$cliente_id,PDO::PARAM_INT);
        $result->bindValue(':formulario_id',$formulario_id,PDO::PARAM_INT);
        $result->bindValue(':fecha_diligenciamiento',$fecha_diligenciamiento);
        $result->execute();

        if(!is_null($result->errorInfo()[2]))
            return array('error' => $result->errorInfo()[2]);
        else
            return $result->fetch(PDO::FETCH_ASSOC);
    }

    public function getInfoActividadPrincipalByCondition($condition){
        $sql = "SELECT
                    *
                FROM
                    tipos_actividades_principales
                WHERE
                    {$condition['condition']} = :{$condition['condition']}";
        $result = $this->_db->prepare($sql);
        $result->bindValue(":{$condition['condition']}",$condition['data']);
        $result->execute();
        return $result->fetch(PDO::FETCH_ASSOC);
    }

     //Verifica los campos requeridos para terminar un proceso y salta los pasos de completitud y verificación
    public function camposRequeridos($tipoCliente) {

        if($tipoCliente == 'NAT'){

            return array(
                'primer_apellido',
                'primer_nombre',
                'correo_electronico',
                'direccion_residencia',
                'departamento_residencia',
                'ciudad_residencia',
                'lugar_expedicion_documento',
                'fecha_expedicion_documento',
                'fecha_nacimiento',
                'lugar_nacimiento',
                'ocupacion',
                'nacionalidad_1',
                'celular',
                'actividad_eco_principal',
                'ingresos',
                'egresos',
                'activos',
                'pasivos',
                'patrimonio',
                'declaracion_origen_fondos'
            );
        }

        if($tipoCliente == 'JUR'){

            return array(
                'razon_social',
                'info_basica_tipo_sociedad',
                'info_basica_digito_verificacion',
                'ofi_principal_direccion',
                'ofi_principal_tipo_empresa',
                'ofi_principal_ciiu',
                'ofi_principal_ciiu_cod',
                'ofi_principal_sector',
                'ofi_principal_departamento_empresa',
                'ofi_principal_ciudad_empresa',
                'ofi_principal_telefono',
                'ofi_principal_email',
                'rep_legal_primer_apellido',
                'rep_legal_nombres',
                'rep_legal_tipo_documento',
                'rep_legal_documento',
                'rep_legal_fecha_exp_documento',
                'rep_legal_lugar_expedicion',
                'rep_legal_fecha_nacimiento',
                'rep_legal_lugar_nacimiento',
                'rep_legal_nacionalidad_1',
                'anexo_accionistas',
                'anexo_sub_accionistas',
                'ingresos',
                'egresos',
                'activos',
                'pasivos',
                'patrimonio',
                'declaracion_origen_fondos'
            );
        }
    }

    public function UltFechaProcesoSarlaft($type = false, $documento_cliente){

        if($type == 'NAT'){
            $sql = "SELECT DISTINCT
                        IFNULL(MAX(DATE(ZEPCS.FECHA_PROCESO)),  MAX(DATE(ZR.fecha_diligenciamiento))) AS FECHA_ULT_ACTUALIZACION,
                        CONCAT(CN.primer_nombre,' ',CN.primer_apellido) AS NOMBRE_CLIENTE,
                        TP.descripcion AS TIPO_DOCUMENTO
                    FROM 
                        clientes
                    INNER JOIN tipos_documentos TP ON TP.id = clientes.tipo_documento
                    INNER JOIN zr_radicacion ZR ON ZR.cliente_id = clientes.id
                    LEFT JOIN cliente_sarlaft_natural CN ON CN.cliente = ZR.cliente_id
                    LEFT JOIN zr_estado_proceso_clientes_sarlaft ZEPCS ON ZEPCS.PROCESO_CLIENTE_ID = ZR.cliente_id
                        AND ZEPCS.PROCESO_FECHA_DILIGENCIAMIENTO = (
                            SELECT 
                                MAX(ZR2.fecha_diligenciamiento)
                            FROM
                                zr_radicacion ZR2
                            WHERE
                                ZR2.cliente_id = ZR.cliente_id
                        )
                        AND ZEPCS.PROCESO_INOUTBOUND = 'INBOUND'
                    WHERE
                        clientes.documento = :documento_cliente";
        }else{

            $sql = "SELECT DISTINCT
                        IFNULL(MAX(DATE(ZEPCS.FECHA_PROCESO)),  MAX(DATE(ZR.fecha_diligenciamiento))) AS FECHA_ULT_ACTUALIZACION,
                        CJ.razon_social AS NOMBRE_CLIENTE,
                        TP.descripcion AS TIPO_DOCUMENTO
                    FROM 
                        clientes
                    INNER JOIN tipos_documentos TP ON TP.id = clientes.tipo_documento
                    INNER JOIN zr_radicacion ZR ON ZR.cliente_id = clientes.id
                    LEFT JOIN cliente_sarlaft_juridico CJ ON CJ.cliente = ZR.cliente_id
                    LEFT JOIN zr_estado_proceso_clientes_sarlaft ZEPCS ON ZEPCS.PROCESO_CLIENTE_ID = ZR.cliente_id
                        AND ZEPCS.PROCESO_FECHA_DILIGENCIAMIENTO = (
                            SELECT 
                                MAX(ZR2.fecha_diligenciamiento)
                            FROM
                                zr_radicacion ZR2
                            WHERE
                                ZR2.cliente_id = ZR.cliente_id
                        )
                        AND ZEPCS.PROCESO_INOUTBOUND = 'INBOUND'
                    WHERE
                        clientes.documento = :documento_cliente";
        }

        $result = $this->_db->prepare($sql);
        $result->bindValue(':documento_cliente',$documento_cliente);
        $resultado = $result->execute();

        if(!is_null($result->errorInfo()[2]))
            return array('error' => $result->errorInfo()[2]);
        else
            return $result->fetch(PDO::FETCH_ASSOC);
    }

    //Obtener numero de radicacion por cliente y formulario
    public function getRadicacionId($id, $fecha_diligenciamiento){
        $sql = "SELECT ZR.id 
                FROM zr_radicacion ZR
                LEFT JOIN zr_estado_proceso_clientes_sarlaft ZEPCS
                ON ZEPCS.PROCESO_CLIENTE_ID = ZR.cliente_id
                AND ZEPCS.PROCESO_FECHA_DILIGENCIAMIENTO = ZR.fecha_diligenciamiento
                WHERE ZR.repetido = 0
                AND ZR.cliente_id = :id AND ZR.fecha_diligenciamiento = :fecha_diligenciamiento
                GROUP BY ZR.id";
        $result = $this->_db->prepare($sql);

        $result->bindValue(":id", $id);
        $result->bindValue(":fecha_diligenciamiento", $fecha_diligenciamiento);

        $resultado = $result->execute();

        if(!is_null($result->errorInfo()[2]))
            return array('error' => $result->errorInfo()[2]);
        else
            return $result->fetch(PDO::FETCH_ASSOC);
    }
    //Obtiene la informacion de vinculos relacion dependiendo del tipo de cliente
    public function getVinculoRelacion($Tipo){

        if($Tipo == "Natural"){
            $result = $this->_db->prepare("SELECT valor_dos AS vinculo_relacion FROM multi_param WHERE nombre_parametro = 'vinculo_relacion'");
        } 
        else if ($Tipo == "Juridico"){
            $result = $this->_db->prepare("SELECT valor_dos AS vinculo_relacion FROM multi_param WHERE nombre_parametro = 'vinculo_relacion_juridico'");
        }
        $result->execute();

        if(!is_null($result->errorInfo()[2]))
            return array('error' => $result->errorInfo()[2]);
        else
            return $result->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getUltimaExpedicionDoc($idCliente) {
        $sql = "SELECT NOMBRE_ARCHIVO, FECHA_EMISION FROM relacion_archivo_radicacion WHERE CLIENTE_ID = :id_cliente";
        $result = $this->_db->prepare($sql);

        $result->bindValue(":id_cliente", $idCliente);

        $resultado = $result->execute();

        if(!is_null($result->errorInfo()[2]))
            return array('error' => $result->errorInfo()[2]);
        else
            return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getVerificadoSarlaftJuridico($id) {
        $sql = "SELECT * FROM cliente_sarlaft_juridico_verificado WHERE cliente_sarlaft_juridico_id = :id_juridico";
        $result = $this->_db->prepare($sql);

        $result->bindValue(":id_juridico", $id);

        $resultado = $result->execute();

        if(!is_null($result->errorInfo()[2]))
            return array('error' => $result->errorInfo()[2]);
        else
            return $result->fetch(PDO::FETCH_ASSOC);
    }

    public function getVerificadoSarlaftNatural($id) {
        $sql = "SELECT * FROM cliente_sarlaft_natural_verificado WHERE cliente_sarlaft_natural_id = :id_natural";
        $result = $this->_db->prepare($sql);

        $result->bindValue(":id_natural", $id);

        $resultado = $result->execute();

        if(!is_null($result->errorInfo()[2]))
            return array('error' => $result->errorInfo()[2]);
        else
            return $result->fetch(PDO::FETCH_ASSOC);
    }

    public function getVerificadoAccionistas($id) {
        $sql = "SELECT * FROM accionistas_verificado WHERE accionista_id = :id_accionista";
        $result = $this->_db->prepare($sql);

        $result->bindValue(":id_accionista", $id);

        $resultado = $result->execute();

        if(!is_null($result->errorInfo()[2]))
            return array('error' => $result->errorInfo()[2]);
        else
            return $result->fetch(PDO::FETCH_ASSOC);
    }

    //Obtiene los tipos de empresa
    public function getTipoEmpresa(){

        $result = $this->_db->prepare("SELECT valor_dos AS tipo_empresa FROM multi_param WHERE nombre_parametro = 'tipo_empresa'");
         
        $result->execute();

        if(!is_null($result->errorInfo()[2]))
            return array('error' => $result->errorInfo()[2]);
        else
            return $result->fetchAll(PDO::FETCH_ASSOC);
    }
}