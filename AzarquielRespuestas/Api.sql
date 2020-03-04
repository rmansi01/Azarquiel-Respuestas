--
-- Table structure for table `usuario` *************************
--
DROP TABLE IF EXISTS `usuario`;
CREATE TABLE `usuario` (
`telefono` varchar(10) NOT NULL,
`nick` varchar(20) DEFAULT NULL,
`avatar` varchar(40) DEFAULT NULL,

PRIMARY KEY (`telefono`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
--
-- Dumping data for table `usuario`
--
INSERT INTO `usuario` (telefono,nick,avatar) VALUES 
('669739200', 'Paco', '669739200_avatar_1551006631.png');
--
-- Table structure for table `tema` *************************
--
DROP TABLE IF EXISTS `preguntas`;
CREATE TABLE `preguntas` (
  `telefono` varchar(10) NOT NULL,
  `nick` varchar(20) NOT NULL,
  `avatar` varchar(40) DEFAULT NULL,
  `_id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `fecha` datetime NOT NULL,
  `pregunta` varchar(500) DEFAULT NULL,

PRIMARY KEY (`_id`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tema`
--

INSERT INTO `preguntas` VALUES 
('669739200', 'Paco', '669739200_avatar_1551006631.png',1,'2016-03-26 09:12:12','¿Que te gusta comer?');
--
-- Table structure for table `comentario`  *************************
--
DROP TABLE IF EXISTS `comentario`;
CREATE TABLE `comentario` (
  `telefono` varchar(10) NOT NULL,
  `nick` varchar(20) NOT NULL,
  `avatar` varchar(40) DEFAULT NULL,
  `_id` mediumint(9) NOT NULL,
  `fecha` datetime NOT NULL,
  `post` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`telefono`,`_id`,`fecha`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `comentario`
--
INSERT INTO `comentario` VALUES 
('628354600','Yoel','628354600_avatar_1551006655.png',1,'2016-03-26 08:35:23','falta poco eh?');




