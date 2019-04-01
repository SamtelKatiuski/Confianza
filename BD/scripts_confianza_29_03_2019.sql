INSERT INTO `multi_param` (nombre_parametro, descripcion, valor_uno, valor_dos, valor_tres)
VALUES
('tipo_proceso', 'Nueva radicacion', 'Nueva radicacion', '1', NULL),
('tipo_proceso', 'Actualizacion', 'Actualizacion', '2', NULL),
('tipo_proceso', 'Confirmacion', 'Confirmacion', '3', NULL);

-- 01/04/2019

ALTER TABLE `zr_radicacion` ADD `fecha_actualizacion` DATE DEFAULT NULL;

INSERT INTO `estados_sarlaft` (desc_type) VALUES 
('ACTUALIZACION'),
('CONFIRMACION');