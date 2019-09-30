CREATE TABLE ss_comments__comment_thread
(
    id VARCHAR(10) NOT NULL,

    CONSTRAINT `ss_c_comment_thread_pk` PRIMARY KEY (id)
);

CREATE TABLE ss_comments__comment
(
    id         VARCHAR(10) NOT NULL,
    thread_id  VARCHAR(10) NOT NULL,
    created_on DATETIME    NOT NULL,
    text       TEXT        NOT NULL,

    CONSTRAINT `ss_c_comment_pk` PRIMARY KEY (id),

    CONSTRAINT `ss_c_thread_fk` FOREIGN KEY
        (thread_id) REFERENCES ss_comments__comment_thread (id)
);
