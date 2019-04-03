<?php
class visualizacionController extends Controller 
{
    public $_clientes;
    public $_visualizador;
    
    public function __construct() {

        if(Session::get('Mundial_authenticate')){

            if(in_array(Session::getLevel(Session::get("Mundial_user_rol")),[
                Session::getLevel('Gerente'),
                Session::getLevel('Operador Asistemyca'),
                Session::getLevel('Operador Mundial'),
                Session::getLevel('Reportes'),
                Session::getLevel('Operador Radicador'),
                Session::getLevel('Perfil Intermedio')
            ])){

                try {

                    parent::__construct();

                    $this->_global = $this->loadModel("global");
                    $this->_clientes = $this->loadModel("clientes");     
                    $this->_visualizador = $this->loadModel("visualizador");

                } catch (Exception $e) {
                    die($e->getMessage());
                }
            }else{
                $this->redireccionar('error', 'access', ['5656']);
            }
        }else{
            $this->redireccionar('error', 'access', ['5656']);
        }
    }

    public function index() {
        
        $this->_view->titulo = "Visualización"; 
        $this->_view->setJs(array('index'));           
        $this->_view->linea = $this->_visualizador->getBussinesLines(); 
        $this->_view->documentos = $this->_global->getAllTypeClients();
        $this->_view->renderizar('index', 'visualizacion');
    }

    // ********************** JAV01 ***************************
    
    //Obtiene listado de carpetas por filtro de busqueda
    public function folder_search() {

        if(Server::RequestMethod("POST")) {
            $data = Server::post();
            $data["padre"] = FOLDERS_PATH;

            $res = $this->_visualizador->getFoldersList($data);

            if(count($res))
            {
                $html = '<ul>';

                foreach ($res as $row) {
                    $val = '';
                    $class = '';
                    $pos = substr($val, -1);
                        
                    if($pos != '\\')   
                    {
                        $val = substr_replace($row["nodo"], "", -1);
                        $class = 'folder inactive';
                    }
                    else
                    {
                        $val = $row["nodo"];
                        $class = 'file ';

                    }

                    $html .= "<li class='" . $class . "' open_folder='no' path='" . $data["padre"] . $row["nodo"] . "' parms='" . json_encode($data) . "'><label style='padding: 3px 30px;'>" . $val . "</label></li>";
                }

                $html .= "</ul>";
                
            } else {

                $html = '<ul><li class="search-empty">No existen coincidencias con su busqueda...</li></ul>';
            }

            echo $html;

        } else {

            $this->redireccionar("error/access/5656");
        }
    }

    //Obtener listado de carpetas hijas
    public function getNextLevelFolder() {

        if(Server::RequestMethod("POST")) {

            $data = Server::post();

            $res = $this->_visualizador->getFoldersList($data);
            $html = '';

            if(count($res))
            {
                $html = '<ul style="padding: 0px 22px;">';

                foreach ($res as $row) 
                {
                    $val = $row["nodo"];
                    $class = '';
                    $type = '';
                    $pos = substr($val, -1);
                    
                    if($pos === '\\')   
                    {
                        $val = substr_replace($val, "", -1);
                        $class = 'folder inactive';
                    }
                    else
                    {
                        if(strpos($val, ".") === false)
                            $class = "folder inactive";
                        else
                        {
                            $class = "file " ;  
                            // Visualización de iconos de acuerdo a tipo de archivo
                            $ext = explode('.', $val);
                            $ext = $ext[count($ext) - 1];

                            switch (strtolower($ext))
                            {
                                case 'png':
                                case 'gif':
                                case 'jpg':
                                case 'jpeg' :
                                    $type = 'image';
                                    break;                           
                                case 'pdf':
                                    $type = 'pdf';
                                    break;
                                case 'xls':
                                case 'xlsx': 
                                case 'xlsm':
                                    $type = 'excel';
                                    break;
                                case 'doc':
                                case 'docx':
                                    $type = 'word';
                                    break; 
                                default:
                                    $type = 'default-file';
                                    break;
                            }
                        }
                    }

                    $pos = substr($data["padre"], -1);
                    
                    if($pos !== '\\')
                        $fld = $data["padre"] . '\\';
                    else
                        $fld = $data["padre"] ;

                    $html .= "<li class='" . $class . $type . " new-level' open_folder='no' path='" . $fld . $row["nodo"] . "' parms='" . json_encode($data) . "'><label style='padding: 0px 0px 0px 30px;'>" . $val . "</label></li>";
                }

                $html .= "</ul>";
                
            }

            echo json_encode(array("res" => $html, "data" => $data));
        } else {

            $this->redireccionar("error/access/5656");
        }
    }

    public function fileViewer() {
        if(Server::RequestMethod("POST")) {

            $path = Server::post("pathFolder");

            if(file_exists($path))
            {
                $path = str_replace(ROOT, BASE_URL, $path);
                
                echo '<iframe src="' . $path . '" width="100%" height="100%" style="border: none;" ></iframe>';
            }
            else
            {
                echo "<div class='viewer-error'>El archivo específico no existe en la ruta indicada...</div>";
            }

        } else {

            $this->redireccionar("error/access/5656");
        }
    }
}