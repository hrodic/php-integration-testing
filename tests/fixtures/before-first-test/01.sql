CREATE TABLE IF NOT EXISTS `test`.`ephemeral_table`
(
    `id`       INT         NOT NULL AUTO_INCREMENT,
    `float`    FLOAT       NULL,
    `varchar`  VARCHAR(45) NULL,
    `datetime` DATETIME    NULL,
    PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `test`.`persistent_table`
(
    `id`       INT         NOT NULL AUTO_INCREMENT,
    PRIMARY KEY (`id`)
);