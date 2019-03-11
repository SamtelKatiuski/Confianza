DELIMITER $$

ALTER ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `reporte_cargue_clientes_naturales` AS 
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