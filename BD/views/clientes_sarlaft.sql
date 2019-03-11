DELIMITER $$

ALTER ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `clientes_sarlaft` AS 
SELECT
  `clientes`.`documento`        AS `documento`,
  `clientes`.`id`               AS `cliente_id`,
  `clientes`.`tipo_documento`   AS `tipo_documento`,
  `td`.`tipo_persona`           AS `TIPO_PERSONA`,
  `cn`.`id`                     AS `formulario_id`,
  `zr`.`fecha_diligenciamiento` AS `fecha_diligenciamiento`,
  CONCAT_WS('-',CONVERT(CONCAT(DATE_FORMAT(CAST(`zr`.`created` AS DATE),'%Y%m%d'),`zr`.`cliente_id`) USING utf8mb4),`zr`.`consecutivo`) AS `numero_radicado`,
  `zr`.`numero_planilla`        AS `numero_planilla`,
  IF((ISNULL(`cn`.`primer_apellido`) AND ISNULL(`cn`.`segundo_apellido`) AND ISNULL(`cn`.`primer_nombre`) AND ISNULL(`cn`.`segundo_nombre`)),'VACIO',UCASE(TRIM(CONCAT_WS(' ',`cn`.`primer_apellido`,`cn`.`segundo_apellido`,`cn`.`primer_nombre`,`cn`.`segundo_nombre`)))) AS `NOMBRE_CLIENTE`,
  `zepcs`.`ESTADO_PROCESO_ID`   AS `estado_formulario_id`,
  `estados_sarlaft`.`desc_type` AS `ESTADO_FORM`,
  `zepcs`.`PROCESO_ACTIVO`      AS `PROCESO_ACTIVO`
FROM (((((`clientes`
       JOIN `tipos_documentos` `td`
         ON ((`td`.`id` = `clientes`.`tipo_documento`)))
      JOIN `zr_radicacion` `zr`
        ON (((`zr`.`cliente_id` = `clientes`.`id`)
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
     JOIN `zr_estado_proceso_clientes_sarlaft` `zepcs`
       ON (((`zepcs`.`PROCESO_CLIENTE_ID` = `zr`.`cliente_id`)
            AND (`zepcs`.`FECHA_PROCESO` = (SELECT
                                              MAX(`zr_estado_proceso_clientes_sarlaft`.`FECHA_PROCESO`)
                                            FROM `zr_estado_proceso_clientes_sarlaft`
                                            WHERE (`zr_estado_proceso_clientes_sarlaft`.`PROCESO_CLIENTE_ID` = `zepcs`.`PROCESO_CLIENTE_ID`))))))
    JOIN `estados_sarlaft`
      ON ((`estados_sarlaft`.`id` = `zepcs`.`ESTADO_PROCESO_ID`)))
   LEFT JOIN `cliente_sarlaft_natural` `cn`
     ON ((`cn`.`cliente` = `clientes`.`id`)))
WHERE (`td`.`tipo_persona` = 'NAT')
GROUP BY `clientes`.`documento` UNION ALL SELECT
                                            `clientes`.`documento`         AS `documento`,
                                            `clientes`.`id`                AS `id`,
                                            `clientes`.`tipo_documento`    AS `tipo_documento`,
                                            `td`.`tipo_persona`            AS `TIPO_PERSONA`,
                                            `cj`.`id`                      AS `id`,
                                            `zr`.`fecha_diligenciamiento`  AS `fecha_diligenciamiento`,
                                            CONCAT_WS('-',CONVERT(CONCAT(DATE_FORMAT(CAST(`zr`.`created` AS DATE),'%Y%m%d'),`zr`.`cliente_id`) USING utf8mb4),`zr`.`consecutivo`) AS `Name_exp_7`,
                                            `zr`.`numero_planilla`         AS `numero_planilla`,
                                            UCASE(IFNULL(`cj`.`razon_social`,'VACIO')) AS `NOMBRE_CLIENTE`,
                                            `zepcs`.`ESTADO_PROCESO_ID`    AS `ESTADO_PROCESO_ID`,
                                            `estados_sarlaft`.`desc_type`  AS `desc_type`,
                                            `zepcs`.`PROCESO_ACTIVO`       AS `PROCESO_ACTIVO`
                                          FROM (((((`clientes`
                                                 JOIN `tipos_documentos` `td`
                                                   ON ((`td`.`id` = `clientes`.`tipo_documento`)))
                                                JOIN `zr_radicacion` `zr`
                                                  ON (((`zr`.`cliente_id` = `clientes`.`id`)
                                                       AND (`zr`.`formulario_sarlaft` = 1)
                                                       AND (`zr`.`radicacion_proceso` = 'LEGAL')
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
                                               JOIN `zr_estado_proceso_clientes_sarlaft` `zepcs`
                                                 ON (((`zepcs`.`PROCESO_CLIENTE_ID` = `zr`.`cliente_id`)
                                                      AND (`zepcs`.`FECHA_PROCESO` = (SELECT
                                                                                        MAX(`zr_estado_proceso_clientes_sarlaft`.`FECHA_PROCESO`)
                                                                                      FROM `zr_estado_proceso_clientes_sarlaft`
                                                                                      WHERE (`zr_estado_proceso_clientes_sarlaft`.`PROCESO_CLIENTE_ID` = `zepcs`.`PROCESO_CLIENTE_ID`))))))
                                              JOIN `estados_sarlaft`
                                                ON ((`estados_sarlaft`.`id` = `zepcs`.`ESTADO_PROCESO_ID`)))
                                             LEFT JOIN `cliente_sarlaft_juridico` `cj`
                                               ON ((`cj`.`cliente` = `clientes`.`id`)))
                                          WHERE (`td`.`tipo_persona` = 'JUR')
                                          GROUP BY `clientes`.`documento`$$

DELIMITER ;