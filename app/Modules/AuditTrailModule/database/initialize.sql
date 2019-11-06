CREATE TABLE audit__entry
(
    id         VARCHAR(10) NOT NULL,
    subject    VARCHAR(10) NOT NULL,
    instant    DATETIME    NOT NULL,
    actor      VARCHAR(10) NULL,
    type       VARCHAR(72) NOT NULL,
    parameters TEXT        NOT NULL,

    CONSTRAINT `pk_al__event` PRIMARY KEY (id),

    CONSTRAINT `fk_actor_user_id` FOREIGN KEY
        (actor) REFERENCES common__user (id)
);
