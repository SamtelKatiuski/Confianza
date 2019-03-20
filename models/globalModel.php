<?php 

class globalModel extends Model
{
    public function __construct() {
        parent::__construct();       
    }

    //Obtiene la informacion de paises en orden alfabético
    public function getCountries() {
        $resul = $this->_db->prepare("SELECT * FROM paises ORDER BY nombre_pais ASC");
        $resul->execute();
        return $resul->fetchAll();
    }

    //Obtiene la informacion de todas las ocupaciones en orden alfabético
    public function getOccupations() {
        $resul = $this->_db->prepare("SELECT * FROM ocupacion ORDER BY descripcion ASC");
        $resul->execute();
        return $resul->fetchAll();
    }

    //Obtiene la informacion de los tipos de vias
    public function getMultiParam($param) {
        $resul = $this->_db->prepare("SELECT * FROM multi_param WHERE nombre_parametro = :parametro");
        $resul->bindValue(':parametro', $param, PDO::PARAM_STR);
        $resul->execute();
        return $resul->fetchAll();
    }
    //Obtiene la informacion de departamentos por pais, defualt: Colombia
    public function getDepartaments($pais = 170) {
        $result = $this->_db->prepare("SELECT * FROM departamentos WHERE pais = :pais");
        $result->bindValue(":pais", $pais);

        $resultado = $result->execute();

        if(!is_null($result->errorInfo()[2]))
            return $result->errorInfo()[2];
        else
            return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    //Obtiene la informacion de ciudades por departamento
    public function getCities($departamento = false) {
        if($departamento) {
            $result = $this->_db->prepare("SELECT * FROM ciudades WHERE departamento = :departamento");
            $result->bindValue(":departamento", $departamento);
        } else {
            $result = $this->_db->prepare("SELECT * FROM ciudades");
        }

        $resultado = $result->execute();

        if(!is_null($result->errorInfo()[2]))
            return $result->errorInfo()[2];
        else
            return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtiene la informacion de abreviados para los documento de la plataforma
    public function AbreviadosDocumentos() {

        $sql = "SELECT * FROM zr_tipo_documento";
        $result = $this->_db->prepare($sql);
        $result->execute();

        if(!is_null($result->errorInfo()[2]))
            return $result->errorInfo()[2];
        else
            return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtiene la informacion de los tipo de documentos de los clientes en la plataforma
    public function TipoDocumentos() {
        $sql = "SELECT * FROM tipos_documentos";
        $result = $this->_db->prepare($sql);
        $result->execute();

        if(!is_null($result->errorInfo()[2]))
            return $result->errorInfo()[2];
        else
            return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtiene los nombres de todas las columnas de una tabla especifica
    public function getColumnsTable($table) {
        $sql = "DESCRIBE {$table}";
        $result = $this->_db->prepare($sql);
        $resultado = $result->execute();

        if(!is_null($result->errorInfo()[2]))
            return array('error' => $result->errorInfo()[2]);
        else
            return $result->fetchAll(PDO::FETCH_COLUMN);
    }

    // Obtiene los tipos de documentos en la plataforma
    public function getAllTypeClients(){
        $resul = $this->_db->prepare("SELECT * FROM tipos_documentos ORDER BY id");
        $resul->execute();
        return $resul->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtiene el tipo de documento por un id
    public function getTipoDocumentoByID($id){
        $result = $this->_db->prepare("SELECT * FROM tipos_documentos WHERE id = :id ORDER BY id");
        $result->bindValue(':id',$id, PDO::PARAM_INT);
        $result->execute();

        if(!is_null($result->errorInfo()[2]))
            return array('error' => $result->errorInfo()[2]);
        else
            return $result->fetch(PDO::FETCH_ASSOC);
    }

    // Obtiene la linea de negocio por un id
    public function getLineaNegocioByID($id){
        $result = $this->_db->prepare("SELECT * FROM linea_negocio WHERE ID_LINEA = :id ORDER BY ID_LINEA");
        $result->bindValue(':id',$id, PDO::PARAM_INT);
        $result->execute();

        if(!is_null($result->errorInfo()[2]))
            return array('error' => $result->errorInfo()[2]);
        else
            return $result->fetch(PDO::FETCH_ASSOC);
    }

    // Obtiene la linea de negocio por un id
    public function getAllLineaNegocio(){
        $result = $this->_db->prepare("SELECT * FROM linea_negocio");
        $result->execute();

        if(!is_null($result->errorInfo()[2]))
            return array('error' => $result->errorInfo()[2]);
        else
            return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getVinculacionesByid($id){
        $result = $this->_db->prepare("SELECT * FROM vinculaciones WHERE id = :id");
        $result->bindValue(':id',$id, PDO::PARAM_INT);
        $result->execute();

        if(!is_null($result->errorInfo()[2]))
            return array('error' => $result->errorInfo()[2]);
        else
            return $result->fetch(PDO::FETCH_ASSOC); 
    }

    public function getRelacionesByid($id){
        $result = $this->_db->prepare("SELECT * FROM tipos_relaciones_tom_ase_bene WHERE id = :id");
        $result->bindValue(':id',$id, PDO::PARAM_INT);
        $result->execute();

        if(!is_null($result->errorInfo()[2]))
            return array('error' => $result->errorInfo()[2]);
        else
            return $result->fetch(PDO::FETCH_ASSOC); 
    }

    // Obtener nombre de cliente por documento - JAV01
    public function nameClient($documentClient){
        $result = $this->_db->prepare("SELECT AO.nombre_cliente 
                                        FROM archivo_organizado AO 
                                        WHERE AO.numero_ident_cliente = :documento 
                                        AND AO.id = (SELECT MAX(id) FROM archivo_organizado WHERE NUMERO_IDENT_CLIENTE = AO.numero_ident_cliente)
                                        GROUP BY AO.nombre_cliente");
        $result->bindValue(':documento',$documentClient);
        $result->execute();

        if(!is_null($result->errorInfo()[2]))
            return array('error' => $result->errorInfo()[2]);
        else
            return $result->fetch(PDO::FETCH_ASSOC); 
    }

    public function getAnios(){
        $result = $this->_db->prepare("SELECT * FROM anio");
        $result->execute();

        if(!is_null($result->errorInfo()[2]))
            return array('error' => $result->errorInfo()[2]);
        else
            return $result->fetchAll(PDO::FETCH_ASSOC); 
    }

    public function getMonedas(){
        $result = $this->_db->prepare("SELECT * FROM tipo_moneda");
        $result->execute();

        if(!is_null($result->errorInfo()[2]))
            return array('error' => $result->errorInfo()[2]);
        else
            return $result->fetchAll(PDO::FETCH_ASSOC); 
    }

    public function getSucursales(){
        $result = $this->_db->prepare("SELECT * FROM sucursal");
        $result->execute();

        if(!is_null($result->errorInfo()[2]))
            return array('error' => $result->errorInfo()[2]);
        else
            return $result->fetchAll(PDO::FETCH_ASSOC); 
    }
}