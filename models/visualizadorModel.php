<?php

class visualizadorModel extends Model 
{

    public function __construct()
    {
        parent::__construct();       
    }
    
    public function getPendReportJuridico($fechaInicio, $fechaFin){
        $sql = "SELECT csj.fecha_diligenciamiento, 'Operador Mundial', td.codigo,  
            csj.razon_social, csj.rep_legal_documento, csj.fecha_verificacion, csj.estado_form  FROM cliente_sarlaft_juridico csj JOIN clientes c on c.id = csj.cliente
            JOIN tipos_documentos td on td.id = c.tipo_documento where estado_form_id in (3,10,11) AND (csj.fecha_diligenciamiento BETWEEN '".$fechaInicio."' and '".$fechaFin."')";
        $resul = $this->_db->prepare($sql);
        $resul->execute();

        return $resul->fetchAll();
    }
    
    public function getPendReportNatural($fechaInicio, $fechaFin){
        $sql = "SELECT csj.fecha_diligenciamiento, 'Operador Mundial', td.codigo,  
            CONCAT(csj.primer_nombre, ' ', csj.segundo_nombre, ' ', csj.primer_apellido, ' ', "
                . "csj.segundo_apellido) as nombre, c.documento, csj.fecha_verificacion, csj.estado_form  FROM cliente_sarlaft_natural csj JOIN clientes c on c.id = csj.cliente
            JOIN tipos_documentos td on td.id = c.tipo_documento where estado_form_id in (3,10,11) AND (csj.fecha_diligenciamiento BETWEEN '".$fechaInicio."' and '".$fechaFin."')";
        $resul = $this->_db->prepare($sql);
        $resul->execute();

        return $resul->fetchAll();
    }
    
    public function getVerCompReportJuridico($fechaInicio, $fechaFin){
        $sql = "SELECT csj.fecha_diligenciamiento, 'Operador Mundial', td.codigo,  
            csj.razon_social, csj.rep_legal_documento, csj.fecha_verificacion, case csj.corr_info_fin when 1 then 'Si' when 0 then 'No' end, case csj.campos_completos when 1 then 'Si' when 0 then 'No' end, csj.campos_completitud, csj.fecha_completitud, '' FROM cliente_sarlaft_juridico csj JOIN clientes c on c.id = csj.cliente
            JOIN tipos_documentos td on td.id = c.tipo_documento where estado_form_id in(12,19) AND (csj.fecha_diligenciamiento BETWEEN '".$fechaInicio."' and '".$fechaFin."')";
        $resul = $this->_db->prepare($sql);
        $resul->execute();

        return $resul->fetchAll();
    }
    
    public function getVerCompReportNatural($fechaInicio, $fechaFin){
        $sql = "SELECT csj.fecha_diligenciamiento, 'Operador Mundial', td.codigo,  
            CONCAT(csj.primer_nombre, ' ', csj.segundo_nombre, ' ', csj.primer_apellido, ' ', "
                . "csj.segundo_apellido) as nombre, c.documento, csj.fecha_verificacion  FROM cliente_sarlaft_natural csj JOIN clientes c on c.id = csj.cliente
            JOIN tipos_documentos td on td.id = c.tipo_documento where estado_form_id in(12,19) AND (csj.fecha_diligenciamiento BETWEEN '".$fechaInicio."' and '".$fechaFin."')";
        $resul = $this->_db->prepare($sql);
        $resul->execute();

        return $resul->fetchAll();
    }
    
    //Obtener información de cliente por consecutivo
    public function getCheckListReportJuridico($fechaInicio, $fechaFin){
        
        $sql = "SELECT csj.fecha_diligenciamiento, 'Operador Mundial', td.codigo,  
            csj.razon_social, csj.rep_legal_documento, 'Sí', '', '', '', CASE csj.tiene_entrevista WHEN 1 THEN 'Si' WHEN 0 THEN 'No' end , '', csj.estado_form,  
            csj.observacion_entrevista, c.id  FROM cliente_sarlaft_juridico csj JOIN clientes c on c.id = csj.cliente
            JOIN tipos_documentos td on td.id = c.tipo_documento where estado_form_id <> 20 AND (csj.fecha_diligenciamiento BETWEEN '".$fechaInicio."' and '".$fechaFin."')";
        $resul = $this->_db->prepare($sql);
        $resul->execute();

        return $resul->fetchAll();
    }
    
    public function getChecklistReportNatural($fechaInicio, $fechaFin){
        $sql = "SELECT csn.fecha_diligenciamiento, 'Operador Mundial', td.codigo, "
                . "CONCAT(csn.primer_nombre, ' ', csn.segundo_nombre, ' ', csn.primer_apellido, ' ', "
                . "csn.segundo_apellido) as nombre, c.documento, 'Sí', '', '', '', "
                . " CASE csn.informacion_entrevista WHEN 1 THEN 'Si' WHEN 0 THEN 'No' end , '', csn.estado_form, csn.observacion_entrevista, c.id "
                . "FROM cliente_sarlaft_natural csn JOIN clientes c on c.id = csn.cliente "
                . "JOIN tipos_documentos td on td.id = c.tipo_documento where estado_form_id <> 20 AND (csn.fecha_diligenciamiento BETWEEN '".$fechaInicio."' and '".$fechaFin."')";
        $resul = $this->_db->prepare($sql);
        $resul->execute();

        return $resul->fetchAll();
    }

    public function getBussinesLines(){
        $res = $this->_db->prepare("SELECT * FROM linea_negocio ORDER BY id_linea ASC");
        $res->execute();
        return $res->fetchAll();
    }

    public function getFoldersList($parms){
        $sql = 'CALL consulta_documentos (:root, :anio, :name , :document, :poliza, :siniestro, :linea)';
        $res = $this->_db->prepare($sql);

        $res->bindValue(":root", $parms["padre"]);
        $res->bindValue(":anio", '');
        $res->bindValue(":name", $parms["nombre_cliente"]);
        $res->bindValue(":document", $parms["numero_documento"]);
        $res->bindValue(":poliza", $parms["poliza"]);
        $res->bindValue(":siniestro", '');
        $res->bindValue(":linea", '');

        $res->execute();
        return $res->fetchAll();
    }
    
}
