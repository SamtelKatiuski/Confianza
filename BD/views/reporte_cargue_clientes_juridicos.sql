DELIMITER $$

ALTER ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `reporte_cargue_clientes_juridicos` AS 
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