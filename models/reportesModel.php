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
}
