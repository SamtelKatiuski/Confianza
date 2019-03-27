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

/*Table structure for table `clientes_sarlaft` */

DROP TABLE IF EXISTS `clientes_sarlaft`;

/*!50001 DROP VIEW IF EXISTS `clientes_sarlaft` */;
/*!50001 DROP TABLE IF EXISTS `clientes_sarlaft` */;

/*!50001 CREATE TABLE  `clientes_sarlaft`(
 `documento` varchar(100) ,
 `cliente_id` int(11) ,
 `tipo_documento` int(11) ,
 `TIPO_PERSONA` varchar(3) ,
 `formulario_id` int(11) ,
 `fecha_diligenciamiento` date ,
 `numero_radicado` varchar(70) ,
 `numero_planilla` varchar(255) ,
 `NOMBRE_CLIENTE` varchar(323) ,
 `estado_formulario_id` int(11) ,
 `ESTADO_FORM` varchar(50) ,
 `PROCESO_ACTIVO` tinyint(4) 
)*/;

/*Table structure for table `reporte_cargue_clientes_juridicos` */

DROP TABLE IF EXISTS `reporte_cargue_clientes_juridicos`;

/*!50001 DROP VIEW IF EXISTS `reporte_cargue_clientes_juridicos` */;
/*!50001 DROP TABLE IF EXISTS `reporte_cargue_clientes_juridicos` */;

/*!50001 CREATE TABLE  `reporte_cargue_clientes_juridicos`(
 `CLIENTE_ID` int(11) ,
 `FECHA_RADICACION` datetime ,
 `USUARIO_RADICAICON` varchar(80) ,
 `FECHA_CAPTURA` datetime ,
 `FECHA_ACTUALIZACION` datetime ,
 `FECHA_DILIGENCIAMIENTO` date ,
 `COD_ASEG` binary(0) ,
 `TIPO_DOCUMENTO` int(11) ,
 `NOMBRE_TOMADOR` varchar(230) ,
 `IDENTIFICACION_TOMADOR` varchar(100) ,
 `REPRESENTANTE_LEGAL_PRIMER_APELLIDO` varchar(255) ,
 `REPRESENTANTE_LEGAL_SEGUNDO_APELLIDO` varchar(255) ,
 `REPRESENTANTE_LEGAL_NOMBRES` varchar(255) ,
 `REPRESENTANTE_TIPO_DOCUMENTO` int(11) ,
 `REPRESENTANTE_DOCUMENTO` int(11) ,
 `REPRESENTANTE_DIRECCION_RESIDENCIA` text ,
 `REPRESENTANTE_CIUDAD_RESIDENCIA` varchar(80) ,
 `REPRESENTANTE_TELEFONO_RESIDENCIA` varchar(100) ,
 `SUCURSAL_DIRECCION` varchar(100) ,
 `SUCURSAL_CIUDAD` varchar(80) ,
 `SUCURSAL_TELEFONO` varchar(100) ,
 `SUCURSAL_FAX` varchar(255) ,
 `CELULAR` varchar(50) ,
 `TELEFONO` varchar(100) ,
 `TIPO_EMPRESA` varchar(255) ,
 `CIIU_COD` varchar(50) ,
 `ACTIVIDAD_ECONOMICA` varchar(500) ,
 `INGRESOS` bigint(100) ,
 `EGRESOS` bigint(100) ,
 `ACTIVOS` bigint(100) ,
 `PASIVOS` bigint(100) ,
 `PATRIMONIO` bigint(100) ,
 `OTROS_INGRESOS` bigint(100) ,
 `CONCEPTO_OTROS_INGRESOS` varchar(100) ,
 `TRANSACCIONES_MONEDA_EXTRANJERA` varchar(2) ,
 `TRANSACCIONES_MONEDA_EXTRANJERA_CUAL` varchar(80) ,
 `TRANSACCIONES_MONEDA_EXTRANJERA_OTRAS` varchar(100) ,
 `DECLARACION_ORIGEN_FONDOS` varchar(130) ,
 `CIUDAD_DILIGENCIAMIENTO` varchar(80) ,
 `SUCURSAL` varchar(30) 
)*/;

/*Table structure for table `reporte_cargue_clientes_naturales` */

DROP TABLE IF EXISTS `reporte_cargue_clientes_naturales`;

/*!50001 DROP VIEW IF EXISTS `reporte_cargue_clientes_naturales` */;
/*!50001 DROP TABLE IF EXISTS `reporte_cargue_clientes_naturales` */;

/*!50001 CREATE TABLE  `reporte_cargue_clientes_naturales`(
 `CLIENTE_ID` int(11) ,
 `FECHA_RADICACION` datetime ,
 `USUARIO_RADICAICON` varchar(80) ,
 `FECHA_CAPTURA` datetime ,
 `FECHA_VERIFICACION` datetime ,
 `FECHA_DILIGENCIAMIENTO` date ,
 `COD_ASEG` binary(0) ,
 `PRIMER_APELLIDO` varchar(80) ,
 `SEGUNDO_APELLIDO` varchar(80) ,
 `NOMBRES` varchar(161) ,
 `TIPO_DOCUMENTO` int(11) ,
 `NUMERO_DOCUMENTO` varchar(100) ,
 `LUGAR_NACIMIENTO` varchar(150) ,
 `FECHA_NACIMIENTO` varchar(10) ,
 `OCUPACION_1` varchar(130) ,
 `OCUPACION_2` binary(0) ,
 `CIIU_COD` varchar(50) ,
 `TIPO_ACTIVIDAD` varchar(130) ,
 `EMPRESA_DONDE_TRABAJA` varchar(140) ,
 `CARGO` varchar(130) ,
 `CIUDAD_EMPRESA` varchar(80) ,
 `DIRECCION_EMPRESA` text ,
 `TELEFONO_EMPRESA` varchar(100) ,
 `DIRECCION_RESIDENCIA` text ,
 `CIUDAD_RESIDENCIA` varchar(80) ,
 `DEPARTAMENTO_RESIDENCIA` varchar(80) ,
 `TELEFONO_RESIDENCIA` varchar(100) ,
 `CELULAR` bigint(20) ,
 `INGRESOS` bigint(100) ,
 `EGRESOS` bigint(100) ,
 `ACTIVOS` bigint(100) ,
 `PASIVOS` bigint(100) ,
 `PATRIMONIO` bigint(100) ,
 `OTROS_INGRESOS` bigint(100) ,
 `CONCEPTO_OTROS_INGRESOS` varchar(100) ,
 `TRANSACCIONES_MONEDA_EXTRANJERA` varchar(2) ,
 `TRANSACCIONES_MONEDA_EXTRANJERA_CUAL` varchar(80) ,
 `TRANSACCIONES_MONEDA_EXTRANJERA_OTRAS` text ,
 `DECLARACION_ORIGEN_FONDOS` varchar(130) ,
 `CIUDAD_DILIGENCIAMIENTO` varchar(80) ,
 `SUCURSAL` varchar(30) 
)*/;

/*Table structure for table `reporte_clientes_beneficiario_final_peps` */

DROP TABLE IF EXISTS `reporte_clientes_beneficiario_final_peps`;

/*!50001 DROP VIEW IF EXISTS `reporte_clientes_beneficiario_final_peps` */;
/*!50001 DROP TABLE IF EXISTS `reporte_clientes_beneficiario_final_peps` */;

/*!50001 CREATE TABLE  `reporte_clientes_beneficiario_final_peps`(
 `FECHA_RADICACION` date ,
 `LINEA_NEGOCIO` varchar(100) ,
 `CORREO` varchar(100) ,
 `CLIENTE_TIPO_DOCUMENTO_CODIGO` varchar(50) ,
 `CLIENTE_DOCUMENTO` varchar(100) ,
 `NOMBRE_CLIENTE` varchar(323) ,
 `FECHA_GESTION` datetime ,
 `CONCEPTO` varchar(255) ,
 `ANEXO_PPES` varchar(2) ,
 `ANEXO_ACCIONISTAS` varchar(2) ,
 `TIPO_FORMULARIO` varchar(2) 
)*/;

/*Table structure for table `reporte_clientes_capturados` */

DROP TABLE IF EXISTS `reporte_clientes_capturados`;

/*!50001 DROP VIEW IF EXISTS `reporte_clientes_capturados` */;
/*!50001 DROP TABLE IF EXISTS `reporte_clientes_capturados` */;

/*!50001 CREATE TABLE  `reporte_clientes_capturados`(
 `FECHA_RADICACION` datetime ,
 `USUARIO_ASISTEMYCA` varchar(80) ,
 `USUARIO_CAPTURA` varchar(80) ,
 `FECHA_CAPTURA` datetime ,
 `PROCESO` varchar(50) ,
 `NOMBRE_TOMADOR` varchar(323) ,
 `TIPO_PERSONA` varchar(3) ,
 `CLIENTE_ID` int(11) ,
 `IDENTIFICACION_TOMADOR` varchar(100) ,
 `FECHA_DILIGENCIAMIENTO` date ,
 `TIPO_SOLICITUD` varchar(20) ,
 `CLASE_VINCULACION` varchar(255) ,
 `CLASE_VINCULACION_OTRO` varchar(120) ,
 `TOMADOR_ASEGURADO` varchar(120) ,
 `TOMADOR_ASEGURADO_OTRO` varchar(120) ,
 `TOMADOR_BENEFICIARIO` varchar(120) ,
 `TOMADOR_BENEFICIARIO_OTRO` varchar(120) ,
 `ASEGURADO_BENEFICIARIO` varchar(120) ,
 `ASEGURADO_BENEFICIARIO_OTRO` varchar(120) ,
 `PRIMER_APELLIDO` varchar(80) ,
 `SEGUNDO_APELLIDO` varchar(80) ,
 `NOMBRES` varchar(161) ,
 `NOMBRE_O_RAZON_SOCIAL` varchar(230) ,
 `TIPO_DOCUMENTO` varchar(10) ,
 `COD_DOCUMENTO` int(11) ,
 `TIPO_SOCIEDAD` varchar(25) ,
 `LUGAR_EXPEDICION` varchar(120) ,
 `SEXO` varchar(1) ,
 `ESTADO_CIVIL` varchar(20) ,
 `FECHA_EXPEDICION_DOCUMENTO_PN` date ,
 `FECHA_NACIMIENTO_PN` varchar(10) ,
 `LUGAR_NACIMIENTO_PN` varchar(150) ,
 `NACIONALIDAD_1_PN` varchar(120) ,
 `NACIONALIDAD_2_PN` varchar(120) ,
 `DIRECCION_OFICINA_PRINCIPAL_RESIDENCIA` text ,
 `TIPO_EMPRESA` varchar(255) ,
 `CIIU_ACTIVIDAD_ECONOMICA` varchar(500) ,
 `CIIU_ACTIVIDAD_ECONOMICA_OTRA` varchar(255) ,
 `CIIU_COD` varchar(50) ,
 `ACTIVIDAD_ECONOMICA` varchar(500) ,
 `SECTOR` varchar(130) ,
 `BREVE_DESCRIPCION` varchar(255) ,
 `DEPARTAMENTO_OFICINA_RESIDENCIA` varchar(80) ,
 `CIUDAD_OFICINA_RESIDENCIA` varchar(80) ,
 `TELEFONO_OFICINA_RESIDENCIA` varchar(100) ,
 `CELULAR` bigint(20) ,
 `PAGINA_WEB` varchar(255) ,
 `CORREO_ELECTRONICO` text ,
 `SUCURSAL_DEPARATAMENTO` varchar(80) ,
 `SUCURSAL_CIUDAD` varchar(80) ,
 `SUCURSAL_DIRECCION` varchar(100) ,
 `SUCURSAL_TELEFONO` varchar(100) ,
 `SUCURSAL_FAX` varchar(255) ,
 `OCUPACION` varchar(130) ,
 `TIPO_ACTIVIDAD` varchar(130) ,
 `CARGO` varchar(130) ,
 `EMPRESA_DONDE_TRABAJA_PN` varchar(140) ,
 `DIRECCION_EMPRESA_PN` text ,
 `CIUDAD_EMPRESA_PN` varchar(80) ,
 `DEPARTAMENTO_EMPRESA_PN` varchar(80) ,
 `TELEFONO_EMPRESA_PN` varchar(100) ,
 `ACTIVIDAD_SEVUNDARIA_PN` varchar(500) ,
 `CIIU_SECUNDARIA_PN` varchar(50) ,
 `REPRESENTANTE_LEGAL_PRIMER_APELLIDO` varchar(255) ,
 `REPRESENTANTE_LEGAL_SEGUNDO_APELLIDO` varchar(255) ,
 `REPRESENTANTE_LEGAL_NOMBRES` varchar(255) ,
 `REPRESENTANTE_TIPO_DOCUMENTO` varchar(10) ,
 `REPRESENTANTE_COD_DOCUMENTO` int(11) ,
 `REPRESENTANTE_DOCUMENTO` int(11) ,
 `REPRESENTANTE_FECHA_EXPEDICION` date ,
 `REPRESENTANTE_LUGAR_EXPEDICION` varchar(255) ,
 `REPRESENTANTE_FECHA_NACIMIENTO` date ,
 `REPRESENTANTE_LUGAR_NACIMIENTO` varchar(40) ,
 `REPRESENTANTE_NACIONALIDAD_1` varchar(120) ,
 `REPRESENTANTE_NACIONALIDAD_2` varchar(120) ,
 `REPRESENTANTE_EMAIL` text ,
 `REPRESENTANTE_DIRECCION_RESIDENCIA` text ,
 `REPRESENTANTE_CIUDAD_RESIDENCIA` varchar(80) ,
 `REPRESENTANTE_DEPARTAMENTO_RESIDENCIA` varchar(80) ,
 `REPRESENTANTE_PAIS_RESIDENCIA` varchar(120) ,
 `REPRESENTANTE_TELEFONO_RESIDENCIA` varchar(100) ,
 `REPRESENTANTE_CELULAR_RESIDENCIA` varchar(50) ,
 `REPRESENTANTE_PERSONA_PUBLICA` varchar(2) ,
 `REPRESENTANTE_RECURSOS_PUBLICOS` varchar(2) ,
 `REPRESENTANTE_OBLIGACIONES_TRIBUTARIAS` varchar(2) ,
 `REPRESENTANTE_OBLIGACIONES_TRIBUTARIAS_CUAL` varchar(100) ,
 `PERSONA_PUBLICA_PN` varchar(2) ,
 `VINCULO_PERSONA_PUBLICA_PN` varchar(2) ,
 `RECURSOS_PUBLICOS_PN` varchar(2) ,
 `INGRESOS` bigint(100) ,
 `EGRESOS` bigint(100) ,
 `ACTIVOS` bigint(100) ,
 `PASIVOS` bigint(100) ,
 `PATRIMONIO` bigint(100) ,
 `OTROS_INGRESOS` bigint(100) ,
 `CONCEPTO_OTROS_INGRESOS` varchar(100) ,
 `DECLARACION_ORIGEN_FONDOS` varchar(130) ,
 `TRANSACCIONES_MONEDA_EXTRANJERA` varchar(2) ,
 `TRANSACCIONES_MONEDA_EXTRANJERA_CUAL` varchar(80) ,
 `TRANSACCIONES_MONEDA_EXTRANJERA_OTRAS` text ,
 `PRODUCTOS_EXTERIOR` varchar(2) ,
 `CUENTAS_MONEDA_EXTRANJERA` varchar(2) ,
 `RECLAMACIONES` varchar(2) ,
 `RECLAMACION_ANIO` int(11) ,
 `RECLAMACION_RAMO` varchar(40) ,
 `RECLAMACION_COMPANIA` varchar(40) ,
 `RECLAMACION_VALOR` bigint(100) ,
 `RECLAMACION_RESULTADO` int(11) ,
 `RECLAMACION_ANIO_2` int(11) ,
 `RECLAMACION_RAMO_2` varchar(40) ,
 `RECLAMACION_COMPANIA_2` varchar(40) ,
 `RECLAMACION_VALOR_2` bigint(100) ,
 `RECLAMACION_RESULTADO_2` int(11) ,
 `CIUDAD_DILIGENCIAMIENTO` varchar(80) ,
 `SUCURSAL` varchar(30) 
)*/;

