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
);

CREATE TABLE common__comment_thread
(
    id VARCHAR(10) NOT NULL,

    CONSTRAINT `ss_c_comment_thread_pk` PRIMARY KEY (id)
);

CREATE TABLE common__comment
(
    id         VARCHAR(10) NOT NULL,
    user_id    VARCHAR(10) NOT NULL,
    thread_id  VARCHAR(10) NOT NULL,
    created_on DATETIME    NOT NULL,
    text       TEXT        NOT NULL,

    CONSTRAINT `ss_c_comment_pk` PRIMARY KEY (id),

    CONSTRAINT `ss_c_thread_fk` FOREIGN KEY
        (thread_id) REFERENCES common__comment_thread (id),
    CONSTRAINT `ss_c_user_fk` FOREIGN KEY
        (user_id) REFERENCES common__user (id)
);

CREATE TABLE `common__user_file_thread`
(
    id           INT      NOT NULL AUTO_INCREMENT,
    date_created DATETIME NULL,

    CONSTRAINT `ss_f_user_file_thread_pk` PRIMARY KEY (id)
);

CREATE TABLE `common__user_file`
(
    id       INT          NOT NULL AUTO_INCREMENT,
    filename VARCHAR(320) NOT NULL,
    size     INT          NOT NULL,
    thread_id INT NULL,

    CONSTRAINT `ss_f_user_file_pk` PRIMARY KEY (id),

    CONSTRAINT `ss_f_user_file_thread_fk` FOREIGN KEY (thread_id)
        REFERENCES common__user_file_thread (id) ON UPDATE CASCADE ON DELETE RESTRICT
);
