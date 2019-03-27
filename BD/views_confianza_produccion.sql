USE `confianza`;

DROP VIEW IF EXISTS `clientes_sarlaft`;

DELIMITER $$

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `clientes_sarlaft` AS 
SELECT
  `clientes`.`documento`        AS `documento`,
  `clientes`.`id`               AS `cliente_id`,
  `clientes`.`tipo_documento`   AS `tipo_documento`,
  `td`.`tipo_persona`           AS `TIPO_PERSONA`,
  `cn`.`id`                     AS `formulario_id`,
  `zr`.`fecha_diligenciamiento` AS `fecha_diligenciamiento`,
  CONCAT_WS('-',CONCAT(DATE_FORMAT(CAST(`zr`.`created` AS DATE),'%Y%m%d'),`zr`.`cliente_id`),`zr`.`consecutivo`) AS `numero_radicado`,
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
                                            CONCAT_WS('-',CONCAT(DATE_FORMAT(CAST(`zr`.`created` AS DATE),'%Y%m%d'),`zr`.`cliente_id`),`zr`.`consecutivo`) AS `Name_exp_7`,
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

DROP VIEW IF EXISTS `reporte_cargue_clientes_juridicos`;

DELIMITER $$

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `reporte_cargue_clientes_juridicos` AS 
SELECT
  `clientes`.`id`                                AS `CLIENTE_ID`,
  `zr`.`created`                                 AS `FECHA_RADICACION`,
  UCASE(`users`.`nombre`)                        AS `USUARIO_RADICAICON`,
  `gcc`.`FECHA_GESTION`                          AS `FECHA_CAPTURA`,
  (SELECT
     MAX(`gcv`.`FECHA_GESTION`)
   FROM `gestion_clientes_completitud_verificacion` `gcv`
   WHERE ((`gcv`.`GESTION_CLIENTE_ID` = `zr`.`cliente_id`)
          AND (`gcv`.`GESTION_FECHA_DILIGENCIAMIENTO` = `zr`.`fecha_diligenciamiento`)
          AND (`gcv`.`GESTION_PROCESO_ID` = 5)
          AND (`gcv`.`FECHA_GESTION` > `gcc`.`FECHA_GESTION`))) AS `FECHA_ACTUALIZACION`,
  `zr`.`fecha_diligenciamiento`                  AS `FECHA_DILIGENCIAMIENTO`,
  NULL                                           AS `COD_ASEG`,
  `clientes`.`tipo_documento`                    AS `TIPO_DOCUMENTO`,
  UCASE(TRIM(`cj`.`razon_social`))               AS `NOMBRE_TOMADOR`,
  `clientes`.`documento`                         AS `IDENTIFICACION_TOMADOR`,
  UCASE(`cj`.`rep_legal_primer_apellido`)        AS `REPRESENTANTE_LEGAL_PRIMER_APELLIDO`,
  UCASE(`cj`.`rep_legal_segundo_apellido`)       AS `REPRESENTANTE_LEGAL_SEGUNDO_APELLIDO`,
  UCASE(`cj`.`rep_legal_nombres`)                AS `REPRESENTANTE_LEGAL_NOMBRES`,
  `cj`.`rep_legal_tipo_documento`                AS `REPRESENTANTE_TIPO_DOCUMENTO`,
  `cj`.`rep_legal_documento`                     AS `REPRESENTANTE_DOCUMENTO`,
  `cj`.`rep_legal_direccion_residencia`          AS `REPRESENTANTE_DIRECCION_RESIDENCIA`,
  (SELECT
     UCASE(`ciudades`.`nombre_ciudad`)
   FROM `ciudades`
   WHERE (`ciudades`.`id` = `cj`.`rep_legal_ciudad_residencia`)) AS `REPRESENTANTE_CIUDAD_RESIDENCIA`,
  `cj`.`rep_legal_telefono_residencia`           AS `REPRESENTANTE_TELEFONO_RESIDENCIA`,
  `cj`.`sucursal_direccion`                      AS `SUCURSAL_DIRECCION`,
  (SELECT
     UCASE(`ciudades`.`nombre_ciudad`)
   FROM `ciudades`
   WHERE (`ciudades`.`id` = `cj`.`sucursal_ciudad`)) AS `SUCURSAL_CIUDAD`,
  `cj`.`sucursal_telefono`                       AS `SUCURSAL_TELEFONO`,
  `cj`.`sucursal_fax`                            AS `SUCURSAL_FAX`,
  `cj`.`rep_legal_celular_residencia`            AS `CELULAR`,
  `cj`.`rep_legal_telefono_residencia`           AS `TELEFONO`,
  `cj`.`ofi_principal_tipo_empresa`              AS `TIPO_EMPRESA`,
  `cj`.`ofi_principal_ciiu_cod`                  AS `CIIU_COD`,
  (SELECT
     UCASE(`tap`.`nombre_actividad_principal`)
   FROM `tipos_actividades_principales` `tap`
   WHERE (`tap`.`id` = `cj`.`ofi_principal_ciiu_cod`)) AS `ACTIVIDAD_ECONOMICA`,
  `cj`.`ingresos`                                AS `INGRESOS`,
  `cj`.`egresos`                                 AS `EGRESOS`,
  `cj`.`activos`                                 AS `ACTIVOS`,
  `cj`.`pasivos`                                 AS `PASIVOS`,
  `cj`.`patrimonio`                              AS `PATRIMONIO`,
  `cj`.`otros_ingresos`                          AS `OTROS_INGRESOS`,
  `cj`.`desc_otros_ingresos`                     AS `CONCEPTO_OTROS_INGRESOS`,
  IF((`cj`.`operaciones_moneda_extranjera` = 1),'SI','NO') AS `TRANSACCIONES_MONEDA_EXTRANJERA`,
  (SELECT
     `tipos_operaciones_moneda_extranjera`.`desc_operacion`
   FROM `tipos_operaciones_moneda_extranjera`
   WHERE (`tipos_operaciones_moneda_extranjera`.`id` = `cj`.`tipo_operaciones_moneda_extranjera`)) AS `TRANSACCIONES_MONEDA_EXTRANJERA_CUAL`,
  `cj`.`tipo_operaciones_moneda_extranjera_otro` AS `TRANSACCIONES_MONEDA_EXTRANJERA_OTRAS`,
  `cj`.`declaracion_origen_fondos`               AS `DECLARACION_ORIGEN_FONDOS`,
  (SELECT
     UCASE(`ciudades`.`nombre_ciudad`)
   FROM `ciudades`
   WHERE (`ciudades`.`id` = `cj`.`ciudad_diligenciamiento`)) AS `CIUDAD_DILIGENCIAMIENTO`,
  `cj`.`sucursal`                                AS `SUCURSAL`
