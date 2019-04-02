<?php

class radicacionModel extends Model {

	public function __construct() {
		parent::__construct();		
	}	
	
	//Consulta ultimo radicado de un cliente por el numero de documento
	public function searchClientRadicado($document){
		$sql = "SELECT 
					Cliente.id AS CLIENTE_ID,
					Cliente.tipo_documento AS TIPO_DOCUMENTO_CLIENTE,
					CONCAT_WS('-',CONCAT(DATE_FORMAT(Radicacion.created, '%Y%m%d'),Cliente.id),Radicacion.consecutivo)  AS CONSECUTIVO_RADICADO,
					Radicacion.fecha_diligenciamiento AS FECHA_DILIGENCIAMIENTO,
					Radicacion.numero_pLanilla AS NUMERO_PLANILLA,
					Radicacion.tipo_cliente AS TIPO_RADICACION,
					Radicacion.tipo_medio AS TIPO_MEDIO,
					Radicacion.devuelto AS DEVUELTO,
					Radicacion.separado AS SEPARADO,
					Radicacion.cantidad_separada AS CANTIDAD_SEPARADA,
					Radicacion.digitalizado AS DIGITALIZADO,
					IF(ISNULL(Radicacion.formulario) AND ISNULL(Radicacion.radicacion_proceso),'Nuevo','Viejo') AS FORMULARIO,
					Radicacion.formulario AS FORMULARIO_V,
					Radicacion.cantidad_documentos AS CANTIDAD_DOCUMENTOS,
					Radicacion.medio_recepcion AS MEDIO_RECEPCION,
					Radicacion.radicacion_proceso AS RADICACION_PROCESO,
					Radicacion.correo_radicacion AS CORREO_RADICACION,
					Radicacion.linea_negocio_id AS LINEA_NEGOCIO_ID,
					Radicacion.radicacion_observacion AS RADICADO_OBSERVACION,
					Radicacion.created AS FECHA_CREACION,
					Radicacion.formulario_sarlaft AS FORMULARIO_SARLAFT,
					Radicacion.fecha_recepcion AS FECHA_RECEPCION,
					Radicacion.hora_recepcion AS HORA_RECEPCION,
					(SELECT ESTADO_PROCESO_ID FROM zr_estado_proceso_clientes_sarlaft WHERE PROCESO_CLIENTE_ID = ZEPCS.PROCESO_CLIENTE_ID AND ESTADO_PROCESO_ID != ZEPCS.ESTADO_PROCESO_ID ORDER BY ID DESC LIMIT 1) AS ANT_ESTADO_PROCESO_ID,
					ZEPCS.ESTADO_PROCESO_ID,
					ES.desc_type AS ESTADO_FORM
				FROM clientes AS Cliente 
				LEFT JOIN zr_radicacion AS Radicacion ON Radicacion.cliente_id = Cliente.id
					AND Radicacion.created = (SELECT MAX(created) FROM zr_radicacion WHERE cliente_id = Radicacion.cliente_id)
				LEFT JOIN zr_estado_proceso_clientes_sarlaft ZEPCS ON ZEPCS.PROCESO_CLIENTE_ID = Radicacion.cliente_id
					AND ZEPCS.FECHA_PROCESO = (SELECT MAX(FECHA_PROCESO) FROM zr_estado_proceso_clientes_sarlaft WHERE PROCESO_CLIENTE_ID = Cliente.id AND PROCESO_FECHA_DILIGENCIAMIENTO = (SELECT MAX(fecha_diligenciamiento) FROM zr_radicacion WHERE cliente_id = Cliente.id))
				LEFT JOIN estados_sarlaft ES ON ES.id = ZEPCS.ESTADO_PROCESO_ID
				WHERE 
					Cliente.documento = :document_cliente_radicado";

		$result = $this->_db->prepare($sql);

		$result->bindValue(':document_cliente_radicado',$document);
		$result->execute();

		$resultado = $result->execute();

        if(!is_null($result->errorInfo()[2]))
            return array('error' => $result->errorInfo()[2]);
        else
            return $result->fetch(PDO::FETCH_ASSOC);
	}

	

