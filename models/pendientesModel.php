<?php
/**
 * summary
 */
class pendientesModel extends Model
{

    public function __construct() {
        parent::__construct();
    }

    // Obtiene listado de clientes pendientes
    public function getAllClientesPendientes($fec_ini , $fec_fin){
        $sql = "SELECT DISTINCT
                    clientes.id,
                    ZEPCS.id AS ID_PROCESO,
                    clientes.documento as DOCUMENTO_CLIENTE,
                    ZR.fecha_diligenciamiento AS FECHA_DILIGENCIAMIENTO,
                    TD.codigo AS CLIENTE_TIPO_DOCUMENTO_CODIGO,
                    CJ.razon_social AS NOMBRE_CLIENTE,
                    ZR.correo_radicacion AS CORREO_RADICACION,
                    estados_sarlaft.desc_type AS ESTADO_PROCESO,
                    DATE_FORMAT(ZR.created, '%Y-%m-%d') AS FECHA_RADICACION,
                    DATE_FORMAT(ZR.created, '%r') AS HORA_RADICACION,
                    ZR.radicacion_observacion AS OBSERVACION
                FROM
                    cliente_sarlaft_juridico CJ
                INNER JOIN clientes ON clientes.id = CJ.cliente
                INNER JOIN tipos_documentos TD ON TD.id = clientes.tipo_documento
                INNER JOIN zr_radicacion ZR ON ZR.cliente_id = CJ.cliente
                    AND ZR.created = (
                        SELECT 
                            MAX(ZR2.created)
                        FROM
                            zr_radicacion ZR2
                        WHERE
                            ZR2.cliente_id = ZR.cliente_id
                            AND ZR2.formulario_sarlaft = 1
                            AND ZR2.formulario_repetido = 0 
                            AND ZR2.devuelto = 'No'
                            AND ZR2.radicacion_proceso = 'LEGAL'
                    )
                INNER JOIN zr_estado_proceso_clientes_sarlaft ZEPCS ON ZEPCS.PROCESO_CLIENTE_ID = ZR.cliente_id
                    AND ZEPCS.FECHA_PROCESO = (
                        SELECT 
                            MAX(ZEPCS2.FECHA_PROCESO)
                        FROM 
                            zr_estado_proceso_clientes_sarlaft ZEPCS2
                        WHERE
                            ZEPCS2.PROCESO_CLIENTE_ID = ZEPCS.PROCESO_CLIENTE_ID
                            AND ZEPCS2.PROCESO_FECHA_DILIGENCIAMIENTO = ZR.fecha_diligenciamiento
                    )
                    AND ZEPCS.ESTADO_PROCESO_ID IN (10,11,12,15,16,2)
                    INNER JOIN estados_sarlaft ON estados_sarlaft.id = ZEPCS.ESTADO_PROCESO_ID
                WHERE 
                    ZR.created BETWEEN :fec_ini AND :fec_fin
                UNION ALL
                SELECT DISTINCT
                    clientes.id,
                    ZEPCS.id AS ID_PROCESO,
                    clientes.documento as DOCUMENTO_CLIENTE,
                    ZR.fecha_diligenciamiento AS FECHA_DILIGENCIAMIENTO,
                    TD.codigo AS CLIENTE_TIPO_DOCUMENTO_CODIGO,
                    TRIM(CONCAT_WS(' ',CN.primer_apellido,CN.segundo_apellido,CN.primer_nombre,CN.segundo_nombre)) AS NOMBRE_CLIENTE,
                    ZR.correo_radicacion AS CORREO_RADICACION, 
                    estados_sarlaft.desc_type AS ESTADO_PROCESO,
                    DATE_FORMAT(ZR.created, '%Y-%m-%d') AS FECHA_RADICACION,
                    DATE_FORMAT(ZR.created, '%r') AS HORA_RADICACION,
                    ZR.radicacion_observacion AS OBSERVACION
                FROM
                    cliente_sarlaft_natural CN
                INNER JOIN clientes ON clientes.id = CN.cliente
                INNER JOIN tipos_documentos TD ON TD.id = clientes.tipo_documento
                INNER JOIN zr_radicacion ZR ON ZR.cliente_id = CN.cliente
                    AND ZR.created = (
                            SELECT 
                                MAX(ZR2.created)
                            FROM
                                zr_radicacion ZR2
                            WHERE
                                ZR2.cliente_id = ZR.cliente_id
                                AND ZR2.formulario_sarlaft = 1
                                AND ZR2.formulario_repetido = 0 
                                AND ZR2.devuelto = 'No'
                                AND ZR2.radicacion_proceso = 'LEGAL'
                        )
                INNER JOIN zr_estado_proceso_clientes_sarlaft ZEPCS ON ZEPCS.PROCESO_CLIENTE_ID = ZR.cliente_id
                    AND ZEPCS.FECHA_PROCESO = (
                        SELECT 
                            MAX(ZEPCS2.FECHA_PROCESO)
                        FROM 
                            zr_estado_proceso_clientes_sarlaft ZEPCS2
                        WHERE
                            ZEPCS2.PROCESO_CLIENTE_ID = ZEPCS.PROCESO_CLIENTE_ID
                            AND ZEPCS2.PROCESO_FECHA_DILIGENCIAMIENTO = ZR.fecha_diligenciamiento
                    )
                    AND ZEPCS.ESTADO_PROCESO_ID IN (10,11,12,15,16,2)
                INNER JOIN estados_sarlaft ON estados_sarlaft.id = ZEPCS.ESTADO_PROCESO_ID
                WHERE 
                    ZR.created BETWEEN :fec_ini AND :fec_fin";

        $result = $this->_db->prepare($sql);
        $result->bindValue(":fec_ini" , $fec_ini);
        $result->bindValue(":fec_fin" , $fec_fin);

        $resultado = $result->execute();

        if(!is_null($result->errorInfo()[2]))
            return array('error' => $result->errorInfo()[2]);
        else
            return $result->fetchAll(PDO::FETCH_ASSOC);
    } 

