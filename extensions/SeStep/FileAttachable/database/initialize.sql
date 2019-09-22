CREATE TABLE `ss_files__user_file_thread`
(
    id           INT      NOT NULL AUTO_INCREMENT,
    date_created DATETIME NULL,

    CONSTRAINT `ss_f_user_file_thread_pk` PRIMARY KEY (id)
);

CREATE TABLE `ss_files__user_file`
(
    id       INT          NOT NULL AUTO_INCREMENT,
    filename VARCHAR(320) NOT NULL,
    size     INT          NOT NULL,
    thread_id INT NULL,

    CONSTRAINT `ss_f_user_file_pk` PRIMARY KEY (id),

    CONSTRAINT `ss_f_user_file_thread_fk` FOREIGN KEY (thread_id)
        REFERENCES ss_files__user_file_thread (id) ON UPDATE CASCADE ON DELETE RESTRICT
);
