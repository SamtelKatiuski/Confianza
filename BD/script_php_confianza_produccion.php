<?php

class Query {

	private $db;

	function __construct() {
		try {
			$this->db = new PDO('mysql:host=localhost; dbname=confianza_test', 'root', '');
			$this->db->exec('set character set utf8');	
		} catch (PDOException $ex) {
			throw new Exception("Error de conexion", 1);
		}
	}

	/**
	 * Es la función principal desde la cual se ejecutará el código
	 */
	public function init() {
		/**
		 * Listo las tablas a las cuales se les crearán o actualizarán campos
		 */
		$tables = [
			'zr_anexos_ppes',
			'productos',
			'cliente_sarlaft_natural',
			'cliente_sarlaft_juridico',
			'accionistas'
		];
		/**
		 * Recorro las tablas para trabajar en ella
		 */
		foreach ($tables as $table) {
			echo "<hr>Tabla: <strong>{$table}</strong><br><hr>";
			if ($this->getTable($table)) { //Si la tabla existe
				$columns = $this->getColums($table); //Obtengo las columnas
				if (!empty($columns)) {
					$newFields = $this->getNewOrUpdateColumns($table); //Obtengo las columnas a crear o a actualizar
					foreach ($newFields as $valueNewField) { //Recorro las columnas
						$partsQuery = explode(' ', $valueNewField);
						/**
						 * Si la columna existe en la tabla, se actualiza el tipo de dato
						 */
						$columName = $partsQuery[0];
						array_shift($partsQuery);
						if (in_array($columName, $columns)) {
							echo "Columna: <strong>{$columName}</strong> a actualizar...<br>";
							$query = "ALTER TABLE `{$table}` MODIFY `{$columName}` ";
							$query .= implode(' ', $partsQuery);
							try {
								$statement = $this->db->prepare($query);
								if ($statement->execute()) {
									echo "Columna: <strong>{$columName}</strong> actualizada correctamente en la tabla  <strong>{$table}</strong>...<br>";
								} else {
									echo "Columna: <strong>{$columName}</strong> / <strong>Error</strong> al actualizar en la tabla  <strong>{$table}</strong>...<br>";
								}								
							} catch (PDOException $ex) {
								echo "Columna: <strong>{$columName}</strong> / <strong>Error:</strong>" . $ex->getMessage() . " <strong>Linea:</strong> " . $ex->getLine() . "<br>";
							}
							/**
							 * Si la columna no existe en la tabla, se crea
							 */
						} else {
							echo "Columna: <strong>{$columName}</strong> a crear...<br>";
							$query = "ALTER TABLE `{$table}` ADD `{$columName}` ";
							$query .= implode(' ', $partsQuery);
							try {
								$statement = $this->db->prepare($query);
								if ($statement->execute()) {
									echo "Columna: <strong>{$columName}</strong> creada correctamente en la tabla  <strong>{$table}</strong>...<br>";
								} else {
									echo "Columna: <strong>{$columName}</strong> / <strong>Error</strong> al crear en la tabla  <strong>{$table}</strong>...<br>";
								}								
							} catch (PDOException $ex) {
								echo "Columna: <strong>{$columName}</strong> / <strong>Error:</strong>" . $ex->getMessage() . " <strong>Linea: </strong>" . $ex->getLine() . "<br>";
							}
						}
					}
				}
			} else {
				echo "La tabla <strong>{$table}</strong> no existe...";
			}
		}
	}

	/**
	 * Valida si la tabla existe en la base de datos
	 * @param string $table corresponde al nombre de la tabla
	 * @return boolean devuelve TRUE si existe, de lo contrario devuelve FALSE
	 */
	public function getTable($table) {
		try {
			$query = "show tables";
			$statement = $this->db->prepare($query);
			if ($statement) {
				$statement->execute();
				if ($result = $statement->fetchAll(PDO::FETCH_NUM)) {
					foreach ($result as $value) {
						$tables[] = $value[0];
					}
					if (isset($tables) && !empty($tables)) {
						if (in_array($table, $tables)) {
							return true;
						}
					}
				}
			}
			return false;
		} catch (PDOException $ex) {
			throw $ex;
		}
	}

	/**
	 * Obtiene las columnas de una tabla
	 * @param string $table corresponde al nombre de la tabla de la cual se obtendrán las columnas
	 * @return array $columnsTable es el array con las columnas de la tabla
	 */
	public function getColums($table) {
		try {
			$query = "DESC {$table}";
			if (!is_null($this->db)) {
				$statement = $this->db->prepare($query);
				if ($statement) {
					$statement->execute();
					foreach ($field = $statement->fetchAll(PDO::FETCH_ASSOC) as $valueField) {
						$columnsTable[] = $valueField['Field'];
					}
					return $columnsTable;
				}
			}
			return [];
		} catch (PDOException $ex) {
			throw new Exception("Error al obtener las columnas", 1);
		}
	}

