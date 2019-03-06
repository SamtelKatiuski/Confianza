<?php

//Clase encargada de interactuar con servidor FTP
class FTP
{
    //Conexión con FTP
    private $conn = null;

    //Abrir conexión con FTP
    public function __construct($host, $timeout = 90)
    {
        $this->conn = ftp_connect($host);
        ftp_set_option($this->conn, FTP_TIMEOUT_SEC, $timeout);
    }

    //Iniciar sesión en FTP
    public function login($username, $password, $pasive = false)
    {
        if($this->conn != null)
        {
            if(ftp_login($this->conn, $username, $password))
            {
                if($pasive)
                    ftp_pasv($this->conn, true);

                return true;
            }
        }

        return false;
    }

    //Saber si archivo existe en FTP
    public function fileExists($filename, $partial_file_name = false)
    {
        if($partial_file_name)
            return @count($this->list_files($filename , $partial_file_name));
        else
            return (ftp_size($this->conn, $filename) != -1 ? 1 : 0);
    }

    //Obtener archivo de FTP
    public function get($rute_file,$filename)
    {
        $path = $rute_file . "/" . $filename;

        $local_file = ROOT . "files/" . $filename;
        // var_dump($local_file);

        $res = @ftp_get($this->conn, $local_file, $path, FTP_BINARY);

        if($res)
            return $local_file;
        else
            return $res;
    }

    //Obtener lista de archivos en FTP, o buscar por nombre parcial
    public function list_files($folder = ".", $partial_file_name = false)
    {
        $files = array();

        if($partial_file_name)
        {
            $file = $folder . "/*" . $partial_file_name . "*";
            
            $files_list = @ftp_nlist($this->conn, $file);

            if(count($files_list))
            {
                foreach ($files_list as $row) 
                {
                    $files[] = $row;
                }
            }            
        }
        else
        {            
            $files_list = ftp_nlist($this->conn, $folder);

            if(count($files_list))
            {
                foreach ($files_list as $row) 
                {
                    $files[] = $row;
                }
            }
        }

        return $files;
    }

    //Cerrar conexión con FTP
    public function close()
    {
        if($this->conn != null)
            ftp_close($this->conn);
    }
}