	//Consulta el ultimo numero de radicado para asignarlo a un cliente
	public function NRadicado(){
		$result = $this->_db->prepare("SELECT IFNULL((MAX(zr_radicacion.consecutivo)+1),1) AS CONSECUTIVO_RADICADO FROM zr_radicacion");
		$result->execute();
		return $result->fetch(PDO::FETCH_ASSOC);
	}

	

	//Consulta la ultima fecha de diligienciamiento de un cliente por el id
	public function getUltFechaDiligenciamientoSarlaftByClienteId($cliente_id){

		$sql = "SELECT
					MAX(Radicacion.fecha_diligenciamiento) AS ULT_FECHA_DILIGENCIAMIENTO
				FROM zr_radicacion Radicacion
				WHERE 
					Radicacion.formulario_sarlaft = 1
					AND Radicacion.radicacion_proceso = 'LEGAL'
					AND Radicacion.devuelto = 'No'
					AND Radicacion.cliente_id = :cliente_id";
		$result = $this->_db->prepare($sql);
		$result->bindValue(':cliente_id',$cliente_id,PDO::PARAM_INT);
		$resultado = $result->execute();
        if(!is_null($result->errorInfo()[2]))
            return $result->errorInfo()[2];
        else
            return $result->fetch(PDO::FETCH_ASSOC);
	}

	//Consulta la ultima fecha de diligienciamiento de un cliente por el id
	public function getFechaDiligenciamientoSarlaftByClienteId($cliente_id,$fecha_diligenciamiento){

		$sql = "SELECT
					Radicacion.fecha_diligenciamiento AS ULT_FECHA_DILIGENCIAMIENTO
				FROM zr_radicacion Radicacion
				WHERE 
					Radicacion.formulario_sarlaft = 1
					AND Radicacion.radicacion_proceso = 'LEGAL'
					AND Radicacion.devuelto = 'No'
					AND Radicacion.cliente_id = :cliente_id
					AND Radicacion.fecha_diligenciamiento = :fecha_diligenciamiento";
		$result = $this->_db->prepare($sql);
		$result->bindValue(':cliente_id',$cliente_id,PDO::PARAM_INT);
		$result->bindValue(':fecha_diligenciamiento',$fecha_diligenciamiento);
		$resultado = $result->execute();

        if(!is_null($result->errorInfo()[2]))
            return $result->errorInfo()[2];
        else
            return $result->fetch(PDO::FETCH_ASSOC);
	}

	//Consulta la cantidad de radicaciones por el usuario actual
	public function cantidadRadicacionesUser($user_id){

		$sql = "SELECT 
					IFNULL(SUM(zr_radicacion.cantidad_documentos),0) AS CANTIDAD_DOCUMENTOS,
					COUNT(*) AS CANTIDAD_RADICACIONES
				FROM zr_radicacion
				WHERE zr_radicacion.funcionario_id = :user_id
					AND DATE(created) = DATE(NOW())";
		$result = $this->_db->prepare($sql);
		$result->bindValue(':user_id',$user_id,PDO::PARAM_INT);
		$resultado = $result->execute();
        if(!is_null($result->errorInfo()[2]))
            return array('error' => $result->errorInfo()[2]);
        else
            return $result->fetch(PDO::FETCH_ASSOC);
	}

	//Consulta la cantidad de radicaciones por cliente
	public function cantidadRadicacionesCliente($id, $fecha){

		$sql = "SELECT 
					zr_radicacion.fecha_diligenciamiento AS FECHA,
					COUNT(*) AS CANTIDAD_RADICACIONES
				FROM zr_radicacion
				WHERE zr_radicacion.cliente_id = :id
				AND zr_radicacion.formulario_sarlaft = 1
				AND zr_radicacion.repetido = 0
				AND zr_radicacion.fecha_diligenciamiento = :fecha
				GROUP BY zr_radicacion.fecha_diligenciamiento
				HAVING COUNT(*) > 0";

		$result = $this->_db->prepare($sql);

		$result->bindValue(':id',$id,PDO::PARAM_INT);
		$result->bindValue(':fecha',$fecha);

		$resultado = $result->execute();
        if(!is_null($result->errorInfo()[2]))
            return array('error' => $result->errorInfo()[2]);
        else
            return $result->fetch(PDO::FETCH_ASSOC);
	}

