CREATE TABLE IF NOT EXISTS `IMG_TBL` (
  `IMG_ID` int(6) NOT NULL AUTO_INCREMENT,
  `FILENAME` varchar(10) CHARACTER SET utf8 NOT NULL,
  `UPLOAD_DATE` date DEFAULT NULL,
  `HITS` int(11) NOT NULL DEFAULT '0',
  `REPORTS` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`IMG_ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1253 ;