    // Obtiene información de pendientes por cliente 
    public function getInfoPenddingById($cliente_id, $proceso_id){
        $sql = "
            SELECT DISTINCT
            clientes.id,
            ZR.id AS RADICACION_ID,
            ZEPCS.id AS ID_PROCESO,
            clientes.documento as DOCUMENTO_CLIENTE,
            ZR.fecha_diligenciamiento AS FECHA_DILIGENCIAMIENTO,
            TD.codigo AS CLIENTE_TIPO_DOCUMENTO_CODIGO,
            CJ.razon_social AS NOMBRE_CLIENTE,
            ZR.correo_radicacion AS CORREO_RADICACION,
            estados_sarlaft.desc_type AS ESTADO_PROCESO,
            DATE_FORMAT(ZR.created, '%Y-%m-%d') AS FECHA_RADICACION,
            DATE_FORMAT(ZR.created, '%r') AS HORA_RADICACION,
            ZR.radicacion_observacion AS OBSERVACION
            FROM
            cliente_sarlaft_juridico CJ
            INNER JOIN clientes ON clientes.id = CJ.cliente
            INNER JOIN tipos_documentos TD ON TD.id = clientes.tipo_documento
            INNER JOIN zr_radicacion ZR ON ZR.cliente_id = CJ.cliente
            AND ZR.formulario_sarlaft = 1
            AND ZR.formulario_repetido = 0 
            AND ZR.devuelto = 'No'
            AND ZR.radicacion_proceso = 'LEGAL'
            INNER JOIN zr_estado_proceso_clientes_sarlaft ZEPCS ON ZEPCS.PROCESO_CLIENTE_ID = CJ.cliente
            AND ZEPCS.PROCESO_FECHA_DILIGENCIAMIENTO = ZR.fecha_diligenciamiento
            AND ZEPCS.ESTADO_PROCESO_ID IN (10,11,12,15,16, 2)
            INNER JOIN estados_sarlaft ON estados_sarlaft.id = ZEPCS.ESTADO_PROCESO_ID
            WHERE clientes.id = :cliente_id AND ZEPCS.id = :proceso_id
            UNION ALL
            SELECT DISTINCT
            clientes.id,
            ZR.id AS RADICACION_ID,
            ZEPCS.id AS ID_PROCESO,
            clientes.documento as DOCUMENTO_CLIENTE,
            ZR.fecha_diligenciamiento AS FECHA_DILIGENCIAMIENTO,
            TD.codigo AS CLIENTE_TIPO_DOCUMENTO_CODIGO,
            TRIM(CONCAT_WS(' ',CN.primer_apellido,CN.segundo_apellido,CN.primer_nombre,CN.segundo_nombre)) AS NOMBRE_CLIENTE,
            ZR.correo_radicacion AS CORREO_RADICACION, 
            estados_sarlaft.desc_type AS ESTADO_PROCESO,
            DATE_FORMAT(ZR.created, '%Y-%m-%d') AS FECHA_RADICACION,
            DATE_FORMAT(ZR.created, '%r') AS HORA_RADICACION,
            ZR.radicacion_observacion AS OBSERVACION
            FROM
            cliente_sarlaft_natural CN
            INNER JOIN clientes ON clientes.id = CN.cliente
            INNER JOIN tipos_documentos TD ON TD.id = clientes.tipo_documento
            INNER JOIN zr_radicacion ZR ON ZR.cliente_id = CN.cliente
            AND ZR.formulario_sarlaft = 1
            and ZR.formulario_repetido = 0
            AND ZR.devuelto = 'No'
            AND ZR.radicacion_proceso = 'LEGAL'
            INNER JOIN zr_estado_proceso_clientes_sarlaft ZEPCS ON ZEPCS.PROCESO_CLIENTE_ID = CN.cliente
            AND ZEPCS.PROCESO_FECHA_DILIGENCIAMIENTO = ZR.fecha_diligenciamiento
            AND ZEPCS.ESTADO_PROCESO_ID IN (10,11,12,15,16, 2)
            INNER JOIN estados_sarlaft ON estados_sarlaft.id = ZEPCS.ESTADO_PROCESO_ID  
            WHERE clientes.id = :cliente_id AND ZEPCS.id = :proceso_id";

        $result = $this->_db->prepare($sql);
        $result->bindValue(":cliente_id" , $cliente_id);        
        $result->bindValue(":proceso_id" , $proceso_id);        

        $resultado = $result->execute();

        if(!is_null($result->errorInfo()[2]))
            return array('error' => $result->errorInfo()[2]);
        else
            return $result->fetchAll(PDO::FETCH_ASSOC);
    }


