DELIMITER $$

ALTER ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `reporte_clientes_pendientes` AS 
SELECT DISTINCT
  `clientes`.`id`               AS `id`,
  `zr`.`created`                AS `FECHA_RADICACION`,
  `zr`.`fecha_diligenciamiento` AS `fecha_diligenciamiento`,
  `zr`.`correo_radicacion`      AS `CORREO`,
  `zr`.`fecha_envio_correo`     AS `FECHA_ENVIO_CORREO`,
  `td`.`codigo`                 AS `CLIENTE_TIPO_DOCUMENTO_CODIGO`,
  `clientes`.`documento`        AS `CLIENTE_DOCUMENTO`,
  `cj`.`razon_social`           AS `NOMBRE_CLIENTE`,
  `zr`.`correo_radicacion`      AS `CORREO_RADICACION`,
  IF((`cj`.`firma` = 1),'SI','NO') AS `FIRMA`,
  IF((`cj`.`huella` = 1),'SI','NO') AS `HUELLA`,
  IF((`cj`.`entrevista` = 1),'SI','NO') AS `ENTREVISTA`,
  `es`.`desc_type`              AS `ESTADO_PROCESO`,
  `zr`.`radicacion_proceso`     AS `PROCESO_RADICACION`,
  IF((`zepcs`.`ESTADO_PROCESO_ID` <> 12),NULL,'PENDIENTE SARLAFT') AS `DOCUMENTO_PENDIENTE_CODIGO`,
  `zr`.`radicacion_observacion` AS `OBSERVACION`
FROM (((((`clientes`
       JOIN `tipos_documentos` `td`
         ON ((`td`.`id` = `clientes`.`tipo_documento`)))
      JOIN `cliente_sarlaft_juridico` `cj`
        ON ((`cj`.`cliente` = `clientes`.`id`)))
     JOIN `zr_radicacion` `zr`
       ON (((`zr`.`cliente_id` = `cj`.`cliente`)
            AND (`zr`.`created` = (SELECT
                                     MAX(`zr2`.`created`)
                                   FROM `zr_radicacion` `zr2`
                                   WHERE ((`zr2`.`cliente_id` = `zr`.`cliente_id`)
                                          AND (`zr2`.`fecha_diligenciamiento` = (SELECT
                                                                                   MAX(`zr3`.`fecha_diligenciamiento`)
                                                                                 FROM `zr_radicacion` `zr3`
                                                                                 WHERE ((`zr3`.`cliente_id` = `zr2`.`cliente_id`)
                                                                                        AND (`zr3`.`formulario_sarlaft` = 1)
                                                                                        AND (`zr3`.`repetido` = 0)
                                                                                        AND (`zr3`.`radicacion_proceso` = 'LEGAL'))))))))))
    LEFT JOIN `zr_estado_proceso_clientes_sarlaft` `zepcs`
      ON (((`zepcs`.`PROCESO_CLIENTE_ID` = `zr`.`cliente_id`)
           AND (`zepcs`.`FECHA_PROCESO` = (SELECT
                                             MAX(`zepcs2`.`FECHA_PROCESO`)
                                           FROM `zr_estado_proceso_clientes_sarlaft` `zepcs2`
                                           WHERE ((`zepcs2`.`PROCESO_CLIENTE_ID` = `zepcs`.`PROCESO_CLIENTE_ID`)
                                                  AND (`zepcs2`.`PROCESO_FECHA_DILIGENCIAMIENTO` = `zr`.`fecha_diligenciamiento`)))))))
   LEFT JOIN `estados_sarlaft` `es`
     ON ((`es`.`id` = `zepcs`.`ESTADO_PROCESO_ID`)))
