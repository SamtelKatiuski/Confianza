-- Cambios correspondientes a los requerimientos 1 y 2

-- ZR_TIPO_DOCUMENTO

UPDATE zr_tipo_documento SET codigo = 'FCC' WHERE codigo = 'SAA';
UPDATE zr_tipo_documento SET codigo = 'CCE' WHERE codigo = 'DOC';
UPDATE zr_tipo_documento SET codigo = 'CCO' WHERE codigo = 'CAC';

INSERT INTO zr_tipo_documento (codigo, descripcion, proceso) VALUES
('MAC', 'Mail de Actualizacion o Confirmacion', 'LEGAL'),
('FAC', 'Formato de Actualizacion o Confirmacion', 'LEGAL'),
('DDC', 'Documento de Constitucion (Union Temporal, Consorcios y Sociedades Futuras)', 'LEGAL'),
('ACC', 'Composicion Accionaria', 'LEGAL'),
('EFI', 'Estados financieros incompletos (Balance, PYG)', 'LEGAL'),
('NEF', 'Notas de Estado Financiero', 'LEGAL'),
('RTA', 'Declaracion de renta', 'LEGAL'),
('RET', 'Certificado de ingresos y Retenciones', 'LEGAL');

UPDATE zr_tipo_documento SET descripcion = 'Se veran los Formularios de Conocimiento el cual identificara la fecha de diligenciamiento del formulario' WHERE id = 1;
UPDATE zr_tipo_documento SET descripcion = 'Cuando se reciba una cedula de ciudadania o de extranjeria, permiso especial de permanencia' WHERE id = 2;
UPDATE zr_tipo_documento SET descripcion = 'Cuando se reciba RUT en persona natural o juridica' WHERE id = 6;
UPDATE zr_tipo_documento SET descripcion = 'Cuando se reciba Camara de Comercio en persona natural o juridica' WHERE id = 3;
UPDATE zr_tipo_documento SET descripcion = 'Estados finacieron completos (Balance, PYG y Notas a los estados)' WHERE id = 4;

-- TIPO_ARCHIVO

UPDATE tipo_archivo SET tipo_doc = 'FCC' WHERE tipo_doc = 'SAA';
UPDATE tipo_archivo SET tipo_doc = 'CCE' WHERE tipo_doc = 'DOC';
UPDATE tipo_archivo SET tipo_doc = 'CCO' WHERE tipo_doc = 'CAC';

INSERT INTO tipo_archivo (tipo_doc, nombre_doc, id_carpeta, id_linea_negocio, activo) VALUES 
('MAC', NULL, 6, NULL, 1),
('FAC', NULL, 6, NULL, 1),
('DDC', NULL, 6, NULL, 1),
('ACC', NULL, 6, NULL, 1),
('EFI', NULL, 6, NULL, 1),
('NEF', NULL, 6, NULL, 1),
('RTA', NULL, 6, NULL, 1),
('RET', NULL, 6, NULL, 1);

UPDATE relacion_archivo_radicacion SET nombre_archivo = CONCAT('FCC', RIGHT(nombre_archivo, LENGTH(nombre_archivo) - 3)) WHERE LEFT(nombre_archivo, 3) = 'SAA';
UPDATE relacion_archivo_radicacion SET nombre_archivo = CONCAT('CCE', RIGHT(nombre_archivo, LENGTH(nombre_archivo) - 3)) WHERE LEFT(nombre_archivo, 3) = 'DOC';
UPDATE relacion_archivo_radicacion SET nombre_archivo = CONCAT('CAC', RIGHT(nombre_archivo, LENGTH(nombre_archivo) - 3)) WHERE LEFT(nombre_archivo, 3) = 'CCO';

UPDATE archivo_organizado SET nombre_archivo = CONCAT('FCC', RIGHT(nombre_archivo, LENGTH(nombre_archivo) - 3)), id_tipo_doc = 'FCC' WHERE LEFT(nombre_archivo, 3) = 'SAA';
UPDATE archivo_organizado SET nombre_archivo = CONCAT('CCE', RIGHT(nombre_archivo, LENGTH(nombre_archivo) - 3)), id_tipo_doc = 'CCE' WHERE LEFT(nombre_archivo, 3) = 'DOC';
UPDATE archivo_organizado SET nombre_archivo = CONCAT('CAC', RIGHT(nombre_archivo, LENGTH(nombre_archivo) - 3)), id_tipo_doc = 'CAC' WHERE LEFT(nombre_archivo, 3) = 'CCO';


ALTER TABLE zr_radicacion CHANGE formulario_repetido repetido TINYINT(1) NOT NULL;
ALTER TABLE relacion_archivo_radicacion ADD FECHA_EMISION DATE NULL;