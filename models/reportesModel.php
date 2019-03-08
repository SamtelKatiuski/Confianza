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
        $sql = "SELECT * FROM reporte_facturacion WHERE FECHA_RADICACION BETWEEN :inicio AND :fin";

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
        $sql = "SELECT CL.id AS CLIENTE_ID,
                DATE_FORMAT(ZR.created, '%Y-%m-%d') AS FECHA_RADICACION,
                US.nombre AS USUARIO_RADICADOR,
                TD.codigo AS TIPO_ID_CLIENTE,
                LEFT(RAR.nombre_archivo, 3) AS TIPO_DOC,
                AO.nombre_cliente AS NOMBRE_CLIENTE,
                MAX(RAR.fecha_emision) AS FECHA_EMISION
                FROM relacion_archivo_radicacion RAR
                INNER JOIN clientes Cl ON CL.id = RAR.cliente_id
                INNER JOIN tipos_documentos TD ON CL.tipo_documento = TD.id
                INNER JOIN zr_radicacion ZR ON ZR.id = RAR.radicacion_id
                INNER JOIN archivo_organizado AO ON ZR.id = AO.radicacion_id
                INNER JOIN users US ON ZR.funcionario_id = US.id
                WHERE RAR.FECHA_EMISION BETWEEN :inicio AND :fin
                GROUP BY CLIENTE_ID, TIPO_DOC";

        $result = $this->_db->prepare($sql);
        
        $result->bindValue(":inicio" , $fechasReporte["inicio"] . ' 00:00:00');
        $result->bindValue(":fin" , $fechasReporte["fin"] . ' 23:59:59');

        $resultado = $result->execute();

        if(!is_null($result->errorInfo()[2]))
            return array('error' => $result->errorInfo()[2]);
        else
            return $result->fetchAll(PDO::FETCH_ASSOC);
    }
}
