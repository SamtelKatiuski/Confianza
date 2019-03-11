DELIMITER $$

ALTER ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `reporte_clientes_beneficiario_final_peps` AS 
SELECT
  CAST(`zr`.`created` AS DATE)     AS `FECHA_RADICACION`,
  `linea_negocio`.`NOMBRE`         AS `LINEA_NEGOCIO`,
  `zr`.`correo_radicacion`         AS `CORREO`,
  `tipos_documentos`.`descripcion` AS `CLIENTE_TIPO_DOCUMENTO_CODIGO`,
  `clientes`.`documento`           AS `CLIENTE_DOCUMENTO`,
  IF((ISNULL(`cn`.`primer_apellido`) AND ISNULL(`cn`.`segundo_apellido`) AND ISNULL(`cn`.`primer_nombre`) AND ISNULL(`cn`.`segundo_nombre`)),'VACIO',UCASE(TRIM(CONCAT_WS(' ',`cn`.`primer_apellido`,`cn`.`segundo_apellido`,`cn`.`primer_nombre`,`cn`.`segundo_nombre`)))) AS `NOMBRE_CLIENTE`,
  `gcv`.`FECHA_GESTION`            AS `FECHA_GESTION`,
  `zr_tipologias`.`tipologia`      AS `CONCEPTO`,
  IF((`cn`.`anexo_preguntas_ppes` = 1),'SI','NO') AS `ANEXO_PPES`,
  'NO'                             AS `ANEXO_ACCIONISTAS`,
  IF((`zr`.`formulario` = 'Nuevo'),'SI','NO') AS `TIPO_FORMULARIO`
FROM ((((((`clientes`
        JOIN `tipos_documentos`
          ON ((`tipos_documentos`.`id` = `clientes`.`tipo_documento`)))
       JOIN `cliente_sarlaft_natural` `cn`
         ON ((`cn`.`cliente` = `clientes`.`id`)))
      JOIN `zr_radicacion` `zr`
        ON (((`zr`.`cliente_id` = `cn`.`cliente`)
             AND (`zr`.`created` = (SELECT
                                      MAX(`zr_radicacion`.`created`)
                                    FROM `zr_radicacion`
                                    WHERE ((`zr_radicacion`.`cliente_id` = `zr`.`cliente_id`)
                                           AND (`zr_radicacion`.`formulario_sarlaft` = 1)
                                           AND (`zr_radicacion`.`radicacion_proceso` = 'LEGAL')
                                           AND (`zr_radicacion`.`fecha_diligenciamiento` = (SELECT
                                                                                              MAX(`zr2`.`fecha_diligenciamiento`)
                                                                                            FROM `zr_radicacion` `zr2`
                                                                                            WHERE (`zr2`.`cliente_id` = `zr`.`cliente_id`)))))))))
     JOIN `linea_negocio`
       ON ((`linea_negocio`.`ID_LINEA` = `zr`.`linea_negocio_id`)))
    LEFT JOIN `gestion_clientes_completitud_verificacion` `gcv`
      ON (((`gcv`.`GESTION_CLIENTE_ID` = `clientes`.`id`)
           AND (`gcv`.`GESTION_FECHA_DILIGENCIAMIENTO` = `zr`.`fecha_diligenciamiento`))))
   LEFT JOIN `zr_tipologias`
     ON ((`zr_tipologias`.`id` = `gcv`.`GESTION_ESTADO_TIPOLOGIA_ID`)))
WHERE (`cn`.`anexo_preguntas_ppes` = 1)UNION ALL SELECT
                                                   CAST(`zr`.`created` AS DATE)      AS `FECHA_RADICACION`,
                                                   `linea_negocio`.`NOMBRE`          AS `LINEA_NEGOCIO`,
                                                   `zr`.`correo_radicacion`          AS `CORREO`,
                                                   `tipos_documentos`.`descripcion`  AS `CLIENTE_TIPO_DOCUMENTO_CODIGO`,
                                                   `clientes`.`documento`            AS `CLIENTE_DOCUMENTO`,
                                                   `cj`.`razon_social`               AS `NOMBRE_CLIENTE`,
                                                   `gcv`.`FECHA_GESTION`             AS `FECHA_GESTION`,
                                                   `zr_tipologias`.`tipologia`       AS `CONCEPTO`,
                                                   IF((`cj`.`anexo_preguntas_ppes` = 1),'SI','NO') AS `ANEXO_PPES`,
                                                   IF((`cj`.`anexo_accionistas` = 1),'SI','NO') AS `ANEXO_ACCIONISTAS`,
                                                   IF((`zr`.`formulario` = 'Nuevo'),'SI','NO') AS `FORMULARIO_NUEVO`
                                                 FROM ((((((`clientes`
                                                         JOIN `tipos_documentos`
                                                           ON ((`tipos_documentos`.`id` = `clientes`.`tipo_documento`)))
                                                        JOIN `cliente_sarlaft_juridico` `cj`
                                                          ON ((`cj`.`cliente` = `clientes`.`id`)))
                                                       JOIN `zr_radicacion` `zr`
                                                         ON (((`zr`.`cliente_id` = `cj`.`cliente`)
                                                              AND (`zr`.`created` = (SELECT
                                                                                       MAX(`zr_radicacion`.`created`)
                                                                                     FROM `zr_radicacion`
                                                                                     WHERE ((`zr_radicacion`.`cliente_id` = `zr`.`cliente_id`)
                                                                                            AND (`zr_radicacion`.`formulario_sarlaft` = 1)
                                                                                            AND (`zr_radicacion`.`radicacion_proceso` = 'LEGAL')
                                                                                            AND (`zr_radicacion`.`fecha_diligenciamiento` = (SELECT
                                                                                                                                               MAX(`zr2`.`fecha_diligenciamiento`)
                                                                                                                                             FROM `zr_radicacion` `zr2`
                                                                                                                                             WHERE (`zr2`.`cliente_id` = `zr`.`cliente_id`)))))))))
                                                      JOIN `linea_negocio`
                                                        ON ((`linea_negocio`.`ID_LINEA` = `zr`.`linea_negocio_id`)))
                                                     LEFT JOIN `gestion_clientes_completitud_verificacion` `gcv`
                                                       ON (((`gcv`.`GESTION_CLIENTE_ID` = `clientes`.`id`)
                                                            AND (`gcv`.`GESTION_FECHA_DILIGENCIAMIENTO` = `zr`.`fecha_diligenciamiento`))))
                                                    LEFT JOIN `zr_tipologias`
                                                      ON ((`zr_tipologias`.`id` = `gcv`.`GESTION_ESTADO_TIPOLOGIA_ID`)))
                                                 WHERE ((`cj`.`anexo_preguntas_ppes` = 1)
                                                         OR (`cj`.`anexo_accionistas` = 1))$$

DELIMITER ;