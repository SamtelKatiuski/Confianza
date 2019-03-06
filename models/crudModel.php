
<?php
class crudModel extends Model
{
    public function __construct() {
        parent::__construct();    
    }
    
    public function Save($table,$data, $lastId = false) {

    	foreach ($data as $KeyDataSQL => $valueDataSQL) {
            $valuesColumnsSQL[":".$KeyDataSQL] = $valueDataSQL;
        }

        $columnsSQL = implode(",", array_keys($data));
        $dataSQL = implode(",", array_keys($valuesColumnsSQL));
        $sql  = "INSERT INTO {$table} ({$columnsSQL}) VALUES ({$dataSQL})";

        $parms = array();
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

            array_push($parms, array("key" => $keyData , "value" => $valueData));
        }

        $resultado = $result->execute();

        //Obtener ultimo id insertado
        if($lastId){
            
            if($resultado)
                return $this->_db->lastInsertId();            
        }else{

            if(!is_null($result->errorInfo()[2]))
                return array('error' => $result->errorInfo()[2]);
            else
                return $resultado;
        }


    }

    public function Update($table,$data) {
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

    public function Delete($table, $id){
        $sql  = "DELETE FROM {$table} WHERE {$table}.id = :id";

        $result = $this->_db->prepare($sql);
        $result->bindValue(":id",$id);

        return $result->execute();
    }
}