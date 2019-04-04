<?php

class reportesModel extends Model 
{

    public function __construct() {
        parent::__construct();       
    }

    public function getChecklistDocumentosClientes(){
        $sql = "SELECT * FROM reporte_clientes_checklist_documentos";

        $result = $this->_db->prepare($sql);

        $resultado = $result->execute();

        if(!is_null($result->errorInfo()[2]))
            return array('error' => $result->errorInfo()[2]);
        else
            return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getGestionPPESBeneficiariofinal(){
        $sql = "SELECT * FROM reporte_clientes_beneficiario_final_peps";

        $result = $this->_db->prepare($sql);

        $resultado = $result->execute();

        if(!is_null($result->errorInfo()[2]))
            return array('error' => $result->errorInfo()[2]);
        else
            return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getGestionVerificacionCompleitudClientes(){
        $sql = "SELECT * FROM reporte_clientes_completitud_verificacion";

        $result = $this->_db->prepare($sql);

        $resultado = $result->execute();

        if(!is_null($result->errorInfo()[2]))
            return array('error' => $result->errorInfo()[2]);
        else
            return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPendientesClientes($fechasReporte = array()){
        $sql = "SELECT * FROM reporte_clientes_pendientes WHERE FECHA_RADICACION BETWEEN :inicio AND :fin";

        $result = $this->_db->prepare($sql);

        $result->bindValue(":inicio" , $fechasReporte["inicio"] . ' 00:00:00');
        $result->bindValue(":fin" , $fechasReporte["fin"] . ' 23:59:59');

        $resultado = $result->execute();

        if(!is_null($result->errorInfo()[2]))
            return array('error' => $result->errorInfo()[2]);
        else
            return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getFacturacion($fechasReporte = array()){
        $sql = "SELECT DISTINCT * FROM reporte_facturacion WHERE FECHA_RADICACION BETWEEN :inicio AND :fin";

        $result = $this->_db->prepare($sql);

        $result->bindValue(":inicio" , $fechasReporte["inicio"] . ' 00:00:00');
        $result->bindValue(":fin" , $fechasReporte["fin"] . ' 23:59:59');

        $resultado = $result->execute();

        if(!is_null($result->errorInfo()[2]))
            return array('error' => $result->errorInfo()[2]);
        else
            return $result->fetchAll(PDO::FETCH_ASSOC);
    }  

    public function getDatosClientesCapturados($fechasReporte = array()){
        $sql = "SELECT * FROM reporte_clientes_capturados WHERE FECHA_CAPTURA BETWEEN :inicio AND :fin";

        $result = $this->_db->prepare($sql);

        $result->bindValue(":inicio" , $fechasReporte["inicio"] . ' 00:00:00');
        $result->bindValue(":fin" , $fechasReporte["fin"] . ' 23:59:59');

        $resultado = $result->execute();

        if(!is_null($result->errorInfo()[2]))
            return array('error' => $result->errorInfo()[2]);
        else
            return $result->fetchAll(PDO::FETCH_ASSOC);
    } 

    public function getDatosClientesCapturadosNaturales($fechasReporte = array()){
        $sql = "SELECT * FROM reporte_cargue_clientes_naturales WHERE FECHA_CAPTURA BETWEEN :inicio AND :fin";

        $result = $this->_db->prepare($sql);
        
        $result->bindValue(":inicio" , $fechasReporte["inicio"] . ' 00:00:00');
        $result->bindValue(":fin" , $fechasReporte["fin"] . ' 23:59:59');

        $resultado = $result->execute();

        if(!is_null($result->errorInfo()[2]))
            return array('error' => $result->errorInfo()[2]);
        else
            return $result->fetchAll(PDO::FETCH_ASSOC);
    }    

    public function getDatosClientesCapturadosJuridicos($fechasReporte = array()){
        $sql = "SELECT * FROM reporte_cargue_clientes_juridicos WHERE FECHA_CAPTURA BETWEEN :inicio AND :fin";

        $result = $this->_db->prepare($sql);
        
        $result->bindValue(":inicio" , $fechasReporte["inicio"] . ' 00:00:00');
        $result->bindValue(":fin" , $fechasReporte["fin"] . ' 23:59:59');

        $resultado = $result->execute();

        if(!is_null($result->errorInfo()[2]))
            return array('error' => $result->errorInfo()[2]);
        else
            return $result->fetchAll(PDO::FETCH_ASSOC);
    }   
    
    public function getDatosFechasDocumentos($fechasReporte = array()){
        $sql = "SELECT
        CL.id AS CLIENTE_ID,
        DATE_FORMAT(ZR.created, '%Y-%m-%d') AS FECHA_RADICACION,
        US.nombre AS USUARIO_RADICADOR,
        (SELECT correo_radicacion FROM zr_radicacion WHERE created = (SELECT MAX(created) FROM zr_radicacion WHERE cliente_id = CL.id LIMIT 1) ORDER BY id DESC LIMIT 1) AS USUARIO_SUSCRIPTOR,
        TD.codigo AS TIPO_ID_CLIENTE,
        CL.documento AS NUMERO_ID_CLIENTE,
        AO.nombre_cliente AS NOMBRE_CLIENTE,
        LEFT(RAR.nombre_archivo, 3) AS TIPO_DOC,
        (SELECT MAX(fecha_emision) FROM relacion_archivo_radicacion WHERE LEFT(relacion_archivo_radicacion.nombre_archivo, 3) = TIPO_DOC AND cliente_id = CL.id) AS FECHA_EMISION,
        (SELECT MAX(fecha_diligenciamiento) FROM zr_radicacion WHERE correo_radicacion = ZR.correo_radicacion) AS FCC
        FROM relacion_archivo_radicacion RAR
        INNER JOIN clientes Cl ON CL.id = RAR.cliente_id
        INNER JOIN tipos_documentos TD ON CL.tipo_documento = TD.id
        INNER JOIN zr_radicacion ZR ON ZR.id = RAR.radicacion_id
        INNER JOIN archivo_organizado AO ON ZR.id = AO.radicacion_id
        INNER JOIN users US ON ZR.funcionario_id = US.id
        WHERE DATE_FORMAT(ZR.created, '%Y-%m-%d') BETWEEN :inicio AND :fin
        ORDER BY CLIENTE_ID";

        $result = $this->_db->prepare($sql);
        
        $result->bindValue(":inicio" , $fechasReporte["inicio"]);
        $result->bindValue(":fin" , $fechasReporte["fin"]);

        $resultado = $result->execute();

        if(!is_null($result->errorInfo()[2]))
            return array('error' => $result->errorInfo()[2]);
        else
            return $result->fetchAll(PDO::FETCH_ASSOC);
    }
}