	//Consulta el ultimo estado de un cliente por el id del cliente y por la fecha de diligenciamiento
	public function getEndStatusByClienteId($cliente_id,$fecha_diligenciamiento = NULL){

		$sql = "SELECT
					ZEPCS.PROCESO_USUARIO_ID,
					ZEPCS.PROCESO_CLIENTE_ID,
					ZEPCS.PROCESO_LINEA_NEGOCIO_ID,
					ZEPCS.PROCESO_FECHA_DILIGENCIAMIENTO,
					(SELECT ESTADO_PROCESO_ID FROM zr_estado_proceso_clientes_sarlaft WHERE PROCESO_CLIENTE_ID = ZEPCS.PROCESO_CLIENTE_ID ORDER BY ID DESC LIMIT 1,1) AS ANT_ESTADO_PROCESO_ID,
					ZEPCS.ESTADO_PROCESO_ID,
					ES.desc_type AS ESTADO_PROCESO_DESCRIPCION,
					ZEPCS.PROCESO_ACTIVO
				FROM
					zr_estado_proceso_clientes_sarlaft ZEPCS
				INNER JOIN estados_sarlaft ES ON ES.id = ZEPCS.ESTADO_PROCESO_ID
				WHERE ZEPCS.PROCESO_CLIENTE_ID = :cliente_id
					AND ZEPCS.PROCESO_FECHA_DILIGENCIAMIENTO = :fecha_diligenciamiento
					AND ZEPCS.FECHA_PROCESO = (SELECT MAX(FECHA_PROCESO) FROM zr_estado_proceso_clientes_sarlaft WHERE PROCESO_CLIENTE_ID = ZEPCS.PROCESO_CLIENTE_ID)";
		$result = $this->_db->prepare($sql);
		$result->bindValue(':cliente_id',$cliente_id,PDO::PARAM_INT);
		$result->bindValue(':fecha_diligenciamiento',$fecha_diligenciamiento,PDO::PARAM_INT);
		$resultado = $result->execute();

        if(!is_null($result->errorInfo()[2]))
            return array('error' => $result->errorInfo()[2]);
        else
            return $result->fetch(PDO::FETCH_ASSOC);
	}

	//Consulta la lista de abreviados de los documentos sarlaft dependiendo del tipo de proceso
	public function getAllAbreviadosSarlaftByProceso($radicacion_proceso){

		$sql = "SELECT * FROM zr_tipo_documento WHERE zr_tipo_documento.proceso = :radicacion_proceso";
		$result = $this->_db->prepare($sql);
		$result->bindValue(':radicacion_proceso',$radicacion_proceso);
		$resultado = $result->execute();

        if(!is_null($result->errorInfo()[2]))
            return array('error' => $result->errorInfo()[2]);
        else
            return $result->fetchAll(PDO::FETCH_ASSOC);
	}

