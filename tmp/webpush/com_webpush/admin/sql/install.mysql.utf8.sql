/*!40101 SET @OLD_CHARACTER_SET_CLIENT = @@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS = @@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION = @@COLLATION_CONNECTION */;
SET NAMES utf8mb4;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS = @@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS = 0 */;
/*!40101 SET @OLD_SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO', SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES = @@SQL_NOTES, SQL_NOTES = 0 */;

DROP TABLE IF EXISTS `#__webpush_configs`;

CREATE TABLE `#__webpush_configs`
(
    `id`    INT UNSIGNED                            NOT NULL AUTO_INCREMENT,
    `name`  VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `value` TEXT COLLATE utf8mb4_unicode_ci         NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `#__webpush_subscribers`;

CREATE TABLE `#__webpush_subscribers`
(
    `id`                bigint unsigned                                               NOT NULL AUTO_INCREMENT,
    `subscribable_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `subscribable_id`   bigint unsigned                                               NOT NULL,
    `endpoint`          varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `public_key`        varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `auth_token`        varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `content_encoding`  varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `#__webpush_endpoint_unique_subscriptions` (`endpoint`),
    KEY `#__webpush_subscribable_type_id_index_subscribers` (`subscribable_type`, `subscribable_id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

/*!40111 SET SQL_NOTES = @OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE = @OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS = @OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT = @OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS = @OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION = @OLD_COLLATION_CONNECTION */;