/*Table structure for table `reporte_clientes_checklist_documentos` */

DROP TABLE IF EXISTS `reporte_clientes_checklist_documentos`;

/*!50001 DROP VIEW IF EXISTS `reporte_clientes_checklist_documentos` */;
/*!50001 DROP TABLE IF EXISTS `reporte_clientes_checklist_documentos` */;

/*!50001 CREATE TABLE  `reporte_clientes_checklist_documentos`(
 `FECHA_RADICACION` date ,
 `CORREO` varchar(100) ,
 `CLIENTE_TIPO_DOCUMENTO_CODIGO` varchar(10) ,
 `CLIENTE_DOCUMENTO` varchar(100) ,
 `NOMBRE_CLIENTE` varchar(323) ,
 `RADICACION_PROCESO` varchar(5) ,
 `SAA` varchar(2) ,
 `TIPO_FORMULARIO` varchar(2) ,
 `CLIENTE_ESTADO_PROCESO` varchar(50) ,
 `NUMERO_PLANILLA` varchar(255) ,
 `OBSERVACIONES` text 
)*/;

/*Table structure for table `reporte_clientes_completitud_verificacion` */

DROP TABLE IF EXISTS `reporte_clientes_completitud_verificacion`;

/*!50001 DROP VIEW IF EXISTS `reporte_clientes_completitud_verificacion` */;
/*!50001 DROP TABLE IF EXISTS `reporte_clientes_completitud_verificacion` */;

/*!50001 CREATE TABLE  `reporte_clientes_completitud_verificacion`(
 `FECHA_RADICACION` datetime ,
 `CORREO` varchar(100) ,
 `CLIENTE_TIPO_DOCUMENTO_CODIGO` varchar(10) ,
 `CLIENTE_DOCUMENTO` varchar(100) ,
 `NOMBRE_CLIENTE` varchar(323) ,
 `FECHA_COMPLETITUD` datetime ,
 `GESTION_CAMPOS_COMPLETADOS` text ,
 `FECHA_VERIFICACION` datetime ,
 `PREGUNTA_CAMPOS_COMPLETADOS` varchar(2) ,
 `GESTION_OBSERVACIONES` text 
)*/;

/*Table structure for table `reporte_clientes_pendientes` */

DROP TABLE IF EXISTS `reporte_clientes_pendientes`;

/*!50001 DROP VIEW IF EXISTS `reporte_clientes_pendientes` */;
/*!50001 DROP TABLE IF EXISTS `reporte_clientes_pendientes` */;

/*!50001 CREATE TABLE  `reporte_clientes_pendientes`(
 `id` int(11) ,
 `FECHA_RADICACION` datetime ,
 `fecha_diligenciamiento` date ,
 `CORREO` varchar(100) ,
 `FECHA_ENVIO_CORREO` datetime ,
 `CLIENTE_TIPO_DOCUMENTO_CODIGO` varchar(10) ,
 `CLIENTE_DOCUMENTO` varchar(100) ,
 `NOMBRE_CLIENTE` varchar(323) ,
 `CORREO_RADICACION` varchar(100) ,
 `FIRMA` varchar(2) ,
 `HUELLA` varchar(2) ,
 `ENTREVISTA` varchar(2) ,
 `ESTADO_PROCESO` varchar(50) ,
 `PROCESO_RADICACION` varchar(5) ,
 `DOCUMENTO_PENDIENTE_CODIGO` varchar(17) ,
 `OBSERVACION` text 
)*/;

/*Table structure for table `reporte_facturacion` */

DROP TABLE IF EXISTS `reporte_facturacion`;

/*!50001 DROP VIEW IF EXISTS `reporte_facturacion` */;
/*!50001 DROP TABLE IF EXISTS `reporte_facturacion` */;

/*!50001 CREATE TABLE  `reporte_facturacion`(
 `cliente_id` int(11) ,
 `FECHA_RADICACION` datetime ,
 `user_id` int(11) ,
 `USUARIO_ASISTEMYCA` varchar(80) ,
 `USUARIO_CAPTURA` varchar(80) ,
 `FECHA_CAPTURA` datetime ,
 `NUMERO_PLANILLA` varchar(255) ,
 `FECHA_DILIGENCIAMIENTO` date ,
 `USUARIO_SUSCRIPTOR` varchar(100) ,
 `SEPARADO` varchar(2) ,
 `TIPO_CLIENTE_CODIGO` varchar(10) ,
 `CLIENTE_DOCUMENTO` varchar(100) ,
 `NUMERO_RADICACION` varchar(70) ,
 `CANT_DOCUMENTOS` int(11) ,
 `NOMBRE_TOMADOR` varchar(323) ,
 `FORMULARIO_REPETIDO` varchar(2) ,
 `PROCESO_COMPLETITUD` datetime ,
 `PROCESO_VERIFICACION` datetime ,
 `ESTADO_TIPOLOGIA` varchar(255) ,
 `INTENTO_LLAMADA` int(11) ,
 `FORMA_RECEPCION` varchar(7) ,
 `RADICACION_PROCESO` varchar(5) ,
 `TIPO_FORMULARIO` varchar(5) ,
 `TIPO_LLAMADA` varchar(12) ,
 `ESTADO_PROCESO` varchar(50) ,
 `FECHA_PROCESO_CAPTURA` datetime ,
 `FORMULARIO_CAPTURADO` varchar(2) ,
 `OBSERVACION` text 
)*/;

/*View structure for view clientes_sarlaft */

/*!50001 DROP TABLE IF EXISTS `clientes_sarlaft` */;
/*!50001 DROP VIEW IF EXISTS `clientes_sarlaft` */;

/*!50001 CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `clientes_sarlaft` AS select `clientes`.`documento` AS `documento`,`clientes`.`id` AS `cliente_id`,`clientes`.`tipo_documento` AS `tipo_documento`,`td`.`tipo_persona` AS `TIPO_PERSONA`,`cn`.`id` AS `formulario_id`,`zr`.`fecha_diligenciamiento` AS `fecha_diligenciamiento`,concat_ws('-',concat(date_format(cast(`zr`.`created` as date),'%Y%m%d'),`zr`.`cliente_id`),`zr`.`consecutivo`) AS `numero_radicado`,`zr`.`numero_planilla` AS `numero_planilla`,if((isnull(`cn`.`primer_apellido`) and isnull(`cn`.`segundo_apellido`) and isnull(`cn`.`primer_nombre`) and isnull(`cn`.`segundo_nombre`)),'VACIO',ucase(trim(concat_ws(' ',`cn`.`primer_apellido`,`cn`.`segundo_apellido`,`cn`.`primer_nombre`,`cn`.`segundo_nombre`)))) AS `NOMBRE_CLIENTE`,`zepcs`.`ESTADO_PROCESO_ID` AS `estado_formulario_id`,`estados_sarlaft`.`desc_type` AS `ESTADO_FORM`,`zepcs`.`PROCESO_ACTIVO` AS `PROCESO_ACTIVO` from (((((`clientes` join `tipos_documentos` `td` on((`td`.`id` = `clientes`.`tipo_documento`))) join `zr_radicacion` `zr` on(((`zr`.`cliente_id` = `clientes`.`id`) and (`zr`.`created` = (select max(`zr_radicacion`.`created`) from `zr_radicacion` where ((`zr_radicacion`.`cliente_id` = `zr`.`cliente_id`) and (`zr_radicacion`.`formulario_sarlaft` = 1) and (`zr_radicacion`.`radicacion_proceso` = 'LEGAL') and (`zr_radicacion`.`fecha_diligenciamiento` = (select max(`zr2`.`fecha_diligenciamiento`) from `zr_radicacion` `zr2` where (`zr2`.`cliente_id` = `zr`.`cliente_id`))))))))) join `zr_estado_proceso_clientes_sarlaft` `zepcs` on(((`zepcs`.`PROCESO_CLIENTE_ID` = `zr`.`cliente_id`) and (`zepcs`.`FECHA_PROCESO` = (select max(`zr_estado_proceso_clientes_sarlaft`.`FECHA_PROCESO`) from `zr_estado_proceso_clientes_sarlaft` where (`zr_estado_proceso_clientes_sarlaft`.`PROCESO_CLIENTE_ID` = `zepcs`.`PROCESO_CLIENTE_ID`)))))) join `estados_sarlaft` on((`estados_sarlaft`.`id` = `zepcs`.`ESTADO_PROCESO_ID`))) left join `cliente_sarlaft_natural` `cn` on((`cn`.`cliente` = `clientes`.`id`))) where (`td`.`tipo_persona` = 'NAT') group by `clientes`.`documento` union all select `clientes`.`documento` AS `documento`,`clientes`.`id` AS `id`,`clientes`.`tipo_documento` AS `tipo_documento`,`td`.`tipo_persona` AS `TIPO_PERSONA`,`cj`.`id` AS `id`,`zr`.`fecha_diligenciamiento` AS `fecha_diligenciamiento`,concat_ws('-',concat(date_format(cast(`zr`.`created` as date),'%Y%m%d'),`zr`.`cliente_id`),`zr`.`consecutivo`) AS `Name_exp_7`,`zr`.`numero_planilla` AS `numero_planilla`,ucase(ifnull(`cj`.`razon_social`,'VACIO')) AS `NOMBRE_CLIENTE`,`zepcs`.`ESTADO_PROCESO_ID` AS `ESTADO_PROCESO_ID`,`estados_sarlaft`.`desc_type` AS `desc_type`,`zepcs`.`PROCESO_ACTIVO` AS `PROCESO_ACTIVO` from (((((`clientes` join `tipos_documentos` `td` on((`td`.`id` = `clientes`.`tipo_documento`))) join `zr_radicacion` `zr` on(((`zr`.`cliente_id` = `clientes`.`id`) and (`zr`.`formulario_sarlaft` = 1) and (`zr`.`radicacion_proceso` = 'LEGAL') and (`zr`.`created` = (select max(`zr_radicacion`.`created`) from `zr_radicacion` where ((`zr_radicacion`.`cliente_id` = `zr`.`cliente_id`) and (`zr_radicacion`.`formulario_sarlaft` = 1) and (`zr_radicacion`.`radicacion_proceso` = 'LEGAL') and (`zr_radicacion`.`fecha_diligenciamiento` = (select max(`zr2`.`fecha_diligenciamiento`) from `zr_radicacion` `zr2` where (`zr2`.`cliente_id` = `zr`.`cliente_id`))))))))) join `zr_estado_proceso_clientes_sarlaft` `zepcs` on(((`zepcs`.`PROCESO_CLIENTE_ID` = `zr`.`cliente_id`) and (`zepcs`.`FECHA_PROCESO` = (select max(`zr_estado_proceso_clientes_sarlaft`.`FECHA_PROCESO`) from `zr_estado_proceso_clientes_sarlaft` where (`zr_estado_proceso_clientes_sarlaft`.`PROCESO_CLIENTE_ID` = `zepcs`.`PROCESO_CLIENTE_ID`)))))) join `estados_sarlaft` on((`estados_sarlaft`.`id` = `zepcs`.`ESTADO_PROCESO_ID`))) left join `cliente_sarlaft_juridico` `cj` on((`cj`.`cliente` = `clientes`.`id`))) where (`td`.`tipo_persona` = 'JUR') group by `clientes`.`documento` */;

