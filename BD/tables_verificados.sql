/*
SQLyog Community v13.1.2 (64 bit)
MySQL - 10.1.10-MariaDB : Database - confianza
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

USE `confianza`;

/*Table structure for table `accionistas_verificado` */

DROP TABLE IF EXISTS `accionistas_verificado`;

CREATE TABLE `accionistas_verificado` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `accionista_id` int(11) NOT NULL,
  `verificacion_accionista_documento` varchar(2) DEFAULT NULL,
  `verificacion_accionista_nombres` varchar(2) DEFAULT NULL,
  `verificacion_accionista_participacion` varchar(2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `accionista_id` (`accionista_id`),
  CONSTRAINT `fk_accionista_verificado_accionista_id` FOREIGN KEY (`accionista_id`) REFERENCES `accionistas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `cliente_sarlaft_juridico_verificado` */

DROP TABLE IF EXISTS `cliente_sarlaft_juridico_verificado`;

CREATE TABLE `cliente_sarlaft_juridico_verificado` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cliente_sarlaft_juridico_id` int(11) NOT NULL,
  `verificacion_documento` varchar(2) DEFAULT NULL,
  `verificacion_razon_social` varchar(2) DEFAULT NULL,
  `verificacion_direccion_oficina_principal` varchar(2) DEFAULT NULL,
  `verificacion_ciiu_actividad_economica` varchar(2) DEFAULT NULL,
  `verificacion_ciiu_cod` varchar(2) DEFAULT NULL,
  `verificacion_sector` varchar(2) DEFAULT NULL,
  `verificacion_departamento_empresa` varchar(2) DEFAULT NULL,
  `verificacion_ciudad_empresa` varchar(2) DEFAULT NULL,
  `verificacion_oficina_principal_telefono` varchar(2) DEFAULT NULL,
  `verificacion_rep_legal_primer_apellido` varchar(2) DEFAULT NULL,
  `verificacion_rep_legal_segundo_apellido` varchar(2) DEFAULT NULL,
  `verificacion_rep_legal_nombres` varchar(2) DEFAULT NULL,
  `verificacion_rep_legal_tipo_documento` varchar(2) DEFAULT NULL,
  `verificacion_rep_legal_numero_documento` varchar(2) DEFAULT NULL,
  `verificacion_info_financiera_ingresos` varchar(2) DEFAULT NULL,
  `verificacion_info_financiera_egresos` varchar(2) DEFAULT NULL,
  `verificacion_info_financiera_activos` varchar(2) DEFAULT NULL,
  `verificacion_info_financiera_pasivos` varchar(2) DEFAULT NULL,
  `verificacion_declaracion_fondos` varchar(2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_sarlaft_verificado_cliente_sarlaft_juridio` (`cliente_sarlaft_juridico_id`),
  CONSTRAINT `fk_sarlaft_verificado_cliente_sarlaft_juridio` FOREIGN KEY (`cliente_sarlaft_juridico_id`) REFERENCES `cliente_sarlaft_juridico` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
