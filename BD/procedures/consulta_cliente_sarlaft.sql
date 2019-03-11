DELIMITER $$

USE `confianza`$$

DROP PROCEDURE IF EXISTS `consulta_cliente_sarlaft`$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `consulta_cliente_sarlaft`(IN  `tipo_cliente_sarlaft` VARCHAR(20), IN  `cliente_sarlaft_id` INT)
BEGIN
	
	IF(tipo_cliente_sarlaft = 'NAT') THEN
			SELECT
				clientes.documento AS documento,
				clientes.id AS cliente_id,
				clientes.tipo_documento,
				TD.tipo_persona AS TIPO_PERSONA,
				CN.id AS formulario_id,
				ZR.linea_negocio_id,
				ZR.fecha_diligenciamiento,
				CONCAT_WS('-',CONCAT(DATE_FORMAT(DATE(ZR.created), '%Y%m%d'),ZR.cliente_id),ZR.consecutivo) AS numero_radicado,
				ZR.numero_planilla,
				IF((ISNULL(CN.primer_apellido) AND ISNULL(CN.segundo_apellido) AND ISNULL(CN.primer_nombre) AND ISNULL(CN.segundo_nombre)),'VACIO',UPPER(TRIM(CONCAT_WS(' ',CN.primer_apellido,CN.segundo_apellido,CN.primer_nombre,CN.segundo_nombre)))) AS NOMBRE_CLIENTE,
				ZEPCS.ESTADO_PROCESO_ID AS estado_formulario_id,
				estados_sarlaft.desc_type AS ESTADO_FORM,
				ZEPCS.PROCESO_ACTIVO AS PROCESO_ACTIVO,
				ZEPCS.PROCESO_NUM_INOUTBOUND AS NUMERO_MARCADO
			FROM clientes
				INNER JOIN tipos_documentos TD ON TD.id = clientes.tipo_documento
				INNER JOIN zr_radicacion ZR ON ZR.cliente_id = clientes.id
					AND ZR.created = (
						SELECT 
							MAX(ZR2.created) 
						FROM 
							zr_radicacion ZR2
						WHERE 
							ZR2.cliente_id = ZR.cliente_id
						AND ZR2.fecha_diligenciamiento = (
								SELECT 
									MAX(ZR3.fecha_diligenciamiento)
								FROM
									zr_radicacion ZR3
								WHERE
									ZR3.cliente_id = ZR2.cliente_id
								AND ZR3.formulario_sarlaft = 1
								AND ZR3.repetido = 0
								AND ZR3.radicacion_proceso = 'LEGAL'
						)						
					)
			INNER JOIN zr_estado_proceso_clientes_sarlaft ZEPCS ON ZEPCS.PROCESO_CLIENTE_ID = ZR.cliente_id
				AND ZEPCS.FECHA_PROCESO = (
					SELECT 
						MAX(ZEPCS2.FECHA_PROCESO) 
					FROM 
						zr_estado_proceso_clientes_sarlaft ZEPCS2
					WHERE 
						ZEPCS2.PROCESO_CLIENTE_ID = ZEPCS.PROCESO_CLIENTE_ID
						AND ZEPCS2.PROCESO_FECHA_DILIGENCIAMIENTO = ZR.fecha_diligenciamiento
				)
			INNER JOIN estados_sarlaft ON estados_sarlaft.id = ZEPCS.ESTADO_PROCESO_ID
			LEFT JOIN cliente_sarlaft_natural CN ON CN.cliente = clientes.id
			WHERE
				TD.tipo_persona = 'NAT'
				AND clientes.id = cliente_sarlaft_id
			ORDER BY ZR.id DESC
			LIMIT 1;
		ELSE
			SELECT
				clientes.documento AS documento,
				clientes.id AS cliente_id,
				clientes.tipo_documento,
				TD.tipo_persona AS TIPO_PERSONA,
				CJ.id AS formulario_id,
				ZR.linea_negocio_id,
				ZR.fecha_diligenciamiento,
				CONCAT_WS('-',CONCAT(DATE_FORMAT(DATE(ZR.created), '%Y%m%d'),ZR.cliente_id),ZR.consecutivo) AS numero_radicado,
				ZR.numero_planilla,
				UPPER(IFNULL(CJ.razon_social,'VACIO')) AS NOMBRE_CLIENTE,
				ZEPCS.ESTADO_PROCESO_ID AS estado_formulario_id,
				estados_sarlaft.desc_type AS ESTADO_FORM,
				ZEPCS.PROCESO_ACTIVO AS PROCESO_ACTIVO,
				ZEPCS.PROCESO_NUM_INOUTBOUND AS NUMERO_MARCADO
			FROM 
				clientes
			INNER JOIN tipos_documentos TD ON TD.id = clientes.tipo_documento
			INNER JOIN zr_radicacion ZR ON ZR.cliente_id = clientes.id
					AND ZR.created = (
						SELECT 
							MAX(ZR2.created) 
						FROM 
							zr_radicacion ZR2
						WHERE 
							ZR2.cliente_id = ZR.cliente_id
						AND ZR2.fecha_diligenciamiento = (
								SELECT 
									MAX(ZR3.fecha_diligenciamiento)
								FROM
									zr_radicacion ZR3
								WHERE
									ZR3.cliente_id = ZR2.cliente_id
								AND ZR3.formulario_sarlaft = 1
								AND ZR3.repetido = 0
								AND ZR3.radicacion_proceso = 'LEGAL'
						)						
					)
			INNER JOIN zr_estado_proceso_clientes_sarlaft ZEPCS ON ZEPCS.PROCESO_CLIENTE_ID = ZR.cliente_id
				AND ZEPCS.FECHA_PROCESO = (
					SELECT 
						MAX(ZEPCS2.FECHA_PROCESO) 
					FROM 
						zr_estado_proceso_clientes_sarlaft ZEPCS2
					WHERE 
						ZEPCS2.PROCESO_CLIENTE_ID = ZEPCS.PROCESO_CLIENTE_ID
						AND ZEPCS2.PROCESO_FECHA_DILIGENCIAMIENTO = ZR.fecha_diligenciamiento
				)
			INNER JOIN estados_sarlaft ON estados_sarlaft.id = ZEPCS.ESTADO_PROCESO_ID 
			LEFT JOIN cliente_sarlaft_juridico CJ ON CJ.cliente = clientes.id
			WHERE
				TD.tipo_persona = 'JUR'
				AND clientes.id = cliente_sarlaft_id
			ORDER BY ZR.id DESC
			LIMIT 1;
	END IF;
END$$

DELIMITER ;