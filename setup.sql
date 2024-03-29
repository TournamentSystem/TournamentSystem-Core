SET FOREIGN_KEY_CHECKS = FALSE;

DROP TABLE IF EXISTS tournament_user;
DROP TABLE IF EXISTS tournament_person;
DROP TABLE IF EXISTS tournament_modules;
DROP TABLE IF EXISTS tournament_permissions;

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
    name     VARCHAR(32) NOT NULL,
    module   VARCHAR(16) NOT NULL,
    settings JSON,

    PRIMARY KEY (name)
);
CREATE TABLE tournament_permissions (
    user       VARCHAR(32) NOT NULL,
    permission VARCHAR(64) NOT NULL,

    PRIMARY KEY (user, permission),
    FOREIGN KEY (user) REFERENCES tournament_user (name) ON DELETE CASCADE
);
