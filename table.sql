/* SYNTAX: mysql -useandb -pPASSWORD < FILE */
USE seandb;

CREATE TABLE IF NOT EXISTS `ludwig_von_beatdown` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `is_active` TINYINT UNSIGNED DEFAULT 0,
    `game_state` TINYINT UNSIGNED DEFAULT 0,
    `game_x` TINYINT UNSIGNED DEFAULT 0,
    `game_y` TINYINT UNSIGNED DEFAULT 0,
    `last_updated` INT(11) DEFAULT 0,
    `rand_seed` VARCHAR(8) DEFAULT 0,
    `player_registry` VARCHAR(8000) DEFAULT NULL,
    PRIMARY KEY `id` (`id`),
    KEY `is_active` (`is_active`),
    KEY `last_updated` (`last_updated`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `ludwig_players` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `game_id` INT(11) UNSIGNED DEFAULT 0,
    `player_id` VARCHAR(20), /* uniqid generated from js */
    `player_username` VARCHAR(32) DEFAULT '', /* human-readable name */
    `player_state` TINYINT UNSIGNED DEFAULT 0, /* 0 = uninitialized, 1 = alive, 2 = dead */
    `ttl` INT(11) DEFAULT 0, /* player can be autodeleted after this time. */
    PRIMARY KEY `id` (`id`),
    KEY `game_id` (`game_id`),
    KEY `player_id` (`player_id`),
    KEY `ttl` (`ttl`)
) ENGINE=InnoDB;