    //Obtiene listado de clientes pendientes de dia para un funcionario especifico
    public function getAllClientesByUser(){
        $sql = 'SELECT
                    CS.cliente_id AS CLIENTE_ID,
                    CS.cliente_documento AS CLIENTE_DOCUMENTO,
                    estados_sarlaft.desc_type AS ESTADO_CLIENTE,
                    TD.tipo_persona AS TIPO_CLIENTE,
                    CS.NOMBRE_CLIENTE
                FROM clientes_sarlaft CS
                    INNER JOIN tipos_documentos TD ON CS.tipo_ident_client_id = TD.id
                    INNER JOIN estados_sarlaft ON CS.estado_formulario_id = estados_sarlaft.id
                WHERE CS.estado_formulario_id = 4';

        $result = $this->_db->prepare($sql);

        $resultado = $result->execute();

        if(!is_null($result->errorInfo()[2]))
            return array('error' => $result->errorInfo()[2]);
        else
            return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    // Libera un documento de un cliente que estuviera pendiente 
    public function setReleaseFileClientPending($cliente_id,$tp_documento){

        $sql = "UPDATE zr_clientes_pendientes_documentos SET RECIBIDO = 1, FECHA_RECIBIDO = CURRENT_TIMESTAMP WHERE CLIENTE_ID = :cliente_id AND DOCUMENTO_PENDIENTE_ID = :tp_documento";

        $result = $this->_db->prepare($sql);
        $result->bindValue(':cliente_id',$cliente_id,PDO::PARAM_INT);
        $result->bindValue(':tp_documento',$tp_documento,PDO::PARAM_INT);

        $resultado = $result->execute();

        if(!is_null($result->errorInfo()[2]))
            return array('error' => $result->errorInfo()[2]);
        else
            return $resultado;
    }

    public function getCountDocumentsPendingClientById($cliente_id){
        $sql = 'SELECT
                    COUNT(*)
                FROM zr_clientes_pendientes_documentos ZCPD
                WHERE ZCPD.CLIENTE_ID = :cliente_id
                    AND ZCPD.RECIBIDO = 0';

        $result = $this->_db->prepare($sql);
        $result->bindValue(':cliente_id',$cliente_id,PDO::PARAM_INT);

        $resultado = $result->execute();

        if(!is_null($result->errorInfo()[2]))
            return array('error' => $result->errorInfo()[2]);
        else
            return $result->fetch(PDO::FETCH_COLUMN);
    }
}