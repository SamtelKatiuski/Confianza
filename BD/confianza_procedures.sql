/*
SQLyog Community v13.1.2 (64 bit)
MySQL - 10.1.10-MariaDB : Database - confianza
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`confianza` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `confianza`;

/* Procedure structure for procedure `consulta_cliente_sarlaft` */

/*!50003 DROP PROCEDURE IF EXISTS  `consulta_cliente_sarlaft` */;

DELIMITER $$

/*!50003 CREATE PROCEDURE `consulta_cliente_sarlaft`(IN  `tipo_cliente_sarlaft` varchar(20), IN  `cliente_sarlaft_id` int)
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
END */$$
DELIMITER ;

/* Procedure structure for procedure `consulta_documentos` */

/*!50003 DROP PROCEDURE IF EXISTS  `consulta_documentos` */;

DELIMITER $$

/*!50003 CREATE PROCEDURE `consulta_documentos`(IN `padre` varCHAR(200), IN `anho_filtro` INT, IN `nombre_cliente_filtro` varCHAR(50), IN `numero_documento` vARCHAR(50), IN `poliza` varCHAR(50), IN `siniestro` vaRCHAR(50), IN `linea_negocio` INT)
BEGIN
  if ( poliza = '' ) then
	SELECT distinct
		CASE 
			WHEN length(padre) = length(carpeta) then nombre_archivo
            ELSE
			  case 
			  when instr( substr(carpeta, length(padre) +1) , '\\') =0  then substr(carpeta, length(padre) +1)
			  else substring(carpeta,  1+length(padre),  instr( substr(carpeta, length(padre) +1) , '\\')   )
			  end
		END nodo
 	FROM archivo_organizado
	where carpeta like concat(replace(padre,'\\','_'),'%')   
	  and (anho = anho_filtro or anho_filtro = 0)
     and ( nombre_cliente like concat('%',nombre_cliente_filtro, '%') or nombre_cliente_filtro='' OR nombre_consorc like concat('%',nombre_cliente_filtro, '%'))
	  and ( numero_ident_cliente like concat(numero_documento, '%')  or numero_documento = '' or NUMERO_IDENT_CONSORC like concat(numero_documento, '%'))
	  and ( numero_siniestro = siniestro or siniestro = '')
      and (id_linea_negocio =  linea_negocio or linea_negocio =0);
  else
		(
			SELECT distinct
				CASE 
					WHEN length(padre) = length(carpeta) then nombre_archivo
		            ELSE
					  case 
					  when instr( substr(carpeta, length(padre) +1) , '\\') =0  then substr(carpeta, length(padre) +1)
					  else substring(carpeta,  1+length(padre),  instr( substr(carpeta, length(padre) +1) , '\\')   )
					  end
				END nodo
		 	FROM archivo_organizado
			where carpeta like concat(replace(padre,'\\','_'),'%')
			  and num_poliza = poliza
		     and (anho = anho_filtro or anho_filtro = 0)
		     and ( nombre_cliente like concat('%',nombre_cliente_filtro, '%') or nombre_cliente_filtro='' OR nombre_consorc like concat('%',nombre_cliente_filtro, '%'))
			  and ( numero_ident_cliente like concat(numero_documento, '%') or numero_documento = '' or NUMERO_IDENT_CONSORC like concat(numero_documento, '%'))
			  and ( numero_siniestro = siniestro or siniestro = '') 
			  and (id_linea_negocio =  linea_negocio or linea_negocio =0)   
	  )
		union all
		(
			SELECT distinct
				CASE 
					WHEN length(padre) = length(carpeta) then nombre_archivo
		            ELSE
					  case 
					  when instr( substr(carpeta, length(padre) +1) , '\\') =0  then substr(carpeta, length(padre) +1)
					  else substring(carpeta,  1+length(padre),  instr( substr(carpeta, length(padre) +1) , '\\')   )
					  end
				END nodo
		 	FROM archivo_organizado
			where carpeta like concat(replace(padre,'\\','_'),'%')
			  and num_poliza is null
		     and (anho = anho_filtro or anho_filtro = 0)
		     and ( nombre_cliente like concat('%',nombre_cliente_filtro, '%') or nombre_cliente_filtro='' OR nombre_consorc like concat('%',nombre_cliente_filtro, '%'))
			  and ( numero_ident_cliente like concat(numero_documento, '%') or numero_documento = '' or NUMERO_IDENT_CONSORC like concat(numero_documento, '%'))
			  and ( numero_siniestro = siniestro or siniestro = '')
			  and (id_linea_negocio =  linea_negocio or linea_negocio =0)
			  and exists ( select 1
					from archivo_organizado a2
		    		where a2.num_poliza = poliza
		      	 and a2.TIPO_IDENT_CLIENTE = archivo_organizado.TIPO_IDENT_CLIENTE
			  		 and a2.numero_ident_cliente = archivo_organizado.numero_ident_cliente)
		);
	end if;
	
END */$$
DELIMITER ;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
