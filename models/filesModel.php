<?php
class filesModel extends Model
{
    public function __construct() {
        parent::__construct();       
    }

    // Obtiene todos los datos de los archivos que ha cargado el robot en la base de datos
    public function getAllFilesRobot() {
        $sql = "SELECT * FROM archivo_organizado";
        $result = $this->_db->prepare($sql);
        $result->execute();

        if(!is_null($result->errorInfo()[2]))
            return array('error' => $result->errorInfo()[2]);
        else
            return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtiene todos los datos de los archivos que ha cargado el robot en la base de datos
    public function getAllDocumentsClientsRobot() {
        $sql = "SELECT DISTINCT NUMERO_IDENT_CLIENTE FROM archivo_organizado";
        $result = $this->_db->prepare($sql);
        $result->execute();

        if(!is_null($result->errorInfo()[2]))
            return array('error' => $result->errorInfo()[2]);
        else
            return $result->fetchAll(PDO::FETCH_COLUMN);
    }

    // Obtiene el tipo de identificacion, el numero de identificacion de un archivo que halla carga el robot en la plataforma
    public function getClientRobot($documento) {
        $sql = "SELECT 
                    DISTINCT TD.id AS TIPO_IDENT_CLIENTE,
                    archivo_organizado.TIPO_IDENT_CLIENTE AS TIPO_IDENT_CLIENTE_CODIGO
                FROM archivo_organizado
                    INNER JOIN tipos_documentos TD ON archivo_organizado.TIPO_IDENT_CLIENTE = TD.codigo
                WHERE 
                    archivo_organizado.NUMERO_IDENT_CLIENTE = :NUMERO_IDENT_CLIENTE";

        $result = $this->_db->prepare($sql);
        $result->bindValue(":NUMERO_IDENT_CLIENTE",$documento);
        $result->execute();

        if(!is_null($result->errorInfo()[2]))
            return array('error' => $result->errorInfo()[2]);
        else
            return $result->fetch(PDO::FETCH_ASSOC);
    }

    // Verifica si el cliente existe en la tabla de archivo organizado
    public function VerifyClientExist($document) {

        $sql = "SELECT 
                    COUNT(archivo_organizado.id) AS CARGADO_ROBOT,
                    ( SELECT COUNT(clientes.id) FROM clientes WHERE clientes.documento = archivo_organizado.NUMERO_IDENT_CLIENTE ) AS CARGADO_SISTEMA
                FROM archivo_organizado
                WHERE archivo_organizado.NUMERO_IDENT_CLIENTE = :document";

        $result = $this->_db->prepare($sql);
        $result->bindValue(':document',$document);
        $result->execute();

        if(!is_null($result->errorInfo()[2]))
            return array('error' => $result->errorInfo()[2]);
        else
            return $result->fetch(PDO::FETCH_ASSOC);
    }

    // Obtiene la información de un tipo de archivo por el codigo del archivo
    public function getFileIDByCodigo($codigo){
        $sql = "SELECT * FROM zr_tipo_documento WHERE codigo =:codigo";
        $result = $this->_db->prepare($sql);
        $result->bindValue(':codigo', $codigo);
        $result->execute();

        if(!is_null($result->errorInfo()[2]))
            return $result->errorInfo()[2];
        else
            return $result->fetch(PDO::FETCH_ASSOC);
    }

     //Borrar todos los documentos asociados a un cliente - JAV01
    public function deleteFilesClient($doc){
        $sql = "DELETE FROM archivo_organizado WHERE NUMERO_IDENT_CLIENTE = :doc";
        $result = $this->_db->prepare($sql);
        $result->bindValue(':doc', $doc);
        $result->execute();

        if(!is_null($result->errorInfo()[2]))
            return array('error' => $result->errorInfo()[2]);
        else
            return $result->fetch(PDO::FETCH_ASSOC);
    }
}