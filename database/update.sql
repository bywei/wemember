
-----------2017/09/26------------
ALTER TABLE `tp_client`  ADD `Username` VARCHAR(20) NOT NULL AFTER `Cid`,  ADD `Password` VARCHAR(40) NOT NULL AFTER `Username`,  ADD `UserPhone` VARCHAR(30) NOT NULL AFTER `Password`;

ALTER TABLE  `tp_client` ADD  `Status` TINYINT( 2 ) NOT NULL DEFAULT  '0';

ALTER TABLE  `tp_with` ADD  `Address` CHAR( 100 ) NOT NULL AFTER  `Cid`;
ALTER TABLE  `tp_with` ADD  `GoodsNum` CHAR( 20 ) NOT NULL AFTER  `Cid`;
ALTER TABLE  `tp_with` ADD  `Phone` CHAR( 20 ) NOT NULL AFTER  `Cid`;
ALTER TABLE  `tp_with` ADD  `ConsigneeName` CHAR( 20 ) NOT NULL AFTER  `Cid`;
ALTER TABLE  `tp_with` ADD  `OrderNo` CHAR( 20 ) NOT NULL AFTER  `Cid`;

ALTER TABLE  `tp_client` ADD  `Status` TINYINT( 1 ) NOT NULL DEFAULT  '0' AFTER  `UserPhone` ;