WHERE (`zepcs`.`ESTADO_PROCESO_ID` IN(10,11,12,15,16,2))UNION ALL SELECT DISTINCT
                                                                    `confianza`.`clientes`.`id`        AS `id`,
                                                                    `zr`.`created`                     AS `FECHA_RADICACION`,
                                                                    `zr`.`fecha_diligenciamiento`      AS `fecha_diligenciamiento`,
                                                                    `zr`.`correo_radicacion`           AS `CORREO`,
                                                                    `zr`.`fecha_envio_correo`          AS `FECHA_ENVIO_CORREO`,
                                                                    `td`.`codigo`                      AS `CLIENTE_TIPO_DOCUMENTO_CODIGO`,
                                                                    `confianza`.`clientes`.`documento` AS `CLIENTE_DOCUMENTO`,
                                                                    TRIM(CONCAT_WS(' ',`cn`.`primer_apellido`,`cn`.`segundo_apellido`,`cn`.`primer_nombre`,`cn`.`segundo_nombre`)) AS `NOMBRE_CLIENTE`,
                                                                    `zr`.`correo_radicacion`           AS `CORREO_RADICACION`,
                                                                    IF((`cn`.`firma` = 1),'SI','NO')   AS `FIRMA`,
                                                                    IF((`cn`.`huella` = 1),'SI','NO')  AS `HUELLA`,
                                                                    IF((`cn`.`entrevista` = 1),'SI','NO') AS `ENTREVISTA`,
                                                                    `es`.`desc_type`                   AS `ESTADO_PROCESO`,
                                                                    `zr`.`radicacion_proceso`          AS `PROCESO_RADICACION`,
                                                                    IF((`zepcs`.`ESTADO_PROCESO_ID` <> 12),NULL,'PENDIENTE SARLAFT') AS `DOCUMENTO_PENDIENTE_CODIGO`,
                                                                    `zr`.`radicacion_observacion`      AS `OBSERVACION`
                                                                  FROM (((((`clientes`
                                                                         JOIN `tipos_documentos` `td`
                                                                           ON ((`td`.`id` = `confianza`.`clientes`.`tipo_documento`)))
                                                                        JOIN `cliente_sarlaft_natural` `cn`
                                                                          ON ((`cn`.`cliente` = `confianza`.`clientes`.`id`)))
                                                                       JOIN `zr_radicacion` `zr`
                                                                         ON (((`zr`.`cliente_id` = `cn`.`cliente`)
                                                                              AND (`zr`.`created` = (SELECT
                                                                                                       MAX(`zr2`.`created`)
                                                                                                     FROM `zr_radicacion` `zr2`
                                                                                                     WHERE ((`zr2`.`cliente_id` = `zr`.`cliente_id`)
                                                                                                            AND (`zr2`.`fecha_diligenciamiento` = (SELECT
                                                                                                                                                     MAX(`zr3`.`fecha_diligenciamiento`)
                                                                                                                                                   FROM `zr_radicacion` `zr3`
                                                                                                                                                   WHERE ((`zr3`.`cliente_id` = `zr2`.`cliente_id`)
                                                                                                                                                          AND (`zr3`.`formulario_sarlaft` = 1)
                                                                                                                                                          AND (`zr3`.`repetido` = 0)
                                                                                                                                                          AND (`zr3`.`radicacion_proceso` = 'LEGAL'))))))))))
                                                                      LEFT JOIN `zr_estado_proceso_clientes_sarlaft` `zepcs`
                                                                        ON (((`zepcs`.`PROCESO_CLIENTE_ID` = `zr`.`cliente_id`)
                                                                             AND (`zepcs`.`FECHA_PROCESO` = (SELECT
                                                                                                               MAX(`zepcs2`.`FECHA_PROCESO`)
                                                                                                             FROM `zr_estado_proceso_clientes_sarlaft` `zepcs2`
                                                                                                             WHERE ((`zepcs2`.`PROCESO_CLIENTE_ID` = `zepcs`.`PROCESO_CLIENTE_ID`)
                                                                                                                    AND (`zepcs2`.`PROCESO_FECHA_DILIGENCIAMIENTO` = `zr`.`fecha_diligenciamiento`)))))))
                                                                     LEFT JOIN `estados_sarlaft` `es`
                                                                       ON ((`es`.`id` = `zepcs`.`ESTADO_PROCESO_ID`)))
                                                                  WHERE (`zepcs`.`ESTADO_PROCESO_ID` IN(10,11,12,15,16,2))$$

DELIMITER ;