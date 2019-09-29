CREATE TABLE `common__user`
(
    id            VARCHAR(10) NOT NULL,
    type          VARCHAR(12) NOT NULL,
    username      VARCHAR(32) NOT NULL,
    password      VARCHAR(60) NOT NULL,
    registered    DATETIME    NOT NULL,
    last_activity DATETIME    NULL,

    CONSTRAINT `common__user_pk` PRIMARY KEY (id)
);

CREATE TABLE `common__person`
(
    id           VARCHAR(10) NOT NULL,
    display_name VARCHAR(72) NOT NULL,
    user_id      VARCHAR(10) NULL,

    CONSTRAINT `common__person_pk` PRIMARY KEY (id)
);

CREATE TABLE `common__contact`
(
    id        INT          NOT NULL AUTO_INCREMENT,
    person_id VARCHAR(10)  NOT NULL,
    type      VARCHAR(16)  NOT NULL,
    value     VARCHAR(256) NOT NULL,

    CONSTRAINT `common__contact_pk` PRIMARY KEY (id),
    CONSTRAINT `contact_person_id` FOREIGN KEY
        `common__person_pk` (person_id) REFERENCES common__person (id)
        ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE `common__slug`
(
    id VARCHAR(64) NOT NULL,

    CONSTRAINT `common_slug_pk` PRIMARY KEY (id)
)
