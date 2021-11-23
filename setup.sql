SET FOREIGN_KEY_CHECKS = FALSE;

DROP TABLE IF EXISTS tournament_user;
DROP TABLE IF EXISTS tournament_person;
DROP TABLE IF EXISTS tournament_modules;

SET FOREIGN_KEY_CHECKS = TRUE;

#
# TABLES
#
CREATE TABLE tournament_user (
    name     VARCHAR(32)  NOT NULL,
    password VARCHAR(255) NOT NULL,

    PRIMARY KEY (name)
);
CREATE TABLE tournament_person (
    id        INT UNSIGNED NOT NULL AUTO_INCREMENT,
    firstname VARCHAR(128) NOT NULL,
    name      VARCHAR(64)  NOT NULL,
    birthday  DATE         NOT NULL,
    gender    BIT(4)       NOT NULL,

    PRIMARY KEY (id)
);
CREATE TABLE tournament_modules (
    id   INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(16)  NOT NULL,

    PRIMARY KEY (id),
    UNIQUE (name)
);