/*View structure for view reporte_cargue_clientes_juridicos */

/*!50001 DROP TABLE IF EXISTS `reporte_cargue_clientes_juridicos` */;
/*!50001 DROP VIEW IF EXISTS `reporte_cargue_clientes_juridicos` */;

/*!50001 CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `reporte_cargue_clientes_juridicos` AS select `clientes`.`id` AS `CLIENTE_ID`,`zr`.`created` AS `FECHA_RADICACION`,ucase(`users`.`nombre`) AS `USUARIO_RADICAICON`,`gcc`.`FECHA_GESTION` AS `FECHA_CAPTURA`,(select max(`gcv`.`FECHA_GESTION`) from `gestion_clientes_completitud_verificacion` `gcv` where ((`gcv`.`GESTION_CLIENTE_ID` = `zr`.`cliente_id`) and (`gcv`.`GESTION_FECHA_DILIGENCIAMIENTO` = `zr`.`fecha_diligenciamiento`) and (`gcv`.`GESTION_PROCESO_ID` = 5) and (`gcv`.`FECHA_GESTION` > `gcc`.`FECHA_GESTION`))) AS `FECHA_ACTUALIZACION`,`zr`.`fecha_diligenciamiento` AS `FECHA_DILIGENCIAMIENTO`,NULL AS `COD_ASEG`,`clientes`.`tipo_documento` AS `TIPO_DOCUMENTO`,ucase(trim(`cj`.`razon_social`)) AS `NOMBRE_TOMADOR`,`clientes`.`documento` AS `IDENTIFICACION_TOMADOR`,ucase(`cj`.`rep_legal_primer_apellido`) AS `REPRESENTANTE_LEGAL_PRIMER_APELLIDO`,ucase(`cj`.`rep_legal_segundo_apellido`) AS `REPRESENTANTE_LEGAL_SEGUNDO_APELLIDO`,ucase(`cj`.`rep_legal_nombres`) AS `REPRESENTANTE_LEGAL_NOMBRES`,`cj`.`rep_legal_tipo_documento` AS `REPRESENTANTE_TIPO_DOCUMENTO`,`cj`.`rep_legal_documento` AS `REPRESENTANTE_DOCUMENTO`,`cj`.`rep_legal_direccion_residencia` AS `REPRESENTANTE_DIRECCION_RESIDENCIA`,(select ucase(`ciudades`.`nombre_ciudad`) from `ciudades` where (`ciudades`.`id` = `cj`.`rep_legal_ciudad_residencia`)) AS `REPRESENTANTE_CIUDAD_RESIDENCIA`,`cj`.`rep_legal_telefono_residencia` AS `REPRESENTANTE_TELEFONO_RESIDENCIA`,`cj`.`sucursal_direccion` AS `SUCURSAL_DIRECCION`,(select ucase(`ciudades`.`nombre_ciudad`) from `ciudades` where (`ciudades`.`id` = `cj`.`sucursal_ciudad`)) AS `SUCURSAL_CIUDAD`,`cj`.`sucursal_telefono` AS `SUCURSAL_TELEFONO`,`cj`.`sucursal_fax` AS `SUCURSAL_FAX`,`cj`.`rep_legal_celular_residencia` AS `CELULAR`,`cj`.`rep_legal_telefono_residencia` AS `TELEFONO`,`cj`.`ofi_principal_tipo_empresa` AS `TIPO_EMPRESA`,`cj`.`ofi_principal_ciiu_cod` AS `CIIU_COD`,(select ucase(`tap`.`nombre_actividad_principal`) from `tipos_actividades_principales` `tap` where (`tap`.`id` = convert(`cj`.`ofi_principal_ciiu_cod` using utf8))) AS `ACTIVIDAD_ECONOMICA`,`cj`.`ingresos` AS `INGRESOS`,`cj`.`egresos` AS `EGRESOS`,`cj`.`activos` AS `ACTIVOS`,`cj`.`pasivos` AS `PASIVOS`,`cj`.`patrimonio` AS `PATRIMONIO`,`cj`.`otros_ingresos` AS `OTROS_INGRESOS`,`cj`.`desc_otros_ingresos` AS `CONCEPTO_OTROS_INGRESOS`,if((`cj`.`operaciones_moneda_extranjera` = 1),'SI','NO') AS `TRANSACCIONES_MONEDA_EXTRANJERA`,(select `tipos_operaciones_moneda_extranjera`.`desc_operacion` from `tipos_operaciones_moneda_extranjera` where (`tipos_operaciones_moneda_extranjera`.`id` = `cj`.`tipo_operaciones_moneda_extranjera`)) AS `TRANSACCIONES_MONEDA_EXTRANJERA_CUAL`,`cj`.`tipo_operaciones_moneda_extranjera_otro` AS `TRANSACCIONES_MONEDA_EXTRANJERA_OTRAS`,`cj`.`declaracion_origen_fondos` AS `DECLARACION_ORIGEN_FONDOS`,(select ucase(`ciudades`.`nombre_ciudad`) from `ciudades` where (`ciudades`.`id` = `cj`.`ciudad_diligenciamiento`)) AS `CIUDAD_DILIGENCIAMIENTO`,`cj`.`sucursal` AS `SUCURSAL` from (((((`clientes` join `tipos_documentos` `td` on((`td`.`id` = `clientes`.`tipo_documento`))) join `zr_radicacion` `zr` on(((`zr`.`cliente_id` = `clientes`.`id`) and (`zr`.`created` = (select max(`zr2`.`created`) from `zr_radicacion` `zr2` where ((`zr2`.`cliente_id` = `zr`.`cliente_id`) and (`zr2`.`fecha_diligenciamiento` = (select max(`zr3`.`fecha_diligenciamiento`) from `zr_radicacion` `zr3` where ((`zr3`.`cliente_id` = `zr2`.`cliente_id`) and (`zr3`.`formulario_sarlaft` = 1) and (`zr3`.`repetido` = 0) and (`zr3`.`radicacion_proceso` = 'LEGAL')))))))))) join `cliente_sarlaft_juridico` `cj` on((`cj`.`cliente` = `zr`.`cliente_id`))) join `users` on((`users`.`id` = `zr`.`funcionario_id`))) join `gestion_clientes_captura` `gcc` on(((`zr`.`cliente_id` = `gcc`.`GESTION_CLIENTE_ID`) and (`gcc`.`FECHA_GESTION` = (select max(`gestion_clientes_captura`.`FECHA_GESTION`) from `gestion_clientes_captura` where ((`gestion_clientes_captura`.`GESTION_CLIENTE_ID` = `gcc`.`GESTION_CLIENTE_ID`) and (`gcc`.`GESTION_FECHA_DILIGENCIAMIENTO` = `zr`.`fecha_diligenciamiento`))))))) where (`cj`.`razon_social` is not null) */;

/*View structure for view reporte_cargue_clientes_naturales */

/*!50001 DROP TABLE IF EXISTS `reporte_cargue_clientes_naturales` */;
/*!50001 DROP VIEW IF EXISTS `reporte_cargue_clientes_naturales` */;

/*!50001 CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `reporte_cargue_clientes_naturales` AS select distinct `clientes`.`id` AS `CLIENTE_ID`,`zr`.`created` AS `FECHA_RADICACION`,ucase(`users`.`nombre`) AS `USUARIO_RADICAICON`,`gcc`.`FECHA_GESTION` AS `FECHA_CAPTURA`,(select max(`gcv`.`FECHA_GESTION`) from `gestion_clientes_completitud_verificacion` `gcv` where ((`gcv`.`GESTION_CLIENTE_ID` = `zr`.`cliente_id`) and (`gcv`.`GESTION_FECHA_DILIGENCIAMIENTO` = `zr`.`fecha_diligenciamiento`) and (`gcv`.`GESTION_PROCESO_ID` = 5) and (`gcv`.`FECHA_GESTION` > `gcc`.`FECHA_GESTION`))) AS `FECHA_VERIFICACION`,`zr`.`fecha_diligenciamiento` AS `FECHA_DILIGENCIAMIENTO`,NULL AS `COD_ASEG`,ucase(trim(`cn`.`primer_apellido`)) AS `PRIMER_APELLIDO`,ucase(trim(`cn`.`segundo_apellido`)) AS `SEGUNDO_APELLIDO`,ucase(trim(concat_ws(' ',`cn`.`primer_nombre`,`cn`.`segundo_nombre`))) AS `NOMBRES`,`clientes`.`tipo_documento` AS `TIPO_DOCUMENTO`,`clientes`.`documento` AS `NUMERO_DOCUMENTO`,ucase(`cn`.`lugar_nacimiento`) AS `LUGAR_NACIMIENTO`,ucase(`cn`.`fecha_nacimiento`) AS `FECHA_NACIMIENTO`,ucase(`cn`.`ocupacion`) AS `OCUPACION_1`,NULL AS `OCUPACION_2`,ifnull(`cn`.`ciiu_cod`,`cn`.`actividad_eco_principal`) AS `CIIU_COD`,(select ucase(`tipos_actividades`.`nombre_tipo_actividad`) from `tipos_actividades` where (`tipos_actividades`.`id` = `cn`.`tipo_actividad`)) AS `TIPO_ACTIVIDAD`,ucase(`cn`.`empresa_donde_trabaja`) AS `EMPRESA_DONDE_TRABAJA`,ucase(`cn`.`cargo`) AS `CARGO`,(select ucase(`ciudades`.`nombre_ciudad`) from `ciudades` where (`ciudades`.`id` = `cn`.`ciudad_empresa`)) AS `CIUDAD_EMPRESA`,`cn`.`direccion_empresa` AS `DIRECCION_EMPRESA`,`cn`.`telefono_empresa` AS `TELEFONO_EMPRESA`,`cn`.`direccion_residencia` AS `DIRECCION_RESIDENCIA`,(select ucase(`ciudades`.`nombre_ciudad`) from `ciudades` where (`ciudades`.`id` = `cn`.`ciudad_residencia`)) AS `CIUDAD_RESIDENCIA`,(select ucase(`departamentos`.`nombre_departamento`) from `departamentos` where (`departamentos`.`id` = `cn`.`departamento_residencia`)) AS `DEPARTAMENTO_RESIDENCIA`,`cn`.`telefono` AS `TELEFONO_RESIDENCIA`,`cn`.`celular` AS `CELULAR`,`cn`.`ingresos` AS `INGRESOS`,`cn`.`egresos` AS `EGRESOS`,`cn`.`activos` AS `ACTIVOS`,`cn`.`pasivos` AS `PASIVOS`,`cn`.`patrimonio` AS `PATRIMONIO`,`cn`.`otros_ingresos` AS `OTROS_INGRESOS`,`cn`.`desc_otros_ingresos` AS `CONCEPTO_OTROS_INGRESOS`,if((`cn`.`operaciones_moneda_extranjera` = 1),'SI','NO') AS `TRANSACCIONES_MONEDA_EXTRANJERA`,(select ucase(`tipos_operaciones_moneda_extranjera`.`desc_operacion`) from `tipos_operaciones_moneda_extranjera` where (`tipos_operaciones_moneda_extranjera`.`id` = `cn`.`tipo_operaciones_moneda_extranjera`)) AS `TRANSACCIONES_MONEDA_EXTRANJERA_CUAL`,`cn`.`desc_operacion_mon_extr` AS `TRANSACCIONES_MONEDA_EXTRANJERA_OTRAS`,`cn`.`declaracion_origen_fondos` AS `DECLARACION_ORIGEN_FONDOS`,(select ucase(`ciudades`.`nombre_ciudad`) from `ciudades` where (`ciudades`.`id` = `cn`.`ciudad_diligenciamiento`)) AS `CIUDAD_DILIGENCIAMIENTO`,`cn`.`sucursal` AS `SUCURSAL` from (((((`clientes` join `tipos_documentos` `td` on((`td`.`id` = `clientes`.`tipo_documento`))) join `zr_radicacion` `zr` on(((`zr`.`cliente_id` = `clientes`.`id`) and (`zr`.`created` = (select max(`zr2`.`created`) from `zr_radicacion` `zr2` where ((`zr2`.`cliente_id` = `zr`.`cliente_id`) and (`zr2`.`fecha_diligenciamiento` = (select max(`zr3`.`fecha_diligenciamiento`) from `zr_radicacion` `zr3` where ((`zr3`.`cliente_id` = `zr2`.`cliente_id`) and (`zr3`.`formulario_sarlaft` = 1) and (`zr3`.`repetido` = 0) and (`zr3`.`radicacion_proceso` = 'LEGAL')))))))))) join `cliente_sarlaft_natural` `cn` on((`cn`.`cliente` = `zr`.`cliente_id`))) join `users` on((`users`.`id` = `zr`.`funcionario_id`))) join `gestion_clientes_captura` `gcc` on(((`zr`.`cliente_id` = `gcc`.`GESTION_CLIENTE_ID`) and (`gcc`.`FECHA_GESTION` = (select max(`gestion_clientes_captura`.`FECHA_GESTION`) from `gestion_clientes_captura` where ((`gestion_clientes_captura`.`GESTION_CLIENTE_ID` = `gcc`.`GESTION_CLIENTE_ID`) and (`gcc`.`GESTION_FECHA_DILIGENCIAMIENTO` = `zr`.`fecha_diligenciamiento`))))))) where ((`cn`.`primer_apellido` is not null) or (`cn`.`segundo_apellido` is not null) or (`cn`.`primer_nombre` is not null) or (`cn`.`segundo_nombre` is not null)) group by `clientes`.`documento` */;

