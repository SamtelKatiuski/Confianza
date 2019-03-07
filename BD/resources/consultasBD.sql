-- Obtiene los clientes
SELECT * FROM clientes WHERE documento = '9876543210';
SELECT * FROM clientes WHERE documento = '15265';

-- Obtiene radicacion de 9876543210
SELECT * FROM zr_radicacion WHERE cliente_id = 5687;
-- Obtiene radicacion de 15265
SELECT * FROM zr_radicacion WHERE cliente_id = 5688;

-- Obtiene sarlaft de 9876543210
SELECT * FROM cliente_sarlaft_juridico WHERE cliente = 5687;
-- Obtiene sarlaft de 15265
SELECT * FROM cliente_sarlaft_natural WHERE cliente = 5688;

-- Obtiene el archivo organizado del cliente juridico con documento 9876543210
SELECT * FROM archivo_organizado WHERE radicacion_id = 5514;
-- Obtiene el archivo organizado del cliente juridico con documento 15265
SELECT * FROM archivo_organizado WHERE radicacion_id = 5515;

-- Obtiene la relacion de archivo radicacion de 9876543210
SELECT * FROM relacion_archivo_radicacion WHERE cliente_id = 5687;
-- Obtiene la relacion de archivo radicacion de 15265
SELECT * FROM relacion_archivo_radicacion WHERE cliente_id = 5688;

DESC zr_radicacion;
DESC clientes;
DESC cliente_sarlaft_natural;
DESC cliente_sarlaft_juridico;
DESC archivo_organizado;

SELECT * FROM relacion_archivo_radicacion WHERE cliente_id = 5690;

SELECT * FROM clientes WHERE documento = '65432';
SELECT * FROM zr_radicacion WHERE cliente_id = 5690;
SELECT * FROM cliente_sarlaft_natural WHERE cliente = 5690;
SELECT * FROM archivo_organizado WHERE radicacion_id = 5527;
SELECT * FROM relacion_archivo_radicacion WHERE cliente_id = 5690;