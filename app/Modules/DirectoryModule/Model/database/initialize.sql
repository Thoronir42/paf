CREATE TABLE directory__person
(
    id           VARCHAR(10) NOT NULL,
    display_name VARCHAR(72) NOT NULL,
    user_id      VARCHAR(10) NULL,

    CONSTRAINT `directory__person_pk` PRIMARY KEY (id)
);

CREATE TABLE directory__contact
(
    id        INT          NOT NULL AUTO_INCREMENT,
    person_id VARCHAR(10)  NOT NULL,
    type      VARCHAR(16)  NOT NULL,
    value     VARCHAR(256) NOT NULL,

    CONSTRAINT `directory__contact_pk` PRIMARY KEY (id),
    CONSTRAINT `contact_person_id` FOREIGN KEY
        `directory__person_pk` (person_id) REFERENCES directory__person (id)
        ON UPDATE CASCADE ON DELETE CASCADE
);