/*View structure for view reporte_clientes_beneficiario_final_peps */

/*!50001 DROP TABLE IF EXISTS `reporte_clientes_beneficiario_final_peps` */;
/*!50001 DROP VIEW IF EXISTS `reporte_clientes_beneficiario_final_peps` */;

/*!50001 CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `reporte_clientes_beneficiario_final_peps` AS select cast(`zr`.`created` as date) AS `FECHA_RADICACION`,`linea_negocio`.`NOMBRE` AS `LINEA_NEGOCIO`,`zr`.`correo_radicacion` AS `CORREO`,`tipos_documentos`.`descripcion` AS `CLIENTE_TIPO_DOCUMENTO_CODIGO`,`clientes`.`documento` AS `CLIENTE_DOCUMENTO`,if((isnull(`cn`.`primer_apellido`) and isnull(`cn`.`segundo_apellido`) and isnull(`cn`.`primer_nombre`) and isnull(`cn`.`segundo_nombre`)),'VACIO',ucase(trim(concat_ws(' ',`cn`.`primer_apellido`,`cn`.`segundo_apellido`,`cn`.`primer_nombre`,`cn`.`segundo_nombre`)))) AS `NOMBRE_CLIENTE`,`gcv`.`FECHA_GESTION` AS `FECHA_GESTION`,`zr_tipologias`.`tipologia` AS `CONCEPTO`,if((`cn`.`anexo_preguntas_ppes` = 1),'SI','NO') AS `ANEXO_PPES`,'NO' AS `ANEXO_ACCIONISTAS`,if((`zr`.`formulario` = 'Nuevo'),'SI','NO') AS `TIPO_FORMULARIO` from ((((((`clientes` join `tipos_documentos` on((`tipos_documentos`.`id` = `clientes`.`tipo_documento`))) join `cliente_sarlaft_natural` `cn` on((`cn`.`cliente` = `clientes`.`id`))) join `zr_radicacion` `zr` on(((`zr`.`cliente_id` = `cn`.`cliente`) and (`zr`.`created` = (select max(`zr_radicacion`.`created`) from `zr_radicacion` where ((`zr_radicacion`.`cliente_id` = `zr`.`cliente_id`) and (`zr_radicacion`.`formulario_sarlaft` = 1) and (`zr_radicacion`.`radicacion_proceso` = 'LEGAL') and (`zr_radicacion`.`fecha_diligenciamiento` = (select max(`zr2`.`fecha_diligenciamiento`) from `zr_radicacion` `zr2` where (`zr2`.`cliente_id` = `zr`.`cliente_id`))))))))) join `linea_negocio` on((`linea_negocio`.`ID_LINEA` = `zr`.`linea_negocio_id`))) left join `gestion_clientes_completitud_verificacion` `gcv` on(((`gcv`.`GESTION_CLIENTE_ID` = `clientes`.`id`) and (`gcv`.`GESTION_FECHA_DILIGENCIAMIENTO` = `zr`.`fecha_diligenciamiento`)))) left join `zr_tipologias` on((`zr_tipologias`.`id` = `gcv`.`GESTION_ESTADO_TIPOLOGIA_ID`))) where (`cn`.`anexo_preguntas_ppes` = 1) union all select cast(`zr`.`created` as date) AS `FECHA_RADICACION`,`linea_negocio`.`NOMBRE` AS `LINEA_NEGOCIO`,`zr`.`correo_radicacion` AS `CORREO`,`tipos_documentos`.`descripcion` AS `CLIENTE_TIPO_DOCUMENTO_CODIGO`,`clientes`.`documento` AS `CLIENTE_DOCUMENTO`,`cj`.`razon_social` AS `NOMBRE_CLIENTE`,`gcv`.`FECHA_GESTION` AS `FECHA_GESTION`,`zr_tipologias`.`tipologia` AS `CONCEPTO`,if((`cj`.`anexo_preguntas_ppes` = 1),'SI','NO') AS `ANEXO_PPES`,if((`cj`.`anexo_accionistas` = 1),'SI','NO') AS `ANEXO_ACCIONISTAS`,if((`zr`.`formulario` = 'Nuevo'),'SI','NO') AS `FORMULARIO_NUEVO` from ((((((`clientes` join `tipos_documentos` on((`tipos_documentos`.`id` = `clientes`.`tipo_documento`))) join `cliente_sarlaft_juridico` `cj` on((`cj`.`cliente` = `clientes`.`id`))) join `zr_radicacion` `zr` on(((`zr`.`cliente_id` = `cj`.`cliente`) and (`zr`.`created` = (select max(`zr_radicacion`.`created`) from `zr_radicacion` where ((`zr_radicacion`.`cliente_id` = `zr`.`cliente_id`) and (`zr_radicacion`.`formulario_sarlaft` = 1) and (`zr_radicacion`.`radicacion_proceso` = 'LEGAL') and (`zr_radicacion`.`fecha_diligenciamiento` = (select max(`zr2`.`fecha_diligenciamiento`) from `zr_radicacion` `zr2` where (`zr2`.`cliente_id` = `zr`.`cliente_id`))))))))) join `linea_negocio` on((`linea_negocio`.`ID_LINEA` = `zr`.`linea_negocio_id`))) left join `gestion_clientes_completitud_verificacion` `gcv` on(((`gcv`.`GESTION_CLIENTE_ID` = `clientes`.`id`) and (`gcv`.`GESTION_FECHA_DILIGENCIAMIENTO` = `zr`.`fecha_diligenciamiento`)))) left join `zr_tipologias` on((`zr_tipologias`.`id` = `gcv`.`GESTION_ESTADO_TIPOLOGIA_ID`))) where ((`cj`.`anexo_preguntas_ppes` = 1) or (`cj`.`anexo_accionistas` = 1)) */;

/*View structure for view reporte_clientes_capturados */

/*!50001 DROP TABLE IF EXISTS `reporte_clientes_capturados` */;
/*!50001 DROP VIEW IF EXISTS `reporte_clientes_capturados` */;

