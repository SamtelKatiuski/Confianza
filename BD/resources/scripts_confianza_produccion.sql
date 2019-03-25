
-- ZR_ANEXOS_PPES

ALTER TABLE `zr_anexos_ppes` MODIFY `ppes_vinculo_relacion` VARCHAR(255) DEFAULT NULL;
ALTER TABLE `zr_anexos_ppes` MODIFY `ppes_nombre` VARCHAR(255) DEFAULT NULL;
ALTER TABLE `zr_anexos_ppes` MODIFY `ppes_tipo_identificacion` INT(1) DEFAULT NULL;
ALTER TABLE `zr_anexos_ppes` MODIFY `ppes_no_documento` VARCHAR(255) DEFAULT NULL;
ALTER TABLE `zr_anexos_ppes` MODIFY `ppes_nacionalidad` INT(1) DEFAULT NULL;
ALTER TABLE `zr_anexos_ppes` MODIFY `ppes_entidad` VARCHAR(255) DEFAULT NULL;
ALTER TABLE `zr_anexos_ppes` MODIFY `ppes_cargo` VARCHAR(255) DEFAULT NULL;
ALTER TABLE `zr_anexos_ppes` MODIFY `ppes_fecha_ingreso` DATE DEFAULT NULL;
ALTER TABLE `zr_anexos_ppes` MODIFY `ppes_desvinculacion` DATE DEFAULT NULL;
ALTER TABLE `zr_anexos_ppes` MODIFY `ppes_motivo` VARCHAR(255) DEFAULT NULL;


-- PRODUCTOS

ALTER TABLE `productos` MODIFY `tipo_producto` VARCHAR(30) DEFAULT NULL;
ALTER TABLE `productos` MODIFY `identificacion_producto` VARCHAR(30) DEFAULT NULL;
ALTER TABLE `productos` MODIFY `entidad` VARCHAR(30) DEFAULT NULL;
ALTER TABLE `productos` MODIFY `monto` DECIMAL(10, 0) DEFAULT NULL;
ALTER TABLE `productos` MODIFY `ciudad` VARCHAR(30) DEFAULT NULL;
ALTER TABLE `productos` MODIFY `pais` VARCHAR(30) DEFAULT NULL;
ALTER TABLE `productos` MODIFY `moneda` VARCHAR(20) DEFAULT NULL;

-- CLIENTE_SARLAFT_NATURAL

ALTER TABLE `cliente_sarlaft_natural` MODIFY `reclamaciones` INT(1) DEFAULT NULL;
ALTER TABLE `cliente_sarlaft_natural` MODIFY `reclamacion_anio` INT(4) DEFAULT NULL;
ALTER TABLE `cliente_sarlaft_natural` MODIFY `reclamacion_ramo` VARCHAR(40) DEFAULT NULL;
ALTER TABLE `cliente_sarlaft_natural` MODIFY `reclamacion_compania` VARCHAR(40) DEFAULT NULL;
ALTER TABLE `cliente_sarlaft_natural` MODIFY `reclamacion_valor` BIGINT(100) DEFAULT NULL;
ALTER TABLE `cliente_sarlaft_natural` MODIFY `reclamacion_resultado` INT(1) DEFAULT NULL;
ALTER TABLE `cliente_sarlaft_natural` MODIFY `reclamacion_anio_2` INT(11) DEFAULT NULL;
ALTER TABLE `cliente_sarlaft_natural` MODIFY `reclamacion_ramo_2` VARCHAR(40) DEFAULT NULL;
ALTER TABLE `cliente_sarlaft_natural` MODIFY `reclamacion_compania_2` VARCHAR(40) DEFAULT NULL;
ALTER TABLE `cliente_sarlaft_natural` MODIFY `reclamacion_valor_2` BIGINT(100) DEFAULT NULL;
ALTER TABLE `cliente_sarlaft_natural` MODIFY `reclamacion_resultado_2` INT(1) DEFAULT NULL;
ALTER TABLE `cliente_sarlaft_natural` MODIFY `huella` INT(1) DEFAULT NULL;
ALTER TABLE `cliente_sarlaft_natural` MODIFY `firma` INT(1) DEFAULT NULL;
ALTER TABLE `cliente_sarlaft_natural` MODIFY `verificacion` INT(1) DEFAULT NULL;
ALTER TABLE `cliente_sarlaft_natural` MODIFY `entrevista` INT(1) DEFAULT NULL;
ALTER TABLE `cliente_sarlaft_natural` MODIFY `autoriza_tratamiento` VARCHAR(5) DEFAULT NULL;
ALTER TABLE `cliente_sarlaft_natural` MODIFY `autoriza_info_fasecolda` VARCHAR(5) DEFAULT NULL;

