INSERT INTO `multi_param` (nombre_parametro, descripcion, valor_uno, valor_dos, valor_tres)
VALUES
('tipo_proceso', 'Nueva radicacion', 'Nueva radicacion', '1', NULL),
('tipo_proceso', 'Actualizacion', 'Actualizacion', '2', NULL),
('tipo_proceso', 'Confirmacion', 'Confirmacion', '3', NULL);

ALTER TABLE `zr_radicacion` ADD `fecha_actualizacion` DATE DEFAULT NULL;