/*!50001 CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `reporte_clientes_capturados` AS select `zr`.`created` AS `FECHA_RADICACION`,`users`.`nombre` AS `USUARIO_ASISTEMYCA`,(select ucase(`users`.`nombre`) from `users` where (`users`.`id` = `gcc`.`GESTION_USUARIO_ID`)) AS `USUARIO_CAPTURA`,`gcc`.`FECHA_GESTION` AS `FECHA_CAPTURA`,`es`.`desc_type` AS `PROCESO`,ucase(trim(concat_ws(' ',`cn`.`primer_apellido`,`cn`.`segundo_apellido`,`cn`.`primer_nombre`,`cn`.`segundo_nombre`))) AS `NOMBRE_TOMADOR`,`td`.`tipo_persona` AS `TIPO_PERSONA`,`clientes`.`id` AS `CLIENTE_ID`,`clientes`.`documento` AS `IDENTIFICACION_TOMADOR`,if((`zr`.`fecha_diligenciamiento` = '0000-00-00'),NULL,`zr`.`fecha_diligenciamiento`) AS `FECHA_DILIGENCIAMIENTO`,`cn`.`tipo_solicitud` AS `TIPO_SOLICITUD`,`cn`.`clase_vinculacion` AS `CLASE_VINCULACION`,`cn`.`clase_vinculacion_otro` AS `CLASE_VINCULACION_OTRO`,`cn`.`relacion_tom_asegurado` AS `TOMADOR_ASEGURADO`,`cn`.`relacion_tom_asegurado_otra` AS `TOMADOR_ASEGURADO_OTRO`,`cn`.`relacion_tom_beneficiario` AS `TOMADOR_BENEFICIARIO`,`cn`.`relacion_tom_beneficiario_otra` AS `TOMADOR_BENEFICIARIO_OTRO`,`cn`.`relacion_aseg_beneficiario` AS `ASEGURADO_BENEFICIARIO`,`cn`.`relacion_aseg_beneficiario_otra` AS `ASEGURADO_BENEFICIARIO_OTRO`,ucase(`cn`.`primer_apellido`) AS `PRIMER_APELLIDO`,ucase(`cn`.`segundo_apellido`) AS `SEGUNDO_APELLIDO`,ucase(trim(concat_ws(' ',`cn`.`primer_nombre`,`cn`.`segundo_nombre`))) AS `NOMBRES`,NULL AS `NOMBRE_O_RAZON_SOCIAL`,`td`.`codigo` AS `TIPO_DOCUMENTO`,`td`.`id` AS `COD_DOCUMENTO`,NULL AS `TIPO_SOCIEDAD`,`cn`.`lugar_expedicion_documento` AS `LUGAR_EXPEDICION`,`cn`.`sexo` AS `SEXO`,(select ucase(`estados_civiles`.`desc_estado_civil`) from `estados_civiles` where (`estados_civiles`.`id` = `cn`.`estado_civil`)) AS `ESTADO_CIVIL`,`cn`.`fecha_expedicion_documento` AS `FECHA_EXPEDICION_DOCUMENTO_PN`,ucase(`cn`.`fecha_nacimiento`) AS `FECHA_NACIMIENTO_PN`,ucase(`cn`.`lugar_nacimiento`) AS `LUGAR_NACIMIENTO_PN`,(select ucase(`paises`.`nombre_pais`) from `paises` where (`paises`.`id` = `cn`.`nacionalidad_1`)) AS `NACIONALIDAD_1_PN`,(select ucase(`paises`.`nombre_pais`) from `paises` where (`paises`.`id` = `cn`.`nacionalidad_2`)) AS `NACIONALIDAD_2_PN`,`cn`.`direccion_residencia` AS `DIRECCION_OFICINA_PRINCIPAL_RESIDENCIA`,NULL AS `TIPO_EMPRESA`,(select `tipos_actividades_principales`.`nombre_actividad_principal` from `tipos_actividades_principales` where (`tipos_actividades_principales`.`id` = convert(ifnull(`cn`.`ciiu_cod`,`cn`.`actividad_eco_principal`) using utf8))) AS `CIIU_ACTIVIDAD_ECONOMICA`,ucase(`cn`.`actividad_eco_principal_otra`) AS `CIIU_ACTIVIDAD_ECONOMICA_OTRA`,`cn`.`ciiu_cod` AS `CIIU_COD`,(select ucase(`tap`.`nombre_actividad_principal`) from `tipos_actividades_principales` `tap` where (`tap`.`id` = convert(`cn`.`ciiu_cod` using utf8))) AS `ACTIVIDAD_ECONOMICA`,(select ucase(`sector`.`desc_sector`) from `sector` where (`sector`.`id` = `cn`.`sector`)) AS `SECTOR`,NULL AS `BREVE_DESCRIPCION`,(select ucase(`departamentos`.`nombre_departamento`) from `departamentos` where (`departamentos`.`id` = `cn`.`departamento_residencia`)) AS `DEPARTAMENTO_OFICINA_RESIDENCIA`,(select ucase(`ciudades`.`nombre_ciudad`) from `ciudades` where (`ciudades`.`id` = `cn`.`ciudad_residencia`)) AS `CIUDAD_OFICINA_RESIDENCIA`,`cn`.`telefono` AS `TELEFONO_OFICINA_RESIDENCIA`,`cn`.`celular` AS `CELULAR`,NULL AS `PAGINA_WEB`,`cn`.`correo_electronico` AS `CORREO_ELECTRONICO`,NULL AS `SUCURSAL_DEPARATAMENTO`,NULL AS `SUCURSAL_CIUDAD`,NULL AS `SUCURSAL_DIRECCION`,NULL AS `SUCURSAL_TELEFONO`,NULL AS `SUCURSAL_FAX`,ucase(`cn`.`ocupacion`) AS `OCUPACION`,(select ucase(`tipos_actividades`.`nombre_tipo_actividad`) from `tipos_actividades` where (`tipos_actividades`.`id` = `cn`.`tipo_actividad`)) AS `TIPO_ACTIVIDAD`,ucase(`cn`.`cargo`) AS `CARGO`,ucase(`cn`.`empresa_donde_trabaja`) AS `EMPRESA_DONDE_TRABAJA_PN`,`cn`.`direccion_empresa` AS `DIRECCION_EMPRESA_PN`,(select ucase(`ciudades`.`nombre_ciudad`) from `ciudades` where (`ciudades`.`id` = `cn`.`ciudad_empresa`)) AS `CIUDAD_EMPRESA_PN`,(select ucase(`departamentos`.`nombre_departamento`) from `departamentos` where (`departamentos`.`id` = `cn`.`departamento_empresa`)) AS `DEPARTAMENTO_EMPRESA_PN`,`cn`.`telefono_empresa` AS `TELEFONO_EMPRESA_PN`,(select `tipos_actividades_principales`.`nombre_actividad_principal` from `tipos_actividades_principales` where (`tipos_actividades_principales`.`id` = `cn`.`actividad_secundaria`)) AS `ACTIVIDAD_SEVUNDARIA_PN`,`cn`.`ciiu_secundario` AS `CIIU_SECUNDARIA_PN`,NULL AS `REPRESENTANTE_LEGAL_PRIMER_APELLIDO`,NULL AS `REPRESENTANTE_LEGAL_SEGUNDO_APELLIDO`,NULL AS `REPRESENTANTE_LEGAL_NOMBRES`,NULL AS `REPRESENTANTE_TIPO_DOCUMENTO`,NULL AS `REPRESENTANTE_COD_DOCUMENTO`,NULL AS `REPRESENTANTE_DOCUMENTO`,NULL AS `REPRESENTANTE_FECHA_EXPEDICION`,NULL AS `REPRESENTANTE_LUGAR_EXPEDICION`,NULL AS `REPRESENTANTE_FECHA_NACIMIENTO`,NULL AS `REPRESENTANTE_LUGAR_NACIMIENTO`,NULL AS `REPRESENTANTE_NACIONALIDAD_1`,NULL AS `REPRESENTANTE_NACIONALIDAD_2`,NULL AS `REPRESENTANTE_EMAIL`,NULL AS `REPRESENTANTE_DIRECCION_RESIDENCIA`,NULL AS `REPRESENTANTE_CIUDAD_RESIDENCIA`,NULL AS `REPRESENTANTE_DEPARTAMENTO_RESIDENCIA`,NULL AS `REPRESENTANTE_PAIS_RESIDENCIA`,NULL AS `REPRESENTANTE_TELEFONO_RESIDENCIA`,NULL AS `REPRESENTANTE_CELULAR_RESIDENCIA`,NULL AS `REPRESENTANTE_PERSONA_PUBLICA`,NULL AS `REPRESENTANTE_RECURSOS_PUBLICOS`,NULL AS `REPRESENTANTE_OBLIGACIONES_TRIBUTARIAS`,NULL AS `REPRESENTANTE_OBLIGACIONES_TRIBUTARIAS_CUAL`,if((`cn`.`persona_publica` = 'SI'),'SI','NO') AS `PERSONA_PUBLICA_PN`,if((`cn`.`vinculo_persona_publica` = 'SI'),'SI','NO') AS `VINCULO_PERSONA_PUBLICA_PN`,if((`cn`.`productos_publicos` = 'SI'),'SI','NO') AS `RECURSOS_PUBLICOS_PN`,`cn`.`ingresos` AS `INGRESOS`,`cn`.`egresos` AS `EGRESOS`,`cn`.`activos` AS `ACTIVOS`,`cn`.`pasivos` AS `PASIVOS`,`cn`.`patrimonio` AS `PATRIMONIO`,`cn`.`otros_ingresos` AS `OTROS_INGRESOS`,`cn`.`desc_otros_ingresos` AS `CONCEPTO_OTROS_INGRESOS`,`cn`.`declaracion_origen_fondos` AS `DECLARACION_ORIGEN_FONDOS`,if((`cn`.`operaciones_moneda_extranjera` = 'SI'),'SI','NO') AS `TRANSACCIONES_MONEDA_EXTRANJERA`,(select ucase(`tipos_operaciones_moneda_extranjera`.`desc_operacion`) from `tipos_operaciones_moneda_extranjera` where (`tipos_operaciones_moneda_extranjera`.`id` = `cn`.`tipo_operaciones_moneda_extranjera`)) AS `TRANSACCIONES_MONEDA_EXTRANJERA_CUAL`,`cn`.`desc_operacion_mon_extr` AS `TRANSACCIONES_MONEDA_EXTRANJERA_OTRAS`,if((`cn`.`productos_exterior` = 'SI'),'SI','NO') AS `PRODUCTOS_EXTERIOR`,if((`cn`.`cuentas_moneda_exterior` = 'SI'),'SI','NO') AS `CUENTAS_MONEDA_EXTRANJERA`,if((`cn`.`reclamaciones` = 1),'SI','NO') AS `RECLAMACIONES`,`cn`.`reclamacion_anio` AS `RECLAMACION_ANIO`,`cn`.`reclamacion_ramo` AS `RECLAMACION_RAMO`,`cn`.`reclamacion_compania` AS `RECLAMACION_COMPANIA`,`cn`.`reclamacion_valor` AS `RECLAMACION_VALOR`,`cn`.`reclamacion_resultado` AS `RECLAMACION_RESULTADO`,`cn`.`reclamacion_anio_2` AS `RECLAMACION_ANIO_2`,`cn`.`reclamacion_ramo_2` AS `RECLAMACION_RAMO_2`,`cn`.`reclamacion_compania_2` AS `RECLAMACION_COMPANIA_2`,`cn`.`reclamacion_valor_2` AS `RECLAMACION_VALOR_2`,`cn`.`reclamacion_resultado_2` AS `RECLAMACION_RESULTADO_2`,(select ucase(`ciudades`.`nombre_ciudad`) from `ciudades` where (`ciudades`.`id` = `cn`.`ciudad_diligenciamiento`)) AS `CIUDAD_DILIGENCIAMIENTO`,`cn`.`sucursal` AS `SUCURSAL` from (((((((`clientes` join `tipos_documentos` `td` on((`td`.`id` = `clientes`.`tipo_documento`))) join `cliente_sarlaft_natural` `cn` on((`cn`.`cliente` = `clientes`.`id`))) join `zr_radicacion` `zr` on(((`zr`.`cliente_id` = `clientes`.`id`) and (`zr`.`created` = (select max(`zr2`.`created`) from `zr_radicacion` `zr2` where ((`zr2`.`cliente_id` = `zr`.`cliente_id`) and (`zr2`.`fecha_diligenciamiento` = (select max(`zr3`.`fecha_diligenciamiento`) from `zr_radicacion` `zr3` where ((`zr3`.`cliente_id` = `zr2`.`cliente_id`) and (`zr3`.`formulario_sarlaft` = 1) and (`zr3`.`repetido` = 0) and (`zr3`.`radicacion_proceso` = 'LEGAL')))))))))) join `zr_estado_proceso_clientes_sarlaft` `zepcs` on(((`zepcs`.`PROCESO_CLIENTE_ID` = `cn`.`cliente`) and (`zepcs`.`ESTADO_PROCESO_ID` not in (2,12)) and (`zepcs`.`FECHA_PROCESO` = (select max(`zepcs2`.`FECHA_PROCESO`) from `zr_estado_proceso_clientes_sarlaft` `zepcs2` where ((`zepcs2`.`PROCESO_CLIENTE_ID` = `zepcs`.`PROCESO_CLIENTE_ID`) and (`zepcs2`.`PROCESO_FECHA_DILIGENCIAMIENTO` = `zr`.`fecha_diligenciamiento`))))))) join `users` on((`users`.`id` = `zr`.`funcionario_id`))) join `estados_sarlaft` `es` on((`es`.`id` = `zepcs`.`ESTADO_PROCESO_ID`))) join `gestion_clientes_captura` `gcc` on(((`zr`.`cliente_id` = `gcc`.`GESTION_CLIENTE_ID`) and (`gcc`.`FECHA_GESTION` = (select max(`gestion_clientes_captura`.`FECHA_GESTION`) from `gestion_clientes_captura` where ((`gestion_clientes_captura`.`GESTION_CLIENTE_ID` = `gcc`.`GESTION_CLIENTE_ID`) and (`gcc`.`GESTION_FECHA_DILIGENCIAMIENTO` = `zr`.`fecha_diligenciamiento`))))))) where ((`cn`.`primer_apellido` is not null) or (`cn`.`segundo_apellido` is not null) or (`cn`.`primer_nombre` is not null) or (`cn`.`segundo_nombre` is not null)) group by `clientes`.`documento` union all select `zr`.`created` AS `FECHA_RADICACION`,`users`.`nombre` AS `USUARIO_ASISTEMYCA`,(select ucase(`users`.`nombre`) from `users` where (`users`.`id` = `gcc`.`GESTION_USUARIO_ID`)) AS `USUARIO_CAPTURA`,`gcc`.`FECHA_GESTION` AS `FECHA_CAPTURA`,`es`.`desc_type` AS `PROCESO`,ucase(trim(`cj`.`razon_social`)) AS `NOMBRE_TOMADOR`,`td`.`tipo_persona` AS `TIPO_PERSONA`,`clientes`.`id` AS `CLIENTE_ID`,`clientes`.`documento` AS `IDENTIFICACION_TOMADOR`,if((`zr`.`fecha_diligenciamiento` = '0000-00-00'),NULL,`zr`.`fecha_diligenciamiento`) AS `FECHA_DILIGENCIAMIENTO`,`cj`.`tipo_solicitud` AS `TIPO_SOLICITUD`,`cj`.`clase_vinculacion` AS `CLASE_VINCULACION`,`cj`.`clase_vinculacion_otro` AS `CLASE_VINCULACION_OTRO`,`cj`.`relacion_tom_asegurado` AS `TOMADOR_ASEGURADO`,`cj`.`relacion_tom_asegurado_otra` AS `TOMADOR_ASEGURADO_OTRO`,`cj`.`relacion_tom_beneficiario` AS `TOMADOR_BENEFICIARIO`,`cj`.`relacion_tom_beneficiario_otra` AS `TOMADOR_BENEFICIARIO_OTRO`,`cj`.`relacion_aseg_beneficiario` AS `ASEGURADO_BENEFICIARIO`,`cj`.`relacion_aseg_beneficiario_otra` AS `ASEGURADO_BENEFICIARIO_OTRO`,NULL AS `PRIMER_APELLIDO`,NULL AS `SEGUNDO_APELLIDO`,NULL AS `NOMBRES`,ucase(`cj`.`razon_social`) AS `NOMBRE_O_RAZON_SOCIAL`,`td`.`codigo` AS `TIPO_DOCUMENTO`,`td`.`id` AS `COD_DOCUMENTO`,if((`cj`.`info_basica_tipo_sociedad` <> 8),(select `tipos_sociedad`.`tipo` from `tipos_sociedad` where (`tipos_sociedad`.`id` = `cj`.`info_basica_tipo_sociedad`)),`cj`.`info_basica_tipo_sociedad_otro`) AS `TIPO_SOCIEDAD`,NULL AS `LUGAR_EXPEDICION`,NULL AS `SEXO`,NULL AS `ESTADO_CIVIL`,NULL AS `FECHA_EXPEDICION_DOCUMENTO_PN`,NULL AS `FECHA_NACIMIENTO_PN`,NULL AS `LUGAR_NACIMIENTO_PN`,NULL AS `NACIONALIDAD_1_PN`,NULL AS `NACIONALIDAD_2_PN`,`cj`.`ofi_principal_direccion` AS `DIRECCION_OFICINA_PRINCIPAL_RESIDENCIA`,`cj`.`ofi_principal_tipo_empresa` AS `TIPO_EMPRESA`,(select `tipos_actividades_principales`.`nombre_actividad_principal` from `tipos_actividades_principales` where (`tipos_actividades_principales`.`id` = convert(ifnull(`cj`.`ofi_principal_ciiu_cod`,`cj`.`ofi_principal_ciiu`) using utf8))) AS `CIIU_ACTIVIDAD_ECONOMICA`,ucase(`cj`.`ofi_principal_ciiu_otro`) AS `CIIU_ACTIVIDAD_ECONOMICA_OTRA`,`cj`.`ofi_principal_ciiu_cod` AS `CIIU_COD`,(select ucase(`tap`.`nombre_actividad_principal`) from `tipos_actividades_principales` `tap` where (`tap`.`id` = convert(`cj`.`ofi_principal_ciiu_cod` using utf8))) AS `ACTIVIDAD_ECONOMICA`,(select ucase(`sector`.`desc_sector`) from `sector` where (`sector`.`id` = `cj`.`ofi_principal_sector`)) AS `SECTOR`,ucase(`cj`.`ofi_principal_breve_descripcion_objeto_social`) AS `BREVE_DESCRIPCION`,(select ucase(`departamentos`.`nombre_departamento`) from `departamentos` where (`departamentos`.`id` = `cj`.`ofi_principal_departamento_empresa`)) AS `DEPARTAMENTO_OFICINA_RESIDENCIA`,(select ucase(`ciudades`.`nombre_ciudad`) from `ciudades` where (`ciudades`.`id` = `cj`.`ofi_principal_ciudad_empresa`)) AS `CIUDAD_OFICINA_RESIDENCIA`,`cj`.`ofi_principal_telefono` AS `TELEFONO_OFICINA_RESIDENCIA`,NULL AS `CELULAR`,`cj`.`ofi_principal_pagina_web` AS `PAGINA_WEB`,`cj`.`ofi_principal_email` AS `CORREO_ELECTRONICO`,(select ucase(`departamentos`.`nombre_departamento`) from `departamentos` where (`departamentos`.`id` = `cj`.`sucursal_departamento`)) AS `SUCURSAL_DEPARATAMENTO`,(select ucase(`ciudades`.`nombre_ciudad`) from `ciudades` where (`ciudades`.`id` = `cj`.`sucursal_ciudad`)) AS `SUCURSAL_CIUDAD`,`cj`.`sucursal_direccion` AS `SUCURSAL_DIRECCION`,`cj`.`sucursal_telefono` AS `SUCURSAL_TELEFONO`,`cj`.`sucursal_fax` AS `SUCURSAL_FAX`,NULL AS `OCUPACION`,NULL AS `TIPO_ACTIVIDAD`,NULL AS `CARGO`,NULL AS `EMPRESA_DONDE_TRABAJA_PN`,NULL AS `DIRECCION_EMPRESA_PN`,NULL AS `CIUDAD_EMPRESA_PN`,NULL AS `DEPARTAMENTO_EMPRESA_PN`,NULL AS `TELEFONO_EMPRESA_PN`,NULL AS `ACTIVIDAD_SEVUNDARIA_PN`,NULL AS `CIIU_SECUNDARIA_PN`,ucase(`cj`.`rep_legal_primer_apellido`) AS `REPRESENTANTE_LEGAL_PRIMER_APELLIDO`,ucase(`cj`.`rep_legal_segundo_apellido`) AS `REPRESENTANTE_LEGAL_SEGUNDO_APELLIDO`,ucase(`cj`.`rep_legal_nombres`) AS `REPRESENTANTE_LEGAL_NOMBRES`,(select `tipos_documentos`.`codigo` from `tipos_documentos` where (`tipos_documentos`.`id` = `cj`.`rep_legal_tipo_documento`)) AS `REPRESENTANTE_TIPO_DOCUMENTO`,`cj`.`rep_legal_tipo_documento` AS `REPRESENTANTE_COD_DOCUMENTO`,`cj`.`rep_legal_documento` AS `REPRESENTANTE_DOCUMENTO`,`cj`.`rep_legal_fecha_exp_documento` AS `REPRESENTANTE_FECHA_EXPEDICION`,`cj`.`rep_legal_lugar_expedicion` AS `REPRESENTANTE_LUGAR_EXPEDICION`,`cj`.`rep_legal_fecha_nacimiento` AS `REPRESENTANTE_FECHA_NACIMIENTO`,`cj`.`rep_legal_lugar_nacimiento` AS `REPRESENTANTE_LUGAR_NACIMIENTO`,(select `paises`.`nombre_pais` from `paises` where (`paises`.`id` = `cj`.`rep_legal_nacionalidad_1`)) AS `REPRESENTANTE_NACIONALIDAD_1`,(select `paises`.`nombre_pais` from `paises` where (`paises`.`id` = `cj`.`rep_legal_nacionalidad_2`)) AS `REPRESENTANTE_NACIONALIDAD_2`,`cj`.`rep_legal_email` AS `REPRESENTANTE_EMAIL`,`cj`.`rep_legal_direccion_residencia` AS `REPRESENTANTE_DIRECCION_RESIDENCIA`,(select ucase(`ciudades`.`nombre_ciudad`) from `ciudades` where (`ciudades`.`id` = `cj`.`rep_legal_ciudad_residencia`)) AS `REPRESENTANTE_CIUDAD_RESIDENCIA`,(select ucase(`departamentos`.`nombre_departamento`) from `departamentos` where (`departamentos`.`id` = `cj`.`rep_legal_departamento_residencia`)) AS `REPRESENTANTE_DEPARTAMENTO_RESIDENCIA`,(select `paises`.`nombre_pais` from `paises` where (`paises`.`id` = `cj`.`rep_legal_pais_residencia`)) AS `REPRESENTANTE_PAIS_RESIDENCIA`,`cj`.`rep_legal_telefono_residencia` AS `REPRESENTANTE_TELEFONO_RESIDENCIA`,`cj`.`rep_legal_celular_residencia` AS `REPRESENTANTE_CELULAR_RESIDENCIA`,if((`cj`.`rep_legal_persona_publica` = 'SI'),'SI','NO') AS `REPRESENTANTE_PERSONA_PUBLICA`,if((`cj`.`rep_legal_recursos_publicos` = 'SI'),'SI','NO') AS `REPRESENTANTE_RECURSOS_PUBLICOS`,if((`cj`.`rep_legal_obligaciones_tributarias` = 'SI'),'SI','NO') AS `REPRESENTANTE_OBLIGACIONES_TRIBUTARIAS`,`cj`.`rep_legal_obligaciones_tributarias_indique` AS `REPRESENTANTE_OBLIGACIONES_TRIBUTARIAS_CUAL`,NULL AS `PERSONA_PUBLICA_PN`,NULL AS `VINCULO_PERSONA_PUBLICA_PN`,NULL AS `RECURSOS_PUBLICOS_PN`,`cj`.`ingresos` AS `INGRESOS`,`cj`.`egresos` AS `EGRESOS`,`cj`.`activos` AS `ACTIVOS`,`cj`.`pasivos` AS `PASIVOS`,`cj`.`patrimonio` AS `PATRIMONIO`,`cj`.`otros_ingresos` AS `OTROS_INGRESOS`,`cj`.`desc_otros_ingresos` AS `CONCEPTO_OTROS_INGRESOS`,`cj`.`declaracion_origen_fondos` AS `DECLARACION_ORIGEN_FONDOS`,if((`cj`.`operaciones_moneda_extranjera` = 'SI'),'SI','NO') AS `TRANSACCIONES_MONEDA_EXTRANJERA`,(select `tipos_operaciones_moneda_extranjera`.`desc_operacion` from `tipos_operaciones_moneda_extranjera` where (`tipos_operaciones_moneda_extranjera`.`id` = `cj`.`tipo_operaciones_moneda_extranjera`)) AS `TRANSACCIONES_MONEDA_EXTRANJERA_CUAL`,`cj`.`tipo_operaciones_moneda_extranjera_otro` AS `TRANSACCIONES_MONEDA_EXTRANJERA_OTRAS`,if((`cj`.`productos_exterior` = 'SI'),'SI','NO') AS `PRODUCTOS_EXTERIOR`,if((`cj`.`cuentas_moneda_exterior` = 'SI'),'SI','NO') AS `CUENTAS_MONEDA_EXTRANJERA`,if((`cj`.`reclamaciones` = 1),'SI','NO') AS `RECLAMACIONES`,`cj`.`reclamacion_anio` AS `RECLAMACION_ANIO`,`cj`.`reclamacion_ramo` AS `RECLAMACION_RAMO`,`cj`.`reclamacion_compania` AS `RECLAMACION_COMPANIA`,`cj`.`reclamacion_valor` AS `RECLAMACION_VALOR`,`cj`.`reclamacion_resultado` AS `RECLAMACION_RESULTADO`,`cj`.`reclamacion_anio_2` AS `RECLAMACION_ANIO_2`,`cj`.`reclamacion_ramo_2` AS `RECLAMACION_RAMO_2`,`cj`.`reclamacion_compania_2` AS `RECLAMACION_COMPANIA_2`,`cj`.`reclamacion_valor_2` AS `RECLAMACION_VALOR_2`,`cj`.`reclamacion_resultado_2` AS `RECLAMACION_RESULTADO_2`,(select ucase(`ciudades`.`nombre_ciudad`) from `ciudades` where (`ciudades`.`id` = `cj`.`ciudad_diligenciamiento`)) AS `CIUDAD_DILIGENCIAMIENTO`,`cj`.`sucursal` AS `SUCURSAL` from (((((((`clientes` join `tipos_documentos` `td` on((`td`.`id` = `clientes`.`tipo_documento`))) join `cliente_sarlaft_juridico` `cj` on((`cj`.`cliente` = `clientes`.`id`))) join `zr_radicacion` `zr` on(((`zr`.`cliente_id` = `cj`.`cliente`) and (`zr`.`created` = (select max(`zr2`.`created`) from `zr_radicacion` `zr2` where ((`zr2`.`cliente_id` = `zr`.`cliente_id`) and (`zr2`.`fecha_diligenciamiento` = (select max(`zr3`.`fecha_diligenciamiento`) from `zr_radicacion` `zr3` where ((`zr3`.`cliente_id` = `zr2`.`cliente_id`) and (`zr3`.`formulario_sarlaft` = 1) and (`zr3`.`repetido` = 0) and (`zr3`.`radicacion_proceso` = 'LEGAL')))))))))) join `zr_estado_proceso_clientes_sarlaft` `zepcs` on(((`zepcs`.`PROCESO_CLIENTE_ID` = `cj`.`cliente`) and (`zepcs`.`ESTADO_PROCESO_ID` not in (2,12)) and (`zepcs`.`FECHA_PROCESO` = (select max(`zepcs2`.`FECHA_PROCESO`) from `zr_estado_proceso_clientes_sarlaft` `zepcs2` where ((`zepcs2`.`PROCESO_CLIENTE_ID` = `zepcs`.`PROCESO_CLIENTE_ID`) and (`zepcs2`.`PROCESO_FECHA_DILIGENCIAMIENTO` = `zr`.`fecha_diligenciamiento`))))))) join `users` on((`users`.`id` = `zr`.`funcionario_id`))) join `estados_sarlaft` `es` on((`es`.`id` = `zepcs`.`ESTADO_PROCESO_ID`))) join `gestion_clientes_captura` `gcc` on(((`zr`.`cliente_id` = `gcc`.`GESTION_CLIENTE_ID`) and (`gcc`.`FECHA_GESTION` = (select max(`gestion_clientes_captura`.`FECHA_GESTION`) from `gestion_clientes_captura` where ((`gestion_clientes_captura`.`GESTION_CLIENTE_ID` = `gcc`.`GESTION_CLIENTE_ID`) and (`gcc`.`GESTION_FECHA_DILIGENCIAMIENTO` = `zr`.`fecha_diligenciamiento`))))))) where (`cj`.`razon_social` is not null) group by `clientes`.`documento` */;

