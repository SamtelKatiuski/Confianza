<?php 

class migracionModel extends Model
{
    public function __construct() {
        parent::__construct();    
    }

    // Obtiene la ultima fecha en la que se migro un tipo de archivo
    public function findOldDateMigrate($tipo_migracion) {
    	$sql = "SELECT MAX(FECHA_MIGRACION_ARCHIVO) AS ULT_FECHA_MIGRACION  FROM zr_migraciones WHERE TIPO_MIGRACION = :tipo_migracion";

    	$result = $this->_db->prepare($sql);
    	$result->bindValue(':tipo_migracion',$tipo_migracion);

        $resultado = $result->execute();

        if(!is_null($result->errorInfo()[2]))
            return array('error' => $result->errorInfo()[2]);
        else
            return $result->fetch(PDO::FETCH_ASSOC);
    }

    // Obtiene los clientes que se encuentren es estado migracion en la plataforma
    public function LiberarClientesMigracion(){
        $sql = "SELECT 
                    CS.formulario_id,
                    CS.cliente_id,
                    TD.tipo_persona,
                    CS.estado_formulario_id,
                    CS.fecha_diligenciamiento AS FECHA_DILIGENCIAMIENTO_ACTUAL
                FROM
                    clientes_sarlaft CS 
                    INNER JOIN clientes ON clientes.id = CS.cliente_id
                    INNER JOIN tipos_documentos TD ON TD.id = clientes.tipo_documento 
                WHERE
                    estado_formulario_id = 13";

        $result = $this->_db->prepare($sql);
        $resultado = $result->execute();

        if(!is_null($result->errorInfo()[2]))
            return array('error' => $result->errorInfo()[2]);
        else
            return $result->fetchAll(PDO::FETCH_ASSOC);
    }
}