CREATE TABLE `common__user`
(
    id           INT         NOT NULL AUTO_INCREMENT,
    username     VARCHAR(32) NOT NULL,
    password     VARCHAR(60) NOT NULL,
    registered   DATETIME    NOT NULL,
    lastActivity DATETIME    NULL,

    CONSTRAINT `common__user_pk` PRIMARY KEY (id)
);

CREATE TABLE `common__contact`
(
    id      INT          NOT NULL AUTO_INCREMENT,
    user_id INT          NOT NULL,
    type    VARCHAR(16)  NOT NULL,
    value   VARCHAR(256) NOT NULL,

    CONSTRAINT `common__contact_pk` PRIMARY KEY (id),
    CONSTRAINT `contact_user_id` FOREIGN KEY
        `common__user_pk` (user_id) REFERENCES common__user (id)
        ON UPDATE CASCADE ON DELETE CASCADE
);