/*View structure for view reporte_clientes_checklist_documentos */

/*!50001 DROP TABLE IF EXISTS `reporte_clientes_checklist_documentos` */;
/*!50001 DROP VIEW IF EXISTS `reporte_clientes_checklist_documentos` */;

/*!50001 CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `reporte_clientes_checklist_documentos` AS select distinct cast(`zr`.`created` as date) AS `FECHA_RADICACION`,`zr`.`correo_radicacion` AS `CORREO`,`tipos_documentos`.`codigo` AS `CLIENTE_TIPO_DOCUMENTO_CODIGO`,`clientes`.`documento` AS `CLIENTE_DOCUMENTO`,ucase(trim(concat_ws(' ',`cn`.`primer_apellido`,`cn`.`segundo_apellido`,`cn`.`primer_nombre`,`cn`.`segundo_nombre`))) AS `NOMBRE_CLIENTE`,`zr`.`radicacion_proceso` AS `RADICACION_PROCESO`,if(((select count(`archivo_organizado`.`ID_TIPO_DOC`) from `archivo_organizado` where ((`archivo_organizado`.`NUMERO_IDENT_CLIENTE` = `clientes`.`documento`) and (`archivo_organizado`.`ID_TIPO_DOC` = 'SAA'))) >= 1),'SI','NO') AS `SAA`,if((`zr`.`formulario` = 'Nuevo'),'SI','NO') AS `TIPO_FORMULARIO`,if((`zr`.`devuelto` = 'Si'),'DEVUELTO',(select `estados_sarlaft`.`desc_type` from `estados_sarlaft` where (`estados_sarlaft`.`id` = `zepcs`.`ESTADO_PROCESO_ID`))) AS `CLIENTE_ESTADO_PROCESO`,`zr`.`numero_planilla` AS `NUMERO_PLANILLA`,`zr`.`radicacion_observacion` AS `OBSERVACIONES` from ((((`cliente_sarlaft_natural` `cn` join `clientes` on((`clientes`.`id` = `cn`.`cliente`))) join `tipos_documentos` on((`tipos_documentos`.`id` = `clientes`.`tipo_documento`))) join `zr_radicacion` `zr` on(((`zr`.`cliente_id` = `clientes`.`id`) and (`zr`.`created` = (select max(`zr2`.`created`) from `zr_radicacion` `zr2` where ((`zr2`.`cliente_id` = `zr`.`cliente_id`) and (`zr2`.`fecha_diligenciamiento` = (select max(`zr3`.`fecha_diligenciamiento`) from `zr_radicacion` `zr3` where (`zr3`.`cliente_id` = `zr2`.`cliente_id`))))))))) join `zr_estado_proceso_clientes_sarlaft` `zepcs` on(((`zepcs`.`PROCESO_CLIENTE_ID` = `zr`.`cliente_id`) and (`zepcs`.`PROCESO_FECHA_DILIGENCIAMIENTO` = `zr`.`fecha_diligenciamiento`) and (`zepcs`.`FECHA_PROCESO` = (select max(`zr_estado_proceso_clientes_sarlaft`.`FECHA_PROCESO`) from `zr_estado_proceso_clientes_sarlaft` where (`zr_estado_proceso_clientes_sarlaft`.`PROCESO_CLIENTE_ID` = `zepcs`.`PROCESO_CLIENTE_ID`)))))) union all select distinct cast(`zr`.`created` as date) AS `FECHA_RADICACION`,`zr`.`correo_radicacion` AS `CORREO`,`tipos_documentos`.`codigo` AS `CLIENTE_TIPO_DOCUMENTO_CODIGO`,`clientes`.`documento` AS `CLIENTE_DOCUMENTO`,ucase(`cj`.`razon_social`) AS `NOMBRE_CLIENTE`,`zr`.`radicacion_proceso` AS `RADICACION_PROCESO`,if(((select count(`archivo_organizado`.`ID_TIPO_DOC`) from `archivo_organizado` where ((`archivo_organizado`.`NUMERO_IDENT_CLIENTE` = `clientes`.`documento`) and (`archivo_organizado`.`ID_TIPO_DOC` = 'SAA'))) >= 1),'SI','NO') AS `SAA`,if((`zr`.`formulario` = 'Nuevo'),'SI','NO') AS `TIPO_FORMULARIO`,if((`zr`.`devuelto` = 'Si'),'DEVUELTO',(select `estados_sarlaft`.`desc_type` from `estados_sarlaft` where (`estados_sarlaft`.`id` = `zepcs`.`ESTADO_PROCESO_ID`))) AS `CLIENTE_ESTADO_PROCESO`,`zr`.`numero_planilla` AS `NUMERO_PLANILLA`,`zr`.`radicacion_observacion` AS `OBSERVACIONES` from ((((`cliente_sarlaft_juridico` `cj` join `clientes` on((`clientes`.`id` = `cj`.`cliente`))) join `tipos_documentos` on((`tipos_documentos`.`id` = `clientes`.`tipo_documento`))) join `zr_radicacion` `zr` on(((`zr`.`cliente_id` = `clientes`.`id`) and (`zr`.`created` = (select max(`zr2`.`created`) from `zr_radicacion` `zr2` where ((`zr2`.`cliente_id` = `zr`.`cliente_id`) and (`zr2`.`fecha_diligenciamiento` = (select max(`zr3`.`fecha_diligenciamiento`) from `zr_radicacion` `zr3` where (`zr3`.`cliente_id` = `zr2`.`cliente_id`))))))))) join `zr_estado_proceso_clientes_sarlaft` `zepcs` on(((`zepcs`.`PROCESO_CLIENTE_ID` = `zr`.`cliente_id`) and (`zepcs`.`PROCESO_FECHA_DILIGENCIAMIENTO` = `zr`.`fecha_diligenciamiento`) and (`zepcs`.`FECHA_PROCESO` = (select max(`zr_estado_proceso_clientes_sarlaft`.`FECHA_PROCESO`) from `zr_estado_proceso_clientes_sarlaft` where (`zr_estado_proceso_clientes_sarlaft`.`PROCESO_CLIENTE_ID` = `zepcs`.`PROCESO_CLIENTE_ID`)))))) */;