FROM (((((`clientes`
       JOIN `tipos_documentos` `td`
         ON ((`td`.`id` = `clientes`.`tipo_documento`)))
      JOIN `zr_radicacion` `zr`
        ON (((`zr`.`cliente_id` = `clientes`.`id`)
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
     JOIN `cliente_sarlaft_juridico` `cj`
       ON ((`cj`.`cliente` = `zr`.`cliente_id`)))
    JOIN `users`
      ON ((`users`.`id` = `zr`.`funcionario_id`)))
   JOIN `gestion_clientes_captura` `gcc`
     ON (((`zr`.`cliente_id` = `gcc`.`GESTION_CLIENTE_ID`)
          AND (`gcc`.`FECHA_GESTION` = (SELECT
                                          MAX(`gestion_clientes_captura`.`FECHA_GESTION`)
                                        FROM `gestion_clientes_captura`
                                        WHERE ((`gestion_clientes_captura`.`GESTION_CLIENTE_ID` = `gcc`.`GESTION_CLIENTE_ID`)
                                               AND (`gcc`.`GESTION_FECHA_DILIGENCIAMIENTO` = `zr`.`fecha_diligenciamiento`)))))))
WHERE (`cj`.`razon_social` IS NOT NULL)$$

DELIMITER ;

DROP VIEW IF EXISTS `reporte_cargue_clientes_naturales`;

DELIMITER $$

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `reporte_cargue_clientes_naturales` AS 
SELECT DISTINCT
  `clientes`.`id`                  AS `CLIENTE_ID`,
  `zr`.`created`                   AS `FECHA_RADICACION`,
  UCASE(`users`.`nombre`)          AS `USUARIO_RADICAICON`,
  `gcc`.`FECHA_GESTION`            AS `FECHA_CAPTURA`,
  (SELECT
     MAX(`gcv`.`FECHA_GESTION`)
   FROM `gestion_clientes_completitud_verificacion` `gcv`
   WHERE ((`gcv`.`GESTION_CLIENTE_ID` = `zr`.`cliente_id`)
          AND (`gcv`.`GESTION_FECHA_DILIGENCIAMIENTO` = `zr`.`fecha_diligenciamiento`)
          AND (`gcv`.`GESTION_PROCESO_ID` = 5)
          AND (`gcv`.`FECHA_GESTION` > `gcc`.`FECHA_GESTION`))) AS `FECHA_VERIFICACION`,
  `zr`.`fecha_diligenciamiento`    AS `FECHA_DILIGENCIAMIENTO`,
  NULL                             AS `COD_ASEG`,
  UCASE(TRIM(`cn`.`primer_apellido`)) AS `PRIMER_APELLIDO`,
  UCASE(TRIM(`cn`.`segundo_apellido`)) AS `SEGUNDO_APELLIDO`,
  UCASE(TRIM(CONCAT_WS(' ',`cn`.`primer_nombre`,`cn`.`segundo_nombre`))) AS `NOMBRES`,
  `clientes`.`tipo_documento`      AS `TIPO_DOCUMENTO`,
  `clientes`.`documento`           AS `NUMERO_DOCUMENTO`,
  UCASE(`cn`.`lugar_nacimiento`)   AS `LUGAR_NACIMIENTO`,
  UCASE(`cn`.`fecha_nacimiento`)   AS `FECHA_NACIMIENTO`,
  UCASE(`cn`.`ocupacion`)          AS `OCUPACION_1`,
  NULL                             AS `OCUPACION_2`,
  IFNULL(`cn`.`ciiu_cod`,`cn`.`actividad_eco_principal`) AS `CIIU_COD`,
  (SELECT
     UCASE(`tipos_actividades`.`nombre_tipo_actividad`)
   FROM `tipos_actividades`
   WHERE (`tipos_actividades`.`id` = `cn`.`tipo_actividad`)) AS `TIPO_ACTIVIDAD`,
  UCASE(`cn`.`empresa_donde_trabaja`) AS `EMPRESA_DONDE_TRABAJA`,
  UCASE(`cn`.`cargo`)              AS `CARGO`,
  (SELECT
     UCASE(`ciudades`.`nombre_ciudad`)
   FROM `ciudades`
   WHERE (`ciudades`.`id` = `cn`.`ciudad_empresa`)) AS `CIUDAD_EMPRESA`,
  `cn`.`direccion_empresa`         AS `DIRECCION_EMPRESA`,
  `cn`.`telefono_empresa`          AS `TELEFONO_EMPRESA`,
  `cn`.`direccion_residencia`      AS `DIRECCION_RESIDENCIA`,
  (SELECT
     UCASE(`ciudades`.`nombre_ciudad`)
   FROM `ciudades`
   WHERE (`ciudades`.`id` = `cn`.`ciudad_residencia`)) AS `CIUDAD_RESIDENCIA`,
  (SELECT
     UCASE(`departamentos`.`nombre_departamento`)
   FROM `departamentos`
   WHERE (`departamentos`.`id` = `cn`.`departamento_residencia`)) AS `DEPARTAMENTO_RESIDENCIA`,
  `cn`.`telefono`                  AS `TELEFONO_RESIDENCIA`,
  `cn`.`celular`                   AS `CELULAR`,
  `cn`.`ingresos`                  AS `INGRESOS`,
  `cn`.`egresos`                   AS `EGRESOS`,
  `cn`.`activos`                   AS `ACTIVOS`,
  `cn`.`pasivos`                   AS `PASIVOS`,
  `cn`.`patrimonio`                AS `PATRIMONIO`,
  `cn`.`otros_ingresos`            AS `OTROS_INGRESOS`,
  `cn`.`desc_otros_ingresos`       AS `CONCEPTO_OTROS_INGRESOS`,
  IF((`cn`.`operaciones_moneda_extranjera` = 1),'SI','NO') AS `TRANSACCIONES_MONEDA_EXTRANJERA`,
  (SELECT
     UCASE(`tipos_operaciones_moneda_extranjera`.`desc_operacion`)
   FROM `tipos_operaciones_moneda_extranjera`
   WHERE (`tipos_operaciones_moneda_extranjera`.`id` = `cn`.`tipo_operaciones_moneda_extranjera`)) AS `TRANSACCIONES_MONEDA_EXTRANJERA_CUAL`,
  `cn`.`desc_operacion_mon_extr`   AS `TRANSACCIONES_MONEDA_EXTRANJERA_OTRAS`,
  `cn`.`declaracion_origen_fondos` AS `DECLARACION_ORIGEN_FONDOS`,
  (SELECT
     UCASE(`ciudades`.`nombre_ciudad`)
   FROM `ciudades`
   WHERE (`ciudades`.`id` = `cn`.`ciudad_diligenciamiento`)) AS `CIUDAD_DILIGENCIAMIENTO`,
  `cn`.`sucursal`                  AS `SUCURSAL`
FROM (((((`clientes`
       JOIN `tipos_documentos` `td`
         ON ((`td`.`id` = `clientes`.`tipo_documento`)))
      JOIN `zr_radicacion` `zr`
        ON (((`zr`.`cliente_id` = `clientes`.`id`)
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
     JOIN `cliente_sarlaft_natural` `cn`
       ON ((`cn`.`cliente` = `zr`.`cliente_id`)))
    JOIN `users`
      ON ((`users`.`id` = `zr`.`funcionario_id`)))
   JOIN `gestion_clientes_captura` `gcc`
     ON (((`zr`.`cliente_id` = `gcc`.`GESTION_CLIENTE_ID`)
          AND (`gcc`.`FECHA_GESTION` = (SELECT
                                          MAX(`gestion_clientes_captura`.`FECHA_GESTION`)
                                        FROM `gestion_clientes_captura`
                                        WHERE ((`gestion_clientes_captura`.`GESTION_CLIENTE_ID` = `gcc`.`GESTION_CLIENTE_ID`)
                                               AND (`gcc`.`GESTION_FECHA_DILIGENCIAMIENTO` = `zr`.`fecha_diligenciamiento`)))))))
WHERE ((`cn`.`primer_apellido` IS NOT NULL)
        OR (`cn`.`segundo_apellido` IS NOT NULL)
        OR (`cn`.`primer_nombre` IS NOT NULL)
        OR (`cn`.`segundo_nombre` IS NOT NULL))
GROUP BY `clientes`.`documento`$$

DELIMITER ;

DROP VIEW IF EXISTS `reporte_clientes_beneficiario_final_peps`;

DELIMITER $$

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `reporte_clientes_beneficiario_final_peps` AS 
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


DROP VIEW IF EXISTS `reporte_clientes_capturados`;

DELIMITER $$

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `reporte_clientes_capturados` AS 
SELECT `zr`.`created` AS `FECHA_RADICACION`,`users`.`nombre` AS `USUARIO_ASISTEMYCA`,(SELECT UCASE(`users`.`nombre`) FROM `users` WHERE (`users`.`id` = `gcc`.`GESTION_USUARIO_ID`)) AS `USUARIO_CAPTURA`,`gcc`.`FECHA_GESTION` AS `FECHA_CAPTURA`,`es`.`desc_type` AS `PROCESO`,UCASE(TRIM(CONCAT_WS(' ',`cn`.`primer_apellido`,`cn`.`segundo_apellido`,`cn`.`primer_nombre`,`cn`.`segundo_nombre`))) AS `NOMBRE_TOMADOR`,`td`.`tipo_persona` AS `TIPO_PERSONA`,`clientes`.`id` AS `CLIENTE_ID`,`clientes`.`documento` AS `IDENTIFICACION_TOMADOR`,IF((`zr`.`fecha_diligenciamiento` = '0000-00-00'),NULL,`zr`.`fecha_diligenciamiento`) AS `FECHA_DILIGENCIAMIENTO`,`cn`.`tipo_solicitud` AS `TIPO_SOLICITUD`,`cn`.`clase_vinculacion` AS `CLASE_VINCULACION`,`cn`.`clase_vinculacion_otro` AS `CLASE_VINCULACION_OTRO`,`cn`.`relacion_tom_asegurado` AS `TOMADOR_ASEGURADO`,`cn`.`relacion_tom_asegurado_otra` AS `TOMADOR_ASEGURADO_OTRO`,`cn`.`relacion_tom_beneficiario` AS `TOMADOR_BENEFICIARIO`,`cn`.`relacion_tom_beneficiario_otra` AS `TOMADOR_BENEFICIARIO_OTRO`,`cn`.`relacion_aseg_beneficiario` AS `ASEGURADO_BENEFICIARIO`,`cn`.`relacion_aseg_beneficiario_otra` AS `ASEGURADO_BENEFICIARIO_OTRO`,UCASE(`cn`.`primer_apellido`) AS `PRIMER_APELLIDO`,UCASE(`cn`.`segundo_apellido`) AS `SEGUNDO_APELLIDO`,UCASE(TRIM(CONCAT_WS(' ',`cn`.`primer_nombre`,`cn`.`segundo_nombre`))) AS `NOMBRES`,NULL AS `NOMBRE_O_RAZON_SOCIAL`,`td`.`codigo` AS `TIPO_DOCUMENTO`,`td`.`id` AS `COD_DOCUMENTO`,NULL AS `TIPO_SOCIEDAD`,`cn`.`lugar_expedicion_documento` AS `LUGAR_EXPEDICION`,`cn`.`sexo` AS `SEXO`,(SELECT UCASE(`estados_civiles`.`desc_estado_civil`) FROM `estados_civiles` WHERE (`estados_civiles`.`id` = `cn`.`estado_civil`)) AS `ESTADO_CIVIL`,`cn`.`fecha_expedicion_documento` AS `FECHA_EXPEDICION_DOCUMENTO_PN`,UCASE(`cn`.`fecha_nacimiento`) AS `FECHA_NACIMIENTO_PN`,UCASE(`cn`.`lugar_nacimiento`) AS `LUGAR_NACIMIENTO_PN`,(SELECT UCASE(`paises`.`nombre_pais`) FROM `paises` WHERE (`paises`.`id` = `cn`.`nacionalidad_1`)) AS `NACIONALIDAD_1_PN`,(SELECT UCASE(`paises`.`nombre_pais`) FROM `paises` WHERE (`paises`.`id` = `cn`.`nacionalidad_2`)) AS `NACIONALIDAD_2_PN`,`cn`.`direccion_residencia` AS `DIRECCION_OFICINA_PRINCIPAL_RESIDENCIA`,NULL AS `TIPO_EMPRESA`,(SELECT `tipos_actividades_principales`.`nombre_actividad_principal` FROM `tipos_actividades_principales` WHERE (`tipos_actividades_principales`.`id` = IFNULL(`cn`.`ciiu_cod`,`cn`.`actividad_eco_principal`))) AS `CIIU_ACTIVIDAD_ECONOMICA`,UCASE(`cn`.`actividad_eco_principal_otra`) AS `CIIU_ACTIVIDAD_ECONOMICA_OTRA`,`cn`.`ciiu_cod` AS `CIIU_COD`,(SELECT UCASE(`tap`.`nombre_actividad_principal`) FROM `tipos_actividades_principales` `tap` WHERE (`tap`.`id` = `cn`.`ciiu_cod`)) AS `ACTIVIDAD_ECONOMICA`,(SELECT UCASE(`sector`.`desc_sector`) FROM `sector` WHERE (`sector`.`id` = `cn`.`sector`)) AS `SECTOR`,NULL AS `BREVE_DESCRIPCION`,(SELECT UCASE(`departamentos`.`nombre_departamento`) FROM `departamentos` WHERE (`departamentos`.`id` = `cn`.`departamento_residencia`)) AS `DEPARTAMENTO_OFICINA_RESIDENCIA`,(SELECT UCASE(`ciudades`.`nombre_ciudad`) FROM `ciudades` WHERE (`ciudades`.`id` = `cn`.`ciudad_residencia`)) AS `CIUDAD_OFICINA_RESIDENCIA`,`cn`.`telefono` AS `TELEFONO_OFICINA_RESIDENCIA`,`cn`.`celular` AS `CELULAR`,NULL AS `PAGINA_WEB`,`cn`.`correo_electronico` AS `CORREO_ELECTRONICO`,NULL AS `SUCURSAL_DEPARATAMENTO`,NULL AS `SUCURSAL_CIUDAD`,NULL AS `SUCURSAL_DIRECCION`,NULL AS `SUCURSAL_TELEFONO`,NULL AS `SUCURSAL_FAX`,UCASE(`cn`.`ocupacion`) AS `OCUPACION`,(SELECT UCASE(`tipos_actividades`.`nombre_tipo_actividad`) FROM `tipos_actividades` WHERE (`tipos_actividades`.`id` = `cn`.`tipo_actividad`)) AS `TIPO_ACTIVIDAD`,UCASE(`cn`.`cargo`) AS `CARGO`,UCASE(`cn`.`empresa_donde_trabaja`) AS `EMPRESA_DONDE_TRABAJA_PN`,`cn`.`direccion_empresa` AS `DIRECCION_EMPRESA_PN`,(SELECT UCASE(`ciudades`.`nombre_ciudad`) FROM `ciudades` WHERE (`ciudades`.`id` = `cn`.`ciudad_empresa`)) AS `CIUDAD_EMPRESA_PN`,(SELECT UCASE(`departamentos`.`nombre_departamento`) FROM `departamentos` WHERE (`departamentos`.`id` = `cn`.`departamento_empresa`)) AS `DEPARTAMENTO_EMPRESA_PN`,`cn`.`telefono_empresa` AS `TELEFONO_EMPRESA_PN`,(SELECT `tipos_actividades_principales`.`nombre_actividad_principal` FROM `tipos_actividades_principales` WHERE (`tipos_actividades_principales`.`id` = `cn`.`actividad_secundaria`)) AS `ACTIVIDAD_SEVUNDARIA_PN`,`cn`.`ciiu_secundario` AS `CIIU_SECUNDARIA_PN`,NULL AS `REPRESENTANTE_LEGAL_PRIMER_APELLIDO`,NULL AS `REPRESENTANTE_LEGAL_SEGUNDO_APELLIDO`,NULL AS `REPRESENTANTE_LEGAL_NOMBRES`,NULL AS `REPRESENTANTE_TIPO_DOCUMENTO`,NULL AS `REPRESENTANTE_COD_DOCUMENTO`,NULL AS `REPRESENTANTE_DOCUMENTO`,NULL AS `REPRESENTANTE_FECHA_EXPEDICION`,NULL AS `REPRESENTANTE_LUGAR_EXPEDICION`,NULL AS `REPRESENTANTE_FECHA_NACIMIENTO`,NULL AS `REPRESENTANTE_LUGAR_NACIMIENTO`,NULL AS `REPRESENTANTE_NACIONALIDAD_1`,NULL AS `REPRESENTANTE_NACIONALIDAD_2`,NULL AS `REPRESENTANTE_EMAIL`,NULL AS `REPRESENTANTE_DIRECCION_RESIDENCIA`,NULL AS `REPRESENTANTE_CIUDAD_RESIDENCIA`,NULL AS `REPRESENTANTE_DEPARTAMENTO_RESIDENCIA`,NULL AS `REPRESENTANTE_PAIS_RESIDENCIA`,NULL AS `REPRESENTANTE_TELEFONO_RESIDENCIA`,NULL AS `REPRESENTANTE_CELULAR_RESIDENCIA`,NULL AS `REPRESENTANTE_PERSONA_PUBLICA`,NULL AS `REPRESENTANTE_RECURSOS_PUBLICOS`,NULL AS `REPRESENTANTE_OBLIGACIONES_TRIBUTARIAS`,NULL AS `REPRESENTANTE_OBLIGACIONES_TRIBUTARIAS_CUAL`,IF((`cn`.`persona_publica` = 1),'SI','NO') AS `PERSONA_PUBLICA_PN`,IF((`cn`.`vinculo_persona_publica` = 1),'SI','NO') AS `VINCULO_PERSONA_PUBLICA_PN`,IF((`cn`.`productos_publicos` = 1),'SI','NO') AS `RECURSOS_PUBLICOS_PN`,`cn`.`ingresos` AS `INGRESOS`,`cn`.`egresos` AS `EGRESOS`,`cn`.`activos` AS `ACTIVOS`,`cn`.`pasivos` AS `PASIVOS`,`cn`.`patrimonio` AS `PATRIMONIO`,`cn`.`otros_ingresos` AS `OTROS_INGRESOS`,`cn`.`desc_otros_ingresos` AS `CONCEPTO_OTROS_INGRESOS`,`cn`.`declaracion_origen_fondos` AS `DECLARACION_ORIGEN_FONDOS`,IF((`cn`.`operaciones_moneda_extranjera` = 1),'SI','NO') AS `TRANSACCIONES_MONEDA_EXTRANJERA`,(SELECT UCASE(`tipos_operaciones_moneda_extranjera`.`desc_operacion`) FROM `tipos_operaciones_moneda_extranjera` WHERE (`tipos_operaciones_moneda_extranjera`.`id` = `cn`.`tipo_operaciones_moneda_extranjera`)) AS `TRANSACCIONES_MONEDA_EXTRANJERA_CUAL`,`cn`.`desc_operacion_mon_extr` AS `TRANSACCIONES_MONEDA_EXTRANJERA_OTRAS`,IF((`cn`.`productos_exterior` = 1),'SI','NO') AS `PRODUCTOS_EXTERIOR`,IF((`cn`.`cuentas_moneda_exterior` = 1),'SI','NO') AS `CUENTAS_MONEDA_EXTRANJERA`,IF((`cn`.`reclamaciones` = 1),'SI','NO') AS `RECLAMACIONES`,`cn`.`reclamacion_anio` AS `RECLAMACION_ANIO`,`cn`.`reclamacion_ramo` AS `RECLAMACION_RAMO`,`cn`.`reclamacion_compania` AS `RECLAMACION_COMPANIA`,`cn`.`reclamacion_valor` AS `RECLAMACION_VALOR`,`cn`.`reclamacion_resultado` AS `RECLAMACION_RESULTADO`,`cn`.`reclamacion_anio_2` AS `RECLAMACION_ANIO_2`,`cn`.`reclamacion_ramo_2` AS `RECLAMACION_RAMO_2`,`cn`.`reclamacion_compania_2` AS `RECLAMACION_COMPANIA_2`,`cn`.`reclamacion_valor_2` AS `RECLAMACION_VALOR_2`,`cn`.`reclamacion_resultado_2` AS `RECLAMACION_RESULTADO_2`,(SELECT UCASE(`ciudades`.`nombre_ciudad`) FROM `ciudades` WHERE (`ciudades`.`id` = `cn`.`ciudad_diligenciamiento`)) AS `CIUDAD_DILIGENCIAMIENTO`,`cn`.`sucursal` AS `SUCURSAL` FROM (((((((`clientes` JOIN `tipos_documentos` `td` ON((`td`.`id` = `clientes`.`tipo_documento`))) JOIN `cliente_sarlaft_natural` `cn` ON((`cn`.`cliente` = `clientes`.`id`))) JOIN `zr_radicacion` `zr` ON(((`zr`.`cliente_id` = `clientes`.`id`) AND (`zr`.`created` = (SELECT MAX(`zr2`.`created`) FROM `zr_radicacion` `zr2` WHERE ((`zr2`.`cliente_id` = `zr`.`cliente_id`) AND (`zr2`.`fecha_diligenciamiento` = (SELECT MAX(`zr3`.`fecha_diligenciamiento`) FROM `zr_radicacion` `zr3` WHERE ((`zr3`.`cliente_id` = `zr2`.`cliente_id`) AND (`zr3`.`formulario_sarlaft` = 1) AND (`zr3`.`repetido` = 0) AND (`zr3`.`radicacion_proceso` = 'LEGAL')))))))))) JOIN `zr_estado_proceso_clientes_sarlaft` `zepcs` ON(((`zepcs`.`PROCESO_CLIENTE_ID` = `cn`.`cliente`) AND (`zepcs`.`ESTADO_PROCESO_ID` NOT IN (2,12)) AND (`zepcs`.`FECHA_PROCESO` = (SELECT MAX(`zepcs2`.`FECHA_PROCESO`) FROM `zr_estado_proceso_clientes_sarlaft` `zepcs2` WHERE ((`zepcs2`.`PROCESO_CLIENTE_ID` = `zepcs`.`PROCESO_CLIENTE_ID`) AND (`zepcs2`.`PROCESO_FECHA_DILIGENCIAMIENTO` = `zr`.`fecha_diligenciamiento`))))))) JOIN `users` ON((`users`.`id` = `zr`.`funcionario_id`))) JOIN `estados_sarlaft` `es` ON((`es`.`id` = `zepcs`.`ESTADO_PROCESO_ID`))) JOIN `gestion_clientes_captura` `gcc` ON(((`zr`.`cliente_id` = `gcc`.`GESTION_CLIENTE_ID`) AND (`gcc`.`FECHA_GESTION` = (SELECT MAX(`gestion_clientes_captura`.`FECHA_GESTION`) FROM `gestion_clientes_captura` WHERE ((`gestion_clientes_captura`.`GESTION_CLIENTE_ID` = `gcc`.`GESTION_CLIENTE_ID`) AND (`gcc`.`GESTION_FECHA_DILIGENCIAMIENTO` = `zr`.`fecha_diligenciamiento`))))))) WHERE ((`cn`.`primer_apellido` IS NOT NULL) OR (`cn`.`segundo_apellido` IS NOT NULL) OR (`cn`.`primer_nombre` IS NOT NULL) OR (`cn`.`segundo_nombre` IS NOT NULL)) GROUP BY `clientes`.`documento` UNION ALL SELECT `zr`.`created` AS `FECHA_RADICACION`,`users`.`nombre` AS `USUARIO_ASISTEMYCA`,(SELECT UCASE(`users`.`nombre`) FROM `users` WHERE (`users`.`id` = `gcc`.`GESTION_USUARIO_ID`)) AS `USUARIO_CAPTURA`,`gcc`.`FECHA_GESTION` AS `FECHA_CAPTURA`,`es`.`desc_type` AS `PROCESO`,UCASE(TRIM(`cj`.`razon_social`)) AS `NOMBRE_TOMADOR`,`td`.`tipo_persona` AS `TIPO_PERSONA`,`clientes`.`id` AS `CLIENTE_ID`,`clientes`.`documento` AS `IDENTIFICACION_TOMADOR`,IF((`zr`.`fecha_diligenciamiento` = '0000-00-00'),NULL,`zr`.`fecha_diligenciamiento`) AS `FECHA_DILIGENCIAMIENTO`,`cj`.`tipo_solicitud` AS `TIPO_SOLICITUD`,`cj`.`clase_vinculacion` AS `CLASE_VINCULACION`,`cj`.`clase_vinculacion_otro` AS `CLASE_VINCULACION_OTRO`,`cj`.`relacion_tom_asegurado` AS `TOMADOR_ASEGURADO`,`cj`.`relacion_tom_asegurado_otra` AS `TOMADOR_ASEGURADO_OTRO`,`cj`.`relacion_tom_beneficiario` AS `TOMADOR_BENEFICIARIO`,`cj`.`relacion_tom_beneficiario_otra` AS `TOMADOR_BENEFICIARIO_OTRO`,`cj`.`relacion_aseg_beneficiario` AS `ASEGURADO_BENEFICIARIO`,`cj`.`relacion_aseg_beneficiario_otra` AS `ASEGURADO_BENEFICIARIO_OTRO`,NULL AS `PRIMER_APELLIDO`,NULL AS `SEGUNDO_APELLIDO`,NULL AS `NOMBRES`,UCASE(`cj`.`razon_social`) AS `NOMBRE_O_RAZON_SOCIAL`,`td`.`codigo` AS `TIPO_DOCUMENTO`,`td`.`id` AS `COD_DOCUMENTO`,IF((`cj`.`info_basica_tipo_sociedad` <> 8),(SELECT `tipos_sociedad`.`tipo` FROM `tipos_sociedad` WHERE (`tipos_sociedad`.`id` = `cj`.`info_basica_tipo_sociedad`)),`cj`.`info_basica_tipo_sociedad_otro`) AS `TIPO_SOCIEDAD`,NULL AS `LUGAR_EXPEDICION`,NULL AS `SEXO`,NULL AS `ESTADO_CIVIL`,NULL AS `FECHA_EXPEDICION_DOCUMENTO_PN`,NULL AS `FECHA_NACIMIENTO_PN`,NULL AS `LUGAR_NACIMIENTO_PN`,NULL AS `NACIONALIDAD_1_PN`,NULL AS `NACIONALIDAD_2_PN`,`cj`.`ofi_principal_direccion` AS `DIRECCION_OFICINA_PRINCIPAL_RESIDENCIA`,`cj`.`ofi_principal_tipo_empresa` AS `TIPO_EMPRESA`,(SELECT `tipos_actividades_principales`.`nombre_actividad_principal` FROM `tipos_actividades_principales` WHERE (`tipos_actividades_principales`.`id` = IFNULL(`cj`.`ofi_principal_ciiu_cod`,`cj`.`ofi_principal_ciiu`))) AS `CIIU_ACTIVIDAD_ECONOMICA`,UCASE(`cj`.`ofi_principal_ciiu_otro`) AS `CIIU_ACTIVIDAD_ECONOMICA_OTRA`,`cj`.`ofi_principal_ciiu_cod` AS `CIIU_COD`,(SELECT UCASE(`tap`.`nombre_actividad_principal`) FROM `tipos_actividades_principales` `tap` WHERE (`tap`.`id` = `cj`.`ofi_principal_ciiu_cod`)) AS `ACTIVIDAD_ECONOMICA`,(SELECT UCASE(`sector`.`desc_sector`) FROM `sector` WHERE (`sector`.`id` = `cj`.`ofi_principal_sector`)) AS `SECTOR`,UCASE(`cj`.`ofi_principal_breve_descripcion_objeto_social`) AS `BREVE_DESCRIPCION`,(SELECT UCASE(`departamentos`.`nombre_departamento`) FROM `departamentos` WHERE (`departamentos`.`id` = `cj`.`ofi_principal_departamento_empresa`)) AS `DEPARTAMENTO_OFICINA_RESIDENCIA`,(SELECT UCASE(`ciudades`.`nombre_ciudad`) FROM `ciudades` WHERE (`ciudades`.`id` = `cj`.`ofi_principal_ciudad_empresa`)) AS `CIUDAD_OFICINA_RESIDENCIA`,`cj`.`ofi_principal_telefono` AS `TELEFONO_OFICINA_RESIDENCIA`,NULL AS `CELULAR`,`cj`.`ofi_principal_pagina_web` AS `PAGINA_WEB`,`cj`.`ofi_principal_email` AS `CORREO_ELECTRONICO`,(SELECT UCASE(`departamentos`.`nombre_departamento`) FROM `departamentos` WHERE (`departamentos`.`id` = `cj`.`sucursal_departamento`)) AS `SUCURSAL_DEPARATAMENTO`,(SELECT UCASE(`ciudades`.`nombre_ciudad`) FROM `ciudades` WHERE (`ciudades`.`id` = `cj`.`sucursal_ciudad`)) AS `SUCURSAL_CIUDAD`,`cj`.`sucursal_direccion` AS `SUCURSAL_DIRECCION`,`cj`.`sucursal_telefono` AS `SUCURSAL_TELEFONO`,`cj`.`sucursal_fax` AS `SUCURSAL_FAX`,NULL AS `OCUPACION`,NULL AS `TIPO_ACTIVIDAD`,NULL AS `CARGO`,NULL AS `EMPRESA_DONDE_TRABAJA_PN`,NULL AS `DIRECCION_EMPRESA_PN`,NULL AS `CIUDAD_EMPRESA_PN`,NULL AS `DEPARTAMENTO_EMPRESA_PN`,NULL AS `TELEFONO_EMPRESA_PN`,NULL AS `ACTIVIDAD_SEVUNDARIA_PN`,NULL AS `CIIU_SECUNDARIA_PN`,UCASE(`cj`.`rep_legal_primer_apellido`) AS `REPRESENTANTE_LEGAL_PRIMER_APELLIDO`,UCASE(`cj`.`rep_legal_segundo_apellido`) AS `REPRESENTANTE_LEGAL_SEGUNDO_APELLIDO`,UCASE(`cj`.`rep_legal_nombres`) AS `REPRESENTANTE_LEGAL_NOMBRES`,(SELECT `tipos_documentos`.`codigo` FROM `tipos_documentos` WHERE (`tipos_documentos`.`id` = `cj`.`rep_legal_tipo_documento`)) AS `REPRESENTANTE_TIPO_DOCUMENTO`,`cj`.`rep_legal_tipo_documento` AS `REPRESENTANTE_COD_DOCUMENTO`,`cj`.`rep_legal_documento` AS `REPRESENTANTE_DOCUMENTO`,`cj`.`rep_legal_fecha_exp_documento` AS `REPRESENTANTE_FECHA_EXPEDICION`,`cj`.`rep_legal_lugar_expedicion` AS `REPRESENTANTE_LUGAR_EXPEDICION`,`cj`.`rep_legal_fecha_nacimiento` AS `REPRESENTANTE_FECHA_NACIMIENTO`,`cj`.`rep_legal_lugar_nacimiento` AS `REPRESENTANTE_LUGAR_NACIMIENTO`,(SELECT `paises`.`nombre_pais` FROM `paises` WHERE (`paises`.`id` = `cj`.`rep_legal_nacionalidad_1`)) AS `REPRESENTANTE_NACIONALIDAD_1`,(SELECT `paises`.`nombre_pais` FROM `paises` WHERE (`paises`.`id` = `cj`.`rep_legal_nacionalidad_2`)) AS `REPRESENTANTE_NACIONALIDAD_2`,`cj`.`rep_legal_email` AS `REPRESENTANTE_EMAIL`,`cj`.`rep_legal_direccion_residencia` AS `REPRESENTANTE_DIRECCION_RESIDENCIA`,(SELECT UCASE(`ciudades`.`nombre_ciudad`) FROM `ciudades` WHERE (`ciudades`.`id` = `cj`.`rep_legal_ciudad_residencia`)) AS `REPRESENTANTE_CIUDAD_RESIDENCIA`,(SELECT UCASE(`departamentos`.`nombre_departamento`) FROM `departamentos` WHERE (`departamentos`.`id` = `cj`.`rep_legal_departamento_residencia`)) AS `REPRESENTANTE_DEPARTAMENTO_RESIDENCIA`,(SELECT `paises`.`nombre_pais` FROM `paises` WHERE (`paises`.`id` = `cj`.`rep_legal_pais_residencia`)) AS `REPRESENTANTE_PAIS_RESIDENCIA`,`cj`.`rep_legal_telefono_residencia` AS `REPRESENTANTE_TELEFONO_RESIDENCIA`,`cj`.`rep_legal_celular_residencia` AS `REPRESENTANTE_CELULAR_RESIDENCIA`,IF((`cj`.`rep_legal_persona_publica` = 1),'SI','NO') AS `REPRESENTANTE_PERSONA_PUBLICA`,IF((`cj`.`rep_legal_recursos_publicos` = 1),'SI','NO') AS `REPRESENTANTE_RECURSOS_PUBLICOS`,IF((`cj`.`rep_legal_obligaciones_tributarias` = 1),'SI','NO') AS `REPRESENTANTE_OBLIGACIONES_TRIBUTARIAS`,`cj`.`rep_legal_obligaciones_tributarias_indique` AS `REPRESENTANTE_OBLIGACIONES_TRIBUTARIAS_CUAL`,NULL AS `PERSONA_PUBLICA_PN`,NULL AS `VINCULO_PERSONA_PUBLICA_PN`,NULL AS `RECURSOS_PUBLICOS_PN`,`cj`.`ingresos` AS `INGRESOS`,`cj`.`egresos` AS `EGRESOS`,`cj`.`activos` AS `ACTIVOS`,`cj`.`pasivos` AS `PASIVOS`,`cj`.`patrimonio` AS `PATRIMONIO`,`cj`.`otros_ingresos` AS `OTROS_INGRESOS`,`cj`.`desc_otros_ingresos` AS `CONCEPTO_OTROS_INGRESOS`,`cj`.`declaracion_origen_fondos` AS `DECLARACION_ORIGEN_FONDOS`,IF((`cj`.`operaciones_moneda_extranjera` = 1),'SI','NO') AS `TRANSACCIONES_MONEDA_EXTRANJERA`,(SELECT `tipos_operaciones_moneda_extranjera`.`desc_operacion` FROM `tipos_operaciones_moneda_extranjera` WHERE (`tipos_operaciones_moneda_extranjera`.`id` = `cj`.`tipo_operaciones_moneda_extranjera`)) AS `TRANSACCIONES_MONEDA_EXTRANJERA_CUAL`,`cj`.`tipo_operaciones_moneda_extranjera_otro` AS `TRANSACCIONES_MONEDA_EXTRANJERA_OTRAS`,IF((`cj`.`productos_exterior` = 1),'SI','NO') AS `PRODUCTOS_EXTERIOR`,IF((`cj`.`cuentas_moneda_exterior` = 1),'SI','NO') AS `CUENTAS_MONEDA_EXTRANJERA`,IF((`cj`.`reclamaciones` = 1),'SI','NO') AS `RECLAMACIONES`,`cj`.`reclamacion_anio` AS `RECLAMACION_ANIO`,`cj`.`reclamacion_ramo` AS `RECLAMACION_RAMO`,`cj`.`reclamacion_compania` AS `RECLAMACION_COMPANIA`,`cj`.`reclamacion_valor` AS `RECLAMACION_VALOR`,`cj`.`reclamacion_resultado` AS `RECLAMACION_RESULTADO`,`cj`.`reclamacion_anio_2` AS `RECLAMACION_ANIO_2`,`cj`.`reclamacion_ramo_2` AS `RECLAMACION_RAMO_2`,`cj`.`reclamacion_compania_2` AS `RECLAMACION_COMPANIA_2`,`cj`.`reclamacion_valor_2` AS `RECLAMACION_VALOR_2`,`cj`.`reclamacion_resultado_2` AS `RECLAMACION_RESULTADO_2`,(SELECT UCASE(`ciudades`.`nombre_ciudad`) FROM `ciudades` WHERE (`ciudades`.`id` = `cj`.`ciudad_diligenciamiento`)) AS `CIUDAD_DILIGENCIAMIENTO`,`cj`.`sucursal` AS `SUCURSAL` FROM (((((((`clientes` JOIN `tipos_documentos` `td` ON((`td`.`id` = `clientes`.`tipo_documento`))) JOIN `cliente_sarlaft_juridico` `cj` ON((`cj`.`cliente` = `clientes`.`id`))) JOIN `zr_radicacion` `zr` ON(((`zr`.`cliente_id` = `cj`.`cliente`) AND (`zr`.`created` = (SELECT MAX(`zr2`.`created`) FROM `zr_radicacion` `zr2` WHERE ((`zr2`.`cliente_id` = `zr`.`cliente_id`) AND (`zr2`.`fecha_diligenciamiento` = (SELECT MAX(`zr3`.`fecha_diligenciamiento`) FROM `zr_radicacion` `zr3` WHERE ((`zr3`.`cliente_id` = `zr2`.`cliente_id`) AND (`zr3`.`formulario_sarlaft` = 1) AND (`zr3`.`repetido` = 0) AND (`zr3`.`radicacion_proceso` = 'LEGAL')))))))))) JOIN `zr_estado_proceso_clientes_sarlaft` `zepcs` ON(((`zepcs`.`PROCESO_CLIENTE_ID` = `cj`.`cliente`) AND (`zepcs`.`ESTADO_PROCESO_ID` NOT IN (2,12)) AND (`zepcs`.`FECHA_PROCESO` = (SELECT MAX(`zepcs2`.`FECHA_PROCESO`) FROM `zr_estado_proceso_clientes_sarlaft` `zepcs2` WHERE ((`zepcs2`.`PROCESO_CLIENTE_ID` = `zepcs`.`PROCESO_CLIENTE_ID`) AND (`zepcs2`.`PROCESO_FECHA_DILIGENCIAMIENTO` = `zr`.`fecha_diligenciamiento`))))))) JOIN `users` ON((`users`.`id` = `zr`.`funcionario_id`))) JOIN `estados_sarlaft` `es` ON((`es`.`id` = `zepcs`.`ESTADO_PROCESO_ID`))) JOIN `gestion_clientes_captura` `gcc` ON(((`zr`.`cliente_id` = `gcc`.`GESTION_CLIENTE_ID`) AND (`gcc`.`FECHA_GESTION` = (SELECT MAX(`gestion_clientes_captura`.`FECHA_GESTION`) FROM `gestion_clientes_captura` WHERE ((`gestion_clientes_captura`.`GESTION_CLIENTE_ID` = `gcc`.`GESTION_CLIENTE_ID`) AND (`gcc`.`GESTION_FECHA_DILIGENCIAMIENTO` = `zr`.`fecha_diligenciamiento`))))))) WHERE (`cj`.`razon_social` IS NOT NULL) GROUP BY `clientes`.`documento`$$

DELIMITER ;


DROP VIEW IF EXISTS `reporte_clientes_checklist_documentos`;

DELIMITER $$

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `reporte_clientes_checklist_documentos` AS 
SELECT DISTINCT CAST(`zr`.`created` AS DATE) AS `FECHA_RADICACION`,`zr`.`correo_radicacion` AS `CORREO`,`tipos_documentos`.`codigo` AS `CLIENTE_TIPO_DOCUMENTO_CODIGO`,`clientes`.`documento` AS `CLIENTE_DOCUMENTO`,UCASE(TRIM(CONCAT_WS(' ',`cn`.`primer_apellido`,`cn`.`segundo_apellido`,`cn`.`primer_nombre`,`cn`.`segundo_nombre`))) AS `NOMBRE_CLIENTE`,`zr`.`radicacion_proceso` AS `RADICACION_PROCESO`,IF(((SELECT COUNT(`archivo_organizado`.`ID_TIPO_DOC`) FROM `archivo_organizado` WHERE ((`archivo_organizado`.`NUMERO_IDENT_CLIENTE` = `clientes`.`documento`) AND (`archivo_organizado`.`ID_TIPO_DOC` = 'SAA'))) >= 1),'SI','NO') AS `SAA`,IF((`zr`.`formulario` = 'Nuevo'),'SI','NO') AS `TIPO_FORMULARIO`,IF((`zr`.`devuelto` = 'Si'),'DEVUELTO',(SELECT `estados_sarlaft`.`desc_type` FROM `estados_sarlaft` WHERE (`estados_sarlaft`.`id` = `zepcs`.`ESTADO_PROCESO_ID`))) AS `CLIENTE_ESTADO_PROCESO`,`zr`.`numero_planilla` AS `NUMERO_PLANILLA`,`zr`.`radicacion_observacion` AS `OBSERVACIONES` FROM ((((`cliente_sarlaft_natural` `cn` JOIN `clientes` ON((`clientes`.`id` = `cn`.`cliente`))) JOIN `tipos_documentos` ON((`tipos_documentos`.`id` = `clientes`.`tipo_documento`))) JOIN `zr_radicacion` `zr` ON(((`zr`.`cliente_id` = `clientes`.`id`) AND (`zr`.`created` = (SELECT MAX(`zr2`.`created`) FROM `zr_radicacion` `zr2` WHERE ((`zr2`.`cliente_id` = `zr`.`cliente_id`) AND (`zr2`.`fecha_diligenciamiento` = (SELECT MAX(`zr3`.`fecha_diligenciamiento`) FROM `zr_radicacion` `zr3` WHERE (`zr3`.`cliente_id` = `zr2`.`cliente_id`))))))))) JOIN `zr_estado_proceso_clientes_sarlaft` `zepcs` ON(((`zepcs`.`PROCESO_CLIENTE_ID` = `zr`.`cliente_id`) AND (`zepcs`.`PROCESO_FECHA_DILIGENCIAMIENTO` = `zr`.`fecha_diligenciamiento`) AND (`zepcs`.`FECHA_PROCESO` = (SELECT MAX(`zr_estado_proceso_clientes_sarlaft`.`FECHA_PROCESO`) FROM `zr_estado_proceso_clientes_sarlaft` WHERE (`zr_estado_proceso_clientes_sarlaft`.`PROCESO_CLIENTE_ID` = `zepcs`.`PROCESO_CLIENTE_ID`)))))) UNION ALL SELECT DISTINCT CAST(`zr`.`created` AS DATE) AS `FECHA_RADICACION`,`zr`.`correo_radicacion` AS `CORREO`,`tipos_documentos`.`codigo` AS `CLIENTE_TIPO_DOCUMENTO_CODIGO`,`clientes`.`documento` AS `CLIENTE_DOCUMENTO`,UCASE(`cj`.`razon_social`) AS `NOMBRE_CLIENTE`,`zr`.`radicacion_proceso` AS `RADICACION_PROCESO`,IF(((SELECT COUNT(`archivo_organizado`.`ID_TIPO_DOC`) FROM `archivo_organizado` WHERE ((`archivo_organizado`.`NUMERO_IDENT_CLIENTE` = `clientes`.`documento`) AND (`archivo_organizado`.`ID_TIPO_DOC` = 'SAA'))) >= 1),'SI','NO') AS `SAA`,IF((`zr`.`formulario` = 'Nuevo'),'SI','NO') AS `TIPO_FORMULARIO`,IF((`zr`.`devuelto` = 'Si'),'DEVUELTO',(SELECT `estados_sarlaft`.`desc_type` FROM `estados_sarlaft` WHERE (`estados_sarlaft`.`id` = `zepcs`.`ESTADO_PROCESO_ID`))) AS `CLIENTE_ESTADO_PROCESO`,`zr`.`numero_planilla` AS `NUMERO_PLANILLA`,`zr`.`radicacion_observacion` AS `OBSERVACIONES` FROM ((((`cliente_sarlaft_juridico` `cj` JOIN `clientes` ON((`clientes`.`id` = `cj`.`cliente`))) JOIN `tipos_documentos` ON((`tipos_documentos`.`id` = `clientes`.`tipo_documento`))) JOIN `zr_radicacion` `zr` ON(((`zr`.`cliente_id` = `clientes`.`id`) AND (`zr`.`created` = (SELECT MAX(`zr2`.`created`) FROM `zr_radicacion` `zr2` WHERE ((`zr2`.`cliente_id` = `zr`.`cliente_id`) AND (`zr2`.`fecha_diligenciamiento` = (SELECT MAX(`zr3`.`fecha_diligenciamiento`) FROM `zr_radicacion` `zr3` WHERE (`zr3`.`cliente_id` = `zr2`.`cliente_id`))))))))) JOIN `zr_estado_proceso_clientes_sarlaft` `zepcs` ON(((`zepcs`.`PROCESO_CLIENTE_ID` = `zr`.`cliente_id`) AND (`zepcs`.`PROCESO_FECHA_DILIGENCIAMIENTO` = `zr`.`fecha_diligenciamiento`) AND (`zepcs`.`FECHA_PROCESO` = (SELECT MAX(`zr_estado_proceso_clientes_sarlaft`.`FECHA_PROCESO`) FROM `zr_estado_proceso_clientes_sarlaft` WHERE (`zr_estado_proceso_clientes_sarlaft`.`PROCESO_CLIENTE_ID` = `zepcs`.`PROCESO_CLIENTE_ID`))))))$$

DELIMITER ;


DROP VIEW IF EXISTS `reporte_clientes_completitud_verificacion`;


DELIMITER $$

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `reporte_clientes_completitud_verificacion` AS 
SELECT
  `zr`.`created`                AS `FECHA_RADICACION`,
  `zr`.`correo_radicacion`      AS `CORREO`,
  `td`.`codigo`                 AS `CLIENTE_TIPO_DOCUMENTO_CODIGO`,
  `clientes`.`documento`        AS `CLIENTE_DOCUMENTO`,
  `cs`.`NOMBRE_CLIENTE`         AS `NOMBRE_CLIENTE`,
  (SELECT
     MAX(`gestion_clientes_completitud_verificacion`.`FECHA_GESTION`)
   FROM `gestion_clientes_completitud_verificacion`
   WHERE ((`gestion_clientes_completitud_verificacion`.`GESTION_CLIENTE_ID` = `gcv`.`GESTION_CLIENTE_ID`)
          AND (`gestion_clientes_completitud_verificacion`.`GESTION_PROCESO_ID` = 6))) AS `FECHA_COMPLETITUD`,
  (SELECT
     `gestion_clientes_completitud_verificacion`.`GESTION_CAMPOS_COMPLETADOS`
   FROM `gestion_clientes_completitud_verificacion`
   WHERE ((`gestion_clientes_completitud_verificacion`.`GESTION_CLIENTE_ID` = `gcv`.`GESTION_CLIENTE_ID`)
          AND (`gestion_clientes_completitud_verificacion`.`GESTION_PROCESO_ID` = 6))) AS `GESTION_CAMPOS_COMPLETADOS`,
  IF((`gcv`.`GESTION_PROCESO_ID` = 5),`gcv`.`FECHA_GESTION`,NULL) AS `FECHA_VERIFICACION`,
  IF((ISNULL(`gcv`.`GESTION_CAMPOS_VACIOS`) AND ((SELECT `gestion_clientes_completitud_verificacion`.`GESTION_CAMPOS_COMPLETADOS` FROM `gestion_clientes_completitud_verificacion` WHERE ((`gestion_clientes_completitud_verificacion`.`GESTION_CLIENTE_ID` = `gcv`.`GESTION_CLIENTE_ID`) AND (`gestion_clientes_completitud_verificacion`.`GESTION_PROCESO_ID` = 6))) IS NOT NULL)),'SI','NO') AS `PREGUNTA_CAMPOS_COMPLETADOS`,
  `gcv`.`GESTION_OBSERVACIONES` AS `GESTION_OBSERVACIONES`
FROM ((((`gestion_clientes_completitud_verificacion` `gcv`
      JOIN `clientes_sarlaft` `cs`
        ON ((`cs`.`cliente_id` = `gcv`.`GESTION_CLIENTE_ID`)))
     JOIN `clientes`
       ON ((`clientes`.`id` = `cs`.`cliente_id`)))
    JOIN `tipos_documentos` `td`
      ON ((`td`.`id` = `clientes`.`tipo_documento`)))
   JOIN `zr_radicacion` `zr`
     ON (((`zr`.`cliente_id` = `clientes`.`id`)
          AND (`zr`.`formulario_sarlaft` = 1)
          AND (`zr`.`fecha_diligenciamiento` = `gcv`.`GESTION_FECHA_DILIGENCIAMIENTO`))))
WHERE (`gcv`.`FECHA_GESTION` = (SELECT
                                  MAX(`gestion_clientes_completitud_verificacion`.`FECHA_GESTION`)
                                FROM `gestion_clientes_completitud_verificacion`
                                WHERE (`gestion_clientes_completitud_verificacion`.`GESTION_CLIENTE_ID` = `gcv`.`GESTION_CLIENTE_ID`)))$$

DELIMITER ;


DROP VIEW IF EXISTS `reporte_clientes_pendientes`;

DELIMITER $$

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `reporte_clientes_pendientes` AS 
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
                                                                    `clientes`.`id`                AS `id`,
                                                                    `zr`.`created`                 AS `FECHA_RADICACION`,
                                                                    `zr`.`fecha_diligenciamiento`  AS `fecha_diligenciamiento`,
                                                                    `zr`.`correo_radicacion`       AS `CORREO`,
                                                                    `zr`.`fecha_envio_correo`      AS `FECHA_ENVIO_CORREO`,
                                                                    `td`.`codigo`                  AS `CLIENTE_TIPO_DOCUMENTO_CODIGO`,
                                                                    `clientes`.`documento`         AS `CLIENTE_DOCUMENTO`,
                                                                    TRIM(CONCAT_WS(' ',`cn`.`primer_apellido`,`cn`.`segundo_apellido`,`cn`.`primer_nombre`,`cn`.`segundo_nombre`)) AS `NOMBRE_CLIENTE`,
                                                                    `zr`.`correo_radicacion`       AS `CORREO_RADICACION`,
                                                                    IF((`cn`.`firma` = 1),'SI','NO') AS `FIRMA`,
                                                                    IF((`cn`.`huella` = 1),'SI','NO') AS `HUELLA`,
                                                                    IF((`cn`.`entrevista` = 1),'SI','NO') AS `ENTREVISTA`,
                                                                    `es`.`desc_type`               AS `ESTADO_PROCESO`,
                                                                    `zr`.`radicacion_proceso`      AS `PROCESO_RADICACION`,
                                                                    IF((`zepcs`.`ESTADO_PROCESO_ID` <> 12),NULL,'PENDIENTE SARLAFT') AS `DOCUMENTO_PENDIENTE_CODIGO`,
                                                                    `zr`.`radicacion_observacion`  AS `OBSERVACION`
                                                                  FROM (((((`clientes`
                                                                         JOIN `tipos_documentos` `td`
                                                                           ON ((`td`.`id` = `clientes`.`tipo_documento`)))
                                                                        JOIN `cliente_sarlaft_natural` `cn`
                                                                          ON ((`cn`.`cliente` = `clientes`.`id`)))
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


DROP VIEW IF EXISTS `reporte_facturacion`;

DELIMITER $$

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `reporte_facturacion` AS 
SELECT `clientes`.`id` AS `cliente_id`,`zr`.`created` AS `FECHA_RADICACION`,`users`.`id` AS `user_id`,`users`.`nombre` AS `USUARIO_ASISTEMYCA`,(SELECT UCASE(`users1`.`nombre`) FROM `users` `users1` WHERE (`users1`.`id` = `gcc`.`GESTION_USUARIO_ID`)) AS `USUARIO_CAPTURA`,`gcc`.`FECHA_GESTION` AS `FECHA_CAPTURA`,`zr`.`numero_planilla` AS `NUMERO_PLANILLA`,IF((`zr`.`fecha_diligenciamiento` = '0000-00-00'),NULL,`zr`.`fecha_diligenciamiento`) AS `FECHA_DILIGENCIAMIENTO`,`zr`.`correo_radicacion` AS `USUARIO_SUSCRIPTOR`,`zr`.`separado` AS `SEPARADO`,`td`.`codigo` AS `TIPO_CLIENTE_CODIGO`,`clientes`.`documento` AS `CLIENTE_DOCUMENTO`,CONCAT_WS('-',CONCAT(DATE_FORMAT(CAST(`zr`.`created` AS DATE),'%Y%m%d'),`zr`.`cliente_id`),`zr`.`consecutivo`) AS `NUMERO_RADICACION`,`zr`.`cantidad_documentos` AS `CANT_DOCUMENTOS`,TRIM(CONCAT_WS(' ',`cn`.`primer_apellido`,`cn`.`segundo_apellido`,`cn`.`primer_nombre`,`cn`.`segundo_nombre`)) AS `NOMBRE_TOMADOR`,IF((`zr`.`repetido` = 1),'SI','NO') AS `FORMULARIO_REPETIDO`,(SELECT MAX(`gccv1`.`FECHA_GESTION`) FROM `gestion_clientes_completitud_verificacion` `gccv1` WHERE ((`gccv1`.`GESTION_CLIENTE_ID` = `gccv`.`GESTION_CLIENTE_ID`) AND (`gccv1`.`GESTION_PROCESO_ID` = 6))) AS `PROCESO_COMPLETITUD`,(SELECT MAX(`gccv2`.`FECHA_GESTION`) FROM `gestion_clientes_completitud_verificacion` `gccv2` WHERE ((`gccv2`.`GESTION_CLIENTE_ID` = `gccv`.`GESTION_CLIENTE_ID`) AND (`gccv2`.`GESTION_PROCESO_ID` = 5))) AS `PROCESO_VERIFICACION`,`zt`.`tipologia` AS `ESTADO_TIPOLOGIA`,`gccv`.`GESTION_NO_INTENTOS` AS `INTENTO_LLAMADA`,`zr`.`tipo_medio` AS `FORMA_RECEPCION`,`zr`.`radicacion_proceso` AS `RADICACION_PROCESO`,`zr`.`formulario` AS `TIPO_FORMULARIO`,`zepcs`.`PROCESO_INOUTBOUND` AS `TIPO_LLAMADA`,IF((`zr`.`repetido` <> 1),IF((`es`.`desc_type` IS NOT NULL),`es`.`desc_type`,'FECHA ANTIGUA'),'DEVUELTO') AS `ESTADO_PROCESO`,IF(((`zepcs`.`ESTADO_PROCESO_ID` NOT IN (2,1)) AND (`zr`.`repetido` <> 1)),`zepcs`.`FECHA_PROCESO`,NULL) AS `FECHA_PROCESO_CAPTURA`,IF(((`zepcs`.`ESTADO_PROCESO_ID` NOT IN (2,1)) AND (`zr`.`repetido` <> 1)),'Si','No') AS `FORMULARIO_CAPTURADO`,`zr`.`radicacion_observacion` AS `OBSERVACION` FROM (((((((((`clientes` JOIN `tipos_documentos` `td` ON((`td`.`id` = `clientes`.`tipo_documento`))) JOIN `zr_radicacion` `zr` ON((`zr`.`cliente_id` = `clientes`.`id`))) JOIN `users` ON((`users`.`id` = `zr`.`funcionario_id`))) JOIN `cliente_sarlaft_natural` `cn` ON((`cn`.`cliente` = `zr`.`cliente_id`))) LEFT JOIN `gestion_clientes_captura` `gcc` ON(((`gcc`.`GESTION_CLIENTE_ID` = `zr`.`cliente_id`) AND (`gcc`.`FECHA_GESTION` = (SELECT MAX(`gcc2`.`FECHA_GESTION`) FROM `gestion_clientes_captura` `gcc2` WHERE ((`gcc2`.`GESTION_CLIENTE_ID` = `gcc`.`GESTION_CLIENTE_ID`) AND (`gcc2`.`GESTION_FECHA_DILIGENCIAMIENTO` = `zr`.`fecha_diligenciamiento`) AND (`gcc2`.`FECHA_GESTION` >= `zr`.`created`))))))) LEFT JOIN `gestion_clientes_completitud_verificacion` `gccv` ON(((`gccv`.`GESTION_CLIENTE_ID` = `zr`.`cliente_id`) AND (`gccv`.`FECHA_GESTION` = (SELECT MAX(`gccv2`.`FECHA_GESTION`) FROM `gestion_clientes_completitud_verificacion` `gccv2` WHERE ((`gccv2`.`GESTION_CLIENTE_ID` = `gccv`.`GESTION_CLIENTE_ID`) AND (`gccv2`.`GESTION_FECHA_DILIGENCIAMIENTO` = `zr`.`fecha_diligenciamiento`) AND (`gccv2`.`FECHA_GESTION` >= `zr`.`created`))))))) LEFT JOIN `zr_tipologias` `zt` ON((`zt`.`id` = `gccv`.`GESTION_ESTADO_TIPOLOGIA_ID`))) LEFT JOIN `zr_estado_proceso_clientes_sarlaft` `zepcs` ON(((`zepcs`.`PROCESO_CLIENTE_ID` = `zr`.`cliente_id`) AND (`zepcs`.`FECHA_PROCESO` = (SELECT MAX(`zepcs1`.`FECHA_PROCESO`) FROM `zr_estado_proceso_clientes_sarlaft` `zepcs1` WHERE ((`zepcs1`.`PROCESO_CLIENTE_ID` = `zepcs`.`PROCESO_CLIENTE_ID`) AND (`zepcs1`.`PROCESO_FECHA_DILIGENCIAMIENTO` = `zr`.`fecha_diligenciamiento`) AND (`zepcs1`.`FECHA_PROCESO` >= `zr`.`created`))))))) LEFT JOIN `estados_sarlaft` `es` ON((`es`.`id` = `zepcs`.`ESTADO_PROCESO_ID`))) UNION ALL SELECT `clientes`.`id` AS `cliente_id`,`zr`.`created` AS `FECHA_RADICACION`,`users`.`id` AS `user_id`,`users`.`nombre` AS `USUARIO_ASISTEMYCA`,(SELECT UCASE(`users1`.`nombre`) FROM `users` `users1` WHERE (`users1`.`id` = `gcc`.`GESTION_USUARIO_ID`)) AS `USUARIO_CAPTURA`,`gcc`.`FECHA_GESTION` AS `FECHA_CAPTURA`,`zr`.`numero_planilla` AS `NUMERO_PLANILLA`,IF((`zr`.`fecha_diligenciamiento` = '0000-00-00'),NULL,`zr`.`fecha_diligenciamiento`) AS `FECHA_DILIGENCIAMIENTO`,`zr`.`correo_radicacion` AS `USUARIO_SUSCRIPTOR`,`zr`.`separado` AS `SEPARADO`,`td`.`codigo` AS `TIPO_CLIENTE_CODIGO`,`clientes`.`documento` AS `CLIENTE_DOCUMENTO`,CONCAT_WS('-',CONCAT(DATE_FORMAT(CAST(`zr`.`created` AS DATE),'%Y%m%d'),`zr`.`cliente_id`),`zr`.`consecutivo`) AS `NUMERO_RADICACION`,`zr`.`cantidad_documentos` AS `CANT_DOCUMENTOS`,TRIM(`cj`.`razon_social`) AS `NOMBRE_TOMADOR`,IF((`zr`.`repetido` = 1),'SI','NO') AS `FORMULARIO_REPETIDO`,(SELECT MAX(`gccv1`.`FECHA_GESTION`) FROM `gestion_clientes_completitud_verificacion` `gccv1` WHERE ((`gccv1`.`GESTION_CLIENTE_ID` = `gccv`.`GESTION_CLIENTE_ID`) AND (`gccv1`.`GESTION_PROCESO_ID` = 6))) AS `PROCESO_COMPLETITUD`,(SELECT MAX(`gccv2`.`FECHA_GESTION`) FROM `gestion_clientes_completitud_verificacion` `gccv2` WHERE ((`gccv2`.`GESTION_CLIENTE_ID` = `gccv`.`GESTION_CLIENTE_ID`) AND (`gccv2`.`GESTION_PROCESO_ID` = 5))) AS `PROCESO_VERIFICACION`,`zt`.`tipologia` AS `ESTADO_TIPOLOGIA`,`gccv`.`GESTION_NO_INTENTOS` AS `INTENTO_LLAMADA`,`zr`.`tipo_medio` AS `FORMA_RECEPCION`,`zr`.`radicacion_proceso` AS `RADICACION_PROCESO`,`zr`.`formulario` AS `TIPO_FORMULARIO`,`zepcs`.`PROCESO_INOUTBOUND` AS `TIPO_LLAMADA`,IF((`zr`.`repetido` <> 1),IF((`es`.`desc_type` IS NOT NULL),`es`.`desc_type`,'FECHA ANTIGUA'),'DEVUELTO') AS `ESTADO_PROCESO`,IF(((`zepcs`.`ESTADO_PROCESO_ID` NOT IN (2,1)) AND (`zr`.`repetido` <> 1)),`zepcs`.`FECHA_PROCESO`,NULL) AS `FECHA_PROCESO_CAPTURA`,IF(((`zepcs`.`ESTADO_PROCESO_ID` NOT IN (2,1)) AND (`zr`.`repetido` <> 1)),'Si','No') AS `FORMULARIO_CAPTURADO`,`zr`.`radicacion_observacion` AS `OBSERVACION` FROM (((((((((`clientes` JOIN `tipos_documentos` `td` ON((`td`.`id` = `clientes`.`tipo_documento`))) JOIN `zr_radicacion` `zr` ON((`zr`.`cliente_id` = `clientes`.`id`))) JOIN `users` ON((`users`.`id` = `zr`.`funcionario_id`))) JOIN `cliente_sarlaft_juridico` `cj` ON((`cj`.`cliente` = `zr`.`cliente_id`))) LEFT JOIN `gestion_clientes_captura` `gcc` ON(((`gcc`.`GESTION_CLIENTE_ID` = `zr`.`cliente_id`) AND (`gcc`.`FECHA_GESTION` = (SELECT MAX(`gcc2`.`FECHA_GESTION`) FROM `gestion_clientes_captura` `gcc2` WHERE ((`gcc2`.`GESTION_CLIENTE_ID` = `gcc`.`GESTION_CLIENTE_ID`) AND (`gcc2`.`GESTION_FECHA_DILIGENCIAMIENTO` = `zr`.`fecha_diligenciamiento`) AND (`gcc2`.`FECHA_GESTION` >= `zr`.`created`))))))) LEFT JOIN `gestion_clientes_completitud_verificacion` `gccv` ON(((`gccv`.`GESTION_CLIENTE_ID` = `zr`.`cliente_id`) AND (`gccv`.`FECHA_GESTION` = (SELECT MAX(`gccv2`.`FECHA_GESTION`) FROM `gestion_clientes_completitud_verificacion` `gccv2` WHERE ((`gccv2`.`GESTION_CLIENTE_ID` = `gccv`.`GESTION_CLIENTE_ID`) AND (`gccv2`.`GESTION_FECHA_DILIGENCIAMIENTO` = `zr`.`fecha_diligenciamiento`) AND (`gccv2`.`FECHA_GESTION` >= `zr`.`created`))))))) LEFT JOIN `zr_tipologias` `zt` ON((`zt`.`id` = `gccv`.`GESTION_ESTADO_TIPOLOGIA_ID`))) LEFT JOIN `zr_estado_proceso_clientes_sarlaft` `zepcs` ON(((`zepcs`.`PROCESO_CLIENTE_ID` = `zr`.`cliente_id`) AND (`zepcs`.`FECHA_PROCESO` = (SELECT MAX(`zepcs1`.`FECHA_PROCESO`) FROM `zr_estado_proceso_clientes_sarlaft` `zepcs1` WHERE ((`zepcs1`.`PROCESO_CLIENTE_ID` = `zepcs`.`PROCESO_CLIENTE_ID`) AND (`zepcs1`.`PROCESO_FECHA_DILIGENCIAMIENTO` = `zr`.`fecha_diligenciamiento`) AND (`zepcs1`.`FECHA_PROCESO` >= `zr`.`created`))))))) LEFT JOIN `estados_sarlaft` `es` ON((`es`.`id` = `zepcs`.`ESTADO_PROCESO_ID`)))$$

DELIMITER ;