CREATE TABLE `oauth_access_tokens`
(
    `id`           int(11)                             NOT NULL AUTO_INCREMENT,
    `access_token` text COLLATE utf8_swedish_ci        NOT NULL,
    `client_id`    varchar(80) COLLATE utf8_swedish_ci NOT NULL,
    `user_id`      varchar(80) COLLATE utf8_swedish_ci          DEFAULT NULL,
    `expires`      timestamp                           NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    `scope`        varchar(4000) COLLATE utf8_swedish_ci        DEFAULT NULL,
    PRIMARY KEY (`id`)
)
    ENGINE = InnoDB
    AUTO_INCREMENT = 16
    DEFAULT CHARSET = utf8
    COLLATE = utf8_swedish_ci;

CREATE TABLE `oauth_authorization_codes`
(
    `id`                 int(11)                             NOT NULL AUTO_INCREMENT,
    `authorization_code` text COLLATE utf8_swedish_ci        NOT NULL,
    `client_id`          varchar(80) COLLATE utf8_swedish_ci NOT NULL,
    `user_id`            varchar(80) COLLATE utf8_swedish_ci          DEFAULT NULL,
    `redirect_uri`       varchar(2000) COLLATE utf8_swedish_ci        DEFAULT NULL,
    `expires`            timestamp                           NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    `scope`              varchar(4000) COLLATE utf8_swedish_ci        DEFAULT NULL,
    `id_token`           varchar(1000) COLLATE utf8_swedish_ci        DEFAULT NULL,
    PRIMARY KEY (`id`)
)
    ENGINE = InnoDB
    AUTO_INCREMENT = 9
    DEFAULT CHARSET = utf8
    COLLATE = utf8_swedish_ci;

CREATE TABLE `oauth_clients`
(
    `id`            int(11)                                           NOT NULL AUTO_INCREMENT,
    `client_id`     varchar(80) COLLATE utf8_swedish_ci               NOT NULL,
    `client_secret` varchar(80) COLLATE utf8_swedish_ci                        DEFAULT NULL,
    `redirect_uri`  varchar(2000) COLLATE utf8_swedish_ci                      DEFAULT NULL,
    `grant_types`   varchar(80) COLLATE utf8_swedish_ci                        DEFAULT NULL,
    `scope`         varchar(4000) COLLATE utf8_swedish_ci                      DEFAULT NULL,
    `user_id`       varchar(80) COLLATE utf8_swedish_ci                        DEFAULT NULL,
    `visibility`    enum ('public','private') COLLATE utf8_swedish_ci NOT NULL DEFAULT 'public',
    PRIMARY KEY (`id`)
)
    ENGINE = InnoDB
    DEFAULT CHARSET = utf8
    COLLATE = utf8_swedish_ci;

CREATE TABLE `oauth_jwt`
(
    `id`         int(11)                               NOT NULL AUTO_INCREMENT,
    `client_id`  varchar(80) COLLATE utf8_swedish_ci   NOT NULL,
    `subject`    varchar(80) COLLATE utf8_swedish_ci DEFAULT NULL,
    `public_key` varchar(2000) COLLATE utf8_swedish_ci NOT NULL,
    PRIMARY KEY (`id`)
)
    ENGINE = InnoDB
    DEFAULT CHARSET = utf8
    COLLATE = utf8_swedish_ci;

CREATE TABLE `oauth_refresh_tokens`
(
    `id`            int(11)                             NOT NULL AUTO_INCREMENT,
    `refresh_token` text COLLATE utf8_swedish_ci        NOT NULL,
    `client_id`     varchar(80) COLLATE utf8_swedish_ci NOT NULL,
    `user_id`       varchar(80) COLLATE utf8_swedish_ci          DEFAULT NULL,
    `expires`       timestamp                           NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    `scope`         varchar(4000) COLLATE utf8_swedish_ci        DEFAULT NULL,
    PRIMARY KEY (`id`)
)
    ENGINE = InnoDB
    AUTO_INCREMENT = 7
    DEFAULT CHARSET = utf8
    COLLATE = utf8_swedish_ci;

-- Table to hold all available scopes
CREATE TABLE `oauth_scopes`
(
    `id`         int(11)                             NOT NULL AUTO_INCREMENT,
    `scope`      varchar(80) COLLATE utf8_swedish_ci NOT NULL,
    `is_default` tinyint(1) DEFAULT NULL,
    PRIMARY KEY (`id`)
)
    ENGINE = InnoDB
    DEFAULT CHARSET = utf8
    COLLATE = utf8_swedish_ci;