/*View structure for view reporte_clientes_completitud_verificacion */

/*!50001 DROP TABLE IF EXISTS `reporte_clientes_completitud_verificacion` */;
/*!50001 DROP VIEW IF EXISTS `reporte_clientes_completitud_verificacion` */;

/*!50001 CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `reporte_clientes_completitud_verificacion` AS select `zr`.`created` AS `FECHA_RADICACION`,`zr`.`correo_radicacion` AS `CORREO`,`td`.`codigo` AS `CLIENTE_TIPO_DOCUMENTO_CODIGO`,`clientes`.`documento` AS `CLIENTE_DOCUMENTO`,`cs`.`NOMBRE_CLIENTE` AS `NOMBRE_CLIENTE`,(select max(`gestion_clientes_completitud_verificacion`.`FECHA_GESTION`) from `gestion_clientes_completitud_verificacion` where ((`gestion_clientes_completitud_verificacion`.`GESTION_CLIENTE_ID` = `gcv`.`GESTION_CLIENTE_ID`) and (`gestion_clientes_completitud_verificacion`.`GESTION_PROCESO_ID` = 6))) AS `FECHA_COMPLETITUD`,(select `gestion_clientes_completitud_verificacion`.`GESTION_CAMPOS_COMPLETADOS` from `gestion_clientes_completitud_verificacion` where ((`gestion_clientes_completitud_verificacion`.`GESTION_CLIENTE_ID` = `gcv`.`GESTION_CLIENTE_ID`) and (`gestion_clientes_completitud_verificacion`.`GESTION_PROCESO_ID` = 6))) AS `GESTION_CAMPOS_COMPLETADOS`,if((`gcv`.`GESTION_PROCESO_ID` = 5),`gcv`.`FECHA_GESTION`,NULL) AS `FECHA_VERIFICACION`,if((isnull(`gcv`.`GESTION_CAMPOS_VACIOS`) and ((select `gestion_clientes_completitud_verificacion`.`GESTION_CAMPOS_COMPLETADOS` from `gestion_clientes_completitud_verificacion` where ((`gestion_clientes_completitud_verificacion`.`GESTION_CLIENTE_ID` = `gcv`.`GESTION_CLIENTE_ID`) and (`gestion_clientes_completitud_verificacion`.`GESTION_PROCESO_ID` = 6))) is not null)),'SI','NO') AS `PREGUNTA_CAMPOS_COMPLETADOS`,`gcv`.`GESTION_OBSERVACIONES` AS `GESTION_OBSERVACIONES` from ((((`gestion_clientes_completitud_verificacion` `gcv` join `clientes_sarlaft` `cs` on((`cs`.`cliente_id` = `gcv`.`GESTION_CLIENTE_ID`))) join `clientes` on((`clientes`.`id` = `cs`.`cliente_id`))) join `tipos_documentos` `td` on((`td`.`id` = `clientes`.`tipo_documento`))) join `zr_radicacion` `zr` on(((`zr`.`cliente_id` = `clientes`.`id`) and (`zr`.`formulario_sarlaft` = 1) and (`zr`.`fecha_diligenciamiento` = `gcv`.`GESTION_FECHA_DILIGENCIAMIENTO`)))) where (`gcv`.`FECHA_GESTION` = (select max(`gestion_clientes_completitud_verificacion`.`FECHA_GESTION`) from `gestion_clientes_completitud_verificacion` where (`gestion_clientes_completitud_verificacion`.`GESTION_CLIENTE_ID` = `gcv`.`GESTION_CLIENTE_ID`))) */;

/*View structure for view reporte_clientes_pendientes */

/*!50001 DROP TABLE IF EXISTS `reporte_clientes_pendientes` */;
/*!50001 DROP VIEW IF EXISTS `reporte_clientes_pendientes` */;

/*!50001 CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `reporte_clientes_pendientes` AS select distinct `clientes`.`id` AS `id`,`zr`.`created` AS `FECHA_RADICACION`,`zr`.`fecha_diligenciamiento` AS `fecha_diligenciamiento`,`zr`.`correo_radicacion` AS `CORREO`,`zr`.`fecha_envio_correo` AS `FECHA_ENVIO_CORREO`,`td`.`codigo` AS `CLIENTE_TIPO_DOCUMENTO_CODIGO`,`clientes`.`documento` AS `CLIENTE_DOCUMENTO`,`cj`.`razon_social` AS `NOMBRE_CLIENTE`,`zr`.`correo_radicacion` AS `CORREO_RADICACION`,if((`cj`.`firma` = 1),'SI','NO') AS `FIRMA`,if((`cj`.`huella` = 1),'SI','NO') AS `HUELLA`,if((`cj`.`entrevista` = 1),'SI','NO') AS `ENTREVISTA`,`es`.`desc_type` AS `ESTADO_PROCESO`,`zr`.`radicacion_proceso` AS `PROCESO_RADICACION`,if((`zepcs`.`ESTADO_PROCESO_ID` <> 12),NULL,'PENDIENTE SARLAFT') AS `DOCUMENTO_PENDIENTE_CODIGO`,`zr`.`radicacion_observacion` AS `OBSERVACION` from (((((`clientes` join `tipos_documentos` `td` on((`td`.`id` = `clientes`.`tipo_documento`))) join `cliente_sarlaft_juridico` `cj` on((`cj`.`cliente` = `clientes`.`id`))) join `zr_radicacion` `zr` on(((`zr`.`cliente_id` = `cj`.`cliente`) and (`zr`.`created` = (select max(`zr2`.`created`) from `zr_radicacion` `zr2` where ((`zr2`.`cliente_id` = `zr`.`cliente_id`) and (`zr2`.`fecha_diligenciamiento` = (select max(`zr3`.`fecha_diligenciamiento`) from `zr_radicacion` `zr3` where ((`zr3`.`cliente_id` = `zr2`.`cliente_id`) and (`zr3`.`formulario_sarlaft` = 1) and (`zr3`.`repetido` = 0) and (`zr3`.`radicacion_proceso` = 'LEGAL')))))))))) left join `zr_estado_proceso_clientes_sarlaft` `zepcs` on(((`zepcs`.`PROCESO_CLIENTE_ID` = `zr`.`cliente_id`) and (`zepcs`.`FECHA_PROCESO` = (select max(`zepcs2`.`FECHA_PROCESO`) from `zr_estado_proceso_clientes_sarlaft` `zepcs2` where ((`zepcs2`.`PROCESO_CLIENTE_ID` = `zepcs`.`PROCESO_CLIENTE_ID`) and (`zepcs2`.`PROCESO_FECHA_DILIGENCIAMIENTO` = `zr`.`fecha_diligenciamiento`))))))) left join `estados_sarlaft` `es` on((`es`.`id` = `zepcs`.`ESTADO_PROCESO_ID`))) where (`zepcs`.`ESTADO_PROCESO_ID` in (10,11,12,15,16,2)) union all select distinct `clientes`.`id` AS `id`,`zr`.`created` AS `FECHA_RADICACION`,`zr`.`fecha_diligenciamiento` AS `fecha_diligenciamiento`,`zr`.`correo_radicacion` AS `CORREO`,`zr`.`fecha_envio_correo` AS `FECHA_ENVIO_CORREO`,`td`.`codigo` AS `CLIENTE_TIPO_DOCUMENTO_CODIGO`,`clientes`.`documento` AS `CLIENTE_DOCUMENTO`,trim(concat_ws(' ',`cn`.`primer_apellido`,`cn`.`segundo_apellido`,`cn`.`primer_nombre`,`cn`.`segundo_nombre`)) AS `NOMBRE_CLIENTE`,`zr`.`correo_radicacion` AS `CORREO_RADICACION`,if((`cn`.`firma` = 1),'SI','NO') AS `FIRMA`,if((`cn`.`huella` = 1),'SI','NO') AS `HUELLA`,if((`cn`.`entrevista` = 1),'SI','NO') AS `ENTREVISTA`,`es`.`desc_type` AS `ESTADO_PROCESO`,`zr`.`radicacion_proceso` AS `PROCESO_RADICACION`,if((`zepcs`.`ESTADO_PROCESO_ID` <> 12),NULL,'PENDIENTE SARLAFT') AS `DOCUMENTO_PENDIENTE_CODIGO`,`zr`.`radicacion_observacion` AS `OBSERVACION` from (((((`clientes` join `tipos_documentos` `td` on((`td`.`id` = `clientes`.`tipo_documento`))) join `cliente_sarlaft_natural` `cn` on((`cn`.`cliente` = `clientes`.`id`))) join `zr_radicacion` `zr` on(((`zr`.`cliente_id` = `cn`.`cliente`) and (`zr`.`created` = (select max(`zr2`.`created`) from `zr_radicacion` `zr2` where ((`zr2`.`cliente_id` = `zr`.`cliente_id`) and (`zr2`.`fecha_diligenciamiento` = (select max(`zr3`.`fecha_diligenciamiento`) from `zr_radicacion` `zr3` where ((`zr3`.`cliente_id` = `zr2`.`cliente_id`) and (`zr3`.`formulario_sarlaft` = 1) and (`zr3`.`repetido` = 0) and (`zr3`.`radicacion_proceso` = 'LEGAL')))))))))) left join `zr_estado_proceso_clientes_sarlaft` `zepcs` on(((`zepcs`.`PROCESO_CLIENTE_ID` = `zr`.`cliente_id`) and (`zepcs`.`FECHA_PROCESO` = (select max(`zepcs2`.`FECHA_PROCESO`) from `zr_estado_proceso_clientes_sarlaft` `zepcs2` where ((`zepcs2`.`PROCESO_CLIENTE_ID` = `zepcs`.`PROCESO_CLIENTE_ID`) and (`zepcs2`.`PROCESO_FECHA_DILIGENCIAMIENTO` = `zr`.`fecha_diligenciamiento`))))))) left join `estados_sarlaft` `es` on((`es`.`id` = `zepcs`.`ESTADO_PROCESO_ID`))) where (`zepcs`.`ESTADO_PROCESO_ID` in (10,11,12,15,16,2)) */;

