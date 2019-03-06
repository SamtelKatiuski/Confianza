<?php 

class Helpers{

    public static function formatData($columnsSQL,$data){

        $dataReturn = array();

        foreach ($columnsSQL as $valuesSQL) {
            if(isset($data[$valuesSQL])){
                if(is_array($data[$valuesSQL])){
                    $dataReturn[$valuesSQL] = self::ToFormat($data[$valuesSQL],'ArrayToString',array('delimiter' => ','));
                }else{
                    $dataReturn[$valuesSQL] = strtoupper(Security::limpiarCadena(self::ToFormat($data[$valuesSQL])));
                    if($dataReturn[$valuesSQL] == ''){
                        $dataReturn[$valuesSQL] = NULL;
                    }
                }
            }
        }

        return $dataReturn;
    }

    public static function ToFormat($data, $format = 'string', $callback = false){
        switch ($format) {
            case 'int':
                return (int)$data;
                break;
            case 'float':
                return (float)$data;
                break;
            case 'boolean':
                return (boolean)$data;
                break;
            case 'double':
                return (double)$data;
                break;
            case 'ArrayToString':
                    return implode($callback['delimiter'],$data);
                break;
            case 'StringToArray':
                    return explode($callback['delimiter'],$data);
                break;
            case 'replace':
                return str_replace($callback['delimiter'],$callback['replace'],$data);
            default:
                return (string)$data;
                break;
        }
    }

    public static function LoadFile($DATAFILE, $VALIDEXTENSION = ['XLSX,XLS,IMG,PNG,GIF'], $RUTETEMP = false, $MAX_SIZE = 62914560){
        try {
            
            $FILELOAD = array(); // Variable para enviar el resultado al cargar el documento
            if(isset($DATAFILE["name"]) && !empty($DATAFILE["name"])){ // Verifica que halla llegado un archivo para migrar

                if($DATAFILE["size"] <= $MAX_SIZE){ // Tamaño permitido default 60 MB

                    if($DATAFILE["tmp_name"]){ // Verifica que se halla enviado a una carpeta temporal

                        if(!$DATAFILE["error"]){ // Verifica que no lleguen errores en el archivo

                            $extensionFile = strtoupper(pathinfo($DATAFILE["name"], PATHINFO_EXTENSION)); // Obtiene la extension del archivo

                            if(in_array($extensionFile,$VALIDEXTENSION)){ // Verifica que la extension del archivo

                                if(!$RUTETEMP){
                                    $rutaTempFile = ROOT . 'files' . DIR_SEP; // Ruta temporal por defecto
                                }else{
                                    $rutaTempFile = $RUTETEMP; // Ruta asignada donde se va a aguardar el documento
                                }

                                if(is_readable($rutaTempFile)){

                                    if(@move_uploaded_file($DATAFILE["tmp_name"], $rutaTempFile . $DATAFILE['name'])){

                                        $FILELOAD['success'] = array('ruta_temp' => $rutaTempFile . $DATAFILE['name']);
                                        return $FILELOAD;
                                    }else{
                                        throw new Exception('ERROR NO SE PUDO MOVER EL ARCHIVO A LA CARPETA ESPECIFICA');
                                    }
                                }else{
                                    throw new Exception('LA ASIGANCION DE CARPETA TEMPORAL NO CONTINUO , PORQUE NO EXISTE LA CARPETA ' . $RUTETEMP);
                                }
                            }else{
                                throw new Exception('SOLO SE ACEPTAN EXTENSIONES DE TIPO ' . implode(',',$VALIDEXTENSION));
                            }
                        }else{
                            throw new Exception($DATAFILE['archivo_migracion']['error']);
                        }
                    }else{
                        throw new Exception('NO EXISTE UNA RUTA TEMPORAL PARA EL ARCHIVO');
                    }
                }else{
                    throw new Exception('EL TAMAÑO DEL ARCHIVO ES DEMASIADO GRANDE AL ADMITIDO (' . $MAX_SIZE . ')');
                }
            }else{
                throw new Exception('NO SE OBTUVO NINGUN ARCHIVO');
            }
        } catch (Exception $ex) {

            $FILELOAD['error'] = array('message' => $ex->getMessage());
            return $FILELOAD;
        }
    }
}