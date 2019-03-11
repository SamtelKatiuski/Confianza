DELIMITER $$

ALTER ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `reporte_clientes_completitud_verificacion` AS 
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