/*View structure for view reporte_facturacion */

/*!50001 DROP TABLE IF EXISTS `reporte_facturacion` */;
/*!50001 DROP VIEW IF EXISTS `reporte_facturacion` */;

/*!50001 CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `reporte_facturacion` AS select `clientes`.`id` AS `cliente_id`,`zr`.`created` AS `FECHA_RADICACION`,`users`.`id` AS `user_id`,`users`.`nombre` AS `USUARIO_ASISTEMYCA`,(select ucase(`users1`.`nombre`) from `users` `users1` where (`users1`.`id` = `gcc`.`GESTION_USUARIO_ID`)) AS `USUARIO_CAPTURA`,`gcc`.`FECHA_GESTION` AS `FECHA_CAPTURA`,`zr`.`numero_planilla` AS `NUMERO_PLANILLA`,if((`zr`.`fecha_diligenciamiento` = '0000-00-00'),NULL,`zr`.`fecha_diligenciamiento`) AS `FECHA_DILIGENCIAMIENTO`,`zr`.`correo_radicacion` AS `USUARIO_SUSCRIPTOR`,`zr`.`separado` AS `SEPARADO`,`td`.`codigo` AS `TIPO_CLIENTE_CODIGO`,`clientes`.`documento` AS `CLIENTE_DOCUMENTO`,concat_ws('-',concat(date_format(cast(`zr`.`created` as date),'%Y%m%d'),`zr`.`cliente_id`),`zr`.`consecutivo`) AS `NUMERO_RADICACION`,`zr`.`cantidad_documentos` AS `CANT_DOCUMENTOS`,trim(concat_ws(' ',`cn`.`primer_apellido`,`cn`.`segundo_apellido`,`cn`.`primer_nombre`,`cn`.`segundo_nombre`)) AS `NOMBRE_TOMADOR`,if((`zr`.`repetido` = 1),'SI','NO') AS `FORMULARIO_REPETIDO`,(select max(`gccv1`.`FECHA_GESTION`) from `gestion_clientes_completitud_verificacion` `gccv1` where ((`gccv1`.`GESTION_CLIENTE_ID` = `gccv`.`GESTION_CLIENTE_ID`) and (`gccv1`.`GESTION_PROCESO_ID` = 6))) AS `PROCESO_COMPLETITUD`,(select max(`gccv2`.`FECHA_GESTION`) from `gestion_clientes_completitud_verificacion` `gccv2` where ((`gccv2`.`GESTION_CLIENTE_ID` = `gccv`.`GESTION_CLIENTE_ID`) and (`gccv2`.`GESTION_PROCESO_ID` = 5))) AS `PROCESO_VERIFICACION`,`zt`.`tipologia` AS `ESTADO_TIPOLOGIA`,`gccv`.`GESTION_NO_INTENTOS` AS `INTENTO_LLAMADA`,`zr`.`tipo_medio` AS `FORMA_RECEPCION`,`zr`.`radicacion_proceso` AS `RADICACION_PROCESO`,`zr`.`formulario` AS `TIPO_FORMULARIO`,`zepcs`.`PROCESO_INOUTBOUND` AS `TIPO_LLAMADA`,if((`zr`.`repetido` <> 1),if((`es`.`desc_type` is not null),`es`.`desc_type`,'FECHA ANTIGUA'),'DEVUELTO') AS `ESTADO_PROCESO`,if(((`zepcs`.`ESTADO_PROCESO_ID` not in (2,1)) and (`zr`.`repetido` <> 1)),`zepcs`.`FECHA_PROCESO`,NULL) AS `FECHA_PROCESO_CAPTURA`,if(((`zepcs`.`ESTADO_PROCESO_ID` not in (2,1)) and (`zr`.`repetido` <> 1)),'Si','No') AS `FORMULARIO_CAPTURADO`,`zr`.`radicacion_observacion` AS `OBSERVACION` from (((((((((`clientes` join `tipos_documentos` `td` on((`td`.`id` = `clientes`.`tipo_documento`))) join `zr_radicacion` `zr` on((`zr`.`cliente_id` = `clientes`.`id`))) join `users` on((`users`.`id` = `zr`.`funcionario_id`))) join `cliente_sarlaft_natural` `cn` on((`cn`.`cliente` = `zr`.`cliente_id`))) left join `gestion_clientes_captura` `gcc` on(((`gcc`.`GESTION_CLIENTE_ID` = `zr`.`cliente_id`) and (`gcc`.`FECHA_GESTION` = (select max(`gcc2`.`FECHA_GESTION`) from `gestion_clientes_captura` `gcc2` where ((`gcc2`.`GESTION_CLIENTE_ID` = `gcc`.`GESTION_CLIENTE_ID`) and (`gcc2`.`GESTION_FECHA_DILIGENCIAMIENTO` = `zr`.`fecha_diligenciamiento`) and (`gcc2`.`FECHA_GESTION` >= `zr`.`created`))))))) left join `gestion_clientes_completitud_verificacion` `gccv` on(((`gccv`.`GESTION_CLIENTE_ID` = `zr`.`cliente_id`) and (`gccv`.`FECHA_GESTION` = (select max(`gccv2`.`FECHA_GESTION`) from `gestion_clientes_completitud_verificacion` `gccv2` where ((`gccv2`.`GESTION_CLIENTE_ID` = `gccv`.`GESTION_CLIENTE_ID`) and (`gccv2`.`GESTION_FECHA_DILIGENCIAMIENTO` = `zr`.`fecha_diligenciamiento`) and (`gccv2`.`FECHA_GESTION` >= `zr`.`created`))))))) left join `zr_tipologias` `zt` on((`zt`.`id` = `gccv`.`GESTION_ESTADO_TIPOLOGIA_ID`))) left join `zr_estado_proceso_clientes_sarlaft` `zepcs` on(((`zepcs`.`PROCESO_CLIENTE_ID` = `zr`.`cliente_id`) and (`zepcs`.`FECHA_PROCESO` = (select max(`zepcs1`.`FECHA_PROCESO`) from `zr_estado_proceso_clientes_sarlaft` `zepcs1` where ((`zepcs1`.`PROCESO_CLIENTE_ID` = `zepcs`.`PROCESO_CLIENTE_ID`) and (`zepcs1`.`PROCESO_FECHA_DILIGENCIAMIENTO` = `zr`.`fecha_diligenciamiento`) and (`zepcs1`.`FECHA_PROCESO` >= `zr`.`created`))))))) left join `estados_sarlaft` `es` on((`es`.`id` = `zepcs`.`ESTADO_PROCESO_ID`))) union all select `clientes`.`id` AS `cliente_id`,`zr`.`created` AS `FECHA_RADICACION`,`users`.`id` AS `user_id`,`users`.`nombre` AS `USUARIO_ASISTEMYCA`,(select ucase(`users1`.`nombre`) from `users` `users1` where (`users1`.`id` = `gcc`.`GESTION_USUARIO_ID`)) AS `USUARIO_CAPTURA`,`gcc`.`FECHA_GESTION` AS `FECHA_CAPTURA`,`zr`.`numero_planilla` AS `NUMERO_PLANILLA`,if((`zr`.`fecha_diligenciamiento` = '0000-00-00'),NULL,`zr`.`fecha_diligenciamiento`) AS `FECHA_DILIGENCIAMIENTO`,`zr`.`correo_radicacion` AS `USUARIO_SUSCRIPTOR`,`zr`.`separado` AS `SEPARADO`,`td`.`codigo` AS `TIPO_CLIENTE_CODIGO`,`clientes`.`documento` AS `CLIENTE_DOCUMENTO`,concat_ws('-',concat(date_format(cast(`zr`.`created` as date),'%Y%m%d'),`zr`.`cliente_id`),`zr`.`consecutivo`) AS `NUMERO_RADICACION`,`zr`.`cantidad_documentos` AS `CANT_DOCUMENTOS`,trim(`cj`.`razon_social`) AS `NOMBRE_TOMADOR`,if((`zr`.`repetido` = 1),'SI','NO') AS `FORMULARIO_REPETIDO`,(select max(`gccv1`.`FECHA_GESTION`) from `gestion_clientes_completitud_verificacion` `gccv1` where ((`gccv1`.`GESTION_CLIENTE_ID` = `gccv`.`GESTION_CLIENTE_ID`) and (`gccv1`.`GESTION_PROCESO_ID` = 6))) AS `PROCESO_COMPLETITUD`,(select max(`gccv2`.`FECHA_GESTION`) from `gestion_clientes_completitud_verificacion` `gccv2` where ((`gccv2`.`GESTION_CLIENTE_ID` = `gccv`.`GESTION_CLIENTE_ID`) and (`gccv2`.`GESTION_PROCESO_ID` = 5))) AS `PROCESO_VERIFICACION`,`zt`.`tipologia` AS `ESTADO_TIPOLOGIA`,`gccv`.`GESTION_NO_INTENTOS` AS `INTENTO_LLAMADA`,`zr`.`tipo_medio` AS `FORMA_RECEPCION`,`zr`.`radicacion_proceso` AS `RADICACION_PROCESO`,`zr`.`formulario` AS `TIPO_FORMULARIO`,`zepcs`.`PROCESO_INOUTBOUND` AS `TIPO_LLAMADA`,if((`zr`.`repetido` <> 1),if((`es`.`desc_type` is not null),`es`.`desc_type`,'FECHA ANTIGUA'),'DEVUELTO') AS `ESTADO_PROCESO`,if(((`zepcs`.`ESTADO_PROCESO_ID` not in (2,1)) and (`zr`.`repetido` <> 1)),`zepcs`.`FECHA_PROCESO`,NULL) AS `FECHA_PROCESO_CAPTURA`,if(((`zepcs`.`ESTADO_PROCESO_ID` not in (2,1)) and (`zr`.`repetido` <> 1)),'Si','No') AS `FORMULARIO_CAPTURADO`,`zr`.`radicacion_observacion` AS `OBSERVACION` from (((((((((`clientes` join `tipos_documentos` `td` on((`td`.`id` = `clientes`.`tipo_documento`))) join `zr_radicacion` `zr` on((`zr`.`cliente_id` = `clientes`.`id`))) join `users` on((`users`.`id` = `zr`.`funcionario_id`))) join `cliente_sarlaft_juridico` `cj` on((`cj`.`cliente` = `zr`.`cliente_id`))) left join `gestion_clientes_captura` `gcc` on(((`gcc`.`GESTION_CLIENTE_ID` = `zr`.`cliente_id`) and (`gcc`.`FECHA_GESTION` = (select max(`gcc2`.`FECHA_GESTION`) from `gestion_clientes_captura` `gcc2` where ((`gcc2`.`GESTION_CLIENTE_ID` = `gcc`.`GESTION_CLIENTE_ID`) and (`gcc2`.`GESTION_FECHA_DILIGENCIAMIENTO` = `zr`.`fecha_diligenciamiento`) and (`gcc2`.`FECHA_GESTION` >= `zr`.`created`))))))) left join `gestion_clientes_completitud_verificacion` `gccv` on(((`gccv`.`GESTION_CLIENTE_ID` = `zr`.`cliente_id`) and (`gccv`.`FECHA_GESTION` = (select max(`gccv2`.`FECHA_GESTION`) from `gestion_clientes_completitud_verificacion` `gccv2` where ((`gccv2`.`GESTION_CLIENTE_ID` = `gccv`.`GESTION_CLIENTE_ID`) and (`gccv2`.`GESTION_FECHA_DILIGENCIAMIENTO` = `zr`.`fecha_diligenciamiento`) and (`gccv2`.`FECHA_GESTION` >= `zr`.`created`))))))) left join `zr_tipologias` `zt` on((`zt`.`id` = `gccv`.`GESTION_ESTADO_TIPOLOGIA_ID`))) left join `zr_estado_proceso_clientes_sarlaft` `zepcs` on(((`zepcs`.`PROCESO_CLIENTE_ID` = `zr`.`cliente_id`) and (`zepcs`.`FECHA_PROCESO` = (select max(`zepcs1`.`FECHA_PROCESO`) from `zr_estado_proceso_clientes_sarlaft` `zepcs1` where ((`zepcs1`.`PROCESO_CLIENTE_ID` = `zepcs`.`PROCESO_CLIENTE_ID`) and (`zepcs1`.`PROCESO_FECHA_DILIGENCIAMIENTO` = `zr`.`fecha_diligenciamiento`) and (`zepcs1`.`FECHA_PROCESO` >= `zr`.`created`))))))) left join `estados_sarlaft` `es` on((`es`.`id` = `zepcs`.`ESTADO_PROCESO_ID`))) */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
