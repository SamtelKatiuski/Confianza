DELIMITER $$

ALTER ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `reporte_clientes_checklist_documentos` AS 
SELECT DISTINCT CAST(`zr`.`created` AS DATE) AS `FECHA_RADICACION`,`zr`.`correo_radicacion` AS `CORREO`,`tipos_documentos`.`codigo` AS `CLIENTE_TIPO_DOCUMENTO_CODIGO`,`clientes`.`documento` AS `CLIENTE_DOCUMENTO`,UCASE(TRIM(CONCAT_WS(' ',`cn`.`primer_apellido`,`cn`.`segundo_apellido`,`cn`.`primer_nombre`,`cn`.`segundo_nombre`))) AS `NOMBRE_CLIENTE`,`zr`.`radicacion_proceso` AS `RADICACION_PROCESO`,IF(((SELECT COUNT(`archivo_organizado`.`ID_TIPO_DOC`) FROM `archivo_organizado` WHERE ((`archivo_organizado`.`NUMERO_IDENT_CLIENTE` = `clientes`.`documento`) AND (`archivo_organizado`.`ID_TIPO_DOC` = 'SAA'))) >= 1),'SI','NO') AS `SAA`,IF((`zr`.`formulario` = 'Nuevo'),'SI','NO') AS `TIPO_FORMULARIO`,IF((`zr`.`devuelto` = 'Si'),'DEVUELTO',(SELECT `estados_sarlaft`.`desc_type` FROM `estados_sarlaft` WHERE (`estados_sarlaft`.`id` = `zepcs`.`ESTADO_PROCESO_ID`))) AS `CLIENTE_ESTADO_PROCESO`,`zr`.`numero_planilla` AS `NUMERO_PLANILLA`,`zr`.`radicacion_observacion` AS `OBSERVACIONES` FROM ((((`cliente_sarlaft_natural` `cn` JOIN `clientes` ON((`clientes`.`id` = `cn`.`cliente`))) JOIN `tipos_documentos` ON((`tipos_documentos`.`id` = `clientes`.`tipo_documento`))) JOIN `zr_radicacion` `zr` ON(((`zr`.`cliente_id` = `clientes`.`id`) AND (`zr`.`created` = (SELECT MAX(`zr2`.`created`) FROM `zr_radicacion` `zr2` WHERE ((`zr2`.`cliente_id` = `zr`.`cliente_id`) AND (`zr2`.`fecha_diligenciamiento` = (SELECT MAX(`zr3`.`fecha_diligenciamiento`) FROM `zr_radicacion` `zr3` WHERE (`zr3`.`cliente_id` = `zr2`.`cliente_id`))))))))) JOIN `zr_estado_proceso_clientes_sarlaft` `zepcs` ON(((`zepcs`.`PROCESO_CLIENTE_ID` = `zr`.`cliente_id`) AND (`zepcs`.`PROCESO_FECHA_DILIGENCIAMIENTO` = `zr`.`fecha_diligenciamiento`) AND (`zepcs`.`FECHA_PROCESO` = (SELECT MAX(`zr_estado_proceso_clientes_sarlaft`.`FECHA_PROCESO`) FROM `zr_estado_proceso_clientes_sarlaft` WHERE (`zr_estado_proceso_clientes_sarlaft`.`PROCESO_CLIENTE_ID` = `zepcs`.`PROCESO_CLIENTE_ID`)))))) UNION ALL SELECT DISTINCT CAST(`zr`.`created` AS DATE) AS `FECHA_RADICACION`,`zr`.`correo_radicacion` AS `CORREO`,`tipos_documentos`.`codigo` AS `CLIENTE_TIPO_DOCUMENTO_CODIGO`,`clientes`.`documento` AS `CLIENTE_DOCUMENTO`,UCASE(`cj`.`razon_social`) AS `NOMBRE_CLIENTE`,`zr`.`radicacion_proceso` AS `RADICACION_PROCESO`,IF(((SELECT COUNT(`archivo_organizado`.`ID_TIPO_DOC`) FROM `archivo_organizado` WHERE ((`archivo_organizado`.`NUMERO_IDENT_CLIENTE` = `clientes`.`documento`) AND (`archivo_organizado`.`ID_TIPO_DOC` = 'SAA'))) >= 1),'SI','NO') AS `SAA`,IF((`zr`.`formulario` = 'Nuevo'),'SI','NO') AS `TIPO_FORMULARIO`,IF((`zr`.`devuelto` = 'Si'),'DEVUELTO',(SELECT `estados_sarlaft`.`desc_type` FROM `estados_sarlaft` WHERE (`estados_sarlaft`.`id` = `zepcs`.`ESTADO_PROCESO_ID`))) AS `CLIENTE_ESTADO_PROCESO`,`zr`.`numero_planilla` AS `NUMERO_PLANILLA`,`zr`.`radicacion_observacion` AS `OBSERVACIONES` FROM ((((`cliente_sarlaft_juridico` `cj` JOIN `clientes` ON((`clientes`.`id` = `cj`.`cliente`))) JOIN `tipos_documentos` ON((`tipos_documentos`.`id` = `clientes`.`tipo_documento`))) JOIN `zr_radicacion` `zr` ON(((`zr`.`cliente_id` = `clientes`.`id`) AND (`zr`.`created` = (SELECT MAX(`zr2`.`created`) FROM `zr_radicacion` `zr2` WHERE ((`zr2`.`cliente_id` = `zr`.`cliente_id`) AND (`zr2`.`fecha_diligenciamiento` = (SELECT MAX(`zr3`.`fecha_diligenciamiento`) FROM `zr_radicacion` `zr3` WHERE (`zr3`.`cliente_id` = `zr2`.`cliente_id`))))))))) JOIN `zr_estado_proceso_clientes_sarlaft` `zepcs` ON(((`zepcs`.`PROCESO_CLIENTE_ID` = `zr`.`cliente_id`) AND (`zepcs`.`PROCESO_FECHA_DILIGENCIAMIENTO` = `zr`.`fecha_diligenciamiento`) AND (`zepcs`.`FECHA_PROCESO` = (SELECT MAX(`zr_estado_proceso_clientes_sarlaft`.`FECHA_PROCESO`) FROM `zr_estado_proceso_clientes_sarlaft` WHERE (`zr_estado_proceso_clientes_sarlaft`.`PROCESO_CLIENTE_ID` = `zepcs`.`PROCESO_CLIENTE_ID`))))))$$

DELIMITER ;