-- CLIENTE_SARLAFT_JURIDICO
ALTER TABLE `cliente_sarlaft_juridico` MODIFY `reclamaciones` INT(1) DEFAULT NULL;
ALTER TABLE `cliente_sarlaft_juridico` MODIFY `reclamacion_anio` INT(4) DEFAULT NULL;
ALTER TABLE `cliente_sarlaft_juridico` MODIFY `reclamacion_ramo` VARCHAR(40) DEFAULT NULL;
ALTER TABLE `cliente_sarlaft_juridico` MODIFY `reclamacion_compania` VARCHAR(40) DEFAULT NULL;
ALTER TABLE `cliente_sarlaft_juridico` MODIFY `reclamacion_valor` BIGINT(100) DEFAULT NULL;
ALTER TABLE `cliente_sarlaft_juridico` MODIFY `reclamacion_resultado` INT(1) DEFAULT NULL;
ALTER TABLE `cliente_sarlaft_juridico` MODIFY `reclamacion_anio_2` INT(11) DEFAULT NULL;
ALTER TABLE `cliente_sarlaft_juridico` MODIFY `reclamacion_ramo_2` VARCHAR(40) DEFAULT NULL;
ALTER TABLE `cliente_sarlaft_juridico` MODIFY `reclamacion_compania_2` VARCHAR(40) DEFAULT NULL;
ALTER TABLE `cliente_sarlaft_juridico` MODIFY `reclamacion_valor_2` BIGINT(100) DEFAULT NULL;
ALTER TABLE `cliente_sarlaft_juridico` MODIFY `reclamacion_resultado_2` INT(1) DEFAULT NULL;
ALTER TABLE `cliente_sarlaft_juridico` MODIFY `rep_legal_persona_publica` VARCHAR(5) DEFAULT NULL;
ALTER TABLE `cliente_sarlaft_juridico` MODIFY `rep_legal_recursos_publicos` VARCHAR(5) DEFAULT NULL;
ALTER TABLE `cliente_sarlaft_juridico` MODIFY `rep_legal_obligaciones_tributarias` VARCHAR(5) DEFAULT NULL;
ALTER TABLE `cliente_sarlaft_juridico` MODIFY `anexo_accionistas` VARCHAR(5) DEFAULT NULL;
ALTER TABLE `cliente_sarlaft_juridico` MODIFY `operaciones_moneda_extranjera` VARCHAR(5) DEFAULT NULL;
ALTER TABLE `cliente_sarlaft_juridico` MODIFY `cuentas_moneda_exterior` VARCHAR(5) DEFAULT NULL;
ALTER TABLE `cliente_sarlaft_juridico` MODIFY `productos_exterior` VARCHAR(5) DEFAULT NULL;
ALTER TABLE `cliente_sarlaft_juridico` MODIFY `huella` INT(1) DEFAULT NULL;
ALTER TABLE `cliente_sarlaft_juridico` MODIFY `firma` INT(1) DEFAULT NULL;
ALTER TABLE `cliente_sarlaft_juridico` MODIFY `verificacion` INT(1) DEFAULT NULL;
ALTER TABLE `cliente_sarlaft_juridico` MODIFY `entrevista` INT(1) DEFAULT NULL;
ALTER TABLE `cliente_sarlaft_juridico` MODIFY `autoriza_tratamiento` VARCHAR(5) DEFAULT NULL;
ALTER TABLE `cliente_sarlaft_juridico` MODIFY `autoriza_info_fasecolda` VARCHAR(5) DEFAULT NULL;

-- ACCIONISTAS

ALTER TABLE `accionistas` MODIFY `accionista_cotiza_bolsa` VARCHAR(5) DEFAULT NULL;
ALTER TABLE `accionistas` MODIFY `accionista_persona_publica` VARCHAR(5) DEFAULT NULL;
ALTER TABLE `accionistas` MODIFY `accionista_obligaciones_otro_pais` VARCHAR(5) DEFAULT NULL;