	/**
	 * Obtiene los campos a crear o a actualizar de una tabla
	 * @param string $table es el nombre de la tabla
	 * @return array $campos es un array con los campos
	 */
	public function getNewOrUpdateColumns($table) {
		$campos = [];
		switch ($table) {
			case 'zr_anexos_ppes':
				$campos = [
					"ppes_vinculo_relacion VARCHAR(255) DEFAULT NULL",
					"ppes_nombre VARCHAR(255) DEFAULT NULL",
					"ppes_tipo_identificacion INT(1) DEFAULT NULL",
					"ppes_no_documento VARCHAR(255) DEFAULT NULL",
					"ppes_nacionalidad INT(1) DEFAULT NULL",
					"ppes_entidad VARCHAR(255) DEFAULT NULL",
					"ppes_cargo VARCHAR(255) DEFAULT NULL",
					"ppes_fecha_ingreso DATE DEFAULT NULL",
					"ppes_desvinculacion DATE DEFAULT NULL",
					"ppes_motivo VARCHAR(255) DEFAULT NULL"
				];
				break;
			case 'productos':
				$campos = [
					"tipo_producto VARCHAR(30) DEFAULT NULL",
					"identificacion_producto VARCHAR(30) DEFAULT NULL",
					"entidad VARCHAR(30) DEFAULT NULL",
					"monto DECIMAL(10, 0) DEFAULT NULL",
					"ciudad VARCHAR(30) DEFAULT NULL",
					"pais VARCHAR(30) DEFAULT NULL",
					"moneda VARCHAR(20) DEFAULT NULL"
				];
				break;
			case 'cliente_sarlaft_natural':
				$campos = [
					"sucursal VARCHAR(30) DEFAULT NULL",
					"persona_publica VARCHAR(5) DEFAULT NULL",
					"vinculo_persona_publica VARCHAR(5) DEFAULT NULL",
					"productos_publicos VARCHAR(5) DEFAULT NULL",
					"obligaciones_tributarias_otro_pais VARCHAR(5) DEFAULT NULL",
					"desc_obligaciones_tributarias_otro_pais VARCHAR(130) DEFAULT NULL",
					"anexo_preguntas_ppes INT(1) DEFAULT NULL",
					"operaciones_moneda_extranjera VARCHAR(5) DEFAULT NULL",
					"cuentas_moneda_exterior VARCHAR(5) DEFAULT NULL",
					"productos_exterior VARCHAR(5) DEFAULT NULL",
					"reclamaciones INT(1) DEFAULT NULL",
					"reclamacion_anio INT(4) DEFAULT NULL",
					"reclamacion_ramo VARCHAR(40) DEFAULT NULL",
					"reclamacion_compania VARCHAR(40) DEFAULT NULL",
					"reclamacion_valor BIGINT(100) DEFAULT NULL",
					"reclamacion_resultado INT(1) DEFAULT NULL",
					"reclamacion_anio_2 INT(11) DEFAULT NULL",
					"reclamacion_ramo_2 VARCHAR(40) DEFAULT NULL",
					"reclamacion_compania_2 VARCHAR(40) DEFAULT NULL",
					"reclamacion_valor_2 BIGINT(100) DEFAULT NULL",
					"reclamacion_resultado_2 INT(1) DEFAULT NULL",
					"huella INT(1) DEFAULT NULL",
					"firma INT(1) DEFAULT NULL",
					"verificacion INT(1) DEFAULT NULL",
					"entrevista INT(1) DEFAULT NULL",
					"autoriza_tratamiento VARCHAR(5) DEFAULT NULL",
					"autoriza_info_fasecolda VARCHAR(5) DEFAULT NULL",
					"tipo_moneda VARCHAR(80) DEFAULT NULL",
					"actividad_eco_principal VARCHAR(50) DEFAULT NULL",
					"ciiu_cod VARCHAR(50) DEFAULT NULL"
				];
				break;
			case 'cliente_sarlaft_juridico':
				$campos = [
					"residencia_sociedad VARCHAR(30) DEFAULT NULL",
					"reclamaciones INT(1) DEFAULT NULL",
					"reclamacion_anio INT(4) DEFAULT NULL",
					"reclamacion_ramo VARCHAR(40) DEFAULT NULL",
					"reclamacion_compania VARCHAR(40) DEFAULT NULL",
					"reclamacion_valor BIGINT(100) DEFAULT NULL",
					"reclamacion_resultado INT(1) DEFAULT NULL",
					"reclamacion_anio_2 INT(11) DEFAULT NULL",
					"reclamacion_ramo_2 VARCHAR(40) DEFAULT NULL",
					"reclamacion_compania_2 VARCHAR(40) DEFAULT NULL",
					"reclamacion_valor_2 BIGINT(100) DEFAULT NULL",
					"reclamacion_resultado_2 INT(1) DEFAULT NULL",
					"rep_legal_persona_publica VARCHAR(5) DEFAULT NULL",
					"rep_legal_recursos_publicos VARCHAR(5) DEFAULT NULL",
					"rep_legal_obligaciones_tributarias VARCHAR(5) DEFAULT NULL",
					"anexo_accionistas VARCHAR(5) DEFAULT NULL",
					"anexo_sub_accionistas INT(1) DEFAULT NULL",
					"anexo_preguntas_ppes INT(1) DEFAULT NULL",
					"operaciones_moneda_extranjera VARCHAR(5) DEFAULT NULL",
					"cuentas_moneda_exterior VARCHAR(5) DEFAULT NULL",
					"productos_exterior VARCHAR(5) DEFAULT NULL",
					"huella INT(1) DEFAULT NULL",
					"firma INT(1) DEFAULT NULL",
					"verificacion INT(1) DEFAULT NULL",
					"entrevista INT(1) DEFAULT NULL",
					"autoriza_tratamiento VARCHAR(5) DEFAULT NULL",
					"autoriza_info_fasecolda VARCHAR(5) DEFAULT NULL",
					"tipo_moneda VARCHAR(80) DEFAULT NULL",
					"ofi_principal_ciiu VARCHAR(50) DEFAULT NULL",
					"ofi_principal_ciiu_cod VARCHAR(50) DEFAULT NULL"
				];
				break;
			case 'accionistas':
				$campos = [
					"accionista_cotiza_bolsa VARCHAR(5) DEFAULT NULL",
					"accionista_persona_publica VARCHAR(5) DEFAULT NULL",
					"accionista_obligaciones_otro_pais VARCHAR(5) DEFAULT NULL"
				];
				break;
		}
		return $campos;
	}
}

$objetoQuery = new Query();
$objetoQuery->init();