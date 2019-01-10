CREATE TABLE IF NOT EXISTS `view_snapshots` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `sessionID` VARCHAR(255) NOT NULL,
  `template` VARCHAR(255) NOT NULL,
  `requestURI` varchar(255) NOT NULL,
  `step` INT(11) NOT NULL,
  `variables` LONGTEXT NULL,
  `params` LONGTEXT NULL,
  PRIMARY KEY (`id`)
) ENGINE = InnoDB;