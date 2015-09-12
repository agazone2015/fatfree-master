/* Cafe Table */
CREATE TABLE `cafes` (
    `cafeId` INT(4) NOT NULL AUTO_INCREMENT,
    `cafeName` VARCHAR(40) NOT NULL,
    `isActive` BIT(1) NOT NULL DEFAULT b'1',
    PRIMARY KEY (`cafeId`)
)
COLLATE='latin1_swedish_ci'
ENGINE=InnoDB
AUTO_INCREMENT=3
;


/* Category Table */
CREATE TABLE `categories` (
    `categoryId` INT(4) NOT NULL AUTO_INCREMENT,
    `categoryName` VARCHAR(40) NOT NULL,
    `isActive` BIT(1) NOT NULL DEFAULT b'1',
    PRIMARY KEY (`categoryId`)
)
COLLATE='latin1_swedish_ci'
ENGINE=InnoDB
AUTO_INCREMENT=23
;

/* Item Table */
CREATE TABLE `items` (
    `itemId` INT(4) NOT NULL AUTO_INCREMENT,
    `itemName` VARCHAR(40) NOT NULL,
    `description` VARCHAR(100) NULL DEFAULT NULL,
    `price` FLOAT(6,2) NULL DEFAULT NULL,
    `picture` VARCHAR(100) NULL DEFAULT NULL,
    `isActive` TINYINT(1) NOT NULL DEFAULT '1',
    `isVegetarian` TINYINT(1) NOT NULL DEFAULT '0',
    `isPopular` TINYINT(1) NOT NULL DEFAULT '0',
    `isSpecial` TINYINT(1) NOT NULL DEFAULT '0',
    `cafeId` INT(4) NOT NULL,
    `categoryId` INT(4) NOT NULL,
    PRIMARY KEY (`itemId`),
    INDEX `cafeId` (`cafeId`),
    INDEX `categoryId` (`categoryId`),
    CONSTRAINT `items_ibfk_1` FOREIGN KEY (`cafeId`) REFERENCES `cafes` (`cafeId`),
    CONSTRAINT `items_ibfk_2` FOREIGN KEY (`categoryId`) REFERENCES `categories` (`categoryId`)
)
COLLATE='latin1_swedish_ci'
ENGINE=InnoDB
AUTO_INCREMENT=35
;

/* extra tables*/
CREATE TABLE `information` (
    `infoId` INT(4) NOT NULL AUTO_INCREMENT,
    `about` VARCHAR(1000) NULL DEFAULT NULL,
    `isPriceOn` TINYINT(1) NOT NULL DEFAULT '0',
    `isPromotionOn` TINYINT(1) NOT NULL DEFAULT '0',
    `isSpecialOn` TINYINT(1) NOT NULL DEFAULT '0',
    PRIMARY KEY (`infoId`)
)
COLLATE='latin1_swedish_ci'
ENGINE=InnoDB
AUTO_INCREMENT=2
;

/* Images table*/
CREATE TABLE `images` (
    `imageId` SMALLINT(5) NOT NULL AUTO_INCREMENT,
    `imageName` VARCHAR(50) NOT NULL,
    `imageLink` VARCHAR(200) NOT NULL,
    `isIncluded` TINYINT(1) NOT NULL DEFAULT '0',
    `size` MEDIUMINT(9) NOT NULL,
    PRIMARY KEY (`imageId`)
)
COLLATE='latin1_swedish_ci'
ENGINE=InnoDB
AUTO_INCREMENT=30
;

/* User table */
CREATE TABLE `users` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `password` VARCHAR(50) NOT NULL,
    `name` VARCHAR(50) NOT NULL COLLATE 'latin1_swedish_ci',
    `email` VARCHAR(150) NULL DEFAULT NULL COLLATE 'latin1_swedish_ci',
    `mobile` VARCHAR(20) NULL DEFAULT NULL COLLATE 'latin1_swedish_ci',
    `address` VARCHAR(255) NULL DEFAULT NULL COLLATE 'latin1_swedish_ci',
    PRIMARY KEY (`id`)
)
COLLATE='utf8_general_ci'
ENGINE=MyISAM
ROW_FORMAT=DYNAMIC
AUTO_INCREMENT=24
;