	//Obtener listado de radicaciones por cliente - JAV01
    public function getListRadicacion($doc){
        $result = $this->_db->prepare("SELECT 
        	ZR.id AS ID_RADICACION,
			CONCAT_WS('-',CONCAT(DATE_FORMAT(DATE(ZR.created), '%Y%m%d'),ZR.cliente_id),ZR.consecutivo) AS NUMERO_RADICACION,
			TD.tipo_persona AS TIPO_PERSONA,
			CL.documento AS DOCUMENTO,
			ZR.fecha_diligenciamiento AS FECHA_DILIGENCIAMIENTO,
			DATE_FORMAT(ZR.created, '%Y-%m-%d') AS FECHA_RADICACION,
			DATE_FORMAT(ZR.created, '%r') AS HORA_RADICACION,
			US.nombre AS USUARIO_RADICACION
			FROM zr_radicacion ZR 
			INNER JOIN clientes CL 
			ON ZR.cliente_id = CL.id 
			INNER JOIN tipos_documentos TD 
			ON CL.tipo_documento = TD.id 
			INNER JOIN users US
			ON ZR.funcionario_id = US.id
			WHERE CL.documento = :doc
			ORDER BY ZR.created DESC");

        $result->bindValue(":doc", $doc);
        $result->execute();

        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    //Obtener listado de radicaciones por cliente - JAV01
    public function getLastRadicacion($doc){
        $result = $this->_db->prepare("SELECT 
        	ZR.id AS ID_RADICACION
			FROM zr_radicacion ZR 
			INNER JOIN clientes CL 
			ON ZR.cliente_id = CL.id
			WHERE 
			ZR.created = (SELECT MAX(zr_radicacion.created) FROM zr_radicacion WHERE zr_radicacion.cliente_id = ZR.cliente_id)
			AND CL.documento = :doc");

        $result->bindValue(":doc", $doc);
        $result->execute();

        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    //Consulta el ultimo numero de radicado para asignarlo a un cliente
	public function NRadicadoAnt($id){
		$result = $this->_db->prepare("SELECT zr_radicacion.consecutivo AS CONSECUTIVO_RADICADO FROM zr_radicacion WHERE zr_radicacion.id = :id ");
		$result->bindValue(':id',$id);
		$result->execute();
		return $result->fetch(PDO::FETCH_ASSOC);
	}

	//Consulta radicado de un cliente por el id de radicado
	public function searchClientRadicadoById($id){
		$sql = "SELECT 
				Cliente.id AS CLIENTE_ID,
				Cliente.documento AS CLIENTE_DOCUMENTO,
				Cliente.tipo_documento AS TIPO_DOCUMENTO_CLIENTE,
				CONCAT_WS('-',CONCAT(DATE_FORMAT(Radicacion.created, '%Y%m%d'),Cliente.id),Radicacion.consecutivo)  AS CONSECUTIVO_RADICADO,
				Radicacion.id AS RADICACION_ID,
				Radicacion.fecha_diligenciamiento AS FECHA_DILIGENCIAMIENTO,
				Radicacion.numero_pLanilla AS NUMERO_PLANILLA,
				Radicacion.tipo_cliente AS TIPO_RADICACION,
				Radicacion.tipo_medio AS TIPO_MEDIO,
				Radicacion.devuelto AS DEVUELTO,
				Radicacion.separado AS SEPARADO,
				Radicacion.cantidad_separada AS CANTIDAD_SEPARADA,
				Radicacion.digitalizado AS DIGITALIZADO,
				IF(ISNULL(Radicacion.formulario) AND ISNULL(Radicacion.radicacion_proceso),'Nuevo','Viejo') AS FORMULARIO,
				Radicacion.formulario AS FORMULARIO_V,
				Radicacion.cantidad_documentos AS CANTIDAD_DOCUMENTOS,
				Radicacion.medio_recepcion AS MEDIO_RECEPCION,
				Radicacion.radicacion_proceso AS RADICACION_PROCESO,
				Radicacion.correo_radicacion AS CORREO_RADICACION,
				Radicacion.linea_negocio_id AS LINEA_NEGOCIO_ID,
				Radicacion.radicacion_observacion AS RADICADO_OBSERVACION,
				Radicacion.created AS FECHA_CREACION,
				Radicacion.formulario_sarlaft AS FORMULARIO_SARLAFT,
				Radicacion.fecha_recepcion AS FECHA_RECEPCION,
				Radicacion.hora_recepcion AS HORA_RECEPCION
				FROM zr_radicacion AS Radicacion 
				INNER JOIN clientes AS Cliente ON Radicacion.cliente_id = Cliente.id
				WHERE Radicacion.id = :id";

		$result = $this->_db->prepare($sql);

		$result->bindValue(':id',$id);
		$result->execute();

        if(!is_null($result->errorInfo()[2]))
            return array('error' => $result->errorInfo()[2]);
        else
            return $result->fetch(PDO::FETCH_ASSOC);
	}

	// Obtener listado de archivos relacionados a una radicación
	public function getFilesRadicacion($id){
		$sql = "SELECT NOMBRE_ARCHIVO, CLIENTE_ID FROM relacion_archivo_radicacion WHERE RADICACION_ID = :id";
		$result = $this->_db->prepare($sql);

		$result->bindValue(':id',$id);
		$result->execute();

        if(!is_null($result->errorInfo()[2]))
            return array('error' => $result->errorInfo()[2]);
        else
            return $result->fetchAll();
	}

	// Obtener listado de usuarios confianza
	public function getAllUsuariosConfiaza(){
		$sql = "SELECT id, usuario, correo FROM usuarios_confianza";
		$result = $this->_db->prepare($sql);

		$result->execute();

        if(!is_null($result->errorInfo()[2]))
            return array('error' => $result->errorInfo()[2]);
        else
            return $result->fetchAll(PDO::FETCH_ASSOC);
	}
}